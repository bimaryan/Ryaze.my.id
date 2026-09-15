import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, router } from '@inertiajs/react';
import { useState } from 'react';
import Swal from 'sweetalert2';

function generatePassword(length = 16) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
    let pass = '';
    for (let i = 0; i < length; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
    return pass;
}

export default function Emails({ emails, projects }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const emailList = Array.isArray(emails) ? emails : (emails?.data || []);
    const projectList = Array.isArray(projects) ? projects : (projects?.data || []);

    const { data, setData, post, processing, reset } = useForm({
        email_prefix: '',
        domain: '',
        password: '',
    });

    function handleCreate(e) {
        e.preventDefault();
        post(route('user_hosting.emails.store'), {
            onSuccess: () => { setShowCreateModal(false); reset(); },
        });
    }

    function handleDelete(email) {
        Swal.fire({
            title: `Hapus ${email.email_address}?`,
            text: 'Email dan semua datanya akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('user_hosting.emails.destroy', email.hashid || email.id));
            }
        });
    }

    const domains = projectList.map(p => p.ryaze_domain).filter(Boolean);

    return (
        <DashboardLayout title="Email Manager">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-envelope text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Email Manager</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola alamat email untuk domain Anda.</p>
                    </div>
                    <div className="ml-auto">
                        <button onClick={() => setShowCreateModal(true)}
                            className="inline-flex items-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            + Buat Email
                        </button>
                    </div>
                </div>
            </div>

            <div className="mt-6">
                {emailList.length === 0 ? (
                    <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] p-12 text-center">
                        <div className="w-16 h-16 bg-slate-100 dark:bg-slate-700/50 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                            <i className="fa-solid fa-envelope"></i>
                        </div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Belum ada email</h3>
                        <p className="text-[#999] dark:text-white/40 mb-6 text-sm">Buat alamat email pertama Anda untuk domain yang tersedia.</p>
                        <button onClick={() => setShowCreateModal(true)}
                            className="inline-flex items-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            + Buat Email
                        </button>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {emailList.map(email => (
                            <div key={email.id || email.hashid} className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed]/30 hover:shadow-md transition-all duration-200 flex flex-col">
                                <div className="p-5 flex-1">
                                    <div className="flex items-center justify-between mb-3">
                                        <div className="flex items-center gap-2">
                                            <i className="fa-solid fa-envelope text-[#7c3aed]"></i>
                                            <span className="font-bold text-[#333] dark:text-white text-sm">{email.email_address}</span>
                                        </div>
                                        <button onClick={() => handleDelete(email)} className="text-rose-500 hover:text-rose-700 transition" title="Hapus">
                                            <i className="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                    <div className="flex items-center gap-2 mb-2">
                                        <span className="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">
                                            <i className="fa-solid fa-circle-check mr-1"></i>Active
                                        </span>
                                    </div>
                                    <div className="space-y-2 text-xs">
                                        <div className="flex items-center justify-between">
                                            <span className="text-[#999] dark:text-white/40">Domain</span>
                                            <span className="font-mono text-[#333] dark:text-white">{email.domain}</span>
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <span className="text-[#999] dark:text-white/40">Quota</span>
                                            <span className="text-[#333] dark:text-white">{email.quota || '500 MB'}</span>
                                        </div>
                                    </div>
                                </div>
                                <div className="px-5 py-3 bg-[#fafafa] dark:bg-white/[0.02] border-t border-[#e5e5e5] dark:border-[#1a1a2e] rounded-b-xl">
                                    <a href={`https://webmail.${email.domain}`} target="_blank" rel="noopener noreferrer"
                                        className="text-xs font-semibold text-[#7c3aed] dark:text-[#a78bfa] hover:underline flex items-center gap-1">
                                        <i className="fa-solid fa-globe"></i> Buka Webmail <i className="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* Create Modal */}
            {showCreateModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div className="w-full max-w-md bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl">
                        <div className="p-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm">Buat Email Baru</h3>
                            <button onClick={() => { setShowCreateModal(false); reset(); }} className="text-[#999] hover:text-red-500 transition">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <form onSubmit={handleCreate} className="p-5 space-y-4">
                            <div>
                                <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Email Prefix</label>
                                <div className="flex items-center">
                                    <input type="text" value={data.email_prefix} onChange={e => setData('email_prefix', e.target.value)} required
                                        placeholder="nama"
                                        className="flex-1 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-l-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                    <span className="text-xs text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border border-l-0 border-[#e5e5e5] dark:border-[#1a1a2e] rounded-r-xl px-3 py-2.5 font-mono">@</span>
                                </div>
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Domain</label>
                                <select value={data.domain} onChange={e => setData('domain', e.target.value)} required
                                    className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition">
                                    <option value="">Pilih domain</option>
                                    {domains.map(d => <option key={d} value={d}>{d}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Password</label>
                                <div className="flex items-center gap-2">
                                    <input type="text" value={data.password} onChange={e => setData('password', e.target.value)}
                                        className="flex-1 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                    <button type="button" onClick={() => setData('password', generatePassword())}
                                        className="px-3 py-2.5 text-xs font-medium text-[#7c3aed] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 rounded-lg hover:bg-[#ede4ff] dark:hover:bg-[#7c3aed]/20 transition whitespace-nowrap">
                                        <i className="fa-solid fa-rotate mr-1"></i>Generate
                                    </button>
                                </div>
                            </div>
                            <div className="flex justify-end gap-3 pt-2">
                                <button type="button" onClick={() => { setShowCreateModal(false); reset(); }}
                                    className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg hover:bg-[#fafafa] transition">
                                    Batal
                                </button>
                                <button type="submit" disabled={processing}
                                    className="px-4 py-2 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition disabled:opacity-50">
                                    {processing ? 'Membuat...' : 'Buat Email'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
