import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Index() {
    const { articles, categories, featured } = usePage().props;
    const [activeCategory, setActiveCategory] = useState('');

    const handleCategoryFilter = (slug) => {
        setActiveCategory(slug);
        router.get('/blog', slug ? { category: slug } : {}, { preserveState: true, replace: true });
    };

    return (
        <DashboardLayout title="Blog">
            <div className="space-y-6">
                {featured && (
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                        <div className="md:flex">
                            {featured.featured_image && (
                                <div className="md:w-1/2">
                                    <img src={featured.featured_image} alt={featured.title} className="w-full h-48 md:h-full object-cover" />
                                </div>
                            )}
                            <div className={`p-6 md:p-8 flex flex-col justify-center ${featured.featured_image ? 'md:w-1/2' : 'w-full'}`}>
                                <div className="flex items-center gap-2 mb-3">
                                    {featured.category && (
                                        <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]">
                                            {featured.category.name}
                                        </span>
                                    )}
                                    <span className="text-[11px] text-[#999] dark:text-white/40">
                                        {new Date(featured.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                    </span>
                                </div>
                                <Link href={`/blog/${featured.slug}`} className="text-lg font-bold text-[#333] dark:text-white hover:text-[#7c3aed] transition-colors leading-tight">
                                    {featured.title}
                                </Link>
                                {featured.excerpt && (
                                    <p className="text-[13px] text-[#666] dark:text-white/60 mt-2 line-clamp-2">{featured.excerpt}</p>
                                )}
                                <div className="flex items-center gap-2 mt-4">
                                    {featured.author && (
                                        <span className="text-[12px] text-[#999] dark:text-white/40">
                                            <i className="fa-solid fa-user mr-1"></i>{featured.author.name}
                                        </span>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {categories?.length > 0 && (
                    <div className="flex items-center gap-2 overflow-x-auto pb-1">
                        <button
                            onClick={() => handleCategoryFilter('')}
                            className={`px-4 py-1.5 text-[13px] font-medium whitespace-nowrap transition-colors ${
                                !activeCategory
                                    ? 'bg-[#7c3aed] text-white'
                                    : 'bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'
                            }`}
                        >
                            Semua
                        </button>
                        {categories.map((cat) => (
                            <button
                                key={cat.id}
                                onClick={() => handleCategoryFilter(cat.slug)}
                                className={`px-4 py-1.5 text-[13px] font-medium whitespace-nowrap transition-colors ${
                                    activeCategory === cat.slug
                                        ? 'bg-[#7c3aed] text-white'
                                        : 'bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'
                                }`}
                            >
                                {cat.name}
                            </button>
                        ))}
                    </div>
                )}

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {articles?.data?.length > 0 ? articles.data.map((article) => (
                        <div key={article.id} className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-colors">
                            {article.featured_image && (
                                <img src={article.featured_image} alt={article.title} className="w-full h-40 object-cover" />
                            )}
                            <div className="p-4">
                                <div className="flex items-center gap-2 mb-2">
                                    {article.category && (
                                        <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]">
                                            {article.category.name}
                                        </span>
                                    )}
                                    <span className="text-[11px] text-[#999] dark:text-white/40">
                                        {new Date(article.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                    </span>
                                </div>
                                <Link href={`/blog/${article.slug}`} className="text-[14px] font-bold text-[#333] dark:text-white hover:text-[#7c3aed] transition-colors leading-tight line-clamp-2">
                                    {article.title}
                                </Link>
                                {article.excerpt && (
                                    <p className="text-[12px] text-[#666] dark:text-white/60 mt-2 line-clamp-2">{article.excerpt}</p>
                                )}
                                {article.author && (
                                    <p className="text-[11px] text-[#999] dark:text-white/40 mt-3">
                                        <i className="fa-solid fa-user mr-1"></i>{article.author.name}
                                    </p>
                                )}
                            </div>
                        </div>
                    )) : (
                        <div className="col-span-full bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] px-5 py-12 text-center">
                            <i className="fa-solid fa-newspaper text-3xl text-[#999] dark:text-white/20 mb-3"></i>
                            <p className="text-sm text-[#999] dark:text-white/40">Belum ada artikel</p>
                        </div>
                    )}
                </div>

                {articles?.last_page > 1 && (
                    <div className="flex items-center justify-center gap-1">
                        {articles.prev_page_url && (
                            <Link href={articles.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                        )}
                        {[...Array(articles.last_page)].map((_, i) => (
                            <Link key={i + 1} href={`${articles.path}?page=${i + 1}${activeCategory ? `&category=${activeCategory}` : ''}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${articles.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                        ))}
                        {articles.next_page_url && (
                            <Link href={articles.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                        )}
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
