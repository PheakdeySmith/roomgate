@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
@endphp

@extends('backends.layouts.app')

@section('title', 'My Home | RoomGate')

@push('style')
<style>
    /* Modern Dashboard Design */
    :root {
        --primary: #4361ee;
        --secondary: #3a0ca3;
        --success: #06d6a0;
        --warning: #ffd166;
        --danger: #ef476f;
        --info: #118ab2;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-400: #ced4da;
        --gray-500: #adb5bd;
        --gray-600: #6c757d;
        --gray-700: #495057;
        --gray-800: #343a40;
        --gray-900: #212529;
        --card-border-radius: 10px;
        --card-shadow: 0 2px 15px rgba(0, 0, 0, 0.04);
        --card-shadow-hover: 0 10px 30px rgba(0, 0, 0, 0.1);
        --animation-speed: 0.25s;
    }

    [data-bs-theme="dark"] {
        --gray-100: #1a1d20;
        --gray-200: #212529;
        --gray-300: #343a40;
        --gray-400: #495057;
        --gray-500: #6c757d;
        --gray-600: #adb5bd;
        --card-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
        --card-shadow-hover: 0 10px 30px rgba(0, 0, 0, 0.25);
    }

    /* Main dashboard styles */
    .dashboard-container {
        padding: 1.5rem;
    }

    /* Card styles */
    .modern-card {
        border-radius: var(--card-border-radius);
        border: none;
        box-shadow: var(--card-shadow);
        transition: transform var(--animation-speed) ease, box-shadow var(--animation-speed) ease;
        background-color: var(--bs-body-bg);
        overflow: hidden;
        height: 100%;
    }

    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--card-shadow-hover);
    }

    .modern-card .card-header {
        background: transparent;
        border-bottom: 1px solid var(--gray-200);
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modern-card .card-body {
        padding: 1.5rem;
    }

    .modern-card .card-title {
        font-weight: 600;
        margin-bottom: 0;
        font-size: 1rem;
    }

    /* Balance card with gradient */
    .balance-card {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
    }

    .balance-card .text-muted {
        color: rgba(255, 255, 255, 0.75) !important;
    }

    /* Stats card styles */
    .stat-card .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .stat-card .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-card .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    /* Button styles */
    .modern-btn {
        border-radius: 6px;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        transition: all var(--animation-speed) ease;
        border: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .modern-btn-primary {
        background-color: var(--primary);
        color: white;
    }

    .modern-btn-primary:hover {
        background-color: var(--secondary);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .modern-btn-outline {
        background-color: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .modern-btn-outline:hover {
        background-color: var(--primary);
        color: white;
    }

    .modern-btn-white {
        background-color: white;
        color: var(--primary);
    }

    /* List styles */
    .modern-list-item {
        border: none;
        border-bottom: 1px solid var(--gray-200);
        padding: 1rem 1.5rem;
        transition: background-color var(--animation-speed) ease;
    }

    .modern-list-item:last-child {
        border-bottom: none;
    }

    .modern-list-item:hover {
        background-color: var(--gray-100);
    }

    /* Badge styles */
    .modern-badge {
        border-radius: 6px;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* Progress bar styles */
    .modern-progress {
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        background-color: var(--gray-200);
    }

    .modern-progress .progress-bar {
        border-radius: 4px;
    }

    /* Mobile navigation */
    .mobile-nav {
        background-color: var(--bs-body-bg);
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
    }

    .mobile-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0.75rem 0;
        color: var(--gray-600);
        text-decoration: none;
        transition: color var(--animation-speed) ease;
    }

    .mobile-nav-item.active {
        color: var(--primary);
    }

    .mobile-nav-item i {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }

    /* Card action links */
    .card-link {
        color: var(--primary);
        text-decoration: none;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        transition: color var(--animation-speed) ease;
    }

    .card-link:hover {
        color: var(--secondary);
    }

    .card-link i {
        transition: transform var(--animation-speed) ease;
        margin-left: 0.25rem;
    }

    .card-link:hover i {
        transform: translateX(3px);
    }

    /* Utility progress icons */
    .utility-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Notification styles */
    .notification-item {
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid dashboard-container">
    <!-- Dashboard Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 fw-bold">Hello, {{ Auth::user()->first_name ?? Auth::user()->name }}</h1>
                @if ($currentContract)
                <p class="text-muted">{{ $currentContract->room->property->name }} • Room {{ $currentContract->room->room_number }}</p>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="#" class="btn btn-light rounded-circle">
                    <i class="ti ti-bell"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Total Balance Due Summary -->
        <div class="col-12">
            <div class="modern-card balance-card mb-0">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="display-6 fw-bold mb-0">
                                @if($totalBalanceDue > 0)
                                    ${{ number_format($totalBalanceDue, 2) }}
                                @else
                                    All Paid Up!
                                @endif
                            </h3>
                            <div class="text-muted mt-2">
                                @if($stats['pending_invoices'] > 0)
                                    {{ $stats['pending_invoices'] }} pending {{ Str::plural('invoice', $stats['pending_invoices']) }}
                                @else
                                    You're all caught up on payments
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            @if($nextInvoice)
                            <button class="modern-btn modern-btn-white invoice-details-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#invoiceDetailsModal"
                                    data-invoice-id="{{ $nextInvoice->id }}">
                                Pay Now
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Row -->
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 stat-card">
                <div class="card-body text-center py-4">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #06d6a0, #1a9988); color: white;">
                        <i class="ti ti-wallet fs-4"></i>
                    </div>
                    <div class="stat-value">
                        @if(($totalPaidThisMonth ?? 0) > 0)
                            ${{ number_format($totalPaidThisMonth, 2) }}
                        @else
                            -
                        @endif
                    </div>
                    <div class="stat-label">Paid This Month</div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 stat-card">
                <div class="card-body text-center py-4">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #4361ee, #3a0ca3); color: white;">
                        <i class="ti ti-file-invoice fs-4"></i>
                    </div>
                    <div class="stat-value">{{ $stats['total_invoices'] }}</div>
                    <div class="stat-label">Invoices</div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 stat-card">
                <div class="card-body text-center py-4">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ffd166, #ef9a2d); color: white;">
                        <i class="ti ti-calendar fs-4"></i>
                    </div>
                    <div class="stat-value">{{ $stats['contract_days_left'] }}</div>
                    <div class="stat-label">Days Left</div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 stat-card">
                <div class="card-body text-center py-4">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #118ab2, #073b4c); color: white;">
                        <i class="ti ti-check fs-4"></i>
                    </div>
                    <div class="stat-value">{{ $stats['paid_invoices'] }}</div>
                    <div class="stat-label">Paid</div>
                </div>
            </div>
        </div>
        
        <!-- Payment History Chart -->
        <div class="col-12 col-lg-6">
            <div class="modern-card h-100">
                <div class="card-header">
                    <h5 class="card-title">Payment History</h5>
                    <a href="{{ route('tenant.invoices') }}" class="card-link">
                        View All <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div id="payment-history-mini-chart" style="height: 240px;"></div>
                </div>
            </div>
        </div>
        
        <!-- Recent Invoices -->
        <div class="col-12 col-lg-6">
            <div class="modern-card h-100">
                <div class="card-header">
                    <h5 class="card-title">Recent Invoices</h5>
                    <a href="{{ route('tenant.invoices') }}" class="card-link">
                        View All <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($recentInvoices as $invoice)
                        <li class="list-group-item modern-list-item d-flex align-items-center invoice-details-btn" 
                            style="cursor: pointer;"
                            data-bs-toggle="modal" 
                            data-bs-target="#invoiceDetailsModal" 
                            data-invoice-id="{{ $invoice->id }}">
                            <div class="me-3">
                                @php
                                    $bgColor = $invoice->status == 'paid' ? 'var(--success)' : ($invoice->status == 'overdue' ? 'var(--danger)' : 'var(--warning)');
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
                                <span class="modern-badge" 
                                    style="background-color: {{ $bgColor }}20; color: {{ $bgColor }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item modern-list-item py-4 text-center">No recent invoices found</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Utility Usage -->
        <div class="col-12 col-lg-6">
            <div class="modern-card h-100">
                <div class="card-header">
                    <h5 class="card-title">Utility Usage</h5>
                    <a href="{{ route('tenant.utility-usage') }}" class="card-link">
                        View All <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div id="utility-usage-chart" style="height: 220px;"></div>
                    
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
                    @endphp
                    
                    @foreach($utilityPercentages as $name => $data)
                    <div class="mb-3 mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-medium">{{ $name }}</span>
                            <span class="text-muted small">{{ $data['percentage'] }}%</span>
                        </div>
                        <div class="modern-progress">
                            <div class="progress-bar bg-{{ $data['color'] }}" style="width: {{ min(100, $data['percentage']) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Recent Utility Bills -->
        <div class="col-12 col-lg-6">
            <div class="modern-card h-100">
                <div class="card-header">
                    <h5 class="card-title">Recent Utility Bills</h5>
                    <a href="{{ route('tenant.utility-bills') }}" class="card-link">
                        View All <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($recentUtilityBills as $bill)
                        <li class="list-group-item modern-list-item">
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
                            @endphp
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="utility-icon" style="background-color: var(--{{ $color }})20; color: var(--{{ $color }})">
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
                                    <span class="modern-badge" 
                                        style="background-color: {{ $statusColor }}20; color: {{ $statusColor }}">
                                        {{ $bill->is_paid ? 'Paid' : 'Pending' }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item modern-list-item py-4 text-center">No utility bills found</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Important Notifications -->
        @if(!empty($notifications))
        <div class="col-12">
            <div class="modern-card">
                <div class="card-header">
                    <h5 class="card-title">Important Notifications</h5>
                </div>
                <div class="card-body">
                    @foreach($notifications as $notification)
                    @php
                        $notifColor = $notification['type'] == 'danger' ? 'var(--danger)' : 
                                    ($notification['type'] == 'warning' ? 'var(--warning)' : 'var(--primary)');
                    @endphp
                    <div class="notification-item mb-3" style="background-color: {{ $notifColor }}10; border-left: 4px solid {{ $notifColor }}">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-{{ $notification['icon'] }} fs-4 me-3" style="color: {{ $notifColor }}"></i>
                            <p class="mb-0">{{ $notification['message'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        <!-- Mobile Navigation for small screens -->
        <div class="d-md-none fixed-bottom mobile-nav py-1" style="z-index: 1030;">
            <div class="d-flex justify-content-around">
                <a href="#" class="mobile-nav-item active">
                    <i class="ti ti-home"></i>
                    <small>Home</small>
                </a>
                <a href="{{ route('tenant.invoices') }}" class="mobile-nav-item">
                    <i class="ti ti-receipt"></i>
                    <small>Invoices</small>
                </a>
                <a href="{{ route('tenant.utility-bills') }}" class="mobile-nav-item">
                    <i class="ti ti-bolt"></i>
                    <small>Utilities</small>
                </a>
                <a href="{{ route('tenant.utility-usage') }}" class="mobile-nav-item">
                    <i class="ti ti-chart-line"></i>
                    <small>Usage</small>
                </a>
                <a href="#" class="mobile-nav-item">
                    <i class="ti ti-user"></i>
                    <small>Profile</small>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Details Modal -->
<div class="modal fade" id="invoiceDetailsModal" tabindex="-1" aria-labelledby="invoiceDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-md">
        <div class="modal-content" style="border-radius: var(--card-border-radius); overflow: hidden;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="invoiceDetailsModalLabel">Invoice Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="invoice-details-content">
                    <!-- Invoice details will be loaded here -->
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="modern-btn modern-btn-outline" data-bs-dismiss="modal">Close</button>
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
            const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim();
            const secondaryColor = getComputedStyle(document.documentElement).getPropertyValue('--secondary').trim();
            const successColor = getComputedStyle(document.documentElement).getPropertyValue('--success').trim();
            const warningColor = getComputedStyle(document.documentElement).getPropertyValue('--warning').trim();
            const dangerColor = getComputedStyle(document.documentElement).getPropertyValue('--danger').trim();
            
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
                        height: 240,
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
                    colors: [primaryColor, successColor],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
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
                                fontWeight: 500
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
                        horizontalAlign: 'right'
                    }
                };
                
                try {
                    new ApexCharts(document.querySelector("#payment-history-mini-chart"), paymentHistoryOptions).render();
                } catch (error) {
                    console.error("Error rendering payment chart:", error);
                    document.querySelector("#payment-history-mini-chart").innerHTML = '<div class="text-center text-muted py-2">Error loading chart</div>';
                }
            } else {
                document.querySelector("#payment-history-mini-chart").innerHTML = '<div class="text-center text-muted py-2">No payment history available</div>';
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
                        height: 220,
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
                            top: 0,
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
                                fontWeight: 500
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
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '12px',
                        markers: {
                            radius: 3
                        }
                    },
                    colors: [primaryColor, successColor, warningColor, dangerColor],
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
                    document.querySelector("#utility-usage-chart").innerHTML = '<div class="text-center text-muted py-3">Error loading chart</div>';
                }
            } else {
                document.querySelector("#utility-usage-chart").innerHTML = '<div class="text-center text-muted py-3">No utility usage data available</div>';
            }

            function loadInvoiceDetails(invoiceId) {
                // Show loading spinner
                $('#invoice-details-content').html(`
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
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
                        let statusColor, statusBg;
                        if (response.invoice.status === 'paid') {
                            statusColor = 'var(--success)';
                            statusBg = 'var(--success)' + '20';
                        } else if (response.invoice.status === 'overdue') {
                            statusColor = 'var(--danger)';
                            statusBg = 'var(--danger)' + '20';
                        } else {
                            statusColor = 'var(--warning)';
                            statusBg = 'var(--warning)' + '20';
                        }
                        
                        // Build the invoice details HTML with modern styling
                        const invoiceHtml = `
                            <div class="p-4">
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="fw-bold mb-0">Invoice #${response.invoice.invoice_number}</h4>
                                        <span class="modern-badge" style="background-color: ${statusBg}; color: ${statusColor}">
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
                                
                                <div style="background-color: var(--gray-100); padding: 20px; border-radius: 10px;">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal</span>
                                        <span>$${subtotal.toFixed(2)}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Paid Amount</span>
                                        <span>$${parseFloat(response.invoice.paid_amount).toFixed(2)}</span>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold pt-3 border-top">
                                        <span>Balance</span>
                                        <span style="color: var(--primary); font-size: 1.2rem;">$${(parseFloat(response.invoice.total_amount) - parseFloat(response.invoice.paid_amount)).toFixed(2)}</span>
                                    </div>
                                </div>
                                
                                ${response.invoice.status !== 'paid' ? 
                                  `<div class="mt-4 d-grid">
                                      <button class="modern-btn modern-btn-primary py-3">Pay Now</button>
                                   </div>` : ''}
                            </div>
                        `;
                        
                        // Update the modal content
                        $('#invoice-details-content').html(invoiceHtml);
                    },
                    error: function(xhr) {
                        // Show error message with modern styling
                        $('#invoice-details-content').html(`
                            <div class="p-4 text-center" style="background-color: var(--danger)20; border-radius: 10px; color: var(--danger);">
                                <i class="ti ti-alert-circle fs-1 mb-3"></i>
                                <p class="mb-0">Failed to load invoice details. Please try again.</p>
                            </div>
                        `);
                        console.error('Error loading invoice details:', xhr);
                    }
                });
            }
        });
    </script>
@endpush
