@extends('index')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header 
        title="Buat Tiket Baru" 
        subtitle="Sampaikan kendala atau pertanyaan Anda, tim kami akan segera membantu.">
        <x-slot:iconSlot>
            <div class="shrink-0 w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-pen-to-square text-indigo-600 text-xl"></i>
            </div>
        </x-slot:iconSlot>
        <x-slot:actions>
            <a href="{{ route('user_hosting.tickets.index') }}" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg font-medium transition text-sm flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="max-w-3xl mt-6">
        <form action="{{ route('user_hosting.tickets.store') }}" method="POST" class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            @csrf
            <div class="p-6 space-y-5">
                <div>
                    <label for="subject" class="block text-sm font-semibold text-slate-700 mb-1.5">Subjek</label>
                    <input type="text" name="subject" id="subject" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Contoh: Domain saya tidak bisa diakses" required>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="department" class="block text-sm font-semibold text-slate-700 mb-1.5">Departemen</label>
                        <select name="department" id="department" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" required>
                            <option value="Hosting">Hosting</option>
                            <option value="Billing">Tagihan / Billing</option>
                            <option value="Teknis">Dukungan Teknis</option>
                            <option value="Joki">Layanan Joki</option>
                        </select>
                    </div>
                    <div>
                        <label for="priority" class="block text-sm font-semibold text-slate-700 mb-1.5">Prioritas</label>
                        <select name="priority" id="priority" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" required>
                            <option value="low">Rendah</option>
                            <option value="medium" selected>Sedang</option>
                            <option value="high">Tinggi (Penting)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="message" class="block text-sm font-semibold text-slate-700 mb-1.5">Pesan Anda</label>
                    <textarea name="message" id="message" rows="6" class="py-3 w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="Jelaskan kendala Anda secara detail di sini..." required></textarea>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Tiket
                </button>
            </div>
        </form>
    </div>
</x-ui.page-layout>
@endsection
