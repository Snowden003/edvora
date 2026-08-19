<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'amount',
        'message',
        'show_name',
        'status',
        'donated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'show_name' => 'boolean',
        'donated_at' => 'datetime',
    ];
}
