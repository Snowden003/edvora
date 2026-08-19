<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edvora Email Verification</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fc;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fc;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.1);">

                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#0D0D0D 0%,#1F3A5F 50%,#1F8FFF 100%);padding:40px 48px;text-align:center;">
                            <div style="display:inline-flex;align-items:center;gap:10px;">
                                <div style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;display:inline-flex;align-items:center;justify-content:center;font-size:22px;">🎓</div>
                                <span style="color:#ffffff;font-size:1.6rem;font-weight:800;letter-spacing:-0.5px;">Edvora</span>
                            </div>
                            <p style="color:rgba(255,255,255,0.6);margin:8px 0 0;font-size:0.9rem;">Learning Platform</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:48px 48px 32px;">

                            <p style="margin:0 0 8px;font-size:0.9rem;color:#6c757d;text-transform:uppercase;letter-spacing:1px;font-weight:600;">Email Verification</p>
                            <h1 style="margin:0 0 16px;font-size:1.8rem;font-weight:800;color:#0D0D0D;line-height:1.2;">
                                Verify your account, <span style="color:#1F8FFF;">{{ $user->name }}</span>
                            </h1>
                            <p style="margin:0 0 32px;color:#495057;font-size:1rem;line-height:1.7;">
                                Welcome to Edvora! To complete your registration, please enter the 6-digit verification code below. This code is valid for <strong>10 minutes</strong>.
                            </p>

                            {{-- OTP Box --}}
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding:0 0 32px;">
                                        <div style="background:linear-gradient(135deg,rgba(31,143,255,0.08),rgba(0,240,255,0.05));border:2px dashed rgba(31,143,255,0.4);border-radius:16px;padding:28px 40px;display:inline-block;">
                                            <p style="margin:0 0 8px;font-size:0.75rem;color:#6c757d;text-transform:uppercase;letter-spacing:2px;font-weight:600;">Your verification code</p>
                                            <div style="font-size:3rem;font-weight:900;letter-spacing:16px;color:#1F8FFF;font-family:'Courier New',monospace;line-height:1;">
                                                {{ $code }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 24px;color:#495057;font-size:0.95rem;line-height:1.7;">
                                Enter this code on the verification page to activate your Edvora account.
                            </p>

                            {{-- Divider --}}
                            <hr style="border:none;border-top:1px solid #e9ecef;margin:0 0 24px;">

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background:#fff8e1;border-left:4px solid #ffc107;border-radius:8px;padding:14px 16px;">
                                        <p style="margin:0;font-size:0.85rem;color:#856404;">
                                            <strong>⚠️ Security notice:</strong> Never share this code with anyone. Edvora staff will never ask for your verification code.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f8f9fa;padding:24px 48px;border-top:1px solid #e9ecef;text-align:center;">
                            <p style="margin:0 0 8px;font-size:0.8rem;color:#adb5bd;">
                                This email was sent to <strong>{{ $user->email }}</strong> because an account was created on Edvora.
                            </p>
                            <p style="margin:0;font-size:0.8rem;color:#adb5bd;">
                                If you did not create this account, you can safely ignore this email.
                            </p>
                            <p style="margin:12px 0 0;font-size:0.8rem;color:#ced4da;">© {{ date('Y') }} Edvora Tech. All rights reserved.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
