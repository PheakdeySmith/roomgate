<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'room_id',
        'contract_id',
        'name',
        'type',
        'file_path',
        'description',
    ];

    /**
     * Get the user that owns the document.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the room that the document is associated with.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    
    /**
     * Get the contract that the document is associated with.
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
