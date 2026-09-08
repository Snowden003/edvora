import React, { useState } from 'react';
import { Radio, Calendar } from 'lucide-react';

export default function SessionsLineChart({ chartData }) {
    const [hoveredIndex, setHoveredIndex] = useState(null);

    const labels = chartData?.labels || [];
    const data = chartData?.data || [];

    const maxValue = Math.max(...data, 4);
    const totalSessions = data.reduce((acc, val) => acc + val, 0);

    const height = 180;
    const width = 600;
    const padding = { top: 15, right: 15, bottom: 35, left: 35 };

    const chartWidth = width - padding.left - padding.right;
    const chartHeight = height - padding.top - padding.bottom;

    const points = data.map((val, idx) => {
        const x = padding.left + (idx / (data.length - 1 || 1)) * chartWidth;
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
            <div className="flex items-center justify-between pb-4 border-b border-slate-800/80">
                <div className="flex items-center gap-3">
                    <div className="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20">
                        <Radio size={20} className="animate-pulse" />
                    </div>
                    <div>
                        <h3 className="text-base font-bold text-white">فعالیت جلسات آنلاین و زنده (۳۰ روز اخیر)</h3>
                        <p className="text-xs text-slate-400">
                            مجموع جلسات تشکیل شده: <span className="text-purple-400 font-bold">{totalSessions.toLocaleString('fa-IR')}</span> جلسه
                        </p>
                    </div>
                </div>
            </div>

            {/* SVG Chart Area */}
            <div className="relative mt-4 w-full h-[200px] flex items-center justify-center">
                <svg
                    viewBox={`0 0 ${width} ${height}`}
                    className="w-full h-full overflow-visible"
                    preserveAspectRatio="none"
                >
                    <defs>
                        <linearGradient id="sessionGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stopColor="#a855f7" stopOpacity="0.4" />
                            <stop offset="100%" stopColor="#a855f7" stopOpacity="0.0" />
                        </linearGradient>
                    </defs>

                    {/* Area fill */}
                    {areaD && <path d={areaD} fill="url(#sessionGradient)" />}

                    {/* Line stroke */}
                    {pathD && (
                        <path
                            d={pathD}
                            fill="none"
                            stroke="#a855f7"
                            strokeWidth="2.5"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                        />
                    )}

                    {/* Points every 3 items for cleaner UI */}
                    {points.map((p, idx) => (
                        <g key={idx}>
                            {idx % 4 === 0 && (
                                <circle
                                    cx={p.x}
                                    cy={p.y}
                                    r={3}
                                    fill="#a855f7"
                                    stroke="#0f172a"
                                    strokeWidth="2"
                                />
                            )}
                            <rect
                                x={p.x - 8}
                                y={padding.top}
                                width={16}
                                height={chartHeight}
                                fill="transparent"
                                onMouseEnter={() => setHoveredIndex(idx)}
                                onMouseLeave={() => setHoveredIndex(null)}
                            />
                            {idx % 5 === 0 && (
                                <text
                                    x={p.x}
                                    y={height - 8}
                                    fill="#64748b"
                                    fontSize="9"
                                    textAnchor="middle"
                                >
                                    {p.label}
                                </text>
                            )}
                        </g>
                    ))}
                </svg>

                {/* Tooltip Overlay */}
                {hoveredIndex !== null && points[hoveredIndex] && (
                    <div
                        className="absolute pointer-events-none transform -translate-x-1/2 -translate-y-full bg-slate-950 text-white text-xs px-3 py-1.5 rounded-xl border border-purple-500/40 shadow-xl"
                        style={{
                            left: `${(points[hoveredIndex].x / width) * 100}%`,
                            top: `${(points[hoveredIndex].y / height) * 100 - 8}%`,
                        }}
                    >
                        <div className="font-bold text-purple-300">{points[hoveredIndex].label}</div>
                        <div className="text-slate-300">
                            {points[hoveredIndex].val.toLocaleString('fa-IR')} جلسه آنلاین
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
