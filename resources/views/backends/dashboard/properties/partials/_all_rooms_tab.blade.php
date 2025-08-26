
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
            <h4 class="header-title mb-0 text-truncate">All Room</h4>
        </div>
    </div>

    {{-- Column for Buttons, aligned to the end on medium screens and up --}}
    <div class="col-md-6">
        <div class="d-flex justify-content-md-end gap-2">
            @if (Auth::check() && Auth::user()->hasRole('landlord'))
            <a class="btn btn-primary btn-sm d-inline-flex align-items-center" data-bs-toggle="modal" href="#createModal" role="button">
                <i class="ti ti-plus"></i>
                    <span class="d-none d-sm-inline ms-1">Add Room</span>
            </a>
        @endif
            {{-- "View All" link is now always visible --}}
            <a href="{{ route('landlord.rooms.index') }}" class="btn btn-light btn-sm d-inline-flex align-items-center">
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
                    <th class="ps-3">Room Number</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Monthly Rent</th>
                    <th>Last Updated</th>
                    <th class="text-center" style="width: 120px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rooms as $room)
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex justify-content-start align-items-center gap-3">
                                <div
                                    class="avatar-md bg-light-subtle d-inline-flex align-items-center justify-content-center rounded-2">
                                    <i class="ti ti-door fs-22 text-secondary"></i>
                                </div>
                                <span class="fw-semibold">{{ $room->room_number ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td>{{ $room->roomType->name ?? 'Uncategorized' }}</td>
                        <td>
                            @if ($room->status == 'available')
                                <span class="badge bg-success-subtle text-success fs-12 p-1">Available</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fs-12 p-1">Occupied</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $priceRecord = $basePrices[$room->room_type_id] ?? null;
                            @endphp

                            @if ($priceRecord)
                                {!! format_money($priceRecord->price) !!}
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </td>
                        {{-- To show the creation date, change updated_at to created_at --}}
                        <td>{{ $room->created_at->format('M d, Y') }}</td>
                        <td class="pe-3">
                            <div class="hstack gap-1 justify-content-end">
                                <a href="javascript:void(0);" class="btn btn-soft-primary btn-icon btn-sm rounded-circle">
                                    <i class="ti ti-eye"></i></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <p class="mb-0">No rooms have been added to this property yet.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@if (Auth::check() && Auth::user()->hasRole('landlord'))
    @include('backends.dashboard.rooms.create_room')
    @include('backends.dashboard.rooms.edit_room')
@endif


<script>
    document.addEventListener('DOMContentLoaded', function () {

        $(function () {
            $('#createModal #room_type_id').select2({
                dropdownParent: $('#createModal'),
                placeholder: "Select an option",
                allowClear: true
            });
        });


        const tabTriggers = document.querySelectorAll('#v-pills-tab a');
        tabTriggers.forEach(triggerEl => {
            triggerEl.addEventListener('shown.bs.tab', event => {
                localStorage.setItem('activePropertyTab', event.target.getAttribute('href'));
            });
        });

        const lastActiveTab = localStorage.getItem('activePropertyTab');
        if (lastActiveTab) {
            const tabToActivate = document.querySelector(`#v-pills-tab a[href="${lastActiveTab}"]`);
            if (tabToActivate) {
                const tab = new bootstrap.Tab(tabToActivate);
                tab.show();
            }
        }
    });

</script>