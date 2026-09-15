import DashboardLayout from '../../../Layouts/DashboardLayout';
import { Link } from '@inertiajs/react';

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

function getMiniBarColor(percent) {
    if (percent >= 50) return 'bg-indigo-500';
    if (percent >= 20) return 'bg-indigo-400';
    return 'bg-indigo-300';
}

export default function StorageDetail({ items, total_used, total_human, limit_bytes, limit_human, percent, path }) {
    const itemList = Array.isArray(items) ? items : (items?.data || []);
    const freeBytes = Math.max(0, (limit_bytes || 0) - (total_used || 0));
    const units = ['B', 'KB', 'MB', 'GB'];
    const i = freeBytes > 0 ? Math.floor(Math.log(freeBytes) / Math.log(1024)) : 0;
    const freeHuman = `${(freeBytes / Math.pow(1024, Math.max(i, 0))).toFixed(1)} ${units[Math.min(i, 3)]}`;

    return (
        <DashboardLayout title="Storage Detail">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i className="fa-solid fa-folder-open text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Storage Detail</h1>
                        {path && (
                            <p className="text-xs text-[#999] dark:text-white/40 font-mono mt-1 px-2 py-0.5 bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded inline-block truncate max-w-full">
                                {path}
                            </p>
                        )}
                    </div>
                    <div className="ml-auto">
                        <Link href={route('user_hosting.storage')}
                            className="inline-flex items-center gap-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="mt-6 bg-white dark:bg-[#0d0d18] rounded-xl border border-[#e5e5e5] dark:border-[#1a1a2e] shadow-sm p-5">
                <h3 className="font-bold text-[#333] dark:text-white text-sm mb-3 flex items-center gap-2">
                    <i className="fa-solid fa-lightbulb text-amber-400 dark:text-amber-300"></i> Tips Hemat Storage
                </h3>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-[#999] dark:text-white/40">
                    <div className="flex items-start gap-2">
                        <i className="fa-solid fa-circle-check text-emerald-400 dark:text-emerald-300 mt-0.5 shrink-0"></i>
                        <span>Folder <code className="bg-slate-100 dark:bg-slate-700/50 px-1 rounded">node_modules</code> bisa sangat besar. Jalankan <code className="bg-slate-100 dark:bg-slate-700/50 px-1 rounded">npm install</code> saat deploy, bukan di-commit ke Git.</span>
                    </div>
                    <div className="flex items-start gap-2">
                        <i className="fa-solid fa-circle-check text-emerald-400 dark:text-emerald-300 mt-0.5 shrink-0"></i>
                        <span>Folder <code className="bg-slate-100 dark:bg-slate-700/50 px-1 rounded">vendor</code> di Laravel juga auto-generated. Cukup commit <code className="bg-slate-100 dark:bg-slate-700/50 px-1 rounded">composer.json</code> saja.</span>
                    </div>
                    <div className="flex items-start gap-2">
                        <i className="fa-solid fa-circle-check text-emerald-400 dark:text-emerald-300 mt-0.5 shrink-0"></i>
                        <span>Hapus log lama di <code className="bg-slate-100 dark:bg-slate-700/50 px-1 rounded">storage/logs</code> secara berkala.</span>
                    </div>
                    <div className="flex items-start gap-2">
                        <i className="fa-solid fa-circle-check text-emerald-400 dark:text-emerald-300 mt-0.5 shrink-0"></i>
                        <span>Gunakan tab <strong>Terminal</strong> untuk menjalankan <code className="bg-slate-100 dark:bg-slate-700/50 px-1 rounded">rm -rf storage/logs/*.log</code>.</span>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 mt-6">
                <div className="bg-white dark:bg-[#0d0d18] rounded-xl border border-[#e5e5e5] dark:border-[#1a1a2e] shadow-sm p-5 flex flex-col justify-between">
                    <div>
                        <p className="text-xs text-[#999] dark:text-white/40 font-semibold uppercase tracking-wide mb-1">Terpakai</p>
                        <p className="text-2xl font-bold text-[#333] dark:text-white">{total_human}</p>
                        <p className="text-xs text-[#999] dark:text-white/40 mt-1">dari {limit_human}</p>
                    </div>
                </div>
                <div className="bg-white dark:bg-[#0d0d18] rounded-xl border border-[#e5e5e5] dark:border-[#1a1a2e] shadow-sm p-5">
                    <p className="text-xs text-[#999] dark:text-white/40 font-semibold uppercase tracking-wide mb-1">Tersedia</p>
                    <p className="text-2xl font-bold text-emerald-600 dark:text-emerald-300">{freeHuman}</p>
                    <p className="text-xs text-[#999] dark:text-white/40 mt-1">sisa kapasitas</p>
                </div>
                <div className="bg-white dark:bg-[#0d0d18] rounded-xl border border-[#e5e5e5] dark:border-[#1a1a2e] shadow-sm p-5 flex flex-col justify-center">
                    <p className="text-xs text-[#999] dark:text-white/40 font-semibold uppercase tracking-wide mb-2">Penggunaan</p>
                    <div className="w-full bg-slate-100 dark:bg-slate-700/50 rounded-full h-2.5 overflow-hidden mb-1">
                        <div className={`${getBarColor(percent)} h-2.5 rounded-full`} style={{ width: `${percent}%` }}></div>
                    </div>
                    <p className={`${getTextColor(percent)} text-sm font-bold`}>{percent}%</p>
                </div>
            </div>

            {percent >= 90 && (
                <div className="mb-6 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/40 rounded-xl px-5 py-4 flex items-start gap-3">
                    <i className="fa-solid fa-triangle-exclamation text-rose-500 dark:text-rose-400 text-lg mt-0.5"></i>
                    <div>
                        <p className="font-bold text-rose-700 dark:text-rose-300 text-sm">Storage hampir penuh!</p>
                        <p className="text-rose-600 dark:text-rose-300 text-xs mt-0.5">Hapus file atau folder yang tidak diperlukan. Deployment baru akan gagal jika storage melebihi batas.</p>
                    </div>
                </div>
            )}

            <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="px-6 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                    <h2 className="font-bold text-[#333] dark:text-white text-sm">Breakdown Folder & File</h2>
                    <span className="text-xs text-[#999] dark:text-white/40">{itemList.length} item</span>
                </div>
                {itemList.length === 0 ? (
                    <div className="px-6 py-16 text-center">
                        <i className="fa-regular fa-folder-open text-slate-200 dark:text-slate-600 text-5xl mb-4"></i>
                        <p className="text-[#999] dark:text-white/40">Folder kosong atau project belum di-deploy.</p>
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm text-left">
                            <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <tr>
                                    <th className="px-6 py-3 text-left">Nama</th>
                                    <th className="px-4 py-3 text-right whitespace-nowrap">Ukuran</th>
                                    <th className="px-6 py-3 text-left hidden sm:table-cell sm:w-[200px]">Proporsi</th>
                                    <th className="px-4 py-3 text-right hidden sm:table-cell sm:w-[80px]">%</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {itemList.map((item, idx) => {
                                    const pc = item.percent || 0;
                                    return (
                                        <tr key={idx} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                            <td className="px-6 py-3">
                                                <div className="flex items-center gap-2 min-w-0">
                                                    {item.is_dir ? (
                                                        <i className="fa-solid fa-folder text-amber-400 dark:text-amber-300 shrink-0"></i>
                                                    ) : (
                                                        <i className="fa-regular fa-file-lines text-[#999] dark:text-white/40 shrink-0"></i>
                                                    )}
                                                    <span className="font-mono text-[#333] dark:text-white text-xs truncate">{item.name}</span>
                                                    {['vendor', 'node_modules', '.git'].includes(item.name) && (
                                                        <span className="text-[10px] px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700/50 text-[#999] dark:text-white/40 rounded font-medium shrink-0">auto</span>
                                                    )}
                                                </div>
                                            </td>
                                            <td className="px-4 py-3 text-right font-semibold text-[#333] dark:text-white text-xs whitespace-nowrap">
                                                {item.human}
                                            </td>
                                            <td className="px-6 py-3 hidden sm:table-cell">
                                                <div className="w-full bg-slate-100 dark:bg-slate-700/50 rounded-full h-1.5 overflow-hidden">
                                                    <div className={`${getMiniBarColor(pc)} h-1.5 rounded-full`} style={{ width: `${pc}%` }}></div>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3 text-right text-xs text-[#999] dark:text-white/40 hidden sm:table-cell whitespace-nowrap">
                                                {pc}%
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
