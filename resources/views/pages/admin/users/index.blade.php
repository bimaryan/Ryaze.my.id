@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Manajemen Pengguna" 
            subtitle="Daftar semua klien dan admin di dalam sistem." 
            icon="fa-solid fa-users" 
        />

        <div>
            <h2 class="text-lg font-bold text-slate-800 mb-4 px-1">Daftar Semua Klien</h2>
            <x-ui.table>
                <x-slot:head>
                    <th scope="col" class="px-6 py-4">Nama Pengguna</th>
                    <th scope="col" class="px-6 py-4">Email Address</th>
                    <th scope="col" class="px-6 py-4">Role / Tipe Akun</th>
                    <th scope="col" class="px-6 py-4">Tanggal Daftar</th>
                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                </x-slot:head>
                            @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-800 flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm uppercase shadow-sm border border-slate-200">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @if ($user->role == 'user_joki')
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-blue-50 text-blue-600 border border-blue-200">Jasa
                                        Joki Code</span>
                                @elseif($user->role == 'user_hosting')
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-emerald-50 text-emerald-600 border border-emerald-200">App
                                        Deployment</span>
                                @else
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ ucfirst(str_replace('_', ' ', $user->role ?? 'User')) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('superadmin.users.show', $user->hashid) }}"
                                    class="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white transition-all duration-200 shadow-sm tooltip"
                                    title="Detail Profil">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-users-slash text-3xl mb-3 text-slate-300"></i>
                                <p>Belum ada data pengguna.</p>
                            </td>
                        </tr>
                        @endforelse
                <x-slot:pagination>
                    @if ($users->hasPages())
                        {{ $users->links() }}
                    @endif
                </x-slot:pagination>
            </x-ui.table>
        </div>
    </x-ui.page-layout>
@endsection
