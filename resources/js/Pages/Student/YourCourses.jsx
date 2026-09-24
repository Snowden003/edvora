import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import StudentLayout from '@/Layouts/StudentLayout';
import { useLanguage } from '@/Context/LanguageContext';
import {
    BookOpen,
    CheckCircle,
    Activity,
    Users,
    Search,
    Filter,
    PlayCircle,
    MessageSquare,
    Clock,
    X,
    Sparkles,
    GraduationCap,
    Lightbulb,
    AlertOctagon
} from 'lucide-react';

export default function YourCourses({
    enrollments = { data: [], links: [] },
    categories = [],
    avgProgress = 0,
    counts = { all: 0, inProgress: 0, completed: 0, banned: 0 },
    filters = {}
}) {
    const { t, isRtl } = useLanguage();
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [selectedCategory, setSelectedCategory] = useState(filters.category || '');
    const currentStatus = filters.status || '';

    // Handle search input debounce
    useEffect(() => {
        const timeout = setTimeout(() => {
            if (searchTerm !== (filters.search || '')) {
                applyFilter(searchTerm, selectedCategory, currentStatus);
            }
        }, 400);
        return () => clearTimeout(timeout);
    }, [searchTerm]);

    const handleCategoryChange = (e) => {
        const newCat = e.target.value;
        setSelectedCategory(newCat);
        applyFilter(searchTerm, newCat, currentStatus);
    };

    const handleStatusTab = (status) => {
        applyFilter(searchTerm, selectedCategory, status);
    };

    const applyFilter = (search, category, status) => {
        router.get(
            '/student/courses',
            { search, category, status: status || undefined },
            { preserveState: true, preserveScroll: true, replace: true }
        );
    };

    const clearFilters = () => {
        setSearchTerm('');
        setSelectedCategory('');
        router.get('/student/courses', {}, { preserveState: true, preserveScroll: true });
    };

    const enrollmentsData = enrollments?.data || [];
    const paginationLinks = enrollments?.links || [];

    return (
        <StudentLayout title={`${t('my_courses')} - ${t('brand_title')}`}>
            <Head>
                <title>{`${t('my_courses')} - ${t('brand_title')}`}</title>
                <meta
                    name="description"
                    content="Track your enrolled courses, continue online lessons, and review completion progress on Edvora Tech."
                />
            </Head>

            <div className="space-y-6 max-w-7xl mx-auto">
                {/* Courses Hub Banner */}
                <div className="relative rounded-3xl overflow-hidden shadow-2xl bg-gradient-to-br from-[#061E3E] via-slate-900 to-[#1F8FFF] text-white p-6 sm:p-12 border border-blue-900/50">
                    <div className="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
                        <div className="absolute top-8 left-12 text-white/10 animate-bounce" style={{ animationDuration: '7s' }}>
                            <BookOpen size={48} />
                        </div>
                        <div className="absolute bottom-6 left-1/2 text-white/5 animate-pulse" style={{ animationDuration: '5s' }}>
                            <Lightbulb size={40} />
                        </div>
                        <div className="absolute top-10 right-1/4 text-white/10 animate-bounce" style={{ animationDuration: '9s' }}>
                            <GraduationCap size={52} />
                        </div>
                    </div>

                    <div className="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div className="space-y-3 max-w-2xl text-center md:text-start">
                            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-semibold text-cyan-300">
                                <Sparkles size={13} className="text-cyan-400" />
                                <span>{t('portal_name')}</span>
                            </span>
                            <h1 className="text-2xl sm:text-4xl font-black text-white tracking-tight">
                                {t('your_courses_hero_title')}
                            </h1>
                            <p className="text-sm sm:text-base text-blue-100 leading-relaxed">
                                {t('your_courses_hero_desc')}
                            </p>
                        </div>

                        <div className="hidden lg:flex items-center justify-center shrink-0">
                            <GraduationCap size={110} className="text-white/15 transform rotate-6" />
                        </div>
                    </div>
                </div>

                {/* 4 Quick Stat Cards */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
                        <div className="w-12 h-12 rounded-2xl bg-brand-500/10 text-brand-600 flex items-center justify-center shrink-0">
                            <BookOpen size={24} />
                        </div>
                        <div>
                            <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block">{t('all_courses')}</span>
                            <h4 className="text-2xl font-extrabold text-slate-800 tracking-tight mt-0.5">
                                {counts.all}
                            </h4>
                        </div>
                    </div>

                    <div className="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
                        <div className="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                            <CheckCircle size={24} />
                        </div>
                        <div>
                            <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block">{t('stat_completed_courses')}</span>
                            <h4 className="text-2xl font-extrabold text-slate-800 tracking-tight mt-0.5">
                                {counts.completed}
                            </h4>
                        </div>
                    </div>

                    <div className="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
                        <div className="w-12 h-12 rounded-2xl bg-cyan-500/10 text-cyan-600 flex items-center justify-center shrink-0">
                            <PlayCircle size={24} />
                        </div>
                        <div>
                            <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block">{t('status_in_progress')}</span>
                            <h4 className="text-2xl font-extrabold text-slate-800 tracking-tight mt-0.5">
                                {counts.inProgress}
                            </h4>
                        </div>
                    </div>

                    <div className="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
                        <div className="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                            <Activity size={24} />
                        </div>
                        <div>
                            <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block">{t('avg_progress')}</span>
                            <h4 className="text-2xl font-extrabold text-slate-800 tracking-tight mt-0.5">
                                {avgProgress}%
                            </h4>
                        </div>
                    </div>
                </div>

                {/* Status Filter Tabs */}
                <div className="flex flex-wrap items-center gap-2">
                    <button
                        onClick={() => handleStatusTab('')}
                        type="button"
                        className={`px-4 py-2 rounded-2xl text-xs font-bold transition-all ${
                            !currentStatus
                                ? 'bg-brand-600 dark:bg-gradient-to-r dark:from-cyan-500 dark:to-brand-600 text-white dark:text-slate-950 shadow-md shadow-brand-500/20 dark:shadow-[0_0_15px_rgba(0,240,255,0.4)]'
                                : 'bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'
                        }`}
                    >
                        {t('filter_all')} ({counts.all})
                    </button>

                    <button
                        onClick={() => handleStatusTab('in_progress')}
                        type="button"
                        className={`px-4 py-2 rounded-2xl text-xs font-bold transition-all ${
                            currentStatus === 'in_progress'
                                ? 'bg-brand-600 dark:bg-gradient-to-r dark:from-cyan-500 dark:to-brand-600 text-white dark:text-slate-950 shadow-md shadow-brand-500/20 dark:shadow-[0_0_15px_rgba(0,240,255,0.4)]'
                                : 'bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'
                        }`}
                    >
                        {t('filter_in_progress')} ({counts.inProgress})
                    </button>

                    <button
                        onClick={() => handleStatusTab('completed')}
                        type="button"
                        className={`px-4 py-2 rounded-2xl text-xs font-bold transition-all ${
                            currentStatus === 'completed'
                                ? 'bg-emerald-600 dark:bg-emerald-500 text-white dark:text-slate-950 shadow-md shadow-emerald-500/20 dark:shadow-[0_0_15px_rgba(16,185,129,0.4)]'
                                : 'bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'
                        }`}
                    >
                        {t('filter_completed')} ({counts.completed})
                    </button>

                    {counts.banned > 0 && (
                        <button
                            onClick={() => handleStatusTab('banned')}
                            type="button"
                            className={`px-4 py-2 rounded-2xl text-xs font-bold transition-all ${
                                currentStatus === 'banned'
                                    ? 'bg-rose-600 dark:bg-rose-500 text-white dark:text-slate-950 shadow-md shadow-rose-500/20'
                                    : 'bg-white dark:bg-rose-950/30 border border-rose-200 dark:border-rose-500/30 text-rose-600 dark:text-rose-300 hover:bg-rose-50 dark:hover:bg-rose-900/40'
                            }`}
                        >
                            {t('filter_suspended')} ({counts.banned})
                        </button>
                    )}
                </div>

                {/* Control Bar: Search & Category Filter */}
                <div className="p-4 sm:p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                    <div className="relative w-full md:w-96">
                        <Search size={18} className={`absolute top-1/2 -translate-y-1/2 text-slate-400 ${isRtl ? 'right-4' : 'left-4'}`} />
                        <input
                            type="text"
                            placeholder={t('search_course_placeholder')}
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className={`w-full py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 transition-all ${
                                isRtl ? 'pr-11 pl-10' : 'pl-11 pr-10'
                            }`}
                        />
                        {searchTerm && (
                            <button
                                onClick={() => setSearchTerm('')}
                                className={`absolute top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 ${isRtl ? 'left-3' : 'right-3'}`}
                            >
                                <X size={14} />
                            </button>
                        )}
                    </div>

                    <div className="flex items-center gap-3 w-full md:w-auto justify-between md:justify-end">
                        <div className="relative w-full sm:w-56">
                            <select
                                value={selectedCategory}
                                onChange={handleCategoryChange}
                                className="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 transition-all appearance-none cursor-pointer"
                            >
                                <option value="">{t('all_categories')}</option>
                                {categories.map((cat) => (
                                    <option key={cat.id} value={cat.name}>
                                        {cat.name}
                                    </option>
                                ))}
                            </select>
                            <Filter size={14} className={`absolute top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none ${isRtl ? 'left-3' : 'right-3'}`} />
                        </div>

                        {(searchTerm || selectedCategory || currentStatus) && (
                            <button
                                onClick={clearFilters}
                                className="px-3.5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors whitespace-nowrap"
                            >
                                {t('reset_filters')}
                            </button>
                        )}
                    </div>
                </div>

                {/* Courses Grid */}
                {enrollmentsData.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        {enrollmentsData.map((item) => {
                            const course = item.course;
                            if (!course) return null;

                            const isBanned = item.status === 'banned';
                            const isCompleted = item.is_course_completed || item.status === 'completed' || item.actual_progress >= 100;
                            const progress = item.actual_progress !== undefined ? item.actual_progress : item.progress_percentage || 0;

                            const thumbnail = course.thumbnail_url || (course.thumbnail
                                ? (course.thumbnail.startsWith('http') ? course.thumbnail : `/storage/${course.thumbnail}`)
                                : 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=600&h=320&fit=crop');

                            return (
                                <div
                                    key={item.id}
                                    className={`group bg-white rounded-3xl overflow-hidden border shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between ${
                                        isBanned ? 'border-rose-200 opacity-90' : 'border-slate-200/80'
                                    }`}
                                >
                                    {/* Card Image & Overlays */}
                                    <div className="relative h-48 w-full overflow-hidden bg-slate-100">
                                        <img
                                            src={thumbnail}
                                            alt={course.title}
                                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        />
                                        {course.category && (
                                            <span className={`absolute top-3 px-3 py-1 rounded-full bg-slate-950/70 backdrop-blur-md text-white text-[11px] font-bold ${isRtl ? 'right-3' : 'left-3'}`}>
                                                {course.category.name}
                                            </span>
                                        )}
                                        <span
                                            className={`absolute top-3 px-3 py-1 rounded-full text-[11px] font-bold shadow-sm ${isRtl ? 'left-3' : 'right-3'} ${
                                                isBanned
                                                    ? 'bg-rose-600 text-white'
                                                    : isCompleted
                                                    ? 'bg-emerald-500 text-white'
                                                    : 'bg-brand-600 text-white'
                                            }`}
                                        >
                                            {isBanned ? t('filter_suspended') : isCompleted ? t('status_completed') : t('status_in_progress')}
                                        </span>
                                    </div>

                                    {/* Card Body */}
                                    <div className="p-6 flex-1 flex flex-col justify-between space-y-4">
                                        <div>
                                            <h3 className="font-extrabold text-slate-800 text-base leading-snug line-clamp-2 group-hover:text-brand-600 transition-colors">
                                                {course.title}
                                            </h3>

                                            {/* Progress Bar */}
                                            <div className="mt-3 space-y-1.5">
                                                <div className="flex items-center justify-between text-xs font-bold">
                                                    <span className="text-slate-400">{t('avg_progress')}</span>
                                                    <span className={isCompleted ? 'text-emerald-600' : 'text-brand-600'}>
                                                        {progress}%
                                                    </span>
                                                </div>
                                                <div className="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                                    <div
                                                        className={`h-2 rounded-full transition-all duration-700 ${
                                                            isCompleted ? 'bg-emerald-500' : 'bg-brand-500'
                                                        }`}
                                                        style={{ width: `${progress}%` }}
                                                    />
                                                </div>
                                                {item.total_lessons_count > 0 && (
                                                    <span className="text-[11px] text-slate-400 font-semibold block">
                                                        {item.completed_lessons_count || 0} / {item.total_lessons_count} {t('lessons_count')}
                                                    </span>
                                                )}
                                            </div>
                                        </div>

                                        {/* Footer Actions */}
                                        <div className="flex items-center justify-between gap-3 pt-3 border-t border-slate-100">
                                            {!isBanned ? (
                                                <>
                                                    <a
                                                        href={`/student/courses/${course.slug}/learn`}
                                                        className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-sm transition-all"
                                                    >
                                                        <PlayCircle size={14} />
                                                        <span>{isCompleted ? t('view_all') : t('learn')}</span>
                                                    </a>

                                                    <a
                                                        href={`/courses/${course.id}/chat`}
                                                        className="p-2 rounded-xl bg-slate-100 hover:bg-brand-50 text-slate-600 hover:text-brand-600 transition-colors"
                                                        title={t('chat')}
                                                    >
                                                        <MessageSquare size={16} />
                                                    </a>
                                                </>
                                            ) : (
                                                <span className="text-xs font-bold text-rose-600 flex items-center gap-1">
                                                    <AlertOctagon size={14} />
                                                    <span>{t('filter_suspended')}</span>
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                ) : (
                    /* Empty State */
                    <div className="p-16 rounded-3xl bg-white border border-slate-200/80 shadow-sm text-center space-y-4">
                        <div className="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <BookOpen size={32} />
                        </div>
                        <div className="space-y-1">
                            <h4 className="text-lg font-bold text-slate-800">{t('no_courses_filtered')}</h4>
                            <p className="text-xs text-slate-400 max-w-sm mx-auto">
                                {t('search_course_placeholder')}
                            </p>
                        </div>
                        {(searchTerm || selectedCategory || currentStatus) && (
                            <button
                                onClick={clearFilters}
                                className="px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-bold hover:bg-brand-500 transition-colors shadow-sm"
                            >
                                {t('reset_filters')}
                            </button>
                        )}
                    </div>
                )}

                {/* Pagination */}
                {paginationLinks && paginationLinks.length > 3 && (
                    <div className="flex items-center justify-center gap-1.5 pt-6">
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

                            return (
                                <Link
                                    key={idx}
                                    href={link.url}
                                    preserveScroll
                                    preserveState
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                    className={`px-3.5 py-2 rounded-xl text-xs font-bold transition-colors ${
                                        link.active
                                            ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20'
                                            : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-brand-600'
                                    }`}
                                />
                            );
                        })}
                    </div>
                )}
            </div>
        </StudentLayout>
    );
}
