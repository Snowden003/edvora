<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'event_topic', 'thumbnail', 'location', 'google_maps_url',
        'type', 'event_mode', 'start_date', 'end_date', 'duration', 'presenter',
        'max_attendees', 'registered_count', 'status',
        'invitation_card_type', 'invitation_card_content', 'invitation_card_file', 'workshop_details',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'workshop_details' => 'json',
    ];

    public function workshopSchedules(): HasMany
    {
        return $this->hasMany(WorkshopSchedule::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function registeredUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registrations')
            ->withPivot('status', 'created_at')
            ->withTimestamps();
    }

    public function isUserRegistered(int $userId): bool
    {
        return $this->registrations()
            ->where('user_id', $userId)
            ->where('status', 'registered')
            ->exists();
    }

    /**
     * Check if event is active and open for registration
     */
    public function isOpenForRegistration(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if event has reached capacity
     */
    public function isFull(): bool
    {
        if (is_null($this->max_attendees)) {
            return false;
        }
        return $this->getActualRegisteredCount() >= $this->max_attendees;
    }

    /**
     * Get actual registered count from registrations table
     */
    public function getActualRegisteredCount(): int
    {
        return $this->registrations()
            ->where('status', 'registered')
            ->count();
    }
}
