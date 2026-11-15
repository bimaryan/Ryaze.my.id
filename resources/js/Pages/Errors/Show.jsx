import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';
import { useReveal, RevealStyle } from '../../Components/PublicSection';

function Action({ action, variant }) {
    const base = 'inline-flex items-center justify-center gap-2.5 px-7 py-3.5 text-sm font-semibold transition-colors';
    const styles = variant === 'primary'
        ? 'bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] hover:bg-[#1D5B96] dark:hover:bg-[#6BA8DC]'
        : 'border border-[#C9D5E2] dark:border-[#24384F] text-[#0F1A2A] dark:text-white hover:border-[#1D5B96] hover:text-[#1D5B96] dark:hover:border-[#6BA8DC] dark:hover:text-[#6BA8DC]';
    const icon = <i className={`fa-solid ${action.icon || 'fa-arrow-right'} text-[11px]`} aria-hidden="true"></i>;

    if (action.action === 'reload') {
        return (
            <button type="button" onClick={() => window.location.reload()} className={`${base} ${styles}`}>
                {icon}
                {action.label}
            </button>
        );
    }

    if (action.action === 'back') {
        return (
            <button type="button" onClick={() => window.history.back()} className={`${base} ${styles}`}>
                <i className="fa-solid fa-arrow-left text-[11px]" aria-hidden="true"></i>
                {action.label}
            </button>
        );
    }

    return (
        <Link href={action.href || '/'} className={`${base} ${styles}`}>
            {icon}
            {action.label}
        </Link>
    );
}

export default function Show({
    code,
    title,
    message,
    description,
    next,
    primary,
    secondary,
    causes = [],
    requestPath,
    requestMethod,
    requestTime,
    seoTitle,
    seoDescription,
    canonical,
}) {
    useReveal();

    const codeClass = code.length > 3
        ? 'text-[clamp(2.6rem,11vw,6.25rem)] leading-[0.92] tracking-[-0.05em]'
        : 'text-[clamp(6.5rem,24vw,18rem)] leading-[0.76] tracking-[-0.075em]';

    return (
        <PublicLayout bodyClass="bg-[#F4F8FC] font-sans antialiased text-[#0F1A2A] dark:bg-[#0A1322] dark:text-white">
            <Head title={seoTitle || `${code} — ${title} · Ryaze`}>
                <meta name="description" content={seoDescription || description} />
                <link rel="canonical" href={canonical} />
                <meta property="og:type" content="website" />
                <meta property="og:site_name" content="Ryaze" />
                <meta property="og:locale" content="id_ID" />
                <meta property="og:title" content={seoTitle || `${code} — ${title} · Ryaze`} />
                <meta property="og:description" content={seoDescription || description} />
                <meta name="twitter:card" content="summary_large_image" />
                <meta name="twitter:title" content={seoTitle || `${code} — ${title} · Ryaze`} />
                <meta name="twitter:description" content={seoDescription || description} />
            </Head>

            <RevealStyle />

            <main className="flex min-h-[calc(100vh-4rem)] items-center">
                <div className="mx-auto w-full max-w-6xl px-6 pt-28 pb-20 lg:pt-32 lg:pb-24">
                    <div className="grid items-end gap-12 lg:grid-cols-12 lg:gap-20">
                        <div className="lg:col-span-7" data-reveal>
                            <div
                                className={`select-none whitespace-nowrap font-black text-[#1D5B96] dark:text-[#6BA8DC] ${codeClass}`}
                                aria-hidden="true"
                            >
                                {code}
                            </div>
                            <h1 className="mt-6 max-w-[12em] text-[clamp(2rem,4.5vw,3rem)] font-black leading-[1.05] tracking-[-0.04em] text-[#0F1A2A] dark:text-white">
                                {message}
                            </h1>
                        </div>

                        <div className="pb-1 lg:col-span-5" data-reveal>
                            <p className="max-w-[44ch] text-base leading-[1.72] text-[#525252] dark:text-white/60">
                                {description}
                            </p>

                            {next && (
                                <p className="mt-5 flex max-w-[40ch] items-start gap-2.5 text-[13.5px] leading-relaxed text-[#0F1A2A] dark:text-white/85">
                                    <i className="fa-solid fa-arrow-right mt-1 text-[10px] text-[#1D5B96] dark:text-[#6BA8DC]" aria-hidden="true"></i>
                                    <span>{next}</span>
                                </p>
                            )}

                            <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                                {primary && <Action action={primary} variant="primary" />}
                                {secondary && <Action action={secondary} variant="secondary" />}
                            </div>

                            <p className="mt-9 flex flex-wrap items-center gap-x-4 gap-y-1 border-t border-[#EDF2F7] pt-4 font-mono text-[10px] font-medium uppercase tracking-[0.12em] text-[#737373] dark:border-[#152238] dark:text-white/45">
                                <span className="font-semibold text-[#1D5B96] dark:text-[#6BA8DC]">{requestMethod}</span>
                                <span className="normal-case tracking-[0.02em] break-all">{requestPath}</span>
                                <span>{requestTime}</span>
                            </p>
                        </div>
                    </div>

                    {causes.length > 0 && (
                        <section className="mt-16 border-t border-[#E3EAF1] pt-7 lg:mt-20 dark:border-[#1B2C44]" data-reveal>
                            <p className="mb-6 font-mono text-[10px] font-semibold uppercase tracking-[0.2em] text-[#94A3B4] dark:text-white/32">
                                Kemungkinan penyebab
                            </p>
                            <div className="grid gap-6 sm:grid-cols-3 sm:gap-10">
                                {causes.map((cause) => (
                                    <div key={cause.title} className="border-t border-[#C9D5E2] pt-3.5 dark:border-[#24384F]">
                                        <p className="mb-1 flex items-center gap-2 text-[13.5px] font-bold text-[#0F1A2A] dark:text-white">
                                            <i className={`fa-solid ${cause.icon} text-[11px] text-[#1D5B96] dark:text-[#6BA8DC]`} aria-hidden="true"></i>
                                            {cause.title}
                                        </p>
                                        <p className="text-[12.5px] leading-relaxed text-[#737373] dark:text-white/45">
                                            {cause.desc}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </section>
                    )}
                </div>
            </main>
        </PublicLayout>
    );
}
