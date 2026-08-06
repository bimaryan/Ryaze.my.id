@props([
    'links' => [],
    'active' => '',
])

@php
    $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal';
    $siteLogo = \App\Models\Setting::where('key', 'site_logo')->value('value');
    $dashboardUrl = match (Auth::user()->role ?? '') {
        'superadmin' => route('superadmin.dashboard'),
        'admin_joki' => route('admin_joki.dashboard'),
        'admin_hosting' => route('admin_hosting.dashboard'),
        'user_joki' => route('user_joki.dashboard'),
        'user_hosting' => route('user_hosting.dashboard'),
        default => url('/'),
    };
@endphp

<nav x-data="{ mobileMenuOpen: false }" class="fixed top-0 z-50 w-full bg-white/90 backdrop-blur-md border-b border-slate-200 transition-all duration-200">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                @if ($siteLogo)
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="Logo" class="h-8 object-contain">
                @else
                    <div class="bg-indigo-600 text-white rounded-md w-8 h-8 flex items-center justify-center">
                        <i class="fa-solid fa-code text-sm"></i>
                    </div>
                @endif
                <span class="text-xl font-bold tracking-tight text-indigo-600">{{ $siteName }}</span>
            </a>

            @if (count($links))
                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    @foreach ($links as $link)
                        <a href="{{ $link['href'] }}" class="{{ ($active && $active === $link['label']) || ($link['active'] ?? false) ? 'text-indigo-600 font-semibold' : 'hover:text-indigo-600 transition-colors' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ $dashboardUrl }}" class="hidden sm:inline-flex text-sm font-semibold bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors hidden sm:block">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="hidden sm:inline-flex text-sm font-semibold bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        Daftar
                    </a>
                @endauth

                @if (count($links))
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none">
                        <i class="fa-solid fa-bars text-xl" x-show="!mobileMenuOpen"></i>
                        <i class="fa-solid fa-xmark text-xl" x-show="mobileMenuOpen" style="display: none;"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if (count($links))
        <div x-show="mobileMenuOpen" style="display: none;" class="md:hidden bg-white border-t border-slate-200" x-transition>
            <div class="px-4 pt-2 pb-6 space-y-1">
                @foreach ($links as $link)
                    <a href="{{ $link['href'] }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50">
                        {{ $link['label'] }}
                    </a>
                @endforeach

                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col gap-2">
                    @auth
                        <a href="{{ $dashboardUrl }}" class="block w-full text-center text-sm font-semibold bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center text-sm font-semibold border border-slate-200 text-slate-700 px-4 py-2 rounded-md hover:bg-slate-50 transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="block w-full text-center text-sm font-semibold bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    @endif
</nav>
