<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $table = 'features';
    protected $fillable = [
        'title', 'description', 'image_path', 'link', 'bullets', 'order', 'is_highlighted'
    ];

    protected $casts = [
        'bullets' => 'array',
        'is_highlighted' => 'boolean',
    ];
}
