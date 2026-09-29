import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    Home,
    LayoutDashboard,
    BookOpen,
    UserCheck,
    HelpCircle,
    Bell,
    UserCog,
    LogOut,
    ChevronRight,
    ChevronLeft,
    X,
    ShieldCheck,
    Sparkles
} from 'lucide-react';


export default function TeacherSidebar({
    isOpen,
    setIsOpen,
    isCollapsed,
    setIsCollapsed
}) {
    const page = usePage();
    const url = page?.url || (typeof window !== 'undefined' ? window.location.pathname : '') || '';
    const props = page?.props || {};
    const auth = props.auth || {};
    const teacherNav = props.teacherNav || {};
    const unreadNotificationsCount = props.unreadNotificationsCount || 0;

    // No submenu – courses dropdown replaced by dedicated hub page

    // Save collapse state in localStorage
    const toggleCollapse = () => {
        const next = !isCollapsed;
        setIsCollapsed(next);
        try {
            localStorage.setItem('teacherSidebarCollapsed', String(next));
        } catch (e) {
            // ignore
        }
    };

    const isDashboardActive = url === '/teacher/dashboard' || url === '/teacher';
    const isYourCoursesActive = url.startsWith('/teacher/your-courses') || url.startsWith('/teacher/courses');
    const isEnrollmentsActive = url.startsWith('/teacher/enrollment-requests');
    const isQuizzesActive = url.startsWith('/teacher/quizzes');
    const isNotificationsActive = url.startsWith('/teacher/notifications');
    const isProfileActive = url.startsWith('/profile');

    const rawCourses = teacherNav?.courses || [];
    const teacherCourses = Array.isArray(rawCourses) ? rawCourses : Object.values(rawCourses || {});
    const totalTeacherCourses = teacherNav?.totalCourses ?? teacherCourses.length;
    const pendingEnrollments = teacherNav?.pendingEnrollmentsCount || 0;
    const unreadNotifications = Number(unreadNotificationsCount) || 0;

    const user = auth?.user || {};
    const userAvatar = user?.avatar_url || (typeof user?.avatar === 'string' && user.avatar
        ? (user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`)
        : null);

    return (
        <>
            {/* Mobile Backdrop Overlay */}
            {isOpen && (
                <div
                    onClick={() => setIsOpen(false)}
                    className="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-xs lg:hidden transition-opacity"
                    aria-hidden="true"
                />
            )}

            {/* Sidebar Shell - Positioned on the RIGHT */}
            <aside
                className={`fixed top-0 bottom-0 right-0 z-50 flex flex-col bg-white dark:bg-[#071328] border-l border-[#E5EAF2] dark:border-cyan-500/20 shadow-2xl lg:shadow-none transition-transform duration-300 ease-in-out select-none
                    ${isOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'}
                    ${isCollapsed ? 'lg:w-20' : 'lg:w-72'}
                    w-72 max-w-[85vw]
                `}
                dir="rtl"
            >
                {/* Desktop Collapse Button */}
                <button
                    onClick={toggleCollapse}
                    type="button"
                    title={isCollapsed ? 'گسترش منو' : 'جمع کردن منو'}
                    className="hidden lg:flex absolute -left-3.5 top-6 z-30 w-7 h-7 bg-white dark:bg-[#091224] border border-[#E5EAF2] dark:border-cyan-500/40 rounded-full items-center justify-center text-slate-500 dark:text-cyan-400 hover:text-[#0A58CA] dark:hover:text-cyan-300 shadow-md dark:shadow-[0_0_15px_rgba(0,240,255,0.3)] transition-all duration-200 hover:scale-110 active:scale-95"
                >
                    {isCollapsed ? (
                        <ChevronLeft size={15} />
                    ) : (
                        <ChevronRight size={15} />
                    )}
                </button>

                {/* Mobile Drawer Header */}
                <div className="flex lg:hidden items-center justify-between p-4 border-b border-[#E5EAF2] dark:border-white/10 bg-slate-50/70 dark:bg-[#030917]/80">
                    <div className="flex items-center gap-2">
                        <div className="w-8 h-8 rounded-xl bg-[#0A58CA]/10 dark:bg-cyan-500/20 text-[#0A58CA] dark:text-cyan-400 dark:border dark:border-cyan-500/30 flex items-center justify-center font-black text-sm">
                            E
                        </div>
                        <div>
                            <span className="text-xs font-black text-[#111827] dark:text-white block">ادورا تک</span>
                            <span className="text-[10px] text-[#0A58CA] dark:text-cyan-400 font-bold">پنل مدرسین</span>
                        </div>
                    </div>
                    <button
                        onClick={() => setIsOpen(false)}
                        className="p-2 rounded-xl bg-slate-100 dark:bg-[#0c1a36] text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-colors"
                        aria-label="بستن منو"
                    >
                        <X size={18} />
                    </button>
                </div>

                {/* Desktop Brand Header */}
                <div className="hidden lg:flex p-4 lg:p-5 border-b border-[#E5EAF2] dark:border-white/10 items-center gap-3">
                    <a href="/" className="flex items-center gap-3 group overflow-hidden">
                        <div className="relative w-10 h-10 rounded-xl overflow-hidden shrink-0 shadow-md shadow-[#0A58CA]/10 dark:shadow-cyan-500/10 border border-[#E5EAF2] dark:border-cyan-500/30 group-hover:scale-105 transition-transform duration-300">
                            <img
                                src="/assets/images/logo1.jpg"
                                alt="Edvora Tech"
                                className="w-full h-full object-cover"
                                onError={(e) => {
                                    e.target.style.display = 'none';
                                    if (e.target.nextElementSibling) {
                                        e.target.nextElementSibling.style.display = 'flex';
                                    }
                                }}
                            />
                            <div className="hidden w-full h-full bg-gradient-to-tr from-[#0A58CA] to-[#1683F7] items-center justify-center text-white font-bold text-base">
                                E
                            </div>
                        </div>

                        {!isCollapsed && (
                            <div className="flex flex-col min-w-0 transition-opacity duration-200">
                                <span className="font-extrabold text-[#111827] dark:text-white text-base leading-tight tracking-tight group-hover:text-[#0A58CA] dark:group-hover:text-cyan-400 transition-colors truncate">
                                    ادورا تک
                                </span>
                                <div className="flex items-center gap-1 mt-0.5">
                                    <ShieldCheck size={12} className="text-[#0A58CA] dark:text-cyan-400 shrink-0" />
                                    <span className="text-[11px] font-bold text-[#0A58CA] dark:text-cyan-400 tracking-wider truncate">
                                        مدرس دوره
                                    </span>
                                </div>
                            </div>
                        )}
                    </a>
                </div>

                {/* Navigation Items (Scrollable) */}
                <div className="flex-1 overflow-y-auto overflow-x-hidden p-3 space-y-5">
                    {/* SECTION: MAIN MENU */}
                    <div>
                        {!isCollapsed && (
                            <div className="px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-400 dark:text-cyan-300/80">
                                منوی اصلی
                            </div>
                        )}
                        <ul className="space-y-1">
                            {/* Home */}
                            <li>
                                <a
                                    href="/"
                                    title={isCollapsed ? 'صفحه اصلی سایت' : undefined}
                                    className="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-[#0A58CA] dark:hover:text-cyan-400 hover:bg-[#F5F8FC] dark:hover:bg-cyan-500/10 transition-all group"
                                >
                                    <div className="w-8 h-8 rounded-lg bg-slate-100 dark:bg-[#0c1a36] flex items-center justify-center text-slate-600 dark:text-slate-300 group-hover:bg-[#0A58CA]/10 dark:group-hover:bg-cyan-500/20 group-hover:text-[#0A58CA] dark:group-hover:text-cyan-400 transition-colors shrink-0">
                                        <Home size={18} />
                                    </div>
                                    {!isCollapsed && <span>صفحه اصلی سایت</span>}
                                </a>
                            </li>

                            {/* Dashboard */}
                            <li>
                                <Link
                                    href="/teacher/dashboard"
                                    onClick={() => setIsOpen(false)}
                                    title={isCollapsed ? 'داشبورد' : undefined}
                                    className={`flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group ${
                                        isDashboardActive
                                            ? 'bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 text-white shadow-md shadow-[#0A58CA]/20 dark:shadow-[0_0_20px_rgba(0,240,255,0.3)]'
                                            : 'text-slate-600 dark:text-slate-300 hover:text-[#0A58CA] dark:hover:text-cyan-400 hover:bg-[#F5F8FC] dark:hover:bg-cyan-500/10'
                                    }`}
                                >
                                    <div
                                        className={`w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors ${
                                            isDashboardActive
                                                ? 'bg-white/20 text-white'
                                                : 'bg-slate-100 dark:bg-[#0c1a36] text-slate-600 dark:text-slate-300 group-hover:bg-[#0A58CA]/10 dark:group-hover:bg-cyan-500/20 group-hover:text-[#0A58CA] dark:group-hover:text-cyan-400'
                                        }`}
                                    >
                                        <LayoutDashboard size={18} />
                                    </div>
                                    {!isCollapsed && <span>داشبورد اساتید</span>}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    {/* SECTION: TEACHING */}
                    <div>
                        {!isCollapsed && (
                            <div className="px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-400 dark:text-cyan-300/80">
                                مدیریت تدریس
                            </div>
                        )}
                        <ul className="space-y-1">
                            {/* My Courses – Direct Link to hub page */}
                            <li>
                                <Link
                                    href="/teacher/your-courses"
                                    onClick={() => setIsOpen(false)}
                                    title={isCollapsed ? 'دوره‌های من' : undefined}
                                    className={`flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group ${
                                        isYourCoursesActive
                                            ? 'bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 text-white shadow-md shadow-[#0A58CA]/20 dark:shadow-[0_0_20px_rgba(0,240,255,0.3)]'
                                            : 'text-slate-600 dark:text-slate-300 hover:text-[#0A58CA] dark:hover:text-cyan-400 hover:bg-[#F5F8FC] dark:hover:bg-cyan-500/10'
                                    }`}
                                >
                                    <div className="flex items-center gap-3 min-w-0">
                                        <div
                                            className={`w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors ${
                                                isYourCoursesActive
                                                    ? 'bg-white/20 text-white'
                                                    : 'bg-slate-100 dark:bg-[#0c1a36] text-slate-600 dark:text-slate-300 group-hover:bg-[#0A58CA]/10 dark:group-hover:bg-cyan-500/20 group-hover:text-[#0A58CA] dark:group-hover:text-cyan-400'
                                            }`}
                                        >
                                            <BookOpen size={18} />
                                        </div>
                                        {!isCollapsed && <span className="truncate">دوره‌های من</span>}
                                    </div>
                                    {!isCollapsed && totalTeacherCourses > 0 && (
                                        <span className={`px-1.5 py-0.5 rounded-full text-[10px] font-bold ${
                                            isYourCoursesActive
                                                ? 'bg-white/25 text-white'
                                                : 'bg-[#0A58CA]/10 dark:bg-cyan-500/20 text-[#0A58CA] dark:text-cyan-400 dark:border dark:border-cyan-500/30'
                                        }`}>
                                            {totalTeacherCourses}
                                        </span>
                                    )}
                                </Link>
                            </li>

                            {/* Enrollment Requests */}
                            <li>
                                <Link
                                    href="/teacher/enrollment-requests"
                                    onClick={() => setIsOpen(false)}
                                    title={isCollapsed ? 'درخواست‌های ثبت‌نام' : undefined}
                                    className={`flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group ${
                                        isEnrollmentsActive
                                            ? 'bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 text-white shadow-md shadow-[#0A58CA]/20 dark:shadow-[0_0_20px_rgba(0,240,255,0.3)]'
                                            : 'text-slate-600 dark:text-slate-300 hover:text-[#0A58CA] dark:hover:text-cyan-400 hover:bg-[#F5F8FC] dark:hover:bg-cyan-500/10'
                                    }`}
                                >
                                    <div className="flex items-center gap-3 min-w-0">
                                        <div
                                            className={`w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors ${
                                                isEnrollmentsActive
                                                    ? 'bg-white/20 text-white'
                                                    : 'bg-slate-100 dark:bg-[#0c1a36] text-slate-600 dark:text-slate-300 group-hover:bg-[#0A58CA]/10 dark:group-hover:bg-cyan-500/20 group-hover:text-[#0A58CA] dark:group-hover:text-cyan-400'
                                            }`}
                                        >
                                            <UserCheck size={18} />
                                        </div>
                                        {!isCollapsed && <span className="truncate">درخواست‌های ثبت‌نام</span>}
                                    </div>
                                    {!isCollapsed && pendingEnrollments > 0 && (
                                        <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F59E0B] text-white animate-pulse">
                                            {pendingEnrollments}
                                        </span>
                                    )}
                                </Link>
                            </li>

                            {/* Quizzes */}
                            <li>
                                <Link
                                    href="/teacher/quizzes"
                                    onClick={() => setIsOpen(false)}
                                    title={isCollapsed ? 'آزمون‌ها و کوییزها' : undefined}
                                    className={`flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group ${
                                        isQuizzesActive
                                            ? 'bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 text-white shadow-md shadow-[#0A58CA]/20 dark:shadow-[0_0_20px_rgba(0,240,255,0.3)]'
                                            : 'text-slate-600 dark:text-slate-300 hover:text-[#0A58CA] dark:hover:text-cyan-400 hover:bg-[#F5F8FC] dark:hover:bg-cyan-500/10'
                                    }`}
                                >
                                    <div
                                        className={`w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors ${
                                            isQuizzesActive
                                                ? 'bg-white/20 text-white'
                                                : 'bg-slate-100 dark:bg-[#0c1a36] text-slate-600 dark:text-slate-300 group-hover:bg-[#0A58CA]/10 dark:group-hover:bg-cyan-500/20 group-hover:text-[#0A58CA] dark:group-hover:text-cyan-400'
                                        }`}
                                    >
                                        <HelpCircle size={18} />
                                    </div>
                                    {!isCollapsed && <span>آزمون‌ها و کوییزها</span>}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    {/* SECTION: ACCOUNT */}
                    <div>
                        {!isCollapsed && (
                            <div className="px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-400 dark:text-cyan-300/80">
                                حساب کاربری
                            </div>
                        )}
                        <ul className="space-y-1">
                            {/* Notifications */}
                            <li>
                                <Link
                                    href="/teacher/notifications"
                                    onClick={() => setIsOpen(false)}
                                    title={isCollapsed ? 'اعلان‌ها' : undefined}
                                    className={`flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group ${
                                        isNotificationsActive
                                            ? 'bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 text-white shadow-md shadow-[#0A58CA]/20 dark:shadow-[0_0_20px_rgba(0,240,255,0.3)]'
                                            : 'text-slate-600 dark:text-slate-300 hover:text-[#0A58CA] dark:hover:text-cyan-400 hover:bg-[#F5F8FC] dark:hover:bg-cyan-500/10'
                                    }`}
                                >
                                    <div className="flex items-center gap-3 min-w-0">
                                        <div
                                            className={`w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors ${
                                                isNotificationsActive
                                                    ? 'bg-white/20 text-white'
                                                    : 'bg-slate-100 dark:bg-[#0c1a36] text-slate-600 dark:text-slate-300 group-hover:bg-[#0A58CA]/10 dark:group-hover:bg-cyan-500/20 group-hover:text-[#0A58CA] dark:group-hover:text-cyan-400'
                                            }`}
                                        >
                                            <Bell size={18} />
                                        </div>
                                        {!isCollapsed && <span>اعلان‌ها</span>}
                                    </div>
                                    {!isCollapsed && unreadNotifications > 0 && (
                                        <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EF4444] text-white">
                                            {unreadNotifications}
                                        </span>
                                    )}
                                </Link>
                            </li>

                            {/* Profile */}
                            <li>
                                <Link
                                    href="/profile"
                                    onClick={() => setIsOpen(false)}
                                    title={isCollapsed ? 'پروفایل و تنظیمات' : undefined}
                                    className={`flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group ${
                                        isProfileActive
                                            ? 'bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 text-white shadow-md shadow-[#0A58CA]/20 dark:shadow-[0_0_20px_rgba(0,240,255,0.3)]'
                                            : 'text-slate-600 dark:text-slate-300 hover:text-[#0A58CA] dark:hover:text-cyan-400 hover:bg-[#F5F8FC] dark:hover:bg-cyan-500/10'
                                    }`}
                                >
                                    <div
                                        className={`w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors ${
                                            isProfileActive
                                                ? 'bg-white/20 text-white'
                                                : 'bg-slate-100 dark:bg-[#0c1a36] text-slate-600 dark:text-slate-300 group-hover:bg-[#0A58CA]/10 dark:group-hover:bg-cyan-500/20 group-hover:text-[#0A58CA] dark:group-hover:text-cyan-400'
                                        }`}
                                    >
                                        <UserCog size={18} />
                                    </div>
                                    {!isCollapsed && <span>پروفایل و تنظیمات</span>}
                                </Link>
                            </li>

                            {/* Logout via Inertia POST */}
                            <li>
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    onClick={() => setIsOpen(false)}
                                    title={isCollapsed ? 'خروج' : undefined}
                                    className="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-[#EF4444] dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/15 transition-colors group text-right"
                                >
                                    <div className="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-500/20 flex items-center justify-center text-[#EF4444] dark:text-rose-400 group-hover:bg-rose-100 dark:group-hover:bg-rose-500/30 transition-colors shrink-0">
                                        <LogOut size={18} />
                                    </div>
                                    {!isCollapsed && <span>خروج از حساب</span>}
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>

                {/* Footer User Info */}
                {!isCollapsed && user && user.name && (
                    <div className="p-3 border-t border-[#E5EAF2] dark:border-white/10 bg-[#F5F8FC]/70 dark:bg-[#040e24]/80 mt-auto">
                        <div className="flex items-center gap-3 p-2 rounded-xl bg-white dark:bg-[#071328] border border-[#E5EAF2] dark:border-cyan-500/30 dark:shadow-[0_0_15px_rgba(0,240,255,0.1)] shadow-xs">
                            <div className="relative w-9 h-9 rounded-full overflow-hidden shrink-0 border border-[#0A58CA]/30 dark:border-cyan-500/40">
                                {userAvatar ? (
                                    <img
                                        src={userAvatar}
                                        alt={user.name}
                                        className="w-full h-full object-cover"
                                    />
                                ) : (
                                    <div className="w-full h-full bg-[#0A58CA] dark:bg-cyan-600 text-white flex items-center justify-center font-bold text-xs">
                                        {user.name?.charAt(0) || 'M'}
                                    </div>
                                )}
                                <span className="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full ring-2 ring-white dark:ring-slate-900" />
                            </div>
                            <div className="flex-1 min-w-0">
                                <p className="text-xs font-bold text-[#111827] dark:text-white truncate leading-tight">
                                    {user.name}
                                </p>
                                <p className="text-[10px] text-slate-400 dark:text-cyan-300/70 truncate" dir="ltr">
                                    {user.email || ''}
                                </p>
                            </div>
                        </div>
                    </div>
                )}
            </aside>
        </>
    );
}
