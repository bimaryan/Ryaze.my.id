import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, router } from '@inertiajs/react';
import { useState } from 'react';

function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

export default function Services({ services }) {
    const { errors } = useForm();
    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";

    const [showCreate, setShowCreate] = useState(false);
    const [showEdit, setShowEdit] = useState(false);
    const [editService, setEditService] = useState(null);

    const createForm = useForm({ name: '', description: '', base_price: '', is_active: true });
    const editForm = useForm({ name: '', description: '', base_price: '', is_active: true });

    const openEditModal = (service) => {
        setEditService(service);
        editForm.setData({
            name: service.name,
            description: service.description,
            base_price: service.base_price,
            is_active: service.is_active,
        });
        setShowEdit(true);
    };

    const handleCreate = (e) => {
        e.preventDefault();
        createForm.post('/admin/joki/services', {
            onSuccess: () => { createForm.reset(); setShowCreate(false); },
        });
    };

    const handleEdit = (e) => {
        e.preventDefault();
        editForm.put(`/admin/joki/services/${editService.hashid}`, {
            onSuccess: () => { editForm.reset(); setShowEdit(false); setEditService(null); },
        });
    };

    const handleDelete = (service) => {
        if (window.Swal) {
            window.Swal.fire({
                title: 'Hapus Layanan?',
                text: 'Yakin ingin menghapus layanan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    router.delete(`/admin/joki/services/${service.hashid}`);
                }
            });
        } else {
            if (confirm('Yakin ingin menghapus layanan ini?')) {
                router.delete(`/admin/joki/services/${service.hashid}`);
            }
        }
    };

    return (
        <DashboardLayout title="Manajemen Layanan Joki">
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-list text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Manajemen Layanan Joki</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola tipe layanan joki, harga dasar, dan status aktifnya.</p>
                    </div>
                    <div className="ml-auto">
                        <button onClick={() => setShowCreate(true)} className="inline-flex items-center flex-shrink-0 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            + Tambah Layanan
                        </button>
                    </div>
                </div>
            </div>

            <div className="mt-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm text-left">
                            <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <tr>
                                    <th className="px-6 py-4">Nama Layanan</th>
                                    <th className="px-6 py-4">Harga Dasar</th>
                                    <th className="px-6 py-4">Status</th>
                                    <th className="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {services?.length > 0 ? services.map((service, idx) => (
                                    <tr key={idx} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="font-bold text-[#333] dark:text-white">{service.name}</div>
                                            <div className="text-xs text-[#999] dark:text-white/40 truncate max-w-xs">{service.description?.substring(0, 50)}</div>
                                        </td>
                                        <td className="px-6 py-4 font-mono font-medium">{formatRupiah(service.base_price)}</td>
                                        <td className="px-6 py-4">
                                            <span className={`px-2.5 py-1 text-xs font-bold rounded-full whitespace-nowrap inline-block ${service.is_active ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300'}`}>
                                                {service.is_active ? 'Aktif' : 'Tidak Aktif'}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <div className="flex items-center justify-end gap-2">
                                                <button onClick={() => openEditModal(service)} className="w-8 h-8 rounded-lg flex items-center justify-center text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 hover:bg-[#7c3aed] hover:text-white transition-all duration-200 shadow-sm" title="Edit Layanan">
                                                    <i className="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button onClick={() => handleDelete(service)} className="w-8 h-8 rounded-lg flex items-center justify-center text-rose-600 dark:text-rose-300 bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-600 hover:text-white transition-all duration-200 shadow-sm" title="Hapus Layanan">
                                                    <i className="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="4" className="px-6 py-10 text-center text-[#999] dark:text-white/40">Belum ada layanan yang ditambahkan.</td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {/* Create Modal */}
            {showCreate && (
                <div className="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4">
                    <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
                        <div className="p-6 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between bg-[#fafafa] dark:bg-white/[0.02]">
                            <h3 className="text-lg font-bold text-[#333] dark:text-white">Tambah Layanan Joki</h3>
                            <button onClick={() => setShowCreate(false)} className="text-[#999] dark:text-white/40 hover:text-rose-500 transition-colors p-2 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                <i className="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <form onSubmit={handleCreate}>
                            <div className="p-6 space-y-4">
                                <div>
                                    <label className="block text-sm font-medium text-[#333] dark:text-white mb-1">Nama Layanan</label>
                                    <input type="text" required value={createForm.data.name} onChange={(e) => createForm.setData('name', e.target.value)} className={inputCls} />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-[#333] dark:text-white mb-1">Deskripsi</label>
                                    <textarea rows="3" required value={createForm.data.description} onChange={(e) => createForm.setData('description', e.target.value)} className={inputCls}></textarea>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-[#333] dark:text-white mb-1">Harga Dasar (Rp)</label>
                                    <input type="number" required min="0" value={createForm.data.base_price} onChange={(e) => createForm.setData('base_price', e.target.value)} className={inputCls} />
                                </div>
                                <div className="flex items-center gap-2">
                                    <input type="checkbox" checked={createForm.data.is_active} onChange={(e) => createForm.setData('is_active', e.target.checked)} className="rounded border-[#e5e5e5] dark:border-white/20 text-[#7c3aed] focus:ring-[#7c3aed]" />
                                    <label className="text-sm font-medium text-[#333] dark:text-white">Aktifkan Layanan</label>
                                </div>
                            </div>
                            <div className="p-6 border-t border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] flex justify-end gap-3">
                                <button type="button" onClick={() => setShowCreate(false)} className="px-5 py-2.5 text-sm font-medium text-[#666] dark:text-white/60 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl hover:bg-[#fafafa] dark:hover:bg-white/5 transition-colors">Batal</button>
                                <button type="submit" disabled={createForm.processing} className="px-5 py-2.5 text-sm font-medium text-white bg-[#7c3aed] rounded-xl hover:bg-[#6d28d9] shadow-sm transition-all">Simpan Layanan</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Edit Modal */}
            {showEdit && editService && (
                <div className="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4">
                    <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
                        <div className="p-6 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between bg-[#fafafa] dark:bg-white/[0.02]">
                            <h3 className="text-lg font-bold text-[#333] dark:text-white">Edit Layanan Joki</h3>
                            <button onClick={() => { setShowEdit(false); setEditService(null); }} className="text-[#999] dark:text-white/40 hover:text-rose-500 transition-colors p-2 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                <i className="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <form onSubmit={handleEdit}>
                            <div className="p-6 space-y-4">
                                <div>
                                    <label className="block text-sm font-medium text-[#333] dark:text-white mb-1">Nama Layanan</label>
                                    <input type="text" required value={editForm.data.name} onChange={(e) => editForm.setData('name', e.target.value)} className={inputCls} />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-[#333] dark:text-white mb-1">Deskripsi</label>
                                    <textarea rows="3" required value={editForm.data.description} onChange={(e) => editForm.setData('description', e.target.value)} className={inputCls}></textarea>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-[#333] dark:text-white mb-1">Harga Dasar (Rp)</label>
                                    <input type="number" required min="0" value={editForm.data.base_price} onChange={(e) => editForm.setData('base_price', e.target.value)} className={inputCls} />
                                </div>
                                <div className="flex items-center gap-2">
                                    <input type="checkbox" checked={editForm.data.is_active} onChange={(e) => editForm.setData('is_active', e.target.checked)} className="rounded border-[#e5e5e5] dark:border-white/20 text-[#7c3aed] focus:ring-[#7c3aed]" />
                                    <label className="text-sm font-medium text-[#333] dark:text-white">Aktifkan Layanan</label>
                                </div>
                            </div>
                            <div className="p-6 border-t border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] flex justify-end gap-3">
                                <button type="button" onClick={() => { setShowEdit(false); setEditService(null); }} className="px-5 py-2.5 text-sm font-medium text-[#666] dark:text-white/60 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl hover:bg-[#fafafa] dark:hover:bg-white/5 transition-colors">Batal</button>
                                <button type="submit" disabled={editForm.processing} className="px-5 py-2.5 text-sm font-medium text-white bg-[#7c3aed] rounded-xl hover:bg-[#6d28d9] shadow-sm transition-all">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
