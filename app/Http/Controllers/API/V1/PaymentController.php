<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Payment;
use App\Models\Invoice;
use App\Services\Payment\PaymentService;
use App\Services\Invoice\InvoiceService;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends BaseController
{
    protected PaymentService $paymentService;
    protected InvoiceService $invoiceService;
    protected NotificationService $notificationService;

    public function __construct(
        PaymentService $paymentService,
        InvoiceService $invoiceService,
        NotificationService $notificationService
    ) {
        $this->paymentService = $paymentService;
        $this->invoiceService = $invoiceService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $filters = $request->only(['from_date', 'to_date', 'status', 'payment_method', 'invoice_id']);

            if ($user->hasRole('landlord')) {
                $payments = $this->paymentService->getLandlordPayments($user, $filters);
            } elseif ($user->hasRole('tenant')) {
                $payments = $this->paymentService->getTenantPayments($user, $filters);
            } else {
                return $this->sendError('Unauthorized role', [], 403);
            }

            $data = $payments->map(function ($payment) {
                return $this->transformPayment($payment);
            });

            if ($request->has('page')) {
                return $this->sendPaginatedResponse($payments, 'Payments retrieved successfully');
            }

            return $this->sendResponse($data, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve payments', [$e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,mobile_payment,check',
            'payment_date' => 'nullable|date',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $invoice = Invoice::with('contract')->findOrFail($request->invoice_id);

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

            // Check if invoice can receive payment
            if ($invoice->status === 'paid') {
                return $this->sendError('Invoice is already fully paid', [], 400);
            }

            if ($invoice->status === 'void') {
                return $this->sendError('Cannot make payment to a voided invoice', [], 400);
            }

            // Check payment amount
            $balance = $invoice->total_amount - $invoice->paid_amount;
            if ($request->amount > $balance) {
                return $this->sendError("Payment amount exceeds invoice balance of {$balance}", [], 400);
            }

            // Process payment using service
            $payment = $this->paymentService->processPayment($invoice, $request->all());

            // Send notification
            $this->notificationService->sendPaymentReceivedNotification($payment, ['email']);

            $data = $this->transformPayment($payment->load('invoice.contract'));

            return $this->sendResponse($data, 'Payment processed successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('Failed to process payment', [$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified payment
     */
    public function show($id)
    {
        try {
            $payment = Payment::with(['invoice.contract.tenant', 'invoice.contract.room.property'])
                ->findOrFail($id);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($payment->invoice->contract->room->property->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if ($payment->invoice->contract->user_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            $data = $this->transformPayment($payment, true);

            return $this->sendResponse($data, 'Payment details retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Payment not found', [$e->getMessage()], 404);
        }
    }

    /**
     * Process refund for a payment
     */
    public function refund(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'refund_amount' => 'required|numeric|min:0.01',
            'refund_reason' => 'required|string',
            'refund_method' => 'required|in:cash,bank_transfer,credit_card,mobile_payment,check',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $payment = Payment::findOrFail($id);

            // Check authorization
            if ($payment->invoice->contract->room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if payment can be refunded
            if ($payment->status === 'refunded') {
                return $this->sendError('Payment has already been refunded', [], 400);
            }

            if ($request->refund_amount > $payment->amount) {
                return $this->sendError('Refund amount exceeds payment amount', [], 400);
            }

            // Process refund using service
            $refund = $this->paymentService->refundPayment($payment, $request->refund_amount, $request->all());

            // Send notification
            $this->notificationService->sendNotification(
                $payment->invoice->contract->tenant,
                'payment_refunded',
                'email',
                'Payment Refunded',
                "A refund of {$request->refund_amount} has been processed for your payment."
            );

            return $this->sendResponse(['refund_id' => $refund->id], 'Refund processed successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to process refund', [$e->getMessage()], 500);
        }
    }

    /**
     * Get payment methods
     */
    public function getPaymentMethods()
    {
        $methods = [
            ['value' => 'cash', 'label' => 'Cash', 'icon' => 'cash'],
            ['value' => 'bank_transfer', 'label' => 'Bank Transfer', 'icon' => 'bank'],
            ['value' => 'credit_card', 'label' => 'Credit Card', 'icon' => 'credit-card'],
            ['value' => 'mobile_payment', 'label' => 'Mobile Payment', 'icon' => 'mobile'],
            ['value' => 'check', 'label' => 'Check', 'icon' => 'check'],
        ];

        return $this->sendResponse($methods, 'Payment methods retrieved successfully');
    }

    /**
     * Process bulk payments
     */
    public function bulkPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payments' => 'required|array|min:1',
            'payments.*.invoice_id' => 'required|exists:invoices,id',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,mobile_payment,check',
            'payment_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();
            $processed = [];
            $errors = [];

            foreach ($request->payments as $paymentData) {
                try {
                    $invoice = Invoice::with('contract')->findOrFail($paymentData['invoice_id']);

                    // Check authorization
                    if ($user->hasRole('landlord')) {
                        if ($invoice->contract->room->property->landlord_id !== $user->id) {
                            $errors[] = "Invoice #{$invoice->invoice_number}: Unauthorized";
                            continue;
                        }
                    } elseif ($user->hasRole('tenant')) {
                        if ($invoice->contract->user_id !== $user->id) {
                            $errors[] = "Invoice #{$invoice->invoice_number}: Unauthorized";
                            continue;
                        }
                    }

                    // Process payment
                    $payment = $this->paymentService->processPayment($invoice, [
                        'amount' => $paymentData['amount'],
                        'payment_method' => $request->payment_method,
                        'payment_date' => $request->payment_date,
                    ]);

                    $processed[] = $this->transformPayment($payment);

                } catch (\Exception $e) {
                    $errors[] = "Invoice #{$paymentData['invoice_id']}: " . $e->getMessage();
                }
            }

            $response = [
                'processed' => $processed,
                'errors' => $errors,
                'summary' => [
                    'total' => count($request->payments),
                    'successful' => count($processed),
                    'failed' => count($errors),
                    'total_amount' => collect($processed)->sum('amount'),
                ],
            ];

            return $this->sendResponse($response, 'Bulk payment processing completed');
        } catch (\Exception $e) {
            return $this->sendError('Bulk payment processing failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Get payment history
     */
    public function getHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();
            $limit = $request->input('limit', 50);

            if ($user->hasRole('tenant')) {
                $history = $this->paymentService->getTenantPaymentHistory($user, $limit);
            } else {
                $history = $this->paymentService->getPaymentHistory($user, $request->all());
            }

            $data = $history->map(function ($payment) {
                return $this->transformPayment($payment);
            });

            return $this->sendResponse($data, 'Payment history retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve payment history', [$e->getMessage()], 500);
        }
    }

    /**
     * Get pending payments
     */
    public function getPendingPayments()
    {
        try {
            $user = Auth::user();

            $pendingInvoices = $this->paymentService->getPendingPayments($user);

            $data = $pendingInvoices->map(function ($invoice) {
                return [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'tenant' => [
                        'id' => $invoice->contract->tenant->id,
                        'name' => $invoice->contract->tenant->name,
                    ],
                    'property' => $invoice->contract->room->property->name,
                    'room' => $invoice->contract->room->room_number,
                    'total_amount' => $invoice->total_amount,
                    'paid_amount' => $invoice->paid_amount,
                    'balance' => $invoice->total_amount - $invoice->paid_amount,
                    'due_date' => $invoice->due_date,
                    'days_overdue' => $invoice->due_date < now() ? now()->diffInDays($invoice->due_date) : 0,
                    'status' => $invoice->status,
                ];
            });

            return $this->sendResponse($data, 'Pending payments retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve pending payments', [$e->getMessage()], 500);
        }
    }

    /**
     * Make payment (for tenants)
     */
    public function makePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,mobile_payment,check',
            'transaction_reference' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();
            $invoice = Invoice::with('contract')->findOrFail($request->invoice_id);

            // Check if invoice belongs to tenant
            if ($invoice->contract->user_id !== $user->id) {
                return $this->sendError('This invoice does not belong to you', [], 403);
            }

            // Check payment eligibility
            $eligibility = $this->paymentService->validatePaymentEligibility($user, $invoice);
            if (!empty($eligibility)) {
                return $this->sendError('Payment not eligible', $eligibility, 400);
            }

            // Process payment
            $payment = $this->paymentService->processPayment($invoice, $request->all());

            // Send notifications
            $this->notificationService->sendPaymentReceivedNotification($payment, ['email']);

            $data = $this->transformPayment($payment->load('invoice'));

            return $this->sendResponse($data, 'Payment successful', 201);
        } catch (\Exception $e) {
            return $this->sendError('Payment failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Get tenant's payment history
     */
    public function getMyPaymentHistory(Request $request)
    {
        try {
            $user = Auth::user();
            $limit = $request->input('limit', 20);

            $payments = $this->paymentService->getTenantPaymentHistory($user, $limit);

            $data = $payments->map(function ($payment) {
                return $this->transformPayment($payment);
            });

            return $this->sendResponse($data, 'Payment history retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve payment history', [$e->getMessage()], 500);
        }
    }

    /**
     * Get tenant's pending payments
     */
    public function getMyPendingPayments()
    {
        try {
            $user = Auth::user();

            $pendingInvoices = Invoice::whereHas('contract', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
                ->where('status', '!=', 'paid')
                ->where('status', '!=', 'void')
                ->with(['contract.room.property', 'lineItems'])
                ->orderBy('due_date', 'asc')
                ->get();

            $data = $pendingInvoices->map(function ($invoice) {
                return [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'property' => $invoice->contract->room->property->name,
                    'room' => $invoice->contract->room->room_number,
                    'total_amount' => $invoice->total_amount,
                    'paid_amount' => $invoice->paid_amount,
                    'balance' => $invoice->total_amount - $invoice->paid_amount,
                    'due_date' => $invoice->due_date,
                    'is_overdue' => $invoice->due_date < now(),
                    'days_overdue' => $invoice->due_date < now() ? now()->diffInDays($invoice->due_date) : 0,
                    'status' => $invoice->status,
                ];
            });

            return $this->sendResponse($data, 'Pending payments retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve pending payments', [$e->getMessage()], 500);
        }
    }

    /**
     * Setup auto-pay for tenant
     */
    public function setupAutoPay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:credit_card,bank_transfer',
            'payment_day' => 'required|integer|min:1|max:28',
            'max_amount' => 'nullable|numeric|min:0',
            'card_number' => 'required_if:payment_method,credit_card|nullable|string',
            'card_expiry' => 'required_if:payment_method,credit_card|nullable|string',
            'bank_account' => 'required_if:payment_method,bank_transfer|nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            // Store auto-pay settings (encrypted)
            DB::table('auto_payment_settings')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'payment_method' => $request->payment_method,
                    'payment_day' => $request->payment_day,
                    'max_amount' => $request->max_amount,
                    'is_active' => true,
                    'payment_details' => encrypt($request->only(['card_number', 'card_expiry', 'bank_account'])),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            return $this->sendResponse(null, 'Auto-pay setup successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to setup auto-pay', [$e->getMessage()], 500);
        }
    }

    /**
     * Cancel auto-pay
     */
    public function cancelAutoPay()
    {
        try {
            $user = Auth::user();

            DB::table('auto_payment_settings')
                ->where('user_id', $user->id)
                ->update(['is_active' => false, 'updated_at' => now()]);

            return $this->sendResponse(null, 'Auto-pay cancelled successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to cancel auto-pay', [$e->getMessage()], 500);
        }
    }

    /**
     * Generate payment receipt
     */
    public function generateReceipt($id)
    {
        try {
            $payment = Payment::with([
                'invoice.contract.tenant',
                'invoice.contract.room.property',
                'invoice.lineItems'
            ])->findOrFail($id);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($payment->invoice->contract->room->property->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if ($payment->invoice->contract->user_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            $receipt = [
                'receipt_number' => 'RCP-' . str_pad($payment->id, 8, '0', STR_PAD_LEFT),
                'payment_date' => $payment->payment_date,
                'payment_amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'transaction_reference' => $payment->transaction_reference,
                'tenant' => [
                    'name' => $payment->invoice->contract->tenant->name,
                    'email' => $payment->invoice->contract->tenant->email,
                ],
                'property' => [
                    'name' => $payment->invoice->contract->room->property->name,
                    'address' => $payment->invoice->contract->room->property->address_line_1,
                ],
                'room' => $payment->invoice->contract->room->room_number,
                'invoice' => [
                    'number' => $payment->invoice->invoice_number,
                    'total_amount' => $payment->invoice->total_amount,
                    'balance_after_payment' => $payment->invoice->total_amount - $payment->invoice->paid_amount,
                ],
                'receipt_url' => url("/api/v1/payments/{$payment->id}/receipt/download"),
            ];

            return $this->sendResponse($receipt, 'Receipt generated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to generate receipt', [$e->getMessage()], 500);
        }
    }

    /**
     * Handle payment webhook
     */
    public function handleWebhook(Request $request, $provider)
    {
        Log::info("Payment webhook received from {$provider}", $request->all());

        try {
            // Validate webhook signature based on provider
            switch ($provider) {
                case 'stripe':
                    // Validate Stripe signature
                    break;
                case 'paypal':
                    // Validate PayPal signature
                    break;
                case 'aba':
                    // Validate ABA Bank signature
                    break;
                default:
                    return $this->sendError('Unknown payment provider', [], 400);
            }

            // Process webhook based on event type
            $eventType = $request->input('type') ?? $request->input('event_type');

            switch ($eventType) {
                case 'payment.succeeded':
                case 'payment_intent.succeeded':
                    // Handle successful payment
                    $this->handleSuccessfulPayment($request->all(), $provider);
                    break;

                case 'payment.failed':
                case 'payment_intent.payment_failed':
                    // Handle failed payment
                    $this->handleFailedPayment($request->all(), $provider);
                    break;

                case 'refund.created':
                    // Handle refund
                    $this->handleRefund($request->all(), $provider);
                    break;
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error("Webhook processing failed: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle successful payment from webhook
     */
    protected function handleSuccessfulPayment(array $data, string $provider)
    {
        // Implementation based on provider
        Log::info("Processing successful payment from {$provider}");
    }

    /**
     * Handle failed payment from webhook
     */
    protected function handleFailedPayment(array $data, string $provider)
    {
        // Implementation based on provider
        Log::info("Processing failed payment from {$provider}");
    }

    /**
     * Handle refund from webhook
     */
    protected function handleRefund(array $data, string $provider)
    {
        // Implementation based on provider
        Log::info("Processing refund from {$provider}");
    }

    /**
     * Transform payment for API response
     */
    protected function transformPayment($payment, $detailed = false)
    {
        $data = [
            'id' => $payment->id,
            'invoice_number' => $payment->invoice->invoice_number,
            'amount' => $payment->amount,
            'payment_date' => $payment->payment_date,
            'payment_method' => $payment->payment_method,
            'transaction_reference' => $payment->transaction_reference,
            'status' => $payment->status,
            'created_at' => $payment->created_at,
        ];

        if ($detailed) {
            $data['invoice'] = [
                'id' => $payment->invoice->id,
                'total_amount' => $payment->invoice->total_amount,
                'paid_amount' => $payment->invoice->paid_amount,
                'balance' => $payment->invoice->total_amount - $payment->invoice->paid_amount,
                'status' => $payment->invoice->status,
            ];
            $data['tenant'] = [
                'id' => $payment->invoice->contract->tenant->id,
                'name' => $payment->invoice->contract->tenant->name,
                'email' => $payment->invoice->contract->tenant->email,
            ];
            $data['property'] = [
                'id' => $payment->invoice->contract->room->property->id,
                'name' => $payment->invoice->contract->room->property->name,
            ];
            $data['room'] = [
                'id' => $payment->invoice->contract->room->id,
                'number' => $payment->invoice->contract->room->room_number,
            ];
            $data['notes'] = $payment->notes;
        }

        return $data;
    }
}