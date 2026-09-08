import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    LayoutDashboard,
    GraduationCap,
    BookOpen,
    Users,
    UserCheck,
    Award,
    Sparkles,
    Map,
    Coins,
    MessageSquare,
    Star,
    CalendarDays,
    Settings,
    FolderKanban,
    FileText,
    ExternalLink,
    ChevronLeft,
    ChevronRight,
    Menu,
    X,
    Shield,
    LogOut
} from 'lucide-react';

export default function Sidebar({ isOpen, setIsOpen, isCollapsed, setIsCollapsed }) {
    const { url, props } = usePage();
    const user = props.auth?.user;
    const pendingTeachersCount = props.stats?.pending_teachers_count || 0;
    const unreadMessagesCount = props.stats?.unread_messages || 0;

    const navGroups = [
        {
            title: 'داشبورد اصلی',
            items: [
                {
                    name: 'پیشخوان و آمار',
                    icon: LayoutDashboard,
                    href: '/admin/dashboard',
                    active: url === '/admin' || url === '/admin/dashboard',
                    isSpa: true,
                },
            ],
        },
        {
            title: 'مدیریت محتوا',
            items: [
                {
                    name: 'دوره‌های آموزشی',
                    icon: GraduationCap,
                    href: '/admin/courses',
                    active: url.startsWith('/admin/courses'),
                    isSpa: true,
                },
                {
                    name: 'کتابخانه و مقالات',
                    icon: BookOpen,
                    href: '/admin/books',
                    active: url.startsWith('/admin/books'),
                    isSpa: true,
                },
                {
                    name: 'صفحات سایت',
                    icon: FileText,
                    href: '/admin-panel/company-pages-management',
                    active: url.includes('company-pages'),
                },
                {
                    name: 'مدیریت فایل‌ها',
                    icon: FolderKanban,
                    href: '/admin-panel/file-manager',
                    active: url.includes('file-manager'),
                },
            ],
        },
        {
            title: 'کاربران و اعضا',
            items: [
                {
                    name: 'دانشجویان',
                    icon: Users,
                    href: '/admin-panel/students',
                    active: url.includes('/students'),
                },
                {
                    name: 'اساتید و مدرسان',
                    icon: UserCheck,
                    href: '/admin-panel/teachers',
                    active: url.includes('/teachers'),
                    badge: pendingTeachersCount > 0 ? `${pendingTeachersCount} جدید` : null,
                    badgeColor: 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                },
            ],
        },
        {
            title: 'آموزش و امتیازدهی',
            items: [
                {
                    name: 'گواهینامه‌ها',
                    icon: Award,
                    href: '/admin-panel/certificates',
                    active: url.includes('certificates'),
                },
                {
                    name: 'قوانین امتیازات',
                    icon: Sparkles,
                    href: '/admin-panel/scoring-rules',
                    active: url.includes('scoring-rules'),
                },
                {
                    name: 'مراحل نقشه راه',
                    icon: Map,
                    href: '/admin-panel/roadmap-stages',
                    active: url.includes('roadmap-stages'),
                },
                {
                    name: 'امتیازات کاربران',
                    icon: Coins,
                    href: '/admin-panel/points',
                    active: url.includes('points'),
                },
            ],
        },
        {
            title: 'ارتباطات و رویدادها',
            items: [
                {
                    name: 'پیام‌های تماس',
                    icon: MessageSquare,
                    href: '/admin-panel/contact-messages',
                    active: url.includes('contact-messages'),
                    badge: unreadMessagesCount > 0 ? unreadMessagesCount : null,
                    badgeColor: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                },
                {
                    name: 'نظرات و بازخوردها',
                    icon: Star,
                    href: '/admin-panel/reviews',
                    active: url.includes('reviews'),
                },
                {
                    name: 'مدیریت رویدادها',
                    icon: CalendarDays,
                    href: '/admin/events',
                    active: url.includes('/admin/events'),
                },
            ],
        },
        {
            title: 'تنظیمات و سیستم',
            items: [
                {
                    name: 'تنظیمات وب‌سایت',
                    icon: Settings,
                    href: '/admin-panel/site-settings',
                    active: url.includes('site-settings'),
                },
                {
                    name: 'پنل کلاسیک فیلامنت',
                    icon: ExternalLink,
                    href: '/admin-panel',
                    active: false,
                    isExternal: true,
                },
            ],
        },
    ];

    return (
        <>
            {/* Mobile Backdrop */}
            {isOpen && (
                <div
                    className="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden transition-opacity"
                    onClick={() => setIsOpen(false)}
                />
            )}

            {/* Sidebar Container */}
            <aside
                className={`fixed top-0 bottom-0 right-0 z-50 flex flex-col bg-slate-900/95 lg:bg-slate-900/80 backdrop-blur-2xl border-l border-slate-800/80 text-slate-200 transition-all duration-300 ease-in-out shadow-2xl lg:shadow-none
                ${isOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'}
                ${isCollapsed ? 'lg:w-20' : 'lg:w-72'}
                w-72
                `}
            >
                {/* Brand Header */}
                <div className="h-20 flex items-center justify-between px-5 border-b border-slate-800/80 bg-slate-950/40">
                    <Link href="/admin/dashboard" className="flex items-center gap-3 group">
                        <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 via-brand-500 to-accent-500 flex items-center justify-center shadow-glow text-white font-black text-xl tracking-wider transition-transform group-hover:scale-105">
                            E
                        </div>
                        {!isCollapsed && (
                            <div className="flex flex-col">
                                <span className="font-extrabold text-base tracking-tight text-white flex items-center gap-1.5 font-display">
                                    Edvora Tech
                                    <span className="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-brand-500/20 text-brand-300 border border-brand-500/30">
                                        ADMIN
                                    </span>
                                </span>
                                <span className="text-xs text-slate-400 font-medium">سامانه مدیریت آموزشگاه</span>
                            </div>
                        )}
                    </Link>

                    {/* Mobile Close Button */}
                    <button
                        onClick={() => setIsOpen(false)}
                        className="lg:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800"
                    >
                        <X size={20} />
                    </button>
                </div>

                {/* Navigation Links (Scrollable) */}
                <div className="flex-1 overflow-y-auto px-3 py-4 space-y-6">
                    {navGroups.map((group, gIdx) => (
                        <div key={gIdx} className="space-y-1">
                            {!isCollapsed && (
                                <h3 className="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    {group.title}
                                </h3>
                            )}
                            <div className="space-y-1 mt-1">
                                {group.items.map((item, itemIdx) => {
                                    const Icon = item.icon;
                                    const activeClass = item.active
                                        ? 'bg-gradient-to-r from-brand-500/20 to-brand-600/10 text-brand-400 font-bold border-r-4 border-brand-500 shadow-sm'
                                        : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60 font-medium';

                                    const linkContent = (
                                        <div
                                            className={`flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group relative ${activeClass}`}
                                            title={isCollapsed ? item.name : undefined}
                                        >
                                            <div
                                                className={`p-1.5 rounded-lg transition-colors ${
                                                    item.active
                                                        ? 'bg-brand-500/20 text-brand-400'
                                                        : 'text-slate-400 group-hover:text-brand-300 group-hover:bg-slate-800'
                                                }`}
                                            >
                                                <Icon size={19} />
                                            </div>

                                            {!isCollapsed && (
                                                <div className="flex-1 flex items-center justify-between min-w-0">
                                                    <span className="truncate text-sm">{item.name}</span>
                                                    {item.badge && (
                                                        <span
                                                            className={`text-[11px] font-bold px-2 py-0.5 rounded-full border ${item.badgeColor}`}
                                                        >
                                                            {item.badge}
                                                        </span>
                                                    )}
                                                </div>
                                            )}

                                            {/* Tooltip for collapsed state */}
                                            {isCollapsed && (
                                                <div className="hidden lg:block absolute left-full ml-3 px-2.5 py-1.5 bg-slate-900 text-white text-xs rounded-lg shadow-xl border border-slate-700 whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                                                    {item.name}
                                                </div>
                                            )}
                                        </div>
                                    );

                                    return item.isSpa ? (
                                        <Link key={itemIdx} href={item.href}>
                                            {linkContent}
                                        </Link>
                                    ) : (
                                        <a
                                            key={itemIdx}
                                            href={item.href}
                                            target={item.isExternal ? '_blank' : '_self'}
                                            rel={item.isExternal ? 'noreferrer' : undefined}
                                        >
                                            {linkContent}
                                        </a>
                                    );
                                })}
                            </div>
                        </div>
                    ))}
                </div>

                {/* Bottom Profile & Toggle section */}
                <div className="p-3 border-t border-slate-800/80 bg-slate-950/40 space-y-2">
                    {/* User Profile Summary */}
                    <div className="flex items-center gap-3 p-2 rounded-xl bg-slate-800/40 border border-slate-800">
                        <div className="relative">
                            <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-white text-sm shadow-md">
                                {user?.name ? user.name.charAt(0).toUpperCase() : 'A'}
                            </div>
                            <span className="absolute -bottom-0.5 -left-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-slate-900" />
                        </div>
                        {!isCollapsed && (
                            <div className="flex-1 min-w-0">
                                <h4 className="text-sm font-bold text-slate-200 truncate">{user?.name || 'مدیر ارشد'}</h4>
                                <p className="text-[11px] text-slate-400 truncate">{user?.email || 'admin@edvora.org'}</p>
                            </div>
                        )}
                        {!isCollapsed && (
                            <a
                                href="/logout"
                                className="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-colors"
                                title="خروج از حساب"
                            >
                                <LogOut size={17} />
                            </a>
                        )}
                    </div>

                    {/* Desktop Collapse Toggle */}
                    <button
                        onClick={() => setIsCollapsed(!isCollapsed)}
                        className="hidden lg:flex w-full items-center justify-center gap-2 py-2 px-3 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-colors text-xs font-medium"
                    >
                        {isCollapsed ? (
                            <>
                                <ChevronLeft size={16} />
                            </>
                        ) : (
                            <>
                                <ChevronRight size={16} />
                                <span>جمع کردن منو</span>
                            </>
                        )}
                    </button>
                </div>
            </aside>
        </>
    );
}
