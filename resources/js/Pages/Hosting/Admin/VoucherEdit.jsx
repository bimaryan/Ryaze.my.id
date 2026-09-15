import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router, Link, useForm } from '@inertiajs/react';

export default function VoucherEdit({ voucher }) {
    const { data, setData, put, processing, errors } = useForm({
        code: voucher.code || '',
        discount_type: voucher.discount_amount ? 'amount' : 'percentage',
        discount_value: voucher.discount_amount ?? voucher.discount_percentage ?? '',
        max_uses: voucher.max_uses ?? '',
        expires_at: voucher.expires_at ? new Date(voucher.expires_at).toISOString().slice(0, 16) : '',
        is_active: voucher.is_active ?? true,
    });

    function handleSubmit(e) {
        e.preventDefault();
        put(route('admin_hosting.vouchers.update', { hashid: voucher.hashid }));
    }

    return (
        <DashboardLayout title={`Edit Voucher: ${voucher.code}`}>
            <div className="mb-1">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-indigo-50 dark:bg-indigo-500/20 flex items-center justify-center">
                            <i className="fa-solid fa-ticket text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <div>
                            <h1 className="text-lg font-bold text-[#333] dark:text-white">Edit Voucher: {voucher.code}</h1>
                            <p className="text-[13px] text-[#999] dark:text-white/40">Perbarui detail voucher diskon.</p>
                        </div>
                    </div>
                    <Link
                        href={route('admin_hosting.vouchers.index')}
                        className="inline-flex items-center justify-center bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:bg-[#fafafa] dark:hover:bg-white/5 px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm"
                    >
                        Kembali
                    </Link>
                </div>
            </div>

            <div className="bg-white dark:bg-[#0d0d18] rounded-xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] p-6 max-w-2xl mt-4">
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div>
                        <label className="block text-sm font-bold text-[#333] dark:text-white mb-1.5">Kode Voucher <span className="text-red-500">*</span></label>
                        <input
                            type="text"
                            value={data.code}
                            onChange={(e) => setData('code', e.target.value.toUpperCase())}
                            required
                            className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition uppercase"
                        />
                        {errors.code && <p className="text-xs text-red-500 mt-1">{errors.code}</p>}
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-bold text-[#333] dark:text-white mb-1.5">Tipe Diskon <span className="text-red-500">*</span></label>
                            <select
                                value={data.discount_type}
                                onChange={(e) => setData('discount_type', e.target.value)}
                                required
                                className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                            >
                                <option value="amount">Nominal (Rp)</option>
                                <option value="percentage">Persentase (%)</option>
                            </select>
                            {errors.discount_type && <p className="text-xs text-red-500 mt-1">{errors.discount_type}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-[#333] dark:text-white mb-1.5">Nilai Diskon <span className="text-red-500">*</span></label>
                            <input
                                type="number"
                                value={data.discount_value}
                                onChange={(e) => setData('discount_value', e.target.value)}
                                required
                                className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                            />
                            {errors.discount_value && <p className="text-xs text-red-500 mt-1">{errors.discount_value}</p>}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-bold text-[#333] dark:text-white mb-1.5">Batas Penggunaan <span className="text-[#999] dark:text-white/40 font-normal">(Opsional)</span></label>
                            <input
                                type="number"
                                value={data.max_uses}
                                onChange={(e) => setData('max_uses', e.target.value)}
                                placeholder="Kosongkan untuk tanpa batas"
                                className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                            />
                            {errors.max_uses && <p className="text-xs text-red-500 mt-1">{errors.max_uses}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-[#333] dark:text-white mb-1.5">Berlaku Sampai <span className="text-[#999] dark:text-white/40 font-normal">(Opsional)</span></label>
                            <input
                                type="datetime-local"
                                value={data.expires_at}
                                onChange={(e) => setData('expires_at', e.target.value)}
                                className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                            />
                            {errors.expires_at && <p className="text-xs text-red-500 mt-1">{errors.expires_at}</p>}
                        </div>
                    </div>

                    <div>
                        <label className="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                checked={data.is_active}
                                onChange={(e) => setData('is_active', e.target.checked)}
                                className="w-5 h-5 text-indigo-600 dark:text-indigo-400 rounded border-[#e5e5e5] dark:border-[#1a1a2e] focus:ring-indigo-500"
                            />
                            <span className="text-sm font-bold text-[#333] dark:text-white">Voucher Aktif</span>
                        </label>
                    </div>

                    <div className="pt-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-end">
                        <button type="submit" disabled={processing}
                            className="bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-bold px-6 py-2.5 rounded-lg transition-colors shadow-sm disabled:opacity-50">
                            {processing ? 'Memperbarui...' : 'Perbarui Voucher'}
                        </button>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}
