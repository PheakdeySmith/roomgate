@extends('backends.layouts.app')

@section('title', 'Edit User Subscription')

@push('style')
<link href="{{ asset('assets/css/vendor/dataTables.bootstrap5.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/vendor/responsive.bootstrap5.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">Edit Subscription</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit User Subscription</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Edit Subscription Details</h5>

                    <form action="{{ route('admin.subscriptions.update', $subscription->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Landlord User</label>
                            <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">Select a Landlord</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $subscription->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subscription_plan_id" class="form-label">Subscription Plan</label>
                            <select name="subscription_plan_id" id="subscription_plan_id" class="form-select @error('subscription_plan_id') is-invalid @enderror">
                                <option value="">Select a Subscription Plan</option>
                                @foreach($subscriptionPlans as $plan)
                                    <option value="{{ $plan->id }}" {{ old('subscription_plan_id', $subscription->subscription_plan_id) == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} ({{ $plan->formatted_price }} / {{ $plan->formatted_duration }})
                                    </option>
                                @endforeach
                            </select>
                            @error('subscription_plan_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" 
                                           value="{{ old('start_date', $subscription->start_date->format('Y-m-d')) }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" 
                                           value="{{ old('end_date', $subscription->end_date->format('Y-m-d')) }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="active" {{ old('status', $subscription->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="canceled" {{ old('status', $subscription->status) == 'canceled' ? 'selected' : '' }}>Canceled</option>
                                        <option value="expired" {{ old('status', $subscription->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_status" class="form-label">Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-select @error('payment_status') is-invalid @enderror">
                                        <option value="paid" {{ old('payment_status', $subscription->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="pending" {{ old('payment_status', $subscription->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="trial" {{ old('payment_status', $subscription->payment_status) == 'trial' ? 'selected' : '' }}>Trial</option>
                                        <option value="failed" {{ old('payment_status', $subscription->payment_status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                    @error('payment_status')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount_paid" class="form-label">Amount Paid</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control @error('amount_paid') is-invalid @enderror" 
                                               id="amount_paid" name="amount_paid" 
                                               value="{{ old('amount_paid', $subscription->amount_paid) }}">
                                    </div>
                                    @error('amount_paid')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                                        <option value="">None</option>
                                        <option value="credit_card" {{ old('payment_method', $subscription->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                        <option value="paypal" {{ old('payment_method', $subscription->payment_method) == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                        <option value="bank_transfer" {{ old('payment_method', $subscription->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="cash" {{ old('payment_method', $subscription->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="transaction_id" class="form-label">Transaction ID (Optional)</label>
                            <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" 
                                   id="transaction_id" name="transaction_id" 
                                   value="{{ old('transaction_id', $subscription->transaction_id) }}">
                            @error('transaction_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $subscription->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Subscription</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Current Subscription Information</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">User:</span>
                            <span class="fw-medium">{{ $subscription->user->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Plan:</span>
                            <span class="fw-medium">{{ $subscription->subscriptionPlan->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Status:</span>
                            <span class="fw-medium">
                                @if($subscription->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($subscription->status == 'canceled')
                                    <span class="badge bg-danger">Canceled</span>
                                @else
                                    <span class="badge bg-warning">Expired</span>
                                @endif
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Payment Status:</span>
                            <span class="fw-medium">
                                @if($subscription->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($subscription->payment_status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($subscription->payment_status == 'trial')
                                    <span class="badge bg-info">Trial</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Duration:</span>
                            <span class="fw-medium">{{ $subscription->start_date->format('M d, Y') }} - {{ $subscription->end_date->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Amount Paid:</span>
                            <span class="fw-medium">{{ $subscription->formatted_amount_paid }}</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Plan Features</h6>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Properties Limit:</span>
                        <span class="fw-medium">{{ $subscription->subscriptionPlan->properties_limit }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Rooms Limit:</span>
                        <span class="fw-medium">{{ $subscription->subscriptionPlan->rooms_limit }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Analytics:</span>
                        <span class="fw-medium">{{ $subscription->subscriptionPlan->has_analytics ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Reports:</span>
                        <span class="fw-medium">{{ $subscription->subscriptionPlan->has_reports ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">API Access:</span>
                        <span class="fw-medium">{{ $subscription->subscriptionPlan->has_api_access ? 'Yes' : 'No' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Quick Links</h5>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="btn btn-outline-info">
                            <i class="ti ti-eye me-1"></i> View Subscription Details
                        </a>
                        <a href="{{ route('admin.users.edit', $subscription->user_id) }}" class="btn btn-outline-primary">
                            <i class="ti ti-user me-1"></i> Edit Landlord Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
