<div class="subscription-warning-banner">
    @php
        // Don't show warning banner on subscription-related pages
        $currentUrl = url()->current();
        $isSubscriptionPage = 
            Str::contains($currentUrl, '/subscription/plans') || 
            Str::contains($currentUrl, '/subscription/checkout') || 
            Str::contains($currentUrl, '/subscription-plans') ||
            Str::contains($currentUrl, '/landlord/subscription');
    @endphp

    @if(Auth::user()->hasRole('landlord') && !$isSubscriptionPage)
        @php
            $activeSubscription = Auth::user()->activeSubscription();
        @endphp

        @if(!$activeSubscription)
            <div class="alert alert-warning alert-dismissible fade show mb-0 mt-2" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-triangle fs-24 me-2"></i>
                    <div>
                        <strong>Subscription Required!</strong> 
                        You don't have an active subscription. Please <a href="{{ route('landlord.subscription.plans') }}" class="alert-link">subscribe</a> to access all features.
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('landlord.subscription.plans') }}" class="btn btn-warning btn-sm d-none d-sm-inline-block">Subscribe Now</a>
                        <a href="{{ route('landlord.subscription.plans') }}" class="btn btn-warning btn-icon btn-sm d-inline-block d-sm-none"><i class="ti ti-arrow-right"></i></a>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif($activeSubscription->payment_status !== 'paid' && $activeSubscription->payment_status !== 'trial')
            <div class="alert alert-info alert-dismissible fade show mb-0 mt-2" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-credit-card fs-24 me-2"></i>
                    <div>
                        <strong>Payment Required!</strong> Your subscription payment is pending. Please complete payment to access all features.
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('landlord.subscription.plans') }}" class="btn btn-info btn-sm d-none d-sm-inline-block">Complete Payment</a>
                        <a href="{{ route('landlord.subscription.plans') }}" class="btn btn-info btn-icon btn-sm d-inline-block d-sm-none"><i class="ti ti-arrow-right"></i></a>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endif
</div>
