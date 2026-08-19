<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation Card - {{ $event_title }}</title>
    <style>
        @font-face {
            font-family: 'IRANSans';
            src: url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/fonts/webfonts/Vazirmatn-Regular.woff2') format('woff2');
            font-weight: normal;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'IRANSans', 'Vazirmatn', 'Tahoma', sans-serif;
            width: 80mm;
            height: 120mm;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 8mm;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 6mm;
            height: 100%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #1F8FFF;
            padding-bottom: 4mm;
        }

        .logo {
            font-size: 14px;
            font-weight: bold;
            color: #1F8FFF;
            margin-bottom: 2mm;
        }

        .title {
            font-size: 11px;
            color: #333;
            font-weight: bold;
        }

        .content {
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4mm 0;
        }

        .message {
            font-size: 9px;
            line-height: 1.6;
            color: #555;
            margin-bottom: 4mm;
        }

        .name {
            font-size: 12px;
            font-weight: bold;
            color: #1F8FFF;
            margin: 3mm 0;
            padding: 2mm;
            background: #f0f7ff;
            border-radius: 4px;
        }

        .event-name {
            font-size: 10px;
            font-weight: bold;
            color: #333;
            margin: 2mm 0;
        }

        .details {
            font-size: 8px;
            color: #666;
            margin-top: 3mm;
            line-height: 1.5;
        }

        .registration-code {
            text-align: center;
            margin-top: 4mm;
            padding: 3mm;
            background: #f5f5f5;
            border-radius: 4px;
            border: 1px dashed #1F8FFF;
        }

        .code-label {
            font-size: 7px;
            color: #888;
            margin-bottom: 1mm;
        }

        .code-value {
            font-size: 11px;
            font-weight: bold;
            color: #1F8FFF;
            font-family: 'Courier New', monospace;
        }

        .footer {
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 3mm;
            font-size: 7px;
            color: #999;
        }

        .stamp {
            position: absolute;
            top: 15mm;
            right: 10mm;
            width: 15mm;
            height: 15mm;
            border: 2px solid #1F8FFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-15deg);
            opacity: 0.6;
        }

        .stamp-text {
            font-size: 6px;
            color: #1F8FFF;
            font-weight: bold;
        }

        .check-icon {
            color: #28a745;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <div class="logo">EDVORA TECH</div>
            <div class="title">کارت دعوت / Invitation Card</div>
        </div>

        <div class="content">
            <div class="message">
                این کاربر با موفقیت در برنامه<br>
                This user has successfully registered for
            </div>

            <div class="name">{{ $attendee_name }}</div>

            <div class="event-name">"{{ $event_title }}"</div>

            <div class="details">
                که در تاریخ {{ $event_date }}<br>
                در آدرس {{ $event_location }}<br>
                برگزار می‌شود، ثبت‌نام کرده است.<br><br>
                Held on {{ $event_date }} at {{ $event_location }}
            </div>

            @if($event_duration)
            <div class="details" style="margin-top: 2mm;">
                Duration: {{ $event_duration }}
            </div>
            @endif
        </div>

        <div class="registration-code">
            <div class="code-label">کد ثبت‌نام / Registration Code</div>
            <div class="code-value">{{ $registration_code }}</div>
        </div>

        <div class="footer">
            <span class="check-icon">✓</span> Verified by Edvora Tech<br>
            Registration Date: {{ $registration_date }}
        </div>
    </div>
</body>
</html>
