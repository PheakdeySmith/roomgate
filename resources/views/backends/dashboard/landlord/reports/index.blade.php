@extends('backends.layouts.app')

@section('title', 'Reports Dashboard')

@push('style')
<style>
    .report-card {
        transition: all 0.3s;
        border-radius: 10px;
    }
    
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card {
        padding: 1.5rem;
        height: 100%;
        border-radius: 10px;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        margin-bottom: 1rem;
    }
    
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
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
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
                <h4 class="page-title">Reports Dashboard</h4>
            </div>
        </div>
    </div>
    
    <!-- Report Navigation Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <a href="{{ route('landlord.reports.room-occupancy') }}" class="text-decoration-none">
                <div class="card report-card h-100">
                    <div class="card-body d-flex flex-column align-items-center p-4">
                        <div class="d-flex justify-content-center align-items-center mb-3 bg-primary-subtle rounded-circle" style="width: 80px; height: 80px;">
                            <i class="ti ti-door text-primary" style="font-size: 2.5rem;"></i>
                        </div>
                        <h4 class="text-center mb-2">Room Occupancy</h4>
                        <p class="text-muted text-center mb-0">Monitor room occupancy rates, vacancy status, and distribution across properties.</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-4 mb-3">
            <a href="{{ route('landlord.reports.tenant-report') }}" class="text-decoration-none">
                <div class="card report-card h-100">
                    <div class="card-body d-flex flex-column align-items-center p-4">
                        <div class="d-flex justify-content-center align-items-center mb-3 bg-success-subtle rounded-circle" style="width: 80px; height: 80px;">
                            <i class="ti ti-users text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h4 class="text-center mb-2">Tenant Reports</h4>
                        <p class="text-muted text-center mb-0">Track tenant information, contract statuses, and distribution across properties.</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-4 mb-3">
            <a href="{{ route('landlord.reports.financial-report') }}" class="text-decoration-none">
                <div class="card report-card h-100">
                    <div class="card-body d-flex flex-column align-items-center p-4">
                        <div class="d-flex justify-content-center align-items-center mb-3 bg-info-subtle rounded-circle" style="width: 80px; height: 80px;">
                            <i class="ti ti-cash text-info" style="font-size: 2.5rem;"></i>
                        </div>
                        <h4 class="text-center mb-2">Financial Reports</h4>
                        <p class="text-muted text-center mb-0">Analyze revenue, payment collection rates, and financial performance.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Key Statistics Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4">Key Statistics Overview</h4>
                    
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card bg-primary-subtle">
                                <div class="stat-icon bg-primary text-white">
                                    <i class="ti ti-home"></i>
                                </div>
                                <div class="stat-value">{{ number_format($totalRooms) }}</div>
                                <div class="stat-label">Total Rooms</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card bg-success-subtle">
                                <div class="stat-icon bg-success text-white">
                                    <i class="ti ti-check"></i>
                                </div>
                                <div class="stat-value">{{ number_format($occupancyRate, 1) }}%</div>
                                <div class="stat-label">Occupancy Rate</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card bg-info-subtle">
                                <div class="stat-icon bg-info text-white">
                                    <i class="ti ti-users"></i>
                                </div>
                                <div class="stat-value">{{ number_format($totalTenants) }}</div>
                                <div class="stat-label">Active Tenants</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card bg-warning-subtle">
                                <div class="stat-icon bg-warning text-white">
                                    <i class="ti ti-coins"></i>
                                </div>
                                <div class="stat-value">{{ number_format($collectionRate, 1) }}%</div>
                                <div class="stat-label">Collection Rate</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Occupancy and Financial Charts -->
    <div class="row mb-4">
        <!-- Occupancy Status Chart -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="header-title mb-3">Room Occupancy Status</h4>
                    <div class="chart-container">
                        <canvas id="occupancyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Financial Chart -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="header-title mb-3">Monthly Financial Overview ({{ $currentYear }})</h4>
                    <div class="chart-container">
                        <canvas id="financialChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Financial Summary -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Financial Summary</h4>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Total Revenue</h5>
                                    <h3 class="mt-3 mb-2">${{ number_format($totalAmount, 2) }}</h3>
                                    <p class="mb-0 text-muted">For {{ $currentYear }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card bg-success-subtle">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Collected</h5>
                                    <h3 class="mt-3 mb-2">${{ number_format($paidAmount, 2) }}</h3>
                                    <p class="mb-0 text-muted">{{ $totalAmount > 0 ? number_format(($paidAmount / $totalAmount) * 100, 1) : 0 }}% of total</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card bg-danger-subtle">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Outstanding</h5>
                                    <h3 class="mt-3 mb-2">${{ number_format($unpaidAmount, 2) }}</h3>
                                    <p class="mb-0 text-muted">{{ $totalAmount > 0 ? number_format(($unpaidAmount / $totalAmount) * 100, 1) : 0 }}% of total</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Room Occupancy Chart
        const occupancyData = {
            labels: ['Occupied', 'Vacant'],
            datasets: [{
                data: [{{ $occupiedRooms }}, {{ $vacantRooms }}],
                backgroundColor: ['#0ACF97', '#f1556c'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        };
        
        const occupancyChart = new Chart(document.getElementById('occupancyChart'), {
            type: 'doughnut',
            data: occupancyData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = {{ $totalRooms }};
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Financial Chart
        const months = [];
        const revenue = [];
        const collected = [];
        
        @foreach($monthlyData as $data)
            months.push('{{ $data['month'] }}');
            revenue.push({{ $data['total'] }});
            collected.push({{ $data['paid'] }});
        @endforeach
        
        const financialChart = new Chart(document.getElementById('financialChart'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Total Revenue',
                        data: revenue,
                        backgroundColor: 'rgba(57, 175, 209, 0.7)',
                        borderColor: '#39afd1',
                        borderWidth: 1
                    },
                    {
                        label: 'Collected Amount',
                        data: collected,
                        backgroundColor: 'rgba(10, 207, 151, 0.7)',
                        borderColor: '#0ACF97',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                return `${context.dataset.label}: $${value.toLocaleString()}`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
