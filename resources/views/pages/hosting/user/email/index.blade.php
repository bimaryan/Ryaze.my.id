@extends('index')

@section('content')
    <x-ui.page-layout>

        @if ($errors->any())
            <script nonce="{{ app('csp_nonce') }}">
                (function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: @js(implode('<br>', $errors->all())),
                        confirmButtonColor: '#1D5B96',
                        customClass: { popup: 'text-sm' }
                    });
                })();
            </script>
        @endif

        <x-ui.page-header
            title="Email Management"
            eyebrow="Mailbox & Domain"
            subtitle="Kelola alamat email untuk domain aplikasi Anda."
            icon="fa-envelope"
            iconColor="purple">
            <x-slot:actions>
                <button type="button" id="btn-open-create-modal" class="ui-btn ui-btn-md ui-btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Buat Akun Email
                </button>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            @forelse ($emails as $email)
                <div class="ui-panel overflow-hidden">
                    <div class="ui-panel-head bg-[#F4F8FC] px-5 py-4 dark:bg-[#12233A]">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="ui-icon-box h-10 w-10">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <div class="min-w-0">
                                <h3 class="truncate font-mono text-sm font-semibold text-[#0F1A2A] dark:text-white">{{ $email->email_address }}</h3>
                                <span class="ui-status mt-0.5"><span class="ui-status-dot"></span>Active</span>
                            </div>
                        </div>
                        <button type="button" data-action="{{ route('user_hosting.emails.destroy', $email->hashid) }}"
                                class="btn-delete-email ui-icon-btn ui-icon-btn-danger" title="Hapus Email">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>

                    <div class="space-y-4 p-5">
                        <div class="grid grid-cols-2 gap-px border border-[#E3EAF1] bg-[#E3EAF1] dark:border-[#2A4463] dark:bg-[#2A4463]">
                            <div class="bg-[#FAFBFD] p-3 dark:bg-[#0F1D30]">
                                <span class="ui-eyebrow-muted mb-1 block">Domain</span>
                                <code class="text-sm font-mono text-[#0F1A2A] dark:text-white">{{ $email->domain }}</code>
                            </div>
                            <div class="bg-[#FAFBFD] p-3 dark:bg-[#0F1D30]">
                                <span class="ui-eyebrow-muted mb-1 block">Quota</span>
                                <span class="text-sm font-medium text-[#0F1A2A] dark:text-white">{{ $email->quota_mb }} MB</span>
                            </div>
                        </div>

                        <div class="border-t border-[#EDF2F7] pt-4 dark:border-[#24384F]">
                            <a href="{{ rtrim(env('POSTE_IO_URL', 'https://mail.ryaze.my.id'), '/') }}/webmail" target="_blank"
                               class="ui-chip">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Login Webmail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="ui-panel col-span-full flex flex-col items-center justify-center px-6 py-16 text-center">
                    <span class="ui-icon-box mb-4 h-12 w-12">
                        <i class="fa-regular fa-envelope-open text-lg"></i>
                    </span>
                    <h3 class="ui-display text-lg">Belum Ada Akun Email</h3>
                    <p class="mt-1 max-w-sm text-sm text-[#525252] dark:text-white/50">Anda belum membuat akun email profesional. Klik tombol di bawah untuk membuat email pertama Anda.</p>
                    <button type="button" id="btn-open-create-empty" class="ui-btn ui-btn-md ui-btn-primary mt-5">
                        <i class="fa-solid fa-plus"></i> Buat Email
                    </button>
                </div>
            @endforelse
        </div>

        <div id="create-modal" class="ui-modal-overlay hidden">
            <div class="ui-panel relative flex max-h-[90vh] w-full max-w-md flex-col">
                <div class="ui-panel-head bg-[#F4F8FC] dark:bg-[#12233A]">
                    <h3 class="ui-modal-title">Buat Akun Email Baru</h3>
                    <button type="button" class="btn-close-modal ui-icon-btn ui-icon-btn-neutral">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form id="create-form" action="{{ route('user_hosting.emails.store') }}" method="POST">
                    @csrf
                    <div class="flex-1 space-y-4 overflow-y-auto p-6">
                        <div>
                            <label class="ui-label">Alamat Email <span class="text-rose-500">*</span></label>
                            <div class="flex gap-2">
                                <input type="text" name="prefix" class="ui-field" placeholder="admin" required pattern="^[a-zA-Z0-9_\.-]+$">
                                <span class="flex items-center px-1 font-mono font-bold text-[#8A9BAE] dark:text-white/40">@</span>
                                <select name="domain" class="ui-field" required>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->ryaze_domain }}">{{ $project->ryaze_domain }}</option>
                                        @if ($project->custom_domain)
                                            <option value="{{ $project->custom_domain }}">{{ $project->custom_domain }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <p class="mt-1.5 text-[11px] text-[#525252] dark:text-white/45">Pilih domain dari project Anda.</p>
                        </div>

                        <div>
                            <label class="ui-label">Password <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input type="text" name="password" id="email-password" class="ui-field pr-11 font-mono" placeholder="Minimal 8 karakter" required minlength="8">
                                <button type="button" id="btn-generate-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8A9BAE] transition-colors hover:text-[#1D5B96] dark:text-white/40 dark:hover:text-[#6BA8DC]" title="Generate Password">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-[#EDF2F7] bg-[#FAFBFD] p-6 dark:border-[#2A4463] dark:bg-[#0F1D30]">
                        <button type="button" class="btn-close-modal ui-btn ui-btn-md ui-btn-secondary">Batal</button>
                        <button type="submit" class="ui-btn ui-btn-md ui-btn-primary">Buat Email</button>
                    </div>
                </form>
            </div>
        </div>

        <form id="delete-form" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <script nonce="{{ app('csp_nonce') }}">
            (function () {
                const modal = document.getElementById('create-modal');
                const form = document.getElementById('create-form');

                function openModal() {
                    if (!modal) return;
                    modal.classList.remove('hidden');
                    const input = form?.querySelector('input[name="prefix"]');
                    if (input) input.focus();
                }

                function closeModal() {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    form?.reset();
                }

                document.getElementById('btn-open-create-modal')?.addEventListener('click', openModal);
                document.getElementById('btn-open-create-empty')?.addEventListener('click', openModal);
                document.querySelectorAll('.btn-close-modal').forEach(el => el.addEventListener('click', closeModal));
                modal?.addEventListener('click', function (e) {
                    if (e.target === modal) closeModal();
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
                });

                document.getElementById('btn-generate-password')?.addEventListener('click', () => {
                    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+';
                    let pass = '';
                    for (let i = 0; i < 12; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
                    document.getElementById('email-password').value = pass;
                });

                document.querySelectorAll('.btn-delete-email').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const action = this.dataset.action;
                        Swal.fire({
                            title: 'Hapus Akun Email?',
                            text: 'Semua pesan di dalam mailbox ini juga akan terhapus. Tindakan ini tidak bisa dibatalkan!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#e11d48',
                            cancelButtonColor: '#94A3B4',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal',
                            customClass: { popup: 'text-sm' }
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
