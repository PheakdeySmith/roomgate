<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeterReading extends Model
{
    use HasFactory;
    protected $fillable = [
        'meter_id',
        'reading_value',
        'reading_date',
        'recorded_by_id',
    ];
    protected $casts = [
        'reading_date' => 'date',
    ];

    /**
     * Get the meter that this reading belongs to.
     */
    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    /**
     * Get the user who recorded this reading.
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }
}