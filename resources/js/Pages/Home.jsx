import PublicLayout from '../Layouts/PublicLayout';
import { Head, usePage } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import { useReveal, RevealStyle, SectionHead } from '../Components/PublicSection';

function rupiah(n) {
    return new Intl.NumberFormat('id-ID').format(n);
}

function formatDate(val) {
    if (!val) return '';
    const d = new Date(val);
    if (isNaN(d.getTime())) return val;
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

const faqData = [
    { q: 'Apa itu Ryaze?', a: 'Platform hosting & development. Auto-deploy dari Git, SSL gratis, database MySQL, web terminal, panel kontrol lengkap.' },
    { q: 'Teknologi apa yang didukung?', a: 'Node.js, PHP/Laravel, React, Vue.js, Next.js, dan HTML statis. Deploy otomatis dari repositori Git.' },
    { q: 'SSL tersedia?', a: "Ya. Setiap project otomatis dapat sertifikat SSL gratis via Let's Encrypt." },
    { q: 'Database tersedia?', a: 'Ya. MySQL bawaan, dikelola lewat panel, mini phpMyAdmin, dan API key.' },
    { q: 'Bisa pesan website/aplikasi?', a: 'Bisa. Sistem informasi, SaaS, website korporat, prototipe fungsional — arsitektur modern, bersih, terdokumentasi.' },
    { q: 'Cara mulai?', a: 'Daftar gratis, pilih paket, deploy dari Git dalam hitungan menit.' },
];

const specRows = [
    { k: 'DEPLOY', v: 'Auto-deploy dari Git push' },
    { k: 'SSL', v: "Let's Encrypt, otomatis" },
    { k: 'DATABASE', v: 'MySQL + mini phpMyAdmin' },
    { k: 'TERMINAL', v: 'Web terminal di panel' },
    { k: 'SUPPORT', v: '24/7' },
];

const heroStats = [
    { n: '99.9%', l: 'Uptime Server' },
    { n: '100+', l: 'Project Selesai' },
    { n: '< 5 mnt', l: 'Auto-Deploy' },
];

function FaqItem({ index, q, a }) {
    const [open, setOpen] = useState(false);
    return (
        <div className="border-b border-[#E3EAF1] dark:border-[#1B2C44]">
            <button onClick={() => setOpen(!open)} className="w-full flex items-start gap-5 py-6 text-left group">
                <span className="font-mono text-[11px] text-[#94A3B4] dark:text-white/30 pt-1 shrink-0">{String(index + 1).padStart(2, '0')}</span>
                <span className="flex-1 text-[15px] font-semibold text-[#0F1A2A] dark:text-white group-hover:text-[#1D5B96] dark:group-hover:text-[#6BA8DC] transition-colors">{q}</span>
                <span className={`text-[#1D5B96] dark:text-[#6BA8DC] text-lg leading-none pt-0.5 transition-transform duration-200 ${open ? 'rotate-45' : ''}`}>+</span>
            </button>
            <div className={`overflow-hidden transition-all duration-300 ${open ? 'max-h-48' : 'max-h-0'}`}>
                <p className="pb-6 pl-[3.25rem] pr-8 text-sm text-[#525252] dark:text-white/55 leading-relaxed">{a}</p>
            </div>
        </div>
    );
}

export default function Home({ plans, planPricing, planPricingYearly, articles, starterPricing, starterPricingYearly, promos = [], version, seoTitle, seoDescription }) {
    const { csrf_token, auth } = usePage().props;
    useReveal();
    const [cycle, setCycle] = useState('monthly');
    const [slide, setSlide] = useState(0);
    const [chatOpen, setChatOpen] = useState(false);
    const [chatMsgs, setChatMsgs] = useState([]);
    const [chatInput, setChatInput] = useState('');
    const [chatLoading, setChatLoading] = useState(false);
    const [chatHistory, setChatHistory] = useState([]);
    const chatEndRef = useRef(null);

    useEffect(() => {
        if (chatOpen) {
            chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
        }
    }, [chatMsgs, chatOpen]);

    const sendChat = async (e) => {
        e.preventDefault();
        const text = chatInput.trim();
        if (!text || chatLoading) return;
        setChatInput('');
        setChatMsgs(prev => [...prev, { id: Date.now(), text, user: true }]);
        setChatLoading(true);
        try {
            const r = await fetch('/chat', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf_token }, body: JSON.stringify({ message: text, history: chatHistory }) });
            if (r.ok) { const d = await r.json(); if (d.reply) { setChatMsgs(p => [...p, { id: Date.now() + 1, text: d.reply, user: false }]); setChatHistory(p => [...p.slice(-8), { role: 'user', content: text }, { role: 'assistant', content: d.reply }]); } }
        } catch {}
        setChatLoading(false);
    };

    const activePlans = Object.entries(plans).filter(([, p]) => p.is_active);

    const isYearly = cycle === 'yearly';

    const priceOf = (slug) => {
        const monthly = (planPricing && planPricing[slug]) || { normal: 0, promo: null, active: 0 };
        if (!isYearly) return { ...monthly, unit: '/bln' };
        const yearly = (planPricingYearly && planPricingYearly[slug]) || { normal: 0, promo: null, active: 0 };
        if (!monthly.active) return { ...monthly, unit: '/bln' };
        if (!yearly.active) {
            return { normal: monthly.normal * 12, promo: null, active: monthly.active * 12, unit: '/thn' };
        }
        return { ...yearly, unit: '/thn' };
    };

    const activePromos = Array.isArray(promos) ? promos : [];
    const [paused, setPaused] = useState(false);

    const sp = isYearly
        ? (starterPricingYearly && starterPricingYearly.active
            ? starterPricingYearly
            : starterPricing
                ? { ...starterPricing, normal: starterPricing.normal * 12, promo: null, active: starterPricing.active * 12 }
                : { normal: 0, promo: null, active: 0 })
        : (starterPricing || { normal: 0, promo: null, active: 0 });

    useEffect(() => {
        if (activePromos.length < 2 || paused) return;
        const t = setInterval(() => setSlide(s => (s === activePromos.length - 1 ? 0 : s + 1)), 5000);
        return () => clearInterval(t);
    }, [activePromos.length, paused]);

    const goSlide = (i) => {
        setSlide(activePromos.length ? (i + activePromos.length) % activePromos.length : 0);
    };

    const userRole = auth?.user?.role;
    const canSubscribe = ['user_hosting', 'admin_hosting', 'superadmin'].includes(userRole);
    const planCtaHref = canSubscribe ? `/user/hosting/subscription?cycle=${cycle}` : '/register';

    const pageUrl = 'https://ryaze.my.id';

    const faqJsonLd = {
        '@context': 'https://schema.org',
        '@type': 'FAQPage',
        mainEntity: faqData.map((f) => ({
            '@type': 'Question',
            name: f.q,
            acceptedAnswer: {
                '@type': 'Answer',
                text: f.a
            }
        }))
    };

    const webPageJsonLd = {
        '@context': 'https://schema.org',
        '@type': 'WebPage',
        name: 'Jasa Pembuatan Website & Shared Hosting Indonesia',
        description: 'Platform hosting & development modern. Jasa pembuatan website & aplikasi. Shared hosting Indonesia dengan auto-deploy dari Git, SSL gratis, database MySQL, web terminal, panel kontrol lengkap.',
        url: pageUrl,
        inLanguage: 'id-ID',
        publisher: {
            '@type': 'Organization',
            name: 'Ryaze',
            logo: {
                '@type': 'ImageObject',
                url: `${pageUrl}/og-image.png`,
                width: 1200,
                height: 630
            }
        }
    };

    const breadcrumbJsonLd = {
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: [
            { '@type': 'ListItem', position: 1, name: 'Beranda', item: 'https://ryaze.my.id/' }
        ]
    };

    return (
        <>
            <Head title={seoTitle}>
                <meta name="description" content={seoDescription} />
                <meta property="og:type" content="website" />
                <meta property="og:url" content="https://ryaze.my.id/" />
                <meta property="og:title" content={seoTitle} />
                <meta property="og:description" content={seoDescription} />
                <meta property="og:image" content="https://ryaze.my.id/og-image.png" />
                <meta property="og:site_name" content="Ryaze" />
                <meta property="og:locale" content="id_ID" />
                <meta property="twitter:card" content="summary_large_image" />
                <meta property="twitter:url" content="https://ryaze.my.id/" />
                <meta property="twitter:title" content={seoTitle} />
                <meta property="twitter:description" content={seoDescription} />
                <meta property="twitter:image" content="https://ryaze.my.id/og-image.png" />
                <link rel="canonical" href="https://ryaze.my.id/" />
                <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(faqJsonLd) }} />
                <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(webPageJsonLd) }} />
                <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbJsonLd) }} />
            </Head>
            <PublicLayout>
                <RevealStyle />
                <style>{`
                    .price-strike { position: relative; }
                    .price-strike::after { content: ''; position: absolute; left: -2px; right: -2px; top: 50%; height: 1px; background: currentColor; opacity: 0.4; }
                `}</style>

            {/* HERO */}
            <section className="bg-white dark:bg-[#0A1322]">
                <div className="max-w-6xl mx-auto px-6">
                    <div className="min-h-[calc(100vh-4rem)] flex flex-col justify-center pt-28 pb-0 lg:pt-32">
                        <div className="grid lg:grid-cols-12 gap-12 lg:gap-14 items-center">
                            <div className="lg:col-span-7" data-reveal>
                                <p className="font-mono text-[11px] uppercase tracking-[0.25em] text-[#1D5B96] dark:text-[#6BA8DC] mb-6">
                                    Hosting &amp; Development Platform
                                </p>
                                <h1 className="text-[clamp(2.6rem,6.5vw,4.75rem)] font-black text-[#0F1A2A] dark:text-white leading-[0.98] tracking-[-0.04em] mb-7">
                                    Bangun produk digital.<br />
                                    <span className="text-[#1D5B96] dark:text-[#6BA8DC]">Deploy dalam hitungan menit.</span>
                                </h1>
                                <p className="text-[#525252] dark:text-white/60 text-lg max-w-md leading-relaxed mb-10">
                                    Jasa pembuatan website &amp; aplikasi. Shared hosting Indonesia dengan auto-deploy dari Git, SSL gratis, database MySQL.
                                </p>
                                <div className="flex flex-col sm:flex-row gap-3">
                                    <a href="#pricing" className="group inline-flex items-center justify-center gap-2.5 px-7 py-3.5 bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] text-sm font-semibold hover:bg-[#1D5B96] dark:hover:bg-[#6BA8DC] transition-colors">
                                        Lihat Paket
                                        <i className="fa-solid fa-arrow-right text-[11px] transition-transform duration-300 group-hover:translate-x-1"></i>
                                    </a>
                                    <a href="#about" className="inline-flex items-center justify-center px-7 py-3.5 border border-[#C9D5E2] dark:border-[#24384F] text-[#0F1A2A] dark:text-white text-sm font-semibold hover:border-[#1D5B96] hover:text-[#1D5B96] dark:hover:border-[#6BA8DC] dark:hover:text-[#6BA8DC] transition-colors">
                                        Pelajari Lebih Lanjut
                                    </a>
                                </div>
                            </div>

                            {/* Terminal panel */}
                            <div className="lg:col-span-5" data-reveal>
                                <div className="border border-[#E3EAF1] dark:border-[#1B2C44] bg-[#F0F6FC] dark:bg-[#0D1828]">
                                    <div className="flex items-center justify-between px-4 py-2.5 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                                        <span className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">deploy.log</span>
                                        <span className="flex items-center gap-1.5 font-mono text-[10px] tracking-widest text-emerald-600 dark:text-emerald-400">
                                            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> LIVE
                                        </span>
                                    </div>
                                    <div className="p-5 sm:p-6 font-mono text-[12px] sm:text-[12.5px] leading-[1.9]">
                                        <p><span className="text-[#1D5B96] dark:text-[#6BA8DC]">$</span> <span className="text-[#0F1A2A] dark:text-white">git push ryaze production</span></p>
                                        <p className="text-[#737373] dark:text-white/35">remote: Building application...</p>
                                        <p className="text-[#737373] dark:text-white/35">remote: Installing dependencies</p>
                                        <p className="text-[#737373] dark:text-white/35">remote: Issuing SSL certificate</p>
                                        <p><span className="text-emerald-600 dark:text-emerald-400">&#10003;</span> <span className="text-[#0F1A2A] dark:text-white">Deployed in 47s</span></p>
                                        <p className="pt-2.5 border-t border-[#E3EAF1] dark:border-[#1B2C44] mt-2.5">
                                            <span className="text-[#737373] dark:text-white/35">&#8594;</span>{' '}
                                            <span className="text-[#1D5B96] dark:text-[#6BA8DC]">https://ryaze.my.id</span>
                                            <span className="inline-block w-[7px] h-[14px] bg-[#1D5B96] dark:bg-[#6BA8DC] align-middle ml-1 animate-pulse"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Stats strip */}
                        <div className="mt-16 lg:mt-24 grid grid-cols-3 divide-x divide-[#E3EAF1] dark:divide-[#1B2C44] border-t border-[#E3EAF1] dark:border-[#1B2C44]" data-reveal>
                            {heroStats.map((s, i) => (
                                <div key={i} className={`py-7 pr-4 ${i > 0 ? 'pl-4 sm:pl-8' : ''}`}>
                                    <p className="text-2xl sm:text-3xl lg:text-4xl font-black text-[#0F1A2A] dark:text-white tracking-tight">{s.n}</p>
                                    <p className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/40 mt-2">{s.l}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* PROMO CAROUSEL */}
            {activePromos.length > 0 && (
                <section className="bg-white dark:bg-[#0A1322] border-y border-[#E3EAF1] dark:border-[#1B2C44]">
                    <div className="max-w-6xl mx-auto px-6 py-14 lg:py-16">
                        <div
                            className="relative group border border-[#E3EAF1] dark:border-[#1B2C44]"
                            data-reveal
                            onMouseEnter={() => setPaused(true)}
                            onMouseLeave={() => setPaused(false)}
                        >
                            <div
                                className="flex transition-transform duration-500 ease-in-out"
                                style={{ transform: `translateX(-${slide * 100}%)` }}
                            >
                                {activePromos.map((p, i) => (
                                    <div key={p.id ?? i} className="w-full shrink-0">
                                        {p.banner_url ? (
                                            <a href={p.target_url || '#pricing'} className="block bg-white dark:bg-[#0D1828]">
                                                <img src={p.banner_url} alt={p.title} className="w-full h-auto max-h-[420px] object-cover" />
                                            </a>
                                        ) : (
                                            <a href={p.target_url || '#pricing'} className="block">
                                                <div className="relative overflow-hidden bg-[#F0F6FC] dark:bg-[#0D1828] px-6 py-14 sm:py-16 text-center">
                                                    <div className="absolute inset-0 grid-texture hidden dark:block"></div>
                                                    <div className="relative">
                                                        <span className="inline-flex items-center gap-2 font-mono text-[10px] font-semibold text-[#1D5B96] dark:text-[#7FB4E2] tracking-[0.25em] uppercase mb-5">
                                                            <span className="w-6 h-px bg-[#1D5B96] dark:bg-[#6BA8DC]"></span> Promo <span className="w-6 h-px bg-[#1D5B96] dark:bg-[#6BA8DC]"></span>
                                                        </span>
                                                        <h3 className="text-xl sm:text-3xl font-black text-[#0F1A2A] dark:text-white tracking-tight max-w-3xl mx-auto">{p.title}</h3>
                                                        {p.description && (
                                                            <p className="text-[#525252] dark:text-white/50 mt-3 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">{p.description}</p>
                                                        )}
                                                        {p.target_url && (
                                                            <span className="inline-flex items-center gap-2 mt-6 px-4 py-2.5 bg-[#1D5B96] hover:bg-[#2A6FB0] text-white font-semibold text-sm transition-colors duration-300">
                                                                Klaim sekarang
                                                                <i className="fa-solid fa-arrow-right text-[11px]"></i>
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>
                                            </a>
                                        )}
                                    </div>
                                ))}
                            </div>

                            {activePromos.length > 1 && (
                                <>
                                    <button
                                        onClick={() => goSlide(slide - 1)}
                                        aria-label="Promo sebelumnya"
                                        className="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-black/45 hover:bg-[#1D5B96] text-white flex items-center justify-center opacity-0 group-hover:opacity-100 focus:opacity-100 transition-all duration-300 backdrop-blur-sm border border-white/15"
                                    >
                                        <i className="fa-solid fa-chevron-left text-[11px]"></i>
                                    </button>
                                    <button
                                        onClick={() => goSlide(slide + 1)}
                                        aria-label="Promo berikutnya"
                                        className="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-black/45 hover:bg-[#1D5B96] text-white flex items-center justify-center opacity-0 group-hover:opacity-100 focus:opacity-100 transition-all duration-300 backdrop-blur-sm border border-white/15"
                                    >
                                        <i className="fa-solid fa-chevron-right text-[11px]"></i>
                                    </button>
                                    <div className="absolute bottom-4 left-0 right-0 flex justify-center">
                                        <div className="flex gap-1.5 px-2.5 py-2 bg-[#12233A]/85 dark:bg-black/45 backdrop-blur-sm">
                                            {activePromos.map((p, i) => (
                                                <button
                                                    key={p.id ?? i}
                                                    onClick={() => goSlide(i)}
                                                    aria-label={`Promo ${i + 1}`}
                                                    className={`h-1.5 transition-all duration-300 ${slide === i ? 'bg-white w-6' : 'bg-white/40 w-1.5 hover:bg-white/70'}`}
                                                />
                                            ))}
                                        </div>
                                    </div>
                                </>
                            )}
                        </div>
                    </div>
                </section>
            )}

            {/* ABOUT — spec sheet */}
            <section id="about" className="bg-[#F4F8FC] dark:bg-[#0B1526] border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                <div className="max-w-6xl mx-auto px-6 py-20 lg:py-28">
                    <div className="grid lg:grid-cols-12 gap-12 lg:gap-16 items-start">
                        <div className="lg:col-span-5">
                            <SectionHead
                                num="01"
                                label="Platform"
                                title="Hosting yang tidak ambigu."
                            />
                            <p className="mt-6 text-[#525252] dark:text-white/60 leading-relaxed" data-reveal>
                                Ryaze dirancang untuk developer dan bisnis yang butuh deployment cepat, infrastruktur andal, dan kontrol penuh.
                            </p>
                            <p className="mt-4 text-[#525252] dark:text-white/60 leading-relaxed" data-reveal>
                                Auto-deploy dari Git, SSL gratis, database lengkap, panel kontrol — harga terjangkau.
                            </p>
                        </div>
                        <div className="lg:col-span-7 lg:pt-4" data-reveal>
                            <div className="border border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322]">
                                <div className="flex items-center justify-between px-6 py-3.5 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                                    <span className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Spesifikasi</span>
                                    <span className="flex items-center gap-1.5 font-mono text-[10px] tracking-widest text-emerald-600 dark:text-emerald-400">
                                        <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ONLINE
                                    </span>
                                </div>
                                <dl>
                                    {specRows.map((r, i) => (
                                        <div key={r.k} className={`flex items-baseline justify-between gap-6 px-6 py-4 ${i < specRows.length - 1 ? 'border-b border-[#EDF2F7] dark:border-[#152238]' : ''}`}>
                                            <dt className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/35 shrink-0">{r.k}</dt>
                                            <dd className="text-sm font-medium text-[#0F1A2A] dark:text-white text-right">{r.v}</dd>
                                        </div>
                                    ))}
                                </dl>
                                <div className="grid grid-cols-3 divide-x divide-[#E3EAF1] dark:divide-[#1B2C44] border-t border-[#E3EAF1] dark:border-[#1B2C44]">
                                    {[
                                        { v: '99.9%', l: 'Uptime' },
                                        { v: '< 5mnt', l: 'Deploy' },
                                        { v: '24/7', l: 'Support' },
                                    ].map((s, i) => (
                                        <div key={i} className="px-5 py-5 text-center">
                                            <p className="text-lg font-black text-[#0F1A2A] dark:text-white">{s.v}</p>
                                            <p className="font-mono text-[9px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/35 mt-1">{s.l}</p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* SERVICES */}
            <section id="services" className="bg-white dark:bg-[#0A1322] border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                <div className="max-w-6xl mx-auto px-6 py-20 lg:py-28">
                    <div className="mb-14">
                        <SectionHead
                            num="02"
                            label="Layanan"
                            title="Infrastruktur & Solusi Digital."
                            sub="Solusi komprehensif mulai dari pengembangan aplikasi modern hingga hosting server berkinerja tinggi."
                        />
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-px bg-[#E3EAF1] dark:bg-[#1B2C44] border border-[#E3EAF1] dark:border-[#1B2C44]" data-reveal>
                        {/* Service 1: Jasa Pembuatan Sistem */}
                        <div className="bg-white dark:bg-[#0A1322] p-8 lg:p-10 flex flex-col justify-between">
                            <div>
                                <div className="flex items-center justify-between mb-8">
                                    <span className="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC]">
                                        Custom Development
                                    </span>
                                    <div className="w-8 h-8 bg-[#F0F6FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] flex items-center justify-center text-[#1D5B96] dark:text-[#6BA8DC]">
                                        <i className="fa-solid fa-code text-xs"></i>
                                    </div>
                                </div>
                                <h3 className="text-2xl font-black text-[#0F1A2A] dark:text-white tracking-tight mb-3">
                                    Jasa Pembuatan Sistem &amp; Web
                                </h3>
                                <p className="text-[#525252] dark:text-white/60 text-[15px] leading-relaxed mb-8">
                                    Sistem informasi terintegrasi, aplikasi SaaS, website korporat, dan prototipe bisnis fungsional. Arsitektur modular, performa cepat, dan terdokumentasi lengkap.
                                </p>
                                <ul className="space-y-3 mb-10 border-t border-[#EDF2F7] dark:border-[#152238] pt-6">
                                    {[
                                        'Backend & REST API (Laravel / Node.js)',
                                        'Frontend Modern & Responsif (React / Next.js)',
                                        'Keamanan Data & Standar Skalabilitas',
                                    ].map(f => (
                                        <li key={f} className="flex items-center gap-3 text-sm text-[#525252] dark:text-white/70">
                                            <span className="font-mono text-[10px] text-[#1D5B96] dark:text-[#6BA8DC] font-bold">&#10003;</span>
                                            <span>{f}</span>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                            <a
                                href="/consultation"
                                className="inline-flex items-center justify-center gap-2 px-6 py-3.5 border border-[#C9D5E2] dark:border-[#24384F] text-[#0F1A2A] dark:text-white font-semibold text-sm hover:border-[#1D5B96] hover:text-[#1D5B96] dark:hover:border-[#6BA8DC] dark:hover:text-[#6BA8DC] transition-colors"
                            >
                                Mulai Konsultasi
                                <i className="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                        {/* Service 2: Shared App Hosting */}
                        <div className="bg-[#FAFBFD] dark:bg-[#0D1828] p-8 lg:p-10 flex flex-col justify-between relative overflow-hidden">
                            <div>
                                <div className="flex items-center justify-between mb-8">
                                    <span className="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC]">
                                        Cloud Hosting
                                    </span>
                                    <div className="w-8 h-8 bg-white dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#1B2C44] flex items-center justify-center text-[#1D5B96] dark:text-[#6BA8DC]">
                                        <i className="fa-solid fa-rocket text-xs"></i>
                                    </div>
                                </div>
                                <div className="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 mb-3">
                                    <h3 className="text-2xl font-black text-[#0F1A2A] dark:text-white tracking-tight">
                                        Shared App Hosting
                                    </h3>
                                    {sp && sp.active > 0 && (
                                        <div className="sm:text-right">
                                            {sp.promo !== null && (
                                                <span className="text-xs line-through text-[#94A3B4] dark:text-white/35 block font-mono">
                                                    Rp {rupiah(sp.normal)}
                                                </span>
                                            )}
                                            <div className="flex items-baseline gap-1">
                                                <span className="text-xl sm:text-2xl font-black text-[#0F1A2A] dark:text-white">
                                                    Rp {rupiah(sp.active)}
                                                </span>
                                                <span className="text-xs font-mono text-[#737373] dark:text-white/40">
                                                    {isYearly ? '/thn' : '/bln'}
                                                </span>
                                            </div>
                                        </div>
                                    )}
                                </div>
                                <p className="text-[#525252] dark:text-white/60 text-[15px] leading-relaxed mb-8">
                                    Deployment otomatis langsung dari push repositori Git ke server produksi. Dilengkapi web terminal, database manager, dan sertifikat SSL otomatis.
                                </p>
                                <ul className="space-y-3 mb-10 border-t border-[#EDF2F7] dark:border-[#152238] pt-6">
                                    {[
                                        'Auto Deploy Git (Node.js, PHP, Laravel, HTML)',
                                        'Database MySQL & SSL Gratis Otomatis',
                                        'File Manager Panel & Web Terminal Langsung',
                                    ].map(f => (
                                        <li key={f} className="flex items-center gap-3 text-sm text-[#525252] dark:text-white/70">
                                            <span className="font-mono text-[10px] text-[#1D5B96] dark:text-[#6BA8DC] font-bold">&#10003;</span>
                                            <span>{f}</span>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                            <a
                                href="#pricing"
                                className="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] font-semibold text-sm hover:bg-[#1D5B96] dark:hover:bg-[#6BA8DC] transition-colors"
                            >
                                Lihat Opsi Paket
                                <i className="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {/* PRICING */}
            <section id="pricing" className="bg-[#F4F8FC] dark:bg-[#0B1526]">
                <div className="max-w-6xl mx-auto px-6 py-20 lg:py-28">
                    <div className="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-12">
                        <SectionHead
                            num="03"
                            label="Harga"
                            title="Pilih paket."
                            sub="Transparan. Tanpa biaya tersembunyi."
                        />
                        <div className="flex items-center gap-6 border-b border-[#E3EAF1] dark:border-[#1B2C44] shrink-0" data-reveal>
                            {[
                                { key: 'monthly', label: 'Bulanan' },
                                { key: 'yearly', label: 'Tahunan', badge: 'HEMAT' },
                            ].map(c => (
                                <button
                                    key={c.key}
                                    onClick={() => setCycle(c.key)}
                                    className={`pb-3 -mb-px text-sm font-semibold border-b-2 transition-colors ${cycle === c.key ? 'border-[#1D5B96] dark:border-[#6BA8DC] text-[#0F1A2A] dark:text-white' : 'border-transparent text-[#737373] dark:text-white/40 hover:text-[#0F1A2A] dark:hover:text-white'}`}
                                >
                                    {c.label}
                                    {c.badge && (
                                        <span className={`ml-2 font-mono text-[10px] font-semibold tracking-wider ${cycle === c.key ? 'text-emerald-600 dark:text-emerald-400' : 'text-emerald-600/70 dark:text-emerald-400/60'}`}>
                                            {c.badge}
                                        </span>
                                    )}
                                </button>
                            ))}
                        </div>
                    </div>

                    <div className={`grid grid-cols-1 gap-px bg-[#E3EAF1] dark:bg-[#1B2C44] border border-[#E3EAF1] dark:border-[#1B2C44] ${
                        activePlans.length >= 4 ? 'sm:grid-cols-2 lg:grid-cols-4' :
                        activePlans.length === 3 ? 'sm:grid-cols-3' :
                        activePlans.length === 2 ? 'sm:grid-cols-2' : ''
                    }`} data-reveal>
                        {activePlans.map(([slug, plan]) => {
                            const p = priceOf(slug);
                            const pop = slug === 'pro';
                            return (
                                <div key={slug} className={`flex flex-col p-7 ${pop ? 'bg-[#10243C] dark:bg-[#142E4D] text-white' : 'bg-white dark:bg-[#0A1322]'}`}>
                                    <div className="flex items-center justify-between mb-6">
                                        <span className={`font-mono text-[11px] font-semibold uppercase tracking-[0.2em] ${pop ? 'text-[#7FB4E2]' : 'text-[#1D5B96] dark:text-[#6BA8DC]'}`}>{plan.label}</span>
                                        {pop && <span className="font-mono text-[9px] font-semibold uppercase tracking-[0.2em] text-[#0A1322] bg-[#7FB4E2] px-2 py-1">Populer</span>}
                                    </div>
                                    <div className="mb-7">
                                        {p.promo !== null && <span className={`text-xs line-through block mb-1 ${pop ? 'text-white/40' : 'text-[#94A3B4] dark:text-white/35'}`}>Rp {rupiah(p.normal)}</span>}
                                        {p.promo === null && <div className="h-[18px] mb-1"></div>}
                                        <div className="flex items-baseline gap-1.5">
                                            <span className={`text-[1.9rem] font-black tracking-tight ${pop ? 'text-white' : 'text-[#0F1A2A] dark:text-white'}`}>Rp {rupiah(p.active)}</span>
                                            <span className={`text-[12px] font-medium ${pop ? 'text-white/50' : 'text-[#737373] dark:text-white/35'}`}>{p.unit}</span>
                                        </div>
                                        {isYearly && p.active > 0 && (
                                            <span className={`text-[11px] mt-1.5 block font-mono ${pop ? 'text-white/45' : 'text-[#737373] dark:text-white/35'}`}>
                                                setara Rp {rupiah(Math.round(p.active / 12))}/bln
                                            </span>
                                        )}
                                    </div>
                                    <ul className={`flex-1 border-t ${pop ? 'border-white/10' : 'border-[#E3EAF1] dark:border-[#1B2C44]'}`}>
                                        {plan.features.map(f => (
                                            <li key={f} className={`flex items-start gap-2.5 py-2.5 text-[13px] border-b ${pop ? 'text-white/80 border-white/[0.07]' : 'text-[#525252] dark:text-white/55 border-[#EDF2F7] dark:border-[#152238]'}`}>
                                                <span className={`font-mono text-[10px] mt-[3px] ${pop ? 'text-[#7FB4E2]' : 'text-[#1D5B96] dark:text-[#6BA8DC]'}`}>&#10003;</span>
                                                <span>{f}</span>
                                            </li>
                                        ))}
                                    </ul>
                                    <a href={planCtaHref} className={`mt-7 block text-center py-3 text-sm font-semibold transition-colors ${pop ? 'bg-[#2A6FAD] text-white hover:bg-white hover:text-[#0F1A2A]' : 'bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] hover:bg-[#1D5B96] dark:hover:bg-[#6BA8DC] dark:hover:text-[#0F1A2A]'}`}>
                                        Pilih Paket
                                    </a>
                                </div>
                            );
                        })}
                    </div>

                    <div className="grid grid-cols-2 md:grid-cols-4 gap-px bg-[#E3EAF1] dark:bg-[#1B2C44] border border-t-0 border-[#E3EAF1] dark:border-[#1B2C44]" data-reveal>
                        {[
                            { i: 'fa-shield-halved', l: 'SSL Gratis' },
                            { i: 'fa-database', l: 'MySQL' },
                            { i: 'fa-rotate', l: 'Auto-Deploy' },
                            { i: 'fa-headset', l: 'Support' },
                        ].map(f => (
                            <div key={f.l} className="bg-white dark:bg-[#0A1322] flex items-center justify-center gap-2.5 py-4">
                                <i className={`fa-solid ${f.i} text-[#1D5B96] dark:text-[#6BA8DC] text-[12px]`}></i>
                                <span className="font-mono text-[11px] font-medium uppercase tracking-[0.12em] text-[#0F1A2A] dark:text-white">{f.l}</span>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* BLOG */}
            <section id="blog" className="bg-white dark:bg-[#0A1322] border-y border-[#E3EAF1] dark:border-[#1B2C44]">
                <div className="max-w-6xl mx-auto px-6 py-20 lg:py-28">
                    <div className="flex items-end justify-between mb-12 gap-6" data-reveal>
                        <SectionHead num="04" label="Artikel" title="Artikel Terbaru" />
                        <a href="/blog" className="hidden md:inline-flex items-center gap-2 font-mono text-[11px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/45 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors shrink-0 pb-2">
                            Semua Artikel <i className="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                    {articles.length > 0 ? (
                        <>
                            {/* Lead story */}
                            <a href={articles[0].url} className="group grid md:grid-cols-2 border border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322] mb-px" data-reveal>
                                <div className="h-56 md:h-full md:min-h-[280px] overflow-hidden bg-[#E8F0F9] dark:bg-[#16304D] order-1 md:order-none">
                                    {articles[0].cover_image ? (
                                        <img src={articles[0].cover_image} alt={articles[0].title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                    ) : (
                                        <div className="w-full h-full flex items-center justify-center text-[#1D5B96]/20 dark:text-white/15"><i className="fa-solid fa-newspaper text-5xl"></i></div>
                                    )}
                                </div>
                                <div className="p-7 sm:p-10 flex flex-col justify-center">
                                    {articles[0].category && <span className="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] mb-3 block">{articles[0].category.name}</span>}
                                    <h3 className="text-2xl sm:text-3xl font-black text-[#0F1A2A] dark:text-white tracking-tight leading-tight mb-4 group-hover:text-[#1D5B96] dark:group-hover:text-[#6BA8DC] transition-colors line-clamp-3">{articles[0].title}</h3>
                                    <p className="text-sm text-[#525252] dark:text-white/50 leading-relaxed line-clamp-3 mb-6">{articles[0].excerpt || ''}</p>
                                    <div className="flex items-center gap-2.5 font-mono text-[10px] uppercase tracking-[0.15em] text-[#94A3B4] dark:text-white/30">
                                        <span>{formatDate(articles[0].published_at)}</span>
                                        {articles[0].reading_time ? (
                                            <>
                                                <span>&middot;</span>
                                                <span>{articles[0].reading_time} mnt</span>
                                            </>
                                        ) : null}
                                    </div>
                                </div>
                            </a>

                            {articles.length > 1 && (
                                <div
                                    className={`grid gap-6 mt-6 ${
                                        articles.length === 2
                                            ? 'grid-cols-1 md:max-w-md'
                                            : articles.length === 3
                                            ? 'grid-cols-1 md:grid-cols-2'
                                            : 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
                                    }`}
                                    data-reveal
                                >
                                    {articles.slice(1).map(a => (
                                        <a
                                            key={a.id}
                                            href={a.url}
                                            className="bg-white dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] p-5 group flex flex-col transition-colors"
                                        >
                                            {a.cover_image ? (
                                                <div className="h-40 overflow-hidden bg-[#E8F0F9] dark:bg-[#16304D] mb-5 border-b border-[#E3EAF1] dark:border-[#1B2C44]"><img src={a.cover_image} alt={a.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" /></div>
                                            ) : (
                                                <div className="h-40 bg-[#E8F0F9] dark:bg-[#16304D] mb-5 border-b border-[#E3EAF1] dark:border-[#1B2C44] flex items-center justify-center text-[#1D5B96]/20 dark:text-white/12"><i className="fa-solid fa-newspaper text-3xl"></i></div>
                                            )}
                                            {a.category && <span className="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC]">{a.category.name}</span>}
                                            <h3 className="text-[15px] font-bold text-[#0F1A2A] dark:text-white mt-2 mb-2 group-hover:text-[#1D5B96] dark:group-hover:text-[#6BA8DC] transition-colors line-clamp-2 leading-snug">{a.title}</h3>
                                            <p className="text-[13px] text-[#525252] dark:text-white/45 line-clamp-2 mb-5 flex-1">{a.excerpt || ''}</p>
                                            <div className="flex items-center gap-2.5 font-mono text-[10px] uppercase tracking-[0.15em] text-[#94A3B4] dark:text-white/25 pt-4 border-t border-[#EDF2F7] dark:border-[#152238]">
                                                <span>{formatDate(a.published_at)}</span>
                                                {a.reading_time ? (
                                                    <>
                                                        <span>&middot;</span>
                                                        <span>{a.reading_time} mnt</span>
                                                    </>
                                                ) : null}
                                            </div>
                                        </a>
                                    ))}
                                </div>
                            )}
                        </>
                    ) : (
                        <div className="border border-[#E3EAF1] dark:border-[#1B2C44] py-20 text-center font-mono text-[12px] uppercase tracking-[0.2em] text-[#94A3B4] dark:text-white/30" data-reveal>Belum ada artikel.</div>
                    )}

                    <div className="mt-8 md:hidden text-center">
                        <a href="/blog" className="inline-flex items-center gap-2 font-mono text-[11px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/45 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">Semua Artikel <i className="fa-solid fa-arrow-right text-[10px]"></i></a>
                    </div>
                </div>
            </section>

            {/* FAQ */}
            <section id="faq" className="bg-[#F4F8FC] dark:bg-[#0B1526] border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                <div className="max-w-6xl mx-auto px-6 py-20 lg:py-28">
                    <div className="grid lg:grid-cols-12 gap-10 lg:gap-16">
                        <div className="lg:col-span-4">
                            <SectionHead num="05" label="FAQ" title="Pertanyaan Umum" />
                        </div>
                        <div className="lg:col-span-8 lg:pt-2" data-reveal>
                            {faqData.map((f, i) => <FaqItem key={i} index={i} q={f.q} a={f.a} />)}
                        </div>
                    </div>
                </div>
            </section>

            {/* CTA */}
            <section className="relative bg-[#1A4A78] dark:bg-[#143D66] overflow-hidden">
                <div className="absolute inset-0 grid-texture"></div>
                <div className="relative max-w-6xl mx-auto px-6 py-24 lg:py-32 text-center flex flex-col items-center" data-reveal>
                    <p className="font-mono text-[11px] uppercase tracking-[0.25em] text-white/55 mb-6">Mulai</p>
                    <h2 className="text-[clamp(2.2rem,5.5vw,4rem)] font-black text-white tracking-[-0.03em] leading-[1] mb-6">
                        Siap mengeksekusi ide?
                    </h2>
                    <p className="text-white/70 text-lg mb-10 max-w-md mx-auto leading-relaxed">
                        Daftar gratis, deploy dari Git, jalankan dalam hitungan menit.
                    </p>
                    <a href="/register" className="group inline-flex items-center gap-2.5 px-8 py-4 bg-white text-[#0F1A2A] text-sm font-semibold hover:bg-[#0F1A2A] hover:text-white transition-colors">
                        Mulai Sekarang
                        <i className="fa-solid fa-arrow-right text-[11px] transition-transform duration-300 group-hover:translate-x-1"></i>
                    </a>
                    {version && (
                        <p className="mt-10 font-mono text-[11px] text-white/40 tracking-[0.2em] uppercase">
                            <i className="fa-solid fa-code-branch mr-2"></i>{version}
                        </p>
                    )}
                </div>
            </section>

            {/* FLOATING AI CHAT WIDGET */}
            <div className="fixed bottom-6 right-6 z-40 flex flex-col items-end">
                {chatOpen && (
                    <div className="bg-white dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#1B2C44] shadow-2xl w-[calc(100vw-3rem)] max-w-sm h-[480px] sm:h-[520px] mb-3 flex flex-col overflow-hidden">
                        {/* Header */}
                        <div className="bg-[#0F1A2A] dark:bg-[#0B1526] px-4 py-3 flex justify-between items-center border-b border-white/10 shrink-0">
                            <div className="flex items-center gap-2.5">
                                <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span className="text-white font-mono font-bold text-xs uppercase tracking-wider">Ryaze Assistant</span>
                                <span className="font-mono text-[9px] uppercase px-1.5 py-0.5 bg-white/10 text-white/70 tracking-widest">AI</span>
                            </div>
                            <button
                                onClick={() => setChatOpen(false)}
                                aria-label="Tutup chat"
                                className="w-7 h-7 flex items-center justify-center text-white/50 hover:text-white transition-colors"
                            >
                                <i className="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </div>

                        {/* Messages Area */}
                        <div className="flex-1 p-4 overflow-y-auto space-y-3 bg-[#FAFBFD] dark:bg-[#070E18]">
                            {chatMsgs.length === 0 && (
                                <div className="space-y-3">
                                    <div className="flex items-start gap-2.5">
                                        <div className="w-6 h-6 bg-[#12233A] dark:bg-[#1D5B96] text-white flex items-center justify-center shrink-0">
                                            <i className="fa-solid fa-robot text-[10px]"></i>
                                        </div>
                                        <div className="bg-white dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] px-3.5 py-2.5 text-xs text-[#0F1A2A] dark:text-white/80 max-w-[85%] leading-relaxed shadow-sm">
                                            Halo! Saya asisten AI Ryaze. Ada yang bisa saya bantu seputar paket hosting, deploy Git, atau jasa website?
                                        </div>
                                    </div>
                                    <div className="pt-2 pl-8 flex flex-col gap-1.5">
                                        <p className="font-mono text-[10px] text-[#737373] dark:text-white/35 uppercase tracking-wider">Pertanyaan cepat:</p>
                                        {[
                                            'Bagaimana cara deploy dari Git?',
                                            'Apakah ada SSL gratis & MySQL?',
                                            'Bisa pesan jasa pembuatan website?',
                                        ].map(suggestion => (
                                            <button
                                                key={suggestion}
                                                type="button"
                                                onClick={() => {
                                                    setChatInput(suggestion);
                                                }}
                                                className="text-left font-mono text-[11px] text-[#1D5B96] dark:text-[#6BA8DC] hover:underline"
                                            >
                                                &rarr; {suggestion}
                                            </button>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {chatMsgs.map(m => (
                                <div key={m.id} className={`flex items-start gap-2.5 ${m.user ? 'flex-row-reverse' : ''}`}>
                                    <div className={`w-6 h-6 flex items-center justify-center shrink-0 ${m.user ? 'bg-[#E3EAF1] dark:bg-[#1B2C44] text-[#0F1A2A] dark:text-white' : 'bg-[#12233A] dark:bg-[#1D5B96] text-white'}`}>
                                        <i className={`fa-solid ${m.user ? 'fa-user text-[10px]' : 'fa-robot text-[10px]'}`}></i>
                                    </div>
                                    <div className={`px-3.5 py-2.5 text-xs max-w-[85%] leading-relaxed shadow-sm ${
                                        m.user
                                            ? 'bg-[#12233A] dark:bg-[#1D5B96] text-white'
                                            : 'bg-white dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] text-[#0F1A2A] dark:text-white/85 whitespace-pre-wrap'
                                    }`}>
                                        {m.text}
                                    </div>
                                </div>
                            ))}

                            {chatLoading && (
                                <div className="flex items-start gap-2.5">
                                    <div className="w-6 h-6 bg-[#12233A] dark:bg-[#1D5B96] text-white flex items-center justify-center shrink-0">
                                        <i className="fa-solid fa-robot text-[10px]"></i>
                                    </div>
                                    <div className="bg-white dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] px-4 py-3 flex gap-1.5 shadow-sm">
                                        <div className="w-1.5 h-1.5 bg-[#1D5B96] dark:bg-[#6BA8DC] rounded-full animate-bounce"></div>
                                        <div className="w-1.5 h-1.5 bg-[#1D5B96] dark:bg-[#6BA8DC] rounded-full animate-bounce" style={{ animationDelay: '0.15s' }}></div>
                                        <div className="w-1.5 h-1.5 bg-[#1D5B96] dark:bg-[#6BA8DC] rounded-full animate-bounce" style={{ animationDelay: '0.3s' }}></div>
                                    </div>
                                </div>
                            )}
                            <div ref={chatEndRef} />
                        </div>

                        {/* Input Footer */}
                        <form onSubmit={sendChat} className="p-3 bg-white dark:bg-[#0A1322] border-t border-[#E3EAF1] dark:border-[#1B2C44] flex gap-2">
                            <input
                                type="text"
                                value={chatInput}
                                onChange={e => setChatInput(e.target.value)}
                                placeholder="Ketik pesan..."
                                required
                                className="flex-1 bg-[#F4F8FC] dark:bg-[#0D1828] border border-[#C9D5E2] dark:border-[#24384F] text-xs px-3.5 py-2.5 text-[#0F1A2A] dark:text-white placeholder-[#94A3B4] dark:placeholder-white/30 focus:outline-none focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] transition-colors"
                            />
                            <button
                                type="submit"
                                disabled={chatLoading || !chatInput.trim()}
                                aria-label="Kirim pesan"
                                className="w-9 h-9 bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] hover:bg-[#1D5B96] dark:hover:bg-[#6BA8DC] flex items-center justify-center transition-colors disabled:opacity-40 shrink-0"
                            >
                                <i className="fa-solid fa-paper-plane text-[11px]"></i>
                            </button>
                        </form>
                    </div>
                )}

                <button
                    onClick={() => setChatOpen(!chatOpen)}
                    aria-label={chatOpen ? 'Tutup bantuan chat' : 'Buka bantuan AI'}
                    className="w-12 h-12 sm:w-14 sm:h-14 bg-[#12233A] dark:bg-[#1D5B96] hover:bg-[#1D5B96] dark:hover:bg-[#2A6FAD] text-white flex items-center justify-center shadow-2xl border border-white/10 transition-all hover:scale-105 ml-auto relative group"
                >
                    <i className={`fa-solid ${chatOpen ? 'fa-xmark' : 'fa-message'} text-lg group-hover:scale-110 transition-transform`}></i>
                    {!chatOpen && (
                        <span className="absolute -top-1 -right-1 w-3 h-3 bg-emerald-400 border-2 border-white dark:border-[#0A1322] rounded-full"></span>
                    )}
                </button>
            </div>
        </PublicLayout>
    </>
);
}
