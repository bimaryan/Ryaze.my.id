import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function History() {
    const { wallet, transactions } = usePage().props;

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
    };

    const typeBadge = (type) => {
        const map = {
            credit: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            debit: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
            withdrawal: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            topup: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${map[type] || map.credit}`}>
                {type}
            </span>
        );
    };

    return (
        <DashboardLayout title="Wallet">
            <div className="space-y-6">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[12px] font-semibold text-[#999] dark:text-white/40 uppercase tracking-wider">Saldo Wallet</p>
                        <p className="text-2xl font-black text-[#7c3aed] mt-1">{formatCurrency(wallet?.balance)}</p>
                    </div>
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5 flex items-center justify-between">
                        <div>
                            <p className="text-[12px] font-semibold text-[#999] dark:text-white/40 uppercase tracking-wider">Total Pendapatan</p>
                            <p className="text-xl font-black text-green-600 dark:text-green-400 mt-1">{formatCurrency(wallet?.total_earned)}</p>
                        </div>
                        <Link href="/user/wallet/withdraw" className="px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                            <i className="fa-solid fa-money-bill-transfer mr-1"></i>Tarik Dana
                        </Link>
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Riwayat Transaksi</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tipe</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Jumlah</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Deskripsi</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {transactions?.data?.length > 0 ? transactions.data.map((t) => (
                                    <tr key={t.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3">{typeBadge(t.type)}</td>
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">
                                            {t.type === 'debit' || t.type === 'withdrawal' ? '-' : '+'}{formatCurrency(t.amount)}
                                        </td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{t.description || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(t.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="4" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Belum ada transaksi
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {transactions?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {transactions.from}-{transactions.to} dari {transactions.total} transaksi
                            </p>
                            <div className="flex items-center gap-1">
                                {transactions.prev_page_url && (
                                    <Link href={transactions.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(transactions.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${transactions.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${transactions.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {transactions.next_page_url && (
                                    <Link href={transactions.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
