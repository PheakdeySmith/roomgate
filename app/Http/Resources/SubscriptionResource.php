<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $now = Carbon::now();
        $endDate = Carbon::parse($this->end_date);
        $startDate = Carbon::parse($this->start_date);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'plan' => $this->whenLoaded('plan', function () {
                return [
                    'id' => $this->plan->id,
                    'name' => $this->plan->name,
                    'description' => $this->plan->description,
                    'price' => $this->plan->price,
                    'duration_days' => $this->plan->duration_days,
                    'property_limit' => $this->plan->property_limit,
                    'room_limit' => $this->plan->room_limit,
                    'features' => $this->plan->features ?? [],
                    'billing_cycle' => $this->plan->billing_cycle ?? 'monthly',
                ];
            }),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'is_cancelled' => $this->is_cancelled ?? false,
            'cancelled_at' => $this->cancelled_at,
            'cancellation_reason' => $this->cancellation_reason,
            'days_remaining' => $this->is_active ? max(0, $now->diffInDays($endDate, false)) : 0,
            'days_used' => $startDate->diffInDays($now),
            'is_expiring_soon' => $this->is_active && $now->diffInDays($endDate) <= 7,
            'is_expired' => !$this->is_active && $endDate->isPast(),
            'auto_renew' => $this->auto_renew ?? true,
            'next_billing_date' => $this->is_active && $this->auto_renew ? $this->end_date : null,
            'trial_ends_at' => $this->trial_ends_at,
            'is_on_trial' => $this->trial_ends_at && Carbon::parse($this->trial_ends_at)->isFuture(),
            'grace_period_ends' => $this->grace_period_ends,
            'is_in_grace_period' => $this->grace_period_ends && Carbon::parse($this->grace_period_ends)->isFuture(),
            'usage' => [
                'properties' => [
                    'used' => $this->user?->properties()->count() ?? 0,
                    'limit' => $this->plan?->property_limit ?? 0,
                    'percentage' => $this->plan?->property_limit > 0
                        ? round((($this->user?->properties()->count() ?? 0) / $this->plan->property_limit) * 100, 2)
                        : 0,
                    'remaining' => max(0, ($this->plan?->property_limit ?? 0) - ($this->user?->properties()->count() ?? 0))
                ],
                'rooms' => [
                    'used' => $this->user?->properties()->withCount('rooms')->get()->sum('rooms_count') ?? 0,
                    'limit' => $this->plan?->room_limit ?? 0,
                    'percentage' => $this->plan?->room_limit > 0
                        ? round((($this->user?->properties()->withCount('rooms')->get()->sum('rooms_count') ?? 0) / $this->plan->room_limit) * 100, 2)
                        : 0,
                    'remaining' => max(0, ($this->plan?->room_limit ?? 0) - ($this->user?->properties()->withCount('rooms')->get()->sum('rooms_count') ?? 0))
                ]
            ],
            'payment_method' => $this->payment_method,
            'last_payment_date' => $this->last_payment_date,
            'last_payment_amount' => $this->last_payment_amount,
            'total_paid' => $this->total_paid ?? 0,
            'stripe_subscription_id' => $this->when(
                $request->user()?->hasRole('admin'),
                $this->stripe_subscription_id
            ),
            'paypal_subscription_id' => $this->when(
                $request->user()?->hasRole('admin'),
                $this->paypal_subscription_id
            ),
            'notes' => $this->notes,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}