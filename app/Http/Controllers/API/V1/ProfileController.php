<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\V1\BaseController;
use App\Models\User;
use App\Models\Document;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use App\Services\Payment\PaymentService;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProfileController extends BaseController
{
    protected $paymentService;
    protected $notificationService;

    public function __construct(
        PaymentService $paymentService,
        NotificationService $notificationService
    ) {
        $this->paymentService = $paymentService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get authenticated user profile
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        try {
            $user = Auth::user()->load([
                'roles',
                'subscription.plan',
                'properties' => function ($query) {
                    $query->select('id', 'name', 'address', 'type', 'landlord_id')
                          ->withCount(['rooms', 'activeContracts']);
                },
                'tenantContracts' => function ($query) {
                    $query->active()
                          ->with(['room.property:id,name,address'])
                          ->latest();
                }
            ]);

            // Add computed fields
            $profile = $user->toArray();

            if ($user->hasRole('landlord')) {
                $profile['subscription_status'] = [
                    'is_active' => $user->subscription?->is_active ?? false,
                    'plan_name' => $user->subscription?->plan?->name,
                    'expires_at' => $user->subscription?->end_date,
                    'days_remaining' => $user->subscription?->end_date
                        ? Carbon::parse($user->subscription->end_date)->diffInDays(now())
                        : null,
                    'property_usage' => [
                        'used' => $user->properties()->count(),
                        'limit' => $user->subscription?->plan?->property_limit ?? 0
                    ],
                    'room_usage' => [
                        'used' => $user->properties()->withCount('rooms')->get()->sum('rooms_count'),
                        'limit' => $user->subscription?->plan?->room_limit ?? 0
                    ]
                ];
            }

            if ($user->hasRole('tenant')) {
                $activeContract = $user->tenantContracts()->active()->first();
                $profile['rental_status'] = [
                    'has_active_contract' => !is_null($activeContract),
                    'property' => $activeContract?->room?->property?->name,
                    'room' => $activeContract?->room?->room_number,
                    'monthly_rent' => $activeContract?->monthly_rent,
                    'contract_ends' => $activeContract?->end_date
                ];
            }

            // Add statistics
            $profile['statistics'] = $this->getUserStatistics($user);

            return $this->sendResponse($profile, 'Profile retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve profile', $e->getMessage(), 500);
        }
    }

    /**
     * Update user profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                'phone' => 'sometimes|nullable|string|max:20',
                'address' => 'sometimes|nullable|string|max:500',
                'date_of_birth' => 'sometimes|nullable|date|before:today',
                'national_id' => 'sometimes|nullable|string|max:50',
                'emergency_contact' => 'sometimes|nullable|string|max:255',
                'emergency_phone' => 'sometimes|nullable|string|max:20',
                'language' => 'sometimes|nullable|in:en,kh',
                'timezone' => 'sometimes|nullable|timezone',
                'notification_preferences' => 'sometimes|nullable|array',
                'notification_preferences.email' => 'boolean',
                'notification_preferences.sms' => 'boolean',
                'notification_preferences.push' => 'boolean',
                'notification_preferences.database' => 'boolean'
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator->errors());
            }

            DB::beginTransaction();

            // Update basic profile
            $profileData = $request->except(['notification_preferences']);
            $user->fill($profileData);

            // Handle notification preferences
            if ($request->has('notification_preferences')) {
                $user->notification_preferences = $request->notification_preferences;
            }

            $user->save();

            DB::commit();

            return $this->sendResponse($user->fresh(), 'Profile updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to update profile', $e->getMessage(), 500);
        }
    }

    /**
     * Update profile picture
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAvatar(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'avatar' => 'required|image|mimes:jpeg,png,jpg|max:5120' // 5MB max
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator->errors());
            }

            $user = Auth::user();

            // Delete old avatar if exists
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();

            return $this->sendResponse([
                'avatar_url' => Storage::url($path)
            ], 'Avatar updated successfully');

        } catch (\Exception $e) {
            return $this->sendError('Failed to update avatar', $e->getMessage(), 500);
        }
    }

    /**
     * Change password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|confirmed'
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator->errors());
            }

            $user = Auth::user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return $this->sendError('Invalid current password', null, 400);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Send notification
            $this->notificationService->sendPasswordChangedNotification($user);

            // Revoke current token for security
            $user->currentAccessToken()->delete();

            return $this->sendResponse(null, 'Password changed successfully. Please login again.');

        } catch (\Exception $e) {
            return $this->sendError('Failed to change password', $e->getMessage(), 500);
        }
    }

    /**
     * Get user documents
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function documents(Request $request)
    {
        try {
            $user = Auth::user();

            $query = Document::where('uploaded_by', $user->id);

            // Filter by type
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Filter by entity
            if ($request->has('entity_type')) {
                $query->where('documentable_type', $request->entity_type);
            }

            $documents = $query->with('documentable')
                              ->orderBy('created_at', 'desc')
                              ->paginate(20);

            return $this->sendPaginatedResponse($documents, 'Documents retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve documents', $e->getMessage(), 500);
        }
    }

    /**
     * Upload document
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadDocument(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB
                'type' => 'required|in:contract,invoice,receipt,id_card,other',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'entity_type' => 'nullable|string|in:contract,property,room',
                'entity_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator->errors());
            }

            $user = Auth::user();

            // Store document
            $path = $request->file('document')->store('documents/' . $user->id, 'public');

            $document = Document::create([
                'name' => $request->name,
                'type' => $request->type,
                'file_path' => $path,
                'file_size' => $request->file('document')->getSize(),
                'file_type' => $request->file('document')->getMimeType(),
                'description' => $request->description,
                'uploaded_by' => $user->id,
                'documentable_type' => $request->entity_type,
                'documentable_id' => $request->entity_id
            ]);

            return $this->sendResponse($document, 'Document uploaded successfully');

        } catch (\Exception $e) {
            return $this->sendError('Failed to upload document', $e->getMessage(), 500);
        }
    }

    /**
     * Delete document
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDocument($id)
    {
        try {
            $user = Auth::user();
            $document = Document::where('uploaded_by', $user->id)->findOrFail($id);

            // Delete file from storage
            if (Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }

            $document->delete();

            return $this->sendResponse(null, 'Document deleted successfully');

        } catch (\Exception $e) {
            return $this->sendError('Failed to delete document', $e->getMessage(), 500);
        }
    }

    /**
     * Get subscription details (landlord only)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscription()
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Subscription is only available for landlords', null, 403);
            }

            $subscription = $user->subscription()->with('plan')->first();

            if (!$subscription) {
                // Get available plans for upgrade
                $plans = SubscriptionPlan::where('is_active', true)
                                        ->orderBy('price')
                                        ->get();

                return $this->sendResponse([
                    'has_subscription' => false,
                    'available_plans' => $plans
                ], 'No active subscription');
            }

            // Calculate usage
            $propertyCount = $user->properties()->count();
            $roomCount = $user->properties()->withCount('rooms')->get()->sum('rooms_count');

            $subscriptionData = [
                'subscription' => $subscription,
                'usage' => [
                    'properties' => [
                        'used' => $propertyCount,
                        'limit' => $subscription->plan->property_limit,
                        'percentage' => $subscription->plan->property_limit > 0
                            ? round(($propertyCount / $subscription->plan->property_limit) * 100, 2)
                            : 0
                    ],
                    'rooms' => [
                        'used' => $roomCount,
                        'limit' => $subscription->plan->room_limit,
                        'percentage' => $subscription->plan->room_limit > 0
                            ? round(($roomCount / $subscription->plan->room_limit) * 100, 2)
                            : 0
                    ]
                ],
                'billing_history' => $this->paymentService->getUserPaymentHistory($user->id, 10),
                'upgrade_options' => SubscriptionPlan::where('is_active', true)
                                                    ->where('price', '>', $subscription->plan->price)
                                                    ->orderBy('price')
                                                    ->get()
            ];

            return $this->sendResponse($subscriptionData, 'Subscription details retrieved');

        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve subscription', $e->getMessage(), 500);
        }
    }

    /**
     * Upgrade subscription plan
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upgradeSubscription(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'plan_id' => 'required|exists:subscription_plans,id',
                'payment_method' => 'required|in:card,bank_transfer,aba,wing',
                'payment_details' => 'required|array'
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator->errors());
            }

            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Only landlords can upgrade subscriptions', null, 403);
            }

            $newPlan = SubscriptionPlan::findOrFail($request->plan_id);
            $currentSubscription = $user->subscription;

            DB::beginTransaction();

            // Calculate prorated amount if upgrading mid-cycle
            $amount = $newPlan->price;
            if ($currentSubscription && $currentSubscription->is_active) {
                $daysRemaining = Carbon::parse($currentSubscription->end_date)->diffInDays(now());
                $dailyRate = $currentSubscription->plan->price / 30;
                $credit = $daysRemaining * $dailyRate;
                $amount = max(0, $newPlan->price - $credit);
            }

            // Process payment
            $payment = $this->paymentService->processSubscriptionPayment([
                'user_id' => $user->id,
                'amount' => $amount,
                'plan_id' => $newPlan->id,
                'payment_method' => $request->payment_method,
                'payment_details' => $request->payment_details
            ]);

            if (!$payment['success']) {
                DB::rollBack();
                return $this->sendError('Payment failed', $payment['message'], 400);
            }

            // Update subscription
            $newEndDate = $currentSubscription && $currentSubscription->is_active
                ? Carbon::parse($currentSubscription->end_date)->addDays($newPlan->duration_days)
                : now()->addDays($newPlan->duration_days);

            UserSubscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'subscription_plan_id' => $newPlan->id,
                    'start_date' => now(),
                    'end_date' => $newEndDate,
                    'is_active' => true,
                    'payment_id' => $payment['payment_id']
                ]
            );

            // Send confirmation notification
            $this->notificationService->sendSubscriptionUpgradeNotification($user, $newPlan);

            DB::commit();

            return $this->sendResponse([
                'subscription' => $user->subscription()->with('plan')->first(),
                'payment' => $payment
            ], 'Subscription upgraded successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to upgrade subscription', $e->getMessage(), 500);
        }
    }

    /**
     * Cancel subscription
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelSubscription(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|max:500',
                'feedback' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator->errors());
            }

            $user = Auth::user();
            $subscription = $user->subscription;

            if (!$subscription || !$subscription->is_active) {
                return $this->sendError('No active subscription to cancel', null, 400);
            }

            DB::beginTransaction();

            // Mark subscription as cancelled (will remain active until end date)
            $subscription->is_cancelled = true;
            $subscription->cancellation_reason = $request->reason;
            $subscription->cancellation_feedback = $request->feedback;
            $subscription->cancelled_at = now();
            $subscription->save();

            // Send cancellation notification
            $this->notificationService->sendSubscriptionCancellationNotification($user);

            DB::commit();

            return $this->sendResponse([
                'subscription' => $subscription,
                'message' => 'Subscription will remain active until ' . $subscription->end_date
            ], 'Subscription cancelled successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to cancel subscription', $e->getMessage(), 500);
        }
    }

    /**
     * Get user activity log
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activityLog(Request $request)
    {
        try {
            $user = Auth::user();

            $activities = DB::table('activity_log')
                          ->where('causer_id', $user->id)
                          ->where('causer_type', 'App\\Models\\User')
                          ->orderBy('created_at', 'desc')
                          ->limit(50)
                          ->get();

            return $this->sendResponse($activities, 'Activity log retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve activity log', $e->getMessage(), 500);
        }
    }

    /**
     * Delete user account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAccount(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required',
                'confirmation' => 'required|in:DELETE_MY_ACCOUNT'
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator->errors());
            }

            $user = Auth::user();

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return $this->sendError('Invalid password', null, 400);
            }

            // Check for active contracts or outstanding payments
            if ($user->hasRole('tenant')) {
                $activeContracts = $user->tenantContracts()->active()->count();
                if ($activeContracts > 0) {
                    return $this->sendError('Cannot delete account with active contracts', null, 400);
                }
            }

            if ($user->hasRole('landlord')) {
                $activeContracts = $user->properties()
                                      ->with('rooms.contracts')
                                      ->get()
                                      ->pluck('rooms')
                                      ->flatten()
                                      ->pluck('contracts')
                                      ->flatten()
                                      ->where('status', 'active')
                                      ->count();

                if ($activeContracts > 0) {
                    return $this->sendError('Cannot delete account with active tenant contracts', null, 400);
                }
            }

            DB::beginTransaction();

            // Soft delete user account
            $user->deleted_at = now();
            $user->email = $user->email . '_deleted_' . time();
            $user->save();

            // Revoke all tokens
            $user->tokens()->delete();

            // Send account deletion notification
            $this->notificationService->sendAccountDeletionNotification($user);

            DB::commit();

            return $this->sendResponse(null, 'Account deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to delete account', $e->getMessage(), 500);
        }
    }

    /**
     * Get user statistics
     *
     * @param User $user
     * @return array
     */
    private function getUserStatistics(User $user)
    {
        $stats = [];

        if ($user->hasRole('landlord')) {
            $stats = [
                'total_properties' => $user->properties()->count(),
                'total_rooms' => $user->properties()->withCount('rooms')->get()->sum('rooms_count'),
                'occupied_rooms' => $user->properties()
                    ->with(['rooms.contracts' => function ($q) {
                        $q->active();
                    }])
                    ->get()
                    ->pluck('rooms')
                    ->flatten()
                    ->filter(function ($room) {
                        return $room->contracts->isNotEmpty();
                    })
                    ->count(),
                'total_tenants' => $user->properties()
                    ->with(['rooms.contracts' => function ($q) {
                        $q->active()->with('tenant');
                    }])
                    ->get()
                    ->pluck('rooms')
                    ->flatten()
                    ->pluck('contracts')
                    ->flatten()
                    ->pluck('tenant')
                    ->unique('id')
                    ->count(),
                'monthly_revenue' => $user->properties()
                    ->with(['rooms.contracts' => function ($q) {
                        $q->active();
                    }])
                    ->get()
                    ->pluck('rooms')
                    ->flatten()
                    ->pluck('contracts')
                    ->flatten()
                    ->sum('monthly_rent'),
                'pending_payments' => DB::table('invoices')
                    ->join('contracts', 'invoices.contract_id', '=', 'contracts.id')
                    ->join('rooms', 'contracts.room_id', '=', 'rooms.id')
                    ->join('properties', 'rooms.property_id', '=', 'properties.id')
                    ->where('properties.landlord_id', $user->id)
                    ->where('invoices.status', 'pending')
                    ->sum('invoices.total_amount')
            ];
        }

        if ($user->hasRole('tenant')) {
            $stats = [
                'total_contracts' => $user->tenantContracts()->count(),
                'active_contracts' => $user->tenantContracts()->active()->count(),
                'total_payments' => DB::table('payments')
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->join('contracts', 'invoices.contract_id', '=', 'contracts.id')
                    ->where('contracts.tenant_id', $user->id)
                    ->sum('payments.amount'),
                'pending_invoices' => DB::table('invoices')
                    ->join('contracts', 'invoices.contract_id', '=', 'contracts.id')
                    ->where('contracts.tenant_id', $user->id)
                    ->where('invoices.status', 'pending')
                    ->count(),
                'overdue_amount' => DB::table('invoices')
                    ->join('contracts', 'invoices.contract_id', '=', 'contracts.id')
                    ->where('contracts.tenant_id', $user->id)
                    ->where('invoices.status', 'overdue')
                    ->sum('invoices.total_amount')
            ];
        }

        return $stats;
    }
}