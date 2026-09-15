import DashboardLayout from '../../Layouts/DashboardLayout';
import { useForm, usePage, router } from '@inertiajs/react';
import { useState, useCallback } from 'react';

function parseToMB(val) {
    val = val.toString().trim().toLowerCase().replace(/\s+/g, '');
    const num = parseFloat(val);
    if (isNaN(num)) return 0;
    if (/tb$/.test(val)) return Math.round(num * 1048576);
    if (/gb$/.test(val)) return Math.round(num * 1024);
    if (/mb$/.test(val)) return Math.round(num);
    return Math.round(num);
}

function formatMB(mb) {
    if (mb === 0) return '0 MB';
    if (mb < 1024) return mb + ' MB';
    if (mb < 1048576) return (mb / 1024).toFixed(2).replace(/\.?0+$/, '') + ' GB';
    return (mb / 1048576).toFixed(2).replace(/\.?0+$/, '') + ' TB';
}

const plans = [
    { key: 'free', label: 'Paket Free', defaults: { price: '', promo: '', storage: '256', max_projects: '1', features: '256 MB Storage\nMaks. 1 Project\n1 MySQL Database\nSubdomain Bawaan\nPrioritas Support' } },
    { key: 'starter', label: 'Paket Starter', defaults: { price: '15000', promo: '', storage: '1024', max_projects: '3', features: '1 GB Storage\nMaks. 3 Project\nMySQL & PostgreSQL\nSubdomain Bawaan\nPrioritas Support' } },
    { key: 'pro', label: 'Paket Pro', defaults: { price: '30000', promo: '', storage: '3072', max_projects: '10', features: '3 GB Storage\nMaks. 10 Project\nMySQL, PostgreSQL & Redis\nSubdomain Bawaan\nPrioritas Support' } },
    { key: 'business', label: 'Paket Business', defaults: { price: '75000', promo: '', storage: '10240', max_projects: '-1', features: '10 GB Storage\nProject Unlimited\nSemua Database\nSubdomain Bawaan\nPrioritas Support' } },
];

export default function Settings({ settings, articleCategories }) {
    const { flash } = usePage().props;
    const [logoPreview, setLogoPreview] = useState(settings?.site_logo ? `/storage/${settings.site_logo}` : null);
    const [faviconPreview, setFaviconPreview] = useState(settings?.site_favicon ? `/storage/${settings.site_favicon}` : null);

    const { data, setData, post, processing } = useForm({
        site_name: settings?.site_name || 'Ryaze Portal',
        site_description: settings?.site_description || '',
        site_logo: null,
        site_favicon: null,
        contact_whatsapp: settings?.contact_whatsapp || '',
        contact_email: settings?.contact_email || '',
        social_github: settings?.social_github || '',
        social_instagram: settings?.social_instagram || '',
        social_linkedin: settings?.social_linkedin || '',
        wa_api_endpoint: settings?.wa_api_endpoint || 'https://api.ryz.my.id/api/whatsapp/v1/send-message',
        wa_api_token: settings?.wa_api_token || '',
        payment_dana: settings?.payment_dana || '085157433395',
        admin_fee_percentage: settings?.admin_fee_percentage || '0',
        available_frameworks: settings?.available_frameworks || 'html,php,laravel,react,nextjs,python,node,vue',
        enable_registration: settings?.enable_registration !== '0',
        maintenance_mode: settings?.maintenance_mode === '1',
        ...plans.reduce((acc, p) => {
            acc[`plan_${p.key}_active`] = settings?.[`plan_${p.key}_active`] !== '0';
            acc[`plan_${p.key}_price`] = settings?.[`plan_${p.key}_price`] || p.defaults.price;
            acc[`plan_${p.key}_promo`] = settings?.[`plan_${p.key}_promo`] || p.defaults.promo;
            acc[`plan_${p.key}_storage`] = settings?.[`plan_${p.key}_storage`] || p.defaults.storage;
            acc[`plan_${p.key}_max_projects`] = settings?.[`plan_${p.key}_max_projects`] || p.defaults.max_projects;
            acc[`plan_${p.key}_features`] = settings?.[`plan_${p.key}_features`] || p.defaults.features;
            return acc;
        }, {}),
        blog_ai_topics: settings?.blog_ai_topics || '',
        blog_ai_category_id: settings?.blog_ai_category_id || '',
        blog_ai_frequency: settings?.blog_ai_frequency || 'daily',
        blog_ai_enabled: settings?.blog_ai_enabled === '1',
        blog_ai_auto_publish: settings?.blog_ai_auto_publish === '1',
        '1panel_url': settings?.['1panel_url'] || '',
        '1panel_api_key': settings?.['1panel_api_key'] || '',
        pakasir_server_key: settings?.pakasir_server_key || '',
        google_analytics_id: settings?.google_analytics_id || '',
    });

    const [frameworkTags, setFrameworkTags] = useState(
        (data.available_frameworks || '').split(',').filter(t => t.trim())
    );
    const [newTag, setNewTag] = useState('');
    const [storageHints, setStorageHints] = useState(() => {
        const hints = {};
        plans.forEach(p => {
            const val = data[`plan_${p.key}_storage`] || p.defaults.storage;
            const mb = parseToMB(val);
            hints[p.key] = { mb, display: formatMB(mb) };
        });
        return hints;
    });

    const handleChange = useCallback((e) => {
        const { name, value, type, checked, files } = e.target;
        if (type === 'file') {
            setData(name, files[0]);
            if (name === 'site_logo' && files[0]) {
                setLogoPreview(URL.createObjectURL(files[0]));
            }
            if (name === 'site_favicon' && files[0]) {
                setFaviconPreview(URL.createObjectURL(files[0]));
            }
        } else if (type === 'checkbox') {
            setData(name, checked);
        } else {
            setData(name, value);
        }
    }, [setData]);

    const handleStorageChange = useCallback((planKey, value) => {
        setData(`plan_${planKey}_storage`, value);
        const mb = parseToMB(value);
        setStorageHints(prev => ({ ...prev, [planKey]: { mb, display: formatMB(mb) } }));
    }, [setData]);

    const addFrameworkTag = () => {
        const tag = newTag.trim().toLowerCase().replace(/[^a-z0-9]/g, '');
        if (tag && !frameworkTags.includes(tag)) {
            const updated = [...frameworkTags, tag];
            setFrameworkTags(updated);
            setData('available_frameworks', updated.join(','));
        }
        setNewTag('');
    };

    const removeFrameworkTag = (index) => {
        const updated = frameworkTags.filter((_, i) => i !== index);
        setFrameworkTags(updated);
        setData('available_frameworks', updated.join(','));
    };

    const handleFrameworkKeyDown = (e) => {
        if (e.key === 'Enter' || e.key === ' ' || e.key === ',') {
            e.preventDefault();
            addFrameworkTag();
        }
        if (e.key === 'Backspace' && newTag === '' && frameworkTags.length > 0) {
            removeFrameworkTag(frameworkTags.length - 1);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const submitData = { ...data };
        plans.forEach(p => {
            submitData[`plan_${p.key}_storage`] = parseToMB(data[`plan_${p.key}_storage`]);
        });
        submitData.enable_registration = data.enable_registration ? '1' : '0';
        submitData.maintenance_mode = data.maintenance_mode ? '1' : '0';
        submitData.blog_ai_enabled = data.blog_ai_enabled ? '1' : '0';
        submitData.blog_ai_auto_publish = data.blog_ai_auto_publish ? '1' : '0';
        plans.forEach(p => {
            submitData[`plan_${p.key}_active`] = data[`plan_${p.key}_active`] ? '1' : '0';
        });
        router.post(route('superadmin.settings.update'), submitData, {
            forceFormData: true,
            preserveScroll: true,
        });
    };

    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";
    const fileInputCls = "w-full border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg shadow-sm focus:ring-[#7c3aed] focus:border-[#7c3aed] p-2 text-sm text-[#333] dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#f5f0ff] file:text-[#7c3aed] hover:file:bg-[#ede9fe] dark:file:bg-[#7c3aed]/10 dark:file:text-[#a78bfa] dark:hover:file:bg-[#7c3aed]/20";

    return (
        <DashboardLayout title="Pengaturan Sistem">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-cogs text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Pengaturan Sistem</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola pengaturan global untuk platform Ryaze.</p>
                    </div>
                </div>
            </div>

            {flash?.success && (
                <div className="mt-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-300 text-sm rounded-xl">
                    {flash.success}
                </div>
            )}

            <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-6">
                <form onSubmit={handleSubmit} className="space-y-8">

                    {/* Section 1: Identitas & SEO */}
                    <div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">
                            <i className="fa-solid fa-id-card text-[#7c3aed] dark:text-[#a78bfa] mr-2"></i> Identitas & SEO
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Nama Website</label>
                                <input type="text" name="site_name" value={data.site_name} onChange={handleChange} className={inputCls} />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Deskripsi Website (SEO)</label>
                                <input type="text" name="site_description" value={data.site_description} onChange={handleChange} className={inputCls} placeholder="Portal layanan joki dan hosting terbaik..." />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Upload Logo (opsional)</label>
                                {logoPreview && <img src={logoPreview} alt="Logo" className="h-12 mb-2 rounded border border-[#e5e5e5] dark:border-[#1a1a2e] p-1 bg-[#fafafa] dark:bg-[#0d0d18]/60 object-contain" />}
                                <input type="file" name="site_logo" accept="image/*" onChange={handleChange} className={fileInputCls} />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Upload Favicon (opsional)</label>
                                {faviconPreview && <img src={faviconPreview} alt="Favicon" className="h-8 mb-2 rounded border border-[#e5e5e5] dark:border-[#1a1a2e] p-1 bg-[#fafafa] dark:bg-[#0d0d18]/60 object-contain" />}
                                <input type="file" name="site_favicon" accept="image/*" onChange={handleChange} className={fileInputCls} />
                            </div>
                        </div>
                    </div>

                    {/* Section 2: Kontak & Sosial Media */}
                    <div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">
                            <i className="fa-solid fa-address-book text-[#7c3aed] dark:text-[#a78bfa] mr-2"></i> Kontak & Sosial Media
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Nomor WhatsApp CS</label>
                                <div className="flex">
                                    <span className="inline-flex items-center px-4 py-2.5 text-sm text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-[#0d0d18]/80 border border-r-0 border-[#e5e5e5] dark:border-[#1a1a2e] rounded-l-xl">+62</span>
                                    <input type="text" name="contact_whatsapp" value={data.contact_whatsapp} onChange={handleChange} className={`${inputCls} rounded-l-none`} placeholder="81234567890" />
                                </div>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Email Dukungan</label>
                                <input type="email" name="contact_email" value={data.contact_email} onChange={handleChange} className={inputCls} placeholder="support@ryaze.my.id" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">URL GitHub</label>
                                <input type="url" name="social_github" value={data.social_github} onChange={handleChange} className={inputCls} placeholder="https://github.com/bimaryan" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">URL Instagram</label>
                                <input type="url" name="social_instagram" value={data.social_instagram} onChange={handleChange} className={inputCls} placeholder="https://instagram.com/bimaryan" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">URL LinkedIn</label>
                                <input type="url" name="social_linkedin" value={data.social_linkedin} onChange={handleChange} className={inputCls} placeholder="https://linkedin.com/in/bimaryan" />
                            </div>
                        </div>
                    </div>

                    {/* Section 3: API & Integrasi */}
                    <div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">
                            <i className="fa-solid fa-plug text-[#7c3aed] dark:text-[#a78bfa] mr-2"></i> API & Integrasi
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">WhatsApp API Endpoint</label>
                                <input type="text" name="wa_api_endpoint" value={data.wa_api_endpoint} onChange={handleChange} className={inputCls} />
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">Kosongkan jika tidak menggunakan WA Notif.</p>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">WhatsApp API Token</label>
                                <input type="password" name="wa_api_token" value={data.wa_api_token} onChange={handleChange} className={inputCls} placeholder="Token API" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Nomor DANA (Transfer Manual)</label>
                                <input type="text" name="payment_dana" value={data.payment_dana} onChange={handleChange} className={inputCls} placeholder="085157433395" />
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">Digunakan untuk opsi pembayaran Transfer Manual via DANA.</p>
                            </div>
                        </div>
                    </div>

                    {/* Section 4: Kontrol Layanan */}
                    <div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">
                            <i className="fa-solid fa-sliders text-[#7c3aed] dark:text-[#a78bfa] mr-2"></i> Kontrol Layanan
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Biaya Admin / Pajak Layanan (%)</label>
                                <input type="number" name="admin_fee_percentage" value={data.admin_fee_percentage} onChange={handleChange} className={inputCls} min="0" max="100" step="0.1" />
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">Biaya admin yang ditambahkan ke setiap tagihan pembayaran (%).</p>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Pilihan Framework Tersedia</label>
                                <div className="w-full bg-[#fafafa] dark:bg-[#0d0d18]/60 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-2 flex flex-wrap gap-2 focus-within:ring-2 focus-within:ring-[#7c3aed]/20 focus-within:border-[#7c3aed] transition-all min-h-[46px]">
                                    {frameworkTags.map((tag, index) => (
                                        <span key={index} className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-sm font-medium bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa]">
                                            {tag}
                                            <button type="button" onClick={() => removeFrameworkTag(index)} className="text-[#7c3aed]/60 hover:text-[#7c3aed] focus:outline-none">
                                                <i className="fa-solid fa-xmark"></i>
                                            </button>
                                        </span>
                                    ))}
                                    <input
                                        type="text"
                                        value={newTag}
                                        onChange={(e) => setNewTag(e.target.value)}
                                        onKeyDown={handleFrameworkKeyDown}
                                        placeholder="Ketik lalu Enter..."
                                        className="flex-1 bg-transparent border-none outline-none focus:ring-0 text-sm text-[#333] dark:text-white min-w-[120px] p-1"
                                    />
                                </div>
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">Ketik nama framework lalu tekan Enter atau Spasi.</p>
                            </div>

                            <div className="space-y-4">
                                <div className="p-4 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 border border-[#ede9fe] dark:border-[#7c3aed]/30 rounded-xl">
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <h4 className="font-bold text-[#7c3aed] dark:text-[#a78bfa] text-sm">Buka Pendaftaran Akun</h4>
                                            <p className="text-xs text-[#7c3aed]/60 dark:text-[#a78bfa]/60 mt-1">Matikan untuk menutup registrasi pengguna baru.</p>
                                        </div>
                                        <label className="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="enable_registration" checked={data.enable_registration} onChange={handleChange} className="sr-only peer" />
                                            <div className="w-11 h-6 bg-[#e5e5e5] dark:bg-[#1a1a2e] peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#7c3aed]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#ccc] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#7c3aed]"></div>
                                        </label>
                                    </div>
                                </div>

                                <div className="p-4 bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/30 rounded-xl">
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <h4 className="font-bold text-red-800 dark:text-red-200 text-sm">Mode Pemeliharaan</h4>
                                            <p className="text-xs text-red-600 dark:text-red-300 mt-1">Aktifkan untuk memblokir akses ke fitur klien.</p>
                                        </div>
                                        <label className="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="maintenance_mode" checked={data.maintenance_mode} onChange={handleChange} className="sr-only peer" />
                                            <div className="w-11 h-6 bg-red-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-red-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Section 5: Harga Hosting */}
                    <div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">
                            <i className="fa-solid fa-tags text-[#7c3aed] dark:text-[#a78bfa] mr-2"></i> Harga Langganan Hosting (Paket)
                        </h3>
                        <p className="text-xs text-[#999] dark:text-white/40 mb-4">Perubahan di sini otomatis sync ke landing page & halaman pilih paket.</p>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            {plans.map((plan) => (
                                <div key={plan.key} className="bg-[#fafafa] dark:bg-[#0d0d18]/60 border border-[#e5e5e5] dark:border-[#1a1a2e] p-4 rounded-xl flex flex-col justify-between">
                                    <div>
                                        <div className="flex items-center justify-between mb-3 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">
                                            <h4 className="font-bold text-[#333] dark:text-white">{plan.label}</h4>
                                            <label className="relative inline-flex items-center cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    name={`plan_${plan.key}_active`}
                                                    checked={data[`plan_${plan.key}_active`]}
                                                    onChange={handleChange}
                                                    className="sr-only peer"
                                                />
                                                <div className="w-9 h-5 bg-[#e5e5e5] dark:bg-[#1a1a2e] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#7c3aed]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#ccc] after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#7c3aed]"></div>
                                            </label>
                                        </div>
                                        <div className="space-y-3">
                                            {plan.key !== 'free' && (
                                                <>
                                                    <div>
                                                        <label className="block text-xs font-medium text-[#666] dark:text-white/60 mb-1">Harga Normal (Rp)</label>
                                                        <input type="number" name={`plan_${plan.key}_price`} value={data[`plan_${plan.key}_price`]} onChange={handleChange} className={inputCls} min="0" step="1000" />
                                                    </div>
                                                    <div>
                                                        <label className="block text-xs font-medium text-[#666] dark:text-white/60 mb-1">Harga Promo (Rp)</label>
                                                        <input type="number" name={`plan_${plan.key}_promo`} value={data[`plan_${plan.key}_promo`]} onChange={handleChange} className={inputCls} min="0" step="1000" placeholder="Kosongkan jika tak ada promo" />
                                                    </div>
                                                </>
                                            )}
                                            <div>
                                                <label className="block text-xs font-medium text-[#666] dark:text-white/60 mb-1">Storage</label>
                                                <input type="text" value={data[`plan_${plan.key}_storage`]} onChange={(e) => handleStorageChange(plan.key, e.target.value)} className={inputCls} placeholder="Contoh: 4gb, 512mb, 1tb" />
                                                <p className="text-xs text-[#7c3aed] dark:text-[#a78bfa] mt-1 font-medium">= {storageHints[plan.key]?.display} ({storageHints[plan.key]?.mb} MB)</p>
                                            </div>
                                            <div>
                                                <label className="block text-xs font-medium text-[#666] dark:text-white/60 mb-1">Maks. Project (-1 = unlimited)</label>
                                                <input type="number" name={`plan_${plan.key}_max_projects`} value={data[`plan_${plan.key}_max_projects`]} onChange={handleChange} className={inputCls} min="-1" />
                                            </div>
                                            <div>
                                                <label className="block text-xs font-medium text-[#666] dark:text-white/60 mb-1">Fitur (satu per baris)</label>
                                                <textarea name={`plan_${plan.key}_features`} value={data[`plan_${plan.key}_features`]} onChange={handleChange} rows="4" className={inputCls} />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Section 6: Blog AI */}
                    <div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">
                            <i className="fa-solid fa-wand-magic-sparkles text-violet-500 dark:text-violet-400 mr-2"></i> Otomasi Blog AI
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Daftar Topik</label>
                                <textarea name="blog_ai_topics" value={data.blog_ai_topics} onChange={handleChange} rows="4" className={inputCls} placeholder="Satu topik per baris, misalnya:\nPanduan deploy website Laravel\nCara memilih hosting untuk UMKM" />
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">Sistem memilih satu topik secara acak dan menghasilkan artikel beserta cover image.</p>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Kategori Default</label>
                                <select name="blog_ai_category_id" value={data.blog_ai_category_id} onChange={handleChange} className={inputCls}>
                                    <option value="">Tanpa kategori</option>
                                    {articleCategories?.map((cat) => (
                                        <option key={cat.id} value={cat.id}>{cat.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Frekuensi</label>
                                <select name="blog_ai_frequency" value={data.blog_ai_frequency} onChange={handleChange} className={inputCls}>
                                    <option value="daily">Setiap hari</option>
                                    <option value="weekly">Setiap minggu</option>
                                </select>
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">Scheduler mengecek setiap jam dan membuat satu artikel saat jadwalnya tiba.</p>
                            </div>
                            <div className="p-4 bg-violet-50 dark:bg-violet-500/10 border border-violet-100 dark:border-violet-500/30 rounded-xl">
                                <div className="flex items-center justify-between gap-4">
                                    <div>
                                        <h4 className="font-bold text-violet-800 dark:text-violet-200 text-sm">Aktifkan Otomasi</h4>
                                        <p className="text-xs text-violet-700 dark:text-violet-300 mt-1">Butuh OPENAI_API_KEY dan queue worker yang aktif.</p>
                                    </div>
                                    <label className="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="blog_ai_enabled" checked={data.blog_ai_enabled} onChange={handleChange} className="sr-only peer" />
                                        <div className="w-11 h-6 bg-[#e5e5e5] dark:bg-[#1a1a2e] peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-violet-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-600"></div>
                                    </label>
                                </div>
                            </div>
                            <div className="p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/30 rounded-xl">
                                <div className="flex items-center justify-between gap-4">
                                    <div>
                                        <h4 className="font-bold text-amber-800 dark:text-amber-200 text-sm">Auto-publish</h4>
                                        <p className="text-xs text-amber-700 dark:text-amber-300 mt-1">Matikan agar hasil otomatis selalu masuk draft untuk review.</p>
                                    </div>
                                    <label className="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="blog_ai_auto_publish" checked={data.blog_ai_auto_publish} onChange={handleChange} className="sr-only peer" />
                                        <div className="w-11 h-6 bg-[#e5e5e5] dark:bg-[#1a1a2e] peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-amber-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Section 7: 1Panel */}
                    <div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">
                            <i className="fa-solid fa-server text-[#7c3aed] dark:text-[#a78bfa] mr-2"></i> API & Integrasi 1Panel
                        </h3>
                        <p className="text-xs text-[#999] dark:text-white/40 mb-4">Integrasi ini digunakan untuk memantau status kesehatan server langsung dari 1Panel.</p>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">1Panel API URL</label>
                                <input type="url" name="1panel_url" value={data['1panel_url']} onChange={handleChange} className={inputCls} placeholder="https://192.168.1.1:10086" />
                                <p className="text-[11px] text-[#999] dark:text-white/40 mt-1">URL panel tanpa trailing slash (contoh: https://ip:port)</p>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">1Panel API Key / Token</label>
                                <input type="text" name="1panel_api_key" value={data['1panel_api_key']} onChange={handleChange} className={inputCls} placeholder="Token dari 1Panel API" />
                                <p className="text-[11px] text-[#999] dark:text-white/40 mt-1">Dapatkan di 1Panel &gt; Pengaturan Panel &gt; API.</p>
                            </div>
                        </div>
                    </div>

                    {/* Section 8: Integrasi Eksternal */}
                    <div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">
                            <i className="fa-solid fa-plug text-[#7c3aed] dark:text-[#a78bfa] mr-2"></i> Integrasi Eksternal
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Pakasir Server Key</label>
                                <input type="password" name="pakasir_server_key" value={data.pakasir_server_key} onChange={handleChange} className={inputCls} placeholder="Pakasir API Server Key" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-1">Google Analytics ID</label>
                                <input type="text" name="google_analytics_id" value={data.google_analytics_id} onChange={handleChange} className={inputCls} placeholder="G-XXXXXXXXXX" />
                            </div>
                        </div>
                    </div>

                    <div className="pt-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-end">
                        <button type="submit" disabled={processing} className="px-5 py-2.5 bg-[#7c3aed] text-white rounded-lg font-medium hover:bg-[#6d28d9] shadow-sm transition disabled:opacity-50">
                            <i className="fa-solid fa-save mr-2"></i> {processing ? 'Menyimpan...' : 'Simpan Pengaturan'}
                        </button>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}
