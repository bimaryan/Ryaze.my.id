{{-- ╔══════════════════════════════════════════════════════╗ --}}
{{-- ║                 TOP NAVBAR                           ║ --}}
{{-- ╚══════════════════════════════════════════════════════╝ --}}

<nav class="fixed inset-x-0 top-0 z-50" id="main-navbar" aria-label="Navigasi utama">
    <div
        class="relative bg-white/70 dark:bg-[#0a0f1a]/80 backdrop-blur-2xl border-b border-slate-200/50 dark:border-white/[0.08] shadow-sm shadow-slate-900/5 transition-colors duration-300">
        <div class="px-4 lg:px-6">
            <div class="flex items-center justify-between gap-3 h-16">

                {{-- Left: Hamburger + Brand --}}
                <div class="flex min-w-0 items-center gap-3 sm:gap-4">
                    <button x-ref="sidebarToggle" @click="sidebarOpen = !sidebarOpen"
                        :aria-expanded="sidebarOpen.toString()" aria-expanded="false"
                        aria-label="Buka atau tutup menu navigasi" aria-controls="logo-sidebar" type="button"
                        class="sm:hidden inline-flex shrink-0 items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:text-slate-400 dark:hover:text-indigo-400 dark:hover:bg-indigo-500/10 transition-all duration-200 focus-visible:ring-2 focus-visible:ring-indigo-500 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h11" />
                        </svg>
                    </button>

                    <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-3 group">
                        @php $siteLogo = \App\Models\Setting::where('key', 'site_logo')->value('value'); @endphp
                        @if ($siteLogo)
                            <img src="{{ asset('storage/' . $siteLogo) }}" alt="Logo"
                                class="h-8 w-auto object-contain group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div
                                class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:shadow-indigo-500/50 group-hover:scale-105 transition-all duration-300">
                                <i class="fa-solid fa-code text-white text-sm"></i>
                            </div>
                        @endif
                        <span
                            class="text-lg font-extrabold bg-gradient-to-r from-slate-800 to-slate-600 dark:from-white dark:to-slate-300 bg-clip-text text-transparent tracking-tight whitespace-nowrap group-hover:from-indigo-600 group-hover:to-violet-600 dark:group-hover:from-indigo-400 dark:group-hover:to-violet-400 transition-all duration-300">
                            {{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal' }}
                        </span>
                    </a>
                </div>

                {{-- Right: Actions --}}
                <div class="flex items-center gap-3">
                    {{-- Dark mode toggle --}}
                    <button type="button" onclick="ryazeToggleTheme(event)" aria-label="Ganti tema"
                        class="relative inline-flex h-8 w-14 flex-shrink-0 cursor-pointer items-center rounded-full bg-slate-200/80 dark:bg-indigo-500/30 border border-transparent dark:border-indigo-500/50 transition-all duration-300 focus:outline-none hover:bg-slate-300 dark:hover:bg-indigo-500/50 shadow-inner"
                        role="switch">
                        <span
                            class="pointer-events-none inline-flex h-6 w-6 transform translate-x-1 dark:translate-x-7 items-center justify-center rounded-full bg-white dark:bg-[#0a0f1a] shadow-sm transition-all duration-300 ease-in-out">
                            <i
                                class="fa-solid fa-sun text-[10px] text-amber-500 absolute opacity-100 dark:opacity-0 transition-opacity duration-300"></i>
                            <i
                                class="fa-solid fa-moon text-[10px] text-indigo-400 absolute opacity-0 dark:opacity-100 transition-opacity duration-300"></i>
                        </span>
                    </button>

                    {{-- Notification bell --}}
                    @php $unreadNotifications = Auth::check() ? Auth::user()->unreadNotifications : collect([]); @endphp
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open"
                            class="relative flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:text-slate-400 dark:hover:text-indigo-400 dark:hover:bg-indigo-500/10 transition-all duration-200 focus:outline-none"
                            type="button">
                            <i class="fa-solid fa-bell text-lg transition-transform duration-300"
                                :class="open ? 'scale-110' : ''"></i>
                            @if ($unreadNotifications->count() > 0)
                                <span
                                    class="absolute top-2 right-2 flex h-2 w-2 items-center justify-center rounded-full bg-rose-500">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                </span>
                            @endif
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                            class="z-50 absolute right-0 mt-3 w-80 bg-white/95 dark:bg-[#111827]/95 backdrop-blur-xl border border-slate-200/60 dark:border-white/10 rounded-2xl shadow-2xl shadow-slate-900/20 overflow-hidden"
                            style="display: none;">
                            <div
                                class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/5">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-100">Notifikasi</span>
                                    @if ($unreadNotifications->count() > 0)
                                        <span
                                            class="px-2 py-0.5 text-[10px] font-bold bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 rounded-full">
                                            {{ $unreadNotifications->count() }} Baru
                                        </span>
                                    @endif
                                </div>
                                @if ($unreadNotifications->count() > 0)
                                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 font-medium transition-colors">
                                            Tandai Terbaca
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div
                                class="max-h-72 overflow-y-auto divide-y divide-slate-100 dark:divide-white/5 scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-700">
                                @forelse($unreadNotifications as $notification)
                                    <a href="#"
                                        onclick="event.preventDefault(); document.getElementById('mark-read-{{ $notification->id }}').submit();"
                                        class="flex gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                                        <div
                                            class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-500/20 flex items-center justify-center mt-0.5 group-hover:scale-110 transition-transform">
                                            <i
                                                class="fa-solid fa-bell text-indigo-500 dark:text-indigo-400 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p
                                                class="text-sm font-medium text-slate-700 dark:text-slate-300 leading-snug line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {{ $notification->data['message'] ?? 'Notifikasi baru' }}
                                            </p>
                                            <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                                                <i class="fa-regular fa-clock"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </a>
                                    <form id="mark-read-{{ $notification->id }}"
                                        action="{{ route('notifications.markRead', $notification->id) }}" method="POST"
                                        class="hidden">@csrf</form>
                                @empty
                                    <div class="py-10 text-center">
                                        <div
                                            class="w-12 h-12 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <i
                                                class="fa-solid fa-check-double text-slate-300 dark:text-slate-600 text-xl"></i>
                                        </div>
                                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Semua sudah
                                            terbaca</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="hidden md:block w-px h-8 bg-slate-200 dark:bg-white/10 mx-1"></div>

                    {{-- User info --}}
                    <div class="hidden md:flex items-center gap-3 text-right group cursor-pointer"
                        onclick="document.getElementById('user-menu-btn').click()">
                        <div>
                            <p
                                class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ Auth::user()->name ?? 'Guest' }}</p>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 leading-tight mt-0.5">
                                {{ Auth::check() ? ucwords(str_replace('_', ' ', Auth::user()->role)) : '' }}
                            </p>
                        </div>
                    </div>

                    {{-- Avatar dropdown --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button id="user-menu-btn" @click="open = !open"
                            class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-sm shadow-md shadow-indigo-500/20 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all duration-300 focus:outline-none ring-2 ring-transparent focus:ring-indigo-500/50">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                            class="z-50 absolute right-0 mt-3 w-56 bg-white/95 dark:bg-[#111827]/95 backdrop-blur-xl border border-slate-200/60 dark:border-white/10 rounded-2xl shadow-2xl shadow-slate-900/20 overflow-hidden"
                            style="display: none;">
                            <div
                                class="px-4 py-4 bg-slate-50/80 dark:bg-white/[0.02] border-b border-slate-100 dark:border-white/5">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                                    {{ Auth::user()->name ?? 'Guest' }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                    {{ Auth::user()->email ?? '' }}</p>
                            </div>
                            <ul class="p-2 space-y-1">
                                <li>
                                    <a href="{{ route('profile.edit') }}"
                                        class="group flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-xl transition-all duration-200">
                                        <div
                                            class="w-6 flex justify-center text-slate-400 group-hover:text-indigo-500 transition-colors">
                                            <i class="fa-solid fa-user-astronaut"></i>
                                        </div>
                                        Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full group flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:text-rose-600 dark:hover:text-rose-400 rounded-xl transition-all duration-200">
                                            <div
                                                class="w-6 flex justify-center text-slate-400 group-hover:text-rose-500 transition-colors">
                                                <i class="fa-solid fa-right-from-bracket"></i>
                                            </div>
                                            Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- ╔══════════════════════════════════════════════════════╗ --}}
{{-- ║                 SIDEBAR                              ║ --}}
{{-- ╚══════════════════════════════════════════════════════╝ --}}

<aside id="logo-sidebar"
    :class="sidebarOpen && !desktop ? 'translate-x-0 shadow-2xl' : (desktop ? 'translate-x-0' : '-translate-x-full')"
    class="fixed top-0 left-0 z-40 h-[100dvh] pt-16 transition-transform duration-300 ease-in-out bg-white dark:bg-[#0a0f1a] border-r border-slate-200/80 dark:border-white/[0.08] w-64 sm:translate-x-0"
    aria-label="Sidebar">

    <div
        class="absolute top-16 left-0 right-0 h-24 bg-gradient-to-b from-slate-50 to-transparent dark:from-white/[0.02] dark:to-transparent pointer-events-none">
    </div>

    <div class="flex h-full flex-col relative z-10">
        <div
            class="flex-1 overflow-y-auto px-3 py-4 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-slate-200 hover:scrollbar-thumb-slate-300 dark:scrollbar-thumb-white/10 dark:hover:scrollbar-thumb-white/20">
            @php
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

                // Stylings
                $navLink = fn(
                    $active,
                ) => 'group flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-all duration-300 ease-out ' .
                    ($active
                        ? 'bg-gradient-to-r from-indigo-500 to-violet-600 text-white shadow-md shadow-indigo-500/25'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 hover:translate-x-1 dark:text-slate-400 dark:hover:bg-white/[0.03] dark:hover:text-indigo-400');

                $iconBox = fn(
                    $active,
                ) => 'flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-sm transition-transform duration-300 group-hover:scale-110 ' .
                    ($active
                        ? 'text-white'
                        : 'text-slate-400 group-hover:text-indigo-500 dark:text-slate-500 dark:group-hover:text-indigo-400');

                // Header seksi yang lebih elegan
                $sectionLabel = 'flex items-center gap-3 px-2 pt-6 pb-2';
            @endphp

            <nav class="space-y-1">

                {{-- Dashboard --}}
                <a href="{{ $dashboardUrl }}" class="{{ $navLink(request()->routeIs('*.dashboard')) }}">
                    <span class="{{ $iconBox(request()->routeIs('*.dashboard')) }}">
                        <i class="fa-solid fa-border-all"></i>
                    </span>
                    <span>Dashboard</span>
                    @if (request()->routeIs('*.dashboard'))
                        <span class="ms-auto w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                    @endif
                </a>

                {{-- ── SISTEM UTAMA (SUPERADMIN) ──────────────────────────────────── --}}
                @if ($role === 'superadmin')
                    <div class="{{ $sectionLabel }}">
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Sistem
                            Utama</span>
                        <div
                            class="h-px flex-1 bg-gradient-to-r from-slate-200 to-transparent dark:from-white/10 dark:to-transparent">
                        </div>
                    </div>

                    <a href="{{ route('superadmin.users.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.users*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('superadmin.users*')) }}"><i
                                class="fa-solid fa-users"></i></span>
                        <span>Data Pengguna</span>
                    </a>
                    <a href="{{ route('superadmin.portfolios.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.portfolios*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('superadmin.portfolios*')) }}"><i
                                class="fa-solid fa-briefcase"></i></span>
                        <span>Portofolio</span>
                    </a>
                    <a href="{{ route('admin.promo_events.index') }}"
                        class="{{ $navLink(request()->routeIs('admin.promo_events*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('admin.promo_events*')) }}"><i
                                class="fa-solid fa-bullhorn"></i></span>
                        <span>Promo Event</span>
                    </a>
                    <a href="{{ route('superadmin.articles.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.articles*') || request()->routeIs('superadmin.article_categories*')) }}">
                        <span
                            class="{{ $iconBox(request()->routeIs('superadmin.articles*') || request()->routeIs('superadmin.article_categories*')) }}"><i
                                class="fa-solid fa-newspaper"></i></span>
                        <span>Manajemen Blog</span>
                    </a>
                    <a href="{{ route('superadmin.settings') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.settings*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('superadmin.settings*')) }}"><i
                                class="fa-solid fa-sliders"></i></span>
                        <span>Pengaturan Sistem</span>
                    </a>
                    <a href="{{ route('superadmin.backup.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.backup*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('superadmin.backup*')) }}"><i
                                class="fa-solid fa-box-archive"></i></span>
                        <span>Sistem Backup</span>
                    </a>
                    <a href="{{ route('superadmin.withdrawals.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.withdrawals*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('superadmin.withdrawals*')) }}"><i
                                class="fa-solid fa-money-bill-transfer"></i></span>
                        <span>Kelola Penarikan</span>
                    </a>
                    <a href="{{ route('superadmin.finance') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.finance')) }}">
                        <span class="{{ $iconBox(request()->routeIs('superadmin.finance')) }}"><i
                                class="fa-solid fa-chart-pie"></i></span>
                        <span>Laporan Keuangan</span>
                    </a>
                @endif

                {{-- ── MANAJEMEN JOKI ──────────────────────────────────────────────── --}}
                @if ($isAdminJoki)
                    <div class="{{ $sectionLabel }}">
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Manajemen
                            Joki</span>
                        <div
                            class="h-px flex-1 bg-gradient-to-r from-slate-200 to-transparent dark:from-white/10 dark:to-transparent">
                        </div>
                    </div>

                    <a href="{{ route('admin_joki.orders') }}"
                        class="{{ $navLink(request()->routeIs('admin_joki.orders*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('admin_joki.orders*')) }}"><i
                                class="fa-solid fa-code-branch"></i></span>
                        <span>Kelola Pesanan Joki</span>
                    </a>
                    <a href="{{ route('admin_joki.services.index') }}"
                        class="{{ $navLink(request()->routeIs('admin_joki.services*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('admin_joki.services*')) }}"><i
                                class="fa-solid fa-list-check"></i></span>
                        <span>Manajemen Layanan</span>
                    </a>
                    <a href="{{ route('admin_joki.finance') }}"
                        class="{{ $navLink(request()->routeIs('admin_joki.finance')) }}">
                        <span class="{{ $iconBox(request()->routeIs('admin_joki.finance')) }}"><i
                                class="fa-solid fa-wallet"></i></span>
                        <span>Keuangan Joki</span>
                    </a>
                @endif

                {{-- ── MANAJEMEN HOSTING ───────────────────────────────────────────── --}}
                @if ($isAdminHosting)
                    <div class="{{ $sectionLabel }}">
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Manajemen
                            Hosting</span>
                        <div
                            class="h-px flex-1 bg-gradient-to-r from-slate-200 to-transparent dark:from-white/10 dark:to-transparent">
                        </div>
                    </div>

                    <a href="{{ route('admin_hosting.projects') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.projects')) }}">
                        <span class="{{ $iconBox(request()->routeIs('admin_hosting.projects')) }}"><i
                                class="fa-solid fa-server"></i></span>
                        <span>Kelola Project</span>
                    </a>
                    <a href="{{ route('admin_hosting.deployments') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.deployments')) }}">
                        <span class="{{ $iconBox(request()->routeIs('admin_hosting.deployments')) }}"><i
                                class="fa-solid fa-clock-rotate-left"></i></span>
                        <span>Riwayat Deploy</span>
                    </a>
                    <a href="{{ route('admin_hosting.pending') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.pending')) }}">
                        <span class="{{ $iconBox(request()->routeIs('admin_hosting.pending')) }}"><i
                                class="fa-solid fa-triangle-exclamation"></i></span>
                        <span>Butuh Tindakan</span>
                    </a>
                    <a href="{{ route('admin_hosting.databases') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.databases')) }}">
                        <span class="{{ $iconBox(request()->routeIs('admin_hosting.databases')) }}"><i
                                class="fa-solid fa-database"></i></span>
                        <span>Semua Database</span>
                    </a>
                    <a href="{{ route('admin_hosting.storage') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.storage')) }}">
                        <span class="{{ $iconBox(request()->routeIs('admin_hosting.storage')) }}"><i
                                class="fa-solid fa-hard-drive"></i></span>
                        <span>Limit Penyimpanan</span>
                    </a>
                    <a href="{{ route('admin_hosting.billing') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.billing')) }}">
                        <span class="{{ $iconBox(request()->routeIs('admin_hosting.billing')) }}"><i
                                class="fa-solid fa-file-invoice-dollar"></i></span>
                        <span>Kelola Tagihan</span>
                    </a>
                    <a href="{{ route('admin_hosting.vouchers.index') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.vouchers*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('admin_hosting.vouchers*')) }}"><i
                                class="fa-solid fa-ticket"></i></span>
                        <span>Kelola Voucher</span>
                    </a>
                    <a href="{{ route('admin_hosting.tickets.index') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.tickets*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('admin_hosting.tickets*')) }}"><i
                                class="fa-solid fa-headset"></i></span>
                        <span>Kelola Tiket</span>
                    </a>
                @endif

                {{-- ── LAYANAN KLIEN JOKI ──────────────────────────────────────────── --}}
                @if ($isUserJoki)
                    <div class="{{ $sectionLabel }}">
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Layanan
                            Joki</span>
                        <div
                            class="h-px flex-1 bg-gradient-to-r from-slate-200 to-transparent dark:from-white/10 dark:to-transparent">
                        </div>
                    </div>

                    <a href="{{ route('user_joki.create') }}"
                        class="{{ $navLink(request()->routeIs('user_joki.create')) }}">
                        <span class="{{ $iconBox(request()->routeIs('user_joki.create')) }}"><i
                                class="fa-solid fa-cart-plus"></i></span>
                        <span>Buat Pesanan</span>
                    </a>
                    <a href="{{ route('user_joki.progress') }}"
                        class="{{ $navLink(request()->routeIs('user_joki.progress')) }}">
                        <span class="{{ $iconBox(request()->routeIs('user_joki.progress')) }}"><i
                                class="fa-solid fa-laptop-code"></i></span>
                        <span>Progres Pengerjaan</span>
                    </a>
                    <a href="{{ route('user_joki.riwayat') }}"
                        class="{{ $navLink(request()->routeIs('user_joki.riwayat')) }}">
                        <span class="{{ $iconBox(request()->routeIs('user_joki.riwayat')) }}"><i
                                class="fa-solid fa-clock-rotate-left"></i></span>
                        <span>Riwayat Selesai</span>
                    </a>
                    <a href="{{ route('user_joki.billing') }}"
                        class="{{ $navLink(request()->routeIs('user_joki.billing')) }}">
                        <span class="{{ $iconBox(request()->routeIs('user_joki.billing')) }}"><i
                                class="fa-solid fa-file-invoice-dollar"></i></span>
                        <span>Riwayat Tagihan</span>
                    </a>
                @endif

                {{-- ── LAYANAN KLIEN HOSTING ───────────────────────────────────────── --}}
                @if ($isUserHosting)
                    <div class="{{ $sectionLabel }}">
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Hosting</span>
                        <div
                            class="h-px flex-1 bg-gradient-to-r from-slate-200 to-transparent dark:from-white/10 dark:to-transparent">
                        </div>
                    </div>

                    <a href="{{ route('user_hosting.create') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.marketplace')) }} ">
                        <span
                            class="flex-shrink-0 w-7 h-7 flex items-center justify-center text-white text-sm group-hover:animate-bounce">
                            <i class="fa-solid fa-rocket"></i>
                        </span>
                        <span>Deploy Aplikasi</span>
                        <i class="fa-solid fa-plus ms-auto text-white/70 text-xs"></i>
                    </a>

                    <a href="{{ route('user_hosting.marketplace') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.marketplace')) }} mt-1">
                        <span class="{{ $iconBox(request()->routeIs('user_hosting.marketplace')) }}"><i
                                class="fa-solid fa-store"></i></span>
                        <span>App Marketplace</span>
                    </a>
                    <a href="{{ route('user_hosting.apk.index') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.apk*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('user_hosting.apk*')) }}"><i
                                class="fa-brands fa-android"></i></span>
                        <span>Web to APK</span>
                    </a>
                    <a href="{{ route('user_hosting.tunnels.index') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.tunnels*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('user_hosting.tunnels*')) }}"><i
                                class="fa-solid fa-network-wired"></i></span>
                        <span>Local Tunnels</span>
                        <span
                            class="ms-auto px-2 py-0.5 text-[9px] font-bold bg-violet-100 dark:bg-violet-500/20 text-violet-600 dark:text-violet-400 rounded-md">Beta</span>
                    </a>
                    <a href="{{ route('user_hosting.templates') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.templates')) }}">
                        <span class="{{ $iconBox(request()->routeIs('user_hosting.templates')) }}"><i
                                class="fa-solid fa-layer-group"></i></span>
                        <span>Galeri Template</span>
                    </a>
                    <a href="{{ route('user_hosting.projects') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }}">
                        <span
                            class="{{ $iconBox(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }}"><i
                                class="fa-solid fa-terminal"></i></span>
                        <span>Proyek Aktif</span>
                    </a>
                    <a href="{{ route('user_hosting.databases') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.databases') && !request()->routeIs('user_hosting.databases.pma')) }}">
                        <span
                            class="{{ $iconBox(request()->routeIs('user_hosting.databases') && !request()->routeIs('user_hosting.databases.pma')) }}"><i
                                class="fa-solid fa-database"></i></span>
                        <span>Database</span>
                    </a>
                    <a href="{{ route('user_hosting.databases.pma') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.databases.pma')) }}">
                        <span class="{{ $iconBox(request()->routeIs('user_hosting.databases.pma')) }}"><i
                                class="fa-solid fa-table-columns"></i></span>
                        <span>DB Manager</span>
                    </a>
                    <a href="{{ route('user_hosting.storage') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.storage*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('user_hosting.storage*')) }}"><i
                                class="fa-solid fa-folder-open"></i></span>
                        <span>File & Storage</span>
                    </a>
                    <a href="{{ route('user_hosting.docs') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.docs*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('user_hosting.docs*')) }}"><i
                                class="fa-solid fa-book-open"></i></span>
                        <span>Dokumentasi</span>
                    </a>

                    <div class="{{ $sectionLabel }} mt-4">
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Akun</span>
                        <div
                            class="h-px flex-1 bg-gradient-to-r from-slate-200 to-transparent dark:from-white/10 dark:to-transparent">
                        </div>
                    </div>

                    <a href="{{ route('user_hosting.subscription') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.subscription')) }}">
                        <span class="{{ $iconBox(request()->routeIs('user_hosting.subscription')) }}"><i
                                class="fa-solid fa-crown"></i></span>
                        <span>Langganan Paket</span>
                        @if (!Auth::user()->hasActiveHostingSubscription())
                            <span
                                class="ms-auto px-2 py-0.5 text-[9px] font-bold bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-md animate-pulse">Beli</span>
                        @else
                            <span
                                class="ms-auto w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
                        @endif
                    </a>

                    {{-- Menyelesaikan tag yang terpotong dari prompt kamu --}}
                    <a href="{{ route('user_hosting.tickets.index') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.tickets*')) }}">
                        <span class="{{ $iconBox(request()->routeIs('user_hosting.tickets*')) }}"><i
                                class="fa-solid fa-headset"></i></span>
                        <span>Pusat Bantuan</span>
                    </a>
                @endif

            </nav>
        </div>
    </div>
</aside>
