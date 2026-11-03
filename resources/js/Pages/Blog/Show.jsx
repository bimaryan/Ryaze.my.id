import PublicLayout from '../../Layouts/PublicLayout';
import { Head } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { useReveal, RevealStyle, SectionHead } from '../../Components/PublicSection';

function formatNewsDate(val) {
    if (!val) return '';
    const d = new Date(val);
    if (isNaN(d.getTime())) return val;
    return d.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

function formatDateShort(val) {
    if (!val) return '';
    const d = new Date(val);
    if (isNaN(d.getTime())) return val;
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

export default function Show({ article, related = [], popular = [], categories = [], latestUpdates = [] }) {
    useReveal();
    const [copied, setCopied] = useState(false);
    const [fontSize, setFontSize] = useState('base'); // 'sm' | 'base' | 'lg'
    const [scrollPercent, setScrollPercent] = useState(0);

    // Reading progress tracker
    useEffect(() => {
        const onScroll = () => {
            const total = document.documentElement.scrollHeight - window.innerHeight;
            if (total > 0) {
                setScrollPercent(Math.min(100, Math.max(0, (window.scrollY / total) * 100)));
            }
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    const tags = (() => {
        if (!article?.tags) return [];
        if (Array.isArray(article.tags)) return article.tags;
        return article.tags.split(',').map(t => t.trim()).filter(Boolean);
    })();

    const pageUrl = typeof window !== 'undefined' ? window.location.href : `https://ryaze.my.id/blog/${article?.slug}`;
    const pageTitle = `${article?.seo_title || article?.title || 'Artikel'} | Ryaze News`;
    const description = article?.seo_description || article?.excerpt || '';
    const ogImage = article?.cover_image || 'https://ryaze.my.id/og-image.png';

    const copyUrl = async () => {
        try {
            await navigator.clipboard.writeText(pageUrl);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch {
            setCopied(false);
        }
    };

    const articleJsonLd = {
        '@context': 'https://schema.org',
        '@type': 'NewsArticle',
        headline: article?.title,
        description: description,
        image: ogImage,
        datePublished: article?.published_at,
        url: pageUrl,
        author: {
            '@type': 'Person',
            name: article?.user?.name || 'Redaksi Ryaze',
        },
        publisher: {
            '@type': 'Organization',
            name: 'Ryaze',
            logo: {
                '@type': 'ImageObject',
                url: 'https://ryaze.my.id/og-image.png',
            },
        },
    };

    const fontSizeClass = {
        sm: 'text-[15px] leading-[1.8]',
        base: 'text-[16.5px] leading-[1.85]',
        lg: 'text-[18.5px] leading-[1.9]',
    }[fontSize];

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
                <meta property="og:image" content={ogImage} />
                <meta property="twitter:card" content="summary_large_image" />
                <meta property="twitter:url" content={pageUrl} />
                <meta property="twitter:title" content={pageTitle} />
                <meta property="twitter:description" content={description} />
                <meta property="twitter:image" content={ogImage} />
                <link rel="canonical" href={pageUrl} />
                <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(articleJsonLd) }} />
            </Head>

            <PublicLayout>
                <RevealStyle />

                {/* READING PROGRESS BAR */}
                <div className="fixed top-16 inset-x-0 z-40 h-[3px] bg-transparent pointer-events-none">
                    <div
                        className="h-full bg-[#1D5B96] dark:bg-[#6BA8DC] transition-all duration-100 ease-out"
                        style={{ width: `${scrollPercent}%` }}
                    />
                </div>

                {/* NEWS HERO & EDITORIAL HEADER */}
                <header className="bg-white dark:bg-[#0A1322] border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                    <div className="max-w-6xl mx-auto px-6 pt-28 pb-8 lg:pt-32 lg:pb-10" data-reveal>
                        {/* News Breadcrumbs */}
                        <nav className="flex items-center gap-2 font-mono text-[10.5px] uppercase tracking-[0.18em] text-[#737373] dark:text-white/40 mb-6 overflow-x-auto whitespace-nowrap">
                            <a href="/" className="hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">Home</a>
                            <span className="text-[#C9D5E2] dark:text-[#24384F]">/</span>
                            <a href="/blog" className="hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">Blog</a>
                            {article?.category && (
                                <>
                                    <span className="text-[#C9D5E2] dark:text-[#24384F]">/</span>
                                    <a
                                        href={`/blog?category=${article.category.slug}`}
                                        className="text-[#1D5B96] dark:text-[#6BA8DC] font-semibold hover:underline"
                                    >
                                        {article.category.name}
                                    </a>
                                </>
                            )}
                        </nav>

                        {/* News Rubric Badge */}
                        <div className="flex items-center gap-3 mb-4">
                            {article?.category ? (
                                <a
                                    href={`/blog?category=${article.category.slug}`}
                                    className="inline-flex items-center gap-1.5 px-3 py-1 font-mono text-[10px] uppercase tracking-[0.2em] font-bold text-white bg-[#12233A] dark:bg-[#1D5B96] hover:bg-[#1D5B96] transition-colors"
                                >
                                    <span className="w-1.5 h-1.5 bg-[#6BA8DC] rounded-full"></span>
                                    {article.category.name}
                                </a>
                            ) : (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 font-mono text-[10px] uppercase tracking-[0.2em] font-bold text-white bg-[#12233A] dark:bg-[#1D5B96]">
                                    Blog
                                </span>
                            )}
                            <span className="font-mono text-[11px] uppercase tracking-[0.15em] text-[#737373] dark:text-white/40">
                                {article?.reading_time || 3} Menit Baca
                            </span>
                        </div>

                        {/* Main News Headline (H1) */}
                        <h1 className="text-[clamp(2.1rem,4.5vw,3.6rem)] font-black text-[#0F1A2A] dark:text-white leading-[1.08] tracking-[-0.035em] mb-6 max-w-5xl">
                            {article?.title}
                        </h1>

                        {/* News Excerpt / Lead Summary */}
                        {article?.excerpt && (
                            <p className="text-[17px] sm:text-lg text-[#525252] dark:text-white/70 leading-relaxed font-normal mb-8 max-w-4xl pl-4 border-l-2 border-[#1D5B96] dark:border-[#6BA8DC]">
                                {article.excerpt}
                            </p>
                        )}

                        {/* News Byline, Timestamp & Reader Toolbar */}
                        <div className="pt-5 border-t border-[#E3EAF1] dark:border-[#1B2C44] flex flex-col md:flex-row md:items-center justify-between gap-5">
                            {/* Author & Publisher */}
                            <div className="flex items-center gap-3.5">
                                <div className="w-10 h-10 bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] flex items-center justify-center font-mono font-bold text-sm ring-1 ring-[#E3EAF1] dark:ring-[#24384F] shrink-0">
                                    {article?.user?.name ? article.user.name.charAt(0).toUpperCase() : 'R'}
                                </div>
                                <div className="leading-snug">
                                    <p className="text-sm font-bold text-[#0F1A2A] dark:text-white">
                                        {article?.user?.name || 'Redaksi Ryaze'}
                                    </p>
                                    <p className="font-mono text-[11px] text-[#737373] dark:text-white/40 mt-0.5">
                                        {formatNewsDate(article?.published_at)}
                                        {article?.views_count !== undefined && (
                                            <>
                                                <span className="mx-1.5 text-[#C9D5E2] dark:text-[#24384F]">&middot;</span>
                                                <span className="inline-flex items-center gap-1">
                                                    <i className="fa-solid fa-eye text-[10px]"></i>
                                                    {article.views_count} Pembaca
                                                </span>
                                            </>
                                        )}
                                    </p>
                                </div>
                            </div>

                            {/* News Actions: Font Size Control + Share Buttons */}
                            <div className="flex flex-wrap items-center gap-3">
                                {/* Font Size Adjuster (Classic News Portal Feature) */}
                                <div className="hidden sm:flex items-center border border-[#E3EAF1] dark:border-[#1B2C44] bg-[#F4F8FC] dark:bg-[#0D1828] h-8 font-mono text-[11px]">
                                    <span className="px-2 text-[#94A3B4] dark:text-white/30 text-[10px] uppercase tracking-wider border-r border-[#E3EAF1] dark:border-[#1B2C44]">
                                        Teks
                                    </span>
                                    <button
                                        type="button"
                                        onClick={() => setFontSize('sm')}
                                        title="Perkecil Ukuran Teks"
                                        className={`px-2 h-full flex items-center justify-center transition-colors ${
                                            fontSize === 'sm'
                                                ? 'bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] font-bold'
                                                : 'text-[#525252] dark:text-white/60 hover:text-[#1D5B96]'
                                        }`}
                                    >
                                        A-
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setFontSize('base')}
                                        title="Ukuran Normal Teks"
                                        className={`px-2 h-full flex items-center justify-center border-x border-[#E3EAF1] dark:border-[#1B2C44] transition-colors ${
                                            fontSize === 'base'
                                                ? 'bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] font-bold'
                                                : 'text-[#525252] dark:text-white/60 hover:text-[#1D5B96]'
                                        }`}
                                    >
                                        A
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setFontSize('lg')}
                                        title="Perbesar Ukuran Teks"
                                        className={`px-2 h-full flex items-center justify-center transition-colors ${
                                            fontSize === 'lg'
                                                ? 'bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] font-bold'
                                                : 'text-[#525252] dark:text-white/60 hover:text-[#1D5B96]'
                                        }`}
                                    >
                                        A+
                                    </button>
                                </div>

                                {/* Share Toolbar */}
                                <div className="flex items-center gap-2">
                                    <button
                                        onClick={copyUrl}
                                        aria-label="Salin tautan artikel"
                                        className="h-8 inline-flex items-center justify-center gap-1.5 px-3 font-mono text-[10px] uppercase tracking-[0.15em] text-[#525252] dark:text-white/60 bg-[#F4F8FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors"
                                    >
                                        <i className={`fa-solid ${copied ? 'fa-check text-emerald-500' : 'fa-link'} text-[10px]`}></i>
                                        <span>{copied ? 'Tersalin' : 'Salin'}</span>
                                    </button>
                                    <a
                                        href={`https://api.whatsapp.com/send?text=${encodeURIComponent(`${article?.title || ''} - ${pageUrl}`)}`}
                                        target="_blank"
                                        rel="noreferrer"
                                        aria-label="Bagikan ke WhatsApp"
                                        title="Bagikan ke WhatsApp"
                                        className="h-8 w-8 inline-flex items-center justify-center text-[#525252] dark:text-white/60 bg-[#F4F8FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-emerald-500 hover:text-emerald-500 transition-colors"
                                    >
                                        <i className="fa-brands fa-whatsapp text-[12px]"></i>
                                    </a>
                                    <a
                                        href={`https://twitter.com/intent/tweet?text=${encodeURIComponent(article?.title || '')}&url=${encodeURIComponent(pageUrl)}`}
                                        target="_blank"
                                        rel="noreferrer"
                                        aria-label="Bagikan ke X"
                                        title="Bagikan ke X"
                                        className="h-8 w-8 inline-flex items-center justify-center text-[#525252] dark:text-white/60 bg-[#F4F8FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors"
                                    >
                                        <i className="fa-brands fa-x-twitter text-[11px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                {/* MAIN CONTENT + NEWS SIDEBAR (2-COLUMN GRID) */}
                <main className="bg-white dark:bg-[#0A1322]">
                    <div className="max-w-6xl mx-auto px-6 py-10 lg:py-14">
                        <div className="grid lg:grid-cols-12 gap-10 lg:gap-14 items-start">
                            {/* LEFT COLUMN: THE NEWS ARTICLE (8 COLS) */}
                            <div className="lg:col-span-8">
                                {/* Featured Hero Image with Caption */}
                                {article?.cover_image && (
                                    <figure className="mb-8 border border-[#E3EAF1] dark:border-[#1B2C44] bg-[#F0F6FC] dark:bg-[#0D1828] overflow-hidden">
                                        <img
                                            src={article.cover_image}
                                            alt={article.title}
                                            className="w-full h-auto max-h-[460px] object-cover"
                                        />
                                        <figcaption className="px-4 py-2.5 bg-[#F4F8FC] dark:bg-[#0D1828] border-t border-[#E3EAF1] dark:border-[#1B2C44] text-[11.5px] font-mono text-[#737373] dark:text-white/40 flex items-center justify-between">
                                            <span>Foto: {article.title}</span>
                                            <span className="italic">(Dok. Ryaze)</span>
                                        </figcaption>
                                    </figure>
                                )}

                                {/* Article Body with Dateline */}
                                <div className="border-b border-[#E3EAF1] dark:border-[#1B2C44] pb-10">

                                    <div
                                        className={`prose prose-slate dark:prose-invert max-w-none text-[#2D3748] dark:text-white/85 ${fontSizeClass}
                                            prose-headings:text-[#0F1A2A] dark:prose-headings:text-white prose-headings:font-black prose-headings:tracking-tight
                                            prose-h2:text-2xl sm:prose-h2:text-3xl prose-h2:mt-8 prose-h2:mb-4
                                            prose-h3:text-xl sm:prose-h3:text-2xl prose-h3:mt-6 prose-h3:mb-3
                                            prose-p:my-4
                                            prose-a:text-[#1D5B96] dark:prose-a:text-[#6BA8DC] prose-a:underline hover:prose-a:text-[#12233A] dark:hover:prose-a:text-white
                                            prose-strong:text-[#0F1A2A] dark:prose-strong:text-white prose-strong:font-bold
                                            prose-code:text-[#1D5B96] dark:prose-code:text-[#6BA8DC] prose-code:bg-[#F0F6FC] dark:prose-code:bg-[#0D1828] prose-code:border prose-code:border-[#E3EAF1] dark:prose-code:border-[#1B2C44] prose-code:px-1.5 prose-code:py-0.5 prose-code:font-mono prose-code:text-sm prose-code:before:content-none prose-code:after:content-none
                                            prose-pre:bg-[#0A1322] dark:prose-pre:bg-[#070E18] prose-pre:border prose-pre:border-[#E3EAF1] dark:prose-pre:border-[#1B2C44] prose-pre:rounded-none prose-pre:p-5 prose-pre:font-mono
                                            prose-blockquote:border-l-4 prose-blockquote:border-[#1D5B96] dark:prose-blockquote:border-[#6BA8DC] prose-blockquote:italic prose-blockquote:text-[#525252] dark:prose-blockquote:text-white/70 prose-blockquote:bg-[#F4F8FC] dark:prose-blockquote:bg-[#0D1828]/50 prose-blockquote:py-3.5 prose-blockquote:px-5 prose-blockquote:my-6
                                            prose-img:border prose-img:border-[#E3EAF1] dark:prose-img:border-[#1B2C44] prose-img:my-6
                                            prose-hr:border-[#E3EAF1] dark:prose-hr:border-[#1B2C44] prose-hr:my-8`}
                                        dangerouslySetInnerHTML={{ __html: article?.body || '' }}
                                    />

                                    {/* Inline "BACA JUGA" Box (Classic News Media Callout) */}
                                    {related.length > 0 && (
                                        <div className="my-8 p-4 bg-[#F0F6FC] dark:bg-[#0D1828] border-l-4 border-[#1D5B96] dark:border-[#6BA8DC] border border-[#E3EAF1] dark:border-[#1B2C44]">
                                            <span className="font-mono text-[10px] font-bold text-[#1D5B96] dark:text-[#6BA8DC] uppercase tracking-widest block mb-1">
                                                Baca Juga:
                                            </span>
                                            <a
                                                href={`/blog/${related[0].slug}`}
                                                className="text-[14px] sm:text-[15px] font-bold text-[#0F1A2A] dark:text-white hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors flex items-center justify-between gap-3 group"
                                            >
                                                <span>{related[0].title}</span>
                                                <i className="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform shrink-0"></i>
                                            </a>
                                        </div>
                                    )}
                                </div>

                                {/* Topics / Tags (Topik Terkait) */}
                                {tags.length > 0 && (
                                    <div className="py-6 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                                        <div className="flex flex-wrap items-center gap-2">
                                            <span className="font-mono text-[11px] uppercase tracking-[0.18em] text-[#737373] dark:text-white/40 mr-1 flex items-center gap-1.5">
                                                <i className="fa-solid fa-tags text-[10px]"></i>
                                                Topik Terkait:
                                            </span>
                                            {tags.map((tag, i) => (
                                                <span
                                                    key={i}
                                                    className="px-3 py-1 font-mono text-[10px] uppercase tracking-[0.15em] bg-[#F4F8FC] dark:bg-[#0D1828] text-[#525252] dark:text-white/70 border border-[#E3EAF1] dark:border-[#1B2C44]"
                                                >
                                                    #{tag}
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                {/* Editorial Author Box & Media Statement */}
                                <div className="py-8 border-b border-[#E3EAF1] dark:border-[#1B2C44] bg-[#F9FBFE] dark:bg-[#0B1526] p-6 border border-[#E3EAF1] dark:border-[#1B2C44] mt-8">
                                    <div className="flex items-start gap-4">
                                        <div className="w-12 h-12 bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] flex items-center justify-center font-mono font-bold text-base shrink-0 ring-1 ring-[#E3EAF1] dark:ring-[#24384F]">
                                            {article?.user?.name ? article.user.name.charAt(0).toUpperCase() : 'R'}
                                        </div>
                                        <div className="flex-1">
                                            <div className="flex items-baseline justify-between gap-3 mb-1.5">
                                                <h4 className="text-base font-bold text-[#0F1A2A] dark:text-white">
                                                    {article?.user?.name || 'Redaksi Ryaze'}
                                                </h4>
                                                <span className="font-mono text-[10px] uppercase tracking-[0.18em] text-[#1D5B96] dark:text-[#6BA8DC] font-semibold">
                                                    Tim Editorial
                                                </span>
                                            </div>
                                            <p className="text-xs sm:text-[13px] text-[#525252] dark:text-white/60 leading-relaxed mb-3">
                                                Menulis dan menyajikan panduan teknis, tutorial web development, arsitektur server cloud, serta dokumentasi fitur-fitur platform Ryaze.
                                            </p>
                                            <p className="text-[11px] font-mono text-[#94A3B4] dark:text-white/35 italic">
                                                Seluruh materi telah ditinjau dan teruji di lingkungan hosting server produksi Ryaze.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {/* Bottom Share & Navigation Bar */}
                                <div className="pt-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div className="flex items-center gap-2">
                                        <span className="font-mono text-[11px] uppercase tracking-[0.15em] text-[#737373] dark:text-white/40 mr-1">
                                            Bagikan:
                                        </span>
                                        <button
                                            onClick={copyUrl}
                                            className="h-8 inline-flex items-center justify-center gap-1.5 px-3 font-mono text-[10px] uppercase tracking-[0.15em] text-[#525252] dark:text-white/60 bg-[#F4F8FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] transition-colors"
                                        >
                                            <i className={`fa-solid ${copied ? 'fa-check text-emerald-500' : 'fa-link'} text-[10px]`}></i>
                                            <span>{copied ? 'Tersalin' : 'Salin'}</span>
                                        </button>
                                        <a
                                            href={`https://api.whatsapp.com/send?text=${encodeURIComponent(`${article?.title || ''} - ${pageUrl}`)}`}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="h-8 w-8 inline-flex items-center justify-center text-[#525252] dark:text-white/60 bg-[#F4F8FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-emerald-500 hover:text-emerald-500 transition-colors"
                                        >
                                            <i className="fa-brands fa-whatsapp text-[12px]"></i>
                                        </a>
                                        <a
                                            href={`https://twitter.com/intent/tweet?text=${encodeURIComponent(article?.title || '')}&url=${encodeURIComponent(pageUrl)}`}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="h-8 w-8 inline-flex items-center justify-center text-[#525252] dark:text-white/60 bg-[#F4F8FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] transition-colors"
                                        >
                                            <i className="fa-brands fa-x-twitter text-[11px]"></i>
                                        </a>
                                    </div>

                                    <a
                                        href="/blog"
                                        className="h-8 inline-flex items-center justify-center gap-2 px-4 font-mono text-[10px] uppercase tracking-[0.18em] border border-[#C9D5E2] dark:border-[#24384F] text-[#0F1A2A] dark:text-white hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors"
                                    >
                                        <i className="fa-solid fa-arrow-left text-[9px]"></i>
                                        Indeks Blog
                                    </a>
                                </div>
                            </div>

                            {/* RIGHT COLUMN: NEWS SIDEBAR (4 COLS) */}
                            <aside className="lg:col-span-4 space-y-8 sticky top-20">
                                {/* SIDEBAR WIDGET 1: BERITA TERPOPULER */}
                                <div className="border border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322]">
                                    <div className="flex items-center justify-between px-5 py-3.5 border-b border-[#E3EAF1] dark:border-[#1B2C44] bg-[#F4F8FC] dark:bg-[#0D1828]">
                                        <div className="flex items-center gap-2">
                                            <span className="w-2 h-2 rounded-full bg-[#1D5B96] dark:bg-[#6BA8DC]"></span>
                                            <span className="font-mono text-[11px] font-bold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">
                                                Terpopuler
                                            </span>
                                        </div>
                                        <span className="font-mono text-[9px] uppercase tracking-widest text-[#94A3B4] dark:text-white/30">
                                            Trending
                                        </span>
                                    </div>

                                    <div className="divide-y divide-[#EDF2F7] dark:divide-[#152238]">
                                        {popular.length > 0 ? (
                                            popular.map((item, index) => (
                                                <a
                                                    key={item.id}
                                                    href={`/blog/${item.slug}`}
                                                    className="p-4 flex items-start gap-4 group hover:bg-[#FAFBFD] dark:hover:bg-[#0D192B] transition-colors"
                                                >
                                                    <span className="font-mono text-xl font-black text-[#1D5B96] dark:text-[#6BA8DC] leading-none shrink-0 w-6 pt-0.5">
                                                        {String(index + 1).padStart(2, '0')}
                                                    </span>
                                                    <div className="flex-1">
                                                        {item.category && (
                                                            <span className="font-mono text-[9px] uppercase tracking-wider font-semibold text-[#1D5B96] dark:text-[#6BA8DC] block mb-1">
                                                                {item.category.name}
                                                            </span>
                                                        )}
                                                        <h4 className="text-[13px] font-bold text-[#0F1A2A] dark:text-white group-hover:text-[#1D5B96] dark:group-hover:text-[#6BA8DC] transition-colors line-clamp-2 leading-snug">
                                                            {item.title}
                                                        </h4>
                                                        <p className="font-mono text-[10px] uppercase tracking-wider text-[#94A3B4] dark:text-white/30 mt-2">
                                                            {formatDateShort(item.published_at)}
                                                            {item.views_count !== undefined && ` · ${item.views_count} views`}
                                                        </p>
                                                    </div>
                                                </a>
                                            ))
                                        ) : (
                                            <div className="p-6 text-center text-xs font-mono text-[#94A3B4] dark:text-white/40">
                                                Belum ada blog terpopuler lainnya.
                                            </div>
                                        )}
                                    </div>
                                </div>

                                {/* SIDEBAR WIDGET 2: RUBRIK KATEGORI */}
                                {categories.length > 0 && (
                                    <div className="border border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322]">
                                        <div className="px-5 py-3.5 border-b border-[#E3EAF1] dark:border-[#1B2C44] bg-[#F4F8FC] dark:bg-[#0D1828]">
                                            <span className="font-mono text-[11px] font-bold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">
                                                Kategori Berita
                                            </span>
                                        </div>
                                        <div className="p-4 space-y-1.5">
                                            {categories.map(cat => (
                                                <a
                                                    key={cat.id}
                                                    href={`/blog?category=${cat.slug}`}
                                                    className="flex items-center justify-between px-3 py-2 text-xs font-medium text-[#525252] dark:text-white/70 hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors border border-transparent hover:border-[#E3EAF1] dark:hover:border-[#1B2C44]"
                                                >
                                                    <span className="font-mono text-[11px] uppercase tracking-wider">{cat.name}</span>
                                                    <span className="font-mono text-[10px] text-[#94A3B4] dark:text-white/35 bg-[#EDF2F7] dark:bg-[#152238] px-1.5 py-0.5 rounded-none">
                                                        {cat.articles_count ?? 0}
                                                    </span>
                                                </a>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                {/* SIDEBAR WIDGET 3: KILAS INFO UPDATE */}
                                {latestUpdates.length > 0 && (
                                    <div className="border border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322]">
                                        <div className="flex items-center justify-between px-5 py-3.5 border-b border-[#E3EAF1] dark:border-[#1B2C44] bg-[#F4F8FC] dark:bg-[#0D1828]">
                                            <span className="font-mono text-[11px] font-bold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">
                                                Kilas Update
                                            </span>
                                            <a
                                                href="/updates"
                                                className="font-mono text-[9px] uppercase tracking-wider text-[#1D5B96] dark:text-[#6BA8DC] hover:underline"
                                            >
                                                Semua &rarr;
                                            </a>
                                        </div>
                                        <div className="divide-y divide-[#EDF2F7] dark:divide-[#152238]">
                                            {latestUpdates.map(u => (
                                                <a
                                                    key={u.id}
                                                    href={`/updates/${u.slug}`}
                                                    className="p-4 block group hover:bg-[#FAFBFD] dark:hover:bg-[#0D192B] transition-colors"
                                                >
                                                    <div className="flex items-center gap-2 mb-1.5 font-mono text-[9px] uppercase tracking-wider">
                                                        <span className="text-[#1D5B96] dark:text-[#6BA8DC] font-semibold">{u.category}</span>
                                                        <span className="text-[#94A3B4] dark:text-white/30">&middot;</span>
                                                        <span className="text-[#94A3B4] dark:text-white/40">{formatDateShort(u.published_at)}</span>
                                                    </div>
                                                    <h5 className="text-xs font-bold text-[#0F1A2A] dark:text-white group-hover:text-[#1D5B96] dark:group-hover:text-[#6BA8DC] transition-colors line-clamp-2 leading-snug">
                                                        {u.title}
                                                    </h5>
                                                </a>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                {/* SIDEBAR WIDGET 4: PROMO / BANNER REDAKSI */}
                                <div className="border border-[#E3EAF1] dark:border-[#1B2C44] bg-[#12233A] dark:bg-[#0D1828] p-6 text-white relative overflow-hidden">
                                    <div className="absolute top-0 right-0 w-28 h-28 bg-[#1D5B96]/20 rounded-full blur-2xl pointer-events-none"></div>
                                    <span className="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#7FB4E2] block mb-2">
                                        Platform Hosting
                                    </span>
                                    <h4 className="text-base font-black text-white leading-snug mb-3">
                                        Deploy Aplikasi Anda dalam Hitungan Menit.
                                    </h4>
                                    <p className="text-xs text-white/70 leading-relaxed mb-5 font-sans">
                                        Auto-deploy dari Git, SSL Let's Encrypt gratis, MySQL bawaan, web terminal &amp; panel kontrol lengkap.
                                    </p>
                                    <a
                                        href="/#pricing"
                                        className="inline-flex items-center justify-center gap-2 w-full py-2.5 bg-white text-[#0F1A2A] text-xs font-semibold hover:bg-[#6BA8DC] hover:text-[#0F1A2A] transition-colors"
                                    >
                                        Lihat Paket Hosting
                                        <i className="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </aside>
                        </div>
                    </div>
                </main>

                {/* BOTTOM SECTION: BERITA & PANDUAN TERKAIT */}
                {related && related.length > 0 && (
                    <section className="bg-[#F4F8FC] dark:bg-[#0B1526] border-t border-[#E3EAF1] dark:border-[#1B2C44]">
                        <div className="max-w-6xl mx-auto px-6 py-16 lg:py-24">
                            <div className="flex items-end justify-between mb-10 gap-6" data-reveal>
                                <SectionHead
                                    num="02"
                                    label="Rekomendasi"
                                    title="Berita &amp; Panduan Terkait"
                                    sub="Wawasan dan artikel lain seputar web development &amp; hosting."
                                />
                                <a
                                    href="/blog"
                                    className="hidden sm:inline-flex items-center gap-2 font-mono text-[11px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/45 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors shrink-0 pb-2"
                                >
                                    Semua Berita <i className="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>

                            <div
                                className={`grid gap-6 ${
                                    related.length === 1
                                        ? 'grid-cols-1 md:max-w-md'
                                        : related.length === 2
                                        ? 'grid-cols-1 md:grid-cols-2'
                                        : 'grid-cols-1 md:grid-cols-3'
                                }`}
                                data-reveal
                            >
                                {related.map(a => (
                                    <a
                                        key={a.id}
                                        href={`/blog/${a.slug}`}
                                        className="bg-white dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] group flex flex-col p-6 transition-colors"
                                    >
                                        {a.cover_image ? (
                                            <div className="h-40 overflow-hidden bg-[#E8F0F9] dark:bg-[#16304D] mb-5 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                                                <img
                                                    src={a.cover_image}
                                                    alt={a.title}
                                                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                />
                                            </div>
                                        ) : (
                                            <div className="h-40 bg-[#E8F0F9] dark:bg-[#16304D] mb-5 border-b border-[#E3EAF1] dark:border-[#1B2C44] flex items-center justify-center text-[#1D5B96]/20 dark:text-white/12">
                                                <i className="fa-solid fa-newspaper text-3xl"></i>
                                            </div>
                                        )}
                                        {a.category && (
                                            <span className="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC]">
                                                {a.category.name}
                                            </span>
                                        )}
                                        <h3 className="text-[15px] font-bold text-[#0F1A2A] dark:text-white mt-2 mb-2 group-hover:text-[#1D5B96] dark:group-hover:text-[#6BA8DC] transition-colors line-clamp-2 leading-snug">
                                            {a.title}
                                        </h3>
                                        <div className="flex items-center gap-2.5 font-mono text-[10px] uppercase tracking-[0.15em] text-[#94A3B4] dark:text-white/25 pt-4 mt-auto border-t border-[#EDF2F7] dark:border-[#152238]">
                                            <span>{formatDateShort(a.published_at)}</span>
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

                            <div className="mt-8 text-center sm:hidden">
                                <a
                                    href="/blog"
                                    className="inline-flex items-center gap-2 font-mono text-[11px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/45 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors"
                                >
                                    Semua Berita <i className="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </section>
                )}
            </PublicLayout>
        </>
    );
}
