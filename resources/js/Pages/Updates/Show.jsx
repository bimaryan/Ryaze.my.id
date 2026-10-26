import PublicLayout from '../../Layouts/PublicLayout';
import { Head, usePage } from '@inertiajs/react';
import { useEffect } from 'react';

/* Lihat catatan di Updates/Index.jsx: kelas warna ditulis literal di sini
   supaya Tailwind memindainya saat build. */
const PRIORITY_STYLE = {
    critical: { label: 'Darurat', chip: 'bg-rose-500 text-white', rail: 'bg-rose-500' },
    high: { label: 'Mendesak', chip: 'bg-amber-400 text-slate-900', rail: 'bg-amber-400' },
    normal: { label: 'Penting', chip: 'bg-[#7c3aed] text-white', rail: '' },
    low: { label: 'Biasanya', chip: 'bg-[#f5f0ff] dark:bg-[#1a1025] text-[#7c3aed] dark:text-white/70', rail: '' },
};

export default function Show({ update, related, categories }) {
    const priority = PRIORITY_STYLE[update?.priority] || PRIORITY_STYLE.normal;
    const cat = (categories || []).find(c => c.key === update?.category) || null;

    useEffect(() => {
        if (update?.title) document.title = `${update.title} - Info Update Ryaze`;
    }, [update]);

    const pageUrl = typeof window !== 'undefined' ? window.location.href : '';

    return (
        <PublicLayout title={`${update?.title} - Info Update Ryaze`} description={update?.excerpt || ''}>
            <Head>
                <meta property="og:type" content="article" />
                <meta property="og:title" content={update?.title} />
                <meta property="og:description" content={update?.excerpt || ''} />
                <meta property="og:url" content={pageUrl} />
                <meta property="og:site_name" content="Ryaze" />
                <meta name="twitter:card" content="summary_large_image" />
                <meta name="twitter:title" content={update?.title} />
                <meta name="twitter:description" content={update?.excerpt || ''} />
                <link rel="canonical" href={pageUrl} />
            </Head>

            <article className="bg-white dark:bg-[#0d0d18]">
                <div className="max-w-3xl mx-auto px-6 pt-28 pb-16 lg:pt-36 lg:pb-20">
                    {/* Breadcrumb */}
                    <nav className="flex items-center gap-2 text-xs text-[#999] dark:text-white/40 mb-8">
                        <a href="/" className="hover:text-[#7c3aed] transition-colors">Home</a>
                        <i className="fa-solid fa-chevron-right text-[8px]"></i>
                        <a href="/updates" className="hover:text-[#7c3aed] transition-colors">Info Update</a>
                        {cat && (
                            <>
                                <i className="fa-solid fa-chevron-right text-[8px]"></i>
                                <a href={`/updates?kategori=${cat.key}`} className="hover:text-[#7c3aed] transition-colors">{cat.label}</a>
                            </>
                        )}
                    </nav>

                    <div className="relative">
                        {priority.rail && <span className={`absolute -left-6 top-1 bottom-1 w-1 ${priority.rail}`} aria-hidden="true"></span>}

                        <div className="flex flex-wrap items-center gap-2 mb-5">
                            {cat && (
                                <span className="px-2.5 py-0.5 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] dark:text-white/80 text-[10px] font-bold tracking-wider uppercase inline-flex items-center gap-1.5">
                                    <i className={`fa-solid ${cat.icon} text-[8px]`}></i>
                                    {cat.label}
                                </span>
                            )}
                            <span className={`px-2.5 py-0.5 text-[10px] font-bold tracking-wider uppercase ${priority.chip}`}>
                                {priority.label}
                            </span>
                            {update?.version && (
                                <span className="px-2.5 py-0.5 text-[10px] font-mono font-bold bg-[#f5f5f5] dark:bg-[#1a1a2e] text-[#666] dark:text-white/60">
                                    {update.version}
                                </span>
                            )}
                        </div>

                        <h1 className="text-[clamp(1.8rem,4vw,2.8rem)] font-black text-[#7c3aed] dark:text-white leading-[1.15] tracking-[-0.03em] mb-6">
                            {update?.title}
                        </h1>

                        <div className="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-[#999] dark:text-white/40 pb-8 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <span><i className="fa-regular fa-calendar mr-1.5"></i>{update?.published_at_long}</span>
                            {update?.author && (
                                <span className="inline-flex items-center gap-1.5">
                                    <span className="w-5 h-5 bg-[#7c3aed] text-white text-[9px] font-bold flex items-center justify-center">
                                        {update.author.charAt(0).toUpperCase()}
                                    </span>
                                    {update.author}
                                </span>
                            )}
                        </div>

                        {/* Deskripsi disimpan sebagai teks polos, bukan HTML. */}
                        <p className="mt-8 text-[15px] text-[#444] dark:text-white/75 leading-[1.9] whitespace-pre-line">
                            {update?.description}
                        </p>
                    </div>
                </div>
            </article>

            {/* UPDATE LAIN */}
            {related && related.length > 0 && (
                <section className="bg-[#fafafa] dark:bg-[#0a0a14] border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="max-w-6xl mx-auto px-6 py-14 lg:py-16">
                        <h2 className="text-sm font-bold text-[#7c3aed] dark:text-white uppercase tracking-[0.2em] mb-8">
                            Info Update lain
                        </h2>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-px bg-[#e5e5e5] dark:bg-[#1a1a2e] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            {related.map(item => {
                                const itemCat = (categories || []).find(c => c.key === item.category);
                                return (
                                    <a
                                        key={item.id}
                                        href={`/updates/${item.slug}`}
                                        className="group bg-white dark:bg-[#0d0d18] p-6 transition-colors hover:bg-[#fafafa] dark:hover:bg-[#0f0f1c]"
                                    >
                                        <div className="flex items-center gap-2 mb-3">
                                            {itemCat && (
                                                <span className="px-2 py-0.5 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] dark:text-white/80 text-[10px] font-bold tracking-wider uppercase inline-flex items-center gap-1.5">
                                                    <i className={`fa-solid ${itemCat.icon} text-[8px]`}></i>
                                                    {itemCat.label}
                                                </span>
                                            )}
                                        </div>
                                        <h3 className="text-[15px] font-bold text-[#7c3aed] dark:text-white leading-snug group-hover:text-[#6d28d9] dark:group-hover:text-white transition-colors line-clamp-2">
                                            {item.title}
                                        </h3>
                                        <p className="mt-3 text-xs text-[#999] dark:text-white/40">{item.published_at}</p>
                                    </a>
                                );
                            })}
                        </div>

                        <div className="mt-8">
                            <a href="/updates" className="inline-flex items-center gap-2 text-sm font-semibold text-[#7c3aed] dark:text-white/70 hover:text-[#6d28d9] dark:hover:text-white transition-colors">
                                <i className="fa-solid fa-arrow-left text-[10px]"></i>
                                Semua Info Update
                            </a>
                        </div>
                    </div>
                </section>
            )}
        </PublicLayout>
    );
}