@php
    $allReadings = $meter->meterReadings->sortByDesc('reading_date')->sortByDesc('id')->values();
    $latest_reading = $allReadings->first();
    $unique_prefix = "{$type}-{$meter->id}";
    
    // Individual meter status check
    $needs_reading = true;
    $now = now();
    
    if ($latest_reading && \Carbon\Carbon::parse($latest_reading->reading_date)->isSameMonth($now)) {
        $needs_reading = false;
    }
@endphp

<div class="card mb-3 {{ $needs_reading ? 'border-warning' : '' }} shadow-sm">
    <div class="card-header {{ $needs_reading ? 'bg-warning-subtle' : 'bg-light' }} bg-opacity-50">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 d-flex align-items-center">
                <i class="ti ti-{{ $meter->utilityType->name === 'Electricity' ? 'bolt text-warning' : 'droplet text-info' }} me-2"></i>
                <span class="d-flex flex-column">
                    {{ $meter->utilityType->name }} Meter 
                    <small class="text-muted fs-12">#{{ $meter->meter_number }}</small>
                </span>
            </h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-icon btn-light" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <button type="button" class="dropdown-item edit-meter-btn" data-bs-toggle="modal"
                            data-bs-target="#editMeterModal" data-meter-number="{{ $meter->meter_number }}"
                            data-utility-type-id="{{ $meter->utility_type_id }}"
                            data-initial-reading="{{ $meter->initial_reading }}"
                            data-installed-at="{{ $meter->installed_at->format('Y-m-d') }}"
                            data-update-url="{{ route('landlord.meters.update', $meter->id) }}">
                            Edit
                        </button>
                    </li>
                    <li>
                        <form action="{{ route('landlord.meters.deactivate', $meter->id) }}" method="POST"
                            class="deactivate-meter-form" data-meter-number="{{ $meter->meter_number }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="dropdown-item text-danger">Deactivate</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- DESKTOP VIEW --}}
        <div class="row d-none d-md-flex">
            <div class="col-md-4 border-end">
                <h5>New Reading</h5>
                <form action="{{ route('landlord.meter-readings.store') }}" method="POST" class="ajax-form">
                    @csrf
                    <input type="hidden" name="meter_id" value="{{ $meter->id }}">
                    <div class="mb-2">
                        <label for="reading-d-{{ $unique_prefix }}" class="form-label">New Value</label>
                        <input type="number" step="0.01" class="form-control" id="reading-d-{{ $unique_prefix }}"
                            name="reading_value" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Save</button>
                </form>
                <hr>
                <div class="text-center">
                    <small class="text-muted d-block">Last Recorded Reading:</small>
                    <small class="text-muted last-reading-date d-block">
                        @php
                            $dateObj = $latest_reading ? $latest_reading->reading_date : $meter->installed_at;
                        @endphp
                        {{ $dateObj->format('M d, Y') }}
                    </small>
                    <h5 class="mb-0 mt-2 last-reading-value">
                        {{ floor($latest_reading->reading_value ?? $meter->initial_reading) }}
                        <small class="text-muted">
                            {{ $meter->utilityType->unit_of_measure }}
                        </small>
                    </h5>
                </div>
            </div>
            <div class="col-md-8">
                <h5>Reading History</h5>
                <div class="history-container" id="history-container-d-{{ $meter->id }}"
                    data-url="{{ route('landlord.meters.history', $meter->id) }}">
                    <div class="text-center p-4">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MOBILE VIEW - REDESIGNED TO MATCH SCREENSHOT --}}
        <div class="d-md-none">
            <!-- Last Reading Card - Vertical Layout -->
            <div class="bg-light-subtle rounded-3 p-3 mb-3">
                <div class="text-center">
                    <!-- Title -->
                    <span class="text-muted fs-12 d-block mb-1">Last Reading</span>
                    
                    <!-- Date -->
                    <span class="badge bg-white text-dark border small last-reading-date d-inline-block mb-2" style="max-width: 120px;">
                        @php
                            $dateObj = $latest_reading ? $latest_reading->reading_date : $meter->installed_at;
                        @endphp
                        {{ $dateObj->format('M d, Y') }}
                    </span>
                    
                    <!-- Reading Value -->
                    <h3 class="mb-0 fw-bold last-reading-value" style="font-size: calc(1.3rem + 0.8vw);">
                        {{ floor($latest_reading->reading_value ?? $meter->initial_reading) }}
                        <span class="text-muted">{{ $meter->utilityType->unit_of_measure }}</span>
                    </h3>
                    
                    <!-- Unit -->
                    
                </div>
            </div>

            <!-- Record New Reading -->
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <span class="fw-medium d-flex align-items-center fs-7">
                            <i class="ti ti-pencil me-1"></i>New Reading
                        </span>
                    </div>
                    <small class="text-muted">{{ $meter->utilityType->unit_of_measure }}</small>
                </div>
                
                <form action="{{ route('landlord.meter-readings.store') }}" method="POST" class="ajax-form">
                    @csrf
                    <input type="hidden" name="meter_id" value="{{ $meter->id }}">
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" class="form-control bg-white border-end-0" 
                               name="reading_value" id="reading-m-{{ $unique_prefix }}" 
                               placeholder="Enter value" required>
                        <button type="submit" class="btn btn-dark">
                            <i class="ti ti-device-floppy"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reading History -->
            <div class="mt-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="ti ti-history me-1"></i>
                    <span class="fw-medium">Reading History</span>
                </div>
                
                <div class="history-container" id="history-container-m-{{ $meter->id }}"
                    data-url="{{ route('landlord.meters.history', $meter->id) }}">
                    <div class="text-center p-3">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <p class="text-muted small mt-1 mb-0">Loading history...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>