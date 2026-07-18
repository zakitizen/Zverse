<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Short extends Model
{
    protected $fillable = [
        'title', 'description', 'author', 'handle', 'category',
        'video_url', 'thumbnail', 'likes', 'comments', 'shares',
        'views', 'duration', 'tags', 'verified',
    ];

    protected $casts = [
        'tags'     => 'array',
        'verified' => 'boolean',
        'likes'    => 'integer',
        'comments' => 'integer',
        'shares'   => 'integer',
    ];
}
