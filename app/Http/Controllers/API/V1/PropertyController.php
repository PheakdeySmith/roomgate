<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Property;
use App\Services\Property\PropertyService;
use App\Services\Room\RoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PropertyController extends BaseController
{
    protected PropertyService $propertyService;
    protected RoomService $roomService;

    public function __construct(PropertyService $propertyService, RoomService $roomService)
    {
        $this->propertyService = $propertyService;
        $this->roomService = $roomService;
    }

    /**
     * Display a listing of properties
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            // Get properties with filters
            $properties = $this->propertyService->getLandlordProperties($user, $request->all());

            // Transform data for API response
            $data = $properties->map(function ($property) {
                return $this->transformProperty($property);
            });

            if ($request->has('page')) {
                return $this->sendPaginatedResponse($properties, 'Properties retrieved successfully');
            }

            return $this->sendResponse($data, 'Properties retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve properties', [$e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created property
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
            'status' => 'required|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            // Check subscription limits
            if ($user->hasReachedPropertyLimit()) {
                $subscription = $user->activeSubscription();
                $limit = $subscription ? $subscription->subscriptionPlan->properties_limit : 0;
                return $this->sendError("Property limit reached ($limit). Please upgrade your subscription.", [], 403);
            }

            // Create property using service
            $property = $this->propertyService->createProperty($request->all(), $user);

            $data = $this->transformProperty($property);

            return $this->sendResponse($data, 'Property created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('Failed to create property', [$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified property
     */
    public function show($id)
    {
        try {
            $property = Property::findOrFail($id);

            // Check authorization
            if ($property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Get detailed property info using service
            $stats = $this->propertyService->getPropertyStatistics($property);

            $data = $this->transformProperty($property);
            $data['statistics'] = $stats;
            $data['rooms'] = $this->roomService->getPropertyRooms($property->id)->map(function ($room) {
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'floor' => $room->floor,
                    'status' => $room->status,
                    'monthly_rent' => $room->monthly_rent,
                    'tenant' => $room->activeContract ? [
                        'id' => $room->activeContract->tenant->id,
                        'name' => $room->activeContract->tenant->name,
                    ] : null,
                ];
            });

            return $this->sendResponse($data, 'Property details retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Property not found', [$e->getMessage()], 404);
        }
    }

    /**
     * Update the specified property
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'property_type' => 'sometimes|required|string|in:apartment,house,condo,townhouse,commercial',
            'description' => 'nullable|string',
            'address_line_1' => 'sometimes|required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'state_province' => 'sometimes|required|string|max:255',
            'postal_code' => 'sometimes|required|string|max:20',
            'country' => 'sometimes|required|string|max:255',
            'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
            'status' => 'sometimes|required|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $property = Property::findOrFail($id);

            // Check authorization
            if ($property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Update using service
            $property = $this->propertyService->updateProperty($property, $request->all());

            $data = $this->transformProperty($property);

            return $this->sendResponse($data, 'Property updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update property', [$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified property
     */
    public function destroy($id)
    {
        try {
            $property = Property::findOrFail($id);

            // Check authorization
            if ($property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if property has active contracts
            $activeContracts = $property->rooms()->whereHas('contracts', function ($q) {
                $q->where('status', 'active');
            })->count();

            if ($activeContracts > 0) {
                return $this->sendError('Cannot delete property with active contracts', [], 400);
            }

            // Delete using service
            $this->propertyService->deleteProperty($property);

            return $this->sendResponse(null, 'Property deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete property', [$e->getMessage()], 500);
        }
    }

    /**
     * Get property rooms
     */
    public function getRooms($id)
    {
        try {
            $property = Property::findOrFail($id);

            // Check authorization
            if ($property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $rooms = $this->roomService->getPropertyRooms($property->id);

            $data = $rooms->map(function ($room) {
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'floor' => $room->floor,
                    'size' => $room->size,
                    'status' => $room->status,
                    'monthly_rent' => $room->monthly_rent,
                    'amenities' => $room->amenities->map(function ($amenity) {
                        return [
                            'id' => $amenity->id,
                            'name' => $amenity->name,
                            'price' => $amenity->amenity_price,
                        ];
                    }),
                    'current_tenant' => $room->activeContract ? [
                        'id' => $room->activeContract->tenant->id,
                        'name' => $room->activeContract->tenant->name,
                        'contract_end' => $room->activeContract->end_date,
                    ] : null,
                ];
            });

            return $this->sendResponse($data, 'Rooms retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve rooms', [$e->getMessage()], 500);
        }
    }

    /**
     * Get property statistics
     */
    public function getStats($id)
    {
        try {
            $property = Property::findOrFail($id);

            // Check authorization
            if ($property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $stats = $this->propertyService->getPropertyStatistics($property);

            return $this->sendResponse($stats, 'Statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve statistics', [$e->getMessage()], 500);
        }
    }

    /**
     * Get property occupancy
     */
    public function getOccupancy($id)
    {
        try {
            $property = Property::findOrFail($id);

            // Check authorization
            if ($property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $occupancy = $this->propertyService->getOccupancyRate($property);

            $data = [
                'occupancy_rate' => $occupancy,
                'total_rooms' => $property->rooms->count(),
                'occupied_rooms' => $property->rooms->where('status', 'occupied')->count(),
                'available_rooms' => $property->rooms->where('status', 'available')->count(),
                'maintenance_rooms' => $property->rooms->where('status', 'maintenance')->count(),
            ];

            return $this->sendResponse($data, 'Occupancy data retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve occupancy data', [$e->getMessage()], 500);
        }
    }

    /**
     * Upload property image
     */
    public function uploadImage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $property = Property::findOrFail($id);

            // Check authorization
            if ($property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($property->cover_image && File::exists(public_path($property->cover_image))) {
                    File::delete(public_path($property->cover_image));
                }

                $file = $request->file('image');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/property-photos');
                File::makeDirectory($destinationPath, 0755, true, true);
                $file->move($destinationPath, $filename);

                $imagePath = 'uploads/property-photos/' . $filename;
                $property->update(['cover_image' => $imagePath]);
            }

            $data = [
                'image_url' => asset($property->cover_image),
            ];

            return $this->sendResponse($data, 'Image uploaded successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to upload image', [$e->getMessage()], 500);
        }
    }

    /**
     * Search properties
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();
            $query = $request->input('query');

            $properties = Property::where('landlord_id', $user->id)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('address_line_1', 'like', "%{$query}%")
                        ->orWhere('city', 'like', "%{$query}%")
                        ->orWhere('property_type', 'like', "%{$query}%");
                })
                ->get();

            $data = $properties->map(function ($property) {
                return $this->transformProperty($property);
            });

            return $this->sendResponse($data, 'Search results retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Search failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Get property types
     */
    public function getPropertyTypes()
    {
        $types = [
            ['value' => 'apartment', 'label' => 'Apartment'],
            ['value' => 'house', 'label' => 'House'],
            ['value' => 'condo', 'label' => 'Condominium'],
            ['value' => 'townhouse', 'label' => 'Townhouse'],
            ['value' => 'commercial', 'label' => 'Commercial'],
        ];

        return $this->sendResponse($types, 'Property types retrieved successfully');
    }

    /**
     * Transform property for API response
     */
    protected function transformProperty($property)
    {
        return [
            'id' => $property->id,
            'name' => $property->name,
            'property_type' => $property->property_type,
            'description' => $property->description,
            'address' => [
                'line_1' => $property->address_line_1,
                'line_2' => $property->address_line_2,
                'city' => $property->city,
                'state_province' => $property->state_province,
                'postal_code' => $property->postal_code,
                'country' => $property->country,
            ],
            'year_built' => $property->year_built,
            'status' => $property->status,
            'cover_image' => $property->cover_image ? asset($property->cover_image) : null,
            'total_rooms' => $property->rooms_count ?? $property->rooms->count(),
            'occupied_rooms' => $property->rooms ? $property->rooms->where('status', 'occupied')->count() : 0,
            'created_at' => $property->created_at,
            'updated_at' => $property->updated_at,
        ];
    }
}