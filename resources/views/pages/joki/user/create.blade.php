@extends('index')

@section('content')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen bg-slate-50 relative">
        <div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-11 h-11 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fa-solid fa-plus text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Buat Pesanan Joki</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Isi detail proyek Anda, saya akan segera meninjau untuk menentukan estimasi biaya dan waktu pengerjaan.</p>
                </div>
            </div>
            <a href="{{ route('user_joki.dashboard') }}" class="inline-flex justify-center items-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
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
                            <select name="service_id" id="service_select" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-shadow">
                                <option value="" disabled selected>-- Pilih Jenis Layanan --</option>
                                @foreach (\App\Models\JokiService::where('is_active', true)->get() as $service)
                                    <!-- Tambahkan atribut data-price di sini -->
                                    <option value="{{ $service->id }}"
                                        data-price="{{ number_format($service->base_price, 0, ',', '.') }}">
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                            <!-- Tempat menampilkan harga estimasi -->
                            <p id="price_estimate" class="mt-2 text-sm font-semibold text-indigo-600 hidden">
                                <i class="fa-solid fa-tag me-1"></i> Estimasi Harga Mulai: Rp <span
                                    id="price_value">0</span>
                            </p>
                            <p class="text-xs text-slate-400 mt-1">*Harga final ditentukan setelah kesepakatan.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Deadline Pengerjaan</label>
                            <input type="date" name="deadline" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-shadow">
                        </div>
                    </div>

                    <!-- Baris 2: Nama Proyek & Tech Stack -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Proyek</label>
                            <input type="text" name="project_name" required placeholder="Contoh: Web Portofolio..."
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-shadow">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tech Stack (Opsional)</label>
                            <input type="text" name="tech_stack" placeholder="Contoh: Laravel, React, Vue..."
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-shadow">
                        </div>
                    </div>

                    <!-- Baris 3: Deskripsi -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Kebutuhan Detail</label>
                        <textarea name="description" rows="5" required
                            placeholder="Jelaskan secara rinci fitur apa saja yang diinginkan, jumlah halaman, dsb..."
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-shadow"></textarea>
                    </div>

                    <!-- Footer Action -->
                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                            Kirim Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script untuk menampilkan estimasi harga -->
    <script nonce="{{ app('csp_nonce') ?? '' }}">
        document.addEventListener("DOMContentLoaded", function() {
            const serviceSelect = document.getElementById('service_select');
            const priceEstimateBox = document.getElementById('price_estimate');
            const priceValue = document.getElementById('price_value');

            serviceSelect.addEventListener('change', function() {
                // Ambil harga dari attribute data-price milik option yang dipilih
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price');

                if (price) {
                    priceValue.textContent = price;
                    priceEstimateBox.classList.remove('hidden');
                } else {
                    priceEstimateBox.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
