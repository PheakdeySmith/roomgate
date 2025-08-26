@extends('backends.layouts.app')

@section('title', 'Subscription Success')

@push('style')
<style>
    .success-card {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .success-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background-color: rgba(25, 135, 84, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .success-icon i {
        font-size: 40px;
        color: var(--bs-success);
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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Subscription Success</li>
                    </ol>
                </div>
                <h4 class="page-title">Subscription Success</h4>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card success-card">
                <div class="card-body text-center p-5">
                    <div class="success-icon">
                        <i class="ti ti-check"></i>
                    </div>
                    
                    <h3 class="mb-3">Payment Successful!</h3>
                    <p class="text-muted mb-4">Thank you for your subscription. Your payment has been processed successfully.</p>
                    
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="text-start">
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Transaction ID:</div>
                                    <div class="col-6 fw-bold">{{ $subscription->transaction_id }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Subscription Plan:</div>
                                    <div class="col-6">{{ $subscription->subscriptionPlan->name }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Amount Paid:</div>
                                    <div class="col-6">${{ number_format($subscription->amount_paid, 2) }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6 text-muted">Start Date:</div>
                                    <div class="col-6">{{ $subscription->start_date->format('M d, Y') }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-muted">End Date:</div>
                                    <div class="col-6">{{ $subscription->end_date->format('M d, Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                        <a href="#" class="btn btn-outline-secondary" onclick="window.print()">Print Receipt</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
