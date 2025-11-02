<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Models\Contract;
use App\Models\MaintenanceRequest;
use App\Services\Tenant\TenantService;
use App\Services\Contract\ContractService;
use App\Services\Invoice\InvoiceService;
use App\Services\Payment\PaymentService;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TenantController extends BaseController
{
    protected TenantService $tenantService;
    protected ContractService $contractService;
    protected InvoiceService $invoiceService;
    protected PaymentService $paymentService;
    protected NotificationService $notificationService;

    public function __construct(
        TenantService $tenantService,
        ContractService $contractService,
        InvoiceService $invoiceService,
        PaymentService $paymentService,
        NotificationService $notificationService
    ) {
        $this->tenantService = $tenantService;
        $this->contractService = $contractService;
        $this->invoiceService = $invoiceService;
        $this->paymentService = $paymentService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of tenants (for landlords)
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Only landlords can view tenants', [], 403);
            }

            $tenants = User::role('tenant')
                ->where('landlord_id', $user->id)
                ->with('contracts.room.property')
                ->when($request->has('status'), function ($q) use ($request) {
                    $q->where('status', $request->status);
                })
                ->when($request->has('search'), function ($q) use ($request) {
                    $search = $request->search;
                    $q->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->get();

            $data = $tenants->map(function ($tenant) {
                return $this->transformTenant($tenant);
            });

            if ($request->has('page')) {
                return $this->sendPaginatedResponse($tenants, 'Tenants retrieved successfully');
            }

            return $this->sendResponse($data, 'Tenants retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve tenants', [$e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created tenant
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'send_welcome_email' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Only landlords can create tenants', [], 403);
            }

            // Create tenant using service
            $tenantData = $request->only(['name', 'email', 'phone', 'password']);
            $tenantData['landlord_id'] = $user->id;

            $tenant = $this->tenantService->createTenant($tenantData);

            // Send welcome email if requested
            if ($request->input('send_welcome_email', true)) {
                $this->notificationService->sendNotification(
                    $tenant,
                    'welcome',
                    'email',
                    'Welcome to RoomGate',
                    'Your tenant account has been created successfully. You can now login to view your rental information.'
                );
            }

            $data = $this->transformTenant($tenant);

            return $this->sendResponse($data, 'Tenant created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('Failed to create tenant', [$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified tenant
     */
    public function show($id)
    {
        try {
            $tenant = User::role('tenant')->findOrFail($id);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($tenant->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if ($tenant->id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            // Get tenant details with statistics
            $eligibility = $this->tenantService->checkEligibility($tenant);
            $rentalHistory = $this->tenantService->getRentalHistory($tenant);

            $data = $this->transformTenant($tenant, true);
            $data['eligibility'] = $eligibility;
            $data['rental_history'] = $rentalHistory;

            return $this->sendResponse($data, 'Tenant details retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Tenant not found', [$e->getMessage()], 404);
        }
    }

    /**
     * Update the specified tenant
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'sometimes|required|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $tenant = User::role('tenant')->findOrFail($id);

            // Check authorization
            if ($tenant->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Update using service
            $tenant = $this->tenantService->updateTenant($tenant, $request->all());

            $data = $this->transformTenant($tenant);

            return $this->sendResponse($data, 'Tenant updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update tenant', [$e->getMessage()], 500);
        }
    }

    /**
     * Archive a tenant
     */
    public function archive($id)
    {
        try {
            $tenant = User::role('tenant')->findOrFail($id);

            // Check authorization
            if ($tenant->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Archive using service
            $result = $this->tenantService->archiveTenant($tenant);

            if (!$result) {
                return $this->sendError('Cannot archive tenant with active contracts', [], 400);
            }

            return $this->sendResponse(null, 'Tenant archived successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to archive tenant', [$e->getMessage()], 500);
        }
    }

    /**
     * Restore archived tenant
     */
    public function restore($id)
    {
        try {
            $tenant = $this->tenantService->restoreTenant($id);

            if (!$tenant) {
                return $this->sendError('Tenant not found or not archived', [], 404);
            }

            // Check authorization
            if ($tenant->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $data = $this->transformTenant($tenant);

            return $this->sendResponse($data, 'Tenant restored successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to restore tenant', [$e->getMessage()], 500);
        }
    }

    /**
     * Get tenant's contracts
     */
    public function getContracts($id)
    {
        try {
            $tenant = User::role('tenant')->findOrFail($id);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($tenant->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if ($tenant->id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            $contracts = $this->tenantService->getTenantContracts($tenant);

            $data = $contracts->map(function ($contract) {
                return [
                    'id' => $contract->id,
                    'property' => $contract->room->property->name,
                    'room' => $contract->room->room_number,
                    'start_date' => $contract->start_date,
                    'end_date' => $contract->end_date,
                    'monthly_rent' => $contract->monthly_rent,
                    'status' => $contract->status,
                ];
            });

            return $this->sendResponse($data, 'Contracts retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve contracts', [$e->getMessage()], 500);
        }
    }

    /**
     * Get tenant's invoices
     */
    public function getInvoices($id)
    {
        try {
            $tenant = User::role('tenant')->findOrFail($id);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($tenant->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if ($tenant->id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            $invoices = $this->tenantService->getTenantInvoices($tenant);

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

            return $this->sendResponse($data, 'Invoices retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve invoices', [$e->getMessage()], 500);
        }
    }

    /**
     * Get tenant's payments
     */
    public function getPayments($id)
    {
        try {
            $tenant = User::role('tenant')->findOrFail($id);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($tenant->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if ($tenant->id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            $payments = $this->tenantService->getTenantPaymentHistory($tenant);

            $data = $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'invoice_number' => $payment->invoice->invoice_number,
                    'amount' => $payment->amount,
                    'payment_date' => $payment->payment_date,
                    'payment_method' => $payment->payment_method,
                    'status' => $payment->status,
                ];
            });

            return $this->sendResponse($data, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve payments', [$e->getMessage()], 500);
        }
    }

    /**
     * Get tenant's documents
     */
    public function getDocuments($id)
    {
        try {
            $tenant = User::role('tenant')->findOrFail($id);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($tenant->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if ($tenant->id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            $documents = $this->tenantService->getTenantDocuments($tenant);

            $data = $documents->map(function ($document) {
                return [
                    'id' => $document->id,
                    'file_name' => $document->file_name,
                    'file_type' => $document->file_type,
                    'description' => $document->description,
                    'uploaded_date' => $document->created_at,
                    'file_url' => asset($document->file_path),
                ];
            });

            return $this->sendResponse($data, 'Documents retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve documents', [$e->getMessage()], 500);
        }
    }

    /**
     * Send invite to tenant
     */
    public function sendInvite($id)
    {
        try {
            $tenant = User::role('tenant')->findOrFail($id);

            // Check authorization
            if ($tenant->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Generate invitation token
            $token = Str::random(64);

            // Store token (you might want to create an invitations table)
            \DB::table('tenant_invitations')->insert([
                'tenant_id' => $tenant->id,
                'token' => $token,
                'expires_at' => now()->addDays(7),
                'created_at' => now(),
            ]);

            // Send invitation
            $this->notificationService->sendNotification(
                $tenant,
                'invitation',
                'email',
                'Invitation to RoomGate',
                "You've been invited to join RoomGate. Click the link to activate your account: " . url("/activate/{$token}")
            );

            return $this->sendResponse(null, 'Invitation sent successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to send invitation', [$e->getMessage()], 500);
        }
    }

    /**
     * Search tenants
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();
            $query = $request->input('query');

            $tenants = User::role('tenant')
                ->where('landlord_id', $user->id)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%");
                })
                ->get();

            $data = $tenants->map(function ($tenant) {
                return $this->transformTenant($tenant);
            });

            return $this->sendResponse($data, 'Search results retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Search failed', [$e->getMessage()], 500);
        }
    }

    // ==================== Tenant Self-Service Methods ====================

    /**
     * Get current contract for logged-in tenant
     */
    public function getCurrentContract()
    {
        try {
            $tenant = Auth::user();

            $contract = $this->tenantService->getActiveContract($tenant);

            if (!$contract) {
                return $this->sendError('No active contract found', [], 404);
            }

            $data = [
                'id' => $contract->id,
                'property' => [
                    'name' => $contract->room->property->name,
                    'address' => $contract->room->property->address_line_1,
                ],
                'room' => [
                    'number' => $contract->room->room_number,
                    'floor' => $contract->room->floor,
                    'amenities' => $contract->room->amenities->pluck('name'),
                ],
                'start_date' => $contract->start_date,
                'end_date' => $contract->end_date,
                'monthly_rent' => $contract->monthly_rent,
                'deposit_amount' => $contract->deposit_amount,
                'days_remaining' => now()->diffInDays($contract->end_date),
                'status' => $contract->status,
            ];

            return $this->sendResponse($data, 'Contract retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve contract', [$e->getMessage()], 500);
        }
    }

    /**
     * Get invoices for logged-in tenant
     */
    public function getMyInvoices(Request $request)
    {
        try {
            $tenant = Auth::user();
            $filters = $request->only(['status', 'from_date', 'to_date']);

            $invoices = $this->tenantService->getTenantInvoices($tenant, $filters);

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
                    'is_overdue' => $invoice->due_date < now() && $invoice->status !== 'paid',
                ];
            });

            return $this->sendResponse($data, 'Invoices retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve invoices', [$e->getMessage()], 500);
        }
    }

    /**
     * Get payments for logged-in tenant
     */
    public function getMyPayments(Request $request)
    {
        try {
            $tenant = Auth::user();
            $limit = $request->input('limit', 20);

            $payments = $this->tenantService->getTenantPaymentHistory($tenant, $limit);

            $data = $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'invoice_number' => $payment->invoice->invoice_number,
                    'amount' => $payment->amount,
                    'payment_date' => $payment->payment_date,
                    'payment_method' => $payment->payment_method,
                    'transaction_reference' => $payment->transaction_reference,
                    'status' => $payment->status,
                ];
            });

            return $this->sendResponse($data, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve payments', [$e->getMessage()], 500);
        }
    }

    /**
     * Get documents for logged-in tenant
     */
    public function getMyDocuments()
    {
        try {
            $tenant = Auth::user();

            $documents = $this->tenantService->getTenantDocuments($tenant);

            $data = $documents->map(function ($document) {
                return [
                    'id' => $document->id,
                    'file_name' => $document->file_name,
                    'file_type' => $document->file_type,
                    'description' => $document->description,
                    'uploaded_date' => $document->created_at,
                    'file_url' => asset($document->file_path),
                ];
            });

            return $this->sendResponse($data, 'Documents retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve documents', [$e->getMessage()], 500);
        }
    }

    /**
     * Request contract renewal
     */
    public function requestRenewal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'preferred_end_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $tenant = Auth::user();
            $contract = $this->tenantService->getActiveContract($tenant);

            if (!$contract) {
                return $this->sendError('No active contract to renew', [], 404);
            }

            $result = $this->tenantService->requestContractRenewal($tenant, $contract, $request->all());

            if (!$result['success']) {
                return $this->sendError($result['message'], [], 400);
            }

            // Notify landlord
            $landlord = User::find($contract->room->property->landlord_id);
            $this->notificationService->sendNotification(
                $landlord,
                'renewal_request',
                'email',
                'Contract Renewal Request',
                "{$tenant->name} has requested to renew their contract for Room {$contract->room->room_number}"
            );

            return $this->sendResponse($result, 'Renewal request submitted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to request renewal', [$e->getMessage()], 500);
        }
    }

    /**
     * Submit maintenance request
     */
    public function submitMaintenanceRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'category' => 'required|in:plumbing,electrical,general,cleaning,pest_control,other',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $tenant = Auth::user();

            $maintenanceRequest = $this->tenantService->submitMaintenanceRequest($tenant, $request->all());

            if (!$maintenanceRequest) {
                return $this->sendError('Failed to submit maintenance request', [], 500);
            }

            return $this->sendResponse(['request_id' => $maintenanceRequest->id], 'Maintenance request submitted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to submit maintenance request', [$e->getMessage()], 500);
        }
    }

    /**
     * Get maintenance requests for logged-in tenant
     */
    public function getMaintenanceRequests()
    {
        try {
            $tenant = Auth::user();

            if (!class_exists(MaintenanceRequest::class)) {
                return $this->sendResponse([], 'Maintenance requests feature not available');
            }

            $requests = MaintenanceRequest::where('tenant_id', $tenant->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $requests->map(function ($request) {
                return [
                    'id' => $request->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'priority' => $request->priority,
                    'category' => $request->category,
                    'status' => $request->status,
                    'created_at' => $request->created_at,
                    'resolved_at' => $request->resolved_at,
                ];
            });

            return $this->sendResponse($data, 'Maintenance requests retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve maintenance requests', [$e->getMessage()], 500);
        }
    }

    /**
     * Get utility usage for logged-in tenant
     */
    public function getUtilityUsage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $tenant = Auth::user();

            $consumption = $this->tenantService->getUtilityConsumption(
                $tenant,
                Carbon::parse($request->from_date),
                Carbon::parse($request->to_date)
            );

            return $this->sendResponse($consumption, 'Utility usage retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve utility usage', [$e->getMessage()], 500);
        }
    }

    /**
     * Transform tenant for API response
     */
    protected function transformTenant($tenant, $detailed = false)
    {
        $activeContract = $tenant->contracts ? $tenant->contracts->where('status', 'active')->first() : null;

        $data = [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'email' => $tenant->email,
            'phone' => $tenant->phone,
            'status' => $tenant->status,
            'created_at' => $tenant->created_at,
            'has_active_contract' => $activeContract !== null,
        ];

        if ($activeContract) {
            $data['current_rental'] = [
                'property' => $activeContract->room->property->name,
                'room' => $activeContract->room->room_number,
                'monthly_rent' => $activeContract->monthly_rent,
                'contract_end' => $activeContract->end_date,
            ];
        }

        if ($detailed) {
            $data['total_contracts'] = $tenant->contracts ? $tenant->contracts->count() : 0;
            $data['image'] = $tenant->image ? asset($tenant->image) : null;
            $data['landlord_id'] = $tenant->landlord_id;
            $data['updated_at'] = $tenant->updated_at;
        }

        return $data;
    }
}