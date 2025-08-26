@extends('backends.layouts.app')

@section('title', 'User Details')

@push('style')
<style>
    .user-info {
        margin-bottom: 2rem;
    }
    
    .user-info .title {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .user-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.25rem;
    }
    
    .user-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        margin-bottom: 1.5rem;
        object-fit: cover;
        border: 5px solid #f8f9fa;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .user-details {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
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
                        <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Back</a></li>
                        <li class="breadcrumb-item active">User Details</li>
                    </ol>
                </div>
                <h4 class="page-title">User Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">User #{{ $user->id }}</h5>
                        <div>
                            @if($user->status == 'active')
                                <span class="user-badge bg-success-subtle text-success">Active</span>
                            @else
                                <span class="user-badge bg-danger-subtle text-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-center mb-4">
                        <img src="{{ $user->image ? asset($user->image) : asset('assets/images/default_image.png') }}" 
                            class="user-avatar" alt="User Avatar">
                        <h4>{{ $user->name }}</h4>
                        @foreach($user->roles as $role)
                            @if($role->name == 'admin')
                                <span class="user-badge bg-danger-subtle text-danger">Admin</span>
                            @elseif($role->name == 'landlord')
                                <span class="user-badge bg-primary-subtle text-primary">Landlord</span>
                            @elseif($role->name == 'tenant')
                                <span class="user-badge bg-info-subtle text-info">Tenant</span>
                            @else
                                <span class="user-badge bg-secondary-subtle text-secondary">{{ ucfirst($role->name) }}</span>
                            @endif
                        @endforeach
                    </div>
                    
                    <div class="user-details">
                        <h6 class="mb-3">Personal Information</h6>
                        <div class="info-item">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">{{ $user->name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email Address</div>
                            <div class="info-value">{{ $user->email }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value">{{ $user->phone ?? 'Not provided' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                @if($user->status == 'active')
                                    <span class="user-badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="user-badge bg-danger-subtle text-danger">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Joined Date</div>
                            <div class="info-value">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Quick Actions</h5>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Back to Previous Page
                        </a>
                    </div>
                </div>
            </div>
            
            @if($user->hasRole('landlord'))
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Subscription Status</h5>
                    
                    @if($user->subscriptions && $user->subscriptions->where('status', 'active')->count() > 0)
                        @php
                            $activeSubscription = $user->subscriptions->where('status', 'active')->first();
                        @endphp
                        <div class="alert alert-success mb-3">
                            <h6 class="mb-1">Active Subscription</h6>
                            <p class="mb-0">{{ $activeSubscription->subscriptionPlan->name }}</p>
                            <small class="text-muted">Valid until: {{ $activeSubscription->end_date->format('M d, Y') }}</small>
                        </div>
                        <a href="{{ route('admin.subscriptions.show', $activeSubscription->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-eye me-1"></i> View Subscription
                        </a>
                    @else
                        <div class="alert alert-warning mb-3">
                            <h6 class="mb-1">No Active Subscription</h6>
                            <p class="mb-0">This landlord doesn't have an active subscription.</p>
                        </div>
                        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-plus me-1"></i> Create Subscription
                        </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
