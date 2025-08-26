<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'landlord_id',
        'name',
        'property_type',
        'description',
        'address_line_1',
        'address_line_2',
        'city',
        'state_province',
        'postal_code',
        'country',
        'year_built',
        'cover_image',
        'status',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function contracts()
    {
        return $this->hasManyThrough(Contract::class, Room::class);
    }

    public function roomTypes()
    {
        return $this->belongsToMany(RoomType::class, 'base_prices')
            ->withPivot('price', 'effective_date')
            ->withTimestamps();
    }

    public function utilityRates()
    {
        return $this->hasMany(UtilityRate::class);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->landlord_id === $user->id;
    }
}
