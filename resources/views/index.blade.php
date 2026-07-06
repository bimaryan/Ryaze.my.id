<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'RYAZE PORTAL' }}</title>
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
</head>

<body class="bg-mesh font-sans antialiased text-slate-900">
    @include('components.navbar')
    @yield('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js" nonce="{{ csp_nonce() }}"></script>
    @include('components.hot-toast')
    @stack('scripts')
</body>

</html>

