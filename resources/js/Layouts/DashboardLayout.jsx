import { useState, useEffect, Fragment } from 'react';
import { Link, router, usePage } from '@inertiajs/react';

const sidebarNav = {
    superadmin: [
        { section: 'Sistem Utama', items: [
            { label: 'Dashboard', icon: 'fa-solid fa-border-all', route: 'superadmin.dashboard', href: '/superadmin/dashboard' },
            { label: 'Data Pengguna', icon: 'fa-solid fa-users', route: 'superadmin.users*', href: '/superadmin/users' },
            { label: 'Portofolio', icon: 'fa-solid fa-briefcase', route: 'superadmin.portfolios*', href: '/superadmin/portfolios' },
            { label: 'Promo Event', icon: 'fa-solid fa-bullhorn', route: 'admin.promo_events*', href: '/superadmin/promo-events' },
            { label: 'Blog', icon: 'fa-solid fa-newspaper', route: 'superadmin.articles*', href: '/superadmin/articles' },
            { label: 'Pengaturan', icon: 'fa-solid fa-cogs', route: 'superadmin.settings*', href: '/superadmin/settings' },
            { label: 'Backup', icon: 'fa-solid fa-box-archive', route: 'superadmin.backup*', href: '/superadmin/backup' },
            { label: 'Penarikan', icon: 'fa-solid fa-money-bill-transfer', route: 'superadmin.withdrawals*', href: '/superadmin/withdrawals' },
            { label: 'Keuangan', icon: 'fa-solid fa-chart-pie', route: 'superadmin.finance', href: '/superadmin/finance' },
        ]},
        { section: 'Manajemen Joki', items: [
            { label: 'Pesanan Joki', icon: 'fa-solid fa-code-branch', route: 'admin_joki.orders*', href: '/admin/joki/orders' },
            { label: 'Layanan', icon: 'fa-solid fa-list', route: 'admin_joki.services*', href: '/admin/joki/services' },
            { label: 'Keuangan Joki', icon: 'fa-solid fa-wallet', route: 'admin_joki.finance', href: '/admin/joki/finance' },
        ]},
        { section: 'Manajemen Hosting', items: [
            { label: 'Project', icon: 'fa-solid fa-server', route: 'admin_hosting.projects', href: '/admin/hosting/projects' },
            { label: 'Deploy', icon: 'fa-solid fa-history', route: 'admin_hosting.deployments', href: '/admin/hosting/deployments' },
            { label: 'Pending', icon: 'fa-solid fa-warning', route: 'admin_hosting.pending', href: '/admin/hosting/pending' },
            { label: 'Database', icon: 'fa-solid fa-database', route: 'admin_hosting.databases', href: '/admin/hosting/databases' },
            { label: 'Storage', icon: 'fa-solid fa-hard-drive', route: 'admin_hosting.storage', href: '/admin/hosting/storage' },
            { label: 'Tagihan', icon: 'fa-solid fa-file-invoice-dollar', route: 'admin_hosting.billing', href: '/admin/hosting/billing' },
            { label: 'Voucher', icon: 'fa-solid fa-ticket', route: 'admin_hosting.vouchers*', href: '/admin/hosting/vouchers' },
            { label: 'Tiket', icon: 'fa-solid fa-headset', route: 'admin_hosting.tickets*', href: '/admin/hosting/tickets' },
        ]},
        { section: 'Klien Joki', items: [
            { label: 'Buat Pesanan', icon: 'fa-solid fa-cart-plus', route: 'user_joki.create', href: '/user/joki/create' },
            { label: 'Progres', icon: 'fa-solid fa-laptop-code', route: 'user_joki.progress', href: '/user/joki/progress' },
            { label: 'Riwayat', icon: 'fa-solid fa-history', route: 'user_joki.riwayat', href: '/user/joki/riwayat' },
            { label: 'Tagihan', icon: 'fa-solid fa-file-invoice-dollar', route: 'user_joki.billing', href: '/user/joki/billing' },
        ]},
        { section: 'Klien Hosting', items: [
            { label: 'Deploy', icon: 'fa-solid fa-rocket', route: 'user_hosting.create', href: '/user/hosting/create' },
            { label: 'Marketplace', icon: 'fa-solid fa-store', route: 'user_hosting.marketplace', href: '/user/hosting/marketplace' },
            { label: 'Proyek', icon: 'fa-solid fa-terminal', route: 'user_hosting.projects', href: '/user/hosting/projects' },
            { label: 'Database', icon: 'fa-solid fa-database', route: 'user_hosting.databases', href: '/user/hosting/databases' },
            { label: 'Storage', icon: 'fa-solid fa-hard-drive', route: 'user_hosting.storage*', href: '/user/hosting/storage' },
            { label: 'Langganan', icon: 'fa-solid fa-crown', route: 'user_hosting.subscription', href: '/user/hosting/subscription' },
            { label: 'Billing', icon: 'fa-solid fa-file-invoice-dollar', route: 'user_hosting.billing', href: '/user/hosting/billing' },
            { label: 'Tiket', icon: 'fa-solid fa-life-ring', route: 'user_hosting.tickets*', href: '/user/hosting/tickets' },
        ]},
        { section: 'Pendapatan', items: [
            { label: 'Wallet', icon: 'fa-solid fa-wallet', route: 'user.wallet*', href: '/user/wallet' },
            { label: 'Affiliate', icon: 'fa-solid fa-users-viewfinder', route: 'user.affiliate*', href: '/user/affiliate' },
        ]},
    ],
    admin_hosting: [
        { section: 'Hosting', items: [
            { label: 'Dashboard', icon: 'fa-solid fa-border-all', route: 'admin_hosting.dashboard', href: '/admin/hosting/dashboard' },
            { label: 'Project', icon: 'fa-solid fa-server', route: 'admin_hosting.projects', href: '/admin/hosting/projects' },
            { label: 'Deploy', icon: 'fa-solid fa-history', route: 'admin_hosting.deployments', href: '/admin/hosting/deployments' },
            { label: 'Pending', icon: 'fa-solid fa-warning', route: 'admin_hosting.pending', href: '/admin/hosting/pending' },
            { label: 'Database', icon: 'fa-solid fa-database', route: 'admin_hosting.databases', href: '/admin/hosting/databases' },
            { label: 'Storage', icon: 'fa-solid fa-hard-drive', route: 'admin_hosting.storage', href: '/admin/hosting/storage' },
            { label: 'Tagihan', icon: 'fa-solid fa-file-invoice-dollar', route: 'admin_hosting.billing', href: '/admin/hosting/billing' },
            { label: 'Voucher', icon: 'fa-solid fa-ticket', route: 'admin_hosting.vouchers*', href: '/admin/hosting/vouchers' },
            { label: 'Tiket', icon: 'fa-solid fa-headset', route: 'admin_hosting.tickets*', href: '/admin/hosting/tickets' },
        ]},
        { section: 'Klien Hosting', items: [
            { label: 'Deploy', icon: 'fa-solid fa-rocket', route: 'user_hosting.create', href: '/user/hosting/create' },
            { label: 'Marketplace', icon: 'fa-solid fa-store', route: 'user_hosting.marketplace', href: '/user/hosting/marketplace' },
            { label: 'Proyek', icon: 'fa-solid fa-terminal', route: 'user_hosting.projects', href: '/user/hosting/projects' },
            { label: 'Database', icon: 'fa-solid fa-database', route: 'user_hosting.databases', href: '/user/hosting/databases' },
            { label: 'Storage', icon: 'fa-solid fa-hard-drive', route: 'user_hosting.storage*', href: '/user/hosting/storage' },
            { label: 'Langganan', icon: 'fa-solid fa-crown', route: 'user_hosting.subscription', href: '/user/hosting/subscription' },
            { label: 'Billing', icon: 'fa-solid fa-file-invoice-dollar', route: 'user_hosting.billing', href: '/user/hosting/billing' },
            { label: 'Tiket', icon: 'fa-solid fa-life-ring', route: 'user_hosting.tickets*', href: '/user/hosting/tickets' },
        ]},
        { section: 'Pendapatan', items: [
            { label: 'Wallet', icon: 'fa-solid fa-wallet', route: 'user.wallet*', href: '/user/wallet' },
            { label: 'Affiliate', icon: 'fa-solid fa-users-viewfinder', route: 'user.affiliate*', href: '/user/affiliate' },
        ]},
    ],
    admin_joki: [
        { section: 'Joki', items: [
            { label: 'Dashboard', icon: 'fa-solid fa-border-all', route: 'admin_joki.dashboard', href: '/admin/joki/dashboard' },
            { label: 'Pesanan', icon: 'fa-solid fa-code-branch', route: 'admin_joki.orders*', href: '/admin/joki/orders' },
            { label: 'Layanan', icon: 'fa-solid fa-list', route: 'admin_joki.services*', href: '/admin/joki/services' },
            { label: 'Keuangan', icon: 'fa-solid fa-wallet', route: 'admin_joki.finance', href: '/admin/joki/finance' },
        ]},
        { section: 'Klien Joki', items: [
            { label: 'Buat Pesanan', icon: 'fa-solid fa-cart-plus', route: 'user_joki.create', href: '/user/joki/create' },
            { label: 'Progres', icon: 'fa-solid fa-laptop-code', route: 'user_joki.progress', href: '/user/joki/progress' },
            { label: 'Riwayat', icon: 'fa-solid fa-history', route: 'user_joki.riwayat', href: '/user/joki/riwayat' },
            { label: 'Tagihan', icon: 'fa-solid fa-file-invoice-dollar', route: 'user_joki.billing', href: '/user/joki/billing' },
        ]},
        { section: 'Pendapatan', items: [
            { label: 'Wallet', icon: 'fa-solid fa-wallet', route: 'user.wallet*', href: '/user/wallet' },
            { label: 'Affiliate', icon: 'fa-solid fa-users-viewfinder', route: 'user.affiliate*', href: '/user/affiliate' },
        ]},
    ],
    user_hosting: [
        { section: 'Hosting', items: [
            { label: 'Dashboard', icon: 'fa-solid fa-border-all', route: 'user_hosting.dashboard', href: '/user/hosting/dashboard' },
            { label: 'Deploy', icon: 'fa-solid fa-rocket', route: 'user_hosting.create', href: '/user/hosting/create' },
            { label: 'Marketplace', icon: 'fa-solid fa-store', route: 'user_hosting.marketplace', href: '/user/hosting/marketplace' },
            { label: 'Web to APK', icon: 'fa-brands fa-android', route: 'user_hosting.apk*', href: '/user/hosting/apk' },
            { label: 'Tunnels', icon: 'fa-solid fa-network-wired', route: 'user_hosting.tunnels*', href: '/user/hosting/tunnels', badge: 'Beta' },
            { label: 'Template', icon: 'fa-solid fa-layer-group', route: 'user_hosting.templates', href: '/user/hosting/templates' },
            { label: 'Proyek', icon: 'fa-solid fa-terminal', route: 'user_hosting.projects', href: '/user/hosting/projects' },
            { label: 'Database', icon: 'fa-solid fa-database', route: 'user_hosting.databases', href: '/user/hosting/databases' },
            { label: 'DB Manager', icon: 'fa-solid fa-server', route: 'user_hosting.databases.pma', href: '/user/hosting/pma' },
            { label: 'Storage', icon: 'fa-solid fa-hard-drive', route: 'user_hosting.storage*', href: '/user/hosting/storage' },
            { label: 'Dokumentasi', icon: 'fa-solid fa-book', route: 'user_hosting.docs*', href: '/user/hosting/docs' },
        ]},
        { section: 'Akun', items: [
            { label: 'Langganan', icon: 'fa-solid fa-crown', route: 'user_hosting.subscription', href: '/user/hosting/subscription' },
            { label: 'Billing', icon: 'fa-solid fa-file-invoice-dollar', route: 'user_hosting.billing', href: '/user/hosting/billing' },
            { label: 'Tiket', icon: 'fa-solid fa-life-ring', route: 'user_hosting.tickets*', href: '/user/hosting/tickets' },
            { label: 'Wallet', icon: 'fa-solid fa-wallet', route: 'user.wallet*', href: '/user/wallet' },
            { label: 'Affiliate', icon: 'fa-solid fa-users-viewfinder', route: 'user.affiliate*', href: '/user/affiliate' },
        ]},
    ],
    user_joki: [
        { section: 'Joki', items: [
            { label: 'Dashboard', icon: 'fa-solid fa-border-all', route: 'user_joki.dashboard', href: '/user/joki/dashboard' },
            { label: 'Buat Pesanan', icon: 'fa-solid fa-cart-plus', route: 'user_joki.create', href: '/user/joki/create' },
            { label: 'Progres', icon: 'fa-solid fa-laptop-code', route: 'user_joki.progress', href: '/user/joki/progress' },
            { label: 'Riwayat', icon: 'fa-solid fa-history', route: 'user_joki.riwayat', href: '/user/joki/riwayat' },
            { label: 'Tagihan', icon: 'fa-solid fa-file-invoice-dollar', route: 'user_joki.billing', href: '/user/joki/billing' },
        ]},
        { section: 'Akun', items: [
            { label: 'Wallet', icon: 'fa-solid fa-wallet', route: 'user.wallet*', href: '/user/wallet' },
            { label: 'Affiliate', icon: 'fa-solid fa-users-viewfinder', route: 'user.affiliate*', href: '/user/affiliate' },
        ]},
    ],
};

function matchRoute(pattern, url) {
    return url.startsWith(pattern.replace('*', ''));
}

export default function DashboardLayout({ children, title }) {
    const { auth, flash } = usePage().props;
    const user = auth?.user;
    const [collapsed, setCollapsed] = useState(() => {
        if (typeof window === 'undefined') return false;
        return localStorage.getItem('ryaze-sidebar') === 'collapsed';
    });
    const [mobileOpen, setMobileOpen] = useState(false);
    const [dark, setDark] = useState(() => {
        if (typeof window === 'undefined') return false;
        const s = localStorage.getItem('ryaze-theme');
        if (s) return s === 'dark';
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    });
    const [hoveredItem, setHoveredItem] = useState(null);

    useEffect(() => {
        document.documentElement.classList.toggle('dark', dark);
        document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
        localStorage.setItem('ryaze-theme', dark ? 'dark' : 'light');
    }, [dark]);

    useEffect(() => {
        localStorage.setItem('ryaze-sidebar', collapsed ? 'collapsed' : 'expanded');
    }, [collapsed]);

    const currentUrl = typeof window !== 'undefined' ? window.location.pathname : '/';
    const navSections = sidebarNav[user?.role] || sidebarNav.user_hosting;
    const initials = (user?.name || 'U').split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
    const roleLabel = (user?.role || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

    const navItem = (item) => {
        const active = matchRoute(item.route, currentUrl);
        return (
            <Link
                key={item.route}
                href={item.href}
                onMouseEnter={() => collapsed && setHoveredItem(item.route)}
                onMouseLeave={() => setHoveredItem(null)}
                className={`relative flex items-center gap-3 transition-all duration-150 group ${
                    collapsed ? 'justify-center px-0 py-2.5 mx-2' : 'px-3 py-2 mx-2'
                } text-[13px] font-medium ${
                    active
                        ? 'bg-[#7c3aed] text-white'
                        : 'text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5 hover:text-[#7c3aed] dark:hover:text-white'
                }`}
            >
                <i className={`${item.icon} text-sm w-5 text-center shrink-0`}></i>
                {!collapsed && (
                    <>
                        <span className="flex-1 truncate">{item.label}</span>
                        {item.badge && (
                            <span className="px-1.5 py-0.5 text-[9px] font-bold bg-[#7c3aed]/10 text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]">{item.badge}</span>
                        )}
                    </>
                )}
                {collapsed && hoveredItem === item.route && (
                    <div className="absolute left-full ml-2 px-2.5 py-1 bg-[#333] dark:bg-[#1a1025] text-white text-[12px] font-medium whitespace-nowrap z-50 shadow-lg">
                        {item.label}
                    </div>
                )}
            </Link>
        );
    };

    return (
        <div className="min-h-screen bg-[#fafafa] dark:bg-[#0a0a14] flex font-sans antialiased">
            {flash?.success && (
                <div className="fixed top-4 right-4 z-[100] px-4 py-3 bg-green-500 text-white text-sm font-medium shadow-lg animate-slide-down">
                    {flash.success}
                </div>
            )}
            {flash?.error && (
                <div className="fixed top-4 right-4 z-[100] px-4 py-3 bg-red-500 text-white text-sm font-medium shadow-lg animate-slide-down">
                    {flash.error}
                </div>
            )}

            <aside className={`hidden lg:flex flex-col h-screen sticky top-0 z-40 bg-white dark:bg-[#0d0d18] border-r border-[#e5e5e5] dark:border-[#1a1a2e] transition-all duration-300 ${collapsed ? 'w-[60px]' : 'w-60'}`}>
                <div className={`h-14 flex items-center border-b border-[#e5e5e5] dark:border-[#1a1a2e] shrink-0 ${collapsed ? 'justify-center' : 'px-4'}`}>
                    <Link href="/" className="flex items-center gap-2.5 shrink-0">
                        <div className="w-7 h-7 bg-[#7c3aed] flex items-center justify-center shrink-0">
                            <span className="text-white font-black text-xs">R</span>
                        </div>
                        {!collapsed && <span className="font-black text-[#7c3aed] dark:text-white text-sm tracking-tight">RYAZE</span>}
                    </Link>
                </div>

                <nav className="flex-1 overflow-y-auto py-3 space-y-4">
                    {navSections.map((section, si) => (
                        <div key={si}>
                            {!collapsed && (
                                <div className="px-5 pb-1.5">
                                    <span className="text-[10px] font-bold text-[#999] dark:text-white/30 uppercase tracking-wider">{section.section}</span>
                                </div>
                            )}
                            {collapsed && <div className="mx-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e]"></div>}
                            <div className="space-y-0.5">
                                {section.items.map(navItem)}
                            </div>
                        </div>
                    ))}
                </nav>

                <div className="border-t border-[#e5e5e5] dark:border-[#1a1a2e] p-2 shrink-0">
                    <button
                        onClick={() => setCollapsed(!collapsed)}
                        className="flex items-center justify-center w-full py-2.5 text-[#999] dark:text-white/30 hover:text-[#7c3aed] dark:hover:text-white transition-colors"
                    >
                        <i className={`fa-solid ${collapsed ? 'fa-angles-right' : 'fa-angles-left'} text-sm`}></i>
                        {!collapsed && <span className="ml-2 text-[13px] font-medium">Tutup</span>}
                    </button>
                </div>
            </aside>

            {mobileOpen && (
                <Fragment>
                    <div className="fixed inset-0 bg-black/40 z-40 lg:hidden" onClick={() => setMobileOpen(false)}></div>
                    <aside className="fixed inset-y-0 left-0 z-50 w-60 bg-white dark:bg-[#0d0d18] border-r border-[#e5e5e5] dark:border-[#1a1a2e] lg:hidden flex flex-col">
                        <div className="h-14 flex items-center justify-between px-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <Link href="/" className="flex items-center gap-2.5">
                                <div className="w-7 h-7 bg-[#7c3aed] flex items-center justify-center">
                                    <span className="text-white font-black text-xs">R</span>
                                </div>
                                <span className="font-black text-[#7c3aed] dark:text-white text-sm tracking-tight">RYAZE</span>
                            </Link>
                            <button onClick={() => setMobileOpen(false)} className="w-8 h-8 flex items-center justify-center text-[#999] hover:text-[#333]">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <nav className="flex-1 overflow-y-auto py-3 space-y-4">
                            {navSections.map((section, si) => (
                                <div key={si}>
                                    <div className="px-5 pb-1.5">
                                        <span className="text-[10px] font-bold text-[#999] dark:text-white/30 uppercase tracking-wider">{section.section}</span>
                                    </div>
                                    <div className="space-y-0.5">
                                        {section.items.map(item => {
                                            const active = matchRoute(item.route, currentUrl);
                                            return (
                                                <Link
                                                    key={item.route}
                                                    href={item.href}
                                                    onClick={() => setMobileOpen(false)}
                                                    className={`flex items-center gap-3 px-5 py-2.5 text-[13px] font-medium transition-colors ${
                                                        active ? 'bg-[#7c3aed] text-white' : 'text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5'
                                                    }`}
                                                >
                                                    <i className={`${item.icon} text-sm w-5 text-center`}></i>
                                                    <span className="flex-1">{item.label}</span>
                                                    {item.badge && (
                                                        <span className="px-1.5 py-0.5 text-[9px] font-bold bg-[#7c3aed]/10 text-[#7c3aed]">{item.badge}</span>
                                                    )}
                                                </Link>
                                            );
                                        })}
                                    </div>
                                </div>
                            ))}
                        </nav>
                    </aside>
                </Fragment>
            )}

            <div className="flex-1 flex flex-col min-w-0">
                <header className="h-14 bg-white dark:bg-[#0d0d18] border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between px-4 shrink-0 sticky top-0 z-30">
                    <div className="flex items-center gap-3">
                        <button
                            onClick={() => setMobileOpen(true)}
                            className="lg:hidden w-8 h-8 flex items-center justify-center text-[#666] dark:text-white/60 hover:text-[#7c3aed] transition-colors"
                        >
                            <i className="fa-solid fa-bars text-base"></i>
                        </button>
                        <h1 className="text-[15px] font-bold text-[#333] dark:text-white">{title || 'Dashboard'}</h1>
                    </div>

                    <div className="flex items-center gap-1">
                        <button
                            onClick={() => setDark(d => !d)}
                            className="w-8 h-8 flex items-center justify-center text-[#999] dark:text-white/40 hover:text-[#7c3aed] dark:hover:text-white transition-colors"
                        >
                            <i className={`fa-solid ${dark ? 'fa-sun' : 'fa-moon'} text-sm`}></i>
                        </button>

                        <button className="relative w-8 h-8 flex items-center justify-center text-[#999] dark:text-white/40 hover:text-[#7c3aed] dark:hover:text-white transition-colors">
                            <i className="fa-solid fa-bell text-sm"></i>
                        </button>

                        <div className="w-px h-5 bg-[#e5e5e5] dark:bg-[#1a1a2e] mx-1.5"></div>

                        <div className="relative group">
                            <button className="flex items-center gap-2.5 p-1 hover:bg-[#f5f0ff] dark:hover:bg-white/5 transition-colors">
                                <div className="w-8 h-8 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa] flex items-center justify-center text-sm font-bold">
                                    {initials}
                                </div>
                                <div className="hidden md:block text-left">
                                    <p className="text-[13px] font-semibold text-[#333] dark:text-white leading-tight">{user?.name}</p>
                                    <p className="text-[11px] text-[#999] dark:text-white/40 leading-tight">{roleLabel}</p>
                                </div>
                                <i className="fa-solid fa-chevron-down text-[10px] text-[#999] dark:text-white/40 hidden md:block"></i>
                            </button>
                            <div className="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-[#1a1025] border border-[#e5e5e5] dark:border-[#2d1f42] shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                                <Link href="/profile" className="flex items-center gap-2 px-4 py-2.5 text-[13px] text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5 hover:text-[#7c3aed] transition-colors">
                                    <i className="fa-solid fa-user text-xs w-4 text-center"></i> Profil Saya
                                </Link>
                                <button
                                    onClick={() => router.post(route('logout'))}
                                    className="flex items-center gap-2 w-full px-4 py-2.5 text-[13px] text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors border-t border-[#e5e5e5] dark:border-[#2d1f42]"
                                >
                                    <i className="fa-solid fa-right-from-bracket text-xs w-4 text-center"></i> Keluar
                                </button>
                            </div>
                        </div>
                    </div>
                </header>

                <main className="flex-1 overflow-y-auto p-4 md:p-6">
                    {children}
                </main>
            </div>

            <style>{`
                @keyframes slide-down {
                    from { opacity: 0; transform: translateY(-10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .animate-slide-down { animation: slide-down 0.3s ease-out; }
            `}</style>
        </div>
    );
}
