import React, { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    Users,
    Search,
    Filter,
    UserCheck,
    UserX,
    Ban,
    CheckCircle2,
    AlertTriangle,
    Mail,
    Send,
    Eye,
    GraduationCap,
    BookOpen,
    Calendar,
    Phone,
    MapPin,
    Award,
    Sparkles,
    X,
    Clock,
    Layers,
    LayoutGrid,
    List,
    RotateCcw,
    ShieldAlert,
    ExternalLink,
    ChevronDown,
    Trash2,
    MessageSquare,
    Check,
    AlertCircle,
    Info,
    School,
    HeartHandshake,
} from 'lucide-react';

export default function Index({
    students,
    categories = [],
    courses = [],
    stats = {},
    filters = {},
}) {
    // Search and filter states
    const [search, setSearch] = useState(filters.search || '');
    const [categoryId, setCategoryId] = useState(filters.category_id || 'all');
    const [courseId, setCourseId] = useState(filters.course_id || 'all');
    const [status, setStatus] = useState(filters.status || 'all');
    const [enrollmentStatus, setEnrollmentStatus] = useState(filters.enrollment_status || 'all');
    const [sortBy, setSortBy] = useState(filters.sort_by || 'created_at');
    const [sortDir, setSortDir] = useState(filters.sort_dir || 'desc');
    const [viewMode, setViewMode] = useState('grid'); // 'grid' | 'table'

    // Selection
    const [selectedIds, setSelectedIds] = useState([]);

    // Modals
    const [detailStudent, setDetailStudent] = useState(null);
    const [messageStudent, setMessageStudent] = useState(null);
    const [statusModalStudent, setStatusModalStudent] = useState(null);
    const [classBanModalData, setClassBanModalData] = useState(null); // { student, enrollment }
    const [bulkActionModal, setBulkActionModal] = useState(null); // 'message' | 'ban' | 'activate'

    // Form inputs for modals
    const [messageForm, setMessageForm] = useState({ title: '', message: '', priority: 'normal' });
    const [statusForm, setStatusForm] = useState({ status: 'banned', reason: '' });
    const [classStatusForm, setClassStatusForm] = useState({ status: 'banned', reason: '' });
    const [bulkForm, setBulkForm] = useState({ title: '', message: '', reason: '' });
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Filter Navigation
    const applyFilters = (overrides = {}) => {
        const query = {
            search,
            category_id: categoryId,
            course_id: courseId,
            status,
            enrollment_status: enrollmentStatus,
            sort_by: sortBy,
            sort_dir: sortDir,
            ...overrides,
        };

        Object.keys(query).forEach((key) => {
            if (query[key] === '' || query[key] === null || query[key] === 'all') {
                delete query[key];
            }
        });

        router.get('/admin/students', query, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        applyFilters({ search });
    };

    const handleCategoryClick = (catId) => {
        setCategoryId(catId);
        setCourseId('all'); // reset specific course
        applyFilters({ category_id: catId, course_id: 'all' });
    };

    const handleResetFilters = () => {
        setSearch('');
        setCategoryId('all');
        setCourseId('all');
        setStatus('all');
        setEnrollmentStatus('all');
        setSortBy('created_at');
        setSortDir('desc');
        router.get('/admin/students', {}, { preserveState: true, preserveScroll: true });
    };

    // Selection handlers
    const toggleSelect = (id) => {
        setSelectedIds((prev) =>
            prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id]
        );
    };

    const selectAll = () => {
        const list = students.data || [];
        if (selectedIds.length === list.length) {
            setSelectedIds([]);
        } else {
            setSelectedIds(list.map((s) => s.id));
        }
    };

    // Account Status Update Submit
    const handleStatusSubmit = (e) => {
        e.preventDefault();
        if (!statusModalStudent) return;
        setIsSubmitting(true);

        router.post(
            `/admin/students/${statusModalStudent.id}/status`,
            {
                status: statusForm.status,
                reason: statusForm.reason,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsSubmitting(false);
                    setStatusModalStudent(null);
                    setStatusForm({ status: 'banned', reason: '' });
                    // Refresh detail student if open
                    if (detailStudent?.id === statusModalStudent.id) {
                        setDetailStudent((prev) => ({ ...prev, status: statusForm.status }));
                    }
                },
                onError: () => setIsSubmitting(false),
            }
        );
    };

    // Class Enrollment Status Submit (Ban from Class)
    const handleClassStatusSubmit = (e) => {
        e.preventDefault();
        if (!classBanModalData) return;
        setIsSubmitting(true);

        const { student, enrollment } = classBanModalData;

        router.post(
            `/admin/students/${student.id}/enrollment/${enrollment.id}/status`,
            {
                status: classStatusForm.status,
                reason: classStatusForm.reason,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsSubmitting(false);
                    setClassBanModalData(null);
                    setClassStatusForm({ status: 'banned', reason: '' });
                    // Update detailStudent local state if open
                    if (detailStudent?.id === student.id) {
                        setDetailStudent((prev) => ({
                            ...prev,
                            enrollments: prev.enrollments.map((enr) =>
                                enr.id === enrollment.id
                                    ? { ...enr, status: classStatusForm.status }
                                    : enr
                            ),
                        }));
                    }
                },
                onError: () => setIsSubmitting(false),
            }
        );
    };

    // Remove from Class
    const handleRemoveFromClass = (student, enrollment) => {
        if (!confirm(`آیا از حذف کامل دانشجو از کلاس «${enrollment.course_title}» مطمئن هستید؟`)) {
            return;
        }

        router.delete(`/admin/students/${student.id}/enrollment/${enrollment.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                if (detailStudent?.id === student.id) {
                    setDetailStudent((prev) => ({
                        ...prev,
                        enrollments: prev.enrollments.filter((enr) => enr.id !== enrollment.id),
                    }));
                }
            },
        });
    };

    // Send Message Submit
    const handleMessageSubmit = (e) => {
        e.preventDefault();
        if (!messageStudent || !messageForm.title || !messageForm.message) return;
        setIsSubmitting(true);

        router.post(
            `/admin/students/${messageStudent.id}/message`,
            messageForm,
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsSubmitting(false);
                    setMessageStudent(null);
                    setMessageForm({ title: '', message: '', priority: 'normal' });
                },
                onError: () => setIsSubmitting(false),
            }
        );
    };

    // Bulk Action Submit
    const handleBulkSubmit = (action) => {
        if (selectedIds.length === 0) return;
        setIsSubmitting(true);

        router.post(
            '/admin/students/bulk-action',
            {
                ids: selectedIds,
                action: action,
                title: bulkForm.title,
                message: bulkForm.message,
                reason: bulkForm.reason,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsSubmitting(false);
                    setSelectedIds([]);
                    setBulkActionModal(null);
                    setBulkForm({ title: '', message: '', reason: '' });
                },
                onError: () => setIsSubmitting(false),
            }
        );
    };

    const studentList = students.data || [];

    return (
        <AdminLayout title="مدیریت دانشجویان - پنل مدیریت ادورا">
            <div className="p-4 sm:p-6 lg:p-8 space-y-6">
                {/* ─── Top Banner & Summary Stats ─── */}
                <div className="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-900/90 to-slate-800/80 border border-slate-800 p-6 shadow-2xl">
                    <div className="absolute top-0 right-0 -mt-10 -mr-10 w-72 h-72 bg-brand-500/10 rounded-full blur-3xl pointer-events-none" />
                    <div className="absolute bottom-0 left-0 -mb-10 -ml-10 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none" />

                    <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div className="space-y-2">
                            <div className="flex items-center gap-3">
                                <div className="p-3 rounded-2xl bg-brand-500/20 text-brand-400 border border-brand-500/30">
                                    <GraduationCap size={28} />
                                </div>
                                <div>
                                    <h1 className="text-2xl sm:text-3xl font-black text-white tracking-tight">
                                        مدیریت شاگردان و دانشجویان
                                    </h1>
                                    <p className="text-sm text-slate-400">
                                        دسته‌بندی بر اساس رشته و دوره، مشاهده کامل پرونده، ارسال پیام و کنترل حضور در کلاس‌ها
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Top Summary Stats */}
                        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div className="bg-slate-950/60 rounded-2xl p-3.5 border border-slate-800 text-center">
                                <span className="text-xs text-slate-400 block mb-1">کل شاگردان</span>
                                <span className="text-xl font-black text-white">
                                    {(stats.total_students || 0).toLocaleString('fa-IR')}
                                </span>
                            </div>

                            <div className="bg-slate-950/60 rounded-2xl p-3.5 border border-slate-800 text-center">
                                <span className="text-xs text-slate-400 block mb-1">شاگردان فعال</span>
                                <span className="text-xl font-black text-emerald-400">
                                    {(stats.active_students || 0).toLocaleString('fa-IR')}
                                </span>
                            </div>

                            <div className="bg-slate-950/60 rounded-2xl p-3.5 border border-slate-800 text-center">
                                <span className="text-xs text-slate-400 block mb-1">مسدود / معلق</span>
                                <span className="text-xl font-black text-rose-400">
                                    {(stats.banned_students || 0).toLocaleString('fa-IR')}
                                </span>
                            </div>

                            <div className="bg-slate-950/60 rounded-2xl p-3.5 border border-slate-800 text-center">
                                <span className="text-xs text-slate-400 block mb-1">ثبت‌نام در کلاس‌ها</span>
                                <span className="text-xl font-black text-brand-400">
                                    {(stats.total_enrollments || 0).toLocaleString('fa-IR')}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* ─── Categorization Tabs (نظر به چیزی که می‌خوانند) ─── */}
                <div className="space-y-2">
                    <div className="flex items-center justify-between">
                        <span className="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                            <Layers size={15} className="text-brand-400" />
                            <span>دسته‌بندی موضوعی دوره‌های آموزشی:</span>
                        </span>
                        <span className="text-xs text-slate-500">
                            فیلتر سریع شاگردان بر اساس رشته و تخصص
                        </span>
                    </div>

                    <div className="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin">
                        <button
                            onClick={() => handleCategoryClick('all')}
                            className={`px-4 py-2 rounded-2xl text-xs font-semibold whitespace-nowrap transition-all flex items-center gap-2 shadow-sm ${
                                categoryId === 'all'
                                    ? 'bg-brand-500 text-white shadow-brand-500/25 ring-2 ring-brand-500/30'
                                    : 'bg-slate-900/80 hover:bg-slate-800 text-slate-300 border border-slate-800'
                            }`}
                        >
                            <span>همه شاگردان</span>
                            <span className="px-1.5 py-0.5 rounded-full bg-slate-950/60 text-[10px]">
                                {stats.total_students || 0}
                            </span>
                        </button>

                        {categories.map((cat) => (
                            <button
                                key={cat.id}
                                onClick={() => handleCategoryClick(String(cat.id))}
                                className={`px-4 py-2 rounded-2xl text-xs font-semibold whitespace-nowrap transition-all flex items-center gap-2 shadow-sm ${
                                    categoryId === String(cat.id)
                                        ? 'bg-brand-500 text-white shadow-brand-500/25 ring-2 ring-brand-500/30'
                                        : 'bg-slate-900/80 hover:bg-slate-800 text-slate-300 border border-slate-800'
                                }`}
                            >
                                <BookOpen size={14} className={categoryId === String(cat.id) ? 'text-white' : 'text-brand-400'} />
                                <span>{cat.name}</span>
                                {cat.enrolled_students_count > 0 && (
                                    <span className="px-1.5 py-0.5 rounded-full bg-slate-950/60 text-[10px]">
                                        {cat.enrolled_students_count}
                                    </span>
                                )}
                            </button>
                        ))}

                        <button
                            onClick={() => handleCategoryClick('no_course')}
                            className={`px-4 py-2 rounded-2xl text-xs font-semibold whitespace-nowrap transition-all flex items-center gap-2 shadow-sm ${
                                categoryId === 'no_course'
                                    ? 'bg-amber-500 text-white shadow-amber-500/25'
                                    : 'bg-slate-900/80 hover:bg-slate-800 text-slate-400 border border-slate-800'
                            }`}
                        >
                            <span>بدون ثبت‌نام دوره</span>
                        </button>
                    </div>
                </div>

                {/* ─── Search & Filters Control Bar ─── */}
                <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-900/50 p-4 rounded-3xl border border-slate-800/80 backdrop-blur-md">
                    {/* Search Input */}
                    <form onSubmit={handleSearchSubmit} className="relative flex-1 max-w-md">
                        <Search className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="جستجو نام، تذکره، ایمیل، تلفن، رشته تحصیلی..."
                            className="w-full pr-10 pl-10 py-2.5 rounded-2xl bg-slate-950 border border-slate-800 text-slate-100 placeholder-slate-500 text-xs focus:outline-none focus:border-brand-500 transition-colors"
                        />
                        {search && (
                            <button
                                type="button"
                                onClick={() => {
                                    setSearch('');
                                    applyFilters({ search: '' });
                                }}
                                className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white"
                            >
                                <X size={15} />
                            </button>
                        )}
                    </form>

                    {/* Filter Dropdowns */}
                    <div className="flex flex-wrap items-center gap-2.5 text-xs">
                        {/* Specific Course Select */}
                        <select
                            value={courseId}
                            onChange={(e) => {
                                setCourseId(e.target.value);
                                applyFilters({ course_id: e.target.value });
                            }}
                            className="py-2 px-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 text-xs focus:outline-none focus:border-brand-500 max-w-[160px] truncate"
                        >
                            <option value="all">همه کلاس‌ها و دوره‌ها</option>
                            {courses.map((c) => (
                                <option key={c.id} value={c.id}>
                                    {c.title}
                                </option>
                            ))}
                        </select>

                        {/* Account Status */}
                        <select
                            value={status}
                            onChange={(e) => {
                                setStatus(e.target.value);
                                applyFilters({ status: e.target.value });
                            }}
                            className="py-2 px-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 text-xs focus:outline-none focus:border-brand-500"
                        >
                            <option value="all">وضعیت حساب: همه</option>
                            <option value="active">حساب‌های فعال</option>
                            <option value="banned">حساب‌های مسدود (بن)</option>
                            <option value="suspended">حساب‌های تعلیق‌شده</option>
                        </select>

                        {/* Class Status */}
                        <select
                            value={enrollmentStatus}
                            onChange={(e) => {
                                setEnrollmentStatus(e.target.value);
                                applyFilters({ enrollment_status: e.target.value });
                            }}
                            className="py-2 px-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 text-xs focus:outline-none focus:border-brand-500"
                        >
                            <option value="all">وضعیت کلاسی: همه</option>
                            <option value="active">فعال در کلاس</option>
                            <option value="banned">محروم/بن از کلاس</option>
                            <option value="suspended">تعلیق از کلاس</option>
                            <option value="completed">دوره تمام شده</option>
                        </select>

                        {/* Reset Filters */}
                        {(search || categoryId !== 'all' || courseId !== 'all' || status !== 'all' || enrollmentStatus !== 'all') && (
                            <button
                                onClick={handleResetFilters}
                                title="پاکسازی فیلترها"
                                className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors"
                            >
                                <RotateCcw size={15} />
                            </button>
                        )}

                        {/* View Switcher */}
                        <div className="flex items-center bg-slate-950 rounded-xl p-1 border border-slate-800 text-slate-400 mr-auto lg:mr-0">
                            <button
                                onClick={() => setViewMode('grid')}
                                className={`p-1.5 rounded-lg transition-colors ${
                                    viewMode === 'grid' ? 'bg-slate-800 text-white' : 'hover:text-white'
                                }`}
                                title="نمایش کارتی"
                            >
                                <LayoutGrid size={15} />
                            </button>
                            <button
                                onClick={() => setViewMode('table')}
                                className={`p-1.5 rounded-lg transition-colors ${
                                    viewMode === 'table' ? 'bg-slate-800 text-white' : 'hover:text-white'
                                }`}
                                title="نمایش جدولی"
                            >
                                <List size={15} />
                            </button>
                        </div>
                    </div>
                </div>

                {/* ─── Bulk Action Bar (When students are selected) ─── */}
                {selectedIds.length > 0 && (
                    <div className="flex items-center justify-between p-4 rounded-2xl bg-brand-500/10 border border-brand-500/30 text-brand-300 text-sm animate-in fade-in">
                        <div className="flex items-center gap-3">
                            <CheckCircle2 size={18} className="text-brand-400 shrink-0" />
                            <span>
                                <strong>{selectedIds.length}</strong> شاگرد برای عملیات همگانی انتخاب شده‌اند.
                            </span>
                        </div>

                        <div className="flex items-center gap-2">
                            <button
                                onClick={() => setSelectedIds([])}
                                className="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium"
                            >
                                لغو انتخاب
                            </button>
                            <button
                                onClick={() => setBulkActionModal('message')}
                                className="px-3.5 py-1.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-medium flex items-center gap-1.5 shadow-sm"
                            >
                                <Mail size={14} />
                                <span>ارسال پیام گروهی</span>
                            </button>
                            <button
                                onClick={() => handleBulkSubmit('activate')}
                                className="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-medium flex items-center gap-1.5 shadow-sm"
                            >
                                <UserCheck size={14} />
                                <span>فعال‌سازی همگانی</span>
                            </button>
                            <button
                                onClick={() => handleBulkSubmit('ban')}
                                className="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-medium flex items-center gap-1.5 shadow-sm"
                            >
                                <Ban size={14} />
                                <span>مسدودسازی همگانی</span>
                            </button>
                        </div>
                    </div>
                )}

                {/* ─── Students List (Grid or Table) ─── */}
                {studentList.length === 0 ? (
                    <div className="flex flex-col items-center justify-center p-12 rounded-3xl bg-slate-900/40 border border-slate-800/80 text-center space-y-4">
                        <div className="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center text-slate-500">
                            <Users size={32} />
                        </div>
                        <div className="space-y-1">
                            <h3 className="text-base font-bold text-slate-200">
                                هیچ دانشجویی با این مشخصات یافت نشد!
                            </h3>
                            <p className="text-xs text-slate-400 max-w-sm">
                                فیلترهای جستجو، دسته‌بندی موضوعی یا وضعیت انتخاب‌شده را تغییر دهید.
                            </p>
                        </div>
                        <button
                            onClick={handleResetFilters}
                            className="px-4 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-medium text-xs flex items-center gap-2 shadow-md transition-colors"
                        >
                            <RotateCcw size={15} />
                            <span>نمایش همه دانشجویان</span>
                        </button>
                    </div>
                ) : viewMode === 'grid' ? (
                    /* ─── Grid View (Cards) ─── */
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        {studentList.map((student) => {
                            const isSelected = selectedIds.includes(student.id);
                            const isBanned = student.status === 'banned' || student.status === 'suspended';

                            return (
                                <div
                                    key={student.id}
                                    className={`group relative flex flex-col rounded-3xl bg-slate-900/80 border transition-all overflow-hidden p-5 space-y-4 shadow-sm ${
                                        isSelected
                                            ? 'border-brand-500 ring-2 ring-brand-500/30'
                                            : isBanned
                                            ? 'border-rose-500/30 bg-rose-500/5'
                                            : 'border-slate-800/80 hover:border-slate-700 hover:shadow-xl'
                                    }`}
                                >
                                    {/* Top Card Row: Avatar, Info, Status Badge */}
                                    <div className="flex items-start justify-between gap-3">
                                        <div className="flex items-center gap-3">
                                            {/* Selection Checkbox */}
                                            <input
                                                type="checkbox"
                                                checked={isSelected}
                                                onChange={() => toggleSelect(student.id)}
                                                className="w-4 h-4 rounded border-slate-700 bg-slate-950 text-brand-500 focus:ring-brand-500/20 cursor-pointer"
                                            />

                                            {/* Avatar */}
                                            <div className="relative">
                                                {student.avatar ? (
                                                    <img
                                                        src={student.avatar}
                                                        alt={student.name}
                                                        className="w-12 h-12 rounded-2xl object-cover border border-slate-700"
                                                    />
                                                ) : (
                                                    <div className="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-600 to-indigo-700 text-white font-bold text-base flex items-center justify-center border border-slate-700">
                                                        {student.name.charAt(0)}
                                                    </div>
                                                )}
                                                <span
                                                    className={`absolute -bottom-1 -left-1 w-3.5 h-3.5 rounded-full border-2 border-slate-900 ${
                                                        student.status === 'active'
                                                            ? 'bg-emerald-500'
                                                            : student.status === 'banned'
                                                            ? 'bg-rose-500'
                                                            : 'bg-amber-500'
                                                    }`}
                                                />
                                            </div>

                                            {/* Name & ID */}
                                            <div className="min-w-0">
                                                <h3
                                                    onClick={() => setDetailStudent(student)}
                                                    className="text-sm font-bold text-white truncate hover:text-brand-300 cursor-pointer transition-colors"
                                                >
                                                    {student.name}
                                                </h3>
                                                <p className="text-[11px] text-slate-400 font-mono truncate">
                                                    تذکره: {student.profile.national_id || '-'}
                                                </p>
                                            </div>
                                        </div>

                                        {/* Status Pill */}
                                        <span
                                            className={`px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border ${
                                                student.status === 'active'
                                                    ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'
                                                    : student.status === 'banned'
                                                    ? 'bg-rose-500/10 text-rose-400 border-rose-500/30'
                                                    : 'bg-amber-500/10 text-amber-400 border-amber-500/30'
                                            }`}
                                        >
                                            {student.status === 'active'
                                                ? 'فعال'
                                                : student.status === 'banned'
                                                ? 'مسدود (بن)'
                                                : 'معلق'}
                                        </span>
                                    </div>

                                    {/* Field of Study & Contact */}
                                    <div className="space-y-1.5 text-xs text-slate-300 border-t border-b border-slate-800/80 py-3">
                                        <div className="flex items-center gap-2 text-slate-400">
                                            <Mail size={13} className="text-slate-500 shrink-0" />
                                            <span className="truncate font-mono text-[11px]">{student.email}</span>
                                        </div>
                                        <div className="flex items-center gap-2 text-slate-400">
                                            <Phone size={13} className="text-slate-500 shrink-0" />
                                            <span className="font-mono text-[11px]">{student.profile.phone_number}</span>
                                        </div>
                                        <div className="flex items-center gap-2 text-slate-400">
                                            <School size={13} className="text-slate-500 shrink-0" />
                                            <span className="truncate">
                                                رشته: {student.profile.field_of_study !== '-' ? student.profile.field_of_study : 'نامشخص'}
                                            </span>
                                        </div>
                                    </div>

                                    {/* What they study / Enrolled Courses Section */}
                                    <div className="space-y-1.5 flex-1">
                                        <span className="text-[11px] font-semibold text-slate-400 block">
                                            دوره‌ها و کلاس‌های ثبت‌نامی ({student.enrollments.length}):
                                        </span>
                                        {student.enrollments.length === 0 ? (
                                            <span className="text-[11px] text-slate-500 italic block">
                                                هنوز در هیچ دوره‌ای ثبت‌نام نکرده است.
                                            </span>
                                        ) : (
                                            <div className="flex flex-wrap gap-1.5 max-h-20 overflow-y-auto scrollbar-none">
                                                {student.enrollments.map((enr) => {
                                                    const isEnrBanned = enr.status === 'banned' || enr.status === 'suspended';
                                                    return (
                                                        <span
                                                            key={enr.id}
                                                            title={`دسته‌بندی: ${enr.category_name} | وضعیت در کلاس: ${enr.status}`}
                                                            className={`px-2 py-1 rounded-xl text-[10px] font-medium border flex items-center gap-1 ${
                                                                isEnrBanned
                                                                    ? 'bg-rose-500/10 text-rose-300 border-rose-500/30 line-through'
                                                                    : 'bg-brand-500/10 text-brand-300 border-brand-500/20'
                                                            }`}
                                                        >
                                                            <BookOpen size={11} />
                                                            <span className="truncate max-w-[130px]">{enr.course_title}</span>
                                                        </span>
                                                    );
                                                })}
                                            </div>
                                        )}
                                    </div>

                                    {/* Action Buttons */}
                                    <div className="flex items-center justify-between pt-2 border-t border-slate-800/80 gap-2">
                                        <button
                                            onClick={() => setDetailStudent(student)}
                                            className="flex-1 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium flex items-center justify-center gap-1.5 transition-colors"
                                        >
                                            <Eye size={14} />
                                            <span>مشاهده کامل</span>
                                        </button>

                                        <button
                                            onClick={() => setMessageStudent(student)}
                                            className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors"
                                            title="ارسال پیام به دانشجو"
                                        >
                                            <MessageSquare size={15} />
                                        </button>

                                        <button
                                            onClick={() => {
                                                setStatusModalStudent(student);
                                                setStatusForm({
                                                    status: student.status === 'active' ? 'banned' : 'active',
                                                    reason: '',
                                                });
                                            }}
                                            className={`p-2 rounded-xl transition-colors ${
                                                isBanned
                                                    ? 'bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300'
                                                    : 'bg-rose-500/20 hover:bg-rose-500/30 text-rose-300'
                                            }`}
                                            title={isBanned ? 'رفع مسدودیت' : 'مسدودسازی / بن'}
                                        >
                                            {isBanned ? <UserCheck size={15} /> : <Ban size={15} />}
                                        </button>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                ) : (
                    /* ─── Table View ─── */
                    <div className="rounded-3xl border border-slate-800 overflow-hidden bg-slate-900/60 backdrop-blur-md">
                        <div className="overflow-x-auto">
                            <table className="w-full text-right text-xs">
                                <thead className="bg-slate-950/80 text-slate-400 border-b border-slate-800 uppercase font-medium">
                                    <tr>
                                        <th className="p-4 w-10 text-center">
                                            <input
                                                type="checkbox"
                                                checked={selectedIds.length > 0 && selectedIds.length === studentList.length}
                                                onChange={selectAll}
                                                className="w-4 h-4 rounded border-slate-700 bg-slate-900 text-brand-500 focus:ring-brand-500/20 cursor-pointer"
                                            />
                                        </th>
                                        <th className="p-4">مشخصات دانشجو</th>
                                        <th className="p-4">تماس و هویت</th>
                                        <th className="p-4">رشته و تحصیلات</th>
                                        <th className="p-4">کلاس‌ها و دوره‌های ثبت‌نامی</th>
                                        <th className="p-4">امتیاز XP</th>
                                        <th className="p-4">وضعیت حساب</th>
                                        <th className="p-4 text-center">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-800/60">
                                    {studentList.map((student) => {
                                        const isSelected = selectedIds.includes(student.id);
                                        const isBanned = student.status === 'banned' || student.status === 'suspended';

                                        return (
                                            <tr
                                                key={student.id}
                                                className={`hover:bg-slate-800/40 transition-colors ${
                                                    isSelected ? 'bg-brand-500/5' : ''
                                                }`}
                                            >
                                                <td className="p-4 text-center">
                                                    <input
                                                        type="checkbox"
                                                        checked={isSelected}
                                                        onChange={() => toggleSelect(student.id)}
                                                        className="w-4 h-4 rounded border-slate-700 bg-slate-900 text-brand-500 focus:ring-brand-500/20 cursor-pointer"
                                                    />
                                                </td>
                                                <td className="p-4">
                                                    <div
                                                        className="flex items-center gap-3 cursor-pointer"
                                                        onClick={() => setDetailStudent(student)}
                                                    >
                                                        {student.avatar ? (
                                                            <img
                                                                src={student.avatar}
                                                                alt={student.name}
                                                                className="w-9 h-9 rounded-xl object-cover border border-slate-700"
                                                            />
                                                        ) : (
                                                            <div className="w-9 h-9 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center">
                                                                {student.name.charAt(0)}
                                                            </div>
                                                        )}
                                                        <div>
                                                            <span className="font-bold text-slate-100 hover:text-brand-300 block">
                                                                {student.name}
                                                            </span>
                                                            <span className="text-[11px] text-slate-500">
                                                                پدر: {student.profile.father_name}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="p-4 space-y-0.5">
                                                    <div className="font-mono text-[11px] text-slate-300">
                                                        {student.email}
                                                    </div>
                                                    <div className="font-mono text-[11px] text-slate-400">
                                                        {student.profile.phone_number}
                                                    </div>
                                                </td>
                                                <td className="p-4">
                                                    <span className="px-2.5 py-1 rounded-xl bg-slate-800 border border-slate-700 text-slate-300 font-medium">
                                                        {student.profile.field_of_study !== '-' ? student.profile.field_of_study : 'عمومی'}
                                                    </span>
                                                </td>
                                                <td className="p-4">
                                                    {student.enrollments.length === 0 ? (
                                                        <span className="text-slate-500 italic">بدون دوره</span>
                                                    ) : (
                                                        <div className="flex flex-wrap gap-1 max-w-xs">
                                                            {student.enrollments.map((enr) => (
                                                                <span
                                                                    key={enr.id}
                                                                    className={`px-2 py-0.5 rounded-lg text-[10px] font-medium border ${
                                                                        enr.status === 'banned'
                                                                            ? 'bg-rose-500/10 text-rose-300 border-rose-500/30'
                                                                            : 'bg-brand-500/10 text-brand-300 border-brand-500/20'
                                                                    }`}
                                                                >
                                                                    {enr.course_title}
                                                                </span>
                                                            ))}
                                                        </div>
                                                    )}
                                                </td>
                                                <td className="p-4 font-mono font-bold text-brand-400">
                                                    {student.xp} XP
                                                </td>
                                                <td className="p-4">
                                                    <span
                                                        className={`px-2.5 py-1 rounded-full text-[10px] font-bold border ${
                                                            student.status === 'active'
                                                                ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'
                                                                : 'bg-rose-500/10 text-rose-400 border-rose-500/30'
                                                        }`}
                                                    >
                                                        {student.status === 'active' ? 'فعال' : 'مسدود'}
                                                    </span>
                                                </td>
                                                <td className="p-4">
                                                    <div className="flex items-center justify-center gap-1.5">
                                                        <button
                                                            onClick={() => setDetailStudent(student)}
                                                            className="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-slate-200"
                                                            title="مشاهده پرونده کامل"
                                                        >
                                                            <Eye size={15} />
                                                        </button>
                                                        <button
                                                            onClick={() => setMessageStudent(student)}
                                                            className="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-slate-200"
                                                            title="ارسال پیام"
                                                        >
                                                            <MessageSquare size={15} />
                                                        </button>
                                                        <button
                                                            onClick={() => {
                                                                setStatusModalStudent(student);
                                                                setStatusForm({
                                                                    status: student.status === 'active' ? 'banned' : 'active',
                                                                    reason: '',
                                                                });
                                                            }}
                                                            className={`p-1.5 rounded-lg ${
                                                                isBanned
                                                                    ? 'text-emerald-400 hover:bg-emerald-500/20'
                                                                    : 'text-rose-400 hover:bg-rose-500/20'
                                                            }`}
                                                            title={isBanned ? 'فعال‌سازی' : 'بن کردن'}
                                                        >
                                                            {isBanned ? <UserCheck size={15} /> : <Ban size={15} />}
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

                {/* ─── Pagination ─── */}
                {students.links && students.links.length > 3 && (
                    <div className="flex items-center justify-center gap-1.5 pt-4">
                        {students.links.map((link, idx) => (
                            <Link
                                key={idx}
                                href={link.url || '#'}
                                preserveScroll
                                className={`px-3 py-1.5 rounded-xl text-xs font-semibold transition-all ${
                                    link.active
                                        ? 'bg-brand-500 text-white shadow-md shadow-brand-500/25'
                                        : link.url
                                        ? 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800'
                                        : 'bg-slate-950 text-slate-600 cursor-not-allowed border border-slate-900'
                                }`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}

                {/* ─── MODAL 1: Full Student Profile Drawer / Modal (همه مشخصات) ─── */}
                {detailStudent && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-950/85 backdrop-blur-md animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-4xl rounded-3xl overflow-hidden shadow-2xl flex flex-col max-h-[92vh]">
                            {/* Modal Header */}
                            <div className="flex items-center justify-between p-5 border-b border-slate-800 bg-slate-950/50">
                                <div className="flex items-center gap-3">
                                    {detailStudent.avatar ? (
                                        <img
                                            src={detailStudent.avatar}
                                            alt={detailStudent.name}
                                            className="w-12 h-12 rounded-2xl object-cover border border-slate-700"
                                        />
                                    ) : (
                                        <div className="w-12 h-12 rounded-2xl bg-brand-600 text-white font-bold text-lg flex items-center justify-center">
                                            {detailStudent.name.charAt(0)}
                                        </div>
                                    )}
                                    <div>
                                        <div className="flex items-center gap-2">
                                            <h2 className="text-lg font-black text-white">{detailStudent.name}</h2>
                                            <span
                                                className={`px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${
                                                    detailStudent.status === 'active'
                                                        ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'
                                                        : 'bg-rose-500/10 text-rose-400 border-rose-500/30'
                                                }`}
                                            >
                                                {detailStudent.status === 'active' ? 'حساب فعال' : 'مسدود / بن'}
                                            </span>
                                        </div>
                                        <p className="text-xs text-slate-400 font-mono">
                                            شناسه کاربری: #{detailStudent.id} | تاریخ عضویت: {detailStudent.registered_date}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-center gap-2">
                                    <button
                                        onClick={() => {
                                            setMessageStudent(detailStudent);
                                        }}
                                        className="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium flex items-center gap-1.5 transition-colors"
                                    >
                                        <MessageSquare size={14} className="text-brand-400" />
                                        <span>ارسال پیام</span>
                                    </button>

                                    <button
                                        onClick={() => {
                                            setStatusModalStudent(detailStudent);
                                            setStatusForm({
                                                status: detailStudent.status === 'active' ? 'banned' : 'active',
                                                reason: '',
                                            });
                                        }}
                                        className={`px-3.5 py-2 rounded-xl text-xs font-medium flex items-center gap-1.5 transition-colors ${
                                            detailStudent.status === 'active'
                                                ? 'bg-rose-600/20 text-rose-300 hover:bg-rose-600/30 border border-rose-500/30'
                                                : 'bg-emerald-600/20 text-emerald-300 hover:bg-emerald-600/30 border border-emerald-500/30'
                                        }`}
                                    >
                                        <Ban size={14} />
                                        <span>{detailStudent.status === 'active' ? 'مسدودسازی حساب' : 'رفع مسدودیت'}</span>
                                    </button>

                                    <button
                                        onClick={() => setDetailStudent(null)}
                                        className="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800"
                                    >
                                        <X size={18} />
                                    </button>
                                </div>
                            </div>

                            {/* Modal Content Tabs / Sections */}
                            <div className="p-6 overflow-y-auto space-y-6 flex-1 text-xs">
                                {/* 1. Personal & Identity Info */}
                                <div className="space-y-3">
                                    <h3 className="text-sm font-bold text-brand-400 flex items-center gap-2">
                                        <Users size={16} />
                                        <span>مشخصات فردی و هویتی</span>
                                    </h3>
                                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">نام و تخلص:</span>
                                            <span className="text-slate-200 font-semibold">{detailStudent.name}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">نام پدر:</span>
                                            <span className="text-slate-200 font-semibold">{detailStudent.profile.father_name}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">نام مادر:</span>
                                            <span className="text-slate-200">{detailStudent.profile.mother_name}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">جنسیت:</span>
                                            <span className="text-slate-200">
                                                {detailStudent.profile.gender === 'female' ? 'خانم' : detailStudent.profile.gender === 'male' ? 'آقا' : 'نامشخص'}
                                            </span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">شماره تذکره / شناسنامه:</span>
                                            <span className="text-slate-200 font-mono font-bold text-amber-300">
                                                {detailStudent.profile.national_id}
                                            </span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">تاریخ تولد:</span>
                                            <span className="text-slate-200 font-mono">{detailStudent.profile.date_of_birth}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">شماره تماس مستقیم:</span>
                                            <span className="text-slate-200 font-mono">{detailStudent.profile.phone_number}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">شماره واتساپ:</span>
                                            <span className="text-slate-200 font-mono">{detailStudent.profile.whatsapp_number}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">شماره پاسپورت:</span>
                                            <span className="text-slate-200 font-mono">{detailStudent.profile.passport_number}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">گروه خونی:</span>
                                            <span className="text-slate-200 font-mono">{detailStudent.profile.blood_type}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">وضعیت تأهل:</span>
                                            <span className="text-slate-200">{detailStudent.profile.marital_status}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">مجموع امتیاز XP:</span>
                                            <span className="text-brand-400 font-bold font-mono">{detailStudent.xp} XP</span>
                                        </div>
                                    </div>
                                </div>

                                {/* 2. Education & Academic Background */}
                                <div className="space-y-3">
                                    <h3 className="text-sm font-bold text-emerald-400 flex items-center gap-2">
                                        <GraduationCap size={16} />
                                        <span>مشخصات تحصیلی و آموزشی</span>
                                    </h3>
                                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">آخرین مقطع تحصیلی:</span>
                                            <span className="text-slate-200 font-semibold">{detailStudent.profile.last_education_level}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">رشته تحصیلی (Field of Study):</span>
                                            <span className="text-emerald-300 font-bold">{detailStudent.profile.field_of_study}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">مکتب / دانشگاه:</span>
                                            <span className="text-slate-200">{detailStudent.profile.university_name !== '-' ? detailStudent.profile.university_name : detailStudent.profile.last_school_name}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">سال فراغت و معدل (GPA):</span>
                                            <span className="text-slate-200">
                                                {detailStudent.profile.graduation_year} | معدل: {detailStudent.profile.gpa}
                                            </span>
                                        </div>
                                        <div className="col-span-2">
                                            <span className="text-slate-500 block mb-0.5">مهارت‌ها و زبان‌ها:</span>
                                            <span className="text-slate-300">{detailStudent.profile.skills} / {detailStudent.profile.languages}</span>
                                        </div>
                                        <div className="col-span-2">
                                            <span className="text-slate-500 block mb-0.5">سایر مدارک و گواهینامه‌ها:</span>
                                            <span className="text-slate-300">{detailStudent.profile.other_certifications}</span>
                                        </div>
                                    </div>
                                </div>

                                {/* 3. Location & Emergency Contact */}
                                <div className="space-y-3">
                                    <h3 className="text-sm font-bold text-purple-400 flex items-center gap-2">
                                        <MapPin size={16} />
                                        <span>سکونت، آدرس و تماس اضطراری</span>
                                    </h3>
                                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">ولایت و ولسوالی:</span>
                                            <span className="text-slate-200 font-semibold">{detailStudent.profile.province} - {detailStudent.profile.district}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">کد پستی:</span>
                                            <span className="text-slate-200 font-mono">{detailStudent.profile.postal_code}</span>
                                        </div>
                                        <div className="col-span-2">
                                            <span className="text-slate-500 block mb-0.5">آدرس فعلی:</span>
                                            <span className="text-slate-200">{detailStudent.profile.current_address}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">شخص تماس اضطراری:</span>
                                            <span className="text-slate-200 font-semibold">{detailStudent.profile.emergency_contact_name}</span>
                                        </div>
                                        <div>
                                            <span className="text-slate-500 block mb-0.5">نسبت با دانشجو:</span>
                                            <span className="text-slate-200">{detailStudent.profile.emergency_contact_relation}</span>
                                        </div>
                                        <div className="col-span-2">
                                            <span className="text-slate-500 block mb-0.5">شماره تماس اضطراری:</span>
                                            <span className="text-slate-200 font-mono font-bold text-amber-300">
                                                {detailStudent.profile.emergency_contact_phone}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {/* 4. Classes & Enrolled Courses - با قابلیت بن از کلاس */}
                                <div className="space-y-3">
                                    <div className="flex items-center justify-between">
                                        <h3 className="text-sm font-bold text-amber-400 flex items-center gap-2">
                                            <BookOpen size={16} />
                                            <span>کلاس‌ها و دوره‌های ثبت‌نامی ({detailStudent.enrollments.length})</span>
                                        </h3>
                                        <span className="text-[11px] text-slate-400">
                                            می‌توانید دانشجو را از یک کلاس مشخص بن کرده یا حضورش را تعلیق نمایید.
                                        </span>
                                    </div>

                                    {detailStudent.enrollments.length === 0 ? (
                                        <div className="p-6 rounded-2xl bg-slate-950/60 border border-slate-800 text-center text-slate-500 italic">
                                            این دانشجو در حال حاضر در هیچ کلاسی ثبت‌نام نکرده است.
                                        </div>
                                    ) : (
                                        <div className="space-y-3">
                                            {detailStudent.enrollments.map((enr) => {
                                                const isClassBanned = enr.status === 'banned' || enr.status === 'suspended';

                                                return (
                                                    <div
                                                        key={enr.id}
                                                        className={`p-4 rounded-2xl border transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4 ${
                                                            isClassBanned
                                                                ? 'bg-rose-500/10 border-rose-500/30'
                                                                : 'bg-slate-950/80 border-slate-800/80'
                                                        }`}
                                                    >
                                                        <div className="flex items-center gap-3">
                                                            {enr.course_thumbnail ? (
                                                                <img
                                                                    src={enr.course_thumbnail}
                                                                    alt={enr.course_title}
                                                                    className="w-14 h-14 rounded-2xl object-cover border border-slate-700 shrink-0"
                                                                />
                                                            ) : (
                                                                <div className="w-14 h-14 rounded-2xl bg-slate-800 flex items-center justify-center text-brand-400 shrink-0">
                                                                    <BookOpen size={24} />
                                                                </div>
                                                            )}
                                                            <div>
                                                                <div className="flex items-center gap-2">
                                                                    <h4 className="text-sm font-bold text-white">
                                                                        {enr.course_title}
                                                                    </h4>
                                                                    <span className="px-2 py-0.5 rounded-lg bg-slate-800 text-[10px] text-brand-300 border border-slate-700">
                                                                        {enr.category_name}
                                                                    </span>
                                                                </div>
                                                                <p className="text-[11px] text-slate-400 mt-0.5">
                                                                    مدرس: <strong>{enr.teacher_name}</strong> | تاریخ ثبت‌نام: {enr.enrolled_at}
                                                                </p>
                                                                <div className="flex items-center gap-3 mt-1.5 text-[11px] text-slate-400">
                                                                    <span>پیشرفت: <strong>%{enr.progress_percentage}</strong></span>
                                                                    <span
                                                                        className={`px-2 py-0.5 rounded-full text-[10px] font-bold border ${
                                                                            enr.status === 'active'
                                                                                ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'
                                                                                : enr.status === 'banned'
                                                                                ? 'bg-rose-500/10 text-rose-400 border-rose-500/30'
                                                                                : enr.status === 'suspended'
                                                                                ? 'bg-amber-500/10 text-amber-400 border-amber-500/30'
                                                                                : 'bg-blue-500/10 text-blue-400 border-blue-500/30'
                                                                        }`}
                                                                    >
                                                                        {enr.status === 'active'
                                                                            ? 'فعال در کلاس'
                                                                            : enr.status === 'banned'
                                                                            ? 'محروم / بن از کلاس'
                                                                            : enr.status === 'suspended'
                                                                            ? 'تعلیق موقت'
                                                                            : 'تکمیل‌شده'}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {/* Class Action Buttons */}
                                                        <div className="flex items-center gap-2 self-end sm:self-center">
                                                            <button
                                                                onClick={() => {
                                                                    setClassBanModalData({
                                                                        student: detailStudent,
                                                                        enrollment: enr,
                                                                    });
                                                                    setClassStatusForm({
                                                                        status: enr.status === 'active' ? 'banned' : 'active',
                                                                        reason: '',
                                                                    });
                                                                }}
                                                                className={`px-3 py-1.5 rounded-xl text-xs font-medium flex items-center gap-1.5 transition-colors ${
                                                                    isClassBanned
                                                                        ? 'bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30'
                                                                        : 'bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30'
                                                                }`}
                                                            >
                                                                <Ban size={13} />
                                                                <span>
                                                                    {isClassBanned ? 'فعال‌سازی در کلاس' : 'بن از این کلاس'}
                                                                </span>
                                                            </button>

                                                            <button
                                                                onClick={() => handleRemoveFromClass(detailStudent, enr)}
                                                                className="p-1.5 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 transition-colors"
                                                                title="حذف کامل از دوره"
                                                            >
                                                                <Trash2 size={14} />
                                                            </button>
                                                        </div>
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* ─── MODAL 2: Send Message to Student (ارسال پیام) ─── */}
                {messageStudent && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-sm animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-3xl p-6 shadow-2xl space-y-4">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-800">
                                <div className="flex items-center gap-2.5">
                                    <div className="p-2.5 rounded-2xl bg-brand-500/20 text-brand-400">
                                        <MessageSquare size={20} />
                                    </div>
                                    <div>
                                        <h3 className="text-base font-bold text-white">
                                            ارسال پیام به «{messageStudent.name}»
                                        </h3>
                                        <p className="text-xs text-slate-400">
                                            گیرنده: {messageStudent.email}
                                        </p>
                                    </div>
                                </div>
                                <button
                                    onClick={() => setMessageStudent(null)}
                                    className="text-slate-400 hover:text-white"
                                >
                                    <X size={18} />
                                </button>
                            </div>

                            <form onSubmit={handleMessageSubmit} className="space-y-4 text-xs">
                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">
                                        موضوع یا عنوان پیام:
                                    </label>
                                    <input
                                        type="text"
                                        required
                                        value={messageForm.title}
                                        onChange={(e) => setMessageForm({ ...messageForm, title: e.target.value })}
                                        placeholder="مثال: تذکر در خصوص حضور در کلاس یا اطلاعیه آزمون..."
                                        className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">
                                        اولویت پیام:
                                    </label>
                                    <div className="grid grid-cols-3 gap-2">
                                        {[
                                            { key: 'normal', label: 'عادی' },
                                            { key: 'important', label: 'مهم' },
                                            { key: 'warning', label: 'اخطار انضباطی' },
                                        ].map((p) => (
                                            <button
                                                key={p.key}
                                                type="button"
                                                onClick={() => setMessageForm({ ...messageForm, priority: p.key })}
                                                className={`py-2 px-3 rounded-xl border font-semibold transition-all ${
                                                    messageForm.priority === p.key
                                                        ? 'bg-brand-500 text-white border-brand-500 shadow-md shadow-brand-500/20'
                                                        : 'bg-slate-950 text-slate-400 border-slate-800 hover:bg-slate-800'
                                                }`}
                                            >
                                                {p.label}
                                            </button>
                                        ))}
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">
                                        متن پیام:
                                    </label>
                                    <textarea
                                        rows={5}
                                        required
                                        value={messageForm.message}
                                        onChange={(e) => setMessageForm({ ...messageForm, message: e.target.value })}
                                        placeholder="پیام یا توضیحات لازم را اینجا بنویسید..."
                                        className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div className="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                                    <button
                                        type="button"
                                        onClick={() => setMessageStudent(null)}
                                        className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium"
                                    >
                                        انصراف
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={isSubmitting || !messageForm.title || !messageForm.message}
                                        className="px-5 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 disabled:opacity-50 text-white font-medium flex items-center gap-1.5 shadow-md shadow-brand-500/20"
                                    >
                                        <Send size={14} />
                                        <span>{isSubmitting ? 'در حال ارسال...' : 'ارسال پیام به دانشجو'}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* ─── MODAL 3: Account Ban / Status Change ─── */}
                {statusModalStudent && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-sm animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4">
                            <div className="flex items-center gap-3 text-rose-400 pb-3 border-b border-slate-800">
                                <div className="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/30">
                                    <Ban size={22} />
                                </div>
                                <div>
                                    <h3 className="text-base font-bold text-white">
                                        تغییر وضعیت حساب «{statusModalStudent.name}»
                                    </h3>
                                    <p className="text-xs text-slate-400">کنترل دسترسی کلی دانشجو به سامانه</p>
                                </div>
                            </div>

                            <form onSubmit={handleStatusSubmit} className="space-y-4 text-xs">
                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">
                                        وضعیت حساب جدید:
                                    </label>
                                    <div className="grid grid-cols-3 gap-2">
                                        {[
                                            { key: 'active', label: 'فعال', color: 'emerald' },
                                            { key: 'suspended', label: 'تعلیق موقت', color: 'amber' },
                                            { key: 'banned', label: 'مسدود (بن)', color: 'rose' },
                                        ].map((opt) => (
                                            <button
                                                key={opt.key}
                                                type="button"
                                                onClick={() => setStatusForm({ ...statusForm, status: opt.key })}
                                                className={`py-2 px-3 rounded-xl border font-semibold transition-all ${
                                                    statusForm.status === opt.key
                                                        ? opt.key === 'active'
                                                            ? 'bg-emerald-600 text-white border-emerald-500'
                                                            : 'bg-rose-600 text-white border-rose-500'
                                                        : 'bg-slate-950 text-slate-400 border-slate-800'
                                                }`}
                                            >
                                                {opt.label}
                                            </button>
                                        ))}
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">
                                        دلیل یا توضیحات (به دانشجو اعلام می‌شود):
                                    </label>
                                    <textarea
                                        rows={3}
                                        value={statusForm.reason}
                                        onChange={(e) => setStatusForm({ ...statusForm, reason: e.target.value })}
                                        placeholder="مثال: عدم رعایت مقررات، تاخیر مکرر یا تذکر اداری..."
                                        className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div className="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                                    <button
                                        type="button"
                                        onClick={() => setStatusModalStudent(null)}
                                        className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium"
                                    >
                                        انصراف
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={isSubmitting}
                                        className={`px-5 py-2 rounded-xl text-white font-medium flex items-center gap-1.5 shadow-md ${
                                            statusForm.status === 'active'
                                                ? 'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-600/20'
                                                : 'bg-rose-600 hover:bg-rose-500 shadow-rose-600/20'
                                        }`}
                                    >
                                        <Check size={14} />
                                        <span>{isSubmitting ? 'در حال ثبت...' : 'ثبت تغییر وضعیت'}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* ─── MODAL 4: Class/Course Ban Modal (بن از کلاس مشخص) ─── */}
                {classBanModalData && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-sm animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4">
                            <div className="flex items-center gap-3 text-amber-400 pb-3 border-b border-slate-800">
                                <div className="p-3 rounded-2xl bg-amber-500/20 border border-amber-500/30">
                                    <AlertTriangle size={22} />
                                </div>
                                <div>
                                    <h3 className="text-base font-bold text-white">
                                        تغییر وضعیت در کلاس «{classBanModalData.enrollment.course_title}»
                                    </h3>
                                    <p className="text-xs text-slate-400">
                                        دانشجو: {classBanModalData.student.name}
                                    </p>
                                </div>
                            </div>

                            <form onSubmit={handleClassStatusSubmit} className="space-y-4 text-xs">
                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">
                                        وضعیت حضور در این کلاس:
                                    </label>
                                    <div className="grid grid-cols-3 gap-2">
                                        {[
                                            { key: 'active', label: 'فعال در کلاس' },
                                            { key: 'suspended', label: 'تعلیق موقت' },
                                            { key: 'banned', label: 'محروم / بن' },
                                        ].map((opt) => (
                                            <button
                                                key={opt.key}
                                                type="button"
                                                onClick={() => setClassStatusForm({ ...classStatusForm, status: opt.key })}
                                                className={`py-2 px-3 rounded-xl border font-semibold transition-all ${
                                                    classStatusForm.status === opt.key
                                                        ? opt.key === 'active'
                                                            ? 'bg-emerald-600 text-white border-emerald-500'
                                                            : 'bg-rose-600 text-white border-rose-500'
                                                        : 'bg-slate-950 text-slate-400 border-slate-800'
                                                }`}
                                            >
                                                {opt.label}
                                            </button>
                                        ))}
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">
                                        دلیل یا توضیحات برای دانشجو:
                                    </label>
                                    <textarea
                                        rows={3}
                                        value={classStatusForm.reason}
                                        onChange={(e) => setClassStatusForm({ ...classStatusForm, reason: e.target.value })}
                                        placeholder="توضیحاتی که برای دانشجو پیامک/اعلان خواهد شد..."
                                        className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div className="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                                    <button
                                        type="button"
                                        onClick={() => setClassBanModalData(null)}
                                        className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium"
                                    >
                                        انصراف
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={isSubmitting}
                                        className="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-1.5 shadow-md shadow-amber-600/20"
                                    >
                                        <Check size={14} />
                                        <span>{isSubmitting ? 'در حال ثبت...' : 'ثبت وضعیت کلاس'}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* ─── MODAL 5: Bulk Message ─── */}
                {bulkActionModal === 'message' && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-sm animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-3xl p-6 shadow-2xl space-y-4">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-800">
                                <div className="flex items-center gap-2.5">
                                    <div className="p-2.5 rounded-2xl bg-brand-500/20 text-brand-400">
                                        <Mail size={20} />
                                    </div>
                                    <div>
                                        <h3 className="text-base font-bold text-white">ارسال پیام گروهی</h3>
                                        <p className="text-xs text-slate-400">
                                            ارسال پیام به {selectedIds.length} دانشجوی انتخاب‌شده
                                        </p>
                                    </div>
                                </div>
                                <button
                                    onClick={() => setBulkActionModal(null)}
                                    className="text-slate-400 hover:text-white"
                                >
                                    <X size={18} />
                                </button>
                            </div>

                            <form
                                onSubmit={(e) => {
                                    e.preventDefault();
                                    handleBulkSubmit('message');
                                }}
                                className="space-y-4 text-xs"
                            >
                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">
                                        عنوان پیام:
                                    </label>
                                    <input
                                        type="text"
                                        required
                                        value={bulkForm.title}
                                        onChange={(e) => setBulkForm({ ...bulkForm, title: e.target.value })}
                                        placeholder="عنوان اطلاعیه عمومی..."
                                        className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">
                                        متن پیام:
                                    </label>
                                    <textarea
                                        rows={5}
                                        required
                                        value={bulkForm.message}
                                        onChange={(e) => setBulkForm({ ...bulkForm, message: e.target.value })}
                                        placeholder="متن پیام گروهی را وارد کنید..."
                                        className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div className="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                                    <button
                                        type="button"
                                        onClick={() => setBulkActionModal(null)}
                                        className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium"
                                    >
                                        انصراف
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={isSubmitting || !bulkForm.title || !bulkForm.message}
                                        className="px-5 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 disabled:opacity-50 text-white font-medium flex items-center gap-1.5 shadow-md shadow-brand-500/20"
                                    >
                                        <Send size={14} />
                                        <span>{isSubmitting ? 'در حال ارسال...' : 'ارسال همگانی'}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}
            </div>
        </AdminLayout>
    );
}
