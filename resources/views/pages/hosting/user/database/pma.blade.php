@extends('index')

@section('content')
    <x-ui.page-layout>

        {{-- SweetAlert2 --}}
        {{-- Flash via SweetAlert --}}
        @if ($errors->any())
        <script nonce="{{ app('csp_nonce') }}">
            document.addEventListener('DOMContentLoaded', () => Swal.fire({
                icon: 'error', title: 'Validasi Gagal',
                html: '{!! implode('<br>', array_map('addslashes', $errors->all())) !!}',
                confirmButtonColor: '#4F46E5', customClass: { popup: 'rounded-xl text-sm' }
            }));
        </script>
        @endif

        {{-- Header --}}
        <x-ui.page-header 
            title="phpMyAdmin" 
            subtitle="Login ke phpMyAdmin untuk mengelola tabel dan isi database secara langsung." 
            icon="fa-server" 
            iconColor="indigo">
        </x-ui.page-header>

    {{-- Database Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse ($databases as $db)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
            {{-- Card Header --}}
            <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-database text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">{{ $db->db_name }}</h3>
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">Active</span>
                    </div>
                </div>
            </div>

            <div class="p-5 space-y-4">
                {{-- phpMyAdmin auto-login via POST --}}
                <div class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="text-sm text-indigo-900">
                        <strong>Login Otomatis</strong><br>
                        <span class="opacity-80 text-xs">Klik tombol di samping untuk masuk ke phpMyAdmin database <code class="font-mono bg-indigo-100 px-1 rounded">{{ $db->db_name }}</code> tanpa harus mengetik password.</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Form POST auto-login ke phpMyAdmin --}}
                        <form method="POST" action="{{ rtrim(env('PMA_URL', '#'), '/') }}/index.php" target="_blank" class="shrink-0">
                            <input type="hidden" name="pma_username" value="{{ $db->db_username }}">
                            <input type="hidden" name="pma_password" value="{{ $db->db_password }}">
                            <input type="hidden" name="server" value="1">
                            <input type="hidden" name="pma_servername" value="{{ $db->host }}">
                            <button type="submit"
                                class="bg-indigo-600 border border-indigo-600 text-white hover:bg-indigo-700 hover:border-indigo-700 transition-all text-xs font-bold py-2.5 px-4 rounded-xl shadow-sm flex items-center gap-2 whitespace-nowrap">
                                <i class="fa-solid fa-server"></i>
                                Buka phpMyAdmin
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-16 text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-slate-100 text-slate-300 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-server text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-700 mb-1">Belum ada Database</h3>
            <p class="text-slate-500 text-sm">Klik "Buat Database" untuk memulai.</p>
        </div>
        @endforelse
    </div>

    </x-ui.page-layout>
@endsection
