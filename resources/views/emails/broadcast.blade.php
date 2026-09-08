<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    @if(!empty($preheader))
    <div style="display:none;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
        {{ $preheader }}
    </div>
    @endif
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f1f5f9;padding:30px 15px;">
        <tr>
            <td align="center">
                <!-- Container Table (Max 600px) -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;background-color:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.06);border:1px solid #e2e8f0;">

                    <!-- Brand Header Bar -->
                    @php
                        // Resolve Logo (Embed inline CID for maximum compatibility across all email clients)
                        $logoSrc = null;
                        $optLogo = public_path('assets/images/email-logo.jpg');
                        $origLogo = public_path('assets/images/logo1.jpg');
                        $logoFile = file_exists($optLogo) ? $optLogo : (file_exists($origLogo) ? $origLogo : null);

                        if ($logoFile && isset($message)) {
                            try {
                                $logoSrc = $message->embed($logoFile);
                            } catch (\Throwable $e) {
                                $logoSrc = asset('assets/images/logo1.jpg');
                            }
                        } else {
                            $logoSrc = asset('assets/images/logo1.jpg');
                        }

                        // Resolve Hero Banner (Embed inline CID if local file, or preserve external URL)
                        $bannerSrc = null;
                        $candidate = $bannerImage ?? $bannerUrl ?? null;

                        if (!empty($candidate)) {
                            $cleanCandidate = ltrim(str_replace(['\\'], '/', $candidate), '/');
                            if (str_starts_with($cleanCandidate, 'storage/')) {
                                $cleanCandidate = substr($cleanCandidate, 8);
                            }

                            $storageCandidate = storage_path('app/public/' . $cleanCandidate);
                            $publicCandidate = public_path($candidate);

                            if (file_exists($storageCandidate) && isset($message)) {
                                try {
                                    $bannerSrc = $message->embed($storageCandidate);
                                } catch (\Throwable $e) {
                                    $bannerSrc = asset('storage/' . $cleanCandidate);
                                }
                            } elseif (file_exists($publicCandidate) && isset($message)) {
                                try {
                                    $bannerSrc = $message->embed($publicCandidate);
                                } catch (\Throwable $e) {
                                    $bannerSrc = asset($candidate);
                                }
                            } elseif (filter_var($candidate, FILTER_VALIDATE_URL)) {
                                // If URL points to local dev domain (.test or localhost), check if file exists locally to embed
                                if ((str_contains($candidate, '.test') || str_contains($candidate, 'localhost') || str_contains($candidate, '127.0.0.1')) && isset($message)) {
                                    $urlPath = parse_url($candidate, PHP_URL_PATH);
                                    if ($urlPath) {
                                        $relPath = ltrim(preg_replace('#^/storage/#', '', $urlPath), '/');
                                        $localSt = storage_path('app/public/' . $relPath);
                                        $localPub = public_path(ltrim($urlPath, '/'));
                                        if (file_exists($localSt)) {
                                            try { $bannerSrc = $message->embed($localSt); } catch (\Throwable $e) {}
                                        } elseif (file_exists($localPub)) {
                                            try { $bannerSrc = $message->embed($localPub); } catch (\Throwable $e) {}
                                        }
                                    }
                                }
                                if (!$bannerSrc) {
                                    $bannerSrc = $candidate;
                                }
                            }
                        }

                        // Default Banner Fallback
                        if (empty($bannerSrc)) {
                            $optHero = public_path('assets/images/email-hero.jpg');
                            $origHero = public_path('assets/images/hero_logo_design.png');
                            $heroFile = file_exists($optHero) ? $optHero : (file_exists($origHero) ? $origHero : null);

                            if ($heroFile && isset($message)) {
                                try {
                                    $bannerSrc = $message->embed($heroFile);
                                } catch (\Throwable $e) {
                                    $bannerSrc = asset('assets/images/hero_logo_design.png');
                                }
                            } else {
                                $bannerSrc = asset('assets/images/hero_logo_design.png');
                            }
                        }
                    @endphp
                    <tr>
                        <td align="center" style="background:linear-gradient(135deg,#020811 0%,#0f294d 50%,#1f8fff 100%);padding:24px 30px;text-align:center;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/') }}" target="_blank" style="text-decoration:none;display:inline-block;">
                                            <img src="{{ $logoSrc }}" alt="Edvora Tech" width="110" style="height:auto;border-radius:8px;display:block;border:0;margin:0 auto;" />
                                        </a>
                                        <p style="margin:8px 0 0;font-size:12px;font-weight:700;color:rgba(255,255,255,0.7);letter-spacing:1px;text-transform:uppercase;">
                                            Free Digital Education Platform
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Hero Banner Image -->
                    <tr>
                        <td align="center" style="padding:0;background-color:#0b1526;">
                            <img src="{{ $bannerSrc }}" alt="{{ $subject }}" width="600" style="width:100%;max-width:600px;height:auto;max-height:280px;object-fit:cover;display:block;border:0;" />
                        </td>
                    </tr>

                    <!-- Email Body Area -->
                    <tr>
                        <td style="padding:36px 36px 28px;">

                            <!-- Eyebrow Badge -->
                            <div style="margin-bottom:14px;">
                                <span style="display:inline-block;padding:4px 12px;border-radius:50px;background-color:rgba(31,143,255,0.12);color:#0284c7;font-size:11px;font-weight:800;letter-spacing:0.5px;text-transform:uppercase;">
                                    Official Announcement
                                </span>
                            </div>

                            <!-- Subject / Title -->
                            <h1 style="margin:0 0 18px 0;font-size:22px;line-height:1.4;font-weight:800;color:#0f172a;letter-spacing:-0.02em;">
                                {{ $subject }}
                            </h1>

                            <!-- Greeting -->
                            <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#334155;">
                                Dear <strong>{{ $recipient->name ?? 'Student' }}</strong>,
                            </p>

                            <!-- Main Message Content -->
                            @php
                                $processedBody = str_replace(
                                    ['{name}', '{email}', '{app_name}'],
                                    [$recipient->name ?? 'Student', $recipient->email ?? '', config('app.name', 'Edvora Tech')],
                                    $body
                                );
                            @endphp
                            <div style="font-size:15px;line-height:1.75;color:#475569;margin-bottom:28px;">
                                {!! nl2br(strip_tags($processedBody, '<p><br><b><strong><i><em><u><a><ul><ol><li><h3><h4><blockquote><code>')) !!}
                            </div>

                            <!-- Call To Action Button (If configured) -->
                            @if(!empty($ctaText) && !empty($ctaUrl))
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:30px 0 20px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $ctaUrl }}" target="_blank" style="display:inline-block;padding:14px 34px;background:linear-gradient(135deg,#1f8fff 0%,#00b4d8 100%);color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;border-radius:12px;box-shadow:0 4px 14px rgba(31,143,255,0.35);letter-spacing:0.2px;">
                                            {{ $ctaText }} &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Platform Signature Divider -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid #e2e8f0;margin-top:28px;padding-top:20px;">
                                <tr>
                                    <td>
                                        <p style="margin:0;font-size:13px;color:#64748b;line-height:1.5;">
                                            Warm regards,<br>
                                            <strong style="color:#0f172a;">The Edvora Tech Team</strong><br>
                                            <a href="{{ url('/') }}" style="color:#1f8fff;text-decoration:none;font-weight:600;">www.edvora.org</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer Bar -->
                    <tr>
                        <td align="center" style="background-color:#f8fafc;padding:24px 30px;border-top:1px solid #e2e8f0;text-align:center;">
                            <p style="margin:0 0 6px;font-size:12px;color:#94a3b8;">
                                You are receiving this email because you are registered as a learner on <strong>Edvora Tech</strong>.
                            </p>
                            <p style="margin:0 0 10px;font-size:12px;color:#64748b;">
                                Sent to: <strong>{{ $recipient->email }}</strong>
                            </p>
                            <p style="margin:0;font-size:11px;color:#cbd5e1;">
                                &copy; {{ date('Y') }} Edvora Tech. Empowering education with free digital learning.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
