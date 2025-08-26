@extends('backends.layouts.app')

@section('title', 'Utilities | RoomGate')

@push('style')
    {{-- Styles remain the same --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/mermaid.min.css">
    <link href="{{ asset('assets') }}/css/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.core.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.snow.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="page-container">
        {{-- Page Title --}}
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">{{ __('messages.table') }} Utility Type</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">Utility Type</li>
                </ol>
            </div>
        </div>

        {{-- Main Content --}}
        @if (Auth::check() && Auth::user()->hasRole('admin'))
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom border-dashed">
                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                <h4 class="header-title">Utility Type</h4>
                                @if (Auth::check() && Auth::user()->hasRole('admin'))
                                    <a class="btn btn-primary" data-bs-toggle="modal" href="#createModal" role="button">
                                        <i class="ti ti-plus me-1"></i>{{ __('messages.create') }} Utility Type
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="table-gridjs"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom border-dashed">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <h4 class="header-title">Pending Tasks</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger mb-0">
                            <strong>Task 1:</strong> The ability to view contract details is not yet active.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @if (Auth::check() && Auth::user()->hasRole('admin'))
        @include('backends.dashboard.utilities.create')
        @include('backends.dashboard.utilities.edit')
    @endif
@endsection

@push('script')
    {{-- Scripts remain the same --}}
    <script src="{{ asset('assets') }}/js/gridjs.umd.js"></script>
    <script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>
    <script src="{{ asset('assets') }}/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const utilityTypesData = {!! json_encode(
                $utilityTypes->map(function ($utilityType, $key) {
                        $utilityTypeDataForJs = [
                            'id' => $utilityType->id,
                            'name' => $utilityType->name,
                            'unit_of_measure' => $utilityType->unit_of_measure,
                            'billing_type' => $utilityType->billing_type,
                            'destroy_url' => route('admin.utility_types.destroy', $utilityType->id),
                            'edit_url' => route('admin.utility_types.update', $utilityType->id),
                            'view_url' => route('admin.utility_types.show', $utilityType->id),
                        ];
                        return [
                            $key + 1,
                            $utilityTypeDataForJs['name'],
                            $utilityTypeDataForJs['unit_of_measure'],
                            $utilityTypeDataForJs['billing_type'],
                            $utilityTypeDataForJs,
                        ];
                    })->values()->all(),
            ) !!};

            if (typeof utilityTypesData !== 'undefined' && Array.isArray(utilityTypesData)) {
                if (utilityTypesData.length === 0) {
                    document.getElementById("table-gridjs").innerHTML =
                        '<div class="alert alert-info text-center">No utility type found.</div>';
                } else {
                    new gridjs.Grid({
                        columns: [{
                            name: "#",
                            width: "50px"
                        }, {
                            name: "{{ __('messages.name') }}",
                            width: "200px"
                        }, {
                            name: "Unit of Measure",
                            width: "150px"
                        }, {
                            name: "Billing Type",
                            width: "150px"
                        }, {
                            name: "{{ __('messages.action') }}",
                            width: "150px",
                            sort: false,
                            formatter: (_, row) => {
                                const actionData = row.cells[4].data;
                                const deleteButtonHtml = `
                                                <button data-destroy-url="${actionData.destroy_url}"
                                                        data-utility-type-name="${actionData.name}"
                                                        type="button"
                                                        class="btn btn-soft-danger btn-icon btn-sm rounded-circle delete-utilityType"
                                                        title="Delete"><i class="ti ti-trash"></i></button>`;

                                const editButtonHtml = `
                                                <button class="btn btn-soft-success btn-icon btn-sm rounded-circle edit-utilityType-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal"
                                                        data-utility-type-data='${JSON.stringify(actionData)}'
                                                        role="button"
                                                        title="Edit"><i class="ti ti-edit fs-16"></i></button>`;

                                return gridjs.html(`
                                                <div class="hstack gap-1 justify-content-end">
                                                    <a href="${actionData.view_url}" class="btn btn-soft-primary btn-icon btn-sm rounded-circle" title="View Details"><i class="ti ti-eye"></i></a>
                                                    @if (Auth::check() && Auth::user()->hasRole('admin'))
                                                        ${editButtonHtml}
                                                    @endif
                                                    ${deleteButtonHtml}
                                                </div>`);
                            }
                        }],
                        pagination: {
                            limit: 10,
                            summary: true
                        },
                        sort: true,
                        search: true,
                        data: utilityTypesData,
                        style: {
                            table: {
                                'font-size': '0.85rem'
                            }
                        }
                    }).render(document.getElementById("table-gridjs"));
                }
            } else {
                console.error("Grid.js Error: utilityTypesData is missing or not a valid array.");
                document.getElementById("table-gridjs").innerHTML =
                    '<div class="alert alert-danger">Could not load utility type data.</div>';
            }

            document.addEventListener('click', function(e) {
                const deleteButton = e.target.closest('.delete-utilityType');
                if (deleteButton) {
                    const utilityTypeName = deleteButton.getAttribute('data-utility-type-name') ||
                        'this utility type';
                    const actionUrl = deleteButton.getAttribute('data-destroy-url');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')
                        .getAttribute('content');

                    Swal.fire({
                        title: "Are you sure?",
                        text: `Utility Type "${utilityTypeName}" will be permanently deleted! This action cannot be undone.`,
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
                            form.innerHTML = `
                                            <input type="hidden" name="_token" value="${csrfToken}">
                                            <input type="hidden" name="_method" value="DELETE">
                                        `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }

                const editButton = e.target.closest('.edit-utilityType-btn');
                if (editButton) {
                    const modal = $('#editModal');
                    const utilityTypeData = JSON.parse(editButton.dataset.utilityTypeData);

                    modal.find('#editName').val(utilityTypeData.name);
                    modal.find('#editUnitOfMeasure').val(utilityTypeData.unit_of_measure);
                    modal.find('#editBillingType').val(utilityTypeData.billing_type).trigger(
                        'change');

                    modal.find('#editUtilityTypeForm').attr('action', utilityTypeData.edit_url);
                }
            });

            $(function() {
                $('#createModal .select2').select2({
                    dropdownParent: $('#createModal')
                });
                $('#editModal .select2').select2({
                    dropdownParent: $('#editModal')
                });
            });


        });
    </script>
@endpush
