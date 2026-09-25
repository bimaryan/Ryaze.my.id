import { useState, useEffect, useRef } from 'react';
import { Link, useForm, Head } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function Register({ errors, siteName, turnstileSiteKey }) {
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const turnstileRef = useRef(null);
    const turnstileWidgetId = useRef(null);
    const [turnstileToken, setTurnstileToken] = useState('');

    const { data, setData, post, processing } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: 'user_hosting',
        ref: new URLSearchParams(window.location.search).get('ref') || '',
        consultation_token: new URLSearchParams(window.location.search).get('consultation_token') || '',
        'cf-turnstile-response': '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        setData('cf-turnstile-response', turnstileToken);
        post(route('register.process'));
    };

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

        if (window.turnstile) {
            renderWidget();
        } else {
            const interval = setInterval(() => {
                if (window.turnstile) {
                    clearInterval(interval);
                    renderWidget();
                }
            }, 100);
            return () => clearInterval(interval);
        }

        return () => {
            if (turnstileWidgetId.current) {
                try { window.turnstile.remove(turnstileWidgetId.current); } catch (e) {}
                turnstileWidgetId.current = null;
            }
        };
    }, [turnstileSiteKey]);

    useEffect(() => {
        const els = document.querySelectorAll('[data-reveal]');
        if (!('IntersectionObserver' in window)) { els.forEach(el => el.style.opacity = '1'); return; }
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); } });
        }, { threshold: 0.1 });
        els.forEach(el => io.observe(el));
    }, []);

    return (
        <PublicLayout title="Daftar" withNav={false} withFooter={false} bodyClass="bg-[#fafafa] dark:bg-[#0a0a14] font-sans antialiased">
            <Head>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer />
            </Head>

            <style>{`
                [data-reveal] { opacity: 0; transform: translateY(20px); transition: opacity 0.5s ease, transform 0.5s ease; }
                [data-reveal].revealed { opacity: 1; transform: none; }
            `}</style>

            <div className="min-h-screen flex items-center justify-center p-6">
                <div className="max-w-md w-full" data-reveal>
                    <div className="bg-[#7c3aed] dark:bg-[#1a1025] px-8 py-10 text-center">
                        <Link href="/" className="inline-flex items-center gap-2.5 mb-6">
                            <div className="w-7 h-7 bg-white flex items-center justify-center">
                                <span className="text-[#7c3aed] font-black text-xs">R</span>
                            </div>
                            <span className="font-black text-white text-sm tracking-tight">RYAZE</span>
                        </Link>
                        <h1 className="text-3xl font-black text-white tracking-tight">Buat Akun</h1>
                        <p className="text-white/80 mt-2 text-sm">Daftar untuk mulai pesan Joki atau Hosting</p>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-8">
                        <form onSubmit={handleSubmit} className="space-y-5">
                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content} />
                            {data.ref && <input type="hidden" name="ref" value={data.ref} />}
                            {data.consultation_token && <input type="hidden" name="consultation_token" value={data.consultation_token} />}

                            <div>
                                <label htmlFor="name" className="block text-sm font-medium text-[#7c3aed] dark:text-white mb-2">Nama Lengkap</label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    className={`w-full bg-[#fafafa] dark:bg-[#0a0a14] border ${errors.name ? 'border-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42]'} px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] transition-all`}
                                    placeholder="John Doe"
                                    autoFocus
                                />
                                {errors.name && <p className="mt-1 text-sm text-red-500 dark:text-red-400">{errors.name}</p>}
                            </div>

                            <div>
                                <label htmlFor="email" className="block text-sm font-medium text-[#7c3aed] dark:text-white mb-2">Email Address</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    className={`w-full bg-[#fafafa] dark:bg-[#0a0a14] border ${errors.email ? 'border-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42]'} px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] transition-all`}
                                    placeholder="nama@email.com"
                                    autoComplete="email"
                                />
                                {errors.email && <p className="mt-1 text-sm text-red-500 dark:text-red-400">{errors.email}</p>}
                            </div>

                            <div>
                                <label htmlFor="password" className="block text-sm font-medium text-[#7c3aed] dark:text-white mb-2">Password</label>
                                <div className="relative">
                                    <input
                                        type={showPassword ? 'text' : 'password'}
                                        id="password"
                                        name="password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        className={`w-full pr-12 bg-[#fafafa] dark:bg-[#0a0a14] border ${errors.password ? 'border-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42]'} px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] transition-all`}
                                        placeholder="Min. 8 karakter"
                                        autoComplete="new-password"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(!showPassword)}
                                        className="absolute inset-y-0 right-0 pr-3 flex items-center text-[#999] dark:text-white/50 hover:text-[#7c3aed] dark:hover:text-white transition-colors"
                                    >
                                        <i className={`fa-solid ${showPassword ? 'fa-eye-slash' : 'fa-eye'} text-sm`}></i>
                                    </button>
                                </div>
                                <p className="mt-2 text-xs text-[#999] dark:text-white/50">
                                    Gunakan minimal 8 karakter dengan kombinasi huruf besar & kecil, angka, dan simbol.
                                </p>
                                {errors.password && (
                                    <ul className="mt-2 text-xs text-red-500 dark:text-red-400 list-disc list-inside space-y-1">
                                        {Array.isArray(errors.password) ? errors.password.map((e, i) => <li key={i}>{e}</li>) : <li>{errors.password}</li>}
                                    </ul>
                                )}
                            </div>

                            <div>
                                <label htmlFor="password_confirmation" className="block text-sm font-medium text-[#7c3aed] dark:text-white mb-2">Konfirmasi Password</label>
                                <div className="relative">
                                    <input
                                        type={showConfirmPassword ? 'text' : 'password'}
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        value={data.password_confirmation}
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        className={`w-full pr-12 bg-[#fafafa] dark:bg-[#0a0a14] border ${errors.password_confirmation ? 'border-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42]'} px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] transition-all`}
                                        placeholder="Ulangi password"
                                        autoComplete="new-password"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                        className="absolute inset-y-0 right-0 pr-3 flex items-center text-[#999] dark:text-white/50 hover:text-[#7c3aed] dark:hover:text-white transition-colors"
                                    >
                                        <i className={`fa-solid ${showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'} text-sm`}></i>
                                    </button>
                                </div>
                                {errors.password_confirmation && <p className="mt-1 text-sm text-red-500 dark:text-red-400">{errors.password_confirmation}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-[#7c3aed] dark:text-white mb-2">Pilih Layanan Utama</label>
                                <div className="grid grid-cols-2 gap-4">
                                    <label
                                        className={`relative flex items-center justify-center p-3 border cursor-pointer transition-colors has-[:checked]:border-[#7c3aed] has-[:checked]:bg-[#f5f0ff] dark:has-[:checked]:bg-[#7c3aed]/10 has-[:checked]:ring-1 has-[:checked]:ring-[#7c3aed] ${errors.role ? 'border-red-500 ring-1 ring-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42] bg-white dark:bg-[#0a0a14] hover:bg-[#fafafa] dark:hover:bg-[#1a1a2e]'}`}
                                    >
                                        <input
                                            type="radio"
                                            name="role"
                                            value="user_joki"
                                            checked={data.role === 'user_joki'}
                                            onChange={(e) => setData('role', e.target.value)}
                                            className="peer sr-only"
                                        />
                                        <span className="text-sm font-medium text-[#666] dark:text-white/60 peer-checked:text-[#7c3aed]">Jasa Joki</span>
                                    </label>

                                    <label
                                        className={`relative flex items-center justify-center p-3 border cursor-pointer transition-colors has-[:checked]:border-[#7c3aed] has-[:checked]:bg-[#f5f0ff] dark:has-[:checked]:bg-[#7c3aed]/10 has-[:checked]:ring-1 has-[:checked]:ring-[#7c3aed] ${errors.role ? 'border-red-500 ring-1 ring-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42] bg-white dark:bg-[#0a0a14] hover:bg-[#fafafa] dark:hover:bg-[#1a1a2e]'}`}
                                    >
                                        <input
                                            type="radio"
                                            name="role"
                                            value="user_hosting"
                                            checked={data.role === 'user_hosting'}
                                            onChange={(e) => setData('role', e.target.value)}
                                            className="peer sr-only"
                                        />
                                        <span className="text-sm font-medium text-[#666] dark:text-white/60 peer-checked:text-[#7c3aed]">Beli Hosting</span>
                                    </label>
                                </div>
                                {errors.role && <p className="mt-1 text-sm text-red-500 dark:text-red-400">{errors.role}</p>}
                            </div>

                            {errors.captcha && <p className="text-sm text-red-500 dark:text-red-400 text-center">{errors.captcha}</p>}

                            <div className="flex justify-center">
                                <div ref={turnstileRef}></div>
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-[#7c3aed] text-white font-semibold py-3 px-4 hover:bg-[#6d28d9] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 mt-2"
                            >
                                {processing ? (
                                    <>
                                        <i className="fa-solid fa-spinner fa-spin text-sm"></i>
                                        <span>Memproses...</span>
                                    </>
                                ) : (
                                    'Daftar Sekarang'
                                )}
                            </button>

                            <div className="relative flex items-center py-2">
                                <div className="flex-grow border-t border-[#e5e5e5] dark:border-[#2d1f42]"></div>
                                <span className="flex-shrink-0 mx-4 text-[#999] dark:text-white/50 text-sm">Atau daftar dengan</span>
                                <div className="flex-grow border-t border-[#e5e5e5] dark:border-[#2d1f42]"></div>
                            </div>

                            <a
                                href={route('auth.google')}
                                className="w-full bg-white dark:bg-[#1a1a2e] border border-[#e5e5e5] dark:border-[#2d1f42] hover:bg-[#fafafa] dark:hover:bg-[#252542] text-[#333] dark:text-white font-semibold py-3 px-4 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 ease-in-out flex items-center justify-center gap-3"
                            >
                                <svg className="w-5 h-5" viewBox="0 0 24 24">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                                </svg>
                                Google
                            </a>
                        </form>

                        {errors.form && <p className="mt-4 text-sm text-red-500 dark:text-red-400 text-center">{errors.form}</p>}

                        <div className="mt-8 text-center text-sm text-[#666] dark:text-white/60">
                            Sudah punya akun?
                            <Link href={route('login')} className="font-semibold text-[#7c3aed] hover:text-[#6d28d9] transition-colors ml-1">Masuk di sini</Link>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
