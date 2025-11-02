<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\BasePrice;
use App\Models\Contract;
use App\Services\Contract\ContractService;
use App\Services\Room\RoomService;
use App\Services\Tenant\TenantService;
use App\Services\Notification\NotificationService;
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
    protected ContractService $contractService;
    protected RoomService $roomService;
    protected TenantService $tenantService;
    protected NotificationService $notificationService;

    public function __construct(
        ContractService $contractService,
        RoomService $roomService,
        TenantService $tenantService,
        NotificationService $notificationService
    ) {
        $this->contractService = $contractService;
        $this->roomService = $roomService;
        $this->tenantService = $tenantService;
        $this->notificationService = $notificationService;
    }
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        if (!$currentUser->hasRole('landlord')) {
            return redirect()->route('unauthorized');
        }

        // Use service to get contracts with filters
        $filters = $request->all();
        $filters['paginate'] = true;
        $filters['per_page'] = $request->per_page ?? 15;
        $contracts = $this->contractService->getLandlordContracts($currentUser, $filters);

        // Use room service to get available rooms
        $availableRooms = $this->roomService->getLandlordAvailableRooms($currentUser);

        // Get all rooms for the landlord
        $allRooms = $this->roomService->getLandlordRooms($currentUser);

        // Get tenants
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
        // Authorization
        if ($contract->room->property->landlord_id !== Auth::id()) {
            abort(403);
        }

        // Use service to get contract details with statistics
        $contractDetails = $this->contractService->getContractDetails($contract);

        // Get tenant documents using service
        $tenantDocuments = $this->tenantService->getTenantDocuments($contract->tenant);

        // Paginated histories
        $invoices = $contract->invoices()
            ->latest('issue_date')
            ->paginate(10, ['*'], 'invoices_page');

        $utilityHistory = $contract->utilityBills()
            ->with('utilityType')
            ->latest('billing_period_end')
            ->paginate(10, ['*'], 'usage_page');

        return view('backends.dashboard.contracts.show', [
            'contract' => $contract,
            'invoices' => $invoices,
            'utilityHistory' => $utilityHistory,
            'totalMonthlyRent' => $contractDetails['total_monthly_rent'],
            'currentBalance' => $contractDetails['current_balance'],
            'daysRemaining' => $contractDetails['days_remaining'],
            'rentAmount' => $contractDetails['rent_amount'],
            'tenantDocuments' => $tenantDocuments,
        ]);
    }

    public function create()
    {
        $currentUser = Auth::user();

        // Use room service to get available rooms
        $availableRooms = $this->roomService->getLandlordAvailableRooms($currentUser);

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

        try {
            // Handle file uploads
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

            // Prepare data for service
            $validatedData['image'] = $imageDbPath;
            $validatedData['contract_image'] = $contractImageDbPath;

            // Use service to create contract with new tenant
            $contract = $this->contractService->createContractWithTenant($validatedData, $currentUser);

            // Send welcome notification
            $this->notificationService->sendWelcomeTenantNotification(
                $contract->tenant,
                $contract,
                ['email']
            );

            return redirect()->route('landlord.contracts.index')
                ->with('success', 'New tenant and contract created successfully.');

        } catch (\Exception $e) {
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

        try {
            // Handle contract image upload
            if ($request->hasFile('contract_image')) {
                if ($contract->contract_image && File::exists(public_path($contract->contract_image))) {
                    File::delete(public_path($contract->contract_image));
                }
                $file = $request->file('contract_image');
                $filename = time() . '_contract_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/contracts'), $filename);
                $validatedData['contract_image'] = 'uploads/contracts/' . $filename;
            }

            // Use service to update contract
            $updatedContract = $this->contractService->updateContract($contract, $validatedData);

            return back()->with('success', 'Contract and Room status updated successfully.');

        } catch (\Exception $e) {
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
        $currentUser = Auth::user();
        if (!$currentUser->hasRole('landlord') || $contract->room->property->landlord_id !== $currentUser->id) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Check for existing invoices before deleting
        if ($contract->invoices()->exists()) {
            return back()->with('error', 'Contracts with existing invoices cannot be deleted.');
        }

        try {
            // Delete contract image if exists
            if ($contract->contract_image && File::exists(public_path($contract->contract_image))) {
                File::delete(public_path($contract->contract_image));
            }

            // Use service to terminate/delete contract
            $this->contractService->terminateContract($contract, true);

            return redirect()->route('landlord.contracts.index')->with('success', 'Contract has been deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Contract deletion failed for contract ID ' . $contract->id . ': ' . $e->getMessage());
            return back()->with('error', 'An error occurred while trying to delete the contract.');
        }
    }
}
