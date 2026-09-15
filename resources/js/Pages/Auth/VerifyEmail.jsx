import { useEffect } from 'react';
import { useForm, Head } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function VerifyEmail({ siteName, success, message, turnstileSiteKey }) {
    const { post, processing } = useForm({});

    const handleResend = () => {
        post(route('verification.send'));
    };

    useEffect(() => {
        const els = document.querySelectorAll('[data-reveal]');
        if (!('IntersectionObserver' in window)) { els.forEach(el => el.style.opacity = '1'); return; }
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); } });
        }, { threshold: 0.1 });
        els.forEach(el => io.observe(el));
    }, []);

    return (
        <PublicLayout title="Verifikasi Email" withNav={false} withFooter={false} bodyClass="bg-[#fafafa] dark:bg-[#0a0a14] font-sans antialiased">
            <Head>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer />
            </Head>

            <style>{`
                [data-reveal] { opacity: 0; transform: translateY(20px); transition: opacity 0.5s ease, transform 0.5s ease; }
                [data-reveal].revealed { opacity: 1; transform: none; }
            `}</style>

            <div className="min-h-screen flex items-center justify-center p-6">
                <div className="max-w-md w-full" data-reveal>
                    <div className="bg-[#1a1025] dark:bg-[#1a1025] px-8 py-10 text-center rounded-t-2xl relative">
                        <Link href="/" className="inline-flex items-center gap-2.5 mb-6">
                            <div className="w-7 h-7 bg-[#7c3aed] flex items-center justify-center">
                                <span className="text-white font-black text-xs">R</span>
                            </div>
                            <span className="font-black text-white text-sm tracking-tight">RYAZE</span>
                        </Link>
                        <div className="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i className="fa-solid fa-envelope-open-text text-white text-2xl"></i>
                        </div>
                        <h1 className="text-2xl font-black text-white tracking-tight">Cek Email Anda!</h1>
                        <p className="text-[#a78bfa] mt-2 text-sm">Verifikasi diperlukan untuk melanjutkan</p>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-b-2xl p-8">
                        <div className="mb-5 text-sm text-[#666] dark:text-white/60 text-center leading-relaxed">
                            Kami telah mengirimkan tautan verifikasi ke email Anda. Silakan buka email dan klik tombol verifikasi untuk mengaktifkan akun Anda.
                        </div>

                        <div className="mb-5 flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-xl p-4">
                            <div className="flex-shrink-0 mt-0.5">
                                <i className="fa-solid fa-triangle-exclamation text-amber-500 text-base"></i>
                            </div>
                            <div>
                                <p className="text-amber-800 dark:text-amber-300 font-semibold text-sm mb-1">Email tidak ditemukan di Inbox?</p>
                                <p className="text-amber-700 dark:text-amber-400 text-xs leading-relaxed">
                                    Coba periksa folder <strong>Spam</strong> atau <strong>Junk Mail</strong> di email Anda. Jika ada, tandai sebagai "Bukan Spam" agar email berikutnya langsung masuk ke inbox.
                                </p>
                            </div>
                        </div>

                        <div className="mb-6 bg-[#fafafa] dark:bg-[#1a1a2e] rounded-xl p-4 space-y-2">
                            <p className="text-xs font-semibold text-[#999] dark:text-white/50 uppercase tracking-wide mb-3">Langkah verifikasi</p>
                            <div className="flex items-center gap-3">
                                <span className="w-6 h-6 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0">1</span>
                                <p className="text-sm text-[#666] dark:text-white/60">Buka aplikasi email Anda</p>
                            </div>
                            <div className="flex items-center gap-3">
                                <span className="w-6 h-6 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0">2</span>
                                <p className="text-sm text-[#666] dark:text-white/60">Cari email dari <strong className="text-[#7c3aed]">Ryaze</strong></p>
                            </div>
                            <div className="flex items-center gap-3">
                                <span className="w-6 h-6 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0">3</span>
                                <p className="text-sm text-[#666] dark:text-white/60">Klik tombol <strong>"Verifikasi Email Sekarang"</strong></p>
                            </div>
                            <div className="flex items-center gap-3">
                                <span className="w-6 h-6 bg-amber-100 dark:bg-amber-900/50 text-amber-500 dark:text-amber-400 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0">!</span>
                                <p className="text-sm text-[#999] dark:text-white/50">Tidak ada? Cek folder <strong>Spam / Junk</strong></p>
                            </div>
                        </div>

                        <form onSubmit={(e) => { e.preventDefault(); handleResend(); }} className="space-y-3">
                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content} />
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-[#7c3aed] text-white font-semibold py-3 px-4 rounded-lg hover:bg-[#6d28d9] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                {processing ? (
                                    <>
                                        <i className="fa-solid fa-spinner fa-spin text-sm"></i>
                                        <span>Mengirim ulang...</span>
                                    </>
                                ) : (
                                    <>
                                        <i className="fa-solid fa-paper-plane text-sm"></i>
                                        <span>Kirim Ulang Email Verifikasi</span>
                                    </>
                                )}
                            </button>
                        </form>

                        <div className="mt-6 pt-5 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-center text-sm">
                            <form method="POST" action={route('logout')}>
                                <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content} />
                                <button type="submit" className="font-semibold text-[#999] dark:text-white/50 hover:text-[#1a1025] dark:hover:text-white transition-colors flex items-center gap-1.5">
                                    <i className="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                                    Keluar dari akun ini
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}