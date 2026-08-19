<x-mail::message>
# 🎉 You have been assigned as an Instructor!

Dear {{ $instructorName }},

You have been selected as an instructor/presenter for the following event:

---

## 📋 Event Details

**Event Title:** {{ $event->title }}

**Event Type:** {{ ucfirst($event->type) }}

**Topic:** {{ $event->event_topic ?? 'General' }}

**Date:** {{ $event->start_date?->format('F d, Y') ?? 'TBD' }}

**Duration:** {{ $event->duration ?? 'N/A' }}

**Location:** {{ $event->location ?? 'Online' }}

@if(isset($event->workshop_details['event_time']))
**Event Time:** {{ $event->workshop_details['event_time']['start_time'] ?? '' }} - {{ $event->workshop_details['event_time']['end_time'] ?? '' }}
@endif

@if($event->event_mode === 'in-person')
**Mode:** In-Person (Google Maps link will be provided)
@else
**Mode:** Online
@endif

---

@if(!empty($eventFeatures))
## ✨ Event Features

@foreach($eventFeatures as $feature)
- {{ $feature }}
@endforeach
@endif

---

## 📝 Description

{{ $event->description }}

---

@if($event->type === 'workshop' && isset($event->workshop_details['completion_outcome']))
## 🎯 What Participants Will Achieve

{{ $event->workshop_details['completion_outcome'] }}
@endif

---

**Instructor Type:** {{ $instructorType === 'existing' ? 'Registered Teacher' : 'Guest Instructor' }}

We look forward to having you as part of this event!

Best regards,<br>
**Edvora Tech Team**

---

<x-mail::button :url="route('home')">
Visit Edvora Tech
</x-mail::button>

</x-mail::message>
