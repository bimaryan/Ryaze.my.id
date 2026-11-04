import { useEffect } from 'react';

export function useReveal() {
    useEffect(() => {
        const els = document.querySelectorAll('[data-reveal]');
        if (!('IntersectionObserver' in window)) {
            els.forEach(el => (el.style.opacity = '1'));
            return;
        }
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('revealed');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });
        els.forEach(el => io.observe(el));
        return () => io.disconnect();
    }, []);
}

export function RevealStyle() {
    return (
        <style>{`
            [data-reveal] { opacity: 0; transform: translateY(16px); transition: opacity 0.55s cubic-bezier(0.22,1,0.36,1), transform 0.55s cubic-bezier(0.22,1,0.36,1); }
            [data-reveal].revealed { opacity: 1; transform: none; }
            .grid-texture { background-image: linear-gradient(rgba(255,255,255,0.045) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.045) 1px, transparent 1px); background-size: 32px 32px; }
        `}</style>
    );
}

export function SectionHead({ num, label, title, sub, invert = false, as: Heading = 'h2' }) {
    return (
        <div data-reveal>
            <div className={`flex items-center gap-3 font-mono text-[11px] uppercase tracking-[0.2em] mb-5 ${invert ? 'text-white/45' : 'text-[#525252] dark:text-white/40'}`}>
                <span className={invert ? 'text-white' : 'text-[#1D5B96] dark:text-[#6BA8DC]'}>{num}</span>
                <span className={`w-6 h-px ${invert ? 'bg-white/25' : 'bg-[#C9D5E2] dark:bg-[#24384F]'}`}></span>
                <span>{label}</span>
            </div>
            <Heading className={`text-[clamp(1.9rem,4vw,3rem)] font-black tracking-[-0.03em] leading-[1.05] ${invert ? 'text-white' : 'text-[#0F1A2A] dark:text-white'}`}>
                {title}
            </Heading>
            {sub && (
                <p className={`mt-3 text-[15px] leading-relaxed ${invert ? 'text-white/55' : 'text-[#525252] dark:text-white/55'}`}>{sub}</p>
            )}
        </div>
    );
}
