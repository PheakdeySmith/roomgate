@extends('backends.layouts.app')

@section('title', 'Manage Rates for ' . $property->name)

@push('style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/mermaid.min.css">
    <link href="{{ asset('assets') }}/css/sweetalert2.min.css" rel="stylesheet" type="text/css">

    <link href="{{ asset('assets') }}/css/quill.core.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.snow.css" rel="stylesheet" type="text/css">

    {{-- Note: You have pickr themes (classic, monolith, nano) included twice. Remove duplicates. --}}
    <link href="{{ asset('assets') }}/css/classic.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/monolith.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/nano.min.css" rel="stylesheet" type="text/css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@endpush

@section('content')
    <div class="page-container">
        {{-- Page Title remains the same --}}
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Manage Utility Rates for: {{ $property->name }}</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('landlord.properties.index') }}">Properties</a></li>
                    <li class="breadcrumb-item active">Manage Rates</li>
                </ol>
            </div>
        </div>

        {{-- Main Content Card --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom border-dashed">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <h4 class="header-title">All Utility Types & Rates</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="table-rates-gridjs"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals remain the same --}}
    @include('backends.dashboard.utilities.create-rate', ['utilityTypes' => $allUtilityTypes])
    @include('backends.dashboard.utilities.edit-rate')
@endsection

@push('script')
    <script src="{{ asset('assets') }}/js/gridjs.umd.js"></script>
    <script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>
    <script src="{{ asset('assets') }}/js/select2.min.js"></script>
    <script src="{{ asset('assets') }}/js/dropzone-min.js"></script>
    <script src="{{ asset('assets') }}/js/quill.min.js"></script>
    <script src="{{ asset('assets') }}/js/pickr.min.js"></script>
    <script src="{{ asset('assets') }}/js/ecommerce-add-products.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr(".flatpickr-date", {
                dateFormat: "Y-m-d",
                defaultDate: "today" // Sets the default date for the "Add" modal
            });
            // 1. Use the new $utilityData variable from the controller
            const utilityData = {!! json_encode($utilityData->values()) !!};
            // Get user's currency settings
            const userCurrency = '{{ Auth::user()->currency_code }}';
            const userExchangeRate = {{ Auth::user()->exchange_rate ?? 1 }};

            // 2. Render Grid.js Table
            if (document.getElementById("table-rates-gridjs")) {
                new gridjs.Grid({
                    columns: [
                        { 
                            name: "Utility Type", 
                            width: "250px", 
                            formatter: (cell, row) => {
                                const item = row.cells[0].data;
                                const rate = item.rate;
                                let html = `<div><p class="mb-0 fw-medium text-dark">${item.type.name}</p>`;
                                
                                if (rate) {
                                    html += `<small class="text-muted">Effective: ${new Date(rate.effective_from).toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/(\d+) ([A-Za-z]+)/, '$1 $2,')}</small></div>`;
                                } else {
                                    html += `<small class="text-muted">Not set</small></div>`;
                                }
                                return gridjs.html(html);
                            }
                        },
                        {
                            name: "Rate", 
                            width: "250px",
                            formatter: (cell, row) => {
                                const rate = row.cells[0].data.rate;
                                if (rate) {
                                    // Get the raw rate value
                                    const rateValue = parseFloat(rate.rate);
                                    
                                    // Convert from USD to user's currency (multiply by exchange rate)
                                    const convertedRate = rateValue * userExchangeRate;
                                    
                                    // Format the number with proper decimal places
                                    const formattedAmount = new Intl.NumberFormat('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }).format(convertedRate);
                                    
                                    // Get the currency symbol based on user's currency
                                    let currencySymbol = '$'; // Default
                                    let currencyPosition = 'before'; // Default
                                    
                                    switch (userCurrency) {
                                        case 'USD': 
                                            currencySymbol = '$'; 
                                            currencyPosition = 'before';
                                            break;
                                        case 'GBP': 
                                            currencySymbol = '£'; 
                                            currencyPosition = 'before';
                                            break;
                                        case 'EUR': 
                                            currencySymbol = '€'; 
                                            currencyPosition = 'before';
                                            break;
                                        case 'KHR': 
                                            currencySymbol = '៛'; 
                                            currencyPosition = 'after';
                                            break;
                                        case 'JPY': 
                                            currencySymbol = '¥'; 
                                            currencyPosition = 'before';
                                            break;
                                        case 'CNY': 
                                            currencySymbol = '¥'; 
                                            currencyPosition = 'before';
                                            break;
                                        case 'THB': 
                                            currencySymbol = '฿'; 
                                            currencyPosition = 'before';
                                            break;
                                        case 'SGD': 
                                            currencySymbol = 'S$'; 
                                            currencyPosition = 'before';
                                            break;
                                        case 'MYR': 
                                            currencySymbol = 'RM'; 
                                            currencyPosition = 'before';
                                            break;
                                        case 'VND': 
                                            currencySymbol = '₫'; 
                                            currencyPosition = 'after';
                                            break;
                                    }
                                    
                                    // Format with proper currency symbol position
                                    let formattedCurrency = '';
                                    if (currencyPosition === 'before') {
                                        formattedCurrency = `${currencySymbol}${formattedAmount}`;
                                    } else {
                                        formattedCurrency = `${formattedAmount} ${currencySymbol}`;
                                    }
                                    
                                    return gridjs.html(`<span class="fw-semibold text-danger">${formattedCurrency} / ${row.cells[0].data.type.unit_of_measure}</span>`);
                                } else {
                                    return gridjs.html('<span class="text-muted">Not Set</span>');
                                }
                            }
                        },
                        {
                            name: "Actions",
                            width: "150px",
                            sort: false,
                            formatter: (cell, row) => {
                                const item = row.cells[0].data;
                                // If rate EXISTS, show Edit/Delete buttons
                                if (item.rate) {
                                    const editButtonHtml = `<button class="btn btn-soft-success btn-icon btn-sm rounded-circle edit-rate-btn" data-bs-toggle="modal" data-bs-target="#editRateModal" data-rate-data='${JSON.stringify(item.rate)}' data-utility-type-name="${item.type.name}" title="Edit Rate"><i class="ti ti-edit fs-16"></i></button>`;
                                    const deleteButtonHtml = `<button class="btn btn-soft-danger btn-icon btn-sm rounded-circle delete-rate-btn" data-destroy-url="{{ url('landlord/utility-rates') }}/${item.rate.id}" data-utility-type-name="${item.type.name}" title="Delete Rate"><i class="ti ti-trash"></i></button>`;
                                    return gridjs.html(`<div class="hstack gap-1 justify-content-end">${editButtonHtml}${deleteButtonHtml}</div>`);
                                }
                                // If rate is NULL, show Add button
                                else {
                                    const addButtonHtml = `<button class="btn btn-sm btn-soft-primary add-rate-btn" data-bs-toggle="modal" data-bs-target="#createRateModal" data-utility-type-id="${item.type.id}" data-utility-type-name="${item.type.name}"><i class="ti ti-plus me-1"></i> Add Rate</button>`;

                                    return gridjs.html(`<div class="d-flex justify-content-end">${addButtonHtml}</div>`);
                                }
                            }
                        }
                    ],
                    // Use a hidden column to store the raw data object for each row
                    data: utilityData.map(item => [item]),
                    search: true,
                    sort: {
                        // Multi-column sorting
                        multiColumn: false,
                        // Custom compare function to sort by Utility Type name
                        compare: (a, b) => {
                            if (a.type.name < b.type.name) return -1;
                            if (a.type.name > b.type.name) return 1;
                            return 0;
                        }
                    },
                    pagination: { limit: 10 },
                    style: { table: { 'font-size': '0.85rem' } }
                }).render(document.getElementById("table-rates-gridjs"));
            }

            document.addEventListener('click', function (e) {

                const addButton = e.target.closest('.add-rate-btn');
                if (addButton) {
                    const utilityId = addButton.dataset.utilityTypeId;
                    const utilityName = addButton.dataset.utilityTypeName;

                    if (utilityName === undefined) {
                        console.error("This should not happen now, but there's still a mismatch.");
                        return;
                    }

                    const createModal = document.getElementById('createRateModal');
                    const hiddenIdInput = createModal.querySelector('#create_utility_type_id');
                    const disabledNameInput = createModal.querySelector('#create_utility_type_name');

                    hiddenIdInput.value = utilityId;
                    disabledNameInput.value = utilityName;
                }

                // Handle EDIT button
                const editButton = e.target.closest('.edit-rate-btn');
                if (editButton) {
                    const modal = $('#editRateModal');
                    const rateData = JSON.parse(editButton.dataset.rateData);
                    const utilityTypeName = editButton.dataset.utilityTypeName;

                    modal.find('.modal-title').text(`Edit Rate for ${utilityTypeName}`);
                    modal.find('#editRate').val(rateData.rate);
                    modal.find('#editEffectiveFrom').val(new Date(rateData.effective_from).toISOString().split('T')[0]);
                    modal.find('#editRateForm').attr('action', `{{ url('landlord/utility-rates') }}/${rateData.id}`);

                    modal.find('#edit_utility_type_name').val(utilityTypeName);
                }

                // Handle Delete Button Click
                const deleteButton = e.target.closest('.delete-rate-btn');
                if (deleteButton) {
                    const utilityTypeName = deleteButton.getAttribute('data-utility-type-name');
                    const actionUrl = deleteButton.getAttribute('data-destroy-url');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: `The rate for "${utilityTypeName}" will be permanently deleted!`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "No, cancel",
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        customClass: {
                            confirmButton: "swal2-confirm btn btn-danger me-2 mt-2",
                            cancelButton: "swal2-cancel btn btn-secondary mt-2",
                        },
                        buttonsStyling: false,
                        showCloseButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = actionUrl;
                            form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}"><input type="hidden" name="_method" value="DELETE">`;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }
            });

            // Initialize Select2 for modals
            $(function () {
                // DELETE THIS LINE
                $('#createRateModal .select2').select2({ dropdownParent: $('#createRateModal') });
            });
        });
    </script>
@endpush