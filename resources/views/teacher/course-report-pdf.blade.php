<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Course Report – {{ $course->title }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; background: #fff; }

    /* ── Header ── */
    .header {
        background: #1e40af;
        padding: 28px 36px 24px;
        color: #fff;
    }
    .brand { font-size: 18px; font-weight: 700; letter-spacing: 1px; }
    .report-label { font-size: 9px; text-transform: uppercase; letter-spacing: 2px; opacity: .7; text-align: right; }
    .report-date  { font-size: 10px; opacity: .8; text-align: right; margin-top: 2px; }
    .course-title { font-size: 22px; font-weight: 700; margin-top: 14px; line-height: 1.3; }
    .course-meta-chip { background: rgba(255,255,255,.2); border-radius: 20px; padding: 3px 10px; font-size: 9px; margin-right: 6px; }

    /* ── Page ── */
    .page { padding: 28px 36px; }

    /* ── Section ── */
    .section { margin-bottom: 24px; }
    .section-title {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.2px; color: #4f46e5;
        border-bottom: 2px solid #4f46e5;
        padding-bottom: 5px; margin-bottom: 12px;
    }

    /* ── Info Grid ── */
    .info-grid { width: 100%; border-collapse: collapse; }
    .info-grid td { padding: 5px 8px; vertical-align: top; font-size: 9.5px; }
    .info-grid .lbl { color: #64748b; font-weight: 700; width: 140px; }
    .info-grid .val { color: #1e293b; }
    .info-grid tr:nth-child(even) td { background: #f8fafc; }

    /* ── Stat Boxes ── */
    .stat-box {
        border-radius: 8px; padding: 12px 14px;
        text-align: center; width: 100%;
    }
    .stat-box .stat-num { font-size: 18px; font-weight: 700; line-height: 1.2; }
    .stat-box .stat-lbl { font-size: 8px; text-transform: uppercase; letter-spacing: .8px; margin-top: 3px; opacity: .75; }
    .stat-blue   { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .stat-green  { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .stat-orange { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
    .stat-purple { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
    .stat-cyan   { background: #cffafe; color: #0e7490; border: 1px solid #a5f3fc; }

    /* ── Table ── */
    .data-table { width: 100%; border-collapse: collapse; font-size: 9px; }
    .data-table th {
        background: #f1f5f9; color: #475569;
        text-transform: uppercase; letter-spacing: .6px;
        padding: 6px 8px; text-align: left; font-size: 8px;
        border-bottom: 1px solid #e2e8f0;
    }
    .data-table td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:nth-child(even) td { background: #fafbfc; }

    /* ── Badge ── */
    .badge {
        display: inline-block; border-radius: 20px;
        padding: 2px 8px; font-size: 8px; font-weight: 700;
    }
    .badge-green  { background: #dcfce7; color: #16a34a; }
    .badge-blue   { background: #dbeafe; color: #1d4ed8; }
    .badge-orange { background: #ffedd5; color: #c2410c; }
    .badge-gray   { background: #f1f5f9; color: #64748b; }
    .badge-purple { background: #ede9fe; color: #7c3aed; }

    /* ── Progress Bar ── */
    .prog-wrap { background: #e2e8f0; border-radius: 20px; height: 6px; width: 80px; display: inline-block; vertical-align: middle; }
    .prog-fill  { height: 6px; border-radius: 20px; background: #4f46e5; }

    /* ── Footer ── */
    .footer {
        margin-top: 30px;
        border-top: 1px solid #e2e8f0;
        padding-top: 10px;
        font-size: 8px; color: #94a3b8;
        width: 100%;
    }
    .empty-note { color: #94a3b8; font-style: italic; font-size: 9px; padding: 10px 0; }

    /* ── Days badges ── */
    .day-chip {
        display: inline-block; background: #dbeafe; color: #1d4ed8;
        border-radius: 20px; padding: 2px 7px; font-size: 7.5px;
        font-weight: 600; margin: 1px;
    }
</style>
</head>
<body>

{{-- ══ HEADER ══ --}}
<div class="header">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td><div class="brand">EDVORA</div></td>
            <td align="right">
                <div class="report-label">Course Report</div>
                <div class="report-date">Generated: {{ now()->format('M d, Y') }}</div>
            </td>
        </tr>
    </table>
    <div class="course-title">{{ $course->title }}</div>
    <div style="margin-top:8px;">
        <span class="course-meta-chip">{{ $course->category->name ?? '—' }}</span>
        <span class="course-meta-chip">{{ ucfirst($course->level ?? '—') }}</span>
        <span class="course-meta-chip">{{ $course->duration_hours ?? 0 }} hrs total</span>
        <span class="course-meta-chip">{{ ucfirst($course->status) }}</span>
    </div>
</div>

<div class="page">

    {{-- ══ STAT SUMMARY ══ --}}
    <div class="section">
        <div class="section-title">Overview</div>
        <table width="100%" cellpadding="0" cellspacing="6" style="margin-bottom:16px;">
            <tr>
                <td width="19%">
                    <div class="stat-box stat-blue">
                        <div class="stat-num">{{ $course->lessons->count() }}</div>
                        <div class="stat-lbl">Lessons</div>
                    </div>
                </td>
                <td width="2%"></td>
                <td width="19%">
                    <div class="stat-box stat-green">
                        <div class="stat-num">{{ $enrollments->count() }}</div>
                        <div class="stat-lbl">Students</div>
                    </div>
                </td>
                <td width="2%"></td>
                <td width="19%">
                    <div class="stat-box stat-purple">
                        <div class="stat-num">@php $h=floor($totalSessionMinutes/60);$m=$totalSessionMinutes%60;echo $h.'h '.$m.'m'; @endphp</div>
                        <div class="stat-lbl">Hours Done</div>
                    </div>
                </td>
                <td width="2%"></td>
                <td width="19%">
                    <div class="stat-box stat-orange">
                        <div class="stat-num">@php $rh=floor($remainingMinutes/60);$rm=$remainingMinutes%60;echo $rh.'h '.$rm.'m'; @endphp</div>
                        <div class="stat-lbl">Hours Left</div>
                    </div>
                </td>
                <td width="2%"></td>
                <td width="19%">
                    <div class="stat-box stat-cyan">
                        <div class="stat-num">{{ $avgProgress }}%</div>
                        <div class="stat-lbl">Avg. Progress</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ══ COURSE INFO ══ --}}
    <div class="section">
        <div class="section-title">Course Details</div>
        <table class="info-grid">
            <tr><td class="lbl">Title</td><td class="val">{{ $course->title }}</td></tr>
            <tr><td class="lbl">Category</td><td class="val">{{ $course->category->name ?? '—' }}</td></tr>
            <tr><td class="lbl">Level</td><td class="val">{{ ucfirst($course->level ?? '—') }}</td></tr>
            <tr><td class="lbl">Total Duration</td><td class="val">{{ $course->duration_hours ?? 0 }} hours</td></tr>
            <tr><td class="lbl">Status</td><td class="val">{{ ucfirst($course->status) }}</td></tr>
            @if($course->primary_class_start)
            <tr>
                <td class="lbl">Primary Class Time</td>
                <td class="val">
                    {{ substr($course->primary_class_start,0,5) }} – {{ substr($course->primary_class_end,0,5) }}
                    @if($course->primary_class_days)
                        &nbsp;
                        @foreach($course->primary_class_days as $d)
                            <span class="day-chip">{{ ucfirst($d) }}</span>
                        @endforeach
                    @endif
                </td>
            </tr>
            @endif
            @if($course->secondary_class_start)
            <tr>
                <td class="lbl">Backup Class Time</td>
                <td class="val">
                    {{ substr($course->secondary_class_start,0,5) }} – {{ substr($course->secondary_class_end,0,5) }}
                    @if($course->secondary_class_days)
                        &nbsp;
                        @foreach($course->secondary_class_days as $d)
                            <span class="day-chip">{{ ucfirst($d) }}</span>
                        @endforeach
                    @endif
                </td>
            </tr>
            @endif
            @if($course->description)
            <tr><td class="lbl">Description</td><td class="val">{{ strip_tags($course->description) }}</td></tr>
            @endif
        </table>
    </div>

    {{-- ══ TEACHER INFO ══ --}}
    <div class="section">
        <div class="section-title">Instructor</div>
        <table class="info-grid">
            <tr><td class="lbl">Name</td><td class="val">{{ $user->name }}</td></tr>
            <tr><td class="lbl">Email</td><td class="val">{{ $user->email }}</td></tr>
            @if($teacher)
                @if(!empty($teacher->specialization))
                <tr><td class="lbl">Specialization</td><td class="val">{{ $teacher->specialization }}</td></tr>
                @endif
                @if(!empty($teacher->expertise))
                <tr><td class="lbl">Expertise</td><td class="val">{{ $teacher->expertise }}</td></tr>
                @endif
                @if(!empty($teacher->years_of_experience))
                <tr><td class="lbl">Experience</td><td class="val">{{ $teacher->years_of_experience }} years</td></tr>
                @endif
                @if(!empty($teacher->linkedin))
                <tr><td class="lbl">LinkedIn</td><td class="val">{{ $teacher->linkedin }}</td></tr>
                @endif
                <tr><td class="lbl">Verified</td><td class="val">{{ $teacher->is_verified ? 'Yes' : 'No' }}</td></tr>
            @endif
        </table>
    </div>

    {{-- ══ LESSONS ══ --}}
    <div class="section">
        <div class="section-title">Lessons ({{ $course->lessons->count() }})</div>
        @if($course->lessons->count())
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Duration</th>
                    <th>Recording</th>
                </tr>
            </thead>
            <tbody>
                @foreach($course->lessons as $l)
                <tr>
                    <td>{{ $l->order ?: $loop->iteration }}</td>
                    <td>{{ $l->title }}</td>
                    <td>{{ $l->duration_minutes ? $l->duration_minutes . ' min' : '—' }}</td>
                    <td>
                        @if($l->video_url)
                            <span class="badge badge-green">Available</span>
                        @else
                            <span class="badge badge-gray">None</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-note">No lessons added yet.</div>
        @endif
    </div>

    {{-- ══ STUDENTS ══ --}}
    <div class="section">
        <div class="section-title">Enrolled Students ({{ $enrollments->count() }})</div>
        @if($enrollments->count())
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Enrolled</th>
                    <th>Status</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollments as $i => $e)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $e->name }}</td>
                    <td>{{ $e->email }}</td>
                    <td>{{ \Carbon\Carbon::parse($e->enrolled_at)->format('M d, Y') }}</td>
                    <td>
                        @if($e->status === 'active')
                            <span class="badge badge-blue">Active</span>
                        @elseif($e->status === 'completed')
                            <span class="badge badge-green">Completed</span>
                        @else
                            <span class="badge badge-gray">{{ ucfirst($e->status) }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="prog-wrap">
                            <div class="prog-fill" style="width: {{ $e->progress_percentage }}%;"></div>
                        </div>
                        <span style="margin-left:5px;">{{ $e->progress_percentage }}%</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-note">No students enrolled yet.</div>
        @endif
    </div>

    {{-- ══ SESSION HISTORY ══ --}}
    <div class="section">
        <div class="section-title">Class Session History ({{ $pastSessions->count() }})</div>
        @if($pastSessions->count())
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Duration</th>
                    <th>Attendees</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pastSessions as $i => $s)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $s->started_at->format('M d, Y') }}</td>
                    <td>{{ $s->started_at->format('H:i') }}</td>
                    <td>{{ $s->ended_at ? $s->ended_at->format('H:i') : '—' }}</td>
                    <td><span class="badge badge-purple">{{ $s->duration }}</span></td>
                    <td>{{ $s->attendees_count }}</td>
                    <td>{{ $s->note ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-note">No class sessions recorded yet.</div>
        @endif
    </div>

    {{-- ══ DOCUMENTS ══ --}}
    <div class="section">
        <div class="section-title">Uploaded Documents ({{ $documents->count() }})</div>
        @if($documents->count())
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Lesson</th>
                    <th>Size</th>
                    <th>Uploaded</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $i => $doc)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $doc->title }}</td>
                    <td>{{ $doc->lesson ? $doc->lesson->title : 'General' }}</td>
                    <td>{{ $doc->file_size_formatted }}</td>
                    <td>{{ $doc->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-note">No documents uploaded yet.</div>
        @endif
    </div>

    {{-- ══ FOOTER ══ --}}
    <table width="100%" style="margin-top:30px;border-top:1px solid #e2e8f0;padding-top:10px;font-size:8px;color:#94a3b8;">
        <tr>
            <td>Edvora Learning Platform – Confidential</td>
            <td align="right">{{ $course->title }} | Report by {{ $user->name }}</td>
        </tr>
    </table>

</div>
</body>
</html>
