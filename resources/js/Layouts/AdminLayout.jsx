import React, { useState } from 'react';
import { Head, usePage, Link } from '@inertiajs/react';
import Sidebar from '@/Components/Admin/Sidebar';
import Topbar from '@/Components/Admin/Topbar';
import AdminMobileMenu from '@/Components/Admin/AdminMobileMenu';
import {
    CheckCircle,
    AlertCircle,
    Info,
    X,
    LayoutDashboard,
    GraduationCap,
    Users,
    UserCheck,
    Menu
} from 'lucide-react';

export default function AdminLayout({ children, title = 'پنل مدیریت ادورا' }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [isCollapsed, setIsCollapsed] = useState(false);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const { flash, url } = usePage().props;
    const currentUrl = usePage().url;
    const [showFlash, setShowFlash] = useState(true);

    const isDashboardActive = currentUrl === '/admin' || currentUrl === '/admin/dashboard';
    const isCoursesActive = currentUrl.startsWith('/admin/courses');
    const isStudentsActive = currentUrl.startsWith('/admin/students');
    const isTeachersActive = currentUrl.startsWith('/admin/teachers');

    return (
        <div className="min-h-screen bg-slate-950 text-slate-100 font-sans flex antialiased selection:bg-brand-500 selection:text-white" dir="rtl">
            <Head title={title} />

            {/* Collapsible Persian / English Sidebar */}
            <Sidebar
                isOpen={sidebarOpen}
                setIsOpen={setSidebarOpen}
                isCollapsed={isCollapsed}
                setIsCollapsed={setIsCollapsed}
            />

            {/* Main Application Area */}
            <div
                className={`flex-1 flex flex-col min-w-0 transition-all duration-300 ${
                    isCollapsed ? 'lg:mr-20' : 'lg:mr-72'
                }`}
            >
                {/* Fixed Topbar */}
                <Topbar onToggleSidebar={() => setSidebarOpen(!sidebarOpen)} />

                {/* Flash Messages */}
                {showFlash && (flash?.success || flash?.error || flash?.info) && (
                    <div className="mx-4 sm:mx-8 mt-4">
                        {flash?.success && (
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">
                                <div className="flex items-center gap-3">
                                    <CheckCircle size={20} className="text-emerald-400 shrink-0" />
                                    <span>{flash.success}</span>
                                </div>
                                <button onClick={() => setShowFlash(false)} className="text-emerald-400 hover:text-emerald-200">
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                        {flash?.error && (
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm">
                                <div className="flex items-center gap-3">
                                    <AlertCircle size={20} className="text-rose-400 shrink-0" />
                                    <span>{flash.error}</span>
                                </div>
                                <button onClick={() => setShowFlash(false)} className="text-rose-400 hover:text-rose-200">
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                        {flash?.info && (
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-brand-500/10 border border-brand-500/30 text-brand-300 text-sm">
                                <div className="flex items-center gap-3">
                                    <Info size={20} className="text-brand-400 shrink-0" />
                                    <span>{flash.info}</span>
                                </div>
                                <button onClick={() => setShowFlash(false)} className="text-brand-400 hover:text-brand-200">
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                    </div>
                )}

                {/* Main Content Area */}
                <main className="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto space-y-8 pb-28 lg:pb-8">
                    {children}
                </main>

                {/* Mobile Floating Bottom Navigation Dock */}
                <nav
                    className="lg:hidden fixed bottom-3 inset-x-3 z-40 bg-slate-900/95 backdrop-blur-2xl border border-slate-700/80 rounded-2xl shadow-[0_15px_45px_-5px_rgba(0,0,0,0.65),0_0_20px_rgba(6,182,212,0.15)] px-1.5 py-1.5 flex items-center justify-between gap-1 transition-all select-none"
                    aria-label="Admin Mobile Navigation Dock"
                >
                    <Link
                        href="/admin/dashboard"
                        className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all ${
                            isDashboardActive
                                ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/35 shadow-[0_0_12px_rgba(6,182,212,0.25)]'
                                : 'text-slate-400 hover:text-slate-200'
                        }`}
                    >
                        <LayoutDashboard size={20} className={isDashboardActive ? 'text-cyan-400 drop-shadow-[0_0_8px_#00f0ff]' : ''} />
                        <span className="text-[10px] font-bold tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                            داشبورد
                        </span>
                        {isDashboardActive && <span className="w-1.5 h-1.5 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f0ff] mt-0.5" />}
                    </Link>

                    <Link
                        href="/admin/courses"
                        className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all ${
                            isCoursesActive
                                ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/35 shadow-[0_0_12px_rgba(6,182,212,0.25)]'
                                : 'text-slate-400 hover:text-slate-200'
                        }`}
                    >
                        <GraduationCap size={20} className={isCoursesActive ? 'text-cyan-400 drop-shadow-[0_0_8px_#00f0ff]' : ''} />
                        <span className="text-[10px] font-bold tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                            دوره‌ها
                        </span>
                        {isCoursesActive && <span className="w-1.5 h-1.5 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f0ff] mt-0.5" />}
                    </Link>

                    <Link
                        href="/admin/students"
                        className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all ${
                            isStudentsActive
                                ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/35 shadow-[0_0_12px_rgba(6,182,212,0.25)]'
                                : 'text-slate-400 hover:text-slate-200'
                        }`}
                    >
                        <Users size={20} className={isStudentsActive ? 'text-cyan-400 drop-shadow-[0_0_8px_#00f0ff]' : ''} />
                        <span className="text-[10px] font-bold tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                            دانشجویان
                        </span>
                        {isStudentsActive && <span className="w-1.5 h-1.5 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f0ff] mt-0.5" />}
                    </Link>

                    <Link
                        href="/admin/teachers"
                        className={`flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center transition-all ${
                            isTeachersActive
                                ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/35 shadow-[0_0_12px_rgba(6,182,212,0.25)]'
                                : 'text-slate-400 hover:text-slate-200'
                        }`}
                    >
                        <UserCheck size={20} className={isTeachersActive ? 'text-cyan-400 drop-shadow-[0_0_8px_#00f0ff]' : ''} />
                        <span className="text-[10px] font-bold tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                            اساتید
                        </span>
                        {isTeachersActive && <span className="w-1.5 h-1.5 rounded-full bg-cyan-400 shadow-[0_0_8px_#00f0ff] mt-0.5" />}
                    </Link>

                    <button
                        type="button"
                        onClick={() => setMobileMenuOpen(true)}
                        className="flex-1 min-w-0 flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-center text-slate-400 hover:text-slate-200 transition-all"
                    >
                        <div className="relative">
                            <Menu size={20} />
                            <span className="absolute -top-0.5 -right-0.5 w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping" />
                        </div>
                        <span className="text-[10px] font-bold tracking-tight whitespace-nowrap overflow-hidden text-ellipsis w-full text-center mt-1 leading-none">
                            منو
                        </span>
                    </button>
                </nav>

                {/* Admin Mobile Application Bottom Sheet Menu */}
                <AdminMobileMenu
                    isOpen={mobileMenuOpen}
                    onClose={() => setMobileMenuOpen(false)}
                />

                {/* Modern Footer */}
                <footer className="h-16 border-t border-slate-800/80 px-4 sm:px-8 flex items-center justify-between text-xs text-slate-500 bg-slate-950/40">
                    <div>
                        © {new Date().getFullYear()} <span className="text-slate-400 font-semibold">Edvora Tech</span> — تمامی حقوق محفوظ است.
                    </div>
                    <div className="flex items-center gap-4">
                        <span className="flex items-center gap-1.5">
                            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                            سیستم فعال
                        </span>
                        <span>نسخه ۲.۰ SPA</span>
                    </div>
                </footer>
            </div>
        </div>
    );
}
