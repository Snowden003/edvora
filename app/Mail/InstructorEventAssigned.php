<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstructorEventAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $instructorName;
    public $instructorType;
    public $eventFeatures;

    /**
     * Create a new message instance.
     */
    public function __construct(Event $event, string $instructorName, string $instructorType = 'existing', array $eventFeatures = [])
    {
        $this->event = $event;
        $this->instructorName = $instructorName;
        $this->instructorType = $instructorType;
        $this->eventFeatures = $eventFeatures;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have been assigned as instructor for: ' . $this->event->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.instructor-assigned',
            with: [
                'event' => $this->event,
                'instructorName' => $this->instructorName,
                'instructorType' => $this->instructorType,
                'eventFeatures' => $this->eventFeatures,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
