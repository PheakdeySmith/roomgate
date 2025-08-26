@extends('backends.layouts.app')

@section('title', 'Manage Prices | RoomGate')

@push('style')
    {{-- Styles for Select2 dropdown and Flatpickr date picker --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
    <div class="page-container">
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Manage Prices for {{ $property->name }}</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('landlord.properties.index') }}">Properties</a></li>
                    <li class="breadcrumb-item active">Manage Price</li>
                </ol>
            </div>
        </div>

        {{-- Section 1: Dynamic Property Info Header --}}
        <div class="card">
            <div class="card-body p-0">
                <div
                    class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-0 text-center align-items-center justify-content-center">
                    <div class="col border-end border-light border-dashed">
                        <div class="p-3">
                            <h5 class="text-muted fs-13 text-uppercase">Property Name</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 mt-2">
                                <h4 class="mb-0 fw-bold">{{ $property->name }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col border-end border-light border-dashed">
                        <div class="p-3">
                            <h5 class="text-muted fs-13 text-uppercase">Location</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 mt-2">
                                <h4 class="mb-0 fw-bold">{{ $property->city }}, {{ $property->country }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3">
                            <h5 class="text-muted fs-13 text-uppercase">Built In</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 mt-2">
                                <h4 class="mb-0 fw-bold">{{ $property->year_built }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Section 2: Form to Add or Edit a Price --}}
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0" id="form-title">Add New Room Price</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h6 class="alert-heading mb-1">Whoops! Something went wrong.</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- This form's action and method will be changed by JavaScript for editing --}}
                        <form id="price-form" method="POST"
                            action="{{ route('landlord.properties.storePrice', $property) }}">
                            @csrf
                            <div id="form-method"></div> {{-- A hidden div for the @method('PUT') directive --}}
                            <input type="hidden" id="original_effective_date" name="original_effective_date">

                            <div class="mb-3">
                                <label for="room_type_id" class="form-label">Room Type</label>
                                <select class="form-select" id="room_type_id" name="room_type_id" required>
                                    <option value="" disabled selected>-- Select a Room Type --</option>
                                    @foreach ($allRoomTypes as $roomType)
                                        <option value="{{ $roomType->id }}">{{ $roomType->name }} (Capacity:
                                            {{ $roomType->capacity }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price ({{ Auth::user()->currency_code }})</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price"
                                    placeholder="e.g. 150.00" required>
                            </div>
                            <div class="mb-3">
                                <label for="effective_date" class="form-label">Effective Date</label>
                                <input type="text" class="form-control" id="effective_date" name="effective_date"
                                    value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary d-none" id="cancel-edit-btn">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="submit-btn">Assign Price</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Section 3: Table of Existing Prices for this Property --}}
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Currently Assigned Prices</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Room Type</th>
                                        <th>Price</th>
                                        <th>Effective Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($property->roomTypes as $assignedRoomType)
                                        <tr>
                                            <td>{{ $assignedRoomType->name }}</td>
                                            <td>{!! format_money($assignedRoomType->pivot->price) !!}</td>
                                            <td>{{ $assignedRoomType->pivot->effective_date }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('landlord.properties.roomTypes.overrides.index', ['property' => $property->id, 'roomType' => $assignedRoomType->id]) }}"
                                                        class="btn btn-soft-info btn-icon btn-sm rounded-circle"
                                                        title="Set Seasonal Prices">
                                                        <i class="ti ti-calendar-stats"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-soft-primary btn-icon btn-sm rounded-circle edit-btn"
                                                        title="Edit" data-room-type-id="{{ $assignedRoomType->id }}"
                                                        data-price="{{ $assignedRoomType->pivot->price }}"
                                                        data-effective-date="{{ $assignedRoomType->pivot->effective_date }}"
                                                        data-update-url="{{ route('landlord.properties.updatePrice', $property) }}">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-soft-danger btn-icon btn-sm rounded-circle delete-price-btn"
                                                        title="Delete" data-room-type-name="{{ $assignedRoomType->name }}"
                                                        data-effective-date="{{ $assignedRoomType->pivot->effective_date }}"
                                                        data-room-type-id="{{ $assignedRoomType->id }}"
                                                        data-action-url="{{ route('landlord.properties.destroyPrice', $property) }}">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No prices have been set for
                                                this
                                                property yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Get references to form elements
            const priceForm = document.getElementById('price-form');
            const formTitle = document.getElementById('form-title');
            const submitBtn = document.getElementById('submit-btn');
            const cancelBtn = document.getElementById('cancel-edit-btn');
            const formMethodDiv = document.getElementById('form-method');

            // Input fields
            const roomTypeSelect = $('#room_type_id'); // Use jQuery object for Select2
            const priceInput = document.getElementById('price');
            const effectiveDateInput = document.getElementById('effective_date');
            const originalEffectiveDateInput = document.getElementById('original_effective_date');

            const addUrl = "{{ route('landlord.properties.storePrice', $property) }}";

            // Initialize Flatpickr & Select2
            const datePicker = flatpickr("#effective_date", {
                dateFormat: "Y-m-d"
            });
            roomTypeSelect.select2({
                placeholder: "-- Select a Room Type --",
                allowClear: true
            });

            // Listen for clicks on ANY edit button
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    // Get data from the clicked button's data-* attributes
                    const roomTypeId = this.dataset.roomTypeId;
                    const price = this.dataset.price;
                    const effectiveDate = this.dataset.effectiveDate;
                    const updateUrl = this.dataset.updateUrl;

                    // --- Change form to "Edit Mode" ---
                    formTitle.innerText = 'Edit Price';
                    submitBtn.innerText = 'Update Price';
                    priceForm.action = updateUrl;
                    formMethodDiv.innerHTML = '@method('PUT')'; // Set method to PUT for update
                    cancelBtn.classList.remove('d-none'); // Show the cancel button

                    // --- Populate form fields ---
                    priceInput.value = price;
                    originalEffectiveDateInput.value =
                        effectiveDate; // Store the original date for lookup
                    datePicker.setDate(effectiveDate, true); // Set Flatpickr's date

                    // Set Select2's value and disable it to prevent changing the room type
                    roomTypeSelect.val(roomTypeId).trigger('change');

                    // Scroll to the form for better UX
                    priceForm.scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });

            // Function to reset the form to "Add Mode"
            function resetForm() {
                formTitle.innerText = 'Add New Room Price';
                submitBtn.innerText = 'Assign Price';
                priceForm.action = addUrl;
                priceForm.reset(); // Reset form fields
                formMethodDiv.innerHTML = ''; // Remove the @method('PUT')
                cancelBtn.classList.add('d-none'); // Hide cancel button
                roomTypeSelect.val(null).trigger('change'); // Reset Select2
                roomTypeSelect.prop('disabled', false); // Re-enable the dropdown
                datePicker.setDate(new Date(), true); // Reset date to today
            }

            // Listen for click on the cancel button
            cancelBtn.addEventListener('click', resetForm);


            document.addEventListener('click', function (e) {
                // We listen for a click on our new delete button class
                if (e.target.closest('.delete-price-btn')) {
                    const button = e.target.closest('.delete-price-btn');
                    const actionUrl = button.dataset.actionUrl;

                    // Get the specific data for this price record
                    const roomTypeName = button.dataset.roomTypeName || 'this price';
                    const roomTypeId = button.dataset.roomTypeId;
                    const effectiveDate = button.dataset.effectiveDate;

                    if (!actionUrl || !roomTypeId || !effectiveDate) {
                        console.error('Delete action data is missing from the button.');
                        Swal.fire('Error!', 'Cannot proceed with deletion. Required data is missing.',
                            'error');
                        return;
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content');

                    Swal.fire({
                        title: "Are you sure?",
                        // Updated confirmation text
                        text: `The price for "${roomTypeName}" on ${effectiveDate} will be permanently deleted!`,
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
                            // Dynamically create and submit a form
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = actionUrl;

                            // Create hidden inputs
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;

                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'DELETE';

                            // Add the data required by the controller to identify the record
                            const roomTypeIdInput = document.createElement('input');
                            roomTypeIdInput.type = 'hidden';
                            roomTypeIdInput.name = 'room_type_id';
                            roomTypeIdInput.value = roomTypeId;

                            const effectiveDateInput = document.createElement('input');
                            effectiveDateInput.type = 'hidden';
                            effectiveDateInput.name = 'effective_date';
                            effectiveDateInput.value = effectiveDate;

                            // Append all inputs to the form and submit
                            form.appendChild(csrfInput);
                            form.appendChild(methodInput);
                            form.appendChild(roomTypeIdInput);
                            form.appendChild(effectiveDateInput);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
@endpush