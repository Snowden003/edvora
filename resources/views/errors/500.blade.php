<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | Edvora</title>
    <link href="{{ asset('assets/css/error-pages.css') }}" rel="stylesheet" />
</head>
<body class="error-page">
    <div class="glow-orb glow-1-red"></div>
    <div class="glow-orb glow-2-pink"></div>

    <div class="error-container">
        <div class="error-content border-500">
            <div class="error-code code-500">500</div>
            <div class="error-icon icon-500">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.07 4.93a10 10 0 0 1 0 14.14" stroke-linecap="round"/>
                    <path d="M4.93 4.93a10 10 0 0 0 0 14.14" stroke-linecap="round"/>
                    <path d="M16.24 7.76a6 6 0 0 1 0 8.49" stroke-linecap="round"/>
                    <path d="M7.76 7.76a6 6 0 0 0 0 8.49" stroke-linecap="round"/>
                </svg>
            </div>
            <h1 class="error-title">Internal Server Error</h1>
            <p class="error-message">
                Something went wrong on our end.<br>
                Our team has been notified. Please try again later.
            </p>
            <div class="error-actions">
                <a href="{{ url()->previous() }}" class="error-btn error-btn-red">
                    <svg class="error-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Go Back
                </a>
                <a href="{{ route('home') }}" class="error-btn error-btn-outline-red">
                    Home Page
                </a>
            </div>
        </div>
    </div>
</body>
</html>
