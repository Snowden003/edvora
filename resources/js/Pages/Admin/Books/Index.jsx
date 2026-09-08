import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    BookOpen,
    PlusCircle,
    Search,
    Filter,
    CheckCircle2,
    Clock,
    Download,
    Eye,
    Trash2,
    Edit3,
    ExternalLink,
    ChevronLeft,
    ChevronRight,
    LayoutGrid,
    List,
    AlertTriangle,
    X,
    Sparkles,
    FileText,
    FolderKanban,
    RotateCcw,
    Layers,
    BookMarked,
    Check,
    ArrowUpDown
} from 'lucide-react';

export default function Index({ books, summary = {}, categories = [], filters = {} }) {
    const [search, setSearch] = useState(filters.search || '');
    const [categoryId, setCategoryId] = useState(filters.category_id || '');
    const [isPublished, setIsPublished] = useState(
        filters.is_published !== undefined && filters.is_published !== null ? String(filters.is_published) : ''
    );
    const [sortBy, setSortBy] = useState(filters.sort_by || 'created_at');
    const [sortDir, setSortDir] = useState(filters.sort_dir || 'desc');
    const [viewMode, setViewMode] = useState('table'); // 'table' | 'grid'

    // Selection for bulk actions
    const [selectedIds, setSelectedIds] = useState([]);
    const [deleteModalBook, setDeleteModalBook] = useState(null);
    const [bulkDeleteModalOpen, setBulkDeleteModalOpen] = useState(false);
    const [previewBook, setPreviewBook] = useState(null);

    // Filter apply helper
    const applyFilters = (overrides = {}) => {
        const query = {
            search,
            category_id: categoryId,
            is_published: isPublished,
            sort_by: sortBy,
            sort_dir: sortDir,
            ...overrides,
        };

        // Remove empty values
        Object.keys(query).forEach((key) => {
            if (query[key] === '' || query[key] === null || query[key] === undefined) {
                delete query[key];
            }
        });

        router.get('/admin/books', query, { preserveState: true, preserveScroll: true });
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        applyFilters({ search });
    };

    const handleResetFilters = () => {
        setSearch('');
        setCategoryId('');
        setIsPublished('');
        setSortBy('created_at');
        setSortDir('desc');
        router.get('/admin/books', {}, { preserveState: true, preserveScroll: true });
    };

    // Bulk selection helpers
    const allIds = books.data.map((b) => b.id);
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
            '/admin/books/bulk-action',
            { action, ids: selectedIds },
            {
                preserveScroll: true,
                onSuccess: () => setSelectedIds([]),
            }
        );
    };

    const confirmBulkDelete = () => {
        router.post(
            '/admin/books/bulk-action',
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

    const handleTogglePublish = (bookId) => {
        router.post(`/admin/books/${bookId}/toggle-publish`, {}, { preserveScroll: true });
    };

    const confirmSingleDelete = () => {
        if (!deleteModalBook) return;
        router.delete(`/admin/books/${deleteModalBook.id}`, {
            preserveScroll: true,
            onSuccess: () => setDeleteModalBook(null),
        });
    };

    const handleSortChange = (newSortBy) => {
        let newDir = 'asc';
        if (sortBy === newSortBy) {
            newDir = sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            newDir = newSortBy === 'created_at' || newSortBy === 'download_count' || newSortBy === 'view_count' ? 'desc' : 'asc';
        }
        setSortBy(newSortBy);
        setSortDir(newDir);
        applyFilters({ sort_by: newSortBy, sort_dir: newDir });
    };

    return (
        <AdminLayout title="مدیریت کتابخانه و مقالات | ادورا">
            <div className="space-y-6">
                {/* Header with Title and Create Button */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/60 backdrop-blur-xl p-6 rounded-3xl border border-slate-800/80 shadow-2xl relative overflow-hidden">
                    <div className="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none" />
                    <div className="absolute -left-10 -bottom-10 w-40 h-40 bg-brand-500/10 rounded-full blur-3xl pointer-events-none" />

                    <div className="flex items-center gap-4 relative z-10">
                        <div className="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20 shrink-0">
                            <BookOpen size={28} />
                        </div>
                        <div>
                            <div className="flex items-center gap-2">
                                <h1 className="text-xl sm:text-2xl font-black text-white tracking-tight font-display">
                                    مدیریت کتابخانه و مقالات
                                </h1>
                                <span className="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    {summary.total || 0} اثر
                                </span>
                            </div>
                            <p className="text-xs sm:text-sm text-slate-400 mt-1">
                                انتشار و مدیریت کتاب‌ها، جزوات آموزشی و مقالات تخصصی ادورا همراه با فایل PDF و تصویر کاور
                            </p>
                        </div>
                    </div>

                    <div className="flex items-center gap-3 relative z-10">
                        <Link
                            href="/admin/books/create"
                            className="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-bold shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/50 transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]"
                        >
                            <PlusCircle size={18} />
                            <span>افزودن کتاب / مقاله جدید</span>
                        </Link>
                    </div>
                </div>

                {/* KPI Summary Cards */}
                <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                    {/* Total Books */}
                    <div className="p-4 rounded-2xl bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 shadow-lg flex flex-col justify-between hover:border-slate-700 transition-all">
                        <div className="flex items-center justify-between text-slate-400 mb-2">
                            <span className="text-xs font-semibold">کل کتاب‌ها</span>
                            <BookOpen size={16} className="text-brand-400" />
                        </div>
                        <div className="text-2xl font-black text-white font-mono">{summary.total || 0}</div>
                        <span className="text-[10px] text-slate-500 mt-1">آثار ثبت شده در دیتابیس</span>
                    </div>

                    {/* Published */}
                    <div className="p-4 rounded-2xl bg-slate-900/60 backdrop-blur-xl border border-emerald-500/20 bg-emerald-950/10 shadow-lg flex flex-col justify-between hover:border-emerald-500/40 transition-all">
                        <div className="flex items-center justify-between text-emerald-400 mb-2">
                            <span className="text-xs font-semibold">منتشر شده</span>
                            <CheckCircle2 size={16} className="text-emerald-400" />
                        </div>
                        <div className="text-2xl font-black text-emerald-400 font-mono">{summary.published || 0}</div>
                        <span className="text-[10px] text-emerald-500/80 mt-1">قابل مشاهده در سایت</span>
                    </div>

                    {/* Drafts */}
                    <div className="p-4 rounded-2xl bg-slate-900/60 backdrop-blur-xl border border-amber-500/20 bg-amber-950/10 shadow-lg flex flex-col justify-between hover:border-amber-500/40 transition-all">
                        <div className="flex items-center justify-between text-amber-400 mb-2">
                            <span className="text-xs font-semibold">پیش‌نویس</span>
                            <Clock size={16} className="text-amber-400" />
                        </div>
                        <div className="text-2xl font-black text-amber-400 font-mono">{summary.draft || 0}</div>
                        <span className="text-[10px] text-amber-500/80 mt-1">مخفی از دید عموم</span>
                    </div>

                    {/* Total Downloads */}
                    <div className="p-4 rounded-2xl bg-slate-900/60 backdrop-blur-xl border border-indigo-500/20 bg-indigo-950/10 shadow-lg flex flex-col justify-between hover:border-indigo-500/40 transition-all">
                        <div className="flex items-center justify-between text-indigo-400 mb-2">
                            <span className="text-xs font-semibold">مجموع دانلودها</span>
                            <Download size={16} className="text-indigo-400" />
                        </div>
                        <div className="text-2xl font-black text-indigo-400 font-mono">{summary.total_downloads || 0}</div>
                        <span className="text-[10px] text-indigo-500/80 mt-1">بارگیری PDF</span>
                    </div>

                    {/* Total Views */}
                    <div className="p-4 rounded-2xl bg-slate-900/60 backdrop-blur-xl border border-sky-500/20 bg-sky-950/10 shadow-lg flex flex-col justify-between hover:border-sky-500/40 transition-all">
                        <div className="flex items-center justify-between text-sky-400 mb-2">
                            <span className="text-xs font-semibold">مجموع بازدیدها</span>
                            <Eye size={16} className="text-sky-400" />
                        </div>
                        <div className="text-2xl font-black text-sky-400 font-mono">{summary.total_views || 0}</div>
                        <span className="text-[10px] text-sky-500/80 mt-1">مشاهده صفحات</span>
                    </div>

                    {/* Categories */}
                    <div className="p-4 rounded-2xl bg-slate-900/60 backdrop-blur-xl border border-purple-500/20 bg-purple-950/10 shadow-lg flex flex-col justify-between hover:border-purple-500/40 transition-all">
                        <div className="flex items-center justify-between text-purple-400 mb-2">
                            <span className="text-xs font-semibold">دسته‌بندی‌ها</span>
                            <FolderKanban size={16} className="text-purple-400" />
                        </div>
                        <div className="text-2xl font-black text-purple-400 font-mono">{summary.categories_count || 0}</div>
                        <span className="text-[10px] text-purple-500/80 mt-1">دارای کتاب فعال</span>
                    </div>
                </div>

                {/* Filters, Search and View Controls */}
                <div className="p-5 rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-4">
                    <div className="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                        {/* Search Input */}
                        <form onSubmit={handleSearchSubmit} className="flex-1 relative">
                            <Search size={18} className="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                            <input
                                type="text"
                                placeholder="جستجوی عنوان، نویسنده، توضیحات، یا نامک (Slug)..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-10 pr-11 py-2.5 rounded-2xl bg-slate-950/60 border border-slate-800 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all"
                            />
                            {search && (
                                <button
                                    type="button"
                                    onClick={() => {
                                        setSearch('');
                                        applyFilters({ search: '' });
                                    }}
                                    className="absolute left-3 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-200"
                                >
                                    <X size={16} />
                                </button>
                            )}
                        </form>

                        {/* View Switcher and Reset Button */}
                        <div className="flex items-center gap-2 self-end lg:self-auto">
                            {(search || categoryId || isPublished || sortBy !== 'created_at') && (
                                <button
                                    onClick={handleResetFilters}
                                    className="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-semibold transition-all border border-slate-700"
                                    title="پاک کردن فیلترها"
                                >
                                    <RotateCcw size={14} />
                                    <span>بازنشانی</span>
                                </button>
                            )}

                            {/* View Switcher: Table vs Grid */}
                            <div className="flex items-center p-1 rounded-xl bg-slate-950/80 border border-slate-800">
                                <button
                                    onClick={() => setViewMode('table')}
                                    className={`p-1.5 rounded-lg transition-all ${
                                        viewMode === 'table'
                                            ? 'bg-brand-500 text-white shadow-md'
                                            : 'text-slate-400 hover:text-slate-200'
                                    }`}
                                    title="نمای جدول"
                                >
                                    <List size={18} />
                                </button>
                                <button
                                    onClick={() => setViewMode('grid')}
                                    className={`p-1.5 rounded-lg transition-all ${
                                        viewMode === 'grid'
                                            ? 'bg-brand-500 text-white shadow-md'
                                            : 'text-slate-400 hover:text-slate-200'
                                    }`}
                                    title="نمای کارت‌ها"
                                >
                                    <LayoutGrid size={18} />
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Filter Dropdowns */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2 border-t border-slate-800/60 text-xs">
                        {/* Category Filter */}
                        <div>
                            <label className="block text-[11px] font-semibold text-slate-400 mb-1">دسته‌بندی</label>
                            <select
                                value={categoryId}
                                onChange={(e) => {
                                    setCategoryId(e.target.value);
                                    applyFilters({ category_id: e.target.value });
                                }}
                                className="w-full px-3 py-2 rounded-xl bg-slate-950/80 border border-slate-800 text-slate-200 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20"
                            >
                                <option value="">همه دسته‌بندی‌ها</option>
                                {categories.map((cat) => (
                                    <option key={cat.id} value={cat.id}>
                                        {cat.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Status Filter */}
                        <div>
                            <label className="block text-[11px] font-semibold text-slate-400 mb-1">وضعیت انتشار</label>
                            <select
                                value={isPublished}
                                onChange={(e) => {
                                    setIsPublished(e.target.value);
                                    applyFilters({ is_published: e.target.value });
                                }}
                                className="w-full px-3 py-2 rounded-xl bg-slate-950/80 border border-slate-800 text-slate-200 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20"
                            >
                                <option value="">همه وضعیت‌ها</option>
                                <option value="true">منتشر شده (عمومی)</option>
                                <option value="false">پیش‌نویس (غیرفعال)</option>
                            </select>
                        </div>

                        {/* Sort By */}
                        <div>
                            <label className="block text-[11px] font-semibold text-slate-400 mb-1">مرتب‌سازی بر اساس</label>
                            <select
                                value={sortBy}
                                onChange={(e) => {
                                    setSortBy(e.target.value);
                                    applyFilters({ sort_by: e.target.value });
                                }}
                                className="w-full px-3 py-2 rounded-xl bg-slate-950/80 border border-slate-800 text-slate-200 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20"
                            >
                                <option value="created_at">تاریخ ایجاد</option>
                                <option value="title">عنوان کتاب</option>
                                <option value="download_count">بیشترین دانلود</option>
                                <option value="view_count">بیشترین بازدید</option>
                                <option value="author">نویسنده</option>
                            </select>
                        </div>

                        {/* Sort Direction */}
                        <div>
                            <label className="block text-[11px] font-semibold text-slate-400 mb-1">ترتیب</label>
                            <select
                                value={sortDir}
                                onChange={(e) => {
                                    setSortDir(e.target.value);
                                    applyFilters({ sort_dir: e.target.value });
                                }}
                                className="w-full px-3 py-2 rounded-xl bg-slate-950/80 border border-slate-800 text-slate-200 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20"
                            >
                                <option value="desc">نزولی (بیشترین / جدیدترین)</option>
                                <option value="asc">صعودی (کمترین / قدیمی‌ترین)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {/* Bulk Actions Floating Bar */}
                {selectedIds.length > 0 && (
                    <div className="flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl bg-gradient-to-r from-slate-900 to-indigo-950/80 border border-indigo-500/40 shadow-2xl animate-in fade-in slide-in-from-bottom-2">
                        <div className="flex items-center gap-3">
                            <span className="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-300 font-bold flex items-center justify-center border border-indigo-500/30 text-xs">
                                {selectedIds.length}
                            </span>
                            <span className="text-sm font-bold text-white">کتاب / مقاله انتخاب شده است</span>
                        </div>

                        <div className="flex flex-wrap items-center gap-2">
                            <button
                                onClick={() => handleBulkAction('publish')}
                                className="px-3.5 py-1.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 text-xs font-bold transition-all flex items-center gap-1.5"
                            >
                                <CheckCircle2 size={14} />
                                <span>انتشار همگانی</span>
                            </button>

                            <button
                                onClick={() => handleBulkAction('unpublish')}
                                className="px-3.5 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 text-xs font-bold transition-all flex items-center gap-1.5"
                            >
                                <Clock size={14} />
                                <span>تبدیل به پیش‌نویس</span>
                            </button>

                            <button
                                onClick={() => handleBulkAction('delete')}
                                className="px-3.5 py-1.5 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 text-xs font-bold transition-all flex items-center gap-1.5"
                            >
                                <Trash2 size={14} />
                                <span>حذف گروهی</span>
                            </button>

                            <button
                                onClick={() => setSelectedIds([])}
                                className="p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors mr-2"
                                title="لغو انتخاب"
                            >
                                <X size={16} />
                            </button>
                        </div>
                    </div>
                )}

                {/* Content: Table View OR Grid View */}
                {books.data.length === 0 ? (
                    <div className="p-12 text-center rounded-3xl bg-slate-900/40 border border-slate-800/80 backdrop-blur-xl">
                        <div className="w-16 h-16 rounded-3xl bg-slate-800/80 border border-slate-700/80 flex items-center justify-center text-slate-400 mx-auto mb-4">
                            <BookOpen size={32} />
                        </div>
                        <h3 className="text-base font-bold text-white">هیچ کتاب یا مقاله‌ای یافت نشد</h3>
                        <p className="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                            با تغییر فیلترهای جستجو یا افزودن یک اثر جدید، کتابخانه و مقالات ادورا را گسترش دهید.
                        </p>
                        <div className="mt-6">
                            <Link
                                href="/admin/books/create"
                                className="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-lg shadow-brand-500/25 transition-all"
                            >
                                <PlusCircle size={16} />
                                <span>افزودن اولین کتاب</span>
                            </Link>
                        </div>
                    </div>
                ) : viewMode === 'table' ? (
                    /* Table View */
                    <div className="rounded-3xl bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 shadow-2xl overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full text-right text-sm">
                                <thead>
                                    <tr className="border-b border-slate-800 bg-slate-950/40 text-xs font-bold text-slate-400">
                                        <th className="p-4 w-12 text-center">
                                            <input
                                                type="checkbox"
                                                checked={isAllSelected}
                                                onChange={toggleSelectAll}
                                                className="rounded border-slate-700 bg-slate-900 text-brand-500 focus:ring-brand-500 focus:ring-offset-slate-900 w-4 h-4 cursor-pointer"
                                            />
                                        </th>
                                        <th className="py-4 px-3 w-16 text-center">جلد</th>
                                        <th
                                            className="py-4 px-4 cursor-pointer hover:text-white transition-colors"
                                            onClick={() => handleSortChange('title')}
                                        >
                                            <div className="flex items-center gap-1.5">
                                                <span>عنوان و نویسنده</span>
                                                <ArrowUpDown size={13} />
                                            </div>
                                        </th>
                                        <th className="py-4 px-4">دسته‌بندی</th>
                                        <th
                                            className="py-4 px-4 text-center cursor-pointer hover:text-white transition-colors"
                                            onClick={() => handleSortChange('is_published')}
                                        >
                                            <div className="flex items-center justify-center gap-1.5">
                                                <span>وضعیت انتشار</span>
                                                <ArrowUpDown size={13} />
                                            </div>
                                        </th>
                                        <th
                                            className="py-4 px-4 text-center cursor-pointer hover:text-white transition-colors"
                                            onClick={() => handleSortChange('download_count')}
                                        >
                                            <div className="flex items-center justify-center gap-1.5">
                                                <span>دانلود / بازدید</span>
                                                <ArrowUpDown size={13} />
                                            </div>
                                        </th>
                                        <th
                                            className="py-4 px-4 text-center cursor-pointer hover:text-white transition-colors"
                                            onClick={() => handleSortChange('created_at')}
                                        >
                                            <div className="flex items-center justify-center gap-1.5">
                                                <span>تاریخ ثبت</span>
                                                <ArrowUpDown size={13} />
                                            </div>
                                        </th>
                                        <th className="py-4 px-4 text-center w-36">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-800/60 text-slate-300">
                                    {books.data.map((book) => {
                                        const isSelected = selectedIds.includes(book.id);
                                        return (
                                            <tr
                                                key={book.id}
                                                className={`hover:bg-slate-800/40 transition-colors ${
                                                    isSelected ? 'bg-brand-500/10' : ''
                                                }`}
                                            >
                                                <td className="p-4 text-center">
                                                    <input
                                                        type="checkbox"
                                                        checked={isSelected}
                                                        onChange={() => toggleSelectOne(book.id)}
                                                        className="rounded border-slate-700 bg-slate-900 text-brand-500 focus:ring-brand-500 focus:ring-offset-slate-900 w-4 h-4 cursor-pointer"
                                                    />
                                                </td>

                                                {/* Cover Image Thumbnail */}
                                                <td className="py-3 px-3 text-center">
                                                    <div
                                                        onClick={() => setPreviewBook(book)}
                                                        className="w-11 h-15 rounded-lg overflow-hidden bg-slate-800 border border-slate-700 shadow-md mx-auto cursor-pointer group relative flex items-center justify-center"
                                                    >
                                                        {book.cover_image ? (
                                                            <img
                                                                src={book.cover_image}
                                                                alt={book.title}
                                                                className="w-full h-full object-cover transition-transform group-hover:scale-110"
                                                            />
                                                        ) : (
                                                            <BookMarked size={20} className="text-slate-500" />
                                                        )}
                                                        <div className="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                                            <Eye size={14} />
                                                        </div>
                                                    </div>
                                                </td>

                                                {/* Title & Author */}
                                                <td className="py-3 px-4">
                                                    <div className="flex flex-col min-w-0 max-w-md">
                                                        <span
                                                            onClick={() => setPreviewBook(book)}
                                                            className="font-bold text-white hover:text-brand-400 cursor-pointer transition-colors truncate"
                                                        >
                                                            {book.title}
                                                        </span>
                                                        <div className="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                                                            <span>نویسنده: {book.author || 'نامشخص'}</span>
                                                            <span className="text-slate-600">•</span>
                                                            <span className="text-[11px] font-mono text-slate-500 truncate" dir="ltr">
                                                                /{book.slug}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>

                                                {/* Category */}
                                                <td className="py-3 px-4">
                                                    <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                                        {book.category_name}
                                                    </span>
                                                </td>

                                                {/* Published Switcher */}
                                                <td className="py-3 px-4 text-center">
                                                    <button
                                                        onClick={() => handleTogglePublish(book.id)}
                                                        className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border transition-all ${
                                                            book.is_published
                                                                ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30 hover:bg-emerald-500/20'
                                                                : 'bg-amber-500/10 text-amber-400 border-amber-500/30 hover:bg-amber-500/20'
                                                        }`}
                                                        title="برای تغییر وضعیت کلیک کنید"
                                                    >
                                                        <span
                                                            className={`w-1.5 h-1.5 rounded-full ${
                                                                book.is_published ? 'bg-emerald-400' : 'bg-amber-400'
                                                            }`}
                                                        />
                                                        <span>{book.is_published ? 'منتشر شده' : 'پیش‌نویس'}</span>
                                                    </button>
                                                </td>

                                                {/* Downloads & Views */}
                                                <td className="py-3 px-4 text-center">
                                                    <div className="flex items-center justify-center gap-3 text-xs">
                                                        <span className="flex items-center gap-1 text-indigo-300" title="دانلودها">
                                                            <Download size={13} />
                                                            <span className="font-mono font-bold">{book.download_count}</span>
                                                        </span>
                                                        <span className="flex items-center gap-1 text-sky-300" title="بازدیدها">
                                                            <Eye size={13} />
                                                            <span className="font-mono font-bold">{book.view_count}</span>
                                                        </span>
                                                    </div>
                                                </td>

                                                {/* Created Date */}
                                                <td className="py-3 px-4 text-center text-xs text-slate-400 font-mono">
                                                    {book.created_at || '—'}
                                                </td>

                                                {/* Action Buttons */}
                                                <td className="py-3 px-4 text-center">
                                                    <div className="flex items-center justify-center gap-1.5">
                                                        {/* Public View link */}
                                                        {book.is_published && (
                                                            <a
                                                                href={`/books/${book.slug}`}
                                                                target="_blank"
                                                                rel="noreferrer"
                                                                className="p-1.5 rounded-xl bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition-colors"
                                                                title="مشاهده در وب‌سایت"
                                                            >
                                                                <ExternalLink size={15} />
                                                            </a>
                                                        )}

                                                        {/* Edit */}
                                                        <Link
                                                            href={`/admin/books/${book.id}/edit`}
                                                            className="p-1.5 rounded-xl bg-brand-500/20 text-brand-300 hover:bg-brand-500 hover:text-white transition-colors"
                                                            title="ویرایش کتاب"
                                                        >
                                                            <Edit3 size={15} />
                                                        </Link>

                                                        {/* Download PDF direct */}
                                                        {book.pdf_file && (
                                                            <a
                                                                href={book.pdf_file}
                                                                target="_blank"
                                                                rel="noreferrer"
                                                                download
                                                                className="p-1.5 rounded-xl bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500 hover:text-white transition-colors"
                                                                title="دانلود فایل PDF"
                                                            >
                                                                <Download size={15} />
                                                            </a>
                                                        )}

                                                        {/* Delete */}
                                                        <button
                                                            onClick={() => setDeleteModalBook(book)}
                                                            className="p-1.5 rounded-xl bg-rose-500/20 text-rose-300 hover:bg-rose-500 hover:text-white transition-colors"
                                                            title="حذف کتاب"
                                                        >
                                                            <Trash2 size={15} />
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
                    /* Grid / Card View */
                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        {books.data.map((book) => {
                            const isSelected = selectedIds.includes(book.id);
                            return (
                                <div
                                    key={book.id}
                                    className={`rounded-3xl bg-slate-900/70 backdrop-blur-xl border transition-all duration-300 flex flex-col justify-between overflow-hidden group shadow-xl hover:-translate-y-1.5 hover:shadow-2xl ${
                                        isSelected
                                            ? 'border-brand-500 ring-2 ring-brand-500/30'
                                            : 'border-slate-800/80 hover:border-slate-700'
                                    }`}
                                >
                                    {/* Cover Preview & Top Badges */}
                                    <div className="relative aspect-[3/4] bg-slate-950/80 overflow-hidden flex items-center justify-center">
                                        {book.cover_image ? (
                                            <img
                                                src={book.cover_image}
                                                alt={book.title}
                                                className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                            />
                                        ) : (
                                            <div className="flex flex-col items-center gap-2 text-slate-600">
                                                <BookOpen size={48} />
                                                <span className="text-xs font-semibold">فاقد تصویر جلد</span>
                                            </div>
                                        )}

                                        {/* Selection Checkbox */}
                                        <div className="absolute top-3 right-3 z-20">
                                            <input
                                                type="checkbox"
                                                checked={isSelected}
                                                onChange={() => toggleSelectOne(book.id)}
                                                className="rounded-lg border-slate-700 bg-slate-900/90 text-brand-500 focus:ring-brand-500 w-5 h-5 cursor-pointer shadow-lg"
                                            />
                                        </div>

                                        {/* Category Badge */}
                                        <div className="absolute top-3 left-3 z-20">
                                            <span className="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-950/80 backdrop-blur-md text-slate-200 border border-slate-700 shadow-md">
                                                {book.category_name}
                                            </span>
                                        </div>

                                        {/* Status Chip Overlay */}
                                        <div className="absolute bottom-3 right-3 z-20">
                                            <button
                                                onClick={() => handleTogglePublish(book.id)}
                                                className={`px-2.5 py-0.5 rounded-full text-[11px] font-bold backdrop-blur-md border shadow-md flex items-center gap-1 ${
                                                    book.is_published
                                                        ? 'bg-emerald-950/90 text-emerald-300 border-emerald-500/40'
                                                        : 'bg-amber-950/90 text-amber-300 border-amber-500/40'
                                                }`}
                                            >
                                                <span
                                                    className={`w-1.5 h-1.5 rounded-full ${
                                                        book.is_published ? 'bg-emerald-400' : 'bg-amber-400'
                                                    }`}
                                                />
                                                {book.is_published ? 'منتشر شده' : 'پیش‌نویس'}
                                            </button>
                                        </div>

                                        {/* Dark overlay with quick preview icon */}
                                        <div
                                            onClick={() => setPreviewBook(book)}
                                            className="absolute inset-0 bg-slate-950/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer z-10"
                                        >
                                            <span className="px-3.5 py-2 rounded-2xl bg-white/20 backdrop-blur-md text-white font-bold text-xs flex items-center gap-1.5 border border-white/30 shadow-2xl">
                                                <Eye size={15} />
                                                <span>مشاهده جزئیات</span>
                                            </span>
                                        </div>
                                    </div>

                                    {/* Card Body */}
                                    <div className="p-5 flex-1 flex flex-col justify-between space-y-4">
                                        <div>
                                            <h3
                                                onClick={() => setPreviewBook(book)}
                                                className="font-bold text-white text-base hover:text-brand-400 cursor-pointer transition-colors line-clamp-1"
                                                title={book.title}
                                            >
                                                {book.title}
                                            </h3>
                                            <p className="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
                                                <span>نویسنده:</span>
                                                <span className="text-slate-300 font-semibold">{book.author || 'نامشخص'}</span>
                                            </p>
                                        </div>

                                        {/* Download & View stats */}
                                        <div className="grid grid-cols-2 gap-2 pt-3 border-t border-slate-800/80 text-xs">
                                            <div className="p-2 rounded-xl bg-slate-950/50 border border-slate-800 flex items-center justify-between">
                                                <span className="text-[11px] text-slate-400 flex items-center gap-1">
                                                    <Download size={12} className="text-indigo-400" />
                                                    دانلود
                                                </span>
                                                <span className="font-bold text-indigo-300 font-mono">{book.download_count}</span>
                                            </div>
                                            <div className="p-2 rounded-xl bg-slate-950/50 border border-slate-800 flex items-center justify-between">
                                                <span className="text-[11px] text-slate-400 flex items-center gap-1">
                                                    <Eye size={12} className="text-sky-400" />
                                                    بازدید
                                                </span>
                                                <span className="font-bold text-sky-300 font-mono">{book.view_count}</span>
                                            </div>
                                        </div>

                                        {/* Card Actions */}
                                        <div className="flex items-center justify-between gap-2 pt-2">
                                            <div className="flex items-center gap-1.5">
                                                {book.is_published && (
                                                    <a
                                                        href={`/books/${book.slug}`}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="p-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition-colors"
                                                        title="مشاهده عمومی"
                                                    >
                                                        <ExternalLink size={15} />
                                                    </a>
                                                )}
                                                {book.pdf_file && (
                                                    <a
                                                        href={book.pdf_file}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        download
                                                        className="p-2 rounded-xl bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500 hover:text-white transition-colors"
                                                        title="دانلود فایل PDF"
                                                    >
                                                        <Download size={15} />
                                                    </a>
                                                )}
                                            </div>

                                            <div className="flex items-center gap-1.5">
                                                <Link
                                                    href={`/admin/books/${book.id}/edit`}
                                                    className="px-3 py-1.5 rounded-xl bg-brand-500/20 hover:bg-brand-500 text-brand-300 hover:text-white transition-colors text-xs font-bold flex items-center gap-1"
                                                >
                                                    <Edit3 size={14} />
                                                    <span>ویرایش</span>
                                                </Link>
                                                <button
                                                    onClick={() => setDeleteModalBook(book)}
                                                    className="p-1.5 rounded-xl bg-rose-500/20 text-rose-300 hover:bg-rose-500 hover:text-white transition-colors"
                                                    title="حذف کتاب"
                                                >
                                                    <Trash2 size={15} />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}

                {/* Pagination */}
                {books.links && books.links.length > 3 && (
                    <div className="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl bg-slate-900/60 backdrop-blur-xl border border-slate-800/80">
                        <div className="text-xs text-slate-400">
                            نمایش <span className="font-bold text-white font-mono">{books.from || 0}</span> تا{' '}
                            <span className="font-bold text-white font-mono">{books.to || 0}</span> از مجموع{' '}
                            <span className="font-bold text-white font-mono">{books.total || 0}</span> اثر
                        </div>

                        <div className="flex items-center gap-1.5">
                            {books.links.map((link, idx) => {
                                if (!link.url && link.label === '&laquo; Previous') {
                                    return (
                                        <span
                                            key={idx}
                                            className="px-3 py-1.5 rounded-xl bg-slate-900/50 text-slate-600 text-xs cursor-not-allowed border border-slate-800"
                                        >
                                            قبلی
                                        </span>
                                    );
                                }
                                if (!link.url && link.label === 'Next &raquo;') {
                                    return (
                                        <span
                                            key={idx}
                                            className="px-3 py-1.5 rounded-xl bg-slate-900/50 text-slate-600 text-xs cursor-not-allowed border border-slate-800"
                                        >
                                            بعدی
                                        </span>
                                    );
                                }

                                const isPrev = link.label.includes('Previous') || link.label.includes('&laquo;');
                                const isNext = link.label.includes('Next') || link.label.includes('&raquo;');
                                const label = isPrev ? 'قبلی' : isNext ? 'بعدی' : link.label;

                                return (
                                    <Link
                                        key={idx}
                                        href={link.url || '#'}
                                        preserveScroll
                                        className={`px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border ${
                                            link.active
                                                ? 'bg-brand-500 text-white border-brand-500 shadow-md shadow-brand-500/20'
                                                : 'bg-slate-950/60 hover:bg-slate-800 text-slate-300 border-slate-800'
                                        }`}
                                    >
                                        {label}
                                    </Link>
                                );
                            })}
                        </div>
                    </div>
                )}
            </div>

            {/* Single Delete Confirmation Modal */}
            {deleteModalBook && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in">
                    <div className="w-full max-w-md p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl space-y-4">
                        <div className="w-12 h-12 rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center border border-rose-500/30 mx-auto">
                            <AlertTriangle size={24} />
                        </div>
                        <div className="text-center space-y-1.5">
                            <h3 className="text-base font-bold text-white">حذف کتاب / مقاله</h3>
                            <p className="text-xs text-slate-400 leading-relaxed">
                                آیا از حذف کتاب <span className="font-bold text-slate-200">«{deleteModalBook.title}»</span> اطمینان دارید؟ تمام فایل‌های جلد و PDF مربوطه حذف خواهند شد.
                            </p>
                        </div>
                        <div className="flex items-center gap-3 pt-2">
                            <button
                                onClick={confirmSingleDelete}
                                className="flex-1 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold transition-all shadow-lg shadow-rose-500/25"
                            >
                                بله، حذف کن
                            </button>
                            <button
                                onClick={() => setDeleteModalBook(null)}
                                className="flex-1 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-all"
                            >
                                انصراف
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Bulk Delete Confirmation Modal */}
            {bulkDeleteModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in">
                    <div className="w-full max-w-md p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl space-y-4">
                        <div className="w-12 h-12 rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center border border-rose-500/30 mx-auto">
                            <AlertTriangle size={24} />
                        </div>
                        <div className="text-center space-y-1.5">
                            <h3 className="text-base font-bold text-white">حذف گروهی آثار</h3>
                            <p className="text-xs text-slate-400 leading-relaxed">
                                آیا از حذف همزمان <span className="font-bold text-rose-400">{selectedIds.length}</span> اثر انتخاب شده اطمینان دارید؟ این عملیات غیرقابل بازگشت است.
                            </p>
                        </div>
                        <div className="flex items-center gap-3 pt-2">
                            <button
                                onClick={confirmBulkDelete}
                                className="flex-1 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold transition-all shadow-lg shadow-rose-500/25"
                            >
                                حذف گروهی
                            </button>
                            <button
                                onClick={() => setBulkDeleteModalOpen(false)}
                                className="flex-1 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-all"
                            >
                                انصراف
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Book Detail / Quick Preview Modal */}
            {previewBook && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in">
                    <div className="w-full max-w-2xl p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
                        <div className="flex items-center justify-between pb-4 border-b border-slate-800">
                            <div className="flex items-center gap-3">
                                <div className="p-2.5 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    <BookOpen size={20} />
                                </div>
                                <div>
                                    <h3 className="text-base font-bold text-white font-display">{previewBook.title}</h3>
                                    <p className="text-xs text-slate-400">نویسنده: {previewBook.author || 'نامشخص'}</p>
                                </div>
                            </div>
                            <button
                                onClick={() => setPreviewBook(null)}
                                className="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800"
                            >
                                <X size={20} />
                            </button>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            {/* Cover preview */}
                            <div className="aspect-[3/4] rounded-2xl bg-slate-950 overflow-hidden border border-slate-800 shadow-lg flex items-center justify-center">
                                {previewBook.cover_image ? (
                                    <img src={previewBook.cover_image} alt={previewBook.title} className="w-full h-full object-cover" />
                                ) : (
                                    <BookMarked size={48} className="text-slate-600" />
                                )}
                            </div>

                            {/* Info */}
                            <div className="sm:col-span-2 space-y-4">
                                <div className="grid grid-cols-2 gap-3 text-xs">
                                    <div className="p-3 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                        <span className="text-slate-400">دسته‌بندی</span>
                                        <div className="font-bold text-white">{previewBook.category_name}</div>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                        <span className="text-slate-400">وضعیت</span>
                                        <div className={`font-bold ${previewBook.is_published ? 'text-emerald-400' : 'text-amber-400'}`}>
                                            {previewBook.is_published ? 'منتشر شده' : 'پیش‌نویس'}
                                        </div>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                        <span className="text-slate-400">تعداد دانلود</span>
                                        <div className="font-bold text-indigo-300 font-mono">{previewBook.download_count}</div>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                        <span className="text-slate-400">تعداد بازدید</span>
                                        <div className="font-bold text-sky-300 font-mono">{previewBook.view_count}</div>
                                    </div>
                                </div>

                                {/* Description */}
                                <div className="p-4 rounded-2xl bg-slate-950/40 border border-slate-800 space-y-2">
                                    <span className="text-xs font-bold text-slate-300">توضیحات و معرفی:</span>
                                    <p className="text-xs text-slate-400 leading-relaxed whitespace-pre-line max-h-40 overflow-y-auto">
                                        {previewBook.description || 'توضیحاتی برای این اثر وارد نشده است.'}
                                    </p>
                                </div>

                                {/* Action bar */}
                                <div className="flex flex-wrap items-center gap-2 pt-2">
                                    {previewBook.pdf_file && (
                                        <a
                                            href={previewBook.pdf_file}
                                            target="_blank"
                                            rel="noreferrer"
                                            download
                                            className="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold flex items-center gap-1.5 transition-all shadow-md shadow-indigo-600/20"
                                        >
                                            <Download size={15} />
                                            <span>دانلود فایل PDF</span>
                                        </a>
                                    )}
                                    <Link
                                        href={`/admin/books/${previewBook.id}/edit`}
                                        className="px-4 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold flex items-center gap-1.5 transition-all"
                                    >
                                        <Edit3 size={15} />
                                        <span>ویرایش کامل</span>
                                    </Link>
                                    {previewBook.is_published && (
                                        <a
                                            href={`/books/${previewBook.slug}`}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold flex items-center gap-1.5 transition-all"
                                        >
                                            <ExternalLink size={15} />
                                            <span>مشاهده در سایت</span>
                                        </a>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AdminLayout>
    );
}
