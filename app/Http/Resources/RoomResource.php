<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $activeContract = $this->contracts()
            ->active()
            ->with('tenant')
            ->first();

        return [
            'id' => $this->id,
            'room_number' => $this->room_number,
            'floor' => $this->floor,
            'type' => $this->type,
            'size_sqm' => $this->size_sqm,
            'monthly_rent' => $this->monthly_rent,
            'deposit_amount' => $this->deposit_amount,
            'description' => $this->description,
            'features' => $this->features ?? [],
            'amenities' => $this->amenities ?? [],
            'images' => $this->images ? collect($this->images)->map(function ($image) {
                return asset('storage/' . $image);
            }) : [],
            'status' => $this->status,
            'is_available' => !$activeContract,
            'availability_date' => $this->when(!$activeContract, function () {
                return $this->availability_date ?? now()->toDateString();
            }),
            'property' => new PropertyResource($this->whenLoaded('property')),
            'current_contract' => $this->when($activeContract, function () use ($activeContract) {
                return new ContractResource($activeContract);
            }),
            'current_tenant' => $this->when($activeContract, function () use ($activeContract) {
                return new UserResource($activeContract->tenant);
            }),
            'meters' => MeterResource::collection($this->whenLoaded('meters')),
            'pricing' => $this->whenLoaded('pricing', function () {
                return [
                    'electricity_rate' => $this->pricing->electricity_rate ?? 0,
                    'water_rate' => $this->pricing->water_rate ?? 0,
                    'garbage_fee' => $this->pricing->garbage_fee ?? 0,
                    'internet_fee' => $this->pricing->internet_fee ?? 0,
                    'parking_fee' => $this->pricing->parking_fee ?? 0,
                    'cleaning_fee' => $this->pricing->cleaning_fee ?? 0,
                ];
            }),
            'maintenance_requests' => $this->when($request->user()?->hasRole('landlord'), function () {
                return $this->maintenanceRequests()
                    ->pending()
                    ->count();
            }),
            'last_inspection_date' => $this->last_inspection_date,
            'next_inspection_date' => $this->next_inspection_date,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}