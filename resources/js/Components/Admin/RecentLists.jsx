import React from 'react';
import { router } from '@inertiajs/react';
import {
    UserCheck,
    GraduationCap,
    MessageSquare,
    ChevronLeft,
    Clock,
    CheckCircle2,
    AlertCircle,
    Mail,
    ArrowUpRight,
    Check
} from 'lucide-react';

export default function RecentLists({ pendingTeachers = [], recentCourses = [], recentMessages = [] }) {
    const handleVerifyTeacher = (id) => {
        if (confirm('آیا از تایید و فعال‌سازی این استاد اطمینان دارید؟ استاد بلافاصله در آمار و بخش اساتید وبسایت نمایش داده خواهد شد.')) {
            router.post(`/admin/teachers/${id}/verify`, {}, {
                preserveScroll: true,
            });
        }
    };
    return (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {/* 1. Pending Teacher Verifications */}
            <div className="rounded-3xl p-6 bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 shadow-xl flex flex-col justify-between">
                <div>
                    <div className="flex items-center justify-between pb-4 border-b border-slate-800/80">
                        <div className="flex items-center gap-2.5">
                            <div className="p-2 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                <UserCheck size={18} />
                            </div>
                            <h3 className="text-sm font-bold text-white">اساتید در انتظار تایید</h3>
                        </div>
                        <a
                            href="/admin-panel/teachers"
                            className="text-xs text-brand-400 hover:text-brand-300 font-bold flex items-center gap-1"
                        >
                            <span>مشاهده همه</span>
                            <ChevronLeft size={14} />
                        </a>
                    </div>

                    <div className="mt-4 space-y-3">
                        {pendingTeachers.length === 0 ? (
                            <div className="py-8 text-center text-xs text-slate-400">
                                درخواست تایید مدرکی در انتظار نیست ✨
                            </div>
                        ) : (
                            pendingTeachers.map((t) => (
                                <div
                                    key={t.id}
                                    className="p-3 rounded-2xl bg-slate-950/50 border border-slate-800/60 flex items-center justify-between gap-3 hover:border-amber-500/30 transition-colors"
                                >
                                    <div className="flex items-center gap-3 min-w-0">
                                        <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-600 to-amber-400 text-white font-bold flex items-center justify-center text-xs shrink-0">
                                            {t.name.charAt(0)}
                                        </div>
                                        <div className="min-w-0">
                                            <h4 className="text-xs font-bold text-white truncate">{t.name}</h4>
                                            <p className="text-[11px] text-slate-400 truncate">{t.specialization}</p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-1.5 shrink-0">
                                        <button
                                            type="button"
                                            onClick={() => handleVerifyTeacher(t.id)}
                                            className="px-2.5 py-1 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-400 border border-emerald-500/30 text-[11px] font-bold transition-all active:scale-95 flex items-center gap-1"
                                            title="تایید و فعال‌سازی فوری"
                                        >
                                            <Check size={12} />
                                            <span>تایید</span>
                                        </button>
                                        <a
                                            href="/admin-panel/teachers"
                                            target="_blank"
                                            rel="noreferrer"
                                            className="px-2 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700/60 text-[11px] font-bold shrink-0 transition-colors"
                                        >
                                            بررسی
                                        </a>
                                    </div>
                                </div>
                            ))
                        )}
                    </div>
                </div>
            </div>

            {/* 2. Recent Courses */}
            <div className="rounded-3xl p-6 bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 shadow-xl flex flex-col justify-between">
                <div>
                    <div className="flex items-center justify-between pb-4 border-b border-slate-800/80">
                        <div className="flex items-center gap-2.5">
                            <div className="p-2 rounded-xl bg-brand-500/10 text-brand-400 border border-brand-500/20">
                                <GraduationCap size={18} />
                            </div>
                            <h3 className="text-sm font-bold text-white">جدیدترین دوره‌ها</h3>
                        </div>
                        <a
                            href="/admin/courses"
                            className="text-xs text-brand-400 hover:text-brand-300 font-bold flex items-center gap-1"
                        >
                            <span>مشاهده همه</span>
                            <ChevronLeft size={14} />
                        </a>
                    </div>

                    <div className="mt-4 space-y-3">
                        {recentCourses.length === 0 ? (
                            <div className="py-8 text-center text-xs text-slate-400">
                                دوره‌ای یافت نشد
                            </div>
                        ) : (
                            recentCourses.map((c) => (
                                <div
                                    key={c.id}
                                    className="p-3 rounded-2xl bg-slate-950/50 border border-slate-800/60 flex items-center justify-between gap-3 hover:border-brand-500/30 transition-colors"
                                >
                                    <div className="min-w-0 flex-1">
                                        <div className="flex items-center gap-2">
                                            <h4 className="text-xs font-bold text-white truncate">{c.title}</h4>
                                        </div>
                                        <div className="flex items-center gap-3 mt-1 text-[11px] text-slate-400">
                                            <span className="text-brand-400 font-medium">{c.category}</span>
                                            <span>•</span>
                                            <span>{c.enrollments_count.toLocaleString('fa-IR')} دانشجو</span>
                                        </div>
                                    </div>
                                    <a
                                        href={`/admin/courses/${c.id}/edit`}
                                        className="p-1.5 rounded-lg text-slate-400 hover:text-brand-400 hover:bg-brand-500/10 transition-colors"
                                        title="ویرایش دوره"
                                    >
                                        <ArrowUpRight size={16} />
                                    </a>
                                </div>
                            ))
                        )}
                    </div>
                </div>
            </div>

            {/* 3. Recent Contact Inquiries */}
            <div className="rounded-3xl p-6 bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 shadow-xl flex flex-col justify-between">
                <div>
                    <div className="flex items-center justify-between pb-4 border-b border-slate-800/80">
                        <div className="flex items-center gap-2.5">
                            <div className="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <MessageSquare size={18} />
                            </div>
                            <h3 className="text-sm font-bold text-white">پیام‌های تماس دریافتی</h3>
                        </div>
                        <a
                            href="/admin-panel/contact-messages"
                            className="text-xs text-brand-400 hover:text-brand-300 font-bold flex items-center gap-1"
                        >
                            <span>مشاهده همه</span>
                            <ChevronLeft size={14} />
                        </a>
                    </div>

                    <div className="mt-4 space-y-3">
                        {recentMessages.length === 0 ? (
                            <div className="py-8 text-center text-xs text-slate-400">
                                پیام جدیدی وجود ندارد
                            </div>
                        ) : (
                            recentMessages.map((m) => (
                                <div
                                    key={m.id}
                                    className="p-3 rounded-2xl bg-slate-950/50 border border-slate-800/60 flex items-center justify-between gap-3 hover:border-emerald-500/30 transition-colors"
                                >
                                    <div className="min-w-0 flex-1">
                                        <div className="flex items-center justify-between gap-2">
                                            <h4 className="text-xs font-bold text-white truncate">{m.name}</h4>
                                            <span className="text-[10px] text-slate-400 shrink-0">{m.created_at}</span>
                                        </div>
                                        <p className="text-[11px] text-slate-400 truncate mt-0.5">{m.subject || 'بدون عنوان'}</p>
                                    </div>
                                    <a
                                        href={`/admin-panel/contact-messages/${m.id}`}
                                        className="p-1.5 rounded-lg text-slate-400 hover:text-emerald-400 hover:bg-emerald-500/10 transition-colors"
                                        title="مشاهده پیام"
                                    >
                                        <ArrowUpRight size={16} />
                                    </a>
                                </div>
                            ))
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
