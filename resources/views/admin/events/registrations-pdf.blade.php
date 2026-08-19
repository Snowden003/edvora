<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Registrations - {{ $event->title }}</title>
    <style>
        @page {
            margin: 10mm 10mm 15mm 10mm;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.2;
            color: #333;
        }

        /* Clean Header */
        .header {
            background: #1f8fff;
            color: white;
            padding: 12px 15px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0 0 3px 0;
        }

        .header .report-title {
            font-size: 10px;
            opacity: 0.9;
        }

        /* Compact Event Info */
        .event-info {
            background: #f5f7fa;
            border-left: 4px solid #1f8fff;
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .event-info h4 {
            font-size: 10px;
            color: #1a1a2e;
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row-table {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 3px 0;
            font-size: 9px;
        }

        .info-cell.label {
            font-weight: bold;
            color: #666;
            width: 80px;
        }

        .info-cell.value {
            color: #333;
            padding-right: 20px;
        }

        /* Stats Bar */
        .stats {
            background: #1a1a2e;
            color: white;
            padding: 8px 12px;
            text-align: center;
            margin-bottom: 12px;
        }

        .stats strong {
            font-size: 12px;
        }

        /* Table */
        .attendees-section h4 {
            font-size: 10px;
            color: #1a1a2e;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 2px solid #1f8fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        th {
            background: #1f8fff;
            color: white;
            padding: 6px 5px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 5px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        tr:nth-child(even) {
            background: #fafbfc;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
        }

        .badge-green {
            background: #28a745;
            color: white;
        }

        .badge-blue {
            background: #17a2b8;
            color: white;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
            background: #f8f9fa;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 5mm;
            left: 10mm;
            right: 10mm;
            text-align: center;
            font-size: 7px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $event->title }}</h1>
        <div class="report-title">Registration Report | {{ now()->format('F d, Y') }}</div>
    </div>

    <!-- Event Info -->
    <div class="event-info">
        <h4>Event Details</h4>
        <div class="info-grid">
            <div class="info-row-table">
                <div class="info-cell label">Type:</div>
                <div class="info-cell value">{{ ucfirst($event->type) }}</div>
                <div class="info-cell label">Mode:</div>
                <div class="info-cell value">{{ ucfirst($event->event_mode) }}</div>
            </div>
            <div class="info-row-table">
                <div class="info-cell label">Date:</div>
                <div class="info-cell value">{{ $event->start_date?->format('M d, Y') ?? 'TBD' }}</div>
                <div class="info-cell label">Duration:</div>
                <div class="info-cell value">{{ $event->duration ?? 'N/A' }}</div>
            </div>
            <div class="info-row-table">
                <div class="info-cell label">Location:</div>
                <div class="info-cell value">{{ $event->location ?? 'Online' }}</div>
                <div class="info-cell label">Capacity:</div>
                <div class="info-cell value">{{ $actualCount }} / {{ $event->max_attendees ?? 'Unlimited' }}</div>
            </div>
            @if($event->presenter)
            <div class="info-row-table">
                <div class="info-cell label">Presenter:</div>
                <div class="info-cell value">{{ $event->presenter }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="stats">
        <strong>Total Registered Attendees: {{ $actualCount }}</strong>
    </div>

    <!-- Attendees Table -->
    <div class="attendees-section">
        <h4>Registered Attendees</h4>

        @if($registrations->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 22%;">Name</th>
                    <th style="width: 26%;">Email</th>
                    <th style="width: 14%;">Phone</th>
                    <th style="width: 12%;">Role</th>
                    <th style="width: 14%;">Registered</th>
                    <th style="width: 7%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registrations as $index => $registration)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $registration->user->name ?? 'N/A' }}</td>
                    <td>{{ $registration->user->email ?? 'N/A' }}</td>
                    <td>{{ $registration->user->phone ?? '-' }}</td>
                    <td style="text-align: center;"><span class="badge badge-blue">{{ ucfirst($registration->user->role ?? 'User') }}</span></td>
                    <td>{{ $registration->created_at?->format('M d, Y') ?? '-' }}</td>
                    <td style="text-align: center;"><span class="badge badge-green">{{ ucfirst($registration->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">No registrations found for this event.</div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        Generated by Edvora Admin System | {{ $generatedAt }}
    </div>
</body>
</html>
