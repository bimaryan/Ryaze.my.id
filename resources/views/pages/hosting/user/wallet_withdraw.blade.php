@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Tarik Saldo" 
            eyebrow="Penarikan Dana"
            subtitle="Tarik saldo Wallet Anda ke rekening bank atau e-wallet." 
            icon="fa-money-bill-transfer" 
            iconColor="indigo">
            <x-slot:actions>
                <a href="{{ route('user.wallet.history') }}"
                    class="ui-btn ui-btn-secondary px-5 py-2.5 text-sm font-bold">
                    &larr; Kembali ke Wallet
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1 space-y-6">
                {{-- Navy CTA band --}}
                <div class="bg-[#12233A] border border-[#1D5B96] p-6 text-white relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 opacity-10">
                        <i class="fa-solid fa-wallet text-9xl"></i>
                    </div>
                    <div class="relative z-10">
                        <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#7FB4E2] block mb-2">Saldo Tersedia</span>
                        <div class="text-4xl font-black tracking-tight">
                            Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- Ringkasan / ketentuan --}}
                <div class="ui-panel">
                    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                        <h2 class="font-mono text-[11px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">Ringkasan Penarikan</h2>
                    </div>
                    <dl>
                        <div class="ui-spec-row">
                            <dt class="ui-eyebrow-muted">Saldo Tersedia</dt>
                            <dd class="font-mono text-sm font-bold text-[#0F1A2A] dark:text-white">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</dd>
                        </div>
                        <div class="ui-spec-row">
                            <dt class="ui-eyebrow-muted">Minimal Penarikan</dt>
                            <dd class="font-mono text-sm font-bold text-[#0F1A2A] dark:text-white">Rp 50.000</dd>
                        </div>
                        <div class="ui-spec-row">
                            <dt class="ui-eyebrow-muted">Estimasi Proses</dt>
                            <dd class="font-mono text-sm font-bold text-[#0F1A2A] dark:text-white">Maksimal 2x24 Jam Kerja</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="ui-panel h-full">
                    <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                        <h2 class="font-mono text-[11px] font-semibold uppercase tracking-[0.2em] text-[#0F1A2A] dark:text-white">Formulir Penarikan</h2>
                        <span class="ui-eyebrow-muted">Nominal &amp; Rekening Tujuan</span>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('user.wallet.withdraw.process') }}" method="POST" class="space-y-5">
                            @csrf
                            
                            <div>
                                <label class="block text-sm font-bold text-[#0F1A2A] dark:text-white/85 mb-1.5">Nominal Penarikan <span class="text-rose-500 dark:text-rose-400">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-[#525252] dark:text-[#8A9BAE] font-bold">Rp</span>
                                    </div>
                                    <input type="number" name="amount" min="50000" max="{{ $wallet->balance }}" value="{{ old('amount', 50000) }}" required
                                        class="w-full bg-[#F4F8FC] dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] pl-12 pr-4 py-3 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] outline-none transition font-mono text-lg font-bold">
                                </div>
                                @error('amount')
                                    <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-[#0F1A2A] dark:text-white/85 mb-1.5">Bank / E-Wallet <span class="text-rose-500 dark:text-rose-400">*</span></label>
                                    <input type="text" name="bank_name" placeholder="BCA / GoPay / OVO" value="{{ old('bank_name') }}" required
                                        class="w-full bg-[#F4F8FC] dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] px-4 py-3 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] outline-none transition">
                                    @error('bank_name')
                                        <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-[#0F1A2A] dark:text-white/85 mb-1.5">Nomor Rekening / HP <span class="text-rose-500 dark:text-rose-400">*</span></label>
                                    <input type="text" name="account_number" placeholder="0812xxxxxx / 8371xxxxx" value="{{ old('account_number') }}" required
                                        class="w-full bg-[#F4F8FC] dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] px-4 py-3 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] outline-none transition">
                                    @error('account_number')
                                        <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#0F1A2A] dark:text-white/85 mb-1.5">Nama Pemilik Rekening <span class="text-rose-500 dark:text-rose-400">*</span></label>
                                <input type="text" name="account_name" placeholder="Atas Nama" value="{{ old('account_name') }}" required
                                    class="w-full bg-[#F4F8FC] dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] px-4 py-3 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] outline-none transition">
                                @error('account_name')
                                    <p class="text-rose-500 dark:text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="pt-4 border-t border-[#EDF2F7] dark:border-[#2A4463]">
                                <button type="submit" class="ui-btn ui-btn-primary w-full">
                                    <i class="fa-solid fa-paper-plane mr-2"></i> Ajukan Penarikan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.page-layout>
@endsection
