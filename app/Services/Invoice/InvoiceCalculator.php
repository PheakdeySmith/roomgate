<?php

namespace App\Services\Invoice;

use App\Models\Contract;
use App\Models\BasePrice;
use App\Models\UtilityRate;
use App\Models\UtilityBill;
use Illuminate\Support\Collection;

class InvoiceCalculator
{
    /**
     * Calculate rent amount for a contract
     */
    public function calculateRentAmount(Contract $contract): float
    {
        // If contract has a specific rent amount, use it
        if ($contract->rent_amount !== null) {
            return $contract->rent_amount;
        }

        // Otherwise, get the base price for the room type and property
        $basePrice = BasePrice::where('property_id', $contract->room->property_id)
            ->where('room_type_id', $contract->room->room_type_id)
            ->orderBy('effective_date', 'desc')
            ->first();

        return $basePrice ? $basePrice->price : 0;
    }

    /**
     * Calculate utility data for a contract
     */
    public function calculateUtilityData(Contract $contract): array
    {
        $utilityData = [];
        $propertyRates = $contract->room->property->utilityRates;

        foreach ($propertyRates as $rate) {
            $data = $this->calculateSingleUtility($contract, $rate);
            if ($data) {
                $utilityData[] = $data;
            }
        }

        return $utilityData;
    }

    /**
     * Calculate a single utility for a contract
     */
    protected function calculateSingleUtility(Contract $contract, UtilityRate $rate): ?array
    {
        $meter = $contract->room->meters->firstWhere('utility_type_id', $rate->utility_type_id);

        if (!$meter) {
            return null;
        }

        $readings = $meter->meterReadings()
            ->latest('reading_date')
            ->take(2)
            ->get();

        $consumption = 0;
        $startReading = null;
        $endReading = null;

        if ($readings->count() >= 2) {
            // Two readings available
            $endReading = $readings[0]->reading_value;
            $startReading = $readings[1]->reading_value;
            $consumption = max(0, $endReading - $startReading);
        } elseif ($readings->count() === 1) {
            // One reading available
            $endReading = $readings[0]->reading_value;
            $startReading = $meter->initial_reading;
            $consumption = max(0, $endReading - $startReading);
        } else {
            // No readings available
            $startReading = $meter->initial_reading;
            $endReading = $meter->initial_reading;
            $consumption = 0;
        }

        return [
            'utility_type' => $rate->utilityType,
            'utility_type_id' => $rate->utility_type_id,
            'rate' => $rate->rate,
            'consumption' => $consumption,
            'start_reading' => $startReading,
            'end_reading' => $endReading,
            'amount' => $consumption * $rate->rate,
        ];
    }

    /**
     * Calculate amenity total for a contract
     */
    public function calculateAmenityTotal(Contract $contract): float
    {
        return $contract->room->amenities->sum('price');
    }

    /**
     * Calculate total invoice amount
     */
    public function calculateInvoiceTotal(
        float $rent,
        float $amenities,
        array $utilities,
        float $discountPercent = 0
    ): float {
        $subtotal = $rent + $amenities;

        // Add utility amounts
        foreach ($utilities as $utility) {
            $subtotal += $utility['amount'] ?? 0;
        }

        // Apply discount
        if ($discountPercent > 0) {
            $discount = $subtotal * ($discountPercent / 100);
            $subtotal -= $discount;
        }

        return max(0, $subtotal);
    }

    /**
     * Calculate invoice summary
     */
    public function calculateInvoiceSummary(Contract $contract, float $discountPercent = 0): array
    {
        $rent = $this->calculateRentAmount($contract);
        $amenities = $this->calculateAmenityTotal($contract);
        $utilities = $this->calculateUtilityData($contract);

        $utilityTotal = collect($utilities)->sum('amount');
        $subtotal = $rent + $amenities + $utilityTotal;
        $discount = $subtotal * ($discountPercent / 100);
        $total = $subtotal - $discount;

        return [
            'rent' => $rent,
            'amenities' => $amenities,
            'utilities' => $utilityTotal,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'discount_percent' => $discountPercent,
            'total' => max(0, $total),
            'utility_details' => $utilities,
        ];
    }

    /**
     * Calculate payment distribution for partial payments
     */
    public function calculatePaymentDistribution(float $totalPaid, array $lineItems): array
    {
        $distribution = [];
        $totalAmount = collect($lineItems)->sum('amount');

        if ($totalAmount <= 0) {
            return $distribution;
        }

        $paymentRatio = min(1, $totalPaid / $totalAmount);

        foreach ($lineItems as $item) {
            $distribution[] = [
                'line_item_id' => $item['id'],
                'amount' => $item['amount'],
                'paid' => round($item['amount'] * $paymentRatio, 2),
                'balance' => round($item['amount'] * (1 - $paymentRatio), 2),
            ];
        }

        return $distribution;
    }

    /**
     * Calculate late fee
     */
    public function calculateLateFee(float $invoiceAmount, int $daysLate, float $lateFeePercent = 5): float
    {
        if ($daysLate <= 0) {
            return 0;
        }

        // Simple flat percentage late fee
        return round($invoiceAmount * ($lateFeePercent / 100), 2);
    }

    /**
     * Calculate prorated amount for partial month
     */
    public function calculateProratedAmount(float $monthlyAmount, int $daysInMonth, int $daysOccupied): float
    {
        if ($daysInMonth <= 0 || $daysOccupied <= 0) {
            return 0;
        }

        return round(($monthlyAmount / $daysInMonth) * $daysOccupied, 2);
    }
}