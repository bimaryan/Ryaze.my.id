import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router, usePage } from '@inertiajs/react';
import { useState } from 'react';

function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

function statusBadge(status) {
    if (status === 'paid') return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40">Dibayar</span>;
    if (status === 'pending') return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-300 border border-amber-200 dark:border-amber-500/40">Menunggu</span>;
    return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-500/40">Dibatalkan</span>;
}

export default function Affiliate({ user, totalReferrals, totalCommission, commissions }) {
    const { url } = usePage();
    const [copied, setCopied] = useState(false);

    const referralLink = typeof window !== 'undefined' ? `${window.location.origin}/register?ref=${user?.referral_code || ''}` : '';
    const referralCode = user?.referral_code || '-';

    const handleCopy = () => {
        navigator.clipboard.writeText(referralLink).then(() => {
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        });
    };

    const paginationLinks = commissions?.links || [];
    const hasPages = paginationLinks.length > 3;

    return (
        <DashboardLayout title="Program Affiliate">
            <div className="mb-1">
                <div className="flex items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-users-viewfinder text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Program Affiliate</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Undang teman dan dapatkan komisi dari setiap referral.</p>
                    </div>
                </div>
            </div>

            <div className="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] relative overflow-hidden">
                    <div className="absolute -right-4 -top-4 w-24 h-24 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 rounded-full"></div>
                    <div className="relative z-10">
                        <p className="text-sm font-medium text-[#999] dark:text-white/40 mb-1">Total Undangan</p>
                        <h3 className="text-2xl font-bold text-[#333] dark:text-white">{totalReferrals || 0}</h3>
                        <p className="text-xs text-[#999] dark:text-white/40 mt-1">Pengguna terdaftar</p>
                    </div>
                </div>
                <div className="bg-gradient-to-br from-[#7c3aed] to-[#5b21b6] p-6 rounded-2xl shadow-sm relative overflow-hidden">
                    <div className="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full"></div>
                    <div className="relative z-10">
                        <p className="text-sm font-medium text-white/70 mb-1">Total Komisi</p>
                        <h3 className="text-2xl font-black text-white">{formatRupiah(totalCommission)}</h3>
                        <p className="text-xs text-white/50 mt-1">Pendapatan dari referral</p>
                    </div>
                </div>
            </div>

            <div className="mt-6 bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="px-6 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <h3 className="text-sm font-bold text-[#333] dark:text-white flex items-center gap-2">
                        <i className="fa-solid fa-link text-[#7c3aed] dark:text-[#a78bfa]"></i> Link Referral Anda
                    </h3>
                </div>
                <div className="p-6">
                    <div className="flex items-center gap-3">
                        <div className="flex-1 bg-[#fafafa] dark:bg-white/5 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-3 flex items-center">
                            <span className="text-[13px] font-mono text-[#666] dark:text-white/60 truncate">{referralLink}</span>
                        </div>
                        <button onClick={handleCopy} className={`px-5 py-3 text-sm font-bold rounded-xl transition ${copied ? 'bg-emerald-500 text-white' : 'bg-[#7c3aed] text-white hover:bg-[#6d28d9]'}`}>
                            {copied ? '<i className="fa-solid fa-check me-1"></i>Tersalin' : '<i className="fa-solid fa-copy me-1"></i>Salin'}
                        </button>
                    </div>
                    <p className="text-xs text-[#999] dark:text-white/40 mt-2">Kode referral: <span className="font-mono font-bold text-[#7c3aed] dark:text-[#a78bfa]">{referralCode}</span></p>
                </div>
            </div>

            <div className="mt-6 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="px-6 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <h3 className="text-sm font-bold text-[#333] dark:text-white">Riwayat Komisi</h3>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4">Tanggal</th>
                                <th className="px-6 py-4">Pengguna</th>
                                <th className="px-6 py-4">Keterangan</th>
                                <th className="px-6 py-4 text-right">Komisi</th>
                                <th className="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {commissions?.data?.length > 0 ? commissions.data.map((row, idx) => (
                                <tr key={row.id || idx} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-6 py-4 text-xs text-[#999] dark:text-white/40 whitespace-nowrap">{formatDate(row.created_at)}</td>
                                    <td className="px-6 py-4 font-medium text-[#333] dark:text-white">{row.user?.name || '-'}</td>
                                    <td className="px-6 py-4 text-xs text-[#666] dark:text-white/60">{row.description || '-'}</td>
                                    <td className="px-6 py-4 text-right font-mono font-medium text-emerald-600 dark:text-emerald-300">+{formatRupiah(row.amount)}</td>
                                    <td className="px-6 py-4 text-center">{statusBadge(row.status)}</td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan="5" className="px-6 py-12 text-center text-[#999] dark:text-white/40">
                                        <i className="fa-solid fa-users-viewfinder text-3xl mb-3 text-slate-300 dark:text-slate-400 block"></i>
                                        <p className="text-sm">Belum ada riwayat komisi. Mulai ajak teman Anda!</p>
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
        </DashboardLayout>
    );
}
