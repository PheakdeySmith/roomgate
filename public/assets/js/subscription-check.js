/**
 * Consolidated Subscription Check & Blocking Script
 * Handles all client-side subscription-related logic.
 */

document.addEventListener('DOMContentLoaded', function() {
    // console.log('Consolidated Subscription Check script loaded');

    // Helper: Show modal
    const showSubscriptionRequiredModal = (isUnpaid) => {
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 is not loaded');
            alert('Your subscription is inactive. Please renew your subscription to access all features.');
            return;
        }

        const subscriptionPlansUrl = window.subscriptionPlansUrl || '/landlord/subscription/plans';
        const title = isUnpaid ? 'Subscription Payment Required' : 'Subscription Required';
        const text = isUnpaid
            ? 'Your subscription payment is pending. Please complete payment to access all features.'
            : 'Your subscription is inactive or missing. Please subscribe to access all features.';
        const confirmButtonText = isUnpaid ? 'Complete Payment' : 'Subscribe Now';

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: confirmButtonText,
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-primary me-2 mt-2',
                cancelButton: 'btn btn-secondary me-2 mt-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = subscriptionPlansUrl;
            }
        });
    };

    // Subscription status logic - read from server-rendered HTML
    let hasUnpaidSubscription = document.body.classList.contains('has-unpaid-subscription');
    let hasInactiveSubscription = document.body.classList.contains('has-inactive-subscription');
    
    // console.log('Initial Body Classes:', document.body.className);
    
    const subscriptionStatusEl = document.getElementById('subscription-status-data');
    if (subscriptionStatusEl) {
        try {
            const subscriptionData = JSON.parse(subscriptionStatusEl.getAttribute('data-status'));
            // console.log('Subscription status data:', subscriptionData);
            
            const isActiveAndPaid = subscriptionData && subscriptionData.active === true && subscriptionData.payment_status === 'paid';
            
            if (isActiveAndPaid) {
                document.body.classList.remove('has-inactive-subscription');
                document.body.classList.remove('has-unpaid-subscription');
                hasInactiveSubscription = false;
                hasUnpaidSubscription = false;
            } else {
                if (!subscriptionData.active || subscriptionData.status === 'inactive' || subscriptionData.status === 'cancelled') {
                    document.body.classList.add('has-inactive-subscription');
                    hasInactiveSubscription = true;
                }
                if (subscriptionData.payment_status !== 'paid') {
                    document.body.classList.add('has-unpaid-subscription');
                    hasUnpaidSubscription = true;
                }
            }
        } catch (e) {
            console.error('Error parsing subscription status:', e);
            // Fallback to the classes already set on the body by the server
        }
    }
    
    // Final check for the body classes after JS manipulation
    // console.log('Final Body Classes:', document.body.className);


    // Block write operations if subscription has issues
    if (hasUnpaidSubscription || hasInactiveSubscription) {
        // Block form submissions
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const method = form.getAttribute('method')?.toUpperCase() || 'GET';
            if (method === 'POST' || form.querySelector('input[name="_method"]')) {
                e.preventDefault();
                showSubscriptionRequiredModal(hasUnpaidSubscription);
                return false;
            }
        }, true);

        // Block modal buttons
        document.addEventListener('click', function(e) {
            const button = e.target.closest('button[data-bs-toggle="modal"], a[data-bs-toggle="modal"], .add-new, .btn-primary, .btn-success');
            if (button) {
                const buttonText = button.textContent.toLowerCase();
                if (["add", "edit", "create", "update", "delete", "remove", "new"].some(word => buttonText.includes(word))) {
                    e.preventDefault();
                    e.stopPropagation();
                    showSubscriptionRequiredModal(hasUnpaidSubscription);
                    return false;
                }
            }
        }, true);
        
        // Block delete buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-type') || e.target.closest('.btn-danger')) {
                e.preventDefault();
                e.stopPropagation();
                showSubscriptionRequiredModal(hasUnpaidSubscription);
                return false;
            }
        }, true);
        
        // Show notification once per session
        if (!sessionStorage.getItem('subscription_notification_shown') && !window.location.href.includes('/landlord/subscription')) {
            showSubscriptionRequiredModal(hasUnpaidSubscription);
            sessionStorage.setItem('subscription_notification_shown', 'true');
        }
    }
});