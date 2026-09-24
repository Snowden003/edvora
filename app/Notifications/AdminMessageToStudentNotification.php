<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminMessageToStudentNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $subject,
        public string $body,
        public ?string $courseName = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.admin.message-to-student', [
                'student' => $notifiable,
                'subject' => $this->subject,
                'body'    => $this->body,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'admin_message',
            'subject'     => $this->subject,
            'body'        => $this->body,
            'course_name' => $this->courseName,
        ];
    }
}
