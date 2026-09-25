@extends('layouts.app')

@section('title', 'Terms of Service - ' . ($siteSettings->get('company_name', 'Edvora Tech')))

@push('styles')
<link href="{{ asset('assets/css/company-pages.css') }}?v={{ file_exists(public_path('assets/css/company-pages.css')) ? filemtime(public_path('assets/css/company-pages.css')) : time() }}" rel="stylesheet" />
@endpush

@section('content')
    <x-company-preloader />

    @php
        $hero = $content['hero'] ?? [];
        $modules = $content['modules'] ?? [];
        $support = $content['support'] ?? [];
    @endphp

    <div class="cp-page-body">
        <canvas id="cp3dCanvas" class="cp-3d-canvas"></canvas>

        <!-- Hero (keeps own dark styling) -->
        <x-page-hero
            layout="centered"
            badgeIcon="bi bi-shield-lock-fill"
            badgeText="{{ $hero['badge'] ?? 'Edvora Legal Framework' }}"
            titlePrefix=""
            highlight="{{ $hero['title'] ?? 'Terms of Service' }}"
            subtitle="{{ $hero['subtitle'] ?? 'Our commitment to your privacy, security, and an elite learning experience. Transparent and clearly defined.' }}"
            :floatingIcons="['bi bi-shield-check', 'bi bi-file-earmark-text', 'bi bi-lock', 'bi bi-fingerprint', 'bi bi-patch-check']"
        >
            <x-slot:actionsSlot>
                <div class="d-flex justify-content-center gap-4 flex-wrap text-white-50 small pt-2">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar3 text-primary"></i> Last Updated: {{ $hero['last_updated'] ?? 'Dec 2024' }}
                    </span>
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-patch-check-fill text-info"></i> {{ $hero['version'] ?? 'Version 2.4.0' }}
                    </span>
                </div>
            </x-slot:actionsSlot>
        </x-page-hero>

        <!-- Terms Content -->
        <section class="cp-section">
            <div class="container position-relative" style="z-index: 2;">

                <!-- Search -->
                <div class="cp-legal-search-wrapper">
                    <i class="bi bi-search cp-legal-search-icon"></i>
                    <input type="text" id="legalSearchInput" class="cp-legal-search-input" placeholder="Search terms, clauses, topics (e.g. account, privacy, rules)..." autocomplete="off" />
                </div>

                <!-- Quick Jump TOC -->
                <div class="cp-toc-bar justify-content-md-center">
                    @foreach($modules as $mod)
                        <a href="#term-{{ $mod['number'] ?? '' }}" class="cp-toc-pill">
                            {{ $mod['number'] ?? '' }}. {{ $mod['title'] ?? '' }}
                        </a>
                    @endforeach
                </div>

                <!-- Bento Legal Grid -->
                <div class="cp-bento-grid">
                    @foreach($modules as $mod)
                        @php
                            $color = $mod['color'] ?? 'blue';
                            $glowClass = match($color) {
                                'teal', 'cyan' => 'cp-glass-card-glow-cyan',
                                'violet', 'purple' => 'cp-glass-card-glow-purple',
                                'amber', 'gold' => 'cp-glass-card-glow-gold',
                                default => 'cp-glass-card-glow-blue',
                            };
                            $iconBoxClass = match($color) {
                                'teal', 'cyan' => 'cp-icon-box-cyan',
                                'violet', 'purple' => 'cp-icon-box-purple',
                                'amber', 'gold' => 'cp-icon-box-gold',
                                default => 'cp-icon-box-blue',
                            };
                            $badgeClass = match($color) {
                                'teal', 'cyan' => 'cp-badge-cyan',
                                'violet', 'purple' => 'cp-badge-purple',
                                'amber', 'gold' => 'cp-badge-gold',
                                default => 'cp-badge-blue',
                            };
                        @endphp

                        <div class="cp-glass-card {{ $glowClass }} cp-legal-module-card d-flex flex-column" id="term-{{ $mod['number'] ?? '' }}">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="cp-icon-box {{ $iconBoxClass }} mb-0" style="width: 50px; height: 50px; font-size: 1.3rem;">
                                    <i class="bi {{ $mod['icon'] ?? 'bi-file-text' }}"></i>
                                </div>
                                <span class="cp-badge {{ $badgeClass }} mb-0">Clause {{ $mod['number'] ?? '' }}</span>
                            </div>

                            <h3 class="fw-bold mb-3 module-title" style="color: var(--cp-text-heading); font-size: 1.3rem;">
                                {{ $mod['number'] ?? '' }}. {{ $mod['title'] ?? '' }}
                            </h3>

                            <p class="small flex-grow-1 module-text" style="color: var(--cp-text-body); line-height: 1.7; font-size: 0.95rem;">
                                {{ $mod['text'] ?? '' }}
                            </p>

                            @if(!empty($mod['tldr']))
                            <div class="cp-tldr-box">
                                <span class="cp-tldr-badge">TL;DR</span>
                                <span class="small tldr-text" style="color: var(--cp-primary); font-size: 0.85rem; line-height: 1.5;">{{ $mod['tldr'] }}</span>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Support Box -->
                @if(!empty($support))
                <div class="mt-5">
                    <div class="cp-glass-card cp-glass-card-glow-blue p-4 p-lg-5 text-center">
                        <div class="cp-icon-box cp-icon-box-blue mx-auto mb-3">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h3 class="fw-bold mb-2" style="color: var(--cp-text-heading);">{{ $support['title'] ?? 'Support & Inquiries' }}</h3>
                        <p class="mb-4 mx-auto" style="color: var(--cp-text-body); max-width: 550px;">
                            {{ $support['description'] ?? 'Have legal questions or need assistance regarding our terms? Our team is always here to help.' }}
                        </p>
                        <a href="{{ $support['link_url'] ?? route('contact') }}" class="btn btn-primary rounded-pill px-5 py-2 fw-bold"
                           style="background: linear-gradient(135deg, #1F8FFF, #00D2FF); border: none;">
                            {{ $support['link_text'] ?? 'Open Contact Form' }}
                        </a>
                    </div>
                </div>
                @endif

                <div class="text-center small pt-5" style="color: var(--cp-text-muted);">
                    <p class="mb-1">For legal inquiries, use the <a href="{{ route('contact') }}" class="fw-semibold text-decoration-none" style="color: var(--cp-primary);">{{ $siteSettings->get('company_name', 'Edvora') }} Contact Center</a>.</p>
                    <p class="opacity-75">&copy; {{ date('Y') }} {{ $siteSettings->get('footer_copyright', 'Edvora Tech. All rights reserved.') }}</p>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/company-bg-3d.js') }}?v={{ file_exists(public_path('assets/js/company-bg-3d.js')) ? filemtime(public_path('assets/js/company-bg-3d.js')) : time() }}" defer></script>
<script src="{{ asset('assets/js/company-pages.js') }}?v={{ file_exists(public_path('assets/js/company-pages.js')) ? filemtime(public_path('assets/js/company-pages.js')) : time() }}" defer></script>
@endpush
