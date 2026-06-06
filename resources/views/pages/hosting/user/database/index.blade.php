@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50 relative">

        @if (session('success'))
            <div
                class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i> <span>{{ session('success') }}</span>
            </div>
        @elseif (session('error'))
            <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation"></i> <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Database & phpMyAdmin</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola database MySQL untuk aplikasi Anda.</p>
            </div>
            <button onclick="document.getElementById('createDbModal').classList.remove('hidden')"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm flex items-center gap-2 text-sm">
                <i class="fa-solid fa-plus"></i> Buat Database
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse ($databases as $db)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50 px-5 py-4 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-database text-indigo-500 text-xl"></i>
                            <h3 class="font-bold text-slate-800 text-base">{{ $db->db_name }}</h3>
                        </div>
                        <form action="{{ route('user_hosting.databases.destroy', $db->hashid) }}" method="POST"
                            onsubmit="return confirm('Hapus database permanen?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-rose-500 p-2"><i
                                    class="fa-regular fa-trash-can"></i></button>
                        </form>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-slate-500 font-semibold uppercase">Username</span>
                                <code
                                    class="text-sm bg-slate-50 border border-slate-200 px-2 py-1 rounded">{{ $db->db_username }}</code>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-slate-500 font-semibold uppercase">Password</span>
                                <code
                                    class="text-sm bg-slate-50 border border-slate-200 px-2 py-1 rounded blur-sm hover:blur-none cursor-pointer">{{ $db->db_password }}</code>
                            </div>
                        </div>

                        <hr class="border-slate-100">

                        <div
                            class="bg-blue-50 border border-blue-100 p-4 rounded-lg flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-sm text-blue-800">
                                <strong>Login phpMyAdmin</strong><br>
                                Gunakan Username & Password di atas untuk login. Anda hanya akan melihat database milik Anda
                                sendiri.
                            </div>
                            <a href="{{ env('PMA_URL', '#') }}" target="_blank"
                                class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-4 rounded-lg shadow-sm">
                                <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Buka phpMyAdmin
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-xl border border-slate-200 p-12 text-center">
                    <p class="text-slate-500 text-sm">Belum ada database. Silakan buat baru.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal -->
    <div id="createDbModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between">
                <h3 class="font-bold text-slate-800">Buat Database</h3>
                <button onclick="document.getElementById('createDbModal').classList.add('hidden')"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('user_hosting.databases.store') }}" method="POST" class="p-6">
                @csrf
                <label class="block text-sm font-medium mb-2">Nama Database</label>
                <input type="text" name="db_name" required pattern="[A-Za-z0-9_]+"
                    class="w-full border rounded-lg p-2.5 outline-none focus:border-indigo-500">
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('createDbModal').classList.add('hidden')"
                        class="px-4 py-2 border rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Buat DB</button>
                </div>
            </form>
        </div>
    </div>
@endsection
