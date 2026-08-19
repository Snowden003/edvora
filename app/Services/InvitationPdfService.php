<?php

namespace App\Services;

use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvitationPdfService
{
    /**
     * Generate a PDF invitation card for an event registration
     *
     * @param Event $event
     * @param string $attendeeName
     * @return string Path to the generated PDF
     */
    public function generateInvitationPdf(Event $event, string $attendeeName): string
    {
        $data = [
            'attendee_name' => $attendeeName,
            'event_title' => $event->title,
            'event_date' => $event->start_date?->format('F d, Y'),
            'event_time' => $event->start_date?->format('H:i'),
            'event_location' => $event->location ?: 'Online',
            'event_duration' => $event->duration,
            'event_presenter' => $event->presenter,
            'registration_date' => now()->format('F d, Y'),
            'registration_code' => $this->generateRegistrationCode($event, $attendeeName),
        ];

        $pdf = Pdf::loadView('pdf.invitation-card', $data);
        $pdf->setPaper([0, 0, 226.77, 340.16], 'portrait'); // Small card size (80mm x 120mm)

        $filename = 'invitations/' . $event->id . '_' . Str::slug($attendeeName) . '_' . time() . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Generate a unique registration code
     */
    private function generateRegistrationCode(Event $event, string $attendeeName): string
    {
        $prefix = 'EDV';
        $eventCode = strtoupper(substr($event->slug, 0, 3));
        $random = strtoupper(substr(md5($attendeeName . $event->id . time()), 0, 6));

        return $prefix . '-' . $eventCode . '-' . $random;
    }
}
