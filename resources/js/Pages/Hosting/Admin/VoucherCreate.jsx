import { usePage, useForm, router } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function VoucherCreate() {
    const { data, setData, post, processing, errors } = useForm({
        code: '',
        discount_type: 'percent',
        discount_value: '',
        max_uses: '',
        expiry_date: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/hosting/vouchers');
    };

    return (
        <DashboardLayout title="Buat Voucher">
            <div className="space-y-6">
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Detail Voucher</h3>
                        </div>
                        <div className="p-5 space-y-4">
                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Kode Voucher</label>
                                <input
                                    type="text"
                                    value={data.code}
                                    onChange={(e) => setData('code', e.target.value.toUpperCase())}
                                    placeholder="Contoh: DISKON50"
                                    className="w-full px-3 py-2 text-[13px] font-mono border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                {errors.code && <p className="mt-1 text-[12px] text-red-500">{errors.code}</p>}
                            </div>

                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Tipe Diskon</label>
                                <select
                                    value={data.discount_type}
                                    onChange={(e) => setData('discount_type', e.target.value)}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                >
                                    <option value="percent">Persen (%)</option>
                                    <option value="fixed">Nominal (Rp)</option>
                                </select>
                                {errors.discount_type && <p className="mt-1 text-[12px] text-red-500">{errors.discount_type}</p>}
                            </div>

                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Nilai Diskon</label>
                                <input
                                    type="number"
                                    value={data.discount_value}
                                    onChange={(e) => setData('discount_value', e.target.value)}
                                    placeholder={data.discount_type === 'percent' ? 'Contoh: 10' : 'Contoh: 50000'}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                {errors.discount_value && <p className="mt-1 text-[12px] text-red-500">{errors.discount_value}</p>}
                            </div>

                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Maksimum Penggunaan</label>
                                <input
                                    type="number"
                                    value={data.max_uses}
                                    onChange={(e) => setData('max_uses', e.target.value)}
                                    placeholder="Kosongkan untuk tanpa batas"
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                {errors.max_uses && <p className="mt-1 text-[12px] text-red-500">{errors.max_uses}</p>}
                            </div>

                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Tanggal Kedaluwarsa</label>
                                <input
                                    type="date"
                                    value={data.expiry_date}
                                    onChange={(e) => setData('expiry_date', e.target.value)}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                {errors.expiry_date && <p className="mt-1 text-[12px] text-red-500">{errors.expiry_date}</p>}
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center gap-2 justify-end">
                        <button
                            type="button"
                            onClick={() => router.get('/admin/hosting/vouchers')}
                            className="px-5 py-2 text-[13px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] disabled:opacity-50 transition-colors"
                        >
                            {processing ? 'Menyimpan...' : 'Buat Voucher'}
                        </button>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}
