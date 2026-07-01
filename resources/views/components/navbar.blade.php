<nav class="bg-white border-b border-gray-200 fixed w-full z-50 top-0 start-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo & Links --}}
            <div class="flex items-center gap-8">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <div class="bg-gray-900 text-white rounded p-1.5 flex items-center justify-center w-8 h-8">
                        <i class="fa-solid fa-code text-sm"></i>
                    </div>
                    <span class="self-center text-lg font-bold whitespace-nowrap text-gray-900">Ryaze Portal</span>
                </a>

                {{-- Horizontal Links --}}
                <div class="hidden md:flex space-x-1">
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

                        $isAdminHosting = in_array($role, ['superadmin', 'admin_hosting']);
                        $isAdminJoki = in_array($role, ['superadmin', 'admin_joki']);
                        $isUserHosting = $role === 'user_hosting';
                        $isUserJoki = $role === 'user_joki';

                        $navLink = fn($active) => 'px-3 py-2 rounded-md text-sm font-medium transition-colors ' .
                            ($active
                                ? 'bg-gray-100 text-gray-900'
                                : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50');
                    @endphp

                    <a href="{{ $dashboardUrl }}" class="{{ $navLink(request()->routeIs('*.dashboard')) }}">Overview</a>

                    {{-- Superadmin Links --}}
                    @if ($role === 'superadmin')
                        <a href="{{ route('superadmin.users.index') }}" class="{{ $navLink(request()->routeIs('superadmin.users*')) }}">Users</a>
                    @endif

                    {{-- Admin Hosting --}}
                    @if ($isAdminHosting)
                        <a href="{{ route('admin_hosting.projects') }}" class="{{ $navLink(request()->routeIs('admin_hosting.projects')) }}">Projects</a>
                        <a href="{{ route('admin_hosting.deployments') }}" class="{{ $navLink(request()->routeIs('admin_hosting.deployments')) }}">Deployments</a>
                        <a href="{{ route('admin_hosting.billing') }}" class="{{ $navLink(request()->routeIs('admin_hosting.billing')) }}">Billing</a>
                    @endif

                    {{-- User Hosting --}}
                    @if ($isUserHosting)
                        <a href="{{ route('user_hosting.projects') }}" class="{{ $navLink(request()->routeIs('user_hosting.projects') || request()->routeIs('user_hosting.show')) }}">Projects</a>
                        <a href="{{ route('user_hosting.databases') }}" class="{{ $navLink(request()->routeIs('user_hosting.databases*')) }}">Databases</a>
                        <a href="{{ route('user_hosting.storage') }}" class="{{ $navLink(request()->routeIs('user_hosting.storage*')) }}">Storage</a>
                        <a href="{{ route('user_hosting.billing') }}" class="{{ $navLink(request()->routeIs('user_hosting.billing')) }}">Billing</a>
                        <a href="{{ route('user_hosting.docs') }}" class="{{ $navLink(request()->routeIs('user_hosting.docs*')) }}">Docs</a>
                    @endif

                    {{-- Admin Joki --}}
                    @if ($isAdminJoki)
                        <a href="{{ route('admin_joki.orders') }}" class="{{ $navLink(request()->routeIs('admin_joki.orders*')) }}">Orders</a>
                        <a href="{{ route('admin_joki.services.index') }}" class="{{ $navLink(request()->routeIs('admin_joki.services*')) }}">Services</a>
                        <a href="{{ route('admin_joki.finance') }}" class="{{ $navLink(request()->routeIs('admin_joki.finance')) }}">Finance</a>
                    @endif

                    {{-- User Joki --}}
                    @if ($isUserJoki)
                        <a href="{{ route('user_joki.progress') }}" class="{{ $navLink(request()->routeIs('user_joki.progress')) }}">Progress</a>
                        <a href="{{ route('user_joki.riwayat') }}" class="{{ $navLink(request()->routeIs('user_joki.riwayat')) }}">History</a>
                        <a href="{{ route('user_joki.billing') }}" class="{{ $navLink(request()->routeIs('user_joki.billing')) }}">Billing</a>
                    @endif

                </div>
            </div>

            {{-- Right Section: Notifications & User --}}
            <div class="flex items-center gap-4">
                @php
                    $unreadNotifications = Auth::check() ? Auth::user()->unreadNotifications : collect([]);
                @endphp
                <button id="dropdownNotificationButton" data-dropdown-toggle="dropdownNotification"
                    class="relative p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
                    type="button">
                    <span class="sr-only">View notifications</span>
                    <i class="fa-regular fa-bell text-xl"></i>
                    @if($unreadNotifications->count() > 0)
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    @endif
                </button>

                {{-- Notification Dropdown --}}
                <div id="dropdownNotification"
                    class="z-20 hidden w-80 max-w-sm bg-white divide-y divide-gray-100 rounded-lg shadow-lg border border-gray-100"
                    aria-labelledby="dropdownNotificationButton">
                    <div class="flex items-center justify-between px-4 py-3 font-semibold text-gray-700 bg-gray-50 rounded-t-lg border-b border-gray-100">
                        <span>Notifications</span>
                        @if($unreadNotifications->count() > 0)
                        <form action="{{ route('notifications.markAllRead') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-gray-900 hover:text-gray-700">Mark all read</button>
                        </form>
                        @endif
                    </div>
                    <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                        @forelse($unreadNotifications as $notification)
                            <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left flex px-4 py-3 hover:bg-gray-50">
                                    <div class="w-full pl-3">
                                        <div class="text-gray-600 text-sm mb-1.5">{{ $notification->data['message'] ?? 'New notification' }}</div>
                                        <div class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </button>
                            </form>
                        @empty
                            <p class="px-6 py-4 text-sm text-gray-500 text-center">No new notifications.</p>
                        @endforelse
                    </div>
                </div>

                {{-- User Dropdown --}}
                <div class="relative ml-3">
                    <button type="button"
                        class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
                        id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown">
                        <span class="sr-only">Open user menu</span>
                        <div class="h-8 w-8 rounded-full bg-gray-900 text-white flex items-center justify-center font-semibold border border-gray-200">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                    </button>
                </div>

                <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-lg border border-gray-100 min-w-[200px]"
                    id="user-dropdown">
                    <div class="px-4 py-3">
                        <p class="text-sm text-gray-900 font-semibold">{{ Auth::user()->name ?? 'Guest' }}</p>
                        <p class="text-xs text-gray-500 truncate mt-0.5">
                            {{ Auth::check() ? ucwords(str_replace('_', ' ', Auth::user()->role)) : '' }}
                        </p>
                    </div>
                    <ul class="py-1" aria-labelledby="user-menu-button">
                        <li>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                    Sign out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
