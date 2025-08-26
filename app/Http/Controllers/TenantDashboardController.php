<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\MeterReading;
use App\Models\Meter;
use App\Models\UtilityBill;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TenantDashboardController extends Controller
{
    /**
     * Display the tenant's dashboard with all relevant statistics and data.
     */
    public function index(Request $request)
    {
        $tenant = Auth::user();
        $now = Carbon::now();

        // Get the tenant's active contract
        $currentContract = Contract::where('user_id', $tenant->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        // --- Base Queries (to keep code DRY and scoped to the tenant) ---
        $invoicesQuery = Invoice::whereHas('contract', fn($q) => $q->where('user_id', $tenant->id));

        // Get next invoice due (if any)
        $nextInvoice = (clone $invoicesQuery)
            ->whereIn('status', ['sent', 'overdue'])
            ->orderBy('due_date')
            ->first();

        // Get recent invoices
        $recentInvoices = (clone $invoicesQuery)
            ->with(['contract', 'lineItems'])
            ->latest('issue_date')
            ->limit(5)
            ->get();

        // Get total balance due - calculate as total_amount - paid_amount
        $pendingInvoices = (clone $invoicesQuery)
            ->whereIn('status', ['sent', 'overdue'])
            ->get();
            
        $totalBalanceDue = $pendingInvoices->sum(function($invoice) {
            return $invoice->total_amount - $invoice->paid_amount;
        });

        // Get total paid this month
        $totalPaidThisMonth = (clone $invoicesQuery)
            ->where('status', 'paid')
            ->where('issue_date', '>=', Carbon::now()->startOfMonth())
            ->sum('paid_amount');

        // Calculate remaining stats for backwards compatibility
        $totalInvoices = (clone $invoicesQuery)->count();
        $pendingInvoices = (clone $invoicesQuery)->whereIn('status', ['sent', 'overdue'])->count();
        $paidInvoices = (clone $invoicesQuery)->where('status', 'paid')->count();
        
        $stats = [
            'total_invoices' => $totalInvoices,
            'pending_invoices' => $pendingInvoices,
            'paid_invoices' => $paidInvoices,
            'contract_days_left' => $currentContract ? intval($now->diffInDays($currentContract->end_date, false)) : 0,
        ];

        // Get data for simple payment history chart (Last 6 Months)
        $months = collect([]);
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('M Y'));
        }
        
        $paymentData = (clone $invoicesQuery)
            ->select(
                DB::raw('SUM(total_amount) as billed'),
                DB::raw('SUM(paid_amount) as paid'),
                DB::raw("DATE_FORMAT(issue_date, '%b %Y') as monthname"),
                DB::raw("MIN(issue_date) as month_date") // Add this for ordering
            )
            ->where('issue_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('monthname')
            ->orderBy('month_date', 'asc') // Order by the aggregated date field instead
            ->get();
            
        $billedChart = $months->mapWithKeys(fn($month) => [
            $month => $paymentData->firstWhere('monthname', $month)->billed ?? 0
        ]);
        
        $paidChart = $months->mapWithKeys(fn($month) => [
            $month => $paymentData->firstWhere('monthname', $month)->paid ?? 0
        ]);

        // Get all invoices for backward compatibility
        $allInvoices = (clone $invoicesQuery)
            ->with(['contract', 'lineItems'])
            ->latest('issue_date')
            ->get();

        // Get recent utility bills
        $recentUtilityBills = UtilityBill::whereHas('contract', fn($q) => $q->where('user_id', $tenant->id))
            ->with(['utilityType', 'contract'])
            ->latest('billing_period_end')
            ->limit(3)
            ->get();

        // Get utility bills for backward compatibility
        $utilityBills = UtilityBill::whereHas('contract', fn($q) => $q->where('user_id', $tenant->id))
            ->with(['utilityType', 'contract'])
            ->latest('billing_period_end')
            ->get();

        // Get utility usage data for the chart (simplified)
        $utilityData = [];
        $meterReadingHistory = [];
        
        if ($currentContract) {
            // Get all meters associated with the tenant's room
            $meters = Meter::where('room_id', $currentContract->room_id)->get();
            
            foreach ($meters as $meter) {
                $utilityName = $meter->utilityType->name;
                $utilityData[$utilityName] = [];
                
                // Process meter readings for simplified chart
                $readings = MeterReading::where('meter_id', $meter->id)
                    ->where('reading_date', '>=', now()->subMonths(5)->startOfMonth())
                    ->orderBy('reading_date')
                    ->get()
                    ->groupBy(function ($reading) {
                        return Carbon::parse($reading->reading_date)->format('M Y');
                    });
                
                // Store all readings for detailed history view (backward compatibility)
                $meterReadingHistory[$meter->id] = [
                    'meter' => $meter,
                    'readings' => MeterReading::where('meter_id', $meter->id)
                        ->with('recordedBy')
                        ->orderBy('reading_date', 'desc')
                        ->get(),
                    'paginatedReadings' => MeterReading::where('meter_id', $meter->id)
                        ->with('recordedBy')
                        ->orderBy('reading_date', 'desc')
                        ->paginate(5, ['*'], "meter_{$meter->id}_page"),
                    'allReadings' => MeterReading::where('meter_id', $meter->id)
                        ->orderBy('reading_date', 'desc')
                        ->get()
                ];
                
                // Calculate monthly usage
                $previousReading = null;
                
                foreach ($months as $month) {
                    if (isset($readings[$month]) && count($readings[$month]) > 0) {
                        $monthReadings = $readings[$month];
                        $latestReading = $monthReadings->sortByDesc('reading_date')->first()->reading_value;
                        
                        if ($previousReading === null) {
                            // For the first month with data, use the reading as is or calculate from initial reading
                            $earliestInPeriod = $monthReadings->sortBy('reading_date')->first()->reading_value;
                            $usage = $latestReading - ($earliestInPeriod ?: $meter->initial_reading);
                        } else {
                            // For subsequent months, calculate usage from previous month
                            $usage = $latestReading - $previousReading;
                        }
                        
                        $utilityData[$utilityName][$month] = max(0, $usage); // Ensure no negative usage
                        $previousReading = $latestReading;
                    } else {
                        $utilityData[$utilityName][$month] = 0;
                    }
                }
            }
        }

        // Get important notifications
        $notifications = [];
        
        // Add overdue invoice notification
        $overdueInvoices = (clone $invoicesQuery)->where('status', 'overdue')->count();
        
        if ($overdueInvoices > 0) {
            $notifications[] = [
                'type' => 'danger',
                'icon' => 'alert-triangle',
                'message' => "You have {$overdueInvoices} overdue " . ($overdueInvoices > 1 ? 'invoices' : 'invoice') . " that " . ($overdueInvoices > 1 ? 'require' : 'requires') . " immediate attention."
            ];
        }
        
        // Add contract ending soon notification
        if ($currentContract && $currentContract->end_date->diffInDays(now()) <= 30) {
            $daysUntilEnd = intval($currentContract->end_date->diffInDays(now()));
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'calendar',
                'message' => "Your contract is ending in {$daysUntilEnd} days. Please contact the property manager to discuss renewal options."
            ];
        }
        
        // Add high utility usage notification (simplified example)
        foreach ($utilityData as $utilityName => $monthlyData) {
            $currentMonth = now()->format('M Y');
            $lastMonth = now()->subMonth()->format('M Y');
            
            if (isset($monthlyData[$currentMonth]) && isset($monthlyData[$lastMonth])) {
                $currentUsage = $monthlyData[$currentMonth];
                $lastUsage = $monthlyData[$lastMonth];
                
                if ($currentUsage > $lastUsage * 1.25 && $currentUsage > 0 && $lastUsage > 0) { // 25% increase
                    $notifications[] = [
                        'type' => 'info',
                        'icon' => 'zap',
                        'message' => "Your {$utilityName} usage has increased by " . number_format(($currentUsage - $lastUsage) / $lastUsage * 100, 0) . "% compared to last month."
                    ];
                }
            }
        }

        // Always use the simplified dashboard design
        $viewTemplate = 'backends.dashboard.tenant.index_simplified';

        return view($viewTemplate, compact(
            'currentContract',
            'nextInvoice',
            'recentInvoices',
            'totalBalanceDue',
            'totalPaidThisMonth',
            'recentUtilityBills',
            'utilityData',
            'notifications',
            'months',
            // Include variables for backwards compatibility
            'stats',
            'billedChart',
            'paidChart',
            'allInvoices',
            'utilityBills',
            'meterReadingHistory'
        ));
    }
    
    /**
     * Get all invoices for the tenant
     */
    public function allInvoices(Request $request)
    {
        // Start with base query
        $query = Invoice::whereHas('contract', fn($q) => $q->where('user_id', Auth::id()))
            ->with(['contract', 'lineItems']);
        
        // Apply status filter if provided
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter recent (last 30 days) if requested
        if ($request->has('status') && $request->status == 'recent') {
            $query->where('issue_date', '>=', now()->subDays(30));
        }
        
        // Filter rent-only invoices if requested
        if ($request->has('status') && $request->status == 'rent_only') {
            $query->whereHas('lineItems', function($q) {
                $q->where('description', 'like', '%rent%');
            });
        }
        
        // Get paginated results
        $invoices = $query->latest('issue_date')->paginate(10);
        
        // Append query parameters for pagination links
        $invoices->appends($request->all());
            
        return view('backends.dashboard.tenant.invoices', compact('invoices'));
    }
    
    /**
     * Get all utility bills for the tenant
     */
    public function allUtilityBills(Request $request)
    {
        // Start with base query
        $query = UtilityBill::whereHas('contract', fn($q) => $q->where('user_id', Auth::id()))
            ->with(['utilityType', 'contract.room.property', 'lineItem']);
        
        // Apply type filter if provided
        if ($request->has('type') && $request->type != 'all') {
            $query->whereHas('utilityType', function($q) use ($request) {
                $q->where('name', $request->type);
            });
        }
        
        // Get paginated results with 10 items per page
        $utilityBills = $query->latest('billing_period_end')->paginate(10);
            
        // Append query parameters for pagination links
        $utilityBills->appends($request->all());
            
        return view('backends.dashboard.tenant.utility-bills', compact('utilityBills'));
    }
    
    /**
     * Get utility usage details
     */
    public function utilityUsage()
    {
        $tenant = Auth::user();
        
        // Get the tenant's active contract
        $currentContract = Contract::where('user_id', $tenant->id)
            ->where('status', 'active')
            ->latest()
            ->first();
            
        if (!$currentContract) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'No active contract found.');
        }
        
        // Get meters associated with the tenant's room
        $meters = Meter::where('room_id', $currentContract->room_id)
            ->with('utilityType')
            ->get();
            
        // Get readings for each meter
        $meterReadingHistory = [];
        
        foreach ($meters as $meter) {
            $readings = MeterReading::where('meter_id', $meter->id)
                ->with('recordedBy')
                ->orderBy('reading_date', 'desc')
                ->paginate(10, ['*'], "meter_{$meter->id}_page");
                
            $meterReadingHistory[$meter->id] = [
                'meter' => $meter,
                'readings' => $readings,
                'allReadings' => MeterReading::where('meter_id', $meter->id)
                    ->orderBy('reading_date', 'desc')
                    ->get()
            ];
        }
        
        // Get utility usage data for the chart
        $utilityData = [];
        $months = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('M Y');
        }
        
        foreach ($meters as $meter) {
            $utilityName = $meter->utilityType->name;
            $utilityData[$utilityName] = [];
            
            // First, collect all readings chronologically
            $allReadings = MeterReading::where('meter_id', $meter->id)
                ->orderBy('reading_date', 'asc')
                ->get();
                
            // Initialize all months to zero
            foreach ($months as $month) {
                $utilityData[$utilityName][$month] = 0;
            }
            
            // If no readings, continue to next meter
            if ($allReadings->isEmpty()) {
                continue;
            }
                
            // Initialize previous reading for proper usage calculation
            $previousMonthReading = $meter->initial_reading;
            $previousMonthDate = null;
            
            // For simpler approach, just display the actual meter readings where they exist
            foreach ($months as $month) {
                $monthDate = Carbon::createFromFormat('M Y', $month);
                $monthStart = $monthDate->copy()->startOfMonth();
                $monthEnd = $monthDate->copy()->endOfMonth();
                
                // Get readings in this month
                $monthReadings = $allReadings->filter(function($reading) use ($monthStart, $monthEnd) {
                    $readingDate = Carbon::parse($reading->reading_date);
                    return $readingDate >= $monthStart && $readingDate <= $monthEnd;
                });
                
                if ($monthReadings->isNotEmpty()) {
                    // If we have readings in this month, use the latest reading value directly
                    $latestReading = $monthReadings->sortByDesc('reading_date')->first();
                    
                    // Since we're focusing on showing the actual reading values rather than calculated usage
                    // Just use the reading value directly for clarity in this demo
                    $utilityData[$utilityName][$month] = $latestReading->reading_value;
                }
                // If no readings this month, we keep the default zero
            }
            
            // Instead of adding artificial demo values, let's use the actual readings
            if (array_sum($utilityData[$utilityName]) == 0 && $allReadings->isNotEmpty()) {
                // Find the latest reading month and use the actual reading value
                $latestReading = $allReadings->sortByDesc('reading_date')->first();
                $readingMonth = Carbon::parse($latestReading->reading_date)->format('M Y');
                
                // Only add a data point for the month that has an actual reading
                if (isset($utilityData[$utilityName][$readingMonth])) {
                    // For electricity, use the exact reading value
                    if (strtolower($utilityName) == 'electricity') {
                        $utilityData[$utilityName][$readingMonth] = $latestReading->reading_value;
                    } else {
                        // For water, use the actual reading value
                        $utilityData[$utilityName][$readingMonth] = $latestReading->reading_value;
                    }
                }
            }
        }
        
        return view('backends.dashboard.tenant.utility-usage', compact(
            'meters',
            'meterReadingHistory',
            'utilityData',
            'months'
        ));
    }
    
    /**
     * Get invoice details for AJAX request
     */
    public function getInvoiceDetails(Invoice $invoice)
    {
        // Check if this invoice belongs to the authenticated tenant
        if ($invoice->contract->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Load the invoice with its line items
        $invoice->load(['contract', 'lineItems']);
        
        // Calculate balance manually to avoid NaN issues
        $balance = $invoice->total_amount - $invoice->paid_amount;
        
        // Create a modified invoice with the calculated balance
        $invoiceData = $invoice->toArray();
        $invoiceData['balance'] = $balance;
        
        return response()->json([
            'invoice' => $invoiceData,
            'line_items' => $invoice->lineItems,
            'contract' => $invoice->contract,
            'room' => $invoice->contract->room,
            'property' => $invoice->contract->room->property,
        ]);
    }
    
    /**
     * Reset the dashboard design to original
     */
    public function resetDesign()
    {
        Session::forget('dashboard_design');
        return redirect()->route('tenant.dashboard');
    }
    
    /**
     * Get meter readings for AJAX pagination
     */
    public function getMeterReadings(Request $request, $meterId)
    {
        $tenant = Auth::user();
        $currentContract = Contract::where('user_id', $tenant->id)
            ->where('status', 'active')
            ->latest()
            ->first();
            
        if (!$currentContract) {
            return response()->json(['error' => 'No active contract found'], 404);
        }
        
        // Verify this meter belongs to the tenant's room
        $meter = Meter::where('id', $meterId)
            ->where('room_id', $currentContract->room_id)
            ->with('utilityType')
            ->first();
            
        if (!$meter) {
            return response()->json(['error' => 'Meter not found'], 404);
        }
        
        // Get paginated readings
        $readings = MeterReading::where('meter_id', $meterId)
            ->with('recordedBy')
            ->orderBy('reading_date', 'desc')
            ->paginate(10);
            
        // Get all readings for this meter (for consumption calculation)
        $allReadings = MeterReading::where('meter_id', $meterId)
            ->orderBy('reading_date')
            ->get();
            
        // Format the readings for JSON response
        $formattedReadings = [];
        foreach ($readings as $reading) {
            // Calculate consumption
            $chronologicalReadings = collect($allReadings->all())->sortBy('reading_date')->values();
            $chronoIndex = $chronologicalReadings->search(fn($item) => $item->id === $reading->id);
            $isFirstEverReading = ($chronoIndex == 0);
            
            if ($isFirstEverReading) {
                $consumption = $reading->reading_value - $meter->initial_reading;
            } else {
                $previousReading = $chronologicalReadings->get($chronoIndex - 1);
                $consumption = $reading->reading_value - $previousReading->reading_value;
            }
            
            $formattedReadings[] = [
                'id' => $reading->id,
                'reading_date' => $reading->reading_date,
                'reading_value' => $reading->reading_value,
                'consumption' => $consumption,
                'unit' => $meter->utilityType->unit,
                'recorded_by' => $reading->recordedBy->name,
            ];
        }
        
        return response()->json([
            'readings' => $formattedReadings,
            'has_more_pages' => $readings->hasMorePages(),
            'next_page' => $readings->hasMorePages() ? $readings->currentPage() + 1 : null,
            'current_page' => $readings->currentPage(),
        ]);
    }
    
    /**
     * Display the tenant's profile page
     */
    public function profile()
    {
        $user = Auth::user();
        $now = Carbon::now();
        
        // Get the tenant's active contract
        $currentContract = Contract::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();
            
        // Calculate stats
        $stats = [
            'total_contracts' => Contract::where('user_id', $user->id)->count(),
            'properties_rented' => Contract::where('user_id', $user->id)
                ->select('room_id')
                ->distinct()
                ->count(),
            'months_as_tenant' => Contract::where('user_id', $user->id)
                ->where('start_date', '<=', now())
                ->sum(DB::raw('TIMESTAMPDIFF(MONTH, GREATEST(start_date, DATE_SUB(NOW(), INTERVAL 1 YEAR)), LEAST(end_date, NOW()))'))
        ];
        
        // Get user's documents
        $documents = \App\Models\Document::where('user_id', $user->id)
            ->latest()
            ->get();
        
        return view('backends.dashboard.tenant.profile', compact('user', 'currentContract', 'stats', 'documents'));
    }
    
    /**
     * Update the tenant's profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'emergency_contact' => 'nullable|string|max:255',
        ]);
        
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->date_of_birth = $request->date_of_birth;
        $user->emergency_contact = $request->emergency_contact;
        
        if ($user->isDirty('email')) {
            // Set email_verified_at to null without type errors
            $user->forceFill(['email_verified_at' => null]);
        }
        
        $user->save();
        
        return redirect()->route('tenant.profile')->with('success', 'Profile information updated successfully.');
    }
    
    /**
     * Upload a document for the tenant
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|string|in:id,contract,proof_of_address,other',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'document_description' => 'nullable|string|max:1000',
        ]);
        
        $user = Auth::user();
        
        // Create uploads directory if it doesn't exist
        $uploadPath = 'uploads/tenant_documents/' . $user->id;
        if (!file_exists(public_path($uploadPath))) {
            mkdir(public_path($uploadPath), 0777, true);
        }
        
        // Store the file with a unique name
        $fileName = time() . '_' . $request->file('document_file')->getClientOriginalName();
        $request->file('document_file')->move(public_path($uploadPath), $fileName);
        
        // Save document info to database
        $document = new \App\Models\Document();
        $document->user_id = $user->id;
        $document->name = $request->document_name;
        $document->type = $request->document_type;
        $document->file_path = $uploadPath . '/' . $fileName;
        $document->description = $request->document_description;
        $document->save();
        
        return redirect()->route('tenant.profile')->with('success', 'Document uploaded successfully.');
    }
    
    /**
     * Download a tenant document
     */
    public function downloadDocument(\App\Models\Document $document)
    {
        // Check if the document belongs to the current user
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        
        $filePath = public_path($document->file_path);
        
        if (!file_exists($filePath)) {
            return redirect()->route('tenant.profile')->with('error', 'Document not found.');
        }
        
        // Get the file extension to determine content type
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $contentType = 'application/octet-stream'; // Default
        
        if ($extension === 'pdf') {
            $contentType = 'application/pdf';
        } elseif (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $contentType = 'image/' . $extension;
        }
        
        return response()->download($filePath, $document->name . '.' . $extension, ['Content-Type' => $contentType]);
    }
    
    /**
     * Delete a tenant document
     */
    public function deleteDocument(\App\Models\Document $document)
    {
        // Check if the document belongs to the current user
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        
        // Get the file path
        $filePath = public_path($document->file_path);
        
        // Delete the file if it exists
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete the document record
        $document->delete();
        
        return redirect()->route('tenant.profile')->with('success', 'Document deleted successfully.');
    }
}
