<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeterResource extends JsonResource
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
            'meter_number' => $this->meter_number,
            'type' => $this->type,
            'room_id' => $this->room_id,
            'room' => new RoomResource($this->whenLoaded('room')),
            'initial_reading' => $this->initial_reading,
            'current_reading' => $this->currentReading?->reading_value ?? $this->initial_reading,
            'last_reading_date' => $this->currentReading?->reading_date,
            'unit' => $this->unit ?? ($this->type === 'electricity' ? 'kWh' : 'm³'),
            'rate_per_unit' => $this->rate_per_unit,
            'is_active' => $this->is_active ?? true,
            'installation_date' => $this->installation_date,
            'last_maintenance_date' => $this->last_maintenance_date,
            'next_maintenance_date' => $this->next_maintenance_date,
            'manufacturer' => $this->manufacturer,
            'model' => $this->model,
            'location' => $this->location,
            'notes' => $this->notes,
            'readings' => MeterReadingResource::collection($this->whenLoaded('readings')),
            'latest_readings' => $this->when($request->has('include_latest'), function () {
                return MeterReadingResource::collection(
                    $this->readings()
                        ->latest('reading_date')
                        ->limit(6)
                        ->get()
                );
            }),
            'average_consumption' => $this->when($this->readings_count > 1, function () {
                $readings = $this->readings()
                    ->orderBy('reading_date')
                    ->limit(6)
                    ->get();

                if ($readings->count() < 2) {
                    return 0;
                }

                $totalConsumption = 0;
                for ($i = 1; $i < $readings->count(); $i++) {
                    $totalConsumption += $readings[$i]->reading_value - $readings[$i - 1]->reading_value;
                }

                return round($totalConsumption / ($readings->count() - 1), 2);
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}