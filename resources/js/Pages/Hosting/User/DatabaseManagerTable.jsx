import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function DatabaseManagerTable() {
    const { database, table, columns, data, type } = usePage().props;
    const [editingRow, setEditingRow] = useState(null);
    const [editForm, setEditForm] = useState({});

    const handleEdit = (row) => {
        setEditingRow(row);
        setEditForm({ ...row });
    };

    const handleSaveEdit = () => {
        router.put(`/user/hosting/pma/${database?.id}/table/${table}/row`, { original: editingRow, updated: editForm }, {
            preserveState: true,
            onFinish: () => setEditingRow(null),
        });
    };

    const handleDeleteRow = (row) => {
        if (confirm('Delete this row?')) {
            router.delete(`/user/hosting/pma/${database?.id}/table/${table}/row`, {
                data: { row },
                preserveState: true,
            });
        }
    };

    return (
        <DashboardLayout title={`${table} - Database Manager`}>
            <div className="space-y-6">
                <div className="flex items-center gap-3 flex-wrap">
                    <Link href="/user/hosting/pma" className="text-[13px] text-[#999] dark:text-white/40 hover:text-[#7c3aed] transition-colors">
                        <i className="fa-solid fa-arrow-left mr-1"></i>Databases
                    </Link>
                    <span className="text-[#e5e5e5] dark:text-white/20">/</span>
                    <Link href={`/user/hosting/pma/${database?.id}`} className="text-[13px] text-[#999] dark:text-white/40 hover:text-[#7c3aed] transition-colors">
                        {database?.name || database?.database}
                    </Link>
                    <span className="text-[#e5e5e5] dark:text-white/20">/</span>
                    <span className="text-[13px] font-bold text-[#333] dark:text-white">{table}</span>
                    <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed]">{type}</span>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <h3 className="text-[13px] font-bold text-[#333] dark:text-white">Table Data</h3>
                        <span className="text-[12px] text-[#999] dark:text-white/40">{data?.total || data?.length || 0} rows</span>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    {(columns || []).map((col) => (
                                        <th key={col} className="px-4 py-2 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider whitespace-nowrap">{col}</th>
                                    ))}
                                    <th className="px-4 py-2 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {(data?.data || data || []).length > 0 ? (data?.data || data).map((row, ri) => (
                                    <tr key={ri} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        {(columns || []).map((col) => (
                                            <td key={col} className="px-4 py-2 text-[12px] font-mono text-[#333] dark:text-white max-w-[200px] truncate">
                                                {editingRow && editingRow === row ? (
                                                    <input
                                                        type="text"
                                                        value={editForm[col] ?? ''}
                                                        onChange={(e) => setEditForm({ ...editForm, [col]: e.target.value })}
                                                        className="w-full px-2 py-1 text-[12px] font-mono border border-[#7c3aed] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none"
                                                    />
                                                ) : (
                                                    String(row[col] ?? 'NULL')
                                                )}
                                            </td>
                                        ))}
                                        <td className="px-4 py-2">
                                            {editingRow === row ? (
                                                <div className="flex items-center gap-2">
                                                    <button onClick={handleSaveEdit} className="text-[11px] text-green-500 hover:text-green-600 font-medium">Save</button>
                                                    <button onClick={() => setEditingRow(null)} className="text-[11px] text-[#999] hover:text-[#333] font-medium">Cancel</button>
                                                </div>
                                            ) : (
                                                <div className="flex items-center gap-2">
                                                    <button onClick={() => handleEdit(row)} className="text-[11px] text-[#7c3aed] hover:text-[#6d28d9] font-medium">Edit</button>
                                                    <button onClick={() => handleDeleteRow(row)} className="text-[11px] text-red-500 hover:text-red-600 font-medium">Delete</button>
                                                </div>
                                            )}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan={(columns || []).length + 1} className="px-5 py-8 text-center text-[13px] text-[#999] dark:text-white/40">
                                            No data
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {data?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Showing {data.from}-{data.to} of {data.total}
                            </p>
                            <div className="flex items-center gap-1">
                                {data.prev_page_url && (
                                    <Link href={data.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(Math.min(data.last_page, 5))].map((_, i) => (
                                    <Link key={i + 1} href={`${data.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${data.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {data.next_page_url && (
                                    <Link href={data.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
