import React, { useState } from 'react';
import { Activity } from 'lucide-react';
import { useLanguage } from '../../Context/LanguageContext';

export default function WeeklyActivityChart({
    weeklyProgress = null,
    labels = null,
    data = null,
    completionRate = 0
}) {
    const { t, isRtl } = useLanguage();
    const [hoveredIdx, setHoveredIdx] = useState(null);

    const chartLabels = labels || weeklyProgress?.labels || ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
    const chartData = data || weeklyProgress?.data || [0, 0, 0, 0, 0, 0, 0];

    const safeData = chartData.length > 0 ? chartData : [0, 0, 0, 0, 0, 0, 0];
    const rawLabels = chartLabels.length > 0 ? chartLabels : ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

    const dayKeyMap = {
        'Mon': 'day_mon',
        'Tue': 'day_tue',
        'Wed': 'day_wed',
        'Thu': 'day_thu',
        'Fri': 'day_fri',
        'Sat': 'day_sat',
        'Sun': 'day_sun',
    };

    const maxVal = Math.max(...safeData, 8);

    return (
        <div className="w-full space-y-4">
            <div className="h-56 flex items-end gap-2 sm:gap-6 pt-6 pb-2 border-b border-slate-200/80 dark:border-slate-800/80 px-2">
                {rawLabels.map((day, idx) => {
                    const val = safeData[idx] || 0;
                    const heightPercent = Math.max(8, Math.round((val / maxVal) * 100));
                    const localizedDay = dayKeyMap[day] ? t(dayKeyMap[day], day) : day;

                    return (
                        <div
                            key={idx}
                            className="flex-1 flex flex-col items-center h-full justify-end relative group cursor-pointer"
                            onMouseEnter={() => setHoveredIdx(idx)}
                            onMouseLeave={() => setHoveredIdx(null)}
                        >
                            {/* Hover Tooltip */}
                            {hoveredIdx === idx && (
                                <div className="absolute -top-12 z-20 px-2.5 py-1 bg-slate-900 dark:bg-[#071329] border border-slate-700 dark:border-cyan-500/40 text-white dark:text-cyan-200 rounded-lg shadow-lg dark:shadow-[0_0_20px_rgba(0,240,255,0.3)] text-[10px] font-bold whitespace-nowrap animate-in fade-in zoom-in-95 duration-150 pointer-events-none">
                                    <span>{val} {t('activity_xp', 'Activity XP')}</span>
                                    <div className="w-1.5 h-1.5 bg-slate-900 dark:bg-[#071329] border-r border-b border-slate-700 dark:border-cyan-500/40 rotate-45 mx-auto -mb-1 mt-0.5" />
                                </div>
                            )}

                            {/* Bar Track */}
                            <div className="w-full bg-slate-100 dark:bg-slate-800/60 rounded-t-xl overflow-hidden flex flex-col justify-end h-full p-0.5 group-hover:bg-slate-200/70 group-hover:dark:bg-slate-700/60 transition-colors">
                                <div
                                    className="w-full bg-gradient-to-t from-brand-600 to-indigo-500 dark:from-cyan-500 dark:via-brand-600 dark:to-indigo-500 rounded-t-lg transition-all duration-700 ease-out flex items-start justify-center pt-1 shadow-sm dark:shadow-[0_0_15px_rgba(0,240,255,0.35)]"
                                    style={{ height: `${heightPercent}%` }}
                                >
                                    {val > 0 && (
                                        <span className="text-[9px] font-bold text-white/95">
                                            {val}
                                        </span>
                                    )}
                                </div>
                            </div>

                            {/* Day Label */}
                            <span className="text-[10px] text-slate-500 dark:text-slate-400 font-semibold mt-2 group-hover:text-brand-600 dark:group-hover:text-cyan-400 transition-colors">
                                {localizedDay}
                            </span>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
