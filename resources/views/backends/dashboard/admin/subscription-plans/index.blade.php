@extends('backends.layouts.app')

@section('title', 'Subscription Plans')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Subscription Plans</li>
                    </ol>
                </div>
                <h4 class="page-title">Subscription Plans</h4>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Manage Subscription Plans</h5>
                            <p class="text-muted mb-0">
                                Create and manage plans that determine landlord access and limits.
                            </p>
                        </div>
                        <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i> Create New Plan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Plan Name</th>
                                    <th>Price</th>
                                    <th>Duration</th>
                                    <th>Limits (Properties/Rooms)</th>
                                    <th>Active Subscribers</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($plan->is_featured)
                                                <i class="ti ti-star text-warning me-2" title="Featured Plan"></i>
                                            @endif
                                            <div>
                                                <h5 class="m-0">{{ $plan->name }}</h5>
                                                <small class="text-muted">Group: {{ $plan->plan_group }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="m-0">${{ number_format($plan->price, 2) }}</h5>
                                        <small class="text-muted">Base: ${{ number_format($plan->base_monthly_price, 2) }}/mo</small>
                                    </td>
                                    <td>
                                        {{ $plan->duration_days == 365 ? 'Annual' : ($plan->duration_days == 30 ? 'Monthly' : $plan->duration_days . ' days') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info">{{ $plan->properties_limit }}</span> /
                                        <span class="badge bg-primary-subtle text-primary">{{ $plan->rooms_limit }}</span>
                                    </td>
                                    <td>
                                        <h5 class="m-0">{{ $plan->user_subscriptions_count }}</h5>
                                    </td>
                                    <td>
                                        @if($plan->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.subscription-plans.edit', $plan->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.subscription-plans.destroy', $plan->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this plan?')">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-0">No subscription plans found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection