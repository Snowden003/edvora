@extends('layouts.app')

@section('title', 'How We Work - ' . ($siteSettings->get('company_name', 'Edvora Tech')))

@push('styles')
<link href="{{ asset('assets/css/company-pages.css') }}?v={{ file_exists(public_path('assets/css/company-pages.css')) ? filemtime(public_path('assets/css/company-pages.css')) : time() }}" rel="stylesheet" />
@endpush

@section('content')
    <x-company-preloader />

    @php
        $hero = $content['hero'] ?? [];
        $mission = $content['mission'] ?? [];
        $values = $content['values'] ?? [];
        $pillars = $content['pillars'] ?? [];
        $impactSection = $content['impact_section'] ?? [];
        $cta = $content['cta'] ?? [];
    @endphp

    <div class="cp-page-body">
        <canvas id="cp3dCanvas" class="cp-3d-canvas"></canvas>

        <!-- Hero Section (keeps own dark styling) -->
        <x-page-hero
            layout="centered"
            badgeIcon="bi bi-heart-fill text-danger"
            badgeText="{{ $hero['badge'] ?? 'Non-Profit Educational Mission' }}"
            titlePrefix="{{ $hero['title_prefix'] ?? 'How We' }}"
            highlight="{{ $hero['highlight'] ?? 'Work' }}"
            subtitle="{{ $hero['subtitle'] ?? 'Empowering youth and learners through accessible education, vibrant community, and modern technology.' }}"
            :floatingIcons="['bi bi-heart', 'bi bi-award', 'bi bi-lightning-charge', 'bi bi-people', 'bi bi-mortarboard']"
        >
            <x-slot:actionsSlot>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ $hero['cta_url'] ?? route('courses.index') }}" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg"
                       style="background: linear-gradient(135deg, #1F8FFF, #00D2FF); border: none;">
                        <i class="bi bi-collection-play me-2"></i>{{ $hero['cta_text'] ?? 'Explore Programs' }}
                    </a>
                    <a href="{{ route('about') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 py-3 fw-semibold"
                       style="border-color: rgba(255,255,255,0.2); backdrop-filter: blur(8px);">
                        <i class="bi bi-info-circle me-2"></i>About Us
                    </a>
                </div>
            </x-slot:actionsSlot>

            @if(isset($heroStats) && count($heroStats) > 0)
                <div class="row g-3 justify-content-center pt-4">
                    @foreach($heroStats as $stat)
                        <div class="col-md-4 col-sm-6">
                            <div class="hero-stat-pill w-100 justify-content-center">
                                <div class="stat-pill-content text-center">
                                    <span class="stat-pill-val">{{ $stat->value }}{{ $stat->suffix ?? '' }}</span>
                                    <span class="stat-pill-lbl">{{ $stat->label }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-page-hero>

        <!-- Mission Purpose Section -->
        <section class="cp-section">
            <div class="container position-relative" style="z-index: 2;">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-9">
                        <span class="cp-badge cp-badge-cyan">
                            <i class="bi bi-bullseye"></i> Our Purpose
                        </span>
                        <h2 class="cp-section-title mb-4" style="font-size: 2.3rem;">
                            {{ $mission['title'] ?? 'Our Non-Profit Mission' }}
                        </h2>
                        <p class="lead mx-auto mb-5" style="color: var(--cp-text-body); line-height: 1.9; font-size: 1.15rem; max-width: 820px;">
                            {{ $mission['description'] ?? 'Edvora is a dedicated non-profit organization committed to empowering young minds through accessible, quality education and innovative technology solutions.' }}
                        </p>
                    </div>
                </div>

                <!-- Core Values Grid -->
                @if(!empty($values))
                <div class="row g-4 align-items-stretch">
                    @foreach($values as $val)
                        @php
                            $color = $val['color'] ?? 'blue';
                            $glowClass = match($color) {
                                'orange', 'gold' => 'cp-glass-card-glow-gold',
                                'purple' => 'cp-glass-card-glow-purple',
                                'cyan' => 'cp-glass-card-glow-cyan',
                                default => 'cp-glass-card-glow-blue',
                            };
                            $iconBoxClass = match($color) {
                                'orange', 'gold' => 'cp-icon-box-gold',
                                'purple' => 'cp-icon-box-purple',
                                'cyan' => 'cp-icon-box-cyan',
                                default => 'cp-icon-box-blue',
                            };
                            $badgeClass = match($color) {
                                'orange', 'gold' => 'cp-badge-gold',
                                'purple' => 'cp-badge-purple',
                                'cyan' => 'cp-badge-cyan',
                                default => 'cp-badge-blue',
                            };
                        @endphp
                        <div class="col-lg-4 col-md-6">
                            <div class="cp-glass-card {{ $glowClass }} h-100 d-flex flex-column text-start">
                                <div class="cp-icon-box {{ $iconBoxClass }}">
                                    <i class="bi {{ $val['icon'] ?? 'bi-people-fill' }}"></i>
                                </div>
                                <span class="cp-badge {{ $badgeClass }} align-self-start mb-2">Core Value</span>
                                <h4 class="fw-bold mb-3" style="color: var(--cp-text-heading); font-size: 1.35rem;">{{ $val['title'] ?? '' }}</h4>
                                <p class="mb-0 flex-grow-1" style="color: var(--cp-text-body); line-height: 1.7;">
                                    {{ $val['description'] ?? '' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </section>

        <!-- Operational Pillars -->
        @if(!empty($pillars))
        <section class="cp-section cp-section-divider">
            <div class="container position-relative" style="z-index: 2;">
                <div class="text-center mb-5">
                    <span class="cp-badge cp-badge-purple">
                        <i class="bi bi-gear-wide-connected"></i> Operations
                    </span>
                    <h2 class="cp-section-title">How We Operate</h2>
                    <p class="cp-section-desc">A deep dive into our transparent, community-driven workflow and scalable infrastructure.</p>
                </div>

                <div class="row g-4 align-items-stretch">
                    @foreach($pillars as $idx => $pillar)
                    <div class="col-lg-4 col-md-6">
                        <div class="cp-pillar-card d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="cp-icon-box cp-icon-box-blue mb-0" style="width: 52px; height: 52px; font-size: 1.3rem;">
                                    <i class="bi {{ $pillar['icon'] ?? 'bi-gear-fill' }}"></i>
                                </div>
                                <span class="cp-badge cp-badge-cyan mb-0">Pillar 0{{ $idx + 1 }}</span>
                            </div>

                            <h4 class="fw-bold mb-1" style="color: var(--cp-text-heading); font-size: 1.25rem;">{{ $pillar['title'] ?? '' }}</h4>
                            @if(!empty($pillar['subtitle']))
                            <p class="small fw-semibold mb-3" style="color: var(--cp-primary);">{{ $pillar['subtitle'] }}</p>
                            @endif
                            <p class="small mb-4 flex-grow-1" style="color: var(--cp-text-body); line-height: 1.7;">
                                {{ $pillar['description'] ?? '' }}
                            </p>

                            @if(!empty($pillar['bullets']))
                            <div class="pt-3 mt-auto" style="border-top: 1px solid rgba(0,0,0,0.06);">
                                @foreach($pillar['bullets'] as $b)
                                <div class="d-flex align-items-start gap-2 mb-2">
                                    <div class="cp-bullet-icon mt-1"><i class="bi bi-check2"></i></div>
                                    <div>
                                        <strong class="d-block small" style="color: var(--cp-text-heading);">{{ $b['title'] ?? '' }}</strong>
                                        <span style="font-size: 0.82rem; color: var(--cp-text-muted);">{{ $b['desc'] ?? '' }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Global Impact Matrix -->
        @if(isset($impactStats) && count($impactStats) > 0)
        <section class="cp-section">
            <div class="container position-relative" style="z-index: 2;">
                <div class="row justify-content-center text-center mb-5">
                    <div class="col-lg-8">
                        <span class="cp-badge cp-badge-gold">
                            <i class="bi bi-globe-americas"></i> Metrics
                        </span>
                        <h2 class="cp-section-title">{{ $impactSection['title'] ?? 'Our Global Impact' }}</h2>
                        <p class="cp-section-desc">{{ $impactSection['subtitle'] ?? "Together, we're making a measurable difference in learners' lives across the globe." }}</p>
                    </div>
                </div>

                <div class="row g-4 justify-content-center text-center">
                    @foreach($impactStats as $stat)
                    <div class="col-lg-3 col-6">
                        <div class="cp-stat-card">
                            <div class="cp-stat-value">{{ $stat->value }}{{ $stat->suffix ?? '' }}</div>
                            <div class="cp-stat-label">{{ $stat->label }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- CTA Section -->
        <section class="cp-section-sm pb-5">
            <div class="container position-relative" style="z-index: 2;">
                <div class="cp-glass-card cp-glass-card-glow-blue p-4 p-lg-5 text-center">
                    <div class="mx-auto" style="max-width: 650px;">
                        <div class="cp-icon-box cp-icon-box-blue mx-auto mb-3">
                            <i class="bi bi-rocket-takeoff-fill"></i>
                        </div>
                        <h3 class="fw-bold mb-3" style="color: var(--cp-text-heading); font-size: 2rem;">{{ $cta['title'] ?? 'Join the Movement Today' }}</h3>
                        <p class="lead mb-4" style="color: var(--cp-text-body); font-size: 1.05rem;">
                            {{ $cta['description'] ?? 'Whether you want to learn, teach, or support our non-profit mission, there is a place for you at Edvora.' }}
                        </p>
                        <a href="{{ $cta['button_url'] ?? route('register') }}" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg"
                           style="background: linear-gradient(135deg, #1F8FFF, #00D2FF); border: none;">
                            <i class="bi bi-person-plus-fill me-2"></i>{{ $cta['button_text'] ?? 'Get Started' }}
                        </a>
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
