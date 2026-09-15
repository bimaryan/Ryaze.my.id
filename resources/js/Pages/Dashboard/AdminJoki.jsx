import { Link } from '@inertiajs/react';
import Chart from 'react-apexcharts';
import DashboardLayout from '../../Layouts/DashboardLayout';

function StatusCard({ icon, label, value, borderColor }) {
    return (
        <div className={`bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] border-t-4 ${borderColor} p-5 hover:-translate-y-1 hover:shadow-md transition-all duration-200`}>
            <div className="flex items-center gap-4">
                <div className="w-12 h-12 flex items-center justify-center bg-[#fafafa] dark:bg-white/[0.03] rounded-xl">
                    <i className={`${icon} text-xl`}></i>
                </div>
                <div>
                    <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">{label}</p>
                    <p className="text-2xl font-black text-[#333] dark:text-white">{value || 0}</p>
                </div>
            </div>
        </div>
    );
}

function StatusBadge({ status }) {
    const styles = {
        completed: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
        progress: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
        review: 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
        pending: 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
        cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
    };
    return (
        <span className={`text-[10px] font-bold uppercase px-2 py-0.5 rounded ${styles[status] || 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300'}`}>
            {status}
        </span>
    );
}

export default function AdminJoki({
    pendingOrders,
    progressOrders,
    reviewOrders,
    completedOrders,
    queueOrders,
    chartOrderStatus,
    chartNewOrders,
    chartCompletedOrders,
}) {
    const now = new Date();

    const completedTrendChart = chartCompletedOrders ? {
        options: {
            chart: { type: 'line', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
            colors: ['#10b981'],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05 } },
            xaxis: { categories: chartCompletedOrders.labels || [], labels: { style: { fontSize: '11px' } } },
            yaxis: { labels: { style: { fontSize: '11px' } }, min: 0 },
            grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: { theme: 'dark' },
        },
        series: [{ name: 'Selesai', data: chartCompletedOrders.series || [] }],
    } : null;

    const orderStatusChart = chartOrderStatus ? {
        options: {
            chart: { type: 'pie', fontFamily: 'Inter, sans-serif' },
            labels: chartOrderStatus.labels || [],
            colors: ['#f59e0b', '#3b82f6', '#8b5cf6', '#10b981', '#ef4444'],
            legend: { position: 'bottom', fontSize: '12px', labels: { colors: undefined } },
            dataLabels: { enabled: true, style: { fontSize: '12px' } },
            stroke: { width: 0 },
            tooltip: { theme: 'dark' },
        },
        series: chartOrderStatus.series || [],
    } : null;

    const newOrdersChart = chartNewOrders ? {
        options: {
            chart: { type: 'bar', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
            colors: ['#6366f1'],
            plotOptions: {
                bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 }
            },
            xaxis: { categories: chartNewOrders.labels || [], labels: { style: { fontSize: '11px' } } },
            yaxis: { labels: { style: { fontSize: '11px' } }, min: 0 },
            grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: { theme: 'dark' },
        },
        series: [{ name: 'Pesanan Baru', data: chartNewOrders.series || [] }],
    } : null;

    return (
        <DashboardLayout title="Admin Joki Dashboard">
            <div className="space-y-6">

                {/* === 1. STATUS CARDS (4-col grid) === */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatusCard
                        icon="fa-solid fa-clock text-amber-500"
                        label="Pesanan Baru (Pending)"
                        value={pendingOrders}
                        borderColor="border-t-amber-500"
                    />
                    <StatusCard
                        icon="fa-solid fa-spinner text-blue-500"
                        label="Sedang Dikerjakan"
                        value={progressOrders}
                        borderColor="border-t-blue-500"
                    />
                    <StatusCard
                        icon="fa-solid fa-eye text-purple-500"
                        label="Menunggu Review Klien"
                        value={reviewOrders}
                        borderColor="border-t-purple-500"
                    />
                    <StatusCard
                        icon="fa-solid fa-check-circle text-emerald-500"
                        label="Proyek Selesai Bulan Ini"
                        value={completedOrders}
                        borderColor="border-t-emerald-500"
                    />
                </div>

                {/* === 2. CHARTS SECTION (3-col grid) === */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    {/* Completed Orders Trend */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4">Tren Proyek Selesai</h3>
                        {completedTrendChart ? (
                            <Chart options={completedTrendChart.options} series={completedTrendChart.series} type="line" height={250} />
                        ) : (
                            <div className="flex items-center justify-center h-[250px] text-[12px] text-[#999] dark:text-white/40">Tidak ada data</div>
                        )}
                    </div>

                    {/* Order Status Distribution */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4">Distribusi Status Pesanan</h3>
                        {orderStatusChart ? (
                            <Chart options={orderStatusChart.options} series={orderStatusChart.series} type="pie" height={250} />
                        ) : (
                            <div className="flex items-center justify-center h-[250px] text-[12px] text-[#999] dark:text-white/40">Tidak ada data</div>
                        )}
                    </div>

                    {/* New Orders */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4">Pesanan Baru (6 Bulan)</h3>
                        {newOrdersChart ? (
                            <Chart options={newOrdersChart.options} series={newOrdersChart.series} type="bar" height={250} />
                        ) : (
                            <div className="flex items-center justify-center h-[250px] text-[12px] text-[#999] dark:text-white/40">Tidak ada data</div>
                        )}
                    </div>
                </div>

                {/* === 3. ACTIVE QUEUE TABLE === */}
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 flex items-center justify-center bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa] rounded-xl">
                                <i className="fa-solid fa-list-check text-lg"></i>
                            </div>
                            <div>
                                <h3 className="text-sm font-bold text-[#333] dark:text-white">Antrean Pekerjaan Aktif</h3>
                                <p className="text-[11px] text-[#999] dark:text-white/40">{queueOrders?.length || 0} pesanan dalam antrean</p>
                            </div>
                        </div>
                        <Link href={route('admin_joki.orders')} className="text-[12px] font-medium text-[#7c3aed] dark:text-[#a78bfa] hover:underline">
                            Lihat Semua <i className="fa-solid fa-arrow-right ml-1"></i>
                        </Link>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02]">
                                    <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Klien & Pesanan</th>
                                    <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Proyek & Tech Stack</th>
                                    <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status & Progress</th>
                                    <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Deadline</th>
                                    <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {queueOrders?.map((o) => {
                                    const isOverdue = o.deadline && new Date(o.deadline) < now;
                                    return (
                                        <tr key={o.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                            <td className="px-5 py-3">
                                                <p className="text-[13px] font-semibold text-[#333] dark:text-white">{o.client_name}</p>
                                                <p className="text-[11px] font-mono text-[#999] dark:text-white/40">{o.order_number}</p>
                                            </td>
                                            <td className="px-5 py-3">
                                                <p className="text-[13px] font-medium text-[#333] dark:text-white">{o.project_name}</p>
                                                <p className="text-[11px] text-[#999] dark:text-white/40">{o.tech_stack}</p>
                                            </td>
                                            <td className="px-5 py-3">
                                                <div className="space-y-1.5">
                                                    <StatusBadge status={o.status} />
                                                    {o.status === 'progress' && (
                                                        <div className="w-full h-1.5 bg-gray-200 dark:bg-white/10 rounded-full overflow-hidden">
                                                            <div className="h-full bg-blue-500 rounded-full transition-all duration-500" style={{ width: `${Math.min(Math.max(o.progress || 0, 0), 100)}%` }} />
                                                        </div>
                                                    )}
                                                </div>
                                            </td>
                                            <td className="px-5 py-3">
                                                {o.deadline ? (
                                                    <span className={`text-[12px] font-medium ${isOverdue ? 'text-red-600 dark:text-red-400 font-bold' : 'text-[#666] dark:text-white/60'}`}>
                                                        {isOverdue && <i className="fa-solid fa-triangle-exclamation mr-1"></i>}
                                                        {new Date(o.deadline).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                                    </span>
                                                ) : (
                                                    <span className="text-[12px] text-[#999] dark:text-white/40">-</span>
                                                )}
                                            </td>
                                            <td className="px-5 py-3">
                                                <Link
                                                    href={route('admin_joki.orders.edit', { hashid: o.hashid })}
                                                    className="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-medium text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/20 hover:bg-[#ede5ff] dark:hover:bg-[#7c3aed]/30 rounded-lg transition-colors"
                                                >
                                                    <i className="fa-solid fa-pen-to-square text-[10px]"></i>
                                                    Edit
                                                </Link>
                                            </td>
                                        </tr>
                                    );
                                })}
                                {!queueOrders?.length && (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-12 text-center">
                                            <div className="flex flex-col items-center">
                                                <i className="fa-solid fa-inbox text-3xl text-[#e5e5e5] dark:text-white/10 mb-3"></i>
                                                <p className="text-[13px] text-[#999] dark:text-white/40">Tidak ada pesanan dalam antrean</p>
                                            </div>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </DashboardLayout>
    );
}
