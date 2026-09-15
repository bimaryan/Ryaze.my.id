import { useState, useEffect, useRef } from 'react';
import { Link, useForm, Head } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function Login({ errors, siteName, turnstileSiteKey }) {
    const [showPassword, setShowPassword] = useState(false);
    const turnstileRef = useRef(null);
    const [turnstileToken, setTurnstileToken] = useState('');

    const { data, setData, post, processing } = useForm({
        email: '',
        password: '',
        remember: false,
        'cf-turnstile-response': '',
    });

    useEffect(() => {
        window.onTurnstileSuccess = (token) => setTurnstileToken(token);
        return () => { delete window.onTurnstileSuccess; };
    }, []);

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
        <PublicLayout title="Login" withNav={false} withFooter={false} bodyClass="bg-[#fafafa] dark:bg-[#0a0a14] font-sans antialiased">
            <Head>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer />
            </Head>

            <style>{`
                [data-reveal] { opacity: 0; transform: translateY(20px); transition: opacity 0.5s ease, transform 0.5s ease; }
                [data-reveal].revealed { opacity: 1; transform: none; }
            `}</style>

            <div className="min-h-screen flex items-center justify-center p-6">
                <div className="max-w-md w-full" data-reveal>
                    <div className="bg-[#1a1025] dark:bg-[#1a1025] px-8 py-10 text-center">
                        <Link href="/" className="inline-flex items-center gap-2.5 mb-6">
                            <div className="w-7 h-7 bg-[#7c3aed] flex items-center justify-center">
                                <span className="text-white font-black text-xs">R</span>
                            </div>
                            <span className="font-black text-white text-sm tracking-tight">RYAZE</span>
                        </Link>
                        <h1 className="text-3xl font-black text-white tracking-tight">{siteName ?? 'Ryaze Portal'}</h1>
                        <p className="text-[#a78bfa] mt-2 text-sm">Masuk untuk mengelola Joki & Hosting Anda</p>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-8">
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content} />

                            <div>
                                <label htmlFor="email" className="block text-sm font-medium text-[#1a1025] dark:text-white mb-2">Email Address</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    className={`w-full bg-[#fafafa] dark:bg-[#0a0a14] border ${errors.email ? 'border-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42]'} px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] transition-all`}
                                    placeholder="nama@email.com"
                                    autoComplete="email"
                                    autoFocus
                                />
                                {errors.email && <p className="mt-1 text-sm text-red-500 dark:text-red-400">{errors.email}</p>}
                            </div>

                            <div>
                                <div className="flex items-center justify-between mb-2">
                                    <label htmlFor="password" className="block text-sm font-medium text-[#1a1025] dark:text-white">Password</label>
                                    <Link href={route('password.request')} className="text-sm font-medium text-[#7c3aed] hover:text-[#6d28d9] transition-colors">Lupa password?</Link>
                                </div>
                                <div className="relative">
                                    <input
                                        type={showPassword ? 'text' : 'password'}
                                        id="password"
                                        name="password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        className={`w-full pr-12 bg-[#fafafa] dark:bg-[#0a0a14] border ${errors.password ? 'border-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42]'} px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] transition-all`}
                                        placeholder="********"
                                        autoComplete="current-password"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(!showPassword)}
                                        className="absolute inset-y-0 right-0 pr-3 flex items-center text-[#999] dark:text-white/50 hover:text-[#1a1025] dark:hover:text-white transition-colors"
                                    >
                                        <i className={`fa-solid ${showPassword ? 'fa-eye-slash' : 'fa-eye'} text-sm`}></i>
                                    </button>
                                </div>
                                {errors.password && <p className="mt-1 text-sm text-red-500 dark:text-red-400">{errors.password}</p>}
                            </div>

                            {errors.captcha && <p className="text-sm text-red-500 dark:text-red-400 text-center">{errors.captcha}</p>}

                            <div className="flex justify-center">
                                <div
                                    ref={turnstileRef}
                                    className="cf-turnstile"
                                    data-sitekey={turnstileSiteKey || ''}
                                    data-callback="onTurnstileSuccess"
                                    data-theme="auto"
                                ></div>
                            </div>

                            <div className="flex items-center">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    id="remember"
                                    checked={data.remember}
                                    onChange={(e) => setData('remember', e.target.checked)}
                                    className="h-4 w-4 text-[#7c3aed] focus:ring-[#7c3aed] border-[#e5e5e5] dark:border-[#2d1f42] cursor-pointer"
                                />
                                <label htmlFor="remember" className="ml-2 block text-sm text-[#666] dark:text-white/60 cursor-pointer">
                                    Ingat saya
                                </label>
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-[#1a1025] dark:bg-white dark:text-[#1a1025] text-white font-semibold py-3 px-4 hover:bg-[#2d1f42] dark:hover:bg-slate-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                {processing ? (
                                    <>
                                        <i className="fa-solid fa-spinner fa-spin text-sm"></i>
                                        <span>Memproses...</span>
                                    </>
                                ) : (
                                    'Masuk ke Dashboard'
                                )}
                            </button>
                        </form>

                        {errors.form && <p className="mt-4 text-sm text-red-500 dark:text-red-400 text-center">{errors.form}</p>}

                        <div className="mt-8 text-center text-sm text-[#666] dark:text-white/60">
                            Belum punya akun?
                            <Link href={route('register')} className="font-semibold text-[#7c3aed] hover:text-[#6d28d9] transition-colors ml-1">Daftar sekarang</Link>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}