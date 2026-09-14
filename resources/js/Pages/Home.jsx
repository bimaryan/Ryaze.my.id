import PublicLayout from '../Layouts/PublicLayout';
import { Link } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';

function formatRupiah(n) {
    return new Intl.NumberFormat('id-ID').format(n);
}

const faqItems = [
    {
        q: 'Apa itu Ryaze?',
        a: 'Ryaze adalah platform layanan jasa pembuatan website dan aplikasi sekaligus penyedia shared hosting Indonesia dengan auto-deploy dari repositori Git, SSL gratis, database MySQL, web terminal, dan panel kontrol lengkap.',
    },
    {
        q: 'Teknologi apa saja yang didukung hosting Ryaze?',
        a: 'Hosting Ryaze mendukung Node.js, PHP (termasuk Laravel), Python, React, Vue.js, dan website statis HTML. Setiap project di-deploy otomatis dari repositori Git Anda.',
    },
    {
        q: 'Apakah SSL gratis tersedia?',
        a: 'Ya, setiap project hosting di Ryaze otomatis mendapatkan sertifikat SSL gratis sehingga website Anda aman dan diakses melalui HTTPS.',
    },
    {
        q: 'Apakah tersedia database untuk project saya?',
        a: 'Ya, setiap project mendapatkan database MySQL bawaan yang dapat dikelola melalui panel, mini phpMyAdmin, dan API key untuk koneksi aplikasi.',
    },
    {
        q: 'Apakah bisa request jasa pembuatan website atau aplikasi?',
        a: 'Bisa. Ryaze menerima pengerjaan sistem informasi, aplikasi SaaS, website korporat, hingga prototipe fungsional dengan arsitektur modern yang bersih dan terdokumentasi.',
    },
    {
        q: 'Bagaimana cara mulai menggunakan Ryaze?',
        a: 'Cukup daftar akun secara gratis, pilih paket hosting yang sesuai, lalu deploy project Anda langsung dari repositori Git dalam hitungan menit.',
    },
];

const colorMap = {
    slate: {
        bg: 'bg-white dark:bg-white/5 dark:backdrop-blur-xl',
        text: 'text-slate-900 dark:text-white',
        btn: 'bg-slate-100 dark:bg-white/10 text-slate-900 dark:text-white hover:bg-slate-200 dark:hover:bg-white/20 border border-transparent dark:border-white/10',
        check: 'text-slate-400 dark:text-slate-500',
    },
    indigo: {
        bg: 'bg-white dark:bg-white/5 dark:backdrop-blur-xl',
        text: 'text-slate-900 dark:text-white',
        btn: 'bg-slate-100 dark:bg-white/10 text-slate-900 dark:text-white hover:bg-slate-200 dark:hover:bg-white/20 border border-transparent dark:border-white/10',
        check: 'text-slate-400 dark:text-slate-500',
    },
    violet: {
        bg: 'bg-slate-900 dark:bg-indigo-500/10 dark:backdrop-blur-xl',
        text: 'text-white dark:text-white',
        btn: 'bg-white dark:bg-white text-slate-900 dark:text-slate-900 hover:bg-slate-100 dark:hover:bg-slate-200 border border-slate-700 dark:border-transparent',
        check: 'text-slate-400 dark:text-indigo-300',
    },
    amber: {
        bg: 'bg-white dark:bg-white/5 dark:backdrop-blur-xl',
        text: 'text-slate-900 dark:text-white',
        btn: 'bg-slate-100 dark:bg-white/10 text-slate-900 dark:text-white hover:bg-slate-200 dark:hover:bg-white/20 border border-transparent dark:border-white/10',
        check: 'text-slate-400 dark:text-slate-500',
    },
};

export default function Home({
    plans,
    planPricing,
    articles,
    starterPricing,
    siteName,
    socialLinks,
    url,
}) {
    const [chatOpen, setChatOpen] = useState(false);
    const [chatInput, setChatInput] = useState('');
    const [chatMessages, setChatMessages] = useState([]);
    const [chatLoading, setChatLoading] = useState(false);
    const [notifDot, setNotifDot] = useState(true);
    const chatHistoryRef = useRef([]);
    const messagesEndRef = useRef(null);

    useEffect(() => {
        const revealEls = document.querySelectorAll('.reveal');
        if (
            'IntersectionObserver' in window &&
            !window.matchMedia('(prefers-reduced-motion: reduce)').matches
        ) {
            const io = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('reveal-visible');
                            io.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.08 }
            );
            revealEls.forEach((el) => io.observe(el));
            return () => io.disconnect();
        } else {
            revealEls.forEach((el) => el.classList.add('reveal-visible'));
        }
    }, []);

    const toggleChat = () => {
        setChatOpen((prev) => !prev);
        if (!chatOpen) setNotifDot(false);
    };

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    useEffect(() => {
        scrollToBottom();
    }, [chatMessages]);

    const handleChatSubmit = async (e) => {
        e.preventDefault();
        const text = chatInput.trim();
        if (!text) return;

        setChatMessages((prev) => [...prev, { text, isUser: true }]);
        setChatInput('');
        setChatLoading(true);

        const typingId = Date.now();
        setChatMessages((prev) => [...prev, { typing: true, id: typingId }]);

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const response = await fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    message: text,
                    history: chatHistoryRef.current,
                }),
            });

            setChatMessages((prev) => prev.filter((m) => m.id !== typingId));

            if (response.ok) {
                const data = await response.json();
                if (data.reply) {
                    setChatMessages((prev) => [...prev, { text: data.reply, isUser: false }]);
                    chatHistoryRef.current.push({ role: 'user', content: text });
                    chatHistoryRef.current.push({ role: 'assistant', content: data.reply });
                    if (chatHistoryRef.current.length > 10)
                        chatHistoryRef.current = chatHistoryRef.current.slice(-10);
                } else {
                    setChatMessages((prev) => [...prev, { text: 'Maaf, terjadi kesalahan.', isUser: false }]);
                }
            } else {
                setChatMessages((prev) => [...prev, { text: 'Maaf, gagal terhubung ke server.', isUser: false }]);
            }
        } catch {
            setChatMessages((prev) => prev.filter((m) => m.id !== typingId));
            setChatMessages((prev) => [...prev, { text: 'Terjadi kesalahan jaringan.', isUser: false }]);
        } finally {
            setChatLoading(false);
        }
    };

    const formatMessage = (text) =>
        text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');

    return (
        <PublicLayout title="Jasa Pembuatan Website & Shared Hosting Indonesia">

            <style>{`
                .reveal{opacity:0;transform:translateY(20px);transition:opacity .6s cubic-bezier(.16,1,.3,1),transform .6s cubic-bezier(.16,1,.3,1)}
                .reveal.reveal-visible{opacity:1;transform:translateY(0)}
                @media(prefers-reduced-motion:reduce){.reveal{opacity:1;transform:none;transition:none}}
                .stack-logo{transition:all .3s ease;opacity:.6}
                .stack-logo:hover{opacity:1}
                .bg-dot-pattern{background-image:radial-gradient(rgba(15,23,42,.1) 1px,transparent 1px);background-size:24px 24px}
                .dark .bg-dot-pattern{background-image:radial-gradient(rgba(255,255,255,.05) 1px,transparent 1px)}
                .hero-glow{position:absolute;width:600px;height:600px;background:radial-gradient(circle,rgba(79,70,229,.15) 0%,rgba(0,0,0,0) 70%);top:-200px;left:50%;transform:translateX(-50%);pointer-events:none;z-index:0}
            `}</style>

            {/* HERO SECTION */}
            <section className="relative pt-32 pb-24 lg:pt-48 lg:pb-32 bg-white dark:bg-[#030712] min-h-[90vh] flex items-center overflow-hidden border-b border-slate-200 dark:border-white/5">
                <div className="absolute inset-0 bg-dot-pattern z-0 opacity-40"></div>
                <div className="hero-glow hidden dark:block"></div>
                <div className="absolute top-0 inset-x-0 h-40 bg-gradient-to-b from-white dark:from-[#030712] to-transparent z-0"></div>
                <div className="absolute bottom-0 inset-x-0 h-40 bg-gradient-to-t from-white dark:from-[#030712] to-transparent z-0"></div>

                <div className="max-w-5xl mx-auto px-6 relative z-10 text-center">
                    <div className="inline-flex items-center gap-2.5 px-3 py-1 rounded-full border border-slate-200 dark:border-white/10 bg-white/50 dark:bg-white/5 backdrop-blur-md shadow-sm text-slate-600 dark:text-slate-300 text-xs sm:text-sm font-medium mb-8 transition-colors hover:border-slate-300 dark:hover:border-white/20 mb-10 max-w-4xl mx-auto">
                        <span className="flex h-1.5 w-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                        Sistem Deployment Tersedia
                        <i className="fa-solid fa-arrow-right text-[10px] ml-1 opacity-50"></i>
                    </div>

                    <h1 className="text-5xl md:text-7xl font-bold tracking-tighter text-slate-900 dark:text-transparent dark:bg-clip-text dark:bg-gradient-to-b dark:from-white dark:to-slate-400 leading-tight mb-6">
                        Bangun Produk Digital Anda <br className="hidden md:block" />
                        Lebih Cepat & Kuat.
                    </h1>

                    <p className="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed font-normal">
                        Jasa pembuatan website & aplikasi terpercaya, plus shared hosting Indonesia dengan auto-deploy, SSL gratis, dan database MySQL. Tim development profesional siap mengeksekusi visi teknologi Anda tanpa kompromi.
                    </p>

                    <div className="flex flex-col sm:flex-row justify-center gap-4 mb-16">
                        <a href="#services" className="px-8 py-3 text-sm font-semibold rounded-full text-white bg-slate-900 dark:bg-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-200 shadow-[0_0_20px_rgba(255,255,255,0.1)] dark:shadow-[0_0_20px_rgba(255,255,255,0.2)] transition-all flex items-center justify-center gap-2">
                            Jelajahi Layanan
                        </a>
                    </div>

                    <div className="grid grid-cols-3 gap-6 max-w-3xl mx-auto mb-16">
                        <div className="flex flex-col items-center justify-center">
                            <p className="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">99.9%</p>
                            <p className="text-[10px] font-bold text-slate-500 dark:text-slate-500 mt-2 uppercase tracking-[0.2em]">Uptime Server</p>
                        </div>
                        <div className="flex flex-col items-center justify-center border-x border-slate-200/50 dark:border-white/10">
                            <p className="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">100+</p>
                            <p className="text-[10px] font-bold text-slate-500 dark:text-slate-500 mt-2 uppercase tracking-[0.2em]">Project Selesai</p>
                        </div>
                        <div className="flex flex-col items-center justify-center">
                            <p className="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">&lt;5 mnt</p>
                            <p className="text-[10px] font-bold text-slate-500 dark:text-slate-500 mt-2 uppercase tracking-[0.2em]">Auto-Deploy</p>
                        </div>
                    </div>

                    <div className="pt-10 border-t border-slate-200/50 dark:border-white/5 max-w-4xl mx-auto">
                        <p className="text-[10px] font-bold text-slate-400 dark:text-slate-600 uppercase tracking-[0.2em] mb-8">Didukung oleh Teknologi Modern</p>
                        <div className="flex flex-wrap justify-center gap-10 text-slate-400 dark:text-slate-500">
                            <i className="fa-brands fa-laravel text-3xl md:text-4xl stack-logo"></i>
                            <i className="fa-brands fa-react text-3xl md:text-4xl stack-logo"></i>
                            <i className="fa-brands fa-node-js text-3xl md:text-4xl stack-logo"></i>
                            <i className="fa-brands fa-python text-3xl md:text-4xl stack-logo"></i>
                            <i className="fa-brands fa-vuejs text-3xl md:text-4xl stack-logo"></i>
                            <i className="fa-brands fa-docker text-3xl md:text-4xl stack-logo"></i>
                        </div>
                    </div>
                </div>
            </section>

            {/* ABOUT SECTION */}
            <section id="about" className="py-24 bg-slate-50 dark:bg-[#030712] border-b border-slate-200 dark:border-white/5">
                <div className="max-w-7xl mx-auto px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div className="reveal">
                            <span className="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400 mb-3 block">Tentang Ryaze</span>
                            <h2 className="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-white mb-6">Platform Hosting & Development modern untuk bisnis Anda.</h2>

                            <p className="text-base text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                                Ryaze adalah platform shared hosting Indonesia yang dirancang untuk developer dan bisnis yang
                                menginginkan deployment cepat, infrastruktur andal, dan kontrol penuh atas aplikasi mereka.
                            </p>
                            <p className="text-base text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
                                Kami menyediakan lingkungan hosting otomatis dengan auto-deploy dari Git, SSL gratis,
                                database lengkap, serta panel kontrol berfitur tinggi — semuanya dengan harga terjangkau.
                            </p>

                            <div className="flex flex-wrap gap-2">
                                {['AUTO DEPLOY', 'SSL GRATIS', 'FULLSTACK WEB', 'SHARED SERVER'].map((tag) => (
                                    <span key={tag} className="px-4 py-1.5 bg-indigo-50 dark:bg-white/5 backdrop-blur-sm border border-indigo-200 dark:border-white/10 rounded-full text-[11px] font-bold tracking-[0.1em] text-indigo-600 dark:text-slate-300">{tag}</span>
                                ))}
                            </div>
                        </div>

                        <div className="flex justify-center lg:justify-end reveal">
                            <div className="w-full max-w-sm">
                                <div className="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden bg-white dark:bg-white/5 dark:backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] relative group">
                                    <div className="absolute -inset-0.5 bg-gradient-to-br from-white/20 to-transparent opacity-0 dark:opacity-100 pointer-events-none rounded-2xl"></div>

                                    <div className="p-8 space-y-6">
                                        <div className="text-center">
                                            <div className="w-16 h-16 bg-indigo-100 dark:bg-indigo-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                                <i className="fa-solid fa-rocket text-2xl text-indigo-600 dark:text-indigo-400"></i>
                                            </div>
                                            <h3 className="font-bold text-slate-900 dark:text-white text-xl tracking-tight">Ryaze Hosting</h3>
                                            <p className="text-slate-500 dark:text-slate-400 font-medium text-sm mt-1">Platform Deployment Modern</p>
                                        </div>
                                        <div className="grid grid-cols-2 gap-4">
                                            <div className="text-center p-3 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                                                <p className="text-2xl font-bold text-slate-900 dark:text-white">99.9%</p>
                                                <p className="text-[10px] font-bold text-slate-500 uppercase tracking-wider mt-1">Uptime</p>
                                            </div>
                                            <div className="text-center p-3 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                                                <p className="text-2xl font-bold text-slate-900 dark:text-white">&lt;5mnt</p>
                                                <p className="text-[10px] font-bold text-slate-500 uppercase tracking-wider mt-1">Deploy</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="px-6 py-4 border-t border-slate-200 dark:border-white/10 flex items-center justify-center text-xs font-medium text-slate-500 dark:text-slate-400 relative z-10 bg-white dark:bg-transparent">
                                        <span className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div> Server Aktif 24/7</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* SERVICES SECTION */}
            <section id="services" className="py-24 bg-white dark:bg-[#030712] border-b border-slate-200 dark:border-white/5 relative overflow-hidden">
                <div className="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-indigo-50/50 dark:from-white/[0.02] to-transparent pointer-events-none"></div>
                <div className="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <div className="mb-16 max-w-2xl reveal">
                        <span className="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400 mb-3 block">Layanan</span>
                        <h2 className="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-white mb-4">Infrastruktur & Layanan.</h2>
                        <p className="text-slate-500 dark:text-slate-400 text-lg leading-relaxed">Kami merancang arsitektur web dan infrastruktur shared hosting yang andal untuk melayani project Anda kapan saja.</p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8 reveal">
                        {/* Web Dev Box */}
                        <div className="rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 dark:backdrop-blur-xl p-8 lg:p-10 flex flex-col h-full group shadow-sm transition-all hover:border-slate-300 dark:hover:border-white/20 hover:-translate-y-1 relative">
                            <div className="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl pointer-events-none"></div>
                            <div className="relative z-10">
                                <div className="w-12 h-12 rounded-lg bg-white dark:bg-white/10 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white flex items-center justify-center mb-8 shadow-sm">
                                    <i className="fa-solid fa-laptop-code text-xl"></i>
                                </div>
                                <h3 className="text-2xl font-bold text-slate-900 dark:text-white tracking-tight mb-4">Jasa Pembuatan Sistem</h3>
                                <p className="text-slate-500 dark:text-slate-400 text-base leading-relaxed mb-8">
                                    Pengerjaan sistem informasi, aplikasi SaaS, website korporat, hingga prototipe fungsional. Berbasis arsitektur modern yang bersih, efisien, dan terdokumentasi.
                                </p>
                                <ul className="space-y-4 mb-10 text-sm font-medium text-slate-600 dark:text-slate-300 flex-1">
                                    <li className="flex items-center gap-3">
                                        <div className="flex-shrink-0 text-slate-400 dark:text-slate-500"><i className="fa-solid fa-check"></i></div>
                                        Backend & API Design
                                    </li>
                                    <li className="flex items-center gap-3">
                                        <div className="flex-shrink-0 text-slate-400 dark:text-slate-500"><i className="fa-solid fa-check"></i></div>
                                        Frontend Modern (React/Vue)
                                    </li>
                                    <li className="flex items-center gap-3">
                                        <div className="flex-shrink-0 text-slate-400 dark:text-slate-500"><i className="fa-solid fa-check"></i></div>
                                        Keamanan & Skalabilitas Tinggi
                                    </li>
                                </ul>
                                <Link href="/consultation" className="inline-flex items-center justify-center w-full sm:w-auto px-6 py-2.5 text-sm font-semibold text-slate-900 dark:text-white bg-white dark:bg-white/10 border border-slate-300 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/20 rounded-full transition-colors shadow-sm">
                                    Mulai Konsultasi <i className="fa-solid fa-robot ml-2"></i>
                                </Link>
                            </div>
                        </div>

                        {/* Hosting Box */}
                        <div className="rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 dark:backdrop-blur-xl p-8 lg:p-10 flex flex-col h-full group shadow-sm transition-all hover:border-slate-300 dark:hover:border-white/20 hover:-translate-y-1 relative overflow-hidden">
                            <div className="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl pointer-events-none"></div>
                            <div className="absolute top-0 right-0 p-8 opacity-5 pointer-events-none transition-transform duration-700 group-hover:scale-110">
                                <i className="fa-solid fa-server text-9xl text-slate-900 dark:text-white"></i>
                            </div>
                            <div className="relative z-10 flex-1 flex flex-col">
                                <div className="w-12 h-12 rounded-lg bg-white dark:bg-white/10 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white flex items-center justify-center mb-8 shadow-sm">
                                    <i className="fa-solid fa-server text-xl"></i>
                                </div>

                                <div className="flex flex-col sm:flex-row sm:items-end justify-between mb-4 gap-4">
                                    <h3 className="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Shared App Hosting</h3>
                                    <div className="flex flex-col sm:items-end">
                                        {starterPricing.promo > 0 ? (
                                            <>
                                                <span className="text-[11px] font-medium text-slate-400 dark:text-slate-500 line-through mb-1">Rp {formatRupiah(starterPricing.normal)}</span>
                                                <div className="flex items-baseline gap-1">
                                                    <span className="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Rp {formatRupiah(starterPricing.promo)}</span>
                                                    <span className="text-xs text-slate-500">/bln</span>
                                                </div>
                                            </>
                                        ) : (
                                            <div className="flex items-baseline gap-1">
                                                <span className="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Rp {formatRupiah(starterPricing.normal)}</span>
                                                <span className="text-xs text-slate-500">/bln</span>
                                            </div>
                                        )}
                                    </div>
                                </div>

                                <p className="text-slate-500 dark:text-slate-400 text-base leading-relaxed mb-8">
                                    Hosting murah dengan deployment otomatis tanpa pusing. Eksekusi repositori kode langsung ke server publik dengan dukungan Web-Terminal, proses manager, dan database bawaan.
                                </p>

                                <ul className="space-y-4 mb-10 text-sm font-medium text-slate-600 dark:text-slate-300 flex-1">
                                    <li className="flex items-center gap-3">
                                        <div className="flex-shrink-0 text-slate-400 dark:text-slate-500"><i className="fa-solid fa-check"></i></div>
                                        Auto Deploy (Node, PHP, Python, dsb)
                                    </li>
                                    <li className="flex items-center gap-3">
                                        <div className="flex-shrink-0 text-slate-400 dark:text-slate-500"><i className="fa-solid fa-check"></i></div>
                                        Database (MySQL) & SSL Gratis
                                    </li>
                                    <li className="flex items-center gap-3">
                                        <div className="flex-shrink-0 text-slate-400 dark:text-slate-500"><i className="fa-solid fa-check"></i></div>
                                        File Manager & Web Terminal Lengkap
                                    </li>
                                </ul>

                                <Link href="/register" className="inline-flex items-center justify-center w-full sm:w-auto px-6 py-2.5 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200 rounded-full transition-colors shadow-sm">
                                    Deploy Sekarang <i className="fa-solid fa-arrow-right ml-2 text-[10px]"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* PRICING SECTION */}
            <section id="pricing" className="py-24 bg-slate-50 dark:bg-[#030712] border-b border-slate-200 dark:border-white/5 relative overflow-hidden">
                <div className="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <div className="mb-16 text-center reveal">
                        <span className="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400 mb-3 block">Harga Transparan</span>
                        <h2 className="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-white mb-4">Pilih Paket Hosting</h2>
                        <p className="text-slate-500 dark:text-slate-400 text-lg max-w-2xl mx-auto">Deploy project Anda sekarang. Mulai dari harga terjangkau dengan fitur lengkap, siap scale sesuai kebutuhan.</p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 reveal">
                        {Object.entries(plans).map(([slug, plan]) => {
                            if (!plan.is_active) return null;
                            const pricing = planPricing[slug];
                            const hc = colorMap[plan.color] || colorMap.slate;
                            const isPopular = slug === 'pro';

                            return (
                                <div key={slug} className={`relative flex flex-col ${hc.bg} border ${isPopular ? 'border-slate-900 dark:border-indigo-500 shadow-md z-10 scale-100 lg:scale-[1.02]' : 'border-slate-200 dark:border-white/10 shadow-sm'} rounded-2xl overflow-hidden transition-all hover:border-slate-300 dark:hover:border-white/20`}>
                                    {isPopular && (
                                        <div className="absolute top-0 inset-x-0 bg-slate-900 dark:bg-indigo-500 text-white text-[10px] font-bold text-center py-1 uppercase tracking-wider">
                                            Paling Populer
                                        </div>
                                    )}

                                    <div className="p-8 flex-1 mt-4">
                                        <h3 className={`text-lg font-semibold ${isPopular ? 'text-white dark:text-white' : hc.text} mb-2`}>{plan.label}</h3>

                                        <div className="mb-6">
                                            {pricing.promo !== null ? (
                                                <span className={`text-xs font-medium ${isPopular ? 'text-violet-200 dark:text-indigo-200' : 'text-slate-400 dark:text-slate-500'} line-through block mb-1`}>Rp {formatRupiah(pricing.normal)}</span>
                                            ) : (
                                                <div className="h-4 mb-1"></div>
                                            )}
                                            <div className="flex items-baseline gap-1">
                                                <span className={`text-3xl font-bold tracking-tight ${isPopular ? 'text-white dark:text-white' : hc.text}`}>Rp {formatRupiah(pricing.active)}</span>
                                                <span className={`${isPopular ? 'text-violet-200 dark:text-indigo-200' : 'text-slate-500 dark:text-slate-400'} text-sm`}>/bln</span>
                                            </div>
                                        </div>

                                        <ul className="space-y-3">
                                            {plan.features.map((feat, i) => (
                                                <li key={i} className={`flex items-start gap-3 text-sm ${isPopular ? 'text-violet-100 dark:text-indigo-100' : 'text-slate-600 dark:text-slate-400'}`}>
                                                    <div className="mt-1 flex-shrink-0">
                                                        <i className={`fa-solid fa-check ${isPopular ? 'text-violet-300 dark:text-indigo-300' : hc.check} text-[10px]`}></i>
                                                    </div>
                                                    <span>{feat}</span>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>

                                    <div className="p-8 pt-0">
                                        <Link href="/register" className={`flex items-center justify-center w-full ${isPopular ? 'bg-white text-indigo-600 hover:bg-violet-50 dark:bg-white dark:text-indigo-600 dark:hover:bg-violet-50' : hc.btn} font-semibold py-2.5 rounded-full transition-colors text-sm`}>
                                            Pilih Paket
                                        </Link>
                                    </div>
                                </div>
                            );
                        })}
                    </div>

                    {/* Features Bottom Line */}
                    <div className="mt-16 pt-8 border-t border-slate-200/60 dark:border-white/10 grid grid-cols-2 md:grid-cols-4 gap-6 reveal">
                        <div className="flex items-center justify-center gap-3">
                            <div className="w-10 h-10 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-700 dark:text-slate-300 border border-transparent dark:border-white/10">
                                <i className="fa-solid fa-shield-halved"></i>
                            </div>
                            <span className="text-sm font-medium text-slate-700 dark:text-slate-300">SSL Gratis</span>
                        </div>
                        <div className="flex items-center justify-center gap-3">
                            <div className="w-10 h-10 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-700 dark:text-slate-300 border border-transparent dark:border-white/10">
                                <i className="fa-solid fa-database"></i>
                            </div>
                            <span className="text-sm font-medium text-slate-700 dark:text-slate-300">Database MySQL</span>
                        </div>
                        <div className="flex items-center justify-center gap-3">
                            <div className="w-10 h-10 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-700 dark:text-slate-300 border border-transparent dark:border-white/10">
                                <i className="fa-solid fa-rotate"></i>
                            </div>
                            <span className="text-sm font-medium text-slate-700 dark:text-slate-300">Auto-Deploy Git</span>
                        </div>
                        <div className="flex items-center justify-center gap-3">
                            <div className="w-10 h-10 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-700 dark:text-slate-300 border border-transparent dark:border-white/10">
                                <i className="fa-solid fa-headset"></i>
                            </div>
                            <span className="text-sm font-medium text-slate-700 dark:text-slate-300">Support 1-on-1</span>
                        </div>
                    </div>
                </div>
            </section>

            {/* BLOG SECTION */}
            <section className="py-24 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-700" id="blog">
                <div className="max-w-7xl mx-auto px-6 lg:px-8">
                    <div className="flex flex-col md:flex-row justify-between items-end gap-6 mb-12 reveal">
                        <div>
                            <h2 className="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50 mb-2">Artikel Terbaru</h2>
                            <p className="text-slate-500 dark:text-slate-400 text-sm max-w-2xl">Tulisan seputar web development, tips hosting, dan wawasan teknologi lainnya dari tim Ryaze.</p>
                        </div>
                        <Link href="/blog" className="hidden md:inline-flex text-sm font-semibold text-indigo-600 dark:text-indigo-400 items-center gap-2 hover:underline">
                            Lihat Semua Artikel <i className="fa-solid fa-arrow-right text-xs"></i>
                        </Link>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 reveal">
                        {articles.length > 0 ? (
                            articles.map((article) => (
                                <Link key={article.id} href={`/blog/${article.slug}`} className="group bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-white/10 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 rounded-2xl overflow-hidden flex flex-col">
                                    {article.cover_image ? (
                                        <div className="h-48 overflow-hidden bg-slate-100 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
                                            <img src={article.cover_image} alt={article.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                        </div>
                                    ) : (
                                        <div className="h-48 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-300 dark:text-slate-400">
                                            <i className="fa-solid fa-newspaper text-5xl"></i>
                                        </div>
                                    )}
                                    <div className="p-6 flex flex-col flex-1">
                                        {article.category && (
                                            <span className="text-[10px] font-bold uppercase text-indigo-600 dark:text-indigo-400 mb-2">{article.category.name}</span>
                                        )}
                                        <h3 className="text-lg font-bold text-slate-900 dark:text-slate-50 mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2">
                                            {article.title}
                                        </h3>
                                        <p className="text-slate-500 dark:text-slate-400 text-sm line-clamp-2 mb-4 flex-1">
                                            {article.excerpt}
                                        </p>
                                        <div className="flex items-center gap-3 text-xs text-slate-400 dark:text-slate-500 mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
                                            <span>{article.published_at}</span>
                                            <span>&middot;</span>
                                            <span>{article.reading_time} min</span>
                                        </div>
                                    </div>
                                </Link>
                            ))
                        ) : (
                            <div className="col-span-full py-12 border border-dashed border-slate-300 dark:border-slate-600 rounded-lg text-center bg-white dark:bg-slate-800/60">
                                <p className="text-sm text-slate-500 dark:text-slate-400 font-medium">Belum ada artikel.</p>
                            </div>
                        )}
                    </div>

                    <div className="mt-8 md:hidden text-center">
                        <Link href="/blog" className="text-sm font-semibold text-indigo-600 dark:text-indigo-400 inline-flex items-center gap-2 hover:underline">
                            Lihat Semua Artikel <i className="fa-solid fa-arrow-right text-xs"></i>
                        </Link>
                    </div>
                </div>
            </section>

            {/* FAQ SECTION */}
            <section id="faq" className="py-24 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
                <div className="max-w-3xl mx-auto px-6 lg:px-8">
                    <div className="mb-14 text-center reveal">
                        <span className="text-xs font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-3 block">Pertanyaan Umum</span>
                        <h2 className="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mb-4">Yang Sering Ditanyakan</h2>
                        <p className="text-slate-500 dark:text-slate-400 text-base">Semua yang perlu Anda ketahui sebelum deploy atau memesan jasa pembuatan website.</p>
                    </div>

                    <div className="space-y-4 reveal">
                        {faqItems.map((item, i) => (
                            <details key={i} className={`bg-white dark:bg-white/5 dark:backdrop-blur-xl border border-slate-200/60 dark:border-white/10 shadow-sm transition-all duration-300 rounded-2xl p-6 lg:p-8 group ${i === 0 ? 'bg-white dark:bg-slate-800/60' : 'hover:border-slate-300 dark:hover:border-white/20'}`} open={i === 0}>
                                <summary className="cursor-pointer font-bold text-slate-900 dark:text-slate-50 flex items-center justify-between gap-4">
                                    {item.q}
                                    <i className="fa-solid fa-chevron-down text-xs text-slate-400 dark:text-slate-500 group-open:rotate-180 transition-transform"></i>
                                </summary>
                                <p className="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mt-4">
                                    {item.a}
                                </p>
                            </details>
                        ))}
                    </div>
                </div>
            </section>

            {/* CALL TO ACTION */}
            <section className="py-24 bg-slate-50 dark:bg-[#030712] text-center px-6 relative overflow-hidden border-t border-slate-200 dark:border-white/5">
                <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-indigo-900/10 via-[#030712] to-[#030712] pointer-events-none hidden dark:block"></div>
                <div className="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-white/[0.02] to-transparent pointer-events-none"></div>

                <div className="max-w-3xl mx-auto relative z-10 reveal">
                    <h2 className="text-3xl md:text-5xl font-bold tracking-tight mb-6 text-slate-900 dark:text-transparent dark:bg-clip-text dark:bg-gradient-to-b dark:from-white dark:to-slate-400">Siap Mengeksekusi Ide?</h2>
                    <p className="text-slate-500 dark:text-slate-400 text-lg mb-10 max-w-xl mx-auto">Daftar sekarang untuk mengakses lingkungan deployment yang kuat atau hubungi kami untuk pengerjaan perangkat lunak Anda.</p>
                    <Link href="/register" className="inline-flex px-8 py-3 bg-slate-900 text-white dark:bg-white dark:text-slate-900 text-sm font-semibold rounded-full hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors shadow-sm dark:shadow-[0_0_20px_rgba(255,255,255,0.15)]">
                        Mulai Secara Gratis
                    </Link>
                </div>
            </section>

            {/* Chatbot Widget */}
            <div className="fixed bottom-6 right-6 z-50 font-sans">
                {chatOpen && (
                    <div className="flex flex-col bg-white dark:bg-[#0B0F19] border border-slate-200 dark:border-white/10 shadow-2xl rounded-2xl w-80 h-96 mb-4 overflow-hidden transition-all duration-300 transform origin-bottom-right">
                        <div className="bg-slate-900 dark:bg-indigo-600 px-4 py-3 text-white flex justify-between items-center shadow-sm">
                            <div className="flex items-center gap-2">
                                <div className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                                <span className="font-bold text-sm">Ryaze Assistant</span>
                            </div>
                            <button onClick={toggleChat} className="text-indigo-100 hover:text-white transition-colors">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div className="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50 dark:bg-[#030712] text-sm flex flex-col">
                            <div className="flex items-start gap-2">
                                <div className="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0 mt-0.5">
                                    <i className="fa-solid fa-robot text-[10px] text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                <div className="bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 px-3 py-2 rounded-2xl rounded-tl-sm text-slate-700 dark:text-slate-200 shadow-sm max-w-[85%]">
                                    Halo! Saya asisten AI Ryaze. Ada yang bisa saya bantu hari ini?
                                </div>
                            </div>
                            {chatMessages.map((msg, i) => {
                                if (msg.typing) {
                                    return (
                                        <div key={msg.id || i} className="flex items-start gap-2">
                                            <div className="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center shrink-0 mt-0.5">
                                                <i className="fa-solid fa-robot text-[10px] text-indigo-600 dark:text-indigo-400"></i>
                                            </div>
                                            <div className="bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm flex gap-1 items-center">
                                                <div className="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></div>
                                                <div className="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style={{ animationDelay: '0.1s' }}></div>
                                                <div className="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style={{ animationDelay: '0.2s' }}></div>
                                            </div>
                                        </div>
                                    );
                                }
                                return (
                                    <div key={i} className={`flex items-start gap-2 ${msg.isUser ? 'flex-row-reverse' : ''}`}>
                                        <div className={`w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5 ${msg.isUser ? 'bg-slate-200 dark:bg-white/10' : 'bg-indigo-100 dark:bg-indigo-500/20'}`}>
                                            <i className={`fa-solid ${msg.isUser ? 'fa-user text-[10px] text-slate-500 dark:text-slate-400' : 'fa-robot text-[10px] text-indigo-600 dark:text-indigo-400'}`}></i>
                                        </div>
                                        <div className={`px-3 py-2 rounded-2xl shadow-sm max-w-[85%] break-words ${msg.isUser ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-tr-sm' : 'bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-200 rounded-tl-sm'}`} dangerouslySetInnerHTML={{ __html: formatMessage(msg.text) }}></div>
                                    </div>
                                );
                            })}
                            <div ref={messagesEndRef} />
                        </div>
                        <div className="p-3 bg-white dark:bg-[#0B0F19] border-t border-slate-100 dark:border-white/10">
                            <form onSubmit={handleChatSubmit} className="flex items-center gap-2">
                                <input type="text" value={chatInput} onChange={(e) => setChatInput(e.target.value)} placeholder="Ketik pesan..." required className="flex-1 bg-slate-50 dark:bg-[#030712] border border-slate-200 dark:border-white/10 text-sm rounded-full px-4 py-2 focus:outline-none focus:border-slate-400 dark:focus:border-white/20 transition-all text-slate-900 dark:text-white" />
                                <button type="submit" disabled={chatLoading} className="w-9 h-9 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors shrink-0 shadow-sm disabled:opacity-50">
                                    <i className="fa-solid fa-paper-plane text-[10px] -ml-0.5"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                )}

                <button onClick={toggleChat} className="w-14 h-14 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full flex items-center justify-center shadow-lg shadow-indigo-200 transition-all hover:scale-105 ml-auto relative">
                    <i className="fa-solid fa-message text-xl"></i>
                    {notifDot && <span className="absolute top-0 right-0 w-3.5 h-3.5 bg-rose-500 border-2 border-white dark:border-slate-900 rounded-full"></span>}
                </button>
            </div>

        </PublicLayout>
    );
}
