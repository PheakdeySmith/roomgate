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
            <h4 class="header-title mb-0 text-truncate">All Contracts</h4>
        </div>
    </div>

    {{-- Column for Buttons, aligned to the end on medium screens and up --}}
    <div class="col-md-6">
        <div class="d-flex justify-content-md-end gap-2">
            @if (Auth::check() && Auth::user()->hasRole('landlord'))
                {{-- "Add Contract" button with text that hides on very small screens --}}
                <a href="{{ route('landlord.contracts.create') }}" class="btn btn-primary btn-sm d-inline-flex align-items-center">
                    <i class="ti ti-plus"></i>
                    <span class="d-none d-sm-inline ms-1">Add Contract</span>
                </a>
            @endif
            {{-- "View All" link is now always visible --}}
            <a href="{{ route('landlord.contracts.index') }}" class="btn btn-light btn-sm d-inline-flex align-items-center">
                <span>View All</span>
                <i class="ti ti-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="table-responsive">
        <table class="table table-hover text-nowrap mb-0">
            <thead class="bg-dark-subtle">
                <tr>
                    <th class="ps-3">Tenant</th>
                    <th>Room Number</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th class="text-center" style="width: 120px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($property->contracts as $contract)
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex justify-content-start align-items-center gap-3">
                                <div class="avatar-sm">
                                    <img src="{{ $contract->tenant->image ? asset($contract->tenant->image) : asset('assets/images/default_image.png') }}"
                                        alt="tenant image" class="rounded-circle avatar-md">
                                </div>
                                <span class="fw-semibold">{{ $contract->tenant->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td>{{ $contract->room->room_number ?? 'N/A' }}</td>
                        <td>{{ $contract->start_date ? $contract->start_date->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ $contract->end_date ? $contract->end_date->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            @if ($contract->status == 'active')
                                <span class="badge bg-success-subtle text-success fs-12 p-1">Active</span>
                            @elseif ($contract->status == 'expired')
                                <span class="badge bg-danger-subtle text-danger fs-12 p-1">Expired</span>
                            @else
                                <span
                                    class="badge bg-secondary-subtle text-secondary fs-12 p-1">{{ ucfirst($contract->status) }}</span>
                            @endif
                        </td>
                        <td class="pe-3">
                            <div class="hstack gap-1 justify-content-end">
                                <a href="#" class="btn btn-soft-primary btn-icon btn-sm rounded-circle">
                                    <i class="ti ti-eye"></i></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <p class="mb-0">No contracts found for this property.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


</div>