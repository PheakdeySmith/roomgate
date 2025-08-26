@extends('backends.layouts.app')

@section('title', 'FAQ | RoomGate')

@push('style')
    {{-- Keep your existing styles --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/mermaid.min.css">
    <link href="{{ asset('assets') }}/css/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.core.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/css/quill.snow.css" rel="stylesheet" type="text/css">
@endpush

@section('content')
    <div class="page-container">
        {{-- Page Title --}}
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">FAQ Section</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">FAQ Section</li>
                </ol>
            </div>
        </div>

        @if (Auth::check() && Auth::user()->hasRole('admin'))
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom border-dashed">
                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                <h4 class="header-title">FAQ Content</h4>
                                <a class="btn btn-primary" data-bs-toggle="modal" href="#createFaqModal" role="button">
                                    <i class="ti ti-plus me-1"></i>Add FAQ
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="table-faq-gridjs"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- Modals --}}
    @if (Auth::check() && Auth::user()->hasRole('admin'))
        @include('backends.dashboard.admin.frontend.faq.create')
        @include('backends.dashboard.admin.frontend.faq.edit')
    @endif
@endsection

@push('script')
    <script src="{{ asset('assets') }}/js/gridjs.umd.js"></script>
    <script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const faqData = {!! json_encode(
                $faqs->map(function ($faq, $key) {
                    $faqDataForJs = [
                        'id' => $faq->id,
                        'question' => Str::limit(strip_tags($faq->question), 50),
                        'answer' => Str::limit(strip_tags($faq->answer), 80),
                        'full_question' => $faq->question, // For edit modal
                        'full_answer' => $faq->answer,     // For edit modal
                        'order' => $faq->order,
                        'destroy_url' => route('admin.faq.destroy', $faq->id),
                        'update_url' => route('admin.faq.update', $faq->id),
                    ];
                    return [
                        $key + 1,
                        $faqDataForJs['question'],
                        $faqDataForJs['answer'],
                        $faqDataForJs['order'],
                        $faqDataForJs, // Hidden data object
                    ];
                })->values()->all(),
            ) !!};

            if (document.getElementById("table-faq-gridjs")) {
                if (faqData.length === 0) {
                    document.getElementById("table-faq-gridjs").innerHTML =
                        '<div class="alert alert-info text-center">No FAQs found.</div>';
                } else {
                    new gridjs.Grid({
                        columns: [{ name: "#", width: "50px" },
                            { name: "Question", width: "300px" },
                            { name: "Answer", width: "400px" },
                            { name: "Order", width: "80px" },
                            {
                                name: "Action",
                                width: "150px",
                                sort: false,
                                formatter: (_, row) => {
                                    const actionData = row.cells[4].data;
                                    const deleteButtonHtml = `
                                        <button data-destroy-url="${actionData.destroy_url}"
                                                data-faq-question="${actionData.question}"
                                                type="button"
                                                class="btn btn-soft-danger btn-icon btn-sm rounded-circle delete-faq"
                                                title="Delete"><i class="ti ti-trash"></i></button>`;

                                    const editButtonHtml = `
                                        <button class="btn btn-soft-success btn-icon btn-sm rounded-circle edit-faq-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editFaqModal"
                                                data-faq-data='${JSON.stringify(actionData)}'
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
                        pagination: { limit: 10 },
                        sort: true,
                        search: true,
                        data: faqData,
                    }).render(document.getElementById("table-faq-gridjs"));
                }
            }

            document.addEventListener('click', function(e) {
                const deleteButton = e.target.closest('.delete-faq');
                if (deleteButton) {
                    const question = deleteButton.getAttribute('data-faq-question') || 'this FAQ';
                    const actionUrl = deleteButton.getAttribute('data-destroy-url');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    Swal.fire({
                        title: "Are you sure?",
                        text: `FAQ "${question}" will be permanently deleted!`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, delete it!",
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

                const editButton = e.target.closest('.edit-faq-btn');
                if (editButton) {
                    const modal = $('#editFaqModal');
                    const data = JSON.parse(editButton.dataset.faqData);
                    modal.find('#editQuestion').val(data.full_question);
                    modal.find('#editAnswer').val(data.full_answer);
                    modal.find('#editOrder').val(data.order);
                    modal.find('#editFaqForm').attr('action', data.update_url);
                }
            });
        });
    </script>
@endpush