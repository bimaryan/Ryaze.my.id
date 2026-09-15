import { useEffect } from 'react';
import { Link, useForm, Head } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function ForgotPassword({ errors, siteName }) {
    const { data, setData, post, processing } = useForm({
        email: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('password.email'));
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
            <style>{`
                [data-reveal] { opacity: 0; transform: translateY(20px); transition: opacity 0.5s ease, transform 0.5s ease; }
                [data-reveal].revealed { opacity: 1; transform: none; }
            `}</style>

            <div className="min-h-screen flex items-center justify-center p-6">
                <div className="max-w-md w-full" data-reveal>
                    <div className="bg-[#7c3aed] dark:bg-[#1a1025] px-8 py-10 text-center relative">
                        <Link href="/" className="inline-flex items-center gap-2.5 mb-6">
                            <div className="w-7 h-7 bg-white flex items-center justify-center">
                                <span className="text-[#7c3aed] font-black text-xs">R</span>
                            </div>
                            <span className="font-black text-white text-sm tracking-tight">RYAZE</span>
                        </Link>
                        <h1 className="text-3xl font-black text-white tracking-tight">Lupa Password?</h1>
                        <p className="text-white/80 mt-2 text-sm">Masukkan email Anda dan kami akan mengirimkan instruksi reset.</p>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-8">
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content} />

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
                                    autoFocus
                                />
                                {errors.email && <p className="mt-1 text-sm text-red-500 dark:text-red-400">{errors.email}</p>}
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
                                    'Kirim Instruksi Reset'
                                )}
                            </button>
                        </form>

                        <div className="mt-8 text-center text-sm text-[#666] dark:text-white/60">
                            Ingat password Anda?
                            <Link href={route('login')} className="font-semibold text-[#7c3aed] hover:text-[#6d28d9] transition-colors ml-1">Kembali ke Login</Link>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
