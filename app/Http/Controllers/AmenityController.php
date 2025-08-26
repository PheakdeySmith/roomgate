<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AmenityController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $amenitiesQuery = Amenity::query();

        if ($currentUser->hasRole('landlord')) {
            $amenities = $amenitiesQuery
                ->where('landlord_id', $currentUser->id)
                ->latest()
                ->get();
        } else {
            return redirect()->route('unauthorized');
        }

        return view('backends.dashboard.amenities.index', compact('amenities'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->hasRole('landlord')) {
            return redirect()->route('unauthorized');
        }
        // dd($request->all());

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amenity_price'=> 'required|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
        ]);

        

        try {
            DB::beginTransaction();

            Amenity::create([
                'landlord_id' => $currentUser->id,
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'amenity_price' => $validatedData['amenity_price'],
                'status' => $validatedData['status'],
            ]);

            DB::commit();

            return back()->with('success', 'Amenity created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging.
            Log::error('Amenity creation failed: ' . $e->getMessage());

            // Redirect back with a user-friendly error message.
            return back()->with('error', 'An unexpected error occurred. Could not create the amenity.')->withInput();
        }
    }

    public function update(Request $request, Amenity $amenity)
    {
        $currentUser = Auth::user();

        if (!$currentUser->hasRole('landlord') || $amenity->landlord_id !== $currentUser->id) {
            return redirect()->route('unauthorized');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amenity_price' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
        ]);

        try {
            DB::beginTransaction();

            $amenity->update($validatedData);

            DB::commit();

            return back()->with('success', 'Amenity updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Amenity update failed: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred during amenity update.')->withInput();
        }
    }

    public function destroy(Request $request, Amenity $amenity)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->hasRole('landlord')) {
            return redirect()->route('unauthorized');
        }

        if ($amenity->rooms()->exists()) {
            return back()->with('error', 'This amenity currently assigned.');
        }

        if ($amenity->roomTypes()->exists()) {
            return back()->with('error', 'This amenity currently assigned.');
        }

        try {
            DB::beginTransaction();
            $amenity->delete();
            DB::commit();
            return back()->with('success', 'Amenity deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Amenity deletion failed: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred during amenity deletion.')->withInput();
        }
    }
}
