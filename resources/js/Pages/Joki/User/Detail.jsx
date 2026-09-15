import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, router, Link } from '@inertiajs/react';
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

export default function Detail({ order }) {
    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";

    const revisionForm = useForm({ revision_note: '' });
    const reviewForm = useForm({ rating: '', review: '' });

    const [showPaymentModal, setShowPaymentModal] = useState(false);
    const [paymentData, setPaymentData] = useState({ amount: 0, invoice: '' });

    const openPaymentModal = (amount, invoice) => {
        setPaymentData({ amount, invoice });
        setShowPaymentModal(true);
    };

    const handleRevisionSubmit = (e) => {
        e.preventDefault();
        revisionForm.post(`/user/joki/orders/${order.hashid}/revision`, {
            onSuccess: () => revisionForm.reset(),
        });
    };

    const handleReviewSubmit = (e) => {
        e.preventDefault();
        reviewForm.post(`/user/joki/orders/${order.hashid}/review`, {
            onSuccess: () => reviewForm.reset(),
        });
    };

    const pakasirSlug = typeof window !== 'undefined' && window.pakasirSlug ? window.pakasirSlug : 'ryaze';
    const adminWa = typeof window !== 'undefined' && window.adminWa ? window.adminWa : '';
    const paymentDana = typeof window !== 'undefined' && window.paymentDana ? window.paymentDana : '085157433395';

    return (
        <DashboardLayout title={`Detail Proyek: ${order.project_name}`}>
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-file-invoice text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Detail Proyek: {order.project_name}</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Pantau progres, tagihan, dan ajukan revisi di halaman ini.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href="/user/joki/progress" className="inline-flex justify-center items-center bg-[#fafafa] dark:bg-white/5 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:bg-[#f5f0ff] dark:hover:bg-white/10 text-[#666] dark:text-white/60 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Left Column */}
                <div className="lg:col-span-2 space-y-6">
                    {/* Progress */}
                    <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="font-bold text-[#333] dark:text-white mb-4">Progres Keseluruhan</h3>
                        <div className="w-full bg-[#e5e5e5] dark:bg-white/10 rounded-full h-4 mb-3 overflow-hidden">
                            <div className="bg-[#7c3aed] h-4 rounded-full transition-all duration-500" style={{ width: `${order.progress}%` }}></div>
                        </div>
                        <p className="text-sm text-[#666] dark:text-white/60">Saat ini pengerjaan mencapai <strong className="text-[#7c3aed] dark:text-[#a78bfa] text-base">{order.progress}%</strong></p>
                    </div>

                    {/* Milestones */}
                    <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">Target Pengerjaan (Milestone)</h3>
                        {order.milestones?.length > 0 ? (
                            <div className="space-y-4 relative border-l-2 border-[#e5e5e5] dark:border-[#1a1a2e] ml-3 pl-5">
                                {order.milestones.map((milestone, idx) => (
                                    <div key={idx} className="relative">
                                        <div className={`absolute -left-[29px] top-1 w-4 h-4 rounded-full border-2 border-white ${milestone.status === 'done' ? 'bg-emerald-500' : milestone.status === 'working' ? 'bg-blue-500' : 'bg-[#ccc] dark:bg-white/20'}`}></div>
                                        <h4 className="font-bold text-[#333] dark:text-white text-sm">{milestone.title}</h4>
                                        <p className="text-xs text-[#999] dark:text-white/40 mt-1 mb-1">{milestone.description}</p>
                                        <div className="flex gap-3 text-[11px] font-semibold mt-2">
                                            <span className={`${milestone.status === 'done' ? 'text-emerald-600 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/10' : milestone.status === 'working' ? 'text-blue-600 dark:text-blue-300 bg-blue-50 dark:bg-blue-500/10' : 'text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/5'} px-2 py-1 rounded`}>
                                                Status: {milestone.status?.toUpperCase()}
                                            </span>
                                            {milestone.due_date && (
                                                <span className="text-rose-600 dark:text-rose-300 bg-rose-50 dark:bg-rose-500/10 px-2 py-1 rounded">
                                                    <i className="fa-regular fa-calendar mr-1"></i> Target: {formatDate(milestone.due_date)}
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <p className="text-sm text-[#999] dark:text-white/40 italic text-center py-4">Belum ada milestone yang ditambahkan oleh Admin.</p>
                        )}
                    </div>

                    {/* Revision Form */}
                    {(order.status === 'review' || order.status === 'progress') && (
                        <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">Ajukan Revisi</h3>
                            <form onSubmit={handleRevisionSubmit}>
                                <textarea rows="3" required placeholder="Jelaskan bagian mana yang perlu diperbaiki..." value={revisionForm.data.revision_note} onChange={(e) => revisionForm.setData('revision_note', e.target.value)} className="mb-3 w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition"></textarea>
                                <button type="submit" disabled={revisionForm.processing} className="bg-rose-500 hover:bg-rose-600 text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors w-full sm:w-auto">
                                    Kirim Permintaan Revisi
                                </button>
                            </form>

                            {order.revisions?.length > 0 && (
                                <div className="mt-6 space-y-3">
                                    <h4 className="text-xs font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Riwayat Revisi</h4>
                                    {order.revisions.map((rev, idx) => (
                                        <div key={idx} className="bg-[#fafafa] dark:bg-white/[0.02] p-4 rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e] text-sm">
                                            <div className="flex justify-between items-start mb-2">
                                                <span className="font-semibold text-[#333] dark:text-white">Catatan Anda:</span>
                                                <span className={`text-[10px] px-2 py-1 rounded font-bold uppercase ${rev.status === 'resolved' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : rev.status === 'fixing' ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300' : 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300'}`}>
                                                    {rev.status}
                                                </span>
                                            </div>
                                            <p className="text-[#666] dark:text-white/60 mb-3">{rev.revision_note}</p>
                                            {rev.admin_reply && (
                                                <div className="bg-[#f5f0ff] dark:bg-[#7c3aed]/10 p-3 rounded border border-[#ede9fe] dark:border-[#7c3aed]/30">
                                                    <span className="text-xs font-bold text-[#7c3aed] dark:text-[#a78bfa] block mb-1">Balasan Admin:</span>
                                                    <p className="text-[#333] dark:text-white text-xs">{rev.admin_reply}</p>
                                                </div>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    )}

                    {/* Review Form */}
                    {order.status === 'completed' && (
                        <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">Ulasan Anda</h3>
                            {order.rating || order.review ? (
                                <div className="bg-[#f5f0ff] dark:bg-[#7c3aed]/10 p-4 rounded-xl border border-[#ede9fe] dark:border-[#7c3aed]/30">
                                    <div className="flex items-center gap-1 text-amber-500 dark:text-amber-400 mb-2 text-lg">
                                        {[1,2,3,4,5].map(i => (
                                            <i key={i} className={`fa-solid fa-star ${i <= order.rating ? '' : 'text-[#e5e5e5] dark:text-white/20'}`}></i>
                                        ))}
                                    </div>
                                    <p className="text-sm text-[#333] dark:text-white italic">"{order.review}"</p>
                                </div>
                            ) : (
                                <form onSubmit={handleReviewSubmit}>
                                    <div className="mb-4">
                                        <label className="block text-xs font-bold text-[#333] dark:text-white mb-2">Rating (1-5)</label>
                                        <div className="flex items-center gap-4">
                                            {[5,4,3,2,1].map(i => (
                                                <label key={i} className="cursor-pointer text-center group">
                                                    <input type="radio" name="rating" value={i} className="peer sr-only" required onChange={(e) => reviewForm.setData('rating', e.target.value)} />
                                                    <div className="w-10 h-10 rounded-full flex items-center justify-center border-2 border-[#e5e5e5] dark:border-[#1a1a2e] peer-checked:border-amber-500 peer-checked:bg-amber-50 group-hover:border-amber-300 transition-all">
                                                        <span className="font-bold text-[#999] dark:text-white/40 peer-checked:text-amber-500">{i}</span>
                                                    </div>
                                                </label>
                                            ))}
                                        </div>
                                    </div>
                                    <div className="mb-4">
                                        <label className="block text-xs font-bold text-[#333] dark:text-white mb-2">Ulasan</label>
                                        <textarea rows="3" required placeholder="Bagaimana pengalaman Anda bekerja sama dengan kami?" value={reviewForm.data.review} onChange={(e) => reviewForm.setData('review', e.target.value)} className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition"></textarea>
                                    </div>
                                    <button type="submit" disabled={reviewForm.processing} className="bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors w-full">
                                        Kirim Ulasan
                                    </button>
                                </form>
                            )}
                        </div>
                    )}

                    {/* Live Chat */}
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

                {/* Right Column */}
                <div className="space-y-6">
                    {/* Live Preview */}
                    {order.preview_url && (
                        <div className="bg-[#1a1a2e] dark:bg-[#0d0d18] p-6 rounded-2xl shadow-md text-white border border-[#1a1a2e] dark:border-[#1a1a2e]">
                            <div className="flex items-center gap-3 mb-3">
                                <div className="w-10 h-10 bg-white/10 rounded-full flex justify-center items-center">
                                    <i className="fa-solid fa-eye text-emerald-400"></i>
                                </div>
                                <div>
                                    <h3 className="font-bold text-white text-sm">Live Preview</h3>
                                    <p className="text-xs text-white/40">Lihat hasil pengerjaan secara langsung.</p>
                                </div>
                            </div>
                            <a href={order.preview_url} target="_blank" className="block w-full bg-[#7c3aed] hover:bg-[#6d28d9] text-center text-white font-bold px-4 py-2 rounded-lg text-sm transition-colors">
                                Buka Preview URL &rarr;
                            </a>
                        </div>
                    )}

                    {/* 1-Click Deploy */}
                    {order.status === 'completed' && (
                        <div className="bg-gradient-to-br from-[#7c3aed] to-violet-600 p-6 rounded-2xl text-white relative overflow-hidden">
                            <div className="absolute top-0 right-0 p-4 opacity-10">
                                <i className="fa-solid fa-server text-6xl"></i>
                            </div>
                            <div className="relative z-10">
                                <h3 className="font-bold text-white mb-2 text-lg">1-Click Deploy</h3>
                                <p className="text-white/70 text-xs mb-4">Proyek Anda sudah selesai! Anda bisa langsung meng-online-kannya ke layanan Ryaze Hosting hanya dengan satu klik.</p>
                                {order.is_deployed_to_hosting ? (
                                    <div className="bg-white/20 border border-white/30 rounded-lg px-4 py-2 text-sm font-semibold flex items-center justify-center gap-2">
                                        <i className="fa-solid fa-check-circle"></i> Sudah di-deploy
                                    </div>
                                ) : (
                                    <form onSubmit={(e) => { e.preventDefault(); if (confirm('Apakah Anda yakin ingin men-deploy project ini ke Ryaze Hosting?')) router.post(`/user/joki/orders/${order.hashid}/deploy-hosting`); }}>
                                        <button type="submit" className="w-full bg-white dark:bg-white/20 text-[#7c3aed] dark:text-white hover:bg-white/90 dark:hover:bg-white/30 font-bold px-4 py-2.5 rounded-lg text-sm shadow-md transition-colors flex justify-center items-center gap-2">
                                            Deploy ke Ryaze Hosting
                                        </button>
                                    </form>
                                )}
                            </div>
                        </div>
                    )}

                    {/* Payments */}
                    <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-3">Tagihan Pembayaran</h3>
                        {order.payments?.length > 0 ? (
                            <div className="space-y-4">
                                {order.payments.map((payment, idx) => (
                                    <div key={idx} className={`border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg p-4 ${payment.status === 'paid' ? 'bg-emerald-50/30 dark:bg-emerald-500/10' : 'bg-white dark:bg-[#0d0d18]'}`}>
                                        <div className="flex justify-between items-center mb-2">
                                            <span className="font-bold text-[#333] dark:text-white text-sm">{payment.payment_name}</span>
                                            <span className={`text-xs px-2 py-1 rounded font-bold uppercase ${payment.status === 'paid' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : payment.status === 'unpaid' ? 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300' : 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300'}`}>
                                                {payment.status}
                                            </span>
                                        </div>
                                        <div className="text-xl font-black text-[#333] dark:text-white mb-3">{formatRupiah(payment.amount)}</div>
                                        {payment.status === 'unpaid' || payment.status === 'failed' ? (
                                            <button type="button" onClick={() => openPaymentModal(payment.amount, payment.invoice_number)}
                                                className="mt-2 block w-full text-center bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-xs font-bold py-2.5 rounded-lg transition-colors shadow-md">
                                                <i className="fa-solid fa-credit-card mr-1"></i> Pilih Metode Pembayaran
                                            </button>
                                        ) : (
                                            <p className="mt-2 text-xs text-emerald-600 dark:text-emerald-300 font-bold text-center bg-emerald-100 dark:bg-emerald-500/20 py-2 rounded-lg border border-emerald-200 dark:border-emerald-500/40">
                                                <i className="fa-solid fa-check-circle mr-1"></i> LUNAS
                                                {payment.paid_at && ` (${formatDate(payment.paid_at)})`}
                                            </p>
                                        )}
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-6">
                                <i className="fa-solid fa-file-invoice-dollar text-3xl text-[#e5e5e5] dark:text-white/20 mb-2"></i>
                                <p className="text-sm text-[#999] dark:text-white/40">Belum ada tagihan dari Admin.</p>
                            </div>
                        )}
                    </div>

                    {/* Basic Info */}
                    <div className="bg-white dark:bg-[#0d0d18] p-6 rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-3">Informasi Dasar</h3>
                        <div className="space-y-3 text-sm">
                            <div className="flex justify-between">
                                <span className="text-[#999] dark:text-white/40">Status</span>
                                <span className="font-bold text-[#7c3aed] dark:text-[#a78bfa] uppercase">{order.status}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-[#999] dark:text-white/40">Tech Stack</span>
                                <span className="font-semibold text-[#333] dark:text-white">{order.tech_stack || '-'}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-[#999] dark:text-white/40">Harga Total</span>
                                <span className="font-bold text-emerald-600 dark:text-emerald-300">{formatRupiah(order.price)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Payment Modal */}
            {showPaymentModal && (
                <div className="fixed inset-0 z-[100] flex items-center justify-center w-full h-full bg-slate-900/50 backdrop-blur-sm p-4">
                    <div className="relative bg-white dark:bg-[#0d0d18] rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                        <div className="flex items-center justify-between p-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-lg font-bold text-[#333] dark:text-white">Pilih Metode Pembayaran</h3>
                            <button type="button" onClick={() => setShowPaymentModal(false)} className="text-[#999] dark:text-white/40 hover:bg-[#fafafa] dark:hover:bg-white/5 rounded-lg text-sm w-8 h-8 flex justify-center items-center">
                                <i className="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <div className="p-6 space-y-4">
                            <div className="text-center mb-6">
                                <p className="text-sm text-[#999] dark:text-white/40 font-medium mb-1">Total Tagihan</p>
                                <div className="text-3xl font-black text-[#333] dark:text-white">{formatRupiah(paymentData.amount)}</div>
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">Invoice: <span className="font-mono">{paymentData.invoice}</span></p>
                            </div>

                            <a href={`https://app.pakasir.com/pay/${pakasirSlug}/${paymentData.amount}?order_id=${paymentData.invoice}`} target="_blank" onClick={() => setShowPaymentModal(false)}
                                className="flex items-center justify-between p-4 border-2 border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl hover:border-[#7c3aed] hover:bg-[#f5f0ff] dark:hover:bg-[#7c3aed]/10 transition-all group">
                                <div className="flex items-center gap-4">
                                    <div className="w-12 h-12 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 rounded-lg flex items-center justify-center text-[#7c3aed] dark:text-[#a78bfa] group-hover:bg-[#7c3aed] group-hover:text-white transition-colors">
                                        <i className="fa-solid fa-bolt text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 className="font-bold text-[#333] dark:text-white text-sm">Otomatis (Virtual Account/QRIS)</h4>
                                        <p className="text-xs text-[#999] dark:text-white/40">Konfirmasi instan, diproses otomatis.</p>
                                    </div>
                                </div>
                                <i className="fa-solid fa-chevron-right text-[#e5e5e5] dark:text-white/20 group-hover:text-[#7c3aed]"></i>
                            </a>

                            <div className="p-4 border-2 border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl space-y-3 mt-4">
                                <div className="flex items-center gap-4">
                                    <div className="w-12 h-12 bg-blue-50 dark:bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-300">
                                        <i className="fa-solid fa-wallet text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 className="font-bold text-[#333] dark:text-white text-sm">Transfer DANA</h4>
                                        <p className="text-xs text-[#999] dark:text-white/40 font-mono text-lg font-bold mt-1 text-[#333] dark:text-white">{paymentDana}</p>
                                    </div>
                                </div>
                                <div className="pt-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <p className="text-[11px] text-[#999] dark:text-white/40 leading-relaxed mb-3">
                                        Setelah melakukan transfer, silakan kirim bukti pembayaran melalui WhatsApp untuk diverifikasi secara manual oleh Admin.
                                    </p>
                                    <a href={`https://wa.me/62${adminWa}?text=${encodeURIComponent(`Halo Admin, saya ingin konfirmasi pembayaran untuk Invoice *${paymentData.invoice}* sebesar *${formatRupiah(paymentData.amount)}* via DANA. Berikut lampiran buktinya:`)}`}
                                        target="_blank" onClick={() => setShowPaymentModal(false)}
                                        className="block w-full text-center bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 rounded-lg text-sm transition-colors shadow-sm">
                                        <i className="fa-brands fa-whatsapp mr-1"></i> Konfirmasi ke Admin
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
