@extends('layouts.app')

@section('title', 'How We Work - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/about.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!-- Header -->
    

    <!-- Hero Section -->
    <section class="hero-animated">
        <!-- Background Effects -->
        <div class="story-bg-effects">
        </div>

        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 text-center">
                    <!-- Badge -->
                    <div class="story-badge">
                        <i class="bi bi-heart-fill text-brand-yellow heart-beat me-2"></i>
                        <span class="text-white fw-semibold" style="font-size:1.1rem">Non-Profit Organization</span>
                    </div>

                    <!-- Title -->
                    <h1 class="story-title">
                        <span>How We</span><br>
                        <span class="highlight">Work</span>
                    </h1>

                    <!-- Subtitle -->
                    <p class="story-subtitle">
                        Empowering youth through education, community, and technology
                    </p>
                    <!-- CTA Buttons -->
                    <div class="hero-cta">
                        <a href="{{ route('courses.index') }}" class="btn-hero-primary">
                            <i class="bi bi-collection-play me-2"></i>Explore Programs
                        </a>
                    </div>

                    <!-- Hero Stats -->
                    <div class="hero-stats">
                        @foreach($heroStats as $stat)
                        <div class="hero-stat">
                            <div class="value">{{ $stat->value }}{{ $stat->suffix }}</div>
                            <div class="label">{{ $stat->label }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-5 section-alt-1">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="text-brand-blue fw-bold mb-4" style="font-size:2.5rem">Our Non-Profit Mission</h2>
                    <p class="para-lg" style="max-width:800px; margin:0 auto;">
                        Edvora is a dedicated non-profit organization committed to empowering young minds through
                        accessible, quality education and innovative technology solutions.
                    </p>
                </div>
            </div>

            <!-- Core Values -->
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <div class="story-card text-center h-100">
                        <div class="story-icon mx-auto">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h4 class="text-brand-blue fw-bold mb-3">Youth Empowerment</h4>
                        <p class="para-lg">We believe in the potential of every young person and work tirelessly to
                            provide them with the tools and opportunities they need to succeed.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="story-card alt-1 text-center h-100">
                        <div class="story-icon orange mx-auto">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h4 class="text-dark fw-bold mb-3">Community Impact</h4>
                        <p class="para-lg">Our work extends beyond individual learning to create positive change in
                            communities and society as a whole.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="story-card alt-2 text-center h-100">
                        <div class="story-icon mx-auto">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-brand-blue">Accessibility</h4>
                        <p class="para-lg">We ensure that quality education is accessible to all, regardless of economic
                            background or geographical location.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How We Operate -->
    <section class="py-5 section-alt-2">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="text-brand-blue fw-bold mb-5" style="font-size:2.5rem">How We Operate</h2>
                </div>
            </div>

            <div class="row g-5 align-items-center mb-5">
                <div class="col-lg-6">
                    <div class="story-card">
                        <h3 class="text-brand-blue fw-bold mb-4" style="font-size:2rem">
                            <i class="bi bi-gear-fill me-3 text-dark"></i>
                            Volunteer-Driven Model
                        </h3>
                        <p class="para-lg mb-4">
                            Our organization thrives on the dedication of passionate volunteers - educators,
                            technologists,
                            and youth advocates who believe in our mission.
                        </p>
                        <ul class="operate-list">
                            <li><strong>Expert Educators:</strong> Professional teachers volunteer their time</li>
                            <li><strong>Tech Innovators:</strong> Developers contribute to platform development</li>
                            <li><strong>Community Leaders:</strong> Local advocates help reach underserved communities
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="story-visual visual-alt-1">
                        <div class="visual-elements">
                            <div class="bubble white float1" style="top: 20%; left: 20%; width: 60px; height: 60px;">
                            </div>
                            <div class="bubble orange float2"
                                style="bottom: 30%; right: 25%; width: 40px; height: 40px;"></div>
                            <div class="bubble white float3"
                                style="top: 10%; right: 10%; width: 50px; height: 50px; opacity: 0.1"></div>
                        </div>
                        <i class="bi bi-people z-top" style="font-size:6rem"></i>
                    </div>
                </div>
            </div>

            <div class="row g-5 align-items-center mb-5">
                <div class="col-lg-6 order-lg-2">
                    <div class="story-card alt-1">
                        <h3 class="text-dark fw-bold mb-4" style="font-size:2rem">
                            <i class="bi bi-currency-dollar me-3 text-brand-blue"></i>
                            Funding & Sustainability
                        </h3>
                        <p class="para-lg mb-4">
                            As a non-profit organization, we operate through grants, donations, and partnerships with
                            educational institutions and technology companies.
                        </p>
                        <ul class="operate-list">
                            <li><strong>Educational Grants:</strong> Government and foundation funding</li>
                            <li><strong>Corporate Partnerships:</strong> Tech companies supporting our mission</li>
                            <li><strong>Community Donations:</strong> Individual supporters who believe in our cause
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1">
                    <div class="story-visual visual-alt-1">
                        <div class="visual-elements">
                            <div class="bubble orange float1" style="top: 25%; left: 25%; width: 70px; height: 70px;">
                            </div>
                            <div class="bubble white float3"
                                style="bottom: 15%; right: 15%; width: 50px; height: 50px;"></div>
                            <div class="bubble blue float2"
                                style="top: 15%; right: 20%; width: 60px; height: 60px; background: rgba(31, 143, 255, 0.1)">
                            </div>
                        </div>
                        <i class="bi bi-heart-fill z-top" style="font-size:6rem"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Statistics -->
    <section class="py-5 impact-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="text-brand-blue fw-bold mb-4" style="font-size:2.5rem">Our Impact</h2>
                    <p class="para-lg" style="max-width:600px; margin:0 auto;">
                        Together, we're making a difference in young lives across the globe
                    </p>
                </div>
            </div>
            <div class="row g-4">
                @foreach($impactStats as $stat)
                <div class="col-lg-3 col-md-6">
                    <div class="impact-card">
                        <div class="impact-value">{{ $stat->value }}{{ $stat->suffix }}</div>
                        <div class="impact-label">{{ $stat->label }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Modern Footer -->
    

    <!-- Bootstrap 5 JS -->
    


    <!-- Custom JS -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
