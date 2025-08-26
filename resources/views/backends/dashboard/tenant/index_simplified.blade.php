@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
@endphp

@extends('backends.layouts.app')

@section('title', 'My Home | RoomGate')

@push('style')
<style>
    /* Online Learning Mobile App Design - Exact Match */
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
    
    body {
        background-color: var(--bg-light);
        color: var(--text-color);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
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
    
    .section-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background-color: var(--icon-bg);
        color: var(--primary-color);
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
    
    .section-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-color);
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
    
    .dashboard-hero {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        position: relative;
        overflow: hidden;
        border-radius: var(--card-radius);
        box-shadow: 0 15px 25px -10px rgba(71, 207, 209, 0.3);
    }
    
    .dashboard-hero::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.7;
    }
    
    .dashboard-stat {
        text-align: center;
        padding: 1.75rem 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .dashboard-stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
        background-color: var(--icon-bg);
        color: var(--primary-color);
    }
    
    .dashboard-stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        color: var(--text-color);
    }
    
    .dashboard-stat-label {
        font-size: 0.875rem;
        color: var(--text-muted);
        font-weight: 500;
    }
    
    .dashboard-list-item {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        transition: background-color 0.2s ease;
    }
    
    .dashboard-list-item:hover {
        background-color: rgba(71, 207, 209, 0.05);
    }
    
    [data-bs-theme="dark"] .dashboard-list-item {
        border-bottom-color: rgba(255,255,255,0.05);
    }
    
    .dashboard-badge {
        display: inline-block;
        padding: 0.35em 0.85em;
        font-size: 0.75em;
        font-weight: 600;
        line-height: 1.4;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 1rem;
    }
    
    .dashboard-badge-success {
        background-color: rgba(16, 185, 129, 0.12);
        color: #10B981;
    }
    
    .dashboard-badge-warning {
        background-color: rgba(245, 158, 11, 0.12);
        color: #F59E0B;
    }
    
    .dashboard-badge-danger {
        background-color: rgba(239, 68, 68, 0.12);
        color: #EF4444;
    }
    
    .dashboard-btn {
        display: inline-block;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        padding: 0.75rem 1.5rem;
        font-size: 0.875rem;
        border-radius: var(--button-radius);
        transition: all 0.2s;
        border: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
    }
    
    .dashboard-btn-primary {
        background-color: var(--primary-color);
        color: white;
    }
    
    .dashboard-btn-primary:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.12);
    }
    
    .dashboard-btn-white {
        background-color: white;
        color: var(--primary-color);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }
    
    .dashboard-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: all 0.2s;
    }
    
    .dashboard-link:hover {
        color: var(--secondary-color);
    }
    
    .dashboard-link i {
        transition: transform 0.2s;
    }
    
    .dashboard-link:hover i {
        transform: translateX(3px);
    }
    
    .dashboard-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: var(--bs-body-bg);
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
        z-index: 1000;
        padding: 0.5rem 0;
    }
    
    .dashboard-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        padding: 0.5rem;
        color: #6B7280;
        text-decoration: none;
    }
    
    .dashboard-nav-item.active {
        color: #4F46E5;
    }
    
    /* Animation keyframes */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease forwards;
    }
    
    /* Progress bar styling */
    .progress {
        height: var(--progress-height);
        border-radius: 1rem;
        background-color: rgba(0,0,0,0.05);
        overflow: visible;
    }
    
    .progress-bar {
        border-radius: 1rem;
        background-color: var(--primary-color);
        position: relative;
        transition: width 0.5s ease;
    }
    
    .progress-bar::after {
        content: attr(data-progress);
        position: absolute;
        right: 0;
        top: -20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    /* Tags styling */
    .tag {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        background-color: var(--bg-light);
        border-radius: 1rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
    }
    
    .tag.active {
        background-color: var(--primary-color);
        color: white;
    }
    
    /* Calendar/Date Selector */
    .select-date-section {
        background-color: var(--bg-light);
        border-radius: var(--card-radius);
        padding: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }
    
    .date-selector {
        display: flex;
        justify-content: space-between;
        overflow-x: auto;
        -ms-overflow-style: none;
        scrollbar-width: none;
        gap: 0.5rem;
        padding: 0.5rem 0;
    }
    
    .date-selector::-webkit-scrollbar {
        display: none;
    }
    
    .date-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 40px;
        padding: 0.5rem 0;
        cursor: pointer;
    }
    
    .date-day {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .date-number {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-color);
    }
    
    .date-item.active .date-number {
        background-color: var(--primary-color);
        color: white;
    }
    
    /* Welcome Section */
    .welcome-section {
        margin-bottom: 1.5rem;
    }
    
    .welcome-avatar {
        width: 38px;
        height: 38px;
    }
    
    .avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .welcome-title {
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    .notification-btn {
        border: none;
        background: transparent;
        position: relative;
        padding: 0;
        color: var(--text-color);
    }
    
    /* Category Filter */
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
    
    /* Section Styling */
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0;
    }
    
    .view-all-link {
        color: var(--primary-color);
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
    }
    
    /* Learning Card */
    .learning-card {
        background-color: rgba(71, 207, 209, 0.15);
        border-radius: var(--card-radius);
        padding: 1.25rem;
        position: relative;
        margin-bottom: 1rem;
        display: flex;
    }
    
    .learning-card-body {
        display: flex;
        flex: 1;
    }
    
    .learning-card-icon {
        margin-right: 1rem;
    }
    
    .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 1.5rem;
    }
    
    .learning-card-title {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }
    
    .learning-card-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .learning-tag {
        font-size: 0.7rem;
        color: var(--text-muted);
    }
    
    .learning-tag:not(:last-child)::after {
        content: "•";
        margin-left: 0.5rem;
    }
    
    .learning-progress {
        margin-top: 0.75rem;
    }
    
    .progress-bg {
        height: 6px;
        background-color: rgba(255,255,255,0.5);
        border-radius: 3px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background-color: var(--primary-color);
        border-radius: 3px;
    }
    
    .learning-card-actions {
        align-self: flex-start;
    }
    
    .card-action-btn {
        background: transparent;
        border: none;
        color: var(--text-muted);
        padding: 0;
    }
    
    /* Hours Chart */
    .hours-chart-card {
        background-color: white;
        border-radius: var(--card-radius);
        padding: 1.25rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        min-height: 300px;
    }
    
    #payment-history-chart {
        height: 300px;
    }
    
    /* Course Categories */
    .course-categories {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .course-category {
        background-color: white;
        border-radius: var(--card-radius);
        padding: 0.75rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .category-icon {
        width: 40px;
        height: 40px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .ui-icon {
        background-color: var(--primary-color);
    }
    
    .dev-icon {
        background-color: #EF4444;
    }
    
    .pending-icon {
        background-color: #F59E0B;
    }
    
    .category-more-btn {
        background: transparent;
        border: none;
        color: var(--text-muted);
        padding: 0;
    }
    
    /* Room rental specific styles */
    .invoice-details-btn {
        cursor: pointer;
    }
    
    .invoice-details-btn:hover {
        background-color: rgba(71, 207, 209, 0.05);
    }
    
    /* Bottom Navigation for Mobile */
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
    
    
    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .dashboard-header h1 {
            font-size: 1.5rem;
        }
        
        .dashboard-stat-icon {
            width: 48px;
            height: 48px;
            font-size: 1.25rem;
        }
        
        .dashboard-stat-value {
            font-size: 1.25rem;
        }
        
        .mobile-nav {
            display: flex;
            justify-content: space-around;
        }
        
        .content-page {
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
                <h1>My Home</h1>
            </div>
            <div class="dashboard-header-actions">
                <button class="header-icon" data-bs-toggle="tooltip" title="Notifications">
                    <i class="ti ti-bell"></i>
                </button>
                <a href="{{ route('tenant.profile') }}" class="header-icon" data-bs-toggle="tooltip" title="Profile">
                    <i class="ti ti-user"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <!-- Welcome Section -->
        <div class="welcome-section mb-4">
           @if($currentContract)
        <div class="my-tasks-section mb-4">
            
            <div class="learning-card">
                <div class="learning-card-body">
                    <div class="learning-card-icon">
                        <span class="icon-wrapper">
                            <i class="ti ti-building"></i>
                        </span>
                    </div>
                    
                    <div class="learning-card-content">
                        <h4 class="learning-card-title">Welcome back, {{ Auth::user()->name }}</h4>
                        
                        <div class="learning-card-tags">
                            <span class="learning-tag">
                                <i class="ti ti-calendar me-1"></i> Contract active
                            </span>
                        </div>
                        
                        <div class="learning-progress">
                            @php
                                $totalDays = $currentContract->start_date->diffInDays($currentContract->end_date);
                                $daysLeft = (int)now()->diffInDays($currentContract->end_date, false); // Cast to integer to remove decimals
                                $progress = max(0, min(100, ($totalDays - $daysLeft) / $totalDays * 100));
                            @endphp
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Contract Progress</span>
                                <span class="small">{{ $daysLeft }} days left</span>
                            </div>
                            <div class="progress-bg">
                                <div class="progress-fill" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="category-filter mt-3">
                <div class="d-flex">
                    <a href="{{ route('tenant.dashboard') }}" class="category-button active">
                        <span class="category-icon"><i class="ti ti-home-2"></i></span>
                    </a>
                    <a href="{{ route('tenant.invoices') }}" class="category-button">
                        <span class="category-icon"><i class="ti ti-receipt"></i></span>
                    </a>
                    <a href="{{ route('tenant.utility-bills') }}" class="category-button">
                        <span class="category-icon"><i class="ti ti-bolt"></i></span>
                    </a>
                    <a href="{{ route('tenant.invoices') }}" class="category-tag text-decoration-none">
                        <span>Rent</span>
                    </a>
                    <a href="{{ route('tenant.utility-bills') }}" class="category-tag text-decoration-none">
                        <span>Utilities</span>
                    </a>
                    @if($currentContract)
                    <a href="" class="category-tag text-decoration-none">
                        <span>Room {{ $currentContract->room->room_number }}</span>
                    </a>
                    @endif
                    <a href="{{ route('tenant.profile') }}" class="category-tag text-decoration-none">
                        <span>Profile</span>
                    </a>
                    <a href="#" class="category-tag text-decoration-none">
                        <span>Settings</span>
                    </a>
                </div>
            </div>
            
            <!-- Utilities Section -->
            <div class="course-categories mt-3">
                @forelse($recentInvoices->take(2) as $invoice)
                <div class="course-category invoice-details-btn"
                     data-bs-toggle="modal" 
                     data-bs-target="#invoiceDetailsModal" 
                     data-invoice-id="{{ $invoice->id }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            @php
                                $statusClass = $invoice->status == 'paid' ? 'ui-icon' : ($invoice->status == 'overdue' ? 'dev-icon' : 'pending-icon');
                                $icon = $invoice->status == 'paid' ? 'check' : ($invoice->status == 'overdue' ? 'alert-triangle' : 'clock');
                            @endphp
                            <div class="category-icon {{ $statusClass }}">
                                <i class="ti ti-{{ $icon }}"></i>
                            </div>
                            <div class="ms-3">
                                <div class="fw-bold">Invoice #{{ $invoice->invoice_number }}</div>
                                <div class="text-muted small">{{ $invoice->issue_date->format('M d, Y') }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="fw-bold">${{ number_format($invoice->total_amount, 2) }}</div>
                            <div class="text-muted small text-end">{{ ucfirst($invoice->status) }}</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="course-category">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="text-muted">
                            <i class="ti ti-receipt fs-3 mb-2"></i>
                            <p class="mb-0">No recent invoices found</p>
                        </div>
                    </div>
                </div>
                @endforelse
                @forelse($recentUtilityBills->take(2) as $bill)
                <div class="course-category">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            @php
                                $utilityColors = [
                                    'electricity' => '#47CFD1',
                                    'water' => '#3B82F6',
                                    'gas' => '#F59E0B',
                                    'internet' => '#8B5CF6'
                                ];
                                $utilityIcons = [
                                    'electricity' => 'bolt',
                                    'water' => 'droplet',
                                    'gas' => 'flame',
                                    'internet' => 'wifi'
                                ];
                                
                                $utilityName = strtolower($bill->utilityType->name);
                                $bgColor = $utilityColors[$utilityName] ?? '#47CFD1';
                                $icon = $utilityIcons[$utilityName] ?? 'bolt';
                            @endphp
                            <div class="category-icon" style="background-color: {{ $bgColor }};">
                                <i class="ti ti-{{ $icon }}"></i>
                            </div>
                            <div class="ms-3">
                                <div class="fw-bold">{{ $bill->utilityType->name }}</div>
                                <div class="text-muted small">
                                    {{ Carbon::parse($bill->billing_period_start)->format('M d') }} - 
                                    {{ Carbon::parse($bill->billing_period_end)->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="fw-bold">${{ number_format($bill->amount, 2) }}</div>
                            <div class="text-muted small text-end">{{ $bill->is_paid ? 'Paid' : 'Pending' }}</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="course-category">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="text-muted">
                            <i class="ti ti-bolt fs-3 mb-2"></i>
                            <p class="mb-0">No utility bills found</p>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        @endif

        
            
            
        </div>
        
        <!-- Balance Summary -->
        <div class="ongoing-class-section mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="section-title">Balance Summary</h3>
                <div class="section-dots">
                    <i class="ti ti-dots"></i>
                </div>
            </div>
            
            <!-- Balance Card -->
            <div class="learning-card">
                <div class="learning-card-body">
                    <div class="learning-card-icon">
                        <span class="icon-wrapper">
                            <i class="ti ti-wallet"></i>
                        </span>
                    </div>
                    
                    <div class="learning-card-content">
                        <h4 class="learning-card-title">
                            @if($totalBalanceDue > 0)
                                ${{ number_format($totalBalanceDue, 2) }}
                            @else
                                All Paid Up!
                            @endif
                        </h4>
                        
                        <div class="learning-card-tags">
                            @if($stats['pending_invoices'] > 0)
                            <span class="learning-tag">{{ $stats['pending_invoices'] }} pending {{ Str::plural('invoice', $stats['pending_invoices']) }}</span>
                            @else
                            <span class="learning-tag">No pending payments</span>
                            @endif
                            <span class="learning-tag">Due {{ $nextInvoice ? $nextInvoice->due_date->format('M d') : 'N/A' }}</span>
                        </div>
                        
                        @if($nextInvoice)
                        <div class="learning-progress">
                            @php
                                $daysUntilDue = (int)now()->diffInDays($nextInvoice->due_date, false);
                                $progress = $daysUntilDue < 0 ? 100 : max(0, 100 - ($daysUntilDue / 30 * 100));
                            @endphp
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Due in</span>
                                <span class="small">{{ $daysUntilDue < 0 ? 'Overdue' : $daysUntilDue . ' days' }}</span>
                            </div>
                            <div class="progress-bg">
                                <div class="progress-fill" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="learning-card-actions">
                    @if($nextInvoice)
                    <button class="card-action-btn invoice-details-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#invoiceDetailsModal"
                            data-invoice-id="{{ $nextInvoice->id }}">
                        <i class="ti ti-credit-card"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Payment History Section -->
        <div class="my-tasks-section mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="section-title">Payment History</h3>
                <a href="{{ route('tenant.invoices') }}" class="view-all-link">See all <i class="ti ti-chevron-right"></i></a>
            </div>
            
            <!-- Payment Chart -->
            <div class="hours-chart-card">
                <div id="payment-history-chart"></div>
                
                <div class="hours-total mt-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fw-bold">Total Paid</div>
                            <div class="text-muted small">${{ number_format($paidChart instanceof \Illuminate\Support\Collection ? $paidChart->sum() : array_sum($paidChart ?? []), 2) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="me-3" style="display: flex; align-items: center;">
                                <span style="width: 10px; height: 10px; background-color: #4F46E5; border-radius: 50%; display: inline-block; margin-right: 5px;"></span>
                                <span class="text-muted small">Billed</span>
                            </span>
                            <span style="display: flex; align-items: center;">
                                <span style="width: 10px; height: 10px; background-color: #10B981; border-radius: 50%; display: inline-block; margin-right: 5px;"></span>
                                <span class="text-muted small">Paid</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Invoices -->
            
        </div>
        
        <!-- Property Info Section -->
        
    </div>
    
    <!-- Main Content -->
    
        
        <!-- Mobile Navigation for small screens -->
        <div class="d-md-none mobile-nav">
            <div class="mobile-nav-wrapper">
                <a href="{{ route('tenant.dashboard') }}" class="mobile-nav-item active">
                    <i class="ti ti-home mobile-nav-icon"></i>
                    <span class="mobile-nav-label">Home</span>
                </a>
                <a href="{{ route('tenant.invoices') }}" class="mobile-nav-item">
                    <i class="ti ti-receipt mobile-nav-icon"></i>
                    <span class="mobile-nav-label">Invoices</span>
                </a>
                <a href="{{ route('tenant.utility-bills') }}" class="mobile-nav-item">
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
</div>

<!-- Invoice Details Modal -->
<div class="modal fade" id="invoiceDetailsModal" tabindex="-1" aria-labelledby="invoiceDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content" style="border-radius: 0.75rem; overflow: hidden;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="invoiceDetailsModalLabel">Invoice Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="invoice-details-content">
                    <!-- Invoice details will be loaded here -->
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading invoice details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
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
            
            // Handle invoice details modal
            $('.invoice-details-btn').on('click', function() {
                const invoiceId = $(this).data('invoice-id');
                loadInvoiceDetails(invoiceId);
            });

            // --- Payment History Chart ---
            const billedChartData = @json($billedChart instanceof \Illuminate\Support\Collection ? $billedChart->toArray() : ($billedChart ?? []));
            const paidChartData = @json($paidChart instanceof \Illuminate\Support\Collection ? $paidChart->toArray() : ($paidChart ?? []));
            
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
                        type: 'bar',
                        stacked: false,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'inherit',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                            animateGradually: {
                                enabled: true,
                                delay: 150
                            },
                            dynamicAnimation: {
                                enabled: true,
                                speed: 350
                            }
                        }
                    },
                    colors: ['#4F46E5', '#10B981'],
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '60%',
                            dataLabels: {
                                position: 'top'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return '$' + val;
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '10px',
                            colors: ["#304758"]
                        }
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: Object.keys(billedChartData),
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: '10px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Amount ($)'
                        },
                        labels: {
                            formatter: function(value) {
                                return '$' + value.toFixed(0);
                            }
                        }
                    },
                    grid: {
                        borderColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)',
                        strokeDashArray: 4
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
                        horizontalAlign: 'right'
                    },
                    fill: {
                        opacity: 0.9,
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: "vertical",
                            shadeIntensity: 0.25,
                            gradientToColors: undefined,
                            inverseColors: true,
                            opacityFrom: 0.85,
                            opacityTo: 0.85,
                            stops: [50, 100]
                        }
                    }
                };
                
                try {
                    if (document.querySelector("#payment-history-chart")) {
                        new ApexCharts(document.querySelector("#payment-history-chart"), paymentHistoryOptions).render();
                    }
                } catch (error) {
                    if (document.querySelector("#payment-history-chart")) {
                        document.querySelector("#payment-history-chart").innerHTML = '<div class="text-center text-muted py-4"><i class="ti ti-chart-bar fs-3 mb-2"></i><p class="mb-0">Error loading chart</p></div>';
                    }
                }
            } else {
                if (document.querySelector("#payment-history-chart")) {
                    document.querySelector("#payment-history-chart").innerHTML = '<div class="text-center text-muted py-4"><i class="ti ti-chart-bar fs-3 mb-2"></i><p class="mb-0">No payment history available</p></div>';
                }
            }

            // --- Utility Usage Chart ---
            const utilityData = @json($utilityData ?? []);
            const utilityChartSeries = [];
            
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
                        fontFamily: 'inherit'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    grid: {
                        borderColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)',
                        strokeDashArray: 4
                    },
                    xaxis: {
                        categories: lastThreeMonths
                    },
                    colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444'],
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
                    if (document.querySelector("#utility-usage-chart")) {
                        new ApexCharts(document.querySelector("#utility-usage-chart"), utilityUsageOptions).render();
                    }
                } catch (error) {
                    if (document.querySelector("#utility-usage-chart")) {
                        document.querySelector("#utility-usage-chart").innerHTML = '<div class="text-center text-muted py-4"><i class="ti ti-bolt fs-3 mb-2"></i><p class="mb-0">Error loading chart</p></div>';
                    }
                }
            } else {
                if (document.querySelector("#utility-usage-chart")) {
                    document.querySelector("#utility-usage-chart").innerHTML = '<div class="text-center text-muted py-4"><i class="ti ti-bolt fs-3 mb-2"></i><p class="mb-0">No utility usage data available</p></div>';
                }
            }

            function loadInvoiceDetails(invoiceId) {
                // Show loading spinner
                $('#invoice-details-content').html(`
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
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
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span>${item.description}</span>
                                    <span class="fw-medium">$${parseFloat(item.amount).toFixed(2)}</span>
                                </li>
                            `;
                        });
                        
                        // Generate status badge style
                        let statusClass;
                        if (response.invoice.status === 'paid') {
                            statusClass = 'success';
                        } else if (response.invoice.status === 'overdue') {
                            statusClass = 'danger';
                        } else {
                            statusClass = 'warning';
                        }
                        
                        // Build the invoice details HTML
                        const invoiceHtml = `
                            <div>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="fw-bold mb-0">Invoice #${response.invoice.invoice_number}</h4>
                                        <span class="dashboard-badge dashboard-badge-${statusClass}">
                                            ${response.invoice.status.charAt(0).toUpperCase() + response.invoice.status.slice(1)}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted mb-2">Issue Date: ${issueDate}</div>
                                        <div class="text-muted mb-2">Due Date: ${dueDate}</div>
                                        <div class="text-muted mb-2">Property: ${response.property.name}</div>
                                        <div class="text-muted">Room: ${response.room.room_number}</div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h5 class="fw-bold mb-3">Line Items</h5>
                                    <ul class="list-unstyled">
                                        ${lineItemsHtml}
                                    </ul>
                                </div>
                                
                                <div class="bg-light p-3 rounded">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal</span>
                                        <span class="fw-medium">$${subtotal.toFixed(2)}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Paid Amount</span>
                                        <span class="fw-medium">$${parseFloat(response.invoice.paid_amount).toFixed(2)}</span>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold pt-2 border-top">
                                        <span>Balance</span>
                                        <span class="text-primary">$${(parseFloat(response.invoice.total_amount) - parseFloat(response.invoice.paid_amount)).toFixed(2)}</span>
                                    </div>
                                </div>
                                
                                ${response.invoice.status !== 'paid' ? 
                                  `<div class="mt-4 d-grid">
                                      <button class="dashboard-btn dashboard-btn-primary">
                                        <i class="ti ti-credit-card me-2"></i> Pay Now
                                      </button>
                                   </div>` : ''}
                            </div>
                        `;
                        
                        // Update the modal content
                        $('#invoice-details-content').html(invoiceHtml);
                    },
                    error: function(xhr) {
                        // Show error message
                        $('#invoice-details-content').html(`
                            <div class="p-4 text-center">
                                <div class="alert alert-danger mx-auto" style="max-width: 300px;">
                                    <i class="ti ti-alert-circle fs-1 mb-3 d-block"></i>
                                    <p class="mb-0">Failed to load invoice details. Please try again.</p>
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
