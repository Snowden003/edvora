import React, { useState } from 'react';
import { X, Calendar, Clock, Video, BookOpen, CheckCircle } from 'lucide-react';

export default function ScheduleClassModal({
    isOpen,
    onClose,
    courses = [],
    onScheduled
}) {
    const [title, setTitle] = useState('');
    const [courseId, setCourseId] = useState(courses[0]?.id || '');
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
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in duration-200" dir="rtl">
            <div className="bg-white rounded-[24px] shadow-2xl border border-[#E5EAF2] max-w-lg w-full overflow-hidden animate-in zoom-in-95 duration-200">
                {/* Header */}
                <div className="p-6 border-b border-[#E5EAF2] flex items-center justify-between bg-slate-50/60">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-2xl bg-[#0A58CA]/10 text-[#0A58CA] flex items-center justify-center">
                            <Video size={20} />
                        </div>
                        <div>
                            <h3 className="font-extrabold text-[#111827] text-lg">برنامه‌ریزی صنف زنده</h3>
                            <p className="text-xs text-slate-400">تنظیم و زمان‌بندی جلسه آموزشی آنلاین برای دانشجویان</p>
                        </div>
                    </div>
                    <button
                        onClick={onClose}
                        className="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                        aria-label="بستن"
                    >
                        <X size={20} />
                    </button>
                </div>

                {/* Form or Success State */}
                {submitted ? (
                    <div className="p-8 text-center space-y-3">
                        <div className="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto animate-bounce">
                            <CheckCircle size={32} />
                        </div>
                        <h4 className="text-lg font-bold text-[#111827]">جلسه صنف با موفقیت ثبت شد!</h4>
                        <p className="text-xs text-slate-500">جزئیات جلسه ذخیره گردید و به تقویم آموزشی اضافه شد.</p>
                    </div>
                ) : (
                    <form onSubmit={handleSubmit} className="p-6 space-y-4">
                        {/* Class Title */}
                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                عنوان جلسه صنف
                            </label>
                            <input
                                type="text"
                                required
                                placeholder="مثال: کارگاه عملی کدنویسی - هفته چهارم"
                                value={title}
                                onChange={(e) => setTitle(e.target.value)}
                                className="w-full px-4 py-2.5 rounded-xl border border-[#E5EAF2] bg-[#F5F8FC] text-sm focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 focus:border-[#0A58CA] transition-all"
                            />
                        </div>

                        {/* Course Select */}
                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                دوره آموزشی مربوطه
                            </label>
                            <div className="relative">
                                <select
                                    required
                                    value={courseId}
                                    onChange={(e) => setCourseId(e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl border border-[#E5EAF2] bg-[#F5F8FC] text-sm focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 focus:border-[#0A58CA] transition-all cursor-pointer"
                                >
                                    {courses.map((c) => (
                                        <option key={c.id} value={c.id}>
                                            {c.title}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {/* Date and Time Row */}
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                    تاریخ جلسه
                                </label>
                                <input
                                    type="date"
                                    required
                                    value={date}
                                    onChange={(e) => setDate(e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl border border-[#E5EAF2] bg-[#F5F8FC] text-sm focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 focus:border-[#0A58CA] transition-all"
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                    ساعت شروع
                                </label>
                                <input
                                    type="time"
                                    required
                                    value={time}
                                    onChange={(e) => setTime(e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl border border-[#E5EAF2] bg-[#F5F8FC] text-sm focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 focus:border-[#0A58CA] transition-all"
                                />
                            </div>
                        </div>

                        {/* Duration */}
                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                مدت زمان جلسه
                            </label>
                            <select
                                value={duration}
                                onChange={(e) => setDuration(e.target.value)}
                                className="w-full px-4 py-2.5 rounded-xl border border-[#E5EAF2] bg-[#F5F8FC] text-sm focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 focus:border-[#0A58CA] transition-all cursor-pointer"
                            >
                                <option value="30">۳۰ دقیقه</option>
                                <option value="60">۱ ساعت (۶۰ دقیقه)</option>
                                <option value="90">۱.۵ ساعت (۹۰ دقیقه)</option>
                                <option value="120">۲ ساعت (۱۲۰ دقیقه)</option>
                            </select>
                        </div>

                        {/* Description */}
                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                مباحث و یادداشت‌ها (اختیاری)
                            </label>
                            <textarea
                                rows={3}
                                placeholder="سرفصل‌های مورد بحث، پیش‌نیازهای جلسه..."
                                value={description}
                                onChange={(e) => setDescription(e.target.value)}
                                className="w-full px-4 py-2.5 rounded-xl border border-[#E5EAF2] bg-[#F5F8FC] text-sm focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 focus:border-[#0A58CA] transition-all resize-none"
                            />
                        </div>

                        {/* Buttons */}
                        <div className="pt-2 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                onClick={onClose}
                                className="px-5 py-2.5 rounded-xl border border-[#E5EAF2] text-slate-600 hover:bg-slate-100 font-bold text-xs transition-colors"
                            >
                                انصراف
                            </button>
                            <button
                                type="submit"
                                className="px-5 py-2.5 rounded-xl bg-[#0A58CA] hover:bg-[#1683F7] text-white font-bold text-xs shadow-xs active:scale-95 transition-all"
                            >
                                ثبت و زمان‌بندی جلسه
                            </button>
                        </div>
                    </form>
                )}
            </div>
        </div>
    );
}
