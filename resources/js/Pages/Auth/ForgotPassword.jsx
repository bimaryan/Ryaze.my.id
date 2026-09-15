import { useState, useEffect } from 'react';
import { Link, useForm, Head } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function ForgotPassword({ errors, siteName, status, turnstileSiteKey }) {
    const [turnstileToken, setTurnstileToken] = useState('');

    const { data, setData, post, processing } = useForm({
        email: '',
        'cf-turnstile-response': '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        setData('cf-turnstile-response', turnstileToken);
        post(route('password.email'));
    };

    const onTurnstileSuccess = (token) => {
        setTurnstileToken(token);
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
        <PublicLayout title="Lupa Password" withNav={false} withFooter={false} bodyClass="bg-[#fafafa] dark:bg-[#0a0a14] font-sans antialiased">
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
                        <Link
                            href={route('login')}
                            className="absolute top-4 left-4 text-[#a78bfa] hover:text-white transition"
                        >
                            <i className="fa-solid fa-arrow-left"></i>
                        </Link>
                        <Link href="/" className="inline-flex items-center gap-2.5 mb-6">
                            <div className="w-7 h-7 bg-[#7c3aed] flex items-center justify-center">
                                <span className="text-white font-black text-xs">R</span>
                            </div>
                            <span className="font-black text-white text-sm tracking-tight">RYAZE</span>
                        </Link>
                        <h1 className="text-3xl font-black text-white tracking-tight">{siteName ?? 'Ryaze Portal'}</h1>
                        <p className="text-[#a78bfa] mt-2 text-sm">Masukkan email Anda untuk menerima link reset password.</p>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-b-2xl p-8">
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
                                    className={`w-full bg-[#fafafa] dark:bg-[#0a0a14] border ${errors.email ? 'border-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42]'} rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] transition-all`}
                                    placeholder="nama@email.com"
                                    autoComplete="email"
                                    required
                                    autoFocus
                                />
                                {errors.email && <p className="mt-1 text-sm text-red-500 dark:text-red-400">{errors.email}</p>}
                            </div>

                            {errors.captcha && <p className="text-sm text-red-500 dark:text-red-400 text-center">{errors.captcha}</p>}

                            <div className="flex justify-center">
                                <div
                                    className="cf-turnstile"
                                    data-sitekey={turnstileSiteKey || ''}
                                    data-callback={onTurnstileSuccess}
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
                                        <span>Mengirim...</span>
                                    </>
                                ) : (
                                    <>
                                        <span>Kirim Link Reset</span>
                                        <i className="fa-solid fa-paper-plane text-sm"></i>
                                    </>
                                )}
                            </button>
                        </form>

                        {errors.form && <p className="mt-4 text-sm text-red-500 dark:text-red-400 text-center">{errors.form}</p>}

                        <div className="mt-8 text-center text-sm text-[#666] dark:text-white/60">
                            Ingat password Anda?
                            <Link href={route('login')} className="font-semibold text-[#7c3aed] hover:text-[#6d28d9] transition-colors ml-1">Masuk di sini</Link>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}