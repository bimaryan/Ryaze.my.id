import { Link, usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';

const navLinks = [
    { label: 'Tentang', href: '/#about' },
    { label: 'Layanan', href: '/#services' },
    { label: 'Harga', href: '/#pricing' },
    { label: 'Blog', href: '/blog' },
];

function ThemeToggle() {
    const [dark, setDark] = useState(() => {
        if (typeof window !== 'undefined') {
            const stored = localStorage.getItem('ryaze-theme');
            if (stored) return stored === 'dark';
            return window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        return false;
    });

    useEffect(() => {
        document.documentElement.classList.toggle('dark', dark);
        document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
        localStorage.setItem('ryaze-theme', dark ? 'dark' : 'light');
    }, [dark]);

    return (
        <button onClick={() => setDark(!dark)} className="p-2 rounded-lg bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 transition-colors">
            <i className={`fa-solid ${dark ? 'fa-sun' : 'fa-moon'} text-sm text-slate-700 dark:text-slate-200`}></i>
        </button>
    );
}

export default function PublicLayout({ children, title, description }) {
    const [scrolled, setScrolled] = useState(false);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 20);
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    return (
        <div className="min-h-screen flex flex-col">
            {/* Navbar */}
            <nav className={`fixed top-0 inset-x-0 z-50 transition-all duration-300 ${scrolled ? 'bg-white/80 dark:bg-slate-950/80 backdrop-blur-xl border-b border-slate-200/50 dark:border-white/5 shadow-sm' : ''}`}>
                <div className="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                    <Link href="/" className="flex items-center gap-2">
                        <div className="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <span className="text-white font-bold text-sm">R</span>
                        </div>
                        <span className="font-bold text-slate-900 dark:text-white">Ryaze</span>
                    </Link>
                    <div className="hidden md:flex items-center gap-8">
                        {navLinks.map((l) => (
                            <a key={l.href} href={l.href} className="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">{l.label}</a>
                        ))}
                    </div>
                    <div className="flex items-center gap-3">
                        <ThemeToggle />
                        <Link href="/login" className="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">Masuk</Link>
                        <Link href="/register" className="text-sm font-semibold px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Daftar</Link>
                    </div>
                </div>
            </nav>

            {/* Content */}
            {children}

            {/* Footer */}
            <footer className="border-t border-slate-200 dark:border-white/5 py-12 bg-white dark:bg-slate-950">
                <div className="max-w-7xl mx-auto px-6">
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
                        <div className="col-span-2 md:col-span-1">
                            <div className="flex items-center gap-2 mb-4">
                                <div className="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                                    <span className="text-white font-bold text-sm">R</span>
                                </div>
                                <span className="font-bold text-slate-900 dark:text-white">Ryaze</span>
                            </div>
                            <p className="text-sm text-slate-500 dark:text-slate-400">Platform hosting & development modern untuk bisnis Anda.</p>
                        </div>
                        <div>
                            <h4 className="font-semibold text-slate-900 dark:text-white text-sm mb-4">Layanan</h4>
                            <ul className="space-y-2 text-sm text-slate-500 dark:text-slate-400">
                                <li><a href="/#services" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Web Development</a></li>
                                <li><a href="/#pricing" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Hosting</a></li>
                                <li><a href="/consultation" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Konsultasi</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-semibold text-slate-900 dark:text-white text-sm mb-4">Perusahaan</h4>
                            <ul className="space-y-2 text-sm text-slate-500 dark:text-slate-400">
                                <li><a href="/#about" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Tentang</a></li>
                                <li><a href="/blog" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Blog</a></li>
                                <li><a href="/#faq" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">FAQ</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-semibold text-slate-900 dark:text-white text-sm mb-4">Legal</h4>
                            <ul className="space-y-2 text-sm text-slate-500 dark:text-slate-400">
                                <li><a href="/privacy" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Kebijakan Privasi</a></li>
                                <li><a href="/terms" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Syarat & Ketentuan</a></li>
                            </ul>
                        </div>
                    </div>
                    <div className="mt-10 pt-8 border-t border-slate-200/50 dark:border-white/5 flex flex-col md:flex-row items-center justify-between gap-4">
                        <p className="text-xs text-slate-400 dark:text-slate-500">&copy; {new Date().getFullYear()} Ryaze. All rights reserved.</p>
                        <div className="flex items-center gap-4">
                            <a href="https://github.com" target="_blank" className="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"><i className="fa-brands fa-github"></i></a>
                            <a href="https://instagram.com" target="_blank" className="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"><i className="fa-brands fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" className="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"><i className="fa-brands fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    );
}
