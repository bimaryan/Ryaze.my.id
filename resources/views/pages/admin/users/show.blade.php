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
                        class="inline-flex justify-center items-center bg-blue-50 text-blue-700 hover:bg-blue-100 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm border border-blue-200">
                        <i class="fa-solid fa-user-shield mr-2"></i> Edit Role
                    </button>
                    
                    <form action="{{ route('superadmin.users.status.toggle', $user->hashid) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" onclick="event.preventDefault(); Swal.fire({title: 'Konfirmasi', text: 'Apakah Anda yakin ingin {{ $user->status === 'active' ? 'menangguhkan' : 'mengaktifkan' }} akun ini?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#4f46e5', cancelButtonColor: '#ef4444', confirmButtonText: 'Ya, Lanjutkan', cancelButtonText: 'Batal'}).then((result) => { if (result.isConfirmed) { this.closest('form').submit(); } })"
                            class="inline-flex justify-center items-center px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm border {{ $user->status === 'active' ? 'bg-orange-50 text-orange-700 hover:bg-orange-100 border-orange-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-emerald-200' }}">
                            <i class="fa-solid {{ $user->status === 'active' ? 'fa-ban' : 'fa-check' }} mr-2"></i> 
                            {{ $user->status === 'active' ? 'Suspend' : 'Unsuspend' }}
                        </button>
                    </form>

                    <form action="{{ route('superadmin.users.destroy', $user->hashid) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="event.preventDefault(); Swal.fire({title: 'Peringatan', text: 'Peringatan: Aksi ini akan menghapus akun user secara permanen. Lanjutkan?', icon: 'error', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'}).then((result) => { if (result.isConfirmed) { this.closest('form').submit(); } })"
                            class="inline-flex justify-center items-center bg-red-50 border border-red-200 hover:bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                            <i class="fa-solid fa-trash mr-2"></i> Hapus
                        </button>
                    </form>
                @endif
                <a href="{{ route('superadmin.users.index') }}"
                    class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>
        
        <!-- Modal Edit Role -->
        <dialog id="modal-role-{{ $user->hashid }}" class="m-auto backdrop:bg-black/50 p-6 rounded-2xl shadow-2xl max-w-md w-full border border-slate-200 open:animate-in open:fade-in open:zoom-in-95">
            <form action="{{ route('superadmin.users.role.update', $user->hashid) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <h3 class="font-bold text-lg text-slate-800 mb-1">Edit Role Klien</h3>
                    <p class="text-sm text-slate-500">Pilih hak akses baru untuk {{ $user->name }}</p>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Role Saat Ini: {{ str_replace('_', ' ', $user->role) }}</label>
                    <select name="role" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                        <option value="user_hosting" {{ $user->role === 'user_hosting' ? 'selected' : '' }}>User Hosting</option>
                        <option value="user_joki" {{ $user->role === 'user_joki' ? 'selected' : '' }}>User Joki</option>
                        <option value="admin_hosting" {{ $user->role === 'admin_hosting' ? 'selected' : '' }}>Admin Hosting</option>
                        <option value="admin_joki" {{ $user->role === 'admin_joki' ? 'selected' : '' }}>Admin Joki</option>
                        <option value="superadmin" {{ $user->role === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    </select>
                </div>
                
                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="document.getElementById('modal-role-{{ $user->hashid }}').close()" class="px-4 py-2 bg-white text-slate-700 border border-slate-300 rounded-lg font-medium hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </dialog>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

            <div class="lg:col-span-1 space-y-6">
                <x-ui.card class="p-6">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                        <div class="flex items-center gap-4 min-w-0">
                            <div
                                class="w-16 h-16 shrink-0 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-black text-3xl shadow-inner">
                                {{ substr($user->name, 0, 1) }}
                            </div>

                            <div class="min-w-0">
                                <h2 class="text-lg font-bold text-slate-800 leading-tight truncate">{{ $user->name }}</h2>
                                <p class="text-sm text-slate-500 mt-0.5 truncate">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="shrink-0 self-start sm:self-auto">
                            <span
                                class="inline-block px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-[11px] tracking-wide font-bold uppercase border border-slate-200">
                                {{ str_replace('_', ' ', $user->role ?? 'User') }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-5 text-left space-y-3 text-sm">
                        <div class="flex items-center text-slate-600">
                            <i class="fa-solid fa-calendar-alt w-6 text-center text-slate-400"></i>
                            <span>Terdaftar: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y') }}</strong></span>
                        </div>
                        <div class="flex items-center text-slate-600">
                            <i class="fa-solid fa-clock w-6 text-center text-slate-400"></i>
                            <span>Waktu: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($user->created_at)->format('H:i') }} WIB</strong></span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 mt-5 border-t border-slate-100 pt-5">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Status Akun</span>
                            <span class="font-bold px-2 py-0.5 rounded-full text-[10px] uppercase tracking-wider {{ $user->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                {{ $user->status }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Total Pesanan Joki</span>
                            <span class="font-semibold text-slate-800">{{ $user->client_orders_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Total Proyek Hosting</span>
                            <span class="font-semibold text-slate-800">{{ $user->hosting_projects_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Storage Hosting</span>
                            <span class="font-semibold text-slate-800">{{ number_format($user->hosting_storage_limit_mb ?? 500) }} MB</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">IP Terakhir</span>
                            <span class="font-mono text-xs text-slate-600">{{ $user->last_login_ip ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Aktivitas Terakhir</span>
                            <span class="font-semibold text-slate-800">{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : '-' }}</span>
                        </div>
                    </div>

                </x-ui.card>
            </div>

            <div class="lg:col-span-2">
                <x-ui.card class="p-6">
                    <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Riwayat Pesanan Joki Klien Ini</h3>

                    @if (isset($jokiOrders) && $jokiOrders->count() > 0)
                        <div class="space-y-4">
                            @foreach ($jokiOrders as $order)
                                <div
                                    class="border border-slate-200 rounded-xl p-4 hover:bg-slate-50 transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-bold text-slate-800">{{ $order->project_name }}</h4>
                                            <span
                                                class="text-[10px] px-2 py-0.5 rounded font-bold uppercase
                                                {{ $order->status == 'completed' ? 'bg-emerald-100 text-emerald-700' : ($order->status == 'progress' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600') }}">
                                                {{ $order->status }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500">Order ID: {{ $order->order_number }} | Harga: Rp
                                            {{ number_format($order->price ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                    <a href="{{ route('admin_joki.orders.edit', $order->hashid) }}"
                                        class="inline-block text-xs border border-indigo-200 text-indigo-700 bg-indigo-50 px-4 py-2 rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-200 font-semibold shadow-sm">
                                        Kelola Proyek
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 text-2xl">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-500">Klien ini belum pernah membuat pesanan Joki.</p>
                        </div>
                    @endif
                </x-ui.card>

                <x-ui.card class="p-6 mt-6">
                    <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Riwayat Proyek Hosting Klien Ini</h3>

                    @if (isset($hostingProjects) && $hostingProjects->count() > 0)
                        <div class="space-y-4">
                            @foreach ($hostingProjects as $project)
                                <div class="border border-slate-200 rounded-xl p-4 hover:bg-slate-50 transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-bold text-slate-800">{{ $project->project_name }}</h4>
                                            <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase {{ $project->status === 'active' ? 'bg-emerald-100 text-emerald-700' : ($project->status === 'suspended' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600') }}">
                                                {{ $project->status }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500">
                                            <i class="fa-solid fa-code mr-1"></i> {{ ucfirst($project->framework) }}
                                        </p>
                                    </div>
                                    <a href="{{ route('admin_hosting.projects') }}"
                                        class="inline-block text-xs border border-indigo-200 text-indigo-700 bg-indigo-50 px-4 py-2 rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-200 font-semibold shadow-sm">
                                        Lihat di Hosting
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 text-2xl">
                                <i class="fa-solid fa-server"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-500">Klien ini belum memiliki proyek hosting.</p>
                        </div>
                    @endif
                </x-ui.card>
            </div>

        </div>
    </x-ui.page-layout>
@endsection
