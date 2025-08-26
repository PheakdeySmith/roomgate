<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SubscriptionManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test the admin can view subscription plans
     * 
     * @return void
     */
    public function test_admin_can_view_subscription_plans()
    {
        // Create an admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        // Create a subscription plan
        $plan = SubscriptionPlan::create([
            'name' => 'Premium Plan',
            'code' => 'premium-plan',
            'description' => 'This is a premium plan',
            'price' => 99.99,
            'duration_days' => 30,
            'properties_limit' => 10,
            'rooms_limit' => 50,
            'is_featured' => true,
            'is_active' => true
        ]);
        
        // Login as admin
        $this->actingAs($admin);
        
        // Visit the subscription plans page
        $response = $this->get(route('admin.subscription-plans.index'));
        
        // Assert successful response
        $response->assertStatus(200);
        
        // Assert the page contains the plan name
        $response->assertSee($plan->name);
    }
    
    /**
     * Test admin can create a subscription plan
     * 
     * @return void
     */
    public function test_admin_can_create_subscription_plan()
    {
        // Create an admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        // Login as admin
        $this->actingAs($admin);
        
        // Visit the create plan page
        $response = $this->get(route('admin.subscription-plans.create'));
        $response->assertStatus(200);
        
        // Submit a plan creation form
        $planData = [
            'name' => 'Test Plan',
            'price' => 49.99,
            'duration_days' => 30,
            'properties_limit' => 5,
            'rooms_limit' => 20,
            'description' => 'This is a test plan',
            'is_featured' => true,
            'is_active' => true,
            'features' => ['Feature 1', 'Feature 2']
        ];
        
        $response = $this->post(route('admin.subscription-plans.store'), $planData);
        
        // Assert redirect to index page with success message
        $response->assertRedirect(route('admin.subscription-plans.index'));
        $response->assertSessionHas('success');
        
        // Assert the plan was created in the database
        $this->assertDatabaseHas('subscription_plans', [
            'name' => 'Test Plan',
            'price' => 49.99,
            'duration_days' => 30,
            'properties_limit' => 5,
            'rooms_limit' => 20
        ]);
    }
    
    /**
     * Test admin can update a subscription plan
     * 
     * @return void
     */
    public function test_admin_can_update_subscription_plan()
    {
        // Create an admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        // Create a plan to update
        $plan = SubscriptionPlan::create([
            'name' => 'Old Plan',
            'code' => 'old-plan',
            'description' => 'This is an old plan',
            'price' => 29.99,
            'duration_days' => 30,
            'properties_limit' => 3,
            'rooms_limit' => 15,
            'is_featured' => false,
            'is_active' => true
        ]);
        
        // Login as admin
        $this->actingAs($admin);
        
        // Visit the edit plan page
        $response = $this->get(route('admin.subscription-plans.edit', $plan));
        $response->assertStatus(200);
        
        // Submit the update form
        $updatedData = [
            'name' => 'Updated Plan',
            'price' => 39.99,
            'duration_days' => 60,
            'properties_limit' => 6,
            'rooms_limit' => 25,
            'description' => 'This is an updated plan',
            'is_featured' => true,
            'is_active' => true
        ];
        
        $response = $this->put(route('admin.subscription-plans.update', $plan), $updatedData);
        
        // Assert redirect to index page with success message
        $response->assertRedirect(route('admin.subscription-plans.index'));
        $response->assertSessionHas('success');
        
        // Assert the plan was updated in the database
        $this->assertDatabaseHas('subscription_plans', [
            'id' => $plan->id,
            'name' => 'Updated Plan',
            'price' => 39.99,
            'duration_days' => 60,
            'properties_limit' => 6,
            'rooms_limit' => 25
        ]);
    }
    
    /**
     * Test admin can delete a subscription plan
     * 
     * @return void
     */
    public function test_admin_can_delete_subscription_plan()
    {
        // Create an admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        // Create a plan to delete
        $plan = SubscriptionPlan::create([
            'name' => 'Plan to Delete',
            'code' => 'plan-to-delete',
            'description' => 'This plan will be deleted',
            'price' => 19.99,
            'duration_days' => 30,
            'properties_limit' => 2,
            'rooms_limit' => 10,
            'is_featured' => false,
            'is_active' => true
        ]);
        
        // Login as admin
        $this->actingAs($admin);
        
        // Delete the plan
        $response = $this->delete(route('admin.subscription-plans.destroy', $plan));
        
        // Assert redirect to index page with success message
        $response->assertRedirect(route('admin.subscription-plans.index'));
        $response->assertSessionHas('success');
        
        // Assert the plan was deleted from the database
        $this->assertDatabaseMissing('subscription_plans', ['id' => $plan->id]);
    }
    
    /**
     * Test admin can view user subscriptions
     * 
     * @return void
     */
    public function test_admin_can_view_user_subscriptions()
    {
        // Create an admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        // Create a landlord
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        
        // Create a subscription plan
        $plan = SubscriptionPlan::create([
            'name' => 'Basic Plan',
            'code' => 'basic-plan',
            'price' => 29.99,
            'duration_days' => 30,
            'properties_limit' => 3,
            'rooms_limit' => 15,
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
            'amount_paid' => 29.99
        ]);
        
        // Login as admin
        $this->actingAs($admin);
        
        // Visit the subscriptions page
        $response = $this->get(route('admin.subscriptions.index'));
        
        // Assert successful response
        $response->assertStatus(200);
        
        // Assert the page contains the subscription info
        $response->assertSee($landlord->name);
        $response->assertSee($plan->name);
    }
    
    /**
     * Test admin can create a user subscription
     * 
     * @return void
     */
    public function test_admin_can_create_user_subscription()
    {
        // Create an admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        // Create a landlord
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        
        // Create a subscription plan
        $plan = SubscriptionPlan::create([
            'name' => 'Standard Plan',
            'code' => 'standard-plan',
            'price' => 39.99,
            'duration_days' => 30,
            'properties_limit' => 5,
            'rooms_limit' => 20,
            'is_active' => true
        ]);
        
        // Login as admin
        $this->actingAs($admin);
        
        // Visit the create subscription page
        $response = $this->get(route('admin.subscriptions.create'));
        $response->assertStatus(200);
        
        // Submit a subscription creation form
        $subscriptionData = [
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
            'amount_paid' => 39.99,
            'notes' => 'Test subscription created by admin'
        ];
        
        $response = $this->post(route('admin.subscriptions.store'), $subscriptionData);
        
        // Assert redirect to index page with success message
        $response->assertRedirect(route('admin.subscriptions.index'));
        $response->assertSessionHas('success');
        
        // Assert the subscription was created in the database
        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'payment_status' => 'paid',
            'notes' => 'Test subscription created by admin'
        ]);
    }
    
    /**
     * Test admin can view existing subscription before creating a new one
     * 
     * @return void
     */
    public function test_admin_sees_warning_when_user_has_active_subscription()
    {
        // Create an admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        // Create a landlord
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        
        // Create a subscription plan
        $plan = SubscriptionPlan::create([
            'name' => 'Basic Plan',
            'code' => 'basic-plan',
            'price' => 29.99,
            'duration_days' => 30,
            'properties_limit' => 3,
            'rooms_limit' => 15,
            'is_active' => true
        ]);
        
        // Create an active subscription for the landlord
        $subscription = UserSubscription::create([
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active',
            'payment_status' => 'paid',
            'amount_paid' => 29.99
        ]);
        
        // Login as admin
        $this->actingAs($admin);
        
        // Try to create a new subscription for the same user
        $subscriptionData = [
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
            'amount_paid' => 29.99,
            'notes' => 'Another subscription'
        ];
        
        $response = $this->post(route('admin.subscriptions.store'), $subscriptionData);
        
        // Assert redirect back with warning
        $response->assertSessionHas('warning');
        $response->assertSessionHas('show_existing_options');
        
        // Now try again with explicit handling
        $subscriptionData['handle_existing'] = 'cancel';
        
        $response = $this->post(route('admin.subscriptions.store'), $subscriptionData);
        
        // Assert success this time
        $response->assertRedirect(route('admin.subscriptions.index'));
        $response->assertSessionHas('success');
        
        // Assert the old subscription was canceled
        $this->assertDatabaseHas('user_subscriptions', [
            'id' => $subscription->id,
            'status' => 'canceled'
        ]);
        
        // Assert a new subscription was created
        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'notes' => 'Another subscription'
        ]);
    }
    
    /**
     * Test admin can cancel a user subscription
     * 
     * @return void
     */
    public function test_admin_can_cancel_user_subscription()
    {
        // Create an admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        // Create a landlord
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        
        // Create a subscription plan
        $plan = SubscriptionPlan::create([
            'name' => 'Basic Plan',
            'code' => 'basic-plan',
            'price' => 29.99,
            'duration_days' => 30,
            'properties_limit' => 3,
            'rooms_limit' => 15,
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
            'amount_paid' => 29.99
        ]);
        
        // Login as admin
        $this->actingAs($admin);
        
        // Cancel the subscription
        $response = $this->post(route('admin.subscriptions.cancel', $subscription));
        
        // Assert redirect to index page with success message
        $response->assertRedirect(route('admin.subscriptions.index'));
        $response->assertSessionHas('success');
        
        // Assert the subscription was canceled in the database
        $this->assertDatabaseHas('user_subscriptions', [
            'id' => $subscription->id,
            'status' => 'canceled'
        ]);
    }
    
    /**
     * Test admin can renew a user subscription
     * 
     * @return void
     */
    public function test_admin_can_renew_user_subscription()
    {
        // Create an admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        // Create a landlord
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        
        // Create a subscription plan
        $plan = SubscriptionPlan::create([
            'name' => 'Basic Plan',
            'code' => 'basic-plan',
            'price' => 29.99,
            'duration_days' => 30,
            'properties_limit' => 3,
            'rooms_limit' => 15,
            'is_active' => true
        ]);
        
        // Create a subscription
        $subscription = UserSubscription::create([
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => now()->subDays(25),
            'end_date' => now()->addDays(5),
            'status' => 'active',
            'payment_status' => 'paid',
            'amount_paid' => 29.99
        ]);
        
        // Login as admin
        $this->actingAs($admin);
        
        // Renew the subscription
        $response = $this->post(route('admin.subscriptions.renew', $subscription));
        
        // Assert redirect to index page with success message
        $response->assertRedirect(route('admin.subscriptions.index'));
        $response->assertSessionHas('success');
        
        // Assert a new subscription was created
        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'payment_status' => 'pending',
            'notes' => 'Renewal of subscription #' . $subscription->id
        ]);
        
        // Assert there are now two subscriptions for this user
        $this->assertEquals(2, UserSubscription::where('user_id', $landlord->id)->count());
    }
}
