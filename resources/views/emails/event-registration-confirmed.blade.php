<x-mail::message>
# Registration Confirmed! 🎉

Dear {{ $user->name ?? 'Participant' }},

You have successfully registered in the system!

We will see you at the event!

**Event Details:**

---

## **{{ $event->title }}**

**Event Type:** {{ ucfirst($event->type) }}

**Topic:** {{ $event->event_topic ?? 'General' }}

**Date:** {{ $event->start_date?->format('F d, Y') ?? 'TBD' }}

**Duration:** {{ $event->duration ?? 'N/A' }}

@if(isset($event->workshop_details['event_time']))
**Time:** {{ $event->workshop_details['event_time']['start_time'] ?? '' }} - {{ $event->workshop_details['event_time']['end_time'] ?? '' }}
@endif

**Location:** {{ $event->location ?? 'Online' }}

@if($event->event_mode === 'in-person')
**Mode:** In-Person
@else
**Mode:** Online
@endif

---

@if(isset($event->workshop_details['presenters']) && count($event->workshop_details['presenters']) > 0)
**Speakers:**
@foreach($event->workshop_details['presenters'] as $presenter)
- {{ $presenter['name'] }}
@endforeach
@endif

@if(isset($event->workshop_details['completion_outcome']))
**What You Will Achieve:**
{{ $event->workshop_details['completion_outcome'] }}
@endif

---

@if($event->invitation_card_type === 'pdf')
Your PDF invitation card will be generated and available for download shortly.
@endif

<x-mail::button :url="route('events.detail', $event->slug)" color="primary">
View Event Details
</x-mail::button>

Thank you for registering! See you at the event!

Best regards,<br>
**{{ config('app.name') }} Team**

<x-mail::subcopy>
If you have any questions, please use the Edvora contact form at {{ url('/contact') }}
</x-mail::subcopy>
</x-mail::message>
