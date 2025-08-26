<?php

namespace App\Models;

use Carbon\Carbon;
use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id',
        'room_type_id',
        'room_number',
        'description',
        'size',
        'floor',
        'status',
    ];

    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_MAINTENANCE = 'maintenance';

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'amenity_room');
    }

    public function meters()
    {
        return $this->hasMany(Meter::class);
    }

    public function activeContract()
    {
        return $this->hasOne(Contract::class)->where('status', 'active')->latestOfMany();
    }
    
    public function currentContract()
    {
        return $this->hasOne(Contract::class)
            ->where('end_date', '>', now())
            ->orderBy('end_date', 'asc');
    }

    public function getMeterStatusAttribute(): array
    {
        // If the room is vacant (no active contract), it doesn't need a reading.
        if (!$this->activeContract) {
            return ['text' => 'Vacant', 'class' => 'secondary', 'icon' => 'moon'];
        }

        // If the room has no meters assigned, it can't be read.
        if ($this->meters->isEmpty()) {
            return ['text' => 'No Meters', 'class' => 'info', 'icon' => 'help-hexagon'];
        }

        // Find the most recent reading date from any of the room's meters
        $latestReadingDate = null;
        foreach ($this->meters as $meter) {
            $latestForThisMeter = $meter->meterReadings()->latest('reading_date')->first();
            if ($latestForThisMeter) {
                $currentDate = Carbon::parse($latestForThisMeter->reading_date);
                if (!$latestReadingDate || $currentDate->gt($latestReadingDate)) {
                    $latestReadingDate = $currentDate;
                }
            }
        }

        // If there are no readings at all for any meter
        if (!$latestReadingDate) {
            return ['text' => 'Needs Reading', 'class' => 'warning', 'icon' => 'clock-hour-4'];
        }

        $now = Carbon::now();

        // Check if the reading was done in the current month
        if ($latestReadingDate->isSameMonth($now, 'year')) {
            return ['text' => 'Recorded', 'class' => 'success', 'icon' => 'check'];
        }

        // Check if the reading is more than a month old
        if ($latestReadingDate->lt($now->copy()->subMonth()->startOfMonth())) {
            $monthsOverdue = $latestReadingDate->diffInMonths($now);
            return ['text' => "{$monthsOverdue} Month(s) Overdue", 'class' => 'danger', 'icon' => 'alert-triangle'];
        }

        // Default to "Needs Reading" for last month's reading
        return ['text' => 'Needs Reading', 'class' => 'warning', 'icon' => 'clock-hour-4'];
    }


    public function activeMeters()
    {
        return $this->hasMany(Meter::class)->where('status', 'active');
    }

    public function allMeters()
    {
        return $this->hasMany(Meter::class)->orderBy('created_at', 'desc');
    }

}
