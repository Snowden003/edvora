@extends('layouts.app')

@section('title', 'About Us - ' . ($siteSettings->get('company_name', 'Edvora Tech')))

@push('styles')
<link href="{{ asset('assets/css/company-pages.css') }}?v={{ file_exists(public_path('assets/css/company-pages.css')) ? filemtime(public_path('assets/css/company-pages.css')) : time() }}" rel="stylesheet" />
@endpush

@section('content')
    <x-company-preloader />

    @php
        $hero = $content['hero'] ?? [];
        $mv = $content['mission_vision'] ?? [];
        $timeline = $content['timeline'] ?? [];
        $team = $content['team'] ?? [];
        $cta = $content['cta'] ?? [];
    @endphp

    <div class="cp-page-body">
        <!-- 3D Crystal Constellation Background -->
        <canvas id="cp3dCanvas" class="cp-3d-canvas"></canvas>

        <!-- Premium Hero Section (keeps own dark styling) -->
        <x-page-hero
            layout="centered"
            badgeIcon="bi bi-info-circle-fill"
            badgeText="About Edvora"
            titlePrefix="{{ $hero['title'] ?? 'The Story of' }}"
            highlight="{{ $hero['highlight'] ?? ($siteSettings->get('company_name', 'Edvora')) }}"
            subtitle="{{ $hero['subtitle'] ?? 'Pioneering the future of education with passion, innovation, and an unwavering commitment to accessible learning.' }}"
            :floatingIcons="['bi bi-compass', 'bi bi-lightbulb', 'bi bi-globe', 'bi bi-mortarboard', 'bi bi-award']"
        />

        <!-- Mission & Vision Section -->
        <section class="cp-section">
            <div class="container position-relative" style="z-index: 2;">
                <div class="text-center mb-5">
                    <span class="cp-badge cp-badge-cyan">
                        <i class="bi bi-compass-fill"></i> Core Purpose
                    </span>
                    <h2 class="cp-section-title">{{ $mv['section_title'] ?? 'Our Mission & Vision' }}</h2>
                    <p class="cp-section-desc">{{ $mv['section_subtitle'] ?? "The driving force behind Edvora's purpose and global commitment." }}</p>
                </div>

                <div class="row g-4 align-items-stretch">
                    <!-- Mission -->
                    <div class="col-lg-6">
                        <div class="cp-glass-card cp-glass-card-glow-blue h-100 d-flex flex-column text-start">
                            <div class="cp-card-corner-glow" style="background: var(--cp-primary);"></div>
                            <div class="cp-icon-box cp-icon-box-blue">
                                <i class="bi {{ $mv['mission_icon'] ?? 'bi-bullseye' }}"></i>
                            </div>
                            <span class="cp-badge cp-badge-blue mb-2">Our Mission</span>
                            <h3 class="fw-bold mb-3" style="color: var(--cp-text-heading);">{{ $mv['mission_title'] ?? 'Democratizing Tech Education' }}</h3>
                            <p class="mb-0 flex-grow-1" style="color: var(--cp-text-body); line-height: 1.8; font-size: 1.05rem;">
                                {{ $mv['mission_text'] ?? 'To democratize education by providing world-class learning experiences that empower individuals to achieve their personal and professional goals, regardless of their background or location.' }}
                            </p>
                        </div>
                    </div>
                    <!-- Vision -->
                    <div class="col-lg-6">
                        <div class="cp-glass-card cp-glass-card-glow-cyan h-100 d-flex flex-column text-start">
                            <div class="cp-card-corner-glow" style="background: var(--cp-cyan);"></div>
                            <div class="cp-icon-box cp-icon-box-cyan">
                                <i class="bi {{ $mv['vision_icon'] ?? 'bi-eye' }}"></i>
                            </div>
                            <span class="cp-badge cp-badge-cyan mb-2">Our Vision</span>
                            <h3 class="fw-bold mb-3" style="color: var(--cp-text-heading);">{{ $mv['vision_title'] ?? 'Global Community of Innovators' }}</h3>
                            <p class="mb-0 flex-grow-1" style="color: var(--cp-text-body); line-height: 1.8; font-size: 1.05rem;">
                                {{ $mv['vision_text'] ?? "To become the world's leading online education platform, creating a global community of lifelong learners who drive innovation and positive change in their industries and communities." }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Story Timeline -->
        @if(!empty($timeline['items']))
        <section class="cp-section cp-section-divider">
            <div class="container position-relative" style="z-index: 2;">
                <div class="text-center mb-5">
                    <span class="cp-badge cp-badge-purple">
                        <i class="bi bi-clock-history"></i> Milestones
                    </span>
                    <h2 class="cp-section-title">{{ $timeline['section_title'] ?? 'Our Journey' }}</h2>
                    <p class="cp-section-desc">{{ $timeline['section_subtitle'] ?? 'A timeline of key breakthroughs, scaling horizons, and achievements.' }}</p>
                </div>

                <div class="cp-timeline-wrapper">
                    @foreach($timeline['items'] as $idx => $item)
                        @php $side = ($idx % 2 === 0) ? 'left' : 'right'; @endphp
                        <div class="cp-timeline-item {{ $side }}">
                            <div class="cp-timeline-node">
                                <i class="bi {{ $item['icon'] ?? 'bi-star-fill' }}"></i>
                            </div>
                            <div class="cp-timeline-content">
                                <span class="cp-timeline-year">{{ $item['year'] ?? '' }}</span>
                                <h4 class="fw-bold mb-2" style="color: var(--cp-text-heading); font-size: 1.2rem;">{{ $item['title'] ?? '' }}</h4>
                                <p class="mb-0 small" style="color: var(--cp-text-body); line-height: 1.7;">{{ $item['description'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Meet Our Team Section -->
        @if(!empty($team['members']))
        <section class="cp-section">
            <div class="container position-relative" style="z-index: 2;">
                <div class="text-center mb-4">
                    <span class="cp-badge cp-badge-gold">
                        <i class="bi bi-people-fill"></i> Brilliant Minds
                    </span>
                    <h2 class="cp-section-title">{{ $team['section_title'] ?? 'Meet Our Team' }}</h2>
                    <p class="cp-section-desc">{{ $team['section_subtitle'] ?? "The passionate innovators, educators, and architects behind Edvora." }}</p>
                </div>

                <div class="cp-filter-nav">
                    <button class="cp-filter-btn active" data-filter="all">All Members</button>
                    <button class="cp-filter-btn" data-filter="tech">Engineering & Tech</button>
                    <button class="cp-filter-btn" data-filter="creative">Creative & Design</button>
                    <button class="cp-filter-btn" data-filter="marketing">Growth & Outreach</button>
                </div>

                <div class="row g-4 justify-content-center">
                    @foreach($team['members'] as $member)
                        @php $category = strtolower($member['category'] ?? 'tech'); @endphp
                        <div class="col-lg-4 col-md-6 cp-team-col" data-category="{{ $category }}">
                            <div class="cp-team-card h-100 d-flex flex-column">
                                <div class="cp-team-avatar-box">
                                    <i class="bi {{ $member['icon'] ?? 'bi-person-badge-fill' }}"></i>
                                </div>
                                <h4 class="fw-bold mb-1" style="color: var(--cp-text-heading); font-size: 1.2rem;">{{ $member['name'] ?? '' }}</h4>
                                <span class="cp-badge cp-badge-blue align-self-center my-2">{{ $member['role'] ?? '' }}</span>
                                <p class="small mb-4 flex-grow-1" style="color: var(--cp-text-body); line-height: 1.6;">{{ $member['bio'] ?? '' }}</p>
                                <div class="cp-team-social">
                                    @if(!empty($member['linkedin']))
                                        <a href="{{ $member['linkedin'] }}" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                                    @endif
                                    @if(!empty($member['twitter']))
                                        <a href="{{ $member['twitter'] }}" target="_blank" rel="noopener" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                                    @endif
                                    @if(!empty($member['github']))
                                        <a href="{{ $member['github'] }}" target="_blank" rel="noopener" aria-label="GitHub"><i class="bi bi-github"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Conversion CTA Section -->
        <section class="cp-section-sm pb-5">
            <div class="container position-relative" style="z-index: 2;">
                <div class="cp-glass-card cp-glass-card-glow-blue p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8 text-start">
                            <span class="cp-badge cp-badge-cyan mb-2">Get Started</span>
                            <h2 class="fw-bold mb-2" style="color: var(--cp-text-heading); font-size: 2rem;">{{ $cta['title'] ?? 'Ready to Start Your Learning Journey?' }}</h2>
                            <p class="mb-0 lead" style="color: var(--cp-text-body); font-size: 1.05rem;">
                                {{ $cta['subtitle'] ?? 'Join thousands of students who are already transforming their careers with Edvora.' }}
                            </p>
                        </div>
                        <div class="col-lg-4 text-lg-end text-center">
                            <a href="{{ $cta['button_url'] ?? route('register') }}" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg"
                               style="background: linear-gradient(135deg, #1F8FFF, #00D2FF); border: none;">
                                <i class="bi bi-rocket-takeoff me-2"></i>{{ $cta['button_text'] ?? 'Get Started Today' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/company-bg-3d.js') }}?v={{ file_exists(public_path('assets/js/company-bg-3d.js')) ? filemtime(public_path('assets/js/company-bg-3d.js')) : time() }}" defer></script>
<script src="{{ asset('assets/js/company-pages.js') }}?v={{ file_exists(public_path('assets/js/company-pages.js')) ? filemtime(public_path('assets/js/company-pages.js')) : time() }}" defer></script>
@endpush
