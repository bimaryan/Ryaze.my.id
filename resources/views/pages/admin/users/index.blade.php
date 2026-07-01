@extends('index')

@section('content')
    <x-ui.page-layout>
        <div
            class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-users text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Manajemen Pengguna</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Daftar semua klien dan admin di dalam sistem.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mt-6">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-800">Daftar Semua Klien</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
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
                                    class="inline-block text-xs border border-indigo-200 text-indigo-700 bg-indigo-50 px-4 py-2 rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-200 font-semibold shadow-sm">
                                    Detail Profil
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
                        </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </x-ui.page-layout>
@endsection
