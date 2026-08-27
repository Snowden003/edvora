@extends('layouts.app')

@section('title', 'Terms of Service - ' . ($siteSettings->get('company_name', 'Edvora Tech')))

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/terms.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
    @php
        $hero = $content['hero'] ?? [];
        $modules = $content['modules'] ?? [];
        $support = $content['support'] ?? [];
    @endphp

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
                            <i class="bi bi-shield-lock-fill me-2"></i>{{ $hero['badge'] ?? 'Edvora Legal Framework' }}
                        </span>
                        <h1 class="display-2 fw-bold text-white mb-4" style="letter-spacing: -2px;">
                            {{ $hero['title'] ?? 'Terms of Service' }}
                        </h1>
                        <p class="lead text-white-50 mx-auto mb-4" style="max-width: 700px; font-weight: 300;">
                            {{ $hero['subtitle'] ?? "Our commitment to your privacy, security, and elite learning experience. We've refined our terms to be as transparent as our platform." }}
                        </p>
                        <div class="d-flex justify-content-center gap-5 text-white-50 small">
                            <span class="d-flex align-items-center gap-2">
                                <i class="bi bi-calendar3 text-primary"></i> Last Updated: {{ $hero['last_updated'] ?? 'Dec 2024' }}
                            </span>
                            <span class="d-flex align-items-center gap-2">
                                <i class="bi bi-patch-check text-info"></i> {{ $hero['version'] ?? 'Version 2.4.0' }}
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
                @foreach($modules as $mod)
                    @php
                        $sizeClass = match($mod['size'] ?? 'normal') {
                            'wide' => 'bento-wide',
                            'large' => 'bento-large',
                            'tall' => 'bento-tall',
                            'dark' => 'bento-dark',
                            default => '',
                        };

                        $colorClass = match($mod['color'] ?? 'blue') {
                            'teal' => 'line-teal',
                            'violet' => 'line-violet',
                            'amber' => 'line-amber',
                            'dark' => 'line-dark',
                            default => 'line-blue',
                        };

                        $iconBgClass = match($mod['color'] ?? 'blue') {
                            'teal' => 'bg-teal-glass',
                            'violet' => 'bg-violet-glass',
                            'amber' => 'bg-amber-glass',
                            default => 'bg-blue-glass',
                        };
                    @endphp

                    <div class="bento-card card {{ $sizeClass }}" data-aos="fade-up">
                        @if(($mod['size'] ?? '') !== 'dark')
                            <div class="accent-line {{ $colorClass }}"></div>
                            <div class="module-icon {{ $iconBgClass }}">
                                <i class="bi {{ $mod['icon'] ?? 'bi-file-text' }}"></i>
                            </div>
                        @else
                            <span class="bento-icon-main text-white"><i class="bi {{ $mod['icon'] ?? 'bi-shield-shaded' }}"></i></span>
                        @endif

                        <h3 class="module-title {{ ($mod['size'] ?? '') === 'dark' ? 'text-white' : '' }}">
                            {{ $mod['number'] ?? '' }}. {{ $mod['title'] ?? '' }}
                        </h3>

                        <p class="module-text {{ ($mod['size'] ?? '') === 'dark' ? 'text-white opacity-75' : '' }}">
                            {{ $mod['text'] ?? '' }}
                        </p>

                        @if(!empty($mod['tldr']))
                        <div class="plain-english-min {{ ($mod['size'] ?? '') === 'dark' ? 'border-white border-opacity-10' : '' }}">
                            <span class="tldr-pill {{ ($mod['size'] ?? '') === 'dark' ? 'bg-white text-dark' : '' }}">TL;DR</span>
                            <span class="tldr-text {{ ($mod['size'] ?? '') === 'dark' ? 'text-white opacity-50' : '' }}">{{ $mod['tldr'] }}</span>
                        </div>
                        @endif
                    </div>
                @endforeach

                <!-- Support Box -->
                @if(!empty($support))
                <div class="bento-card card bento-wide" data-aos="fade-up">
                    <div class="accent-line line-blue"></div>
                    <div class="module-icon bg-blue-glass"><i class="bi bi-headset"></i></div>
                    <h3 class="module-title">{{ $support['title'] ?? 'Support & Inquiries' }}</h3>
                    <p class="small text-muted mt-2 mb-0">{{ $support['description'] ?? 'Use the contact form for legal and general inquiries.' }}</p>
                    <a href="{{ $support['link_url'] ?? route('contact') }}" class="text-primary text-decoration-none fw-bold">{{ $support['link_text'] ?? 'Open contact form' }}</a>
                </div>
                @endif
            </div>

            <!-- Visual Separator -->
            <div class="py-5">
                <div class="mx-auto"
                    style="width: 50px; height: 3px; background: linear-gradient(90deg, transparent, var(--primary-blue), transparent);">
                </div>
            </div>

            <div class="text-center text-muted small px-4">
                <p class="mb-0">For legal inquiries, use the <a href="{{ route('contact') }}"
                        class="text-primary text-decoration-none fw-bold">{{ $siteSettings->get('company_name', 'Edvora') }} contact form</a>.</p>
                <p>&copy; {{ date('Y') }} {{ $siteSettings->get('footer_copyright', 'Edvora Tech. All rights reserved.') }}</p>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
