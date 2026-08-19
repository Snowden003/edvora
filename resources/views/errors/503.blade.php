<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Service Unavailable | Edvora</title>
    <link href="{{ asset('assets/css/error-pages.css') }}" rel="stylesheet" />
</head>
<body class="error-page">
    <div class="glow-orb glow-1-blue"></div>
    <div class="glow-orb glow-2-purple"></div>

    <div class="error-container">
        <div class="error-content border-503">
            <div class="error-code code-503">503</div>
            <div class="error-icon icon-503">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="error-title">Service Unavailable</h1>
            <p class="error-message">
                The service is temporarily down for maintenance.<br>
                We'll be back shortly. Thank you for your patience.
            </p>
            <div class="error-actions">
                <a href="javascript:location.reload()" class="error-btn error-btn-primary">
                    <svg class="error-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"/>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                    </svg>
                    Try Again
                </a>
                <a href="{{ route('home') }}" class="error-btn error-btn-outline">
                    Home Page
                </a>
            </div>
        </div>
    </div>
</body>
</html>
