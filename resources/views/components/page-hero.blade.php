@props([
    'layout' => 'centered', // 'centered' or 'split'
    'badgeIcon' => null,
    'badgeText' => null,
    'badgeClass' => '',
    'title' => null,
    'titlePrefix' => null,
    'highlight' => null,
    'highlightClass' => '',
    'titleSuffix' => null,
    'typewriter' => false,
    'subtitle' => null,
    'stats' => [],
    'image' => null,
    'imageAlt' => 'Edvora Tech',
    'showParticles' => true,
    'showFloatingIcons' => true,
    'floatingIcons' => [
        'bi bi-book',
        'bi bi-lightbulb',
        'bi bi-mortarboard',
        'bi bi-trophy',
        'bi bi-star',
    ],
    'class' => '',
])

<section class="page-hero-wrapper page-hero-{{ $layout }} {{ $class }}">
    <!-- Background Animated Mesh, Particles & Glow Blobs -->
    <div class="page-hero-bg">
        <div class="hero-glow-blob hero-glow-blob-1"></div>
        <div class="hero-glow-blob hero-glow-blob-2"></div>
        <div class="hero-glow-blob hero-glow-blob-3"></div>
        <div class="hero-grid-overlay"></div>

        @if($showFloatingIcons)
            @foreach($floatingIcons as $idx => $fIcon)
                <div class="hero-floating-icon icon-pos-{{ ($idx % 5) + 1 }}">
                    <i class="{{ $fIcon }}"></i>
                </div>
            @endforeach
        @endif

        @if($showParticles)
            <div class="hero-particle p1"></div>
            <div class="hero-particle p2"></div>
            <div class="hero-particle p3"></div>
            <div class="hero-particle p4"></div>
            <div class="hero-particle p5"></div>
        @endif
    </div>

    <!-- Main Container -->
    <div class="container page-hero-container position-relative">
        @if($layout === 'split')
            <div class="row align-items-center g-4 g-lg-5">
                <!-- Left Column (Content) -->
                <div class="col-lg-7 col-xl-7 text-start">
                    @if($badgeText)
                        <div class="hero-cyber-badge {{ $badgeClass }}">
                            @if($badgeIcon)
                                <span class="badge-icon"><i class="{{ $badgeIcon }}"></i></span>
                            @endif
                            <span>{{ $badgeText }}</span>
                        </div>
                    @endif

                    <h1 class="hero-main-title">
                        @if($typewriter)
                            <span class="typewriter-text" data-text="{{ $title ?? ($titlePrefix . ' ' . $highlight . ' ' . $titleSuffix) }}"></span>
                        @elseif(isset($titleSlot))
                            {{ $titleSlot }}
                        @elseif($titlePrefix || $highlight || $titleSuffix)
                            {{ $titlePrefix }}
                            @if($highlight)
                                <span class="highlight {{ $highlightClass }}">{{ $highlight }}</span>
                            @endif
                            {{ $titleSuffix }}
                        @elseif($title)
                            {!! html_entity_decode($title, ENT_QUOTES, 'UTF-8') !!}
                        @endif
                    </h1>

                    @if($subtitle)
                        <p class="hero-main-subtitle">
                            @if($typewriter)
                                <span class="typewriter-text" data-text="{{ $subtitle }}"></span>
                            @else
                                {{ $subtitle }}
                            @endif
                        </p>
                    @endif

                    @if(!empty($stats))
                        <div class="hero-stats-row mb-4">
                            @foreach($stats as $st)
                                <div class="hero-stat-pill">
                                    @if(isset($st['icon']))
                                        <span class="stat-pill-icon"><i class="{{ $st['icon'] }}"></i></span>
                                    @endif
                                    <div class="stat-pill-content">
                                        <span class="stat-pill-val">{{ $st['value'] }}</span>
                                        @if(isset($st['label']))
                                            <span class="stat-pill-lbl">{{ $st['label'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif(isset($statsSlot))
                        {{ $statsSlot }}
                    @endif

                    @if(isset($actionsSlot))
                        <div class="hero-actions-wrapper mb-3">
                            {{ $actionsSlot }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>

                <!-- Right Column (Media / Custom Slot) -->
                <div class="col-lg-5 col-xl-5 text-center mt-4 mt-lg-0">
                    @if(isset($mediaSlot))
                        {{ $mediaSlot }}
                    @else
                        <div class="hero-media-showcase">
                            <div class="hero-backdrop-glow"></div>
                            <div class="hero-rings-wrapper">
                                <div class="hero-pulse-ring ring-1"></div>
                                <div class="hero-pulse-ring ring-2"></div>
                                <div class="hero-pulse-ring ring-3"></div>
                            </div>
                            <img src="{{ $image ?? asset('assets/images/hero_logo_design.png') }}"
                                 alt="{{ $imageAlt }}"
                                 class="hero-main-img" />
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Centered Layout -->
            <div class="row justify-content-center text-center">
                <div class="col-lg-10 col-xl-9">
                    @if($badgeText)
                        <div class="hero-cyber-badge {{ $badgeClass }} mx-auto">
                            @if($badgeIcon)
                                <span class="badge-icon"><i class="{{ $badgeIcon }}"></i></span>
                            @endif
                            <span>{{ $badgeText }}</span>
                        </div>
                    @endif

                    <h1 class="hero-main-title">
                        @if($typewriter)
                            <span class="typewriter-text" data-text="{{ $title ?? ($titlePrefix . ' ' . $highlight . ' ' . $titleSuffix) }}"></span>
                        @elseif(isset($titleSlot))
                            {{ $titleSlot }}
                        @elseif($titlePrefix || $highlight || $titleSuffix)
                            {{ $titlePrefix }}
                            @if($highlight)
                                <span class="highlight {{ $highlightClass }}">{{ $highlight }}</span>
                            @endif
                            {{ $titleSuffix }}
                        @elseif($title)
                            {!! html_entity_decode($title, ENT_QUOTES, 'UTF-8') !!}
                        @endif
                    </h1>

                    @if($subtitle)
                        <p class="hero-main-subtitle centered-sub">
                            @if($typewriter)
                                <span class="typewriter-text" data-text="{{ $subtitle }}"></span>
                            @else
                                {{ $subtitle }}
                            @endif
                        </p>
                    @endif

                    @if(!empty($stats))
                        <div class="hero-stats-row justify-center mb-4">
                            @foreach($stats as $st)
                                <div class="hero-stat-pill">
                                    @if(isset($st['icon']))
                                        <span class="stat-pill-icon"><i class="{{ $st['icon'] }}"></i></span>
                                    @endif
                                    <div class="stat-pill-content">
                                        <span class="stat-pill-val">{{ $st['value'] }}</span>
                                        @if(isset($st['label']))
                                            <span class="stat-pill-lbl">{{ $st['label'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif(isset($statsSlot))
                        {{ $statsSlot }}
                    @endif

                    @if(isset($actionsSlot))
                        <div class="hero-actions-wrapper mb-3">
                            {{ $actionsSlot }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </div>
        @endif
    </div>
</section>
