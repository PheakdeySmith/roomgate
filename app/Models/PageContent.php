<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    protected $table = 'page_contents';
    protected $fillable = [
        'key', 'title', 'subtitle', 'content', 'image_path', 'button_text', 'button_link', 'video_url'
    ];
}
