import React, { useState, useRef, useEffect } from 'react';
import { usePage, Link } from '@inertiajs/react';
import {
    Menu,
    Bell,
    ExternalLink,
    UserCog,
    LogOut,
    ChevronDown,
    Sparkles
} from 'lucide-react';

import PwaInstallButton from '@/Components/PwaInstallButton';

export default function TeacherTopbar({ onToggleSidebar, title = 'پنل اساتید و مدرسین' }) {
    const { auth, unreadNotificationsCount } = usePage().props;
    const [dropdownOpen, setDropdownOpen] = useState(false);
    const dropdownRef = useRef(null);

    const user = auth?.user;
    const unread = unreadNotificationsCount || 0;

    useEffect(() => {
        const handleClickOutside = (e) => {
            if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
                setDropdownOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const userAvatar = user?.avatar_url || (user?.avatar
        ? (user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`)
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'Instructor')}&background=0A58CA&color=fff&size=100`);

    return (
        <header
            className="hidden lg:flex sticky top-0 z-30 h-16 bg-white/85 backdrop-blur-md border-b border-[#E5EAF2] px-4 sm:px-8 items-center justify-between transition-all"
            dir="rtl"
        >
            {/* Right: Mobile Toggle & Page Title */}
            <div className="flex items-center gap-3">
                <button
                    onClick={onToggleSidebar}
                    type="button"
                    className="p-2 rounded-xl text-slate-600 hover:text-[#0A58CA] hover:bg-[#F5F8FC] lg:hidden transition-colors"
                    aria-label="باز کردن منو"
                >
                    <Menu size={22} />
                </button>

                <div className="flex items-center gap-2.5">
                    <span className="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#0A58CA]/10 border border-[#0A58CA]/20 text-[11px] font-bold text-[#0A58CA]">
                        <Sparkles size={12} className="text-[#0A58CA]" />
                        <span>پنل مدرسین</span>
                    </span>
                    <h1 className="text-base sm:text-lg font-bold text-[#111827] tracking-tight">
                        {title}
                    </h1>
                </div>
            </div>

            {/* Left: Actions & User Menu */}
            <div className="flex items-center gap-3">
                {/* PWA Install Button */}
                <PwaInstallButton variant="topbar" />

            {/* View Website Link */}
            <a
                href="/"
                target="_blank"
                rel="noreferrer"
                title="مشاهده وب‌سایت در برگه جدید"
                className="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-[#0A58CA] hover:bg-[#F5F8FC] transition-colors border border-transparent hover:border-[#E5EAF2]"
            >
                <ExternalLink size={14} />
                <span>مشاهده سایت</span>
            </a>

            {/* Notifications Bell */}
            <a
                href="/teacher/notifications"
                title="اعلان‌ها"
                className="relative p-2 rounded-xl text-slate-600 hover:text-[#0A58CA] hover:bg-[#F5F8FC] transition-colors"
            >
                <Bell size={20} />
                {unread > 0 && (
                    <span className="absolute top-1.5 left-1.5 w-4 h-4 rounded-full bg-[#EF4444] text-white text-[10px] font-bold flex items-center justify-center ring-2 ring-white">
                        {unread > 9 ? '+۹' : unread}
                    </span>
                )}
            </a>

            {/* User Dropdown */}
            <div className="relative" ref={dropdownRef}>
                <button
                    onClick={() => setDropdownOpen(!dropdownOpen)}
                    type="button"
                    className="flex items-center gap-2 p-1 pr-2 sm:pr-3 rounded-full hover:bg-slate-100 transition-colors border border-transparent hover:border-[#E5EAF2]"
                >
                    <div className="relative w-8 h-8 rounded-full overflow-hidden ring-2 ring-[#0A58CA]/20">
                        <img
                            src={userAvatar}
                            alt={user?.name || 'مدرس'}
                            className="w-full h-full object-cover"
                        />
                        <span className="absolute bottom-0 left-0 w-2.5 h-2.5 bg-emerald-500 rounded-full ring-1 ring-white" />
                    </div>
                    <span className="hidden sm:block text-xs font-bold text-slate-700 max-w-[120px] truncate">
                        {user?.name || 'مدرس'}
                    </span>
                    <ChevronDown size={14} className="text-slate-400 hidden sm:block" />
                </button>

                {/* Dropdown Menu - Aligns to left */}
                {dropdownOpen && (
                    <div className="absolute left-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-[#E5EAF2] py-2 z-50 animate-in fade-in slide-in-from-top-2 duration-150 text-right">
                        <div className="px-4 py-2.5 border-b border-[#E5EAF2]">
                            <p className="text-xs font-bold text-[#111827] truncate">
                                {user?.name}
                            </p>
                            <p className="text-[11px] text-slate-400 truncate mt-0.5" dir="ltr">
                                {user?.email}
                            </p>
                        </div>

                        <div className="p-1">
                            <a
                                href="/profile"
                                className="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-slate-600 hover:text-[#0A58CA] hover:bg-[#F5F8FC] transition-colors"
                            >
                                <UserCog size={15} />
                                <span>پروفایل و تنظیمات</span>
                            </a>

                            <form action="/logout" method="POST">
                                <input
                                    type="hidden"
                                    name="_token"
                                    value={document.querySelector('meta[name="csrf-token"]')?.content || ''}
                                />
                                <button
                                    type="submit"
                                    className="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-[#EF4444] hover:bg-rose-50 transition-colors text-right"
                                >
                                    <LogOut size={15} />
                                    <span>خروج از حساب</span>
                                </button>
                            </form>
                        </div>
                    </div>
                )}
            </div>
        </div>
        </header>
    );
}
