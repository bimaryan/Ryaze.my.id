@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Tarik Saldo" 
            subtitle="Tarik saldo Wallet Anda ke rekening bank atau e-wallet." 
            icon="fa-money-bill-transfer" 
            iconColor="indigo">
            <x-slot:actions>
                <a href="{{ route('user.wallet.history') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-bold transition shadow-sm">
                    &larr; Kembali ke Wallet
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white shadow-md relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 opacity-10">
                        <i class="fa-solid fa-wallet text-9xl"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="text-sm font-medium text-indigo-100 mb-1">Saldo Tersedia</div>
                        <div class="text-4xl font-black mb-4">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</div>
                        <p class="text-xs text-indigo-200 leading-relaxed">
                            Minimal penarikan adalah Rp 50.000. Dana akan diproses dalam waktu maksimal 2x24 jam kerja.
                        </p>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <form action="{{ route('user.wallet.withdraw.process') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Nominal Penarikan <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-slate-500 font-bold">Rp</span>
                                </div>
                                <input type="number" name="amount" min="50000" max="{{ $wallet->balance }}" value="{{ old('amount', 50000) }}" required
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-12 pr-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition font-mono text-lg font-bold">
                            </div>
                            @error('amount')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Bank / E-Wallet <span class="text-rose-500">*</span></label>
                                <input type="text" name="bank_name" placeholder="BCA / GoPay / OVO" value="{{ old('bank_name') }}" required
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                @error('bank_name')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Nomor Rekening / HP <span class="text-rose-500">*</span></label>
                                <input type="text" name="account_number" placeholder="0812xxxxxx / 8371xxxxx" value="{{ old('account_number') }}" required
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                @error('account_number')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Pemilik Rekening <span class="text-rose-500">*</span></label>
                            <input type="text" name="account_name" placeholder="Atas Nama" value="{{ old('account_name') }}" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            @error('account_name')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-md hover:shadow-lg transition">
                                <i class="fa-solid fa-paper-plane mr-2"></i> Ajukan Penarikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-ui.page-layout>
@endsection
