@extends('layouts.app')

@section('title', 'Learning Roadmap - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/roadmap.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!-- Header -->
    

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-background"></div>

        <!-- Floating Elements -->
        <div class="floating-element"><i class="fas fa-network-wired"></i></div>
        <div class="floating-element"><i class="fab fa-windows"></i></div>
        <div class="floating-element"><i class="fas fa-keyboard"></i></div>
        <div class="floating-element"><i class="fas fa-code"></i></div>
        <div class="floating-element"><i class="fab fa-linux"></i></div>
        <div class="floating-element"><i class="fas fa-user-secret"></i></div>

        <!-- Particles -->
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>

        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-route me-2" style="color: #00F0FF;"></i>
                Professional Learning Path
            </div>
            <h1 class="hero-title">Computer Science Roadmap</h1>
            <p class="hero-subtitle">From Beginner to Professional in 6 Stages</p>
        </div>
    </section>

    <!-- Roadmap Section -->
    <section class="roadmap-section">
        <div class="roadmap-container">
            <h2 class="roadmap-title">Your Learning Journey</h2>

            <div class="timeline">
                @if($stages->isNotEmpty())
                    @foreach($stages as $stage)
                        <div class="timeline-item">
                            <div class="timeline-node">
                                <i class="{{ $stage->icon }}"></i>
                            </div>
                            <div class="timeline-content">
                                @if($stage->image_url)
                                    <div class="stage-image">
                                        <img src="{{ \Illuminate\Support\Str::startsWith($stage->image_url, ['http://', 'https://', '//']) ? $stage->image_url : asset('storage/' . $stage->image_url) }}" alt="{{ $stage->title }}" class="img-fluid rounded">
                                    </div>
                                @endif
                                <div class="stage-number">{{ $stage->stage_number }}</div>
                                <h3 class="stage-title">{{ $stage->title }}</h3>
                                <p class="stage-description">
                                    {{ $stage->description }}
                                </p>
                                @if(!empty($stage->skills))
                                    <div class="stage-skills">
                                        @foreach($stage->skills as $skillKey => $skillValue)
                                            <span class="skill-tag">{{ $skillValue ?: $skillKey }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                @if($stage->duration)
                                    <div class="stage-duration">
                                        <i class="fas fa-clock me-1"></i>
                                        Duration: {{ $stage->duration }}
                                    </div>
                                @endif
                                <button class="progress-btn mt-3">Start Learning</button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <p class="text-white-50">Roadmap content is being prepared. Please check back soon.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Modern Footer -->
    

    <!-- Bootstrap 5 JS -->
    
    <!-- Custom JS -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/roadmap.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
