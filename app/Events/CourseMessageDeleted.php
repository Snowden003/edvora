<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseMessageDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public int $courseId, public int $messageId)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('course-chat.'.$this->courseId),
            new Channel('course-chat-bg.'.$this->courseId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'course.message.deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'course_id'  => $this->courseId,
            'message_id' => $this->messageId,
        ];
    }
}
