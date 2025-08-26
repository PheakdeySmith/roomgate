@extends('backends.layouts.app')

@section('title', 'Feature | RoomGate')

@push('style')
    {{-- Keep your existing styles --}}
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
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Feature Section</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Feature Section</li>
                </ol>
            </div>
        </div>

        @if (Auth::check() && Auth::user()->hasRole('admin'))
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom border-dashed">
                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                <h4 class="header-title">Feature Content</h4>
                                <a class="btn btn-primary" data-bs-toggle="modal" href="#createFeatureModal" role="button">
                                    <i class="ti ti-plus me-1"></i>Add Feature
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="table-feature-gridjs"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- Modals --}}
    @if (Auth::check() && Auth::user()->hasRole('admin'))
        @include('backends.dashboard.admin.frontend.feature.create')
        @include('backends.dashboard.admin.frontend.feature.edit')
    @endif
@endsection

@push('script')
    {{-- Keep your existing script includes --}}
    <script src="{{ asset('assets') }}/js/gridjs.umd.js"></script>
    <script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>
    <script src="{{ asset('assets') }}/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const featureData = {!! json_encode(
                $features->map(function ($feature, $key) {
                    // This object contains ALL the data needed for the edit modal
                    $featureDataForJs = [
                        'id' => $feature->id,
                        'image_path' => $feature->image_path ? asset($feature->image_path) : null,
                        'title' => $feature->title,
                        'description' => $feature->description, // Use the FULL description for the modal
                        'link' => $feature->link,
                        'bullets' => $feature->bullets,
                        'order' => $feature->order,
                        'is_highlighted' => $feature->is_highlighted,
                        'destroy_url' => route('admin.feature.destroy', $feature->id),
                        'update_url' => route('admin.feature.update', $feature->id),
                    ];
                    // This array defines the data for each row in the visible grid
                    return [
                        $key + 1,
                        $featureDataForJs['image_path'],
                        $featureDataForJs['title'],
                        $featureDataForJs['is_highlighted'],
                        $featureDataForJs['order'],
                        $featureDataForJs, // The complete data object is hidden at the end
                    ];
                })->values()->all(),
            ) !!};

            if (typeof featureData !== 'undefined' && Array.isArray(featureData)) {
                if (featureData.length === 0) {
                    document.getElementById("table-feature-gridjs").innerHTML =
                        '<div class="alert alert-info text-center">No features found.</div>';
                } else {
                    new gridjs.Grid({
                        columns: [{ name: "#", width: "50px" },
                            { name: "Image", width: "80px", formatter: (cell) => cell ? gridjs.html(`<img src="${cell}" alt="Image" style="max-width:50px;max-height:50px; object-fit: cover;">`) : '' },
                            { name: "Title", width: "250px" },
                            {
                                name: "Status",
                                width: "120px",
                                formatter: (cell) => {
                                    return cell ? gridjs.html('<span class="badge bg-success-subtle text-success">Highlighted</span>') : gridjs.html('<span class="badge bg-secondary-subtle text-secondary">Standard</span>');
                                }
                            },
                            { name: "Order", width: "80px" },
                            {
                                name: "Action",
                                width: "150px",
                                sort: false,
                                formatter: (_, row) => {
                                    // The full data object is now at index 5
                                    const actionData = row.cells[5].data;
                                    const deleteButtonHtml = `
                                        <button data-destroy-url="${actionData.destroy_url}"
                                                data-feature-title="${actionData.title}"
                                                type="button"
                                                class="btn btn-soft-danger btn-icon btn-sm rounded-circle delete-feature"
                                                title="Delete"><i class="ti ti-trash"></i></button>`;

                                    const editButtonHtml = `
                                        <button class="btn btn-soft-success btn-icon btn-sm rounded-circle edit-feature-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editFeatureModal"
                                                data-feature-data='${JSON.stringify(actionData)}'
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
                        pagination: { limit: 10, summary: true },
                        sort: true,
                        search: true,
                        data: featureData,
                        style: { table: { 'font-size': '0.85rem' } }
                    }).render(document.getElementById("table-feature-gridjs"));
                }
            }

            document.addEventListener('click', function(e) {
                // Delete feature logic
                const deleteButton = e.target.closest('.delete-feature');
                if (deleteButton) {
                    const featureTitle = deleteButton.getAttribute('data-feature-title') || 'this feature';
                    const actionUrl = deleteButton.getAttribute('data-destroy-url');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    Swal.fire({
                        title: "Are you sure?",
                        text: `Feature \"${featureTitle}\" will be permanently deleted! This action cannot be undone.`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "No, cancel",
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = actionUrl;
                            form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}"><input type="hidden" name="_method" value="DELETE">`;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }

                // Edit feature logic
                const editButton = e.target.closest('.edit-feature-btn');
                if (editButton) {
                    const modal = $('#editFeatureModal');
                    const featureData = JSON.parse(editButton.dataset.featureData);

                    // Populate all fields from the full data object
                    modal.find('#editTitle').val(featureData.title);
                    modal.find('#editDescription').val(featureData.description);
                    modal.find('#editLink').val(featureData.link);
                    modal.find('#editOrder').val(featureData.order);
                    modal.find('#editFeatureForm').attr('action', featureData.update_url);
                    modal.find('#edit_is_highlighted').prop('checked', featureData.is_highlighted);

                    // Handle the image preview
                    const imagePreview = modal.find('#current-image-preview');
                    const noImageText = modal.find('#no-current-image');
                    if (featureData.image_path) {
                        imagePreview.attr('src', featureData.image_path).show();
                        noImageText.hide();
                    } else {
                        imagePreview.hide();
                        noImageText.show();
                    }

                    // Logic to populate bullets
                    const bulletsContainer = modal.find('#edit-bullets-container');
                    bulletsContainer.empty();
                    if (featureData.bullets && Array.isArray(featureData.bullets)) {
                        featureData.bullets.forEach(bullet => {
                            const bulletHtml = `
                                <div class="input-group mb-2">
                                    <input type="text" name="bullets[]" class="form-control" value="${bullet}" required>
                                    <button class="btn btn-outline-danger remove-bullet" type="button">Remove</button>
                                </div>`;
                            bulletsContainer.append(bulletHtml);
                        });
                    }
                }
            });

            function setupBulletHandlers(modalId) {
                const modal = document.getElementById(modalId);
                if (!modal) return;
                modal.addEventListener('click', function(e) {
                    if (e.target.matches('.add-bullet')) {
                        const container = e.target.previousElementSibling;
                        const bulletHtml = `
                            <div class="input-group mb-2">
                                <input type="text" name="bullets[]" class="form-control" placeholder="Feature bullet point" required>
                                <button class="btn btn-outline-danger remove-bullet" type="button">Remove</button>
                            </div>`;
                        container.insertAdjacentHTML('beforeend', bulletHtml);
                    }
                    if (e.target.matches('.remove-bullet')) {
                        e.target.closest('.input-group').remove();
                    }
                });
            }

            setupBulletHandlers('createFeatureModal');
            setupBulletHandlers('editFeatureModal');
        });
    </script>
@endpush