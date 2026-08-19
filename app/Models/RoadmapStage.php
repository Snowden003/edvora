<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadmapStage extends Model
{
    protected $fillable = [
        'stage_number',
        'title',
        'description',
        'icon',
        'image_url',
        'duration',
        'skills',
        'order',
        'is_active',
    ];

    protected $casts = [
        'skills'    => 'array',
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];
}
