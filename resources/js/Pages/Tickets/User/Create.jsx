import { usePage, useForm, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        subject: '',
        priority: 'medium',
        message: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/user/tickets');
    };

    return (
        <DashboardLayout title="Buat Tiket">
            <div className="max-w-2xl mx-auto space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Buat Tiket Baru</h2>
                        <p className="text-[12px] text-[#999] dark:text-white/40 mt-1">Kirim permintaan bantuan atau pertanyaan kepada tim kami.</p>
                    </div>

                    <form onSubmit={handleSubmit} className="p-5 space-y-5">
                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Subjek</label>
                            <input
                                type="text"
                                value={data.subject}
                                onChange={(e) => setData('subject', e.target.value)}
                                placeholder="Permasalahan dengan project saya"
                                required
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                            />
                            {errors.subject && <p className="mt-1 text-[12px] text-red-500">{errors.subject}</p>}
                        </div>

                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Prioritas</label>
                            <select
                                value={data.priority}
                                onChange={(e) => setData('priority', e.target.value)}
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                            >
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            {errors.priority && <p className="mt-1 text-[12px] text-red-500">{errors.priority}</p>}
                        </div>

                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Pesan</label>
                            <textarea
                                value={data.message}
                                onChange={(e) => setData('message', e.target.value)}
                                placeholder="Jelaskan permasalahan Anda secara detail..."
                                rows={6}
                                required
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors resize-none"
                            />
                            {errors.message && <p className="mt-1 text-[12px] text-red-500">{errors.message}</p>}
                        </div>

                        <div className="flex items-center gap-3 pt-2">
                            <button
                                type="submit"
                                disabled={processing || !data.subject || !data.message}
                                className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                            >
                                {processing ? 'Mengirim...' : 'Kirim Tiket'}
                            </button>
                            <Link href="/user/tickets" className="px-5 py-2 text-[13px] font-medium text-[#666] dark:text-white/60 hover:text-[#7c3aed] transition-colors">
                                Batal
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}
