import React, { useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import Sidebar from '@/Components/Admin/Sidebar';
import Topbar from '@/Components/Admin/Topbar';
import { CheckCircle, AlertCircle, Info, X } from 'lucide-react';

export default function AdminLayout({ children, title = 'پنل مدیریت ادورا' }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [isCollapsed, setIsCollapsed] = useState(false);
    const { flash } = usePage().props;
    const [showFlash, setShowFlash] = useState(true);

    return (
        <div className="min-h-screen bg-slate-950 text-slate-100 font-sans flex antialiased selection:bg-brand-500 selection:text-white" dir="rtl">
            <Head title={title} />

            {/* Collapsible Persian / English Sidebar */}
            <Sidebar
                isOpen={sidebarOpen}
                setIsOpen={setSidebarOpen}
                isCollapsed={isCollapsed}
                setIsCollapsed={setIsCollapsed}
            />

            {/* Main Application Area */}
            <div
                className={`flex-1 flex flex-col min-w-0 transition-all duration-300 ${
                    isCollapsed ? 'lg:mr-20' : 'lg:mr-72'
                }`}
            >
                {/* Fixed Topbar */}
                <Topbar onToggleSidebar={() => setSidebarOpen(!sidebarOpen)} />

                {/* Flash Messages */}
                {showFlash && (flash?.success || flash?.error || flash?.info) && (
                    <div className="mx-4 sm:mx-8 mt-4">
                        {flash?.success && (
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">
                                <div className="flex items-center gap-3">
                                    <CheckCircle size={20} className="text-emerald-400 shrink-0" />
                                    <span>{flash.success}</span>
                                </div>
                                <button onClick={() => setShowFlash(false)} className="text-emerald-400 hover:text-emerald-200">
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                        {flash?.error && (
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm">
                                <div className="flex items-center gap-3">
                                    <AlertCircle size={20} className="text-rose-400 shrink-0" />
                                    <span>{flash.error}</span>
                                </div>
                                <button onClick={() => setShowFlash(false)} className="text-rose-400 hover:text-rose-200">
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                        {flash?.info && (
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-brand-500/10 border border-brand-500/30 text-brand-300 text-sm">
                                <div className="flex items-center gap-3">
                                    <Info size={20} className="text-brand-400 shrink-0" />
                                    <span>{flash.info}</span>
                                </div>
                                <button onClick={() => setShowFlash(false)} className="text-brand-400 hover:text-brand-200">
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                    </div>
                )}

                {/* Main Content Area */}
                <main className="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto space-y-8">
                    {children}
                </main>

                {/* Modern Footer */}
                <footer className="h-16 border-t border-slate-800/80 px-4 sm:px-8 flex items-center justify-between text-xs text-slate-500 bg-slate-950/40">
                    <div>
                        © {new Date().getFullYear()} <span className="text-slate-400 font-semibold">Edvora Tech</span> — تمامی حقوق محفوظ است.
                    </div>
                    <div className="flex items-center gap-4">
                        <span className="flex items-center gap-1.5">
                            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                            سیستم فعال
                        </span>
                        <span>نسخه ۲.۰ SPA</span>
                    </div>
                </footer>
            </div>
        </div>
    );
}
