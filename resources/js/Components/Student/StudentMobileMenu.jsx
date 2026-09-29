import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { useLanguage } from '@/Context/LanguageContext';
import { useTheme } from '@/Context/ThemeContext';
import PwaInstallButton from '@/Components/PwaInstallButton';
import {
    X,
    LayoutDashboard,
    BookOpen,
    ClipboardCheck,
    Award,
    Trophy,
    Compass,
    Bell,
    User,
    FileText,
    HelpCircle,
    LogOut,
    Sun,
    Moon,
    Globe,
    ChevronLeft,
    Sparkles,
    Shield
} from 'lucide-react';

export default function StudentMobileMenu({ isOpen, setIsOpen }) {
    const { url } = usePage();
    const { auth, studentNav, unreadNotificationsCount } = usePage().props;
    const { t, isRtl, lang, setLang } = useLanguage();
    const { isDark, toggleTheme } = useTheme();

    if (!isOpen) return null;

    const user = auth?.user;
    const studentLevel = studentNav?.level || { level: 1, title: 'Scholar', progress: 0 };
    const unread = unreadNotificationsCount || 0;

    const userAvatar = user?.avatar_url || (user?.avatar
        ? (user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`)
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'Student')}&background=1f8fff&color=fff&size=100`);

    const close = () => setIsOpen(false);

    return (
        <div className="lg:hidden fixed inset-0 z-50 flex flex-col justify-end" dir={isRtl ? 'rtl' : 'ltr'}>
            {/* Backdrop */}
            <div
                className="fixed inset-0 bg-slate-950/75 backdrop-blur-md transition-opacity duration-300"
                onClick={close}
            />

            {/* Bottom Sheet Modal Container */}
            <div
                className="relative z-10 w-full max-h-[92vh] bg-white dark:bg-[#071126] border-t border-slate-200/80 dark:border-cyan-500/25 rounded-t-[32px] shadow-[0_-20px_60px_-15px_rgba(0,0,0,0.5)] flex flex-col overflow-hidden animate-in slide-in-from-bottom duration-300"
            >
                {/* Drag Handle Indicator */}
                <div className="w-full flex items-center justify-center pt-3 pb-1" onClick={close}>
                    <div className="w-12 h-1.5 rounded-full bg-slate-300 dark:bg-slate-700 cursor-pointer" />
                </div>

                {/* Header with User Info & Close Button */}
                <div className="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-3">
                    <div className="flex items-center gap-3 min-w-0">
                        <div className="relative w-12 h-12 rounded-2xl overflow-hidden ring-2 ring-cyan-500/40 shrink-0 shadow-md">
                            <img src={userAvatar} alt={user?.name || 'Student'} className="w-full h-full object-cover" />
                            <span className="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 rounded-full ring-2 ring-white dark:ring-slate-900" />
                        </div>
                        <div className="flex flex-col min-w-0">
                            <div className="flex items-center gap-1.5">
                                <h3 className="text-sm font-black text-slate-800 dark:text-white truncate">
                                    {user?.name || 'دانش‌آموز'}
                                </h3>
                                <Sparkles size={13} className="text-cyan-500 shrink-0" />
                            </div>
                            <span className="text-[11px] font-bold text-cyan-600 dark:text-cyan-400 mt-0.5">
                                🔥 {studentLevel.progress || 0} XP · سطح {studentLevel.level || 1} ({studentLevel.title || 'Scholar'})
                            </span>
                        </div>
                    </div>

                    <button
                        type="button"
                        onClick={close}
                        className="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white flex items-center justify-center transition-colors shrink-0"
                        aria-label="بستن"
                    >
                        <X size={18} />
                    </button>
                </div>

                {/* Scrollable Menu Body */}
                <div className="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4 overscroll-contain">
                    {/* PWA Install Promotion Banner */}
                    <PwaInstallButton variant="menu" />

                    {/* Quick Setting Pills */}
                    <div className="grid grid-cols-2 gap-2.5">
                        <button
                            type="button"
                            onClick={toggleTheme}
                            className="flex items-center justify-between p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 text-xs font-bold text-slate-700 dark:text-slate-200"
                        >
                            <span className="flex items-center gap-2">
                                {isDark ? <Sun size={16} className="text-amber-400" /> : <Moon size={16} className="text-slate-600" />}
                                {isDark ? 'حالت روز' : 'حالت شب'}
                            </span>
                            <span className="text-[10px] px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                {isDark ? 'روشن' : 'تاریک'}
                            </span>
                        </button>

                        <button
                            type="button"
                            onClick={() => setLang(lang === 'fa' ? 'en' : 'fa')}
                            className="flex items-center justify-between p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 text-xs font-bold text-slate-700 dark:text-slate-200"
                        >
                            <span className="flex items-center gap-2">
                                <Globe size={16} className="text-cyan-500" />
                                {lang === 'fa' ? 'زبان برنامه' : 'Language'}
                            </span>
                            <span className="text-[10px] px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                {lang === 'fa' ? 'دری' : 'EN'}
                            </span>
                        </button>
                    </div>

                    {/* Section 1: Academic & Learning */}
                    <div>
                        <p className="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-2 mb-2">
                            بخش‌های آموزشی
                        </p>
                        <div className="bg-slate-50 dark:bg-slate-900/50 border border-slate-200/70 dark:border-slate-800/80 rounded-2xl overflow-hidden divide-y divide-slate-200/50 dark:divide-slate-800/50">
                            <Link
                                href="/student/dashboard"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-500 flex items-center justify-center">
                                        <LayoutDashboard size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800 dark:text-slate-200">داشبورد اصلی</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </Link>

                            <Link
                                href="/student/courses"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center">
                                        <BookOpen size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800 dark:text-slate-200">کورس‌های من</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </Link>

                            <Link
                                href="/student/quizzes"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                                        <ClipboardCheck size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800 dark:text-slate-200">آزمون‌ها و ارزیابی‌ها</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </Link>

                            <Link
                                href="/student/certificates"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
                                        <Award size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800 dark:text-slate-200">تصدیق‌نامه‌ها و اسناد</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </Link>

                            <a
                                href="/courses"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-500 flex items-center justify-center">
                                        <Compass size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800 dark:text-slate-200">کاوش تمام دوره‌ها</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </a>

                            <a
                                href="/leaderboard"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-yellow-500/10 text-yellow-500 flex items-center justify-center">
                                        <Trophy size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800 dark:text-slate-200">جدول پیشتازان صنف</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </a>
                        </div>
                    </div>

                    {/* Section 2: Account & Settings */}
                    <div>
                        <p className="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-2 mb-2">
                            حساب کاربری و اطلاعات
                        </p>
                        <div className="bg-slate-50 dark:bg-slate-900/50 border border-slate-200/70 dark:border-slate-800/80 rounded-2xl overflow-hidden divide-y divide-slate-200/50 dark:divide-slate-800/50">
                            <a
                                href="/student/notifications"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center">
                                        <Bell size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800 dark:text-slate-200">اعلان‌ها و پیام‌ها</span>
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
                                href="/student/profile"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-500 flex items-center justify-center">
                                        <User size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800 dark:text-slate-200">پروفایل کاربری من</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </Link>

                            <Link
                                href="/student/profile-details"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center">
                                        <FileText size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800 dark:text-slate-200">مشخصات و اسناد فردی</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </Link>

                            <Link
                                href="/scoring-help"
                                onClick={close}
                                className="flex items-center justify-between p-3.5 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
                                        <HelpCircle size={18} />
                                    </div>
                                    <span className="text-xs font-bold text-slate-800 dark:text-slate-200">قوانین و راهنمای امتیازات</span>
                                </div>
                                <ChevronLeft size={16} className="text-slate-400" />
                            </Link>
                        </div>
                    </div>

                    {/* Logout Action Button */}
                    <div className="pt-2 pb-6">
                        <Link
                            method="post"
                            as="button"
                            href="/logout"
                            onClick={close}
                            className="w-full flex items-center justify-center gap-2 p-3.5 rounded-2xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/25 text-rose-600 dark:text-rose-400 text-xs font-black transition-colors"
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
