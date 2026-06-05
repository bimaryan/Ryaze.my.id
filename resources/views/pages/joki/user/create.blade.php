@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50">
        <!-- Header -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Pesan Proyek Baru</h1>
                <p class="text-sm text-slate-500 mt-0.5">
                    Isi detail proyek Anda, saya akan segera meninjau dan memulai pengerjaannya.
                </p>
            </div>
            <a href="{{ route('user_joki.dashboard') }}"
                class="text-sm font-semibold text-slate-500 hover:text-indigo-600 transition">
                &larr; Kembali
            </a>
        </div>

        <!-- Form Section -->
        <div class="mt-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <form action="{{ route('user_joki.store') }}" method="POST" class="p-8 space-y-6">
                    @csrf

                    <!-- Baris 1: Layanan & Deadline -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Layanan</label>
                            <select name="service_id" required
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                                @foreach (\App\Models\JokiService::where('is_active', true)->get() as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Deadline</label>
                            <input type="date" name="deadline" required
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                    </div>

                    <!-- Baris 2: Nama Proyek & Tech Stack -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Proyek</label>
                            <input type="text" name="project_name" required placeholder="Contoh: Web Portofolio..."
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tech Stack</label>
                            <input type="text" name="tech_stack" placeholder="Contoh: Laravel, React, Vue..."
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                    </div>

                    <!-- Baris 3: Deskripsi -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Kebutuhan</label>
                        <textarea name="description" rows="5" required placeholder="Jelaskan fitur apa saja yang diinginkan..."
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                    </div>

                    <!-- Footer Action -->
                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="submit"
                            class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all hover:-translate-y-0.5">
                            Kirim Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
