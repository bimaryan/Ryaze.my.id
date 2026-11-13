@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- ── 11. USER HOSTING – Storage Overview ────────────────────────── --}}
        <x-ui.page-header 
            title="Storage" 
            eyebrow="Alokasi Disk"
            status="ONLINE"
            subtitle="Monitor penggunaan disk seluruh project Anda." 
            icon="fa-hard-drive" 
            iconColor="emerald">
            <x-slot:actions>
                <a href="{{ route('user_hosting.dashboard') }}"
                    class="ui-btn ui-btn-secondary px-5 py-2.5 text-sm font-medium">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Total Usage Card --}}
        @php
            $barColor = $percent >= 90 ? 'bg-rose-500' : ($percent >= 70 ? 'bg-amber-500' : 'bg-[#1D5B96] dark:bg-[#6BA8DC]');
            $textColor = $percent >= 90 ? 'text-rose-600 dark:text-rose-300' : ($percent >= 70 ? 'text-amber-600 dark:text-amber-300' : 'text-[#1D5B96] dark:text-[#6BA8DC]');
            $bgLight =
                $percent >= 90
                    ? 'bg-rose-50 dark:bg-rose-500/10 border-rose-200 dark:border-rose-500/40'
                    : ($percent >= 70
                        ? 'bg-amber-50 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/40'
                        : 'bg-[#E8F0F9] dark:bg-[#1D5B96]/10 border-[#C9D5E2] dark:border-[#1D5B96]/40');
        @endphp

        @php
            $upgradeInvoice = \App\Models\HostingPayment::where('user_id', Auth::id())
                ->where('invoice_number', 'like', 'HST-UPG-%')
                ->where('status', 'unpaid')
                ->where('created_at', '>=', now()->subHours(24))
                ->first();
        @endphp

        @if ($upgradeInvoice && Auth::user()->role !== 'superadmin')
            <div class="mt-6 bg-[#E8F0F9] dark:bg-[#1D5B96]/10 border border-[#C9D5E2] dark:border-[#1D5B96]/40 px-5 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#1D5B96]/40 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-hard-drive text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                    </div>
                    <div>
                        <p class="font-bold text-[#0F1A2A] dark:text-[#6BA8DC] text-sm">Menunggu Pembayaran Upgrade Storage</p>
                        <p class="text-[#1D5B96] dark:text-[#6BA8DC] text-xs mt-0.5">Selesaikan tagihan Anda sebesar Rp {{ number_format($upgradeInvoice->amount, 0, ',', '.') }} untuk memperbesar total kapasitas akun Anda.</p>
                    </div>
                </div>
                <a href="{{ route('payment.doku', ['order_id' => $upgradeInvoice->invoice_number]) }}"
                    class="ui-btn ui-btn-secondary shrink-0 w-full sm:w-auto">
                    <i class="fa-solid fa-credit-card"></i> Bayar via DOKU
                </a>
                <a href="{{ route('payment.qris', ['order_id' => $upgradeInvoice->invoice_number]) }}"
                    class="ui-btn ui-btn-primary shrink-0 w-full sm:w-auto">
                    <i class="fa-solid fa-qrcode"></i> Bayar via QRIS
                </a>
            </div>
        @endif

        <div class="ui-panel mt-6 overflow-hidden">
            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                <h2 class="font-mono text-[11px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">Total Penggunaan</h2>
                <span class="font-mono text-xs font-bold border {{ $bgLight }} {{ $textColor }} px-3 py-1">{{ $percent }}% terpakai</span>
            </div>
            <div class="p-6">
                <div class="flex items-baseline gap-2 mb-4">
                    <span class="text-3xl font-black text-[#0F1A2A] dark:text-white tracking-tight">{{ $total_human }}</span>
                    <span class="text-[#8A9BAE] dark:text-white/45 text-sm">/ {{ $limit_human }}</span>
                </div>

                {{-- Progress bar --}}
                <div class="w-full bg-[#F0F6FC] dark:bg-[#0F1D30] h-1.5 overflow-hidden">
                    <div class="{{ $barColor }} h-1.5 transition-all duration-700"
                        style="width: {{ $percent }}%"></div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-[#8A9BAE] dark:text-white/45">
                    <span>{{ $total_human }} digunakan</span>
                    <span>{{ $limit_human }} batas</span>
                </div>

                @if ($percent >= 90)
                    <div class="mt-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/40 px-4 py-3 flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-rose-500 dark:text-rose-400 mt-0.5"></i>
                        <p class="text-rose-700 dark:text-rose-300 text-sm">Storage hampir penuh! Hapus file yang tidak diperlukan atau hubungi
                            admin untuk upgrade kapasitas.</p>
                    </div>
                @elseif($percent >= 70)
                    <div class="mt-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/40 px-4 py-3 flex items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation text-amber-500 dark:text-amber-400 mt-0.5"></i>
                        <p class="text-amber-700 dark:text-amber-300 text-sm">Penggunaan storage melebihi 70%. Pertimbangkan untuk membersihkan file
                            yang tidak diperlukan.</p>
                    </div>
                @endif

                @if (Auth::user()->role !== 'superadmin' && (Auth::user()->hosting_storage_limit_mb ?? 1024) < 3072 && !$upgradeInvoice)
                    <form action="{{ route('user_hosting.storage.upgrade') }}" method="POST" class="mt-6 pt-5 border-t border-[#EDF2F7] dark:border-[#2A4463]">
                        @csrf
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex-1 w-full max-w-sm">
                                <p class="font-bold text-[#0F1A2A] dark:text-white text-sm">Butuh lebih banyak kapasitas?</p>
                                <p class="text-xs text-[#525252] dark:text-[#8A9BAE] mt-0.5 mb-2">Upgrade storage Anda 1GB tambahan hanya dengan Rp 15.000.</p>
                                <input type="text" name="voucher_code" placeholder="Kode Voucher (Opsional)" class="w-full px-3 py-2 border border-[#E3EAF1] dark:border-[#2A4463] text-sm focus:outline-none focus:border-[#1D5B96] focus:ring-1 focus:ring-[#1D5B96] transition-colors">
                            </div>
                            <button type="submit" class="ui-btn ui-btn-secondary w-full shrink-0 sm:w-auto">
                                <i class="fa-solid fa-arrow-up-right-dots"></i> Upgrade 1GB (Rp 15.000)
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- Per-project breakdown --}}
        <div class="bg-white dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] overflow-hidden mt-6">
            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                <h2 class="font-mono text-[11px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">Penggunaan Per Project</h2>
                <span class="ui-eyebrow-muted">{{ $items->total() }} project</span>
            </div>

            @if ($items->isEmpty())
                <div class="px-6 py-16 text-center">
                    <i class="fa-solid fa-box-open text-[#C9D5E2] text-5xl mb-4"></i>
                    <p class="text-[#8A9BAE] dark:text-white/45 font-medium">Belum ada project yang di-deploy.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-px bg-[#E3EAF1] dark:bg-[#2A4463] border-t border-[#E3EAF1] dark:border-[#2A4463]">
                    @foreach ($items as $item)
                        @php
                            $pc = $item['percent'];
                            $bc = $pc >= 90 ? 'bg-rose-500' : ($pc >= 70 ? 'bg-amber-500' : 'bg-[#1D5B96] dark:bg-[#6BA8DC]');
                        @endphp
                        <div class="ui-cell p-5 sm:p-6 flex flex-col gap-4 transition-colors hover:bg-[#E8F0F9] dark:hover:bg-[#1A2F49]">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    {{-- Framework icon --}}
                                    <div
                                        class="w-10 h-10 bg-[#F4F8FC] dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] text-[#1D5B96] dark:text-[#6BA8DC] flex items-center justify-center shrink-0">
                                        <i class="{{ get_framework_icon($item['project']->framework) }}"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-[#0F1A2A] dark:text-white text-sm truncate">
                                            {{ $item['project']->project_name }}</p>
                                        <p class="text-xs text-[#8A9BAE] dark:text-white/45 font-mono truncate">{{ $item['dir'] }}</p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="font-bold text-[#0F1A2A] dark:text-white/85 text-sm">{{ $item['used_human'] }}</p>
                                    <p class="text-xs text-[#8A9BAE] dark:text-white/45 font-mono">{{ $pc }}%</p>
                                </div>
                            </div>
                            {{-- Mini progress bar --}}
                            <div class="w-full bg-[#F0F6FC] dark:bg-[#0F1D30] h-1 overflow-hidden">
                                <div class="{{ $bc }} h-1 transition-all duration-500"
                                    style="width: {{ $pc }}%"></div>
                            </div>
                            <div class="flex items-center justify-between mt-auto">
                                <span class="text-xs font-mono uppercase tracking-wider text-[#8A9BAE] dark:text-white/45">
                                    <i
                                        class="fa-solid fa-circle text-[8px] mr-1
                            {{ $item['project']->status == 'active' ? 'text-emerald-400 dark:text-emerald-300' : ($item['project']->status == 'building' ? 'text-amber-400 dark:text-amber-300' : 'text-rose-400 dark:text-rose-300') }}"></i>
                                    {{ $item['project']->status }}
                                </span>
                                <a href="{{ route('user_hosting.storage.show', $item['project']->hashid) }}"
                                   class="ui-arrow-link">
                                    Detail <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($items->hasPages())
                    <div class="px-6 py-4 border-t border-[#EDF2F7] dark:border-[#2A4463]">
                        {{ $items->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- Info --}}
        <p class="text-xs text-[#8A9BAE] dark:text-white/45 mt-4 flex items-center gap-1.5">
            <i class="fa-solid fa-circle-info text-[#C9D5E2] dark:text-[#8A9BAE]"></i>
            Limit storage gabungan: <strong>{{ $limit_human }}</strong>. Data diperbarui setiap kali halaman dimuat.
        </p>

    </x-ui.page-layout>
@endsection
