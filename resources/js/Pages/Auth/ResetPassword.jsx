import { useState, useEffect, useRef } from 'react';
import { Link, useForm, Head } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function ResetPassword({ errors, siteName, token, email, turnstileSiteKey }) {
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const [turnstileToken, setTurnstileToken] = useState('');

    const { data, setData, post, processing } = useForm({
        token: token,
        email: email,
        password: '',
        password_confirmation: '',
        'cf-turnstile-response': '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        setData('cf-turnstile-response', turnstileToken);
        post(route('password.update'));
    };

    useEffect(() => {
        window.onTurnstileSuccess = (token) => setTurnstileToken(token);
        return () => { delete window.onTurnstileSuccess; };
    }, []);

    useEffect(() => {
        const els = document.querySelectorAll('[data-reveal]');
        if (!('IntersectionObserver' in window)) { els.forEach(el => el.style.opacity = '1'); return; }
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); } });
        }, { threshold: 0.1 });
        els.forEach(el => io.observe(el));
    }, []);

    return (
        <PublicLayout title="Reset Password" withNav={false} withFooter={false} bodyClass="bg-[#fafafa] dark:bg-[#0a0a14] font-sans antialiased">
            <Head>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer />
            </Head>

            <style>{`
                [data-reveal] { opacity: 0; transform: translateY(20px); transition: opacity 0.5s ease, transform 0.5s ease; }
                [data-reveal].revealed { opacity: 1; transform: none; }
            `}</style>

            <div className="min-h-screen flex items-center justify-center p-6">
                <div className="max-w-md w-full" data-reveal>
                    <div className="bg-[#1a1025] dark:bg-[#1a1025] px-8 py-10 text-center rounded-t-2xl">
                        <Link href="/" className="inline-flex items-center gap-2.5 mb-6">
                            <div className="w-7 h-7 bg-[#7c3aed] flex items-center justify-center">
                                <span className="text-white font-black text-xs">R</span>
                            </div>
                            <span className="font-black text-white text-sm tracking-tight">RYAZE</span>
                        </Link>
                        <h1 className="text-3xl font-black text-white tracking-tight">{siteName ?? 'Ryaze Portal'}</h1>
                        <p className="text-[#a78bfa] mt-2 text-sm">Silakan masukkan password baru untuk akun Anda.</p>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-b-2xl p-8">
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content} />
                            <input type="hidden" name="token" value={token} />
                            <input type="hidden" name="email" value={email} />

                            <div>
                                <label htmlFor="password" className="block text-sm font-medium text-[#1a1025] dark:text-white mb-2">Password Baru</label>
                                <div className="relative">
                                    <input
                                        type={showPassword ? 'text' : 'password'}
                                        id="password"
                                        name="password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        className={`w-full pr-12 bg-[#fafafa] dark:bg-[#0a0a14] border ${errors.password ? 'border-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42]'} rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] transition-all`}
                                        placeholder="••••••••"
                                        required
                                        autoFocus
                                        autoComplete="new-password"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(!showPassword)}
                                        className="absolute inset-y-0 right-0 pr-3 flex items-center text-[#999] dark:text-white/50 hover:text-[#1a1025] dark:hover:text-white transition-colors"
                                    >
                                        <i className={`fa-solid ${showPassword ? 'fa-eye-slash' : 'fa-eye'} text-sm`}></i>
                                    </button>
                                </div>
                                <p className="mt-2 text-xs text-[#999] dark:text-white/50">
                                    Gunakan minimal 8 karakter dengan kombinasi huruf besar & kecil, angka, dan simbol.
                                </p>
                                {errors.password && <p className="mt-1 text-sm text-red-500 dark:text-red-400">{errors.password}</p>}
                            </div>

                            <div>
                                <label htmlFor="password_confirmation" className="block text-sm font-medium text-[#1a1025] dark:text-white mb-2">Konfirmasi Password Baru</label>
                                <div className="relative">
                                    <input
                                        type={showConfirmPassword ? 'text' : 'password'}
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        value={data.password_confirmation}
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        className={`w-full pr-12 bg-[#fafafa] dark:bg-[#0a0a14] border ${errors.password_confirmation ? 'border-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42]'} rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] transition-all`}
                                        placeholder="••••••••"
                                        required
                                        autoComplete="new-password"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                        className="absolute inset-y-0 right-0 pr-3 flex items-center text-[#999] dark:text-white/50 hover:text-[#1a1025] dark:hover:text-white transition-colors"
                                    >
                                        <i className={`fa-solid ${showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'} text-sm`}></i>
                                    </button>
                                </div>
                                {errors.password_confirmation && <p className="mt-1 text-sm text-red-500 dark:text-red-400">{errors.password_confirmation}</p>}
                            </div>

                            {errors.captcha && <p className="text-sm text-red-500 dark:text-red-400 text-center">{errors.captcha}</p>}

                            <div className="flex justify-center">
                                <div
                                    className="cf-turnstile"
                                    data-sitekey={turnstileSiteKey || ''}
                                    data-callback="onTurnstileSuccess"
                                    data-theme="auto"
                                ></div>
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-[#7c3aed] text-white font-semibold py-3 px-4 rounded-lg hover:bg-[#6d28d9] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                {processing ? (
                                    <>
                                        <i className="fa-solid fa-spinner fa-spin text-sm"></i>
                                        <span>Memproses...</span>
                                    </>
                                ) : (
                                    <>
                                        <span>Reset Password</span>
                                        <i className="fa-solid fa-check text-sm"></i>
                                    </>
                                )}
                            </button>
                        </form>

                        {errors.form && <p className="mt-4 text-sm text-red-500 dark:text-red-400 text-center">{errors.form}</p>}
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}