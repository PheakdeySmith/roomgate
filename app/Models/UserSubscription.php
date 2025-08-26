<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'start_date',
        'end_date',
        'status',
        'payment_status',
        'payment_method',
        'transaction_id',
        'amount_paid',
        'notes',
        'meta_data',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'amount_paid' => 'decimal:2',
        'meta_data' => 'json',
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription plan
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    /**
     * Check if subscription is in trial period
     */
    public function isInTrial(): bool
    {
        return $this->payment_status === 'trial' && $this->end_date > now();
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->end_date < now()) {
            return 0;
        }

        return (int)now()->diffInDays($this->end_date, false);
    }

    /**
     * Get formatted amount paid
     */
    public function getFormattedAmountPaidAttribute(): string
    {
        return '$' . number_format((float)$this->amount_paid, 2);
    }
    
    /**
     * Scope a query to only include active subscriptions
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
                     ->where('end_date', '>', now());
    }
    
    /**
     * Scope a query to only include expired subscriptions
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('end_date', '<', now());
    }
    
    /**
     * Scope a query to only include trial subscriptions
     */
    public function scopeTrial(Builder $query): Builder
    {
        return $query->where('payment_status', 'trial')
                     ->where('end_date', '>', now());
    }
    
    /**
     * Scope a query to only include subscriptions with a specific status
     */
    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope a query to only include subscriptions expiring within a certain number of days
     */
    public function scopeExpiringWithin(Builder $query, int $days): Builder
    {
        $now = now();
        $future = $now->copy()->addDays($days);
        
        return $query->where('status', 'active')
                     ->where('end_date', '>', $now)
                     ->where('end_date', '<=', $future);
    }
}
