@props([
    'title' => '',
    'description' => '',
    'withNav' => true,
    'withFooter' => true,
    'bodyClass' => 'bg-white font-sans antialiased text-slate-900',
    'htmlClass' => 'scroll-smooth',
    'lang' => 'id',
    'links' => [],
    'active' => '',
    'ogImage' => '',
    'footerCompact' => false,
])

<!DOCTYPE html>
<html lang="{{ $lang }}" class="{{ $htmlClass }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @php
        $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze Portal';
        $siteDescription = \App\Models\Setting::where('key', 'site_description')->value('value') ?? 'Layanan web hosting canggih dan jasa development profesional.';
        $siteFavicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
        $gaId = \App\Models\Setting::where('key', 'google_analytics_id')->value('value');
        $pageTitle = $title ? $title . ' - ' . $siteName : $siteName;
        $pageDescription = $description ?: $siteDescription;
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">

    @if ($siteFavicon)
        <link rel="icon" href="{{ asset('storage/' . $siteFavicon) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $siteFavicon) }}">
    @endif

    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:locale" content="id_ID">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="twitter:image" content="{{ $ogImage }}">
    @elseif ($siteFavicon)
        <meta property="og:image" content="{{ asset('storage/' . $siteFavicon) }}">
        <meta property="twitter:image" content="{{ asset('storage/' . $siteFavicon) }}">
    @endif

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $pageTitle }}">
    <meta property="twitter:description" content="{{ $pageDescription }}">

    @if ($gaId)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}" nonce="{{ csp_nonce() }}"></script>
        <script nonce="{{ csp_nonce() }}">
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" nonce="{{ csp_nonce() }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer" nonce="{{ csp_nonce() }}">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js" nonce="{{ csp_nonce() }}"></script>

    @stack('head')
</head>

<body class="{{ $bodyClass }}">
    @if ($withNav)
        <x-public-nav :links="$links" :active="$active" />
    @endif

    {{ $slot }}

    @if ($withFooter)
        <x-public-footer :compact="$footerCompact" />
    @endif

    @include('components.hot-toast')
    @stack('scripts')
</body>

</html>
