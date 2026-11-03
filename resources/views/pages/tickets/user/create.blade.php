@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Buat Tiket Baru" 
        eyebrow="Dukungan Teknis"
        subtitle="Sampaikan kendala atau pertanyaan Anda, tim kami akan segera membantu.">
        <x-slot:iconSlot>
            <div class="shrink-0 w-12 h-12 bg-[#E8F0F9] dark:bg-[#1D5B96]/20 flex items-center justify-center border border-[#E3EAF1] dark:border-[#2A4463]">
                <i class="fa-solid fa-pen-to-square text-[#1D5B96] dark:text-[#6BA8DC] text-xl"></i>
            </div>
        </x-slot:iconSlot>
        <x-slot:actions>
            <a href="{{ route('user_hosting.tickets.index') }}" class="ui-btn ui-btn-md ui-btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="max-w-3xl mt-6">
        <form action="{{ route('user_hosting.tickets.store') }}" method="POST" class="ui-panel overflow-hidden">
            @csrf
            <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">Formulir Tiket</span>
                <span class="ui-status"><span class="ui-status-dot"></span> Siap Dikirim</span>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label for="subject" class="block font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/40 mb-2">Subjek</label>
                    <input type="text" name="subject" id="subject" class="w-full bg-[#F4F8FC] dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#24384F] px-4 py-2.5 text-sm text-[#0F1A2A] dark:text-white focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] outline-none transition" placeholder="Contoh: Domain saya tidak bisa diakses" required>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="department" class="block font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/40 mb-2">Departemen</label>
                        <select name="department" id="department" class="w-full bg-[#F4F8FC] dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#24384F] px-4 py-2.5 text-sm text-[#0F1A2A] dark:text-white focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] outline-none transition" required>
                            <option value="Hosting">Hosting</option>
                            <option value="Billing">Tagihan / Billing</option>
                            <option value="Teknis">Dukungan Teknis</option>
                            <option value="Joki">Layanan Joki</option>
                        </select>
                    </div>
                    <div>
                        <label for="priority" class="block font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/40 mb-2">Prioritas</label>
                        <select name="priority" id="priority" class="w-full bg-[#F4F8FC] dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#24384F] px-4 py-2.5 text-sm text-[#0F1A2A] dark:text-white focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] outline-none transition" required>
                            <option value="low">Rendah</option>
                            <option value="medium" selected>Sedang</option>
                            <option value="high">Tinggi (Penting)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="message" class="block font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/40 mb-2">Pesan Anda</label>
                    <textarea name="message" id="message" rows="6" class="w-full bg-[#F4F8FC] dark:bg-[#0F1D30] border border-[#C9D5E2] dark:border-[#24384F] px-4 py-2.5 text-sm text-[#0F1A2A] dark:text-white focus:ring-2 focus:ring-[#1D5B96]/20 focus:border-[#1D5B96] outline-none transition resize-y" placeholder="Jelaskan kendala Anda secara detail di sini..." required></textarea>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-[#F4F8FC] dark:bg-[#12233A] border-t border-[#E3EAF1] dark:border-[#2A4463] flex items-center justify-end gap-3">
                <button type="submit" class="ui-btn ui-btn-md ui-btn-primary font-mono text-[11px] uppercase tracking-[0.2em]">
                    <i class="fa-solid fa-paper-plane text-xs"></i> Kirim Tiket
                </button>
            </div>
        </form>
    </div>
</x-ui.page-layout>
@endsection
