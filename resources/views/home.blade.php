@extends('layouts.app')

@section('title', 'Edvora Tech - Free Practical Skills for Afghan Women')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/home-hero.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/home-value-proposition.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/courses.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/popular-courses.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/teachers.css') }}" />
    <link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/home-roadmap.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/beta-notice.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/statistics.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/continue-learning.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/recent-courses.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/home-course-showcase.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/home-new-sections.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/home-ai-section.css') }}" />
@endpush

@section('content')
    <section class="hero2">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="7000" data-bs-pause="hover">
            <div class="carousel-inner">

                {{-- Slide 1: Main value proposition --}}
                <div class="carousel-item active">
                    <div class="hero2__glow hero2__glow--a"></div>
                    <div class="hero2__glow hero2__glow--b"></div>
                    <div class="hero2__glow hero2__glow--c"></div>
                    <div class="container">
                        <div class="hero2__inner">
                            <div class="hero2__content">
                                <span class="hero2__eyebrow"><i class="bi bi-stars"></i> Edvora Tech Platform</span>
                                <h1 class="hero2__title">Everything You Need to Learn.<br><span class="gradient-text">100% Free Forever.</span></h1>
                                <p class="hero2__desc">Master high-demand tech skills with interactive courses, hands-on projects, expert mentors, and a thriving global community — completely free of charge.</p>

                                <div class="hero2__features">
                                    <div class="hero2__feature"><i class="bi bi-people-fill"></i><span>Expert Mentors</span></div>
                                    <div class="hero2__feature"><i class="bi bi-code-slash"></i><span>Interactive Labs</span></div>
                                    <div class="hero2__feature"><i class="bi bi-award-fill"></i><span>Verified Certificates</span></div>
                                    <div class="hero2__feature"><i class="bi bi-book-fill"></i><span>Best Tech Books</span></div>
                                    <div class="hero2__feature"><i class="bi bi-globe2"></i><span>Global Community</span></div>
                                </div>

                                <div class="hero2__actions">
                                    <a href="{{ route('courses.index') }}" class="btn-hero-primary">
                                        Start Learning Free <i class="bi bi-arrow-right"></i>
                                    </a>
                                    <a href="{{ auth()->check() ? (auth()->user()->role === 'teacher' ? route('teacher.your-courses') : route('student.courses')) : route('register') }}" class="btn-hero-secondary">
                                        <i class="bi bi-rocket-takeoff-fill"></i> {{ auth()->check() ? 'My Dashboard' : 'Create Free Account' }}
                                    </a>
                                </div>

                                <div class="hero2__stats">
                                    <div class="hero2__stat">
                                        <span class="hero2__stat-num" data-target="{{ $totalStudents }}">{{ $totalStudents }}</span>
                                        <span class="hero2__stat-label">Active Learners</span>
                                    </div>
                                    <div class="hero2__stat">
                                        <span class="hero2__stat-num" data-target="{{ $totalCourses }}">{{ $totalCourses }}</span>
                                        <span class="hero2__stat-label">Free Courses</span>
                                    </div>
                                    <div class="hero2__stat">
                                        <span class="hero2__stat-num" data-target="{{ $completedCourses }}">{{ $completedCourses }}</span>
                                        <span class="hero2__stat-label">Graduated</span>
                                    </div>
                                </div>
                            </div>

                            <div class="hero2__visual">
                                <!-- Floating Live Badge -->
                                <div class="hero2__badge-floating">
                                    <div class="hero2__pulse-dot"></div>
                                    <div class="hero2__badge-text">
                                        <span class="hero2__badge-title">Interactive Learning</span>
                                        <span class="hero2__badge-sub">Join +2,500 Active Students</span>
                                    </div>
                                </div>

                                <!-- Central Showcase Card -->
                                <div class="hero2__card-showcase">
                                    <div class="hero2__card-header">
                                        <div class="hero2__card-badge"><i class="bi bi-lightning-charge-fill"></i> Live Class</div>
                                        <div class="hero2__card-dots">
                                            <div class="hero2__card-dot hero2__card-dot--red"></div>
                                            <div class="hero2__card-dot hero2__card-dot--yellow"></div>
                                            <div class="hero2__card-dot hero2__card-dot--green"></div>
                                        </div>
                                    </div>
                                    <div class="hero2__card-media">
                                        <img src="{{ asset('assets/images/hero_logo_design.png') }}" alt="Edvora Tech" class="hero2__card-img" fetchpriority="high">
                                        <div class="hero2__play-btn"><i class="bi bi-play-fill"></i></div>
                                    </div>
                                    <div class="hero2__card-title">Full-Stack Development & AI</div>
                                    <div class="hero2__card-sub">
                                        <span><i class="bi bi-mortarboard-fill text-info"></i> Hands-on Labs</span>
                                        <span><i class="bi bi-patch-check-fill text-success"></i> Free Cert</span>
                                    </div>
                                </div>

                                <!-- Floating Code Snippet Widget -->
                                <div class="hero2__code-widget">
                                    <div class="hero2__code-header">
                                        <span class="hero2__code-lang">app.js</span>
                                        <i class="bi bi-code-square text-info" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <div class="hero2__code-content"><span id="heroCodeTyped"></span><span class="hero2__code-cursor"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 2: Featured Course --}}
                @if($featuredCourses->isNotEmpty())
                    @php $fc = $featuredCourses->first(); @endphp
                    <div class="carousel-item">
                        <div class="hero2__glow hero2__glow--a"></div>
                        <div class="hero2__glow hero2__glow--b"></div>
                        <div class="container">
                            <div class="hero2__inner">
                                <div class="hero2__content">
                                    <span class="hero2__eyebrow"><i class="bi bi-award-fill"></i> {{ $fc->category->name ?? 'Featured Course' }}</span>
                                    <h1 class="hero2__title">{{ \Illuminate\Support\Str::limit($fc->title, 40) }}<br><span class="gradient-text">Featured Masterclass</span></h1>
                                    <p class="hero2__desc">{{ \Illuminate\Support\Str::limit($fc->description, 140) }}</p>

                                    <div class="hero2__features">
                                        <div class="hero2__feature"><i class="bi bi-bar-chart-fill"></i><span>{{ ucfirst($fc->level) }}</span></div>
                                        <div class="hero2__feature"><i class="bi bi-clock-fill"></i><span>{{ $fc->duration_hours }} Hours</span></div>
                                        <div class="hero2__feature"><i class="bi bi-people-fill"></i><span>{{ $fc->enrolled_count }} Enrolled</span></div>
                                        @if($fc->has_certificate)
                                            <div class="hero2__feature"><i class="bi bi-patch-check-fill"></i><span>Certificate</span></div>
                                        @endif
                                    </div>

                                    <div class="hero2__actions">
                                        <a href="{{ route('courses.detail', $fc->slug) }}" class="btn-hero-primary">
                                            Explore Course <i class="bi bi-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="hero2__visual">
                                    <div class="hero2__card-showcase">
                                        <div class="hero2__card-header">
                                            <div class="hero2__card-badge"><i class="bi bi-star-fill text-warning"></i> Featured</div>
                                            <div class="hero2__card-dots">
                                                <div class="hero2__card-dot hero2__card-dot--red"></div>
                                                <div class="hero2__card-dot hero2__card-dot--yellow"></div>
                                                <div class="hero2__card-dot hero2__card-dot--green"></div>
                                            </div>
                                        </div>
                                        <div class="hero2__card-media">
                                            <img src="{{ $fc->thumbnail ? asset('storage/' . $fc->thumbnail) : asset('assets/images/hero_logo_design.png') }}" alt="{{ $fc->title }}" class="hero2__card-img" style="object-fit: cover;">
                                            <div class="hero2__play-btn"><i class="bi bi-play-fill"></i></div>
                                        </div>
                                        <div class="hero2__card-title">{{ \Illuminate\Support\Str::limit($fc->title, 32) }}</div>
                                        <div class="hero2__card-sub">
                                            <span><i class="bi bi-person-fill text-info"></i> {{ $fc->teacher->name ?? 'Edvora Mentor' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Controls & Indicators -->
            @php
                $hasFeatured = $featuredCourses->isNotEmpty();
                $heroSlideNum = 0;
            @endphp
            @if($hasFeatured)
                <div class="hero2__navigation-container container">
                    <button class="hero2__arrow hero2__arrow--prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Previous">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="hero2__arrow hero2__arrow--next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Next">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                <div class="carousel-indicators hero2__indicators">
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                </div>
            @endif

            <!-- Tech Marquee Bar -->
            <div class="hero2__marquee-wrap">
                <div class="hero2__marquee-track">
                    <div class="hero2__marquee-item"><i class="bi bi-filetype-py"></i> Python Programming</div>
                    <div class="hero2__marquee-item"><i class="bi bi-code-slash"></i> Web Development</div>
                    <div class="hero2__marquee-item"><i class="bi bi-cpu-fill"></i> Artificial Intelligence</div>
                    <div class="hero2__marquee-item"><i class="bi bi-palette-fill"></i> UI/UX Design</div>
                    <div class="hero2__marquee-item"><i class="bi bi-shield-lock-fill"></i> Cyber Security</div>
                    <div class="hero2__marquee-item"><i class="bi bi-diagram-3-fill"></i> Data Science</div>
                    <div class="hero2__marquee-item"><i class="bi bi-filetype-php"></i> Laravel & PHP</div>
                    <div class="hero2__marquee-item"><i class="bi bi-braces"></i> JavaScript & React</div>
                    <!-- Duplicate for infinite seamless scroll -->
                    <div class="hero2__marquee-item"><i class="bi bi-filetype-py"></i> Python Programming</div>
                    <div class="hero2__marquee-item"><i class="bi bi-code-slash"></i> Web Development</div>
                    <div class="hero2__marquee-item"><i class="bi bi-cpu-fill"></i> Artificial Intelligence</div>
                    <div class="hero2__marquee-item"><i class="bi bi-palette-fill"></i> UI/UX Design</div>
                    <div class="hero2__marquee-item"><i class="bi bi-shield-lock-fill"></i> Cyber Security</div>
                    <div class="hero2__marquee-item"><i class="bi bi-diagram-3-fill"></i> Data Science</div>
                    <div class="hero2__marquee-item"><i class="bi bi-filetype-php"></i> Laravel & PHP</div>
                    <div class="hero2__marquee-item"><i class="bi bi-braces"></i> JavaScript & React</div>
                </div>
            </div>

        </div>
    </section>

    <section class="home-value-steps" id="how-edvora-works">
        <div class="container">
            <div class="home-value-steps__intro"><span>Made for your next step</span><h2>Everything you need to begin</h2><p>Choose a skill, learn with expert guidance, and turn your progress into confidence.</p></div>
            <div class="row g-4">
                <div class="col-md-4"><article class="home-value-step-card"><span class="home-value-step-card__number">01</span><div class="home-value-step-card__icon"><i class="bi bi-compass"></i></div><h3>Choose a practical skill</h3><p>Explore technology, computer, and career skills designed for real-world opportunities.</p></article></div>
                <div class="col-md-4"><article class="home-value-step-card"><span class="home-value-step-card__number">02</span><div class="home-value-step-card__icon"><i class="bi bi-play-circle"></i></div><h3>Learn for free</h3><p>Study structured lessons at your own pace with no course fees.</p></article></div>
                <div class="col-md-4"><article class="home-value-step-card"><span class="home-value-step-card__number">03</span><div class="home-value-step-card__icon"><i class="bi bi-rocket-takeoff"></i></div><h3>Build your future</h3><p>Grow your confidence and take your next step toward education, work, and independence.</p></article></div>
            </div>
        </div>
    </section>

    @if($recentCourses->isNotEmpty())
    <section class="home-course-showcase">
        <div class="container">
            <div class="home-course-showcase__header">
                <span class="home-course-showcase__eyebrow"><i class="bi bi-clock-history"></i> Just added</span>
                <h2>Recent Courses</h2>
                <p>Start with the newest practical skills available on Edvora.</p>
            </div>

            <div class="recent-courses-carousel" id="recentCoursesCarousel">
                <button class="recent-courses-carousel__nav recent-courses-carousel__nav--prev" type="button" aria-label="Previous" onclick="recentCoursesScroll(-1)"><i class="bi bi-chevron-left"></i></button>
                <button class="recent-courses-carousel__nav recent-courses-carousel__nav--next" type="button" aria-label="Next" onclick="recentCoursesScroll(1)"><i class="bi bi-chevron-right"></i></button>
                <div class="recent-courses-carousel__track" id="recentCoursesTrack">
                    @foreach($recentCourses as $course)
                        @php
                            $today = \Carbon\Carbon::today();
                            $startDate = $course->start_date ? \Carbon\Carbon::parse($course->start_date)->startOfDay() : null;
                            $endDate = $course->end_date ? \Carbon\Carbon::parse($course->end_date)->startOfDay() : null;

                            $dateStatus = [
                                'type' => 'open',
                                'badge_text' => 'Open Course',
                                'countdown_text' => 'Flexible Schedule',
                                'icon' => 'bi bi-lightning-charge-fill',
                                'pulse' => false,
                            ];

                            if ($startDate && $startDate->greaterThan($today)) {
                                $daysUntilStart = (int) $today->diffInDays($startDate, false);
                                $dateStatus['type'] = 'upcoming';
                                $dateStatus['icon'] = 'bi bi-rocket-takeoff-fill';
                                $dateStatus['pulse'] = true;

                                if ($daysUntilStart === 0) {
                                    $dateStatus['badge_text'] = 'Starts Today';
                                    $dateStatus['countdown_text'] = 'Starts today';
                                } elseif ($daysUntilStart === 1) {
                                    $dateStatus['badge_text'] = 'Starts Tomorrow';
                                    $dateStatus['countdown_text'] = '1 day left until start';
                                } else {
                                    $dateStatus['badge_text'] = "Starts in {$daysUntilStart} days";
                                    $dateStatus['countdown_text'] = "{$daysUntilStart} days left until start";
                                }
                            } elseif (
                                (($startDate && $startDate->lessThanOrEqualTo($today)) || $course->started_at !== null || $course->status === 'started')
                                && ($endDate && $endDate->greaterThanOrEqualTo($today))
                            ) {
                                $daysUntilEnd = (int) $today->diffInDays($endDate, false);
                                $dateStatus['type'] = 'ongoing';
                                $dateStatus['icon'] = 'bi bi-hourglass-split';
                                $dateStatus['pulse'] = true;

                                if ($daysUntilEnd === 0) {
                                    $dateStatus['badge_text'] = 'Ends Today';
                                    $dateStatus['countdown_text'] = 'Ends today';
                                } elseif ($daysUntilEnd === 1) {
                                    $dateStatus['badge_text'] = 'Ends Tomorrow';
                                    $dateStatus['countdown_text'] = '1 day left until class ends';
                                } else {
                                    $dateStatus['badge_text'] = "Ends in {$daysUntilEnd} days";
                                    $dateStatus['countdown_text'] = "{$daysUntilEnd} days left until class ends";
                                }
                            } elseif (($endDate && $endDate->lessThan($today)) || in_array($course->status, ['archived', 'completed'])) {
                                $dateStatus['type'] = 'finished';
                                $dateStatus['icon'] = 'bi bi-check2-circle';
                                $dateStatus['badge_text'] = 'Completed';
                                $dateStatus['countdown_text'] = 'Course completed';
                                $dateStatus['pulse'] = false;
                            } elseif ($startDate && $startDate->lessThanOrEqualTo($today)) {
                                $dateStatus['type'] = 'ongoing';
                                $dateStatus['icon'] = 'bi bi-play-circle-fill';
                                $dateStatus['badge_text'] = 'In Progress';
                                $dateStatus['countdown_text'] = 'Class in progress';
                                $dateStatus['pulse'] = true;
                            }
                        @endphp
                        <div class="recent-courses-carousel__item">
                            <article class="home-course-card {{ $course->is_featured ? 'is-featured-course' : '' }}">
                                <div class="home-course-card__image">
                                    <!-- Floating Live Status Badge -->
                                    <div class="edvora-floating-status-pill status-{{ $dateStatus['type'] }}" style="top: 10px; left: 10px; position: absolute; z-index: 5;">
                                        @if($dateStatus['pulse'])
                                            <span class="status-live-dot"></span>
                                        @endif
                                        <i class="{{ $dateStatus['icon'] }}"></i>
                                        <span>{{ $dateStatus['badge_text'] }}</span>
                                    </div>

                                    @if($course->is_featured)
                                        <!-- VIP Floating Badge -->
                                        <span class="home-course-card__vip-badge">
                                            <i class="bi bi-star-fill"></i> VIP
                                        </span>
                                    @endif

                                    @if($course->thumbnail)
                                        <img src="{{ Str::startsWith($course->thumbnail, ['http://', 'https://']) ? $course->thumbnail : asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" loading="lazy" decoding="async" style="object-fit: cover;">
                                    @else
                                        <i class="bi {{ $course->category?->icon ?? 'bi-code-slash' }}"></i>
                                    @endif
                                    <span class="home-course-card__tag" style="top: auto; bottom: 10px; left: 10px;">{{ $course->category->name ?? 'General' }}</span>
                                    <span class="home-course-card__free">Free</span>
                                </div>
                                <div class="home-course-card__body">
                                    <div class="home-course-card__teacher"><i class="bi bi-person-circle"></i>{{ $course->teacher->name ?? 'Edvora Instructor' }}</div>
                                    <h3 class="home-course-card__title"><a href="{{ route('courses.detail', $course->slug) }}">{{ $course->title }}</a></h3>

                                    <!-- Schedule & Countdown Capsule -->
                                    <div class="edvora-schedule-capsule schedule-{{ $dateStatus['type'] }}" style="margin-bottom: 12px; padding: 9px 12px;">
                                        <div class="capsule-countdown-row">
                                            <i class="{{ $dateStatus['icon'] }}"></i>
                                            <span class="countdown-text">{{ $dateStatus['countdown_text'] }}</span>
                                        </div>
                                        <div class="capsule-date-row">
                                            <i class="bi bi-calendar-event"></i>
                                            <span>
                                                @if($course->start_date && $course->end_date)
                                                    {{ $course->start_date->format('M d') }} - {{ $course->end_date->format('M d, Y') }}
                                                @elseif($course->start_date)
                                                    Starts {{ $course->start_date->format('M d, Y') }}
                                                @elseif($course->end_date)
                                                    Ends {{ $course->end_date->format('M d, Y') }}
                                                @else
                                                    Flexible Schedule
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    <p class="home-course-card__description">{{ Str::limit(strip_tags($course->description), 90) }}</p>
                                    <div class="home-course-card__footer">
                                        <span><i class="bi bi-clock"></i>{{ $course->duration_hours ? $course->duration_hours . ' hours' : 'Self-paced' }}</span>
                                        <a href="{{ route('courses.detail', $course->slug) }}">View course <i class="bi bi-arrow-right"></i></a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="home-course-showcase__cta"><a href="{{ route('courses.index') }}">Explore all courses <i class="bi bi-arrow-right"></i></a></div>
        </div>
    </section>
    @endif

    @if($popularCourses->isNotEmpty())
    <section class="home-course-showcase home-course-showcase--popular">
        <div class="container">
            <div class="home-course-showcase__header">
                <span class="home-course-showcase__eyebrow"><i class="bi bi-trophy-fill"></i> Top Rated Courses</span>
                <h2>Popular Courses</h2>
                <p>The most enrolled and highest-rated courses on Edvora.</p>
            </div>

            <div class="popular-courses-bordered">
                <div class="row g-4">
                    @foreach($popularCourses as $course)
                        @php
                            $today = \Carbon\Carbon::today();
                            $startDate = $course->start_date ? \Carbon\Carbon::parse($course->start_date)->startOfDay() : null;
                            $endDate = $course->end_date ? \Carbon\Carbon::parse($course->end_date)->startOfDay() : null;

                            $dateStatus = [
                                'type' => 'open',
                                'badge_text' => 'Open Course',
                                'countdown_text' => 'Flexible Schedule',
                                'icon' => 'bi bi-lightning-charge-fill',
                                'pulse' => false,
                            ];

                            if ($startDate && $startDate->greaterThan($today)) {
                                $daysUntilStart = (int) $today->diffInDays($startDate, false);
                                $dateStatus['type'] = 'upcoming';
                                $dateStatus['icon'] = 'bi bi-rocket-takeoff-fill';
                                $dateStatus['pulse'] = true;

                                if ($daysUntilStart === 0) {
                                    $dateStatus['badge_text'] = 'Starts Today';
                                    $dateStatus['countdown_text'] = 'Starts today';
                                } elseif ($daysUntilStart === 1) {
                                    $dateStatus['badge_text'] = 'Starts Tomorrow';
                                    $dateStatus['countdown_text'] = '1 day left until start';
                                } else {
                                    $dateStatus['badge_text'] = "Starts in {$daysUntilStart} days";
                                    $dateStatus['countdown_text'] = "{$daysUntilStart} days left until start";
                                }
                            } elseif (
                                (($startDate && $startDate->lessThanOrEqualTo($today)) || $course->started_at !== null || $course->status === 'started')
                                && ($endDate && $endDate->greaterThanOrEqualTo($today))
                            ) {
                                $daysUntilEnd = (int) $today->diffInDays($endDate, false);
                                $dateStatus['type'] = 'ongoing';
                                $dateStatus['icon'] = 'bi bi-hourglass-split';
                                $dateStatus['pulse'] = true;

                                if ($daysUntilEnd === 0) {
                                    $dateStatus['badge_text'] = 'Ends Today';
                                    $dateStatus['countdown_text'] = 'Ends today';
                                } elseif ($daysUntilEnd === 1) {
                                    $dateStatus['badge_text'] = 'Ends Tomorrow';
                                    $dateStatus['countdown_text'] = '1 day left until class ends';
                                } else {
                                    $dateStatus['badge_text'] = "Ends in {$daysUntilEnd} days";
                                    $dateStatus['countdown_text'] = "{$daysUntilEnd} days left until class ends";
                                }
                            } elseif (($endDate && $endDate->lessThan($today)) || in_array($course->status, ['archived', 'completed'])) {
                                $dateStatus['type'] = 'finished';
                                $dateStatus['icon'] = 'bi bi-check2-circle';
                                $dateStatus['badge_text'] = 'Completed';
                                $dateStatus['countdown_text'] = 'Course completed';
                                $dateStatus['pulse'] = false;
                            } elseif ($startDate && $startDate->lessThanOrEqualTo($today)) {
                                $dateStatus['type'] = 'ongoing';
                                $dateStatus['icon'] = 'bi bi-play-circle-fill';
                                $dateStatus['badge_text'] = 'In Progress';
                                $dateStatus['countdown_text'] = 'Class in progress';
                                $dateStatus['pulse'] = true;
                            }
                        @endphp
                        <div class="col-lg-4 col-md-6">
                            <article class="popular-course-card h-100 {{ $course->is_featured ? 'is-featured-course' : '' }}">
                                <div class="popular-course-card__media{{ $course->thumbnail ? '' : ' popular-course-card__media--' . ($course->category?->slug ?? 'general') }}">
                                    <!-- Floating Live Status Badge -->
                                    <div class="edvora-floating-status-pill status-{{ $dateStatus['type'] }}" style="top: 12px; left: 12px; position: absolute; z-index: 5;">
                                        @if($dateStatus['pulse'])
                                            <span class="status-live-dot"></span>
                                        @endif
                                        <i class="{{ $dateStatus['icon'] }}"></i>
                                        <span>{{ $dateStatus['badge_text'] }}</span>
                                    </div>

                                    @if($course->is_featured)
                                        <span class="popular-course-vip-tag">
                                            <i class="bi bi-star-fill"></i> VIP
                                        </span>
                                    @endif

                                    @if($course->thumbnail)
                                        <img src="{{ Str::startsWith($course->thumbnail, ['http://', 'https://']) ? $course->thumbnail : asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" loading="lazy" decoding="async" style="object-fit: cover;">
                                    @endif
                                </div>
                                <div class="popular-course-card__body">
                                    <div class="popular-course-card__badges">
                                        @if($course->is_featured)
                                            <span class="popular-course-card__badge popular-course-card__badge--vip"><i class="bi bi-star-fill"></i> Featured</span>
                                        @endif
                                        <span class="popular-course-card__badge popular-course-card__badge--rank"><i class="bi bi-trophy-fill"></i> Top {{ $loop->iteration }}</span>
                                        <span class="popular-course-card__badge popular-course-card__badge--category"><i class="bi {{ $course->category?->icon ?? 'bi-grid' }}"></i> {{ $course->category?->name ?? 'General' }}</span>
                                        <span class="popular-course-card__badge popular-course-card__badge--free">Free</span>
                                    </div>
                                    <div class="popular-course-card__teacher">
                                        @if($course->teacher?->avatar ?? false)
                                            <img src="{{ Str::startsWith($course->teacher->avatar, ['http://', 'https://']) ? $course->teacher->avatar : asset('storage/' . $course->teacher->avatar) }}" class="popular-course-card__avatar" alt="{{ $course->teacher->name }}" loading="lazy" decoding="async">
                                        @else
                                            <span class="popular-course-card__avatar popular-course-card__avatar--initials">{{ strtoupper(substr($course->teacher?->name ?? 'Edvora Instructor', 0, 1)) }}</span>
                                        @endif
                                        <span>{{ $course->teacher?->name ?? 'Edvora Instructor' }}</span>
                                    </div>
                                    <h3 class="popular-course-card__title"><a href="{{ route('courses.detail', $course->slug) }}">{{ $course->title }}</a></h3>
                                    <div class="popular-course-card__rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= round($course->rating ?? 0) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                        <span class="popular-course-card__rating-value">{{ number_format($course->rating ?? 0, 1) }}</span>
                                        <span class="popular-course-card__rating-count">({{ $course->total_reviews ?? 0 }})</span>
                                    </div>

                                    <!-- Schedule & Countdown Capsule -->
                                    <div class="edvora-schedule-capsule schedule-{{ $dateStatus['type'] }}" style="margin-bottom: 14px; padding: 10px 13px;">
                                        <div class="capsule-countdown-row">
                                            <i class="{{ $dateStatus['icon'] }}"></i>
                                            <span class="countdown-text">{{ $dateStatus['countdown_text'] }}</span>
                                        </div>
                                        <div class="capsule-date-row">
                                            <i class="bi bi-calendar-event"></i>
                                            <span>
                                                @if($course->start_date && $course->end_date)
                                                    {{ $course->start_date->format('M d') }} - {{ $course->end_date->format('M d, Y') }}
                                                @elseif($course->start_date)
                                                    Starts {{ $course->start_date->format('M d, Y') }}
                                                @elseif($course->end_date)
                                                    Ends {{ $course->end_date->format('M d, Y') }}
                                                @else
                                                    Flexible Schedule
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    <p class="popular-course-card__description">{{ Str::limit(strip_tags($course->description), 90) }}</p>
                                    <div class="popular-course-card__meta">
                                        <span><i class="bi bi-people"></i>{{ $course->enrollments_count ?? 0 }} learners</span>
                                        <span><i class="bi bi-clock"></i>{{ $course->duration_hours ? $course->duration_hours . 'h' : 'Self-paced' }}</span>
                                        @if($course->level)
                                            <span><i class="bi bi-bar-chart"></i>{{ ucfirst($course->level) }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('courses.detail', $course->slug) }}" class="popular-course-card__cta">View Course <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="home-course-showcase__cta"><a href="{{ route('courses.index') }}">View all courses <i class="bi bi-arrow-right"></i></a></div>
        </div>
    </section>
    @endif

    <section class="py-5 statistics-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold" style="color: #1f8fff">Our Achievements</h2>
                <p class="text-muted">Real numbers from our growing learning community</p>
            </div>

            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="stat-number">{{ $totalCourses }}</div>
                        <div class="stat-label">Total Courses</div>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div class="stat-number">{{ $completedCourses }}</div>
                        <div class="stat-label">Completed Courses</div>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-mortarboard"></i>
                        </div>
                        <div class="stat-number">{{ $totalTeachers }}</div>
                        <div class="stat-label">Expert Teachers</div>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stat-number">{{ $totalStudents }}</div>
                        <div class="stat-label">Active Students</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Edvora AI Chatbot Banner Section --}}
    <section class="home-ai-section">
        <div class="container">
            <div class="home-ai-hero-card">
                <div class="home-ai-hero-card__glow"></div>
                <div class="row g-5 align-items-center position-relative">
                    <div class="col-lg-7">
                        <span class="home-ai-badge">
                            <i class="bi bi-stars"></i> Edvora AI — New Feature
                        </span>
                        <h2 class="home-ai-title">
                            Get Answers Faster<br>
                            <span class="home-ai-title-gradient">with Edvora AI Assistant!</span>
                        </h2>
                        <p class="home-ai-desc">
                            Edvora Tech's Smart Assistant has direct access to our <strong class="home-ai-highlight">live database</strong>, finding accurate and up-to-date information about courses, instructors, live classes, events, and free books in seconds.
                        </p>
                        <div class="home-ai-chips">
                            <button type="button" class="home-ai-chip-item" onclick="window.edvoraAiOpenWithTopic && window.edvoraAiOpenWithTopic('courses')">📚 Courses</button>
                            <button type="button" class="home-ai-chip-item" onclick="window.edvoraAiOpenWithTopic && window.edvoraAiOpenWithTopic('teachers')">👨‍🏫 Instructors</button>
                            <button type="button" class="home-ai-chip-item" onclick="window.edvoraAiOpenWithTopic && window.edvoraAiOpenWithTopic('classes')">🔴 Live Classes</button>
                            <button type="button" class="home-ai-chip-item" onclick="window.edvoraAiOpenWithTopic && window.edvoraAiOpenWithTopic('events')">🎉 Events</button>
                            <button type="button" class="home-ai-chip-item" onclick="window.edvoraAiOpenWithTopic && window.edvoraAiOpenWithTopic('books')">📖 Free Books</button>
                        </div>
                        <button type="button" id="homeAiOpenBtn" class="home-ai-cta-btn" onclick="document.getElementById('edvoraAiTrigger').click()">
                            <i class="bi bi-robot"></i>
                            Chat with Edvora AI Now
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block">
                        <div class="home-ai-preview-card">
                            <!-- Simulated chat preview -->
                            <div class="home-ai-preview-header">
                                <div class="home-ai-preview-avatar"><i class="bi bi-cpu-fill"></i></div>
                                <div>
                                    <div class="home-ai-preview-name">Edvora Smart Assistant</div>
                                    <div class="home-ai-preview-status">
                                        <span class="home-ai-status-dot"></span> Online — Live Database Access
                                    </div>
                                </div>
                            </div>
                            <div class="home-ai-preview-messages">
                                <div class="home-ai-preview-row home-ai-preview-row--user">
                                    <div class="home-ai-bubble-user">
                                        How many courses are available on the site?
                                    </div>
                                    <div class="home-ai-user-avatar"><i class="bi bi-person-fill"></i></div>
                                </div>
                                <div class="home-ai-preview-row">
                                    <div class="home-ai-bot-avatar"><i class="bi bi-robot"></i></div>
                                    <div class="home-ai-bubble-bot">
                                        There are currently <strong class="home-ai-highlight-text">{{ $totalCourses }} active courses</strong> available on Edvora Tech! All courses are completely <strong class="home-ai-highlight-success">free</strong>. 🎓
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-preview-section">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <span class="section-eyebrow"><i class="bi bi-info-circle-fill"></i> About Edvora</span>
                    <h2>A platform built by women, for women.</h2>
                    <p class="about-lead">
                        Edvora is more than a learning platform — it's a movement. Founded by a passionate team of Afghan women and allies, we believe that every woman deserves the chance to learn, grow, and build a future on her own terms. Our team of educators, developers, and designers work tirelessly to create courses that are practical, accessible, and empowering.
                    </p>
                    <div class="about-preview-stats">
                        <div class="about-preview-stat">
                            <div class="num">{{ $totalCourses }}</div>
                            <div class="label">Free Courses</div>
                        </div>
                        <div class="about-preview-stat">
                            <div class="num">{{ $totalTeachers }}</div>
                            <div class="label">Expert Teachers</div>
                        </div>
                        <div class="about-preview-stat">
                            <div class="num">{{ $totalStudents }}</div>
                            <div class="label">Active Students</div>
                        </div>
                    </div>
                    <a href="{{ route('about') }}" class="about-preview-cta">
                        <i class="bi bi-people-fill"></i> Meet Our Team & Learn More <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="col-lg-6">
                    <div class="about-visual-card">
                        <div class="about-visual-card__bg-glow"></div>
                        <div class="about-visual-card__badge">
                            <i class="bi bi-stars"></i>
                            <span>Created by Women, For Women</span>
                        </div>
                        
                        <div class="about-visual-canvas">
                            <div class="about-visual-centerpiece">
                                <div class="about-avatar-circle about-avatar-circle--main">
                                    <i class="bi bi-code-slash"></i>
                                </div>
                                <div class="about-orbit-ring about-orbit-ring--1"></div>
                                <div class="about-orbit-ring about-orbit-ring--2"></div>
                            </div>

                            <div class="about-floating-pill about-floating-pill--top-left">
                                <div class="pill-icon pill-icon--pink"><i class="bi bi-mortarboard-fill"></i></div>
                                <div class="pill-text">
                                    <strong>Practical Skills</strong>
                                    <small>Free & Self-Paced</small>
                                </div>
                            </div>

                            <div class="about-floating-pill about-floating-pill--bottom-left">
                                <div class="pill-icon pill-icon--purple"><i class="bi bi-shield-lock-fill"></i></div>
                                <div class="pill-text">
                                    <strong>100% Safe Space</strong>
                                    <small>Afghan Women Community</small>
                                </div>
                            </div>

                            <div class="about-floating-pill about-floating-pill--top-right">
                                <div class="pill-icon pill-icon--blue"><i class="bi bi-award-fill"></i></div>
                                <div class="pill-text">
                                    <strong>Verified Certificates</strong>
                                    <small>Build Your Portfolio</small>
                                </div>
                            </div>

                            <div class="about-code-snippet-box">
                                <div class="code-snippet-header">
                                    <span class="dot dot--red"></span>
                                    <span class="dot dot--yellow"></span>
                                    <span class="dot dot--green"></span>
                                    <span class="code-title">empower.py</span>
                                </div>
                                <div class="code-snippet-body">
                                    <code><span class="kw">class</span> <span class="cls">AfghanWomenLeader</span>:</code>
                                    <code>&nbsp;&nbsp;<span class="prop">skills</span> = [<span class="str">"Coding"</span>, <span class="str">"Network"</span>]</code>
                                    <code>&nbsp;&nbsp;<span class="prop">future</span> = <span class="str">"Bright & Free"</span> ✨</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="why-edvora-section">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-7">
                    <span class="section-eyebrow"><i class="bi bi-lightbulb-fill"></i> Why Edvora Exists</span>
                    <h2>Edvora was built to <span>open doors</span> that have been closed.</h2>
                    <p class="why-lead">
                        In a world where access to education is not equal, millions of women and girls are denied the opportunity to learn, grow, and build independent futures. Edvora was created to break down those barriers — to provide a free, safe, and supportive platform where Afghan women can learn practical digital and career skills at their own pace, from anywhere.
                    </p>

                    <div class="why-edvora-cards">
                        <div class="why-edvora-card">
                            <div class="why-edvora-card__icon"><i class="bi bi-shield-lock-fill"></i></div>
                            <h3>Safe & Accessible</h3>
                            <p>Learn from home with a platform designed for privacy, safety, and accessibility — no barriers, no judgment.</p>
                        </div>
                        <div class="why-edvora-card">
                            <div class="why-edvora-card__icon"><i class="bi bi-gift-fill"></i></div>
                            <h3>Completely Free</h3>
                            <p>Every course, every resource, every event is free. Education should never depend on your ability to pay.</p>
                        </div>
                        <div class="why-edvora-card">
                            <div class="why-edvora-card__icon"><i class="bi bi-people-fill"></i></div>
                            <h3>Community Driven</h3>
                            <p>Join a growing community of women learners supporting each other on their journey to independence.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="why-visual-showcase">
                        <div class="why-visual-bg"></div>
                        <div class="why-visual-card-main">
                            <div class="why-portal-icon">
                                <i class="bi bi-door-open-fill"></i>
                            </div>
                            <h3>Opening Doors to Tech</h3>
                            <p>Democratizing computer science education with zero financial barriers.</p>
                            
                            <div class="why-feature-list">
                                <div class="why-feature-item">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>Zero tuition fees forever</span>
                                </div>
                                <div class="why-feature-item">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>High-quality interactive labs</span>
                                </div>
                                <div class="why-feature-item">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>Career & freelance pathways</span>
                                </div>
                            </div>
                        </div>

                        <div class="why-floating-badge why-floating-badge--1">
                            <i class="bi bi-globe2 text-primary"></i>
                            <span>Global Network</span>
                        </div>
                        <div class="why-floating-badge why-floating-badge--2">
                            <i class="bi bi-rocket-takeoff-fill text-warning"></i>
                            <span>Infinite Growth</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="impact-women-section">
        <div class="container">
            <div class="text-center mb-2">
                <span class="section-eyebrow"><i class="bi bi-heart-fill"></i> Our Impact</span>
                <h2>How Edvora empowers women in our community</h2>
                <p class="impact-lead mx-auto">
                    When a woman learns a new skill, the impact ripples outward — to her family, her community, and the next generation. Here's how Edvora is creating change.
                </p>
            </div>

            <div class="impact-women-grid">
                <div class="impact-women-card">
                    <div class="impact-women-card__icon impact-women-card__icon--pink">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h3>Education Access</h3>
                    <p>Women who were denied formal education can now learn modern digital skills — from computer basics to programming — entirely online and for free.</p>
                </div>
                <div class="impact-women-card">
                    <div class="impact-women-card__icon impact-women-card__icon--blue">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <h3>Career Independence</h3>
                    <p>Practical, job-ready skills open pathways to remote work, freelancing, and entrepreneurship — giving women the ability to support themselves and their families.</p>
                </div>
                <div class="impact-women-card">
                    <div class="impact-women-card__icon impact-women-card__icon--purple">
                        <i class="bi bi-chat-heart-fill"></i>
                    </div>
                    <h3>Confidence & Community</h3>
                    <p>Through events and peer support, women build confidence, find their voice, and connect with a community that believes in their potential.</p>
                </div>
                <div class="impact-women-card">
                    <div class="impact-women-card__icon impact-women-card__icon--green">
                        <i class="bi bi-globe-asia-australia"></i>
                    </div>
                    <h3>Breaking Barriers</h3>
                    <p>Every woman who learns on Edvora becomes proof that talent has no borders. Together, we're reshaping what's possible for women in Afghanistan and beyond.</p>
                </div>
            </div>
        </div>
    </section>

    @if($upcomingEvents->isNotEmpty())
    <section class="events-section py-5">
        <div class="events-bg-effects" aria-hidden="true">
            <span class="event-particle particle-1"></span>
            <span class="event-particle particle-2"></span>
        </div>

        <div class="container position-relative">
            <div class="text-center mb-5 events-header">
                <h2 class="fw-bold mb-3 events-title">
                    <i class="bi bi-calendar-event me-3 events-icon"></i>
                    Upcoming Events
                </h2>

                <p class="text-muted fs-5 events-subtitle">
                    Join our exciting events and expand your knowledge
                </p>

                <div class="events-title-decoration"></div>
            </div>

            <div class="row g-4">
                @foreach($upcomingEvents as $event)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            @if($event->thumbnail)
                                <img src="{{ asset('storage/' . $event->thumbnail) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 200px; object-fit: cover;" loading="lazy" decoding="async">
                            @else
                                <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                    <i class="bi bi-calendar-event fs-1 text-muted"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title fw-bold">{{ $event->title }}</h5>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-calendar3 me-1"></i>{{ $event->start_date->format('M d, Y') }}
                                    <i class="bi bi-clock ms-2 me-1"></i>{{ $event->start_date->format('H:i') }}
                                </p>
                                <p class="card-text text-muted small">{{ \Illuminate\Support\Str::limit($event->description, 100) }}</p>
                                <a href="{{ route('events.detail', $event->slug) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-info-circle me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('events.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-right me-2"></i>View All Events
                </a>
            </div>
        </div>
    </section>
    @endif

    <section class="py-5 premium-roadmap-section">
        <div class="roadmap-bg-effects">
            <div class="roadmap-float-icon"><i class="bi bi-router"></i></div>
            <div class="roadmap-float-icon"><i class="bi bi-windows"></i></div>
            <div class="roadmap-float-icon"><i class="bi bi-keyboard"></i></div>
            <div class="roadmap-float-icon"><i class="bi bi-code-slash"></i></div>
            <div class="roadmap-float-icon"><i class="bi bi-ubuntu"></i></div>
            <div class="roadmap-float-icon"><i class="bi bi-shield-check"></i></div>

            <div class="roadmap-particle"></div>
            <div class="roadmap-particle"></div>
            <div class="roadmap-particle"></div>
            <div class="roadmap-particle"></div>
        </div>

        <div class="container" style="position: relative; z-index: 10">
            <div class="text-center mb-5 roadmap-header">
                <div class="roadmap-badge">
                    <i class="bi bi-map"></i>
                    <span>Learning Path</span>
                </div>
                <h2 class="fw-bold mb-3 roadmap-title">Computer Science Roadmap</h2>
                <p class="roadmap-subtitle">
                    Master Technology in 6 Progressive Stages
                </p>
            </div>

            <div class="compact-roadmap">
                <div class="roadmap-steps">
                    <div class="step-item">
                        <div class="step-image" aria-hidden="true">
                            <i class="bi bi-router"></i>
                        </div>
                        <div class="step-content">
                            <h6>Network</h6>
                            <small>2-3 months</small>
                            <p>Learn TCP/IP & routing basics</p>
                        </div>
                    </div>
                    <div class="step-arrow">→</div>

                    <div class="step-item">
                        <div class="step-image" aria-hidden="true">
                            <i class="bi bi-windows"></i>
                        </div>
                        <div class="step-content">
                            <h6>Windows</h6>
                            <small>1.5-2 months</small>
                            <p>Master Windows Server & AD</p>
                        </div>
                    </div>
                    <div class="step-arrow">→</div>

                    <div class="step-item">
                        <div class="step-image" aria-hidden="true">
                            <i class="bi bi-keyboard"></i>
                        </div>
                        <div class="step-content">
                            <h6>Typing</h6>
                            <small>1 month</small>
                            <p>Achieve 60+ WPM speed</p>
                        </div>
                    </div>
                    <div class="step-arrow">→</div>

                    <div class="step-item">
                        <div class="step-image" aria-hidden="true">
                            <i class="bi bi-code-slash"></i>
                        </div>
                        <div class="step-content">
                            <h6>Programming</h6>
                            <small>4-6 months</small>
                            <p>Build apps with Python & JS</p>
                        </div>
                    </div>
                    <div class="step-arrow">→</div>

                    <div class="step-item">
                        <div class="step-image" aria-hidden="true">
                            <i class="bi bi-ubuntu"></i>
                        </div>
                        <div class="step-content">
                            <h6>Linux</h6>
                            <small>3-4 months</small>
                            <p>Command line & server admin</p>
                        </div>
                    </div>
                    <div class="step-arrow">→</div>

                    <div class="step-item">
                        <div class="step-image" aria-hidden="true">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="step-content">
                            <h6>Security</h6>
                            <small>4-5 months</small>
                            <p>Ethical hacking & pen testing</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center roadmap-cta-container">
                <a href="{{ route('roadmap') }}" class="premium-cta-btn">
                    <div class="btn-shine-effect"></div>
                    <i class="bi bi-rocket-takeoff-fill me-3"></i>Explore Complete Learning Roadmap
                </a>
            </div>
        </div>
    </section>

    @if($hasTopStudents)
    <section class="top-students-preview">
        <div class="container">
            <div class="text-center mb-2">
                <span class="section-eyebrow"><i class="bi bi-trophy-fill"></i> Top Performers</span>
                <h2>Top Performing Students</h2>
                <p class="subtitle">Celebrating our academic achievers and their outstanding performance</p>
            </div>

            <div class="top-students-podium">
                @php
                    $rankClasses = ['gold', 'silver', 'bronze'];
                    $avatarColors = ['#ffd700', '#c0c0c0', '#cd7f32'];
                @endphp
                @foreach($topStudents as $index => $student)
                    <div class="top-student-card top-student-card--{{ $rankClasses[$index] ?? 'bronze' }}">
                        <div class="top-student-rank-badge top-student-rank-badge--{{ $rankClasses[$index] ?? 'bronze' }}">
                            {{ $index + 1 }}
                        </div>
                        <div class="top-student-avatar" style="background: {{ $avatarColors[$index] ?? '#cd7f32' }};">
                            {{ strtoupper(substr($student->name, 0, 1)) }}
                        </div>
                        <div class="top-student-name">{{ $student->name }}</div>
                        <div class="top-student-xp">{{ number_format($student->xp) }} XP</div>
                        <div class="top-student-xp-label">Total Experience</div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('leaderboard') }}" class="about-preview-cta">
                    <i class="bi bi-bar-chart-fill"></i> View Full Leaderboard <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
    @endif

    @auth
        @if(Auth::user()->role === 'student')
            <section class="py-5 continue-learning-section">
                <div class="container">
                    <h2 class="fw-bold mb-4 continue-learning-title">
                        Continue Your Learning
                    </h2>

                    @if($continueLearning->count() > 0)
                        <div class="row g-4">
                            @foreach($continueLearning as $enrollment)
                                <div class="col-lg-3 col-md-6">
                                    <div class="course-card-enhanced">
                                        <div class="continue-card-image">
                                            @if($enrollment->course->thumbnail)
                                                <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}"
                                                    alt="{{ $enrollment->course->title }}" loading="lazy" decoding="async">
                                            @else
                                                <div class="continue-card-placeholder-img">
                                                    <i class="bi {{ $enrollment->course->category->icon ?? 'bi-book' }}"></i>
                                                </div>
                                            @endif
                                            <div class="continue-card-progress-badge">{{ $enrollment->progress_percentage }}%</div>
                                        </div>
                                        <div class="card-body">
                                            <span
                                                class="continue-card-category">{{ $enrollment->course->category->name ?? 'General' }}</span>
                                            <h5 class="card-title">{{ $enrollment->course->title }}</h5>
                                            <p class="instructor-text">
                                                <i
                                                    class="bi bi-person-circle me-1"></i>{{ $enrollment->course->teacher->name ?? 'Instructor' }}
                                            </p>
                                            <div class="progress-container">
                                                <div class="progress-header">
                                                    <span>Progress</span>
                                                    <span>{{ $enrollment->progress_percentage }}%</span>
                                                </div>
                                                <div class="premium-progress">
                                                    <div class="premium-progress-bar"
                                                        data-progress="{{ $enrollment->progress_percentage }}"></div>
                                                </div>
                                            </div>
                                            <a href="{{ route('student.courses.learn', $enrollment->course->slug) }}"
                                                class="btn btn-premium">
                                                <i class="bi bi-play-circle me-2"></i>Continue Learning
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-section-placeholder"
                            style="background: rgba(255, 255, 255, 0.03); border-color: rgba(31, 143, 255, 0.2);">
                            <div class="empty-section-icon">
                                <i class="bi bi-journal-bookmark"></i>
                            </div>
                            <h4 style="color: white;">Start Your Learning Journey!</h4>
                            <p style="color: rgba(255,255,255,0.6);">Enroll in courses and your progress will appear here. Pick up right
                                where you left off!</p>
                            <a href="{{ route('courses.index') }}" class="btn btn-premium mt-3"
                                style="width: auto; display: inline-block; padding: 12px 30px;">
                                <i class="bi bi-search me-2"></i>Browse Courses
                            </a>
                        </div>
                    @endif
                </div>
            </section>
        @endif
    @endauth

    @if($approvedReviews->isNotEmpty())
    <section class="py-5 px-5 gray">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold" style="color: #1f8fff">What Our Students Say</h2>
                <p>Real feedback from our learning community</p>
            </div>

            <div class="row g-4">
                @foreach($approvedReviews as $review)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                            <i class="bi bi-person-fill fs-5"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $review->user->name ?? 'Anonymous' }}</h6>
                                        <small class="text-muted">{{ $review->course->title ?? '' }}</small>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }} text-warning"></i>
                                    @endfor
                                </div>
                                <p class="card-text text-muted small">"{{ $review->comment }}"</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="donation-home-section">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-7">
                    <span class="section-eyebrow"><i class="bi bi-heart-fill"></i> Support Edvora</span>
                    <h2>Your donation can <span>change a woman's future</span></h2>
                    <p class="donation-lead">
                        Edvora is and always will be free for our students. But keeping our platform running, creating new courses, and providing resources costs real money. Your support helps us continue this mission — every contribution, big or small, makes a direct impact on the lives of women learning with us.
                    </p>

                    <div class="donation-stats-row">
                        <div class="donation-stat-box">
                            <div class="num">${{ number_format($totalDonations) }}</div>
                            <div class="label">Total Raised</div>
                        </div>
                        <div class="donation-stat-box">
                            <div class="num">{{ number_format($totalSupporters) }}</div>
                            <div class="label">Supporters</div>
                        </div>
                        <div class="donation-stat-box">
                            <div class="num">{{ number_format($totalStudents) }}</div>
                            <div class="label">Students Helped</div>
                        </div>
                    </div>

                    <a href="{{ route('foundation') }}" class="donation-cta-btn">
                        <i class="bi bi-heart-fill"></i> Donate Now <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="col-lg-5">
                    <div class="donation-visual-card">
                        <div class="donation-visual-card__glow"></div>
                        <div class="donation-card-content">
                            <div class="donation-heart-icon">
                                <i class="bi bi-heart-fill"></i>
                            </div>
                            <h3>Edvora Foundation</h3>
                            <p>Transparent & Direct Impact</p>
                            <div class="donation-impact-bars">
                                <div class="impact-bar-item">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span class="text-light">Course & Curriculum</span>
                                        <strong class="text-info">100% Free</strong>
                                    </div>
                                    <div class="progress" style="height: 6px; background: rgba(255,255,255,0.1);">
                                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                                    </div>
                                </div>
                                <div class="impact-bar-item mt-3">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span class="text-light">Servers & Platform Infra</span>
                                        <strong class="text-warning">Funded by Donors</strong>
                                    </div>
                                    <div class="progress" style="height: 6px; background: rgba(255,255,255,0.1);">
                                        <div class="progress-bar bg-info" style="width: 85%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="donation-quote mt-4">
                                <i class="bi bi-quote fs-3 text-primary opacity-50"></i>
                                <p class="small mb-0">"Empowering one woman with education transforms an entire community."</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 light">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4" style="color: #1f8fff">Get In Touch</h2>
                    <form id="contactForm" data-url="{{ parse_url(route('contact.store'), PHP_URL_PATH) }}">
                        @csrf
                        <input type="hidden" name="category" value="general">
                        <div id="contactAlert" class="alert d-none mb-3"></div>
                        <div class="mb-3">
                            <label for="contactName" class="form-label text-muted">Full Name</label>
                            <input type="text" class="form-control" id="contactName" name="name"
                                value="{{ Auth::check() ? Auth::user()->name : '' }}" required />
                        </div>
                        <div class="mb-3">
                            <label for="contactEmail" class="form-label text-muted">Email Address</label>
                            <input type="email" class="form-control" id="contactEmail" name="email"
                                value="{{ Auth::check() ? Auth::user()->email : '' }}" required />
                        </div>
                        <div class="mb-3">
                            <label for="contactSubject" class="form-label text-muted">Subject</label>
                            <input type="text" class="form-control" id="contactSubject" name="subject" required />
                        </div>
                        <div class="mb-3">
                            <label for="contactMessage" class="form-label text-muted">Message</label>
                            <textarea class="form-control" id="contactMessage" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btnLink" id="contactSubmitBtn">
                            <span class="btn-text">Send Message</span>
                            <span class="btn-loading d-none">
                                <span class="spinner-border spinner-border-sm me-2"></span>Sending...
                            </span>
                        </button>
                    </form>
                </div>

                <div class="col-lg-6">
                    <h3 class="fw-bold mb-4" style="color: #1f8fff">
                        Need Help?
                    </h3>
                    <div class="contact-info">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-chat-square-text-fill fs-4 me-3" style="color: #1f8fff"></i>
                            <div>
                                <h6 class="mb-1">Send Us a Message</h6>
                                <p class="text-muted mb-0">Use the contact form to reach the Edvora team.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- VIP Course Announcement Popup Modal --}}
    @php
        $vipPromoCourse = (isset($featuredCourses) && $featuredCourses->isNotEmpty())
            ? $featuredCourses->first()
            : \App\Models\Course::with(['teacher', 'category'])->where('is_featured', true)->where('status', '!=', 'draft')->latest()->first();

        if ($vipPromoCourse) {
            $today = \Carbon\Carbon::today();
            $startDate = $vipPromoCourse->start_date ? \Carbon\Carbon::parse($vipPromoCourse->start_date)->startOfDay() : null;
            $endDate = $vipPromoCourse->end_date ? \Carbon\Carbon::parse($vipPromoCourse->end_date)->startOfDay() : null;

            // Schedule & Countdown
            $scheduleCountdown = 'Flexible Schedule';
            $scheduleDateFormatted = null;
            if ($startDate) {
                $scheduleDateFormatted = $startDate->format('M d, Y');
                if ($startDate->greaterThan($today)) {
                    $daysRemaining = (int) $today->diffInDays($startDate, false);
                    if ($daysRemaining === 0) {
                        $scheduleCountdown = 'Starts Today';
                    } elseif ($daysRemaining === 1) {
                        $scheduleCountdown = 'Starts Tomorrow (1 day left)';
                    } else {
                        $scheduleCountdown = "Starts in {$daysRemaining} days";
                    }
                } elseif ($startDate->equalTo($today)) {
                    $scheduleCountdown = 'Starts Today';
                } else {
                    $scheduleCountdown = 'Ongoing Course';
                }
            }

            // Duration (Bootcamp days / Weeks / Months / Hours)
            $durationFormatted = null;
            if ($startDate && $endDate) {
                $diffDays = (int) $startDate->diffInDays($endDate, false) + 1;
                if ($diffDays > 0) {
                    if ($diffDays <= 21) {
                        $durationFormatted = "{$diffDays}-Day Bootcamp";
                    } elseif ($diffDays <= 60) {
                        $weeks = max(1, (int) round($diffDays / 7));
                        $durationFormatted = "{$weeks} Weeks Program";
                    } else {
                        $months = max(1, (int) round($diffDays / 30));
                        $durationFormatted = "{$months} Months Program";
                    }
                }
            }
            if (!$durationFormatted && $vipPromoCourse->duration_hours) {
                $durationFormatted = "{$vipPromoCourse->duration_hours} Hours";
            }

            $thumbUrl = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&h=380&fit=crop';
            if ($vipPromoCourse->thumbnail) {
                $thumbUrl = Str::startsWith($vipPromoCourse->thumbnail, ['http://', 'https://'])
                    ? $vipPromoCourse->thumbnail
                    : asset('storage/' . $vipPromoCourse->thumbnail);
            }

            $teacherAvatarUrl = null;
            if ($vipPromoCourse->teacher && $vipPromoCourse->teacher->avatar) {
                $teacherAvatarUrl = Str::startsWith($vipPromoCourse->teacher->avatar, ['http://', 'https://'])
                    ? $vipPromoCourse->teacher->avatar
                    : asset('storage/' . $vipPromoCourse->teacher->avatar);
            }
        }
    @endphp

    @if($vipPromoCourse)
        <div id="edvoraVipPopupModal" class="edvora-vip-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="vipModalTitle" style="display: none;">
            <div class="edvora-vip-modal-card">
                <!-- Top Header: VIP Pill & Close Button -->
                <div class="edvora-vip-modal-topbar">
                    <div class="edvora-vip-badge-pill">
                        <i class="bi bi-star-fill"></i>
                        <span>VIP Class</span>
                    </div>
                    <button type="button" class="edvora-vip-modal-close-btn" id="closeVipModalXBtn" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Fitted Poster Box (Fully accommodates vertical or landscape posters without cropping) -->
                <div class="edvora-vip-poster-box">
                    <div class="edvora-vip-poster-ambient" style="background-image: url('{{ $thumbUrl }}');"></div>
                    <img src="{{ $thumbUrl }}"
                         alt="{{ $vipPromoCourse->title }}"
                         class="edvora-vip-poster-img"
                         loading="lazy"
                         onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&h=380&fit=crop';">
                </div>

                <!-- Minimal Content -->
                <div class="edvora-vip-modal-content">
                    <h3 class="edvora-vip-modal-title" id="vipModalTitle">
                        {{ $vipPromoCourse->title }}
                    </h3>

                    <div class="edvora-vip-teacher-row">
                        @if($teacherAvatarUrl)
                            <img src="{{ $teacherAvatarUrl }}" alt="{{ $vipPromoCourse->teacher->name ?? 'Instructor' }}" class="edvora-vip-teacher-avatar">
                        @else
                            <div class="edvora-vip-teacher-avatar-fallback">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        @endif
                        <div class="edvora-vip-teacher-meta">
                            <span class="edvora-vip-teacher-label">Instructor</span>
                            <span class="edvora-vip-teacher-name">{{ $vipPromoCourse->teacher->name ?? 'Edvora Senior Instructor' }}</span>
                        </div>
                    </div>

                    <div class="edvora-vip-meta-grid">
                        <div class="edvora-vip-meta-item">
                            <div class="edvora-vip-meta-icon">
                                <i class="bi bi-calendar2-week"></i>
                            </div>
                            <div class="edvora-vip-meta-text">
                                <span class="edvora-vip-meta-label">Schedule</span>
                                <span class="edvora-vip-meta-value">{{ $scheduleCountdown }}</span>
                                @if($scheduleDateFormatted)
                                    <span class="edvora-vip-meta-sub">{{ $scheduleDateFormatted }}</span>
                                @endif
                            </div>
                        </div>

                        @if($durationFormatted)
                        <div class="edvora-vip-meta-item">
                            <div class="edvora-vip-meta-icon">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="edvora-vip-meta-text">
                                <span class="edvora-vip-meta-label">Duration</span>
                                <span class="edvora-vip-meta-value">{{ $durationFormatted }}</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="edvora-vip-actions">
                        <a href="{{ route('courses.detail', $vipPromoCourse->slug) }}" class="btn-vip-modal-enroll" id="vipModalEnrollBtn">
                            <span>View Course Details</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const vipModal = document.getElementById('edvoraVipPopupModal');
            if (!vipModal) return;

            // Check if dismissed in this session
            const isDismissed = sessionStorage.getItem('edvora_vip_modal_dismissed');
            if (isDismissed) return;

            // Show after 1.2s delay for smooth entrance
            setTimeout(() => {
                vipModal.style.display = 'flex';
                // Trigger reflow for smooth transition
                void vipModal.offsetWidth;
                vipModal.classList.add('is-visible');
            }, 1200);

            function closeVipModal() {
                vipModal.classList.remove('is-visible');
                sessionStorage.setItem('edvora_vip_modal_dismissed', 'true');
                setTimeout(() => {
                    vipModal.style.display = 'none';
                }, 350);
            }

            const closeBtn = document.getElementById('closeVipModalXBtn');
            const dismissBtn = document.getElementById('dismissVipModalBtn');
            if (closeBtn) closeBtn.addEventListener('click', closeVipModal);
            if (dismissBtn) dismissBtn.addEventListener('click', closeVipModal);

            vipModal.addEventListener('click', function (e) {
                if (e.target === vipModal) {
                    closeVipModal();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && vipModal.classList.contains('is-visible')) {
                    closeVipModal();
                }
            });
        });
    </script>
    <script src="{{ asset('assets/js/home-hero.js') }}" defer></script>
    <script src="{{ asset('assets/js/home-roadmap.js') }}" defer></script>
    <script src="{{ asset('assets/js/continue-learning.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const defaultVideo = "{{ asset('assets/videos/how_create_account.mp4') }}";
            const modalBackdrop = document.createElement('div');
            modalBackdrop.className = 'hero-video-modal';
            modalBackdrop.style.display = 'none';
            modalBackdrop.innerHTML = `
                <div class="hero-video-modal-backdrop">
                    <div class="hero-video-modal-content">
                        <div class="hero-video-modal-header">
                            <h5 class="hero-video-modal-title">Video</h5>
                            <button type="button" class="hero-video-modal-close">&times;</button>
                        </div>
                        <div class="hero-video-modal-body">
                            <video controls class="hero-video-player">
                                <source src="" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modalBackdrop);

            const video = modalBackdrop.querySelector('video');
            const source = video.querySelector('source');
            const titleEl = modalBackdrop.querySelector('.hero-video-modal-title');
            const closeBtn = modalBackdrop.querySelector('.hero-video-modal-close');
            const outer = modalBackdrop.querySelector('.hero-video-modal-backdrop');

            function openModal(src, title) {
                source.src = src;
                titleEl.textContent = title || 'Video';
                modalBackdrop.style.display = 'block';
                video.load();
                video.play();
            }

            function closeModal() {
                modalBackdrop.style.display = 'none';
                video.pause();
                source.src = '';
                video.load();
            }

            closeBtn.addEventListener('click', closeModal);
            outer.addEventListener('click', function (e) {
                if (e.target === outer) closeModal();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeModal();
            });

            document.querySelectorAll('[data-video-toggle]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    openModal(btn.dataset.video || defaultVideo, btn.dataset.title || 'Watch Demo');
                });
            });
        });
    </script>
    <script src="{{ asset('assets/js/contact-form.js') }}" defer></script>
    <script>
        function recentCoursesScroll(direction) {
            const track = document.getElementById('recentCoursesTrack');
            if (!track) return;
            const item = track.querySelector('.recent-courses-carousel__item');
            const scrollAmount = item ? item.offsetWidth + 22 : track.clientWidth * 0.8;
            track.scrollBy({ left: scrollAmount * direction, behavior: 'smooth' });
        }
    </script>
@endpush
