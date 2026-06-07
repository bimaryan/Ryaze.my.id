<nav class="fixed top-0 z-50 w-full bg-indigo-700 border-b border-indigo-800 shadow-md">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start gap-2 rtl:justify-end">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-indigo-100 rounded-lg sm:hidden hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                        </path>
                    </svg>
                </button>

                <a href="{{ url('/') }}" class="flex ms-2 md:me-24 items-center gap-2">
                    <div class="bg-white text-indigo-700 rounded p-1.5 shadow-sm">
                        <i class="fa-solid fa-code"></i>
                    </div>
                    <span class="self-center text-xl font-bold sm:text-2xl whitespace-nowrap text-white">Ryaze
                        Portal</span>
                </a>
            </div>

            <div class="flex items-center">
                <div class="flex items-center ms-3 gap-5">
                    <button id="dropdownNotificationButton" data-dropdown-toggle="dropdownNotification"
                        class="relative inline-flex items-center text-sm font-medium text-center text-indigo-200 hover:text-white focus:outline-none transition-colors"
                        type="button">
                        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 14 20">
                            <path
                                d="M12.133 10.632v-1.8A5.406 5.406 0 0 0 7.979 3.57.946.946 0 0 0 8 3.464V1.1a1 1 0 0 0-2 0v2.364a.946.946 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C1.867 13.018 0 13.614 0 14.807 0 15.4 0 16 .538 16h12.924C14 16 14 15.4 14 14.807c0-1.193-1.867-1.789-1.867-4.175ZM3.823 17a3.453 3.453 0 0 0 6.354 0H3.823Z" />
                        </svg>
                        <div
                            class="absolute block w-5 h-5 bg-emerald-500 border-2 border-indigo-700 rounded-full -top-1 start-3">
                            <p class="text-white text-xs font-bold">0</p>
                        </div>
                    </button>

                    <div id="dropdownNotification"
                        class="z-20 hidden max-w-sm bg-white divide-y divide-slate-100 rounded-lg shadow-xl"
                        aria-labelledby="dropdownNotificationButton">
                        <div
                            class="block px-4 py-3 font-semibold text-center text-slate-700 rounded-t-lg bg-slate-50 border-b border-slate-100">
                            Notifikasi Terbaru
                        </div>
                        <div class="divide-y divide-slate-100">
                            <p class="px-6 py-4 text-sm text-slate-500 text-center">Belum ada notifikasi baru.</p>
                        </div>
                    </div>

                    <div class="hidden md:block text-right border-l border-indigo-500 pl-5">
                        <p class="text-sm font-semibold text-white">{{ Auth::user()->name ?? 'Guest' }}</p>
                        <p class="text-xs text-indigo-200">
                            {{ Auth::check() ? ucwords(str_replace('_', ' ', Auth::user()->role)) : 'No Role' }}</p>
                    </div>
                    <div>
                        <button type="button"
                            class="flex text-sm bg-indigo-800 rounded-full focus:ring-4 focus:ring-indigo-400 shadow-sm transition-transform hover:scale-105"
                            aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            <div
                                class="w-9 h-9 rounded-full bg-white text-indigo-700 flex items-center justify-center font-bold text-lg">
                                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                            </div>
                        </button>
                    </div>

                    <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-slate-100 rounded-xl shadow-xl border border-slate-100"
                        id="dropdown-user">
                        <div class="px-4 py-3 md:hidden">
                            <p class="text-sm text-slate-900 font-bold">
                                {{ Auth::user()->name ?? 'Guest' }}</p>
                            <p class="text-xs font-medium text-slate-500 truncate">
                                {{ Auth::check() ? ucwords(str_replace('_', ' ', Auth::user()->role)) : '' }}</p>
                        </div>
                        <ul class="py-1" role="none">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-slate-50"
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
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-slate-50 border-r border-slate-200 sm:translate-x-0"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-slate-50">
        <ul class="space-y-2 font-medium">

            @php
                $dashboardUrl = match (Auth::user()->role ?? '') {
                    'superadmin' => route('superadmin.dashboard'),
                    'admin_joki' => route('admin_joki.dashboard'),
                    'user_joki' => route('user_joki.dashboard'),
                    'user_hosting' => route('user_hosting.dashboard'),
                    'admin_hosting' => route('admin_hosting.dashboard'),
                    default => url('/'),
                };
            @endphp

            <li>
                <a href="{{ $dashboardUrl }}"
                    class="flex items-center p-3 rounded-lg transition-all duration-200 group {{ request()->is('*dashboard*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <span class="flex-1 ms-3 whitespace-nowrap"><i class="fa-solid fa-border-all me-2"></i>
                        Dashboard</span>
                </a>
            </li>

            @if (in_array(Auth::user()->role, ['superadmin', 'admin_joki', 'admin_hosting']))
                <li class="pt-5 mt-5 space-y-2 border-t border-slate-200">
                    <span class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Manajemen Admin</span>
                </li>

                @if (in_array(Auth::user()->role, ['superadmin', 'admin_joki']))
                    <li>
                        <a href="{{ route('admin_joki.orders') }}"
                            class="flex items-center p-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin_joki.orders') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-100 hover:text-indigo-700' }}">
                            <span class="flex-1 ms-3 whitespace-nowrap"><i class="fa-solid fa-code-branch me-2"></i>
                                Kelola Pesanan Joki</span>
                        </a>
                    </li>
                @endif

                @if (in_array(Auth::user()->role, ['superadmin', 'admin_hosting']))
                    <li>
                        <a href="#"
                            class="flex items-center p-3 rounded-lg transition-all duration-200 group text-slate-600 hover:bg-indigo-100 hover:text-indigo-700">
                            <span class="flex-1 ms-3 whitespace-nowrap"><i class="fa-solid fa-server me-2"></i> Node /
                                Server Instances</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role === 'superadmin')
                    <li>
                        <a href="{{ route('superadmin.users.index') }}"
                            class="flex items-center p-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('superadmin.users.index') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-100 hover:text-indigo-700' }}">
                            <span class="flex-1 ms-3 whitespace-nowrap"><i class="fa-solid fa-users me-2"></i> Data
                                Pengguna</span>
                        </a>
                    </li>
                @endif
            @endif

            @if (in_array(Auth::user()->role, ['user_joki', 'user_hosting']))
                <li class="pt-5 mt-5 space-y-2 border-t border-slate-200">
                    <span class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Layanan Klien</span>
                </li>

                @if (Auth::user()->role === 'user_joki')
                    <li>
                        <a href="{{ route('user_joki.create') }}"
                            class="flex items-center p-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('user_joki.create') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-100 hover:text-indigo-700' }}">
                            <span class="flex-1 ms-3 whitespace-nowrap"><i class="fa-solid fa-cart-plus me-2"></i> Pesan
                                Joki Baru</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_joki.progress') }}"
                            class="flex items-center p-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('user_joki.progress') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-100 hover:text-indigo-700' }}">
                            <span class="flex-1 ms-3 whitespace-nowrap"><i class="fa-solid fa-laptop-code me-2"></i>
                                Progres Joki Saya</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_joki.riwayat') }}"
                            class="flex items-center p-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('user_joki.riwayat') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-100 hover:text-indigo-700' }}">
                            <span class="flex-1 ms-3 whitespace-nowrap"><i class="fa-solid fa-history me-2"></i>
                                Riwayat Joki Saya</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role === 'user_hosting')
                    <li>
                        <a href="{{ route('user_hosting.create') }}"
                            class="flex items-center p-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('user_hosting.create') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-100 hover:text-indigo-700' }}">
                            <span class="flex-1 ms-3 whitespace-nowrap"><i class="fa-brands fa-github me-2"></i>
                                Deploy Proyek Baru</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.projects') }}"
                            class="flex items-center p-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('user_hosting.projects') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-100 hover:text-indigo-700' }}">
                            <span class="flex-1 ms-3 whitespace-nowrap"><i class="fa-solid fa-terminal me-2"></i>
                                Aplikasi Ter-deploy</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_hosting.databases') }}"
                            class="flex items-center p-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('user_hosting.databases') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-100 hover:text-indigo-700' }}">
                            <span class="flex-1 ms-3 whitespace-nowrap"><i class="fa-solid fa-database me-2"></i>
                                Database Mysql</span>
                        </a>
                    </li>
                @endif

                <li>
                    <a href="#"
                        class="flex items-center p-3 rounded-lg transition-all duration-200 group text-slate-600 hover:bg-indigo-100 hover:text-indigo-700">
                        <span class="flex-1 ms-3 whitespace-nowrap"><i
                                class="fa-solid fa-file-invoice-dollar me-2"></i> Riwayat Tagihan</span>
                    </a>
                </li>
            @endif

        </ul>
    </div>
</aside>
