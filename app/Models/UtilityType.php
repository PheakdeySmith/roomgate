<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UtilityType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'unit_of_measure',
        'billing_type',
    ];

    /**
     * Get the meters associated with this utility type.
     */
    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }

    /**
     * Get the utility rates associated with this utility type.
     */
    public function utilityRates(): HasMany
    {
        return $this->hasMany(UtilityRate::class);
    }
}