<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class UtilityBill extends Model
{
    use HasFactory;
    protected $fillable = [
        'contract_id',
        'utility_type_id',
        'billing_period_start',
        'billing_period_end',
        'start_reading',
        'end_reading',
        'consumption',
        'rate_applied',
        'amount',
    ];
    
    protected $appends = ['is_paid'];
    
    /**
     * Determine if this utility bill is paid based on its associated line item
     */
    public function getIsPaidAttribute()
    {
        // Get the linked line item
        $lineItem = $this->lineItem;
        
        // Return true if status is 'paid' or false otherwise
        return $lineItem && $lineItem->status === 'paid';
    }

    protected $casts = [
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function utilityType(): BelongsTo
    {
        return $this->belongsTo(UtilityType::class);
    }

    public function lineItem(): MorphOne
    {
        return $this->morphOne(LineItem::class, 'lineable');
    }
    
    /**
     * Get the property associated with this utility bill through the contract and room.
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id')->withDefault(function () {
            // If no direct property_id, try to get it through the contract relationship
            return $this->contract ? $this->contract->room->property : null;
        });
    }
}