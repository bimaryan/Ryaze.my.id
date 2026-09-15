import { useState, useEffect } from 'react';
import { Link, useForm, Head } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function ResetPassword({ errors, siteName }) {
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);

    const { data, setData, post, processing } = useForm({
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
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

    return (
        <PublicLayout title="Reset Password" withNav={false} withFooter={false} bodyClass="bg-[#fafafa] dark:bg-[#0a0a14] font-sans antialiased">
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
                        <h1 className="text-3xl font-black text-white tracking-tight">Buat Password Baru</h1>
                        <p className="text-white/80 mt-2 text-sm">Masukkan password baru Anda untuk akun.</p>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-8">
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content} />

                            <div>
                                <label htmlFor="password" className="block text-sm font-medium text-[#7c3aed] dark:text-white mb-2">Password Baru</label>
                                <div className="relative">
                                    <input
                                        type={showPassword ? 'text' : 'password'}
                                        id="password"
                                        name="password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        className={`w-full pr-12 bg-[#fafafa] dark:bg-[#0a0a14] border ${errors.password ? 'border-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42]'} px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] transition-all`}
                                        placeholder="Min. 8 karakter"
                                        autoFocus
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
                                <label htmlFor="password_confirmation" className="block text-sm font-medium text-[#7c3aed] dark:text-white mb-2">Konfirmasi Password Baru</label>
                                <div className="relative">
                                    <input
                                        type={showConfirmPassword ? 'text' : 'password'}
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        value={data.password_confirmation}
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        className={`w-full pr-12 bg-[#fafafa] dark:bg-[#0a0a14] border ${errors.password_confirmation ? 'border-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42]'} px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] transition-all`}
                                        placeholder="Ulangi password baru"
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

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-[#7c3aed] text-white font-semibold py-3 px-4 hover:bg-[#6d28d9] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                {processing ? (
                                    <>
                                        <i className="fa-solid fa-spinner fa-spin text-sm"></i>
                                        <span>Memproses...</span>
                                    </>
                                ) : (
                                    'Reset Password'
                                )}
                            </button>
                        </form>

                        <div className="mt-8 text-center text-sm text-[#666] dark:text-white/60">
                            <Link href={route('login')} className="font-semibold text-[#7c3aed] hover:text-[#6d28d9] transition-colors">Kembali ke Login</Link>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
