<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @php
        $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'RYAZE PORTAL';
        $siteDescription = \App\Models\Setting::where('key', 'site_description')->value('value') ?? 'Platform Layanan Joki dan Web Hosting Profesional Terpercaya.';
        $siteFavicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
        $gaId = \App\Models\Setting::where('key', 'google_analytics_id')->value('value');
    @endphp

    <title>@hasSection('title')@yield('title') - {{ $siteName }}@else{{ $siteName }}@endif</title>
    
    <meta name="description" content="@hasSection('seo_description')@yield('seo_description')@else{{ $siteDescription }}@endif">
    
    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@hasSection('title')@yield('title') - {{ $siteName }}@else{{ $siteName }}@endif">
    <meta property="og:description" content="@hasSection('seo_description')@yield('seo_description')@else{{ $siteDescription }}@endif">
    @if($siteFavicon)<meta property="og:image" content="{{ asset('storage/' . $siteFavicon) }}">@endif

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@hasSection('title')@yield('title') - {{ $siteName }}@else{{ $siteName }}@endif">
    <meta property="twitter:description" content="@hasSection('seo_description')@yield('seo_description')@else{{ $siteDescription }}@endif">
    @if($siteFavicon)<meta property="twitter:image" content="{{ asset('storage/' . $siteFavicon) }}">@endif

    <link rel="canonical" href="{{ url()->current() }}">
    
    @if($siteFavicon)
        <link rel="icon" href="{{ asset('storage/' . $siteFavicon) }}">
    @endif

    @if($gaId)
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" nonce="{{ csp_nonce() }}"></script>
    <script nonce="{{ csp_nonce() }}">
        window.Swal = Swal.mixin({
            customClass: {
                popup: 'rounded-2xl shadow-xl border border-slate-100',
                title: 'text-xl font-bold text-slate-800',
                htmlContainer: 'text-sm text-slate-500'
            }
        });
    </script>
    <script src="https://kit.fontawesome.com/f74deb4653.js" crossorigin="anonymous" nonce="{{ csp_nonce() }}"></script>
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js" nonce="{{ csp_nonce() }}"></script>

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" nonce="{{ csp_nonce() }}"></script>
</head>

<body class="bg-mesh font-sans antialiased text-slate-900">
    @include('components.navbar')
    @yield('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js" nonce="{{ csp_nonce() }}"></script>
    @include('components.hot-toast')
    @stack('scripts')

    <!-- Global Smart AJAX Navigation -->
    <script nonce="{{ csp_nonce() }}">
        document.addEventListener('DOMContentLoaded', function () {
            // Find the global PJAX container
            const container = document.getElementById('pjax-container');
            if (!container) return; // Only run on pages that use the page-layout

            let currentUrl = window.location.href;
            const loadedScripts = new Set();

            function isSameOrigin(url) {
                try {
                    const urlObj = new URL(url);
                    return urlObj.origin === window.location.origin && !urlObj.pathname.startsWith('/storage/');
                } catch (err) {
                    return false;
                }
            }

            async function fetchAndUpdate(url) {
                container.style.opacity = '0.5';
                container.style.pointerEvents = 'none';

                try {
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    // Redirect (login/session expired), error (419/500), or maintenance
                    // page must do a full navigation, otherwise the user gets stuck.
                    // Same-origin redirects (trailing slash, https upgrade, canonical)
                    // are already followed by fetch itself, so only bail out when the
                    // final response ended up on another origin or failed.
                    if (!response.ok) {
                        window.location.href = url;
                        return;
                    }
                    if (response.redirected) {
                        try {
                            if (new URL(response.url).origin !== window.location.origin) {
                                window.location.href = url;
                                return;
                            }
                        } catch (err) {
                            window.location.href = url;
                            return;
                        }
                    }

                    const html = await response.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newContainer = doc.getElementById('pjax-container');

                    // Target page doesn't use PJAX (public/home/auth page) -> full navigation
                    if (!newContainer) {
                        window.location.href = url;
                        return;
                    }

                    // Update page title & meta description
                    const newTitle = doc.querySelector('title');
                    if (newTitle) document.title = newTitle.textContent;
                    const newDesc = doc.querySelector('meta[name="description"]');
                    if (newDesc && newDesc.getAttribute('content')) {
                        let desc = document.querySelector('meta[name="description"]');
                        if (!desc) {
                            desc = document.createElement('meta');
                            desc.setAttribute('name', 'description');
                            document.head.appendChild(desc);
                        }
                        desc.setAttribute('content', newDesc.getAttribute('content'));
                    }

                    // Swap container content
                    container.innerHTML = newContainer.innerHTML;

                    // Swap sidebar while preserving scroll position
                    const currentSidebar = document.getElementById('logo-sidebar');
                    const newSidebar = doc.getElementById('logo-sidebar');
                    if (currentSidebar && newSidebar) {
                        const scrollContainer = currentSidebar.querySelector('.overflow-y-auto');
                        const scrollPos = scrollContainer ? scrollContainer.scrollTop : 0;
                        currentSidebar.innerHTML = newSidebar.innerHTML;
                        const newScrollContainer = currentSidebar.querySelector('.overflow-y-auto');
                        if (newScrollContainer) newScrollContainer.scrollTop = scrollPos;
                    }

                    // Cleanup stale modal/drawer backdrops and body lock
                    document.querySelectorAll('[modal-backdrop], [drawer-backdrop]').forEach(el => el.remove());
                    document.body.classList.remove('overflow-hidden');

                    // Re-run scripts inside the container (page-level init code)
                    container.querySelectorAll('script').forEach(oldScript => {
                        // External scripts already loaded don't need to be re-downloaded
                        if (oldScript.src) {
                            if (loadedScripts.has(oldScript.src)) return;
                            if (document.querySelector('script[src="' + oldScript.src + '"]')) {
                                loadedScripts.add(oldScript.src);
                                return;
                            }
                            loadedScripts.add(oldScript.src);
                        }
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => {
                            newScript.setAttribute(attr.name, attr.value);
                        });
                        newScript.appendChild(document.createTextNode(oldScript.textContent));
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });

                    // Re-initialize Alpine components inside the new content
                    if (window.Alpine && typeof Alpine.initTree === 'function') {
                        container.querySelectorAll('[x-data]').forEach(root => {
                            try { Alpine.initTree(root); } catch (e) { console.error('Alpine init failed:', e); }
                        });
                    }

                    // Force Flowbite to re-initialize
                    document.querySelectorAll('[data-modal-target], [data-modal-toggle], [data-drawer-target], [data-drawer-toggle], [data-dropdown-toggle], [data-tooltip-target]').forEach(el => {
                        el.removeAttribute('data-modal-initialized');
                        el.removeAttribute('data-drawer-initialized');
                        el.removeAttribute('data-dropdown-initialized');
                        el.removeAttribute('data-tooltip-initialized');
                    });
                    if (typeof initFlowbite === 'function') {
                        initFlowbite();
                    }

                    // Scroll back to top
                    window.scrollTo(0, 0);

                    // Update the URL without reloading the page
                    if (url !== window.location.href) {
                        window.history.pushState({ path: url }, '', url);
                        currentUrl = url;
                    }

                    // Let page-level code react to PJAX navigation
                    document.dispatchEvent(new CustomEvent('pjax:end', { detail: { url: url } }));
                } catch (err) {
                    console.error('Error fetching data:', err);
                    window.location.href = url; // Fallback: full navigation
                } finally {
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';
                }
            }

            // Intercept Clicks on Links (Pagination, Filters)
            document.body.addEventListener('click', function (e) {
                const link = e.target.closest('a');
                if (link && link.href && !link.href.includes('#') && !link.hasAttribute('download') && link.target !== '_blank') {
                    if (isSameOrigin(link.href)) {
                        e.preventDefault();
                        fetchAndUpdate(link.href);
                    }
                }
            });

            // Intercept GET Forms (Search)
            document.body.addEventListener('submit', function (e) {
                const form = e.target.closest('form');
                if (form && form.method.toUpperCase() === 'GET' && form.action) {
                    if (isSameOrigin(form.action)) {
                        e.preventDefault();
                        const formData = new FormData(form);
                        const params = new URLSearchParams(formData);
                        const urlObj = new URL(form.action);
                        urlObj.search = params.toString();
                        fetchAndUpdate(urlObj.toString());
                    }
                }
            });

            // Handle Delete Buttons
            document.body.addEventListener('click', function (e) {
                const btn = e.target.closest('.delete-btn');
                if (btn) {
                    const form = btn.closest('.delete-form');
                    if (form) {
                        Swal.fire({
                            title: 'Apakah Anda yakin?',
                            text: 'Data yang dihapus tidak dapat dikembalikan!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#4f46e5',
                            cancelButtonColor: '#f43f5e',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    }
                }
            });

            // Handle browser Back/Forward buttons
            window.addEventListener('popstate', function (e) {
                if (window.location.href !== currentUrl) {
                    fetchAndUpdate(window.location.href);
                }
            });
        });
    </script>
</body>

</html>

