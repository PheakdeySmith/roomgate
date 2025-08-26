@php
    $status = $room->meter_status;
    $accordionId = "accordion-{$type}-{$room->id}";
    $headerId = "heading-{$type}-{$room->id}";
@endphp

<div class="accordion-item {{ $status['class'] === 'danger' ? 'border border-danger' : '' }}">
    <h2 class="accordion-header" id="{{ $headerId }}">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#{{ $accordionId }}">
            <span class="fw-bold me-2">Room {{ $room->room_number }}</span> -
            <span class="text-muted ms-2">{{ $room->activeContract->tenant->name ?? 'Vacant' }}</span>
            <span class="badge bg-{{ $status['class'] }}-subtle text-{{ $status['class'] }} ms-auto"><i
                    class="ti ti-{{ $status['icon'] }} me-1"></i>{{ $status['text'] }}</span>
        </button>
    </h2>
    <div id="{{ $accordionId }}" class="accordion-collapse collapse" data-bs-parent="#{{ $type }}RoomsAccordion">
        <div class="accordion-body">
            {{-- Main content based on status --}}
            @if ($status['icon'] === 'help-hexagon')
                <div class="alert alert-info text-center">
                    <p class="mb-2">No utility meters assigned.</p><button class="btn btn-sm btn-success"
                        data-bs-toggle="modal" data-bs-target="#assignMeterModal" data-room-id="{{ $room->id }}"
                        data-room-number="{{ $room->room_number }}"><i class="ti ti-plus me-1"></i> Assign First
                        Meter</button>
                </div>
            @else
                {{-- Action Buttons --}}
                <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                        data-bs-target="#meterHistoryModal-{{ $room->id }}">
                        <i class="ti ti-history me-1"></i> View History
                    </button>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#assignMeterModal"
                        data-room-id="{{ $room->id }}" data-room-number="{{ $room->room_number }}"><i
                            class="ti ti-plus me-1"></i> Assign New Meter</button>
                </div>

                @if ($status['icon'] === 'alert-triangle')
                    <div class="alert alert-danger">
                        <h4 class="alert-heading">Warning: Reading Overdue!</h4>
                        <p>This room's reading is several months old.</p>
                    </div>
                @elseif ($status['icon'] === 'moon')
                    <div class="alert alert-secondary text-center">This room is vacant. Meter readings are paused.</div>
                @endif

                {{-- CHANGE: Loop over activeMeters for the main display --}}
                @foreach ($room->activeMeters as $meter)
                    @include('backends.dashboard.properties.partials.utility_items._meter_card', ['meter' => $meter, 'type' => $type, 'status' => $status])
                @endforeach
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="meterHistoryModal-{{ $room->id }}" tabindex="-1"
    aria-labelledby="meterHistoryModalLabel-{{ $room->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="meterHistoryModalLabel-{{ $room->id }}">Meter History for Room
                    {{ $room->room_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Use the toggle to set which meter is currently active for this room. Only one
                    meter per utility type can be active at a time.</p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Utility</th>
                                <th>Meter #</th>
                                <th>Installed On</th>
                                <th class="text-center">Status (Active)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($room->allMeters as $histMeter)
                                <tr>
                                    <td>{{ $histMeter->utilityType->name }}</td>
                                    <td>{{ $histMeter->meter_number }}</td>
                                    <td>{{ $histMeter->installed_at->format('M d, Y') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('landlord.meters.toggle-status', $histMeter->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <input type="checkbox" id="switch-{{ $histMeter->id }}"
                                                onchange="this.form.submit()" data-switch="success"
                                                @checked($histMeter->status == 'active')>

                                            <label for="switch-{{ $histMeter->id }}" data-on-label="On"
                                                data-off-label="Off"></label>

                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No meter history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>