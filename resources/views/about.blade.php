@extends('layouts.app')

@section('title', 'About Us - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/about.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/glass-panel.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!-- Header -->
    

    <!-- Premium Hero Section -->
    <section class="hero-animated">
        <div class="container text-center text-white">
            <h1 class="story-title display-3 fw-bold anim-slide-in-left">The Story of <span
                    class="highlight">Edvora</span></h1>
            <p class="story-subtitle lead anim-slide-in-right anim-delay-03">Pioneering the future of education with
                passion, innovation, and a commitment to lifelong learning.</p>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-5 section-animated" style="background-color: #f8f9fa;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">Our Mission & Vision</h2>
                <p class="text-muted">The driving force behind Edvora's purpose.</p>
            </div>
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="about-glass-card h-100">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i class="bi bi-bullseye fs-1" style="color: #1F8FFF;"></i>
                            </div>
                            <h3 class="fw-bold mb-3 text-dark">Our Mission</h3>
                            <p class="text-muted">To democratize education by providing world-class learning experiences
                                that empower individuals to achieve their personal and professional goals, regardless of
                                their background or location.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-glass-card h-100">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i class="bi bi-eye fs-1" style="color: #1F8FFF;"></i>
                            </div>
                            <h3 class="fw-bold mb-3 text-dark">Our Vision</h3>
                            <p class="text-muted">To become the world's leading online education platform, creating a
                                global community of lifelong learners who drive innovation and positive change in their
                                industries and communities.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story Timeline -->
    <section class="py-5 section-animated bg-white overflow-hidden">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark display-5">Our Journey</h2>
                <div class="mx-auto bg-primary mb-3" style="width: 80px; height: 4px; border-radius: 2px;"></div>
                <p class="text-muted lead">A timeline of our milestones and achievements.</p>
            </div>

            <div class="timeline-premium">
                <!-- 2020 -->
                <div class="timeline-item-premium item-blue">
                    <div class="timeline-icon-premium">
                        <i class="bi bi-lightbulb-fill"></i>
                    </div>
                    <div class="timeline-content-wrapper">
                        <div class="timeline-content-premium">
                            <span class="timeline-year">2020</span>
                            <h3 class="timeline-title">The Spark of an Idea</h3>
                            <p class="timeline-desc mb-0">Edvora was born from a vision to make tech education
                                accessible to all. We started with a small team and a big dream to revolutionize online
                                learning in the tech space.</p>
                        </div>
                    </div>
                </div>

                <!-- 2021 -->
                <div class="timeline-item-premium item-yellow">
                    <div class="timeline-icon-premium">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="timeline-content-wrapper">
                        <div class="timeline-content-premium">
                            <span class="timeline-year">2021</span>
                            <h3 class="timeline-title">Reaching 1,000 Students</h3>
                            <p class="timeline-desc mb-0">A major milestone reached! We empowered our first 1,000
                                learners, validating our mission and fueling our drive to scale even further across the
                                region.</p>
                        </div>
                    </div>
                </div>

                <!-- 2022 -->
                <div class="timeline-item-premium item-green">
                    <div class="timeline-icon-premium">
                        <i class="bi bi-globe-americas"></i>
                    </div>
                    <div class="timeline-content-wrapper">
                        <div class="timeline-content-premium">
                            <span class="timeline-year">2022</span>
                            <h3 class="timeline-title">Global Expansion</h3>
                            <p class="timeline-desc mb-0">We broke borders, expanding into 50+ countries. Edvora became
                                a truly global community, connecting learners from diverse backgrounds through
                                technology.</p>
                        </div>
                    </div>
                </div>

                <!-- 2024 -->
                <div class="timeline-item-premium item-purple">
                    <div class="timeline-icon-premium">
                        <i class="bi bi-cpu-fill"></i>
                    </div>
                    <div class="timeline-content-wrapper">
                        <div class="timeline-content-premium">
                            <span class="timeline-year">2024</span>
                            <h3 class="timeline-title">Future of Tech Education</h3>
                            <p class="timeline-desc mb-0">Today, we lead the way with AI-integrated tools and immersive
                                learning experiences, shaping the future of education for the next generation of
                                innovators.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Meet Our Team Section -->
    <section class="py-5 section-animated" style="background-color: #f8f9fa;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark display-5">Meet Our Team</h2>
                <div class="mx-auto bg-primary mb-3" style="width: 80px; height: 4px; border-radius: 2px;"></div>
                <p class="lead text-muted">The passionate individuals driving Edvora's mission forward.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- 1. Mobin hassani -->
                <div class="col-lg-4 col-md-6 cat-tech">
                    <div class="team-card-v2">
                        <div class="team-profile-placeholder">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <h5 class="team-name-v2">Mobin hassani</h5>
                        <span class="team-role-v2">Founder</span>
                        <p class="team-bio-v2">Visionary leader providing the strategic direction and inspiration behind
                            Edvora's ecosystem.</p>
                        <div class="team-social-v2">
                            <a href="#"><i class="bi bi-linkedin"></i></a>
                            <a href="#"><i class="bi bi-twitter"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 2. Alia Sharifi -->
                <div class="col-lg-4 col-md-6 cat-tech">
                    <div class="team-card-v2">
                        <div class="team-profile-placeholder">
                            <i class="bi bi-phone-vibrate"></i>
                        </div>
                        <h5 class="team-name-v2">Alia Sharifi</h5>
                        <span class="team-role-v2">Application Developer</span>
                        <p class="team-bio-v2">Crafting seamless mobile and desktop application experiences with
                            cutting-edge technologies.</p>
                        <div class="team-social-v2">
                            <a href="#"><i class="bi bi-github"></i></a>
                            <a href="#"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 3. Nilofar ataie -->
                <div class="col-lg-4 col-md-6 cat-marketing">
                    <div class="team-card-v2">
                        <div class="team-profile-placeholder">
                            <i class="bi bi-search-heart"></i>
                        </div>
                        <h5 class="team-name-v2">Nilofar ataie</h5>
                        <span class="team-role-v2">SEO Specialist</span>
                        <p class="team-bio-v2">Optimizing our digital presence to ensure Edvora reaches every curious
                            mind across the globe.</p>
                        <div class="team-social-v2">
                            <a href="#"><i class="bi bi-linkedin"></i></a>
                            <a href="#"><i class="bi bi-globe"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 4. Ismail Farhang -->
                <div class="col-lg-4 col-md-6 cat-tech">
                    <div class="team-card-v2">
                        <div class="team-profile-placeholder">
                            <i class="bi bi-pc-display"></i>
                        </div>
                        <h5 class="team-name-v2">Ismail Farhang</h5>
                        <span class="team-role-v2">IT Support</span>
                        <p class="team-bio-v2">Ensuring a smooth and reliable technological infrastructure for our team
                            and students alike.</p>
                        <div class="team-social-v2">
                            <a href="#"><i class="bi bi-envelope-fill"></i></a>
                            <a href="#"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 5. Fatima Rahmani -->
                <div class="col-lg-4 col-md-6 cat-tech">
                    <div class="team-card-v2">
                        <div class="team-profile-placeholder">
                            <i class="bi bi-code-slash"></i>
                        </div>
                        <h5 class="team-name-v2">Fatima Rahmani</h5>
                        <span class="team-role-v2">Web Developer</span>
                        <p class="team-bio-v2">Building the robust and interactive web platform that powers Edvora's
                            learning journey.</p>
                        <div class="team-social-v2">
                            <a href="#"><i class="bi bi-github"></i></a>
                            <a href="#"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 6. Jawad Hakimi -->
                <div class="col-lg-4 col-md-6 cat-creative">
                    <div class="team-card-v2">
                        <div class="team-profile-placeholder">
                            <i class="bi bi-palette-fill"></i>
                        </div>
                        <h5 class="team-name-v2">Jawad Hakimi</h5>
                        <span class="team-role-v2">UI/UX Designer</span>
                        <p class="team-bio-v2">Designing intuitive and beautiful user interfaces that make learning an
                            absolute joy.</p>
                        <div class="team-social-v2">
                            <a href="#"><i class="bi bi-behance"></i></a>
                            <a href="#"><i class="bi bi-dribbble"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 7. Mahdi Yousefi -->
                <div class="col-lg-4 col-md-6 cat-creative">
                    <div class="team-card-v2">
                        <div class="team-profile-placeholder">
                            <i class="bi bi-brush-fill"></i>
                        </div>
                        <h5 class="team-name-v2">Mahdi Yousefi</h5>
                        <span class="team-role-v2">Graphic Designer</span>
                        <p class="team-bio-v2">Creating the stunning visual identity and assets that define the Edvora
                            brand.</p>
                        <div class="team-social-v2">
                            <a href="#"><i class="bi bi-instagram"></i></a>
                            <a href="#"><i class="bi bi-behance"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 8. Timor Sidaqat -->
                <div class="col-lg-4 col-md-6 cat-creative">
                    <div class="team-card-v2">
                        <div class="team-profile-placeholder">
                            <i class="bi bi-video-fill"></i>
                        </div>
                        <h5 class="team-name-v2">Timor Sidaqat</h5>
                        <span class="team-role-v2">Video Editor</span>
                        <p class="team-bio-v2">Producing high-quality educational videos and cinematic content for our
                            community.</p>
                        <div class="team-social-v2">
                            <a href="#"><i class="bi bi-youtube"></i></a>
                            <a href="#"><i class="bi bi-play-circle-fill"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 9. Sadaf Ahmadzai -->
                <div class="col-lg-4 col-md-6 cat-marketing">
                    <div class="team-card-v2">
                        <div class="team-profile-placeholder">
                            <i class="bi bi-megaphone-fill"></i>
                        </div>
                        <h5 class="team-name-v2">Sadaf Ahmadzai</h5>
                        <span class="team-role-v2">Digital Marketing</span>
                        <p class="team-bio-v2">Spreading the word and engaging with our global community across all
                            digital channels.</p>
                        <div class="team-social-v2">
                            <a href="#"><i class="bi bi-instagram"></i></a>
                            <a href="#"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: #ffffff;">
        <div class="container">
            <div class="row align-items-center about-glass-card p-4 p-lg-5">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-3 text-dark">Ready to Start Your Learning Journey?</h2>
                    <p class="lead mb-0 text-muted">Join thousands of students who are already transforming their
                        careers with Edvora.</p>
                </div>
                <div class="col-lg-4 text-center mt-3 mt-lg-0">
                    <a href="{{ route('register') }}" class="btn btn-lg fw-bold px-5 py-3"
                        style="background: linear-gradient(135deg, #1F8FFF, #00A8FF); color: #fff; box-shadow: 0 4px 15px rgba(31,143,255,0.4); border-radius: 30px; transition: all 0.3s ease; border: none;">
                        <i class="bi bi-rocket-takeoff me-2"></i>Get Started Today
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Modern Footer -->
    

    <!-- Bootstrap 5 JS -->
    
    <!-- Custom JS -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/about.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
