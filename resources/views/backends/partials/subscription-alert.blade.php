<div class="subscription-alert-container">
    @if(session('subscription_alert'))
        <div class="alert alert-{{ session('subscription_alert')['type'] }} alert-dismissible fade show" role="alert">
            <div class="d-flex">
                <div class="flex-shrink-0">
                    @if(session('subscription_alert')['type'] === 'danger')
                        <i class="ti ti-alert-circle fs-4 me-2"></i>
                    @elseif(session('subscription_alert')['type'] === 'warning')
                        <i class="ti ti-alert-triangle fs-4 me-2"></i>
                    @else
                        <i class="ti ti-info-circle fs-4 me-2"></i>
                    @endif
                </div>
                <div class="flex-grow-1 ms-2">
                    <strong>Subscription Notice:</strong> {{ session('subscription_alert')['message'] }}
                    
                    @if(session('subscription_alert')['type'] === 'danger' || (session('subscription_status.days_remaining', 0) <= 7 && session('subscription_status.active', false)))
                        <a href="{{ route('landlord.subscription.plans') }}" class="btn btn-sm btn-outline-{{ session('subscription_alert')['type'] }} ms-3">
                            {{ session('subscription_status.active', false) ? 'Renew Subscription' : 'Subscribe Now' }}
                        </a>
                    @endif
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<style>
    .subscription-alert-container {
        position: relative;
        z-index: 999;
        margin-bottom: 1rem;
    }
</style>
