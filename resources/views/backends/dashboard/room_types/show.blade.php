@extends('backends.layouts.app')

@section('title', 'Room Type Details | RoomGate')

@push('style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/mermaid.min.css">
    <link href="{{ asset('assets') }}/css/icons.min.css" rel="stylesheet" type="text/css">
    <style>
        .amenity-badge {
            margin-right: 8px;
            margin-bottom: 8px;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
        }
        
        .amenity-badge i {
            margin-right: 5px;
        }
        
        .room-type-card {
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }
        
        .avatar-title {
            align-items: center;
            background-color: var(--bs-primary);
            color: #fff;
            display: flex;
            font-weight: 500;
            height: 100%;
            justify-content: center;
            width: 100%;
        }
        
        .text-bg-primary {
            color: #fff!important;
            background-color: rgba(var(--bs-primary-rgb),1)!important;
        }
        
        .text-bg-info {
            color: #fff!important;
            background-color: rgba(var(--bs-info-rgb),1)!important;
        }
        
        .text-bg-success {
            color: #fff!important;
            background-color: rgba(var(--bs-success-rgb),1)!important;
        }
        
        .text-bg-warning {
            color: #fff!important;
            background-color: rgba(var(--bs-warning-rgb),1)!important;
        }
    </style>
@endpush

@section('content')
    <div class="page-container">
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Room Type Details</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Boron</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('landlord.room_types.index') }}">Room Types</a></li>
                    <li class="breadcrumb-item active">{{ $roomType->name }}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card room-type-card">
                    <div class="card-header border-bottom border-dashed">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title mb-0">{{ $roomType->name }}</h4>
                            <span class="badge badge-soft-{{ $roomType->status === 'active' ? 'success' : 'danger' }} ms-2">
                                {{ ucfirst($roomType->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5 class="fs-15 mb-3">Description</h5>
                            <p class="text-muted mb-0">
                                {{ $roomType->description ?: 'No description provided.' }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <h5 class="fs-15 mb-3">Capacity</h5>
                            <p class="d-flex align-items-center">
                                <i class="ti ti-users fs-16 me-1 text-primary"></i>
                                <span class="fw-medium">{{ $roomType->capacity }} {{ $roomType->capacity > 1 ? 'persons' : 'person' }}</span>
                            </p>
                        </div>

                        <div class="mb-4">
                            <h5 class="fs-15 mb-3">Amenities</h5>
                            @if($roomType->amenities->count() > 0)
                                <div>
                                    @foreach($roomType->amenities as $amenity)
                                        <span class="amenity-badge badge bg-light text-dark">
                                            <i class="ti ti-check-circle text-success"></i> {{ $amenity->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">No amenities assigned to this room type.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Rooms with this type -->
                <div class="card room-type-card mt-4">
                    <div class="card-header border-bottom border-dashed">
                        <h5 class="card-title mb-0">Rooms using this type</h5>
                    </div>
                    <div class="card-body">
                        @if($roomCount > 0)
                            <p class="mb-3">This room type is used by {{ $roomCount }} {{ $roomCount === 1 ? 'room' : 'rooms' }}.</p>
                            <a href="{{ route('landlord.rooms.index') }}?type={{ $roomType->id }}" class="btn btn-sm btn-primary">
                                <i class="ti ti-list me-1"></i> View Rooms
                            </a>
                        @else
                            <div class="alert alert-info mb-0">
                                No rooms are currently using this room type.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Stats Cards -->
                <div class="row row-cols-1 text-center">
                    
                    <div class="col">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="text-muted fs-13 text-uppercase">Associated Rooms</h5>
                                <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                    <div class="user-img fs-42 flex-shrink-0">
                                        <span class="avatar-title text-bg-success rounded-circle fs-22">
                                            <i class="ti ti-home-2"></i>
                                        </span>
                                    </div>
                                    <h3 class="mb-0 fw-bold">{{ $roomCount }}</h3>
                                </div>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Rooms using this type</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="text-muted fs-13 text-uppercase">Amenities</h5>
                                <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                    <div class="user-img fs-42 flex-shrink-0">
                                        <span class="avatar-title text-bg-warning rounded-circle fs-22">
                                            <i class="ti ti-tools"></i>
                                        </span>
                                    </div>
                                    <h3 class="mb-0 fw-bold">{{ $roomType->amenities->count() }}</h3>
                                </div>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Included with this type</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="card room-type-card">
                    <div class="card-header border-bottom border-dashed">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('landlord.room_types.index') }}" class="btn btn-outline-primary">
                                <i class="ti ti-arrow-left me-1"></i> Back to Room Types
                            </a>
                            <button type="button" 
                                    class="btn btn-soft-success edit-type-btn"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal" 
                                    data-id="{{ $roomType->id }}" 
                                    data-name="{{ $roomType->name }}" 
                                    data-description="{{ $roomType->description ?: '' }}" 
                                    data-capacity="{{ $roomType->capacity }}" 
                                    data-status="{{ $roomType->status }}" 
                                    data-update-url="{{ route('landlord.room_types.update', $roomType->id) }}"
                                    data-amenities="{{ $roomType->amenities->pluck('id')->implode(',') }}">
                                <i class="ti ti-edit me-1"></i> Edit Room Type
                            </button>
                            <button type="button"
                                    class="btn btn-soft-danger delete-type"
                                    data-type-id="{{ $roomType->id }}"
                                    data-type-name="{{ $roomType->name }}"
                                    data-action-url="{{ route('landlord.room_types.destroy', $roomType->id) }}">
                                <i class="ti ti-trash me-1"></i> Delete Room Type
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('backends.dashboard.room_types.edit')
@endsection

@push('script')
    <script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>
    <script src="{{ asset('assets') }}/js/select2.min.js"></script>
    
    <script>
        $(function () {
            $('#editStatus').select2({
                dropdownParent: $('#editModal'),
                placeholder: "Select status",
                allowClear: true
            });
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
