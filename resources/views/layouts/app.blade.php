<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
    <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') ?? 'mt1' }}">
    @php
        $metaTitle = trim($__env->yieldContent('meta_title', $__env->yieldContent('title', 'Edvora Tech - Free Online Education for Afghan Women')));
        $metaDescription = trim($__env->yieldContent('meta_description', 'Edvora provides free online courses and practical digital skills education for Afghan women and girls.'));
        $canonicalUrl = trim($__env->yieldContent('canonical', url()->current()));
        $metaImage = trim($__env->yieldContent('meta_image', asset('assets/images/logo1.jpg')));
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

    <link href="{{ asset('assets/css/variables.css') }}?v={{ file_exists(public_path('assets/css/variables.css')) ? filemtime(public_path('assets/css/variables.css')) : time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/global.css') }}?v={{ file_exists(public_path('assets/css/global.css')) ? filemtime(public_path('assets/css/global.css')) : time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/animations.css') }}?v={{ file_exists(public_path('assets/css/animations.css')) ? filemtime(public_path('assets/css/animations.css')) : time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.css') }}?v={{ file_exists(public_path('assets/css/style.css')) ? filemtime(public_path('assets/css/style.css')) : time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/page-hero.css') }}?v={{ file_exists(public_path('assets/css/page-hero.css')) ? filemtime(public_path('assets/css/page-hero.css')) : time() }}" rel="stylesheet" />

    @stack('styles')

    <link href="{{ asset('assets/css/modern-footer.css') }}?v={{ file_exists(public_path('assets/css/modern-footer.css')) ? filemtime(public_path('assets/css/modern-footer.css')) : time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/ai-chatbot.css') }}?v={{ file_exists(public_path('assets/css/ai-chatbot.css')) ? filemtime(public_path('assets/css/ai-chatbot.css')) : time() }}" rel="stylesheet" />
    @auth
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

<body class="edvora-theme {{ $isHomePage ? 'page-home' : '' }} @yield('body-class')" data-user-role="{{ Auth::check() ? Auth::user()->role : 'guest' }}">
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
    @endauth

    @include('components.ai-chatbot')
    @stack('scripts')
</body>
</html>
