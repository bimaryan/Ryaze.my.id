<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <script>
        (function() {
            var stored = localStorage.getItem('ryaze-theme');
            if (stored === null) {
                stored = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            if (stored === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.style.colorScheme = 'dark';
            } else {
                document.documentElement.style.colorScheme = 'light';
            }
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        // Meta SEO server-side. Halaman Inertia mengirim prop:
        // seoTitle/seoDescription/canonical/robots/ogType/ogImage dari controller.
        // Prop `title`/`description` lama tetap didukung sebagai fallback.
        $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'Ryaze';
        $siteDescription = \App\Models\Setting::where('key', 'site_description')->value('value') ?? 'Platform Layanan Joki dan Web Hosting Profesional Terpercaya.';
        $pageProps = $page['props'] ?? [];
        $seoTitle = $pageProps['seoTitle'] ?? $pageProps['title'] ?? $siteName;
        $seoDescription = $pageProps['seoDescription'] ?? $pageProps['description'] ?? $siteDescription;
        $seoCanonical = $pageProps['canonical'] ?? url()->current();
        $seoRobots = $pageProps['robots'] ?? 'index, follow, max-image-preview:large';
        $seoOgType = $pageProps['ogType'] ?? 'website';
        $seoOgImage = $pageProps['ogImage'] ?? asset('og-image.png');
        $siteFavicon = $pageProps['favicon'] ?? \App\Models\Setting::where('key', 'site_favicon')->value('value');
        $gaId = \App\Models\Setting::where('key', 'google_analytics_id')->value('value');
    @endphp

    <title>{{ $seoTitle }}</title>
    <meta data-seo-server name="description" content="{{ $seoDescription }}">
    <meta data-seo-server name="robots" content="{{ $seoRobots }}">
    <link data-seo-server rel="canonical" href="{{ $seoCanonical }}">

    {{-- React Head mengambil alih metadata ini setelah aplikasi dimuat. --}}
    <meta data-seo-server property="og:type" content="{{ $seoOgType }}">
    <meta data-seo-server property="og:site_name" content="{{ $siteName }}">
    <meta data-seo-server property="og:locale" content="id_ID">
    <meta data-seo-server property="og:url" content="{{ $seoCanonical }}">
    <meta data-seo-server property="og:title" content="{{ $seoTitle }}">
    <meta data-seo-server property="og:description" content="{{ $seoDescription }}">
    <meta data-seo-server property="og:image" content="{{ $seoOgImage }}">
    <meta data-seo-server property="og:image:alt" content="{{ $seoTitle }}">

    <meta data-seo-server name="twitter:card" content="summary_large_image">
    <meta data-seo-server name="twitter:url" content="{{ $seoCanonical }}">
    <meta data-seo-server name="twitter:title" content="{{ $seoTitle }}">
    <meta data-seo-server name="twitter:description" content="{{ $seoDescription }}">
    <meta data-seo-server name="twitter:image" content="{{ $seoOgImage }}">

    @foreach ($pageProps['structuredData'] ?? [] as $schema)
        <script data-seo-server type="application/ld+json" nonce="{{ csp_nonce() }}">{!! json_encode($schema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) !!}</script>
    @endforeach

    <link rel="icon" type="image/x-icon" href="{{ !empty($siteFavicon) ? asset('storage/' . $siteFavicon) : asset('icon/logo_ryaze.ico') }}">

    @if($gaId)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}" nonce="{{ csp_nonce() }}"></script>
        <script nonce="{{ csp_nonce() }}">
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body class="bg-mesh font-sans antialiased text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    @inertia
</body>

</html>
