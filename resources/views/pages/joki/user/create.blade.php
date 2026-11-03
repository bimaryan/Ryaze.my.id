@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header
            title="Buat Pesanan Joki"
            eyebrow="Layanan Joki · Brief Proyek"
            subtitle="Isi detail proyek Anda, saya akan segera meninjau untuk menentukan estimasi biaya dan waktu pengerjaan."
            icon="fa-solid fa-plus">
            <x-slot:actions>
                <a href="{{ route('user_joki.dashboard') }}" class="ui-btn-secondary font-mono text-[11px] uppercase tracking-[0.15em]">
                    &larr; Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <!-- Form Section -->
        <div class="mt-6">
            <div class="ui-panel">
                <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#0F1A2A]">
                    <h2 class="ui-eyebrow text-[11px]">Brief Pesanan</h2>
                    <span class="ui-eyebrow-muted">Form</span>
                </div>
                <form action="{{ route('user_joki.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    <!-- Baris 1: Layanan & Deadline -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="ui-eyebrow-muted block mb-2">Pilih Layanan</label>
                            <select name="service_id" id="service_select" required
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] outline-none transition">
                                <option value="" disabled selected>-- Pilih Jenis Layanan --</option>
                                @foreach (\App\Models\JokiService::where('is_active', true)->get() as $service)
                                    <option value="{{ $service->id }}"
                                        data-price="{{ number_format($service->base_price, 0, ',', '.') }}">
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                            <!-- Tempat menampilkan harga estimasi -->
                            <p id="price_estimate" class="mt-2 font-mono text-[12px] font-semibold text-[#1D5B96] dark:text-[#6BA8DC] hidden">
                                <i class="fa-solid fa-tag me-1"></i> Estimasi Harga Mulai: Rp <span
                                    id="price_value">0</span>
                            </p>
                            <p class="text-xs text-[#94A3B4] dark:text-white/35 mt-1">*Harga final ditentukan setelah kesepakatan.</p>
                        </div>
                        <div>
                            <label class="ui-eyebrow-muted block mb-2">Deadline Pengerjaan</label>
                            <input type="date" name="deadline" required
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] outline-none transition">
                        </div>
                    </div>

                    <!-- Baris 2: Nama Proyek & Tech Stack -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="ui-eyebrow-muted block mb-2">Nama Proyek</label>
                            <input type="text" name="project_name" required placeholder="Contoh: Web Portofolio..."
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] outline-none transition">
                        </div>
                        <div>
                            <label class="ui-eyebrow-muted block mb-2">Tech Stack (Opsional)</label>
                            <input type="text" name="tech_stack" placeholder="Contoh: Laravel, React, Vue..."
                                class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] outline-none transition">
                        </div>
                    </div>

                    <!-- Baris 3: Deskripsi -->
                    <div>
                        <label class="ui-eyebrow-muted block mb-2">Deskripsi Kebutuhan Detail</label>
                        <textarea name="description" rows="5" required
                            placeholder="Jelaskan secara rinci fitur apa saja yang diinginkan, jumlah halaman, dsb..."
                            class="w-full bg-white dark:bg-[#0A1322] border border-[#C9D5E2] dark:border-[#1B2C44] px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] outline-none transition"></textarea>
                    </div>

                    <!-- Footer Action -->
                    <div class="flex justify-end pt-4 border-t border-[#EDF2F7] dark:border-[#152238]">
                        <button type="submit"
                            class="ui-btn-primary font-mono text-[11px] uppercase tracking-[0.15em]">
                            Kirim Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-ui.page-layout>

    <!-- Script untuk menampilkan estimasi harga -->
    <script nonce="{{ app('csp_nonce') ?? '' }}">
        (function() {
            var serviceSelect = document.getElementById('service_select');
            var priceEstimateBox = document.getElementById('price_estimate');
            var priceValue = document.getElementById('price_value');

            if (serviceSelect) {
                serviceSelect.addEventListener('change', function() {
                    // Ambil harga dari attribute data-price milik option yang dipilih
                    var selectedOption = this.options[this.selectedIndex];
                    var price = selectedOption.getAttribute('data-price');

                    if (price) {
                        priceValue.textContent = price;
                        priceEstimateBox.classList.remove('hidden');
                    } else {
                        priceEstimateBox.classList.add('hidden');
                    }
                });
            }
        })();
    </script>
@endsection
