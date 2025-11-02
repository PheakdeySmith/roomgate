<?php

namespace App\Services\Tenant;

use App\Models\User;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\MeterReading;
use App\Models\Document;
use App\Models\MaintenanceRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class TenantService
{
    /**
     * Create a new tenant user
     */
    public function createTenant(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $tenant = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password'] ?? Str::random(16)),
                'email_verified_at' => now(),
            ]);

            // Assign tenant role
            $tenantRole = Role::firstOrCreate(['name' => 'tenant']);
            $tenant->assignRole($tenantRole);

            // Create tenant profile if additional data provided
            if (isset($data['profile'])) {
                $tenant->profile()->create($data['profile']);
            }

            Log::info("New tenant created: {$tenant->email}");

            return $tenant;
        });
    }

    /**
     * Update tenant information
     */
    public function updateTenant(User $tenant, array $data): User
    {
        return DB::transaction(function () use ($tenant, $data) {
            $tenant->update([
                'name' => $data['name'] ?? $tenant->name,
                'email' => $data['email'] ?? $tenant->email,
                'phone' => $data['phone'] ?? $tenant->phone,
            ]);

            if (isset($data['password'])) {
                $tenant->update(['password' => Hash::make($data['password'])]);
            }

            if (isset($data['profile']) && $tenant->profile) {
                $tenant->profile->update($data['profile']);
            }

            Log::info("Tenant updated: {$tenant->email}");

            return $tenant->fresh();
        });
    }

    /**
     * Get tenant dashboard statistics
     */
    public function getTenantDashboard(User $tenant): array
    {
        $activeContract = $this->getActiveContract($tenant);

        if (!$activeContract) {
            return [
                'has_active_contract' => false,
                'message' => 'No active rental contract found',
            ];
        }

        $currentInvoice = $this->getCurrentInvoice($activeContract);
        $upcomingInvoices = $this->getUpcomingInvoices($activeContract);
        $paymentHistory = $this->getRecentPayments($tenant);
        $meterReadings = $this->getLatestMeterReadings($activeContract);

        return [
            'has_active_contract' => true,
            'contract' => [
                'id' => $activeContract->id,
                'room' => $activeContract->room->room_number,
                'property' => $activeContract->room->property->name,
                'monthly_rent' => $activeContract->monthly_rent,
                'start_date' => $activeContract->start_date,
                'end_date' => $activeContract->end_date,
                'days_remaining' => now()->diffInDays($activeContract->end_date),
                'status' => $activeContract->status,
            ],
            'current_invoice' => $currentInvoice ? [
                'id' => $currentInvoice->id,
                'invoice_number' => $currentInvoice->invoice_number,
                'amount' => $currentInvoice->total_amount,
                'paid_amount' => $currentInvoice->paid_amount,
                'balance' => $currentInvoice->total_amount - $currentInvoice->paid_amount,
                'due_date' => $currentInvoice->due_date,
                'status' => $currentInvoice->status,
                'is_overdue' => $currentInvoice->due_date < now() && $currentInvoice->status !== 'paid',
            ] : null,
            'upcoming_invoices' => $upcomingInvoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => $invoice->total_amount,
                    'due_date' => $invoice->due_date,
                    'days_until_due' => now()->diffInDays($invoice->due_date),
                ];
            }),
            'payment_summary' => [
                'total_paid' => $paymentHistory->sum('amount'),
                'last_payment_date' => $paymentHistory->first()?->payment_date,
                'last_payment_amount' => $paymentHistory->first()?->amount,
                'payment_count' => $paymentHistory->count(),
            ],
            'meter_readings' => $meterReadings,
            'notifications' => $this->getTenantNotifications($tenant),
        ];
    }

    /**
     * Get active contract for tenant
     */
    public function getActiveContract(User $tenant): ?Contract
    {
        return Contract::where('user_id', $tenant->id)
            ->where('status', 'active')
            ->with(['room.property', 'room.amenities'])
            ->first();
    }

    /**
     * Get all contracts for a tenant
     */
    public function getTenantContracts(User $tenant, array $filters = []): Collection
    {
        $query = Contract::where('user_id', $tenant->id)
            ->with(['room.property', 'room.amenities']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['property_id'])) {
            $query->whereHas('room', function ($q) use ($filters) {
                $q->where('property_id', $filters['property_id']);
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get current invoice for contract
     */
    protected function getCurrentInvoice(Contract $contract): ?Invoice
    {
        return Invoice::where('contract_id', $contract->id)
            ->whereMonth('issue_date', now()->month)
            ->whereYear('issue_date', now()->year)
            ->with('lineItems')
            ->first();
    }

    /**
     * Get upcoming invoices for contract
     */
    protected function getUpcomingInvoices(Contract $contract, int $months = 3): Collection
    {
        return Invoice::where('contract_id', $contract->id)
            ->where('due_date', '>', now())
            ->where('status', '!=', 'paid')
            ->orderBy('due_date', 'asc')
            ->limit($months)
            ->get();
    }

    /**
     * Get tenant's payment history
     */
    public function getTenantPaymentHistory(User $tenant, int $limit = null): Collection
    {
        $query = Payment::whereHas('invoice.contract', function ($query) use ($tenant) {
            $query->where('user_id', $tenant->id);
        })
            ->with(['invoice.contract.room.property'])
            ->orderBy('payment_date', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get recent payments for tenant
     */
    protected function getRecentPayments(User $tenant, int $limit = 5): Collection
    {
        return $this->getTenantPaymentHistory($tenant, $limit);
    }

    /**
     * Get latest meter readings for contract
     */
    protected function getLatestMeterReadings(Contract $contract): array
    {
        $meters = $contract->room->meters;
        $readings = [];

        foreach ($meters as $meter) {
            $latestReading = MeterReading::where('meter_id', $meter->id)
                ->latest('reading_date')
                ->first();

            if ($latestReading) {
                $readings[] = [
                    'meter_type' => $meter->meter_type,
                    'meter_number' => $meter->meter_number,
                    'last_reading' => $latestReading->reading_value,
                    'reading_date' => $latestReading->reading_date,
                    'consumption' => $latestReading->consumption,
                ];
            }
        }

        return $readings;
    }

    /**
     * Get tenant notifications
     */
    protected function getTenantNotifications(User $tenant): array
    {
        $notifications = [];
        $activeContract = $this->getActiveContract($tenant);

        if ($activeContract) {
            // Contract expiring soon
            $daysUntilExpiry = now()->diffInDays($activeContract->end_date);
            if ($daysUntilExpiry <= 30 && $daysUntilExpiry > 0) {
                $notifications[] = [
                    'type' => 'warning',
                    'message' => "Your contract expires in {$daysUntilExpiry} days",
                    'action' => 'renew_contract',
                ];
            }

            // Overdue invoices
            $overdueInvoices = Invoice::where('contract_id', $activeContract->id)
                ->where('status', '!=', 'paid')
                ->where('due_date', '<', now())
                ->count();

            if ($overdueInvoices > 0) {
                $notifications[] = [
                    'type' => 'alert',
                    'message' => "You have {$overdueInvoices} overdue invoice(s)",
                    'action' => 'view_invoices',
                ];
            }

            // Pending maintenance requests
            if (class_exists(MaintenanceRequest::class)) {
                $pendingRequests = MaintenanceRequest::where('tenant_id', $tenant->id)
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->count();

                if ($pendingRequests > 0) {
                    $notifications[] = [
                        'type' => 'info',
                        'message' => "You have {$pendingRequests} maintenance request(s) in progress",
                        'action' => 'view_maintenance',
                    ];
                }
            }
        }

        return $notifications;
    }

    /**
     * Get tenant's invoices
     */
    public function getTenantInvoices(User $tenant, array $filters = []): Collection
    {
        $query = Invoice::whereHas('contract', function ($q) use ($tenant) {
            $q->where('user_id', $tenant->id);
        })->with(['contract.room.property', 'lineItems']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_date'])) {
            $query->where('issue_date', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->where('issue_date', '<=', $filters['to_date']);
        }

        return $query->orderBy('issue_date', 'desc')->get();
    }

    /**
     * Get tenant's documents
     */
    public function getTenantDocuments(User $tenant): Collection
    {
        // Get documents from all tenant's contracts
        return Document::whereHas('contract', function ($q) use ($tenant) {
            $q->where('user_id', $tenant->id);
        })->orderBy('created_at', 'desc')->get();
    }

    /**
     * Validate tenant can make payment
     */
    public function validatePaymentEligibility(User $tenant, Invoice $invoice): array
    {
        $errors = [];

        // Check if invoice belongs to tenant
        if ($invoice->contract->user_id !== $tenant->id) {
            $errors[] = 'This invoice does not belong to you';
        }

        // Check if invoice is already paid
        if ($invoice->status === 'paid') {
            $errors[] = 'This invoice has already been paid';
        }

        // Check if invoice is void
        if ($invoice->status === 'void') {
            $errors[] = 'This invoice has been voided and cannot be paid';
        }

        return $errors;
    }

    /**
     * Get tenant rental history
     */
    public function getRentalHistory(User $tenant): array
    {
        $contracts = Contract::where('user_id', $tenant->id)
            ->with(['room.property', 'invoices'])
            ->orderBy('start_date', 'desc')
            ->get();

        return $contracts->map(function ($contract) {
            return [
                'property' => $contract->room->property->name,
                'room' => $contract->room->room_number,
                'start_date' => $contract->start_date,
                'end_date' => $contract->end_date,
                'monthly_rent' => $contract->monthly_rent,
                'status' => $contract->status,
                'duration_months' => $contract->start_date->diffInMonths($contract->end_date),
                'total_paid' => $contract->invoices->where('status', 'paid')->sum('total_amount'),
                'payment_performance' => $this->calculatePaymentPerformance($contract),
            ];
        })->toArray();
    }

    /**
     * Calculate payment performance for a contract
     */
    protected function calculatePaymentPerformance(Contract $contract): array
    {
        $invoices = $contract->invoices;
        $totalInvoices = $invoices->count();

        if ($totalInvoices === 0) {
            return [
                'score' => 100,
                'rating' => 'No payment history',
            ];
        }

        $paidOnTime = $invoices->filter(function ($invoice) {
            return $invoice->status === 'paid' &&
                   $invoice->payment_date <= $invoice->due_date;
        })->count();

        $paidLate = $invoices->filter(function ($invoice) {
            return $invoice->status === 'paid' &&
                   $invoice->payment_date > $invoice->due_date;
        })->count();

        $unpaid = $invoices->where('status', '!=', 'paid')->count();

        $score = ($paidOnTime / $totalInvoices) * 100;

        $rating = match (true) {
            $score >= 90 => 'Excellent',
            $score >= 75 => 'Good',
            $score >= 60 => 'Fair',
            default => 'Poor',
        };

        return [
            'score' => round($score, 2),
            'rating' => $rating,
            'paid_on_time' => $paidOnTime,
            'paid_late' => $paidLate,
            'unpaid' => $unpaid,
            'total' => $totalInvoices,
        ];
    }

    /**
     * Request contract renewal
     */
    public function requestContractRenewal(User $tenant, Contract $contract, array $data): array
    {
        if ($contract->user_id !== $tenant->id) {
            return [
                'success' => false,
                'message' => 'You are not authorized to renew this contract',
            ];
        }

        if ($contract->status !== 'active') {
            return [
                'success' => false,
                'message' => 'Only active contracts can be renewed',
            ];
        }

        // Check if renewal request already exists
        if ($contract->renewal_requested) {
            return [
                'success' => false,
                'message' => 'A renewal request has already been submitted',
            ];
        }

        // Update contract with renewal request
        $contract->update([
            'renewal_requested' => true,
            'renewal_request_date' => now(),
            'renewal_notes' => $data['notes'] ?? null,
        ]);

        Log::info("Contract renewal requested by tenant: Contract #{$contract->id}, Tenant: {$tenant->email}");

        return [
            'success' => true,
            'message' => 'Your renewal request has been submitted',
            'contract_id' => $contract->id,
        ];
    }

    /**
     * Get tenant's utility consumption
     */
    public function getUtilityConsumption(User $tenant, Carbon $startDate, Carbon $endDate): array
    {
        $activeContract = $this->getActiveContract($tenant);

        if (!$activeContract) {
            return [];
        }

        $meters = $activeContract->room->meters;
        $consumption = [];

        foreach ($meters as $meter) {
            $readings = MeterReading::where('meter_id', $meter->id)
                ->whereBetween('reading_date', [$startDate, $endDate])
                ->orderBy('reading_date', 'asc')
                ->get();

            $monthlyData = [];
            foreach ($readings as $reading) {
                $monthlyData[] = [
                    'month' => $reading->reading_date->format('Y-m'),
                    'reading' => $reading->reading_value,
                    'consumption' => $reading->consumption,
                    'cost' => $reading->consumption * $meter->rate_per_unit,
                ];
            }

            $consumption[$meter->meter_type] = [
                'meter_number' => $meter->meter_number,
                'unit' => $meter->meter_type === 'electricity' ? 'kWh' : 'm³',
                'rate' => $meter->rate_per_unit,
                'monthly_data' => $monthlyData,
                'total_consumption' => collect($monthlyData)->sum('consumption'),
                'total_cost' => collect($monthlyData)->sum('cost'),
                'average_consumption' => collect($monthlyData)->avg('consumption'),
            ];
        }

        return $consumption;
    }

    /**
     * Submit maintenance request
     */
    public function submitMaintenanceRequest(User $tenant, array $data): ?MaintenanceRequest
    {
        $activeContract = $this->getActiveContract($tenant);

        if (!$activeContract) {
            Log::warning("Tenant {$tenant->email} attempted to submit maintenance request without active contract");
            return null;
        }

        if (class_exists(MaintenanceRequest::class)) {
            $request = MaintenanceRequest::create([
                'tenant_id' => $tenant->id,
                'contract_id' => $activeContract->id,
                'room_id' => $activeContract->room_id,
                'title' => $data['title'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? 'normal',
                'status' => 'pending',
                'category' => $data['category'] ?? 'general',
                'images' => $data['images'] ?? [],
            ]);

            Log::info("Maintenance request submitted: ID {$request->id} by tenant {$tenant->email}");

            return $request;
        }

        return null;
    }

    /**
     * Check if tenant is eligible for contract
     */
    public function checkEligibility(User $tenant): array
    {
        // Check if tenant has active contracts
        $activeContracts = Contract::where('user_id', $tenant->id)
            ->where('status', 'active')
            ->count();

        // Check payment history
        $paymentHistory = $this->getTenantPaymentHistory($tenant);
        $totalInvoices = Invoice::whereHas('contract', function ($q) use ($tenant) {
            $q->where('user_id', $tenant->id);
        })->count();

        $paidInvoices = Invoice::whereHas('contract', function ($q) use ($tenant) {
            $q->where('user_id', $tenant->id);
        })->where('status', 'paid')->count();

        $paymentRate = $totalInvoices > 0 ? ($paidInvoices / $totalInvoices) * 100 : 100;

        // Check for blacklist or restrictions
        $isBlacklisted = $tenant->is_blacklisted ?? false;

        return [
            'eligible' => !$isBlacklisted && $paymentRate >= 60,
            'active_contracts' => $activeContracts,
            'payment_rate' => round($paymentRate, 2),
            'is_blacklisted' => $isBlacklisted,
            'eligibility_notes' => $this->getEligibilityNotes($paymentRate, $isBlacklisted, $activeContracts),
        ];
    }

    /**
     * Get eligibility notes
     */
    protected function getEligibilityNotes(float $paymentRate, bool $isBlacklisted, int $activeContracts): array
    {
        $notes = [];

        if ($isBlacklisted) {
            $notes[] = 'Tenant is currently blacklisted';
        }

        if ($paymentRate < 60) {
            $notes[] = 'Poor payment history (below 60% payment rate)';
        } elseif ($paymentRate < 80) {
            $notes[] = 'Fair payment history (60-80% payment rate)';
        } else {
            $notes[] = 'Good payment history';
        }

        if ($activeContracts > 0) {
            $notes[] = "Currently has {$activeContracts} active contract(s)";
        }

        return $notes;
    }

    /**
     * Archive tenant (soft delete)
     */
    public function archiveTenant(User $tenant): bool
    {
        // Check for active contracts
        $activeContracts = Contract::where('user_id', $tenant->id)
            ->where('status', 'active')
            ->count();

        if ($activeContracts > 0) {
            Log::warning("Cannot archive tenant {$tenant->email} - has active contracts");
            return false;
        }

        $tenant->delete(); // Soft delete

        Log::info("Tenant archived: {$tenant->email}");

        return true;
    }

    /**
     * Restore archived tenant
     */
    public function restoreTenant($tenantId): ?User
    {
        $tenant = User::withTrashed()->find($tenantId);

        if ($tenant && $tenant->trashed()) {
            $tenant->restore();
            Log::info("Tenant restored: {$tenant->email}");
            return $tenant;
        }

        return null;
    }
}