@extends('layouts.app')
@section('hide_header')
@endsection

@section('hide_footer')
@endsection
@section('title', $course->title . ' - مرکز آموزش و دروس - ادوُرا تِک')

@push('styles')
<link href="{{ asset('assets/css/student-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/course-learning.css') }}" rel="stylesheet" />
<style>
/* Animations for live notes */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
@keyframes slideOutRight {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}
</style>
@endpush

@section('content')

  <div class="dashboard-wrapper">
    <x-student-sidebar />

    <main class="main-content">
      <div class="container-fluid py-4 px-xl-4">

        {{-- Day names & levels helper --}}
        @php
          $dayNamesFa = [
            'saturday' => 'شنبه',
            'sunday' => 'یکشنبه',
            'monday' => 'دوشنبه',
            'tuesday' => 'سه‌شنبه',
            'wednesday' => 'چهارشنبه',
            'thursday' => 'پنج‌شنبه',
            'friday' => 'جمعه',
          ];
          $translatedDays = [];
          if (!empty($course->primary_class_days)) {
              foreach ($course->primary_class_days as $day) {
                  $translatedDays[] = $dayNamesFa[strtolower($day)] ?? $day;
              }
          }
          $levelNamesFa = [
            'beginner' => 'مبتدی',
            'intermediate' => 'متوسط',
            'advanced' => 'پیشرفته',
          ];
          $courseLevelFa = $levelNamesFa[strtolower($course->level ?? '')] ?? ucfirst($course->level);

          $catNamesFa = [
            'programming' => 'برنامه‌نویسی',
            'design' => 'طراحی و گرافیک',
            'business' => 'تجارت و مدیریت',
            'languages' => 'زبان‌های خارجی',
          ];
          $courseCatFa = $catNamesFa[strtolower($course->category->name ?? '')] ?? $course->category?->name;
        @endphp

        {{-- Hero Header --}}
        <div class="learning-hero">
          <div class="learning-hero-content">
            <div class="breadcrumb-light">
              <a href="{{ route('student.dashboard') }}">داشبورد</a>
              <i class="bi bi-chevron-left"></i>
              <a href="{{ route('student.courses') }}">کورس‌های من</a>
              <i class="bi bi-chevron-left"></i>
              <span>{{ Str::limit($course->title, 40) }}</span>
            </div>

            <h1>{{ $course->title }}</h1>

            <div class="d-flex flex-wrap gap-2 align-items-center mt-2">
              @if($course->teacher)
              <a href="{{ route('teachers.show', $course->teacher_id) }}" class="teacher-badge text-decoration-none">
                <i class="bi bi-person-fill"></i>
                استاد: {{ $course->teacher->name }}
              </a>
              @else
              <span class="teacher-badge">
                <i class="bi bi-person-fill"></i>
                استاد دوره
              </span>
              @endif

              @if($course->category)
              <span class="teacher-badge">
                <i class="bi bi-tag-fill"></i>
                {{ $courseCatFa }}
              </span>
              @endif

              @if($course->level)
              <span class="teacher-badge">
                <i class="bi bi-bar-chart-steps"></i>
                سطح {{ $courseLevelFa }}
              </span>
              @endif

              {{-- Class Schedule --}}
              @if($course->primary_class_start && $course->primary_class_end)
              <span class="teacher-badge" title="ساعت برگزاری صنف">
                <i class="bi bi-clock"></i>
                ساعت {{ \Carbon\Carbon::parse($course->primary_class_start)->format('h:i A') }} الی {{ \Carbon\Carbon::parse($course->primary_class_end)->format('h:i A') }}
                @if(!empty($translatedDays))
                  ({{ implode('، ', $translatedDays) }})
                @endif
              </span>
              @endif

              {{-- Course Duration --}}
              @if($course->start_date || $course->end_date)
              <span class="teacher-badge" title="مدت زمان کورس">
                <i class="bi bi-calendar-range"></i>
                @if($course->start_date && $course->end_date)
                  از {{ $course->start_date->format('Y/m/d') }} الی {{ $course->end_date->format('Y/m/d') }}
                @elseif($course->start_date)
                  آغاز از {{ $course->start_date->format('Y/m/d') }}
                @else
                  تا تاریخ {{ $course->end_date->format('Y/m/d') }}
                @endif
              </span>
              @endif
            </div>

            {{-- Progress Badge --}}
            <div class="mt-2 mb-2 d-flex flex-wrap gap-2">
              <span class="badge bg-success" style="font-size:0.85rem;">
                <i class="bi bi-check-circle-fill me-1"></i>
                {{ $completedLessonsCount ?? 0 }}/{{ $totalLessons ?? count($lessons) }} درس
              </span>
              <span class="badge bg-info text-dark" style="font-size:0.85rem;">
                <i class="bi bi-graph-up me-1"></i>{{ $progressPercent ?? 0 }}٪ تکمیل شده
              </span>
            </div>

            <div class="progress-overview">
              <div class="progress-overview-bar">
                <div class="progress-track">
                  <div class="progress-fill" style="width: {{ $stats['progress'] }}%"></div>
                </div>
                <div class="progress-text">{{ $stats['progress'] }}٪ تکمیل شده &mdash; {{ $stats['completed_lessons'] }} از مجموع {{ $stats['total_lessons'] }} درس تمام شد</div>
              </div>
              <div class="d-flex flex-wrap gap-2">
                <span class="progress-stat-pill">
                  <i class="bi bi-journal-text"></i> {{ $stats['total_lessons'] }} درس
                </span>
                <span class="progress-stat-pill">
                  <i class="bi bi-file-earmark-arrow-down"></i> {{ $stats['total_documents'] }} فایل
                </span>
                <span class="progress-stat-pill">
                  <i class="bi bi-sticky"></i> {{ $stats['total_notes'] }} نوت
                </span>
                <span class="progress-stat-pill" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
                  <i class="bi bi-clock"></i>
                  @if(($stats['total_learning_hours'] ?? 0) > 0)
                    {{ $stats['total_learning_hours'] }} ساعت و {{ $stats['total_learning_minutes'] }} دقیقه
                  @else
                    {{ $stats['total_learning_minutes'] ?? 0 }} دقیقه
                  @endif
                  مدت آموزش
                </span>
              </div>
            </div>
          </div>
        </div>

        {{-- Live Class Banner - Shows when class is active --}}
        <div id="live-class-banner" class="mb-4 p-4 rounded-4 {{ $activeSession ? '' : 'd-none' }}" style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;box-shadow:0 10px 40px rgba(31,143,255,0.3);">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
              <div style="background:#fff;border-radius:12px;width:50px;height:50px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-broadcast" style="font-size:24px;color:#1F8FFF;"></i>
              </div>
              <div>
                <div style="font-weight:700;font-size:1.1rem;">
                  <i class="bi bi-broadcast me-1"></i>صنف آنلاین هم‌اکنون در جریان است
                </div>
                <div style="font-size:.85rem;opacity:.9;" id="live-class-info">
                  @if($activeSession && $activeSession->lesson)
                    <span class="badge bg-warning text-dark me-2"><i class="bi bi-book me-1"></i>درس {{ $activeSession->lesson->order }}</span>
                    {{ $activeSession->lesson->title }}
                  @else
                    Google Meet
                  @endif
                  @if($activeSession && $activeSession->room_name)
                    • اتاق: <span id="live-room-name">{{ $activeSession->room_name }}</span>
                  @endif
                </div>
              </div>
            </div>
            <div>
              <button id="join-class-btn" class="btn fw-bold px-4 py-2" onclick="joinClass('{{ $activeSession->id ?? '' }}')" style="background:#fff;color:#1F8FFF;border:none;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
                <i class="bi bi-box-arrow-up-right me-2"></i>پیوستن به صنف
              </button>
            </div>
          </div>
          <div class="mt-3 pt-3" style="border-top:1px solid rgba(255,255,255,0.2);">
            <div class="d-flex flex-wrap align-items-center gap-2" style="font-size:.8rem;opacity:.9;">
              <i class="bi bi-info-circle"></i>
              <span>شما می‌توانید به صنف وصل شوید؛ کمره و مایکروفون شما در ابتدا خاموش می‌باشد. برای طرح سوال از بخش چت استفاده کنید.</span>
            </div>
          </div>
        </div>

        {{-- Quick Stats --}}
        <div class="learning-stats-row">
          <div class="learning-stat-card">
            <div class="learning-stat-icon blue"><i class="bi bi-journal-text"></i></div>
            <div class="learning-stat-value">{{ $stats['total_lessons'] }}</div>
            <div class="learning-stat-label">درس‌ها</div>
          </div>
          <div class="learning-stat-card">
            <div class="learning-stat-icon green"><i class="bi bi-check-circle"></i></div>
            <div class="learning-stat-value">{{ $stats['completed_lessons'] }}</div>
            <div class="learning-stat-label">تکمیل‌شده</div>
          </div>
          <div class="learning-stat-card">
            <div class="learning-stat-icon orange"><i class="bi bi-file-earmark-arrow-down"></i></div>
            <div class="learning-stat-value">{{ $stats['total_documents'] }}</div>
            <div class="learning-stat-label">فایل‌ها</div>
          </div>
          <div class="learning-stat-card">
            <div class="learning-stat-icon purple"><i class="bi bi-pencil-square"></i></div>
            <div class="learning-stat-value">{{ $stats['total_quizzes'] }}</div>
            <div class="learning-stat-label">امتحانات</div>
          </div>
          <div class="learning-stat-card">
            <div class="learning-stat-icon cyan"><i class="bi bi-camera-video"></i></div>
            <div class="learning-stat-value">{{ $stats['total_sessions'] }}</div>
            <div class="learning-stat-label">جلسات</div>
          </div>
          <div class="learning-stat-card">
            <div class="learning-stat-icon" style="background: linear-gradient(135deg, #10b981, #059669); color: white;"><i class="bi bi-clock"></i></div>
            <div class="learning-stat-value">
              @if(($stats['total_learning_hours'] ?? 0) > 0)
                {{ $stats['total_learning_hours'] }}س {{ $stats['total_learning_minutes'] }}د
              @else
                {{ $stats['total_learning_minutes'] ?? 0 }}د
              @endif
            </div>
            <div class="learning-stat-label">مدت آموزش</div>
          </div>
        </div>


        {{-- ===================== CURRICULUM TAB ===================== --}}
        <div class="tab-panel active" id="panel-curriculum">
          @if($lessons->isEmpty())
            <div class="learning-empty">
              <i class="bi bi-journal-x"></i>
              <div class="learning-empty-title">هنوز درسی اضافه نشده است</div>
              <p>استاد محترم تاکنون درسی برای این کورس ثبت نکرده است.</p>
            </div>
          @else
            {{-- Progress Bar --}}
            <div class="mb-4 p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-dark"><i class="bi bi-check-circle-fill text-success me-2"></i>پیشرفت درسی شما</span>
                <span class="badge bg-success">{{ $completedLessonsCount ?? 0 }} / {{ $totalLessons ?? count($lessons) }} درس</span>
              </div>
              <div class="progress" style="height:10px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercent ?? 0 }}%"></div>
              </div>
              <div class="text-end mt-1">
                <small class="text-muted">{{ $progressPercent ?? 0 }}٪ تکمیل شد</small>
              </div>
            </div>

            <div class="lesson-list">
              @foreach($lessons as $index => $lesson)
              @php
                $isCompleted = ($lessonCompletion[$lesson->id] ?? false) || (isset($completedLessonIds) && in_array($lesson->id, $completedLessonIds));
              @endphp
              <div class="lesson-item {{ $isCompleted ? 'completed' : '' }}" style="{{ $isCompleted ? 'background:#f0fdf4;border-right:4px solid #22c55e;' : '' }}">
                <div class="lesson-number" style="{{ $isCompleted ? 'background:#22c55e;color:#fff;' : '' }}">
                  @if($isCompleted)
                    <i class="bi bi-check-lg"></i>
                  @else
                    {{ $index + 1 }}
                  @endif
                </div>
                <div class="lesson-info">
                  <div class="lesson-title lesson-expand-toggle" data-lesson="{{ $lesson->id }}">
                    {{ $lesson->title }}
                    @if($isCompleted)
                      <span class="badge bg-success ms-2" style="font-size:0.65rem;">تکمیل شد</span>
                    @endif
                    <i class="bi bi-chevron-down expand-icon" style="font-size: 0.75rem; margin-right: 0.25rem;"></i>
                  </div>
                  <div class="lesson-meta">
                    @if($lesson->duration_minutes)
                    <span><i class="bi bi-clock me-1"></i>{{ $lesson->duration_minutes }} دقیقه</span>
                    @endif
                    @if($lesson->is_free)
                    <span><i class="bi bi-unlock me-1"></i>رایگان</span>
                    @endif
                  </div>

                  {{-- Expandable Content --}}
                  <div class="lesson-expand-content" id="lesson-content-{{ $lesson->id }}">
                    @if($lesson->description)
                      <p>{{ $lesson->description }}</p>
                    @endif
                    @if($lesson->content)
                      <div>{!! nl2br(e(Str::limit($lesson->content, 500))) !!}</div>
                    @endif

                    {{-- Documents attached to this lesson --}}
                    @php $lessonDocs = $documents->where('lesson_id', $lesson->id); @endphp
                    @if($lessonDocs->count() > 0)
                    <div class="mt-3">
                      <strong class="d-block mb-2" style="font-size: 0.8125rem; color: #6b7280;">
                        <i class="bi bi-paperclip me-1"></i>فایل‌های ضمیمه ({{ $lessonDocs->count() }})
                      </strong>
                      @foreach($lessonDocs as $doc)
                      <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="document-download me-2 mb-1">
                        <i class="bi bi-download"></i> {{ $doc->title ?: $doc->file_name }}
                      </a>
                      @endforeach
                    </div>
                    @endif
                  </div>
                </div>

                <span class="lesson-status-badge {{ $isCompleted ? 'done' : 'pending' }}">
                  {{ $isCompleted ? 'تکمیل گردید' : 'در انتظار تکمیل' }}
                </span>
              </div>
              @endforeach
            </div>
          @endif
        </div>

        {{-- ===================== DOCUMENTS TAB ===================== --}}
        <div class="tab-panel" id="panel-documents">
          @if($documents->isEmpty())
            <div class="learning-empty">
              <i class="bi bi-folder-x"></i>
              <div class="learning-empty-title">هنوز فایلی ثبت نشده است</div>
              <p>هیچ اسناد یا فایل درسی برای این کورس آپلود نشده است.</p>
            </div>
          @else
            <div class="document-grid">
              @foreach($documents as $doc)
              @php
                $ext = strtolower(pathinfo($doc->file_name ?? $doc->file_path, PATHINFO_EXTENSION));
                $iconClass = 'default';
                $iconName = 'bi-file-earmark';
                if (in_array($ext, ['pdf'])) {
                    $iconClass = 'pdf';
                    $iconName = 'bi-file-earmark-pdf-fill';
                } elseif (in_array($ext, ['doc', 'docx'])) {
                    $iconClass = 'doc';
                    $iconName = 'bi-file-earmark-word-fill';
                } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                    $iconClass = 'img';
                    $iconName = 'bi-file-earmark-image-fill';
                } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
                    $iconClass = 'doc';
                    $iconName = 'bi-file-earmark-spreadsheet-fill';
                } elseif (in_array($ext, ['ppt', 'pptx'])) {
                    $iconClass = 'pdf';
                    $iconName = 'bi-file-earmark-slides-fill';
                } elseif (in_array($ext, ['zip', 'rar', '7z'])) {
                    $iconClass = 'default';
                    $iconName = 'bi-file-earmark-zip-fill';
                }
              @endphp
              <div class="document-card">
                <div class="document-icon {{ $iconClass }}">
                  <i class="bi {{ $iconName }}"></i>
                </div>
                <div class="document-info">
                  <div class="document-title">{{ $doc->title ?: $doc->file_name }}</div>
                  <div class="document-meta">
                    @if($doc->file_size)
                      {{ $doc->file_size_formatted }}
                    @endif
                    @if($doc->lesson)
                      &middot; درس: {{ Str::limit($doc->lesson->title, 20) }}
                    @endif
                    &middot; {{ $doc->created_at->diffForHumans() }}
                  </div>
                  @if($doc->description)
                    <div class="document-meta">{{ Str::limit($doc->description, 80) }}</div>
                  @endif
                  <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="document-download">
                    <i class="bi bi-download"></i> دانلود فایل
                  </a>
                </div>
              </div>
              @endforeach
            </div>
          @endif
        </div>

        {{-- ===================== NOTES TAB ===================== --}}
        <div class="tab-panel" id="panel-notes">
          <div id="class-notes-container" data-course-slug="{{ $course->slug }}">
          @if($classNotes->isEmpty())
            <div class="learning-empty" id="notes-empty-state">
              <i class="bi bi-sticky"></i>
              <div class="learning-empty-title">هنوز نوتی ثبت نشده است</div>
              <p>استاد محترم تاکنون نوتی برای این صنف ارسال نکرده است.</p>
            </div>
            <div class="notes-list d-none" id="notes-list-container">
            </div>
          @else
            <div class="notes-list" id="notes-list-container">
              @foreach($classNotes as $note)
              <div class="note-card">
                <div class="note-header">
                  <div class="note-title">{{ $note->title }}</div>
                  <div class="note-date">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $note->class_date ? $note->class_date->format('Y/m/d') : $note->created_at->format('Y/m/d') }}
                  </div>
                </div>
                <div class="note-author">
                  <i class="bi bi-person-fill"></i>
                  {{ $note->teacher->name ?? 'استاد' }}
                </div>
                @if($note->content)
                <div class="note-content truncated" id="note-content-{{ $note->id }}">{{ $note->content }}</div>
                <button class="note-toggle" data-note="{{ $note->id }}">
                  <i class="bi bi-chevron-down me-1"></i>مشاهده کامل
                </button>
                @endif
              </div>
              @endforeach
            </div>
          @endif
          </div>
        </div>

        {{-- ===================== SESSIONS TAB ===================== --}}
        <div class="tab-panel" id="panel-sessions">
          @if($sessions->isEmpty())
            <div class="learning-empty">
              <i class="bi bi-camera-video-off"></i>
              <div class="learning-empty-title">هنوز جلسه‌ای برگزار نشده است</div>
              <p>هیچ جلسه آنلاین ثبت‌شده‌ای برای این کورس وجود ندارد.</p>
            </div>
          @else
            <div class="sessions-list">
              @foreach($sessions as $session)
              <div class="session-card {{ $session->status === 'active' ? 'session-active' : '' }}">
                <div class="session-icon {{ $session->status === 'active' ? 'session-icon-live' : '' }}">
                  <i class="bi bi-camera-video-fill"></i>
                </div>
                <div class="session-info">
                  <div class="session-title">
                    <span class="badge me-1" style="background:#1F8FFF;color:#fff;font-size:.65rem;">Google Meet</span>
                    جلسه شماره {{ $loop->remaining + 1 }}
                    @if($session->note)
                      <span class="text-muted">&mdash; {{ Str::limit($session->note, 40) }}</span>
                    @endif
                  </div>
                  <div class="session-meta">
                    @if($session->started_at)
                    <span><i class="bi bi-calendar3 me-1"></i>{{ $session->started_at->format('Y/m/d') }}</span>
                    <span><i class="bi bi-clock me-1"></i>{{ $session->started_at->format('h:i A') }}</span>
                    @endif
                    <span><i class="bi bi-hourglass me-1"></i>{{ $session->duration }}</span>
                    @if($session->attendees_count)
                    <span><i class="bi bi-people me-1"></i>{{ $session->attendees_count }} محصل حاضر</span>
                    @endif
                  </div>
                </div>
                <div class="d-flex flex-column align-items-end gap-2">
                  <span class="session-status {{ $session->status === 'active' ? 'live' : 'ended' }}">
                    {{ $session->status === 'active' ? 'پخش زنده هم‌اکنون' : 'پایان یافته' }}
                  </span>
                  @if($session->status === 'active')
                    <a href="{{ $session->meet_link }}" target="_blank"
                       class="btn btn-sm fw-bold px-3"
                       style="background:#1F8FFF;color:#fff;border:none;border-radius:8px;font-size:.75rem;">
                      <i class="bi bi-box-arrow-up-right me-1"></i>پیوستن
                    </a>
                  @endif
                </div>
              </div>
              @endforeach
            </div>
          @endif
        </div>

        {{-- ===================== QUIZZES TAB ===================== --}}
        <div class="tab-panel" id="panel-quizzes">
          @if($quizzes->isEmpty())
            <div class="learning-empty">
              <i class="bi bi-pencil-square"></i>
              <div class="learning-empty-title">امتحانی موجود نیست</div>
              <p>هیچ کویز یا امتحانی برای این کورس در دسترس نمی‌باشد.</p>
            </div>
          @else
            <div class="quiz-grid">
              @foreach($quizzes as $quiz)
              @php $attempt = $quizAttempts->get($quiz->id); @endphp
              <div class="quiz-card">
                <div class="quiz-header">
                  <div class="quiz-icon">
                    <i class="bi bi-pencil-square"></i>
                  </div>
                  <div class="quiz-title">{{ $quiz->title }}</div>
                </div>

                <div class="quiz-meta">
                  <span><i class="bi bi-question-circle me-1"></i>{{ $quiz->questions_count }} سوال</span>
                  @if($quiz->duration_minutes)
                  <span><i class="bi bi-clock me-1"></i>{{ $quiz->duration_minutes }} دقیقه</span>
                  @endif
                  @if($quiz->passing_score)
                  <span><i class="bi bi-bullseye me-1"></i>نمره قبولی: {{ $quiz->passing_score }}٪</span>
                  @endif
                </div>

                @if($attempt)
                  <div class="quiz-result {{ $attempt->passed ? 'passed' : 'failed' }}">
                    <div class="quiz-result-label">
                      {{ $attempt->passed ? 'مؤفقانه سپری شد' : 'ناکام / نیاز به تکرار' }}
                    </div>
                    <div class="quiz-result-score">
                      {{ $attempt->score }}/{{ $attempt->total_points }}
                      @if($attempt->total_points > 0)
                        ({{ round(($attempt->score / $attempt->total_points) * 100) }}٪)
                      @endif
                    </div>
                  </div>
                @endif

                @if($quiz->is_published)
                  @if(!$attempt || ($quiz->max_attempts && $attempt))
                    <a href="{{ route('student.exams.take', $quiz->id) }}" class="quiz-action">
                      <i class="bi bi-play-fill me-1"></i>
                      {{ $attempt ? 'امتحان مجدد' : 'شروع امتحان' }}
                    </a>
                  @endif
                @else
                  <div class="quiz-action" style="opacity: 0.5; pointer-events: none;">
                    <i class="bi bi-lock me-1"></i> فعلاً در دسترس نیست
                  </div>
                @endif
              </div>
              @endforeach
            </div>
          @endif
        </div>

        <div class="tab-panel" id="panel-chat">
          <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('courses.chat.show', $course) }}" class="btn btn-sm fw-semibold" style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;border-radius:10px;">
              <i class="bi bi-box-arrow-up-right me-1"></i> باز کردن صفحه کامل گفتگو
            </a>
          </div>
          <x-course-chat :course="$course" :user="$user" />
        </div>

        {{-- ===================== REVIEWS TAB ===================== --}}
        <div class="tab-panel" id="panel-reviews">

          {{-- Write / Edit Review Form --}}
          <div class="review-form-card mb-4">
            <h5 class="fw-bold mb-3" style="color:#1F8FFF;">
              <i class="bi bi-pencil-square me-2"></i>{{ $userReview ? 'ویرایش نظر شما' : 'ثبت نظر و ارزیابی' }}
            </h5>
            <form id="reviewForm" action="{{ route('student.courses.reviews.store', $course->slug) }}" method="POST">
              @csrf
              <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">امتیاز شما به کورس</label>
                <div class="star-rating-input" id="starRatingInput">
                  @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $userReview && $i <= $userReview->rating ? 'bi-star-fill' : 'bi-star' }} star-input"
                       data-value="{{ $i }}"
                       style="font-size:1.5rem;cursor:pointer;color:{{ $userReview && $i <= $userReview->rating ? '#f59e0b' : '#d1d5db' }};transition:color .15s;"></i>
                  @endfor
                  <input type="hidden" name="rating" id="ratingInput" value="{{ $userReview->rating ?? '' }}">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">دیدگاه و نظر شما</label>
                <textarea name="comment" rows="3" class="form-control" placeholder="تجربه و نظر خود را در مورد این کورس بنویسید..."
                          style="border-radius:10px;border:1px solid #e2e8f0;font-size:.9rem;" required>{{ $userReview->comment ?? '' }}</textarea>
              </div>
              <button type="submit" class="btn fw-bold px-4 py-2" style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;border-radius:10px;">
                <i class="bi bi-send me-2"></i>{{ $userReview ? 'بروزرسانی نظر' : 'ثبت نظر' }}
              </button>
            </form>
            @if(session('review_success'))
              <div class="alert alert-success mt-3 rounded-3" style="font-size:.85rem;">
                <i class="bi bi-check-circle me-1"></i>{{ session('review_success') }}
              </div>
            @endif
          </div>

          {{-- Reviews List --}}
          <div class="review-list-header d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="color:#1e293b;">
              <i class="bi bi-chat-left-text me-2" style="color:#1F8FFF;"></i>نظرات شاگردان
              <span class="badge rounded-pill ms-2" style="background:#1F8FFF;font-size:.75rem;">{{ $reviews->count() }}</span>
            </h5>
            @if($reviews->count() > 0)
              @php $avgRating = $reviews->avg('rating'); @endphp
              <div class="d-flex align-items-center gap-2">
                <div class="d-flex">
                  @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= round($avgRating) ? 'bi-star-fill' : 'bi-star' }}" style="color:#f59e0b;font-size:.9rem;"></i>
                  @endfor
                </div>
                <span class="fw-bold" style="color:#1e293b;">{{ number_format($avgRating, 1) }}</span>
              </div>
            @endif
          </div>

          @if($reviews->isEmpty())
            <div class="learning-empty">
              <i class="bi bi-chat-left-dots"></i>
              <div class="learning-empty-title">هنوز نظری ثبت نشده است</div>
              <p>اولین نفری باشید که برای این کورس نظر ثبت می‌کنید!</p>
            </div>
          @else
            <div class="reviews-list">
              @foreach($reviews as $rev)
              @php
                $isOwn = $rev->user_id === $user->id;
                $userLike = $userReviewLikes[$rev->id] ?? null;
              @endphp
              <div class="review-card-item {{ $isOwn ? 'review-own' : '' }}">
                <div class="d-flex align-items-start gap-3">
                  <div class="review-avatar-circle">
                    @if($isOwn)
                      <img src="{{ $user->avatar ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar)) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=40&background=1F8FFF&color=fff' }}"
                           alt="You" class="review-avatar-img">
                    @else
                      <div class="review-anon-avatar">
                        <i class="bi bi-person-fill"></i>
                      </div>
                    @endif
                  </div>
                  <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <div>
                        <span class="fw-semibold" style="color:#1e293b;font-size:.9rem;">
                          {{ $isOwn ? 'شما' : 'شاگرد' }}
                        </span>
                        @if($isOwn)
                          <span class="badge ms-2" style="background:#dcfce7;color:#15803d;font-size:.65rem;">نظر شما</span>
                        @endif
                      </div>
                      <small class="text-muted">{{ $rev->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="mb-2">
                      @for($i = 1; $i <= 5; $i++)
                        <i class="bi {{ $i <= $rev->rating ? 'bi-star-fill' : 'bi-star' }}" style="color:#f59e0b;font-size:.8rem;"></i>
                      @endfor
                    </div>
                    <p class="mb-2" style="color:#4b5563;font-size:.875rem;line-height:1.6;">{{ $rev->comment }}</p>

                    {{-- Like / Dislike buttons (not for own review) --}}
                    @if(!$isOwn)
                    <div class="d-flex gap-3 mt-1">
                      <button class="btn btn-sm review-like-btn {{ $userLike === 'like' ? 'active-like' : '' }}"
                              data-review-id="{{ $rev->id }}" data-type="like"
                              data-url="{{ parse_url(route('student.reviews.like', $rev->id), PHP_URL_PATH) }}">
                        <i class="bi {{ $userLike === 'like' ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' }}"></i>
                        <span class="like-count">{{ $rev->likes_count }}</span>
                      </button>
                      <button class="btn btn-sm review-like-btn {{ $userLike === 'dislike' ? 'active-dislike' : '' }}"
                              data-review-id="{{ $rev->id }}" data-type="dislike"
                              data-url="{{ parse_url(route('student.reviews.like', $rev->id), PHP_URL_PATH) }}">
                        <i class="bi {{ $userLike === 'dislike' ? 'bi-hand-thumbs-down-fill' : 'bi-hand-thumbs-down' }}"></i>
                        <span class="dislike-count">{{ $rev->dislikes_count }}</span>
                      </button>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          @endif
        </div>

      </div>
    </main>
  </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/student-dashboard.js') }}"></script>
<script src="{{ asset('assets/js/course-learning.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>

<script>
// Live Class Notes Polling System
(function() {
    const notesContainer = document.getElementById('class-notes-container');
    if (!notesContainer) return;

    const courseSlug = notesContainer.dataset.courseSlug;
    let lastNoteCount = parseInt(notesContainer.dataset.noteCount) || 0;
    let isNotesTabActive = false;
    let pollInterval;

    // Check if notes tab is active
    function checkNotesTab() {
        const notesPanel = document.getElementById('panel-notes');
        isNotesTabActive = notesPanel && notesPanel.classList.contains('active');
        return isNotesTabActive;
    }

    // Fetch latest notes via AJAX
    function fetchLatestNotes() {
        if (!checkNotesTab()) return; // Only poll when notes tab is visible

        fetch(`/student/courses/${courseSlug}/notes`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.notes) {
                    updateNotesUI(data.notes);
                }
            })
            .catch(err => console.log('Notes fetch error:', err));
    }

    // Update notes UI
    function updateNotesUI(notes) {
        const container = document.getElementById('notes-list-container');
        const emptyState = document.getElementById('notes-empty-state');

        if (!container) return;

        // Get existing note IDs
        const existingIds = Array.from(container.querySelectorAll('[data-note-id]'))
            .map(el => parseInt(el.dataset.noteId));

        // Find new notes
        const newNotes = notes.filter(note => !existingIds.includes(note.id));

        if (newNotes.length > 0) {
            // Hide empty state if visible
            if (emptyState && !emptyState.classList.contains('d-none')) {
                emptyState.classList.add('d-none');
                container.classList.remove('d-none');
            }

            // Add new notes to top with animation
            newNotes.forEach((note, index) => {
                const noteElement = createNoteElement(note);
                noteElement.style.animation = 'slideInDown 0.5s ease ' + (index * 0.1) + 's';
                container.insertBefore(noteElement, container.firstChild);
            });

            // Update count
            lastNoteCount = notes.length;
        }
    }

    // Create note element HTML
    function createNoteElement(note) {
        const div = document.createElement('div');
        div.className = 'note-card';
        div.setAttribute('data-note-id', note.id);
        div.style.cssText = 'background: #f8fafc; border-left: 4px solid #f59e0b; margin-bottom: 12px; padding: 16px; border-radius: 8px; animation: slideInDown 0.5s ease;';

        div.innerHTML = `
            <div class="note-header">
                <div class="note-title" style="font-weight: 600; color: #1f2937;">${note.title || 'نوت درسی'}</div>
                <div class="note-date" style="font-size: 12px; color: #6b7280;">
                    <i class="bi bi-calendar3 me-1"></i>${note.class_date || note.created_at}
                </div>
            </div>
            <div class="note-author" style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">
                <i class="bi bi-person-circle me-1"></i>ارسال‌شده توسط: ${note.teacher_name}
            </div>
            <div class="note-content" style="font-size: 14px; color: #4b5563; line-height: 1.6;">
                ${note.content}
            </div>
        `;
        return div;
    }

    // Start polling when notes tab is active
    function startPolling() {
        if (pollInterval) return;
        pollInterval = setInterval(fetchLatestNotes, 10000); // Poll every 10 seconds
    }

    // Stop polling
    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    // Tab change detection
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            setTimeout(() => {
                if (checkNotesTab()) {
                    startPolling();
                    fetchLatestNotes(); // Immediate fetch
                } else {
                    stopPolling();
                }
            }, 100);
        });
    });

    // Start polling if notes tab is active on load
    if (checkNotesTab()) {
        startPolling();
    }

    // Expose function for WebSocket callback
    window.refreshClassNotes = fetchLatestNotes;
})();

</script>

{{-- Review System JS --}}
<script>
(function() {
    // Star Rating Input
    const stars = document.querySelectorAll('#starRatingInput .star-input');
    const ratingInput = document.getElementById('ratingInput');

    stars.forEach(function(star) {
        star.addEventListener('click', function() {
            const val = parseInt(this.dataset.value);
            ratingInput.value = val;
            stars.forEach(function(s) {
                const sv = parseInt(s.dataset.value);
                s.classList.toggle('bi-star-fill', sv <= val);
                s.classList.toggle('bi-star', sv > val);
                s.style.color = sv <= val ? '#f59e0b' : '#d1d5db';
            });
        });
        star.addEventListener('mouseenter', function() {
            const val = parseInt(this.dataset.value);
            stars.forEach(function(s) {
                const sv = parseInt(s.dataset.value);
                s.style.color = sv <= val ? '#f59e0b' : '#d1d5db';
            });
        });
    });

    const starContainer = document.getElementById('starRatingInput');
    if (starContainer) {
        starContainer.addEventListener('mouseleave', function() {
            const current = parseInt(ratingInput.value) || 0;
            stars.forEach(function(s) {
                const sv = parseInt(s.dataset.value);
                s.style.color = sv <= current ? '#f59e0b' : '#d1d5db';
            });
        });
    }

    // Like / Dislike buttons
    document.querySelectorAll('.review-like-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
            const type = this.dataset.type;
            const reviewId = this.dataset.reviewId;
            const thisBtn = this;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ type: type })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                // Update all buttons for this review
                const card = thisBtn.closest('.review-card-item');
                const likeBtn = card.querySelector('[data-type="like"]');
                const dislikeBtn = card.querySelector('[data-type="dislike"]');

                likeBtn.querySelector('.like-count').textContent = data.likes_count;
                dislikeBtn.querySelector('.dislike-count').textContent = data.dislikes_count;

                // Reset styles
                likeBtn.classList.remove('active-like');
                dislikeBtn.classList.remove('active-dislike');
                likeBtn.querySelector('i').className = 'bi bi-hand-thumbs-up';
                dislikeBtn.querySelector('i').className = 'bi bi-hand-thumbs-down';

                if (data.status === 'added' || data.status === 'switched') {
                    if (type === 'like') {
                        likeBtn.classList.add('active-like');
                        likeBtn.querySelector('i').className = 'bi bi-hand-thumbs-up-fill';
                    } else {
                        dislikeBtn.classList.add('active-dislike');
                        dislikeBtn.querySelector('i').className = 'bi bi-hand-thumbs-down-fill';
                    }
                }
            });
        });
    });

    // Auto-open reviews tab on success flash
    @if(session('review_success'))
    document.addEventListener('DOMContentLoaded', function() {
        const reviewTab = document.querySelector('[data-tab="reviews"]');
        if (reviewTab) reviewTab.click();
    });
    @endif
})();
</script>

{{-- Class Session Management --}}
<script>
    let classWindow = null;
    let classCheckInterval = null;
    let isInClass = false;

    // Open class in popup window
    function openClassPopup(url) {
        // Close any existing window
        if (classWindow && !classWindow.closed) {
            classWindow.focus();
            return false;
        }

        // Open popup
        const width = screen.width * 0.9;
        const height = screen.height * 0.9;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;

        classWindow = window.open(url, 'LiveClass', `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes,status=yes`);

        if (classWindow) {
            isInClass = true;
            startClassCheck();
        } else {
            alert('لطفاً باز شدن پنجره‌های پاپ‌آپ (Pop-ups) را در مرورگر خود مجاز سازید تا بتوانید وارد صنف شوید.');
        }

        return false;
    }

    // Check if class is still active
    function startClassCheck() {
        if (classCheckInterval) {
            clearInterval(classCheckInterval);
        }

        classCheckInterval = setInterval(() => {
            if (!classWindow || classWindow.closed) {
                isInClass = false;
                clearInterval(classCheckInterval);
                return;
            }

            // Check if session is still active
            fetch('{{ route('student.courses.check-session', $course->slug) }}', {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.is_active && isInClass) {
                    // Class ended, close popup and show message
                    closeClassAndNotify();
                }
            })
            .catch(err => console.log('Class check error:', err));
        }, 5000); // Check every 5 seconds
    }

    // Close class and show notification
    function closeClassAndNotify() {
        isInClass = false;

        // Close the popup window
        if (classWindow && !classWindow.closed) {
            classWindow.close();
        }

        if (classCheckInterval) {
            clearInterval(classCheckInterval);
        }

        // Show alert
        showClassEndedModal();
    }

    // Show class ended modal
    function showClassEndedModal() {
        // Remove existing
        const existing = document.getElementById('class-ended-overlay');
        if (existing) existing.remove();

        const overlay = document.createElement('div');
        overlay.id = 'class-ended-overlay';
        overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.85); z-index: 10000; display: flex; align-items: center; justify-content: center; direction: rtl;';

        overlay.innerHTML = `
            <div style="background: white; border-radius: 16px; padding: 40px; text-align: center; max-width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); font-family: 'Vazirmatn', sans-serif;">
                <div style="background: #fee2e2; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </div>
                <h3 style="color: #1f2937; margin-bottom: 10px; font-size: 22px; font-weight: 700;">صنف به پایان رسید</h3>
                <p style="color: #6b7280; margin-bottom: 24px; font-size: 15px;">استاد محترم جلسه درسی را به پایان رساند.</p>
                <button onclick="document.getElementById('class-ended-overlay').remove(); window.location.reload();"
                        style="background: #1f8fff; color: white; border: none; padding: 12px 32px; border-radius: 8px; font-size: 15px; cursor: pointer; font-weight: 600;">
                    تأیید و تازه‌سازی صفحه
                </button>
            </div>
        `;

        document.body.appendChild(overlay);

        // Play sound
        try {
            const audio = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audio.createOscillator();
            const gain = audio.createGain();
            osc.connect(gain);
            gain.connect(audio.destination);
            osc.frequency.setValueAtTime(400, audio.currentTime);
            gain.gain.setValueAtTime(0.3, audio.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audio.currentTime + 0.3);
            osc.start();
            osc.stop(audio.currentTime + 0.3);
        } catch(e) {}
    }

    // Simple AJAX Polling for Class Notes
    let lastNoteId = 0;
    let notesPollingInterval = null;

    // Play notification sound
    function playNotificationSound() {
        try {
            const audio = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audio.createOscillator();
            const gain = audio.createGain();
            osc.connect(gain);
            gain.connect(audio.destination);
            osc.frequency.setValueAtTime(523.25, audio.currentTime);
            osc.frequency.setValueAtTime(659.25, audio.currentTime + 0.1);
            osc.frequency.setValueAtTime(783.99, audio.currentTime + 0.2);
            gain.gain.setValueAtTime(0.3, audio.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audio.currentTime + 0.8);
            osc.start();
            osc.stop(audio.currentTime + 0.8);
        } catch(e) {}
    }

    // Show notification toast
    function showNoteNotification(note) {
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.style.cssText = 'position:fixed;top:20px;left:20px;z-index:9999;direction:rtl;';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.style.cssText = `
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 10px;
            box-shadow: 0 10px 40px rgba(245, 158, 11, 0.3);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            max-width: 400px;
            animation: slideInRight 0.3s ease;
            cursor: pointer;
            direction: rtl;
            text-align: right;
            font-family: 'Vazirmatn', sans-serif;
        `;
        toast.onclick = function() {
            document.querySelector('.tab-btn[data-tab="notes"]')?.click();
            this.remove();
        };
        toast.innerHTML = `
            <div style="font-size: 24px;">
                <i class="bi bi-journal-text"></i>
            </div>
            <div style="flex: 1;">
                <div style="font-weight: 700; margin-bottom: 4px;">
                    <i class="bi bi-bell-fill ms-1"></i>نوت درسی جدید!
                </div>
                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px;">
                    ${note.title || 'یک نوت جدید برای این صنف اضافه شد'}
                </div>
                <div style="font-size: 12px; opacity: 0.8; line-height: 1.4;">
                    ${note.content ? note.content.substring(0, 80) + (note.content.length > 80 ? '...' : '') : ''}
                </div>
                <div style="font-size: 11px; margin-top: 8px; opacity: 0.7;">
                    <i class="bi bi-hand-index-thumb ms-1"></i>جهت مشاهده کلیک نمایید
                </div>
            </div>
            <button onclick="event.stopPropagation();this.parentElement.remove()" style="background:none;border:none;color:white;font-size:18px;cursor:pointer;padding:0;">
                <i class="bi bi-x-lg"></i>
            </button>
        `;

        toastContainer.appendChild(toast);

        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }
        }, 15000);
    }

    // Check for new notes
    function checkForNewNotes() {
        fetch('/student/courses/{{ $course->slug }}/notes')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.notes && data.notes.length > 0) {
                    const latestNote = data.notes[0];
                    
                    if (lastNoteId === 0) {
                        lastNoteId = latestNote.id;
                    } else if (latestNote.id > lastNoteId) {
                        lastNoteId = latestNote.id;
                        
                        playNotificationSound();
                        showNoteNotification(latestNote);
                        
                        if (window.refreshClassNotes) {
                            window.refreshClassNotes();
                        }
                    }
                }
            })
            .catch(() => {});
    }

    // Start polling every 10 seconds
    checkForNewNotes();
    notesPollingInterval = setInterval(checkForNewNotes, 10000);

    function showLiveClassBanner(data) {
        const banner = document.getElementById('live-class-banner');
        const btn = document.getElementById('join-class-btn');
        const info = document.getElementById('live-class-info');

        if (banner && btn) {
            // Show banner
            banner.classList.remove('d-none');

            // Enable button
            btn.classList.remove('disabled');
            // Directly use room_url for Google Meet link
            const joinUrl = data.room_url ? data.room_url : '#';
            btn.href = joinUrl;
            btn.target = '_blank';

            // Update room name if exists
            if (data.room_name && data.room_name !== 'Google Meet') {
                info.innerHTML = `Google Meet • اتاق: ${data.room_name}`;
              } else {
                info.innerHTML = `Google Meet`;
              }

            // Play sound
            try {
                const audio = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audio.createOscillator();
                const gain = audio.createGain();
                osc.connect(gain);
                gain.connect(audio.destination);
                osc.frequency.setValueAtTime(880, audio.currentTime);
                osc.frequency.setValueAtTime(1100, audio.currentTime + 0.1);
                gain.gain.setValueAtTime(0.3, audio.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, audio.currentTime + 0.4);
                osc.start();
                osc.stop(audio.currentTime + 0.4);
            } catch(e) {}

            // Show toast
            showClassStartedToast(data);
        }
    }

    function showClassStartedToast(data) {
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.style.cssText = 'position: fixed; top: 20px; left: 20px; z-index: 9999; max-width: 350px; direction: rtl;';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.style.cssText = 'background: #fff; border-right: 4px solid #22c55e; border-radius: 8px; padding: 15px; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.15); animation: slideIn 0.3s ease; direction: rtl; text-align: right; font-family: Vazirmatn, sans-serif;';
        toast.innerHTML = `
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="background: #22c55e; color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="bi bi-camera-video-fill"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #1f2937; margin-bottom: 4px;">${data.course_title}</div>
                    <p style="font-size: 13px; color: #6b7280; margin: 0 0 8px 0;">صنف زنده آغاز شد!</p>
                    <button onclick="document.getElementById('join-class-btn').click();" style="display: inline-block; background: #1f8fff; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; border: none; cursor: pointer; font-weight: 600;">
                        پیوستن به صنف <i class="bi bi-box-arrow-up-right" style="margin-right: 4px;"></i>
                    </button>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 0;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        `;

        toastContainer.appendChild(toast);
        setTimeout(() => toast.remove(), 15000);
    }

    // Fallback: Polling - Check if class started
    function checkClassStarted() {
        fetch('{{ route('student.courses.check-session', $course->slug) }}', {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const banner = document.getElementById('live-class-banner');
            // If class is active but banner is hidden, show it
            if (data.is_active && banner && banner.classList.contains('d-none')) {
                showLiveClassBanner({
                    course_title: data.course_title,
                    room_url: data.room_url,
                    room_name: data.room_name
                });
            }
        })
        .catch(err => console.log('Check class error:', err));
    }

    // Start polling every 10 seconds (fallback)
    setInterval(checkClassStarted, 10000);
    // Initial check after 2 seconds
    setTimeout(checkClassStarted, 2000);

    // ─── Join Class via AJAX ─────────────────────────────────────────────────
    window.joinClass = function(sessionId) {
        const btn = document.getElementById('join-class-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm ms-2"></span>در حال اتصال...';
        }

        fetch('{{ route('student.courses.sessions.join', $course->slug) }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.url) {
                window.open(data.url, '_blank');
            } else if (data.error) {
                alert(data.error);
            }
        })
        .catch(err => {
            console.error('Join class error:', err);
            alert('مشکلی رخ داد، لطفاً دوباره تلاش نمایید.');
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-box-arrow-up-right ms-2"></i>پیوستن به صنف';
            }
        });
    };
</script>
@endpush
