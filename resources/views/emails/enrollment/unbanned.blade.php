<x-mail::message>
# Welcome Back, {{ $student->name }}!

Your access to **{{ $course->title }}** has been reinstated by instructor **{{ $teacher->name }}**.

You can now continue your learning, view lessons, and participate in upcoming live class sessions.

<x-mail::button :url="config('app.url') . '/student/courses/' . $course->slug . '/learn'">
Continue Learning
</x-mail::button>

Sincerely,<br>
**{{ config('app.name') }} Team**
</x-mail::message>
