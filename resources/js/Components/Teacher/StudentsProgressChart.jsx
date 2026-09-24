import React, { useState } from 'react';
import { Users, Star, BarChart3 } from 'lucide-react';

export default function StudentsProgressChart({
    labels = [],
    students = [],
    ratings = []
}) {
    const [hoveredIdx, setHoveredIdx] = useState(null);

    if (!labels || labels.length === 0) {
        return (
            <div className="h-64 flex flex-col items-center justify-center text-slate-400 gap-2">
                <BarChart3 size={36} className="text-slate-300 stroke-[1.5]" />
                <p className="text-sm font-medium">No course enrollment data to display yet.</p>
                <p className="text-xs text-slate-400">Published courses and student enrollments will appear here.</p>
            </div>
        );
    }

    const maxStudents = Math.max(...students, 10);

    return (
        <div className="w-full space-y-4">
            {/* Chart Legend */}
            <div className="flex items-center justify-end gap-4 text-xs">
                <div className="flex items-center gap-1.5 text-slate-500 font-medium">
                    <span className="w-3 h-3 rounded-md bg-gradient-to-t from-brand-600 to-indigo-500 inline-block" />
                    <span>Enrolled Students</span>
                </div>
                <div className="flex items-center gap-1.5 text-slate-500 font-medium">
                    <span className="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block ring-2 ring-amber-100" />
                    <span>Avg. Rating (/5)</span>
                </div>
            </div>

            {/* Bars Canvas Area */}
            <div className="h-56 flex items-end gap-3 sm:gap-6 pt-6 pb-2 border-b border-slate-200/80 px-2 overflow-x-auto">
                {labels.map((label, idx) => {
                    const studentCount = students[idx] || 0;
                    const rating = ratings[idx] || 0;
                    const heightPercent = Math.max(12, Math.round((studentCount / maxStudents) * 100));

                    return (
                        <div
                            key={idx}
                            className="flex-1 min-w-[50px] max-w-[80px] flex flex-col items-center h-full justify-end relative group cursor-pointer"
                            onMouseEnter={() => setHoveredIdx(idx)}
                            onMouseLeave={() => setHoveredIdx(null)}
                        >
                            {/* Hover Tooltip Card */}
                            {hoveredIdx === idx && (
                                <div className="absolute -top-16 z-20 px-3 py-2 bg-slate-900 text-white rounded-xl shadow-xl text-center whitespace-nowrap animate-in fade-in zoom-in-95 duration-150 pointer-events-none">
                                    <p className="text-[11px] font-bold text-slate-200">{label}</p>
                                    <div className="flex items-center justify-center gap-2 mt-0.5 text-[10px]">
                                        <span className="text-brand-300 font-semibold">{studentCount} Students</span>
                                        <span>•</span>
                                        <span className="text-amber-300 font-semibold">{rating} ★</span>
                                    </div>
                                    <div className="w-2 h-2 bg-slate-900 rotate-45 mx-auto -mb-3 mt-1" />
                                </div>
                            )}

                            {/* Rating Dot on Top of Bar */}
                            <div className="mb-1.5 flex items-center gap-0.5">
                                <span className="text-[10px] font-bold text-amber-600 bg-amber-50 px-1 py-0.2 rounded border border-amber-200/60">
                                    {rating}★
                                </span>
                            </div>

                            {/* Bar */}
                            <div className="w-full bg-slate-100 rounded-t-xl overflow-hidden flex flex-col justify-end h-full p-0.5 group-hover:bg-slate-200/60 transition-colors">
                                <div
                                    className="w-full bg-gradient-to-t from-brand-600 to-indigo-500 rounded-t-lg transition-all duration-700 ease-out flex items-start justify-center pt-1 shadow-sm"
                                    style={{ height: `${heightPercent}%` }}
                                >
                                    <span className="text-[9px] font-extrabold text-white/90">
                                        {studentCount}
                                    </span>
                                </div>
                            </div>

                            {/* X-axis Label */}
                            <span
                                className="text-[10px] text-slate-500 font-medium truncate w-full text-center mt-2 group-hover:text-brand-600 transition-colors"
                                title={label}
                            >
                                {label}
                            </span>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
