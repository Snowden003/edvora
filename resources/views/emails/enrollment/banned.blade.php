<x-mail::message>
# Hello {{ $student->name }},

We are writing to inform you that your student account access has been **suspended / banned** by instructor **{{ $teacher->name }}** for the course **{{ $course->title }}**.

<x-mail::panel>
**Student Name:** {{ $student->name }}  
**Course:** {{ $course->title }}  
**Instructor:** {{ $teacher->name }}  
**Account Status:** Suspended / Banned  
**Effective Date:** {{ now()->format('M d, Y - H:i') }}  

@if(!empty($reason))
**Instructor Note:**  
{{ $reason }}
@endif
</x-mail::panel>

### What this means:
- You can no longer access your student dashboard, courses, or learning materials.
- Access to upcoming live class sessions, discussion channels, and quizzes has been suspended.

If you believe this action was made in error or you would like to request an appeal, please reach out to our support team.

<x-mail::button :url="config('app.url') . '/contact'">
Contact Support
</x-mail::button>

Sincerely,<br>
**{{ config('app.name') }} Support Team**
</x-mail::message>
