import { usePage } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Backup() {
    const { backups } = usePage().props;

    const formatSize = (bytes) => {
        if (!bytes) return '0 MB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    };

    return (
        <DashboardLayout title="Backup">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Daftar Backup</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Nama</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Ukuran</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {backups?.length > 0 ? backups.map((b, i) => (
                                    <tr key={i} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3">
                                            <span className="text-[13px] font-semibold text-[#333] dark:text-white font-mono">{b.name || b.filename}</span>
                                        </td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">
                                            {formatSize(b.size)}
                                        </td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {b.date ? new Date(b.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-'}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="3" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada backup
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
