@extends('layouts.app')

@section('title', 'Our Story - ' . ($siteSettings->get('company_name', 'Edvora Tech')))

@push('styles')
<link href="{{ asset('assets/css/company-pages.css') }}?v={{ file_exists(public_path('assets/css/company-pages.css')) ? filemtime(public_path('assets/css/company-pages.css')) : time() }}" rel="stylesheet" />
@endpush

@section('content')
    <x-company-preloader />

    @php
        $hero = $content['hero'] ?? [];
        $genesis = $content['genesis'] ?? [];
        $vision = $content['vision'] ?? [];
        $teamStory = $content['team_story'] ?? [];
        $missionToday = $content['mission_today'] ?? [];
    @endphp

    <div class="cp-page-body">
        <canvas id="cp3dCanvas" class="cp-3d-canvas"></canvas>

        <!-- Hero Section (keeps own dark styling) -->
        <x-page-hero
            layout="centered"
            badgeIcon="bi bi-gem"
            badgeText="{{ $hero['badge'] ?? 'Our Journey' }}"
            badgeClass="badge-gold"
            titlePrefix="{{ $hero['title_prefix'] ?? 'The Story Behind' }}"
            highlight="{{ $hero['highlight'] ?? ($siteSettings->get('company_name', 'Edvora')) }}"
            subtitle="{{ $hero['subtitle'] ?? 'From an ambitious dream to empowering thousands of learners through accessible, cutting-edge technology education.' }}"
            :floatingIcons="['bi bi-rocket-takeoff', 'bi bi-lightbulb', 'bi bi-stars', 'bi bi-award', 'bi bi-globe']"
        />

        <!-- Story Chapters -->
        <section class="cp-section">
            <div class="container position-relative" style="z-index: 2;">

                <!-- Chapter 1: The Genesis -->
                <div class="row g-4 mb-5 align-items-stretch">
                    <div class="col-lg-6">
                        <div class="cp-story-chapter-card">
                            <span class="cp-story-index">01</span>
                            <div class="cp-card-corner-glow" style="background: var(--cp-primary);"></div>
                            <div class="cp-icon-box cp-icon-box-blue">
                                <i class="bi {{ $genesis['icon'] ?? 'bi-lightbulb' }}"></i>
                            </div>
                            <span class="cp-badge cp-badge-blue mb-2">Chapter 01 // Origins</span>
                            <h3 class="fw-bold mb-3" style="color: var(--cp-text-heading); font-size: 1.85rem;">{{ $genesis['title'] ?? 'The Genesis' }}</h3>
                            <p class="lead mb-0" style="color: var(--cp-text-body); line-height: 1.8; font-size: 1.05rem;">
                                {{ $genesis['text'] ?? 'Our story begins with a brilliant idea that emerged from the intersection of technology and necessity. Conceived with the dream of revolutionizing education through innovative technology, Edvora was built to empower learners everywhere.' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="cp-visual-podium">
                            <div class="cp-podium-orb" style="width: 180px; height: 180px; background: rgba(31, 143, 255, 0.25); top: 15%; left: 20%;"></div>
                            <div class="cp-podium-orb" style="width: 120px; height: 120px; background: rgba(0, 210, 255, 0.2); bottom: 15%; right: 25%;"></div>
                            <i class="bi bi-rocket-takeoff-fill cp-podium-center-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Chapter 2: The Vision -->
                <div class="row g-4 mb-5 align-items-stretch">
                    <div class="col-lg-6 order-lg-2">
                        <div class="cp-story-chapter-card">
                            <span class="cp-story-index">02</span>
                            <div class="cp-card-corner-glow" style="background: var(--cp-cyan);"></div>
                            <div class="cp-icon-box cp-icon-box-cyan">
                                <i class="bi {{ $vision['icon'] ?? 'bi-eye' }}"></i>
                            </div>
                            <span class="cp-badge cp-badge-cyan mb-2">Chapter 02 // Outlook</span>
                            <h3 class="fw-bold mb-3" style="color: var(--cp-text-heading); font-size: 1.85rem;">{{ $vision['title'] ?? 'The Vision' }}</h3>
                            <p class="lead mb-0" style="color: var(--cp-text-body); line-height: 1.8; font-size: 1.05rem;">
                                {{ $vision['text'] ?? "We recognized technology's power to elevate society's knowledge. Our mission became clear: create a platform that democratizes quality education and empowers every individual to reach their full potential through innovative learning experiences." }}
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <div class="cp-visual-podium">
                            <div class="cp-podium-orb" style="width: 170px; height: 170px; background: rgba(0, 210, 255, 0.2); top: 20%; right: 20%;"></div>
                            <div class="cp-podium-orb" style="width: 140px; height: 140px; background: rgba(139, 92, 246, 0.18); bottom: 20%; left: 20%;"></div>
                            <i class="bi bi-eye-fill cp-podium-center-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Chapter 3: The Edvora Team -->
                <div class="row g-4 mb-5 align-items-stretch">
                    <div class="col-lg-6">
                        <div class="cp-story-chapter-card">
                            <span class="cp-story-index">03</span>
                            <div class="cp-card-corner-glow" style="background: var(--cp-purple);"></div>
                            <div class="cp-icon-box cp-icon-box-purple">
                                <i class="bi {{ $teamStory['icon'] ?? 'bi-people-fill' }}"></i>
                            </div>
                            <span class="cp-badge cp-badge-purple mb-2">Chapter 03 // Synergy</span>
                            <h3 class="fw-bold mb-3" style="color: var(--cp-text-heading); font-size: 1.85rem;">{{ $teamStory['title'] ?? 'The Edvora Team' }}</h3>
                            <p class="lead mb-3" style="color: var(--cp-text-body); line-height: 1.8; font-size: 1.05rem;">
                                {{ $teamStory['paragraph_1'] ?? 'Our passionate group of innovators, educators, and software engineers work around the clock to create an unmatched educational experience.' }}
                            </p>
                            @if(!empty($teamStory['paragraph_2']))
                            <p class="mb-0" style="color: var(--cp-text-body); line-height: 1.8;">
                                {{ $teamStory['paragraph_2'] }}
                            </p>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="cp-visual-podium">
                            <div class="cp-podium-orb" style="width: 190px; height: 190px; background: rgba(139, 92, 246, 0.2); top: 15%; left: 25%;"></div>
                            <div class="cp-podium-orb" style="width: 120px; height: 120px; background: rgba(31, 143, 255, 0.18); bottom: 15%; right: 20%;"></div>
                            <i class="bi bi-diagram-3-fill cp-podium-center-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Mission Today & Stats -->
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="cp-glass-card cp-glass-card-glow-gold p-4 p-lg-5 text-center">
                            <div class="cp-card-corner-glow" style="background: var(--cp-gold);"></div>
                            <div class="cp-icon-box cp-icon-box-gold mx-auto">
                                <i class="bi {{ $missionToday['icon'] ?? 'bi-mortarboard' }}"></i>
                            </div>
                            <span class="cp-badge cp-badge-gold mb-3">Present & Beyond</span>
                            <h2 class="cp-section-title mb-3">{{ $missionToday['title'] ?? 'Our Mission Today' }}</h2>
                            <p class="cp-section-desc mb-5" style="max-width: 800px;">
                                {{ $missionToday['text'] ?? "Dedicated to advancing society's knowledge through cutting-edge educational technology. We believe that by providing accessible, high-quality learning experiences, we can empower individuals and communities to thrive in an increasingly digital world." }}
                            </p>

                            @if(!empty($missionToday['stats']))
                            <div class="row g-4 justify-content-center pt-2">
                                @foreach($missionToday['stats'] as $st)
                                <div class="col-md-4 col-sm-6">
                                    <div class="cp-stat-card">
                                        <div class="cp-stat-value">{{ $st['value'] ?? '' }}</div>
                                        <div class="cp-stat-label">{{ $st['label'] ?? '' }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
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
