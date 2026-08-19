<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply from Edvora Tech</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f7fa; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1f8fff, #0066cc); padding: 30px 40px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;">Edvora Tech</h1>
                            <p style="color: rgba(255,255,255,0.85); margin: 8px 0 0; font-size: 14px;">We've replied to your message</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #333; font-size: 16px; margin: 0 0 20px;">
                                Hi <strong>{{ $senderName }}</strong>,
                            </p>
                            <p style="color: #555; font-size: 15px; line-height: 1.6; margin: 0 0 25px;">
                                Thank you for reaching out to us. Here is our response to your message:
                            </p>

                            <!-- Original Message -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #dee2e6; border-radius: 8px; padding: 16px 20px; margin-bottom: 25px;">
                                <p style="color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px; font-weight: 600;">Your Original Message</p>
                                <p style="color: #555; font-size: 14px; line-height: 1.6; margin: 0;">{{ $originalMessage }}</p>
                            </div>

                            <!-- Admin Reply -->
                            <div style="background-color: #e8f4fd; border-left: 4px solid #1f8fff; border-radius: 8px; padding: 16px 20px; margin-bottom: 25px;">
                                <p style="color: #1f8fff; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px; font-weight: 600;">Our Reply</p>
                                <div style="color: #333; font-size: 15px; line-height: 1.7; margin: 0;">{!! $adminReply !!}</div>
                            </div>

                            <p style="color: #555; font-size: 14px; line-height: 1.6; margin: 25px 0 0;">
                                If you have any further questions, feel free to reply to this email or visit our website.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 25px 40px; text-align: center; border-top: 1px solid #eee;">
                            <p style="color: #999; font-size: 13px; margin: 0 0 5px;">
                                &copy; {{ date('Y') }} Edvora Tech. All rights reserved.
                            </p>
                            <p style="color: #bbb; font-size: 12px; margin: 0;">
                                This email was sent in response to your contact form submission.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
