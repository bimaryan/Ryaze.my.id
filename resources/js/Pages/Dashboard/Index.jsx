import DashboardLayout from '../../Layouts/DashboardLayout';
import { Link, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';

export default function Index({ stats, projects, activeBilling, expiredBilling, walletBalance, referralCode, promos, announcements }) {
    const [activeSlide, setActiveSlide] = useState(0);
    const [dismissedAnnouncements, setDismissedAnnouncements] = useState([]);
    const [serverHealth, setServerHealth] = useState(null);

    useEffect(() => {
        if (promos && promos.length > 1) {
            const interval = setInterval(() => {
                setActiveSlide((prev) => (prev + 1) % promos.length);
            }, 5000);
            return () => clearInterval(interval);
        }
    }, [promos]);

    useEffect(() => {
        const fetchHealth = () => {
            fetch(route('user_hosting.server_status'))
                .then((res) => res.json())
                .then((data) => setServerHealth(data))
                .catch(() => setServerHealth({ status: 'disconnected', cpu: 0, ram: 0 }));
        };
        fetchHealth();
        const interval = setInterval(fetchHealth, 30000);
        return () => clearInterval(interval);
    }, []);

    const copyReferral = () => {
        navigator.clipboard.writeText(referralCode);
    };

    const getExpiryDays = () => {
        if (!activeBilling?.next_due_date) return null;
        const diff = new Date(activeBilling.next_due_date) - new Date();
        return Math.ceil(diff / (1000 * 60 * 60 * 24));
    };

    const daysLeft = getExpiryDays();

    const visibleAnnouncements = announcements?.filter(
        (a) => !dismissedAnnouncements.includes(a.id) && (!a.expires_at || new Date(a.expires_at) > new Date())
    ) || [];

    const getTypeStyles = (type) => {
        switch (type) {
            case 'info': return 'bg-blue-50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/30 text-blue-700 dark:text-blue-300';
            case 'update': return 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-300';
            case 'maintenance': return 'bg-amber-50 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/30 text-amber-700 dark:text-amber-300';
            case 'warning': return 'bg-rose-50 dark:bg-rose-500/10 border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300';
            default: return 'bg-blue-50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/30 text-blue-700 dark:text-blue-300';
        }
    };

    const getTypeIcon = (type) => {
        switch (type) {
            case 'info': return 'fa-solid fa-circle-info';
            case 'update': return 'fa-solid fa-arrow-up';
            case 'maintenance': return 'fa-solid fa-wrench';
            case 'warning': return 'fa-solid fa-triangle-exclamation';
            default: return 'fa-solid fa-circle-info';
        }
    };

    const getDisplayDomain = (p) => p.ssl_domain || p.ryaze_domain;

    const getStatusBadge = (status) => {
        switch (status) {
            case 'active': return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300';
            case 'building': return 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300';
            case 'failed': return 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300';
            default: return 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300';
        }
    };

    return (
        <DashboardLayout title="Dashboard Hosting">
            <div className="space-y-6">

                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-xl font-bold text-[#333] dark:text-white">Dashboard</h1>
                        <p className="text-sm text-[#999] dark:text-white/40 mt-0.5">Kelola hosting dan project Anda</p>
                    </div>
                    <Link
                        href={route('user_hosting.create')}
                        className="px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors"
                    >
                        + Deploy Baru
                    </Link>
                </div>

                {/* Promo Banner Carousel */}
                {promos && promos.length > 0 && (
                    <div className="relative overflow-hidden">
                        <div className="relative h-40 sm:h-48">
                            {promos.map((promo, idx) => (
                                <div
                                    key={promo.id}
                                    className={`absolute inset-0 transition-opacity duration-500 ${
                                        idx === activeSlide ? 'opacity-100' : 'opacity-0 pointer-events-none'
                                    }`}
                                >
                                    {promo.banner_image ? (
                                        <a href={promo.target_url || '#'} className="block h-full">
                                            <img
                                                src={promo.banner_image}
                                                alt={promo.title}
                                                className="w-full h-full object-cover"
                                            />
                                        </a>
                                    ) : (
                                        <a
                                            href={promo.target_url || '#'}
                                            className="block h-full bg-gradient-to-r from-indigo-500 to-purple-600 p-6 flex flex-col justify-center"
                                        >
                                            <h3 className="text-lg font-bold text-white">{promo.title}</h3>
                                            <p className="text-sm text-white/80 mt-1">{promo.description}</p>
                                        </a>
                                    )}
                                </div>
                            ))}
                        </div>

                        {promos.length > 1 && (
                            <>
                                <button
                                    onClick={() => setActiveSlide((prev) => (prev - 1 + promos.length) % promos.length)}
                                    className="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition-colors"
                                >
                                    <i className="fa-solid fa-chevron-left text-xs"></i>
                                </button>
                                <button
                                    onClick={() => setActiveSlide((prev) => (prev + 1) % promos.length)}
                                    className="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition-colors"
                                >
                                    <i className="fa-solid fa-chevron-right text-xs"></i>
                                </button>
                                <div className="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
                                    {promos.map((_, idx) => (
                                        <button
                                            key={idx}
                                            onClick={() => setActiveSlide(idx)}
                                            className={`w-2 h-2 rounded-full transition-colors ${
                                                idx === activeSlide ? 'bg-white' : 'bg-white/40'
                                            }`}
                                        />
                                    ))}
                                </div>
                            </>
                        )}
                    </div>
                )}

                {/* Announcement Banners */}
                {visibleAnnouncements.length > 0 && (
                    <div className="space-y-3">
                        {visibleAnnouncements.map((ann) => (
                            <div
                                key={ann.id}
                                className={`border p-4 ${getTypeStyles(ann.type)}`}
                            >
                                <div className="flex items-start justify-between gap-3">
                                    <div className="flex items-start gap-3">
                                        <i className={`${getTypeIcon(ann.type)} mt-0.5`}></i>
                                        <div>
                                            <p className="text-sm font-bold flex items-center gap-1.5">
                                                {ann.is_pinned && <i className="fa-solid fa-thumbtack text-[10px]"></i>}
                                                {ann.title}
                                            </p>
                                            <p className="text-xs mt-1 opacity-80">{ann.content}</p>
                                            {ann.expires_at && (
                                                <p className="text-[11px] mt-1.5 opacity-60">
                                                    Berakhir: {new Date(ann.expires_at).toLocaleDateString('id-ID')}
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                    <button
                                        onClick={() => setDismissedAnnouncements((prev) => [...prev, ann.id])}
                                        className="shrink-0 opacity-60 hover:opacity-100 transition-opacity"
                                    >
                                        <i className="fa-solid fa-xmark text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}

                {/* Expired Hosting Warning */}
                {expiredBilling && (
                    <div className="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 p-5">
                        <div className="flex items-center justify-between gap-4">
                            <div className="flex items-center gap-3">
                                <i className="fa-solid fa-triangle-exclamation text-red-500 text-lg"></i>
                                <div>
                                    <p className="text-sm font-bold text-red-700 dark:text-red-300">
                                        Langganan {expiredBilling.plan} Telah Berakhir!
                                    </p>
                                    <p className="text-xs text-red-600 dark:text-red-400 mt-0.5">
                                        Kedaluwarsa: {new Date(expiredBilling.next_due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                    </p>
                                </div>
                            </div>
                            <Link
                                href={route('user_hosting.subscription')}
                                className="shrink-0 px-4 py-2 bg-red-600 text-white text-[13px] font-semibold hover:bg-red-700 transition-colors"
                            >
                                Perpanjang Sekarang
                            </Link>
                        </div>
                    </div>
                )}

                {/* Active Billing Countdown Warning */}
                {activeBilling && daysLeft !== null && daysLeft <= 7 && daysLeft > 0 && (
                    <div className="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 p-4">
                        <div className="flex items-center gap-3">
                            <i className="fa-solid fa-clock text-amber-500"></i>
                            <div>
                                <p className="text-sm font-bold text-amber-700 dark:text-amber-300">
                                    Langganan {activeBilling.plan} Berakhir dalam {daysLeft} hari
                                </p>
                                <p className="text-xs text-amber-600 dark:text-amber-400 mt-0.5">
                                    Jatuh tempo: {new Date(activeBilling.next_due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                </p>
                            </div>
                        </div>
                    </div>
                )}

                {/* Wallet & Affiliate Summary */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {/* Ryaze Wallet */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white mb-3">
                            <i className="fa-solid fa-wallet text-[#7c3aed] mr-1.5"></i>
                            Ryaze Wallet
                        </h3>
                        <p className="text-2xl font-black text-[#7c3aed]">
                            Rp {Number(walletBalance || 0).toLocaleString('id-ID')}
                        </p>
                        <div className="flex gap-2 mt-4">
                            <Link
                                href={route('user.wallet.history')}
                                className="flex-1 text-center px-3 py-2 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[13px] font-medium text-[#333] dark:text-white hover:bg-[#f5f5f5] dark:hover:bg-white/[0.02] transition-colors"
                            >
                                Riwayat
                            </Link>
                            <Link
                                href={route('user.wallet.withdraw')}
                                className="flex-1 text-center px-3 py-2 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[13px] font-medium text-[#333] dark:text-white hover:bg-[#f5f5f5] dark:hover:bg-white/[0.02] transition-colors"
                            >
                                Tarik Dana
                            </Link>
                            <Link
                                href={route('user.wallet.topup')}
                                className="flex-1 text-center px-3 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors"
                            >
                                Top Up
                            </Link>
                        </div>
                    </div>

                    {/* Affiliate Program */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white mb-3">
                            <i className="fa-solid fa-users text-[#7c3aed] mr-1.5"></i>
                            Affiliate Program
                        </h3>
                        <p className="text-[12px] text-[#999] dark:text-white/40 mb-2">Kode Referral Anda:</p>
                        <div className="flex items-center gap-2">
                            <div className="flex-1 px-3 py-2 bg-[#f5f5f5] dark:bg-white/[0.03] border border-[#e5e5e5] dark:border-[#1a1a2e] text-[13px] font-mono font-bold text-[#333] dark:text-white select-all">
                                {referralCode || '-'}
                            </div>
                            <button
                                onClick={copyReferral}
                                className="px-3 py-2 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[13px] font-medium text-[#333] dark:text-white hover:bg-[#f5f5f5] dark:hover:bg-white/[0.02] transition-colors"
                            >
                                <i className="fa-solid fa-copy"></i>
                            </button>
                        </div>
                        <Link
                            href={route('user.affiliate.dashboard')}
                            className="block mt-4 text-center px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors"
                        >
                            Buka Dashboard Affiliate
                        </Link>
                    </div>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {/* Hosting Aktif */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[13px] text-[#999] dark:text-white/40 font-medium">Hosting Aktif</p>
                        <p className="text-2xl font-black text-emerald-500 mt-1">{stats?.active || 0}</p>
                    </div>

                    {/* Tagihan Belum Lunas */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[13px] text-[#999] dark:text-white/40 font-medium">Tagihan Belum Lunas</p>
                        <p className="text-2xl font-black text-rose-500 mt-1">{stats?.unpaid || 0}</p>
                    </div>

                    {/* Tiket Bantuan */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[13px] text-[#999] dark:text-white/40 font-medium">Tiket Bantuan</p>
                        <p className="text-2xl font-black text-sky-500 mt-1">{stats?.tickets || 0}</p>
                    </div>

                    {/* Server Node Health */}
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[13px] text-[#999] dark:text-white/40 font-medium">Server Node</p>
                        {serverHealth ? (
                            <div className="mt-1">
                                <div className="flex items-center gap-2">
                                    <div className={`w-2 h-2 rounded-full ${
                                        serverHealth.status === 'healthy' ? 'bg-emerald-500' :
                                        serverHealth.status === 'heavy_load' ? 'bg-amber-500' :
                                        'bg-rose-500'
                                    }`}></div>
                                    <span className={`text-sm font-bold ${
                                        serverHealth.status === 'healthy' ? 'text-emerald-500' :
                                        serverHealth.status === 'heavy_load' ? 'text-amber-500' :
                                        'text-rose-500'
                                    }`}>
                                        {serverHealth.status === 'healthy' ? 'Healthy' :
                                         serverHealth.status === 'heavy_load' ? 'Heavy Load' :
                                         'Disconnected'}
                                    </span>
                                </div>
                                <div className="flex gap-3 mt-2">
                                    <span className="text-[11px] text-[#999] dark:text-white/40">
                                        CPU: {serverHealth.cpu || 0}%
                                    </span>
                                    <span className="text-[11px] text-[#999] dark:text-white/40">
                                        RAM: {serverHealth.ram || 0}%
                                    </span>
                                </div>
                            </div>
                        ) : (
                            <p className="text-sm text-[#999] dark:text-white/40 mt-1">Memuat...</p>
                        )}
                    </div>
                </div>

                {/* Services Table */}
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Layanan Hosting</h2>
                        <Link
                            href={route('user_hosting.projects')}
                            className="text-[13px] font-medium text-[#7c3aed] hover:text-[#6d28d9] transition-colors"
                        >
                            Lihat Semua
                        </Link>
                    </div>

                    {projects && projects.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                        <th className="text-left px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Domain / Project</th>
                                        <th className="text-left px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Framework</th>
                                        <th className="text-left px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                        <th className="text-right px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                    {projects.map((p) => (
                                        <tr key={p.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                            <td className="px-5 py-3">
                                                <p className="text-[13px] font-semibold text-[#333] dark:text-white truncate">{getDisplayDomain(p)}</p>
                                                <p className="text-[11px] text-[#999] dark:text-white/40 truncate">{p.project_name}</p>
                                            </td>
                                            <td className="px-5 py-3">
                                                <span className="text-[12px] font-medium text-[#999] dark:text-white/50">{p.framework || '-'}</span>
                                            </td>
                                            <td className="px-5 py-3">
                                                <span className={`text-[11px] font-bold uppercase px-2 py-0.5 ${getStatusBadge(p.status)}`}>
                                                    {p.status}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3 text-right">
                                                <Link
                                                    href={route('user_hosting.show', { hashid: p.hashid })}
                                                    className="inline-block px-3 py-1.5 bg-[#7c3aed] text-white text-[12px] font-semibold hover:bg-[#6d28d9] transition-colors"
                                                >
                                                    Kelola
                                                </Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <div className="px-5 py-10 text-center">
                            <i className="fa-solid fa-server text-3xl text-[#e5e5e5] dark:text-white/10 mb-3"></i>
                            <p className="text-sm text-[#999] dark:text-white/40">Belum ada project hosting.</p>
                            <Link
                                href={route('user_hosting.create')}
                                className="inline-block mt-3 px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors"
                            >
                                Deploy Baru
                            </Link>
                        </div>
                    )}
                </div>

            </div>
        </DashboardLayout>
    );
}
