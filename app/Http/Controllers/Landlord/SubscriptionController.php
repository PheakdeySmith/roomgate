<?php

namespace App\Http\Controllers\Landlord;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of available subscription plans
     */
    public function plans()
    {
        $user = Auth::user();
        $plans = SubscriptionPlan::all();
        $activeSubscription = $user->activeSubscription();
        
        return view('backends.dashboard.landlord.subscription.plans', compact('plans', 'activeSubscription'));
    }
    
    /**
     * Show the checkout page for a plan
     */
    public function checkout(SubscriptionPlan $plan)
    {
        $user = Auth::user();
        
        return view('backends.dashboard.landlord.subscription.checkout', compact('plan', 'user'));
    }
    
    /**
     * Process a subscription purchase
     */
    public function purchase(Request $request, SubscriptionPlan $plan)
    {
        // Validate the request
        $request->validate([
            'payment_method' => 'required|string',
            // Add more validation as needed
        ]);
        
        $user = Auth::user();
        
        // Create new subscription
        $subscription = new UserSubscription();
        $subscription->user_id = $user->id;
        $subscription->subscription_plan_id = $plan->id;
        $subscription->start_date = now();
        $subscription->end_date = now()->addDays($plan->duration_days);
        $subscription->status = 'active';
        $subscription->payment_status = 'paid'; // You might want to change this based on payment gateway
        $subscription->payment_method = $request->payment_method;
        $subscription->transaction_id = 'TXN-' . uniqid(); // Generate from payment gateway
        $subscription->amount_paid = $plan->price;
        $subscription->notes = 'Subscription purchased via website';
        $subscription->save();
        
        // Redirect to success page
        return redirect()->route('landlord.subscription.success', $subscription->id)
            ->with('success', 'Subscription purchased successfully!');
    }
    
    /**
     * Show the success page after purchase
     */
    public function success(UserSubscription $subscription)
    {
        $user = Auth::user();
        
        // Check if this subscription belongs to the user
        if ($subscription->user_id !== $user->id) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid subscription');
        }
        
        return view('backends.dashboard.landlord.subscription.success', compact('subscription'));
    }
}
