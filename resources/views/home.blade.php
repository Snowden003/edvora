@extends('layouts.app')

@section('title', 'Edvora Tech - Free Practical Skills for Afghan Women')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/home-hero.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/home-value-proposition.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/courses.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/popular-courses.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/teachers.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/competitions.css') }}" />
    <link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/home-roadmap.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/beta-notice.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/statistics.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/continue-learning.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/recent-courses.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/home-course-showcase.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/home-new-sections.css') }}" />
@endpush

@section('content')
    <section class="hero2">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000" data-bs-pause="hover">
            <div class="carousel-inner">

                {{-- Slide 1: Main value proposition --}}
                <div class="carousel-item active">
                    <div class="hero2__glow hero2__glow--a"></div>
                    <div class="hero2__glow hero2__glow--b"></div>
                    <div class="container hero2__inner">
                        <div class="hero2__content">
                            <span class="hero2__eyebrow"><i class="bi bi-stars"></i> Edvora Tech</span>
                            <h1 class="hero2__title">Everything You Need to Learn.<br><span>Completely Free.</span></h1>
                            <p class="hero2__desc">Premium courses, expert mentors, hands-on labs and a thriving global community — all completely free. No subscriptions, no paywalls.</p>

                            <div class="hero2__features">
                                <div class="hero2__feature"><i class="bi bi-people-fill"></i><span>Expert Mentors</span></div>
                                <div class="hero2__feature"><i class="bi bi-mortarboard-fill"></i><span>Premium Classes</span></div>
                                <div class="hero2__feature"><i class="bi bi-book-fill"></i><span>Best Books</span></div>
                                <div class="hero2__feature"><i class="bi bi-flask-fill"></i><span>Practice Labs</span></div>
                                <div class="hero2__feature"><i class="bi bi-globe2"></i><span>Global Community</span></div>
                            </div>

                            <div class="hero2__stats">
                                <div class="hero2__stat"><strong>{{ $totalStudents }}</strong><span>Active Learners</span></div>
                                <div class="hero2__stat"><strong>{{ $totalCourses }}</strong><span>Free Courses</span></div>
                                <div class="hero2__stat"><strong>{{ $completedCourses }}</strong><span>Completed</span></div>
                            </div>
                        </div>

                        <div class="hero2__visual">
                            <div class="hero2__ring hero2__ring--1"></div>
                            <div class="hero2__ring hero2__ring--2"></div>
                            <div class="hero2__badge"><i class="bi bi-shield-lock-fill"></i></div>
                            <img src="{{ asset('assets/images/hero_logo_design.png') }}" alt="Edvora Tech" class="hero2__logo" fetchpriority="high" decoding="async">
                            <div class="hero2__particle hero2__particle--1"></div>
                            <div class="hero2__particle hero2__particle--2"></div>
                            <div class="hero2__particle hero2__particle--3"></div>
                        </div>
                    </div>
                </div>

                {{-- Slide 2: Featured / Special courses (admin controlled) --}}
                <div class="carousel-item">
                    <div class="hero2__glow hero2__glow--a"></div>
                    <div class="hero2__glow hero2__glow--b"></div>
                    <div class="container hero2__inner hero2__inner--featured">

                        @forelse ($featuredCourses as $fc)
                            <div class="hero2__spotlight">
                                <div class="hero2__spotlight-media">
                                    @if($fc->thumbnail)
                                        <img src="{{ asset('storage/' . $fc->thumbnail) }}" alt="{{ $fc->title }}" loading="lazy" decoding="async">
                                    @else
                                        <div class="hero2__spotlight-media--fallback"><i class="bi bi-{{ $fc->category->icon ?? 'mortarboard-fill' }}"></i></div>
                                    @endif
                                    <span class="hero2__spotlight-ribbon"><i class="bi bi-award-fill"></i> Special</span>
                                </div>

                                <div class="hero2__spotlight-body">
                                    <span class="hero2__eyebrow"><i class="bi bi-stars"></i> {{ $fc->category->name ?? 'Featured' }}</span>
                                    <h1 class="hero2__title hero2__title--sm">{{ $fc->title }}</h1>
                                    <p class="hero2__desc">{{ \Illuminate\Support\Str::limit($fc->description, 140) }}</p>

                                    <div class="hero2__spotlight-badges">
                                        <span class="hero2__spotlight-badge"><i class="bi bi-bar-chart-fill"></i> {{ ucfirst($fc->level) }}</span>
                                        <span class="hero2__spotlight-badge"><i class="bi bi-clock-fill"></i> {{ $fc->duration_hours }}h</span>
                                        <span class="hero2__spotlight-badge"><i class="bi bi-people-fill"></i> {{ $fc->enrolled_count }} Students</span>
                                        @if($fc->rating > 0)
                                            <span class="hero2__spotlight-badge"><i class="bi bi-star-fill"></i> {{ number_format($fc->rating, 1) }}</span>
                                        @endif
                                        @if($fc->has_certificate)
                                            <span class="hero2__spotlight-badge"><i class="bi bi-patch-check-fill"></i> Certificate</span>
                                        @endif
                                        @if($fc->teacher)
                                            <span class="hero2__spotlight-badge"><i class="bi bi-person-fill"></i> {{ $fc->teacher->name }}</span>
                                        @endif
                                    </div>

                                    <a href="{{ route('courses.detail', $fc->slug) }}" class="hero2__spotlight-cta">
                                        Explore Course <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="hero2__content" style="margin: 0 auto; text-align:center;">
                                <span class="hero2__eyebrow"><i class="bi bi-award-fill"></i> Special Courses</span>
                                <h1 class="hero2__title">No Special Courses Yet.<br><span>Check Back Soon.</span></h1>
                                <p class="hero2__desc">An admin hasn't marked any course as featured yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Slide 3: Quick actions --}}
                <div class="carousel-item">
                    <div class="hero2__glow hero2__glow--a"></div>
                    <div class="hero2__glow hero2__glow--b"></div>
                    <div class="container hero2__inner hero2__inner--actions">
                        <div class="hero2__content">
                            <span class="hero2__eyebrow"><i class="bi bi-lightning-charge-fill"></i> Quick Actions</span>
                            <h1 class="hero2__title">Jump Right Back In.<br><span>Everything's One Click Away.</span></h1>
                            <p class="hero2__desc">Browse the library, enroll in a new course, or continue where you left off.</p>
                        </div>

                        <div class="hero2__actions-grid">
                            <a href="{{ route('courses.index') }}" class="hero2__action-card">
                                <span class="hero2__action-icon"><i class="bi bi-book-half"></i></span>
                                <h3>Course Library</h3>
                                <p>Browse free courses & resources</p>
                            </a>
                            <a href="{{ route('courses.index') }}" class="hero2__action-card">
                                <span class="hero2__action-icon"><i class="bi bi-journal-plus"></i></span>
                                <h3>Register New Course</h3>
                                <p>Enroll in a course today</p>
                            </a>
                            <a href="{{ auth()->check() ? (auth()->user()->role === 'teacher' ? route('teacher.your-courses') : route('student.courses')) : route('login') }}" class="hero2__action-card">
                                <span class="hero2__action-icon"><i class="bi bi-collection-play"></i></span>
                                <h3>My Current Courses</h3>
                                <p>Continue your learning</p>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <button class="hero2__arrow hero2__arrow--prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Previous">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="hero2__arrow hero2__arrow--next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Next">
                <i class="bi bi-chevron-right"></i>
            </button>
            <div class="carousel-indicators hero2__indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
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

    <section class="home-course-showcase">
        <div class="container">
            <div class="home-course-showcase__header">
                <span class="home-course-showcase__eyebrow"><i class="bi bi-clock-history"></i> Just added</span>
                <h2>Recent Courses</h2>
                <p>Start with the newest practical skills available on Edvora.</p>
            </div>

            @if($recentCourses->count() > 0)
                <div class="recent-courses-carousel" id="recentCoursesCarousel">
                    <button class="recent-courses-carousel__nav recent-courses-carousel__nav--prev" type="button" aria-label="Previous" onclick="recentCoursesScroll(-1)"><i class="bi bi-chevron-left"></i></button>
                    <button class="recent-courses-carousel__nav recent-courses-carousel__nav--next" type="button" aria-label="Next" onclick="recentCoursesScroll(1)"><i class="bi bi-chevron-right"></i></button>
                    <div class="recent-courses-carousel__track" id="recentCoursesTrack">
                        @foreach($recentCourses as $course)
                            <div class="recent-courses-carousel__item">
                                <article class="home-course-card">
                                    <div class="home-course-card__image">
                                        @if($course->thumbnail)
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" loading="lazy" decoding="async">
                                        @else
                                            <i class="bi {{ $course->category?->icon ?? 'bi-code-slash' }}"></i>
                                        @endif
                                        <span class="home-course-card__tag">{{ $course->category->name ?? 'General' }}</span>
                                        <span class="home-course-card__free">Free</span>
                                    </div>
                                    <div class="home-course-card__body">
                                        <div class="home-course-card__teacher"><i class="bi bi-person-circle"></i>{{ $course->teacher->name ?? 'Edvora Instructor' }}</div>
                                        <h3 class="home-course-card__title"><a href="{{ route('courses.detail', $course->slug) }}">{{ $course->title }}</a></h3>
                                        <p class="home-course-card__description">{{ Str::limit(strip_tags($course->description), 115) }}</p>
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
            @else
                <div class="empty-section-placeholder empty-small">
                    <div class="empty-section-icon"><i class="bi bi-compass"></i></div>
                    <h4>Find Your Learning Path</h4>
                    <p>Explore Edvora's learning opportunities and choose the practical skill you want to build next.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-primary btn-sm mt-3"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Explore Courses</a>
                </div>
            @endif

            <div class="home-course-showcase__cta"><a href="{{ route('courses.index') }}">Explore all courses <i class="bi bi-arrow-right"></i></a></div>
        </div>
    </section>

    <section class="home-course-showcase home-course-showcase--popular">
        <div class="container">
            <div class="home-course-showcase__header">
                <span class="home-course-showcase__eyebrow"><i class="bi bi-trophy-fill"></i> Top Rated Courses</span>
                <h2>Popular Courses</h2>
                <p>The most enrolled and highest-rated courses on Edvora.</p>
            </div>

            @if($popularCourses->count() > 0)
                <div class="popular-courses-bordered">
                <div class="row g-4">
                    @foreach($popularCourses as $course)
                        <div class="col-lg-4 col-md-6">
                            <article class="popular-course-card h-100">
                                <div class="popular-course-card__media{{ $course->thumbnail ? '' : ' popular-course-card__media--' . ($course->category?->slug ?? 'general') }}">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" loading="lazy" decoding="async">
                                    @endif
                                </div>
                                <div class="popular-course-card__body">
                                    <div class="popular-course-card__badges">
                                        <span class="popular-course-card__badge popular-course-card__badge--rank"><i class="bi bi-trophy-fill"></i> Top {{ $loop->iteration }}</span>
                                        <span class="popular-course-card__badge popular-course-card__badge--category"><i class="bi {{ $course->category?->icon ?? 'bi-grid' }}"></i> {{ $course->category?->name ?? 'General' }}</span>
                                        <span class="popular-course-card__badge popular-course-card__badge--free">Free</span>
                                    </div>
                                    <div class="popular-course-card__teacher">
                                        @if($course->teacher?->avatar ?? false)
                                            <img src="{{ asset('storage/' . $course->teacher->avatar) }}" class="popular-course-card__avatar" alt="{{ $course->teacher->name }}" loading="lazy" decoding="async">
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
                                    <p class="popular-course-card__description">{{ Str::limit(strip_tags($course->description), 100) }}</p>
                                    <div class="popular-course-card__meta">
                                        <span><i class="bi bi-people"></i>{{ $course->enrollments_count ?? 0 }} learners</span>
                                        <span><i class="bi bi-clock"></i>{{ $course->duration_hours ? $course->duration_hours . 'h' : 'Self-paced' }}</span>
                                        @if($course->level)
                                            <span><i class="bi bi-bar-chart"></i>{{ $course->level }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('courses.detail', $course->slug) }}" class="popular-course-card__cta">View Course <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
                </div>
            @else
                <div class="empty-section-placeholder empty-small">
                    <div class="empty-section-icon"><i class="bi bi-journal-bookmark"></i></div>
                    <h4>Explore Learning Paths</h4>
                    <p>Discover the practical skills you can begin learning with Edvora.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-primary btn-sm mt-3"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Explore Courses</a>
                </div>
            @endif

            <div class="home-course-showcase__cta"><a href="{{ route('courses.index') }}">View all courses <i class="bi bi-arrow-right"></i></a></div>
        </div>
    </section>

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
                    <div class="about-preview-visual">
                        <div class="about-preview-visual__badge">
                            <i class="bi bi-stars me-2"></i>Our Team
                        </div>
                        <i class="bi bi-people-fill"></i>
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
                    <div class="about-preview-visual" style="min-height: 380px;">
                        <div class="about-preview-visual__badge">
                            <i class="bi bi-heart-fill me-2"></i>Our Purpose
                        </div>
                        <i class="bi bi-rocket-takeoff-fill"></i>
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
                    <p>Through events, competitions, and peer support, women build confidence, find their voice, and connect with a community that believes in their potential.</p>
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

    @if($upcomingCompetitions->isNotEmpty())
    <section class="py-5 gray">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: #1f8fff">Upcoming Competitions</h2>
                <a href="{{ route('competitions.index') }}" class="btn btn-outline-primary white-hover">View All</a>
            </div>

            <div class="row g-4">
                @foreach($upcomingCompetitions as $competition)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            @if($competition->thumbnail)
                                <img src="{{ asset('storage/' . $competition->thumbnail) }}" class="card-img-top" alt="{{ $competition->title }}" style="height: 200px; object-fit: cover;" loading="lazy" decoding="async">
                            @else
                                <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                    <i class="bi bi-trophy fs-1 text-muted"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title fw-bold">{{ $competition->title }}</h5>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-calendar3 me-1"></i>{{ $competition->start_date->format('M d, Y') }}
                                </p>
                                <p class="card-text text-muted small">{{ \Illuminate\Support\Str::limit($competition->description, 100) }}</p>
                                <a href="{{ route('competitions.detail', $competition->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-info-circle me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

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
                    <div class="about-preview-visual" style="min-height: 340px;">
                        <div class="about-preview-visual__badge">
                            <i class="bi bi-gem-fill me-2"></i>Edvora Foundation
                        </div>
                        <i class="bi bi-heart-fill"></i>
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
@endsection

@push('scripts')
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
