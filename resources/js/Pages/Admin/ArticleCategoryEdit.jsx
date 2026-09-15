import DashboardLayout from '../../Layouts/DashboardLayout';
import { useForm, Link } from '@inertiajs/react';

export default function ArticleCategoryEdit({ category }) {
    const { data, setData, put, processing, errors } = useForm({
        name: category.name || '',
        description: category.description || '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        put(route('superadmin.article_categories.update', { hashid: category.hashid }));
    }

    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";

    return (
        <DashboardLayout title="Edit Kategori">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-folder-open text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Edit Kategori</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Perbarui kategori: {category.name}</p>
                    </div>
                    <div className="ml-auto">
                        <Link href={route('superadmin.article_categories.index')} className="bg-white dark:bg-[#0d0d18]/60 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:bg-[#fafafa] dark:hover:bg-white/5 px-4 py-2 rounded-lg font-medium transition text-sm shadow-sm">
                            Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="max-w-2xl mt-4">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-sm p-6">
                    <form onSubmit={handleSubmit}>
                        <div className="mb-5">
                            <label className="block mb-1.5 text-sm font-medium text-[#666] dark:text-white/60">Nama Kategori <span className="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" value={data.name} onChange={(e) => setData('name', e.target.value)} required className={inputCls} />
                            {errors.name && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.name}</p>}
                        </div>
                        <div className="mb-5">
                            <label className="block mb-1.5 text-sm font-medium text-[#666] dark:text-white/60">Deskripsi</label>
                            <textarea value={data.description} onChange={(e) => setData('description', e.target.value)} rows="3" className={inputCls} />
                            {errors.description && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.description}</p>}
                        </div>
                        <button type="submit" disabled={processing} className="bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-semibold text-sm py-3 px-6 rounded-lg transition shadow-sm disabled:opacity-50">
                            <i className="fa-solid fa-save mr-2"></i>{processing ? 'Menyimpan...' : 'Perbarui Kategori'}
                        </button>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}
