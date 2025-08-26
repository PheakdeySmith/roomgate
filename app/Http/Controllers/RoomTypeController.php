<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RoomTypeController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        // This query for all of the landlord's amenities is correct.
        $amenities = Amenity::where('landlord_id', $currentUser->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        if ($currentUser->hasRole('landlord')) {
            $roomTypes = RoomType::with('amenities')
                ->where('landlord_id', $currentUser->id)
                ->latest()
                ->get();
        } else {
            return redirect()->route('unauthorized');
        }

        // Pass both collections to the view.
        return view('backends.dashboard.room_types.index', compact('roomTypes', 'amenities'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->hasRole('landlord')) {
            return redirect()->route('unauthorized');
        }
        
        // Check if subscription payment is pending
        $subscription = $currentUser->activeSubscription();
        if ($subscription && $subscription->payment_status !== 'paid' && !$subscription->isInTrial()) {
            return redirect()->route('landlord.subscription.plans')
                ->with('error', 'Your subscription payment is pending. Please complete payment to access all features.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity'=> 'required|integer|min:1',
            'amenities'   => 'nullable|array',
            'amenities.*' => 'exists:amenities,id,landlord_id,' . $currentUser->id,
        ]);

        try {
            DB::beginTransaction();

            $roomType = RoomType::create([
                'landlord_id' => $currentUser->id,
                'name'        => $validatedData['name'],
                'capacity'    => $validatedData['capacity'],
                'description' => $validatedData['description'],
            ]);

            if (!empty($validatedData['amenities'])) {
                $roomType->amenities()->attach($validatedData['amenities']);
            }

            DB::commit();

            return back()->with('success', 'Room Type created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging.
            Log::error('Room Type creation failed: ' . $e->getMessage());

            // Redirect back with a user-friendly error message.
            return back()->with('error', 'An unexpected error occurred. Could not create the room type.')->withInput();
        }
    }

    public function update(Request $request, RoomType $roomType)
    {
        $currentUser = Auth::user();

        if (!$currentUser->hasRole('landlord') || $roomType->landlord_id !== $currentUser->id) {
            return redirect()->route('unauthorized');
        }
        
        // Check if subscription payment is pending
        $subscription = $currentUser->activeSubscription();
        if ($subscription && $subscription->payment_status !== 'paid' && !$subscription->isInTrial()) {
            return redirect()->route('landlord.subscription.plans')
                ->with('error', 'Your subscription payment is pending. Please complete payment to access all features.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity'=> 'required|integer|min:1',
            'status' => 'required|string|in:active,inactive',
            'amenities'   => 'nullable|array',
            'amenities.*' => 'exists:amenities,id,landlord_id,' . $currentUser->id,
        ]);

        try {
            DB::beginTransaction();

            $roomType->update($validatedData);

            $roomType->amenities()->sync($request->input('amenities', []));

            DB::commit();

            return back()->with('success', 'Room Type updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Room Type update failed: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred during room type update.')->withInput();
        }
    }

    public function destroy(Request $request, RoomType $roomType)
    {
        $currentUser = Auth::user();

        $canDelete = false;

        if ($currentUser->hasRole('landlord')) {
            if ($roomType->landlord_id === $currentUser->id) {
                $canDelete = true;
            }
        }

        if (!$canDelete) {
            return redirect()->route('unauthorized');
        }
        
        // Check if subscription payment is pending
        $subscription = $currentUser->activeSubscription();
        if ($subscription && $subscription->payment_status !== 'paid' && !$subscription->isInTrial()) {
            return redirect()->route('landlord.subscription.plans')
                ->with('error', 'Your subscription payment is pending. Please complete payment to access all features.');
        }

        $roomType->delete();

        return back()->with('success', value: 'Room Type deleted successfully.');
    }
    
    public function show(Request $request, RoomType $roomType)
    {
        $currentUser = Auth::user();
        
        // Check if user has permission to view this room type
        if ($currentUser->hasRole('landlord') && $roomType->landlord_id !== $currentUser->id) {
            return redirect()->route('unauthorized');
        }
        
        // Load the amenities relationship if it hasn't been already
        if (!$roomType->relationLoaded('amenities')) {
            $roomType->load('amenities');
        }
        
        // Get all amenities for the edit modal
        $amenities = Amenity::where('landlord_id', $currentUser->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        // Count rooms using this type
        $roomCount = $roomType->rooms()->count();
        
        return view('backends.dashboard.room_types.show', compact('roomType', 'roomCount', 'amenities'));
    }
    
}
