<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\UtilityRate;
use App\Models\UtilityType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UtilityRateController extends Controller
{
    public function index(Property $property)
    {
        $this->_authorizeLandlordAction($property);

        $allUtilityTypes = UtilityType::orderBy('name')->get();

        $existingRates = $property->utilityRates->keyBy('utility_type_id');

        $utilityData = $allUtilityTypes->map(function ($utilityType) use ($existingRates) {
            $rate = $existingRates->get($utilityType->id);

            return (object) [
                'type' => $utilityType,
                'rate' => $rate,
            ];
        });

        return view('backends.dashboard.utilities.utility', [
            'property' => $property,
            'utilityData' => $utilityData,
            'allUtilityTypes' => $allUtilityTypes,
        ]);
    }

    public function store(Request $request, Property $property)
    {
        $this->_authorizeLandlordAction($property);

        $request->validate([
            'utility_type_id' => [
                'required',
                'exists:utility_types,id',

                Rule::unique('utility_rates')->where(function ($query) use ($property) {
                    return $query->where('property_id', $property->id);
                }),
            ],
            'rate' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
        ], [
            // Custom error message for the unique rule
            'utility_type_id.unique' => 'A rate for this utility type has already been set for this property.'
        ]);

        $property->utilityRates()->create($request->all());

        return back()->with('success', 'Utility rate created successfully.');
    }

    /**
     * Update the specified utility rate in storage.
     * This is called when you submit the "Edit Rate" modal form.
     */
    public function update(Request $request, UtilityRate $rate)
    {
        // Authorize that the landlord owns the property associated with the rate
        $this->_authorizeLandlordAction($rate->property);

        $request->validate([
            'rate' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
        ]);

        $rate->update($request->only('rate', 'effective_from'));

        return back()->with('success', 'Utility rate updated successfully.');
    }

    /**
     * Remove the specified utility rate from storage.
     * This is called when you click the delete button in the table.
     */
    public function destroy(UtilityRate $rate)
    {
        // Authorize that the landlord owns the property associated with the rate
        $this->_authorizeLandlordAction($rate->property);

        $rate->delete();

        return back()->with('success', 'Utility rate deleted successfully.');
    }

    private function _authorizeLandlordAction(Property $property): void
    {
        $user = Auth::user();

        if (!$user->isLandlord() || !$property->isOwnedBy($user)) {
            abort(403);
        }
    }
}
