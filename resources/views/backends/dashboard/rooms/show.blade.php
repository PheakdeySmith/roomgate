@extends('backends.layouts.app')

@section('title', 'Room Details')

@section('content')
    <div class="page-container">
        {{-- Page Header --}}
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Room Details</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('landlord.rooms.index') }}">Rooms</a></li>
                    <li class="breadcrumb-item active">{{ $room->room_number }}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        {{-- Header with Room Number, Property, and Status --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h4 class="mb-0">Room {{ $room->room_number }}</h4>
                                <p class="text-muted mb-0">{{ $room->property->name }}</p>
                            </div>
                            <div>
                                @switch($room->status)
                                    @case('available')
                                        <span class="badge fs-12 bg-success-subtle text-success">Available</span>
                                    @break

                                    @case('occupied')
                                        <span class="badge fs-12 bg-danger-subtle text-danger">Occupied</span>
                                    @break

                                    @case('maintenance')
                                        <span class="badge fs-12 bg-warning-subtle text-warning">Maintenance</span>
                                    @break
                                @endswitch
                            </div>
                        </div>

                        {{-- Description --}}
                        <p class="text-muted">{{ $room->description ?? 'No description provided for this room.' }}</p>
                        <hr>

                        {{-- Icon-based Specifications --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-tag fs-4 text-primary"></i>
                                    <div>
                                        <p class="text-muted mb-0 small">Room Type</p>
                                        <h6 class="mb-0">{{ $room->roomType->name }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-ruler-2 fs-4 text-primary"></i>
                                    <div>
                                        <p class="text-muted mb-0 small">Size</p>
                                        <h6 class="mb-0">{{ $room->size ?? 'N/A' }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-layers-subtract fs-4 text-primary"></i>
                                    <div>
                                        <p class="text-muted mb-0 small">Floor</p>
                                        <h6 class="mb-0">{{ $room->floor ?? 'N/A' }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Contract & Tenant History</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tenant</th>
                                        <th>Contract Period</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($room->contracts as $contract)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {{-- FIX 1: Use the avatar_url accessor for the correct path and default image --}}
                                                    <img src="{{ $contract->tenant->image ? asset($contract->tenant->image) : asset('assets/images/default_image.png') }}"
                                                        alt="Avatar" class="rounded me-2"
                                                        style="width: 30px; height: 30px; object-fit: cover;">
                                                    <a
                                                        href="{{ route('landlord.contracts.show', $contract) }}">{{ $contract->tenant->name }}</a>
                                                </div>
                                            </td>
                                            <td>{{ $contract->start_date->format('M d, Y') }} -
                                                {{ $contract->end_date->format('M d, Y') }}</td>
                                            <td class="text-center">
                                                {{-- FIX 2: Use a @switch statement for colored status badges --}}
                                                @switch($contract->status)
                                                    @case('active')
                                                        <span class="badge bg-success-subtle text-success">Active</span>
                                                    @break

                                                    @case('expired')
                                                        <span class="badge bg-danger-subtle text-danger">Expired</span>
                                                    @break

                                                    @case('terminated')
                                                        <span class="badge bg-warning-subtle text-warning">Terminated</span>
                                                    @break

                                                    @default
                                                        <span
                                                            class="badge bg-secondary-subtle text-secondary">{{ ucfirst($contract->status) }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted p-4">No contract history found
                                                    for this room.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 80px;">
                        <div class="card">
                            <div class="card-body text-center">
                                @if ($activeContract)
                                    <img src="{{ $activeContract->tenant->image ? asset($activeContract->tenant->image) : asset('assets/images/default_image.png') }}"
                                        alt="Tenant Avatar" class="rounded mb-2"
                                        style="width: 100px; height: 100px; object-fit: cover;">
                                    <h5 class="mb-1">{{ $activeContract->tenant->name }}</h5>
                                    <p class="text-muted fs-14">Current Tenant</p>
                                    <a href="{{ route('landlord.contracts.show', $activeContract) }}"
                                        class="btn btn-sm btn-primary">View Contract</a>
                                @else
                                    <div class="p-3">
                                        <i class="ti ti-moon-stars fs-1 text-success"></i>
                                        <h5 class="mt-2">This Room is Available</h5>
                                        @if ($room->status == 'available')
                                            <a href="{{ route('landlord.contracts.create', ['room_id' => $room->id]) }}"
                                                class="btn btn-sm btn-success mt-2">Create New Contract</a>
                                        @else
                                            <p class="text-muted mb-0">Status: {{ ucfirst(string: $room->status) }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header border-bottom">
                                <h5 class="card-title mb-0">Default Pricing & Amenities</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-2">
                                    <tbody>
                                        <tr>
                                            <td>Base Rent</td>
                                            <td class="text-end fw-semibold">
                                                {{-- Check for a specific rent amount from an active contract first --}}
                                                @if (isset($room->activeContract->rent_amount))
                                                    {!! format_money($room->activeContract->rent_amount) !!}

                                                    {{-- Otherwise, check if the $basePrice object was found for this room --}}
                                                @elseif($basePrice)
                                                    {!! format_money($basePrice->price) !!}

                                                    {{-- If no contract or base price exists, show 0 --}}
                                                @else
                                                    {!! format_money(0) !!}
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr class="my-2">
                                <p class="text-muted mb-2">Included Amenities:</p>
                                <ul class="list-unstyled mb-0">
                                    @forelse ($room->amenities as $amenity)
                                        <li class="d-flex justify-content-between">
                                            <span>{{ $amenity->name }}</span>
                                            <span>+ {!! format_money($amenity->amenity_price) !!}</span>
                                        </li>
                                    @empty
                                        <li class="text-muted small">No special amenities included.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
