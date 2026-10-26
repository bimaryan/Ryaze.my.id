import PublicLayout from '../../Layouts/PublicLayout';

/* Peta warna hard-coded di sini (bukan dari PHP) supaya Tailwind memindainya
   saat build. Kalau kelasnya datang dari backend, Tailwind tidak pernah
   melihatnya dan warnanya hilang di produksi. */
const PRIORITY_STYLE = {
    critical: { label: 'Darurat', chip: 'bg-rose-500 text-white', rail: 'bg-rose-500' },
    high: { label: 'Mendesak', chip: 'bg-amber-400 text-slate-900', rail: 'bg-amber-400' },
    normal: { label: 'Penting', chip: 'bg-[#7c3aed] text-white', rail: '' },
    low: { label: 'Biasanya', chip: 'bg-[#f5f0ff] dark:bg-[#1a1025] text-[#7c3aed] dark:text-white/70', rail: '' },
};

export default function Index({ updates, categories, currentCategory }) {
    const items = updates?.data || [];

    const activeCat = categories?.find(c => c.key === currentCategory) || null;

    return (
        <PublicLayout
            title={activeCat ? `${activeCat.label} - Info Update` : 'Info Update - Ryaze'}
            description="Kabar terbaru, fitur baru, dan informasi pemeliharaan Ryaze."
        >
            {/* HERO */}
            <section className="relative bg-white dark:bg-[#0a0a14] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                <div className="max-w-6xl mx-auto px-6 pt-28 pb-14 lg:pt-36 lg:pb-16">
                    <div className="max-w-2xl">
                        <span className="text-[11px] font-bold text-[#7c3aed] uppercase tracking-[0.2em] mb-4 block">Info Update</span>
                        <h1 className="text-[clamp(2rem,5vw,3.5rem)] font-black text-[#7c3aed] dark:text-white leading-[1.1] tracking-[-0.03em] mb-4">
                            Kabar terbaru dari Ryaze
                        </h1>
                        <p className="text-[#666] dark:text-white/70 text-[15px] leading-relaxed">
                            Fitur baru, jadwal pemeliharaan, dan pengumuman resmi Ryaze.
                            Update penting ditandai dengan label prioritasnya.
                        </p>
                    </div>
                </div>
            </section>

            {/* FILTER KATEGORI */}
            <section className="bg-white dark:bg-[#0d0d18] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                <div className="max-w-6xl mx-auto px-6 py-5">
                    <div className="flex flex-wrap gap-2">
                        <a
                            href="/updates"
                            className={`px-3 py-1.5 text-[11px] font-bold tracking-wider uppercase transition-colors border ${
                                !currentCategory
                                    ? 'bg-[#7c3aed] text-white border-[#7c3aed]'
                                    : 'bg-white dark:bg-[#0a0a14] text-[#666] dark:text-white/60 border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed] hover:text-[#7c3aed]'
                            }`}
                        >
                            Semua
                        </a>
                        {(categories || []).map(cat => (
                            <a
                                key={cat.key}
                                href={`/updates?kategori=${cat.key}`}
                                className={`inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold tracking-wider uppercase transition-colors border ${
                                    currentCategory === cat.key
                                        ? 'bg-[#7c3aed] text-white border-[#7c3aed]'
                                        : 'bg-white dark:bg-[#0a0a14] text-[#666] dark:text-white/60 border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed] hover:text-[#7c3aed]'
                                }`}
                            >
                                <i className={`fa-solid ${cat.icon} text-[9px]`}></i>
                                {cat.label}
                                <span className="ml-0.5 opacity-50">{cat.count}</span>
                            </a>
                        ))}
                    </div>
                </div>
            </section>

            {/* DAFTAR UPDATE */}
            <section className="bg-white dark:bg-[#0d0d18]">
                <div className="max-w-6xl mx-auto px-6 py-12 lg:py-16">
                    {items.length > 0 ? (
                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-px bg-[#e5e5e5] dark:bg-[#1a1a2e] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            {items.map(item => {
                                const priority = PRIORITY_STYLE[item.priority] || PRIORITY_STYLE.normal;
                                const cat = (categories || []).find(c => c.key === item.category);
                                return (
                                    <a
                                        key={item.id}
                                        href={`/updates/${item.slug}`}
                                        className="group relative bg-white dark:bg-[#0d0d18] p-7 flex flex-col transition-colors hover:bg-[#fafafa] dark:hover:bg-[#0f0f1c]"
                                    >
                                        {priority.rail && (
                                            <span className={`absolute left-0 top-0 bottom-0 w-1 ${priority.rail}`} aria-hidden="true"></span>
                                        )}

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
                                        </div>

                                        <h2 className="text-lg font-bold text-[#7c3aed] dark:text-white leading-snug group-hover:text-[#6d28d9] dark:group-hover:text-white transition-colors">
                                            {item.title}
                                        </h2>

                                        <p className="mt-3 text-sm text-[#666] dark:text-white/60 leading-relaxed flex-1 line-clamp-3">
                                            {item.excerpt}
                                        </p>

                                        <div className="mt-6 pt-5 border-t border-[#f0f0f0] dark:border-[#1a1a2e] flex items-center justify-between">
                                            <span className="text-xs text-[#999] dark:text-white/40">
                                                {item.published_at}
                                                {item.version && <span className="ml-2 font-mono">{item.version}</span>}
                                            </span>
                                            <span className="text-xs font-semibold text-[#7c3aed] dark:text-white/70 group-hover:gap-2 transition-all inline-flex items-center gap-1">
                                                Baca <i className="fa-solid fa-arrow-right text-[9px]"></i>
                                            </span>
                                        </div>
                                    </a>
                                );
                            })}
                        </div>
                    ) : (
                        <div className="text-center py-20 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <i className="fa-regular fa-bell-slash text-3xl text-[#ccc] dark:text-white/30"></i>
                            <p className="mt-4 text-base font-bold text-[#7c3aed] dark:text-white">Belum ada info update</p>
                            <p className="mt-1 text-sm text-[#999] dark:text-white/50">
                                {currentCategory
                                    ? 'Tidak ada update pada kategori ini. Coba lihat kategori lain.'
                                    : 'Kabar terbaru dari Ryaze akan tampil di sini.'}
                            </p>
                        </div>
                    )}

                    {updates && updates.last_page > 1 && (
                        <div className="mt-10 flex items-center justify-center gap-2">
                            {updates.prev_page_url && (
                                <a href={updates.prev_page_url} className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">
                                    <i className="fa-solid fa-chevron-left text-[10px]"></i>
                                </a>
                            )}
                            {Array.from({ length: updates.last_page }, (_, i) => i + 1).map(page => (
                                <a
                                    key={page}
                                    href={`${updates.path}?kategori=${currentCategory || ''}&page=${page}`}
                                    className={`w-10 h-10 flex items-center justify-center text-sm font-semibold transition-colors ${
                                        page === updates.current_page
                                            ? 'bg-[#7c3aed] text-white'
                                            : 'text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed] hover:text-[#7c3aed]'
                                    }`}
                                >
                                    {page}
                                </a>
                            ))}
                            {updates.next_page_url && (
                                <a href={updates.next_page_url} className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">
                                    <i className="fa-solid fa-chevron-right text-[10px]"></i>
                                </a>
                            )}
                        </div>
                    )}
                </div>
            </section>
        </PublicLayout>
    );
}