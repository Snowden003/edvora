import React from 'react';
import { UserCheck } from 'lucide-react';

export default function TeacherStatusChart({ chartData }) {
    const active = chartData?.active || 0;
    const pending = chartData?.pending || 0;
    const rejected = chartData?.rejected || 0;
    const suspended = chartData?.suspended || 0;

    const total = active + pending + rejected + suspended || 1;

    const items = [
        { label: 'فعال', count: active, color: '#10b981', bgClass: 'bg-emerald-500' },
        { label: 'در انتظار تایید', count: pending, color: '#f59e0b', bgClass: 'bg-amber-500' },
        { label: 'رد شده', count: rejected, color: '#ef4444', bgClass: 'bg-rose-500' },
        { label: 'معلق', count: suspended, color: '#64748b', bgClass: 'bg-slate-500' },
    ];

    // SVG doughnut calculation
    const size = 160;
    const strokeWidth = 22;
    const radius = (size - strokeWidth) / 2;
    const circumference = 2 * Math.PI * radius;

    let accumulatedOffset = 0;

    return (
        <div className="rounded-3xl p-6 bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 shadow-xl flex flex-col justify-between">
            <div className="flex items-center gap-3 pb-4 border-b border-slate-800/80">
                <div className="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    <UserCheck size={20} />
                </div>
                <div>
                    <h3 className="text-base font-bold text-white">وضعیت اساتید</h3>
                    <p className="text-xs text-slate-400">
                        مجموع: <span className="text-amber-400 font-bold">{total.toLocaleString('fa-IR')}</span> مدرس
                    </p>
                </div>
            </div>

            {/* Doughnut Chart & Legends */}
            <div className="mt-6 flex flex-col sm:flex-row items-center justify-around gap-6">
                {/* SVG Doughnut */}
                <div className="relative w-40 h-40 flex items-center justify-center shrink-0">
                    <svg width={size} height={size} className="transform -rotate-90">
                        {/* Background Ring */}
                        <circle
                            cx={size / 2}
                            cy={size / 2}
                            r={radius}
                            fill="transparent"
                            stroke="rgba(255, 255, 255, 0.05)"
                            strokeWidth={strokeWidth}
                        />

                        {/* Segments */}
                        {items.map((item, idx) => {
                            const ratio = item.count / total;
                            const strokeDasharray = `${circumference * ratio} ${circumference * (1 - ratio)}`;
                            const strokeDashoffset = -accumulatedOffset;
                            accumulatedOffset += circumference * ratio;

                            return ratio > 0 ? (
                                <circle
                                    key={idx}
                                    cx={size / 2}
                                    cy={size / 2}
                                    r={radius}
                                    fill="transparent"
                                    stroke={item.color}
                                    strokeWidth={strokeWidth}
                                    strokeDasharray={strokeDasharray}
                                    strokeDashoffset={strokeDashoffset}
                                    className="transition-all duration-500"
                                />
                            ) : null;
                        })}
                    </svg>

                    {/* Inner Center Text */}
                    <div className="absolute inset-0 flex flex-col items-center justify-center text-center">
                        <span className="text-2xl font-black text-white font-display">
                            {active.toLocaleString('fa-IR')}
                        </span>
                        <span className="text-[10px] text-emerald-400 font-bold">مدرس تایید شده</span>
                    </div>
                </div>

                {/* Legend list */}
                <div className="flex-1 space-y-2.5 w-full">
                    {items.map((item, idx) => {
                        const percent = Math.round((item.count / total) * 100);
                        return (
                            <div key={idx} className="flex items-center justify-between text-xs">
                                <div className="flex items-center gap-2">
                                    <span className={`w-2.5 h-2.5 rounded-full ${item.bgClass}`} />
                                    <span className="text-slate-300 font-medium">{item.label}</span>
                                </div>
                                <div className="flex items-center gap-2">
                                    <span className="text-white font-bold">{item.count.toLocaleString('fa-IR')}</span>
                                    <span className="text-slate-400 text-[10px]">({percent}٪)</span>
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>
        </div>
    );
}
