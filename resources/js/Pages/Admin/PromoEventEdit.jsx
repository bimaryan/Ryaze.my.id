import DashboardLayout from '../../Layouts/DashboardLayout';
import { useForm, Link } from '@inertiajs/react';

export default function PromoEventEdit({ promo_event }) {
    const toLocalDatetime = (dateStr) => {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        const pad = (n) => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    };

    const { data, setData, post, processing, errors } = useForm({
        title: promo_event.title || '',
        description: promo_event.description || '',
        start_date: toLocalDatetime(promo_event.start_date),
        end_date: toLocalDatetime(promo_event.end_date),
        banner_image: null,
        is_active: promo_event.is_active || false,
        _method: 'PUT',
    });

    function handleChange(e) {
        const { name, value, type, checked, files } = e.target;
        if (type === 'file') {
            setData(name, files[0]);
        } else if (type === 'checkbox') {
            setData(name, checked);
        } else {
            setData(name, value);
        }
    }

    function handleSubmit(e) {
        e.preventDefault();
        post(route('admin.promo_events.update', { hashid: promo_event.hashid }), { forceFormData: true });
    }

    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";

    return (
        <DashboardLayout title="Edit Promo Event">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-regular fa-pen-to-square text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Edit Promo Event</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Perbarui data promo event.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href={route('admin.promo_events.index')} className="bg-white dark:bg-[#0d0d18]/60 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:bg-[#fafafa] dark:hover:bg-white/5 px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-sm p-6 mt-6">
                <form onSubmit={handleSubmit} encType="multipart/form-data">
                    <div className="space-y-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Judul Promo <span className="text-red-500 dark:text-red-400">*</span></label>
                                <input type="text" value={data.title} onChange={handleChange} required className={inputCls} />
                                {errors.title && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.title}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Target URL</label>
                                <input type="url" name="target_url" value={data.target_url || ''} onChange={handleChange} className={inputCls} placeholder="https://..." />
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">Opsional. Link saat banner diklik.</p>
                                {errors.target_url && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.target_url}</p>}
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Deskripsi Promo</label>
                            <textarea value={data.description} onChange={handleChange} rows="3" className={inputCls} />
                            {errors.description && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.description}</p>}
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Mulai Berlaku <span className="text-red-500 dark:text-red-400">*</span></label>
                                <input type="datetime-local" name="start_date" value={data.start_date} onChange={handleChange} required className={inputCls} />
                                {errors.start_date && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.start_date}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Selesai Berlaku <span className="text-red-500 dark:text-red-400">*</span></label>
                                <input type="datetime-local" name="end_date" value={data.end_date} onChange={handleChange} required className={inputCls} />
                                {errors.end_date && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.end_date}</p>}
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Banner Image</label>
                            {promo_event.banner_image && (
                                <div className="mb-3">
                                    <p className="text-xs text-[#999] dark:text-white/40 mb-2">Gambar saat ini:</p>
                                    <img src={promo_event.banner_url || `/storage/${promo_event.banner_image}`} alt="Banner" className="w-48 object-cover rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e] shadow-sm" />
                                </div>
                            )}
                            <input type="file" name="banner_image" accept="image/*" onChange={handleChange} className="block w-full text-sm text-[#999] dark:text-white/40 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#f5f0ff] file:text-[#7c3aed] hover:file:bg-[#ede9fe] dark:file:bg-[#7c3aed]/10 dark:file:text-[#a78bfa] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg" />
                            <p className="text-xs text-[#999] dark:text-white/40 mt-2">Format: JPG, PNG, WEBP. Kosongkan jika tidak ingin mengubah banner.</p>
                            {errors.banner_image && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.banner_image}</p>}
                        </div>

                        <div>
                            <label className="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" checked={data.is_active} onChange={handleChange} className="sr-only peer" />
                                <div className="w-11 h-6 bg-[#e5e5e5] dark:bg-[#1a1a2e] peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#7c3aed]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#7c3aed]"></div>
                                <span className="ml-3 text-sm font-medium text-[#666] dark:text-white/60">Aktifkan promo ini</span>
                            </label>
                        </div>

                        <div className="pt-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-end">
                            <button type="submit" disabled={processing} className="bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition disabled:opacity-50">
                                <i className="fa-solid fa-save mr-2"></i>{processing ? 'Menyimpan...' : 'Perbarui Promo'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}
