import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, Link } from '@inertiajs/react';
import { useState } from 'react';

function formatRupiah(num) {
    return Number(num || 0).toLocaleString('id-ID');
}

export default function Create({ services }) {
    const { data, setData, post, processing, errors } = useForm({
        service_id: '',
        deadline: '',
        project_name: '',
        tech_stack: '',
        description: '',
    });

    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";

    const [priceEstimate, setPriceEstimate] = useState(null);

    const handleServiceChange = (e) => {
        const serviceId = e.target.value;
        setData('service_id', serviceId);
        const selected = services?.find(s => String(s.id) === String(serviceId));
        setPriceEstimate(selected ? selected.base_price : null);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/user/joki/orders');
    };

    return (
        <DashboardLayout title="Buat Pesanan Joki">
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-plus text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Buat Pesanan Joki</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Isi detail proyek Anda, saya akan segera meninjau untuk menentukan estimasi biaya dan waktu pengerjaan.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href="/user/joki/dashboard" className="inline-flex justify-center items-center bg-[#fafafa] dark:bg-white/5 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:bg-[#f5f0ff] dark:hover:bg-white/10 text-[#666] dark:text-white/60 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="mt-6">
                <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                    <form onSubmit={handleSubmit} className="p-8 space-y-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-bold text-[#333] dark:text-white mb-2">Pilih Layanan</label>
                                <select value={data.service_id} onChange={handleServiceChange} required className={inputCls}>
                                    <option value="" disabled>-- Pilih Jenis Layanan --</option>
                                    {services?.filter(s => s.is_active).map((service) => (
                                        <option key={service.id} value={service.id}>{service.name}</option>
                                    ))}
                                </select>
                                {priceEstimate !== null && (
                                    <p className="mt-2 text-sm font-semibold text-[#7c3aed] dark:text-[#a78bfa]">
                                        <i className="fa-solid fa-tag me-1"></i> Estimasi Harga Mulai: Rp {formatRupiah(priceEstimate)}
                                    </p>
                                )}
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">*Harga final ditentukan setelah kesepakatan.</p>
                            </div>
                            <div>
                                <label className="block text-sm font-bold text-[#333] dark:text-white mb-2">Deadline Pengerjaan</label>
                                <input type="date" required value={data.deadline} onChange={(e) => setData('deadline', e.target.value)} className={inputCls} />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-bold text-[#333] dark:text-white mb-2">Nama Proyek</label>
                                <input type="text" required placeholder="Contoh: Web Portofolio..." value={data.project_name} onChange={(e) => setData('project_name', e.target.value)} className={inputCls} />
                            </div>
                            <div>
                                <label className="block text-sm font-bold text-[#333] dark:text-white mb-2">Tech Stack (Opsional)</label>
                                <input type="text" placeholder="Contoh: Laravel, React, Vue..." value={data.tech_stack} onChange={(e) => setData('tech_stack', e.target.value)} className={inputCls} />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-bold text-[#333] dark:text-white mb-2">Deskripsi Kebutuhan Detail</label>
                            <textarea rows="5" required placeholder="Jelaskan secara rinci fitur apa saja yang diinginkan, jumlah halaman, dsb..." value={data.description} onChange={(e) => setData('description', e.target.value)} className={inputCls}></textarea>
                        </div>

                        <div className="flex justify-end pt-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <button type="submit" disabled={processing} className="bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-sm font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                                Kirim Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}
