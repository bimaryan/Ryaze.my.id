import PublicLayout from '../../Layouts/PublicLayout';
import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { useReveal } from '../../Components/PublicSection';

function formatDate(iso) {
    if (!iso) return 'Tidak tersedia';
    const d = new Date(iso);
    if (isNaN(d)) return iso;
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

const specRows = [
    { k: 'SUMBER', v: 'WHOIS / RDAP' },
    { k: 'DNS', v: 'A · AAAA · MX · TXT · NS' },
    { k: 'BIAYA', v: 'Gratis' },
    { k: 'RATE LIMIT', v: 'Standar' },
];

export default function Index({ seoTitle, seoDescription, canonical }) {
    useReveal();
    const { csrf } = usePage().props;
    const [domain, setDomain] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');
    const [loadingDomain, setLoadingDomain] = useState('');
    const [result, setResult] = useState(null);
    const [copied, setCopied] = useState(false);

    const hasDnsRecords = result?.dns && Object.values(result.dns).some(r => r && r.length > 0);

    const search = async (e) => {
        e.preventDefault();
        const value = domain.trim();
        if (!value || loading) return;

        setLoading(true);
        setError(false);
        setErrorMessage('');
        setResult(null);
        setLoadingDomain(value);

        try {
            const res = await fetch('/whois/lookup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({ domain: value }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || `Server merespons HTTP ${res.status}`);
            setResult(data);
        } catch (err) {
            setError(true);
            setErrorMessage(err.message || 'Terjadi kesalahan tidak terduga.');
        } finally {
            setLoading(false);
        }
    };

    const copyRaw = async () => {
        if (!result) return;
        const w = result.whois || {};
        const lines = [
            `Domain: ${result.domain}`,
            `Registrar: ${w.registrar || '-'}`,
            `Registrasi: ${formatDate(w.registered_at)}`,
            `Kedaluwarsa: ${formatDate(w.expires_at)}`,
            `Update: ${formatDate(w.updated_at)}`,
            `DNSSEC: ${w.dnssec?.enabled ? 'Aktif' : 'Tidak aktif'}`,
            w.nameservers?.length ? `Nameserver:\n${w.nameservers.map(n => `  - ${n}`).join('\n')}` : null,
        ].filter(Boolean);

        try {
            await navigator.clipboard.writeText(lines.join('\n'));
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch {
            setCopied(false);
        }
    };

    const siteName = 'Ryaze';
    const pageUrl = canonical || 'https://ryaze.my.id/whois';
    const ogImage = 'https://ryaze.my.id/og-image.png';
    const description = seoDescription || 'Cek informasi registrasi domain WHOIS/RDAP gratis: registrar, tanggal registrasi & kedaluwarsa, nameserver, status, dan record DNS.';
    const pageTitle = seoTitle || 'WHOIS Domain Lookup | Ryaze';

    const webPageJsonLd = {
        '@context': 'https://schema.org',
        '@type': 'WebPage',
        name: 'WHOIS Domain Lookup',
        description,
        url: pageUrl,
        inLanguage: 'id-ID',
        publisher: {
            '@type': 'Organization',
            name: siteName,
            logo: {
                '@type': 'ImageObject',
                url: ogImage,
                width: 1200,
                height: 630,
            },
        },
    };

    const breadcrumbJsonLd = {
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: [
            { '@type': 'ListItem', position: 1, name: 'Beranda', item: 'https://ryaze.my.id/' },
            { '@type': 'ListItem', position: 2, name: 'WHOIS Domain Lookup', item: pageUrl },
        ],
    };

    return (
        <>
            <Head title={pageTitle}>
                <meta name="description" content={description} />
                <meta property="og:type" content="website" />
                <meta property="og:url" content={pageUrl} />
                <meta property="og:site_name" content={siteName} />
                <meta property="og:locale" content="id_ID" />
                <meta property="og:title" content={pageTitle} />
                <meta property="og:description" content={description} />
                <meta property="og:image" content={ogImage} />
                <meta property="twitter:card" content="summary_large_image" />
                <meta property="twitter:url" content={pageUrl} />
                <meta property="twitter:title" content={pageTitle} />
                <meta property="twitter:description" content={description} />
                <meta property="twitter:image" content={ogImage} />
                <link rel="canonical" href={pageUrl} />
                <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(webPageJsonLd) }} />
                <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbJsonLd) }} />
            </Head>
            <PublicLayout>
                <style>{`
                    [data-reveal] { opacity: 0; transform: translateY(16px); transition: opacity 0.55s cubic-bezier(0.22,1,0.36,1), transform 0.55s cubic-bezier(0.22,1,0.36,1); }
                    [data-reveal].revealed { opacity: 1; transform: none; }
                    .grid-texture { background-image: linear-gradient(rgba(255,255,255,0.045) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.045) 1px, transparent 1px); background-size: 32px 32px; }
                `}</style>

                {/* HERO */}
                <section className="bg-white dark:bg-[#0A1322] border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                    <div className="max-w-6xl mx-auto px-6 pt-28 pb-14 lg:pt-32 lg:pb-16">
                        <div className="grid lg:grid-cols-12 gap-10 lg:gap-14 items-end">
                            <div className="lg:col-span-8" data-reveal>
                                <p className="font-mono text-[11px] uppercase tracking-[0.25em] text-[#1D5B96] dark:text-[#6BA8DC] mb-6">
                                    Domain Tools
                                </p>
                                <h1 className="text-[clamp(2.4rem,6vw,4.25rem)] font-black text-[#0F1A2A] dark:text-white leading-[0.98] tracking-[-0.04em] mb-6">
                                    Cek domain.<br />
                                    <span className="text-[#1D5B96] dark:text-[#6BA8DC]">Baca jejaknya.</span>
                                </h1>
                                <p className="text-[#525252] dark:text-white/60 text-lg max-w-lg leading-relaxed">
                                    Cek informasi registrasi &amp; DNS suatu domain — registrar, tanggal kedaluwarsa, nameserver, status, dan record DNS.
                                </p>
                            </div>

                            <div className="lg:col-span-4" data-reveal>
                                <div className="border border-[#E3EAF1] dark:border-[#1B2C44] bg-[#F0F6FC] dark:bg-[#0D1828]">
                                    <div className="flex items-center justify-between px-5 py-3 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                                        <span className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">lookup.spec</span>
                                        <span className="flex items-center gap-1.5 font-mono text-[10px] tracking-widest text-emerald-600 dark:text-emerald-400">
                                            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ONLINE
                                        </span>
                                    </div>
                                    <dl>
                                        {specRows.map((r, i) => (
                                            <div key={r.k} className={`flex items-baseline justify-between gap-4 px-5 py-3.5 ${i < specRows.length - 1 ? 'border-b border-[#E3EAF1] dark:border-[#1B2C44]' : ''}`}>
                                                <dt className="font-mono text-[10px] uppercase tracking-[0.18em] text-[#737373] dark:text-white/35 shrink-0">{r.k}</dt>
                                                <dd className="text-[13px] font-medium text-[#0F1A2A] dark:text-white text-right">{r.v}</dd>
                                            </div>
                                        ))}
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* TOOL */}
                <section className="bg-[#F4F8FC] dark:bg-[#0B1526]">
                    <div className="max-w-6xl mx-auto px-6 py-14 lg:py-20">
                        <div className="border border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322]" data-reveal>
                            <div className="flex items-center justify-between px-6 py-3.5 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                                <span className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">domain.lookup</span>
                                <span className="flex items-center gap-1.5 font-mono text-[10px] tracking-widest text-[#1D5B96] dark:text-[#6BA8DC]">
                                    <span className="w-1.5 h-1.5 rounded-full bg-[#1D5B96] dark:bg-[#6BA8DC]"></span> RDAP + DNS
                                </span>
                            </div>

                            <div className="p-6 sm:p-8">
                                <form onSubmit={search}>
                                    <label htmlFor="domain-input" className="block font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/35 mb-3">
                                        Nama Domain <span className="text-rose-500 dark:text-rose-400">*</span>
                                    </label>
                                    <div className="flex flex-col sm:flex-row gap-3">
                                        <div className="relative flex-1">
                                            <div className="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                                <i className="fa-solid fa-globe text-[#94A3B4] dark:text-white/30 text-sm"></i>
                                            </div>
                                            <input
                                                type="text"
                                                id="domain-input"
                                                value={domain}
                                                onChange={e => setDomain(e.target.value)}
                                                placeholder="contoh: ryaze.my.id"
                                                autoComplete="off"
                                                spellCheck="false"
                                                required
                                                className="w-full pl-11 pr-4 py-3.5 border border-[#C9D5E2] dark:border-[#24384F] bg-white dark:bg-[#0D1828] text-[#0F1A2A] dark:text-white placeholder-[#94A3B4] dark:placeholder-white/25 focus:outline-none focus:border-[#1D5B96] dark:focus:border-[#6BA8DC] transition-colors font-mono text-sm"
                                            />
                                        </div>
                                        <button
                                            type="submit"
                                            disabled={loading}
                                            className="group inline-flex justify-center items-center gap-2.5 bg-[#12233A] dark:bg-white text-white dark:text-[#0F1A2A] hover:bg-[#1D5B96] dark:hover:bg-[#6BA8DC] px-7 py-3.5 text-sm font-semibold transition-colors whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <i className="fa-solid fa-magnifying-glass text-[12px]"></i>
                                            <span>{loading ? 'Mencari...' : 'Cek Domain'}</span>
                                        </button>
                                    </div>
                                </form>

                                <div className="mt-8 pt-8 border-t border-[#EDF2F7] dark:border-[#152238]">
                                    {loading && (
                                        <div className="border border-[#E3EAF1] dark:border-[#1B2C44] bg-[#F0F6FC] dark:bg-[#0D1828]">
                                            <div className="flex items-center justify-between px-5 py-3 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                                                <span className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40">query</span>
                                                <span className="font-mono text-[10px] text-[#1D5B96] dark:text-[#6BA8DC]">{loadingDomain}</span>
                                            </div>
                                            <div className="p-6 space-y-2 font-mono text-[12px]">
                                                <p className="text-[#737373] dark:text-white/35">remote: resolving nameservers...</p>
                                                <p className="text-[#737373] dark:text-white/35">remote: fetching RDAP record...</p>
                                                <p className="flex items-center gap-2 text-[#0F1A2A] dark:text-white">
                                                    <span className="w-1.5 h-4 bg-[#1D5B96] dark:bg-[#6BA8DC] animate-pulse"></span>
                                                    Mengambil data domain
                                                </p>
                                            </div>
                                        </div>
                                    )}

                                    {!loading && error && (
                                        <div className="relative border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/[0.06] p-6 pl-7 overflow-hidden">
                                            <span className="absolute left-0 top-0 bottom-0 w-[3px] bg-rose-500" aria-hidden="true"></span>
                                            <p className="font-mono text-[10px] uppercase tracking-[0.2em] text-rose-600 dark:text-rose-400 mb-2">Gagal</p>
                                            <p className="font-bold text-rose-800 dark:text-rose-300 text-sm mb-1">Gagal mengambil data</p>
                                            <p className="text-sm text-rose-600 dark:text-rose-400/90 leading-relaxed">{errorMessage}</p>
                                        </div>
                                    )}

                                    {!loading && !error && result && !result.registered && (
                                        <div className="relative border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/[0.06] p-6 pl-7 overflow-hidden">
                                            <span className="absolute left-0 top-0 bottom-0 w-[3px] bg-emerald-500" aria-hidden="true"></span>
                                            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                <div>
                                                    <p className="font-mono text-[10px] uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400 mb-2">Tersedia</p>
                                                    <p className="font-black text-lg text-emerald-800 dark:text-emerald-300 mb-1 flex items-center gap-2">
                                                        <i className="fa-solid fa-circle-check"></i> Domain Tersedia
                                                    </p>
                                                    <p className="text-sm text-emerald-700 dark:text-emerald-400/90">{result?.message}</p>
                                                </div>
                                            </div>
                                            <p className="mt-4 pt-4 border-t border-emerald-200/70 dark:border-emerald-500/20 font-mono text-[11px] text-emerald-600/70 dark:text-emerald-500/70 leading-relaxed">
                                                Catatan: hasil RDAP bisa tertunda. Pastikan dengan registrar sebelum membeli.
                                            </p>
                                        </div>
                                    )}

                                    {!loading && !error && result && result.registered && (
                                        <div className="space-y-6">
                                            <div className="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-[#E3EAF1] dark:border-[#1B2C44]">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-11 h-11 border border-[#E3EAF1] dark:border-[#1B2C44] bg-[#F0F6FC] dark:bg-[#0D1828] flex items-center justify-center">
                                                        <i className="fa-solid fa-globe text-[#1D5B96] dark:text-[#6BA8DC] text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <p className="font-mono font-bold text-lg text-[#0F1A2A] dark:text-white">{result?.domain}</p>
                                                        <p className="font-mono text-[10px] uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400">Domain terdaftar</p>
                                                    </div>
                                                </div>
                                                <button
                                                    type="button"
                                                    onClick={copyRaw}
                                                    className="inline-flex items-center gap-2 font-mono text-[10px] uppercase tracking-[0.15em] text-[#525252] dark:text-white/50 hover:text-[#1D5B96] dark:hover:text-[#6BA8DC] bg-white dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] hover:border-[#1D5B96] dark:hover:border-[#6BA8DC] px-3.5 py-2.5 transition-colors"
                                                >
                                                    <i className={`fa-regular ${copied ? 'fa-circle-check' : 'fa-copy'}`}></i> {copied ? 'Tersalin' : 'Salin'}
                                                </button>
                                            </div>

                                            <dl className="grid grid-cols-1 sm:grid-cols-2 border border-[#E3EAF1] dark:border-[#1B2C44]">
                                                {[
                                                    { k: 'Registrar', v: result?.whois?.registrar || 'Tidak tersedia' },
                                                    {
                                                        k: 'DNSSEC',
                                                        v: (
                                                            <span className={`flex items-center gap-1.5 ${result?.whois?.dnssec?.enabled ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#737373] dark:text-white/35'}`}>
                                                                <i className={`fa-solid ${result?.whois?.dnssec?.enabled ? 'fa-shield-halved' : 'fa-shield'}`}></i>
                                                                {result?.whois?.dnssec?.enabled ? 'Aktif (signed)' : 'Tidak aktif'}
                                                            </span>
                                                        ),
                                                    },
                                                    { k: 'Tanggal Registrasi', v: formatDate(result?.whois?.registered_at) },
                                                    { k: 'Tanggal Kedaluwarsa', v: formatDate(result?.whois?.expires_at) },
                                                ].map((row, i) => (
                                                    <div key={row.k} className={`p-5 ${i < 2 ? 'border-b border-[#EDF2F7] dark:border-[#152238]' : ''} ${i % 2 === 0 ? 'sm:border-r border-[#E3EAF1] dark:border-[#1B2C44]' : ''}`}>
                                                        <dt className="font-mono text-[10px] uppercase tracking-[0.18em] text-[#737373] dark:text-white/35 mb-2">{row.k}</dt>
                                                        <dd className="text-sm font-semibold text-[#0F1A2A] dark:text-white">{row.v}</dd>
                                                    </div>
                                                ))}
                                                <div className="sm:col-span-2 p-5 border-t border-[#E3EAF1] dark:border-[#1B2C44]">
                                                    <dt className="font-mono text-[10px] uppercase tracking-[0.18em] text-[#737373] dark:text-white/35 mb-2">Update Terakhir</dt>
                                                    <dd className="text-sm font-semibold text-[#0F1A2A] dark:text-white">{formatDate(result?.whois?.updated_at)}</dd>
                                                </div>
                                            </dl>

                                            {result?.whois?.status?.length > 0 && (
                                                <div>
                                                    <h3 className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/35 mb-3 flex items-center gap-2">
                                                        <i className="fa-solid fa-flag text-[10px]"></i> Status Domain
                                                    </h3>
                                                    <div className="flex flex-wrap gap-2">
                                                        {result.whois.status.map(status => (
                                                            <span key={status} className="inline-flex items-center font-mono text-[11px] px-2.5 py-1 bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30">
                                                                {status}
                                                            </span>
                                                        ))}
                                                    </div>
                                                </div>
                                            )}

                                            {result?.whois?.nameservers?.length > 0 && (
                                                <div>
                                                    <h3 className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/35 mb-3 flex items-center gap-2">
                                                        <i className="fa-solid fa-server text-[10px]"></i> Nameserver
                                                    </h3>
                                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-px bg-[#E3EAF1] dark:bg-[#1B2C44] border border-[#E3EAF1] dark:border-[#1B2C44]">
                                                        {result.whois.nameservers.map(ns => (
                                                            <div key={ns} className="flex items-center gap-2.5 bg-white dark:bg-[#0A1322] px-4 py-3">
                                                                <i className="fa-solid fa-dns text-[#94A3B4] dark:text-white/25 text-[11px]"></i>
                                                                <span className="font-mono text-xs text-[#0F1A2A] dark:text-white/70">{ns}</span>
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                            )}

                                            {(result?.whois?.abuse?.email || result?.whois?.abuse?.phone) && (
                                                <div>
                                                    <h3 className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/35 mb-3 flex items-center gap-2">
                                                        <i className="fa-solid fa-circle-exclamation text-[10px]"></i> Kontak Abuse Registrar
                                                    </h3>
                                                    <div className="flex flex-wrap gap-2">
                                                        {[
                                                            { key: 'email', value: result?.whois?.abuse?.email },
                                                            { key: 'phone', value: result?.whois?.abuse?.phone },
                                                        ].filter(item => item.value).map(item => (
                                                            <div key={item.key} className="inline-flex items-center gap-2 bg-white dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] px-3.5 py-2.5">
                                                                <i className={`fa-solid text-[#94A3B4] dark:text-white/25 text-[11px] ${item.key === 'email' ? 'fa-envelope' : 'fa-phone'}`}></i>
                                                                <span className="text-xs text-[#0F1A2A] dark:text-white/70 font-mono">{item.value}</span>
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                            )}

                                            <div>
                                                <h3 className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#737373] dark:text-white/35 mb-3 flex items-center gap-2">
                                                    <i className="fa-solid fa-network-wired text-[10px]"></i> Record DNS Publik
                                                </h3>

                                                {!hasDnsRecords && (
                                                    <div className="text-xs text-[#737373] dark:text-white/35 italic bg-[#F4F8FC] dark:bg-[#0D1828] px-4 py-3 border border-dashed border-[#C9D5E2] dark:border-[#24384F]">
                                                        Tidak ada record DNS yang ditemukan oleh resolver (mungkin domain belum di-delegasikan atau resolver gagal).
                                                    </div>
                                                )}

                                                {hasDnsRecords && (
                                                    <div className="space-y-4">
                                                        {Object.entries(result.dns || {}).map(([type, records]) => (
                                                            records && records.length > 0 && (
                                                                <div key={type}>
                                                                    <p className="font-mono text-[10px] uppercase tracking-[0.18em] text-[#737373] dark:text-white/35 mb-2">
                                                                        {type} <span className="text-[#94A3B4] dark:text-white/20">({records.length})</span>
                                                                    </p>
                                                                    <div className="grid gap-px bg-[#E3EAF1] dark:bg-[#1B2C44] border border-[#E3EAF1] dark:border-[#1B2C44]">
                                                                        {records.map((record, idx) => (
                                                                            <div key={`${type}-${idx}`} className="flex items-center gap-2.5 bg-white dark:bg-[#0A1322] px-4 py-2.5">
                                                                                <span className="font-mono text-xs text-[#0F1A2A] dark:text-white/70 break-all">{record.ip || record.host || record.value}</span>
                                                                                {record.priority !== undefined && (
                                                                                    <span className="ml-auto font-mono text-[10px] text-[#737373] dark:text-white/35 bg-[#F4F8FC] dark:bg-[#0D1828] px-1.5 py-0.5 border border-[#E3EAF1] dark:border-[#1B2C44]">
                                                                                        prio {record.priority}
                                                                                    </span>
                                                                                )}
                                                                            </div>
                                                                        ))}
                                                                    </div>
                                                                </div>
                                                            )
                                                        ))}
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    )}

                                    {!loading && !error && !result && (
                                        <div className="flex flex-col items-center justify-center py-14 text-center">
                                            <div className="w-16 h-16 border border-[#E3EAF1] dark:border-[#1B2C44] bg-[#F4F8FC] dark:bg-[#0D1828] flex items-center justify-center mb-5">
                                                <i className="fa-solid fa-magnifying-glass text-2xl text-[#94A3B4] dark:text-white/20"></i>
                                            </div>
                                            <p className="font-mono text-[11px] uppercase tracking-[0.2em] text-[#525252] dark:text-white/40 mb-2">Belum ada domain</p>
                                            <p className="text-xs text-[#737373] dark:text-white/35 max-w-sm leading-relaxed">Masukkan nama domain pada kolom di atas, lalu klik <strong className="text-[#0F1A2A] dark:text-white/70">Cek Domain</strong> untuk melihat detail registrasi &amp; DNS-nya.</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </PublicLayout>
        </>
    );
}
