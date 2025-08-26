/**
 * Navigation Hotfix for Subscription Issues
 * 
 * This script addresses navigation issues that can occur when a user has a cancelled
 * or inactive subscription by ensuring all links work correctly and aren't intercepted.
 * It also adds consistent notification handling for inactive subscriptions.
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Subscription Navigation Hotfix loaded');
    
    // Skip on subscription-related pages
    const currentUrl = window.location.href;
    const isSubscriptionPage = 
        currentUrl.includes('/subscription/plans') || 
        currentUrl.includes('/subscription/checkout') || 
        currentUrl.includes('/subscription-plans') ||
        currentUrl.includes('/landlord/subscription');
    
    if (isSubscriptionPage) {
        console.log('On subscription page - skipping subscription navigation hotfixes');
        return;
    }
    
    // Check for both payment status issues and inactive subscription status
    let hasUnpaidSubscription = document.body.classList.contains('has-unpaid-subscription');
    let hasInactiveSubscription = document.body.classList.contains('has-inactive-subscription');

    // Remove inactive/unpaid classes if subscription is valid
    const subscriptionStatusEl = document.getElementById('subscription-status-data');
    if (subscriptionStatusEl) {
        try {
            const subscriptionData = JSON.parse(subscriptionStatusEl.getAttribute('data-status'));
            if (subscriptionData && subscriptionData.active && subscriptionData.payment_status === 'paid') {
                document.body.classList.remove('has-inactive-subscription');
                document.body.classList.remove('has-unpaid-subscription');
                hasInactiveSubscription = false;
                hasUnpaidSubscription = false;
            }
        } catch (e) {
            console.error('Error parsing subscription status:', e);
        }
    }

    // If the body doesn't have the inactive class but should, add it
    const checkForInactiveSubscription = () => {
        // This uses the subscription_status data from session if available
        const subscriptionStatusEl = document.getElementById('subscription-status-data');
        if (subscriptionStatusEl) {
            try {
                const subscriptionData = JSON.parse(subscriptionStatusEl.getAttribute('data-status'));
                if (subscriptionData && (!subscriptionData.active || subscriptionData.status === 'inactive' || subscriptionData.status === 'cancelled')) {
                    document.body.classList.add('has-inactive-subscription');
                    return true;
                }
            } catch (e) {
                console.error('Error parsing subscription status:', e);
            }
        }
        // ...existing code...
        if (!hasInactiveSubscription && !hasUnpaidSubscription) {
            if (window.location.href.includes('/dashboard')) {
                const subscriptionBanner = document.querySelector('.subscription-warning-banner');
                if (subscriptionBanner) {
                    document.body.classList.add('has-inactive-subscription');
                    return true;
                }
            }
        }
        return hasInactiveSubscription;
    };
    
    const subscriptionHasIssues = hasUnpaidSubscription || hasInactiveSubscription || checkForInactiveSubscription();
    console.log('Subscription status check - Has issues:', subscriptionHasIssues, 
                '(Unpaid:', hasUnpaidSubscription, '| Inactive:', hasInactiveSubscription || checkForInactiveSubscription(), ')');
    
    // Ensure all navigation links work by directly handling their click events
    const fixNavLinks = () => {
        // Target all navigation links that might be affected
        const navLinks = document.querySelectorAll('.side-nav-link, .navbar-nav a, .dropdown-item');
        
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Skip links that are meant to trigger modals
                if (this.getAttribute('data-bs-toggle') === 'modal') {
                    return;
                }
                
                // Skip links without href
                if (!href || href === '#') {
                    return;
                }
                
                console.log('Navigation link clicked:', href);
                
                // For normal links, ensure navigation happens
                if (href && href !== '#' && !href.startsWith('javascript:')) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.location.href = href;
                }
            }, true); // Use capture phase to execute before other handlers
        });
    };
    
    // Fix logout button specifically
    const fixLogoutButton = () => {
        const logoutForms = document.querySelectorAll('form[action*="logout"]');
        
        logoutForms.forEach(form => {
            // Ensure the form has a proper action URL
            if (!form.action || form.action === '') {
                // Try to set a default logout URL if missing
                form.action = '/logout';
            }
            
            const logoutButtons = form.querySelectorAll('button[type="submit"]');
            
            logoutButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    console.log('Logout button clicked, submitting form directly');
                    
                    // Prevent other handlers from capturing this event
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Submit the form directly
                    try {
                        form.submit();
                    } catch (error) {
                        console.error('Error submitting logout form:', error);
                        
                        // Fallback: redirect to logout URL directly
                        window.location.href = '/logout';
                    }
                }, true); // Use capture phase
            });
        });
    };
    
    // Run the fixes
    fixNavLinks();
    fixLogoutButton();
    
    // Add a manual logout link for emergencies
    const addEmergencyLogout = () => {
        // Check if we're potentially stuck in a navigation loop
        const isStuckInLoop = 
            window.location.pathname.includes('dashboard') && 
            sessionStorage.getItem('dashboardLoadCount');
        
        if (isStuckInLoop) {
            // Increment dashboard load counter
            const loadCount = parseInt(sessionStorage.getItem('dashboardLoadCount') || '0');
            sessionStorage.setItem('dashboardLoadCount', (loadCount + 1).toString());
            
            // If loaded multiple times, add emergency logout
            if (loadCount > 2) {
                const navbar = document.querySelector('.navbar-nav');
                if (navbar) {
                    const emergencyLogout = document.createElement('li');
                    emergencyLogout.className = 'nav-item d-none d-lg-block';
                    emergencyLogout.innerHTML = `
                        <a href="/logout" class="nav-link btn btn-danger text-white" 
                           onclick="event.preventDefault(); document.getElementById('emergency-logout-form').submit();">
                            Emergency Logout
                        </a>
                        <form id="emergency-logout-form" action="/logout" method="POST" style="display: none;">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        </form>
                    `;
                    navbar.appendChild(emergencyLogout);
                    
                    // Attach direct event handler
                    const emergencyLogoutLink = emergencyLogout.querySelector('a');
                    emergencyLogoutLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        document.getElementById('emergency-logout-form').submit();
                    }, true);
                }
            }
        } else {
            // Reset counter if not on dashboard
            if (!window.location.pathname.includes('dashboard')) {
                sessionStorage.removeItem('dashboardLoadCount');
            } else {
                // Start counting dashboard loads
                sessionStorage.setItem('dashboardLoadCount', '1');
            }
        }
    };
    
    // Add emergency logout if needed
    addEmergencyLogout();
    
    // Handle inactive subscription notifications and redirects
    const handleInactiveSubscription = () => {
        const hasUnpaidSubscription = document.body.classList.contains('has-unpaid-subscription');
        const hasInactiveSubscription = document.body.classList.contains('has-inactive-subscription');
        
        // Skip if no subscription issues
        if (!hasUnpaidSubscription && !hasInactiveSubscription) {
            return;
        }
        
        // Get the current URL path
        const currentPath = window.location.pathname;
        
        // Skip notification on subscription plan pages
        if (currentPath.includes('/subscription/plans') || 
            currentPath.includes('/subscription/checkout') ||
            currentPath.includes('/subscription-plans')) {
            return;
        }
        
        // Block write operations the same way as unpaid subscriptions
        const writeButtons = document.querySelectorAll('.btn-primary, .btn-success, .add-new, button[data-bs-toggle="modal"]:not([data-subscription-checked])');
        
        writeButtons.forEach(button => {
            // Mark as checked to avoid duplicate handlers
            button.setAttribute('data-subscription-checked', 'true');
            
            // Only intercept buttons for adding/editing actions
            const buttonText = button.textContent.toLowerCase();
            if (buttonText.includes('add') || buttonText.includes('edit') || 
                buttonText.includes('create') || buttonText.includes('update') ||
                buttonText.includes('delete') || buttonText.includes('remove') ||
                buttonText.includes('new')) {
                
                button.addEventListener('click', function(e) {
                    // Skip if it's a navigation button
                    if (button.closest('a[href]') && !button.closest('a[href]').getAttribute('data-bs-toggle')) {
                        return true;
                    }
                    
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Show error message
                    showSubscriptionRequiredModal();
                    
                    return false;
                }, true);
            }
        });
        
        // Show inactive subscription notification once per session
        if (!sessionStorage.getItem('subscription_notification_shown')) {
            showSubscriptionRequiredModal();
            sessionStorage.setItem('subscription_notification_shown', 'true');
        }
    };
    
    // Function to show subscription required modal
    const showSubscriptionRequiredModal = () => {
        // Make sure SweetAlert2 is available
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 is not loaded');
            alert('Your subscription is inactive. Please renew your subscription to access all features.');
            return;
        }
        
        const subscriptionPlansUrl = window.subscriptionPlansUrl || '/landlord/subscription/plans';
        
        Swal.fire({
            title: 'Subscription Required',
            text: 'Your subscription is inactive or payment is pending. Please subscribe or complete payment to access all features.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Go to Subscription Plans',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-primary me-2 mt-2',
                cancelButton: 'btn btn-secondary me-2 mt-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to subscription plans
                window.location.href = subscriptionPlansUrl;
            }
        });
    };
    
    // Run the inactive subscription handler
    handleInactiveSubscription();
});
