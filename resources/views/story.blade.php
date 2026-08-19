@extends('layouts.app')

@section('title', 'Our Story - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/story.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="story-hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 text-center">
                    <div class="story-badge">
                        <i class="bi bi-gem"></i>
                        <span>Our Journey</span>
                    </div>

                    <h1 class="story-title">
                        <span>The Story Behind</span>
                        <br>
                        <span class="highlight">Edvora</span>
                    </h1>

                    <p class="story-subtitle">
                        From a revolutionary idea to transforming education through technology
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Content -->
    <section class="py-5 story-content-section">
        <div class="container">
            <!-- The Beginning -->
            <div class="row mb-5">
                <div class="col-lg-6 anim-slide-in-left">
                    <div class="story-card">
                        <div class="story-icon">
                            <i class="bi bi-lightbulb"></i>
                        </div>
                        <h3 class="text-brand-blue fw-bold mb-3">The Genesis</h3>
                        <p class="para-lg">
                            Our story begins like the revolutionary tale from StartUp - with a brilliant idea that
                            emerged from the intersection of technology and necessity. Just as GenCoin was born from the
                            vision of digital transformation, Edvora was conceived with the dream of revolutionizing
                            education through innovative technology.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 anim-slide-in-right anim-both anim-delay-03">
                    <div class="story-visual visual-alt-1">
                        <div class="visual-elements">
                            <div class="bubble white float1" style="top: 20%; left: 20%; width: 60px; height: 60px;">
                            </div>
                            <div class="bubble orange float2"
                                style="bottom: 30%; right: 25%; width: 40px; height: 40px;"></div>
                            <div class="bubble deep-orange float3"
                                style="top: 50%; right: 15%; width: 50px; height: 50px;"></div>
                        </div>
                        <i class="bi bi-rocket-takeoff-fill z-top" style="font-size: 6rem;"></i>
                    </div>
                </div>
            </div>

            <!-- The Vision -->
            <div class="row mb-5">
                <div class="col-lg-6 order-lg-2 anim-slide-in-right">
                    <div class="story-card alt-1">
                        <div class="story-icon orange">
                            <i class="bi bi-eye"></i>
                        </div>
                        <h3 class="text-brand-blue fw-bold mb-3">The Vision</h3>
                        <p class="para-lg">
                            Like the protagonists in StartUp who saw the potential of cryptocurrency to transform
                            finance, we recognized technology's power to elevate society's knowledge. Our mission became
                            clear: create a platform that democratizes quality education and empowers every individual
                            to reach their full potential through innovative learning experiences.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1 anim-slide-in-left anim-both anim-delay-03">
                    <div class="story-visual visual-alt-1">
                        <div class="visual-elements">
                            <div class="bubble white float2" style="top: 15%; right: 20%; width: 70px; height: 70px;">
                            </div>
                            <div class="bubble"
                                style="bottom: 20%; left: 15%; width: 45px; height: 45px; background: rgba(31, 143, 255, 0.1);">
                            </div>
                            <div class="bubble light float3" style="top: 40%; left: 10%; width: 55px; height: 55px;">
                            </div>
                        </div>
                        <i class="bi bi-eye-fill z-top" style="font-size: 6rem;"></i>
                    </div>
                </div>
            </div>

            <!-- The Edvora Team Story (NEW SECTION) -->
            <div class="row mb-5">
                <div class="col-lg-6 anim-slide-in-left">
                    <div class="story-visual visual-alt-1">
                        <div class="visual-elements">
                            <div class="bubble orange float1" style="top: 10%; left: 30%; width: 80px; height: 80px;">
                            </div>
                            <div class="bubble white float3"
                                style="bottom: 20%; right: 10%; width: 60px; height: 60px;"></div>
                        </div>
                        <i class="bi bi-diagram-3-fill z-top" style="font-size: 6rem;"></i>
                    </div>
                </div>
                <div class="col-lg-6 anim-slide-in-right anim-both anim-delay-03">
                    <div class="story-card alt-2">
                        <div class="story-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h3 class="text-brand-blue fw-bold mb-3">The Edvora Team</h3>
                        <p class="para-lg">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                            laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <p class="para-lg">
                            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla
                            pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt
                            mollit anim id est laborum.
                        </p>
                    </div>
                </div>
            </div>

            <!-- The Mission -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="mission-card text-center">
                        <div class="mission-icon">
                            <i class="bi bi-mortarboard"></i>
                        </div>
                        <h2 class="mission-title">Our Mission Today</h2>
                        <p class="mission-text">
                            Inspired by the entrepreneurial spirit and technological innovation depicted in StartUp, we
                            are dedicated to advancing society's knowledge through cutting-edge educational technology.
                            We believe that by providing accessible, high-quality learning experiences, we can empower
                            individuals and communities to thrive in an increasingly digital world.
                        </p>
                        <div class="mission-stats">
                            <div class="stat-item">
                                <div class="value text-brand-blue">10K+</div>
                                <div class="label">Students Empowered</div>
                            </div>
                            <div class="stat-item">
                                <div class="value text-brand-blue">500+</div>
                                <div class="label">Courses Available</div>
                            </div>
                            <div class="stat-item">
                                <div class="value text-brand-blue">98%</div>
                                <div class="label">Success Rate</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
