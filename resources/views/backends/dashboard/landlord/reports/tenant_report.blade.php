@extends('backends.layouts.app')

@section('title', 'Tenant Report')

@push('style')
    <style>
        .filter-form {
            background-color: var(--bs-light);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .stats-card {
            transition: all 0.3s;
            border-radius: 10px;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 10px;
        }

        .avatar-sm {
            width: 36px;
            height: 36px;
        }

        .contract-status {
            width: 10px;
            height: 10px;
            display: inline-block;
            border-radius: 50%;
            margin-right: 5px;
        }

        .status-active {
            background-color: #0ACF97;
        }

        .status-expiring {
            background-color: #f7b84b;
        }

        .status-expired {
            background-color: #f1556c;
        }

        /* Mobile responsive improvements */
        @media (max-width: 767.98px) {
            .chart-container {
                height: 250px;
            }

            .stats-card h2 {
                font-size: 1.5rem;
            }

            .stats-card h5 {
                font-size: 1rem;
            }

            .filter-form .btn {
                margin-top: 10px;
            }

            .header-title {
                font-size: 1.1rem;
            }

            .table-responsive {
                font-size: 0.85rem;
            }
        }

        /* Print styles */
        @media print {

            .filter-form,
            .page-title-right,
            .navbar-custom,
            .left-side-menu,
            .footer,
            .dataTables_filter,
            .dataTables_length,
            .dataTables_paginate,
            #tenants-table_info,
            .btn,
            .breadcrumb {
                display: none !important;
            }

            .chart-section {
                display: none !important;
            }

            .print-only-table {
                display: block !important;
            }

            .content-page {
                margin-left: 0 !important;
                padding: 0 !important;
            }

            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .card-body {
                padding: 0.5rem !important;
            }

            body {
                font-size: 12px !important;
            }

            h2,
            h3,
            h4 {
                font-size: 14px !important;
                margin-bottom: 0.5rem !important;
            }

            .table {
                font-size: 11px !important;
            }

            .page-title {
                margin-bottom: 1rem !important;
                font-size: 18px !important;
                text-align: center !important;
                display: block !important;
            }

            .print-header {
                text-align: center;
                margin-bottom: 20px;
                display: block !important;
            }

            .print-header h2 {
                font-size: 18px !important;
                margin-bottom: 5px !important;
            }

            .print-only-table h4 {
                font-size: 14px !important;
                margin: 15px 0 5px 0 !important;
                font-weight: bold !important;
            }

            .print-stats-table {
                margin-bottom: 20px !important;
            }
        }

        .print-only-table,
        .print-header {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('landlord.reports.index') }}">Reports</a></li>
                            <li class="breadcrumb-item active">Tenant Report</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Tenant Report</h4>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="filter-form">
                    <form action="{{ route('landlord.reports.tenant-report') }}" method="GET" id="filterForm">
                        <div class="row align-items-end">
                            <div class="col-lg-4 col-md-4 col-sm-6 mb-2">
                                <label for="property_id" class="form-label">Filter by Property</label>
                                <select name="property_id" id="property_id" class="form-select">
                                    <option value="">All Properties</option>
                                    @foreach ($properties as $property)
                                        <option value="{{ $property->id }}"
                                            {{ $selectedProperty && $selectedProperty->id == $property->id ? 'selected' : '' }}>
                                            {{ $property->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 mb-2">
                                <label for="contract_status" class="form-label">Contract Status</label>
                                <select name="contract_status" id="contract_status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="active" {{ request('contract_status') == 'active' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="expiring"
                                        {{ request('contract_status') == 'expiring' ? 'selected' : '' }}>
                                        Expiring Soon (30 days)</option>
                                    <option value="expired" {{ request('contract_status') == 'expired' ? 'selected' : '' }}>
                                        Expired</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-6 mb-2">
                                <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-6 mb-2">
                                <a href="{{ route('landlord.reports.tenant-report') }}"
                                    class="btn btn-secondary w-100">Reset</a>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <button type="button" class="btn btn-success" id="printReport">
                                    <i class="ti ti-printer me-1"></i> Print Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Print Header - Only visible when printing -->
        <div class="print-header">
            <h2>Tenant Report</h2>
            <p>{{ $selectedProperty ? 'Property: ' . $selectedProperty->name : 'All Properties' }}</p>
            <p>Report Date: {{ now()->format('F d, Y') }}</p>
        </div>

        <!-- Key Statistics -->
        <div class="row mb-4 chart-section">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary-subtle p-2 rounded">
                                    <i class="ti ti-users text-primary fs-3"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0">Total Tenants</h5>
                            </div>
                        </div>
                        <h2 class="mt-3 mb-2">{{ number_format($totalTenants) }}</h2>
                        <p class="mb-0 text-muted">
                            {{ $selectedProperty ? 'in ' . $selectedProperty->name : 'across all properties' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-success-subtle p-2 rounded">
                                    <i class="ti ti-check-circle text-success fs-3"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0">Active Contracts</h5>
                            </div>
                        </div>
                        <h2 class="mt-3 mb-2">{{ number_format($activeContracts) }}</h2>
                        <p class="mb-0 text-muted">
                            <span
                                class="text-success">{{ $totalContracts > 0 ? number_format(($activeContracts / $totalContracts) * 100, 1) : 0 }}%</span>
                            of total contracts
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-warning-subtle p-2 rounded">
                                    <i class="ti ti-alert-triangle text-warning fs-3"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0">Expiring Soon</h5>
                            </div>
                        </div>
                        <h2 class="mt-3 mb-2">{{ number_format($expiringContracts) }}</h2>
                        <p class="mb-0 text-muted">
                            contracts expiring in 30 days
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-danger-subtle p-2 rounded">
                                    <i class="ti ti-x text-danger fs-3"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0">Expired Contracts</h5>
                            </div>
                        </div>
                        <h2 class="mt-3 mb-2">{{ number_format($expiredContracts) }}</h2>
                        <p class="mb-0 text-muted">
                            <span
                                class="text-danger">{{ $totalContracts > 0 ? number_format(($expiredContracts / $totalContracts) * 100, 1) : 0 }}%</span>
                            of total contracts
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print-only Statistics Table -->
        <div class="print-only-table">
            <h4>Tenant Statistics Summary</h4>
            <table class="table table-bordered table-sm print-stats-table">
                <thead>
                    <tr>
                        <th>Total Tenants</th>
                        <th>Active Contracts</th>
                        <th>Expiring Soon</th>
                        <th>Expired Contracts</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ number_format($totalTenants) }}</td>
                        <td>
                            {{ number_format($activeContracts) }}
                            ({{ $totalContracts > 0 ? number_format(($activeContracts / $totalContracts) * 100, 1) : 0 }}%)
                        </td>
                        <td>
                            {{ number_format($expiringContracts) }}
                        </td>
                        <td>
                            {{ number_format($expiredContracts) }}
                            ({{ $totalContracts > 0 ? number_format(($expiredContracts / $totalContracts) * 100, 1) : 0 }}%)
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Charts -->
        <div class="row mb-4 chart-section">
            <!-- Contract Status Chart -->
            <div class="col-lg-6 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Contract Status Distribution</h4>
                        <div class="chart-container">
                            <canvas id="contractStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tenant Duration Chart -->
            <div class="col-lg-6 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Tenant Duration</h4>
                        <div class="chart-container">
                            <canvas id="tenantDurationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print-only Chart Tables -->
        <div class="print-only-table">
            <!-- Contract Status Table -->
            <h4>Contract Status Distribution</h4>
            <table class="table table-bordered table-sm print-stats-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Active</td>
                        <td>{{ number_format($activeContracts) }}</td>
                        <td>{{ $totalContracts > 0 ? number_format(($activeContracts / $totalContracts) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    <tr>
                        <td>Expiring Soon</td>
                        <td>{{ number_format($expiringContracts) }}</td>
                        <td>{{ $totalContracts > 0 ? number_format(($expiringContracts / $totalContracts) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    <tr>
                        <td>Expired</td>
                        <td>{{ number_format($expiredContracts) }}</td>
                        <td>{{ $totalContracts > 0 ? number_format(($expiredContracts / $totalContracts) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td><strong>{{ number_format($totalContracts) }}</strong></td>
                        <td><strong>100%</strong></td>
                    </tr>
                </tbody>
            </table>

            <!-- Tenant Duration Table -->
            <h4>Tenant Duration Distribution</h4>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Duration</th>
                        <th>Number of Tenants</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Less than 3 months</td>
                        <td>{{ number_format($durationStats['lessThan3Months']) }}</td>
                        <td>{{ $totalTenants > 0 ? number_format(($durationStats['lessThan3Months'] / $totalTenants) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    <tr>
                        <td>3-6 months</td>
                        <td>{{ number_format($durationStats['3to6Months']) }}</td>
                        <td>{{ $totalTenants > 0 ? number_format(($durationStats['3to6Months'] / $totalTenants) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    <tr>
                        <td>6-12 months</td>
                        <td>{{ number_format($durationStats['6to12Months']) }}</td>
                        <td>{{ $totalTenants > 0 ? number_format(($durationStats['6to12Months'] / $totalTenants) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    <tr>
                        <td>More than 12 months</td>
                        <td>{{ number_format($durationStats['moreThan12Months']) }}</td>
                        <td>{{ $totalTenants > 0 ? number_format(($durationStats['moreThan12Months'] / $totalTenants) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td><strong>{{ number_format($totalTenants) }}</strong></td>
                        <td><strong>100%</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tenant List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Tenant List</h4>

                        <div class="table-responsive">
                            <table class="table table-centered table-hover dt-responsive nowrap w-100 dataTable"
                                id="tenants-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tenant</th>
                                        <th>Phone</th>
                                        <th>Room</th>
                                        <th>Property</th>
                                        <th>Contract Status</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Duration</th>
                                        <th>Rent Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tenants as $tenant)
                                        <tr>
                                            <td class="table-user">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm flex-shrink-0">
                                                        {{-- IMPROVEMENT: Cleaner way to handle default image --}}
                                                        <img src="{{ $tenant->image ? asset($tenant->image) : asset('assets/images/default_image.png') }}"
                                                            alt="User" class="rounded"
                                                            style="width: 40px; height: 40px; object-fit: cover;" />
                                                    </div>
                                                    <div class="flex-grow-1 ms-2">
                                                        <h5 class="mb-0 font-size-14">{{ $tenant->name }}</h5>
                                                        <p class="mb-0 text-muted font-size-12">
                                                            {{ $tenant->email ?? '-' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{-- This column now correctly aligns with the "Phone" header --}}
                                                {{ $tenant->phone_number ?? '-' }}
                                            </td>

                                            {{-- FIX: Use the new 'currentContract' relationship --}}
                                            <td>{{ $tenant->currentContract?->room?->room_number ?? '-' }}</td>
                                            <td>{{ $tenant->currentContract?->room?->property?->name ?? '-' }}</td>

                                            <td>
                                                @if ($tenant->currentContract)
                                                    @php
                                                        $endDate = $tenant->currentContract->end_date;
                                                    @endphp
                                                    @if (now()->between($endDate->copy()->subDays(30), $endDate))
                                                        <span class="badge bg-warning">Expiring Soon</span>
                                                    @elseif($endDate > now())
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Expired</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">No Contract</span>
                                                @endif
                                            </td>

                                            <td>
                                                {{ $tenant->currentContract ? $tenant->currentContract->start_date->format('d M Y') : '-' }}
                                            </td>
                                            <td>
                                                {{ $tenant->currentContract ? $tenant->currentContract->end_date->format('d M Y') : '-' }}
                                            </td>

                                            <td>
                                                @if ($tenant->currentContract)
                                                    @php
                                                        $start = $tenant->currentContract->start_date;
                                                        $end = $tenant->currentContract->end_date;
                                                        $duration = $start->diffForHumans($end, [
                                                            'parts' => 2,
                                                            'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
                                                        ]);
                                                    @endphp
                                                    {{ $duration }}
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td>
                                                {{ $tenant->currentContract ? '$' . number_format($tenant->currentContract->rent_amount, 2) : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <!-- Make sure jQuery is loaded first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <!-- DataTables -->
    <script src="{{ asset('assets/js/vendor/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/dataTables.responsive.min.js') }}"></script>

    <script>
        // Define global chart variables
        let contractStatusChart, tenantDurationChart;

        // Initialize charts after DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded for tenant report');

            // Initialize charts after a small delay to ensure DOM is ready
            setTimeout(initializeCharts, 100);

            // Make sure jQuery is properly loaded before initializing DataTables
            if (typeof jQuery !== 'undefined') {
                console.log('jQuery is loaded, version:', jQuery.fn.jquery);

                // Initialize DataTable with responsive configuration
                jQuery(function($) {
                    try {
                        if ($.fn.DataTable) {
                            var tenantsTable = $('#tenants-table').DataTable({
                                responsive: true,
                                lengthChange: true,
                                lengthMenu: [10, 25, 50, 100],
                                pageLength: 25,
                                columnDefs: [{
                                        responsivePriority: 1,
                                        targets: 0
                                    }, // Tenant
                                    {
                                        responsivePriority: 2,
                                        targets: 2
                                    }, // Room
                                    {
                                        responsivePriority: 3,
                                        targets: 4
                                    }, // Contract Status
                                    {
                                        responsivePriority: 4,
                                        targets: 8
                                    }, // Rent Amount
                                    {
                                        responsivePriority: 10,
                                        targets: '_all'
                                    }
                                ]
                            });
                            console.log('DataTable initialized successfully');
                        } else {
                            console.error('DataTable plugin is not available');
                            // Fallback to standard table
                            console.log('Using standard table as fallback');
                        }
                    } catch (e) {
                        console.error('Error initializing DataTable:', e);
                    }
                });
            } else {
                console.error('jQuery is not loaded!');
            }
        });

        function initializeCharts() {
            try {
                // Initialize contract status chart
                const contractStatusContainer = document.getElementById('contractStatusChart');
                if (!contractStatusContainer) {
                    console.error('Contract status chart container not found');
                    return;
                }

                console.log('Initializing contract status chart');
                contractStatusChart = new Chart(contractStatusContainer, {
                    type: 'pie',
                    data: {
                        labels: ['Active', 'Expiring Soon', 'Expired'],
                        datasets: [{
                            data: [{{ $activeContracts }}, {{ $expiringContracts }},
                                {{ $expiredContracts }}
                            ],
                            backgroundColor: ['#0ACF97', '#f7b84b', '#f1556c'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: window.innerWidth < 768 ? 'bottom' : 'right',
                                labels: {
                                    boxWidth: window.innerWidth < 768 ? 10 : 15,
                                    font: {
                                        size: window.innerWidth < 768 ? 10 : 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw;
                                        const total = {{ $totalContracts }};
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Contract Status Chart initialized successfully');

                // Initialize tenant duration chart
                const tenantDurationContainer = document.getElementById('tenantDurationChart');
                if (!tenantDurationContainer) {
                    console.error('Tenant duration chart container not found');
                    return;
                }

                console.log('Initializing tenant duration chart');
                tenantDurationChart = new Chart(tenantDurationContainer, {
                    type: 'bar',
                    data: {
                        labels: [
                            'Less than 3 months',
                            '3-6 months',
                            '6-12 months',
                            'More than 12 months'
                        ],
                        datasets: [{
                            label: 'Number of Tenants',
                            data: [
                                {{ $durationStats['lessThan3Months'] }},
                                {{ $durationStats['3to6Months'] }},
                                {{ $durationStats['6to12Months'] }},
                                {{ $durationStats['moreThan12Months'] }}
                            ],
                            backgroundColor: [
                                '#6658dd',
                                '#4fc6e1',
                                '#6c757d',
                                '#1abc9c'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Tenants',
                                    font: {
                                        size: window.innerWidth < 768 ? 10 : 12
                                    }
                                },
                                ticks: {
                                    font: {
                                        size: window.innerWidth < 768 ? 9 : 11
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Contract Duration',
                                    font: {
                                        size: window.innerWidth < 768 ? 10 : 12
                                    }
                                },
                                ticks: {
                                    font: {
                                        size: window.innerWidth < 768 ? 8 : 10
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Tenant Duration Chart initialized successfully');
            } catch (error) {
                console.error('Error initializing charts:', error);
            }
        }

        // Handle window resize for better chart responsiveness
        window.addEventListener('resize', function() {
            if (contractStatusChart) {
                // Update legend position on resize
                contractStatusChart.options.plugins.legend.position = window.innerWidth < 768 ? 'bottom' : 'right';
                contractStatusChart.options.plugins.legend.labels.boxWidth = window.innerWidth < 768 ? 10 : 15;
                contractStatusChart.options.plugins.legend.labels.font.size = window.innerWidth < 768 ? 10 : 12;
                contractStatusChart.update();
            }

            if (tenantDurationChart) {
                // Update axis font sizes on resize
                tenantDurationChart.options.scales.y.title.font.size = window.innerWidth < 768 ? 10 : 12;
                tenantDurationChart.options.scales.y.ticks.font.size = window.innerWidth < 768 ? 9 : 11;
                tenantDurationChart.options.scales.x.title.font.size = window.innerWidth < 768 ? 10 : 12;
                tenantDurationChart.options.scales.x.ticks.font.size = window.innerWidth < 768 ? 8 : 10;
                tenantDurationChart.update();
            }
        });

        // Print report function
        document.getElementById('printReport').addEventListener('click', function() {
            document.title = "Tenant Report - " + new Date().toLocaleDateString();
            window.print();

            // Reset title after printing
            setTimeout(function() {
                document.title = "Tenant Report";
            }, 1000);
        });
    </script>
@endpush
