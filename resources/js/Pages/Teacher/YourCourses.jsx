import React, { useState, useEffect, useRef, useMemo } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import TeacherLayout from '@/Layouts/TeacherLayout';
import { useTheme } from '@/Context/ThemeContext';
import {
    BookOpen, Users, Star, Activity, Search, Clock, BarChart2,
    Eye, GraduationCap, X, Sparkles, RotateCcw, ArrowUpRight,
    Zap, MessageSquare, LayoutGrid, List, Flame, Layers, BookMarked
} from 'lucide-react';

function fp(val) {
    if (val === null || val === undefined || isNaN(val)) return '0';
    return Number(val).toLocaleString('fa-IR');
}
function levelFa(l) {
    return { beginner: 'مقدماتی', intermediate: 'متوسط', advanced: 'پیشرفته' }[l] || l || 'عمومی';
}
function sc(status) {
    if (status === 'published' || status === 'active')
        return { label: 'منتشر شده', bg: 'bg-emerald-500/90', pulse: true };
    if (status === 'draft')
        return { label: 'پیش‌نویس', bg: 'bg-amber-400/95', pulse: false };
    return { label: 'آرشیو', bg: 'bg-slate-500/80', pulse: false };
}

function ParticleCanvas() {
    const ref = useRef(null);
    useEffect(() => {
        const c = ref.current; if (!c) return;
        const ctx = c.getContext('2d');
        let W = c.offsetWidth, H = c.offsetHeight;
        c.width = W; c.height = H;
        const pts = Array.from({ length: 60 }, () => ({
            x: Math.random() * W, y: Math.random() * H,
            r: Math.random() * 1.6 + 0.4,
            vx: (Math.random() - .5) * .3, vy: (Math.random() - .5) * .3,
            a: Math.random() * .45 + .1,
        }));
        let id;
        function draw() {
            ctx.clearRect(0, 0, W, H);
            pts.forEach(p => {
                p.x += p.vx; p.y += p.vy;
                if (p.x < 0) p.x = W; if (p.x > W) p.x = 0;
                if (p.y < 0) p.y = H; if (p.y > H) p.y = 0;
                ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(34,211,238,' + p.a + ')'; ctx.fill();
            });
            for (let i = 0; i < pts.length; i++) for (let j = i + 1; j < pts.length; j++) {
                const dx = pts[i].x - pts[j].x, dy = pts[i].y - pts[j].y;
                const d = Math.sqrt(dx * dx + dy * dy);
                if (d < 100) {
                    ctx.beginPath(); ctx.moveTo(pts[i].x, pts[i].y); ctx.lineTo(pts[j].x, pts[j].y);
                    ctx.strokeStyle = 'rgba(34,211,238,' + (.1 * (1 - d / 100)) + ')';
                    ctx.lineWidth = .5; ctx.stroke();
                }
            }
            id = requestAnimationFrame(draw);
        }
        draw();
        const onR = () => { W = c.offsetWidth; H = c.offsetHeight; c.width = W; c.height = H; };
        window.addEventListener('resize', onR);
        return () => { cancelAnimationFrame(id); window.removeEventListener('resize', onR); };
    }, []);
    return <canvas ref={ref} className="absolute inset-0 w-full h-full pointer-events-none opacity-35 dark:opacity-55" />;
}

function StatCard({ icon: Icon, label, value, accent }) {
    return (
        <div className="group relative p-5 rounded-[22px] bg-white/80 dark:bg-[#071328]/85 border border-slate-200/70 dark:border-cyan-500/20 shadow-lg dark:shadow-[0_8px_30px_rgba(0,0,0,0.45)] hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 backdrop-blur-md overflow-hidden flex items-center gap-4">
            <div className={`w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 border shadow-sm group-hover:scale-110 transition-transform duration-300 ${accent}`}>
                <Icon size={22} />
            </div>
            <div>
                <p className="text-[11px] font-bold text-slate-500 dark:text-slate-400 tracking-wide">{label}</p>
                <p className="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-0.5">{fp(value)}</p>
            </div>
        </div>
    );
}

function CourseCard({ course, idx }) {
    const st = sc(course.status);
    const thumb = course.thumbnail_url
        || (course.thumbnail ? (course.thumbnail.startsWith('http') ? course.thumbnail : `/storage/${course.thumbnail}`) : null)
        || 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=700&h=400&fit=crop';
    return (
        <div className="group relative bg-white/80 dark:bg-[#071328]/85 rounded-[26px] overflow-hidden border border-slate-200/70 dark:border-cyan-500/20 shadow-lg dark:shadow-[0_10px_35px_rgba(0,0,0,0.5)] hover:shadow-2xl hover:border-[#0A58CA]/40 dark:hover:border-cyan-400/50 hover:-translate-y-2 transition-all duration-300 flex flex-col backdrop-blur-md" style={{ animationDelay: `${idx * 70}ms` }}>
            <div className="relative h-52 overflow-hidden bg-slate-100 dark:bg-[#040a15]">
                <img src={thumb} alt={course.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" onError={e => { e.target.src = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=700&h=400&fit=crop'; }} />
                <div className="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-900/20 to-black/20 pointer-events-none" />
                <span className="absolute top-3 right-3 px-3 py-1 rounded-full bg-slate-950/70 backdrop-blur-md text-white text-[11px] font-bold border border-white/15">{course.category?.name || 'عمومی'}</span>
                <span className={`absolute top-3 left-3 flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold shadow-md backdrop-blur-md text-white border border-white/20 ${st.bg}`}>
                    {st.pulse && <span className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />}
                    {st.label}
                </span>
                <div className="absolute bottom-3 right-3 left-3 flex items-center justify-between text-white text-xs font-semibold">
                    <div className="flex items-center gap-1 bg-black/55 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/10">
                        <Users size={12} className="text-cyan-300" />
                        <span>{fp(course.enrolled_count || 0)} دانشجو</span>
                    </div>
                    {course.duration_hours && (
                        <div className="flex items-center gap-1 bg-black/55 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/10">
                            <Clock size={12} className="text-amber-300" />
                            <span>{fp(course.duration_hours)} ساعت</span>
                        </div>
                    )}
                </div>
                <div className="pointer-events-none absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-400" style={{ background: 'linear-gradient(135deg,rgba(0,240,255,0.08),rgba(10,88,202,0.06))' }} />
            </div>
            <div className="p-5 flex-1 flex flex-col gap-4">
                <div className="space-y-2">
                    <h3 className="font-extrabold text-slate-900 dark:text-white text-base leading-snug line-clamp-2 group-hover:text-[#0A58CA] dark:group-hover:text-cyan-300 transition-colors">{course.title}</h3>
                    {course.level && (
                        <div className="inline-flex items-center gap-1 text-[11px] font-bold text-slate-500 dark:text-cyan-400/80 bg-slate-100 dark:bg-cyan-950/40 px-2.5 py-0.5 rounded-md border border-slate-200 dark:border-cyan-500/20">
                            <BarChart2 size={11} /><span>سطح: {levelFa(course.level)}</span>
                        </div>
                    )}
                </div>
                <div className="flex items-center justify-between text-xs border-t border-slate-200/70 dark:border-white/8 pt-3">
                    <div className="flex items-center gap-0.5">
                        {[1, 2, 3, 4, 5].map(s => (
                            <Star key={s} size={12} fill={s <= Math.round(course.rating || 0) ? 'currentColor' : 'none'} className={s <= Math.round(course.rating || 0) ? 'text-amber-400' : 'text-slate-300 dark:text-slate-600'} />
                        ))}
                        <span className="font-bold text-slate-700 dark:text-slate-300 mr-1">{fp(Number(course.rating || 0).toFixed(1))}</span>
                    </div>
                    <span className="text-[11px] text-slate-400 dark:text-slate-500">#{fp(course.id)}</span>
                </div>
                <div className="grid grid-cols-2 gap-2.5 pt-1">
                    <a href={`/teacher/courses/${course.id}`} className="group/btn flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl font-bold text-xs text-white bg-gradient-to-r from-[#0A58CA] to-[#1683F7] dark:from-cyan-500 dark:to-blue-600 hover:from-[#0848a6] hover:to-[#0A58CA] shadow-md shadow-[#0A58CA]/20 dark:shadow-[0_0_18px_rgba(0,240,255,0.22)] transition-all duration-200">
                        <Eye size={13} className="group-hover/btn:scale-110 transition-transform" />
                        <span>مدیریت کلاس</span>
                        <ArrowUpRight size={12} className="opacity-70" />
                    </a>
                    <a href={`/courses/${course.id}/chat`} className="group/btn flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl font-bold text-xs bg-white dark:bg-[#0c1a36] border border-slate-200 dark:border-cyan-500/25 text-slate-700 dark:text-cyan-300 hover:bg-indigo-50 dark:hover:bg-indigo-500/15 hover:border-indigo-300 dark:hover:border-indigo-400/40 hover:text-indigo-600 dark:hover:text-indigo-300 transition-all duration-200">
                        <MessageSquare size={13} className="group-hover/btn:scale-110 transition-transform" />
                        <span>چت صنف</span>
                    </a>
                </div>
            </div>
        </div>
    );
}

export default function YourCourses({ courses = { data: [], links: [] }, categories = [], filters = {}, stats = {} }) {
    const { isDark } = useTheme();
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [selectedCategory, setSelectedCategory] = useState(filters.category || '');
    const [viewMode, setViewMode] = useState('grid');
    const [sortBy, setSortBy] = useState('latest');
    const currentPath = typeof window !== 'undefined' ? window.location.pathname : '/teacher/courses';
    const coursesData = courses?.data || [];
    const paginationLinks = courses?.links || [];

    useEffect(() => {
        const t = setTimeout(() => {
            if (searchTerm !== (filters.search || '')) applyFilters();
        }, 400);
        return () => clearTimeout(t);
    }, [searchTerm]);

    const applyFilters = (overrides = {}) => {
        router.get(currentPath, { search: searchTerm, category: selectedCategory, sort: sortBy, ...overrides }, { preserveState: true, preserveScroll: true, replace: true });
    };

    const clearFilters = () => {
        setSearchTerm(''); setSelectedCategory(''); setSortBy('latest');
        router.get(currentPath, {}, { preserveState: true, preserveScroll: true });
    };

    const hasActive = searchTerm || selectedCategory || sortBy !== 'latest';

    const displayed = useMemo(() => {
        let a = [...coursesData];
        if (sortBy === 'students') a.sort((x, y) => (y.enrolled_count || 0) - (x.enrolled_count || 0));
        else if (sortBy === 'rating') a.sort((x, y) => (y.rating || 0) - (x.rating || 0));
        return a;
    }, [coursesData, sortBy]);

    const css = `
        @keyframes orbA{0%,100%{transform:translate3d(0,0,0) scale(1)}33%{transform:translate3d(40px,-55px,0) scale(1.1)}66%{transform:translate3d(-30px,30px,0) scale(.92)}}
        @keyframes orbB{0%,100%{transform:translate3d(0,0,0) scale(1)}40%{transform:translate3d(-50px,40px,0) scale(1.08)}80%{transform:translate3d(35px,-25px,0) scale(.95)}}
        @keyframes flt{0%,100%{transform:translateY(0) rotate(0deg)}50%{transform:translateY(-14px) rotate(3deg)}}
        @keyframes glow{0%,100%{opacity:.3;filter:blur(50px)}50%{opacity:.65;filter:blur(70px)}}
        @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        @keyframes shimBtn{0%{transform:translateX(100%) skewX(-20deg)}100%{transform:translateX(-200%) skewX(-20deg)}}
        .ycA{animation:orbA 20s ease-in-out infinite,glow 10s ease-in-out infinite alternate}
        .ycB{animation:orbB 25s ease-in-out infinite reverse,glow 13s ease-in-out infinite alternate}
        .ycF{animation:flt 7s ease-in-out infinite}
        .ycU{animation:fadeUp .55s cubic-bezier(.22,1,.36,1) both}
        .ycG{backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px)}
        .ycGrid{background-image:linear-gradient(rgba(34,211,238,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(34,211,238,.04) 1px,transparent 1px);background-size:40px 40px}
        .ycShim::after{content:'';position:absolute;top:0;bottom:0;width:50%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.18),transparent);animation:shimBtn 2.5s infinite}
    `;

    return (
        <TeacherLayout title="مرکز دوره‌های من - پنل اساتید ادورا تک">
            <Head>
                <title>مرکز دوره‌های تخصصی من - ادورا تک</title>
                <meta name="description" content="مشاهده و مدیریت تمام دوره‌های آموزشی در پنل اساتید ادورا تک." />
            </Head>
            <style dangerouslySetInnerHTML={{ __html: css }} />

            <div className="relative -m-4 sm:-m-6 lg:-m-8 min-h-[calc(100vh-80px)] overflow-hidden">

                {/* Background */}
                <div className="pointer-events-none absolute inset-0 -z-10">
                    <div className="absolute inset-0 ycGrid" />
                    <div className="ycA absolute -top-32 right-1/3 w-[500px] h-[500px] rounded-full bg-gradient-to-br from-cyan-500/18 via-blue-600/12 to-transparent dark:from-cyan-400/22 dark:via-blue-500/18" />
                    <div className="ycB absolute top-1/2 -left-24 w-[600px] h-[600px] rounded-full bg-gradient-to-tr from-indigo-500/12 via-purple-500/8 to-transparent dark:from-indigo-600/18 dark:via-purple-500/12" />
                    <div className="ycA absolute -bottom-20 right-8 w-[420px] h-[420px] rounded-full bg-gradient-to-tl from-emerald-400/10 via-teal-500/8 to-transparent dark:from-emerald-500/14 dark:via-teal-400/10" />
                    <ParticleCanvas />
                </div>

                <div className="relative z-10 p-4 sm:p-6 lg:p-8 space-y-6">

                    {/* HERO */}
                    <div className="ycG ycU relative rounded-[30px] overflow-hidden border border-white/30 dark:border-cyan-500/25 shadow-2xl dark:shadow-[0_20px_60px_-10px_rgba(0,240,255,0.18)] p-7 sm:p-12"
                        style={{ background: isDark ? 'linear-gradient(135deg,rgba(8,24,52,.94) 0%,rgba(5,12,28,.97) 60%,rgba(12,32,72,.92) 100%)' : 'linear-gradient(135deg,#0A58CA 0%,#0847a0 40%,#0e6ddb 80%,#1683F7 100%)' }}>
                        <div className="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-cyan-400 to-transparent" />
                        <div className="absolute inset-0 pointer-events-none" style={{ background: 'radial-gradient(ellipse 60% 50% at 50% 0%,rgba(0,240,255,.12),transparent)' }} />

                        <div className="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8" dir="rtl">
                            <div className="space-y-5 max-w-2xl text-right">
                                <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 dark:bg-cyan-500/15 border border-white/20 dark:border-cyan-400/30 text-xs font-bold text-cyan-200 backdrop-blur-md">
                                    <Sparkles size={13} className="text-cyan-300 animate-pulse" />
                                    <span>پنل اساتید ادورا تک · مرکز مدیریت دوره‌ها</span>
                                </div>
                                <h1 className="text-3xl sm:text-5xl font-black text-white tracking-tight leading-[1.15]">
                                    مرکز دوره‌های{' '}
                                    <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 via-sky-200 to-cyan-400">تخصصی من</span>
                                </h1>
                                <p className="text-sm sm:text-base text-blue-100/85 dark:text-slate-300 leading-relaxed max-w-xl font-medium">
                                    تمامی دوره‌های آموزشی خود را مشاهده، مدیریت و پایش کنید. در حال حاضر{' '}
                                    <strong className="text-cyan-300 font-extrabold underline decoration-cyan-400/50 underline-offset-4">{fp(stats.totalStudents || 0)} دانشجو</strong>{' '}
                                    را زیر نظر دارید.
                                </p>
                                <div className="flex flex-wrap items-center gap-2.5 pt-1">
                                    {[
                                        { icon: BookOpen, label: `${fp(stats.totalCourses || 0)} دوره تدوین‌شده`, c: 'text-cyan-300' },
                                        { icon: Users, label: `${fp(stats.totalStudents || 0)} دانشجوی فعال`, c: 'text-emerald-300' },
                                        { icon: Star, label: `امتیاز: ${fp(Number(stats.avgRating || 0).toFixed(1))}`, c: 'text-amber-300' },
                                        { icon: Activity, label: `${fp(stats.activeCourses || 0)} دوره فعال`, c: 'text-sky-300' },
                                    ].map(({ icon: I, label, c }) => (
                                        <div key={label} className="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/10 dark:bg-white/5 border border-white/10 text-xs font-semibold text-white/90 backdrop-blur-sm">
                                            <I size={13} className={c} /><span>{label}</span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                            <div className="hidden lg:flex items-center justify-center shrink-0">
                                <div className="relative ycF">
                                    <div className="absolute inset-0 w-36 h-36 rounded-[32px] border-2 border-cyan-400/20 rotate-12 scale-110 animate-pulse" />
                                    <div className="w-36 h-36 rounded-[32px] bg-gradient-to-br from-cyan-400/20 via-blue-600/25 to-indigo-700/30 border border-cyan-400/30 flex items-center justify-center backdrop-blur-md shadow-[0_0_60px_rgba(0,240,255,0.2)]">
                                        <GraduationCap size={68} className="text-cyan-300 drop-shadow-[0_8px_20px_rgba(0,240,255,0.5)]" />
                                    </div>
                                    <div className="absolute -bottom-3 -right-3 w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 border border-white/30 flex items-center justify-center shadow-xl">
                                        <Zap size={22} className="text-yellow-300" />
                                    </div>
                                    <div className="absolute -top-3 -left-3 px-2.5 py-1 rounded-xl bg-emerald-500 border border-white/30 text-white text-[11px] font-extrabold shadow-lg">
                                        {fp(stats.totalCourses || 0)} دوره
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* STAT CARDS */}
                    <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 ycU" dir="rtl" style={{ animationDelay: '100ms' }}>
                        <StatCard icon={Layers} label="کل دوره‌ها" value={stats.totalCourses || 0} accent="bg-gradient-to-br from-[#0A58CA]/15 to-[#0A58CA]/5 dark:from-cyan-500/20 dark:to-blue-600/20 text-[#0A58CA] dark:text-cyan-400 border-[#0A58CA]/20 dark:border-cyan-500/30" />
                        <StatCard icon={Users} label="دانشجویان فعال" value={stats.totalStudents || 0} accent="bg-gradient-to-br from-emerald-500/15 to-emerald-500/5 dark:from-emerald-500/20 dark:to-teal-600/20 text-emerald-600 dark:text-emerald-400 border-emerald-500/20 dark:border-emerald-500/30" />
                        <StatCard icon={Star} label="میانگین امتیاز" value={Number(stats.avgRating || 0).toFixed(1)} accent="bg-gradient-to-br from-amber-500/15 to-amber-500/5 dark:from-amber-500/20 dark:to-yellow-600/20 text-amber-500 dark:text-amber-400 border-amber-500/20 dark:border-amber-500/30" />
                        <StatCard icon={Flame} label="دوره‌های فعال" value={stats.activeCourses || 0} accent="bg-gradient-to-br from-rose-500/15 to-rose-500/5 dark:from-rose-500/20 dark:to-red-600/20 text-rose-600 dark:text-rose-400 border-rose-500/20 dark:border-rose-500/30" />
                    </div>

                    {/* FILTER BAR */}
                    <div className="ycG ycU p-4 sm:p-5 rounded-[22px] bg-white/80 dark:bg-[#071328]/85 border border-slate-200/70 dark:border-cyan-500/20 shadow-lg dark:shadow-[0_8px_30px_rgba(0,0,0,0.4)]" dir="rtl" style={{ animationDelay: '200ms' }}>
                        <div className="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                            <div className="relative flex-1">
                                <Search size={17} className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-cyan-400/70 pointer-events-none" />
                                <input type="text" placeholder="جستجوی عنوان دوره..." value={searchTerm} onChange={e => setSearchTerm(e.target.value)}
                                    className="w-full pr-10 pl-9 py-2.5 rounded-xl bg-slate-100/80 dark:bg-[#0a1b38] border border-slate-200 dark:border-cyan-500/25 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/30 dark:focus:ring-cyan-400/40 focus:border-[#0A58CA] dark:focus:border-cyan-400 transition-all font-medium" />
                                {searchTerm && <button onClick={() => setSearchTerm('')} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 dark:hover:text-white p-0.5 transition-colors"><X size={14} /></button>}
                            </div>
                            <select value={selectedCategory} onChange={e => { setSelectedCategory(e.target.value); applyFilters({ category: e.target.value }); }}
                                className="px-4 py-2.5 rounded-xl bg-slate-100/80 dark:bg-[#0a1b38] border border-slate-200 dark:border-cyan-500/25 text-sm font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/30 dark:focus:ring-cyan-400/40 focus:border-[#0A58CA] dark:focus:border-cyan-400 transition-all cursor-pointer min-w-[160px]">
                                <option value="">همه دسته‌بندی‌ها</option>
                                {categories.map(cat => <option key={cat.id} value={cat.name} className="dark:bg-[#0b172e]">{cat.name}</option>)}
                            </select>
                            <select value={sortBy} onChange={e => { setSortBy(e.target.value); applyFilters({ sort: e.target.value }); }}
                                className="px-4 py-2.5 rounded-xl bg-slate-100/80 dark:bg-[#0a1b38] border border-slate-200 dark:border-cyan-500/25 text-sm font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/30 dark:focus:ring-cyan-400/40 focus:border-[#0A58CA] dark:focus:border-cyan-400 transition-all cursor-pointer min-w-[150px]">
                                <option value="latest">جدیدترین دوره‌ها</option>
                                <option value="students">بیشترین دانشجو</option>
                                <option value="rating">بالاترین امتیاز</option>
                            </select>
                            <div className="flex items-center gap-1 p-1 rounded-xl bg-slate-100 dark:bg-[#0a1b38] border border-slate-200 dark:border-cyan-500/20">
                                <button onClick={() => setViewMode('grid')} title="نمایش شبکه‌ای" className={`p-2 rounded-lg transition-all ${viewMode === 'grid' ? 'bg-white dark:bg-[#0A58CA] text-[#0A58CA] dark:text-white shadow-sm' : 'text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'}`}><LayoutGrid size={16} /></button>
                                <button onClick={() => setViewMode('list')} title="نمایش لیستی" className={`p-2 rounded-lg transition-all ${viewMode === 'list' ? 'bg-white dark:bg-[#0A58CA] text-[#0A58CA] dark:text-white shadow-sm' : 'text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'}`}><List size={16} /></button>
                            </div>
                            {hasActive && (
                                <button onClick={clearFilters} className="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-rose-50 dark:bg-rose-500/15 hover:bg-rose-100 dark:hover:bg-rose-500/25 text-rose-600 dark:text-rose-400 text-xs font-bold transition-all whitespace-nowrap border border-rose-200/50 dark:border-rose-500/30">
                                    <RotateCcw size={13} /><span>پاک کردن</span>
                                </button>
                            )}
                        </div>
                        <div className="mt-3 flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400 font-semibold">
                            <BookOpen size={12} />
                            <span>{fp(courses?.total || coursesData.length)} دوره یافت شد</span>
                            {hasActive && <span className="text-cyan-600 dark:text-cyan-400">· فیلتر فعال</span>}
                        </div>
                    </div>

                    {/* COURSES */}
                    {displayed.length > 0 ? (
                        viewMode === 'grid' ? (
                            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 ycU" dir="rtl" style={{ animationDelay: '300ms' }}>
                                {displayed.map((course, idx) => <CourseCard key={course.id} course={course} idx={idx} />)}
                            </div>
                        ) : (
                            <div className="space-y-3 ycU" dir="rtl" style={{ animationDelay: '300ms' }}>
                                {displayed.map((course, idx) => {
                                    const s = sc(course.status);
                                    const thumb = course.thumbnail_url || (course.thumbnail ? (course.thumbnail.startsWith('http') ? course.thumbnail : `/storage/${course.thumbnail}`) : null) || 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=120&h=120&fit=crop';
                                    return (
                                        <div key={course.id} className="ycG group flex items-center gap-4 p-4 sm:p-5 rounded-[20px] bg-white/80 dark:bg-[#071328]/85 border border-slate-200/70 dark:border-cyan-500/20 shadow-md dark:shadow-[0_6px_25px_rgba(0,0,0,0.4)] hover:shadow-xl hover:border-[#0A58CA]/40 dark:hover:border-cyan-400/50 hover:-translate-y-0.5 transition-all duration-300">
                                            <img src={thumb} alt={course.title} className="w-16 h-16 rounded-2xl object-cover shrink-0 border border-slate-200 dark:border-cyan-500/20 group-hover:scale-105 transition-transform" onError={e => { e.target.src = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=120&h=120&fit=crop'; }} />
                                            <div className="flex-1 min-w-0">
                                                <div className="flex items-start justify-between gap-3">
                                                    <h3 className="font-extrabold text-slate-900 dark:text-white text-sm leading-snug line-clamp-1 group-hover:text-[#0A58CA] dark:group-hover:text-cyan-300 transition-colors">{course.title}</h3>
                                                    <span className={`shrink-0 flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold ${s.bg} text-white`}>
                                                        {s.pulse && <span className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />}{s.label}
                                                    </span>
                                                </div>
                                                <div className="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-[11px] text-slate-500 dark:text-slate-400 font-semibold">
                                                    <span className="flex items-center gap-1"><Users size={11} className="text-cyan-500" />{fp(course.enrolled_count || 0)} دانشجو</span>
                                                    {course.duration_hours && <span className="flex items-center gap-1"><Clock size={11} className="text-amber-500" />{fp(course.duration_hours)} ساعت</span>}
                                                    <span className="flex items-center gap-1"><Star size={11} className="text-amber-400 fill-amber-400" />{fp(Number(course.rating || 0).toFixed(1))}</span>
                                                    {course.category?.name && <span className="flex items-center gap-1"><BookMarked size={11} className="text-indigo-400" />{course.category.name}</span>}
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-2 shrink-0">
                                                <a href={`/teacher/courses/${course.id}`} className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#0A58CA] to-[#1683F7] dark:from-cyan-500 dark:to-blue-600 hover:from-[#0848a6] hover:to-[#0A58CA] shadow-md shadow-[#0A58CA]/20 dark:shadow-[0_0_15px_rgba(0,240,255,0.2)] transition-all">
                                                    <Eye size={13} /><span className="hidden sm:inline">مدیریت</span>
                                                </a>
                                                <a href={`/courses/${course.id}/chat`} className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-white dark:bg-[#0c1a36] border border-slate-200 dark:border-cyan-500/25 text-slate-700 dark:text-cyan-300 hover:bg-indigo-50 dark:hover:bg-indigo-500/15 hover:text-indigo-600 dark:hover:text-indigo-300 transition-all">
                                                    <MessageSquare size={13} /><span className="hidden sm:inline">چت</span>
                                                </a>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )
                    ) : (
                        <div className="ycG ycU p-16 rounded-[28px] bg-white/75 dark:bg-[#071328]/80 border border-slate-200/70 dark:border-cyan-500/20 shadow-xl text-center space-y-5" dir="rtl" style={{ animationDelay: '300ms' }}>
                            <div className="relative mx-auto w-24 h-24">
                                <div className="w-24 h-24 rounded-3xl bg-[#0A58CA]/10 dark:bg-cyan-500/15 text-[#0A58CA] dark:text-cyan-400 flex items-center justify-center border border-[#0A58CA]/20 dark:border-cyan-500/30 shadow-md mx-auto"><BookOpen size={40} /></div>
                                <div className="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-rose-500/80 text-white flex items-center justify-center text-[10px] font-black">!</div>
                            </div>
                            <div className="space-y-2">
                                <h4 className="text-2xl font-extrabold text-slate-900 dark:text-white">هیچ دوره‌ای یافت نشد</h4>
                                <p className="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">عبارات جستجو را تغییر دهید یا فیلترها را پاک کنید.</p>
                            </div>
                            {hasActive && (
                                <button onClick={clearFilters} className="relative overflow-hidden ycShim px-6 py-3 rounded-xl bg-gradient-to-r from-[#0A58CA] to-[#1683F7] dark:from-cyan-500 dark:to-blue-600 text-white text-sm font-bold shadow-md hover:scale-105 transition-all">
                                    پاک کردن فیلترها و مشاهده همه
                                </button>
                            )}
                        </div>
                    )}

                    {/* PAGINATION */}
                    {paginationLinks && paginationLinks.length > 3 && (
                        <div className="flex items-center justify-center gap-2 pt-4 ycU" dir="rtl" style={{ animationDelay: '400ms' }}>
                            {paginationLinks.map((link, idx) => {
                                if (!link.url) return <span key={idx} dangerouslySetInnerHTML={{ __html: link.label }} className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 dark:text-slate-600 cursor-not-allowed bg-white/40 dark:bg-white/5 border border-slate-200/50 dark:border-white/5" />;
                                const isPrev = link.label.includes('Previous') || link.label.includes('&laquo;');
                                const isNext = link.label.includes('Next') || link.label.includes('&raquo;');
                                return (
                                    <Link key={idx} href={link.url} preserveScroll preserveState
                                        className={`px-4 py-2 rounded-xl text-xs font-bold transition-all ${link.active ? 'bg-gradient-to-r from-[#0A58CA] to-[#1683F7] dark:from-cyan-500 dark:to-blue-600 text-white shadow-md shadow-[#0A58CA]/20 dark:shadow-[0_0_15px_rgba(0,240,255,0.3)]' : 'ycG bg-white/80 dark:bg-[#071328]/80 border border-slate-200/80 dark:border-cyan-500/20 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-cyan-500/10 hover:text-[#0A58CA] dark:hover:text-cyan-400'}`}>
                                        {isPrev ? 'قبلی' : isNext ? 'بعدی' : link.label}
                                    </Link>
                                );
                            })}
                        </div>
                    )}
                </div>
            </div>
        </TeacherLayout>
    );
}
