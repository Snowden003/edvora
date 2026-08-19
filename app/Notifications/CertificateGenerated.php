<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateGenerated extends Notification
{
    use Queueable;

    public Certificate $certificate;

    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('🎉 Congratulations! Your Certificate is Ready')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We are thrilled to inform you that you have successfully completed your course!')
            ->line('')
            ->line('📜 **Certificate Details:**')
            ->line('• Course: ' . ($this->certificate->course?->title ?? 'N/A'))
            ->line('• Certificate Number: ' . $this->certificate->certificate_number)
            ->line('• Issue Date: ' . ($this->certificate->issued_at?->format('F d, Y') ?? now()->format('F d, Y')))
            ->line('')
            ->line('You can view and download your certificate from your student dashboard.')
            ->action('View My Certificates', url('/student/certificates'))
            ->line('')
            ->line('Keep up the great work and continue your learning journey with us!')
            ->line('')
            ->salutation('Best regards,\nThe Edvora Team');

        // Attach certificate image if available
        if ($this->certificate->image_path) {
            $imagePath = storage_path('app/public/' . $this->certificate->image_path);
            if (file_exists($imagePath)) {
                $mailMessage->attach($imagePath, [
                    'as' => 'certificate-' . $this->certificate->certificate_number . '.png',
                    'mime' => 'image/png',
                ]);
            }
        }

        return $mailMessage;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'certificate_id' => $this->certificate->id,
            'title' => $this->certificate->title,
            'course_title' => $this->certificate->course?->title,
            'certificate_number' => $this->certificate->certificate_number,
            'message' => 'Your certificate for ' . ($this->certificate->course?->title ?? 'course') . ' is ready!',
            'action_url' => '/student/certificates',
            'image_path' => $this->certificate->image_path,
        ];
    }
}
