import DashboardLayout from '../../Layouts/DashboardLayout';
import { router, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

function formatTime(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
}

function statusBadge(status) {
    if (status === 'completed') return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40">Selesai</span>;
    if (status === 'pending') return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-300 border border-amber-200 dark:border-amber-500/40">Menunggu</span>;
    return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-500/40">Gagal</span>;
}

const presets = [20000, 50000, 100000, 200000, 500000, 1000000];

export default function History({ wallet, transactions }) {
    const [showModal, setShowModal] = useState(false);
    const [topUpAmount, setTopUpAmount] = useState('');

    const paginationLinks = transactions?.links || [];
    const hasPages = paginationLinks.length > 3;
    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";

    const handleTopUp = (e) => {
        e.preventDefault();
        const amount = parseInt(topUpAmount);
        if (!amount || amount < 10000) return;
        router.post('/user/wallet/topup', { amount }, {
            onSuccess: () => { setShowModal(false); setTopUpAmount(''); },
        });
    };

    const handlePay = (transaction) => {
        if (transaction.payment_url) {
            window.location.href = transaction.payment_url;
        }
    };

    return (
        <DashboardLayout title="Riwayat Wallet">
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-wallet text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Wallet</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola saldo dan riwayat transaksi Anda.</p>
                    </div>
                </div>
            </div>

            <div className="mt-6">
                <div className="bg-gradient-to-br from-[#7c3aed] to-[#5b21b6] rounded-2xl p-6 text-white relative overflow-hidden">
                    <div className="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div className="absolute -right-4 -bottom-8 w-24 h-24 bg-white/5 rounded-full"></div>
                    <div className="relative z-10 flex items-center gap-4">
                        <div className="w-14 h-14 bg-white/15 flex items-center justify-center rounded-2xl">
                            <i className="fa-solid fa-wallet text-2xl"></i>
                        </div>
                        <div>
                            <p className="text-sm text-white/70 mb-1">Saldo Aktif</p>
                            <h2 className="text-3xl font-black">{formatRupiah(wallet?.balance)}</h2>
                        </div>
                    </div>
                    <div className="mt-4 relative z-10">
                        <button onClick={() => setShowModal(true)} className="px-5 py-2 bg-white text-[#7c3aed] text-sm font-bold rounded-lg hover:bg-white/90 transition">
                            <i className="fa-solid fa-plus me-1"></i> Top Up
                        </button>
                    </div>
                </div>
            </div>

            <div className="mt-6 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="px-6 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <h3 className="text-sm font-bold text-[#333] dark:text-white">Riwayat Transaksi</h3>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4">Tanggal/Waktu</th>
                                <th className="px-6 py-4">Keterangan</th>
                                <th className="px-6 py-4 text-center">Tipe</th>
                                <th className="px-6 py-4 text-right">Jumlah</th>
                                <th className="px-6 py-4 text-center">Status</th>
                                <th className="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {transactions?.data?.length > 0 ? transactions.data.map((tx, idx) => (
                                <tr key={tx.id || idx} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <p className="text-[#333] dark:text-white">{formatDate(tx.created_at)}</p>
                                        <p className="text-xs text-[#999] dark:text-white/40">{formatTime(tx.created_at)}</p>
                                    </td>
                                    <td className="px-6 py-4">
                                        <p className="font-medium text-[#333] dark:text-white">{tx.description || tx.type}</p>
                                        {tx.reference_id && <p className="text-xs text-[#999] dark:text-white/40 font-mono">{tx.reference_id}</p>}
                                    </td>
                                    <td className="px-6 py-4 text-center">
                                        {tx.type === 'credit' ? (
                                            <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40">
                                                <i className="fa-solid fa-arrow-down text-[10px]"></i> MASUK
                                            </span>
                                        ) : (
                                            <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-500/40">
                                                <i className="fa-solid fa-arrow-up text-[10px]"></i> KELUAR
                                            </span>
                                        )}
                                    </td>
                                    <td className={`px-6 py-4 text-right font-mono font-medium ${tx.type === 'credit' ? 'text-emerald-600 dark:text-emerald-300' : 'text-rose-600 dark:text-rose-300'}`}>
                                        {tx.type === 'credit' ? '+' : '-'}{formatRupiah(tx.amount)}
                                    </td>
                                    <td className="px-6 py-4 text-center">{statusBadge(tx.status)}</td>
                                    <td className="px-6 py-4 text-center">
                                        {tx.status === 'pending' && tx.type === 'credit' && (
                                            <button onClick={() => handlePay(tx)} className="px-3 py-1.5 bg-[#7c3aed] text-white text-xs font-bold rounded-lg hover:bg-[#6d28d9] transition">
                                                Bayar
                                            </button>
                                        )}
                                    </td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan="6" className="px-6 py-12 text-center text-[#999] dark:text-white/40">
                                        <i className="fa-solid fa-receipt text-3xl mb-3 text-slate-300 dark:text-slate-400 block"></i>
                                        <p className="text-sm">Belum ada riwayat transaksi.</p>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {hasPages && (
                    <div className="px-6 py-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-center gap-1">
                        {paginationLinks.map((link, i) => (
                            <button
                                key={i}
                                disabled={!link.url}
                                onClick={() => link.url && router.get(link.url, {}, { preserveState: true, replace: true })}
                                className={`px-3 py-1.5 text-[13px] font-medium rounded-lg transition ${
                                    link.active
                                        ? 'bg-[#7c3aed] text-white shadow-sm'
                                        : link.url
                                            ? 'text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5'
                                            : 'text-[#ccc] dark:text-white/20 cursor-not-allowed'
                                }`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>

            {showModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div className="absolute inset-0 bg-black/40" onClick={() => setShowModal(false)}></div>
                    <div className="relative bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-2xl shadow-xl w-full max-w-md p-6">
                        <div className="flex items-center justify-between mb-5">
                            <h3 className="text-lg font-bold text-[#333] dark:text-white">Top Up Wallet</h3>
                            <button onClick={() => setShowModal(false)} className="w-8 h-8 flex items-center justify-center text-[#999] dark:text-white/40 hover:text-[#333] dark:hover:text-white transition">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <div className="grid grid-cols-3 gap-2 mb-4">
                            {presets.map((p) => (
                                <button
                                    key={p}
                                    onClick={() => setTopUpAmount(String(p))}
                                    className={`px-3 py-2.5 rounded-xl text-sm font-bold border transition ${
                                        parseInt(topUpAmount) === p
                                            ? 'bg-[#7c3aed] text-white border-[#7c3aed]'
                                            : 'bg-[#fafafa] dark:bg-white/5 text-[#333] dark:text-white border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed] hover:text-[#7c3aed]'
                                    }`}
                                >
                                    {p >= 1000000 ? `${p / 1000000}M` : `${p / 1000}K`}
                                </button>
                            ))}
                        </div>

                        <form onSubmit={handleTopUp}>
                            <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Nominal (min. Rp 10.000)</label>
                            <input
                                type="number"
                                min="10000"
                                value={topUpAmount}
                                onChange={(e) => setTopUpAmount(e.target.value)}
                                placeholder="Masukkan nominal..."
                                className={inputCls}
                            />
                            <div className="flex justify-end gap-2 mt-6">
                                <button type="button" onClick={() => setShowModal(false)} className="px-4 py-2.5 bg-[#fafafa] dark:bg-white/5 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 text-sm font-medium rounded-lg hover:bg-[#f5f0ff] dark:hover:bg-white/10 transition">
                                    Batal
                                </button>
                                <button type="submit" disabled={!topUpAmount || parseInt(topUpAmount) < 10000} className="px-5 py-2.5 bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-sm font-bold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    Lanjutkan Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
