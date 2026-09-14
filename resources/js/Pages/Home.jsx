import PublicLayout from '../Layouts/PublicLayout';
import { Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';

function formatRupiah(n) {
    return new Intl.NumberFormat('id-ID').format(n);
}

const faqItems = [
    { q: 'Apa itu Ryaze?', a: 'Ryaze adalah platform layanan jasa pembuatan website dan aplikasi sekaligus penyedia shared hosting Indonesia dengan auto-deploy dari repositori Git, SSL gratis, database MySQL, web terminal, dan panel kontrol lengkap.' },
    { q: 'Teknologi apa saja yang didukung?', a: 'Hosting Ryaze mendukung Node.js, PHP (termasuk Laravel), Python, React, Vue.js, dan website statis HTML. Setiap project di-deploy otomatis dari repositori Git Anda.' },
    { q: 'Apakah SSL gratis tersedia?', a: 'Ya, setiap project hosting di Ryaze otomatis mendapatkan sertifikat SSL gratis sehingga website Anda aman dan diakses melalui HTTPS.' },
    { q: 'Apakah tersedia database untuk project saya?', a: 'Ya, setiap project mendapatkan database MySQL bawaan yang dapat dikelola melalui panel, mini phpMyAdmin, dan API key untuk koneksi aplikasi.' },
    { q: 'Apakah bisa request jasa pembuatan website?', a: 'Bisa. Ryaze menerima pengerjaan sistem informasi, aplikasi SaaS, website korporat, hingga prototipe fungsional dengan arsitektur modern yang bersih dan terdokumentasi.' },
    { q: 'Bagaimana cara mulai menggunakan Ryaze?', a: 'Cukup daftar akun secara gratis, pilih paket hosting yang sesuai, lalu deploy project Anda langsung dari repositori Git dalam hitungan menit.' },
];

const colorMap = {
    slate: { bg: 'bg-white border border-slate-100', text: 'text-slate-900', btn: 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200', check: 'text-purple-400' },
    indigo: { bg: 'bg-white border border-slate-100', text: 'text-slate-900', btn: 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200', check: 'text-purple-400' },
    violet: { bg: 'bg-gradient-to-br from-purple-600 to-violet-600 text-white', text: 'text-white', btn: 'bg-white text-purple-700 hover:bg-purple-50', check: 'text-purple-200' },
    amber: { bg: 'bg-white border border-slate-100', text: 'text-slate-900', btn: 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200', check: 'text-purple-400' },
};

function FaqItem({ q, a, defaultOpen }) {
    const [open, setOpen] = useState(defaultOpen || false);
    return (
        <div className={`border border-slate-100 rounded-2xl overflow-hidden transition-all ${open ? 'bg-white shadow-sm shadow-purple-100' : 'bg-white hover:border-purple-100'}`}>
            <button onClick={() => setOpen(!open)} className="w-full flex items-center justify-between px-6 py-5 text-left">
                <span className="font-semibold text-slate-900 text-sm">{q}</span>
                <i className={`fa-solid fa-plus text-xs text-purple-500 transition-transform duration-300 ${open ? 'rotate-45' : ''}`}></i>
            </button>
            <div className={`overflow-hidden transition-all duration-300 ${open ? 'max-h-40' : 'max-h-0'}`}>
                <p className="px-6 pb-5 text-sm text-slate-500 leading-relaxed">{a}</p>
            </div>
        </div>
    );
}

export default function Home({ plans, planPricing, articles, starterPricing, siteName, socialLinks, url }) {
    useEffect(() => {
        const els = document.querySelectorAll('.reveal');
        if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            els.forEach(el => el.classList.add('reveal-visible'));
            return;
        }
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('reveal-visible'); io.unobserve(e.target); } });
        }, { threshold: 0.08 });
        els.forEach(el => io.observe(el));
        return () => io.disconnect();
    }, []);

    const activePlans = Object.entries(plans).filter(([, p]) => p.is_active);

    return (
        <PublicLayout title="Jasa Pembuatan Website & Shared Hosting Indonesia" description="Platform hosting & development modern untuk bisnis Anda.">
            <style>{`
                .reveal { opacity:0; transform:translateY(24px); transition: opacity .6s cubic-bezier(.16,1,.3,1), transform .6s cubic-bezier(.16,1,.3,1); }
                .reveal.reveal-visible { opacity:1; transform:translateY(0); }
                @media(prefers-reduced-motion:reduce){.reveal{opacity:1;transform:none;transition:none}}
            `}</style>

            {/* HERO */}
            <section className="relative pt-32 pb-24 lg:pt-48 lg:pb-32 bg-white min-h-[90vh] flex items-center">
                <div className="absolute inset-0 overflow-hidden pointer-events-none">
                    <div className="absolute top-20 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-gradient-to-b from-purple-100/60 via-violet-50/30 to-transparent rounded-full blur-3xl"></div>
                    <div className="absolute bottom-0 inset-x-0 h-40 bg-gradient-to-t from-white to-transparent"></div>
                </div>

                <div className="max-w-5xl mx-auto px-6 relative z-10 text-center">
                    <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-purple-50 border border-purple-100 text-purple-700 text-xs font-semibold mb-8">
                        <span className="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                        Platform Deployment Tersedia
                    </div>

                    <h1 className="text-5xl md:text-7xl font-bold tracking-tight text-slate-900 leading-[1.1] mb-6">
                        Bangun Produk Digital<br className="hidden md:block" /> Lebih Cepat & Kuat.
                    </h1>

                    <p className="text-lg text-slate-500 max-w-2xl mx-auto mb-10 leading-relaxed">
                        Jasa pembuatan website & aplikasi terpercaya, plus shared hosting Indonesia dengan auto-deploy, SSL gratis, dan database MySQL.
                    </p>

                    <div className="flex flex-col sm:flex-row justify-center gap-4 mb-20">
                        <a href="#pricing" className="px-8 py-3.5 text-sm font-semibold rounded-full text-white bg-purple-600 hover:bg-purple-700 transition-all shadow-lg shadow-purple-200">
                            Lihat Paket Hosting
                        </a>
                        <a href="#services" className="px-8 py-3.5 text-sm font-semibold rounded-full text-slate-700 bg-white border border-slate-200 hover:border-purple-200 hover:text-purple-700 transition-all">
                            Jelajahi Layanan
                        </a>
                    </div>

                    <div className="grid grid-cols-3 gap-8 max-w-2xl mx-auto mb-20">
                        <div>
                            <p className="text-3xl md:text-4xl font-bold text-slate-900">99.9%</p>
                            <p className="text-xs text-slate-400 mt-1 font-medium uppercase tracking-wider">Uptime</p>
                        </div>
                        <div className="border-x border-slate-100">
                            <p className="text-3xl md:text-4xl font-bold text-slate-900">100+</p>
                            <p className="text-xs text-slate-400 mt-1 font-medium uppercase tracking-wider">Project</p>
                        </div>
                        <div>
                            <p className="text-3xl md:text-4xl font-bold text-slate-900">&lt;5mnt</p>
                            <p className="text-xs text-slate-400 mt-1 font-medium uppercase tracking-wider">Deploy</p>
                        </div>
                    </div>

                    <div className="pt-10 border-t border-slate-100">
                        <p className="text-xs text-slate-400 uppercase tracking-widest mb-6 font-semibold">Didukung Teknologi</p>
                        <div className="flex flex-wrap justify-center gap-8 text-slate-300">
                            {['fa-brands fa-laravel', 'fa-brands fa-react', 'fa-brands fa-node-js', 'fa-brands fa-python', 'fa-brands fa-vuejs', 'fa-brands fa-docker'].map(icon => (
                                <i key={icon} className={`${icon} text-3xl hover:text-purple-400 transition-colors cursor-default`}></i>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* ABOUT */}
            <section id="about" className="py-24 bg-slate-50/50">
                <div className="max-w-7xl mx-auto px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div className="reveal">
                            <span className="text-xs font-bold uppercase tracking-widest text-purple-600 mb-3 block">Tentang Ryaze</span>
                            <h2 className="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 mb-6">Platform Hosting & Development untuk Bisnis Anda.</h2>
                            <p className="text-slate-500 mb-4 leading-relaxed">Ryaze adalah platform shared hosting Indonesia yang dirancang untuk developer dan bisnis yang menginginkan deployment cepat, infrastruktur andal, dan kontrol penuh.</p>
                            <p className="text-slate-500 mb-8 leading-relaxed">Kami menyediakan lingkungan hosting otomatis dengan auto-deploy dari Git, SSL gratis, database lengkap, serta panel kontrol berfitur tinggi.</p>
                            <div className="flex flex-wrap gap-2">
                                {['AUTO DEPLOY', 'SSL GRATIS', 'FULLSTACK WEB', 'SHARED SERVER'].map(tag => (
                                    <span key={tag} className="px-4 py-1.5 bg-purple-50 border border-purple-100 rounded-full text-xs font-bold text-purple-600">{tag}</span>
                                ))}
                            </div>
                        </div>
                        <div className="flex justify-center lg:justify-end reveal">
                            <div className="w-full max-w-sm bg-white rounded-3xl border border-slate-100 p-8 shadow-xl shadow-slate-100/50">
                                <div className="text-center mb-6">
                                    <div className="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i className="fa-solid fa-rocket text-2xl text-purple-600"></i>
                                    </div>
                                    <h3 className="font-bold text-slate-900 text-lg">Ryaze Hosting</h3>
                                    <p className="text-slate-400 text-sm mt-1">Platform Deployment Modern</p>
                                </div>
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="text-center p-4 bg-slate-50 rounded-xl">
                                        <p className="text-2xl font-bold text-slate-900">99.9%</p>
                                        <p className="text-xs text-slate-400 mt-1 font-medium uppercase tracking-wider">Uptime</p>
                                    </div>
                                    <div className="text-center p-4 bg-slate-50 rounded-xl">
                                        <p className="text-2xl font-bold text-slate-900">&lt;5mnt</p>
                                        <p className="text-xs text-slate-400 mt-1 font-medium uppercase tracking-wider">Deploy</p>
                                    </div>
                                </div>
                                <div className="mt-6 px-4 py-3 bg-purple-50 rounded-xl flex items-center justify-center gap-2 text-xs font-semibold text-purple-700">
                                    <span className="w-2 h-2 rounded-full bg-emerald-500"></span> Server Aktif 24/7
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* SERVICES */}
            <section id="services" className="py-24 bg-white">
                <div className="max-w-7xl mx-auto px-6 lg:px-8">
                    <div className="mb-16 max-w-2xl reveal">
                        <span className="text-xs font-bold uppercase tracking-widest text-purple-600 mb-3 block">Layanan</span>
                        <h2 className="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 mb-4">Infrastruktur & Layanan.</h2>
                        <p className="text-slate-500 text-lg">Kami merancang arsitektur web dan infrastruktur shared hosting yang andal.</p>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8 reveal">
                        <div className="rounded-3xl border border-slate-100 p-8 lg:p-10 flex flex-col h-full hover:border-purple-200 hover:shadow-lg hover:shadow-purple-50 transition-all group">
                            <div className="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-8">
                                <i className="fa-solid fa-laptop-code text-xl"></i>
                            </div>
                            <h3 className="text-2xl font-bold text-slate-900 mb-3">Jasa Pembuatan Sistem</h3>
                            <p className="text-slate-500 text-base leading-relaxed mb-8">Pengerjaan sistem informasi, aplikasi SaaS, website korporat, hingga prototipe fungsional. Berbasis arsitektur modern.</p>
                            <ul className="space-y-3 mb-10 text-sm font-medium text-slate-600 flex-1">
                                {['Backend & API Design', 'Frontend Modern (React/Vue)', 'Keamanan & Skalabilitas Tinggi'].map(item => (
                                    <li key={item} className="flex items-center gap-3"><i className="fa-solid fa-check text-purple-400 text-xs"></i>{item}</li>
                                ))}
                            </ul>
                            <a href="/consultation" className="inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-full transition-colors w-full sm:w-auto">
                                Mulai Konsultasi <i className="fa-solid fa-arrow-right ml-2 text-xs"></i>
                            </a>
                        </div>
                        <div className="rounded-3xl border border-slate-100 p-8 lg:p-10 flex flex-col h-full hover:border-purple-200 hover:shadow-lg hover:shadow-purple-50 transition-all group relative overflow-hidden">
                            <div className="absolute top-0 right-0 w-40 h-40 bg-gradient-to-bl from-purple-50 to-transparent rounded-bl-full pointer-events-none"></div>
                            <div className="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-8 relative z-10">
                                <i className="fa-solid fa-server text-xl"></i>
                            </div>
                            <div className="flex flex-col sm:flex-row sm:items-end justify-between mb-3 gap-4 relative z-10">
                                <h3 className="text-2xl font-bold text-slate-900">Shared App Hosting</h3>
                                <div className="flex items-baseline gap-1">
                                    {starterPricing.promo > 0 && <span className="text-xs text-slate-400 line-through">Rp {formatRupiah(starterPricing.normal)}</span>}
                                    <span className="text-2xl font-bold text-slate-900">Rp {formatRupiah(starterPricing.active)}</span>
                                    <span className="text-xs text-slate-400">/bln</span>
                                </div>
                            </div>
                            <p className="text-slate-500 text-base leading-relaxed mb-8 relative z-10">Hosting murah dengan deployment otomatis. Eksekusi repositori kode langsung ke server publik.</p>
                            <ul className="space-y-3 mb-10 text-sm font-medium text-slate-600 flex-1 relative z-10">
                                {['Auto Deploy (Node, PHP, Python)', 'Database (MySQL) & SSL Gratis', 'File Manager & Web Terminal'].map(item => (
                                    <li key={item} className="flex items-center gap-3"><i className="fa-solid fa-check text-purple-400 text-xs"></i>{item}</li>
                                ))}
                            </ul>
                            <a href="/register" className="inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-full transition-colors shadow-lg shadow-purple-200 w-full sm:w-auto relative z-10">
                                Deploy Sekarang <i className="fa-solid fa-arrow-right ml-2 text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {/* PRICING */}
            <section id="pricing" className="py-24 bg-slate-50/50">
                <div className="max-w-7xl mx-auto px-6 lg:px-8">
                    <div className="mb-16 text-center reveal">
                        <span className="text-xs font-bold uppercase tracking-widest text-purple-600 mb-3 block">Harga Transparan</span>
                        <h2 className="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 mb-4">Pilih Paket Hosting</h2>
                        <p className="text-slate-500 text-lg max-w-2xl mx-auto">Deploy project Anda sekarang. Mulai dari harga terjangkau dengan fitur lengkap.</p>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 reveal">
                        {activePlans.map(([slug, plan]) => {
                            const p = planPricing[slug];
                            const hc = colorMap[plan.color] || colorMap.slate;
                            const popular = slug === 'pro';
                            return (
                                <div key={slug} className={`relative flex flex-col rounded-3xl overflow-hidden transition-all ${popular ? 'bg-gradient-to-br from-purple-600 to-violet-600 text-white border border-purple-500 shadow-xl shadow-purple-200 scale-[1.02] z-10' : 'bg-white border border-slate-100 hover:border-purple-200 hover:shadow-lg hover:shadow-purple-50'}`}>
                                    {popular && <div className="text-center py-1.5 bg-white/10 text-white text-[10px] font-bold uppercase tracking-widest">Paling Populer</div>}
                                    <div className="p-8 flex-1">
                                        <h3 className={`text-lg font-semibold mb-2 ${popular ? 'text-white' : 'text-slate-900'}`}>{plan.label}</h3>
                                        <div className="mb-6">
                                            {p.promo !== null && <span className={`text-xs line-through block mb-1 ${popular ? 'text-purple-200' : 'text-slate-400'}`}>Rp {formatRupiah(p.normal)}</span>}
                                            {p.promo === null && <div className="h-4 mb-1"></div>}
                                            <div className="flex items-baseline gap-1">
                                                <span className={`text-3xl font-bold tracking-tight ${popular ? 'text-white' : 'text-slate-900'}`}>Rp {formatRupiah(p.active)}</span>
                                                <span className={`text-sm ${popular ? 'text-purple-200' : 'text-slate-400'}`}>/bln</span>
                                            </div>
                                        </div>
                                        <ul className="space-y-3">
                                            {plan.features.map(f => (
                                                <li key={f} className={`flex items-start gap-3 text-sm ${popular ? 'text-purple-100' : 'text-slate-500'}`}>
                                                    <i className={`fa-solid fa-check text-[10px] mt-1.5 ${popular ? 'text-purple-300' : 'text-purple-400'}`}></i><span>{f}</span>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                    <div className="p-8 pt-0">
                                        <a href="/register" className={`flex items-center justify-center w-full py-2.5 rounded-full text-sm font-semibold transition-all ${popular ? 'bg-white text-purple-700 hover:bg-purple-50 shadow-lg' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200'}`}>
                                            Pilih Paket
                                        </a>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                    <div className="mt-16 pt-8 border-t border-slate-200/60 grid grid-cols-2 md:grid-cols-4 gap-6 reveal">
                        {[{ icon: 'fa-shield-halved', label: 'SSL Gratis' }, { icon: 'fa-database', label: 'Database MySQL' }, { icon: 'fa-rotate', label: 'Auto-Deploy Git' }, { icon: 'fa-headset', label: 'Support 1-on-1' }].map(item => (
                            <div key={item.label} className="flex items-center justify-center gap-3">
                                <div className="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-600"><i className={`fa-solid ${item.icon}`}></i></div>
                                <span className="text-sm font-medium text-slate-700">{item.label}</span>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* BLOG */}
            <section id="blog" className="py-24 bg-white">
                <div className="max-w-7xl mx-auto px-6 lg:px-8">
                    <div className="flex flex-col md:flex-row justify-between items-end gap-6 mb-12 reveal">
                        <div>
                            <h2 className="text-3xl font-bold tracking-tight text-slate-900 mb-2">Artikel Terbaru</h2>
                            <p className="text-slate-500 text-sm">Tulisan seputar web development, tips hosting, dan wawasan teknologi.</p>
                        </div>
                        <a href="/blog" className="hidden md:inline-flex text-sm font-semibold text-purple-600 items-center gap-2 hover:text-purple-700 transition-colors">
                            Lihat Semua <i className="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 reveal">
                        {articles.length > 0 ? articles.map(article => (
                            <a key={article.id} href={article.url} className="group bg-white border border-slate-100 rounded-2xl overflow-hidden hover:shadow-xl hover:shadow-purple-50 hover:-translate-y-1 transition-all duration-300 flex flex-col">
                                {article.cover_image ? (
                                    <div className="h-48 overflow-hidden bg-slate-50"><img src={article.cover_image} alt={article.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" /></div>
                                ) : (
                                    <div className="h-48 bg-slate-50 flex items-center justify-center text-slate-300"><i className="fa-solid fa-newspaper text-5xl"></i></div>
                                )}
                                <div className="p-6 flex flex-col flex-1">
                                    {article.category && <span className="text-[10px] font-bold uppercase text-purple-600 mb-2">{article.category.name}</span>}
                                    <h3 className="text-lg font-bold text-slate-900 mb-2 group-hover:text-purple-600 transition-colors line-clamp-2">{article.title}</h3>
                                    <p className="text-slate-500 text-sm line-clamp-2 mb-4 flex-1">{article.excerpt || ''}</p>
                                    <div className="flex items-center gap-3 text-xs text-slate-400 mt-auto pt-4 border-t border-slate-50">
                                        <span>{article.published_at}</span><span>&middot;</span><span>{article.reading_time} min</span>
                                    </div>
                                </div>
                            </a>
                        )) : (
                            <div className="col-span-full py-12 border border-dashed border-slate-200 rounded-2xl text-center"><p className="text-sm text-slate-400">Belum ada artikel.</p></div>
                        )}
                    </div>
                    <div className="mt-8 md:hidden text-center">
                        <a href="/blog" className="text-sm font-semibold text-purple-600 inline-flex items-center gap-2">Lihat Semua <i className="fa-solid fa-arrow-right text-xs"></i></a>
                    </div>
                </div>
            </section>

            {/* FAQ */}
            <section id="faq" className="py-24 bg-slate-50/50">
                <div className="max-w-3xl mx-auto px-6 lg:px-8">
                    <div className="mb-14 text-center reveal">
                        <span className="text-xs font-bold uppercase tracking-widest text-purple-600 mb-3 block">Pertanyaan Umum</span>
                        <h2 className="text-3xl font-bold tracking-tight text-slate-900 mb-4">Yang Sering Ditanyakan</h2>
                        <p className="text-slate-500 text-base">Semua yang perlu Anda ketahui sebelum deploy.</p>
                    </div>
                    <div className="space-y-3 reveal">
                        {faqItems.map((item, i) => <FaqItem key={i} q={item.q} a={item.a} defaultOpen={i === 0} />)}
                    </div>
                </div>
            </section>

            {/* CTA */}
            <section className="py-24 bg-white text-center px-6">
                <div className="max-w-3xl mx-auto reveal">
                    <div className="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mx-auto mb-8">
                        <i className="fa-solid fa-rocket text-2xl text-purple-600"></i>
                    </div>
                    <h2 className="text-3xl md:text-5xl font-bold tracking-tight text-slate-900 mb-6">Siap Mengeksekusi Ide?</h2>
                    <p className="text-slate-500 text-lg mb-10 max-w-xl mx-auto">Daftar sekarang untuk mengakses lingkungan deployment yang kuat atau hubungi kami untuk pengerjaan perangkat lunak Anda.</p>
                    <a href="/register" className="inline-flex px-8 py-3.5 bg-purple-600 text-white text-sm font-semibold rounded-full hover:bg-purple-700 transition-all shadow-lg shadow-purple-200">
                        Mulai Secara Gratis
                    </a>
                </div>
            </section>

            {/* CHATBOT */}
            <ChatWidget />
        </PublicLayout>
    );
}

function ChatWidget() {
    const [open, setOpen] = useState(false);
    const [messages, setMessages] = useState([{ id: 0, text: 'Halo! Saya asisten AI Ryaze. Ada yang bisa saya bantu hari ini?', isUser: false }]);
    const [input, setInput] = useState('');
    const [loading, setLoading] = useState(false);
    const [history, setHistory] = useState([]);

    const send = async (e) => {
        e.preventDefault();
        const text = input.trim();
        if (!text || loading) return;
        setInput('');
        setMessages(prev => [...prev, { id: Date.now(), text, isUser: true }]);
        setLoading(true);
        try {
            const res = await fetch('/chat', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }, body: JSON.stringify({ message: text, history }) });
            if (res.ok) {
                const data = await res.json();
                if (data.reply) {
                    setMessages(prev => [...prev, { id: Date.now() + 1, text: data.reply, isUser: false }]);
                    setHistory(prev => [...prev.slice(-8), { role: 'user', content: text }, { role: 'assistant', content: data.reply }]);
                }
            }
        } catch {}
        setLoading(false);
    };

    return (
        <div className="fixed bottom-6 right-6 z-50 font-sans">
            {open && (
                <div className="bg-white border border-slate-200 shadow-2xl rounded-2xl w-80 h-96 mb-4 overflow-hidden flex flex-col">
                    <div className="bg-purple-600 px-4 py-3 text-white flex justify-between items-center">
                        <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div><span className="font-bold text-sm">Ryaze Assistant</span></div>
                        <button onClick={() => setOpen(false)} className="text-white/70 hover:text-white"><i className="fa-solid fa-xmark"></i></button>
                    </div>
                    <div className="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50">
                        {messages.map(m => (
                            <div key={m.id} className={`flex items-start gap-2 ${m.isUser ? 'flex-row-reverse' : ''}`}>
                                <div className={`w-6 h-6 rounded-full flex items-center justify-center shrink-0 ${m.isUser ? 'bg-slate-200' : 'bg-purple-100'}`}>
                                    <i className={`fa-solid ${m.isUser ? 'fa-user text-[10px] text-slate-500' : 'fa-robot text-[10px] text-purple-600'}`}></i>
                                </div>
                                <div className={`px-3 py-2 rounded-2xl text-sm max-w-[85%] ${m.isUser ? 'bg-purple-600 text-white rounded-tr-sm' : 'bg-white border border-slate-200 text-slate-700 rounded-tl-sm'}`}>{m.text}</div>
                            </div>
                        ))}
                        {loading && (
                            <div className="flex items-start gap-2">
                                <div className="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center shrink-0"><i className="fa-solid fa-robot text-[10px] text-purple-600"></i></div>
                                <div className="bg-white border border-slate-200 px-4 py-3 rounded-2xl rounded-tl-sm flex gap-1"><div className="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></div><div className="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style={{ animationDelay: '0.1s' }}></div><div className="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style={{ animationDelay: '0.2s' }}></div></div>
                            </div>
                        )}
                    </div>
                    <form onSubmit={send} className="p-3 bg-white border-t border-slate-100 flex gap-2">
                        <input type="text" value={input} onChange={e => setInput(e.target.value)} placeholder="Ketik pesan..." required className="flex-1 bg-slate-50 border border-slate-200 text-sm rounded-full px-4 py-2 focus:outline-none focus:border-purple-300 transition-all" />
                        <button type="submit" disabled={loading || !input.trim()} className="w-9 h-9 rounded-full bg-purple-600 text-white flex items-center justify-center hover:bg-purple-700 transition-colors shrink-0 disabled:opacity-50"><i className="fa-solid fa-paper-plane text-[10px]"></i></button>
                    </form>
                </div>
            )}
            <button onClick={() => setOpen(!open)} className="w-14 h-14 bg-purple-600 hover:bg-purple-700 text-white rounded-full flex items-center justify-center shadow-lg shadow-purple-200 transition-all hover:scale-105 ml-auto relative">
                <i className={`fa-solid ${open ? 'fa-xmark' : 'fa-message'} text-xl`}></i>
                {!open && <span className="absolute top-0 right-0 w-3.5 h-3.5 bg-rose-500 border-2 border-white rounded-full"></span>}
            </button>
        </div>
    );
}
