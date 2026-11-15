import { useEffect, useRef } from 'react';

const LIGHT_RGB = '29, 91, 150';
const DARK_RGB = '107, 168, 220';

export default function PublicParticles() {
    const canvasRef = useRef(null);

    useEffect(() => {
        const canvas = canvasRef.current;
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        let particles = [];
        let frame = null;
        let width = 0;
        let height = 0;

        const isDark = () => document.documentElement.classList.contains('dark');

        const build = () => {
            width = window.innerWidth;
            height = window.innerHeight;
            const dpr = Math.min(window.devicePixelRatio || 1, 2);

            canvas.width = Math.round(width * dpr);
            canvas.height = Math.round(height * dpr);
            canvas.style.width = `${width}px`;
            canvas.style.height = `${height}px`;
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

            const count = Math.min(110, Math.max(24, Math.round((width * height) / 15000)));
            particles = Array.from({ length: count }, () => ({
                x: Math.random() * width,
                y: Math.random() * height,
                r: 0.9 + Math.random() * 1.5,
                vx: (Math.random() - 0.5) * 0.16,
                vy: (Math.random() - 0.5) * 0.16,
                alpha: 0.24 + Math.random() * 0.34,
                phase: Math.random() * Math.PI * 2,
                speed: 0.003 + Math.random() * 0.005,
            }));
        };

        const draw = (time) => {
            const rgb = isDark() ? DARK_RGB : LIGHT_RGB;
            ctx.clearRect(0, 0, width, height);

            for (const p of particles) {
                const twinkle = 0.7 + 0.3 * Math.sin(p.phase + time * p.speed);
                const alpha = p.alpha * twinkle;

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r * 2.8, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(${rgb}, ${(alpha * 0.14).toFixed(3)})`;
                ctx.fill();

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(${rgb}, ${alpha.toFixed(3)})`;
                ctx.fill();
            }
        };

        const step = (time) => {
            for (const p of particles) {
                p.x += p.vx;
                p.y += p.vy;
                if (p.x < -6) p.x = width + 6;
                else if (p.x > width + 6) p.x = -6;
                if (p.y < -6) p.y = height + 6;
                else if (p.y > height + 6) p.y = -6;
            }
            draw(time);
            frame = requestAnimationFrame(step);
        };

        const stop = () => {
            if (frame !== null) {
                cancelAnimationFrame(frame);
                frame = null;
            }
        };

        const start = () => {
            stop();
            if (reduceMotion.matches) {
                draw(0);
                return;
            }
            frame = requestAnimationFrame(step);
        };

        const onVisibility = () => {
            if (document.hidden) stop();
            else start();
        };

        let resizeFrame = null;
        const onResize = () => {
            if (resizeFrame !== null) cancelAnimationFrame(resizeFrame);
            resizeFrame = requestAnimationFrame(() => {
                resizeFrame = null;
                build();
                start();
            });
        };

        const onMotionChange = () => start();

        let themeObserver = null;
        if (typeof MutationObserver !== 'undefined') {
            themeObserver = new MutationObserver(() => {
                if (reduceMotion.matches) draw(0);
            });
            themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        }

        build();
        start();
        window.addEventListener('resize', onResize);
        document.addEventListener('visibilitychange', onVisibility);
        reduceMotion.addEventListener?.('change', onMotionChange);

        return () => {
            stop();
            if (resizeFrame !== null) cancelAnimationFrame(resizeFrame);
            themeObserver?.disconnect();
            window.removeEventListener('resize', onResize);
            document.removeEventListener('visibilitychange', onVisibility);
            reduceMotion.removeEventListener?.('change', onMotionChange);
        };
    }, []);

    return (
        <canvas
            ref={canvasRef}
            className="pointer-events-none fixed inset-0 -z-10 h-full w-full"
            aria-hidden="true"
        />
    );
}
