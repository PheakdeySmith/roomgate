<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SubscriptionLimitsTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    /**
     * Setup basic subscription plan, landlord user, and active subscription
     * 
     * @return array
     */
    private function setupSubscriptionWithLimits($propertiesLimit = 2, $roomsLimit = 5)
    {
        // Create a landlord user
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');

        // Create a subscription plan with specified limits
        $plan = SubscriptionPlan::create([
            'name' => 'Basic Plan',
            'code' => 'basic-plan',
            'price' => 49.99,
            'duration_days' => 30,
            'properties_limit' => $propertiesLimit,
            'rooms_limit' => $roomsLimit,
            'is_active' => true,
            'features' => ['basic_feature_1', 'basic_feature_2']
        ]);

        // Create an active subscription for the landlord
        $subscription = UserSubscription::create([
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active',
            'payment_status' => 'paid',
            'amount_paid' => 49.99
        ]);
        
        return [
            'landlord' => $landlord,
            'plan' => $plan,
            'subscription' => $subscription
        ];
    }
    
    /**
     * Create a test property for the landlord
     * 
     * @param User $landlord
     * @param int $propertyNumber
     * @return Property
     */
    private function createTestProperty($landlord, $propertyNumber = 1)
    {
        return Property::create([
            'landlord_id' => $landlord->id,
            'name' => "Test Property $propertyNumber",
            'property_type' => 'apartment',
            'address_line_1' => "{$propertyNumber}23 Test St",
            'city' => 'Test City',
            'state_province' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Test Country',
            'status' => 'active'
        ]);
    }
    
    /**
     * Create a test room type for the landlord
     * 
     * @param User $landlord
     * @return RoomType
     */
    private function createTestRoomType($landlord)
    {
        return RoomType::create([
            'landlord_id' => $landlord->id,
            'name' => 'Standard Room',
            'description' => 'A standard room',
            'status' => 'active'
        ]);
    }
    
    /**
     * Create a test room for a property
     * 
     * @param Property $property
     * @param RoomType $roomType
     * @param int $roomNumber
     * @return Room
     */
    private function createTestRoom($property, $roomType, $roomNumber = 1)
    {
        return Room::create([
            'property_id' => $property->id,
            'room_type_id' => $roomType->id,
            'room_number' => "R10$roomNumber",
            'status' => 'available'
        ]);
    }

    /** @test */
    public function landlord_cannot_exceed_property_limit_from_subscription()
    {
        // Setup subscription with property limit of 2
        $setup = $this->setupSubscriptionWithLimits(2, 10);
        $landlord = $setup['landlord'];

        // Login as the landlord
        $this->actingAs($landlord);
        
        // Test accessing properties page
        $this->get(route('landlord.properties.index'))
             ->assertSuccessful();

        // Create first property (should succeed)
        $response1 = $this->post(route('landlord.properties.store'), [
            'name' => 'Property 1',
            'property_type' => 'apartment',
            'address_line_1' => '123 Test St',
            'city' => 'Test City',
            'state_province' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Test Country',
            'status' => 'active'
        ]);
        
        $response1->assertSessionHas('success');
        $this->assertDatabaseHas('properties', ['name' => 'Property 1']);

        // Create second property (should succeed)
        $response2 = $this->post(route('landlord.properties.store'), [
            'name' => 'Property 2',
            'property_type' => 'house',
            'address_line_1' => '456 Test Ave',
            'city' => 'Test City',
            'state_province' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Test Country',
            'status' => 'active'
        ]);
        
        $response2->assertSessionHas('success');
        $this->assertDatabaseHas('properties', ['name' => 'Property 2']);

        // Create third property (should fail due to limit)
        $response3 = $this->post(route('landlord.properties.store'), [
            'name' => 'Property 3',
            'property_type' => 'condo',
            'address_line_1' => '789 Test Blvd',
            'city' => 'Test City',
            'state_province' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Test Country',
            'status' => 'active'
        ]);
        
        // Should get a session error
        $response3->assertSessionHas('error');
        
        // Third property should not exist in database
        $this->assertDatabaseMissing('properties', ['name' => 'Property 3']);
    }

    /** @test */
    public function landlord_cannot_access_features_with_expired_subscription()
    {
        // Create a landlord user
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');

        // Create a subscription plan
        $plan = SubscriptionPlan::create([
            'name' => 'Basic Plan',
            'code' => 'basic-plan',
            'price' => 49.99,
            'duration_days' => 30,
            'properties_limit' => 5,
            'rooms_limit' => 20,
            'is_active' => true,
            'features' => ['basic_feature_1', 'basic_feature_2']
        ]);

        // Create an expired subscription for the landlord
        UserSubscription::create([
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => now()->subDays(60),
            'end_date' => now()->subDays(30), // Expired 30 days ago
            'status' => 'active', // Status is still active but date is expired
            'payment_status' => 'paid',
            'amount_paid' => 49.99
        ]);

        // Try to access properties page - should redirect to subscription plans
        $response = $this->actingAs($landlord)
                        ->get(route('landlord.properties.index'));
        
        $response->assertRedirect(route('landlord.subscription.plans'));
        $response->assertSessionHas('warning');
    }
    
    /** @test */
    public function landlord_cannot_exceed_room_limit_from_subscription()
    {
        // Setup subscription with room limit of 3
        $setup = $this->setupSubscriptionWithLimits(5, 3);
        $landlord = $setup['landlord'];
        
        // Create a property
        $property = $this->createTestProperty($landlord);
        
        // Create a room type
        $roomType = $this->createTestRoomType($landlord);
        
        // Login as landlord
        $this->actingAs($landlord);
        
        // Create rooms up to the limit (should succeed)
        for ($i = 1; $i <= 3; $i++) {
            $response = $this->post(route('landlord.rooms.store'), [
                'property_id' => $property->id,
                'room_type_id' => $roomType->id,
                'room_number' => "Room $i",
                'status' => 'available'
            ]);
            
            $response->assertSessionHas('success');
            $this->assertDatabaseHas('rooms', [
                'property_id' => $property->id,
                'room_number' => "Room $i"
            ]);
        }
        
        // Try to create an additional room beyond the limit (should fail)
        $response = $this->post(route('landlord.rooms.store'), [
            'property_id' => $property->id,
            'room_type_id' => $roomType->id,
            'room_number' => "Room 4",
            'status' => 'available'
        ]);
        
        // Check error message
        $response->assertSessionHas('error');
        
        // Verify the extra room was not created
        $this->assertDatabaseMissing('rooms', [
            'property_id' => $property->id,
            'room_number' => "Room 4"
        ]);
        
        // Verify the count of rooms
        $this->assertEquals(3, Room::count());
    }
    
    /** @test */
    public function landlord_can_access_subscription_pages_even_without_active_subscription()
    {
        // Create a landlord user without a subscription
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        
        // Login as landlord
        $this->actingAs($landlord);
        
        // Try to access subscription plans page - should be accessible
        $response = $this->get(route('landlord.subscription.plans'));
        $response->assertStatus(200);
        
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
        
        // Try to access checkout page - should be accessible
        $response = $this->get(route('landlord.subscription.checkout', $plan));
        $response->assertStatus(200);
    }
    
    /** @test */
    public function property_store_method_has_try_catch_blocks()
    {
        // Setup subscription
        $setup = $this->setupSubscriptionWithLimits(5, 10);
        $landlord = $setup['landlord'];
        
        // Login as landlord
        $this->actingAs($landlord);
        
        // Force a database error by passing an invalid property_type
        $response = $this->post(route('landlord.properties.store'), [
            'name' => 'Test Property',
            'property_type' => 'invalid_type', // This should fail validation
            'address_line_1' => '123 Test St',
            'city' => 'Test City',
            'state_province' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Test Country'
        ]);
        
        // Assert that the request failed properly with validation errors
        // rather than throwing an unhandled exception
        $response->assertSessionHasErrors('property_type');
        
        // No property should be created
        $this->assertEquals(0, Property::count());
    }
    
    /** @test */
    public function room_store_method_has_try_catch_blocks()
    {
        // Setup subscription
        $setup = $this->setupSubscriptionWithLimits(5, 10);
        $landlord = $setup['landlord'];
        
        // Create a property
        $property = $this->createTestProperty($landlord);
        
        // Create a room type
        $roomType = $this->createTestRoomType($landlord);
        
        // Login as landlord
        $this->actingAs($landlord);
        
        // Force a database error by passing an invalid status
        $response = $this->post(route('landlord.rooms.store'), [
            'property_id' => $property->id,
            'room_type_id' => $roomType->id,
            'room_number' => 'Test Room',
            'status' => 'invalid_status' // This should fail validation
        ]);
        
        // Assert that the request failed properly with validation errors
        // rather than throwing an unhandled exception
        $response->assertSessionHasErrors('status');
        
        // No room should be created
        $this->assertEquals(0, Room::count());
    }
    
    /** @test */
    public function landlord_can_update_property_without_hitting_limits()
    {
        // Setup subscription with property limit of 2
        $setup = $this->setupSubscriptionWithLimits(2, 5);
        $landlord = $setup['landlord'];
        
        // Create a property
        $property = $this->createTestProperty($landlord);
        
        // Login as landlord
        $this->actingAs($landlord);
        
        // Update the property
        $response = $this->put(route('landlord.properties.update', $property), [
            'name' => 'Updated Property Name',
            'property_type' => 'house',
            'address_line_1' => '123 Updated St',
            'city' => 'Updated City',
            'state_province' => 'Updated State',
            'postal_code' => '54321',
            'country' => 'Updated Country',
            'status' => 'active'
        ]);
        
        // Check success
        $response->assertSessionHas('success');
        
        // Verify the property was updated
        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'name' => 'Updated Property Name',
            'address_line_1' => '123 Updated St'
        ]);
        
        // Verify there's still only one property
        $this->assertEquals(1, Property::count());
    }
    
    /** @test */
    public function test_check_subscription_middleware_allows_subscription_routes()
    {
        // Create a landlord user with no subscription
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        
        // Login as landlord
        $this->actingAs($landlord);
        
        // Trying to access a protected route should redirect to subscription plans
        $this->get(route('landlord.properties.index'))
             ->assertRedirect(route('landlord.subscription.plans'));
        
        // But accessing subscription routes directly should work
        $this->get(route('landlord.subscription.plans'))
             ->assertStatus(200);
    }
    
    /** @test */
    public function user_subscription_model_scopes_work_correctly()
    {
        // Create a landlord
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        
        // Create a plan
        $plan = SubscriptionPlan::create([
            'name' => 'Test Plan',
            'code' => 'test-plan',
            'price' => 49.99,
            'duration_days' => 30,
            'properties_limit' => 5,
            'rooms_limit' => 20,
            'is_active' => true
        ]);
        
        // Create different subscriptions
        
        // Active subscription
        $activeSubscription = UserSubscription::create([
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active',
            'payment_status' => 'paid',
            'amount_paid' => 49.99
        ]);
        
        // Expired subscription
        $expiredSubscription = UserSubscription::create([
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => now()->subDays(60),
            'end_date' => now()->subDays(30),
            'status' => 'active', // Status is active but date is expired
            'payment_status' => 'paid',
            'amount_paid' => 49.99
        ]);
        
        // Trial subscription
        $trialSubscription = UserSubscription::create([
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays(14),
            'status' => 'active',
            'payment_status' => 'trial',
            'amount_paid' => 0
        ]);
        
        // Canceled subscription
        $canceledSubscription = UserSubscription::create([
            'user_id' => $landlord->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => now()->subDays(15),
            'end_date' => now()->addDays(15),
            'status' => 'canceled',
            'payment_status' => 'paid',
            'amount_paid' => 49.99
        ]);
        
        // Test scopes
        
        // Active scope should only return the active and not expired subscription
        $this->assertEquals(2, UserSubscription::active()->count());
        $this->assertTrue(UserSubscription::active()->get()->contains('id', $activeSubscription->id));
        $this->assertTrue(UserSubscription::active()->get()->contains('id', $trialSubscription->id));
        
        // Expired scope should only return the expired subscription
        $this->assertEquals(1, UserSubscription::expired()->count());
        $this->assertTrue(UserSubscription::expired()->get()->contains('id', $expiredSubscription->id));
        
        // Trial scope should only return the trial subscription
        $this->assertEquals(1, UserSubscription::trial()->count());
        $this->assertTrue(UserSubscription::trial()->get()->contains('id', $trialSubscription->id));
        
        // WithStatus scope should filter by status
        $this->assertEquals(3, UserSubscription::withStatus('active')->count());
        $this->assertEquals(1, UserSubscription::withStatus('canceled')->count());
    }
}
