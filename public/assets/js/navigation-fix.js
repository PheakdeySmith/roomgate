/**
 * Navigation Fix Script
 * This script fixes issues with link navigation and logout functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // console.log('Navigation Fix Script loaded');
    
    // Fix for sidebar links
    const sidenavLinks = document.querySelectorAll('.side-nav-link, .navbar-nav a');
    sidenavLinks.forEach(link => {
        // Remove any existing click handlers by cloning and replacing the element
        const newLink = link.cloneNode(true);
        if (link.parentNode) {
            link.parentNode.replaceChild(newLink, link);
        }
        
        // Add a direct navigation handler
        newLink.addEventListener('click', function(e) {
            console.log('Link clicked, navigating to:', this.href);
            // Prevent any other event handlers
            e.preventDefault();
            e.stopPropagation();
            
            // Force navigation by setting window.location directly
            if (this.href) {
                window.location.href = this.href;
            }
            
            return false;
        }, true);
    });
    
    // Fix for logout button
    const logoutForms = document.querySelectorAll('form[action*="logout"]');
    logoutForms.forEach(form => {
        const logoutButton = form.querySelector('button[type="submit"]');
        if (logoutButton) {
            // Remove existing click handlers
            const newButton = logoutButton.cloneNode(true);
            logoutButton.parentNode.replaceChild(newButton, logoutButton);
            
            // Add direct submission handler
            newButton.addEventListener('click', function(e) {
                console.log('Logout button clicked, submitting form');
                // Prevent default handling
                e.preventDefault();
                e.stopPropagation();
                
                // Submit the form directly
                form.submit();
                
                return false;
            }, true);
        }
    });
    
    // Disable any global event handlers that might be interfering with navigation
    // This targets specifically the subscription-check.js global event handler
    if (window.disableSubscriptionChecks) {
        const originalAddEventListener = EventTarget.prototype.addEventListener;
        EventTarget.prototype.addEventListener = function(type, listener, options) {
            // Skip global click listeners
            if (this === document && type === 'click') {
                console.log('Blocked global click listener');
                return;
            }
            return originalAddEventListener.call(this, type, listener, options);
        };
    }
});
