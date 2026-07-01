@extends('index')

@section('content')
    <x-ui.page-layout>
{{-- Header --}}
        <div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Kelola Tagihan Hosting</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Verifikasi pembayaran dan pantau seluruh tagihan hosting pengguna.</p>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <x-ui.table>
    <x-slot:head>
                            <th class="px-6 py-4">Invoice / Tanggal</th>
                            <th class="px-6 py-4">Project / Klien</th>
                            <th class="px-6 py-4">Metode Pembayaran</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                            </x-slot:head>
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $payment->invoice_number }}</div>
                                    <div class="text-xs text-slate-500 mt-1 font-mono">
                                        {{ $payment->created_at->format('d M Y, H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-800">{{ $payment->project->project_name ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $payment->project->client->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 uppercase text-xs font-semibold text-slate-500">
                                    {{ $payment->payment_method ?? 'BELUM DIPILIH' }}
                                </td>
                                <td class="px-6 py-4 font-mono font-medium">Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClass = match ($payment->status) {
                                            'paid' => 'bg-emerald-100 text-emerald-700',
                                            'unpaid' => 'bg-amber-100 text-amber-700',
                                            'failed' => 'bg-rose-100 text-rose-700',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                        $statusLabel = strtoupper($payment->status);
                                    @endphp
                                    <span class="text-xs font-bold px-2 py-1 rounded-full {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" 
                                            class="btn-open-verify px-3 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg transition"
                                            data-hashid="{{ $payment->hashid }}"
                                            data-invoice="{{ $payment->invoice_number }}"
                                            data-status="{{ $payment->status }}">
                                        Verifikasi
                                    </button>
                                        Update Status
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada riwayat tagihan.</td>
                            </tr>
                        @endforelse
                    </x-ui.table>

    </div>

    {{-- Modal Verify --}}
    <div id="verifyModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden scale-95 transition-transform duration-300 transform">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800">Update Status Tagihan</h3>
                <button type="button" class="btn-close-verify text-slate-400 hover:text-rose-500 transition-colors p-2 rounded-lg hover:bg-rose-50">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="verifyForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6">
                    <p class="text-sm text-slate-500 mb-4">Ubah status untuk tagihan <strong id="verify_invoice" class="text-slate-800"></strong>:</p>
                    
                    <select name="status" id="verify_status" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-shadow">
                        <option value="unpaid">UNPAID (Belum Lunas)</option>
                        <option value="paid">PAID (Lunas)</option>
                        <option value="failed">FAILED (Gagal/Dibatalkan)</option>
                    </select>
                </div>
                <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                    <button type="button" class="btn-close-verify px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition-all">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script nonce="{{ app('csp_nonce') ?? '' }}">
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('verifyModal');
            const inner = modal.querySelector('div.bg-white');
            const form = document.getElementById('verifyForm');

            document.querySelectorAll('.btn-open-verify').forEach(btn => {
                btn.addEventListener('click', function() {
                    const hashid = this.getAttribute('data-hashid');
                    const invoice = this.getAttribute('data-invoice');
                    const status = this.getAttribute('data-status');
                    
                    form.action = `/admin/hosting/billing/${hashid}/verify`;
                    const verifyInvoice = document.getElementById('verify_invoice');
                    if(verifyInvoice) verifyInvoice.innerText = invoice;
                    const verifyStatus = document.getElementById('verify_status');
                    if(verifyStatus) verifyStatus.value = status;
                    
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        modal.classList.remove('opacity-0');
                        inner.classList.remove('scale-95');
                    }, 10);
                });
            });

            const closeVerifyModal = () => {
                modal.classList.add('opacity-0');
                inner.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            };

            document.querySelectorAll('.btn-close-verify').forEach(btn => {
                btn.addEventListener('click', closeVerifyModal);
            });
            
            modal.addEventListener('click', (e) => {
                if(e.target === modal) closeVerifyModal();
            });
            
            inner.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        });
    </script>
@endsection
