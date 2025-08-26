<?php

namespace App\Http\Controllers\Landlord;

use App\Models\UserSubscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SubscriptionStatusController extends Controller
{
    /**
     * Check subscription status and update session data
     */
    public function check()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('landlord')) {
            return redirect()->back();
        }
        
        // Get the active subscription
        $subscription = $user->activeSubscription();
        $latestSubscription = $user->latestSubscription();
        

        // Prepare status message
        $statusMessage = '';
        $statusType = 'warning';
        $needsAction = false;

        if (!$subscription) {
            if ($latestSubscription && $latestSubscription->status === 'cancelled') {
                $statusMessage = 'Your subscription has been cancelled. Please subscribe to continue using all features.';
            } elseif ($latestSubscription && $latestSubscription->status === 'inactive') {
                $statusMessage = 'Your subscription is inactive. Please subscribe to continue using all features.';
            } elseif ($latestSubscription && $latestSubscription->end_date < now()) {
                $statusMessage = 'Your subscription has expired. Please renew to continue using all features.';
            } else {
                $statusMessage = 'You don\'t have an active subscription. Please subscribe to access all features.';
            }
            $needsAction = true;
            // Set alert only if no valid subscription
            session()->flash('subscription_alert', [
                'type' => $statusType,
                'message' => $statusMessage,
                'needs_action' => $needsAction
            ]);
        } elseif ($subscription->payment_status !== 'paid' && !$subscription->isInTrial()) {
            $statusMessage = 'Your subscription payment is pending. Please complete payment to access all features.';
            $statusType = 'info';
            $needsAction = true;
            // Set alert only if payment is pending
            session()->flash('subscription_alert', [
                'type' => $statusType,
                'message' => $statusMessage,
                'needs_action' => $needsAction
            ]);
        } elseif ($subscription->days_remaining <= 7) {
            $statusMessage = "Your subscription will expire in {$subscription->days_remaining} days. Please renew to avoid service interruption.";
            $statusType = 'info';
            // Set alert only if expiring soon
            session()->flash('subscription_alert', [
                'type' => $statusType,
                'message' => $statusMessage,
                'needs_action' => false
            ]);
        } else {
            // Clear any previous alert if subscription is valid and paid
            session()->forget('subscription_alert');
        }
        
        // Store subscription status in session
        session(['subscription_status' => [
            'active' => $subscription ? true : false,
            'status' => $subscription ? $subscription->status : ($latestSubscription ? $latestSubscription->status : 'none'),
            'plan' => $subscription ? $subscription->subscriptionPlan->name : null,
            'days_remaining' => $subscription ? $subscription->days_remaining : 0,
            'payment_status' => $subscription ? $subscription->payment_status : null,
            'is_trial' => $subscription ? $subscription->isInTrial() : false,
        ]]);
        
        // Check if already on subscription pages
        $currentUrl = url()->current();
        $isSubscriptionPage = 
            str_contains($currentUrl, '/subscription/plans') || 
            str_contains($currentUrl, '/subscription/checkout') || 
            str_contains($currentUrl, '/subscription-plans') ||
            str_contains($currentUrl, '/landlord/subscription');
        
        if ($needsAction && !$isSubscriptionPage) {
            // Redirect to subscription plans if action needed and not already on subscription page
            return redirect()->route('landlord.subscription.plans')->with('warning', $statusMessage);
        }
        
        // Otherwise return to the previous page
        return redirect()->back()->with($statusType, $statusMessage);
    }
}
