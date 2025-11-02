<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Invoice;
use App\Models\Contract;
use App\Services\Invoice\InvoiceService;
use App\Services\Invoice\InvoiceCalculator;
use App\Services\Notification\NotificationService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InvoiceController extends BaseController
{
    protected InvoiceService $invoiceService;
    protected InvoiceCalculator $invoiceCalculator;
    protected NotificationService $notificationService;
    protected PaymentService $paymentService;

    public function __construct(
        InvoiceService $invoiceService,
        InvoiceCalculator $invoiceCalculator,
        NotificationService $notificationService,
        PaymentService $paymentService
    ) {
        $this->invoiceService = $invoiceService;
        $this->invoiceCalculator = $invoiceCalculator;
        $this->notificationService = $notificationService;
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            $filters = $request->only(['status', 'from_date', 'to_date', 'contract_id', 'property_id', 'room_id']);

            $invoices = $this->invoiceService->getLandlordInvoices($user, $filters);

            $data = $invoices->map(function ($invoice) {
                return $this->transformInvoice($invoice);
            });

            if ($request->has('page')) {
                return $this->sendPaginatedResponse($invoices, 'Invoices retrieved successfully');
            }

            return $this->sendResponse($data, 'Invoices retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve invoices', [$e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contract_id' => 'required|exists:contracts,id',
            'invoice_number' => 'nullable|string|unique:invoices,invoice_number',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'discount' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|string|in:rent,utility,other',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.utility_type_id' => 'required_if:items.*.type,utility|exists:utility_types,id',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $contract = Contract::findOrFail($request->contract_id);

            // Check authorization
            if ($contract->room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Generate invoice number if not provided
            if (!$request->has('invoice_number')) {
                $request->merge([
                    'invoice_number' => $this->invoiceService->generateInvoiceNumber(Auth::user())
                ]);
            }

            // Create invoice
            $invoice = $this->invoiceService->createInvoice($request->all());

            // Send notification
            $this->notificationService->sendInvoiceCreatedNotification($invoice, ['email']);

            $data = $this->transformInvoice($invoice->fresh(['lineItems', 'contract.tenant']));

            return $this->sendResponse($data, 'Invoice created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('Failed to create invoice', [$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified invoice
     */
    public function show($id)
    {
        try {
            $invoice = Invoice::with(['contract.tenant', 'contract.room.property', 'lineItems'])
                ->findOrFail($id);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($invoice->contract->room->property->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if ($invoice->contract->user_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            $data = $this->transformInvoice($invoice, true);

            return $this->sendResponse($data, 'Invoice retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Invoice not found', [$e->getMessage()], 404);
        }
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'issue_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date|after_or_equal:issue_date',
            'discount' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $invoice = Invoice::findOrFail($id);

            // Check authorization
            if ($invoice->contract->room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if invoice can be edited
            if ($invoice->status === 'paid') {
                return $this->sendError('Cannot edit paid invoice', [], 400);
            }

            $invoice->update($request->only(['issue_date', 'due_date', 'discount', 'notes']));

            // Recalculate if discount changed
            if ($request->has('discount')) {
                $invoice = $this->invoiceCalculator->recalculateInvoice($invoice);
            }

            $data = $this->transformInvoice($invoice->fresh());

            return $this->sendResponse($data, 'Invoice updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update invoice', [$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified invoice
     */
    public function destroy($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            // Check authorization
            if ($invoice->contract->room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if invoice can be deleted
            if ($invoice->status === 'paid' || $invoice->paid_amount > 0) {
                return $this->sendError('Cannot delete invoice with payments', [], 400);
            }

            $invoice->delete();

            return $this->sendResponse(null, 'Invoice deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete invoice', [$e->getMessage()], 500);
        }
    }

    /**
     * Send invoice to tenant
     */
    public function send($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            // Check authorization
            if ($invoice->contract->room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Update status to sent
            if ($invoice->status === 'draft') {
                $this->invoiceService->updateInvoiceStatus($invoice, 'sent');
            }

            // Send notification
            $this->notificationService->sendInvoiceCreatedNotification($invoice, ['email', 'sms']);

            return $this->sendResponse(null, 'Invoice sent successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to send invoice', [$e->getMessage()], 500);
        }
    }

    /**
     * Void an invoice
     */
    public function void($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            // Check authorization
            if ($invoice->contract->room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if invoice can be voided
            if ($invoice->status === 'paid') {
                return $this->sendError('Cannot void paid invoice', [], 400);
            }

            $this->invoiceService->updateInvoiceStatus($invoice, 'void');

            return $this->sendResponse(null, 'Invoice voided successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to void invoice', [$e->getMessage()], 500);
        }
    }

    /**
     * Get invoice line items
     */
    public function getLineItems($id)
    {
        try {
            $invoice = Invoice::with('lineItems.lineable')->findOrFail($id);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($invoice->contract->room->property->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if ($invoice->contract->user_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            $data = $invoice->lineItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'description' => $item->description,
                    'amount' => $item->amount,
                    'type' => $item->lineable_type,
                    'status' => $item->status,
                    'paid_amount' => $item->paid_amount,
                ];
            });

            return $this->sendResponse($data, 'Line items retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve line items', [$e->getMessage()], 500);
        }
    }

    /**
     * Get overdue invoices
     */
    public function getOverdueInvoices()
    {
        try {
            $user = Auth::user();

            $invoices = Invoice::whereHas('contract.room.property', function ($q) use ($user) {
                $q->where('landlord_id', $user->id);
            })
                ->where('status', '!=', 'paid')
                ->where('status', '!=', 'void')
                ->where('due_date', '<', now())
                ->with(['contract.tenant', 'contract.room'])
                ->get();

            $data = $invoices->map(function ($invoice) {
                $transformed = $this->transformInvoice($invoice);
                $transformed['days_overdue'] = now()->diffInDays($invoice->due_date);
                return $transformed;
            });

            return $this->sendResponse($data, 'Overdue invoices retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve overdue invoices', [$e->getMessage()], 500);
        }
    }

    /**
     * Bulk create invoices
     */
    public function bulkCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contract_ids' => 'required|array',
            'contract_ids.*' => 'exists:contracts,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'include_utilities' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();
            $created = [];
            $errors = [];

            foreach ($request->contract_ids as $contractId) {
                $contract = Contract::find($contractId);

                // Check authorization
                if ($contract->room->property->landlord_id !== $user->id) {
                    $errors[] = "Contract #{$contractId}: Unauthorized";
                    continue;
                }

                try {
                    // Generate invoice data
                    $invoiceData = [
                        'contract_id' => $contractId,
                        'invoice_number' => $this->invoiceService->generateInvoiceNumber($user),
                        'issue_date' => $request->issue_date,
                        'due_date' => $request->due_date,
                        'items' => [],
                    ];

                    // Add rent item
                    $invoiceData['items'][] = [
                        'type' => 'rent',
                        'description' => 'Monthly Rent',
                        'amount' => $contract->monthly_rent ?? 0,
                    ];

                    // Create invoice
                    $invoice = $this->invoiceService->createInvoice($invoiceData);
                    $created[] = $this->transformInvoice($invoice);

                } catch (\Exception $e) {
                    $errors[] = "Contract #{$contractId}: " . $e->getMessage();
                }
            }

            $response = [
                'created' => $created,
                'errors' => $errors,
                'summary' => [
                    'total' => count($request->contract_ids),
                    'successful' => count($created),
                    'failed' => count($errors),
                ],
            ];

            return $this->sendResponse($response, 'Bulk invoice creation completed');
        } catch (\Exception $e) {
            return $this->sendError('Bulk invoice creation failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Search invoices
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

            $invoices = Invoice::whereHas('contract.room.property', function ($q) use ($user) {
                $q->where('landlord_id', $user->id);
            })
                ->where(function ($q) use ($query) {
                    $q->where('invoice_number', 'like', "%{$query}%")
                        ->orWhereHas('contract.tenant', function ($subQ) use ($query) {
                            $subQ->where('name', 'like', "%{$query}%");
                        });
                })
                ->with(['contract.tenant', 'contract.room'])
                ->get();

            $data = $invoices->map(function ($invoice) {
                return $this->transformInvoice($invoice);
            });

            return $this->sendResponse($data, 'Search results retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Search failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Transform invoice for API response
     */
    protected function transformInvoice($invoice, $detailed = false)
    {
        $data = [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'issue_date' => $invoice->issue_date,
            'due_date' => $invoice->due_date,
            'total_amount' => $invoice->total_amount,
            'paid_amount' => $invoice->paid_amount,
            'balance' => $invoice->total_amount - $invoice->paid_amount,
            'status' => $invoice->status,
            'discount' => $invoice->discount,
            'discount_amount' => $invoice->discount_amount,
            'notes' => $invoice->notes,
            'tenant' => [
                'id' => $invoice->contract->tenant->id,
                'name' => $invoice->contract->tenant->name,
                'email' => $invoice->contract->tenant->email,
            ],
            'property' => [
                'id' => $invoice->contract->room->property->id,
                'name' => $invoice->contract->room->property->name,
            ],
            'room' => [
                'id' => $invoice->contract->room->id,
                'number' => $invoice->contract->room->room_number,
            ],
            'created_at' => $invoice->created_at,
        ];

        if ($detailed && $invoice->relationLoaded('lineItems')) {
            $data['line_items'] = $invoice->lineItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'description' => $item->description,
                    'amount' => $item->amount,
                    'status' => $item->status,
                    'paid_amount' => $item->paid_amount,
                ];
            });
        }

        return $data;
    }
}