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
    LogOut,
    Mail,
    Info,
    Trophy,
    Building
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
            title: 'آموزش و یادگیری',
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
            ],
        },
        {
            title: 'جامعه و رویدادها (Community)',
            items: [
                {
                    name: 'مدیریت رویدادها',
                    icon: CalendarDays,
                    href: '/admin/events',
                    active: url.includes('/admin/events'),
                },
                {
                    name: 'جدول برترین‌ها (Leaderboard)',
                    icon: Trophy,
                    href: '#',
                    active: false,
                    disabled: true,
                },
            ],
        },
        {
            title: 'منابع و مسیر یادگیری (Resources & Roadmap)',
            items: [
                {
                    name: 'مراحل نقشه راه (Roadmap)',
                    icon: Map,
                    href: '#',
                    active: false,
                    disabled: true,
                },
                {
                    name: 'محتوای فونداسیون (Foundation)',
                    icon: Building,
                    href: '#',
                    active: false,
                    disabled: true,
                },
                {
                    name: 'قوانین و راهنمای امتیازات',
                    icon: Sparkles,
                    href: '#',
                    active: false,
                    disabled: true,
                },
                {
                    name: 'گواهینامه‌ها (Certificates)',
                    icon: Award,
                    href: '#',
                    active: false,
                    disabled: true,
                },
                {
                    name: 'امتیازات کاربران (Points)',
                    icon: Coins,
                    href: '#',
                    active: false,
                    disabled: true,
                },
            ],
        },
        {
            title: 'صفحات عمومی (About & Legal)',
            items: [
                {
                    name: 'درباره ما (About Us)',
                    icon: Info,
                    href: '/admin/company-pages/about',
                    active: url === '/admin/company-pages/about' || url.startsWith('/admin/company-pages/about'),
                    isSpa: true,
                },
                {
                    name: 'داستان ما (Our Story)',
                    icon: BookOpen,
                    href: '/admin/company-pages/story',
                    active: url === '/admin/company-pages/story' || url.startsWith('/admin/company-pages/story'),
                    isSpa: true,
                },
                {
                    name: 'نحوه کار ما (How We Work)',
                    icon: Sparkles,
                    href: '/admin/company-pages/how-we-work',
                    active: url === '/admin/company-pages/how-we-work' || url.startsWith('/admin/company-pages/how-we-work'),
                    isSpa: true,
                },
                {
                    name: 'قوانین و شرایط (Terms)',
                    icon: FileText,
                    href: '/admin/company-pages/terms',
                    active: url === '/admin/company-pages/terms' || url.startsWith('/admin/company-pages/terms'),
                    isSpa: true,
                },
                {
                    name: 'حریم خصوصی (Privacy)',
                    icon: Shield,
                    href: '/admin/company-pages/privacy',
                    active: url === '/admin/company-pages/privacy' || url.startsWith('/admin/company-pages/privacy'),
                    isSpa: true,
                },
            ],
        },
        {
            title: 'ارتباطات و پیام‌ها (Communications)',
            items: [
                {
                    name: 'پیام‌های تماس (Contact)',
                    icon: MessageSquare,
                    href: '#',
                    active: false,
                    disabled: true,
                },
                {
                    name: 'نظرات و بازخوردها (Reviews)',
                    icon: Star,
                    href: '#',
                    active: false,
                    disabled: true,
                },
                {
                    name: 'ارسال ایمیل همگانی (Broadcast)',
                    icon: Mail,
                    href: '/admin/broadcast-emails',
                    active: url.startsWith('/admin/broadcast-emails'),
                    isSpa: true,
                },
            ],
        },
        {
            title: 'کاربران و اعضا (Users)',
            items: [
                {
                    name: 'دانشجویان',
                    icon: Users,
                    href: '/admin/students',
                    active: url.startsWith('/admin/students'),
                    isSpa: true,
                },
                {
                    name: 'اساتید و مدرسان',
                    icon: UserCheck,
                    href: '/admin/teachers',
                    active: url.startsWith('/admin/teachers'),
                    isSpa: true,
                    badge: pendingTeachersCount > 0 ? `${pendingTeachersCount} جدید` : null,
                    badgeColor: 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                },
            ],
        },
        {
            title: 'تنظیمات و ابزارها (System)',
            items: [
                {
                    name: 'مدیریت فایل‌ها',
                    icon: FolderKanban,
                    href: '/admin/file-manager',
                    active: url.startsWith('/admin/file-manager'),
                    isSpa: true,
                },
                {
                    name: 'تنظیمات وب‌سایت',
                    icon: Settings,
                    href: '#',
                    active: false,
                    disabled: true,
                },
            ],
        },
    ];

    return (
        <>
            {/* Mobile Backdrop */}
            {/* Sidebar Container - Fixed Desktop Sidebar */}
            <aside
                className={`hidden lg:flex fixed top-0 bottom-0 right-0 z-40 flex-col bg-slate-900/95 backdrop-blur-2xl border-l border-slate-800/80 text-slate-200 transition-all duration-300 ease-in-out select-none
                ${isCollapsed ? 'w-20' : 'w-72'}
                `}
            >
                {/* Brand Header with Integrated Collapse Toggle Button */}
                <div className={`p-4 border-b border-slate-800/80 flex items-center justify-between gap-2 bg-slate-950/40 ${isCollapsed ? 'flex-col justify-center' : ''}`}>
                    {!isCollapsed ? (
                        <>
                            <Link href="/admin/dashboard" className="flex items-center gap-3 group overflow-hidden flex-1 min-w-0">
                                <div className="w-10 h-10 rounded-xl overflow-hidden bg-slate-950 border border-slate-700/60 flex items-center justify-center shadow-md shrink-0 group-hover:scale-105 transition-transform">
                                    <img src="/logo.png" alt="Edvora Tech" className="w-full h-full object-cover" />
                                </div>
                                <div className="flex flex-col min-w-0">
                                    <span className="font-extrabold text-sm tracking-tight text-white flex items-center gap-1.5 truncate">
                                        ادوُرا تِک
                                        <span className="text-[10px] font-bold px-2 py-0.2 rounded-full bg-brand-500/20 text-brand-300 border border-brand-500/30 shrink-0">
                                            ADMIN
                                        </span>
                                    </span>
                                    <span className="text-[11px] text-slate-400 font-medium truncate">سامانه مدیریت آموزشگاه</span>
                                </div>
                            </Link>

                            {/* Collapse Button - Visible & Sleek inside header */}
                            <button
                                type="button"
                                onClick={() => setIsCollapsed(true)}
                                title="جمع کردن منو"
                                className="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all duration-200 shadow-xs active:scale-95 border border-slate-700 shrink-0"
                            >
                                <ChevronRight size={17} />
                            </button>
                        </>
                    ) : (
                        <>
                            <Link href="/admin/dashboard" className="w-10 h-10 rounded-xl overflow-hidden bg-slate-950 border border-slate-700/60 flex items-center justify-center shadow-md hover:scale-105 transition-transform" title="ادوُرا تِک">
                                <img src="/extension_icon.png" alt="Edvora" className="w-8 h-8 object-contain" />
                            </Link>

                            {/* Expand Button */}
                            <button
                                type="button"
                                onClick={() => setIsCollapsed(false)}
                                title="گسترش منو"
                                className="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all duration-200 shadow-xs active:scale-95 border border-slate-700"
                            >
                                <ChevronLeft size={17} />
                            </button>
                        </>
                    )}
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
                                    const isDisabled = item.disabled;
                                    const activeClass = item.active
                                        ? 'bg-gradient-to-r from-brand-500/20 to-brand-600/10 text-brand-400 font-bold border-r-4 border-brand-500 shadow-sm'
                                        : isDisabled
                                        ? 'text-slate-500 opacity-50 cursor-not-allowed select-none'
                                        : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60 font-medium';

                                    const linkContent = (
                                        <div
                                            className={`flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group relative ${activeClass}`}
                                            title={isCollapsed ? (isDisabled ? `${item.name} (به زودی ⭐)` : item.name) : undefined}
                                        >
                                            <div
                                                className={`p-1.5 rounded-lg transition-colors ${
                                                    item.active
                                                        ? 'bg-brand-500/20 text-brand-400'
                                                        : isDisabled
                                                        ? 'text-slate-600'
                                                        : 'text-slate-400 group-hover:text-brand-300 group-hover:bg-slate-800'
                                                }`}
                                            >
                                                <Icon size={19} />
                                            </div>

                                            {!isCollapsed && (
                                                <div className="flex-1 flex items-center justify-between min-w-0">
                                                    <span className="truncate text-sm flex items-center gap-1.5">
                                                        {item.name}
                                                    </span>
                                                    {isDisabled ? (
                                                        <span className="text-[10px] font-medium px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center gap-0.5 shrink-0" title="این بخش به زودی فعال می‌شود">
                                                            <span>⭐</span>
                                                            <span>به زودی</span>
                                                        </span>
                                                    ) : (
                                                        item.badge && (
                                                            <span
                                                                className={`text-[11px] font-bold px-2 py-0.5 rounded-full border ${item.badgeColor}`}
                                                            >
                                                                {item.badge}
                                                            </span>
                                                        )
                                                    )}
                                                </div>
                                            )}

                                            {/* Tooltip for collapsed state */}
                                            {isCollapsed && (
                                                <div className="hidden lg:block absolute left-full ml-3 px-2.5 py-1.5 bg-slate-900 text-white text-xs rounded-lg shadow-xl border border-slate-700 whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                                                    {item.name} {isDisabled && '⭐ (به زودی)'}
                                                </div>
                                            )}
                                        </div>
                                    );

                                    if (isDisabled) {
                                        return (
                                            <div key={itemIdx} className="cursor-not-allowed" onClick={(e) => e.preventDefault()}>
                                                {linkContent}
                                            </div>
                                        );
                                    }

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
                </div>
            </aside>
        </>
    );
}
