@extends('backends.layouts.app')

@section('title', 'Rooms | RoomGate')


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
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Rooms Table</h4>
            </div>
            <div class="text-end">
                @if (Auth::check() && Auth::user()->hasRole('landlord'))
                    <a class="btn btn-primary" data-bs-toggle="modal" href="#createModal" role="button">
                        <i class="ti ti-plus me-1"></i>Add Room
                    </a>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body p-0">

                <div
                    class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-0 text-center align-items-center justify-content-center">

                    <!-- Total Rooms -->
                    <div class="col border-end border-light border-dashed">
                        <div class="mt-3 mt-md-0 p-3">
                            <h5 class="text-muted fs-13 text-uppercase" title="Number of Rooms">Total Rooms</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-secondary-subtle text-secondary rounded-circle fs-22">
                                        <iconify-icon icon="solar:home-2-bold-duotone"></iconify-icon>
                                    </span>
                                </div>
                                <h3 class="mb-0 fw-bold">{{ $rooms->count() }}</h3>
                            </div>
                            <p class="mb-0 text-muted">
                                <span class="text-nowrap">All rooms in this property</span>
                            </p>
                        </div>
                    </div>

                    <!-- Available Rooms -->
                    <div class="col border-end border-light border-dashed">
                        <div class="mt-3 mt-md-0 p-3">
                            <h5 class="text-muted fs-13 text-uppercase" title="Available Rooms">Available Rooms</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-22">
                                        <iconify-icon icon="solar:check-circle-bold-duotone"></iconify-icon>
                                    </span>
                                </div>
                                <h3 class="mb-0 fw-bold">{{ $rooms->where('status', 'available')->count() }}</h3>
                            </div>
                            <p class="mb-0 text-muted">
                                <span class="text-nowrap">Ready for occupancy</span>
                            </p>
                        </div>
                    </div>

                    <!-- Occupied Rooms -->
                    <div class="col border-end border-light border-dashed">
                        <div class="mt-3 mt-md-0 p-3">
                            <h5 class="text-muted fs-13 text-uppercase" title="Occupied Rooms">Occupied Rooms</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-22">
                                        <iconify-icon icon="solar:user-rounded-bold-duotone"></iconify-icon>
                                    </span>
                                </div>
                                <h3 class="mb-0 fw-bold">{{ $rooms->where('status', 'occupied')->count() }}</h3>
                            </div>
                            <p class="mb-0 text-muted">
                                <span class="text-nowrap">Currently rented out</span>
                            </p>
                        </div>
                    </div>

                    <!-- Rooms in Maintenance -->
                    <div class="col border-end border-light border-dashed">
                        <div class="mt-3 mt-md-0 p-3">
                            <h5 class="text-muted fs-13 text-uppercase" title="Rooms in Maintenance">In Maintenance</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-22">
                                        <iconify-icon icon="solar:wrench-bold-duotone"></iconify-icon>
                                    </span>
                                </div>
                                <h3 class="mb-0 fw-bold">{{ $rooms->where('status', 'maintenance')->count() }}</h3>
                            </div>
                            <p class="mb-0 text-muted">
                                <span class="text-nowrap">Under repair or renovation</span>
                            </p>
                        </div>
                    </div>

                    <!-- Room Types Count -->
                    <div class="col">
                        <div class="mt-3 mt-md-0 p-3">
                            <h5 class="text-muted fs-13 text-uppercase" title="Room Types">Room Types</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded-circle fs-22">
                                        <iconify-icon icon="solar:layout-left-bold-duotone"></iconify-icon>
                                    </span>
                                </div>
                                <h3 class="mb-0 fw-bold">{{ $roomTypes->count() }}</h3>
                            </div>
                            <p class="mb-0 text-muted">
                                <span class="text-nowrap">Different room categories</span>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-tabs nav-bordered" role="tablist">
                            <li class="nav-item px-3" role="presentation">
                                <a href="#table" data-bs-toggle="tab" aria-expanded="false" class="nav-link py-2 active"
                                    aria-selected="false" role="tab" tabindex="-1">
                                    <span class="d-block d-sm-none"><iconify-icon icon="solar:notebook-bold"
                                            class="fs-20"></iconify-icon></span>
                                    <span class="d-none d-sm-block"><iconify-icon icon="solar:notebook-bold"
                                            class="fs-14 me-1 align-middle"></iconify-icon> Table</span>
                                </a>
                            </li>
                            {{-- Element tab button commented out
                            <li class="nav-item px-3" role="presentation">
                                <a href="#element" data-bs-toggle="tab" aria-expanded="true" class="nav-link py-2"
                                    aria-selected="true" role="tab">
                                    <span class="d-block d-sm-none"><iconify-icon icon="solar:chat-dots-bold"
                                            class="fs-20"></iconify-icon></span>
                                    <span class="d-none d-sm-block"><iconify-icon icon="solar:chat-dots-bold"
                                            class="fs-14 me-1 align-middle"></iconify-icon> Element</span>
                                </a>
                            </li>
                            --}}
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="table" role="tabpanel">

                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <form action="{{ route('landlord.rooms.index') }}" method="GET">
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <div class="d-flex flex-wrap justify-content-md-end gap-2">

                                                            {{-- Property Filter --}}
                                                            <select name="property_id" class="form-select form-select-sm"
                                                                style="width: auto;">
                                                                <option value="">All Properties</option>
                                                                @foreach($properties as $property)
                                                                    <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>{{ $property->name }}</option>
                                                                @endforeach
                                                            </select>

                                                            {{-- Room Type Filter --}}
                                                            <select name="room_type_id" class="form-select form-select-sm"
                                                                style="width: auto;">
                                                                <option value="">All Room Types</option>
                                                                @foreach($roomTypes as $roomType)
                                                                    <option value="{{ $roomType->id }}" {{ request('room_type_id') == $roomType->id ? 'selected' : '' }}>{{ $roomType->name }}</option>
                                                                @endforeach
                                                            </select>

                                                            {{-- Status Filter --}}
                                                            <select name="status" class="form-select form-select-sm"
                                                                style="width: auto;">
                                                                <option value="">All Statuses</option>
                                                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                                                <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>
                                                                    Maintenance</option>
                                                            </select>

                                                            {{-- Action Buttons --}}
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm">Filter</button>
                                                            <a href="{{ route('landlord.rooms.index') }}"
                                                                class="btn btn-light btn-sm">Clear</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div id="table-gridjs"></div>
                                </div>

                            </div>
                            {{-- Element tab content commented out
                            <div class="tab-pane" id="element" role="tabpanel">
                                <div class="row g-4">
                                    <div class="row g-4">

                                        @forelse ($rooms as $room)
                                            <div class="col-lg-4 col-md-6 mb-4">
                                                <div class="card h-100">

                                                    <div class="card-body mt-5">
                                                        <!-- Room Number -->
                                                        <h5 class="text-primary fw-medium">Room Number :
                                                            {{ $room->room_number }}
                                                        </h5>

                                                        <div>
                                                            <!-- Property Name (assumes a 'property' relationship) -->
                                                            <a href="#!"
                                                                class="fw-semibold fs-16 text-dark">{{ $room->property->name ?? 'N/A' }}</a>
                                                        </div>

                                                        <hr class="my-3">

                                                        <div class="border border-dashed p-2 rounded text-center">
                                                            <div class="row">
                                                                <div class="col-lg-6 col-4 border-end">
                                                                    <p class="text-muted fw-medium fs-14 mb-0"><span
                                                                            class="text-dark">Size : </span>
                                                                        {{ $room->size }}m&sup2;</p>
                                                                </div>
                                                                <div class="col-lg-6 col-4 border-end">
                                                                    <p class="text-muted fw-medium fs-14 mb-0"><span
                                                                            class="text-dark">Floor : </span> {{ $room->floor }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <hr class="my-3">

                                                        <!-- SPLIT AMENITIES SECTION -->
                                                        <div class="row">
                                                            <!-- 1. Amenities from Room Type -->
                                                            <div class="col-6 border-end">
                                                                <h6 class="text-muted text-uppercase fs-12 mb-2">Included
                                                                    Amenities</h6>
                                                                @if (optional($room->roomType)->amenities->isNotEmpty())
                                                                    <ul class="list-unstyled mb-0">
                                                                        @foreach ($room->roomType->amenities as $amenity)
                                                                            <li class="mb-1"><i
                                                                                    class="ti ti-check text-success me-1"></i>
                                                                                {{ $amenity->name }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <p class="fs-13 text-muted mb-0">None</p>
                                                                @endif
                                                            </div>

                                                            <!-- 2. Add-on Amenities (Directly on Room) -->
                                                            <div class="col-6">
                                                                <h6 class="text-muted text-uppercase fs-12 mb-2">Add-on
                                                                    Amenities</h6>
                                                                @php
                                                                    // Get IDs of amenities from the room type to avoid showing them twice
                                                                    $roomTypeAmenityIds =
                                                                        optional($room->roomType)->amenities->pluck(
                                                                            'id',
                                                                        ) ?? collect();
                                                                    // Filter direct room amenities to only show true "add-ons"
                                                                    $addOnAmenities = $room->amenities->whereNotIn(
                                                                        'id',
                                                                        $roomTypeAmenityIds,
                                                                    );
                                                                @endphp

                                                                @if ($addOnAmenities->isNotEmpty())
                                                                    <ul class="list-unstyled mb-0">
                                                                        @foreach ($addOnAmenities as $amenity)
                                                                            <li class="mb-1"><i class="ti ti-plus text-info me-1"></i>
                                                                                {{ $amenity->name }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <p class="fs-13 text-muted mb-0">None</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="card-footer d-flex flex-wrap align-items-center justify-content-between border-top border-dashed">
                                                        <!-- Room Price -->
                                                        <h4
                                                            class="fw-semibold text-danger d-flex align-items-center gap-2 mb-0">
                                                            {!! format_money_with_currency($room->price ?? 0) !!}
                                                        </h4>
                                                    </div>

                                                    <!-- Favorite Button -->
                                                    <span class="position-absolute top-0 end-0 p-2">
                                                        <div data-toggler="on">
                                                            <button type="button" class="btn btn-icon btn-light rounded-circle"
                                                                data-toggler-on="">
                                                                <iconify-icon icon="solar:eye-bold-duotone"
                                                                    class="fs-22 text-info"></iconify-icon>
                                                            </button>
                                                        </div>
                                                    </span>

                                                    <!-- Status Badge -->
                                                    <span class="position-absolute top-0 start-0 p-2">
                                                        @if ($room->status == 'available')
                                                            <span class="badge bg-success fs-11">Available</span>
                                                        @elseif ($room->status == 'occupied')
                                                            <span class="badge bg-danger fs-11">Occupied</span>
                                                        @else
                                                            <span class="badge bg-warning fs-11">{{ ucfirst($room->status) }}</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <p class="text-center text-muted mt-4">No rooms available to display.</p>
                                            </div>
                                        @endforelse


                                    </div>
                                </div>
                            </div>
                            --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (Auth::check() && Auth::user()->hasRole('landlord'))
        @include('backends.dashboard.rooms.create')
        @include('backends.dashboard.rooms.edit')
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
        const roomsData = {!! json_encode(
        $rooms->map(function ($room, $key) {
            $destroyUrl = route('landlord.rooms.destroy', $room->id);
            $editUrl = route('landlord.rooms.update', $room->id);
            $viewUrl = route('landlord.rooms.show', $room->id);
            return [
                $key + 1,
                $room->room_number ?? 'N/A',
                $room->property->name ?? 'N/A',
                $room->roomType->name ?? 'N/A',
                $room->description ?? 'N/A',
                $room->size ?? 'N/A',
                $room->floor ?? 'N/A',
                $room->status ?? 'N/A',
                (object) [
                    'destroy_url' => $destroyUrl,
                    'edit_url' => $editUrl,
                    'view_url' => $viewUrl,
                    'id' => $room->id,
                    'property_id' => $room->property_id,
                    'room_type_id' => $room->room_type_id,
                    'room_number' => $room->room_number,
                    'property_name' => optional($room->property)->name,
                    'room_type_name' => optional($room->roomType)->name,
                    'description' => $room->description,
                    'size' => $room->size,
                    'floor' => $room->floor,
                    'status' => $room->status,
                    'amenities' => $room->amenities->pluck('id'),
                ],
            ];
        })->values()->all(),
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE,
    ) !!};


        new gridjs.Grid({
            columns: [{
                name: "#",
                width: "50px"
            },
            {
                name: "Room Number",
                width: "150px"
            },
            {
                name: "Property",
                width: "150px"
            },
            {
                name: "Room Type",
                width: "150px"
            },
            {
                name: "Description",
                width: "200px"
            },
            {
                name: "Size",
                width: "120px"
            },
            {
                name: "Floor",
                width: "100px"
            },
            {
                name: "Status",
                width: "120px",
                formatter: (cell) => {
                    const statusClass = cell === 'available' ? 'success' : (cell === 'occupied' ?
                        'danger' : 'warning');
                    return gridjs.html(`<span class="badge badge-soft-${statusClass}">${cell}</span>`);
                }
            },
            {
                name: "Action",
                width: "150px",
                sort: false,
                formatter: (_, row) => {
                    // CORRECTED: The data object is at index 8
                    const actionData = row.cells[8].data;

                    const deleteButtonHtml =
                        `<button data-destroy-url="${actionData.destroy_url}"
                                            data-room-number="${actionData.room_number}"
                                            type="button"
                                            class="btn btn-soft-danger btn-icon btn-sm rounded-circle delete-room"
                                            title="Delete"><i class="ti ti-trash"></i></button>`;

                    let editButtonHtml = '';
                    if (actionData.edit_url) {
                        const amenitiesString = (actionData.amenities || []).join(',');
                        editButtonHtml =
                            `<button 
                                                class="btn btn-soft-success btn-icon btn-sm rounded-circle edit-room-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal" 
                                                data-id="${actionData.id}" 
                                                data-update-url="${actionData.edit_url}"
                                                data-property-id="${actionData.property_id}"
                                                data-room-type-id="${actionData.room_type_id}"
                                                data-room-number="${actionData.room_number || ''}" 
                                                data-description="${actionData.description || ''}" 
                                                data-size="${actionData.size || ''}" 
                                                data-floor="${actionData.floor || ''}" 
                                                data-status="${actionData.status}" 
                                                data-amenities="${amenitiesString}"
                                                role="button" 
                                                title="Edit"><i class="ti ti-edit fs-16"></i>
                                            </button>`;
                    }

                    return gridjs.html(`<div class="hstack gap-1 justify-content-end">
                                            <a href="${actionData.view_url}" class="btn btn-soft-primary btn-icon btn-sm rounded-circle" title="View Room"><i class="ti ti-eye"></i></a>
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
            data: roomsData,
            style: {
                table: {
                    'font-size': '0.85rem'
                }
            }
        }).render(document.getElementById("table-gridjs"));

        document.addEventListener('click', function (e) {
            const deleteButton = e.target.closest('.delete-room');
            if (deleteButton) {
                const roomNumber = deleteButton.getAttribute('data-room-number') || 'this room';
                const actionUrl = deleteButton.getAttribute('data-destroy-url');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                Swal.fire({
                    title: "Are you sure?",
                    text: `Room "${roomNumber}" will be permanently deleted! This action cannot be undone.`,
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
                        form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">
                                                      <input type="hidden" name="_method" value="DELETE">`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        });

        $(function () {
            // Initialize Select2 for the 'Create' modal
            $('#createModal #status, #createModal #property_id, #createModal #room_type_id').select2({
                dropdownParent: $('#createModal'),
                placeholder: "Select an option",
                allowClear: true
            });

            // Initialize Select2 for the 'Edit' modal
            // CORRECTED: Fixed typos in the selector
            $('#editModal #edit_status, #editModal #edit_property_id, #editModal #edit_room_type_id').select2({
                dropdownParent: $('#editModal'),
                placeholder: "Select an option",
                allowClear: true
            });
        });

        $('body').on('click', '.edit-room-btn', function () {
            const button = $(this);
            const modal = $('#editModal');

            const updateUrl = button.data('update-url');
            const propertyId = button.data('property-id');
            const roomTypeId = button.data('room-type-id');
            const roomNumber = button.data('room-number') || '';
            const description = button.data('description') || '';
            const size = button.data('size') || '';
            const floor = button.data('floor') || '';
            const status = button.data('status');

            const amenitiesString = button.data('amenities').toString();
            const associatedAmenityIds = amenitiesString ? amenitiesString.split(',') : [];

            modal.find('#editRoomForm').attr('action', updateUrl);

            modal.find('#edit_property_id').val(propertyId).trigger('change');
            modal.find('#edit_room_type_id').val(roomTypeId).trigger('change');
            modal.find('#edit_status').val(status).trigger('change');

            modal.find('#editRoomNumber').val(roomNumber);
            modal.find('#editDescription').val(description);
            modal.find('#editSize').val(size);
            modal.find('#editFloor').val(floor);

            modal.find('input[name="amenities[]"]').prop('checked', false);

            if (associatedAmenityIds.length > 0) {
                associatedAmenityIds.forEach(function (amenityId) {
                    modal.find(`input[name="amenities[]"][value="${amenityId}"]`).prop('checked', true);
                });
            }
        });
    </script>
@endpush