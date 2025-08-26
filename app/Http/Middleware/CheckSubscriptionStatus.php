<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Only apply to landlord users
        if ($user && $user->hasRole('landlord')) {
            $subscription = $user->activeSubscription();
            
            // Prepare default subscription status data
            $subscriptionData = [
                'active' => false,
                'status' => 'none',
                'plan' => null,
                'days_remaining' => 0,
                'payment_status' => null,
                'is_trial' => false,
            ];

            // Update status data based on subscription
            if ($subscription) {
                $subscriptionData['active'] = true;
                $subscriptionData['status'] = $subscription->status;
                $subscriptionData['plan'] = $subscription->subscriptionPlan->name ?? null;
                $subscriptionData['days_remaining'] = $subscription->days_remaining;
                $subscriptionData['payment_status'] = $subscription->payment_status;
                $subscriptionData['is_trial'] = $subscription->isInTrial();

                // Set flash alert for expiring or unpaid subscriptions
                if ($subscription->days_remaining <= 7) {
                    session()->flash('subscription_alert', [
                        'type' => 'warning',
                        'message' => "Your subscription will expire in {$subscription->days_remaining} days. Please renew to avoid service interruption."
                    ]);
                } elseif ($subscription->payment_status !== 'paid' && !$subscription->isInTrial()) {
                    session()->flash('subscription_alert', [
                        'type' => 'warning',
                        'message' => 'Your payment is pending. Some features may be restricted until payment is confirmed.'
                    ]);
                } else {
                     // Clear any previous alert if subscription is valid and paid
                    session()->forget('subscription_alert');
                }
            } else {
                 // No active subscription - get the latest and provide specific messages
                $latestSubscription = $user->latestSubscription();
                $statusMessage = 'You don\'t have an active subscription. Please subscribe to access all features.';
                
                if ($latestSubscription && $latestSubscription->status === 'cancelled') {
                    $statusMessage = 'Your subscription has been cancelled. Please subscribe to continue using all features.';
                } elseif ($latestSubscription && $latestSubscription->end_date && $latestSubscription->end_date < now()) {
                    $statusMessage = 'Your subscription has expired. Please renew to continue using all features.';
                }
                
                session()->flash('subscription_alert', [
                    'type' => 'danger',
                    'message' => $statusMessage
                ]);
            }
            
            // Store the final, clean status object in the session
            session(['subscription_status' => $subscriptionData]);
        }
        
        return $next($request);
    }
    
    // isAllowedWithoutSubscription method is no longer needed here, moved to CheckSubscription.php
}