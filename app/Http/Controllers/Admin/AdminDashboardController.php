<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        // Count statistics
        $stats = [
            'total_landlords' => User::role('landlord')->count(),
            'total_tenants' => User::role('tenant')->count(),
            'active_subscriptions' => UserSubscription::where('status', 'active')
                ->where('end_date', '>', now())
                ->count(),
            'revenue_this_month' => UserSubscription::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount_paid'),
        ];

        // Get recent subscriptions
        $recentSubscriptions = UserSubscription::with(['user', 'subscriptionPlan'])
            ->latest()
            ->limit(5)
            ->get();

        // Get subscription metrics by plan
        $subscriptionsByPlan = DB::table('user_subscriptions')
            ->join('subscription_plans', 'user_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->select('subscription_plans.name', DB::raw('count(*) as total'))
            ->groupBy('subscription_plans.name')
            ->get();

        // Get monthly revenue for the last 6 months
        $monthlyRevenue = [];
        for ($i = 0; $i < 6; $i++) {
            $date = now()->subMonths($i);
            $total = UserSubscription::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('amount_paid');
            
            $monthlyRevenue[6-$i] = [
                'month' => $date->format('M'),
                'total' => $total,
            ];
        }

        return view('backends.dashboard.admin.index', compact(
            'stats',
            'recentSubscriptions',
            'subscriptionsByPlan',
            'monthlyRevenue'
        ));
    }

    /**
     * Display the subscription plans
     */
    public function subscriptionPlans()
    {
        // Get all plans, count their active subscriptions, and order by price
        $plans = SubscriptionPlan::withCount(['userSubscriptions' => function ($query) {
            $query->where('status', 'active');
        }])->orderBy('price')->get();
        
        return view('backends.dashboard.admin.subscription-plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new subscription plan
     */
    public function createSubscriptionPlan()
    {
        return view('backends.dashboard.admin.subscription-plans.create');
    }

    /**
     * Store a newly created subscription plan
     */
    public function storeSubscriptionPlan(Request $request)
    {
        // ADDED validation for new fields
        $request->validate([
            'name' => 'required|string|max:255',
            'plan_group' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'base_monthly_price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:0',
            'properties_limit' => 'required|integer|min:0',
            'rooms_limit' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $code = Str::slug($request->name) . '-' . Str::random(6);

        $features = [];
        if ($request->has('features')) {
            foreach ($request->features as $feature) {
                if (!empty($feature)) {
                    $features[] = $feature;
                }
            }
        }

        // ADDED new fields to the create() method
        SubscriptionPlan::create([
            'name' => $request->name,
            'plan_group' => $request->plan_group,
            'code' => $code,
            'description' => $request->description,
            'price' => $request->price,
            'base_monthly_price' => $request->base_monthly_price,
            'duration_days' => $request->duration_days,
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
            'properties_limit' => $request->properties_limit,
            'rooms_limit' => $request->rooms_limit,
            'features' => !empty($features) ? $features : null,
        ]);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    /**
     * Show the form for editing a subscription plan
     */
    public function editSubscriptionPlan(SubscriptionPlan $plan)
    {
        return view('backends.dashboard.admin.subscription-plans.edit', compact('plan'));
    }

    /**
     * Update the specified subscription plan
     */
    public function updateSubscriptionPlan(Request $request, SubscriptionPlan $plan)
    {
        // ADDED validation for new fields
        $request->validate([
            'name' => 'required|string|max:255',
            'plan_group' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'base_monthly_price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:0',
            'properties_limit' => 'required|integer|min:0',
            'rooms_limit' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $features = [];
        if ($request->has('features')) {
            foreach ($request->features as $feature) {
                if (!empty($feature)) {
                    $features[] = $feature;
                }
            }
        }
        
        // ADDED new fields to the update() method
        $plan->update([
            'name' => $request->name,
            'plan_group' => $request->plan_group,
            'description' => $request->description,
            'price' => $request->price,
            'base_monthly_price' => $request->base_monthly_price,
            'duration_days' => $request->duration_days,
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
            'properties_limit' => $request->properties_limit,
            'rooms_limit' => $request->rooms_limit,
            'features' => !empty($features) ? $features : null,
        ]);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    /**
     * Delete the specified subscription plan
     */
    public function deleteSubscriptionPlan(SubscriptionPlan $plan)
    {
        $plan->delete();

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }

    /**
     * Display all user subscriptions
     */
    public function userSubscriptions(Request $request)
    {
        // Get all subscriptions first to check the total count
        $totalCount = UserSubscription::count();
        
        // Show all data if less than or equal to 25 records, otherwise paginate
        $perPage = ($totalCount <= 25) ? $totalCount : 25;
        
        // If user has selected a specific per page value
        if ($request->has('per_page') && is_numeric($request->per_page)) {
            $perPage = (int) $request->per_page;
        }
        
        $subscriptions = UserSubscription::with(['user', 'subscriptionPlan'])
            ->latest()
            ->paginate($perPage);

        // Make sure URL links in pagination contain the correct path
        $subscriptions->withPath(route('admin.subscriptions.index'));
        
        // Add the total count to the view data
        $totalSubscriptions = $totalCount;

        if ($request->ajax()) {
            return response()->json($subscriptions);
        }

        return view('backends.dashboard.admin.subscriptions.index', compact('subscriptions', 'totalSubscriptions'));
    }

    /**
     * Show the form for creating a new user subscription
     */
    public function createUserSubscription()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        $landlords = User::role('landlord')->get();

        return view('backends.dashboard.admin.subscriptions.create', compact('plans', 'landlords'));
    }

    /**
     * Store a newly created user subscription
     */
    public function storeUserSubscription(Request $request)
    {
        \Log::info('Subscription creation request', ['request_data' => $request->all()]);
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'payment_status' => 'required|in:pending,paid,trial',
            'payment_method' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'amount_paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'handle_existing' => 'nullable|in:cancel,keep',
        ]);

        $user = User::findOrFail($request->user_id);
        $plan = SubscriptionPlan::findOrFail($request->subscription_plan_id);
        
        // Check for any active subscription
        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->first();
            
        \Log::info('Active subscription check', [
            'user_id' => $user->id, 
            'has_active' => $activeSubscription ? true : false,
            'active_subscription' => $activeSubscription
        ]);
        
        // Check for existing active subscription
        if ($activeSubscription) {
            // If no explicit choice was made about handling existing subscription
            if (!$request->has('handle_existing')) {
                // Redirect back with a warning and choices
                $existingPlan = $activeSubscription->subscriptionPlan;
                return redirect()->back()
                    ->with('warning', "User already has an active subscription to {$existingPlan->name} plan until {$activeSubscription->end_date->format('M d, Y')}.")
                    ->withInput()
                    ->with('show_existing_options', true)
                    ->with('active_subscription_id', $activeSubscription->id);
            }
            
            // If admin chose to cancel the existing subscription
            if ($request->handle_existing === 'cancel') {
                $activeSubscription->update([
                    'status' => 'canceled',
                    'canceled_at' => now(),
                    'notes' => ($activeSubscription->notes ? $activeSubscription->notes . ' | ' : '') . 
                        'Canceled due to new subscription creation on ' . now()->format('Y-m-d H:i:s'),
                ]);
            }
            // If admin chose 'keep', we don't do anything to the existing subscription
        }
        
        // Calculate start and end dates
        $startDate = now();
        $endDate = ($plan->duration_days > 0) 
            ? $startDate->copy()->addDays($plan->duration_days) 
            : $startDate->copy()->addYears(100); // For lifetime plans

        UserSubscription::create([
            'user_id' => $request->user_id,
            'subscription_plan_id' => $plan->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'amount_paid' => $request->amount_paid ?? $plan->price,
            'notes' => $request->notes . ($activeSubscription && $request->handle_existing === 'cancel' ? 
                ' | Replaces previous subscription #' . $activeSubscription->id : ''),
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'User subscription created successfully.');
    }

    /**
     * Show subscription details
     */
    public function showUserSubscription(UserSubscription $subscription)
    {
        $subscription->load(['user', 'subscriptionPlan']);
        
        return view('backends.dashboard.admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Cancel a user subscription
     */
    public function cancelUserSubscription(UserSubscription $subscription)
    {
        $subscription->update([
            'status' => 'canceled',
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription canceled successfully.');
    }

    /**
     * Renew a user subscription
     */
    public function renewUserSubscription(UserSubscription $subscription)
    {
        $plan = $subscription->subscriptionPlan;
        
        // Calculate new end date based on current end date
        $startDate = now();
        $endDate = ($plan->duration_days > 0) 
            ? $startDate->copy()->addDays($plan->duration_days) 
            : $startDate->copy()->addYears(100); // For lifetime plans

        UserSubscription::create([
            'user_id' => $subscription->user_id,
            'subscription_plan_id' => $plan->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
            'payment_status' => 'pending',
            'amount_paid' => $plan->price,
            'notes' => 'Renewal of subscription #' . $subscription->id,
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription renewed successfully.');
    }
}
