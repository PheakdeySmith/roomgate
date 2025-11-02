<?php

namespace App\Services\Room;

use App\Models\Room;
use App\Models\Property;
use App\Models\Contract;
use App\Models\Meter;
use App\Models\BasePrice;
use App\Models\PriceOverride;
use App\Models\Amenity;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoomService
{
    // Room status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_RESERVED = 'reserved';

    /**
     * Create a new room
     */
    public function createRoom(array $data): Room
    {
        return DB::transaction(function () use ($data) {
            // Create room
            $room = Room::create([
                'property_id' => $data['property_id'],
                'room_type_id' => $data['room_type_id'],
                'room_number' => $data['room_number'],
                'floor' => $data['floor'] ?? null,
                'size' => $data['size'] ?? null,
                'max_occupants' => $data['max_occupants'] ?? 1,
                'status' => $data['status'] ?? self::STATUS_AVAILABLE,
                'description' => $data['description'] ?? null,
                'features' => $data['features'] ?? null,
            ]);

            // Attach amenities if provided
            if (isset($data['amenities']) && is_array($data['amenities'])) {
                $this->attachAmenities($room, $data['amenities']);
            }

            // Create meters if provided
            if (isset($data['meters']) && is_array($data['meters'])) {
                $this->createMeters($room, $data['meters']);
            }

            return $room;
        });
    }

    /**
     * Update an existing room
     */
    public function updateRoom(Room $room, array $data): Room
    {
        return DB::transaction(function () use ($room, $data) {
            // Check if room can change status
            if (isset($data['status']) && $data['status'] !== $room->status) {
                $this->validateStatusChange($room, $data['status']);
            }

            // Update room
            $room->update($data);

            // Update amenities if provided
            if (isset($data['amenities'])) {
                $this->attachAmenities($room, $data['amenities']);
            }

            return $room->fresh();
        });
    }

    /**
     * Delete a room
     */
    public function deleteRoom(Room $room): bool
    {
        // Check if room has any contracts
        if ($room->contracts()->exists()) {
            throw new \Exception('Cannot delete room with existing contracts.');
        }

        return DB::transaction(function () use ($room) {
            // Delete related data
            $room->amenities()->detach();
            $room->meters()->delete();
            PriceOverride::where('room_id', $room->id)->delete();

            // Delete room
            $room->delete();

            return true;
        });
    }

    /**
     * Get room availability for a date range
     */
    public function checkAvailability(Room $room, Carbon $startDate, Carbon $endDate): bool
    {
        if ($room->status !== self::STATUS_AVAILABLE) {
            return false;
        }

        // Check for active contracts in the date range
        $hasConflict = Contract::where('room_id', $room->id)
            ->where('status', 'active')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        return !$hasConflict;
    }

    /**
     * Get available rooms for a property
     */
    public function getAvailableRooms(Property $property, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = Room::where('property_id', $property->id)
            ->where('status', self::STATUS_AVAILABLE);

        $rooms = $query->get();

        // If date range provided, filter by availability
        if ($startDate && $endDate) {
            $rooms = $rooms->filter(function ($room) use ($startDate, $endDate) {
                return $this->checkAvailability($room, $startDate, $endDate);
            });
        }

        return $rooms;
    }

    /**
     * Get available rooms for a landlord across all properties
     */
    public function getLandlordAvailableRooms($landlord): Collection
    {
        $propertyIds = Property::where('landlord_id', $landlord->id)->pluck('id');

        return Room::whereIn('property_id', $propertyIds)
            ->where('status', self::STATUS_AVAILABLE)
            ->whereDoesntHave('contracts', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['property', 'roomType', 'amenities'])
            ->get();
    }

    /**
     * Get all rooms for a landlord
     */
    public function getLandlordRooms($landlord): Collection
    {
        $propertyIds = Property::where('landlord_id', $landlord->id)->pluck('id');

        return Room::whereIn('property_id', $propertyIds)
            ->with(['property', 'roomType', 'amenities', 'contracts' => function ($query) {
                $query->where('status', 'active')->with('tenant');
            }])
            ->get();
    }

    /**
     * Get rooms for a specific property
     */
    public function getPropertyRooms(Property $property): Collection
    {
        return Room::where('property_id', $property->id)
            ->with(['roomType', 'amenities', 'contracts' => function ($query) {
                $query->where('status', 'active')->with('tenant');
            }])
            ->get();
    }

    /**
     * Update room status
     */
    public function updateRoomStatus(Room $room, string $newStatus): Room
    {
        $this->validateStatusChange($room, $newStatus);

        $room->update(['status' => $newStatus]);

        // Log status change
        Log::info("Room {$room->id} status changed from {$room->status} to {$newStatus}");

        return $room;
    }

    /**
     * Validate room status change
     */
    protected function validateStatusChange(Room $room, string $newStatus): void
    {
        // Can't make occupied room available if it has active contract
        if ($room->status === self::STATUS_OCCUPIED && $newStatus === self::STATUS_AVAILABLE) {
            $hasActiveContract = Contract::where('room_id', $room->id)
                ->where('status', 'active')
                ->exists();

            if ($hasActiveContract) {
                throw new \Exception('Cannot make room available while it has an active contract.');
            }
        }

        // Can't occupy room if it's not available
        if ($newStatus === self::STATUS_OCCUPIED && $room->status !== self::STATUS_AVAILABLE) {
            throw new \Exception('Can only occupy a room that is currently available.');
        }
    }

    /**
     * Attach amenities to a room
     */
    public function attachAmenities(Room $room, array $amenityIds): void
    {
        $room->amenities()->sync($amenityIds);
    }

    /**
     * Create meters for a room
     */
    public function createMeters(Room $room, array $metersData): Collection
    {
        $meters = collect();

        foreach ($metersData as $meterData) {
            $meter = Meter::create([
                'room_id' => $room->id,
                'utility_type_id' => $meterData['utility_type_id'],
                'meter_number' => $meterData['meter_number'],
                'initial_reading' => $meterData['initial_reading'] ?? 0,
                'status' => $meterData['status'] ?? 'active',
                'installation_date' => $meterData['installation_date'] ?? now(),
            ]);

            $meters->push($meter);
        }

        return $meters;
    }

    /**
     * Get room price for a specific date
     */
    public function getRoomPrice(Room $room, Carbon $date): float
    {
        // Check for price override first
        $override = PriceOverride::where('room_id', $room->id)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        if ($override) {
            return $override->price;
        }

        // Get base price
        $basePrice = BasePrice::where('property_id', $room->property_id)
            ->where('room_type_id', $room->room_type_id)
            ->where('effective_date', '<=', $date)
            ->orderBy('effective_date', 'desc')
            ->first();

        return $basePrice ? $basePrice->price : 0;
    }

    /**
     * Set price override for a room
     */
    public function setPriceOverride(Room $room, float $price, Carbon $startDate, Carbon $endDate): PriceOverride
    {
        return PriceOverride::create([
            'room_id' => $room->id,
            'price' => $price,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => 'Manual override',
        ]);
    }

    /**
     * Get room statistics
     */
    public function getRoomStats(Room $room): array
    {
        $activeContract = $room->contracts()
            ->where('status', 'active')
            ->first();

        $totalRevenue = 0;
        $occupancyDays = 0;
        $totalDays = 365; // Last year

        // Calculate occupancy for last year
        $oneYearAgo = now()->subYear();
        $contracts = $room->contracts()
            ->where('start_date', '>=', $oneYearAgo)
            ->get();

        foreach ($contracts as $contract) {
            $start = Carbon::parse($contract->start_date)->max($oneYearAgo);
            $end = Carbon::parse($contract->end_date)->min(now());
            $occupancyDays += $start->diffInDays($end);

            // Calculate revenue
            $months = $start->diffInMonths($end);
            $rentAmount = $contract->rent_amount ?? $this->getRoomPrice($room, $start);
            $totalRevenue += $rentAmount * $months;
        }

        $occupancyRate = round(($occupancyDays / $totalDays) * 100, 2);

        return [
            'current_status' => $room->status,
            'has_active_contract' => $activeContract !== null,
            'current_tenant' => $activeContract ? $activeContract->tenant : null,
            'occupancy_rate' => $occupancyRate,
            'total_revenue' => $totalRevenue,
            'occupancy_days' => $occupancyDays,
            'meter_count' => $room->meters()->count(),
            'active_meters' => $room->meters()->where('status', 'active')->count(),
            'amenities_count' => $room->amenities()->count(),
        ];
    }

    /**
     * Get rooms by property with filters
     */
    public function getRoomsByProperty(Property $property, array $filters = []): Collection
    {
        $query = Room::where('property_id', $property->id);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['room_type_id'])) {
            $query->where('room_type_id', $filters['room_type_id']);
        }

        if (isset($filters['floor'])) {
            $query->where('floor', $filters['floor']);
        }

        if (isset($filters['min_size'])) {
            $query->where('size', '>=', $filters['min_size']);
        }

        if (isset($filters['max_size'])) {
            $query->where('size', '<=', $filters['max_size']);
        }

        // Include relationships
        if (isset($filters['with']) && is_array($filters['with'])) {
            $query->with($filters['with']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'room_number';
        $sortDir = $filters['sort_dir'] ?? 'asc';
        $query->orderBy($sortBy, $sortDir);

        return $query->get();
    }

    /**
     * Check if landlord can add more rooms
     */
    public function canAddRoom($landlord): bool
    {
        $subscription = $landlord->activeSubscription;

        if (!$subscription) {
            return false;
        }

        $currentCount = Room::whereHas('property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })->count();

        $limit = $subscription->plan->room_limit ?? 0;

        return $limit === 0 || $currentCount < $limit;
    }

    /**
     * Bulk update room status
     */
    public function bulkUpdateStatus(array $roomIds, string $status): int
    {
        $updated = 0;

        foreach ($roomIds as $roomId) {
            try {
                $room = Room::find($roomId);
                if ($room) {
                    $this->updateRoomStatus($room, $status);
                    $updated++;
                }
            } catch (\Exception $e) {
                Log::warning("Failed to update room {$roomId} status: " . $e->getMessage());
            }
        }

        return $updated;
    }

    /**
     * Get room occupancy calendar
     */
    public function getOccupancyCalendar(Room $room, int $months = 3): array
    {
        $calendar = [];
        $startDate = now()->startOfMonth();
        $endDate = now()->addMonths($months)->endOfMonth();

        $contracts = Contract::where('room_id', $room->id)
            ->where('end_date', '>=', $startDate)
            ->where('start_date', '<=', $endDate)
            ->get();

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $isOccupied = false;

            foreach ($contracts as $contract) {
                if ($date >= $contract->start_date && $date <= $contract->end_date) {
                    $isOccupied = true;
                    break;
                }
            }

            $calendar[$date->format('Y-m-d')] = [
                'date' => $date->format('Y-m-d'),
                'is_occupied' => $isOccupied,
                'is_weekend' => $date->isWeekend(),
                'is_today' => $date->isToday(),
            ];
        }

        return $calendar;
    }
}