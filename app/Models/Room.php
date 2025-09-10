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
        
        $now = Carbon::now();
        $allMetersRecorded = true;
        $oldestReadingDate = null;
        $hasOverdueReading = false;
        
        // Check each active meter to see if ANY of them need a reading
        foreach ($this->activeMeters as $meter) {
            $latestReading = $meter->meterReadings()->latest('reading_date')->first();
            
            // If this meter has no readings at all
            if (!$latestReading) {
                // This meter needs a reading
                $allMetersRecorded = false;
                break;
            }
            
            $readingDate = Carbon::parse($latestReading->reading_date);
            
            // Track the oldest reading date
            if (!$oldestReadingDate || $readingDate->lt($oldestReadingDate)) {
                $oldestReadingDate = $readingDate;
            }
            
            // If this reading was not done in the current month
            if (!$readingDate->isSameMonth($now)) {
                $allMetersRecorded = false;
                
                // Check if it's more than a month old (overdue)
                if ($readingDate->lt($now->copy()->subMonth()->startOfMonth())) {
                    $hasOverdueReading = true;
                }
            }
        }
        
        // If any active meter is overdue, show the overdue status
        if ($hasOverdueReading && $oldestReadingDate) {
            $monthsOverdue = $oldestReadingDate->diffInMonths($now);
            return ['text' => "{$monthsOverdue} Month(s) Overdue", 'class' => 'danger', 'icon' => 'alert-triangle'];
        }
        
        // If all meters have readings from the current month
        if ($allMetersRecorded) {
            return ['text' => 'Recorded', 'class' => 'success', 'icon' => 'check'];
        }
        
        // Default to "Needs Reading" if any meter needs a current month reading
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
