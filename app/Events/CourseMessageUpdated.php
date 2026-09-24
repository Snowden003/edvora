<?php

namespace App\Events;

use App\Models\CourseMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseMessageUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public CourseMessage $message)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('course-chat.'.$this->message->course_id),
            new Channel('course-chat-bg.'.$this->message->course_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'course.message.updated';
    }

    public function broadcastWith(): array
    {
        $avatar = $this->message->user->avatar;

        return [
            'message' => [
                'id' => $this->message->id,
                'body' => $this->message->body,
                'is_pinned' => (bool) $this->message->is_pinned,
                'is_edited' => (bool) $this->message->is_edited,
                'created_at' => $this->message->created_at->toIso8601String(),
                'updated_at' => $this->message->updated_at->toIso8601String(),
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    'avatar' => $avatar ? (str_starts_with($avatar, 'http') ? $avatar : asset('storage/' . $avatar)) : null,
                    'role' => $this->message->user->role,
                ],
            ],
        ];
    }
}
