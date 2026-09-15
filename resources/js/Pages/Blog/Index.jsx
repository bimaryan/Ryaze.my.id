import PublicLayout from '../../Layouts/PublicLayout';
import { Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

function readingTime(text) {
    if (!text) return '1 min';
    const words = text.replace(/<[^>]*>/g, '').split(/\s+/).length;
    const mins = Math.max(1, Math.ceil(words / 200));
    return `${mins} min baca`;
}

export default function Index({ articles, categories, featured }) {
    const { url } = usePage();
    const [search, setSearch] = useState('');

    const handleSearch = (e) => {
        e.preventDefault();
        const params = new URLSearchParams(url.split('?')[1] || '');
        if (search) params.set('search', search); else params.delete('search');
        params.delete('page');
        router.get('/blog', Object.fromEntries(params), { preserveState: true, replace: true });
    };

    const featuredArticle = featured?.data?.[0] || null;
    const articleList = articles?.data || [];
    const categoryList = categories || [];
    const paginationLinks = articles?.links || [];
    const hasPages = paginationLinks.length > 3;

    return (
        <PublicLayout>
            <div className="pt-20 pb-16">
                <div className="max-w-6xl mx-auto px-6">
                    <div className="mb-10">
                        <h1 className="text-3xl md:text-4xl font-black text-[#333] dark:text-white tracking-tight">Blog</h1>
                        <p className="text-[#666] dark:text-white/60 mt-2">Artikel, tutorial, dan berita terbaru dari Ryaze.</p>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div className="lg:col-span-2 space-y-8">
                            {featuredArticle && (
                                <Link href={`/blog/${featuredArticle.slug}`} className="block group">
                                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-2xl overflow-hidden hover:shadow-lg transition-shadow">
                                        {featuredArticle.cover_image && (
                                            <div className="aspect-video overflow-hidden">
                                                <img src={featuredArticle.cover_image} alt={featuredArticle.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                            </div>
                                        )}
                                        <div className="p-6">
                                            <div className="flex items-center gap-2 mb-3">
                                                <span className="px-2.5 py-0.5 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-300 text-[11px] font-bold rounded border border-rose-200 dark:border-rose-500/40">Sorotan</span>
                                                {featuredArticle.category && (
                                                    <span className="px-2.5 py-0.5 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] dark:text-[#a78bfa] text-[11px] font-bold rounded border border-[#ede9fe] dark:border-[#7c3aed]/30">{featuredArticle.category.name || featuredArticle.category}</span>
                                                )}
                                            </div>
                                            <h2 className="text-xl font-bold text-[#333] dark:text-white group-hover:text-[#7c3aed] dark:group-hover:text-[#a78bfa] transition-colors mb-2">{featuredArticle.title}</h2>
                                            <p className="text-sm text-[#666] dark:text-white/60 line-clamp-2 mb-4">{featuredArticle.excerpt || featuredArticle.body?.replace(/<[^>]*>/g, '').substring(0, 150)}</p>
                                            <div className="flex items-center gap-4 text-xs text-[#999] dark:text-white/40">
                                                <span>{featuredArticle.author?.name || 'Admin'}</span>
                                                <span>{formatDate(featuredArticle.published_at || featuredArticle.created_at)}</span>
                                                <span>{readingTime(featuredArticle.body)}</span>
                                            </div>
                                        </div>
                                    </div>
                                </Link>
                            )}

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {articleList.length > 0 ? articleList.map((article) => (
                                    <Link key={article.id} href={`/blog/${article.slug}`} className="block group">
                                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-2xl overflow-hidden hover:shadow-lg transition-shadow h-full flex flex-col">
                                            {article.cover_image && (
                                                <div className="aspect-video overflow-hidden">
                                                    <img src={article.cover_image} alt={article.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                                </div>
                                            )}
                                            <div className="p-5 flex-1 flex flex-col">
                                                <div className="flex items-center gap-2 mb-2">
                                                    {article.category && (
                                                        <span className="px-2.5 py-0.5 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] dark:text-[#a78bfa] text-[11px] font-bold rounded border border-[#ede9fe] dark:border-[#7c3aed]/30">{article.category.name || article.category}</span>
                                                    )}
                                                </div>
                                                <h3 className="text-[15px] font-bold text-[#333] dark:text-white group-hover:text-[#7c3aed] dark:group-hover:text-[#a78bfa] transition-colors mb-2 line-clamp-2 flex-1">{article.title}</h3>
                                                <p className="text-xs text-[#666] dark:text-white/60 line-clamp-2 mb-3">{article.excerpt || article.body?.replace(/<[^>]*>/g, '').substring(0, 100)}</p>
                                                <div className="flex items-center gap-3 text-[11px] text-[#999] dark:text-white/40 pt-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                                                    <span>{formatDate(article.published_at || article.created_at)}</span>
                                                    <span>{readingTime(article.body)}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </Link>
                                )) : (
                                    <div className="md:col-span-2 py-16 text-center text-[#999] dark:text-white/40">
                                        <i className="fa-solid fa-newspaper text-3xl mb-3 text-slate-300 dark:text-slate-400 block"></i>
                                        <p className="text-sm">Belum ada artikel yang dipublikasikan.</p>
                                    </div>
                                )}
                            </div>

                            {hasPages && (
                                <div className="flex items-center justify-center gap-1">
                                    {paginationLinks.map((link, i) => (
                                        <button
                                            key={i}
                                            disabled={!link.url}
                                            onClick={() => link.url && router.get(link.url, {}, { preserveState: true, replace: true })}
                                            className={`px-3 py-1.5 text-[13px] font-medium rounded-lg transition ${
                                                link.active
                                                    ? 'bg-[#7c3aed] text-white shadow-sm'
                                                    : link.url
                                                        ? 'text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5'
                                                        : 'text-[#ccc] dark:text-white/20 cursor-not-allowed'
                                            }`}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ))}
                                </div>
                            )}
                        </div>

                        <div className="lg:col-span-1 space-y-6">
                            <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-2xl p-5">
                                <form onSubmit={handleSearch}>
                                    <div className="relative">
                                        <i className="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#999] dark:text-white/40 text-sm"></i>
                                        <input
                                            type="text"
                                            value={search}
                                            onChange={(e) => setSearch(e.target.value)}
                                            placeholder="Cari artikel..."
                                            className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl pl-9 pr-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition"
                                        />
                                    </div>
                                </form>
                            </div>

                            <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-2xl p-5">
                                <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4 flex items-center gap-2">
                                    <i className="fa-solid fa-folder text-[#7c3aed] dark:text-[#a78bfa]"></i> Kategori
                                </h3>
                                <div className="space-y-1">
                                    {categoryList.length > 0 ? categoryList.map((cat, idx) => (
                                        <Link
                                            key={cat.id || idx}
                                            href={`/blog?category=${cat.slug || cat.name}`}
                                            className="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5 hover:text-[#7c3aed] dark:hover:text-[#a78bfa] transition-colors"
                                        >
                                            <span className="font-medium">{cat.name}</span>
                                            <span className="px-2 py-0.5 bg-[#fafafa] dark:bg-white/5 text-[#999] dark:text-white/40 text-xs font-bold rounded border border-[#e5e5e5] dark:border-[#1a1a2e]">{cat.articles_count || 0}</span>
                                        </Link>
                                    )) : (
                                        <p className="text-sm text-[#999] dark:text-white/40">Belum ada kategori.</p>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
