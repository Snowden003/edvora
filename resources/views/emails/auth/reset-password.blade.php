<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - Edvora</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fc;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fc;padding:48px 16px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 12px 40px rgba(13,27,62,0.08);border:1px solid #eef2f6;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#0D0D0D 0%,#132238 50%,#1F8FFF 100%);padding:44px 48px;text-align:center;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <div style="display:inline-block;padding:10px 18px;background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.15);border-radius:50px;margin-bottom:12px;">
                                            <span style="font-size:22px;vertical-align:middle;margin-right:6px;">🎓</span>
                                            <span style="color:#ffffff;font-size:1.4rem;font-weight:800;letter-spacing:-0.5px;vertical-align:middle;">Edvora</span>
                                        </div>
                                        <h2 style="color:#ffffff;font-size:1.35rem;font-weight:700;margin:10px 0 0;letter-spacing:-0.3px;">Password Reset Request</h2>
                                        <p style="color:rgba(255,255,255,0.7);font-size:0.88rem;margin:6px 0 0;">Secure Authentication Service</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body Content --}}
                    <tr>
                        <td style="padding:44px 48px 36px;">

                            {{-- Icon & Greeting --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                                <tr>
                                    <td width="56" valign="top">
                                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#EBF5FF 0%,#D1E9FF 100%);border-radius:14px;border:1px solid #B8DCFF;text-align:center;line-height:48px;font-size:22px;">
                                            🔐
                                        </div>
                                    </td>
                                    <td style="padding-left:14px;" valign="middle">
                                        <p style="margin:0;font-size:0.85rem;color:#6b7280;text-transform:uppercase;letter-spacing:1px;font-weight:700;">Account Recovery</p>
                                        <h1 style="margin:4px 0 0;font-size:1.45rem;font-weight:800;color:#111827;line-height:1.2;">
                                            Hello, <span style="color:#1F8FFF;">{{ $user->name ?? 'User' }}</span>
                                        </h1>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 20px;color:#374151;font-size:0.97rem;line-height:1.7;">
                                We received a request to reset the password for your Edvora account. If you initiated this request, click the button below to choose a new secure password:
                            </p>

                            {{-- CTA Button --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:32px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" target="_blank" style="display:inline-block;background:linear-gradient(135deg,#1F8FFF 0%,#0066FF 100%);color:#ffffff;text-decoration:none;font-size:1rem;font-weight:700;padding:16px 38px;border-radius:12px;box-shadow:0 8px 24px rgba(31,143,255,0.35);letter-spacing:0.2px;">
                                            Reset My Password &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Expiration Notice Card --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 28px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="28" valign="top" style="font-size:16px;line-height:1.4;">⏱️</td>
                                                <td style="color:#475569;font-size:0.88rem;line-height:1.6;">
                                                    This password reset link is valid for <strong>{{ $expireMinutes }} minutes</strong>. After that, you will need to submit a new request.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Direct URL Box --}}
                            <p style="margin:0 0 8px;color:#6b7280;font-size:0.84rem;line-height:1.6;">
                                If you are having trouble clicking the "Reset My Password" button, copy and paste the URL below into your web browser:
                            </p>
                            <div style="background:#f1f5f9;padding:12px 16px;border-radius:8px;word-break:break-all;border:1px solid #e2e8f0;margin-bottom:28px;">
                                <a href="{{ $resetUrl }}" style="color:#1F8FFF;font-size:0.8rem;text-decoration:none;font-family:Consolas,Monaco,monospace;line-height:1.5;">
                                    {{ $resetUrl }}
                                </a>
                            </div>

                            {{-- Security Callout --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbeb;border-left:4px solid #f59e0b;border-radius:8px;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p style="margin:0;font-size:0.86rem;color:#92400e;line-height:1.55;">
                                            <strong>🛡️ Didn't request a password reset?</strong><br>
                                            If you did not make this request, you can safely ignore this email. Your password will remain unchanged and your account is secure.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f8fafc;padding:28px 48px;border-top:1px solid #edf2f7;text-align:center;">
                            <p style="margin:0 0 6px;font-size:0.82rem;color:#64748b;">
                                This security email was sent to <strong style="color:#334155;">{{ $user->email }}</strong>
                            </p>
                            <p style="margin:0 0 16px;font-size:0.8rem;color:#94a3b8;">
                                Edvora Tech &bull; Empowering online learning & education
                            </p>
                            <div style="font-size:0.78rem;color:#cbd5e1;border-top:1px solid #e2e8f0;padding-top:14px;">
                                &copy; {{ date('Y') }} Edvora. All rights reserved.
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
