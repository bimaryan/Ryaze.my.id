<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Tailwind Starter - Tailwind CSS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#0ea5e9',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
        <div class="bg-gradient-to-br from-brand to-blue-600 p-8 text-center relative overflow-hidden">
            <div class="absolute inset-0 bg-white/10 opacity-50 pattern-dots"></div>
            <h1 class="text-3xl font-extrabold text-white mb-2 relative z-10">Tailwind CSS</h1>
            <p class="text-blue-100 font-medium relative z-10">Starter Template Siap Pakai!</p>
        </div>
        <div class="p-8">
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <div class="bg-green-100 p-2 rounded-full flex-shrink-0 text-green-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed">Tanpa build atau <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-800">npm install</code>. Langsung edit