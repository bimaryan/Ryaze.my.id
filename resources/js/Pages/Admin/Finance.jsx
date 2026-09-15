import DashboardLayout from '../../Layouts/DashboardLayout';
import { router, usePage } from '@inertiajs/react';
import { useState } from 'react';

function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}, ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
}

export default function Finance({ stats, transactions, filters, availableMethods }) {
    const { url } = usePage();
    const params = new URLSearchParams(url.split('?')[1] || '');

    const [from, setFrom] = useState(params.get('from') || '');
    const [to, setTo] = useState(params.get('to') || '');
    const [service, setService] = useState(params.get('service') || 'all');
    const [method, setMethod] = useState(params.get('method') || '');

    function handleFilter(e) {
        e.preventDefault();
        const query = {};
        if (from) query.from = from;
        if (to) query.to = to;
        if (service !== 'all') query.service = service;
        if (method) query.method = method;
        router.get(route('superadmin.finance'), query, { preserveState: true, replace: true });
    }

    function applyPreset(preset) {
        const query = {};
        if (preset.from) query.from = preset.from;
        if (preset.to) query.to = preset.to;
        router.get(route('superadmin.finance'), query, { preserveState: true, replace: true });
    }

    const presets = [
        { label: 'Bulan Ini', from: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0], to: new Date().toISOString().split('T')[0] },
        { label: 'Bulan Lalu', from: new Date(new Date().getFullYear(), new Date().getMonth() - 1, 1).toISOString().split('T')[0], to: new Date(new Date().getFullYear(), new Date().getMonth(), 0).toISOString().split('T')[0] },
        { label: 'Tahun Ini', from: new Date().getFullYear() + '-01-01', to: new Date().toISOString().split('T')[0] },
        { label: 'Semua Waktu', from: '', to: '' },
    ];

    const paginationLinks = transactions?.links || [];
    const hasPages = paginationLinks.length > 3;
    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";

    return (
        <DashboardLayout title="Laporan Keuangan">
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-chart-pie text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Laporan Keuangan</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Pendapatan dari pembayaran berstatus Lunas sesuai rentang tanggal terpilih.</p>
                    </div>
                    <div className="ml-auto">
                        <div className="inline-flex items-center px-4 py-2 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-500/30 rounded-lg font-bold text-sm">
                            <i className="fa-solid fa-wallet me-2"></i>
                            Total Terpilih: {formatRupiah(stats?.totalRevenue)}
                        </div>
                    </div>
                </div>
            </div>

            <div className="mt-6 space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <form onSubmit={handleFilter} className="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Dari Tanggal</label>
                            <input type="date" value={from} onChange={(e) => setFrom(e.target.value)} className={inputCls} />
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Sampai Tanggal</label>
                            <input type="date" value={to} onChange={(e) => setTo(e.target.value)} className={inputCls} />
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Layanan</label>
                            <select value={service} onChange={(e) => setService(e.target.value)} className={inputCls}>
                                <option value="all">Joki + Hosting</option>
                                <option value="joki">Joki Code</option>
                                <option value="hosting">Hosting</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Metode Pembayaran</label>
                            <select value={method} onChange={(e) => setMethod(e.target.value)} className={inputCls}>
                                <option value="">Semua Metode</option>
                                {availableMethods?.map((m) => (<option key={m} value={m}>{m}</option>))}
                            </select>
                        </div>
                        <div className="md:col-span-4 flex flex-wrap items-center gap-2">
                            <button type="submit" className="inline-flex items-center px-5 py-2.5 bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-sm font-bold rounded-xl transition">
                                <i className="fa-solid fa-filter me-2"></i> Terapkan Filter
                            </button>
                            <button type="button" onClick={() => { setFrom(''); setTo(''); setService('all'); setMethod(''); router.get(route('superadmin.finance'), {}, { preserveState: true, replace: true }); }} className="inline-flex items-center px-4 py-2.5 bg-[#fafafa] dark:bg-white/5 hover:bg-[#f5f0ff] dark:hover:bg-white/10 text-[#666] dark:text-white/60 text-sm font-bold rounded-xl transition border border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <i className="fa-solid fa-rotate-left me-2"></i> Reset
                            </button>
                            <span className="mx-1 text-[#e5e5e5] dark:text-[#1a1a2e]">|</span>
                            {presets.map((preset, idx) => (
                                <button key={idx} type="button" onClick={() => applyPreset(preset)} className={`px-3 py-1.5 rounded-lg text-xs font-bold border transition ${preset.from === from && preset.to === to ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'bg-white dark:bg-[#0d0d18] text-[#666] dark:text-white/60 border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#a78bfa] hover:text-[#7c3aed] dark:hover:text-[#a78bfa]'}`}>
                                    {preset.label}
                                </button>
                            ))}
                        </div>
                    </form>
                </div>

                <div className="bg-[#f5f0ff] dark:bg-[#7c3aed]/10 border border-[#ede9fe] dark:border-[#7c3aed]/30 rounded-xl px-5 py-3 text-sm text-[#7c3aed] dark:text-[#a78bfa] flex items-start gap-3">
                    <i className="fa-solid fa-circle-info mt-0.5"></i>
                    <div>
                        <span className="font-bold">Transparansi perhitungan:</span> angka di bawah dihitung dari daftar transaksi pada tabel di halaman ini (pembayaran berstatus <span className="font-bold">Lunas</span>).
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] relative overflow-hidden">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-sky-50 dark:bg-sky-500/10 rounded-full"></div>
                        <div className="relative z-10">
                            <p className="text-sm font-medium text-[#999] dark:text-white/40 mb-1">Total Pendapatan</p>
                            <h3 className="text-2xl font-bold text-[#333] dark:text-white">{formatRupiah(stats?.totalRevenue)}</h3>
                            <p className="text-xs text-[#999] dark:text-white/40 mt-1">{stats?.totalCount || 0} transaksi lunas</p>
                        </div>
                    </div>
                    <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] relative overflow-hidden">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 rounded-full"></div>
                        <div className="relative z-10">
                            <p className="text-sm font-medium text-[#999] dark:text-white/40 mb-1">Pendapatan Joki Code</p>
                            <h3 className="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">{formatRupiah(stats?.jokiRevenue)}</h3>
                            <p className="text-xs text-[#999] dark:text-white/40 mt-1">{stats?.jokiCount || 0} transaksi lunas</p>
                        </div>
                    </div>
                    <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] relative overflow-hidden">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 dark:bg-emerald-500/10 rounded-full"></div>
                        <div className="relative z-10">
                            <p className="text-sm font-medium text-[#999] dark:text-white/40 mb-1">Pendapatan Hosting</p>
                            <h3 className="text-2xl font-bold text-emerald-700 dark:text-emerald-300">{formatRupiah(stats?.hostingRevenue)}</h3>
                            <p className="text-xs text-[#999] dark:text-white/40 mt-1">{stats?.hostingCount || 0} transaksi lunas</p>
                        </div>
                    </div>
                    <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] relative overflow-hidden">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 dark:bg-amber-500/10 rounded-full"></div>
                        <div className="relative z-10">
                            <p className="text-sm font-medium text-[#999] dark:text-white/40 mb-1">Transaksi Per Metode</p>
                            <div className="space-y-1.5 mt-1">
                                {stats?.methods && Object.entries(stats.methods).slice(0, 3).map(([key, val]) => (
                                    <div key={key} className="flex justify-between text-sm">
                                        <span className="font-medium text-[#666] dark:text-white/60">{key}</span>
                                        <span className="font-bold text-[#333] dark:text-white">{formatRupiah(val.total)}</span>
                                    </div>
                                ))}
                                {(!stats?.methods || Object.keys(stats.methods).length === 0) && (
                                    <p className="text-sm text-[#999] dark:text-white/40">Belum ada data.</p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                    <div className="px-6 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="text-lg font-bold text-[#333] dark:text-white">Daftar Transaksi Lunas</h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm text-left">
                            <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <tr>
                                    <th className="px-6 py-4">Waktu Lunas</th>
                                    <th className="px-6 py-4">Invoice</th>
                                    <th className="px-6 py-4">Klien / User</th>
                                    <th className="px-6 py-4">Layanan</th>
                                    <th className="px-6 py-4">Metode</th>
                                    <th className="px-6 py-4 text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {transactions?.data?.length > 0 ? transactions.data.map((row, idx) => (
                                    <tr key={idx} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-6 py-4 text-xs text-[#999] dark:text-white/40 font-mono">{formatDate(row.paid_at)}</td>
                                        <td className="px-6 py-4 text-xs font-mono font-semibold text-[#666] dark:text-white/60">{row.invoice}</td>
                                        <td className="px-6 py-4 font-medium text-[#333] dark:text-white">{row.client}</td>
                                        <td className="px-6 py-4">
                                            <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold ${row.source === 'joki' ? 'bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] dark:text-[#a78bfa] border border-[#ede9fe] dark:border-[#7c3aed]/30' : 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-500/30'}`}>
                                                {row.source === 'joki' ? 'Joki' : 'Hosting'}
                                            </span>
                                            <span className="text-xs text-[#999] dark:text-white/40 ms-2">{row.detail}</span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="text-xs font-medium bg-[#fafafa] dark:bg-white/5 text-[#666] dark:text-white/60 px-2.5 py-1 rounded-md border border-[#e5e5e5] dark:border-[#1a1a2e]">{row.method}</span>
                                        </td>
                                        <td className="px-6 py-4 text-right font-mono font-medium text-emerald-600 dark:text-emerald-300">+ {formatRupiah(row.amount)}</td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="6" className="px-6 py-10 text-center text-[#999] dark:text-white/40">Tidak ada transaksi lunas pada rentang tanggal tersebut.</td>
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
            </div>
        </DashboardLayout>
    );
}
