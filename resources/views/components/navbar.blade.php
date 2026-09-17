{{-- ╔══════════════════════════════════════════════════════╗ --}}
{{-- ║               TOP NAVBAR                            ║ --}}
{{-- ╚══════════════════════════════════════════════════════╝ --}}

<nav class="fixed inset-x-0 top-0 z-50" id="main-navbar" aria-label="Navigasi utama">
    <div class="relative bg-[#030d1c]/95 backdrop-blur-xl border-b border-[#16263f] shadow-[0_10px_30px_rgba(2,6,23,0.8)]">
        <div class="mx-auto max-w-[1700px] px-4 lg:px-6">
            <div class="flex items-center justify-between gap-3 h-[62px]">

                <div class="flex min-w-0 items-center gap-2 sm:gap-3">
                    <button x-ref="sidebarToggle" @click="sidebarOpen = !sidebarOpen"
                        :aria-expanded="sidebarOpen.toString()" aria-expanded="false"
                        aria-label="Buka atau tutup menu navigasi" aria-controls="logo-sidebar" type="button"
                        class="sm:hidden inline-flex shrink-0 items-center justify-center w-9 h-9 rounded-lg text-slate-300 hover:text-white hover:bg-[#0f1d34] transition-all duration-200 focus-visible:ring-2 focus-visible:ring-violet-500 focus:outline-none ring-1 ring-[#1b2c46] bg-transparent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h11"/>
                        </svg>
                    </button>

                    <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-2.5 group">
                        @php $siteLogo = \App\Models\Setting::where('key', 'site_logo')->value('value'); @endphp
                        @if($siteLogo)
                            <img src="{{ asset('storage/' . $siteLogo) }}" alt="Logo" class="h-7 w-auto object-contain">
                        @else
                            <div class="w-6 h-6 rounded-md bg-gradient-to-br from-[#8b5cf6] to-[#7c3aed] flex items-center justify-center shadow-[0_0_18px_rgba(124,58,237,0.35)]">
                                <i class="fa-solid fa-code text-white text-[9px]"></i>
                            </div>
                        @endif
                        <span class="text-[15px] font-extrabold text-white tracking-tight whitespace-nowrap">
                            {{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'RYAZE' }}
                        </span>
                    </a>
                </div>

                <div class="hidden md:flex items-center justify-center gap-8 text-[12px] font-medium text-slate-300 tracking-[0.08em] uppercase">
                    <a href="#" class="transition hover:text-white">Tentang</a>
                    <a href="#" class="transition hover:text-white">Layanan</a>
                    <a href="#" class="transition hover:text-white">Harga</a>
                    <a href="#" class="transition hover:text-white">Blog</a>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <button type="button" onclick="ryazeToggleTheme(event)" aria-label="Ganti tema"
                        class="relative inline-flex h-8 w-[54px] flex-shrink-0 cursor-pointer items-center rounded-full bg-[#0a162c] ring-1 ring-[#243553] transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-violet-500/40 shadow-inner"
                        role="switch">
                        <span class="pointer-events-none inline-flex h-6 w-6 transform translate-x-1 dark:translate-x-7 items-center justify-center rounded-full bg-white shadow-md transition-all duration-300 ease-in-out">
                            <i class="fa-solid fa-sun text-[9px] text-amber-500 absolute opacity-100 dark:opacity-0 transition-opacity"></i>
                            <i class="fa-solid fa-moon text-[9px] text-violet-600 absolute opacity-0 dark:opacity-100 transition-opacity"></i>
                        </span>
                    </button>

                    <a href="{{ $dashboardUrl ?? url('/') }}" class="inline-flex items-center justify-center h-9 px-4 rounded-lg bg-[#8b5cf6] text-white text-[12px] font-semibold shadow-[0_0_18px_rgba(124,58,237,0.35)] hover:bg-[#7c3aed] transition-all duration-200">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- ╔══════════════════════════════════════════════════════╗ --}}
{{-- ║               SIDEBAR                               ║ --}}
{{-- ╚══════════════════════════════════════════════════════╝ --}}

<aside id="logo-sidebar"
    :class="sidebarOpen && !desktop ? 'translate-x-0' : (desktop ? 'translate-x-0' : '-translate-x-full')"
    class="fixed top-0 left-0 z-40 h-[100dvh] pt-16 transition-transform bg-[#040d1d]/95 border-r border-[#18263f] w-64 sm:translate-x-0 backdrop-blur-xl"
    aria-label="Sidebar">

    <div class="flex h-full flex-col relative">
        <div class="flex-1 overflow-y-auto px-3 py-3">
        @php
            $role = Auth::user()->role ?? '';

            $dashboardUrl = match ($role) {
                'superadmin'    => route('superadmin.dashboard'),
                'admin_joki'    => route('admin_joki.dashboard'),
                'admin_hosting' => route('admin_hosting.dashboard'),
                'user_joki'     => route('user_joki.dashboard'),
                'user_hosting'  => route('user_hosting.dashboard'),
                default         => url('/'),
            };

            $isAdmin        = in_array($role, ['superadmin', 'admin_joki', 'admin_hosting']);
            $isAdminHosting = in_array($role, ['superadmin', 'admin_hosting']);
            $isAdminJoki    = in_array($role, ['superadmin', 'admin_joki']);
            $isUserHosting  = in_array($role, ['superadmin', 'user_hosting', 'admin_hosting']);
            $isUserJoki     = in_array($role, ['superadmin', 'user_joki', 'admin_joki']);
            $isUser         = in_array($role, ['superadmin', 'user_joki', 'user_hosting', 'admin_hosting', 'admin_joki']);

            $navLink = fn($active) =>
                'group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-200 ' .
                ($active
                    ? 'bg-[#8b5cf6]/12 text-white ring-1 ring-[#8b5cf6]/20'
                    : 'text-slate-300 hover:bg-[#0d1a2d] hover:text-white');

            $iconBox = fn($active) =>
                'flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-md text-[11px] transition-all duration-200 ' .
                ($active
                    ? 'bg-[#8b5cf6]/20 text-violet-200'
                    : 'bg-transparent text-slate-500 group-hover:text-violet-300');

            $sectionLabel = 'flex items-center gap-2 px-3 pt-5 pb-2 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500';
        @endphp

        <nav class="px-3 py-3 space-y-0.5">

            {{-- Dashboard --}}
            <a href="{{ $dashboardUrl }}" class="{{ $navLink(request()->routeIs('*.dashboard')) }}">
                <span class="{{ $iconBox(request()->routeIs('*.dashboard')) }}">
                    <i class="fa-solid fa-border-all"></i>
                </span>
                <span>Dashboard</span>
                @if(request()->routeIs('*.dashboard'))
                    <span class="ms-auto w-1.5 h-1.5 rounded-full bg-white/60"></span>
                @endif
            </a>

            {{-- ── SISTEM UTAMA (SUPERADMIN) ──────────────────────────────────── --}}
            @if ($role === 'superadmin')
                <div class="{{ $sectionLabel }}">
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                    <span>Sistem Utama</span>
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                </div>

                <a href="{{ route('superadmin.users.index') }}" class="{{ $navLink(request()->routeIs('superadmin.users*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('superadmin.users*')) }}"><i class="fa-solid fa-users"></i></span>
                    <span>Data Pengguna</span>
                </a>
                <a href="{{ route('superadmin.portfolios.index') }}" class="{{ $navLink(request()->routeIs('superadmin.portfolios*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('superadmin.portfolios*')) }}"><i class="fa-solid fa-briefcase"></i></span>
                    <span>Portofolio</span>
                </a>
                <a href="{{ route('admin.promo_events.index') }}" class="{{ $navLink(request()->routeIs('admin.promo_events*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('admin.promo_events*')) }}"><i class="fa-solid fa-bullhorn"></i></span>
                    <span>Promo Event</span>
                </a>
                <a href="{{ route('superadmin.articles.index') }}" class="{{ $navLink(request()->routeIs('superadmin.articles*') || request()->routeIs('superadmin.article_categories*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('superadmin.articles*') || request()->routeIs('superadmin.article_categories*')) }}"><i class="fa-solid fa-newspaper"></i></span>
                    <span>Manajemen Blog</span>
                </a>
                <a href="{{ route('superadmin.settings') }}" class="{{ $navLink(request()->routeIs('superadmin.settings*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('superadmin.settings*')) }}"><i class="fa-solid fa-sliders"></i></span>
                    <span>Pengaturan Sistem</span>
                </a>
                <a href="{{ route('superadmin.backup.index') }}" class="{{ $navLink(request()->routeIs('superadmin.backup*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('superadmin.backup*')) }}"><i class="fa-solid fa-box-archive"></i></span>
                    <span>Sistem Backup</span>
                </a>
                <a href="{{ route('superadmin.withdrawals.index') }}" class="{{ $navLink(request()->routeIs('superadmin.withdrawals*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('superadmin.withdrawals*')) }}"><i class="fa-solid fa-money-bill-transfer"></i></span>
                    <span>Kelola Penarikan</span>
                </a>
                <a href="{{ route('superadmin.finance') }}" class="{{ $navLink(request()->routeIs('superadmin.finance')) }}">
                    <span class="{{ $iconBox(request()->routeIs('superadmin.finance')) }}"><i class="fa-solid fa-chart-pie"></i></span>
                    <span>Laporan Keuangan</span>
                </a>
            @endif

            {{-- ── MANAJEMEN JOKI ──────────────────────────────────────────────── --}}
            @if ($isAdminJoki)
                <div class="{{ $sectionLabel }}">
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                    <span>Manajemen Joki</span>
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                </div>

                <a href="{{ route('admin_joki.orders') }}" class="{{ $navLink(request()->routeIs('admin_joki.orders*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('admin_joki.orders*')) }}"><i class="fa-solid fa-code-branch"></i></span>
                    <span>Kelola Pesanan Joki</span>
                </a>
                <a href="{{ route('admin_joki.services.index') }}" class="{{ $navLink(request()->routeIs('admin_joki.services*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('admin_joki.services*')) }}"><i class="fa-solid fa-list-check"></i></span>
                    <span>Manajemen Layanan</span>
                </a>
                <a href="{{ route('admin_joki.finance') }}" class="{{ $navLink(request()->routeIs('admin_joki.finance')) }}">
                    <span class="{{ $iconBox(request()->routeIs('admin_joki.finance')) }}"><i class="fa-solid fa-wallet"></i></span>
                    <span>Keuangan Joki</span>
                </a>
            @endif

            {{-- ── MANAJEMEN HOSTING ───────────────────────────────────────────── --}}
            @if ($isAdminHosting)
                <div class="{{ $sectionLabel }}">
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                    <span>Manajemen Hosting</span>
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                </div>

                <a href="{{ route('admin_hosting.projects') }}" class="{{ $navLink(request()->routeIs('admin_hosting.projects')) }}">
                    <span class="{{ $iconBox(request()->routeIs('admin_hosting.projects')) }}"><i class="fa-solid fa-server"></i></span>
                    <span>Kelola Project</span>
                </a>
                <a href="{{ route('admin_hosting.deployments') }}" class="{{ $navLink(request()->routeIs('admin_hosting.deployments')) }}">
                    <span class="{{ $iconBox(request()->routeIs('admin_hosting.deployments')) }}"><i class="fa-solid fa-clock-rotate-left"></i></span>
                    <span>Riwayat Deploy</span>
                </a>
                <a href="{{ route('admin_hosting.pending') }}" class="{{ $navLink(request()->routeIs('admin_hosting.pending')) }}">
                    <span class="{{ $iconBox(request()->routeIs('admin_hosting.pending')) }}"><i class="fa-solid fa-triangle-exclamation"></i></span>
                    <span>Butuh Tindakan</span>
                </a>
                <a href="{{ route('admin_hosting.databases') }}" class="{{ $navLink(request()->routeIs('admin_hosting.databases')) }}">
                    <span class="{{ $iconBox(request()->routeIs('admin_hosting.databases')) }}"><i class="fa-solid fa-database"></i></span>
                    <span>Semua Database</span>
                </a>
                <a href="{{ route('admin_hosting.storage') }}" class="{{ $navLink(request()->routeIs('admin_hosting.storage')) }}">
                    <span class="{{ $iconBox(request()->routeIs('admin_hosting.storage')) }}"><i class="fa-solid fa-hard-drive"></i></span>
                    <span>Limit Penyimpanan</span>
                </a>
                <a href="{{ route('admin_hosting.billing') }}" class="{{ $navLink(request()->routeIs('admin_hosting.billing')) }}">
                    <span class="{{ $iconBox(request()->routeIs('admin_hosting.billing')) }}"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                    <span>Kelola Tagihan</span>
                </a>
                <a href="{{ route('admin_hosting.vouchers.index') }}" class="{{ $navLink(request()->routeIs('admin_hosting.vouchers*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('admin_hosting.vouchers*')) }}"><i class="fa-solid fa-ticket"></i></span>
                    <span>Kelola Voucher</span>
                </a>
                <a href="{{ route('admin_hosting.tickets.index') }}" class="{{ $navLink(request()->routeIs('admin_hosting.tickets*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('admin_hosting.tickets*')) }}"><i class="fa-solid fa-headset"></i></span>
                    <span>Kelola Tiket</span>
                </a>
            @endif

            {{-- ── LAYANAN KLIEN JOKI ──────────────────────────────────────────── --}}
            @if ($isUserJoki)
                <div class="{{ $sectionLabel }}">
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                    <span>Layanan Joki</span>
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                </div>

                <a href="{{ route('user_joki.create') }}" class="{{ $navLink(request()->routeIs('user_joki.create')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_joki.create')) }}"><i class="fa-solid fa-cart-plus"></i></span>
                    <span>Buat Pesanan</span>
                </a>
                <a href="{{ route('user_joki.progress') }}" class="{{ $navLink(request()->routeIs('user_joki.progress')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_joki.progress')) }}"><i class="fa-solid fa-laptop-code"></i></span>
                    <span>Progres Pengerjaan</span>
                </a>
                <a href="{{ route('user_joki.riwayat') }}" class="{{ $navLink(request()->routeIs('user_joki.riwayat')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_joki.riwayat')) }}"><i class="fa-solid fa-clock-rotate-left"></i></span>
                    <span>Riwayat Selesai</span>
                </a>
                <a href="{{ route('user_joki.billing') }}" class="{{ $navLink(request()->routeIs('user_joki.billing')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_joki.billing')) }}"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                    <span>Riwayat Tagihan</span>
                </a>
            @endif

            {{-- ── LAYANAN KLIEN HOSTING ───────────────────────────────────────── --}}
            @if ($isUserHosting)
                <div class="{{ $sectionLabel }}">
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                    <span>Hosting</span>
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                </div>

                <a href="{{ route('user_hosting.create') }}"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-all duration-200 bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:scale-[1.01]">
                    <span class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-white/20 text-white text-xs">
                        <i class="fa-solid fa-rocket"></i>
                    </span>
                    <span>Deploy Aplikasi</span>
                    <i class="fa-solid fa-plus ms-auto text-white/70 text-xs"></i>
                </a>

                <a href="{{ route('user_hosting.marketplace') }}" class="{{ $navLink(request()->routeIs('user_hosting.marketplace')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_hosting.marketplace')) }}"><i class="fa-solid fa-store"></i></span>
                    <span>App Marketplace</span>
                </a>
                <a href="{{ route('user_hosting.apk.index') }}" class="{{ $navLink(request()->routeIs('user_hosting.apk*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_hosting.apk*')) }}"><i class="fa-brands fa-android"></i></span>
                    <span>Web to APK</span>
                </a>
                <a href="{{ route('user_hosting.tunnels.index') }}" class="{{ $navLink(request()->routeIs('user_hosting.tunnels*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_hosting.tunnels*')) }}"><i class="fa-solid fa-network-wired"></i></span>
                    <span>Local Tunnels</span>
                    <span class="ms-auto px-1.5 py-0.5 text-[9px] font-bold bg-violet-100 dark:bg-violet-500/20 text-violet-600 dark:text-violet-400 rounded-md">Beta</span>
                </a>
                <a href="{{ route('user_hosting.templates') }}" class="{{ $navLink(request()->routeIs('user_hosting.templates')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_hosting.templates')) }}"><i class="fa-solid fa-layer-group"></i></span>
                    <span>Galeri Template</span>
                </a>
                <a href="{{ route('user_hosting.projects') }}" class="{{ $navLink(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }}"><i class="fa-solid fa-terminal"></i></span>
                    <span>Proyek Aktif</span>
                </a>
                <a href="{{ route('user_hosting.databases') }}" class="{{ $navLink(request()->routeIs('user_hosting.databases') && !request()->routeIs('user_hosting.databases.pma')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_hosting.databases') && !request()->routeIs('user_hosting.databases.pma')) }}"><i class="fa-solid fa-database"></i></span>
                    <span>Database</span>
                </a>
                <a href="{{ route('user_hosting.databases.pma') }}" class="{{ $navLink(request()->routeIs('user_hosting.databases.pma')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_hosting.databases.pma')) }}"><i class="fa-solid fa-table-columns"></i></span>
                    <span>DB Manager</span>
                </a>
                <a href="{{ route('user_hosting.storage') }}" class="{{ $navLink(request()->routeIs('user_hosting.storage*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_hosting.storage*')) }}"><i class="fa-solid fa-folder-open"></i></span>
                    <span>File & Storage</span>
                </a>
                <a href="{{ route('user_hosting.docs') }}" class="{{ $navLink(request()->routeIs('user_hosting.docs*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_hosting.docs*')) }}"><i class="fa-solid fa-book-open"></i></span>
                    <span>Dokumentasi</span>
                </a>

                <div class="{{ $sectionLabel }}">
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                    <span>Akun</span>
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                </div>

                <a href="{{ route('user_hosting.subscription') }}" class="{{ $navLink(request()->routeIs('user_hosting.subscription')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_hosting.subscription')) }}"><i class="fa-solid fa-crown"></i></span>
                    <span>Langganan Paket</span>
                    @if(!Auth::user()->hasActiveHostingSubscription())
                        <span class="ms-auto px-2 py-0.5 text-[9px] font-bold bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-md animate-pulse">Beli</span>
                    @else
                        <span class="ms-auto w-2 h-2 rounded-full bg-emerald-400"></span>
                    @endif
                </a>
                <a href="{{ route('user_hosting.billing') }}" class="{{ $navLink(request()->routeIs('user_hosting.billing')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_hosting.billing')) }}"><i class="fa-solid fa-receipt"></i></span>
                    <span>Tagihan / Billing</span>
                </a>
                <a href="{{ route('user_hosting.tickets.index') }}" class="{{ $navLink(request()->routeIs('user_hosting.tickets*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user_hosting.tickets*')) }}"><i class="fa-solid fa-headset"></i></span>
                    <span>Tiket Bantuan</span>
                </a>
            @endif

            {{-- ── WALLET & AFFILIATE ─────────────────────────────────────────── --}}
            @if ($isUser)
                <div class="{{ $sectionLabel }}">
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                    <span>Pendapatan</span>
                    <span class="flex-1 h-px bg-slate-200 dark:bg-white/5"></span>
                </div>

                <a href="{{ route('user.wallet.history') }}" class="{{ $navLink(request()->routeIs('user.wallet*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user.wallet*')) }}"><i class="fa-solid fa-wallet"></i></span>
                    <span>Wallet Saya</span>
                </a>
                <a href="{{ route('user.affiliate.dashboard') }}" class="{{ $navLink(request()->routeIs('user.affiliate*')) }}">
                    <span class="{{ $iconBox(request()->routeIs('user.affiliate*')) }}"><i class="fa-solid fa-users-viewfinder"></i></span>
                    <span>Program Affiliate</span>
                </a>
            @endif

        </nav>

        </div>

        <div class="flex-shrink-0 p-3 border-t border-[#18263f] bg-[#060d1b]/80 backdrop-blur-sm">
            <div class="flex items-center gap-3 px-2.5 py-2 rounded-lg bg-[#0a162c] ring-1 ring-[#1b2c46]">
                <div class="flex-shrink-0 w-8 h-8 rounded-md bg-gradient-to-br from-[#8b5cf6] to-[#7c3aed] flex items-center justify-center text-white text-[10px] font-bold">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-semibold text-white truncate">{{ Auth::user()->name ?? 'Guest' }}</p>
                    <p class="text-[9px] text-slate-400 truncate">{{ Auth::check() ? ucwords(str_replace('_', ' ', Auth::user()->role)) : '' }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-md text-slate-400 hover:text-white hover:bg-[#0f1d34] transition-colors" title="Keluar">
                        <i class="fa-solid fa-right-from-bracket text-[10px]"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

{{-- Popup untuk user tanpa langganan hosting aktif saat klik menu Proyek Aktif --}}
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
            confirmButtonColor: '#4f46e5',
        }).then(function (result) {
            if (result.isConfirmed) {
                window.location.href = '{{ route('user_hosting.subscription') }}';
            }
        });
    });
});
</script>
@endif
