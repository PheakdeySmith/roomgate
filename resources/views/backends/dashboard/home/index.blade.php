@extends('backends.layouts.app')

@section('title', 'Dashboard | RoomGate')

@push('style')
    {{-- Add any specific styles here if needed --}}
@endpush

@section('content')
    <div class="page-container">
        {{-- Page Header --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 text-uppercase fw-bold m-0">Dashboard</h4>
                    </div>
                    {{-- Optional: Add a date range filter here later if needed --}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 text-center">

                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-muted fs-13 text-uppercase">Revenue (This Month)</h5>
                                <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                    <div class="user-img fs-42 flex-shrink-0">
                                        <span class="avatar-title text-bg-primary rounded-circle fs-22">
                                            <iconify-icon icon="solar:bill-list-bold-duotone"></iconify-icon>
                                        </span>
                                    </div>
                                    <h3 class="mb-0 fw-bold">{!! format_money($stats['revenue']['current']) !!}</h3>
                                </div>
                                <p class="mb-0 text-muted">
                                    <span
                                        class="{{ $stats['revenue']['change'] >= 0 ? 'text-success' : 'text-danger' }} me-2"><i
                                            class="ti ti-caret-{{ $stats['revenue']['change'] >= 0 ? 'up' : 'down' }}-filled"></i>
                                        {{ number_format(abs($stats['revenue']['change']), 1) }}%</span>
                                    <span class="text-nowrap">Since last month</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-muted fs-13 text-uppercase">Overdue Invoices</h5>
                                <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                    <div class="user-img fs-42 flex-shrink-0">
                                        <span class="avatar-title text-bg-primary rounded-circle fs-22">
                                            <iconify-icon icon="solar:case-round-minimalistic-bold-duotone"></iconify-icon>
                                        </span>
                                    </div>
                                    <h3 class="mb-0 fw-bold">{{ $stats['overdue_count'] }}</h3>
                                </div>
                                <p class="mb-0 text-muted"><span class="text-nowrap">Require attention</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-muted fs-13 text-uppercase">Active Tenants</h5>
                                <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                    <div class="user-img fs-42 flex-shrink-0">
                                        <span class="avatar-title text-bg-primary rounded-circle fs-22">
                                            <iconify-icon icon="solar:wallet-money-bold-duotone"></iconify-icon>
                                        </span>
                                    </div>
                                    <h3 class="mb-0 fw-bold">{{ $stats['active_tenants'] }}</h3>
                                </div>
                                <p class="mb-0 text-muted"><span class="text-nowrap">Currently renting</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-muted fs-13 text-uppercase">Total Properties</h5>
                                <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                    <div class="user-img fs-42 flex-shrink-0">
                                        <span class="avatar-title text-bg-primary rounded-circle fs-22">
                                            <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon>
                                        </span>
                                    </div>
                                    <h3 class="mb-0 fw-bold">{{ $stats['total_properties'] }}</h3>
                                </div>
                                <p class="mb-0 text-muted"><span class="text-nowrap">In your portfolio</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xxl-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title">Revenue Overview (Last 6 Months)</h4>
                    </div>
                    <div class="card-body">
                        <div id="overview-chart" class="apex-charts" style="height: 315px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title">Room Status</h4>
                    </div>
                    <div class="card-body">
                        <div id="room-status-chart" class="apex-charts" style="min-height: 351px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title">Invoices Needing Attention</h4>
                        <a href="{{ route('landlord.payments.index') }}" class="btn btn-sm btn-secondary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive d-none d-lg-block">
                            <table class="table table-hover text-nowrap mb-0">
                                <tbody>
                                    @forelse ($recentInvoices as $invoice)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $invoice->contract->tenant->image ? asset($invoice->contract->tenant->image) : asset('assets/images/default_image.png') }}"
                                                        alt="Avatar" class="rounded me-2"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <h5 class="fs-14 my-1"><a
                                                                href="{{ route('landlord.contracts.show', $invoice->contract) }}"
                                                                class="link-reset">{{ $invoice->contract->tenant->name }}</a>
                                                        </h5>
                                                        <span class="text-muted fs-12">Room
                                                            {{ $invoice->contract->room->room_number }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <h5 class="fs-14 my-1">{{ $invoice->invoice_number }}</h5>
                                                <span class="text-muted fs-12">Due:
                                                    {{ $invoice->due_date->format('M d, Y') }}</span>
                                            </td>
                                            <td>
                                                <h5 class="fs-14 my-1">{!! format_money($invoice->balance) !!}</h5>
                                                <span class="text-muted fs-12">Amount Due</span>
                                            </td>
                                            <td class="text-end pe-3">
                                                @if ($invoice->status == 'overdue')
                                                    <span class="badge badge-soft-danger">Overdue</span>
                                                @else
                                                    <span class="badge badge-soft-warning">Sent</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted p-5">No pending or overdue
                                                invoices
                                                found.</td>
                                        </tr>
                                    @endforelse
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
    <script src="{{ asset('assets/js/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- Overview Area Chart ---
            const revenueChartData = @json($revenueChart);
            const paidChartData = @json($paidChart);
            const overviewChartOptions = {
                series: [{
                        name: 'Revenue',
                        data: Object.values(revenueChartData)
                    },
                    {
                        name: 'Paid',
                        data: Object.values(paidChartData)
                    }
                ],
                chart: {
                    height: 315,
                    type: 'area',
                    toolbar: {
                        show: false
                    },
                    sparkline: {
                        enabled: false
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
                    categories: Object.keys(revenueChartData)
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                }
            };
            new ApexCharts(document.querySelector("#overview-chart"), overviewChartOptions).render();

            // --- Room Status Radial Bar Chart ---
            const roomStatusData = @json($roomStatusData);
            const roomStatusOptions = {
                series: [roomStatusData.available || 0, roomStatusData.occupied || 0, roomStatusData
                    .maintenance || 0
                ],
                chart: {
                    height: 387,
                    type: 'radialBar'
                },
                plotOptions: {
                    radialBar: {
                        offsetY: 0,
                        startAngle: 0,
                        endAngle: 270,
                        hollow: {
                            margin: 5,
                            size: '30%',
                            background: 'transparent'
                        },
                        dataLabels: {
                            name: {
                                show: false
                            },
                            value: {
                                show: false
                            }
                        }
                    }
                },
                labels: ['Available', 'Occupied', 'Maintenance'],
                legend: {
                    show: true,
                    floating: true,
                    fontSize: '14px',
                    position: 'left',
                    offsetX: 10,
                    offsetY: 10,
                    labels: {
                        useSeriesColors: true
                    },
                    markers: {
                        size: 0
                    },
                    formatter: (seriesName, opts) =>
                        `${seriesName}:  ${opts.w.globals.series[opts.seriesIndex]}`,
                    itemMargin: {
                        vertical: 3
                    }
                }
            };
            new ApexCharts(document.querySelector("#room-status-chart"), roomStatusOptions).render();
        });
    </script>
@endpush
