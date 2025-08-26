@extends('backends.layouts.app')

@section('title', 'Subscriptions | RoomGate')

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

@push('script')
    <script src="{{ asset('assets') }}/js/gridjs.umd.js"></script>
    <script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>
    
    <script>
        // Wrap in a try-catch to catch any JSON parsing errors
        try {
            const subscriptionsData = {!! json_encode(
                $subscriptionsData = $subscriptions->map(function ($subscription, $key) {
                    try {
                        // User data formatting
                        $userName = $subscription->user && $subscription->user->name ? $subscription->user->name : 'N/A';
                        $userEmail = $subscription->user && $subscription->user->email ? $subscription->user->email : 'N/A';
                        $userImage = $subscription->user && $subscription->user->image ? asset($subscription->user->image) : asset('assets/images/default_image.png');
                        
                        // Create a user object with all necessary properties
                        $user = [
                            'image' => $userImage,
                            'name' => $userName,
                            'email' => $userEmail,
                        ];
                        
                        // Plan and dates
                        $planName = $subscription->subscriptionPlan && $subscription->subscriptionPlan->name ? $subscription->subscriptionPlan->name : 'No Plan';
                        $startDate = $subscription->start_date ? $subscription->start_date->format('M d, Y') : 'N/A';
                        $endDate = $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A';
                        
                        // Status and payment
                        $status = $subscription->status ? $subscription->status : 'N/A';
                        $paymentStatus = $subscription->payment_status ? $subscription->payment_status : 'N/A';
                        $amountPaid = $subscription->formatted_amount_paid ? $subscription->formatted_amount_paid : 'N/A';
                        
                        // Action URLs
                        $viewUrl = route('admin.subscriptions.show', $subscription->id);
                        $cancelUrl = route('admin.subscriptions.cancel', $subscription->id);
                        $renewUrl = route('admin.subscriptions.renew', $subscription->id);
                        
                        // Create action data object
                        $actionData = [
                            'subscription_id' => $subscription->id,
                            'user_name' => $userName,
                            'view_url' => $viewUrl,
                            'cancel_url' => $cancelUrl,
                            'renew_url' => $renewUrl,
                            'status' => $status
                        ];
                        
                        return [
                            $key + 1,      // 0. #
                            $user,         // 1. User (contains image, name, email)
                            $planName,     // 2. Plan
                            $startDate,    // 3. Start Date
                            $endDate,      // 4. End Date
                            $status,       // 5. Status
                            $paymentStatus,// 6. Payment
                            $amountPaid,   // 7. Amount
                            $actionData    // 8. Action data
                        ];
                    } catch (Exception $e) {
                        // Log the error but return a placeholder row to avoid breaking the whole table
                        error_log('Error processing subscription #' . ($subscription->id ?? 'unknown') . ': ' . $e->getMessage());
                        
                        // Return a properly structured placeholder row
                        return [
                            $key + 1,
                            ['image' => asset('assets/images/default_image.png'), 'name' => 'Error', 'email' => 'Data unavailable'],
                            'N/A',
                            'N/A',
                            'N/A',
                            'error',
                            'N/A',
                            'N/A',
                            ['subscription_id' => $subscription->id ?? 0, 'user_name' => 'Error', 'view_url' => '#', 
                             'cancel_url' => '#', 'renew_url' => '#', 'status' => 'error']
                        ];
                    }
                })->values()->all(),
                JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE,
            ) !!};

            console.log('Subscriptions Data:', subscriptionsData);

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
                name: "User",
                width: "200px",
                formatter: (cell) => {
                    try {
                        // Safety checks for undefined or missing data
                        if (!cell) {
                            return gridjs.html(`<div>User data unavailable</div>`);
                        }
                        
                        const userData = cell;
                        const imageUrl = userData.image || "{{ asset('assets/images/default_image.png') }}";
                        const name = userData.name || "N/A";
                        const email = userData.email || "N/A";
                        
                        return gridjs.html(`
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="${imageUrl}" 
                                        class="rounded-circle avatar-xs" alt="User Image">
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h5 class="mb-0 font-size-14">${name}</h5>
                                    <p class="mb-0 text-muted font-size-12">${email}</p>
                                </div>
                            </div>
                        `);
                    } catch (error) {
                        console.error("Error in User formatter:", error);
                        return gridjs.html(`<div>Error loading user data</div>`);
                    }
                }
            },
            {
                name: "Plan",
                width: "150px"
            },
            {
                name: "Start Date",
                width: "120px"
            },
            {
                name: "End Date",
                width: "120px"
            },
            {
                name: "Status",
                width: "100px",
                formatter: (cell) => {
                    try {
                        const status = cell || 'N/A';
                        let badgeClass = "badge-soft-secondary";
                        
                        if (status === 'active') {
                            badgeClass = "badge-soft-success";
                        } else if (status === 'canceled') {
                            badgeClass = "badge-soft-danger";
                        } else if (status === 'pending') {
                            badgeClass = "badge-soft-warning";
                        } else if (status === 'expired') {
                            badgeClass = "badge-soft-secondary";
                        }
                        
                        return gridjs.html(`<span class="badge ${badgeClass}">${status}</span>`);
                    } catch (error) {
                        console.error("Error in Status formatter:", error);
                        return gridjs.html(`<span class="badge badge-soft-secondary">Error</span>`);
                    }
                }
            },
            {
                name: "Payment",
                width: "100px",
                formatter: (cell) => {
                    try {
                        const paymentStatus = cell || 'N/A';
                        let badgeClass = "badge-soft-secondary";
                        
                        if (paymentStatus === 'paid') {
                            badgeClass = "badge-soft-success";
                        } else if (paymentStatus === 'pending') {
                            badgeClass = "badge-soft-warning";
                        } else if (paymentStatus === 'failed') {
                            badgeClass = "badge-soft-danger";
                        } else if (paymentStatus === 'trial') {
                            badgeClass = "badge-soft-info";
                        }
                        
                        return gridjs.html(`<span class="badge ${badgeClass}">${paymentStatus}</span>`);
                    } catch (error) {
                        console.error("Error in Payment formatter:", error);
                        return gridjs.html(`<span class="badge badge-soft-secondary">Error</span>`);
                    }
                }
            },
            {
                name: "Amount",
                width: "100px"
            },
            {
                name: "Action",
                width: "150px",
                sort: false,
                formatter: (cell) => {
                    try {
                        // Safely access data with null/undefined checks
                        if (!cell) {
                            return gridjs.html(`<div class="text-center text-muted">No actions available</div>`);
                        }
                        
                        const actionData = cell;
                        
                        // Safely extract data with defaults
                        const subscriptionId = actionData.subscription_id || '';
                        const userName = actionData.user_name || 'Unknown';
                        const viewUrl = actionData.view_url || '#';
                        const cancelUrl = actionData.cancel_url || '#';
                        const renewUrl = actionData.renew_url || '#';
                        const status = actionData.status || '';
                        
                        let cancelButtonHtml = '';
                        if (status === 'active') {
                            cancelButtonHtml = `
                                <button type="button" 
                                    class="btn btn-soft-warning btn-icon btn-sm rounded-circle cancel-subscription" 
                                    data-subscription-id="${subscriptionId}"
                                    data-user-name="${userName}"
                                    data-action-url="${cancelUrl}"
                                    title="Cancel Subscription">
                                    <i class="ti ti-x"></i>
                                </button>
                            `;
                        }
                        
                        return gridjs.html(`
                            <div class="hstack gap-1 justify-content-end">
                                <a href="${viewUrl}" class="btn btn-soft-info btn-icon btn-sm rounded-circle" title="View Details">
                                    <i class="ti ti-eye"></i>
                                </a>
                                ${cancelButtonHtml}
                                <button type="button" 
                                    class="btn btn-soft-success btn-icon btn-sm rounded-circle renew-subscription"
                                    data-subscription-id="${subscriptionId}"
                                    data-user-name="${userName}"
                                    data-action-url="${renewUrl}"
                                    title="Create new subscription based on this one">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                        `);
                    } catch (error) {
                        console.error("Error in Action formatter:", error);
                        return gridjs.html(`<div class="text-center text-danger">Error loading actions</div>`);
                    }
                }
            }],
            pagination: {
                limit: 10,
                summary: true
            },
            sort: true,
            search: true,
            data: subscriptionsData,
            style: {
                table: {
                    'font-size': '0.85rem'
                }
            },
            className: {
                table: 'table table-centered table-hover table-nowrap mb-0'
            }
        });
        
        } catch (error) {
            console.error("Error initializing Grid.js:", error);
            document.getElementById("table-gridjs").innerHTML = `
                <div class="alert alert-danger">
                    <strong>Error:</strong> There was a problem loading the subscription data. 
                    <p class="mb-1">Error details: ${error.message}</p>
                    <button class="btn btn-sm btn-outline-danger mt-2" onclick="location.reload()">
                        <i class="ti ti-refresh me-1"></i> Reload Page
                    </button>
                </div>
            `;
        }

        document.addEventListener('click', function(e) {
            // Handle cancel subscription button click
            if (e.target.closest('.cancel-subscription')) {
                const button = e.target.closest('.cancel-subscription');
                e.preventDefault();
                e.stopPropagation();
                
                const subscriptionId = button.getAttribute('data-subscription-id');
                const userName = button.getAttribute('data-user-name') || 'this subscription';
                const actionUrl = button.getAttribute('data-action-url');
                
                console.log('Cancel clicked for subscription:', subscriptionId, userName, actionUrl);
                
                if (!actionUrl) {
                    console.error('Cancel action URL not found on button for subscription ID:', subscriptionId);
                    Swal.fire('Error!', 'Cannot proceed with cancellation. Action URL is missing.', 'error');
                    return;
                }
                
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfMeta) {
                    console.error('CSRF token meta tag not found in document head.');
                    Swal.fire('Error!', 'Cannot proceed: CSRF token not found.', 'error');
                    return;
                }
                const csrfToken = csrfMeta.getAttribute('content');
                
                Swal.fire({
                    title: "Are you sure?",
                    text: `Subscription #${subscriptionId} for "${userName}" will be canceled. This action can be undone by renewing later.`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, cancel it!",
                    cancelButtonText: "No, keep it",
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
                        form.style.display = 'none';
                        
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = csrfToken;
                        
                        form.appendChild(tokenInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
            
            // Handle renew subscription button click
            if (e.target.closest('.renew-subscription')) {
                const button = e.target.closest('.renew-subscription');
                e.preventDefault();
                e.stopPropagation();
                
                const subscriptionId = button.getAttribute('data-subscription-id');
                const userName = button.getAttribute('data-user-name') || 'this subscription';
                const actionUrl = button.getAttribute('data-action-url');
                
                console.log('Renew clicked for subscription:', subscriptionId, userName, actionUrl);
                
                if (!actionUrl) {
                    console.error('Renew action URL not found on button for subscription ID:', subscriptionId);
                    Swal.fire('Error!', 'Cannot proceed with renewal. Action URL is missing.', 'error');
                    return;
                }
                
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfMeta) {
                    console.error('CSRF token meta tag not found in document head.');
                    Swal.fire('Error!', 'Cannot proceed: CSRF token not found.', 'error');
                    return;
                }
                const csrfToken = csrfMeta.getAttribute('content');
                
                Swal.fire({
                    title: "Create New Subscription?",
                    text: `This will create a new subscription record for "${userName}" based on subscription #${subscriptionId}. The current subscription will remain in the system and a new one will be added.`,
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonText: "Yes, create new subscription",
                    cancelButtonText: "No, cancel",
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#3085d6",
                    customClass: {
                        confirmButton: "swal2-confirm btn btn-success me-2 mt-2",
                        cancelButton: "swal2-cancel btn btn-secondary mt-2",
                    },
                    buttonsStyling: false,
                    showCloseButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = actionUrl;
                        form.style.display = 'none';
                        
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = csrfToken;
                        
                        form.appendChild(tokenInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        });
    </script>
@endpush

@section('content')
    <div class="page-container">
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Subscriptions Tables</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Subscriptions Tables</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom border-dashed">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <h4 class="header-title">Subscriptions Data</h4>
                            @if (Auth::check() && Auth::user()->hasRole('admin'))
                                <a class="btn btn-primary" href="{{ route('admin.subscriptions.create') }}" role="button">
                                    <i class="ti ti-plus me-1"></i>Add Subscription
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
                            <strong>Task 1:</strong> The ability to view subscription details is not yet active.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
