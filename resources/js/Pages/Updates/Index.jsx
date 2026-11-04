import PublicLayout from '../../Layouts/PublicLayout';
import { Head } from '@inertiajs/react';
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

export default function Index({ updates, categories, currentCategory, seoTitle, seoDescription, canonical }) {
    useReveal();
    const items = updates?.data || [];
    const pagination = updates;
    const totalUpdates = pagination?.total ?? items.length;

    const activeCat = categories?.find(c => c.key === currentCategory) || null;

    // Canonical mengikuti kategori & nomor halaman agar paginasi tidak
    // duplikat dengan halaman pertama.
    const page = pagination?.current_page ?? 1;
    const params = new URLSearchParams();
    if (currentCategory) params.set('kategori', currentCategory);
    if (page > 1) params.set('page', page);
    const qs = params.toString();
    const pageUrl = canonical || `https://ryaze.my.id/updates${qs ? `?${qs}` : ''}`;
    const pageTitle = seoTitle || (activeCat ? `${activeCat.label} - Info Update | Ryaze` : 'Info Update & Pengumuman | Ryaze');
    const description = seoDescription || 'Kabar terbaru, rilis fitur, status sistem, dan pengumuman pemeliharaan server platform Ryaze.';

    const breadcrumbJsonLd = {
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: [
            { '@type': 'ListItem', position: 1, name: 'Beranda', item: 'https://ryaze.my.id/' },
            { '@type': 'ListItem', position: 2, name: 'Info Update', item: 'https://ryaze.my.id/updates' },
        ],
    };

    return (
        <>
            <Head title={pageTitle}>
                <meta name="description" content={description} />
                <meta property="og:type" content="website" />
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
                <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbJsonLd) }} />
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
                                    label="Changelog & Status"
                                    as="h1"
                                    title={activeCat ? `Update: ${activeCat.label}` : 'Kabar & Info Update'}
                                    sub={description}
                                />
                            </div>
                            <div className="lg:col-span-4" data-reveal>
                                <div className="border border-[#E3EAF1] dark:border-[#1B2C44] bg-[#F0F6FC] dark:bg-[#0D1828]">
                                    <div className="flex items-center justify-between px-5 py-3 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                                        <span className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">system.feed</span>
                                        <span className="flex items-center gap-1.5 font-mono text-[10px] tracking-widest text-emerald-600 dark:text-emerald-400">
                                            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> LIVE
                                        </span>
                                    </div>
                                    <dl>
                                        {[
                                            { k: 'TOTAL UPDATE', v: totalUpdates },
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

                {/* FILTER KATEGORI */}
                <section className="bg-white dark:bg-[#0A1322] border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                    <div className="max-w-6xl mx-auto px-6 py-5">
                        <div className="flex flex-wrap items-center gap-1.5">
                            <a
                                href="/updates"
                                className={`h-9 inline-flex items-center justify-center px-3.5 font-mono text-[10px] uppercase tracking-[0.15em] border transition-colors ${
                                    !currentCategory
                                        ? 'bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] border-[#12233A] dark:border-white font-semibold'
                                        : 'bg-white dark:bg-[#0D1828] text-[#525252] dark:text-white/50 border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC]'
                                }`}
                            >
                                Semua
                            </a>
                            {(categories || []).map(cat => (
                                <a
                                    key={cat.key}
                                    href={`/updates?kategori=${cat.key}`}
                                    className={`h-9 inline-flex items-center justify-center gap-1.5 px-3.5 font-mono text-[10px] uppercase tracking-[0.15em] border transition-colors ${
                                        currentCategory === cat.key
                                            ? 'bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] border-[#12233A] dark:border-white font-semibold'
                                            : 'bg-white dark:bg-[#0D1828] text-[#525252] dark:text-white/50 border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC]'
                                    }`}
                                >
                                    <i className={`fa-solid ${cat.icon} text-[9px]`}></i>
                                    <span>{cat.label}</span>
                                    {cat.count !== undefined && <span className="opacity-50 font-mono text-[9px]">{cat.count}</span>}
                                </a>
                            ))}
                        </div>
                    </div>
                </section>

                {/* DAFTAR UPDATE */}
                <section className="bg-[#F4F8FC] dark:bg-[#0B1526]">
                    <div className="max-w-6xl mx-auto px-6 py-14 lg:py-20">
                        {items.length > 0 ? (
                            <div
                                className={`grid gap-6 ${
                                    items.length === 1
                                        ? 'grid-cols-1 md:max-w-xl'
                                        : items.length === 2
                                        ? 'grid-cols-1 md:grid-cols-2'
                                        : 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
                                }`}
                                data-reveal
                            >
                                {items.map(item => {
                                    const priority = PRIORITY_STYLE[item.priority] || PRIORITY_STYLE.normal;
                                    const cat = (categories || []).find(c => c.key === item.category);
                                    return (
                                        <a
                                            key={item.id}
                                            href={`/updates/${item.slug}`}
                                            className="group relative bg-white dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] p-7 flex flex-col transition-colors hover:bg-[#FAFBFD] dark:hover:bg-[#0D192B]"
                                        >
                                            {priority.rail && (
                                                <span className={`absolute left-0 top-0 bottom-0 w-1 ${priority.rail}`} aria-hidden="true"></span>
                                            )}

                                            {/* Header Tags */}
                                            <div className="flex flex-wrap items-center gap-2 mb-4">
                                                {cat && (
                                                    <span className="inline-flex items-center gap-1.5 px-2.5 py-1 font-mono text-[10px] uppercase tracking-[0.18em] font-semibold text-[#1D5B96] dark:text-[#6BA8DC] bg-[#F0F6FC] dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44]">
                                                        <i className={`fa-solid ${cat.icon} text-[8px]`}></i>
                                                        {cat.label}
                                                    </span>
                                                )}
                                                <span className={`px-2.5 py-1 font-mono text-[9px] uppercase tracking-[0.2em] font-semibold border ${priority.badge}`}>
                                                    {priority.label}
                                                </span>
                                                {item.version && (
                                                    <span className="font-mono text-[10px] uppercase tracking-[0.12em] px-2 py-0.5 bg-[#F4F8FC] dark:bg-[#0D1828] text-[#737373] dark:text-white/40 border border-[#E3EAF1] dark:border-[#1B2C44]">
                                                        {item.version}
                                                    </span>
                                                )}
                                            </div>

                                            {/* Title */}
                                            <h2 className="text-lg font-bold text-[#0F1A2A] dark:text-white leading-snug group-hover:text-[#1D5B96] dark:group-hover:text-[#6BA8DC] transition-colors">
                                                {item.title}
                                            </h2>

                                            {/* Excerpt */}
                                            <p className="mt-3 text-sm text-[#525252] dark:text-white/55 leading-relaxed flex-1 line-clamp-3">
                                                {item.excerpt}
                                            </p>

                                            {/* Footer Info */}
                                            <div className="mt-6 pt-5 border-t border-[#EDF2F7] dark:border-[#152238] flex items-center justify-between">
                                                <span className="font-mono text-[10px] uppercase tracking-[0.15em] text-[#94A3B4] dark:text-white/30">
                                                    {item.published_at}
                                                </span>
                                                <span className="font-mono text-[11px] uppercase tracking-[0.15em] font-semibold text-[#1D5B96] dark:text-[#6BA8DC] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1.5">
                                                    Detail <i className="fa-solid fa-arrow-right text-[9px]"></i>
                                                </span>
                                            </div>
                                        </a>
                                    );
                                })}
                            </div>
                        ) : (
                            <div className="border border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322] py-20 text-center" data-reveal>
                                <i className="fa-regular fa-bell-slash text-3xl text-[#94A3B4] dark:text-white/20 mb-4 block"></i>
                                <p className="font-mono text-[12px] uppercase tracking-[0.2em] font-bold text-[#0F1A2A] dark:text-white">
                                    Belum ada info update
                                </p>
                                <p className="mt-1 text-sm text-[#525252] dark:text-white/50 max-w-sm mx-auto">
                                    {currentCategory
                                        ? 'Tidak ada pembaruan pada kategori ini. Silakan cek kategori lain.'
                                        : 'Semua informasi rilis dan pemeliharaan akan tampil di sini.'}
                                </p>
                            </div>
                        )}

                        {/* PAGINATION */}
                        {pagination && pagination.last_page > 1 && (
                            <div className="mt-12 flex flex-wrap items-center justify-center gap-2" data-reveal>
                                {pagination.prev_page_url && (
                                    <a
                                        href={pagination.prev_page_url}
                                        className="px-4 py-2.5 font-mono text-[10px] uppercase tracking-[0.15em] text-[#525252] dark:text-white/50 border border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors"
                                    >
                                        &larr; Prev
                                    </a>
                                )}
                                {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map(page => (
                                    <a
                                        key={page}
                                        href={`${pagination.path}?kategori=${currentCategory || ''}&page=${page}`}
                                        className={`w-10 h-10 flex items-center justify-center font-mono text-xs font-semibold transition-colors ${
                                            page === pagination.current_page
                                                ? 'bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A]'
                                                : 'bg-white dark:bg-[#0A1322] text-[#525252] dark:text-white/50 border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC]'
                                        }`}
                                    >
                                        {page}
                                    </a>
                                ))}
                                {pagination.next_page_url && (
                                    <a
                                        href={pagination.next_page_url}
                                        className="px-4 py-2.5 font-mono text-[10px] uppercase tracking-[0.15em] text-[#525252] dark:text-white/50 border border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors"
                                    >
                                        Next &rarr;
                                    </a>
                                )}
                            </div>
                        )}
                    </div>
                </section>
            </PublicLayout>
        </>
    );
}
