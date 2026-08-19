<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Attendance Report – {{ $course->title }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1e293b; background: #fff; }

    /* ── Header ── */
    .header {
        background: linear-gradient(135deg, #1e3a5f 0%, #1a2a4a 100%);
        padding: 22px 30px 18px;
        color: #fff;
    }
    .brand        { font-size: 15px; font-weight: 700; letter-spacing: 1.5px; color: #6ab4ff; }
    .report-type  { font-size: 8px; text-transform: uppercase; letter-spacing: 2px; opacity: .7; text-align: right; margin-bottom: 2px; }
    .report-date  { font-size: 8.5px; opacity: .75; text-align: right; }
    .course-title { font-size: 18px; font-weight: 700; margin-top: 10px; line-height: 1.3; }
    .meta-chip    { background: rgba(255,255,255,.15); border-radius: 20px; padding: 2px 9px; font-size: 7.5px; margin-right: 5px; }

    /* ── Summary Bar ── */
    .summary-bar {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 30px;
    }
    .s-box { display: inline-block; text-align: center; padding: 6px 14px; border-radius: 8px; margin-right: 8px; }
    .s-num { font-size: 16px; font-weight: 700; line-height: 1.1; }
    .s-lbl { font-size: 7px; text-transform: uppercase; letter-spacing: .8px; opacity: .7; margin-top: 2px; }
    .sb-blue   { background: #dbeafe; color: #1d4ed8; }
    .sb-purple { background: #ede9fe; color: #6d28d9; }
    .sb-green  { background: #dcfce7; color: #15803d; }
    .sb-orange { background: #ffedd5; color: #c2410c; }
    .sb-red    { background: #fee2e2; color: #b91c1c; }

    /* ── Page ── */
    .page { padding: 18px 30px; }

    /* ── Section ── */
    .section { margin-bottom: 20px; }
    .section-title {
        font-size: 9px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1px; color: #4f46e5;
        border-bottom: 2px solid #4f46e5;
        padding-bottom: 4px; margin-bottom: 10px;
    }

    /* ── Summary Table ── */
    .sum-table { width: 100%; border-collapse: collapse; font-size: 8.5px; margin-bottom: 20px; }
    .sum-table th {
        background: #1e3a5f; color: #fff;
        padding: 7px 8px; text-align: left;
        font-size: 7.5px; text-transform: uppercase; letter-spacing: .5px;
        font-weight: 700;
    }
    .sum-table th:first-child { border-radius: 6px 0 0 0; }
    .sum-table th:last-child  { border-radius: 0 6px 0 0; }
    .sum-table td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .sum-table tr:last-child td { border-bottom: none; }
    .sum-table tr:nth-child(even) td { background: #f8fafc; }
    .sum-table tr:hover td { background: #eff6ff; }

    /* ── Detail Table ── */
    .det-table { width: 100%; border-collapse: collapse; font-size: 8px; }
    .det-table th {
        background: #334155; color: #e2e8f0;
        padding: 6px 6px; text-align: center;
        font-size: 7px; text-transform: uppercase; letter-spacing: .4px;
    }
    .det-table th.name-col { text-align: left; width: 160px; }
    .det-table td { padding: 5px 6px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; text-align: center; }
    .det-table td.name-col { text-align: left; font-weight: 600; color: #1e293b; }
    .det-table tr:nth-child(even) td { background: #fafbfc; }

    /* ── Status Symbols ── */
    .ic-present { color: #15803d; font-weight: 700; font-size: 9px; }
    .ic-late    { color: #92400e; font-weight: 700; font-size: 9px; }
    .ic-absent  { color: #b91c1c; font-weight: 700; font-size: 9px; }

    /* ── Badge ── */
    .badge { display: inline-block; border-radius: 20px; padding: 2px 7px; font-size: 7.5px; font-weight: 700; }
    .b-green  { background: #dcfce7; color: #16a34a; }
    .b-yellow { background: #fef9c3; color: #92400e; }
    .b-red    { background: #fee2e2; color: #b91c1c; }
    .b-blue   { background: #dbeafe; color: #1d4ed8; }
    .b-gray   { background: #f1f5f9; color: #64748b; }
    .b-purple { background: #ede9fe; color: #7c3aed; }

    /* ── Progress Bar ── */
    .prog-wrap { background: #e2e8f0; border-radius: 20px; height: 6px; width: 60px; display: inline-block; vertical-align: middle; }
    .prog-fill  { height: 6px; border-radius: 20px; }
    .prog-good  { background: #16a34a; }
    .prog-warn  { background: #f59e0b; }
    .prog-risk  { background: #dc2626; }

    /* ── Session Header ── */
    .sess-hdr {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 8px;
        font-size: 8.5px;
    }
    .sess-hdr-title { font-weight: 700; color: #1e293b; font-size: 10px; }
    .sess-meta { color: #64748b; margin-top: 2px; }

    /* ── Legend ── */
    .legend { font-size: 8px; color: #64748b; margin-bottom: 10px; }
    .legend span { margin-right: 14px; }

    /* ── Footer ── */
    .footer-row { margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 7.5px; color: #94a3b8; }

    /* ── Page break ── */
    .page-break { page-break-after: always; }
</style>
</head>
<body>

@php
    $totalSessions = $pastSessions->count();
    $perfectCount  = $attendanceSummary->where('rate', 100)->count();
    $riskCount     = $attendanceSummary->where('rate', '<', 60)->count();
@endphp

{{-- ══ HEADER ══ --}}
<div class="header">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td><div class="brand">EDVORA</div></td>
            <td align="right">
                <div class="report-type">Attendance Report</div>
                <div class="report-date">Generated: {{ now()->format('l, M d Y – H:i') }}</div>
            </td>
        </tr>
    </table>
    <div class="course-title">{{ $course->title }}</div>
    <div style="margin-top:7px;">
        <span class="meta-chip">{{ $course->category->name ?? '—' }}</span>
        <span class="meta-chip">{{ ucfirst($course->level ?? '—') }}</span>
        <span class="meta-chip">Instructor: {{ $user->name }}</span>
    </div>
</div>

{{-- ══ SUMMARY BAR ══ --}}
<div class="summary-bar">
    <table cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="s-box sb-blue">
                    <div class="s-num">{{ $enrollments->count() }}</div>
                    <div class="s-lbl">Students</div>
                </div>
            </td>
            <td>
                <div class="s-box sb-purple">
                    <div class="s-num">{{ $totalSessions }}</div>
                    <div class="s-lbl">Sessions</div>
                </div>
            </td>
            <td>
                <div class="s-box sb-green">
                    <div class="s-num">{{ $overallRate }}%</div>
                    <div class="s-lbl">Avg. Attendance</div>
                </div>
            </td>
            <td>
                <div class="s-box sb-orange">
                    <div class="s-num">{{ $perfectCount }}</div>
                    <div class="s-lbl">Perfect Record</div>
                </div>
            </td>
            <td>
                <div class="s-box sb-red">
                    <div class="s-num">{{ $riskCount }}</div>
                    <div class="s-lbl">At Risk (&lt;60%)</div>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="page">

    {{-- ══ PAGE 1: STUDENT SUMMARY ══ --}}
    <div class="section">
        <div class="section-title">Student Attendance Summary</div>

        @if($attendanceSummary->isEmpty())
        <div style="color:#94a3b8;font-style:italic;font-size:9px;padding:12px 0;">No attendance data recorded yet.</div>
        @else
        <table class="sum-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="24%">Student Name</th>
                    <th width="22%">Email</th>
                    <th width="8%" style="text-align:center;">Sessions</th>
                    <th width="8%" style="text-align:center;">Present</th>
                    <th width="8%" style="text-align:center;">Late</th>
                    <th width="8%" style="text-align:center;">Absent</th>
                    <th width="10%" style="text-align:center;">Rate</th>
                    <th width="7%" style="text-align:center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceSummary as $i => $a)
                @php
                    $rateColor = $a->rate >= 80 ? '#16a34a' : ($a->rate >= 60 ? '#f59e0b' : '#dc2626');
                    $progClass = $a->rate >= 80 ? 'prog-good' : ($a->rate >= 60 ? 'prog-warn' : 'prog-risk');
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:700;color:#1e293b;">{{ $a->name }}</td>
                    <td style="color:#64748b;">{{ $a->email }}</td>
                    <td style="text-align:center;font-weight:600;">{{ $a->total }}</td>
                    <td style="text-align:center;">
                        <span class="badge b-green">{{ $a->present }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="badge b-yellow">{{ $a->late }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="badge b-red">{{ $a->absent }}</span>
                    </td>
                    <td style="text-align:center;">
                        <div class="prog-wrap">
                            <div class="prog-fill {{ $progClass }}" style="width:{{ $a->rate }}%;"></div>
                        </div>
                        <span style="margin-left:4px;font-weight:700;color:{{ $rateColor }};">{{ $a->rate }}%</span>
                    </td>
                    <td style="text-align:center;">
                        @if($a->total === 0)
                            <span class="badge b-gray">—</span>
                        @elseif($a->rate >= 80)
                            <span class="badge b-green">Good</span>
                        @elseif($a->rate >= 60)
                            <span class="badge b-yellow">Warning</span>
                        @else
                            <span class="badge b-red">At Risk</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- ══ PAGE 2: PER-SESSION DETAIL ══ --}}
    @if($pastSessions->isNotEmpty() && $attendanceSummary->isNotEmpty())
    <div class="page-break"></div>

    <div class="section">
        <div class="section-title">Detailed Attendance Per Session</div>

        <div class="legend">
            <span><strong style="color:#15803d;">P</strong> = Present</span>
            <span><strong style="color:#92400e;">L</strong> = Late</span>
            <span><strong style="color:#b91c1c;">A</strong> = Absent</span>
            <span><strong style="color:#64748b;">—</strong> = Not Recorded</span>
        </div>

        <table class="det-table">
            <thead>
                <tr>
                    <th class="name-col">#&nbsp;&nbsp;Student</th>
                    @foreach($pastSessions as $si => $s)
                    <th title="{{ $s->started_at->format('M d Y H:i') }}">
                        S{{ $si + 1 }}<br>
                        <span style="font-size:6.5px;opacity:.8;">{{ $s->started_at->format('M d') }}</span>
                    </th>
                    @endforeach
                    <th>Present</th>
                    <th>Late</th>
                    <th>Absent</th>
                    <th>Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceSummary as $i => $a)
                @php
                    $rateColor = $a->rate >= 80 ? '#16a34a' : ($a->rate >= 60 ? '#f59e0b' : '#dc2626');
                @endphp
                <tr>
                    <td class="name-col">{{ $i + 1 }}&nbsp;&nbsp;{{ $a->name }}</td>
                    @foreach($pastSessions as $s)
                    @php
                        $status = collect($a->perSession)->firstWhere('session_id', $s->id)['status'] ?? 'absent';
                    @endphp
                    <td>
                        @if($status === 'present')
                            <span class="ic-present">P</span>
                        @elseif($status === 'late')
                            <span class="ic-late">L</span>
                        @else
                            <span class="ic-absent">A</span>
                        @endif
                    </td>
                    @endforeach
                    <td><span class="badge b-green">{{ $a->present }}</span></td>
                    <td><span class="badge b-yellow">{{ $a->late }}</span></td>
                    <td><span class="badge b-red">{{ $a->absent }}</span></td>
                    <td style="font-weight:700;color:{{ $rateColor }};">{{ $a->rate }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ══ PAGE 3: SESSION LOG ══ --}}
    <div class="page-break"></div>

    <div class="section">
        <div class="section-title">Session Log ({{ $totalSessions }} Sessions)</div>

        @foreach($pastSessions as $si => $s)
        @php
            $sessionStudents = $attendanceSummary->map(fn($a) => (object)[
                'name'   => $a->name,
                'email'  => $a->email,
                'status' => collect($a->perSession)->firstWhere('session_id', $s->id)['status'] ?? 'absent',
            ])->sortBy('name');
            $sPresent = $sessionStudents->where('status', 'present')->count();
            $sLate    = $sessionStudents->where('status', 'late')->count();
            $sAbsent  = $sessionStudents->where('status', 'absent')->count();
            $sRate    = $attendanceSummary->count() > 0
                ? round((($sPresent + $sLate) / $attendanceSummary->count()) * 100)
                : 0;
        @endphp

        <div class="sess-hdr">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <div class="sess-hdr-title">
                            Session #{{ $si + 1 }} &nbsp;—&nbsp; {{ $s->started_at->format('l, M d Y') }}
                        </div>
                        <div class="sess-meta">
                            {{ $s->started_at->format('H:i') }}
                            @if($s->ended_at) – {{ $s->ended_at->format('H:i') }} @endif
                            &nbsp;·&nbsp; Duration: {{ $s->duration }}
                            @if($s->note) &nbsp;·&nbsp; Note: {{ $s->note }} @endif
                        </div>
                    </td>
                    <td align="right" style="white-space:nowrap;">
                        <span class="badge b-green">{{ $sPresent }} Present</span>&nbsp;
                        <span class="badge b-yellow">{{ $sLate }} Late</span>&nbsp;
                        <span class="badge b-red">{{ $sAbsent }} Absent</span>&nbsp;
                        <span class="badge b-blue">{{ $sRate }}% Rate</span>
                    </td>
                </tr>
            </table>
        </div>

        <table class="sum-table" style="margin-bottom:16px;">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="40%">Student Name</th>
                    <th width="35%">Email</th>
                    <th width="20%" style="text-align:center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessionStudents as $si2 => $st)
                <tr>
                    <td>{{ $si2 + 1 }}</td>
                    <td style="font-weight:600;color:#1e293b;">{{ $st->name }}</td>
                    <td style="color:#64748b;">{{ $st->email }}</td>
                    <td style="text-align:center;">
                        @if($st->status === 'present')
                            <span class="badge b-green">✓ Present</span>
                        @elseif($st->status === 'late')
                            <span class="badge b-yellow">⏰ Late</span>
                        @else
                            <span class="badge b-red">✗ Absent</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @endforeach
    </div>
    @endif

    {{-- ══ FOOTER ══ --}}
    <table width="100%" class="footer-row" cellpadding="0" cellspacing="0">
        <tr>
            <td>Edvora Learning Platform – Confidential</td>
            <td align="right">{{ $course->title }} &nbsp;|&nbsp; Attendance Report &nbsp;|&nbsp; By {{ $user->name }}</td>
        </tr>
    </table>

</div>
</body>
</html>
