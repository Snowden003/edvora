<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identity Verification Rejected - Edvora</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fc;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fc;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.1);">

                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#0D0D0D 0%,#1F3A5F 50%,#1F8FFF 100%);padding:32px 48px;text-align:center;">
                            <a href="{{ url('/') }}" style="text-decoration:none;display:inline-block;">
                                <img src="{{ url('assets/images/logo1.jpg') }}" alt="Edvora Tech"
                                     width="120" style="height:auto;border-radius:8px;display:block;border:0;" />
                            </a>
                            <p style="color:rgba(255,255,255,0.6);margin:10px 0 0;font-size:0.85rem;font-family:'Segoe UI',Tahoma,sans-serif;">Learning Platform</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:48px 48px 32px;">

                            <div style="text-align:center;margin-bottom:28px;">
                                <div style="width:72px;height:72px;background:linear-gradient(135deg,#dc3545,#e83e5a);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:36px;">❌</div>
                            </div>

                            <p style="margin:0 0 8px;font-size:0.9rem;color:#6c757d;text-transform:uppercase;letter-spacing:1px;font-weight:600;text-align:center;">Identity Verification</p>
                            <h1 style="margin:0 0 16px;font-size:1.7rem;font-weight:800;color:#0D0D0D;line-height:1.2;text-align:center;">
                                We're sorry, <span style="color:#dc3545;">{{ $user->name }}</span>
                            </h1>
                            <p style="margin:0 0 24px;color:#495057;font-size:1rem;line-height:1.8;text-align:center;">
                                Your identity verification request has been rejected by our admin team.<br>
                                Please re-upload a clearer image of your Tazkira to try again.
                            </p>

                            @if($user->identity_rejection_reason)
                            <div style="background:#fff3cd;border-left:4px solid #ffc107;border-radius:8px;padding:16px;margin-bottom:28px;">
                                <p style="margin:0 0 6px;font-size:0.85rem;color:#856404;font-weight:700;">Reason for rejection:</p>
                                <p style="margin:0;font-size:0.9rem;color:#533f03;">{{ $user->identity_rejection_reason }}</p>
                            </div>
                            @endif

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding:0 0 32px;">
                                        <a href="{{ route('student.identity.show') }}"
                                           style="display:inline-block;background:linear-gradient(135deg,#1F8FFF,#0d6efd);color:#ffffff;text-decoration:none;padding:14px 36px;border-radius:12px;font-size:1rem;font-weight:700;letter-spacing:0.3px;">
                                            Re-upload Image
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <hr style="border:none;border-top:1px solid #e9ecef;margin:0 0 24px;">

                            <div style="background:#fff8e1;border-left:4px solid #ffc107;border-radius:8px;padding:14px 16px;">
                                <p style="margin:0;font-size:0.85rem;color:#856404;">
                                    <strong>⚠️ Tip:</strong> Make sure the photo is clear, readable, and all pages of the Tazkira are fully visible.
                                </p>
                            </div>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f8f9fa;padding:24px 48px;border-top:1px solid #e9ecef;text-align:center;">
                            <p style="margin:0 0 8px;font-size:0.8rem;color:#adb5bd;">
                                This email was sent to <strong>{{ $user->email }}</strong>.
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
