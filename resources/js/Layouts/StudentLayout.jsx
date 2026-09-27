import React, { useState, useEffect } from 'react';
import { Head, usePage, Link } from '@inertiajs/react';
import StudentSidebar from '@/Components/Student/StudentSidebar';
import StudentTopbar from '@/Components/Student/StudentTopbar';
import { useLanguage } from '@/Context/LanguageContext';
import { ThemeProvider, useTheme } from '@/Context/ThemeContext';
import {
    CheckCircle,
    AlertCircle,
    Info,
    X,
    LayoutDashboard,
    BookOpen,
    ClipboardCheck,
    Trophy,
    Menu,
    Sparkles
} from 'lucide-react';

function StudentLayoutInner({ children, title }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [isCollapsed, setIsCollapsed] = useState(false);
    const { flash, url } = usePage().props;
    const currentUrl = usePage().url;
    const [showFlash, setShowFlash] = useState(true);
    const { isRtl, t } = useLanguage();
    const { theme, isDark } = useTheme();

    useEffect(() => {
        try {
            const saved = localStorage.getItem('studentSidebarCollapsed');
            if (saved === 'true') {
                setIsCollapsed(true);
            }
        } catch (e) {
            // ignore
        }
    }, []);

    useEffect(() => {
        if (flash?.success || flash?.error || flash?.info) {
            setShowFlash(true);
        }
    }, [flash]);

    const isDashboardActive = currentUrl === '/student/dashboard' || currentUrl === '/student';
    const isCoursesActive = currentUrl.startsWith('/student/courses');
    const isQuizzesActive = currentUrl.startsWith('/student/quizzes');
    const isLeaderboardActive = currentUrl.startsWith('/leaderboard') || currentUrl.startsWith('/student/certificates');

    return (
        <div
            className={`dashboard-wrapper ${theme} flex min-h-screen bg-slate-50 dark:bg-[#030712] text-slate-800 dark:text-slate-100 font-sans antialiased selection:bg-brand-500 selection:text-white transition-colors duration-200 ${isRtl ? 'font-vazir' : ''}`}
            dir={isRtl ? 'rtl' : 'ltr'}
        >
            <Head title={title} />

            {/* Collapsible Student Sidebar */}
            <StudentSidebar
                isOpen={sidebarOpen}
                setIsOpen={setSidebarOpen}
                isCollapsed={isCollapsed}
                setIsCollapsed={setIsCollapsed}
            />

            {/* Main Application Area */}
            <div className="main-content flex-1 flex flex-col min-w-0" id="mainContent">
                {/* 3D Navigation Bar / Topbar */}
                <StudentTopbar
                    onToggleSidebar={() => setSidebarOpen(!sidebarOpen)}
                    title={title}
                />

                {/* Flash Messages */}
                {showFlash && (flash?.success || flash?.error || flash?.info) && (
                    <div className="mx-4 sm:mx-8 mt-4">
                        {flash?.success && (
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 text-sm shadow-sm animate-in fade-in duration-200">
                                <div className="flex items-center gap-3">
                                    <CheckCircle size={20} className="text-emerald-600 shrink-0" />
                                    <span className="font-medium">{flash.success}</span>
                                </div>
                                <button
                                    onClick={() => setShowFlash(false)}
                                    className="text-emerald-500 hover:text-emerald-800 p-1"
                                >
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                        {flash?.error && (
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-800 text-sm shadow-sm animate-in fade-in duration-200">
                                <div className="flex items-center gap-3">
                                    <AlertCircle size={20} className="text-rose-600 shrink-0" />
                                    <span className="font-medium">{flash.error}</span>
                                </div>
                                <button
                                    onClick={() => setShowFlash(false)}
                                    className="text-rose-500 hover:text-rose-800 p-1"
                                >
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                        {flash?.info && (
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-brand-500/10 border border-brand-500/30 text-brand-800 text-sm shadow-sm animate-in fade-in duration-200">
                                <div className="flex items-center gap-3">
                                    <Info size={20} className="text-brand-600 shrink-0" />
                                    <span className="font-medium">{flash.info}</span>
                                </div>
                                <button
                                    onClick={() => setShowFlash(false)}
                                    className="text-brand-500 hover:text-brand-800 p-1"
                                >
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                    </div>
                )}

                {/* Page Content Viewport */}
                <main className="flex-1 p-4 sm:p-6 lg:p-8 pb-28 lg:pb-8">
                    {children}
                </main>

                {/* =========================================================================
                    3D MOBILE WEB APP FLOATING BOTTOM DOCK NAVIGATION
                    ========================================================================= */}
                <nav
                    className="lg:hidden fixed bottom-3 inset-x-3 z-40 bg-white/90 dark:bg-[#071329]/95 backdrop-blur-2xl border border-white/80 dark:border-cyan-500/25 rounded-2xl shadow-[0_15px_45px_-5px_rgba(0,0,0,0.35),0_0_20px_rgba(0,240,255,0.15)] px-1.5 py-1.5 flex items-center justify-between gap-1 transition-all"
                    aria-label="Mobile Navigation Dock"
                >
                    {/* 1. Dashboard */}
                    <Link
                        href="/student/dashboard"
                        className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all nav-3d-button ${
                            isDashboardActive
                                ? 'bg-gradient-to-tr from-brand-500/20 via-cyan-500/15 to-indigo-500/20 text-brand-600 dark:text-cyan-400 border border-cyan-500/35 shadow-[0_0_12px_rgba(0,240,255,0.25)]'
                                : 'text-slate-400 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'
                        }`}
                    >
                        <LayoutDashboard size={20} className={isDashboardActive ? 'text-cyan-500 dark:text-cyan-400 drop-shadow-[0_0_8px_#00f0ff]' : ''} />
                        <span className="text-[10px] sm:text-[11px] font-bold tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                            {isRtl ? 'داشبورد' : 'Dashboard'}
                        </span>
                        {isDashboardActive && <span className="w-1.5 h-1.5 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f0ff] mt-0.5" />}
                    </Link>

                    {/* 2. Courses */}
                    <Link
                        href="/student/courses"
                        className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all nav-3d-button ${
                            isCoursesActive
                                ? 'bg-gradient-to-tr from-brand-500/20 via-cyan-500/15 to-indigo-500/20 text-brand-600 dark:text-cyan-400 border border-cyan-500/35 shadow-[0_0_12px_rgba(0,240,255,0.25)]'
                                : 'text-slate-400 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'
                        }`}
                    >
                        <BookOpen size={20} className={isCoursesActive ? 'text-cyan-500 dark:text-cyan-400 drop-shadow-[0_0_8px_#00f0ff]' : ''} />
                        <span className="text-[10px] sm:text-[11px] font-bold tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                            {isRtl ? 'کورس‌ها' : 'Courses'}
                        </span>
                        {isCoursesActive && <span className="w-1.5 h-1.5 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f0ff] mt-0.5" />}
                    </Link>

                    {/* 3. Quizzes */}
                    <Link
                        href="/student/quizzes"
                        className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all nav-3d-button ${
                            isQuizzesActive
                                ? 'bg-gradient-to-tr from-brand-500/20 via-cyan-500/15 to-indigo-500/20 text-brand-600 dark:text-cyan-400 border border-cyan-500/35 shadow-[0_0_12px_rgba(0,240,255,0.25)]'
                                : 'text-slate-400 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'
                        }`}
                    >
                        <ClipboardCheck size={20} className={isQuizzesActive ? 'text-cyan-500 dark:text-cyan-400 drop-shadow-[0_0_8px_#00f0ff]' : ''} />
                        <span className="text-[10px] sm:text-[11px] font-bold tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                            {isRtl ? 'آزمون‌ها' : 'Quizzes'}
                        </span>
                        {isQuizzesActive && <span className="w-1.5 h-1.5 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f0ff] mt-0.5" />}
                    </Link>

                    {/* 4. Leaderboard / Awards */}
                    <a
                        href="/leaderboard"
                        className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all nav-3d-button ${
                            isLeaderboardActive
                                ? 'bg-gradient-to-tr from-amber-500/20 via-yellow-500/15 to-orange-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/35 shadow-[0_0_12px_rgba(245,158,11,0.25)]'
                                : 'text-slate-400 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'
                        }`}
                    >
                        <Trophy size={20} className={isLeaderboardActive ? 'text-amber-400 drop-shadow-[0_0_8px_#f59e0b]' : ''} />
                        <span className="text-[10px] sm:text-[11px] font-bold tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                            {isRtl ? 'پیشتازان' : 'Ranks'}
                        </span>
                        {isLeaderboardActive && <span className="w-1.5 h-1.5 rounded-full bg-amber-400 shadow-[0_0_8px_#f59e0b] mt-0.5" />}
                    </a>

                    {/* 5. Menu Button (triggers sidebar) */}
                    <button
                        type="button"
                        onClick={() => setSidebarOpen(true)}
                        className="flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center text-slate-400 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-all nav-3d-button"
                    >
                        <div className="relative">
                            <Menu size={20} />
                            <span className="absolute -top-0.5 -right-0.5 w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping" />
                        </div>
                        <span className="text-[10px] sm:text-[11px] font-bold tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                            {isRtl ? 'منو' : 'Menu'}
                        </span>
                    </button>
                </nav>

                {/* Dashboard Footer (Hidden on mobile to avoid dock collision) */}
                <footer className="hidden sm:flex mt-auto border-t border-slate-200/80 dark:border-slate-800/80 bg-white/50 dark:bg-[#081224]/60 backdrop-blur-xs py-4 px-6 sm:px-8 text-xs text-slate-500 dark:text-slate-400 flex-col sm:flex-row items-center justify-between gap-3 transition-colors">
                    <p>© {new Date().getFullYear()} {t('brand_title')}. {isRtl ? 'تمامی حقوق محفوظ است.' : 'All rights reserved.'}</p>
                    <div className="flex items-center gap-4">
                        <a href="/privacy" className="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                            {isRtl ? 'حریم خصوصی' : 'Privacy'}
                        </a>
                        <a href="/terms" className="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                            {isRtl ? 'شرایط استفاده' : 'Terms'}
                        </a>
                        <a href="/contact" className="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                            {isRtl ? 'تماس با ما' : 'Contact'}
                        </a>
                    </div>
                </footer>
            </div>
        </div>
    );
}

export default function StudentLayout({ children, title = 'Student Dashboard - Edvora Tech' }) {
    return (
        <ThemeProvider>
            <StudentLayoutInner title={title}>
                {children}
            </StudentLayoutInner>
        </ThemeProvider>
    );
}
