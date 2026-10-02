import React, { useState, useEffect, Component } from 'react';
import { Head, usePage, Link } from '@inertiajs/react';
import TeacherSidebar from '@/Components/Teacher/TeacherSidebar';
import TeacherTopbar from '@/Components/Teacher/TeacherTopbar';
import { ThemeProvider, useTheme } from '@/Context/ThemeContext';
import {
    CheckCircle,
    AlertCircle,
    Info,
    X,
    LayoutDashboard,
    BookOpen,
    UserCheck,
    HelpCircle,
    Menu,
    RefreshCw
} from 'lucide-react';

class ErrorBoundary extends Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false, error: null };
    }

    static getDerivedStateFromError(error) {
        return { hasError: true, error };
    }

    componentDidCatch(error, errorInfo) {
        console.error('TeacherLayout caught an error:', error, errorInfo);
    }

    render() {
        if (this.state.hasError) {
            return (
                <div className="min-h-screen bg-[#F5F8FC] dark:bg-[#030712] flex items-center justify-center p-4 font-sans font-vazir text-right" dir="rtl">
                    <div className="bg-white dark:bg-[#0b1528] rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-xl border border-[#E5EAF2] dark:border-rose-500/30 text-center space-y-4">
                        <div className="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto">
                            <AlertCircle size={28} />
                        </div>
                        <h2 className="text-lg font-extrabold text-[#111827] dark:text-white">خطایی در بارگذاری پنل رخ داد</h2>
                        <p className="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            متأسفانه بخشی از اطلاعات بارگذاری نشد. لطفاً صفحه را بازخوانی کنید.
                        </p>
                        <button
                            onClick={() => window.location.reload()}
                            className="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-[#0A58CA] text-white text-xs font-bold hover:bg-[#1683F7] transition-colors"
                        >
                            <RefreshCw size={16} />
                            <span>بارگذاری مجدد صفحه</span>
                        </button>
                    </div>
                </div>
            );
        }
        return this.props.children;
    }
}

function TeacherLayoutInner({ children, title = 'پنل اساتید و مدرسین - ادورا تک' }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [isCollapsed, setIsCollapsed] = useState(false);
    const { theme, isDark } = useTheme();
    const page = usePage();
    const props = page?.props || {};
    const currentUrl = page?.url || (typeof window !== 'undefined' ? window.location.pathname : '');
    const flash = props.flash || {};
    const [showFlash, setShowFlash] = useState(true);

    const teacherNav = props.teacherNav || {};
    const pendingEnrollments = teacherNav.pendingEnrollmentsCount || 0;

    useEffect(() => {
        try {
            const saved = localStorage.getItem('teacherSidebarCollapsed');
            if (saved === 'true') {
                setIsCollapsed(true);
            }
        } catch (e) {
            // ignore
        }
    }, []);

    // Set html dir attribute to rtl
    useEffect(() => {
        if (typeof document !== 'undefined') {
            document.documentElement.dir = 'rtl';
            document.documentElement.lang = 'fa';
        }
    }, []);

    // Reset flash visibility when flash message changes
    useEffect(() => {
        if (flash?.success || flash?.error || flash?.info || flash?.student_action) {
            setShowFlash(true);
        }
    }, [flash]);

    const flashMessage = flash?.success || flash?.student_action;

    // Navigation active states
    const isDashboardActive = currentUrl === '/teacher/dashboard' || currentUrl === '/teacher';
    const isYourCoursesActive = currentUrl.startsWith('/teacher/your-courses') || currentUrl.startsWith('/teacher/courses');
    const isEnrollmentsActive = currentUrl.startsWith('/teacher/enrollment-requests');
    const isQuizzesActive = currentUrl.startsWith('/teacher/quizzes');

    return (
        <div
            className={`min-h-screen ${theme} bg-[#F5F8FC] dark:bg-[#030712] text-[#111827] dark:text-slate-100 font-sans font-vazir flex flex-col antialiased selection:bg-[#0A58CA] selection:text-white transition-colors duration-200`}
            dir="rtl"
        >
            <Head title={title} />

            {/* Collapsible Instructor Sidebar (Positioned on the RIGHT) */}
            <TeacherSidebar
                isOpen={sidebarOpen}
                setIsOpen={setSidebarOpen}
                isCollapsed={isCollapsed}
                setIsCollapsed={setIsCollapsed}
            />

            {/* Main Application Area (Starts from the right after the sidebar on desktop) */}
            <div
                className={`flex-1 flex flex-col min-w-0 transition-all duration-300 ${isCollapsed ? 'lg:mr-20' : 'lg:mr-72'
                    }`}
            >
                {/* Fixed / Sticky Topbar with Theme Switcher */}
                <TeacherTopbar
                    onToggleSidebar={() => setSidebarOpen(!sidebarOpen)}
                    title={title}
                />

                {/* Flash Messages */}
                {showFlash && (flashMessage || flash?.error || flash?.info) && (
                    <div className="mx-3 sm:mx-8 mt-4">
                        {flashMessage && (
                            <div className="flex items-center justify-between p-3.5 sm:p-4 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs sm:text-sm shadow-sm animate-in fade-in duration-200">
                                <div className="flex items-center gap-2.5 sm:gap-3">
                                    <CheckCircle size={18} className="text-emerald-600 dark:text-emerald-400 shrink-0" />
                                    <span className="font-semibold">{flashMessage}</span>
                                </div>
                                <button
                                    onClick={() => setShowFlash(false)}
                                    className="text-emerald-500 hover:text-emerald-800 dark:hover:text-emerald-200 p-1"
                                    aria-label="بستن پیام"
                                >
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                        {flash?.error && (
                            <div className="flex items-center justify-between p-3.5 sm:p-4 rounded-2xl bg-rose-500/10 dark:bg-rose-500/15 border border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs sm:text-sm shadow-sm animate-in fade-in duration-200">
                                <div className="flex items-center gap-2.5 sm:gap-3">
                                    <AlertCircle size={18} className="text-rose-600 dark:text-rose-400 shrink-0" />
                                    <span className="font-semibold">{flash.error}</span>
                                </div>
                                <button
                                    onClick={() => setShowFlash(false)}
                                    className="text-rose-500 hover:text-rose-800 dark:hover:text-rose-200 p-1"
                                    aria-label="بستن پیام"
                                >
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                        {flash?.info && (
                            <div className="flex items-center justify-between p-3.5 sm:p-4 rounded-2xl bg-[#0A58CA]/10 dark:bg-cyan-500/15 border border-[#0A58CA]/30 dark:border-cyan-500/30 text-[#0A58CA] dark:text-cyan-300 text-xs sm:text-sm shadow-sm animate-in fade-in duration-200">
                                <div className="flex items-center gap-2.5 sm:gap-3">
                                    <Info size={18} className="text-[#0A58CA] dark:text-cyan-400 shrink-0" />
                                    <span className="font-semibold">{flash.info}</span>
                                </div>
                                <button
                                    onClick={() => setShowFlash(false)}
                                    className="text-[#0A58CA] hover:text-[#0A58CA]/80 dark:hover:text-cyan-200 p-1"
                                    aria-label="بستن پیام"
                                >
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                    </div>
                )}

                {/* Main Content Body (pb-28 on mobile for bottom navigation dock clearance) */}
                <main className="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 sm:space-y-8 pb-28 lg:pb-8">
                    {children}
                </main>

                {/* Modern RTL Footer (Desktop & Tablet) */}
                <footer className="hidden sm:flex h-16 border-t border-[#E5EAF2] dark:border-white/10 px-4 sm:px-8 flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500 dark:text-slate-400 bg-white/70 dark:bg-[#071328]/80 backdrop-blur-md mt-auto">
                    <div>
                        © {new Date().getFullYear()} <span className="text-[#111827] dark:text-slate-200 font-bold">ادورا تک (Edvora Tech)</span> — تمامی حقوق محفوظ است.
                    </div>
                    <div className="flex items-center gap-4">
                        <span className="flex items-center gap-1.5">
                            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                            سامانه اساتید و مدرسین
                        </span>
                        <span className="text-slate-400 dark:text-slate-500">نسخه ۲.۰</span>
                    </div>
                </footer>
            </div>

            {/* =========================================================================
                TEACHER MOBILE FLOATING BOTTOM DOCK NAVIGATION (with Dark Glow & 3D styling)
                ========================================================================= */}
            <nav
                className="lg:hidden fixed bottom-3 inset-x-3 z-40 bg-white/95 dark:bg-[#071329]/95 backdrop-blur-2xl border border-[#E5EAF2] dark:border-cyan-500/25 shadow-[0_12px_40px_-5px_rgba(10,88,202,0.18),0_4px_16px_rgba(0,0,0,0.06)] dark:shadow-[0_15px_45px_-5px_rgba(0,0,0,0.5),0_0_20px_rgba(0,240,255,0.15)] rounded-2xl px-1.5 py-1.5 flex items-center justify-between gap-1 transition-all"
                aria-label="نوار ناوبری موبایل مدرسین"
                dir="rtl"
            >
                {/* 1. Dashboard */}
                <Link
                    href="/teacher/dashboard"
                    className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all ${
                        isDashboardActive
                            ? 'bg-[#0A58CA]/10 dark:bg-cyan-500/15 text-[#0A58CA] dark:text-cyan-400 font-bold border border-transparent dark:border-cyan-500/30'
                            : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'
                    }`}
                >
                    <LayoutDashboard size={20} className={isDashboardActive ? 'text-[#0A58CA] dark:text-cyan-400' : ''} />
                    <span className="text-[10px] tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                        داشبورد
                    </span>
                    {isDashboardActive && <span className="w-1.5 h-1.5 rounded-full bg-[#0A58CA] dark:bg-cyan-400 mt-0.5" />}
                </Link>

                {/* 2. My Courses */}
                <Link
                    href="/teacher/your-courses"
                    className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all ${
                        isYourCoursesActive
                            ? 'bg-[#0A58CA]/10 dark:bg-cyan-500/15 text-[#0A58CA] dark:text-cyan-400 font-bold border border-transparent dark:border-cyan-500/30'
                            : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'
                    }`}
                >
                    <BookOpen size={20} className={isYourCoursesActive ? 'text-[#0A58CA] dark:text-cyan-400' : ''} />
                    <span className="text-[10px] tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                        دوره‌ها
                    </span>
                    {isYourCoursesActive && <span className="w-1.5 h-1.5 rounded-full bg-[#0A58CA] dark:bg-cyan-400 mt-0.5" />}
                </Link>

                {/* 3. Enrollment Requests */}
                <Link
                    href="/teacher/enrollment-requests"
                    className={`flex-1 min-w-0 relative flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all ${
                        isEnrollmentsActive
                            ? 'bg-[#0A58CA]/10 dark:bg-cyan-500/15 text-[#0A58CA] dark:text-cyan-400 font-bold border border-transparent dark:border-cyan-500/30'
                            : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'
                    }`}
                >
                    <div className="relative">
                        <UserCheck size={20} className={isEnrollmentsActive ? 'text-[#0A58CA] dark:text-cyan-400' : ''} />
                        {pendingEnrollments > 0 && (
                            <span className="absolute -top-1.5 -right-2 px-1 min-w-[15px] h-[15px] bg-[#F59E0B] text-white text-[9px] font-black rounded-full flex items-center justify-center ring-2 ring-white dark:ring-slate-900 animate-pulse">
                                {pendingEnrollments > 9 ? '9+' : pendingEnrollments}
                            </span>
                        )}
                    </div>
                    <span className="text-[10px] tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                        ثبت‌نام‌ها
                    </span>
                    {isEnrollmentsActive && <span className="w-1.5 h-1.5 rounded-full bg-[#0A58CA] dark:bg-cyan-400 mt-0.5" />}
                </Link>

                {/* 4. Quizzes */}
                <Link
                    href="/teacher/quizzes"
                    className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all ${
                        isQuizzesActive
                            ? 'bg-[#0A58CA]/10 dark:bg-cyan-500/15 text-[#0A58CA] dark:text-cyan-400 font-bold border border-transparent dark:border-cyan-500/30'
                            : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'
                    }`}
                >
                    <HelpCircle size={20} className={isQuizzesActive ? 'text-[#0A58CA] dark:text-cyan-400' : ''} />
                    <span className="text-[10px] tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                        آزمون‌ها
                    </span>
                    {isQuizzesActive && <span className="w-1.5 h-1.5 rounded-full bg-[#0A58CA] dark:bg-cyan-400 mt-0.5" />}
                </Link>

                {/* 5. Menu Drawer Trigger */}
                <button
                    type="button"
                    onClick={() => setSidebarOpen(true)}
                    className="flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center text-slate-500 dark:text-slate-400 hover:text-[#0A58CA] dark:hover:text-cyan-400 font-medium transition-all"
                    aria-label="باز کردن منو"
                >
                    <Menu size={20} />
                    <span className="text-[10px] tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                        منو
                    </span>
                </button>
            </nav>
        </div>
    );
}

export default function TeacherLayout({ children, title }) {
    return (
        <ThemeProvider>
            <ErrorBoundary>
                <TeacherLayoutInner title={title}>{children}</TeacherLayoutInner>
            </ErrorBoundary>
        </ThemeProvider>
    );
}
