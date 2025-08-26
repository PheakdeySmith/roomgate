

@extends('backends.layouts.app')

@section('title', 'Hero | RoomGate')

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
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Hero Section</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Hero Section</li>
                </ol>
            </div>
        </div>

        @if (Auth::check() && Auth::user()->hasRole('admin'))
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom border-dashed">
                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                <h4 class="header-title">Hero Content</h4>
                                {{-- <a class="btn btn-primary" data-bs-toggle="modal" href="#createHeroModal" role="button">
                                    <i class="ti ti-plus me-1"></i>Add Hero Content
                                </a> --}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="table-hero-gridjs"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- Modals --}}
    @if (Auth::check() && Auth::user()->hasRole('admin'))
        {{-- @include('backends.dashboard.admin.frontend.hero.create') --}}
        @include('backends.dashboard.admin.frontend.hero.edit')
    @endif
@endsection

@push('script')
    <script src="{{ asset('assets') }}/js/gridjs.umd.js"></script>
    <script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>
    <script src="{{ asset('assets') }}/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const heroContentsData = {!! json_encode(
                $heroContents->map(function ($hero, $key) {
                    $heroDataForJs = [
                        'id' => $hero->id,
                        'title' => $hero->title,
                        'subtitle' => $hero->subtitle,
                        'content' => Str::limit(strip_tags($hero->content), 50),
                        'image_path' => $hero->image_path ? asset($hero->image_path) : null,
                        'button_text' => $hero->button_text,
                        'button_link' => $hero->button_link,
                        'video_url' => $hero->video_url,
                        'update_url' => route('admin.hero.update', $hero->id),
                    ];
                    return [
                        $key + 1,
                        $heroDataForJs['title'],
                        $heroDataForJs['subtitle'],
                        $heroDataForJs['content'],
                        $heroDataForJs['image_path'],
                        $heroDataForJs['button_text'],
                        $heroDataForJs['button_link'],
                        $heroDataForJs['video_url'],
                        $heroDataForJs,
                    ];
                })->values()->all(),
            ) !!};

            if (typeof heroContentsData !== 'undefined' && Array.isArray(heroContentsData)) {
                if (heroContentsData.length === 0) {
                    document.getElementById("table-hero-gridjs").innerHTML =
                        '<div class="alert alert-info text-center">No hero content found.</div>';
                } else {
                    new gridjs.Grid({
                        columns: [
                            { name: "#", width: "50px" },
                            { name: "Title", width: "150px" },
                            { name: "Subtitle", width: "150px" },
                            { name: "Content", width: "200px" },
                            { name: "Image", width: "100px", formatter: (cell) => cell ? gridjs.html(`<img src="${cell}" alt="Hero Image" style="max-width:80px;max-height:60px;">`) : '' },
                            { name: "Button Text", width: "120px" },
                            { name: "Button Link", width: "150px", formatter: (cell) => cell ? gridjs.html(`<a href="${cell}" target="_blank">${cell}</a>`) : '' },
                            { name: "Video URL", width: "120px", formatter: (cell) => cell ? gridjs.html(`<a href="${cell}" target="_blank">Video</a>`) : '' },
                            {
                                name: "Action",
                                width: "150px",
                                sort: false,
                                formatter: (_, row) => {
                                    const actionData = row.cells[8].data;

                                    const editButtonHtml = `
                                        <button class="btn btn-soft-success btn-icon btn-sm rounded-circle edit-hero-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editHeroModal"
                                                data-hero-data='${JSON.stringify(actionData)}'
                                                role="button"
                                                title="Edit"><i class="ti ti-edit fs-16"></i></button>`;

                                    return gridjs.html(`
                                        <div class="hstack gap-1 justify-content-end">
                                            ${editButtonHtml}
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
                        data: heroContentsData,
                        style: {
                            table: {
                                'font-size': '0.85rem'
                            }
                        }
                    }).render(document.getElementById("table-hero-gridjs"));
                }
            } else {
                console.error("Grid.js Error: heroContentsData is missing or not a valid array.");
                document.getElementById("table-hero-gridjs").innerHTML =
                    '<div class="alert alert-danger">Could not load hero content data.</div>';
            }

            document.addEventListener('click', function(e) {
                const deleteButton = e.target.closest('.delete-hero');
                if (deleteButton) {
                    const heroTitle = deleteButton.getAttribute('data-hero-title') || 'this hero content';
                    const actionUrl = deleteButton.getAttribute('data-destroy-url');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    Swal.fire({
                        title: "Are you sure?",
                        text: `Hero "${heroTitle}" will be permanently deleted! This action cannot be undone.`,
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

                const editButton = e.target.closest('.edit-hero-btn');
                if (editButton) {
                    const modal = $('#editHeroModal');
                    const heroData = JSON.parse(editButton.dataset.heroData);

                    modal.find('#editTitle').val(heroData.title);
                    modal.find('#editSubtitle').val(heroData.subtitle);
                    modal.find('#editContent').val(heroData.content);
                    modal.find('#editButtonText').val(heroData.button_text);
                    modal.find('#editButtonLink').val(heroData.button_link);
                    modal.find('#editVideoUrl').val(heroData.video_url);
                    modal.find('#editHeroForm').attr('action', heroData.update_url);
                }
            });

            $(function() {
                $('#createHeroModal .select2').select2({
                    dropdownParent: $('#createHeroModal')
                });
                $('#editHeroModal .select2').select2({
                    dropdownParent: $('#editHeroModal')
                });
            });
        });
    </script>
@endpush
