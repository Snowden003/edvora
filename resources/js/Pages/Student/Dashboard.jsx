import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import StudentLayout from '@/Layouts/StudentLayout';
import WeeklyActivityChart from '@/Components/Student/WeeklyActivityChart';
import CourseStatusDonut from '@/Components/Student/CourseStatusDonut';
import { useLanguage } from '@/Context/LanguageContext';
import {
    BookOpen,
    CheckCircle,
    ClipboardCheck,
    Award,
    Zap,
    Flame,
    PlayCircle,
    MessageSquare,
    Trophy,
    Calendar,
    Bell,
    ArrowRight,
    ArrowLeft,
    Sparkles,
    GraduationCap,
    Clock,
    AlertOctagon,
    Crown,
    Star,
    Compass
} from 'lucide-react';

export default function Dashboard({
    stats = {},
    level = { level: 1, title: 'Scholar', progress: 0 },
    enrollments = [],
    bannedEnrollments = [],
    upcomingEvents = [],
    leaderboard = [],
    achievements = [],
    certificates = [],
    completedEnrollments = [],
    weeklyProgress = {},
    courseStats = {},
    examStats = {},
    activities = [],
    notifications = [],
    unreadNotifCount = 0
}) {
    const { auth } = usePage().props;
    const { t, isRtl } = useLanguage();
    const user = auth?.user || {};

    const userAvatar = user.avatar_url || (user.avatar
        ? (user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`)
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name || 'Student')}&background=1F8FFF&color=fff&size=200`);

    const firstActiveCourse = enrollments.length > 0 ? enrollments[0] : null;

    return (
        <StudentLayout title={`${t('dashboard')} - ${t('brand_title')}`}>
            <Head>
                <title>{`${t('dashboard')} - ${t('brand_title')}`}</title>
                <meta
                    name="description"
                    content={isRtl ? "پورتال شاگردان ادورا تک. دسترسی به کورس‌ها، پیگیری پیشرفت درسی و اخذ تصدیق‌نامه‌های معتبر." : "Edvora Tech Student Portal. Access your courses, track learning progress, and earn recognized certificates."}
                />
            </Head>

            {/* Banned Courses Warning Banner (if any) */}
            {bannedEnrollments.length > 0 && (
                <div className="space-y-3 mb-6">
                    {bannedEnrollments.map((banned) => (
                        <div
                            key={banned.id}
                            className="p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-between gap-4 text-rose-800"
                        >
                            <div className="flex items-center gap-3">
                                <div className="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                    <AlertOctagon size={20} />
                                </div>
                                <div>
                                    <h4 className="text-xs font-bold">{t('suspended_title')}</h4>
                                    <p className="text-[11px] text-rose-600">
                                        {t('suspended_desc')} <strong>{banned.course_title}</strong> {t('suspended_by')}
                                    </p>
                                </div>
                            </div>
                            <Link
                                href="/student/courses?status=banned"
                                className="px-3 py-1.5 rounded-xl bg-white border border-rose-300 text-rose-700 text-xs font-bold shrink-0 hover:bg-rose-50 transition-colors"
                            >
                                {t('view_status')}
                            </Link>
                        </div>
                    ))}
                </div>
            )}

            {/* Welcome Banner */}
            <div className="relative rounded-3xl overflow-hidden shadow-2xl bg-gradient-to-br from-[#030712] via-[#09152e] to-[#040e24] dark:from-[#02050f] dark:via-[#071329] dark:to-[#030919] text-white p-5 sm:p-8 lg:p-10 border border-slate-800/80 dark:border-cyan-500/30 dark:shadow-[0_20px_50px_-15px_rgba(0,240,255,0.2)] mb-6 transition-all">
                <div className="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
                    <div className="absolute top-4 left-6 text-cyan-400/15 animate-bounce" style={{ animationDuration: '6s' }}>
                        <GraduationCap size={40} />
                    </div>
                    <div className="absolute bottom-6 left-1/3 text-indigo-400/15 animate-pulse" style={{ animationDuration: '4s' }}>
                        <Sparkles size={42} />
                    </div>
                    <div className="absolute top-8 right-1/4 text-amber-400/15 animate-bounce" style={{ animationDuration: '8s' }}>
                        <Award size={36} />
                    </div>
                    {/* Glowing Aurora Orbs */}
                    <div className="absolute -top-24 -right-24 w-96 h-96 bg-cyan-500/20 dark:bg-cyan-500/25 rounded-full blur-3xl pointer-events-none" />
                    <div className="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-600/20 dark:bg-indigo-600/25 rounded-full blur-3xl pointer-events-none" />
                </div>

                <div className="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6 lg:gap-8">
                    {/* Main Identity & Greeting: Avatar is prominently first/beside name */}
                    <div className="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6 text-center sm:text-start flex-1 min-w-0 w-full">
                        {/* 3D Holographic User Avatar (Never pushed to the bottom!) */}
                        <div className="relative shrink-0 group">
                            <div className="relative w-20 h-20 sm:w-24 sm:h-24 lg:w-28 lg:h-28 rounded-2xl sm:rounded-3xl p-[2.5px] bg-gradient-to-tr from-cyan-400 via-brand-500 to-indigo-500 shadow-[0_0_30px_rgba(0,240,255,0.35)] group-hover:shadow-[0_0_40px_rgba(0,240,255,0.55)] transition-all">
                                <div className="w-full h-full rounded-[14px] sm:rounded-[22px] overflow-hidden bg-slate-950 relative">
                                    <img
                                        src={userAvatar}
                                        alt={user.name || 'Student'}
                                        className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                    />
                                    <div className="absolute inset-0 bg-gradient-to-t from-slate-950/40 via-transparent to-transparent pointer-events-none" />
                                </div>
                                <span className="absolute -bottom-1 -right-1 w-4 h-4 sm:w-5 sm:h-5 bg-emerald-500 border-2 border-slate-900 rounded-full ring-2 ring-emerald-400/60 shadow-[0_0_10px_#10b981] animate-pulse" title={t('active')} />
                            </div>
                        </div>

                        {/* User Greeting & Description */}
                        <div className="space-y-3 flex-1 min-w-0 w-full">
                            <div className="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-400/30 text-xs font-bold text-cyan-300 shadow-[0_0_15px_rgba(0,240,255,0.15)]">
                                    <Sparkles size={13} className="text-cyan-400 animate-pulse" />
                                    <span>{t('level')} {level.level} · {level.title || t('scholar')}</span>
                                </div>
                                {stats.streak > 0 && (
                                    <div className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-xs font-extrabold text-amber-400 shadow-xs">
                                        <span className="anim-flame text-xs">🔥</span>
                                        <span>{stats.streak} {t('days')}</span>
                                    </div>
                                )}
                            </div>

                            <h2 className="text-xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white leading-tight">
                                {t('welcome_back')}{' '}
                                <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-sky-300 to-indigo-300 drop-shadow-[0_0_20px_rgba(0,240,255,0.4)]">
                                    {user.name || t('scholar')}
                                </span> 👋
                            </h2>

                            <p className="text-xs sm:text-sm text-slate-300 leading-relaxed font-medium max-w-xl">
                                {t('hero_desc_start')}{' '}
                                <strong className="text-cyan-300 font-bold">{stats.active_courses || 0} {t('hero_active_courses')}</strong>{' '}
                                {t('hero_and')}{' '}
                                <strong className="text-indigo-300 font-bold">{upcomingEvents.length} {t('hero_upcoming_events')}</strong>{' '}
                                {t('hero_desc_end')}
                            </p>

                            {/* Action Buttons */}
                            <div className="flex flex-wrap items-center justify-center sm:justify-start gap-3 pt-1">
                                {firstActiveCourse ? (
                                    <a
                                        href={`/student/courses/${firstActiveCourse.course.slug}/learn`}
                                        className="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-gradient-to-r from-cyan-500 via-brand-600 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-bold text-xs shadow-[0_0_25px_-3px_rgba(0,240,255,0.5)] transition-all hover:scale-[1.02] active:scale-95"
                                    >
                                        <PlayCircle size={16} />
                                        <span>{t('continue_course')} {firstActiveCourse.course.title}</span>
                                    </a>
                                ) : (
                                    <a
                                        href="/courses"
                                        className="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-gradient-to-r from-cyan-500 via-brand-600 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-bold text-xs shadow-[0_0_25px_-3px_rgba(0,240,255,0.5)] transition-all hover:scale-[1.02] active:scale-95"
                                    >
                                        <Compass size={16} />
                                        <span>{t('explore_btn')}</span>
                                    </a>
                                )}

                                <a
                                    href="/leaderboard"
                                    className="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/15 dark:bg-slate-900/60 dark:hover:bg-slate-800/80 border border-white/20 dark:border-amber-400/30 text-white font-bold text-xs transition-all hover:scale-[1.02] shadow-xs active:scale-95"
                                >
                                    <Trophy size={14} className="text-amber-400 drop-shadow-[0_0_8px_rgba(251,191,36,0.6)]" />
                                    <span>{t('rank_badge')}{stats.rank ? ` #${stats.rank}` : ' —'}</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    {/* Progress & Ranking Glass Card */}
                    <div className="w-full lg:w-72 shrink-0 bg-white/5 dark:bg-slate-900/60 backdrop-blur-xl border border-white/10 dark:border-cyan-500/25 rounded-2xl p-4 sm:p-5 flex flex-col gap-3 shadow-lg">
                        <div className="flex items-center justify-between">
                            <span className="text-[11px] font-bold text-slate-300 dark:text-cyan-300/80 uppercase tracking-wider">
                                {t('progression_title')}
                            </span>
                            <span className="inline-flex items-center gap-1 text-xs font-black text-amber-400">
                                <Trophy size={13} />
                                <span>{stats.rank ? `#${stats.rank}` : '—'}</span>
                            </span>
                        </div>

                        {/* Level & XP Mini Bar */}
                        <div className="space-y-1.5">
                            <div className="flex items-center justify-between text-xs">
                                <span className="font-extrabold text-white">
                                    {t('level')} {level.level}
                                </span>
                                <span className="font-bold text-cyan-300 text-[11px]">
                                    {level.progress || 0} XP
                                </span>
                            </div>
                            <div className="w-full h-2 rounded-full bg-slate-800 dark:bg-slate-950/80 overflow-hidden p-0.5 border border-white/10">
                                <div
                                    className="h-full rounded-full bg-gradient-to-r from-cyan-400 to-indigo-500 shadow-[0_0_10px_#00f0ff] transition-all duration-700"
                                    style={{ width: `${Math.min(100, Math.max(8, (level.progress || 0) % 100))}%` }}
                                />
                            </div>
                        </div>

                        {/* Quick Stats Grid */}
                        <div className="grid grid-cols-2 gap-2 pt-1 border-t border-white/10 dark:border-slate-800/80">
                            <div className="p-2 rounded-xl bg-white/5 dark:bg-slate-950/40 text-center">
                                <span className="text-[10px] text-slate-400 block">{t('stat_active_courses')}</span>
                                <span className="text-sm font-black text-cyan-300 mt-0.5 block">{stats.active_courses || 0}</span>
                            </div>
                            <div className="p-2 rounded-xl bg-white/5 dark:bg-slate-950/40 text-center">
                                <span className="text-[10px] text-slate-400 block">{t('stat_certificates')}</span>
                                <span className="text-sm font-black text-amber-300 mt-0.5 block">{stats.certificates || 0}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* 6 Stats Grid Tiles — Cyber Neon Cards */}
            <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                {/* 1. Active Courses (Cyan) */}
                <div className="p-4 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-slate-900/95 dark:to-cyan-950/30 border border-slate-200/80 dark:border-cyan-500/25 shadow-sm hover:shadow-md hover:dark:border-cyan-400/60 hover:dark:shadow-[0_0_25px_-5px_rgba(6,182,212,0.4)] transition-all group">
                    <div className="w-10 h-10 rounded-xl bg-brand-50 dark:bg-cyan-500/15 text-brand-600 dark:text-cyan-400 dark:border dark:border-cyan-500/30 dark:shadow-[0_0_12px_rgba(6,182,212,0.3)] flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <BookOpen size={20} />
                    </div>
                    <span className="text-[11px] font-bold text-slate-500 dark:text-cyan-300/70 uppercase tracking-wider block">{t('stat_active_courses')}</span>
                    <h3 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight mt-0.5 rtl-num">
                        {stats.active_courses || 0}
                    </h3>
                </div>

                {/* 2. Completed (Emerald) */}
                <div className="p-4 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-slate-900/95 dark:to-emerald-950/30 border border-slate-200/80 dark:border-emerald-500/25 shadow-sm hover:shadow-md hover:dark:border-emerald-400/60 hover:dark:shadow-[0_0_25px_-5px_rgba(16,185,129,0.4)] transition-all group">
                    <div className="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 dark:border dark:border-emerald-500/30 dark:shadow-[0_0_12px_rgba(16,185,129,0.3)] flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <CheckCircle size={20} />
                    </div>
                    <span className="text-[11px] font-bold text-slate-500 dark:text-emerald-300/70 uppercase tracking-wider block">{t('stat_completed_courses')}</span>
                    <h3 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight mt-0.5 rtl-num">
                        {stats.completed_courses || 0}
                    </h3>
                </div>

                {/* 3. Exams Passed (Purple) */}
                <div className="p-4 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-slate-900/95 dark:to-purple-950/30 border border-slate-200/80 dark:border-purple-500/25 shadow-sm hover:shadow-md hover:dark:border-purple-400/60 hover:dark:shadow-[0_0_25px_-5px_rgba(168,85,247,0.4)] transition-all group">
                    <div className="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-500/15 text-purple-600 dark:text-purple-400 dark:border dark:border-purple-500/30 dark:shadow-[0_0_12px_rgba(168,85,247,0.3)] flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <ClipboardCheck size={20} />
                    </div>
                    <span className="text-[11px] font-bold text-slate-500 dark:text-purple-300/70 uppercase tracking-wider block">{t('stat_exams_passed')}</span>
                    <h3 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight mt-0.5 rtl-num">
                        {stats.passed_exams || 0}
                    </h3>
                </div>

                {/* 4. Certificates (Amber Gold) */}
                <div className="p-4 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-slate-900/95 dark:to-amber-950/30 border border-slate-200/80 dark:border-amber-500/25 shadow-sm hover:shadow-md hover:dark:border-amber-400/60 hover:dark:shadow-[0_0_25px_-5px_rgba(245,158,11,0.4)] transition-all group">
                    <div className="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 dark:border dark:border-amber-500/30 dark:shadow-[0_0_12px_rgba(245,158,11,0.3)] flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <Award size={20} />
                    </div>
                    <span className="text-[11px] font-bold text-slate-500 dark:text-amber-300/70 uppercase tracking-wider block">{t('stat_certificates')}</span>
                    <h3 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight mt-0.5 rtl-num">
                        {stats.certificates || 0}
                    </h3>
                </div>

                {/* 5. Streak (Flame Orange) */}
                <div className="p-4 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-slate-900/95 dark:to-orange-950/30 border border-slate-200/80 dark:border-orange-500/25 shadow-sm hover:shadow-md hover:dark:border-orange-400/60 hover:dark:shadow-[0_0_25px_-5px_rgba(249,115,22,0.4)] transition-all group">
                    <div className="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-500/15 text-orange-600 dark:text-orange-400 dark:border dark:border-orange-500/30 dark:shadow-[0_0_12px_rgba(249,115,22,0.3)] flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <Flame size={20} />
                    </div>
                    <span className="text-[11px] font-bold text-slate-500 dark:text-orange-300/70 uppercase tracking-wider block">{t('stat_streak')}</span>
                    <h3 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight mt-0.5 flex items-baseline gap-1">
                        <span className="rtl-num font-mono">{stats.current_streak || 0}</span>
                        <span className="text-xs font-bold text-slate-500 dark:text-orange-300/60">{t('days')}</span>
                    </h3>
                </div>

                {/* 6. Total Points / Score (Electric Sky Blue) */}
                <div className="p-4 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-slate-900/95 dark:to-sky-950/30 border border-slate-200/80 dark:border-sky-500/25 shadow-sm hover:shadow-md hover:dark:border-sky-400/60 hover:dark:shadow-[0_0_25px_-5px_rgba(14,165,233,0.4)] transition-all group">
                    <div className="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-sky-500/15 text-cyan-600 dark:text-sky-400 dark:border dark:border-sky-500/30 dark:shadow-[0_0_12px_rgba(14,165,233,0.3)] flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <Zap size={20} />
                    </div>
                    <span className="text-[11px] font-bold text-slate-500 dark:text-sky-300/70 uppercase tracking-wider block">{t('stat_total_xp')}</span>
                    <h3 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight mt-0.5 flex items-baseline gap-1">
                        <span className="rtl-num font-mono">{(stats.total_xp || 0).toLocaleString()}</span>
                        <span className="text-xs font-bold text-slate-500 dark:text-sky-300/60">{t('points_unit')}</span>
                    </h3>
                </div>
            </div>

            {/* Score Progression Hub */}
            <div className="p-6 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0b1329] dark:to-[#050916] border border-slate-200/80 dark:border-cyan-500/20 shadow-sm dark:shadow-[0_15px_35px_-10px_rgba(0,0,0,0.7)] mb-6 transition-all">
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800/80">
                    <div>
                        <div className="flex items-center gap-2">
                            <span className="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_rgba(0,240,255,0.8)]" />
                            <h3 className="font-extrabold text-slate-900 dark:text-white text-lg">{t('progression_title')}</h3>
                        </div>
                        <p className="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                            {t('progression_desc')}
                        </p>
                    </div>

                    <div className="flex items-center gap-3">
                        <div className="px-3.5 py-1.5 rounded-xl bg-brand-50 dark:bg-cyan-500/10 border border-brand-200/50 dark:border-cyan-400/30 text-brand-700 dark:text-cyan-300 text-xs font-bold shadow-xs">
                            {t('level')} {level.level} · {level.title || t('scholar')}
                        </div>
                        <a
                            href="/scoring-help"
                            className="text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-brand-600 dark:hover:text-cyan-400 transition-colors flex items-center gap-1"
                        >
                            <span>{t('how_scoring_works')}</span>
                            {isRtl ? <ArrowLeft size={13} /> : <ArrowRight size={13} />}
                        </a>
                    </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6">
                    {/* Earned XP */}
                    <div className="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/25 border border-emerald-200/60 dark:border-emerald-500/30 dark:shadow-[0_0_20px_-5px_rgba(16,185,129,0.2)]">
                        <span className="text-xs font-bold text-emerald-800 dark:text-emerald-300 block">{t('earned_points')}</span>
                        <div className="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 flex items-baseline gap-1.5">
                            <span className="rtl-num font-mono font-black text-2xl">+{((stats.earned_points || stats.total_score || 0)).toLocaleString()}</span>
                            <span className="text-xs font-bold text-emerald-700 dark:text-emerald-300/70">{t('points_unit')}</span>
                        </div>
                    </div>

                    {/* Deducted Points */}
                    <div className="p-4 rounded-2xl bg-rose-50/60 dark:bg-rose-950/25 border border-rose-200/60 dark:border-rose-500/30 dark:shadow-[0_0_20px_-5px_rgba(244,63,94,0.2)]">
                        <span className="text-xs font-bold text-rose-800 dark:text-rose-300 block">{t('deducted_points')}</span>
                        <div className="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1 flex items-baseline gap-1.5">
                            <span className="rtl-num font-mono font-black text-2xl">-{Math.abs(stats.deducted_points || 0).toLocaleString()}</span>
                            <span className="text-xs font-bold text-rose-700 dark:text-rose-300/70">{t('points_unit')}</span>
                        </div>
                    </div>

                    {/* Overall Rank */}
                    <div className="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/25 border border-amber-200/60 dark:border-amber-500/30 dark:shadow-[0_0_20px_-5px_rgba(245,158,11,0.2)]">
                        <span className="text-xs font-bold text-amber-800 dark:text-amber-300 block">{t('overall_rank')}</span>
                        <div className="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1 flex items-baseline gap-1">
                            <span className="text-lg font-bold text-amber-500">#</span>
                            <span className="rtl-num font-mono font-black text-2xl">{stats.rank || '—'}</span>
                        </div>
                    </div>
                </div>

                {/* Progression Bar */}
                <div className="pt-6">
                    <div className="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                        <span>{t('next_level_label')}</span>
                        <span className="rtl-num font-mono text-cyan-500 dark:text-cyan-400">{level.progress || 0}%</span>
                    </div>
                    <div className="w-full h-3.5 rounded-full bg-slate-100 dark:bg-slate-800/80 border dark:border-slate-700/60 overflow-hidden p-0.5">
                        <div
                            className={`h-full rounded-full transition-all duration-1000 shadow-[0_0_12px_rgba(6,182,212,0.6)] ${
                                isRtl
                                    ? 'bg-gradient-to-l from-cyan-400 via-brand-500 to-indigo-500'
                                    : 'bg-gradient-to-r from-cyan-400 via-brand-500 to-indigo-500'
                            }`}
                            style={{ width: `${Math.min(100, Math.max(0, level.progress || 0))}%` }}
                        />
                    </div>
                </div>
            </div>

            {/* Charts Row */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
                <div className="lg:col-span-8 p-6 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0b1329] dark:to-[#050916] border border-slate-200/80 dark:border-slate-800/80 hover:dark:border-cyan-500/30 shadow-sm dark:shadow-[0_10px_30px_-10px_rgba(0,0,0,0.6)] transition-all">
                    <div className="mb-4">
                        <h4 className="font-extrabold text-slate-800 dark:text-white text-base">{t('weekly_activity_title')}</h4>
                        <p className="text-xs text-slate-400 dark:text-slate-400 mt-0.5">{t('weekly_activity_desc')}</p>
                    </div>
                    <WeeklyActivityChart
                        weeklyProgress={weeklyProgress}
                        completionRate={stats.completion_rate || 0}
                    />
                </div>

                <div className="lg:col-span-4 p-6 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0b1329] dark:to-[#050916] border border-slate-200/80 dark:border-slate-800/80 hover:dark:border-cyan-500/30 shadow-sm dark:shadow-[0_10px_30px_-10px_rgba(0,0,0,0.6)] flex flex-col justify-between transition-all">
                    <div>
                        <h4 className="font-extrabold text-slate-800 dark:text-white text-base">{t('course_status_title')}</h4>
                        <p className="text-xs text-slate-400 dark:text-slate-400 mt-0.5">{t('course_status_desc')}</p>
                    </div>
                    <CourseStatusDonut courseStats={courseStats} />
                </div>
            </div>

            {/* My Active Courses */}
            <div className="p-6 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0b1329] dark:to-[#050916] border border-slate-200/80 dark:border-slate-800/80 hover:dark:border-cyan-500/30 shadow-sm dark:shadow-[0_10px_30px_-10px_rgba(0,0,0,0.6)] space-y-4 mb-6 transition-all">
                <div className="flex items-center justify-between">
                    <div>
                        <h4 className="font-extrabold text-slate-800 dark:text-white text-base">{t('active_courses_section_title')}</h4>
                        <p className="text-xs text-slate-400 dark:text-slate-400 mt-0.5">{t('active_courses_section_desc')}</p>
                    </div>
                    <Link
                        href="/student/courses"
                        className="text-xs font-bold text-brand-600 dark:text-cyan-400 hover:text-brand-700 dark:hover:text-cyan-300 flex items-center gap-1 hover:underline"
                    >
                        <span>{t('view_all_courses')}</span>
                        {isRtl ? <ArrowLeft size={14} /> : <ArrowRight size={14} />}
                    </Link>
                </div>

                {enrollments.length > 0 ? (
                    <div className="space-y-3">
                        {enrollments.map((item) => (
                            <div
                                key={item.id}
                                className="p-4 rounded-2xl bg-slate-50/70 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-brand-200 dark:hover:border-cyan-500/40 hover:dark:bg-slate-800/60 transition-all"
                            >
                                <div className="flex items-center gap-4 min-w-0">
                                    <div className="w-12 h-12 rounded-xl bg-slate-200 dark:bg-slate-800 border dark:border-slate-700 overflow-hidden shrink-0 flex items-center justify-center">
                                        {item.course.thumbnail ? (
                                            <img
                                                src={item.course.thumbnail}
                                                alt={item.course.title}
                                                className="w-full h-full object-cover"
                                            />
                                        ) : (
                                            <BookOpen size={20} className="text-brand-600 dark:text-cyan-400" />
                                        )}
                                    </div>
                                    <div className="min-w-0">
                                        <h5 className="font-bold text-slate-800 dark:text-white text-sm truncate">
                                            {item.course.title}
                                        </h5>
                                        <div className="flex flex-wrap items-center gap-2 sm:gap-3 mt-1 text-xs text-slate-400">
                                            {item.course.category && (
                                                <span className="px-2 py-0.5 bg-white dark:bg-slate-800/90 rounded-md border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-cyan-300 font-bold text-[10px]">
                                                    {item.course.category.name === 'Programming' ? t('cat_programming', item.course.category.name) : (item.course.category.name === 'Design' ? t('cat_design', item.course.category.name) : item.course.category.name)}
                                                </span>
                                            )}
                                            <span className="font-medium text-slate-500 dark:text-slate-400">
                                                {item.course.duration_weeks === 'Self-paced' ? t('self_paced') : item.course.duration_weeks}
                                            </span>
                                            <span className="flex items-center gap-1 font-bold text-slate-700 dark:text-slate-300">
                                                <span className="rtl-num font-mono text-cyan-600 dark:text-cyan-400">{item.progress_percentage}%</span>
                                                <span>{t('completed_percent')}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div className="flex items-center gap-2 shrink-0">
                                    <a
                                        href={`/courses/${item.course.id}/chat`}
                                        className="p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-cyan-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                                        title={t('chat')}
                                    >
                                        <MessageSquare size={16} />
                                    </a>
                                    <a
                                        href={`/student/courses/${item.course.slug}/learn`}
                                        className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 dark:bg-gradient-to-r dark:from-cyan-500 dark:to-brand-600 dark:hover:from-cyan-400 dark:hover:to-brand-500 text-white font-bold text-xs shadow-sm dark:shadow-[0_0_15px_rgba(0,240,255,0.35)] transition-all"
                                    >
                                        <PlayCircle size={15} />
                                        <span>{t('learn')}</span>
                                    </a>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="py-12 text-center text-slate-400 space-y-2">
                        <BookOpen size={36} className="text-slate-300 dark:text-slate-600 mx-auto" />
                        <p className="text-xs font-medium">{t('no_courses_yet')}</p>
                        <a
                            href="/courses"
                            className="inline-block mt-2 px-4 py-2 rounded-xl bg-brand-600 dark:bg-gradient-to-r dark:from-cyan-500 dark:to-brand-600 text-white text-xs font-bold hover:bg-brand-500 dark:shadow-[0_0_15px_rgba(0,240,255,0.35)] transition-colors"
                        >
                            {t('browse_courses')}
                        </a>
                    </div>
                )}
            </div>

            {/* Community Leaderboard & Certificates */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
                {/* Leaderboard */}
                <div className="lg:col-span-7 p-6 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0b1329] dark:to-[#050916] border border-slate-200/80 dark:border-slate-800/80 hover:dark:border-cyan-500/30 shadow-sm dark:shadow-[0_10px_30px_-10px_rgba(0,0,0,0.6)] space-y-4 transition-all">
                    <div className="flex items-center justify-between">
                        <div>
                            <h4 className="font-extrabold text-slate-800 dark:text-white text-base">{t('leaderboard_section_title')}</h4>
                            <p className="text-xs text-slate-400 dark:text-slate-400 mt-0.5">{t('leaderboard_section_desc')}</p>
                        </div>
                        <a
                            href="/leaderboard"
                            className="text-xs font-bold text-brand-600 dark:text-cyan-400 hover:text-brand-700 dark:hover:text-cyan-300 hover:underline flex items-center gap-1"
                        >
                            <span>{t('full_board')}</span>
                            {isRtl ? <ArrowLeft size={13} /> : <ArrowRight size={13} />}
                        </a>
                    </div>

                    {/* Top 3 Podium */}
                    {leaderboard.length >= 3 && (
                        <div className="grid grid-cols-3 gap-2 pt-2 pb-4 text-center">
                            {/* Rank 2 */}
                            <div className="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/80 border border-slate-100 dark:border-slate-700/60 flex flex-col items-center justify-end">
                                <div className="w-10 h-10 rounded-full overflow-hidden border-2 border-slate-300 dark:border-slate-500 mb-1">
                                    <img src={leaderboard[1].avatar} alt={leaderboard[1].name} className="w-full h-full object-cover" />
                                </div>
                                <span className="text-xs font-bold text-slate-800 dark:text-slate-200 truncate w-full">{leaderboard[1].name}</span>
                                <span className="text-[10px] text-slate-400">{leaderboard[1].xp.toLocaleString()} {t('points_unit')}</span>
                                <span className="mt-1 px-2 py-0.2 rounded-full text-[10px] font-bold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">{t('rank_2nd')}</span>
                            </div>

                            {/* Rank 1 */}
                            <div className="p-3 rounded-2xl bg-amber-50/80 dark:bg-gradient-to-b dark:from-amber-950/40 dark:to-slate-900/90 border border-amber-200 dark:border-amber-400/50 dark:shadow-[0_0_25px_-5px_rgba(245,158,11,0.35)] flex flex-col items-center justify-end -mt-3">
                                <Crown size={16} className="text-amber-500 dark:text-amber-400 mb-1 animate-pulse" />
                                <div className="w-12 h-12 rounded-full overflow-hidden border-2 border-amber-400 mb-1 shadow-[0_0_12px_rgba(251,191,36,0.5)]">
                                    <img src={leaderboard[0].avatar} alt={leaderboard[0].name} className="w-full h-full object-cover" />
                                </div>
                                <span className="text-xs font-black text-slate-900 dark:text-white truncate w-full">{leaderboard[0].name}</span>
                                <span className="text-[10px] text-amber-700 dark:text-amber-400 font-bold">{leaderboard[0].xp.toLocaleString()} {t('points_unit')}</span>
                                <span className="mt-1 px-2 py-0.2 rounded-full text-[10px] font-black bg-amber-400 text-amber-950">{t('rank_1st')}</span>
                            </div>

                            {/* Rank 3 */}
                            <div className="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/80 border border-slate-100 dark:border-amber-700/40 flex flex-col items-center justify-end">
                                <div className="w-10 h-10 rounded-full overflow-hidden border-2 border-amber-600/40 mb-1">
                                    <img src={leaderboard[2].avatar} alt={leaderboard[2].name} className="w-full h-full object-cover" />
                                </div>
                                <span className="text-xs font-bold text-slate-800 dark:text-slate-200 truncate w-full">{leaderboard[2].name}</span>
                                <span className="text-[10px] text-slate-400">{leaderboard[2].xp.toLocaleString()} {t('points_unit')}</span>
                                <span className="mt-1 px-2 py-0.2 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-400">{t('rank_3rd')}</span>
                            </div>
                        </div>
                    )}
                </div>

                {/* Certificates Showcase */}
                <div className="lg:col-span-5 p-6 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0b1329] dark:to-[#050916] border border-slate-200/80 dark:border-slate-800/80 hover:dark:border-cyan-500/30 shadow-sm dark:shadow-[0_10px_30px_-10px_rgba(0,0,0,0.6)] space-y-4 transition-all">
                    <div className="flex items-center justify-between">
                        <div>
                            <h4 className="font-extrabold text-slate-800 dark:text-white text-base">{t('earned_certificates_title')}</h4>
                            <p className="text-xs text-slate-400 dark:text-slate-400 mt-0.5">{t('earned_certificates_desc')}</p>
                        </div>
                        <Link
                            href="/student/certificates"
                            className="text-xs font-bold text-brand-600 dark:text-cyan-400 hover:text-brand-700 dark:hover:text-cyan-300 hover:underline flex items-center gap-1"
                        >
                            <span>{t('view_all')} ({certificates.length + completedEnrollments.length})</span>
                            {isRtl ? <ArrowLeft size={13} /> : <ArrowRight size={13} />}
                        </Link>
                    </div>

                    {certificates.length > 0 || completedEnrollments.length > 0 ? (
                        <div className="space-y-3">
                            {certificates.slice(0, 3).map((cert) => (
                                <div
                                    key={cert.id}
                                    className="p-3 rounded-2xl bg-blue-50/50 dark:bg-cyan-950/20 border-s-4 border-brand-500 dark:border-cyan-400 dark:border-t dark:border-r dark:border-b dark:border-cyan-500/20 flex items-center gap-3 transition-colors"
                                >
                                    <Award size={24} className="text-brand-600 dark:text-cyan-400 shrink-0" />
                                    <div className="min-w-0">
                                        <h5 className="font-bold text-slate-800 dark:text-slate-100 text-xs truncate">{cert.title}</h5>
                                        <p className="text-[10px] text-slate-400">{t('issued_date')} {cert.issued_at}</p>
                                    </div>
                                </div>
                            ))}

                            {completedEnrollments.slice(0, 3).map((item) => (
                                <div
                                    key={item.id}
                                    className="p-3 rounded-2xl bg-amber-50/50 dark:bg-amber-950/20 border-s-4 border-amber-500 dark:border-amber-400 dark:border-t dark:border-r dark:border-b dark:border-amber-500/20 flex items-center gap-3 transition-colors"
                                >
                                    <Trophy size={24} className="text-amber-600 dark:text-amber-400 shrink-0" />
                                    <div className="min-w-0">
                                        <h5 className="font-bold text-slate-800 dark:text-slate-100 text-xs truncate">{item.course_title}</h5>
                                        <p className="text-[10px] text-slate-400">{t('completed_program')}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="py-12 text-center text-slate-400 space-y-2">
                            <Award size={36} className="text-slate-300 dark:text-slate-600 mx-auto" />
                            <p className="text-xs font-medium">{t('no_certs_yet')}</p>
                            <Link
                                href="/student/courses"
                                className="inline-block mt-2 px-4 py-2 rounded-xl bg-brand-50 dark:bg-cyan-500/10 text-brand-700 dark:text-cyan-300 border dark:border-cyan-500/30 text-xs font-bold hover:bg-brand-100 dark:hover:bg-cyan-500/20 transition-colors"
                            >
                                {t('start_learning')}
                            </Link>
                        </div>
                    )}
                </div>
            </div>

            {/* Upcoming Live Events & Notifications */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {/* Events */}
                <div className="lg:col-span-7 p-6 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0b1329] dark:to-[#050916] border border-slate-200/80 dark:border-slate-800/80 hover:dark:border-cyan-500/30 shadow-sm dark:shadow-[0_10px_30px_-10px_rgba(0,0,0,0.6)] space-y-4 transition-all">
                    <div className="flex items-center justify-between">
                        <div>
                            <h4 className="font-extrabold text-slate-800 dark:text-white text-base">{t('upcoming_events_title')}</h4>
                            <p className="text-xs text-slate-400 dark:text-slate-400 mt-0.5">{t('upcoming_events_desc')}</p>
                        </div>
                        <a href="/events" className="text-xs font-bold text-brand-600 dark:text-cyan-400 hover:text-brand-700 dark:hover:text-cyan-300 hover:underline flex items-center gap-1">
                            <span>{t('all_events')}</span>
                            {isRtl ? <ArrowLeft size={13} /> : <ArrowRight size={13} />}
                        </a>
                    </div>

                    {upcomingEvents.length > 0 ? (
                        <div className="space-y-3">
                            {upcomingEvents.map((event) => (
                                <div
                                    key={event.id}
                                    className="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-4 hover:border-brand-200 dark:hover:border-cyan-500/30 transition-all"
                                >
                                    <div className="flex items-center gap-3 min-w-0">
                                        <div className="w-10 h-10 rounded-xl bg-brand-50 dark:bg-cyan-500/10 text-brand-600 dark:text-cyan-400 flex items-center justify-center shrink-0">
                                            <Calendar size={18} />
                                        </div>
                                        <div className="min-w-0">
                                            <h5 className="font-bold text-slate-800 dark:text-white text-xs truncate">{event.title}</h5>
                                            <p className="text-[10px] text-slate-400">{event.start_date}</p>
                                        </div>
                                    </div>
                                    <span className="px-2.5 py-1 rounded-full text-[10px] font-bold bg-brand-100 dark:bg-cyan-500/20 text-brand-700 dark:text-cyan-300 border dark:border-cyan-500/30 shrink-0 uppercase">
                                        {event.type}
                                    </span>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="py-8 text-center text-slate-400 text-xs font-medium">
                            {t('no_events')}
                        </div>
                    )}
                </div>

                {/* Notifications Stream */}
                <div className="lg:col-span-5 p-6 rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0b1329] dark:to-[#050916] border border-slate-200/80 dark:border-slate-800/80 hover:dark:border-cyan-500/30 shadow-sm dark:shadow-[0_10px_30px_-10px_rgba(0,0,0,0.6)] space-y-4 transition-all">
                    <div className="flex items-center justify-between">
                        <div>
                            <h4 className="font-extrabold text-slate-800 dark:text-white text-base">{t('notifications_title')}</h4>
                            <p className="text-xs text-slate-400 dark:text-slate-400 mt-0.5">{t('notifications_desc')}</p>
                        </div>
                        {unreadNotifCount > 0 && (
                            <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white shadow-[0_0_10px_rgba(244,63,94,0.5)]">
                                {unreadNotifCount} {t('new_badge')}
                            </span>
                        )}
                    </div>

                    {notifications.length > 0 ? (
                        <div className="space-y-2.5">
                            {notifications.map((n) => (
                                <div
                                    key={n.id}
                                    className="p-3 rounded-2xl bg-slate-50/70 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800/80 flex items-start gap-3 text-xs hover:border-brand-200 dark:hover:border-cyan-500/30 transition-all"
                                >
                                    <div className="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_8px_rgba(0,240,255,0.7)] mt-1.5 shrink-0" />
                                    <div className="flex-1 min-w-0">
                                        <h6 className="font-bold text-slate-800 dark:text-white truncate">{n.title}</h6>
                                        <p className="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1">{n.message}</p>
                                        <span className="text-[10px] text-slate-400 mt-0.5 block">{n.created_at}</span>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="py-8 text-center text-slate-400 text-xs font-medium">
                            {t('no_notifications')}
                        </div>
                    )}
                </div>
            </div>
        </StudentLayout>
    );
}
