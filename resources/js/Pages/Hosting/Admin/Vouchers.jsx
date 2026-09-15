import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router, Link } from '@inertiajs/react';
import Swal from 'sweetalert2';

function formatDate(dateStr) {
    if (!dateStr) return 'Selamanya';
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

function isVoucherValid(voucher) {
    if (!voucher.is_active) return false;
    if (voucher.max_uses && voucher.uses >= voucher.max_uses) return false;
    if (voucher.expires_at && new Date(voucher.expires_at) < new Date()) return false;
    return true;
}

export default function Vouchers({ vouchers }) {
    const paginationLinks = vouchers?.links || [];
    const hasPages = paginationLinks.length > 3;

    function handleDelete(voucher) {
        Swal.fire({
            title: 'Hapus Voucher?',
            text: 'Apakah Anda yakin ingin menghapus voucher ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('admin_hosting.vouchers.destroy', { hashid: voucher.hashid }));
            }
        });
    }

    return (
        <DashboardLayout title="Kelola Voucher">
            <div className="mb-1">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-indigo-50 dark:bg-indigo-500/20 flex items-center justify-center">
                            <i className="fa-solid fa-ticket text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <div>
                            <h1 className="text-lg font-bold text-[#333] dark:text-white">Kelola Voucher</h1>
                            <p className="text-[13px] text-[#999] dark:text-white/40">Buat dan kelola voucher diskon untuk klien.</p>
                        </div>
                    </div>
                    <Link
                        href={route('admin_hosting.vouchers.create')}
                        className="inline-flex items-center justify-center bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-medium px-4 py-2.5 rounded-lg text-sm transition-colors shadow-sm"
                    >
                        <i className="fa-solid fa-plus mr-2"></i> Tambah Voucher
                    </Link>
                </div>
            </div>

            <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4">Kode Voucher</th>
                                <th className="px-6 py-4">Diskon</th>
                                <th className="px-6 py-4">Penggunaan</th>
                                <th className="px-6 py-4">Berlaku Sampai</th>
                                <th className="px-6 py-4 text-center">Status</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {vouchers?.data?.length > 0 ? vouchers.data.map((voucher) => {
                                const valid = isVoucherValid(voucher);
                                return (
                                    <tr key={voucher.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="font-bold text-[#333] dark:text-white font-mono">{voucher.code}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            {voucher.discount_percentage ? (
                                                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 dark:bg-purple-500/20 text-purple-800 dark:text-purple-200">
                                                    Diskon {voucher.discount_percentage}%
                                                </span>
                                            ) : voucher.discount_amount ? (
                                                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-200">
                                                    Potongan {formatRupiah(voucher.discount_amount)}
                                                </span>
                                            ) : null}
                                        </td>
                                        <td className="px-6 py-4 text-sm text-[#666] dark:text-white/60">
                                            {voucher.uses} / {voucher.max_uses ?? 'Tak Terbatas'}
                                        </td>
                                        <td className="px-6 py-4 text-sm text-[#666] dark:text-white/60">
                                            {formatDate(voucher.expires_at)}
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            {valid ? (
                                                <span className="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">AKTIF</span>
                                            ) : (
                                                <span className="text-xs font-bold px-2.5 py-1 rounded-full bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300">TIDAK AKTIF</span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <div className="flex items-center justify-end gap-2">
                                                <Link
                                                    href={route('admin_hosting.vouchers.edit', { hashid: voucher.hashid })}
                                                    className="w-8 h-8 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-600 hover:text-white transition-all shadow-sm"
                                                    title="Edit Voucher"
                                                >
                                                    <i className="fa-solid fa-pen-to-square"></i>
                                                </Link>
                                                <button
                                                    onClick={() => handleDelete(voucher)}
                                                    className="w-8 h-8 rounded-lg flex items-center justify-center text-rose-600 dark:text-rose-300 bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-600 hover:text-white transition-all shadow-sm"
                                                    title="Hapus Voucher"
                                                >
                                                    <i className="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                );
                            }) : (
                                <tr>
                                    <td colSpan="6" className="px-6 py-10 text-center text-[#999] dark:text-white/40">Belum ada voucher yang dibuat.</td>
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
