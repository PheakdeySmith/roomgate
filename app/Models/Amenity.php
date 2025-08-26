<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    protected $fillable = [
        'name',
        'description',
        'amenity_price',
        'landlord_id',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'amenity_room');
    }

    public function roomTypes()
    {
        return $this->belongsToMany(RoomType::class, 'amenity_room_type');
    }
}
