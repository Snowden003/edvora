import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import TeacherLayout from '@/Layouts/TeacherLayout';
import {
    BookOpen,
    Users,
    Star,
    Activity,
    Search,
    Filter,
    Clock,
    BarChart2,
    Eye,
    GraduationCap,
    Lightbulb,
    X,
    ChevronLeft,
    ChevronRight,
    Sparkles,
    RotateCcw
} from 'lucide-react';

function formatPersianNumber(val) {
    if (val === null || val === undefined || isNaN(val)) return '۰';
    return Number(val).toLocaleString('fa-IR');
}

export default function YourCourses({
    courses = { data: [], links: [] },
    categories = [],
    filters = {},
    stats = {}
}) {
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [selectedCategory, setSelectedCategory] = useState(filters.category || '');

    // Handle search input debounce
    useEffect(() => {
        const timeout = setTimeout(() => {
            if (searchTerm !== (filters.search || '')) {
                applyFilter(searchTerm, selectedCategory);
            }
        }, 400);
        return () => clearTimeout(timeout);
    }, [searchTerm]);

    const handleCategoryChange = (e) => {
        const newCat = e.target.value;
        setSelectedCategory(newCat);
        applyFilter(searchTerm, newCat);
    };

    const applyFilter = (search, category) => {
        router.get(
            '/teacher/your-courses',
            { search, category },
            { preserveState: true, preserveScroll: true, replace: true }
        );
    };

    const clearFilters = () => {
        setSearchTerm('');
        setSelectedCategory('');
        router.get('/teacher/your-courses', {}, { preserveState: true, preserveScroll: true });
    };

    const coursesData = courses?.data || [];
    const paginationLinks = courses?.links || [];

    return (
        <TeacherLayout title="دوره‌های من - ادورا تک">
            <Head>
                <title>دوره‌های من - ادورا تک</title>
                <meta
                    name="description"
                    content="مدیریت دوره‌های آموزشی، بررسی دانشجویان و سازماندهی برنامه‌های تدریس در ادورا تک."
                />
            </Head>

            {/* Courses Hub Banner */}
            <div className="relative rounded-[24px] overflow-hidden shadow-sm bg-gradient-to-br from-[#0A58CA] via-[#0947a5] to-[#1683F7] text-white p-6 sm:p-10 border border-[#E5EAF2]" dir="rtl">
                <div className="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div className="space-y-3 max-w-2xl text-right">
                        <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 border border-white/20 text-xs font-semibold text-white">
                            <Sparkles size={13} />
                            <span>مدیریت محتوای آموزشی</span>
                        </span>
                        <h1 className="text-2xl sm:text-3xl font-black text-white tracking-tight">
                            مرکز دوره‌های آموزشی من
                        </h1>
                        <p className="text-xs sm:text-sm text-blue-100 leading-relaxed">
                            مدیریت و گسترش تأثیر آموزشی شما در جامعه. هم‌اکنون شما راهنمایی و تدریس{' '}
                            <strong className="text-white font-bold">{formatPersianNumber(stats.totalStudents || 0)} دانشجو</strong>{' '}
                            را در دوره‌های تخصصی خود بر عهده دارید.
                        </p>
                    </div>

                    <div className="hidden lg:flex items-center justify-center shrink-0 pl-6">
                        <GraduationCap size={100} className="text-white/20 transform -rotate-6" />
                    </div>
                </div>
            </div>

            {/* 4 Quick Stat Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5" dir="rtl">
                {/* Total Courses */}
                <div className="p-6 rounded-[20px] bg-white border border-[#E5EAF2] shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
                    <div className="w-12 h-12 rounded-2xl bg-[#0A58CA]/10 text-[#0A58CA] flex items-center justify-center shrink-0">
                        <BookOpen size={24} />
                    </div>
                    <div>
                        <span className="text-xs font-bold text-slate-400 block">کل دوره‌ها</span>
                        <h4 className="text-2xl font-extrabold text-[#111827] tracking-tight mt-0.5 font-vazir">
                            {formatPersianNumber(stats.totalCourses || 0)}
                        </h4>
                    </div>
                </div>

                {/* Active Students */}
                <div className="p-6 rounded-[20px] bg-white border border-[#E5EAF2] shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
                    <div className="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                        <Users size={24} />
                    </div>
                    <div>
                        <span className="text-xs font-bold text-slate-400 block">دانشجویان فعال</span>
                        <h4 className="text-2xl font-extrabold text-[#111827] tracking-tight mt-0.5 font-vazir">
                            {formatPersianNumber(stats.totalStudents || 0)}
                        </h4>
                    </div>
                </div>

                {/* Avg Rating */}
                <div className="p-6 rounded-[20px] bg-white border border-[#E5EAF2] shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
                    <div className="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                        <Star size={24} />
                    </div>
                    <div>
                        <span className="text-xs font-bold text-slate-400 block">میانگین امتیاز</span>
                        <h4 className="text-2xl font-extrabold text-[#111827] tracking-tight mt-0.5 font-vazir">
                            {formatPersianNumber(Number(stats.avgRating || 0).toFixed(1))} ★
                        </h4>
                    </div>
                </div>

                {/* Active Courses */}
                <div className="p-6 rounded-[20px] bg-white border border-[#E5EAF2] shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
                    <div className="w-12 h-12 rounded-2xl bg-[#1683F7]/10 text-[#1683F7] flex items-center justify-center shrink-0">
                        <Activity size={24} />
                    </div>
                    <div>
                        <span className="text-xs font-bold text-slate-400 block">دوره‌های فعال</span>
                        <h4 className="text-2xl font-extrabold text-[#111827] tracking-tight mt-0.5 font-vazir">
                            {formatPersianNumber(stats.activeCourses || 0)}
                        </h4>
                    </div>
                </div>
            </div>

            {/* Control Bar: Search & Category Filter */}
            <div className="p-4 sm:p-5 rounded-[20px] bg-white border border-[#E5EAF2] shadow-sm flex flex-col md:flex-row items-center justify-between gap-4" dir="rtl">
                {/* Search Input */}
                <div className="relative w-full md:w-96">
                    <Search size={18} className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                    <input
                        type="text"
                        placeholder="جستجوی دوره بر اساس عنوان..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        className="w-full pr-10 pl-10 py-2.5 rounded-xl bg-[#F5F8FC] border border-[#E5EAF2] text-xs sm:text-sm text-[#111827] placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 focus:border-[#0A58CA] transition-all"
                    />
                    {searchTerm && (
                        <button
                            onClick={() => setSearchTerm('')}
                            className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1"
                        >
                            <X size={14} />
                        </button>
                    )}
                </div>

                {/* Filters and Counters */}
                <div className="flex items-center gap-3 w-full md:w-auto justify-between md:justify-end">
                    <div className="relative w-full sm:w-56">
                        <select
                            value={selectedCategory}
                            onChange={handleCategoryChange}
                            className="w-full px-4 py-2.5 rounded-xl bg-[#F5F8FC] border border-[#E5EAF2] text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 focus:border-[#0A58CA] transition-all cursor-pointer"
                        >
                            <option value="">همه دسته‌بندی‌ها</option>
                            {categories.map((cat) => (
                                <option key={cat.id} value={cat.name}>
                                    {cat.name}
                                </option>
                            ))}
                        </select>
                    </div>

                    {(searchTerm || selectedCategory) && (
                        <button
                            onClick={clearFilters}
                            className="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors whitespace-nowrap"
                        >
                            <RotateCcw size={14} />
                            <span>پاک کردن</span>
                        </button>
                    )}
                </div>
            </div>

            {/* Courses Grid */}
            {coursesData.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" dir="rtl">
                    {coursesData.map((course) => {
                        const thumbnail = course.thumbnail_url || (course.thumbnail
                            ? (course.thumbnail.startsWith('http') ? course.thumbnail : `/storage/${course.thumbnail}`)
                            : 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=600&h=320&fit=crop');

                        return (
                            <div
                                key={course.id}
                                className="group bg-white rounded-[22px] overflow-hidden border border-[#E5EAF2] shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between"
                            >
                                {/* Card Image & Overlays */}
                                <div className="relative h-48 w-full overflow-hidden bg-slate-100">
                                    <img
                                        src={thumbnail}
                                        alt={course.title}
                                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        onError={(e) => {
                                            e.target.src = 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=600&h=320&fit=crop';
                                        }}
                                    />
                                    {/* Category Pill */}
                                    <span className="absolute top-3 right-3 px-3 py-1 rounded-full bg-slate-950/70 backdrop-blur-xs text-white text-[11px] font-bold">
                                        {course.category?.name || 'عمومی'}
                                    </span>
                                    {/* Status Badge */}
                                    <span
                                        className={`absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold shadow-sm ${
                                            course.status === 'active' || course.status === 'published'
                                                ? 'bg-emerald-500 text-white'
                                                : course.status === 'draft'
                                                ? 'bg-amber-400 text-slate-900'
                                                : 'bg-slate-700 text-white'
                                        }`}
                                    >
                                        {course.status === 'active' || course.status === 'published'
                                            ? 'فعال'
                                            : course.status === 'draft'
                                            ? 'پیش‌نویس'
                                            : 'تکمیل‌شده'}
                                    </span>
                                </div>

                                {/* Card Body */}
                                <div className="p-6 flex-1 flex flex-col justify-between space-y-4">
                                    <div>
                                        <h3 className="font-extrabold text-[#111827] text-base leading-snug line-clamp-2 group-hover:text-[#0A58CA] transition-colors">
                                            {course.title}
                                        </h3>

                                        <div className="flex items-center gap-2 mt-2.5 text-xs text-slate-500 font-medium">
                                            <Users size={15} className="text-[#0A58CA] shrink-0" />
                                            <span>{formatPersianNumber(course.enrolled_count || 0)} دانشجو ثبت‌نام‌شده</span>
                                        </div>
                                    </div>

                                    {/* Rating */}
                                    <div className="flex items-center gap-2 pt-2 border-t border-[#E5EAF2]">
                                        <div className="flex items-center gap-0.5 text-amber-400">
                                            {[1, 2, 3, 4, 5].map((star) => (
                                                <Star
                                                    key={star}
                                                    size={14}
                                                    fill={star <= Math.round(course.rating || 0) ? 'currentColor' : 'none'}
                                                    className={star <= Math.round(course.rating || 0) ? 'text-amber-400' : 'text-slate-200'}
                                                />
                                            ))}
                                        </div>
                                        <span className="text-xs font-bold text-slate-700 font-vazir">
                                            {formatPersianNumber(Number(course.rating || 0).toFixed(1))}
                                        </span>
                                    </div>

                                    {/* Card Footer Actions */}
                                    <div className="flex items-center justify-between gap-3 pt-3 border-t border-[#E5EAF2]">
                                        <a
                                            href={`/teacher/courses/${course.id}`}
                                            className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#0A58CA]/10 hover:bg-[#0A58CA] text-[#0A58CA] hover:text-white font-bold text-xs transition-colors"
                                        >
                                            <Eye size={14} />
                                            <span>مشاهده جزئیات دوره</span>
                                        </a>

                                        <div className="flex items-center gap-2 text-[11px] text-slate-400 font-semibold">
                                            {course.duration_hours && (
                                                <span className="flex items-center gap-1">
                                                    <Clock size={12} />
                                                    {formatPersianNumber(course.duration_hours)} ساعت
                                                </span>
                                            )}
                                            {course.level && (
                                                <span className="flex items-center gap-1">
                                                    <BarChart2 size={12} />
                                                    {course.level}
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            ) : (
                /* Empty State */
                <div className="p-16 rounded-[24px] bg-white border border-[#E5EAF2] shadow-sm text-center space-y-4" dir="rtl">
                    <div className="w-16 h-16 rounded-3xl bg-[#0A58CA]/10 text-[#0A58CA] flex items-center justify-center mx-auto">
                        <BookOpen size={32} />
                    </div>
                    <div className="space-y-1">
                        <h4 className="text-lg font-bold text-[#111827]">هیچ دوره‌ای مطابق فیلتر یافت نشد</h4>
                        <p className="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                            لطفاً فیلترهای جستجو یا دسته‌بندی را تغییر دهید تا تمام دوره‌های آموزشی نمایش داده شوند.
                        </p>
                    </div>
                    {(searchTerm || selectedCategory) && (
                        <button
                            onClick={clearFilters}
                            className="px-4 py-2 rounded-xl bg-[#0A58CA] text-white text-xs font-bold hover:bg-[#1683F7] transition-colors shadow-xs"
                        >
                            پاک کردن فیلترها
                        </button>
                    )}
                </div>
            )}

            {/* Pagination */}
            {paginationLinks && paginationLinks.length > 3 && (
                <div className="flex items-center justify-center gap-1.5 pt-6" dir="rtl">
                    {paginationLinks.map((link, idx) => {
                        if (!link.url) {
                            return (
                                <span
                                    key={idx}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                    className="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-300 cursor-not-allowed select-none"
                                />
                            );
                        }

                        const isPrevious = link.label.includes('Previous') || link.label.includes('&laquo;');
                        const isNext = link.label.includes('Next') || link.label.includes('&raquo;');

                        return (
                            <Link
                                key={idx}
                                href={link.url}
                                preserveScroll
                                preserveState
                                className={`px-3.5 py-2 rounded-xl text-xs font-bold transition-colors ${
                                    link.active
                                        ? 'bg-[#0A58CA] text-white shadow-xs'
                                        : 'bg-white border border-[#E5EAF2] text-slate-600 hover:bg-slate-50 hover:text-[#0A58CA]'
                                }`}
                            >
                                {isPrevious ? 'قبلی' : isNext ? 'بعدی' : link.label}
                            </Link>
                        );
                    })}
                </div>
            )}
        </TeacherLayout>
    );
}
