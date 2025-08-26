<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\RoomType;
use Illuminate\Http\Request;
use App\Models\PriceOverride;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PriceOverrideController extends Controller
{
    public function index(Property $property, RoomType $roomType)
    {
        $this->_authorizeLandlordAction($property);

        if (!$property->roomTypes()->where('room_types.id', $roomType->id)->exists()) {
            return redirect()->route('accessDenied');
        }

        $priceOverrides = $roomType->priceOverrides()
                               ->where('property_id', $property->id)
                               ->get();

        $events = $priceOverrides->map(function ($override) {
            return [
                'id' => $override->id,
                'title' => $override->title,
                'start' => $override->start_date->toDateString(),
                'end' => $override->end_date->addDay()->toDateString(),
                'className' => $override->color,
                'allDay' => true,
                'extendedProps' => [
                    'price' => $override->price,
                    'color' => $override->color
                ]
            ];
        });

        $overridesForTable = $priceOverrides->map(function ($override, $key) use ($property, $roomType) {
        // Generate the destroy URL for the action button
        $destroyUrl = route('landlord.properties.roomTypes.overrides.destroy', [$property, $roomType, $override]);

        // Return a simple array, just like your properties table script expects
        return [
            $key + 1,
            $override->id,
            $override->title,
            '$' . number_format($override->price, 2),
            $override->start_date->format('Y-m-d'),
            $override->end_date->format('Y-m-d'),
            $override->color,
            (object) [
                'destroy_url' => $destroyUrl,
                'override_id' => $override->id,
                'override_name' => $override->title,
            ]
        ];
    });

        return view('backends.dashboard.properties.price-override', [
            'property' => $property,
            'roomType' => $roomType,
            'events' => $events,
            'overridesForTable' => $overridesForTable,
        ]);
    }

    public function store(Request $request, Property $property, RoomType $roomType)
    {
        $this->_authorizeLandlordAction($property);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'color' => [
                'required',
                'string',
                Rule::in([
                    'bg-primary-subtle',
                    'bg-info-subtle',
                    'bg-warning-subtle',
                    'bg-danger-subtle',
                    'bg-success-subtle'
                ])
            ],
        ]);

        $validatedData['room_type_id'] = $roomType->id;
        $validatedData['property_id'] = $property->id;
        $override = PriceOverride::create($validatedData);

        $event = [
            'id' => $override->id,
            'title' => $override->title,
            'start' => $override->start_date->toDateString(),
            'end' => $override->end_date->addDay()->toDateString(),
            'className' => $override->color,
            'allDay' => true,
            'extendedProps' => [
                'price' => $override->price,
                'color' => $override->color
            ]
        ];

        return response()->json($event, 201, ['success' => 'Override created successfully!']);
    }

    public function update(Request $request, Property $property, RoomType $roomType, PriceOverride $override)
    {
        $this->_authorizeLandlordAction($property);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'color' => ['required', 'string', Rule::in(['bg-primary-subtle', 'bg-info-subtle', 'bg-warning-subtle', 'bg-danger-subtle', 'bg-success-subtle'])],
        ]);

        $override->update($validatedData);

        // This part is crucial. It must return the full event data.
        $event = [
            'id' => $override->id,
            'title' => $override->title,
            'start' => $override->start_date->toDateString(),
            'end' => $override->end_date->addDay()->toDateString(),
            'className' => $override->color,
            'allDay' => true,
            'extendedProps' => [
                'price' => $override->price,
                'color' => $override->color
            ]
        ];

        // Return the updated event as JSON
        return response()->json($event);
    }

    /**
     * Remove the specified price override from storage.
     */
    public function destroy(Property $property, RoomType $roomType, PriceOverride $override)
    {
        $this->_authorizeLandlordAction($property);

        // Delete the record
        $override->delete();

        return response()->json(['success' => 'Event deleted successfully!']);
    }

    private function _authorizeLandlordAction(Property $property): void
    {
        $user = Auth::user();

        if (!$user->isLandlord() || !$property->isOwnedBy($user)) {
            abort(403);
        }
    }
}
