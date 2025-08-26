<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the room occupancy report.
     *
     * @return \Illuminate\Http\Response
     */
    public function roomOccupancy(Request $request)
    {
        $user = Auth::user();
        $properties = Property::where('user_id', $user->id)->get();
        $selectedProperty = $request->input('property_id') ? Property::find($request->input('property_id')) : null;

        $query = Room::with(['property', 'roomType', 'currentContract'])
            ->whereHas('property', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        if ($selectedProperty) {
            $query->where('property_id', $selectedProperty->id);
        }

        $rooms = $query->get();

        // Calculate statistics
        $totalRooms = $rooms->count();
        $occupiedRooms = $rooms->filter(function ($room) {
            return $room->currentContract !== null;
        })->count();

        $vacantRooms = $totalRooms - $occupiedRooms;
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        // Group by room type
        $roomsByType = $rooms->groupBy('room_type_id')->map(function ($typeRooms) {
            $roomType = $typeRooms->first()->roomType;
            $total = $typeRooms->count();
            $occupied = $typeRooms->filter(function ($room) {
                return $room->currentContract !== null;
            })->count();

            return [
                'name' => $roomType->name,
                'total' => $total,
                'occupied' => $occupied,
                'vacant' => $total - $occupied,
                'occupancy_rate' => $total > 0 ? ($occupied / $total) * 100 : 0
            ];
        });

        return view('backends.dashboard.landlord.reports.room_occupancy', compact(
            'rooms',
            'properties',
            'selectedProperty',
            'totalRooms',
            'occupiedRooms',
            'vacantRooms',
            'occupancyRate',
            'roomsByType'
        ));
    }

    /**
     * Display the tenant report.
     *
     * @return \Illuminate\Http\Response
     */
    public function tenantReport(Request $request)
    {
        $user = Auth::user();
        $properties = Property::where('user_id', $user->id)->get();
        $selectedProperty = $request->input('property_id') ? Property::find($request->input('property_id')) : null;

        // In your controller's tenantReport method

        $query = User::role('tenant')
            ->whereHas('contracts.room.property', function ($propertyQuery) use ($user, $selectedProperty) {
                $propertyQuery->where('landlord_id', $user->id);

                if ($selectedProperty) {
                    $propertyQuery->where('id', $selectedProperty->id);
                }
            })
            ->with([
                // Eager load only the necessary relationships for the view
                'currentContract.room.property',
            ]);

        $tenants = $query->get();

        // Calculate statistics
        $totalTenants = $tenants->count();
        $activeTenants = $tenants->filter(function ($tenant) {
            return $tenant->contracts->contains(function ($contract) {
                return $contract->status === 'active';
            });
        })->count();

        $inactiveTenants = $totalTenants - $activeTenants;

        // Group tenants by property
        $tenantsByProperty = [];
        foreach ($tenants as $tenant) {
            foreach ($tenant->contracts as $contract) {
                $propertyId = $contract->room->property->id;
                $propertyName = $contract->room->property->name;

                if (!isset($tenantsByProperty[$propertyId])) {
                    $tenantsByProperty[$propertyId] = [
                        'name' => $propertyName,
                        'total' => 0,
                        'active' => 0,
                        'inactive' => 0
                    ];
                }

                $tenantsByProperty[$propertyId]['total']++;
                if ($contract->status === 'active') {
                    $tenantsByProperty[$propertyId]['active']++;
                } else {
                    $tenantsByProperty[$propertyId]['inactive']++;
                }
            }
        }

        return view('backends.dashboard.landlord.reports.tenant_report', compact(
            'tenants',
            'properties',
            'selectedProperty',
            'totalTenants',
            'activeTenants',
            'inactiveTenants',
            'tenantsByProperty'
        ));
    }

    /**
     * Display the financial report.
     *
     * @return \Illuminate\Http\Response
     */
    public function financialReport(Request $request)
    {
        $user = Auth::user();
        $currentYear = $request->input('year', Carbon::now()->year);
        $currentMonth = $request->input('month', null);

        $properties = Property::where('user_id', $user->id)->get();
        $selectedProperty = $request->input('property_id') ? Property::find($request->input('property_id')) : null;

        $query = Invoice::with(['contract', 'contract.room', 'contract.room.property'])
            ->whereHas('contract', function ($q) use ($user, $selectedProperty) {
                $q->whereHas('room', function ($r) use ($user, $selectedProperty) {
                    $r->whereHas('property', function ($p) use ($user) {
                        $p->where('user_id', $user->id);
                    });

                    if ($selectedProperty) {
                        $r->where('property_id', $selectedProperty->id);
                    }
                });
            });

        // Apply year filter
        $query->whereYear('due_date', $currentYear);

        // Apply month filter if selected
        if ($currentMonth) {
            $query->whereMonth('due_date', $currentMonth);
        }

        $invoices = $query->get();

        // Calculate statistics
        $totalInvoices = $invoices->count();
        $paidInvoices = $invoices->where('status', 'paid')->count();
        $unpaidInvoices = $invoices->where('status', 'unpaid')->count();
        $totalAmount = $invoices->sum('total_amount');
        $paidAmount = $invoices->where('status', 'paid')->sum('total_amount');
        $unpaidAmount = $invoices->where('status', 'unpaid')->sum('total_amount');

        // Prepare data for monthly chart
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthInvoices = $invoices->filter(function ($invoice) use ($i) {
                return Carbon::parse($invoice->due_date)->month == $i;
            });

            $monthlyData[$i] = [
                'month' => date('F', mktime(0, 0, 0, $i, 1)),
                'total' => $monthInvoices->sum('total_amount'),
                'paid' => $monthInvoices->where('status', 'paid')->sum('total_amount'),
                'unpaid' => $monthInvoices->where('status', 'unpaid')->sum('total_amount')
            ];
        }

        // Group by property
        $invoicesByProperty = [];
        foreach ($invoices as $invoice) {
            $propertyId = $invoice->contract->room->property->id;
            $propertyName = $invoice->contract->room->property->name;

            if (!isset($invoicesByProperty[$propertyId])) {
                $invoicesByProperty[$propertyId] = [
                    'name' => $propertyName,
                    'total' => 0,
                    'paid' => 0,
                    'unpaid' => 0
                ];
            }

            $invoicesByProperty[$propertyId]['total'] += $invoice->total_amount;
            if ($invoice->status === 'paid') {
                $invoicesByProperty[$propertyId]['paid'] += $invoice->total_amount;
            } else {
                $invoicesByProperty[$propertyId]['unpaid'] += $invoice->total_amount;
            }
        }

        return view('backends.dashboard.landlord.reports.financial_report', compact(
            'invoices',
            'properties',
            'selectedProperty',
            'currentYear',
            'currentMonth',
            'totalInvoices',
            'paidInvoices',
            'unpaidInvoices',
            'totalAmount',
            'paidAmount',
            'unpaidAmount',
            'monthlyData',
            'invoicesByProperty'
        ));
    }

    /**
     * Display an overview of all reports.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        // Get room occupancy stats
        $rooms = Room::whereHas('property', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        $totalRooms = $rooms->count();
        $occupiedRooms = $rooms->filter(function ($room) {
            return $room->currentContract !== null;
        })->count();

        $vacantRooms = $totalRooms - $occupiedRooms;
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        // Get tenant stats
        $tenants = User::role('tenant')
            ->whereHas('contracts', function ($query) use ($user) {
                $query->whereHas('room', function ($q) use ($user) {
                    $q->whereHas('property', function ($p) use ($user) {
                        $p->where('user_id', $user->id);
                    });
                });
            })->get();

        $totalTenants = $tenants->count();
        $activeTenants = $tenants->filter(function ($tenant) {
            return $tenant->contracts->contains(function ($contract) {
                return $contract->status === 'active';
            });
        })->count();

        // Get financial stats for current year
        $currentYear = Carbon::now()->year;
        $invoices = Invoice::whereHas('contract', function ($q) use ($user) {
            $q->whereHas('room', function ($r) use ($user) {
                $r->whereHas('property', function ($p) use ($user) {
                    $p->where('user_id', $user->id);
                });
            });
        })
            ->whereYear('due_date', $currentYear)
            ->get();

        $totalAmount = $invoices->sum('total_amount');
        $paidAmount = $invoices->where('status', 'paid')->sum('total_amount');
        $unpaidAmount = $invoices->where('status', 'unpaid')->sum('total_amount');
        $collectionRate = $totalAmount > 0 ? ($paidAmount / $totalAmount) * 100 : 0;

        // Monthly financial data for chart
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthInvoices = $invoices->filter(function ($invoice) use ($i) {
                return Carbon::parse($invoice->due_date)->month == $i;
            });

            $monthlyData[$i] = [
                'month' => date('F', mktime(0, 0, 0, $i, 1)),
                'total' => $monthInvoices->sum('total_amount'),
                'paid' => $monthInvoices->where('status', 'paid')->sum('total_amount')
            ];
        }

        return view('backends.dashboard.landlord.reports.index', compact(
            'totalRooms',
            'occupiedRooms',
            'vacantRooms',
            'occupancyRate',
            'totalTenants',
            'activeTenants',
            'totalAmount',
            'paidAmount',
            'unpaidAmount',
            'collectionRate',
            'monthlyData',
            'currentYear'
        ));
    }
}
