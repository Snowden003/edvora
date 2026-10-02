import React, { useState, useRef, useEffect } from 'react';
import { usePage, Link } from '@inertiajs/react';
import { useTheme } from '@/Context/ThemeContext';
import {
    Menu,
    Bell,
    ExternalLink,
    UserCog,
    LogOut,
    ChevronDown,
    Sparkles,
    ShieldCheck,
    Sun,
    Moon,
    GraduationCap
} from 'lucide-react';

import PwaInstallButton from '@/Components/PwaInstallButton';

export default function TeacherTopbar({ onToggleSidebar, title = 'داشبورد اساتید' }) {
    const { isDark, toggleTheme } = useTheme();
    const page = usePage();
    const props = page?.props || {};
    const auth = props.auth || {};
    const unreadNotificationsCount = props.unreadNotificationsCount || 0;
    const [dropdownOpen, setDropdownOpen] = useState(false);
    const dropdownRef = useRef(null);

    const user = auth?.user || {};
    const unread = Number(unreadNotificationsCount) || 0;

    useEffect(() => {
        const handleClickOutside = (e) => {
            if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
                setDropdownOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const userAvatar = user?.avatar_url || (typeof user?.avatar === 'string' && user.avatar
        ? (user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`)
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'Instructor')}&background=0A58CA&color=fff&size=100`);

    const displayTitle = typeof title === 'string' ? title.replace(' - ادورا تک', '').trim() : 'داشبورد مدرسین';

    return (
        <>
            {/* =========================================================================
                1. MOBILE TOP NAVIGATION BAR (Ultra-Futuristic Floating Glass Island - Set with Student Dashboard)
                ========================================================================= */}
            <header className="lg:hidden sticky top-2 z-40 px-3 transition-all duration-300" dir="rtl">
                <div className="relative overflow-hidden rounded-2xl bg-white/85 dark:bg-[#071328]/90 backdrop-blur-2xl border border-white/80 dark:border-cyan-500/25 shadow-[0_12px_36px_-6px_rgba(0,0,0,0.12),0_0_20px_rgba(0,240,255,0.1)] px-3 py-2 flex items-center justify-between transition-all">
                    {/* Ambient Glow Gradients in Dark Mode */}
                    <div className="absolute -top-12 -left-12 w-28 h-28 bg-cyan-500/20 rounded-full blur-2xl pointer-events-none" />
                    <div className="absolute -bottom-12 -right-12 w-28 h-28 bg-[#0A58CA]/20 dark:bg-cyan-500/20 rounded-full blur-2xl pointer-events-none" />

                    {/* Brand & 3D Holographic Logo (Just like Student Dashboard) */}
                    <Link
                        href="/teacher/dashboard"
                        className="relative z-10 flex items-center gap-2.5 group"
                    >
                        {/* 3D Holographic Logo Box */}
                        <div className="relative w-10 h-10 rounded-2xl p-[1.5px] bg-gradient-to-tr from-cyan-400 via-blue-600 to-indigo-600 shadow-md shadow-blue-500/25 group-hover:shadow-cyan-400/40 transition-shadow">
                            <div className="w-full h-full rounded-[14px] overflow-hidden bg-slate-950 flex items-center justify-center relative">
                                <img
                                    src="/assets/images/logo1.jpg"
                                    alt="Edvora Tech"
                                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                    onError={(e) => {
                                        e.target.style.display = 'none';
                                    }}
                                />
                                {/* Specular Light Reflection */}
                                <div className="absolute inset-0 bg-gradient-to-tr from-transparent via-white/15 to-transparent pointer-events-none" />
                            </div>
                            {/* Live Online Status Ping */}
                            <span className="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 rounded-full ring-2 ring-white dark:ring-slate-900 shadow-xs" />
                        </div>

                        {/* Title & Teacher Status Badge */}
                        <div className="flex flex-col">
                            <div className="flex items-center gap-1.5">
                                <span className="text-sm font-black text-slate-800 dark:text-white tracking-tight leading-none group-hover:text-[#0A58CA] dark:group-hover:text-cyan-400 transition-colors">
                                    ادورا تک
                                </span>
                                <Sparkles size={11} className="text-cyan-500 animate-pulse" />
                            </div>
                            <span className="text-[10px] text-[#0A58CA] dark:text-cyan-400/90 font-bold mt-1 flex items-center gap-1 leading-none">
                                <span className="inline-block w-1.5 h-1.5 rounded-full bg-cyan-400" />
                                پنل مدرسین · استاد {user?.name?.split(' ')[0] || ''}
                            </span>
                        </div>
                    </Link>

                    {/* Quick 3D Interactive Items */}
                    <div className="relative z-10 flex items-center gap-2">
                        {/* 3D Theme Switcher */}
                        <button
                            onClick={toggleTheme}
                            type="button"
                            className="w-9 h-9 rounded-xl border border-slate-200 dark:border-cyan-500/25 bg-slate-50/90 dark:bg-slate-900/80 text-slate-700 dark:text-amber-400 flex items-center justify-center shadow-xs hover:border-amber-400/50 transition-all active:scale-95"
                            title={isDark ? 'حالت روشن' : 'حالت دارک مود'}
                            aria-label="تغییر تم"
                        >
                            {isDark ? (
                                <Sun size={17} className="text-amber-400 drop-shadow-[0_0_8px_rgba(251,191,36,0.6)]" />
                            ) : (
                                <Moon size={17} className="text-slate-600" />
                            )}
                        </button>

                        {/* 3D Notification Bell */}
                        <Link
                            href="/teacher/notifications"
                            className="relative w-9 h-9 rounded-xl border border-slate-200 dark:border-cyan-500/25 bg-slate-50/90 dark:bg-slate-900/80 text-slate-700 dark:text-slate-200 flex items-center justify-center shadow-xs hover:border-blue-400/50 transition-all"
                            title="اعلان‌ها"
                        >
                            <Bell size={17} className={unread > 0 ? 'text-[#0A58CA] dark:text-cyan-400' : ''} />
                            {unread > 0 && (
                                <span className="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center ring-2 ring-white dark:ring-slate-900 animate-pulse">
                                    {unread > 9 ? '9+' : unread}
                                </span>
                            )}
                        </Link>

                        {/* 3D Sidebar Toggle Button */}
                        <button
                            onClick={onToggleSidebar}
                            type="button"
                            className="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#0A58CA] via-blue-600 to-cyan-500 text-white flex items-center justify-center shadow-md shadow-blue-500/30 active:scale-90 transition-transform"
                            aria-label="منوی ناوبری"
                            title="منوی اصلی"
                        >
                            <Menu size={18} />
                        </button>
                    </div>
                </div>
            </header>

            {/* =========================================================================
                2. DESKTOP NAVIGATION BAR (Next-Gen SaaS Glassmorphism Topbar)
                ========================================================================= */}
            <header className="hidden lg:flex sticky top-0 z-30 h-16 bg-white/85 dark:bg-[#071328]/90 backdrop-blur-2xl border-b border-slate-200/80 dark:border-cyan-500/20 px-8 items-center justify-between shadow-[0_4px_25px_-5px_rgba(0,0,0,0.06),0_0_20px_rgba(0,240,255,0.06)] transition-colors">
                {/* Right: 3D Emblem & Dynamic Page Title */}
                <div className="flex items-center gap-3.5">
                    {/* 3D Holographic Orb */}
                    <div className="relative w-9 h-9 rounded-xl p-[1.5px] bg-gradient-to-tr from-cyan-400 via-[#0A58CA] to-indigo-600 shadow-sm shadow-blue-500/20">
                        <div className="w-full h-full rounded-[10px] overflow-hidden bg-slate-950 flex items-center justify-center relative">
                            <img
                                src="/assets/images/logo1.jpg"
                                alt="Edvora Tech"
                                className="w-full h-full object-cover"
                            />
                        </div>
                    </div>

                    <div className="flex items-center gap-2.5">
                        <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 dark:bg-cyan-950/40 border border-blue-200/80 dark:border-cyan-500/30 text-xs font-bold text-[#0A58CA] dark:text-cyan-300 shadow-xs">
                            <ShieldCheck size={13} className="text-cyan-500" />
                            <span>پنل اساتید و مدرسین</span>
                        </span>
                        <h1 className="text-base sm:text-lg font-black text-slate-800 dark:text-white tracking-tight">
                            {displayTitle}
                        </h1>
                    </div>
                </div>

                {/* Left: Interactive Tools & Instructor Dropdown */}
                <div className="flex items-center gap-3">
                    {/* Active Teacher Badge */}
                    <div
                        className="hidden xl:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#0A58CA]/10 dark:bg-cyan-950/40 border border-[#0A58CA]/20 dark:border-cyan-500/30 text-[#0A58CA] dark:text-cyan-300 font-extrabold text-xs shadow-xs cursor-default"
                        title="سامانه اساتید ادورا تک"
                    >
                        <GraduationCap size={15} className="text-[#0A58CA] dark:text-cyan-400" />
                        <span>مدرس رسمی ادورا</span>
                    </div>
                    {/* 3D Theme Switcher */}
                    <button
                        onClick={toggleTheme}
                        type="button"
                        className="w-9 h-9 rounded-xl border border-slate-200 dark:border-cyan-500/25 bg-slate-50/90 dark:bg-slate-900/80 text-slate-700 dark:text-amber-400 flex items-center justify-center shadow-xs hover:border-amber-400/50 transition-all active:scale-95"
                        title={isDark ? 'حالت روشن' : 'حالت دارک مود'}
                        aria-label="تغییر تم"
                    >
                        {isDark ? (
                            <Sun size={17} className="text-amber-400 drop-shadow-[0_0_8px_rgba(251,191,36,0.6)]" />
                        ) : (
                            <Moon size={17} className="text-slate-600" />
                        )}
                    </button>

                    {/* View Website Link */}
                    <a
                        href="/"
                        target="_blank"
                        rel="noreferrer"
                        title="مشاهده وب‌سایت در برگه جدید"
                        className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-[#0A58CA] dark:hover:text-cyan-400 hover:bg-slate-100 dark:hover:bg-[#0c1a36] transition-colors border border-transparent hover:border-[#E5EAF2] dark:hover:border-white/10"
                    >
                        <ExternalLink size={14} />
                        <span>مشاهده سایت</span>
                    </a>

                    {/* Notifications Bell */}
                    <Link
                        href="/teacher/notifications"
                        title="اعلان‌ها"
                        className="relative p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-[#0A58CA] dark:hover:text-cyan-400 hover:bg-[#F5F8FC] dark:hover:bg-[#0c1a36] transition-colors"
                    >
                        <Bell size={20} />
                        {unread > 0 && (
                            <span className="absolute top-1 left-1 px-1 min-w-[16px] h-4 rounded-full bg-[#EF4444] text-white text-[9px] font-bold flex items-center justify-center ring-2 ring-white dark:ring-slate-900">
                                {unread > 9 ? '9+' : unread}
                            </span>
                        )}
                    </Link>

                    {/* User Profile Dropdown */}
                    <div className="relative" ref={dropdownRef}>
                        <button
                            onClick={() => setDropdownOpen(!dropdownOpen)}
                            type="button"
                            className="flex items-center gap-2 p-1 pr-2 sm:pr-3 rounded-full hover:bg-slate-100 dark:hover:bg-[#0c1a36] transition-colors border border-transparent hover:border-[#E5EAF2] dark:hover:border-white/10"
                            aria-label="منوی کاربر"
                        >
                            <div className="relative w-8 h-8 rounded-full overflow-hidden ring-2 ring-[#0A58CA]/30 dark:ring-cyan-500/40 shrink-0">
                                <img
                                    src={userAvatar}
                                    alt={user?.name || 'مدرس'}
                                    className="w-full h-full object-cover"
                                />
                                <span className="absolute bottom-0 left-0 w-2.5 h-2.5 bg-emerald-500 rounded-full ring-1 ring-white dark:ring-slate-900" />
                            </div>
                            <span className="text-xs font-bold text-slate-700 dark:text-slate-200 max-w-[120px] truncate">
                                {user?.name || 'مدرس'}
                            </span>
                            <ChevronDown size={14} className="text-slate-400" />
                        </button>

                        {/* Dropdown Menu - Aligns to left */}
                        {dropdownOpen && (
                            <div className="absolute left-0 mt-2 w-56 bg-white dark:bg-[#071328] rounded-2xl shadow-xl border border-[#E5EAF2] dark:border-cyan-500/25 dark:shadow-[0_15px_40px_-5px_rgba(0,0,0,0.8),0_0_20px_rgba(0,240,255,0.1)] py-2 z-50 animate-in fade-in slide-in-from-top-2 duration-150 text-right">
                                <div className="px-4 py-2.5 border-b border-[#E5EAF2] dark:border-white/10">
                                    <p className="text-xs font-bold text-[#111827] dark:text-white truncate">
                                        {user?.name || 'مدرس گرامی'}
                                    </p>
                                    <p className="text-[11px] text-slate-400 truncate mt-0.5" dir="ltr">
                                        {user?.email || ''}
                                    </p>
                                </div>

                                <div className="p-1 space-y-0.5">
                                    <Link
                                        href="/profile"
                                        onClick={() => setDropdownOpen(false)}
                                        className="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-[#0A58CA] dark:hover:text-cyan-400 hover:bg-[#F5F8FC] dark:hover:bg-cyan-500/15 transition-colors"
                                    >
                                        <UserCog size={15} />
                                        <span>پروفایل و تنظیمات</span>
                                    </Link>

                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        onClick={() => setDropdownOpen(false)}
                                        className="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-[#EF4444] hover:bg-rose-50 dark:hover:bg-rose-500/15 transition-colors text-right"
                                    >
                                        <LogOut size={15} />
                                        <span>خروج از حساب</span>
                                    </Link>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </header>
        </>
    );
}
