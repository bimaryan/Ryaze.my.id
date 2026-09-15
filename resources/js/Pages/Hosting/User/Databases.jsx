import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Databases() {
    const { databases, nosqlDatabases, pgsqlDatabases } = usePage().props;
    const [activeTab, setActiveTab] = useState('mysql');
    const [showCreate, setShowCreate] = useState(false);
    const [form, setForm] = useState({ name: '', username: '', password: '' });

    const handleCreate = (e) => {
        e.preventDefault();
        router.form({ ...form, type: activeTab }).post('/user/hosting/databases', {
            onSuccess: () => { setShowCreate(false); setForm({ name: '', username: '', password: '' }); }
        });
    };

    const statusBadge = (status) => {
        const styles = {
            active: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            inactive: 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${styles[status] || styles.active}`}>
                {status || 'active'}
            </span>
        );
    };

    const tabs = [
        { key: 'mysql', label: 'MySQL', data: databases },
        { key: 'mongodb', label: 'MongoDB', data: nosqlDatabases },
        { key: 'postgresql', label: 'PostgreSQL', data: pgsqlDatabases },
    ];

    const currentData = tabs.find(t => t.key === activeTab)?.data || [];

    return (
        <DashboardLayout title="Databases">
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                        {tabs.map((tab) => (
                            <button
                                key={tab.key}
                                onClick={() => setActiveTab(tab.key)}
                                className={`px-4 py-2 text-[13px] font-medium transition-colors ${
                                    activeTab === tab.key
                                        ? 'bg-[#7c3aed] text-white'
                                        : 'border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'
                                }`}
                            >
                                {tab.label}
                                <span className="ml-1.5 text-[11px] opacity-60">({(tab.data?.data || tab.data || []).length})</span>
                            </button>
                        ))}
                    </div>
                    <button onClick={() => setShowCreate(true)} className="px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                        <i className="fa-solid fa-plus mr-1.5"></i>New Database
                    </button>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">DB Name</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Username</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Host</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {(currentData?.data || currentData || []).length > 0 ? (currentData?.data || currentData).map((db) => (
                                    <tr key={db.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{db.name || db.database}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{db.username}</td>
                                        <td className="px-5 py-3 text-[13px] font-mono text-[#666] dark:text-white/60">{db.host || 'localhost'}</td>
                                        <td className="px-5 py-3">{statusBadge(db.status)}</td>
                                        <td className="px-5 py-3 text-right">
                                            <Link href={`/user/hosting/pma/${db.id}`} className="text-[12px] text-[#7c3aed] hover:text-[#6d28d9] font-medium transition-colors">
                                                <i className="fa-solid fa-database mr-1"></i>Manage
                                            </Link>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-12 text-center">
                                            <i className="fa-solid fa-database text-3xl text-[#e5e5e5] dark:text-white/10 mb-3 block"></i>
                                            <p className="text-[13px] text-[#999] dark:text-white/40 mb-3">No {activeTab} databases</p>
                                            <button onClick={() => setShowCreate(true)} className="text-[13px] text-[#7c3aed] hover:text-[#6d28d9] font-medium transition-colors">
                                                Create your first database
                                            </button>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {showCreate && (
                    <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] max-w-md w-full">
                            <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                                <h3 className="text-[15px] font-bold text-[#333] dark:text-white">Create {activeTab.toUpperCase()} Database</h3>
                                <button onClick={() => setShowCreate(false)} className="text-[#999] hover:text-[#333] dark:hover:text-white transition-colors">
                                    <i className="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <form onSubmit={handleCreate} className="p-5 space-y-4">
                                <div>
                                    <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Database Name</label>
                                    <input type="text" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} required className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors" />
                                </div>
                                <div>
                                    <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Username</label>
                                    <input type="text" value={form.username} onChange={(e) => setForm({ ...form, username: e.target.value })} required className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors" />
                                </div>
                                <div>
                                    <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Password</label>
                                    <input type="password" value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} required className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors" />
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
