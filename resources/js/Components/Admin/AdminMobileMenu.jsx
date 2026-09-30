import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    LayoutDashboard,
    GraduationCap,
    BookOpen,
    Users,
    UserCheck,
    CalendarDays,
    FolderKanban,
    Mail,
    FileText,
    ExternalLink,
    LogOut,
    X,
    ShieldCheck,
    Sparkles,
    Settings,
    ChevronLeft
} from 'lucide-react';
import PwaInstallButton from '@/Components/PwaInstallButton';

export default function AdminMobileMenu({ isOpen, onClose }) {
    const { url, props } = usePage();
    const user = props.auth?.user;
    const pendingTeachersCount = props.stats?.pending_teachers_count || 0;
    const unreadMessagesCount = props.stats?.unread_messages || 0;

    if (!isOpen) return null;

    const navSections = [
        {
            title: 'بخش اصلی و مدیریت',
            items: [
                {
                    name: 'پیشخوان و آمار',
                    icon: LayoutDashboard,
                    href: '/admin/dashboard',
                    color: 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20',
                    active: url === '/admin' || url === '/admin/dashboard',
                },
                {
                    name: 'دوره‌های آموزشی',
                    icon: GraduationCap,
                    href: '/admin/courses',
                    color: 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20',
                    active: url.startsWith('/admin/courses'),
                },
                {
                    name: 'کتابخانه و مقالات',
                    icon: BookOpen,
                    href: '/admin/books',
                    color: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                    active: url.startsWith('/admin/books'),
                },
                {
                    name: 'مدیریت دانشجویان',
                    icon: Users,
                    href: '/admin/students',
                    color: 'text-sky-400 bg-sky-500/10 border-sky-500/20',
                    active: url.startsWith('/admin/students'),
                },
                {
                    name: 'اساتید و مدرسان',
                    icon: UserCheck,
                    href: '/admin/teachers',
                    color: 'text-purple-400 bg-purple-500/10 border-purple-500/20',
                    active: url.startsWith('/admin/teachers'),
                    badge: pendingTeachersCount > 0 ? `${pendingTeachersCount} جدید` : null,
                },
                {
                    name: 'مدیریت فایل‌ها',
                    icon: FolderKanban,
                    href: '/admin/file-manager',
                    color: 'text-amber-400 bg-amber-500/10 border-amber-500/20',
                    active: url.startsWith('/admin/file-manager'),
                },
                {
                    name: 'رویدادها و همایش‌ها',
                    icon: CalendarDays,
                    href: '/admin/events',
                    color: 'text-rose-400 bg-rose-500/10 border-rose-500/20',
                    active: url.startsWith('/admin/events'),
                },
                {
                    name: 'ارسال ایمیل همگانی',
                    icon: Mail,
                    href: '/admin/broadcast-email',
                    color: 'text-pink-400 bg-pink-500/10 border-pink-500/20',
                    active: url.startsWith('/admin/broadcast-email'),
                },
                {
                    name: 'صفحات عمومی سایت',
                    icon: FileText,
                    href: '/admin/company-pages',
                    color: 'text-teal-400 bg-teal-500/10 border-teal-500/20',
                    active: url.startsWith('/admin/company-pages'),
                },
            ],
        },
    ];

    return (
        <div className="fixed inset-0 z-50 lg:hidden flex flex-col justify-end" dir="rtl">
            {/* Backdrop Overlay */}
            <div
                className="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity duration-300 animate-in fade-in"
                onClick={onClose}
            />

            {/* Bottom Sheet Modal Menu Container */}
            <div className="relative z-10 w-full max-h-[85vh] overflow-y-auto bg-slate-900 border-t border-slate-700/80 rounded-t-3xl shadow-2xl p-5 space-y-5 animate-in slide-in-from-bottom duration-300 select-none">
                {/* Drag Handle & Header */}
                <div className="flex flex-col items-center gap-3">
                    <div className="w-12 h-1.5 rounded-full bg-slate-700" />
                    <div className="w-full flex items-center justify-between">
                        <div className="flex items-center gap-2.5">
                            <div className="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-600 via-brand-500 to-indigo-600 flex items-center justify-center shadow-sm">
                                <img src="/extension_icon.png" alt="Edvora" className="w-6 h-6 object-contain" />
                            </div>
                            <div>
                                <h3 className="font-black text-sm text-white">منوی مدیریت ادورا</h3>
                                <span className="text-[10px] text-cyan-400 font-bold flex items-center gap-1">
                                    <Sparkles size={10} />
                                    <span>کنترل پنل مرکزی سیستم</span>
                                </span>
                            </div>
                        </div>

                        <button
                            type="button"
                            onClick={onClose}
                            className="w-8 h-8 rounded-full bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center border border-slate-700 active:scale-95 transition-all"
                            aria-label="بستن منو"
                        >
                            <X size={17} />
                        </button>
                    </div>
                </div>

                {/* Admin User Profile Card */}
                <div className="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700 flex items-center justify-between gap-3 shadow-sm">
                    <div className="flex items-center gap-3 min-w-0">
                        <div className="relative w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-black text-white text-base shadow-md shrink-0">
                            {user?.name ? user.name.charAt(0).toUpperCase() : 'A'}
                            <span className="absolute -bottom-0.5 -left-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-slate-900" />
                        </div>
                        <div className="min-w-0">
                            <h4 className="font-extrabold text-white text-sm truncate">{user?.name || 'مدیر کل'}</h4>
                            <div className="flex items-center gap-1.5 mt-0.5">
                                <span className="px-2 py-0.2 rounded-full text-[10px] font-bold bg-brand-500/20 text-brand-300 border border-brand-500/30">
                                    مدیر ارشد (ADMIN)
                                </span>
                            </div>
                        </div>
                    </div>

                    <a
                        href="/"
                        target="_blank"
                        rel="noreferrer"
                        className="px-3 py-1.5 rounded-xl bg-slate-700/80 hover:bg-slate-700 text-slate-200 text-xs font-bold flex items-center gap-1 shrink-0 border border-slate-600"
                    >
                        <ExternalLink size={13} />
                        <span>سایت</span>
                    </a>
                </div>

                {/* PWA Install Promotion Banner Card */}
                <PwaInstallButton variant="card" />

                {/* Navigation Sections */}
                {navSections.map((sec, idx) => (
                    <div key={idx} className="space-y-2">
                        <span className="text-[11px] font-bold text-slate-400 block px-1">
                            {sec.title}
                        </span>
                        <div className="grid grid-cols-1 gap-1.5">
                            {sec.items.map((item, iIdx) => {
                                const Icon = item.icon;
                                return (
                                    <Link
                                        key={iIdx}
                                        href={item.href}
                                        onClick={onClose}
                                        className={`flex items-center justify-between p-3 rounded-2xl border transition-all active:scale-[0.99] ${
                                            item.active
                                                ? 'bg-slate-800 border-cyan-500/40 text-cyan-300 shadow-sm'
                                                : 'bg-slate-800/40 border-slate-800 text-slate-300 hover:bg-slate-800 hover:text-white'
                                        }`}
                                    >
                                        <div className="flex items-center gap-3 min-w-0">
                                            <div className={`w-9 h-9 rounded-xl border flex items-center justify-center shrink-0 ${item.color}`}>
                                                <Icon size={18} />
                                            </div>
                                            <span className="font-bold text-xs truncate">{item.name}</span>
                                        </div>

                                        <div className="flex items-center gap-2 shrink-0">
                                            {item.badge && (
                                                <span className="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                                    {item.badge}
                                                </span>
                                            )}
                                            <ChevronLeft size={16} className="text-slate-500" />
                                        </div>
                                    </Link>
                                );
                            })}
                        </div>
                    </div>
                ))}

                {/* Logout Button */}
                <div className="pt-2">
                    <Link
                        method="post"
                        as="button"
                        href="/logout"
                        onClick={onClose}
                        className="w-full flex items-center justify-center gap-2 p-3.5 rounded-2xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 font-bold text-xs transition-colors"
                    >
                        <LogOut size={16} />
                        <span>خروج امن از حساب مدیریت</span>
                    </Link>
                </div>
            </div>
        </div>
    );
}
