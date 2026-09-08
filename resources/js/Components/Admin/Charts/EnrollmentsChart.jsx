import React, { useState } from 'react';
import { BookMarked } from 'lucide-react';

export default function EnrollmentsChart({ chartData }) {
    const [hoveredIndex, setHoveredIndex] = useState(null);

    const labels = chartData?.labels || [];
    const data = chartData?.data || [];

    const maxValue = Math.max(...data, 5);
    const totalEnrollments = data.reduce((acc, val) => acc + val, 0);

    return (
        <div className="rounded-3xl p-6 bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 shadow-xl flex flex-col justify-between">
            <div className="flex items-center justify-between pb-4 border-b border-slate-800/80">
                <div className="flex items-center gap-3">
                    <div className="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <BookMarked size={20} />
                    </div>
                    <div>
                        <h3 className="text-base font-bold text-white">ثبت‌نام در دوره‌ها (۱۲ ماه اخیر)</h3>
                        <p className="text-xs text-slate-400">
                            مجموع ثبت‌نام‌ها: <span className="text-emerald-400 font-bold">{totalEnrollments.toLocaleString('fa-IR')}</span> دوره
                        </p>
                    </div>
                </div>
            </div>

            {/* Bar Chart Container */}
            <div className="mt-6 flex items-end justify-between gap-2 h-48 px-2 relative">
                {data.map((val, idx) => {
                    const heightPercent = Math.max((val / maxValue) * 100, 4);
                    const isHovered = hoveredIndex === idx;

                    return (
                        <div
                            key={idx}
                            className="flex-1 flex flex-col items-center h-full justify-end group cursor-pointer relative"
                            onMouseEnter={() => setHoveredIndex(idx)}
                            onMouseLeave={() => setHoveredIndex(null)}
                        >
                            {/* Hover Value Tooltip */}
                            {isHovered && (
                                <div className="absolute -top-10 bg-slate-950 text-white text-[11px] px-2.5 py-1 rounded-lg border border-emerald-500/40 shadow-xl whitespace-nowrap z-20 pointer-events-none">
                                    <span className="text-emerald-400 font-bold">{val.toLocaleString('fa-IR')}</span> ثبت‌نام
                                </div>
                            )}

                            {/* Bar Pill */}
                            <div className="w-full max-w-[28px] h-full flex items-end">
                                <div
                                    style={{ height: `${heightPercent}%` }}
                                    className={`w-full rounded-t-xl transition-all duration-300 ${
                                        isHovered
                                            ? 'bg-gradient-to-t from-emerald-600 to-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.4)]'
                                            : 'bg-gradient-to-t from-emerald-600/80 to-emerald-500/60 hover:from-emerald-500 hover:to-emerald-400'
                                    }`}
                                />
                            </div>

                            {/* Label */}
                            <span
                                className={`text-[10px] mt-2 truncate w-full text-center transition-colors ${
                                    isHovered ? 'text-emerald-300 font-bold' : 'text-slate-400'
                                }`}
                            >
                                {labels[idx]}
                            </span>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
