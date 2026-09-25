import React, { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    UserCheck,
    UserX,
    CheckCircle2,
    AlertTriangle,
    Mail,
    Send,
    Eye,
    BookOpen,
    GraduationCap,
    Briefcase,
    Calendar,
    Award,
    Sparkles,
    X,
    Clock,
    Plus,
    Edit3,
    Trash2,
    ExternalLink,
    Download,
    Search,
    Filter,
    RotateCcw,
    FileText,
    Globe,
    ShieldCheck,
    ShieldAlert,
    Star,
    LayoutGrid,
    List,
    Phone,
    Users,
    Check,
    AlertCircle,
    Info,
    Layers,
    UserPlus,
} from 'lucide-react';

export default function Index({
    teachers,
    departments = [],
    stats = {},
    filters = {},
}) {
    // Filters & View State
    const [search, setSearch] = useState(filters.search || '');
    const [department, setDepartment] = useState(filters.department || 'all');
    const [verification, setVerification] = useState(filters.verification || 'all');
    const [status, setStatus] = useState(filters.status || 'all');
    const [sortBy, setSortBy] = useState(filters.sort_by || 'created_at');
    const [sortDir, setSortDir] = useState(filters.sort_dir || 'desc');
    const [viewMode, setViewMode] = useState('grid'); // 'grid' | 'table'

    // Selection
    const [selectedIds, setSelectedIds] = useState([]);

    // Modals
    const [detailTeacher, setDetailTeacher] = useState(null);
    const [messageTeacher, setMessageTeacher] = useState(null);
    const [isCreateOpen, setIsCreateOpen] = useState(false);
    const [editTeacher, setEditTeacher] = useState(null);
    const [verifyModalTeacher, setVerifyModalTeacher] = useState(null);
    const [rejectModalTeacher, setRejectModalTeacher] = useState(null);
    const [deleteTeacher, setDeleteTeacher] = useState(null);
    const [bulkActionModal, setBulkActionModal] = useState(null);

    // Form States
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [createForm, setCreateForm] = useState({
        name: '',
        email: '',
        password: '',
        phone: '',
        department: '',
        specialization: '',
        expertise: '',
        years_of_experience: 1,
        bio: '',
        linkedin: '',
        github: '',
        website: '',
        is_verified: true,
        cv_file: null,
        avatar_file: null,
    });

    const [editForm, setEditForm] = useState({
        name: '',
        email: '',
        password: '',
        phone: '',
        department: '',
        specialization: '',
        expertise: '',
        years_of_experience: 0,
        bio: '',
        linkedin: '',
        github: '',
        website: '',
        cv_file: null,
        avatar_file: null,
    });

    const [messageForm, setMessageForm] = useState({ title: '', message: '', priority: 'normal' });
    const [rejectReason, setRejectReason] = useState('');
    const [bulkForm, setBulkForm] = useState({ title: '', message: '' });

    // Apply Filter Helper
    const applyFilters = (overrides = {}) => {
        const query = {
            search,
            department,
            verification,
            status,
            sort_by: sortBy,
            sort_dir: sortDir,
            ...overrides,
        };

        Object.keys(query).forEach((key) => {
            if (query[key] === '' || query[key] === null || query[key] === 'all') {
                delete query[key];
            }
        });

        router.get('/admin/teachers', query, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        applyFilters({ search });
    };

    const handleResetFilters = () => {
        setSearch('');
        setDepartment('all');
        setVerification('all');
        setStatus('all');
        setSortBy('created_at');
        setSortDir('desc');
        router.get('/admin/teachers', {}, { preserveState: true, preserveScroll: true });
    };

    // Selection Handlers
    const toggleSelect = (id) => {
        setSelectedIds((prev) =>
            prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id]
        );
    };

    const selectAll = () => {
        const list = teachers.data || [];
        if (selectedIds.length === list.length) {
            setSelectedIds([]);
        } else {
            setSelectedIds(list.map((t) => t.id));
        }
    };

    // ─── Actions ───

    // Create Teacher Submit
    const handleCreateSubmit = (e) => {
        e.preventDefault();
        setIsSubmitting(true);

        const formData = new FormData();
        Object.keys(createForm).forEach((key) => {
            if (createForm[key] !== null && createForm[key] !== undefined) {
                formData.append(key, createForm[key]);
            }
        });

        router.post('/admin/teachers', formData, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                setIsSubmitting(false);
                setIsCreateOpen(false);
                setCreateForm({
                    name: '',
                    email: '',
                    password: '',
                    phone: '',
                    department: '',
                    specialization: '',
                    expertise: '',
                    years_of_experience: 1,
                    bio: '',
                    linkedin: '',
                    github: '',
                    website: '',
                    is_verified: true,
                    cv_file: null,
                    avatar_file: null,
                });
            },
            onError: () => setIsSubmitting(false),
        });
    };

    // Edit Teacher Submit
    const handleEditSubmit = (e) => {
        e.preventDefault();
        if (!editTeacher) return;
        setIsSubmitting(true);

        const formData = new FormData();
        Object.keys(editForm).forEach((key) => {
            if (editForm[key] !== null && editForm[key] !== undefined && editForm[key] !== '') {
                formData.append(key, editForm[key]);
            }
        });

        router.post(`/admin/teachers/${editTeacher.id}`, formData, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                setIsSubmitting(false);
                setEditTeacher(null);
            },
            onError: () => setIsSubmitting(false),
        });
    };

    // Open Edit Modal
    const openEditModal = (teacher) => {
        setEditTeacher(teacher);
        setEditForm({
            name: teacher.name,
            email: teacher.email,
            password: '',
            phone: teacher.phone !== '-' ? teacher.phone : '',
            department: teacher.department !== 'نامشخص' ? teacher.department : '',
            specialization: teacher.specialization,
            expertise: teacher.expertise_raw,
            years_of_experience: teacher.years_of_experience,
            bio: teacher.bio || '',
            linkedin: teacher.linkedin || '',
            github: teacher.github || '',
            website: teacher.website || '',
            cv_file: null,
            avatar_file: null,
        });
    };

    // Verify Teacher Submit
    const handleVerifySubmit = (teacher) => {
        setIsSubmitting(true);
        router.post(
            `/admin/teachers/${teacher.id}/verify`,
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsSubmitting(false);
                    setVerifyModalTeacher(null);
                    if (detailTeacher?.id === teacher.id) {
                        setDetailTeacher((prev) => ({ ...prev, is_verified: true, status: 'active' }));
                    }
                },
                onError: () => setIsSubmitting(false),
            }
        );
    };

    // Reject Teacher Submit
    const handleRejectSubmit = (e) => {
        e.preventDefault();
        if (!rejectModalTeacher) return;
        setIsSubmitting(true);

        router.post(
            `/admin/teachers/${rejectModalTeacher.id}/reject`,
            { reason: rejectReason },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsSubmitting(false);
                    setRejectModalTeacher(null);
                    setRejectReason('');
                    if (detailTeacher?.id === rejectModalTeacher.id) {
                        setDetailTeacher((prev) => ({ ...prev, is_verified: false, status: 'rejected' }));
                    }
                },
                onError: () => setIsSubmitting(false),
            }
        );
    };

    // Send Message Submit
    const handleMessageSubmit = (e) => {
        e.preventDefault();
        if (!messageTeacher || !messageForm.title || !messageForm.message) return;
        setIsSubmitting(true);

        router.post(
            `/admin/teachers/${messageTeacher.id}/message`,
            messageForm,
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsSubmitting(false);
                    setMessageTeacher(null);
                    setMessageForm({ title: '', message: '', priority: 'normal' });
                },
                onError: () => setIsSubmitting(false),
            }
        );
    };

    // Delete Teacher Submit
    const handleDeleteSubmit = () => {
        if (!deleteTeacher) return;
        setIsSubmitting(true);

        router.delete(`/admin/teachers/${deleteTeacher.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                setIsSubmitting(false);
                setDeleteTeacher(null);
                if (detailTeacher?.id === deleteTeacher.id) {
                    setDetailTeacher(null);
                }
            },
            onError: () => setIsSubmitting(false),
        });
    };

    // Bulk Action Submit
    const handleBulkSubmit = (action) => {
        if (selectedIds.length === 0) return;
        setIsSubmitting(true);

        router.post(
            '/admin/teachers/bulk-action',
            {
                ids: selectedIds,
                action: action,
                title: bulkForm.title,
                message: bulkForm.message,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsSubmitting(false);
                    setSelectedIds([]);
                    setBulkActionModal(null);
                    setBulkForm({ title: '', message: '' });
                },
                onError: () => setIsSubmitting(false),
            }
        );
    };

    const teacherList = teachers.data || [];

    return (
        <AdminLayout title="مدیریت اساتید و مدرسان - پنل مدیریت ادورا">
            <div className="p-4 sm:p-6 lg:p-8 space-y-6">
                {/* ─── Top Banner & Summary Stats ─── */}
                <div className="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-900/90 to-slate-800/80 border border-slate-800 p-6 shadow-2xl">
                    <div className="absolute top-0 right-0 -mt-10 -mr-10 w-72 h-72 bg-brand-500/10 rounded-full blur-3xl pointer-events-none" />
                    <div className="absolute bottom-0 left-0 -mb-10 -ml-10 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl pointer-events-none" />

                    <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div className="space-y-2">
                            <div className="flex items-center gap-3">
                                <div className="p-3 rounded-2xl bg-brand-500/20 text-brand-400 border border-brand-500/30">
                                    <UserCheck size={28} />
                                </div>
                                <div>
                                    <h1 className="text-2xl sm:text-3xl font-black text-white tracking-tight">
                                        مدیریت اساتید و مدرسان
                                    </h1>
                                    <p className="text-sm text-slate-400">
                                        بررسی رزومه و تایید صلاحیت، پایش دوره‌ها و ارسال پیام به مدرسین
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Action: Add Teacher Button */}
                        <div className="flex items-center gap-3">
                            <button
                                onClick={() => setIsCreateOpen(true)}
                                className="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-brand-500 to-indigo-600 hover:from-brand-600 hover:to-indigo-700 text-white font-bold text-xs sm:text-sm transition-all shadow-lg shadow-brand-500/25 flex items-center gap-2 group"
                            >
                                <UserPlus size={18} className="group-hover:scale-110 transition-transform" />
                                <span>ثبت استاد جدید</span>
                            </button>
                        </div>
                    </div>

                    {/* Quick Stats Grid */}
                    <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 pt-6 mt-6 border-t border-slate-800/80">
                        <div className="bg-slate-950/60 rounded-2xl p-3.5 border border-slate-800 text-center">
                            <span className="text-xs text-slate-400 block mb-1">کل اساتید</span>
                            <span className="text-xl font-black text-white">
                                {(stats.total_teachers || 0).toLocaleString('fa-IR')}
                            </span>
                        </div>

                        <div className="bg-slate-950/60 rounded-2xl p-3.5 border border-slate-800 text-center">
                            <span className="text-xs text-slate-400 block mb-1">تاییدشده و رسمی</span>
                            <span className="text-xl font-black text-emerald-400">
                                {(stats.verified_teachers || 0).toLocaleString('fa-IR')}
                            </span>
                        </div>

                        <div className={`rounded-2xl p-3.5 border text-center transition-all ${
                            (stats.pending_teachers || 0) > 0
                                ? 'bg-amber-500/10 border-amber-500/40 animate-pulse'
                                : 'bg-slate-950/60 border-slate-800'
                        }`}>
                            <span className="text-xs text-amber-300 block mb-1 font-semibold">
                                در انتظار بررسی رزومه
                            </span>
                            <span className="text-xl font-black text-amber-400">
                                {(stats.pending_teachers || 0).toLocaleString('fa-IR')}
                            </span>
                        </div>

                        <div className="bg-slate-950/60 rounded-2xl p-3.5 border border-slate-800 text-center">
                            <span className="text-xs text-slate-400 block mb-1">ردشده / تعلیق</span>
                            <span className="text-xl font-black text-rose-400">
                                {(stats.rejected_teachers || 0).toLocaleString('fa-IR')}
                            </span>
                        </div>

                        <div className="bg-slate-950/60 rounded-2xl p-3.5 border border-slate-800 text-center">
                            <span className="text-xs text-slate-400 block mb-1">دوره‌های فعال</span>
                            <span className="text-xl font-black text-brand-400">
                                {(stats.total_courses || 0).toLocaleString('fa-IR')}
                            </span>
                        </div>

                        <div className="bg-slate-950/60 rounded-2xl p-3.5 border border-slate-800 text-center">
                            <span className="text-xs text-slate-400 block mb-1">دانشجویان تحت آموزش</span>
                            <span className="text-xl font-black text-indigo-400">
                                {(stats.total_students || 0).toLocaleString('fa-IR')}
                            </span>
                        </div>
                    </div>
                </div>

                {/* ─── Verification & Status Quick Tabs ─── */}
                <div className="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-thin">
                    {[
                        { key: 'all', label: 'همه اساتید', count: stats.total_teachers },
                        { key: 'pending', label: 'در انتظار تایید مدارک', count: stats.pending_teachers, badgeColor: 'bg-amber-500 text-slate-950 font-bold' },
                        { key: 'verified', label: 'اساتید تاییدشده', count: stats.verified_teachers },
                        { key: 'rejected', label: 'درخواست‌های ردشده', count: stats.rejected_teachers },
                    ].map((tab) => (
                        <button
                            key={tab.key}
                            onClick={() => {
                                setVerification(tab.key);
                                applyFilters({ verification: tab.key });
                            }}
                            className={`px-4 py-2 rounded-2xl text-xs font-semibold whitespace-nowrap transition-all flex items-center gap-2 shadow-sm ${
                                verification === tab.key
                                    ? 'bg-brand-500 text-white shadow-brand-500/25 ring-2 ring-brand-500/30'
                                    : 'bg-slate-900/80 hover:bg-slate-800 text-slate-300 border border-slate-800'
                            }`}
                        >
                            <span>{tab.label}</span>
                            {tab.count !== undefined && (
                                <span className={`px-2 py-0.5 rounded-full text-[10px] ${tab.badgeColor || 'bg-slate-950/60 text-slate-300'}`}>
                                    {tab.count}
                                </span>
                            )}
                        </button>
                    ))}
                </div>

                {/* ─── Filter & Search Control Bar ─── */}
                <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-900/50 p-4 rounded-3xl border border-slate-800/80 backdrop-blur-md">
                    {/* Search */}
                    <form onSubmit={handleSearchSubmit} className="relative flex-1 max-w-md">
                        <Search className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="جستجو نام استاد، ایمیل، مهارت، تخصص یا دپارتمان..."
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

                    {/* Filters */}
                    <div className="flex flex-wrap items-center gap-2.5 text-xs">
                        {/* Department Filter */}
                        <select
                            value={department}
                            onChange={(e) => {
                                setDepartment(e.target.value);
                                applyFilters({ department: e.target.value });
                            }}
                            className="py-2 px-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 text-xs focus:outline-none focus:border-brand-500"
                        >
                            <option value="all">همه دپارتمان‌ها</option>
                            {departments.map((dep, idx) => (
                                <option key={idx} value={dep}>
                                    دپارتمان: {dep}
                                </option>
                            ))}
                        </select>

                        {/* Account Status Filter */}
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
                            <option value="pending">در انتظار تایید</option>
                            <option value="suspended">تعلیق‌شده</option>
                            <option value="rejected">ردشده</option>
                        </select>

                        {/* Reset Filters */}
                        {(search || department !== 'all' || verification !== 'all' || status !== 'all') && (
                            <button
                                onClick={handleResetFilters}
                                title="پاکسازی فیلترها"
                                className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors"
                            >
                                <RotateCcw size={15} />
                            </button>
                        )}

                        {/* Grid / Table Layout Switcher */}
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

                {/* ─── Bulk Action Bar ─── */}
                {selectedIds.length > 0 && (
                    <div className="flex items-center justify-between p-4 rounded-2xl bg-brand-500/10 border border-brand-500/30 text-brand-300 text-sm animate-in fade-in">
                        <div className="flex items-center gap-3">
                            <CheckCircle2 size={18} className="text-brand-400 shrink-0" />
                            <span>
                                <strong>{selectedIds.length}</strong> استاد انتخاب شده است.
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
                                onClick={() => handleBulkSubmit('verify')}
                                className="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-medium flex items-center gap-1.5 shadow-sm"
                            >
                                <ShieldCheck size={14} />
                                <span>تایید صلاحیت همگانی</span>
                            </button>
                        </div>
                    </div>
                )}

                {/* ─── Teachers List ─── */}
                {teacherList.length === 0 ? (
                    <div className="flex flex-col items-center justify-center p-12 rounded-3xl bg-slate-900/40 border border-slate-800/80 text-center space-y-4">
                        <div className="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center text-slate-500">
                            <UserCheck size={32} />
                        </div>
                        <div className="space-y-1">
                            <h3 className="text-base font-bold text-slate-200">
                                استادی با این مشخصات یافت نشد!
                            </h3>
                            <p className="text-xs text-slate-400 max-w-sm">
                                فیلترهای جستجو، دپارتمان یا وضعیت تایید را تغییر دهید.
                            </p>
                        </div>
                        <button
                            onClick={handleResetFilters}
                            className="px-4 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-medium text-xs flex items-center gap-2 shadow-md transition-colors"
                        >
                            <RotateCcw size={15} />
                            <span>نمایش همه اساتید</span>
                        </button>
                    </div>
                ) : viewMode === 'grid' ? (
                    /* ─── Grid View (Cards) ─── */
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        {teacherList.map((teacher) => {
                            const isSelected = selectedIds.includes(teacher.id);
                            const isPending = !teacher.is_verified || teacher.status === 'pending';

                            return (
                                <div
                                    key={teacher.id}
                                    className={`group relative flex flex-col rounded-3xl bg-slate-900/80 border transition-all overflow-hidden p-5 space-y-4 shadow-sm ${
                                        isSelected
                                            ? 'border-brand-500 ring-2 ring-brand-500/30'
                                            : isPending
                                            ? 'border-amber-500/40 bg-amber-500/5'
                                            : 'border-slate-800/80 hover:border-slate-700 hover:shadow-xl'
                                    }`}
                                >
                                    {/* Top Card Row */}
                                    <div className="flex items-start justify-between gap-3">
                                        <div className="flex items-center gap-3">
                                            {/* Selection Checkbox */}
                                            <input
                                                type="checkbox"
                                                checked={isSelected}
                                                onChange={() => toggleSelect(teacher.id)}
                                                className="w-4 h-4 rounded border-slate-700 bg-slate-950 text-brand-500 focus:ring-brand-500/20 cursor-pointer"
                                            />

                                            {/* Avatar */}
                                            <div className="relative">
                                                {teacher.avatar ? (
                                                    <img
                                                        src={teacher.avatar}
                                                        alt={teacher.name}
                                                        className="w-12 h-12 rounded-2xl object-cover border border-slate-700"
                                                    />
                                                ) : (
                                                    <div className="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 text-white font-bold text-base flex items-center justify-center border border-slate-700">
                                                        {teacher.name.charAt(0)}
                                                    </div>
                                                )}

                                                {/* Verification Badge */}
                                                <span
                                                    className={`absolute -bottom-1 -left-1 p-0.5 rounded-full border-2 border-slate-900 ${
                                                        teacher.is_verified
                                                            ? 'bg-emerald-500 text-white'
                                                            : 'bg-amber-500 text-slate-950'
                                                    }`}
                                                    title={teacher.is_verified ? 'استاد رسمی و تاییدشده' : 'در انتظار بررسی مدارک'}
                                                >
                                                    {teacher.is_verified ? <Check size={10} /> : <Clock size={10} />}
                                                </span>
                                            </div>

                                            {/* Name & Specialization */}
                                            <div className="min-w-0">
                                                <h3
                                                    onClick={() => setDetailTeacher(teacher)}
                                                    className="text-sm font-bold text-white truncate hover:text-brand-300 cursor-pointer transition-colors"
                                                >
                                                    {teacher.name}
                                                </h3>
                                                <p className="text-[11px] text-brand-400 truncate">
                                                    {teacher.specialization}
                                                </p>
                                            </div>
                                        </div>

                                        {/* Status Pill */}
                                        <span
                                            className={`px-2.5 py-1 rounded-full text-[10px] font-bold border ${
                                                teacher.is_verified
                                                    ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'
                                                    : teacher.status === 'rejected'
                                                    ? 'bg-rose-500/10 text-rose-400 border-rose-500/30'
                                                    : 'bg-amber-500/10 text-amber-400 border-amber-500/30'
                                            }`}
                                        >
                                            {teacher.is_verified
                                                ? 'تاییدشده'
                                                : teacher.status === 'rejected'
                                                ? 'ردشده'
                                                : 'در انتظار بررسی'}
                                        </span>
                                    </div>

                                    {/* Department & Experience Bar */}
                                    <div className="grid grid-cols-2 gap-2 text-xs bg-slate-950/60 p-2.5 rounded-2xl border border-slate-800/60 text-slate-300">
                                        <div>
                                            <span className="text-[10px] text-slate-500 block">دپارتمان:</span>
                                            <span className="font-semibold truncate block">
                                                {teacher.department}
                                            </span>
                                        </div>
                                        <div>
                                            <span className="text-[10px] text-slate-500 block">سابقه تدریس:</span>
                                            <span className="font-semibold text-amber-400">
                                                {teacher.years_of_experience} سال
                                            </span>
                                        </div>
                                    </div>

                                    {/* Expertise Pills */}
                                    {teacher.expertise.length > 0 && (
                                        <div className="space-y-1">
                                            <span className="text-[10px] text-slate-400 font-semibold block">مهارت‌های اصلی:</span>
                                            <div className="flex flex-wrap gap-1 max-h-14 overflow-hidden">
                                                {teacher.expertise.slice(0, 4).map((exp, i) => (
                                                    <span
                                                        key={i}
                                                        className="px-2 py-0.5 rounded-lg bg-slate-800 text-slate-300 border border-slate-700 text-[10px]"
                                                    >
                                                        {exp}
                                                    </span>
                                                ))}
                                                {teacher.expertise.length > 4 && (
                                                    <span className="text-[10px] text-slate-500 py-0.5">
                                                        +{teacher.expertise.length - 4} بیشتر
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    )}

                                    {/* Courses Taught & Students count */}
                                    <div className="flex items-center justify-between text-xs text-slate-400 pt-2 border-t border-slate-800/80">
                                        <div className="flex items-center gap-1.5">
                                            <BookOpen size={13} className="text-brand-400" />
                                            <span>
                                                <strong>{teacher.courses.length}</strong> دوره
                                            </span>
                                        </div>
                                        <div className="flex items-center gap-1.5">
                                            <Users size={13} className="text-emerald-400" />
                                            <span>
                                                <strong>{teacher.total_students}</strong> شاگرد
                                            </span>
                                        </div>
                                    </div>

                                    {/* Card Action Buttons */}
                                    <div className="flex items-center justify-between pt-2 border-t border-slate-800/80 gap-1.5">
                                        <button
                                            onClick={() => setDetailTeacher(teacher)}
                                            className="flex-1 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium flex items-center justify-center gap-1 transition-colors"
                                        >
                                            <Eye size={13} />
                                            <span>پرونده و رزومه</span>
                                        </button>

                                        {/* Verify / Approve Button if pending */}
                                        {!teacher.is_verified && (
                                            <button
                                                onClick={() => setVerifyModalTeacher(teacher)}
                                                className="p-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white transition-colors"
                                                title="تایید صلاحیت و مدارک استاد"
                                            >
                                                <ShieldCheck size={14} />
                                            </button>
                                        )}

                                        <button
                                            onClick={() => setMessageTeacher(teacher)}
                                            className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors"
                                            title="ارسال پیام به استاد"
                                        >
                                            <Mail size={14} />
                                        </button>

                                        <button
                                            onClick={() => openEditModal(teacher)}
                                            className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors"
                                            title="ویرایش مشخصات"
                                        >
                                            <Edit3 size={14} />
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
                                                checked={selectedIds.length > 0 && selectedIds.length === teacherList.length}
                                                onChange={selectAll}
                                                className="w-4 h-4 rounded border-slate-700 bg-slate-900 text-brand-500 focus:ring-brand-500/20 cursor-pointer"
                                            />
                                        </th>
                                        <th className="p-4">استاد / مدرس</th>
                                        <th className="p-4">تخصص و دپارتمان</th>
                                        <th className="p-4">رزومه CV</th>
                                        <th className="p-4">سابقه کاری</th>
                                        <th className="p-4">دوره‌ها و شاگردان</th>
                                        <th className="p-4">وضعیت تایید</th>
                                        <th className="p-4 text-center">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-800/60">
                                    {teacherList.map((teacher) => {
                                        const isSelected = selectedIds.includes(teacher.id);

                                        return (
                                            <tr
                                                key={teacher.id}
                                                className={`hover:bg-slate-800/40 transition-colors ${
                                                    isSelected ? 'bg-brand-500/5' : ''
                                                }`}
                                            >
                                                <td className="p-4 text-center">
                                                    <input
                                                        type="checkbox"
                                                        checked={isSelected}
                                                        onChange={() => toggleSelect(teacher.id)}
                                                        className="w-4 h-4 rounded border-slate-700 bg-slate-900 text-brand-500 focus:ring-brand-500/20 cursor-pointer"
                                                    />
                                                </td>
                                                <td className="p-4">
                                                    <div
                                                        className="flex items-center gap-3 cursor-pointer"
                                                        onClick={() => setDetailTeacher(teacher)}
                                                    >
                                                        {teacher.avatar ? (
                                                            <img
                                                                src={teacher.avatar}
                                                                alt={teacher.name}
                                                                className="w-9 h-9 rounded-xl object-cover border border-slate-700"
                                                            />
                                                        ) : (
                                                            <div className="w-9 h-9 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center">
                                                                {teacher.name.charAt(0)}
                                                            </div>
                                                        )}
                                                        <div>
                                                            <span className="font-bold text-slate-100 hover:text-brand-300 block">
                                                                {teacher.name}
                                                            </span>
                                                            <span className="text-[11px] text-slate-500 font-mono">
                                                                {teacher.email}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="p-4">
                                                    <span className="font-semibold text-slate-200 block">
                                                        {teacher.specialization}
                                                    </span>
                                                    <span className="text-[11px] text-slate-400">
                                                        دپارتمان: {teacher.department}
                                                    </span>
                                                </td>
                                                <td className="p-4">
                                                    {teacher.cv_url ? (
                                                        <a
                                                            href={teacher.cv_url}
                                                            target="_blank"
                                                            rel="noreferrer"
                                                            className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 transition-colors"
                                                        >
                                                            <FileText size={13} />
                                                            <span>دانلود رزومه PDF</span>
                                                        </a>
                                                    ) : (
                                                        <span className="text-slate-500 italic">بدون رزومه</span>
                                                    )}
                                                </td>
                                                <td className="p-4 font-semibold text-slate-200">
                                                    {teacher.years_of_experience} سال
                                                </td>
                                                <td className="p-4">
                                                    <span className="text-slate-200 font-bold">
                                                        {teacher.courses.length} دوره
                                                    </span>
                                                    <span className="text-[11px] text-slate-400 block">
                                                        {teacher.total_students} دانشجو
                                                    </span>
                                                </td>
                                                <td className="p-4">
                                                    <span
                                                        className={`px-2.5 py-1 rounded-full text-[10px] font-bold border ${
                                                            teacher.is_verified
                                                                ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'
                                                                : 'bg-amber-500/10 text-amber-400 border-amber-500/30'
                                                        }`}
                                                    >
                                                        {teacher.is_verified ? 'تاییدشده' : 'در انتظار بررسی'}
                                                    </span>
                                                </td>
                                                <td className="p-4">
                                                    <div className="flex items-center justify-center gap-1.5">
                                                        <button
                                                            onClick={() => setDetailTeacher(teacher)}
                                                            className="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-slate-200"
                                                            title="مشاهده پرونده کامل"
                                                        >
                                                            <Eye size={15} />
                                                        </button>

                                                        {!teacher.is_verified && (
                                                            <button
                                                                onClick={() => setVerifyModalTeacher(teacher)}
                                                                className="p-1.5 rounded-lg hover:bg-emerald-500/20 text-emerald-400"
                                                                title="تایید مدارک"
                                                            >
                                                                <ShieldCheck size={15} />
                                                            </button>
                                                        )}

                                                        <button
                                                            onClick={() => setMessageTeacher(teacher)}
                                                            className="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-slate-200"
                                                            title="ارسال پیام"
                                                        >
                                                            <Mail size={15} />
                                                        </button>

                                                        <button
                                                            onClick={() => openEditModal(teacher)}
                                                            className="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-slate-200"
                                                            title="ویرایش"
                                                        >
                                                            <Edit3 size={15} />
                                                        </button>

                                                        <button
                                                            onClick={() => setDeleteTeacher(teacher)}
                                                            className="p-1.5 rounded-lg hover:bg-rose-500/20 text-slate-400 hover:text-rose-400"
                                                            title="حذف استاد"
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

                {/* ─── Pagination ─── */}
                {teachers.links && teachers.links.length > 3 && (
                    <div className="flex items-center justify-center gap-1.5 pt-4">
                        {teachers.links.map((link, idx) => (
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

                {/* ─── MODAL 1: Full Teacher Dossier & CV Modal ─── */}
                {detailTeacher && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-950/85 backdrop-blur-md animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-4xl rounded-3xl overflow-hidden shadow-2xl flex flex-col max-h-[92vh]">
                            {/* Modal Header */}
                            <div className="flex items-center justify-between p-5 border-b border-slate-800 bg-slate-950/50">
                                <div className="flex items-center gap-3">
                                    {detailTeacher.avatar ? (
                                        <img
                                            src={detailTeacher.avatar}
                                            alt={detailTeacher.name}
                                            className="w-14 h-14 rounded-2xl object-cover border border-slate-700"
                                        />
                                    ) : (
                                        <div className="w-14 h-14 rounded-2xl bg-indigo-600 text-white font-bold text-xl flex items-center justify-center">
                                            {detailTeacher.name.charAt(0)}
                                        </div>
                                    )}
                                    <div>
                                        <div className="flex items-center gap-2">
                                            <h2 className="text-lg font-black text-white">{detailTeacher.name}</h2>
                                            <span
                                                className={`px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${
                                                    detailTeacher.is_verified
                                                        ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'
                                                        : 'bg-amber-500/10 text-amber-400 border-amber-500/30'
                                                }`}
                                            >
                                                {detailTeacher.is_verified ? 'تاییدشده و رسمی' : 'در انتظار بررسی مدارک'}
                                            </span>
                                        </div>
                                        <p className="text-xs text-brand-400 font-semibold mt-0.5">
                                            {detailTeacher.specialization} | دپارتمان {detailTeacher.department}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-center gap-2">
                                    {!detailTeacher.is_verified && (
                                        <>
                                            <button
                                                onClick={() => setVerifyModalTeacher(detailTeacher)}
                                                className="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-1.5 shadow-md shadow-emerald-600/20"
                                            >
                                                <ShieldCheck size={15} />
                                                <span>تایید مدارک و صلاحیت</span>
                                            </button>
                                            <button
                                                onClick={() => setRejectModalTeacher(detailTeacher)}
                                                className="px-3.5 py-2 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 text-xs font-medium"
                                            >
                                                رد درخواست
                                            </button>
                                        </>
                                    )}

                                    <button
                                        onClick={() => setMessageTeacher(detailTeacher)}
                                        className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium"
                                        title="ارسال پیام"
                                    >
                                        <Mail size={16} />
                                    </button>

                                    <button
                                        onClick={() => setDetailTeacher(null)}
                                        className="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800"
                                    >
                                        <X size={18} />
                                    </button>
                                </div>
                            </div>

                            {/* Dossier Content */}
                            <div className="p-6 overflow-y-auto space-y-6 flex-1 text-xs">
                                {/* 1. Bio & About */}
                                {detailTeacher.bio && (
                                    <div className="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-1.5">
                                        <h4 className="text-xs font-bold text-slate-300">بیوگرافی و معرفی استاد:</h4>
                                        <p className="text-slate-300 leading-relaxed whitespace-pre-line text-xs">
                                            {detailTeacher.bio}
                                        </p>
                                    </div>
                                )}

                                {/* 2. Key Info Cards */}
                                <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                                    <div>
                                        <span className="text-slate-500 block mb-0.5">ایمیل سازمانی:</span>
                                        <span className="text-slate-200 font-mono">{detailTeacher.email}</span>
                                    </div>
                                    <div>
                                        <span className="text-slate-500 block mb-0.5">شماره تماس:</span>
                                        <span className="text-slate-200 font-mono">{detailTeacher.phone}</span>
                                    </div>
                                    <div>
                                        <span className="text-slate-500 block mb-0.5">سابقه کاری:</span>
                                        <span className="text-amber-400 font-bold">{detailTeacher.years_of_experience} سال سابقه</span>
                                    </div>
                                    <div>
                                        <span className="text-slate-500 block mb-0.5">تاریخ عضویت:</span>
                                        <span className="text-slate-200">{detailTeacher.registered_date}</span>
                                    </div>
                                </div>

                                {/* 3. CV & Professional Profiles */}
                                <div className="space-y-3">
                                    <h4 className="text-xs font-bold text-slate-300 flex items-center gap-1.5">
                                        <FileText size={15} className="text-brand-400" />
                                        <span>فایل رزومه کاری (CV) و صفحات تخصصی</span>
                                    </h4>

                                    <div className="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        {detailTeacher.cv_url ? (
                                            <div className="flex items-center gap-3">
                                                <div className="p-3 rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                                    <FileText size={24} />
                                                </div>
                                                <div>
                                                    <span className="font-bold text-white block text-sm">
                                                        {detailTeacher.cv_file_name || 'فایل رزومه استاد (PDF)'}
                                                    </span>
                                                    <span className="text-[11px] text-slate-400">
                                                        فایل رسمی آپلودشده برای بررسی صلاحیت
                                                    </span>
                                                </div>
                                            </div>
                                        ) : (
                                            <span className="text-slate-500 italic">
                                                فایل رزومه آپلود نشده است.
                                            </span>
                                        )}

                                        <div className="flex items-center gap-2">
                                            {detailTeacher.cv_url && (
                                                <a
                                                    href={detailTeacher.cv_url}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    className="px-4 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-medium text-xs flex items-center gap-1.5 shadow-md shadow-brand-500/20"
                                                >
                                                    <Download size={14} />
                                                    <span>مشاهده و دانلود رزومه</span>
                                                </a>
                                            )}

                                            {detailTeacher.linkedin && (
                                                <a
                                                    href={detailTeacher.linkedin}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    className="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-blue-400 font-bold text-xs"
                                                    title="LinkedIn"
                                                >
                                                    LinkedIn
                                                </a>
                                            )}

                                            {detailTeacher.github && (
                                                <a
                                                    href={detailTeacher.github}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    className="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs"
                                                    title="GitHub"
                                                >
                                                    GitHub
                                                </a>
                                            )}

                                            {detailTeacher.website && (
                                                <a
                                                    href={detailTeacher.website}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-400"
                                                    title="Website"
                                                >
                                                    <Globe size={16} />
                                                </a>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                {/* 4. Taught Courses */}
                                <div className="space-y-3">
                                    <h4 className="text-xs font-bold text-slate-300 flex items-center gap-1.5">
                                        <BookOpen size={15} className="text-brand-400" />
                                        <span>دوره‌های تدریسی تحت نظر این استاد ({detailTeacher.courses.length})</span>
                                    </h4>

                                    {detailTeacher.courses.length === 0 ? (
                                        <div className="p-6 rounded-2xl bg-slate-950/60 border border-slate-800 text-center text-slate-500 italic">
                                            این استاد تا کنون دوره‌ای در سامانه ثبت نکرده است.
                                        </div>
                                    ) : (
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            {detailTeacher.courses.map((course) => (
                                                <div
                                                    key={course.id}
                                                    className="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800/80 flex items-center gap-3"
                                                >
                                                    {course.thumbnail ? (
                                                        <img
                                                            src={course.thumbnail}
                                                            alt={course.title}
                                                            className="w-12 h-12 rounded-xl object-cover border border-slate-700 shrink-0"
                                                        />
                                                    ) : (
                                                        <div className="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center text-brand-400 shrink-0">
                                                            <BookOpen size={20} />
                                                        </div>
                                                    )}
                                                    <div className="min-w-0 flex-1">
                                                        <span className="font-bold text-white truncate block">
                                                            {course.title}
                                                        </span>
                                                        <div className="flex items-center justify-between text-[11px] text-slate-400 mt-1">
                                                            <span>دسته: {course.category}</span>
                                                            <span className="text-brand-400 font-semibold font-mono">
                                                                {course.enrolled_count} دانشجو
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* ─── MODAL 2: Create Teacher ─── */}
                {isCreateOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-950/85 backdrop-blur-md animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-3xl overflow-hidden shadow-2xl flex flex-col max-h-[92vh]">
                            <div className="flex items-center justify-between p-5 border-b border-slate-800 bg-slate-950/50">
                                <div className="flex items-center gap-2.5">
                                    <div className="p-2.5 rounded-2xl bg-brand-500/20 text-brand-400">
                                        <UserPlus size={20} />
                                    </div>
                                    <div>
                                        <h3 className="text-base font-bold text-white">ثبت استاد / مدرس جدید</h3>
                                        <p className="text-xs text-slate-400">تعریف حساب کاربری و اطلاعات استادی</p>
                                    </div>
                                </div>
                                <button
                                    onClick={() => setIsCreateOpen(false)}
                                    className="text-slate-400 hover:text-white"
                                >
                                    <X size={18} />
                                </button>
                            </div>

                            <form onSubmit={handleCreateSubmit} className="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">نام و تخلص: *</label>
                                        <input
                                            type="text"
                                            required
                                            value={createForm.name}
                                            onChange={(e) => setCreateForm({ ...createForm, name: e.target.value })}
                                            placeholder="مثال: دکتر احمد محمدی"
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">ایمیل معتبر: *</label>
                                        <input
                                            type="email"
                                            required
                                            value={createForm.email}
                                            onChange={(e) => setCreateForm({ ...createForm, email: e.target.value })}
                                            placeholder="teacher@edvora.org"
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500 font-mono"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">رمز عبور: *</label>
                                        <input
                                            type="password"
                                            required
                                            value={createForm.password}
                                            onChange={(e) => setCreateForm({ ...createForm, password: e.target.value })}
                                            placeholder="حداقل ۸ کاراکتر"
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500 font-mono"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">شماره تماس:</label>
                                        <input
                                            type="text"
                                            value={createForm.phone}
                                            onChange={(e) => setCreateForm({ ...createForm, phone: e.target.value })}
                                            placeholder="07XXXXXXXX"
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500 font-mono"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">تخصص اصلی (Specialization): *</label>
                                        <input
                                            type="text"
                                            required
                                            value={createForm.specialization}
                                            onChange={(e) => setCreateForm({ ...createForm, specialization: e.target.value })}
                                            placeholder="مثال: توسعه‌دهنده فول‌استک یا هوش مصنوعی"
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">دپارتمان:</label>
                                        <input
                                            type="text"
                                            value={createForm.department}
                                            onChange={(e) => setCreateForm({ ...createForm, department: e.target.value })}
                                            placeholder="مثال: کامپیوتر، طراحی، زبان"
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">سابقه کاری (سال):</label>
                                        <input
                                            type="number"
                                            min="0"
                                            max="50"
                                            value={createForm.years_of_experience}
                                            onChange={(e) => setCreateForm({ ...createForm, years_of_experience: parseInt(e.target.value) || 0 })}
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500 font-mono"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">فایل رزومه کاری (CV):</label>
                                        <input
                                            type="file"
                                            accept=".pdf,.doc,.docx"
                                            onChange={(e) => setCreateForm({ ...createForm, cv_file: e.target.files[0] })}
                                            className="w-full text-slate-400 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:bg-slate-800 file:text-slate-200 text-xs cursor-pointer"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">مهارت‌های کلیدی (با کاما جدا کنید):</label>
                                    <input
                                        type="text"
                                        value={createForm.expertise}
                                        onChange={(e) => setCreateForm({ ...createForm, expertise: e.target.value })}
                                        placeholder="Python, React, Node.js, Docker"
                                        className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">بیوگرافی و معرفی کامل:</label>
                                    <textarea
                                        rows={3}
                                        value={createForm.bio}
                                        onChange={(e) => setCreateForm({ ...createForm, bio: e.target.value })}
                                        placeholder="سوابق، افتخارات، تجربیات تدریس و..."
                                        className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div className="flex items-center gap-2 pt-2">
                                    <input
                                        type="checkbox"
                                        id="is_verified_create"
                                        checked={createForm.is_verified}
                                        onChange={(e) => setCreateForm({ ...createForm, is_verified: e.target.checked })}
                                        className="w-4 h-4 rounded border-slate-700 bg-slate-950 text-brand-500"
                                    />
                                    <label htmlFor="is_verified_create" className="text-slate-300 font-semibold cursor-pointer">
                                        تایید مستقیم صلاحیت و فعال‌سازی فوری حساب استاد
                                    </label>
                                </div>

                                <div className="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                                    <button
                                        type="button"
                                        onClick={() => setIsCreateOpen(false)}
                                        className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium"
                                    >
                                        انصراف
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={isSubmitting}
                                        className="px-5 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 disabled:opacity-50 text-white font-bold flex items-center gap-1.5 shadow-md shadow-brand-500/20"
                                    >
                                        <Check size={14} />
                                        <span>{isSubmitting ? 'در حال ثبت...' : 'ثبت استاد در سیستم'}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* ─── MODAL 3: Edit Teacher ─── */}
                {editTeacher && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-950/85 backdrop-blur-md animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-3xl overflow-hidden shadow-2xl flex flex-col max-h-[92vh]">
                            <div className="flex items-center justify-between p-5 border-b border-slate-800 bg-slate-950/50">
                                <div className="flex items-center gap-2.5">
                                    <div className="p-2.5 rounded-2xl bg-brand-500/20 text-brand-400">
                                        <Edit3 size={20} />
                                    </div>
                                    <div>
                                        <h3 className="text-base font-bold text-white">ویرایش مشخصات «{editTeacher.name}»</h3>
                                        <p className="text-xs text-slate-400">به‌روزرسانی تخصص، رزومه و اطلاعات تماس</p>
                                    </div>
                                </div>
                                <button
                                    onClick={() => setEditTeacher(null)}
                                    className="text-slate-400 hover:text-white"
                                >
                                    <X size={18} />
                                </button>
                            </div>

                            <form onSubmit={handleEditSubmit} className="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">نام و تخلص: *</label>
                                        <input
                                            type="text"
                                            required
                                            value={editForm.name}
                                            onChange={(e) => setEditForm({ ...editForm, name: e.target.value })}
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">ایمیل: *</label>
                                        <input
                                            type="email"
                                            required
                                            value={editForm.email}
                                            onChange={(e) => setEditForm({ ...editForm, email: e.target.value })}
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500 font-mono"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">تغییر رمز عبور (اختیاری):</label>
                                        <input
                                            type="password"
                                            value={editForm.password}
                                            onChange={(e) => setEditForm({ ...editForm, password: e.target.value })}
                                            placeholder="تنها در صورت تغییر وارد کنید"
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500 font-mono"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">شماره تماس:</label>
                                        <input
                                            type="text"
                                            value={editForm.phone}
                                            onChange={(e) => setEditForm({ ...editForm, phone: e.target.value })}
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500 font-mono"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">تخصص اصلی: *</label>
                                        <input
                                            type="text"
                                            required
                                            value={editForm.specialization}
                                            onChange={(e) => setEditForm({ ...editForm, specialization: e.target.value })}
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">دپارتمان:</label>
                                        <input
                                            type="text"
                                            value={editForm.department}
                                            onChange={(e) => setEditForm({ ...editForm, department: e.target.value })}
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">سال‌های سابقه کاری:</label>
                                        <input
                                            type="number"
                                            min="0"
                                            max="50"
                                            value={editForm.years_of_experience}
                                            onChange={(e) => setEditForm({ ...editForm, years_of_experience: parseInt(e.target.value) || 0 })}
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500 font-mono"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">جایگزینی رزومه (CV):</label>
                                        <input
                                            type="file"
                                            accept=".pdf,.doc,.docx"
                                            onChange={(e) => setEditForm({ ...editForm, cv_file: e.target.files[0] })}
                                            className="w-full text-slate-400 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:bg-slate-800 file:text-slate-200 text-xs cursor-pointer"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">مهارت‌های کلیدی:</label>
                                    <input
                                        type="text"
                                        value={editForm.expertise}
                                        onChange={(e) => setEditForm({ ...editForm, expertise: e.target.value })}
                                        className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">لینکدین (LinkedIn):</label>
                                        <input
                                            type="url"
                                            value={editForm.linkedin}
                                            onChange={(e) => setEditForm({ ...editForm, linkedin: e.target.value })}
                                            placeholder="https://linkedin.com/in/..."
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500 font-mono"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">گیت‌هاب (GitHub):</label>
                                        <input
                                            type="url"
                                            value={editForm.github}
                                            onChange={(e) => setEditForm({ ...editForm, github: e.target.value })}
                                            placeholder="https://github.com/..."
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500 font-mono"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-slate-300 font-semibold mb-1">وب‌سایت شخصی:</label>
                                        <input
                                            type="url"
                                            value={editForm.website}
                                            onChange={(e) => setEditForm({ ...editForm, website: e.target.value })}
                                            placeholder="https://..."
                                            className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500 font-mono"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">بیوگرافی:</label>
                                    <textarea
                                        rows={3}
                                        value={editForm.bio}
                                        onChange={(e) => setEditForm({ ...editForm, bio: e.target.value })}
                                        className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div className="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                                    <button
                                        type="button"
                                        onClick={() => setEditTeacher(null)}
                                        className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium"
                                    >
                                        انصراف
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={isSubmitting}
                                        className="px-5 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 disabled:opacity-50 text-white font-bold flex items-center gap-1.5 shadow-md shadow-brand-500/20"
                                    >
                                        <Check size={14} />
                                        <span>{isSubmitting ? 'در حال ذخیره...' : 'ذخیره تغییرات'}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* ─── MODAL 4: Verify Teacher Confirmation ─── */}
                {verifyModalTeacher && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-sm animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4">
                            <div className="flex items-center gap-3 text-emerald-400">
                                <div className="p-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/30">
                                    <ShieldCheck size={24} />
                                </div>
                                <div>
                                    <h3 className="text-base font-bold text-white">تایید صلاحیت و مدارک استاد</h3>
                                    <p className="text-xs text-slate-400">استاد: {verifyModalTeacher.name}</p>
                                </div>
                            </div>

                            <p className="text-xs text-slate-300 leading-relaxed">
                                آیا از بررسی رزومه و تایید صلاحیت تدریس <strong className="text-white">{verifyModalTeacher.name}</strong> اطمینان دارید؟ با تایید، حساب کاربری وی فوراً فعال شده و اعلان رسمی به پنل او ارسال می‌گردد.
                            </p>

                            <div className="flex items-center justify-end gap-2 pt-2">
                                <button
                                    onClick={() => setVerifyModalTeacher(null)}
                                    className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium"
                                >
                                    انصراف
                                </button>
                                <button
                                    onClick={() => handleVerifySubmit(verifyModalTeacher)}
                                    disabled={isSubmitting}
                                    className="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-1.5 shadow-md shadow-emerald-600/20"
                                >
                                    <Check size={14} />
                                    <span>{isSubmitting ? 'در حال ثبت...' : 'بله، تایید و فعال کن'}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                )}

                {/* ─── MODAL 5: Reject Teacher Modal ─── */}
                {rejectModalTeacher && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-sm animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4">
                            <div className="flex items-center gap-3 text-rose-400">
                                <div className="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/30">
                                    <ShieldAlert size={24} />
                                </div>
                                <div>
                                    <h3 className="text-base font-bold text-white">رد درخواست تدریس</h3>
                                    <p className="text-xs text-slate-400">مدرس: {rejectModalTeacher.name}</p>
                                </div>
                            </div>

                            <form onSubmit={handleRejectSubmit} className="space-y-4 text-xs">
                                <div>
                                    <label className="block text-slate-300 font-semibold mb-1">
                                        علت رد درخواست (برای مدرس اعلان می‌شود):
                                    </label>
                                    <textarea
                                        rows={3}
                                        value={rejectReason}
                                        onChange={(e) => setRejectReason(e.target.value)}
                                        placeholder="مثال: عدم تطابق سوابق کاری، ناقص بودن رزومه و..."
                                        className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div className="flex items-center justify-end gap-2 pt-2">
                                    <button
                                        type="button"
                                        onClick={() => setRejectModalTeacher(null)}
                                        className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium"
                                    >
                                        انصراف
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={isSubmitting}
                                        className="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold flex items-center gap-1.5 shadow-md shadow-rose-600/20"
                                    >
                                        <X size={14} />
                                        <span>{isSubmitting ? 'در حال ثبت...' : 'رد درخواست تدریس'}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* ─── MODAL 6: Send Message to Teacher ─── */}
                {messageTeacher && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-sm animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-3xl p-6 shadow-2xl space-y-4">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-800">
                                <div className="flex items-center gap-2.5">
                                    <div className="p-2.5 rounded-2xl bg-brand-500/20 text-brand-400">
                                        <Mail size={20} />
                                    </div>
                                    <div>
                                        <h3 className="text-base font-bold text-white">
                                            ارسال پیام به «{messageTeacher.name}»
                                        </h3>
                                        <p className="text-xs text-slate-400 font-mono">
                                            گیرنده: {messageTeacher.email}
                                        </p>
                                    </div>
                                </div>
                                <button
                                    onClick={() => setMessageTeacher(null)}
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
                                        placeholder="اطلاعیه تقویم آموزشی، برگزاری آزمون و..."
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
                                            { key: 'warning', label: 'اخطار مدیریتی' },
                                        ].map((p) => (
                                            <button
                                                key={p.key}
                                                type="button"
                                                onClick={() => setMessageForm({ ...messageForm, priority: p.key })}
                                                className={`py-2 px-3 rounded-xl border font-semibold transition-all ${
                                                    messageForm.priority === p.key
                                                        ? 'bg-brand-500 text-white border-brand-500 shadow-md shadow-brand-500/20'
                                                        : 'bg-slate-950 text-slate-400 border-slate-800'
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
                                        placeholder="متن پیام برای استاد..."
                                        className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div className="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                                    <button
                                        type="button"
                                        onClick={() => setMessageTeacher(null)}
                                        className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium"
                                    >
                                        انصراف
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={isSubmitting || !messageForm.title || !messageForm.message}
                                        className="px-5 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 disabled:opacity-50 text-white font-bold flex items-center gap-1.5 shadow-md shadow-brand-500/20"
                                    >
                                        <Send size={14} />
                                        <span>{isSubmitting ? 'در حال ارسال...' : 'ارسال پیام'}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* ─── MODAL 7: Delete Teacher Confirmation ─── */}
                {deleteTeacher && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-sm animate-in fade-in">
                        <div className="bg-slate-900 border border-slate-800 w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4">
                            <div className="flex items-center gap-3 text-rose-400">
                                <div className="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/30">
                                    <AlertTriangle size={24} />
                                </div>
                                <div>
                                    <h3 className="text-base font-bold text-white">حذف حساب استاد</h3>
                                    <p className="text-xs text-slate-400">این عملیات غیرقابل بازگشت است.</p>
                                </div>
                            </div>

                            <p className="text-xs text-slate-300 leading-relaxed">
                                آیا از حذف حساب کاربری و پرونده تدریس <strong className="text-white">{deleteTeacher.name}</strong> مطمئن هستید؟
                            </p>

                            <div className="flex items-center justify-end gap-2 pt-2">
                                <button
                                    onClick={() => setDeleteTeacher(null)}
                                    className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium"
                                >
                                    انصراف
                                </button>
                                <button
                                    onClick={handleDeleteSubmit}
                                    disabled={isSubmitting}
                                    className="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold flex items-center gap-1.5 shadow-md shadow-rose-600/20"
                                >
                                    <Trash2 size={14} />
                                    <span>{isSubmitting ? 'در حال حذف...' : 'بله، حذف کن'}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AdminLayout>
    );
}
