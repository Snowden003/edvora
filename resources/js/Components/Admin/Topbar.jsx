import React, { useState, useRef, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    Menu,
    Search,
    Bell,
    Globe,
    Moon,
    Sun,
    ChevronDown,
    User,
    Settings,
    LogOut,
    ExternalLink,
    Shield,
    Sparkles,
    CheckCircle2,
    Calendar,
    ArrowUpRight
} from 'lucide-react';

export default function Topbar({ onToggleSidebar }) {
    const { props } = usePage();
    const user = props.auth?.user;
    const unreadCount = props.unreadNotificationsCount || 0;

    const [isProfileMenuOpen, setIsProfileMenuOpen] = useState(false);
    const [isNotificationsOpen, setIsNotificationsOpen] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');

    const profileMenuRef = useRef(null);
    const notificationsRef = useRef(null);

    // Close dropdowns on outside click
    useEffect(() => {
        function handleClickOutside(e) {
            if (profileMenuRef.current && !profileMenuRef.current.contains(e.target)) {
                setIsProfileMenuOpen(false);
            }
            if (notificationsRef.current && !notificationsRef.current.contains(e.target)) {
                setIsNotificationsOpen(false);
            }
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    // Current Persian date greeting
    const today = new Intl.DateTimeFormat('fa-IR', {
        dateStyle: 'full',
    }).format(new Date());

    return (
        <header className="sticky top-0 z-30 h-20 bg-slate-900/80 backdrop-blur-xl border-b border-slate-800/80 px-4 sm:px-8 flex items-center justify-between transition-all">
            {/* Left section: Hamburger & Search */}
            <div className="flex items-center gap-4 flex-1 max-w-xl">
                <button
                    onClick={onToggleSidebar}
                    className="p-2.5 rounded-xl bg-slate-800/60 hover:bg-slate-800 text-slate-300 hover:text-white lg:hidden transition-colors border border-slate-700/50"
                    aria-label="Toggle menu"
                >
                    <Menu size={20} />
                </button>

                {/* Global Search Input */}
                <div className="relative w-full hidden sm:block">
                    <Search className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                    <input
                        type="text"
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        placeholder="جستجو در دوره‌ها، دانشجویان، اساتید، پیام‌ها..."
                        className="w-full pl-4 pr-10 py-2.5 rounded-xl bg-slate-800/50 border border-slate-700/60 text-sm text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all"
                    />
                    <div className="absolute left-3 top-1/2 -translate-y-1/2 hidden md:flex items-center gap-1 text-[10px] bg-slate-700/60 text-slate-300 px-2 py-0.5 rounded border border-slate-600">
                        <span>⌘</span>
                        <span>K</span>
                    </div>
                </div>
            </div>

            {/* Right section: Actions & Profile */}
            <div className="flex items-center gap-2 sm:gap-3">
                {/* View Live Website */}
                <a
                    href="/"
                    target="_blank"
                    rel="noreferrer"
                    className="hidden md:flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white bg-slate-800/60 hover:bg-slate-800 border border-slate-700/50 transition-all hover:border-slate-600 group"
                >
                    <Globe size={15} className="text-brand-400 group-hover:rotate-12 transition-transform" />
                    <span>مشاهده سایت</span>
                    <ArrowUpRight size={13} className="text-slate-400" />
                </a>

                {/* Notifications Dropdown */}
                <div className="relative" ref={notificationsRef}>
                    <button
                        onClick={() => setIsNotificationsOpen(!isNotificationsOpen)}
                        className="relative p-2.5 rounded-xl bg-slate-800/60 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/50 transition-colors"
                        aria-label="Notifications"
                    >
                        <Bell size={19} />
                        {unreadCount > 0 && (
                            <span className="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-rose-500 text-white text-[10px] font-extrabold flex items-center justify-center animate-pulse shadow-md">
                                {unreadCount > 9 ? '9+' : unreadCount}
                            </span>
                        )}
                    </button>

                    {isNotificationsOpen && (
                        <div className="absolute left-0 sm:right-auto sm:left-0 mt-3 w-80 sm:w-96 rounded-2xl bg-slate-900/95 backdrop-blur-2xl border border-slate-800 shadow-2xl overflow-hidden z-50 animate-in fade-in slide-in-from-top-2 duration-200">
                            <div className="p-4 border-b border-slate-800 flex items-center justify-between bg-slate-950/50">
                                <div className="flex items-center gap-2">
                                    <Bell size={16} className="text-brand-400" />
                                    <h4 className="text-sm font-bold text-white">اعلان‌ها و رویدادها</h4>
                                </div>
                                <span className="text-xs text-brand-400 bg-brand-500/10 px-2 py-0.5 rounded-full font-medium">
                                    {unreadCount} خوانده نشده
                                </span>
                            </div>

                            <div className="p-3 divide-y divide-slate-800/60 max-h-72 overflow-y-auto">
                                <div className="p-3 rounded-xl hover:bg-slate-800/50 transition-colors cursor-pointer">
                                    <div className="flex items-start gap-3">
                                        <div className="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0">
                                            <CheckCircle2 size={16} />
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <p className="text-xs text-slate-200 font-semibold">ثبت‌نام جدید استاد</p>
                                            <p className="text-[11px] text-slate-400 mt-0.5">درخواست تایید مدرک توسط مدرس جدید ثبت شد.</p>
                                            <span className="text-[10px] text-slate-400 mt-1 block">۱۰ دقیقه پیش</span>
                                        </div>
                                    </div>
                                </div>

                                <div className="p-3 rounded-xl hover:bg-slate-800/50 transition-colors cursor-pointer">
                                    <div className="flex items-start gap-3">
                                        <div className="w-8 h-8 rounded-lg bg-brand-500/10 text-brand-400 flex items-center justify-center shrink-0">
                                            <Calendar size={16} />
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <p className="text-xs text-slate-200 font-semibold">کلاس زنده جدید شروع شد</p>
                                            <p className="text-[11px] text-slate-400 mt-0.5">جلسه آنلاین طراحی وب آغاز شده است.</p>
                                            <span className="text-[10px] text-slate-400 mt-1 block">۱ ساعت پیش</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="p-3 border-t border-slate-800 bg-slate-950/40 text-center">
                                <a href="/admin-panel/contact-messages" className="text-xs text-brand-400 hover:text-brand-300 font-bold">
                                    مشاهده تمام پیام‌ها و اعلان‌ها
                                </a>
                            </div>
                        </div>
                    )}
                </div>

                {/* Profile Dropdown */}
                <div className="relative" ref={profileMenuRef}>
                    <button
                        onClick={() => setIsProfileMenuOpen(!isProfileMenuOpen)}
                        className="flex items-center gap-2 p-1.5 sm:px-3 sm:py-2 rounded-xl bg-slate-800/60 hover:bg-slate-800 border border-slate-700/50 text-slate-200 transition-all"
                    >
                        <div className="w-8 h-8 rounded-lg bg-gradient-to-tr from-brand-600 to-accent-500 flex items-center justify-center text-white font-bold text-xs shadow-md">
                            {user?.name ? user.name.charAt(0).toUpperCase() : 'A'}
                        </div>
                        <div className="hidden sm:flex flex-col text-right">
                            <span className="text-xs font-bold text-white truncate max-w-[100px]">{user?.name || 'مدیر سیستم'}</span>
                            <span className="text-[10px] text-brand-400 font-medium">Administrator</span>
                        </div>
                        <ChevronDown size={15} className={`text-slate-400 transition-transform ${isProfileMenuOpen ? 'rotate-180' : ''}`} />
                    </button>

                    {isProfileMenuOpen && (
                        <div className="absolute left-0 mt-3 w-56 rounded-2xl bg-slate-900/95 backdrop-blur-2xl border border-slate-800 shadow-2xl p-2 z-50 animate-in fade-in slide-in-from-top-2 duration-200">
                            <div className="px-3 py-2.5 border-b border-slate-800 mb-1">
                                <p className="text-xs font-bold text-white truncate">{user?.name || 'مدیر ارشد'}</p>
                                <p className="text-[11px] text-slate-400 truncate">{user?.email || 'admin@edvora.org'}</p>
                                <div className="mt-1.5 inline-flex items-center gap-1 text-[10px] bg-brand-500/20 text-brand-300 px-2 py-0.5 rounded-full border border-brand-500/30 font-semibold">
                                    <Shield size={10} />
                                    <span>دسترسی کامل مدیریت</span>
                                </div>
                            </div>

                            <a
                                href="/profile"
                                className="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/80 transition-colors"
                            >
                                <User size={15} className="text-slate-400" />
                                <span>پروفایل کاربری</span>
                            </a>

                            <a
                                href="/admin-panel/site-settings"
                                className="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/80 transition-colors"
                            >
                                <Settings size={15} className="text-slate-400" />
                                <span>تنظیمات سیستم</span>
                            </a>

                            <a
                                href="/admin-panel"
                                target="_blank"
                                rel="noreferrer"
                                className="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/80 transition-colors"
                            >
                                <div className="flex items-center gap-2.5">
                                    <Sparkles size={15} className="text-amber-400" />
                                    <span>پنل فیلامنت</span>
                                </div>
                                <ExternalLink size={12} className="text-slate-400" />
                            </a>

                            <div className="my-1 border-t border-slate-800" />

                            <a
                                href="/logout"
                                className="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-colors"
                            >
                                <LogOut size={15} />
                                <span>خروج از پنل</span>
                            </a>
                        </div>
                    )}
                </div>
            </div>
        </header>
    );
}
