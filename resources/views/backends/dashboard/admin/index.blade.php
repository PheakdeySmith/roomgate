@extends('backends.layouts.app')

@section('title', 'Admin Dashboard')

@push('style')
<style>
    .recent-subscriptions {
        margin-bottom: 2rem;
    }
    
    .subscription-table th {
        font-weight: 600;
    }
    
    .subscription-table td {
        vertical-align: middle;
    }
    
    .subscription-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.25rem;
    }
    
    .chart-container {
        height: 350px;
        margin-bottom: 2rem;
    }
    
    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.15);
    }
    
    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.15); 
    }
    
    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.15);
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
                <h4 class="page-title">Admin Dashboard</h4>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Total Landlords</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="ti ti-users text-primary fs-2"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="mb-0 text-primary fw-bold">{{ $stats['total_landlords'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Total Tenants</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="ti ti-user-check text-success fs-2"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="mb-0 text-success fw-bold">{{ $stats['total_tenants'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Active Subscriptions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="ti ti-credit-card text-info fs-2"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="mb-0 text-info fw-bold">{{ $stats['active_subscriptions'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Revenue this Month</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="ti ti-currency-dollar text-warning fs-2"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="mb-0 text-warning fw-bold">${{ number_format($stats['revenue_this_month'], 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Monthly Revenue</h5>
                </div>
                <div class="card-body">
                    <div id="monthly-revenue-chart" class="chart-container"></div>
                </div>
            </div>
        </div>

        <!-- Subscription Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Subscriptions by Plan</h5>
                </div>
                <div class="card-body">
                    <div id="subscription-distribution-chart" class="chart-container"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Subscriptions -->
    <div class="card recent-subscriptions">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Recent Subscriptions</h5>
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-centered table-hover table-nowrap mb-0 subscription-table">
                    <thead>
                        <tr>
                            <th>Landlord</th>
                            <th>Plan</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSubscriptions as $subscription)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm d-flex justify-content-left align-items-left ">
                                        <img src="{{ asset($subscription->user->image) ?? asset('assets/images/default_image.png') }}"
                                                        alt="User" class="rounded"
                                                        style="width: 100%; height: 100%; object-fit: cover;" />
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h5 class="mb-0 font-size-14">{{ $subscription->user->name }}</h5>
                                        <p class="mb-0 text-muted font-size-12">{{ $subscription->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $subscription->subscriptionPlan->name }}</td>
                            <td>{{ $subscription->start_date->format('M d, Y') }}</td>
                            <td>{{ $subscription->end_date->format('M d, Y') }}</td>
                            <td>
                                @if($subscription->status == 'active')
                                    <span class="subscription-badge bg-success-subtle text-success">Active</span>
                                @elseif($subscription->status == 'canceled')
                                    <span class="subscription-badge bg-danger-subtle text-danger">Canceled</span>
                                @else
                                    <span class="subscription-badge bg-warning-subtle text-warning">Expired</span>
                                @endif
                            </td>
                            <td>${{ number_format($subscription->amount_paid, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="btn btn-sm btn-info">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No subscriptions found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    $(document).ready(function() {
        // Monthly Revenue Chart
        var monthlyRevenueOptions = {
            series: [{
                name: 'Revenue',
                data: [
                    @foreach($monthlyRevenue as $data)
                        {{ $data['total'] }},
                    @endforeach
                ]
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '60%',
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: 2,
                curve: 'smooth'
            },
            xaxis: {
                categories: [
                    @foreach($monthlyRevenue as $data)
                        '{{ $data["month"] }}',
                    @endforeach
                ],
            },
            yaxis: {
                title: {
                    text: 'Revenue ($)'
                }
            },
            fill: {
                opacity: 1
            },
            colors: ['#0d6efd']
        };

        var monthlyRevenueChart = new ApexCharts(document.querySelector("#monthly-revenue-chart"), monthlyRevenueOptions);
        monthlyRevenueChart.render();

        // Subscription Distribution Chart
        var subscriptionDistributionOptions = {
            series: [
                @foreach($subscriptionsByPlan as $data)
                    {{ $data->total }},
                @endforeach
            ],
            chart: {
                type: 'donut',
                height: 350
            },
            labels: [
                @foreach($subscriptionsByPlan as $data)
                    '{{ $data->name }}',
                @endforeach
            ],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            colors: ['#0d6efd', '#198754', '#0dcaf0', '#ffc107', '#dc3545']
        };

        var subscriptionDistributionChart = new ApexCharts(document.querySelector("#subscription-distribution-chart"), subscriptionDistributionOptions);
        subscriptionDistributionChart.render();
    });
</script>
@endpush
