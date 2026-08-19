<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>502 - Bad Gateway | Edvora</title>
    <link href="{{ asset('assets/css/error-pages.css') }}" rel="stylesheet" />
</head>
<body class="error-page">
    <div class="glow-orb glow-1-red"></div>
    <div class="glow-orb glow-2-orange"></div>

    <div class="error-container">
        <div class="error-content border-502">
            <div class="error-code code-502">502</div>
            <div class="error-icon icon-502">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="2" y="2" width="20" height="8" rx="2" ry="2" stroke-linecap="round"/>
                    <rect x="2" y="14" width="20" height="8" rx="2" ry="2" stroke-linecap="round"/>
                    <line x1="6" y1="6" x2="6.01" y2="6" stroke-linecap="round"/>
                    <line x1="6" y1="18" x2="6.01" y2="18" stroke-linecap="round"/>
                    <line x1="10" y1="10" x2="10" y2="14" stroke-linecap="round" stroke-dasharray="2 2"/>
                </svg>
            </div>
            <h1 class="error-title">Bad Gateway</h1>
            <p class="error-message">
                The server received an invalid response from an upstream server.<br>
                Please try again in a few minutes.
            </p>
            <div class="error-actions">
                <a href="javascript:location.reload()" class="error-btn error-btn-red">
                    <svg class="error-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"/>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                    </svg>
                    Try Again
                </a>
                <a href="{{ route('home') }}" class="error-btn error-btn-outline-red">
                    Home Page
                </a>
            </div>
        </div>
    </div>
</body>
</html>
