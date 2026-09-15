import DashboardLayout from '../../Layouts/DashboardLayout';
import { useForm, Link } from '@inertiajs/react';

export default function AnnouncementEdit({ announcement }) {
    const toLocalDatetime = (dateStr) => {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        const pad = (n) => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    };

    const { data, setData, put, processing, errors } = useForm({
        title: announcement.title || '',
        content: announcement.content || '',
        type: announcement.type || 'info',
        audience: announcement.audience || 'all',
        starts_at: toLocalDatetime(announcement.starts_at),
        expires_at: toLocalDatetime(announcement.expires_at),
        is_active: announcement.is_active || false,
        is_pinned: announcement.is_pinned || false,
    });

    function handleChange(e) {
        const { name, value, type, checked } = e.target;
        if (type === 'checkbox') {
            setData(name, checked);
        } else {
            setData(name, value);
        }
    }

    function handleSubmit(e) {
        e.preventDefault();
        put(route('superadmin.announcements.update', { hashid: announcement.hashid }));
    }

    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";

    return (
        <DashboardLayout title="Edit Informasi">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-pen-to-square text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Edit Informasi</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Perbarui informasi atau pengumuman.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href={route('superadmin.announcements.index')} className="bg-white dark:bg-[#0d0d18]/60 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:bg-[#fafafa] dark:hover:bg-white/5 px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-sm p-6 mt-6">
                <form onSubmit={handleSubmit}>
                    <div className="space-y-6">
                        <div>
                            <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Judul Informasi <span className="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" value={data.title} onChange={handleChange} required className={inputCls} />
                            {errors.title && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.title}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Isi Informasi</label>
                            <textarea value={data.content} onChange={handleChange} rows="5" className={inputCls} />
                            {errors.content && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.content}</p>}
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Tipe Informasi <span className="text-red-500 dark:text-red-400">*</span></label>
                                <select name="type" value={data.type} onChange={handleChange} className={inputCls}>
                                    <option value="info">Info</option>
                                    <option value="update">Update</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="warning">Warning</option>
                                </select>
                                {errors.type && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.type}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Target Audience <span className="text-red-500 dark:text-red-400">*</span></label>
                                <select name="audience" value={data.audience} onChange={handleChange} className={inputCls}>
                                    <option value="all">Semua Pengguna</option>
                                    <option value="hosting">Hosting Saja</option>
                                    <option value="joki">Joki Saja</option>
                                </select>
                                {errors.audience && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.audience}</p>}
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Mulai Ditampilkan</label>
                                <input type="datetime-local" name="starts_at" value={data.starts_at} onChange={handleChange} className={inputCls} />
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">Kosongkan jika ingin langsung ditampilkan.</p>
                                {errors.starts_at && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.starts_at}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Berakhir Ditampilkan</label>
                                <input type="datetime-local" name="expires_at" value={data.expires_at} onChange={handleChange} className={inputCls} />
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">Kosongkan jika tidak ada batas waktu.</p>
                                {errors.expires_at && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.expires_at}</p>}
                            </div>
                        </div>

                        <div className="flex flex-col sm:flex-row gap-4">
                            <label className="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" checked={data.is_active} onChange={handleChange} className="sr-only peer" />
                                <div className="w-11 h-6 bg-[#e5e5e5] dark:bg-[#1a1a2e] peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#7c3aed]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#7c3aed]"></div>
                                <span className="ml-3 text-sm font-medium text-[#666] dark:text-white/60">Aktif</span>
                            </label>

                            <label className="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_pinned" checked={data.is_pinned} onChange={handleChange} className="sr-only peer" />
                                <div className="w-11 h-6 bg-[#e5e5e5] dark:bg-[#1a1a2e] peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                <span className="ml-3 text-sm font-medium text-[#666] dark:text-white/60">Sematkan (Pinned)</span>
                            </label>
                        </div>

                        <div className="pt-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-end">
                            <button type="submit" disabled={processing} className="bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition disabled:opacity-50">
                                <i className="fa-solid fa-save mr-2"></i>{processing ? 'Menyimpan...' : 'Perbarui Informasi'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}
