@extends('backends.layouts.app')

@section('title', 'Properties | RoomGate')

@push('style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/mermaid.min.css">
    <link href="{{ asset('assets') }}/css/sweetalert2.min.css" rel="stylesheet" type="text/css">

    <link href="{{ asset('assets') }}/css/quill.core.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.snow.css" rel="stylesheet" type="text/css">

    {{-- Note: You have pickr themes (classic, monolith, nano) included twice. Remove duplicates. --}}
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
                        <i class="ti ti-plus me-1"></i>Add Property
                    </a>
                @endif
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
                                    <span class="d-block d-sm-none"><iconify-icon icon="solar:notebook-bold"
                                            class="fs-20"></iconify-icon></span>
                                    <span class="d-none d-sm-block"><iconify-icon icon="solar:notebook-bold"
                                            class="fs-14 me-1 align-middle"></iconify-icon> Table</span>
                                </a>
                            </li>
                            <li class="nav-item px-3" role="presentation">
                                <a href="#element" data-bs-toggle="tab" aria-expanded="true" class="nav-link py-2 active"
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
                            <div class="tab-pane" id="table" role="tabpanel">
                                <div class="row">
                                    <div id="table-gridjs"></div>
                                </div>
                            </div>
                            <div class="tab-pane active show" id="element" role="tabpanel">
                                <div class="row g-3">
                                    @forelse ($properties as $property)
                                        <div class="col-lg-4 col-md-6">
                                            <div class="card h-100 position-relative">

                                                <span class="position-absolute top-0 end-0 p-2">
                                                    <span
                                                        class="badge bg-{{ $property->status == 'active' ? 'success' : 'secondary' }} fs-11">{{ ucfirst($property->status) }}</span>
                                                </span>

                                                <div class="card-body">
                                                    <h5 class="text-primary fw-medium">{{ $property->name ?? 'N/A' }}</h5>
                                                    <p class="text-muted mb-2">Type: {{ $property->property_type ?? 'N/A' }}</p>

                                                    @php
                                                        $address_parts = array_filter([
                                                            $property->address_line_1,
                                                            $property->address_line_2,
                                                            $property->city,
                                                            $property->state_province,
                                                            $property->postal_code,
                                                            $property->country
                                                        ]);
                                                        $full_address = implode(', ', $address_parts);
                                                    @endphp
                                                    <p class="text-muted mb-0"><i
                                                            class="ti ti-map-pin me-1"></i>{{ $full_address }}</p>

                                                    <hr class="my-3">

                                                    <div class="border border-dashed p-2 rounded text-center mb-3">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <p class="text-muted fw-medium fs-14 mb-0">
                                                                    <span class="text-dark">Built : </span>
                                                                    {{ $property->year_built ?? 'N/A' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <h6 class="text-muted text-uppercase fs-12 mb-3">Available Room Types &
                                                            Prices</h6>

                                                        @forelse ($property->roomTypes as $roomType)
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <div>
                                                                    <p class="mb-0 fw-medium text-dark">{{ $roomType->name }}</p>
                                                                    <small class="text-muted">Effective:
                                                                        {{ \Carbon\Carbon::parse($roomType->pivot->effective_date)->format('d M, Y') }}</small>
                                                                </div>
                                                                <p class="mb-0 fw-semibold text-danger">
                                                                    {!! format_money($roomType->pivot->price) !!}
                                                                </p>
                                                            </div>
                                                        @empty
                                                            <div class="text-center py-2">
                                                                <p class="fs-13 text-muted mb-0">No room types and prices have been
                                                                    set for this property yet.</p>
                                                            </div>
                                                        @endforelse
                                                    </div>

                                                    <hr class="my-3">

                                                    <div>
                                                        <h6 class="text-muted text-uppercase fs-12 mb-3">Utility Rates
                                                        </h6>

                                                        @forelse ($property->utilityRates as $rate)
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <div>

                                                                    <p class="mb-0 fw-medium text-dark">
                                                                        {{ $rate->utilityType->name }}
                                                                    </p>
                                                                    <small class="text-muted">Effective:
                                                                        {{ $rate->effective_from->format('d M, Y') }}</small>
                                                                </div>

                                                                <p class="mb-0 fw-semibold text-danger">
                                                                    {!! format_money($rate->rate) !!}
                                                                    <small class="text-muted">/
                                                                        {{ $rate->utilityType->unit_of_measure }}</small>
                                                                </p>
                                                            </div>
                                                        @empty
                                                            <div class="text-center py-2">
                                                                <p class="fs-13 text-muted mb-0">No utility rates have
                                                                    been set for
                                                                    this property yet.</p>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>

                                                <div class="card-footer border-top border-dashed">
                                                    <div class="d-flex justify-content-end gap-2">

                                                        {{-- "Manage Prices" Button --}}
                                                        <a href="{{ route('landlord.properties.createPrice', ['property' => $property->id]) }}"
                                                            class="btn btn-sm btn-primary d-inline-flex align-items-center">
                                                            <i class="ti ti-tags"></i>
                                                            <span class="d-none d-md-inline ms-1">Manage Prices</span>
                                                        </a>

                                                        {{-- "Utility Rates" Button --}}
                                                        <a href="{{ route('landlord.properties.rates.index', ['property' => $property->id]) }}"
                                                            class="btn btn-sm btn-primary d-inline-flex align-items-center">
                                                            <i class="ti ti-bolt"></i>
                                                            <span class="d-none d-md-inline ms-1">Utility Rates</span>
                                                        </a>

                                                        {{-- "View Details" Button --}}
                                                        <a href="{{ route('landlord.properties.show', $property->id) }}"
                                                            class="btn btn-sm btn-light d-inline-flex align-items-center">
                                                            <i class="ti ti-eye"></i>
                                                            <span class="d-none d-md-inline ms-1">View Details</span>
                                                        </a>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="text-center text-muted mt-4">You have not created any properties yet.</p>
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

    @if (Auth::check() && Auth::user()->hasRole('landlord'))
        @include('backends.dashboard.properties.create')
        @include('backends.dashboard.properties.edit')
    @endif
@endsection

@push('script')
    <script src="{{ asset('assets') }}/js/gridjs.umd.js"></script>
    <script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>
    <script src="{{ asset('assets') }}/js/select2.min.js"></script>
    <script src="{{ asset('assets') }}/js/dropzone-min.js"></script>
    <script src="{{ asset('assets') }}/js/quill.min.js"></script>
    <script src="{{ asset('assets') }}/js/pickr.min.js"></script>
    <script src="{{ asset('assets') }}/js/ecommerce-add-products.js"></script>

    <script>
        const propertiesData = {!! json_encode(
        $properties->map(function ($property, $key) {
            $destroyUrl = '';
            $editUrl = '';
            $viewUrl = '';

            $propertyImage =
                $property->cover_image && is_string($property->cover_image)
                ? asset($property->cover_image)
                : asset('assets/images/default_image.png');

            if (auth()->check()) {
                if (auth()->user()->hasRole('landlord')) {
                    if ($property->landlord_id === auth()->id()) {
                        $destroyUrl = $property->id ? route('landlord.properties.destroy', $property->id) : '';
                        $editUrl = $property->id ? route('landlord.properties.update', $property->id) : '';
                        $viewUrl = $property->id ? route('landlord.properties.show', $property->id) : '';
                    }
                }
            }

            return [
                $key + 1,
                $propertyImage,
                $property->name ?? 'N/A',
                $property->property_type ?? 'N/A',
                $property->description ?? 'N/A',
                $property->address_line_1 ?? 'N/A',
                $property->address_line_2 ?? 'N/A',
                $property->city ?? 'N/A',
                $property->state_province ?? 'N/A',
                $property->postal_code ?? 'N/A',
                $property->country ?? 'N/A',
                $property->year_built ?? 'N/A',
                $property->status ?? 'N/A',
                (object) [
                    'destroy_url' => $destroyUrl,
                    'edit_url' => $editUrl,
                    'property_view_url' => $viewUrl,
                    'actual_property_id' => $property->id,
                ],
            ];
        })->values()->all(),
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE,
    ) !!};

        const clearAndRenderGrid = (containerId, gridConfig) => {
            const container = document.getElementById(containerId);
            if (container) {
                container.innerHTML = "";
                new gridjs.Grid(gridConfig).render(container);
            }
        };

        clearAndRenderGrid("table-gridjs", {
            columns: [{
                name: "#",
                width: "50px"
            },
            {
                name: "Image",
                width: "80px",
                formatter: (_, row) => gridjs.html(
                    `<div class="avatar-sm"><img src="${row.cells[1].data}" alt="Prop" class="rounded" style="width:100%; height:100%; object-fit:cover;"></div>`
                )
            },
            {
                name: "Name",
                width: "200px"
            },
            {
                name: "Property Type",
                width: "150px"
            },
            {
                name: "Location",
                width: "250px",
                formatter: (_, row) => `${row.cells[5].data}, ${row.cells[7].data}`
            },
            {
                name: "Status",
                width: "100px",
                formatter: (_, row) => gridjs.html(
                    `<span class="badge badge-soft-${row.cells[12].data === 'active' ? 'success' : 'danger'}">${row.cells[12].data}</span>`
                )
            },
            {
                name: "Action",
                width: "150px",
                sort: false,
                formatter: (_, row) => {
                    const actionData = row._cells[13].data;

                    const destroyUrl = actionData?.destroy_url;
                    const editUrl = actionData?.edit_url;
                    const propertyViewUrl = actionData.property_view_url;

                    const id = row.cells[0].data;
                    const image = row.cells[1].data;
                    const name = row.cells[2].data;
                    const property_type = row.cells[3].data;
                    const description = row.cells[4].data;
                    const address_line_1 = row.cells[5].data;
                    const address_line_2 = row.cells[6].data;
                    const city = row.cells[7].data;
                    const state_province = row.cells[8].data;
                    const postal_code = row.cells[9].data;
                    const country = row.cells[10].data;
                    const year_built = row.cells[11].data;
                    const status = row.cells[12].data;

                    let deleteButtonHtml = destroyUrl ?
                        `<button
                                                data-property-id="${id}" data-property-name="${name}" data-action-url="${destroyUrl}" type="button" class="btn btn-soft-danger btn-icon btn-sm rounded-circle delete-property" title="Delete"><i class="ti ti-trash"></i></button>` :
                        '';

                    let editButtonHtml = editUrl ?
                        `<button class="btn btn-soft-success btn-icon btn-sm rounded-circle edit-property-btn" data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="${id}" data-image="${image}" data-name="${name}" data-property-type="${property_type}" data-description="${description}" data-address-line-1="${address_line_1}" data-address-line-2="${address_line_2}" data-city="${city}" data-state-province="${state_province}" data-postal-code="${postal_code}" data-country="${country}" data-year-built="${year_built}" data-status="${status}" data-edit-url="${editUrl}" role="button" title="Edit"><i class="ti ti-edit fs-16"></i></button>` :
                        '';

                    return gridjs.html(`
                                                    <div class="hstack gap-1 justify-content-end">
                                                        <a href="${propertyViewUrl}" class="btn btn-soft-primary btn-icon btn-sm rounded-circle" title="View Property"><i class="ti ti-eye"></i></a>
                                                        ${editButtonHtml}
                                                        ${deleteButtonHtml}
                                                    </div>`);
                }
            },


            {
                name: "Description",
                hidden: true
            },
            {
                name: "Address Line 1",
                hidden: true
            },
            {
                name: "Address Line 2",
                hidden: true
            },
            {
                name: "City",
                hidden: true
            },
            {
                name: "State/Province",
                hidden: true
            },
            {
                name: "Postal Code",
                hidden: true
            },
            {
                name: "Country",
                hidden: true
            },
            {
                name: "Year Built",
                hidden: true
            },
            ],
            pagination: {
                limit: 10,
                summary: true
            },
            sort: true,
            search: true,
            data: propertiesData,
            style: {
                table: {
                    'font-size': '0.85rem'
                }
            }
        });

        document.addEventListener('click', function (e) {
            if (e.target.closest('.delete-property')) {
                const button = e.target.closest('.delete-property');
                const propertyId = button.getAttribute('data-property-id');
                const propertyName = button.getAttribute('data-property-name') || 'this property';
                const actionUrl = button.getAttribute('data-action-url');

                if (!actionUrl) {
                    console.error('Delete action URL not found on the button.');
                    Swal.fire('Error!', 'Cannot proceed with deletion. Action URL is missing.', 'error');
                    return;
                }

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfMeta) {
                    console.error('CSRF token meta tag not found.');
                    Swal.fire('Error!', 'Cannot proceed: CSRF token not found.', 'error');
                    return;
                }
                const csrfToken = csrfMeta.getAttribute('content');

                Swal.fire({
                    title: "Are you sure?",
                    text: `Property "${propertyName}" will be permanently deleted! This action cannot be undone.`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel",
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    customClass: {
                        confirmButton: "swal2-confirm btn btn-danger me-2 mt-2",
                        cancelButton: "swal2-cancel btn btn-secondary mt-2",
                    },
                    buttonsStyling: false,
                    showCloseButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = actionUrl;

                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = csrfToken;

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';

                        form.appendChild(tokenInput);
                        form.appendChild(methodInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        });

        $(function () {
            $('#status, #property_type, #country').select2({
                dropdownParent: $('#createModal'),
                placeholder: "Select an option",
                allowClear: true
            });

            $('#edit_status, #edit_property_type, #edit_country').select2({
                dropdownParent: $('#editModal'),
                placeholder: "Select an option",
                allowClear: true
            });
        });

        $('body').on('click', '.edit-property-btn', function () {
            const button = $(this);
            const modal = $('#editModal');

            const id = button.data('id');
            const actionUrl = button.data('edit-url');
            const imageUrl = button.data('image');

            modal.find('#editPropertyId').val(id);
            modal.find('#editName').val(button.data('name'));
            modal.find('#editDescription').val(button.data('description'));
            modal.find('#editAddressLine1').val(button.data('address-line-1'));
            modal.find('#editAddressLine2').val(button.data('address-line-2'));
            modal.find('#editCity').val(button.data('city'));
            modal.find('#editStateProvince').val(button.data('state-province'));
            modal.find('#editPostalCode').val(button.data('postal-code'));
            modal.find('#editYearBuilt').val(button.data('year-built'));

            modal.find('#edit_property_type').val(button.data('property-type')).trigger('change');
            modal.find('#edit_country').val(button.data('country')).trigger('change');
            modal.find('#edit_status').val(button.data('status')).trigger('change');

            const imagePreview = modal.find('#editImagePreview');
            const existingImagePathField = modal.find('#editExistingImagePath');
            if (imageUrl && imageUrl !== '{{ asset('assets/images/default_image.png') }}') {
                imagePreview.attr('src', imageUrl).show();
                existingImagePathField.val(imageUrl);
            } else {
                imagePreview.attr('src', 'https://placehold.co/150x150/e9ecef/6c757d?text=No+Image').hide();
                existingImagePathField.val('');
            }
            modal.find('#editImage').val('');
            modal.find('#removeCurrentImage').prop('checked', false);

            modal.find('#editPropertyForm').attr('action', actionUrl);
        });

        $('#editImage').on('change', function () {
            const input = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#editImagePreview').attr('src', e.target.result).show();
                }
                reader.readAsDataURL(input.files[0]);
                $('#removeCurrentImage').prop('checked', false);
            }
        });

        $('#removeCurrentImage').on('change', function () {
            if ($(this).is(':checked')) {
                $('#editImagePreview').hide();
                $('#editImage').val('');
            } else {
                const existingImage = $('#editExistingImagePath').val();
                if (existingImage && !$('#editImage').val()) {
                    $('#editImagePreview').attr('src', existingImage).show();
                } else if (!$('#editImage').val()) {
                    $('#editImagePreview').hide();
                }
            }
        });
    </script>
@endpush