<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meter extends Model
{
    use HasFactory;
    protected $fillable = [
        'property_id',
        'room_id',
        'utility_type_id',
        'meter_number',
        'initial_reading',
        'installed_at',
        'description',
    ];

    protected $casts = [
        'installed_at' => 'date',
    ];

    /**
     * Get the type of utility this meter measures (e.g., Electricity).
     */
    public function utilityType(): BelongsTo
    {
        return $this->belongsTo(UtilityType::class);
    }

    /**
     * Get the property this meter belongs to.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the specific room this meter is for (if it's a sub-meter).
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get all the historical readings for this meter.
     */
    public function meterReadings(): HasMany
    {
        return $this->hasMany(MeterReading::class);
    }
}