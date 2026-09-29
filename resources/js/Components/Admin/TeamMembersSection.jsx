import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import {
    Users,
    UserPlus,
    QrCode,
    ExternalLink,
    Edit3,
    Trash2,
    Phone,
    Mail,
    Send,
    CheckCircle2,
    Copy,
    Download,
    X,
    Upload,
    Sparkles,
    ShieldCheck,
    Check,
    Contact
} from 'lucide-react';

export default function TeamMembersSection({ teamMembers = [] }) {
    const [selectedMemberForQr, setSelectedMemberForQr] = useState(null);
    const [editingMember, setEditingMember] = useState(null);
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [copiedUrl, setCopiedUrl] = useState(false);

    // Form state for Add / Edit
    const initialForm = {
        name: '',
        role_title: '',
        department: '',
        email: '',
        phone: '',
        telegram: '',
        linkedin: '',
        github: '',
        bio: '',
        employee_id: '',
        status: 'active',
        avatar_file: null,
        avatar_url: '',
    };
    const [form, setForm] = useState(initialForm);
    const [previewAvatar, setPreviewAvatar] = useState(null);
    const [submitting, setSubmitting] = useState(false);
    const [errors, setErrors] = useState({});

    const handleOpenAdd = () => {
        setEditingMember(null);
        setForm(initialForm);
        setPreviewAvatar(null);
        setErrors({});
        setIsAddModalOpen(true);
    };

    const handleOpenEdit = (member) => {
        setEditingMember(member);
        setForm({
            name: member.name || '',
            role_title: member.role_title || '',
            department: member.department || '',
            email: member.email || '',
            phone: member.phone || '',
            telegram: member.telegram || '',
            linkedin: member.linkedin || '',
            github: member.github || '',
            bio: member.bio || '',
            employee_id: member.employee_id || '',
            status: member.status || 'active',
            avatar_file: null,
            avatar_url: member.avatar && member.avatar.startsWith('http') ? member.avatar : '',
        });
        setPreviewAvatar(member.avatar_url || null);
        setErrors({});
        setIsAddModalOpen(true);
    };

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setForm((prev) => ({ ...prev, avatar_file: file }));
            setPreviewAvatar(URL.createObjectURL(file));
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setSubmitting(true);
        setErrors({});

        const formData = new FormData();
        Object.keys(form).forEach((key) => {
            if (form[key] !== null && form[key] !== undefined && form[key] !== '') {
                formData.append(key, form[key]);
            }
        });

        if (editingMember) {
            router.post(`/admin/team-members/${editingMember.id}`, formData, {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    setIsAddModalOpen(false);
                    setSubmitting(false);
                },
                onError: (errs) => {
                    setErrors(errs);
                    setSubmitting(false);
                },
            });
        } else {
            router.post('/admin/team-members', formData, {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    setIsAddModalOpen(false);
                    setSubmitting(false);
                },
                onError: (errs) => {
                    setErrors(errs);
                    setSubmitting(false);
                },
            });
        }
    };

    const handleDelete = (member) => {
        if (confirm(`آیا از حذف عضو «${member.name}» از تیم اطمینان دارید؟ کیو‌آر کد و کارت دیجیتال ایشان نیز غیرفعال خواهد شد.`)) {
            router.delete(`/admin/team-members/${member.id}`, {
                preserveScroll: true,
            });
        }
    };

    const handleCopyUrl = (url) => {
        navigator.clipboard.writeText(url).then(() => {
            setCopiedUrl(true);
            setTimeout(() => setCopiedUrl(false), 2000);
        });
    };

    const downloadQrAsPng = (member) => {
        if (!member.qr_data_uri) return;
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = 600;
            canvas.height = 600;
            const ctx = canvas.getContext('2d');

            // Draw white background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Draw QR code with padding
            ctx.drawImage(img, 40, 40, 520, 520);

            // Export PNG
            const a = document.createElement('a');
            a.download = `edvora-qr-${member.employee_id || member.id}.png`;
            a.href = canvas.toDataURL('image/png');
            a.click();
        };
        img.src = member.qr_data_uri;
    };

    return (
        <div id="team-members" className="space-y-4 scroll-mt-6">
            {/* Header & Controls */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900/60 backdrop-blur-xl p-5 rounded-3xl border border-slate-800/80 shadow-xl">
                <div className="space-y-1">
                    <div className="flex items-center gap-2">
                        <div className="p-2 rounded-xl bg-gradient-to-tr from-brand-600 to-accent-500 text-white shadow-glow">
                            <Users size={18} />
                        </div>
                        <h2 className="text-lg font-black text-white font-display">
                            اعضای تیم و سیستم کیو‌آر کد اختصاصی
                        </h2>
                        <span className="text-xs px-2.5 py-0.5 rounded-full bg-brand-500/10 text-brand-400 font-bold border border-brand-500/20">
                            {teamMembers.length} هم‌تیمی
                        </span>
                    </div>
                    <p className="text-xs text-slate-400">
                        معرفی هم‌تیمی‌ها، تولید اتوماتیک QR کد اختصاصی و صفحه احراز هویت دیجیتال برای اسکن سریع
                    </p>
                </div>

                <div className="flex items-center gap-2">
                    <button
                        onClick={handleOpenAdd}
                        className="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-gradient-to-r from-brand-600 to-teal-500 hover:from-brand-500 hover:to-teal-400 text-white text-xs font-bold shadow-glow transition-all active:scale-95"
                    >
                        <UserPlus size={15} />
                        <span>افزودن هم‌تیمی جدید</span>
                    </button>
                </div>
            </div>

            {/* Empty State */}
            {teamMembers.length === 0 ? (
                <div className="rounded-3xl p-12 bg-slate-900/40 border border-slate-800/60 text-center space-y-4">
                    <div className="w-16 h-16 rounded-2xl bg-brand-500/10 text-brand-400 flex items-center justify-center mx-auto border border-brand-500/20">
                        <QrCode size={32} />
                    </div>
                    <div className="space-y-1">
                        <h3 className="text-base font-bold text-white">هنوز عضوی در تیم ثبت نشده است</h3>
                        <p className="text-xs text-slate-400 max-w-md mx-auto">
                            با کلیک بر روی دکمه زیر اولین هم‌تیمی را اضافه کنید تا سیستم بلافاصله یک QR کد هوشمند و صفحه کارت ویزیت دیجیتال برای او صادر نماید.
                        </p>
                    </div>
                    <button
                        onClick={handleOpenAdd}
                        className="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-glow transition-all"
                    >
                        <UserPlus size={15} />
                        <span>افزودن اولین عضو تیم</span>
                    </button>
                </div>
            ) : (
                /* Members Grid */
                <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    {teamMembers.map((member) => (
                        <div
                            key={member.id}
                            className="group rounded-3xl p-5 bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 hover:border-brand-500/40 shadow-xl transition-all flex flex-col justify-between relative overflow-hidden"
                        >
                            {/* Subtle Ambient Hover Glow */}
                            <div className="absolute top-0 right-0 w-32 h-32 bg-brand-500/5 rounded-full blur-2xl group-hover:bg-brand-500/15 transition-all pointer-events-none" />

                            <div className="space-y-4 relative z-10">
                                {/* Top Header: Avatar & Info */}
                                <div className="flex items-start justify-between gap-3">
                                    <div className="flex items-center gap-3 min-w-0">
                                        <div className="relative shrink-0">
                                            <img
                                                src={member.avatar_url}
                                                alt={member.name}
                                                className="w-13 h-13 rounded-2xl object-cover border-2 border-slate-700 bg-slate-800 shadow-md"
                                                onError={(e) => {
                                                    e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(member.name)}&background=0d9488&color=ffffff&size=128`;
                                                }}
                                            />
                                            {member.status === 'active' ? (
                                                <span className="absolute -bottom-1 -left-1 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-slate-900" title="عضو فعال" />
                                            ) : (
                                                <span className="absolute -bottom-1 -left-1 w-3.5 h-3.5 rounded-full bg-slate-500 border-2 border-slate-900" title="غیرفعال" />
                                            )}
                                        </div>

                                        <div className="min-w-0">
                                            <div className="flex items-center gap-1.5">
                                                <h3 className="text-sm font-black text-white truncate font-display">
                                                    {member.name}
                                                </h3>
                                                <ShieldCheck size={14} className="text-accent-400 shrink-0" title="شناسه معتبر" />
                                            </div>
                                            <p className="text-xs font-semibold text-brand-400 truncate">
                                                {member.role_title}
                                            </p>
                                            <div className="flex items-center gap-2 mt-1">
                                                {member.department && (
                                                    <span className="text-[10px] px-2 py-0.5 rounded-md bg-slate-800 text-slate-300 border border-slate-700/60 truncate">
                                                        {member.department}
                                                    </span>
                                                )}
                                                {member.employee_id && (
                                                    <span className="text-[10px] px-1.5 py-0.5 rounded-md bg-slate-800/80 font-mono text-slate-400 border border-slate-700/50" dir="ltr">
                                                        {member.employee_id}
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Mini Interactive QR Badge */}
                                    <button
                                        type="button"
                                        onClick={() => setSelectedMemberForQr(member)}
                                        className="shrink-0 p-2 rounded-2xl bg-slate-800/80 hover:bg-brand-500/20 text-slate-300 hover:text-brand-400 border border-slate-700/60 hover:border-brand-500/40 transition shadow-sm group/qr flex flex-col items-center gap-1"
                                        title="نمایش و دریافت QR کد"
                                    >
                                        <QrCode size={20} className="group-hover/qr:scale-110 transition-transform" />
                                        <span className="text-[9px] font-bold">QR کد</span>
                                    </button>
                                </div>

                                {/* Bio Snippet */}
                                {member.bio && (
                                    <p className="text-xs text-slate-400 line-clamp-2 leading-relaxed bg-slate-950/30 p-2.5 rounded-xl border border-slate-800/50">
                                        {member.bio}
                                    </p>
                                )}

                                {/* Contact Mini Badges */}
                                <div className="flex flex-wrap items-center gap-2 pt-1">
                                    {member.phone && (
                                        <a
                                            href={`tel:${member.phone}`}
                                            className="inline-flex items-center gap-1 text-[11px] text-slate-400 hover:text-emerald-400 bg-slate-800/40 px-2 py-1 rounded-lg border border-slate-800 transition"
                                            title="تماس"
                                        >
                                            <Phone size={11} className="text-emerald-400" />
                                            <span dir="ltr" className="font-mono">{member.phone}</span>
                                        </a>
                                    )}
                                    {member.email && (
                                        <a
                                            href={`mailto:${member.email}`}
                                            className="inline-flex items-center gap-1 text-[11px] text-slate-400 hover:text-sky-400 bg-slate-800/40 px-2 py-1 rounded-lg border border-slate-800 transition"
                                            title="ایمیل"
                                        >
                                            <Mail size={11} className="text-sky-400" />
                                            <span className="truncate max-w-[140px] font-mono">{member.email}</span>
                                        </a>
                                    )}
                                    {member.telegram && (
                                        <a
                                            href={`https://t.me/${member.telegram.replace('@', '')}`}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="inline-flex items-center gap-1 text-[11px] text-slate-400 hover:text-blue-400 bg-slate-800/40 px-2 py-1 rounded-lg border border-slate-800 transition"
                                            title="تلگرام"
                                        >
                                            <Send size={11} className="text-blue-400" />
                                            <span>@{member.telegram.replace('@', '')}</span>
                                        </a>
                                    )}
                                </div>
                            </div>

                            {/* Card Actions Bar */}
                            <div className="pt-4 mt-4 border-t border-slate-800/70 flex items-center justify-between gap-2 relative z-10">
                                <button
                                    onClick={() => setSelectedMemberForQr(member)}
                                    className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-500/10 hover:bg-brand-500/20 text-brand-400 text-xs font-bold border border-brand-500/20 transition active:scale-95"
                                >
                                    <QrCode size={13} />
                                    <span>دریافت QR</span>
                                </button>

                                <div className="flex items-center gap-1.5">
                                    <a
                                        href={member.public_url}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="p-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition"
                                        title="مشاهده صفحه اسکن شده هم‌تیمی"
                                    >
                                        <ExternalLink size={14} />
                                    </a>
                                    <button
                                        onClick={() => handleOpenEdit(member)}
                                        className="p-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition"
                                        title="ویرایش مشخصات"
                                    >
                                        <Edit3 size={14} />
                                    </button>
                                    <button
                                        onClick={() => handleDelete(member)}
                                        className="p-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 transition"
                                        title="حذف هم‌تیمی"
                                    >
                                        <Trash2 size={14} />
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}

            {/* ─── MODAL 1: Dedicated QR Code View & Export ─── */}
            {selectedMemberForQr && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-in fade-in duration-200">
                    <div className="w-full max-w-md rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl overflow-hidden relative space-y-0">
                        {/* Header */}
                        <div className="p-5 bg-gradient-to-r from-brand-600 via-teal-600 to-accent-600 flex items-center justify-between text-white">
                            <div className="flex items-center gap-2">
                                <QrCode size={20} />
                                <h3 className="text-sm font-black font-display">
                                    کیو‌آر کد اختصاصی و کارت شناسایی
                                </h3>
                            </div>
                            <button
                                onClick={() => setSelectedMemberForQr(null)}
                                className="p-1 rounded-xl bg-black/20 hover:bg-black/40 text-white transition"
                            >
                                <X size={16} />
                            </button>
                        </div>

                        {/* Content */}
                        <div className="p-6 space-y-5">
                            {/* Member info preview */}
                            <div className="flex items-center gap-3 bg-slate-950/50 p-3 rounded-2xl border border-slate-800">
                                <img
                                    src={selectedMemberForQr.avatar_url}
                                    alt={selectedMemberForQr.name}
                                    className="w-12 h-12 rounded-xl object-cover border border-slate-700"
                                />
                                <div className="min-w-0">
                                    <div className="flex items-center gap-1.5">
                                        <h4 className="text-sm font-bold text-white truncate">
                                            {selectedMemberForQr.name}
                                        </h4>
                                        <ShieldCheck size={13} className="text-accent-400" />
                                    </div>
                                    <p className="text-xs text-brand-400">
                                        {selectedMemberForQr.role_title}
                                    </p>
                                    <p className="text-[10px] text-slate-400">
                                        {selectedMemberForQr.department || 'ادورا تک'}
                                    </p>
                                </div>
                            </div>

                            {/* Crisp QR Code Visual Display */}
                            <div className="p-6 rounded-3xl bg-white text-slate-900 flex flex-col items-center justify-center shadow-xl mx-auto w-fit border border-slate-200">
                                {selectedMemberForQr.qr_data_uri ? (
                                    <img
                                        src={selectedMemberForQr.qr_data_uri}
                                        alt={`QR Code - ${selectedMemberForQr.name}`}
                                        className="w-56 h-56 object-contain"
                                    />
                                ) : (
                                    <div className="w-56 h-56 flex items-center justify-center text-xs text-slate-400">
                                        درحال بارگذاری QR کد...
                                    </div>
                                )}
                                <span className="text-[10px] font-bold text-slate-600 mt-2 tracking-wide font-mono">
                                    {selectedMemberForQr.employee_id || selectedMemberForQr.uuid.substring(0, 13)}
                                </span>
                            </div>

                            {/* Explanation */}
                            <p className="text-center text-xs text-slate-300 leading-relaxed bg-brand-500/10 p-3 rounded-2xl border border-brand-500/20">
                                <Sparkles size={14} className="inline text-brand-400 ml-1" />
                                هر شخصی این کد را با دوربین موبایل اسکن کند، مستقیماً به صفحه پروفایل تاییدشده 
                                <strong className="text-brand-300 mx-1">«{selectedMemberForQr.name}»</strong>
                                منتقل شده و فقط مشخصات و راه‌های ارتباطی ایشان را مشاهده می‌کند.
                            </p>

                            {/* Public Link Copy Bar */}
                            <div className="flex items-center gap-2 bg-slate-950 p-2 rounded-2xl border border-slate-800">
                                <input
                                    type="text"
                                    readOnly
                                    value={selectedMemberForQr.public_url}
                                    className="flex-1 bg-transparent border-none text-xs text-slate-300 focus:outline-none px-2 font-mono"
                                    dir="ltr"
                                />
                                <button
                                    onClick={() => handleCopyUrl(selectedMemberForQr.public_url)}
                                    className="flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition active:scale-95 shrink-0"
                                >
                                    {copiedUrl ? (
                                        <>
                                            <Check size={13} className="text-emerald-400" />
                                            <span>کپی شد!</span>
                                        </>
                                    ) : (
                                        <>
                                            <Copy size={13} />
                                            <span>کپی لینک</span>
                                        </>
                                    )}
                                </button>
                            </div>

                            {/* Actions Buttons */}
                            <div className="grid grid-cols-2 gap-2.5">
                                <button
                                    onClick={() => downloadQrAsPng(selectedMemberForQr)}
                                    className="flex items-center justify-center gap-2 py-2.5 px-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold border border-slate-700 transition active:scale-95"
                                >
                                    <Download size={14} className="text-brand-400" />
                                    <span>دانلود تصویر QR (PNG)</span>
                                </button>

                                <a
                                    href={`/admin/team-members/${selectedMemberForQr.id}/qr-svg`}
                                    className="flex items-center justify-center gap-2 py-2.5 px-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold border border-slate-700 transition active:scale-95"
                                >
                                    <Download size={14} className="text-accent-400" />
                                    <span>دانلود برداری (SVG)</span>
                                </a>

                                <a
                                    href={selectedMemberForQr.vcard_url}
                                    className="flex items-center justify-center gap-2 py-2.5 px-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold border border-slate-700 transition active:scale-95"
                                >
                                    <Contact size={14} className="text-emerald-400" />
                                    <span>دانلود کارت مخاطب (vCard)</span>
                                </a>

                                <a
                                    href={selectedMemberForQr.public_url}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="flex items-center justify-center gap-2 py-2.5 px-3 rounded-2xl bg-gradient-to-r from-brand-600 to-teal-500 hover:from-brand-500 hover:to-teal-400 text-white text-xs font-bold transition active:scale-95 shadow-glow"
                                >
                                    <ExternalLink size={14} />
                                    <span>تست صفحه اسکن</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* ─── MODAL 2: Add or Edit Team Member ─── */}
            {isAddModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-in fade-in duration-200 overflow-y-auto">
                    <div className="w-full max-w-2xl rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl overflow-hidden my-8">
                        {/* Header */}
                        <div className="p-5 bg-gradient-to-r from-brand-600 to-accent-600 flex items-center justify-between text-white">
                            <div className="flex items-center gap-2">
                                <UserPlus size={18} />
                                <h3 className="text-sm font-black font-display">
                                    {editingMember ? `ویرایش هم‌تیمی: ${editingMember.name}` : 'افزودن عضو جدید به تیم'}
                                </h3>
                            </div>
                            <button
                                onClick={() => setIsAddModalOpen(false)}
                                className="p-1 rounded-xl bg-black/20 hover:bg-black/40 text-white transition"
                            >
                                <X size={16} />
                            </button>
                        </div>

                        {/* Form */}
                        <form onSubmit={handleSubmit} className="p-6 space-y-4">
                            {/* Avatar Picker & Preview */}
                            <div className="flex flex-col sm:flex-row items-center gap-4 bg-slate-950/50 p-4 rounded-2xl border border-slate-800">
                                <div className="relative shrink-0">
                                    <img
                                        src={previewAvatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(form.name || 'عضو')}&background=0d9488&color=ffffff&size=128`}
                                        alt="Avatar Preview"
                                        className="w-16 h-16 rounded-2xl object-cover border-2 border-slate-700 bg-slate-800"
                                    />
                                </div>
                                <div className="flex-1 space-y-1 text-right">
                                    <label className="text-xs font-bold text-white block">تصویر پرسنلی / آواتار</label>
                                    <p className="text-[11px] text-slate-400">
                                        تصویر عضو تیم را آپلود کنید یا آدرس اینترنتی تصویر را در زیر وارد نمایید.
                                    </p>
                                    <div className="flex items-center gap-2 pt-1">
                                        <label className="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition">
                                            <Upload size={13} />
                                            <span>انتخاب فایل عکس</span>
                                            <input
                                                type="file"
                                                accept="image/*"
                                                onChange={handleFileChange}
                                                className="hidden"
                                            />
                                        </label>
                                        <span className="text-[11px] text-slate-500">یا لینک مستقیم:</span>
                                    </div>
                                    <input
                                        type="url"
                                        placeholder="https://example.com/photo.jpg"
                                        value={form.avatar_url}
                                        onChange={(e) => {
                                            setForm({ ...form, avatar_url: e.target.value });
                                            if (e.target.value) setPreviewAvatar(e.target.value);
                                        }}
                                        className="w-full text-xs bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 mt-1"
                                        dir="ltr"
                                    />
                                </div>
                            </div>

                            {/* Row 1: Name & Role Title */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="space-y-1">
                                    <label className="text-xs font-bold text-slate-300">
                                        نام و نام خانوادگی <span className="text-rose-400">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        required
                                        placeholder="مثال: سارا محمدی"
                                        value={form.name}
                                        onChange={(e) => setForm({ ...form, name: e.target.value })}
                                        className="w-full text-xs bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500"
                                    />
                                    {errors.name && <p className="text-[11px] text-rose-400">{errors.name}</p>}
                                </div>

                                <div className="space-y-1">
                                    <label className="text-xs font-bold text-slate-300">
                                        سمت و نقش شغلی <span className="text-rose-400">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        required
                                        placeholder="مثال: مدیر فنی / برنامه‌نویس ارشد"
                                        value={form.role_title}
                                        onChange={(e) => setForm({ ...form, role_title: e.target.value })}
                                        className="w-full text-xs bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500"
                                    />
                                    {errors.role_title && <p className="text-[11px] text-rose-400">{errors.role_title}</p>}
                                </div>
                            </div>

                            {/* Row 2: Department & Employee ID */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="space-y-1">
                                    <label className="text-xs font-bold text-slate-300">دپارتمان / واحد سازمانی</label>
                                    <input
                                        type="text"
                                        placeholder="مثال: تیم توسعه نرم‌افزار، آموزش، روابط عمومی"
                                        value={form.department}
                                        onChange={(e) => setForm({ ...form, department: e.target.value })}
                                        className="w-full text-xs bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500"
                                    />
                                </div>

                                <div className="space-y-1">
                                    <label className="text-xs font-bold text-slate-300">کد پرسنلی (اختیاری)</label>
                                    <input
                                        type="text"
                                        placeholder="مثال: EDV-1024"
                                        value={form.employee_id}
                                        onChange={(e) => setForm({ ...form, employee_id: e.target.value })}
                                        className="w-full text-xs bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 font-mono"
                                        dir="ltr"
                                    />
                                </div>
                            </div>

                            {/* Row 3: Email & Phone */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="space-y-1">
                                    <label className="text-xs font-bold text-slate-300">پست الکترونیکی (ایمیل)</label>
                                    <input
                                        type="email"
                                        placeholder="name@edvora.org"
                                        value={form.email}
                                        onChange={(e) => setForm({ ...form, email: e.target.value })}
                                        className="w-full text-xs bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 font-mono"
                                        dir="ltr"
                                    />
                                </div>

                                <div className="space-y-1">
                                    <label className="text-xs font-bold text-slate-300">شماره تماس (جهت کارت ویزیت)</label>
                                    <input
                                        type="tel"
                                        placeholder="+98 912 345 6789"
                                        value={form.phone}
                                        onChange={(e) => setForm({ ...form, phone: e.target.value })}
                                        className="w-full text-xs bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 font-mono"
                                        dir="ltr"
                                    />
                                </div>
                            </div>

                            {/* Row 4: Telegram & LinkedIn */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="space-y-1">
                                    <label className="text-xs font-bold text-slate-300">آیدی تلگرام (بدون @)</label>
                                    <input
                                        type="text"
                                        placeholder="username"
                                        value={form.telegram}
                                        onChange={(e) => setForm({ ...form, telegram: e.target.value })}
                                        className="w-full text-xs bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 font-mono"
                                        dir="ltr"
                                    />
                                </div>

                                <div className="space-y-1">
                                    <label className="text-xs font-bold text-slate-300">لینک یا یوزرنیم لینکدین</label>
                                    <input
                                        type="text"
                                        placeholder="linkedin.com/in/username"
                                        value={form.linkedin}
                                        onChange={(e) => setForm({ ...form, linkedin: e.target.value })}
                                        className="w-full text-xs bg-slate-950 border border-slate-700/80 rounded-2xl px-3.5 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 font-mono"
                                        dir="ltr"
                                    />
                                </div>
                            </div>

                            {/* Row 5: Bio / Duties */}
                            <div className="space-y-1">
                                <label className="text-xs font-bold text-slate-300">بیوگرافی، مهارت‌ها و مسئولیت‌ها</label>
                                <textarea
                                    rows={2}
                                    placeholder="توضیح مختصری از نقش، مهارت‌ها و وظایف این عضو در ادورا تک..."
                                    value={form.bio}
                                    onChange={(e) => setForm({ ...form, bio: e.target.value })}
                                    className="w-full text-xs bg-slate-950 border border-slate-700/80 rounded-2xl p-3 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 resize-none leading-relaxed"
                                />
                            </div>

                            {/* Row 6: Status */}
                            <div className="flex items-center gap-4 bg-slate-950/40 p-3 rounded-2xl border border-slate-800">
                                <label className="text-xs font-bold text-slate-300">وضعیت عضویت:</label>
                                <div className="flex items-center gap-4">
                                    <label className="flex items-center gap-1.5 cursor-pointer text-xs text-white">
                                        <input
                                            type="radio"
                                            name="status"
                                            value="active"
                                            checked={form.status === 'active'}
                                            onChange={(e) => setForm({ ...form, status: e.target.value })}
                                            className="text-brand-500 focus:ring-brand-500 bg-slate-900"
                                        />
                                        <span>فعال در تیم</span>
                                    </label>
                                    <label className="flex items-center gap-1.5 cursor-pointer text-xs text-slate-400">
                                        <input
                                            type="radio"
                                            name="status"
                                            value="inactive"
                                            checked={form.status === 'inactive'}
                                            onChange={(e) => setForm({ ...form, status: e.target.value })}
                                            className="text-brand-500 focus:ring-brand-500 bg-slate-900"
                                        />
                                        <span>غیرفعال</span>
                                    </label>
                                </div>
                            </div>

                            {/* Footer Submit */}
                            <div className="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-800">
                                <button
                                    type="button"
                                    onClick={() => setIsAddModalOpen(false)}
                                    className="px-4 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition"
                                >
                                    انصراف
                                </button>
                                <button
                                    type="submit"
                                    disabled={submitting}
                                    className="flex items-center gap-2 px-6 py-2.5 rounded-2xl bg-gradient-to-r from-brand-600 to-teal-500 hover:from-brand-500 hover:to-teal-400 text-white text-xs font-bold shadow-glow transition active:scale-95 disabled:opacity-50"
                                >
                                    <CheckCircle2 size={15} />
                                    <span>{submitting ? 'درحال ذخیره‌سازی...' : (editingMember ? 'بروزرسانی هم‌تیمی' : 'افزودن و صدور QR کد')}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
