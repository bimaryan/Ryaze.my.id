<div
    class="p-4 bg-white rounded-lg shadow-md flex items-center flex items-center justify-between border border-slate-200">
    <p class="text-2xl font-semibold text-indigo-600">Dashboard</p>

    <div
        class="px-2 py-1 bg-slate-100 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 flex items-center gap-2">
        <i class="fa-regular fa-calendar text-slate-400"></i>
        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
    </div>
</div>
