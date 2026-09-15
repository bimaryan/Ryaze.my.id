import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, usePage, router, Link } from '@inertiajs/react';
import { useState } from 'react';

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

export default function EditOrder({ order, consultation }) {
    const { errors } = usePage().props;
    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";

    const statusForm = useForm({
        status: order.status || 'pending',
        progress: order.progress || 0,
        price: order.price || '',
        preview_url: order.preview_url || '',
        repo_link: order.repo_link || '',
        demo_link: order.demo_link || '',
    });

    const milestoneForm = useForm({
        title: '',
        due_date: '',
        description: '',
        status: 'pending',
    });

    const [replyForms, setReplyForms] = useState({});

    const handleReply = (e, revHashid) => {
        e.preventDefault();
        const formData = replyForms[revHashid] || { admin_reply: '', status: 'pending' };
        router.put(`/admin/joki/revisions/${revHashid}/reply`, formData, { preserveState: true });
    };

    const updateReplyField = (revHashid, field, value) => {
        setReplyForms(prev => ({
            ...prev,
            [revHashid]: { ...(prev[revHashid] || { admin_reply: '', status: 'pending' }), [field]: value },
        }));
    };

    const paymentForm = useForm({ payment_name: '', amount: '' });

    const [verifyForms, setVerifyForms] = useState({});

    const handleVerify = (e, paymentHashid) => {
        e.preventDefault();
        const formData = verifyForms[paymentHashid] || { status: 'paid' };
        router.put(`/admin/joki/payments/${paymentHashid}/verify`, formData, { preserveState: true });
    };

    const updateVerifyField = (paymentHashid, value) => {
        setVerifyForms(prev => ({ ...prev, [paymentHashid]: { status: value } }));
    };

    return (
        <DashboardLayout title={`Control Center: ${order.order_number}`}>
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-pen-to-square text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Control Center: {order.order_number}</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">
                            Klien: <span className="font-semibold text-[#7c3aed] dark:text-[#a78bfa]">{order.client?.name}</span> | Layanan: {order.service?.name}
                        </p>
                    </div>
                    <div className="ml-auto flex items-center gap-2">
                        {order.status === 'completed' && (
                            <form onSubmit={(e) => { e.preventDefault(); router.post(`/admin/joki/orders/${order.hashid}/portfolio`); }}>
                                <button type="submit" className="inline-flex items-center bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                                    <i className="fa-solid fa-star me-2"></i> Jadikan Portofolio
                                </button>
                            </form>
                        )}
                        <Link href="/admin/joki/orders" className="inline-flex items-center bg-[#fafafa] dark:bg-white/5 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:bg-[#f5f0ff] dark:hover:bg-white/10 text-[#666] dark:text-white/60 px-4 py-2 rounded-lg text-sm font-medium transition">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            {errors && Object.keys(errors).length > 0 && (
                <div className="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/40 text-rose-700 dark:text-rose-300">
                    <div className="flex items-start gap-3">
                        <i className="fa-solid fa-circle-exclamation mt-1"></i>
                        <div>
                            <h3 className="font-bold text-sm">Gagal memperbarui pesanan:</h3>
                            <ul className="list-disc list-inside text-sm mt-1">
                                {Object.values(errors).map((err, i) => (
                                    <li key={i}>{err}</li>
                                ))}
                            </ul>
                        </div>
                    </div>
                </div>
            )}

            <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div className="xl:col-span-2 space-y-6">
                    {/* Card 1: Update Status */}
                    <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">Update Status & Hasil Kerja</h3>
                        <form onSubmit={(e) => { e.preventDefault(); statusForm.put(`/admin/joki/orders/${order.hashid}`); }} className="space-y-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-bold text-[#666] dark:text-white/60 mb-1">Status Proyek</label>
                                    <select value={statusForm.data.status} onChange={(e) => statusForm.setData('status', e.target.value)} className={inputCls}>
                                        <option value="pending">Pending</option>
                                        <option value="progress">In Progress (Coding)</option>
                                        <option value="review">Review Klien</option>
                                        <option value="completed">Completed (Selesai)</option>
                                        <option value="canceled">Canceled (Batal)</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#666] dark:text-white/60 mb-1">Progres (%)</label>
                                    <input type="number" min="0" max="100" value={statusForm.data.progress} onChange={(e) => statusForm.setData('progress', e.target.value)} className={inputCls} />
                                </div>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                                <div>
                                    <label className="block text-xs font-bold text-[#666] dark:text-white/60 mb-1">Harga Deal (Rp)</label>
                                    <input type="number" value={statusForm.data.price} onChange={(e) => statusForm.setData('price', e.target.value)} className={inputCls} />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#666] dark:text-white/60 mb-1">Live Preview URL</label>
                                    <input type="url" value={statusForm.data.preview_url} onChange={(e) => statusForm.setData('preview_url', e.target.value)} placeholder="https://staging.ryaze.my.id" className={inputCls} />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#666] dark:text-white/60 mb-1">Link Repo / GitHub</label>
                                    <input type="url" value={statusForm.data.repo_link} onChange={(e) => statusForm.setData('repo_link', e.target.value)} className={inputCls} />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#666] dark:text-white/60 mb-1">Link Demo Akhir</label>
                                    <input type="url" value={statusForm.data.demo_link} onChange={(e) => statusForm.setData('demo_link', e.target.value)} className={inputCls} />
                                </div>
                            </div>
                            <div className="text-right pt-2">
                                <button type="submit" disabled={statusForm.processing} className="bg-[#7c3aed] text-white font-bold py-2 px-6 rounded-lg hover:bg-[#6d28d9] transition-colors text-sm">Simpan Perubahan Utama</button>
                            </div>
                        </form>
                    </div>

                    {/* Card 2: Milestones */}
                    <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">Target Pengerjaan (Milestones)</h3>

                        <form onSubmit={(e) => { e.preventDefault(); milestoneForm.post(`/admin/joki/orders/${order.hashid}/milestones`, { onSuccess: () => milestoneForm.reset() }); }}
                            className="mb-6 bg-[#fafafa] dark:bg-white/[0.02] p-4 rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                                <div className="md:col-span-2">
                                    <input type="text" required placeholder="Judul Tugas (Cth: Slicing UI)" value={milestoneForm.data.title} onChange={(e) => milestoneForm.setData('title', e.target.value)} className={inputCls} />
                                </div>
                                <div>
                                    <input type="date" value={milestoneForm.data.due_date} onChange={(e) => milestoneForm.setData('due_date', e.target.value)} className={inputCls} />
                                </div>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div className="md:col-span-2">
                                    <input type="text" placeholder="Deskripsi singkat..." value={milestoneForm.data.description} onChange={(e) => milestoneForm.setData('description', e.target.value)} className={inputCls} />
                                </div>
                                <div className="flex gap-2">
                                    <select value={milestoneForm.data.status} onChange={(e) => milestoneForm.setData('status', e.target.value)} className={inputCls}>
                                        <option value="pending">Pending</option>
                                        <option value="working">Working</option>
                                        <option value="done">Done</option>
                                    </select>
                                    <button type="submit" disabled={milestoneForm.processing} className="bg-emerald-500 text-white px-4 rounded-lg hover:bg-emerald-600 font-bold"><i className="fa-solid fa-plus"></i></button>
                                </div>
                            </div>
                        </form>

                        <div className="space-y-3">
                            {order.milestones?.length > 0 ? order.milestones.map((m, idx) => (
                                <div key={idx} className={`flex justify-between items-center p-3 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg ${m.status === 'done' ? 'bg-emerald-50 dark:bg-emerald-500/10' : 'bg-white dark:bg-[#0d0d18]'}`}>
                                    <div>
                                        <h4 className="font-bold text-sm text-[#333] dark:text-white">{m.title}</h4>
                                        <p className="text-xs text-[#999] dark:text-white/40">{m.description}</p>
                                    </div>
                                    <div className="text-right">
                                        <span className={`text-xs font-bold px-2 py-1 rounded ${m.status === 'done' ? 'bg-emerald-200 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-200' : m.status === 'working' ? 'bg-blue-200 dark:bg-blue-500/20 text-blue-800 dark:text-blue-200' : 'bg-[#e5e5e5] dark:bg-white/10 text-[#333] dark:text-white'}`}>
                                            {m.status?.toUpperCase()}
                                        </span>
                                    </div>
                                </div>
                            )) : (
                                <p className="text-xs text-[#999] dark:text-white/40 text-center py-2">Belum ada milestone.</p>
                            )}
                        </div>
                    </div>

                    {/* Card 3: Revisions */}
                    <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="flex justify-between items-center mb-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-3">
                            <h3 className="font-bold text-[#333] dark:text-white flex items-center gap-2">
                                <i className="fa-solid fa-comments text-[#7c3aed] dark:text-[#a78bfa]"></i>
                                Permintaan Revisi Klien
                            </h3>
                            <span className="bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa] text-xs font-bold px-2.5 py-1 rounded-full">
                                {order.revisions?.length || 0} Permintaan
                            </span>
                        </div>

                        <div className="space-y-6">
                            {order.revisions?.length > 0 ? order.revisions.map((rev, idx) => (
                                <div key={idx} className={`relative pl-4 border-l-2 ${rev.status === 'resolved' ? 'border-emerald-500' : rev.status === 'fixing' ? 'border-blue-500' : rev.status === 'rejected' ? 'border-rose-500' : 'border-amber-400'}`}>
                                    <div className={`absolute -left-[9px] top-0 w-4 h-4 rounded-full border-4 border-white shadow-sm ${rev.status === 'resolved' ? 'bg-emerald-500' : rev.status === 'fixing' ? 'bg-blue-500' : rev.status === 'rejected' ? 'bg-rose-500' : 'bg-amber-400'}`}></div>
                                    
                                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-sm overflow-hidden">
                                        <div className={`p-4 ${rev.status === 'pending' ? 'bg-amber-50/30 dark:bg-amber-500/10' : 'bg-[#fafafa] dark:bg-white/[0.02]'} border-b border-[#e5e5e5] dark:border-[#1a1a2e]`}>
                                            <div className="flex justify-between items-start gap-4">
                                                <div className="flex items-start gap-3">
                                                    <div className="w-8 h-8 rounded-full bg-[#e5e5e5] dark:bg-white/10 text-[#666] dark:text-white/60 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                        <i className="fa-solid fa-user text-xs"></i>
                                                    </div>
                                                    <div>
                                                        <p className="text-[11px] font-bold text-[#999] dark:text-white/40 mb-1 uppercase tracking-wider">
                                                            {order.client?.name} &bull; {formatDate(rev.created_at)}
                                                        </p>
                                                        <div className="text-sm text-[#333] dark:text-white leading-relaxed font-medium">
                                                            "{rev.revision_note}"
                                                        </div>
                                                    </div>
                                                </div>
                                                <span className={`text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider flex-shrink-0 ${rev.status === 'resolved' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : rev.status === 'fixing' ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300' : rev.status === 'rejected' ? 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300' : 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300'}`}>
                                                    {rev.status}
                                                </span>
                                            </div>
                                        </div>

                                        <div className="p-4 bg-white dark:bg-[#0d0d18]">
                                            {rev.admin_reply && (
                                                <div className="mb-4 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 rounded-lg p-3 border border-[#ede9fe] dark:border-[#7c3aed]/30">
                                                    <div className="flex items-start gap-3">
                                                        <div className="w-7 h-7 rounded-full bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa] flex items-center justify-center flex-shrink-0 mt-0.5">
                                                            <i className="fa-solid fa-headset text-[10px]"></i>
                                                        </div>
                                                        <div>
                                                            <p className="text-[11px] font-bold text-[#7c3aed] dark:text-[#a78bfa] mb-0.5 uppercase tracking-wider">Admin Reply</p>
                                                            <p className="text-sm text-[#333] dark:text-white">{rev.admin_reply}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            )}

                                            <form onSubmit={(e) => handleReply(e, rev.hashid)}>
                                                <label className="block text-xs font-bold text-[#666] dark:text-white/60 mb-2">Update Status & Tanggapan</label>
                                                <div className="flex flex-col sm:flex-row gap-3">
                                                    <div className="relative flex-1">
                                                        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#999] dark:text-white/40">
                                                            <i className="fa-solid fa-pen text-xs"></i>
                                                        </div>
                                                        <input type="text" required placeholder="Ketik tanggapan Anda di sini..."
                                                            value={replyForms[rev.hashid]?.admin_reply ?? rev.admin_reply ?? ''}
                                                            onChange={(e) => updateReplyField(rev.hashid, 'admin_reply', e.target.value)}
                                                            className="w-full pl-9 pr-3 py-2.5 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg text-sm focus:ring-2 focus:ring-[#7c3aed] outline-none transition-all" />
                                                    </div>
                                                    <select value={replyForms[rev.hashid]?.status ?? rev.status} onChange={(e) => updateReplyField(rev.hashid, 'status', e.target.value)}
                                                        className="sm:w-40 flex-shrink-0 font-medium text-[#333] dark:text-white w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition">
                                                        <option value="pending">Pending</option>
                                                        <option value="fixing">Fixing</option>
                                                        <option value="resolved">Resolved</option>
                                                        <option value="rejected">Rejected</option>
                                                    </select>
                                                    <button type="submit" className="w-full sm:w-auto flex-shrink-0 bg-[#7c3aed] text-white px-5 py-2.5 rounded-lg hover:bg-[#6d28d9] hover:shadow-md text-sm font-bold transition-all flex items-center justify-center gap-2">
                                                        Simpan <i className="fa-solid fa-paper-plane text-xs"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            )) : (
                                <div className="text-center py-8">
                                    <div className="w-16 h-16 bg-[#fafafa] dark:bg-white/5 text-[#999] dark:text-white/30 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i className="fa-solid fa-clipboard-check text-2xl"></i>
                                    </div>
                                    <h4 className="text-sm font-bold text-[#333] dark:text-white mb-1">Belum ada revisi</h4>
                                    <p className="text-xs font-medium text-[#999] dark:text-white/40">Klien belum mengajukan permintaan revisi untuk proyek ini.</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Card 4: Live Chat */}
                    {order.status !== 'pending' && (
                        <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2 flex items-center gap-2">
                                <i className="fa-solid fa-comments text-[#7c3aed] dark:text-[#a78bfa]"></i> Live Chat
                            </h3>
                            <p className="text-sm text-[#999] dark:text-white/40 text-center py-8">
                                <i className="fa-solid fa-comments text-2xl mb-2 block"></i>
                                Komponen chat akan dimuat di sini.
                            </p>
                        </div>
                    )}
                </div>

                <div className="space-y-6">
                    {/* Sidebar Card 5: Project Requirements */}
                    <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="font-bold text-[#333] dark:text-white mb-3 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">Kebutuhan Proyek</h3>
                        <div className="text-sm text-[#666] dark:text-white/60 mb-3">
                            <span className="block font-bold text-[#333] dark:text-white">Nama:</span> {order.project_name}
                        </div>
                        <div className="text-sm text-[#666] dark:text-white/60 mb-3">
                            <span className="block font-bold text-[#333] dark:text-white">Tech Stack:</span> {order.tech_stack || '-'}
                        </div>
                        <div className="text-sm text-[#666] dark:text-white/60">
                            <span className="block font-bold text-[#333] dark:text-white mb-1">Deskripsi:</span>
                            <div className="bg-[#fafafa] dark:bg-white/[0.02] p-3 rounded border border-[#e5e5e5] dark:border-[#1a1a2e] whitespace-pre-line">{order.description}</div>
                        </div>
                    </div>

                    {/* Sidebar Card 6: AI Consultation */}
                    {consultation?.chat_history && (
                        <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="font-bold text-[#333] dark:text-white mb-3 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2 flex items-center gap-2">
                                <i className="fa-solid fa-robot text-[#7c3aed]"></i> Riwayat Konsultasi AI
                            </h3>
                            <div className="space-y-3 max-h-80 overflow-y-auto pr-2">
                                {consultation.chat_history.map((msg, idx) => (
                                    <div key={idx} className={`${msg.role === 'user' ? 'bg-[#f5f0ff] dark:bg-[#7c3aed]/10 border-[#ede9fe] dark:border-[#7c3aed]/20 ml-4' : 'bg-[#fafafa] dark:bg-white/[0.02] border-[#e5e5e5] dark:border-[#1a1a2e] mr-4'} p-3 rounded-lg border text-sm`}>
                                        <div className={`font-bold mb-1 text-[11px] uppercase tracking-wider ${msg.role === 'user' ? 'text-[#7c3aed] dark:text-[#a78bfa]' : 'text-[#999] dark:text-white/40'}`}>
                                            {msg.role === 'user' ? order.client?.name : 'AI Konsultan'}
                                        </div>
                                        <div className="text-[#333] dark:text-white/80 leading-relaxed whitespace-pre-wrap">{msg.content}</div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Sidebar Card 7: Payments */}
                    <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">Tagihan & Pembayaran</h3>

                        <form onSubmit={(e) => { e.preventDefault(); paymentForm.post(`/admin/joki/orders/${order.hashid}/payments`, { onSuccess: () => paymentForm.reset() }); }}
                            className="mb-5 bg-[#fafafa] dark:bg-white/[0.02] p-3 rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <div className="space-y-2">
                                <input type="text" required placeholder="Nama (Cth: DP 50%)" value={paymentForm.data.payment_name} onChange={(e) => paymentForm.setData('payment_name', e.target.value)} className={inputCls} />
                                <input type="number" required placeholder="Nominal (Cth: 1500000)" value={paymentForm.data.amount} onChange={(e) => paymentForm.setData('amount', e.target.value)} className={inputCls} />
                                <button type="submit" disabled={paymentForm.processing} className="w-full bg-[#333] dark:bg-white/10 text-white font-bold py-2 rounded-lg hover:bg-[#444] dark:hover:bg-white/15 text-sm transition">Buat Tagihan</button>
                            </div>
                        </form>

                        <div className="space-y-4">
                            {order.payments?.length > 0 ? order.payments.map((payment, idx) => (
                                <div key={idx} className={`border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg p-4 ${payment.status === 'pending_verification' ? 'border-amber-400 bg-amber-50/30 dark:bg-amber-500/10' : ''}`}>
                                    <div className="flex justify-between items-start mb-2">
                                        <div>
                                            <p className="font-bold text-sm text-[#333] dark:text-white">{payment.payment_name}</p>
                                            <p className="text-xs font-medium text-emerald-600 dark:text-emerald-300">{formatRupiah(payment.amount)}</p>
                                        </div>
                                        <span className="text-[10px] font-bold px-2 py-1 rounded uppercase bg-[#e5e5e5] dark:bg-white/10 text-[#666] dark:text-white/60">
                                            {payment.status}
                                        </span>
                                    </div>

                                    {payment.proof_image && (
                                        <div className="mt-3 pt-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                                            <p className="text-xs font-bold text-[#333] dark:text-white mb-2">Bukti Transfer:</p>
                                            <a href={`/storage/${payment.proof_image}`} target="_blank" className="block">
                                                <img src={`/storage/${payment.proof_image}`} alt="Bukti TF" className="w-full h-24 object-cover rounded border border-[#e5e5e5] dark:border-[#1a1a2e] mb-3 hover:opacity-80 transition-opacity" />
                                            </a>

                                            {payment.status === 'pending_verification' && (
                                                <form onSubmit={(e) => handleVerify(e, payment.hashid)} className="flex gap-2">
                                                    <select value={verifyForms[payment.hashid]?.status || 'paid'} onChange={(e) => updateVerifyField(payment.hashid, e.target.value)}
                                                        className="flex-1 px-2 py-1 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition">
                                                        <option value="paid">Terima (Lunas)</option>
                                                        <option value="failed">Tolak (Gagal)</option>
                                                        <option value="unpaid">Reset ke Unpaid</option>
                                                    </select>
                                                    <button type="submit" className="bg-[#7c3aed] text-white px-3 py-1 rounded text-xs font-bold">Proses</button>
                                                </form>
                                            )}
                                        </div>
                                    )}
                                </div>
                            )) : (
                                <p className="text-xs text-[#999] dark:text-white/40 text-center py-2">Belum ada tagihan dibuat.</p>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
