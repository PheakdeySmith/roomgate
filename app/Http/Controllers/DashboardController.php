<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the landlord's dashboard with all relevant statistics and data.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Redirect users to their role-specific dashboards
        if ($user->hasRole('tenant')) {
            return redirect()->route('tenant.dashboard');
        }
        
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        
        $landlord = $user;
        $now = Carbon::now();

            $invoicesQuery = Invoice::whereHas('contract.room.property', fn($q) => $q->where('landlord_id', $landlord->id));


        // --- Base Queries (to keep code DRY and scoped to the landlord) ---
        $invoicesQuery = Invoice::whereHas('contract.room.property', fn($q) => $q->where('landlord_id', $landlord->id));
        $roomsQuery = Room::whereHas('property', fn($q) => $q->where('landlord_id', $landlord->id));
        $contractsQuery = Contract::whereHas('room.property', fn($q) => $q->where('landlord_id', $landlord->id));

        // --- 1. KPI Card Calculations ---
        $revenueThisMonth = (clone $invoicesQuery)->whereYear('issue_date', $now->year)->whereMonth('issue_date', $now->month)->sum('total_amount');
        $revenueLastMonth = (clone $invoicesQuery)->whereYear('issue_date', $now->copy()->subMonth()->year)->whereMonth('issue_date', $now->copy()->subMonth()->month)->sum('total_amount');

        $paidThisMonth = (clone $invoicesQuery)->where('status', 'paid')->whereYear('updated_at', $now->year)->whereMonth('updated_at', $now->month)->sum('paid_amount');
        $paidLastMonth = (clone $invoicesQuery)->where('status', 'paid')->whereYear('updated_at', $now->copy()->subMonth()->year)->whereMonth('updated_at', $now->copy()->subMonth()->month)->sum('paid_amount');
        
        $overdueCount = (clone $invoicesQuery)->where('status', 'overdue')->count();
        $activeTenants = (clone $contractsQuery)->where('status', 'active')->count();


        $totalProperties = Property::where('landlord_id', $landlord->id)->count();

    $calculateChange = fn($current, $previous) => $previous > 0 ? (($current - $previous) / $previous) * 100 : ($current > 0 ? 100 : 0);

    $stats = [
        'revenue' => ['current' => $revenueThisMonth, 'change' => $calculateChange($revenueThisMonth, $revenueLastMonth)],
        'overdue_count' => $overdueCount,
        'active_tenants' => $activeTenants,
        'total_properties' => $totalProperties, // ✨ Added new stat
    ];

        // --- 2. Data for Overview Chart (Revenue vs. Paid Last 6 Months) ---
        $revenueData = (clone $invoicesQuery)
            ->select(
                DB::raw('SUM(total_amount) as total'),
                DB::raw("DATE_FORMAT(issue_date, '%Y-%m') as yearmonth"),
                DB::raw("DATE_FORMAT(issue_date, '%b') as monthname")
            )
            ->where('issue_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('yearmonth', 'monthname') // Use both for correctness
            ->orderBy('yearmonth', 'asc')
            ->get();

        $paidData = (clone $invoicesQuery)->where('status', 'paid')
            ->select(
                DB::raw('SUM(paid_amount) as total'),
                DB::raw("DATE_FORMAT(updated_at, '%Y-%m') as yearmonth"),
                DB::raw("DATE_FORMAT(updated_at, '%b') as monthname")
            )
            ->where('updated_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('yearmonth', 'monthname') // Use both for correctness
            ->orderBy('yearmonth', 'asc')
            ->get();

        // Ensure both collections have the same months for chart alignment
        $months = collect([]);
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('M'));
        }
        $revenueChart = $months->mapWithKeys(fn($month) => [$month => $revenueData->firstWhere('monthname', $month)->total ?? 0]);
        $paidChart = $months->mapWithKeys(fn($month) => [$month => $paidData->firstWhere('monthname', $month)->total ?? 0]);

        // --- 3. Data for Room Status Chart ---
        $roomStatusData = (clone $roomsQuery)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // --- 4. Data for Recent Invoices List ---
        $recentInvoices = (clone $invoicesQuery)
            ->with('contract.tenant')
            ->whereIn('status', ['overdue', 'sent'])
            ->latest('due_date')
            ->limit(5)
            ->get();
        
        // --- 5. Data for Property Overview List ---
        $properties = $landlord->properties()->withCount(['rooms', 'rooms as occupied_rooms_count' => function ($query) {
            $query->where('status', 'occupied');
        }])->get();
            
        return view('backends.dashboard.home.index', compact(
            'stats',
            'revenueChart',
            'paidChart',
            'roomStatusData',
            'recentInvoices',
            'properties'
        ));
    }
}