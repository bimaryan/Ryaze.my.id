import { usePage, useForm, router } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function AnnouncementEdit() {
    const { announcement } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        title: announcement?.title || '',
        message: announcement?.message || '',
        type: announcement?.type || 'info',
        is_active: announcement?.is_active ?? true,
        _method: 'put',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(`/superadmin/announcements/${announcement?.hashid || announcement?.id}`);
    };

    return (
        <DashboardLayout title="Edit Pengumuman">
            <div className="space-y-6">
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Detail Pengumuman</h3>
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
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Pesan</label>
                                <textarea
                                    value={data.message}
                                    onChange={(e) => setData('message', e.target.value)}
                                    rows={6}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors resize-none"
                                />
                                {errors.message && <p className="mt-1 text-[12px] text-red-500">{errors.message}</p>}
                            </div>

                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Tipe</label>
                                <select
                                    value={data.type}
                                    onChange={(e) => setData('type', e.target.value)}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                >
                                    <option value="info">Info</option>
                                    <option value="warning">Warning</option>
                                    <option value="success">Success</option>
                                </select>
                                {errors.type && <p className="mt-1 text-[12px] text-red-500">{errors.type}</p>}
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
                            onClick={() => router.get('/superadmin/announcements')}
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
