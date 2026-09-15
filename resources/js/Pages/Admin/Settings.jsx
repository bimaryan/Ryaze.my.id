import { usePage, useForm } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Settings() {
    const { settings, articleCategories } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        site_name: settings?.site_name || '',
        site_description: settings?.site_description || '',
        contact_email: settings?.contact_email || '',
        contact_whatsapp: settings?.contact_whatsapp || '',
        contact_instagram: settings?.contact_instagram || '',
        contact_github: settings?.contact_github || '',
        contact_discord: settings?.contact_discord || '',
        footer_text: settings?.footer_text || '',
        meta_title: settings?.meta_title || '',
        meta_description: settings?.meta_description || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/superadmin/settings');
    };

    const fields = [
        { key: 'site_name', label: 'Nama Situs', type: 'text' },
        { key: 'site_description', label: 'Deskripsi Situs', type: 'textarea' },
        { key: 'contact_email', label: 'Email Kontak', type: 'email' },
        { key: 'contact_whatsapp', label: 'WhatsApp', type: 'text' },
        { key: 'contact_instagram', label: 'Instagram', type: 'text' },
        { key: 'contact_github', label: 'GitHub', type: 'text' },
        { key: 'contact_discord', label: 'Discord', type: 'text' },
        { key: 'footer_text', label: 'Teks Footer', type: 'textarea' },
        { key: 'meta_title', label: 'Meta Title', type: 'text' },
        { key: 'meta_description', label: 'Meta Description', type: 'textarea' },
    ];

    return (
        <DashboardLayout title="Pengaturan">
            <div className="space-y-6">
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Informasi Umum</h3>
                        </div>
                        <div className="p-5 space-y-4">
                            {fields.map((field) => (
                                <div key={field.key}>
                                    <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">{field.label}</label>
                                    {field.type === 'textarea' ? (
                                        <textarea
                                            value={data[field.key]}
                                            onChange={(e) => setData(field.key, e.target.value)}
                                            rows={3}
                                            className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors resize-none"
                                        />
                                    ) : (
                                        <input
                                            type={field.type}
                                            value={data[field.key]}
                                            onChange={(e) => setData(field.key, e.target.value)}
                                            className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                        />
                                    )}
                                    {errors[field.key] && (
                                        <p className="mt-1 text-[12px] text-red-500">{errors[field.key]}</p>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="flex justify-end">
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] disabled:opacity-50 transition-colors"
                        >
                            {processing ? 'Menyimpan...' : 'Simpan Pengaturan'}
                        </button>
                    </div>
                </form>

                {articleCategories?.length > 0 && (
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Kategori Artikel</h3>
                        </div>
                        <div className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {articleCategories.map((cat) => (
                                <div key={cat.id} className="px-5 py-3 flex items-center justify-between">
                                    <span className="text-[13px] font-medium text-[#333] dark:text-white">{cat.name}</span>
                                    <span className="text-[12px] text-[#999] dark:text-white/40">{cat.articles_count || 0} artikel</span>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
