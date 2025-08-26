<div class="row g-2 align-items-center mb-3">
    {{-- Column for Title --}}
    <div class="col-md-6">
        <div class="d-flex align-items-center gap-2">
            {{-- This hamburger menu button will only show on extra-small to large screens --}}
            <div class="flex-shrink-0 d-xl-none d-inline-flex">
                <button class="btn btn-sm btn-icon btn-soft-primary align-items-center p-0" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#fileManagerSidebar" aria-controls="fileManagerSidebar">
                    <i class="ti ti-menu-2 fs-20"></i>
                </button>
            </div>
            <h4 class="header-title mb-0 text-truncate">Manage Utility Readings</h4>
        </div>
    </div>

    {{-- Column for Buttons, aligned to the end on medium screens and up --}}
    <div class="col-md-6">
        <div class="d-flex justify-content-md-end gap-2">
            @if (Auth::check() && Auth::user()->hasRole('landlord'))
                {{-- "Add Contract" button with text that hides on very small screens --}}
                <a href="{{ route('landlord.properties.rates.index', ['property' => $property->id]) }}"
                    class="btn btn-primary btn-sm d-inline-flex align-items-center">
                    <i class="ti ti-settings"></i>
                    <span class="d-none d-sm-inline ms-1">Property Rate</span>
                </a>
            @endif
        </div>
    </div>
</div>

<ul class="nav nav-tabs mb-3" id="utilitiesTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="all-rooms-tab" data-bs-toggle="tab" data-bs-target="#all-rooms-pane"
            type="button" role="tab"><i class="ti ti-list-details"></i><span class="d-none d-sm-inline ms-1">All
                Rooms</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="recorded-tab" data-bs-toggle="tab" data-bs-target="#recorded-pane" type="button"
            role="tab"><i class="ti ti-circle-check"></i><span
                class="d-none d-sm-inline ms-1">Recorded</span></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="needs-reading-tab" data-bs-toggle="tab" data-bs-target="#needs-reading-pane"
            type="button" role="tab"><i class="ti ti-alert-triangle"></i><span
                class="d-none d-sm-inline ms-1">Needs
                Reading</span></button>
    </li>
</ul>

<div class="mb-3">
    <input type="text" id="roomSearchInput" class="form-control" placeholder="Search by Room, Tenant, or Meter #...">
</div>

<div class="tab-content" id="utilitiesTabContent">

    <div class="tab-pane fade show active" id="all-rooms-pane" role="tabpanel" aria-labelledby="all-rooms-tab"
        tabindex="0">
        <div class="accordion" id="allRoomsAccordion">
            @forelse ($property->rooms as $room)
                @include('backends.dashboard.properties.partials.utility_items._room_utility_item', [
                    'room' => $room,
                    'type' => 'all',
                ])
            @empty
                <div class="alert alert-info text-center">There are no rooms in this property yet.</div>
            @endforelse
        </div>
    </div>

    <div class="tab-pane fade" id="recorded-pane" role="tabpanel" aria-labelledby="recorded-tab" tabindex="0">
        <div class="accordion" id="recordedAccordion">
            @foreach ($property->rooms as $room)
                @if ($room->meter_status['text'] === 'Recorded')
                    @include('backends.dashboard.properties.partials.utility_items._room_utility_item', [
                        'room' => $room,
                        'type' => 'rec',
                    ])
                @endif
            @endforeach
        </div>
    </div>

    <div class="tab-pane fade" id="needs-reading-pane" role="tabpanel" aria-labelledby="needs-reading-tab"
        tabindex="0">
        <div class="accordion" id="needsReadingAccordion">
            @foreach ($property->rooms as $room)
                @if (in_array($room->meter_status['icon'], ['clock-hour-4', 'alert-triangle']))
                    @include('backends.dashboard.properties.partials.utility_items._room_utility_item', [
                        'room' => $room,
                        'type' => 'needs',
                    ])
                @endif
            @endforeach
        </div>
    </div>
</div>

<div class="modal fade" id="assignMeterModal" tabindex="-1" aria-labelledby="assignMeterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignMeterModalLabel">Assign New Meter</h5><button type="button"
                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('landlord.meters.store') }}" method="POST" id="assignMeterForm">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">
                    <input type="hidden" name="room_id" id="modalRoomId">
                    <div class="mb-3"><label class="form-label">Assigning to Room:</label><input type="text"
                            class="form-control" id="modalRoomNumber" disabled></div>
                    <div class="mb-3"><label for="utility_type" class="form-label">Utility Type</label>
                        <select class="form-select" id="utility_type" name="utility_type_id" required>
                            <option selected disabled value="">Choose...</option>
                            @foreach ($utilityTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label for="meter_number" class="form-label">Meter Number</label><input
                            type="text" class="form-control" name="meter_number" required></div>
                    <div class="mb-3"><label for="initial_reading" class="form-label">Initial
                            Reading</label><input type="number" step="0.01" class="form-control"
                            name="initial_reading" value="0.00" required></div>
                    <div class="mb-3"><label for="installed_at" class="form-label">Installation
                            <input type="text" class="form-control" id="installed_at" name="installed_at"
                                value="{{ old('installed_at', now()->format('Y-m-d')) }}" required></div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary"
                    form="assignMeterForm">Save Meter</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="editMeterModal" tabindex="-1" aria-labelledby="editMeterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMeterModalLabel">Edit Meter Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- The action URL will be set by JavaScript --}}
                <form action="" method="POST" id="editMeterForm">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="edit_utility_type" class="form-label">Utility Type</label>
                        <select class="form-select" id="edit_utility_type" name="utility_type_id" required>
                            <option disabled value="">Choose...</option>
                            @foreach ($utilityTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_meter_number" class="form-label">Meter Number</label>
                        <input type="text" class="form-control" id="edit_meter_number" name="meter_number"
                            required>
                    </div>

                        <div class="mb-3">
                            <label for="edit_initial_reading" class="form-label">Initial Reading</label>
                            <input type="number" step="0.01" class="form-control" id="edit_initial_reading"
                                name="initial_reading" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_installed_at" class="form-label">Installation Date</label>
                            <input type="date" class="form-control" id="edit_installed_at" name="installed_at"
                                required>
                        </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                {{-- This button submits the form outside of it --}}
                <button type="submit" class="btn btn-primary" form="editMeterForm">Save Changes</button>
            </div>
        </div>
    </div>
</div>
