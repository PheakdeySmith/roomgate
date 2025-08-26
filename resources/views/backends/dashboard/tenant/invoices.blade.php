@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
@endphp

@extends('backends.layouts.app')

@section('title', 'My Invoices | RoomGate')

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
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background-color: var(--bg-softer);
        color: var(--primary-color);
        padding: 0;
        border-radius: 12px;
        margin-right: 0.75rem;
        transition: all 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .back-button:hover {
        transform: translateX(-2px);
        background-color: var(--icon-bg);
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
        border-radius: 12px;
        background-color: var(--bg-softer);
        color: var(--primary-color);
        border: none;
        padding: 0;
        transition: all 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .header-icon:hover {
        transform: translateY(-2px);
        background-color: var(--icon-bg);
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
    
    .invoice-card {
        background-color: white;
        border-radius: var(--card-radius);
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    
    .invoice-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .invoice-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }
    
    .icon-paid {
        background-color: #10B981;
    }
    
    .icon-pending {
        background-color: #F59E0B;
    }
    
    .icon-overdue {
        background-color: #EF4444;
    }
    
    .invoice-details {
        flex: 1;
    }
    
    .invoice-amount {
        font-size: 1.25rem;
        font-weight: 700;
    }
    
    .invoice-status {
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
    
    .status-paid {
        background-color: rgba(16, 185, 129, 0.12);
        color: #10B981;
    }
    
    .status-pending {
        background-color: rgba(245, 158, 11, 0.12);
        color: #F59E0B;
    }
    
    .status-overdue {
        background-color: rgba(239, 68, 68, 0.12);
        color: #EF4444;
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
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    /* Fix for active state */
    a.category-tag.text-decoration-none.active {
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
    
    /* Modal styles */
    .modal-content {
        border-radius: var(--card-radius);
        border: none;
        overflow: hidden;
    }
    
    .modal-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.25rem 1.5rem;
    }
    
    .modal-footer {
        border-top: 1px solid rgba(0,0,0,0.05);
        padding: 1.25rem 1.5rem;
    }
    
    .modal-body {
        padding: 1.5rem;
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
                <h1>My Invoices</h1>
            </div>
            <div class="dashboard-header-actions">
                <button class="header-icon" data-bs-toggle="tooltip" title="Filter Invoices">
                    <i class="ti ti-filter"></i>
                </button>
                <button class="header-icon" data-bs-toggle="tooltip" title="Download History">
                    <i class="ti ti-download"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="mb-3">
        <!-- Category Filter -->
        <div class="category-filter">
            <div class="d-flex">
                <a href="{{ route('tenant.invoices') }}" class="category-tag text-decoration-none {{ !request('status') ? 'active' : '' }}">
                    <span>All</span>
                </a>
                <a href="{{ route('tenant.invoices', ['status' => 'paid']) }}" class="category-tag text-decoration-none {{ request('status') == 'paid' ? 'active' : '' }}">
                    <span>Paid</span>
                </a>
                <a href="{{ route('tenant.invoices', ['status' => 'pending']) }}" class="category-tag text-decoration-none {{ request('status') == 'pending' ? 'active' : '' }}">
                    <span>Pending</span>
                </a>
                <a href="{{ route('tenant.invoices', ['status' => 'overdue']) }}" class="category-tag text-decoration-none {{ request('status') == 'overdue' ? 'active' : '' }}">
                    <span>Overdue</span>
                </a>
                <a href="{{ route('tenant.invoices', ['status' => 'recent']) }}" class="category-tag text-decoration-none {{ request('status') == 'recent' ? 'active' : '' }}">
                    <span>Recent</span>
                </a>
                <a href="{{ route('tenant.invoices', ['status' => 'rent_only']) }}" class="category-tag text-decoration-none {{ request('status') == 'rent_only' ? 'active' : '' }}">
                    <span>Rent Only</span>
                </a>
            </div>
        </div>
        
        <!-- Invoices Section -->
        <div class="dashboard-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="ti ti-receipt me-2"></i>
                    All Invoices
                </h3>
            </div>
            
            <!-- Invoices List -->
            @forelse ($invoices as $invoice)
                @php
                    $statusClass = '';
                    $iconClass = '';
                    $statusIcon = '';
                    
                    if ($invoice->status == 'paid') {
                        $statusClass = 'status-paid';
                        $iconClass = 'icon-paid';
                        $statusIcon = 'check';
                    } elseif ($invoice->status == 'overdue') {
                        $statusClass = 'status-overdue';
                        $iconClass = 'icon-overdue';
                        $statusIcon = 'alert-triangle';
                    } else {
                        $statusClass = 'status-pending';
                        $iconClass = 'icon-pending';
                        $statusIcon = 'clock';
                    }
                @endphp
                
                <div class="invoice-card animate-fade-in invoice-details-btn" data-bs-toggle="modal" data-bs-target="#invoiceDetailsModal" data-invoice-id="{{ $invoice->id }}">
                    <div class="d-flex">
                        <div class="invoice-icon {{ $iconClass }} me-3">
                            <i class="ti ti-{{ $statusIcon }}"></i>
                        </div>
                        <div class="invoice-details">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h4 class="mb-0">Invoice #{{ $invoice->invoice_number }}</h4>
                                <div class="invoice-status {{ $statusClass }}">
                                    {{ ucfirst($invoice->status) }}
                                </div>
                            </div>
                            <div class="text-muted small mb-2">
                                Issued: {{ $invoice->issue_date->format('M d, Y') }} | 
                                Due: {{ $invoice->due_date->format('M d, Y') }}
                            </div>
                            <div class="d-flex justify-content-between align-items-end">
                                <div>
                                    <div class="text-muted small">Total</div>
                                    <div class="fw-medium">${{ number_format($invoice->total_amount, 2) }}</div>
                                </div>
                                <div>
                                    <div class="text-muted small">Paid</div>
                                    <div class="fw-medium">${{ number_format($invoice->paid_amount, 2) }}</div>
                                </div>
                                <div>
                                    <div class="text-muted small">Balance</div>
                                    <div class="invoice-amount">${{ number_format($invoice->balance, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-light rounded-4 animate-fade-in">
                    <i class="ti ti-receipt-off display-1 text-muted mb-3"></i>
                    <h4>No Invoices Found</h4>
                    <p class="text-muted">There are no invoices recorded for your account at this time.</p>
                </div>
            @endforelse
            
            <!-- Pagination -->
            @if($invoices->hasPages())
                <div class="pagination-container">
                    {{ $invoices->links('vendor.pagination.custom-pagination') }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Mobile Navigation for small screens -->
    <div class="d-md-none mobile-nav">
        <div class="mobile-nav-wrapper">
            <a href="{{ route('tenant.dashboard') }}" class="mobile-nav-item">
                <i class="ti ti-home mobile-nav-icon"></i>
                <span class="mobile-nav-label">Home</span>
            </a>
            <a href="{{ route('tenant.invoices') }}" class="mobile-nav-item active">
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

<!-- Invoice Details Modal -->
<div class="modal fade" id="invoiceDetailsModal" tabindex="-1" aria-labelledby="invoiceDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Handle invoice details modal
        $('.invoice-details-btn').on('click', function() {
            const invoiceId = $(this).data('invoice-id');
            loadInvoiceDetails(invoiceId);
        });
        
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
                    let statusIcon;
                    if (response.invoice.status === 'paid') {
                        statusClass = 'status-paid';
                        statusIcon = 'check';
                    } else if (response.invoice.status === 'overdue') {
                        statusClass = 'status-overdue';
                        statusIcon = 'alert-triangle';
                    } else {
                        statusClass = 'status-pending';
                        statusIcon = 'clock';
                    }
                    
                    // Build the invoice details HTML
                    const invoiceHtml = `
                        <div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="fw-bold mb-0">Invoice #${response.invoice.invoice_number}</h4>
                                    <span class="invoice-status ${statusClass}">
                                        <i class="ti ti-${statusIcon} me-1"></i>
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
                            
                            <div class="bg-light p-3 rounded-3">
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
                                  <button class="btn btn-primary btn-action">
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
