<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Synchronize LineItem statuses with their parent Invoice statuses
        $invoices = DB::table('invoices')->get(['id', 'status', 'paid_amount', 'total_amount']);
        
        foreach ($invoices as $invoice) {
            $newStatus = $invoice->status;
            
            // Update all line items to match their parent invoice's status
            if ($newStatus == 'paid') {
                DB::table('line_items')
                  ->where('invoice_id', $invoice->id)
                  ->update([
                      'status' => 'paid',
                      'paid_amount' => DB::raw('amount')
                  ]);
            } elseif ($newStatus == 'partial') {
                // Calculate payment ratio for partial payments
                if ($invoice->paid_amount > 0 && $invoice->total_amount > 0) {
                    $paymentRatio = $invoice->paid_amount / $invoice->total_amount;
                    
                    // Get all line items for this invoice
                    $lineItems = DB::table('line_items')
                                   ->where('invoice_id', $invoice->id)
                                   ->get(['id', 'amount']);
                    
                    // Update each line item individually
                    foreach ($lineItems as $lineItem) {
                        DB::table('line_items')
                          ->where('id', $lineItem->id)
                          ->update([
                              'status' => 'partial',
                              'paid_amount' => round($lineItem->amount * $paymentRatio, 2)
                          ]);
                    }
                }
            } else {
                // For all other statuses (draft, sent, overdue, void)
                DB::table('line_items')
                  ->where('invoice_id', $invoice->id)
                  ->update([
                      'status' => $newStatus,
                      'paid_amount' => 0
                  ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need for down migration - this is a one-time data sync
    }
};
