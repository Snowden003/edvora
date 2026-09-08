@extends('layouts.app')

@section('title', 'About Us - ' . ($siteSettings->get('company_name', 'Edvora Tech')))

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/about.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/glass-panel.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush


<!-- This is a comment for test -->
@section('content')
    @php
        $hero = $content['hero'] ?? [];
        $mv = $content['mission_vision'] ?? [];
        $timeline = $content['timeline'] ?? [];
        $team = $content['team'] ?? [];
        $cta = $content['cta'] ?? [];
    @endphp

    <!-- Premium Hero Section -->
    <x-page-hero
        layout="centered"
        badgeIcon="bi bi-info-circle-fill"
        badgeText="About Edvora"
        titlePrefix="{{ $hero['title'] ?? 'The Story of' }}"
        highlight="{{ $hero['highlight'] ?? ($siteSettings->get('company_name', 'Edvora')) }}"
        subtitle="{{ $hero['subtitle'] ?? 'Pioneering the future of education with passion, innovation, and an unwavering commitment to accessible learning.' }}"
        :floatingIcons="['bi bi-compass', 'bi bi-lightbulb', 'bi bi-globe', 'bi bi-mortarboard', 'bi bi-award']"
    />

    <!-- Mission & Vision -->
    <section class="py-5 section-animated" style="background-color: #f8f9fa;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">{{ $mv['section_title'] ?? 'Our Mission & Vision' }}</h2>
                <p class="text-muted">{{ $mv['section_subtitle'] ?? "The driving force behind Edvora's purpose." }}</p>
            </div>
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="about-glass-card h-100">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i class="bi {{ $mv['mission_icon'] ?? 'bi-bullseye' }} fs-1" style="color: #1F8FFF;"></i>
                            </div>
                            <h3 class="fw-bold mb-3 text-dark">{{ $mv['mission_title'] ?? 'Our Mission' }}</h3>
                            <p class="text-muted">
                                {{ $mv['mission_text'] ?? 'To democratize education by providing world-class learning experiences that empower individuals to achieve their personal and professional goals, regardless of their background or location.' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-glass-card h-100">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i class="bi {{ $mv['vision_icon'] ?? 'bi-eye' }} fs-1" style="color: #1F8FFF;"></i>
                            </div>
                            <h3 class="fw-bold mb-3 text-dark">{{ $mv['vision_title'] ?? 'Our Vision' }}</h3>
                            <p class="text-muted">
                                {{ $mv['vision_text'] ?? "To become the world's leading online education platform, creating a global community of lifelong learners who drive innovation and positive change in their industries and communities." }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story Timeline -->
    @if(!empty($timeline['items']))
    <section class="py-5 section-animated bg-white overflow-hidden">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark display-5">{{ $timeline['section_title'] ?? 'Our Journey' }}</h2>
                <div class="mx-auto bg-primary mb-3" style="width: 80px; height: 4px; border-radius: 2px;"></div>
                <p class="text-muted lead">{{ $timeline['section_subtitle'] ?? 'A timeline of our milestones and achievements.' }}</p>
            </div>

            <div class="timeline-premium">
                @foreach($timeline['items'] as $item)
                <div class="timeline-item-premium item-{{ $item['color'] ?? 'blue' }}">
                    <div class="timeline-icon-premium">
                        <i class="bi {{ $item['icon'] ?? 'bi-lightbulb-fill' }}"></i>
                    </div>
                    <div class="timeline-content-wrapper">
                        <div class="timeline-content-premium">
                            <span class="timeline-year">{{ $item['year'] ?? '' }}</span>
                            <h3 class="timeline-title">{{ $item['title'] ?? '' }}</h3>
                            <p class="timeline-desc mb-0">{{ $item['description'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Meet Our Team Section -->
    @if(!empty($team['members']))
    <section class="py-5 section-animated" style="background-color: #f8f9fa;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark display-5">{{ $team['section_title'] ?? 'Meet Our Team' }}</h2>
                <div class="mx-auto bg-primary mb-3" style="width: 80px; height: 4px; border-radius: 2px;"></div>
                <p class="lead text-muted">{{ $team['section_subtitle'] ?? "The passionate individuals driving Edvora's mission forward." }}</p>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach($team['members'] as $member)
                <div class="col-lg-4 col-md-6 cat-{{ $member['category'] ?? 'tech' }}">
                    <div class="team-card-v2">
                        <div class="team-profile-placeholder">
                            <i class="bi {{ $member['icon'] ?? 'bi-person-badge-fill' }}"></i>
                        </div>
                        <h5 class="team-name-v2">{{ $member['name'] ?? '' }}</h5>
                        <span class="team-role-v2">{{ $member['role'] ?? '' }}</span>
                        <p class="team-bio-v2">{{ $member['bio'] ?? '' }}</p>
                        <div class="team-social-v2">
                            @if(!empty($member['linkedin']))
                                <a href="{{ $member['linkedin'] }}" target="_blank" rel="noopener"><i class="bi bi-linkedin"></i></a>
                            @endif
                            @if(!empty($member['twitter']))
                                <a href="{{ $member['twitter'] }}" target="_blank" rel="noopener"><i class="bi bi-twitter"></i></a>
                            @endif
                            @if(!empty($member['github']))
                                <a href="{{ $member['github'] }}" target="_blank" rel="noopener"><i class="bi bi-github"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="py-5" style="background: #ffffff;">
        <div class="container">
            <div class="row align-items-center about-glass-card p-4 p-lg-5">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-3 text-dark">{{ $cta['title'] ?? 'Ready to Start Your Learning Journey?' }}</h2>
                    <p class="lead mb-0 text-muted">{{ $cta['subtitle'] ?? 'Join thousands of students who are already transforming their careers with Edvora.' }}</p>
                </div>
                <div class="col-lg-4 text-center mt-3 mt-lg-0">
                    <a href="{{ $cta['button_url'] ?? route('register') }}" class="btn btn-lg fw-bold px-5 py-3"
                        style="background: linear-gradient(135deg, #1F8FFF, #00A8FF); color: #fff; box-shadow: 0 4px 15px rgba(31,143,255,0.4); border-radius: 30px; transition: all 0.3s ease; border: none;">
                        <i class="bi bi-rocket-takeoff me-2"></i>{{ $cta['button_text'] ?? 'Get Started Today' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/about.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
