@props([
    'compact' => false,
])

@php
    $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal';
    $socialGithub = \App\Models\Setting::where('key', 'social_github')->value('value');
    $socialInstagram = \App\Models\Setting::where('key', 'social_instagram')->value('value');
    $socialLinkedin = \App\Models\Setting::where('key', 'social_linkedin')->value('value');
    $contactEmail = \App\Models\Setting::where('key', 'contact_email')->value('value');
    $contactWhatsapp = \App\Models\Setting::where('key', 'contact_whatsapp')->value('value');
@endphp

<footer class="bg-white border-t border-slate-200 {{ $compact ? 'py-8' : 'py-12' }}">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        @if (! $compact)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="flex flex-col items-start gap-2">
                    <div class="flex items-center gap-2 text-indigo-600">
                        <i class="fa-solid fa-code text-lg"></i>
                        <span class="text-xl font-bold tracking-tight">{{ $siteName }}</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed max-w-xs mt-1">
                        Jasa pembuatan website & aplikasi, plus shared hosting Indonesia dengan auto-deploy, SSL gratis,
                        dan database MySQL.
                    </p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Layanan</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/#services') }}" class="text-slate-500 hover:text-indigo-600 transition-colors">Jasa Pembuatan Website</a></li>
                        <li><a href="{{ url('/#services') }}" class="text-slate-500 hover:text-indigo-600 transition-colors">Shared Hosting Indonesia</a></li>
                        <li><a href="{{ url('/#pricing') }}" class="text-slate-500 hover:text-indigo-600 transition-colors">Harga Paket Hosting</a></li>
                        <li><a href="{{ url('/#portfolio') }}" class="text-slate-500 hover:text-indigo-600 transition-colors">Portofolio</a></li>
                        <li><a href="{{ route('blog.index') }}" class="text-slate-500 hover:text-indigo-600 transition-colors">Blog & Tips Hosting</a></li>
                    </ul>
                </div>
                <div class="flex flex-col items-start gap-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Ikuti Kami</p>
                    <div class="flex gap-6 items-center">
                        @if ($contactEmail)
                            <a href="mailto:{{ $contactEmail }}" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                <i class="fa-solid fa-envelope text-xl"></i>
                            </a>
                        @endif
                        @if ($contactWhatsapp)
                            <a href="https://wa.me/62{{ ltrim($contactWhatsapp, '0') }}" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-emerald-500 transition-colors">
                                <i class="fa-brands fa-whatsapp text-xl"></i>
                            </a>
                        @endif
                        @if ($socialGithub)
                            <a href="{{ $socialGithub }}" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                <i class="fa-brands fa-github text-xl"></i>
                            </a>
                        @endif
                        @if ($socialInstagram)
                            <a href="{{ $socialInstagram }}" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-pink-600 transition-colors">
                                <i class="fa-brands fa-instagram text-xl"></i>
                            </a>
                        @endif
                        @if ($socialLinkedin)
                            <a href="{{ $socialLinkedin }}" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-blue-600 transition-colors">
                                <i class="fa-brands fa-linkedin text-xl"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        <div class="{{ $compact ? '' : 'mt-8 pt-8 border-t border-slate-100' }} flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-slate-500">
            <p>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
            <p>Engineered by Bima Ryan.</p>
        </div>
    </div>
</footer>
