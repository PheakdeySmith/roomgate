@extends('backends.layouts.app')

@section('title', 'Add Tenant & Contract | RoomGate')

@push('style')
    {{-- Your existing stylesheets --}}
    <link href="{{ asset('assets') }}/css/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush


@section('content')
    <div class="page-container">
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Add New Tenant & Contract</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('landlord.contracts.index') }}">Contracts</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </div>
        </div>

        {{-- The main form wraps everything --}}
        <form method="POST" action="{{ route('landlord.contracts.store') }}" enctype="multipart/form-data" id="create-tenant-contract-form">
            @csrf
            <div class="row">
                {{-- LEFT COLUMN: TEXTUAL INFORMATION --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header border-bottom border-dashed">
                            <h4 class="card-title mb-0">Tenant & Contract Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Must be at least 8 characters" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password" required>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label for="room_id" class="form-label">Assign Room</label>
                                    <select class="form-control select2" id="room_id" name="room_id" required>
                                        <option value="" disabled selected>Select an available room...</option>
                                        @foreach ($availableRooms as $room)
                                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                                {{ $room->property->name }} - {{ $room->room_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="start_date" class="form-label">Contract Start Date</label>
                                    <input type="text" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="end_date" class="form-label">Contract End Date</label>
                                    <input type="text" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="rent_amount" class="form-label">Rent Amount ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="rent_amount" name="rent_amount" placeholder="e.g. 500.00" value="{{ old('rent_amount') }}">
                                    <small class="form-text text-muted">Optional. Leave blank if rent is not applicable.</small>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="billing_cycle" class="form-label">Billing Cycle</label>
                                    <select class="form-control select2" id="billing_cycle" name="billing_cycle" required>
                                        <option value="monthly" selected>Monthly</option>
                                        <option value="yearly">Yearly</option>
                                        <option value="daily">Daily</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: FILE UPLOADS --}}
                <div class="col-lg-4">
                    {{-- Tenant Image Upload Card --}}
                    <div class="card">
                        <div class="card-header"><h4 class="card-title mb-0">Tenant Profile Image</h4></div>
                        <div class="card-body text-center">
                            <img id="image_preview" src="{{ asset('assets/images/default_image.png') }}" alt="Image Preview" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 2px solid #eee;">
                            {{-- CORRECTION 1: Added a visually-hidden label for accessibility --}}
                            <label for="image" class="visually-hidden">Tenant Profile Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="form-text text-muted">Optional. Recommended size: 300x300px.</small>
                        </div>
                    </div>

                    {{-- Contract Scan Upload Card --}}
                    <div class="card">
                        <div class="card-header"><h4 class="card-title mb-0">Contract Scan/File</h4></div>
                        <div class="card-body">
                            {{-- CORRECTION 2: Added a visible label for the file input --}}
                            <label for="contract_image" class="form-label">Upload Contract File</label>
                            <input type="file" class="form-control" id="contract_image" name="contract_image" accept="image/*,.pdf">
                            <small class="form-text text-muted">Optional. Accepts images or PDF.</small>
                        </div>
                    </div>

                    {{-- Submit Button Card --}}
                    <div class="card">
                         <div class="card-body">
                             <div class="d-grid">
                                 <button type="submit" class="btn btn-primary">Create Tenant & Contract</button>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection


@push('script')
    {{-- Your other scripts can be included here --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // Initialize Select2 dropdowns
            $('.select2').select2({
                width: '100%' // Ensure it takes full width of its container
            });

            // Initialize Flatpickr date pickers
            flatpickr("#start_date, #end_date", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
            });

            // --- Live Image Preview Logic ---
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image_preview');
            
            if(imageInput && imagePreview) {
                imageInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                        };
                        
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
@endpush
