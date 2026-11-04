import { useState, useEffect, useRef } from 'react';
import { Link, useForm, Head, usePage } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function ForgotPassword({ errors, siteName, turnstileSiteKey }) {
    const { flash } = usePage().props;
    const turnstileRef = useRef(null);
    const turnstileWidgetId = useRef(null);
    const [turnstileToken, setTurnstileToken] = useState('');
    const [showSuccess, setShowSuccess] = useState(false);

    const { data, setData, post, processing } = useForm({
        email: '',
        'cf-turnstile-response': '',
    });

    useEffect(() => {
        const renderWidget = () => {
            if (turnstileRef.current && window.turnstile && turnstileSiteKey) {
                if (turnstileWidgetId.current) {
                    try { window.turnstile.remove(turnstileWidgetId.current); } catch (e) {}
                    turnstileWidgetId.current = null;
                }
                turnstileRef.current.innerHTML = '';
                turnstileWidgetId.current = window.turnstile.render(turnstileRef.current, {
                    sitekey: turnstileSiteKey,
                    callback: (token) => setTurnstileToken(token),
                    theme: 'auto',
                });
            }
        };

        let interval;
        if (window.turnstile) {
            renderWidget();
        } else {
            interval = setInterval(() => {
                if (window.turnstile) {
                    clearInterval(interval);
                    renderWidget();
                }
            }, 100);
        }

        return () => {
            if (interval) clearInterval(interval);
            if (turnstileWidgetId.current) {
                try { window.turnstile.remove(turnstileWidgetId.current); } catch (e) {}
                turnstileWidgetId.current = null;
            }
        };
    }, [turnstileSiteKey]);

    const handleSubmit = (e) => {
        e.preventDefault();
        setData('cf-turnstile-response', turnstileToken);
        post(route('password.email'), {
            onSuccess: () => {
                setShowSuccess(true);
                setData('email', '');
                setData('cf-turnstile-response', '');
                setTurnstileToken('');
            },
        });
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
        <PublicLayout title="Lupa Password" withNav={false} withFooter={false} bodyClass="bg-slate-50 dark:bg-[#0A1322] font-sans antialiased">
            <Head>
                <meta name="robots" content="noindex, nofollow" />
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer />
            </Head>

            <style>{`
                [data-reveal] { opacity: 0; transform: translateY(20px); transition: opacity 0.5s ease, transform 0.5s ease; }
                [data-reveal].revealed { opacity: 1; transform: none; }
            `}</style>

            <div className="min-h-screen flex items-center justify-center p-4 py-12">
                <div className="w-full max-w-[420px]" data-reveal>

                    {/* Card */}
                    <div className="bg-white dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] overflow-hidden">
                        {/* Card Header */}
                        <div className="bg-[#F0F6FC] dark:bg-[#0F1A2A] border-b border-[#E3EAF1] dark:border-[#1B2C44] px-8 py-6">
                            <p className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] mb-1">PEMULIHAN AKUN</p>
                            <h1 className="text-xl font-black text-[#0F1A2A] dark:text-white tracking-tight">Lupa Password?</h1>
                            <p className="text-[#525252] dark:text-[#8A9BAE] text-sm mt-1">Kami kirimkan instruksi reset ke email Anda</p>
                        </div>

                        <div className="p-8">
                            {showSuccess || flash?.success ? (
                                /* Success state */
                                <div className="space-y-6">
                                    <div className="bg-emerald-50 dark:bg-[#0A1322] border border-emerald-200 dark:border-emerald-500/30 p-5 flex gap-4">
                                        <div className="flex-shrink-0 w-10 h-10 bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-center">
                                            <i className="fa-solid fa-envelope-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
                                        </div>
                                        <div>
                                            <p className="font-mono text-[10px] uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400 mb-1">EMAIL TERKIRIM</p>
                                            <p className="text-sm text-[#525252] dark:text-[#8A9BAE] leading-relaxed">
                                                Link reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.
                                            </p>
                                        </div>
                                    </div>
                                    <button
                                        onClick={() => setShowSuccess(false)}
                                        className="w-full bg-white dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#C9D5E2] dark:hover:border-[#24384F] text-[#0F1A2A] dark:text-[#C9D5E2] font-mono text-[10px] uppercase tracking-[0.2em] py-3 px-4 flex items-center justify-center gap-2 transition-all"
                                    >
                                        <i className="fa-solid fa-rotate-right text-xs"></i>
                                        Kirim Ulang
                                    </button>
                                    <div className="text-center">
                                        <Link href={route('login')} className="font-mono text-[10px] uppercase tracking-[0.15em] text-[#1D5B96] hover:text-[#164A7C] dark:text-[#6BA8DC] dark:hover:text-[#90BFE0] transition-colors inline-flex items-center gap-2">
                                            <i className="fa-solid fa-arrow-left text-xs"></i>
                                            Kembali ke Login
                                        </Link>
                                    </div>
                                </div>
                            ) : (
                                /* Form */
                                <form onSubmit={handleSubmit} className="space-y-5">
                                    <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content} />
                                    <input type="hidden" name="cf-turnstile-response" value={data['cf-turnstile-response']} />

                                    <div>
                                        <label htmlFor="email" className="block font-mono text-[10px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] mb-2">
                                            Alamat Email
                                        </label>
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            value={data.email}
                                            onChange={(e) => setData('email', e.target.value)}
                                            className={`w-full bg-slate-50 dark:bg-[#0A1322] border ${errors.email ? 'border-rose-500' : 'border-[#E3EAF1] dark:border-[#1B2C44] focus:border-[#1D5B96]'} px-4 py-3 text-sm text-[#0F1A2A] dark:text-white placeholder-[#94A3B4] dark:placeholder-[#3D556E] focus:outline-none focus:ring-1 focus:ring-[#1D5B96] transition-all`}
                                            placeholder="nama@email.com"
                                            autoFocus
                                        />
                                        {errors.email && <p className="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{errors.email}</p>}
                                    </div>

                                    {errors['cf-turnstile-response'] && (
                                        <p className="text-xs text-rose-600 dark:text-rose-400 text-center">{errors['cf-turnstile-response']}</p>
                                    )}

                                    <div className="flex justify-center">
                                        <div ref={turnstileRef}></div>
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full bg-[#1D5B96] text-white font-mono text-[11px] uppercase tracking-[0.2em] py-3.5 px-4 hover:bg-[#2568A8] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2.5 transition-colors"
                                    >
                                        {processing ? (
                                            <>
                                                <i className="fa-solid fa-spinner fa-spin text-sm"></i>
                                                <span>Mengirim...</span>
                                            </>
                                        ) : (
                                            <>
                                                <span>Kirim Instruksi Reset</span>
                                                <i className="fa-solid fa-paper-plane text-xs"></i>
                                            </>
                                        )}
                                    </button>
                                </form>
                            )}

                            {/* Footer */}
                            <div className="mt-8 pt-6 border-t border-[#E3EAF1] dark:border-[#1B2C44] text-center">
                                <span className="font-mono text-[10px] uppercase tracking-[0.15em] text-[#737373] dark:text-[#3D556E]">Ingat password?{' '}</span>
                                <Link href={route('login')} className="font-mono text-[10px] uppercase tracking-[0.15em] text-[#1D5B96] hover:text-[#164A7C] dark:text-[#6BA8DC] dark:hover:text-[#90BFE0] transition-colors">
                                    Kembali ke Login
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
