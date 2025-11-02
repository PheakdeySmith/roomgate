<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class MeterReadingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        // Get previous reading for consumption calculation
        $previousReading = $this->meter->readings()
            ->where('reading_date', '<', $this->reading_date)
            ->orderBy('reading_date', 'desc')
            ->first();

        $consumption = $previousReading
            ? $this->reading_value - $previousReading->reading_value
            : 0;

        $daysSinceLast = $previousReading
            ? Carbon::parse($previousReading->reading_date)->diffInDays(Carbon::parse($this->reading_date))
            : 0;

        return [
            'id' => $this->id,
            'meter_id' => $this->meter_id,
            'meter' => new MeterResource($this->whenLoaded('meter')),
            'reading_value' => $this->reading_value,
            'reading_date' => $this->reading_date,
            'reading_month' => Carbon::parse($this->reading_date)->format('Y-m'),
            'consumption' => $consumption,
            'days_since_last' => $daysSinceLast,
            'daily_average' => $daysSinceLast > 0 ? round($consumption / $daysSinceLast, 2) : 0,
            'photo_path' => $this->photo_path ? asset('storage/' . $this->photo_path) : null,
            'notes' => $this->notes,
            'is_estimated' => $this->is_estimated ?? false,
            'read_by' => new UserResource($this->whenLoaded('readBy')),
            'verified_by' => new UserResource($this->whenLoaded('verifiedBy')),
            'verified_at' => $this->verified_at,
            'utility_bill' => $this->whenLoaded('utilityBill', function () {
                return [
                    'id' => $this->utilityBill->id,
                    'amount' => $this->utilityBill->amount,
                    'rate' => $this->utilityBill->rate,
                    'status' => $this->utilityBill->status,
                    'invoice_id' => $this->utilityBill->invoice_id,
                ];
            }),
            'previous_reading' => $previousReading ? [
                'value' => $previousReading->reading_value,
                'date' => $previousReading->reading_date,
            ] : null,
            'is_abnormal' => $this->checkIfAbnormal($consumption, $previousReading),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }

    /**
     * Check if reading consumption is abnormal
     *
     * @param float $consumption
     * @param $previousReading
     * @return bool
     */
    private function checkIfAbnormal(float $consumption, $previousReading): bool
    {
        if (!$previousReading) {
            return false;
        }

        // Get average consumption from last 3 readings
        $avgConsumption = $this->meter->readings()
            ->where('reading_date', '<', $this->reading_date)
            ->orderBy('reading_date', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($reading, $index) use ($previousReading) {
                if ($index === 0) {
                    return null; // Skip first as it's the previous reading
                }

                $prevReading = $this->meter->readings()
                    ->where('reading_date', '<', $reading->reading_date)
                    ->orderBy('reading_date', 'desc')
                    ->first();

                return $prevReading ? $reading->reading_value - $prevReading->reading_value : null;
            })
            ->filter()
            ->avg();

        if (!$avgConsumption) {
            return false;
        }

        // Flag as abnormal if consumption is 50% higher or lower than average
        return $consumption > ($avgConsumption * 1.5) || $consumption < ($avgConsumption * 0.5);
    }
}