@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Manajemen Pengguna" 
            subtitle="Daftar semua klien dan admin di dalam sistem." 
            icon="fa-solid fa-users" 
        />

        <div id="users-container" class="transition-opacity duration-300">
            <div class="mb-6 flex overflow-x-auto gap-2 pb-2 hide-scrollbar">
                <a href="{{ route('superadmin.users.index') }}" class="px-4 py-2  text-sm font-medium whitespace-nowrap transition {{ !request()->has('role') || request('role') == '' ? 'bg-[#1D5B96] text-white ' : 'bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] text-[#525252] dark:text-white/60 hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]' }}">Semua Pengguna</a>
                <a href="{{ route('superadmin.users.index', ['role' => 'user_joki']) }}" class="px-4 py-2  text-sm font-medium whitespace-nowrap transition {{ request('role') == 'user_joki' ? 'bg-[#1D5B96] text-white ' : 'bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] text-[#525252] dark:text-white/60 hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]' }}">Klien Joki</a>
                <a href="{{ route('superadmin.users.index', ['role' => 'user_hosting']) }}" class="px-4 py-2  text-sm font-medium whitespace-nowrap transition {{ request('role') == 'user_hosting' ? 'bg-emerald-600 text-white ' : 'bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] text-[#525252] dark:text-white/60 hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]' }}">Klien Hosting</a>
                <a href="{{ route('superadmin.users.index', ['role' => 'admin']) }}" class="px-4 py-2  text-sm font-medium whitespace-nowrap transition {{ request('role') == 'admin' ? 'bg-amber-600 text-white ' : 'bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] text-[#525252] dark:text-white/60 hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]' }}">Admin Joki/Hosting</a>
                <a href="{{ route('superadmin.users.index', ['role' => 'superadmin']) }}" class="px-4 py-2  text-sm font-medium whitespace-nowrap transition {{ request('role') == 'superadmin' ? 'bg-[#12233A] text-white ' : 'bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] text-[#525252] dark:text-white/60 hover:bg-[#F4F8FC] dark:hover:bg-[#0D1828]' }}">Superadmin</a>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center mb-4 px-1 gap-4">
                <form action="{{ route('superadmin.users.index') }}" method="GET" class="flex items-center w-full sm:w-auto">
                    @if(request()->has('role'))
                        <input type="hidden" name="role" value="{{ request('role') }}">
                    @endif
                    <select name="status" class="w-full sm:w-40 bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44]  px-3 py-2.5 text-sm text-[#0F1A2A] dark:text-white focus:ring-2 focus:border-[#1D5B96] outline-none transition">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <i class="fa-solid fa-search text-[#94A3B4] dark:text-white/35"></i>
                        </div>
                        <input type="text" name="search" class="text-[#0F1A2A] dark:text-white block ps-9 p-2 w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="p-2 ms-2 text-sm font-medium text-white bg-[#1D5B96]  hover:bg-[#2A6FAD]  transition">
                        Cari
                    </button>
                    @if(request()->has('search') && request()->search != '')
                        <a href="{{ route('superadmin.users.index', array_filter(['role' => request('role'), 'status' => request('status')])) }}" class="p-2 ms-2 text-sm font-medium text-[#525252] dark:text-white/60 bg-[#F0F6FC] dark:bg-[#0D1828]  hover:bg-[#E8F0F9] dark:hover:bg-[#1D5B96] transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
            <x-ui.table>
                <x-slot:head>
                    <th scope="col" class="px-6 py-4">Nama Pengguna</th>
                    <th scope="col" class="px-6 py-4">Email Address</th>
                    <th scope="col" class="px-6 py-4">Role / Tipe Akun</th>
                    <th scope="col" class="px-6 py-4">Expired Hosting</th>
                    <th scope="col" class="px-6 py-4">Tanggal Daftar</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                </x-slot:head>
                            @forelse($users as $user)
                        <tr class="hover:bg-[#F4F8FC] dark:bg-[#0D1828] dark:hover:bg-[#0D1828] transition-colors">
                            <td class="px-6 py-4 font-medium text-[#0F1A2A] dark:text-white flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-full bg-[#F0F6FC] dark:bg-[#0D1828] text-[#525252] dark:text-white/60 flex items-center justify-center font-bold text-sm uppercase  border border-[#C9D5E2] dark:border-[#1B2C44]">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @if ($user->role == 'user_joki')
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-[#E8F0F9] dark:bg-[#1D5B96]/10 text-[#1D5B96] dark:text-[#6BA8DC] border border-[#C9D5E2] dark:border-[#1D5B96]/40">Jasa
                                        Joki Code</span>
                                @elseif($user->role == 'user_hosting')
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/40">App
                                        Deployment</span>
                                @else
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-[#E8F0F9] dark:bg-[#1D5B96]/10 text-[#1D5B96] dark:text-[#6BA8DC] border border-[#C9D5E2] dark:border-[#1D5B96]/40 uppercase">{{ str_replace('_', ' ', $user->role) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if(in_array($user->role, ['user_hosting', 'superadmin', 'admin_hosting']))
                                    @php
                                        $activeBilling = $user->hostingBillings->first();
                                        $currentPlan = $activeBilling ? $activeBilling->plan : 'free';
                                    @endphp
                                    @if($activeBilling && $activeBilling->next_due_date && strtolower($currentPlan) !== 'free')
                                        <span class="text-xs font-medium {{ \Carbon\Carbon::parse($activeBilling->next_due_date)->isPast() ? 'text-rose-500 dark:text-rose-400' : 'text-emerald-500 dark:text-emerald-400' }}">
                                            {{ \Carbon\Carbon::parse($activeBilling->next_due_date)->translatedFormat('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-[#94A3B4] dark:text-white/35 text-xs italic">-</span>
                                    @endif
                                @else
                                    <span class="text-[#94A3B4] dark:text-white/35 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y, H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->status == 'active')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/40">Aktif</span>
                                @elseif ($user->status == 'suspended')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-500/40">Suspended</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-[#F0F6FC] dark:bg-[#0D1828] text-[#525252] dark:text-white/60 border border-[#C9D5E2] dark:border-[#1B2C44]">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('superadmin.users.show', $user->hashid) }}"
                                    class="ui-icon-btn ui-icon-btn-primary tooltip"
                                    title="Detail Profil">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-[#737373] dark:text-white/40">
                                <i class="fa-solid fa-users-slash text-3xl mb-3 text-[#C9D5E2] dark:text-white/25"></i>
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

