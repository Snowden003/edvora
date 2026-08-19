<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $student,
        public Course $course,
        public string $reason,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enrollment Update - ' . $this->course->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.enrollment.rejected',
            with: [
                'student' => $this->student,
                'course' => $this->course,
                'reason' => $this->reason,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
