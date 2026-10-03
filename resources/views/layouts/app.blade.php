<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA & Mobile Web App Meta & Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Edvora">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/icons/icon-512x512.png">
    <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
    <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') ?? 'mt1' }}">

    @php
        $metaTitle = trim($__env->yieldContent('meta_title', $__env->yieldContent('title', 'Edvora Tech - Free Online Education for Afghan Women')));
        $metaDescription = trim($__env->yieldContent('meta_description', 'Edvora provides free online courses and practical digital skills education for Afghan women and girls.'));
        $canonicalUrl = trim($__env->yieldContent('canonical', url()->current()));
        $metaImage = trim($__env->yieldContent('meta_image', asset('logo.png')));
        $metaType = trim($__env->yieldContent('meta_type', 'website'));
    @endphp
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:type" content="{{ $metaType }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
    @stack('structured_data')
    @php($isHomePage = request()->routeIs('home'))

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Vazirmatn:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/fonts/webfonts/Vazirmatn-font-face.css" />

    <style>
        [dir="rtl"], .rtl-layout {
            font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            letter-spacing: 0 !important;
        }
        [dir="rtl"] body, .rtl-layout body {
            font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            font-weight: 500;
        }
        /* Bootstrap 5 Carousel RTL support */
        [dir="rtl"] .carousel-item,
        .rtl-layout .carousel-item {
            float: right;
            margin-right: 0 !important;
            margin-left: -100% !important;
        }
        [dir="rtl"] .carousel-item.active:not(.carousel-item-start):not(.carousel-item-end),
        .rtl-layout .carousel-item.active:not(.carousel-item-start):not(.carousel-item-end) {
            margin-right: 0 !important;
            margin-left: 0 !important;
            float: none;
        }
        [dir="rtl"] .carousel-item-next:not(.carousel-item-start),
        [dir="rtl"] .active.carousel-item-end,
        .rtl-layout .carousel-item-next:not(.carousel-item-start),
        .rtl-layout .active.carousel-item-end {
            transform: translateX(-100%) !important;
        }
        [dir="rtl"] .carousel-item-prev:not(.carousel-item-end),
        [dir="rtl"] .active.carousel-item-start,
        .rtl-layout .carousel-item-prev:not(.carousel-item-end),
        .rtl-layout .active.carousel-item-start {
            transform: translateX(100%) !important;
        }
    </style>

    <link href="{{ asset('assets/css/variables.css') }}?v={{ file_exists(public_path('assets/css/variables.css')) ? filemtime(public_path('assets/css/variables.css')) : time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/global.css') }}?v={{ file_exists(public_path('assets/css/global.css')) ? filemtime(public_path('assets/css/global.css')) : time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/animations.css') }}?v={{ file_exists(public_path('assets/css/animations.css')) ? filemtime(public_path('assets/css/animations.css')) : time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.css') }}?v={{ file_exists(public_path('assets/css/style.css')) ? filemtime(public_path('assets/css/style.css')) : time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/page-hero.css') }}?v={{ file_exists(public_path('assets/css/page-hero.css')) ? filemtime(public_path('assets/css/page-hero.css')) : time() }}" rel="stylesheet" />

    @stack('styles')

    <link href="{{ asset('assets/css/modern-footer.css') }}?v={{ file_exists(public_path('assets/css/modern-footer.css')) ? filemtime(public_path('assets/css/modern-footer.css')) : time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/ai-chatbot.css') }}?v={{ file_exists(public_path('assets/css/ai-chatbot.css')) ? filemtime(public_path('assets/css/ai-chatbot.css')) : time() }}" rel="stylesheet" />
    @auth
    <link href="{{ asset('assets/css/notifications.css') }}?v={{ file_exists(public_path('assets/css/notifications.css')) ? filemtime(public_path('assets/css/notifications.css')) : time() }}" rel="stylesheet" />
    @vite('resources/js/app.js')
    @endauth
    @if(app()->environment('production'))
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-GKLFPS4011"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'G-GKLFPS4011');
        </script>
    @endif
</head>

<body class="edvora-theme {{ $isHomePage ? 'page-home' : '' }} @yield('body-class')" data-user-role="{{ Auth::check() ? Auth::user()->role : 'guest' }}" data-user-id="{{ Auth::check() ? Auth::id() : '' }}">
    @if (!View::hasSection('hide_header'))
        @include('layouts.partials.header')
    @endif

    @yield('content')

    @if (!View::hasSection('hide_footer'))
        @include('layouts.partials.footer')
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="{{ asset('assets/js/main.js') }}" defer></script>
    <script src="{{ asset('assets/js/modern-footer.js') }}" defer></script>
    @auth
    <script src="{{ asset('assets/js/notifications.js') }}" defer></script>
    @include('components.banned-account-modal')
    @endauth

    {{-- Floating Ask AI Widget (Excluded completely from all dashboards and dedicated chat pages) --}}
    @if(!request()->is('teacher*', 'student*', 'admin*', 'scoring-help*', 'courses/*/chat*', 'ai-chat*', 'assistant*') && !View::hasSection('hide_ai_chatbot'))
        @include('components.ai-chatbot')
    @endif

    <!-- PWA Service Worker Registration & Install Prompt Capture -->
    <script>
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            window.deferredPwaPrompt = e;
            window.dispatchEvent(new CustomEvent('pwa-prompt-ready'));
        });
        window.addEventListener('appinstalled', function() {
            window.deferredPwaPrompt = null;
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(reg) {
                        // console.log('PWA ServiceWorker registered with scope:', reg.scope);
                    })
                    .catch(function(err) {
                        console.warn('PWA ServiceWorker registration failed:', err);
                    });
            });
        }
    </script>
</body>
</html>
