import { usePage, useForm, router } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function ArticleCreate() {
    const { categories } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        slug: '',
        content: '',
        category_id: '',
        status: 'draft',
        featured_image: null,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/superadmin/articles');
    };

    const generateSlug = (title) => {
        return title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    };

    return (
        <DashboardLayout title="Buat Artikel">
            <div className="space-y-6">
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Detail Artikel</h3>
                        </div>
                        <div className="p-5 space-y-4">
                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Judul</label>
                                <input
                                    type="text"
                                    value={data.title}
                                    onChange={(e) => {
                                        setData('title', e.target.value);
                                        if (!data.slug) setData('slug', generateSlug(e.target.value));
                                    }}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                {errors.title && <p className="mt-1 text-[12px] text-red-500">{errors.title}</p>}
                            </div>

                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Slug</label>
                                <input
                                    type="text"
                                    value={data.slug}
                                    onChange={(e) => setData('slug', e.target.value)}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                {errors.slug && <p className="mt-1 text-[12px] text-red-500">{errors.slug}</p>}
                            </div>

                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Konten</label>
                                <textarea
                                    value={data.content}
                                    onChange={(e) => setData('content', e.target.value)}
                                    rows={12}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors resize-none font-mono"
                                />
                                {errors.content && <p className="mt-1 text-[12px] text-red-500">{errors.content}</p>}
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Kategori</label>
                                    <select
                                        value={data.category_id}
                                        onChange={(e) => setData('category_id', e.target.value)}
                                        className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                    >
                                        <option value="">Pilih Kategori</option>
                                        {categories?.map((cat) => (
                                            <option key={cat.id} value={cat.id}>{cat.name}</option>
                                        ))}
                                    </select>
                                    {errors.category_id && <p className="mt-1 text-[12px] text-red-500">{errors.category_id}</p>}
                                </div>
                                <div>
                                    <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Status</label>
                                    <select
                                        value={data.status}
                                        onChange={(e) => setData('status', e.target.value)}
                                        className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                    >
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Gambar Featured</label>
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={(e) => setData('featured_image', e.target.files[0])}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors file:mr-3 file:py-1 file:px-3 file:border-0 file:text-[13px] file:font-semibold file:bg-[#f5f0ff] file:text-[#7c3aed] hover:file:bg-[#ede9fe]"
                                />
                                {errors.featured_image && <p className="mt-1 text-[12px] text-red-500">{errors.featured_image}</p>}
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center gap-2 justify-end">
                        <button
                            type="button"
                            onClick={() => router.get('/superadmin/articles')}
                            className="px-5 py-2 text-[13px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] disabled:opacity-50 transition-colors"
                        >
                            {processing ? 'Menyimpan...' : 'Simpan Artikel'}
                        </button>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}
