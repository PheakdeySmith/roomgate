@extends('backends.layouts.app')

@section('title', 'Invoices | RoomGate')

@push('style')
    <link href="{{ asset('assets') }}/css/flatpickr.min.css" rel="stylesheet" type="text/css">
    <style>
        .filter-group {
            padding: 0.5rem 1rem;
        }

        .filter-option-list .filter-option {
            display: block;
            text-decoration: none;
            color: var(--bs-body-color);
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
            transition: background-color 0.15s ease-in-out;
        }

        .filter-option-list .filter-option:hover {
            background-color: var(--bs-secondary-bg);
        }

        .filter-option-list .filter-option.active {
            background-color: var(--bs-secondary-bg);
            font-weight: 600;
        }

        #filtersOffcanvas {
            height: 65vh;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        @media (min-width: 768px) {
            .border-end-md {
                border-right: 1px solid var(--bs-border-color);
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-container">
        {{-- Page Header --}}
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Invoices</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Invoices</li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-0 text-center align-items-center">

                    {{-- 1. New Contracts --}}
                    <div class="col border-end-md border-light border-dashed">
                        <div class="p-3">
                            <h5 class="text-muted fs-13 text-uppercase">New Contracts</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                                <h3 class="mb-0 fw-bold">{{ number_format($stats['new_contracts']['current']) }}</h3>
                            </div>
                            <p class="mb-0 text-muted">
                                <span
                                    class="{{ $stats['new_contracts']['change'] >= 0 ? 'text-success' : 'text-danger' }} me-2">
                                    <i
                                        class="ti ti-caret-{{ $stats['new_contracts']['change'] >= 0 ? 'up' : 'down' }}-filled"></i>
                                    {{ number_format(abs($stats['new_contracts']['change']), 1) }}%
                                </span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div>
                    </div>

                    {{-- 2. Total Revenue --}}
                    <div class="col border-end-md border-light border-dashed">
                        <div class="p-3">
                            <h5 class="text-muted fs-13 text-uppercase">Total Revenue</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                                <h3 class="mb-0 fw-bold">{!! format_money($stats['revenue']['current']) !!}</h3>
                            </div>
                            <p class="mb-0 text-muted">
                                <span class="{{ $stats['revenue']['change'] >= 0 ? 'text-success' : 'text-danger' }} me-2">
                                    <i
                                        class="ti ti-caret-{{ $stats['revenue']['change'] >= 0 ? 'up' : 'down' }}-filled"></i>
                                    {{ number_format(abs($stats['revenue']['change']), 1) }}%
                                </span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div>
                    </div>

                    {{-- 3. Utility Revenue --}}
                    <div class="col border-end-md border-light border-dashed">
                        <div class="p-3">
                            <h5 class="text-muted fs-13 text-uppercase">Utility Revenue</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                                <h3 class="mb-0 fw-bold">{!! format_money($stats['utility_revenue']['current']) !!}</h3>
                            </div>
                            <p class="mb-0 text-muted">
                                <span
                                    class="{{ $stats['utility_revenue']['change'] >= 0 ? 'text-success' : 'text-danger' }} me-2">
                                    <i
                                        class="ti ti-caret-{{ $stats['utility_revenue']['change'] >= 0 ? 'up' : 'down' }}-filled"></i>
                                    {{ number_format(abs($stats['utility_revenue']['change']), 1) }}%
                                </span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div>
                    </div>

                    {{-- 4. Amount Paid --}}
                    <div class="col border-end-md border-light border-dashed">
                        <div class="p-3">
                            <h5 class="text-muted fs-13 text-uppercase">Amount Paid</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                                <h3 class="mb-0 fw-bold">{!! format_money($stats['paid']['current']) !!}</h3>
                            </div>
                            <p class="mb-0 text-muted">
                                <span class="{{ $stats['paid']['change'] >= 0 ? 'text-success' : 'text-danger' }} me-2">
                                    <i class="ti ti-caret-{{ $stats['paid']['change'] >= 0 ? 'up' : 'down' }}-filled"></i>
                                    {{ number_format(abs($stats['paid']['change']), 1) }}%
                                </span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div>
                    </div>

                    {{-- 5. Cancelled Revenue --}}
                    <div class="col">
                        <div class="p-3">
                            <h5 class="text-muted fs-13 text-uppercase">Cancelled Revenue</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                                <h3 class="mb-0 fw-bold">{!! format_money($stats['cancelled']['current']) !!}</h3>
                            </div>
                            <p class="mb-0 text-muted">
                                {{-- For cancelled, an increase is bad (red) --}}
                                <span
                                    class="{{ $stats['cancelled']['change'] > 0 ? 'text-danger' : 'text-success' }} me-2">
                                    <i
                                        class="ti ti-caret-{{ $stats['cancelled']['change'] > 0 ? 'up' : 'down' }}-filled"></i>
                                    {{ number_format(abs($stats['cancelled']['change']), 1) }}%
                                </span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom border-light">
                        <div class="row justify-content-between g-3">
                            <div class="col-lg-7">
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <div class="position-relative">
                                            <input type="text" name="search" class="form-control ps-4 filter-control"
                                                placeholder="Search by ID, Tenant..." value="{{ request('search') }}">
                                            <i class="ti ti-search position-absolute top-50 translate-middle-y ms-2"></i>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                            <input type="text" name="date_range"
                                                class="form-control flatpickr-input filter-control"
                                                data-provider="flatpickr" data-default-date="{{ request('date_range') }}"
                                                data-date-format="Y-m-d" data-range-date="true"
                                                placeholder="Filter by issue date...">
                                            <span class="input-group-text bg-primary border-primary text-white">
                                                <i class="ti ti-calendar fs-15"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="text-lg-end">
                                    <button type="button" class="btn btn-dark d-none d-md-inline-block me-1"
                                        data-bs-toggle="modal" data-bs-target="#filtersModal">
                                        <i class="ti ti-filter me-1"></i> Filters
                                    </button>
                                    <button type="button" class="btn btn-dark d-inline-block d-md-none me-1"
                                        data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas"
                                        aria-controls="filtersOffcanvas">
                                        <i class="ti ti-filter me-1"></i> Filters
                                    </button>
                                    <a href="{{ route('landlord.payments.create') }}" class="btn btn-success"><i
                                            class="ti ti-plus me-1"></i>Add Invoice</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover text-nowrap mb-0">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th class="ps-3" style="width: 50px;">#</th>
                                    <th>Invoice ID</th>
                                    <th>Room </th>
                                    <th>Created On</th>
                                    <th>Invoice To</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width: 120px;">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($invoices as $invoice)
                                    <tr>
                                        <td class="ps-3">
                                            <strong>{{ ($invoices->currentPage() - 1) * $invoices->perPage() + $loop->iteration }}</strong>
                                        </td>
                                        <td>
                                            <a href="{{ route('landlord.payments.show', $invoice->id) }}"
                                                class="text-muted fw-semibold">{{ $invoice->invoice_number }}</a>
                                        </td>
                                        <td>
                                            Room {{ $invoice->contract->room->room_number }}
                                        </td>
                                        <td>
                                            <span
                                                class="fs-15 text-muted">{{ $invoice->issue_date->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-sm d-flex justify-content-left align-items-left ">
                                                    <img src="{{ asset($invoice->contract->tenant->image) ?? asset('assets/images/default_image.png') }}"
                                                        alt="User" class="rounded"
                                                        style="width: 100%; height: 100%; object-fit: cover;" />
                                                </div>
                                                <h6 class="fs-14 mb-0">{{ $invoice->contract->tenant->name }}</h6>
                                            </div>
                                        </td>
                                        <td>
                                            {!! format_money($invoice->total_amount) !!}
                                        </td>
                                        <td>
                                            <span
                                                class="fs-15 text-muted">{{ $invoice->due_date->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            @switch($invoice->status)
                                                @case('paid')
                                                    <span class="badge bg-success-subtle text-success fs-12 p-1">Paid</span>
                                                @break

                                                @case('partial')
                                                    <span class="badge bg-primary-subtle text-primary fs-12 p-1">Partial</span>
                                                @break

                                                @case('overdue')
                                                    <span class="badge bg-warning-subtle text-warning fs-12 p-1">Overdue</span>
                                                @break

                                                @case('void')
                                                    <span class="badge bg-danger-subtle text-danger fs-12 p-1">Void</span>
                                                @break

                                                @case('sent')
                                                    <span class="badge bg-info-subtle text-info fs-12 p-1">Sent</span>
                                                @break

                                                @case('draft')
                                                    <span class="badge bg-secondary-subtle text-secondary fs-12 p-1">Draft</span>
                                                @break

                                                @default
                                                    <span
                                                        class="badge bg-light text-dark fs-12 p-1">{{ ucfirst($invoice->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td class="pe-3">
                                            <div class="hstack gap-1 justify-content-end">
                                                <a href="{{ route('landlord.payments.show', $invoice->id) }}"
                                                    class="btn btn-soft-primary btn-icon btn-sm rounded-circle"
                                                    title="View Invoice">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-soft-info btn-icon btn-sm rounded-circle print-invoice-btn"
                                                    title="Print Invoice"
                                                    data-invoice-id="{{ $invoice->id }}"
                                                    data-invoice-number="{{ $invoice->invoice_number }}"
                                                    data-issue-date="{{ $invoice->issue_date->format('d M Y') }}"
                                                    data-due-date="{{ $invoice->due_date->format('d M Y') }}"
                                                    data-tenant-name="{{ $invoice->contract->tenant->name }}"
                                                    data-room-number="{{ $invoice->contract->room->room_number }}"
                                                    data-total-amount="{{ number_format($invoice->total_amount, 2) }}">
                                                    <i class="ti ti-printer"></i>
                                                </button>
                                                {{-- <a href=""
                                                    class="btn btn-soft-success btn-icon btn-sm rounded-circle"><i
                                                        class="ti ti-edit fs-16"></i></a> --}}
                                                {{-- <form action="" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-soft-danger btn-icon btn-sm rounded-circle"><i
                                                            class="ti ti-trash"></i></button>
                                                </form> --}}
                                            </div>

                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">No invoices found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table><!-- end table -->
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                {{-- This single line renders your custom pagination view --}}
                                {{ $invoices->links('vendor.pagination.custom-pagination') }}
                            </div>
                        </div>
                    </div> <!-- end card-->
                </div> <!-- end col -->
            </div>

        </div> <!-- container -->


        <div class="modal fade" id="filtersModal" tabindex="-1" aria-labelledby="filtersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filtersModalLabel">Search Filters</h5><button type="button"
                            class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('backends.dashboard.payments.partials._filters_content')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light clear-filters-btn">Clear Filters</button>
                        <button type="button" class="btn btn-primary apply-filters-btn" data-bs-dismiss="modal">Apply
                            Filters</button>
                    </div>

                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-bottom" tabindex="-1" id="filtersOffcanvas"
            aria-labelledby="filtersOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="filtersOffcanvasLabel">Search Filters</h5><button type="button"
                    class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                @include('backends.dashboard.payments.partials._filters_content')
            </div>
            <div class="offcanvas-footer p-3 border-top">
                <button type="button" class="btn btn-light w-100 mb-2 clear-filters-btn">Clear Filters</button>
                <button type="button" class="btn btn-primary w-100 apply-filters-btn" data-bs-dismiss="offcanvas">Apply
                    Filters</button>
            </div>
        </div>

    @endsection

    @push('script')
        @include('backends.dashboard.payments.partials.format-money-js')
        <script src="{{ asset('assets/js/flatpickr.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const invoiceTableBody = document.querySelector('.table tbody');
                const paginationContainer = document.querySelector('.card-footer .d-flex');

                /**
                 * Debounce function to delay execution
                 */
                const debounce = (func, delay) => {
                    let timeoutId;
                    return function(...args) {
                        clearTimeout(timeoutId);
                        timeoutId = setTimeout(() => func.apply(this, args), delay);
                    };
                };

                /**
                 * Helper to get status badge classes based on status text
                 */
                const getStatusBadge = (status) => {
                    const sanitizedStatus = status.toLowerCase();
                    switch (sanitizedStatus) {
                        case 'paid':
                            return 'bg-success-subtle text-success';
                        case 'partial': // New
                            return 'bg-primary-subtle text-primary';
                        case 'overdue':
                            return 'bg-warning-subtle text-warning';
                        case 'void':
                            return 'bg-danger-subtle text-danger';
                        case 'sent': // New
                            return 'bg-info-subtle text-info';
                        case 'draft': // New
                            return 'bg-secondary-subtle text-secondary';
                        default:
                            return 'bg-light text-dark'; // A more neutral default
                    }
                };

                /**
                 * Main function to fetch data and update the table via AJAX
                 */
                async function applyFilters(page = 1) {
                    const params = new URLSearchParams();

                    // 1. Get search value
                    const searchInput = document.querySelector('input[name="search"]');
                    if (searchInput && searchInput.value) {
                        params.set('search', searchInput.value);
                    }

                    // 2. Get date range value
                    const dateRangeInput = document.querySelector('input[name="date_range"]');
                    if (dateRangeInput && dateRangeInput.value) {
                        params.set('date_range', dateRangeInput.value);
                    }

                    // 3. Get values from filter links (Property, Room Type, Status)
                    document.querySelectorAll('.filter-option-list').forEach(list => {
                        const filterGroup = list.dataset.filterGroup;
                        const activeOption = list.querySelector('.filter-option.active');
                        // Ensure 'any-status' or empty values are not added to the URL
                        if (activeOption && activeOption.dataset.value && !['any-status', ''].includes(
                                activeOption.dataset.value)) {
                            params.set(filterGroup, activeOption.dataset.value);
                        }
                    });

                    // 4. Add page number for pagination
                    params.set('page', page);

                    const url = `{{ url()->current() }}?${params.toString()}`;
                    window.history.pushState({
                        path: url
                    }, '', url);

                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });

                        if (!response.ok) throw new Error('Network response was not ok');

                        const data = await response.json();
                        updateTable(data);

                    } catch (error) {
                        console.error('Error fetching invoices:', error);
                        invoiceTableBody.innerHTML =
                            `<tr><td colspan="9" class="text-center text-danger py-4">Failed to load data. Please try again.</td></tr>`;
                    }
                }

                /**
                 * Function to redraw the table and pagination with new data
                 */
                function updateTable(data) {
                    invoiceTableBody.innerHTML = '';
                    if (!data.invoices || data.invoices.data.length === 0) {
                        invoiceTableBody.innerHTML =
                            `<tr><td colspan="9" class="text-center text-muted py-4">No invoices found.</td></tr>`;
                    } else {
                        data.invoices.data.forEach(invoice => {
                            const issueDate = new Date(invoice.issue_date).toLocaleDateString('en-GB', {
                                day: 'numeric',
                                month: 'short',
                                year: 'numeric'
                            });
                            const dueDate = new Date(invoice.due_date).toLocaleDateString('en-GB', {
                                day: 'numeric',
                                month: 'short',
                                year: 'numeric'
                            });
                            const statusClass = getStatusBadge(invoice.status);
                            const tenantName = invoice.contract.tenant ? invoice.contract.tenant.name : 'N/A';
                            const roomNumber = invoice.contract.room ? invoice.contract.room.room_number :
                                'N/A';
                            const statusText = invoice.status.charAt(0).toUpperCase() + invoice.status.slice(1);

                            // Basic view/edit/delete URLs (update these to your actual routes)
                            const viewUrl = `{{ url('landlord/payments') }}/${invoice.id}`;
                            const editUrl = `{{ url('landlord/payments') }}/${invoice.id}/edit`;
                            const deleteUrl = `{{ url('landlord/payments') }}/${invoice.id}`;


                            const row = `
                            <tr>
                                <td class="ps-3"><input type="checkbox" class="form-check-input" id="customCheck${invoice.id}"></td>
                                <td><a href="${viewUrl}" class="text-muted fw-semibold">${invoice.invoice_number}</a></td>
                                <td>Room ${roomNumber}</td>
                                <td><span class="fs-15 text-muted">${issueDate}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="fs-14 mb-0">${tenantName}</h6>
                                    </div>
                                </td>
                                <td>$${parseFloat(invoice.total_amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                <td><span class="fs-15 text-muted">${dueDate}</span></td>
                                <td><span class="badge ${statusClass} fs-12 p-1">${statusText}</span></td>
                                <td class="pe-3">
                                    <div class="hstack gap-1 justify-content-end">
                                        <a href="${viewUrl}" class="btn btn-soft-primary btn-icon btn-sm rounded-circle" title="View Invoice"><i class="ti ti-eye"></i></a>
                                        <button type="button" class="btn btn-soft-info btn-icon btn-sm rounded-circle print-invoice-btn" 
                                            title="Print Invoice"
                                            data-invoice-id="${invoice.id}"
                                            data-invoice-number="${invoice.invoice_number}"
                                            data-issue-date="${issueDate}"
                                            data-due-date="${dueDate}"
                                            data-tenant-name="${tenantName}"
                                            data-room-number="${roomNumber}"
                                            data-total-amount="${parseFloat(invoice.total_amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}">
                                            <i class="ti ti-printer"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;

                            invoiceTableBody.insertAdjacentHTML('beforeend', row);
                        });
                    }
                    paginationContainer.innerHTML = data.pagination;
                }

                // --- ALL EVENT LISTENERS ---

                // Listener for live search
                document.querySelector('input[name="search"]').addEventListener('input', debounce(() => applyFilters(),
                    400));

                // Listener for Flatpickr
                flatpickr('input[name="date_range"]', {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    onChange: function(selectedDates) {
                        if (selectedDates.length === 2) {
                            // We need to set the value manually for applyFilters to pick it up
                            this.input.value = this.formatDate(selectedDates[0], "Y-m-d") + ' to ' + this
                                .formatDate(selectedDates[1], "Y-m-d");
                            applyFilters();
                        }
                    }
                });

                // Listener for pagination links (delegated to the container)
                paginationContainer.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A' && e.target.closest('.pagination')) {
                        e.preventDefault();
                        const url = new URL(e.target.href);
                        const page = url.searchParams.get('page');
                        applyFilters(page);
                    }
                });


                // Listener for the modal/offcanvas "Apply Filters" button
                document.querySelectorAll('.apply-filters-btn').forEach(button => {
                    button.addEventListener('click', () => applyFilters());
                });

                // Listener for filter links
                document.querySelectorAll('.filter-option').forEach(option => {
                    option.addEventListener('click', function(e) {
                        e.preventDefault();
                        // Deselect siblings
                        this.closest('.filter-option-list').querySelectorAll('.filter-option').forEach(
                            opt => opt.classList.remove('active'));
                        // Select clicked option
                        this.classList.add('active');
                        // No need to call applyFilters here; user will click the main button.
                    });
                });

                // Listener for the "Clear Filters" button
                document.querySelectorAll('.clear-filters-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        document.querySelector('input[name="search"]').value = '';
                        if (document.querySelector('input[name="date_range"]')._flatpickr) {
                            document.querySelector('input[name="date_range"]')._flatpickr.clear();
                        }

                        document.querySelectorAll('.filter-option.active').forEach(opt => opt.classList
                            .remove('active'));
                        document.querySelectorAll('.filter-option-list').forEach(list => {
                            // Set the "All/Any" options to active
                            list.querySelector('.filter-option:first-child').classList.add(
                                'active');
                        });

                        applyFilters(); // Fetch all results again
                    });
                });

                // Listener for the print invoice button (delegated event handler for dynamically added buttons)
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.print-invoice-btn')) {
                        const btn = e.target.closest('.print-invoice-btn');
                        printInvoice(btn);
                    }
                });

                /**
                 * Function to print an invoice
                 */
                function printInvoice(button) {
                    // Extract invoice ID from button attributes
                    const invoiceId = button.getAttribute('data-invoice-id');
                    
                    // Show loading indicator
                    Swal.fire({
                        title: 'Generating Invoice',
                        text: 'Please wait...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Fetch invoice details using our new endpoint
                    fetch(`{{ route('landlord.payments.getInvoiceDetails', ['invoice' => ':invoiceId']) }}`.replace(':invoiceId', invoiceId), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Close loading indicator
                        Swal.close();
                        
                        // Extract data from the response
                        const invoice = data.invoice;
                        const tenant = data.tenant;
                        const property = data.property;
                        const room = data.room;
                        const lineItems = data.line_items;
                        
                        // Generate and print the invoice
                        generateInvoicePrint(
                            invoice.invoice_number,
                            invoice.issue_date,
                            invoice.due_date,
                            tenant.name,
                            room.room_number,
                            invoice.total_amount,
                            lineItems
                        );
                    })
                    .catch(error => {
                        // Close loading indicator and show error
                        Swal.close();
                        console.error('Error fetching invoice details:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not fetch invoice details. Please try again.'
                        });
                    });
                }

                /**
                 * Generate and print the invoice
                 */
                async function generateInvoicePrint(invoiceNumber, issueDate, dueDate, tenantName, roomNumber, totalAmount, lineItems) {
                    // Image assets
                    const logoUrl = "{{ asset('assets/images/logo-dark.png') }}";
                    
                    // Check QR codes availability
                    const hasQrCode1 = {{ Auth::user()->qr_code_1 ? 'true' : 'false' }};
                    const hasQrCode2 = {{ Auth::user()->qr_code_2 ? 'true' : 'false' }};
                    const qrCode1Url = hasQrCode1 ? "{{ Auth::user()->qr_code_1 ? asset('uploads/qrcodes/' . Auth::user()->qr_code_1) : '' }}" : "";
                    const qrCode2Url = hasQrCode2 ? "{{ Auth::user()->qr_code_2 ? asset('uploads/qrcodes/' . Auth::user()->qr_code_2) : '' }}" : "";

                    // Format dates
                    const formattedIssueDate = new Date(issueDate).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                    const formattedDueDate = new Date(dueDate).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                    
                    // Get the currency symbol from user preferences
                    const currencySymbol = '{{ auth()->user()->currency_code ?: "$" }}';
                    
                    // Generate items HTML
                    let itemsHtml = '';
                    let subtotal = 0;
                    let discount = 0;
                    let amountsToFormat = [];
                    
                    if (lineItems && lineItems.length > 0) {
                        lineItems.forEach((item, index) => {
                            const itemAmount = parseFloat(item.amount);
                            
                            // Check if it's a discount line item
                            if (item.description.toLowerCase().includes('discount')) {
                                discount += Math.abs(itemAmount);
                                return; // Skip this iteration
                            }
                            
                            subtotal += itemAmount;
                            amountsToFormat.push(itemAmount); // Add amount to our formatting array
                            
                            let detailHtml = item.description;
                            
                            // Check for additional details from lineable
                            let subDescription = '';
                            if (item.lineable && item.lineable.consumption) {
                                const rateApplied = parseFloat(item.lineable.rate_applied);
                                amountsToFormat.push(rateApplied); // Add rate to our formatting array
                                subDescription = `Consumption: ${item.lineable.consumption} units × Rate: [RATE_${amountsToFormat.length-1}]/unit`;
                            } else if (item.lineable && item.lineable.amount) {
                                const lineableAmount = parseFloat(item.lineable.amount);
                                amountsToFormat.push(lineableAmount); // Add amount to our formatting array
                                subDescription = `Amount: [AMOUNT_${amountsToFormat.length-1}]`;
                            }
                            
                            if (subDescription) {
                                detailHtml = `<h6 class="mb-0">${item.description}</h6>
                                             <p class="text-muted mb-0 small">${subDescription}</p>`;
                            }
                            
                            itemsHtml += `
                            <tr>
                                <th scope="row">${String(index + 1).padStart(2, '0')}</th>
                                <td class="text-start">${detailHtml}</td>
                                <td>1</td>
                                <td>[AMOUNT_${index}]</td>
                                <td class="text-end">[AMOUNT_${index}]</td>
                            </tr>`;
                        });
                    } else {
                        // If no items, just show the total as one item
                        subtotal = parseFloat(totalAmount);
                        amountsToFormat.push(subtotal);
                        
                        itemsHtml = `
                        <tr>
                            <th scope="row">01</th>
                            <td class="text-start">Invoice Total</td>
                            <td>1</td>
                            <td>[AMOUNT_0]</td>
                            <td class="text-end">[AMOUNT_0]</td>
                        </tr>`;
                    }
                    
                    // Add subtotal, discount and total to formatting array
                    amountsToFormat.push(subtotal);
                    amountsToFormat.push(discount);
                    amountsToFormat.push(subtotal - discount);
                    
                    // Get formatted amounts from the server
                    try {
                        const formattedAmounts = await formatMoneyBatch(amountsToFormat);
                        
                        // Replace placeholders with formatted amounts
                        let updatedItemsHtml = itemsHtml;
                        
                        // Replace all [AMOUNT_x] placeholders
                        for (let i = 0; i < lineItems.length; i++) {
                            const regex = new RegExp(`\\[AMOUNT_${i}\\]`, 'g');
                            updatedItemsHtml = updatedItemsHtml.replace(regex, formattedAmounts[i]);
                        }
                        
                        // Replace all [RATE_x] and [AMOUNT_x] in the descriptions
                        for (let i = lineItems.length; i < formattedAmounts.length - 3; i++) {
                            const rateRegex = new RegExp(`\\[RATE_${i}\\]`, 'g');
                            const amountRegex = new RegExp(`\\[AMOUNT_${i}\\]`, 'g');
                            updatedItemsHtml = updatedItemsHtml.replace(rateRegex, formattedAmounts[i]);
                            updatedItemsHtml = updatedItemsHtml.replace(amountRegex, formattedAmounts[i]);
                        }
                        
                        const formattedSubtotal = formattedAmounts[formattedAmounts.length - 3];
                        const formattedDiscount = formattedAmounts[formattedAmounts.length - 2];
                        const formattedTotal = formattedAmounts[formattedAmounts.length - 1];
                        
                        generateInvoiceHTML(updatedItemsHtml, formattedSubtotal, formattedDiscount, formattedTotal);
                        
                    } catch (error) {
                        console.error('Error formatting amounts:', error);
                        
                        // Fallback to client-side formatting
                        itemsHtml = itemsHtml.replace(/\[AMOUNT_\d+\]/g, (match) => {
                            const index = parseInt(match.match(/\d+/)[0]);
                            return `${currencySymbol}${amountsToFormat[index].toFixed(2)}`;
                        });
                        
                        itemsHtml = itemsHtml.replace(/\[RATE_\d+\]/g, (match) => {
                            const index = parseInt(match.match(/\d+/)[0]);
                            return `${currencySymbol}${amountsToFormat[index].toFixed(2)}`;
                        });
                        
                        const subtotalText = `${currencySymbol}${subtotal.toFixed(2)}`;
                        const discountText = `-${currencySymbol}${discount.toFixed(2)}`;
                        const totalText = `${currencySymbol}${(subtotal - discount).toFixed(2)}`;
                        
                        generateInvoiceHTML(itemsHtml, subtotalText, discountText, totalText);
                    }
                    
                    function generateInvoiceHTML(itemsHtml, subtotalText, discountText, totalText) {
                        // Create the invoice HTML
                        const invoiceHtml = `
                        <!DOCTYPE html>
                        <html lang="en">
                        <head>
                            <meta charset="UTF-8">
                            <title>Invoice #${invoiceNumber}</title>
                            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                            <style>
                                body { font-family: sans-serif; -webkit-print-color-adjust: exact; background-color: #fff !important; }
                                .invoice-items-table .totals-row td, .invoice-items-table .final-total-row th, .invoice-items-table .final-total-row td { border: none; padding-top: 0.5rem; padding-bottom: 0.5rem; }
                                .invoice-items-table .totals-row td:nth-last-child(-n+2) { border-top: 1px solid #e9ecef; }
                                .invoice-items-table .final-total-row th, .invoice-items-table .final-total-row td { border-top: 2px solid #212529; border-bottom: 2px solid #212529; }
                                @media print {
                                    .container { max-width: 100%; }
                                    .table th, .table td { padding: 0.5rem; }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="container py-4">
                                <div class="row align-items-center mb-4"><div class="col-6"><img src="${logoUrl}" height="60" alt="Logo"></div><div class="col-6 text-end"><h6>Invoice #${invoiceNumber}</h6></div></div><hr>
                                <div class="row mb-4"><div class="col-6"><p class="fw-bold">Bill To:</p><p>${tenantName}<br>Room ${roomNumber}<br>Phnom Penh</p></div><div class="col-6 text-end"><p><span class="fw-bold">Invoice Date:</span> ${formattedIssueDate}</p><p><span class="fw-bold">Due Date:</span> ${formattedDueDate}</p></div></div>
                                <table class="table invoice-items-table text-center table-nowrap align-middle mb-0">
                                    <thead class="bg-light bg-opacity-50"><tr><th style="width:50px;">#</th><th class="text-start">Item Details</th><th>Quantity</th><th>Unit Price</th><th class="text-end">Amount</th></tr></thead>
                                    <tbody>
                                        ${itemsHtml}
                                        <tr class="totals-row"><td colspan="3"></td><td class="text-end">Subtotal</td><td class="text-end">${subtotalText}</td></tr>
                                        <tr class="totals-row"><td colspan="3"></td><td class="text-end">Discount</td><td class="text-end">-${discountText}</td></tr>
                                        <tr class="final-total-row fs-4 fw-bold"><th colspan="3"></th><th class="text-end">Total Amount</th><th class="text-end">${totalText}</th></tr>
                                    </tbody>
                                </table>
                                <div class="text-center mt-5">
                                    ${hasQrCode1 ? `<img src="${qrCode1Url}" height="100" class="mx-2" alt="QR Code 1">` : ''}
                                    ${hasQrCode2 ? `<img src="${qrCode2Url}" height="100" class="mx-2" alt="QR Code 2">` : ''}
                                    ${(!hasQrCode1 && !hasQrCode2) ? '<p class="text-muted">No payment QR codes available</p>' : ''}
                                </div>
                            </div>
                        </body>
                        </html>`;
                        
                        // Create iframe to print
                        const iframe = document.createElement('iframe');
                        iframe.style.display = 'none';
                        document.body.appendChild(iframe);
                        iframe.contentDocument.write(invoiceHtml);
                        iframe.contentDocument.close();
                        
                        iframe.onload = function() {
                            setTimeout(function() {
                                iframe.contentWindow.print();
                                // Remove iframe after printing or after a timeout
                                setTimeout(function() {
                                    document.body.removeChild(iframe);
                                }, 1000);
                            }, 500);
                        };
                    }
                }
            });
        </script>
    @endpush
