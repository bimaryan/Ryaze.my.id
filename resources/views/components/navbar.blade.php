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

<nav class="fixed top-0 z-50 w-full shadow bg-white dark:text-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800"
    x-data="{ userMenuOpen: false, notifOpen: false }">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start gap-2 rtl:justify-end">
                <button @click="sidebarOpen = !sidebarOpen" aria-controls="logo-sidebar" type="button"
                    class="inline-flex items-center p-2 text-sm text-slate-500 rounded-lg sm:hidden hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:focus:ring-slate-600">
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
                        $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal';
                    @endphp
                    @if ($siteLogo)
                        <img src="{{ asset('storage/' . $siteLogo) }}" alt="Logo" class="h-8 object-contain">
                    @else
                        <div class="bg-indigo-600 text-white rounded-md w-8 h-8 flex items-center justify-center">
                            <i class="fa-solid fa-code text-sm"></i>
                        </div>
                    @endif
                    <span
                        class="text-xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400 whitespace-nowrap">{{ $siteName }}</span>
                </a>
            </div>

            <div class="flex items-center gap-3">
                {{-- Dark mode toggle --}}
                <button type="button" onclick="ryazeToggleTheme(event)" aria-label="Ganti tema"
                    class="relative w-8 h-8 flex items-center justify-center text-slate-400 dark:text-slate-500 hover:text-[#7c3aed] dark:hover:text-white transition-colors focus:outline-none">
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
                        class="relative inline-flex items-center text-sm font-medium text-center text-slate-500 hover:text-indigo-600 focus:outline-none dark:hover:text-white dark:text-slate-400 p-2"
                        type="button">
                        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 14 20">
                            <path
                                d="M12.133 10.632v-1.8A5.406 5.406 0 0 0 7.979 3.57.946.946 0 0 0 8 3.464V1.1a1 1 0 0 0-2 0v2.364a.946.946 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C1.867 13.018 0 13.614 0 14.807 0 15.4 0 16 .538 16h12.924C14 16 14 15.4 14 14.807c0-1.193-1.867-1.789-1.867-4.175ZM3.823 17a3.453 3.453 0 0 0 6.354 0H3.823Z" />
                        </svg>
                        @if ($unreadNotifications->count() > 0)
                            <div
                                class="absolute flex items-center justify-center w-5 h-5 bg-red-500 border-2 border-white rounded-full -top-0.5 start-4 dark:border-slate-900">
                                <p class="text-white text-[10px] font-bold">
                                    {{ $unreadNotifications->count() }}
                                </p>
                            </div>
                        @endif
                    </button>

                    <div x-show="notifOpen" style="display: none;"
                        class="z-50 absolute right-0 mt-2 w-80 max-w-sm bg-white divide-y divide-slate-100 rounded-lg shadow-lg dark:bg-slate-800 dark:divide-slate-700">
                        <div
                            class="block px-4 py-2 font-medium text-center text-slate-700 rounded-t-lg bg-slate-50 dark:bg-slate-800 dark:text-white flex justify-between items-center">
                            <span>Notifikasi</span>
                            @if ($unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                        Tandai Terbaca
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-72 overflow-y-auto">
                            @forelse($unreadNotifications as $notification)
                                <a href="#"
                                    onclick="event.preventDefault(); document.getElementById('mark-read-{{ $notification->id }}').submit();"
                                    class="flex px-4 py-3 hover:bg-slate-100 dark:hover:bg-slate-700">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                            {{ $notification->data['message'] ?? 'Notifikasi baru' }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                            <i class="fa-regular fa-clock"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </a>
                                <form id="mark-read-{{ $notification->id }}"
                                    action="{{ route('notifications.markRead', $notification->id) }}" method="POST"
                                    class="hidden">@csrf</form>
                            @empty
                                <p class="px-4 py-3 text-sm text-center text-slate-500 dark:text-slate-400">Belum ada
                                    notifikasi baru.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- User Profile --}}
                <div class="flex items-center gap-3 ms-2 relative">
                    <div class="hidden md:block text-right">
                        <p class="text-sm font-medium text-slate-900 dark:text-white">
                            {{ Auth::user()->name ?? 'Guest' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ Auth::check() ? ucwords(str_replace('_', ' ', Auth::user()->role)) : '' }}
                        </p>
                    </div>
                    <div>
                        <button @click="userMenuOpen = !userMenuOpen; notifOpen = false"
                            @click.outside="userMenuOpen = false" type="button"
                            class="flex text-sm bg-indigo-600 text-white rounded-full focus:ring-4 focus:ring-indigo-300 dark:focus:ring-indigo-600 items-center justify-center w-8 h-8 font-bold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </button>
                    </div>

                    <div x-show="userMenuOpen" style="display: none;"
                        class="z-50 absolute right-0 top-10 mt-2 w-48 text-base list-none bg-white divide-y divide-slate-100 rounded-lg shadow-lg dark:bg-slate-700 dark:divide-slate-600">
                        <div class="px-4 py-3 md:hidden">
                            <p class="text-sm text-slate-900 dark:text-white">{{ Auth::user()->name ?? 'Guest' }}</p>
                            <p class="text-sm font-medium text-slate-500 truncate dark:text-slate-400">
                                {{ Auth::user()->email ?? '' }}</p>
                        </div>
                        <ul class="py-1" role="none">
                            <li>
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-600 dark:hover:text-white">Profil
                                    Saya</a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-600 dark:hover:text-white text-red-600 dark:text-red-400">
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
    :class="`${sidebarOpen && !desktop ? 'translate-x-0 shadow-2xl' : (desktop ? 'translate-x-0' : '-translate-x-full')} ${$store.sidebar.collapsed ? 'lg:w-20' : 'lg:w-64'}`"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-all duration-300 ease-in-out bg-white border-r border-slate-200 dark:bg-slate-900 dark:border-slate-800"
    aria-label="Sidebar">

    <div id="sidebar-scroll" class="ui-sidebar-scroll bg-white dark:bg-slate-900">

        {{-- Desktop collapse toggle --}}
        <div class="hidden lg:flex mb-2" :class="$store.sidebar.collapsed ? 'justify-center' : 'justify-end'">
            <button @click="$store.sidebar.toggle()" type="button"
                class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:text-slate-500 dark:hover:bg-slate-800 dark:hover:text-white transition-colors focus:outline-none"
                :aria-label="$store.sidebar.collapsed ? 'Perluas sidebar' : 'Ciutkan sidebar'"
                :title="$store.sidebar.collapsed ? 'Perluas sidebar' : 'Ciutkan sidebar'">
                <i class="fa-solid fa-angles-left text-xs transition-transform duration-300"
                    :class="$store.sidebar.collapsed ? 'rotate-180' : ''"></i>
            </button>
        </div>
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

            // Item aktif: highlight lembut + rail penanda di tepi kiri
            // (lihat blok <style> di bawah). Sengaja TIDAK pakai pill
            // indigo solid — bikin penuh warna dan garis penandanya
            // jadi kelihatan nyambung asal-asalan.
            $navLink = fn($active) => 'ui-sidebar-link group ' .
                ($active
                    ? 'bg-indigo-600/10 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300 font-semibold'
                    : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white');

            // Atribut penanda menu aktif. Dipakai oleh CSS (garis
            // penanda saat sidebar dicuit) dan JS auto-scroll.
            // WAJIB dibungkus {!! !!}: kalau tidak, Blade meng-escape
            // tanda kutip dan hasilnya `aria-current="&quot;page&quot;"`,
            // yaitu nilai dengan kutip literal — selector
            // [aria-current="page"] tidak akan pernah cocok.
            $navCurrent = fn($active) => $active
                ? new \Illuminate\Support\HtmlString('aria-current="page"')
                : '';

            $iconBox = fn($active) => 'flex-shrink-0 w-5 h-5 transition duration-75 ' .
                ($active
                    ? 'text-indigo-600 dark:text-indigo-400'
                    : 'text-slate-500 group-hover:text-slate-700 dark:text-slate-400 dark:group-hover:text-white');

            $sectionLabel =
                'mt-5 mb-2 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider dark:text-slate-400';
        @endphp

        <ul class="space-y-1.5 font-medium">
            {{-- Dashboard --}}
            <li>
                <a href="{{ $dashboardUrl }}" class="{{ $navLink(request()->routeIs('*.dashboard')) }}" {{ $navCurrent(request()->routeIs('*.dashboard')) }}>
                    <i
                        class="fa-solid fa-table-columns {{ $iconBox(request()->routeIs('*.dashboard')) }} text-center"></i>
                    <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Dashboard</span>
                </a>
            </li>

            {{-- ── SISTEM UTAMA (SUPERADMIN) ──────────────────────────────────── --}}
            @if ($role === 'superadmin')
                <li class="{{ $sectionLabel }}" x-show="!$store.sidebar.collapsed" x-cloak>Sistem Utama</li>

                <li>
                    <a href="{{ route('superadmin.users.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.users*')) }}" {{ $navCurrent(request()->routeIs('superadmin.users*')) }}>
                        <i
                            class="fa-solid fa-users {{ $iconBox(request()->routeIs('superadmin.users*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Data Pengguna</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.portfolios.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.portfolios*')) }}" {{ $navCurrent(request()->routeIs('superadmin.portfolios*')) }}>
                        <i
                            class="fa-solid fa-briefcase {{ $iconBox(request()->routeIs('superadmin.portfolios*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Portofolio</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.educations.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.educations*')) }}" {{ $navCurrent(request()->routeIs('superadmin.educations*')) }}>
                        <i
                            class="fa-solid fa-graduation-cap {{ $iconBox(request()->routeIs('superadmin.educations*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Pendidikan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.experiences.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.experiences*')) }}" {{ $navCurrent(request()->routeIs('superadmin.experiences*')) }}>
                        <i
                            class="fa-solid fa-briefcase {{ $iconBox(request()->routeIs('superadmin.experiences*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Pengalaman</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.testimonials.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.testimonials*')) }}" {{ $navCurrent(request()->routeIs('superadmin.testimonials*')) }}>
                        <i
                            class="fa-solid fa-comment-dots {{ $iconBox(request()->routeIs('superadmin.testimonials*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Testimoni</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.certifications.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.certifications*')) }}" {{ $navCurrent(request()->routeIs('superadmin.certifications*')) }}>
                        <i
                            class="fa-solid fa-certificate {{ $iconBox(request()->routeIs('superadmin.certifications*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Sertifikat</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.contact_messages.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.contact_messages*')) }}" {{ $navCurrent(request()->routeIs('superadmin.contact_messages*')) }}>
                        <i
                            class="fa-solid fa-inbox {{ $iconBox(request()->routeIs('superadmin.contact_messages*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Pesan Masuk</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.digital_products.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.digital_products*') || request()->routeIs('superadmin.digital_purchases*') || request()->routeIs('superadmin.tips*')) }}" {{ $navCurrent(request()->routeIs('superadmin.digital_products*') || request()->routeIs('superadmin.digital_purchases*') || request()->routeIs('superadmin.tips*')) }}>
                        <i
                            class="fa-solid fa-box-open {{ $iconBox(request()->routeIs('superadmin.digital_products*') || request()->routeIs('superadmin.digital_purchases*') || request()->routeIs('superadmin.tips*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Produk Digital</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.skills.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.skills*')) }}" {{ $navCurrent(request()->routeIs('superadmin.skills*')) }}>
                        <i
                            class="fa-solid fa-code {{ $iconBox(request()->routeIs('superadmin.skills*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Skill & Tech</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.promo_events.index') }}"
                        class="{{ $navLink(request()->routeIs('admin.promo_events*')) }}" {{ $navCurrent(request()->routeIs('admin.promo_events*')) }}>
                        <i
                            class="fa-solid fa-bullhorn {{ $iconBox(request()->routeIs('admin.promo_events*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Promo Event</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.info_updates.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.info_updates*')) }}" {{ $navCurrent(request()->routeIs('superadmin.info_updates*')) }}>
                        <i
                            class="fa-solid fa-bullhorn {{ $iconBox(request()->routeIs('superadmin.info_updates*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Info Update</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.articles.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.articles*') || request()->routeIs('superadmin.article_categories*')) }}" {{ $navCurrent(request()->routeIs('superadmin.articles*') || request()->routeIs('superadmin.article_categories*')) }}>
                        <i
                            class="fa-solid fa-newspaper {{ $iconBox(request()->routeIs('superadmin.articles*') || request()->routeIs('superadmin.article_categories*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Manajemen Blog</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.settings') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.settings*')) }}" {{ $navCurrent(request()->routeIs('superadmin.settings*')) }}>
                        <i
                            class="fa-solid fa-sliders {{ $iconBox(request()->routeIs('superadmin.settings*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Pengaturan Sistem</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.backup.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.backup*')) }}" {{ $navCurrent(request()->routeIs('superadmin.backup*')) }}>
                        <i
                            class="fa-solid fa-box-archive {{ $iconBox(request()->routeIs('superadmin.backup*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Sistem Backup</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.withdrawals.index') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.withdrawals*')) }}" {{ $navCurrent(request()->routeIs('superadmin.withdrawals*')) }}>
                        <i
                            class="fa-solid fa-money-bill-transfer {{ $iconBox(request()->routeIs('superadmin.withdrawals*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Kelola Penarikan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('superadmin.finance') }}"
                        class="{{ $navLink(request()->routeIs('superadmin.finance')) }}" {{ $navCurrent(request()->routeIs('superadmin.finance')) }}>
                        <i
                            class="fa-solid fa-chart-pie {{ $iconBox(request()->routeIs('superadmin.finance')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Laporan Keuangan</span>
                    </a>
                </li>
            @endif

            {{-- ── MANAJEMEN JOKI ──────────────────────────────────────────────── --}}
            @if ($isAdminJoki)
                <li class="{{ $sectionLabel }}" x-show="!$store.sidebar.collapsed" x-cloak>Manajemen Joki</li>

                <li>
                    <a href="{{ route('admin_joki.orders') }}"
                        class="{{ $navLink(request()->routeIs('admin_joki.orders*')) }}" {{ $navCurrent(request()->routeIs('admin_joki.orders*')) }}>
                        <i
                            class="fa-solid fa-code-branch {{ $iconBox(request()->routeIs('admin_joki.orders*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Kelola Pesanan Joki</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_joki.services.index') }}"
                        class="{{ $navLink(request()->routeIs('admin_joki.services*')) }}" {{ $navCurrent(request()->routeIs('admin_joki.services*')) }}>
                        <i
                            class="fa-solid fa-list-check {{ $iconBox(request()->routeIs('admin_joki.services*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Manajemen Layanan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_joki.finance') }}"
                        class="{{ $navLink(request()->routeIs('admin_joki.finance')) }}" {{ $navCurrent(request()->routeIs('admin_joki.finance')) }}>
                        <i
                            class="fa-solid fa-wallet {{ $iconBox(request()->routeIs('admin_joki.finance')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Keuangan Joki</span>
                    </a>
                </li>
            @endif

            {{-- ── MANAJEMEN HOSTING ───────────────────────────────────────────── --}}
            @if ($isAdminHosting)
                <li class="{{ $sectionLabel }}" x-show="!$store.sidebar.collapsed" x-cloak>Manajemen Hosting</li>

                <li>
                    <a href="{{ route('admin_hosting.projects') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.projects')) }}" {{ $navCurrent(request()->routeIs('admin_hosting.projects')) }}>
                        <i
                            class="fa-solid fa-server {{ $iconBox(request()->routeIs('admin_hosting.projects')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Kelola Project</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.deployments') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.deployments')) }}" {{ $navCurrent(request()->routeIs('admin_hosting.deployments')) }}>
                        <i
                            class="fa-solid fa-clock-rotate-left {{ $iconBox(request()->routeIs('admin_hosting.deployments')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Riwayat Deploy</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.pending') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.pending')) }}" {{ $navCurrent(request()->routeIs('admin_hosting.pending')) }}>
                        <i
                            class="fa-solid fa-triangle-exclamation {{ $iconBox(request()->routeIs('admin_hosting.pending')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Butuh Tindakan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.databases') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.databases')) }}" {{ $navCurrent(request()->routeIs('admin_hosting.databases')) }}>
                        <i
                            class="fa-solid fa-database {{ $iconBox(request()->routeIs('admin_hosting.databases')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Semua Database</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.storage') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.storage')) }}" {{ $navCurrent(request()->routeIs('admin_hosting.storage')) }}>
                        <i
                            class="fa-solid fa-hard-drive {{ $iconBox(request()->routeIs('admin_hosting.storage')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Limit Penyimpanan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.billing') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.billing')) }}" {{ $navCurrent(request()->routeIs('admin_hosting.billing')) }}>
                        <i
                            class="fa-solid fa-file-invoice-dollar {{ $iconBox(request()->routeIs('admin_hosting.billing')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Kelola Tagihan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.vouchers.index') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.vouchers*')) }}" {{ $navCurrent(request()->routeIs('admin_hosting.vouchers*')) }}>
                        <i
                            class="fa-solid fa-ticket {{ $iconBox(request()->routeIs('admin_hosting.vouchers*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Kelola Voucher</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_hosting.tickets.index') }}"
                        class="{{ $navLink(request()->routeIs('admin_hosting.tickets*')) }}" {{ $navCurrent(request()->routeIs('admin_hosting.tickets*')) }}>
                        <i
                            class="fa-solid fa-headset {{ $iconBox(request()->routeIs('admin_hosting.tickets*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Kelola Tiket</span>
                    </a>
                </li>
            @endif

            {{-- ── LAYANAN KLIEN JOKI ──────────────────────────────────────────── --}}
            @if ($isUserJoki)
                <li class="{{ $sectionLabel }}" x-show="!$store.sidebar.collapsed" x-cloak>Layanan Joki</li>

                <li>
                    <a href="{{ route('user_joki.create') }}"
                        class="{{ $navLink(request()->routeIs('user_joki.create')) }}" {{ $navCurrent(request()->routeIs('user_joki.create')) }}>
                        <i
                            class="fa-solid fa-cart-plus {{ $iconBox(request()->routeIs('user_joki.create')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Buat Pesanan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_joki.progress') }}"
                        class="{{ $navLink(request()->routeIs('user_joki.progress')) }}" {{ $navCurrent(request()->routeIs('user_joki.progress')) }}>
                        <i
                            class="fa-solid fa-laptop-code {{ $iconBox(request()->routeIs('user_joki.progress')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Progres Pengerjaan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_joki.riwayat') }}"
                        class="{{ $navLink(request()->routeIs('user_joki.riwayat')) }}" {{ $navCurrent(request()->routeIs('user_joki.riwayat')) }}>
                        <i
                            class="fa-solid fa-clock-rotate-left {{ $iconBox(request()->routeIs('user_joki.riwayat')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Riwayat Selesai</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_joki.billing') }}"
                        class="{{ $navLink(request()->routeIs('user_joki.billing')) }}" {{ $navCurrent(request()->routeIs('user_joki.billing')) }}>
                        <i
                            class="fa-solid fa-file-invoice-dollar {{ $iconBox(request()->routeIs('user_joki.billing')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Riwayat Tagihan</span>
                    </a>
                </li>
            @endif

            {{-- ── LAYANAN KLIEN HOSTING ───────────────────────────────────────── --}}
            @if ($isUserHosting)
                <li class="{{ $sectionLabel }}" x-show="!$store.sidebar.collapsed" x-cloak>Hosting</li>

                <li>
                    <a href="{{ route('user_hosting.create') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.create')) }}" {{ $navCurrent(request()->routeIs('user_hosting.create')) }}>
                        <i
                            class="fa-solid fa-rocket {{ $iconBox(request()->routeIs('user_hosting.create')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Deploy Aplikasi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_hosting.marketplace') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.marketplace')) }}" {{ $navCurrent(request()->routeIs('user_hosting.marketplace')) }}>
                        <i
                            class="fa-solid fa-store {{ $iconBox(request()->routeIs('user_hosting.marketplace')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>App Marketplace</span>
                    </a>
                </li>
                {{-- <li>
                    <a href="{{ route('user_hosting.apk.index') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.apk*')) }}" {{ $navCurrent(request()->routeIs('user_hosting.apk*')) }}>
                        <i
                            class="fa-brands fa-android {{ $iconBox(request()->routeIs('user_hosting.apk*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Web to APK</span>
                    </a>
                </li> --}}
                <li>
                    <a href="{{ route('user_hosting.tunnels.index') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.tunnels*')) }}" {{ $navCurrent(request()->routeIs('user_hosting.tunnels*')) }}>
                        <i
                            class="fa-solid fa-network-wired {{ $iconBox(request()->routeIs('user_hosting.tunnels*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Local Tunnels</span>
                        <span
                            class="inline-flex items-center justify-center px-2 ms-3 text-sm font-medium text-indigo-800 bg-indigo-100 rounded-full dark:bg-indigo-900 dark:text-indigo-300"
                            x-show="!$store.sidebar.collapsed" x-cloak>Beta</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_hosting.templates') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.templates')) }}" {{ $navCurrent(request()->routeIs('user_hosting.templates')) }}>
                        <i
                            class="fa-solid fa-layer-group {{ $iconBox(request()->routeIs('user_hosting.templates')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Galeri Template</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_hosting.domains.index') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.domains.*')) }}" {{ $navCurrent(request()->routeIs('user_hosting.domains.*')) }}>
                        <i
                            class="fa-solid fa-globe {{ $iconBox(request()->routeIs('user_hosting.domains.*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Domains</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_hosting.projects') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }}" {{ $navCurrent(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }}>
                        <i
                            class="fa-solid fa-terminal {{ $iconBox(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Proyek Aktif</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_hosting.databases') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.databases') && !request()->routeIs('user_hosting.databases.pma')) }}" {{ $navCurrent(request()->routeIs('user_hosting.databases') && !request()->routeIs('user_hosting.databases.pma')) }}>
                        <i
                            class="fa-solid fa-database {{ $iconBox(request()->routeIs('user_hosting.databases') && !request()->routeIs('user_hosting.databases.pma')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Database</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_hosting.databases.pma') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.databases.pma')) }}" {{ $navCurrent(request()->routeIs('user_hosting.databases.pma')) }}>
                        <i
                            class="fa-solid fa-table-columns {{ $iconBox(request()->routeIs('user_hosting.databases.pma')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>DB Manager</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_hosting.storage') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.storage*')) }}" {{ $navCurrent(request()->routeIs('user_hosting.storage*')) }}>
                        <i
                            class="fa-solid fa-folder-open {{ $iconBox(request()->routeIs('user_hosting.storage*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>File & Storage</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_hosting.docs') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.docs*')) }}" {{ $navCurrent(request()->routeIs('user_hosting.docs*')) }}>
                        <i
                            class="fa-solid fa-book-open {{ $iconBox(request()->routeIs('user_hosting.docs*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Dokumentasi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('whois.index') }}"
                        class="{{ $navLink(request()->routeIs('whois*')) }}" {{ $navCurrent(request()->routeIs('whois*')) }}>
                        <i
                            class="fa-solid fa-magnifying-glass {{ $iconBox(request()->routeIs('whois*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Cek Domain</span>
                    </a>
                </li>

                <li class="{{ $sectionLabel }} mt-4" x-show="!$store.sidebar.collapsed" x-cloak>Akun</li>

                <li>
                    <a href="{{ route('user_hosting.subscription') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.subscription')) }}" {{ $navCurrent(request()->routeIs('user_hosting.subscription')) }}>
                        <i
                            class="fa-solid fa-crown {{ $iconBox(request()->routeIs('user_hosting.subscription')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Langganan Paket</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_hosting.billing') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.billing')) }}" {{ $navCurrent(request()->routeIs('user_hosting.billing')) }}>
                        <i
                            class="fa-solid fa-file-invoice-dollar {{ $iconBox(request()->routeIs('user_hosting.billing')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Riwayat Tagihan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user_hosting.tickets.index') }}"
                        class="{{ $navLink(request()->routeIs('user_hosting.tickets*')) }}" {{ $navCurrent(request()->routeIs('user_hosting.tickets*')) }}>
                        <i
                            class="fa-solid fa-headset {{ $iconBox(request()->routeIs('user_hosting.tickets*')) }} text-center"></i>
                        <span class="ui-sidebar-label" x-show="!$store.sidebar.collapsed" x-cloak>Pusat Bantuan</span>
                    </a>
                </li>
            @endif
        </ul>

        {{-- Version Badge --}}
        <div class="mt-auto pt-6 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center px-2 py-2"
                :class="$store.sidebar.collapsed ? 'justify-center' : 'justify-between'">
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-600 uppercase tracking-widest"
                    x-show="!$store.sidebar.collapsed" x-cloak>Ryaze Portal</span>
                <span
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 text-[10px] font-bold text-indigo-500 dark:text-indigo-400 tracking-wide">
                    <i class="fa-solid fa-code-branch text-[8px]"></i>
                    <span x-show="!$store.sidebar.collapsed" x-cloak>{{ $appVersion }}</span>
                </span>
            </div>
        </div>
    </div>
</aside>

{{-- Sidebar collapse styling (desktop only) --}}
<style>
    @media (min-width: 1024px) {
        #logo-sidebar[class*="lg:w-20"] a {
            justify-content: center;
        }

        #logo-sidebar[class*="lg:w-20"] a i {
            margin: 0;
        }
    }

    /* Penanda menu aktif: rail vertikal yang menempel rapi di tepi
       kiri sidebar, bukan garis kecil yang mengambang jauh dari
       item. Tetap kelihatan walau sidebar dicuit (labelnya
       disembunyikan), karena posisinya dihitung dari tepi sidebar. */
    #logo-sidebar a[aria-current="page"] {
        position: relative;
    }

    #logo-sidebar a[aria-current="page"]::before {
        content: "";
        position: absolute;
        /* -0.75rem = tepat sebesar padding px-3 milik .ui-sidebar-scroll,
           jadi rail-nya nyangkut di tepi sidebar, bukan di tengah. */
        left: -0.75rem;
        top: 50%;
        translate: 0 -50%;
        height: 1.5rem;
        width: 4px;
        /* Radius hanya di sisi kanan: ujung kirinya rata dengan tepi
           sidebar, tidak terpotong bulat. */
        border-radius: 0 4px 4px 0;
        background-color: var(--color-indigo-600);
    }

    /* Hover di menu aktif harus tetap nuansa indigo. Tanpa ini,
       state hover item biasa (bg-slate-100) menimpa highlight
       aktif dan item aktif mendadak jadi abu-abu.
       Specificity ID (1,2,1) mengalahkan utility Tailwind (0,2,0). */
    #logo-sidebar a[aria-current="page"]:hover {
        background-color: rgb(79 70 229 / 0.15);
    }
</style>

{{--
    Auto-scroll sidebar ke menu yang sedang aktif.

    Sidebar bisa setinggi 2452px sementara viewport hanya 720px,
    sehingga halaman seperti /superadmin/backup menaruh item aktif
    di BAWAH area yang terlihat — user menekan tombol di sidebar
    tapi tidak tahu halaman mana yang terbuka, dan scrollbar tetap
    di posisi atas.

    Perilaku:
      - hanya scroll bila item aktif memang terpotong; menu di
        atas tidak pernah membuat sidebar "berganti tempat"
      - item aktif diberi sedikit ruang kosong di atas dan bawah
        supaya tidak menempel tepi
      - dijalankan ulang setelah sidebar dibuka di layar kecil
        (saathidden -> flex, posisi baru bisa dihitung)
--}}
<script nonce="{{ csp_nonce() }}">
    (function () {
        const scrollActiveIntoView = () => {
            const scroller = document.getElementById('sidebar-scroll');
            if (!scroller) return;

            const active = scroller.querySelector('[aria-current="page"]');
            if (!active) return;

            // Posisi item aktif di dalam scroller, TIDAK bergantung
            // pada scroll sekarang: dihitung dari offsetTop terhadap
            // scroller langsung.
            //
            // activeTop  > scrollTop  -> item ada di BAWAH area terlihat
            // activeBottom < scrollTop -> item ada di ATAS area terlihat
            //
            // Kita mau item aktif berada dengan sedikit ruang di
            // bawah viewport scroller (bukan tepat di tepi bawah),
            // supaya konteks sekelompok menu-nya kelihatan.
            const pad = 24;
            const activeTop = active.offsetTop - scroller.offsetTop;
            const activeBottom = activeTop + active.offsetHeight;

            const viewTop = scroller.scrollTop;
            const viewBottom = viewTop + scroller.clientHeight;

            // Sudah pas (ada ruang di atas DAN di bawah) -> diamkan.
            if (activeTop >= viewTop + pad && activeBottom <= viewBottom - pad) return;

            // Scroll sehingga activeBottom berada di viewBottom - pad.
            let target = activeBottom - scroller.clientHeight + pad;

            // Kalau item-nya muat dari atas, align ke atas saja.
            if (activeTop - pad >= 0 && activeTop < viewTop) {
                target = activeTop - pad;
            }

            const max = scroller.scrollHeight - scroller.clientHeight;
            scroller.scrollTo({
                top: Math.max(0, Math.min(target, max)),
                behavior: 'smooth',
            });
        };

        // Setelah DOM siap.
        document.addEventListener('DOMContentLoaded', scrollActiveIntoView);

        // Setelah navigasi PJAX selesai mengganti konten.
        document.addEventListener('pjax:end', scrollActiveIntoView);

        // Sidebar dibuka di layar kecil: dari -translate-x-full
        // menjadi translate-x-0, posisi item ikut berubah.

        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(scrollActiveIntoView, 150);
        });
    })();
</script>
