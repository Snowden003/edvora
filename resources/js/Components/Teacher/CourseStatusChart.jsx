import React from 'react';

export default function CourseStatusChart({ statusCounts = {} }) {
    const active = statusCounts.active || 0;
    const draft = statusCounts.draft || 0;
    const completed = statusCounts.completed || 0;
    const archived = statusCounts.archived || 0;

    const total = active + draft + completed + archived;

    const data = [
        { label: 'Published / Active', count: active, color: '#1F8FFF' },
        { label: 'Draft', count: draft, color: '#FBBF24' },
        { label: 'Completed', count: completed, color: '#10B981' },
        { label: 'Archived', count: archived, color: '#94A3B8' },
    ];

    // Calculate SVG circle strokeDasharray and strokeDashoffset
    const radius = 64;
    const circumference = 2 * Math.PI * radius; // ~402.12

    let accumulatedPercentage = 0;
    const slices = data.map((item) => {
        const percentage = total > 0 ? item.count / total : 0;
        const strokeDasharray = `${percentage * circumference} ${circumference}`;
        const strokeDashoffset = -accumulatedPercentage * circumference;
        accumulatedPercentage += percentage;
        return {
            ...item,
            strokeDasharray,
            strokeDashoffset,
        };
    });

    return (
        <div className="flex flex-col items-center justify-center w-full py-2">
            {/* SVG Donut Chart */}
            <div className="relative w-44 h-44 flex items-center justify-center">
                <svg className="w-full h-full transform -rotate-90" viewBox="0 0 160 160">
                    {/* Background Ring */}
                    <circle
                        cx="80"
                        cy="80"
                        r={radius}
                        fill="transparent"
                        stroke="#f1f5f9"
                        strokeWidth="18"
                    />

                    {/* Slices */}
                    {total > 0 &&
                        slices.map((slice, i) => {
                            if (slice.count <= 0) return null;
                            return (
                                <circle
                                    key={i}
                                    cx="80"
                                    cy="80"
                                    r={radius}
                                    fill="transparent"
                                    stroke={slice.color}
                                    strokeWidth="18"
                                    strokeDasharray={slice.strokeDasharray}
                                    strokeDashoffset={slice.strokeDashoffset}
                                    strokeLinecap="round"
                                    className="transition-all duration-700 ease-out hover:opacity-80"
                                />
                            );
                        })}
                </svg>

                {/* Center Content */}
                <div className="absolute inset-0 flex flex-col items-center justify-center text-center">
                    <span className="text-2xl sm:text-3xl font-extrabold text-slate-800 leading-none">
                        {total}
                    </span>
                    <span className="text-[11px] font-semibold text-slate-400 mt-1 uppercase tracking-wider">
                        Courses
                    </span>
                </div>
            </div>

            {/* Custom Legend */}
            <div className="flex flex-wrap justify-center gap-x-4 gap-y-2 mt-6">
                {slices.map((item, idx) => (
                    <div key={idx} className="flex items-center gap-2 text-xs">
                        <span
                            className="w-2.5 h-2.5 rounded-full shrink-0 shadow-sm"
                            style={{ backgroundColor: item.color }}
                        />
                        <span className="text-slate-500 font-medium">{item.label}</span>
                        <span className="font-bold text-slate-800 bg-slate-100 px-1.5 py-0.2 rounded text-[11px]">
                            {item.count}
                        </span>
                    </div>
                ))}
            </div>
        </div>
    );
}
