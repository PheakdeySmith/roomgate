@extends('backends.layouts.app')

@section('title', 'Room Occupancy Report')

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
        width: 100%;
        min-height: 250px;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }
    
    .bg-occupied {
        background-color: #d4edda;
        color: #155724;
    }
    
    .bg-vacant {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    /* Mobile-specific adjustments */
    @media (max-width: 767.98px) {
        .btn {
            margin-bottom: 10px;
            padding: 0.4rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .filter-form {
            padding: 15px 10px;
        }
        
        .card-body {
            padding: 15px 10px;
        }
        
        h2 {
            font-size: 1.5rem;
        }
        
        h4 {
            font-size: 1.2rem;
        }
        
        .chart-container {
            height: 250px;
            min-height: 200px;
        }
        
        .form-label {
            font-size: 0.875rem;
            margin-bottom: 0.3rem;
        }
        
        .form-select, .form-control {
            font-size: 0.875rem;
            padding: 0.375rem 0.5rem;
        }
        
        .stats-card .fs-3 {
            font-size: 1.25rem !important;
        }
        
        .stats-card h5 {
            font-size: 1rem;
        }
        
        .stats-card h2 {
            font-size: 1.25rem;
        }
        
        .stats-card p {
            font-size: 0.75rem;
        }
        
        /* Ensure table responsiveness */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Fix spacing between cards */
        .mb-4 {
            margin-bottom: 1rem !important;
        }
    }
    
    /* Small devices (landscape phones) */
    @media (max-width: 575.98px) {
        .chart-container {
            height: 200px;
            min-height: 180px;
        }
        
        .header-title {
            font-size: 1.1rem;
        }
        
        .card-body {
            padding: 12px 8px;
        }
        
        /* Make sure buttons don't get scrunched */
        .btn {
            width: 100%;
            margin-bottom: 8px;
        }
        
        /* Further reduce font sizes */
        .stats-card h2 {
            font-size: 1.1rem;
        }
    }
    
    /* Print-specific styles */
    @media print {
        body {
            font-size: 12px;
        }
        
        .no-print, .filter-form, .btn, .page-title-box, .navbar, .leftside-menu, .footer {
            display: none !important;
        }
        
        /* Hide chart sections when printing */
        .chart-section, .stats-card, .chart-container {
            display: none !important;
        }
        
        /* Show print-only tables */
        .print-only-table {
            display: block !important;
        }
        
        table.table-bordered th, table.table-bordered td {
            border: 1px solid #dee2e6 !important;
        }
        
        .table-bordered th {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 20px;
            display: block !important;
        }
        
        .print-footer {
            text-align: center;
            font-size: 10px;
            margin-top: 20px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            display: block !important;
        }
        
        /* Reset container width for print */
        .container-fluid {
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Print-only header -->
    <div class="print-header" style="display: none;">
        <h1>ROOM OCCUPANCY REPORT</h1>
        <div class="company-details">RoomGate Property Management System</div>
        <div class="report-period">
            @if($selectedProperty)
                Property: {{ $selectedProperty->name }}
            @else
                All Properties
            @endif
        </div>
        <div class="report-date">Generated on: {{ now()->format('F d, Y') }}</div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('landlord.reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Room Occupancy</li>
                    </ol>
                </div>
                <h4 class="page-title">Room Occupancy Report</h4>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="row">
        <div class="col-12">
            <div class="filter-form">
                <form action="{{ route('landlord.reports.room-occupancy') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="property_id" class="form-label">Filter by Property</label>
                            <select name="property_id" id="property_id" class="form-select">
                                <option value="">All Properties</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}" {{ $selectedProperty && $selectedProperty->id == $property->id ? 'selected' : '' }}>
                                        {{ $property->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-12 mb-3">
                            <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('landlord.reports.room-occupancy') }}" class="btn btn-secondary w-100">Reset</a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <button type="button" class="btn btn-success w-100" onclick="printReport()">
                                <i class="ti ti-printer me-1"></i> Print Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="row mb-4 chart-section">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary-subtle p-2 rounded">
                                <i class="ti ti-building text-primary fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">Total Rooms</h5>
                        </div>
                    </div>
                    <h2 class="mt-3 mb-2">{{ number_format($totalRooms) }}</h2>
                    <p class="mb-0 text-muted">{{ $selectedProperty ? $selectedProperty->name : 'All Properties' }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success-subtle p-2 rounded">
                                <i class="ti ti-home-check text-success fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">Occupied Rooms</h5>
                        </div>
                    </div>
                    <h2 class="mt-3 mb-2">{{ number_format($occupiedRooms) }}</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-success">{{ $totalRooms > 0 ? number_format(($occupiedRooms / $totalRooms) * 100, 1) : 0 }}%</span> of total rooms
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger-subtle p-2 rounded">
                                <i class="ti ti-home text-danger fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">Vacant Rooms</h5>
                        </div>
                    </div>
                    <h2 class="mt-3 mb-2">{{ number_format($vacantRooms) }}</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-danger">{{ $totalRooms > 0 ? number_format(($vacantRooms / $totalRooms) * 100, 1) : 0 }}%</span> of total rooms
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info-subtle p-2 rounded">
                                <i class="ti ti-chart-pie text-info fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">Occupancy Rate</h5>
                        </div>
                    </div>
                    <h2 class="mt-3 mb-2">{{ number_format($occupancyRate, 1) }}%</h2>
                    <p class="mb-0 text-muted">
                        @if($occupancyRate >= 90)
                            <span class="text-success">Very High</span>
                        @elseif($occupancyRate >= 75)
                            <span class="text-info">Good</span>
                        @elseif($occupancyRate >= 60)
                            <span class="text-warning">Moderate</span>
                        @else
                            <span class="text-danger">Low</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Print-only Summary Table -->
    <div class="print-only-table" style="display: none;">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">Room Occupancy Summary</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr class="table-light">
                                <th>Total Rooms</th>
                                <th>Occupied Rooms</th>
                                <th>Vacant Rooms</th>
                                <th>Occupancy Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ number_format($totalRooms) }}</td>
                                <td>{{ number_format($occupiedRooms) }} ({{ $totalRooms > 0 ? number_format(($occupiedRooms / $totalRooms) * 100, 1) : 0 }}%)</td>
                                <td>{{ number_format($vacantRooms) }} ({{ $totalRooms > 0 ? number_format(($vacantRooms / $totalRooms) * 100, 1) : 0 }}%)</td>
                                <td><strong>{{ number_format($occupancyRate, 1) }}%</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Room Type Occupancy Chart/Table -->
    <div class="row mb-4">
        <div class="col-12 col-lg-6 chart-section mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="header-title mb-3">Room Type Distribution</h4>
                    <div class="chart-container">
                        <canvas id="roomTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6 chart-section mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="header-title mb-3">Occupancy Status</h4>
                    <div class="chart-container">
                        <canvas id="occupancyStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 chart-section">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Occupancy by Room Type</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr class="table-light">
                                    <th>Room Type</th>
                                    <th>Total Rooms</th>
                                    <th>Occupied</th>
                                    <th>Vacant</th>
                                    <th>Occupancy Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roomsByType as $type)
                                <tr>
                                    <td>{{ $type['name'] }}</td>
                                    <td>{{ $type['total'] }}</td>
                                    <td>{{ $type['occupied'] }}</td>
                                    <td>{{ $type['vacant'] }}</td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 120px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                style="width: {{ $type['occupancy_rate'] }}%"></div>
                                        </div>
                                        <span class="ms-1">{{ number_format($type['occupancy_rate'], 1) }}%</span>
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
    
    <!-- Print-only Room Type Table -->
    <div class="print-only-table" style="display: none;">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">Occupancy by Room Type</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr class="table-light">
                                <th>Room Type</th>
                                <th>Total Rooms</th>
                                <th>Occupied</th>
                                <th>Vacant</th>
                                <th>Occupancy Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roomsByType as $type)
                            <tr>
                                <td>{{ $type['name'] }}</td>
                                <td>{{ $type['total'] }}</td>
                                <td>{{ $type['occupied'] }}</td>
                                <td>{{ $type['vacant'] }}</td>
                                <td>{{ number_format($type['occupancy_rate'], 1) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Room Details Table -->
    <div class="row">
        <div class="col-12 chart-section">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Room Details</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0" id="rooms-table">
                            <thead>
                                <tr class="table-light">
                                    <th>Room Number</th>
                                    <th>Property</th>
                                    <th>Room Type</th>
                                    <th>Status</th>
                                    <th>Tenant</th>
                                    <th>Contract Start</th>
                                    <th>Contract End</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rooms as $room)
                                <tr>
                                    <td>{{ $room->room_number }}</td>
                                    <td>{{ $room->property ? $room->property->name : 'N/A' }}</td>
                                    <td>{{ $room->roomType ? $room->roomType->name : 'N/A' }}</td>
                                    <td>
                                        @if($room->currentContract)
                                            <span class="badge bg-success">Occupied</span>
                                        @else
                                            <span class="badge bg-danger">Vacant</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($room->currentContract && isset($room->currentContract->tenant))
                                            {{ $room->currentContract->tenant->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($room->currentContract && $room->currentContract->start_date)
                                            {{ \Carbon\Carbon::parse($room->currentContract->start_date)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($room->currentContract && $room->currentContract->end_date)
                                            {{ \Carbon\Carbon::parse($room->currentContract->end_date)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
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
    
    <!-- Print-only Room Details Table -->
    <div class="print-only-table" style="display: none;">
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3">Room Details</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr class="table-light">
                                <th>Room Number</th>
                                <th>Property</th>
                                <th>Room Type</th>
                                <th>Status</th>
                                <th>Tenant</th>
                                <th>Contract Start</th>
                                <th>Contract End</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rooms as $room)
                            <tr>
                                <td>{{ $room->room_number }}</td>
                                <td>{{ $room->property ? $room->property->name : 'N/A' }}</td>
                                <td>{{ $room->roomType ? $room->roomType->name : 'N/A' }}</td>
                                <td>
                                    @if($room->currentContract)
                                        Occupied
                                    @else
                                        Vacant
                                    @endif
                                </td>
                                <td>
                                    @if($room->currentContract && isset($room->currentContract->tenant))
                                        {{ $room->currentContract->tenant->name }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($room->currentContract && $room->currentContract->start_date)
                                        {{ \Carbon\Carbon::parse($room->currentContract->start_date)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($room->currentContract && $room->currentContract->end_date)
                                        {{ \Carbon\Carbon::parse($room->currentContract->end_date)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Print-only footer -->
    <div class="print-footer" style="display: none;">
        <div>© {{ date('Y') }} RoomGate Property Management System | All rights reserved</div>
        <div>Report generated on {{ now()->format('F d, Y h:i A') }}</div>
    </div>
</div>
@endsection

@push('script')
<!-- Load Chart.js for room occupancy visualizations -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="{{ asset('assets') }}/js/vendor/jquery.dataTables.min.js"></script>
<script src="{{ asset('assets') }}/js/vendor/dataTables.bootstrap5.js"></script>
<script src="{{ asset('assets') }}/js/vendor/dataTables.responsive.min.js"></script>

<script>
    // Define global chart variables
    let roomTypeChart, occupancyStatusChart;
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Room occupancy report DOM loaded');
        
        // Initialize charts after a small delay to ensure DOM is ready
        setTimeout(initializeCharts, 100);
        
        // Initialize DataTables with mobile-friendly options
        try {
            $('#rooms-table').DataTable({
                responsive: true,
                lengthChange: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                pageLength: 10,
                dom: '<"row"<"col-sm-6"l><"col-sm-6"f>><"table-responsive"t><"row"<"col-sm-6"i><"col-sm-6"p>>',
                language: {
                    search: "",
                    searchPlaceholder: "Search rooms...",
                    paginate: {
                        previous: "<i class='ti ti-chevron-left'>",
                        next: "<i class='ti ti-chevron-right'>"
                    },
                    info: "_START_ to _END_ of _TOTAL_",
                    lengthMenu: "Show _MENU_"
                },
                columnDefs: [
                    // Set priority for responsive behavior
                    { responsivePriority: 1, targets: [0, 3] }, // Room number and status are highest priority
                    { responsivePriority: 2, targets: [1, 4] },  // Property and tenant are next priority
                    { responsivePriority: 3, targets: [2, 5, 6] } // Other columns are lowest priority
                ]
            });
            console.log('DataTable initialized successfully');
        } catch (error) {
            console.error('Error initializing DataTable:', error);
        }
    });
    
    function initializeCharts() {
        try {
            // Room Type Distribution Chart
            const roomTypeCtx = document.getElementById('roomTypeChart');
            if (!roomTypeCtx) {
                console.error('Room type chart container not found');
            } else {
                console.log('Initializing room type chart');
                
                // Extract room type data from the roomsByType array
                const roomTypeLabels = [];
                const roomTypeCounts = [];
                const roomTypeColors = [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
                    '#5a5c69', '#6f42c1', '#fd7e14', '#20c997', '#6c757d'
                ];
                
                @foreach($roomsByType as $index => $type)
                    roomTypeLabels.push("{{ $type['name'] }}");
                    roomTypeCounts.push({{ $type['total'] }});
                @endforeach
                
                roomTypeChart = new Chart(roomTypeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: roomTypeLabels,
                        datasets: [{
                            data: roomTypeCounts,
                            backgroundColor: roomTypeColors,
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
                                        const total = roomTypeCounts.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Room type chart initialized successfully');
            }
            
            // Occupancy Status Chart
            const occupancyStatusCtx = document.getElementById('occupancyStatusChart');
            if (!occupancyStatusCtx) {
                console.error('Occupancy status chart container not found');
            } else {
                console.log('Initializing occupancy status chart');
                
                occupancyStatusChart = new Chart(occupancyStatusCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Occupied', 'Vacant'],
                        datasets: [{
                            data: [{{ $occupiedRooms }}, {{ $vacantRooms }}],
                            backgroundColor: ['#1cc88a', '#e74a3b'],
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
                                        const total = {{ $totalRooms }};
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Occupancy status chart initialized successfully');
            }
        } catch (error) {
            console.error('Error initializing charts:', error);
        }
    }
    
    // Handle window resize for responsive charts
    window.addEventListener('resize', function() {
        if (roomTypeChart) {
            roomTypeChart.options.plugins.legend.position = window.innerWidth < 768 ? 'bottom' : 'right';
            roomTypeChart.options.plugins.legend.labels.boxWidth = window.innerWidth < 768 ? 10 : 15;
            roomTypeChart.options.plugins.legend.labels.font.size = window.innerWidth < 768 ? 10 : 12;
            roomTypeChart.update();
        }
        
        if (occupancyStatusChart) {
            occupancyStatusChart.options.plugins.legend.position = window.innerWidth < 768 ? 'bottom' : 'right';
            occupancyStatusChart.options.plugins.legend.labels.boxWidth = window.innerWidth < 768 ? 10 : 15;
            occupancyStatusChart.options.plugins.legend.labels.font.size = window.innerWidth < 768 ? 10 : 12;
            occupancyStatusChart.update();
        }
    });
    
    // Enhanced print function
    function printReport() {
        document.title = "Room Occupancy Report - " + new Date().toLocaleDateString();
        
        // Make sure print-only elements are shown
        const printOnlyElements = document.querySelectorAll('.print-only-table');
        printOnlyElements.forEach(el => el.style.display = 'block');
        
        window.print();
        
        // Reset after printing
        setTimeout(function() {
            document.title = "Room Occupancy Report";
            printOnlyElements.forEach(el => el.style.display = 'none');
        }, 500);
    }
</script>
@endpush
