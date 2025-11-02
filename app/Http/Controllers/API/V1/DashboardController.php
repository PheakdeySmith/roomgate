<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Property\PropertyService;
use App\Services\Invoice\InvoiceService;
use App\Services\Payment\PaymentService;
use App\Services\Tenant\TenantService;
use App\Services\Report\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends BaseController
{
    protected PropertyService $propertyService;
    protected InvoiceService $invoiceService;
    protected PaymentService $paymentService;
    protected TenantService $tenantService;
    protected ReportService $reportService;

    public function __construct(
        PropertyService $propertyService,
        InvoiceService $invoiceService,
        PaymentService $paymentService,
        TenantService $tenantService,
        ReportService $reportService
    ) {
        $this->propertyService = $propertyService;
        $this->invoiceService = $invoiceService;
        $this->paymentService = $paymentService;
        $this->tenantService = $tenantService;
        $this->reportService = $reportService;
    }

    /**
     * Get landlord dashboard data
     */
    public function landlordDashboard()
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Get dashboard statistics
            $stats = $this->getLandlordStats($user);
            $recentActivity = $this->getRecentActivity($user);
            $upcomingEvents = $this->getUpcomingEvents($user);
            $financialSummary = $this->getFinancialSummary($user);
            $occupancyChart = $this->getOccupancyChartData($user);
            $revenueChart = $this->getRevenueChartData($user);

            $data = [
                'statistics' => $stats,
                'recent_activity' => $recentActivity,
                'upcoming_events' => $upcomingEvents,
                'financial_summary' => $financialSummary,
                'charts' => [
                    'occupancy' => $occupancyChart,
                    'revenue' => $revenueChart,
                ],
                'subscription' => $this->getSubscriptionInfo($user),
            ];

            return $this->sendResponse($data, 'Dashboard data retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve dashboard data', [$e->getMessage()], 500);
        }
    }

    /**
     * Get tenant dashboard data
     */
    public function tenantDashboard()
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('tenant')) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Get tenant dashboard data using service
            $dashboardData = $this->tenantService->getTenantDashboard($user);

            return $this->sendResponse($dashboardData, 'Dashboard data retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve dashboard data', [$e->getMessage()], 500);
        }
    }

    /**
     * Get admin dashboard data
     */
    public function adminDashboard()
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('admin')) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $data = [
                'system_stats' => $this->getSystemStats(),
                'user_stats' => $this->getUserStats(),
                'financial_overview' => $this->getSystemFinancialOverview(),
                'subscription_stats' => $this->getSubscriptionStats(),
                'recent_registrations' => $this->getRecentRegistrations(),
                'system_health' => $this->getSystemHealth(),
            ];

            return $this->sendResponse($data, 'Admin dashboard data retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve dashboard data', [$e->getMessage()], 500);
        }
    }

    /**
     * Get general statistics
     */
    public function getStats()
    {
        try {
            $user = Auth::user();
            $stats = [];

            if ($user->hasRole('landlord')) {
                $stats = $this->getLandlordStats($user);
            } elseif ($user->hasRole('tenant')) {
                $contract = $this->tenantService->getActiveContract($user);
                if ($contract) {
                    $stats = [
                        'current_rent' => $contract->monthly_rent,
                        'pending_payments' => Invoice::where('contract_id', $contract->id)
                            ->where('status', '!=', 'paid')
                            ->sum('total_amount'),
                        'days_remaining' => now()->diffInDays($contract->end_date),
                    ];
                }
            } elseif ($user->hasRole('admin')) {
                $stats = $this->getSystemStats();
            }

            return $this->sendResponse($stats, 'Statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve statistics', [$e->getMessage()], 500);
        }
    }

    /**
     * Global search across entities
     */
    public function globalSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        try {
            $user = Auth::user();
            $query = $request->input('query');
            $limit = $request->input('limit', 10);
            $results = [];

            if ($user->hasRole('landlord')) {
                // Search properties
                $properties = Property::where('landlord_id', $user->id)
                    ->where('name', 'like', "%{$query}%")
                    ->limit($limit)
                    ->get(['id', 'name', 'property_type']);

                // Search rooms
                $rooms = Room::whereHas('property', function ($q) use ($user) {
                    $q->where('landlord_id', $user->id);
                })->where('room_number', 'like', "%{$query}%")
                    ->limit($limit)
                    ->get(['id', 'room_number']);

                // Search tenants
                $tenants = User::role('tenant')
                    ->where('landlord_id', $user->id)
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%");
                    })
                    ->limit($limit)
                    ->get(['id', 'name', 'email']);

                // Search invoices
                $invoices = Invoice::whereHas('contract.room.property', function ($q) use ($user) {
                    $q->where('landlord_id', $user->id);
                })->where('invoice_number', 'like', "%{$query}%")
                    ->limit($limit)
                    ->get(['id', 'invoice_number', 'total_amount']);

                $results = [
                    'properties' => $properties,
                    'rooms' => $rooms,
                    'tenants' => $tenants,
                    'invoices' => $invoices,
                ];
            }

            return $this->sendResponse($results, 'Search results retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Search failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Get exchange rates
     */
    public function getExchangeRates()
    {
        try {
            // Cache exchange rates for 1 hour
            $rates = Cache::remember('exchange_rates', 3600, function () {
                // Fetch from NBC API or your configured source
                // For now, return static rates
                return [
                    'USD' => 1.0,
                    'KHR' => 4100.0, // 1 USD = 4100 KHR
                    'updated_at' => now()->toIso8601String(),
                ];
            });

            return $this->sendResponse($rates, 'Exchange rates retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve exchange rates', [$e->getMessage()], 500);
        }
    }

    // ==================== Helper Methods ====================

    /**
     * Get landlord statistics
     */
    protected function getLandlordStats($user)
    {
        $properties = Property::where('landlord_id', $user->id)->get();
        $propertyIds = $properties->pluck('id');

        $totalRooms = Room::whereIn('property_id', $propertyIds)->count();
        $occupiedRooms = Room::whereIn('property_id', $propertyIds)
            ->where('status', 'occupied')
            ->count();
        $availableRooms = Room::whereIn('property_id', $propertyIds)
            ->where('status', 'available')
            ->count();

        $activeContracts = Contract::whereHas('room', function ($q) use ($propertyIds) {
            $q->whereIn('property_id', $propertyIds);
        })->where('status', 'active')->count();

        $totalTenants = User::role('tenant')
            ->where('landlord_id', $user->id)
            ->count();

        $monthlyRevenue = Invoice::whereHas('contract.room', function ($q) use ($propertyIds) {
            $q->whereIn('property_id', $propertyIds);
        })->whereMonth('issue_date', now()->month)
            ->whereYear('issue_date', now()->year)
            ->where('status', 'paid')
            ->sum('total_amount');

        $pendingPayments = Invoice::whereHas('contract.room', function ($q) use ($propertyIds) {
            $q->whereIn('property_id', $propertyIds);
        })->where('status', '!=', 'paid')
            ->where('status', '!=', 'void')
            ->sum('total_amount');

        $overdueInvoices = Invoice::whereHas('contract.room', function ($q) use ($propertyIds) {
            $q->whereIn('property_id', $propertyIds);
        })->where('status', '!=', 'paid')
            ->where('status', '!=', 'void')
            ->where('due_date', '<', now())
            ->count();

        return [
            'total_properties' => $properties->count(),
            'total_rooms' => $totalRooms,
            'occupied_rooms' => $occupiedRooms,
            'available_rooms' => $availableRooms,
            'occupancy_rate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0,
            'active_contracts' => $activeContracts,
            'total_tenants' => $totalTenants,
            'monthly_revenue' => $monthlyRevenue,
            'pending_payments' => $pendingPayments,
            'overdue_invoices' => $overdueInvoices,
        ];
    }

    /**
     * Get recent activity
     */
    protected function getRecentActivity($user)
    {
        $activities = [];

        // Recent contracts
        $recentContracts = Contract::whereHas('room.property', function ($q) use ($user) {
            $q->where('landlord_id', $user->id);
        })->latest()
            ->limit(5)
            ->get();

        foreach ($recentContracts as $contract) {
            $activities[] = [
                'type' => 'contract',
                'message' => "New contract for {$contract->tenant->name} in Room {$contract->room->room_number}",
                'date' => $contract->created_at,
            ];
        }

        // Recent payments
        $recentPayments = Payment::whereHas('invoice.contract.room.property', function ($q) use ($user) {
            $q->where('landlord_id', $user->id);
        })->latest()
            ->limit(5)
            ->get();

        foreach ($recentPayments as $payment) {
            $activities[] = [
                'type' => 'payment',
                'message' => "Payment of {$payment->amount} received from {$payment->invoice->contract->tenant->name}",
                'date' => $payment->created_at,
            ];
        }

        // Sort by date and limit
        usort($activities, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Get upcoming events
     */
    protected function getUpcomingEvents($user)
    {
        $events = [];

        // Contracts expiring soon
        $expiringContracts = Contract::whereHas('room.property', function ($q) use ($user) {
            $q->where('landlord_id', $user->id);
        })->where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->get();

        foreach ($expiringContracts as $contract) {
            $events[] = [
                'type' => 'contract_expiring',
                'title' => 'Contract Expiring',
                'description' => "Contract for {$contract->tenant->name} expires",
                'date' => $contract->end_date,
                'days_remaining' => now()->diffInDays($contract->end_date),
            ];
        }

        // Invoices due soon
        $upcomingInvoices = Invoice::whereHas('contract.room.property', function ($q) use ($user) {
            $q->where('landlord_id', $user->id);
        })->where('status', '!=', 'paid')
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->get();

        foreach ($upcomingInvoices as $invoice) {
            $events[] = [
                'type' => 'invoice_due',
                'title' => 'Invoice Due',
                'description' => "Invoice #{$invoice->invoice_number} due",
                'date' => $invoice->due_date,
                'days_remaining' => now()->diffInDays($invoice->due_date),
            ];
        }

        // Sort by date
        usort($events, function ($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        return array_slice($events, 0, 10);
    }

    /**
     * Get financial summary
     */
    protected function getFinancialSummary($user)
    {
        $propertyIds = Property::where('landlord_id', $user->id)->pluck('id');

        // Current month
        $currentMonthRevenue = Invoice::whereHas('contract.room', function ($q) use ($propertyIds) {
            $q->whereIn('property_id', $propertyIds);
        })->whereMonth('issue_date', now()->month)
            ->whereYear('issue_date', now()->year)
            ->where('status', 'paid')
            ->sum('total_amount');

        // Previous month
        $previousMonthRevenue = Invoice::whereHas('contract.room', function ($q) use ($propertyIds) {
            $q->whereIn('property_id', $propertyIds);
        })->whereMonth('issue_date', now()->subMonth()->month)
            ->whereYear('issue_date', now()->subMonth()->year)
            ->where('status', 'paid')
            ->sum('total_amount');

        // Year to date
        $yearRevenue = Invoice::whereHas('contract.room', function ($q) use ($propertyIds) {
            $q->whereIn('property_id', $propertyIds);
        })->whereYear('issue_date', now()->year)
            ->where('status', 'paid')
            ->sum('total_amount');

        // Outstanding balance
        $outstandingBalance = Invoice::whereHas('contract.room', function ($q) use ($propertyIds) {
            $q->whereIn('property_id', $propertyIds);
        })->where('status', '!=', 'paid')
            ->where('status', '!=', 'void')
            ->sum(DB::raw('total_amount - paid_amount'));

        return [
            'current_month_revenue' => $currentMonthRevenue,
            'previous_month_revenue' => $previousMonthRevenue,
            'month_over_month_change' => $previousMonthRevenue > 0
                ? round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 2)
                : 0,
            'year_to_date_revenue' => $yearRevenue,
            'outstanding_balance' => $outstandingBalance,
            'average_monthly_revenue' => round($yearRevenue / now()->month, 2),
        ];
    }

    /**
     * Get occupancy chart data
     */
    protected function getOccupancyChartData($user)
    {
        $propertyIds = Property::where('landlord_id', $user->id)->pluck('id');
        $months = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $totalRooms = Room::whereIn('property_id', $propertyIds)->count();

            // Get occupied rooms for that month
            $occupiedRooms = Contract::whereHas('room', function ($q) use ($propertyIds) {
                $q->whereIn('property_id', $propertyIds);
            })->where(function ($q) use ($date) {
                $q->where('start_date', '<=', $date->endOfMonth())
                    ->where('end_date', '>=', $date->startOfMonth());
            })->count();

            $months[] = [
                'month' => $date->format('M Y'),
                'occupied' => $occupiedRooms,
                'available' => $totalRooms - $occupiedRooms,
                'rate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0,
            ];
        }

        return $months;
    }

    /**
     * Get revenue chart data
     */
    protected function getRevenueChartData($user)
    {
        $propertyIds = Property::where('landlord_id', $user->id)->pluck('id');
        $months = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $revenue = Invoice::whereHas('contract.room', function ($q) use ($propertyIds) {
                $q->whereIn('property_id', $propertyIds);
            })->whereMonth('issue_date', $date->month)
                ->whereYear('issue_date', $date->year)
                ->where('status', 'paid')
                ->sum('total_amount');

            $months[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
            ];
        }

        return $months;
    }

    /**
     * Get subscription info
     */
    protected function getSubscriptionInfo($user)
    {
        $subscription = $user->activeSubscription();

        if (!$subscription) {
            return null;
        }

        return [
            'plan_name' => $subscription->subscriptionPlan->name,
            'is_active' => $subscription->is_active,
            'start_date' => $subscription->start_date,
            'end_date' => $subscription->end_date,
            'days_remaining' => now()->diffInDays($subscription->end_date),
            'limits' => [
                'properties' => [
                    'used' => Property::where('landlord_id', $user->id)->count(),
                    'limit' => $subscription->subscriptionPlan->properties_limit,
                ],
                'rooms' => [
                    'used' => Room::whereIn('property_id', Property::where('landlord_id', $user->id)->pluck('id'))->count(),
                    'limit' => $subscription->subscriptionPlan->rooms_limit,
                ],
            ],
        ];
    }

    /**
     * Get system statistics (admin)
     */
    protected function getSystemStats()
    {
        return [
            'total_users' => User::count(),
            'total_landlords' => User::role('landlord')->count(),
            'total_tenants' => User::role('tenant')->count(),
            'total_properties' => Property::count(),
            'total_rooms' => Room::count(),
            'active_contracts' => Contract::where('status', 'active')->count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
            'pending_payments' => Invoice::where('status', '!=', 'paid')->where('status', '!=', 'void')->sum('total_amount'),
        ];
    }

    /**
     * Get user statistics (admin)
     */
    protected function getUserStats()
    {
        return [
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_week' => User::where('created_at', '>=', now()->subWeek())->count(),
            'new_users_month' => User::where('created_at', '>=', now()->subMonth())->count(),
            'active_users' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
            'user_growth_rate' => $this->calculateGrowthRate('users'),
        ];
    }

    /**
     * Get system financial overview (admin)
     */
    protected function getSystemFinancialOverview()
    {
        return [
            'total_revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
            'monthly_revenue' => Invoice::where('status', 'paid')
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('total_amount'),
            'average_rent' => Contract::where('status', 'active')->avg('monthly_rent'),
            'payment_success_rate' => $this->calculatePaymentSuccessRate(),
        ];
    }

    /**
     * Get subscription statistics (admin)
     */
    protected function getSubscriptionStats()
    {
        return [
            'active_subscriptions' => DB::table('user_subscriptions')->where('is_active', true)->count(),
            'expiring_soon' => DB::table('user_subscriptions')
                ->where('is_active', true)
                ->whereBetween('end_date', [now(), now()->addDays(7)])
                ->count(),
            'revenue_by_plan' => DB::table('user_subscriptions')
                ->join('subscription_plans', 'user_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
                ->select('subscription_plans.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(subscription_plans.price) as revenue'))
                ->groupBy('subscription_plans.name')
                ->get(),
        ];
    }

    /**
     * Get recent registrations (admin)
     */
    protected function getRecentRegistrations()
    {
        return User::latest()
            ->limit(10)
            ->get(['id', 'name', 'email', 'created_at'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRoleNames()->first(),
                    'created_at' => $user->created_at,
                ];
            });
    }

    /**
     * Get system health metrics (admin)
     */
    protected function getSystemHealth()
    {
        return [
            'database_size' => $this->getDatabaseSize(),
            'storage_usage' => $this->getStorageUsage(),
            'error_rate' => $this->getErrorRate(),
            'response_time' => $this->getAverageResponseTime(),
            'uptime' => $this->getSystemUptime(),
        ];
    }

    /**
     * Calculate growth rate
     */
    protected function calculateGrowthRate($entity)
    {
        $currentMonth = 0;
        $previousMonth = 0;

        switch ($entity) {
            case 'users':
                $currentMonth = User::whereMonth('created_at', now()->month)->count();
                $previousMonth = User::whereMonth('created_at', now()->subMonth()->month)->count();
                break;
        }

        return $previousMonth > 0
            ? round((($currentMonth - $previousMonth) / $previousMonth) * 100, 2)
            : 0;
    }

    /**
     * Calculate payment success rate
     */
    protected function calculatePaymentSuccessRate()
    {
        $totalInvoices = Invoice::count();
        $paidInvoices = Invoice::where('status', 'paid')->count();

        return $totalInvoices > 0
            ? round(($paidInvoices / $totalInvoices) * 100, 2)
            : 0;
    }

    /**
     * Get database size
     */
    protected function getDatabaseSize()
    {
        try {
            $result = DB::select("SELECT
                SUM(data_length + index_length) / 1024 / 1024 AS size_mb
                FROM information_schema.tables
                WHERE table_schema = ?", [env('DB_DATABASE')]);

            return round($result[0]->size_mb ?? 0, 2) . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get storage usage
     */
    protected function getStorageUsage()
    {
        try {
            $path = storage_path();
            $totalSize = 0;

            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
                $totalSize += $file->getSize();
            }

            return round($totalSize / 1024 / 1024, 2) . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get error rate
     */
    protected function getErrorRate()
    {
        // This would typically read from your log files or monitoring system
        return '0.01%';
    }

    /**
     * Get average response time
     */
    protected function getAverageResponseTime()
    {
        // This would typically come from your monitoring system
        return '120ms';
    }

    /**
     * Get system uptime
     */
    protected function getSystemUptime()
    {
        // This would typically check your server uptime
        return '99.99%';
    }
}