<nav class="fixed top-0 z-50 w-full shadow bg-gray-50">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start gap-2 rtl:justify-end">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                        </path>
                    </svg>
                </button>

                <a href="{{ url('/') }}" class="flex ms-2 md:me-24 items-center gap-2">
                    <span class="self-center text-xl text-indigo-600 font-bold sm:text-2xl whitespace-nowrap">Ryaze
                        Portal</span>
                </a>
            </div>

            <div class="flex items-center">
                <div class="flex items-center ms-3 gap-5">
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
                                class="absolute block w-5 h-5 bg-emerald-500 border-2 border-white rounded-full -top-1 start-3">
                                <p class="text-slate-800 text-[10px] leading-tight font-bold">
                                    {{ $unreadNotifications->count() > 9 ? '9+' : $unreadNotifications->count() }}</p>
                            </div>
                        @endif
                    </button>

                    <div id="dropdownNotification"
                        class="z-20 hidden w-80 max-w-sm bg-white divide-y divide-slate-100 rounded-lg shadow-xl"
                        aria-labelledby="dropdownNotificationButton">
                        <div
                            class="flex items-center justify-between px-4 py-3 font-semibold text-slate-700 rounded-t-lg bg-transparent border-b border-slate-100">
                            <span>Notifikasi Terbaru</span>
                            @if ($unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800">Tandai
                                        Dibaca</button>
                                </form>
                            @endif
                        </div>
                        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                            @forelse($unreadNotifications as $notification)
                                <a href="#"
                                    onclick="event.preventDefault(); document.getElementById('mark-read-{{ $notification->id }}').submit();"
                                    class="flex px-4 py-3 hover:bg-transparent">
                                    <div class="w-full pl-3">
                                        <div class="text-slate-600 text-sm mb-1.5">
                                            {{ $notification->data['message'] ?? 'Notifikasi baru' }}</div>
                                        <div class="text-xs text-slate-500">
                                            {{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </a>
                                <form id="mark-read-{{ $notification->id }}"
                                    action="{{ route('notifications.markRead', $notification->id) }}" method="POST"
                                    class="hidden">
                                    @csrf
                                </form>
                            @empty
                                <p class="px-6 py-4 text-sm text-slate-500 text-center">Belum ada notifikasi baru.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Info User --}}
                    <div class="hidden md:block text-right border-l border-slate-300 pl-5">
                        <p class="text-sm font-semibold text-slate-800">{{ Auth::user()->name ?? 'Guest' }}</p>
                        <p class="text-xs text-slate-500">
                            {{ Auth::check() ? ucwords(str_replace('_', ' ', Auth::user()->role)) : 'No Role' }}
                        </p>
                    </div>

                    {{-- Avatar + Dropdown --}}
                    <div>
                        <button type="button"
                            class="flex text-sm bg-slate-100 rounded-full focus:ring-4 focus:ring-slate-200 border border-slate-200 shadow-sm transition-transform hover:scale-105"
                            aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            <div
                                class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-lg">
                                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                            </div>
                        </button>
                    </div>

                    <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-slate-100 rounded-xl shadow-xl border border-slate-100"
                        id="dropdown-user">
                        <div class="px-4 py-3 md:hidden">
                            <p class="text-sm text-slate-900 font-bold">{{ Auth::user()->name ?? 'Guest' }}</p>
                            <p class="text-xs font-medium text-slate-500 truncate">
                                {{ Auth::check() ? ucwords(str_replace('_', ' ', Auth::user()->role)) : '' }}
                            </p>
                        </div>
                        <ul class="py-1" role="none">
                            <li>
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-slate-700 hover:bg-transparent">
                                    <i class="fa-solid fa-user me-2 text-indigo-500"></i> Profil Saya
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-transparent border-t border-slate-100"
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
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 mt-3 overflow-y-auto bg-white">
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
                $isUserHosting = $role === 'user_hosting';
                $isUserJoki = $role === 'user_joki';
                $isUser = in_array($role, ['user_joki', 'user_hosting']);

                $navLink = fn(
                    $active,
                ) => 'flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group text-sm font-medium ' .
                    ($active
                        ? 'bg-indigo-600 text-slate-800 shadow-md text-white shadow-indigo-200/50'
                        : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700');

                $iconClass = fn($active) => 'w-6 text-center text-lg transition-transform group-hover:scale-110 ' .
                    ($active ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600');
            @endphp

            {{-- Dashboard --}}
            <li>
                <a href="{{ $dashboardUrl }}" class="{{ $navLink(request()->routeIs('*.dashboard')) }}">
                    <i class="fa-solid fa-border-all {{ $iconClass(request()->routeIs('*.dashboard')) }}"></i>
                    <span class="ms-3 whitespace-nowrap">Dashboard</span>
                </a>
            </li>

            {{-- ══ SISTEM UTAMA (SUPERADMIN) ══════════════════════════════════ --}}
            @if ($role === 'superadmin')
                <li class="pt-4 pb-1 mt-4 border-t border-slate-200/60">
                    <span class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sistem Utama</span>
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

            @endif

            {{-- ══ MANAJEMEN JOKI ══════════════════════════════════ --}}
            @if ($isAdminJoki)
                <li class="pt-4 pb-1 mt-4 border-t border-slate-200/60">
                    <span class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        {{ $role === 'superadmin' ? 'Manajemen Joki' : 'Manajemen Admin' }}
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

            {{-- ══ MANAJEMEN HOSTING ══════════════════════════════════ --}}
            @if ($isAdminHosting)
                <li class="pt-4 pb-1 mt-4 border-t border-slate-200/60">
                    <span class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
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
            @endif


            {{-- ══ LAYANAN KLIEN ════════════════════════════════════ --}}
            @if ($isUser)
                <li class="pt-4 pb-1 mt-4 border-t border-slate-200/60">
                    <span class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Layanan
                        Klien</span>
                </li>

                {{-- Menu User Joki --}}
                @if ($isUserJoki)
                    <li>
                        <a href="{{ route('user_joki.create') }}"
                            class="{{ $navLink(request()->routeIs('user_joki.create')) }}">
                            <i
                                class="fa-solid fa-cart-plus {{ $iconClass(request()->routeIs('user_joki.create')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Pesan Joki Baru</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_joki.progress') }}"
                            class="{{ $navLink(request()->routeIs('user_joki.progress')) }}">
                            <i
                                class="fa-solid fa-laptop-code {{ $iconClass(request()->routeIs('user_joki.progress')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Progres Joki Saya</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_joki.riwayat') }}"
                            class="{{ $navLink(request()->routeIs('user_joki.riwayat')) }}">
                            <i
                                class="fa-solid fa-history {{ $iconClass(request()->routeIs('user_joki.riwayat')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Riwayat Joki Saya</span>
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

                {{-- Menu User Hosting --}}
                @if ($isUserHosting)
                    <li>
                        <a href="{{ route('user_hosting.create') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.create')) }}">
                            <i
                                class="fa-brands fa-github {{ $iconClass(request()->routeIs('user_hosting.create')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Deploy Proyek Baru</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.projects') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }}">
                            <i
                                class="fa-solid fa-terminal {{ $iconClass(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Aplikasi Ter-deploy</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.databases') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.databases*')) }}">
                            <i
                                class="fa-solid fa-database {{ $iconClass(request()->routeIs('user_hosting.databases*')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Database MySQL</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.storage') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.storage*')) }}">
                            <i
                                class="fa-solid fa-hard-drive {{ $iconClass(request()->routeIs('user_hosting.storage*')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Penyimpanan / Storage</span>
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
                        <a href="{{ route('user_hosting.billing') }}"
                            class="{{ $navLink(request()->routeIs('user_hosting.billing')) }}">
                            <i
                                class="fa-solid fa-file-invoice-dollar {{ $iconClass(request()->routeIs('user_hosting.billing')) }}"></i>
                            <span class="ms-3 whitespace-nowrap">Riwayat Tagihan</span>
                        </a>
                    </li>
                @endif
            @endif

        </ul>
    </div>
</aside>
