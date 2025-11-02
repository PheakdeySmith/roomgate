<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Contract;
use App\Models\Property;
use App\Models\RoomType;
use App\Services\Invoice\InvoiceService;
use App\Services\Invoice\InvoiceCalculator;
use App\Services\Utility\UtilityBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    protected InvoiceService $invoiceService;
    protected InvoiceCalculator $invoiceCalculator;
    protected UtilityBillingService $utilityBillingService;

    public function __construct(
        InvoiceService $invoiceService,
        InvoiceCalculator $invoiceCalculator,
        UtilityBillingService $utilityBillingService
    ) {
        $this->invoiceService = $invoiceService;
        $this->invoiceCalculator = $invoiceCalculator;
        $this->utilityBillingService = $utilityBillingService;
    }
    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $landlord = Auth::user();

        // Get invoices using the service
        $invoices = $this->invoiceService->getLandlordInvoices($landlord, $request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'invoices' => $invoices,
                'pagination' => (string) $invoices->links('vendor.pagination.custom-pagination')
            ]);
        }

        // Get properties and room types for filters
        $properties = Property::where('landlord_id', $landlord->id)->orderBy('name')->get();
        $roomTypes = RoomType::where('landlord_id', $landlord->id)->orderBy('name')->get();

        // Get dashboard stats using the service
        $stats = $this->invoiceService->getDashboardStats($landlord);

        return view('backends.dashboard.payments.index', compact('invoices', 'stats', 'properties', 'roomTypes'));
    }

    /**
     * Show the form for creating a new invoice
     */
    public function create()
    {
        $landlord = Auth::user();

        if (!$landlord->hasRole('landlord')) {
            abort(403, 'Unauthorized');
        }

        // Get active contracts
        $contracts = Contract::with(['tenant', 'room.amenities'])
            ->where('status', 'active')
            ->whereHas('room.property', function ($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })
            ->get();

        // Generate invoice number using the service
        $invoiceNumber = $this->invoiceService->generateInvoiceNumber($landlord);

        $issueDate = now()->format('Y-m-d');
        $dueDate = now()->addDays(15)->format('Y-m-d');

        $qrCode1 = $landlord->qr_code_1 ? asset('uploads/qrcodes/' . $landlord->qr_code_1) : null;
        $qrCode2 = $landlord->qr_code_2 ? asset('uploads/qrcodes/' . $landlord->qr_code_2) : null;

        return view('backends.dashboard.payments.create', compact(
            'contracts',
            'invoiceNumber',
            'issueDate',
            'dueDate',
            'qrCode1',
            'qrCode2'
        ));
    }

    /**
     * Get contract details for invoice creation
     */
    public function getContractDetails(Contract $contract)
    {
        // Use service to get contract details
        $details = $this->invoiceService->getContractDetailsForInvoice($contract);

        return response()->json($details);
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request)
    {
        // For AJAX requests, ensure we return JSON responses for errors
        if ($request->expectsJson() || $request->ajax()) {
            $request->headers->set('Accept', 'application/json');
        }

        try {
            // Validate the request data
            $validatedData = $request->validate([
                'contract_id' => 'required|exists:contracts,id',
                'invoice_number' => 'required|string',
                'issue_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:issue_date',
                'discount' => 'nullable|numeric|min:0|max:100',
                'items' => 'required|array|min:1',
                'items.*.type' => 'required|string|in:rent,utility',
                'items.*.description' => 'required|string',
                'items.*.amount' => 'required|numeric|min:0',
                'items.*.utility_type_id' => 'required_if:items.*.type,utility|exists:utility_types,id',
                'items.*.start_reading' => 'nullable|numeric',
                'items.*.end_reading' => 'nullable|numeric',
                'items.*.consumption' => 'required_if:items.*.type,utility|numeric',
                'items.*.rate' => 'required_if:items.*.type,utility|numeric',
            ]);

            // Validate invoice number uniqueness using service
            if (!$this->invoiceService->validateInvoiceNumber($validatedData['invoice_number'], $validatedData['contract_id'])) {
                throw ValidationException::withMessages([
                    'invoice_number' => ['The invoice number has already been used. Please use a different number.']
                ]);
            }

            // Create invoice using the service
            $invoice = $this->invoiceService->createInvoice($validatedData);

        } catch (ValidationException $e) {
            Log::error('Invoice validation failed: ' . json_encode($e->errors()));

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('Invoice creation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create invoice: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to create invoice: ' . $e->getMessage())->withInput();
        }

        // Success response
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully!',
                'redirect_url' => route('landlord.payments.show', $invoice->id)
            ], 200);
        }

        return redirect()->route('landlord.payments.show', $invoice->id)
            ->with('success', 'Invoice created successfully!');
    }

    // In app/Http/Controllers/PaymentController.php

    public function show($invoiceId)
    {
        $landlord = Auth::user();

        // Find the invoice but ONLY if it belongs to the current landlord.
        $invoice = Invoice::where('id', $invoiceId)
            ->whereHas('contract.room.property', function ($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })
            ->with([
                'contract.tenant',
                'contract.room.property',
                'lineItems.lineable'
            ])
            ->firstOrFail(); // Fails to a 404 page if not found or not owned.

        // Now you are guaranteed to have the correct, authorized invoice.
        return view('backends.dashboard.payments.show', compact('invoice'));
    }


public function getInvoiceDetails(Invoice $invoice)
{
    // Authorize request
    if ($invoice->contract->room->property->landlord_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Load all necessary relationships
    $invoice->load([
        'contract.tenant',
        'contract.room.property',
        'lineItems.lineable',
    ]);

    // Return the invoice details
    return response()->json([
        'invoice' => $invoice,
        'tenant' => $invoice->contract->tenant,
        'property' => $invoice->contract->room->property,
        'room' => $invoice->contract->room,
        'line_items' => $invoice->lineItems,
    ]);
}

    /**
     * Update invoice status
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        try {
            // Authorize the request
            if ($invoice->contract->room->property->landlord_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Validate the incoming status
            $validated = $request->validate([
                'status' => 'required|string|in:draft,sent,paid,partial,overdue,void',
            ]);

            // Use service to update invoice status
            $updatedInvoice = $this->invoiceService->updateInvoiceStatus($invoice, $validated['status']);

            return response()->json([
                'message' => 'Invoice status updated successfully.',
                'invoice' => $updatedInvoice
            ]);

        } catch (\Exception $e) {
            Log::error('Invoice Status Update Failed: ' . $e->getMessage());
            Log::error('Exception Stack Trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Status update failed: ' . $e->getMessage(),
                'debug_info' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
}