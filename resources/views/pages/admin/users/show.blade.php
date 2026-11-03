@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header 
            title="Profil Klien: {{ $user->name }}" 
            subtitle="Lihat informasi detail dan riwayat pesanan klien." 
            icon="fa-solid fa-user">
            <x-slot:actions>
                @if($user->id !== auth()->id())
                    <button onclick="document.getElementById('modal-role-{{ $user->hashid }}').showModal()"
                        class="inline-flex justify-center items-center bg-[#1D5B96] hover:bg-[#2A6FAD] text-white px-5 py-2.5  text-sm font-medium transition  border border-transparent">
                        <i class="fa-solid fa-user-shield mr-2"></i> Edit Role
                    </button>
                    
                    <form action="{{ route('superadmin.users.status.toggle', $user->hashid) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" onclick="event.preventDefault(); Swal.fire({title: 'Konfirmasi', text: 'Apakah Anda yakin ingin {{ $user->status === 'active' ? 'menangguhkan' : 'mengaktifkan' }} akun ini?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#4f46e5', cancelButtonColor: '#ef4444', confirmButtonText: 'Ya, Lanjutkan', cancelButtonText: 'Batal'}).then((result) => { if (result.isConfirmed) { this.closest('form').submit(); } })"
                            class="inline-flex justify-center items-center px-5 py-2.5  text-sm font-medium transition  border {{ $user->status === 'active' ? 'bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-300 hover:bg-orange-100 dark:hover:bg-orange-500/20 border-orange-200 dark:border-orange-500/40' : 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 border-emerald-200 dark:border-emerald-500/40' }}">
                            <i class="fa-solid {{ $user->status === 'active' ? 'fa-ban' : 'fa-check' }} mr-2"></i> 
                            {{ $user->status === 'active' ? 'Suspend' : 'Unsuspend' }}
                        </button>
                    </form>

                    <form action="{{ route('superadmin.users.destroy', $user->hashid) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="event.preventDefault(); Swal.fire({title: 'Peringatan', text: 'Peringatan: Aksi ini akan menghapus akun user secara permanen. Lanjutkan?', icon: 'error', showCancelButton: true, confirmButtonColor: '#e11d48', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'}).then((result) => { if (result.isConfirmed) { this.closest('form').submit(); } })"
                            class="inline-flex justify-center items-center bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/40 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-rose-700 dark:text-rose-400 px-5 py-2.5  text-sm font-medium transition ">
                            <i class="fa-solid fa-trash mr-2"></i> Hapus
                        </button>
                    </form>
                @endif
                <a href="{{ route('superadmin.users.index') }}"
                    class="inline-flex justify-center items-center bg-white dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#2A4463] hover:bg-[#E8F0F9] dark:hover:bg-[#0D1828] text-[#0F1A2A] dark:text-white px-5 py-2.5  text-sm font-medium transition ">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>
        
        <!-- Modal Edit Role -->
        <dialog id="modal-role-{{ $user->hashid }}" class="m-auto backdrop:bg-black/50 p-6   max-w-md w-full border border-[#C9D5E2] dark:border-[#1B2C44] open:animate-in open:fade-in open:zoom-in-95">
            <form action="{{ route('superadmin.users.role.update', $user->hashid) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <h3 class="font-bold text-lg text-[#0F1A2A] dark:text-white mb-1">Edit Role Klien</h3>
                    <p class="text-sm text-[#737373] dark:text-white/40">Pilih hak akses baru untuk {{ $user->name }}</p>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-[#0F1A2A] dark:text-white mb-2">Role Saat Ini: {{ str_replace('_', ' ', $user->role) }}</label>
                    <select name="role" class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:border-[#1D5B96] outline-none transition">
                        <option value="user_hosting" {{ $user->role === 'user_hosting' ? 'selected' : '' }}>User Hosting</option>
                        <option value="user_joki" {{ $user->role === 'user_joki' ? 'selected' : '' }}>User Joki</option>
                        <option value="admin_hosting" {{ $user->role === 'admin_hosting' ? 'selected' : '' }}>Admin Hosting</option>
                        <option value="admin_joki" {{ $user->role === 'admin_joki' ? 'selected' : '' }}>Admin Joki</option>
                        <option value="superadmin" {{ $user->role === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    </select>
                </div>
                
                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="document.getElementById('modal-role-{{ $user->hashid }}').close()" class="px-4 py-2 text-[#525252] dark:text-white/60 hover:text-[#0F1A2A] dark:hover:text-white bg-white dark:bg-[#0D1828] hover:bg-[#E8F0F9] dark:hover:bg-[#12233A] border border-[#C9D5E2] dark:border-[#1B2C44]  font-medium transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#1D5B96] text-white  font-medium hover:bg-[#2A6FAD] ">Simpan Perubahan</button>
                </div>
            </form>
        </dialog>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

            <div class="lg:col-span-1 space-y-6">
                <x-ui.card class="p-6">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                        <div class="flex items-center gap-4 min-w-0">
                            <div
                                class="w-16 h-16 shrink-0 bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC] rounded-full flex items-center justify-center font-black text-3xl shadow-inner">
                                {{ substr($user->name, 0, 1) }}
                            </div>

                            <div class="min-w-0">
                                <h2 class="text-lg font-bold text-[#0F1A2A] dark:text-white leading-tight truncate">{{ $user->name }}</h2>
                                <p class="text-sm text-[#737373] dark:text-white/40 mt-0.5 truncate">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="shrink-0 self-start sm:self-auto">
                            <span
                                class="inline-block px-3 py-1 bg-[#F0F6FC] dark:bg-[#0D1828] text-[#0F1A2A] dark:text-white rounded-full text-[11px] tracking-wide font-bold uppercase border border-[#C9D5E2] dark:border-[#1B2C44]">
                                {{ str_replace('_', ' ', $user->role ?? 'User') }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-[#E3EAF1] dark:border-[#1B2C44] pt-5 text-left space-y-3 text-sm">
                        <div class="flex items-center text-[#525252] dark:text-white/60">
                            <i class="fa-solid fa-calendar-alt w-6 text-center text-[#94A3B4] dark:text-white/35"></i>
                            <span>Terdaftar: <strong class="text-[#0F1A2A] dark:text-white">{{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y') }}</strong></span>
                        </div>
                        <div class="flex items-center text-[#525252] dark:text-white/60">
                            <i class="fa-solid fa-clock w-6 text-center text-[#94A3B4] dark:text-white/35"></i>
                            <span>Waktu: <strong class="text-[#0F1A2A] dark:text-white">{{ \Carbon\Carbon::parse($user->created_at)->format('H:i') }} WIB</strong></span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 mt-5 border-t border-[#E3EAF1] dark:border-[#1B2C44] pt-5">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-[#737373] dark:text-white/40">Status Akun</span>
                            <span class="font-bold px-2 py-0.5 rounded-full text-[10px] uppercase tracking-wider {{ $user->status === 'active' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300' }}">
                                {{ $user->status }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-[#737373] dark:text-white/40">Paket Hosting</span>
                            @php
                                $activeBilling = $user->hostingBillings()->where('status', 'active')->where('next_due_date', '>', now())->latest()->first();
                                $currentPlan = $activeBilling ? $activeBilling->plan : 'free';
                                $plans = \App\Models\User::hostingPlans();
                                $planLabel = $plans[$currentPlan]['label'] ?? ucfirst($currentPlan);
                                $colorMap = [
                                    'slate'  => 'bg-[#F0F6FC] dark:bg-[#0D1828] text-[#0F1A2A] dark:text-white',
                                    'indigo' => 'bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC]',
                                    'violet' => 'bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC]',
                                    'amber'  => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300',
                                ];
                                $badgeClass = $colorMap[$plans[$currentPlan]['color'] ?? 'slate'] ?? $colorMap['slate'];
                            @endphp
                            <span class="font-bold px-2 py-0.5 rounded-full text-[10px] uppercase tracking-wider {{ $badgeClass }}">
                                {{ $planLabel }}
                            </span>
                        </div>
                        @if(strtolower($currentPlan) !== 'free')
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-[#737373] dark:text-white/40">Expired Hosting</span>
                            @if($activeBilling && $activeBilling->next_due_date)
                                <span class="font-semibold {{ \Carbon\Carbon::parse($activeBilling->next_due_date)->isPast() ? 'text-rose-500 dark:text-rose-400' : 'text-[#0F1A2A] dark:text-white' }}">
                                    {{ \Carbon\Carbon::parse($activeBilling->next_due_date)->translatedFormat('d F Y') }}
                                </span>
                            @else
                                <span class="text-[#94A3B4] dark:text-white/35 italic font-semibold">-</span>
                            @endif
                        </div>
                        @endif
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-[#737373] dark:text-white/40">Total Pesanan Joki</span>
                            <span class="font-semibold text-[#0F1A2A] dark:text-white">{{ $user->client_orders_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-[#737373] dark:text-white/40">Total Proyek Hosting</span>
                            <span class="font-semibold text-[#0F1A2A] dark:text-white">{{ $user->hosting_projects_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-[#737373] dark:text-white/40">Storage Hosting</span>
                            @php
                                $totalLimit = $user->hosting_storage_limit_mb ?? (\App\Models\User::getPlanLimits($currentPlan)['storage_mb'] ?? 256);
                            @endphp
                            <span class="font-semibold text-[#0F1A2A] dark:text-white">{{ number_format($totalLimit) }} MB</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-[#737373] dark:text-white/40">IP Terakhir</span>
                            <span class="font-mono text-xs text-[#525252] dark:text-white/60">{{ $user->last_login_ip ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-[#737373] dark:text-white/40">Aktivitas Terakhir</span>
                            <span class="font-semibold text-[#0F1A2A] dark:text-white">{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : '-' }}</span>
                        </div>
                    </div>

                </x-ui.card>
            </div>

            <div class="lg:col-span-2">
                <x-ui.card class="p-6">
                    <h3 class="font-bold text-[#0F1A2A] dark:text-white mb-4 border-b pb-2">Riwayat Pesanan Joki Klien Ini</h3>

                    @if (isset($jokiOrders) && $jokiOrders->count() > 0)
                        <div class="space-y-4">
                            @foreach ($jokiOrders as $order)
                                <div
                                    class="border border-[#C9D5E2] dark:border-[#1B2C44]  p-4 hover:bg-[#F4F8FC] dark:bg-[#0D1828] dark:hover:bg-[#0D1828] transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-bold text-[#0F1A2A] dark:text-white">{{ $order->project_name }}</h4>
                                            <span
                                                class="text-[10px] px-2 py-0.5 rounded font-bold uppercase
                                                {{ $order->status == 'completed' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : ($order->status == 'progress' ? 'bg-[#E8F0F9] dark:bg-[#1D5B96]/20 text-[#1D5B96] dark:text-[#6BA8DC]' : 'bg-[#F0F6FC] dark:bg-[#0D1828] text-[#525252] dark:text-white/60') }}">
                                                {{ $order->status }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-[#737373] dark:text-white/40">Order ID: {{ $order->order_number }} | Harga: Rp
                                            {{ number_format($order->price ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                    <a href="{{ route('admin_joki.orders.edit', $order->hashid) }}"
                                        class="inline-block text-xs border border-[#C9D5E2] dark:border-[#1D5B96]/40 text-[#1D5B96] dark:text-[#6BA8DC] bg-[#E8F0F9] dark:bg-[#1D5B96]/10 px-4 py-2  hover:bg-[#1D5B96] hover:text-white transition-all duration-200 font-semibold ">
                                        Kelola Proyek
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 bg-[#F4F8FC] dark:bg-[#0D1828] rounded-full flex items-center justify-center mx-auto mb-3 text-[#C9D5E2] dark:text-white/25 text-2xl">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <p class="text-sm font-medium text-[#737373] dark:text-white/40">Klien ini belum pernah membuat pesanan Joki.</p>
                        </div>
                    @endif
                </x-ui.card>

                <x-ui.card class="p-6 mt-6">
                    <h3 class="font-bold text-[#0F1A2A] dark:text-white mb-4 border-b pb-2">Riwayat Proyek Hosting Klien Ini</h3>

                    @if (isset($hostingProjects) && $hostingProjects->count() > 0)
                        <div class="space-y-4">
                            @foreach ($hostingProjects as $project)
                                <div class="border border-[#C9D5E2] dark:border-[#1B2C44]  p-4 hover:bg-[#F4F8FC] dark:bg-[#0D1828] dark:hover:bg-[#0D1828] transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-bold text-[#0F1A2A] dark:text-white">{{ $project->project_name }}</h4>
                                            <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase {{ $project->status === 'active' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : ($project->status === 'suspended' ? 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300' : 'bg-[#F0F6FC] dark:bg-[#0D1828] text-[#525252] dark:text-white/60') }}">
                                                {{ $project->status }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-[#737373] dark:text-white/40">
                                            <i class="fa-solid fa-code mr-1"></i> {{ ucfirst($project->framework) }}
                                        </p>
                                    </div>
                                    <a href="{{ route('admin_hosting.projects') }}"
                                        class="inline-block text-xs border border-[#C9D5E2] dark:border-[#1D5B96]/40 text-[#1D5B96] dark:text-[#6BA8DC] bg-[#E8F0F9] dark:bg-[#1D5B96]/10 px-4 py-2  hover:bg-[#1D5B96] hover:text-white transition-all duration-200 font-semibold ">
                                        Lihat di Hosting
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-[#F4F8FC] dark:bg-[#0D1828] rounded-full flex items-center justify-center mx-auto mb-3 text-[#C9D5E2] dark:text-white/25 text-2xl">
                                <i class="fa-solid fa-server"></i>
                            </div>
                            <p class="text-sm font-medium text-[#737373] dark:text-white/40">Klien ini belum memiliki proyek hosting.</p>
                        </div>
                    @endif
                </x-ui.card>
            </div>

        </div>
    </x-ui.page-layout>
@endsection
