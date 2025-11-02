<?php

namespace App\Services\Property;

use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\BasePrice;
use App\Models\Contract;
use App\Models\UtilityRate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PropertyService
{
    /**
     * Create a new property
     */
    public function createProperty(array $data, $landlord): Property
    {
        return DB::transaction(function () use ($data, $landlord) {
            // Handle cover image upload
            $coverImagePath = null;
            if (isset($data['cover_image']) && $data['cover_image'] instanceof UploadedFile) {
                $coverImagePath = $this->uploadFile(
                    $data['cover_image'],
                    'uploads/properties',
                    'property'
                );
            }

            // Create property
            $property = Property::create([
                'landlord_id' => $landlord->id,
                'name' => $data['name'],
                'address' => $data['address'],
                'property_type' => $data['property_type'] ?? 'residential',
                'year_built' => $data['year_built'] ?? null,
                'description' => $data['description'] ?? null,
                'cover_image' => $coverImagePath,
                'status' => $data['status'] ?? 'active',
                'amenities' => $data['amenities'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
            ]);

            // Set base prices if provided
            if (isset($data['room_types']) && is_array($data['room_types'])) {
                $this->setBasePrices($property, $data['room_types']);
            }

            // Set utility rates if provided
            if (isset($data['utility_rates']) && is_array($data['utility_rates'])) {
                $this->setUtilityRates($property, $data['utility_rates']);
            }

            return $property;
        });
    }

    /**
     * Update an existing property
     */
    public function updateProperty(Property $property, array $data): Property
    {
        return DB::transaction(function () use ($property, $data) {
            // Handle cover image upload
            if (isset($data['cover_image']) && $data['cover_image'] instanceof UploadedFile) {
                // Delete old image if exists
                if ($property->cover_image) {
                    $this->deleteFile($property->cover_image);
                }

                $data['cover_image'] = $this->uploadFile(
                    $data['cover_image'],
                    'uploads/properties',
                    'property'
                );
            }

            // Update property
            $property->update($data);

            // Update base prices if provided
            if (isset($data['room_types']) && is_array($data['room_types'])) {
                $this->setBasePrices($property, $data['room_types']);
            }

            // Update utility rates if provided
            if (isset($data['utility_rates']) && is_array($data['utility_rates'])) {
                $this->setUtilityRates($property, $data['utility_rates']);
            }

            return $property->fresh();
        });
    }

    /**
     * Delete a property
     */
    public function deleteProperty(Property $property): bool
    {
        // Check if property has any active contracts
        $hasActiveContracts = Contract::whereHas('room', function ($query) use ($property) {
            $query->where('property_id', $property->id);
        })->where('status', 'active')->exists();

        if ($hasActiveContracts) {
            throw new \Exception('Cannot delete property with active contracts.');
        }

        return DB::transaction(function () use ($property) {
            // Delete cover image if exists
            if ($property->cover_image) {
                $this->deleteFile($property->cover_image);
            }

            // Delete related data
            $property->rooms()->delete();
            $property->utilityRates()->delete();
            BasePrice::where('property_id', $property->id)->delete();

            // Delete property
            $property->delete();

            return true;
        });
    }

    /**
     * Get property statistics
     */
    public function getPropertyStats(Property $property): array
    {
        $totalRooms = $property->rooms()->count();
        $occupiedRooms = $property->rooms()->where('status', Room::STATUS_OCCUPIED)->count();
        $availableRooms = $property->rooms()->where('status', Room::STATUS_AVAILABLE)->count();
        $maintenanceRooms = $property->rooms()->where('status', Room::STATUS_MAINTENANCE)->count();

        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0;

        // Calculate revenue
        $activeContracts = Contract::whereHas('room', function ($query) use ($property) {
            $query->where('property_id', $property->id);
        })->where('status', 'active')->get();

        $monthlyRevenue = 0;
        foreach ($activeContracts as $contract) {
            $rentAmount = $contract->rent_amount;
            if (!$rentAmount) {
                $basePrice = BasePrice::where('property_id', $property->id)
                    ->where('room_type_id', $contract->room->room_type_id)
                    ->orderBy('effective_date', 'desc')
                    ->first();
                $rentAmount = $basePrice ? $basePrice->price : 0;
            }
            $monthlyRevenue += $rentAmount;
        }

        // Get tenant count
        $totalTenants = $activeContracts->unique('user_id')->count();

        return [
            'total_rooms' => $totalRooms,
            'occupied_rooms' => $occupiedRooms,
            'available_rooms' => $availableRooms,
            'maintenance_rooms' => $maintenanceRooms,
            'occupancy_rate' => $occupancyRate,
            'monthly_revenue' => $monthlyRevenue,
            'total_tenants' => $totalTenants,
            'active_contracts' => $activeContracts->count(),
        ];
    }

    /**
     * Get properties for a landlord with filters
     */
    public function getLandlordProperties($landlord, array $filters = [])
    {
        $query = Property::where('landlord_id', $landlord->id);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['property_type'])) {
            $query->where('property_type', $filters['property_type']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Include relationships
        if (isset($filters['with']) && is_array($filters['with'])) {
            $query->with($filters['with']);
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
     * Set base prices for room types in a property
     */
    public function setBasePrices(Property $property, array $roomTypePrices): void
    {
        foreach ($roomTypePrices as $roomTypeId => $price) {
            BasePrice::updateOrCreate(
                [
                    'property_id' => $property->id,
                    'room_type_id' => $roomTypeId,
                ],
                [
                    'price' => $price,
                    'effective_date' => now(),
                ]
            );
        }
    }

    /**
     * Set utility rates for a property
     */
    public function setUtilityRates(Property $property, array $utilityRates): void
    {
        foreach ($utilityRates as $utilityTypeId => $rate) {
            UtilityRate::updateOrCreate(
                [
                    'property_id' => $property->id,
                    'utility_type_id' => $utilityTypeId,
                ],
                [
                    'rate' => $rate,
                    'effective_date' => now(),
                ]
            );
        }
    }

    /**
     * Get occupancy trend for a property
     */
    public function getOccupancyTrend(Property $property, int $months = 12): array
    {
        $trend = [];
        $currentDate = now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $currentDate->copy()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            // Count occupied rooms during that month
            $occupiedCount = Contract::whereHas('room', function ($query) use ($property) {
                $query->where('property_id', $property->id);
            })
                ->where('start_date', '<=', $endOfMonth)
                ->where('end_date', '>=', $startOfMonth)
                ->where('status', 'active')
                ->distinct('room_id')
                ->count('room_id');

            $totalRooms = $property->rooms()->count();
            $occupancyRate = $totalRooms > 0 ? round(($occupiedCount / $totalRooms) * 100, 2) : 0;

            $trend[] = [
                'month' => $date->format('M Y'),
                'occupied' => $occupiedCount,
                'total' => $totalRooms,
                'rate' => $occupancyRate,
            ];
        }

        return $trend;
    }

    /**
     * Get revenue trend for a property
     */
    public function getRevenueTrend(Property $property, int $months = 12): array
    {
        $trend = [];
        $currentDate = now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $currentDate->copy()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            // Calculate revenue for that month
            $invoices = \App\Models\Invoice::whereHas('contract.room', function ($query) use ($property) {
                $query->where('property_id', $property->id);
            })
                ->whereBetween('issue_date', [$startOfMonth, $endOfMonth])
                ->get();

            $totalRevenue = $invoices->sum('total_amount');
            $paidRevenue = $invoices->where('status', 'paid')->sum('paid_amount');

            $trend[] = [
                'month' => $date->format('M Y'),
                'total' => $totalRevenue,
                'paid' => $paidRevenue,
                'unpaid' => $totalRevenue - $paidRevenue,
            ];
        }

        return $trend;
    }

    /**
     * Check if landlord can add more properties
     */
    public function canAddProperty($landlord): bool
    {
        $subscription = $landlord->activeSubscription;

        if (!$subscription) {
            return false;
        }

        $currentCount = Property::where('landlord_id', $landlord->id)->count();
        $limit = $subscription->plan->property_limit ?? 0;

        return $limit === 0 || $currentCount < $limit;
    }

    /**
     * Get property summary for dashboard
     */
    public function getPropertiesSummary($landlord): array
    {
        $properties = Property::where('landlord_id', $landlord->id)->get();

        $summary = [
            'total_properties' => $properties->count(),
            'total_rooms' => 0,
            'occupied_rooms' => 0,
            'available_rooms' => 0,
            'total_tenants' => 0,
            'monthly_revenue' => 0,
            'properties' => [],
        ];

        foreach ($properties as $property) {
            $stats = $this->getPropertyStats($property);
            $summary['total_rooms'] += $stats['total_rooms'];
            $summary['occupied_rooms'] += $stats['occupied_rooms'];
            $summary['available_rooms'] += $stats['available_rooms'];
            $summary['total_tenants'] += $stats['total_tenants'];
            $summary['monthly_revenue'] += $stats['monthly_revenue'];

            $summary['properties'][] = [
                'id' => $property->id,
                'name' => $property->name,
                'stats' => $stats,
            ];
        }

        $summary['occupancy_rate'] = $summary['total_rooms'] > 0 ?
            round(($summary['occupied_rooms'] / $summary['total_rooms']) * 100, 2) : 0;

        return $summary;
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