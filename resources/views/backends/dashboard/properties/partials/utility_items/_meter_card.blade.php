@php
    $allReadings = $meter->meterReadings->sortByDesc('reading_date')->sortByDesc('id')->values();
    $latest_reading = $allReadings->first();
    $unique_prefix = "{$type}-{$meter->id}";
@endphp

<div class="card mb-3 {{ $status['class'] === 'warning' ? 'border-warning' : '' }}">
    <div class="card-header {{ $status['class'] === 'warning' ? 'bg-warning-subtle' : '' }}">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i
                    class="ti ti-{{ $meter->utilityType->name === 'Electricity' ? 'bolt text-warning' : 'droplet text-info' }} me-2"></i>
                {{ $meter->utilityType->name }} Meter
                <small class="text-muted">#{{ $meter->meter_number }}</small>
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
                <h5>Record New Reading</h5>
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
                <div>
                    <small class="text-muted d-block">Last Recorded Reading:</small>
                    <h5 class="mb-0 last-reading-value">
                        {{ number_format($latest_reading->reading_value ?? $meter->initial_reading, 2) }}
                        {{ $meter->utilityType->unit_of_measure }}
                    </h5>
                    <small class="text-muted last-reading-date">
                        on {{ ($latest_reading->reading_date ?? $meter->installed_at)->format('M d, Y') }}
                    </small>
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

        {{-- MOBILE VIEW --}}
        <div class="d-md-none">
            <ul class="nav nav-pills nav-fill btn-group mb-3">
                <li class="nav-item"><button class="btn btn-sm btn-outline-primary active" data-bs-toggle="tab"
                        data-bs-target="#record-{{ $unique_prefix }}">Record</button></li>
                <li class="nav-item"><button class="btn btn-sm btn-outline-primary" data-bs-toggle="tab"
                        data-bs-target="#history-{{ $unique_prefix }}">History</button></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="record-{{ $unique_prefix }}">
                    <form action="{{ route('landlord.meter-readings.store') }}" method="POST" class="ajax-form">
                        @csrf
                        <input type="hidden" name="meter_id" value="{{ $meter->id }}">
                        <div class="mb-2">
                            <label for="reading-m-{{ $unique_prefix }}" class="form-label">New Value</label>
                            <input type="number" step="0.01" class="form-control" name="reading_value"
                                id="reading-m-{{ $unique_prefix }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save Reading</button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted d-block">Last Recorded Reading:</small>
                        <h5 class="mb-0 last-reading-value">
                            {{ number_format($latest_reading->reading_value ?? $meter->initial_reading, 2) }}
                            {{ $meter->utilityType->unit_of_measure }}
                        </h5>
                        <small class="text-muted last-reading-date">
                            on {{ ($latest_reading->reading_date ?? $meter->installed_at)->format('M d, Y') }}
                        </small>
                    </div>
                </div>
                <div class="tab-pane fade" id="history-{{ $unique_prefix }}">
                    <div class="history-container" id="history-container-m-{{ $meter->id }}"
                        data-url="{{ route('landlord.meters.history', $meter->id) }}">
                        <div class="text-center p-4">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>