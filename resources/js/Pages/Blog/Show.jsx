import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Show() {
    const { article, related } = usePage().props;

    return (
        <DashboardLayout title={article?.title || 'Blog'}>
            <div className="max-w-3xl mx-auto space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <Link href="/blog" className="text-[13px] text-[#999] dark:text-white/40 hover:text-[#7c3aed] transition-colors">
                            <i className="fa-solid fa-arrow-left mr-1"></i>Kembali ke Blog
                        </Link>
                    </div>

                    {article?.featured_image && (
                        <img src={article.featured_image} alt={article.title} className="w-full h-64 object-cover" />
                    )}

                    <div className="p-5 md:p-8">
                        <div className="flex items-center gap-2 mb-4">
                            {article?.category && (
                                <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]">
                                    {article.category.name}
                                </span>
                            )}
                            <span className="text-[11px] text-[#999] dark:text-white/40">
                                {new Date(article?.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                            </span>
                        </div>

                        <h1 className="text-xl md:text-2xl font-black text-[#333] dark:text-white leading-tight">
                            {article?.title}
                        </h1>

                        {article?.author && (
                            <div className="flex items-center gap-2 mt-4 pb-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <div className="w-8 h-8 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa] flex items-center justify-center text-xs font-bold">
                                    {(article.author.name || 'A').split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase()}
                                </div>
                                <div>
                                    <p className="text-[13px] font-semibold text-[#333] dark:text-white">{article.author.name}</p>
                                    <p className="text-[11px] text-[#999] dark:text-white/40">Penulis</p>
                                </div>
                            </div>
                        )}

                        <div
                            className="prose prose-sm dark:prose-invert max-w-none mt-6 text-[14px] leading-relaxed text-[#333] dark:text-white/80"
                            dangerouslySetInnerHTML={{ __html: article?.content }}
                        />
                    </div>
                </div>

                {related?.length > 0 && (
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h2 className="text-sm font-bold text-[#333] dark:text-white">Artikel Terkait</h2>
                        </div>
                        <div className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {related.map((rel) => (
                                <Link key={rel.id} href={`/blog/${rel.slug}`} className="flex items-center gap-4 px-5 py-3 hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    {rel.featured_image && (
                                        <img src={rel.featured_image} alt={rel.title} className="w-16 h-12 object-cover shrink-0" />
                                    )}
                                    <div className="min-w-0">
                                        <p className="text-[13px] font-semibold text-[#333] dark:text-white hover:text-[#7c3aed] transition-colors truncate">
                                            {rel.title}
                                        </p>
                                        <p className="text-[11px] text-[#999] dark:text-white/40 mt-0.5">
                                            {new Date(rel.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </p>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
