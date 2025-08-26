/**
 * Debug Navigation Script
 * This script helps identify issues with link navigation
 */

document.addEventListener('DOMContentLoaded', function() {
    // console.log('Debug Navigation Script loaded');
    
    // Function to log navigation events
    function logClickEvent(e) {
        const target = e.target;
        const closestLink = target.closest('a');
        
        if (closestLink) {
            console.log('Link clicked:', {
                href: closestLink.getAttribute('href'),
                text: closestLink.textContent.trim(),
                tagName: target.tagName,
                classes: target.className,
                defaultPrevented: e.defaultPrevented,
                eventPhase: e.eventPhase
            });
            
            // Check if any parent has event listeners that might be interfering
            let currentNode = closestLink;
            while (currentNode && currentNode !== document) {
                if (currentNode._eventListeners && currentNode._eventListeners.click) {
                    console.log('Found click listener on parent:', currentNode);
                }
                currentNode = currentNode.parentNode;
            }
        }
    }
    
    // Function to log form submissions
    function logFormSubmit(e) {
        console.log('Form submission:', {
            action: e.target.action,
            method: e.target.method,
            defaultPrevented: e.defaultPrevented
        });
    }
    
    // Add logging to all click events
    document.addEventListener('click', logClickEvent, true);
    
    // Add logging to all form submissions
    document.addEventListener('submit', logFormSubmit, true);
    
    // Monitor navigation events
    window.addEventListener('beforeunload', function(e) {
        console.log('Navigation away from page');
    });
    
    // Patch history methods to log navigation
    const originalPushState = history.pushState;
    history.pushState = function() {
        console.log('History pushState called with:', arguments);
        return originalPushState.apply(this, arguments);
    };
    
    const originalReplaceState = history.replaceState;
    history.replaceState = function() {
        console.log('History replaceState called with:', arguments);
        return originalReplaceState.apply(this, arguments);
    };
    
    // Fix for sidebar links
    const sidenavLinks = document.querySelectorAll('.side-nav-link');
    sidenavLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            console.log('Sidebar link clicked:', this.href);
            // Force navigation by setting window.location directly
            if (this.href) {
                window.location.href = this.href;
            }
        });
    });
    
    // Fix for logout button
    const logoutForm = document.querySelector('form[action*="logout"]');
    if (logoutForm) {
        const logoutButton = logoutForm.querySelector('button[type="submit"]');
        if (logoutButton) {
            logoutButton.addEventListener('click', function(e) {
                console.log('Logout button clicked');
                // Bypass the default event handler and submit the form directly
                e.stopPropagation();
                e.preventDefault();
                logoutForm.submit();
            }, true);
        }
    }
});
