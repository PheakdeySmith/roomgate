@extends('backends.layouts.app')

@section('title', 'Financial Report')

@push('style')
<style>
    .filter-form {
        background-color: var(--bs-light);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .table-container {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }
    
    .table-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .table-title {
        margin: 0;
        font-size: 18px;
        font-weight: 500;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }
    
    .bg-paid {
        background-color: #d4edda;
        color: #155724;
    }
    
    .bg-unpaid {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .bg-partial {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .bg-overdue {
        background-color: #ffe6e6;
        color: #9f1a1a;
    }
    
    .stats-card {
        transition: all 0.3s;
        border-radius: 10px;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    /* Chart styles */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
        min-height: 250px;
    }
    
    .chart-section {
        margin-bottom: 20px;
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
        
        .dataTable {
            width: 100% !important;
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
        /* Hide elements that shouldn't be printed */
        .navbar, .leftside-menu, .footer, .filter-form, 
        button:not(.exclude-from-print), .btn, .page-title-box,
        .dataTable_filter, .dataTable_length, .dataTable_paginate, 
        .dataTables_info, .dataTables_wrapper .row:first-child, 
        .dataTables_wrapper .row:last-child {
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
        
        /* Reset body padding and background */
        body, .content-page {
            padding: 0 !important;
            margin: 0 !important;
            background: white !important;
            width: 100% !important;
        }
        
        /* Make content full width */
        .container-fluid {
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        /* Add professional header for print */
        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        
        .print-header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
            font-weight: bold;
        }
        
        .print-header .company-details {
            margin-top: 5px;
            color: #555;
        }
        
        .print-header .report-period {
            margin-top: 5px;
            font-style: italic;
            color: #777;
        }
        
        /* Add professional footer */
        .print-footer {
            display: block !important;
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
        }
        
        /* Format tables for printing */
        table.dataTable {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        
        table.dataTable th, table.dataTable td {
            padding: 8px !important;
            border: 1px solid #ddd !important;
        }
        
        table.dataTable th {
            background-color: #f5f5f5 !important;
        }
        
        /* Ensure all text is black for better printing */
        .text-muted {
            color: #333 !important;
        }
        
        /* Add page numbers */
        @page {
            margin: 1cm;
        }
        
        /* Improve card styling for print */
        .card-body {
            padding: 15px !important;
        }
        
        /* Make sure content isn't cut off */
        .row {
            display: block !important;
        }
        
        .col-md-3, .col-md-4, .col-md-8, .col-md-12, .col-12 {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Print-only header -->
    <div class="print-header" style="display: none;">
        <h1>FINANCIAL REPORT</h1>
        <div class="company-details">RoomGate Property Management System</div>
        <div class="report-period">
            @if(!empty($selectedMonth) && is_numeric($selectedMonth) && isset($monthNames[(int)$selectedMonth]))
                Period: {{ $monthNames[(int)$selectedMonth] }} {{ $selectedYear }}
            @else
                Period: Full Year {{ $selectedYear }}
            @endif
            @if($selectedProperty)
                | Property: {{ $selectedProperty->name }}
            @else
                | All Properties
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
                        <li class="breadcrumb-item active">Financial Report</li>
                    </ol>
                </div>
                <h4 class="page-title">Financial Report</h4>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="row">
        <div class="col-12">
            <div class="filter-form">
                <form action="{{ route('landlord.reports.financial-report') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
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
                        <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                            <label for="year" class="form-label">Year</label>
                            <select name="year" id="year" class="form-select">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                            <label for="month" class="form-label">Month</label>
                            <select name="month" id="month" class="form-select">
                                <option value="">All Months</option>
                                @foreach($monthNames as $num => $name)
                                    <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                        </div>
                        <div class="col-lg-1 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('landlord.reports.financial-report') }}" class="btn btn-secondary w-100">Reset</a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <button type="button" class="btn btn-success w-100" onclick="printReport()">
                                <i class="ti ti-printer me-1"></i> Print Report
                            </button>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="exportToPDF()">
                                <i class="ti ti-file-export me-1"></i> Export PDF
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Key Statistics -->
    <div class="row mb-4 chart-section">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-primary-subtle p-2 rounded">
                                <i class="ti ti-receipt text-primary fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">Total Revenue</h5>
                        </div>
                    </div>
                    <h2 class="mt-3 mb-2">{{ number_format($totalAmount, 2) }}</h2>
                    <p class="mb-0 text-muted">
                        @if(!empty($selectedMonth) && is_numeric($selectedMonth) && isset($monthNames[(int)$selectedMonth]))
                            {{ $monthNames[(int)$selectedMonth] }} {{ $selectedYear }}
                        @else
                            {{ $selectedYear }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-success-subtle p-2 rounded">
                                <i class="ti ti-check-circle text-success fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">Paid Amount</h5>
                        </div>
                    </div>
                    <h2 class="mt-3 mb-2">{{ number_format($paidAmount, 2) }}</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-success">{{ $totalAmount > 0 ? number_format(($paidAmount / $totalAmount) * 100, 1) : 0 }}%</span> of total revenue
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-danger-subtle p-2 rounded">
                                <i class="ti ti-alert-circle text-danger fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">Outstanding Amount</h5>
                        </div>
                    </div>
                    <h2 class="mt-3 mb-2">{{ number_format($outstandingAmount, 2) }}</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-danger">{{ $totalAmount > 0 ? number_format(($outstandingAmount / $totalAmount) * 100, 1) : 0 }}%</span> of total revenue
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-warning-subtle p-2 rounded">
                                <i class="ti ti-file-invoice text-warning fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">Total Invoices</h5>
                        </div>
                    </div>
                    <h2 class="mt-3 mb-2">{{ number_format($totalInvoices) }}</h2>
                    <p class="mb-0 text-muted">
                        {{ $paidInvoices }} paid, {{ $totalInvoices - $paidInvoices }} unpaid
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Print-only Financial Summary Table -->
    <div class="print-only-table" style="display: none;">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">Financial Summary</h4>
                <div class="table-responsive">
                    <table class="table table-centered table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Total Revenue</th>
                                <th>Paid Amount</th>
                                <th>Outstanding Amount</th>
                                <th>Total Invoices</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ number_format($totalAmount, 2) }}</td>
                                <td>
                                    {{ number_format($paidAmount, 2) }}
                                    ({{ $totalAmount > 0 ? number_format(($paidAmount / $totalAmount) * 100, 1) : 0 }}%)
                                </td>
                                <td>
                                    {{ number_format($outstandingAmount, 2) }}
                                    ({{ $totalAmount > 0 ? number_format(($outstandingAmount / $totalAmount) * 100, 1) : 0 }}%)
                                </td>
                                <td>
                                    {{ number_format($totalInvoices) }}
                                    ({{ $paidInvoices }} paid, {{ $totalInvoices - $paidInvoices }} unpaid)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="row mb-4">
        <!-- Monthly Revenue Chart -->
        <div class="col-lg-8 col-md-12 mb-4 chart-section">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="header-title mb-3">Monthly Revenue ({{ $selectedYear }})</h4>
                    <div class="chart-container">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Payment Status Chart -->
        <div class="col-lg-4 col-md-12 mb-4 chart-section">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="header-title mb-3">Payment Status</h4>
                    <div class="chart-container">
                        <canvas id="paymentStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Print-only tables (hidden in web view, shown only when printing) -->
    <div class="print-only-table" style="display: none;">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">Monthly Revenue ({{ $selectedYear }})</h4>
                <div class="table-responsive">
                    <table class="table table-centered table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                <th>Total Revenue</th>
                                <th>Paid Amount</th>
                                <th>Outstanding</th>
                                <th>Payment Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyData as $index => $month)
                            <tr>
                                <td>{{ isset(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][$index]) ? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][$index] : 'Month ' . ($index + 1) }}</td>
                                <td>{{ number_format($month['paid'] + $month['unpaid'], 2) }}</td>
                                <td>{{ number_format($month['paid'], 2) }}</td>
                                <td>{{ number_format($month['unpaid'], 2) }}</td>
                                <td>
                                    @php
                                        $total = $month['paid'] + $month['unpaid'];
                                        $percentage = $total > 0 ? ($month['paid'] / $total) * 100 : 0;
                                    @endphp
                                    {{ number_format($percentage, 1) }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">Payment Status Summary</h4>
                <div class="table-responsive">
                    <table class="table table-centered table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Paid</td>
                                <td>{{ $paymentStatusStats['paid'] }}</td>
                                <td>{{ $totalInvoices > 0 ? number_format(($paymentStatusStats['paid'] / $totalInvoices) * 100, 1) : 0 }}%</td>
                            </tr>
                            <tr>
                                <td>Partial</td>
                                <td>{{ $paymentStatusStats['partial'] }}</td>
                                <td>{{ $totalInvoices > 0 ? number_format(($paymentStatusStats['partial'] / $totalInvoices) * 100, 1) : 0 }}%</td>
                            </tr>
                            <tr>
                                <td>Unpaid</td>
                                <td>{{ $paymentStatusStats['unpaid'] }}</td>
                                <td>{{ $totalInvoices > 0 ? number_format(($paymentStatusStats['unpaid'] / $totalInvoices) * 100, 1) : 0 }}%</td>
                            </tr>
                            <tr>
                                <td>Overdue</td>
                                <td>{{ $paymentStatusStats['overdue'] }}</td>
                                <td>{{ $totalInvoices > 0 ? number_format(($paymentStatusStats['overdue'] / $totalInvoices) * 100, 1) : 0 }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Revenue by Property -->
    <div class="row mb-4">
        <div class="col-12 chart-section">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Revenue by Property</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-centered table-hover dt-responsive nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Property</th>
                                    <th>Total Revenue</th>
                                    <th>Paid Amount</th>
                                    <th>Outstanding</th>
                                    <th>Payment Rate</th>
                                    <th>Number of Invoices</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revenueByProperty as $property)
                                <tr>
                                    <td>{{ $property['name'] }}</td>
                                    <td>{{ number_format($property['total_revenue'], 2) }}</td>
                                    <td>{{ number_format($property['paid_amount'], 2) }}</td>
                                    <td>{{ number_format($property['outstanding'], 2) }}</td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 100%; max-width: 120px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                style="width: {{ $property['payment_rate'] }}%"></div>
                                        </div>
                                        <span class="ms-1">{{ number_format($property['payment_rate'], 1) }}%</span>
                                    </td>
                                    <td>{{ $property['invoice_count'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Print-only Property Revenue Table -->
    <div class="print-only-table" style="display: none;">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">Revenue by Property</h4>
                <div class="table-responsive">
                    <table class="table table-centered table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Property</th>
                                <th>Total Revenue</th>
                                <th>Paid Amount</th>
                                <th>Outstanding</th>
                                <th>Payment Rate</th>
                                <th>Number of Invoices</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($revenueByProperty as $property)
                            <tr>
                                <td>{{ $property['name'] }}</td>
                                <td>{{ number_format($property['total_revenue'], 2) }}</td>
                                <td>{{ number_format($property['paid_amount'], 2) }}</td>
                                <td>{{ number_format($property['outstanding'], 2) }}</td>
                                <td>{{ number_format($property['payment_rate'], 1) }}%</td>
                                <td>{{ $property['invoice_count'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Invoice List -->
    <div class="row">
        <div class="col-12 chart-section">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Invoice List</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-centered table-hover dt-responsive nowrap w-100 dataTable" id="invoices-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Tenant</th>
                                    <th>Room</th>
                                    <th>Property</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                <tr>
                                    <td data-order="{{ $invoice->invoice_number }}">
                                        <span class="d-inline-block text-truncate" style="max-width: 120px;">
                                            {{ $invoice->invoice_number }}
                                        </span>
                                    </td>
                                    <td data-order="{{ $invoice->contract->tenant->name ?? 'N/A' }}">
                                        <span class="d-inline-block text-truncate" style="max-width: 120px;">
                                            {{ $invoice->contract->tenant->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $invoice->contract->room->room_number ?? 'N/A' }}</td>
                                    <td data-order="{{ $invoice->contract->room->property->name ?? 'N/A' }}">
                                        <span class="d-inline-block text-truncate" style="max-width: 120px;">
                                            {{ $invoice->contract->room->property->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td data-order="{{ $invoice->issue_date }}">
                                        {{ \Carbon\Carbon::parse($invoice->issue_date)->format('d M Y') }}
                                    </td>
                                    <td data-order="{{ $invoice->due_date }}">
                                        {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}
                                    </td>
                                    <td data-order="{{ $invoice->total_amount }}">
                                        {{ number_format($invoice->total_amount, 2) }}
                                    </td>
                                    <td>
                                        @if($invoice->status === 'paid')
                                            <span class="payment-status status-paid"></span>
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($invoice->status === 'partial')
                                            <span class="payment-status status-partial"></span>
                                            <span class="badge bg-warning">Partial</span>
                                        @elseif($invoice->due_date < now() && $invoice->status !== 'paid')
                                            <span class="payment-status status-overdue"></span>
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="payment-status status-unpaid"></span>
                                            <span class="badge bg-secondary">Unpaid</span>
                                        @endif
                                    </td>
                                    <td data-order="{{ $invoice->payment_date ?? '' }}">
                                        @if($invoice->payment_date)
                                            {{ \Carbon\Carbon::parse($invoice->payment_date)->format('d M Y') }}
                                        @else
                                            <span class="text-muted">-</span>
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
    
    <!-- Print-only Invoice List -->
    <div class="print-only-table" style="display: none;">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">Invoice List</h4>
                <div class="table-responsive">
                    <table class="table table-centered table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Tenant</th>
                                <th>Room</th>
                                <th>Property</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->contract->tenant->name ?? 'N/A' }}</td>
                                <td>{{ $invoice->contract->room->room_number ?? 'N/A' }}</td>
                                <td>{{ $invoice->contract->room->property->name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->issue_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</td>
                                <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                <td>
                                    @if($invoice->status === 'paid')
                                        Paid
                                    @elseif($invoice->status === 'partial')
                                        Partial
                                    @elseif($invoice->due_date < now() && $invoice->status !== 'paid')
                                        Overdue
                                    @else
                                        Unpaid
                                    @endif
                                </td>
                                <td>
                                    @if($invoice->payment_date)
                                        {{ \Carbon\Carbon::parse($invoice->payment_date)->format('d M Y') }}
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
        <div>This report is system-generated and valid as of {{ now()->format('F d, Y h:i A') }}</div>
        <div>Page <span class="page-number"></span></div>
    </div>
</div>
@endsection

@push('script')
<!-- Load all required libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="{{ asset('assets') }}/js/vendor/jquery.dataTables.min.js"></script>
<script src="{{ asset('assets') }}/js/vendor/dataTables.bootstrap5.js"></script>
<script src="{{ asset('assets') }}/js/vendor/dataTables.responsive.min.js"></script>
<!-- Add html2pdf library for PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    // Define global chart variables so they can be accessed across functions
    let monthlyRevenueChart, paymentStatusChart;
    
    // Wait for window load to ensure all resources are available
    window.onload = function() {
        console.log('Window fully loaded for financial report');
        // Initialize charts immediately
        initializeCharts();
    };
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded for financial report');
        
        try {
            // Initialize DataTables with mobile-friendly options
            $('#invoices-table').DataTable({
                responsive: true,
                lengthChange: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                pageLength: 10,
                language: {
                    paginate: {
                        previous: "<i class='ti ti-chevron-left'>",
                        next: "<i class='ti ti-chevron-right'>"
                    },
                    info: "_START_ to _END_ of _TOTAL_",
                    lengthMenu: "Show _MENU_"
                },
                columnDefs: [
                    // Set priority for responsive behavior
                    { responsivePriority: 1, targets: [0, 6, 7] }, // Invoice #, Amount, Status are highest priority
                    { responsivePriority: 2, targets: [1, 2] },    // Tenant, Room are next priority
                    { responsivePriority: 3, targets: [4, 5, 8] }, // Dates are medium priority
                    { responsivePriority: 4, targets: 3 }          // Property is lowest priority
                ]
            });
            console.log('DataTable initialized successfully');
        } catch (e) {
            console.error('Error initializing DataTable:', e);
        }
        
        // Initialize charts if not yet initialized
        if (!monthlyRevenueChart || !paymentStatusChart) {
            setTimeout(function() {
                console.log('Delayed chart initialization');
                initializeCharts();
            }, 500);
        }
    });
    
    function initializeCharts() {
        console.log('Initializing charts');
        
        // Monthly Revenue Chart
        initializeMonthlyRevenueChart();
        
        // Payment Status Chart
        initializePaymentStatusChart();
        
        // Handle window resize for better mobile experience
        window.addEventListener('resize', handleChartResize);
    }
    
    function initializeMonthlyRevenueChart() {
        try {
            // Check if chart container exists
            const monthlyChartContainer = document.getElementById('monthlyRevenueChart');
            if (!monthlyChartContainer) {
                console.error('Monthly chart container not found');
                return;
            }
            console.log('Monthly chart container found:', monthlyChartContainer);
            
            // Set up data
            const monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const paidAmounts = Array(12).fill(0);
            const outstandingAmounts = Array(12).fill(0);
            
            // Debug data
            console.log('Monthly data:', @json($monthlyData));
            
            @foreach($monthlyData as $index => $month)
                @if($index >= 1 && $index <= 12)
                    paidAmounts[{{ $index - 1 }}] = {{ $month['paid'] ?? 0 }};
                    outstandingAmounts[{{ $index - 1 }}] = {{ $month['unpaid'] ?? 0 }};
                @endif
            @endforeach
            
            // Check if chart already exists and destroy it
            if (monthlyRevenueChart) {
                monthlyRevenueChart.destroy();
            }
            
            const ctx = monthlyChartContainer.getContext('2d');
            if (!ctx) {
                console.error('Could not get 2D context for monthly chart');
                return;
            }
            
            monthlyRevenueChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [
                        {
                            label: 'Paid',
                            data: paidAmounts,
                            backgroundColor: '#0ACF97',
                            borderColor: '#0ACF97',
                            borderWidth: 1
                        },
                        {
                            label: 'Outstanding',
                            data: outstandingAmounts,
                            backgroundColor: '#f1556c',
                            borderColor: '#f1556c',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: window.innerWidth < 768 ? 'bottom' : 'top',
                            labels: {
                                boxWidth: window.innerWidth < 768 ? 10 : 12,
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            ticks: {
                                font: {
                                    size: window.innerWidth < 768 ? 9 : 11
                                }
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: window.innerWidth < 768 ? 9 : 11
                                }
                            },
                            title: {
                                display: true,
                                text: 'Amount',
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        }
                    }
                }
            });
            console.log('Monthly revenue chart initialized successfully');
        } catch (error) {
            console.error('Error initializing monthly revenue chart:', error);
        }
    }
    
    function initializePaymentStatusChart() {
        try {
            // Check if chart container exists
            const paymentChartContainer = document.getElementById('paymentStatusChart');
            if (!paymentChartContainer) {
                console.error('Payment chart container not found');
                return;
            }
            console.log('Payment chart container found:', paymentChartContainer);
            
            // Check if chart already exists and destroy it
            if (paymentStatusChart) {
                paymentStatusChart.destroy();
            }
            
            const pctx = paymentChartContainer.getContext('2d');
            if (!pctx) {
                console.error('Could not get 2D context for payment chart');
                return;
            }
            
            const paymentStatusData = {
                labels: ['Paid', 'Partial', 'Unpaid', 'Overdue'],
                datasets: [{
                    data: [
                        {{ $paymentStatusStats['paid'] ?? 0 }}, 
                        {{ $paymentStatusStats['partial'] ?? 0 }}, 
                        {{ $paymentStatusStats['unpaid'] ?? 0 }}, 
                        {{ $paymentStatusStats['overdue'] ?? 0 }}
                    ],
                    backgroundColor: ['#0ACF97', '#f7b84b', '#6c757d', '#f1556c'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            };
            
            paymentStatusChart = new Chart(pctx, {
                type: 'doughnut',
                data: paymentStatusData,
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
                                    const total = {{ $totalInvoices ?? 0 }};
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
            console.log('Payment status chart initialized successfully');
        } catch (error) {
            console.error('Error initializing payment status chart:', error);
        }
    }
    
    function handleChartResize() {
        if (monthlyRevenueChart) {
            console.log('Updating monthly chart for resize');
            
            // Update legend position and font sizes based on screen width
            monthlyRevenueChart.options.plugins.legend.position = window.innerWidth < 768 ? 'bottom' : 'top';
            monthlyRevenueChart.options.plugins.legend.labels.boxWidth = window.innerWidth < 768 ? 10 : 12;
            monthlyRevenueChart.options.plugins.legend.labels.font.size = window.innerWidth < 768 ? 10 : 12;
            monthlyRevenueChart.options.scales.x.ticks.font.size = window.innerWidth < 768 ? 9 : 11;
            monthlyRevenueChart.options.scales.y.ticks.font.size = window.innerWidth < 768 ? 9 : 11;
            monthlyRevenueChart.options.scales.y.title.font.size = window.innerWidth < 768 ? 10 : 12;
            
            monthlyRevenueChart.update();
        }
        
        if (paymentStatusChart) {
            console.log('Updating payment chart for resize');
            
            // Update legend position and font sizes based on screen width
            paymentStatusChart.options.plugins.legend.position = window.innerWidth < 768 ? 'bottom' : 'right';
            paymentStatusChart.options.plugins.legend.labels.boxWidth = window.innerWidth < 768 ? 10 : 15;
            paymentStatusChart.options.plugins.legend.labels.font.size = window.innerWidth < 768 ? 10 : 12;
            
            paymentStatusChart.update();
        }
    }
    
    // Print report function
    function printReport() {
        document.title = "Financial Report - " + new Date().toLocaleDateString();
        
        // Make sure print-only elements are shown
        const printOnlyElements = document.querySelectorAll('.print-only-table');
        printOnlyElements.forEach(el => el.style.display = 'block');
        
        // Call print
        window.print();
        
        // Reset after printing
        setTimeout(function() {
            document.title = "Financial Report";
            printOnlyElements.forEach(el => el.style.display = 'none');
        }, 1000);
    }
    
    // Export to PDF function
    function exportToPDF() {
        // Show loading indicator
        const loadingEl = document.createElement('div');
        loadingEl.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75';
        loadingEl.style.zIndex = '9999';
        loadingEl.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><span class="ms-2">Generating PDF...</span>';
        document.body.appendChild(loadingEl);
        
        // Get the container element
        const element = document.querySelector('.container-fluid');
        
        // Show print-only elements
        const printHeader = document.querySelector('.print-header');
        const printFooter = document.querySelector('.print-footer');
        const printOnlyElements = document.querySelectorAll('.print-only-table');
        
        // Make charts and cards invisible for PDF
        const chartSections = document.querySelectorAll('.chart-section');
        
        printHeader.style.display = 'block';
        printFooter.style.display = 'block';
        printOnlyElements.forEach(el => el.style.display = 'block');
        chartSections.forEach(el => el.style.display = 'none');
        
        // Hide elements that shouldn't be in PDF
        const elementsToHide = document.querySelectorAll('.filter-form, .page-title-box, .btn');
        elementsToHide.forEach(el => el.style.display = 'none');
        
        // Setup PDF options
        const opt = {
            margin: [10, 10, 10, 10],
            filename: 'Financial_Report_' + new Date().toISOString().split('T')[0] + '.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, logging: false },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        
        // Generate PDF
        html2pdf().from(element).set(opt).save()
            .then(() => {
                // Restore elements visibility after PDF generation
                printHeader.style.display = '';
                printFooter.style.display = '';
                printOnlyElements.forEach(el => el.style.display = 'none');
                chartSections.forEach(el => el.style.display = '');
                elementsToHide.forEach(el => el.style.display = '');
                
                // Remove loading indicator
                document.body.removeChild(loadingEl);
            });
    }
</script>
@endpush
