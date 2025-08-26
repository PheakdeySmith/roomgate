<?php

namespace Tests\Feature\Landlord;

use Tests\TestCase;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test landlord can view subscription plans
     * 
     * @return void
     */
    public function test_landlord_can_view_subscription_plans()
    {
        // Create a landlord
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        
        // Create subscription plans
        $basicPlan = SubscriptionPlan::create([
            'name' => 'Basic Plan',
            'code' => 'basic-plan',
            'price' => 29.99,
            'duration_days' => 30,
            'properties_limit' => 3,
            'rooms_limit' => 15,
            'is_active' => true,
            'is_featured' => false,
            'features' => ['feature1', 'feature2']
        ]);
        
        $premiumPlan = SubscriptionPlan::create([
            'name' => 'Premium Plan',
            'code' => 'premium-plan',
            'price' => 99.99,
            'duration_days' => 30,
            'properties_limit' => 10,
            'rooms_limit' => 50,
            'is_active' => true,
            'is_featured' => true,
            'features' => ['feature1', 'feature2', 'feature3', 'feature4']
        ]);
        
        // Login as landlord
        $this->actingAs($landlord);
        
        // Visit the subscription plans page
        $response = $this->get(route('landlord.subscription.plans'));
        
        // Assert successful response
        $response->assertStatus(200);
        
        // Assert the page contains both plan names
        $response->assertSee($basicPlan->name);
        $response->assertSee($premiumPlan->name);
        
        // Assert the page shows the featured plan highlight
        $response->assertSee($premiumPlan->formatted_price);
    }
    
    /**
     * Test landlord can see checkout page for a plan
     * 
     * @return void
     */
    public function test_landlord_can_see_checkout_page()
    {
        // Create a landlord
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        
        // Create a subscription plan
        $plan = SubscriptionPlan::create([
            'name' => 'Standard Plan',
            'code' => 'standard-plan',
            'price' => 49.99,
            'duration_days' => 30,
            'properties_limit' => 5,
            'rooms_limit' => 25,
            'is_active' => true,
            'features' => ['feature1', 'feature2', 'feature3']
        ]);
        
        // Login as landlord
        $this->actingAs($landlord);
        
        // Visit the checkout page for this plan
        $response = $this->get(route('landlord.subscription.checkout', $plan));
        
        // Assert successful response
        $response->assertStatus(200);
        
        // Assert the page contains the plan name and price
        $response->assertSee($plan->name);
        $response->assertSee($plan->formatted_price);
    }
    
    /**
     * Test landlord can purchase a subscription
     * 
     * @return void
     */
    public function test_landlord_can_purchase_subscription()
    {
        // Create a landlord
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        
        // Create a subscription plan
        $plan = SubscriptionPlan::create([
            'name' => 'Standard Plan',
            'code' => 'standard-plan',
            'price' => 49.99,
            'duration_days' => 30,
            'properties_limit' => 5,
            'rooms_limit' => 25,
            'is_active' => true,
            'features' => ['feature1', 'feature2', 'feature3']
        ]);
        
        // Login as landlord
        $this->actingAs($landlord);
        
        // Purchase the plan
        $response = $this->post(route('landlord.subscription.purchase', $plan), [
            'payment_method' => 'credit_card'
        ]);
        
        // Assert the subscription was created
        $subscription = UserSubscription::where('user_id', $landlord->id)
            ->where('subscription_plan_id', $plan->id)
            ->first();
        
        $this->assertNotNull($subscription);
        $this->assertEquals('active', $subscription->status);
        $this->assertEquals('paid', $subscription->payment_status);
        $this->assertEquals($plan->price, $subscription->amount_paid);
        
        // Assert redirect to success page
        $response->assertRedirect(route('landlord.subscription.success', $subscription->id));
        $response->assertSessionHas('success');
    }
    
    /**
     * Test landlord can see success page after purchase
     * 
     * @return void
     */
    public function test_landlord_can_see_success_page()
    {
        // Create a landlord
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        
        // Create a subscription plan
        $plan = SubscriptionPlan::create([
            'name' => 'Standard Plan',
            'code' => 'standard-plan',
            'price' => 49.99,
            'duration_days' => 30,
            'properties_limit' => 5,
            'rooms_limit' => 25,
            'is_active' => true
        ]);
        
        // Create a subscription
        $subscription = UserSubscription::create([
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active',
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
            'transaction_id' => 'TXN-' . uniqid(),
            'amount_paid' => $plan->price,
            'notes' => 'Subscription purchased via website'
        ]);
        
        // Login as landlord
        $this->actingAs($landlord);
        
        // Visit the success page
        $response = $this->get(route('landlord.subscription.success', $subscription->id));
        
        // Assert successful response
        $response->assertStatus(200);
        
        // Assert the page contains the subscription details
        $response->assertSee($plan->name);
        $response->assertSee($subscription->payment_method);
    }
    
    /**
     * Test landlord cannot view success page for another user's subscription
     * 
     * @return void
     */
    public function test_landlord_cannot_view_another_users_subscription()
    {
        // Create two landlords
        $landlord1 = User::factory()->create();
        $landlord1->assignRole('landlord');
        
        $landlord2 = User::factory()->create();
        $landlord2->assignRole('landlord');
        
        // Create a subscription plan
        $plan = SubscriptionPlan::create([
            'name' => 'Standard Plan',
            'code' => 'standard-plan',
            'price' => 49.99,
            'duration_days' => 30,
            'properties_limit' => 5,
            'rooms_limit' => 25,
            'is_active' => true
        ]);
        
        // Create a subscription for landlord2
        $subscription = UserSubscription::create([
            'user_id' => $landlord2->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active',
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
            'transaction_id' => 'TXN-' . uniqid(),
            'amount_paid' => $plan->price,
            'notes' => 'Subscription purchased via website'
        ]);
        
        // Login as landlord1
        $this->actingAs($landlord1);
        
        // Try to visit the success page for landlord2's subscription
        $response = $this->get(route('landlord.subscription.success', $subscription->id));
        
        // Assert redirect with error
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }
    
    /**
     * Test landlord with existing subscription sees their plan highlighted
     * 
     * @return void
     */
    public function test_landlord_with_subscription_sees_current_plan_highlighted()
    {
        // Create a landlord
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        
        // Create subscription plans
        $basicPlan = SubscriptionPlan::create([
            'name' => 'Basic Plan',
            'code' => 'basic-plan',
            'price' => 29.99,
            'duration_days' => 30,
            'properties_limit' => 3,
            'rooms_limit' => 15,
            'is_active' => true
        ]);
        
        $premiumPlan = SubscriptionPlan::create([
            'name' => 'Premium Plan',
            'code' => 'premium-plan',
            'price' => 99.99,
            'duration_days' => 30,
            'properties_limit' => 10,
            'rooms_limit' => 50,
            'is_active' => true
        ]);
        
        // Create a subscription for the basic plan
        $subscription = UserSubscription::create([
            'user_id' => $landlord->id,
            'subscription_plan_id' => $basicPlan->id,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active',
            'payment_status' => 'paid',
            'amount_paid' => $basicPlan->price
        ]);
        
        // Login as landlord
        $this->actingAs($landlord);
        
        // Visit the subscription plans page
        $response = $this->get(route('landlord.subscription.plans'));
        
        // Assert successful response
        $response->assertStatus(200);
        
        // Assert the page shows "Current Plan" for the basic plan
        $response->assertSee('Current Plan');
    }
}
