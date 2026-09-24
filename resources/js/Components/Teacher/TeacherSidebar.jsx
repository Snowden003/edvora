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
    ChevronDown,
    Eye,
    MessageSquare,
    Grid,
    X,
    ShieldCheck
} from 'lucide-react';

export default function TeacherSidebar({
    isOpen,
    setIsOpen,
    isCollapsed,
    setIsCollapsed
}) {
    const { url } = usePage();
    const { auth, teacherNav, unreadNotificationsCount } = usePage().props;
    const [coursesSubmenuOpen, setCoursesSubmenuOpen] = useState(
        url.startsWith('/teacher/courses') || url.startsWith('/teacher/your-courses')
    );

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

    const teacherCourses = teacherNav?.courses || [];
    const totalTeacherCourses = teacherNav?.totalCourses || teacherCourses.length;
    const pendingEnrollments = teacherNav?.pendingEnrollmentsCount || 0;
    const unreadNotifications = unreadNotificationsCount || 0;

    return (
        <>
            {/* Mobile Backdrop Overlay */}
            {isOpen && (
                <div
                    onClick={() => setIsOpen(false)}
                    className="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-sm lg:hidden transition-opacity"
                    aria-hidden="true"
                />
            )}

            {/* Sidebar Shell - Positioned on the RIGHT */}
            <aside
                className={`fixed top-0 bottom-0 right-0 z-50 flex flex-col bg-white border-l border-[#E5EAF2] shadow-xl lg:shadow-none transition-all duration-300 ease-in-out select-none
                    ${isOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'}
                    ${isCollapsed ? 'lg:w-20' : 'lg:w-72'}
                    w-72
                `}
                dir="rtl"
            >
                {/* Desktop Collapse Button - on the left edge of the right-sidebar */}
                <button
                    onClick={toggleCollapse}
                    type="button"
                    title={isCollapsed ? 'گسترش منو' : 'جمع کردن منو'}
                    className="hidden lg:flex absolute -left-3.5 top-6 z-30 w-7 h-7 bg-white border border-[#E5EAF2] rounded-full items-center justify-center text-slate-500 hover:text-[#0A58CA] hover:border-[#0A58CA]/50 shadow-md transition-all duration-200 hover:scale-110 active:scale-95"
                >
                    {isCollapsed ? (
                        <ChevronLeft size={15} />
                    ) : (
                        <ChevronRight size={15} />
                    )}
                </button>

                {/* Mobile Header */}
                <div className="flex lg:hidden items-center justify-between p-4 border-b border-[#E5EAF2]">
                    <span className="text-xs font-bold text-slate-500">منوی ناوبری</span>
                    <button
                        onClick={() => setIsOpen(false)}
                        className="p-1.5 rounded-xl bg-slate-100 text-slate-500 hover:text-slate-800 hover:bg-slate-200 transition-colors"
                        aria-label="بستن منو"
                    >
                        <X size={18} />
                    </button>
                </div>

                {/* Brand Header */}
                <div className="p-4 lg:p-5 border-b border-[#E5EAF2] flex items-center gap-3">
                    <a href="/" className="flex items-center gap-3 group overflow-hidden">
                        <div className="relative w-10 h-10 rounded-xl overflow-hidden shrink-0 shadow-md shadow-[#0A58CA]/10 border border-[#E5EAF2] group-hover:scale-105 transition-transform duration-300">
                            <img
                                src="/assets/images/logo1.jpg"
                                alt="Edvora Tech"
                                className="w-full h-full object-cover"
                                onError={(e) => {
                                    e.target.style.display = 'none';
                                    e.target.nextElementSibling.style.display = 'flex';
                                }}
                            />
                            <div className="hidden w-full h-full bg-gradient-to-tr from-[#0A58CA] to-[#1683F7] items-center justify-center text-white font-bold text-base">
                                E
                            </div>
                        </div>

                        {!isCollapsed && (
                            <div className="flex flex-col min-w-0 transition-opacity duration-200">
                                <span className="font-extrabold text-[#111827] text-base leading-tight tracking-tight group-hover:text-[#0A58CA] transition-colors truncate">
                                    ادورا تک
                                </span>
                                <div className="flex items-center gap-1 mt-0.5">
                                    <ShieldCheck size={12} className="text-[#0A58CA] shrink-0" />
                                    <span className="text-[11px] font-bold text-[#0A58CA] tracking-wider truncate">
                                        مدرس دوره
                                    </span>
                                </div>
                            </div>
                        )}
                    </a>
                </div>

                {/* Navigation Items (Scrollable) */}
                <div className="flex-1 overflow-y-auto overflow-x-hidden p-3 space-y-6">
                    {/* SECTION: MAIN MENU */}
                    <div>
                        {!isCollapsed && (
                            <div className="px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-400">
                                منوی اصلی
                            </div>
                        )}
                        <ul className="space-y-1">
                            {/* Home */}
                            <li>
                                <a
                                    href="/"
                                    title={isCollapsed ? 'خانه' : undefined}
                                    className="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-[#0A58CA] hover:bg-[#F5F8FC] transition-all group"
                                >
                                    <div className="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 group-hover:bg-[#0A58CA]/10 group-hover:text-[#0A58CA] transition-colors shrink-0">
                                        <Home size={18} />
                                    </div>
                                    {!isCollapsed && <span>خانه</span>}
                                </a>
                            </li>

                            {/* Dashboard */}
                            <li>
                                <Link
                                    href="/teacher/dashboard"
                                    title={isCollapsed ? 'داشبورد' : undefined}
                                    className={`flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group ${
                                        isDashboardActive
                                            ? 'bg-[#0A58CA] text-white shadow-md shadow-[#0A58CA]/20'
                                            : 'text-slate-600 hover:text-[#0A58CA] hover:bg-[#F5F8FC]'
                                    }`}
                                >
                                    <div
                                        className={`w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors ${
                                            isDashboardActive
                                                ? 'bg-white/20 text-white'
                                                : 'bg-slate-100 text-slate-600 group-hover:bg-[#0A58CA]/10 group-hover:text-[#0A58CA]'
                                        }`}
                                    >
                                        <LayoutDashboard size={18} />
                                    </div>
                                    {!isCollapsed && <span>داشبورد</span>}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    {/* SECTION: TEACHING */}
                    <div>
                        {!isCollapsed && (
                            <div className="px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-400">
                                تدریس
                            </div>
                        )}
                        <ul className="space-y-1">
                            {/* My Courses dropdown */}
                            <li>
                                <button
                                    onClick={() => setCoursesSubmenuOpen(!coursesSubmenuOpen)}
                                    type="button"
                                    title={isCollapsed ? 'دوره‌های من' : undefined}
                                    className={`w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group ${
                                        isYourCoursesActive && !coursesSubmenuOpen
                                            ? 'bg-[#0A58CA]/10 text-[#0A58CA]'
                                            : 'text-slate-600 hover:text-[#0A58CA] hover:bg-[#F5F8FC]'
                                    }`}
                                >
                                    <div className="flex items-center gap-3 min-w-0">
                                        <div
                                            className={`w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors ${
                                                isYourCoursesActive
                                                    ? 'bg-[#0A58CA]/15 text-[#0A58CA]'
                                                    : 'bg-slate-100 text-slate-600 group-hover:bg-[#0A58CA]/10 group-hover:text-[#0A58CA]'
                                            }`}
                                        >
                                            <BookOpen size={18} />
                                        </div>
                                        {!isCollapsed && (
                                            <span className="truncate">دوره‌های من</span>
                                        )}
                                    </div>
                                    {!isCollapsed && (
                                        <div className="flex items-center gap-1.5 shrink-0">
                                            {totalTeacherCourses > 0 && (
                                                <span className="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-[#0A58CA]/10 text-[#0A58CA]">
                                                    {totalTeacherCourses}
                                                </span>
                                            )}
                                            <ChevronDown
                                                size={15}
                                                className={`text-slate-400 transition-transform duration-200 ${
                                                    coursesSubmenuOpen ? 'rotate-180' : ''
                                                }`}
                                            />
                                        </div>
                                    )}
                                </button>

                                {/* Dropdown Submenu */}
                                {coursesSubmenuOpen && !isCollapsed && (
                                    <div className="mt-1 mr-4 pr-3 border-r-2 border-[#E5EAF2] space-y-1 py-1">
                                        <Link
                                            href="/teacher/your-courses"
                                            className={`flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition-colors ${
                                                url === '/teacher/your-courses'
                                                    ? 'bg-[#0A58CA]/10 text-[#0A58CA]'
                                                    : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'
                                            }`}
                                        >
                                            <div className="flex items-center gap-2">
                                                <Grid size={14} className="text-slate-400" />
                                                <span>همه دوره‌ها</span>
                                            </div>
                                            <span className="px-1.5 py-0.2 bg-white border border-[#E5EAF2] rounded text-[10px] text-slate-600 font-bold">
                                                {totalTeacherCourses}
                                            </span>
                                        </Link>

                                        {teacherCourses.slice(0, 6).map((course) => {
                                            const isThisCourse = url.includes(`/teacher/courses/${course.id}`);
                                            return (
                                                <div
                                                    key={course.id}
                                                    className={`group/course rounded-lg p-2 transition-all ${
                                                        isThisCourse ? 'bg-slate-100/80' : 'hover:bg-slate-50'
                                                    }`}
                                                >
                                                    <div className="flex items-center justify-between gap-1 mb-1">
                                                        <a
                                                            href={`/teacher/courses/${course.id}`}
                                                            className="text-xs font-medium text-slate-700 hover:text-[#0A58CA] truncate flex-1 block"
                                                            title={course.title}
                                                        >
                                                            {course.title}
                                                        </a>
                                                    </div>
                                                    <div className="flex items-center gap-1.5 pt-1">
                                                        <a
                                                            href={`/teacher/courses/${course.id}`}
                                                            className="flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 hover:bg-[#0A58CA]/10 text-slate-600 hover:text-[#0A58CA] transition-colors"
                                                            title="مشاهده جزئیات دوره"
                                                        >
                                                            <Eye size={11} />
                                                            <span>جزئیات</span>
                                                        </a>
                                                        <a
                                                            href={`/courses/${course.id}/chat`}
                                                            className="flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 transition-colors"
                                                            title="گفتگوی زنده دوره"
                                                        >
                                                            <MessageSquare size={11} />
                                                            <span>چت صنف</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>
                                )}
                            </li>

                            {/* Enrollment Requests / Enrolled */}
                            <li>
                                <a
                                    href="/teacher/enrollment-requests"
                                    title={isCollapsed ? 'ثبت‌نام‌ها' : undefined}
                                    className={`flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group ${
                                        isEnrollmentsActive
                                            ? 'bg-[#0A58CA] text-white shadow-md shadow-[#0A58CA]/20'
                                            : 'text-slate-600 hover:text-[#0A58CA] hover:bg-[#F5F8FC]'
                                    }`}
                                >
                                    <div className="flex items-center gap-3 min-w-0">
                                        <div
                                            className={`w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors ${
                                                isEnrollmentsActive
                                                    ? 'bg-white/20 text-white'
                                                    : 'bg-slate-100 text-slate-600 group-hover:bg-[#0A58CA]/10 group-hover:text-[#0A58CA]'
                                            }`}
                                        >
                                            <UserCheck size={18} />
                                        </div>
                                        {!isCollapsed && <span className="truncate">ثبت‌نام‌ها</span>}
                                    </div>
                                    {!isCollapsed && pendingEnrollments > 0 && (
                                        <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F59E0B] text-white animate-pulse">
                                            {pendingEnrollments}
                                        </span>
                                    )}
                                </a>
                            </li>

                            {/* Quizzes */}
                            <li>
                                <a
                                    href="/teacher/quizzes"
                                    title={isCollapsed ? 'آزمون‌ها' : undefined}
                                    className={`flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group ${
                                        isQuizzesActive
                                            ? 'bg-[#0A58CA] text-white shadow-md shadow-[#0A58CA]/20'
                                            : 'text-slate-600 hover:text-[#0A58CA] hover:bg-[#F5F8FC]'
                                    }`}
                                >
                                    <div
                                        className={`w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors ${
                                            isQuizzesActive
                                                ? 'bg-white/20 text-white'
                                                : 'bg-slate-100 text-slate-600 group-hover:bg-[#0A58CA]/10 group-hover:text-[#0A58CA]'
                                        }`}
                                    >
                                        <HelpCircle size={18} />
                                    </div>
                                    {!isCollapsed && <span>آزمون‌ها</span>}
                                </a>
                            </li>
                        </ul>
                    </div>

                    {/* SECTION: ACCOUNT */}
                    <div>
                        {!isCollapsed && (
                            <div className="px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-400">
                                حساب کاربری
                            </div>
                        )}
                        <ul className="space-y-1">
                            {/* Notifications */}
                            <li>
                                <a
                                    href="/teacher/notifications"
                                    title={isCollapsed ? 'اعلان‌ها' : undefined}
                                    className={`flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group ${
                                        isNotificationsActive
                                            ? 'bg-[#0A58CA] text-white shadow-md shadow-[#0A58CA]/20'
                                            : 'text-slate-600 hover:text-[#0A58CA] hover:bg-[#F5F8FC]'
                                    }`}
                                >
                                    <div className="flex items-center gap-3 min-w-0">
                                        <div
                                            className={`w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors ${
                                                isNotificationsActive
                                                    ? 'bg-white/20 text-white'
                                                    : 'bg-slate-100 text-slate-600 group-hover:bg-[#0A58CA]/10 group-hover:text-[#0A58CA]'
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
                                </a>
                            </li>

                            {/* Profile */}
                            <li>
                                <a
                                    href="/profile"
                                    title={isCollapsed ? 'پروفایل' : undefined}
                                    className={`flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group ${
                                        isProfileActive
                                            ? 'bg-[#0A58CA] text-white shadow-md shadow-[#0A58CA]/20'
                                            : 'text-slate-600 hover:text-[#0A58CA] hover:bg-[#F5F8FC]'
                                    }`}
                                >
                                    <div
                                        className={`w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors ${
                                            isProfileActive
                                                ? 'bg-white/20 text-white'
                                                : 'bg-slate-100 text-slate-600 group-hover:bg-[#0A58CA]/10 group-hover:text-[#0A58CA]'
                                        }`}
                                    >
                                        <UserCog size={18} />
                                    </div>
                                    {!isCollapsed && <span>پروفایل</span>}
                                </a>
                            </li>

                            {/* Logout */}
                            <li>
                                <form action="/logout" method="POST" className="w-full">
                                    <input
                                        type="hidden"
                                        name="_token"
                                        value={document.querySelector('meta[name="csrf-token"]')?.content || ''}
                                    />
                                    <button
                                        type="submit"
                                        title={isCollapsed ? 'خروج' : undefined}
                                        className="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-[#EF4444] hover:bg-rose-50 transition-colors group"
                                    >
                                        <div className="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-[#EF4444] group-hover:bg-rose-100 transition-colors shrink-0">
                                            <LogOut size={18} />
                                        </div>
                                        {!isCollapsed && <span>خروج</span>}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                {/* Footer User Info */}
                {!isCollapsed && auth?.user && (
                    <div className="p-3 border-t border-[#E5EAF2] bg-[#F5F8FC]/70">
                        <div className="flex items-center gap-3 p-2 rounded-xl bg-white border border-[#E5EAF2] shadow-xs">
                            <div className="relative w-9 h-9 rounded-full overflow-hidden shrink-0 border border-[#0A58CA]/30">
                                {auth.user.avatar || auth.user.avatar_url ? (
                                    <img
                                        src={auth.user.avatar_url || (auth.user.avatar.startsWith('http') ? auth.user.avatar : `/storage/${auth.user.avatar}`)}
                                        alt={auth.user.name}
                                        className="w-full h-full object-cover"
                                    />
                                ) : (
                                    <div className="w-full h-full bg-[#0A58CA] text-white flex items-center justify-center font-bold text-xs">
                                        {auth.user.name?.charAt(0) || 'U'}
                                    </div>
                                )}
                                <span className="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full ring-2 ring-white" />
                            </div>
                            <div className="flex-1 min-w-0">
                                <p className="text-xs font-bold text-[#111827] truncate leading-tight">
                                    {auth.user.name}
                                </p>
                                <p className="text-[10px] text-slate-400 truncate">
                                    {auth.user.email}
                                </p>
                            </div>
                        </div>
                    </div>
                )}
            </aside>
        </>
    );
}
