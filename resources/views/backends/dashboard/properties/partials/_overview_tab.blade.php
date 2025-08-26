<div class="d-flex align-items-center gap-1 mb-3">
    <div class="flex-shrink-0 d-xl-none d-inline-flex">
        <button class="btn btn-sm btn-icon btn-soft-primary align-items-center p-0" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#fileManagerSidebar" aria-controls="fileManagerSidebar">
            <i class="ti ti-menu-2 fs-20"></i>
        </button>
    </div>
    <h4 class="header-title">Property Overview</h4>
</div>

<div class="row">
    <div class="col-md-6 col-xxl-3" id="roomsCard">
        <div class="card border">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div
                        class="flex-shrink-0 avatar-md bg-primary-subtle d-inline-flex align-items-center justify-content-center rounded-2">
                        <i class="ti ti-building fs-18 text-primary"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span data-toggler="on">
                            <a href="#" data-toggler-on="">
                                <i class="ti ti-star-filled text-warning fs-16"></i>
                            </a>
                            <a href="#" data-toggler-off="" class="d-none">
                                <i class="ti ti-star-filled text-muted fs-16"></i>
                            </a>
                        </span>
                        <div class="dropdown flex-shrink-0 text-muted">
                            <a href="#" class="dropdown-toggle drop-arrow-none fs-20 link-reset p-0"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('landlord.rooms.index', ['property_id' => $property->id]) }}" class="dropdown-item">
                                    <i class="ti ti-list me-1"></i> View All Rooms
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="ti ti-plus me-1"></i> Add Room
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-grow-1 mt-3">
                    <h5 class="mb-1"><a href="{{ route('landlord.rooms.index', ['property_id' => $property->id]) }}" class="link-reset">Rooms</a></h5>
                    <p class="text-muted mb-0">{{ $property->rooms->count() }} Total Rooms</p>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3 mb-1">
                    <p class="fs-14 mb-0">{{ $property->rooms->where('status', 'occupied')->count() }} Occupied</p>
                    <p class="fs-14 mb-0">{{ $property->rooms->where('status', 'available')->count() }} Available</p>
                </div>
                <div class="progress progress-sm bg-primary-subtle" role="progressbar"
                    aria-label="Room occupancy" aria-valuenow="{{ $property->rooms->count() > 0 ? ($property->rooms->where('status', 'occupied')->count() / $property->rooms->count()) * 100 : 0 }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: {{ $property->rooms->count() > 0 ? ($property->rooms->where('status', 'occupied')->count() / $property->rooms->count()) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div><!-- end col -->

    <div class="col-md-6 col-xxl-3" id="contractsCard">
        <div class="card border">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div
                        class="flex-shrink-0 avatar-md bg-success-subtle d-inline-flex align-items-center justify-content-center rounded-2">
                        <i class="ti ti-file-contract fs-18 text-success"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span data-toggler="off">
                            <a href="#" data-toggler-on="" class="d-none">
                                <i class="ti ti-star-filled text-warning fs-16"></i>
                            </a>
                            <a href="#" data-toggler-off="">
                                <i class="ti ti-star-filled text-muted fs-16"></i>
                            </a>
                        </span>
                        <div class="dropdown flex-shrink-0 text-muted">
                            <a href="#" class="dropdown-toggle drop-arrow-none fs-20 link-reset p-0"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#v-pills-contracts" class="dropdown-item" data-bs-toggle="tab" role="tab" aria-controls="v-pills-contracts">
                                    <i class="ti ti-list me-1"></i> View All Contracts
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="ti ti-plus me-1"></i> Create Contract
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-grow-1 mt-3">
                    <h5 class="mb-1"><a href="#v-pills-contracts" class="link-reset" data-bs-toggle="tab" role="tab" aria-controls="v-pills-contracts">Contracts</a></h5>
                    <p class="text-muted mb-0">{{ $property->contracts->count() }} Total Contracts</p>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3 mb-1">
                    <p class="fs-14 mb-0">{{ $property->contracts->where('status', 'active')->count() }} Active</p>
                    <p class="fs-14 mb-0">{{ $property->contracts->where('status', '!=', 'active')->count() }} Inactive/Expired</p>
                </div>
                <div class="progress progress-sm bg-success-subtle" role="progressbar"
                    aria-label="Active contracts" aria-valuenow="{{ $property->contracts->count() > 0 ? ($property->contracts->where('status', 'active')->count() / $property->contracts->count()) * 100 : 0 }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: {{ $property->contracts->count() > 0 ? ($property->contracts->where('status', 'active')->count() / $property->contracts->count()) * 100 : 0 }}%">
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end col -->

    <div class="col-md-6 col-xxl-3" id="utilitiesCard">
        <div class="card border">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div
                        class="flex-shrink-0 avatar-md bg-info-subtle d-inline-flex align-items-center justify-content-center rounded-2">
                        <i class="ti ti-bolt fs-18 text-info"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span data-toggler="off">
                            <a href="#" data-toggler-on="" class="d-none">
                                <i class="ti ti-star-filled text-warning fs-16"></i>
                            </a>
                            <a href="#" data-toggler-off="">
                                <i class="ti ti-star-filled text-muted fs-16"></i>
                            </a>
                        </span>
                        <div class="dropdown flex-shrink-0 text-muted">
                            <a href="#" class="dropdown-toggle drop-arrow-none fs-20 link-reset p-0"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#v-pills-utilities" class="dropdown-item" data-bs-toggle="tab" role="tab" aria-controls="v-pills-utilities">
                                    <i class="ti ti-list me-1"></i> View Utilities
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="ti ti-plus me-1"></i> Add Meter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-grow-1 mt-3">
                    <h5 class="mb-1"><a href="#v-pills-utilities" class="link-reset" data-bs-toggle="tab" role="tab" aria-controls="v-pills-utilities">Utilities & Meters</a></h5>
                    @php
                        $meterCount = 0;
                        foreach($property->rooms as $room) {
                            $meterCount += $room->meters->count();
                        }
                    @endphp
                    <p class="text-muted mb-0">{{ $meterCount }} Total Meters</p>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3 mb-1">
                    @php
                        $pendingReadingCount = 0;
                        foreach($property->rooms as $room) {
                            if($room->meters->count() > 0) {
                                if(isset($room->getMeterStatusAttribute()['class']) && 
                                  ($room->getMeterStatusAttribute()['class'] == 'warning' || 
                                   $room->getMeterStatusAttribute()['class'] == 'danger')) {
                                    $pendingReadingCount++;
                                }
                            }
                        }
                    @endphp
                    <p class="fs-14 mb-0">{{ $pendingReadingCount }} Need Reading</p>
                    <p class="fs-14 mb-0">{{ $property->utilityRates->count() }} Utility Rates</p>
                </div>
                <div class="progress progress-sm bg-info-subtle" role="progressbar"
                    aria-label="Meters readings" aria-valuenow="{{ $property->rooms->count() > 0 ? (($property->rooms->count() - $pendingReadingCount) / $property->rooms->count()) * 100 : 0 }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: {{ $property->rooms->count() > 0 ? (($property->rooms->count() - $pendingReadingCount) / $property->rooms->count()) * 100 : 0 }}%">
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end col -->

    <div class="col-md-6 col-xxl-3" id="propertyInfoCard">
        <div class="card border">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div
                        class="flex-shrink-0 avatar-md bg-secondary-subtle d-inline-flex align-items-center justify-content-center rounded-2">
                        <i class="ti ti-building-estate fs-18 text-secondary"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span data-toggler="on">
                            <a href="#" data-toggler-on="">
                                <i class="ti ti-star-filled text-warning fs-16"></i>
                            </a>
                            <a href="#" data-toggler-off="" class="d-none">
                                <i class="ti ti-star-filled text-muted fs-16"></i>
                            </a>
                        </span>
                        <div class="dropdown flex-shrink-0 text-muted">
                            <a href="#" class="dropdown-toggle drop-arrow-none fs-20 link-reset p-0"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('landlord.properties.edit', $property->id) }}" class="dropdown-item">
                                    <i class="ti ti-edit me-1"></i> Edit Property
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="ti ti-photo me-1"></i> Change Cover Image
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-grow-1 mt-3">
                    <h5 class="mb-1"><a href="#!" class="link-reset">Property Details</a></h5>
                    <p class="text-muted mb-0">{{ $property->property_type }}</p>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3 mb-1">
                    <p class="fs-14 mb-0">{{ $property->city }}, {{ $property->state_province }}</p>
                    <p class="fs-14 mb-0">{{ $property->year_built ? 'Built: ' . $property->year_built : 'No year data' }}</p>
                </div>
                <div class="progress progress-sm bg-secondary-subtle" role="progressbar"
                    aria-label="Property status" aria-valuenow="{{ $property->status == 'active' ? 100 : 0 }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-secondary"
                        style="width: {{ $property->status == 'active' ? 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div><!-- end col -->
</div>

<div class="px-3 d-flex align-items-center justify-content-between mb-3 mt-4">
    <h4 class="header-title">Recent Activity</h4>
    <a href="#v-pills-contracts" class="link-reset fw-semibold text-decoration-underline link-offset-2" data-bs-toggle="tab" role="tab" aria-controls="v-pills-contracts">View All
        <i class="ti ti-arrow-right"></i></a>
</div>

<div class="table-responsive">
    <table class="table table-centered table-nowrap border-top mb-0">
        <thead class="bg-light bg-opacity-25">
            <tr>
                <th class="ps-3">Room</th>
                <th>Tenant</th>
                <th>Status</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th style="width: 80px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($property->contracts->sortByDesc('created_at')->take(5) as $contract)
            <tr>
                <td class="ps-3">
                    <div class="d-flex align-items-center gap-2">
                        <div
                            class="flex-shrink-0 avatar-md {{ $contract->room->status == 'occupied' ? 'bg-success-subtle' : 'bg-warning-subtle' }} d-inline-flex align-items-center justify-content-center rounded-2">
                            <i class="ti ti-door fs-22 {{ $contract->room->status == 'occupied' ? 'text-success' : 'text-warning' }}"></i>
                        </div>
                        <div>
                            <span class="fw-semibold">
                                <a href="{{ route('landlord.rooms.show', $contract->room->id) }}" class="text-reset">
                                    {{ $contract->room->room_number }}
                                </a>
                            </span>
                            <p class="mb-0 fs-12">{{ $contract->room->roomType ? $contract->room->roomType->name : 'No type' }}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div>
                            <a href="javascript: void(0);">
                                <img src="{{ $contract->tenant->image ? asset($contract->tenant->image) : asset('assets/images/avatar-1.jpg') }}" class="rounded-circle avatar-md"
                                    alt="tenant">
                            </a>
                        </div>
                        <div>
                            <p class="mb-0 text-dark fw-medium">{{ $contract->tenant->name }}</p>
                            <span class="fs-12">{{ $contract->tenant->email }}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge {{ $contract->status == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                        {{ ucfirst($contract->status) }}
                    </span>
                </td>
                <td>
                    {{ $contract->start_date->format('M d, Y') }}
                </td>
                <td>
                    {{ $contract->end_date->format('M d, Y') }}
                </td>
                <td>
                    <div class="dropdown flex-shrink-0 text-muted">
                        <a href="#" class="dropdown-toggle drop-arrow-none fs-20 link-reset" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('landlord.contracts.show', $contract->id) }}" class="dropdown-item">
                                <i class="ti ti-eye me-1"></i> View Contract
                            </a>
                            <a href="{{ route('landlord.contracts.edit', $contract->id) }}" class="dropdown-item">
                                <i class="ti ti-edit me-1"></i> Edit Contract
                            </a>
                            <a href="{{ route('landlord.payments.index', ['contract_id' => $contract->id]) }}" class="dropdown-item">
                                <i class="ti ti-receipt me-1"></i> View Invoices
                            </a>
                            @if($contract->status == 'active')
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item text-danger">
                                <i class="ti ti-x me-1"></i> Terminate Contract
                            </a>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                        <i class="ti ti-file-off fs-24 text-muted mb-2"></i>
                        <p class="text-muted mb-2">No contracts found for this property</p>
                        <a href="{{ route('landlord.contracts.create') }}" class="btn btn-sm btn-primary">
                            <i class="ti ti-plus me-1"></i> Create New Contract
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>