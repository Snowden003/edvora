<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopSchedule extends Model
{
    protected $fillable = [
        'event_id',
        'day',
        'topics',
        'description',
    ];

    protected $casts = [
        'description' => 'string',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
