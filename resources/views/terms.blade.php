@extends('layouts.app')

@section('title', 'Terms of Service - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/terms.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!-- Header -->
    

    <!-- Elite Refined Hero Section -->
    <section class="terms-hero-refined">
        <div class="secure-mesh"></div>
        <div class="hero-glow-orb" style="top: -100px; left: -100px;"></div>
        <div class="hero-glow-orb" style="bottom: -100px; right: -100px; animation-delay: -4s;"></div>

        <!-- Floating Grid Icons -->
        <i class="bi bi-shield-check grid-icon" style="top: 15%; left: 10%; animation-delay: 1s;"></i>
        <i class="bi bi-lock grid-icon" style="top: 65%; left: 85%; animation-delay: 3s;"></i>
        <i class="bi bi-file-earmark-text grid-icon" style="top: 25%; left: 80%; animation-delay: 5s;"></i>
        <i class="bi bi-fingerprint grid-icon" style="top: 75%; left: 15%; animation-delay: 2s;"></i>

        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-10" data-aos="zoom-in">
                    <div class="title-glass-box">
                        <span class="terms-badge-unique">
                            <i class="bi bi-shield-lock-fill me-2"></i>Edvora Legal Framework
                        </span>
                        <h1 class="display-2 fw-bold text-white mb-4" style="letter-spacing: -2px;">Terms of Service
                        </h1>
                        <p class="lead text-white-50 mx-auto mb-4" style="max-width: 700px; font-weight: 300;">
                            Our commitment to your privacy, security, and elite learning experience.
                            We've refined our terms to be as transparent as our platform.
                        </p>
                        <div class="d-flex justify-content-center gap-5 text-white-50 small">
                            <span class="d-flex align-items-center gap-2">
                                <i class="bi bi-calendar3 text-primary"></i> Last Updated: Dec 2024
                            </span>
                            <span class="d-flex align-items-center gap-2">
                                <i class="bi bi-patch-check text-info"></i> Version 2.4.0
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Elite Terms Grid -->
    <section class="terms-content-section">
        <div class="container">
            <div class="bento-grid">

                <!-- 1. Acceptance (Bento Wide) -->
                <div class="bento-card card bento-wide" data-aos="fade-up">
                    <div class="accent-line line-blue"></div>
                    <div class="module-icon bg-blue-glass"><i class="bi bi-check2-circle"></i></div>
                    <h3 class="module-title">1. Acceptance of Terms</h3>
                    <p class="module-text">By accessing and using Edvora ("the Platform"), you accept and agree to be
                        bound by the terms and provision of this agreement. Our platform is designed to be an elite,
                        safe, and productive environment for all.</p>
                    <div class="plain-english-min">
                        <span class="tldr-pill">TL;DR</span>
                        <span class="tldr-text">Using the site means you follow the rules. Simple.</span>
                    </div>
                </div>

                <!-- 2. Description (Bento Large) -->
                <div class="bento-card card bento-large" data-aos="fade-up">
                    <div class="accent-line line-teal"></div>
                    <div class="module-icon bg-teal-glass"><i class="bi bi-cpu"></i></div>
                    <h3 class="module-title">2. Our Services</h3>
                    <p class="module-text">Edvora is a premium learning ecosystem providing expert-led courses,
                        professional growth tools, and community discussion hubs. We specialize in:</p>
                    <ul class="module-text mt-3">
                        <li>High-quality interactive materials</li>
                        <li>Global learner networking</li>
                        <li>Industry-recognized certificates</li>
                        <li>Personalized progress tracking</li>
                    </ul>
                    <div class="plain-english-min">
                        <span class="tldr-pill">TL;DR</span>
                        <span class="tldr-text">We provide the high-tech tools to help you learn and grow your
                            professional career.</span>
                    </div>
                </div>

                <!-- 3. User Accounts (Bento Tall) -->
                <div class="bento-card card bento-tall" data-aos="fade-up">
                    <div class="accent-line line-violet"></div>
                    <div class="module-icon bg-violet-glass"><i class="bi bi-shield-lock"></i></div>
                    <h3 class="module-title">3. Account Integrity</h3>
                    <p class="module-text">Your account is personal. You must maintain the confidentiality of your
                        credentials. You are responsible for all actions taken through your account.</p>
                    <div class="plain-english-min">
                        <span class="tldr-pill">TL;DR</span>
                        <span class="tldr-text">Keep your password safe and don't share your login.</span>
                    </div>
                </div>

                <!-- 4. Enrollment -->
                <div class="bento-card card" data-aos="fade-up">
                    <div class="accent-line line-amber"></div>
                    <div class="module-icon bg-amber-glass"><i class="bi bi-mortarboard"></i></div>
                    <h3 class="module-title small text-uppercase">4. Enrollment</h3>
                    <p class="module-text small">Personal license to learn. Lifetime access to your enrolled courses and
                        all future updates.</p>
                </div>

                <!-- 5. Payments -->
                <div class="bento-card card" data-aos="fade-up">
                    <div class="accent-line line-blue"></div>
                    <div class="module-icon bg-blue-glass"><i class="bi bi-unlock"></i></div>
                    <h3 class="module-title small text-uppercase">5. Free Access</h3>
                    <p class="module-text small">Edvora courses are provided free of charge. No course payment or refund process applies.</p>
                </div>

                <!-- 6. Conduct (Bento Wide) -->
                <div class="bento-card card bento-wide" data-aos="fade-up">
                    <div class="accent-line line-teal"></div>
                    <div class="module-icon bg-teal-glass"><i class="bi bi-person-lines-fill"></i></div>
                    <h3 class="module-title">6. Community Conduct</h3>
                    <p class="module-text">Respect is mandatory. No harassment, content scraping, or unauthorized
                        redistribution of Edvora materials is allowed.</p>
                    <div class="plain-english-min">
                        <span class="tldr-pill">TL;DR</span>
                        <span class="tldr-text">Be kind to others and don't steal or share our course videos.</span>
                    </div>
                </div>

                <!-- 7. IP Rights -->
                <div class="bento-card card" data-aos="fade-up">
                    <div class="accent-line line-violet"></div>
                    <div class="module-icon bg-violet-glass"><i class="bi bi-incognito"></i></div>
                    <h3 class="module-title small text-uppercase">7. IP Rights</h3>
                    <p class="module-text small">Content belongs to Edvora. You have a license to learn, not to own the
                        intellectual property.</p>
                </div>

                <!-- 8. Privacy (Bento Dark) -->
                <div class="bento-card card bento-dark" data-aos="fade-up">
                    <span class="bento-icon-main text-white"><i class="bi bi-shield-shaded"></i></span>
                    <h3 class="module-title text-white">8. Privacy Policy</h3>
                    <p class="module-text text-white opacity-75">Your data is yours. We only use insights to improve
                        your learning experience. We never sell your personal information.</p>
                    <div class="plain-english-min border-white border-opacity-10">
                        <span class="tldr-pill bg-white text-dark">SECURE</span>
                        <span class="tldr-text text-white opacity-50">Your privacy is our biggest commitment.</span>
                    </div>
                </div>

                <!-- 9. Disclaimers -->
                <div class="bento-card card" data-aos="fade-up">
                    <div class="accent-line line-amber"></div>
                    <div class="module-icon bg-amber-glass"><i class="bi bi-exclamation-triangle"></i></div>
                    <h3 class="module-title small text-uppercase">9. Disclaimers</h3>
                    <p class="module-text small">Service provided "as is". We aim for 99.9% uptime and accurate content
                        at all times.</p>
                </div>

                <!-- 10. Liability -->
                <div class="bento-card card" data-aos="fade-up">
                    <div class="accent-line line-blue"></div>
                    <div class="module-icon bg-blue-glass"><i class="bi bi-file-earmark-lock"></i></div>
                    <h3 class="module-title small text-uppercase">10. Liability</h3>
                    <p class="module-text small">Limitation on indirect or incidental damages related to platform usage.
                    </p>
                </div>

                <!-- 11. Termination -->
                <div class="bento-card card" data-aos="fade-up">
                    <div class="accent-line line-teal"></div>
                    <div class="module-icon bg-teal-glass"><i class="bi bi-door-closed"></i></div>
                    <h3 class="module-title small text-uppercase">11. Termination</h3>
                    <p class="module-text small">We reserve the right to suspend accounts violating these terms with
                        prior notice.</p>
                </div>

                <!-- 12. Changes -->
                <div class="bento-card card" data-aos="fade-up">
                    <div class="accent-line line-violet"></div>
                    <div class="module-icon bg-violet-glass"><i class="bi bi-arrow-repeat"></i></div>
                    <h3 class="module-title small text-uppercase">12. Changes</h3>
                    <p class="module-text small">Terms may be updated. Continued use implies acceptance of new terms.
                    </p>
                </div>

                <!-- 13. Governing Law -->
                <div class="bento-card card" data-aos="fade-up">
                    <div class="accent-line line-amber"></div>
                    <div class="module-icon bg-amber-glass"><i class="bi bi-bank"></i></div>
                    <h3 class="module-title small text-uppercase">13. Governing Law</h3>
                    <p class="module-text small">These terms are governed by applicable law.</p>
                </div>

                <!-- 14. Support / Contact (Bento Wide) -->
                <div class="bento-card card bento-wide" data-aos="fade-up">
                    <div class="accent-line line-blue"></div>
                    <div class="module-icon bg-blue-glass"><i class="bi bi-headset"></i></div>
                    <h3 class="module-title">Support & Inquiries</h3>
                    <p class="small text-muted mt-2 mb-0">Use the Edvora contact form for legal and general inquiries.</p>
                    <a href="{{ route('contact') }}" class="text-primary text-decoration-none fw-bold">Open contact form</a>
                </div>
            </div>

            <!-- Visual Separator -->
            <div class="py-5">
                <div class="mx-auto"
                    style="width: 50px; height: 3px; background: linear-gradient(90deg, transparent, var(--primary-blue), transparent);">
                </div>
            </div>

            <div class="text-center text-muted small px-4">
                <p class="mb-0">For legal inquiries, use the <a href="{{ route('contact') }}"
                        class="text-primary text-decoration-none fw-bold">Edvora contact form</a>.</p>
                <p>Edvora Tech Educational Platform © 2024</p>
            </div>
        </div>
    </section>

    <!-- Modern Footer -->
    

    <!-- Bootstrap 5 JS -->
    
    <!-- Custom JS -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
