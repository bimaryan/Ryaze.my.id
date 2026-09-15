import DashboardLayout from '../../Layouts/DashboardLayout';
import { router } from '@inertiajs/react';

function statusBadge(status) {
    const cls = {
        pending: 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300',
        approved: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
        completed: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
        rejected: 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300',
    };
    return <span className={`text-xs font-bold px-2 py-1 rounded-full ${cls[status] || 'bg-[#fafafa] dark:bg-white/5 text-[#666] dark:text-white/60'}`}>{status?.toUpperCase()}</span>;
}

function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return { date: `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`, time: `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}` };
}

export default function Withdrawals({ withdrawals }) {
    function handleApprove(id) {
        if (confirm('Tandai selesai? Pastikan dana sudah ditransfer.')) {
            router.patch(route('superadmin.withdrawals.update', { id: id }), { status: 'completed' }, { preserveScroll: true });
        }
    }

    function handleReject(id) {
        if (confirm('Tolak penarikan dan kembalikan saldo ke user?')) {
            router.patch(route('superadmin.withdrawals.update', { id: id }), { status: 'rejected' }, { preserveScroll: true });
        }
    }

    const paginationLinks = withdrawals?.links || [];
    const hasPages = paginationLinks.length > 3;

    return (
        <DashboardLayout title="Kelola Penarikan Dana">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-money-bill-transfer text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Kelola Penarikan Dana (Withdrawals)</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Manajemen permohonan penarikan dana dari user.</p>
                    </div>
                </div>
            </div>

            <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4">Tanggal</th>
                                <th className="px-6 py-4">User</th>
                                <th className="px-6 py-4">Nominal</th>
                                <th className="px-6 py-4">Rekening Tujuan</th>
                                <th className="px-6 py-4">Status</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {withdrawals?.data?.length > 0 ? withdrawals.data.map((w) => {
                                const dt = formatDate(w.created_at);
                                return (
                                    <tr key={w.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="text-sm font-bold text-[#333] dark:text-white">{dt.date}</div>
                                            <div className="text-xs text-[#999] dark:text-white/40 font-mono">{dt.time}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="text-sm font-bold text-[#333] dark:text-white">{w.user?.name || 'User Terhapus'}</div>
                                            <div className="text-xs text-[#999] dark:text-white/40">{w.user?.email || ''}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="text-sm font-black text-[#7c3aed] dark:text-[#a78bfa] font-mono">{formatRupiah(w.amount)}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="text-sm font-bold text-[#333] dark:text-white">{w.bank_name}</div>
                                            <div className="text-xs text-[#666] dark:text-white/60">{w.account_number} - {w.account_name}</div>
                                        </td>
                                        <td className="px-6 py-4">{statusBadge(w.status)}</td>
                                        <td className="px-6 py-4 text-right">
                                            {w.status === 'pending' ? (
                                                <div className="flex items-center justify-end gap-2">
                                                    <button onClick={() => handleApprove(w.id)} className="bg-emerald-100 dark:bg-emerald-500/20 hover:bg-emerald-200 text-emerald-700 dark:text-emerald-300 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                        <i className="fa-solid fa-check"></i> Selesai
                                                    </button>
                                                    <button onClick={() => handleReject(w.id)} className="bg-red-100 dark:bg-red-500/20 hover:bg-red-200 text-red-700 dark:text-red-300 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                        <i className="fa-solid fa-xmark"></i> Tolak
                                                    </button>
                                                </div>
                                            ) : (
                                                <span className="text-xs text-[#999] dark:text-white/40">Tidak ada aksi</span>
                                            )}
                                        </td>
                                    </tr>
                                );
                            }) : (
                                <tr>
                                    <td colSpan="6" className="px-6 py-10 text-center text-[#999] dark:text-white/40">Belum ada permohonan penarikan dana.</td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {hasPages && (
                    <div className="px-6 py-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-center gap-1">
                        {paginationLinks.map((link, i) => (
                            <button key={i} disabled={!link.url} onClick={() => link.url && router.get(link.url, {}, { preserveState: true, replace: true })} className={`px-3 py-1.5 text-[13px] font-medium rounded-lg transition ${link.active ? 'bg-[#7c3aed] text-white shadow-sm' : link.url ? 'text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5' : 'text-[#ccc] dark:text-white/20 cursor-not-allowed'}`} dangerouslySetInnerHTML={{ __html: link.label }} />
                        ))}
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
