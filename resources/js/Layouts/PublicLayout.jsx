import { Link, usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';

const dashboardUrl = {
    superadmin: '/superadmin/dashboard',
    admin_joki: '/admin/joki/dashboard',
    admin_hosting: '/admin/hosting/dashboard',
    user_joki: '/user/joki/dashboard',
    user_hosting: '/user/hosting/dashboard',
    default: '/user/hosting/dashboard',
};

const navLinks = [
    { label: 'Tentang', href: '/#about' },
    { label: 'Layanan', href: '/#services' },
    { label: 'Harga', href: '/#pricing' },
    { label: 'Blog', href: '/blog' },
];

function getInitialDark() {
    if (typeof window === 'undefined') return false;
    const s = localStorage.getItem('ryaze-theme');
    if (s) return s === 'dark';
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

function applyDark(dark) {
    document.documentElement.classList.toggle('dark', dark);
    document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
}

export default function PublicLayout({ children, withNav = true, withFooter = true, bodyClass }) {
    const { auth } = usePage().props;
    const user = auth?.user;
    const [scrolled, setScrolled] = useState(false);
    const [dark, setDark] = useState(getInitialDark);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    useEffect(() => {
        applyDark(dark);
        localStorage.setItem('ryaze-theme', dark ? 'dark' : 'light');
    }, [dark]);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 1);
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    const closeMobileMenu = () => setMobileMenuOpen(false);

    return (
        <div className={`${bodyClass || ''} min-h-screen flex flex-col`}>
            {withNav && (
            <nav className={`fixed top-0 inset-x-0 z-50 transition-all duration-200 ${scrolled ? 'bg-white/90 backdrop-blur-md border-b border-[#e5e5e5] dark:bg-[#1a1025]/90 dark:border-[#2d1f42]' : ''}`}>
                <div className="max-w-6xl mx-auto px-6 h-14 flex items-center justify-between">
                    <Link href="/" className="flex items-center gap-2.5">
                        <div className="w-7 h-7 bg-[#7c3aed] flex items-center justify-center">
                            <span className="text-white font-black text-xs">R</span>
                        </div>
                        <span className="font-black text-[#7c3aed] dark:text-white text-sm tracking-tight">RYAZE</span>
                    </Link>
                    <div className="hidden md:flex items-center gap-7">
                        {navLinks.map(l => (
                            <a key={l.href} href={l.href} className="text-[13px] font-medium text-[#666] dark:text-[#999] hover:text-[#7c3aed] dark:hover:text-white transition-colors">{l.label}</a>
                        ))}
                    </div>
                    <div className="flex items-center gap-3">
                        <button onClick={() => setDark(d => !d)} className="w-8 h-8 flex items-center justify-center text-[#999] dark:text-[#666] hover:text-[#7c3aed] dark:hover:text-white transition-colors">
                            <i className={`fa-solid ${dark ? 'fa-sun' : 'fa-moon'} text-sm`}></i>
                        </button>
                        {user ? (
                            <a href={dashboardUrl[user.role] || dashboardUrl.default} className="px-4 py-1.5 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                                Dashboard
                            </a>
                        ) : (
                            <>
                                <a href="/login" className="text-[13px] font-medium text-[#666] dark:text-[#999] hover:text-[#7c3aed] dark:hover:text-white transition-colors hidden sm:block">Masuk</a>
                                <a href="/register" className="px-4 py-1.5 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">Daftar</a>
                            </>
                        )}
                        <button
                            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                            className="md:hidden w-9 h-9 flex items-center justify-center text-[#666] dark:text-[#999] hover:text-[#7c3aed] dark:hover:text-white transition-colors"
                            aria-label={mobileMenuOpen ? 'Tutup menu' : 'Buka menu'}
                        >
                            <i className={`fa-solid ${mobileMenuOpen ? 'fa-xmark' : 'fa-bars'} text-lg`}></i>
                        </button>
                    </div>
                </div>

                {mobileMenuOpen && (
                    <div className="md:hidden border-t border-[#e5e5e5] dark:border-[#2d1f42] bg-white/95 dark:bg-[#1a1025]/95 backdrop-blur-md px-6 py-4 space-y-3 animate-slide-down">
                        {navLinks.map(l => (
                            <a
                                key={l.href}
                                href={l.href}
                                onClick={closeMobileMenu}
                                className="block text-[15px] font-medium text-[#666] dark:text-[#999] hover:text-[#7c3aed] dark:hover:text-white transition-colors py-2"
                            >
                                {l.label}
                            </a>
                        ))}
                        <div className="pt-2 border-t border-[#e5e5e5] dark:border-[#2d1f42] flex flex-col gap-2">
                            {!user && (
                                <a href="/login" onClick={closeMobileMenu} className="text-[15px] font-medium text-[#666] dark:text-[#999] hover:text-[#7c3aed] dark:hover:text-white transition-colors py-2">Masuk</a>
                            )}
                            <a
                                href={user ? (dashboardUrl[user.role] || dashboardUrl.default) : '/register'}
                                onClick={closeMobileMenu}
                                className="px-4 py-2.5 bg-[#7c3aed] text-white text-[15px] font-semibold hover:bg-[#6d28d9] transition-colors text-center"
                            >
                                {user ? 'Dashboard' : 'Daftar'}
                            </a>
                        </div>
                    </div>
                )}
            </nav>
            )}

            {children}

            {withFooter && (
            <footer className="bg-[#7c3aed] border-t border-[#6d28d9]">
                <div className="max-w-6xl mx-auto px-6 py-12">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10">
                        <div>
                            <div className="flex items-center gap-2 mb-4">
                                <div className="w-7 h-7 bg-[#7c3aed] flex items-center justify-center"><span className="text-white font-black text-xs">R</span></div>
                                <span className="font-black text-white text-sm tracking-tight">RYAZE</span>
                            </div>
                            <p className="text-[13px] text-[#999] leading-relaxed">Platform hosting & development untuk bisnis Anda.</p>
                        </div>
                        <div>
                            <h4 className="text-white font-bold text-xs uppercase tracking-widest mb-4">Layanan</h4>
                            <ul className="space-y-2.5 text-[13px] text-[#999]">
                                <li><a href="/#services" className="dark:text-white transition-colors">Web Development</a></li>
                                <li><a href="/#pricing" className="dark:text-white transition-colors">Hosting</a></li>
                                <li><a href="/consultation" className="dark:text-white transition-colors">Konsultasi</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="text-white font-bold text-xs uppercase tracking-widest mb-4">Perusahaan</h4>
                            <ul className="space-y-2.5 text-[13px] text-[#999]">
                                <li><a href="/#about" className="dark:text-white transition-colors">Tentang</a></li>
                                <li><a href="/blog" className="dark:text-white transition-colors">Blog</a></li>
                                <li><a href="/#faq" className="dark:text-white transition-colors">FAQ</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="text-white font-bold text-xs uppercase tracking-widest mb-4">Legal</h4>
                            <ul className="space-y-2.5 text-[13px] text-[#999]">
                                <li><a href="/privacy" className="dark:text-white transition-colors">Privasi</a></li>
                                <li><a href="/terms" className="dark:text-white transition-colors">Syarat</a></li>
                            </ul>
                        </div>
                    </div>
                    <div className="mt-10 pt-8 border-t border-[#6d28d9] flex flex-col md:flex-row items-center justify-between gap-4">
                        <p className="text-xs text-[#999]">&copy; {new Date().getFullYear()} Ryaze.</p>
                        <div className="flex items-center gap-5 text-[#999]">
                            {['fa-brands fa-github', 'fa-brands fa-instagram', 'fa-brands fa-linkedin-in'].map(icon => (
                                <a key={icon} href="#" className="dark:text-white transition-colors"><i className={`${icon} text-sm`}></i></a>
                            ))}
                        </div>
                    </div>
                </div>
            </footer>
            )}

            <style>{`
                @keyframes slide-down {
                    from { opacity: 0; transform: translateY(-10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .animate-slide-down { animation: slide-down 0.2s ease-out; }
            `}</style>
        </div>
    );
}
