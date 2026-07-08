@extends('index')

@section('content')
    <x-ui.page-layout>
        {{-- Header --}}
        <x-ui.page-header 
            title="Affiliate Dashboard" 
            subtitle="Pantau performa program afiliasi dan komisi Anda." 
            icon="fa-users" 
            iconColor="emerald">
            <x-slot:actions>
                <a href="{{ route('user.wallet.history') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-bold transition shadow-sm">
                    <i class="fa-solid fa-wallet mr-2"></i> Cek Wallet
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Statistik Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Total Referrals --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-500 mb-1">Total Undangan</div>
                    <div class="text-3xl font-black text-slate-800">{{ number_format($totalReferrals, 0, ',', '.') }} <span class="text-sm font-medium text-slate-400">Pengguna</span></div>
                </div>
            </div>

            {{-- Total Commission --}}
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl p-6 shadow-md flex items-center gap-4 text-white relative overflow-hidden">
                <div class="absolute right-0 top-0 opacity-10 p-2">
                    <i class="fa-solid fa-money-bill-trend-up text-7xl"></i>
                </div>
                <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-2xl relative z-10">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div class="relative z-10">
                    <div class="text-sm font-bold text-emerald-100 mb-1">Total Pendapatan Komisi</div>
                    <div class="text-3xl font-black">Rp {{ number_format($totalCommission, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        {{-- Referral Link Card --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm mb-6">
            <h3 class="font-bold text-slate-800 mb-2">Link Referral Anda</h3>
            <p class="text-sm text-slate-500 mb-4">Bagikan link ini ke teman Anda. Dapatkan komisi sebesar 10% setiap kali mereka melakukan transaksi pertama maupun tagihan bulanan!</p>
            
            <div class="flex items-center gap-3">
                <code class="bg-slate-50 border border-slate-200 px-4 py-3 rounded-xl text-indigo-600 flex-1 break-all select-all text-sm font-mono font-medium">
                    {{ url('/register?ref=' . ($user->referral_code ?? 'RYZ-'.$user->id)) }}
                </code>
                <button onclick="navigator.clipboard.writeText('{{ url('/register?ref=' . ($user->referral_code ?? 'RYZ-'.$user->id)) }}'); typeof swAlert !== 'undefined' ? swAlert('Berhasil', 'Link referral disalin ke clipboard!', 'success') : Swal.fire('Berhasil', 'Link disalin!', 'success')" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-3 rounded-xl transition shadow-sm flex-shrink-0 font-bold text-sm">
                    <i class="fa-regular fa-copy mr-1"></i> Copy
                </button>
            </div>
        </div>

        {{-- Commission History Table --}}
        <h3 class="font-bold text-slate-800 mb-4 text-lg">Riwayat Komisi</h3>
        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Tanggal</th>
                <th class="px-6 py-4">Pengguna (Ref)</th>
                <th class="px-6 py-4">Keterangan</th>
                <th class="px-6 py-4">Komisi</th>
                <th class="px-6 py-4 text-center">Status</th>
            </x-slot:head>
            @forelse ($commissions as $comm)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800">{{ $comm->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $comm->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-slate-700">
                        {{ $comm->referredUser->name ?? 'User Terhapus' }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-slate-600">
                        {{ $comm->description }}
                    </td>
                    <td class="px-6 py-4 font-mono font-bold text-emerald-600">
                        +Rp{{ number_format($comm->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusClass = match ($comm->status) {
                                'paid' => 'bg-emerald-100 text-emerald-700',
                                'pending' => 'bg-amber-100 text-amber-700',
                                'cancelled' => 'bg-rose-100 text-rose-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                            $statusLabel = strtoupper($comm->status);
                        @endphp
                        <span class="text-xs font-bold px-2 py-1 rounded-full {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-slate-400">Belum ada komisi yang masuk. Yuk mulai ajak teman!</td>
                </tr>
            @endforelse
            <x-slot:pagination>
                {{ $commissions->links() }}
            </x-slot:pagination>
        </x-ui.table>

    </x-ui.page-layout>
@endsection
