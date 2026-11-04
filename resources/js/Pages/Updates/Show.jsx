import PublicLayout from '../../Layouts/PublicLayout';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { useReveal, RevealStyle, SectionHead } from '../../Components/PublicSection';

const PRIORITY_STYLE = {
    critical: {
        label: 'Darurat',
        badge: 'bg-rose-500 text-white border-rose-600',
        rail: 'bg-rose-500',
    },
    high: {
        label: 'Mendesak',
        badge: 'bg-amber-400 text-[#0F1A2A] border-amber-500 font-bold',
        rail: 'bg-amber-400',
    },
    normal: {
        label: 'Penting',
        badge: 'bg-[#12233A] dark:bg-[#1D5B96] text-white border-transparent',
        rail: 'bg-[#1D5B96]',
    },
    low: {
        label: 'Informasi',
        badge: 'bg-[#F0F6FC] dark:bg-[#0D1828] text-[#525252] dark:text-white/70 border-[#E3EAF1] dark:border-[#1B2C44]',
        rail: '',
    },
};

export default function Show({ update, related, categories, seoTitle, seoDescription, canonical }) {
    useReveal();
    const [copied, setCopied] = useState(false);

    const priority = PRIORITY_STYLE[update?.priority] || PRIORITY_STYLE.normal;
    const cat = (categories || []).find(c => c.key === update?.category) || null;

    // update.url sudah berupa URL bersih dari server; hindari query pelacak.
    const pageUrl = canonical || update?.url || (typeof window !== 'undefined'
        ? `${window.location.origin}${window.location.pathname}`
        : `https://ryaze.my.id/updates/${update?.slug}`);
    const pageTitle = seoTitle || `${update?.title || 'Info Update'} | Ryaze`;
    const description = seoDescription || update?.excerpt || 'Kabar terbaru dan pengumuman resmi platform Ryaze.';

    const articleJsonLd = {
        '@context': 'https://schema.org',
        '@type': 'Article',
        headline: update?.title,
        description: description,
        url: pageUrl,
        author: { '@type': 'Person', name: update?.author || 'Tim Ryaze' },
        publisher: {
            '@type': 'Organization',
            name: 'Ryaze',
            logo: { '@type': 'ImageObject', url: 'https://ryaze.my.id/og-image.png', width: 1200, height: 630 },
        },
    };

    const copyUrl = async () => {
        try {
            await navigator.clipboard.writeText(pageUrl);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch {
            setCopied(false);
        }
    };

    return (
        <>
            <Head title={pageTitle}>
                <meta name="description" content={description} />
                <meta property="og:type" content="article" />
                <meta property="og:url" content={pageUrl} />
                <meta property="og:site_name" content="Ryaze" />
                <meta property="og:locale" content="id_ID" />
                <meta property="og:title" content={pageTitle} />
                <meta property="og:description" content={description} />
                <meta property="og:image" content="https://ryaze.my.id/og-image.png" />
                <meta property="twitter:card" content="summary_large_image" />
                <meta property="twitter:url" content={pageUrl} />
                <meta property="twitter:title" content={pageTitle} />
                <meta property="twitter:description" content={description} />
                <link rel="canonical" href={pageUrl} />
                <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(articleJsonLd) }} />
            </Head>
            <PublicLayout>
                <RevealStyle />

                {/* ARTICLE HEADER & CONTENT */}
                <article className="bg-white dark:bg-[#0A1322] border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                    <div className="max-w-4xl mx-auto px-6 pt-28 pb-16 lg:pt-36 lg:pb-24" data-reveal>
                        {/* Breadcrumbs */}
                        <nav className="flex items-center gap-2 font-mono text-[11px] uppercase tracking-[0.18em] text-[#737373] dark:text-white/40 mb-8 overflow-x-auto whitespace-nowrap">
                            <a href="/" className="hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">Home</a>
                            <span className="text-[#C9D5E2] dark:text-[#24384F]">/</span>
                            <a href="/updates" className="hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">Info Update</a>
                            {cat && (
                                <>
                                    <span className="text-[#C9D5E2] dark:text-[#24384F]">/</span>
                                    <a
                                        href={`/updates?kategori=${cat.key}`}
                                        className="hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors"
                                    >
                                        {cat.label}
                                    </a>
                                </>
                            )}
                        </nav>

                        {/* Relative Container with priority rail */}
                        <div className="relative pl-0 sm:pl-6 sm:border-l-2 sm:border-[#E3EAF1] sm:dark:border-[#1B2C44]">
                            {priority.rail && (
                                <span className={`hidden sm:block absolute -left-[2px] top-0 bottom-0 w-[2px] ${priority.rail}`} aria-hidden="true"></span>
                            )}

                            {/* Tags and badges */}
                            <div className="flex flex-wrap items-center gap-2.5 mb-5">
                                {cat && (
                                    <span className="inline-flex items-center gap-1.5 px-2.5 py-1 font-mono text-[10px] uppercase tracking-[0.18em] font-semibold text-[#1D5B96] dark:text-[#6BA8DC] bg-[#F0F6FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44]">
                                        <i className={`fa-solid ${cat.icon} text-[8px]`}></i>
                                        {cat.label}
                                    </span>
                                )}
                                <span className={`px-2.5 py-1 font-mono text-[9px] uppercase tracking-[0.2em] font-semibold border ${priority.badge}`}>
                                    {priority.label}
                                </span>
                                {update?.version && (
                                    <span className="font-mono text-[10px] uppercase tracking-[0.15em] px-2.5 py-1 bg-[#F4F8FC] dark:bg-[#0D1828] text-[#0F1A2A] dark:text-white/70 border border-[#E3EAF1] dark:border-[#1B2C44]">
                                        {update.version}
                                    </span>
                                )}
                            </div>

                            {/* Title */}
                            <h1 className="text-[clamp(2.1rem,5vw,3.4rem)] font-black text-[#0F1A2A] dark:text-white leading-[1.08] tracking-[-0.035em] mb-7">
                                {update?.title}
                            </h1>

                            {/* Metadata & Actions */}
                            <div className="flex flex-wrap items-center justify-between gap-4 pb-8 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                                <div className="flex flex-wrap items-center gap-4 text-xs">
                                    <span className="font-mono text-[11px] uppercase tracking-[0.15em] text-[#737373] dark:text-white/40 flex items-center gap-1.5">
                                        <i className="fa-regular fa-calendar text-[10px]"></i>
                                        {update?.published_at_long || update?.published_at}
                                    </span>
                                    {update?.author && (
                                        <>
                                            <span className="text-[#C9D5E2] dark:text-[#24384F]">&middot;</span>
                                            <span className="inline-flex items-center gap-1.5 text-xs text-[#0F1A2A] dark:text-white font-medium">
                                                <span className="w-5 h-5 bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] text-[9px] font-mono font-bold flex items-center justify-center">
                                                    {update.author.charAt(0).toUpperCase()}
                                                </span>
                                                {update.author}
                                            </span>
                                        </>
                                    )}
                                </div>

                                <div className="flex items-center gap-2">
                                    <button
                                        onClick={copyUrl}
                                        aria-label="Salin tautan update"
                                        className="h-8 inline-flex items-center justify-center gap-1.5 px-3 font-mono text-[10px] uppercase tracking-[0.15em] text-[#525252] dark:text-white/60 bg-[#F4F8FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors"
                                    >
                                        <i className={`fa-solid ${copied ? 'fa-check text-emerald-500' : 'fa-link'} text-[10px]`}></i>
                                        <span>{copied ? 'Tersalin' : 'Salin'}</span>
                                    </button>
                                    <a
                                        href={`https://twitter.com/intent/tweet?text=${encodeURIComponent(update?.title || '')}&url=${encodeURIComponent(pageUrl)}`}
                                        target="_blank"
                                        rel="noreferrer"
                                        aria-label="Bagikan ke X"
                                        className="h-8 w-8 inline-flex items-center justify-center text-[#525252] dark:text-white/60 bg-[#F4F8FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors"
                                    >
                                        <i className="fa-brands fa-x-twitter text-[11px]"></i>
                                    </a>
                                    <a
                                        href={`https://api.whatsapp.com/send?text=${encodeURIComponent(`${update?.title || ''} - ${pageUrl}`)}`}
                                        target="_blank"
                                        rel="noreferrer"
                                        aria-label="Bagikan ke WhatsApp"
                                        className="h-8 w-8 inline-flex items-center justify-center text-[#525252] dark:text-white/60 bg-[#F4F8FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-emerald-500 hover:text-emerald-500 transition-colors"
                                    >
                                        <i className="fa-brands fa-whatsapp text-[12px]"></i>
                                    </a>
                                </div>
                            </div>

                            {/* Main Body Description */}
                            <div className="mt-8 text-[15px] sm:text-[16px] text-[#2D3748] dark:text-white/80 leading-[1.9] whitespace-pre-line font-sans">
                                {update?.description}
                            </div>
                        </div>
                    </div>
                </article>

                {/* UPDATE LAINNYA */}
                {related && related.length > 0 && (
                    <section className="bg-[#F4F8FC] dark:bg-[#0B1526] border-t border-[#E3EAF1] dark:border-[#1B2C44]">
                        <div className="max-w-6xl mx-auto px-6 py-16 lg:py-24">
                            <div className="flex items-end justify-between mb-10 gap-6" data-reveal>
                                <SectionHead
                                    num="02"
                                    label="Arsip Terkait"
                                    title="Info Update Lainnya"
                                    sub="Pembaruan sistem dan catatan rilis sebelumnya."
                                />
                                <a
                                    href="/updates"
                                    className="hidden sm:inline-flex items-center gap-2 font-mono text-[11px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/45 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors shrink-0 pb-2"
                                >
                                    Semua Update <i className="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>

                            <div
                                className={`grid gap-6 ${
                                    related.length === 1
                                        ? 'grid-cols-1 md:max-w-xl'
                                        : related.length === 2
                                        ? 'grid-cols-1 md:grid-cols-2'
                                        : 'grid-cols-1 md:grid-cols-3'
                                }`}
                                data-reveal
                            >
                                {related.map(item => {
                                    const itemCat = (categories || []).find(c => c.key === item.category);
                                    const itemPriority = PRIORITY_STYLE[item.priority] || PRIORITY_STYLE.normal;
                                    return (
                                        <a
                                            key={item.id}
                                            href={`/updates/${item.slug}`}
                                            className="group bg-white dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] p-6 flex flex-col transition-colors hover:bg-[#FAFBFD] dark:hover:bg-[#0D192B]"
                                        >
                                            <div className="flex flex-wrap items-center gap-2 mb-3">
                                                {itemCat && (
                                                    <span className="inline-flex items-center gap-1 px-2 py-0.5 font-mono text-[9px] uppercase tracking-[0.18em] font-semibold text-[#1D5B96] dark:text-[#6BA8DC] bg-[#F0F6FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44]">
                                                        <i className={`fa-solid ${itemCat.icon} text-[7px]`}></i>
                                                        {itemCat.label}
                                                    </span>
                                                )}
                                                <span className={`px-2 py-0.5 font-mono text-[8px] uppercase tracking-[0.18em] font-semibold border ${itemPriority.badge}`}>
                                                    {itemPriority.label}
                                                </span>
                                            </div>

                                            <h3 className="text-[15px] font-bold text-[#0F1A2A] dark:text-white leading-snug group-hover:text-[#1D5B96] dark:group-hover:text-[#6BA8DC] transition-colors line-clamp-2">
                                                {item.title}
                                            </h3>

                                            <p className="mt-2.5 text-xs text-[#525252] dark:text-white/50 line-clamp-2 leading-relaxed flex-1">
                                                {item.excerpt}
                                            </p>

                                            <div className="mt-5 pt-4 border-t border-[#EDF2F7] dark:border-[#152238] flex items-center justify-between font-mono text-[10px] uppercase tracking-[0.15em] text-[#94A3B4] dark:text-white/30">
                                                <span>{item.published_at}</span>
                                                <span className="text-[#1D5B96] dark:text-[#6BA8DC] font-semibold">Detail &rarr;</span>
                                            </div>
                                        </a>
                                    );
                                })}
                            </div>

                            <div className="mt-8 text-center sm:hidden">
                                <a
                                    href="/updates"
                                    className="inline-flex items-center gap-2 font-mono text-[11px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/45 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors"
                                >
                                    Semua Update <i className="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </section>
                )}
            </PublicLayout>
        </>
    );
}
