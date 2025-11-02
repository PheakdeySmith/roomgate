<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\UtilityType;
use App\Models\UtilityRate;
use App\Models\UtilityBill;
use App\Models\Amenity;
use App\Models\BasePrice;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ComprehensiveSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->command->info('Starting comprehensive seeding...');

            // 1. Create subscription plans
            $this->seedSubscriptionPlans();

            // 2. Create admin users
            $admins = $this->seedAdminUsers();

            // 3. Create landlord users with subscriptions
            $landlords = $this->seedLandlordUsers();

            // 4. Create utility types
            $utilityTypes = $this->seedUtilityTypes();

            // 5. Create room types
            $roomTypes = $this->seedRoomTypes($landlords);

            // 6. Create amenities
            $amenities = $this->seedAmenities($landlords);

            // 7. Create properties for each landlord
            foreach ($landlords as $landlord) {
                $this->seedLandlordData($landlord, $roomTypes, $amenities, $utilityTypes);
            }

            $this->command->info('Comprehensive seeding completed!');
            $this->printSummary();
        });
    }

    /**
     * Seed subscription plans
     */
    protected function seedSubscriptionPlans(): void
    {
        $this->command->info('Creating subscription plans...');

        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Perfect for small landlords with up to 5 properties',
                'monthly_price' => 29.99,
                'yearly_price' => 299.90,
                'property_limit' => 5,
                'room_limit' => 20,
                'features' => json_encode([
                    'Basic reporting',
                    'Email support',
                    'Invoice management',
                    'Tenant management'
                ]),
                'is_popular' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Ideal for growing property managers',
                'monthly_price' => 79.99,
                'yearly_price' => 799.90,
                'property_limit' => 20,
                'room_limit' => 100,
                'features' => json_encode([
                    'Advanced reporting',
                    'Priority support',
                    'Invoice management',
                    'Tenant management',
                    'Utility billing',
                    'Document management',
                    'Financial analytics'
                ]),
                'is_popular' => true,
                'status' => 'active',
                'discount_percentage' => 10,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Unlimited properties for large organizations',
                'monthly_price' => 199.99,
                'yearly_price' => 1999.90,
                'property_limit' => 0, // 0 means unlimited
                'room_limit' => 0, // 0 means unlimited
                'features' => json_encode([
                    'All Professional features',
                    'Unlimited properties',
                    'Unlimited rooms',
                    'Custom reports',
                    'API access',
                    'Dedicated support',
                    'Multi-user access',
                    'White labeling'
                ]),
                'is_popular' => false,
                'status' => 'active',
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }

    /**
     * Seed admin users
     */
    protected function seedAdminUsers(): array
    {
        $this->command->info('Creating admin users...');

        $admins = [];

        // Create super admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@roomgate.com',
            'password' => Hash::make('password123'),
            'phone' => '+855 12 345 678',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('admin');
        $admins[] = $superAdmin;

        // Create additional admin
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'sysadmin@roomgate.com',
            'password' => Hash::make('password123'),
            'phone' => '+855 98 765 432',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');
        $admins[] = $admin;

        return $admins;
    }

    /**
     * Seed landlord users with subscriptions
     */
    protected function seedLandlordUsers(): array
    {
        $this->command->info('Creating landlord users...');

        $landlords = [];
        $plans = SubscriptionPlan::all();

        // Create 5 landlords with different subscription plans
        for ($i = 1; $i <= 5; $i++) {
            $landlord = User::create([
                'name' => "Landlord {$i} - " . $this->faker->name,
                'email' => "landlord{$i}@roomgate.com",
                'password' => Hash::make('password123'),
                'phone' => $this->faker->phoneNumber,
                'address' => $this->faker->address,
                'status' => 'active',
                'email_verified_at' => now(),
                'currency' => $this->faker->randomElement(['USD', 'KHR']),
            ]);
            $landlord->assignRole('landlord');

            // Create subscription for landlord
            $plan = $plans->random();
            UserSubscription::create([
                'user_id' => $landlord->id,
                'subscription_plan_id' => $plan->id,
                'start_date' => now()->subMonths(rand(1, 6)),
                'end_date' => now()->addMonths(rand(6, 12)),
                'status' => 'active',
                'payment_status' => 'paid',
                'payment_method' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'cash']),
                'amount' => $plan->monthly_price,
            ]);

            $landlords[] = $landlord;
        }

        return $landlords;
    }

    /**
     * Seed utility types
     */
    protected function seedUtilityTypes(): array
    {
        $this->command->info('Creating utility types...');

        $types = [
            ['name' => 'Electricity', 'unit' => 'kWh', 'description' => 'Electrical power consumption'],
            ['name' => 'Water', 'unit' => 'm³', 'description' => 'Water consumption'],
            ['name' => 'Gas', 'unit' => 'm³', 'description' => 'Natural gas consumption'],
            ['name' => 'Internet', 'unit' => 'month', 'description' => 'Internet service'],
            ['name' => 'Trash', 'unit' => 'month', 'description' => 'Trash collection service'],
        ];

        $utilityTypes = [];
        foreach ($types as $type) {
            $utilityTypes[] = UtilityType::create($type);
        }

        return $utilityTypes;
    }

    /**
     * Seed room types
     */
    protected function seedRoomTypes($landlords): array
    {
        $this->command->info('Creating room types...');

        $roomTypes = [];
        $typeNames = ['Studio', 'One Bedroom', 'Two Bedroom', 'Three Bedroom', 'Penthouse', 'Shared Room'];

        foreach ($landlords as $landlord) {
            foreach ($typeNames as $typeName) {
                $roomTypes[] = RoomType::create([
                    'landlord_id' => $landlord->id,
                    'name' => $typeName,
                    'description' => $this->faker->sentence,
                ]);
            }
        }

        return $roomTypes;
    }

    /**
     * Seed amenities
     */
    protected function seedAmenities($landlords): array
    {
        $this->command->info('Creating amenities...');

        $amenities = [];
        $amenityData = [
            'Air Conditioning' => 10,
            'WiFi' => 5,
            'Parking' => 15,
            'Swimming Pool' => 20,
            'Gym' => 25,
            'Security' => 10,
            'Laundry' => 8,
            'Cleaning Service' => 30,
            'Cable TV' => 12,
            'Kitchen' => 0,
        ];

        foreach ($landlords as $landlord) {
            foreach ($amenityData as $name => $price) {
                $amenities[] = Amenity::create([
                    'landlord_id' => $landlord->id,
                    'name' => $name,
                    'amenity_price' => $price,
                    'description' => $this->faker->sentence,
                ]);
            }
        }

        return $amenities;
    }

    /**
     * Seed data for a specific landlord
     */
    protected function seedLandlordData($landlord, $roomTypes, $amenities, $utilityTypes): void
    {
        $this->command->info("Creating data for {$landlord->name}...");

        // Get landlord's room types and amenities
        $landlordRoomTypes = collect($roomTypes)->where('landlord_id', $landlord->id);
        $landlordAmenities = collect($amenities)->where('landlord_id', $landlord->id);

        // Create 2-3 properties per landlord
        $propertyCount = rand(2, 3);
        for ($p = 1; $p <= $propertyCount; $p++) {
            $property = Property::create([
                'landlord_id' => $landlord->id,
                'name' => "Property {$p} - " . $this->faker->company,
                'address' => $this->faker->address,
                'property_type' => $this->faker->randomElement(['apartment', 'house', 'condo', 'villa']),
                'year_built' => rand(2000, 2023),
                'description' => $this->faker->paragraph,
                'status' => 'active',
            ]);

            // Create base prices for room types
            foreach ($landlordRoomTypes as $roomType) {
                BasePrice::create([
                    'property_id' => $property->id,
                    'room_type_id' => $roomType->id,
                    'price' => rand(100, 1000) * 10, // $100 to $10,000
                    'effective_date' => now()->subMonths(6),
                ]);
            }

            // Create utility rates for property
            foreach ($utilityTypes as $utilityType) {
                UtilityRate::create([
                    'property_id' => $property->id,
                    'utility_type_id' => $utilityType->id,
                    'rate' => $this->faker->randomFloat(2, 0.1, 5),
                    'effective_date' => now()->subMonths(6),
                ]);
            }

            // Create 5-10 rooms per property
            $roomCount = rand(5, 10);
            for ($r = 1; $r <= $roomCount; $r++) {
                $roomType = $landlordRoomTypes->random();
                $room = Room::create([
                    'property_id' => $property->id,
                    'room_type_id' => $roomType->id,
                    'room_number' => sprintf('%s%02d', chr(65 + $p - 1), $r),
                    'floor' => rand(1, 5),
                    'size' => rand(20, 150),
                    'max_occupants' => rand(1, 4),
                    'status' => $this->faker->randomElement(['available', 'occupied', 'occupied', 'occupied']), // 75% occupied
                    'description' => $this->faker->sentence,
                ]);

                // Assign random amenities to room
                $room->amenities()->attach(
                    $landlordAmenities->random(rand(3, 6))->pluck('id')
                );

                // Create meters for room
                $electricMeter = Meter::create([
                    'room_id' => $room->id,
                    'utility_type_id' => $utilityTypes[0]->id, // Electricity
                    'meter_number' => 'ELEC-' . strtoupper($this->faker->bothify('##??####')),
                    'initial_reading' => rand(1000, 5000),
                    'status' => 'active',
                    'installation_date' => now()->subYear(),
                ]);

                $waterMeter = Meter::create([
                    'room_id' => $room->id,
                    'utility_type_id' => $utilityTypes[1]->id, // Water
                    'meter_number' => 'WATER-' . strtoupper($this->faker->bothify('##??####')),
                    'initial_reading' => rand(100, 500),
                    'status' => 'active',
                    'installation_date' => now()->subYear(),
                ]);

                // If room is occupied, create tenant and contract
                if ($room->status === 'occupied') {
                    $this->createTenantAndContract($landlord, $room, $roomType, $electricMeter, $waterMeter);
                }
            }
        }
    }

    /**
     * Create tenant and contract for a room
     */
    protected function createTenantAndContract($landlord, $room, $roomType, $electricMeter, $waterMeter): void
    {
        // Create tenant
        $tenant = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password123'),
            'phone' => $this->faker->phoneNumber,
            'landlord_id' => $landlord->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $tenant->assignRole('tenant');

        // Get base price for the room
        $basePrice = BasePrice::where('property_id', $room->property_id)
            ->where('room_type_id', $roomType->id)
            ->first();

        // Create contract
        $startDate = now()->subMonths(rand(1, 12));
        $endDate = $startDate->copy()->addYear();

        $contract = Contract::create([
            'user_id' => $tenant->id,
            'room_id' => $room->id,
            'landlord_id' => $landlord->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'rent_amount' => $basePrice ? $basePrice->price : rand(200, 1000),
            'billing_cycle' => 'monthly',
            'status' => 'active',
        ]);

        // Create meter readings
        $this->createMeterReadings($electricMeter, $startDate);
        $this->createMeterReadings($waterMeter, $startDate);

        // Create invoices for past months
        $this->createInvoices($contract, $startDate);
    }

    /**
     * Create meter readings
     */
    protected function createMeterReadings($meter, $startDate): void
    {
        $currentReading = $meter->initial_reading;
        $readingDate = $startDate->copy()->startOfMonth();

        while ($readingDate <= now()) {
            $consumption = rand(50, 300);
            $currentReading += $consumption;

            MeterReading::create([
                'meter_id' => $meter->id,
                'reading_value' => $currentReading,
                'reading_date' => $readingDate,
            ]);

            $meter->update(['last_reading_date' => $readingDate]);
            $readingDate->addMonth();
        }
    }

    /**
     * Create invoices
     */
    protected function createInvoices($contract, $startDate): void
    {
        $invoiceDate = $startDate->copy()->startOfMonth();
        $invoiceNumber = 1;

        while ($invoiceDate <= now()) {
            $dueDate = $invoiceDate->copy()->addDays(15);
            $status = $this->faker->randomElement(['paid', 'paid', 'paid', 'sent', 'overdue']);

            $invoice = Invoice::create([
                'contract_id' => $contract->id,
                'invoice_number' => sprintf('INV-%s-%04d', $contract->landlord_id, $invoiceNumber++),
                'issue_date' => $invoiceDate,
                'due_date' => $dueDate,
                'total_amount' => $contract->rent_amount,
                'paid_amount' => $status === 'paid' ? $contract->rent_amount : 0,
                'status' => $status,
                'payment_method' => $status === 'paid' ? $this->faker->randomElement(['cash', 'bank_transfer', 'credit_card']) : null,
                'payment_date' => $status === 'paid' ? $dueDate->copy()->subDays(rand(1, 10)) : null,
            ]);

            // Create line items
            LineItem::create([
                'invoice_id' => $invoice->id,
                'description' => 'Monthly Rent',
                'amount' => $contract->rent_amount,
                'status' => $status,
                'paid_amount' => $status === 'paid' ? $contract->rent_amount : 0,
            ]);

            // Create utility bill and line items
            $room = $contract->room;
            foreach ($room->meters as $meter) {
                $consumption = rand(50, 300);
                $rate = rand(1, 5);
                $amount = $consumption * $rate;

                $utilityBill = UtilityBill::create([
                    'contract_id' => $contract->id,
                    'utility_type_id' => $meter->utility_type_id,
                    'billing_period_start' => $invoiceDate,
                    'billing_period_end' => $invoiceDate->copy()->endOfMonth(),
                    'consumption' => $consumption,
                    'rate_applied' => $rate,
                    'amount' => $amount,
                ]);

                $lineItem = LineItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => "Utility - {$meter->utilityType->name}",
                    'amount' => $amount,
                    'status' => $status,
                    'paid_amount' => $status === 'paid' ? $amount : 0,
                ]);

                $lineItem->lineable()->associate($utilityBill);
                $lineItem->save();

                // Update invoice total
                $invoice->increment('total_amount', $amount);
                if ($status === 'paid') {
                    $invoice->increment('paid_amount', $amount);
                }
            }

            $invoiceDate->addMonth();
        }
    }

    /**
     * Print summary
     */
    protected function printSummary(): void
    {
        $this->command->info("\n=== SEEDING SUMMARY ===");
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Admin Users', User::role('admin')->count()],
                ['Landlord Users', User::role('landlord')->count()],
                ['Tenant Users', User::role('tenant')->count()],
                ['Properties', Property::count()],
                ['Rooms', Room::count()],
                ['Active Contracts', Contract::where('status', 'active')->count()],
                ['Invoices', Invoice::count()],
                ['Meters', Meter::count()],
                ['Meter Readings', MeterReading::count()],
                ['Utility Bills', UtilityBill::count()],
                ['Subscription Plans', SubscriptionPlan::count()],
                ['Active Subscriptions', UserSubscription::where('status', 'active')->count()],
            ]
        );

        $this->command->info("\n=== LOGIN CREDENTIALS ===");
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@roomgate.com', 'password123'],
                ['Landlord 1', 'landlord1@roomgate.com', 'password123'],
                ['Landlord 2', 'landlord2@roomgate.com', 'password123'],
                ['Landlord 3', 'landlord3@roomgate.com', 'password123'],
                ['Tenant', 'Any generated tenant email', 'password123'],
            ]
        );
    }
}