import { Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';

const navLinks = [
    { label: 'Tentang', href: '/#about' },
    { label: 'Layanan', href: '/#services' },
    { label: 'Harga', href: '/#pricing' },
    { label: 'Blog', href: '/blog' },
];

export default function PublicLayout({ children, title, description }) {
    const [scrolled, setScrolled] = useState(false);
    const [dark, setDark] = useState(() => {
        if (typeof window !== 'undefined') {
            const s = localStorage.getItem('ryaze-theme');
            if (s) return s === 'dark';
            return window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        return false;
    });

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 1);
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    useEffect(() => {
        document.documentElement.classList.toggle('dark', dark);
        document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
        localStorage.setItem('ryaze-theme', dark ? 'dark' : 'light');
    }, [dark]);

    return (
        <div className="min-h-screen flex flex-col">
            <nav className={`fixed top-0 inset-x-0 z-50 transition-all duration-200 ${scrolled ? 'bg-white/90 backdrop-blur-md border-b border-[#e5e5e5]' : ''}`}>
                <div className="max-w-6xl mx-auto px-6 h-14 flex items-center justify-between">
                    <Link href="/" className="flex items-center gap-2.5">
                        <div className="w-7 h-7 bg-[#7c3aed] flex items-center justify-center">
                            <span className="text-white font-black text-xs">R</span>
                        </div>
                        <span className="font-black text-[#1a1025] text-sm tracking-tight">RYAZE</span>
                    </Link>
                    <div className="hidden md:flex items-center gap-7">
                        {navLinks.map(l => (
                            <a key={l.href} href={l.href} className="text-[13px] font-medium text-[#666] hover:text-[#1a1025] transition-colors">{l.label}</a>
                        ))}
                    </div>
                    <div className="flex items-center gap-3">
                        <button onClick={() => setDark(!dark)} className="w-8 h-8 flex items-center justify-center text-[#999] hover:text-[#1a1025] transition-colors">
                            <i className={`fa-solid ${dark ? 'fa-sun' : 'fa-moon'} text-sm`}></i>
                        </button>
                        <a href="/login" className="text-[13px] font-medium text-[#666] hover:text-[#1a1025] transition-colors hidden sm:block">Masuk</a>
                        <a href="/register" className="px-4 py-1.5 bg-[#1a1025] text-white text-[13px] font-semibold hover:bg-[#2d1f42] transition-colors">Daftar</a>
                    </div>
                </div>
            </nav>

            {children}

            <footer className="bg-[#1a1025] border-t border-[#2d1f42]">
                <div className="max-w-6xl mx-auto px-6 py-12">
                    <div className="grid md:grid-cols-4 gap-10">
                        <div>
                            <div className="flex items-center gap-2 mb-4">
                                <div className="w-7 h-7 bg-[#7c3aed] flex items-center justify-center"><span className="text-white font-black text-xs">R</span></div>
                                <span className="font-black text-white text-sm tracking-tight">RYAZE</span>
                            </div>
                            <p className="text-[13px] text-[#666] leading-relaxed">Platform hosting & development untuk bisnis Anda.</p>
                        </div>
                        <div>
                            <h4 className="text-white font-bold text-xs uppercase tracking-widest mb-4">Layanan</h4>
                            <ul className="space-y-2.5 text-[13px] text-[#999]">
                                <li><a href="/#services" className="hover:text-white transition-colors">Web Development</a></li>
                                <li><a href="/#pricing" className="hover:text-white transition-colors">Hosting</a></li>
                                <li><a href="/consultation" className="hover:text-white transition-colors">Konsultasi</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="text-white font-bold text-xs uppercase tracking-widest mb-4">Perusahaan</h4>
                            <ul className="space-y-2.5 text-[13px] text-[#999]">
                                <li><a href="/#about" className="hover:text-white transition-colors">Tentang</a></li>
                                <li><a href="/blog" className="hover:text-white transition-colors">Blog</a></li>
                                <li><a href="/#faq" className="hover:text-white transition-colors">FAQ</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="text-white font-bold text-xs uppercase tracking-widest mb-4">Legal</h4>
                            <ul className="space-y-2.5 text-[13px] text-[#999]">
                                <li><a href="/privacy" className="hover:text-white transition-colors">Privasi</a></li>
                                <li><a href="/terms" className="hover:text-white transition-colors">Syarat</a></li>
                            </ul>
                        </div>
                    </div>
                    <div className="mt-10 pt-8 border-t border-[#2d1f42] flex flex-col md:flex-row items-center justify-between gap-4">
                        <p className="text-xs text-[#666]">&copy; {new Date().getFullYear()} Ryaze.</p>
                        <div className="flex items-center gap-5 text-[#666]">
                            {['fa-brands fa-github', 'fa-brands fa-instagram', 'fa-brands fa-linkedin-in'].map(icon => (
                                <a key={icon} href="#" className="hover:text-white transition-colors"><i className={`${icon} text-sm`}></i></a>
                            ))}
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    );
}
