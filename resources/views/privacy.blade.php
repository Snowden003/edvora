@extends('layouts.app')

@section('title', 'Privacy Policy - ' . ($siteSettings->get('company_name', 'Edvora Tech')))

@push('styles')
<link href="{{ asset('assets/css/company-pages.css') }}?v={{ file_exists(public_path('assets/css/company-pages.css')) ? filemtime(public_path('assets/css/company-pages.css')) : time() }}" rel="stylesheet" />
@endpush

@section('content')
    <x-company-preloader />

    @php
        $hero = $content['hero'] ?? [];
        $panes = $content['panes'] ?? [];
    @endphp

    <div class="cp-page-body">
        <!-- Prism Navigation Dots (Desktop) -->
        <div class="cp-prism-nav d-none d-lg-flex">
            <a href="#hero" class="cp-prism-dot active" title="Top" aria-label="Top"></a>
            @foreach($panes as $idx => $pane)
                <a href="#pane-{{ $pane['index'] ?? $idx }}" class="cp-prism-dot" title="{{ $pane['title'] ?? 'Section ' . ($idx+1) }}" aria-label="{{ $pane['title'] ?? 'Section ' . ($idx+1) }}"></a>
            @endforeach
        </div>

        <canvas id="cp3dCanvas" class="cp-3d-canvas"></canvas>

        <!-- Hero (keeps own dark styling) -->
        <div id="hero">
            <x-page-hero
                layout="centered"
                badgeIcon="bi bi-shield-check"
                badgeText="{{ $hero['tag'] ?? 'Privacy Standards // 2024' }}"
                titlePrefix="{{ $hero['heading_1'] ?? 'Safeguarding' }}"
                highlight="{{ $hero['heading_2'] ?? 'Digital Experience' }}"
                subtitle="{{ $hero['subtitle'] ?? 'Trust is the core of our educational architecture. We protect your data with multi-layered, zero-compromise security protocols.' }}"
                :floatingIcons="['bi bi-fingerprint', 'bi bi-shield-shaded', 'bi bi-lock-fill', 'bi bi-key', 'bi bi-eye-slash']"
            >
                <x-slot:actionsSlot>
                    <div class="d-flex justify-content-center gap-3 flex-wrap pt-2">
                        <span class="cp-badge" style="background: rgba(0,210,255,0.15); color: #38bdf8; border: 1px solid rgba(0,210,255,0.3);"><i class="bi bi-shield-lock-fill"></i> AES-256</span>
                        <span class="cp-badge" style="background: rgba(31,143,255,0.15); color: #60a5fa; border: 1px solid rgba(31,143,255,0.3);"><i class="bi bi-patch-check-fill"></i> TLS 1.3</span>
                        <span class="cp-badge" style="background: rgba(139,92,246,0.15); color: #c084fc; border: 1px solid rgba(139,92,246,0.3);"><i class="bi bi-person-check-fill"></i> GDPR</span>
                    </div>
                </x-slot:actionsSlot>
            </x-page-hero>
        </div>

        <!-- Privacy Panes -->
        <section class="cp-section">
            <div class="container position-relative" style="z-index: 2; max-width: 960px;">
                <div class="d-flex flex-column gap-4">
                    @foreach($panes as $idx => $pane)
                        @php
                            $glowClass = ($idx % 2 === 0) ? 'cp-glass-card-glow-blue' : 'cp-glass-card-glow-cyan';
                            $iconBoxClass = ($idx % 2 === 0) ? 'cp-icon-box-blue' : 'cp-icon-box-cyan';
                        @endphp
                        <div class="cp-glass-card {{ $glowClass }} cp-prism-pane p-4 p-md-5" id="pane-{{ $pane['index'] ?? $idx }}">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="cp-badge cp-badge-cyan mb-0">Protocol {{ $pane['index'] ?? sprintf('%02d', $idx + 1) }}</span>
                                <div class="cp-icon-box {{ $iconBoxClass }} mb-0" style="width: 44px; height: 44px; font-size: 1.15rem;">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                            </div>

                            <h2 class="fw-bold mb-3" style="color: var(--cp-text-heading); font-size: 1.65rem;">
                                {{ $pane['title'] ?? '' }}
                            </h2>

                            <p class="mb-4" style="color: var(--cp-text-body); line-height: 1.85; font-size: 1.05rem;">
                                {{ $pane['text'] ?? '' }}
                            </p>

                            @if(!empty($pane['tags']))
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach($pane['tags'] as $tag)
                                    <span class="cp-tag-pill">
                                        <i class="bi bi-tag-fill me-1" style="color: var(--cp-primary); opacity: 0.6;"></i>{{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                            @endif

                            @if(!empty($pane['sub_note']))
                            <div class="pt-3 mt-3" style="border-top: 1px solid rgba(0,0,0,0.06);">
                                <p class="small mb-0 d-flex align-items-center gap-2" style="color: var(--cp-primary);">
                                    <i class="bi bi-check-circle-fill"></i>{{ $pane['sub_note'] }}
                                </p>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Data Sovereignty Box -->
                <div class="mt-5">
                    <div class="cp-glass-card cp-glass-card-glow-purple p-4 p-lg-5 text-center">
                        <div class="cp-icon-box cp-icon-box-purple mx-auto mb-3">
                            <i class="bi bi-person-gear"></i>
                        </div>
                        <h3 class="fw-bold mb-2" style="color: var(--cp-text-heading);">Your Data, Your Sovereignty</h3>
                        <p class="mb-4 mx-auto" style="color: var(--cp-text-body); max-width: 600px; line-height: 1.7;">
                            Under our platform architecture, you retain absolute ownership of your educational profile, progress history, and personal details. You can request complete data export or account removal anytime.
                        </p>
                        <a href="{{ route('contact') }}" class="btn btn-primary rounded-pill px-5 py-2 fw-bold"
                           style="background: linear-gradient(135deg, #8B5CF6, #1F8FFF); border: none;">
                            <i class="bi bi-envelope-fill me-2"></i>Contact Privacy Officer
                        </a>
                    </div>
                </div>

                <div class="text-center small pt-5" style="color: var(--cp-text-muted);">
                    <p class="mb-1">Committed to protecting privacy across the global educational ecosystem.</p>
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
