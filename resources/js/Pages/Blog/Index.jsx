import PublicLayout from '../../Layouts/PublicLayout';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { router } from '@inertiajs/react';
import { useReveal, RevealStyle, SectionHead } from '../../Components/PublicSection';

function formatDate(val) {
    if (!val) return '';
    const d = new Date(val);
    if (isNaN(d.getTime())) return val;
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function breadcrumbJsonLd(currentCategory) {
    const trail = [
        { '@type': 'ListItem', position: 1, name: 'Beranda', item: 'https://ryaze.my.id/' },
        { '@type': 'ListItem', position: 2, name: 'Blog', item: 'https://ryaze.my.id/blog' },
    ];
    if (currentCategory) {
        trail.push({ '@type': 'ListItem', position: 3, name: currentCategory.name, item: `https://ryaze.my.id/blog/kategori/${currentCategory.slug}` });
    }
    return { '@context': 'https://schema.org', '@type': 'BreadcrumbList', itemListElement: trail };
}

export default function Index({ articles, categories, featured, currentCategory, seoTitle, seoDescription, canonical, robots }) {
    useReveal();
    const [search, setSearch] = useState('');

    function handleSearch(e) {
        e.preventDefault();
        const params = {};
        if (search.trim()) params.search = search.trim();
        if (currentCategory) params.category = currentCategory.slug;
        router.get('/blog', params, { preserveState: true, replace: true });
    }

    const items = articles?.data || [];
    const pagination = articles;
    const totalArticles = pagination?.total ?? items.length;

    // Canonical per-URL: ikut kategori & nomor halaman, tanpa parameter search.
    const page = pagination?.current_page ?? 1;
    const basePath = currentCategory ? `https://ryaze.my.id/blog/kategori/${currentCategory.slug}` : 'https://ryaze.my.id/blog';
    const pageUrl = canonical || (page > 1 ? `${basePath}?page=${page}` : basePath);
    const description = seoDescription || currentCategory?.description || 'Tutorial, tips, dan berita seputar web development, hosting, dan teknologi terbaru.';
    const title = seoTitle || (currentCategory ? `${currentCategory.name} - Blog | Ryaze` : 'Artikel & Insights - Blog | Ryaze');

    return (
        <>
            <Head title={title}>
                <meta name="description" content={description} />
                {robots && <meta head-key="robots" name="robots" content={robots} />}
                <meta property="og:type" content="website" />
                <meta property="og:url" content={pageUrl} />
                <meta property="og:site_name" content="Ryaze" />
                <meta property="og:locale" content="id_ID" />
                <meta property="og:title" content={title} />
                <meta property="og:description" content={description} />
                <meta property="og:image" content="https://ryaze.my.id/og-image.png" />
                <meta property="twitter:card" content="summary_large_image" />
                <meta property="twitter:url" content={pageUrl} />
                <meta property="twitter:title" content={title} />
                <meta property="twitter:description" content={description} />
                <link rel="canonical" href={pageUrl} />
                <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbJsonLd(currentCategory)) }} />
            </Head>
            <PublicLayout>
                <RevealStyle />

                {/* HERO */}
                <section className="bg-white dark:bg-[#0A1322] border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                    <div className="max-w-6xl mx-auto px-6 pt-28 pb-14 lg:pt-32 lg:pb-16">
                        <div className="grid lg:grid-cols-12 gap-10 lg:gap-14 items-end">
                            <div className="lg:col-span-8">
                                <SectionHead
                                    num="01"
                                    label="Blog"
                                    as="h1"
                                    title={currentCategory ? currentCategory.name : 'Artikel & Insights'}
                                    sub={description}
                                />
                            </div>
                            <div className="lg:col-span-4" data-reveal>
                                <div className="border border-[#E3EAF1] dark:border-[#1B2C44] bg-[#F0F6FC] dark:bg-[#0D1828]">
                                    <div className="flex items-center justify-between px-5 py-3 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                                        <span className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">arsip.index</span>
                                        <span className="flex items-center gap-1.5 font-mono text-[10px] tracking-widest text-emerald-600 dark:text-emerald-400">
                                            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> LIVE
                                        </span>
                                    </div>
                                    <dl>
                                        {[
                                            { k: 'TOTAL ARTIKEL', v: totalArticles },
                                            { k: 'KATEGORI', v: categories?.length ?? 0 },
                                            { k: 'HALAMAN', v: `${pagination?.current_page ?? 1}/${pagination?.last_page ?? 1}` },
                                        ].map((r, i) => (
                                            <div key={r.k} className={`flex items-baseline justify-between gap-4 px-5 py-3.5 ${i < 2 ? 'border-b border-[#E3EAF1] dark:border-[#1B2C44]' : ''}`}>
                                                <dt className="font-mono text-[10px] uppercase tracking-[0.18em] text-[#737373] dark:text-white/35 shrink-0">{r.k}</dt>
                                                <dd className="text-[13px] font-medium text-[#0F1A2A] dark:text-white text-right font-mono">{r.v}</dd>
                                            </div>
                                        ))}
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* SEARCH + CATEGORIES */}
                <section className="bg-white dark:bg-[#0A1322] border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                    <div className="max-w-6xl mx-auto px-6 py-5">
                        <div className="flex flex-col lg:flex-row lg:items-center gap-5">
                            <form onSubmit={handleSearch} className="w-full lg:max-w-sm">
                                <div className="flex items-stretch h-9">
                                    <div className="relative flex-1">
                                        <div className="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                            <i className="fa-solid fa-magnifying-glass text-[#94A3B4] dark:text-white/25 text-[11px]"></i>
                                        </div>
                                        <input
                                            type="text"
                                            value={search}
                                            onChange={e => setSearch(e.target.value)}
                                            placeholder="Cari artikel..."
                                            className="w-full h-full pl-9 pr-3 bg-white dark:bg-[#0D1828] border border-r-0 border-[#C9D5E2] dark:border-[#24384F] font-mono text-xs text-[#0F1A2A] dark:text-white placeholder-[#94A3B4] dark:placeholder-white/25 focus:outline-none focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] transition-colors"
                                        />
                                    </div>
                                    <button
                                        type="submit"
                                        aria-label="Cari"
                                        className="h-full px-3.5 bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] border border-l-0 border-[#12233A] dark:border-white hover:bg-[#1D5B96] dark:hover:bg-[#6BA8DC] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] flex items-center justify-center transition-colors shrink-0"
                                    >
                                        <i className="fa-solid fa-arrow-right text-[11px]"></i>
                                    </button>
                                </div>
                            </form>

                            <div className="flex flex-wrap items-center gap-1.5">
                                <a
                                    href="/blog"
                                    className={`h-9 inline-flex items-center justify-center px-3.5 font-mono text-[10px] uppercase tracking-[0.15em] border transition-colors ${
                                        !currentCategory
                                            ? 'bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] border-[#12233A] dark:border-white font-semibold'
                                            : 'bg-white dark:bg-[#0D1828] text-[#525252] dark:text-white/50 border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC]'
                                    }`}
                                >
                                    Semua
                                </a>
                                {categories.map(cat => (
                                    <a
                                        key={cat.id}
                                        href={`/blog?category=${cat.slug}`}
                                        className={`h-9 inline-flex items-center justify-center gap-1.5 px-3.5 font-mono text-[10px] uppercase tracking-[0.15em] border transition-colors ${
                                            currentCategory?.id === cat.id
                                                ? 'bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] border-[#12233A] dark:border-white font-semibold'
                                                : 'bg-white dark:bg-[#0D1828] text-[#525252] dark:text-white/50 border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC]'
                                        }`}
                                    >
                                        <span>{cat.name}</span>
                                        {cat.articles_count !== undefined && <span className="opacity-50 font-mono text-[9px]">{cat.articles_count}</span>}
                                    </a>
                                ))}
                            </div>
                        </div>
                    </div>
                </section>

                {/* FEATURED */}
                {featured && !currentCategory && (
                    <section className="bg-[#F4F8FC] dark:bg-[#0B1526] border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                        <div className="max-w-6xl mx-auto px-6 py-14 lg:py-16">
                            <div className="flex items-center gap-3 font-mono text-[11px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40 mb-5" data-reveal>
                                <span className="text-[#1D5B96] dark:text-[#6BA8DC]">02</span>
                                <span className="w-6 h-px bg-[#C9D5E2] dark:bg-[#24384F]"></span>
                                <span>Pilihan Editor</span>
                            </div>
                            <a
                                href={`/blog/${featured.slug}`}
                                className={`group border border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322] ${featured.cover_image ? 'grid grid-cols-1 lg:grid-cols-2' : 'block'}`}
                                data-reveal
                            >
                                {featured.cover_image && (
                                    <div className="h-64 lg:h-full min-h-[260px] overflow-hidden bg-[#E8F0F9] dark:bg-[#16304D] border-b lg:border-b-0 lg:border-r border-[#E3EAF1] dark:border-[#1B2C44]">
                                        <img src={featured.cover_image} alt={featured.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                    </div>
                                )}
                                <div className={`${featured.cover_image ? 'p-8 lg:p-10' : 'p-10 lg:p-14'} flex flex-col justify-center`}>
                                    <div className="flex items-center gap-3 mb-4">
                                        {featured.category && <span className="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC]">{featured.category.name}</span>}
                                        <span className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#94A3B4] dark:text-white/25">Featured</span>
                                    </div>
                                    <h2 className={`${featured.cover_image ? 'text-2xl lg:text-3xl' : 'text-3xl lg:text-4xl max-w-3xl'} font-black text-[#0F1A2A] dark:text-white tracking-tight leading-tight mb-3 group-hover:text-[#1D5B96] dark:group-hover:text-[#6BA8DC] transition-colors line-clamp-3`}>{featured.title}</h2>
                                    {featured.excerpt && (
                                        <p className={`text-[#525252] dark:text-white/50 text-[15px] leading-relaxed mb-6 line-clamp-3 ${!featured.cover_image ? 'max-w-2xl' : ''}`}>{featured.excerpt}</p>
                                    )}
                                    <div className="flex flex-wrap items-center gap-2.5 font-mono text-[10px] uppercase tracking-[0.15em] text-[#94A3B4] dark:text-white/30">
                                        {featured.user && <span>{featured.user.name}</span>}
                                        {featured.user && featured.published_at && <span>&middot;</span>}
                                        {featured.published_at && <span>{formatDate(featured.published_at)}</span>}
                                        {featured.reading_time ? (
                                            <>
                                                <span>&middot;</span>
                                                <span>{featured.reading_time} mnt baca</span>
                                            </>
                                        ) : null}
                                    </div>
                                </div>
                            </a>
                        </div>
                    </section>
                )}

                {/* ARTICLES GRID */}
                <section className="bg-white dark:bg-[#0A1322]">
                    <div className="max-w-6xl mx-auto px-6 py-14 lg:py-20">
                        <div className="mb-10" data-reveal>
                            <SectionHead
                                num={featured && !currentCategory ? '03' : '02'}
                                label="Arsip"
                                title={currentCategory ? 'Artikel kategori ini' : 'Semua Artikel'}
                            />
                        </div>

                        {items.length > 0 ? (
                            <div
                                className={`grid gap-6 ${
                                    items.length === 1
                                        ? 'grid-cols-1 md:max-w-md'
                                        : items.length === 2
                                        ? 'grid-cols-1 md:grid-cols-2'
                                        : 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
                                }`}
                                data-reveal
                            >
                                {items.map(a => (
                                    <a
                                        key={a.id}
                                        href={`/blog/${a.slug}`}
                                        className="bg-white dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] group flex flex-col transition-colors"
                                    >
                                        {a.cover_image ? (
                                            <div className="h-44 overflow-hidden bg-[#E8F0F9] dark:bg-[#16304D] border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                                                <img src={a.cover_image} alt={a.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                            </div>
                                        ) : (
                                            <div className="h-44 bg-[#E8F0F9] dark:bg-[#16304D] border-b border-[#E3EAF1] dark:border-[#1B2C44] flex items-center justify-center text-[#1D5B96]/20 dark:text-white/12">
                                                <i className="fa-solid fa-newspaper text-4xl"></i>
                                            </div>
                                        )}
                                        <div className="p-6 flex flex-col flex-1">
                                            {a.category && <span className="font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC]">{a.category.name}</span>}
                                            <h3 className="text-[15px] font-bold text-[#0F1A2A] dark:text-white mt-2 mb-2 group-hover:text-[#1D5B96] dark:group-hover:text-[#6BA8DC] transition-colors line-clamp-2 leading-snug">{a.title}</h3>
                                            <p className="text-[13px] text-[#525252] dark:text-white/45 line-clamp-2 mb-5 flex-1">{a.excerpt || ''}</p>
                                            <div className="flex items-center gap-2.5 font-mono text-[10px] uppercase tracking-[0.15em] text-[#94A3B4] dark:text-white/25 pt-4 border-t border-[#EDF2F7] dark:border-[#152238]">
                                                {a.user && <span>{a.user.name}</span>}
                                                {a.user && a.published_at && <span>&middot;</span>}
                                                {a.published_at && <span>{formatDate(a.published_at)}</span>}
                                                {a.reading_time ? (
                                                    <>
                                                        <span>&middot;</span>
                                                        <span>{a.reading_time} mnt</span>
                                                    </>
                                                ) : null}
                                            </div>
                                        </div>
                                    </a>
                                ))}
                            </div>
                        ) : (
                            <div className="border border-[#E3EAF1] dark:border-[#1B2C44] py-20 text-center" data-reveal>
                                <i className="fa-solid fa-newspaper text-3xl text-[#94A3B4] dark:text-white/15 mb-4 block"></i>
                                <p className="font-mono text-[11px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Belum ada artikel</p>
                            </div>
                        )}
                    </div>
                </section>

                {/* PAGINATION */}
                {pagination && pagination.last_page > 1 && (
                    <section className="bg-white dark:bg-[#0A1322] border-t border-[#E3EAF1] dark:border-[#1B2C44]">
                        <div className="max-w-6xl mx-auto px-6 py-8 flex flex-wrap items-center justify-center gap-2">
                            {pagination.prev_page_url && (
                                <a href={pagination.prev_page_url} className="px-4 py-2.5 font-mono text-[10px] uppercase tracking-[0.15em] text-[#525252] dark:text-white/50 border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">
                                    &larr; Prev
                                </a>
                            )}
                            {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map(page => (
                                <a
                                    key={page}
                                    href={`${pagination.path}?page=${page}`}
                                    className={`w-10 h-10 flex items-center justify-center font-mono text-xs font-semibold transition-colors ${
                                        page === pagination.current_page
                                            ? 'bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A]'
                                            : 'text-[#525252] dark:text-white/50 border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC]'
                                    }`}
                                >
                                    {page}
                                </a>
                            ))}
                            {pagination.next_page_url && (
                                <a href={pagination.next_page_url} className="px-4 py-2.5 font-mono text-[10px] uppercase tracking-[0.15em] text-[#525252] dark:text-white/50 border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">
                                    Next &rarr;
                                </a>
                            )}
                        </div>
                    </section>
                )}
            </PublicLayout>
        </>
    );
}
