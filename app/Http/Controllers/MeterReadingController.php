<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MeterReadingController extends Controller
{
    /**
     * Store a newly created meter reading in storage.
     */
    public function store(Request $request)
    {
        // 1. Basic validation
        $request->validate([
            'meter_id' => 'required|exists:meters,id',
            'reading_value' => 'required|numeric|min:0',
        ]);

        // 2. Advanced Validation: Ensure the new reading is not less than the last one.
        $meter = Meter::findOrFail($request->meter_id);
        $lastReading = $meter->meterReadings()->latest('reading_date')->first();

        // Determine the last known value
        $lastValue = $lastReading ? $lastReading->reading_value : $meter->initial_reading;

        if ($request->reading_value < $lastValue) {
            // If the new value is smaller, throw a validation error.
            throw ValidationException::withMessages([
                'reading_value' => 'The new reading value cannot be less than the last recorded value of ' . $lastValue,
            ]);
        }

        // 3. Create the new meter reading
        $newReading = MeterReading::create([
            'meter_id' => $meter->id,
            'reading_value' => $request->reading_value,
            'reading_date' => now(), // Use the current date for the reading
            'recorded_by_id' => Auth::id(),
        ]);

        $meter->last_reading_date = $newReading->reading_date;
        $meter->save();

        $newReading->load(['recordedBy', 'meter.utilityType']);

        // 5. Return the complete JSON response
        return response()->json([
            'success' => true,
            'message' => 'Reading saved successfully!',
            'reading' => $newReading
        ]);
    }
}
