<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'duration_days',
        'is_featured',
        'is_active',
        'properties_limit',
        'rooms_limit',
        'features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'features' => 'json',
    ];

    /**
     * Get all user subscriptions for this plan
     */
    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format((float)$this->price, 2);
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration_days == 7) {
            return '1 Week';
        } elseif ($this->duration_days == 30) {
            return '1 Month';
        } elseif ($this->duration_days == 365) {
            return '1 Year';
        } elseif ($this->duration_days == 0) {
            return 'Lifetime';
        } else {
            return $this->duration_days . ' Days';
        }
    }
}
