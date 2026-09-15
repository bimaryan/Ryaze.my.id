import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Databases() {
    const { usersWithDatabases, users } = usePage().props;
    const [showPasswords, setShowPasswords] = useState({});

    const togglePassword = (id) => {
        setShowPasswords((prev) => ({ ...prev, [id]: !prev[id] }));
    };

    return (
        <DashboardLayout title="Databases">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Database Users</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">User Name</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Email</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">DB Username</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">DB Host</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">DB Password</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {usersWithDatabases?.data?.length > 0 ? usersWithDatabases.data.map((item) => (
                                    <tr key={item.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{item.user?.name || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{item.user?.email || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] font-mono text-[#666] dark:text-white/60">{item.db_username || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] font-mono text-[#666] dark:text-white/60">{item.db_host || '-'}</td>
                                        <td className="px-5 py-3">
                                            <div className="flex items-center gap-2">
                                                <span className="text-[13px] font-mono text-[#666] dark:text-white/60">
                                                    {showPasswords[item.id] ? (item.db_password || '-') : '••••••••'}
                                                </span>
                                                <button
                                                    onClick={() => togglePassword(item.id)}
                                                    className="text-[#999] dark:text-white/40 hover:text-[#7c3aed] transition-colors"
                                                >
                                                    <i className={`fa-solid ${showPasswords[item.id] ? 'fa-eye-slash' : 'fa-eye'} text-xs`}></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada database
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {usersWithDatabases?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {usersWithDatabases.from}-{usersWithDatabases.to} dari {usersWithDatabases.total} database
                            </p>
                            <div className="flex items-center gap-1">
                                {usersWithDatabases.prev_page_url && (
                                    <Link href={usersWithDatabases.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(usersWithDatabases.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${usersWithDatabases.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${usersWithDatabases.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {usersWithDatabases.next_page_url && (
                                    <Link href={usersWithDatabases.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
