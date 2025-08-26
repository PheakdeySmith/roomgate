<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\UtilityType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UtilityTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentUser = Auth::user();

        if (!$currentUser->hasRole(['landlord', 'admin'])) {
            return redirect()->route('unauthorized');
        }

        $properties = collect();

        $utilityTypes = UtilityType::latest()->get();

        $properties = Property::query()
            ->with('utilityRates.utilityType')
            ->where('landlord_id', $currentUser->id)
            ->latest()
            ->get();

        return view('backends.dashboard.utilities.index', compact('properties', 'utilityTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Authorization
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('unauthorized');
        }

        // 2. Validation
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:utility_types,name',
            'unit_of_measure' => 'required|string|max:255',
            'billing_type' => ['required', Rule::in(['metered', 'flat_rate'])],
        ]);

        UtilityType::create($validatedData);

        return redirect()->back()->with('success', 'Utility Type created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UtilityType $utilityType)
    {
        // 1. Authorization
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('unauthorized');
        }

        // 2. Validation
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('utility_types')->ignore($utilityType->id)],
            'unit_of_measure' => 'required|string|max:255',
            'billing_type' => ['required', Rule::in(['metered', 'flat_rate'])],
        ]);

        $utilityType->update($validatedData);

        return redirect()->back()->with('success', 'Utility Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UtilityType $utilityType)
    {
        // 1. Authorization
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('unauthorized');
        }

        if ($utilityType->meters()->exists() || $utilityType->utilityRates()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete this utility type because it is currently in use by a meter or a rate plan.');
        }

        $utilityType->delete();

        return redirect()->back()->with('success', 'Utility Type deleted successfully.');
    }
}
