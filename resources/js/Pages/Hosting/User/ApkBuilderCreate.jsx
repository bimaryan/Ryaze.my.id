import { useState } from 'react';
import { usePage, Link, router } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function ApkBuilderCreate() {
    const { flash } = usePage().props;
    const [form, setForm] = useState({
        website_url: '',
        app_name: '',
        icon: null,
    });
    const [preview, setPreview] = useState(null);

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setForm({ ...form, icon: file });
            setPreview(URL.createObjectURL(file));
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const formData = new FormData();
        formData.append('website_url', form.website_url);
        formData.append('app_name', form.app_name);
        if (form.icon) formData.append('icon', form.icon);
        router.post('/user/hosting/apk', formData, { forceFormData: true });
    };

    return (
        <DashboardLayout title="Create APK">
            <div className="max-w-xl mx-auto space-y-6">
                {flash?.success && (
                    <div className="px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-300">
                        {flash.success}
                    </div>
                )}

                <div className="flex items-center gap-3">
                    <Link href="/user/hosting/apk" className="text-[13px] text-[#999] dark:text-white/40 hover:text-[#7c3aed] transition-colors">
                        <i className="fa-solid fa-arrow-left mr-1"></i>Back
                    </Link>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Web to APK Builder</h2>
                        <p className="text-[12px] text-[#999] dark:text-white/40 mt-1">Convert any website into an Android application.</p>
                    </div>

                    <form onSubmit={handleSubmit} className="p-5 space-y-5">
                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Website URL</label>
                            <input
                                type="url"
                                value={form.website_url}
                                onChange={(e) => setForm({ ...form, website_url: e.target.value })}
                                placeholder="https://example.com"
                                required
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                            />
                        </div>

                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">App Name</label>
                            <input
                                type="text"
                                value={form.app_name}
                                onChange={(e) => setForm({ ...form, app_name: e.target.value })}
                                placeholder="My App"
                                required
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                            />
                        </div>

                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">App Icon (Optional)</label>
                            <div className="flex items-center gap-4">
                                <div className="w-20 h-20 border-2 border-dashed border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-center overflow-hidden">
                                    {preview ? (
                                        <img src={preview} alt="Preview" className="w-full h-full object-cover" />
                                    ) : (
                                        <i className="fa-solid fa-image text-[#999] dark:text-white/20 text-xl"></i>
                                    )}
                                </div>
                                <div>
                                    <label className="px-4 py-2 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[13px] font-medium text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors cursor-pointer inline-block">
                                        <i className="fa-solid fa-upload mr-1.5"></i>Choose File
                                    </label>
                                    <input type="file" accept="image/*" onChange={handleFileChange} className="hidden" />
                                    <p className="text-[11px] text-[#999] dark:text-white/30 mt-1">PNG, JPG, 512x512 recommended</p>
                                </div>
                            </div>
                        </div>

                        <div className="flex items-center gap-3 pt-2">
                            <button
                                type="submit"
                                disabled={!form.website_url || !form.app_name}
                                className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                            >
                                <i className="fa-solid fa-hammer mr-1.5"></i>Build APK
                            </button>
                            <Link href="/user/hosting/apk" className="px-5 py-2 text-[13px] font-medium text-[#666] dark:text-white/60 hover:text-[#7c3aed] transition-colors">
                                Cancel
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}
