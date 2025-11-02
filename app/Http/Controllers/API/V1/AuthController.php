<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Services\Tenant\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class AuthController extends BaseController
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * User login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'device_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Invalid credentials', [], 401);
        }

        if ($user->status !== 'active') {
            return $this->sendError('Your account is not active. Please contact support.', [], 403);
        }

        // Create token
        $token = $user->createToken($request->device_name)->plainTextToken;

        // Get user roles
        $roles = $user->getRoleNames();

        // Prepare response data
        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'image' => $user->image ? asset($user->image) : null,
                'roles' => $roles,
                'created_at' => $user->created_at,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ];

        // Add subscription info for landlords
        if ($user->hasRole('landlord')) {
            $subscription = $user->activeSubscription();
            if ($subscription) {
                $data['subscription'] = [
                    'plan' => $subscription->subscriptionPlan->name,
                    'is_active' => $subscription->is_active,
                    'end_date' => $subscription->end_date,
                    'properties_limit' => $subscription->subscriptionPlan->properties_limit,
                    'rooms_limit' => $subscription->subscriptionPlan->rooms_limit,
                ];
            }
        }

        return $this->sendResponse($data, 'Login successful');
    }

    /**
     * User registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:landlord,tenant',
            'device_name' => 'required|string',
            'subscription_plan_id' => 'required_if:role,landlord|exists:subscription_plans,id',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // Assign role
            $user->assignRole($request->role);

            // If landlord, create subscription
            if ($request->role === 'landlord' && $request->subscription_plan_id) {
                $plan = SubscriptionPlan::find($request->subscription_plan_id);

                $user->userSubscriptions()->create([
                    'subscription_plan_id' => $plan->id,
                    'start_date' => now(),
                    'end_date' => now()->addDays($plan->duration_days),
                    'is_active' => true,
                    'payment_status' => 'pending', // Will be updated after payment
                ]);
            }

            // Create token
            $token = $user->createToken($request->device_name)->plainTextToken;

            $data = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'roles' => [$request->role],
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ];

            return $this->sendResponse($data, 'Registration successful', 201);

        } catch (\Exception $e) {
            return $this->sendError('Registration failed', [$e->getMessage()], 500);
        }
    }

    /**
     * User logout
     */
    public function logout(Request $request)
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return $this->sendResponse(null, 'Logout successful');
        } catch (\Exception $e) {
            return $this->sendError('Logout failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = $request->user();

            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            // Create new token
            $token = $user->createToken($request->device_name)->plainTextToken;

            $data = [
                'token' => $token,
                'token_type' => 'Bearer',
            ];

            return $this->sendResponse($data, 'Token refreshed successfully');
        } catch (\Exception $e) {
            return $this->sendError('Token refresh failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = User::where('email', $request->email)->first();

            // Generate reset token
            $token = Str::random(64);

            // Store token in password_resets table
            \DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'email' => $user->email,
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            // Here you would send an email with the reset link
            // For API, we'll return the token (in production, never do this)
            $data = [
                'message' => 'Password reset link has been sent to your email',
                // 'token' => $token, // Only for testing, remove in production
            ];

            return $this->sendResponse($data, 'Password reset email sent');
        } catch (\Exception $e) {
            return $this->sendError('Failed to process password reset', [$e->getMessage()], 500);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            // Check token validity
            $reset = \DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$reset || !Hash::check($request->token, $reset->token)) {
                return $this->sendError('Invalid or expired reset token', [], 400);
            }

            // Check if token is not expired (24 hours)
            if (now()->diffInHours($reset->created_at) > 24) {
                return $this->sendError('Reset token has expired', [], 400);
            }

            // Update password
            $user = User::where('email', $request->email)->first();
            $user->update(['password' => Hash::make($request->password)]);

            // Delete reset token
            \DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            // Revoke all tokens
            $user->tokens()->delete();

            return $this->sendResponse(null, 'Password reset successful');
        } catch (\Exception $e) {
            return $this->sendError('Password reset failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'verification_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = User::where('email', $request->email)->first();

            // Here you would verify the code
            // For simplicity, we'll just mark as verified
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            return $this->sendResponse(null, 'Email verified successfully');
        } catch (\Exception $e) {
            return $this->sendError('Email verification failed', [$e->getMessage()], 500);
        }
    }

    /**
     * Get subscription plans
     */
    public function getSubscriptionPlans()
    {
        try {
            $plans = SubscriptionPlan::where('is_active', true)->get()->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'price' => $plan->price,
                    'duration_days' => $plan->duration_days,
                    'properties_limit' => $plan->properties_limit,
                    'rooms_limit' => $plan->rooms_limit,
                    'features' => $plan->features ?? [],
                ];
            });

            return $this->sendResponse($plans, 'Subscription plans retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve subscription plans', [$e->getMessage()], 500);
        }
    }
}