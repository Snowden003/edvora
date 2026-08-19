<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
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
                                <div style="width:72px;height:72px;background:linear-gradient(135deg,#10B981,#059669);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:36px;">📢</div>
                            </div>

                            <p style="margin:0 0 8px;font-size:0.9rem;color:#6c757d;text-transform:uppercase;letter-spacing:1px;font-weight:600;text-align:center;">Message from Edvora Admin</p>
                            <h1 style="margin:0 0 24px;font-size:1.6rem;font-weight:800;color:#0D0D0D;line-height:1.3;text-align:center;">
                                {{ $subject }}
                            </h1>

                            <p style="margin:0 0 8px;font-size:0.95rem;color:#374151;">
                                Hi <strong>{{ $teacher->name }}</strong>,
                            </p>

                            <div style="margin:16px 0 28px;font-size:0.95rem;color:#495057;line-height:1.8;border-left:4px solid #10B981;padding-left:16px;background:#f0fdf4;border-radius:0 8px 8px 0;padding:16px 16px 16px 20px;">
                                {!! nl2br(strip_tags($body, '<b><strong><i><em><u><br><p><ul><ol><li><a>')) !!}
                            </div>

                            <hr style="border:none;border-top:1px solid #e9ecef;margin:0 0 24px;">

                            <div style="background:#f0fdf4;border-left:4px solid #10B981;border-radius:8px;padding:14px 16px;">
                                <p style="margin:0;font-size:0.85rem;color:#166534;">
                                    <strong>ℹ️ Note:</strong> This message was sent directly from the Edvora admin team. You do not need to reply to this email.
                                </p>
                            </div>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f8f9fa;padding:24px 48px;border-top:1px solid #e9ecef;text-align:center;">
                            <p style="margin:0 0 8px;font-size:0.8rem;color:#adb5bd;">
                                This email was sent to <strong>{{ $teacher->email }}</strong>.
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
