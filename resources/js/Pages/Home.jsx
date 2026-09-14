import PublicLayout from '../Layouts/PublicLayout';
import { useState, useEffect } from 'react';

function rupiah(n) {
    return new Intl.NumberFormat('id-ID').format(n);
}

const faqData = [
    { q: 'Apa itu Ryaze?', a: 'Platform hosting & development. Auto-deploy dari Git, SSL gratis, database MySQL, web terminal, panel kontrol lengkap.' },
    { q: 'Teknologi apa yang didukung?', a: 'Node.js, PHP/Laravel, Python, React, Vue.js, Next.js, dan HTML statis. Deploy otomatis dari repositori Git.' },
    { q: 'SSL tersedia?', a: 'Ya. Setiap project otomatis dapat sertifikat SSL gratis via Let\'s Encrypt.' },
    { q: 'Database tersedia?', a: 'Ya. MySQL bawaan, dikelola lewat panel, mini phpMyAdmin, dan API key.' },
    { q: 'Bisa pesan website/aplikasi?', a: 'Bisa. Sistem informasi, SaaS, website korporat, prototipe fungsional — arsitektur modern, bersih, terdokumentasi.' },
    { q: 'Cara mulai?', a: 'Daftar gratis, pilih paket, deploy dari Git dalam hitungan menit.' },
];

function Faq({ q, a }) {
    const [open, setOpen] = useState(false);
    return (
        <div className="border-b border-[#e5e5e5] dark:border-[#2d1f42]">
            <button onClick={() => setOpen(!open)} className="w-full flex items-center justify-between py-5 text-left group">
                <span className="text-[15px] font-medium text-[#1a1025] dark:text-white group-hover:text-[#7c3aed] transition-colors">{q}</span>
                <span className={`text-[#7c3aed] text-lg transition-transform duration-200 ${open ? 'rotate-45' : ''}`}>+</span>
            </button>
            <div className={`overflow-hidden transition-all duration-300 ${open ? 'max-h-40 pb-5' : 'max-h-0'}`}>
                <p className="text-sm text-[#666] dark:text-[#999] leading-relaxed">{a}</p>
            </div>
        </div>
    );
}

export default function Home({ plans, planPricing, articles, starterPricing }) {
    const [chatOpen, setChatOpen] = useState(false);
    const [chatMsgs, setChatMsgs] = useState([]);
    const [chatInput, setChatInput] = useState('');
    const [chatLoading, setChatLoading] = useState(false);
    const [chatHistory, setChatHistory] = useState([]);

    useEffect(() => {
        const els = document.querySelectorAll('[data-reveal]');
        if (!('IntersectionObserver' in window)) { els.forEach(el => el.style.opacity = '1'); return; }
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); } });
        }, { threshold: 0.1 });
        els.forEach(el => io.observe(el));
        return () => io.disconnect();
    }, []);

    const sendChat = async (e) => {
        e.preventDefault();
        const text = chatInput.trim();
        if (!text || chatLoading) return;
        setChatInput('');
        setChatMsgs(prev => [...prev, { id: Date.now(), text, user: true }]);
        setChatLoading(true);
        try {
            const r = await fetch('/chat', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }, body: JSON.stringify({ message: text, history: chatHistory }) });
            if (r.ok) { const d = await r.json(); if (d.reply) { setChatMsgs(p => [...p, { id: Date.now() + 1, text: d.reply, user: false }]); setChatHistory(p => [...p.slice(-8), { role: 'user', content: text }, { role: 'assistant', content: d.reply }]); } }
        } catch {}
        setChatLoading(false);
    };

    const activePlans = Object.entries(plans).filter(([, p]) => p.is_active);

    return (
        <PublicLayout title="Jasa Pembuatan Website & Shared Hosting Indonesia" description="Platform hosting & development modern.">
            <style>{`
                [data-reveal] { opacity: 0; transform: translateY(20px); transition: opacity 0.5s ease, transform 0.5s ease; }
                [data-reveal].revealed { opacity: 1; transform: none; }
                .price-strike { position: relative; }
                .price-strike::after { content: ''; position: absolute; left: -2px; right: -2px; top: 50%; height: 1px; background: currentColor; opacity: 0.4; }
            `}</style>

            {/* ═══ HERO ═══ */}
            <section className="relative bg-[#fafafa] dark:bg-[#0a0a14] overflow-hidden">
                <div className="absolute inset-0" style={{ backgroundImage: 'radial-gradient(circle at 1px 1px, #ddd 1px, transparent 0)', backgroundSize: '32px 32px' }}></div>
                <div className="dark:hidden absolute inset-0" style={{ backgroundImage: 'radial-gradient(circle at 1px 1px, rgba(124,58,237,0.06) 1px, transparent 0)', backgroundSize: '32px 32px' }}></div>
                <div className="relative max-w-6xl mx-auto px-6 pt-32 pb-28 lg:pt-44 lg:pb-36">
                    <div className="max-w-3xl" data-reveal>
                        <div className="inline-block px-3 py-1 bg-[#7c3aed] text-white text-[11px] font-bold tracking-widest uppercase mb-8">
                            Deployment Tersedia
                        </div>
                        <h1 className="text-[clamp(2.5rem,6vw,4.5rem)] font-black text-[#1a1025] dark:text-white leading-[1.05] tracking-[-0.03em] mb-6">
                            Bangun produk digital,<br />
                            <span className="text-[#7c3aed]">deploy dalam hitungan menit.</span>
                        </h1>
                        <p className="text-[#666] dark:text-[#888] text-lg max-w-xl mb-10 leading-relaxed">
                            Jasa pembuatan website & aplikasi. Shared hosting Indonesia dengan auto-deploy dari Git, SSL gratis, database MySQL.
                        </p>
                        <div className="flex flex-wrap gap-3">
                            <a href="#pricing" className="px-7 py-3 bg-[#1a1025] dark:bg-white dark:text-[#1a1025] text-white text-sm font-semibold hover:bg-[#2d1f42] dark:hover:bg-slate-200 transition-colors">
                                Lihat Paket
                            </a>
                            <a href="#services" className="px-7 py-3 border border-[#ddd] dark:border-[#333] text-[#1a1025] dark:text-[#999] text-sm font-semibold hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">
                                Pelajari Lebih Lanjut
                            </a>
                        </div>
                    </div>

                    <div className="mt-20 grid grid-cols-3 gap-px bg-[#e5e5e5] dark:bg-[#1a1a2e] border border-[#e5e5e5] dark:border-[#1a1a2e]" data-reveal>
                        {[
                            { n: '99.9%', l: 'Uptime Server' },
                            { n: '100+', l: 'Project Selesai' },
                            { n: '< 5 mnt', l: 'Auto-Deploy' },
                        ].map((s, i) => (
                            <div key={i} className="bg-[#fafafa] dark:bg-[#0d0d18] px-6 py-8 text-center">
                                <p className="text-3xl font-black text-[#1a1025] dark:text-white tracking-tight">{s.n}</p>
                                <p className="text-[11px] font-bold text-[#999] dark:text-[#555] mt-2 uppercase tracking-widest">{s.l}</p>
                            </div>
                        ))}
                    </div>

                    <div className="mt-16 flex items-center gap-8" data-reveal>
                        <span className="text-[10px] font-bold text-[#bbb] dark:text-[#444] uppercase tracking-[0.2em]">Tech Stack</span>
                        <div className="flex gap-6 text-[#ccc] dark:text-[#333]">
                            {['fa-brands fa-laravel', 'fa-brands fa-react', 'fa-brands fa-node-js', 'fa-brands fa-python', 'fa-brands fa-vuejs', 'fa-brands fa-docker'].map(icon => (
                                <i key={icon} className={`${icon} text-2xl hover:text-[#7c3aed] transition-colors cursor-default`}></i>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* ═══ ABOUT ═══ */}
            <section id="about" className="bg-white dark:bg-[#0d0d18] border-y border-[#e5e5e5] dark:border-[#1a1a2e]">
                <div className="max-w-6xl mx-auto px-6 py-24 lg:py-32">
                    <div className="grid lg:grid-cols-12 gap-16 items-start">
                        <div className="lg:col-span-5" data-reveal>
                            <span className="text-[11px] font-bold text-[#7c3aed] uppercase tracking-[0.2em] mb-4 block">Tentang</span>
                            <h2 className="text-4xl font-black text-[#1a1025] dark:text-white tracking-tight leading-[1.1] mb-6">
                                Platform hosting<br />yang tidak ambigu.
                            </h2>
                            <p className="text-[#666] dark:text-[#888] leading-relaxed mb-4">
                                Ryaze dirancang untuk developer dan bisnis yang butuh deployment cepat, infrastruktur andal, dan kontrol penuh.
                            </p>
                            <p className="text-[#666] dark:text-[#888] leading-relaxed mb-8">
                                Auto-deploy dari Git, SSL gratis, database lengkap, panel kontrol — harga terjangkau.
                            </p>
                            <div className="flex flex-wrap gap-2">
                                {['AUTO DEPLOY', 'SSL GRATIS', 'MYSQL', 'TERMINAL'].map(t => (
                                    <span key={t} className="px-3 py-1 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] text-[11px] font-bold tracking-wider border border-[#e9e0ff] dark:border-[#7c3aed]/20">{t}</span>
                                ))}
                            </div>
                        </div>
                        <div className="lg:col-span-7 lg:pt-8" data-reveal>
                            <div className="border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-[#0a0a14]">
                                <div className="border-b border-[#e5e5e5] dark:border-[#1a1a2e] px-8 py-6 flex items-center justify-between">
                                    <div className="flex items-center gap-4">
                                        <div className="w-12 h-12 bg-[#7c3aed] flex items-center justify-center">
                                            <i className="fa-solid fa-server text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <h3 className="font-bold text-[#1a1025] dark:text-white text-lg">Ryaze Hosting</h3>
                                            <p className="text-[11px] text-[#999] dark:text-[#555] font-medium uppercase tracking-wider">Shared Server Infrastructure</p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-2 text-xs font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-1.5 border border-emerald-100 dark:border-emerald-500/20">
                                        <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                                    </div>
                                </div>
                                <div className="grid grid-cols-3 divide-x divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                    {[
                                        { v: '99.9%', l: 'Uptime' },
                                        { v: '< 5mnt', l: 'Deploy' },
                                        { v: '24/7', l: 'Support' },
                                    ].map((s, i) => (
                                        <div key={i} className="px-6 py-6 text-center">
                                            <p className="text-2xl font-black text-[#1a1025] dark:text-white">{s.v}</p>
                                            <p className="text-[10px] text-[#999] dark:text-[#555] font-bold uppercase tracking-widest mt-1">{s.l}</p>
                                        </div>
                                    ))}
                                </div>
                                <div className="border-t border-[#e5e5e5] dark:border-[#1a1a2e] px-8 py-4 flex items-center gap-6 text-xs text-[#999] dark:text-[#555]">
                                    <span><i className="fa-solid fa-shield-halved mr-1.5 text-[#7c3aed]"></i> SSL Otomatis</span>
                                    <span><i className="fa-solid fa-database mr-1.5 text-[#7c3aed]"></i> MySQL Bawaan</span>
                                    <span><i className="fa-solid fa-terminal mr-1.5 text-[#7c3aed]"></i> Web Terminal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* ═══ SERVICES ═══ */}
            <section id="services" className="bg-[#fafafa] dark:bg-[#0a0a14]">
                <div className="max-w-6xl mx-auto px-6 py-24 lg:py-32">
                    <div className="mb-16" data-reveal>
                        <span className="text-[11px] font-bold text-[#7c3aed] uppercase tracking-[0.2em] mb-4 block">Layanan</span>
                        <h2 className="text-4xl font-black text-[#1a1025] dark:text-white tracking-tight">Infrastruktur & Layanan.</h2>
                    </div>

                    <div className="grid md:grid-cols-2 gap-px bg-[#e5e5e5] dark:bg-[#1a1a2e] border border-[#e5e5e5] dark:border-[#1a1a2e]" data-reveal>
                        <div className="bg-white dark:bg-[#0d0d18] p-8 lg:p-10 flex flex-col">
                            <div className="w-10 h-10 bg-[#1a1025] dark:bg-white dark:text-[#1a1025] flex items-center justify-center mb-8">
                                <i className="fa-solid fa-code text-white dark:text-[#1a1025] text-sm"></i>
                            </div>
                            <h3 className="text-2xl font-black text-[#1a1025] dark:text-white mb-3">Jasa Pembuatan Sistem</h3>
                            <p className="text-[#666] dark:text-[#888] text-[15px] leading-relaxed mb-8 flex-1">
                                Sistem informasi, aplikasi SaaS, website korporat, prototipe fungsional. Arsitektur modern, bersih, terdokumentasi.
                            </p>
                            <ul className="space-y-3 mb-8 text-sm text-[#444] dark:text-[#999]">
                                {['Backend & API Design', 'Frontend Modern (React / Vue)', 'Keamanan & Skalabilitas'].map(f => (
                                    <li key={f} className="flex items-center gap-3">
                                        <span className="w-4 h-4 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 flex items-center justify-center flex-shrink-0"><i className="fa-solid fa-check text-[8px] text-[#7c3aed]"></i></span>
                                        {f}
                                    </li>
                                ))}
                            </ul>
                            <a href="/consultation" className="inline-flex items-center justify-center px-6 py-2.5 border border-[#1a1025] dark:border-[#333] text-[#1a1025] dark:text-white text-sm font-semibold hover:bg-[#1a1025] hover:text-white dark:hover:bg-white dark:hover:text-[#1a1025] transition-colors w-full sm:w-auto">
                                Mulai Konsultasi
                            </a>
                        </div>

                        <div className="bg-white dark:bg-[#0d0d18] p-8 lg:p-10 flex flex-col relative overflow-hidden">
                            <div className="absolute top-0 right-0 w-32 h-32 bg-[#f5f0ff] dark:bg-[#7c3aed]/5 rounded-bl-[80px]"></div>
                            <div className="w-10 h-10 bg-[#7c3aed] flex items-center justify-center mb-8 relative z-10">
                                <i className="fa-solid fa-rocket text-white text-sm"></i>
                            </div>
                            <div className="relative z-10">
                                <div className="flex items-end justify-between mb-3">
                                    <h3 className="text-2xl font-black text-[#1a1025] dark:text-white">Shared App Hosting</h3>
                                    <div className="text-right">
                                        {starterPricing.promo > 0 && <span className="text-xs text-[#999] dark:text-[#555] price-strike block">Rp {rupiah(starterPricing.normal)}</span>}
                                        <div className="flex items-baseline gap-1">
                                            <span className="text-2xl font-black text-[#1a1025] dark:text-white">Rp {rupiah(starterPricing.active)}</span>
                                            <span className="text-xs text-[#999] dark:text-[#555]">/bln</span>
                                        </div>
                                    </div>
                                </div>
                                <p className="text-[#666] dark:text-[#888] text-[15px] leading-relaxed mb-8">
                                    Deployment otomatis dari Git ke server. Web terminal, process manager, database bawaan.
                                </p>
                            </div>
                            <ul className="space-y-3 mb-8 text-sm text-[#444] dark:text-[#999] relative z-10">
                                {['Auto Deploy (Node, PHP, Python)', 'Database MySQL & SSL Gratis', 'File Manager & Web Terminal'].map(f => (
                                    <li key={f} className="flex items-center gap-3">
                                        <span className="w-4 h-4 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 flex items-center justify-center flex-shrink-0"><i className="fa-solid fa-check text-[8px] text-[#7c3aed]"></i></span>
                                        {f}
                                    </li>
                                ))}
                            </ul>
                            <a href="/register" className="inline-flex items-center justify-center px-6 py-2.5 bg-[#7c3aed] text-white text-sm font-semibold hover:bg-[#6d28d9] transition-colors w-full sm:w-auto relative z-10">
                                Deploy Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {/* ═══ PRICING ═══ */}
            <section id="pricing" className="bg-white dark:bg-[#0d0d18] border-y border-[#e5e5e5] dark:border-[#1a1a2e]">
                <div className="max-w-6xl mx-auto px-6 py-24 lg:py-32">
                    <div className="mb-16" data-reveal>
                        <span className="text-[11px] font-bold text-[#7c3aed] uppercase tracking-[0.2em] mb-4 block">Harga</span>
                        <h2 className="text-4xl font-black text-[#1a1025] dark:text-white tracking-tight mb-3">Pilih Paket Hosting</h2>
                        <p className="text-[#666] dark:text-[#888] text-[15px]">Transparan. Tanpa biaya tersembunyi.</p>
                    </div>

                    <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-px bg-[#e5e5e5] dark:bg-[#1a1a2e] border border-[#e5e5e5] dark:border-[#1a1a2e] mb-16" data-reveal>
                        {activePlans.map(([slug, plan]) => {
                            const p = planPricing[slug];
                            const pop = slug === 'pro';
                            return (
                                <div key={slug} className={`${pop ? 'bg-[#1a1025] dark:bg-[#2d1f42] text-white border-[#2d1f42] dark:border-[#3d2f52]' : 'bg-white dark:bg-[#0d0d18] border-[#e5e5e5] dark:border-[#1a1a2e]'} p-8 flex flex-col relative`}>
                                    {pop && <div className="bg-[#7c3aed] text-white text-[10px] font-bold tracking-widest uppercase text-center py-1.5 -mx-8 -mt-8 mb-6">Paling Populer</div>}
                                    <h3 className={`text-lg font-bold mb-4 ${pop ? 'text-white' : 'text-[#1a1025] dark:text-white'}`}>{plan.label}</h3>
                                    <div className="mb-6">
                                        {p.promo !== null && <span className={`text-xs price-strike block mb-1 ${pop ? 'text-[#a78bfa]' : 'text-[#999] dark:text-[#555]'}`}>Rp {rupiah(p.normal)}</span>}
                                        {p.promo === null && <div className="h-4 mb-1"></div>}
                                        <div className="flex items-baseline gap-1">
                                            <span className={`text-3xl font-black tracking-tight ${pop ? 'text-white' : 'text-[#1a1025] dark:text-white'}`}>Rp {rupiah(p.active)}</span>
                                            <span className={`text-sm ${pop ? 'text-[#a78bfa]' : 'text-[#999] dark:text-[#555]'}`}>/bln</span>
                                        </div>
                                    </div>
                                    <ul className="space-y-2.5 flex-1">
                                        {plan.features.map(f => (
                                            <li key={f} className={`flex items-start gap-2.5 text-[13px] ${pop ? 'text-[#d4c4f0]' : 'text-[#666] dark:text-[#999]'}`}>
                                                <i className={`fa-solid fa-check text-[9px] mt-[5px] ${pop ? 'text-[#a78bfa]' : 'text-[#7c3aed]'}`}></i>
                                                <span>{f}</span>
                                            </li>
                                        ))}
                                    </ul>
                                    <a href="/register" className={`mt-8 flex items-center justify-center w-full py-2.5 text-sm font-semibold transition-colors ${pop ? 'bg-white text-[#1a1025] hover:bg-slate-100' : 'bg-[#1a1025] dark:bg-white dark:text-[#1a1025] text-white hover:bg-[#2d1f42] dark:hover:bg-slate-200'}`}>
                                        Pilih Paket
                                    </a>
                                </div>
                            );
                        })}
                    </div>

                    <div className="grid grid-cols-2 md:grid-cols-4 gap-px bg-[#e5e5e5] dark:bg-[#1a1a2e] border border-[#e5e5e5] dark:border-[#1a1a2e]" data-reveal>
                        {[
                            { i: 'fa-shield-halved', l: 'SSL Gratis' },
                            { i: 'fa-database', l: 'MySQL' },
                            { i: 'fa-rotate', l: 'Auto-Deploy' },
                            { i: 'fa-headset', l: 'Support' },
                        ].map(f => (
                            <div key={f.l} className="bg-[#fafafa] dark:bg-[#0a0a14] flex items-center justify-center gap-3 py-5">
                                <i className={`fa-solid ${f.i} text-[#7c3aed] text-sm`}></i>
                                <span className="text-sm font-semibold text-[#1a1025] dark:text-white">{f.l}</span>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ═══ BLOG ═══ */}
            <section id="blog" className="bg-[#fafafa] dark:bg-[#0a0a14]">
                <div className="max-w-6xl mx-auto px-6 py-24 lg:py-32">
                    <div className="flex items-end justify-between mb-12" data-reveal>
                        <div>
                            <span className="text-[11px] font-bold text-[#7c3aed] uppercase tracking-[0.2em] mb-3 block">Blog</span>
                            <h2 className="text-4xl font-black text-[#1a1025] dark:text-white tracking-tight">Artikel Terbaru</h2>
                        </div>
                        <a href="/blog" className="text-sm font-semibold text-[#7c3aed] hover:text-[#6d28d9] transition-colors hidden md:inline-flex items-center gap-2">
                            Semua Artikel <i className="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>

                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-px bg-[#e5e5e5] dark:bg-[#1a1a2e] border border-[#e5e5e5] dark:border-[#1a1a2e]" data-reveal>
                        {articles.length > 0 ? articles.map(a => (
                            <a key={a.id} href={a.url} className="bg-white dark:bg-[#0d0d18] group">
                                {a.cover_image ? (
                                    <div className="h-44 overflow-hidden bg-[#f5f5f5] dark:bg-[#111]"><img src={a.cover_image} alt={a.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" /></div>
                                ) : (
                                    <div className="h-44 bg-[#f5f5f5] dark:bg-[#111] flex items-center justify-center text-[#ccc] dark:text-[#333]"><i className="fa-solid fa-newspaper text-4xl"></i></div>
                                )}
                                <div className="p-6">
                                    {a.category && <span className="text-[10px] font-bold text-[#7c3aed] uppercase tracking-widest">{a.category.name}</span>}
                                    <h3 className="text-[15px] font-bold text-[#1a1025] dark:text-white mt-2 mb-2 group-hover:text-[#7c3aed] transition-colors line-clamp-2">{a.title}</h3>
                                    <p className="text-sm text-[#999] dark:text-[#555] line-clamp-2 mb-4">{a.excerpt || ''}</p>
                                    <div className="flex items-center gap-3 text-xs text-[#bbb] dark:text-[#444] pt-4 border-t border-[#f0f0f0] dark:border-[#1a1a2e]">
                                        <span>{a.published_at}</span><span>&middot;</span><span>{a.reading_time} mnt</span>
                                    </div>
                                </div>
                            </a>
                        )) : (
                            <div className="bg-white dark:bg-[#0d0d18] py-16 text-center col-span-full text-sm text-[#999]">Belum ada artikel.</div>
                        )}
                    </div>

                    <div className="mt-8 md:hidden text-center">
                        <a href="/blog" className="text-sm font-semibold text-[#7c3aed]">Semua Artikel →</a>
                    </div>
                </div>
            </section>

            {/* ═══ FAQ ═══ */}
            <section id="faq" className="bg-white dark:bg-[#0d0d18] border-y border-[#e5e5e5] dark:border-[#1a1a2e]">
                <div className="max-w-3xl mx-auto px-6 py-24 lg:py-32">
                    <div className="mb-12" data-reveal>
                        <span className="text-[11px] font-bold text-[#7c3aed] uppercase tracking-[0.2em] mb-4 block">FAQ</span>
                        <h2 className="text-4xl font-black text-[#1a1025] dark:text-white tracking-tight">Pertanyaan Umum</h2>
                    </div>
                    <div data-reveal>
                        {faqData.map((f, i) => <Faq key={i} q={f.q} a={f.a} />)}
                    </div>
                </div>
            </section>

            {/* ═══ CTA ═══ */}
            <section className="bg-[#1a1025]">
                <div className="max-w-6xl mx-auto px-6 py-24 lg:py-32 text-center" data-reveal>
                    <h2 className="text-4xl md:text-5xl font-black text-white tracking-tight mb-6">Siap Mengeksekusi Ide?</h2>
                    <p className="text-[#a78bfa] text-lg mb-10 max-w-lg mx-auto">Daftar gratis, deploy dari Git, jalankan dalam hitungan menit.</p>
                    <a href="/register" className="inline-flex px-8 py-3.5 bg-[#7c3aed] text-white text-sm font-semibold hover:bg-[#6d28d9] transition-colors">
                        Mulai Sekarang
                    </a>
                </div>
            </section>

            {/* ═══ CHATBOT ═══ */}
            <div className="fixed bottom-6 right-6 z-50">
                {chatOpen && (
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] shadow-2xl w-80 h-96 mb-4 flex flex-col overflow-hidden">
                        <div className="bg-[#1a1025] px-4 py-3 flex justify-between items-center">
                            <div className="flex items-center gap-2">
                                <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span className="text-white font-bold text-sm">Ryaze Assistant</span>
                            </div>
                            <button onClick={() => setChatOpen(false)} className="text-white/50 hover:text-white"><i className="fa-solid fa-xmark"></i></button>
                        </div>
                        <div className="flex-1 p-4 overflow-y-auto space-y-3 bg-[#fafafa] dark:bg-[#0a0a14]">
                            {chatMsgs.length === 0 && (
                                <div className="flex items-start gap-2">
                                    <div className="w-6 h-6 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 flex items-center justify-center shrink-0"><i className="fa-solid fa-robot text-[10px] text-[#7c3aed]"></i></div>
                                    <div className="bg-white dark:bg-[#1a1025] border border-[#e5e5e5] dark:border-[#2d1f42] px-3 py-2 text-sm text-[#444] dark:text-[#ccc] max-w-[85%]">Halo! Saya asisten AI Ryaze. Ada yang bisa saya bantu?</div>
                                </div>
                            )}
                            {chatMsgs.map(m => (
                                <div key={m.id} className={`flex items-start gap-2 ${m.user ? 'flex-row-reverse' : ''}`}>
                                    <div className={`w-6 h-6 flex items-center justify-center shrink-0 ${m.user ? 'bg-[#e5e5e5] dark:bg-[#333]' : 'bg-[#f5f0ff] dark:bg-[#7c3aed]/10'}`}>
                                        <i className={`fa-solid ${m.user ? 'fa-user text-[10px] text-[#999]' : 'fa-robot text-[10px] text-[#7c3aed]'}`}></i>
                                    </div>
                                    <div className={`px-3 py-2 text-sm max-w-[85%] ${m.user ? 'bg-[#1a1025] dark:bg-[#7c3aed] text-white' : 'bg-white dark:bg-[#1a1025] border border-[#e5e5e5] dark:border-[#2d1f42] text-[#444] dark:text-[#ccc]'}`}>{m.text}</div>
                                </div>
                            ))}
                            {chatLoading && (
                                <div className="flex items-start gap-2">
                                    <div className="w-6 h-6 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 flex items-center justify-center shrink-0"><i className="fa-solid fa-robot text-[10px] text-[#7c3aed]"></i></div>
                                    <div className="bg-white dark:bg-[#1a1025] border border-[#e5e5e5] dark:border-[#2d1f42] px-4 py-3 flex gap-1">
                                        <div className="w-1.5 h-1.5 bg-[#ccc] dark:bg-[#555] rounded-full animate-bounce"></div>
                                        <div className="w-1.5 h-1.5 bg-[#ccc] dark:bg-[#555] rounded-full animate-bounce" style={{ animationDelay: '0.1s' }}></div>
                                        <div className="w-1.5 h-1.5 bg-[#ccc] dark:bg-[#555] rounded-full animate-bounce" style={{ animationDelay: '0.2s' }}></div>
                                    </div>
                                </div>
                            )}
                        </div>
                        <form onSubmit={sendChat} className="p-3 bg-white dark:bg-[#0d0d18] border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex gap-2">
                            <input type="text" value={chatInput} onChange={e => setChatInput(e.target.value)} placeholder="Ketik pesan..." required className="flex-1 bg-[#fafafa] dark:bg-[#0a0a14] border border-[#e5e5e5] dark:border-[#2d1f42] text-sm px-4 py-2 focus:outline-none focus:border-[#7c3aed] transition-colors dark:text-white" />
                            <button type="submit" disabled={chatLoading || !chatInput.trim()} className="w-9 h-9 bg-[#7c3aed] text-white flex items-center justify-center hover:bg-[#6d28d9] transition-colors disabled:opacity-40"><i className="fa-solid fa-paper-plane text-[10px]"></i></button>
                        </form>
                    </div>
                )}
                <button onClick={() => setChatOpen(!chatOpen)} className="w-14 h-14 bg-[#7c3aed] hover:bg-[#6d28d9] text-white rounded-full flex items-center justify-center shadow-lg shadow-[#7c3aed]/30 transition-all hover:scale-105 ml-auto relative">
                    <i className={`fa-solid ${chatOpen ? 'fa-xmark' : 'fa-message'} text-xl`}></i>
                    {!chatOpen && <span className="absolute -top-0.5 -right-0.5 w-3 h-3 bg-rose-500 border-2 border-white dark:border-[#1a1025] rounded-full"></span>}
                </button>
            </div>
        </PublicLayout>
    );
}
