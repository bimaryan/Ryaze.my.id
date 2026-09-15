import DashboardLayout from '../../Layouts/DashboardLayout';
import { useForm, Link } from '@inertiajs/react';

function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

export default function Withdraw({ wallet }) {
    const { data, setData, post, processing, errors } = useForm({
        amount: '',
        bank_name: '',
        account_number: '',
        account_name: '',
    });

    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";
    const balance = wallet?.balance || 0;

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/user/wallet/withdraw');
    };

    return (
        <DashboardLayout title="Penarikan Dana">
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-money-bill-transfer text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Penarikan Dana</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Tarik saldo wallet Anda ke rekening bank.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href="/user/wallet" className="inline-flex justify-center items-center bg-[#fafafa] dark:bg-white/5 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:bg-[#f5f0ff] dark:hover:bg-white/10 text-[#666] dark:text-white/60 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:col-span-1">
                    <div className="bg-gradient-to-br from-[#7c3aed] to-[#5b21b6] rounded-2xl p-6 text-white relative overflow-hidden">
                        <div className="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full"></div>
                        <div className="absolute -right-4 -bottom-8 w-24 h-24 bg-white/5 rounded-full"></div>
                        <div className="relative z-10">
                            <p className="text-sm text-white/70 mb-1">Saldo Tersedia</p>
                            <h2 className="text-3xl font-black mb-3">{formatRupiah(balance)}</h2>
                            <p className="text-xs text-white/50">Minimal penarikan Rp 50.000</p>
                        </div>
                    </div>
                </div>

                <div className="lg:col-span-2">
                    <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                        <div className="px-6 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Formulir Penarikan</h3>
                        </div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-5">
                            <div>
                                <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Jumlah Penarikan (min. Rp 50.000)</label>
                                <input
                                    type="number"
                                    min="50000"
                                    max={balance}
                                    required
                                    value={data.amount}
                                    onChange={(e) => setData('amount', e.target.value)}
                                    placeholder="Masukkan jumlah..."
                                    className={inputCls}
                                />
                                {errors.amount && <p className="text-xs text-rose-500 mt-1">{errors.amount}</p>}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Nama Bank</label>
                                    <input
                                        type="text"
                                        required
                                        value={data.bank_name}
                                        onChange={(e) => setData('bank_name', e.target.value)}
                                        placeholder="Contoh: BCA, Mandiri..."
                                        className={inputCls}
                                    />
                                    {errors.bank_name && <p className="text-xs text-rose-500 mt-1">{errors.bank_name}</p>}
                                </div>
                                <div>
                                    <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Nomor Rekening</label>
                                    <input
                                        type="text"
                                        required
                                        value={data.account_number}
                                        onChange={(e) => setData('account_number', e.target.value)}
                                        placeholder="Masukkan nomor rekening..."
                                        className={inputCls}
                                    />
                                    {errors.account_number && <p className="text-xs text-rose-500 mt-1">{errors.account_number}</p>}
                                </div>
                            </div>

                            <div>
                                <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Nama Pemilik Rekening</label>
                                <input
                                    type="text"
                                    required
                                    value={data.account_name}
                                    onChange={(e) => setData('account_name', e.target.value)}
                                    placeholder="Sesuai nama di rekening bank..."
                                    className={inputCls}
                                />
                                {errors.account_name && <p className="text-xs text-rose-500 mt-1">{errors.account_name}</p>}
                            </div>

                            <div className="flex justify-end pt-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <button type="submit" disabled={processing} className="bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-sm font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm disabled:opacity-50">
                                    {processing ? 'Memproses...' : 'Ajukan Penarikan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
