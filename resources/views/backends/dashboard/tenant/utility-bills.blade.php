@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
@endphp

@extends('backends.layouts.app')

@section('title', 'Utility Bills | RoomGate')

@push('style')
<style>
    /* Modern Mobile App Design */
    :root {
        --primary-color: #47CFD1;
        --secondary-color: #2DB6B8;
        --accent-color: #70E1E3;
        --dark-color: #333333;
        --light-color: #FFFFFF;
        --bg-light: #FFFFFF;
        --bg-softer: #F8FDFD;
        --text-color: #333333;
        --text-muted: #718096;
        --card-radius: 1.25rem;
        --button-radius: 1.75rem;
        --progress-height: 0.5rem;
        --icon-bg: rgba(71, 207, 209, 0.15);
        --shadow-color: rgba(71, 207, 209, 0.2);
        --class-card-bg: rgba(112, 225, 227, 0.2);
    }
    
    [data-bs-theme="dark"] {
        --bg-light: #1A1A1A;
        --text-color: #E2E8F0;
        --text-muted: #A0AEC0;
        --icon-bg: rgba(71, 207, 209, 0.2);
    }
    
    .dashboard-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 1.25rem;
    }
    
    .dashboard-section {
        margin-bottom: 1.5rem;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .section-title {
        font-weight: 700;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .dashboard-header {
        position: sticky;
        top: 0;
        background-color: transparent;
        z-index: 100;
        padding: 1rem 0 1.5rem 0;
        margin-bottom: 1rem;
    }
    
    .dashboard-header h1 {
        font-weight: 600;
        font-size: 1.4rem;
        color: var(--text-color);
        margin: 0;
    }
    
    .back-button {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        color: var(--text-color);
        padding: 0;
        margin-right: 0.75rem;
    }
    
    .dashboard-header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .header-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: var(--bg-softer);
        color: var(--text-color);
        border: none;
        padding: 0;
        transition: all 0.2s ease;
    }
    
    .header-icon:hover {
        background-color: var(--icon-bg);
        transform: translateY(-2px);
    }
    
    .dashboard-card {
        border-radius: var(--card-radius);
        box-shadow: 0 10px 20px -5px var(--shadow-color);
        height: 100%;
        transition: transform 0.3s, box-shadow 0.3s;
        overflow: hidden;
        border: none;
        background-color: var(--light-color);
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 25px -10px var(--shadow-color);
    }
    
    .welcome-section {
        margin-bottom: 1.5rem;
    }
    
    .utility-bill-card {
        background-color: white;
        border-radius: var(--card-radius);
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .utility-bill-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .utility-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }
    
    .electricity-icon {
        background-color: #47CFD1;
    }
    
    .water-icon {
        background-color: #3B82F6;
    }
    
    .gas-icon {
        background-color: #F59E0B;
    }
    
    .internet-icon {
        background-color: #8B5CF6;
    }
    
    .utility-details {
        flex: 1;
    }
    
    .utility-amount {
        font-size: 1.25rem;
        font-weight: 700;
    }
    
    .utility-status {
        display: inline-block;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.375rem;
    }
    
    .utility-status-paid {
        background-color: rgba(16, 185, 129, 0.12);
        color: #10B981;
    }
    
    .utility-status-pending {
        background-color: rgba(245, 158, 11, 0.12);
        color: #F59E0B;
    }
    
    .utility-status-partial {
        background-color: rgba(124, 58, 237, 0.12);
        color: #7C3AED;
    }
    
    .btn-action {
        border-radius: var(--button-radius);
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        transition: all 0.2s;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-primary:hover {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }
    
    .category-filter {
        display: flex;
        overflow-x: auto;
        padding: 0.5rem 0;
        gap: 0.75rem;
        -ms-overflow-style: none;
        scrollbar-width: none;
        margin-bottom: 1.5rem;
        -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
    }
    
    .category-filter::-webkit-scrollbar {
        display: none;
    }
    
    .category-filter .d-flex {
        display: flex;
        flex-wrap: nowrap;
        gap: 0.75rem;
        padding-right: 1rem; /* Add some padding at the end for better UX */
    }
    
    .category-button {
        width: 40px;
        height: 40px;
        min-width: 40px; /* Ensure the button doesn't shrink */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f5f5f5;
        flex-shrink: 0; /* Prevent shrinking */
    }
    
    .category-button.active {
        background-color: var(--primary-color);
        color: white;
    }
    
    .category-tag {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        background-color: #f5f5f5;
        font-size: 0.85rem;
        white-space: nowrap;
        flex-shrink: 0; /* Prevent shrinking */
        transition: all 0.2s ease;
    }
    
    .category-tag:hover {
        background-color: var(--icon-bg);
        transform: translateY(-2px);
    }
    
    .category-tag.active {
        background-color: var(--primary-color);
        color: white;
    }
    
    .pagination-container {
        display: flex;
        justify-content: center;
        margin: 2rem 0 1rem;
    }
    
    .pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        gap: 0.25rem;
    }
    
    .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 50%;
        color: var(--text-color);
        background-color: transparent;
        border: none;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .page-item.active .page-link {
        background-color: var(--primary-color);
        color: white;
    }
    
    .page-item .page-link:hover {
        background-color: var(--icon-bg);
    }
    
    /* Mobile Navigation */
    .mobile-nav {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: var(--light-color);
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        z-index: 1000;
        padding: 0.75rem;
    }
    
    .mobile-nav-wrapper {
        display: flex;
        justify-content: space-evenly;
        align-items: center;
        position: relative;
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
    }
    
    .mobile-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0.5rem;
        color: var(--text-muted);
        text-decoration: none;
        position: relative;
        z-index: 2;
        flex: 1;
        text-align: center;
    }
    
    .mobile-nav-item.active {
        color: var(--primary-color);
    }
    
    .mobile-nav-icon {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }
    
    .mobile-nav-label {
        font-size: 0.7rem;
        font-weight: 500;
    }
    
    /* Animation keyframes */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease forwards;
    }
    
    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .dashboard-header h1 {
            font-size: 1.5rem;
        }
        
        .mobile-nav {
            display: flex;
            justify-content: space-around;
        }
        
        .container-fluid {
            padding-bottom: 80px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid dashboard-container px-3">
    <!-- Dashboard Header -->
    <div class="dashboard-header animate-fade-in">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('tenant.dashboard') }}" class="back-button">
                    <i class="ti ti-chevron-left"></i>
                </a>
                <h1>Utility Bills</h1>
            </div>
            <div class="dashboard-header-actions">
                <button class="header-icon" data-bs-toggle="tooltip" title="Filter Bills">
                    <i class="ti ti-filter"></i>
                </button>
                <button class="header-icon" data-bs-toggle="tooltip" title="Usage Statistics">
                    <i class="ti ti-chart-line"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="mb-3">
            <!-- Category Filter -->
        <div class="category-filter">
            <div class="d-flex">
                <a href="{{ route('tenant.utility-bills') }}" class="category-tag text-decoration-none {{ !request('type') ? 'active' : '' }}">
                    <span>All</span>
                </a>
                <a href="{{ route('tenant.utility-bills', ['type' => 'Electricity']) }}" class="category-tag text-decoration-none {{ request('type') == 'Electricity' ? 'active' : '' }}">
                    <span>Electricity</span>
                </a>
                <a href="{{ route('tenant.utility-bills', ['type' => 'Water']) }}" class="category-tag text-decoration-none {{ request('type') == 'Water' ? 'active' : '' }}">
                    <span>Water</span>
                </a>
                <a href="{{ route('tenant.utility-bills', ['type' => 'Gas']) }}" class="category-tag text-decoration-none {{ request('type') == 'Gas' ? 'active' : '' }}">
                    <span>Gas</span>
                </a>
                <a href="{{ route('tenant.utility-bills', ['type' => 'Internet']) }}" class="category-tag text-decoration-none {{ request('type') == 'Internet' ? 'active' : '' }}">
                    <span>Internet</span>
                </a>
                <a href="{{ route('tenant.utility-usage') }}" class="category-tag text-decoration-none">
                    <span>Usage Stats</span>
                </a>
            </div>
        </div>
        
        <!-- Utility Bills Section -->
        <div class="dashboard-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="ti ti-bolt-filled me-2"></i>
                    All Utility Bills
                </h3>
            </div>
            
            <!-- Utility Bills List -->
            @forelse ($utilityBills as $bill)
                @php
                    $utilityClass = '';
                    $utilityIcon = 'gauge';
                    
                    switch(strtolower($bill->utilityType->name)) {
                        case 'electricity':
                            $utilityClass = 'electricity-icon';
                            $utilityIcon = 'bolt';
                            break;
                        case 'water':
                            $utilityClass = 'water-icon';
                            $utilityIcon = 'droplet';
                            break;
                        case 'gas':
                            $utilityClass = 'gas-icon';
                            $utilityIcon = 'flame';
                            break;
                        case 'internet':
                            $utilityClass = 'internet-icon';
                            $utilityIcon = 'wifi';
                            break;
                    }
                @endphp
                
                <div class="utility-bill-card animate-fade-in">
                    <div class="d-flex">
                        <div class="utility-icon {{ $utilityClass }} me-3">
                            <i class="ti ti-{{ $utilityIcon }}"></i>
                        </div>
                        <div class="utility-details">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h4 class="mb-0">{{ $bill->utilityType->name }}</h4>
                                @php
                                    $lineItem = $bill->lineItem;
                                    $status = $lineItem ? $lineItem->status : 'pending';
                                    $statusClass = match($status) {
                                        'paid' => 'utility-status-paid',
                                        'partial' => 'utility-status-partial',
                                        default => 'utility-status-pending'
                                    };
                                    $statusText = match($status) {
                                        'paid' => 'Paid',
                                        'partial' => 'Partial',
                                        'draft' => 'Draft',
                                        'sent' => 'Sent',
                                        'overdue' => 'Overdue',
                                        'void' => 'Void',
                                        default => 'Pending'
                                    };
                                @endphp
                                <div class="utility-status {{ $statusClass }}">
                                    {{ $statusText }}
                                </div>
                            </div>
                            <div class="text-muted small mb-2">
                                {{ Carbon::parse($bill->billing_period_start)->format('M d') }} - 
                                {{ Carbon::parse($bill->billing_period_end)->format('M d, Y') }}
                            </div>
                            <div class="d-flex justify-content-between align-items-end">
                                <div>
                                    <div class="text-muted small">Usage</div>
                                    <div class="fw-medium">{{ number_format($bill->consumption, 2) }} {{ $bill->utilityType->unit_of_measure }}</div>
                                </div>
                                <div>
                                    <div class="text-muted small">Property Rate</div>
                                    <div class="fw-medium">${{ number_format($bill->rate_applied, 4) }}/{{ $bill->utilityType->unit_of_measure }}</div>
                                </div>
                                <div>
                                    <div class="text-muted small">Amount</div>
                                    <div class="utility-amount">${{ number_format($bill->amount, 2) }}</div>
                                </div>
                            </div>
                            <div class="mt-2 text-muted small">
                                <i class="ti ti-info-circle me-1"></i> Rate applies to {{ $bill->contract->room->property->name }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-light rounded-4 animate-fade-in">
                    <i class="ti ti-bolt-off display-1 text-muted mb-3"></i>
                    <h4>No Utility Bills Found</h4>
                    <p class="text-muted">There are no utility bills recorded for your account at this time.</p>
                </div>
            @endforelse
            
            <!-- Simple Pagination -->
            @if($utilityBills->hasPages())
                <div class="pagination-container my-4">
                    {{ $utilityBills->links('vendor.pagination.custom-pagination') }}
                </div>
            @endif
        </div>
        
        <div class="text-center mt-4 mb-5">
            <a href="{{ route('tenant.utility-usage') }}" class="btn btn-primary btn-action">
                <i class="ti ti-chart-line me-2"></i> View Usage Details
            </a>
        </div>
    </div>
    
    <!-- Mobile Navigation for small screens -->
    <div class="d-md-none mobile-nav">
        <div class="mobile-nav-wrapper">
            <a href="{{ route('tenant.dashboard') }}" class="mobile-nav-item">
                <i class="ti ti-home mobile-nav-icon"></i>
                <span class="mobile-nav-label">Home</span>
            </a>
            <a href="{{ route('tenant.invoices') }}" class="mobile-nav-item">
                <i class="ti ti-receipt mobile-nav-icon"></i>
                <span class="mobile-nav-label">Invoices</span>
            </a>
            <a href="{{ route('tenant.utility-bills') }}" class="mobile-nav-item active">
                <i class="ti ti-bolt mobile-nav-icon"></i>
                <span class="mobile-nav-label">Utilities</span>
            </a>
            <a href="{{ route('tenant.profile') }}" class="mobile-nav-item">
                <i class="ti ti-user mobile-nav-icon"></i>
                <span class="mobile-nav-label">Profile</span>
            </a>
        
        </div>
    </div>
</div>
@endsection
