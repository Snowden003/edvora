import React from 'react';
import { Head, Link } from '@inertiajs/react';
import StudentLayout from '@/Layouts/StudentLayout';
import { useLanguage } from '@/Context/LanguageContext';
import { Award, CheckCircle, Download, Calendar, ExternalLink, Sparkles, BookOpen } from 'lucide-react';

export default function Certificates({
    certificates = [],
    completedEnrollments = []
}) {
    const { t, isRtl } = useLanguage();
    const totalCount = certificates.length + completedEnrollments.length;

    return (
        <StudentLayout title={`${t('certificates')} - ${t('brand_title')}`}>
            <Head>
                <title>{`${t('certificates')} - ${t('brand_title')}`}</title>
                <meta
                    name="description"
                    content="View and download your official verified certificates of completion from Edvora Tech programs."
                />
            </Head>

            <div className="space-y-6 max-w-7xl mx-auto">
                {/* Banner */}
                <div className="relative rounded-3xl overflow-hidden shadow-2xl bg-gradient-to-br from-[#061E3E] via-slate-900 to-[#1F8FFF] text-white p-6 sm:p-12 border border-blue-900/50">
                    <div className="relative z-10 space-y-3 max-w-2xl text-center md:text-start">
                        <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-semibold text-cyan-300">
                            <Sparkles size={13} className="text-cyan-400" />
                            <span>{t('verified_credential')}</span>
                        </span>
                        <h1 className="text-2xl sm:text-4xl font-black text-white tracking-tight">
                            {t('certificates_hero_title')}
                        </h1>
                        <p className="text-sm sm:text-base text-blue-100 leading-relaxed">
                            {t('certificates_hero_desc')}
                        </p>
                    </div>
                </div>

                {/* Certificates Showcase Grid */}
                {certificates.length > 0 || completedEnrollments.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        {/* Issued Certificates */}
                        {certificates.map((cert) => (
                            <div
                                key={cert.id}
                                className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between space-y-5"
                            >
                                <div className="space-y-4">
                                    <div className="flex items-center justify-between">
                                        <div className="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center">
                                            <Award size={26} />
                                        </div>
                                        <span className="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center gap-1">
                                            <CheckCircle size={12} />
                                            <span>{t('status_completed')}</span>
                                        </span>
                                    </div>

                                    <div>
                                        <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                                            {cert.category}
                                        </span>
                                        <h3 className="text-base font-extrabold text-slate-800 mt-1 leading-snug">
                                            {cert.title || cert.course_title}
                                        </h3>
                                    </div>

                                    <div className="p-3 rounded-2xl bg-slate-50 border border-slate-100 space-y-1 text-xs text-slate-500">
                                        <div className="flex items-center justify-between">
                                            <span>{t('cert_number')}</span>
                                            <span className="font-mono font-bold text-slate-700">{cert.certificate_number}</span>
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <span>{t('issued_on')}</span>
                                            <span className="font-semibold text-slate-700">{cert.issued_at}</span>
                                        </div>
                                    </div>
                                </div>

                                <div className="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                                    <span className="text-xs font-bold text-slate-400">{t('brand_title')}</span>
                                    {cert.download_url && (
                                        <a
                                            href={cert.download_url}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-sm transition-all"
                                        >
                                            <Download size={14} />
                                            <span>{t('download_cert')}</span>
                                        </a>
                                    )}
                                </div>
                            </div>
                        ))}

                        {/* Completed Course Achievements */}
                        {completedEnrollments.map((item) => (
                            <div
                                key={item.id}
                                className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between space-y-5"
                            >
                                <div className="space-y-4">
                                    <div className="flex items-center justify-between">
                                        <div className="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                            <Award size={26} />
                                        </div>
                                        <span className="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                                            {t('completed_program')}
                                        </span>
                                    </div>

                                    <div>
                                        <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                                            {item.category}
                                        </span>
                                        <h3 className="text-base font-extrabold text-slate-800 mt-1 leading-snug">
                                            {item.course_title}
                                        </h3>
                                    </div>

                                    <div className="p-3 rounded-2xl bg-slate-50 border border-slate-100 space-y-1 text-xs text-slate-500">
                                        <div className="flex items-center justify-between">
                                            <span>{t('status')}</span>
                                            <span className="font-bold text-emerald-600">100% {t('status_completed')}</span>
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <span>{t('issued_on')}</span>
                                            <span className="font-semibold text-slate-700">{item.completed_at}</span>
                                        </div>
                                    </div>
                                </div>

                                <div className="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                                    <span className="text-xs font-semibold text-slate-400">{t('verified_credential')}</span>
                                    <span className="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">
                                        {t('status_completed')}
                                    </span>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="p-16 rounded-3xl bg-white border border-slate-200/80 shadow-sm text-center space-y-4">
                        <div className="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <Award size={32} />
                        </div>
                        <div className="space-y-1">
                            <h4 className="text-lg font-bold text-slate-800">{t('no_certificates_title')}</h4>
                            <p className="text-xs text-slate-400 max-w-sm mx-auto">
                                {t('no_certificates_desc')}
                            </p>
                        </div>
                        <Link
                            href="/student/courses"
                            className="inline-block px-5 py-2.5 rounded-2xl bg-brand-600 text-white font-bold text-xs shadow-sm hover:bg-brand-500 transition-colors"
                        >
                            {t('browse_courses')}
                        </Link>
                    </div>
                )}
            </div>
        </StudentLayout>
    );
}
