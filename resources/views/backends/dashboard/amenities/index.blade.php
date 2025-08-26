@extends('backends.layouts.app')

@section('title', 'Amenities | RoomGate')

@push('style')
    {{-- Styles remain the same --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/mermaid.min.css">
    <link href="{{ asset('assets') }}/css/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.core.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.snow.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="page-container">
        {{-- Page Title --}}
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">{{ __('messages.table') }} {{ __('messages.amenities') }}</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('messages.amenities') }}</li>
                </ol>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom border-dashed">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <h4 class="header-title">{{ __('messages.amenities') }}</h4>
                            @if (Auth::check() && Auth::user()->hasRole('landlord'))
                                <a class="btn btn-primary" data-bs-toggle="modal" href="#createModal" role="button">
                                    <i class="ti ti-plus me-1"></i>{{ __('messages.create') }} {{ __('messages.amenities') }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="table-gridjs"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @if (Auth::check() && Auth::user()->hasRole('landlord'))
        @include('backends.dashboard.amenities.create')
        @include('backends.dashboard.amenities.edit')
    @endif
@endsection

@push('script')
    {{-- Scripts remain the same --}}
    <script src="{{ asset('assets') }}/js/gridjs.umd.js"></script>
    <script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>
    <script src="{{ asset('assets') }}/js/select2.min.js"></script>
    <script src="{{ asset('js') }}/currency-format.js"></script>

    <script>
        // Set current currency from PHP settings
        @if(Auth::check() && Auth::user()->currency_code)
            setCurrentCurrency('{{ Auth::user()->currency_code }}', {{ Auth::user()->exchange_rate ?? 1 }});
        @endif
        // 1. DATA MAPPING: Changed to map amenities data
        const amenitiesData = {!! json_encode(
        $amenities->map(function ($amenity, $key) {
            $amenityDataForJs = [
                'id' => $amenity->id,
                'name' => $amenity->name ?? 'N/A',
                'description' => $amenity->description ?? 'N/A',
                'amenity_price' => $amenity->amenity_price,
                'status' => $amenity->status ?? 'N/A',
                'destroy_url' => route('landlord.amenities.destroy', $amenity->id),
                'edit_url' => route('landlord.amenities.update', $amenity->id),
                'view_url' => route('landlord.amenities.show', $amenity->id),
            ];

            return [
                $key + 1,
                $amenityDataForJs['name'],
                $amenityDataForJs['description'],
                $amenityDataForJs['amenity_price'],
                $amenityDataForJs['status'],
                $amenityDataForJs,
            ];
        })->values()->all(),
    ) !!};

        // 2. GRIDJS SETUP: Columns updated for amenities
        new gridjs.Grid({
            columns: [
                { name: "#", width: "50px" },
                { name: "{{ __('messages.name') }}", width: "200px" },
                { name: "{{ __('messages.description') }}", width: "150px" },
                { 
                    name: "{{ __('messages.price') }}", 
                    width: "150px",
                    formatter: (cell) => {
                        return gridjs.html(formatMoney(cell));
                    }
                },
                { 
                    name: "Status",
                    width: "120px",
                    formatter: (cell) => {
                        return gridjs.html(
                            `<span class="badge badge-soft-${cell === 'active' ? 'success' : 'danger'}">${cell}</span>`
                        );
                    } 
                },
                {
                    name: "{{ __('messages.action') }}",
                    width: "150px",
                    sort: false,
                    formatter: (_, row) => {
                        const actionData = row._cells[5].data;

                        const destroyUrl = actionData?.destroy_url;
                        const editUrl = actionData?.edit_url;
                        const typeViewUrl = actionData?.type_view_url;

                        const id = row.cells[0].data;
                        const name = row.cells[1].data;
                        const description = row.cells[2].data;
                        const amenity_price = row.cells[3].data;
                        const status = row.cells[4].data;

                        let deleteButtonHtml = destroyUrl ?
                            `<button 
                                data-type-id="${id}" 
                                data-type-name="${name}" 
                                data-action-url="${actionData?.destroy_url}" 
                                type="button" 
                                class="btn btn-soft-danger btn-icon btn-sm rounded-circle delete-type" 
                                title="Delete"><i class="ti ti-trash"></i></button>` : '';

                        let editButtonHtml = editUrl ?
                            `<button 
                                class="btn btn-soft-success btn-icon btn-sm rounded-circle edit-type-btn" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal" 
                                data-id="${id}" 
                                data-name="${name}" 
                                data-description="${description}" 
                                data-amenity_price="${amenity_price}" 
                                data-status="${status}" 
                                data-edit-url="${actionData?.edit_url}" 
                                role="button" 
                                title="Edit"><i class="ti ti-edit fs-16"></i></button>` : '';
                            '';

                        return gridjs.html(`
                            <div class="hstack gap-1 justify-content-end">
                                ${editButtonHtml}
                                ${deleteButtonHtml}
                            </div>`);
                    }
                }
            ],
            pagination: { limit: 10, summary: true },
            sort: true,
            search: true,
            data: amenitiesData,
            style: {
                table: {
                    'font-size': '0.85rem'
                }
            }
        }).render(document.getElementById("table-gridjs"));

        document.addEventListener('click', function (e) {
            if (e.target.closest('.delete-type')) {
                const button = e.target.closest('.delete-type');
                const typeId = button.getAttribute('data-type-id');
                const typeName = button.getAttribute('data-type-name') || 'this type';
                const actionUrl = button.getAttribute('data-action-url');

                if (!actionUrl) {
                    console.error('Delete action URL not found on the button.');
                    Swal.fire('Error!', 'Cannot proceed with deletion. Action URL is missing.', 'error');
                    return;
                }

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfMeta) {
                    console.error('CSRF token meta tag not found.');
                    Swal.fire('Error!', 'Cannot proceed: CSRF token not found.', 'error');
                    return;
                }
                const csrfToken = csrfMeta.getAttribute('content');

                Swal.fire({
                    title: "Are you sure?",
                    text: `Amenity "${typeName}" will be permanently deleted! This action cannot be undone.`,
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

                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = csrfToken;

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';

                        form.appendChild(tokenInput);
                        form.appendChild(methodInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        });

        $(function () {
            // Initialize createModal select2
            $('#status').select2({
                dropdownParent: $('#createModal'),
                placeholder: "Select status",
                allowClear: true
            });
            
            // Initialize editModal select2 separately
            $('#editStatus').select2({
                dropdownParent: $('#editModal'),
                placeholder: "Select status",
                allowClear: true
            });
        });

        $('body').on('click', '.edit-type-btn', function () {
            const button = $(this);
            const modal = $('#editModal');

            const id = button.data('id');
            const actionUrl = button.data('edit-url');
            const name = button.data('name');
            const description = button.data('description');
            const amenity_price = button.data('amenity_price');
            const status = button.data('status');

                // Populate the modal form with the correct data
                // NOTE: Ensure your edit modal form has these input IDs
            modal.find('#editRoomTypeId').val(id); // A hidden input for the ID
            modal.find('#editName').val(name);
            modal.find('#editDescription').val(description);
            modal.find('#editAmenityPrice').val(amenity_price);
            modal.find('#editStatus').val(status).trigger('change');

            // Set the form's action URL
            // NOTE: Ensure your edit modal's form tag has id="editRoomTypeForm"
            modal.find('#editRoomTypeForm').attr('action', actionUrl);
        });
    </script>
@endpush