<?php

namespace App\Services\Utility;

use App\Models\Contract;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\UtilityBill;
use App\Models\UtilityRate;
use App\Models\UtilityType;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UtilityBillingService
{
    /**
     * Calculate utility bills for a contract
     */
    public function calculateUtilityBills(Contract $contract, Carbon $billingPeriodStart, Carbon $billingPeriodEnd): Collection
    {
        $bills = collect();
        $property = $contract->room->property;
        $room = $contract->room;

        // Get all utility rates for the property
        $utilityRates = $property->utilityRates()->with('utilityType')->get();

        foreach ($utilityRates as $rate) {
            $bill = $this->calculateSingleUtilityBill($room, $rate, $billingPeriodStart, $billingPeriodEnd);
            if ($bill) {
                $bills->push($bill);
            }
        }

        return $bills;
    }

    /**
     * Calculate a single utility bill
     */
    protected function calculateSingleUtilityBill(
        $room,
        UtilityRate $rate,
        Carbon $billingPeriodStart,
        Carbon $billingPeriodEnd
    ): ?array {
        // Find the meter for this utility type
        $meter = $room->meters()
            ->where('utility_type_id', $rate->utility_type_id)
            ->where('status', 'active')
            ->first();

        if (!$meter) {
            return null;
        }

        // Get readings for the billing period
        $readings = $this->getReadingsForPeriod($meter, $billingPeriodStart, $billingPeriodEnd);

        // Calculate consumption
        $consumption = $this->calculateConsumption($meter, $readings);

        // Calculate amount
        $amount = $this->calculateAmount($consumption, $rate);

        return [
            'utility_type_id' => $rate->utility_type_id,
            'utility_type' => $rate->utilityType,
            'meter_id' => $meter->id,
            'start_reading' => $readings['start'],
            'end_reading' => $readings['end'],
            'consumption' => $consumption,
            'rate_applied' => $rate->rate,
            'amount' => $amount,
            'billing_period_start' => $billingPeriodStart,
            'billing_period_end' => $billingPeriodEnd,
        ];
    }

    /**
     * Get readings for a billing period
     */
    protected function getReadingsForPeriod(Meter $meter, Carbon $start, Carbon $end): array
    {
        // Get the latest reading before or at the start of the period
        $startReading = $meter->meterReadings()
            ->where('reading_date', '<=', $start)
            ->orderBy('reading_date', 'desc')
            ->first();

        // Get the latest reading before or at the end of the period
        $endReading = $meter->meterReadings()
            ->where('reading_date', '<=', $end)
            ->orderBy('reading_date', 'desc')
            ->first();

        // If no readings exist, use initial reading
        if (!$startReading && !$endReading) {
            return [
                'start' => $meter->initial_reading,
                'end' => $meter->initial_reading,
            ];
        }

        // If only one reading exists
        if (!$startReading) {
            return [
                'start' => $meter->initial_reading,
                'end' => $endReading->reading_value,
            ];
        }

        if (!$endReading) {
            return [
                'start' => $startReading->reading_value,
                'end' => $startReading->reading_value,
            ];
        }

        return [
            'start' => $startReading->reading_value,
            'end' => $endReading->reading_value,
        ];
    }

    /**
     * Calculate consumption between readings
     */
    protected function calculateConsumption(Meter $meter, array $readings): float
    {
        $consumption = $readings['end'] - $readings['start'];

        // Ensure consumption is not negative
        if ($consumption < 0) {
            Log::warning("Negative consumption detected for meter {$meter->id}: {$consumption}");
            return 0;
        }

        return $consumption;
    }

    /**
     * Calculate amount based on consumption and rate
     */
    protected function calculateAmount(float $consumption, UtilityRate $rate): float
    {
        // Simple calculation: consumption * rate
        // Could be extended for tiered pricing
        return round($consumption * $rate->rate, 2);
    }

    /**
     * Create utility bill records
     */
    public function createUtilityBills(Contract $contract, array $billsData): Collection
    {
        $bills = collect();

        DB::transaction(function () use ($contract, $billsData, &$bills) {
            foreach ($billsData as $billData) {
                $bill = UtilityBill::create([
                    'contract_id' => $contract->id,
                    'utility_type_id' => $billData['utility_type_id'],
                    'billing_period_start' => $billData['billing_period_start'],
                    'billing_period_end' => $billData['billing_period_end'],
                    'start_reading' => $billData['start_reading'],
                    'end_reading' => $billData['end_reading'],
                    'consumption' => $billData['consumption'],
                    'rate_applied' => $billData['rate_applied'],
                    'amount' => $billData['amount'],
                ]);

                $bills->push($bill);
            }
        });

        return $bills;
    }

    /**
     * Get meters that need reading
     */
    public function getMetersNeedingReading($landlordId, int $daysSinceLastReading = 30): Collection
    {
        return Meter::whereHas('room.property', function ($query) use ($landlordId) {
            $query->where('landlord_id', $landlordId);
        })
            ->where('status', 'active')
            ->where(function ($query) use ($daysSinceLastReading) {
                $query->whereNull('last_reading_date')
                    ->orWhere('last_reading_date', '<', now()->subDays($daysSinceLastReading));
            })
            ->with(['room', 'utilityType'])
            ->get();
    }

    /**
     * Record a meter reading
     */
    public function recordMeterReading(Meter $meter, float $readingValue, ?Carbon $readingDate = null): MeterReading
    {
        // Validate reading is not less than previous
        $lastReading = $meter->meterReadings()->latest('reading_date')->first();

        if ($lastReading && $readingValue < $lastReading->reading_value) {
            throw new \InvalidArgumentException("Reading value cannot be less than previous reading");
        }

        // If no last reading, check against initial reading
        if (!$lastReading && $readingValue < $meter->initial_reading) {
            throw new \InvalidArgumentException("Reading value cannot be less than initial reading");
        }

        return DB::transaction(function () use ($meter, $readingValue, $readingDate) {
            // Create the reading
            $reading = MeterReading::create([
                'meter_id' => $meter->id,
                'reading_value' => $readingValue,
                'reading_date' => $readingDate ?? now(),
            ]);

            // Update meter's last reading date
            $meter->update(['last_reading_date' => $reading->reading_date]);

            return $reading;
        });
    }

    /**
     * Get utility consumption summary for a period
     */
    public function getConsumptionSummary(Contract $contract, Carbon $startDate, Carbon $endDate): array
    {
        $summary = [];
        $room = $contract->room;

        $meters = $room->meters()->where('status', 'active')->with('utilityType')->get();

        foreach ($meters as $meter) {
            $readings = $this->getReadingsForPeriod($meter, $startDate, $endDate);
            $consumption = $this->calculateConsumption($meter, $readings);

            $summary[] = [
                'utility_type' => $meter->utilityType->name,
                'meter_number' => $meter->meter_number,
                'start_reading' => $readings['start'],
                'end_reading' => $readings['end'],
                'consumption' => $consumption,
                'unit' => $meter->utilityType->unit ?? 'units',
            ];
        }

        return $summary;
    }

    /**
     * Estimate next bill amount
     */
    public function estimateNextBill(Contract $contract): float
    {
        // Get average consumption from last 3 months
        $threeMonthsAgo = now()->subMonths(3);

        $averageAmount = UtilityBill::where('contract_id', $contract->id)
            ->where('billing_period_start', '>=', $threeMonthsAgo)
            ->avg('amount');

        return round($averageAmount ?? 0, 2);
    }

    /**
     * Get utility billing history
     */
    public function getBillingHistory(Contract $contract, int $months = 12): Collection
    {
        return UtilityBill::where('contract_id', $contract->id)
            ->where('billing_period_start', '>=', now()->subMonths($months))
            ->orderBy('billing_period_end', 'desc')
            ->with('utilityType')
            ->get();
    }

    /**
     * Check for abnormal consumption
     */
    public function checkAbnormalConsumption(Meter $meter, float $currentConsumption): ?array
    {
        // Get average consumption from last 3 readings
        $lastReadings = $meter->meterReadings()
            ->orderBy('reading_date', 'desc')
            ->take(4)
            ->get();

        if ($lastReadings->count() < 2) {
            return null;
        }

        $consumptions = [];
        for ($i = 0; $i < $lastReadings->count() - 1; $i++) {
            $consumptions[] = $lastReadings[$i]->reading_value - $lastReadings[$i + 1]->reading_value;
        }

        $averageConsumption = collect($consumptions)->avg();

        // Check if current consumption is 50% higher or lower than average
        $threshold = 0.5;
        $difference = abs($currentConsumption - $averageConsumption) / $averageConsumption;

        if ($difference > $threshold) {
            return [
                'is_abnormal' => true,
                'current_consumption' => $currentConsumption,
                'average_consumption' => round($averageConsumption, 2),
                'difference_percent' => round($difference * 100, 2),
            ];
        }

        return null;
    }
}