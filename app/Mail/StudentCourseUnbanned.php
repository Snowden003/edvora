<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentCourseUnbanned extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $student,
        public Course $course,
        public User $teacher,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Good News: Course Access Reinstated - ' . $this->course->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.enrollment.unbanned',
            with: [
                'student' => $this->student,
                'course' => $this->course,
                'teacher' => $this->teacher,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
