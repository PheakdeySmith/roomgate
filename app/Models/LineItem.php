<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LineItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_id',
        'description',
        'amount',
        'paid_amount',
        'status',
        'lineable_id',
        'lineable_type',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function lineable(): MorphTo
    {
        return $this->morphTo();
    }
}