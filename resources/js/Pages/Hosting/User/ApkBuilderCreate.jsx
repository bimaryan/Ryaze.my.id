import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, usePage, router } from '@inertiajs/react';
import { useState, useRef } from 'react';

function Toggle({ label, description, checked, onChange }) {
    return (
        <div className="flex items-center justify-between py-3 border-b border-[#e5e5e5] dark:border-[#1a1a2e] last:border-0">
            <div>
                <p className="text-sm font-semibold text-[#333] dark:text-white">{label}</p>
                {description && <p className="text-xs text-[#999] dark:text-white/40 mt-0.5">{description}</p>}
            </div>
            <button type="button" onClick={onChange}
                className={`relative w-11 h-6 rounded-full transition-colors ${checked ? 'bg-[#7c3aed]' : 'bg-slate-300 dark:bg-slate-600'}`}>
                <span className={`absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform ${checked ? 'translate-x-5' : ''}`}></span>
            </button>
        </div>
    );
}

export default function ApkBuilderCreate({ user }) {
    const [iconPreview, setIconPreview] = useState(null);
    const fileInputRef = useRef(null);
    const hasSubscription = user?.has_hosting_subscription;

    const { data, setData, post, processing } = useForm({
        app_name: '',
        app_url: '',
        package_name: '',
        theme_color: '#7c3aed',
        background_color: '#ffffff',
        nav_bar_color: '#7c3aed',
        splash_fade_duration: '1000',
        display_mode: 'standalone',
        orientation: 'portrait',
        push_notifications: false,
        fallback_engine: 'webview',
        icon: null,
        version_name: '1.0.0',
        version_code: '1',
        keystore_alias: '',
        store_password: '',
        key_password: '',
    });

    function handleIconSelect(e) {
        const file = e.target.files?.[0];
        if (file) {
            setData('icon', file);
            const reader = new FileReader();
            reader.onload = (ev) => setIconPreview(ev.target.result);
            reader.readAsDataURL(file);
        }
    }

    function handleSubmit(e) {
        e.preventDefault();
        post(route('user_hosting.apk.store'), {
            forceFormData: true,
        });
    }

    if (!hasSubscription) {
        return (
            <DashboardLayout title="Web to APK">
                <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] p-12 text-center">
                    <div className="w-16 h-16 bg-amber-100 dark:bg-amber-500/20 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i className="fa-solid fa-crown"></i>
                    </div>
                    <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Langganan Diperlukan</h3>
                    <p className="text-[#999] dark:text-white/40 mb-6 text-sm">Anda memerlukan langganan hosting aktif untuk menggunakan fitur Web to APK.</p>
                    <a href={route('user_hosting.subscription')}
                        className="inline-flex items-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                        <i className="fa-solid fa-crown"></i> Lihat Paket
                    </a>
                </div>
            </DashboardLayout>
        );
    }

    return (
        <DashboardLayout title="Buat APK Baru">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-brands fa-android text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Buat APK Baru</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Konversi website menjadi aplikasi Android.</p>
                    </div>
                </div>
            </div>

            <form onSubmit={handleSubmit} encType="multipart/form-data" className="mt-6">
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Left: Main Config */}
                    <div className="lg:col-span-2 space-y-6">
                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm mb-4 flex items-center gap-2">
                                <i className="fa-solid fa-globe text-[#7c3aed]"></i> Info Aplikasi
                            </h3>
                            <div className="space-y-4">
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">App Name</label>
                                    <input type="text" value={data.app_name} onChange={e => setData('app_name', e.target.value)} required
                                        placeholder="My Awesome App"
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">App URL</label>
                                    <input type="url" value={data.app_url} onChange={e => setData('app_url', e.target.value)} required
                                        placeholder="https://myapp.ryaze.my.id"
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                    <p className="text-[10px] text-[#999] dark:text-white/40 mt-1">Harus HTTPS</p>
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Package Name</label>
                                    <input type="text" value={data.package_name} onChange={e => setData('package_name', e.target.value)} required
                                        placeholder="com.example.myapp"
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                </div>
                            </div>
                        </div>

                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm mb-4 flex items-center gap-2">
                                <i className="fa-solid fa-palette text-[#7c3aed]"></i> Tema & Tampilan
                            </h3>
                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Theme Color</label>
                                    <div className="flex items-center gap-2">
                                        <input type="color" value={data.theme_color} onChange={e => setData('theme_color', e.target.value)}
                                            className="w-10 h-10 rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e] cursor-pointer" />
                                        <input type="text" value={data.theme_color} onChange={e => setData('theme_color', e.target.value)}
                                            className="flex-1 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-3 py-2 text-xs text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Background Color</label>
                                    <div className="flex items-center gap-2">
                                        <input type="color" value={data.background_color} onChange={e => setData('background_color', e.target.value)}
                                            className="w-10 h-10 rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e] cursor-pointer" />
                                        <input type="text" value={data.background_color} onChange={e => setData('background_color', e.target.value)}
                                            className="flex-1 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-3 py-2 text-xs text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Nav Bar Color</label>
                                    <div className="flex items-center gap-2">
                                        <input type="color" value={data.nav_bar_color} onChange={e => setData('nav_bar_color', e.target.value)}
                                            className="w-10 h-10 rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e] cursor-pointer" />
                                        <input type="text" value={data.nav_bar_color} onChange={e => setData('nav_bar_color', e.target.value)}
                                            className="flex-1 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-3 py-2 text-xs text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                    </div>
                                </div>
                            </div>
                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Splash Fade Duration (ms)</label>
                                    <input type="number" value={data.splash_fade_duration} onChange={e => setData('splash_fade_duration', e.target.value)}
                                        min={0} max={5000}
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Display Mode</label>
                                    <select value={data.display_mode} onChange={e => setData('display_mode', e.target.value)}
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition">
                                        <option value="standalone">Standalone</option>
                                        <option value="fullscreen">Fullscreen</option>
                                        <option value="minimal-ui">Minimal UI</option>
                                        <option value="browser">Browser</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Orientation</label>
                                    <select value={data.orientation} onChange={e => setData('orientation', e.target.value)}
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition">
                                        <option value="portrait">Portrait</option>
                                        <option value="landscape">Landscape</option>
                                        <option value="any">Any</option>
                                    </select>
                                </div>
                            </div>
                            <div className="mt-4">
                                <Toggle label="Push Notifications" description="Aktifkan notifikasi push di aplikasi."
                                    checked={data.push_notifications} onChange={() => setData('push_notifications', !data.push_notifications)} />
                            </div>
                            <div className="mt-2">
                                <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Fallback Engine</label>
                                <select value={data.fallback_engine} onChange={e => setData('fallback_engine', e.target.value)}
                                    className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition">
                                    <option value="webview">WebView</option>
                                    <option value="chrome">Chrome Custom Tab</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Right: Icon & Keystore */}
                    <div className="space-y-6">
                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm mb-4 flex items-center gap-2">
                                <i className="fa-solid fa-image text-[#7c3aed]"></i> Icon Aplikasi
                            </h3>
                            <div className="text-center">
                                {iconPreview ? (
                                    <div className="mb-3">
                                        <img src={iconPreview} alt="Icon Preview" className="w-24 h-24 mx-auto rounded-2xl border border-[#e5e5e5] dark:border-[#1a1a2e] shadow-sm" />
                                    </div>
                                ) : (
                                    <div className="w-24 h-24 mx-auto bg-[#fafafa] dark:bg-white/[0.02] border-2 border-dashed border-[#e5e5e5] dark:border-[#1a1a2e] rounded-2xl flex items-center justify-center mb-3">
                                        <i className="fa-solid fa-image text-[#ccc] dark:text-white/20 text-2xl"></i>
                                    </div>
                                )}
                                <input type="file" ref={fileInputRef} onChange={handleIconSelect} accept="image/png,image/jpeg" className="hidden" />
                                <button type="button" onClick={() => fileInputRef.current?.click()}
                                    className="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-[#7c3aed] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 rounded-lg hover:bg-[#ede4ff] dark:hover:bg-[#7c3aed]/20 transition">
                                    <i className="fa-solid fa-upload"></i> Pilih Icon (PNG/JPG)
                                </button>
                            </div>
                        </div>

                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm mb-4 flex items-center gap-2">
                                <i className="fa-solid fa-key text-[#7c3aed]"></i> Keystore & Versioning
                            </h3>
                            <div className="space-y-3">
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Version Name</label>
                                    <input type="text" value={data.version_name} onChange={e => setData('version_name', e.target.value)}
                                        placeholder="1.0.0"
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Version Code</label>
                                    <input type="number" value={data.version_code} onChange={e => setData('version_code', e.target.value)}
                                        min={1}
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Keystore Alias</label>
                                    <input type="text" value={data.keystore_alias} onChange={e => setData('keystore_alias', e.target.value)}
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Store Password</label>
                                    <input type="password" value={data.store_password} onChange={e => setData('store_password', e.target.value)}
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Key Password</label>
                                    <input type="password" value={data.key_password} onChange={e => setData('key_password', e.target.value)}
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                </div>
                            </div>
                        </div>

                        <button type="submit" disabled={processing}
                            className="w-full bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-bold py-3 rounded-xl text-sm transition flex items-center justify-center gap-2 shadow-sm disabled:opacity-50">
                            {processing ? (
                                <><i className="fa-solid fa-spinner fa-spin"></i> Memproses...</>
                            ) : (
                                <><i className="fa-brands fa-android"></i> Build APK</>
                            )}
                        </button>
                    </div>
                </div>
            </form>
        </DashboardLayout>
    );
}
