import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, Link } from '@inertiajs/react';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        subject: '',
        department: '',
        priority: 'medium',
        message: '',
    });

    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/user/hosting/tickets');
    };

    return (
        <DashboardLayout title="Buat Tiket Baru">
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-plus text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Buat Tiket Baru</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kirimkan pertanyaan atau masalah Anda kepada tim support kami.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href="/user/hosting/tickets" className="inline-flex justify-center items-center bg-[#fafafa] dark:bg-white/5 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:bg-[#f5f0ff] dark:hover:bg-white/10 text-[#666] dark:text-white/60 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="mt-6">
                <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                    <form onSubmit={handleSubmit} className="p-8 space-y-6">
                        <div>
                            <label className="block text-sm font-bold text-[#333] dark:text-white mb-2">Subjek</label>
                            <input
                                type="text"
                                required
                                value={data.subject}
                                onChange={(e) => setData('subject', e.target.value)}
                                placeholder="Ringkas masalah Anda..."
                                className={inputCls}
                            />
                            {errors.subject && <p className="text-xs text-rose-500 mt-1">{errors.subject}</p>}
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-bold text-[#333] dark:text-white mb-2">Departemen</label>
                                <select value={data.department} onChange={(e) => setData('department', e.target.value)} required className={inputCls}>
                                    <option value="" disabled>-- Pilih Departemen --</option>
                                    <option value="Hosting">Hosting</option>
                                    <option value="Billing">Billing</option>
                                    <option value="Teknis">Teknis</option>
                                    <option value="Joki">Joki</option>
                                </select>
                                {errors.department && <p className="text-xs text-rose-500 mt-1">{errors.department}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-bold text-[#333] dark:text-white mb-2">Prioritas</label>
                                <select value={data.priority} onChange={(e) => setData('priority', e.target.value)} className={inputCls}>
                                    <option value="low">Rendah</option>
                                    <option value="medium">Sedang</option>
                                    <option value="high">Tinggi</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-bold text-[#333] dark:text-white mb-2">Pesan</label>
                            <textarea
                                rows={6}
                                required
                                value={data.message}
                                onChange={(e) => setData('message', e.target.value)}
                                placeholder="Jelaskan masalah Anda secara detail..."
                                className={inputCls}
                            ></textarea>
                            {errors.message && <p className="text-xs text-rose-500 mt-1">{errors.message}</p>}
                        </div>

                        <div className="flex justify-end pt-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <button type="submit" disabled={processing} className="bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-sm font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm disabled:opacity-50">
                                {processing ? 'Mengirim...' : 'Kirim Tiket'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}
