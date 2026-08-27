<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <link href="{{ asset('assets/css/variables.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/global.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/animations.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />

    @stack('styles')

    <link href="{{ asset('assets/css/modern-footer.css') }}" rel="stylesheet" />
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

    @stack('scripts')
</body>
</html>
