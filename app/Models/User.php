<?php

namespace App\Models;

use App\Models\Amenity;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'qr_code_1',
        'qr_code_2',
        'phone',
        'status',
        'landlord_id',
        'currency_code',
        'exchange_rate',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'landlord_id');
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class, 'landlord_id');
    }

    public function amenities()
    {
        return $this->hasMany(Amenity::class, 'landlord_id');
    }

    public function rooms()
    {
        return $this->hasManyThrough(Room::class, Property::class, 'landlord_id', 'property_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'user_id');
    }

    public function assignContracts()
    {
        return $this->hasMany(Contract::class, 'user_id');
    }

    public function currentContract()
{
    return $this->hasOne(Contract::class, 'user_id')->latest('end_date');
}

    public function isLandlord(): bool
    {
        return $this->hasRole('landlord');
    }

    /**
     * Get all subscriptions for this user
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the active subscription for this user
     */
    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('payment_status', 'paid')
            ->where('end_date', '>', now())
            ->latest('end_date')
            ->first();
    }
    
    /**
     * Get the latest subscription for this user (active or not)
     */
    public function latestSubscription()
    {
        return $this->subscriptions()
            ->latest('end_date')
            ->first();
    }

    /**
     * Check if user has an active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }
    
    /**
     * Check if user has an inactive subscription
     */
    public function hasInactiveSubscription(): bool
    {
        // Check if user has no active subscription
        if (!$this->activeSubscription()) {
            // Get the latest subscription, regardless of status
            $latestSubscription = $this->latestSubscription();
            
            // If there's a latest subscription with inactive or cancelled status, mark as inactive
            if ($latestSubscription && in_array($latestSubscription->status, ['inactive', 'cancelled'])) {
                return true;
            }
            
            // If there's no active subscription at all, also consider as inactive
            return true;
        }
        
        // Check if the active subscription is marked as inactive or cancelled
        if ($this->activeSubscription()->status !== 'active') {
            return true;
        }
        
        // Check if there's an expired subscription
        return $this->subscriptions()
            ->where(function($query) {
                $query->where('status', 'inactive')
                    ->orWhere('status', 'cancelled')
                    ->orWhere('end_date', '<', now());
            })
            ->exists();
    }

    /**
     * Check if user is on trial
     */
    public function isOnTrial(): bool
    {
        $subscription = $this->activeSubscription();
        return $subscription && $subscription->payment_status === 'trial';
    }
    
    /**
     * Check if user has reached property limit
     */
    public function hasReachedPropertyLimit(): bool
    {
        $subscription = $this->activeSubscription();
        if (!$subscription) {
            return true;
        }
        
        $limit = $subscription->subscriptionPlan->properties_limit;

        // Count how many properties the landlord currently has.
        $count = $this->properties()->count();

        // Return true if the count is greater than or equal to the limit.
        return $count >= $limit;
    }
    
    /**
     * Check if user has reached room limit
     */
    public function hasReachedRoomLimit(): bool
    {
        $subscription = $this->activeSubscription();
        if (!$subscription) {
            return true; // No subscription means limit is reached
        }
        
        $plan = $subscription->subscriptionPlan;
        return $this->rooms()->count() >= $plan->rooms_limit;
    }
}
