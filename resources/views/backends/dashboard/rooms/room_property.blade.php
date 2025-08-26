@extends('backends.layouts.app')

@section('title', 'Rooms | RoomGate')


@push('style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/mermaid.min.css">
    <link href="{{ asset('assets') }}/css/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.core.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.snow.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/classic.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/monolith.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/nano.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="page-container">
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Rooms Table</h4>
            </div>
            <div class="text-end">
                @if (Auth::check() && Auth::user()->hasRole('landlord'))
                    <a class="btn btn-primary" data-bs-toggle="modal" href="#createModal" role="button">
                        <i class="ti ti-plus me-1"></i>Add Room
                    </a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">

                <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-0 text-center align-items-center justify-content-center">

                    <div class="col border-end border-light border-dashed">
                        <div class="mt-3 mt-md-0 p-3">
                            <h5 class="text-muted fs-13 text-uppercase" title="Number of Rooms">No. of Rooms</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-secondary-subtle text-secondary rounded-circle fs-22">
                                        <iconify-icon icon="solar:home-2-bold-duotone"></iconify-icon>
                                    </span>
                                </div>
                                <h3 class="mb-0 fw-bold">{{ $rooms->count() }}</h3>
                            </div>
                            <p class="mb-0 text-muted">
                                <span class="text-nowrap">Total rooms in this property</span>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-tabs nav-bordered" role="tablist">
                            <li class="nav-item px-3" role="presentation">
                                <a href="#table" data-bs-toggle="tab" aria-expanded="false" class="nav-link py-2 active"
                                    aria-selected="false" role="tab" tabindex="-1">
                                    <span class="d-block d-sm-none"><iconify-icon icon="solar:notebook-bold"
                                            class="fs-20"></iconify-icon></span>
                                    <span class="d-none d-sm-block"><iconify-icon icon="solar:notebook-bold"
                                            class="fs-14 me-1 align-middle"></iconify-icon> Table</span>
                                </a>
                            </li>
                            <li class="nav-item px-3" role="presentation">
                                <a href="#element" data-bs-toggle="tab" aria-expanded="true" class="nav-link py-2"
                                    aria-selected="true" role="tab">
                                    <span class="d-block d-sm-none"><iconify-icon icon="solar:chat-dots-bold"
                                            class="fs-20"></iconify-icon></span>
                                    <span class="d-none d-sm-block"><iconify-icon icon="solar:chat-dots-bold"
                                            class="fs-14 me-1 align-middle"></iconify-icon> Element</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="table" role="tabpanel">

                                <div class="row">
                                    <div id="table-gridjs"></div>
                                </div>

                            </div>
                            <div class="tab-pane" id="element" role="tabpanel">
                                <div class="row g-2">
                                    <div class="row">

                                        @forelse ($rooms as $room)
                                            <div class="col-lg-4 col-md-6">
                                                <div class="card h-100">

                                                    <div class="card-body mt-5">
                                                        {{-- Room Number --}}
                                                        <h5 class="text-primary fw-medium">Room Number :
                                                            {{ $room->room_number }}
                                                        </h5>

                                                        <div>
                                                            {{-- Property Name (assumes a 'property' relationship) --}}
                                                            <a href="#!"
                                                                class="fw-semibold fs-16 text-dark">{{ $room->property->name ?? 'N/A' }}</a>
                                                        </div>

                                                        <hr class="my-3">

                                                        <div class="border border-dashed p-2 rounded text-center">
                                                            <div class="row">
                                                                <div class="col-lg-6 col-4 border-end">
                                                                    <p class="text-muted fw-medium fs-14 mb-0"><span
                                                                            class="text-dark">Size : </span> {{ $room->size }}m&sup2;</p>
                                                                </div>
                                                                <div class="col-lg-6 col-4 border-end">
                                                                    <p class="text-muted fw-medium fs-14 mb-0"><span
                                                                            class="text-dark">Floor : </span> {{ $room->floor }}</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <hr class="my-3">

                                                        {{-- SPLIT AMENITIES SECTION --}}
                                                        <div class="row">
                                                            {{-- 1. Amenities from Room Type --}}
                                                            <div class="col-6 border-end">
                                                                <h6 class="text-muted text-uppercase fs-12 mb-2">Included
                                                                    Amenities</h6>
                                                                @if (optional($room->roomType)->amenities->isNotEmpty())
                                                                    <ul class="list-unstyled mb-0">
                                                                        @foreach ($room->roomType->amenities as $amenity)
                                                                            <li class="mb-1"><i
                                                                                    class="ti ti-check text-success me-1"></i>
                                                                                {{ $amenity->name }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <p class="fs-13 text-muted mb-0">None</p>
                                                                @endif
                                                            </div>

                                                            {{-- 2. Add-on Amenities (Directly on Room) --}}
                                                            <div class="col-6">
                                                                <h6 class="text-muted text-uppercase fs-12 mb-2">Add-on
                                                                    Amenities</h6>
                                                                @php
                                                                    // Get IDs of amenities from the room type to avoid showing them twice
                                                                    $roomTypeAmenityIds =
                                                                        optional($room->roomType)->amenities->pluck(
                                                                            'id',
                                                                        ) ?? collect();
                                                                    // Filter direct room amenities to only show true "add-ons"
                                                                    $addOnAmenities = $room->amenities->whereNotIn(
                                                                        'id',
                                                                        $roomTypeAmenityIds,
                                                                    );
                                                                @endphp

                                                                @if ($addOnAmenities->isNotEmpty())
                                                                    <ul class="list-unstyled mb-0">
                                                                        @foreach ($addOnAmenities as $amenity)
                                                                            <li class="mb-1"><i class="ti ti-plus text-info me-1"></i>
                                                                                {{ $amenity->name }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <p class="fs-13 text-muted mb-0">None</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="card-footer d-flex flex-wrap align-items-center justify-content-between border-top border-dashed">
                                                        {{-- Room Price --}}
                                                        <h4
                                                            class="fw-semibold text-danger d-flex align-items-center gap-2 mb-0">
                                                            {!! format_money_with_currency($room->price ?? 0) !!}
                                                        </h4>
                                                    </div>

                                                    {{-- Favorite Button --}}
                                                    <span class="position-absolute top-0 end-0 p-2">
                                                        <div data-toggler="on">
                                                            <button type="button" class="btn btn-icon btn-light rounded-circle"
                                                                data-toggler-on="">
                                                                <iconify-icon icon="solar:eye-bold-duotone"
                                                                    class="fs-22 text-info"></iconify-icon>
                                                            </button>
                                                        </div>
                                                    </span>

                                                    {{-- Status Badge --}}
                                                    <span class="position-absolute top-0 start-0 p-2">
                                                        @if ($room->status == 'available')
                                                            <span class="badge bg-success fs-11">Available</span>
                                                        @elseif ($room->status == 'occupied')
                                                            <span class="badge bg-danger fs-11">Occupied</span>
                                                        @else
                                                            <span class="badge bg-warning fs-11">{{ ucfirst($room->status) }}</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <p class="text-center text-muted mt-4">No rooms available to display.</p>
                                            </div>
                                        @endforelse


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @if (Auth::check() && Auth::user()->hasRole('landlord'))
    @endif
@endsection

@push('script')

@endpush