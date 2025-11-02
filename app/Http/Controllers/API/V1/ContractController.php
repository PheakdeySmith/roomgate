<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Contract;
use App\Models\Room;
use App\Models\User;
use App\Services\Contract\ContractService;
use App\Services\Room\RoomService;
use App\Services\Tenant\TenantService;
use App\Services\Invoice\InvoiceService;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ContractController extends BaseController
{
    protected ContractService $contractService;
    protected RoomService $roomService;
    protected TenantService $tenantService;
    protected InvoiceService $invoiceService;
    protected NotificationService $notificationService;

    public function __construct(
        ContractService $contractService,
        RoomService $roomService,
        TenantService $tenantService,
        InvoiceService $invoiceService,
        NotificationService $notificationService
    ) {
        $this->contractService = $contractService;
        $this->roomService = $roomService;
        $this->tenantService = $tenantService;
        $this->invoiceService = $invoiceService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of contracts
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $filters = $request->all();

            if ($user->hasRole('landlord')) {
                $contracts = $this->contractService->getLandlordContracts($user, $filters);
            } elseif ($user->hasRole('tenant')) {
                $contracts = $this->tenantService->getTenantContracts($user, $filters);
            } else {
                return $this->sendError('Unauthorized role', [], 403);
            }

            $data = $contracts->map(function ($contract) {
                return $this->transformContract($contract);
            });

            if ($request->has('page')) {
                return $this->sendPaginatedResponse($contracts, 'Contracts retrieved successfully');
            }

            return $this->sendResponse($data, 'Contracts retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve contracts', [$e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created contract
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Tenant information
            'tenant_id' => 'nullable|exists:users,id',
            'tenant_name' => 'required_without:tenant_id|string|max:255',
            'tenant_email' => 'required_without:tenant_id|email|unique:users,email',
            'tenant_phone' => 'nullable|string|max:20',
            'tenant_password' => 'required_without:tenant_id|string|min:8',

            // Contract details
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'billing_cycle' => 'required|in:daily,monthly,yearly',
            'payment_due_day' => 'nullable|integer|min:1|max:31',
            'notes' => 'nullable|string',
            'auto_renew' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            // Check room authorization
            $room = Room::findOrFail($request->room_id);
            if ($room->property->landlord_id !== $user->id) {
                return $this->sendError('Unauthorized to use this room', [], 403);
            }

            // Check room availability
            if (!$this->roomService->checkRoomAvailability($room, $request->start_date, $request->end_date)) {
                return $this->sendError('Room is not available for the selected dates', [], 400);
            }

            // Use service to create contract
            if ($request->has('tenant_id')) {
                // Existing tenant
                $tenant = User::findOrFail($request->tenant_id);
                $contractData = $request->only([
                    'room_id', 'start_date', 'end_date', 'monthly_rent',
                    'deposit_amount', 'billing_cycle', 'payment_due_day', 'notes', 'auto_renew'
                ]);
                $contractData['user_id'] = $tenant->id;
                $contract = $this->contractService->createContract($contractData, $user);
            } else {
                // New tenant
                $contract = $this->contractService->createContractWithTenant($request->all(), $user);
            }

            // Send notifications
            $this->notificationService->sendWelcomeTenantNotification(
                $contract->tenant,
                $contract,
                ['email']
            );

            $data = $this->transformContract($contract->load(['tenant', 'room.property']));

            return $this->sendResponse($data, 'Contract created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('Failed to create contract', [$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified contract
     */
    public function show($id)
    {
        try {
            $contract = Contract::with([
                'tenant',
                'room.property',
                'room.amenities',
                'invoices',
                'room.meters'
            ])->findOrFail($id);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($contract->room->property->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if ($contract->user_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            // Get contract details with statistics
            $contractDetails = $this->contractService->getContractDetails($contract);

            $data = $this->transformContract($contract, true);
            $data['statistics'] = $contractDetails;

            return $this->sendResponse($data, 'Contract details retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Contract not found', [$e->getMessage()], 404);
        }
    }

    /**
     * Update the specified contract
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'end_date' => 'sometimes|required|date|after:start_date',
            'monthly_rent' => 'sometimes|nullable|numeric|min:0',
            'billing_cycle' => 'sometimes|required|in:daily,monthly,yearly',
            'payment_due_day' => 'nullable|integer|min:1|max:31',
            'notes' => 'nullable|string',
            'auto_renew' => 'sometimes|boolean',
            'status' => 'sometimes|required|in:active,expired,terminated',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $contract = Contract::findOrFail($id);

            // Check authorization
            if ($contract->room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if contract can be updated
            if ($contract->status === 'terminated') {
                return $this->sendError('Cannot update terminated contract', [], 400);
            }

            // Update using service
            $contract = $this->contractService->updateContract($contract, $request->all());

            $data = $this->transformContract($contract->fresh(['tenant', 'room.property']));

            return $this->sendResponse($data, 'Contract updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update contract', [$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified contract
     */
    public function destroy($id)
    {
        try {
            $contract = Contract::findOrFail($id);

            // Check authorization
            if ($contract->room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if contract has invoices
            if ($contract->invoices()->exists()) {
                return $this->sendError('Cannot delete contract with existing invoices', [], 400);
            }

            // Use service to terminate/delete
            $this->contractService->terminateContract($contract, true);

            return $this->sendResponse(null, 'Contract deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete contract', [$e->getMessage()], 500);
        }
    }

    /**
     * Renew a contract
     */
    public function renew(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'new_end_date' => 'required|date|after:today',
            'new_monthly_rent' => 'nullable|numeric|min:0',
            'renewal_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $contract = Contract::findOrFail($id);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($contract->room->property->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if ($contract->user_id !== $user->id) {
                    return $this->sendError('You can only request renewal, not renew directly', [], 403);
                }
            }

            // Check if contract can be renewed
            if ($contract->status !== 'active') {
                return $this->sendError('Only active contracts can be renewed', [], 400);
            }

            // Renew using service
            $newContract = $this->contractService->renewContract($contract, [
                'end_date' => $request->new_end_date,
                'monthly_rent' => $request->new_monthly_rent ?? $contract->monthly_rent,
                'notes' => $request->renewal_notes,
            ]);

            // Send notification
            $this->notificationService->sendNotification(
                $newContract->tenant,
                'contract_renewed',
                'email',
                'Contract Renewed Successfully',
                "Your contract has been renewed until {$newContract->end_date->format('Y-m-d')}"
            );

            $data = $this->transformContract($newContract->load(['tenant', 'room.property']));

            return $this->sendResponse($data, 'Contract renewed successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to renew contract', [$e->getMessage()], 500);
        }
    }

    /**
     * Terminate a contract
     */
    public function terminate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'termination_date' => 'required|date',
            'termination_reason' => 'required|string',
            'refund_deposit' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $contract = Contract::findOrFail($id);

            // Check authorization
            if ($contract->room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if contract can be terminated
            if ($contract->status !== 'active') {
                return $this->sendError('Only active contracts can be terminated', [], 400);
            }

            // Terminate using service
            $result = $this->contractService->terminateContract($contract, false, [
                'termination_date' => $request->termination_date,
                'termination_reason' => $request->termination_reason,
                'refund_deposit' => $request->refund_deposit ?? false,
            ]);

            // Send notification
            $this->notificationService->sendNotification(
                $contract->tenant,
                'contract_terminated',
                'email',
                'Contract Terminated',
                "Your contract has been terminated as of {$request->termination_date}"
            );

            return $this->sendResponse(null, 'Contract terminated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to terminate contract', [$e->getMessage()], 500);
        }
    }

    /**
     * Get contract invoices
     */
    public function getInvoices($id)
    {
        try {
            $contract = Contract::findOrFail($id);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($contract->room->property->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if ($contract->user_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            $invoices = $contract->invoices()->with('lineItems')->get();

            $data = $invoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'issue_date' => $invoice->issue_date,
                    'due_date' => $invoice->due_date,
                    'total_amount' => $invoice->total_amount,
                    'paid_amount' => $invoice->paid_amount,
                    'balance' => $invoice->total_amount - $invoice->paid_amount,
                    'status' => $invoice->status,
                ];
            });

            return $this->sendResponse($data, 'Contract invoices retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve invoices', [$e->getMessage()], 500);
        }
    }

    /**
     * Get contract payments
     */
    public function getPayments($id)
    {
        try {
            $contract = Contract::findOrFail($id);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($contract->room->property->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if ($contract->user_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            $payments = DB::table('payments')
                ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                ->where('invoices.contract_id', $contract->id)
                ->select('payments.*', 'invoices.invoice_number')
                ->get();

            $data = $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'invoice_number' => $payment->invoice_number,
                    'amount' => $payment->amount,
                    'payment_date' => $payment->payment_date,
                    'payment_method' => $payment->payment_method,
                    'transaction_reference' => $payment->transaction_reference,
                    'status' => $payment->status,
                ];
            });

            return $this->sendResponse($data, 'Contract payments retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve payments', [$e->getMessage()], 500);
        }
    }

    /**
     * Get expiring contracts
     */
    public function getExpiringContracts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'days' => 'nullable|integer|min:1|max:90',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();
            $days = $request->input('days', 30);

            $contracts = $this->contractService->getExpiringContracts($user, $days);

            $data = $contracts->map(function ($contract) {
                $transformed = $this->transformContract($contract);
                $transformed['days_until_expiry'] = now()->diffInDays($contract->end_date);
                return $transformed;
            });

            return $this->sendResponse($data, 'Expiring contracts retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve expiring contracts', [$e->getMessage()], 500);
        }
    }

    /**
     * Get expired contracts
     */
    public function getExpiredContracts()
    {
        try {
            $user = Auth::user();

            $contracts = Contract::whereHas('room.property', function ($q) use ($user) {
                $q->where('landlord_id', $user->id);
            })
                ->where('status', 'expired')
                ->with(['tenant', 'room.property'])
                ->get();

            $data = $contracts->map(function ($contract) {
                $transformed = $this->transformContract($contract);
                $transformed['days_expired'] = now()->diffInDays($contract->end_date);
                return $transformed;
            });

            return $this->sendResponse($data, 'Expired contracts retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve expired contracts', [$e->getMessage()], 500);
        }
    }

    /**
     * Upload contract document
     */
    public function uploadDocument(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'document_type' => 'required|string|in:contract,agreement,addendum,other',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $contract = Contract::findOrFail($id);

            // Check authorization
            if ($contract->room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Handle file upload
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/contract-documents');
                File::makeDirectory($destinationPath, 0755, true, true);
                $file->move($destinationPath, $filename);

                // Store document reference in database
                DB::table('documents')->insert([
                    'contract_id' => $contract->id,
                    'user_id' => $contract->user_id,
                    'file_path' => 'uploads/contract-documents/' . $filename,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $request->document_type,
                    'description' => $request->description,
                    'uploaded_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $this->sendResponse(['file_url' => asset('uploads/contract-documents/' . $filename)], 'Document uploaded successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to upload document', [$e->getMessage()], 500);
        }
    }

    /**
     * Transform contract for API response
     */
    protected function transformContract($contract, $detailed = false)
    {
        $data = [
            'id' => $contract->id,
            'tenant' => [
                'id' => $contract->tenant->id,
                'name' => $contract->tenant->name,
                'email' => $contract->tenant->email,
                'phone' => $contract->tenant->phone,
            ],
            'room' => [
                'id' => $contract->room->id,
                'number' => $contract->room->room_number,
                'floor' => $contract->room->floor,
            ],
            'property' => [
                'id' => $contract->room->property->id,
                'name' => $contract->room->property->name,
                'address' => $contract->room->property->address_line_1,
            ],
            'start_date' => $contract->start_date,
            'end_date' => $contract->end_date,
            'monthly_rent' => $contract->monthly_rent,
            'deposit_amount' => $contract->deposit_amount,
            'billing_cycle' => $contract->billing_cycle,
            'status' => $contract->status,
            'auto_renew' => $contract->auto_renew ?? false,
            'created_at' => $contract->created_at,
        ];

        if ($detailed) {
            $data['payment_due_day'] = $contract->payment_due_day;
            $data['notes'] = $contract->notes;
            $data['termination_date'] = $contract->termination_date;
            $data['termination_reason'] = $contract->termination_reason;
            $data['total_invoiced'] = $contract->invoices->sum('total_amount');
            $data['total_paid'] = $contract->invoices->sum('paid_amount');
            $data['outstanding_balance'] = $data['total_invoiced'] - $data['total_paid'];
        }

        return $data;
    }
}