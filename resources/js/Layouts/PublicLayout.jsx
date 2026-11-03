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
    const { auth, navLinks, footerLinks } = usePage().props;
    const { url } = usePage();
    const user = auth?.user;
    const currentPath = (url || '/').split('?')[0].replace(/\/+$/, '') || '/';
    const isActive = (href) => {
        const path = (href || '/').split('?')[0].replace(/\/+$/, '') || '/';
        return path === '/' ? currentPath === '/' : currentPath === path || currentPath.startsWith(path + '/');
    };
    const [scrolled, setScrolled] = useState(false);
    const [dark, setDark] = useState(getInitialDark);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    useEffect(() => {
        applyDark(dark);
    }, [dark]);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 1);
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    const toggleDark = () => {
        const next = !dark;
        applyDark(next);
        localStorage.setItem('ryaze-theme', next ? 'dark' : 'light');
        setDark(next);
    };

    const closeMobileMenu = () => setMobileMenuOpen(false);

    return (
        <div className={`${bodyClass || ''} min-h-screen flex flex-col`}>
            {withNav && (
            <nav className={`fixed top-0 inset-x-0 z-50 bg-white/85 dark:bg-[#0A1322]/85 backdrop-blur-xl border-b border-[#E3EAF1] dark:border-[#1B2C44] transition-shadow duration-300 ${scrolled ? 'shadow-[0_1px_0_0_rgba(29,91,150,0.08),0_8px_24px_-12px_rgba(15,26,42,0.12)] dark:shadow-[0_1px_0_0_rgba(107,168,220,0.06),0_8px_24px_-12px_rgba(0,0,0,0.5)]' : ''}`}>
                <div className="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                    {/* Logo */}
                    <Link href="/" className="flex items-center gap-3 group shrink-0">
                        <img
                            src="/icon/logo_ryaze.png"
                            alt="Ryaze"
                            width={32}
                            height={32}
                            className="h-8 w-8 object-contain ring-1 ring-[#E3EAF1] dark:ring-[#24384F] group-hover:ring-[#1D5B96] dark:group-hover:ring-[#6BA8DC] transition-all duration-300"
                        />
                        <span className="font-black text-[#0F1A2A] dark:text-white text-[15px] tracking-[-0.02em] leading-none">RYAZE</span>
                    </Link>

                    {/* Desktop links — mono tab style */}
                    <div className="hidden md:flex items-stretch h-16 -mx-2">
                        {navLinks.map(l => (
                            <a
                                key={l.href}
                                href={l.href}
                                className={`relative flex items-center px-4 font-mono text-[11px] uppercase tracking-[0.15em] transition-colors duration-200 ${
                                    isActive(l.href)
                                        ? 'text-[#1D5B96] dark:text-[#6BA8DC]'
                                        : 'text-[#525252] dark:text-white/45 hover:text-[#0F1A2A] dark:hover:text-white'
                                }`}
                            >
                                {l.label}
                                <span className={`absolute inset-x-4 bottom-0 h-[2px] bg-[#1D5B96] dark:bg-[#6BA8DC] transition-transform duration-300 origin-left ${isActive(l.href) ? 'scale-x-100' : 'scale-x-0'}`}></span>
                            </a>
                        ))}
                    </div>

                    {/* Actions */}
                    <div className="flex items-center gap-1.5 sm:gap-2 shrink-0">
                        <button
                            onClick={toggleDark}
                            className="w-9 h-9 flex items-center justify-center text-[#737373] dark:text-white/40 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] hover:bg-[#EDF4FB] dark:hover:bg-[#6BA8DC]/10 transition-colors"
                            aria-label="Ganti tema"
                        >
                            <i className={`fa-solid ${dark ? 'fa-sun' : 'fa-moon'} text-[13px] transition-all duration-300`}></i>
                        </button>
                        {user ? (
                            <a href={dashboardUrl[user.role] || dashboardUrl.default} className="ml-1 inline-flex items-center gap-2 px-4 py-2 bg-[#1D5B96] hover:bg-[#164A7C] text-white text-[12px] font-semibold uppercase tracking-[0.08em] transition-colors">
                                Dashboard
                                <i className="fa-solid fa-arrow-right text-[9px] opacity-70"></i>
                            </a>
                        ) : (
                            <>
                                <a href="/login" className="hidden sm:inline-flex items-center px-4 py-2 font-mono text-[11px] uppercase tracking-[0.15em] text-[#525252] dark:text-white/50 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">Masuk</a>
                                <a href="/register" className="inline-flex items-center px-4 py-2 bg-[#1D5B96] hover:bg-[#164A7C] text-white text-[12px] font-semibold uppercase tracking-[0.08em] transition-colors">
                                    Daftar
                                </a>
                            </>
                        )}
                        <button
                            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                            className="md:hidden w-9 h-9 flex items-center justify-center text-[#525252] dark:text-white/50 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] hover:bg-[#EDF4FB] dark:hover:bg-[#6BA8DC]/10 transition-colors"
                            aria-label={mobileMenuOpen ? 'Tutup menu' : 'Buka menu'}
                        >
                            <i className={`fa-solid ${mobileMenuOpen ? 'fa-xmark' : 'fa-bars'} text-base`}></i>
                        </button>
                    </div>
                </div>

                {/* Mobile menu */}
                {mobileMenuOpen && (
                    <div className="md:hidden border-t border-[#E3EAF1] dark:border-[#1B2C44] bg-white/95 dark:bg-[#0A1322]/95 backdrop-blur-md px-6 py-2 animate-slide-down">
                        {navLinks.map(l => (
                            <a
                                key={l.href}
                                href={l.href}
                                onClick={closeMobileMenu}
                                className={`flex items-center justify-between py-3.5 border-b border-[#EDF2F7] dark:border-[#152238] font-mono text-[11px] uppercase tracking-[0.15em] transition-colors ${
                                    isActive(l.href)
                                        ? 'text-[#1D5B96] dark:text-[#6BA8DC]'
                                        : 'text-[#525252] dark:text-white/45'
                                }`}
                            >
                                {l.label}
                                <span className={`w-1.5 h-1.5 bg-[#1D5B96] dark:bg-[#6BA8DC] ${isActive(l.href) ? 'opacity-100' : 'opacity-0'}`}></span>
                            </a>
                        ))}
                        <div className="pt-4 pb-2 flex flex-col gap-2">
                            {!user && (
                                <a href="/login" onClick={closeMobileMenu} className="block text-center py-2.5 font-mono text-[11px] uppercase tracking-[0.15em] text-[#525252] dark:text-white/50 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">
                                    Masuk
                                </a>
                            )}
                            <a
                                href={user ? (dashboardUrl[user.role] || dashboardUrl.default) : '/register'}
                                onClick={closeMobileMenu}
                                className="block text-center py-2.5 bg-[#1D5B96] hover:bg-[#164A7C] text-white text-[12px] font-semibold uppercase tracking-[0.08em] transition-colors"
                            >
                                {user ? 'Dashboard' : 'Daftar'}
                            </a>
                        </div>
                    </div>
                )}
            </nav>
            )}

            {/* flex-1 + bg dasar: halaman pendek tetap mengisi layar dan
                celah di atas footer tidak terlihat seperti "lubang" */}
            <div className="flex-1 bg-slate-50 dark:bg-[#0A1322]">
                {children}
            </div>

            {withFooter && (
            <footer className="mt-auto border-t border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322]">
                <div className="max-w-6xl mx-auto px-6 py-6">
                    <div className="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div className="flex items-center gap-2.5">
                            <img src="/icon/logo_ryaze.png" alt="Ryaze" width={24} height={24} className="h-6 w-6 object-contain ring-1 ring-[#E3EAF1] dark:ring-[#24384F]" />
                            <span className="font-black text-[#0F1A2A] dark:text-white text-sm tracking-tight">RYAZE</span>
                            <span className="font-mono text-[10px] text-[#94A3B4] dark:text-white/30 hidden sm:inline tracking-[0.1em]">&copy; {new Date().getFullYear()}</span>
                        </div>
                        <div className="flex items-center gap-6">
                            {footerLinks.map(l => (
                                <a key={l.href} href={l.href} className="font-mono text-[10px] uppercase tracking-[0.15em] text-[#737373] dark:text-white/40 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] transition-colors">{l.label}</a>
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
