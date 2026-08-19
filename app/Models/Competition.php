<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'thumbnail',
        'status', 'prizes', 'start_date', 'end_date', 'participants_count',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'prizes'     => 'array',
    ];
}
