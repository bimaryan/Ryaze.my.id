import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function DatabaseManager() {
    const { database, tables, type } = usePage().props;
    const [query, setQuery] = useState('');
    const [queryResult, setQueryResult] = useState(null);

    const handleQuery = (e) => {
        e.preventDefault();
        router.post(`/user/hosting/pma/${database?.id}/query`, { query }, {
            preserveState: true,
            onSuccess: (page) => {
                setQueryResult(page.props.queryResult);
            }
        });
    };

    return (
        <DashboardLayout title="Database Manager">
            <div className="space-y-6">
                <div className="flex items-center gap-3">
                    <Link href="/user/hosting/pma" className="text-[13px] text-[#999] dark:text-white/40 hover:text-[#7c3aed] transition-colors">
                        <i className="fa-solid fa-arrow-left mr-1"></i>Databases
                    </Link>
                    <span className="text-[#e5e5e5] dark:text-white/20">/</span>
                    <span className="text-[13px] font-bold text-[#333] dark:text-white">{database?.name || database?.database}</span>
                    <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed]">{type}</span>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <div className="lg:col-span-1">
                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <div className="px-4 py-3 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <h3 className="text-[12px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tables ({tables?.length || 0})</h3>
                            </div>
                            <div className="max-h-96 overflow-y-auto divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {tables?.length > 0 ? tables.map((table) => (
                                    <Link
                                        key={table}
                                        href={`/user/hosting/pma/${database?.id}/table/${table}`}
                                        className="block px-4 py-2.5 text-[13px] text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5 hover:text-[#7c3aed] transition-colors"
                                    >
                                        <i className="fa-solid fa-table text-[10px] mr-2 text-[#999] dark:text-white/30"></i>
                                        {table}
                                    </Link>
                                )) : (
                                    <div className="px-4 py-6 text-center text-[12px] text-[#999] dark:text-white/40">No tables</div>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className="lg:col-span-3 space-y-4">
                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <h3 className="text-[13px] font-bold text-[#333] dark:text-white">SQL Query</h3>
                            </div>
                            <form onSubmit={handleQuery} className="p-4">
                                <textarea
                                    value={query}
                                    onChange={(e) => setQuery(e.target.value)}
                                    rows={4}
                                    placeholder="SELECT * FROM users LIMIT 10;"
                                    className="w-full px-3 py-2 text-[12px] font-mono border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors resize-none"
                                />
                                <div className="flex items-center gap-3 mt-3">
                                    <button type="submit" className="px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                                        <i className="fa-solid fa-play mr-1.5"></i>Execute
                                    </button>
                                    <button type="button" onClick={() => { setQuery(''); setQueryResult(null); }} className="px-4 py-2 text-[13px] font-medium text-[#666] dark:text-white/60 hover:text-[#7c3aed] transition-colors">
                                        Clear
                                    </button>
                                </div>
                            </form>
                        </div>

                        {queryResult && (
                            <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                                    <h3 className="text-[13px] font-bold text-[#333] dark:text-white">Result</h3>
                                    {queryResult.message && (
                                        <span className="text-[12px] text-[#999] dark:text-white/40">{queryResult.message}</span>
                                    )}
                                </div>
                                <div className="overflow-x-auto">
                                    {queryResult.columns && queryResult.rows ? (
                                        <table className="w-full text-left">
                                            <thead>
                                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                                    {queryResult.columns.map((col, i) => (
                                                        <th key={i} className="px-4 py-2 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">{col}</th>
                                                    ))}
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                                {queryResult.rows.map((row, ri) => (
                                                    <tr key={ri} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                                        {queryResult.columns.map((col, ci) => (
                                                            <td key={ci} className="px-4 py-2 text-[12px] font-mono text-[#333] dark:text-white max-w-[200px] truncate">{String(row[col] ?? 'NULL')}</td>
                                                        ))}
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    ) : (
                                        <div className="p-4 text-[13px] text-[#666] dark:text-white/60">{queryResult.message || 'Query executed successfully.'}</div>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
