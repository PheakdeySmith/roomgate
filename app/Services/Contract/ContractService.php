<?php

namespace App\Services\Contract;

use App\Models\Contract;
use App\Models\Room;
use App\Models\User;
use App\Models\BasePrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class ContractService
{
    /**
     * Create a new contract with tenant
     */
    public function createContractWithTenant(array $data, $landlord): Contract
    {
        return DB::transaction(function () use ($data, $landlord) {
            // Handle tenant image upload
            $tenantImagePath = null;
            if (isset($data['tenant_image']) && $data['tenant_image'] instanceof UploadedFile) {
                $tenantImagePath = $this->uploadFile(
                    $data['tenant_image'],
                    'uploads/profile-photos',
                    'image'
                );
            }

            // Handle contract document upload
            $contractImagePath = null;
            if (isset($data['contract_image']) && $data['contract_image'] instanceof UploadedFile) {
                $contractImagePath = $this->uploadFile(
                    $data['contract_image'],
                    'uploads/contracts',
                    'contract'
                );
            }

            // Create tenant user
            $tenant = User::create([
                'name' => $data['tenant_name'],
                'email' => $data['tenant_email'],
                'phone' => $data['tenant_phone'] ?? null,
                'password' => Hash::make($data['tenant_password']),
                'image' => $tenantImagePath,
                'landlord_id' => $landlord->id,
                'status' => 'active',
            ]);

            $tenant->assignRole('tenant');

            // Create contract
            $contract = Contract::create([
                'user_id' => $tenant->id,
                'room_id' => $data['room_id'],
                'landlord_id' => $landlord->id,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'rent_amount' => $data['rent_amount'] ?? null,
                'billing_cycle' => $data['billing_cycle'],
                'status' => 'active',
                'contract_image' => $contractImagePath,
            ]);

            // Update room status
            $this->updateRoomStatus($data['room_id'], Room::STATUS_OCCUPIED);

            return $contract;
        });
    }

    /**
     * Create contract for existing tenant
     */
    public function createContract(array $data, $landlord): Contract
    {
        return DB::transaction(function () use ($data, $landlord) {
            // Handle contract document upload
            $contractImagePath = null;
            if (isset($data['contract_image']) && $data['contract_image'] instanceof UploadedFile) {
                $contractImagePath = $this->uploadFile(
                    $data['contract_image'],
                    'uploads/contracts',
                    'contract'
                );
            }

            // Create contract
            $contract = Contract::create([
                'user_id' => $data['user_id'],
                'room_id' => $data['room_id'],
                'landlord_id' => $landlord->id,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'rent_amount' => $data['rent_amount'] ?? null,
                'billing_cycle' => $data['billing_cycle'],
                'status' => $data['status'] ?? 'active',
                'contract_image' => $contractImagePath,
            ]);

            // Update room status based on contract status
            if ($contract->status === 'active') {
                $this->updateRoomStatus($data['room_id'], Room::STATUS_OCCUPIED);
            }

            return $contract;
        });
    }

    /**
     * Update an existing contract
     */
    public function updateContract(Contract $contract, array $data): Contract
    {
        return DB::transaction(function () use ($contract, $data) {
            $originalRoomId = $contract->room_id;
            $newRoomId = $data['room_id'] ?? $originalRoomId;

            // Handle contract document upload/replacement
            if (isset($data['contract_image']) && $data['contract_image'] instanceof UploadedFile) {
                // Delete old file if exists
                if ($contract->contract_image) {
                    $this->deleteFile($contract->contract_image);
                }

                $data['contract_image'] = $this->uploadFile(
                    $data['contract_image'],
                    'uploads/contracts',
                    'contract'
                );
            }

            // Update contract
            $contract->update($data);

            // Handle room status changes
            if ($originalRoomId !== $newRoomId) {
                // Free up the old room
                $this->updateRoomStatus($originalRoomId, Room::STATUS_AVAILABLE);
            }

            // Update new/current room status based on contract status
            if ($data['status'] === 'active') {
                $this->updateRoomStatus($newRoomId, Room::STATUS_OCCUPIED);
            } else {
                $this->updateRoomStatus($newRoomId, Room::STATUS_AVAILABLE);
            }

            return $contract->fresh();
        });
    }

    /**
     * Terminate a contract
     */
    public function terminateContract(Contract $contract): bool
    {
        return DB::transaction(function () use ($contract) {
            // Update contract status
            $contract->update([
                'status' => 'terminated',
                'terminated_date' => now(),
            ]);

            // Free up the room
            $this->updateRoomStatus($contract->room_id, Room::STATUS_AVAILABLE);

            return true;
        });
    }

    /**
     * Delete a contract (if no invoices exist)
     */
    public function deleteContract(Contract $contract): bool
    {
        // Check for existing invoices
        if ($contract->invoices()->exists()) {
            throw new \Exception('Contracts with existing invoices cannot be deleted.');
        }

        return DB::transaction(function () use ($contract) {
            // Delete contract image if exists
            if ($contract->contract_image) {
                $this->deleteFile($contract->contract_image);
            }

            // Free up the room
            $this->updateRoomStatus($contract->room_id, Room::STATUS_AVAILABLE);

            // Delete the contract
            $contract->delete();

            return true;
        });
    }

    /**
     * Calculate rent amount for a contract
     */
    public function calculateRentAmount(Contract $contract): float
    {
        if ($contract->rent_amount !== null) {
            return $contract->rent_amount;
        }

        // Get base price from room type
        $basePrice = BasePrice::where('property_id', $contract->room->property_id)
            ->where('room_type_id', $contract->room->room_type_id)
            ->orderBy('effective_date', 'desc')
            ->first();

        return $basePrice ? $basePrice->price : 0;
    }

    /**
     * Get contract statistics
     */
    public function getContractStats(Contract $contract): array
    {
        $rentAmount = $this->calculateRentAmount($contract);
        $roomAmenities = $contract->room->amenities;
        $totalMonthlyRent = $rentAmount + $roomAmenities->sum('amenity_price');

        $totalBilled = $contract->invoices()->sum('total_amount');
        $totalPaid = $contract->invoices()->sum('paid_amount');
        $currentBalance = $totalBilled - $totalPaid;

        $daysRemaining = max(0, intval(now()->diffInDays($contract->end_date, false)));

        return [
            'rent_amount' => $rentAmount,
            'amenities_amount' => $roomAmenities->sum('amenity_price'),
            'total_monthly_rent' => $totalMonthlyRent,
            'total_billed' => $totalBilled,
            'total_paid' => $totalPaid,
            'current_balance' => $currentBalance,
            'days_remaining' => $daysRemaining,
            'is_expiring_soon' => $daysRemaining <= 30,
            'is_expired' => $contract->end_date < now(),
        ];
    }

    /**
     * Get contracts for a landlord with filters
     */
    public function getLandlordContracts($landlord, array $filters = [])
    {
        $query = Contract::whereHas('room.property', function ($q) use ($landlord) {
            $q->where('landlord_id', $landlord->id);
        })->with(['room.property', 'tenant']);

        // Apply filters
        if (isset($filters['tenant_id'])) {
            $query->where('user_id', $filters['tenant_id']);
        }

        if (isset($filters['room_id'])) {
            $query->where('room_id', $filters['room_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['property_id'])) {
            $query->whereHas('room', function ($q) use ($filters) {
                $q->where('property_id', $filters['property_id']);
            });
        }

        // Date range filter
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereBetween('start_date', [$filters['start_date'], $filters['end_date']])
                    ->orWhereBetween('end_date', [$filters['start_date'], $filters['end_date']]);
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        return isset($filters['paginate']) && $filters['paginate'] ?
            $query->paginate($filters['per_page'] ?? 15) :
            $query->get();
    }

    /**
     * Check if a room is available for a contract period
     */
    public function isRoomAvailable(int $roomId, Carbon $startDate, Carbon $endDate, ?int $excludeContractId = null): bool
    {
        $query = Contract::where('room_id', $roomId)
            ->where('status', 'active')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            });

        if ($excludeContractId) {
            $query->where('id', '!=', $excludeContractId);
        }

        return !$query->exists();
    }

    /**
     * Get expiring contracts
     */
    public function getExpiringContracts($landlord, int $daysAhead = 30)
    {
        $expiryDate = now()->addDays($daysAhead);

        return Contract::whereHas('room.property', function ($q) use ($landlord) {
            $q->where('landlord_id', $landlord->id);
        })
            ->where('status', 'active')
            ->where('end_date', '<=', $expiryDate)
            ->where('end_date', '>=', now())
            ->with(['tenant', 'room.property'])
            ->orderBy('end_date', 'asc')
            ->get();
    }

    /**
     * Renew a contract
     */
    public function renewContract(Contract $contract, array $data): Contract
    {
        return DB::transaction(function () use ($contract, $data) {
            // Mark the old contract as expired
            $contract->update(['status' => 'expired']);

            // Create a new contract with the same details
            $newContract = Contract::create([
                'user_id' => $contract->user_id,
                'room_id' => $contract->room_id,
                'landlord_id' => $contract->landlord_id,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'rent_amount' => $data['rent_amount'] ?? $contract->rent_amount,
                'billing_cycle' => $data['billing_cycle'] ?? $contract->billing_cycle,
                'status' => 'active',
                'contract_image' => $data['contract_image'] ?? null,
                'previous_contract_id' => $contract->id,
            ]);

            return $newContract;
        });
    }

    /**
     * Mark expired contracts
     */
    public function markExpiredContracts(): int
    {
        $expiredCount = Contract::where('status', 'active')
            ->where('end_date', '<', now())
            ->update([
                'status' => 'expired'
            ]);

        // Free up rooms for expired contracts
        $expiredContracts = Contract::where('status', 'expired')
            ->whereHas('room', function ($q) {
                $q->where('status', Room::STATUS_OCCUPIED);
            })
            ->get();

        foreach ($expiredContracts as $contract) {
            $this->updateRoomStatus($contract->room_id, Room::STATUS_AVAILABLE);
        }

        return $expiredCount;
    }

    /**
     * Update room status
     */
    protected function updateRoomStatus(int $roomId, string $status): void
    {
        Room::where('id', $roomId)->update(['status' => $status]);
    }

    /**
     * Upload a file
     */
    protected function uploadFile(UploadedFile $file, string $path, string $prefix = ''): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . $prefix . '_' . Str::slug($originalName) . '.' . $extension;

        $destinationPath = public_path($path);

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);

        return $path . '/' . $filename;
    }

    /**
     * Delete a file
     */
    protected function deleteFile(string $path): bool
    {
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}