<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('sidebar', {
            collapsed: JSON.parse(localStorage.getItem('ryaze_sidebar_collapsed') || 'false'),
            toggle() {
                this.collapsed = !this.collapsed;
                localStorage.setItem('ryaze_sidebar_collapsed', JSON.stringify(this.collapsed));
            }
        });
    });
</script>

<nav class="fixed top-0 z-50 w-full shadow-sm bg-white dark:text-white dark:bg-[#0F1D30] border-b border-[#E3EAF1] dark:border-[#2A4463]"
    x-data="{ userMenuOpen: false, notifOpen: false }">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start gap-2 rtl:justify-end">
                <button @click="sidebarOpen = !sidebarOpen" aria-controls="logo-sidebar" type="button"
                    class="inline-flex items-center p-2 text-sm text-[#525252] dark:text-white/60 sm:hidden hover:bg-[#F4F8FC] dark:hover:bg-[#12233A] focus:outline-none">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                        </path>
                    </svg>
                </button>
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 ms-2 md:ms-0">
                    @php
                        $siteLogo = \App\Models\Setting::where('key', 'site_logo')->value('value');
                        $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'RYAZE';
                    @endphp
                    @if ($siteLogo)
                        <img src="{{ asset('storage/' . $siteLogo) }}" alt="Logo" class="h-8 object-contain">
                    @else
                        <div class="bg-[#12233A] text-white border border-[#1D5B96] w-7 h-7 flex items-center justify-center">
                            <span class="font-black font-mono text-xs">R</span>
                        </div>
                    @endif
                    <span
                        class="text-base font-black tracking-[0.15em] text-[#0F1A2A] dark:text-white uppercase font-mono whitespace-nowrap">{{ $siteName }}</span>
                </a>
            </div>

            <div class="flex items-center gap-3">
                {{-- Dark mode toggle --}}
                <button type="button" onclick="ryazeToggleTheme(event)" aria-label="Ganti tema"
                    class="relative w-8 h-8 flex items-center justify-center text-[#8A9BAE] dark:text-white/40 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors focus:outline-none">
                    {{-- Moon: visible in light mode --}}
                    <i
                        class="fa-solid fa-moon text-sm
                        absolute transition-all duration-300 ease-in-out
                        opacity-100 scale-100 rotate-0
                        dark:opacity-0 dark:scale-75 dark:-rotate-90"></i>
                    {{-- Sun: visible in dark mode --}}
                    <i
                        class="fa-solid fa-sun text-sm
                        absolute transition-all duration-300 ease-in-out
                        opacity-0 scale-75 rotate-90
                        dark:opacity-100 dark:scale-100 dark:rotate-0"></i>
                </button>

                @php $unreadNotifications = Auth::check() ? Auth::user()->unreadNotifications : collect([]); @endphp

                {{-- Notifications --}}
                <div class="relative">
                    <button @click="notifOpen = !notifOpen; userMenuOpen = false" @click.outside="notifOpen = false"
                        class="relative inline-flex items-center text-sm font-medium text-center text-[#8A9BAE] hover:text-[#1D5B96] focus:outline-none dark:hover:text-[#6BA8DC] dark:text-white/40 p-2"
                        type="button">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 14 20">
                            <path
                                d="M12.133 10.632v-1.8A5.406 5.406 0 0 0 7.979 3.57.946.946 0 0 0 8 3.464V1.1a1 1 0 0 0-2 0v2.364a.946.946 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C1.867 13.018 0 13.614 0 14.807 0 15.4 0 16 .538 16h12.924C14 16 14 15.4 14 14.807c0-1.193-1.867-1.789-1.867-4.175ZM3.823 17a3.453 3.453 0 0 0 6.354 0H3.823Z" />
                        </svg>
                        @if ($unreadNotifications->count() > 0)
                            <div
                                class="absolute flex items-center justify-center w-4 h-4 bg-rose-500 border-2 border-white -top-0.5 start-4 dark:border-[#0F1D30]">
                                <p class="text-white text-[9px] font-bold font-mono">
                                    {{ $unreadNotifications->count() }}
                                </p>
                            </div>
                        @endif
                    </button>

                    <div x-show="notifOpen" style="display: none;"
                        class="z-50 absolute right-0 mt-2 w-80 max-w-sm bg-white divide-y divide-[#E3EAF1] border border-[#E3EAF1] shadow-2xl dark:bg-[#0F1D30] dark:divide-[#2A4463] dark:border-[#2A4463]">
                        <div
                            class="block px-4 py-2.5 font-mono text-[11px] uppercase tracking-wider font-semibold text-[#0F1A2A] bg-[#F4F8FC] dark:bg-[#12233A] dark:text-white flex justify-between items-center">
                            <span>Notifikasi</span>
                            @if ($unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="text-[10px] text-[#1D5B96] dark:text-[#6BA8DC] hover:underline font-mono">
                                        Tandai Terbaca
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="divide-y divide-[#E3EAF1] dark:divide-[#2A4463] max-h-72 overflow-y-auto">
                            @forelse($unreadNotifications as $notification)
                                <a href="#"
                                    onclick="event.preventDefault(); document.getElementById('mark-read-{{ $notification->id }}').submit();"
                                    class="flex px-4 py-3 hover:bg-[#F4F8FC] dark:hover:bg-[#12233A]/60 transition-colors">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-[#0F1A2A] dark:text-white truncate">
                                            {{ $notification->data['message'] ?? 'Notifikasi baru' }}
                                        </p>
                                        <p class="font-mono text-[10px] text-[#8A9BAE] dark:text-white/40 mt-1">
                                            <i class="fa-regular fa-clock"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </a>
                                <form id="mark-read-{{ $notification->id }}"
                                    action="{{ route('notifications.markRead', $notification->id) }}" method="POST"
                                    class="hidden">@csrf</form>
                            @empty
                                <p class="px-4 py-4 text-xs font-mono text-center text-[#8A9BAE] dark:text-white/40">Belum ada
                                    notifikasi baru.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- User Profile --}}
                <div class="flex items-center gap-3 ms-2 relative">
                    <div class="hidden md:block text-right">
                        <p class="text-xs font-bold text-[#0F1A2A] dark:text-white">
                            {{ Auth::user()->name ?? 'Guest' }}</p>
                        <p class="font-mono text-[10px] text-[#8A9BAE] dark:text-white/40 uppercase tracking-wider">
                            {{ Auth::check() ? ucwords(str_replace('_', ' ', Auth::user()->role)) : '' }}
                        </p>
                    </div>
                    <div>
                        <button @click="userMenuOpen = !userMenuOpen; notifOpen = false"
                            @click.outside="userMenuOpen = false" type="button"
                            class="flex text-xs bg-[#12233A] text-white border border-[#1D5B96] dark:bg-[#1D5B96] dark:border-[#2568A8] items-center justify-center w-8 h-8 font-mono font-bold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </button>
                    </div>

                    <div x-show="userMenuOpen" style="display: none;"
                        class="z-50 absolute right-0 top-10 mt-2 w-48 text-base list-none bg-white divide-y divide-[#E3EAF1] border border-[#E3EAF1] shadow-2xl dark:bg-[#0F1D30] dark:divide-[#2A4463] dark:border-[#2A4463]">
                        <div class="px-4 py-3 md:hidden">
                            <p class="text-xs font-bold text-[#0F1A2A] dark:text-white">{{ Auth::user()->name ?? 'Guest' }}</p>
                            <p class="text-xs font-mono text-[#8A9BAE] truncate dark:text-white/40">
                                {{ Auth::user()->email ?? '' }}</p>
                        </div>
                        <ul class="py-1" role="none">
                            <li>
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-xs font-mono uppercase tracking-wider text-[#525252] hover:bg-[#F4F8FC] hover:text-[#1D5B96] dark:text-white/70 dark:hover:bg-[#12233A] dark:hover:text-[#6BA8DC]">Profil
                                    Saya</a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left block px-4 py-2 text-xs font-mono uppercase tracking-wider text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/20">
                                        Keluar <i class="fa-solid fa-right-from-bracket ms-1"></i>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- SIDEBAR --}}
<aside id="logo-sidebar"
    :class="`${sidebarOpen && !desktop ? 'translate-x-0 shadow-2xl' : (desktop ? 'translate-x-0' : '-translate-x-full')} ${$store.sidebar.collapsed ? 'sm:w-20 w-64' : 'w-64'}`"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 transition-all duration-200 ease-in-out bg-white border-r border-[#E3EAF1] dark:bg-[#0F1D30] dark:border-[#2A4463] flex flex-col"
    aria-label="Sidebar">

    @php
        use App\Helpers\AppVersion;
        $appVersion = AppVersion::get();
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

        // Jumlah konfirmasi QRIS yang menunggu verifikasi, dipakai sebagai badge sidebar.
        $pendingQrisJoki = $pendingQrisHosting = 0;
        if ($isAdmin) {
            $pendingQris = \App\Models\PaymentConfirmation::pending()
                ->selectRaw('order_type, count(*) c')
                ->groupBy('order_type')
                ->pluck('c', 'order_type');
            $pendingQrisJoki = (int) $pendingQris->only(\App\Models\PaymentConfirmation::JOKI_TYPES)->sum();
            $pendingQrisHosting = (int) $pendingQris->only(\App\Models\PaymentConfirmation::HOSTING_TYPES)->sum();
        }
        $pendingQrisAll = $pendingQrisJoki + $pendingQrisHosting;

        // Structured helper for clean, consistent rendering matching Home.jsx
        $item = function ($url, $label, $icon, $active, $badge = null) {
            $activeClass = $active
                ? 'bg-[#12233A] text-white dark:bg-[#1D5B96] dark:text-white font-semibold'
                : 'text-[#525252] dark:text-white/60 hover:bg-[#F4F8FC] dark:hover:bg-[#12233A]/60 hover:text-[#0F1A2A] dark:hover:text-white';
            $iconActive = $active
                ? 'text-white dark:text-white'
                : 'text-[#8A9BAE] group-hover:text-[#0F1A2A] dark:text-white/40 dark:group-hover:text-white';
            $ariaCurrent = $active ? 'aria-current="page"' : '';
            $indicator = $active
                ? '<span class="absolute left-0 top-1 bottom-1 w-1 bg-[#1D5B96] dark:bg-[#6BA8DC]" :class="$store.sidebar.collapsed ? \'hidden\' : \'\'"></span>'
                : '';
            $badgeHtml = $badge
                ? '<span class="ms-auto inline-flex items-center px-1.5 py-0.5 text-[9px] font-mono uppercase tracking-wider font-semibold bg-[#1D5B96]/10 text-[#1D5B96] border border-[#1D5B96]/30 dark:bg-[#6BA8DC]/20 dark:text-[#6BA8DC] dark:border-[#6BA8DC]/40" x-show="!$store.sidebar.collapsed" x-cloak>' . e($badge) . '</span>'
                : '';

            return '
            <li>
                <a href="' . e($url) . '"
                   ' . $ariaCurrent . '
                   title="' . e($label) . '"
                   class="group relative flex items-center gap-3 px-3 py-2 text-[12.5px] font-medium transition-colors duration-150 ' . $activeClass . '"
                   :class="$store.sidebar.collapsed ? \'justify-center px-2\' : \'\'">
                    ' . $indicator . '
                    <span class="w-5 h-5 flex items-center justify-center shrink-0 ' . $iconActive . '">
                        <i class="' . e($icon) . ' text-xs"></i>
                    </span>
                    <span class="truncate leading-none" x-show="!$store.sidebar.collapsed" x-cloak>' . e($label) . '</span>
                    ' . $badgeHtml . '
                </a>
            </li>';
        };

        $section = function ($title) {
            return '
            <li class="pt-4 pb-1 px-3 select-none" x-show="!$store.sidebar.collapsed" x-cloak>
                <span class="font-mono text-[10px] font-bold uppercase tracking-[0.2em] text-[#8A9BAE] dark:text-white/35">' . e($title) . '</span>
            </li>
            <li class="my-2 border-t border-[#E3EAF1] dark:border-[#2A4463] mx-2" x-show="$store.sidebar.collapsed" x-cloak></li>';
        };
    @endphp

    <div id="sidebar-scroll" class="ui-sidebar-scroll min-h-0 bg-white dark:bg-[#0F1D30]">
        <ul class="space-y-0.5">
            {{-- Dashboard --}}
            {!! $item($dashboardUrl, 'Dashboard', 'fa-solid fa-table-columns', request()->routeIs('*.dashboard')) !!}

            {{-- ── SISTEM UTAMA (SUPERADMIN) ──────────────────────────────────── --}}
            @if ($role === 'superadmin')
                {!! $section('Sistem Utama') !!}
                {!! $item(route('superadmin.users.index'), 'Data Pengguna', 'fa-solid fa-users', request()->routeIs('superadmin.users*')) !!}
                {!! $item(route('superadmin.users.index', ['status' => 'inactive']), 'Pengguna Tidak Aktif', 'fa-solid fa-user-slash', request()->routeIs('superadmin.users*') && request('status') == 'inactive') !!}
                {!! $item(route('superadmin.portfolios.index'), 'Portofolio', 'fa-solid fa-briefcase', request()->routeIs('superadmin.portfolios*')) !!}
                {!! $item(route('superadmin.educations.index'), 'Pendidikan', 'fa-solid fa-graduation-cap', request()->routeIs('superadmin.educations*')) !!}
                {!! $item(route('superadmin.experiences.index'), 'Pengalaman', 'fa-solid fa-briefcase', request()->routeIs('superadmin.experiences*')) !!}
                {!! $item(route('superadmin.testimonials.index'), 'Testimoni', 'fa-solid fa-comment-dots', request()->routeIs('superadmin.testimonials*')) !!}
                {!! $item(route('superadmin.certifications.index'), 'Sertifikat', 'fa-solid fa-certificate', request()->routeIs('superadmin.certifications*')) !!}
                {!! $item(route('superadmin.contact_messages.index'), 'Pesan Masuk', 'fa-solid fa-inbox', request()->routeIs('superadmin.contact_messages*')) !!}
                {!! $item(route('superadmin.digital_products.index'), 'Produk Digital', 'fa-solid fa-box-open', request()->routeIs('superadmin.digital_products*') || request()->routeIs('superadmin.digital_purchases*') || request()->routeIs('superadmin.tips*')) !!}
                {!! $item(route('superadmin.skills.index'), 'Skill & Tech', 'fa-solid fa-code', request()->routeIs('superadmin.skills*')) !!}
                {!! $item(route('admin.promo_events.index'), 'Promo Event', 'fa-solid fa-bullhorn', request()->routeIs('admin.promo_events*')) !!}
                {!! $item(route('superadmin.info_updates.index'), 'Info Update', 'fa-solid fa-bullhorn', request()->routeIs('superadmin.info_updates*')) !!}
                {!! $item(route('superadmin.articles.index'), 'Manajemen Blog', 'fa-solid fa-newspaper', request()->routeIs('superadmin.articles*') || request()->routeIs('superadmin.article_categories*')) !!}
                {!! $item(route('superadmin.settings'), 'Pengaturan Sistem', 'fa-solid fa-sliders', request()->routeIs('superadmin.settings*')) !!}
                {!! $item(route('superadmin.backup.index'), 'Sistem Backup', 'fa-solid fa-box-archive', request()->routeIs('superadmin.backup*')) !!}
                {!! $item(route('superadmin.withdrawals.index'), 'Kelola Penarikan', 'fa-solid fa-money-bill-transfer', request()->routeIs('superadmin.withdrawals*')) !!}
                {!! $item(route('superadmin.finance'), 'Laporan Keuangan', 'fa-solid fa-chart-pie', request()->routeIs('superadmin.finance')) !!}
                {!! $item(route('superadmin.confirmations'), 'Konfirmasi Pembayaran QRIS', 'fa-solid fa-qrcode', request()->routeIs('superadmin.confirmations'), $pendingQrisAll ?: null) !!}
            @endif

            {{-- ── MANAJEMEN JOKI ──────────────────────────────────────────────── --}}
            @if ($isAdminJoki)
                {!! $section('Manajemen Joki') !!}
                {!! $item(route('admin_joki.orders'), 'Kelola Pesanan Joki', 'fa-solid fa-code-branch', request()->routeIs('admin_joki.orders*')) !!}
                {!! $item(route('admin_joki.services.index'), 'Manajemen Layanan', 'fa-solid fa-list-check', request()->routeIs('admin_joki.services*')) !!}
                {!! $item(route('admin_joki.finance'), 'Keuangan Joki', 'fa-solid fa-wallet', request()->routeIs('admin_joki.finance')) !!}
                @if ($role === 'admin_joki')
                    {!! $item(route('admin_joki.confirmations'), 'Konfirmasi QRIS Joki', 'fa-solid fa-qrcode', request()->routeIs('admin_joki.confirmations'), $pendingQrisJoki ?: null) !!}
                @endif
            @endif

            {{-- ── MANAJEMEN HOSTING ───────────────────────────────────────────── --}}
            @if ($isAdminHosting)
                {!! $section('Manajemen Hosting') !!}
                {!! $item(route('admin_hosting.projects'), 'Kelola Project', 'fa-solid fa-server', request()->routeIs('admin_hosting.projects')) !!}
                {!! $item(route('admin_hosting.deployments'), 'Riwayat Deploy', 'fa-solid fa-clock-rotate-left', request()->routeIs('admin_hosting.deployments')) !!}
                {!! $item(route('admin_hosting.pending'), 'Butuh Tindakan', 'fa-solid fa-triangle-exclamation', request()->routeIs('admin_hosting.pending')) !!}
                {!! $item(route('admin_hosting.databases'), 'Semua Database', 'fa-solid fa-database', request()->routeIs('admin_hosting.databases')) !!}
                {!! $item(route('admin_hosting.storage'), 'Limit Penyimpanan', 'fa-solid fa-hard-drive', request()->routeIs('admin_hosting.storage')) !!}
                {!! $item(route('admin_hosting.billing'), 'Kelola Tagihan', 'fa-solid fa-file-invoice-dollar', request()->routeIs('admin_hosting.billing')) !!}
                @if ($role === 'admin_hosting')
                    {!! $item(route('admin_hosting.confirmations'), 'Konfirmasi QRIS Hosting', 'fa-solid fa-qrcode', request()->routeIs('admin_hosting.confirmations'), $pendingQrisHosting ?: null) !!}
                @endif
                {!! $item(route('admin_hosting.vouchers.index'), 'Kelola Voucher', 'fa-solid fa-ticket', request()->routeIs('admin_hosting.vouchers*')) !!}
                {!! $item(route('admin_hosting.student_verifications'), 'Verifikasi Mahasiswa', 'fa-solid fa-graduation-cap', request()->routeIs('admin_hosting.student_verifications*')) !!}
                {!! $item(route('admin_hosting.tickets.index'), 'Kelola Tiket', 'fa-solid fa-headset', request()->routeIs('admin_hosting.tickets*')) !!}
            @endif

            {{-- ── LAYANAN KLIEN JOKI ──────────────────────────────────────────── --}}
            @if ($isUserJoki)
                {!! $section('Layanan Joki') !!}
                {!! $item(route('user_joki.create'), 'Buat Pesanan', 'fa-solid fa-cart-plus', request()->routeIs('user_joki.create')) !!}
                {!! $item(route('user_joki.progress'), 'Progres Pengerjaan', 'fa-solid fa-laptop-code', request()->routeIs('user_joki.progress')) !!}
                {!! $item(route('user_joki.riwayat'), 'Riwayat Selesai', 'fa-solid fa-clock-rotate-left', request()->routeIs('user_joki.riwayat')) !!}
                {!! $item(route('user_joki.billing'), 'Riwayat Tagihan', 'fa-solid fa-file-invoice-dollar', request()->routeIs('user_joki.billing')) !!}
            @endif

            {{-- ── LAYANAN KLIEN HOSTING ───────────────────────────────────────── --}}
            @if ($isUserHosting)
                {!! $section('Hosting') !!}
                {!! $item(route('user_hosting.create'), 'Deploy Aplikasi', 'fa-solid fa-rocket', request()->routeIs('user_hosting.create')) !!}
                {!! $item(route('user_hosting.domains.index'), 'Domains', 'fa-solid fa-globe', request()->routeIs('user_hosting.domains.*')) !!}
                {!! $item(route('user_hosting.projects'), 'Proyek Aktif', 'fa-solid fa-terminal', request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) !!}
                {!! $item(route('user_hosting.databases'), 'Database', 'fa-solid fa-database', request()->routeIs('user_hosting.databases') && !request()->routeIs('user_hosting.databases.pma')) !!}
                {!! $item(route('user_hosting.databases.pma'), 'DB Manager', 'fa-solid fa-table-columns', request()->routeIs('user_hosting.databases.pma')) !!}
                {!! $item(route('user_hosting.storage'), 'File & Storage', 'fa-solid fa-folder-open', request()->routeIs('user_hosting.storage*')) !!}
                {!! $item(route('user_hosting.docs'), 'Dokumentasi', 'fa-solid fa-book-open', request()->routeIs('user_hosting.docs*')) !!}
                {!! $item(route('whois.index'), 'Cek Domain', 'fa-solid fa-magnifying-glass', request()->routeIs('whois*')) !!}

                {!! $section('Akun') !!}
                @if ($role === 'user_hosting' && \App\Models\Setting::val('student_promo_active', '0') == '1')
                    {!! $item(route('user_hosting.student_promo'), 'Promo Mahasiswa', 'fa-solid fa-graduation-cap', request()->routeIs('user_hosting.student_promo')) !!}
                @endif
                {!! $item(route('user_hosting.subscription'), 'Langganan Paket', 'fa-solid fa-crown', request()->routeIs('user_hosting.subscription')) !!}
                {!! $item(route('user_hosting.billing'), 'Riwayat Tagihan', 'fa-solid fa-file-invoice-dollar', request()->routeIs('user_hosting.billing')) !!}
                {!! $item(route('user_hosting.tickets.index'), 'Pusat Bantuan', 'fa-solid fa-headset', request()->routeIs('user_hosting.tickets*')) !!}
            @endif
        </ul>
    </div>

    {{-- Pinned Footer: Status, Version & Collapse Toggle --}}
    <div class="shrink-0 border-t border-[#E3EAF1] dark:border-[#2A4463] bg-[#F4F8FC] dark:bg-[#0A1322] px-3 py-2.5 flex items-center transition-all duration-200"
        :class="$store.sidebar.collapsed ? 'justify-center' : 'justify-between'">

        {{-- App Info & Version --}}
        <div class="flex items-center gap-2 min-w-0" x-show="!$store.sidebar.collapsed" x-cloak>
            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0" title="Online"></div>
            <div class="flex items-center gap-1.5 truncate">
                <span class="text-[11px] font-mono font-semibold text-[#0F1A2A] dark:text-white truncate">RYAZE</span>
                <span class="text-[9px] font-mono px-1.5 py-0.5 bg-white dark:bg-[#0F1D30] text-[#525252] dark:text-white/40 border border-[#E3EAF1] dark:border-[#2A4463] shrink-0">{{ str_starts_with($appVersion, 'v') ? $appVersion : 'v' . $appVersion }}</span>
            </div>
        </div>

        {{-- Desktop Collapse Toggle Button --}}
        <button @click="$store.sidebar.toggle()" type="button"
            class="hidden sm:flex w-7 h-7 items-center justify-center text-[#8A9BAE] hover:text-[#1D5B96] hover:bg-[#E8F0F9] dark:text-white/40 dark:hover:bg-[#12233A] dark:hover:text-white transition-colors focus:outline-none"
            :aria-label="$store.sidebar.collapsed ? 'Perluas sidebar' : 'Ciutkan sidebar'"
            :title="$store.sidebar.collapsed ? 'Perluas sidebar' : 'Ciutkan sidebar'">
            <i class="fa-solid fa-angles-left text-xs transition-transform duration-200"
                :class="$store.sidebar.collapsed ? 'rotate-180' : ''"></i>
        </button>
    </div>
</aside>

{{-- Sidebar collapse styling --}}
<style>
    @media (min-width: 640px) {
        #logo-sidebar[class*="sm:w-20"] a {
            justify-content: center;
        }

        #logo-sidebar[class*="sm:w-20"] a i {
            margin: 0;
        }
    }

    /* Active link hover: tetap beraksen steel blue / navy */
    #logo-sidebar a[aria-current="page"]:hover {
        background-color: #12233A;
    }
    .dark #logo-sidebar a[aria-current="page"]:hover {
        background-color: #1D5B96;
    }

    /* Sleek, minimal custom scrollbar for sidebar */
    #sidebar-scroll {
        scrollbar-width: thin;
        scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
    }
    #sidebar-scroll::-webkit-scrollbar {
        width: 5px;
    }
    #sidebar-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    #sidebar-scroll::-webkit-scrollbar-thumb {
        background-color: rgba(148, 163, 184, 0.35);
        border-radius: 9999px;
    }
    #sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background-color: rgba(148, 163, 184, 0.6);
    }
    .dark #sidebar-scroll {
        scrollbar-color: rgba(71, 85, 105, 0.5) transparent;
    }
    .dark #sidebar-scroll::-webkit-scrollbar-thumb {
        background-color: rgba(71, 85, 105, 0.5);
    }
    .dark #sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background-color: rgba(100, 116, 139, 0.7);
    }
</style>

{{--
    Auto-scroll sidebar ke menu yang sedang aktif.

    Memastikan menu yang sedang aktif (aria-current="page") selalu
    berada di area tengah vertikal container sidebar secara proporsional
    (comfort eye-level view), baik saat page load awal, navigasi PJAX,
    maupun resize window / mobile drawer.
--}}
<script nonce="{{ csp_nonce() }}">
    (function () {
        const scrollActiveIntoView = (instant = false) => {
            const scroller = document.getElementById('sidebar-scroll');
            if (!scroller) return;

            const active = scroller.querySelector('[aria-current="page"]');
            if (!active) return;

            const scrollerRect = scroller.getBoundingClientRect();
            const activeRect = active.getBoundingClientRect();

            // Jika sidebar sedang tersembunyi (misal drawer mobile tertutup)
            if (scrollerRect.height <= 0 || scroller.clientHeight <= 0) return;

            // Hitung posisi relatif item terhadap viewport scroller
            const relTop = activeRect.top - scrollerRect.top;
            const activeHeight = active.offsetHeight || activeRect.height;
            const scrollerHeight = scroller.clientHeight;

            // Target scroll untuk menempatkan item tepat di tengah vertikal scroller
            const currentScroll = scroller.scrollTop;
            const itemCenterInContent = currentScroll + relTop + (activeHeight / 2);
            const targetScroll = itemCenterInContent - (scrollerHeight / 2);

            const maxScroll = scroller.scrollHeight - scrollerHeight;
            const finalScroll = Math.max(0, Math.min(Math.round(targetScroll), maxScroll));

            // Jika posisi sudah berada di area tengah (toleransi 16px), tidak perlu animasi ulang
            if (Math.abs(currentScroll - finalScroll) < 16) return;

            scroller.scrollTo({
                top: finalScroll,
                behavior: instant ? 'auto' : 'smooth',
            });
        };

        const triggerAutoScroll = (instant = false) => {
            requestAnimationFrame(() => {
                scrollActiveIntoView(instant);
            });
        };

        // Saat DOM siap (initial page load)
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                triggerAutoScroll(true);
                // Cadangan timeout kecil setelah styling/font stabil
                setTimeout(() => triggerAutoScroll(true), 60);
            });
        } else {
            triggerAutoScroll(true);
            setTimeout(() => triggerAutoScroll(true), 60);
        }

        // Cadangan setelah seluruh aset (font & gambar) selesai dimuat penuh
        window.addEventListener('load', () => triggerAutoScroll(true));

        // Setelah navigasi PJAX selesai memuat halaman baru
        document.addEventListener('pjax:end', () => {
            setTimeout(() => triggerAutoScroll(false), 50);
        });

        // Saat window di-resize (desktop toggle atau orientasi mobile)
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => triggerAutoScroll(false), 120);
        });
    })();
</script>
