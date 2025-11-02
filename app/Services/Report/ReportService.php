<?php

namespace App\Services\Report;

use App\Models\Property;
use App\Models\Room;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\User;
use App\Models\UtilityBill;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Generate financial report for a landlord
     */
    public function generateFinancialReport($landlord, Carbon $startDate, Carbon $endDate): array
    {
        // Revenue Analysis
        $totalRevenue = $this->calculateRevenue($landlord, $startDate, $endDate);
        $paidRevenue = $this->calculatePaidRevenue($landlord, $startDate, $endDate);
        $unpaidRevenue = $totalRevenue - $paidRevenue;

        // Invoice Analysis
        $invoiceStats = $this->getInvoiceStatistics($landlord, $startDate, $endDate);

        // Utility Analysis
        $utilityStats = $this->getUtilityStatistics($landlord, $startDate, $endDate);

        // Monthly Breakdown
        $monthlyBreakdown = $this->getMonthlyBreakdown($landlord, $startDate, $endDate);

        // Payment Methods
        $paymentMethods = $this->getPaymentMethodsBreakdown($landlord, $startDate, $endDate);

        // Top Properties by Revenue
        $topProperties = $this->getTopPropertiesByRevenue($landlord, $startDate, $endDate);

        // Collection Rate
        $collectionRate = $totalRevenue > 0 ? round(($paidRevenue / $totalRevenue) * 100, 2) : 0;

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'revenue' => [
                'total' => $totalRevenue,
                'paid' => $paidRevenue,
                'unpaid' => $unpaidRevenue,
                'collection_rate' => $collectionRate,
            ],
            'invoices' => $invoiceStats,
            'utilities' => $utilityStats,
            'monthly_breakdown' => $monthlyBreakdown,
            'payment_methods' => $paymentMethods,
            'top_properties' => $topProperties,
        ];
    }

    /**
     * Generate occupancy report for a landlord
     */
    public function generateOccupancyReport($landlord): array
    {
        $properties = Property::where('landlord_id', $landlord->id)
            ->with(['rooms.contracts' => function ($query) {
                $query->where('status', 'active');
            }])
            ->get();

        $totalRooms = 0;
        $occupiedRooms = 0;
        $availableRooms = 0;
        $maintenanceRooms = 0;
        $propertyStats = [];

        foreach ($properties as $property) {
            $propertyTotal = $property->rooms->count();
            $propertyOccupied = $property->rooms->where('status', Room::STATUS_OCCUPIED)->count();
            $propertyAvailable = $property->rooms->where('status', Room::STATUS_AVAILABLE)->count();
            $propertyMaintenance = $property->rooms->where('status', Room::STATUS_MAINTENANCE)->count();

            $totalRooms += $propertyTotal;
            $occupiedRooms += $propertyOccupied;
            $availableRooms += $propertyAvailable;
            $maintenanceRooms += $propertyMaintenance;

            $propertyStats[] = [
                'id' => $property->id,
                'name' => $property->name,
                'total_rooms' => $propertyTotal,
                'occupied' => $propertyOccupied,
                'available' => $propertyAvailable,
                'maintenance' => $propertyMaintenance,
                'occupancy_rate' => $propertyTotal > 0 ? round(($propertyOccupied / $propertyTotal) * 100, 2) : 0,
            ];
        }

        $overallOccupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0;

        // Get occupancy trend
        $occupancyTrend = $this->getOccupancyTrend($landlord, 12);

        // Get upcoming contract expirations
        $expiringContracts = $this->getExpiringContracts($landlord, 30);

        return [
            'summary' => [
                'total_properties' => $properties->count(),
                'total_rooms' => $totalRooms,
                'occupied_rooms' => $occupiedRooms,
                'available_rooms' => $availableRooms,
                'maintenance_rooms' => $maintenanceRooms,
                'occupancy_rate' => $overallOccupancyRate,
            ],
            'properties' => $propertyStats,
            'trend' => $occupancyTrend,
            'expiring_contracts' => $expiringContracts,
        ];
    }

    /**
     * Generate tenant report
     */
    public function generateTenantReport($landlord): array
    {
        // Get all tenants
        $tenants = User::role('tenant')
            ->where('landlord_id', $landlord->id)
            ->with(['contracts.room.property', 'contracts.invoices'])
            ->get();

        $tenantStats = [];
        $totalTenants = $tenants->count();
        $activeTenants = 0;

        foreach ($tenants as $tenant) {
            $activeContract = $tenant->contracts->where('status', 'active')->first();
            if ($activeContract) {
                $activeTenants++;
            }

            $totalPaid = $tenant->contracts->flatMap->invoices->sum('paid_amount');
            $totalDue = $tenant->contracts->flatMap->invoices->sum('total_amount');
            $balance = $totalDue - $totalPaid;

            $tenantStats[] = [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'email' => $tenant->email,
                'phone' => $tenant->phone,
                'status' => $activeContract ? 'active' : 'inactive',
                'current_room' => $activeContract ? $activeContract->room->room_number : null,
                'current_property' => $activeContract ? $activeContract->room->property->name : null,
                'contract_start' => $activeContract ? $activeContract->start_date : null,
                'contract_end' => $activeContract ? $activeContract->end_date : null,
                'total_paid' => $totalPaid,
                'total_due' => $totalDue,
                'balance' => $balance,
                'payment_status' => $balance > 0 ? 'outstanding' : 'clear',
            ];
        }

        // Get tenant growth trend
        $tenantGrowth = $this->getTenantGrowthTrend($landlord, 12);

        // Get payment defaulters
        $defaulters = $this->getPaymentDefaulters($landlord);

        return [
            'summary' => [
                'total_tenants' => $totalTenants,
                'active_tenants' => $activeTenants,
                'inactive_tenants' => $totalTenants - $activeTenants,
                'tenants_with_balance' => collect($tenantStats)->where('balance', '>', 0)->count(),
            ],
            'tenants' => $tenantStats,
            'growth_trend' => $tenantGrowth,
            'payment_defaulters' => $defaulters,
        ];
    }

    /**
     * Generate utility usage report
     */
    public function generateUtilityReport($landlord, Carbon $startDate, Carbon $endDate): array
    {
        $utilityBills = UtilityBill::whereHas('contract.room.property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })
            ->whereBetween('billing_period_start', [$startDate, $endDate])
            ->with(['utilityType', 'contract.room.property'])
            ->get();

        // Group by utility type
        $byType = $utilityBills->groupBy('utility_type_id')->map(function ($bills) {
            $utilityType = $bills->first()->utilityType;
            return [
                'utility_type' => $utilityType->name,
                'unit' => $utilityType->unit,
                'total_consumption' => $bills->sum('consumption'),
                'total_amount' => $bills->sum('amount'),
                'average_rate' => $bills->avg('rate_applied'),
                'bill_count' => $bills->count(),
            ];
        });

        // Group by property
        $byProperty = $utilityBills->groupBy('contract.room.property_id')->map(function ($bills) {
            $property = $bills->first()->contract->room->property;
            return [
                'property_id' => $property->id,
                'property_name' => $property->name,
                'total_consumption' => $bills->sum('consumption'),
                'total_amount' => $bills->sum('amount'),
                'bill_count' => $bills->count(),
            ];
        });

        // Monthly trend
        $monthlyTrend = $this->getUtilityMonthlyTrend($landlord, $startDate, $endDate);

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_bills' => $utilityBills->count(),
                'total_amount' => $utilityBills->sum('amount'),
                'total_consumption' => $utilityBills->sum('consumption'),
            ],
            'by_type' => $byType->values(),
            'by_property' => $byProperty->values(),
            'monthly_trend' => $monthlyTrend,
        ];
    }

    /**
     * Calculate total revenue
     */
    protected function calculateRevenue($landlord, Carbon $startDate, Carbon $endDate): float
    {
        return Invoice::whereHas('contract.room.property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->sum('total_amount');
    }

    /**
     * Calculate paid revenue
     */
    protected function calculatePaidRevenue($landlord, Carbon $startDate, Carbon $endDate): float
    {
        return Invoice::whereHas('contract.room.property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->sum('paid_amount');
    }

    /**
     * Get invoice statistics
     */
    protected function getInvoiceStatistics($landlord, Carbon $startDate, Carbon $endDate): array
    {
        $invoices = Invoice::whereHas('contract.room.property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->get();

        return [
            'total' => $invoices->count(),
            'paid' => $invoices->where('status', 'paid')->count(),
            'sent' => $invoices->where('status', 'sent')->count(),
            'overdue' => $invoices->where('status', 'overdue')->count(),
            'void' => $invoices->where('status', 'void')->count(),
            'partial' => $invoices->where('status', 'partial')->count(),
            'draft' => $invoices->where('status', 'draft')->count(),
        ];
    }

    /**
     * Get utility statistics
     */
    protected function getUtilityStatistics($landlord, Carbon $startDate, Carbon $endDate): array
    {
        $utilityBills = UtilityBill::whereHas('contract.room.property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })
            ->whereBetween('billing_period_start', [$startDate, $endDate])
            ->get();

        return [
            'total_bills' => $utilityBills->count(),
            'total_amount' => $utilityBills->sum('amount'),
            'total_consumption' => $utilityBills->sum('consumption'),
            'average_amount' => $utilityBills->avg('amount'),
        ];
    }

    /**
     * Get monthly breakdown
     */
    protected function getMonthlyBreakdown($landlord, Carbon $startDate, Carbon $endDate): array
    {
        $breakdown = [];
        $current = $startDate->copy()->startOfMonth();

        while ($current <= $endDate) {
            $monthEnd = $current->copy()->endOfMonth();

            $monthRevenue = $this->calculateRevenue($landlord, $current, $monthEnd);
            $monthPaid = $this->calculatePaidRevenue($landlord, $current, $monthEnd);

            $breakdown[] = [
                'month' => $current->format('M Y'),
                'revenue' => $monthRevenue,
                'paid' => $monthPaid,
                'unpaid' => $monthRevenue - $monthPaid,
            ];

            $current->addMonth();
        }

        return $breakdown;
    }

    /**
     * Get payment methods breakdown
     */
    protected function getPaymentMethodsBreakdown($landlord, Carbon $startDate, Carbon $endDate): array
    {
        return Invoice::whereHas('contract.room.property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(paid_amount) as total')
            ->get()
            ->toArray();
    }

    /**
     * Get top properties by revenue
     */
    protected function getTopPropertiesByRevenue($landlord, Carbon $startDate, Carbon $endDate, int $limit = 5): array
    {
        return DB::table('invoices')
            ->join('contracts', 'invoices.contract_id', '=', 'contracts.id')
            ->join('rooms', 'contracts.room_id', '=', 'rooms.id')
            ->join('properties', 'rooms.property_id', '=', 'properties.id')
            ->where('properties.landlord_id', $landlord->id)
            ->whereBetween('invoices.issue_date', [$startDate, $endDate])
            ->groupBy('properties.id', 'properties.name')
            ->selectRaw('properties.id, properties.name, SUM(invoices.total_amount) as total_revenue')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get occupancy trend
     */
    protected function getOccupancyTrend($landlord, int $months): array
    {
        $trend = [];
        $currentDate = now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $currentDate->copy()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $totalRooms = Room::whereHas('property', function ($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })->count();

            $occupiedRooms = Contract::whereHas('room.property', function ($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })
                ->where('start_date', '<=', $endOfMonth)
                ->where('end_date', '>=', $startOfMonth)
                ->where('status', 'active')
                ->distinct('room_id')
                ->count('room_id');

            $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0;

            $trend[] = [
                'month' => $date->format('M Y'),
                'occupied' => $occupiedRooms,
                'total' => $totalRooms,
                'rate' => $occupancyRate,
            ];
        }

        return $trend;
    }

    /**
     * Get expiring contracts
     */
    protected function getExpiringContracts($landlord, int $daysAhead): array
    {
        $expiryDate = now()->addDays($daysAhead);

        return Contract::whereHas('room.property', function ($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })
            ->where('status', 'active')
            ->where('end_date', '<=', $expiryDate)
            ->where('end_date', '>=', now())
            ->with(['tenant', 'room.property'])
            ->get()
            ->map(function ($contract) {
                return [
                    'contract_id' => $contract->id,
                    'tenant_name' => $contract->tenant->name,
                    'room' => $contract->room->room_number,
                    'property' => $contract->room->property->name,
                    'end_date' => $contract->end_date->format('Y-m-d'),
                    'days_remaining' => now()->diffInDays($contract->end_date),
                ];
            })
            ->toArray();
    }

    /**
     * Get tenant growth trend
     */
    protected function getTenantGrowthTrend($landlord, int $months): array
    {
        $trend = [];
        $currentDate = now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $currentDate->copy()->subMonths($i);
            $endOfMonth = $date->copy()->endOfMonth();

            $tenantCount = User::role('tenant')
                ->where('landlord_id', $landlord->id)
                ->where('created_at', '<=', $endOfMonth)
                ->count();

            $trend[] = [
                'month' => $date->format('M Y'),
                'count' => $tenantCount,
            ];
        }

        return $trend;
    }

    /**
     * Get payment defaulters
     */
    protected function getPaymentDefaulters($landlord, int $limit = 10): array
    {
        return DB::table('users')
            ->join('contracts', 'users.id', '=', 'contracts.user_id')
            ->join('invoices', 'contracts.id', '=', 'invoices.contract_id')
            ->join('rooms', 'contracts.room_id', '=', 'rooms.id')
            ->join('properties', 'rooms.property_id', '=', 'properties.id')
            ->where('properties.landlord_id', $landlord->id)
            ->where('invoices.status', '!=', 'paid')
            ->where('invoices.status', '!=', 'void')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->selectRaw('
                users.id,
                users.name,
                users.email,
                SUM(invoices.total_amount - invoices.paid_amount) as total_outstanding,
                COUNT(invoices.id) as unpaid_invoices
            ')
            ->having('total_outstanding', '>', 0)
            ->orderBy('total_outstanding', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get utility monthly trend
     */
    protected function getUtilityMonthlyTrend($landlord, Carbon $startDate, Carbon $endDate): array
    {
        $trend = [];
        $current = $startDate->copy()->startOfMonth();

        while ($current <= $endDate) {
            $monthEnd = $current->copy()->endOfMonth();

            $monthBills = UtilityBill::whereHas('contract.room.property', function ($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })
                ->whereBetween('billing_period_start', [$current, $monthEnd])
                ->get();

            $trend[] = [
                'month' => $current->format('M Y'),
                'consumption' => $monthBills->sum('consumption'),
                'amount' => $monthBills->sum('amount'),
                'bills' => $monthBills->count(),
            ];

            $current->addMonth();
        }

        return $trend;
    }

    /**
     * Export report to array (for CSV/Excel)
     */
    public function exportReport(array $reportData, string $format = 'array'): mixed
    {
        // This method can be extended to support different export formats
        // For now, it just returns the array data
        return $reportData;
    }
}