@php
    $siteLogo = \App\Models\Setting::where('key', 'site_logo')->value('value');
    $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'RYAZE';
    $unreadNotifications = Auth::check() ? Auth::user()->unreadNotifications : collect([]);
    $role = Auth::user()->role ?? '';

    $dashboardUrl = match ($role) {
        'superadmin' => route('superadmin.dashboard'),
        'admin_joki' => route('admin_joki.dashboard'),
        'admin_hosting' => route('admin_hosting.dashboard'),
        'user_joki' => route('user_joki.dashboard'),
        'user_hosting' => route('user_hosting.dashboard'),
        default => url('/'),
    };

    $isAdmin = in_array($role, ['superadmin', 'admin_joki', 'admin_hosting']);
    $isAdminHosting = in_array($role, ['superadmin', 'admin_hosting']);
    $isAdminJoki = in_array($role, ['superadmin', 'admin_joki']);
    $isUserHosting = in_array($role, ['superadmin', 'user_hosting', 'admin_hosting']);
    $isUserJoki = in_array($role, ['superadmin', 'user_joki', 'admin_joki']);
    $isUser = in_array($role, ['superadmin', 'user_joki', 'user_hosting', 'admin_hosting', 'admin_joki']);

    $navLink = fn($active) => 'flex items-center gap-3 px-3 py-2 transition-all duration-150 group text-[13px] font-medium sidebar-link ' .
        ($active
            ? 'bg-[#7c3aed] text-white'
            : 'text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5 hover:text-[#7c3aed] dark:hover:text-white');

    $iconClass = fn($active) => 'w-5 text-center text-sm shrink-0 transition-transform group-hover:scale-110 ' .
        ($active ? 'text-white' : 'text-[#999] dark:text-white/40 group-hover:text-[#7c3aed]');
@endphp

<aside id="logo-sidebar"
    x-data="{ collapsed: localStorage.getItem('ryaze-sidebar') === 'collapsed' }"
    x-init="$watch('collapsed', v => localStorage.setItem('ryaze-sidebar', v ? 'collapsed' : 'expanded'))"
    :class="collapsed ? 'w-[60px]' : 'w-60'"
    class="fixed top-0 left-0 z-40 h-[100dvh] transition-all duration-300 bg-white dark:bg-[#0d0d18] border-r border-[#e5e5e5] dark:border-[#1a1a2e] hidden lg:flex flex-col"
    aria-label="Sidebar">

    {{-- Logo --}}
    <div :class="collapsed ? 'justify-center px-2' : 'px-4'" class="h-14 flex items-center border-b border-[#e5e5e5] dark:border-[#1a1a2e] shrink-0">
        <a href="/" class="flex items-center gap-2.5 shrink-0">
            <div class="w-7 h-7 bg-[#7c3aed] flex items-center justify-center shrink-0">
                <span class="text-white font-black text-xs">R</span>
            </div>
            <span x-show="!collapsed" x-transition class="font-black text-[#7c3aed] dark:text-white text-sm tracking-tight">{{ $siteName }}</span>
        </a>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto py-3 space-y-4">

        {{-- Dashboard --}}
        <div>
            <a href="{{ $dashboardUrl }}" class="{{ $navLink(request()->routeIs('*.dashboard')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                <i class="fa-solid fa-border-all {{ $iconClass(request()->routeIs('*.dashboard')) }}"></i>
                <span x-show="!collapsed" class="flex-1 truncate">Dashboard</span>
            </a>
        </div>

        {{-- SISTEM UTAMA (SUPERADMIN) --}}
        @if ($role === 'superadmin')
            <div>
                <div x-show="!collapsed" class="px-5 pb-1.5">
                    <span class="text-[10px] font-bold text-[#999] dark:text-white/30 uppercase tracking-wider">Sistem Utama</span>
                </div>
                <div x-show="collapsed" class="mx-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e]"></div>
                <div class="space-y-0.5">
                    <a href="{{ route('superadmin.users.index') }}" class="{{ $navLink(request()->routeIs('superadmin.users*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-users {{ $iconClass(request()->routeIs('superadmin.users*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Data Pengguna</span>
                    </a>
                    <a href="{{ route('superadmin.portfolios.index') }}" class="{{ $navLink(request()->routeIs('superadmin.portfolios*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-briefcase {{ $iconClass(request()->routeIs('superadmin.portfolios*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Portofolio</span>
                    </a>
                    <a href="{{ route('admin.promo_events.index') }}" class="{{ $navLink(request()->routeIs('admin.promo_events*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-bullhorn {{ $iconClass(request()->routeIs('admin.promo_events*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Promo Event</span>
                    </a>
                    <a href="{{ route('superadmin.articles.index') }}" class="{{ $navLink(request()->routeIs('superadmin.articles*') || request()->routeIs('superadmin.article_categories*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-newspaper {{ $iconClass(request()->routeIs('superadmin.articles*') || request()->routeIs('superadmin.article_categories*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Blog</span>
                    </a>
                    <a href="{{ route('superadmin.settings') }}" class="{{ $navLink(request()->routeIs('superadmin.settings*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-cogs {{ $iconClass(request()->routeIs('superadmin.settings*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Pengaturan</span>
                    </a>
                    <a href="{{ route('superadmin.backup.index') }}" class="{{ $navLink(request()->routeIs('superadmin.backup*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-box-archive {{ $iconClass(request()->routeIs('superadmin.backup*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Backup</span>
                    </a>
                    <a href="{{ route('superadmin.withdrawals.index') }}" class="{{ $navLink(request()->routeIs('superadmin.withdrawals*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-money-bill-transfer {{ $iconClass(request()->routeIs('superadmin.withdrawals*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Penarikan</span>
                    </a>
                    <a href="{{ route('superadmin.finance') }}" class="{{ $navLink(request()->routeIs('superadmin.finance')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-chart-pie {{ $iconClass(request()->routeIs('superadmin.finance')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Keuangan</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- MANAJEMEN JOKI --}}
        @if ($isAdminJoki)
            <div>
                <div x-show="!collapsed" class="px-5 pb-1.5">
                    <span class="text-[10px] font-bold text-[#999] dark:text-white/30 uppercase tracking-wider">Joki</span>
                </div>
                <div x-show="collapsed" class="mx-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e]"></div>
                <div class="space-y-0.5">
                    <a href="{{ route('admin_joki.orders') }}" class="{{ $navLink(request()->routeIs('admin_joki.orders*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-code-branch {{ $iconClass(request()->routeIs('admin_joki.orders*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Pesanan</span>
                    </a>
                    <a href="{{ route('admin_joki.services.index') }}" class="{{ $navLink(request()->routeIs('admin_joki.services*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-list {{ $iconClass(request()->routeIs('admin_joki.services*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Layanan</span>
                    </a>
                    <a href="{{ route('admin_joki.finance') }}" class="{{ $navLink(request()->routeIs('admin_joki.finance')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-wallet {{ $iconClass(request()->routeIs('admin_joki.finance')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Keuangan</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- MANAJEMEN HOSTING --}}
        @if ($isAdminHosting)
            <div>
                <div x-show="!collapsed" class="px-5 pb-1.5">
                    <span class="text-[10px] font-bold text-[#999] dark:text-white/30 uppercase tracking-wider">Hosting</span>
                </div>
                <div x-show="collapsed" class="mx-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e]"></div>
                <div class="space-y-0.5">
                    <a href="{{ route('admin_hosting.projects') }}" class="{{ $navLink(request()->routeIs('admin_hosting.projects')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-server {{ $iconClass(request()->routeIs('admin_hosting.projects')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Project</span>
                    </a>
                    <a href="{{ route('admin_hosting.deployments') }}" class="{{ $navLink(request()->routeIs('admin_hosting.deployments')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-history {{ $iconClass(request()->routeIs('admin_hosting.deployments')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Deploy</span>
                    </a>
                    <a href="{{ route('admin_hosting.pending') }}" class="{{ $navLink(request()->routeIs('admin_hosting.pending')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-warning {{ $iconClass(request()->routeIs('admin_hosting.pending')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Pending</span>
                    </a>
                    <a href="{{ route('admin_hosting.databases') }}" class="{{ $navLink(request()->routeIs('admin_hosting.databases')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-database {{ $iconClass(request()->routeIs('admin_hosting.databases')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Database</span>
                    </a>
                    <a href="{{ route('admin_hosting.storage') }}" class="{{ $navLink(request()->routeIs('admin_hosting.storage')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-hard-drive {{ $iconClass(request()->routeIs('admin_hosting.storage')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Storage</span>
                    </a>
                    <a href="{{ route('admin_hosting.billing') }}" class="{{ $navLink(request()->routeIs('admin_hosting.billing')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-file-invoice-dollar {{ $iconClass(request()->routeIs('admin_hosting.billing')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Tagihan</span>
                    </a>
                    <a href="{{ route('admin_hosting.vouchers.index') }}" class="{{ $navLink(request()->routeIs('admin_hosting.vouchers*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-ticket {{ $iconClass(request()->routeIs('admin_hosting.vouchers*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Voucher</span>
                    </a>
                    <a href="{{ route('admin_hosting.tickets.index') }}" class="{{ $navLink(request()->routeIs('admin_hosting.tickets*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-headset {{ $iconClass(request()->routeIs('admin_hosting.tickets*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Tiket</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- LAYANAN KLIEN JOKI --}}
        @if ($isUserJoki)
            <div>
                <div x-show="!collapsed" class="px-5 pb-1.5">
                    <span class="text-[10px] font-bold text-[#999] dark:text-white/30 uppercase tracking-wider">Joki</span>
                </div>
                <div x-show="collapsed" class="mx-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e]"></div>
                <div class="space-y-0.5">
                    <a href="{{ route('user_joki.create') }}" class="{{ $navLink(request()->routeIs('user_joki.create')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-cart-plus {{ $iconClass(request()->routeIs('user_joki.create')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Buat Pesanan</span>
                    </a>
                    <a href="{{ route('user_joki.progress') }}" class="{{ $navLink(request()->routeIs('user_joki.progress')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-laptop-code {{ $iconClass(request()->routeIs('user_joki.progress')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Progres</span>
                    </a>
                    <a href="{{ route('user_joki.riwayat') }}" class="{{ $navLink(request()->routeIs('user_joki.riwayat')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-history {{ $iconClass(request()->routeIs('user_joki.riwayat')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Riwayat</span>
                    </a>
                    <a href="{{ route('user_joki.billing') }}" class="{{ $navLink(request()->routeIs('user_joki.billing')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-file-invoice-dollar {{ $iconClass(request()->routeIs('user_joki.billing')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Tagihan</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- LAYANAN KLIEN HOSTING --}}
        @if ($isUserHosting)
            <div>
                <div x-show="!collapsed" class="px-5 pb-1.5">
                    <span class="text-[10px] font-bold text-[#999] dark:text-white/30 uppercase tracking-wider">Hosting</span>
                </div>
                <div x-show="collapsed" class="mx-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e]"></div>
                <div class="space-y-0.5">
                    <a href="{{ route('user_hosting.create') }}" class="{{ $navLink(request()->routeIs('user_hosting.create')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-rocket {{ $iconClass(request()->routeIs('user_hosting.create')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Deploy</span>
                    </a>
                    <a href="{{ route('user_hosting.marketplace') }}" class="{{ $navLink(request()->routeIs('user_hosting.marketplace')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-store {{ $iconClass(request()->routeIs('user_hosting.marketplace')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Marketplace</span>
                    </a>
                    <a href="{{ route('user_hosting.apk.index') }}" class="{{ $navLink(request()->routeIs('user_hosting.apk*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-brands fa-android {{ $iconClass(request()->routeIs('user_hosting.apk*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Web to APK</span>
                    </a>
                    <a href="{{ route('user_hosting.tunnels.index') }}" class="{{ $navLink(request()->routeIs('user_hosting.tunnels*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-network-wired {{ $iconClass(request()->routeIs('user_hosting.tunnels*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Tunnels</span>
                        <span x-show="!collapsed" class="px-1.5 py-0.5 text-[9px] font-bold bg-[#7c3aed]/10 text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]">Beta</span>
                    </a>
                    <a href="{{ route('user_hosting.templates') }}" class="{{ $navLink(request()->routeIs('user_hosting.templates')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-layer-group {{ $iconClass(request()->routeIs('user_hosting.templates')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Template</span>
                    </a>
                    <a href="{{ route('user_hosting.projects') }}" class="{{ $navLink(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-terminal {{ $iconClass(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Proyek</span>
                    </a>
                    <a href="{{ route('user_hosting.databases') }}" class="{{ $navLink(request()->routeIs('user_hosting.databases') && !request()->routeIs('user_hosting.databases.pma')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-database {{ $iconClass(request()->routeIs('user_hosting.databases') && !request()->routeIs('user_hosting.databases.pma')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Database</span>
                    </a>
                    <a href="{{ route('user_hosting.databases.pma') }}" class="{{ $navLink(request()->routeIs('user_hosting.databases.pma')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-server {{ $iconClass(request()->routeIs('user_hosting.databases.pma')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">DB Manager</span>
                    </a>
                    <a href="{{ route('user_hosting.storage') }}" class="{{ $navLink(request()->routeIs('user_hosting.storage*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-hard-drive {{ $iconClass(request()->routeIs('user_hosting.storage*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Storage</span>
                    </a>
                    <a href="{{ route('user_hosting.docs') }}" class="{{ $navLink(request()->routeIs('user_hosting.docs*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-book {{ $iconClass(request()->routeIs('user_hosting.docs*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Dokumentasi</span>
                    </a>
                </div>
            </div>

            <div>
                <div x-show="!collapsed" class="px-5 pb-1.5">
                    <span class="text-[10px] font-bold text-[#999] dark:text-white/30 uppercase tracking-wider">Akun</span>
                </div>
                <div x-show="collapsed" class="mx-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e]"></div>
                <div class="space-y-0.5">
                    <a href="{{ route('user_hosting.subscription') }}" class="{{ $navLink(request()->routeIs('user_hosting.subscription')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-crown {{ $iconClass(request()->routeIs('user_hosting.subscription')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Langganan</span>
                        @if(!Auth::user()->hasActiveHostingSubscription())
                            <span x-show="!collapsed" class="px-1.5 py-0.5 text-[9px] font-bold text-red-500 bg-red-100 dark:text-red-300 dark:bg-red-500/20">Beli</span>
                        @endif
                    </a>
                    <a href="{{ route('user_hosting.billing') }}" class="{{ $navLink(request()->routeIs('user_hosting.billing')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-file-invoice-dollar {{ $iconClass(request()->routeIs('user_hosting.billing')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Billing</span>
                    </a>
                    <a href="{{ route('user_hosting.tickets.index') }}" class="{{ $navLink(request()->routeIs('user_hosting.tickets*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-life-ring {{ $iconClass(request()->routeIs('user_hosting.tickets*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Tiket</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- WALLET & AFFILIATE --}}
        @if ($isUser)
            <div>
                <div x-show="!collapsed" class="px-5 pb-1.5">
                    <span class="text-[10px] font-bold text-[#999] dark:text-white/30 uppercase tracking-wider">Pendapatan</span>
                </div>
                <div x-show="collapsed" class="mx-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e]"></div>
                <div class="space-y-0.5">
                    <a href="{{ route('user.wallet.history') }}" class="{{ $navLink(request()->routeIs('user.wallet*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-wallet {{ $iconClass(request()->routeIs('user.wallet*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Wallet</span>
                    </a>
                    <a href="{{ route('user.affiliate.dashboard') }}" class="{{ $navLink(request()->routeIs('user.affiliate*')) }}" :class="collapsed ? 'justify-center px-0 mx-2' : 'px-3 mx-2'">
                        <i class="fa-solid fa-users-viewfinder {{ $iconClass(request()->routeIs('user.affiliate*')) }}"></i>
                        <span x-show="!collapsed" class="flex-1 truncate">Affiliate</span>
                    </a>
                </div>
            </div>
        @endif

    </nav>

    {{-- Collapse button --}}
    <div class="border-t border-[#e5e5e5] dark:border-[#1a1a2e] p-2 shrink-0">
        <button @click="collapsed = !collapsed"
            class="flex items-center justify-center w-full py-2.5 text-[#999] dark:text-white/30 hover:text-[#7c3aed] dark:hover:text-white transition-colors">
            <i class="fa-solid" :class="collapsed ? 'fa-angles-right' : 'fa-angles-left'"></i>
            <span x-show="!collapsed" class="ml-2 text-[13px] font-medium">Tutup</span>
        </button>
    </div>
</aside>

{{-- Mobile overlay --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden" onclick="document.getElementById('logo-sidebar').classList.add('-translate-x-full'); this.classList.add('hidden');"></div>

{{-- Navbar --}}
<nav class="fixed top-0 z-50 w-full bg-white dark:bg-[#0d0d18] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
    <div class="h-14 px-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button onclick="document.getElementById('logo-sidebar').classList.toggle('-translate-x-full'); document.getElementById('sidebar-overlay').classList.toggle('hidden');"
                class="lg:hidden w-8 h-8 flex items-center justify-center text-[#666] dark:text-white/60 hover:text-[#7c3aed] transition-colors">
                <i class="fa-solid fa-bars text-base"></i>
            </button>
            <span class="text-[15px] font-bold text-[#333] dark:text-white">@yield('title', 'Dashboard')</span>
        </div>

        <div class="flex items-center gap-1">
            {{-- Toggle Tema --}}
            <button type="button" onclick="ryazeToggleTheme(event)" aria-label="Ganti tema"
                class="w-8 h-8 flex items-center justify-center text-[#999] dark:text-white/40 hover:text-[#7c3aed] dark:hover:text-white transition-colors">
                <i class="fa-solid fa-moon text-sm dark:hidden"></i>
                <i class="fa-solid fa-sun text-sm hidden dark:inline"></i>
            </button>

            {{-- Notifikasi --}}
            <button id="dropdownNotificationButton" data-dropdown-toggle="dropdownNotification"
                class="relative w-8 h-8 flex items-center justify-center text-[#999] dark:text-white/40 hover:text-[#7c3aed] dark:hover:text-white transition-colors"
                type="button">
                <i class="fa-solid fa-bell text-sm"></i>
                @if ($unreadNotifications->count() > 0)
                    <div class="absolute w-4 h-4 bg-red-500 text-white text-[9px] font-bold flex items-center justify-center -top-0.5 -right-0.5">
                        {{ $unreadNotifications->count() > 9 ? '9+' : $unreadNotifications->count() }}
                    </div>
                @endif
            </button>

            <div id="dropdownNotification"
                class="z-50 hidden w-72 bg-white dark:bg-[#1a1025] border border-[#e5e5e5] dark:border-[#2d1f42] shadow-lg"
                aria-labelledby="dropdownNotificationButton">
                <div class="px-4 py-3 border-b border-[#e5e5e5] dark:border-[#2d1f42] flex items-center justify-between">
                    <span class="text-sm font-semibold text-[#333] dark:text-white">Notifikasi</span>
                    @if ($unreadNotifications->count() > 0)
                        <form action="{{ route('notifications.markAllRead') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-[#7c3aed] hover:text-[#6d28d9]">Tandai Dibaca</button>
                        </form>
                    @endif
                </div>
                <div class="divide-y divide-[#e5e5e5] dark:divide-[#2d1f42] max-h-80 overflow-y-auto">
                    @forelse($unreadNotifications as $notification)
                        <a href="#" onclick="event.preventDefault(); document.getElementById('mark-read-{{ $notification->id }}').submit();"
                            class="flex px-4 py-3 hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                            <div class="w-full pl-3">
                                <div class="text-[13px] text-[#666] dark:text-white/60 mb-1">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</div>
                                <div class="text-[11px] text-[#999] dark:text-white/30">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                        </a>
                        <form id="mark-read-{{ $notification->id }}" action="{{ route('notifications.markRead', $notification->id) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @empty
                        <p class="px-4 py-6 text-sm text-[#999] dark:text-white/40 text-center">Belum ada notifikasi.</p>
                    @endforelse
                </div>
            </div>

            <div class="w-px h-5 bg-[#e5e5e5] dark:bg-[#1a1a2e] mx-1.5"></div>

            {{-- User --}}
            <div class="relative group">
                <button class="flex items-center gap-2.5 p-1 hover:bg-[#f5f0ff] dark:hover:bg-white/5 transition-colors">
                    <div class="w-8 h-8 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa] flex items-center justify-center text-sm font-bold">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-[13px] font-semibold text-[#333] dark:text-white leading-tight">{{ Auth::user()->name ?? 'Guest' }}</p>
                        <p class="text-[11px] text-[#999] dark:text-white/40 leading-tight">{{ Auth::check() ? ucwords(str_replace('_', ' ', Auth::user()->role)) : '' }}</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-[#999] dark:text-white/40 hidden md:block"></i>
                </button>
                <div class="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-[#1a1025] border border-[#e5e5e5] dark:border-[#2d1f42] shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-[13px] text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5 hover:text-[#7c3aed] transition-colors">
                        <i class="fa-solid fa-user text-xs w-4 text-center"></i> Profil Saya
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 w-full px-4 py-2.5 text-[13px] text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors border-t border-[#e5e5e5] dark:border-[#2d1f42]">
                            <i class="fa-solid fa-right-from-bracket text-xs w-4 text-center"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- Spacer --}}
<div class="h-14"></div>

{{-- Tooltip for collapsed sidebar --}}
<style>
    #logo-sidebar .sidebar-link { position: relative; }
    #logo-sidebar[data-collapsed="true"] .sidebar-link:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        left: calc(100% + 8px);
        top: 50%;
        transform: translateY(-50%);
        padding: 4px 10px;
        background: #333;
        color: #fff;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
        z-index: 50;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('logo-sidebar');
    if (!sidebar) return;

    function updateCollapsedAttr() {
        sidebar.setAttribute('data-collapsed', sidebar.classList.contains('w-[60px]') ? 'true' : 'false');
    }

    // Observe class changes
    var observer = new MutationObserver(updateCollapsedAttr);
    observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });
    updateCollapsedAttr();

    // Add tooltip data to links
    sidebar.querySelectorAll('.sidebar-link').forEach(function(link) {
        var span = link.querySelector('span:not(.px-1\\:5)');
        if (span) link.setAttribute('data-tooltip', span.textContent.trim());
    });
});
</script>

@if ($isUserHosting && !Auth::user()->hasActiveHostingSubscription())
<script>
document.addEventListener('DOMContentLoaded', function () {
    var proyekLink = document.querySelector('a[href="{{ route('user_hosting.projects') }}"]');
    if (!proyekLink || typeof Swal === 'undefined') return;

    proyekLink.addEventListener('click', function (e) {
        e.preventDefault();
        Swal.fire({
            icon: 'info',
            title: 'Belum Berlangganan Hosting',
            html: 'Untuk <strong>melihat & mengelola proyek hosting</strong>, Anda perlu memiliki paket langganan aktif terlebih dahulu.',
            showCancelButton: true,
            confirmButtonText: 'Lihat Paket',
            cancelButtonText: 'Tutup',
            confirmButtonColor: '#7c3aed',
        }).then(function (result) {
            if (result.isConfirmed) {
                window.location.href = '{{ route('user_hosting.subscription') }}';
            }
        });
    });
});
</script>
@endif
