import React, { useState } from 'react';
import { Users, TrendingUp } from 'lucide-react';

export default function RegistrationsChart({ chartData }) {
    const [activeTab, setActiveTab] = useState('all');
    const [hoveredIndex, setHoveredIndex] = useState(null);

    const labels = chartData?.labels || [];
    const datasets = chartData?.datasets || { all: [], student: [], teacher: [] };
    const currentData = datasets[activeTab] || [];

    const maxValue = Math.max(...currentData, 5);
    const totalCount = currentData.reduce((acc, val) => acc + val, 0);

    const tabs = [
        { key: 'all', label: 'همه کاربران' },
        { key: 'student', label: 'دانشجویان' },
        { key: 'teacher', label: 'اساتید' },
    ];

    // SVG coordinates
    const height = 220;
    const width = 600;
    const padding = { top: 20, right: 20, bottom: 40, left: 40 };

    const chartWidth = width - padding.left - padding.right;
    const chartHeight = height - padding.top - padding.bottom;

    const points = currentData.map((val, idx) => {
        const x = padding.left + (idx / (currentData.length - 1 || 1)) * chartWidth;
        const y = padding.top + chartHeight - (val / maxValue) * chartHeight;
        return { x, y, val, label: labels[idx] };
    });

    const pathD = points.reduce((acc, p, idx) => {
        return idx === 0 ? `M ${p.x} ${p.y}` : `${acc} L ${p.x} ${p.y}`;
    }, '');

    const areaD = points.length > 0
        ? `${pathD} L ${points[points.length - 1].x} ${padding.top + chartHeight} L ${points[0].x} ${padding.top + chartHeight} Z`
        : '';

    return (
        <div className="rounded-3xl p-6 bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 shadow-xl flex flex-col justify-between">
            {/* Header with Title and Tabs */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-800/80">
                <div className="flex items-center gap-3">
                    <div className="p-2.5 rounded-xl bg-brand-500/10 text-brand-400 border border-brand-500/20">
                        <Users size={20} />
                    </div>
                    <div>
                        <h3 className="text-base font-bold text-white">روند ثبت‌نام کاربران (۱۲ ماه اخیر)</h3>
                        <p className="text-xs text-slate-400">
                            مجموع ثبت‌نام دوره: <span className="text-brand-400 font-bold">{totalCount.toLocaleString('fa-IR')}</span> نفر
                        </p>
                    </div>
                </div>

                {/* Filter Tabs */}
                <div className="flex items-center gap-1 p-1 rounded-xl bg-slate-950/60 border border-slate-800 text-xs font-semibold">
                    {tabs.map((tab) => (
                        <button
                            key={tab.key}
                            onClick={() => setActiveTab(tab.key)}
                            className={`px-3 py-1.5 rounded-lg transition-all ${
                                activeTab === tab.key
                                    ? 'bg-brand-500 text-white shadow-glow font-bold'
                                    : 'text-slate-400 hover:text-white'
                            }`}
                        >
                            {tab.label}
                        </button>
                    ))}
                </div>
            </div>

            {/* SVG Chart Area */}
            <div className="relative mt-4 w-full h-[240px] flex items-center justify-center">
                <svg
                    viewBox={`0 0 ${width} ${height}`}
                    className="w-full h-full overflow-visible"
                    preserveAspectRatio="none"
                >
                    <defs>
                        <linearGradient id="regGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stopColor="#0c8de9" stopOpacity="0.4" />
                            <stop offset="100%" stopColor="#0c8de9" stopOpacity="0.0" />
                        </linearGradient>
                    </defs>

                    {/* Horizontal Grid lines */}
                    {[0, 0.25, 0.5, 0.75, 1].map((ratio, i) => {
                        const y = padding.top + chartHeight * (1 - ratio);
                        const val = Math.round(maxValue * ratio);
                        return (
                            <g key={i}>
                                <line
                                    x1={padding.left}
                                    y1={y}
                                    x2={width - padding.right}
                                    y2={y}
                                    stroke="rgba(148, 163, 184, 0.08)"
                                    strokeDasharray="4 4"
                                />
                                <text
                                    x={padding.left - 8}
                                    y={y + 4}
                                    fill="#64748b"
                                    fontSize="10"
                                    textAnchor="end"
                                >
                                    {val.toLocaleString('fa-IR')}
                                </text>
                            </g>
                        );
                    })}

                    {/* Area fill */}
                    {areaD && <path d={areaD} fill="url(#regGradient)" />}

                    {/* Line stroke */}
                    {pathD && (
                        <path
                            d={pathD}
                            fill="none"
                            stroke="#0c8de9"
                            strokeWidth="3"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                        />
                    )}

                    {/* Interactive Points */}
                    {points.map((p, idx) => (
                        <g key={idx} className="cursor-pointer">
                            {/* Hover line */}
                            {hoveredIndex === idx && (
                                <line
                                    x1={p.x}
                                    y1={padding.top}
                                    x2={p.x}
                                    y2={padding.top + chartHeight}
                                    stroke="#38bdf8"
                                    strokeWidth="1.5"
                                    strokeDasharray="2 2"
                                />
                            )}
                            <circle
                                cx={p.x}
                                cy={p.y}
                                r={hoveredIndex === idx ? 6 : 4}
                                fill="#0c8de9"
                                stroke="#0f172a"
                                strokeWidth="2.5"
                                className="transition-all duration-200"
                            />
                            {/* Invisible wider hover hit area */}
                            <rect
                                x={p.x - 15}
                                y={padding.top}
                                width={30}
                                height={chartHeight}
                                fill="transparent"
                                onMouseEnter={() => setHoveredIndex(idx)}
                                onMouseLeave={() => setHoveredIndex(null)}
                            />
                            {/* X-axis labels */}
                            <text
                                x={p.x}
                                y={height - 10}
                                fill={hoveredIndex === idx ? '#38bdf8' : '#64748b'}
                                fontSize="10"
                                textAnchor="middle"
                                fontWeight={hoveredIndex === idx ? 'bold' : 'normal'}
                            >
                                {p.label}
                            </text>
                        </g>
                    ))}
                </svg>

                {/* Tooltip Overlay */}
                {hoveredIndex !== null && points[hoveredIndex] && (
                    <div
                        className="absolute pointer-events-none transform -translate-x-1/2 -translate-y-full bg-slate-950 text-white text-xs px-3 py-1.5 rounded-xl border border-brand-500/40 shadow-xl"
                        style={{
                            left: `${(points[hoveredIndex].x / width) * 100}%`,
                            top: `${(points[hoveredIndex].y / height) * 100 - 8}%`,
                        }}
                    >
                        <div className="font-bold text-brand-300">{points[hoveredIndex].label}</div>
                        <div className="text-slate-300">
                            {points[hoveredIndex].val.toLocaleString('fa-IR')} کاربر
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
