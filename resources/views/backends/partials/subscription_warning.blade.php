@php
    $activeSubscription = Auth::user()->activeSubscription();
@endphp
@if(Auth::user()->hasRole('landlord') && $activeSubscription && $activeSubscription->payment_status !== 'paid' && $activeSubscription->payment_status !== 'trial')
<div class="alert alert-warning alert-dismissible bg-warning-subtle text-warning fade show mb-0 rounded-0 mt-2" role="alert">
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0">
            <i class="ti ti-alert-triangle fs-18"></i>
        </div>
        <div class="flex-grow-1 ms-2">
            <strong>Subscription Payment Required!</strong> 
            Your subscription features are limited until payment is completed.
        </div>
        <div class="ms-auto me-2">
            <a href="{{ route('landlord.subscription.plans') }}" class="btn btn-warning btn-sm d-none d-sm-inline-block">Complete Payment</a>
            <a href="{{ route('landlord.subscription.plans') }}" class="btn btn-warning btn-icon btn-sm d-inline-block d-sm-none"><i class="ti ti-arrow-right"></i></a>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif
