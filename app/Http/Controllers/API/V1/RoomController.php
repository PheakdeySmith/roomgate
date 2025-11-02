<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Room;
use App\Models\Property;
use App\Models\Amenity;
use App\Models\Meter;
use App\Services\Room\RoomService;
use App\Services\Property\PropertyService;
use App\Services\Contract\ContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoomController extends BaseController
{
    protected RoomService $roomService;
    protected PropertyService $propertyService;
    protected ContractService $contractService;

    public function __construct(
        RoomService $roomService,
        PropertyService $propertyService,
        ContractService $contractService
    ) {
        $this->roomService = $roomService;
        $this->propertyService = $propertyService;
        $this->contractService = $contractService;
    }

    /**
     * Display a listing of rooms
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Only landlords can view rooms', [], 403);
            }

            $filters = $request->only(['property_id', 'status', 'floor', 'room_type_id']);
            $rooms = $this->roomService->getLandlordRooms($user, $filters);

            $data = $rooms->map(function ($room) {
                return $this->transformRoom($room);
            });

            if ($request->has('page')) {
                return $this->sendPaginatedResponse($rooms, 'Rooms retrieved successfully');
            }

            return $this->sendResponse($data, 'Rooms retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve rooms', [$e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created room
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'room_number' => 'required|string|max:50',
            'floor' => 'nullable|integer',
            'size' => 'nullable|numeric',
            'room_type_id' => 'nullable|exists:room_types,id',
            'monthly_rent' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();
            $property = Property::findOrFail($request->property_id);

            // Check authorization
            if ($property->landlord_id !== $user->id) {
                return $this->sendError('Unauthorized to add room to this property', [], 403);
            }

            // Check room limits
            if ($user->hasReachedRoomLimit()) {
                $subscription = $user->activeSubscription();
                $limit = $subscription ? $subscription->subscriptionPlan->rooms_limit : 0;
                return $this->sendError("Room limit reached ($limit). Please upgrade your subscription.", [], 403);
            }

            // Check duplicate room number
            $exists = Room::where('property_id', $property->id)
                ->where('room_number', $request->room_number)
                ->exists();

            if ($exists) {
                return $this->sendError('Room number already exists in this property', [], 400);
            }

            // Create room using service
            $room = $this->roomService->createRoom($request->all(), $property);

            // Attach amenities if provided
            if ($request->has('amenities')) {
                $room->amenities()->sync($request->amenities);
            }

            $data = $this->transformRoom($room->load('amenities'));

            return $this->sendResponse($data, 'Room created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('Failed to create room', [$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified room
     */
    public function show($id)
    {
        try {
            $room = Room::with([
                'property',
                'roomType',
                'amenities',
                'activeContract.tenant',
                'meters.meterReadings' => function ($q) {
                    $q->latest()->limit(6);
                }
            ])->findOrFail($id);

            // Check authorization
            if ($room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $data = $this->transformRoom($room, true);
            $data['availability_calendar'] = $this->roomService->getAvailabilityCalendar($room);
            $data['statistics'] = $this->roomService->getRoomStatistics($room);

            return $this->sendResponse($data, 'Room details retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Room not found', [$e->getMessage()], 404);
        }
    }

    /**
     * Update the specified room
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'room_number' => 'sometimes|required|string|max:50',
            'floor' => 'nullable|integer',
            'size' => 'nullable|numeric',
            'room_type_id' => 'nullable|exists:room_types,id',
            'monthly_rent' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $room = Room::findOrFail($id);

            // Check authorization
            if ($room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if status change is valid
            if ($request->has('status') && $request->status !== $room->status) {
                if ($request->status === 'occupied' && $room->status === 'available') {
                    return $this->sendError('Cannot manually set room to occupied. Create a contract instead.', [], 400);
                }

                if ($request->status === 'available' && $room->status === 'occupied') {
                    if ($room->activeContract) {
                        return $this->sendError('Cannot set occupied room to available. Terminate the contract first.', [], 400);
                    }
                }
            }

            // Update room using service
            $room = $this->roomService->updateRoom($room, $request->all());

            $data = $this->transformRoom($room->fresh('amenities'));

            return $this->sendResponse($data, 'Room updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update room', [$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified room
     */
    public function destroy($id)
    {
        try {
            $room = Room::findOrFail($id);

            // Check authorization
            if ($room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if room has active contract
            if ($room->activeContract) {
                return $this->sendError('Cannot delete room with active contract', [], 400);
            }

            // Check if room has any contracts
            if ($room->contracts()->exists()) {
                return $this->sendError('Cannot delete room with contract history', [], 400);
            }

            $room->delete();

            return $this->sendResponse(null, 'Room deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete room', [$e->getMessage()], 500);
        }
    }

    /**
     * Check room availability
     */
    public function checkAvailability(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $room = Room::findOrFail($id);

            // Check authorization
            if ($room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $isAvailable = $this->roomService->checkRoomAvailability(
                $room,
                $request->start_date,
                $request->end_date
            );

            $data = [
                'available' => $isAvailable,
                'room_status' => $room->status,
            ];

            if (!$isAvailable && $room->activeContract) {
                $data['current_contract'] = [
                    'tenant' => $room->activeContract->tenant->name,
                    'end_date' => $room->activeContract->end_date,
                ];
            }

            return $this->sendResponse($data, 'Availability checked successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to check availability', [$e->getMessage()], 500);
        }
    }

    /**
     * Get room availability calendar
     */
    public function getAvailabilityCalendar($id)
    {
        try {
            $room = Room::findOrFail($id);

            // Check authorization
            if ($room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $calendar = $this->roomService->getAvailabilityCalendar($room);

            return $this->sendResponse($calendar, 'Availability calendar retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve calendar', [$e->getMessage()], 500);
        }
    }

    /**
     * Attach amenities to room
     */
    public function attachAmenities(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amenities' => 'required|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $room = Room::findOrFail($id);

            // Check authorization
            if ($room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $room->amenities()->sync($request->amenities);

            // Recalculate total rent
            $totalRent = $this->roomService->calculateTotalRent($room);

            $data = [
                'amenities' => $room->amenities->map(function ($amenity) {
                    return [
                        'id' => $amenity->id,
                        'name' => $amenity->name,
                        'price' => $amenity->amenity_price,
                    ];
                }),
                'total_rent' => $totalRent,
            ];

            return $this->sendResponse($data, 'Amenities updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update amenities', [$e->getMessage()], 500);
        }
    }

    /**
     * Detach amenity from room
     */
    public function detachAmenity($roomId, $amenityId)
    {
        try {
            $room = Room::findOrFail($roomId);

            // Check authorization
            if ($room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $room->amenities()->detach($amenityId);

            // Recalculate total rent
            $totalRent = $this->roomService->calculateTotalRent($room);

            return $this->sendResponse(['total_rent' => $totalRent], 'Amenity removed successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to remove amenity', [$e->getMessage()], 500);
        }
    }

    /**
     * Add meter to room
     */
    public function addMeter(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'meter_number' => 'required|string|unique:meters,meter_number',
            'meter_type' => 'required|in:electricity,water,gas',
            'initial_reading' => 'required|numeric|min:0',
            'rate_per_unit' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $room = Room::findOrFail($id);

            // Check authorization
            if ($room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if meter of this type already exists
            $existingMeter = Meter::where('room_id', $room->id)
                ->where('meter_type', $request->meter_type)
                ->where('status', 'active')
                ->exists();

            if ($existingMeter) {
                return $this->sendError("Active {$request->meter_type} meter already exists for this room", [], 400);
            }

            $meter = Meter::create([
                'room_id' => $room->id,
                'meter_number' => $request->meter_number,
                'meter_type' => $request->meter_type,
                'initial_reading' => $request->initial_reading,
                'current_reading' => $request->initial_reading,
                'rate_per_unit' => $request->rate_per_unit,
                'status' => $request->status,
            ]);

            return $this->sendResponse($this->transformMeter($meter), 'Meter added successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to add meter', [$e->getMessage()], 500);
        }
    }

    /**
     * Get room meters
     */
    public function getMeters($id)
    {
        try {
            $room = Room::findOrFail($id);

            // Check authorization
            if ($room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $meters = $room->meters()->with(['meterReadings' => function ($q) {
                $q->latest()->limit(12);
            }])->get();

            $data = $meters->map(function ($meter) {
                return $this->transformMeter($meter, true);
            });

            return $this->sendResponse($data, 'Meters retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve meters', [$e->getMessage()], 500);
        }
    }

    /**
     * Update room status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:available,maintenance',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $room = Room::findOrFail($id);

            // Check authorization
            if ($room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Cannot change status if occupied
            if ($room->status === 'occupied') {
                return $this->sendError('Cannot change status of occupied room', [], 400);
            }

            $room->update([
                'status' => $request->status,
                'status_notes' => $request->notes,
                'status_updated_at' => now(),
            ]);

            return $this->sendResponse(['status' => $room->status], 'Status updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update status', [$e->getMessage()], 500);
        }
    }

    /**
     * Search rooms
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:1',
            'property_id' => 'nullable|exists:properties,id',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();
            $query = $request->input('query');

            $rooms = Room::whereHas('property', function ($q) use ($user) {
                $q->where('landlord_id', $user->id);
            })
                ->when($request->has('property_id'), function ($q) use ($request) {
                    $q->where('property_id', $request->property_id);
                })
                ->where('room_number', 'like', "%{$query}%")
                ->with('property', 'activeContract.tenant')
                ->get();

            $data = $rooms->map(function ($room) {
                return $this->transformRoom($room);
            });

            return $this->sendResponse($data, 'Search results retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Search failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Get amenity list
     */
    public function getAmenityList()
    {
        try {
            $user = Auth::user();

            $amenities = Amenity::where(function ($q) use ($user) {
                if ($user && $user->hasRole('landlord')) {
                    $q->where('landlord_id', $user->id)
                        ->orWhereNull('landlord_id');
                } else {
                    $q->whereNull('landlord_id');
                }
            })
                ->where('status', 'active')
                ->get(['id', 'name', 'amenity_price', 'description']);

            return $this->sendResponse($amenities, 'Amenities retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve amenities', [$e->getMessage()], 500);
        }
    }

    /**
     * Transform room for API response
     */
    protected function transformRoom($room, $detailed = false)
    {
        $data = [
            'id' => $room->id,
            'property' => [
                'id' => $room->property->id,
                'name' => $room->property->name,
            ],
            'room_number' => $room->room_number,
            'floor' => $room->floor,
            'size' => $room->size,
            'monthly_rent' => $room->monthly_rent,
            'status' => $room->status,
            'amenities' => $room->amenities ? $room->amenities->map(function ($amenity) {
                return [
                    'id' => $amenity->id,
                    'name' => $amenity->name,
                    'price' => $amenity->amenity_price,
                ];
            }) : [],
            'total_rent' => $room->monthly_rent + ($room->amenities ? $room->amenities->sum('amenity_price') : 0),
        ];

        if ($room->activeContract) {
            $data['current_tenant'] = [
                'id' => $room->activeContract->tenant->id,
                'name' => $room->activeContract->tenant->name,
                'contract_end' => $room->activeContract->end_date,
            ];
        }

        if ($detailed) {
            $data['room_type'] = $room->roomType ? [
                'id' => $room->roomType->id,
                'name' => $room->roomType->name,
            ] : null;
            $data['description'] = $room->description;
            $data['meters_count'] = $room->meters ? $room->meters->count() : 0;
            $data['created_at'] = $room->created_at;
            $data['updated_at'] = $room->updated_at;
        }

        return $data;
    }

    /**
     * Transform meter for API response
     */
    protected function transformMeter($meter, $detailed = false)
    {
        $data = [
            'id' => $meter->id,
            'meter_number' => $meter->meter_number,
            'meter_type' => $meter->meter_type,
            'current_reading' => $meter->current_reading,
            'rate_per_unit' => $meter->rate_per_unit,
            'status' => $meter->status,
        ];

        if ($detailed && $meter->meterReadings) {
            $data['recent_readings'] = $meter->meterReadings->map(function ($reading) {
                return [
                    'date' => $reading->reading_date,
                    'reading' => $reading->reading_value,
                    'consumption' => $reading->consumption,
                ];
            });
        }

        return $data;
    }
}