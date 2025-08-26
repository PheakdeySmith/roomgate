<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\RoomType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class RoomController extends Controller
{

    public function index(Request $request)
    {
        $currentUser = Auth::user();

        if ($currentUser->hasRole(roles: 'landlord')) {
            // Start the base query for rooms belonging to the current landlord
            $roomsQuery = Room::whereHas('property', function ($query) use ($currentUser) {
                $query->where('landlord_id', $currentUser->id);
            });

            // Apply filter for Property
            if ($request->filled('property_id')) {
                $roomsQuery->where('property_id', $request->property_id);
            }

            // Apply filter for Room Type
            if ($request->filled('room_type_id')) {
                $roomsQuery->where('room_type_id', $request->room_type_id);
            }
            
            // Apply filter for Status
            if ($request->filled('status') && $request->status !== 'all') {
                $roomsQuery->where('status', $request->status);
            }

            $rooms = $roomsQuery->with('property', 'roomType', 'amenities')->latest()->get();

            $properties = Property::where('landlord_id', $currentUser->id)->get();
            $roomTypes = RoomType::where('landlord_id', $currentUser->id)->get();
            $amenities = Amenity::where('landlord_id', $currentUser->id)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        } else {
            return redirect()->route('unauthorized');
        }

        return view('backends.dashboard.rooms.index', compact('rooms', 'properties', 'roomTypes', 'amenities'));
    }

    public function show(Room $room)
    {
        // 1. Authorization: Ensure the landlord owns this room.
        if ($room->property->landlord_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Eager-load all relationships for efficiency.
        $room->load([
            'property',
            'roomType',
            'amenities',
            'contracts' => function ($query) {
                // Load all contracts for this room, and for each contract, load the tenant.
                $query->with('tenant')->orderBy('start_date', 'desc');
            }
        ]);

        // 3. Find the currently active contract for this room, if one exists.
        $activeContract = $room->contracts->where('status', 'active')->first();

        // 4. Pass all the data to the view.
        return view('backends.dashboard.rooms.show', compact('room', 'activeContract'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->hasRole('landlord')) {
            return redirect()->route('unauthorized');
        }
        
        // Check subscription limits for rooms
        if ($currentUser->hasReachedRoomLimit()) {
            $subscription = $currentUser->activeSubscription();
            $limit = $subscription ? $subscription->subscriptionPlan->rooms_limit : 0;
            
            return back()->with('error', "You have reached the maximum number of rooms ($limit) allowed in your subscription plan. Please upgrade your plan to add more rooms.")
                  ->withInput();
        }

        $validatedData = $request->validate([
            'property_id' => [
                'required',
                Rule::exists('properties', 'id')->where(function ($query) use ($currentUser) {
                    $query->where('landlord_id', $currentUser->id);
                }),
            ],
            'room_type_id' => [
                'required',
                Rule::exists('room_types', 'id')->where(function ($query) use ($currentUser) {
                    $query->where('landlord_id', $currentUser->id);
                }),
            ],
            'room_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rooms')->where(function ($query) use ($request) {
                    return $query->where('property_id', $request->property_id);
                }),
            ],
            'description' => 'nullable|string',
            'size' => 'nullable|string|max:255',
            'floor' => 'nullable|integer',
            'status' => 'required|string|in:available,occupied,maintenance',
            'amenities'   => 'nullable|array',
            'amenities.*' => 'exists:amenities,id,landlord_id,' . $currentUser->id,
        ]);

        try {
            DB::beginTransaction();

            $room = Room::create($validatedData);

            if (!empty($validatedData['amenities'])) {
                $room->amenities()->attach($validatedData['amenities']);
            }

            DB::commit();

            return back()->with('success', 'Room created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Room creation failed: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred. Could not create the room.')->withInput();
        }
    }

    public function update(Request $request, Room $room)
    {
        $currentUser = Auth::user();

        if (!$currentUser->hasRole('landlord') || $room->property->landlord_id !== $currentUser->id) {
            return redirect()->route('unauthorized');
        }

        $validatedData = $request->validate([
            'property_id' => [
                'required',
                Rule::exists('properties', 'id')->where('landlord_id', $currentUser->id),
            ],
            'room_type_id' => [
                'required',
                Rule::exists('room_types', 'id')->where('landlord_id', $currentUser->id),
            ],
            'room_number' => [
                'required',
                'string',
                'max:255',

                Rule::unique('rooms')->ignore($room->id)->where(function ($query) use ($request) {
                    return $query->where('property_id', $request->property_id);
                }),
            ],
            'description' => 'nullable|string',
            'size' => 'nullable|string|max:255',
            'floor' => 'nullable|integer',
            'status' => 'required|string|in:available,occupied,maintenance',
            'amenities'   => 'nullable|array',
            'amenities.*' => 'exists:amenities,id,landlord_id,' . $currentUser->id,
        ]);

        try {
            DB::beginTransaction();

            $room->update($validatedData);

            if (!empty($validatedData['amenities'])) {
                $room->amenities()->sync($validatedData['amenities']);
            }

            DB::commit();

            return back()->with('success', 'Room updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Room update failed: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred. Could not update the room.');
        }
    }

    /**
     * Remove the specified room from storage.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Room $room)
    {
        $currentUser = Auth::user();

        if (!$currentUser->hasRole('landlord') || $room->property->landlord_id !== $currentUser->id) {
            return redirect()->route('unauthorized');
        }

        try {
            DB::beginTransaction();

            $room->delete();

            DB::commit();

            return back()->with('success', 'Room deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Room deletion failed: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred. Could not delete the room.');
        }
    }

    public function storeRoom(Request $request, Property $property)
    {
        $currentUser = Auth::user();

        if (!$currentUser || !$currentUser->hasRole('landlord') || $property->landlord_id !== $currentUser->id) {
            return redirect()->route('unauthorized');
        }
        
        // Check subscription limits for rooms
        if ($currentUser->hasReachedRoomLimit()) {
            $subscription = $currentUser->activeSubscription();
            $limit = $subscription ? $subscription->subscriptionPlan->rooms_limit : 0;
            
            return back()->with('error', "You have reached the maximum number of rooms ($limit) allowed in your subscription plan. Please upgrade your plan to add more rooms.")
                  ->withInput();
        }

        $validatedData = $request->validate([
            'room_type_id' => [
                'required',
                Rule::exists('room_types', 'id')->where(function ($query) use ($currentUser) {
                    $query->where('landlord_id', $currentUser->id);
                }),
            ],
            'room_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rooms')->where(function ($query) use ($property) {
                    return $query->where('property_id', $property->id);
                }),
            ],
            'description' => 'nullable|string',
            'size' => 'nullable|string|max:255',
            'floor' => 'nullable|integer',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id,landlord_id,' . $currentUser->id,
        ]);

        try {
            DB::beginTransaction();

            $createData = $validatedData;
            $createData['property_id'] = $property->id;

            $room = Room::create($createData);

            if (!empty($validatedData['amenities'])) {
                $room->amenities()->attach($validatedData['amenities']);
            }

            DB::commit();

            return back()->with('success', 'Room created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Room creation failed: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred. Could not create the room.')->withInput();
        }
    }
}
