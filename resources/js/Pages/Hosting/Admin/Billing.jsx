import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router } from '@inertiajs/react';
import { useState, useRef, useEffect } from 'react';

const statusConfig = {
    paid: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
    unpaid: 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300',
    failed: 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300',
};

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const day = d.getDate();
    const month = months[d.getMonth()];
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${day} ${month} ${year}, ${hours}:${minutes}`;
}

function formatRupiah(amount) {
    return `Rp${Number(amount || 0).toLocaleString('id-ID')}`;
}

export default function Billing({ payments }) {
    const [verifyModal, setVerifyModal] = useState(null);
    const [verifyStatus, setVerifyStatus] = useState('unpaid');
    const modalRef = useRef(null);
    const innerRef = useRef(null);

    const paginationLinks = payments?.links || [];
    const hasPages = paginationLinks && paginationLinks.length > 3;

    function openVerify(payment) {
        setVerifyModal(payment);
        setVerifyStatus(payment.status || 'unpaid');
    }

    function closeVerify() {
        setVerifyModal(null);
    }

    function handleVerify(e) {
        e.preventDefault();
        if (!verifyModal) return;
        router.put(route('admin_hosting.billing.verify', verifyModal.hashid), { status: verifyStatus }, {
            onSuccess: () => closeVerify(),
        });
    }

    useEffect(() => {
        function handleClickOutside(e) {
            if (verifyModal && modalRef.current && !innerRef.current?.contains(e.target)) {
                closeVerify();
            }
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, [verifyModal]);

    return (
        <DashboardLayout title="Kelola Tagihan Hosting">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-indigo-50 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i className="fa-solid fa-file-invoice-dollar text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Kelola Tagihan Hosting</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Verifikasi dan kelola status pembayaran klien.</p>
                    </div>
                </div>
            </div>

            <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4">Invoice / Tanggal</th>
                                <th className="px-6 py-4">Project / Klien</th>
                                <th className="px-6 py-4">Metode Pembayaran</th>
                                <th className="px-6 py-4">Total</th>
                                <th className="px-6 py-4 text-center">Status</th>
                                <th className="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {(Array.isArray(payments) ? payments : payments?.data)?.length > 0
                                ? (Array.isArray(payments) ? payments : payments.data).map((payment) => (
                                    <tr key={payment.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="font-bold text-[#333] dark:text-white">{payment.invoice_number}</div>
                                            <div className="text-xs text-[#999] dark:text-white/40 mt-1 font-mono">
                                                {formatDate(payment.created_at)}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="font-semibold text-[#333] dark:text-white">{payment.project?.project_name ?? 'Langganan Akun'}</div>
                                            <div className="text-xs text-[#999] dark:text-white/40">{payment.user?.name ?? (payment.project?.client?.name ?? '-')}</div>
                                        </td>
                                        <td className="px-6 py-4 uppercase text-xs font-semibold text-[#999] dark:text-white/40">
                                            {payment.payment_method || 'BELUM DIPILIH'}
                                        </td>
                                        <td className="px-6 py-4 font-mono font-medium text-[#333] dark:text-white">
                                            {formatRupiah(payment.amount)}
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <span className={`text-xs font-bold px-2 py-1 rounded-full ${statusConfig[payment.status] || 'bg-slate-100 text-slate-700'}`}>
                                                {(payment.status || '').toUpperCase()}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <button
                                                onClick={() => openVerify(payment)}
                                                className="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-600 hover:text-white transition-all duration-200 shadow-sm"
                                                title="Verifikasi Tagihan"
                                            >
                                                <i className="fa-solid fa-clipboard-check"></i>
                                            </button>
                                        </td>
                                    </tr>
                                )) : (
                                <tr>
                                    <td colSpan="6" className="px-6 py-10 text-center text-[#999] dark:text-white/40">Belum ada riwayat tagihan.</td>
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

            {verifyModal && (
                <div ref={modalRef} className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div ref={innerRef} className="w-full max-w-md bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl overflow-hidden">
                        <div className="p-6 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <h3 className="text-lg font-bold text-[#333] dark:text-white">Update Status Tagihan</h3>
                            <button onClick={closeVerify}
                                className="text-[#999] dark:text-white/40 hover:text-red-500 transition-colors p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10">
                                <i className="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <form onSubmit={handleVerify}>
                            <div className="p-6">
                                <p className="text-sm text-[#999] dark:text-white/40 mb-4">
                                    Ubah status untuk tagihan <strong className="text-[#333] dark:text-white">{verifyModal.invoice_number}</strong>:
                                </p>
                                <select
                                    value={verifyStatus}
                                    onChange={(e) => setVerifyStatus(e.target.value)}
                                    className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                                >
                                    <option value="unpaid">UNPAID (Belum Lunas)</option>
                                    <option value="paid">PAID (Lunas)</option>
                                    <option value="failed">FAILED (Gagal/Dibatalkan)</option>
                                </select>
                            </div>
                            <div className="p-6 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-end gap-3">
                                <button type="button" onClick={closeVerify}
                                    className="px-5 py-2.5 text-sm font-medium text-[#666] dark:text-white/60 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl hover:bg-[#fafafa] dark:hover:bg-white/5 transition-colors">
                                    Batal
                                </button>
                                <button type="submit"
                                    className="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition-all">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
