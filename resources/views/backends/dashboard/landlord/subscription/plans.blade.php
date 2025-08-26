@extends('backends.layouts.app')

@section('title', 'Subscription Plans')

@push('style')
<style>
    .pricing-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .pricing-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .pricing-highlight {
        border: 2px solid var(--bs-primary);
    }
    
    .pricing-feature-list {
        list-style: none;
        padding-left: 0;
    }
    
    .pricing-feature-list li {
        padding: 8px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .pricing-feature-list li:last-child {
        border-bottom: none;
    }
    
    .pricing-feature-list i.ti-check {
        color: var(--bs-success);
    }
    
    .pricing-feature-list i.ti-x {
        color: var(--bs-danger);
    }
    
    .plan-badge {
        position: absolute;
        top: 0;
        right: 0;
        padding: 5px 15px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 0 4px 0 15px;
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
                        <li class="breadcrumb-item active">Subscription Plans</li>
                    </ol>
                </div>
                <h4 class="page-title">Choose Your Subscription Plan</h4>
            </div>
        </div>
    </div>
    
    @if(isset($activeSubscription))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Your Current Subscription</h5>
                            <p class="text-muted mb-0">{{ $activeSubscription->subscriptionPlan->name }} - Valid until {{ $activeSubscription->end_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <span class="badge bg-{{ $activeSubscription->isInTrial() ? 'info' : 'success' }} p-2">
                                {{ $activeSubscription->isInTrial() ? 'Trial' : 'Active' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <div class="row">
        @foreach($plans as $plan)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card pricing-card h-100 {{ $activeSubscription && $activeSubscription->subscription_plan_id == $plan->id ? 'pricing-highlight' : '' }}">
                @if($plan->is_featured)
                <div class="plan-badge bg-warning">Popular</div>
                @endif
                
                <div class="card-body">
                    <h5 class="card-title text-center">{{ $plan->name }}</h5>
                    <div class="text-center my-4">
                        <h2 class="mb-0 fw-bold">${{ number_format($plan->price, 2) }}</h2>
                        <p class="text-muted">for {{ $plan->duration_days }} days</p>
                    </div>
                    
                    <ul class="pricing-feature-list mb-4">
                        @php 
                            $features = json_decode($plan->features, true) ?? [];
                        @endphp
                        
                        @foreach($features as $feature => $enabled)
                            <li>
                                <i class="ti {{ $enabled ? 'ti-check' : 'ti-x' }} me-2"></i>
                                {{ ucwords(str_replace('_', ' ', $feature)) }}
                            </li>
                        @endforeach
                    </ul>
                    
                    <div class="text-center">
                        <a href="{{ route('landlord.subscription.checkout', $plan->id) }}" 
                           class="btn {{ $activeSubscription && $activeSubscription->subscription_plan_id == $plan->id ? 'btn-outline-primary' : 'btn-primary' }} w-100">
                            {{ $activeSubscription && $activeSubscription->subscription_plan_id == $plan->id ? 'Current Plan' : 'Choose Plan' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
