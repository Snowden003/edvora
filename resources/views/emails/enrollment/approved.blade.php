<x-mail::message>
# Congratulations, {{ $student->name }}!

Your enrollment request for **{{ $course->title }}** has been **approved**!

You are now officially enrolled in this course. Here are the class details:

<x-mail::panel>
**Course:** {{ $course->title }}

**Level:** {{ ucfirst($course->level) }}

**Duration:** {{ $course->duration_hours }} hours

@if($course->primary_class_days)
**Class Days:** {{ is_array($course->primary_class_days) ? implode(', ', $course->primary_class_days) : $course->primary_class_days }}
@endif

@if($course->primary_class_start && $course->primary_class_end)
**Class Time:** {{ $course->primary_class_start }} - {{ $course->primary_class_end }}
@endif

@if($course->primary_class_note)
**Note:** {{ $course->primary_class_note }}
@endif
</x-mail::panel>

<x-mail::button :url="config('app.url') . '/courses/' . $course->slug">
View Course
</x-mail::button>

Welcome to the class! We look forward to seeing you there.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
