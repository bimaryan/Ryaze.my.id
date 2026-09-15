import PublicLayout from '../../Layouts/PublicLayout';
import { Link } from '@inertiajs/react';

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

function readingTime(text) {
    if (!text) return '1 min';
    const words = text.replace(/<[^>]*>/g, '').split(/\s+/).length;
    const mins = Math.max(1, Math.ceil(words / 200));
    return `${mins} min baca`;
}

function formatViews(n) {
    if (!n) return '0';
    if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
    return String(n);
}

export default function Show({ article, related }) {
    const relatedList = related?.data || related || [];
    const tags = article?.tags || [];

    return (
        <PublicLayout>
            <div className="pt-20 pb-16">
                <article className="max-w-4xl mx-auto px-6">
                    {article?.cover_image && (
                        <div className="mb-8 rounded-2xl overflow-hidden">
                            <img src={article.cover_image} alt={article.title} className="w-full aspect-video object-cover" />
                        </div>
                    )}

                    <nav className="mb-6 text-xs text-[#999] dark:text-white/40 flex items-center gap-1.5 flex-wrap">
                        <Link href="/" className="hover:text-[#7c3aed] dark:hover:text-white transition-colors">Beranda</Link>
                        <i className="fa-solid fa-chevron-right text-[8px]"></i>
                        <Link href="/blog" className="hover:text-[#7c3aed] dark:hover:text-white transition-colors">Blog</Link>
                        {article?.category && (
                            <>
                                <i className="fa-solid fa-chevron-right text-[8px]"></i>
                                <Link href={`/blog?category=${article.category?.slug || article.category}`} className="hover:text-[#7c3aed] dark:hover:text-white transition-colors">{article.category?.name || article.category}</Link>
                            </>
                        )}
                        <i className="fa-solid fa-chevron-right text-[8px]"></i>
                        <span className="text-[#666] dark:text-white/60 truncate max-w-[200px]">{article?.title}</span>
                    </nav>

                    <h1 className="text-3xl md:text-4xl font-black text-[#333] dark:text-white tracking-tight mb-4 leading-tight">{article?.title}</h1>

                    <div className="flex flex-wrap items-center gap-4 text-sm text-[#999] dark:text-white/40 mb-8 pb-8 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="flex items-center gap-2">
                            {article?.author?.avatar ? (
                                <img src={article.author.avatar} alt={article.author.name} className="w-8 h-8 rounded-full object-cover" />
                            ) : (
                                <div className="w-8 h-8 rounded-full bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa] flex items-center justify-center font-bold text-xs uppercase">
                                    {(article?.author?.name || 'A').charAt(0)}
                                </div>
                            )}
                            <span className="font-medium text-[#666] dark:text-white/60">{article?.author?.name || 'Admin'}</span>
                        </div>
                        <span>{formatDate(article?.published_at || article?.created_at)}</span>
                        <span><i className="fa-regular fa-clock me-1"></i>{readingTime(article?.body)}</span>
                        <span><i className="fa-solid fa-eye me-1"></i>{formatViews(article?.views_count)} views</span>
                    </div>

                    <div className="prose prose-lg max-w-none dark:prose-invert prose-headings:text-[#333] dark:prose-headings:text-white prose-p:text-[#666] dark:prose-p:text-white/70 prose-a:text-[#7c3aed] dark:prose-a:text-[#a78bfa] prose-img:rounded-xl" dangerouslySetInnerHTML={{ __html: article?.body || '' }}></div>

                    {tags.length > 0 && (
                        <div className="mt-10 pt-8 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white mb-3"><i className="fa-solid fa-hashtag me-1 text-[#7c3aed] dark:text-[#a78bfa]"></i>Tags</h3>
                            <div className="flex flex-wrap gap-2">
                                {tags.map((tag, idx) => (
                                    <Link
                                        key={idx}
                                        href={`/blog?tag=${typeof tag === 'string' ? tag : tag.slug || tag.name}`}
                                        className="px-3 py-1.5 bg-[#fafafa] dark:bg-white/5 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 text-xs font-medium rounded-lg hover:bg-[#f5f0ff] dark:hover:bg-white/5 hover:text-[#7c3aed] dark:hover:text-[#a78bfa] hover:border-[#7c3aed]/30 transition"
                                    >
                                        #{typeof tag === 'string' ? tag : tag.name}
                                    </Link>
                                ))}
                            </div>
                        </div>
                    )}
                </article>

                {relatedList.length > 0 && (
                    <div className="max-w-6xl mx-auto px-6 mt-16">
                        <h2 className="text-xl font-bold text-[#333] dark:text-white mb-6">Artikel Terkait</h2>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {relatedList.map((item) => (
                                <Link key={item.id} href={`/blog/${item.slug}`} className="block group">
                                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-2xl overflow-hidden hover:shadow-lg transition-shadow h-full flex flex-col">
                                        {item.cover_image && (
                                            <div className="aspect-video overflow-hidden">
                                                <img src={item.cover_image} alt={item.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                            </div>
                                        )}
                                        <div className="p-5 flex-1 flex flex-col">
                                            {item.category && (
                                                <span className="inline-block w-fit px-2.5 py-0.5 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] dark:text-[#a78bfa] text-[11px] font-bold rounded border border-[#ede9fe] dark:border-[#7c3aed]/30 mb-2">{item.category?.name || item.category}</span>
                                            )}
                                            <h3 className="text-[15px] font-bold text-[#333] dark:text-white group-hover:text-[#7c3aed] dark:group-hover:text-[#a78bfa] transition-colors line-clamp-2 flex-1">{item.title}</h3>
                                            <p className="text-xs text-[#999] dark:text-white/40 mt-2">{formatDate(item.published_at || item.created_at)}</p>
                                        </div>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </PublicLayout>
    );
}
