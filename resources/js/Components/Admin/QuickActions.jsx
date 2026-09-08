import React from 'react';
import { Link } from '@inertiajs/react';
import {
    PlusCircle,
    UserCheck,
    CalendarPlus,
    BookPlus,
    FileSpreadsheet,
    MessageSquare,
    ExternalLink
} from 'lucide-react';

export default function QuickActions() {
    const actions = [
        {
            title: 'افزودن دوره جدید',
            description: 'تعریف دوره، سرفصل‌ها و مدرس',
            icon: PlusCircle,
            href: '/admin/courses/create',
            color: 'from-brand-600 to-brand-500',
            textColor: 'text-brand-400',
            border: 'border-brand-500/20 hover:border-brand-500/40',
            isSpa: true,
        },
        {
            title: 'بررسی اساتید منتظر',
            description: 'بررسی مدارک و تایید صلاحیت',
            icon: UserCheck,
            href: '/admin-panel/teachers',
            color: 'from-amber-600 to-amber-500',
            textColor: 'text-amber-400',
            border: 'border-amber-500/20 hover:border-amber-500/40',
        },
        {
            title: 'ایجاد رویداد / وبینار',
            description: 'برگزاری کارگاه و مدیریت ثبت‌نام',
            icon: CalendarPlus,
            href: '/admin/events/create',
            color: 'from-purple-600 to-purple-500',
            textColor: 'text-purple-400',
            border: 'border-purple-500/20 hover:border-purple-500/40',
        },
        {
            title: 'افزودن کتاب به کتابخانه',
            description: 'بارگذاری فایل PDF و توضیحات',
            icon: BookPlus,
            href: '/admin/books/create',
            color: 'from-emerald-600 to-emerald-500',
            textColor: 'text-emerald-400',
            border: 'border-emerald-500/20 hover:border-emerald-500/40',
            isSpa: true,
        },
    ];

    return (
        <div className="space-y-4">
            <h3 className="text-sm font-bold text-slate-400 uppercase tracking-wider px-1">
                دسترسی سریع و عملیات متداول
            </h3>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {actions.map((action, idx) => {
                    const Icon = action.icon;
                    const content = (
                        <div
                            className={`p-4 rounded-2xl bg-slate-900/60 backdrop-blur-xl border ${action.border} transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group flex items-center gap-3.5 cursor-pointer`}
                        >
                            <div
                                className={`w-11 h-11 rounded-xl bg-gradient-to-tr ${action.color} flex items-center justify-center text-white shadow-md shrink-0 transition-transform group-hover:scale-105`}
                            >
                                <Icon size={20} />
                            </div>
                            <div className="min-w-0 flex-1">
                                <h4 className="text-xs font-bold text-white group-hover:text-brand-300 transition-colors truncate">
                                    {action.title}
                                </h4>
                                <p className="text-[11px] text-slate-400 truncate mt-0.5">{action.description}</p>
                            </div>
                        </div>
                    );

                    return action.isSpa ? (
                        <Link key={idx} href={action.href}>
                            {content}
                        </Link>
                    ) : (
                        <a key={idx} href={action.href}>
                            {content}
                        </a>
                    );
                })}
            </div>
        </div>
    );
}
