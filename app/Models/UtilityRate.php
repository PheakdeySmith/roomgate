<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilityRate extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'effective_from' => 'date',
    ];

    /**
     * Get the property this rate applies to.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the type of utility this rate is for.
     */
    public function utilityType(): BelongsTo
    {
        return $this->belongsTo(UtilityType::class);
    }
}