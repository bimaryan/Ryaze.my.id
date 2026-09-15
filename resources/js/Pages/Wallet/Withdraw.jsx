import { usePage, useForm, Link } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Withdraw() {
    const { wallet } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        amount: '',
        bank_name: '',
        account_number: '',
        account_name: '',
    });

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/user/wallet/withdraw');
    };

    const banks = ['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga', 'Danamon', 'Permata', 'BSI', 'Panin', 'Lainnya'];

    return (
        <DashboardLayout title="Tarik Dana">
            <div className="max-w-2xl mx-auto space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-[12px] font-semibold text-[#999] dark:text-white/40 uppercase tracking-wider">Saldo Tersedia</p>
                            <p className="text-2xl font-black text-[#7c3aed] mt-1">{formatCurrency(wallet?.balance)}</p>
                        </div>
                        <Link href="/user/wallet" className="text-[13px] font-medium text-[#7c3aed] hover:text-[#6d28d9] transition-colors">
                            <i className="fa-solid fa-arrow-left mr-1"></i>Kembali
                        </Link>
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Form Penarikan</h2>
                        <p className="text-[12px] text-[#999] dark:text-white/40 mt-1">Isi data rekening untuk penarikan dana.</p>
                    </div>

                    <form onSubmit={handleSubmit} className="p-5 space-y-5">
                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Jumlah (IDR)</label>
                            <input
                                type="number"
                                value={data.amount}
                                onChange={(e) => setData('amount', e.target.value)}
                                placeholder="50000"
                                min="10000"
                                required
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                            />
                            {errors.amount && <p className="mt-1 text-[12px] text-red-500">{errors.amount}</p>}
                            <p className="text-[11px] text-[#999] dark:text-white/30 mt-1">Minimal penarikan Rp 10.000</p>
                        </div>

                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Nama Bank</label>
                            <select
                                value={data.bank_name}
                                onChange={(e) => setData('bank_name', e.target.value)}
                                required
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                            >
                                <option value="">Pilih bank</option>
                                {banks.map((bank) => (
                                    <option key={bank} value={bank}>{bank}</option>
                                ))}
                            </select>
                            {errors.bank_name && <p className="mt-1 text-[12px] text-red-500">{errors.bank_name}</p>}
                        </div>

                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Nomor Rekening</label>
                            <input
                                type="text"
                                value={data.account_number}
                                onChange={(e) => setData('account_number', e.target.value)}
                                placeholder="1234567890"
                                required
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                            />
                            {errors.account_number && <p className="mt-1 text-[12px] text-red-500">{errors.account_number}</p>}
                        </div>

                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Nama Pemilik Rekening</label>
                            <input
                                type="text"
                                value={data.account_name}
                                onChange={(e) => setData('account_name', e.target.value)}
                                placeholder="John Doe"
                                required
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                            />
                            {errors.account_name && <p className="mt-1 text-[12px] text-red-500">{errors.account_name}</p>}
                        </div>

                        <div className="flex items-center gap-3 pt-2">
                            <button
                                type="submit"
                                disabled={processing || !data.amount || !data.bank_name || !data.account_number || !data.account_name}
                                className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                            >
                                {processing ? 'Mengirim...' : 'Ajukan Penarikan'}
                            </button>
                            <Link href="/user/wallet" className="px-5 py-2 text-[13px] font-medium text-[#666] dark:text-white/60 hover:text-[#7c3aed] transition-colors">
                                Batal
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}
