import { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import Chart from 'react-apexcharts';
import DashboardLayout from '../../Layouts/DashboardLayout';

function StatCard({ icon, label, value, color, highlight, link }) {
    const content = (
        <div className={`relative bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5 overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-200 ${highlight ? 'ring-2 ring-amber-400 dark:ring-amber-500' : ''}`}>
            <div className="flex items-center gap-4">
                <div className={`w-12 h-12 flex items-center justify-center rounded-full shrink-0 ${color}`}>
                    <i className={`${icon} text-lg`}></i>
                </div>
                <div className="min-w-0">
                    <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">{label}</p>
                    <p className="text-2xl font-black text-[#333] dark:text-white">{value || 0}</p>
                </div>
            </div>
        </div>
    );

    if (link) {
        return <Link href={link} className="block">{content}</Link>;
    }
    return content;
}

export default function AdminHosting({ stats, chartNewProjects, chartProjectStatus, chartBillings }) {
    const billingsChart = chartBillings ? {
        options: {
            chart: { type: 'line', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
            colors: ['#10b981'],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
            xaxis: { categories: chartBillings.labels || [], labels: { style: { fontSize: '11px' } } },
            yaxis: {
                labels: {
                    style: { fontSize: '11px' },
                    formatter: (val) => 'Rp ' + Number(val).toLocaleString('id-ID'),
                }
            },
            grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: { theme: 'dark' },
        },
        series: [{ name: 'Pendapatan', data: chartBillings.series || [] }],
    } : null;

    const projectStatusChart = chartProjectStatus ? {
        options: {
            chart: { type: 'pie', fontFamily: 'Inter, sans-serif' },
            labels: chartProjectStatus.labels || [],
            colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#64748b'],
            legend: { position: 'bottom', fontSize: '12px' },
            dataLabels: { enabled: true, style: { fontSize: '11px' } },
            stroke: { width: 0 },
            tooltip: { theme: 'dark' },
        },
        series: chartProjectStatus.series || [],
    } : null;

    const newProjectsChart = chartNewProjects ? {
        options: {
            chart: { type: 'bar', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
            colors: ['#3b82f6'],
            plotOptions: {
                bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 }
            },
            xaxis: { categories: chartNewProjects.labels || [], labels: { style: { fontSize: '11px' } } },
            yaxis: { labels: { style: { fontSize: '11px' } } },
            grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: { theme: 'dark' },
        },
        series: [{ name: 'Project Baru', data: chartNewProjects.series || [] }],
    } : null;

    return (
        <DashboardLayout title="Admin Hosting Dashboard">
            <div className="space-y-6">

                {/* === 1. STAT CARDS (2-6 col grid, rounded-full icons) === */}
                <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <StatCard
                        icon="fa-solid fa-server"
                        label="Total Projects"
                        value={stats?.total_projects}
                        color="bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]"
                    />
                    <StatCard
                        icon="fa-solid fa-check-circle"
                        label="Aktif"
                        value={stats?.active_projects}
                        color="bg-emerald-50 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400"
                    />
                    <StatCard
                        icon="fa-solid fa-users"
                        label="Total Klien"
                        value={stats?.total_clients}
                        color="bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400"
                    />
                    <StatCard
                        icon="fa-solid fa-database"
                        label="Database"
                        value={stats?.total_databases}
                        color="bg-purple-50 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400"
                    />
                    <StatCard
                        icon="fa-solid fa-file-invoice"
                        label="Tagihan Pending"
                        value={stats?.pending_billing}
                        color="bg-amber-50 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400"
                        highlight={stats?.pending_billing > 0}
                    />
                    <StatCard
                        icon="fa-solid fa-hammer"
                        label="Sedang Build"
                        value={stats?.building_now}
                        color="bg-sky-50 text-sky-600 dark:bg-sky-500/20 dark:text-sky-400"
                        highlight={stats?.building_now > 0}
                    />
                </div>

                {/* === 2. CHARTS SECTION (3-col grid) === */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    {/* Hosting Revenue Trend (line chart) */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4">Hosting Revenue Trend</h3>
                        {billingsChart ? (
                            <Chart options={billingsChart.options} series={billingsChart.series} type="line" height={250} />
                        ) : (
                            <div className="flex items-center justify-center h-[250px] text-[12px] text-[#999] dark:text-white/40">Tidak ada data</div>
                        )}
                    </div>

                    {/* Project Status Distribution (pie chart) */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4">Status Project</h3>
                        {projectStatusChart ? (
                            <Chart options={projectStatusChart.options} series={projectStatusChart.series} type="pie" height={250} />
                        ) : (
                            <div className="flex items-center justify-center h-[250px] text-[12px] text-[#999] dark:text-white/40">Tidak ada data</div>
                        )}
                    </div>

                    {/* New Projects (bar chart) */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4">Project Baru (6 Bulan)</h3>
                        {newProjectsChart ? (
                            <Chart options={newProjectsChart.options} series={newProjectsChart.series} type="bar" height={250} />
                        ) : (
                            <div className="flex items-center justify-center h-[250px] text-[12px] text-[#999] dark:text-white/40">Tidak ada data</div>
                        )}
                    </div>
                </div>

                {/* === 3. NAVIGATION CARDS (3-col grid) === */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {/* Membutuhkan Tindakan */}
                    <Link
                        href={route('admin_hosting.pending')}
                        className={`relative bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5 overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-200 block ${stats?.action_required > 0 ? 'ring-2 ring-amber-400 dark:ring-amber-500' : ''}`}
                    >
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 flex items-center justify-center rounded-full bg-amber-50 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 shrink-0">
                                <i className="fa-solid fa-triangle-exclamation text-lg"></i>
                            </div>
                            <div>
                                <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Membutuhkan Tindakan</p>
                                <p className="text-2xl font-black text-[#333] dark:text-white">{stats?.action_required || 0}</p>
                            </div>
                        </div>
                    </Link>

                    {/* Riwayat Deployment */}
                    <Link
                        href={route('admin_hosting.deployments')}
                        className="relative bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5 overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-200 block"
                    >
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 flex items-center justify-center rounded-full bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa] shrink-0">
                                <i className="fa-solid fa-clock-rotate-left text-lg"></i>
                            </div>
                            <div>
                                <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Riwayat Deployment</p>
                                <p className="text-sm font-bold text-[#7c3aed] dark:text-[#a78bfa] mt-1">Lihat Riwayat &rarr;</p>
                            </div>
                        </div>
                    </Link>

                    {/* Semua Project Hosting */}
                    <Link
                        href={route('admin_hosting.projects')}
                        className="relative bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5 overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-200 block"
                    >
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 flex items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 shrink-0">
                                <i className="fa-solid fa-server text-lg"></i>
                            </div>
                            <div>
                                <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Semua Project Hosting</p>
                                <p className="text-2xl font-black text-[#333] dark:text-white">{stats?.total_projects || 0}</p>
                            </div>
                        </div>
                    </Link>
                </div>

            </div>
        </DashboardLayout>
    );
}
