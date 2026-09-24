import React from 'react';
import { useLanguage } from '@/Context/LanguageContext';
import { useTheme } from '@/Context/ThemeContext';

export default function CourseStatusDonut({ courseStats = {} }) {
    const { t } = useLanguage();
    const { isDark } = useTheme();
    const completed = courseStats.completed || 0;
    const inProgress = courseStats.in_progress || 0;
    const notStarted = courseStats.not_started || 0;

    const total = completed + inProgress + notStarted;

    const data = [
        { label: t('status_completed'), count: completed, color: '#10B981' },
        { label: t('status_in_progress'), count: inProgress, color: isDark ? '#00F0FF' : '#1F8FFF' },
        { label: t('status_not_started'), count: notStarted, color: isDark ? '#334155' : '#94A3B8' },
    ];

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
            <div className="relative w-44 h-44 flex items-center justify-center">
                <svg className="w-full h-full transform -rotate-90" viewBox="0 0 160 160">
                    <circle
                        cx="80"
                        cy="80"
                        r={radius}
                        fill="transparent"
                        stroke={isDark ? '#1e293b' : '#f1f5f9'}
                        strokeWidth="18"
                    />

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
                                    style={{
                                        filter: isDark && slice.color !== '#334155' ? `drop-shadow(0 0 6px ${slice.color}80)` : 'none'
                                    }}
                                />
                            );
                        })}
                </svg>

                <div className="absolute inset-0 flex flex-col items-center justify-center text-center">
                    <span className="text-2xl sm:text-3xl font-extrabold text-slate-800 dark:text-white dark:drop-shadow-[0_0_15px_rgba(0,240,255,0.4)] leading-none">
                        {total}
                    </span>
                    <span className="text-[11px] font-semibold text-slate-400 dark:text-slate-400 mt-1 uppercase tracking-wider">
                        {t('all_courses')}
                    </span>
                </div>
            </div>

            <div className="flex flex-wrap justify-center gap-x-4 gap-y-2 mt-6">
                {slices.map((item, idx) => (
                    <div key={idx} className="flex items-center gap-2 text-xs">
                        <span
                            className="w-2.5 h-2.5 rounded-full shrink-0 shadow-sm"
                            style={{
                                backgroundColor: item.color,
                                boxShadow: isDark && item.color !== '#334155' ? `0 0 8px ${item.color}` : 'none'
                            }}
                        />
                        <span className="text-slate-500 dark:text-slate-400 font-medium">{item.label}</span>
                        <span className="font-bold text-slate-800 dark:text-white bg-slate-100 dark:bg-slate-800/80 border dark:border-slate-700/60 px-1.5 py-0.2 rounded text-[11px]">
                            {item.count}
                        </span>
                    </div>
                ))}
            </div>
        </div>
    );
}

