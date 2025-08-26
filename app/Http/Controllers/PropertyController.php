<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\RoomType;
use App\Models\BasePrice;
use App\Models\UtilityType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $propertiesQuery = Property::query();

        if ($currentUser->hasRole('landlord')) {
            $properties = $propertiesQuery
                ->with('roomTypes')
                ->where('landlord_id', $currentUser->id)
                ->latest()
                ->get();
        } else {
            return redirect()->route('unauthorized');
        }

        $utilityTypes = UtilityType::latest()->get();

        return view('backends.dashboard.properties.index', compact('properties', 'utilityTypes'));
    }

    public function show(Property $property)
    {
        $currentUser = Auth::user();
        if ($currentUser->id !== $property->landlord_id) {
            abort(403, 'Unauthorized Action');
        }

        $property->load([
        'rooms' => function ($query) {
            $query->with([
                'activeContract.tenant',
                'activeMeters.utilityType',
                'activeMeters.meterReadings' => function ($subQuery) {
                    $subQuery->latest('reading_date')->limit(12);
                },
                'allMeters.utilityType'
            ])->orderBy('room_number', 'asc');
        },
    ]);
    $utilityTypes = UtilityType::all();

        $rooms = Room::where('property_id', $property->id)
            ->with('roomType', 'amenities')
            ->latest()
            ->paginate(10);

        $basePrices = BasePrice::where('property_id', $property->id)
            ->get()
            ->keyBy('room_type_id');

        $amenities = Amenity::where('landlord_id', $currentUser->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $tenants = User::role('tenant')->where('landlord_id', $currentUser->id)->get();

        $allRooms = Room::whereHas('property', function ($query) use ($currentUser) {
            $query->where('landlord_id', $currentUser->id);
        })
            ->with('property')
            ->get();

        return view('backends.dashboard.properties.show', [
            'property' => $property,
            'utilityTypes' => $utilityTypes,
            'rooms' => $rooms,
            'basePrices' => $basePrices,
            'roomTypes' => $property->roomTypes,
            'amenities' => $amenities,
            'tenants' => $tenants,
            'allRooms' => $allRooms,
        ]);
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->hasRole('landlord')) {
            return redirect()->route('unauthorized');
        }
        
        // Check subscription limits for properties
        if ($currentUser->hasReachedPropertyLimit()) {
            $subscription = $currentUser->activeSubscription();
            $limit = $subscription ? $subscription->subscriptionPlan->properties_limit : 0;
            
            return back()->with('error', "You have reached the maximum number of properties ($limit) allowed in your subscription plan. Please upgrade your plan to add more properties.")
                  ->withInput();
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'property_type' => 'required|string|in:apartment,house,condo,townhouse,commercial',
            'description' => 'nullable|string',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state_province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|string|in:active,inactive',
        ]);

        try {
            DB::beginTransaction();

            $imageDbPath = null;

            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $filename = time() . '_' . Str::slug($originalName) . '.' . $extension;
                $destinationPath = public_path('uploads/property-photos');

                File::makeDirectory($destinationPath, 0755, true, true);

                $file->move($destinationPath, $filename);

                $imageDbPath = 'uploads/property-photos/' . $filename;
            }

            Property::create([
                'landlord_id' => $currentUser->id,
                'name' => $validatedData['name'],
                'property_type' => $validatedData['property_type'],
                'description' => $validatedData['description'],
                'address_line_1' => $validatedData['address_line_1'],
                'address_line_2' => $validatedData['address_line_2'] ?? null,
                'city' => $validatedData['city'],
                'state_province' => $validatedData['state_province'],
                'postal_code' => $validatedData['postal_code'],
                'country' => $validatedData['country'],
                'year_built' => $validatedData['year_built'] ?? null,
                'status' => $validatedData['status'],
                'cover_image' => $imageDbPath,
            ]);

            DB::commit();

            return back()->with('success', 'Property created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Property creation failed: ' . $e->getMessage());

            return back()->with('error', 'An unexpected error occurred. Could not create the property.')->withInput();
        }
    }

    public function createPrice(Property $property)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->hasRole('landlord') || $property->landlord_id !== $currentUser->id) {
            return redirect()->route('unauthorized');
        }

        $property->load('roomTypes');

        $allRoomTypes = RoomType::where('landlord_id', $currentUser->id)
            ->orderBy('name')
            ->get();

        return view('backends.dashboard.properties.create-price', compact('property', 'allRoomTypes'));
    }


    public function storePrice(Request $request, Property $property)
    {

        $validatedData = $request->validate([
            'price' => 'required|numeric|min:0',

            'effective_date' => 'required|date',

            'room_type_id' => [
                'required',
                'exists:room_types,id',
                Rule::unique('base_prices')->where(function ($query) use ($property, $request) {
                    return $query->where('property_id', $property->id)
                        ->where('effective_date', Carbon::parse($request->effective_date)->toDateString());
                }),
            ],
        ], [
            'room_type_id.unique' => 'A price for this room type on this effective date has already been set.',

            'effective_date.date' => 'The effective date must be a valid date (e.g., 2025-06-13).',
        ]);

        try {
            $property->roomTypes()->attach($validatedData['room_type_id'], [
                'price' => $validatedData['price'],
                'effective_date' => $validatedData['effective_date'],
            ]);

            return back()->with('success', 'Room type price set successfully for this property.');

        } catch (\Exception $e) {
            Log::error('Failed to store price: ' . $e->getMessage());
            return back()->with('error', 'Could not store the price. Please try again.')->withInput();
        }
    }

    public function updatePrice(Request $request, Property $property)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->hasRole('landlord') || $property->landlord_id !== $currentUser->id) {
            return redirect()->route('unauthorized');
        }

        $validatedData = $request->validate([
            'price' => 'required|numeric|min:0',
            'effective_date' => 'required|date_format:Y-m-d',
            'room_type_id' => 'required|exists:room_types,id',
            'original_effective_date' => 'required|date_format:Y-m-d',
        ]);

        $property->roomTypes()
            ->where('room_type_id', $validatedData['room_type_id'])
            ->wherePivot('effective_date', $validatedData['original_effective_date'])
            ->updateExistingPivot($validatedData['room_type_id'], [
                'price' => $validatedData['price'],
                'effective_date' => Carbon::parse($validatedData['effective_date']),
            ]);

        return back()->with('success', 'Price updated successfully.');
    }

    public function destroyPrice(Request $request, Property $property)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->hasRole('landlord') || $property->landlord_id !== $currentUser->id) {
            return redirect()->route('unauthorized');
        }

        $data = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'effective_date' => 'required|date_format:Y-m-d',
        ]);

        $property->roomTypes()
            ->wherePivot('effective_date', $data['effective_date'])
            ->detach($data['room_type_id']);

        return back()->with('success', 'Price assignment deleted successfully.');
    }

    public function update(Request $request, Property $property)
    {
        $currentUser = Auth::user();

        if (!$currentUser->hasRole('landlord') || $property->landlord_id !== $currentUser->id) {
            return redirect()->route('unauthorized');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'property_type' => 'required|string|in:apartment,house,condo,townhouse,commercial',
            'description' => 'nullable|string',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state_province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|string|in:active,inactive',
        ]);

        try {
            DB::beginTransaction();

            // Handle image update
            if ($request->hasFile('cover_image')) {
                // Delete old image
                if ($property->cover_image && File::exists(public_path($property->cover_image))) {
                    File::delete(public_path($property->cover_image));
                }

                $file = $request->file('cover_image');
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . Str::slug($originalName) . '.' . $extension;
                $destinationPath = public_path('uploads/property-photos');

                File::makeDirectory($destinationPath, 0755, true, true);
                $file->move($destinationPath, $filename);
                $validatedData['cover_image'] = 'uploads/property-photos/' . $filename;
            }

            $property->update($validatedData);

            DB::commit();

            return back()->with('success', 'Property updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Property update failed: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred during property update.')->withInput();
        }
    }

    public function destroy(Request $request, Property $property)
    {
        $currentUser = Auth::user();

        $canDelete = false;

        if ($currentUser->hasRole('landlord')) {
            if ($property->landlord_id === $currentUser->id) {
                $canDelete = true;
            }
        }

        if (!$canDelete) {
            return redirect()->route('unauthorized');
        }

        if ($property->cover_image && File::exists(public_path($property->cover_image))) {
            File::delete(public_path($property->cover_image));
        }

        $property->delete();

        return back()->with('success', value: 'Property deleted successfully.');
    }
}
