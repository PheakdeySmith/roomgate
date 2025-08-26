<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'contract_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'total_amount',
        'paid_amount',
        'payment_method',
        'payment_date',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    protected function balance(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_amount - $this->paid_amount,
        );
    }
    
    protected static function boot()
    {
        parent::boot();
        
        // When an invoice is updated, sync line items if status changed
        static::updated(function ($invoice) {
            // The isDirty check might not work as expected in the updated event
            // Instead, we'll use the status value directly
            try {
                $newStatus = $invoice->status;
                Log::info("Invoice ID {$invoice->id} status changed to {$newStatus}");
                
                if ($newStatus === 'paid') {
                    // Mark all line items as paid - update individually to avoid SQL quoting issues
                    foreach ($invoice->lineItems as $lineItem) {
                        $lineItem->status = 'paid';
                        $lineItem->paid_amount = $lineItem->amount;
                        $lineItem->save();
                    }
                } elseif ($newStatus === 'partial') {
                    // Only update if we have a valid amount
                    if ($invoice->paid_amount > 0 && $invoice->total_amount > 0) {
                        $paymentRatio = $invoice->paid_amount / $invoice->total_amount;
                        
                        // Update each line item individually
                        foreach ($invoice->lineItems as $lineItem) {
                            $lineItem->update([
                                'status' => 'partial',
                                'paid_amount' => round($lineItem->amount * $paymentRatio, 2)
                            ]);
                        }
                    }
                } else {
                    // For all other statuses (draft, sent, overdue, void)
                    // Update each item individually to avoid SQL quoting issues
                    foreach ($invoice->lineItems as $lineItem) {
                        $lineItem->status = $newStatus; // Properly quoted through Eloquent
                        $lineItem->paid_amount = 0;
                        $lineItem->save();
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error syncing line items status: " . $e->getMessage());
                Log::error($e->getTraceAsString());
            }
        });
    }
}
