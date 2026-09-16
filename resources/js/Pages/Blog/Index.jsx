import PublicLayout from '../../Layouts/PublicLayout';
import { useState } from 'react';
import { router } from '@inertiajs/react';

export default function Index({ articles, categories, featured, currentCategory }) {
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

    return (
        <PublicLayout
            title={currentCategory ? `${currentCategory.name} - Blog` : 'Blog'}
            description={currentCategory?.description || 'Artikel seputar web development, hosting, dan teknologi.'}
        >
            {/* HERO */}
            <section className="relative bg-white dark:bg-[#0a0a14] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                <div className="max-w-6xl mx-auto px-6 pt-28 pb-16 lg:pt-36 lg:pb-20">
                    <div className="max-w-2xl">
                        <span className="text-[11px] font-bold text-[#7c3aed] uppercase tracking-[0.2em] mb-4 block">Blog</span>
                        <h1 className="text-[clamp(2rem,5vw,3.5rem)] font-black text-[#7c3aed] dark:text-white leading-[1.1] tracking-[-0.03em] mb-4">
                            {currentCategory ? currentCategory.name : 'Artikel & Insights'}
                        </h1>
                        <p className="text-[#666] dark:text-white/70 text-[15px] leading-relaxed">
                            {currentCategory?.description || 'Tutorial, tips, dan berita seputar web development, hosting, dan teknologi terbaru.'}
                        </p>
                    </div>
                </div>
            </section>

            {/* SEARCH + CATEGORIES */}
            <section className="bg-white dark:bg-[#0d0d18] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                <div className="max-w-6xl mx-auto px-6 py-6">
                    <div className="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <form onSubmit={handleSearch} className="flex-1 w-full sm:max-w-md">
                            <div className="flex">
                                <input
                                    type="text"
                                    value={search}
                                    onChange={e => setSearch(e.target.value)}
                                    placeholder="Cari artikel..."
                                    className="flex-1 bg-[#fafafa] dark:bg-[#0a0a14] border border-[#e5e5e5] dark:border-[#1a1a2e] px-4 py-2.5 text-sm text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                <button type="submit" className="px-5 py-2.5 bg-[#7c3aed] text-white text-sm font-semibold hover:bg-[#6d28d9] transition-colors">
                                    <i className="fa-solid fa-search"></i>
                                </button>
                            </div>
                        </form>
                        <div className="flex flex-wrap gap-2">
                            <a href="/blog" className={`px-3 py-1.5 text-[11px] font-bold tracking-wider uppercase transition-colors border ${!currentCategory ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'bg-white dark:bg-[#0a0a14] text-[#666] dark:text-white/60 border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>
                                Semua
                            </a>
                            {categories.map(cat => (
                                <a key={cat.id} href={`/blog?category=${cat.slug}`} className={`px-3 py-1.5 text-[11px] font-bold tracking-wider uppercase transition-colors border ${currentCategory?.id === cat.id ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'bg-white dark:bg-[#0a0a14] text-[#666] dark:text-white/60 border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>
                                    {cat.name}
                                    <span className="ml-1 opacity-50">{cat.articles_count}</span>
                                </a>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* FEATURED */}
            {featured && !currentCategory && (
                <section className="bg-white dark:bg-[#0a0a14]">
                    <div className="max-w-6xl mx-auto px-6 py-12">
                        <a href={`/blog/${featured.slug}`} className="group grid grid-cols-1 lg:grid-cols-2 gap-px bg-[#e5e5e5] dark:bg-[#1a1a2e] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            {featured.cover_image ? (
                                <div className="h-64 lg:h-full overflow-hidden bg-[#f5f5f5] dark:bg-[#111]">
                                    <img src={featured.cover_image} alt={featured.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                </div>
                            ) : (
                                <div className="h-64 lg:h-full bg-[#f5f5f5] dark:bg-[#111] flex items-center justify-center text-[#ccc] dark:text-white/60">
                                    <i className="fa-solid fa-newspaper text-5xl"></i>
                                </div>
                            )}
                            <div className="bg-white dark:bg-[#0d0d18] p-8 lg:p-10 flex flex-col justify-center">
                                <div className="flex items-center gap-3 mb-4">
                                    {featured.category && <span className="px-2.5 py-0.5 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] text-[10px] font-bold tracking-wider uppercase">{featured.category.name}</span>}
                                    <span className="text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Featured</span>
                                </div>
                                <h2 className="text-2xl lg:text-3xl font-black text-[#7c3aed] dark:text-white tracking-tight mb-3 group-hover:text-[#6d28d9] dark:group-hover:text-[#a78bfa] transition-colors">{featured.title}</h2>
                                <p className="text-[#666] dark:text-white/60 text-[15px] leading-relaxed mb-6 line-clamp-3">{featured.excerpt || ''}</p>
                                <div className="flex items-center gap-3 text-xs text-[#bbb] dark:text-white/50">
                                    {featured.user && <span>{featured.user.name}</span>}
                                    <span>&middot;</span>
                                    <span>{featured.published_at}</span>
                                    <span>&middot;</span>
                                    <span>{featured.reading_time} mnt baca</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </section>
            )}

            {/* ARTICLES GRID */}
            <section className="bg-white dark:bg-[#0a0a14]">
                <div className="max-w-6xl mx-auto px-6 py-12">
                    {items.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-px bg-[#e5e5e5] dark:bg-[#1a1a2e] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            {items.map(a => (
                                <a key={a.id} href={`/blog/${a.slug}`} className="bg-white dark:bg-[#0d0d18] group">
                                    {a.cover_image ? (
                                        <div className="h-44 overflow-hidden bg-[#f5f5f5] dark:bg-[#111]">
                                            <img src={a.cover_image} alt={a.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                        </div>
                                    ) : (
                                        <div className="h-44 bg-[#f5f5f5] dark:bg-[#111] flex items-center justify-center text-[#ccc] dark:text-white/60">
                                            <i className="fa-solid fa-newspaper text-4xl"></i>
                                        </div>
                                    )}
                                    <div className="p-6">
                                        {a.category && <span className="text-[10px] font-bold text-[#7c3aed] uppercase tracking-widest">{a.category.name}</span>}
                                        <h3 className="text-[15px] font-bold text-[#333] dark:text-white mt-2 mb-2 group-hover:text-[#6d28d9] dark:group-hover:text-[#a78bfa] transition-colors line-clamp-2">{a.title}</h3>
                                        <p className="text-sm text-[#999] dark:text-white/50 line-clamp-2 mb-4">{a.excerpt || ''}</p>
                                        <div className="flex items-center gap-3 text-xs text-[#bbb] dark:text-white/50 pt-4 border-t border-[#f0f0f0] dark:border-[#1a1a2e]">
                                            {a.user && <span>{a.user.name}</span>}
                                            <span>&middot;</span>
                                            <span>{a.published_at}</span>
                                            <span>&middot;</span>
                                            <span>{a.reading_time} mnt</span>
                                        </div>
                                    </div>
                                </a>
                            ))}
                        </div>
                    ) : (
                        <div className="py-20 text-center text-sm text-[#999] dark:text-white/50">
                            <i className="fa-solid fa-newspaper text-4xl text-[#e5e5e5] dark:text-white/10 mb-4 block"></i>
                            Belum ada artikel.
                        </div>
                    )}
                </div>
            </section>

            {/* PAGINATION */}
            {pagination && pagination.last_page > 1 && (
                <section className="bg-white dark:bg-[#0d0d18] border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="max-w-6xl mx-auto px-6 py-8 flex items-center justify-center gap-2">
                        {pagination.prev_page_url && (
                            <a href={pagination.prev_page_url} className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">
                                &larr; Prev
                            </a>
                        )}
                        {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map(page => (
                            <a key={page} href={`${pagination.path}?page=${page}`} className={`w-10 h-10 flex items-center justify-center text-sm font-semibold transition-colors ${page === pagination.current_page ? 'bg-[#7c3aed] text-white' : 'text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>
                                {page}
                            </a>
                        ))}
                        {pagination.next_page_url && (
                            <a href={pagination.next_page_url} className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">
                                Next &rarr;
                            </a>
                        )}
                    </div>
                </section>
            )}
        </PublicLayout>
    );
}
