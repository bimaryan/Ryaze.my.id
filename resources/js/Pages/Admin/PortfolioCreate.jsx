import DashboardLayout from '../../Layouts/DashboardLayout';
import { useForm, Link } from '@inertiajs/react';
import { useState } from 'react';

export default function PortfolioCreate() {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        tags: '',
        link_preview: '',
        link_github: '',
        link_journal: '',
        image: null,
        certificate: null,
        link_copyright: '',
        is_active: true,
    });

    const [imagePreview, setImagePreview] = useState(null);

    function handleChange(e) {
        const { name, value, type, checked, files } = e.target;
        if (type === 'file') {
            setData(name, files[0]);
            if (name === 'image' && files[0]) {
                setImagePreview(URL.createObjectURL(files[0]));
            }
        } else if (type === 'checkbox') {
            setData(name, checked);
        } else {
            setData(name, value);
        }
    }

    function handleSubmit(e) {
        e.preventDefault();
        post(route('superadmin.portfolios.store'), { forceFormData: true });
    }

    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";

    return (
        <DashboardLayout title="Tambah Portofolio">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-plus text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Tambah Portofolio</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Tambahkan data portofolio/mahakarya baru.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href={route('superadmin.portfolios.index')} className="bg-white dark:bg-[#0d0d18]/60 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:bg-[#fafafa] dark:hover:bg-white/5 px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-sm p-6 mt-6">
                <form onSubmit={handleSubmit} encType="multipart/form-data">
                    <div className="space-y-6">
                        <div>
                            <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Judul Portofolio <span className="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" value={data.title} onChange={handleChange} required className={inputCls} />
                            {errors.title && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.title}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Deskripsi <span className="text-red-500 dark:text-red-400">*</span></label>
                            <textarea value={data.description} onChange={handleChange} rows="5" required className={inputCls} />
                            {errors.description && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.description}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Tags / Teknologi (Pisahkan dengan koma)</label>
                            <input type="text" value={data.tags} onChange={handleChange} className={inputCls} placeholder="Contoh: Laravel, Tailwind, React" />
                            {errors.tags && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.tags}</p>}
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Link Live Preview</label>
                                <input type="url" name="link_preview" value={data.link_preview} onChange={handleChange} className={inputCls} placeholder="https://..." />
                                {errors.link_preview && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.link_preview}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Link GitHub / Repo</label>
                                <input type="url" name="link_github" value={data.link_github} onChange={handleChange} className={inputCls} placeholder="https://github.com/..." />
                                {errors.link_github && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.link_github}</p>}
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">
                                <i className="fa-solid fa-book-open mr-1 text-[#7c3aed] dark:text-[#a78bfa]"></i> Link Jurnal
                            </label>
                            <input type="url" name="link_journal" value={data.link_journal} onChange={handleChange} className={inputCls} placeholder="https://journal.example.com/..." />
                            <p className="text-xs text-[#999] dark:text-white/40 mt-1">Opsional. Isi jika portofolio ini dipublikasikan di jurnal ilmiah.</p>
                            {errors.link_journal && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.link_journal}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">Gambar / Thumbnail</label>
                            <input type="file" name="image" accept="image/*" onChange={handleChange} className="block w-full text-sm text-[#999] dark:text-white/40 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#f5f0ff] file:text-[#7c3aed] hover:file:bg-[#ede9fe] dark:file:bg-[#7c3aed]/10 dark:file:text-[#a78bfa] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg" />
                            <p className="text-xs text-[#999] dark:text-white/40 mt-2">Format: JPG, PNG, WEBP (Max 2MB). Kosongkan jika tidak ada.</p>
                            {errors.image && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.image}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">
                                <i className="fa-solid fa-certificate mr-1 text-amber-500 dark:text-amber-400"></i> Sertifikat Hak Cipta
                            </label>
                            <input type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png" onChange={handleChange} className="block w-full text-sm text-[#999] dark:text-white/40 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-amber-500/10 dark:file:text-amber-400 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg" />
                            <p className="text-xs text-[#999] dark:text-white/40 mt-2">Format: PDF, JPG, PNG (Max 5MB). Kosongkan jika tidak ada.</p>
                            {errors.certificate && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.certificate}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-[#666] dark:text-white/60 mb-2">
                                <i className="fa-solid fa-shield-halved mr-1 text-amber-500 dark:text-amber-400"></i> Link Hak Cipta
                            </label>
                            <input type="url" name="link_copyright" value={data.link_copyright} onChange={handleChange} className={inputCls} placeholder="https://pdki-indonesia.dgip.go.id/..." />
                            <p className="text-xs text-[#999] dark:text-white/40 mt-1">Opsional. Link ke halaman resmi pendaftaran hak cipta (DJKI, dll).</p>
                            {errors.link_copyright && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.link_copyright}</p>}
                        </div>

                        <div>
                            <label className="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" checked={data.is_active} onChange={handleChange} className="sr-only peer" />
                                <div className="w-11 h-6 bg-[#e5e5e5] dark:bg-[#1a1a2e] peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#7c3aed]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#7c3aed]"></div>
                                <span className="ml-3 text-sm font-medium text-[#666] dark:text-white/60">Publikasikan secara langsung (Aktif)</span>
                            </label>
                        </div>

                        <div className="pt-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-end">
                            <button type="submit" disabled={processing} className="bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition disabled:opacity-50">
                                <i className="fa-solid fa-save mr-2"></i>{processing ? 'Menyimpan...' : 'Simpan Portofolio'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}
