<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name');
            }),
            'email_verified' => !is_null($this->email_verified_at),
            'language' => $this->language ?? 'en',
            'timezone' => $this->timezone ?? 'Asia/Phnom_Penh',
            'notification_preferences' => $this->notification_preferences ?? [
                'email' => true,
                'sms' => false,
                'push' => false,
                'database' => true
            ],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Role-specific data
            'subscription' => $this->when($this->hasRole('landlord'), function () {
                return new SubscriptionResource($this->whenLoaded('subscription'));
            }),
            'properties_count' => $this->when($this->hasRole('landlord'), function () {
                return $this->properties()->count();
            }),
            'active_contracts_count' => $this->when($this->hasRole('tenant'), function () {
                return $this->tenantContracts()->active()->count();
            }),
        ];
    }
}