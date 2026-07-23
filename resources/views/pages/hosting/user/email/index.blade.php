@extends('index')

@section('content')
    <x-ui.page-layout>

        {{-- SweetAlert2 --}}
        @if ($errors->any())
        <script nonce="{{ app('csp_nonce') }}">
            (function() {
                Swal.fire({
                    icon: 'error', title: 'Validasi Gagal',
                    html: '{!! implode('<br>', array_map('addslashes', $errors->all())) !!}',
                    confirmButtonColor: '#4F46E5', customClass: { popup: 'rounded-xl text-sm' }
                });
            })();
        </script>
        @endif

        {{-- Header --}}
        <x-ui.page-header 
            title="Email Management" 
            subtitle="Kelola alamat email untuk domain aplikasi Anda." 
            icon="fa-envelope" 
            iconColor="purple">
            <x-slot:actions>
                <button id="btn-open-create-modal" class="inline-flex justify-center items-center flex-shrink-0 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    + Buat Akun Email
                </button>
            </x-slot:actions>
        </x-ui.page-header>

    {{-- Email Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse ($emails as $email)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
            {{-- Card Header --}}
            <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-envelope text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">{{ $email->email_address }}</h3>
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">Active</span>
                    </div>
                </div>
                <button data-action="{{ route('user_hosting.emails.destroy', $email->hashid) }}"
                    class="btn-delete-email text-slate-400 hover:text-rose-500 p-2 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Email">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </div>

            <div class="p-5 space-y-4">
                {{-- Domain & Quota --}}
                <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-slate-50/50">
                    <div>
                        <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Domain</span>
                        <code class="text-sm font-mono text-slate-700">{{ $email->domain }}</code>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Quota</span>
                        <span class="text-sm font-medium text-slate-700">{{ $email->quota_mb }} MB</span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100">
                    <a href="{{ rtrim(env('POSTE_IO_URL', 'https://mail.ryaze.my.id'), '/') }}/webmail" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Login Webmail
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 px-4 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 mb-6 rounded-full bg-slate-50 border-2 border-slate-100 flex items-center justify-center">
                <i class="fa-regular fa-envelope-open text-3xl text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Belum Ada Akun Email</h3>
            <p class="text-sm text-slate-500 max-w-sm mb-6">Anda belum membuat akun email profesional apapun. Klik tombol di bawah untuk membuat email pertama Anda.</p>
            <button onclick="document.getElementById('btn-open-create-modal').click()" class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-700 px-5 py-2.5 rounded-xl text-sm font-medium transition-colors">
                <i class="fa-solid fa-plus"></i> Buat Email
            </button>
        </div>
        @endforelse
    </div>

    {{-- Create Email Modal --}}
    <div id="create-modal" class="fixed inset-0 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm modal-overlay"></div>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 relative transform scale-95 transition-transform duration-300 ease-out z-10 flex flex-col max-h-[90vh]">
            
            <div class="flex items-center justify-between p-5 border-b border-slate-100 shrink-0">
                <h3 class="text-lg font-bold text-slate-800">Buat Akun Email Baru</h3>
                <button type="button" class="btn-close-modal text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-100">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-5 overflow-y-auto custom-scrollbar flex-1">
                <form id="create-form" action="{{ route('user_hosting.emails.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    {{-- Prefix & Domain --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email <span class="text-rose-500">*</span></label>
                        <div class="flex gap-2">
                            <input type="text" name="prefix" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" placeholder="admin" required pattern="^[a-zA-Z0-9_\.-]+$">
                            <div class="flex items-center px-2 text-slate-400 font-bold">@</div>
                            <select name="domain" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" required>
                                @foreach($projects as $project)
                                    <option value="{{ $project->ryaze_domain }}">{{ $project->ryaze_domain }}</option>
                                    @if($project->custom_domain)
                                        <option value="{{ $project->custom_domain }}">{{ $project->custom_domain }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Pilih domain dari project Anda.</p>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="password" id="email-password" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition pr-10 font-mono" placeholder="Minimal 8 karakter" required minlength="8">
                            <button type="button" id="btn-generate-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 transition-colors" title="Generate Password">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="p-5 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl shrink-0 flex justify-end gap-2">
                <button type="button" class="btn-close-modal px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-200 bg-slate-100 rounded-xl transition-colors">Batal</button>
                <button type="submit" form="create-form" class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm hover:shadow active:scale-[0.98]">Buat Email</button>
            </div>
        </div>
    </div>
    
    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script nonce="{{ app('csp_nonce') }}">
        (function() {
            const modal = document.getElementById('create-modal');
            const modalInner = modal ? modal.querySelector('.bg-white') : null;
            
            function openModal() {
                if(!modal) return;
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modalInner.classList.remove('scale-95');
            }
            
            function closeModal() {
                if(!modal) return;
                modal.classList.add('opacity-0', 'pointer-events-none');
                modalInner.classList.add('scale-95');
                document.getElementById('create-form').reset();
            }

            document.getElementById('btn-open-create-modal')?.addEventListener('click', openModal);
            [].forEach.call(document.querySelectorAll('.btn-close-modal, .modal-overlay'), el => el.addEventListener('click', closeModal));

            // Generate Password
            document.getElementById('btn-generate-password')?.addEventListener('click', () => {
                const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
                let pass = "";
                for (let i = 0; i < 12; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
                document.getElementById('email-password').value = pass;
            });

            // Delete Confirm
            [].forEach.call(document.querySelectorAll('.btn-delete-email'), btn => {
                btn.addEventListener('click', function() {
                    const action = this.dataset.action;
                    Swal.fire({
                        title: 'Hapus Akun Email?',
                        text: "Semua pesan di dalam mailbox ini juga akan terhapus. Tindakan ini tidak bisa dibatalkan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#EF4444',
                        cancelButtonColor: '#94A3B8',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        customClass: { popup: 'rounded-xl text-sm' }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('delete-form');
                            form.action = action;
                            form.submit();
                        }
                    });
                });
            });
        })();
    </script>

    </x-ui.page-layout>
@endsection
