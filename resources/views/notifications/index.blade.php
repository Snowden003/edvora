@extends('layouts.app')

@section('title', 'Notifications - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/notifications.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="dashboard-wrapper">
    <!-- Sidebar Navigation -->
    @if(Auth::user()->role === 'teacher')
        <x-teacher-sidebar />
    @else
        <x-student-sidebar />
    @endif

    <!-- Main Dashboard Content -->
    <main class="main-content">
        <div class="container-fluid py-5">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-premium mb-1">Notifications</h2>
                    <p class="text-muted mb-0">
                        @if($unreadCount > 0)
                            <span class="badge bg-danger me-2">{{ $unreadCount }} unread</span>
                        @endif
                        Stay updated with your latest activities
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @if($unreadCount > 0)
                        <button type="button" class="btn btn-outline-primary" onclick="markAllAsRead()">
                            <i class="bi bi-check-all me-2"></i>Mark All as Read
                        </button>
                    @endif
                    @if($notifications->count() > 0)
                        <button type="button" class="btn btn-outline-danger" onclick="deleteAllNotifications()">
                            <i class="bi bi-trash3 me-2"></i>Delete All
                        </button>
                    @endif
                    <button type="button" class="btn btn-outline-secondary" onclick="refreshNotifications()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                    </button>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="card border-0 shadow-lg">
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item list-group-item-action notification-item {{ !$notification->is_read ? 'unread' : '' }}" 
                                     data-notification-id="{{ $notification->id }}"
                                     onclick="markAsRead({{ $notification->id }})">
                                    <div class="d-flex w-100 justify-content-between">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <i class="{{ $notification->icon }} fs-4"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 {{ !$notification->is_read ? 'fw-bold' : '' }}">
                                                    {{ $notification->title }}
                                                    @if(!$notification->is_read)
                                                        <span class="badge bg-primary ms-2">New</span>
                                                    @endif
                                                </h6>
                                                <p class="mb-2 text-muted">{{ $notification->message }}</p>

                                                @php
                                                    $notifData = is_array($notification->data) ? $notification->data : json_decode($notification->data ?? '[]', true);
                                                    $meetLink = $notifData['meet_link'] ?? $notifData['room_url'] ?? null;
                                                @endphp

                                                @if($notification->type === \App\Models\Notification::TYPE_CLASS_STARTED && $meetLink)
                                                    <div class="mt-2 mb-2">
                                                        <a href="{{ $meetLink }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm" onclick="event.stopPropagation()">
                                                            <i class="bi bi-camera-video-fill me-1"></i> Join Google Meet Now ↗
                                                        </a>
                                                    </div>
                                                @elseif($notification->type === \App\Models\Notification::TYPE_ENROLLMENT_REQUEST)
                                                    <div class="mt-2 mb-2">
                                                        <a href="{{ route('teacher.enrollment-requests') }}?tab=requests" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" onclick="event.stopPropagation()">
                                                            <i class="bi bi-person-check-fill me-1"></i> Review Requests
                                                        </a>
                                                    </div>
                                                @elseif($notification->type === \App\Models\Notification::TYPE_STUDENT_ENROLLED)
                                                    <div class="mt-2 mb-2">
                                                        <a href="{{ route('teacher.enrollment-requests') }}?tab=enrolled" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="event.stopPropagation()">
                                                            <i class="bi bi-people-fill me-1"></i> View Students
                                                        </a>
                                                    </div>
                                                @elseif($notification->type === \App\Models\Notification::TYPE_ENROLLMENT_APPROVED && !empty($notifData['course_slug']))
                                                    <div class="mt-2 mb-2">
                                                        <a href="{{ route('student.courses.learn', $notifData['course_slug']) }}" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" onclick="event.stopPropagation()">
                                                            <i class="bi bi-journal-bookmark-fill me-1"></i> Go to Classroom
                                                        </a>
                                                    </div>
                                                @elseif($notification->type === \App\Models\Notification::TYPE_EXAM_PUBLISHED)
                                                    <div class="mt-2 mb-2">
                                                        <a href="{{ route('student.exams.index') }}" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" onclick="event.stopPropagation()">
                                                            <i class="bi bi-pencil-square me-1"></i> Take Quiz
                                                        </a>
                                                    </div>
                                                @endif

                                                <div class="d-flex align-items-center gap-3">
                                                    <small class="text-muted">
                                                        <i class="bi bi-tag me-1"></i>{{ $notification->type_label }}
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(!$notification->is_read)
                                                <span class="badge bg-primary rounded-circle p-2">
                                                    <i class="bi bi-dot fs-4"></i>
                                                </span>
                                            @endif
                                            <button class="btn btn-sm btn-outline-danger notif-delete-btn"
                                                    onclick="deleteNotification(event, {{ $notification->id }})">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bell text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">No Notifications</h5>
                            <p class="text-muted">You're all caught up! No new notifications.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/notifications.js') }}"></script>
@endpush
