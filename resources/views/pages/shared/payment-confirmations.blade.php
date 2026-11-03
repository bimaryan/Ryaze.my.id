@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Konfirmasi Pembayaran QRIS"
            eyebrow="Antrean Verifikasi — {{ $label }}"
            icon="qrcode">
            <x-slot:actions>
                <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#8A9BAE] dark:text-white/40">
                    {{ $counts['pending'] ?? 0 }} menunggu
                </p>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="ui-panel mb-6">
            <div class="ui-panel-head">
                <div class="flex items-center gap-1 flex-wrap">
                    @foreach ([
                        'pending'  => 'Menunggu',
                        'verified' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ] as $key => $label)
                        <a href="{{ route($routeName, ['status' => $key]) }}"
                            class="px-3 py-1.5 text-xs font-medium transition {{ $status === $key ? 'bg-[#1D5B96] text-white' : 'text-[#737373] dark:text-white/40 hover:text-[#0F1A2A] dark:hover:text-white' }}">
                            {{ $label }}
                            <span class="font-mono text-[10px] opacity-70">({{ $counts[$key] ?? 0 }})</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <x-ui.table>
                <x-slot:head>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Invoice / Jenis</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Pengirim</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase">Nominal</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Catatan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Waktu</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase">Aksi</th>
                </x-slot:head>

                @forelse ($confirmations as $c)
                    <tr class="hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828] transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-mono text-xs font-semibold text-[#0F1A2A] dark:text-white">{{ $c->order_number }}</div>
                            <div class="mt-0.5 flex items-center gap-1.5 flex-wrap">
                                <span class="text-[11px] text-[#737373] dark:text-white/40">{{ $c->order_type }}</span>
                                @if ($group === \App\Models\PaymentConfirmation::GROUP_ALL)
                                    <span class="ui-badge ui-badge-{{ $c->adminRole() === 'admin_joki' ? 'accent' : 'primary' }}">
                                        {{ $c->adminRole() === 'admin_joki' ? 'Joki' : 'Hosting' }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-[#0F1A2A] dark:text-white">{{ $c->user?->name ?? 'Tamu' }}</div>
                            @if ($c->user?->email)
                                <div class="text-[11px] text-[#737373] dark:text-white/40">{{ $c->user->email }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-mono text-sm text-[#0F1A2A] dark:text-white">
                            Rp{{ number_format($c->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-xs text-[#525252] dark:text-white/60 max-w-[220px]">
                            {{ $c->note ?: '—' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-mono text-[11px] text-[#525252] dark:text-white/60">{{ $c->created_at->format('d M Y H:i') }}</div>
                            @if ($c->status !== 'pending' && $c->verified_at)
                                <div class="text-[10px] text-[#737373] dark:text-white/40 mt-0.5">
                                    oleh {{ $c->verifier?->name ?? 'Sistem' }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                @if ($c->status === 'pending')
                                    <form action="{{ route('payment_confirmations.verify', $c->hashid) }}" method="POST" class="inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="ui-icon-btn ui-icon-btn-success" title="Setujui & Lunasi">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('payment_confirmations.verify', $c->hashid) }}" method="POST" class="inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="ui-icon-btn ui-icon-btn-danger" title="Tolak Konfirmasi">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="{{ $c->statusBadgeClass() }}">
                                        {{ $c->status === 'verified' ? 'Disetujui' : 'Ditolak' }}
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-[#737373] dark:text-white/40">
                            <i class="fa-solid fa-inbox text-2xl mb-3 block text-[#C9D5E2] dark:text-white/20"></i>
                            Tidak ada konfirmasi berstatus {{ $status }} di antrean {{ $label }}.
                        </td>
                    </tr>
                @endforelse

                @if ($confirmations->hasPages())
                    <x-slot:pagination>
                        {{ $confirmations->links() }}
                    </x-slot:pagination>
                @endif
            </x-ui.table>
        </div>
    </x-ui.page-layout>
@endsection
