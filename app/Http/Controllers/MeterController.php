<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class MeterController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate the incoming data
        $validatedData = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_id' => 'required|exists:rooms,id',
            'utility_type_id' => 'required|exists:utility_types,id',
            'meter_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('meters'), // Ensure the meter number is unique
            ],
            'initial_reading' => 'required|numeric|min:0',
            'installed_at' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        Meter::where('room_id', $validatedData['room_id'])
            ->where('utility_type_id', $validatedData['utility_type_id'])
            ->where('status', 'active')
            ->update(['status' => 'inactive']);

        // 3. Create and save the new meter.
        // It will be the only 'active' one for this utility type in this room.
        Meter::create($validatedData);

        // 3. Redirect back with a success message
        return back()->with('success', 'New meter assigned successfully!');
    }

    public function update(Request $request, Meter $meter)
{
    $validatedData = $request->validate([
        'utility_type_id' => 'required|exists:utility_types,id',
        'meter_number'    => 'required|string|max:255',
        'initial_reading' => 'required|numeric|min:0',
        'installed_at'    => 'required|date',
    ]);

    $meter->update($validatedData);

    return back()->with('success', 'Meter updated successfully.');
}

    public function deactivate(Meter $meter)
    {
        // 1. Correct Authorization Check:
        // Ensure the logged-in user owns the property this meter belongs to.
        if (Auth::id() !== $meter->property->landlord_id) {
            abort(403, 'Unauthorized Action');
        }

        $meter->status = 'inactive';
        $meter->save();

        // 3. Redirect back with a success message
        return back()->with('success', "Meter #{$meter->meter_number} has been deactivated.");
    }

    public function toggleStatus(Meter $meter)
    {
        // 1. Authorization
        if (Auth::id() !== $meter->property->landlord_id) {
            abort(403, 'Unauthorized Action');
        }

        // 2. Determine the new status
        $newStatus = ($meter->status === 'active') ? 'inactive' : 'active';

        // 3. IMPORTANT: If we are activating a meter, we must first
        //    deactivate any other active meter of the same type in the same room.
        if ($newStatus === 'active') {
            Meter::where('room_id', $meter->room_id)
                ->where('utility_type_id', $meter->utility_type_id)
                ->where('id', '!=', $meter->id) // Exclude the current meter
                ->update(['status' => 'inactive']);
        }

        // 4. Update the status of the toggled meter
        $meter->status = $newStatus;
        $meter->save();

        return back()->with('success', "Meter status updated successfully.");
    }

    public function getMeterHistory(Meter $meter)
    {
        // 1. Get the paginated results for display
        $paginatedReadings = $meter->meterReadings()
            ->orderByDesc('reading_date')
            ->orderByDesc('id')
            ->paginate(4);

        // 2. Get all readings to perform the consumption calculation reliably
        $allReadings = $meter->meterReadings->sortByDesc('reading_date')->sortByDesc('id')->values();

        // 3. Return the rendered partial view as a response
        return view('backends.dashboard.properties.partials.utility_items._meter-history', [
            'paginatedReadings' => $paginatedReadings,
            'allReadings'       => $allReadings,
            'meter'             => $meter,
        ])->render();
    }
}
