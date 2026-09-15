<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Ryaze' }}</title>
    <meta name="description" content="{{ $description ?? '' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ !empty($favicon) ? asset('storage/' . $favicon) : asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="bg-white font-sans antialiased text-slate-900">
    @inertia
</body>
</html>
