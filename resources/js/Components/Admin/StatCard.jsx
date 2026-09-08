import React from 'react';
import { TrendingUp, TrendingDown, ArrowUpRight } from 'lucide-react';

export default function StatCard({
    title,
    value,
    subtitle,
    icon: Icon,
    color = 'brand',
    trend,
    trendLabel = 'نسبت به ماه قبل',
    trendUp = true,
    href,
}) {
    const colorStyles = {
        brand: {
            bg: 'from-brand-500/15 via-brand-500/5 to-transparent',
            border: 'border-brand-500/20 hover:border-brand-500/40',
            iconBg: 'bg-brand-500/20 text-brand-400',
            glow: 'group-hover:shadow-glow',
        },
        emerald: {
            bg: 'from-emerald-500/15 via-emerald-500/5 to-transparent',
            border: 'border-emerald-500/20 hover:border-emerald-500/40',
            iconBg: 'bg-emerald-500/20 text-emerald-400',
            glow: 'group-hover:shadow-[0_0_25px_-5px_rgba(16,185,129,0.3)]',
        },
        amber: {
            bg: 'from-amber-500/15 via-amber-500/5 to-transparent',
            border: 'border-amber-500/20 hover:border-amber-500/40',
            iconBg: 'bg-amber-500/20 text-amber-400',
            glow: 'group-hover:shadow-[0_0_25px_-5px_rgba(245,158,11,0.3)]',
        },
        purple: {
            bg: 'from-purple-500/15 via-purple-500/5 to-transparent',
            border: 'border-purple-500/20 hover:border-purple-500/40',
            iconBg: 'bg-purple-500/20 text-purple-400',
            glow: 'group-hover:shadow-glow-purple',
        },
        rose: {
            bg: 'from-rose-500/15 via-rose-500/5 to-transparent',
            border: 'border-rose-500/20 hover:border-rose-500/40',
            iconBg: 'bg-rose-500/20 text-rose-400',
            glow: 'group-hover:shadow-[0_0_25px_-5px_rgba(244,63,94,0.3)]',
        },
    };

    const style = colorStyles[color] || colorStyles.brand;

    const Content = (
        <div
            className={`relative overflow-hidden rounded-3xl p-6 bg-slate-900/60 backdrop-blur-xl border ${style.border} transition-all duration-300 group ${style.glow} hover:-translate-y-1`}
        >
            {/* Ambient background glow */}
            <div className={`absolute inset-0 bg-gradient-to-br ${style.bg} opacity-50 pointer-events-none`} />

            <div className="relative z-10 flex items-start justify-between">
                <div className="space-y-2">
                    <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">{title}</span>
                    <div className="text-3xl font-black text-white font-display tracking-tight">
                        {typeof value === 'number' ? value.toLocaleString('fa-IR') : value}
                    </div>
                </div>

                <div className={`p-3 rounded-2xl ${style.iconBg} shadow-inner transition-transform group-hover:scale-110`}>
                    <Icon size={24} />
                </div>
            </div>

            <div className="relative z-10 mt-4 pt-4 border-t border-slate-800/60 flex items-center justify-between text-xs">
                {subtitle && (
                    <span className="text-slate-400 font-medium">{subtitle}</span>
                )}
                {trend && (
                    <div className={`flex items-center gap-1 font-bold ${trendUp ? 'text-emerald-400' : 'text-rose-400'}`}>
                        {trendUp ? <TrendingUp size={14} /> : <TrendingDown size={14} />}
                        <span dir="ltr">{trend}</span>
                    </div>
                )}
                {href && (
                    <ArrowUpRight size={15} className="text-slate-500 group-hover:text-slate-300 transition-colors" />
                )}
            </div>
        </div>
    );

    return href ? (
        <a href={href} className="block focus:outline-none">
            {Content}
        </a>
    ) : (
        Content
    );
}
