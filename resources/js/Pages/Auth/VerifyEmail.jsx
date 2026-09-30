import { useState, useEffect } from 'react';
import { Link, Head, useForm, usePage } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function VerifyEmail({ siteName }) {
    const { auth } = usePage().props;
    const [timeLeft, setTimeLeft] = useState(60);
    const { post, processing } = useForm({});

    useEffect(() => {
        if (timeLeft <= 0) return;
        const timer = setInterval(() => setTimeLeft(t => t - 1), 1000);
        return () => clearInterval(timer);
    }, [timeLeft]);

    useEffect(() => {
        const els = document.querySelectorAll('[data-reveal]');
        if (!('IntersectionObserver' in window)) { els.forEach(el => el.style.opacity = '1'); return; }
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); } });
        }, { threshold: 0.1 });
        els.forEach(el => io.observe(el));
    }, []);

    const resend = (e) => {
        e.preventDefault();
        if (timeLeft > 0) return;
        post(route('verification.send'), {
            onSuccess: () => setTimeLeft(60),
        });
    };

    return (
        <PublicLayout withNav={false} withFooter={false} bodyClass="bg-[#fafafa] dark:bg-[#0a0a14] font-sans antialiased">
            <Head title={`Verifikasi Email - ${siteName ?? 'Ryaze'}`} />

            <style>{`
                [data-reveal] { opacity: 0; transform: translateY(20px); transition: opacity 0.5s ease, transform 0.5s ease; }
                [data-reveal].revealed { opacity: 1; transform: none; }
            `}</style>

            <div className="min-h-screen flex items-center justify-center p-6">
                <div className="max-w-md w-full" data-reveal>
                    <div className="bg-[#7c3aed] dark:bg-[#1a1025] px-8 py-10 text-center relative">
                        <Link href="/" className="inline-flex items-center gap-2.5 mb-6">
                            <div className="w-7 h-7 bg-[#7c3aed] flex items-center justify-center">
                                <span className="text-white font-black text-xs">R</span>
                            </div>
                            <span className="font-black text-white text-sm tracking-tight">RYAZE</span>
                        </Link>
                        <h1 className="text-3xl font-black text-white tracking-tight">Verifikasi Email</h1>
                        <p className="text-white/80 mt-2 text-sm">Kami telah mengirimkan tautan verifikasi ke email Anda.</p>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-8">
                        <div className="text-center space-y-6">
                            {auth?.user?.email && (
                                <p className="text-sm text-[#666] dark:text-white/60">
                                    Tautan verifikasi dikirim ke: <strong className="text-[#7c3aed]">{auth.user.email}</strong>
                                </p>
                            )}

                            <p className="text-sm text-[#666] dark:text-white/60">
                                Silakan cek email Anda dan klik tautan verifikasi untuk mengaktifkan akun.
                                Jika tidak ada email di inbox, cek folder spam.
                            </p>

                            <div className="pt-4 border-t border-[#e5e5e5] dark:border-[#2d1f42] space-y-3">
                                <button
                                    onClick={resend}
                                    disabled={timeLeft > 0 || processing}
                                    className={`inline-flex items-center gap-2 text-sm font-medium transition-colors ${
                                        timeLeft > 0 || processing
                                            ? 'text-[#999] dark:text-white/50 cursor-not-allowed'
                                            : 'text-[#7c3aed] hover:text-[#6d28d9] cursor-pointer'
                                    }`}
                                >
                                    <i className="fa-solid fa-paper-plane text-xs"></i>
                                    {processing ? 'Mengirim...' : timeLeft > 0 ? `Kirim ulang dalam ${timeLeft}s` : 'Kirim ulang email verifikasi'}
                                </button>

                                <div className="pt-4 border-t border-[#e5e5e5] dark:border-[#2d1f42]">
                                    <Link href={route('login')} className="font-semibold text-[#7c3aed] hover:text-[#6d28d9] transition-colors flex items-center justify-center gap-2">
                                        <i className="fa-solid fa-arrow-left text-xs"></i>
                                        Kembali ke Login
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}

