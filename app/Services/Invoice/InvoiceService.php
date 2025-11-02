<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Models\Contract;
use App\Models\LineItem;
use App\Models\UtilityBill;
use App\Models\BasePrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Exception;

class InvoiceService
{
    protected InvoiceNumberGenerator $numberGenerator;
    protected InvoiceCalculator $calculator;

    public function __construct()
    {
        $this->numberGenerator = new InvoiceNumberGenerator();
        $this->calculator = new InvoiceCalculator();
    }

    /**
     * Create a new invoice with line items
     */
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            // Create the main Invoice
            $invoice = Invoice::create([
                'contract_id' => $data['contract_id'],
                'invoice_number' => $data['invoice_number'],
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'status' => $data['status'] ?? 'draft',
                'paid_amount' => 0,
            ]);

            $totalAmount = 0;

            // Create line items
            foreach ($data['items'] as $itemData) {
                $lineable = null;

                // If it's a utility, create the UtilityBill record first
                if ($itemData['type'] === 'utility') {
                    $lineable = UtilityBill::create([
                        'contract_id' => $data['contract_id'],
                        'utility_type_id' => $itemData['utility_type_id'],
                        'billing_period_start' => $data['issue_date'],
                        'billing_period_end' => $data['issue_date'],
                        'start_reading' => $itemData['start_reading'] ?? null,
                        'end_reading' => $itemData['end_reading'] ?? null,
                        'consumption' => $itemData['consumption'],
                        'rate_applied' => $itemData['rate'],
                        'amount' => $itemData['amount'],
                    ]);
                }

                // Create the LineItem
                $lineItem = new LineItem([
                    'description' => $itemData['description'],
                    'amount' => $itemData['amount'],
                    'status' => $invoice->status,
                    'paid_amount' => 0,
                ]);

                $lineItem->invoice()->associate($invoice);

                if ($lineable) {
                    $lineItem->lineable()->associate($lineable);
                }

                $lineItem->save();
                $totalAmount += $lineItem->amount;
            }

            // Apply discount if provided
            if (isset($data['discount']) && $data['discount'] > 0) {
                $discountAmount = $totalAmount * ($data['discount'] / 100);
                LineItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => "Discount ({$data['discount']}%)",
                    'amount' => -$discountAmount,
                    'status' => $invoice->status,
                ]);
                $totalAmount -= $discountAmount;
            }

            // Update the final total
            $invoice->update(['total_amount' => $totalAmount]);

            return $invoice->fresh(['lineItems', 'contract']);
        });
    }

    /**
     * Update invoice status and sync line items
     */
    public function updateInvoiceStatus(Invoice $invoice, string $newStatus): Invoice
    {
        return DB::transaction(function () use ($invoice, $newStatus) {
            Log::info("Updating invoice #{$invoice->id} status to: {$newStatus}");

            $updateData = ['status' => $newStatus];

            // Handle payment data based on status
            switch ($newStatus) {
                case 'paid':
                    $updateData['payment_date'] = now();
                    $updateData['paid_amount'] = $invoice->total_amount;
                    break;

                case 'partial':
                    if (!$invoice->payment_date) {
                        $updateData['payment_date'] = now();
                    }
                    break;

                default:
                    $updateData['payment_date'] = null;
                    $updateData['paid_amount'] = 0;
                    break;
            }

            $invoice->update($updateData);

            // Sync line items status
            $this->syncLineItemsStatus($invoice, $newStatus);

            return $invoice->fresh();
        });
    }

    /**
     * Sync line items status based on invoice status
     */
    protected function syncLineItemsStatus(Invoice $invoice, string $status): void
    {
        switch ($status) {
            case 'paid':
                foreach ($invoice->lineItems as $lineItem) {
                    $lineItem->update([
                        'status' => 'paid',
                        'paid_amount' => $lineItem->amount,
                    ]);
                }
                break;

            case 'partial':
                if ($invoice->paid_amount > 0 && $invoice->total_amount > 0) {
                    $paymentRatio = $invoice->paid_amount / $invoice->total_amount;
                    foreach ($invoice->lineItems as $lineItem) {
                        $lineItem->update([
                            'status' => 'partial',
                            'paid_amount' => round($lineItem->amount * $paymentRatio, 2),
                        ]);
                    }
                }
                break;

            default:
                foreach ($invoice->lineItems as $lineItem) {
                    $lineItem->update([
                        'status' => $status,
                        'paid_amount' => 0,
                    ]);
                }
                break;
        }
    }

    /**
     * Generate invoice number for a landlord
     */
    public function generateInvoiceNumber($landlord): string
    {
        return $this->numberGenerator->generate($landlord);
    }

    /**
     * Validate invoice number uniqueness for a landlord
     */
    public function validateInvoiceNumber(string $invoiceNumber, int $contractId): bool
    {
        $contract = Contract::with('room.property')->find($contractId);
        if (!$contract) {
            return false;
        }

        $landlordId = $contract->room->property->landlord_id;

        $existingInvoice = Invoice::whereHas('contract.room.property', function ($query) use ($landlordId) {
            $query->where('landlord_id', $landlordId);
        })->where('invoice_number', $invoiceNumber)->exists();

        return !$existingInvoice;
    }

    /**
     * Get contract details for invoice creation
     */
    public function getContractDetailsForInvoice(Contract $contract): array
    {
        $contract->load('room.amenities', 'room.meters.meterReadings', 'room.property.utilityRates.utilityType');

        // Calculate rent amount
        $rentAmount = $this->calculator->calculateRentAmount($contract);

        // Get utility data
        $utilityData = $this->calculator->calculateUtilityData($contract);

        return [
            'room_number' => $contract->room->room_number,
            'base_price' => $rentAmount,
            'amenities' => $contract->room->amenities,
            'utility_data' => $utilityData,
        ];
    }

    /**
     * Get invoices for a landlord with filtering
     */
    public function getLandlordInvoices($landlord, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Invoice::whereHas('contract.room.property', fn($q) => $q->where('landlord_id', $landlord->id));

        // Apply filters
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', "%{$searchTerm}%")
                    ->orWhereHas('contract.tenant', function ($tenantQuery) use ($searchTerm) {
                        $tenantQuery->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        if (!empty($filters['date_range']) && strpos($filters['date_range'], ' to ') !== false) {
            [$startDate, $endDate] = explode(' to ', $filters['date_range']);
            $query->whereBetween('issue_date', [Carbon::parse($startDate), Carbon::parse($endDate)]);
        }

        if (!empty($filters['property_id'])) {
            $query->whereHas('contract.room.property', function ($q) use ($filters) {
                $q->where('id', $filters['property_id']);
            });
        }

        if (!empty($filters['room_type_id'])) {
            $query->whereHas('contract.room', function ($q) use ($filters) {
                $q->where('room_type_id', $filters['room_type_id']);
            });
        }

        if (!empty($filters['status']) && $filters['status'] !== 'any-status') {
            $query->where('status', $filters['status']);
        }

        $sortBy = $filters['sort_by'] ?? 'issue_date';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        return $query->with(['contract.tenant', 'contract.room'])
            ->paginate($filters['per_page'] ?? 15)
            ->appends($filters);
    }

    /**
     * Get dashboard statistics for landlord
     */
    public function getDashboardStats($landlord): array
    {
        $now = Carbon::now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();

        // Fetch recent invoices
        $recentInvoices = Invoice::whereHas('contract.room.property', fn($q) => $q->where('landlord_id', $landlord->id))
            ->where('issue_date', '>=', $lastMonthStart)
            ->with('lineItems')
            ->get();

        $invoicesThisMonth = $recentInvoices->where('issue_date', '>=', $thisMonthStart);
        $invoicesLastMonth = $recentInvoices->where('issue_date', '<', $thisMonthStart);

        // Calculate KPIs
        $revenueThisMonth = $invoicesThisMonth->sum('total_amount');
        $revenueLastMonth = $invoicesLastMonth->sum('total_amount');

        $paidThisMonth = $invoicesThisMonth->where('status', 'paid')->sum('paid_amount');
        $paidLastMonth = $invoicesLastMonth->where('status', 'paid')->sum('paid_amount');

        $utilityRevenueThisMonth = $invoicesThisMonth->pluck('lineItems')->flatten()
            ->where('lineable_type', UtilityBill::class)->sum('amount');
        $utilityRevenueLastMonth = $invoicesLastMonth->pluck('lineItems')->flatten()
            ->where('lineable_type', UtilityBill::class)->sum('amount');

        $cancelledThisMonth = $invoicesThisMonth->where('status', 'void')->sum('total_amount');
        $cancelledLastMonth = $invoicesLastMonth->where('status', 'void')->sum('total_amount');

        // New contracts count
        $newContractsThisMonth = Contract::whereHas('room.property', fn($q) => $q->where('landlord_id', $landlord->id))
            ->whereBetween('start_date', [$thisMonthStart, $now->copy()->endOfMonth()])->count();
        $newContractsLastMonth = Contract::whereHas('room.property', fn($q) => $q->where('landlord_id', $landlord->id))
            ->whereBetween('start_date', [$lastMonthStart, $now->copy()->subMonth()->endOfMonth()])->count();

        $calculateChange = fn($current, $previous) =>
            $previous > 0 ? (($current - $previous) / $previous) * 100 : ($current > 0 ? 100 : 0);

        return [
            'new_contracts' => [
                'current' => $newContractsThisMonth,
                'change' => $calculateChange($newContractsThisMonth, $newContractsLastMonth)
            ],
            'revenue' => [
                'current' => $revenueThisMonth,
                'change' => $calculateChange($revenueThisMonth, $revenueLastMonth)
            ],
            'utility_revenue' => [
                'current' => $utilityRevenueThisMonth,
                'change' => $calculateChange($utilityRevenueThisMonth, $utilityRevenueLastMonth)
            ],
            'paid' => [
                'current' => $paidThisMonth,
                'change' => $calculateChange($paidThisMonth, $paidLastMonth)
            ],
            'cancelled' => [
                'current' => $cancelledThisMonth,
                'change' => $calculateChange($cancelledThisMonth, $cancelledLastMonth)
            ],
        ];
    }

    /**
     * Send invoice to tenant
     */
    public function sendInvoiceToTenant(Invoice $invoice): bool
    {
        try {
            // Update status to sent if it's currently draft
            if ($invoice->status === 'draft') {
                $this->updateInvoiceStatus($invoice, 'sent');
            }

            // TODO: Implement email/notification sending logic
            // This would integrate with NotificationService

            return true;
        } catch (Exception $e) {
            Log::error("Failed to send invoice #{$invoice->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark invoice as overdue
     */
    public function markOverdueInvoices(): int
    {
        $overdueCount = 0;

        $overdueInvoices = Invoice::where('status', 'sent')
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $this->updateInvoiceStatus($invoice, 'overdue');
            $overdueCount++;
        }

        return $overdueCount;
    }

    /**
     * Calculate invoice balance
     */
    public function calculateBalance(Invoice $invoice): float
    {
        return $invoice->total_amount - $invoice->paid_amount;
    }

    /**
     * Process partial payment
     */
    public function processPartialPayment(Invoice $invoice, float $amount): Invoice
    {
        return DB::transaction(function () use ($invoice, $amount) {
            $invoice->update([
                'paid_amount' => min($amount, $invoice->total_amount),
                'status' => $amount >= $invoice->total_amount ? 'paid' : 'partial',
                'payment_date' => now(),
            ]);

            $this->syncLineItemsStatus($invoice, $invoice->status);

            return $invoice->fresh();
        });
    }
}