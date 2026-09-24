import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import StudentLayout from '@/Layouts/StudentLayout';
import { useLanguage } from '@/Context/LanguageContext';
import {
    User,
    MapPin,
    GraduationCap,
    PhoneCall,
    FileText,
    Camera,
    CheckCircle2,
    AlertCircle,
    Save,
    ShieldCheck
} from 'lucide-react';

export default function ProfileDetails({ profile }) {
    const { t, isRtl } = useLanguage();
    const { data, setData, post, processing, errors } = useForm({
        first_name: profile?.first_name || '',
        last_name: profile?.last_name || '',
        father_name: profile?.father_name || '',
        gender: profile?.gender || 'female',
        date_of_birth: profile?.date_of_birth || '',
        national_id: profile?.national_id || '',
        passport_number: profile?.passport_number || '',
        phone_number: profile?.phone_number || '',
        whatsapp_number: profile?.whatsapp_number || '',
        province: profile?.province || '',
        district: profile?.district || '',
        postal_code: profile?.postal_code || '',
        last_education_level: profile?.last_education_level || '',
        last_school_name: profile?.last_school_name || '',
        emergency_contact_name: profile?.emergency_contact_name || '',
        emergency_contact_phone: profile?.emergency_contact_phone || '',
        emergency_contact_relation: profile?.emergency_contact_relation || '',
        skills: profile?.skills || '',
        languages: profile?.languages || '',
        about_me: profile?.about_me || '',
        profile_photo: null,
    });

    const [previewPhoto, setPreviewPhoto] = React.useState(profile?.profile_photo_url || null);

    const handlePhotoChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('profile_photo', file);
            setPreviewPhoto(URL.createObjectURL(file));
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/student/profile-details', {
            forceFormData: true,
            preserveScroll: true,
        });
    };

    const educationLevels = [
        { value: 'Primary School', labelKey: 'edu_primary' },
        { value: 'Middle School', labelKey: 'edu_middle' },
        { value: '9th Grade', labelKey: 'edu_9th' },
        { value: '10th Grade', labelKey: 'edu_10th' },
        { value: '11th Grade', labelKey: 'edu_11th' },
        { value: '12th Grade', labelKey: 'edu_12th' },
        { value: 'Diploma', labelKey: 'edu_diploma' },
        { value: 'Associate Degree', labelKey: 'edu_associate' },
        { value: 'Bachelor', labelKey: 'edu_bachelor' },
        { value: 'Master', labelKey: 'edu_master' },
        { value: 'PhD', labelKey: 'edu_phd' }
    ];

    const relationships = [
        { value: 'Father', labelKey: 'rel_father' },
        { value: 'Mother', labelKey: 'rel_mother' },
        { value: 'Brother', labelKey: 'rel_brother' },
        { value: 'Sister', labelKey: 'rel_sister' },
        { value: 'Spouse', labelKey: 'rel_spouse' },
        { value: 'Uncle', labelKey: 'rel_uncle' },
        { value: 'Aunt', labelKey: 'rel_aunt' },
        { value: 'Friend', labelKey: 'rel_friend' },
        { value: 'Other', labelKey: 'rel_other' }
    ];

    return (
        <StudentLayout title={`${t('my_details')} - ${t('brand_title')}`}>
            <Head title={`${t('my_details')} - ${t('brand_title')}`} />

            <div className="space-y-8 max-w-5xl mx-auto pb-12">
                {/* Header Card */}
                <div className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 border border-slate-700/60 p-6 md:p-8 text-white shadow-xl shadow-slate-950/20">
                    <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <div className="flex items-center gap-2.5 mb-2">
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-500/10 text-brand-400 border border-brand-500/20">
                                    <ShieldCheck size={14} /> {t('portal_name')}
                                </span>
                            </div>
                            <h1 className="text-2xl md:text-3xl font-black tracking-tight text-white">
                                {t('details_hero_title')}
                            </h1>
                            <p className="text-slate-400 text-sm mt-1 max-w-xl">
                                {t('details_hero_desc')}
                            </p>
                        </div>

                        <div>
                            {profile?.is_complete ? (
                                <div className="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-semibold text-sm shadow-sm backdrop-blur-md">
                                    <CheckCircle2 size={18} className="text-emerald-400" />
                                    <span>{t('profile_complete_badge')}</span>
                                </div>
                            ) : (
                                <div className="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 font-semibold text-sm shadow-sm backdrop-blur-md">
                                    <AlertCircle size={18} className="text-amber-400" />
                                    <span>{t('profile_incomplete_badge')}</span>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Form */}
                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* 1. Personal Information */}
                    <div className="bg-white rounded-3xl border border-slate-200/80 p-6 md:p-8 shadow-sm">
                        <div className="flex items-center gap-3 pb-5 mb-6 border-b border-slate-100">
                            <div className="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                                <User size={20} />
                            </div>
                            <div>
                                <h2 className="text-lg font-bold text-slate-900">{t('section_personal_info')}</h2>
                                <p className="text-xs text-slate-500">{t('section_personal_info_desc')}</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('first_name')} <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.first_name}
                                    onChange={(e) => setData('first_name', e.target.value)}
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/20 ${errors.first_name ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:border-brand-500'}`}
                                    placeholder={isRtl ? 'مثال: فاطمه' : 'e.g. Fatima'}
                                    required
                                />
                                {errors.first_name && <p className="text-xs text-rose-500 mt-1">{errors.first_name}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('last_name')} <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.last_name}
                                    onChange={(e) => setData('last_name', e.target.value)}
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/20 ${errors.last_name ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:border-brand-500'}`}
                                    placeholder={isRtl ? 'مثال: احمدی' : 'e.g. Ahmadi'}
                                    required
                                />
                                {errors.last_name && <p className="text-xs text-rose-500 mt-1">{errors.last_name}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('father_name')} <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.father_name}
                                    onChange={(e) => setData('father_name', e.target.value)}
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/20 ${errors.father_name ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:border-brand-500'}`}
                                    placeholder={isRtl ? 'مثال: محمد' : 'e.g. Mohammad'}
                                    required
                                />
                                {errors.father_name && <p className="text-xs text-rose-500 mt-1">{errors.father_name}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('dob')} <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    value={data.date_of_birth}
                                    onChange={(e) => setData('date_of_birth', e.target.value)}
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/20 ${errors.date_of_birth ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:border-brand-500'}`}
                                    required
                                />
                                {errors.date_of_birth && <p className="text-xs text-rose-500 mt-1">{errors.date_of_birth}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('national_id')} <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.national_id}
                                    onChange={(e) => setData('national_id', e.target.value)}
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/20 ${errors.national_id ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:border-brand-500'}`}
                                    placeholder="1401-0123-45678"
                                    required
                                />
                                {errors.national_id && <p className="text-xs text-rose-500 mt-1">{errors.national_id}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('passport_number')}
                                </label>
                                <input
                                    type="text"
                                    value={data.passport_number}
                                    onChange={(e) => setData('passport_number', e.target.value)}
                                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"
                                    placeholder={isRtl ? 'اختیاری' : 'Optional'}
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('phone_number')} <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.phone_number}
                                    onChange={(e) => setData('phone_number', e.target.value)}
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/20 ${errors.phone_number ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:border-brand-500'}`}
                                    placeholder="0770123456"
                                    required
                                />
                                {errors.phone_number && <p className="text-xs text-rose-500 mt-1">{errors.phone_number}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('whatsapp_number')}
                                </label>
                                <input
                                    type="text"
                                    value={data.whatsapp_number}
                                    onChange={(e) => setData('whatsapp_number', e.target.value)}
                                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"
                                    placeholder="0770123456"
                                />
                            </div>
                        </div>
                    </div>

                    {/* 2. Address Information */}
                    <div className="bg-white rounded-3xl border border-slate-200/80 p-6 md:p-8 shadow-sm">
                        <div className="flex items-center gap-3 pb-5 mb-6 border-b border-slate-100">
                            <div className="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                                <MapPin size={20} />
                            </div>
                            <div>
                                <h2 className="text-lg font-bold text-slate-900">{t('section_address')}</h2>
                                <p className="text-xs text-slate-500">{t('section_address_desc')}</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('province')} <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.province}
                                    onChange={(e) => setData('province', e.target.value)}
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/20 ${errors.province ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:border-brand-500'}`}
                                    placeholder={isRtl ? 'مثال: کابل، هرات، بلخ...' : 'e.g. Kabul'}
                                    required
                                />
                                {errors.province && <p className="text-xs text-rose-500 mt-1">{errors.province}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('district')} <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.district}
                                    onChange={(e) => setData('district', e.target.value)}
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/20 ${errors.district ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:border-brand-500'}`}
                                    placeholder={isRtl ? 'مثال: ناحیه دهم' : 'e.g. District 10'}
                                    required
                                />
                                {errors.district && <p className="text-xs text-rose-500 mt-1">{errors.district}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('postal_code')}
                                </label>
                                <input
                                    type="text"
                                    value={data.postal_code}
                                    onChange={(e) => setData('postal_code', e.target.value)}
                                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"
                                    placeholder="1001"
                                />
                            </div>
                        </div>
                    </div>

                    {/* 3. Education Background */}
                    <div className="bg-white rounded-3xl border border-slate-200/80 p-6 md:p-8 shadow-sm">
                        <div className="flex items-center gap-3 pb-5 mb-6 border-b border-slate-100">
                            <div className="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                                <GraduationCap size={20} />
                            </div>
                            <div>
                                <h2 className="text-lg font-bold text-slate-900">{t('section_education')}</h2>
                                <p className="text-xs text-slate-500">{t('section_education_desc')}</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('last_education_level')} <span className="text-rose-500">*</span>
                                </label>
                                <select
                                    value={data.last_education_level}
                                    onChange={(e) => setData('last_education_level', e.target.value)}
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/20 ${errors.last_education_level ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:border-brand-500'}`}
                                    required
                                >
                                    <option value="">{t('select_level')}</option>
                                    {educationLevels.map((lvl) => (
                                        <option key={lvl.value} value={lvl.value}>
                                            {t(lvl.labelKey)}
                                        </option>
                                    ))}
                                </select>
                                {errors.last_education_level && <p className="text-xs text-rose-500 mt-1">{errors.last_education_level}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('last_school_name')}
                                </label>
                                <input
                                    type="text"
                                    value={data.last_school_name}
                                    onChange={(e) => setData('last_school_name', e.target.value)}
                                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"
                                    placeholder={isRtl ? 'مثال: لیسه معرفت کابل' : 'e.g. Kabul High School'}
                                />
                            </div>
                        </div>
                    </div>

                    {/* 4. Emergency Contact */}
                    <div className="bg-white rounded-3xl border border-slate-200/80 p-6 md:p-8 shadow-sm">
                        <div className="flex items-center gap-3 pb-5 mb-6 border-b border-slate-100">
                            <div className="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                                <PhoneCall size={20} />
                            </div>
                            <div>
                                <h2 className="text-lg font-bold text-slate-900">{t('section_emergency')}</h2>
                                <p className="text-xs text-slate-500">{t('section_emergency_desc')}</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('emergency_name')} <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.emergency_contact_name}
                                    onChange={(e) => setData('emergency_contact_name', e.target.value)}
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/20 ${errors.emergency_contact_name ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:border-brand-500'}`}
                                    placeholder={isRtl ? 'مثال: احمد' : 'e.g. Ahmad'}
                                    required
                                />
                                {errors.emergency_contact_name && <p className="text-xs text-rose-500 mt-1">{errors.emergency_contact_name}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('emergency_phone')} <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.emergency_contact_phone}
                                    onChange={(e) => setData('emergency_contact_phone', e.target.value)}
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/20 ${errors.emergency_contact_phone ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:border-brand-500'}`}
                                    placeholder="0790123456"
                                    required
                                />
                                {errors.emergency_contact_phone && <p className="text-xs text-rose-500 mt-1">{errors.emergency_contact_phone}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('emergency_relation')} <span className="text-rose-500">*</span>
                                </label>
                                <select
                                    value={data.emergency_contact_relation}
                                    onChange={(e) => setData('emergency_contact_relation', e.target.value)}
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500/20 ${errors.emergency_contact_relation ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200 focus:border-brand-500'}`}
                                    required
                                >
                                    <option value="">{t('select_relation')}</option>
                                    {relationships.map((rel) => (
                                        <option key={rel.value} value={rel.value}>
                                            {t(rel.labelKey)}
                                        </option>
                                    ))}
                                </select>
                                {errors.emergency_contact_relation && <p className="text-xs text-rose-500 mt-1">{errors.emergency_contact_relation}</p>}
                            </div>
                        </div>
                    </div>

                    {/* 5. Additional Information */}
                    <div className="bg-white rounded-3xl border border-slate-200/80 p-6 md:p-8 shadow-sm">
                        <div className="flex items-center gap-3 pb-5 mb-6 border-b border-slate-100">
                            <div className="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">
                                <FileText size={20} />
                            </div>
                            <div>
                                <h2 className="text-lg font-bold text-slate-900">{t('section_additional')}</h2>
                                <p className="text-xs text-slate-500">{t('section_additional_desc')}</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('skills')}
                                </label>
                                <textarea
                                    rows={3}
                                    value={data.skills}
                                    onChange={(e) => setData('skills', e.target.value)}
                                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 resize-none"
                                    placeholder={t('skills_placeholder')}
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('languages')}
                                </label>
                                <textarea
                                    rows={3}
                                    value={data.languages}
                                    onChange={(e) => setData('languages', e.target.value)}
                                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 resize-none"
                                    placeholder={t('languages_placeholder')}
                                />
                            </div>

                            <div className="md:col-span-2">
                                <label className="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {t('about_me')}
                                </label>
                                <textarea
                                    rows={4}
                                    value={data.about_me}
                                    onChange={(e) => setData('about_me', e.target.value)}
                                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 resize-none"
                                    placeholder={t('about_me_placeholder')}
                                />
                            </div>

                            <div className="md:col-span-2">
                                <label className="block text-xs font-semibold text-slate-700 mb-2">
                                    {t('profile_photo')}
                                </label>
                                <div className="flex items-center gap-4">
                                    {previewPhoto ? (
                                        <img
                                            src={previewPhoto}
                                            alt="Student Avatar"
                                            className="w-16 h-16 rounded-2xl object-cover border border-slate-200 shadow-sm"
                                        />
                                    ) : (
                                        <div className="w-16 h-16 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400">
                                            <Camera size={24} />
                                        </div>
                                    )}

                                    <div>
                                        <label className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs cursor-pointer transition-colors shadow-xs">
                                            <Camera size={15} />
                                            <span>{t('upload_photo')}</span>
                                            <input
                                                type="file"
                                                accept="image/*"
                                                onChange={handlePhotoChange}
                                                className="hidden"
                                            />
                                        </label>
                                        <p className="text-[11px] text-slate-400 mt-1">{t('photo_hint')}</p>
                                    </div>
                                </div>
                                {errors.profile_photo && <p className="text-xs text-rose-500 mt-1">{errors.profile_photo}</p>}
                            </div>
                        </div>
                    </div>

                    {/* Action Bar */}
                    <div className="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-brand-600 to-indigo-600 text-white font-bold text-sm shadow-lg shadow-brand-500/25 hover:from-brand-500 hover:to-indigo-500 active:scale-98 transition-all disabled:opacity-50"
                        >
                            <Save size={18} />
                            <span>{processing ? t('saving') : t('save_changes')}</span>
                        </button>
                    </div>
                </form>
            </div>
        </StudentLayout>
    );
}
