<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'room_type_id',
        'price',
        'effective_date',
    ];

    /**
     * Get the room type that owns the base price.
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get the property that owns the base price.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}