<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />
    <title>@yield('title', 'Edvora Tech')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="{{ asset('assets/css/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/global.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/animations.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/beta-notice.css') }}" />
    
    @stack('styles')
</head>

<body class="edvora-theme d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #0D0D0D 0%, #1F8FFF 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-mortarboard-fill me-2"></i>Edvora
            </a>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('leaderboard') }}" class="text-white text-decoration-none small">Leaderboard</a>
                <span class="text-white-50 small">|</span>
                <a href="{{ route('terms') }}" class="text-white text-decoration-none small">Terms</a>
                <span class="text-white-50 small">|</span>
                <a href="{{ route('privacy') }}" class="text-white text-decoration-none small">Privacy</a>
                @yield('auth-nav-button')
            </div>
        </div>
    </nav>

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/auth.js') }}"></script>
    <script src="{{ asset('assets/js/beta-notice.js') }}" defer></script>
    
    @stack('scripts')
</body>
</html>
