import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router } from '@inertiajs/react';
import { useState } from 'react';
import Swal from 'sweetalert2';

const statusConfig = {
    paid: { text: 'PAID', color: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' },
    unpaid: { text: 'UNPAID', color: 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300' },
    failed: { text: 'FAILED', color: 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300' },
};

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

function formatRupiah(amount) {
    return `Rp${Number(amount || 0).toLocaleString('id-ID')}`;
}

export default function Billing({ billings }) {
    const billingList = Array.isArray(billings) ? billings : (billings?.data || []);
    const paginationLinks = billings?.links || [];
    const hasPages = paginationLinks.length > 3;

    return (
        <DashboardLayout title="Riwayat Tagihan">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-indigo-50 dark:bg-indigo-500/20 flex items-center justify-center">
                        <i className="fa-solid fa-file-invoice-dollar text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Riwayat Tagihan</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Lihat dan kelola semua tagihan hosting Anda.</p>
                    </div>
                </div>
            </div>

            <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4">Invoice / Tanggal</th>
                                <th className="px-6 py-4">Deskripsi</th>
                                <th className="px-6 py-4 text-right">Jumlah</th>
                                <th className="px-6 py-4">Metode</th>
                                <th className="px-6 py-4 text-center">Status</th>
                                <th className="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {billingList.length > 0 ? billingList.map((billing) => {
                                const st = statusConfig[billing.status] || statusConfig.failed;
                                return (
                                    <tr key={billing.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="font-bold text-[#333] dark:text-white">{billing.invoice_number}</div>
                                            <div className="text-xs text-[#999] dark:text-white/40 mt-1">{formatDate(billing.created_at)}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="font-medium text-[#666] dark:text-white/60">{billing.description || billing.plan?.name || 'Langganan Hosting'}</span>
                                        </td>
                                        <td className="px-6 py-4 text-right font-mono font-medium text-[#333] dark:text-white">
                                            {formatRupiah(billing.amount)}
                                        </td>
                                        <td className="px-6 py-4 uppercase text-xs font-semibold text-[#999] dark:text-white/40">
                                            {billing.payment_method || '-'}
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <span className={`text-[11px] font-bold px-2.5 py-1 rounded-full ${st.color}`}>
                                                {st.text}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <div className="flex justify-center gap-2">
                                                {billing.status === 'unpaid' && billing.payment_url && (
                                                    <a href={billing.payment_url} target="_blank" rel="noopener noreferrer"
                                                        className="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition shadow-sm">
                                                        <i className="fa-solid fa-credit-card"></i> Bayar
                                                    </a>
                                                )}
                                                {billing.status === 'unpaid' && (
                                                    <button
                                                        onClick={() => handlePayWithSaldo(billing)}
                                                        className="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-500/10 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-500/20 transition">
                                                        <i className="fa-solid fa-wallet"></i> Saldo
                                                    </button>
                                                )}
                                                {billing.status === 'paid' && (
                                                    <span className="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-300 font-medium">
                                                        <i className="fa-solid fa-circle-check"></i> Lunas
                                                    </span>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                );
                            }) : (
                                <tr>
                                    <td colSpan="6" className="px-6 py-12 text-center">
                                        <div className="text-[#999] dark:text-white/40">
                                            <div className="w-16 h-16 bg-slate-100 dark:bg-slate-700/50 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                                                <i className="fa-solid fa-file-invoice-dollar"></i>
                                            </div>
                                            <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Belum ada tagihan</h3>
                                            <p className="text-sm">Tagihan akan muncul di sini setelah Anda berlangganan.</p>
                                        </div>
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

function handlePayWithSaldo(billing) {
    Swal.fire({
        title: 'Bayar dengan Saldo?',
        text: `Tagihan ${billing.invoice_number} sebesar ${formatRupiah(billing.amount)} akan dibayar dari saldo wallet Anda.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        confirmButtonText: 'Ya, Bayar!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('user_hosting.billing.pay_wallet'), {
                billing_id: billing.hashid,
            });
        }
    });
}
