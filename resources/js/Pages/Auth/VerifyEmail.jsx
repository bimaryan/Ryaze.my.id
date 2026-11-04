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
        <PublicLayout title={`Verifikasi Email - ${siteName ?? 'Ryaze'}`} withNav={false} withFooter={false} bodyClass="bg-slate-50 dark:bg-[#0A1322] font-sans antialiased">
            <Head title={`Verifikasi Email - ${siteName ?? 'Ryaze'}`}>
                <meta head-key="robots" name="robots" content="noindex, nofollow" />
            </Head>

            <style>{`
                [data-reveal] { opacity: 0; transform: translateY(20px); transition: opacity 0.5s ease, transform 0.5s ease; }
                [data-reveal].revealed { opacity: 1; transform: none; }
            `}</style>

            <div className="min-h-screen flex items-center justify-center p-4 py-12">
                <div className="w-full max-w-[440px]" data-reveal>

                    {/* Card */}
                    <div className="bg-white dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] overflow-hidden">
                        {/* Header */}
                        <div className="bg-[#F0F6FC] dark:bg-[#0F1A2A] border-b border-[#E3EAF1] dark:border-[#1B2C44] px-8 py-6">
                            <p className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] mb-1">VERIFIKASI KEAMANAN</p>
                            <h1 className="text-xl font-black text-[#0F1A2A] dark:text-white tracking-tight">Verifikasi Email Anda</h1>
                            <p className="text-[#525252] dark:text-[#8A9BAE] text-sm mt-1">Kami telah mengirim tautan verifikasi ke email Anda</p>
                        </div>

                        {/* Content */}
                        <div className="p-8 space-y-6">
                            {auth?.user?.email && (
                                <div className="bg-slate-50 dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#1B2C44] p-4 flex items-center gap-3">
                                    <i className="fa-solid fa-envelope text-[#1D5B96] dark:text-[#6BA8DC] text-base"></i>
                                    <div className="truncate">
                                        <p className="font-mono text-[9px] uppercase tracking-[0.2em] text-[#737373] dark:text-[#4A7A9B]">Terkirim ke</p>
                                        <p className="text-sm font-bold text-[#0F1A2A] dark:text-white truncate">{auth.user.email}</p>
                                    </div>
                                </div>
                            )}

                            <p className="text-sm text-[#525252] dark:text-[#8A9BAE] leading-relaxed">
                                Silakan cek kotak masuk email Anda dan klik tautan verifikasi untuk mengaktifkan akun. Jika tidak menemukan email tersebut, periksa folder spam atau junk.
                            </p>

                            <div className="pt-4 border-t border-[#E3EAF1] dark:border-[#1B2C44] space-y-4">
                                <button
                                    onClick={resend}
                                    disabled={timeLeft > 0 || processing}
                                    className={`w-full font-mono text-[11px] uppercase tracking-[0.2em] py-3.5 px-4 flex items-center justify-center gap-2.5 transition-colors ${
                                        timeLeft > 0 || processing
                                            ? 'bg-white dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#1B2C44] text-[#94A3B4] dark:text-[#3D556E] cursor-not-allowed'
                                            : 'bg-[#1D5B96] hover:bg-[#2568A8] text-white cursor-pointer'
                                    }`}
                                >
                                    <i className="fa-solid fa-paper-plane text-xs"></i>
                                    {processing ? 'Mengirim...' : timeLeft > 0 ? `Kirim ulang (${timeLeft}s)` : 'Kirim Ulang Email Verifikasi'}
                                </button>

                                <div className="text-center pt-2">
                                    <Link href={route('login')} className="font-mono text-[10px] uppercase tracking-[0.15em] text-[#1D5B96] hover:text-[#164A7C] dark:text-[#6BA8DC] dark:hover:text-[#90BFE0] transition-colors inline-flex items-center gap-2">
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
