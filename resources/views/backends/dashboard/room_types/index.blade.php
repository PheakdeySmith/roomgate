@extends('backends.layouts.app')

@section('title', 'Room Types | RoomGate')

@push('style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/mermaid.min.css">
    <link href="{{ asset('assets') }}/css/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.core.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.snow.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/classic.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/monolith.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/nano.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="page-container">
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Room Types Tables</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Boron</a></li>
                    <li class="breadcrumb-item active">Room Types Tables</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom border-dashed">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <h4 class="header-title">Users Data</h4>
                            @if (Auth::check() && Auth::user()->hasRole('landlord'))
                                <a class="btn btn-primary" data-bs-toggle="modal" href="#createModal" role="button">
                                    <i class="ti ti-plus me-1"></i>Add Type
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

    @if (Auth::check() && Auth::user()->hasRole('landlord'))
        @include('backends.dashboard.room_types.create')
        @include('backends.dashboard.room_types.edit')
    @endif
@endsection

@push('script')
    <script src="{{ asset('assets') }}/js/gridjs.umd.js"></script>
    <script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>
    <script src="{{ asset('assets') }}/js/select2.min.js"></script>
    <script src="{{ asset('assets') }}/js/dropzone-min.js"></script>
    <script src="{{ asset('assets') }}/js/quill.min.js"></script>
    <script src="{{ asset('assets') }}/js/pickr.min.js"></script>
    <script src="{{ asset('assets') }}/js/ecommerce-add-products.js"></script>
    <script>
        const roomTypesData = {!! json_encode(
        $roomTypes->map(function ($type, $key) {
            $destroyUrl = route('landlord.room_types.destroy', $type->id);
            $editUrl = route('landlord.room_types.update', $type->id);
            $viewUrl = route('landlord.room_types.show', $type->id);
            return [
                $key + 1,
                $type->name ?? 'N/A',
                $type->description ?? 'N/A',
                $type->capacity ?? 'N/A',
                $type->status ?? 'N/A',
                (object) [
                    'destroy_url' => $destroyUrl,
                    'edit_url' => $editUrl,
                    'view_url' => $viewUrl,
                    'id' => $type->id,
                    'name' => $type->name,
                    'description' => $type->description,
                    'capacity' => $type->capacity,
                    'status' => $type->status,
                    'amenities' => $type->amenities->pluck('id'),
                ],
            ];
        })->values()->all(),
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE,
    ) !!};

        const clearAndRenderGrid = (containerId, gridConfig) => {
            const container = document.getElementById(containerId);
            if (container) {
                container.innerHTML = "";
                new gridjs.Grid(gridConfig).render(container);
            }
        };

        clearAndRenderGrid("table-gridjs", {
            columns: [{
                name: "#",
                width: "50px"
            },
            {
                name: "Name",
                width: "150px"
            },
            {
                name: "Description",
                width: "200px"
            },
            {
                name: "Capacity",
                width: "120px"
            },
            {
                name: "Status",
                width: "100px",
                formatter: (_, row) => {
                    const status = row.cells[4].data;
                    return gridjs.html(
                        `<span class="badge badge-soft-${status === 'active' ? 'success' : 'danger'}">${status}</span>`
                    );
                }
            },
            {
                name: "Action",
                width: "150px",
                sort: false,
                formatter: (_, row) => {
                    const actionData = row._cells[5].data;


                    const destroyUrl = actionData?.destroy_url;
                    const editUrl = actionData?.edit_url;
                    const viewUrl = actionData?.view_url;

                    const id = row.cells[0].data;
                    const name = row.cells[1].data;
                    const description = row.cells[2].data;
                    const capacity = row.cells[3].data;
                    const status = row.cells[4].data;

                    let deleteButtonHtml = destroyUrl ?
                        `<button 
                                            data-type-id="${id}" 
                                            data-type-name="${name}" 
                                            data-action-url="${actionData.destroy_url}" 
                                            type="button" 
                                            class="btn btn-soft-danger btn-icon btn-sm rounded-circle delete-type" 
                                            title="Delete"><i class="ti ti-trash"></i></button>` :
                        '';

                    let editButtonHtml = '';
                    if (editUrl) {

                        const amenitiesString = (actionData.amenities || []).join(',');

                        editButtonHtml =
                            `<button 
                                class="btn btn-soft-success btn-icon btn-sm rounded-circle edit-type-btn" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal" 
                                data-id="${actionData.id}" 
                                data-name="${actionData.name}" 
                                data-description="${actionData.description || ''}" 
                                data-capacity="${actionData.capacity}" 
                                data-status="${actionData.status}" 
                                data-update-url="${actionData.edit_url}"
                                data-amenities="${amenitiesString}"
                                role="button" 
                                title="Edit"><i class="ti ti-edit fs-16"></i>
                            </button>`;
                    }

                    return gridjs.html(`
                                            <div class="hstack gap-1 justify-content-end">
                                                <a href="${viewUrl}" class="btn btn-soft-primary btn-icon btn-sm rounded-circle" title="View type"><i class="ti ti-eye"></i></a>
                                                ${editButtonHtml}
                                                ${deleteButtonHtml}
                                            </div>`);
                }
            }
            ],
            pagination: {
                limit: 10,
                summary: true
            },
            sort: true,
            search: true,
            data: roomTypesData,
            style: {
                table: {
                    'font-size': '0.85rem'
                }
            }
        });

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
                    text: `Room Type "${typeName}" will be permanently deleted! This action cannot be undone.`,
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
            $('#status').select2({
                dropdownParent: $('#createModal, #editModal'),
                placeholder: "Select status",
                allowClear: true
            });
        });

        $('body').on('click', '.edit-type-btn', function () {
            const button = $(this);
            const modal = $('#editModal');

            const updateUrl = button.data('update-url');
            const name = button.data('name');
            const description = button.data('description') || '';
            const capacity = button.data('capacity');
            const status = button.data('status');

            const amenitiesString = button.data('amenities').toString();
            const associatedAmenityIds = amenitiesString ? amenitiesString.split(',') : [];

            modal.find('#editRoomTypeForm').attr('action', updateUrl);
            modal.find('#editName').val(name);
            modal.find('#editDescription').val(description);
            modal.find('#editCapacity').val(capacity);
            modal.find('#editStatus').val(status);

            modal.find('input[name="amenities[]"]').prop('checked', false);

            if (associatedAmenityIds.length > 0) {
                associatedAmenityIds.forEach(function (amenityId) {
                    modal.find(`input[name="amenities[]"][value="${amenityId}"]`).prop('checked', true);
                });
            }
        });

    </script>
@endpush