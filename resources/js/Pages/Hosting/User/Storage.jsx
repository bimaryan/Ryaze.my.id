import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, router, Link } from '@inertiajs/react';
import { useState } from 'react';

const frameworkIcons = {
    html: 'fa-brands fa-html5',
    php: 'fa-brands fa-php',
    laravel: 'fa-brands fa-laravel',
    react: 'fa-brands fa-react',
    nextjs: 'fa-brands fa-node-js',
    python: 'fa-brands fa-python',
    node: 'fa-brands fa-node',
    vue: 'fa-brands fa-vuejs',
};

const statusColors = {
    active: 'text-emerald-400 dark:text-emerald-300',
    building: 'text-amber-400 dark:text-amber-300',
    error: 'text-rose-400 dark:text-rose-300',
    unpaid: 'text-rose-400 dark:text-rose-300',
    suspended: 'text-slate-400 dark:text-slate-500',
};

function getBarColor(percent) {
    if (percent >= 90) return 'bg-rose-500';
    if (percent >= 70) return 'bg-amber-500';
    return 'bg-indigo-500';
}

function getTextColor(percent) {
    if (percent >= 90) return 'text-rose-600 dark:text-rose-300';
    if (percent >= 70) return 'text-amber-600 dark:text-amber-300';
    return 'text-indigo-600 dark:text-indigo-400';
}

function getBgLight(percent) {
    if (percent >= 90) return 'bg-rose-50 dark:bg-rose-500/10 border-rose-200 dark:border-rose-500/40';
    if (percent >= 70) return 'bg-amber-50 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/40';
    return 'bg-indigo-50 dark:bg-indigo-500/10 border-indigo-200 dark:border-indigo-500/40';
}

function getMiniBarColor(percent) {
    if (percent >= 90) return 'bg-rose-500';
    if (percent >= 70) return 'bg-amber-500';
    return 'bg-indigo-400';
}

export default function Storage({ items, total_used, total_human, limit_bytes, limit_human, percent }) {
    const [voucher, setVoucher] = useState('');
    const { post, processing } = useForm({ voucher_code: '' });

    const itemList = Array.isArray(items) ? items : (items?.data || []);
    const paginationLinks = items?.links || [];
    const hasPages = paginationLinks.length > 3;

    function handleUpgrade(e) {
        e.preventDefault();
        post(route('user_hosting.storage.upgrade'), { voucher_code: voucher });
    }

    return (
        <DashboardLayout title="Storage">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i className="fa-solid fa-hard-drive text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Storage</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Monitor penggunaan disk seluruh project Anda.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href={route('user_hosting.dashboard')}
                            className="inline-flex items-center gap-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] p-6 mb-6 mt-6">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                    <div>
                        <p className="text-xs font-semibold text-[#999] dark:text-white/40 uppercase tracking-wide mb-1">Total Penggunaan</p>
                        <div className="flex items-baseline gap-2">
                            <span className="text-3xl font-bold text-[#333] dark:text-white">{total_human}</span>
                            <span className="text-[#999] dark:text-white/40 text-sm">/ {limit_human}</span>
                        </div>
                    </div>
                    <div className="text-right">
                        <span className={`inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold border ${getBgLight(percent)} ${getTextColor(percent)}`}>
                            {percent >= 90 ? <i className="fa-solid fa-triangle-exclamation mr-1.5"></i> : percent >= 70 ? <i className="fa-solid fa-circle-exclamation mr-1.5"></i> : <i className="fa-solid fa-hard-drive mr-1.5"></i>}
                            {percent}% terpakai
                        </span>
                    </div>
                </div>
                <div className="w-full bg-slate-100 dark:bg-slate-700/50 rounded-full h-3 overflow-hidden">
                    <div className={`${getBarColor(percent)} h-3 rounded-full transition-all duration-700`} style={{ width: `${percent}%` }}></div>
                </div>
                <div className="flex justify-between mt-2 text-xs text-[#999] dark:text-white/40">
                    <span>{total_human} digunakan</span>
                    <span>{limit_human} batas</span>
                </div>

                {percent >= 90 && (
                    <div className="mt-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/40 rounded-lg px-4 py-3 flex items-start gap-3">
                        <i className="fa-solid fa-triangle-exclamation text-rose-500 dark:text-rose-400 mt-0.5"></i>
                        <p className="text-rose-700 dark:text-rose-300 text-sm">Storage hampir penuh! Hapus file yang tidak diperlukan atau hubungi admin untuk upgrade kapasitas.</p>
                    </div>
                )}
                {percent >= 70 && percent < 90 && (
                    <div className="mt-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/40 rounded-lg px-4 py-3 flex items-start gap-3">
                        <i className="fa-solid fa-circle-exclamation text-amber-500 dark:text-amber-400 mt-0.5"></i>
                        <p className="text-amber-700 dark:text-amber-300 text-sm">Penggunaan storage melebihi 70%. Pertimbangkan untuk membersihkan file yang tidak diperlukan.</p>
                    </div>
                )}

                <form onSubmit={handleUpgrade} className="mt-6 pt-5 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div className="flex-1 w-full max-w-sm">
                            <p className="font-bold text-[#333] dark:text-white text-sm">Butuh lebih banyak kapasitas?</p>
                            <p className="text-xs text-[#999] dark:text-white/40 mt-0.5 mb-2">Upgrade storage Anda 1GB tambahan hanya dengan Rp 15.000.</p>
                            <input type="text" value={voucher} onChange={e => setVoucher(e.target.value)} placeholder="Kode Voucher (Opsional)"
                                className="w-full px-3 py-2 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg text-sm text-[#333] dark:text-white bg-[#fafafa] dark:bg-[#0d0d18] focus:outline-none focus:border-[#7c3aed] focus:ring-1 focus:ring-[#7c3aed] transition-colors" />
                        </div>
                        <button type="submit" disabled={processing}
                            className="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 px-5 py-2.5 rounded-lg text-sm font-semibold transition shrink-0 disabled:opacity-50">
                            <i className="fa-solid fa-arrow-up-right-dots"></i> Upgrade 1GB (Rp 15.000)
                        </button>
                    </div>
                </form>
            </div>

            <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="px-6 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                    <h2 className="font-bold text-[#333] dark:text-white text-sm">Penggunaan Per Project</h2>
                    <span className="text-xs text-[#999] dark:text-white/40">{itemList.length} project</span>
                </div>
                {itemList.length === 0 ? (
                    <div className="px-6 py-16 text-center">
                        <i className="fa-solid fa-box-open text-slate-200 dark:text-slate-600 text-5xl mb-4"></i>
                        <p className="text-[#999] dark:text-white/40 font-medium">Belum ada project yang di-deploy.</p>
                    </div>
                ) : (
                    <div className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                        {itemList.map((item, idx) => {
                            const project = item.project;
                            const pc = item.percent || 0;
                            const fwIcon = frameworkIcons[project?.framework] || 'fa-solid fa-code';
                            return (
                                <div key={idx} className="px-6 py-4 hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <div className="flex items-center justify-between gap-4 mb-2">
                                        <div className="flex items-center gap-3 min-w-0">
                                            <div className="w-9 h-9 rounded-lg bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-center shrink-0">
                                                <i className={`${fwIcon} text-[#333] dark:text-white`}></i>
                                            </div>
                                            <div className="min-w-0">
                                                <p className="font-semibold text-[#333] dark:text-white text-sm truncate">{project?.project_name}</p>
                                                <p className="text-xs text-[#999] dark:text-white/40 font-mono truncate">{item.dir}</p>
                                            </div>
                                        </div>
                                        <div className="text-right shrink-0">
                                            <p className="font-bold text-[#333] dark:text-white text-sm">{item.used_human}</p>
                                            <p className="text-xs text-[#999] dark:text-white/40">{pc}%</p>
                                        </div>
                                    </div>
                                    <div className="w-full bg-slate-100 dark:bg-slate-700/50 rounded-full h-1.5 overflow-hidden">
                                        <div className={`${getMiniBarColor(pc)} h-1.5 rounded-full transition-all duration-500`} style={{ width: `${pc}%` }}></div>
                                    </div>
                                    <div className="flex items-center justify-between mt-2">
                                        <span className="text-xs text-[#999] dark:text-white/40">
                                            <i className={`fa-solid fa-circle text-xs mr-1 ${statusColors[project?.status] || 'text-slate-400 dark:text-slate-500'}`}></i>
                                            {project?.status}
                                        </span>
                                        <Link href={route('user_hosting.storage.show', project?.hashid)}
                                            className="text-[#7c3aed] dark:text-[#a78bfa] hover:text-[#6d28d9] dark:hover:text-[#7c3aed] text-sm font-bold flex items-center gap-1">
                                            Detail <i className="fa-solid fa-chevron-right text-[10px]"></i>
                                        </Link>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}
                {hasPages && (
                    <div className="px-6 py-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="flex items-center justify-center gap-1">
                            {paginationLinks.map((link, i) => (
                                <button key={i} onClick={() => link.url && router.get(link.url)} disabled={!link.url}
                                    className={`px-3 py-1.5 text-xs font-medium rounded-lg transition ${link.active ? 'bg-[#7c3aed] text-white' : link.url ? 'text-[#666] dark:text-white/60 hover:bg-[#fafafa] dark:hover:bg-white/[0.02]' : 'text-[#ccc] dark:text-white/20 cursor-not-allowed'}`}
                                    dangerouslySetInnerHTML={{ __html: link.label }} />
                            ))}
                        </div>
                    </div>
                )}
            </div>

            <p className="text-xs text-[#999] dark:text-white/40 mt-4 flex items-center gap-1.5">
                <i className="fa-solid fa-circle-info text-[#ccc] dark:text-white/20"></i>
                Limit storage gabungan: <strong>{limit_human}</strong>. Data diperbarui setiap kali halaman dimuat.
            </p>
        </DashboardLayout>
    );
}
