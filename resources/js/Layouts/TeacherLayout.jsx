import React, { useState, useEffect } from 'react';
import { Head, usePage, Link } from '@inertiajs/react';
import TeacherSidebar from '@/Components/Teacher/TeacherSidebar';
import TeacherTopbar from '@/Components/Teacher/TeacherTopbar';
import TeacherMobileMenu from '@/Components/Teacher/TeacherMobileMenu';
import {
    CheckCircle,
    AlertCircle,
    Info,
    X,
    LayoutDashboard,
    BookOpen,
    UserCheck,
    ClipboardCheck,
    Menu
} from 'lucide-react';

export default function TeacherLayout({ children, title = 'پنل اساتید و مدرسین - ادورا تک' }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [isCollapsed, setIsCollapsed] = useState(false);
    const { flash } = usePage().props;
    const currentUrl = usePage().url;
    const [showFlash, setShowFlash] = useState(true);

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
        document.documentElement.dir = 'rtl';
        document.documentElement.lang = 'fa';
    }, []);

    // Reset flash visibility when flash message changes
    useEffect(() => {
        if (flash?.success || flash?.error || flash?.info || flash?.student_action) {
            setShowFlash(true);
        }
    }, [flash]);

    const flashMessage = flash?.success || flash?.student_action;

    return (
        <div
            className="min-h-screen bg-[#F5F8FC] text-[#111827] font-sans font-vazir flex flex-col antialiased selection:bg-[#0A58CA] selection:text-white"
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

            {/* Main Application Area (Starts from the right after the sidebar) */}
            <div
                className={`flex-1 flex flex-col min-w-0 transition-all duration-300 ${isCollapsed ? 'lg:mr-20' : 'lg:mr-72'
                    }`}
            >
                {/* Fixed Topbar */}
                <TeacherTopbar
                    onToggleSidebar={() => setSidebarOpen(!sidebarOpen)}
                    title={title}
                />

                {/* Flash Messages */}
                {showFlash && (flashMessage || flash?.error || flash?.info) && (
                    <div className="mx-4 sm:mx-8 mt-4">
                        {flashMessage && (
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 text-sm shadow-sm animate-in fade-in duration-200">
                                <div className="flex items-center gap-3">
                                    <CheckCircle size={20} className="text-emerald-600 shrink-0" />
                                    <span className="font-medium">{flashMessage}</span>
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
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-[#0A58CA]/10 border border-[#0A58CA]/30 text-[#0A58CA] text-sm shadow-sm animate-in fade-in duration-200">
                                <div className="flex items-center gap-3">
                                    <Info size={20} className="text-[#0A58CA] shrink-0" />
                                    <span className="font-medium">{flash.info}</span>
                                </div>
                                <button
                                    onClick={() => setShowFlash(false)}
                                    className="text-[#0A58CA] hover:text-[#0A58CA]/80 p-1"
                                >
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                    </div>
                )}

                {/* Main Content Body */}
                <main className="flex-1 p-4 sm:p-6 lg:p-8 pb-28 lg:pb-8 max-w-7xl w-full mx-auto space-y-8">
                    {children}
                </main>

                {/* =========================================================================
                    TEACHER MOBILE DOCK NAVIGATION
                    ========================================================================= */}
                <nav
                    className="lg:hidden fixed bottom-3 inset-x-3 z-40 bg-white/95 backdrop-blur-2xl border border-[#E5EAF2] rounded-2xl shadow-[0_15px_45px_-5px_rgba(10,88,202,0.15)] px-1.5 py-1.5 flex items-center justify-between gap-1 transition-all"
                    aria-label="Teacher Mobile Dock"
                >
                    {/* 1. Dashboard */}
                    <Link
                        href="/teacher/dashboard"
                        className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all ${currentUrl === '/teacher/dashboard' || currentUrl === '/teacher'
                            ? 'bg-[#0A58CA]/10 text-[#0A58CA] font-black'
                            : 'text-slate-400 hover:text-slate-700'
                            }`}
                    >
                        <LayoutDashboard size={20} className={currentUrl === '/teacher/dashboard' || currentUrl === '/teacher' ? 'text-[#0A58CA]' : ''} />
                        <span className="text-[10px] font-bold mt-1">داشبورد</span>
                    </Link>

                    {/* 2. Courses */}
                    <Link
                        href="/teacher/your-courses"
                        className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all ${currentUrl.startsWith('/teacher/your-courses') || currentUrl.startsWith('/teacher/courses')
                            ? 'bg-[#0A58CA]/10 text-[#0A58CA] font-black'
                            : 'text-slate-400 hover:text-slate-700'
                            }`}
                    >
                        <BookOpen size={20} className={currentUrl.startsWith('/teacher/your-courses') || currentUrl.startsWith('/teacher/courses') ? 'text-[#0A58CA]' : ''} />
                        <span className="text-[10px] font-bold mt-1">دوره‌ها</span>
                    </Link>

                    {/* 3. Enrollments */}
                    <Link
                        href="/teacher/enrollment-requests"
                        className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all ${currentUrl.startsWith('/teacher/enrollment-requests')
                            ? 'bg-[#0A58CA]/10 text-[#0A58CA] font-black'
                            : 'text-slate-400 hover:text-slate-700'
                            }`}
                    >
                        <UserCheck size={20} className={currentUrl.startsWith('/teacher/enrollment-requests') ? 'text-[#0A58CA]' : ''} />
                        <span className="text-[10px] font-bold mt-1">ثبت‌نام‌ها</span>
                    </Link>

                    {/* 4. Quizzes */}
                    <Link
                        href="/teacher/quizzes"
                        className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all ${currentUrl.startsWith('/teacher/quizzes')
                            ? 'bg-[#0A58CA]/10 text-[#0A58CA] font-black'
                            : 'text-slate-400 hover:text-slate-700'
                            }`}
                    >
                        <ClipboardCheck size={20} className={currentUrl.startsWith('/teacher/quizzes') ? 'text-[#0A58CA]' : ''} />
                        <span className="text-[10px] font-bold mt-1">آزمون‌ها</span>
                    </Link>

                    {/* 5. Mobile App Menu */}
                    <button
                        type="button"
                        onClick={() => setMobileMenuOpen(true)}
                        className="flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center text-slate-400 hover:text-slate-700 transition-all"
                    >
                        <Menu size={20} />
                        <span className="text-[10px] font-bold mt-1">منو</span>
                    </button>
                </nav>

                {/* Mobile Application Bottom Sheet Menu */}
                <TeacherMobileMenu
                    isOpen={mobileMenuOpen}
                    setIsOpen={setMobileMenuOpen}
                />

                {/* Modern RTL Footer */}
                <footer className="hidden sm:flex h-16 border-t border-[#E5EAF2] px-4 sm:px-8 flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500 bg-white/70 backdrop-blur-xs mt-auto">
                    <div>
                        © {new Date().getFullYear()} <span className="text-[#111827] font-bold">ادورا تک (Edvora Tech)</span> — تمامی حقوق محفوظ است.
                    </div>
                    <div className="flex items-center gap-4">
                        <span className="flex items-center gap-1.5">
                            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                            سامانه اساتید و مدرسین
                        </span>
                        <span className="text-slate-400">نسخه ۲.۰</span>
                    </div>
                </footer>
            </div>
        </div>
    );
}
