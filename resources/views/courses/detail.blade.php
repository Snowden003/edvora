@extends('layouts.app')

@php
    $courseUrl = route('courses.detail', $course->slug);
    $courseImage = $course->thumbnail ? (Str::startsWith($course->thumbnail, 'http') ? $course->thumbnail : asset('storage/' . $course->thumbnail)) : 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=600&h=400&fit=crop';
    $courseDescription = (string) \Illuminate\Support\Str::of(strip_tags($course->description ?? ''))
        ->squish()
        ->limit(155, '');
    $lessons = $course->lessons ?? collect();
    $courseSchema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Course',
                'name' => $course->title,
                'description' => $courseDescription,
                'url' => $courseUrl,
                'image' => $courseImage,
                'provider' => [
                    '@type' => 'Organization',
                    'name' => 'Edvora Tech',
                    'url' => url('/'),
                ],
                'educationalLevel' => ucfirst($course->level),
                'timeRequired' => 'PT' . max(1, (int) $course->duration_hours) . 'H',
                'courseMode' => 'online',
                'isAccessibleForFree' => true,
                'offers' => [
                    '@type' => 'Offer',
                    'price' => '0',
                    'priceCurrency' => 'USD',
                    'availability' => 'https://schema.org/InStock',
                ],
            ],
            [
                '@type' => 'FAQPage',
                'mainEntity' => [
                    [
                        '@type' => 'Question',
                        'name' => 'Is ' . $course->title . ' free?',
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => 'Yes. This Edvora course is free for learners.',
                        ],
                    ],
                    [
                        '@type' => 'Question',
                        'name' => 'What level is this course?',
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => 'This course is designed for ' . $course->level . ' learners.',
                        ],
                    ],
                    [
                        '@type' => 'Question',
                        'name' => 'How long does this course take?',
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => 'The course includes approximately ' . $course->duration_hours . ' hours of learning content.',
                        ],
                    ],
                ],
            ],
        ],
    ];

    if ($course->teacher) {
        $courseSchema['@graph'][0]['hasCourseInstance'] = [
            '@type' => 'CourseInstance',
            'courseMode' => 'online',
            'instructor' => [
                '@type' => 'Person',
                'name' => $course->teacher->name,
            ],
        ];
    }
@endphp

@section('title', 'Free ' . $course->title . ' Online Course for Afghan Women | Edvora')
@section('meta_description', 'Free online ' . $course->title . ' course for Afghan women and girls. ' . $courseDescription)
@section('canonical', $courseUrl)
@section('meta_image', $courseImage)
@section('meta_type', 'website')

@push('structured_data')
<script type="application/ld+json">{!! json_encode($courseSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@push('styles')
<link href="{{ asset('assets/css/course-detail-redesign.css') }}" rel="stylesheet" />
@endpush

@section('content')

    <!-- 1. Hero Section (Immersive & Dynamic) -->
    <section class="course-detail-hero">
        <div class="hero-pulse-grid"></div>
        <div class="container hero-content-wrapper">
            <div class="row align-items-center">
                
                <!-- Hero Content -->
                <div class="col-lg-7">
                    <!-- Badges -->
                    <div class="hero-badges mb-4 d-flex gap-2 flex-wrap">
                        <span class="badge badge-category">
                            <i class="bi bi-tag-fill me-1"></i> {{ $course->category->name ?? 'General' }}
                        </span>
                        <span class="badge">
                            <i class="bi bi-bar-chart-fill me-1"></i> {{ ucfirst($course->level) }}
                        </span>
                        <span class="badge bg-success bg-opacity-25 text-success border-0">
                            <i class="bi bi-unlock-fill me-1"></i> Free Access
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="hero-title">{{ $course->title }}</h1>
                    
                    <!-- Description -->
                    <div class="hero-desc">
                        {{ Str::limit(strip_tags($course->description ?? ''), 200) }}
                    </div>

                    <!-- Stats Pills -->
                    <div class="hero-stats-grid">
                        <div class="stat-pill">
                            <i class="bi bi-people-fill"></i>
                            <div class="stat-pill-content">
                                <span class="stat-pill-val">{{ number_format($course->enrolled_count) }}</span>
                                <span class="stat-pill-label">Students</span>
                            </div>
                        </div>
                        <div class="stat-pill">
                            <i class="bi bi-star-fill text-warning"></i>
                            <div class="stat-pill-content">
                                <span class="stat-pill-val">{{ number_format($course->rating, 1) }}</span>
                                <span class="stat-pill-label">Rating</span>
                            </div>
                        </div>
                        <div class="stat-pill">
                            <i class="bi bi-clock-fill"></i>
                            <div class="stat-pill-content">
                                <span class="stat-pill-val">{{ $course->duration_hours }}h</span>
                                <span class="stat-pill-label">Duration</span>
                            </div>
                        </div>
                        @if($course->has_certificate)
                        <div class="stat-pill">
                            <i class="bi bi-award-fill text-success"></i>
                            <div class="stat-pill-content">
                                <span class="stat-pill-val">Yes</span>
                                <span class="stat-pill-label">Certificate</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Call to Action -->
                    @auth
                        @php
                            $studentProfileComplete = auth()->user()->studentProfile?->is_complete ?? false;
                        @endphp
                        @if(!$isEnrolled && !$studentProfileComplete)
                            <div class="alert alert-warning d-flex align-items-center gap-2 mb-4 rounded-3 border-0 bg-warning bg-opacity-25 text-white" role="alert" style="backdrop-filter: blur(10px);">
                                <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                                <div>
                                    <strong class="text-warning">Profile Incomplete.</strong>
                                    You must <a href="{{ route('student.profile-details') }}" class="text-white text-decoration-underline">complete your profile details</a> before enrolling.
                                </div>
                            </div>
                        @endif
                    @endauth

                    <div class="hero-actions d-flex gap-3 flex-wrap">
                        @auth
                            @if($isEnrolled)
                                <button class="btn btn-enroll-primary opacity-75" disabled>
                                    <i class="bi bi-check-circle-fill"></i> Already Enrolled
                                </button>
                            @else
                                <button class="btn btn-enroll-primary enroll-btn"
                                        id="enrollBtn"
                                        data-url="{{ parse_url(route('courses.enroll', $course->id), PHP_URL_PATH) }}">
                                    <i class="bi bi-lightning-charge-fill text-warning"></i> Enroll Now
                                </button>
                            @endif

                            <button class="btn btn-wishlist-glass {{ $isWishlisted ? 'is-wishlisted' : '' }}"
                                    id="wishlistBtn"
                                    data-url="{{ parse_url(route('courses.wishlist.toggle', $course->id), PHP_URL_PATH) }}"
                                    data-wishlisted="{{ $isWishlisted ? '1' : '0' }}">
                                <i class="bi {{ $isWishlisted ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                <span class="wishlist-text">{{ $isWishlisted ? 'In Wishlist' : 'Add to Wishlist' }}</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-enroll-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Login to Enroll
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-wishlist-glass">
                                <i class="bi bi-heart"></i> Login to Wishlist
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Hero Image -->
                <div class="col-lg-5">
                    <div class="hero-thumbnail-wrapper">
                        <img src="{{ $courseImage }}" alt="{{ $course->title }}">
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 2. Main Content (Clean & Readable) -->
    <div class="detail-main-bg py-5">
        <div class="container">
            <div class="row g-5">
                
                <!-- Left Column (Content) -->
                <div class="col-lg-8">
                    
                    <!-- Course Overview -->
                    <section class="mb-5">
                        <h2 class="section-title"><i class="bi bi-info-circle-fill"></i> Course Overview</h2>
                        <div class="content-card">
                            <div class="overview-text mb-4">
                                {!! $course->description !!}
                            </div>
                            
                            <div class="course-features-grid">
                                <div class="feature-item">
                                    <div class="feature-icon"><i class="bi bi-bar-chart-steps"></i></div>
                                    <div class="feature-text">
                                        <span class="feature-text-label">Level</span>
                                        <span class="feature-text-val">{{ ucfirst($course->level) }}</span>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon"><i class="bi bi-clock-history"></i></div>
                                    <div class="feature-text">
                                        <span class="feature-text-label">Duration</span>
                                        <span class="feature-text-val">{{ $course->duration_hours }} Hours</span>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon"><i class="bi bi-tags-fill"></i></div>
                                    <div class="feature-text">
                                        <span class="feature-text-label">Category</span>
                                        <span class="feature-text-val">{{ $course->category->name ?? 'General' }}</span>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon"><i class="bi bi-globe"></i></div>
                                    <div class="feature-text">
                                        <span class="feature-text-label">Language</span>
                                        <span class="feature-text-val">English</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- What You'll Learn -->
                    <section class="mb-5">
                        <h2 class="section-title"><i class="bi bi-check2-square"></i> What You Will Learn</h2>
                        <div class="content-card bg-transparent border-0 shadow-none p-0">
                            <div class="learning-list">
                                @forelse($lessons->take(6) as $lesson)
                                    <div class="learning-item">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>Master the concepts of {{ $lesson->title }} and apply them.</span>
                                    </div>
                                @empty
                                    <div class="learning-item">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>Build practical {{ ucfirst($course->level) }} skills through guided online learning.</span>
                                    </div>
                                    <div class="learning-item">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>Earn a certificate of completion to boost your resume.</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </section>

                    <!-- Curriculum -->
                    <section class="mb-5">
                        <h2 class="section-title"><i class="bi bi-journal-code"></i> Course Curriculum</h2>
                        @php
                            $totalDuration = $lessons->sum('duration_minutes');
                        @endphp

                        @if($lessons->isEmpty())
                            <div class="content-card text-center py-5 bg-white">
                                <i class="bi bi-folder2-open display-1 text-black-50 mb-3"></i>
                                <h4 class="fw-bold">Curriculum Coming Soon</h4>
                                <p class="text-muted mb-0">The instructor is still preparing the lessons for this course.</p>
                            </div>
                        @else
                            <div class="d-flex align-items-center mb-4 text-muted fw-bold">
                                <i class="bi bi-collection-play-fill text-primary me-2 fs-5"></i>
                                <span>{{ $lessons->count() }} Lessons</span>
                                <span class="mx-3 opacity-25">|</span>
                                <i class="bi bi-stopwatch-fill text-primary me-2 fs-5"></i>
                                <span>{{ floor($totalDuration / 60) }}h {{ $totalDuration % 60 }}m Total Length</span>
                            </div>

                            <div class="accordion curriculum-accordion" id="curriculumAccordion">
                                @foreach($lessons as $index => $lesson)
                                <div class="accordion-item curriculum-item">
                                    <h2 class="accordion-header" id="heading-{{ $lesson->id }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $lesson->id }}" aria-expanded="false" aria-controls="collapse-{{ $lesson->id }}">
                                            <div class="curriculum-icon me-3">
                                                {{ $lesson->order }}
                                            </div>
                                            <span class="lesson-title">{{ $lesson->title }}</span>
                                            
                                            <div class="lesson-meta">
                                                @if($lesson->video_url)
                                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><i class="bi bi-play-circle-fill me-1"></i> Video</span>
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2"><i class="bi bi-file-earmark-text-fill me-1"></i> Text</span>
                                                @endif
                                                <span><i class="bi bi-clock-history me-1"></i>{{ $lesson->duration_minutes }}m</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $lesson->id }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $lesson->id }}" data-bs-parent="#curriculumAccordion">
                                        <div class="accordion-body">
                                            @if($lesson->description)
                                                <p class="mb-3">{{ $lesson->description }}</p>
                                            @else
                                                <p class="mb-3 text-muted fst-italic">No additional description provided.</p>
                                            @endif
                                            
                                            @if($lesson->video_url)
                                                <a href="{{ $lesson->video_url }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill fw-bold px-4">
                                                    <i class="bi bi-play-fill fs-5 align-middle me-1"></i> Watch Lesson
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </section>

                    <!-- FAQs -->
                    <section class="mb-5">
                        <h2 class="section-title"><i class="bi bi-chat-quote-fill"></i> Frequently Asked Questions</h2>
                        <div class="accordion faq-accordion" id="courseFaqAccordion">
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="courseFaqFreeHeading">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#courseFaqFree">
                                        Is {{ $course->title }} really free?
                                    </button>
                                </h3>
                                <div id="courseFaqFree" class="accordion-collapse collapse show" data-bs-parent="#courseFaqAccordion">
                                    <div class="accordion-body">Yes. As part of Edvora's mission, this course is completely free for all enrolled learners, with no hidden fees.</div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="courseFaqLevelHeading">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#courseFaqLevel">
                                        Do I need prior experience?
                                    </button>
                                </h3>
                                <div id="courseFaqLevel" class="accordion-collapse collapse" data-bs-parent="#courseFaqAccordion">
                                    <div class="accordion-body">This course is categorized as <strong>{{ ucfirst($course->level) }}</strong>. Please review the "What You Will Learn" section to ensure it matches your current skill level.</div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="courseFaqEnrollmentHeading">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#courseFaqEnrollment">
                                        How do I register and start?
                                    </button>
                                </h3>
                                <div id="courseFaqEnrollment" class="accordion-collapse collapse" data-bs-parent="#courseFaqAccordion">
                                    <div class="accordion-body">Simply create an Edvora account, fill out your student profile details, and click the "Enroll Now" button at the top of this page.</div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>

                <!-- Right Column (Sidebar) -->
                <div class="col-lg-4">
                    <div class="sidebar-sticky">
                        
                        <!-- Instructor Profile -->
                        <div class="instructor-card mb-4">
                            <h4 class="fw-bold mb-4" style="color: #0f172a;">Your Instructor</h4>
                            <div class="instructor-avatar-wrap">
                                <img src="{{ $course->teacher && $course->teacher->avatar ? (str_starts_with($course->teacher->avatar, 'http') ? $course->teacher->avatar : asset('storage/' . $course->teacher->avatar)) : 'https://ui-avatars.com/api/?name=' . urlencode(($course->teacher && $course->teacher->name) ? $course->teacher->name : 'Instructor') . '&size=200&background=random' }}"
                                    alt="{{ ($course->teacher && $course->teacher->name) ? $course->teacher->name : 'Instructor' }}">
                            </div>
                            <h3 class="instructor-name">{{ ($course->teacher && $course->teacher->name) ? $course->teacher->name : 'Edvora Instructor' }}</h3>
                            <div class="instructor-title">{{ ($course->teacher && $course->teacher->department) ? $course->teacher->department : ($course->category->name ?? 'Subject Expert') }}</div>
                            
                            @php
                                $bio = ($course->teacher && $course->teacher->bio) ? $course->teacher->bio : '';
                                $bio = preg_replace('/^(\S{3,20})\1{3,}$/', '', $bio);
                                $bio = \Illuminate\Support\Str::limit($bio, 120);
                            @endphp
                            <p class="instructor-bio">
                                {{ $bio ?: 'An expert instructor with years of industry experience, dedicated to providing high-quality education.' }}
                            </p>
                            
                            @php $teacherProfile = ($course->teacher && $course->teacher->teacher) ? $course->teacher->teacher : null; @endphp
                            <div class="instructor-stats">
                                <div class="istat-item">
                                    <span class="istat-val">{{ $teacherProfile ? number_format($teacherProfile->total_students) : '5k+' }}</span>
                                    <span class="istat-label">Students</span>
                                </div>
                                <div class="istat-item">
                                    <span class="istat-val">{{ $teacherProfile ? $teacherProfile->total_courses : '12' }}</span>
                                    <span class="istat-label">Courses</span>
                                </div>
                                <div class="istat-item">
                                    <span class="istat-val"><i class="bi bi-star-fill text-warning me-1"></i>{{ $teacherProfile ? $teacherProfile->rating : '4.8' }}</span>
                                    <span class="istat-label">Rating</span>
                                </div>
                            </div>
                        </div>

                        <!-- Course Benefits -->
                        <div class="content-card mb-4 p-4">
                            <h5 class="fw-bold mb-4" style="color: #0f172a;">Course Benefits</h5>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                                <li class="d-flex align-items-center gap-3">
                                    <i class="bi bi-unlock-fill text-primary fs-5"></i>
                                    <span class="fw-bold text-secondary">100% Free Access</span>
                                </li>
                                @if($course->has_lifetime_access !== false)
                                <li class="d-flex align-items-center gap-3">
                                    <i class="bi bi-infinity text-success fs-5"></i>
                                    <span class="fw-bold text-secondary">Lifetime Access</span>
                                </li>
                                @endif
                                @if($course->has_mobile_access !== false)
                                <li class="d-flex align-items-center gap-3">
                                    <i class="bi bi-phone-fill text-info fs-5"></i>
                                    <span class="fw-bold text-secondary">Mobile & Desktop</span>
                                </li>
                                @endif
                                @if($course->has_certificate)
                                <li class="d-flex align-items-center gap-3">
                                    <i class="bi bi-patch-check-fill text-warning fs-5"></i>
                                    <span class="fw-bold text-secondary">Certificate of Completion</span>
                                </li>
                                @endif
                            </ul>
                        </div>

                        <!-- Related Courses -->
                        @if($related->isNotEmpty())
                        <div class="content-card p-4">
                            <h5 class="fw-bold mb-4" style="color: #0f172a;">Related Courses</h5>
                            <div class="d-flex flex-column gap-3">
                                @foreach($related as $rel)
                                <a href="{{ route('courses.detail', $rel->slug) }}" class="related-course-item">
                                    <img src="{{ $rel->thumbnail ? asset('storage/' . $rel->thumbnail) : 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=80&h=60&fit=crop' }}"
                                         alt="{{ $rel->title }}" class="related-thumb">
                                    <div class="related-info">
                                        <h6 class="related-title">{{ $rel->title }}</h6>
                                        <span class="related-meta"><i class="bi bi-people-fill me-1"></i>{{ number_format($rel->enrolled_count) }} students</span>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/course-detail.js') }}"></script>
@endpush
