import { useState, useEffect, useRef } from 'react';
import { Link, useForm, Head } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function ResetPassword({ errors, siteName, token, email, turnstileSiteKey }) {
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const turnstileRef = useRef(null);
    const turnstileWidgetId = useRef(null);
    const [turnstileToken, setTurnstileToken] = useState('');

    const { data, setData, post, processing } = useForm({
        token: token,
        email: email,
        password: '',
        password_confirmation: '',
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
        post(route('password.update'));
    };

    useEffect(() => {
        const els = document.querySelectorAll('[data-reveal]');
        if (!('IntersectionObserver' in window)) { els.forEach(el => el.style.opacity = '1'); return; }
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); } });
        }, { threshold: 0.1 });
        els.forEach(el => io.observe(el));
    }, []);

    const inputBase = 'w-full bg-slate-50 dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#1B2C44] focus:border-[#1D5B96] px-4 py-3 text-sm text-[#0F1A2A] dark:text-white placeholder-[#94A3B4] dark:placeholder-[#3D556E] focus:outline-none focus:ring-1 focus:ring-[#1D5B96] transition-all';
    const inputError = 'w-full bg-slate-50 dark:bg-[#0A1322] border border-rose-500 px-4 py-3 text-sm text-[#0F1A2A] dark:text-white placeholder-[#94A3B4] dark:placeholder-[#3D556E] focus:outline-none transition-all';
    const labelClass = 'block font-mono text-[10px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] mb-2';

    return (
        <PublicLayout title="Reset Password" withNav={false} withFooter={false} bodyClass="bg-slate-50 dark:bg-[#0A1322] font-sans antialiased">
            <Head>
                <meta head-key="robots" name="robots" content="noindex, nofollow" />
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
                        {/* Header */}
                        <div className="bg-[#F0F6FC] dark:bg-[#0F1A2A] border-b border-[#E3EAF1] dark:border-[#1B2C44] px-8 py-6">
                            <p className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] mb-1">PEMULIHAN AKUN</p>
                            <h1 className="text-xl font-black text-[#0F1A2A] dark:text-white tracking-tight">Buat Password Baru</h1>
                            <p className="text-[#525252] dark:text-[#8A9BAE] text-sm mt-1">Masukkan kata sandi baru untuk akun Anda</p>
                        </div>

                        {/* Form */}
                        <div className="p-8">
                            <form onSubmit={handleSubmit} className="space-y-5">
                                <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content} />
                                <input type="hidden" name="token" value={data.token} />
                                <input type="hidden" name="email" value={data.email} />
                                <input type="hidden" name="cf-turnstile-response" value={data['cf-turnstile-response']} />

                                <div>
                                    <label htmlFor="password" className={labelClass}>Password Baru</label>
                                    <div className="relative">
                                        <input
                                            type={showPassword ? 'text' : 'password'}
                                            id="password"
                                            name="password"
                                            value={data.password}
                                            onChange={(e) => setData('password', e.target.value)}
                                            className={`pr-12 ${errors.password ? inputError : inputBase}`}
                                            placeholder="Min. 8 karakter"
                                            autoFocus
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowPassword(!showPassword)}
                                            className="absolute inset-y-0 right-0 pr-3 flex items-center text-[#94A3B4] dark:text-[#3D556E] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors"
                                        >
                                            <i className={`fa-solid ${showPassword ? 'fa-eye-slash' : 'fa-eye'} text-sm`}></i>
                                        </button>
                                    </div>
                                    <p className="mt-1.5 font-mono text-[9px] text-[#94A3B4] dark:text-[#3D556E] leading-relaxed">
                                        Min. 8 karakter · kombinasi huruf, angka, dan simbol
                                    </p>
                                    {errors.password && (
                                        <ul className="mt-1.5 text-xs text-rose-600 dark:text-rose-400 list-disc list-inside space-y-0.5">
                                            {Array.isArray(errors.password) ? errors.password.map((e, i) => <li key={i}>{e}</li>) : <li>{errors.password}</li>}
                                        </ul>
                                    )}
                                </div>

                                <div>
                                    <label htmlFor="password_confirmation" className={labelClass}>Konfirmasi Password Baru</label>
                                    <div className="relative">
                                        <input
                                            type={showConfirmPassword ? 'text' : 'password'}
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            value={data.password_confirmation}
                                            onChange={(e) => setData('password_confirmation', e.target.value)}
                                            className={`pr-12 ${errors.password_confirmation ? inputError : inputBase}`}
                                            placeholder="Ulangi password baru"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                            className="absolute inset-y-0 right-0 pr-3 flex items-center text-[#94A3B4] dark:text-[#3D556E] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors"
                                        >
                                            <i className={`fa-solid ${showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'} text-sm`}></i>
                                        </button>
                                    </div>
                                    {errors.password_confirmation && <p className="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{errors.password_confirmation}</p>}
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
                                            <span>Memproses...</span>
                                        </>
                                    ) : (
                                        <>
                                            <span>Simpan Password Baru</span>
                                            <i className="fa-solid fa-arrow-right text-xs"></i>
                                        </>
                                    )}
                                </button>
                            </form>

                            <div className="mt-8 pt-6 border-t border-[#E3EAF1] dark:border-[#1B2C44] text-center">
                                <Link href={route('login')} className="font-mono text-[10px] uppercase tracking-[0.15em] text-[#1D5B96] hover:text-[#164A7C] dark:text-[#6BA8DC] dark:hover:text-[#90BFE0] transition-colors inline-flex items-center gap-2">
                                    <i className="fa-solid fa-arrow-left text-xs"></i>
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
