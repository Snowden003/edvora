@extends('layouts.app')

@section('title', 'Privacy Policy - Edvora Tech | Elite Design')

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
<div class="cyber-fog-bg"></div>

    <!-- Header -->
    

    <!-- Side Nav Dots -->
    <div class="prism-side-nav">
        <a href="#hero" class="nav-dot active" data-label="Top"></a>
        <a href="#collection" class="nav-dot" data-label="Collection"></a>
        <a href="#usage" class="nav-dot" data-label="Usage"></a>
        <a href="#security" class="nav-dot" data-label="Security"></a>
        <a href="#rights" class="nav-dot" data-label="Rights"></a>
    </div>

    <!-- Prism Hero -->
    <section class="privacy-hero-prism" id="hero">
        <div class="hero-visual-prism">
            <div class="prism-element"></div>
        </div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 prism-title-area">
                    <span class="hero-tag">Privacy Standards // 2024</span>
                    <h1 class="prism-heading">
                        Safeguarding <span>Digital</span>
                        <span>Experience</span>
                    </h1>
                    <p class="lead opacity-75 mb-5" style="max-width: 500px;">
                        Trust is the core of our educational architecture. We protect your data with
                        multi-layered glassmorphism inspired security protocols.
                    </p>
                    <div class="d-flex gap-3">
                        <button class="btn btn-primary btn-lg rounded-pill px-5"
                            onclick="location.href='#collection'">View Protocol</button>
                        <button class="btn btn-outline-light btn-lg rounded-pill px-5">Download PDF</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Asymmetric Content Container -->
    <div class="prism-container">

        <!-- Pane 1: Collection -->
        <div class="prism-pane offset-left" id="collection">
            <span class="pane-index">01</span>
            <h2 class="pane-title">Immersive Data Collection</h2>
            <p class="pane-text">
                When you interact with the Edvora ecosystem, we collect fragments of digital presence to curate
                a personalized learning path. This includes session telemetry and academic goal-setting data
                designed to optimize your performance.
            </p>
            <div class="highlight-list">
                <span class="highlight-tag">Interaction Metrics</span>
                <span class="highlight-tag">Bio Preferences</span>
                <span class="highlight-tag">Session Heatmaps</span>
            </div>
        </div>

        <!-- Pane 2: Usage -->
        <div class="prism-pane offset-right" id="usage">
            <span class="pane-index">02</span>
            <h2 class="pane-title">Refined Information Usage</h2>
            <p class="pane-text">
                Your data isn't just stored; it's utilized to refine our neural learning models. We use
                predictive analytics to suggest the best courses, making your educational journey as
                fluid and responsive as our interface design.
            </p>
            <div class="mt-4 pt-3 border-top border-light opacity-10">
                <p class="small text-white-50"><i class="bi bi-shield-check me-2"></i>No data is sold to external
                    entities. Ever.</p>
            </div>
        </div>

        <!-- Pane 3: Security (Visual Feature) -->
        <div class="prism-pane offset-left" id="security" style="border-left: 5px solid var(--cyber-blue);">
            <span class="pane-index">03</span>
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h2 class="pane-title">Military-Grade Encryption</h2>
                    <p class="pane-text">
                        Every datum is shielded behind AES-256 bit encryption, wrapped in TLS 1.3
                        transport protocols. Our architecture is designed to be impenetrable,
                        ensuring your academic assets remain locked within the Edvora vault.
                    </p>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <i class="bi bi-layers-half display-1 text-primary" style="opacity: 0.2; filter: blur(2px);"></i>
                </div>
            </div>
        </div>

        <!-- Pane 4: Rights -->
        <div class="prism-pane offset-right" id="rights">
            <span class="pane-index">04</span>
            <h2 class="pane-title">The Sovereignty of User Rights</h2>
            <p class="pane-text">
                You are the master of your information. We provide intuitive controls to export,
                anonymize, or delete your entire history with a single interaction. Your rights
                are fundamental, not optional.
            </p>
            <div class="highlight-list">
                <span class="highlight-tag">GDPR Ready</span>
                <span class="highlight-tag">Full Erasure</span>
                <span class="highlight-tag">Portability</span>
            </div>
        </div>

    </div>

    <!-- Modern Footer -->
    

    <!-- Scripts -->
    
    <!-- Custom JS -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
<script src="{{ asset('assets/js/pages/privacy.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
