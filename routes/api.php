<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\PropertyController;
use App\Http\Controllers\API\V1\RoomController;
use App\Http\Controllers\API\V1\ContractController;
use App\Http\Controllers\API\V1\InvoiceController;
use App\Http\Controllers\API\V1\PaymentController;
use App\Http\Controllers\API\V1\TenantController;
use App\Http\Controllers\API\V1\DashboardController;
use App\Http\Controllers\API\V1\ReportController;
use App\Http\Controllers\API\V1\UtilityController;
use App\Http\Controllers\API\V1\NotificationController;
use App\Http\Controllers\API\V1\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1
Route::prefix('v1')->group(function () {

    // ==================== Authentication Routes ====================
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
        Route::post('verify-email', [AuthController::class, 'verifyEmail']);
    });

    // ==================== Authenticated Routes ====================
    Route::middleware('auth:sanctum')->group(function () {

        // ==================== Profile Management ====================
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'show']);
            Route::put('/', [ProfileController::class, 'update']);
            Route::post('/avatar', [ProfileController::class, 'updateAvatar']);
            Route::put('/password', [ProfileController::class, 'updatePassword']);
            Route::delete('/', [ProfileController::class, 'destroy']);
        });

        // ==================== Dashboard Routes ====================
        Route::prefix('dashboard')->group(function () {
            Route::get('landlord', [DashboardController::class, 'landlordDashboard'])
                ->middleware('role:landlord');
            Route::get('tenant', [DashboardController::class, 'tenantDashboard'])
                ->middleware('role:tenant');
            Route::get('admin', [DashboardController::class, 'adminDashboard'])
                ->middleware('role:admin');
            Route::get('stats', [DashboardController::class, 'getStats']);
        });

        // ==================== Landlord Routes ====================
        Route::middleware('role:landlord')->group(function () {

            // Property Management
            Route::apiResource('properties', PropertyController::class);
            Route::prefix('properties/{property}')->group(function () {
                Route::get('rooms', [PropertyController::class, 'getRooms']);
                Route::get('stats', [PropertyController::class, 'getStats']);
                Route::get('occupancy', [PropertyController::class, 'getOccupancy']);
                Route::post('upload-image', [PropertyController::class, 'uploadImage']);
                Route::delete('delete-image/{image}', [PropertyController::class, 'deleteImage']);
            });

            // Room Management
            Route::apiResource('rooms', RoomController::class);
            Route::prefix('rooms')->group(function () {
                Route::post('{room}/check-availability', [RoomController::class, 'checkAvailability']);
                Route::get('{room}/availability-calendar', [RoomController::class, 'getAvailabilityCalendar']);
                Route::post('{room}/amenities', [RoomController::class, 'attachAmenities']);
                Route::delete('{room}/amenities/{amenity}', [RoomController::class, 'detachAmenity']);
                Route::post('{room}/meters', [RoomController::class, 'addMeter']);
                Route::get('{room}/meters', [RoomController::class, 'getMeters']);
                Route::put('{room}/status', [RoomController::class, 'updateStatus']);
            });

            // Contract Management
            Route::apiResource('contracts', ContractController::class);
            Route::prefix('contracts')->group(function () {
                Route::post('{contract}/renew', [ContractController::class, 'renew']);
                Route::post('{contract}/terminate', [ContractController::class, 'terminate']);
                Route::get('{contract}/invoices', [ContractController::class, 'getInvoices']);
                Route::get('{contract}/payments', [ContractController::class, 'getPayments']);
                Route::post('{contract}/upload-document', [ContractController::class, 'uploadDocument']);
                Route::get('expiring', [ContractController::class, 'getExpiringContracts']);
                Route::get('expired', [ContractController::class, 'getExpiredContracts']);
            });

            // Invoice Management
            Route::apiResource('invoices', InvoiceController::class);
            Route::prefix('invoices')->group(function () {
                Route::post('{invoice}/send', [InvoiceController::class, 'send']);
                Route::post('{invoice}/void', [InvoiceController::class, 'void']);
                Route::get('{invoice}/line-items', [InvoiceController::class, 'getLineItems']);
                Route::post('{invoice}/line-items', [InvoiceController::class, 'addLineItem']);
                Route::delete('{invoice}/line-items/{lineItem}', [InvoiceController::class, 'removeLineItem']);
                Route::get('overdue', [InvoiceController::class, 'getOverdueInvoices']);
                Route::post('bulk-create', [InvoiceController::class, 'bulkCreate']);
                Route::get('templates', [InvoiceController::class, 'getTemplates']);
                Route::post('preview', [InvoiceController::class, 'preview']);
            });

            // Payment Management
            Route::apiResource('payments', PaymentController::class)->only(['index', 'store', 'show']);
            Route::prefix('payments')->group(function () {
                Route::post('{payment}/refund', [PaymentController::class, 'refund']);
                Route::get('methods', [PaymentController::class, 'getPaymentMethods']);
                Route::post('bulk', [PaymentController::class, 'bulkPayment']);
                Route::get('history', [PaymentController::class, 'getHistory']);
                Route::get('pending', [PaymentController::class, 'getPendingPayments']);
                Route::post('{payment}/receipt', [PaymentController::class, 'generateReceipt']);
            });

            // Tenant Management
            Route::apiResource('tenants', TenantController::class);
            Route::prefix('tenants')->group(function () {
                Route::get('{tenant}/contracts', [TenantController::class, 'getContracts']);
                Route::get('{tenant}/invoices', [TenantController::class, 'getInvoices']);
                Route::get('{tenant}/payments', [TenantController::class, 'getPayments']);
                Route::get('{tenant}/documents', [TenantController::class, 'getDocuments']);
                Route::post('{tenant}/invite', [TenantController::class, 'sendInvite']);
                Route::post('{tenant}/archive', [TenantController::class, 'archive']);
                Route::post('{tenant}/restore', [TenantController::class, 'restore']);
            });

            // Reports
            Route::prefix('reports')->group(function () {
                Route::get('financial', [ReportController::class, 'financialReport']);
                Route::get('occupancy', [ReportController::class, 'occupancyReport']);
                Route::get('tenant', [ReportController::class, 'tenantReport']);
                Route::get('revenue', [ReportController::class, 'revenueReport']);
                Route::get('expense', [ReportController::class, 'expenseReport']);
                Route::get('utility', [ReportController::class, 'utilityReport']);
                Route::post('export', [ReportController::class, 'exportReport']);
                Route::get('summary', [ReportController::class, 'getSummary']);
            });

            // Utility Management
            Route::prefix('utilities')->group(function () {
                Route::get('meters', [UtilityController::class, 'getMeters']);
                Route::post('meters', [UtilityController::class, 'createMeter']);
                Route::put('meters/{meter}', [UtilityController::class, 'updateMeter']);
                Route::delete('meters/{meter}', [UtilityController::class, 'deleteMeter']);
                Route::post('readings', [UtilityController::class, 'recordReading']);
                Route::get('readings/{meter}', [UtilityController::class, 'getMeterReadings']);
                Route::get('bills', [UtilityController::class, 'getUtilityBills']);
                Route::post('bills/calculate', [UtilityController::class, 'calculateBills']);
                Route::get('consumption/{meter}', [UtilityController::class, 'getConsumptionHistory']);
            });
        });

        // ==================== Tenant Routes ====================
        Route::middleware('role:tenant')->group(function () {

            // My Rental
            Route::prefix('my-rental')->group(function () {
                Route::get('contract', [TenantController::class, 'getCurrentContract']);
                Route::get('invoices', [TenantController::class, 'getMyInvoices']);
                Route::get('payments', [TenantController::class, 'getMyPayments']);
                Route::get('documents', [TenantController::class, 'getMyDocuments']);
                Route::post('request-renewal', [TenantController::class, 'requestRenewal']);
                Route::post('maintenance-request', [TenantController::class, 'submitMaintenanceRequest']);
                Route::get('maintenance-requests', [TenantController::class, 'getMaintenanceRequests']);
                Route::get('utility-usage', [TenantController::class, 'getUtilityUsage']);
            });

            // Tenant Payments
            Route::prefix('my-payments')->group(function () {
                Route::post('pay', [PaymentController::class, 'makePayment']);
                Route::get('history', [PaymentController::class, 'getMyPaymentHistory']);
                Route::get('pending', [PaymentController::class, 'getMyPendingPayments']);
                Route::post('setup-auto-pay', [PaymentController::class, 'setupAutoPay']);
                Route::delete('cancel-auto-pay', [PaymentController::class, 'cancelAutoPay']);
            });
        });

        // ==================== Notification Routes ====================
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('unread', [NotificationController::class, 'getUnread']);
            Route::post('{notification}/read', [NotificationController::class, 'markAsRead']);
            Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead']);
            Route::delete('{notification}', [NotificationController::class, 'destroy']);
            Route::get('preferences', [NotificationController::class, 'getPreferences']);
            Route::put('preferences', [NotificationController::class, 'updatePreferences']);
        });

        // ==================== Common Routes ====================

        // Search
        Route::prefix('search')->group(function () {
            Route::get('properties', [PropertyController::class, 'search']);
            Route::get('rooms', [RoomController::class, 'search']);
            Route::get('tenants', [TenantController::class, 'search']);
            Route::get('invoices', [InvoiceController::class, 'search']);
            Route::get('global', [DashboardController::class, 'globalSearch']);
        });

        // Settings
        Route::prefix('settings')->group(function () {
            Route::get('/', [ProfileController::class, 'getSettings']);
            Route::put('/', [ProfileController::class, 'updateSettings']);
            Route::get('subscription', [ProfileController::class, 'getSubscription']);
            Route::post('subscription/upgrade', [ProfileController::class, 'upgradeSubscription']);
        });

        // File Management
        Route::prefix('files')->group(function () {
            Route::post('upload', [ProfileController::class, 'uploadFile']);
            Route::get('{file}', [ProfileController::class, 'getFile']);
            Route::delete('{file}', [ProfileController::class, 'deleteFile']);
        });

        // Exchange Rates (Public data but requires auth)
        Route::get('exchange-rates', [DashboardController::class, 'getExchangeRates']);
    });

    // ==================== Webhook Routes (No Auth Required) ====================
    Route::prefix('webhooks')->group(function () {
        Route::post('payment/{provider}', [PaymentController::class, 'handleWebhook']);
        Route::post('sms/delivery', [NotificationController::class, 'handleSmsDelivery']);
    });

    // ==================== Public Routes ====================
    Route::prefix('public')->group(function () {
        Route::get('subscription-plans', [AuthController::class, 'getSubscriptionPlans']);
        Route::get('property-types', [PropertyController::class, 'getPropertyTypes']);
        Route::get('amenities', [RoomController::class, 'getAmenityList']);
        Route::get('utility-types', [UtilityController::class, 'getUtilityTypes']);
    });
});

// Fallback for undefined API routes
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found. Please check the documentation.',
        'error' => 'Not Found'
    ], 404);
});