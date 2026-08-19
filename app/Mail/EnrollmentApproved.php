<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $student,
        public Course $course,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enrollment Approved - ' . $this->course->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.enrollment.approved',
            with: [
                'student' => $this->student,
                'course' => $this->course,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
