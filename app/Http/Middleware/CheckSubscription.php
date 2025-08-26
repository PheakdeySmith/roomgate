<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // If not a landlord, continue (this middleware only applies to landlords)
        if (!$user || !$user->hasRole('landlord')) {
            return $next($request);
        }
        
        // Allow access to subscription-related routes even without a subscription
        if ($request->routeIs('landlord.subscription.*')) {
            return $next($request);
        }
        
        // Get the active subscription
        $subscription = $user->activeSubscription();
        
        // Check if user has an active subscription
        if (!$subscription) {
            Log::info("User {$user->id} ({$user->email}) has no active subscription - redirecting to subscription plans");
            return redirect()->route('landlord.subscription.plans')
                ->with('warning', 'You need an active subscription to access this feature. Please subscribe to a plan.');
        }
        
        // Check if subscription is expired (this handles database inconsistencies where status is 'active' but end_date has passed)
        if ($subscription->isExpired()) {
            Log::info("User {$user->id} ({$user->email}) has expired subscription - redirecting to subscription plans");
            return redirect()->route('landlord.subscription.plans')
                ->with('warning', 'Your subscription has expired. Please renew your subscription to continue using all features.');
        }
        
        // Check payment status
        if ($subscription->payment_status !== 'paid' && !$subscription->isInTrial()) {
            // Allow access to payment-related routes
            if ($request->routeIs('landlord.subscription.*') || $request->routeIs('landlord.profile.*')) {
                return $next($request);
            }
            
            // Extra check for common write operations that need to be blocked
            $method = $request->method();
            $path = $request->path();
            $action = $request->route()->getActionMethod();
            
            // Block specific actions for room_types and other resources
            $blockedActions = ['store', 'update', 'destroy', 'create', 'edit'];
            
            // Block POST, PUT, DELETE methods to resources
            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE']) || 
                in_array($action, $blockedActions) ||
                strpos($path, 'add') !== false ||
                strpos($path, 'create') !== false ||
                strpos($path, 'delete') !== false ||
                strpos($path, 'edit') !== false) {
                
                Log::info("Blocking write operation: {$method} {$path} ({$action})");
                
                // Using session flash directly to ensure the message appears
                session()->flash('error', 'Your subscription payment is pending. Please complete payment to access all features.');
                
                // Redirect to subscription plans for direct action
                if (request()->ajax()) {
                    return response()->json([
                        'error' => 'Your subscription payment is pending. Please complete payment to access all features.',
                        'redirect' => route('landlord.subscription.plans')
                    ], 403);
                }
                
                return redirect()->route('landlord.subscription.plans');
            }
            
            // For read-only routes (index, show), allow access
            if ($this->isReadOnlyRoute($request)) {
                return $next($request);
            }
            
            // For any other write operations, restrict access
            Log::info("User {$user->id} ({$user->email}) has unpaid subscription - restricting write access");
            
            // Using session flash directly to ensure the message appears
            session()->flash('error', 'Your subscription payment is pending. Please complete payment to access all features.');
            
            // Redirect to subscription plans for direct action
            if (request()->ajax()) {
                return response()->json([
                    'error' => 'Your subscription payment is pending. Please complete payment to access all features.',
                    'redirect' => route('landlord.subscription.plans')
                ], 403);
            }
            
            return redirect()->route('landlord.subscription.plans');
        }
        
        // Check resource limits based on the request
        if ($this->shouldCheckResourceLimits($request)) {
            $planLimits = $this->checkPlanLimits($user, $subscription, $request);
            
            if (!$planLimits['allowed']) {
                Log::info("User {$user->id} ({$user->email}) exceeded subscription limits: " . $planLimits['message']);
                return redirect()->back()
                    ->with('error', $planLimits['message']);
            }
        }
        
        return $next($request);
    }
    
    /**
     * Determine if we should check resource limits for this request
     */
    private function shouldCheckResourceLimits(Request $request): bool
    {
        // Check limits when creating new properties or rooms
        if ($request->routeIs('landlord.properties.store')) {
            return true;
        }
        
        if ($request->routeIs('landlord.rooms.store') || $request->routeIs('landlord.properties.rooms.store')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if the user's subscription plan limits are exceeded
     */
    private function checkPlanLimits($user, $subscription, $request): array
    {
        $plan = $subscription->subscriptionPlan;
        
        // Check property limits when creating a new property
        if ($request->routeIs('landlord.properties.store')) {
            $currentPropertyCount = $user->properties()->count();
            
            if ($currentPropertyCount >= $plan->properties_limit) {
                return [
                    'allowed' => false,
                    'message' => "You have reached the maximum number of properties ({$plan->properties_limit}) allowed in your subscription plan. Please upgrade your plan to add more properties."
                ];
            }
        }
        
        // Check room limits when creating a new room
        if ($request->routeIs('landlord.rooms.store') || $request->routeIs('landlord.properties.rooms.store')) {
            $currentRoomCount = $user->rooms()->count();
            
            if ($currentRoomCount >= $plan->rooms_limit) {
                return [
                    'allowed' => false,
                    'message' => "You have reached the maximum number of rooms ({$plan->rooms_limit}) allowed in your subscription plan. Please upgrade your plan to add more rooms."
                ];
            }
        }
        
        return ['allowed' => true, 'message' => ''];
    }
    
    /**
     * Determine if the request is for a read-only operation
     */
    private function isReadOnlyRoute(Request $request): bool
    {
        // Get route information
        $routeName = $request->route()->getName();
        $method = $request->method();
        
        // Always block POST, PUT, PATCH, DELETE methods - these are write operations
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            Log::info("Blocking write operation: {$method} {$routeName}");
            return false;
        }
        
        // Block routes that are for creating or editing resources
        if ($routeName && (
            str_ends_with($routeName, '.create') || 
            str_ends_with($routeName, '.edit') ||
            str_ends_with($routeName, '.store') ||
            str_ends_with($routeName, '.update') ||
            str_ends_with($routeName, '.destroy')
        )) {
            Log::info("Blocking write route: {$routeName}");
            return false;
        }
        
        // For direct resource actions (room_types.store, etc.)
        $writePatterns = [
            '/\.store$/',
            '/\.update$/',
            '/\.destroy$/',
            '/\.delete$/',
            '/\.create$/',
            '/\.edit$/',
            '/add/',
            '/create/',
            '/store/',
            '/save/',
            '/update/',
            '/delete/',
            '/remove/',
            '/destroy/'
        ];
        
        foreach ($writePatterns as $pattern) {
            if (preg_match($pattern, $routeName)) {
                Log::info("Blocking pattern match: {$routeName} matches {$pattern}");
                return false;
            }
        }
        
        // Allow all other routes (index, show, etc.)
        Log::info("Allowing read-only route: {$method} {$routeName}");
        return true;
    }
}
