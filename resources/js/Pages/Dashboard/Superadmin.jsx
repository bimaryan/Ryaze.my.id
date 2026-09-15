import { useState, useEffect } from 'react';
import { router, Link } from '@inertiajs/react';
import Chart from 'react-apexcharts';
import DashboardLayout from '../../Layouts/DashboardLayout';

function formatRp(n) {
    if (!n) return 'Rp 0';
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

function formatBytes(mb) {
    if (mb >= 1024) return (mb / 1024).toFixed(1) + ' GB';
    return mb.toFixed(1) + ' MB';
}

function StatusBadge({ status }) {
    const styles = {
        completed: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
        progress: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
        active: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
        building: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
        pending: 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
        review: 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
        cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
        suspended: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
    };
    return (
        <span className={`text-[10px] font-bold uppercase px-2 py-0.5 rounded ${styles[status] || 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300'}`}>
            {status}
        </span>
    );
}

function RoleBadge({ role }) {
    const styles = {
        user_joki: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300',
        user_hosting: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
        admin_joki: 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
        admin_hosting: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300',
        superadmin: 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
    };
    return (
        <span className={`text-[10px] font-bold uppercase px-2 py-0.5 rounded ${styles[role] || 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300'}`}>
            {role?.replace('_', ' ')}
        </span>
    );
}

function ProgressBar({ value, color }) {
    const pct = Math.min(Math.max(value || 0, 0), 100);
    const barColor = pct > 80 ? 'bg-red-500' : pct > 50 ? 'bg-amber-500' : color || 'bg-green-500';
    return (
        <div className="w-full h-2 bg-gray-200 dark:bg-white/10 rounded-full overflow-hidden">
            <div className={`h-full rounded-full transition-all duration-500 ${barColor}`} style={{ width: `${pct}%` }} />
        </div>
    );
}

export default function Superadmin({
    totalUsers, activeJokiOrders, totalJokiOrders, activeHosting, totalHosting,
    jokiRevenueMonth, jokiRevenueMonthCount, jokiRevenueTotal,
    hostingRevenueMonth, hostingRevenueMonthCount, hostingRevenueTotal,
    totalRevenueMonth, totalRevenueTotal, totalDatabases, totalStorageMB,
    recentUsers, recentJokiOrders, recentHostingProjects,
    chartUserRoles, chartUserRegistrations, chartRevenue,
}) {
    const [serverStatus, setServerStatus] = useState(null);
    const [serverLoading, setServerLoading] = useState(true);
    const [serverError, setServerError] = useState(false);

    useEffect(() => {
        const fetchStatus = () => {
            fetch(route('superadmin.server_status'))
                .then(res => res.json())
                .then(data => {
                    setServerStatus(data);
                    setServerLoading(false);
                    setServerError(false);
                })
                .catch(() => {
                    setServerError(true);
                    setServerLoading(false);
                });
        };
        fetchStatus();
        const interval = setInterval(fetchStatus, 5000);
        return () => clearInterval(interval);
    }, []);

    const userRolesChart = chartUserRoles ? {
        options: {
            chart: { type: 'donut', fontFamily: 'Inter, sans-serif' },
            labels: chartUserRoles.labels || [],
            colors: ['#7c3aed', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#8b5cf6', '#ec4899'],
            legend: { position: 'bottom', fontSize: '12px', labels: { colors: undefined } },
            plotOptions: {
                pie: { donut: { size: '65%' } }
            },
            dataLabels: { enabled: false },
            stroke: { width: 0 },
            tooltip: { theme: 'dark' },
        },
        series: chartUserRoles.series || [],
    } : null;

    const userRegistrationsChart = chartUserRegistrations ? {
        options: {
            chart: { type: 'area', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
            colors: ['#7c3aed'],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
            xaxis: { categories: chartUserRegistrations.labels || [], labels: { style: { fontSize: '11px' } } },
            yaxis: { labels: { style: { fontSize: '11px' } } },
            grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: { theme: 'dark' },
        },
        series: [{ name: 'Registrasi', data: chartUserRegistrations.series || [] }],
    } : null;

    const revenueChart = chartRevenue ? {
        options: {
            chart: { type: 'bar', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
            colors: ['#7c3aed', '#06b6d4'],
            plotOptions: {
                bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 }
            },
            xaxis: { categories: chartRevenue.labels || [], labels: { style: { fontSize: '11px' } } },
            yaxis: {
                labels: {
                    style: { fontSize: '11px' },
                    formatter: (val) => 'Rp ' + Number(val).toLocaleString('id-ID'),
                }
            },
            legend: { position: 'top', fontSize: '12px' },
            grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: { theme: 'dark' },
        },
        series: [
            { name: 'Joki', data: chartRevenue.joki || [] },
            { name: 'Hosting', data: chartRevenue.hosting || [] },
        ],
    } : null;

    return (
        <DashboardLayout title="Superadmin Dashboard">
            <div className="space-y-6">

                {/* === 1. KPI CARDS (3-col grid) === */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {/* Total Users */}
                    <div className="relative bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5 overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-200">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full" />
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 flex items-center justify-center bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 rounded-xl">
                                <i className="fa-solid fa-users text-xl"></i>
                            </div>
                            <div>
                                <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Total Pengguna</p>
                                <p className="text-2xl font-black text-[#333] dark:text-white">{totalUsers || 0}</p>
                            </div>
                        </div>
                    </div>

                    {/* Pesanan Joki Aktif */}
                    <div className="relative bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5 overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-200">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/5 rounded-full" />
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 flex items-center justify-center bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 rounded-xl">
                                <i className="fa-solid fa-code-branch text-xl"></i>
                            </div>
                            <div>
                                <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Pesanan Joki Aktif</p>
                                <p className="text-2xl font-black text-[#333] dark:text-white">
                                    {activeJokiOrders || 0}
                                    <span className="text-sm font-medium text-[#999] dark:text-white/40"> / {totalJokiOrders || 0}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Pendapatan Bulan Ini */}
                    <div className="relative bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5 overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-200">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-sky-500/5 rounded-full" />
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 flex items-center justify-center bg-sky-50 text-sky-600 dark:bg-sky-500/20 dark:text-sky-400 rounded-xl">
                                <i className="fa-solid fa-chart-line text-xl"></i>
                            </div>
                            <div className="flex-1 min-w-0">
                                <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Pendapatan Bulan Ini</p>
                                <p className="text-2xl font-black text-[#333] dark:text-white">{formatRp(totalRevenueMonth)}</p>
                                <div className="flex gap-3 mt-1">
                                    <span className="text-[10px] text-[#7c3aed] dark:text-[#a78bfa]">Joki: {formatRp(jokiRevenueMonth)}</span>
                                    <span className="text-[10px] text-cyan-600 dark:text-cyan-400">Hosting: {formatRp(hostingRevenueMonth)}</span>
                                </div>
                            </div>
                            <Link href={route('superadmin.finance')} className="text-[11px] text-[#7c3aed] dark:text-[#a78bfa] hover:underline shrink-0">
                                <i className="fa-solid fa-arrow-right"></i>
                            </Link>
                        </div>
                    </div>

                    {/* Hosting Aktif */}
                    <div className="relative bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5 overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-200">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full" />
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 flex items-center justify-center bg-emerald-50 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 rounded-xl">
                                <i className="fa-solid fa-server text-xl"></i>
                            </div>
                            <div>
                                <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Hosting Aktif</p>
                                <p className="text-2xl font-black text-[#333] dark:text-white">
                                    {activeHosting || 0}
                                    <span className="text-sm font-medium text-[#999] dark:text-white/40"> / {totalHosting || 0}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Total Database */}
                    <div className="relative bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5 overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-200">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-orange-500/5 rounded-full" />
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 flex items-center justify-center bg-orange-50 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400 rounded-xl">
                                <i className="fa-solid fa-database text-xl"></i>
                            </div>
                            <div className="flex-1">
                                <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Total Database</p>
                                <p className="text-2xl font-black text-[#333] dark:text-white">{totalDatabases || 0}</p>
                            </div>
                            <Link href={route('admin_hosting.databases')} className="text-[11px] text-[#7c3aed] dark:text-[#a78bfa] hover:underline shrink-0">
                                <i className="fa-solid fa-arrow-right"></i>
                            </Link>
                        </div>
                    </div>

                    {/* Penyimpanan Teralokasi */}
                    <div className="relative bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5 overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-200">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-teal-500/5 rounded-full" />
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 flex items-center justify-center bg-teal-50 text-teal-600 dark:bg-teal-500/20 dark:text-teal-400 rounded-xl">
                                <i className="fa-solid fa-hard-drive text-xl"></i>
                            </div>
                            <div className="flex-1">
                                <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Penyimpanan Teralokasi</p>
                                <p className="text-2xl font-black text-[#333] dark:text-white">{formatBytes(totalStorageMB || 0)}</p>
                            </div>
                            <Link href={route('admin_hosting.storage')} className="text-[11px] text-[#7c3aed] dark:text-[#a78bfa] hover:underline shrink-0">
                                <i className="fa-solid fa-arrow-right"></i>
                            </Link>
                        </div>
                    </div>
                </div>

                {/* === 2. SERVER HEALTH STATUS === */}
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                    <div className="flex items-center justify-between mb-5">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 flex items-center justify-center bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa] rounded-xl">
                                <i className="fa-solid fa-heart-pulse text-lg"></i>
                            </div>
                            <div>
                                <h3 className="text-sm font-bold text-[#333] dark:text-white">Server Health Status</h3>
                                <p className="text-[11px] text-[#999] dark:text-white/40">Real-time monitoring</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-2">
                            <span className={`w-2 h-2 rounded-full ${
                                serverError ? 'bg-red-500' : serverLoading ? 'bg-amber-500 animate-pulse' : 'bg-green-500'
                            }`}></span>
                            <span className={`text-[11px] font-medium ${
                                serverError ? 'text-red-600 dark:text-red-400' : serverLoading ? 'text-amber-600 dark:text-amber-400' : 'text-green-600 dark:text-green-400'
                            }`}>
                                {serverError ? 'Disconnected' : serverLoading ? 'Loading...' : 'Online'}
                            </span>
                        </div>
                    </div>

                    {serverStatus ? (
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            {/* CPU Load */}
                            <div className="bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] p-4 rounded-lg">
                                <div className="flex items-center justify-between mb-2">
                                    <span className="text-[11px] font-medium text-[#666] dark:text-white/60">CPU Load (1m)</span>
                                    <span className={`text-sm font-bold ${
                                        (serverStatus.cpu_load || 0) > 80 ? 'text-red-600 dark:text-red-400' :
                                        (serverStatus.cpu_load || 0) > 50 ? 'text-amber-600 dark:text-amber-400' :
                                        'text-green-600 dark:text-green-400'
                                    }`}>{(serverStatus.cpu_load || 0).toFixed(1)}%</span>
                                </div>
                                <ProgressBar value={serverStatus.cpu_load} />
                            </div>

                            {/* RAM Usage */}
                            <div className="bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] p-4 rounded-lg">
                                <div className="flex items-center justify-between mb-2">
                                    <span className="text-[11px] font-medium text-[#666] dark:text-white/60">RAM</span>
                                    <span className="text-sm font-bold text-[#333] dark:text-white">
                                        {serverStatus.ram_used_gb || '0'} / {serverStatus.ram_total_gb || '0'} GB
                                    </span>
                                </div>
                                <ProgressBar value={serverStatus.ram_percent} />
                            </div>

                            {/* Disk Space */}
                            <div className="bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] p-4 rounded-lg">
                                <div className="flex items-center justify-between mb-2">
                                    <span className="text-[11px] font-medium text-[#666] dark:text-white/60">Disk Space</span>
                                    <span className="text-sm font-bold text-[#333] dark:text-white">
                                        {serverStatus.disk_free_gb || '0'} GB free
                                    </span>
                                </div>
                                <ProgressBar value={serverStatus.disk_percent} />
                            </div>

                            {/* Server Uptime */}
                            <div className="bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] p-4 rounded-lg">
                                <div className="flex items-center justify-between mb-2">
                                    <span className="text-[11px] font-medium text-[#666] dark:text-white/60">Uptime</span>
                                    <i className="fa-solid fa-clock text-[#7c3aed] dark:text-[#a78bfa]"></i>
                                </div>
                                <p className="text-sm font-bold text-[#333] dark:text-white">{serverStatus.uptime || '-'}</p>
                            </div>
                        </div>
                    ) : (
                        <div className="flex items-center justify-center py-8">
                            <div className="text-center">
                                <i className="fa-solid fa-spinner fa-spin text-2xl text-[#7c3aed] dark:text-[#a78bfa] mb-2"></i>
                                <p className="text-[12px] text-[#999] dark:text-white/40">Memuat data server...</p>
                            </div>
                        </div>
                    )}
                </div>

                {/* === 3. CHARTS SECTION (3-col grid) === */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    {/* User Registrations Trend */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4">Trend Registrasi Pengguna</h3>
                        {userRegistrationsChart ? (
                            <Chart options={userRegistrationsChart.options} series={userRegistrationsChart.series} type="area" height={250} />
                        ) : (
                            <div className="flex items-center justify-center h-[250px] text-[12px] text-[#999] dark:text-white/40">Tidak ada data</div>
                        )}
                    </div>

                    {/* User Roles Distribution */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4">Distribusi Role Pengguna</h3>
                        {userRolesChart ? (
                            <Chart options={userRolesChart.options} series={userRolesChart.series} type="donut" height={250} />
                        ) : (
                            <div className="flex items-center justify-center h-[250px] text-[12px] text-[#999] dark:text-white/40">Tidak ada data</div>
                        )}
                    </div>

                    {/* Revenue Comparison */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4">Perbandingan Pendapatan</h3>
                        {revenueChart ? (
                            <Chart options={revenueChart.options} series={revenueChart.series} type="bar" height={250} />
                        ) : (
                            <div className="flex items-center justify-center h-[250px] text-[12px] text-[#999] dark:text-white/40">Tidak ada data</div>
                        )}
                    </div>
                </div>

                {/* === 4. DETAILED REVENUE SECTION (2-col grid) === */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {/* Joki Code Revenue */}
                    <div className="relative bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5 overflow-hidden">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-[#7c3aed]/5 rounded-full" />
                        <div className="flex items-center gap-4 mb-4">
                            <div className="w-12 h-12 flex items-center justify-center bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa] rounded-xl">
                                <i className="fa-solid fa-wallet text-xl"></i>
                            </div>
                            <div>
                                <h3 className="text-sm font-bold text-[#333] dark:text-white">Pendapatan Joki Code</h3>
                                <p className="text-[11px] text-[#999] dark:text-white/40">{jokiRevenueMonthCount || 0} pesanan bulan ini</p>
                            </div>
                        </div>
                        <div className="space-y-2">
                            <div className="flex items-center justify-between p-3 bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg">
                                <span className="text-[12px] text-[#666] dark:text-white/60">Bulan Ini</span>
                                <span className="text-sm font-bold text-[#7c3aed] dark:text-[#a78bfa]">{formatRp(jokiRevenueMonth)}</span>
                            </div>
                            <div className="flex items-center justify-between p-3 bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg">
                                <span className="text-[12px] text-[#666] dark:text-white/60">Total Seluruh</span>
                                <span className="text-sm font-bold text-[#333] dark:text-white">{formatRp(jokiRevenueTotal)}</span>
                            </div>
                        </div>
                    </div>

                    {/* Hosting Revenue */}
                    <div className="relative bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5 overflow-hidden">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-cyan-500/5 rounded-full" />
                        <div className="flex items-center gap-4 mb-4">
                            <div className="w-12 h-12 flex items-center justify-center bg-cyan-50 text-cyan-600 dark:bg-cyan-500/20 dark:text-cyan-400 rounded-xl">
                                <i className="fa-solid fa-cloud text-xl"></i>
                            </div>
                            <div>
                                <h3 className="text-sm font-bold text-[#333] dark:text-white">Pendapatan Hosting</h3>
                                <p className="text-[11px] text-[#999] dark:text-white/40">{hostingRevenueMonthCount || 0} proyek bulan ini</p>
                            </div>
                        </div>
                        <div className="space-y-2">
                            <div className="flex items-center justify-between p-3 bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg">
                                <span className="text-[12px] text-[#666] dark:text-white/60">Bulan Ini</span>
                                <span className="text-sm font-bold text-cyan-600 dark:text-cyan-400">{formatRp(hostingRevenueMonth)}</span>
                            </div>
                            <div className="flex items-center justify-between p-3 bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg">
                                <span className="text-[12px] text-[#666] dark:text-white/60">Total Seluruh</span>
                                <span className="text-sm font-bold text-[#333] dark:text-white">{formatRp(hostingRevenueTotal)}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* === 5. RECENT ACTIVITIES (2-col grid) === */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    {/* Recent Joki Orders */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Pesanan Joki Terbaru</h3>
                            <Link href={route('admin_joki.orders')} className="text-[12px] font-medium text-[#7c3aed] dark:text-[#a78bfa] hover:underline">Lihat Semua</Link>
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead>
                                    <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02]">
                                        <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Pesanan</th>
                                        <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Klien</th>
                                        <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Harga</th>
                                        <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                    {recentJokiOrders?.map((o) => (
                                        <tr key={o.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                            <td className="px-5 py-3">
                                                <p className="text-[13px] font-semibold text-[#333] dark:text-white">{o.project_name}</p>
                                                <p className="text-[11px] text-[#999] dark:text-white/40">{o.order_number}</p>
                                            </td>
                                            <td className="px-5 py-3">
                                                <p className="text-[12px] text-[#666] dark:text-white/60">{o.client_name}</p>
                                                <p className="text-[11px] text-[#999] dark:text-white/40">{o.service_name}</p>
                                            </td>
                                            <td className="px-5 py-3">
                                                <span className="text-[12px] font-bold text-[#333] dark:text-white">{formatRp(o.price)}</span>
                                            </td>
                                            <td className="px-5 py-3">
                                                <StatusBadge status={o.status} />
                                            </td>
                                        </tr>
                                    ))}
                                    {!recentJokiOrders?.length && (
                                        <tr>
                                            <td colSpan="4" className="px-5 py-8 text-center text-[12px] text-[#999] dark:text-white/40">Belum ada data</td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* Recent Hosting Projects */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Project Hosting Terbaru</h3>
                            <Link href={route('admin_hosting.projects')} className="text-[12px] font-medium text-[#7c3aed] dark:text-[#a78bfa] hover:underline">Lihat Semua</Link>
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead>
                                    <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02]">
                                        <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Project</th>
                                        <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Framework</th>
                                        <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Domain</th>
                                        <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                    {recentHostingProjects?.map((p) => (
                                        <tr key={p.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                            <td className="px-5 py-3">
                                                <p className="text-[13px] font-semibold text-[#333] dark:text-white">{p.project_name}</p>
                                                <p className="text-[11px] text-[#999] dark:text-white/40">{p.client_name}</p>
                                            </td>
                                            <td className="px-5 py-3">
                                                <span className="text-[12px] text-[#666] dark:text-white/60 capitalize">{p.framework}</span>
                                            </td>
                                            <td className="px-5 py-3">
                                                <a href={`https://${p.ryaze_domain}`} target="_blank" rel="noopener noreferrer" className="text-[12px] text-[#7c3aed] dark:text-[#a78bfa] hover:underline">{p.ryaze_domain}</a>
                                            </td>
                                            <td className="px-5 py-3">
                                                <StatusBadge status={p.status} />
                                            </td>
                                        </tr>
                                    ))}
                                    {!recentHostingProjects?.length && (
                                        <tr>
                                            <td colSpan="4" className="px-5 py-8 text-center text-[12px] text-[#999] dark:text-white/40">Belum ada data</td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {/* === 6. RECENT USERS TABLE (full width) === */}
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white">Pengguna Terbaru</h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02]">
                                    <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Pengguna</th>
                                    <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Email</th>
                                    <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Role</th>
                                    <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Terdaftar</th>
                                    <th className="px-5 py-3 text-[10px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {recentUsers?.map((u) => (
                                    <tr key={u.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3">
                                            <div className="flex items-center gap-3">
                                                <div className="w-8 h-8 rounded-full bg-[#7c3aed] text-white flex items-center justify-center text-[12px] font-bold shrink-0">
                                                    {u.name?.charAt(0)?.toUpperCase()}
                                                </div>
                                                <div>
                                                    <p className="text-[13px] font-semibold text-[#333] dark:text-white">{u.name}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-5 py-3">
                                            <span className="text-[12px] text-[#666] dark:text-white/60">{u.email}</span>
                                        </td>
                                        <td className="px-5 py-3">
                                            <RoleBadge role={u.role} />
                                        </td>
                                        <td className="px-5 py-3">
                                            <span className="text-[12px] text-[#999] dark:text-white/40">{u.created_at}</span>
                                        </td>
                                        <td className="px-5 py-3">
                                            <Link href={route('superadmin.users.show', { hashid: u.hashid })} className="text-[12px] font-medium text-[#7c3aed] dark:text-[#a78bfa] hover:underline">
                                                Lihat
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                                {!recentUsers?.length && (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-8 text-center text-[12px] text-[#999] dark:text-white/40">Belum ada data</td>
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
