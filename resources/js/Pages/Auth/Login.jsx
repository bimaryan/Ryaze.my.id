import { useState, useEffect, useRef } from 'react';
import { Link, useForm, Head } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function Login({ errors, siteName, turnstileSiteKey }) {
    const [showPassword, setShowPassword] = useState(false);
    const turnstileRef = useRef(null);
    const turnstileWidgetId = useRef(null);
    const [turnstileToken, setTurnstileToken] = useState('');

    const { data, setData, post, processing } = useForm({
        email: '',
        password: '',
        remember: false,
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
        post(route('login.process'));
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
        <PublicLayout title="Masuk" withNav={false} withFooter={false} bodyClass="bg-slate-50 dark:bg-[#0A1322] font-sans antialiased">
            <Head>
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
                            <p className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] mb-1">PORTAL AKSES</p>
                            <h1 className="text-xl font-black text-[#0F1A2A] dark:text-white tracking-tight">Masuk ke Akun</h1>
                            <p className="text-[#525252] dark:text-[#8A9BAE] text-sm mt-1">Kelola Joki & Hosting Anda</p>
                        </div>

                        {/* Form */}
                        <div className="p-8">
                            <form onSubmit={handleSubmit} className="space-y-5">
                                <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content} />

                                {/* Email */}
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
                                        autoComplete="email"
                                        autoFocus
                                    />
                                    {errors.email && <p className="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{errors.email}</p>}
                                </div>

                                {/* Password */}
                                <div>
                                    <div className="flex items-center justify-between mb-2">
                                        <label htmlFor="password" className="block font-mono text-[10px] uppercase tracking-[0.2em] text-[#6BA8DC]">
                                            Password
                                        </label>
                                        <Link href={route('password.request')} className="font-mono text-[9px] uppercase tracking-[0.15em] text-[#737373] dark:text-[#4A7A9B] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">
                                            Lupa?
                                        </Link>
                                    </div>
                                    <div className="relative">
                                        <input
                                            type={showPassword ? 'text' : 'password'}
                                            id="password"
                                            name="password"
                                            value={data.password}
                                            onChange={(e) => setData('password', e.target.value)}
                                            className={`w-full pr-12 bg-slate-50 dark:bg-[#0A1322] border ${errors.password ? 'border-rose-500' : 'border-[#E3EAF1] dark:border-[#1B2C44] focus:border-[#1D5B96]'} px-4 py-3 text-sm text-[#0F1A2A] dark:text-white placeholder-[#94A3B4] dark:placeholder-[#3D556E] focus:outline-none focus:ring-1 focus:ring-[#1D5B96] transition-all`}
                                            placeholder="••••••••"
                                            autoComplete="current-password"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowPassword(!showPassword)}
                                            className="absolute inset-y-0 right-0 pr-3 flex items-center text-[#94A3B4] dark:text-[#3D556E] hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors"
                                        >
                                            <i className={`fa-solid ${showPassword ? 'fa-eye-slash' : 'fa-eye'} text-sm`}></i>
                                        </button>
                                    </div>
                                    {errors.password && <p className="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{errors.password}</p>}
                                </div>

                                {/* Captcha error */}
                                {errors.captcha && <p className="text-xs text-rose-600 dark:text-rose-400 text-center">{errors.captcha}</p>}

                                {/* Turnstile */}
                                <div className="flex justify-center">
                                    <div ref={turnstileRef}></div>
                                </div>

                                {/* Remember */}
                                <div className="flex items-center gap-2.5">
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        id="remember"
                                        checked={data.remember}
                                        onChange={(e) => setData('remember', e.target.checked)}
                                        className="h-4 w-4 border-[#C9D5E2] dark:bg-[#0A1322] dark:border-[#1B2C44] text-[#1D5B96] focus:ring-[#1D5B96] cursor-pointer"
                                    />
                                    <label htmlFor="remember" className="font-mono text-[10px] uppercase tracking-[0.15em] text-[#737373] dark:text-[#4A7A9B] cursor-pointer">
                                        Ingat saya
                                    </label>
                                </div>

                                {/* Submit */}
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
                                            <span>Masuk ke Dashboard</span>
                                            <i className="fa-solid fa-arrow-right text-xs"></i>
                                        </>
                                    )}
                                </button>

                                {/* Divider */}
                                <div className="relative flex items-center gap-3 py-1">
                                    <div className="flex-grow border-t border-[#E3EAF1] dark:border-[#1B2C44]"></div>
                                    <span className="font-mono text-[9px] uppercase tracking-[0.2em] text-[#94A3B4] dark:text-[#3D556E]">atau</span>
                                    <div className="flex-grow border-t border-[#E3EAF1] dark:border-[#1B2C44]"></div>
                                </div>

                                {/* Google OAuth */}
                                <a
                                    href={route('auth.google')}
                                    className="w-full bg-white dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#C9D5E2] dark:hover:border-[#24384F] hover:bg-[#F0F6FC] dark:hover:bg-[#0F1A2A] text-[#0F1A2A] dark:text-[#C9D5E2] font-mono text-[10px] uppercase tracking-[0.15em] py-3 px-4 flex items-center justify-center gap-3 transition-all"
                                >
                                    <svg className="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                                    </svg>
                                    Masuk dengan Google
                                </a>
                            </form>

                            {errors.form && <p className="mt-4 text-xs text-rose-600 dark:text-rose-400 text-center">{errors.form}</p>}

                            {/* Footer link */}
                            <div className="mt-8 pt-6 border-t border-[#E3EAF1] dark:border-[#1B2C44] text-center">
                                <span className="font-mono text-[10px] uppercase tracking-[0.15em] text-[#737373] dark:text-[#3D556E]">Belum punya akun?{' '}</span>
                                <Link href={route('register')} className="font-mono text-[10px] uppercase tracking-[0.15em] text-[#1D5B96] hover:text-[#164A7C] dark:text-[#6BA8DC] dark:hover:text-[#90BFE0] transition-colors">
                                    Daftar sekarang
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
