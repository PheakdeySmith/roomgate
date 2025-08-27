<?php

use App\Models\UtilityType;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UtilityRateController;
use App\Http\Controllers\UtilityTypeController;
use App\Http\Controllers\MeterReadingController;
use App\Http\Controllers\PriceOverrideController;

Route::get('/unauthorized', function () {
    return view('backends.partials.errors.unauthorized');
})->name('unauthorized');

Route::get('/accessDenied', function () {
    return view('backends.partials.errors.access_denied');
})->name('accessDenied');

Route::get('language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes (global access)
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('utility_types', UtilityTypeController::class);

        // Admin Dashboard and Subscription Management
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

        // Subscription Plans
        Route::get('/subscription-plans', [App\Http\Controllers\Admin\AdminDashboardController::class, 'subscriptionPlans'])->name('subscription-plans.index');
        Route::get('/subscription-plans/create', [App\Http\Controllers\Admin\AdminDashboardController::class, 'createSubscriptionPlan'])->name('subscription-plans.create');
        Route::post('/subscription-plans', [App\Http\Controllers\Admin\AdminDashboardController::class, 'storeSubscriptionPlan'])->name('subscription-plans.store');
        Route::get('/subscription-plans/{plan}/edit', [App\Http\Controllers\Admin\AdminDashboardController::class, 'editSubscriptionPlan'])->name('subscription-plans.edit');
        Route::put('/subscription-plans/{plan}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'updateSubscriptionPlan'])->name('subscription-plans.update');
        Route::delete('/subscription-plans/{plan}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'deleteSubscriptionPlan'])->name('subscription-plans.destroy');

        // User Subscriptions
        Route::get('/subscriptions', [App\Http\Controllers\Admin\AdminDashboardController::class, 'userSubscriptions'])->name('subscriptions.index');
        Route::get('/subscriptions/create', [App\Http\Controllers\Admin\AdminDashboardController::class, 'createUserSubscription'])->name('subscriptions.create');
        Route::post('/subscriptions', [App\Http\Controllers\Admin\AdminDashboardController::class, 'storeUserSubscription'])->name('subscriptions.store');
        Route::get('/subscriptions/{subscription}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'showUserSubscription'])->name('subscriptions.show');
        Route::post('/subscriptions/{subscription}/cancel', [App\Http\Controllers\Admin\AdminDashboardController::class, 'cancelUserSubscription'])->name('subscriptions.cancel');
        Route::post('/subscriptions/{subscription}/renew', [App\Http\Controllers\Admin\AdminDashboardController::class, 'renewUserSubscription'])->name('subscriptions.renew');

        Route::get('/frontend/hero', [App\Http\Controllers\Admin\FrontendController::class, 'hero'])->name('hero');
        // Route::post('/frontend/hero', [App\Http\Controllers\Admin\FrontendController::class, 'storeHero'])->name('hero.store');
        Route::put('/frontend/hero/{id}', [App\Http\Controllers\Admin\FrontendController::class, 'updateHero'])->name('hero.update');
        // Route::delete('/frontend/hero/{id}', [App\Http\Controllers\Admin\FrontendController::class, 'destroyHero'])->name('hero.destroy');

        Route::get('/frontend/benefit', [App\Http\Controllers\Admin\FrontendController::class, 'benefit'])->name('benefit');
        Route::post('/frontend/benefit', [App\Http\Controllers\Admin\FrontendController::class, 'storeBenefit'])->name('benefit.store');
        Route::put('/frontend/benefit/{id}', [App\Http\Controllers\Admin\FrontendController::class, 'updateBenefit'])->name('benefit.update');
        Route::delete('/frontend/benefit/{id}', [App\Http\Controllers\Admin\FrontendController::class, 'destroyBenefit'])->name('benefit.destroy');

        Route::get('/frontend/feature', [App\Http\Controllers\Admin\FrontendController::class, 'feature'])->name('feature');
        Route::post('/frontend/feature', [App\Http\Controllers\Admin\FrontendController::class, 'storeFeature'])->name('feature.store');
        Route::put('/frontend/feature/{id}', [App\Http\Controllers\Admin\FrontendController::class, 'updateFeature'])->name('feature.update');
        Route::delete('/frontend/feature/{id}', [App\Http\Controllers\Admin\FrontendController::class, 'destroyFeature'])->name('feature.destroy');

        Route::get('/frontend/faq', [App\Http\Controllers\Admin\FrontendController::class, 'faq'])->name('faq');
        Route::post('/frontend/faq', [App\Http\Controllers\Admin\FrontendController::class, 'storeFaq'])->name('faq.store');
        Route::put('/frontend/faq/{faq}', [App\Http\Controllers\Admin\FrontendController::class, 'updateFaq'])->name('faq.update');
        Route::delete('/frontend/faq/{faq}', [App\Http\Controllers\Admin\FrontendController::class, 'destroyFaq'])->name('faq.destroy');

    });


// Landlord Routes (tenant scoped)
Route::middleware(['auth', 'role:landlord', 'subscription.check'])
    ->prefix('landlord')
    ->name('landlord.')
    ->group(function () {
    
        // Subscription management
        Route::get('/subscription/plans', [App\Http\Controllers\Landlord\SubscriptionController::class, 'plans'])
            ->name('subscription.plans');
        Route::get('/subscription/checkout/{plan}', [App\Http\Controllers\Landlord\SubscriptionController::class, 'checkout'])
            ->name('subscription.checkout');
        Route::post('/subscription/purchase/{plan}', [App\Http\Controllers\Landlord\SubscriptionController::class, 'purchase'])
            ->name('subscription.purchase');
        Route::get('/subscription/success/{subscription}', [App\Http\Controllers\Landlord\SubscriptionController::class, 'success'])
            ->name('subscription.success');
        Route::get('/subscription/check-status', [App\Http\Controllers\Landlord\SubscriptionStatusController::class, 'check'])
            ->name('subscription.check-status');

        // User Profile
        Route::get('/profile', [App\Http\Controllers\Landlord\ProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [App\Http\Controllers\Landlord\ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [App\Http\Controllers\Landlord\ProfileController::class, 'updatePassword'])->name('password.update');
        Route::put('/profile/qrcodes', [App\Http\Controllers\Landlord\ProfileController::class, 'updateQRCodes'])->name('qrcodes.update');
        Route::put('/profile/currency', [App\Http\Controllers\Landlord\ProfileController::class, 'updateCurrencySettings'])->name('currency.update');
        Route::get('/profile/currency/fetch-rate', [App\Http\Controllers\Landlord\ProfileController::class, 'fetchExchangeRate'])->name('currency.fetch-rate');
        Route::post('/getFormattedMoney', [App\Http\Controllers\Landlord\ProfileController::class, 'getFormattedMoney'])->name('getFormattedMoney');

        // Test route for null rent_amount
    
        Route::resource('users', UserController::class);
        Route::resource('properties', PropertyController::class);
        Route::post('/landlord/properties/{property}/rooms', [RoomController::class, 'storeRoom'])->name('properties.rooms.store');
        Route::get('properties/{property}/create-price', [PropertyController::class, 'createPrice'])->name('properties.createPrice');
        Route::post('properties/{property}/store-price', [PropertyController::class, 'storePrice'])->name('properties.storePrice');
        Route::put('properties/{property}/update-price', [PropertyController::class, 'updatePrice'])->name('properties.updatePrice');
        Route::delete('properties/{property}/destroy-price', [PropertyController::class, 'destroyPrice'])->name('properties.destroyPrice');
        Route::get('/properties/{property}/room-types/{roomType}/overrides', [PriceOverrideController::class, 'index'])->name('properties.roomTypes.overrides.index');
        Route::post('/properties/{property}/room-types/{roomType}/overrides', [PriceOverrideController::class, 'store'])->name('properties.roomTypes.overrides.store');
        Route::put('/properties/{property}/room-types/{roomType}/overrides/{override}', [PriceOverrideController::class, 'update'])->name('properties.roomTypes.overrides.update');
        Route::delete('/properties/{property}/room-types/{roomType}/overrides/{override}', [PriceOverrideController::class, 'destroy'])->name('properties.roomTypes.overrides.destroy');
        Route::resource('room_types', RoomTypeController::class);
        Route::resource('contracts', ContractController::class);
        Route::get('find-tenant-contract/{userId}', [ContractController::class, 'findTenantContract'])->name('findTenantContract');
        Route::post('contracts/{contract}/document', [App\Http\Controllers\DocumentController::class, 'uploadContractDocument'])->name('contracts.document.upload');
        Route::resource('rooms', controller: RoomController::class);
        Route::get('/properties/{property}/room-types', [RoomController::class, 'getRoomTypesForProperty'])->name('properties.roomTypes');

        Route::resource('amenities', AmenityController::class);

        // --- Payment MANAGEMENT ---
        Route::resource('payments', controller: PaymentController::class);
        Route::get('/payments/get-contract-details/{contract}', [PaymentController::class, 'getContractDetails'])->name('payments.getContractDetails');
        Route::get('/payments/get-invoice-details/{invoice}', [PaymentController::class, 'getInvoiceDetails'])->name('payments.getInvoiceDetails');
        Route::get('/payments/filter', [PaymentController::class, 'filter'])->name('payments.filter');
        Route::patch('/payments/{invoice}/status', [PaymentController::class, 'updateStatus'])->name('payments.updateStatus');

        // --- END OF PAYMENT MANAGEMENT ---
    
        // --- UTILITY RATE MANAGEMENT ROUTES FOR A SPECIFIC PROPERTY ---
        Route::get('/properties/{property}/rates', [UtilityRateController::class, 'index'])->name('properties.rates.index');
        Route::post('/properties/{property}/rates', [UtilityRateController::class, 'store'])->name('properties.rates.store');
        Route::put('/utility-rates/{rate}', [UtilityRateController::class, 'update'])->name('utility_rates.update');
        Route::delete('/utility-rates/{rate}', [UtilityRateController::class, 'destroy'])->name('utility_rates.destroy');

        // --- END OF UTILITY RATE ROUTES ---
    
        // --- METER MANAGEMENT ---
        Route::post('/meters', [MeterController::class, 'store'])->name('meters.store');
        Route::patch('/meters/{meter}', [MeterController::class, 'update'])->name('meters.update');
        Route::patch('/meters/{meter}/deactivate', [MeterController::class, 'deactivate'])->name('meters.deactivate');
        Route::patch('/meters/{meter}/toggle-status', [MeterController::class, 'toggleStatus'])->name('meters.toggle-status');
        Route::get('/meters/{meter}/history', [MeterController::class, 'getMeterHistory'])->name('meters.history');
        // --- END OF METER MANAGEMENT ---
    
        // --- METER READING MANAGEMENT ---
        Route::post('/meter-readings', [MeterReadingController::class, 'store'])->name('meter-readings.store');
        // --- END OF METER READING MANAGEMENT ---
    
        // --- REPORTS MANAGEMENT ---
        Route::get('/reports', [App\Http\Controllers\Landlord\ReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/room-occupancy', [App\Http\Controllers\Landlord\ReportsController::class, 'roomOccupancy'])->name('reports.room-occupancy');
        Route::get('/reports/tenant-report', [App\Http\Controllers\Landlord\ReportsController::class, 'tenantReport'])->name('reports.tenant-report');
        Route::get('/reports/financial-report', [App\Http\Controllers\Landlord\ReportsController::class, 'financialReport'])->name('reports.financial-report');
        // --- END OF REPORTS MANAGEMENT ---
    
    });

// Tenant Routes (view only)
Route::middleware(['auth', 'role:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\TenantDashboardController::class, 'index'])->name('dashboard');
    Route::get('/invoices', [App\Http\Controllers\TenantDashboardController::class, 'allInvoices'])->name('invoices');
    Route::get('/invoices/{invoice}/details', [App\Http\Controllers\TenantDashboardController::class, 'getInvoiceDetails'])->name('invoices.details');
    Route::get('/utility-bills', [App\Http\Controllers\TenantDashboardController::class, 'allUtilityBills'])->name('utility-bills');
    Route::get('/utility-usage', [App\Http\Controllers\TenantDashboardController::class, 'utilityUsage'])->name('utility-usage');
    Route::get('/utility-readings/{meterId}', [App\Http\Controllers\TenantDashboardController::class, 'getMeterReadings'])->name('utility-readings');
    Route::get('/profile', [App\Http\Controllers\TenantDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\TenantDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/document', [App\Http\Controllers\TenantDashboardController::class, 'uploadDocument'])->name('profile.document.upload');
    Route::delete('/profile/document/{document}', [App\Http\Controllers\TenantDashboardController::class, 'deleteDocument'])->name('profile.document.delete');
});

Route::get('/', [FrontendController::class, 'index'])->name('frontend');
Route::get('/features', [FrontendController::class, 'feature'])->name('features');
Route::get('/terms', [FrontendController::class, 'terms'])->name('terms');
Route::get('/pricing', [FrontendController::class, 'pricing'])->name('pricing');

// Contact Support Page
Route::middleware(['auth'])->group(function () {
    Route::get('/contact-support', [App\Http\Controllers\ContactController::class, 'index'])->name('contact');
    Route::post('/contact-support', [App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');
});

require __DIR__ . '/auth.php';

// Shared document routes - accessible to both landlords and tenants
Route::middleware(['auth'])->group(function () {
    Route::get('/documents/{document}/download', [App\Http\Controllers\DocumentController::class, 'download'])->name('tenant.document.download');
    Route::delete('/documents/{document}', [App\Http\Controllers\DocumentController::class, 'delete'])->name('documents.delete');

    // API routes for exchange rates
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/exchange-rates', [App\Http\Controllers\API\ExchangeRateController::class, 'getAllRates'])->name('exchange-rates.all');
        Route::get('/exchange-rates/currency', [App\Http\Controllers\API\ExchangeRateController::class, 'getRate'])->name('exchange-rates.currency');
        Route::get('/exchange-rates/currencies', [App\Http\Controllers\API\ExchangeRateController::class, 'getSupportedCurrencies'])->name('exchange-rates.currencies');
    });
});

// Include emergency routes
require __DIR__ . '/emergency.php';
