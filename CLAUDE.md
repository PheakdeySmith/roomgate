# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

RoomGate is a multi-tenant property management system built with Laravel 11 for managing rental properties, contracts, invoicing, and utilities in Cambodia. It implements a subscription-based model for landlords with role-based access control.

**Tech Stack:**
- **Backend**: Laravel 11.37.0 (PHP 8.2+)
- **Frontend**: Vite 6.2.4, Tailwind CSS 3.1, Alpine.js 3.4
- **Database**: MySQL (SQLite for testing)
- **Authentication**: Laravel Breeze with Spatie Permissions
- **Testing**: PHPUnit 11.5.3

---

## Build, Test & Development Commands

### Installation & Setup
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# Run migrations and seeders
php artisan migrate --seed

# Create storage symlink for file uploads
php artisan storage:link
```

### Development Server
```bash
# Start Laravel development server
php artisan serve

# In a separate terminal, start Vite dev server for assets
npm run dev

# Or run both concurrently (if configured)
composer run dev
```

### Build & Production
```bash
# Build frontend assets for production
npm run build

# Optimize Laravel for production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Testing
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/PropertyControllerTest.php

# Run with coverage
php artisan test --coverage

# Run specific test method
php artisan test --filter testPropertyCreation
```

### Database Management
```bash
# Reset and reseed database
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_tablename_table

# Run specific seeder
php artisan db:seed --class=PropertySeeder

# Rollback last migration batch
php artisan migrate:rollback
```

---

## Architecture

### Core Domain Structure

The application follows a multi-tenant architecture with three primary user roles:

1. **Admin**: System administrators with full access
2. **Landlord**: Property owners managing properties, rooms, and tenants (subscription-based)
3. **Tenant**: Renters with access to their contracts and invoices

### Key Models & Relationships

```
User (multi-role)
├── Properties (as landlord)
│   ├── Rooms
│   │   ├── Contracts
│   │   ├── Meters (electricity/water)
│   │   └── Pricing
│   └── Documents
├── Contracts (as tenant)
│   └── Invoices
│       └── LineItems (auto-synced)
└── UserSubscription
    └── SubscriptionPlan
```

### Service Layer

Business logic is encapsulated in service classes:
- **ContractService**: Contract creation, renewal, termination
- **InvoiceService**: Invoice generation, line item management
- **RoomService**: Room availability, pricing calculations
- **UserService**: User creation, role assignment
- **UtilityService**: Meter readings, utility bill calculations

### Middleware Stack

```php
// Admin routes
Route::middleware(['auth', 'verified', 'role:admin'])

// Landlord routes with subscription check
Route::middleware(['auth', 'verified', 'role:landlord', 'check.subscription'])

// Tenant routes
Route::middleware(['auth', 'verified', 'role:tenant'])
```

### Subscription System

Landlords operate under subscription plans with limits:
- Property limits enforced in PropertyController
- Room limits enforced in RoomController
- Check via `UserSubscription::hasPropertyLimit()` and `hasRoomLimit()`

### Invoice System

Invoices automatically sync line items from:
- Contract monthly rent
- Utility bills (electricity/water based on meter readings)
- Additional fees defined in contracts

Line items are managed through `invoice_line_items` table with proper foreign key constraints.

---

## Database Schema

### Core Tables
- `users` - Multi-role user accounts
- `properties` - Property listings
- `rooms` - Individual rental units
- `contracts` - Rental agreements
- `invoices` - Monthly billing
- `invoice_line_items` - Invoice breakdown (rent, utilities, fees)
- `meters` - Utility meters (electricity/water)
- `meter_readings` - Monthly readings
- `utility_bills` - Calculated utility charges
- `subscription_plans` - Available plans
- `user_subscriptions` - Active subscriptions

### Utility Management Flow
```
Meter → MeterReading → UtilityBill → Invoice LineItem
```

### Migration Order
Migrations are numbered sequentially (001-021) ensuring proper foreign key relationships.

---

## API Endpoints

### Exchange Rates
- `GET /api/exchange-rates` - Fetch current USD/KHR rates from NBC

### Internal AJAX
- `POST /landlord/rooms/{room}/check-availability`
- `GET /landlord/invoices/{invoice}/line-items`
- `POST /landlord/contracts/{contract}/renew`

---

## Configuration

### Environment Variables
```env
# Database
DB_CONNECTION=mysql
DB_DATABASE=roomgate

# Mail (for notifications)
MAIL_MAILER=smtp

# Queue (for background jobs)
QUEUE_CONNECTION=database

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Subscription
SUBSCRIPTION_GRACE_PERIOD=7
```

### Key Config Files
- `config/app.php` - Application settings, locale
- `config/database.php` - Database connections
- `config/mail.php` - Email configuration
- `config/queue.php` - Job queue settings
- `config/subscription.php` - Subscription settings (custom)

---

## Frontend Architecture

### Build System
- **Vite 6.2.4** for asset bundling
- **Tailwind CSS 3.4** for styling
- **Alpine.js 3.x** for interactivity

### Blade Components
```
resources/views/
├── landlord/     # Landlord dashboard views
├── admin/        # Admin panel views
├── tenant/       # Tenant portal views
├── auth/         # Authentication views
├── components/   # Reusable Blade components
└── layouts/      # App layouts
```

### Localization
Supports English (en) and Khmer (kh) via Laravel's localization:
```php
__('property.title')  // Translates based on app locale
```

---

## Testing Strategy

### Test Database
Uses SQLite in-memory database for fast test execution:
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Test Structure
```
tests/
├── Feature/
│   ├── PropertyControllerTest.php
│   ├── ContractManagementTest.php
│   └── SubscriptionLimitTest.php
└── Unit/
    ├── InvoiceServiceTest.php
    └── UtilityCalculationTest.php
```

---

## Deployment Checklist

1. Set environment to production: `APP_ENV=production`
2. Generate optimized autoloader: `composer install --optimize-autoloader --no-dev`
3. Build frontend assets: `npm run build`
4. Cache configuration: `php artisan config:cache`
5. Cache routes: `php artisan route:cache`
6. Cache views: `php artisan view:cache`
7. Run migrations: `php artisan migrate --force`
8. Set proper file permissions for storage/logs
9. Configure queue workers if using queues
10. Set up SSL certificate for HTTPS

---

## Common Development Tasks

### Adding a New Feature
1. Create migration: `php artisan make:migration`
2. Create model: `php artisan make:model ModelName -m`
3. Create controller: `php artisan make:controller ControllerName --resource`
4. Add routes in `routes/web.php`
5. Create service class if complex logic needed
6. Add tests in `tests/Feature` or `tests/Unit`

### Debugging Subscription Limits
```php
// Check user's subscription status
$user->subscription->is_active
$user->subscription->hasPropertyLimit()
$user->subscription->hasRoomLimit()

// View plan limits
$user->subscription->plan->property_limit
$user->subscription->plan->room_limit
```

### Working with Invoices
```php
// Generate invoice for contract
$invoice = Invoice::create([
    'contract_id' => $contract->id,
    'amount' => $contract->monthly_rent,
    'status' => 'pending'
]);

// Line items auto-sync from related tables
$invoice->syncLineItems(); // Custom method to sync all sources
```

## Important Conventions

### Route Naming
- Resource routes: `{role}.{resource}.{action}`
- Example: `landlord.properties.index`

### Controller Organization
- Admin controllers in `App\Http\Controllers\Admin`
- Landlord controllers in `App\Http\Controllers\Landlord`
- Main controllers in `App\Http\Controllers`

### Validation
- Form requests used for complex validation
- Validation rules in controller methods for simple cases

### Authorization
- Spatie Laravel Permission package for role management
- Policies for resource-level authorization
- Middleware for route-level protection
