@extends('layouts.app')

@section('title', 'How We Work - ' . ($siteSettings->get('company_name', 'Edvora Tech')))

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/about.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/glass-panel.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />

<style>
/* Modern How-We-Work Redesign Styles */
.hww-hero {
    background: radial-gradient(circle at 50% 30%, rgba(31, 143, 255, 0.18) 0%, rgba(13, 27, 62, 0.95) 70%, #060d1f 100%);
    padding: 120px 0 80px;
    position: relative;
    overflow: hidden;
}

.hww-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    border-radius: 50px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    margin-bottom: 24px;
}

.hww-pill-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 36px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.04), 0 5px 15px rgba(31, 143, 255, 0.05);
    border: 1px solid rgba(31, 143, 255, 0.1);
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    position: relative;
    overflow: hidden;
}

.hww-pill-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 25px 50px rgba(31, 143, 255, 0.12);
    border-color: rgba(31, 143, 255, 0.3);
}

.hww-pill-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 6px;
    height: 100%;
    background: linear-gradient(180deg, #1F8FFF, #00D2FF);
    border-radius: 4px 0 0 4px;
}

.hww-icon-wrapper {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: rgba(31, 143, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: #1F8FFF;
    margin-bottom: 20px;
}

.hww-value-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 40px 30px;
    text-align: center;
    border: 1px solid #edf2f7;
    box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    transition: all 0.3s ease;
}

.hww-value-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(31, 143, 255, 0.1);
}

.hww-value-card.blue .val-icon { background: rgba(31, 143, 255, 0.12); color: #1F8FFF; }
.hww-value-card.orange .val-icon { background: rgba(255, 138, 0, 0.12); color: #ff8a00; }
.hww-value-card.purple .val-icon { background: rgba(155, 81, 224, 0.12); color: #9b51e0; }
.hww-value-card.green .val-icon { background: rgba(39, 174, 96, 0.12); color: #27ae60; }

.val-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    margin-bottom: 24px;
    transition: transform 0.3s ease;
}

.hww-value-card:hover .val-icon {
    transform: scale(1.1) rotate(5deg);
}

.bullet-badge {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(31, 143, 255, 0.1);
    color: #1F8FFF;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    flex-shrink: 0;
}
</style>
@endpush

@section('content')
    @php
        $hero = $content['hero'] ?? [];
        $mission = $content['mission'] ?? [];
        $values = $content['values'] ?? [];
        $pillars = $content['pillars'] ?? [];
        $impactSection = $content['impact_section'] ?? [];
        $cta = $content['cta'] ?? [];
    @endphp

    <!-- Modern Hero Section -->
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
                <a href="{{ $hero['cta_url'] ?? route('courses.index') }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-lg">
                    <i class="bi bi-collection-play me-2"></i>{{ $hero['cta_text'] ?? 'Explore Programs' }}
                </a>
                <a href="{{ route('about') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                    <i class="bi bi-info-circle me-2"></i>About Us
                </a>
            </div>
        </x-slot:actionsSlot>

        @if(isset($heroStats) && count($heroStats) > 0)
            <div class="row g-3 justify-content-center pt-3">
                @foreach($heroStats as $stat)
                    <div class="col-md-4 col-sm-6">
                        <div class="hero-stat-pill w-100 justify-content-center">
                            <div class="stat-pill-content text-center">
                                <span class="stat-pill-val">{{ $stat->value }}{{ $stat->suffix }}</span>
                                <span class="stat-pill-lbl">{{ $stat->label }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-page-hero>

    <!-- Mission Statement Section -->
    <section class="py-5" style="background-color: #f8fbff;">
        <div class="container py-4">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-3">Our Purpose</span>
                    <h2 class="fw-bold text-dark mb-4" style="font-size: 2.3rem;">
                        {{ $mission['title'] ?? 'Our Non-Profit Mission' }}
                    </h2>
                    <p class="lead text-muted mx-auto" style="line-height: 1.8;">
                        {{ $mission['description'] ?? 'Edvora is a dedicated non-profit organization committed to empowering young minds through accessible, quality education and innovative technology solutions.' }}
                    </p>
                </div>
            </div>

            <!-- Core Values Grid -->
            @if(!empty($values))
            <div class="row g-4 mt-4">
                @foreach($values as $val)
                <div class="col-lg-4 col-md-6">
                    <div class="hww-value-card {{ $val['color'] ?? 'blue' }} h-100">
                        <div class="val-icon">
                            <i class="bi {{ $val['icon'] ?? 'bi-people-fill' }}"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-3">{{ $val['title'] ?? '' }}</h4>
                        <p class="text-muted mb-0" style="line-height: 1.7;">
                            {{ $val['description'] ?? '' }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <!-- Operational Pillars (How We Operate) -->
    @if(!empty($pillars))
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">Operations</span>
                <h2 class="fw-bold text-dark display-6">How We Operate</h2>
                <p class="text-muted">A look inside our transparent, community-driven workflow.</p>
            </div>

            <div class="row g-4">
                @foreach($pillars as $idx => $pillar)
                <div class="col-lg-4 col-md-6">
                    <div class="hww-pill-card h-100">
                        <div class="hww-icon-wrapper">
                            <i class="bi {{ $pillar['icon'] ?? 'bi-gear-fill' }}"></i>
                        </div>
                        <span class="badge bg-light text-primary fw-semibold mb-2">Pillar 0{{ $idx + 1 }}</span>
                        <h4 class="fw-bold text-dark mb-1">{{ $pillar['title'] ?? '' }}</h4>
                        @if(!empty($pillar['subtitle']))
                        <p class="text-primary small fw-semibold mb-3">{{ $pillar['subtitle'] }}</p>
                        @endif
                        <p class="text-muted small mb-4" style="line-height: 1.6;">
                            {{ $pillar['description'] ?? '' }}
                        </p>

                        @if(!empty($pillar['bullets']))
                        <div class="space-y-3 pt-2 border-top">
                            @foreach($pillar['bullets'] as $b)
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <div class="bullet-badge mt-1"><i class="bi bi-check2"></i></div>
                                <div>
                                    <strong class="d-block text-dark small">{{ $b['title'] ?? '' }}</strong>
                                    <span class="text-muted" style="font-size: 0.82rem;">{{ $b['desc'] ?? '' }}</span>
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

    <!-- Impact Section -->
    @if(isset($impactStats) && count($impactStats) > 0)
    <section class="py-5" style="background: linear-gradient(135deg, #091a3e 0%, #1F8FFF 100%); color: #fff;">
        <div class="container py-4">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <h2 class="fw-bold text-white mb-2">{{ $impactSection['title'] ?? 'Our Global Impact' }}</h2>
                    <p class="text-white-50 lead">{{ $impactSection['subtitle'] ?? "Together, we're making a difference in young lives across the globe" }}</p>
                </div>
            </div>

            <div class="row g-4 justify-content-center text-center">
                @foreach($impactStats as $stat)
                <div class="col-lg-3 col-6">
                    <div class="p-4 rounded-4" style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.15);">
                        <div class="display-5 fw-bold text-warning mb-1">{{ $stat->value }}{{ $stat->suffix }}</div>
                        <div class="text-white-50 fw-semibold">{{ $stat->label }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 text-center" style="background: #ffffff; border: 1px solid #edf2f7;">
                <div class="mx-auto" style="max-width: 600px;">
                    <div class="mb-3">
                        <i class="bi bi-rocket-takeoff-fill text-primary display-4"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-3">{{ $cta['title'] ?? 'Join the Movement Today' }}</h3>
                    <p class="text-muted mb-4 lead" style="font-size: 1.05rem;">
                        {{ $cta['description'] ?? 'Whether you want to learn, teach, or support our non-profit mission, there is a place for you at Edvora.' }}
                    </p>
                    <a href="{{ $cta['button_url'] ?? route('register') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow">
                        <i class="bi bi-person-plus-fill me-2"></i>{{ $cta['button_text'] ?? 'Get Started' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
