import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function DatabasePma() {
    const { databases, pgsqlDatabases } = usePage().props;

    const allDatabases = [
        ...(databases || []).map(d => ({ ...d, type: 'MySQL' })),
        ...(pgsqlDatabases || []).map(d => ({ ...d, type: 'PostgreSQL' })),
    ];

    return (
        <DashboardLayout title="Database Manager">
            <div className="space-y-6">
                <div>
                    <h2 className="text-[13px] font-bold text-[#333] dark:text-white">Database Manager</h2>
                    <p className="text-[12px] text-[#999] dark:text-white/40 mt-1">Select a database to manage tables, run queries, and view data.</p>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {allDatabases.length > 0 ? allDatabases.map((db) => (
                        <Link
                            key={db.id}
                            href={`/user/hosting/pma/${db.id}`}
                            className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed]/50 transition-all p-5 group"
                        >
                            <div className="flex items-center gap-3 mb-3">
                                <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 flex items-center justify-center">
                                    <i className="fa-solid fa-database text-[#7c3aed] text-lg"></i>
                                </div>
                                <div>
                                    <h3 className="text-[14px] font-bold text-[#333] dark:text-white group-hover:text-[#7c3aed] transition-colors">{db.name || db.database}</h3>
                                    <p className="text-[11px] text-[#999] dark:text-white/40">{db.type}</p>
                                </div>
                            </div>
                            <div className="space-y-1.5">
                                <div className="flex items-center justify-between text-[12px]">
                                    <span className="text-[#999] dark:text-white/40">Host</span>
                                    <span className="text-[#666] dark:text-white/60 font-mono">{db.host || 'localhost'}</span>
                                </div>
                                <div className="flex items-center justify-between text-[12px]">
                                    <span className="text-[#999] dark:text-white/40">Username</span>
                                    <span className="text-[#666] dark:text-white/60">{db.username}</span>
                                </div>
                                <div className="flex items-center justify-between text-[12px]">
                                    <span className="text-[#999] dark:text-white/40">Status</span>
                                    <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300">Active</span>
                                </div>
                            </div>
                            <div className="mt-4 pt-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-center">
                                <span className="text-[12px] text-[#7c3aed] font-medium group-hover:underline">
                                    <i className="fa-solid fa-arrow-right mr-1"></i>Open Manager
                                </span>
                            </div>
                        </Link>
                    )) : (
                        <div className="col-span-full py-12 text-center">
                            <i className="fa-solid fa-database text-3xl text-[#e5e5e5] dark:text-white/10 mb-3 block"></i>
                            <p className="text-[13px] text-[#999] dark:text-white/40 mb-3">No databases available</p>
                            <Link href="/user/hosting/databases" className="text-[13px] text-[#7c3aed] hover:text-[#6d28d9] font-medium transition-colors">
                                Create a database first
                            </Link>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
