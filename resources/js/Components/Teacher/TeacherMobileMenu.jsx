import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import PwaInstallButton from '@/Components/PwaInstallButton';
import {
    X,
    LayoutDashboard,
    BookOpen,
    UserCheck,
    ClipboardCheck,
    Bell,
    UserCog,
    ExternalLink,
    LogOut,
    ChevronLeft,
    Sparkles,
    Sun,
    Moon
} from 'lucide-react';

export default function TeacherMobileMenu({ isOpen, setIsOpen }) {
    const { auth, teacherNav, unreadNotificationsCount } = usePage().props;

    if (!isOpen) return null;

    const user = auth?.user;
    const pendingEnrollments = teacherNav?.pendingEnrollmentsCount || 0;
    const unread = unreadNotificationsCount || 0;

    const userAvatar = user?.avatar_url || (user?.avatar
        ? (user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`)
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'Instructor')}&background=0A58CA&color=fff&size=100`);

    const close = () => setIsOpen(false);

    return (
        <div className="lg:hidden fixed inset-0 z-50 flex flex-col justify-end" dir="rtl">
            {/* Backdrop */}
            <div
                className="fixed inset-0 bg-slate-950/75 backdrop-blur-md transition-opacity duration-300"
                onClick={close}
            />

            {/* Bottom Sheet Modal Container */}
            <div
                className="relative z-10 w-full max-h-[92vh] bg-white border-t border-[#E5EAF2] rounded-t-[32px] shadow-[0_-20px_60px_-15px_rgba(0,0,0,0.5)] flex flex-col overflow-hidden animate-in slide-in-from-bottom duration-300"
            >
                {/* Drag Handle Indicator */}
                <div className="w-full flex items-center justify-center pt-3 pb-1" onClick={close}>
                    <div className="w-12 h-1.5 rounded-full bg-slate-300 cursor-pointer" />
                </div>

                {/* Header with User Info & Close Button */}
                <div className="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between gap-3">
                    <div className="flex items-center gap-3 min-w-0">
                        <div className="relative w-12 h-12 rounded-2xl overflow-hidden ring-2 ring-[#0A58CA]/30 shrink-0 shadow-md">
                            <img src={userAvatar} alt={user?.name || 'Instructor'} className="w-full h-full object-cover" />
                            <span className="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 rounded-full ring-2 ring-white" />
                        </div>
                        <div className="flex flex-col min-w-0">
                            <div className="flex items-center gap-1.5">
                                <h3 className="text-sm font-black text-slate-800 truncate">
                                    {user?.name || 'استاد محترم'}
                                </h3>
                                <Sparkles size={13} className="text-[#0A58CA] shrink-0" />
                            </div>
                            <span className="text-[11px] font-bold text-[#0A58CA] mt-0.5">
                                پنل مدیریت اساتید و مدرسین ادورا
                            </span>
                        </div>
                    </div>

                    <button
                        type="button"
                        onClick={close}
                        className="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors shrink-0"
                        aria-label="بستن"
                    >
                        <X size={18} />
                    </button>
                </div>

                {/* Scrollable Menu Body */}
                <div className="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4 overscroll-contain">
                    {/* PWA Install Promotion Banner */}
                    <PwaInstallButton variant="menu" />

                    {/* Section 1: Teaching & Courses */}
                    <div>
                        <p className="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 px-2 mb-2">
                            بخش‌های تدریس و صنف‌ها
                        </p>
                        <div className="bg-slate-50 border border-slate-200/70 rounded-2xl overflow-hidden divide-y divide-slate-200/50">
                            <Link
                                href="/teacher/dashboard"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-[#0A58CA]/10 text-[#0A58CA] flex items-center justify-center">
                                        <LayoutDashboard size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800">داشبورد اصلی مدرس</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </Link>

                            <Link
                                href="/teacher/your-courses"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
                                        <BookOpen size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800">دوره‌های آموزشی من</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </Link>

                            <Link
                                href="/teacher/enrollment-requests"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                                        <UserCheck size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800">درخواست‌های ثبت‌نام</span>
                                </div>
                                <div className="flex items-center gap-2">
                                    {pendingEnrollments > 0 && (
                                        <span className="px-2 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-black">
                                            {pendingEnrollments}
                                        </span>
                                    )}
                                    <ChevronLeft size={16} className="text-slate-400" />
                                </div>
                            </Link>

                            <Link
                                href="/teacher/quizzes"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                                        <ClipboardCheck size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800">آزمون‌ها و سوالات</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </Link>
                        </div>
                    </div>

                    {/* Section 2: Account & Actions */}
                    <div>
                        <p className="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 px-2 mb-2">
                            حساب کاربری و تنظیمات
                        </p>
                        <div className="bg-slate-50 border border-slate-200/70 rounded-2xl overflow-hidden divide-y divide-slate-200/50">
                            <a
                                href="/teacher/notifications"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center">
                                        <Bell size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800">اعلانات و پیام‌ها</span>
                                </div>
                                <div className="flex items-center gap-2">
                                    {unread > 0 && (
                                        <span className="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black">
                                            {unread}
                                        </span>
                                    )}
                                    <ChevronLeft size={16} className="text-slate-400" />
                                </div>
                            </a>

                            <Link
                                href="/profile"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center">
                                        <UserCog size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800">پروفایل و تنظیمات</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </Link>

                            <a
                                href="/"
                                target="_blank"
                                rel="noreferrer"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-600 flex items-center justify-center">
                                        <ExternalLink size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800">مشاهده وب‌سایت</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </a>
                        </div>
                    </div>

                    {/* Logout Action Button */}
                    <div className="pt-2 pb-6">
                        <Link
                            method="post"
                            as="button"
                            href="/logout"
                            onClick={close}
                            className="w-full flex items-center justify-center gap-2 p-3.5 rounded-2xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/25 text-rose-600 text-xs font-black transition-colors"
                        >
                            <LogOut size={16} />
                            <span>خروج از حساب کاربری</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}
