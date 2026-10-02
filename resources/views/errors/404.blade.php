<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحه مورد نظر یافت نشد - ادورا | Edvora</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <link href="{{ asset('assets/css/error-pages.css') }}" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Vazirmatn', -apple-system, sans-serif !important; }
    </style>
</head>
<body class="error-page">
    <div class="glow-orb glow-1-blue"></div>
    <div class="glow-orb glow-2-cyan"></div>

    <div class="error-container">
        <div class="error-content">
            <div class="error-code code-404">۴۰۴</div>
            <div class="error-icon icon-404">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 8v4M12 16h.01" stroke-linecap="round"/>
                </svg>
            </div>
            <h1 class="error-title">صفحه مورد نظر یافت نشد</h1>
            <p class="error-message">
                آدرس وارد شده معتبر نیست یا صفحه به نشانی دیگری منتقل شده است. تا چند لحظه دیگر به داشبورد هدایت می‌شوید...
            </p>
            <div class="error-actions">
                @php
                    $targetUrl = auth()->check() ? auth()->user()->dashboardRoute() : route('pwa.app');
                    $btnLabel = auth()->check() ? 'بازگشت به داشبورد' : 'ورود به اپلیکیشن ادورا';
                @endphp
                <a href="{{ $targetUrl }}" class="error-btn error-btn-primary">
                    <svg class="error-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                    {{ $btnLabel }}
                </a>
                <a href="{{ url()->previous() }}" class="error-btn error-btn-outline">
                    صفحه قبلی
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto-redirect to prevent dead-ends in PWA and app stores
        setTimeout(function() {
            window.location.href = "{{ $targetUrl }}";
        }, 3500);
    </script>
</body>
</html>
