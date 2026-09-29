@extends('layouts.app')

@section('title', ($course->title ?? 'جزئیات دوره') . ' - ادورا تک')

@push('styles')
    <script>
        (function () {
            try {
                document.documentElement.dir = 'rtl';
                document.documentElement.lang = 'fa';
                var theme = localStorage.getItem('edvora_theme') || localStorage.getItem('edvora_student_theme') || 'dark';
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            } catch (e) { }
        })();
    </script>
    <link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/teacher-courses-detail.css') }}?v={{ filemtime(public_path('assets/css/teacher-courses-detail.css')) }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
    <style>
        /* Clean Top Logo Bar */
        .cd-top-logo-bar {
            padding: 1.25rem 2rem 0.5rem 2rem;
            display: flex;
            align-items: center;
        }

        @media (max-width: 991.98px) {
            .cd-top-logo-bar {
                padding: 1rem 1.25rem 0.25rem 1.25rem;
            }
        }

        .cd-top-logo-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .cd-top-logo-link:hover {
            transform: scale(1.08);
        }

        .cd-top-logo-box {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            padding: 2px;
            background: linear-gradient(135deg, #00f0ff, #0A58CA);
            box-shadow: 0 4px 16px rgba(10, 88, 202, 0.28);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .cd-top-logo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
            display: block;
        }

        /* Floating Bottom Dock for Mobile */
        .cd-mobile-dock {
            position: fixed;
            bottom: 12px;
            left: 12px;
            right: 12px;
            z-index: 1040;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            box-shadow: 0 16px 40px -10px rgba(0, 0, 0, 0.18);
            padding: 6px 8px;
            display: flex;
            align-items: center;
            justify-content: space-around;
        }

        html.dark .cd-mobile-dock {
            background: rgba(7, 19, 40, 0.94);
            border-color: rgba(6, 182, 212, 0.25);
            box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.6), 0 0 25px rgba(0, 240, 255, 0.12);
        }

        /* Elevate Ask AI trigger button on mobile above the bottom navigation dock */
        @media (max-width: 991.98px) {
            #edvoraAiTrigger,
            .edvora-ai-trigger {
                bottom: calc(84px + env(safe-area-inset-bottom, 0px)) !important;
                right: 16px !important;
                z-index: 1045 !important;
            }
            .main-content {
                padding-bottom: 7.5rem !important;
            }
            body.sidebar-open #edvoraAiTrigger,
            body.sidebar-open .edvora-ai-trigger {
                opacity: 0 !important;
                visibility: hidden !important;
                pointer-events: none !important;
            }
        }

        .cd-dock-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 6px 2px;
            color: #64748b;
            text-decoration: none;
            font-size: 0.72rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.2s ease;
            border: none;
            background: transparent;
        }

        .cd-dock-item i {
            font-size: 1.25rem;
            margin-bottom: 2px;
        }

        .cd-dock-item:hover,
        .cd-dock-item.active {
            color: #0A58CA;
            background: rgba(10, 88, 202, 0.08);
        }

        html.dark .cd-dock-item {
            color: #94a3b8;
        }

        html.dark .cd-dock-item:hover,
        html.dark .cd-dock-item.active {
            color: #00f0ff;
            background: rgba(0, 240, 255, 0.12);
        }

        .theme-switch-btn {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        html.dark .theme-switch-btn {
            border-color: rgba(6, 182, 212, 0.25);
            background: rgba(15, 23, 42, 0.8);
            color: #fbbf24;
        }

        .theme-switch-btn:hover {
            transform: scale(1.05);
        }
    </style>
@endpush

@section('hide_header', true)
@section('hide_footer', true)

@section('content')
    <div class="dashboard-wrapper">
        <x-teacher-sidebar />

        <main class="main-content">
            {{-- =========================================================================
            TOP LOGO (Replaces Header)
            ========================================================================= --}}
            <div class="cd-top-logo-bar" dir="rtl">
                <a href="{{ route('teacher.dashboard') }}" class="cd-top-logo-link" title="داشبورد ادورا تک">
                    <div class="cd-top-logo-box">
                        <img src="{{ asset('assets/images/logo1.jpg') }}" alt="ادورا تک" class="cd-top-logo-img">
                    </div>
                </a>
            </div>

            <div class="container-fluid py-4 px-3 px-xl-5">

                @php
                    $totalLessons = $course->lessons->count();
                    $totalStudents = $enrollments->count();
                    $avgProgress = $totalStudents > 0 ? round($enrollments->avg('progress_percentage')) : 0;
                    $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : ($course->rating ?? 0);

                    // avatar helper
                    $avatarUrl = function ($av, $name) {
                        if (!$av)
                            return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=1F8FFF&color=fff&size=80';
                        if (str_starts_with($av, 'http'))
                            return $av;
                        if (str_starts_with($av, '/storage/') || str_starts_with($av, 'storage/'))
                            return asset(ltrim($av, '/'));
                        return asset('storage/' . $av);
                    };

                    // Persian status and level helpers
                    $levelFa = match ($course->level) {
                        'beginner' => 'مقدماتی',
                        'intermediate' => 'متوسط',
                        'advanced' => 'پیشرفته',
                        default => 'همه سطوح'
                    };

                    $statusFa = match ($course->status) {
                        'published' => 'منتشر شده',
                        'draft' => 'پیش‌نویس',
                        'archived' => 'بایگانی شده',
                        default => 'فعال'
                    };

                    $daysFaMap = [
                        'saturday' => 'شنبه',
                        'sunday' => 'یکشنبه',
                        'monday' => 'دوشنبه',
                        'tuesday' => 'سه‌شنبه',
                        'wednesday' => 'چهارشنبه',
                        'thursday' => 'پنج‌شنبه',
                        'friday' => 'جمعه',
                    ];
                @endphp

                {{-- ── HERO ─────────────────────────────────────── --}}
                <div class="cd-hero" dir="rtl">
                    {{-- Top Bar: Category & Status Badges (Right) + AI & Export Actions (Left) --}}
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="cd-hero-badge mb-0">
                                <i class="bi bi-tag-fill me-1"></i>
                                {{ $course->category?->name ?? 'دسته‌بندی نشده' }}
                            </div>
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5"
                                style="background:{{ $course->status === 'published' ? 'rgba(16,185,129,0.18)' : 'rgba(245,158,11,0.18)' }};color:{{ $course->status === 'published' ? '#34d399' : '#fbbf24' }};border:1px solid {{ $course->status === 'published' ? 'rgba(16,185,129,0.3)' : 'rgba(245,158,11,0.3)' }};font-size:0.75rem;">
                                <span class="d-inline-block rounded-circle" style="width:7px;height:7px;background:currentColor;"></span>
                                {{ $statusFa }}
                            </span>
                        </div>

                        {{-- AI & PDF Actions --}}
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" id="btnGeminiTranslate"
                                onclick="translateCourseDetailsWithGemini()"
                                class="btn rounded-pill fw-bold px-3 py-1.5 d-inline-flex align-items-center gap-1.5"
                                style="background: linear-gradient(135deg, rgba(0, 240, 255, 0.15), rgba(99, 102, 241, 0.25)); color: #00f0ff; border: 1px solid rgba(0, 240, 255, 0.4); font-size: .82rem; box-shadow: 0 0 15px rgba(0, 240, 255, 0.15);"
                                title="ترجمه هوشمند عنوان و توضیحات دوره با هوش مصنوعی جمنای">
                                <i class="bi bi-stars" style="color:#00f0ff;"></i>
                                <span id="btnGeminiTranslateText">ترجمه با هوش مصنوعی (Gemini)</span>
                            </button>
                            <a href="{{ route('teacher.courses.export-pdf', $course->id) }}"
                                class="btn rounded-pill fw-bold px-3 py-1.5 d-inline-flex align-items-center gap-1.5"
                                style="background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;border:none;font-size:.82rem;box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);">
                                <i class="bi bi-file-earmark-pdf-fill"></i>دریافت گزارش PDF
                            </a>
                        </div>
                    </div>

                    {{-- Title & Description --}}
                    <h1 class="cd-title mb-2" id="courseHeroTitle">{{ $course->title }}</h1>
                    <div class="cd-desc mb-3" id="courseHeroDesc">{!! nl2br(strip_tags($course->description)) !!}</div>

                    {{-- Meta Chips Row --}}
                    <div class="cd-meta-row">
                        <div class="cd-meta-item">
                            <div class="cd-meta-icon" style="background:rgba(31,143,255,0.15);">
                                <i class="bi bi-calendar-check" style="color:#6ab4ff;"></i>
                            </div>
                            <div>
                                <span class="cd-meta-label">تاریخ ایجاد</span>
                                <span class="cd-meta-val">{{ $course->created_at->format('Y/m/d') }}</span>
                            </div>
                        </div>
                        <div class="cd-meta-item">
                            <div class="cd-meta-icon" style="background:rgba(251,191,36,0.15);">
                                <i class="bi bi-hourglass-split" style="color:#fbbf24;"></i>
                            </div>
                            <div>
                                <span class="cd-meta-label">مدت دوره</span>
                                <span class="cd-meta-val">{{ $course->duration_hours }} ساعت</span>
                            </div>
                        </div>
                        <div class="cd-meta-item">
                            <div class="cd-meta-icon" style="background:rgba(16,185,129,0.15);">
                                <i class="bi bi-bar-chart-fill" style="color:#10b981;"></i>
                            </div>
                            <div>
                                <span class="cd-meta-label">سطح آموزش</span>
                                <span class="cd-meta-val">{{ $levelFa }}</span>
                            </div>
                        </div>
                        @if($course->start_date || $course->end_date)
                            <div class="cd-meta-item">
                                <div class="cd-meta-icon" style="background:rgba(168,85,247,0.15);">
                                    <i class="bi bi-calendar-range" style="color:#c084fc;"></i>
                                </div>
                                <div>
                                    <span class="cd-meta-label">بازه زمانی برگزاری</span>
                                    <span class="cd-meta-val">
                                        @if($course->start_date && $course->end_date)
                                            {{ $course->start_date->format('Y/m/d') }} الی {{ $course->end_date->format('Y/m/d') }}
                                        @elseif($course->start_date)
                                            از {{ $course->start_date->format('Y/m/d') }}
                                        @else
                                            تا {{ $course->end_date->format('Y/m/d') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Bottom Row: Schedule & Action Controls --}}
                    <div class="row align-items-center g-3 mt-1 pt-3" style="border-top:1px solid rgba(255,255,255,0.08);">
                        <div class="{{ $course->primary_class_start ? 'col-lg-7' : 'col-12' }}">
                            @if($course->primary_class_start)
                                <div class="cd-schedule-box mt-0">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="schedule-col-title" style="color:#6ab4ff;">
                                                <i class="bi bi-clock me-1"></i>ساعت برگزاری کلاس اصلی
                                            </div>
                                            <div class="schedule-time" dir="ltr">
                                                {{ substr($course->primary_class_start, 0, 5) }}
                                                <span style="color:rgba(255,255,255,.4);font-size:0.9rem;font-weight:400;margin:0 6px;">تا</span>
                                                {{ substr($course->primary_class_end, 0, 5) }}
                                            </div>
                                            @if($course->primary_class_days)
                                                <div class="mt-1">
                                                    @foreach($course->primary_class_days as $day)
                                                        @php $dKey = strtolower($day); @endphp
                                                        <span class="day-badge" style="background:rgba(31,143,255,0.2);color:#6ab4ff;">{{ $daysFaMap[$dKey] ?? $day }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if($course->primary_class_note)
                                                <p style="color:rgba(255,255,255,.65);font-size:.78rem;margin-top:4px;margin-bottom:0;">
                                                    {{ $course->primary_class_note }}</p>
                                            @endif
                                        </div>
                                        @if($course->secondary_class_start)
                                            <div class="col-sm-6" style="border-right:1px solid rgba(255,255,255,.1);padding-right:1rem;">
                                                <div class="schedule-col-title" style="color:#fbbf24;">
                                                    <i class="bi bi-clock-history me-1"></i>ساعت کلاس جبرانی / رزرو
                                                </div>
                                                <div class="schedule-time" dir="ltr">
                                                    {{ substr($course->secondary_class_start, 0, 5) }}
                                                    <span style="color:rgba(255,255,255,.4);font-size:0.9rem;font-weight:400;margin:0 6px;">تا</span>
                                                    {{ substr($course->secondary_class_end, 0, 5) }}
                                                </div>
                                                @if($course->secondary_class_days)
                                                    <div class="mt-1">
                                                        @foreach($course->secondary_class_days as $day)
                                                            @php $dKey = strtolower($day); @endphp
                                                            <span class="day-badge" style="background:rgba(251,191,36,0.2);color:#fbbf24;">{{ $daysFaMap[$dKey] ?? $day }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if($course->secondary_class_note)
                                                    <p style="color:rgba(255,255,255,.65);font-size:.78rem;margin-top:4px;margin-bottom:0;">
                                                        {{ $course->secondary_class_note }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="{{ $course->primary_class_start ? 'col-lg-5 text-lg-end' : 'col-12 text-center' }} d-flex align-items-center justify-content-lg-end justify-content-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-light rounded-pill px-3 py-2 fw-bold shadow-sm"
                                onclick="if(window.switchCourseTab){switchCourseTab('referrals');}document.getElementById('tab-referrals')?.scrollIntoView({behavior:'smooth'});"
                                title="دعوت از دانشجویان از طریق لینک اختصاصی">
                                <i class="bi bi-person-plus-fill me-1 text-success"></i>
                                <span>دعوت از دانشجویان</span>
                            </button>
                            <button type="button" id="toggleEnrollmentBtn"
                                class="btn {{ $course->is_enrollment_closed ? 'btn-outline-warning' : 'btn-outline-light' }} rounded-pill px-3 py-2 fw-bold shadow-sm"
                                data-url="{{ route('teacher.courses.toggle-enrollment', $course->id) }}"
                                onclick="toggleCourseEnrollment(this)">
                                <i class="bi {{ $course->is_enrollment_closed ? 'bi-lock-fill me-1 text-warning' : 'bi-unlock-fill me-1' }}"></i>
                                <span id="enrollmentStatusText">{{ $course->is_enrollment_closed ? 'ثبت‌نام بسته است' : 'بستن ثبت‌نام دوره' }}</span>
                            </button>
                            @if($activeSession)
                                <a href="#live-class-panel" class="btn rounded-pill px-4 py-2 fw-bold shadow-sm"
                                    style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;">
                                    <span class="d-inline-block me-2" style="width:8px;height:8px;background:#fff;border-radius:50%;animation:livePulse 1.2s ease-in-out infinite;"></span>
                                    ورود به کلاس آنلاین
                                </a>
                            @else
                                <a href="#live-class-panel" class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-camera-video-fill me-2 text-primary"></i>شروع جلسه جدید
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── STAT CARDS ───────────────────────────────── --}}
                <div class="row g-3 g-md-4 mb-4" dir="rtl">
                    <div class="col-6 col-xl-3">
                        <div class="cd-stat-card">
                            <div class="cd-stat-icon" style="background:#eff6ff;">
                                <i class="bi bi-book-fill" style="color:#1F8FFF;"></i>
                            </div>
                            <div>
                                <div class="cd-stat-num">{{ $totalLessons }}</div>
                                <div class="cd-stat-lbl">کل درس‌ها و جلسات</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="cd-stat-card">
                            <div class="cd-stat-icon" style="background:#f0fdf4;">
                                <i class="bi bi-people-fill" style="color:#10b981;"></i>
                            </div>
                            <div>
                                <div class="cd-stat-num">{{ $totalStudents }}</div>
                                <div class="cd-stat-lbl">دانشجویان ثبت‌نام شده</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="cd-stat-card">
                            <div class="cd-stat-icon" style="background:#fffbeb;">
                                <i class="bi bi-star-fill" style="color:#f59e0b;"></i>
                            </div>
                            <div>
                                <div class="cd-stat-num">{{ number_format($avgRating, 1) }}</div>
                                <div class="cd-stat-lbl">میانگین امتیاز دوره</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="cd-stat-card">
                            <div class="cd-stat-icon" style="background:#f5f3ff;">
                                <i class="bi bi-graph-up-arrow" style="color:#8b5cf6;"></i>
                            </div>
                            <div>
                                <div class="cd-stat-num">{{ $avgProgress }}%</div>
                                <div class="cd-stat-lbl">میانگین پیشرفت</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── TABS BAR (Responsive Touch-Scrollable Navigation) ────────────────── --}}
                <div class="cd-tabs mb-4" id="cdTabs" dir="rtl">
                    <button class="cd-tab-btn active" data-tab="syllabus" onclick="switchCourseTab('syllabus',this)">
                        <i class="bi bi-list-ol"></i> سرفصل‌ها
                        <span class="badge rounded-pill ms-1"
                            style="background:#e0f2fe;color:#0369a1;font-size:.7rem;">{{ $totalLessons }}</span>
                    </button>
                    <button class="cd-tab-btn" data-tab="students" onclick="switchCourseTab('students',this)">
                        <i class="bi bi-shield-check"></i> دانشجویان و دسترسی
                        <span class="badge rounded-pill ms-1"
                            style="background:#dcfce7;color:#15803d;font-size:.7rem;">{{ $totalStudents }}</span>
                    </button>
                    <button class="cd-tab-btn" data-tab="referrals" onclick="switchCourseTab('referrals',this)">
                        <i class="bi bi-link-45deg text-success"></i> لینک دعوت و رفرال
                        <span class="badge rounded-pill ms-1"
                            style="background:#dcfce7;color:#15803d;font-size:.7rem;">{{ $referralRecords->count() }}</span>
                    </button>
                    <button class="cd-tab-btn" data-tab="points" onclick="switchCourseTab('points',this)">
                        <i class="bi bi-star-fill text-warning"></i> سیستم نمرات
                    </button>
                    <button class="cd-tab-btn" data-tab="reviews" onclick="switchCourseTab('reviews',this)">
                        <i class="bi bi-star"></i> نظرات
                        <span class="badge rounded-pill ms-1"
                            style="background:#fef9c3;color:#a16207;font-size:.7rem;">{{ $reviews->count() }}</span>
                    </button>
                    <button class="cd-tab-btn" data-tab="documents" onclick="switchCourseTab('documents',this)">
                        <i class="bi bi-folder2-open"></i> اسناد و جزوات
                        <span class="badge rounded-pill ms-1"
                            style="background:#e0f2fe;color:#0369a1;font-size:.7rem;">{{ $documents->count() }}</span>
                    </button>
                    <button class="cd-tab-btn" data-tab="history" onclick="switchCourseTab('history',this)">
                        <i class="bi bi-clock-history"></i> تاریخچه جلسات
                        <span class="badge rounded-pill ms-1"
                            style="background:#ede9fe;color:#6d28d9;font-size:.7rem;">{{ $pastSessions->count() }}</span>
                    </button>
                    <button class="cd-tab-btn" data-tab="attendance" onclick="switchCourseTab('attendance',this)">
                        <i class="bi bi-person-check"></i> حضور و غیاب
                        <span class="badge rounded-pill ms-1"
                            style="background:#fef3c7;color:#92400e;font-size:.7rem;">{{ $totalStudents }}</span>
                    </button>
                    <button class="cd-tab-btn" data-tab="classnotes" onclick="switchCourseTab('classnotes',this)">
                        <i class="bi bi-journal-text"></i> یادداشت‌های کلاسی
                        <span class="badge rounded-pill ms-1"
                            style="background:#e0f2fe;color:#0369a1;font-size:.7rem;">{{ $classNotes->count() }}</span>
                    </button>
                    <button class="cd-tab-btn" data-tab="chat" onclick="switchCourseTab('chat',this)">
                        <i class="bi bi-chat-heart"></i> چت صنف
                        <span class="chat-unread-badge ms-1" style="display:none;"
                            data-chat-badge-course="{{ $course->id }}"></span>
                    </button>
                    <button class="cd-tab-btn" data-tab="curriculum" onclick="switchCourseTab('curriculum',this)">
                        <i class="bi bi-pencil-square"></i> ویرایش سیلابس
                        <span class="badge rounded-pill ms-1"
                            style="background:#ede9fe;color:#6d28d9;font-size:.7rem;">{{ $totalLessons }}</span>
                    </button>
                </div>

                {{-- SYLLABUS --}}
                <div class="cd-tab-pane active" id="tab-syllabus" dir="rtl">

                    {{-- Alerts --}}
                    @if(session('session_success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('session_success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('session_warning'))
                        <div class="alert alert-warning alert-dismissible fade show rounded-3 mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('session_warning') }}
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
                                        <i class="bi bi-camera-video-fill me-2" style="color:#1F8FFF;"></i>کلاس آنلاین زنده
                                    </h6>
                                </div>
                                <div class="cd-card-body">

                                    @if($activeSession)
                                        {{-- ACTIVE SESSION - Clean Simple Design --}}

                                        {{-- LIVE Banner --}}
                                        <div
                                            style="background: linear-gradient(135deg, #86efac, #bbf7d0); border: 2px solid #4ade80; border-radius: 12px; padding: 15px; text-align: center; margin-bottom: 15px;">
                                            <span
                                                style="display: inline-block; width: 12px; height: 12px; background: #22c55e; border-radius: 50%; margin-left: 8px; animation: pulse 2s infinite;"></span>
                                            <strong style="color: #166534; font-size: 16px;">کلاس در حال برگزاری است</strong>
                                        </div>

                                        {{-- Session Info Grid --}}
                                        <div
                                            style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                                            {{-- Started --}}
                                            <div class="cd-card p-3 text-center" style="border-radius:10px;">
                                                <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">
                                                    <i class="bi bi-play-fill" style="color: #22c55e;"></i> زمان شروع
                                                </div>
                                                <div style="font-weight: 600; font-size: 18px;" dir="ltr">
                                                    {{ $activeSession->started_at->format('H:i') }}
                                                </div>
                                                <div style="color: #94a3b8; font-size: 11px;">
                                                    {{ $activeSession->started_at->format('Y/m/d') }}
                                                </div>
                                            </div>

                                            {{-- Duration --}}
                                            <div class="cd-card p-3 text-center" style="border-radius:10px;">
                                                <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">
                                                    <i class="bi bi-clock" style="color: #3b82f6;"></i> مدت کلاس
                                                </div>
                                                <div style="font-weight: 700; color: #16a34a; font-size: 20px; font-family: monospace; letter-spacing: 2px;"
                                                    id="sessionDuration" dir="ltr">
                                                    00:00:00
                                                </div>
                                                <div style="color: #94a3b8; font-size: 11px;">در حال اجرا</div>
                                            </div>
                                        </div>

                                        {{-- Participants --}}
                                        <div class="cd-card p-3 text-center mb-3"
                                            style="border-radius:10px; border-color:rgba(14,165,233,0.3);">
                                            <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">
                                                <i class="bi bi-people" style="color: #0ea5e9;"></i> دانشجویان حاضر
                                            </div>
                                            <div style="font-weight: 700; color: #0284c7; font-size: 28px;"
                                                id="liveParticipantCount">
                                                {{ $activeSessionParticipants }}
                                            </div>
                                            <div style="color: #64748b; font-size: 11px;">ورود از طریق لینک سامانه ادورا</div>
                                        </div>

                                        {{-- Room Name --}}
                                        @if($activeSession->room_name)
                                            <div class="cd-card p-2 text-center mb-3" style="border-radius:8px;">
                                                <div style="color: #64748b; font-size: 11px; margin-bottom: 4px;">
                                                    <i class="bi bi-door-open me-1"></i> نام اتاق جلسه
                                                </div>
                                                <code
                                                    style="font-size: 11px; padding: 4px 8px; border-radius: 4px;">{{ $activeSession->room_name }}</code>
                                            </div>
                                        @endif

                                        {{-- Join Button --}}
                                        <a href="{{ $activeSession->meet_link }}" target="_blank"
                                            style="display: block; background: linear-gradient(135deg, #0A58CA, #0284c7); color: white; text-decoration: none; padding: 14px; border-radius: 10px; text-align: center; font-weight: 600; margin-bottom: 12px; box-shadow: 0 4px 15px rgba(10, 88, 202, 0.3);">
                                            <i class="bi bi-box-arrow-up-right me-2"></i>ورود به جلسه گوگل میت (مدرس)
                                        </a>

                                        {{-- Features --}}
                                        <div
                                            style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 8px; padding: 12px; margin-bottom: 15px;">
                                            <small style="color: #10b981;">
                                                <i class="bi bi-shield-check me-1"></i>
                                                دسترسی‌های کامل: اشتراک صفحه، صدا و تصویر و مدیریت شرکت‌کنندگان
                                            </small>
                                        </div>

                                        {{-- 3-Minute Auto-Cancellation Status Box --}}
                                        <div id="autoCloseWarning"
                                            style="display: {{ $activeSessionParticipants == 0 ? 'block' : 'none' }}; background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); border-radius: 10px; padding: 12px; margin-bottom: 15px;">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span style="color: #d97706; font-weight: 700; font-size: 0.82rem;">
                                                    <i class="bi bi-hourglass-split me-1 text-warning"></i> در انتظار ورود
                                                    دانشجویان
                                                </span>
                                                <span id="autoCloseCountdownBadge"
                                                    class="badge bg-warning text-dark font-monospace"
                                                    style="font-size: 0.85rem; padding: 3px 8px;">
                                                    03:00
                                                </span>
                                            </div>
                                            <small id="autoCloseMessage"
                                                style="color: #b45309; display: block; font-size: 0.76rem; line-height: 1.4;">
                                                در صورتی که هیچ دانشجویی تا ۳ دقیقه وارد نشود، کلاس به صورت خودکار لغو شده و درس
                                                مربوطه تکمیل نخواهد شد.
                                            </small>
                                            <div class="progress mt-2"
                                                style="height: 4px; background: rgba(245,158,11,0.2); border-radius: 4px;">
                                                <div id="autoCloseProgressBar"
                                                    class="progress-bar bg-warning progress-bar-striped progress-bar-animated"
                                                    style="width: 100%; transition: width 1s linear;"></div>
                                            </div>
                                        </div>

                                        {{-- Student Joined Success Box --}}
                                        <div id="studentJoinedBox"
                                            style="display: {{ $activeSessionParticipants > 0 ? 'block' : 'none' }}; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 10px; padding: 12px; margin-bottom: 15px;">
                                            <div class="d-flex align-items-center"
                                                style="color: #10b981; font-size: 0.82rem; font-weight: 600;">
                                                <i class="bi bi-check-circle-fill me-2 text-success"
                                                    style="font-size: 1.1rem;"></i>
                                                <span>دانشجو وارد شد! کلاس رسماً فعال گردید.</span>
                                            </div>
                                        </div>

                                        <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0;">

                                        {{-- End Session Form --}}
                                        <form method="POST" action="{{ route('teacher.courses.sessions.end', $course->id) }}">
                                            @csrf
                                            <input type="hidden" name="attendees_count"
                                                value="{{ $activeSession->participants_count ?? 0 }}">

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold" style="font-size: 13px;">
                                                    <i class="bi bi-chat-left-text me-1 text-muted"></i>یادداشت این جلسه
                                                    (اختیاری)
                                                </label>
                                                <textarea name="note" rows="2" class="form-control rounded-3"
                                                    placeholder="مثال: فصل ۳ و ۴ تدریس شد..."></textarea>
                                            </div>

                                            <button type="submit" class="btn btn-outline-danger w-100 rounded-3 py-2 fw-bold"
                                                onclick="return confirm('آیا از پایان دادن به این جلسه اطمینان دارید؟')">
                                                <i class="bi bi-stop-circle me-1"></i>پایان دادن به جلسه کلاس
                                            </button>
                                        </form>

                                    @else
                                        {{-- No Active Session State --}}
                                        <div class="text-center mb-4">
                                            <div class="live-class-icon-wrapper"
                                                style="width:60px;height:60px;border-radius:18px;background:linear-gradient(135deg,#0A58CA,#00f0ff);display:flex;align-items:center;justify-content:center;margin:0 auto 12px auto;box-shadow:0 8px 20px rgba(10,88,202,0.3);">
                                                <i class="bi bi-camera-video-fill" style="font-size:1.8rem;color:#fff;"></i>
                                            </div>
                                            <h6 class="live-class-title fw-bold">شروع کلاس آنلاین گوگل میت</h6>
                                            <p class="live-class-description text-muted small">
                                                لینک اختصاصی Google Meet خود را وارد کرده و جلسه را شروع نمایید.
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
                                                <div class="mb-3 text-end">
                                                    <label for="lesson_id" class="form-label small fw-bold">
                                                        <i class="bi bi-book me-1 text-primary"></i>انتخاب درس برای این جلسه
                                                    </label>
                                                    <select name="lesson_id" id="lesson_id" class="form-select rounded-3" required>
                                                        <option value="" disabled selected>-- درس مورد نظر را انتخاب کنید --
                                                        </option>
                                                        @foreach($availableLessons as $lesson)
                                                            <option value="{{ $lesson->id }}">
                                                                {{ $lesson->order ?: $loop->iteration }}. {{ $lesson->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted" style="font-size:0.75rem;">دانشجویان در پنل خود درس
                                                        مربوطه را مشاهده خواهند کرد.</small>
                                                </div>
                                                <div class="mb-3 text-end">
                                                    <label for="meet_link" class="form-label small fw-bold">
                                                        <i class="bi bi-link-45deg me-1 text-primary"></i>لینک گوگل میت (Google
                                                        Meet)
                                                    </label>
                                                    <input type="text" name="meet_link" id="meet_link"
                                                        class="form-control rounded-3" dir="ltr" required
                                                        placeholder="https://meet.google.com/xxx-xxxx-xxx">
                                                </div>
                                                <button type="submit" class="btn w-100 rounded-3 py-2 fw-bold text-white shadow-sm"
                                                    style="background:linear-gradient(135deg,#0A58CA,#0284c7);border:none;">
                                                    <i class="bi bi-camera-video-fill me-2"></i>شروع جلسه جدید کلاس
                                                </button>
                                            </form>
                                        @else
                                            <div class="alert alert-success border-0 rounded-3 text-center">
                                                <i class="bi bi-check-circle-fill me-2"></i>تمامی درس‌های این دوره تدریس شده‌اند!
                                            </div>
                                        @endif

                                        <div class="p-2.5 rounded-3 mt-3 text-center" style="background:rgba(10,88,202,0.06);">
                                            <small class="text-muted" style="font-size:0.75rem;">
                                                <i class="bi bi-info-circle me-1 text-primary"></i>
                                                لطفاً قبل از شروع، از صحت لینک Google Meet مطمئن شوید.
                                            </small>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                        {{-- ── Lessons List ── --}}
                        <div class="col-lg-8">
                            <div class="cd-card">
                                <div
                                    class="cd-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <h6 class="cd-card-title mb-0">
                                        <i class="bi bi-list-ol me-2 text-primary"></i>فهرست جلسات دوره
                                        ({{ $totalLessons }})
                                    </h6>
                                    @if(isset($completedLessonsCount) && $completedLessonsCount > 0)
                                        <span class="badge bg-success rounded-pill px-3 py-1.5">{{ $completedLessonsCount }}
                                            جلسه تدریس شده</span>
                                    @endif
                                </div>
                                <div class="cd-card-body">
                                    {{-- Progress Bar --}}
                                    @if(isset($progressPercent))
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small class="fw-bold">پیشرفت دوره</small>
                                                <small class="fw-bold text-primary">{{ $progressPercent }}%</small>
                                            </div>
                                            <div class="progress rounded-pill" style="height:8px;">
                                                <div class="progress-bar bg-success rounded-pill" role="progressbar"
                                                    style="width: {{ $progressPercent }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                    @forelse($course->lessons as $lesson)
                                        @php
                                            $isCompleted = isset($completedLessonIds) && in_array($lesson->id, $completedLessonIds);
                                        @endphp
                                        <div class="lesson-row"
                                            style="flex-wrap:wrap; {{ $isCompleted ? 'border-right:3px solid #10b981;' : '' }}">
                                            <div class="lesson-num"
                                                style="{{ $isCompleted ? 'background:#10b981;color:#fff;' : '' }}">
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
                                                        <span
                                                            class="badge bg-success-subtle text-success border border-success-subtle ms-2"
                                                            style="font-size:0.68rem;">تدریس شده</span>
                                                    @endif
                                                </div>
                                                @if($lesson->description)
                                                    <div class="lesson-desc mt-1">{{ Str::limit($lesson->description, 120) }}</div>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                                @if($lesson->duration_minutes)
                                                    <div class="lesson-dur"><i
                                                            class="bi bi-clock me-1"></i>{{ $lesson->duration_minutes }} دقیقه</div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="cd-empty text-center py-5">
                                            <i class="bi bi-journal-x fs-1 text-muted"></i>
                                            <p class="mt-2 text-muted">هنوز درسی برای این دوره ثبت نشده است.</p>
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
                    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3" dir="rtl">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="bi bi-shield-check me-2 text-success"></i>مدیریت دانشجویان و
                                کنترل دسترسی</h5>
                            <p class="text-muted small mb-0">مدیریت دانشجویان عضو این دوره و اعمال محدودیت یا رفع مسدودیت
                                دسترسی به کلاس و محتوا.</p>
                        </div>
                        @if($totalStudents > 0)
                            <input type="text" id="studentSearch" class="form-control rounded-pill" style="max-width:260px;"
                                placeholder="&#128269; جستجوی دانشجو...">
                        @endif
                    </div>

                    @php
                        $bannedCount = $enrollments->where('status', 'banned')->count();
                        $activeCount = $enrollments->where('status', '!=', 'banned')->count();
                    @endphp

                    {{-- Quick stats --}}
                    <div class="row g-3 mb-4" dir="rtl">
                        <div class="col-6 col-md-3">
                            <div class="att-stat-card cd-card" style="border-right:4px solid #10b981;">
                                <div class="att-stat-num text-success">{{ $activeCount }}</div>
                                <div class="att-stat-lbl text-muted">دانشجویان فعال</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="att-stat-card cd-card" style="border-right:4px solid #ef4444;">
                                <div class="att-stat-num text-danger">{{ $bannedCount }}</div>
                                <div class="att-stat-lbl text-muted">مسدود شده</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="att-stat-card cd-card" style="border-right:4px solid #0A58CA;">
                                <div class="att-stat-num text-primary">{{ $totalStudents }}</div>
                                <div class="att-stat-lbl text-muted">کل ثبت‌نامی‌ها</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="att-stat-card cd-card" style="border-right:4px solid #f59e0b;">
                                <div class="att-stat-num text-warning">
                                    {{ $totalStudents > 0 ? round(($activeCount / $totalStudents) * 100) : 0 }}%</div>
                                <div class="att-stat-lbl text-muted">نرخ دسترسی فعال</div>
                            </div>
                        </div>
                    </div>

                    {{-- Students grid --}}
                    @if($enrollments->count() > 0)
                        <div class="row g-3" id="studentsGrid" dir="rtl">
                            @foreach($enrollments as $en)
                                @php
                                    $enAvatar = $avatarUrl($en->avatar, $en->name);
                                    $prog = (int) ($en->progress_percentage ?? 0);
                                    $isBanned = ($en->status === 'banned');
                                @endphp
                                <div class="col-md-6 col-xl-4 student-item">
                                    <div class="cd-card h-100" style="{{ $isBanned ? 'border:1.5px solid #fecaca;' : '' }}">
                                        <div class="cd-card-body p-3">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div style="position:relative;flex-shrink:0;">
                                                    <img src="{{ $enAvatar }}" alt="{{ $en->name }}"
                                                        style="width:52px;height:52px;border-radius:50%;object-fit:cover;{{ $isBanned ? 'filter:grayscale(1);opacity:.6;' : '' }}">
                                                    @if($isBanned)
                                                        <span
                                                            style="position:absolute;bottom:-2px;right:-2px;background:#ef4444;color:#fff;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;font-size:.65rem;border:2px solid #fff;">
                                                            <i class="bi bi-slash-lg"></i>
                                                        </span>
                                                    @else
                                                        <span
                                                            style="position:absolute;bottom:-2px;right:-2px;background:#10b981;color:#fff;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;font-size:.65rem;border:2px solid #fff;">
                                                            <i class="bi bi-check-lg"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="fw-bold text-truncate" style="font-size:.95rem;">{{ $en->name }}
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 mt-1">
                                                        @if($isBanned)
                                                            <span class="badge rounded-pill px-2 py-1 bg-danger-subtle text-danger"
                                                                style="font-size:.7rem;"><i
                                                                    class="bi bi-slash-circle me-1"></i>مسدود</span>
                                                        @else
                                                            <span class="badge rounded-pill px-2 py-1 bg-success-subtle text-success"
                                                                style="font-size:.7rem;"><i
                                                                    class="bi bi-check-circle me-1"></i>فعال</span>
                                                        @endif
                                                        <span class="text-muted" style="font-size:.72rem;">عضویت
                                                            {{ \Carbon\Carbon::parse($en->enrolled_at)->format('Y/m/d') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Progress bar --}}
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span style="font-size:.75rem;" class="text-muted">پیشرفت در دوره</span>
                                                    <span style="font-size:.75rem;font-weight:700;"
                                                        class="{{ $isBanned ? 'text-danger' : 'text-primary' }}">{{ $prog }}%</span>
                                                </div>
                                                <div
                                                    style="height:6px;background:rgba(255,255,255,0.1);border-radius:99px;overflow:hidden;">
                                                    <div
                                                        style="height:100%;width:{{ $prog }}%;border-radius:99px;background:{{ $isBanned ? '#ef4444' : ($prog >= 80 ? '#10b981' : ($prog >= 40 ? '#f59e0b' : '#0A58CA')) }};transition:width .4s;">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Action button --}}
                                            @if($isBanned)
                                                <form method="POST"
                                                    action="{{ route('teacher.courses.students.unban', [$course->id, $en->id]) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn w-100 fw-semibold rounded-3 py-2 btn-outline-success"
                                                        style="font-size:.82rem;"
                                                        onclick="return confirm('آیا دسترسی مجدد به دانشجو اعطا شود؟')">
                                                        <i class="bi bi-person-check-fill me-2"></i>اعطای مجدد دسترسی
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST"
                                                    action="{{ route('teacher.courses.students.ban', [$course->id, $en->id]) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn w-100 fw-semibold rounded-3 py-2 btn-outline-danger"
                                                        style="font-size:.82rem;"
                                                        onclick="return confirm('آیا از مسدودسازی دسترسی این دانشجو اطمینان دارید؟ دسترسی به دوره بلافاصله لغو خواهد شد.')">
                                                        <i class="bi bi-slash-circle me-2"></i>مسدودسازی دسترسی
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
                            <div class="cd-card-body text-center py-5">
                                <div class="cd-empty">
                                    <i class="bi bi-person-x fs-1 text-muted"></i>
                                    <p class="mt-2 text-muted">هنوز هیچ دانشجویی در این دوره ثبت‌نام نکرده است.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- STUDENT SCORING & GAMIFICATION --}}
                {{-- STUDENT SCORING & GAMIFICATION --}}
                <div class="cd-tab-pane" id="tab-points" dir="rtl">
                    @if(session('points_success'))
                        <div class="alert alert-success border-0 rounded-4 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4"
                            role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span class="fw-medium">{{ session('points_success') }}</span>
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Quick Points Adjustment Form -->
                    <div class="cd-card mb-4">
                        <div class="cd-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h6 class="cd-card-title mb-0"><i class="bi bi-plus-slash-minus me-2 text-primary"></i>ثبت و
                                تنظیم امتیاز دانشجو</h6>
                            <span class="text-muted small">اعطا یا کسر امتیاز و تجربه (XP) دانشجو به صورت مستقیم</span>
                        </div>
                        <div class="cd-card-body p-4">
                            <form action="{{ route('teacher.courses.points.store', $course->id) }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">انتخاب دانشجو</label>
                                        <select name="user_id" class="form-select rounded-3 shadow-none" required>
                                            <option value="">دانشجوی مورد نظر را انتخاب کنید...</option>
                                            @foreach($enrollments as $en)
                                                <option value="{{ $en->id }}" {{ old('user_id') == $en->id ? 'selected' : '' }}>
                                                    {{ $en->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">میزان امتیاز (+ یا -)</label>
                                        <input type="number" name="amount" class="form-control rounded-3 shadow-none"
                                            placeholder="مثال: 10 یا -5" required>
                                        <span class="text-muted" style="font-size: 0.72rem;">عدد مثبت اضافه و عدد منفی کسر
                                            می‌کند.</span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">دسته‌بندی امتیاز</label>
                                        <select name="type" class="form-select rounded-3 shadow-none">
                                            @if(isset($scoringRules))
                                                @foreach($scoringRules->groupBy('type') as $type => $group)
                                                    <optgroup label="{{ ucfirst($type) }}">
                                                        @foreach($group as $rule)
                                                            <option value="{{ $rule->action_name }}">{{ $rule->label }}
                                                                ({{ $rule->default_score >= 0 ? '+' : '' }}{{ $rule->default_score }})
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            @endif
                                            <option value="manual">ثبت دستی امتیاز سفارشی</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit"
                                            class="btn btn-primary w-100 rounded-3 py-2 fw-semibold shadow-sm">
                                            <i class="bi bi-check-lg me-1"></i> اعمال امتیاز
                                        </button>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">دلیل / یادداشت برای دانشجو</label>
                                        <input type="text" name="reason" class="form-control rounded-3 shadow-none"
                                            placeholder="مثال: مشارکت عالی در جلسه کلاس آنلاین و حل تمرینات" required
                                            maxlength="500">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Students Overview Table -->
                    <div class="cd-card mb-4">
                        <div class="cd-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h6 class="cd-card-title mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>نمرات و
                                رتبه‌بندی دانشجویان دوره</h6>
                            <span
                                class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">{{ $enrollments->count() }}
                                دانشجو</span>
                        </div>
                        <div class="p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light text-muted" style="font-size: 0.78rem;">
                                        <tr>
                                            <th class="ps-4 py-3">دانشجو</th>
                                            <th class="py-3">مجموع امتیاز</th>
                                            <th class="py-3">کسب شده</th>
                                            <th class="py-3">کسر شده</th>
                                            <th class="py-3">رتبه</th>
                                            <th class="pe-4 py-3 text-end">سوابق</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($enrollments as $en)
                                            @php
                                                $studentModel = \App\Models\User::find($en->id);
                                                $earned = $studentModel ? $studentModel->earnedPoints() : 0;
                                                $deducted = $studentModel ? $studentModel->deductedPoints() : 0;
                                                $total = $studentModel ? $studentModel->totalScore() : 0;
                                                $rank = $studentModel ? $studentModel->leaderboardRank($course->id) : null;
                                            @endphp
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="{{ $avatarUrl($en->avatar, $en->name) }}"
                                                            class="rounded-circle shadow-sm" width="38" height="38"
                                                            style="object-fit:cover"
                                                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($en->name) }}&background=1f8fff&color=fff'">
                                                        <div>
                                                            <div class="fw-bold small">{{ $en->name }}</div>
                                                            <small class="text-muted" style="font-size: 0.75rem;">عضویت
                                                                {{ \Carbon\Carbon::parse($en->enrolled_at)->diffForHumans() }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $total >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} px-3 py-1 rounded-pill fw-bold fs-6">
                                                        {{ number_format($total) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-success fw-semibold">+{{ number_format($earned) }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-danger fw-semibold">-{{ number_format($deducted) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-warning bg-opacity-15 text-dark px-3 py-1 rounded-pill fw-bold">
                                                        #{{ $rank ?? '—' }}
                                                    </span>
                                                </td>
                                                <td class="pe-4 text-end">
                                                    <a href="{{ route('teacher.courses.points.student', [$course->id, $en->id]) }}"
                                                        class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                                        <i class="bi bi-clock-history me-1"></i> سوابق
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">
                                                    <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>
                                                    <p class="small mb-0">هنوز دانشجویی در این دوره ثبت‌نام نکرده است.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Point Changes Table -->
                    <div class="cd-card mb-4">
                        <div class="cd-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h6 class="cd-card-title mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>گزارش آخرین
                                تغییرات امتیازات</h6>
                            <a href="{{ route('scoring.help') }}" class="btn btn-sm btn-link text-decoration-none">راهنمای
                                سیستم امتیازدهی ←</a>
                        </div>
                        <div class="p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light text-muted" style="font-size: 0.78rem;">
                                        <tr>
                                            <th class="ps-4 py-3">دانشجو</th>
                                            <th class="py-3">امتیاز</th>
                                            <th class="py-3">دلیل</th>
                                            <th class="py-3">دسته‌بندی</th>
                                            <th class="py-3">ثبت توسط</th>
                                            <th class="pe-4 py-3">تاریخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($coursePoints))
                                            @forelse($coursePoints as $point)
                                                @php
                                                    $ptStudent = $enrollments->firstWhere('id', $point->user_id);
                                                @endphp
                                                <tr>
                                                    <td class="ps-4 fw-semibold">{{ $ptStudent->name ?? 'دانشجو' }}</td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $point->amount >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} px-2 py-1 fw-bold">
                                                            {{ $point->amount >= 0 ? '+' : '' }}{{ number_format($point->amount) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $point->reason ?? '—' }}</td>
                                                    <td><span
                                                            class="text-muted text-capitalize">{{ str_replace('_', ' ', $point->type) }}</span>
                                                    </td>
                                                    <td class="text-muted small">{{ $point->creator?->name ?? 'سیستم' }}</td>
                                                    <td class="pe-4 text-muted small" dir="ltr">
                                                        {{ $point->created_at->format('Y/m/d H:i') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-5 text-muted">
                                                        <i class="bi bi-journal-x fs-2 d-block mb-2 opacity-25"></i>
                                                        <p class="small mb-0">هنوز هیچ امتیازی در این دوره ثبت نشده است.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- REVIEWS --}}
                <div class="cd-tab-pane" id="tab-reviews" dir="rtl">
                    <div class="cd-card">
                        <div class="cd-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h6 class="cd-card-title mb-0"><i class="bi bi-star-fill me-2 text-warning"></i>نظرات و امتیازات
                                دانشجویان ({{ $reviews->count() }})</h6>
                            @if($reviews->count() > 0)
                                <span class="badge bg-warning text-dark fs-6 px-3">
                                    {{ number_format($avgRating, 1) }} / 5.0
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
                                        <div
                                            style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#94a3b8,#64748b);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;flex-shrink:0;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">دانشجو شماره {{ $revIndex + 1 }}</div>
                                            <div class="star-row">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="bi bi-star{{ $i <= $stars ? '-fill text-warning' : ' text-muted' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="text-start">
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($rev->created_at)->diffForHumans() }}</small>
                                            @if($rev->likes_count > 0 || $rev->dislikes_count > 0)
                                                <div class="mt-1" style="font-size:.75rem;">
                                                    <span class="text-success"><i class="bi bi-hand-thumbs-up-fill"></i>
                                                        {{ $rev->likes_count }}</span>
                                                    <span class="text-danger me-2"><i class="bi bi-hand-thumbs-down-fill"></i>
                                                        {{ $rev->dislikes_count }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if($rev->comment)
                                        <p class="text-muted mb-0" style="font-size:.9rem;">{{ $rev->comment }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="cd-empty text-center py-5">
                                    <i class="bi bi-chat-left-dots fs-1 text-muted"></i>
                                    <p class="mt-2 text-muted">هنوز نظری برای این دوره ثبت نشده است.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- DOCUMENTS --}}
                {{-- DOCUMENTS --}}
                <div class="cd-tab-pane" id="tab-documents" dir="rtl">

                    {{-- Success / Error Alert --}}
                    @if(session('doc_success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('doc_success') }}
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if($errors->has('file') || $errors->has('title'))
                        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ $errors->first('file') ?: $errors->first('title') }}
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row g-4">
                        {{-- Upload Form --}}
                        <div class="col-lg-4">
                            <div class="cd-card h-100">
                                <div class="cd-card-header">
                                    <h6 class="cd-card-title mb-0"><i
                                            class="bi bi-cloud-arrow-up me-2 text-primary"></i>بارگذاری جزوه و اسناد</h6>
                                </div>
                                <div class="cd-card-body p-4">
                                    <form method="POST"
                                        action="{{ route('teacher.courses.documents.upload', $course->id) }}"
                                        enctype="multipart/form-data" id="uploadDocForm">
                                        @csrf

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">عنوان جزوه یا فایل <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control rounded-3"
                                                placeholder="مثال: جزوه هفته اول – مبانی برنامه نویسی"
                                                value="{{ old('title') }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">جلسه یا درس مرتبط</label>
                                            <select name="lesson_id" class="form-select rounded-3">
                                                <option value="">— عمومی (غیروابسته به جلسه خاص) —</option>
                                                @foreach($course->lessons as $lesson)
                                                    <option value="{{ $lesson->id }}">
                                                        جلسه #{{ $lesson->order ?: $loop->iteration }} – {{ $lesson->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">توضیحات</label>
                                            <textarea name="description" rows="3" class="form-control rounded-3"
                                                placeholder="توضیح کوتاه درباره این فایل (اختیاری)"
                                                maxlength="500">{{ old('description') }}</textarea>
                                            <div class="form-text" style="font-size:0.75rem;">حداکثر ۵۰۰ کاراکتر</div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-semibold small">فایل PDF <span
                                                    class="text-danger">*</span></label>
                                            <div class="doc-upload-zone text-center p-3 border rounded-3" id="dropZone"
                                                onclick="document.getElementById('docFileInput').click()"
                                                style="cursor:pointer;border-style:dashed !important;">
                                                <i class="bi bi-file-earmark-pdf-fill"
                                                    style="font-size:2rem;color:#ef4444;"></i>
                                                <p class="mb-0 mt-2 fw-semibold" style="font-size:.9rem;">برای انتخاب فایل
                                                    PDF کلیک کنید</p>
                                                <p class="mb-0 text-muted" style="font-size:.78rem;">فقط فرمت PDF • حداکثر ۳
                                                    مگابایت</p>
                                                <div id="fileChosen" class="mt-2" style="display:none;">
                                                    <span
                                                        class="badge rounded-pill px-3 py-2 bg-success-subtle text-success"
                                                        style="font-size:.78rem;">
                                                        <i class="bi bi-check-circle me-1"></i><span
                                                            id="fileChosenName"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <input type="file" id="docFileInput" name="file" accept=".pdf" class="d-none"
                                                onchange="handleFileChosen(this)">
                                        </div>

                                        <button type="submit" class="btn w-100 rounded-3 fw-bold py-2 text-white shadow-sm"
                                            style="background:linear-gradient(135deg,#0A58CA,#0284c7);border:none;">
                                            <i class="bi bi-cloud-upload me-2"></i>بارگذاری فایل
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Document List --}}
                        <div class="col-lg-8">
                            <div class="cd-card">
                                <div
                                    class="cd-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <h6 class="cd-card-title mb-0">
                                        <i class="bi bi-folder2-open me-2 text-primary"></i>اسناد و جزوات بارگذاری شده
                                    </h6>
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1.5">
                                        {{ $documents->count() }} فایل
                                    </span>
                                </div>
                                <div class="cd-card-body p-3">
                                    @forelse($documents as $doc)
                                        <div
                                            class="doc-row cd-card mb-2 p-3 d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="doc-icon text-danger fs-2 flex-shrink-0">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="doc-title fw-bold" style="font-size:0.95rem;">{{ $doc->title }}
                                                    </div>
                                                    @if($doc->lesson)
                                                        <span
                                                            class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 mt-1"
                                                            style="font-size:0.72rem;">
                                                            <i class="bi bi-bookmark-fill me-1"></i>{{ $doc->lesson->title }}
                                                        </span>
                                                    @else
                                                        <span
                                                            class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5 mt-1"
                                                            style="font-size:0.72rem;">
                                                            <i class="bi bi-collection me-1"></i>عمومی
                                                        </span>
                                                    @endif
                                                    @if($doc->description)
                                                        <p class="text-muted small mb-0 mt-1">{{ $doc->description }}</p>
                                                    @endif
                                                    <div class="d-flex gap-3 text-muted mt-1" style="font-size:0.72rem;">
                                                        <span><i
                                                                class="bi bi-hdd me-1"></i>{{ $doc->file_size_formatted }}</span>
                                                        <span><i
                                                                class="bi bi-clock me-1"></i>{{ $doc->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="doc-actions d-flex align-items-center gap-2 ms-auto">
                                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold"
                                                    style="font-size:.8rem;">
                                                    <i class="bi bi-download me-1"></i>دانلود
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('teacher.courses.documents.delete', [$course->id, $doc->id]) }}"
                                                    onsubmit="return confirm('آیا از حذف این سند اطمینان دارید؟')"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger rounded-pill px-3" title="حذف سند">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="cd-empty text-center py-5">
                                            <i class="bi bi-folder-x fs-1 text-muted"></i>
                                            <p class="mt-2 text-muted">هنوز سندی برای این دوره بارگذاری نشده است. از فرم روبرو
                                                برای افزودن اولین فایل استفاده نمایید.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SESSION HISTORY --}}
                <div class="cd-tab-pane" id="tab-history" dir="rtl">
                    <div class="cd-card">
                        <div class="cd-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h6 class="cd-card-title mb-0">
                                <i class="bi bi-clock-history me-2 text-primary"></i>تاریخچه جلسات برگزار شده
                                ({{ $pastSessions->count() }})
                            </h6>
                        </div>
                        <div class="cd-card-body p-3">
                            @forelse($pastSessions as $s)
                                @php
                                    $hasDocs = $documents->where('created_at', '>=', $s->started_at)
                                        ->where('created_at', '<=', $s->ended_at ?? $s->started_at->addHours(4))
                                        ->count();
                                    $sessionAttendees = $s->attendances->sortByDesc(function ($a) {
                                        return $a->status === 'present' ? 2 : ($a->status === 'late' ? 1 : 0);
                                    });
                                    $isNoAttendance = $s->is_cancelled || ($sessionAttendees->count() === 0 && ($s->attendees_count === 0 || $s->attendees_count === null));
                                @endphp
                                <div class="shistory-card mb-3 p-3 cd-card {{ $isNoAttendance ? 'border-danger-subtle' : '' }}"
                                    style="{{ $isNoAttendance ? 'border-right: 4px solid #ef4444 !important;' : 'border-right: 4px solid #10b981;' }}">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="shistory-index rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                            style="width:36px;height:36px;{{ $isNoAttendance ? 'background: #fee2e2; color: #dc2626;' : 'background: #dcfce7; color: #15803d;' }}">
                                            #{{ $loop->iteration }}
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ $s->started_at->format('Y/m/d') }}
                                                </span>
                                                <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-1">
                                                    <i class="bi bi-hourglass-split me-1"></i>{{ $s->duration }}
                                                </span>
                                                @if($isNoAttendance)
                                                    <span
                                                        class="badge rounded-pill px-3 py-1 bg-danger-subtle text-danger border border-danger-subtle fw-bold">
                                                        <i class="bi bi-person-x-fill me-1"></i>بدون حضور دانشجو (لغو خودکار)
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge rounded-pill px-3 py-1 bg-success-subtle text-success border border-success-subtle fw-bold">
                                                        <i class="bi bi-check2-circle me-1"></i>جلسه برگزار گردید
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Start and End Times & Lesson Info --}}
                                            <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                                                <span class="badge bg-success rounded-pill px-3 py-1">
                                                    <i class="bi bi-play-fill me-1"></i>شروع:
                                                    {{ $s->started_at->format('H:i') }}
                                                </span>
                                                @if($s->ended_at)
                                                    <span class="badge bg-secondary rounded-pill px-3 py-1">
                                                        <i class="bi bi-stop-fill me-1"></i>پایان: {{ $s->ended_at->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1">
                                                        <i class="bi bi-broadcast me-1"></i>در حال برگزاری
                                                    </span>
                                                @endif

                                                @if($s->lesson)
                                                    <span class="badge bg-light text-dark border rounded-pill px-3 py-1">
                                                        <i class="bi bi-book me-1 text-primary"></i>درس
                                                        {{ $s->lesson->order ?: '-' }}: {{ $s->lesson->title }}
                                                        @if($isNoAttendance)
                                                            <span class="text-danger ms-1 fw-bold">(تکمیل نشده)</span>
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="d-flex flex-wrap gap-4 text-muted small">
                                                {{-- Meet Link --}}
                                                <div>
                                                    <span class="d-block text-muted" style="font-size:0.72rem;">لینک جلسه
                                                        میت:</span>
                                                    <a href="{{ $s->meet_link }}" target="_blank"
                                                        class="text-primary text-decoration-none fw-semibold" dir="ltr">
                                                        <i
                                                            class="bi bi-camera-video me-1"></i>{{ Str::limit($s->meet_link, 35) }}
                                                    </a>
                                                </div>
                                                {{-- Attendees --}}
                                                <div>
                                                    <span class="d-block text-muted" style="font-size:0.72rem;">دانشجویان
                                                        حاضر:</span>
                                                    @if($isNoAttendance)
                                                        <span class="text-danger fw-bold">
                                                            <i class="bi bi-x-circle-fill me-1"></i>۰ دانشجو
                                                        </span>
                                                    @else
                                                        <span class="text-success fw-bold">
                                                            <i class="bi bi-people-fill me-1"></i>{{ $sessionAttendees->count() }}
                                                            دانشجو
                                                        </span>
                                                    @endif
                                                </div>
                                                {{-- Documents --}}
                                                <div>
                                                    <span class="d-block text-muted" style="font-size:0.72rem;">اسناد
                                                        کلاسی:</span>
                                                    @if($hasDocs > 0)
                                                        <span class="text-primary fw-semibold">
                                                            <i class="bi bi-paperclip me-1"></i>{{ $hasDocs }} فایل
                                                        </span>
                                                    @else
                                                        <span class="text-muted">ندارد</span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($s->note)
                                                <div class="p-2 rounded-3 mt-2 small"
                                                    style="{{ $isNoAttendance ? 'background:rgba(239,68,68,0.1);color:#ef4444;' : 'background:rgba(10,88,202,0.06);' }}">
                                                    <i
                                                        class="bi {{ $isNoAttendance ? 'bi-exclamation-circle-fill text-danger' : 'bi-chat-left-text text-primary' }} me-1"></i>{{ $s->note }}
                                                </div>
                                            @elseif($isNoAttendance)
                                                <div class="p-2 rounded-3 mt-2 small"
                                                    style="background:rgba(239,68,68,0.1);color:#ef4444;">
                                                    <i class="bi bi-exclamation-circle-fill me-1"></i>جلسه به دلیل عدم ورود
                                                    دانشجویان پس از ۳ دقیقه به طور خودکار لغو شد.
                                                </div>
                                            @endif

                                            {{-- ── Attendees Expandable Section ── --}}
                                            @if($sessionAttendees->count() > 0)
                                                <div class="mt-3">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1"
                                                        onclick="toggleAttendees({{ $s->id }})" id="att-toggle-btn-{{ $s->id }}">
                                                        <i class="bi bi-people-fill me-1"></i>
                                                        نمایش لیست حاضرین ({{ $sessionAttendees->count() }})
                                                    </button>

                                                    <div id="att-list-{{ $s->id }}" style="display:none;margin-top:12px;">
                                                        <div style="display:flex;flex-wrap:wrap;gap:10px;">
                                                            @foreach($sessionAttendees as $att)
                                                                @php
                                                                    $attAvatar = $att->user && $att->user->avatar
                                                                        ? (str_starts_with($att->user->avatar, 'http') ? $att->user->avatar : asset('storage/' . $att->user->avatar))
                                                                        : 'https://ui-avatars.com/api/?name=' . urlencode($att->user->name ?? 'User') . '&background=8b5cf6&color=fff&size=40';
                                                                    $attStatusColor = $att->status === 'present' ? '#10b981' : ($att->status === 'late' ? '#f59e0b' : '#ef4444');
                                                                    $attStatusBg = $att->status === 'present' ? 'rgba(16,185,129,0.1)' : ($att->status === 'late' ? 'rgba(245,158,11,0.1)' : 'rgba(239,68,68,0.1)');
                                                                    $attStatusFa = $att->status === 'present' ? 'حاضر' : ($att->status === 'late' ? 'تاخیر' : 'غایب');
                                                                    $attStatusIcon = $att->status === 'present' ? 'bi-check-circle-fill' : ($att->status === 'late' ? 'bi-clock-history' : 'bi-x-circle-fill');
                                                                @endphp
                                                                <div class="cd-card p-2 d-flex align-items-center gap-2"
                                                                    style="border-radius:10px;min-width:200px;">
                                                                    <img src="{{ $attAvatar }}" alt=""
                                                                        style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                                                    <div style="min-width:0;flex:1;">
                                                                        <div class="fw-bold small text-truncate">
                                                                            {{ $att->user->name ?? 'نامشخص' }}
                                                                        </div>
                                                                        @if($att->joined_at)
                                                                            <div style="font-size:.7rem;" class="text-muted">
                                                                                <i
                                                                                    class="bi bi-box-arrow-in-right me-1 text-success"></i>{{ $att->joined_at->format('H:i') }}
                                                                                @if($att->left_at)
                                                                                    <i
                                                                                        class="bi bi-box-arrow-right ms-2 me-1 text-danger"></i>{{ $att->left_at->format('H:i') }}
                                                                                @endif
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <span
                                                                        style="background:{{ $attStatusBg }};color:{{ $attStatusColor }};border-radius:6px;padding:2px 8px;font-size:.7rem;font-weight:700;">
                                                                        <i class="bi {{ $attStatusIcon }} me-1"></i>{{ $attStatusFa }}
                                                                    </span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="cd-empty text-center py-5">
                                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                                    <p class="mt-2 text-muted">هنوز سابقه‌ای از جلسات قبلی ثبت نشده است.</p>
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
                        <div class="cd-card">
                            <div class="cd-card-body">
                                <div class="cd-empty">
                                    <i class="bi bi-person-x"></i>
                                    <p>هنوز هیچ دانشجویی در این دوره ثبت‌نام نکرده است.</p>
                                </div>
                            </div>
                        </div>
                    @else

                        @php
                            $totalSessions = $pastSessions->count();
                            $overallRate = $attendanceSummary->count() > 0 ? round($attendanceSummary->avg('rate')) : 0;
                            $riskCount = $attendanceSummary->where('rate', '<', 60)->count();
                            $goodCount = $attendanceSummary->where('rate', '>=', 80)->count();
                        @endphp

                        {{-- ── Hero Stats Row ── --}}
                        <div class="att2-stats-row mb-4">
                            <div class="att2-stat-item" style="--c:#1F8FFF;">
                                <div class="att2-stat-icon" style="background:#eff6ff;"><i class="bi bi-people-fill"
                                        style="color:#1F8FFF;"></i></div>
                                <div>
                                    <div class="att2-stat-num">{{ $totalStudents }}</div>
                                    <div class="att2-stat-lbl">دانشجویان</div>
                                </div>
                            </div>
                            <div class="att2-stat-item" style="--c:#8b5cf6;">
                                <div class="att2-stat-icon" style="background:#ede9fe;"><i class="bi bi-calendar-check"
                                        style="color:#8b5cf6;"></i></div>
                                <div>
                                    <div class="att2-stat-num">{{ $totalSessions }}</div>
                                    <div class="att2-stat-lbl">جلسات برگزار شده</div>
                                </div>
                            </div>
                            <div class="att2-stat-item" style="--c:#10b981;">
                                <div class="att2-stat-icon" style="background:#ecfdf5;"><i class="bi bi-graph-up-arrow"
                                        style="color:#10b981;"></i></div>
                                <div>
                                    <div class="att2-stat-num">{{ $overallRate }}%</div>
                                    <div class="att2-stat-lbl">میانگین حضور</div>
                                </div>
                            </div>
                            <div class="att2-stat-item" style="--c:#10b981;">
                                <div class="att2-stat-icon" style="background:#ecfdf5;"><i class="bi bi-award"
                                        style="color:#10b981;"></i></div>
                                <div>
                                    <div class="att2-stat-num">{{ $goodCount }}</div>
                                    <div class="att2-stat-lbl">وضعیت مطلوب (≥۸۰٪)</div>
                                </div>
                            </div>
                            <div class="att2-stat-item" style="--c:#ef4444;">
                                <div class="att2-stat-icon" style="background:#fef2f2;"><i
                                        class="bi bi-exclamation-triangle-fill" style="color:#ef4444;"></i></div>
                                <div>
                                    <div class="att2-stat-num">{{ $riskCount }}</div>
                                    <div class="att2-stat-lbl">در معرض خطر</div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Per-Student Cards ── --}}
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <h6 class="fw-bold mb-0 text-body" style="font-size:1.05rem;"><i class="bi bi-people-fill me-2"
                                    style="color:#1F8FFF;"></i>نمای کلی حضور و غیاب</h6>
                            <a href="{{ route('teacher.courses.export-attendance-pdf', $course->id) }}"
                                class="btn rounded-3 fw-bold px-4 py-2"
                                style="background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;border:none;font-size:.82rem;">
                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>خروجی PDF حضور و غیاب
                            </a>
                        </div>

                        <div class="row g-3 mb-4">
                            @foreach($attendanceSummary as $a)
                                @php
                                    $avatarSrc = $a->avatar
                                        ? (str_starts_with($a->avatar, 'http') ? $a->avatar : asset('storage/' . $a->avatar))
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($a->name) . '&background=1F8FFF&color=fff&size=60';
                                    $rateColor = $a->rate >= 80 ? '#10b981' : ($a->rate >= 60 ? '#f59e0b' : '#ef4444');
                                    $rateBg = $a->rate >= 80 ? 'rgba(16,185,129,0.12)' : ($a->rate >= 60 ? 'rgba(245,158,11,0.12)' : 'rgba(239,68,68,0.12)');
                                    $statusText = $totalSessions === 0 ? 'بدون جلسه' : ($a->rate >= 80 ? 'مطلوب' : ($a->rate >= 60 ? 'هشدار' : 'در معرض خطر'));
                                    $statusIcon = $totalSessions === 0 ? 'bi-dash-circle' : ($a->rate >= 80 ? 'bi-check-circle-fill' : ($a->rate >= 60 ? 'bi-exclamation-circle-fill' : 'bi-x-circle-fill'));
                                @endphp
                                <div class="col-md-6 col-xl-4">
                                    <div class="att2-student-card">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <img src="{{ $avatarSrc }}" class="att2-avatar" alt="">
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-semibold text-truncate student-name" style="font-size:.92rem;">
                                                    {{ $a->name }}</div>
                                                <span class="att2-status-badge"
                                                    style="background:{{ $rateBg }};color:{{ $rateColor }};">
                                                    <i class="bi {{ $statusIcon }} me-1"></i>{{ $statusText }}
                                                </span>
                                            </div>
                                            <div class="att2-rate-circle" style="--rate-color:{{ $rateColor }};">
                                                <span
                                                    style="color:{{ $rateColor }};font-weight:700;font-size:.88rem;">{{ $a->rate }}%</span>
                                            </div>
                                        </div>

                                        {{-- Progress bar --}}
                                        <div class="att2-prog-bar mb-3">
                                            <div class="att2-prog-fill" style="width:{{ $a->rate }}%;background:{{ $rateColor }};">
                                            </div>
                                        </div>

                                        {{-- P / L / A counters --}}
                                        <div class="att2-counters">
                                            <div class="att2-counter">
                                                <span class="att2-counter-val" style="color:#10b981;">{{ $a->present }}</span>
                                                <span class="att2-counter-lbl">حاضر</span>
                                            </div>
                                            <div class="att2-counter">
                                                <span class="att2-counter-val" style="color:#f59e0b;">{{ $a->late }}</span>
                                                <span class="att2-counter-lbl">تاخیر</span>
                                            </div>
                                            <div class="att2-counter">
                                                <span class="att2-counter-val" style="color:#ef4444;">{{ $a->absent }}</span>
                                                <span class="att2-counter-lbl">غایب</span>
                                            </div>
                                            <div class="att2-counter">
                                                <span class="att2-counter-val" style="color:#6366f1;">{{ $a->total }}</span>
                                                <span class="att2-counter-lbl">مجموع</span>
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
                                    <h6 class="cd-card-title"><i class="bi bi-clipboard2-check me-2" style="color:#8b5cf6;"></i>ثبت
                                        حضور و غیاب جلسه</h6>
                                    {{-- Session dropdown selector --}}
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="text-muted small mb-0 fw-semibold">جلسه:</label>
                                        <select id="sessionDropdown" class="form-select form-select-sm rounded-3"
                                            style="max-width:220px;font-size:.82rem;" onchange="showSessionPane(this.value)">
                                            @foreach($pastSessions as $si => $s)
                                                <option value="{{ $s->id }}" {{ $si === 0 ? 'selected' : '' }}>
                                                    جلسه {{ $pastSessions->count() - $si }} · {{ $s->started_at->format('Y/m/d') }}
                                                    ({{ $s->started_at->format('H:i') }})
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
                                            <form method="POST"
                                                action="{{ route('teacher.courses.sessions.attendance', [$course->id, $s->id]) }}"
                                                class="att-ajax-form">
                                                @csrf

                                                {{-- Session info bar --}}
                                                <div
                                                    class="att-session-bar d-flex align-items-center justify-content-between flex-wrap gap-3">
                                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                                        <span class="fw-bold att-session-date" style="font-size:.9rem;">
                                                            <i class="bi bi-calendar3 me-2"
                                                                style="color:#8b5cf6;"></i>{{ $s->started_at->format('Y/m/d') }}
                                                        </span>
                                                        <span class="badge rounded-pill px-3"
                                                            style="background:rgba(139,92,246,0.15);color:#a78bfa;font-size:.73rem;">
                                                            <i class="bi bi-hourglass-split me-1"></i>{{ $s->duration }}
                                                        </span>
                                                        <span class="text-muted" style="font-size:.82rem;">
                                                            {{ $s->started_at->format('H:i') }} تا
                                                            {{ $s->ended_at?->format('H:i') ?? 'نامشخص' }}
                                                        </span>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" onclick="markAll({{ $s->id }},'present')"
                                                            class="btn btn-sm rounded-3 px-3 fw-semibold"
                                                            style="background:rgba(16,185,129,0.15);color:#10b981;border:1px solid rgba(16,185,129,0.3);font-size:.78rem;">
                                                            <i class="bi bi-check-all me-1"></i>همه حاضر
                                                        </button>
                                                        <button type="button" onclick="markAll({{ $s->id }},'absent')"
                                                            class="btn btn-sm rounded-3 px-3 fw-semibold"
                                                            style="background:rgba(239,68,68,0.15);color:#ef4444;border:1px solid rgba(239,68,68,0.3);font-size:.78rem;">
                                                            <i class="bi bi-x-lg me-1"></i>همه غایب
                                                        </button>
                                                    </div>
                                                </div>

                                                {{-- Student rows --}}
                                                <div style="padding:8px 0;">
                                                    @foreach($enrollments as $enIdx => $en)
                                                        @php
                                                            $enAvatar = $en->avatar
                                                                ? (str_starts_with($en->avatar, 'http') ? $en->avatar : asset('storage/' . $en->avatar))
                                                                : 'https://ui-avatars.com/api/?name=' . urlencode($en->name) . '&background=1F8FFF&color=fff&size=60';
                                                            $curStatus = $existing[$en->id] ?? 'present';
                                                        @endphp
                                                        <div
                                                            class="att-mark-row att-mark-row-v2 {{ $enIdx % 2 === 1 ? 'att-row-alt' : '' }}">
                                                            <div class="d-flex align-items-center gap-3" style="min-width:0;flex:1;">
                                                                <img src="{{ $enAvatar }}" class="att-avatar" alt="">
                                                                <div style="min-width:0;">
                                                                    <div class="fw-semibold text-truncate student-name"
                                                                        style="font-size:.88rem;">{{ $en->name }}</div>
                                                                    <div style="font-size:.72rem;color:#94a3b8;">
                                                                        @php
                                                                            $enr = $attendanceSummary->firstWhere('user_id', $en->id);
                                                                        @endphp
                                                                        @if($enr)
                                                                            {{ $enr->present }} حاضر · {{ $enr->late }} تاخیر ·
                                                                            {{ $enr->absent }} غایب
                                                                            <span class="ms-1 fw-semibold"
                                                                                style="color:{{ $enr->rate >= 80 ? '#10b981' : ($enr->rate >= 60 ? '#f59e0b' : '#ef4444') }};">
                                                                                ({{ $enr->rate }}%)
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="att-radio-group-v2" id="rg-{{ $s->id }}-{{ $en->id }}">
                                                                <label
                                                                    class="att-pill-lbl {{ $curStatus === 'present' ? 'att-pill-present-on' : 'att-pill-off' }}">
                                                                    <input type="radio" name="attendance[{{ $en->id }}]" value="present" {{ $curStatus === 'present' ? 'checked' : '' }} class="d-none att-radio-inp"
                                                                        onchange="updatePillGroup(this)">
                                                                    <i class="bi bi-check-circle-fill me-1"></i>حاضر
                                                                </label>
                                                                <label
                                                                    class="att-pill-lbl {{ $curStatus === 'late' ? 'att-pill-late-on' : 'att-pill-off' }}">
                                                                    <input type="radio" name="attendance[{{ $en->id }}]" value="late" {{ $curStatus === 'late' ? 'checked' : '' }} class="d-none att-radio-inp"
                                                                        onchange="updatePillGroup(this)">
                                                                    <i class="bi bi-clock-fill me-1"></i>تاخیر
                                                                </label>
                                                                <label
                                                                    class="att-pill-lbl {{ $curStatus === 'absent' ? 'att-pill-absent-on' : 'att-pill-off' }}">
                                                                    <input type="radio" name="attendance[{{ $en->id }}]" value="absent" {{ $curStatus === 'absent' ? 'checked' : '' }} class="d-none att-radio-inp"
                                                                        onchange="updatePillGroup(this)">
                                                                    <i class="bi bi-x-circle-fill me-1"></i>غایب
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div style="padding:16px 20px;border-top:1px solid rgba(255,255,255,0.06);"
                                                    class="text-start">
                                                    <button type="submit" class="btn rounded-3 fw-bold px-5 py-2"
                                                        style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;">
                                                        <i class="bi bi-save me-2"></i>ذخیره حضور و غیاب
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
                                    <h6 class="cd-card-title"><i class="bi bi-plus-circle-fill me-2 text-primary"></i>افزودن
                                        یادداشت کلاس</h6>
                                </div>
                                <div class="cd-card-body">
                                    <form method="POST" action="{{ route('teacher.courses.notes.store', $course->id) }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" style="font-size:.85rem;">تاریخ جلسه <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="class_date" class="form-control rounded-3"
                                                value="{{ old('class_date', date('Y-m-d')) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" style="font-size:.85rem;">عنوان
                                                (اختیاری)</label>
                                            <input type="text" name="title" class="form-control rounded-3"
                                                placeholder="مثلاً: جلسه ۵ – متغیرها و توابع" value="{{ old('title') }}"
                                                maxlength="255">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" style="font-size:.85rem;">در کلاس امروز چه
                                                مباحثی تدریس شد؟ <span class="text-danger">*</span></label>
                                            <textarea name="content" rows="6" class="form-control rounded-3"
                                                placeholder="خلاصه‌ای از مباحث آموزش داده شده و تکالیف جلسه را اینجا یادداشت کنید..."
                                                maxlength="3000" required>{{ old('content') }}</textarea>
                                            <div class="form-text">حداکثر ۳۰۰۰ نویسه</div>
                                        </div>
                                        <button type="submit" class="btn w-100 rounded-3 fw-bold py-2"
                                            style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;">
                                            <i class="bi bi-save me-2"></i>ذخیره یادداشت
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
                                        <i class="bi bi-journal-text me-2" style="color:#1F8FFF;"></i>یادداشت‌های ثبت‌شده
                                        کلاس
                                    </h6>
                                    <span class="badge rounded-pill px-3"
                                        style="background:rgba(31,143,255,0.15);color:#00f0ff;">
                                        {{ $classNotes->count() }} یادداشت
                                    </span>
                                </div>
                                <div class="cd-card-body">
                                    @forelse($classNotes as $note)
                                        <div class="cn-note-card mb-3" id="note-card-{{ $note->id }}">
                                            {{-- View mode --}}
                                            <div class="cn-view" id="cn-view-{{ $note->id }}">
                                                <div
                                                    class="d-flex align-items-start justify-content-between gap-2 mb-2 flex-wrap">
                                                    <div>
                                                        <span class="badge rounded-pill px-3 me-2"
                                                            style="background:rgba(14,165,233,0.15);color:#38bdf8;font-size:.75rem;">
                                                            <i
                                                                class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($note->class_date)->format('Y/m/d') }}
                                                        </span>
                                                        @if($note->title)
                                                            <span class="fw-bold cn-note-title"
                                                                style="font-size:.95rem;">{{ $note->title }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex gap-2 flex-shrink-0 ms-auto">
                                                        <button class="btn btn-sm rounded-pill px-3"
                                                            style="background:rgba(31,143,255,0.15);color:#00f0ff;border:none;font-size:.78rem;font-weight:600;"
                                                            onclick="toggleNoteEdit({{ $note->id }})">
                                                            <i class="bi bi-pencil me-1"></i>ویرایش
                                                        </button>
                                                        <form method="POST"
                                                            action="{{ route('teacher.courses.notes.delete', [$course->id, $note->id]) }}"
                                                            onsubmit="return confirm('آیا از حذف این یادداشت اطمینان دارید؟')"
                                                            style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm rounded-pill px-3"
                                                                style="background:rgba(239,68,68,0.15);color:#ef4444;border:none;font-size:.78rem;font-weight:600;">
                                                                <i class="bi bi-trash3"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <p class="cn-note-content"
                                                    style="font-size:.9rem;margin:0;white-space:pre-wrap;">{{ $note->content }}
                                                </p>
                                                <div class="mt-2 text-muted" style="font-size:.72rem;">
                                                    <i class="bi bi-clock me-1"></i>ثبت شده
                                                    {{ $note->created_at->diffForHumans() }}
                                                </div>
                                            </div>

                                            {{-- Edit mode (hidden by default) --}}
                                            <div class="cn-edit" id="cn-edit-{{ $note->id }}" style="display:none;">
                                                <form method="POST"
                                                    action="{{ route('teacher.courses.notes.update', [$course->id, $note->id]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="row g-2 mb-2">
                                                        <div class="col-md-4">
                                                            <input type="date" name="class_date"
                                                                class="form-control form-control-sm rounded-3"
                                                                value="{{ $note->class_date->format('Y-m-d') }}" required>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" name="title"
                                                                class="form-control form-control-sm rounded-3"
                                                                placeholder="عنوان یادداشت (اختیاری)" value="{{ $note->title }}"
                                                                maxlength="255">
                                                        </div>
                                                    </div>
                                                    <textarea name="content" rows="4"
                                                        class="form-control form-control-sm rounded-3 mb-2" maxlength="3000"
                                                        required>{{ $note->content }}</textarea>
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" class="btn btn-sm rounded-3 fw-bold px-4"
                                                            style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;">
                                                            <i class="bi bi-save me-1"></i>ذخیره تغییرات
                                                        </button>
                                                        <button type="button" class="btn btn-sm rounded-3 px-3 btn-secondary"
                                                            onclick="toggleNoteEdit({{ $note->id }})">
                                                            انصراف
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @if(!$loop->last)
                                            <hr style="border-color:rgba(255,255,255,0.06);margin:1rem 0;">
                                        @endif
                                    @empty
                                        <div class="cd-empty">
                                            <i class="bi bi-journal-x"></i>
                                            <p>هنوز یادداشتی برای کلاس‌ها ثبت نشده است. از فرم روبرو برای ثبت اولین یادداشت
                                                استفاده کنید.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cd-tab-pane" id="tab-chat">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('courses.chat.show', $course) }}" class="btn btn-sm fw-semibold"
                            style="background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;border:none;border-radius:10px;">
                            <i class="bi bi-box-arrow-up-right me-1"></i> باز کردن گفتگوی تمام صفحه
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
                                    <h6 class="cd-card-title"><i class="bi bi-plus-circle-fill me-2"
                                            style="color:#1F8FFF;"></i>افزودن درس جدید</h6>
                                </div>
                                <div class="cd-card-body">
                                    <form method="POST" action="{{ route('teacher.courses.lessons.store', $course->id) }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" style="font-size:.85rem;">عنوان درس <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control rounded-3"
                                                placeholder="مثلاً: معرفی متغیرها و انواع داده" value="{{ old('title') }}"
                                                required maxlength="255">
                                            @error('title')<small class="text-danger">{{ $message }}</small>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" style="font-size:.85rem;">توضیحات
                                                درس</label>
                                            <textarea name="description" rows="3" class="form-control rounded-3"
                                                placeholder="توضیح مختصری درباره سرفصل‌های این درس بنویسید..."
                                                maxlength="1000">{{ old('description') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" style="font-size:.85rem;">مدت زمان
                                                (دقیقه)</label>
                                            <input type="number" name="duration_minutes" class="form-control rounded-3"
                                                placeholder="مثلاً: ۴۵" min="1" max="600"
                                                value="{{ old('duration_minutes') }}">
                                        </div>
                                        <div class="mb-4">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_free" value="1"
                                                    id="newLessonFree" {{ old('is_free') ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="newLessonFree"
                                                    style="font-size:.85rem;">پیش‌نمایش رایگان</label>
                                            </div>
                                            <small class="text-muted">امکان مشاهده این درس برای دانشجویان ثبت‌نام‌نشده فعال
                                                باشد</small>
                                        </div>
                                        <button type="submit" class="btn w-100 rounded-3 fw-bold py-2"
                                            style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;">
                                            <i class="bi bi-plus-lg me-2"></i>افزودن درس
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
                                        <i class="bi bi-list-ol me-2" style="color:#1F8FFF;"></i>سرفصل‌ها و درس‌های دوره
                                    </h6>
                                    <span class="badge rounded-pill px-3"
                                        style="background:rgba(31,143,255,0.15);color:#00f0ff;">
                                        {{ $totalLessons }} درس
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
                                                            <span class="fw-bold curr-title"
                                                                style="font-size:.95rem;">{{ $lesson->title }}</span>
                                                            @if($lesson->is_free)
                                                                <span class="badge rounded-pill px-2"
                                                                    style="background:rgba(16,185,129,0.15);color:#10b981;font-size:.68rem;">رایگان</span>
                                                            @endif
                                                            @if($lesson->duration_minutes)
                                                                <span class="badge rounded-pill px-2"
                                                                    style="background:rgba(255,255,255,0.06);color:#94a3b8;font-size:.68rem;">
                                                                    <i class="bi bi-clock me-1"></i>{{ $lesson->duration_minutes }}
                                                                    دقیقه
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if($lesson->description)
                                                            <p class="curr-desc text-muted" style="font-size:.82rem;margin:0;">
                                                                {{ Str::limit($lesson->description, 120) }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex gap-2 flex-shrink-0 ms-auto">
                                                        <button class="btn btn-sm rounded-pill px-3"
                                                            style="background:rgba(31,143,255,0.15);color:#00f0ff;border:none;font-size:.78rem;font-weight:600;"
                                                            onclick="toggleCurrEdit({{ $lesson->id }})">
                                                            <i class="bi bi-pencil me-1"></i>ویرایش
                                                        </button>
                                                        <form method="POST"
                                                            action="{{ route('teacher.courses.lessons.delete', [$course->id, $lesson->id]) }}"
                                                            onsubmit="return confirm('آیا از حذف این درس مطمئن هستید؟ این عملیات غیرقابل بازگشت است.')"
                                                            style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm rounded-pill px-3"
                                                                style="background:rgba(239,68,68,0.15);color:#ef4444;border:none;font-size:.78rem;font-weight:600;">
                                                                <i class="bi bi-trash3"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Edit Mode (hidden by default) --}}
                                            <div class="curr-edit" id="curr-edit-{{ $lesson->id }}" style="display:none;">
                                                <form method="POST"
                                                    action="{{ route('teacher.courses.lessons.update', [$course->id, $lesson->id]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="row g-2 mb-2">
                                                        <div class="col-md-2">
                                                            <label class="form-label" style="font-size:.75rem;">ترتیب</label>
                                                            <input type="number" name="order"
                                                                class="form-control form-control-sm rounded-3"
                                                                value="{{ $lesson->order }}" min="1">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label class="form-label" style="font-size:.75rem;">عنوان *</label>
                                                            <input type="text" name="title"
                                                                class="form-control form-control-sm rounded-3"
                                                                value="{{ $lesson->title }}" required maxlength="255">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label" style="font-size:.75rem;">مدت
                                                                (دقیقه)</label>
                                                            <input type="number" name="duration_minutes"
                                                                class="form-control form-control-sm rounded-3"
                                                                value="{{ $lesson->duration_minutes }}" min="1" max="600">
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-end">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" name="is_free"
                                                                    value="1" id="editFree{{ $lesson->id }}" {{ $lesson->is_free ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="editFree{{ $lesson->id }}"
                                                                    style="font-size:.82rem;">رایگان</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <textarea name="description" rows="2"
                                                            class="form-control form-control-sm rounded-3"
                                                            placeholder="توضیحات درس..."
                                                            maxlength="1000">{{ $lesson->description }}</textarea>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" class="btn btn-sm rounded-3 fw-bold px-4"
                                                            style="background:linear-gradient(135deg,#1F8FFF,#6366f1);color:#fff;border:none;">
                                                            <i class="bi bi-save me-1"></i>ذخیره تغییرات
                                                        </button>
                                                        <button type="button" class="btn btn-sm rounded-3 px-3 btn-secondary"
                                                            onclick="toggleCurrEdit({{ $lesson->id }})">
                                                            انصراف
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="cd-empty">
                                            <i class="bi bi-journal-plus"></i>
                                            <p>هنوز درسی تعریف نشده است. از فرم روبرو برای افزودن اولین درس استفاده کنید.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- REFERRALS & INVITE --}}
                <div class="cd-tab-pane" id="tab-referrals">
                    {{-- Invite Link & Sharing Card --}}
                    <div class="cd-card mb-4"
                        style="background: linear-gradient(135deg, rgba(31, 143, 255, 0.08) 0%, rgba(16, 185, 129, 0.06) 100%); border: 1px solid rgba(31, 143, 255, 0.22);">
                        <div class="cd-card-body p-4">
                            <!-- Header row -->
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3 pb-3"
                                style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        style="width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.4rem;box-shadow:0 6px 14px rgba(16,185,129,0.25);flex-shrink:0;">
                                        <i class="bi bi-link-45deg"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1" style="color:var(--text-color, #1e293b);">لینک دعوت و معرفی
                                            دوره</h5>
                                        <p class="text-muted small mb-0">لینک اختصاصی خود را با دانشجویان به اشتراک بگذارید.
                                            دعوت‌ها تا ۳۰ روز به نام شما ثبت و پیگیری می‌شوند.</p>
                                    </div>
                                </div>

                                <!-- Code Badge & Regenerate Button -->
                                <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                                    <div class="d-flex align-items-center px-3 py-1.5 rounded-pill cd-referral-badge-box shadow-sm"
                                        style="gap: 8px;">
                                        <span class="text-muted small fw-semibold">کد معرف:</span>
                                        <span
                                            class="badge rounded-pill bg-primary-subtle text-primary fw-bold font-monospace px-2 py-1"
                                            id="referralCodeDisplay"
                                            style="font-size: 0.95rem; letter-spacing: 1px;">{{ $referral->code }}</span>
                                        <button type="button" class="btn btn-link btn-sm p-0 text-muted ms-1"
                                            onclick="copyReferralCodeOnly()" title="کپی فقط کد">
                                            <i class="bi bi-clipboard" id="copyCodeOnlyIcon"></i>
                                        </button>
                                    </div>
                                    <button type="button"
                                        class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center gap-1.5 shadow-sm cd-regenerate-btn"
                                        id="regenerateReferralBtn" onclick="regenerateReferralCode()"
                                        title="ایجاد کد و لینک دعوت جدید">
                                        <i class="bi bi-arrow-repeat" id="regenerateIcon"></i>
                                        <span>تولید کد جدید</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Main Link and Share Row -->
                            <div class="row g-3 align-items-center">
                                <div class="col-lg-8">
                                    <label class="form-label text-muted small fw-bold mb-1">
                                        <i class="bi bi-link-45deg me-1 text-primary"></i>آدرس لینک دعوت
                                    </label>
                                    <div
                                        class="input-group shadow-sm rounded-3 overflow-hidden border cd-referral-input-group">
                                        <span class="input-group-text border-0 text-muted ps-3">
                                            <i class="bi bi-globe2 text-primary"></i>
                                        </span>
                                        <input type="text" class="form-control border-0 fw-semibold" id="referralInviteUrl"
                                            value="{{ $referral->invite_url }}" readonly
                                            style="font-family: monospace; font-size: 0.92rem; direction: ltr; text-align: left;">
                                        <button class="btn btn-primary px-4 fw-bold d-flex align-items-center gap-2"
                                            type="button" id="copyReferralBtn" onclick="copyReferralLink()">
                                            <i class="bi bi-clipboard" id="copyReferralIcon"></i>
                                            <span id="copyReferralText">کپی لینک</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label text-muted small fw-bold mb-1">
                                        <i class="bi bi-share-fill me-1 text-success"></i>اشتراک‌گذاری مستقیم
                                    </label>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <a href="https://t.me/share/url?url={{ urlencode($referral->invite_url) }}&text={{ urlencode('در دوره ' . $course->title . ' در ادورا تک شرکت کنید!') }}"
                                            target="_blank"
                                            class="btn btn-sm rounded-pill px-3 py-2 fw-semibold text-white shadow-sm flex-grow-1 text-center"
                                            style="background: #229ED9; border: none; font-size: 0.82rem;">
                                            <i class="bi bi-telegram me-1"></i> تلگرام
                                        </a>
                                        <a href="https://api.whatsapp.com/send?text={{ urlencode('دوره ' . $course->title . ' در ادورا تک: ' . $referral->invite_url) }}"
                                            target="_blank"
                                            class="btn btn-sm rounded-pill px-3 py-2 fw-semibold text-white shadow-sm flex-grow-1 text-center"
                                            style="background: #25D366; border: none; font-size: 0.82rem;">
                                            <i class="bi bi-whatsapp me-1"></i> واتساپ
                                        </a>
                                        <a href="mailto:?subject={{ urlencode('دعوت به دوره ' . $course->title) }}&body={{ urlencode("سلام،\n\nشما را به ثبت‌نام در دوره \"" . $course->title . "\" در ادورا تک دعوت می‌کنم.\n\nلینک ثبت‌نام: " . $referral->invite_url) }}"
                                            class="btn btn-sm rounded-pill px-3 py-2 fw-semibold btn-light border shadow-sm flex-grow-1 text-center cd-email-share-btn"
                                            style="font-size: 0.82rem;">
                                            <i class="bi bi-envelope-fill me-1 text-primary"></i> ایمیل
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Stats Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-xl-3">
                            <div class="cd-stat-card">
                                <div class="cd-stat-icon" style="background:rgba(31,143,255,0.12);">
                                    <i class="bi bi-mouse2-fill" style="color:#1F8FFF;"></i>
                                </div>
                                <div>
                                    <div class="cd-stat-num" id="statClicksCount">{{ $referral->clicks_count }}</div>
                                    <div class="cd-stat-lbl">کلیک روی لینک</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="cd-stat-card">
                                <div class="cd-stat-icon" style="background:rgba(14,165,233,0.12);">
                                    <i class="bi bi-person-plus-fill" style="color:#0284c7;"></i>
                                </div>
                                <div>
                                    <div class="cd-stat-num">{{ $referralRecords->where('status', 'registered')->count() }}
                                    </div>
                                    <div class="cd-stat-lbl">دانشجویان ثبت‌نامی</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="cd-stat-card">
                                <div class="cd-stat-icon" style="background:rgba(16,185,129,0.12);">
                                    <i class="bi bi-check-circle-fill" style="color:#10b981;"></i>
                                </div>
                                <div>
                                    <div class="cd-stat-num">{{ $referralRecords->where('status', 'enrolled')->count() }}
                                    </div>
                                    <div class="cd-stat-lbl">دانشجویان عضو دوره</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="cd-stat-card">
                                <div class="cd-stat-icon" style="background:rgba(245,158,11,0.12);">
                                    <i class="bi bi-percent" style="color:#f59e0b;"></i>
                                </div>
                                <div>
                                    <div class="cd-stat-num">
                                        {{ $referral->clicks_count > 0 ? round(($referralRecords->where('status', 'enrolled')->count() / $referral->clicks_count) * 100, 1) : 0 }}%
                                    </div>
                                    <div class="cd-stat-lbl">نرخ تبدیل</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Referred Students List Card --}}
                    <div class="cd-card">
                        <div class="cd-card-header d-flex align-items-center justify-content-between">
                            <h6 class="cd-card-title mb-0">
                                <i class="bi bi-people-fill me-2 text-success"></i>دانشجویان دعوت‌شده
                            </h6>
                            <span class="badge rounded-pill px-3"
                                style="background:rgba(16,185,129,0.15);color:#10b981;font-size:.8rem;">
                                {{ $referralRecords->count() }} دانشجو
                            </span>
                        </div>
                        <div class="cd-card-body p-0">
                            @if($referralRecords->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" style="font-size:0.9rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">دانشجو</th>
                                                <th>وضعیت</th>
                                                <th>زمان ثبت‌نام</th>
                                                <th>زمان عضویت</th>
                                                <th class="pe-4 text-end">عملیات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($referralRecords as $rec)
                                                <tr>
                                                    <td class="ps-4">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <img src="{{ $avatarUrl($rec->user?->avatar, $rec->user?->name ?? 'Student') }}"
                                                                alt="{{ $rec->user?->name ?? 'Student' }}"
                                                                class="rounded-circle shadow-sm"
                                                                style="width: 40px; height: 40px; object-fit: cover;">
                                                            <div>
                                                                <div class="fw-bold student-name">
                                                                    {{ $rec->user?->name ?? 'کاربر #' . $rec->user_id }}</div>
                                                                <div class="text-muted small"
                                                                    style="direction:ltr;text-align:right;">
                                                                    {{ $rec->user?->email ?? '-' }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($rec->status === 'enrolled')
                                                            <span class="badge rounded-pill"
                                                                style="background:rgba(16,185,129,0.15);color:#10b981;padding:6px 12px;font-size:0.75rem;">
                                                                <i class="bi bi-check2-circle me-1"></i> عضو شده
                                                            </span>
                                                        @elseif($rec->status === 'requested')
                                                            <span class="badge rounded-pill"
                                                                style="background:rgba(245,158,11,0.15);color:#f59e0b;padding:6px 12px;font-size:0.75rem;">
                                                                <i class="bi bi-clock me-1"></i> درخواست عضویت
                                                            </span>
                                                        @elseif($rec->status === 'registered')
                                                            <span class="badge rounded-pill"
                                                                style="background:rgba(14,165,233,0.15);color:#38bdf8;padding:6px 12px;font-size:0.75rem;">
                                                                <i class="bi bi-person-check me-1"></i> ثبت‌نام کرده
                                                            </span>
                                                        @else
                                                            <span class="badge rounded-pill bg-light text-muted border"
                                                                style="padding:6px 12px;font-size:0.75rem;">
                                                                {{ $rec->status }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-muted small">
                                                        @if($rec->registered_at)
                                                            {{ $rec->registered_at->format('Y/m/d H:i') }}
                                                        @elseif($rec->created_at)
                                                            {{ $rec->created_at->format('Y/m/d') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-muted small">
                                                        @if($rec->enrolled_at)
                                                            <span class="text-success fw-semibold">
                                                                <i
                                                                    class="bi bi-check me-1"></i>{{ $rec->enrolled_at->format('Y/m/d H:i') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted opacity-75">هنوز عضو دوره نشده</span>
                                                        @endif
                                                    </td>
                                                    <td class="pe-4 text-end">
                                                        @if($rec->user)
                                                            <a href="{{ route('teacher.students.profile', $rec->user->id) }}"
                                                                class="btn btn-sm btn-light border rounded-pill px-3 text-secondary"
                                                                style="font-size:0.8rem;">
                                                                <i class="bi bi-person me-1"></i> پروفایل
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div
                                        style="width: 72px; height: 72px; margin: 0 auto 1rem; background: rgba(16,185,129,0.12); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 2rem;">
                                        <i class="bi bi-link-45deg"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">هنوز دعوتی ثبت نشده است</h6>
                                    <p class="text-muted small mb-3"
                                        style="max-width: 420px; margin-left: auto; margin-right: auto;">
                                        لینک دعوت خود را در شبکه‌های اجتماعی یا با دانشجویان بالقوه به اشتراک بگذارید. تمام
                                        کسانی که با این لینک عضو شوند اینجا نمایش داده می‌شوند.
                                    </p>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold"
                                        onclick="copyReferralLink()">
                                        <i class="bi bi-clipboard me-1"></i> کپی لینک دعوت
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- Mobile Floating Bottom Navigation Dock --}}
            <nav class="d-lg-none cd-mobile-dock" dir="rtl" aria-label="منوی سریع موبایل">
                <a href="{{ route('teacher.dashboard') }}" class="cd-mobile-dock-item">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>داشبورد</span>
                </a>
                <a href="{{ route('teacher.your-courses') }}" class="cd-mobile-dock-item active">
                    <i class="bi bi-collection-play-fill"></i>
                    <span>دوره‌ها</span>
                </a>
                <button type="button" class="cd-mobile-dock-item cd-dock-action-btn"
                    onclick="window.switchCourseTab('syllabus'); window.scrollTo({top:0, behavior:'smooth'});">
                    <div class="cd-dock-action-orb">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <span>کلاس</span>
                </button>
                <a href="{{ route('teacher.exams.index') }}" class="cd-mobile-dock-item">
                    <i class="bi bi-patch-question-fill"></i>
                    <span>آزمون‌ها</span>
                </a>
                <button type="button" class="cd-mobile-dock-item" id="mobileDockMenuBtn"
                    onclick="const b=document.getElementById('sidebarMobileToggle'); if(b) b.click();">
                    <i class="bi bi-list"></i>
                    <span>منو</span>
                </button>
            </nav>
        </main>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/beta-notice.js') }}"></script>
    <script src="{{ asset('assets/js/teacher-dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/attendance.js') }}"></script>
    <script>
        // Class Note Notification Handler
        // Show toast when note is successfully sent to students
        @if(session('note_success'))
            document.addEventListener('DOMContentLoaded', function () {
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
        window.switchCourseTab = function (id, btn) {
            document.querySelectorAll('.cd-tab-pane').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.cd-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.edvora-sidebar-tab-btn').forEach(b => b.classList.remove('active'));

            const target = document.getElementById('tab-' + id);
            if (target) target.classList.add('active');

            if (btn) {
                btn.classList.add('active');
            } else {
                const sidebarBtn = document.querySelector(`.edvora-sidebar-tab-btn[data-tab="${id}"]`);
                if (sidebarBtn) sidebarBtn.classList.add('active');
            }

            if (id === 'chat') {
                if (window.clearCourseChatBadge) {
                    window.clearCourseChatBadge({{ $course->id }});
                }
            }
        };

        window.switchTab = function (id, btn) {
            window.switchCourseTab(id, btn);
        };

        // Activate tab from URL ?tab=... or #...
        document.addEventListener('DOMContentLoaded', function () {
            const params = new URLSearchParams(window.location.search);
            const hash = window.location.hash.replace('#tab-', '').replace('#', '');
            const tabFromUrl = params.get('tab') || hash;
            if (tabFromUrl) {
                window.switchCourseTab(tabFromUrl);
            }
        });

        @if(session('student_action'))
            document.addEventListener('DOMContentLoaded', function () {
                window.switchCourseTab('students');
            });
        @endif

        @if(session('points_success'))
            document.addEventListener('DOMContentLoaded', function () {
                window.switchCourseTab('points');
            });
        @endif

    // Student search
    const searchInput = document.getElementById('studentSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
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
                lbl.classList.remove('att-pill-present-on', 'att-pill-late-on', 'att-pill-absent-on');
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
                alert('حجم فایل بیش از حد مجاز است. حداکثر اندازه مجاز ۳ مگابایت می‌باشد.');
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

        // Toggle session attendees list in history
        function toggleAttendees(sessionId) {
            const list = document.getElementById('att-list-' + sessionId);
            const btn = document.getElementById('att-toggle-btn-' + sessionId);
            if (!list || !btn) return;
            const isVisible = list.style.display !== 'none';
            list.style.display = isVisible ? 'none' : 'flex';
            list.style.flexWrap = 'wrap';
            list.style.gap = '10px';
            // Update button icon/label
            if (isVisible) {
                btn.innerHTML = '<i class="bi bi-people-fill me-1"></i>مشاهده حاضرین';
                btn.style.background = 'rgba(139,92,246,0.15)';
            } else {
                btn.innerHTML = '<i class="bi bi-people-fill me-1"></i>بستن لیست حاضرین';
                btn.style.background = 'rgba(139,92,246,0.3)';
            }
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
            document.addEventListener('DOMContentLoaded', function () {
                const noteBtn = document.querySelector('[onclick*="classnotes"]');
                if (noteBtn) noteBtn.click();
            });
        @endif

        // Auto-open Curriculum tab on lesson success
        @if(session('lesson_success'))
            document.addEventListener('DOMContentLoaded', function () {
                const currBtn = document.querySelector('[onclick*="curriculum"]');
                if (currBtn) currBtn.click();
            });
        @endif

        // Auto-open Documents tab if session success or validation error targets it
        @if(session('doc_success') || $errors->has('file') || $errors->has('title') || $errors->has('description') || $errors->has('lesson_id'))
            document.addEventListener('DOMContentLoaded', function () {
                const docBtn = document.querySelector('[onclick*="documents"]');
                if (docBtn) docBtn.click();
            });
        @endif

        // Live Class Session Duration and Auto-close Check
        @if(isset($activeSession) && $activeSession)
            document.addEventListener('DOMContentLoaded', function () {
                const sessionStartedAt = new Date('{{ $activeSession->started_at->toISOString() }}');
                const durationEl = document.getElementById('sessionDuration');
                const autoCloseWarning = document.getElementById('autoCloseWarning');
                const autoCloseCountdownBadge = document.getElementById('autoCloseCountdownBadge');
                const autoCloseMessage = document.getElementById('autoCloseMessage');
                const autoCloseProgressBar = document.getElementById('autoCloseProgressBar');
                const studentJoinedBox = document.getElementById('studentJoinedBox');
                const participantCountEl = document.getElementById('liveParticipantCount');
                const courseId = {{ $course->id }};
                const sessionId = {{ $activeSession->id }};
                let liveParticipantCount = {{ $activeSessionParticipants }};
                let isAutoClosing = false;

                function formatCountdown(totalSecs) {
                    const mins = Math.floor(totalSecs / 60);
                    const secs = totalSecs % 60;
                    return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                }

                // ── Real-time HH:MM:SS timer and 3-Minute Auto-Cancel ────
                function updateSessionState() {
                    const now = new Date();
                    const diffMs = Math.max(0, now - sessionStartedAt);
                    const totalSecs = Math.floor(diffMs / 1000);
                    const hours = Math.floor(totalSecs / 3600);
                    const mins = Math.floor((totalSecs % 3600) / 60);
                    const secs = totalSecs % 60;

                    if (durationEl) {
                        durationEl.textContent =
                            String(hours).padStart(2, '0') + ':' +
                            String(mins).padStart(2, '0') + ':' +
                            String(secs).padStart(2, '0');
                    }

                    const timeoutLimit = 180; // 3 minutes = 180s
                    const remainingSecs = Math.max(0, timeoutLimit - totalSecs);

                    if (liveParticipantCount === 0) {
                        if (autoCloseWarning) autoCloseWarning.style.display = 'block';
                        if (studentJoinedBox) studentJoinedBox.style.display = 'none';

                        if (autoCloseCountdownBadge) {
                            autoCloseCountdownBadge.textContent = formatCountdown(remainingSecs);
                        }
                        if (autoCloseProgressBar) {
                            const pct = Math.max(0, Math.min(100, (remainingSecs / timeoutLimit) * 100));
                            autoCloseProgressBar.style.width = pct + '%';
                        }

                        // If 3 minutes pass and no student joined, auto-cancel immediately
                        if (remainingSecs <= 0 && !isAutoClosing) {
                            isAutoClosing = true;
                            if (autoCloseCountdownBadge) autoCloseCountdownBadge.textContent = '00:00';
                            if (autoCloseMessage) autoCloseMessage.textContent = '3 minutes elapsed with no attendees. Cancelling class session...';

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
                                    window.location.reload();
                                })
                                .catch(error => {
                                    console.log('Auto-close check error:', error);
                                    window.location.reload();
                                });
                        }
                    } else {
                        if (autoCloseWarning) autoCloseWarning.style.display = 'none';
                        if (studentJoinedBox) studentJoinedBox.style.display = 'block';
                    }
                }

                // Initial update + tick every second
                updateSessionState();
                setInterval(updateSessionState, 1000);

                // ── AJAX: poll participant count every 5 s ────────────────
                function pollParticipants() {
                    if (isAutoClosing) return;

                    fetch('{{ route("teacher.courses.sessions.participants", [$course->id, $activeSession->id]) }}', {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                        .then(r => r.json())
                        .then(data => {
                            liveParticipantCount = data.count;
                            if (participantCountEl) participantCountEl.textContent = data.count;
                            updateSessionState();
                        })
                        .catch(err => console.log('Participant poll error:', err));
                }

                // Poll immediately then every 5 s
                pollParticipants();
                setInterval(pollParticipants, 5000);
            });
        @endif

            function toggleCourseEnrollment(btn) {
                const url = btn.getAttribute('data-url');
                btn.disabled = true;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        btn.disabled = false;
                        if (data.success) {
                            const textSpan = document.getElementById('enrollmentStatusText');
                            const icon = btn.querySelector('i');
                            if (data.is_enrollment_closed) {
                                btn.className = 'btn btn-outline-warning rounded-pill px-3 py-2 fw-bold shadow-sm';
                                icon.className = 'bi bi-lock-fill me-1 text-warning';
                                if (textSpan) textSpan.textContent = 'ثبت‌نام بسته است (کلیک برای بازگشایی)';
                            } else {
                                btn.className = 'btn btn-outline-light rounded-pill px-3 py-2 fw-bold shadow-sm';
                                icon.className = 'bi bi-unlock-fill me-1';
                                if (textSpan) textSpan.textContent = 'بستن ثبت‌نام';
                            }
                            if (typeof showNotificationToast === 'function') {
                                showNotificationToast(data.message, 'success');
                            } else {
                                alert(data.message);
                            }
                        }
                    })
                    .catch(err => {
                        btn.disabled = false;
                        console.error(err);
                    });
            }

        function copyReferralLink() {
            const input = document.getElementById('referralInviteUrl');
            if (!input) return;

            const copyText = input.value;
            const btnText = document.getElementById('copyReferralText');
            const icon = document.getElementById('copyReferralIcon');

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(copyText).then(() => {
                    onCopySuccess();
                }).catch(() => {
                    fallbackCopy(input);
                });
            } else {
                fallbackCopy(input);
            }

            function fallbackCopy(inp) {
                inp.select();
                inp.setSelectionRange(0, 99999);
                try {
                    document.execCommand('copy');
                    onCopySuccess();
                } catch (e) {
                    alert('لطفاً دستی کپی کنید: ' + copyText);
                }
            }

            function onCopySuccess() {
                if (btnText) btnText.textContent = 'کپی شد!';
                if (icon) icon.className = 'bi bi-check2';
                if (typeof showNotificationToast === 'function') {
                    showNotificationToast('لینک دعوت دوره با موفقیت در کلیپ‌بورد کپی شد!', 'success');
                }
                setTimeout(() => {
                    if (btnText) btnText.textContent = 'کپی لینک';
                    if (icon) icon.className = 'bi bi-clipboard';
                }, 2500);
            }
        }

        function copyReferralCodeOnly() {
            const codeEl = document.getElementById('referralCodeDisplay');
            if (!codeEl) return;
            const code = codeEl.textContent.trim();
            const icon = document.getElementById('copyCodeOnlyIcon');

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(code).then(() => {
                    if (icon) {
                        icon.className = 'bi bi-check2 text-success';
                        setTimeout(() => { icon.className = 'bi bi-clipboard'; }, 2000);
                    }
                    if (typeof showNotificationToast === 'function') {
                        showNotificationToast('کد معرف کپی شد: ' + code, 'success');
                    }
                }).catch(() => {
                    prompt('کپی کد معرف:', code);
                });
            } else {
                prompt('کپی کد معرف:', code);
            }
        }

        function regenerateReferralCode() {
            if (!confirm('آیا از ایجاد کد معرف جدید اطمینان دارید؟ لینک قبلی دیگر بازدیدهای جدید را ردیابی نخواهد کرد.')) {
                return;
            }

            const btn = document.getElementById('regenerateReferralBtn');
            const icon = document.getElementById('regenerateIcon');
            if (btn) btn.disabled = true;
            if (icon) icon.style.animation = 'spin 0.8s linear infinite';

            fetch('{{ route("teacher.courses.referral.regenerate", $course->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(r => r.json())
                .then(data => {
                    if (btn) btn.disabled = false;
                    if (icon) icon.style.animation = '';

                    if (data.success) {
                        const input = document.getElementById('referralInviteUrl');
                        const display = document.getElementById('referralCodeDisplay');
                        if (input) input.value = data.url;
                        if (display) display.textContent = data.code;

                        if (typeof showNotificationToast === 'function') {
                            showNotificationToast(data.message || 'کد معرف جدید با موفقیت ایجاد شد!', 'success');
                        } else {
                            alert(data.message || 'کد معرف جدید با موفقیت ایجاد شد!');
                        }
                    } else {
                        alert(data.message || 'خطا در ایجاد کد معرف جدید.');
                    }
                })
                .catch(err => {
                    if (btn) btn.disabled = false;
                    if (icon) icon.style.animation = '';
                    console.error('Referral regenerate error:', err);
                    alert('خطایی در تولید کد معرف رخ داد.');
                });
        }

        // Translate Course Details using Gemini AI
        window.translateCourseDetailsWithGemini = function () {
            const btn = document.getElementById('btnGeminiTranslate');
            const heroTitle = document.getElementById('courseHeroTitle');
            const topbarTitle = document.getElementById('courseTopbarTitle');
            const heroDesc = document.getElementById('courseHeroDesc');

            if (!btn) return;

            if (!confirm('آیا مایلید عنوان و توضیحات این دوره با استفاده از هوش مصنوعی Gemini به زبان فارسی روان ترجمه و ذخیره شود؟')) {
                return;
            }

            const originalBtnHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="width:0.85rem;height:0.85rem;"></span> در حال ترجمه با Gemini...`;

            fetch('{{ route("teacher.courses.ai-translate", $course->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    include_lessons: false,
                    save: true
                })
            })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;

                    if (data.success) {
                        if (heroTitle && data.title) {
                            heroTitle.textContent = data.title;
                            heroTitle.style.transition = 'all 0.5s ease';
                            heroTitle.style.color = '#00f0ff';
                            setTimeout(() => { heroTitle.style.color = ''; }, 2500);
                        }
                        if (topbarTitle && data.title) {
                            topbarTitle.textContent = data.title;
                        }
                        if (heroDesc && data.description) {
                            heroDesc.innerHTML = data.description.replace(/\n/g, '<br>');
                            heroDesc.style.transition = 'all 0.5s ease';
                            heroDesc.style.opacity = '0.5';
                            setTimeout(() => { heroDesc.style.opacity = '1'; }, 300);
                        }
                        document.title = (data.title || 'جزئیات دوره') + ' - ادورا تک';

                        if (typeof showNotificationToast === 'function') {
                            showNotificationToast(data.message || 'ترجمه دوره با موفقیت به فارسی ذخیره شد.', 'success');
                        } else {
                            alert(data.message || 'ترجمه دوره با موفقیت به فارسی ذخیره شد.');
                        }
                    } else {
                        alert(data.message || 'خطا در ترجمه با هوش مصنوعی.');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;
                    console.error('Translation error:', err);
                    alert('خطایی در برقراری ارتباط با سرور برای ترجمه رخ داد.');
                });
        };

        // Global Theme Switcher for Teacher Course Detail
        window.toggleEdvoraTheme = function () {
            const isDark = document.documentElement.classList.toggle('dark');
            if (isDark) {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('edvora_theme', 'dark');
                localStorage.setItem('edvora_student_theme', 'dark');
            } else {
                document.documentElement.removeAttribute('data-theme');
                localStorage.setItem('edvora_theme', 'light');
                localStorage.setItem('edvora_student_theme', 'light');
            }
        };
    </script>
@endpush