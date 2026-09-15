import DashboardLayout from '../../Layouts/DashboardLayout';
import { router, Link } from '@inertiajs/react';
import { useState } from 'react';

export default function Backup({ backups }) {
    const [showRestoreModal, setShowRestoreModal] = useState(false);
    const [deleteName, setDeleteName] = useState(null);
    const [restoreFile, setRestoreFile] = useState(null);

    function handleCreateBackup(e) {
        e.preventDefault();
        if (confirm('Buat Backup? Proses ini memakan waktu beberapa menit. Lanjutkan?')) {
            router.post(route('superadmin.backup.create'));
        }
    }

    function handleRestore(e) {
        e.preventDefault();
        if (!restoreFile) return;
        if (confirm('APAKAH ANDA YAKIN? Data saat ini akan DITIMPA. Lanjutkan?')) {
            router.post(route('superadmin.backup.restore'), { backup_file: restoreFile }, { forceFormData: true });
        }
    }

    function handleDelete() {
        if (!deleteName) return;
        if (confirm('Hapus Backup? Yakin ingin menghapus file backup ini?')) {
            router.delete(route('superadmin.backup.destroy', { filename: deleteName }), {
                onSuccess: () => setDeleteName(null),
            });
        }
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}, ${hours}:${minutes}`;
    }

    return (
        <DashboardLayout title="Sistem Backup & Restore">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-server text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Sistem Backup & Restore</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola pencadangan data Ryaze dan pemulihan sistem (Database + File Klien).</p>
                    </div>
                </div>
            </div>

            <div className="flex flex-wrap items-center gap-2 mt-4">
                <button onClick={() => setShowRestoreModal(true)} className="flex items-center justify-center gap-2 bg-[#fafafa] dark:bg-white/5 hover:bg-[#f5f0ff] dark:hover:bg-white/10 text-[#666] dark:text-white/60 font-semibold px-4 py-2.5 rounded-xl transition-all duration-300 border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <i className="fa-solid fa-upload"></i> Restore Backup
                </button>
                <form onSubmit={handleCreateBackup} className="inline">
                    <button type="submit" className="flex items-center justify-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-semibold px-4 py-2.5 rounded-xl transition-all duration-300">
                        <i className="fa-solid fa-download"></i> Buat Backup Baru
                    </button>
                </form>
            </div>

            <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="p-6 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-between items-center bg-[#fafafa] dark:bg-white/[0.02]">
                    <h2 className="text-lg font-bold text-[#333] dark:text-white"><i className="fa-solid fa-file-archive text-[#999] dark:text-white/40 mr-2"></i>Riwayat Backup Server</h2>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02]">
                            <tr>
                                <th className="px-6 py-4">Nama File</th>
                                <th className="px-6 py-4">Ukuran</th>
                                <th className="px-6 py-4">Tanggal Dibuat</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {backups?.length > 0 ? backups.map((backup, idx) => (
                                <tr key={idx} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-6 py-4 font-medium text-[#333] dark:text-white whitespace-nowrap">
                                        <div className="flex items-center gap-3">
                                            <div className="w-8 h-8 rounded-lg bg-[#f5f0ff] dark:bg-[#7c3aed]/10 flex items-center justify-center text-[#7c3aed] dark:text-[#a78bfa]">
                                                <i className="fa-solid fa-file-zipper"></i>
                                            </div>
                                            {backup.name}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-[#fafafa] dark:bg-white/5 text-[#666] dark:text-white/60 text-xs font-semibold border border-[#e5e5e5] dark:border-[#1a1a2e]">
                                            <i className="fa-solid fa-hard-drive"></i> {backup.size} MB
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <span className="inline-flex items-center gap-1.5 text-[#999] dark:text-white/40 text-sm">
                                            <i className="fa-regular fa-clock"></i>
                                            {formatDate(backup.date)}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <a href={route('superadmin.backup.download', { filename: backup.name })} className="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] dark:text-[#a78bfa] hover:bg-[#7c3aed] hover:text-white transition-colors" title="Download">
                                            <i className="fa-solid fa-download"></i>
                                        </a>
                                        <button onClick={() => setDeleteName(backup.name)} className="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-300 hover:bg-red-600 hover:text-white transition-colors" title="Hapus">
                                            <i className="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan="4" className="px-6 py-12 text-center text-[#999] dark:text-white/40">
                                        <div className="flex flex-col items-center justify-center">
                                            <i className="fa-solid fa-box-open text-4xl mb-3 text-slate-300 dark:text-slate-400"></i>
                                            <p className="font-medium">Belum ada backup sistem yang dibuat.</p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {showRestoreModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                    <div className="relative w-full max-w-md m-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-2xl shadow-2xl overflow-hidden">
                        <div className="px-6 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-between items-center">
                            <h3 className="font-bold text-[#333] dark:text-white text-lg flex items-center gap-2">
                                <i className="fa-solid fa-upload text-[#7c3aed] dark:text-[#a78bfa]"></i> Restore Backup Sistem
                            </h3>
                            <button onClick={() => setShowRestoreModal(false)} className="text-[#999] dark:text-white/40 hover:text-red-500 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10">
                                <i className="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <form onSubmit={handleRestore} className="p-6 space-y-4">
                            <div className="p-4 mb-2 text-sm text-amber-800 dark:text-amber-200 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200/50 flex items-start gap-3">
                                <i className="fa-solid fa-triangle-exclamation mt-0.5 text-amber-600 dark:text-amber-300"></i>
                                <div className="leading-relaxed">
                                    <span className="font-bold block mb-0.5">Peringatan Kritis</span>
                                    Proses ini akan menimpa (overwrite) seluruh database Ryaze dan file-file klien yang ada.
                                </div>
                            </div>

                            <div>
                                <label className="block mb-2 text-sm font-semibold text-[#666] dark:text-white/60">Upload File Backup (.zip)</label>
                                <input type="file" name="backup_file" accept=".zip" required onChange={(e) => setRestoreFile(e.target.files[0])} className="block w-full text-sm text-[#999] dark:text-white/40 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#f5f0ff] file:text-[#7c3aed] hover:file:bg-[#ede9fe] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl bg-[#fafafa] dark:bg-[#0d0d18] focus:outline-none focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] transition-all" />
                                <p className="mt-2 text-xs text-[#999] dark:text-white/40 font-medium"><i className="fa-solid fa-circle-info mr-1 text-[#999] dark:text-white/40"></i>Maksimal ukuran file: 500MB</p>
                            </div>

                            <div className="pt-4 flex justify-end gap-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] mt-6">
                                <button type="button" onClick={() => setShowRestoreModal(false)} className="px-5 py-2.5 text-sm font-semibold text-[#666] dark:text-white/60 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl hover:bg-[#fafafa] dark:hover:bg-white/5 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" className="text-white bg-[#7c3aed] hover:bg-[#6d28d9] focus:ring-4 focus:outline-none focus:ring-[#7c3aed]/30 font-semibold rounded-xl text-sm px-5 py-2.5 transition-colors flex items-center gap-2">
                                    <i className="fa-solid fa-upload"></i> Restore Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {deleteName && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                    <div className="relative w-full max-w-sm m-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl p-6 text-center">
                        <div className="w-12 h-12 bg-red-50 dark:bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i className="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
                        </div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Hapus Backup?</h3>
                        <p className="text-sm text-[#999] dark:text-white/40 mb-6">Yakin ingin menghapus file backup ini?</p>
                        <div className="flex justify-center gap-3">
                            <button onClick={() => setDeleteName(null)} className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg hover:bg-[#f5f0ff] dark:hover:bg-white/5 transition">Batal</button>
                            <button onClick={handleDelete} className="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">Ya, Hapus</button>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
