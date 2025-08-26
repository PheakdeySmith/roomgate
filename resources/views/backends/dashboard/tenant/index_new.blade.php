@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
@endphp

@extends('backends.layouts.app')

@section('title', 'My Home | RoomGate')

@push('style')
<style>
    /* New Tenant Dashboard Design - 2025 */
    :root {
        /* Main color palette */
        --brand-primary: #4F46E5;
        --brand-secondary: #7C3AED;
        --brand-accent: #EC4899;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
        --info: #3B82F6;
        
        /* Neutral palette */
        --neutral-50: #F9FAFB;
        --neutral-100: #F3F4F6;
        --neutral-200: #E5E7EB;
        --neutral-300: #D1D5DB;
        --neutral-400: #9CA3AF;
        --neutral-500: #6B7280;
        --neutral-600: #4B5563;
        --neutral-700: #374151;
        --neutral-800: #1F2937;
        --neutral-900: #111827;
        
        /* Sizing */
        --border-radius-sm: 0.375rem;
        --border-radius-md: 0.5rem;
        --border-radius-lg: 0.75rem;
        --border-radius-xl: 1rem;
        
        /* Shadows */
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        
        /* Transitions */
        --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
        --transition-normal: 300ms cubic-bezier(0.4, 0, 0.2, 1);
        --transition-slow: 500ms cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Dark mode adjustments */
    [data-bs-theme="dark"] {
        --neutral-50: #18181B;
        --neutral-100: #27272A;
        --neutral-200: #3F3F46;
        --neutral-300: #52525B;
        --neutral-400: #71717A;
        --neutral-500: #A1A1AA;
        --neutral-600: #D4D4D8;
        --neutral-700: #E4E4E7;
        --neutral-800: #F4F4F5;
        --neutral-900: #FAFAFA;
        
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.2);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
    }
    
    /* Layout and container styles */
    .tenant-dashboard {
        max-width: 1600px;
        margin: 0 auto;
    }
    
    .tenant-header {
        margin-bottom: 2rem;
    }
    
    .tenant-header h1 {
        font-weight: 700;
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
        background: linear-gradient(to right, var(--brand-primary), var(--brand-accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }
    
    .tenant-section {
        margin-bottom: 2rem;
    }
    
    /* Card styles */
    .tenant-card {
        background-color: var(--bs-body-bg);
        border-radius: var(--border-radius-lg);
        border: 1px solid var(--neutral-200);
        box-shadow: var(--shadow-md);
        height: 100%;
        transition: transform var(--transition-fast), box-shadow var(--transition-fast);
        overflow: hidden;
    }
    
    .tenant-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    
    .tenant-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--neutral-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .tenant-card-title {
        font-weight: 600;
        font-size: 1.125rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .tenant-card-title i {
        color: var(--brand-primary);
        font-size: 1.25rem;
    }
    
    .tenant-card-body {
        padding: 1.5rem;
    }
    
    .tenant-card-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--neutral-200);
        background-color: var(--neutral-50);
    }
    
    /* Hero card styles */
    .tenant-hero-card {
        background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
        color: white;
        border: none;
        position: relative;
        overflow: hidden;
    }
    
    .tenant-hero-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.7;
    }
    
    .tenant-hero-card .tenant-card-body {
        position: relative;
        z-index: 2;
    }
    
    .tenant-hero-card .text-muted {
        color: rgba(255, 255, 255, 0.85) !important;
    }
    
    /* Stat card styles */
    .tenant-stat-card {
        text-align: center;
        padding: 1.5rem;
    }
    
    .tenant-stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
        background: var(--neutral-100);
        color: var(--brand-primary);
        transition: transform var(--transition-fast);
    }
    
    .tenant-stat-card:hover .tenant-stat-icon {
        transform: scale(1.1);
    }
    
    .tenant-stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        color: var(--neutral-900);
    }
    
    .tenant-stat-label {
        font-size: 0.875rem;
        color: var(--neutral-500);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    /* List styles */
    .tenant-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .tenant-list-item {
        padding: 1rem 0;
        border-bottom: 1px solid var(--neutral-200);
        transition: background-color var(--transition-fast);
    }
    
    .tenant-list-item:last-child {
        border-bottom: none;
    }
    
    .tenant-list-item:hover {
        background-color: var(--neutral-50);
    }
    
    .tenant-list-item-clickable {
        cursor: pointer;
    }
    
    /* Badge styles */
    .tenant-badge {
        display: inline-block;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 600;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: var(--border-radius-sm);
        transition: all var(--transition-fast);
    }
    
    .tenant-badge-success {
        background-color: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }
    
    .tenant-badge-warning {
        background-color: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }
    
    .tenant-badge-danger {
        background-color: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }
    
    /* Button styles */
    .tenant-btn {
        display: inline-block;
        font-weight: 500;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        user-select: none;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        border-radius: var(--border-radius-md);
        transition: all var(--transition-fast);
        border: 1px solid transparent;
    }
    
    .tenant-btn-primary {
        background-color: var(--brand-primary);
        color: white;
    }
    
    .tenant-btn-primary:hover {
        background-color: var(--brand-secondary);
        transform: translateY(-2px);
    }
    
    .tenant-btn-outline {
        background-color: transparent;
        border-color: var(--brand-primary);
        color: var(--brand-primary);
    }
    
    .tenant-btn-outline:hover {
        background-color: var(--brand-primary);
        color: white;
    }
    
    .tenant-btn-white {
        background-color: white;
        color: var(--brand-primary);
        box-shadow: var(--shadow-sm);
    }
    
    .tenant-btn-white:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }
    
    .tenant-btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
    
    /* Progress bar */
    .tenant-progress {
        height: 0.5rem;
        background-color: var(--neutral-200);
        border-radius: var(--border-radius-sm);
        overflow: hidden;
    }
    
    .tenant-progress-bar {
        height: 100%;
        border-radius: var(--border-radius-sm);
    }
    
    /* Utility icons */
    .tenant-utility-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--border-radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    
    /* Link styles */
    .tenant-link {
        color: var(--brand-primary);
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: all var(--transition-fast);
    }
    
    .tenant-link:hover {
        color: var(--brand-secondary);
    }
    
    .tenant-link i {
        font-size: 1rem;
        transition: transform var(--transition-fast);
    }
    
    .tenant-link:hover i {
        transform: translateX(3px);
    }
    
    /* Navigation styles */
    .tenant-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: var(--bs-body-bg);
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
        z-index: 1000;
        padding: 0.5rem 0;
    }
    
    .tenant-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        padding: 0.5rem;
        color: var(--neutral-500);
        text-decoration: none;
        transition: all var(--transition-fast);
    }
    
    .tenant-nav-item.active {
        color: var(--brand-primary);
    }
    
    .tenant-nav-item i {
        font-size: 1.25rem;
    }
    
    .tenant-nav-item span {
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    /* Notification styles */
    .tenant-notification {
        padding: 1rem;
        border-radius: var(--border-radius-md);
        margin-bottom: 1rem;
        border-left: 4px solid transparent;
    }
    
    .tenant-notification-success {
        background-color: rgba(16, 185, 129, 0.1);
        border-left-color: var(--success);
    }
    
    .tenant-notification-warning {
        background-color: rgba(245, 158, 11, 0.1);
        border-left-color: var(--warning);
    }
    
    .tenant-notification-danger {
        background-color: rgba(239, 68, 68, 0.1);
        border-left-color: var(--danger);
    }
    
    .tenant-notification-info {
        background-color: rgba(59, 130, 246, 0.1);
        border-left-color: var(--info);
    }
    
    /* Chart styles */
    .tenant-chart-container {
        position: relative;
        height: 280px;
    }
    
    /* Animation keyframes */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .tenant-fade-in {
        animation: fadeIn 0.5s ease forwards;
    }
    
    .tenant-fade-in-delay-1 {
        animation-delay: 0.1s;
        opacity: 0;
    }
    
    .tenant-fade-in-delay-2 {
        animation-delay: 0.2s;
        opacity: 0;
    }
    
    .tenant-fade-in-delay-3 {
        animation-delay: 0.3s;
        opacity: 0;
    }
    
    .tenant-fade-in-delay-4 {
        animation-delay: 0.4s;
        opacity: 0;
    }
    
    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .tenant-header h1 {
            font-size: 1.5rem;
        }
        
        .tenant-stat-value {
            font-size: 1.5rem;
        }
        
        .tenant-card-header {
            padding: 1rem;
        }
        
        .tenant-card-body {
            padding: 1rem;
        }
        
        .tenant-chart-container {
            height: 220px;
        }
        
        .tenant-stat-icon {
            width: 56px;
            height: 56px;
            font-size: 1.25rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid tenant-dashboard py-4">
    <!-- Dashboard Header -->
    <div class="tenant-header tenant-fade-in">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Welcome back, {{ Auth::user()->first_name ?? Auth::user()->name }}</h1>
                @if ($currentContract)
                <p class="text-muted">{{ $currentContract->room->property->name }} • Room {{ $currentContract->room->room_number }}</p>
                @endif
            </div>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="tenant-btn tenant-btn-outline rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-bell"></i>
                        @if(!empty($notifications))
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 0.25rem 0.4rem;">
                            {{ count($notifications) }}
                        </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        @if(!empty($notifications))
                            @foreach($notifications as $notification)
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle p-1" style="background-color: {{ $notification['type'] == 'danger' ? 'var(--danger)' : ($notification['type'] == 'warning' ? 'var(--warning)' : 'var(--info)') }}10;">
                                            <i class="ti ti-{{ $notification['icon'] }}" style="color: {{ $notification['type'] == 'danger' ? 'var(--danger)' : ($notification['type'] == 'warning' ? 'var(--warning)' : 'var(--info)') }};"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="mb-0 text-truncate" style="max-width: 250px;">{{ $notification['message'] }}</p>
                                        <small class="text-muted">Just now</small>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        @else
                            <li><p class="dropdown-item text-muted mb-0">No new notifications</p></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center tenant-link" href="#">See all notifications <i class="ti ti-arrow-right"></i></a></li>
                    </ul>
                </div>
                
                <button class="tenant-btn tenant-btn-outline rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;" id="light-dark-mode" type="button">
                    <i class="ti ti-moon"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Balance Summary Card -->
        <div class="col-12 tenant-fade-in">
            <div class="tenant-card tenant-hero-card">
                <div class="tenant-card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="display-5 fw-bold mb-0">
                                @if($totalBalanceDue > 0)
                                    ${{ number_format($totalBalanceDue, 2) }}
                                @else
                                    All Paid Up!
                                @endif
                            </h3>
                            <div class="text-muted mt-2 fs-5">
                                @if($stats['pending_invoices'] > 0)
                                    {{ $stats['pending_invoices'] }} pending {{ Str::plural('invoice', $stats['pending_invoices']) }}
                                @else
                                    You're all caught up on payments
                                @endif
                            </div>
                            
                            <div class="mt-4">
                                @if($nextInvoice)
                                <button class="tenant-btn tenant-btn-white tenant-btn-lg invoice-details-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#invoiceDetailsModal"
                                        data-invoice-id="{{ $nextInvoice->id }}">
                                    <i class="ti ti-credit-card me-2"></i> Pay Now
                                </button>
                                @endif
                                
                                <a href="{{ route('tenant.invoices') }}" class="tenant-btn tenant-btn-outline border-white text-white tenant-btn-lg ms-2">
                                    View All Invoices
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 d-none d-md-block">
                            <div class="text-center">
                                <img src="{{ asset('assets/images/payment-illustration.svg') }}" alt="Payment" class="img-fluid" style="max-height: 180px;" onerror="this.style.display='none'">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats Row -->
        <div class="col-6 col-md-3 tenant-fade-in tenant-fade-in-delay-1">
            <div class="tenant-card">
                <div class="tenant-stat-card">
                    <div class="tenant-stat-icon" style="background: linear-gradient(135deg, #10B981, #047857); color: white;">
                        <i class="ti ti-wallet"></i>
                    </div>
                    <div class="tenant-stat-value">
                        @if(($totalPaidThisMonth ?? 0) > 0)
                            ${{ number_format($totalPaidThisMonth, 2) }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                    <div class="tenant-stat-label">Paid This Month</div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3 tenant-fade-in tenant-fade-in-delay-2">
            <div class="tenant-card">
                <div class="tenant-stat-card">
                    <div class="tenant-stat-icon" style="background: linear-gradient(135deg, #4F46E5, #3730A3); color: white;">
                        <i class="ti ti-file-invoice"></i>
                    </div>
                    <div class="tenant-stat-value">{{ $stats['total_invoices'] }}</div>
                    <div class="tenant-stat-label">Total Invoices</div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3 tenant-fade-in tenant-fade-in-delay-3">
            <div class="tenant-card">
                <div class="tenant-stat-card">
                    <div class="tenant-stat-icon" style="background: linear-gradient(135deg, #F59E0B, #D97706); color: white;">
                        <i class="ti ti-calendar"></i>
                    </div>
                    <div class="tenant-stat-value">{{ $stats['contract_days_left'] }}</div>
                    <div class="tenant-stat-label">Days Left</div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3 tenant-fade-in tenant-fade-in-delay-4">
            <div class="tenant-card">
                <div class="tenant-stat-card">
                    <div class="tenant-stat-icon" style="background: linear-gradient(135deg, #3B82F6, #1D4ED8); color: white;">
                        <i class="ti ti-check"></i>
                    </div>
                    <div class="tenant-stat-value">{{ $stats['paid_invoices'] }}</div>
                    <div class="tenant-stat-label">Paid Invoices</div>
                </div>
            </div>
        </div>
        
        <!-- Payment History Chart -->
        <div class="col-12 col-lg-7 tenant-fade-in tenant-fade-in-delay-1">
            <div class="tenant-card">
                <div class="tenant-card-header">
                    <h5 class="tenant-card-title">
                        <i class="ti ti-chart-bar"></i> Payment History
                    </h5>
                    <a href="{{ route('tenant.invoices') }}" class="tenant-link">
                        View All <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
                <div class="tenant-card-body">
                    <div class="tenant-chart-container">
                        <div id="payment-history-chart"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Invoices -->
        <div class="col-12 col-lg-5 tenant-fade-in tenant-fade-in-delay-2">
            <div class="tenant-card">
                <div class="tenant-card-header">
                    <h5 class="tenant-card-title">
                        <i class="ti ti-receipt"></i> Recent Invoices
                    </h5>
                    <a href="{{ route('tenant.invoices') }}" class="tenant-link">
                        View All <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
                <div class="tenant-card-body p-0">
                    <ul class="tenant-list">
                        @forelse($recentInvoices as $invoice)
                        <li class="tenant-list-item tenant-list-item-clickable d-flex align-items-center px-3 py-3 invoice-details-btn"
                            data-bs-toggle="modal" 
                            data-bs-target="#invoiceDetailsModal" 
                            data-invoice-id="{{ $invoice->id }}">
                            <div class="me-3">
                                @php
                                    $bgColor = $invoice->status == 'paid' ? 'var(--success)' : ($invoice->status == 'overdue' ? 'var(--danger)' : 'var(--warning)');
                                    $bgColorClass = $invoice->status == 'paid' ? 'success' : ($invoice->status == 'overdue' ? 'danger' : 'warning');
                                @endphp
                                <div class="rounded-circle p-2" 
                                    style="background-color: {{ $bgColor }}20; color: {{ $bgColor }}">
                                    <i class="ti ti-receipt"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium">Invoice #{{ $invoice->invoice_number }}</div>
                                <div class="small text-muted">{{ $invoice->issue_date->format('M d, Y') }} • Due: {{ $invoice->due_date->format('M d') }}</div>
                            </div>
                            <div class="text-end">
                                <div class="{{ $invoice->status == 'paid' ? 'text-success' : '' }} fw-medium">
                                    ${{ number_format($invoice->total_amount, 2) }}
                                </div>
                                <span class="tenant-badge tenant-badge-{{ $bgColorClass }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
                        </li>
                        @empty
                        <li class="tenant-list-item py-4 text-center">
                            <div class="text-muted">
                                <i class="ti ti-receipt fs-3 mb-2"></i>
                                <p class="mb-0">No recent invoices found</p>
                            </div>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Utility Usage -->
        <div class="col-12 col-lg-6 tenant-fade-in tenant-fade-in-delay-3">
            <div class="tenant-card">
                <div class="tenant-card-header">
                    <h5 class="tenant-card-title">
                        <i class="ti ti-bolt"></i> Utility Usage
                    </h5>
                    <a href="{{ route('tenant.utility-usage') }}" class="tenant-link">
                        View All <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
                <div class="tenant-card-body">
                    <div class="tenant-chart-container">
                        <div id="utility-usage-chart"></div>
                    </div>
                    
                    @php
                        // Prepare data for utility progress bars
                        $utilityPercentages = [];
                        $currentMonth = now()->format('M Y');
                        $lastMonth = now()->subMonth()->format('M Y');
                        $colors = ['primary', 'success', 'warning', 'danger'];
                        $colorIndex = 0;
                        
                        foreach ($utilityData as $utilityName => $monthlyData) {
                            if (isset($monthlyData[$currentMonth]) && isset($monthlyData[$lastMonth]) && $monthlyData[$lastMonth] > 0) {
                                $percentage = min(100, round(($monthlyData[$currentMonth] / $monthlyData[$lastMonth]) * 100));
                                $utilityPercentages[$utilityName] = [
                                    'percentage' => $percentage,
                                    'current' => $monthlyData[$currentMonth],
                                    'last' => $monthlyData[$lastMonth],
                                    'color' => $colors[$colorIndex % count($colors)]
                                ];
                                $colorIndex++;
                            }
                        }
                        
                        $colorMapping = [
                            'primary' => '--brand-primary',
                            'success' => '--success',
                            'warning' => '--warning',
                            'danger' => '--danger'
                        ];
                    @endphp
                    
                    <div class="row mt-4">
                        @foreach($utilityPercentages as $name => $data)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">{{ $name }}</span>
                                <span class="text-muted small">{{ $data['percentage'] }}%</span>
                            </div>
                            <div class="tenant-progress">
                                <div class="tenant-progress-bar" 
                                    style="width: {{ min(100, $data['percentage']) }}%; background-color: var({{ $colorMapping[$data['color']] }})"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Last Month: {{ $data['last'] }}</small>
                                <small class="text-muted">This Month: {{ $data['current'] }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Utility Bills -->
        <div class="col-12 col-lg-6 tenant-fade-in tenant-fade-in-delay-4">
            <div class="tenant-card">
                <div class="tenant-card-header">
                    <h5 class="tenant-card-title">
                        <i class="ti ti-receipt-2"></i> Recent Utility Bills
                    </h5>
                    <a href="{{ route('tenant.utility-bills') }}" class="tenant-link">
                        View All <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
                <div class="tenant-card-body p-0">
                    <ul class="tenant-list">
                        @forelse($recentUtilityBills as $bill)
                        <li class="tenant-list-item px-3 py-3">
                            @php
                                $icon = 'bolt';
                                $color = 'primary';
                                
                                if (strtolower($bill->utilityType->name) == 'water') {
                                    $icon = 'droplet';
                                    $color = 'info';
                                } elseif (strtolower($bill->utilityType->name) == 'gas') {
                                    $icon = 'flame';
                                    $color = 'warning';
                                } elseif (strtolower($bill->utilityType->name) == 'internet') {
                                    $icon = 'wifi';
                                    $color = 'primary';
                                }
                                
                                $statusColor = $bill->is_paid ? 'var(--success)' : 'var(--warning)';
                                $statusColorClass = $bill->is_paid ? 'success' : 'warning';
                                
                                // Map colors to CSS variables
                                $colorVar = $color == 'primary' ? '--brand-primary' : 
                                          ($color == 'success' ? '--success' : 
                                          ($color == 'info' ? '--info' : '--warning'));
                            @endphp
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="tenant-utility-icon" style="background-color: var({{ $colorVar }})20; color: var({{ $colorVar }})">
                                        <i class="ti ti-{{ $icon }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium">{{ $bill->utilityType->name }}</div>
                                    <div class="small text-muted">
                                        {{ Carbon::parse($bill->billing_period_start)->format('M d') }} - 
                                        {{ Carbon::parse($bill->billing_period_end)->format('M d, Y') }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-medium">
                                        ${{ number_format($bill->amount, 2) }}
                                    </div>
                                    <span class="tenant-badge tenant-badge-{{ $statusColorClass }}">
                                        {{ $bill->is_paid ? 'Paid' : 'Pending' }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="tenant-list-item py-4 text-center">
                            <div class="text-muted">
                                <i class="ti ti-bolt fs-3 mb-2"></i>
                                <p class="mb-0">No utility bills found</p>
                            </div>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Important Notifications -->
        @if(!empty($notifications))
        <div class="col-12 tenant-fade-in tenant-fade-in-delay-1">
            <div class="tenant-card">
                <div class="tenant-card-header">
                    <h5 class="tenant-card-title">
                        <i class="ti ti-bell"></i> Important Notifications
                    </h5>
                </div>
                <div class="tenant-card-body">
                    @foreach($notifications as $notification)
                    @php
                        $notifType = $notification['type'] == 'danger' ? 'danger' : 
                                    ($notification['type'] == 'warning' ? 'warning' : 'info');
                    @endphp
                    <div class="tenant-notification tenant-notification-{{ $notifType }} mb-3">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-{{ $notification['icon'] }} fs-4 me-3"></i>
                            <div>
                                <p class="mb-0 fw-medium">{{ $notification['message'] }}</p>
                                <small class="text-muted">Today</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        <!-- Mobile Navigation for small screens -->
        <div class="d-md-none tenant-nav">
            <div class="d-flex justify-content-around">
                <a href="#" class="tenant-nav-item active">
                    <i class="ti ti-home"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('tenant.invoices') }}" class="tenant-nav-item">
                    <i class="ti ti-receipt"></i>
                    <span>Invoices</span>
                </a>
                <a href="{{ route('tenant.utility-bills') }}" class="tenant-nav-item">
                    <i class="ti ti-bolt"></i>
                    <span>Utilities</span>
                </a>
                <a href="{{ route('tenant.utility-usage') }}" class="tenant-nav-item">
                    <i class="ti ti-chart-line"></i>
                    <span>Usage</span>
                </a>
                <a href="#" class="tenant-nav-item">
                    <i class="ti ti-user"></i>
                    <span>Profile</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Details Modal -->
<div class="modal fade" id="invoiceDetailsModal" tabindex="-1" aria-labelledby="invoiceDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down modal-md">
        <div class="modal-content" style="border-radius: var(--border-radius-lg); overflow: hidden; border: none; box-shadow: var(--shadow-xl);">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="invoiceDetailsModalLabel">Invoice Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="invoice-details-content">
                    <!-- Invoice details will be loaded here -->
                    <div class="text-center p-5">
                        <div class="spinner-border" style="color: var(--brand-primary)" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading invoice details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="tenant-btn tenant-btn-outline" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="{{ asset('assets/js/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const brandPrimary = getComputedStyle(document.documentElement).getPropertyValue('--brand-primary').trim();
            const brandSecondary = getComputedStyle(document.documentElement).getPropertyValue('--brand-secondary').trim();
            const success = getComputedStyle(document.documentElement).getPropertyValue('--success').trim();
            const warning = getComputedStyle(document.documentElement).getPropertyValue('--warning').trim();
            const danger = getComputedStyle(document.documentElement).getPropertyValue('--danger').trim();
            const info = getComputedStyle(document.documentElement).getPropertyValue('--info').trim();
            const neutral300 = getComputedStyle(document.documentElement).getPropertyValue('--neutral-300').trim();
            const neutral800 = getComputedStyle(document.documentElement).getPropertyValue('--neutral-800').trim();
            
            // Handle invoice details modal
            $('.invoice-details-btn').on('click', function() {
                const invoiceId = $(this).data('invoice-id');
                loadInvoiceDetails(invoiceId);
            });

            // --- Payment History Chart ---
            const billedChartData = @json($billedChart ?? []);
            const paidChartData = @json($paidChart ?? []);
            
            if (Object.keys(billedChartData).length > 0) {
                const paymentHistoryOptions = {
                    series: [
                        {
                            name: 'Billed',
                            data: Object.values(billedChartData)
                        },
                        {
                            name: 'Paid',
                            data: Object.values(paidChartData)
                        }
                    ],
                    chart: {
                        height: 280,
                        type: 'area',
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'inherit',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        }
                    },
                    colors: [brandPrimary, success],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.6,
                            opacityTo: 0.1,
                            stops: [0, 90, 100]
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    xaxis: {
                        categories: Object.keys(billedChartData),
                        labels: {
                            style: {
                                fontSize: '12px',
                                fontWeight: 500,
                                colors: isDarkMode ? neutral300 : neutral800
                            }
                        },
                        axisTicks: {
                            show: false
                        },
                        axisBorder: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(value) {
                                return '$' + value.toFixed(0);
                            },
                            style: {
                                colors: isDarkMode ? neutral300 : neutral800
                            }
                        }
                    },
                    grid: {
                        borderColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)',
                        strokeDashArray: 4,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        },
                        yaxis: {
                            lines: {
                                show: true
                            }
                        },
                        padding: {
                            top: 0,
                            right: 0,
                            bottom: 0,
                            left: 10
                        }
                    },
                    tooltip: {
                        theme: isDarkMode ? 'dark' : 'light',
                        y: {
                            formatter: function(value) {
                                return "$" + value.toFixed(2);
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        labels: {
                            colors: isDarkMode ? neutral300 : neutral800
                        }
                    }
                };
                
                try {
                    new ApexCharts(document.querySelector("#payment-history-chart"), paymentHistoryOptions).render();
                } catch (error) {
                    console.error("Error rendering payment chart:", error);
                    document.querySelector("#payment-history-chart").innerHTML = '<div class="text-center text-muted py-4"><i class="ti ti-chart-bar fs-3 mb-2"></i><p class="mb-0">Error loading chart</p></div>';
                }
            } else {
                document.querySelector("#payment-history-chart").innerHTML = '<div class="text-center text-muted py-4"><i class="ti ti-chart-bar fs-3 mb-2"></i><p class="mb-0">No payment history available</p></div>';
            }

            // --- Utility Usage Chart ---
            const utilityData = @json($utilityData ?? []);
            const utilityChartSeries = [];
            const months = @json($months ?? []);
            
            // Create series data for each utility type - limit to latest 3 months for mobile
            for (const [utilityName, monthlyUsage] of Object.entries(utilityData)) {
                const data = Object.entries(monthlyUsage)
                    .slice(-3) // Get only last 3 months for mobile view
                    .map(([_, value]) => value);
                
                utilityChartSeries.push({
                    name: utilityName,
                    data: data
                });
            }
            
            // Get last 3 months for x-axis
            const lastThreeMonths = Object.keys(utilityData[Object.keys(utilityData)[0]] || {}).slice(-3);
            
            if (utilityChartSeries.length > 0) {
                const utilityUsageOptions = {
                    series: utilityChartSeries,
                    chart: {
                        height: 280,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'inherit',
                        dropShadow: {
                            enabled: true,
                            top: 3,
                            left: 2,
                            blur: 4,
                            opacity: 0.1
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3,
                        lineCap: 'round'
                    },
                    grid: {
                        borderColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)',
                        strokeDashArray: 4,
                        xaxis: {
                            lines: {
                                show: false
                            }
                        },
                        padding: {
                            top: 10,
                            right: 0,
                            bottom: 0,
                            left: 10
                        }
                    },
                    xaxis: {
                        categories: lastThreeMonths,
                        labels: {
                            style: {
                                fontSize: '12px',
                                fontWeight: 500,
                                colors: isDarkMode ? neutral300 : neutral800
                            }
                        },
                        axisTicks: {
                            show: false
                        },
                        axisBorder: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(value) {
                                return value.toFixed(1);
                            },
                            style: {
                                colors: isDarkMode ? neutral300 : neutral800
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '12px',
                        labels: {
                            colors: isDarkMode ? neutral300 : neutral800
                        },
                        markers: {
                            radius: 3
                        }
                    },
                    colors: [brandPrimary, success, warning, danger],
                    markers: {
                        size: 5,
                        strokeWidth: 0,
                        hover: {
                            size: 7
                        }
                    },
                    tooltip: {
                        theme: isDarkMode ? 'dark' : 'light',
                        y: {
                            formatter: function(value) {
                                return value.toFixed(2);
                            }
                        }
                    }
                };
                
                try {
                    new ApexCharts(document.querySelector("#utility-usage-chart"), utilityUsageOptions).render();
                } catch (error) {
                    console.error("Error rendering utility chart:", error);
                    document.querySelector("#utility-usage-chart").innerHTML = '<div class="text-center text-muted py-4"><i class="ti ti-bolt fs-3 mb-2"></i><p class="mb-0">Error loading chart</p></div>';
                }
            } else {
                document.querySelector("#utility-usage-chart").innerHTML = '<div class="text-center text-muted py-4"><i class="ti ti-bolt fs-3 mb-2"></i><p class="mb-0">No utility usage data available</p></div>';
            }

            function loadInvoiceDetails(invoiceId) {
                // Show loading spinner
                $('#invoice-details-content').html(`
                    <div class="text-center p-5">
                        <div class="spinner-border" style="color: var(--brand-primary)" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading invoice details...</p>
                    </div>
                `);
                
                // Make AJAX request to get invoice details
                $.ajax({
                    url: `/tenant/invoices/${invoiceId}/details`,
                    method: 'GET',
                    success: function(response) {
                        // Format the date
                        const issueDate = new Date(response.invoice.issue_date).toLocaleDateString('en-US', {
                            year: 'numeric', month: 'short', day: 'numeric'
                        });
                        const dueDate = new Date(response.invoice.due_date).toLocaleDateString('en-US', {
                            year: 'numeric', month: 'short', day: 'numeric'
                        });
                        
                        // Create HTML for line items
                        let lineItemsHtml = '';
                        let subtotal = 0;
                        
                        response.line_items.forEach(item => {
                            subtotal += parseFloat(item.amount);
                            lineItemsHtml += `
                                <li class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <span>${item.description}</span>
                                    <span class="fw-medium">$${parseFloat(item.amount).toFixed(2)}</span>
                                </li>
                            `;
                        });
                        
                        // Generate status badge style
                        let statusColor, statusBg, statusClass;
                        if (response.invoice.status === 'paid') {
                            statusColor = 'var(--success)';
                            statusBg = 'var(--success)' + '20';
                            statusClass = 'success';
                        } else if (response.invoice.status === 'overdue') {
                            statusColor = 'var(--danger)';
                            statusBg = 'var(--danger)' + '20';
                            statusClass = 'danger';
                        } else {
                            statusColor = 'var(--warning)';
                            statusBg = 'var(--warning)' + '20';
                            statusClass = 'warning';
                        }
                        
                        // Build the invoice details HTML with modern styling
                        const invoiceHtml = `
                            <div class="p-4">
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="fw-bold mb-0">Invoice #${response.invoice.invoice_number}</h4>
                                        <span class="tenant-badge tenant-badge-${statusClass}">
                                            ${response.invoice.status.charAt(0).toUpperCase() + response.invoice.status.slice(1)}
                                        </span>
                                    </div>
                                    <div class="mb-4">
                                        <div class="text-muted mb-2">Issue Date: <span class="fw-medium">${issueDate}</span></div>
                                        <div class="text-muted mb-2">Due Date: <span class="fw-medium">${dueDate}</span></div>
                                        <div class="text-muted mb-2">Property: <span class="fw-medium">${response.property.name}</span></div>
                                        <div class="text-muted">Room: <span class="fw-medium">${response.room.room_number}</span></div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h5 class="fw-bold mb-3">Line Items</h5>
                                    <ul class="list-unstyled">
                                        ${lineItemsHtml}
                                    </ul>
                                </div>
                                
                                <div style="background-color: var(--neutral-100); padding: 20px; border-radius: var(--border-radius-md);">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal</span>
                                        <span class="fw-medium">$${subtotal.toFixed(2)}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Paid Amount</span>
                                        <span class="fw-medium">$${parseFloat(response.invoice.paid_amount).toFixed(2)}</span>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold pt-3 border-top">
                                        <span>Balance</span>
                                        <span style="color: var(--brand-primary); font-size: 1.2rem;">$${(parseFloat(response.invoice.total_amount) - parseFloat(response.invoice.paid_amount)).toFixed(2)}</span>
                                    </div>
                                </div>
                                
                                ${response.invoice.status !== 'paid' ? 
                                  `<div class="mt-4 d-grid">
                                      <button class="tenant-btn tenant-btn-primary tenant-btn-lg">
                                        <i class="ti ti-credit-card me-2"></i> Pay Now
                                      </button>
                                   </div>` : ''}
                            </div>
                        `;
                        
                        // Update the modal content
                        $('#invoice-details-content').html(invoiceHtml);
                    },
                    error: function(xhr) {
                        // Show error message with modern styling
                        $('#invoice-details-content').html(`
                            <div class="p-5 text-center">
                                <div class="tenant-notification tenant-notification-danger mx-auto" style="max-width: 300px;">
                                    <i class="ti ti-alert-circle fs-1 mb-3 d-block"></i>
                                    <p class="mb-0 fw-medium">Failed to load invoice details. Please try again.</p>
                                </div>
                            </div>
                        `);
                        console.error('Error loading invoice details:', xhr);
                    }
                });
            }
        });
    </script>
@endpush
