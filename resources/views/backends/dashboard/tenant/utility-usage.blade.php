@php
    use Carbon\Carbon;
@endphp

@extends('backends.layouts.app')

@section('title', 'Utility Usage | RoomGate')

@push('style')
<style>
    /* Modern Mobile App Design - Core Variables */
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
    
    .dashboard-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 1.25rem;
    }
    
    /* Dashboard Header Styles */
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
    
    /* Animation keyframes */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease forwards;
    }
    
    .page-header {
        background: transparent;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .section-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
    }
    
    .section-card .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 16px 20px;
    }
    
    .section-card .card-header h5 {
        margin: 0;
        font-weight: 600;
    }
    
    .chart-container {
        height: 350px;
        width: 100%;
    }
    
    .meter-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
    }
    
    .meter-card .meter-header {
        padding: 16px 20px;
        background-color: rgba(59, 130, 246, 0.1);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .meter-card .meter-header h5 {
        margin: 0;
        font-weight: 600;
        color: #3b82f6;
    }
    
    .btn-action {
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
    }
    
    .utility-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-right: 12px;
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
    
    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .mobile-nav {
            display: flex;
            justify-content: space-around;
        }
        
        .container-fluid {
            padding-bottom: 80px;
        }
        
        .table-responsive {
            margin: 0;
            padding: 0;
        }
        
        /* Optimize table display for mobile */
        .mobile-table {
            width: 100%;
        }
        
        .mobile-table th,
        .mobile-table td {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }
        
        /* Prioritize important columns on small screens */
        .mobile-table th:nth-child(3),
        .mobile-table td:nth-child(3) {
            display: none;
        }
        
        /* Hide "Recorded By" on smaller screens */
        .mobile-table th:nth-child(4),
        .mobile-table td:nth-child(4) {
            display: none;
        }
        
        /* "Load More" button for infinite scroll */
        .load-more-button {
            padding: 0.75rem;
            width: 100%;
            border-radius: 8px;
            background-color: var(--bg-softer);
            color: var(--text-color);
            border: none;
            margin: 1rem 0;
            transition: all 0.2s;
        }
        
        .load-more-button:hover {
            background-color: var(--icon-bg);
        }
        
        .load-more-button.loading {
            opacity: 0.7;
        }
        
        /* Enhanced empty state */
        .empty-state {
            padding: 2rem 0;
            text-align: center;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--text-muted);
        }
    }
    
    /* Infinite scroll loader */
    .infinite-scroll-loader {
        display: flex;
        justify-content: center;
        padding: 1rem;
    }
    
    /* Search bar for readings */
    .readings-search {
        position: relative;
        margin-bottom: 1rem;
    }
    
    .readings-search input {
        padding-left: 2.5rem;
        border-radius: 20px;
    }
    
    .readings-search i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }
</style>
@endpush

@section('content')
<div class="container-fluid dashboard-container px-3">
    <!-- Dashboard Header -->
    <div class="dashboard-header animate-fade-in">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('tenant.utility-bills') }}" class="back-button">
                    <i class="ti ti-chevron-left"></i>
                </a>
                <h1>Utility Usage</h1>
            </div>
            <div class="dashboard-header-actions">
                <button class="header-icon" data-bs-toggle="tooltip" title="Filter Usage">
                    <i class="ti ti-filter"></i>
                </button>
                <button class="header-icon" data-bs-toggle="tooltip" title="Download Report">
                    <i class="ti ti-download"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Category Filter -->
    <div class="category-filter">
        <div class="d-flex">
            <a href="#" class="category-tag text-decoration-none">
                <span>All Utilities</span>
            </a>
            <a href="#" class="category-tag text-decoration-none">
                <span>Electricity</span>
            </a>
            <a href="#" class="category-tag text-decoration-none">
                <span>Water</span>
            </a>
            <a href="#" class="category-tag text-decoration-none">
                <span>Gas</span>
            </a>
            <a href="#" class="category-tag text-decoration-none">
                <span>Last 3 Months</span>
            </a>
            <a href="#" class="category-tag text-decoration-none">
                <span>Last Year</span>
            </a>
        </div>
    </div>
    
    <!-- Usage Chart -->
    <div class="section-card card mb-4">
        <div class="card-header">
            <h5>Monthly Usage (Last 12 Months)</h5>
        </div>
        <div class="card-body">
            <div id="utility-usage-chart" class="chart-container"></div>
        </div>
    </div>
    
    <!-- Meter Readings -->
    @if(!empty($meterReadingHistory))
        @foreach($meterReadingHistory as $meterId => $meterData)
            <div class="meter-card" id="meter-{{ $meterId }}">
                <div class="meter-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5>{{ $meterData['meter']->utilityType->name }} Meter Readings</h5>
                        <p class="text-muted mb-0">Meter #{{ $meterData['meter']->meter_number }}</p>
                    </div>
                    <div class="utility-icon bg-primary-subtle text-primary">
                        @if(strtolower($meterData['meter']->utilityType->name) == 'electricity')
                            <i class="ti ti-bolt"></i>
                        @elseif(strtolower($meterData['meter']->utilityType->name) == 'water')
                            <i class="ti ti-droplet"></i>
                        @elseif(strtolower($meterData['meter']->utilityType->name) == 'gas')
                            <i class="ti ti-flame"></i>
                        @else
                            <i class="ti ti-gauge"></i>
                        @endif
                    </div>
                </div>
                
                <!-- Search and Filter Bar -->
                <div class="readings-search mx-3 mt-3">
                    <input 
                        type="text" 
                        class="form-control form-control-sm reading-search-input" 
                        placeholder="Search by date..." 
                        data-meter-id="{{ $meterId }}"
                    >
                    <i class="ti ti-search"></i>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover mb-0 mobile-table" id="readings-table-{{ $meterId }}">
                        <thead>
                            <tr>
                                <th data-sort="date" class="sortable">Date <i class="ti ti-arrows-sort ms-1"></i></th>
                                <th data-sort="value" class="sortable">Reading Value <i class="ti ti-arrows-sort ms-1"></i></th>
                                <th data-sort="usage" class="sortable">Usage <i class="ti ti-arrows-sort ms-1"></i></th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody class="readings-body">
                            @forelse($meterData['readings'] as $reading)
                                @php
                                    // Sort readings chronologically (oldest first)
                                    $chronologicalReadings = collect($meterData['allReadings']->all())->sortBy('reading_date')->values();
                                    
                                    // Find the position of this reading in chronological order
                                    $chronoIndex = $chronologicalReadings->search(fn($item) => $item->id === $reading->id);
                                    
                                    // Is this the first reading ever?
                                    $isFirstEverReading = ($chronoIndex == 0);
                                    
                                    if ($isFirstEverReading) {
                                        // First ever reading: subtract initial_reading
                                        $consumption = $reading->reading_value - $meterData['meter']->initial_reading;
                                    } else {
                                        // Get the previous reading in chronological order
                                        $previousReading = $chronologicalReadings->get($chronoIndex - 1);
                                        $consumption = $reading->reading_value - $previousReading->reading_value;
                                    }
                                @endphp
                                <tr class="reading-row">
                                    <td data-label="Date">{{ $reading->reading_date->format('M d, Y') }}</td>
                                    <td data-label="Reading">{{ number_format($reading->reading_value, 2) }} {{ $meterData['meter']->utilityType->unit }}</td>
                                    <td data-label="Usage">
                                        @if ($reading->reading_value == 0)
                                            -
                                        @elseif ($consumption >= 0)
                                            <span class="text-success">{{ number_format($consumption, 2) }} {{ $meterData['meter']->utilityType->unit }}</span>
                                        @else
                                            <span class="text-danger">Error</span>
                                        @endif
                                    </td>
                                    <td data-label="Recorded By">{{ $reading->recordedBy->name }}</td>
                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td colspan="4" class="empty-state py-4">
                                        <i class="ti ti-gauge"></i>
                                        <p class="mb-0">No readings available</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer d-flex justify-content-center flex-column">
                    <!-- Standard pagination (visible on larger screens) -->
                    <div class="d-none d-md-flex justify-content-center standard-pagination">
                        {{ $meterData['readings']->links('vendor.pagination.custom-pagination') }}
                    </div>
                    
                    <!-- Load more button (visible on mobile) -->
                    @if($meterData['readings']->hasMorePages())
                    <div class="d-md-none">
                        <button 
                            class="load-more-button" 
                            data-meter-id="{{ $meterId }}" 
                            data-next-page="{{ $meterData['readings']->currentPage() + 1 }}"
                        >
                            <span>Load More Readings</span>
                            <i class="ti ti-chevron-down ms-2"></i>
                        </button>
                    </div>
                    @endif
                    
                    <!-- Infinite scroll loading indicator -->
                    <div class="infinite-scroll-loader" style="display: none;">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="ms-2 small">Loading readings...</span>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">
            <i class="ti ti-info-circle me-2"></i> No meter readings available.
        </div>
    @endif

    
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

@push('script')
<script src="{{ asset('assets/js/apexcharts.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- Utility Usage Chart ---
        const utilityData = @json($utilityData ?? []);
        const utilityChartSeries = [];
        const months = @json($months ?? []);
        
        // Create series data for each utility type
        for (const [utilityName, monthlyUsage] of Object.entries(utilityData)) {
            utilityChartSeries.push({
                name: utilityName,
                data: Object.values(monthlyUsage)
            });
        }
        
        if (utilityChartSeries.length > 0) {
            const utilityUsageOptions = {
                series: utilityChartSeries,
                chart: {
                    height: 350,
                    type: 'line',
                    toolbar: {
                        show: true
                    },
                    fontFamily: 'inherit',
                    foreColor: '#6c757d',
                    dropShadow: {
                        enabled: true,
                        top: 3,
                        left: 2,
                        blur: 4,
                        opacity: 0.2
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 4,
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 10
                    }
                },
                xaxis: {
                    categories: months,
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Meter Reading Value',
                        style: {
                            fontSize: '12px',
                            fontWeight: 400
                        }
                    },
                    labels: {
                        formatter: function(value) {
                            return value.toFixed(1);
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '14px'
                },
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
                markers: {
                    size: 4,
                    strokeWidth: 0,
                    hover: {
                        size: 6
                    }
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function(value) {
                            return value.toFixed(2);
                        }
                    }
                },
                // Optimize for mobile
                responsive: [{
                    breakpoint: 768,
                    options: {
                        chart: {
                            height: 280
                        },
                        legend: {
                            position: 'bottom',
                            horizontalAlign: 'center',
                            fontSize: '12px',
                            itemMargin: {
                                horizontal: 5,
                                vertical: 0
                            }
                        }
                    }
                }]
            };
            
            try {
                if (document.querySelector("#utility-usage-chart")) {
                    new ApexCharts(document.querySelector("#utility-usage-chart"), utilityUsageOptions).render();
                }
            } catch (error) {
                console.error("Error rendering utility chart:", error);
                if (document.querySelector("#utility-usage-chart")) {
                    document.querySelector("#utility-usage-chart").innerHTML = '<div class="text-center text-muted py-5">Error loading chart</div>';
                }
            }
        } else {
            if (document.querySelector("#utility-usage-chart")) {
                document.querySelector("#utility-usage-chart").innerHTML = '<div class="text-center text-muted py-5">No utility usage data available</div>';
            }
        }
        
        // ---- Enhanced Table Functionality ----
        
        // 1. Handle "Load More" buttons for mobile infinite scroll
        document.querySelectorAll('.load-more-button').forEach(button => {
            button.addEventListener('click', function() {
                const meterId = this.dataset.meterId;
                const nextPage = this.dataset.nextPage;
                loadMoreReadings(meterId, nextPage, this);
            });
        });
        
        // 2. Add search functionality for meter readings
        document.querySelectorAll('.reading-search-input').forEach(input => {
            input.addEventListener('input', debounce(function() {
                const meterId = this.dataset.meterId;
                const searchTerm = this.value.toLowerCase().trim();
                
                // Get all rows in this table
                const tableRows = document.querySelectorAll(`#readings-table-${meterId} .reading-row`);
                
                // If empty, show all rows
                if (searchTerm === '') {
                    tableRows.forEach(row => {
                        row.style.display = '';
                    });
                    
                    // Hide empty state if it exists
                    const emptyRow = document.querySelector(`#readings-table-${meterId} .empty-row`);
                    if (emptyRow) emptyRow.style.display = 'none';
                    return;
                }
                
                // Filter rows based on search term
                let anyVisible = false;
                
                tableRows.forEach(row => {
                    const date = row.querySelector('[data-label="Date"]')?.textContent.toLowerCase() || '';
                    const reading = row.querySelector('[data-label="Reading"]')?.textContent.toLowerCase() || '';
                    const recordedBy = row.querySelector('[data-label="Recorded By"]')?.textContent.toLowerCase() || '';
                    
                    if (date.includes(searchTerm) || reading.includes(searchTerm) || recordedBy.includes(searchTerm)) {
                        row.style.display = '';
                        anyVisible = true;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Show/hide empty state
                let emptyRow = document.querySelector(`#readings-table-${meterId} .empty-row`);
                
                if (!anyVisible) {
                    if (!emptyRow) {
                        emptyRow = document.createElement('tr');
                        emptyRow.className = 'empty-row';
                        emptyRow.innerHTML = `
                            <td colspan="4" class="empty-state py-4">
                                <i class="ti ti-search-off"></i>
                                <p class="mb-0">No readings match your search</p>
                            </td>
                        `;
                        document.querySelector(`#readings-table-${meterId} tbody`).appendChild(emptyRow);
                    } else {
                        emptyRow.style.display = '';
                        emptyRow.querySelector('p').textContent = 'No readings match your search';
                    }
                } else if (emptyRow) {
                    emptyRow.style.display = 'none';
                }
            }, 300));
        });
        
        // 3. Implement sortable columns
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', function() {
                const sortType = this.dataset.sort;
                const meterId = this.closest('.meter-card').id.replace('meter-', '');
                const tableBody = document.querySelector(`#readings-table-${meterId} tbody`);
                const rows = Array.from(tableBody.querySelectorAll('.reading-row'));
                
                // Toggle sort direction
                const currentDir = this.dataset.sortDir || 'asc';
                const newDir = currentDir === 'asc' ? 'desc' : 'asc';
                
                // Reset all headers
                document.querySelectorAll(`#readings-table-${meterId} .sortable`).forEach(h => {
                    h.dataset.sortDir = '';
                    h.querySelector('i').className = 'ti ti-arrows-sort ms-1';
                });
                
                // Set this header as sorted
                this.dataset.sortDir = newDir;
                this.querySelector('i').className = newDir === 'asc' 
                    ? 'ti ti-sort-ascending ms-1' 
                    : 'ti ti-sort-descending ms-1';
                
                // Sort the rows
                rows.sort((a, b) => {
                    let valueA, valueB;
                    
                    if (sortType === 'date') {
                        valueA = new Date(a.querySelector('[data-label="Date"]').textContent).getTime();
                        valueB = new Date(b.querySelector('[data-label="Date"]').textContent).getTime();
                    } else if (sortType === 'value' || sortType === 'usage') {
                        // Extract numeric values from reading or usage cells
                        const cellA = a.querySelector(`[data-label="${sortType === 'value' ? 'Reading' : 'Usage'}"]`).textContent;
                        const cellB = b.querySelector(`[data-label="${sortType === 'value' ? 'Reading' : 'Usage'}"]`).textContent;
                        
                        valueA = parseFloat(cellA.replace(/[^0-9.-]+/g, '')) || 0;
                        valueB = parseFloat(cellB.replace(/[^0-9.-]+/g, '')) || 0;
                    } else {
                        valueA = a.textContent.toLowerCase();
                        valueB = b.textContent.toLowerCase();
                    }
                    
                    if (newDir === 'asc') {
                        return valueA > valueB ? 1 : -1;
                    } else {
                        return valueA < valueB ? 1 : -1;
                    }
                });
                
                // Reorder the rows
                rows.forEach(row => tableBody.appendChild(row));
                
                // Move empty row to the end if it exists
                const emptyRow = tableBody.querySelector('.empty-row');
                if (emptyRow) tableBody.appendChild(emptyRow);
            });
        });
        
        // 4. Detect scroll position to trigger infinite scroll on mobile
        if (window.innerWidth < 768) {
            // Use IntersectionObserver for better performance
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const button = entry.target;
                        const meterId = button.dataset.meterId;
                        const nextPage = button.dataset.nextPage;
                        
                        // Don't trigger if already loading or button is hidden
                        if (!button.classList.contains('loading') && button.style.display !== 'none') {
                            loadMoreReadings(meterId, nextPage, button);
                        }
                    }
                });
            }, { threshold: 0.5 });
            
            // Observe all load more buttons
            document.querySelectorAll('.load-more-button').forEach(button => {
                observer.observe(button);
            });
        }
        
        // Utility function to load more readings via AJAX
        function loadMoreReadings(meterId, nextPage, button) {
            // Show loading state
            button.classList.add('loading');
            button.innerHTML = `
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Loading...</span>
            `;
            
            // Show loading indicator
            const loadingIndicator = document.querySelector(`#meter-${meterId} .infinite-scroll-loader`);
            if (loadingIndicator) loadingIndicator.style.display = 'flex';
            
            // Build the URL with meter ID and page number
            const url = `/tenant/utility-readings/${meterId}?page=${nextPage}`;
            
            // Make AJAX request
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Add new rows to the table
                const tableBody = document.querySelector(`#readings-table-${meterId} tbody`);
                const emptyRow = tableBody.querySelector('.empty-row');
                
                // If we had an empty state showing, remove it now
                if (emptyRow && data.readings.length > 0) {
                    emptyRow.style.display = 'none';
                }
                
                // Append each new reading row
                data.readings.forEach(reading => {
                    const row = document.createElement('tr');
                    row.className = 'reading-row';
                    
                    // Format the date
                    const readingDate = new Date(reading.reading_date);
                    const formattedDate = new Intl.DateTimeFormat('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    }).format(readingDate);
                    
                    // Calculate usage
                    let usageHTML = '';
                    if (reading.reading_value === 0) {
                        usageHTML = '-';
                    } else if (reading.consumption >= 0) {
                        usageHTML = `<span class="text-success">${reading.consumption.toFixed(2)} ${reading.unit}</span>`;
                    } else {
                        usageHTML = `<span class="text-danger">Error</span>`;
                    }
                    
                    // Build the row HTML
                    row.innerHTML = `
                        <td data-label="Date">${formattedDate}</td>
                        <td data-label="Reading">${reading.reading_value.toFixed(2)} ${reading.unit}</td>
                        <td data-label="Usage">${usageHTML}</td>
                        <td data-label="Recorded By">${reading.recorded_by}</td>
                    `;
                    
                    tableBody.appendChild(row);
                });
                
                // Update button for next page or hide it if no more pages
                if (data.has_more_pages) {
                    button.classList.remove('loading');
                    button.innerHTML = `<span>Load More Readings</span><i class="ti ti-chevron-down ms-2"></i>`;
                    button.dataset.nextPage = parseInt(nextPage) + 1;
                } else {
                    button.style.display = 'none';
                }
                
                // Hide loading indicator
                if (loadingIndicator) loadingIndicator.style.display = 'none';
            })
            .catch(error => {
                console.error('Error loading readings:', error);
                
                // Reset button to allow retry
                button.classList.remove('loading');
                button.innerHTML = `<span>Try Again</span><i class="ti ti-refresh ms-2"></i>`;
                
                // Hide loading indicator
                if (loadingIndicator) loadingIndicator.style.display = 'none';
            });
        }
        
        // Utility function to debounce input events
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func.apply(this, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    });
</script>
@endpush
