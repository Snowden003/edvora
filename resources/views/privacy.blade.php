@extends('layouts.app')

@section('title', 'Privacy Policy - ' . ($siteSettings->get('company_name', 'Edvora Tech')))

@section('body-class', 'privacy-page')

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/privacy.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
    @php
        $hero = $content['hero'] ?? [];
        $panes = $content['panes'] ?? [];
    @endphp

    <div class="cyber-fog-bg"></div>

    <!-- Side Nav Dots -->
    <div class="prism-side-nav">
        <a href="#hero" class="nav-dot active" data-label="Top"></a>
        @foreach($panes as $idx => $pane)
            <a href="#pane-{{ $pane['index'] ?? $idx }}" class="nav-dot" data-label="{{ $pane['title'] ?? 'Section ' . ($idx+1) }}"></a>
        @endforeach
    </div>

    <!-- Prism Hero -->
    <section class="privacy-hero-prism" id="hero">
        <div class="hero-visual-prism">
            <div class="prism-element"></div>
        </div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 prism-title-area">
                    <span class="hero-tag">{{ $hero['tag'] ?? 'Privacy Standards // 2024' }}</span>
                    <h1 class="prism-heading">
                        {{ $hero['heading_1'] ?? 'Safeguarding' }} <span>{{ $hero['heading_2'] ?? 'Digital Experience' }}</span>
                    </h1>
                    <p class="lead opacity-75 mb-5" style="max-width: 500px;">
                        {{ $hero['subtitle'] ?? 'Trust is the core of our educational architecture. We protect your data with multi-layered glassmorphism inspired security protocols.' }}
                    </p>
                    <div class="d-flex gap-3">
                        <button class="btn btn-primary btn-lg rounded-pill px-5"
                            onclick="location.href='#pane-01'">{{ $hero['protocol_button_text'] ?? 'View Protocol' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Asymmetric Content Container -->
    <div class="prism-container">
        @foreach($panes as $pane)
            @php
                $alignClass = ($pane['alignment'] ?? 'left') === 'right' ? 'offset-right' : 'offset-left';
            @endphp
            <div class="prism-pane {{ $alignClass }}" id="pane-{{ $pane['index'] ?? '' }}">
                <span class="pane-index">{{ $pane['index'] ?? '' }}</span>
                <h2 class="pane-title">{{ $pane['title'] ?? '' }}</h2>
                <p class="pane-text">
                    {{ $pane['text'] ?? '' }}
                </p>

                @if(!empty($pane['tags']))
                <div class="highlight-list">
                    @foreach($pane['tags'] as $t)
                        <span class="highlight-tag">{{ $t }}</span>
                    @endforeach
                </div>
                @endif

                @if(!empty($pane['sub_note']))
                <div class="mt-4 pt-3 border-top border-light opacity-10">
                    <p class="small text-white-50"><i class="bi bi-shield-check me-2"></i>{{ $pane['sub_note'] }}</p>
                </div>
                @endif
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
<script src="{{ asset('assets/js/pages/privacy.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
