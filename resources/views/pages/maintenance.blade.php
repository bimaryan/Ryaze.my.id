<x-public-layout
    title="Sistem Dalam Pemeliharaan"
    :with-nav="false"
    :with-footer="false"
    body-class="bg-slate-50 dark:bg-slate-900 font-sans antialiased text-slate-900 dark:text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-lg w-full text-center">
        <!-- Icon Animation -->
        <div class="mb-8 relative inline-block">
            <div class="w-24 h-24 bg-indigo-100 dark:bg-indigo-500/20 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                <i class="fa-solid fa-person-digging text-4xl text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <!-- decorative gears -->
            <i class="fa-solid fa-gear text-indigo-400 absolute top-0 -right-4 animate-[spin_4s_linear_infinite]"></i>
            <i class="fa-solid fa-gear text-slate-300 dark:text-slate-400 text-sm absolute bottom-4 -left-2 animate-[spin_3s_linear_infinite_reverse]"></i>
        </div>

        <h1 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 mb-4 tracking-tight">Sistem Sedang Diperbarui</h1>
        
        <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">
            Ryaze sedang melakukan pemeliharaan rutin dan pembaruan sistem untuk memberikan layanan yang lebih baik. Kami akan segera kembali!
        </p>

        <div class="p-4 bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm text-sm text-slate-600 dark:text-slate-300 text-left flex items-start gap-3">
            <i class="fa-solid fa-circle-info text-indigo-500 dark:text-indigo-400 mt-1"></i>
            <div>
                <strong class="text-slate-700 dark:text-slate-200 block mb-1">Apa yang terjadi?</strong>
                Fitur klien sementara ditangguhkan selama proses update berlangsung. Administrator kami sedang bekerja keras menyelesaikannya secepat mungkin. Terima kasih atas kesabaran Anda.
            </div>
        </div>

        <div class="mt-8 text-xs text-slate-400 dark:text-slate-500">
            &copy; {{ date('Y') }} Ryaze Portal. All rights reserved.
        </div>
    </div>
</x-public-layout>
