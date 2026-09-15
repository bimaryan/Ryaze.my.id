import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Finance() {
    const { transactions, totalRevenue, totalCount, jokiRevenue, jokiCount, hostingRevenue, hostingCount, availableMethods, chartMonths, chartJoki, chartHosting, start, end, service, method } = usePage().props;

    const [filterStart, setFilterStart] = useState(start || '');
    const [filterEnd, setFilterEnd] = useState(end || '');
    const [filterService, setFilterService] = useState(service || '');
    const [filterMethod, setFilterMethod] = useState(method || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/superadmin/finance', {
            start: filterStart,
            end: filterEnd,
            service: filterService,
            method: filterMethod,
        }, { preserveState: true, replace: true });
    };

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
    };

    const statCards = [
        { label: 'Total Pendapatan', value: formatCurrency(totalRevenue), sub: `${totalCount} transaksi`, icon: 'fa-solid fa-chart-line', color: 'bg-[#7c3aed]' },
        { label: 'Pendapatan Joki', value: formatCurrency(jokiRevenue), sub: `${jokiCount} transaksi`, icon: 'fa-solid fa-code-branch', color: 'bg-blue-500' },
        { label: 'Pendapatan Hosting', value: formatCurrency(hostingRevenue), sub: `${hostingCount} transaksi`, icon: 'fa-solid fa-server', color: 'bg-green-500' },
    ];

    return (
        <DashboardLayout title="Keuangan">
            <div className="space-y-6">
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {statCards.map((card, i) => (
                        <div key={i} className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                            <div className="flex items-center gap-3 mb-3">
                                <div className={`w-9 h-9 ${card.color} flex items-center justify-center`}>
                                    <i className={`${card.icon} text-white text-sm`}></i>
                                </div>
                                <span className="text-[12px] font-semibold text-[#999] dark:text-white/40 uppercase tracking-wider">{card.label}</span>
                            </div>
                            <p className="text-xl font-black text-[#333] dark:text-white">{card.value}</p>
                            <p className="text-[12px] text-[#999] dark:text-white/40 mt-1">{card.sub}</p>
                        </div>
                    ))}
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white">Filter</h3>
                    </div>
                    <div className="p-5">
                        <form onSubmit={handleFilter} className="flex flex-wrap items-end gap-3">
                            <div>
                                <label className="block text-[12px] font-semibold text-[#999] dark:text-white/40 mb-1 uppercase tracking-wider">Dari</label>
                                <input
                                    type="date"
                                    value={filterStart}
                                    onChange={(e) => setFilterStart(e.target.value)}
                                    className="px-3 py-1.5 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                            </div>
                            <div>
                                <label className="block text-[12px] font-semibold text-[#999] dark:text-white/40 mb-1 uppercase tracking-wider">Sampai</label>
                                <input
                                    type="date"
                                    value={filterEnd}
                                    onChange={(e) => setFilterEnd(e.target.value)}
                                    className="px-3 py-1.5 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                            </div>
                            <div>
                                <label className="block text-[12px] font-semibold text-[#999] dark:text-white/40 mb-1 uppercase tracking-wider">Layanan</label>
                                <select
                                    value={filterService}
                                    onChange={(e) => setFilterService(e.target.value)}
                                    className="px-3 py-1.5 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                >
                                    <option value="">Semua Layanan</option>
                                    <option value="joki">Joki</option>
                                    <option value="hosting">Hosting</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-[12px] font-semibold text-[#999] dark:text-white/40 mb-1 uppercase tracking-wider">Metode</label>
                                <select
                                    value={filterMethod}
                                    onChange={(e) => setFilterMethod(e.target.value)}
                                    className="px-3 py-1.5 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                >
                                    <option value="">Semua Metode</option>
                                    {availableMethods?.map((m, i) => (
                                        <option key={i} value={m}>{m}</option>
                                    ))}
                                </select>
                            </div>
                            <button type="submit" className="px-4 py-1.5 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                                <i className="fa-solid fa-filter mr-1"></i> Filter
                            </button>
                        </form>
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Transaksi</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Sumber</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Invoice</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tanggal</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Jumlah</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Metode</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Klien</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Detail</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {transactions?.data?.length > 0 ? transactions.data.map((t) => (
                                    <tr key={t.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3">
                                            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${t.service === 'joki' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' : 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300'}`}>
                                                {t.service}
                                            </span>
                                        </td>
                                        <td className="px-5 py-3 text-[13px] font-mono text-[#666] dark:text-white/60">{t.invoice_number || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">
                                            {new Date(t.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{formatCurrency(t.amount)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{t.method || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{t.user?.name || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40 truncate max-w-[200px]">{t.description || '-'}</td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="7" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada transaksi
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
                                    <Link key={i + 1} href={`${transactions.path}?page=${i + 1}&start=${filterStart}&end=${filterEnd}&service=${filterService}&method=${filterMethod}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${transactions.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
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
