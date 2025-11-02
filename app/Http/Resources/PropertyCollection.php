<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PropertyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => PropertyResource::collection($this->collection),
            'statistics' => $this->when($request->has('include_stats'), [
                'total_properties' => $this->collection->count(),
                'total_rooms' => $this->collection->sum(function ($property) {
                    return $property->rooms()->count();
                }),
                'occupied_rooms' => $this->collection->sum(function ($property) {
                    return $property->rooms()
                        ->whereHas('contracts', function ($q) {
                            $q->active();
                        })
                        ->count();
                }),
                'available_rooms' => $this->collection->sum(function ($property) {
                    return $property->rooms()
                        ->whereDoesntHave('contracts', function ($q) {
                            $q->active();
                        })
                        ->count();
                }),
                'total_monthly_revenue' => $this->collection->sum(function ($property) {
                    return $property->rooms()
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
                'properties_by_type' => $this->collection->groupBy('type')->map->count(),
            ]),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param Request $request
     * @return array
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'filters_applied' => [
                    'type' => $request->type,
                    'city' => $request->city,
                    'status' => $request->status,
                    'has_available_rooms' => $request->has_available_rooms,
                ],
                'sort' => [
                    'field' => $request->sort_by ?? 'created_at',
                    'direction' => $request->sort_direction ?? 'desc',
                ],
            ],
        ];
    }
}