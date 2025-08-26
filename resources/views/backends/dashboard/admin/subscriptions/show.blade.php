@extends('backends.layouts.app')

@section('title', 'Subscription Details')

@push('style')
<link href="{{ asset('assets') }}/css/sweetalert2.min.css" rel="stylesheet" type="text/css">
<style>
    .subscription-info {
        margin-bottom: 2rem;
    }
    
    .subscription-info .title {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .subscription-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.25rem;
    }
    
    .landlord-info {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .landlord-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-right: 1rem;
    }
    
    .landlord-details h5 {
        margin-bottom: 0.25rem;
    }
    
    .plan-details {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .subscription-dates {
        display: flex;
        margin-bottom: 1.5rem;
    }
    
    .date-box {
        flex: 1;
        padding: 1rem;
        border-radius: 0.5rem;
        text-align: center;
    }
    
    .date-box:first-child {
        margin-right: 1rem;
        background-color: #e8f3ff;
    }
    
    .date-box:last-child {
        background-color: #fff8e8;
    }
    
    .date-box .title {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    .date-box .value {
        font-size: 1.25rem;
        font-weight: 600;
    }
    
    .payment-info {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 1.5rem;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .info-label {
        color: #6c757d;
    }
    
    .info-value {
        font-weight: 500;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        margin-top: 2rem;
    }
    
    /* New styles for the improved cards */
    .subscription-milestones .milestone-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .subscription-milestones .list-group-item {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        position: relative;
    }
    
    .subscription-milestones .list-group-item:not(:last-child):after {
        content: '';
        position: absolute;
        left: 20px;
        top: 55px;
        height: calc(100% - 30px);
        width: 1px;
        background-color: #e9ecef;
    }
    
    .card.shadow-sm {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 0.75rem;
        overflow: hidden;
    }
    
    .card.shadow-sm:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.index') }}">User Subscriptions</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </div>
                <h4 class="page-title">Subscription Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">Subscription #{{ $subscription->id }}</h5>
                        <div>
                            @if($subscription->status == 'active')
                                <span class="subscription-badge bg-success-subtle text-success">Active</span>
                            @elseif($subscription->status == 'canceled')
                                <span class="subscription-badge bg-danger-subtle text-danger">Canceled</span>
                            @else
                                <span class="subscription-badge bg-warning-subtle text-warning">Expired</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="landlord-info">
                        <img src="{{ $subscription->user->image ? asset($subscription->user->image) : asset('assets/images/default_image.png') }}" 
                            class="landlord-avatar" alt="Landlord Avatar">
                        <div class="landlord-details">
                            <h5>{{ $subscription->user->name }}</h5>
                            <p class="text-muted mb-1">{{ $subscription->user->email }}</p>
                            <p class="text-muted mb-0">{{ $subscription->user->phone }}</p>
                        </div>
                    </div>
                    
                    <div class="plan-details">
                        <h6 class="mb-3">Subscription Plan</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">{{ $subscription->subscriptionPlan->name }}</h5>
                            <h5 class="mb-0 text-primary">{{ $subscription->subscriptionPlan->formatted_price }}</h5>
                        </div>
                        <p class="mb-3">{{ $subscription->subscriptionPlan->description }}</p>
                        <div class="row">
                            <div class="col-6">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Duration:</span>
                                    <span class="fw-medium">{{ $subscription->subscriptionPlan->formatted_duration }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Properties:</span>
                                    <span class="fw-medium">{{ $subscription->subscriptionPlan->properties_limit }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Rooms:</span>
                                    <span class="fw-medium">{{ $subscription->subscriptionPlan->rooms_limit }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Days Remaining:</span>
                                    <span class="fw-medium">{{ $subscription->days_remaining }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="subscription-dates">
                        <div class="date-box">
                            <div class="title">Start Date</div>
                            <div class="value">{{ $subscription->start_date->format('M d, Y') }}</div>
                        </div>
                        <div class="date-box">
                            <div class="title">End Date</div>
                            <div class="value">{{ $subscription->end_date->format('M d, Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="payment-info">
                        <h6 class="mb-3">Payment Information</h6>
                        <div class="info-item">
                            <div class="info-label">Payment Status</div>
                            <div class="info-value">
                                @if($subscription->payment_status == 'paid')
                                    <span class="subscription-badge bg-success-subtle text-success">Paid</span>
                                @elseif($subscription->payment_status == 'pending')
                                    <span class="subscription-badge bg-warning-subtle text-warning">Pending</span>
                                @elseif($subscription->payment_status == 'trial')
                                    <span class="subscription-badge bg-info-subtle text-info">Trial</span>
                                @else
                                    <span class="subscription-badge bg-danger-subtle text-danger">Failed</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Amount Paid</div>
                            <div class="info-value">{{ $subscription->formatted_amount_paid }}</div>
                        </div>
                        @if($subscription->payment_method)
                        <div class="info-item">
                            <div class="info-label">Payment Method</div>
                            <div class="info-value">{{ ucfirst($subscription->payment_method) }}</div>
                        </div>
                        @endif
                        @if($subscription->transaction_id)
                        <div class="info-item">
                            <div class="info-label">Transaction ID</div>
                            <div class="info-value">{{ $subscription->transaction_id }}</div>
                        </div>
                        @endif
                    </div>
                    
                    @if($subscription->notes)
                    <div class="mt-4">
                        <h6 class="mb-2">Notes</h6>
                        <p class="mb-0">{{ $subscription->notes }}</p>
                    </div>
                    @endif
                    
                    <div class="action-buttons">
                        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Back to Subscriptions
                        </a>
                        @if($subscription->status == 'active')
                        <button type="button" class="btn btn-warning cancel-subscription" 
                            data-subscription-id="{{ $subscription->id }}"
                            data-user-name="{{ $subscription->user->name }}"
                            data-action-url="{{ route('admin.subscriptions.cancel', $subscription->id) }}">
                            <i class="ti ti-x me-1"></i> Cancel Subscription
                        </button>
                        @endif
                        <button type="button" class="btn btn-success create-new-subscription"
                            data-subscription-id="{{ $subscription->id }}"
                            data-user-name="{{ $subscription->user->name }}"
                            data-action-url="{{ route('admin.subscriptions.renew', $subscription->id) }}">
                            <i class="ti ti-plus me-1"></i> Create New Subscription
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history fs-18 text-primary me-2"></i>
                        <h5 class="card-title mb-0">Subscription Timeline</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="timeline-vertical">
                        <div class="timeline-item pb-4">
                            <div class="d-flex">
                                <div class="timeline-indicator">
                                    <div class="timeline-icon bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; border: 2px solid rgba(0,0,0,0.05);">
                                        <i class="ti ti-credit-card"></i>
                                    </div>
                                </div>
                                <div class="timeline-content ms-3 pb-3" style="border-left: 1px solid rgba(0,0,0,0.1); padding-left: 20px; margin-left: -19px;">
                                    <div class="bg-light p-3 rounded-3">
                                        <h6 class="mb-1">Subscription Created</h6>
                                        <p class="text-muted small mb-0">{{ $subscription->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($subscription->payment_status == 'paid')
                        <div class="timeline-item pb-4">
                            <div class="d-flex">
                                <div class="timeline-indicator">
                                    <div class="timeline-icon bg-success text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; border: 2px solid rgba(0,0,0,0.05);">
                                        <i class="ti ti-check"></i>
                                    </div>
                                </div>
                                <div class="timeline-content ms-3 pb-3" style="border-left: 1px solid rgba(0,0,0,0.1); padding-left: 20px; margin-left: -19px;">
                                    <div class="bg-light p-3 rounded-3">
                                        <h6 class="mb-1">Payment Completed</h6>
                                        <p class="text-muted small mb-0">{{ $subscription->updated_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($subscription->status == 'canceled')
                        <div class="timeline-item pb-4">
                            <div class="d-flex">
                                <div class="timeline-indicator">
                                    <div class="timeline-icon bg-danger text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; border: 2px solid rgba(0,0,0,0.05);">
                                        <i class="ti ti-x"></i>
                                    </div>
                                </div>
                                <div class="timeline-content ms-3 pb-3" style="border-left: 1px solid rgba(0,0,0,0.1); padding-left: 20px; margin-left: -19px;">
                                    <div class="bg-light p-3 rounded-3">
                                        <h6 class="mb-1">Subscription Canceled</h6>
                                        <p class="text-muted small mb-0">{{ $subscription->updated_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($subscription->isExpired())
                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="timeline-indicator">
                                    <div class="timeline-icon bg-warning text-dark d-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; border: 2px solid rgba(0,0,0,0.05);">
                                        <i class="ti ti-alert-triangle"></i>
                                    </div>
                                </div>
                                <div class="timeline-content ms-3" style="padding-left: 20px; margin-left: -19px;">
                                    <div class="bg-light p-3 rounded-3">
                                        <h6 class="mb-1">Subscription Expired</h6>
                                        <p class="text-muted small mb-0">{{ $subscription->end_date->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @elseif($subscription->status == 'active')
                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="timeline-indicator">
                                    <div class="timeline-icon bg-info text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; border: 2px solid rgba(0,0,0,0.05);">
                                        <i class="ti ti-calendar-event"></i>
                                    </div>
                                </div>
                                <div class="timeline-content ms-3" style="padding-left: 20px; margin-left: -19px;">
                                    <div class="bg-light p-3 rounded-3">
                                        <h6 class="mb-1">Expires On</h6>
                                        <p class="text-muted small mb-0">{{ $subscription->end_date->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-light py-3">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-bolt fs-18 text-warning me-2"></i>
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="action-buttons">
                        <a href="{{ url('/admin/users/' . $subscription->user_id) }}" 
                           class="btn btn-primary w-100 mb-3 d-flex align-items-center justify-content-center">
                            <i class="ti ti-user me-2"></i> View Landlord Profile
                        </a>
                        <a href="{{ route('admin.subscriptions.create') }}" 
                           class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center">
                            <i class="ti ti-plus me-2"></i> Create New Subscription
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>
<script>
    // Wait for the DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Get the CSRF token once
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfMeta) {
            console.error('CSRF token meta tag not found in document head.');
        }
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
        
        // Cancel subscription button
        const cancelButton = document.querySelector('.cancel-subscription');
        if (cancelButton) {
            cancelButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                const subscriptionId = this.getAttribute('data-subscription-id');
                const userName = this.getAttribute('data-user-name') || 'this subscription';
                const actionUrl = this.getAttribute('data-action-url');
                
                if (!actionUrl) {
                    console.error('Cancel action URL not found on button');
                    Swal.fire('Error!', 'Cannot proceed with cancellation. Action URL is missing.', 'error');
                    return;
                }
                
                if (!csrfToken) {
                    Swal.fire('Error!', 'Cannot proceed: CSRF token not found.', 'error');
                    return;
                }
                
                Swal.fire({
                    title: "Are you sure?",
                    text: `Subscription #${subscriptionId} for "${userName}" will be canceled. This action can be undone by creating a new subscription later.`,
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
            });
        }
        
        // Create new subscription button
        const renewButton = document.querySelector('.create-new-subscription');
        if (renewButton) {
            renewButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                const subscriptionId = this.getAttribute('data-subscription-id');
                const userName = this.getAttribute('data-user-name') || 'this subscription';
                const actionUrl = this.getAttribute('data-action-url');
                
                if (!actionUrl) {
                    console.error('Renew action URL not found on button');
                    Swal.fire('Error!', 'Cannot proceed with creating new subscription. Action URL is missing.', 'error');
                    return;
                }
                
                if (!csrfToken) {
                    Swal.fire('Error!', 'Cannot proceed: CSRF token not found.', 'error');
                    return;
                }
                
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
            });
        }
    });
</script>
@endpush
