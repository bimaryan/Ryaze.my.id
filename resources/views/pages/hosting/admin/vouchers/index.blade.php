@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header title="Kelola Voucher" icon="ticket" iconColor="indigo">
            <x-slot:actions>
                <a href="{{ route('admin_hosting.vouchers.create') }}" class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah Voucher
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-4">Kode Voucher</th>
                <th class="px-6 py-4">Diskon</th>
                <th class="px-6 py-4">Penggunaan</th>
                <th class="px-6 py-4">Berlaku Sampai</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </x-slot:head>
            
            @forelse ($vouchers as $voucher)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800 font-mono">{{ $voucher->code }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if ($voucher->discount_percentage)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                Diskon {{ $voucher->discount_percentage }}%
                            </span>
                        @elseif ($voucher->discount_amount)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                Potongan Rp{{ number_format($voucher->discount_amount, 0, ',', '.') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $voucher->uses }} / {{ $voucher->max_uses ?? 'Tak Terbatas' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $voucher->expires_at ? $voucher->expires_at->format('d M Y, H:i') : 'Selamanya' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if ($voucher->isValid())
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">AKTIF</span>
                        @else
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-rose-100 text-rose-700">TIDAK AKTIF</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin_hosting.vouchers.edit', $voucher->hashid) }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white transition-all shadow-sm tooltip" title="Edit Voucher">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin_hosting.vouchers.destroy', $voucher->hashid) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); let f = this; Swal.fire({title: 'Hapus Voucher?', text: 'Apakah Anda yakin ingin menghapus voucher ini?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal', customClass: {popup: 'rounded-2xl text-sm'}}).then(res => { if(res.isConfirmed) f.submit(); }); return false;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-rose-600 bg-rose-50 hover:bg-rose-600 hover:text-white transition-all shadow-sm tooltip" title="Hapus Voucher">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada voucher yang dibuat.</td>
                </tr>
            @endforelse
        </x-ui.table>
        
        <div class="mt-4">
            {{ $vouchers->links() }}
        </div>
    </x-ui.page-layout>
@endsection
