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
use Illuminate\Contracts\View\View;

class ReportsController extends Controller
{
    /**
     * Display the room occupancy report.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function roomOccupancy(Request $request): View
    {
        $user = Auth::user();
        $properties = Property::where('landlord_id', $user->id)->get();
        $selectedProperty = $request->input('property_id') ? Property::find($request->input('property_id')) : null;
        
        $query = Room::with(['property', 'roomType', 'currentContract', 'currentContract.tenant'])
            ->whereHas('property', function ($query) use ($user) {
                $query->where('landlord_id', $user->id);
            });
            
        if ($selectedProperty) {
            $query->where('property_id', $selectedProperty->id);
        }
        
        $rooms = $query->get();
        
        // Calculate statistics
        $totalRooms = $rooms->count();
        $occupiedRooms = $rooms->filter(function($room) {
            return $room->currentContract !== null;
        })->count();
        
        $vacantRooms = $totalRooms - $occupiedRooms;
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;
        
        // Group by room type
        $roomsByType = $rooms->groupBy('room_type_id')->map(function($typeRooms) {
            $roomType = $typeRooms->first()->roomType;
            $total = $typeRooms->count();
            $occupied = $typeRooms->filter(function($room) {
                return $room->currentContract !== null;
            })->count();
            
            return [
                'name' => $roomType ? $roomType->name : 'Uncategorized',
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
     * @return \Illuminate\Contracts\View\View
     */
    public function tenantReport(Request $request): View
    {
        $user = Auth::user();
        $properties = Property::where('landlord_id', $user->id)->get();
        $selectedProperty = $request->input('property_id') ? Property::find($request->input('property_id')) : null;
        
        $query = User::role('tenant')
            ->whereHas('contracts', function($query) use ($user, $selectedProperty) {
                $query->whereHas('room', function($q) use ($user, $selectedProperty) {
                    $q->whereHas('property', function($p) use ($user) {
                        $p->where('landlord_id', $user->id);
                    });
                    
                    if ($selectedProperty) {
                        $q->where('property_id', $selectedProperty->id);
                    }
                });
            })
            ->with(['contracts' => function($query) use ($user, $selectedProperty) {
                $query->whereHas('room', function($q) use ($user, $selectedProperty) {
                    $q->whereHas('property', function($p) use ($user) {
                        $p->where('user_id', $user->id);
                    });
                    
                    if ($selectedProperty) {
                        $q->where('property_id', $selectedProperty->id);
                    }
                });
            }, 'contracts.room', 'contracts.room.property']);
        
        $tenants = $query->get();
        
        // Calculate statistics
        $totalTenants = $tenants->count();
        $activeTenants = $tenants->filter(function($tenant) {
            return $tenant->contracts->contains(function($contract) {
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
        
        // Get contract status statistics
        $now = Carbon::now();
        $totalContracts = Contract::whereHas('room', function($q) use ($user) {
            $q->whereHas('property', function($p) use ($user) {
                $p->where('landlord_id', $user->id);
            });
        })->count();
        
        $activeContracts = Contract::whereHas('room', function($q) use ($user) {
            $q->whereHas('property', function($p) use ($user) {
                $p->where('landlord_id', $user->id);
            });
        })->where('end_date', '>', $now)->count();
        
        $expiringContracts = Contract::whereHas('room', function($q) use ($user) {
            $q->whereHas('property', function($p) use ($user) {
                $p->where('landlord_id', $user->id);
            });
        })->where('end_date', '>', $now)
          ->where('end_date', '<=', $now->copy()->addDays(30))
          ->count();
        
        $expiredContracts = Contract::whereHas('room', function($q) use ($user) {
            $q->whereHas('property', function($p) use ($user) {
                $p->where('landlord_id', $user->id);
            });
        })->where('end_date', '<=', $now)->count();
        
        // Calculate contract duration statistics
        $durationStats = [
            'lessThan3Months' => 0,
            '3to6Months' => 0,
            '6to12Months' => 0,
            'moreThan12Months' => 0
        ];
        
        foreach ($tenants as $tenant) {
            foreach ($tenant->contracts as $contract) {
                $start = Carbon::parse($contract->start_date);
                $end = Carbon::parse($contract->end_date);
                $durationMonths = $start->diffInMonths($end);
                
                if ($durationMonths < 3) {
                    $durationStats['lessThan3Months']++;
                } elseif ($durationMonths >= 3 && $durationMonths < 6) {
                    $durationStats['3to6Months']++;
                } elseif ($durationMonths >= 6 && $durationMonths <= 12) {
                    $durationStats['6to12Months']++;
                } else {
                    $durationStats['moreThan12Months']++;
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
            'tenantsByProperty',
            'totalContracts',
            'activeContracts',
            'expiringContracts',
            'expiredContracts',
            'durationStats'
        ));
    }

    /**
     * Display the financial report.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function financialReport(Request $request): View
    {
        $user = Auth::user();
        $currentYear = $request->input('year', Carbon::now()->year);
        $selectedYear = $currentYear; // Add this line to define $selectedYear
        $currentMonth = $request->input('month', null);
        $selectedMonth = $currentMonth; // Add this line to define $selectedMonth
        
        $properties = Property::where('landlord_id', $user->id)->get();
        $selectedProperty = $request->input('property_id') ? Property::find($request->input('property_id')) : null;
        
        $query = Invoice::with(['contract', 'contract.tenant', 'contract.room', 'contract.room.property'])
            ->whereHas('contract', function($q) use ($user, $selectedProperty) {
                $q->whereHas('room', function($r) use ($user, $selectedProperty) {
                    $r->whereHas('property', function($p) use ($user) {
                        $p->where('landlord_id', $user->id);
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
        $outstandingAmount = $unpaidAmount; // Define outstandingAmount to match unpaidAmount
        
        // Prepare data for monthly chart
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthInvoices = $invoices->filter(function($invoice) use ($i) {
                return Carbon::parse($invoice->due_date)->month == $i;
            });
            
            $monthlyData[$i] = [
                'month' => date('F', mktime(0, 0, 0, $i, 1)),
                'total' => $monthInvoices->sum('total_amount'),
                'paid' => $monthInvoices->where('status', 'paid')->sum('total_amount'),
                'unpaid' => $monthInvoices->where('status', 'unpaid')->sum('total_amount'),
                'outstanding' => $monthInvoices->where('status', 'unpaid')->sum('total_amount')
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
        
        // Prepare available years
        $availableYears = [];
        $currentYear = Carbon::now()->year;
        for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++) {
            $availableYears[] = $i;
        }
        
        // Month names for dropdown
        $monthNames = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
        
        // Calculate payment status statistics
        $paymentStatusStats = [
            'paid' => $invoices->where('status', 'paid')->count(),
            'partial' => $invoices->where('status', 'partial')->count(),
            'unpaid' => $invoices->where('status', '!=', 'paid')
                         ->where('status', '!=', 'partial')
                         ->where('due_date', '>=', Carbon::now())->count(),
            'overdue' => $invoices->where('status', '!=', 'paid')
                         ->where('due_date', '<', Carbon::now())->count()
        ];
        
        // Calculate revenue by property
        $revenueByProperty = collect($invoicesByProperty)->map(function ($property) {
            return [
                'name' => $property['name'],
                'total_revenue' => $property['total'],
                'paid_amount' => $property['paid'],
                'outstanding' => $property['unpaid'],
                'payment_rate' => $property['total'] > 0 ? ($property['paid'] / $property['total']) * 100 : 0,
                'invoice_count' => 0 // We'll set this in the next step
            ];
        })->toArray();
        
        // Add invoice count
        foreach ($invoices as $invoice) {
            $propertyId = $invoice->contract->room->property->id;
            if (isset($revenueByProperty[$propertyId])) {
                $revenueByProperty[$propertyId]['invoice_count']++;
            }
        }
        
        // Set outstandingAmount to match unpaidAmount for the view
        $outstandingAmount = $unpaidAmount;
        
        return view('backends.dashboard.landlord.reports.financial_report', compact(
            'invoices',
            'properties',
            'selectedProperty',
            'currentYear',
            'currentMonth',
            'selectedYear',
            'selectedMonth',
            'totalInvoices',
            'paidInvoices',
            'unpaidInvoices',
            'totalAmount',
            'paidAmount',
            'unpaidAmount',
            'outstandingAmount',
            'monthlyData',
            'invoicesByProperty',
            'availableYears',
            'monthNames',
            'paymentStatusStats',
            'revenueByProperty'
        ));
    }

    /**
     * Display an overview of all reports.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Get room occupancy stats
        $rooms = Room::whereHas('property', function($query) use ($user) {
            $query->where('landlord_id', $user->id);
        })->get();
        
        $totalRooms = $rooms->count();
        $occupiedRooms = $rooms->filter(function($room) {
            return $room->currentContract !== null;
        })->count();
        
        $vacantRooms = $totalRooms - $occupiedRooms;
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;
        
        // Get tenant stats
        $tenants = User::role('tenant')
            ->whereHas('contracts', function($query) use ($user) {
                $query->whereHas('room', function($q) use ($user) {
                    $q->whereHas('property', function($p) use ($user) {
                        $p->where('landlord_id', $user->id);
                    });
                });
            })->get();
        
        $totalTenants = $tenants->count();
        $activeTenants = $tenants->filter(function($tenant) {
            return $tenant->contracts->contains(function($contract) {
                return $contract->status === 'active';
            });
        })->count();
        
        // Get financial stats for current year
        $currentYear = Carbon::now()->year;
        $invoices = Invoice::whereHas('contract', function($q) use ($user) {
            $q->whereHas('room', function($r) use ($user) {
                $r->whereHas('property', function($p) use ($user) {
                    $p->where('landlord_id', $user->id);
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
            $monthInvoices = $invoices->filter(function($invoice) use ($i) {
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
