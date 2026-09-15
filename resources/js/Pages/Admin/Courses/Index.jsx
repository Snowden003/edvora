import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import CourseStudentsModal from '@/Components/Admin/CourseStudentsModal';
import {
    GraduationCap,
    PlusCircle,
    Search,
    Filter,
    CheckCircle2,
    PlayCircle,
    Archive,
    Trash2,
    Star,
    ExternalLink,
    Edit3,
    Eye,
    ChevronLeft,
    ChevronRight,
    Users,
    Clock,
    Layers,
    LayoutGrid,
    List,
    AlertTriangle,
    X,
    Sparkles,
    MoreVertical
} from 'lucide-react';

export default function Index({ courses, summary = {}, categories = [], teachers = [], filters = {} }) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || '');
    const [level, setLevel] = useState(filters.level || '');
    const [categoryId, setCategoryId] = useState(filters.category_id || '');
    const [isFeatured, setIsFeatured] = useState(filters.is_featured || '');
    const [viewMode, setViewMode] = useState('table'); // 'table' | 'grid'

    // Viewing students of a specific course
    const [viewingStudentsCourse, setViewingStudentsCourse] = useState(null);

    // Selected courses for bulk actions
    const [selectedIds, setSelectedIds] = useState([]);
    const [deleteModalCourse, setDeleteModalCourse] = useState(null);
    const [bulkDeleteModalOpen, setBulkDeleteModalOpen] = useState(false);

    // Filter apply
    const applyFilters = (overrides = {}) => {
        const query = {
            search,
            status,
            level,
            category_id: categoryId,
            is_featured: isFeatured,
            ...overrides,
        };
        // Remove empty keys
        Object.keys(query).forEach((key) => {
            if (query[key] === '' || query[key] === null || query[key] === undefined) {
                delete query[key];
            }
        });

        router.get('/admin/courses', query, { preserveState: true, preserveScroll: true });
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        applyFilters({ search });
    };

    const handleResetFilters = () => {
        setSearch('');
        setStatus('');
        setLevel('');
        setCategoryId('');
        setIsFeatured('');
        router.get('/admin/courses', {}, { preserveState: true, preserveScroll: true });
    };

    // Bulk selection helpers
    const allIds = courses.data.map((c) => c.id);
    const isAllSelected = allIds.length > 0 && allIds.every((id) => selectedIds.includes(id));

    const toggleSelectAll = () => {
        if (isAllSelected) {
            setSelectedIds([]);
        } else {
            setSelectedIds(allIds);
        }
    };

    const toggleSelectOne = (id) => {
        if (selectedIds.includes(id)) {
            setSelectedIds(selectedIds.filter((item) => item !== id));
        } else {
            setSelectedIds([...selectedIds, id]);
        }
    };

    const handleBulkAction = (action) => {
        if (action === 'delete') {
            setBulkDeleteModalOpen(true);
            return;
        }

        router.post(
            '/admin/courses/bulk-action',
            { action, ids: selectedIds },
            {
                preserveScroll: true,
                onSuccess: () => setSelectedIds([]),
            }
        );
    };

    const confirmBulkDelete = () => {
        router.post(
            '/admin/courses/bulk-action',
            { action: 'delete', ids: selectedIds },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setSelectedIds([]);
                    setBulkDeleteModalOpen(false);
                },
            }
        );
    };

    const handleToggleFeatured = (courseId) => {
        router.post(`/admin/courses/${courseId}/toggle-featured`, {}, { preserveScroll: true });
    };

    const handleDeleteCourse = (course) => {
        setDeleteModalCourse(course);
    };

    const confirmSingleDelete = () => {
        if (!deleteModalCourse) return;
        router.delete(`/admin/courses/${deleteModalCourse.id}`, {
            preserveScroll: true,
            onSuccess: () => setDeleteModalCourse(null),
        });
    };

    // Status helpers
    const statusBadges = {
        draft: { label: 'پیش‌نویس', class: 'bg-slate-500/20 text-slate-300 border-slate-500/30' },
        published: { label: 'منتشر شده', class: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' },
        started: { label: 'در حال برگزاری', class: 'bg-brand-500/20 text-brand-300 border-brand-500/30' },
        archived: { label: 'آرشیو شده', class: 'bg-rose-500/20 text-rose-300 border-rose-500/30' },
    };

    const levelLabels = {
        beginner: { label: 'مبتدی', color: 'text-emerald-400' },
        intermediate: { label: 'متوسط', color: 'text-amber-400' },
        advanced: { label: 'پیشرفته', color: 'text-rose-400' },
    };

    return (
        <AdminLayout title="مدیریت دوره‌ها">
            {/* Header with Title & Action */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div className="flex items-center gap-2">
                        <GraduationCap className="text-brand-400" size={24} />
                        <h1 className="text-2xl sm:text-3xl font-black text-white tracking-tight font-display">
                            مدیریت دوره‌های آموزشی
                        </h1>
                    </div>
                    <p className="text-xs sm:text-sm text-slate-400 mt-1">
                        تعریف، ویرایش، زمان‌بندی جلسات و نظارت بر تمامی دوره‌های پلتفرم ادورا
                    </p>
                </div>

                <Link
                    href="/admin/courses/create"
                    className="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-tr from-brand-600 via-brand-500 to-accent-500 hover:from-brand-500 hover:to-accent-400 text-white font-bold text-sm shadow-glow hover:shadow-lg transition-all active:scale-95 shrink-0"
                >
                    <PlusCircle size={18} />
                    <span>افزودن دوره جدید</span>
                </Link>
            </div>

            {/* KPI Summary Cards */}
            <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div className="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl">
                    <span className="text-xs text-slate-400 font-medium">مجموع دوره‌ها</span>
                    <div className="text-2xl font-black text-white mt-1 font-display">
                        {(summary.total || 0).toLocaleString('fa-IR')}
                    </div>
                </div>

                <div className="p-4 rounded-2xl bg-slate-900/60 border border-emerald-500/20 backdrop-blur-xl">
                    <span className="text-xs text-emerald-400 font-medium">منتشر شده</span>
                    <div className="text-2xl font-black text-emerald-400 mt-1 font-display">
                        {(summary.published || 0).toLocaleString('fa-IR')}
                    </div>
                </div>

                <div className="p-4 rounded-2xl bg-slate-900/60 border border-brand-500/20 backdrop-blur-xl">
                    <span className="text-xs text-brand-400 font-medium">در حال برگزاری</span>
                    <div className="text-2xl font-black text-brand-400 mt-1 font-display">
                        {(summary.started || 0).toLocaleString('fa-IR')}
                    </div>
                </div>

                <div className="p-4 rounded-2xl bg-slate-900/60 border border-slate-700/40 backdrop-blur-xl">
                    <span className="text-xs text-slate-400 font-medium">پیش‌نویس</span>
                    <div className="text-2xl font-black text-slate-300 mt-1 font-display">
                        {(summary.draft || 0).toLocaleString('fa-IR')}
                    </div>
                </div>

                <div className="p-4 rounded-2xl bg-slate-900/60 border border-amber-500/20 backdrop-blur-xl">
                    <span className="text-xs text-amber-400 font-medium">ویژه صفحه اصلی</span>
                    <div className="text-2xl font-black text-amber-400 mt-1 font-display">
                        {(summary.featured || 0).toLocaleString('fa-IR')}
                    </div>
                </div>

                <div className="p-4 rounded-2xl bg-slate-900/60 border border-purple-500/20 backdrop-blur-xl">
                    <span className="text-xs text-purple-400 font-medium">دانشجویان ثبت‌نامی</span>
                    <div className="text-2xl font-black text-purple-400 mt-1 font-display">
                        {(summary.total_enrolled || 0).toLocaleString('fa-IR')}
                    </div>
                </div>
            </div>

            {/* Filter & Search Bar */}
            <div className="p-4 rounded-3xl bg-slate-900/60 border border-slate-800/80 backdrop-blur-xl space-y-4">
                <div className="flex flex-col lg:flex-row gap-3">
                    {/* Search Input */}
                    <form onSubmit={handleSearchSubmit} className="relative flex-1">
                        <Search className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="جستجو بر اساس عنوان، توضیحات یا نام مدرس..."
                            className="w-full pl-4 pr-10 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all"
                        />
                    </form>

                    {/* Filter Selects */}
                    <div className="flex flex-wrap sm:flex-nowrap items-center gap-2">
                        {/* Category Filter */}
                        <select
                            value={categoryId}
                            onChange={(e) => {
                                setCategoryId(e.target.value);
                                applyFilters({ category_id: e.target.value });
                            }}
                            className="px-3 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                        >
                            <option value="">همه دسته‌بندی‌ها</option>
                            {categories.map((cat) => (
                                <option key={cat.id} value={cat.id}>
                                    {cat.name}
                                </option>
                            ))}
                        </select>

                        {/* Status Filter */}
                        <select
                            value={status}
                            onChange={(e) => {
                                setStatus(e.target.value);
                                applyFilters({ status: e.target.value });
                            }}
                            className="px-3 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                        >
                            <option value="">همه وضعیت‌ها</option>
                            <option value="published">منتشر شده</option>
                            <option value="started">در حال برگزاری</option>
                            <option value="draft">پیش‌نویس</option>
                            <option value="archived">آرشیو شده</option>
                        </select>

                        {/* Level Filter */}
                        <select
                            value={level}
                            onChange={(e) => {
                                setLevel(e.target.value);
                                applyFilters({ level: e.target.value });
                            }}
                            className="px-3 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                        >
                            <option value="">همه سطوح</option>
                            <option value="beginner">مبتدی</option>
                            <option value="intermediate">متوسط</option>
                            <option value="advanced">پیشرفته</option>
                        </select>

                        {/* Reset Filters */}
                        {(search || status || level || categoryId || isFeatured) && (
                            <button
                                onClick={handleResetFilters}
                                className="px-3 py-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-bold transition-colors"
                            >
                                حذف فیلترها
                            </button>
                        )}

                        {/* View Mode Toggle */}
                        <div className="flex items-center gap-1 p-1 bg-slate-950/60 border border-slate-800 rounded-xl">
                            <button
                                onClick={() => setViewMode('table')}
                                className={`p-1.5 rounded-lg transition-colors ${
                                    viewMode === 'table' ? 'bg-brand-500 text-white shadow-sm' : 'text-slate-400 hover:text-white'
                                }`}
                                title="نمای جدول"
                            >
                                <List size={16} />
                            </button>
                            <button
                                onClick={() => setViewMode('grid')}
                                className={`p-1.5 rounded-lg transition-colors ${
                                    viewMode === 'grid' ? 'bg-brand-500 text-white shadow-sm' : 'text-slate-400 hover:text-white'
                                }`}
                                title="نمای کارتی"
                            >
                                <LayoutGrid size={16} />
                            </button>
                        </div>
                    </div>
                </div>

                {/* Bulk Actions Bar (Shown when items are selected) */}
                {selectedIds.length > 0 && (
                    <div className="flex flex-wrap items-center justify-between gap-3 p-3 rounded-2xl bg-brand-500/10 border border-brand-500/30 text-xs animate-in fade-in duration-200">
                        <div className="flex items-center gap-2 font-bold text-brand-300">
                            <span className="w-5 h-5 rounded-full bg-brand-500 text-white flex items-center justify-center text-[10px]">
                                {selectedIds.length}
                            </span>
                            <span>دوره انتخاب شده است</span>
                        </div>

                        <div className="flex items-center gap-2 flex-wrap">
                            <button
                                onClick={() => handleBulkAction('publish')}
                                className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 font-bold transition-colors"
                            >
                                <CheckCircle2 size={14} />
                                <span>انتشار گروهی</span>
                            </button>

                            <button
                                onClick={() => handleBulkAction('start')}
                                className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-500/20 hover:bg-brand-500/30 text-brand-300 border border-brand-500/30 font-bold transition-colors"
                            >
                                <PlayCircle size={14} />
                                <span>شروع دوره‌ها</span>
                            </button>

                            <button
                                onClick={() => handleBulkAction('archive')}
                                className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 font-bold transition-colors"
                            >
                                <Archive size={14} />
                                <span>آرشیو گروهی</span>
                            </button>

                            <button
                                onClick={() => handleBulkAction('delete')}
                                className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 font-bold transition-colors"
                            >
                                <Trash2 size={14} />
                                <span>حذف گروهی</span>
                            </button>

                            <button
                                onClick={() => setSelectedIds([])}
                                className="p-1.5 text-slate-400 hover:text-white"
                                title="لغو انتخاب"
                            >
                                <X size={15} />
                            </button>
                        </div>
                    </div>
                )}
            </div>

            {/* Courses Content (Table / Grid) */}
            {courses.data.length === 0 ? (
                <div className="rounded-3xl p-12 bg-slate-900/40 border border-slate-800 text-center space-y-3">
                    <GraduationCap size={48} className="mx-auto text-slate-600" />
                    <h3 className="text-lg font-bold text-white">دوره‌ای یافت نشد</h3>
                    <p className="text-xs text-slate-400 max-w-sm mx-auto">
                        هیچ دوره‌ای مطابق با فیلترهای اعمال شده پیدا نشد. می‌توانید فیلترها را تغییر داده یا دوره جدیدی اضافه کنید.
                    </p>
                    <Link
                        href="/admin/courses/create"
                        className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-xs shadow-glow transition-colors mt-2"
                    >
                        <PlusCircle size={15} />
                        <span>ایجاد دوره اول</span>
                    </Link>
                </div>
            ) : viewMode === 'table' ? (
                /* Table View */
                <div className="rounded-3xl bg-slate-900/60 border border-slate-800/80 backdrop-blur-xl overflow-hidden shadow-2xl">
                    <div className="overflow-x-auto">
                        <table className="w-full text-right text-xs">
                            <thead className="bg-slate-950/60 text-slate-400 border-b border-slate-800 text-[11px] uppercase tracking-wider font-bold">
                                <tr>
                                    <th className="p-4 w-10 text-center">
                                        <input
                                            type="checkbox"
                                            checked={isAllSelected}
                                            onChange={toggleSelectAll}
                                            className="rounded border-slate-700 bg-slate-800 text-brand-500 focus:ring-brand-500"
                                        />
                                    </th>
                                    <th className="p-4">دوره آموزشی</th>
                                    <th className="p-4">دسته‌بندی</th>
                                    <th className="p-4">مدرس</th>
                                    <th className="p-4">سطح</th>
                                    <th className="p-4">وضعیت</th>
                                    <th className="p-4 text-center">دانشجو</th>
                                    <th className="p-4 text-center">ویژه</th>
                                    <th className="p-4 text-center">عملیات</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-800/60 text-slate-200">
                                {courses.data.map((c) => {
                                    const statusInfo = statusBadges[c.status] || statusBadges.draft;
                                    const levelInfo = levelLabels[c.level] || levelLabels.beginner;
                                    const isSelected = selectedIds.includes(c.id);

                                    return (
                                        <tr
                                            key={c.id}
                                            className={`hover:bg-slate-800/40 transition-colors ${
                                                isSelected ? 'bg-brand-500/5' : ''
                                            }`}
                                        >
                                            <td className="p-4 text-center">
                                                <input
                                                    type="checkbox"
                                                    checked={isSelected}
                                                    onChange={() => toggleSelectOne(c.id)}
                                                    className="rounded border-slate-700 bg-slate-800 text-brand-500 focus:ring-brand-500"
                                                />
                                            </td>

                                            {/* Course Title & Thumbnail */}
                                            <td className="p-4">
                                                <div className="flex items-center gap-3 min-w-[220px]">
                                                    <div className="w-12 h-12 rounded-xl bg-slate-800 border border-slate-700/60 overflow-hidden shrink-0 flex items-center justify-center">
                                                        {c.thumbnail ? (
                                                            <img
                                                                src={c.thumbnail}
                                                                alt={c.title}
                                                                className="w-full h-full object-cover"
                                                            />
                                                        ) : (
                                                            <GraduationCap className="text-slate-500" size={20} />
                                                        )}
                                                    </div>
                                                    <div className="min-w-0">
                                                        <Link
                                                            href={`/admin/courses/${c.id}/edit`}
                                                            className="font-bold text-white hover:text-brand-400 transition-colors truncate block text-sm"
                                                        >
                                                            {c.title}
                                                        </Link>
                                                        <span className="text-[11px] text-slate-400 truncate block mt-0.5" dir="ltr">
                                                            /{c.slug}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

                                            {/* Category */}
                                            <td className="p-4">
                                                <span className="px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700/60 text-[11px] font-semibold text-slate-300">
                                                    {c.category_name}
                                                </span>
                                            </td>

                                            {/* Teacher */}
                                            <td className="p-4">
                                                <span className="text-slate-300 font-medium">
                                                    {c.teacher_name}
                                                </span>
                                            </td>

                                            {/* Level */}
                                            <td className="p-4">
                                                <span className={`font-semibold ${levelInfo.color}`}>
                                                    {levelInfo.label}
                                                </span>
                                            </td>

                                            {/* Status Badge */}
                                            <td className="p-4">
                                                <span
                                                    className={`px-2.5 py-1 rounded-full text-[11px] font-bold border ${statusInfo.class}`}
                                                >
                                                    {statusInfo.label}
                                                </span>
                                            </td>

                                            {/* Students Count */}
                                            <td className="p-4 text-center">
                                                <button
                                                    onClick={() => setViewingStudentsCourse(c)}
                                                    className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-800/90 hover:bg-brand-500/20 text-slate-300 hover:text-brand-300 border border-slate-700/70 hover:border-brand-500/40 font-bold transition-all group/btn shadow-sm"
                                                    title="مشاهده مشخصات تمام شاگردان این کلاس"
                                                >
                                                    <Users size={13} className="text-brand-400 group-hover/btn:scale-110 transition-transform" />
                                                    <span>{c.enrolled_count.toLocaleString('fa-IR')}</span>
                                                </button>
                                            </td>

                                            {/* Featured in Hero Toggle */}
                                            <td className="p-4 text-center">
                                                <button
                                                    onClick={() => handleToggleFeatured(c.id)}
                                                    className={`p-1.5 rounded-lg transition-all ${
                                                        c.is_featured
                                                            ? 'text-amber-400 bg-amber-500/10 hover:bg-amber-500/20'
                                                            : 'text-slate-600 hover:text-slate-400 hover:bg-slate-800'
                                                    }`}
                                                    title={c.is_featured ? 'دوره ویژه فعال است (طراحی و نمایش VIP در وب‌سایت)' : 'تنظیم به عنوان دوره ویژه (VIP)'}
                                                >
                                                    <Star size={17} className={c.is_featured ? 'fill-amber-400' : ''} />
                                                </button>
                                            </td>

                                            {/* Actions */}
                                            <td className="p-4 text-center">
                                                <div className="flex items-center justify-center gap-1.5">
                                                    <button
                                                        onClick={() => setViewingStudentsCourse(c)}
                                                        className="p-1.5 rounded-lg text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/10 transition-colors"
                                                        title="مشاهده مشخصات شاگردان کلاس"
                                                    >
                                                        <Users size={16} />
                                                    </button>
                                                    <a
                                                        href={`/courses/${c.slug}`}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                                                        title="پیش‌نمایش در سایت"
                                                    >
                                                        <Eye size={16} />
                                                    </a>
                                                    <Link
                                                        href={`/admin/courses/${c.id}/edit`}
                                                        className="p-1.5 rounded-lg text-brand-400 hover:text-brand-300 hover:bg-brand-500/10 transition-colors"
                                                        title="ویرایش دوره"
                                                    >
                                                        <Edit3 size={16} />
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDeleteCourse(c)}
                                                        className="p-1.5 rounded-lg text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-colors"
                                                        title="حذف دوره"
                                                    >
                                                        <Trash2 size={16} />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                </div>
            ) : (
                /* Grid Cards View */
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {courses.data.map((c) => {
                        const statusInfo = statusBadges[c.status] || statusBadges.draft;
                        const levelInfo = levelLabels[c.level] || levelLabels.beginner;

                        return (
                            <div
                                key={c.id}
                                className="rounded-3xl p-5 bg-slate-900/60 border border-slate-800/80 backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition-all group hover:-translate-y-1"
                            >
                                <div>
                                    {/* Thumbnail Image Header */}
                                    <div className="relative aspect-video rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 mb-4">
                                        {c.thumbnail ? (
                                            <img
                                                src={c.thumbnail}
                                                alt={c.title}
                                                className="w-full h-full object-cover transition-transform group-hover:scale-105 duration-300"
                                            />
                                        ) : (
                                            <div className="w-full h-full flex items-center justify-center bg-gradient-to-tr from-slate-900 to-slate-800 text-slate-600">
                                                <GraduationCap size={40} />
                                            </div>
                                        )}

                                        {/* Status Badge on top */}
                                        <div className="absolute top-3 right-3">
                                            <span className={`px-2.5 py-1 rounded-full text-[10px] font-bold border ${statusInfo.class}`}>
                                                {statusInfo.label}
                                            </span>
                                        </div>

                                        {/* Star featured on top left */}
                                        <div className="absolute top-3 left-3">
                                            <button
                                                onClick={() => handleToggleFeatured(c.id)}
                                                className={`p-1.5 rounded-xl backdrop-blur-md transition-all ${
                                                    c.is_featured
                                                        ? 'bg-amber-500/20 text-amber-400 border border-amber-500/40'
                                                        : 'bg-slate-900/80 text-slate-400 hover:text-white'
                                                }`}
                                                title={c.is_featured ? 'دوره ویژه فعال است (طراحی و نمایش VIP در وب‌سایت)' : 'تنظیم به عنوان دوره ویژه (VIP)'}
                                            >
                                                <Star size={15} className={c.is_featured ? 'fill-amber-400' : ''} />
                                            </button>
                                        </div>
                                    </div>

                                    {/* Category & Level */}
                                    <div className="flex items-center justify-between text-xs mb-2">
                                        <span className="text-brand-400 font-semibold">{c.category_name}</span>
                                        <span className={`font-semibold ${levelInfo.color}`}>{levelInfo.label}</span>
                                    </div>

                                    {/* Title */}
                                    <Link
                                        href={`/admin/courses/${c.id}/edit`}
                                        className="text-base font-bold text-white hover:text-brand-400 transition-colors line-clamp-2"
                                    >
                                        {c.title}
                                    </Link>

                                    <p className="text-xs text-slate-400 mt-1">مدرس: {c.teacher_name}</p>
                                </div>

                                {/* Card Footer Stats & Actions */}
                                <div className="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs">
                                    <button
                                        onClick={() => setViewingStudentsCourse(c)}
                                        className="flex items-center gap-1.5 text-slate-400 hover:text-brand-400 transition-colors group/btn"
                                        title="مشاهده مشخصات شاگردان این کلاس"
                                    >
                                        <Users size={14} className="text-brand-400 group-hover/btn:scale-110 transition-transform" />
                                        <span className="font-semibold">{c.enrolled_count.toLocaleString('fa-IR')} دانشجو</span>
                                    </button>

                                    <div className="flex items-center gap-1.5">
                                        <button
                                            onClick={() => setViewingStudentsCourse(c)}
                                            className="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-bold transition-colors flex items-center gap-1"
                                            title="مشاهده شاگردان صنف"
                                        >
                                            <Users size={13} />
                                            <span>شاگردان</span>
                                        </button>
                                        <Link
                                            href={`/admin/courses/${c.id}/edit`}
                                            className="px-3 py-1.5 rounded-xl bg-brand-500/10 hover:bg-brand-500/20 text-brand-400 border border-brand-500/30 text-xs font-bold transition-colors"
                                        >
                                            ویرایش
                                        </Link>
                                        <button
                                            onClick={() => handleDeleteCourse(c)}
                                            className="p-1.5 rounded-xl text-rose-400 hover:bg-rose-500/10 transition-colors"
                                        >
                                            <Trash2 size={16} />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}

            {/* Pagination */}
            {courses.links && courses.links.length > 3 && (
                <div className="flex items-center justify-center gap-2 pt-4">
                    {courses.links.map((link, idx) => {
                        if (!link.url && link.label === '...') {
                            return (
                                <span key={idx} className="px-3 py-2 text-slate-600 text-xs">
                                    ...
                                </span>
                            );
                        }

                        // Clean HTML entities from labels
                        const labelText = link.label.replace('&laquo; Previous', 'قبلی').replace('Next &raquo;', 'بعدی');

                        return link.url ? (
                            <Link
                                key={idx}
                                href={link.url}
                                preserveScroll
                                preserveState
                                className={`px-3.5 py-2 rounded-xl text-xs font-bold transition-all ${
                                    link.active
                                        ? 'bg-brand-500 text-white shadow-glow'
                                        : 'bg-slate-900/80 hover:bg-slate-800 text-slate-300 border border-slate-800'
                                }`}
                            >
                                {labelText}
                            </Link>
                        ) : null;
                    })}
                </div>
            )}

            {/* Delete Single Course Modal */}
            {deleteModalCourse && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in duration-200">
                    <div className="w-full max-w-md rounded-3xl bg-slate-900 border border-slate-800 p-6 shadow-2xl space-y-4">
                        <div className="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center mx-auto border border-rose-500/20">
                            <AlertTriangle size={24} />
                        </div>
                        <div className="text-center space-y-1">
                            <h3 className="text-lg font-bold text-white">آیا از حذف دوره اطمینان دارید؟</h3>
                            <p className="text-xs text-slate-400">
                                دوره «{deleteModalCourse.title}» و تمام سرفصل‌های آن به طور کامل حذف خواهند شد.
                            </p>
                        </div>
                        <div className="flex items-center gap-3 pt-2">
                            <button
                                onClick={() => setDeleteModalCourse(null)}
                                className="flex-1 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors"
                            >
                                انصراف
                            </button>
                            <button
                                onClick={confirmSingleDelete}
                                className="flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-600/30 transition-colors"
                            >
                                بله، حذف شود
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Bulk Delete Confirmation Modal */}
            {bulkDeleteModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in duration-200">
                    <div className="w-full max-w-md rounded-3xl bg-slate-900 border border-slate-800 p-6 shadow-2xl space-y-4">
                        <div className="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center mx-auto border border-rose-500/20">
                            <AlertTriangle size={24} />
                        </div>
                        <div className="text-center space-y-1">
                            <h3 className="text-lg font-bold text-white">حذف گروهی دوره‌ها</h3>
                            <p className="text-xs text-slate-400">
                                آیا از حذف <span className="text-rose-400 font-bold">{selectedIds.length}</span> دوره انتخاب شده اطمینان دارید؟ این عملیات غیرقابل بازگشت است.
                            </p>
                        </div>
                        <div className="flex items-center gap-3 pt-2">
                            <button
                                onClick={() => setBulkDeleteModalOpen(false)}
                                className="flex-1 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors"
                            >
                                انصراف
                            </button>
                            <button
                                onClick={confirmBulkDelete}
                                className="flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-600/30 transition-colors"
                            >
                                حذف همه موارد
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Course Students Viewer Modal */}
            <CourseStudentsModal
                course={viewingStudentsCourse}
                isOpen={Boolean(viewingStudentsCourse)}
                onClose={() => setViewingStudentsCourse(null)}
                onCountUpdated={(courseId, newCount) => {
                    const found = courses.data.find((c) => c.id === courseId);
                    if (found) {
                        found.enrolled_count = newCount;
                    }
                }}
            />
        </AdminLayout>
    );
}
