import React, { useState, useRef, useEffect } from 'react';
import { usePage, Link } from '@inertiajs/react';
import { useLanguage } from '@/Context/LanguageContext';
import { useTheme } from '@/Context/ThemeContext';
import {
    Menu,
    Bell,
    Compass,
    UserCircle,
    FileText,
    LogOut,
    ChevronDown,
    GraduationCap,
    Trophy,
    Globe,
    Sun,
    Moon
} from 'lucide-react';

export default function StudentTopbar({ onToggleSidebar, title }) {
    const { auth, unreadNotificationsCount } = usePage().props;
    const { lang, setLang, toggleLang, isRtl, t } = useLanguage();
    const { isDark, toggleTheme } = useTheme();
    const [dropdownOpen, setDropdownOpen] = useState(false);
    const [langDropdownOpen, setLangDropdownOpen] = useState(false);
    const dropdownRef = useRef(null);
    const langRef = useRef(null);

    const user = auth?.user;
    const unread = unreadNotificationsCount || 0;

    useEffect(() => {
        const handleClickOutside = (e) => {
            if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
                setDropdownOpen(false);
            }
            if (langRef.current && !langRef.current.contains(e.target)) {
                setLangDropdownOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const userAvatar = user?.avatar_url || (user?.avatar
        ? (user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`)
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'Student')}&background=1F8FFF&color=fff&size=100`);

    return (
        <header className="sticky top-0 z-30 h-16 bg-white/90 dark:bg-[#071022]/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 px-4 sm:px-8 flex items-center justify-between transition-colors">
            {/* Left / Right (based on dir): Mobile Toggle & Page Title */}
            <div className="flex items-center gap-3">
                <button
                    onClick={onToggleSidebar}
                    type="button"
                    className="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden transition-colors"
                    aria-label="Open Sidebar"
                >
                    <Menu size={22} />
                </button>

                <div className="flex items-center gap-2.5">
                    <span className="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-brand-50 dark:bg-brand-950/60 border border-brand-200/60 dark:border-brand-800/50 text-[11px] font-bold text-brand-700 dark:text-brand-300">
                        <GraduationCap size={13} className="text-brand-500" />
                        <span>{t('portal_name')}</span>
                    </span>
                    <h1 className="text-base sm:text-lg font-bold text-slate-800 dark:text-white tracking-tight">
                        {title || t('dashboard')}
                    </h1>
                </div>
            </div>

            {/* Actions & User Menu */}
            <div className="flex items-center gap-2 sm:gap-3">
                {/* Dark / Light Mode Toggle Button */}
                <button
                    onClick={toggleTheme}
                    type="button"
                    className="relative flex items-center justify-center w-9 h-9 rounded-xl border border-slate-200 dark:border-amber-500/30 bg-slate-50/80 dark:bg-amber-950/25 text-slate-700 dark:text-amber-400 hover:border-brand-300 dark:hover:border-amber-400/60 hover:bg-slate-100 dark:hover:bg-amber-900/30 dark:shadow-[0_0_15px_rgba(245,158,11,0.2)] transition-all shadow-xs group"
                    title={isDark ? t('light_mode') : t('dark_mode')}
                    aria-label={t('toggle_theme')}
                >
                    {isDark ? (
                        <Sun size={17} className="text-amber-400 drop-shadow-[0_0_8px_rgba(251,191,36,0.6)] transition-transform group-hover:rotate-45" />
                    ) : (
                        <Moon size={17} className="text-slate-600 transition-transform group-hover:-rotate-12" />
                    )}
                </button>

                {/* Language Switcher Dropdown */}
                <div className="relative" ref={langRef}>
                    <button
                        onClick={() => setLangDropdownOpen(!langDropdownOpen)}
                        type="button"
                        className="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-brand-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900/80 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs transition-colors"
                        title={t('language')}
                    >
                        <Globe size={15} className="text-brand-600 dark:text-brand-400" />
                        <span>{lang === 'fa' ? 'فارسی دری' : 'English'}</span>
                        <ChevronDown size={13} className={`text-slate-400 transition-transform ${langDropdownOpen ? 'rotate-180' : ''}`} />
                    </button>

                    {langDropdownOpen && (
                        <div
                            className={`absolute mt-2 w-36 bg-white dark:bg-[#0b1730] rounded-2xl shadow-xl dark:shadow-slate-950/70 border border-slate-200/80 dark:border-slate-800 py-1.5 z-50 animate-in fade-in slide-in-from-top-2 duration-150 ${
                                isRtl ? 'left-0' : 'right-0'
                            }`}
                        >
                            <button
                                type="button"
                                onClick={() => {
                                    setLang('fa');
                                    setLangDropdownOpen(false);
                                }}
                                className={`w-full flex items-center justify-between px-3.5 py-2 text-xs font-semibold transition-colors ${
                                    lang === 'fa'
                                        ? 'bg-brand-50 dark:bg-brand-950/50 text-brand-600 dark:text-brand-400 font-bold'
                                        : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/80'
                                }`}
                            >
                                <span>فارسی (دری)</span>
                                {lang === 'fa' && <span className="w-1.5 h-1.5 rounded-full bg-brand-600 dark:bg-brand-400" />}
                            </button>

                            <button
                                type="button"
                                onClick={() => {
                                    setLang('en');
                                    setLangDropdownOpen(false);
                                }}
                                className={`w-full flex items-center justify-between px-3.5 py-2 text-xs font-semibold transition-colors ${
                                    lang === 'en'
                                        ? 'bg-brand-50 dark:bg-brand-950/50 text-brand-600 dark:text-brand-400 font-bold'
                                        : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/80'
                                }`}
                            >
                                <span>English</span>
                                {lang === 'en' && <span className="w-1.5 h-1.5 rounded-full bg-brand-600 dark:bg-brand-400" />}
                            </button>
                        </div>
                    )}
                </div>

                {/* Explore Courses Link */}
                <a
                    href="/courses"
                    className="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                >
                    <Compass size={14} />
                    <span>{t('explore_courses')}</span>
                </a>

                {/* Notifications Bell */}
                <a
                    href="/student/notifications"
                    title={t('notifications')}
                    className="relative p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                >
                    <Bell size={20} />
                    {unread > 0 && (
                        <span className="absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center ring-2 ring-white dark:ring-slate-900">
                            {unread > 9 ? '9+' : unread}
                        </span>
                    )}
                </a>

                {/* User Dropdown */}
                <div className="relative" ref={dropdownRef}>
                    <button
                        onClick={() => setDropdownOpen(!dropdownOpen)}
                        type="button"
                        className="flex items-center gap-2 p-1 px-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-700"
                    >
                        <div className="relative w-8 h-8 rounded-full overflow-hidden ring-2 ring-brand-500/20">
                            <img
                                src={userAvatar}
                                alt={user?.name || 'Student'}
                                className="w-full h-full object-cover"
                            />
                            <span className="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full ring-1 ring-white dark:ring-slate-900" />
                        </div>
                        <span className="hidden sm:block text-xs font-bold text-slate-700 dark:text-slate-200 max-w-[120px] truncate">
                            {user?.name || 'Student'}
                        </span>
                        <ChevronDown size={14} className="text-slate-400 hidden sm:block" />
                    </button>

                    {/* Dropdown Menu */}
                    {dropdownOpen && (
                        <div
                            className={`absolute mt-2 w-56 bg-white dark:bg-[#0b1730] rounded-2xl shadow-xl dark:shadow-slate-950/70 border border-slate-200/80 dark:border-slate-800 py-2 z-50 animate-in fade-in slide-in-from-top-2 duration-150 ${
                                isRtl ? 'left-0' : 'right-0'
                            }`}
                        >
                            <div className="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800">
                                <p className="text-xs font-bold text-slate-800 dark:text-white truncate">
                                    {user?.name}
                                </p>
                                <p className="text-[11px] text-slate-400 truncate mt-0.5">
                                    {user?.email}
                                </p>
                            </div>

                            <div className="p-1">
                                <a
                                    href="/student/profile"
                                    className="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors"
                                >
                                    <UserCircle size={15} />
                                    <span>{t('profile')}</span>
                                </a>

                                <Link
                                    href="/student/profile-details"
                                    className="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors"
                                >
                                    <FileText size={15} />
                                    <span>{t('my_details')}</span>
                                </Link>

                                <a
                                    href="/leaderboard"
                                    className="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50/50 dark:hover:bg-amber-950/30 transition-colors"
                                >
                                    <Trophy size={15} className="text-amber-500" />
                                    <span>{t('leaderboard')}</span>
                                </a>

                                <div className="border-t border-slate-100 dark:border-slate-800 my-1" />

                                <Link
                                    method="post"
                                    as="button"
                                    href="/logout"
                                    className="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors text-start"
                                >
                                    <LogOut size={15} />
                                    <span>{t('logout')}</span>
                                </Link>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </header>
    );
}
