@extends('layouts.app')

@section('title', ($course->title ?? 'Course Details') . ' - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/teacher-courses-detail.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="dashboard-wrapper">
    <x-teacher-sidebar />

    <main class="main-content">
        <div class="container-fluid py-4 px-xl-5">

            @php
                $totalLessons  = $course->lessons->count();
                $totalStudents = $enrollments->count();
                $avgProgress   = $totalStudents > 0 ? round($enrollments->avg('progress_percentage')) : 0;
                $avgRating     = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : ($course->rating ?? 0);

                // avatar helper
                $avatarUrl = function($av, $name) {
                    if (!$av) return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=1F8FFF&color=fff&size=80';
                    if (str_starts_with($av,'http')) return $av;
                    if (str_starts_with($av,'/storage/') || str_starts_with($av,'storage/')) return asset(ltrim($av,'/'));
                    return asset('storage/' . $av);
                };
            @endphp

            {{-- ── HERO ─────────────────────────────────────── --}}
            <div class="cd-hero">
                <div class="row align-items-start g-4">
                    <div class="col-lg-8">
                        <div class="cd-hero-badge">
                            <i class="bi bi-tag-fill"></i>
                            {{ $course->category?->name ?? 'Uncategorized' }}
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <h1 class="cd-title mb-0">{{ $course->title }}</h1>
                            <a href="{{ route('teacher.courses.export-pdf', $course->id) }}"
                               class="btn rounded-3 fw-bold px-4 py-2 flex-shrink-0"
                               style="background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;border:none;font-size:.88rem;">
                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>Export PDF Report
                            </a>
                        </div>
                        <div class="cd-desc">{!! nl2br(strip_tags($course->description)) !!}</div>

                        <div class="cd-meta-row">
                            <div class="cd-meta-item">
                                <div class="cd-meta-icon" style="background:rgba(31,143,255,0.15);">
                                    <i class="bi bi-calendar-check" style="color:#6ab4ff;"></i>
                                </div>
                                <div>
                                    <span class="cd-meta-label">Created</span>
                                    <span class="cd-meta-val">{{ $course->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="cd-meta-item">
                                <div class="cd-meta-icon" style="background:rgba(251,191,36,0.15);">
                                    <i class="bi bi-hourglass-split" style="color:#fbbf24;"></i>
                                </div>
                                <div>
                                    <span class="cd-meta-label">Duration</span>
                                    <span class="cd-meta-val">{{ $course->duration_hours }} hrs</span>
                                </div>
                            </div>
                            <div class="cd-meta-item">
                                <div class="cd-meta-icon" style="background:rgba(16,185,129,0.15);">
                                    <i class="bi bi-bar-chart-fill" style="color:#10b981;"></i>
                                </div>
                                <div>
                                    <span class="cd-meta-label">Level</span>
                                    <span class="cd-meta-val">{{ ucfirst($course->level) }}</span>
                                </div>
                            </div>
                            <div class="cd-meta-item">
                                <div class="cd-meta-icon" style="background:rgba(99,102,241,0.15);">
                                    <i class="bi bi-circle-fill fs-6
                                        {{ $course->status==='published' ? '' : '' }}"
                                        style="color:{{ $course->status==='published' ? '#10b981' : ($course->status==='draft' ? '#fbbf24' : '#94a3b8') }};"></i>
                                </div>
                                <div>
                                    <span class="cd-meta-label">Status</span>
                                    <span class="cd-meta-val">{{ ucfirst($course->status) }}</span>
                                </div>
                                @if($course->start_date || $course->end_date)
                                <div>
                                    <span class="cd-meta-label">Duration</span>
                                    <span class="cd-meta-val">
                                        @if($course->start_date && $course->end_date)
                                            {{ $course->start_date->format('M d') }} - {{ $course->end_date->format('M d, Y') }}
                                        @elseif($course->start_date)
                                            From {{ $course->start_date->format('M d, Y') }}
                                        @else
                                            Until {{ $course->end_date->format('M d, Y') }}
                                        @endif
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Schedule --}}
                        @if($course->primary_class_start)
                        <div class="cd-schedule-box">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="schedule-col-title" style="color:#6ab4ff;">
                                        <i class="bi bi-clock me-1"></i>Primary Class Time
                                    </div>
                                    <div class="schedule-time">
                                        {{ substr($course->primary_class_start,0,5) }}
                                        <span style="color:rgba(255,255,255,.4);font-size:1rem;font-weight:400;">to</span>
                                        {{ substr($course->primary_class_end,0,5) }}
                                    </div>
                                    @if($course->primary_class_days)
                                        @foreach($course->primary_class_days as $day)
                                            <span class="day-badge" style="background:rgba(31,143,255,0.2);color:#6ab4ff;">{{ ucfirst($day) }}</span>
                                        @endforeach
                                    @endif
                                    @if($course->primary_class_note)
                                        <p style="color:rgba(255,255,255,.45);font-size:.78rem;margin-top:6px;margin-bottom:0;">{{ $course->primary_class_note }}</p>
                                    @endif
                                </div>
                                @if($course->secondary_class_start)
                                <div class="col-md-6" style="border-left:1px solid rgba(255,255,255,.1);padding-left:1.5rem;">
                                    <div class="schedule-col-title" style="color:#fbbf24;">
                                        <i class="bi bi-clock-history me-1"></i>Backup Class Time
                                    </div>
                                    <div class="schedule-time">
                                        {{ substr($course->secondary_class_start,0,5) }}
                                        <span style="color:rgba(255,255,255,.4);font-size:1rem;font-weight:400;">to</span>
                                        {{ substr($course->secondary_class_end,0,5) }}
                                    </div>
                                    @if($course->secondary_class_days)
                                        @foreach($course->secondary_class_days as $day)
                                            <span class="day-badge" style="background:rgba(251,191,36,0.2);color:#fbbf24;">{{ ucfirst($day) }}</span>
                                        @endforeach
                                    @endif
                                    @if($course->secondary_class_note)
                                        <p style="color:rgba(255,255,255,.45);font-size:.78rem;margin-top:6px;margin-bottom:0;">{{ $course->secondary_class_note }}</p>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        @if($activeSession)
                        <a href="#live-class-panel" class="btn rounded-pill px-4 py-2 fw-bold shadow-sm"
                           style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;">
                            <span class="d-inline-block me-2" style="width:8px;height:8px;background:#fff;border-radius:50%;animation:livePulse 1.2s ease-in-out infinite;"></span>
                            Join Live Class
                        </a>
                        @else
                        <a href="#live-class-panel" class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm">
                            <i class="bi bi-camera-video-fill me-2 text-primary"></i>Start Class
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── STAT CARDS ───────────────────────────────── --}}
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="cd-stat-card">
                        <div class="cd-stat-icon" style="background:#eff6ff;">
                            <i class="bi bi-book-fill" style="color:#1F8FFF;"></i>
                        </div>
                        <div>
                            <div class="cd-stat-num">{{ $totalLessons }}</div>
                            <div class="cd-stat-lbl">Total Lessons</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="cd-stat-card">
                        <div class="cd-stat-icon" style="background:#f0fdf4;">
                            <i class="bi bi-people-fill" style="color:#10b981;"></i>
                        </div>
                        <div>
                            <div class="cd-stat-num">{{ $totalStudents }}</div>
                            <div class="cd-stat-lbl">Enrolled Students</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="cd-stat-card">
                        <div class="cd-stat-icon" style="background:#fffbeb;">
                            <i class="bi bi-star-fill" style="color:#f59e0b;"></i>
                        </div>
                        <div>
                            <div class="cd-stat-num">{{ number_format($avgRating,1) }}</div>
                            <div class="cd-stat-lbl">Average Rating</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="cd-stat-card">
                        <div class="cd-stat-icon" style="background:#f5f3ff;">
                            <i class="bi bi-graph-up-arrow" style="color:#8b5cf6;"></i>
                        </div>
                        <div>
                            <div class="cd-stat-num">{{ $avgProgress }}%</div>
                            <div class="cd-stat-lbl">Avg. Progress</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── TABS ─────────────────────────────────────── --}}
            <div class="cd-tabs" id="cdTabs">
                <button class="cd-tab-btn active" onclick="switchTab('syllabus',this)">
                    <i class="bi bi-list-ol"></i> Syllabus
                    <span class="badge rounded-pill ms-1" style="background:#e0f2fe;color:#0369a1;font-size:.7rem;">{{ $totalLessons }}</span>
                </button>
                <button class="cd-tab-btn" onclick="switchTab('students',this)">
                    <i class="bi bi-shield-check"></i> Students & Access
                    <span class="badge rounded-pill ms-1" style="background:#dcfce7;color:#15803d;font-size:.7rem;">{{ $totalStudents }}</span>
                </button>
                <button class="cd-tab-btn" onclick="switchTab('reviews',this)">
                    <i class="bi bi-star"></i> Reviews
                    <span class="badge rounded-pill ms-1" style="background:#fef9c3;color:#a16207;font-size:.7rem;">{{ $reviews->count() }}</span>
                </button>
                <button class="cd-tab-btn" onclick="switchTab('documents',this)">
                    <i class="bi bi-folder2-open"></i> Documents
                </button>
                <button class="cd-tab-btn" onclick="switchTab('history',this)">
                    <i class="bi bi-clock-history"></i> Session History
                    <span class="badge rounded-pill ms-1" style="background:#ede9fe;color:#6d28d9;font-size:.7rem;">{{ $pastSessions->count() }}</span>
                </button>
                <button class="cd-tab-btn" onclick="switchTab('attendance',this)">
                    <i class="bi bi-person-check"></i> Attendance
                    <span class="badge rounded-pill ms-1" style="background:#fef3c7;color:#92400e;font-size:.7rem;">{{ $totalStudents }}</span>
                </button>
                <button class="cd-tab-btn" onclick="switchTab('classnotes',this)">
                    <i class="bi bi-journal-text"></i> Class Notes
                    <span class="badge rounded-pill ms-1" style="background:#e0f2fe;color:#0369a1;font-size:.7rem;">{{ $classNotes->count() }}</span>
                </button>
                <button class="cd-tab-btn" onclick="switchTab('chat',this)">
                    <i class="bi bi-chat-heart"></i> Course Chat
                </button>
                <button class="cd-tab-btn" onclick="switchTab('curriculum',this)">
                    <i class="bi bi-pencil-square"></i> Curriculum
                    <span class="badge rounded-pill ms-1" style="background:#ede9fe;color:#6d28d9;font-size:.7rem;">{{ $totalLessons }}</span>
                </button>
            </div>

            {{-- SYLLABUS --}}
            <div class="cd-tab-pane active" id="tab-syllabus">

                {{-- Alerts --}}
                @if(session('session_success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('session_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                @if(session('recording_saved'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
                    <i class="bi bi-camera-video-fill me-2"></i>{{ session('recording_saved') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="row g-4">
                    {{-- ── Start Class Panel ── --}}
                    <div class="col-lg-4" id="live-class-panel">
                        <div class="cd-card h-100">
                            <div class="cd-card-header">
                                <h6 class="cd-card-title">
                                    <i class="bi bi-camera-video-fill me-2" style="color:#1F8FFF;"></i>Live Class
                                </h6>
                            </div>
                            <div class="cd-card-body">

                                @if($activeSession)
                                {{-- ACTIVE SESSION - Clean Simple Design --}}

                                {{-- LIVE Banner --}}
                                <div style="background: linear-gradient(135deg, #86efac, #bbf7d0); border: 2px solid #4ade80; border-radius: 12px; padding: 15px; text-align: center; margin-bottom: 15px;">
                                    <span style="display: inline-block; width: 12px; height: 12px; background: #22c55e; border-radius: 50%; margin-right: 8px; animation: pulse 2s infinite;"></span>
                                    <strong style="color: #166534; font-size: 16px;">Class is LIVE</strong>
                                </div>

                                {{-- Session Info Grid --}}
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                                    {{-- Started --}}
                                    <div style="background: #f8fafc; border-radius: 8px; padding: 12px; text-align: center;">
                                        <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">
                                            <i class="bi bi-play-fill" style="color: #22c55e;"></i> Started
                                        </div>
                                        <div style="font-weight: 600; color: #1e293b; font-size: 18px;">
                                            {{ $activeSession->started_at->format('H:i') }}
                                        </div>
                                        <div style="color: #94a3b8; font-size: 11px;">
                                            {{ $activeSession->started_at->format('M d, Y') }}
                                        </div>
                                    </div>

                                    {{-- Duration --}}
                                    <div style="background: #f8fafc; border-radius: 8px; padding: 12px; text-align: center;">
                                        <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">
                                            <i class="bi bi-clock" style="color: #3b82f6;"></i> Duration
                                        </div>
                                        <div style="font-weight: 600; color: #16a34a; font-size: 18px;" id="sessionDuration">
                                            {{ $activeSession->current_duration }}
                                        </div>
                                        <div style="color: #94a3b8; font-size: 11px;">Running</div>
                                    </div>
                                </div>

                                {{-- Participants --}}
                                <div style="background: #f0f9ff; border-radius: 8px; padding: 12px; text-align: center; margin-bottom: 15px; border: 1px solid #bae6fd;">
                                    <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">
                                        <i class="bi bi-people" style="color: #0ea5e9;"></i> Participants
                                    </div>
                                    <div style="font-weight: 700; color: #0369a1; font-size: 24px;">
                                        {{ $activeSession->participants_count ?? 0 }}
                                    </div>
                                    <div style="color: #64748b; font-size: 11px;">Auto-tracked by system</div>
                                </div>

                                {{-- Room Name --}}
                                @if($activeSession->room_name)
                                <div style="background: #f8fafc; border-radius: 8px; padding: 10px; text-align: center; margin-bottom: 15px;">
                                    <div style="color: #64748b; font-size: 11px; margin-bottom: 4px;">
                                        <i class="bi bi-door-open" style="color: #64748b;"></i> Room Name
                                    </div>
                                    <code style="font-size: 11px; background: #e2e8f0; padding: 4px 8px; border-radius: 4px;">{{ $activeSession->room_name }}</code>
                                </div>
                                @endif

                                {{-- Join Button --}}
                                <a href="{{ route('teacher.courses.sessions.join', $course->id) }}" 
                                   style="display: block; background: linear-gradient(135deg, #3b82f6, #6366f1); color: white; text-decoration: none; padding: 14px; border-radius: 10px; text-align: center; font-weight: 600; margin-bottom: 12px; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
                                    <i class="bi bi-box-arrow-up-right" style="margin-right: 8px;"></i>Join as Teacher (Full Controls)
                                </a>

                                {{-- Features --}}
                                <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 12px; margin-bottom: 15px;">
                                    <small style="color: #166534;">
                                        <i class="bi bi-shield-check" style="margin-right: 6px;"></i>
                                        Full presenter controls: screen share, video, audio, manage participants
                                    </small>
                                </div>

                                {{-- Auto-close Warning --}}
                                <div id="autoCloseWarning" style="display: none; background: #fef9c3; border: 1px solid #fde047; border-radius: 8px; padding: 12px; margin-bottom: 15px;">
                                    <small style="color: #854d0e;">
                                        <i class="bi bi-exclamation-triangle" style="margin-right: 6px;"></i>
                                        <span id="autoCloseMessage"></span>
                                    </small>
                                </div>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">

                                {{-- End Session Form --}}
                                <form method="POST" action="{{ route('teacher.courses.sessions.end', $course->id) }}">
                                    @csrf
                                    <input type="hidden" name="attendees_count" value="{{ $activeSession->participants_count ?? 0 }}">

                                    <div style="margin-bottom: 15px;">
                                        <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                                            <i class="bi bi-chat-left-text" style="margin-right: 6px; color: #94a3b8;"></i>Session Note (optional)
                                        </label>
                                        <textarea name="note" rows="2" 
                                                  style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; resize: vertical;"
                                                  placeholder="e.g. Covered chapters 3-4..."></textarea>
                                    </div>

                                    <button type="submit" 
                                            style="width: 100%; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; padding: 12px; border-radius: 10px; font-weight: 600; cursor: pointer;"
                                            onclick="return confirm('End the active class session?')">
                                        <i class="bi bi-stop-circle" style="margin-right: 8px;"></i>End Session
                                    </button>
                                </form>

                                @else
                                {{-- No Active Session State --}}
                                <div class="text-center mb-4">
                                    <div class="live-class-icon-wrapper">
                                        <i class="bi bi-camera-video-fill" style="font-size:2rem;color:#fff;"></i>
                                    </div>
                                    <h6 class="live-class-title">Start MiroTalk SFU Class</h6>
                                    <p class="live-class-description">
                                        Click below to create a secure meeting room. Students will be notified automatically.
                                    </p>
                                </div>

                                {{-- Filter out completed lessons --}}
                                @php
                                    $completedLessonIdsArray = $completedLessonIds ?? [];
                                    $availableLessons = $lessons->whereNotIn('id', $completedLessonIdsArray);
                                    $completedLessonsList = $lessons->whereIn('id', $completedLessonIdsArray);
                                @endphp

                                @if($availableLessons->count() > 0)
                                <form method="POST" action="{{ route('teacher.courses.sessions.start', $course->id) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="lesson_id" class="form-label" style="color: #fff; font-size: 0.9rem;">
                                            <i class="bi bi-book me-1"></i>Select Lesson for this Class
                                        </label>
                                        <select name="lesson_id" id="lesson_id" class="form-select" required style="background: #fff; color: #333; border: 1px solid rgba(255,255,255,0.5);">
                                            <option value="" disabled selected style="color: #666;">-- Choose a lesson --</option>
                                            @foreach($availableLessons as $lesson)
                                                <option value="{{ $lesson->id }}" style="color: #333; background: #fff;">
                                                    {{ $lesson->order }}. {{ $lesson->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-white-50">Students will see which lesson is being taught.</small>
                                    </div>
                                    <button type="submit" class="btn live-class-btn-start">
                                        <i class="bi bi-camera-video-fill me-2"></i>Start MiroTalk SFU Class
                                    </button>
                                </form>
                                @else
                                <div class="alert alert-success" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: #fff;">
                                    <i class="bi bi-check-circle-fill me-2"></i>All lessons have been taught!
                                </div>
                                @endif

                                <div class="live-class-info-box mt-3">
                                    <small>
                                        <i class="bi bi-info-circle me-1"></i>
                                        A unique secure room will be created. You will have full presenter controls.
                                    </small>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>

                    {{-- ── Lessons List ── --}}
                    <div class="col-lg-8">
                        <div class="cd-card">
                            <div class="cd-card-header">
                                <h6 class="cd-card-title">
                                    <i class="bi bi-list-ol me-2 text-primary"></i>Lessons ({{ $totalLessons }})
                                    @if(isset($completedLessonsCount) && $completedLessonsCount > 0)
                                    <span class="ms-2 badge bg-success">{{ $completedLessonsCount }} Completed</span>
                                    @endif
                                </h6>
                            </div>
                            <div class="cd-card-body">
                                {{-- Progress Bar --}}
                                @if(isset($progressPercent))
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Course Progress</small>
                                        <small class="fw-bold">{{ $progressPercent }}%</small>
                                    </div>
                                    <div class="progress" style="height:8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                </div>
                                @endif
                                @forelse($course->lessons as $lesson)
                                @php
                                    $isCompleted = isset($completedLessonIds) && in_array($lesson->id, $completedLessonIds);
                                @endphp
                                <div class="lesson-row" style="flex-wrap:wrap; {{ $isCompleted ? 'background:#f0fdf4;' : '' }}">
                                    <div class="lesson-num" style="{{ $isCompleted ? 'background:#22c55e;color:#fff;' : '' }}">
                                        @if($isCompleted)
                                            <i class="bi bi-check-lg"></i>
                                        @else
                                            {{ $lesson->order ?: $loop->iteration }}
                                        @endif
                                    </div>
                                    <div class="flex-grow-1" style="min-width:0;">
                                        <div class="lesson-title {{ $isCompleted ? 'text-success' : '' }}">
                                            {{ $lesson->title }}
                                            @if($isCompleted)
                                                <span class="badge bg-success ms-2" style="font-size:0.65rem;">COMPLETED</span>
                                            @endif
                                        </div>
                                        @if($lesson->description)
                                            <div class="lesson-desc">{{ Str::limit($lesson->description,80) }}</div>
                                        @endif
                                        {{-- Recording link if exists --}}
                                        @if($lesson->video_url)
                                        <div class="mt-2 d-flex align-items-center gap-2">
                                            <span style="font-size:.75rem;color:#10b981;font-weight:600;">
                                                <i class="bi bi-camera-video-fill me-1"></i>Recording available
                                            </span>
                                            <a href="{{ $lesson->video_url }}" target="_blank"
                                               class="badge rounded-pill px-2" style="background:#f0fdf4;color:#10b981;font-size:.7rem;text-decoration:none;">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>View
                                            </a>
                                            <button class="badge rounded-pill px-2 border-0"
                                                    style="background:#fff7ed;color:#ea580c;font-size:.7rem;cursor:pointer;"
                                                    onclick="toggleRecordingForm({{ $lesson->id }})">
                                                <i class="bi bi-pencil me-1"></i>Update
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        @if($lesson->duration_minutes)
                                            <div class="lesson-dur"><i class="bi bi-clock me-1"></i>{{ $lesson->duration_minutes }} min</div>
                                        @endif
                                        @if(!$lesson->video_url)
                                        <button class="btn btn-sm rounded-pill px-3"
                                                style="background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;font-size:.78rem;font-weight:600;"
                                                onclick="toggleRecordingForm({{ $lesson->id }})">
                                            <i class="bi bi-camera-video me-1"></i>Add Recording
                                        </button>
                                        @endif
                                    </div>

                                    {{-- Recording form (hidden by default) --}}
                                    <div class="recording-form w-100 mt-3" id="rec-form-{{ $lesson->id }}" style="display:none;">
                                        <form method="POST"
                                              action="{{ route('teacher.courses.lessons.recording', [$course->id, $lesson->id]) }}"
                                              class="d-flex gap-2 align-items-start">
                                            @csrf
                                            <div class="flex-grow-1">
                                                <input type="url" name="video_url" class="form-control form-control-sm rounded-3"
                                                       placeholder="YouTube or Google Drive link..."
                                                       value="{{ $lesson->video_url }}" required>
                                            </div>
                                            <button type="submit" class="btn btn-sm rounded-3 fw-bold px-3"
                                                    style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;white-space:nowrap;">
                                                <i class="bi bi-save me-1"></i>Save
                                            </button>
                                            <button type="button" class="btn btn-sm rounded-3 px-3"
                                                    style="background:#f1f5f9;color:#64748b;border:none;"
                                                    onclick="toggleRecordingForm({{ $lesson->id }})">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @empty
                                <div class="cd-empty">
                                    <i class="bi bi-journal-x"></i>
                                    <p>No lessons have been added yet.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STUDENTS & ACCESS --}}
            <div class="cd-tab-pane" id="tab-students">
                @if(session('student_action'))
                <div class="alert alert-info alert-dismissible fade show mb-4 rounded-3" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>{{ session('student_action') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                {{-- Header with search --}}
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="color:#1e293b;"><i class="bi bi-shield-check me-2" style="color:#10b981;"></i>Students & Access Control</h5>
                        <p class="text-muted small mb-0">Manage who can access this course. Ban students to revoke their access immediately.</p>
                    </div>
                    @if($totalStudents > 0)
                    <input type="text" id="studentSearch" class="form-control rounded-pill" style="max-width:240px;" placeholder="&#128269; Search students...">
                    @endif
                </div>

                @php
                    $bannedCount  = $enrollments->where('status','banned')->count();
                    $activeCount  = $enrollments->where('status','!=','banned')->count();
                @endphp

                {{-- Quick stats --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="att-stat-card" style="border-left:4px solid #10b981;">
                            <div class="att-stat-num" style="color:#10b981;">{{ $activeCount }}</div>
                            <div class="att-stat-lbl">Active Students</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="att-stat-card" style="border-left:4px solid #ef4444;">
                            <div class="att-stat-num" style="color:#ef4444;">{{ $bannedCount }}</div>
                            <div class="att-stat-lbl">Banned</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="att-stat-card" style="border-left:4px solid #1F8FFF;">
                            <div class="att-stat-num" style="color:#1F8FFF;">{{ $totalStudents }}</div>
                            <div class="att-stat-lbl">Total Enrolled</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="att-stat-card" style="border-left:4px solid #f59e0b;">
                            <div class="att-stat-num" style="color:#f59e0b;">{{ $totalStudents > 0 ? round(($activeCount/$totalStudents)*100) : 0 }}%</div>
                            <div class="att-stat-lbl">Access Rate</div>
                        </div>
                    </div>
                </div>

                {{-- Students grid --}}
                @if($enrollments->count() > 0)
                <div class="row g-3" id="studentsGrid">
                    @foreach($enrollments as $en)
                    @php
                        $enAvatar  = $avatarUrl($en->avatar, $en->name);
                        $prog      = (int)($en->progress_percentage ?? 0);
                        $isBanned  = ($en->status === 'banned');
                    @endphp
                    <div class="col-md-6 col-xl-4 student-item">
                        <div class="cd-card h-100" style="{{ $isBanned ? 'border:1.5px solid #fecaca;background:#fff5f5;' : '' }}">
                            <div class="cd-card-body">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div style="position:relative;flex-shrink:0;">
                                        <img src="{{ $enAvatar }}" alt="{{ $en->name }}"
                                             style="width:52px;height:52px;border-radius:50%;object-fit:cover;{{ $isBanned ? 'filter:grayscale(1);opacity:.6;' : '' }}">
                                        @if($isBanned)
                                        <span style="position:absolute;bottom:-2px;right:-2px;background:#ef4444;color:#fff;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;font-size:.65rem;border:2px solid #fff;">
                                            <i class="bi bi-slash-lg"></i>
                                        </span>
                                        @else
                                        <span style="position:absolute;bottom:-2px;right:-2px;background:#10b981;color:#fff;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;font-size:.65rem;border:2px solid #fff;">
                                            <i class="bi bi-check-lg"></i>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-semibold text-truncate" style="color:#1e293b;font-size:.95rem;">{{ $en->name }}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            @if($isBanned)
                                            <span class="badge rounded-pill px-2 py-1" style="background:#fef2f2;color:#ef4444;font-size:.7rem;"><i class="bi bi-slash-circle me-1"></i>Banned</span>
                                            @else
                                            <span class="badge rounded-pill px-2 py-1" style="background:#f0fdf4;color:#16a34a;font-size:.7rem;"><i class="bi bi-check-circle me-1"></i>{{ ucfirst($en->status ?? 'active') }}</span>
                                            @endif
                                            <span class="text-muted" style="font-size:.72rem;">Joined {{ \Carbon\Carbon::parse($en->enrolled_at)->format('M d') }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Progress bar --}}
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span style="font-size:.75rem;color:#64748b;">Progress</span>
                                        <span style="font-size:.75rem;font-weight:600;color:{{ $isBanned ? '#ef4444' : '#1e293b' }};">{{ $prog }}%</span>
                                    </div>
                                    <div style="height:6px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
                                        <div style="height:100%;width:{{ $prog }}%;border-radius:99px;background:{{ $isBanned ? '#fca5a5' : ($prog>=80 ? '#10b981' : ($prog>=40 ? '#f59e0b' : '#1F8FFF')) }};transition:width .4s;"></div>
                                    </div>
                                </div>

                                {{-- Action button --}}
                                @if($isBanned)
                                <form method="POST" action="{{ route('teacher.courses.students.unban', [$course->id, $en->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn w-100 fw-semibold rounded-3 py-2"
                                            style="background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0;font-size:.82rem;"
                                            onclick="return confirm('Reinstate {{ addslashes($en->name) }} to this course?')">
                                        <i class="bi bi-person-check-fill me-2"></i>Reinstate Access
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('teacher.courses.students.ban', [$course->id, $en->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn w-100 fw-semibold rounded-3 py-2"
                                            style="background:#fff5f5;color:#ef4444;border:1.5px solid #fecaca;font-size:.82rem;"
                                            onclick="return confirm('Ban {{ addslashes($en->name) }} from this course? They will lose access immediately.')">
                                        <i class="bi bi-slash-circle me-2"></i>Ban from Course
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="cd-card">
                    <div class="cd-card-body">
                        <div class="cd-empty">
                            <i class="bi bi-person-x"></i>
                            <p>No students enrolled in this course yet.</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- REVIEWS --}}
            <div class="cd-tab-pane" id="tab-reviews">
                <div class="cd-card">
                    <div class="cd-card-header">
                        <h6 class="cd-card-title"><i class="bi bi-star-fill me-2 text-warning"></i>Reviews ({{ $reviews->count() }})</h6>
                        @if($reviews->count() > 0)
                            <span class="badge bg-warning text-dark fs-6 px-3">
                                {{ number_format($avgRating,1) }} / 5.0
                            </span>
                        @endif
                    </div>
                    <div class="cd-card-body">
                        @forelse($reviews as $revIndex => $rev)
                        @php
                            $stars = round($rev->rating);
                        @endphp
                        <div class="review-card">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#94a3b8,#64748b);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;flex-shrink:0;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold" style="color:#1e293b;">Student #{{ $revIndex + 1 }}</div>
                                    <div class="star-row">
                                        @for($i=1;$i<=5;$i++)
                                            <i class="bi bi-star{{ $i<=$stars ? '-fill text-warning' : ' text-muted' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($rev->created_at)->diffForHumans() }}</small>
                                    @if($rev->likes_count > 0 || $rev->dislikes_count > 0)
                                    <div class="mt-1" style="font-size:.75rem;">
                                        <span class="text-success"><i class="bi bi-hand-thumbs-up-fill"></i> {{ $rev->likes_count }}</span>
                                        <span class="text-danger ms-2"><i class="bi bi-hand-thumbs-down-fill"></i> {{ $rev->dislikes_count }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @if($rev->comment)
                                <p style="color:#475569;font-size:.9rem;margin:0;">{{ $rev->comment }}</p>
                            @endif
                        </div>
                        @empty
                        <div class="cd-empty">
                            <i class="bi bi-chat-left-dots"></i>
                            <p>No reviews yet for this course.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- DOCUMENTS --}}
            <div class="cd-tab-pane" id="tab-documents">

                {{-- Success / Error Alert --}}
                @if(session('doc_success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('doc_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                @if($errors->has('file') || $errors->has('title'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ $errors->first('file') ?: $errors->first('title') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="row g-4">
                    {{-- Upload Form --}}
                    <div class="col-lg-4">
                        <div class="cd-card h-100">
                            <div class="cd-card-header">
                                <h6 class="cd-card-title"><i class="bi bi-cloud-arrow-up me-2 text-primary"></i>Upload Document</h6>
                            </div>
                            <div class="cd-card-body">
                                <form method="POST"
                                      action="{{ route('teacher.courses.documents.upload', $course->id) }}"
                                      enctype="multipart/form-data"
                                      id="uploadDocForm">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">Document Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control rounded-3"
                                               placeholder="e.g. Week 1 – Lecture Notes"
                                               value="{{ old('title') }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">Related Lesson</label>
                                        <select name="lesson_id" class="form-select rounded-3">
                                            <option value="">— General (not lesson-specific) —</option>
                                            @foreach($course->lessons as $lesson)
                                                <option value="{{ $lesson->id }}">
                                                    #{{ $lesson->order ?: $loop->iteration }} – {{ $lesson->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">Description</label>
                                        <textarea name="description" rows="3" class="form-control rounded-3"
                                                  placeholder="Short description about this document (optional)"
                                                  maxlength="500">{{ old('description') }}</textarea>
                                        <div class="form-text">Max 500 characters</div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">PDF File <span class="text-danger">*</span></label>
                                        <div class="doc-upload-zone" id="dropZone" onclick="document.getElementById('docFileInput').click()">
                                            <i class="bi bi-file-earmark-pdf-fill" style="font-size:2rem;color:#ef4444;"></i>
                                            <p class="mb-0 mt-2 fw-semibold" style="color:#475569;font-size:.9rem;">Click to choose PDF</p>
                                            <p class="mb-0" style="color:#94a3b8;font-size:.78rem;">Only PDF • Max 3 MB</p>
                                            <div id="fileChosen" class="mt-2" style="display:none;">
                                                <span class="badge rounded-pill px-3 py-2" style="background:#dcfce7;color:#15803d;font-size:.78rem;">
                                                    <i class="bi bi-check-circle me-1"></i><span id="fileChosenName"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <input type="file" id="docFileInput" name="file" accept=".pdf" class="d-none"
                                               onchange="handleFileChosen(this)">
                                    </div>

                                    <button type="submit" class="btn w-100 rounded-3 fw-bold py-2"
                                            style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;">
                                        <i class="bi bi-cloud-upload me-2"></i>Upload Document
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Document List --}}
                    <div class="col-lg-8">
                        <div class="cd-card">
                            <div class="cd-card-header">
                                <h6 class="cd-card-title">
                                    <i class="bi bi-folder2-open me-2 text-primary"></i>Uploaded Materials
                                </h6>
                                <span class="badge rounded-pill px-3" style="background:#eff6ff;color:#1F8FFF;">
                                    {{ $documents->count() }} file{{ $documents->count() !== 1 ? 's' : '' }}
                                </span>
                            </div>
                            <div class="cd-card-body">
                                @forelse($documents as $doc)
                                <div class="doc-row">
                                    <div class="doc-icon">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="doc-title">{{ $doc->title }}</div>
                                        @if($doc->lesson)
                                            <span class="doc-lesson-badge">
                                                <i class="bi bi-bookmark-fill me-1"></i>
                                                {{ $doc->lesson->title }}
                                            </span>
                                        @else
                                            <span class="doc-lesson-badge" style="background:#f1f5f9;color:#94a3b8;">
                                                <i class="bi bi-collection me-1"></i>General
                                            </span>
                                        @endif
                                        @if($doc->description)
                                            <p class="doc-desc">{{ $doc->description }}</p>
                                        @endif
                                        <div class="doc-meta">
                                            <span><i class="bi bi-hdd me-1"></i>{{ $doc->file_size_formatted }}</span>
                                            <span><i class="bi bi-clock me-1"></i>{{ $doc->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="doc-actions">
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                           class="btn btn-sm rounded-pill px-3"
                                           style="background:#eff6ff;color:#1F8FFF;border:none;font-size:.8rem;font-weight:600;">
                                            <i class="bi bi-download me-1"></i>Download
                                        </a>
                                        <form method="POST"
                                              action="{{ route('teacher.courses.documents.delete', [$course->id, $doc->id]) }}"
                                              onsubmit="return confirm('Delete this document?')"
                                              style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm rounded-pill px-3"
                                                    style="background:#fef2f2;color:#ef4444;border:none;font-size:.8rem;font-weight:600;">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @empty
                                <div class="cd-empty">
                                    <i class="bi bi-folder-x"></i>
                                    <p>No documents uploaded yet. Use the form to add your first file.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SESSION HISTORY --}}
            <div class="cd-tab-pane" id="tab-history">
                <div class="cd-card">
                    <div class="cd-card-header">
                        <h6 class="cd-card-title">
                            <i class="bi bi-clock-history me-2" style="color:#8b5cf6;"></i>Past Class Sessions ({{ $pastSessions->count() }})
                        </h6>
                    </div>
                    <div class="cd-card-body">
                        @forelse($pastSessions as $s)
                        @php
                            $hasDocs = $documents->where('created_at', '>=', $s->started_at)
                                                 ->where('created_at', '<=', $s->ended_at ?? $s->started_at->addHours(4))
                                                 ->count();
                        @endphp
                        <div class="shistory-card">
                            <div class="shistory-index">#{{ $loop->iteration }}</div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center flex-wrap gap-3 mb-2">
                                    <span class="shistory-date">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $s->started_at->format('D, M d Y') }}
                                    </span>
                                    <span class="shistory-badge-dur">
                                        <i class="bi bi-hourglass-split me-1"></i>{{ $s->duration }}
                                    </span>
                                </div>
                                {{-- Start and End Times --}}
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                    <span class="badge bg-success rounded-pill px-3">
                                        <i class="bi bi-play-fill me-1"></i>Start: {{ $s->started_at->format('H:i') }}
                                    </span>
                                    @if($s->ended_at)
                                    <span class="badge bg-danger rounded-pill px-3">
                                        <i class="bi bi-stop-fill me-1"></i>End: {{ $s->ended_at->format('H:i') }}
                                    </span>
                                    @else
                                    <span class="badge bg-warning text-dark rounded-pill px-3">
                                        <i class="bi bi-broadcast me-1"></i>In Progress
                                    </span>
                                    @endif
                                </div>

                                <div class="d-flex flex-wrap gap-3">
                                    {{-- Meet Link --}}
                                    <div class="shistory-meta-item">
                                        <span class="shistory-meta-label">Meet Link</span>
                                        <a href="{{ $s->meet_link }}" target="_blank" class="shistory-link">
                                            <i class="bi bi-camera-video me-1"></i>{{ Str::limit($s->meet_link, 40) }}
                                        </a>
                                    </div>
                                    {{-- Attendees --}}
                                    <div class="shistory-meta-item">
                                        <span class="shistory-meta-label">Attendees</span>
                                        <span class="shistory-val">
                                            <i class="bi bi-people-fill me-1 text-success"></i>
                                            {{ $s->attendees_count }}
                                        </span>
                                    </div>
                                    {{-- Documents --}}
                                    <div class="shistory-meta-item">
                                        <span class="shistory-meta-label">Documents</span>
                                        @if($hasDocs > 0)
                                            <span class="shistory-val" style="color:#1F8FFF;">
                                                <i class="bi bi-paperclip me-1"></i>{{ $hasDocs }} file{{ $hasDocs>1?'s':'' }}
                                            </span>
                                        @else
                                            <span class="shistory-val" style="color:#94a3b8;">None</span>
                                        @endif
                                    </div>
                                </div>

                                @if($s->note)
                                <div class="shistory-note mt-2">
                                    <i class="bi bi-chat-left-text me-1" style="color:#8b5cf6;"></i>{{ $s->note }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="cd-empty">
                            <i class="bi bi-journal-x"></i>
                            <p>No past sessions yet. Start and end a class to see history here.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ATTENDANCE --}}
            <div class="cd-tab-pane" id="tab-attendance">

                @if(session('att_success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('att_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($enrollments->count() === 0)
                <div class="cd-card"><div class="cd-card-body"><div class="cd-empty">
                    <i class="bi bi-person-x"></i><p>No students enrolled yet.</p>
                </div></div></div>
                @else

                @php
                    $totalSessions = $pastSessions->count();
                    $overallRate   = $attendanceSummary->count() > 0 ? round($attendanceSummary->avg('rate')) : 0;
                    $riskCount     = $attendanceSummary->where('rate', '<', 60)->count();
                    $goodCount     = $attendanceSummary->where('rate', '>=', 80)->count();
                @endphp

                {{-- ── Hero Stats Row ── --}}
                <div class="att2-stats-row mb-4">
                    <div class="att2-stat-item" style="--c:#1F8FFF;">
                        <div class="att2-stat-icon" style="background:#eff6ff;"><i class="bi bi-people-fill" style="color:#1F8FFF;"></i></div>
                        <div><div class="att2-stat-num">{{ $totalStudents }}</div><div class="att2-stat-lbl">Students</div></div>
                    </div>
                    <div class="att2-stat-item" style="--c:#8b5cf6;">
                        <div class="att2-stat-icon" style="background:#ede9fe;"><i class="bi bi-calendar-check" style="color:#8b5cf6;"></i></div>
                        <div><div class="att2-stat-num">{{ $totalSessions }}</div><div class="att2-stat-lbl">Sessions</div></div>
                    </div>
                    <div class="att2-stat-item" style="--c:#10b981;">
                        <div class="att2-stat-icon" style="background:#ecfdf5;"><i class="bi bi-graph-up-arrow" style="color:#10b981;"></i></div>
                        <div><div class="att2-stat-num">{{ $overallRate }}%</div><div class="att2-stat-lbl">Avg Attendance</div></div>
                    </div>
                    <div class="att2-stat-item" style="--c:#10b981;">
                        <div class="att2-stat-icon" style="background:#ecfdf5;"><i class="bi bi-award" style="color:#10b981;"></i></div>
                        <div><div class="att2-stat-num">{{ $goodCount }}</div><div class="att2-stat-lbl">Good (≥80%)</div></div>
                    </div>
                    <div class="att2-stat-item" style="--c:#ef4444;">
                        <div class="att2-stat-icon" style="background:#fef2f2;"><i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;"></i></div>
                        <div><div class="att2-stat-num">{{ $riskCount }}</div><div class="att2-stat-lbl">At Risk</div></div>
                    </div>
                </div>

                {{-- ── Per-Student Cards ── --}}
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h6 class="fw-bold mb-0" style="color:#1e293b;"><i class="bi bi-people-fill me-2" style="color:#1F8FFF;"></i>Attendance Overview</h6>
                    <a href="{{ route('teacher.courses.export-attendance-pdf', $course->id) }}"
                       class="btn rounded-3 fw-bold px-4 py-2"
                       style="background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;border:none;font-size:.82rem;">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i>Export PDF
                    </a>
                </div>

                <div class="row g-3 mb-4">
                    @foreach($attendanceSummary as $a)
                    @php
                        $avatarSrc = $a->avatar
                            ? (str_starts_with($a->avatar,'http') ? $a->avatar : asset('storage/'.$a->avatar))
                            : 'https://ui-avatars.com/api/?name='.urlencode($a->name).'&background=1F8FFF&color=fff&size=60';
                        $rateColor  = $a->rate >= 80 ? '#10b981' : ($a->rate >= 60 ? '#f59e0b' : '#ef4444');
                        $rateBg     = $a->rate >= 80 ? '#ecfdf5' : ($a->rate >= 60 ? '#fffbeb' : '#fef2f2');
                        $statusText = $totalSessions === 0 ? 'No sessions' : ($a->rate >= 80 ? 'Good' : ($a->rate >= 60 ? 'Warning' : 'At Risk'));
                        $statusIcon = $totalSessions === 0 ? 'bi-dash-circle' : ($a->rate >= 80 ? 'bi-check-circle-fill' : ($a->rate >= 60 ? 'bi-exclamation-circle-fill' : 'bi-x-circle-fill'));
                    @endphp
                    <div class="col-md-6 col-xl-4">
                        <div class="att2-student-card">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <img src="{{ $avatarSrc }}" class="att2-avatar" alt="">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-truncate" style="color:#1e293b;font-size:.92rem;">{{ $a->name }}</div>
                                    <span class="att2-status-badge" style="background:{{ $rateBg }};color:{{ $rateColor }};">
                                        <i class="bi {{ $statusIcon }} me-1"></i>{{ $statusText }}
                                    </span>
                                </div>
                                <div class="att2-rate-circle" style="--rate-color:{{ $rateColor }};">
                                    <span style="color:{{ $rateColor }};font-weight:700;font-size:.88rem;">{{ $a->rate }}%</span>
                                </div>
                            </div>

                            {{-- Progress bar --}}
                            <div class="att2-prog-bar mb-3">
                                <div class="att2-prog-fill" style="width:{{ $a->rate }}%;background:{{ $rateColor }};"></div>
                            </div>

                            {{-- P / L / A counters --}}
                            <div class="att2-counters">
                                <div class="att2-counter" style="background:#ecfdf5;border:1px solid #bbf7d0;">
                                    <span style="font-size:1.1rem;font-weight:700;color:#15803d;">{{ $a->present }}</span>
                                    <span style="font-size:.7rem;color:#6b7280;">Present</span>
                                </div>
                                <div class="att2-counter" style="background:#fffbeb;border:1px solid #fde68a;">
                                    <span style="font-size:1.1rem;font-weight:700;color:#92400e;">{{ $a->late }}</span>
                                    <span style="font-size:.7rem;color:#6b7280;">Late</span>
                                </div>
                                <div class="att2-counter" style="background:#fef2f2;border:1px solid #fecaca;">
                                    <span style="font-size:1.1rem;font-weight:700;color:#dc2626;">{{ $a->absent }}</span>
                                    <span style="font-size:.7rem;color:#6b7280;">Absent</span>
                                </div>
                                <div class="att2-counter" style="background:#f8fafc;border:1px solid #e2e8f0;">
                                    <span style="font-size:1.1rem;font-weight:700;color:#475569;">{{ $a->total }}</span>
                                    <span style="font-size:.7rem;color:#6b7280;">Total</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- ── Per-Session Attendance Marking ── --}}
                @if($pastSessions->count())
                <div class="cd-card">
                    <div class="cd-card-header">
                        <h6 class="cd-card-title"><i class="bi bi-clipboard2-check me-2" style="color:#8b5cf6;"></i>Mark Attendance</h6>
                        {{-- Session dropdown selector --}}
                        <div class="d-flex align-items-center gap-2">
                            <label class="text-muted small mb-0 fw-semibold">Session:</label>
                            <select id="sessionDropdown" class="form-select form-select-sm rounded-3" style="max-width:220px;font-size:.82rem;"
                                    onchange="showSessionPane(this.value)">
                                @foreach($pastSessions as $si => $s)
                                <option value="{{ $s->id }}" {{ $si===0 ? 'selected' : '' }}>
                                    {{ $s->started_at->format('D, M d Y') }} · {{ $s->started_at->format('H:i') }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="cd-card-body p-0">
                        {{-- Per-session forms --}}
                        @foreach($pastSessions as $si => $s)
                        @php
                            $existing = \DB::table('session_attendances')
                                ->where('session_id', $s->id)
                                ->pluck('status', 'user_id');
                        @endphp
                        <div class="att-session-pane {{ $si === 0 ? 'active' : '' }}" id="att-session-{{ $s->id }}">
                            <form method="POST" action="{{ route('teacher.courses.sessions.attendance', [$course->id, $s->id]) }}" class="att-ajax-form">
                                @csrf

                                {{-- Session info bar --}}
                                <div style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:1px solid #e2e8f0;padding:14px 20px;" class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <span class="fw-bold" style="color:#1e293b;font-size:.9rem;">
                                            <i class="bi bi-calendar3 me-2" style="color:#8b5cf6;"></i>{{ $s->started_at->format('l, M d Y') }}
                                        </span>
                                        <span class="badge rounded-pill px-3" style="background:#ede9fe;color:#6d28d9;font-size:.73rem;">
                                            <i class="bi bi-hourglass-split me-1"></i>{{ $s->duration }}
                                        </span>
                                        <span style="color:#94a3b8;font-size:.82rem;">
                                            {{ $s->started_at->format('H:i') }} – {{ $s->ended_at?->format('H:i') ?? '?' }}
                                        </span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" onclick="markAll({{ $s->id }},'present')"
                                                class="btn btn-sm rounded-3 px-3 fw-semibold"
                                                style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;font-size:.78rem;">
                                            <i class="bi bi-check-all me-1"></i>All Present
                                        </button>
                                        <button type="button" onclick="markAll({{ $s->id }},'absent')"
                                                class="btn btn-sm rounded-3 px-3 fw-semibold"
                                                style="background:#fef2f2;color:#ef4444;border:1px solid #fecaca;font-size:.78rem;">
                                            <i class="bi bi-x-lg me-1"></i>All Absent
                                        </button>
                                    </div>
                                </div>

                                {{-- Student rows --}}
                                <div style="padding:8px 0;">
                                @foreach($enrollments as $enIdx => $en)
                                @php
                                    $enAvatar = $en->avatar
                                        ? (str_starts_with($en->avatar,'http') ? $en->avatar : asset('storage/'.$en->avatar))
                                        : 'https://ui-avatars.com/api/?name='.urlencode($en->name).'&background=1F8FFF&color=fff&size=60';
                                    $curStatus = $existing[$en->id] ?? 'present';
                                @endphp
                                <div class="att-mark-row att-mark-row-v2 {{ $enIdx % 2 === 1 ? 'att-row-alt' : '' }}">
                                    <div class="d-flex align-items-center gap-3" style="min-width:0;flex:1;">
                                        <img src="{{ $enAvatar }}" class="att-avatar" alt="">
                                        <div style="min-width:0;">
                                            <div class="fw-semibold text-truncate" style="font-size:.88rem;color:#1e293b;">{{ $en->name }}</div>
                                            <div style="font-size:.72rem;color:#94a3b8;">
                                                @php
                                                    $enr = $attendanceSummary->firstWhere('user_id', $en->id);
                                                @endphp
                                                @if($enr)
                                                    {{ $enr->present }}P · {{ $enr->late }}L · {{ $enr->absent }}A
                                                    <span class="ms-1 fw-semibold" style="color:{{ $enr->rate>=80?'#10b981':($enr->rate>=60?'#f59e0b':'#ef4444') }};">
                                                        ({{ $enr->rate }}%)
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="att-radio-group-v2" id="rg-{{ $s->id }}-{{ $en->id }}">
                                        <label class="att-pill-lbl {{ $curStatus==='present' ? 'att-pill-present-on' : 'att-pill-off' }}">
                                            <input type="radio" name="attendance[{{ $en->id }}]" value="present"
                                                   {{ $curStatus==='present'?'checked':'' }} class="d-none att-radio-inp"
                                                   onchange="updatePillGroup(this)">
                                            <i class="bi bi-check-circle-fill me-1"></i>Present
                                        </label>
                                        <label class="att-pill-lbl {{ $curStatus==='late' ? 'att-pill-late-on' : 'att-pill-off' }}">
                                            <input type="radio" name="attendance[{{ $en->id }}]" value="late"
                                                   {{ $curStatus==='late'?'checked':'' }} class="d-none att-radio-inp"
                                                   onchange="updatePillGroup(this)">
                                            <i class="bi bi-clock-fill me-1"></i>Late
                                        </label>
                                        <label class="att-pill-lbl {{ $curStatus==='absent' ? 'att-pill-absent-on' : 'att-pill-off' }}">
                                            <input type="radio" name="attendance[{{ $en->id }}]" value="absent"
                                                   {{ $curStatus==='absent'?'checked':'' }} class="d-none att-radio-inp"
                                                   onchange="updatePillGroup(this)">
                                            <i class="bi bi-x-circle-fill me-1"></i>Absent
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                                </div>

                                <div style="padding:16px 20px;border-top:1px solid #f1f5f9;" class="text-end">
                                    <button type="submit" class="btn rounded-3 fw-bold px-5 py-2"
                                            style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;">
                                        <i class="bi bi-save me-2"></i>Save Attendance
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @endif
            </div>

            {{-- CLASS NOTES --}}
            <div class="cd-tab-pane" id="tab-classnotes">

                @if(session('note_success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('note_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="row g-4">
                    {{-- Add Note Form --}}
                    <div class="col-lg-4">
                        <div class="cd-card h-100">
                            <div class="cd-card-header">
                                <h6 class="cd-card-title"><i class="bi bi-plus-circle-fill me-2 text-primary"></i>Add Class Note</h6>
                            </div>
                            <div class="cd-card-body">
                                <form method="POST" action="{{ route('teacher.courses.notes.store', $course->id) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">Date <span class="text-danger">*</span></label>
                                        <input type="date" name="class_date" class="form-control rounded-3"
                                               value="{{ old('class_date', date('Y-m-d')) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">Title</label>
                                        <input type="text" name="title" class="form-control rounded-3"
                                               placeholder="e.g. Session 5 – Loops & Functions"
                                               value="{{ old('title') }}" maxlength="255">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">What was covered today? <span class="text-danger">*</span></label>
                                        <textarea name="content" rows="6" class="form-control rounded-3"
                                                  placeholder="Write a summary of what students learned in today's class..."
                                                  maxlength="3000" required>{{ old('content') }}</textarea>
                                        <div class="form-text">Max 3000 characters</div>
                                    </div>
                                    <button type="submit" class="btn w-100 rounded-3 fw-bold py-2"
                                            style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;">
                                        <i class="bi bi-save me-2"></i>Save Note
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Notes List --}}
                    <div class="col-lg-8">
                        <div class="cd-card">
                            <div class="cd-card-header">
                                <h6 class="cd-card-title">
                                    <i class="bi bi-journal-text me-2" style="color:#1F8FFF;"></i>All Class Notes
                                </h6>
                                <span class="badge rounded-pill px-3" style="background:#eff6ff;color:#1F8FFF;">
                                    {{ $classNotes->count() }} note{{ $classNotes->count() !== 1 ? 's' : '' }}
                                </span>
                            </div>
                            <div class="cd-card-body">
                                @forelse($classNotes as $note)
                                <div class="cn-note-card mb-3" id="note-card-{{ $note->id }}">
                                    {{-- View mode --}}
                                    <div class="cn-view" id="cn-view-{{ $note->id }}">
                                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                            <div>
                                                <span class="badge rounded-pill px-3 me-2" style="background:#e0f2fe;color:#0369a1;font-size:.75rem;">
                                                    <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($note->class_date)->format('D, M d Y') }}
                                                </span>
                                                @if($note->title)
                                                    <span class="fw-bold" style="color:#1e293b;font-size:.95rem;">{{ $note->title }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex gap-2 flex-shrink-0">
                                                <button class="btn btn-sm rounded-pill px-3"
                                                        style="background:#eff6ff;color:#1F8FFF;border:none;font-size:.78rem;font-weight:600;"
                                                        onclick="toggleNoteEdit({{ $note->id }})">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </button>
                                                <form method="POST"
                                                      action="{{ route('teacher.courses.notes.delete', [$course->id, $note->id]) }}"
                                                      onsubmit="return confirm('Delete this note?')"
                                                      style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm rounded-pill px-3"
                                                            style="background:#fef2f2;color:#ef4444;border:none;font-size:.78rem;font-weight:600;">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <p style="color:#475569;font-size:.9rem;margin:0;white-space:pre-wrap;">{{ $note->content }}</p>
                                        <div class="mt-2" style="font-size:.72rem;color:#94a3b8;">
                                            <i class="bi bi-clock me-1"></i>Added {{ $note->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    {{-- Edit mode (hidden by default) --}}
                                    <div class="cn-edit" id="cn-edit-{{ $note->id }}" style="display:none;">
                                        <form method="POST" action="{{ route('teacher.courses.notes.update', [$course->id, $note->id]) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="row g-2 mb-2">
                                                <div class="col-md-4">
                                                    <input type="date" name="class_date" class="form-control form-control-sm rounded-3"
                                                           value="{{ $note->class_date->format('Y-m-d') }}" required>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" name="title" class="form-control form-control-sm rounded-3"
                                                           placeholder="Title (optional)" value="{{ $note->title }}" maxlength="255">
                                                </div>
                                            </div>
                                            <textarea name="content" rows="4" class="form-control form-control-sm rounded-3 mb-2"
                                                      maxlength="3000" required>{{ $note->content }}</textarea>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-sm rounded-3 fw-bold px-4"
                                                        style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;">
                                                    <i class="bi bi-save me-1"></i>Update
                                                </button>
                                                <button type="button" class="btn btn-sm rounded-3 px-3"
                                                        style="background:#f1f5f9;color:#64748b;border:none;"
                                                        onclick="toggleNoteEdit({{ $note->id }})">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <hr style="border-color:#f1f5f9;margin:1rem 0;">
                                @endif
                                @empty
                                <div class="cd-empty">
                                    <i class="bi bi-journal-x"></i>
                                    <p>No class notes yet. Use the form to add your first note.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cd-tab-pane" id="tab-chat">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('courses.chat.show', $course) }}" class="btn btn-sm fw-semibold" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;border:none;border-radius:10px;">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Open full chat
                    </a>
                </div>
                <x-course-chat :course="$course" :user="$user" />
            </div>

            {{-- CURRICULUM MANAGEMENT --}}
            <div class="cd-tab-pane" id="tab-curriculum">

                @if(session('lesson_success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('lesson_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="row g-4">
                    {{-- Add Lesson Form --}}
                    <div class="col-lg-4">
                        <div class="cd-card h-100">
                            <div class="cd-card-header">
                                <h6 class="cd-card-title"><i class="bi bi-plus-circle-fill me-2" style="color:#1F8FFF;"></i>Add New Lesson</h6>
                            </div>
                            <div class="cd-card-body">
                                <form method="POST" action="{{ route('teacher.courses.lessons.store', $course->id) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">Lesson Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control rounded-3"
                                               placeholder="e.g. Introduction to Variables"
                                               value="{{ old('title') }}" required maxlength="255">
                                        @error('title')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">Description</label>
                                        <textarea name="description" rows="3" class="form-control rounded-3"
                                                  placeholder="Brief description of what this lesson covers..."
                                                  maxlength="1000">{{ old('description') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">Duration (minutes)</label>
                                        <input type="number" name="duration_minutes" class="form-control rounded-3"
                                               placeholder="e.g. 45" min="1" max="600"
                                               value="{{ old('duration_minutes') }}">
                                    </div>
                                    <div class="mb-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_free" value="1" id="newLessonFree" {{ old('is_free') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="newLessonFree" style="font-size:.85rem;color:#475569;">Free Preview</label>
                                        </div>
                                        <small class="text-muted">Allow non-enrolled students to preview this lesson</small>
                                    </div>
                                    <button type="submit" class="btn w-100 rounded-3 fw-bold py-2"
                                            style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;">
                                        <i class="bi bi-plus-lg me-2"></i>Add Lesson
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Lessons List --}}
                    <div class="col-lg-8">
                        <div class="cd-card">
                            <div class="cd-card-header">
                                <h6 class="cd-card-title">
                                    <i class="bi bi-list-ol me-2" style="color:#1F8FFF;"></i>Course Lessons
                                </h6>
                                <span class="badge rounded-pill px-3" style="background:#eff6ff;color:#1F8FFF;">
                                    {{ $totalLessons }} lesson{{ $totalLessons !== 1 ? 's' : '' }}
                                </span>
                            </div>
                            <div class="cd-card-body">
                                @forelse($course->lessons()->orderBy('order')->get() as $lesson)
                                <div class="curr-lesson-card mb-3" id="curr-lesson-{{ $lesson->id }}">
                                    {{-- View Mode --}}
                                    <div class="curr-view" id="curr-view-{{ $lesson->id }}">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="curr-order-num">{{ $lesson->order ?: $loop->iteration }}</div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                                    <span class="fw-bold" style="color:#1e293b;font-size:.95rem;">{{ $lesson->title }}</span>
                                                    @if($lesson->is_free)
                                                        <span class="badge rounded-pill px-2" style="background:#dcfce7;color:#15803d;font-size:.68rem;">FREE</span>
                                                    @endif
                                                    @if($lesson->duration_minutes)
                                                        <span class="badge rounded-pill px-2" style="background:#f1f5f9;color:#64748b;font-size:.68rem;">
                                                            <i class="bi bi-clock me-1"></i>{{ $lesson->duration_minutes }} min
                                                        </span>
                                                    @endif
                                                    @if($lesson->video_url)
                                                        <span class="badge rounded-pill px-2" style="background:#f0fdf4;color:#10b981;font-size:.68rem;">
                                                            <i class="bi bi-camera-video-fill me-1"></i>Has Recording
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($lesson->description)
                                                    <p style="color:#64748b;font-size:.82rem;margin:0;">{{ Str::limit($lesson->description, 120) }}</p>
                                                @endif
                                            </div>
                                            <div class="d-flex gap-2 flex-shrink-0">
                                                <button class="btn btn-sm rounded-pill px-3"
                                                        style="background:#eff6ff;color:#1F8FFF;border:none;font-size:.78rem;font-weight:600;"
                                                        onclick="toggleCurrEdit({{ $lesson->id }})">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </button>
                                                <form method="POST"
                                                      action="{{ route('teacher.courses.lessons.delete', [$course->id, $lesson->id]) }}"
                                                      onsubmit="return confirm('Are you sure you want to delete this lesson? This action cannot be undone.')"
                                                      style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm rounded-pill px-3"
                                                            style="background:#fef2f2;color:#ef4444;border:none;font-size:.78rem;font-weight:600;">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Edit Mode (hidden by default) --}}
                                    <div class="curr-edit" id="curr-edit-{{ $lesson->id }}" style="display:none;">
                                        <form method="POST" action="{{ route('teacher.courses.lessons.update', [$course->id, $lesson->id]) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="row g-2 mb-2">
                                                <div class="col-md-1">
                                                    <label class="form-label" style="font-size:.75rem;color:#94a3b8;">Order</label>
                                                    <input type="number" name="order" class="form-control form-control-sm rounded-3"
                                                           value="{{ $lesson->order }}" min="1">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label" style="font-size:.75rem;color:#94a3b8;">Title *</label>
                                                    <input type="text" name="title" class="form-control form-control-sm rounded-3"
                                                           value="{{ $lesson->title }}" required maxlength="255">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label" style="font-size:.75rem;color:#94a3b8;">Duration (min)</label>
                                                    <input type="number" name="duration_minutes" class="form-control form-control-sm rounded-3"
                                                           value="{{ $lesson->duration_minutes }}" min="1" max="600">
                                                </div>
                                                <div class="col-md-3 d-flex align-items-end">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_free" value="1"
                                                               id="editFree{{ $lesson->id }}" {{ $lesson->is_free ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="editFree{{ $lesson->id }}" style="font-size:.82rem;">Free</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <textarea name="description" rows="2" class="form-control form-control-sm rounded-3"
                                                          placeholder="Description..." maxlength="1000">{{ $lesson->description }}</textarea>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-sm rounded-3 fw-bold px-4"
                                                        style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;">
                                                    <i class="bi bi-save me-1"></i>Save Changes
                                                </button>
                                                <button type="button" class="btn btn-sm rounded-3 px-3"
                                                        style="background:#f1f5f9;color:#64748b;border:none;"
                                                        onclick="toggleCurrEdit({{ $lesson->id }})">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @empty
                                <div class="cd-empty">
                                    <i class="bi bi-journal-plus"></i>
                                    <p>No lessons yet. Use the form to add your first lesson.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
<script src="{{ asset('assets/js/pages/teacher-dashboard.js') }}"></script>
<script src="{{ asset('assets/js/attendance.js') }}"></script>
<script>
// Class Note Notification Handler
// Show toast when note is successfully sent to students
@if(session('note_success'))
document.addEventListener('DOMContentLoaded', function() {
    // Show success toast with animation
    showNotificationToast('{{ session('note_success') }}', 'success');
});
@endif

function showNotificationToast(message, type = 'success') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideIn 0.3s ease;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>${message}</div>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    document.body.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// Add slide animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>
<script>
function addVideoLink() {
    const lessonId = document.getElementById('completed_lesson_select').value;
    const videoUrl = document.getElementById('video_link_input').value.trim();

    if (!lessonId) {
        alert('Please select a completed lesson');
        return;
    }

    if (!videoUrl) {
        alert('Please enter a video URL');
        return;
    }

    // Validate URL
    try {
        new URL(videoUrl);
    } catch {
        alert('Please enter a valid URL');
        return;
    }

    // TODO: Add AJAX call to save video link
    alert('Video link added successfully!\nLesson ID: ' + lessonId + '\nURL: ' + videoUrl);

    // Clear inputs
    document.getElementById('completed_lesson_select').value = '';
    document.getElementById('video_link_input').value = '';
}
</script>
<script>
function switchTab(id, btn) {
    document.querySelectorAll('.cd-tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.cd-tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + id).classList.add('active');
    if (btn) btn.classList.add('active');
}

// Activate tab from URL ?tab=... or #...
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const hash = window.location.hash.replace('#', '');
    const tabFromUrl = params.get('tab') || hash;
    if (tabFromUrl) {
        const btn = document.querySelector('.cd-tab-btn[onclick*="switchTab(\'' + tabFromUrl + '\'"');
        if (btn) switchTab(tabFromUrl, btn);
    }
});

@if(session('student_action'))
document.addEventListener('DOMContentLoaded', function () {
    const studentsBtn = document.querySelector('[onclick*="switchTab(\'students\'"]');
    if (studentsBtn) switchTab('students', studentsBtn);
});
@endif

// Student search
const searchInput = document.getElementById('studentSearch');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.student-item').forEach(el => {
            el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}

// Attendance: old session switcher (kept for compatibility)
function switchAttSession(sessionId, btn) {
    document.querySelectorAll('.att-session-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.att-session-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('att-session-' + sessionId).classList.add('active');
    if (btn) btn.classList.add('active');
}

// Attendance v2: dropdown-driven session switcher
function showSessionPane(sessionId) {
    document.querySelectorAll('.att-session-pane').forEach(p => p.classList.remove('active'));
    const pane = document.getElementById('att-session-' + sessionId);
    if (pane) pane.classList.add('active');
}

// Attendance v2: pill radio visual update
function updatePillGroup(input) {
    const group = input.closest('.att-radio-group-v2');
    if (!group) return;
    group.querySelectorAll('.att-pill-lbl').forEach(lbl => {
        lbl.classList.remove('att-pill-present-on','att-pill-late-on','att-pill-absent-on');
        lbl.classList.add('att-pill-off');
    });
    const activeLbl = input.closest('.att-pill-lbl');
    if (activeLbl) {
        activeLbl.classList.remove('att-pill-off');
        const val = input.value;
        if (val === 'present') activeLbl.classList.add('att-pill-present-on');
        else if (val === 'late') activeLbl.classList.add('att-pill-late-on');
        else if (val === 'absent') activeLbl.classList.add('att-pill-absent-on');
    }
}

// Attendance: mark all students in a session as present/absent
function markAll(sessionId, status) {
    const pane = document.getElementById('att-session-' + sessionId);
    if (!pane) return;
    pane.querySelectorAll('input[type=radio][value=' + status + ']').forEach(r => {
        r.checked = true;
        r.dispatchEvent(new Event('change', { bubbles: true }));
    });
}

// Attendance: highlight selected radio label
document.addEventListener('change', function (e) {
    if (!e.target.classList.contains('att-radio-inp')) return;
    const group = e.target.closest('.att-radio-group');
    if (!group) return;
    group.querySelectorAll('.att-radio-lbl').forEach(l => l.classList.remove('selected'));
    e.target.closest('.att-radio-lbl').classList.add('selected');
});

// Init selected state on page load
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.att-radio-inp:checked').forEach(r => {
        r.closest('.att-radio-lbl').classList.add('selected');
    });
});

// Toggle recording link form per lesson
function toggleRecordingForm(lessonId) {
    const form = document.getElementById('rec-form-' + lessonId);
    if (!form) return;
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    if (form.style.display === 'block') {
        form.querySelector('input[type=url]').focus();
    }
}

// File chosen preview + client-side size check
function handleFileChosen(input) {
    const file = input.files[0];
    if (!file) return;

    const maxBytes = 3 * 1024 * 1024;
    if (file.size > maxBytes) {
        alert('File is too large. Maximum allowed size is 3 MB.');
        input.value = '';
        document.getElementById('fileChosen').style.display = 'none';
        return;
    }
    document.getElementById('fileChosenName').textContent = file.name;
    document.getElementById('fileChosen').style.display = 'block';
}

// Toggle curriculum lesson edit form
function toggleCurrEdit(lessonId) {
    const view = document.getElementById('curr-view-' + lessonId);
    const edit = document.getElementById('curr-edit-' + lessonId);
    if (!view || !edit) return;
    const isEditing = edit.style.display !== 'none';
    view.style.display = isEditing ? '' : 'none';
    edit.style.display = isEditing ? 'none' : '';
    if (!isEditing) edit.querySelector('input[name=title]').focus();
}

// Toggle class note edit form
function toggleNoteEdit(noteId) {
    const view = document.getElementById('cn-view-' + noteId);
    const edit = document.getElementById('cn-edit-' + noteId);
    if (!view || !edit) return;
    const isEditing = edit.style.display !== 'none';
    view.style.display = isEditing ? '' : 'none';
    edit.style.display = isEditing ? 'none' : '';
    if (!isEditing) edit.querySelector('textarea').focus();
}

// Auto-open Notes tab on note_success
@if(session('note_success'))
document.addEventListener('DOMContentLoaded', function() {
    const noteBtn = document.querySelector('[onclick*="classnotes"]');
    if (noteBtn) noteBtn.click();
});
@endif

// Auto-open Curriculum tab on lesson success
@if(session('lesson_success'))
document.addEventListener('DOMContentLoaded', function() {
    const currBtn = document.querySelector('[onclick*="curriculum"]');
    if (currBtn) currBtn.click();
});
@endif

// Auto-open Documents tab if session success or validation error targets it
@if(session('doc_success') || $errors->has('file') || $errors->has('title') || $errors->has('description') || $errors->has('lesson_id'))
document.addEventListener('DOMContentLoaded', function() {
    const docBtn = document.querySelector('[onclick*="documents"]');
    if (docBtn) docBtn.click();
});
@endif

// Live Class Session Duration and Auto-close Check
@if(isset($activeSession) && $activeSession)
document.addEventListener('DOMContentLoaded', function() {
    const sessionStartedAt = new Date('{{ $activeSession->started_at }}');
    const durationEl = document.getElementById('sessionDuration');
    const autoCloseWarning = document.getElementById('autoCloseWarning');
    const autoCloseMessage = document.getElementById('autoCloseMessage');
    const courseId = {{ $course->id }};

    // Update duration every minute
    function updateDuration() {
        const now = new Date();
        const diffMs = now - sessionStartedAt;
        const diffMins = Math.floor(diffMs / 60000);

        if (diffMins < 60) {
            durationEl.textContent = diffMins + ' min';
        } else {
            const hours = Math.floor(diffMins / 60);
            const mins = diffMins % 60;
            durationEl.textContent = hours + 'h ' + mins + 'm';
        }

        // Show warning when approaching auto-close (5+ minutes with no participants)
        if (diffMins >= 5 && {{ $activeSession->participants_count ?? 0 }} === 0) {
            const remaining = 5; // Minutes until auto-close
            autoCloseWarning.style.display = 'block';
            autoCloseMessage.textContent = 'No participants detected. Session will auto-close in ' + remaining + ' minutes.';
        }
    }

    // Initial update
    updateDuration();

    // Update every minute
    setInterval(updateDuration, 60000);

    // Check for auto-close every minute (when there are no participants)
    setInterval(function() {
        if ({{ $activeSession->participants_count ?? 0 }} === 0) {
            // Ping the server to check if session should auto-close
            fetch('{{ route("teacher.courses.auto-close") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.closed_count > 0) {
                    // Session was auto-closed, reload the page
                    window.location.reload();
                }
            })
            .catch(error => console.log('Auto-close check error:', error));
        }
    }, 60000);
});
@endif
</script>
@endpush
