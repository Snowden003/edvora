<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Edvora</title>
    <link href="{{ asset('assets/css/error-pages.css') }}" rel="stylesheet" />
</head>
<body class="error-page">
    <div class="glow-orb glow-1-blue"></div>
    <div class="glow-orb glow-2-cyan"></div>

    <div class="error-container">
        <div class="error-content">
            <div class="error-code code-404">404</div>
            <div class="error-icon icon-404">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 8v4M12 16h.01" stroke-linecap="round"/>
                </svg>
            </div>
            <h1 class="error-title">Page Not Found</h1>
            <p class="error-message">
                The page you're looking for doesn't exist or has been moved.
            </p>
            <div class="error-actions">
                <a href="{{ url()->previous() }}" class="error-btn error-btn-primary">
                    <svg class="error-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Go Back
                </a>
                <a href="{{ route('home') }}" class="error-btn error-btn-outline">
                    Home Page
                </a>
            </div>
        </div>
    </div>
</body>
</html>
