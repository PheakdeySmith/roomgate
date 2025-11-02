<?php

namespace App\Services\Payment;

use App\Models\Invoice;
use App\Models\Contract;
use App\Models\LineItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process a payment for an invoice
     */
    public function processPayment(Invoice $invoice, array $data): Payment
    {
        return DB::transaction(function () use ($invoice, $data) {
            // Create payment record
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'payment_date' => $data['payment_date'] ?? now(),
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'completed',
            ]);

            // Update invoice paid amount
            $totalPaid = $invoice->paid_amount + $data['amount'];
            $invoice->update([
                'paid_amount' => $totalPaid,
                'payment_method' => $data['payment_method'],
                'payment_date' => $data['payment_date'] ?? now(),
            ]);

            // Update invoice status based on payment
            $this->updateInvoiceStatusAfterPayment($invoice, $totalPaid);

            // Distribute payment across line items
            $this->distributePaymentToLineItems($invoice, $totalPaid);

            Log::info("Payment processed for invoice #{$invoice->id}: {$data['amount']}");

            return $payment;
        });
    }

    /**
     * Process a bulk payment for multiple invoices
     */
    public function processBulkPayment(array $invoiceIds, array $data): Collection
    {
        $payments = collect();

        DB::transaction(function () use ($invoiceIds, $data, &$payments) {
            $totalAmount = $data['amount'];
            $remainingAmount = $totalAmount;

            // Process each invoice
            foreach ($invoiceIds as $invoiceId) {
                $invoice = Invoice::find($invoiceId);
                if (!$invoice) continue;

                $balance = $invoice->total_amount - $invoice->paid_amount;
                if ($balance <= 0) continue;

                $paymentAmount = min($remainingAmount, $balance);

                $payment = $this->processPayment($invoice, [
                    'amount' => $paymentAmount,
                    'payment_method' => $data['payment_method'],
                    'payment_date' => $data['payment_date'] ?? now(),
                    'reference_number' => $data['reference_number'] ?? null,
                    'notes' => "Bulk payment - Part of batch payment",
                ]);

                $payments->push($payment);
                $remainingAmount -= $paymentAmount;

                if ($remainingAmount <= 0) break;
            }
        });

        return $payments;
    }

    /**
     * Refund a payment
     */
    public function refundPayment(Payment $payment, float $refundAmount = null): Payment
    {
        return DB::transaction(function () use ($payment, $refundAmount) {
            $refundAmount = $refundAmount ?? $payment->amount;

            // Create refund record
            $refund = Payment::create([
                'invoice_id' => $payment->invoice_id,
                'amount' => -$refundAmount, // Negative amount for refund
                'payment_method' => $payment->payment_method,
                'payment_date' => now(),
                'reference_number' => 'REFUND-' . $payment->reference_number,
                'notes' => 'Refund for payment #' . $payment->id,
                'status' => 'refunded',
                'parent_payment_id' => $payment->id,
            ]);

            // Update invoice
            $invoice = $payment->invoice;
            $newPaidAmount = max(0, $invoice->paid_amount - $refundAmount);
            $invoice->update(['paid_amount' => $newPaidAmount]);

            // Update invoice status
            $this->updateInvoiceStatusAfterPayment($invoice, $newPaidAmount);

            // Update payment status
            $payment->update(['status' => 'refunded']);

            Log::info("Refund processed for payment #{$payment->id}: {$refundAmount}");

            return $refund;
        });
    }

    /**
     * Update invoice status after payment
     */
    protected function updateInvoiceStatusAfterPayment(Invoice $invoice, float $totalPaid): void
    {
        if ($totalPaid >= $invoice->total_amount) {
            $invoice->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'partial']);
        } else {
            // Check if overdue
            if ($invoice->due_date < now() && $invoice->status === 'sent') {
                $invoice->update(['status' => 'overdue']);
            }
        }
    }

    /**
     * Distribute payment across line items
     */
    protected function distributePaymentToLineItems(Invoice $invoice, float $totalPaid): void
    {
        if ($invoice->total_amount <= 0) return;

        $paymentRatio = min(1, $totalPaid / $invoice->total_amount);

        foreach ($invoice->lineItems as $lineItem) {
            $paidAmount = round($lineItem->amount * $paymentRatio, 2);
            $lineItem->update([
                'paid_amount' => $paidAmount,
                'status' => $paidAmount >= $lineItem->amount ? 'paid' : 'partial',
            ]);
        }
    }

    /**
     * Get payment history for a contract
     */
    public function getContractPaymentHistory(Contract $contract): Collection
    {
        return Payment::whereHas('invoice', function ($query) use ($contract) {
            $query->where('contract_id', $contract->id);
        })
            ->with('invoice')
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    /**
     * Get payment history for a tenant
     */
    public function getTenantPaymentHistory($tenantId): Collection
    {
        return Payment::whereHas('invoice.contract', function ($query) use ($tenantId) {
            $query->where('user_id', $tenantId);
        })
            ->with('invoice.contract.room')
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    /**
     * Calculate total payments for a period
     */
    public function calculateTotalPayments($landlordId, Carbon $startDate, Carbon $endDate): array
    {
        $payments = Payment::whereHas('invoice.contract.room.property', function ($query) use ($landlordId) {
            $query->where('landlord_id', $landlordId);
        })
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('amount', '>', 0) // Exclude refunds
            ->get();

        $byMethod = $payments->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ];
        });

        return [
            'total_amount' => $payments->sum('amount'),
            'total_count' => $payments->count(),
            'by_method' => $byMethod,
            'average_payment' => $payments->avg('amount'),
        ];
    }

    /**
     * Get overdue payments
     */
    public function getOverduePayments($landlordId): Collection
    {
        return Invoice::whereHas('contract.room.property', function ($query) use ($landlordId) {
            $query->where('landlord_id', $landlordId);
        })
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'void')
            ->where('due_date', '<', now())
            ->with(['contract.tenant', 'contract.room'])
            ->get()
            ->map(function ($invoice) {
                return [
                    'invoice' => $invoice,
                    'days_overdue' => now()->diffInDays($invoice->due_date),
                    'balance' => $invoice->total_amount - $invoice->paid_amount,
                ];
            });
    }

    /**
     * Process automated recurring payments
     */
    public function processRecurringPayments(): int
    {
        $processed = 0;

        // Get contracts with recurring payment setup
        $contracts = Contract::where('status', 'active')
            ->where('auto_payment', true)
            ->whereNotNull('payment_method_id')
            ->get();

        foreach ($contracts as $contract) {
            // Check if invoice exists for current period
            $currentPeriodInvoice = Invoice::where('contract_id', $contract->id)
                ->whereMonth('issue_date', now()->month)
                ->whereYear('issue_date', now()->year)
                ->first();

            if ($currentPeriodInvoice && $currentPeriodInvoice->status !== 'paid') {
                try {
                    $this->processPayment($currentPeriodInvoice, [
                        'amount' => $currentPeriodInvoice->total_amount,
                        'payment_method' => 'auto_debit',
                        'notes' => 'Automated recurring payment',
                    ]);
                    $processed++;
                } catch (\Exception $e) {
                    Log::error("Failed to process recurring payment for invoice #{$currentPeriodInvoice->id}: " . $e->getMessage());
                }
            }
        }

        return $processed;
    }

    /**
     * Send payment reminder
     */
    public function sendPaymentReminder(Invoice $invoice): bool
    {
        try {
            // This would integrate with NotificationService
            // For now, just log the action
            Log::info("Payment reminder sent for invoice #{$invoice->id}");

            $invoice->update([
                'last_reminder_date' => now(),
                'reminder_count' => ($invoice->reminder_count ?? 0) + 1,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send payment reminder for invoice #{$invoice->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Apply late fee to overdue invoice
     */
    public function applyLateFee(Invoice $invoice, float $feeAmount = null): LineItem
    {
        $feeAmount = $feeAmount ?? ($invoice->total_amount * 0.05); // 5% default late fee

        $lateFee = LineItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Late payment fee',
            'amount' => $feeAmount,
            'status' => $invoice->status,
            'paid_amount' => 0,
        ]);

        // Update invoice total
        $invoice->increment('total_amount', $feeAmount);

        Log::info("Late fee of {$feeAmount} applied to invoice #{$invoice->id}");

        return $lateFee;
    }

    /**
     * Generate payment receipt
     */
    public function generateReceipt(Payment $payment): array
    {
        $invoice = $payment->invoice;
        $contract = $invoice->contract;

        return [
            'receipt_number' => 'RCP-' . str_pad($payment->id, 8, '0', STR_PAD_LEFT),
            'payment_date' => $payment->payment_date,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'reference_number' => $payment->reference_number,
            'invoice_number' => $invoice->invoice_number,
            'tenant_name' => $contract->tenant->name,
            'property' => $contract->room->property->name,
            'room' => $contract->room->room_number,
            'balance_after_payment' => $invoice->total_amount - $invoice->paid_amount,
        ];
    }

    /**
     * Validate payment data
     */
    public function validatePayment(Invoice $invoice, float $amount): array
    {
        $errors = [];

        if ($amount <= 0) {
            $errors[] = 'Payment amount must be greater than zero';
        }

        $balance = $invoice->total_amount - $invoice->paid_amount;
        if ($amount > $balance) {
            $errors[] = 'Payment amount exceeds invoice balance';
        }

        if ($invoice->status === 'void') {
            $errors[] = 'Cannot process payment for voided invoice';
        }

        if ($invoice->status === 'paid') {
            $errors[] = 'Invoice is already fully paid';
        }

        return $errors;
    }
}