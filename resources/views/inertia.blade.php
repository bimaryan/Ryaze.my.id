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
        $seoTitle = $seoTitle ?? $title ?? $siteName;
        $seoDescription = $seoDescription ?? $description ?? $siteDescription;
        $seoCanonical = $canonical ?? url()->current();
        $seoRobots = $robots ?? 'index, follow';
        $seoOgType = $ogType ?? 'website';
        $seoOgImage = $ogImage ?? asset('og-image.png');
        $siteFavicon = $favicon ?? \App\Models\Setting::where('key', 'site_favicon')->value('value');
        $gaId = \App\Models\Setting::where('key', 'google_analytics_id')->value('value');
    @endphp

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="{{ $seoRobots }}">
    <link rel="canonical" href="{{ $seoCanonical }}">

    {{-- Open Graph / Facebook / WhatsApp --}}
    <meta property="og:type" content="{{ $seoOgType }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:image" content="{{ $seoOgImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $seoTitle }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $seoCanonical }}">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoOgImage }}">

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
