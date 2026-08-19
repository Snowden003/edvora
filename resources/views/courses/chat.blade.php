@extends('layouts.app')

@section('title', $course->title . ' — Live Chat | Edvora')
@section('meta_description', 'Join the live classroom chat for ' . $course->title . ' on Edvora.')
@section('canonical', route('courses.chat.show', $course))
@section('body-class', 'live-chat-page')

@push('styles')
<style>
:root {
    --lp-bg: #0b0f19;
    --lp-surface: rgba(255, 255, 255, 0.045);
    --lp-surface-strong: rgba(255, 255, 255, 0.08);
    --lp-border: rgba(255, 255, 255, 0.10);
    --lp-text: #f8fafc;
    --lp-muted: #94a3b8;
    --lp-primary: #6366f1;
    --lp-primary-2: #8b5cf6;
    --lp-accent: #22d3ee;
    --lp-success: #34d399;
}

body.live-chat-page {
    background:
        radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.22) 0%, transparent 35%),
        radial-gradient(circle at 90% 80%, rgba(34, 211, 238, 0.14) 0%, transparent 35%),
        linear-gradient(180deg, #0b0f19 0%, #0f172a 100%);
    color: var(--lp-text);
    min-height: 100vh;
}

.live-chat-hero {
    padding: 28px 32px 18px;
    border-bottom: 1px solid var(--lp-border);
    background: rgba(15, 23, 42, 0.42);
    backdrop-filter: blur(14px);
    position: sticky;
    top: 0;
    z-index: 50;
}

.live-chat-hero__inner {
    max-width: 1280px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.live-chat-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--lp-muted);
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    padding: 8px 14px;
    border: 1px solid var(--lp-border);
    border-radius: 999px;
    background: var(--lp-surface);
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.live-chat-back:hover {
    color: var(--lp-text);
    background: var(--lp-surface-strong);
    border-color: rgba(255, 255, 255, 0.2);
}

.live-chat-title {
    display: flex;
    align-items: center;
    gap: 16px;
    min-width: 0;
}

.live-chat-title__avatar {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    object-fit: cover;
    border: 1px solid var(--lp-border);
    box-shadow: 0 8px 30px rgba(99, 102, 241, 0.25);
    flex-shrink: 0;
}

.live-chat-title__text {
    min-width: 0;
}

.live-chat-title__text h1 {
    font-size: 1.35rem;
    font-weight: 700;
    margin: 0 0 4px;
    color: var(--lp-text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.live-chat-title__text p {
    margin: 0;
    font-size: 0.82rem;
    color: var(--lp-muted);
}

.live-chat-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--lp-success);
    padding: 7px 14px;
    border-radius: 999px;
    background: rgba(52, 211, 153, 0.12);
    border: 1px solid rgba(52, 211, 153, 0.25);
    flex-shrink: 0;
}

.live-chat-status::before {
    content: '';
    width: 8px;
    height: 8px;
    background: var(--lp-success);
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.25);
    animation: live-pulse 2s infinite;
}

@keyframes live-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.15); }
}

.live-chat-shell {
    max-width: 1280px;
    margin: 0 auto;
    padding: 24px 32px 32px;
    height: calc(100vh - 116px);
}

.live-chat-card {
    display: flex;
    height: 100%;
    border-radius: 24px;
    overflow: hidden;
    background: rgba(15, 23, 42, 0.55);
    border: 1px solid var(--lp-border);
    box-shadow:
        0 24px 80px rgba(0, 0, 0, 0.45),
        inset 0 1px 0 rgba(255, 255, 255, 0.06);
    backdrop-filter: blur(20px);
}

.live-chat-card .course-chat {
    display: flex;
    flex-direction: column;
    flex: 1;
    background: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}

.live-chat-card .course-chat__header {
    background: transparent !important;
    border-bottom: 1px solid var(--lp-border);
    padding: 20px 24px;
}

.live-chat-card .course-chat__header h5 {
    color: var(--lp-text);
    font-size: 1rem;
    font-weight: 700;
}

.live-chat-card .course-chat__header p {
    color: var(--lp-muted);
}

.live-chat-card .course-chat__online {
    background: var(--lp-surface);
    border-color: var(--lp-border);
    color: var(--lp-muted);
}

.live-chat-card .course-chat__body {
    flex: 1;
    min-height: 0;
}

.live-chat-card .course-chat__members {
    width: 240px;
    background: rgba(255, 255, 255, 0.02);
    border-right: 1px solid var(--lp-border);
    padding: 20px;
}

.live-chat-card .course-chat__members-title {
    color: var(--lp-muted);
    font-size: 0.7rem;
    letter-spacing: 0.1em;
    margin-bottom: 14px;
}

.live-chat-card .course-chat__member {
    padding: 9px 10px;
    border-radius: 12px;
    transition: background 0.15s ease;
}

.live-chat-card .course-chat__member:hover {
    background: var(--lp-surface);
}

.live-chat-card .course-chat__member-name {
    color: var(--lp-text);
    font-weight: 600;
}

.live-chat-card .course-chat__badge--teacher {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.25), rgba(139, 92, 246, 0.25));
    color: #c4b5fd;
    border: 1px solid rgba(139, 92, 246, 0.35);
}

.live-chat-card .course-chat__badge--online {
    background: rgba(52, 211, 153, 0.12);
    color: var(--lp-success);
}

.live-chat-card .course-chat__conversation {
    background: transparent;
}

.live-chat-card .course-chat__status {
    color: var(--lp-muted);
    border-bottom: 1px solid var(--lp-border);
    background: rgba(255, 255, 255, 0.02);
}

.live-chat-card .course-chat__messages {
    height: auto;
    flex: 1;
    background: transparent;
}

.live-chat-card .course-chat__empty {
    color: var(--lp-muted);
}

.live-chat-card .course-chat__message--mine .course-chat__name {
    color: #a5b4fc;
}

.live-chat-card .course-chat__name {
    color: var(--lp-accent);
}

.live-chat-card .course-chat__bubble {
    background: var(--lp-surface-strong);
    color: var(--lp-text);
    border: 1px solid var(--lp-border);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.live-chat-card .course-chat__message--mine .course-chat__bubble {
    background: linear-gradient(135deg, var(--lp-primary), var(--lp-primary-2));
    border-color: transparent;
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.35);
}

.live-chat-card .course-chat__meta time {
    color: var(--lp-muted);
}

.live-chat-card .course-chat__form {
    background: rgba(255, 255, 255, 0.02);
    border-top: 1px solid var(--lp-border);
    padding: 16px 22px;
    gap: 12px;
}

.live-chat-card .course-chat__form textarea {
    background: var(--lp-surface);
    border-color: var(--lp-border);
    color: var(--lp-text);
    border-radius: 14px;
    font-size: 0.92rem;
}

.live-chat-card .course-chat__form textarea::placeholder {
    color: var(--lp-muted);
}

.live-chat-card .course-chat__form textarea:focus {
    border-color: var(--lp-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18);
}

.live-chat-card .course-chat__form button {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--lp-primary), var(--lp-primary-2));
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.live-chat-card .course-chat__form button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
}

@media (max-width: 860px) {
    .live-chat-hero__inner {
        flex-wrap: wrap;
    }

    .live-chat-title__text h1 {
        font-size: 1.1rem;
    }

    .live-chat-status {
        display: none;
    }

    .live-chat-shell {
        padding: 16px;
        height: calc(100vh - 106px);
    }

    .live-chat-card .course-chat__members {
        display: none;
    }
}
</style>
@endpush

@section('content')
<section class="live-chat-hero">
    <div class="live-chat-hero__inner">
        <a href="{{ $user->role === 'teacher' ? route('teacher.courses.detail', $course->id) : route('student.courses.learn', $course->slug) }}" class="live-chat-back">
            <i class="bi bi-arrow-left"></i>
            Back to course
        </a>

        <div class="live-chat-title">
            <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : asset('assets/images/logo1.jpg') }}"
                 alt="{{ $course->title }}" class="live-chat-title__avatar">
            <div class="live-chat-title__text">
                <h1>{{ $course->title }}</h1>
                <p>Live classroom conversation</p>
            </div>
        </div>

        <span class="live-chat-status">Live</span>
    </div>
</section>

<div class="live-chat-shell">
    <div class="live-chat-card">
        <x-course-chat :course="$course" :user="$user" />
    </div>
</div>
@endsection
