import React, { useState, useRef } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    Folder,
    FolderPlus,
    FolderOpen,
    File,
    FileText,
    FileArchive,
    FileCode,
    FileSpreadsheet,
    UploadCloud,
    Trash2,
    Copy,
    Check,
    ExternalLink,
    Download,
    Eye,
    Search,
    LayoutGrid,
    List,
    RefreshCw,
    SlidersHorizontal,
    HardDrive,
    Sparkles,
    X,
    AlertTriangle,
    CheckCircle2,
    ChevronLeft,
    Image as ImageIcon,
    Video as VideoIcon,
    Music,
    CornerUpRight,
    ArrowUpDown,
    Filter,
} from 'lucide-react';

export default function Index({
    files = [],
    folders = [],
    breadcrumbs = [],
    currentPath = '',
    stats = {},
    filters = {},
}) {
    // State management
    const [search, setSearch] = useState(filters.search || '');
    const [typeFilter, setTypeFilter] = useState(filters.type || 'all');
    const [sortBy, setSortBy] = useState(filters.sort || 'date');
    const [sortDir, setSortDir] = useState(filters.order || 'desc');
    const [viewMode, setViewMode] = useState(filters.mode || 'folder'); // 'folder' | 'all'
    const [displayLayout, setDisplayLayout] = useState('grid'); // 'grid' | 'table'

    // Selection for bulk actions
    const [selectedPaths, setSelectedPaths] = useState([]);

    // Modals
    const [isUploadOpen, setIsUploadOpen] = useState(false);
    const [isCreateFolderOpen, setIsCreateFolderOpen] = useState(false);
    const [newFolderName, setNewFolderName] = useState('');
    const [previewFile, setPreviewFile] = useState(null);
    const [deleteItem, setDeleteItem] = useState(null); // { name, path, isFolder }
    const [isBulkDeleteOpen, setIsBulkDeleteOpen] = useState(false);

    // Upload state
    const [uploadFiles, setUploadFiles] = useState([]);
    const [isDragging, setIsDragging] = useState(false);
    const [uploadProgress, setUploadProgress] = useState(0);
    const [isUploading, setIsUploading] = useState(false);
    const fileInputRef = useRef(null);

    // Copy to clipboard state
    const [copiedPath, setCopiedPath] = useState(null);

    // Apply filters and navigation
    const navigateTo = (overrides = {}) => {
        const query = {
            path: currentPath,
            search,
            type: typeFilter,
            sort: sortBy,
            order: sortDir,
            mode: viewMode,
            ...overrides,
        };

        // Remove empty values
        Object.keys(query).forEach((key) => {
            if (query[key] === '' || query[key] === null || query[key] === undefined) {
                delete query[key];
            }
        });

        router.get('/admin/file-manager', query, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        navigateTo({ search });
    };

    const handleFolderClick = (folderPath) => {
        setSelectedPaths([]);
        navigateTo({ path: folderPath, search: '' });
    };

    const handleUpLevel = () => {
        if (!currentPath) return;
        const parts = currentPath.split('/');
        parts.pop();
        const parentPath = parts.join('/');
        setSelectedPaths([]);
        navigateTo({ path: parentPath });
    };

    // Selection handlers
    const toggleSelect = (path) => {
        setSelectedPaths((prev) =>
            prev.includes(path) ? prev.filter((p) => p !== path) : [...prev, path]
        );
    };

    const selectAll = () => {
        if (selectedPaths.length === files.length) {
            setSelectedPaths([]);
        } else {
            setSelectedPaths(files.map((f) => f.path));
        }
    };

    // Copy URL
    const handleCopyUrl = (url, path) => {
        navigator.clipboard.writeText(window.location.origin + url);
        setCopiedPath(path);
        setTimeout(() => setCopiedPath(null), 2500);
    };

    // Create Folder
    const handleCreateFolder = (e) => {
        e.preventDefault();
        if (!newFolderName.trim()) return;

        router.post(
            '/admin/file-manager/create-folder',
            {
                folder_name: newFolderName,
                target_path: currentPath,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsCreateFolderOpen(false);
                    setNewFolderName('');
                },
            }
        );
    };

    // Upload
    const handleFileDrop = (e) => {
        e.preventDefault();
        setIsDragging(false);
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            setUploadFiles(Array.from(e.dataTransfer.files));
        }
    };

    const handleUploadSubmit = (e) => {
        e.preventDefault();
        if (uploadFiles.length === 0) return;

        setIsUploading(true);
        const formData = new FormData();
        uploadFiles.forEach((file) => {
            formData.append('files[]', file);
        });
        formData.append('target_path', currentPath);

        router.post('/admin/file-manager/upload', formData, {
            forceFormData: true,
            preserveScroll: true,
            onProgress: (progress) => {
                setUploadProgress(progress.percentage || 50);
            },
            onSuccess: () => {
                setIsUploadOpen(false);
                setUploadFiles([]);
                setUploadProgress(0);
                setIsUploading(false);
            },
            onError: () => {
                setIsUploading(false);
            },
        });
    };

    // Delete single
    const confirmDelete = () => {
        if (!deleteItem) return;

        router.post(
            '/admin/file-manager/delete',
            { path: deleteItem.path },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setDeleteItem(null);
                    if (previewFile?.path === deleteItem.path) {
                        setPreviewFile(null);
                    }
                },
            }
        );
    };

    // Bulk Delete
    const confirmBulkDelete = () => {
        if (selectedPaths.length === 0) return;

        router.post(
            '/admin/file-manager/bulk-delete',
            { paths: selectedPaths },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setSelectedPaths([]);
                    setIsBulkDeleteOpen(false);
                },
            }
        );
    };

    // File icon helper
    const getFileIcon = (file, size = 24) => {
        switch (file.type) {
            case 'image':
                return <ImageIcon size={size} className="text-emerald-400" />;
            case 'document':
                if (file.extension === 'pdf') {
                    return <FileText size={size} className="text-rose-400" />;
                }
                if (['xls', 'xlsx', 'csv'].includes(file.extension)) {
                    return <FileSpreadsheet size={size} className="text-teal-400" />;
                }
                return <FileText size={size} className="text-blue-400" />;
            case 'video':
                return <VideoIcon size={size} className="text-purple-400" />;
            case 'audio':
                return <Music size={size} className="text-amber-400" />;
            case 'archive':
                return <FileArchive size={size} className="text-amber-500" />;
            default:
                if (['json', 'js', 'html', 'css', 'php', 'ts'].includes(file.extension)) {
                    return <FileCode size={size} className="text-cyan-400" />;
                }
                return <File size={size} className="text-slate-400" />;
        }
    };

    return (
        <AdminLayout title="مدیریت فایل‌ها - پنل مدیریت ادورا">
            <div className="p-4 sm:p-6 lg:p-8 space-y-6">
                {/* ─── Top Banner & Stats Overview ─── */}
                <div className="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-900/90 to-slate-800/80 border border-slate-800 p-6 shadow-2xl">
                    <div className="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-brand-500/10 rounded-full blur-3xl pointer-events-none" />
                    <div className="absolute bottom-0 left-0 -mb-8 -ml-8 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none" />

                    <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div className="space-y-2">
                            <div className="flex items-center gap-3">
                                <div className="p-3 rounded-2xl bg-brand-500/20 text-brand-400 border border-brand-500/30">
                                    <HardDrive size={26} />
                                </div>
                                <div>
                                    <h1 className="text-2xl sm:text-3xl font-black text-white tracking-tight">
                                        مدیریت فایل‌ها و رسانه‌ها
                                    </h1>
                                    <p className="text-sm text-slate-400">
                                        مرور، آپلود، مدیریت و حذف فایل‌های ذخیره‌شده در سرور
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Top Action Buttons */}
                        <div className="flex flex-wrap items-center gap-3">
                            <button
                                onClick={() => {
                                    setNewFolderName('');
                                    setIsCreateFolderOpen(true);
                                }}
                                className="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 hover:border-slate-600 font-medium text-sm transition-all flex items-center gap-2 shadow-sm"
                            >
                                <FolderPlus size={18} className="text-amber-400" />
                                <span>پوشه جدید</span>
                            </button>

                            <button
                                onClick={() => {
                                    setUploadFiles([]);
                                    setUploadProgress(0);
                                    setIsUploadOpen(true);
                                }}
                                className="px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-indigo-600 hover:from-brand-600 hover:to-indigo-700 text-white font-medium text-sm transition-all shadow-lg shadow-brand-500/25 flex items-center gap-2 group"
                            >
                                <UploadCloud size={18} className="group-hover:-translate-y-0.5 transition-transform" />
                                <span>آپلود فایل</span>
                            </button>
                        </div>
                    </div>

                    {/* Quick Stats Grid */}
                    <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 pt-6 mt-6 border-t border-slate-800/80">
                        <div className="bg-slate-950/50 rounded-2xl p-3.5 border border-slate-800/60">
                            <span className="text-xs text-slate-400 block mb-1">کل فایل‌ها</span>
                            <span className="text-lg font-bold text-white">
                                {(stats.total_files || 0).toLocaleString('fa-IR')}
                            </span>
                        </div>

                        <div className="bg-slate-950/50 rounded-2xl p-3.5 border border-slate-800/60">
                            <span className="text-xs text-slate-400 block mb-1">فضای اشغال‌شده</span>
                            <span className="text-lg font-bold text-brand-400">
                                {stats.total_size_formatted || '0 B'}
                            </span>
                        </div>

                        <div className="bg-slate-950/50 rounded-2xl p-3.5 border border-slate-800/60">
                            <span className="text-xs text-slate-400 block mb-1">تصاویر</span>
                            <span className="text-lg font-bold text-emerald-400">
                                {(stats.categories?.image || 0).toLocaleString('fa-IR')}
                            </span>
                        </div>

                        <div className="bg-slate-950/50 rounded-2xl p-3.5 border border-slate-800/60">
                            <span className="text-xs text-slate-400 block mb-1">اسناد و PDF</span>
                            <span className="text-lg font-bold text-blue-400">
                                {(stats.categories?.document || 0).toLocaleString('fa-IR')}
                            </span>
                        </div>

                        <div className="bg-slate-950/50 rounded-2xl p-3.5 border border-slate-800/60">
                            <span className="text-xs text-slate-400 block mb-1">ویدیو و صوت</span>
                            <span className="text-lg font-bold text-purple-400">
                                {((stats.categories?.video || 0) + (stats.categories?.audio || 0)).toLocaleString('fa-IR')}
                            </span>
                        </div>

                        <div className="bg-slate-950/50 rounded-2xl p-3.5 border border-slate-800/60">
                            <span className="text-xs text-slate-400 block mb-1">فایل‌های فشرده</span>
                            <span className="text-lg font-bold text-amber-400">
                                {(stats.categories?.archive || 0).toLocaleString('fa-IR')}
                            </span>
                        </div>
                    </div>
                </div>

                {/* ─── Breadcrumb Navigation & Mode Toggle ─── */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/60 p-4 rounded-2xl border border-slate-800/80 backdrop-blur-md">
                    {/* Breadcrumbs */}
                    <div className="flex items-center gap-1.5 overflow-x-auto py-1 scrollbar-thin text-sm">
                        {currentPath && (
                            <button
                                onClick={handleUpLevel}
                                title="یک مرحله به بالا"
                                className="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors ml-1"
                            >
                                <CornerUpRight size={16} />
                            </button>
                        )}

                        {breadcrumbs.map((crumb, idx) => {
                            const isLast = idx === breadcrumbs.length - 1;
                            return (
                                <React.Fragment key={crumb.path || 'root'}>
                                    {idx > 0 && <span className="text-slate-600 mx-1">/</span>}
                                    <button
                                        onClick={() => handleFolderClick(crumb.path)}
                                        className={`px-2.5 py-1 rounded-lg text-xs sm:text-sm font-medium transition-all ${
                                            isLast
                                                ? 'bg-brand-500/20 text-brand-300 border border-brand-500/30'
                                                : 'text-slate-400 hover:text-white hover:bg-slate-800'
                                        }`}
                                    >
                                        {crumb.name}
                                    </button>
                                </React.Fragment>
                            );
                        })}
                    </div>

                    {/* View Modes & Display Switcher */}
                    <div className="flex items-center gap-2 self-end md:self-auto">
                        {/* Folder browsing vs All files */}
                        <div className="flex items-center bg-slate-950/80 rounded-xl p-1 border border-slate-800 text-xs">
                            <button
                                onClick={() => {
                                    setViewMode('folder');
                                    navigateTo({ mode: 'folder' });
                                }}
                                className={`px-3 py-1.5 rounded-lg font-medium transition-all ${
                                    viewMode === 'folder'
                                        ? 'bg-slate-800 text-white shadow-sm'
                                        : 'text-slate-400 hover:text-slate-200'
                                }`}
                            >
                                پوشه‌بندی
                            </button>
                            <button
                                onClick={() => {
                                    setViewMode('all');
                                    navigateTo({ mode: 'all' });
                                }}
                                className={`px-3 py-1.5 rounded-lg font-medium transition-all ${
                                    viewMode === 'all'
                                        ? 'bg-slate-800 text-white shadow-sm'
                                        : 'text-slate-400 hover:text-slate-200'
                                }`}
                            >
                                کل فایل‌های سرور
                            </button>
                        </div>

                        {/* Grid / List Switcher */}
                        <div className="flex items-center bg-slate-950/80 rounded-xl p-1 border border-slate-800 text-slate-400">
                            <button
                                onClick={() => setDisplayLayout('grid')}
                                className={`p-1.5 rounded-lg transition-colors ${
                                    displayLayout === 'grid' ? 'bg-slate-800 text-white' : 'hover:text-white'
                                }`}
                                title="نمایش شبکه‌ای"
                            >
                                <LayoutGrid size={16} />
                            </button>
                            <button
                                onClick={() => setDisplayLayout('table')}
                                className={`p-1.5 rounded-lg transition-colors ${
                                    displayLayout === 'table' ? 'bg-slate-800 text-white' : 'hover:text-white'
                                }`}
                                title="نمایش جدولی"
                            >
                                <List size={16} />
                            </button>
                        </div>

                        {/* Reload */}
                        <button
                            onClick={() => navigateTo()}
                            title="تازه‌سازی"
                            className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors"
                        >
                            <RefreshCw size={16} />
                        </button>
                    </div>
                </div>

                {/* ─── Search & Category Filters Bar ─── */}
                <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-900/40 p-4 rounded-2xl border border-slate-800/60">
                    {/* Search Form */}
                    <form onSubmit={handleSearchSubmit} className="relative flex-1 max-w-md">
                        <Search className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="جستجو بر اساس نام فایل..."
                            className="w-full pr-10 pl-10 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-brand-500 transition-colors"
                        />
                        {search && (
                            <button
                                type="button"
                                onClick={() => {
                                    setSearch('');
                                    navigateTo({ search: '' });
                                }}
                                className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white"
                            >
                                <X size={15} />
                            </button>
                        )}
                    </form>

                    {/* Filter Pills */}
                    <div className="flex flex-wrap items-center gap-1.5 text-xs">
                        {[
                            { key: 'all', label: 'همه فایل‌ها' },
                            { key: 'image', label: 'تصاویر' },
                            { key: 'document', label: 'اسناد و PDF' },
                            { key: 'video', label: 'ویدیو' },
                            { key: 'audio', label: 'صوت' },
                            { key: 'archive', label: 'فشرده' },
                        ].map((cat) => (
                            <button
                                key={cat.key}
                                onClick={() => {
                                    setTypeFilter(cat.key);
                                    navigateTo({ type: cat.key });
                                }}
                                className={`px-3 py-1.5 rounded-xl font-medium transition-all ${
                                    typeFilter === cat.key
                                        ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20'
                                        : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300 border border-slate-700/60'
                                }`}
                            >
                                {cat.label}
                            </button>
                        ))}
                    </div>

                    {/* Sort Dropdown */}
                    <div className="flex items-center gap-2 self-end lg:self-auto">
                        <select
                            value={sortBy}
                            onChange={(e) => {
                                setSortBy(e.target.value);
                                navigateTo({ sort: e.target.value });
                            }}
                            className="py-1.5 px-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 text-xs focus:outline-none focus:border-brand-500"
                        >
                            <option value="date">مرتب‌سازی: تاریخ تغییر</option>
                            <option value="name">مرتب‌سازی: نام فایل</option>
                            <option value="size">مرتب‌سازی: حجم فایل</option>
                            <option value="type">مرتب‌سازی: پسوند فایل</option>
                        </select>

                        <button
                            onClick={() => {
                                const newOrder = sortDir === 'asc' ? 'desc' : 'asc';
                                setSortDir(newOrder);
                                navigateTo({ order: newOrder });
                            }}
                            title={sortDir === 'asc' ? 'صعودی' : 'نزولی'}
                            className="p-1.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 hover:text-white"
                        >
                            <ArrowUpDown size={15} />
                        </button>
                    </div>
                </div>

                {/* ─── Bulk Action Bar (When items are selected) ─── */}
                {selectedPaths.length > 0 && (
                    <div className="flex items-center justify-between p-3.5 rounded-2xl bg-brand-500/10 border border-brand-500/30 text-brand-300 text-sm animate-in fade-in duration-200">
                        <div className="flex items-center gap-3">
                            <CheckCircle2 size={18} className="text-brand-400" />
                            <span>
                                <strong>{selectedPaths.length}</strong> فایل برای عملیات همگانی انتخاب شده است.
                            </span>
                        </div>

                        <div className="flex items-center gap-2">
                            <button
                                onClick={() => setSelectedPaths([])}
                                className="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium"
                            >
                                لغو انتخاب
                            </button>
                            <button
                                onClick={() => setIsBulkDeleteOpen(true)}
                                className="px-3 py-1 rounded-lg bg-rose-600 hover:bg-rose-500 text-white text-xs font-medium flex items-center gap-1.5 shadow-sm"
                            >
                                <Trash2 size={14} />
                                <span>حذف موارد انتخاب‌شده</span>
                            </button>
                        </div>
                    </div>
                )}

                {/* ─── Folders Section (Only in folder mode and no search) ─── */}
                {viewMode === 'folder' && !search && folders.length > 0 && (
                    <div className="space-y-3">
                        <h2 className="text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <Folder size={16} className="text-amber-400" />
                            <span>پوشه‌ها ({folders.length})</span>
                        </h2>

                        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                            {folders.map((folder) => (
                                <div
                                    key={folder.path}
                                    className="group relative flex items-center justify-between p-3 rounded-2xl bg-slate-900/80 hover:bg-slate-800 border border-slate-800 hover:border-amber-500/40 transition-all cursor-pointer shadow-sm"
                                    onClick={() => handleFolderClick(folder.path)}
                                >
                                    <div className="flex items-center gap-2.5 min-w-0">
                                        <div className="p-2 rounded-xl bg-amber-500/10 text-amber-400 group-hover:scale-105 transition-transform">
                                            <FolderOpen size={20} />
                                        </div>
                                        <div className="min-w-0">
                                            <p className="text-sm font-semibold text-slate-200 truncate group-hover:text-amber-300 transition-colors">
                                                {folder.name}
                                            </p>
                                            <span className="text-[11px] text-slate-500">
                                                {folder.items_count} آیتم
                                            </span>
                                        </div>
                                    </div>

                                    {/* Delete folder button */}
                                    <button
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            setDeleteItem({
                                                name: folder.name,
                                                path: folder.path,
                                                isFolder: true,
                                            });
                                        }}
                                        className="opacity-0 group-hover:opacity-100 p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-all"
                                        title="حذف پوشه"
                                    >
                                        <Trash2 size={14} />
                                    </button>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* ─── Files Section ─── */}
                <div className="space-y-3">
                    <div className="flex items-center justify-between">
                        <h2 className="text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <File size={16} className="text-brand-400" />
                            <span>
                                فایل‌ها ({files.length})
                                {currentPath ? ` در ${currentPath}` : ''}
                            </span>
                        </h2>

                        {files.length > 0 && (
                            <button
                                onClick={selectAll}
                                className="text-xs text-brand-400 hover:text-brand-300 font-medium"
                            >
                                {selectedPaths.length === files.length ? 'عدم انتخاب همه' : 'انتخاب همه فایل‌ها'}
                            </button>
                        )}
                    </div>

                    {files.length === 0 ? (
                        /* Empty State */
                        <div className="flex flex-col items-center justify-center p-12 rounded-3xl bg-slate-900/40 border border-slate-800/80 text-center space-y-4">
                            <div className="w-16 h-16 rounded-full bg-slate-800/80 flex items-center justify-center text-slate-500">
                                <HardDrive size={32} />
                            </div>
                            <div className="space-y-1">
                                <h3 className="text-base font-bold text-slate-200">
                                    هیچ فایلی در این بخش یافت نشد!
                                </h3>
                                <p className="text-xs text-slate-400 max-w-sm">
                                    {search
                                        ? 'هیچ فایلی با عبارت جستجوی شما مطابقت ندارد.'
                                        : 'این پوشه خالی است. می‌توانید با دکمه زیر فایل جدید آپلود کنید.'}
                                </p>
                            </div>
                            <button
                                onClick={() => {
                                    setUploadFiles([]);
                                    setUploadProgress(0);
                                    setIsUploadOpen(true);
                                }}
                                className="px-4 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-medium text-xs flex items-center gap-2 shadow-md transition-colors"
                            >
                                <UploadCloud size={16} />
                                <span>آپلود فایل جدید</span>
                            </button>
                        </div>
                    ) : displayLayout === 'grid' ? (
                        /* ─── Grid View ─── */
                        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            {files.map((file) => {
                                const isSelected = selectedPaths.includes(file.path);
                                return (
                                    <div
                                        key={file.path}
                                        className={`group relative flex flex-col rounded-2xl bg-slate-900/90 border transition-all overflow-hidden shadow-sm ${
                                            isSelected
                                                ? 'border-brand-500 ring-2 ring-brand-500/30'
                                                : 'border-slate-800 hover:border-slate-700 hover:shadow-lg'
                                        }`}
                                    >
                                        {/* Selection Checkbox */}
                                        <div className="absolute top-2 right-2 z-20">
                                            <input
                                                type="checkbox"
                                                checked={isSelected}
                                                onChange={() => toggleSelect(file.path)}
                                                className="w-4 h-4 rounded border-slate-700 bg-slate-900/80 text-brand-500 focus:ring-brand-500/20 cursor-pointer"
                                            />
                                        </div>

                                        {/* Thumbnail / Preview Area */}
                                        <div
                                            className="relative aspect-square w-full bg-slate-950 flex items-center justify-center overflow-hidden cursor-pointer group-hover:opacity-95"
                                            onClick={() => setPreviewFile(file)}
                                        >
                                            {file.is_image ? (
                                                <img
                                                    src={file.url}
                                                    alt={file.name}
                                                    loading="lazy"
                                                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                />
                                            ) : (
                                                <div className="p-4 flex flex-col items-center gap-2">
                                                    {getFileIcon(file, 40)}
                                                    <span className="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 py-0.5 rounded bg-slate-900 border border-slate-800">
                                                        {file.extension || 'FILE'}
                                                    </span>
                                                </div>
                                            )}

                                            {/* Hover Quick Actions Overlay */}
                                            <div className="absolute inset-0 bg-slate-950/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 z-10">
                                                <button
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        setPreviewFile(file);
                                                    }}
                                                    className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200"
                                                    title="مشاهده"
                                                >
                                                    <Eye size={15} />
                                                </button>
                                                <button
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        handleCopyUrl(file.url, file.path);
                                                    }}
                                                    className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200"
                                                    title="کپی لینک"
                                                >
                                                    {copiedPath === file.path ? (
                                                        <Check size={15} className="text-emerald-400" />
                                                    ) : (
                                                        <Copy size={15} />
                                                    )}
                                                </button>
                                                <a
                                                    href={file.url}
                                                    download={file.name}
                                                    onClick={(e) => e.stopPropagation()}
                                                    className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200"
                                                    title="دانلود"
                                                >
                                                    <Download size={15} />
                                                </a>
                                                <button
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        setDeleteItem({
                                                            name: file.name,
                                                            path: file.path,
                                                            isFolder: false,
                                                        });
                                                    }}
                                                    className="p-2 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300"
                                                    title="حذف"
                                                >
                                                    <Trash2 size={15} />
                                                </button>
                                            </div>
                                        </div>

                                        {/* File Metadata */}
                                        <div className="p-3 flex-1 flex flex-col justify-between space-y-1">
                                            <p
                                                title={file.name}
                                                className="text-xs font-semibold text-slate-200 truncate cursor-pointer hover:text-brand-300"
                                                onClick={() => setPreviewFile(file)}
                                            >
                                                {file.name}
                                            </p>
                                            <div className="flex items-center justify-between text-[11px] text-slate-400">
                                                <span>{file.size_formatted}</span>
                                                <span title={file.last_modified_date}>{file.last_modified}</span>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    ) : (
                        /* ─── Table / List View ─── */
                        <div className="rounded-2xl border border-slate-800 overflow-hidden bg-slate-900/60 backdrop-blur-md">
                            <div className="overflow-x-auto">
                                <table className="w-full text-right text-xs">
                                    <thead className="bg-slate-950/80 text-slate-400 border-b border-slate-800 uppercase font-medium">
                                        <tr>
                                            <th className="p-3 w-10 text-center">
                                                <input
                                                    type="checkbox"
                                                    checked={selectedPaths.length > 0 && selectedPaths.length === files.length}
                                                    onChange={selectAll}
                                                    className="w-4 h-4 rounded border-slate-700 bg-slate-900 text-brand-500 focus:ring-brand-500/20 cursor-pointer"
                                                />
                                            </th>
                                            <th className="p-3">نام فایل</th>
                                            <th className="p-3">نوع / پسوند</th>
                                            <th className="p-3">پوشه</th>
                                            <th className="p-3">حجم</th>
                                            <th className="p-3">تاریخ تغییر</th>
                                            <th className="p-3 text-center">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-800/60">
                                        {files.map((file) => {
                                            const isSelected = selectedPaths.includes(file.path);
                                            return (
                                                <tr
                                                    key={file.path}
                                                    className={`hover:bg-slate-800/40 transition-colors ${
                                                        isSelected ? 'bg-brand-500/5' : ''
                                                    }`}
                                                >
                                                    <td className="p-3 text-center">
                                                        <input
                                                            type="checkbox"
                                                            checked={isSelected}
                                                            onChange={() => toggleSelect(file.path)}
                                                            className="w-4 h-4 rounded border-slate-700 bg-slate-900 text-brand-500 focus:ring-brand-500/20 cursor-pointer"
                                                        />
                                                    </td>
                                                    <td className="p-3">
                                                        <div
                                                            className="flex items-center gap-2.5 cursor-pointer max-w-sm truncate"
                                                            onClick={() => setPreviewFile(file)}
                                                        >
                                                            {getFileIcon(file, 20)}
                                                            <span className="font-semibold text-slate-200 hover:text-brand-300 truncate">
                                                                {file.name}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td className="p-3">
                                                        <span className="px-2 py-0.5 rounded-md bg-slate-800 border border-slate-700 text-slate-300 font-mono text-[11px] uppercase">
                                                            {file.extension || '-'}
                                                        </span>
                                                    </td>
                                                    <td className="p-3 text-slate-400 font-mono text-[11px]">
                                                        {file.folder || 'Root'}
                                                    </td>
                                                    <td className="p-3 text-slate-300 font-medium">
                                                        {file.size_formatted}
                                                    </td>
                                                    <td className="p-3 text-slate-400" title={file.last_modified_date}>
                                                        {file.last_modified}
                                                    </td>
                                                    <td className="p-3">
                                                        <div className="flex items-center justify-center gap-1.5">
                                                            <button
                                                                onClick={() => setPreviewFile(file)}
                                                                className="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-slate-200"
                                                                title="پیش‌نمایش"
                                                            >
                                                                <Eye size={15} />
                                                            </button>
                                                            <button
                                                                onClick={() => handleCopyUrl(file.url, file.path)}
                                                                className="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-slate-200"
                                                                title="کپی آدرس فایل"
                                                            >
                                                                {copiedPath === file.path ? (
                                                                    <Check size={15} className="text-emerald-400" />
                                                                ) : (
                                                                    <Copy size={15} />
                                                                )}
                                                            </button>
                                                            <a
                                                                href={file.url}
                                                                download={file.name}
                                                                className="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-slate-200"
                                                                title="دانلود"
                                                            >
                                                                <Download size={15} />
                                                            </a>
                                                            <button
                                                                onClick={() =>
                                                                    setDeleteItem({
                                                                        name: file.name,
                                                                        path: file.path,
                                                                        isFolder: false,
                                                                    })
                                                                }
                                                                className="p-1.5 rounded-lg hover:bg-rose-500/20 text-slate-400 hover:text-rose-400"
                                                                title="حذف"
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
                    )}
                </div>

                {/* ─── Upload Files Modal ─── */}
                {isUploadOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-3xl p-6 shadow-2xl space-y-4">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-800">
                                <div className="flex items-center gap-2.5">
                                    <div className="p-2 rounded-xl bg-brand-500/20 text-brand-400">
                                        <UploadCloud size={20} />
                                    </div>
                                    <div>
                                        <h3 className="text-base font-bold text-white">آپلود فایل جدید</h3>
                                        <p className="text-xs text-slate-400">
                                            محل ذخیره: {currentPath ? `/${currentPath}` : 'پوشه اصلی (Root)'}
                                        </p>
                                    </div>
                                </div>
                                <button
                                    onClick={() => setIsUploadOpen(false)}
                                    className="text-slate-400 hover:text-white"
                                >
                                    <X size={18} />
                                </button>
                            </div>

                            <form onSubmit={handleUploadSubmit} className="space-y-4">
                                {/* Drag & Drop Area */}
                                <div
                                    onDragOver={(e) => {
                                        e.preventDefault();
                                        setIsDragging(true);
                                    }}
                                    onDragLeave={() => setIsDragging(false)}
                                    onDrop={handleFileDrop}
                                    onClick={() => fileInputRef.current?.click()}
                                    className={`border-2 border-dashed rounded-2xl p-8 text-center cursor-pointer transition-all ${
                                        isDragging
                                            ? 'border-brand-500 bg-brand-500/10'
                                            : 'border-slate-800 hover:border-slate-700 bg-slate-950/50'
                                    }`}
                                >
                                    <input
                                        ref={fileInputRef}
                                        type="file"
                                        multiple
                                        className="hidden"
                                        onChange={(e) => {
                                            if (e.target.files) {
                                                setUploadFiles(Array.from(e.target.files));
                                            }
                                        }}
                                    />
                                    <div className="flex flex-col items-center gap-2">
                                        <div className="p-3 rounded-full bg-slate-800 text-brand-400">
                                            <UploadCloud size={28} />
                                        </div>
                                        <p className="text-sm font-semibold text-slate-200">
                                            فایل‌ها را به اینجا بکشید یا برای انتخاب کلیک کنید
                                        </p>
                                        <span className="text-xs text-slate-500">
                                            حداکثر حجم مجاز هر فایل: ۱۰۰ مگابایت
                                        </span>
                                    </div>
                                </div>

                                {/* Selected files list */}
                                {uploadFiles.length > 0 && (
                                    <div className="space-y-2 max-h-40 overflow-y-auto pr-1">
                                        <p className="text-xs font-semibold text-slate-300">
                                            فایل‌های انتخاب‌شده ({uploadFiles.length}):
                                        </p>
                                        {uploadFiles.map((file, i) => (
                                            <div
                                                key={i}
                                                className="flex items-center justify-between p-2 rounded-xl bg-slate-950 text-xs border border-slate-800 text-slate-300"
                                            >
                                                <span className="truncate max-w-[280px]">{file.name}</span>
                                                <span className="text-slate-500 text-[11px]">
                                                    {(file.size / 1024).toFixed(1)} KB
                                                </span>
                                            </div>
                                        ))}
                                    </div>
                                )}

                                {/* Progress */}
                                {isUploading && (
                                    <div className="space-y-1">
                                        <div className="flex justify-between text-xs text-slate-400">
                                            <span>در حال ارسال...</span>
                                            <span>{uploadProgress}%</span>
                                        </div>
                                        <div className="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                                            <div
                                                className="h-full bg-brand-500 transition-all duration-300"
                                                style={{ width: `${uploadProgress}%` }}
                                            />
                                        </div>
                                    </div>
                                )}

                                <div className="flex items-center justify-end gap-2 pt-2">
                                    <button
                                        type="button"
                                        onClick={() => setIsUploadOpen(false)}
                                        className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium"
                                    >
                                        انصراف
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={uploadFiles.length === 0 || isUploading}
                                        className="px-5 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 disabled:opacity-50 text-white text-xs font-medium flex items-center gap-1.5 shadow-md shadow-brand-500/20"
                                    >
                                        <UploadCloud size={15} />
                                        <span>شروع آپلود</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* ─── Create Folder Modal ─── */}
                {isCreateFolderOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-800">
                                <div className="flex items-center gap-2.5">
                                    <div className="p-2 rounded-xl bg-amber-500/20 text-amber-400">
                                        <FolderPlus size={20} />
                                    </div>
                                    <div>
                                        <h3 className="text-base font-bold text-white">ایجاد پوشه جدید</h3>
                                        <p className="text-xs text-slate-400">
                                            در: {currentPath ? `/${currentPath}` : 'پوشه اصلی (Root)'}
                                        </p>
                                    </div>
                                </div>
                                <button
                                    onClick={() => setIsCreateFolderOpen(false)}
                                    className="text-slate-400 hover:text-white"
                                >
                                    <X size={18} />
                                </button>
                            </div>

                            <form onSubmit={handleCreateFolder} className="space-y-4">
                                <div>
                                    <label className="block text-xs font-semibold text-slate-300 mb-1.5">
                                        نام پوشه
                                    </label>
                                    <input
                                        type="text"
                                        autoFocus
                                        value={newFolderName}
                                        onChange={(e) => setNewFolderName(e.target.value)}
                                        placeholder="مثال: course-thumbnails یا certificates"
                                        className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-sm focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div className="flex items-center justify-end gap-2 pt-2">
                                    <button
                                        type="button"
                                        onClick={() => setIsCreateFolderOpen(false)}
                                        className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium"
                                    >
                                        انصراف
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={!newFolderName.trim()}
                                        className="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 disabled:opacity-50 text-white text-xs font-medium flex items-center gap-1.5 shadow-md"
                                    >
                                        <FolderPlus size={15} />
                                        <span>ایجاد پوشه</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* ─── File Preview / Details Modal ─── */}
                {previewFile && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-3xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
                            {/* Modal Header */}
                            <div className="flex items-center justify-between p-4 border-b border-slate-800">
                                <div className="flex items-center gap-2.5 min-w-0">
                                    {getFileIcon(previewFile, 22)}
                                    <span className="text-sm font-bold text-slate-100 truncate">
                                        {previewFile.name}
                                    </span>
                                </div>
                                <button
                                    onClick={() => setPreviewFile(null)}
                                    className="text-slate-400 hover:text-white"
                                >
                                    <X size={18} />
                                </button>
                            </div>

                            {/* Preview Body */}
                            <div className="p-6 overflow-y-auto space-y-4 flex-1">
                                {previewFile.is_image ? (
                                    <div className="rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 flex items-center justify-center max-h-80">
                                        <img
                                            src={previewFile.url}
                                            alt={previewFile.name}
                                            className="max-h-80 w-auto object-contain"
                                        />
                                    </div>
                                ) : previewFile.type === 'video' ? (
                                    <div className="rounded-2xl overflow-hidden bg-slate-950 border border-slate-800">
                                        <video
                                            controls
                                            src={previewFile.url}
                                            className="w-full max-h-80"
                                        />
                                    </div>
                                ) : previewFile.type === 'audio' ? (
                                    <div className="p-6 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col items-center gap-3">
                                        <Music size={40} className="text-amber-400" />
                                        <audio controls src={previewFile.url} className="w-full" />
                                    </div>
                                ) : previewFile.extension === 'pdf' ? (
                                    <div className="p-8 rounded-2xl bg-slate-950 border border-slate-800 text-center space-y-3">
                                        <FileText size={48} className="text-rose-400 mx-auto" />
                                        <p className="text-sm text-slate-300">سند PDF آماده مشاهده یا دانلود است.</p>
                                        <a
                                            href={previewFile.url}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-brand-400 text-xs font-medium"
                                        >
                                            <ExternalLink size={14} />
                                            <span>باز کردن PDF در تب جدید</span>
                                        </a>
                                    </div>
                                ) : (
                                    <div className="p-8 rounded-2xl bg-slate-950 border border-slate-800 text-center space-y-2">
                                        {getFileIcon(previewFile, 48)}
                                        <p className="text-sm font-semibold text-slate-300">
                                            پیش‌نمایش مستقیم برای این نوع فایل پشتیبانی نمی‌شود.
                                        </p>
                                    </div>
                                )}

                                {/* Metadata Details */}
                                <div className="grid grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80 text-xs">
                                    <div>
                                        <span className="text-slate-500 block mb-0.5">مسیر فایل در سرور:</span>
                                        <span className="text-slate-200 font-mono break-all">{previewFile.path}</span>
                                    </div>
                                    <div>
                                        <span className="text-slate-500 block mb-0.5">حجم فایل:</span>
                                        <span className="text-slate-200 font-semibold">{previewFile.size_formatted}</span>
                                    </div>
                                    <div>
                                        <span className="text-slate-500 block mb-0.5">نوع MIME:</span>
                                        <span className="text-slate-200 font-mono">{previewFile.mime_type}</span>
                                    </div>
                                    <div>
                                        <span className="text-slate-500 block mb-0.5">آخرین تاریخ تغییر:</span>
                                        <span className="text-slate-200">{previewFile.last_modified_date}</span>
                                    </div>
                                </div>
                            </div>

                            {/* Modal Footer */}
                            <div className="flex items-center justify-between p-4 bg-slate-950/60 border-t border-slate-800">
                                <button
                                    onClick={() => {
                                        setDeleteItem({
                                            name: previewFile.name,
                                            path: previewFile.path,
                                            isFolder: false,
                                        });
                                    }}
                                    className="px-3.5 py-2 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-medium flex items-center gap-1.5 transition-colors"
                                >
                                    <Trash2 size={14} />
                                    <span>حذف این فایل</span>
                                </button>

                                <div className="flex items-center gap-2">
                                    <button
                                        onClick={() => handleCopyUrl(previewFile.url, previewFile.path)}
                                        className="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium flex items-center gap-1.5 transition-colors"
                                    >
                                        {copiedPath === previewFile.path ? (
                                            <>
                                                <Check size={14} className="text-emerald-400" />
                                                <span className="text-emerald-300">کپی شد!</span>
                                            </>
                                        ) : (
                                            <>
                                                <Copy size={14} />
                                                <span>کپی آدرس مستقیم</span>
                                            </>
                                        )}
                                    </button>
                                    <a
                                        href={previewFile.url}
                                        download={previewFile.name}
                                        className="px-4 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-medium flex items-center gap-1.5 shadow-md shadow-brand-500/20 transition-colors"
                                    >
                                        <Download size={14} />
                                        <span>دانلود فایل</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* ─── Delete Confirmation Modal ─── */}
                {deleteItem && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4">
                            <div className="flex items-center gap-3 text-rose-400">
                                <div className="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/30">
                                    <AlertTriangle size={24} />
                                </div>
                                <div>
                                    <h3 className="text-base font-bold text-white">
                                        تایید حذف {deleteItem.isFolder ? 'پوشه' : 'فایل'}
                                    </h3>
                                    <p className="text-xs text-slate-400">این عملیات غیرقابل بازگشت است.</p>
                                </div>
                            </div>

                            <p className="text-sm text-slate-300 leading-relaxed">
                                آیا از حذف{' '}
                                <strong className="text-white font-mono">{deleteItem.name}</strong> مطمئن
                                هستید؟
                                {deleteItem.isFolder && (
                                    <span className="block mt-1 text-rose-300 text-xs">
                                        هشدار: تمام فایل‌ها و زیرپوشه‌های داخل این پوشه نیز برای همیشه حذف خواهند شد!
                                    </span>
                                )}
                            </p>

                            <div className="flex items-center justify-end gap-2 pt-2">
                                <button
                                    onClick={() => setDeleteItem(null)}
                                    className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium"
                                >
                                    انصراف
                                </button>
                                <button
                                    onClick={confirmDelete}
                                    className="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-medium flex items-center gap-1.5 shadow-md shadow-rose-600/20"
                                >
                                    <Trash2 size={14} />
                                    <span>بله، حذف کن</span>
                                </button>
                            </div>
                        </div>
                    </div>
                )}

                {/* ─── Bulk Delete Confirmation Modal ─── */}
                {isBulkDeleteOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4">
                            <div className="flex items-center gap-3 text-rose-400">
                                <div className="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/30">
                                    <AlertTriangle size={24} />
                                </div>
                                <div>
                                    <h3 className="text-base font-bold text-white">تایید حذف همگانی</h3>
                                    <p className="text-xs text-slate-400">این عملیات غیرقابل بازگشت است.</p>
                                </div>
                            </div>

                            <p className="text-sm text-slate-300 leading-relaxed">
                                آیا از حذف دائمی <strong>{selectedPaths.length}</strong> فایل انتخاب‌شده از روی
                                سرور مطمئن هستید؟
                            </p>

                            <div className="flex items-center justify-end gap-2 pt-2">
                                <button
                                    onClick={() => setIsBulkDeleteOpen(false)}
                                    className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium"
                                >
                                    انصراف
                                </button>
                                <button
                                    onClick={confirmBulkDelete}
                                    className="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-medium flex items-center gap-1.5 shadow-md shadow-rose-600/20"
                                >
                                    <Trash2 size={14} />
                                    <span>بله، همه را حذف کن</span>
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AdminLayout>
    );
}
