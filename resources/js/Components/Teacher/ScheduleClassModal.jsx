import React, { useState } from 'react';
import { X, Calendar, Clock, Video, BookOpen, CheckCircle } from 'lucide-react';

export default function ScheduleClassModal({
    isOpen,
    onClose,
    courses = [],
    onScheduled
}) {
    const safeCourses = Array.isArray(courses) ? courses : Object.values(courses || {});
    const [title, setTitle] = useState('');
    const [courseId, setCourseId] = useState(safeCourses[0]?.id || '');
    const [date, setDate] = useState(new Date().toISOString().split('T')[0]);
    const [time, setTime] = useState('10:00');
    const [duration, setDuration] = useState('60');
    const [description, setDescription] = useState('');
    const [submitted, setSubmitted] = useState(false);

    if (!isOpen) return null;

    const handleSubmit = (e) => {
        e.preventDefault();
        setSubmitted(true);
        setTimeout(() => {
            if (onScheduled) {
                onScheduled({
                    title,
                    courseId,
                    date,
                    time,
                    duration,
                    description,
                });
            }
            setSubmitted(false);
            onClose();
        }, 1200);
    };

    return (
        <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-950/80 backdrop-blur-md animate-in fade-in duration-200" dir="rtl">
            <div className="bg-white dark:bg-[#071328] rounded-t-[28px] sm:rounded-[24px] shadow-2xl dark:shadow-[0_25px_60px_-15px_rgba(0,0,0,0.85),0_0_35px_rgba(0,240,255,0.15)] border border-[#E5EAF2] dark:border-cyan-500/30 max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden animate-in slide-in-from-bottom-5 sm:zoom-in-95 duration-200">
                {/* Header */}
                <div className="p-4 sm:p-6 border-b border-[#E5EAF2] dark:border-white/10 flex items-center justify-between bg-slate-50/70 dark:bg-[#030917]/50 shrink-0">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-2xl bg-[#0A58CA]/10 dark:bg-cyan-500/20 text-[#0A58CA] dark:text-cyan-400 dark:border dark:border-cyan-500/30 dark:shadow-[0_0_12px_rgba(0,240,255,0.3)] flex items-center justify-center shrink-0">
                            <Video size={20} className="drop-shadow-[0_0_6px_#00f0ff]" />
                        </div>
                        <div>
                            <h3 className="font-extrabold text-[#111827] dark:text-white text-base sm:text-lg">برنامه‌ریزی صنف زنده</h3>
                            <p className="text-[11px] sm:text-xs text-slate-500 dark:text-cyan-300/80">تنظیم و زمان‌بندی جلسه آموزشی آنلاین</p>
                        </div>
                    </div>
                    <button
                        onClick={onClose}
                        className="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-[#0c1a36] transition-colors"
                        aria-label="بستن"
                    >
                        <X size={20} />
                    </button>
                </div>

                {/* Content */}
                <div className="overflow-y-auto p-4 sm:p-6 flex-1">
                    {submitted ? (
                        <div className="py-8 text-center space-y-3">
                            <div className="w-14 h-14 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto animate-bounce">
                                <CheckCircle size={32} />
                            </div>
                            <h4 className="text-base sm:text-lg font-bold text-[#111827] dark:text-white">جلسه صنف با موفقیت ثبت شد!</h4>
                            <p className="text-xs text-slate-500 dark:text-slate-400">جزئیات جلسه ذخیره گردید و به تقویم آموزشی اضافه شد.</p>
                        </div>
                    ) : (
                        <form onSubmit={handleSubmit} className="space-y-4">
                            {/* Class Title */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    عنوان جلسه صنف
                                </label>
                                <input
                                    type="text"
                                    required
                                    placeholder="مثال: کارگاه عملی کدنویسی - هفته چهارم"
                                    value={title}
                                    onChange={(e) => setTitle(e.target.value)}
                                    className="w-full px-3.5 py-2.5 rounded-xl border border-[#E5EAF2] dark:border-white/10 bg-[#F5F8FC] dark:bg-[#030917]/90 text-xs sm:text-sm text-[#111827] dark:text-white focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 dark:focus:ring-cyan-500/30 focus:border-[#0A58CA] dark:focus:border-cyan-400 transition-all"
                                />
                            </div>

                            {/* Course Select */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    دوره آموزشی مربوطه
                                </label>
                                <div className="relative">
                                    <select
                                        required
                                        value={courseId}
                                        onChange={(e) => setCourseId(e.target.value)}
                                        className="w-full px-3.5 py-2.5 rounded-xl border border-[#E5EAF2] dark:border-white/10 bg-[#F5F8FC] dark:bg-[#030917]/90 text-xs sm:text-sm text-[#111827] dark:text-white focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 dark:focus:ring-cyan-500/30 focus:border-[#0A58CA] dark:focus:border-cyan-400 transition-all cursor-pointer"
                                    >
                                        {safeCourses.length > 0 ? (
                                            safeCourses.map((c) => (
                                                <option key={c.id} value={c.id} className="dark:bg-[#071328]">
                                                    {c.title}
                                                </option>
                                            ))
                                        ) : (
                                            <option value="" className="dark:bg-[#071328]">دوره فعالی یافت نشد</option>
                                        )}
                                    </select>
                                </div>
                            </div>

                            {/* Date & Time Grid */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        تاریخ جلسه
                                    </label>
                                    <input
                                        type="date"
                                        required
                                        value={date}
                                        onChange={(e) => setDate(e.target.value)}
                                        className="w-full px-3.5 py-2.5 rounded-xl border border-[#E5EAF2] dark:border-white/10 bg-[#F5F8FC] dark:bg-[#030917]/90 text-xs sm:text-sm text-[#111827] dark:text-white focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 dark:focus:ring-cyan-500/30 focus:border-[#0A58CA] dark:focus:border-cyan-400 transition-all"
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        ساعت شروع
                                    </label>
                                    <input
                                        type="time"
                                        required
                                        value={time}
                                        onChange={(e) => setTime(e.target.value)}
                                        className="w-full px-3.5 py-2.5 rounded-xl border border-[#E5EAF2] dark:border-white/10 bg-[#F5F8FC] dark:bg-[#030917]/90 text-xs sm:text-sm text-[#111827] dark:text-white focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 dark:focus:ring-cyan-500/30 focus:border-[#0A58CA] dark:focus:border-cyan-400 transition-all"
                                    />
                                </div>
                            </div>

                            {/* Duration */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    مدت زمان جلسه
                                </label>
                                <select
                                    value={duration}
                                    onChange={(e) => setDuration(e.target.value)}
                                    className="w-full px-3.5 py-2.5 rounded-xl border border-[#E5EAF2] dark:border-white/10 bg-[#F5F8FC] dark:bg-[#030917]/90 text-xs sm:text-sm text-[#111827] dark:text-white focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 dark:focus:ring-cyan-500/30 focus:border-[#0A58CA] dark:focus:border-cyan-400 transition-all cursor-pointer"
                                >
                                    <option value="30" className="dark:bg-[#071328]">۳۰ دقیقه</option>
                                    <option value="60" className="dark:bg-[#071328]">۱ ساعت (۶۰ دقیقه)</option>
                                    <option value="90" className="dark:bg-[#071328]">۱.۵ ساعت (۹۰ دقیقه)</option>
                                    <option value="120" className="dark:bg-[#071328]">۲ ساعت (۱۲۰ دقیقه)</option>
                                </select>
                            </div>

                            {/* Description */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    مباحث و یادداشت‌ها (اختیاری)
                                </label>
                                <textarea
                                    rows={2}
                                    placeholder="سرفصل‌های مورد بحث، پیش‌نیازهای جلسه..."
                                    value={description}
                                    onChange={(e) => setDescription(e.target.value)}
                                    className="w-full px-3.5 py-2.5 rounded-xl border border-[#E5EAF2] dark:border-white/10 bg-[#F5F8FC] dark:bg-[#030917]/90 text-xs sm:text-sm text-[#111827] dark:text-white focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 dark:focus:ring-cyan-500/30 focus:border-[#0A58CA] dark:focus:border-cyan-400 transition-all resize-none"
                                />
                            </div>

                            {/* Actions */}
                            <div className="pt-2 flex items-center justify-end gap-2.5">
                                <button
                                    type="button"
                                    onClick={onClose}
                                    className="px-4 py-2.5 rounded-xl border border-[#E5EAF2] dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#0c1a36] font-bold text-xs transition-colors"
                                >
                                    انصراف
                                </button>
                                <button
                                    type="submit"
                                    className="px-5 py-2.5 rounded-xl bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 hover:bg-[#1683F7] dark:hover:from-cyan-400 dark:hover:to-blue-500 text-white font-bold text-xs shadow-xs dark:shadow-[0_0_15px_rgba(0,240,255,0.25)] active:scale-95 transition-all"
                                >
                                    ثبت و زمان‌بندی جلسه
                                </button>
                            </div>
                        </form>
                    )}
                </div>
            </div>
        </div>
    );
}
