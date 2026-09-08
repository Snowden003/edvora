import React from 'react';
import { Layers } from 'lucide-react';

export default function CategoryPieChart({ chartData }) {
    const labels = chartData?.labels || [];
    const data = chartData?.data || [];

    const total = data.reduce((acc, val) => acc + val, 0) || 1;

    const palette = [
        'bg-brand-500',
        'bg-emerald-500',
        'bg-amber-500',
        'bg-rose-500',
        'bg-purple-500',
        'bg-cyan-500',
        'bg-pink-500',
        'bg-indigo-500',
    ];

    return (
        <div className="rounded-3xl p-6 bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 shadow-xl flex flex-col justify-between">
            <div className="flex items-center gap-3 pb-4 border-b border-slate-800/80">
                <div className="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20">
                    <Layers size={20} />
                </div>
                <div>
                    <h3 className="text-base font-bold text-white">دوره‌ها بر اساس دسته‌بندی</h3>
                    <p className="text-xs text-slate-400">
                        توزیع دوره‌ها در دسته‌بندی‌های مختلف
                    </p>
                </div>
            </div>

            {/* List with Progress Bars */}
            <div className="mt-6 space-y-3.5">
                {labels.length === 0 ? (
                    <div className="text-center py-6 text-slate-400 text-xs">
                        دوره‌ای ثبت نشده است
                    </div>
                ) : (
                    labels.map((label, idx) => {
                        const count = data[idx] || 0;
                        const percent = Math.round((count / total) * 100);
                        const colorClass = palette[idx % palette.length];

                        return (
                            <div key={idx} className="space-y-1.5">
                                <div className="flex items-center justify-between text-xs">
                                    <span className="text-slate-200 font-semibold">{label}</span>
                                    <div className="flex items-center gap-1.5">
                                        <span className="text-white font-bold">{count.toLocaleString('fa-IR')}</span>
                                        <span className="text-slate-400 text-[10px]">({percent}٪)</span>
                                    </div>
                                </div>
                                <div className="w-full h-2 rounded-full bg-slate-800/80 overflow-hidden">
                                    <div
                                        className={`h-full rounded-full ${colorClass} transition-all duration-500`}
                                        style={{ width: `${percent}%` }}
                                    />
                                </div>
                            </div>
                        );
                    })
                )}
            </div>
        </div>
    );
}
