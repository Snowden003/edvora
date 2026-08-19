<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoringRule extends Model
{
    protected $fillable = [
        'action_name',
        'default_score',
        'type',
        'label',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_score' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
