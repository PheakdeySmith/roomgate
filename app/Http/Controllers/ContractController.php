<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\BasePrice;
use App\Models\Contract;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        if (!$currentUser->hasRole('landlord')) {
            return redirect()->route('unauthorized');
        }

        $contractsQuery = Contract::whereHas('room.property', function ($query) use ($currentUser) {
            $query->where('landlord_id', $currentUser->id);
        })->with(['room.property', 'tenant']);
        
        // Filter by tenant_id if provided (for direct linking from users list)
        if ($request->has('tenant_id')) {
            $contractsQuery->where('user_id', $request->tenant_id); // Using user_id instead of tenant_id
        }
        
        $contracts = $contractsQuery->latest()->get();

        $availableRooms = Room::whereHas('property', function ($query) use ($currentUser) {
            $query->where('landlord_id', $currentUser->id);
        })
            ->where('status', Room::STATUS_AVAILABLE)
            ->with('property')
            ->get();

        $allRooms = Room::whereHas('property', function ($query) use ($currentUser) {
            $query->where('landlord_id', $currentUser->id);
        })
            ->with('property')
            ->get();

        $tenants = User::role('tenant')->where('landlord_id', $currentUser->id)->get();

        return view('backends.dashboard.contracts.index', compact(
            'contracts',
            'availableRooms',
            'allRooms',
            'tenants'
        ));
    }

    public function show(Contract $contract)
    {
        // --- Authorization ---
        if ($contract->room->property->landlord_id !== Auth::id()) {
            abort(403);
        }

        // --- Eager-load core relationships ---
        $contract->load(['tenant', 'room.property', 'room.amenities']);

        // --- Fetch paginated histories for the tabs ---
        $invoices = $contract->invoices()
            ->latest('issue_date')
            ->paginate(10, ['*'], 'invoices_page');

        $utilityHistory = $contract->utilityBills()
            ->with('utilityType')
            ->latest('billing_period_end')
            ->paginate(10, ['*'], 'usage_page');
            
        // --- Fetch tenant documents ---
        $tenantDocuments = \App\Models\Document::where('user_id', $contract->user_id)
            ->latest()
            ->get();

        // --- Calculate Stats ---
        // If rent_amount is null, get the base price from the room type
        if ($contract->rent_amount === null) {
            // Get the latest base price for the room's type and property
            $basePrice = BasePrice::where('property_id', $contract->room->property_id)
                ->where('room_type_id', $contract->room->room_type_id)
                ->orderBy('effective_date', 'desc')
                ->first();
                
            $rentAmount = $basePrice ? $basePrice->price : 0;
        } else {
            $rentAmount = $contract->rent_amount;
        }
        
        // Get amenities assigned directly to the room
        $roomAmenities = $contract->room->amenities;

        // Calculate total monthly rent
        $totalMonthlyRent = (float) $rentAmount + $roomAmenities->sum('amenity_price');
        
        $totalBilled = $contract->invoices()->sum('total_amount');
        $totalPaid = $contract->invoices()->sum('paid_amount');
        $currentBalance = $totalBilled - $totalPaid;
        $daysRemaining = max(0, intval(now()->diffInDays($contract->end_date, false)));

        return view('backends.dashboard.contracts.show', compact(
            'contract',
            'invoices',
            'utilityHistory',
            'totalMonthlyRent',
            'currentBalance',
            'daysRemaining',
            'rentAmount',
            'tenantDocuments'
        ));
    }

    public function create()
    {
        $currentUser = Auth::user();
        // Get all rooms that belong to the landlord and are currently available
        $availableRooms = Room::whereHas('property', function ($query) use ($currentUser) {
            $query->where('landlord_id', $currentUser->id);
        })->where('status', 'available')->get();

        return view('backends.dashboard.contracts.create', compact('availableRooms'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();

        if (!$currentUser->hasRole('landlord')) {
            return back()->with('error', 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'room_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($currentUser) {
                    $room = Room::with('property')->find($value);
                    if (!$room || $room->property->landlord_id !== $currentUser->id) {
                        $fail('The selected room is invalid or does not belong to you.');
                    } elseif ($room->status !== 'available') {
                        $fail('The selected room is not available.');
                    }
                },
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'rent_amount' => 'nullable|numeric|min:0',
            'billing_cycle' => 'required|string|in:daily,monthly,yearly',
            'contract_image' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $imageDbPath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_image_' . Str::slug($originalName) . '.' . $extension;
                $destinationPath = public_path('uploads/profile-photos');
                $relativeDbPath = 'uploads/profile-photos/' . $filename;

                if (!File::isDirectory($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }

                $file->move($destinationPath, $filename);
                $imageDbPath = $relativeDbPath;
            }

            $contractImageDbPath = null;
            if ($request->hasFile('contract_image')) {
                $file = $request->file('contract_image');
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_contract_' . Str::slug($originalName) . '.' . $extension;
                $destinationPath = public_path('uploads/contracts');
                $relativeDbPath = 'uploads/contracts/' . $filename;

                if (!File::isDirectory($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }

                $file->move($destinationPath, $filename);
                $contractImageDbPath = $relativeDbPath;
            }

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'password' => Hash::make($validatedData['password']),
                'image' => $imageDbPath,
                'landlord_id' => $currentUser->id,
                'status' => 'active',
            ]);
            $user->assignRole('tenant');

            Contract::create([
                'user_id' => $user->id,
                'room_id' => $validatedData['room_id'],
                'landlord_id' => $currentUser->id,
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'rent_amount' => $validatedData['rent_amount'],
                'billing_cycle' => $validatedData['billing_cycle'],
                'status' => 'active',
                'contract_image' => $contractImageDbPath,
            ]);

            $room = Room::find($validatedData['room_id']);
            $room->status = 'occupied';
            $room->save();

            DB::commit();

            return redirect()->route('landlord.contracts.index')->with('success', 'New tenant and contract created successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to create new tenant and contract: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred. Please try again.')->withInput();
        }
    }

    public function update(Request $request, Contract $contract)
    {
        $currentUser = Auth::user();

        if (!$currentUser->hasRole('landlord') || $contract->room->property->landlord_id !== $currentUser->id) {
            return redirect()->route('unauthorized');
        }

        $validatedData = $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) use ($currentUser) {
                    return $query->where('landlord_id', $currentUser->id);
                }),
            ],
            'room_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($contract, $currentUser) {
                    $room = Room::with('property')->find($value);
                    if (!$room || !$room->property || $room->property->landlord_id !== $currentUser->id) {
                        $fail('The selected room does not belong to you.');
                        return;
                    }
                    if ($room->status !== Room::STATUS_AVAILABLE && $room->id !== $contract->room_id) {
                        $fail('The selected room is already occupied by another contract.');
                    }
                }
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'rent_amount' => 'nullable|numeric|min:0',
            'billing_cycle' => 'required|string|in:daily,monthly,yearly',
            'status' => 'required|string|in:active,expired,terminated',
            'contract_image' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validatedData['status'] === 'active') {
            $conflictingContract = Contract::where('room_id', $validatedData['room_id'])
                ->where('status', 'active')
                ->where('id', '!=', $contract->id)
                ->exists();

            if ($conflictingContract) {
                return back()->with('error', 'This room is already occupied by an active contract.');
            }
        }

        DB::beginTransaction();

        try {


            if ($request->hasFile('contract_image')) {
                if ($contract->contract_image && File::exists(public_path($contract->contract_image))) {
                    File::delete(public_path($contract->contract_image));
                }
                $file = $request->file('contract_image');
                $filename = time() . '_contract_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/contracts'), $filename);
                $validatedData['contract_image'] = 'uploads/contracts/' . $filename;
            }

            $originalRoomId = $contract->room_id;
            $newRoomId = (int) $validatedData['room_id'];

            $contract->update($validatedData);

            if ($originalRoomId !== $newRoomId) {
                $oldRoom = Room::find($originalRoomId);
                if ($oldRoom) {
                    $oldRoom->status = Room::STATUS_AVAILABLE;
                    $oldRoom->save();
                }
            }

            $newRoom = Room::find($newRoomId);
            if ($newRoom) {
                // Use the validated status for guaranteed accuracy
                if ($validatedData['status'] === 'active') {
                    $newRoom->status = Room::STATUS_OCCUPIED;
                } else {
                    // If contract is not active (expired/terminated), the room becomes available
                    $newRoom->status = Room::STATUS_AVAILABLE;
                }
                $newRoom->save();
            }

            DB::commit();
            return back()->with('success', 'Contract and Room status updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Contract update failed for contract ID ' . $contract->id . ': ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred. Could not update the contract.')->withInput();
        }
    }
    
    /**
     * Find a tenant's contract and redirect to it
     *
     * @param int $userId The user ID (tenant)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function findTenantContract($userId)
    {
        $currentUser = Auth::user();
        if (!$currentUser->hasRole('landlord')) {
            return redirect()->route('unauthorized');
        }

        // Find the most recent active contract for this tenant
        $contract = Contract::whereHas('room.property', function ($query) use ($currentUser) {
            $query->where('landlord_id', $currentUser->id);
        })
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->first();

        if ($contract) {
            // If contract exists, redirect to the contract details page
            return redirect()->route('landlord.contracts.show', $contract->id);
        } else {
            // If no contract found, show all contracts filtered by this tenant
            return redirect()->route('landlord.contracts.index', ['tenant_id' => $userId])
                ->with('warning', 'No active contract found for this tenant. Showing all related contracts.');
        }
    }

    public function destroy(Contract $contract)
    {
        // --- Authorization (Your existing check is good) ---
        $currentUser = Auth::user();
        if (!$currentUser->hasRole('landlord') || $contract->room->property->landlord_id !== $currentUser->id) {
            return back()->with('error', 'Unauthorized action.');
        }

        // --- ✨ NEW: Check for existing invoices before deleting ---
        if ($contract->invoices()->exists()) {
            return back()->with('error', 'Contracts with existing invoices cannot be deleted.');
        }

        // --- Your existing deletion logic is good ---
        DB::beginTransaction();
        try {
            if ($contract->contract_image && File::exists(public_path($contract->contract_image))) {
                File::delete(public_path($contract->contract_image));
            }

            $room = $contract->room;
            if ($room) {
                $room->status = Room::STATUS_AVAILABLE;
                $room->save();
            }

            $contract->delete();

            DB::commit();

            return redirect()->route('landlord.contracts.index')->with('success', 'Contract has been deleted successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Contract deletion failed for contract ID ' . $contract->id . ': ' . $e->getMessage());
            return back()->with('error', 'An error occurred while trying to delete the contract.');
        }
    }
}
