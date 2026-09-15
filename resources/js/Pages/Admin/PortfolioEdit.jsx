import { usePage, useForm, router } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function PortfolioEdit() {
    const { portfolio } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        title: portfolio?.title || '',
        description: portfolio?.description || '',
        image: null,
        url: portfolio?.url || '',
        _method: 'put',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(`/superadmin/portfolios/${portfolio?.hashid || portfolio?.id}`);
    };

    return (
        <DashboardLayout title="Edit Portofolio">
            <div className="space-y-6">
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Detail Portofolio</h3>
                        </div>
                        <div className="p-5 space-y-4">
                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Judul</label>
                                <input
                                    type="text"
                                    value={data.title}
                                    onChange={(e) => setData('title', e.target.value)}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                {errors.title && <p className="mt-1 text-[12px] text-red-500">{errors.title}</p>}
                            </div>

                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Deskripsi</label>
                                <textarea
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    rows={6}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors resize-none"
                                />
                                {errors.description && <p className="mt-1 text-[12px] text-red-500">{errors.description}</p>}
                            </div>

                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Gambar</label>
                                {portfolio?.image && (
                                    <div className="mb-2">
                                        <img src={portfolio.image} alt={portfolio.title} className="w-32 h-20 object-cover border border-[#e5e5e5] dark:border-[#1a1a2e]" />
                                    </div>
                                )}
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={(e) => setData('image', e.target.files[0])}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors file:mr-3 file:py-1 file:px-3 file:border-0 file:text-[13px] file:font-semibold file:bg-[#f5f0ff] file:text-[#7c3aed] hover:file:bg-[#ede9fe]"
                                />
                                {errors.image && <p className="mt-1 text-[12px] text-red-500">{errors.image}</p>}
                            </div>

                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">URL</label>
                                <input
                                    type="url"
                                    value={data.url}
                                    onChange={(e) => setData('url', e.target.value)}
                                    placeholder="https://"
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                {errors.url && <p className="mt-1 text-[12px] text-red-500">{errors.url}</p>}
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center gap-2 justify-end">
                        <button
                            type="button"
                            onClick={() => router.get('/superadmin/portfolios')}
                            className="px-5 py-2 text-[13px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] disabled:opacity-50 transition-colors"
                        >
                            {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                        </button>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}
