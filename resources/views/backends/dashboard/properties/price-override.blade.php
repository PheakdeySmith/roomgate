@extends('backends.layouts.app')

@section('title', 'Seasional Prices | RoomGate')

@push('style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/mermaid.min.css">
    <link href="{{ asset('assets') }}/css/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.core.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.snow.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/classic.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/monolith.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/nano.min.css" rel="stylesheet" type="text/css">
    <style>
        .color-swatch {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .color-swatch.active {
            border: 2px solid var(--bs-primary);
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }
    </style>
@endpush

@section('content')
    <div class="page-container">
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Seasonal Price</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('landlord.properties.index') }}">Properties</a></li>
                    <li class="breadcrumb-item"> <a href="{{ route('landlord.properties.createPrice', ['property' => $property->id]) }}">Manage Prices</a></li>
                    <li class="breadcrumb-item active">Seasonal Price</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-tabs nav-bordered" role="tablist">
                            <li class="nav-item px-3" role="presentation">
                                <a href="#table" data-bs-toggle="tab" aria-expanded="false" class="nav-link py-2"
                                    aria-selected="false" role="tab" tabindex="-1">
                                    <span class="d-none d-sm-block"><iconify-icon icon="solar:notebook-bold"
                                            class="fs-14 me-1 align-middle"></iconify-icon> Table</span>
                                </a>
                            </li>
                            <li class="nav-item px-3" role="presentation">
                                <a href="#element" data-bs-toggle="tab" aria-expanded="true" class="nav-link py-2 active"
                                    aria-selected="true" role="tab">
                                    <span class="d-none d-sm-block"><iconify-icon icon="solar:chat-dots-bold"
                                            class="fs-14 me-1 align-middle"></iconify-icon> Calendar</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane" id="table" role="tabpanel">
                                <div class="row">
                                    <div id="table-gridjs"></div>
                                </div>
                            </div>
                            <div class="tab-pane active show" id="element" role="tabpanel">
                                <div class="row">
                                    <div class="col-xl-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <button class="btn btn-primary w-100" id="btn-new-event">
                                                    <i class="ti ti-plus me-2 align-middle"></i> Set New Price
                                                </button>
                                                <div id="external-events" class="mt-2">
                                                    <p class="text-muted">Drag and drop your event or click in the calendar
                                                    </p>
                                                    <div class="external-event fc-event bg-success-subtle text-success"
                                                        data-class="bg-success-subtle">
                                                        <i class="ti ti-circle-filled me-2"></i>Khmer New Year
                                                    </div>
                                                    <div class="external-event fc-event bg-info-subtle text-info"
                                                        data-class="bg-info-subtle">
                                                        <i class="ti ti-circle-filled me-2"></i>Pchum Ben
                                                    </div>
                                                    <div class="external-event fc-event bg-warning-subtle text-warning"
                                                        data-class="bg-warning-subtle">
                                                        <i class="ti ti-circle-filled me-2"></i>Water Festival
                                                    </div>
                                                    <div class="external-event fc-event bg-danger-subtle text-danger"
                                                        data-class="bg-danger-subtle">
                                                        <i class="ti ti-circle-filled me-2"></i>Royal Ploughing Ceremony
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-9">
                                        <div class="card">
                                            <div class="card-body">
                                                <div id="calendar"
                                                    data-events='@json($events)'
                                                    data-store-url='{{ route('landlord.properties.roomTypes.overrides.store', [$property, $roomType]) }}'
                                                    data-update-url-template='{{ route('landlord.properties.roomTypes.overrides.update', [$property, $roomType, 'OVERRIDE_ID']) }}'
                                                    data-delete-url-template='{{ route('landlord.properties.roomTypes.overrides.destroy', [$property, $roomType, 'OVERRIDE_ID']) }}'
                                                    data-csrf-token='{{ csrf_token() }}'
                                                    data-swal-image-success="{{ asset('assets/images/small-5.jpg') }}"
                                                    data-swal-image-error="{{ asset('assets/images/small-4.jpg') }}"
                                                    class="fc fc-media-screen fc-direction-ltr fc-theme-standard"
                                                    style="height: 758px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="override-modal" tabindex="-1" style="display: none;"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form class="needs-validation" name="override-form" id="override-event"
                                                novalidate="">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="modal-title">
                                                        New Price Override Event
                                                    </h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="mb-2">
                                                                <label class="control-label form-label"
                                                                    for="title">Title</label>
                                                                <input class="form-control" type="text" name="title"
                                                                    id="title" required="">
                                                                <div class="invalid-feedback">Please provide a valid title.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="control-label form-label" for="price">Override
                                                                    Price ($)</label>
                                                                <input class="form-control" type="number" step="0.01"
                                                                    name="price" id="price" required="">
                                                                <div class="invalid-feedback">Please provide a valid price.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="mb-2">
                                                            <label for="start_date" class="form-label">Start Date</label>
                                                            <input type="text" class="form-control flatpickr-input"
                                                                id="start_date" name="start_date" data-provider="flatpickr"
                                                                data-date-format="Y-m-d" readonly="readonly"
                                                                data-sharkid="__1" required="">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="mb-2">
                                                            <label for="end_date" class="form-label">End Date</label>
                                                            <input type="text" class="form-control flatpickr-input"
                                                                id="end_date" name="end_date" data-provider="flatpickr"
                                                                data-date-format="Y-m-d" readonly="readonly"
                                                                data-sharkid="__1" required="">
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Pick Color</label>
                                                        <div class="d-flex gap-2 mb-2">
                                                            <div class="color-swatch bg-info-subtle active"
                                                                data-color="bg-info-subtle"></div>
                                                            <div class="color-swatch bg-primary-subtle"
                                                                data-color="bg-primary-subtle"></div>
                                                            <div class="color-swatch bg-warning-subtle"
                                                                data-color="bg-warning-subtle"></div>
                                                            <div class="color-swatch bg-danger-subtle"
                                                                data-color="bg-danger-subtle"></div>
                                                            <div class="color-swatch bg-success-subtle"
                                                                data-color="bg-success-subtle"></div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                                        <button type="button" class="btn btn-danger" id="btn-delete-event"
                                                            style="display: none;">
                                                            Delete
                                                        </button>
                                                        <button type="button" class="btn btn-light ms-auto"
                                                            data-bs-dismiss="modal">
                                                            Close
                                                        </button>
                                                        <button type="submit" class="btn btn-primary" id="btn-save-event">
                                                            Save
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets') }}/js/gridjs.umd.js"></script>
    <script src="{{ asset('assets') }}/js/index.global.min.js"></script>

    <script src="{{ asset('assets/js/seasonal-price-calendar.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const overridesData = {!! json_encode($overridesForTable->values()->all()) !!};
            if (document.getElementById("table-gridjs")) {
                new gridjs.Grid({
                    columns: [
                        { name: "#", width: "50px" },
                        { name: "ID", width: "80px" },
                        { name: "Title", width: "250px" },
                        { name: "Price", width: "120px" },
                        { name: "Start Date", width: "150px" },
                        { name: "End Date", width: "150px" },
                        {
                            name: "Color",
                            width: "150px",
                            formatter: (_, row) => {
                                const className = row.cells[6].data;
                                return gridjs.html(`<span class="badge ${className} text-dark">${className}</span>`);
                            }
                        },
                        {
                            name: "Action",
                            width: "100px",
                            sort: false,
                            formatter: (_, row) => {
                                const actionData = row.cells[7].data;
                                const destroyUrl = actionData.destroy_url;
                                const overrideName = actionData.override_name;
                                let deleteButtonHtml = destroyUrl ?
                                    `<button
                                        data-override-name="${overrideName}"
                                        data-action-url="${destroyUrl}"
                                        type="button"
                                        class="btn btn-soft-danger btn-icon btn-sm rounded-circle delete-override"
                                        title="Delete"><i class="ti ti-trash"></i></button>` : '';
                                return gridjs.html(`<div class="hstack gap-1 justify-content-center">${deleteButtonHtml}</div>`);
                            },
                        }
                    ],
                    pagination: { limit: 10, summary: true },
                    sort: true,
                    search: true,
                    data: overridesData,
                    style: { table: { 'font-size': '0.85rem' } },
                }).render(document.getElementById("table-gridjs"));
            }

            // Delete confirmation logic
            document.addEventListener('click', function (e) {
                if (e.target.closest('.delete-override')) {
                    const button = e.target.closest('.delete-override');
                    const overrideName = button.getAttribute('data-override-name') || 'this override';
                    const actionUrl = button.getAttribute('data-action-url');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    if (!actionUrl) {
                        Swal.fire('Error!', 'Delete action URL not found.', 'error');
                        return;
                    }

                    Swal.fire({
                        title: "Are you sure?",
                        text: `Property "${overrideName}" will be permanently deleted! This action cannot be undone.`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "No, cancel",
                        confirmButtonColor: "#d33",
                        customClass: {
                            confirmButton: "swal2-confirm btn btn-danger me-2 mt-2",
                            cancelButton: "swal2-cancel btn btn-secondary mt-2",
                        },
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = actionUrl;
                            form.innerHTML = `
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                            `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
@endpush
