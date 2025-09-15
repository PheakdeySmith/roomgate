@extends('backends.layouts.app')

@section('title', 'Users | RoomGate')

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
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Users Tables</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users Tables</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom border-dashed">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <h4 class="header-title">Users Data</h4>
                            {{-- @if (Auth::check() && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('landlord')))
                                <a class="btn btn-primary" data-bs-toggle="modal" href="#createModal" role="button">
                                    <i class="ti ti-plus me-1"></i>Add User
                                </a>
                            @endif --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="table-gridjs"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (Auth::check() && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('landlord')))
        @include('backends.dashboard.users.create')
    @endif
    @include('backends.dashboard.users.edit')
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
        const usersData = {!! json_encode(
            $usersData = $users->map(function ($user, $key) {
                    $destroyUrl = '';
                    $editUrl = '';
                    $viewUrl = ''; // This will be the user-specific view URL

                    $userName = $user->name ?? 'N/A';
                    $userImage =
                        $user->image && is_string($user->image)
                            ? asset($user->image)
                            : asset('assets/images/default_image.png');

                    if (auth()->check()) {
                        if (auth()->user()->hasRole('admin')) {
                            if ($user->hasRole('landlord')) {
                                $destroyUrl = $user->id ? route('admin.users.destroy', $user->id) : '';
                                $editUrl = $user->id ? route('admin.users.update', $user->id) : '';
                                $viewUrl = $user->id ? route('admin.users.show', $user->id) : ''; // Use the actual route for view
                            }
                        } elseif (auth()->user()->hasRole('landlord')) {
                            if ($user->hasRole('tenant') && $user->landlord_id === auth()->id()) {
                                $destroyUrl = $user->id ? route('landlord.users.destroy', $user->id) : '';
                                $editUrl = $user->id ? route('landlord.users.update', $user->id) : '';
                                $viewUrl = $user->id ? route('landlord.users.show', $user->id) : ''; // Use the actual route for view
                            }
                        }
                    }

                    return [
                        $key + 1, // 0. Sequential Number (S.N.)
                        $userImage, // 1. Image URL
                        $userName, // 2. Name
                        $user->email ?? 'N/A', // 3. Email
                        $user->phone ?? 'N/A', // 4. Phone
                        $user->status ?? 'N/A', // 5. Status
                        (object) [
                            // 6. Action data object
                            'destroy_url' => $destroyUrl,
                            'edit_url' => $editUrl,
                            'user_view_url' => $viewUrl, // Contains the fully resolved view URL
                            'actual_user_id' => $user->id, // Crucial: Pass the actual User ID here
                        ],
                    ];
                })->values()->all(),
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE,
        ) !!};

        console.log(usersData);

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
            width: "50px",
            formatter: (cellData) => gridjs.html(`<span class="fw-semibold">${cellData}</span>`)
        },
                {
                    name: "Image",
                    width: "80px",
                    formatter: (_, row) => {
                        const imageUrl = row.cells[1].data;
                        return gridjs.html(`
        <div class="avatar-sm d-flex justify-content-left align-items-left ">
            <img src="${imageUrl}"
                 alt="User"
                 class="rounded"
                 style="width: 100%; height: 100%; object-fit: cover;" />
        </div>
        `);
                    }
                },
                {
                    name: "Name",
                    width: "150px"
                },
                {
                    name: "Email",
                    width: "200px"
                },
                {
                    name: "Phone",
                    width: "120px"
                },
                {
                    name: "Status",
                    width: "100px",
                    formatter: (_, row) => {
                        const status = row.cells[5].data;
                        return gridjs.html(
                            `<span class="badge badge-soft-${status === 'active' ? 'success' : 'danger'}">${status}</span>`
                        );
                    }
                },
                // Example for the Action column formatter
                {
                    name: "Action",
                    width: "150px",
                    sort: false,
                    formatter: (_, row) => {

                        const actionData = row._cells[6].data;

                        const destroyUrl = actionData?.destroy_url;
                        const editUrl = actionData?.edit_url;

                        const id = row.cells[0].data;
                        const image = row.cells[1].data;
                        const name = row.cells[2].data;
                        const email = row.cells[3].data;
                        const phone = row.cells[4].data;
                        const status = row.cells[5].data;

                        let deleteButtonHtml = '';
                        if (destroyUrl) {
                            deleteButtonHtml = `
                    <button data-user-id="${id}"
                            data-user-name="${name}"
                            data-action-url="${destroyUrl}"
                            type="button"
                            class="btn btn-soft-danger btn-icon btn-sm rounded-circle delete-user"
                            title="Delete User">
                        <i class="ti ti-trash"></i>
                    </button>
                `;
                        }

                        let editButtonHtml = '';
                        if (editUrl) {
                            editButtonHtml = `
                    <button
                        class="btn btn-soft-success btn-icon btn-sm rounded-circle edit-user-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal"
                        data-id="${id}"
                        data-image="${image}"
                        data-name="${name}"
                        data-email="${email}"
                        data-phone="${phone}"
                        data-status="${status}"
                        data-edit-url="${editUrl}"
                        role="button" title="Edit User">
                        <i class="ti ti-edit fs-16"></i>
                    </button>
                `;
                        }

                        // Get user view URL (or fallback to contract URL for landlords)
                        const viewUrl = actionData?.user_view_url || '';
                        const actualUserId = actionData?.actual_user_id || '';

                        let viewButtonHtml = '';

                        // Check if user is landlord and looking at tenants
                        const isLandlordViewingTenant = {{ Auth::user()->hasRole('landlord') ? 'true' : 'false' }};

                        if (isLandlordViewingTenant) {
                            // For landlords, find the first contract of the tenant
                            viewButtonHtml = `
                                <a href="/landlord/find-tenant-contract/${actualUserId}"
                                   class="btn btn-soft-primary btn-icon btn-sm rounded-circle"
                                   title="View Tenant Contract">
                                    <i class="ti ti-eye"></i>
                                </a>
                            `;
                        } else {
                            // For admins, use the regular user view URL
                            viewButtonHtml = `
                                <a href="${viewUrl}"
                                   class="btn btn-soft-primary btn-icon btn-sm rounded-circle"
                                   title="View User Details">
                                    <i class="ti ti-eye"></i>
                                </a>
                            `;
                        }

                        return gridjs.html(`
                <div class="hstack gap-1 justify-content-end">
                    ${viewButtonHtml}
                    ${editButtonHtml}
                    ${deleteButtonHtml}
                </div>
            `);
                    }
                }
            ],
            pagination: {
                limit: 10,
                summary: true
            },
            sort: true,
            search: true,
            data: usersData,
            style: {
                table: {
                    'font-size': '0.85rem'
                },
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-user')) {
                const button = e.target.closest('.delete-user');
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name') || 'this user';
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
                    text: `User "${userName}" will be permanently deleted! This action cannot be undone.`,
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

        $(function() {
            $('#status').select2({
                dropdownParent: $('#createModal'),
                placeholder: "Select status",
                allowClear: true
            });

            $('#edit_status').select2({
                dropdownParent: $('#editModal'),
                placeholder: "Select status",
                allowClear: true
            });
        });

        $('body').on('click', '.edit-user-btn', function() {
            const button = $(this);
            const modal = $('#editModal');

            // Get data from the button's data attributes
            const id = button.data('id');
            const name = button.data('name');
            const email = button.data('email');
            const phone = button.data('phone');
            const status = button.data('status');
            const imageUrl = button.data('image');
            const actionUrl = button.data('edit-url');
            // const userRole = button.data('role'); // You'd need to add data-role to your button

            // Populate the modal form fields
            modal.find('#editUserId').val(id); // If you use this hidden field
            modal.find('#editName').val(name);
            modal.find('#editEmail').val(email);
            modal.find('#editPhone').val(phone || ''); // Handle null/undefined phone

            // Set the status dropdown
            modal.find('#editStatus').val(status).trigger('change'); // trigger change for select2

            // Set the role dropdown (if you add role data)
            // modal.find('#editUserRole').val(userRole).trigger('change');


            // Handle image preview
            const imagePreview = modal.find('#editImagePreview');
            const existingImagePathField = modal.find('#editExistingImagePath');
            if (imageUrl && imageUrl !== '{{ asset('assets/images/default_image.png') }}') {
                imagePreview.attr('src', imageUrl).show();
                existingImagePathField.val(imageUrl); // Or the relative path if that's what your backend expects
            } else {
                imagePreview.attr('src', 'https://placehold.co/150x150/e9ecef/6c757d?text=No+Image')
                    .hide(); // Or a default placeholder
                existingImagePathField.val('');
            }
            // Reset file input and remove image checkbox
            modal.find('#editImage').val('');
            modal.find('#removeCurrentImage').prop('checked', false);


            // Set the form's action URL
            modal.find('#editUserForm').attr('action', actionUrl);
        });

        // Optional: Clear image preview if a new file is selected
        $('#editImage').on('change', function() {
            const input = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#editImagePreview').attr('src', e.target.result).show();
                }
                reader.readAsDataURL(input.files[0]);
                $('#removeCurrentImage').prop('checked', false); // Uncheck remove if new image is chosen
            }
        });

        // Optional: Handle "Remove current image" checkbox
        $('#removeCurrentImage').on('change', function() {
            if ($(this).is(':checked')) {
                $('#editImagePreview').hide();
                $('#editImage').val(''); // Clear file input if remove is checked
            } else {
                // If there was an existing image, show it again unless a new file is selected
                const existingImage = $('#editExistingImagePath').val();
                if (existingImage && !$('#editImage').val()) {
                    $('#editImagePreview').attr('src', existingImage).show();
                } else if (!$('#editImage').val()) {
                    $('#editImagePreview').attr('src', 'https://placehold.co/150x150/e9ecef/6c757d?text=No+Image')
                        .hide();
                }
            }
        });
    </script>
@endpush
