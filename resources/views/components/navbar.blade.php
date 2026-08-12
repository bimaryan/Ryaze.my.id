<nav class="fixed top-0 z-50 w-full shadow bg-gray-50 dark:bg-slate-900">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start gap-2 rtl:justify-end">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                        </path>
                    </svg>
                </button>

                <a href="{{ url('/') }}" class="flex ms-2 md:me-24 items-center gap-2">
                    @php
                        $siteLogo = \App\Models\Setting::where('key', 'site_logo')->value('value');
                    @endphp
                    @if($siteLogo)
                        <img src="{{ asset('storage/' . $siteLogo) }}" alt="Logo" class="h-8 object-contain">
                    @endif
                    <span class="self-center text-xl text-indigo-600 font-bold sm:text-2xl whitespace-nowrap">{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal' }}</span>
                </a>
            </div>

            <div class="flex items-center">
                <div class="flex items-center ms-3 gap-5">
                    {{-- Toggle Tema --}}
                    <button type="button" onclick="ryazeToggleTheme()" aria-label="Ganti tema"
                        class="relative inline-flex h-9 w-16 flex-shrink-0 cursor-pointer rounded-full border-2 border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-800 transition-all duration-300 ease-out focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        role="switch" aria-checked="false">
                        <span class="pointer-events-none inline-flex h-7 w-7 items-center justify-center rounded-full bg-white shadow-lg text-amber-500 dark:bg-slate-700 dark:text-indigo-300 transition-transform duration-300 ease-out transform dark:translate-x-7 relative"
                            aria-hidden="true">
                            <i class="fa-solid fa-sun text-[14px] absolute transition-opacity duration-300 opacity-100 dark:opacity-0"></i>
                            <i class="fa-solid fa-moon text-[14px] absolute transition-opacity duration-300 opacity-0 dark:opacity-100"></i>
                        </span>
                        <span class="pointer-events-none absolute inset-0 flex items-center justify-between px-1.5 text-[10px] font-medium text-slate-400 dark:text-slate-500" aria-hidden="true">
                            <i class="fa-solid fa-sun opacity-100 dark:opacity-0 transition-opacity"></i>
                            <i class="fa-solid fa-moon opacity-0 dark:opacity-100 transition-opacity"></i>
                        </span>
                    </button>

                    {{-- Notifikasi --}}
                    @php
                        $unreadNotifications = Auth::check() ? Auth::user()->unreadNotifications : collect([]);
                    @endphp
                    <button id="dropdownNotificationButton" data-dropdown-toggle="dropdownNotification"
                        class="relative inline-flex items-center text-sm font-medium text-center text-indigo-200 hover:text-slate-800 focus:outline-none transition-colors"
                        type="button">
                        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 14 20">
                            <path
                                d="M12.133 10.632v-1.8A5.406 5.406 0 0 0 7.979 3.57.946.946 0 0 0 8 3.464V1.1a1 1 0 0 0-2 0v2.364a.946.946 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C1.867 13.018 0 13.614 0 14.807 0 15.4 0 16 .538 16h12.924C14 16 14 15.4 14 14.807c0-1.193-1.867-1.789-1.867-4.175ZM3.823 17a3.453 3.453 0 0 0 6.354 0H3.823Z" />
                        </svg>
                        @if ($unreadNotifications->count() > 0)
                            <div
                                class="absolute block w-5 h-5 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full -top-1 start-3">
                                <p class="text-slate-800 text-[10px] leading-tight font-bold">
                                    {{ $unreadNotifications->count() > 9 ? '9+' : $unreadNotifications->count() }}</p>
                            </div>
                        @endif
                    </button>

                    <div id="dropdownNotification"
                        class="z-20 hidden w-80 max-w-sm bg-white divide-y divide-slate-100 rounded-lg shadow-xl dark:bg-slate-800 dark:divide-slate-700"
                        aria-labelledby="dropdownNotificationButton">
                        <div
                            class="flex items-center justify-between px-4 py-3 font-semibold text-slate-700 rounded-t-lg bg-transparent border-b border-slate-100 dark:text-slate-200 dark:border-slate-700">
                            <span>Notifikasi Terbaru</span>
                            @if ($unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">Tandai
                                        Dibaca</button>
                                </form>
                            @endif
                        </div>
                        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto dark:divide-slate-700">
                            @forelse($unreadNotifications as $notification)
                                <a href="#"
                                    onclick="event.preventDefault(); document.getElementById('mark-read-{{ $notification->id }}').submit();"
                                    class="flex px-4 py-3 hover:bg-transparent">
                                    <div class="w-full pl-3">
                                        <div class="text-slate-600 text-sm mb-1.5 dark:text-slate-300">
                                            {{ $notification->data['message'] ?? 'Notifikasi baru' }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </a>
                                <form id="mark-read-{{ $notification->id }}"
                                    action="{{ route('notifications.markRead', $notification->id) }}" method="POST"
                                    class="hidden">
                                    @csrf
                                </form>
                            @empty
                                <p class="px-6 py-4 text-sm text-slate-500 text-center dark:text-slate-400">Belum ada notifikasi baru.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Info User --}}
                    <div class="hidden md:block text-right border-l border-slate-300 pl-5 dark:border-slate-700">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ Auth::user()->name ?? 'Guest' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ Auth::check() ? ucwords(str_replace('_', ' ', Auth::user()->role)) : 'No Role' }}
                        </p>
                    </div>

                    {{-- Avatar + Dropdown --}}
                    <div>
                        <button type="button"
                            class="flex text-sm bg-slate-100 rounded-full focus:ring-4 focus:ring-slate-200 border border-slate-200 shadow-sm transition-transform hover:scale-105 dark:bg-slate-800 dark:border-slate-700 dark:focus:ring-slate-700"
                            aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            <div
                                class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-lg dark:bg-indigo-500/20 dark:text-indigo-300">
                                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                            </div>
                        </button>
                    </div>

                    <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-slate-100 rounded-xl shadow-xl border border-slate-100 dark:bg-slate-800 dark:divide-slate-700 dark:border-slate-700"
                        id="dropdown-user">
                        <div class="px-4 py-3 md:hidden">
                            <p class="text-sm text-slate-900 font-bold dark:text-slate-100">{{ Auth::user()->name ?? 'Guest' }}</p>
                            <p class="text-xs font-medium text-slate-500 truncate dark:text-slate-400">
                                {{ Auth::check() ? ucwords(str_replace('_', ' ', Auth::user()->role)) : '' }}
                            </p>
                        </div>
                        <ul class="py-1" role="none">
                            <li>
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-slate-700 hover:bg-transparent dark:text-slate-200">
                                    <i class="fa-solid fa-user me-2 text-indigo-500"></i> Profil Saya
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-transparent border-t border-slate-100 dark:border-slate-700"
                                        role="menuitem">
                                        <i class="fa-solid fa-right-from-bracket me-2"></i> Keluar
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

<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-[100dvh] pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 dark:bg-slate-900 dark:border-slate-800 sm:translate-x-0"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-24 mt-3 overflow-y-auto bg-white dark:bg-slate-900">
        <ul class="space-y-2 font-medium">

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

                $navLink = fn(
                    $active,
                ) => 'flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group text-sm font-medium ' .
                    ($active
                        ? 'bg-indigo-600 text-slate-800 shadow-md text-white shadow-indigo-200/50'
                        : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700 dark:text-slate-300 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-300');

                $iconClass = fn($active) => 'w-6 text-center text-lg transition-transform group-hover:scale-110 ' .
                    ($active ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-500 dark:group-hover:text-indigo-400');
            @endphp

            {{-- Dashboard --}}
            <li>
                <a href="{{ $dashboardUrl }}" class="{{ $navLink(request()->routeIs('*.dashboard')) }}">
                    <i class="fa-solid fa-border-all {{ $iconClass(request()->routeIs('*.dashboard')) }}"></i>
                    <span class="ms-3 whitespace-nowrap">Dashboard</span>
                </a>
            </li>

            {{-- â•â• SISTEM UTAMA (SUPERADMIN) â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
            @if ($role === 'superadmin')
                <li class="pt-4 pb-1 mt-4 border-t border-slate-200/60 dark:border-slate-800">
                    <span class="px-3 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Sistem Utama</span>
                </li>

                <li>
                    <a href="{{ route('superadmin.users.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.users*')) }}">
                        <i class="fa-solid fa-users {{ $iconClass(request()->routeIs('superadmin.users*')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Data Pengguna</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.portfolios.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.portfolios*')) }}">
                        <i class="fa-solid fa-briefcase {{ $iconClass(request()->routeIs('superadmin.portfolios*')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Manajemen Portofolio</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.articles.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.articles*') || request()->routeIs('superadmin.article_categories*')) }}">
                        <i class="fa-solid fa-newspaper {{ $iconClass(request()->routeIs('superadmin.articles*') || request()->routeIs('superadmin.article_categories*')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Manajemen Blog</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.settings') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.settings*')) }}">
                        <i class="fa-solid fa-cogs {{ $iconClass(request()->routeIs('superadmin.settings*')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Pengaturan Sistem</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.backup.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.backup*')) }}">
                        <i class="fa-solid fa-box-archive {{ $iconClass(request()->routeIs('superadmin.backup*')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Sistem Backup</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.withdrawals.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.withdrawals*')) }}">
                        <i class="fa-solid fa-money-bill-transfer {{ $iconClass(request()->routeIs('superadmin.withdrawals*')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Kelola Penarikan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.finance') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.finance')) }}">
                        <i class="fa-solid fa-chart-pie {{ $iconClass(request()->routeIs('superadmin.finance')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Laporan Keuangan</span>
                    </a>
                </li>

            @endif

            {{-- â•â• MANAJEMEN JOKI â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
            @if ($isAdminJoki)
                <li class="pt-4 pb-1 mt-4 border-t border-slate-200/60 dark:border-slate-800">
                    <span class="px-3 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        {{ $role === 'superadmin' ? 'Manajemen Joki' : 'Manajemen Hosting' }}
                    </span>
                </li>

                <li>
                    <a href="{{ route('admin_joki.orders') }}"
                        class="{{ $navLink(request()->routeIs('admin_joki.orders*')) }}">
                        <i
                            class="fa-solid fa-code-branch {{ $iconClass(request()->routeIs('admin_joki.orders*')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Kelola Pesanan Joki</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_joki.services.index') }}"
                        class="{{ $navLink(request()->routeIs('admin_joki.services*')) }}">
                        <i class="fa-solid fa-list {{ $iconClass(request()->routeIs('admin_joki.services*')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Manajemen Layanan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_joki.finance') }}"
                        class="{{ $navLink(request()->routeIs('admin_joki.finance')) }}">
                        <i class="fa-solid fa-wallet {{ $iconClass(request()->routeIs('admin_joki.finance')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Keuangan Joki</span>
                    </a>
                </li>
            @endif

            {{-- â•â• MANAJEMEN HOSTING â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
            @if ($isAdminHosting)
                <li class="pt-4 pb-1 mt-4 border-t border-slate-200/60 dark:border-slate-800">
                    <span class="px-3 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        {{ $role === 'superadmin' ? 'Manajemen Hosting' : 'Manajemen Admin' }}
                    </span>
                </li>

                <li>
                    <a href="{{ route('admin_hosting.projects') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.projects')) }}">
                        <i
                            class="fa-solid fa-server {{ $iconClass(request()->routeIs('admin_hosting.projects')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Kelola Project Hosting</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.deployments') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.deployments')) }}">
                        <i
                            class="fa-solid fa-history {{ $iconClass(request()->routeIs('admin_hosting.deployments')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Riwayat Project</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.pending') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.pending')) }}">
                        <i
                            class="fa-solid fa-warning {{ $iconClass(request()->routeIs('admin_hosting.pending')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Membutuhkan Tindakan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.databases') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.databases')) }}">
                        <i
                            class="fa-solid fa-database {{ $iconClass(request()->routeIs('admin_hosting.databases')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Semua Database</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.storage') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.storage')) }}">
                        <i
                            class="fa-solid fa-hard-drive {{ $iconClass(request()->routeIs('admin_hosting.storage')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Limit Penyimpanan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.billing') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.billing')) }}">
                        <i
                            class="fa-solid fa-file-invoice-dollar {{ $iconClass(request()->routeIs('admin_hosting.billing')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Kelola Tagihan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.vouchers.index') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.vouchers*')) }}">
                        <i
                            class="fa-solid fa-ticket {{ $iconClass(request()->routeIs('admin_hosting.vouchers*')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Kelola Voucher</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.tickets.index') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.tickets*')) }}">
                        <i
                            class="fa-solid fa-headset {{ $iconClass(request()->routeIs('admin_hosting.tickets*')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Kelola Tiket</span>
                    </a>
                </li>
            @endif


            {{-- â•â• LAYANAN KLIEN JOKI â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
            @if ($isUserJoki)
                <li class="pt-4 pb-1 mt-4 border-t border-slate-200/60 dark:border-slate-800">
                    <span class="px-3 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Layanan
                        Klien Joki</span>
                </li>

                {{-- Menu User Joki --}}
                <li>
                    <a href="{{ route('user_joki.create') }}"
                        class="{{ $navLink(request()->routeIs('user_joki.create')) }}">
                        <i
                            class="fa-solid fa-cart-plus {{ $iconClass(request()->routeIs('user_joki.create')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Buat Pesanan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_joki.progress') }}"
                        class="{{ $navLink(request()->routeIs('user_joki.progress')) }}">
                        <i
                            class="fa-solid fa-laptop-code {{ $iconClass(request()->routeIs('user_joki.progress')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Progres Pengerjaan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_joki.riwayat') }}"
                        class="{{ $navLink(request()->routeIs('user_joki.riwayat')) }}">
                        <i
                            class="fa-solid fa-history {{ $iconClass(request()->routeIs('user_joki.riwayat')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Riwayat Selesai</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('user_joki.billing') }}"
                        class="{{ $navLink(request()->routeIs('user_joki.billing')) }}">
                        <i
                            class="fa-solid fa-file-invoice-dollar {{ $iconClass(request()->routeIs('user_joki.billing')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Riwayat Tagihan</span>
                    </a>
                </li>
            @endif

            {{-- â•â• LAYANAN KLIEN HOSTING â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
            @if ($isUserHosting)
                <li class="pt-4 pb-1 mt-4 border-t border-slate-200/60 dark:border-slate-800">
                    <span class="px-3 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Layanan
                        Klien Hosting</span>
                </li>

                {{-- Menu User Hosting --}}
                    <li>
                        <a href="{{ route('user_hosting.create') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.create')) }}">
                            <i
                                class="fa-solid fa-rocket {{ $iconClass(request()->routeIs('user_hosting.create')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Deploy Aplikasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.marketplace') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.marketplace')) }}">
                            <i
                                class="fa-solid fa-store {{ $iconClass(request()->routeIs('user_hosting.marketplace')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">App Marketplace</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.apk.index') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.apk*')) }}">
                            <i
                                class="fa-brands fa-android {{ $iconClass(request()->routeIs('user_hosting.apk*')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Web to APK</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.tunnels.index') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.tunnels*')) }}">
                            <i
                                class="fa-solid fa-network-wired {{ $iconClass(request()->routeIs('user_hosting.tunnels*')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Local Tunnels</span>
                            <span class="ms-2 px-2 py-0.5 text-[10px] font-bold rounded-full bg-purple-100 text-purple-600 dark:bg-purple-500/20 dark:text-purple-300">Beta</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.templates') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.templates')) }}">
                            <i
                                class="fa-solid fa-layer-group {{ $iconClass(request()->routeIs('user_hosting.templates')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Galeri Template</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.projects') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }}">
                            <i
                                class="fa-solid fa-terminal {{ $iconClass(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Proyek Aktif</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.databases') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.databases') && !request()->routeIs('user_hosting.databases.pma')) }}">
                            <i
                                class="fa-solid fa-database {{ $iconClass(request()->routeIs('user_hosting.databases') && !request()->routeIs('user_hosting.databases.pma')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Database</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.databases.pma') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.databases.pma')) }}">
                            <i
                                class="fa-solid fa-server {{ $iconClass(request()->routeIs('user_hosting.databases.pma')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">DB Manager</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.storage') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.storage*')) }}">
                            <i
                                class="fa-solid fa-hard-drive {{ $iconClass(request()->routeIs('user_hosting.storage*')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">File & Storage</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.docs') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.docs*')) }}">
                            <i
                                class="fa-solid fa-book {{ $iconClass(request()->routeIs('user_hosting.docs*')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Dokumentasi</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user_hosting.subscription') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.subscription')) }}">
                            <i
                                class="fa-solid fa-crown {{ $iconClass(request()->routeIs('user_hosting.subscription')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Langganan Paket</span>
                            @if(!Auth::user()->hasActiveHostingSubscription())
                                <span class="ms-auto inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-bold text-rose-500 bg-rose-100 dark:text-rose-300 dark:bg-rose-500/20 rounded-full">
                                    Beli
                                </span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.billing') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.billing')) }}">
                            <i
                                class="fa-solid fa-file-invoice-dollar {{ $iconClass(request()->routeIs('user_hosting.billing')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Tagihan / Billing</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.tickets.index') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.tickets*')) }}">
                            <i
                                class="fa-solid fa-life-ring {{ $iconClass(request()->routeIs('user_hosting.tickets*')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Tiket Bantuan</span>
                        </a>
                    </li>
                @endif

                {{-- Wallet & Affiliate (Semua User) --}}
                @if ($isUser)
                <li class="pt-4 pb-1 mt-4 border-t border-slate-200/60 dark:border-slate-800">
                    <span class="px-3 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Pendapatan</span>
                </li>
                <li>
                    <a href="{{ route('user.wallet.history') }}"
                        class="{{ $navLink(request()->routeIs('user.wallet*')) }}">
                        <i
                            class="fa-solid fa-wallet {{ $iconClass(request()->routeIs('user.wallet*')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Wallet Saya</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.affiliate.dashboard') }}"
                        class="{{ $navLink(request()->routeIs('user.affiliate*')) }}">
                        <i
                            class="fa-solid fa-users-viewfinder {{ $iconClass(request()->routeIs('user.affiliate*')) }}"></i>
                        <span class="ms-3 whitespace-nowrap">Program Affiliate</span>
                    </a>
                </li>
            @endif

        </ul>
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
