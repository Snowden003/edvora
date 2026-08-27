@extends('layouts.app')

@section('title', 'Our Story - ' . ($siteSettings->get('company_name', 'Edvora Tech')))

@push('styles')
<link href="{{ asset('assets/css/story.css') }}" rel="stylesheet" />
@endpush

@section('content')
    @php
        $hero = $content['hero'] ?? [];
        $genesis = $content['genesis'] ?? [];
        $vision = $content['vision'] ?? [];
        $teamStory = $content['team_story'] ?? [];
        $missionToday = $content['mission_today'] ?? [];
    @endphp

    <!-- Hero Section -->
    <section class="story-hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 text-center">
                    <div class="story-badge">
                        <i class="bi bi-gem"></i>
                        <span>{{ $hero['badge'] ?? 'Our Journey' }}</span>
                    </div>

                    <h1 class="story-title">
                        <span>{{ $hero['title_prefix'] ?? 'The Story Behind' }}</span>
                        <br>
                        <span class="highlight">{{ $hero['highlight'] ?? ($siteSettings->get('company_name', 'Edvora')) }}</span>
                    </h1>

                    <p class="story-subtitle">
                        {{ $hero['subtitle'] ?? 'From a revolutionary idea to transforming education through technology' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Content -->
    <section class="py-5 story-content-section">
        <div class="container">
            <!-- The Beginning -->
            <div class="row mb-5">
                <div class="col-lg-6 anim-slide-in-left">
                    <div class="story-card">
                        <div class="story-icon">
                            <i class="bi {{ $genesis['icon'] ?? 'bi-lightbulb' }}"></i>
                        </div>
                        <h3 class="text-brand-blue fw-bold mb-3">{{ $genesis['title'] ?? 'The Genesis' }}</h3>
                        <p class="para-lg">
                            {{ $genesis['text'] ?? 'Our story begins with a brilliant idea that emerged from the intersection of technology and necessity. Conceived with the dream of revolutionizing education through innovative technology, Edvora was built to empower learners everywhere.' }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 anim-slide-in-right anim-both anim-delay-03">
                    <div class="story-visual visual-alt-1">
                        <div class="visual-elements">
                            <div class="bubble white float1" style="top: 20%; left: 20%; width: 60px; height: 60px;"></div>
                            <div class="bubble orange float2" style="bottom: 30%; right: 25%; width: 40px; height: 40px;"></div>
                            <div class="bubble deep-orange float3" style="top: 50%; right: 15%; width: 50px; height: 50px;"></div>
                        </div>
                        <i class="bi bi-rocket-takeoff-fill z-top" style="font-size: 6rem;"></i>
                    </div>
                </div>
            </div>

            <!-- The Vision -->
            <div class="row mb-5">
                <div class="col-lg-6 order-lg-2 anim-slide-in-right">
                    <div class="story-card alt-1">
                        <div class="story-icon orange">
                            <i class="bi {{ $vision['icon'] ?? 'bi-eye' }}"></i>
                        </div>
                        <h3 class="text-brand-blue fw-bold mb-3">{{ $vision['title'] ?? 'The Vision' }}</h3>
                        <p class="para-lg">
                            {{ $vision['text'] ?? "We recognized technology's power to elevate society's knowledge. Our mission became clear: create a platform that democratizes quality education and empowers every individual to reach their full potential through innovative learning experiences." }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1 anim-slide-in-left anim-both anim-delay-03">
                    <div class="story-visual visual-alt-1">
                        <div class="visual-elements">
                            <div class="bubble white float2" style="top: 15%; right: 20%; width: 70px; height: 70px;"></div>
                            <div class="bubble" style="bottom: 20%; left: 15%; width: 45px; height: 45px; background: rgba(31, 143, 255, 0.1);"></div>
                            <div class="bubble light float3" style="top: 40%; left: 10%; width: 55px; height: 55px;"></div>
                        </div>
                        <i class="bi bi-eye-fill z-top" style="font-size: 6rem;"></i>
                    </div>
                </div>
            </div>

            <!-- The Edvora Team Story -->
            <div class="row mb-5">
                <div class="col-lg-6 anim-slide-in-left">
                    <div class="story-visual visual-alt-1">
                        <div class="visual-elements">
                            <div class="bubble orange float1" style="top: 10%; left: 30%; width: 80px; height: 80px;"></div>
                            <div class="bubble white float3" style="bottom: 20%; right: 10%; width: 60px; height: 60px;"></div>
                        </div>
                        <i class="bi bi-diagram-3-fill z-top" style="font-size: 6rem;"></i>
                    </div>
                </div>
                <div class="col-lg-6 anim-slide-in-right anim-both anim-delay-03">
                    <div class="story-card alt-2">
                        <div class="story-icon">
                            <i class="bi {{ $teamStory['icon'] ?? 'bi-people-fill' }}"></i>
                        </div>
                        <h3 class="text-brand-blue fw-bold mb-3">{{ $teamStory['title'] ?? 'The Edvora Team' }}</h3>
                        <p class="para-lg">
                            {{ $teamStory['paragraph_1'] ?? 'Our passionate group of innovators, educators, and software engineers work around the clock to create an unmatched educational experience.' }}
                        </p>
                        @if(!empty($teamStory['paragraph_2']))
                        <p class="para-lg">
                            {{ $teamStory['paragraph_2'] }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- The Mission -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="mission-card text-center">
                        <div class="mission-icon">
                            <i class="bi {{ $missionToday['icon'] ?? 'bi-mortarboard' }}"></i>
                        </div>
                        <h2 class="mission-title">{{ $missionToday['title'] ?? 'Our Mission Today' }}</h2>
                        <p class="mission-text">
                            {{ $missionToday['text'] ?? "Dedicated to advancing society's knowledge through cutting-edge educational technology. We believe that by providing accessible, high-quality learning experiences, we can empower individuals and communities to thrive in an increasingly digital world." }}
                        </p>
                        @if(!empty($missionToday['stats']))
                        <div class="mission-stats">
                            @foreach($missionToday['stats'] as $stat)
                            <div class="stat-item">
                                <div class="value text-brand-blue">{{ $stat['value'] ?? '' }}</div>
                                <div class="label">{{ $stat['label'] ?? '' }}</div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
