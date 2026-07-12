<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'RYAZE PORTAL' }}</title>
    
    @php
        $siteDescription = \App\Models\Setting::where('key', 'site_description')->value('value');
        $siteFavicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
        $gaId = \App\Models\Setting::where('key', 'google_analytics_id')->value('value');
    @endphp

    @if($siteDescription)
        <meta name="description" content="{{ $siteDescription }}">
    @endif
    
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
            
            function fetchAndUpdate(url) {
                container.style.opacity = '0.5';
                container.style.pointerEvents = 'none';
                
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContainer = doc.getElementById('pjax-container');
                    
                    if (newContainer) {
                        container.innerHTML = newContainer.innerHTML;
                        
                        // Execute scripts inside the new container
                        const scripts = container.querySelectorAll('script');
                        scripts.forEach(oldScript => {
                            const newScript = document.createElement('script');
                            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                            newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                            oldScript.parentNode.replaceChild(newScript, oldScript);
                        });
                    }
                    
                    const currentSidebar = document.getElementById('logo-sidebar');
                    const newSidebar = doc.getElementById('logo-sidebar');
                    if (currentSidebar && newSidebar) {
                        // Find the scrollable container inside the sidebar
                        const scrollContainer = currentSidebar.querySelector('.overflow-y-auto');
                        let scrollPos = 0;
                        if (scrollContainer) {
                            scrollPos = scrollContainer.scrollTop;
                        }
                        
                        currentSidebar.innerHTML = newSidebar.innerHTML;
                        
                        // Restore scroll position
                        const newScrollContainer = currentSidebar.querySelector('.overflow-y-auto');
                        if (newScrollContainer) {
                            newScrollContainer.scrollTop = scrollPos;
                        }
                    }
                    
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';
                    
                    // Update the URL without reloading the page
                    if (url !== window.location.href) {
                        window.history.pushState({path: url}, '', url);
                        currentUrl = url;
                    }
                })
                .catch(err => {
                    console.error('Error fetching data:', err);
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';
                });
            }

            // Intercept Clicks on Links (Pagination, Filters)
            document.body.addEventListener('click', function (e) {
                const link = e.target.closest('a');
                if (link && link.href && !link.href.includes('#') && !link.hasAttribute('download') && link.target !== '_blank') {
                    try {
                        const urlObj = new URL(link.href);
                        // Intercept all same-origin links
                        if (urlObj.origin === window.location.origin && !urlObj.pathname.startsWith('/storage/')) {
                            e.preventDefault();
                            fetchAndUpdate(link.href);
                        }
                    } catch(err) {}
                }
            });

            // Intercept GET Forms (Search)
            document.body.addEventListener('submit', function (e) {
                const form = e.target.closest('form');
                if (form && form.method.toUpperCase() === 'GET' && form.action) {
                    try {
                        const urlObj = new URL(form.action);
                        if (urlObj.origin === window.location.origin) {
                            e.preventDefault();
                            const formData = new FormData(form);
                            const params = new URLSearchParams(formData);
                            urlObj.search = params.toString();
                            fetchAndUpdate(urlObj.toString());
                        }
                    } catch(err) {}
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

