import PublicLayout from '../../Layouts/PublicLayout';
import { useEffect } from 'react';

export default function Show({ article, related }) {
    useEffect(() => {
        if (article?.seo_title) document.title = article.seo_title;
    }, [article]);

    const tags = (() => {
        if (!article?.tags) return [];
        if (Array.isArray(article.tags)) return article.tags;
        return article.tags.split(',').map(t => t.trim()).filter(Boolean);
    })();

    return (
        <PublicLayout
            title={article?.seo_title || article?.title || 'Artikel'}
            description={article?.seo_description || article?.excerpt || ''}
        >
            {/* HERO */}
            <section className="relative bg-white dark:bg-[#0a0a14] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                <div className="max-w-3xl mx-auto px-6 pt-28 pb-12 lg:pt-36 lg:pb-16">
                    {/* Breadcrumb */}
                    <nav className="flex items-center gap-2 text-xs text-[#999] dark:text-white/40 mb-8">
                        <a href="/" className="hover:text-[#7c3aed] transition-colors">Home</a>
                        <i className="fa-solid fa-chevron-right text-[8px]"></i>
                        <a href="/blog" className="hover:text-[#7c3aed] transition-colors">Blog</a>
                        {article?.category && (
                            <>
                                <i className="fa-solid fa-chevron-right text-[8px]"></i>
                                <a href={`/blog?category=${article.category.slug}`} className="hover:text-[#7c3aed] transition-colors">{article.category.name}</a>
                            </>
                        )}
                    </nav>

                    {/* Category + Date */}
                    <div className="flex items-center gap-3 mb-4">
                        {article?.category && (
                            <span className="px-2.5 py-0.5 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] text-[10px] font-bold tracking-wider uppercase">
                                {article.category.name}
                            </span>
                        )}
                        <span className="text-xs text-[#999] dark:text-white/40">{article?.published_at}</span>
                    </div>

                    {/* Title */}
                    <h1 className="text-[clamp(1.8rem,4vw,2.8rem)] font-black text-[#7c3aed] dark:text-white leading-[1.1] tracking-[-0.03em] mb-6">
                        {article?.title}
                    </h1>

                    {/* Meta */}
                    <div className="flex flex-wrap items-center gap-4 text-sm text-[#999] dark:text-white/50">
                        {article?.user && (
                            <div className="flex items-center gap-2">
                                <div className="w-8 h-8 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center rounded-full">
                                    <span className="text-[#7c3aed] font-bold text-xs">{article.user.name?.charAt(0)}</span>
                                </div>
                                <span className="font-medium text-[#333] dark:text-white">{article.user.name}</span>
                            </div>
                        )}
                        <span>&middot;</span>
                        <span>{article?.reading_time} mnt baca</span>
                        <span>&middot;</span>
                        <span><i className="fa-solid fa-eye mr-1"></i>{article?.views_count || 0}</span>
                    </div>
                </div>
            </section>

            {/* COVER IMAGE */}
            {article?.cover_image && (
                <section className="bg-white dark:bg-[#0a0a14]">
                    <div className="max-w-4xl mx-auto px-6 py-8">
                        <div className="w-full aspect-video overflow-hidden bg-[#f5f5f5] dark:bg-[#111] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <img src={article.cover_image} alt={article.title} className="w-full h-full object-cover" />
                        </div>
                    </div>
                </section>
            )}

            {/* ARTICLE BODY */}
            <section className="bg-white dark:bg-[#0a0a14]">
                <div className="max-w-3xl mx-auto px-6 py-8">
                    <div
                        className="prose prose-lg max-w-none text-[#333] dark:text-white/80
                            prose-headings:text-[#7c3aed] dark:prose-headings:text-white
                            prose-headings:font-black prose-headings:tracking-tight
                            prose-a:text-[#7c3aed] prose-a:no-underline hover:prose-a:underline
                            prose-strong:text-[#333] dark:prose-strong:text-white
                            prose-code:bg-[#f5f0ff] dark:prose-code:bg-[#1a1025]/50 prose-code:text-[#7c3aed] prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:text-sm prose-code:font-mono
                            prose-pre:bg-[#0d0d18] prose-pre:border prose-pre:border-[#e5e5e5] dark:prose-pre:border-[#1a1a2e]
                            prose-img:border prose-img:border-[#e5e5e5] dark:prose-img:border-[#1a1a2e]
                            prose-blockquote:border-l-[#7c3aed] prose-blockquote:text-[#666] dark:prose-blockquote:text-white/60"
                        dangerouslySetInnerHTML={{ __html: article?.body || '' }}
                    />
                </div>
            </section>

            {/* TAGS */}
            {tags.length > 0 && (
                <section className="bg-white dark:bg-[#0a0a14]">
                    <div className="max-w-3xl mx-auto px-6 py-6">
                        <div className="flex flex-wrap gap-2 pt-6 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                            {tags.map((tag, i) => (
                                <span key={i} className="px-3 py-1 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] text-xs font-semibold border border-[#e9e0ff] dark:border-[#7c3aed]/20">
                                    {tag}
                                </span>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {/* RELATED */}
            {related && related.length > 0 && (
                <section className="bg-white dark:bg-[#0d0d18] border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="max-w-6xl mx-auto px-6 py-16">
                        <div className="mb-10">
                            <span className="text-[11px] font-bold text-[#7c3aed] uppercase tracking-[0.2em] mb-3 block">Artikel Terkait</span>
                            <h2 className="text-2xl font-black text-[#7c3aed] dark:text-white tracking-tight">Baca Juga</h2>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-px bg-[#e5e5e5] dark:bg-[#1a1a2e] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            {related.map(a => (
                                <a key={a.id} href={`/blog/${a.slug}`} className="bg-white dark:bg-[#0d0d18] group">
                                    {a.cover_image ? (
                                        <div className="h-40 overflow-hidden bg-[#f5f5f5] dark:bg-[#111]">
                                            <img src={a.cover_image} alt={a.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                        </div>
                                    ) : (
                                        <div className="h-40 bg-[#f5f5f5] dark:bg-[#111] flex items-center justify-center text-[#ccc] dark:text-white/60">
                                            <i className="fa-solid fa-newspaper text-3xl"></i>
                                        </div>
                                    )}
                                    <div className="p-5">
                                        {a.category && <span className="text-[10px] font-bold text-[#7c3aed] uppercase tracking-widest">{a.category.name}</span>}
                                        <h3 className="text-sm font-bold text-[#333] dark:text-white mt-1.5 mb-2 group-hover:text-[#6d28d9] dark:group-hover:text-[#a78bfa] transition-colors line-clamp-2">{a.title}</h3>
                                        <div className="flex items-center gap-2 text-xs text-[#bbb] dark:text-white/50">
                                            <span>{a.published_at}</span>
                                            <span>&middot;</span>
                                            <span>{a.reading_time} mnt</span>
                                        </div>
                                    </div>
                                </a>
                            ))}
                        </div>
                    </div>
                </section>
            )}
        </PublicLayout>
    );
}
