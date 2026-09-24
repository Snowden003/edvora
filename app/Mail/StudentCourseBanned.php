<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentCourseBanned extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $student,
        public Course $course,
        public User $teacher,
        public ?string $reason = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Important Notice: Account Suspended - ' . $this->course->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.enrollment.banned',
            with: [
                'student' => $this->student,
                'course' => $this->course,
                'teacher' => $this->teacher,
                'reason' => $this->reason,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
