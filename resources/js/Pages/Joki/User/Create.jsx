import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Create() {
    const { services } = usePage().props;
    const [form, setForm] = useState({
        service_id: '',
        project_name: '',
        description: '',
        deadline: '',
    });
    const [files, setFiles] = useState([]);

    const handleSubmit = (e) => {
        e.preventDefault();
        const data = new FormData();
        data.append('service_id', form.service_id);
        data.append('project_name', form.project_name);
        data.append('description', form.description);
        data.append('deadline', form.deadline);
        for (let i = 0; i < files.length; i++) {
            data.append(`files[${i}]`, files[i]);
        }
        router.form(data).post('/user/joki/create');
    };

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
    };

    return (
        <DashboardLayout title="Buat Pesanan">
            <div className="max-w-2xl mx-auto space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Pesan Layanan Joki</h2>
                        <p className="text-[12px] text-[#999] dark:text-white/40 mt-1">Pilih layanan dan isi detail pesanan Anda.</p>
                    </div>

                    <form onSubmit={handleSubmit} className="p-5 space-y-5">
                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Layanan</label>
                            <select
                                value={form.service_id}
                                onChange={(e) => setForm({ ...form, service_id: e.target.value })}
                                required
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                            >
                                <option value="">Pilih layanan</option>
                                {services?.map((s) => (
                                    <option key={s.id} value={s.id}>{s.name} - {formatCurrency(s.price)}</option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Nama Project</label>
                            <input
                                type="text"
                                value={form.project_name}
                                onChange={(e) => setForm({ ...form, project_name: e.target.value })}
                                placeholder="Contoh: Website Company Profile"
                                required
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                            />
                        </div>

                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Deskripsi</label>
                            <textarea
                                value={form.description}
                                onChange={(e) => setForm({ ...form, description: e.target.value })}
                                placeholder="Jelaskan detail pesanan Anda..."
                                rows={5}
                                required
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors resize-none"
                            />
                        </div>

                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Deadline</label>
                            <input
                                type="date"
                                value={form.deadline}
                                onChange={(e) => setForm({ ...form, deadline: e.target.value })}
                                required
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                            />
                        </div>

                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Upload Files (Opsional)</label>
                            <input
                                type="file"
                                multiple
                                onChange={(e) => setFiles(e.target.files)}
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors file:mr-3 file:py-1 file:px-3 file:text-[12px] file:font-medium file:border-0 file:bg-[#f5f0ff] file:text-[#7c3aed] dark:file:bg-[#7c3aed]/20 dark:file:text-[#a78bfa]"
                            />
                            <p className="text-[11px] text-[#999] dark:text-white/30 mt-1">Upload file pendukung jika diperlukan.</p>
                        </div>

                        <div className="flex items-center gap-3 pt-2">
                            <button
                                type="submit"
                                disabled={!form.service_id || !form.project_name || !form.description || !form.deadline}
                                className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                            >
                                <i className="fa-solid fa-paper-plane mr-1.5"></i>Kirim Pesanan
                            </button>
                            <Link href="/user/joki/progress" className="px-5 py-2 text-[13px] font-medium text-[#666] dark:text-white/60 hover:text-[#7c3aed] transition-colors">
                                Batal
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}
