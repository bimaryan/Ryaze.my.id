import { usePage, useForm, router } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function PromoEventCreate() {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        discount_percent: '',
        start_date: '',
        end_date: '',
        is_active: true,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/superadmin/promo-events');
    };

    return (
        <DashboardLayout title="Buat Promo Event">
            <div className="space-y-6">
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Detail Promo Event</h3>
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
                                    rows={4}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors resize-none"
                                />
                                {errors.description && <p className="mt-1 text-[12px] text-red-500">{errors.description}</p>}
                            </div>

                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Diskon (%)</label>
                                <input
                                    type="number"
                                    min="1"
                                    max="100"
                                    value={data.discount_percent}
                                    onChange={(e) => setData('discount_percent', e.target.value)}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                {errors.discount_percent && <p className="mt-1 text-[12px] text-red-500">{errors.discount_percent}</p>}
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Tanggal Mulai</label>
                                    <input
                                        type="date"
                                        value={data.start_date}
                                        onChange={(e) => setData('start_date', e.target.value)}
                                        className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                    />
                                    {errors.start_date && <p className="mt-1 text-[12px] text-red-500">{errors.start_date}</p>}
                                </div>
                                <div>
                                    <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Tanggal Berakhir</label>
                                    <input
                                        type="date"
                                        value={data.end_date}
                                        onChange={(e) => setData('end_date', e.target.value)}
                                        className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                    />
                                    {errors.end_date && <p className="mt-1 text-[12px] text-red-500">{errors.end_date}</p>}
                                </div>
                            </div>

                            <div>
                                <label className="flex items-center gap-3 cursor-pointer">
                                    <div className="relative">
                                        <input
                                            type="checkbox"
                                            checked={data.is_active}
                                            onChange={(e) => setData('is_active', e.target.checked)}
                                            className="sr-only peer"
                                        />
                                        <div className="w-9 h-5 bg-[#e5e5e5] dark:bg-white/10 peer-checked:bg-[#7c3aed] transition-colors"></div>
                                        <div className="absolute left-0.5 top-0.5 w-4 h-4 bg-white shadow-sm transition-transform peer-checked:translate-x-4"></div>
                                    </div>
                                    <span className="text-[13px] font-semibold text-[#333] dark:text-white">Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center gap-2 justify-end">
                        <button
                            type="button"
                            onClick={() => router.get('/superadmin/promo-events')}
                            className="px-5 py-2 text-[13px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] disabled:opacity-50 transition-colors"
                        >
                            {processing ? 'Menyimpan...' : 'Simpan Promo'}
                        </button>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}
