

@extends('backends.layouts.app')

@section('title', 'Benefit | RoomGate')

@push('style')
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
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Benefit Section</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Benefit Section</li>
                </ol>
            </div>
        </div>

        @if (Auth::check() && Auth::user()->hasRole('admin'))
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom border-dashed">
                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                <h4 class="header-title">Benefit Content</h4>
                                <a class="btn btn-primary" data-bs-toggle="modal" href="#createBenefitModal" role="button">
                                    <i class="ti ti-plus me-1"></i>Add Benefit
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="table-benefit-gridjs"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- Modals --}}
    @if (Auth::check() && Auth::user()->hasRole('admin'))
        @include('backends.dashboard.admin.frontend.benefit.create')
        @include('backends.dashboard.admin.frontend.benefit.edit')
    @endif
@endsection

@push('script')
    <script src="{{ asset('assets') }}/js/gridjs.umd.js"></script>
    <script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>
    <script src="{{ asset('assets') }}/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const benefitData = {!! json_encode(
                $benefits->map(function ($benefit, $key) {
                    $benefitDataForJs = [
                        'id' => $benefit->id,
                        'icon_path' => $benefit->icon_path ? asset($benefit->icon_path) : null,
                        'title' => $benefit->title,
                        'description' => Str::limit(strip_tags($benefit->description), 50),
                        'order' => $benefit->order,
                        'destroy_url' => route('admin.benefit.destroy', $benefit->id),
                        'update_url' => route('admin.benefit.update', $benefit->id),
                    ];
                    return [
                        $key + 1,
                        $benefitDataForJs['icon_path'],
                        $benefitDataForJs['title'],
                        $benefitDataForJs['description'],
                        $benefitDataForJs['order'],
                        $benefitDataForJs,
                    ];
                })->values()->all(),
            ) !!};

            if (typeof benefitData !== 'undefined' && Array.isArray(benefitData)) {
                if (benefitData.length === 0) {
                    document.getElementById("table-benefit-gridjs").innerHTML =
                        '<div class="alert alert-info text-center">No benefit found.</div>';
                } else {
                    new gridjs.Grid({
                        columns: [
                            { name: "#", width: "50px" },
                            { name: "Icon", width: "80px", formatter: (cell) => cell ? gridjs.html(`<img src="${cell}" alt="Icon" style="max-width:40px;max-height:40px;">`) : '' },
                            { name: "Title", width: "150px" },
                            { name: "Description", width: "200px" },
                            { name: "Order", width: "80px" },
                            {
                                name: "Action",
                                width: "150px",
                                sort: false,
                                formatter: (_, row) => {
                                    const actionData = row.cells[5].data;
                                    const deleteButtonHtml = `
                                        <button data-destroy-url="${actionData.destroy_url}"
                                                data-benefit-title="${actionData.title}"
                                                type="button"
                                                class="btn btn-soft-danger btn-icon btn-sm rounded-circle delete-benefit"
                                                title="Delete"><i class="ti ti-trash"></i></button>`;

                                    const editButtonHtml = `
                                        <button class="btn btn-soft-success btn-icon btn-sm rounded-circle edit-benefit-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editBenefitModal"
                                                data-benefit-data='${JSON.stringify(actionData)}'
                                                role="button"
                                                title="Edit"><i class="ti ti-edit fs-16"></i></button>`;

                                    return gridjs.html(`
                                        <div class="hstack gap-1 justify-content-end">
                                            ${editButtonHtml}
                                            ${deleteButtonHtml}
                                        </div>`);
                                }
                            }
                        ],
                        pagination: {
                            limit: 10,
                            summary: true
                        },
                        sort: true,
                        search: true,
                        data: benefitData,
                        style: {
                            table: {
                                'font-size': '0.85rem'
                            }
                        }
                    }).render(document.getElementById("table-benefit-gridjs"));
                }
            } else {
                console.error("Grid.js Error: benefitData is missing or not a valid array.");
                document.getElementById("table-benefit-gridjs").innerHTML =
                    '<div class="alert alert-danger">Could not load benefit data.</div>';
            }

            document.addEventListener('click', function(e) {
                const deleteButton = e.target.closest('.delete-benefit');
                if (deleteButton) {
                    const benefitTitle = deleteButton.getAttribute('data-benefit-title') || 'this benefit';
                    const actionUrl = deleteButton.getAttribute('data-destroy-url');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    Swal.fire({
                        title: "Are you sure?",
                        text: `Benefit \"${benefitTitle}\" will be permanently deleted! This action cannot be undone.`,
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

                const editButton = e.target.closest('.edit-benefit-btn');
                if (editButton) {
                    const modal = $('#editBenefitModal');
                    const benefitData = JSON.parse(editButton.dataset.benefitData);

                    modal.find('#editTitle').val(benefitData.title);
                    modal.find('#editDescription').val(benefitData.description);
                    modal.find('#editOrder').val(benefitData.order);
                    modal.find('#editBenefitForm').attr('action', benefitData.update_url);
                }
            });

            $(function() {
                $('#createBenefitModal .select2').select2({
                    dropdownParent: $('#createBenefitModal')
                });
                $('#editBenefitModal .select2').select2({
                    dropdownParent: $('#editBenefitModal')
                });
            });
        });
    </script>
@endpush
