<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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
            'type' => $this->type,
            'address' => $this->address,
            'city' => $this->city,
            'district' => $this->district,
            'commune' => $this->commune,
            'description' => $this->description,
            'features' => $this->features ?? [],
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'total_rooms' => $this->whenCounted('rooms'),
            'available_rooms' => $this->when($this->rooms_count !== null, function () {
                return $this->rooms()
                    ->whereDoesntHave('contracts', function ($q) {
                        $q->active();
                    })
                    ->count();
            }),
            'occupied_rooms' => $this->when($this->rooms_count !== null, function () {
                return $this->rooms()
                    ->whereHas('contracts', function ($q) {
                        $q->active();
                    })
                    ->count();
            }),
            'total_floors' => $this->total_floors,
            'year_built' => $this->year_built,
            'parking_spaces' => $this->parking_spaces,
            'amenities' => $this->amenities ?? [],
            'utilities_included' => $this->utilities_included ?? [],
            'rules' => $this->rules ?? [],
            'landlord' => new UserResource($this->whenLoaded('landlord')),
            'rooms' => RoomResource::collection($this->whenLoaded('rooms')),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'monthly_revenue' => $this->when($request->user()?->id === $this->landlord_id, function () {
                return $this->rooms()
                    ->whereHas('contracts', function ($q) {
                        $q->active();
                    })
                    ->with('contracts')
                    ->get()
                    ->pluck('contracts')
                    ->flatten()
                    ->where('status', 'active')
                    ->sum('monthly_rent');
            }),
            'status' => $this->status ?? 'active',
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}