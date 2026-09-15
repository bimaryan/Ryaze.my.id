import { useState } from 'react';
import { usePage, router } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Tunnels() {
    const { tunnels } = usePage().props;
    const [showCreate, setShowCreate] = useState(false);
    const [form, setForm] = useState({ name: '', local_port: '' });

    const handleCreate = (e) => {
        e.preventDefault();
        router.form(form).post('/user/hosting/tunnels', {
            onSuccess: () => { setShowCreate(false); setForm({ name: '', local_port: '' }); }
        });
    };

    const handleDelete = (id) => {
        if (confirm('Delete this tunnel?')) {
            router.delete(`/user/hosting/tunnels/${id}`);
        }
    };

    const statusBadge = (status) => {
        const styles = {
            active: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            inactive: 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300',
            error: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${styles[status] || styles.active}`}>
                {status || 'active'}
            </span>
        );
    };

    return (
        <DashboardLayout title="Tunnels">
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <p className="text-[13px] text-[#666] dark:text-white/60">{tunnels?.data?.length || tunnels?.length || 0} tunnels</p>
                        <p className="text-[11px] text-[#999] dark:text-white/40 mt-1">Expose local ports to the public internet.</p>
                    </div>
                    <button onClick={() => setShowCreate(true)} className="px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                        <i className="fa-solid fa-plus mr-1.5"></i>New Tunnel
                    </button>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tunnel Name</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Local Port</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Public URL</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {(tunnels?.data || tunnels || []).length > 0 ? (tunnels?.data || tunnels).map((tunnel) => (
                                    <tr key={tunnel.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{tunnel.name}</td>
                                        <td className="px-5 py-3 text-[13px] font-mono text-[#666] dark:text-white/60">{tunnel.local_port}</td>
                                        <td className="px-5 py-3">
                                            {tunnel.public_url ? (
                                                <a href={tunnel.public_url} target="_blank" rel="noopener noreferrer" className="text-[13px] text-[#7c3aed] hover:text-[#6d28d9] font-medium transition-colors break-all">
                                                    {tunnel.public_url}
                                                    <i className="fa-solid fa-arrow-up-right-from-square text-[10px] ml-1"></i>
                                                </a>
                                            ) : (
                                                <span className="text-[13px] text-[#999] dark:text-white/40">-</span>
                                            )}
                                        </td>
                                        <td className="px-5 py-3">{statusBadge(tunnel.status)}</td>
                                        <td className="px-5 py-3 text-right">
                                            <button onClick={() => handleDelete(tunnel.id)} className="text-[12px] text-red-500 hover:text-red-600 font-medium transition-colors">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-12 text-center">
                                            <i className="fa-solid fa-network-wired text-3xl text-[#e5e5e5] dark:text-white/10 mb-3 block"></i>
                                            <p className="text-[13px] text-[#999] dark:text-white/40 mb-3">No tunnels configured</p>
                                            <button onClick={() => setShowCreate(true)} className="text-[13px] text-[#7c3aed] hover:text-[#6d28d9] font-medium transition-colors">
                                                Create your first tunnel
                                            </button>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {tunnels?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Showing {tunnels.from}-{tunnels.to} of {tunnels.total}
                            </p>
                            <div className="flex items-center gap-1">
                                {tunnels.prev_page_url && (
                                    <a href={tunnels.prev_page_url} className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</a>
                                )}
                                {tunnels.next_page_url && (
                                    <a href={tunnels.next_page_url} className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</a>
                                )}
                            </div>
                        </div>
                    )}
                </div>

                {showCreate && (
                    <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] max-w-md w-full">
                            <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                                <h3 className="text-[15px] font-bold text-[#333] dark:text-white">Create Tunnel</h3>
                                <button onClick={() => setShowCreate(false)} className="text-[#999] hover:text-[#333] dark:hover:text-white transition-colors">
                                    <i className="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <form onSubmit={handleCreate} className="p-5 space-y-4">
                                <div>
                                    <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Tunnel Name</label>
                                    <input type="text" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} required placeholder="my-tunnel" className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors" />
                                </div>
                                <div>
                                    <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Local Port</label>
                                    <input type="number" value={form.local_port} onChange={(e) => setForm({ ...form, local_port: e.target.value })} required placeholder="3000" min="1" max="65535" className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors" />
                                </div>
                                <div className="flex items-center gap-3 pt-2">
                                    <button type="submit" className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">Create</button>
                                    <button type="button" onClick={() => setShowCreate(false)} className="px-5 py-2 text-[13px] font-medium text-[#666] dark:text-white/60 hover:text-[#7c3aed] transition-colors">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
