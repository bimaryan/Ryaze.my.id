import DashboardLayout from '../../Layouts/DashboardLayout';
import { router, Link } from '@inertiajs/react';
import { useRef, useState } from 'react';

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

function formatTime(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${hours}:${minutes} WIB`;
}

function formatRp(n) {
    if (!n) return 'Rp 0';
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

function StatusBadge({ status }) {
    const cls =
        status === 'active' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' :
        status === 'completed' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' :
        status === 'progress' ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300' :
        status === 'suspended' ? 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300' :
        'bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]';
    return <span className={`text-[10px] px-2 py-0.5 rounded font-bold uppercase ${cls}`}>{status}</span>;
}

function RoleBadge({ role }) {
    return (
        <span className="inline-block px-3 py-1 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa] rounded-full text-[11px] tracking-wide font-bold uppercase border border-[#e5e5e5] dark:border-[#1a1a2e]">
            {role?.replace(/_/g, ' ')}
        </span>
    );
}

function PlanBadge({ plan, label }) {
    const colorMap = {
        slate: 'bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa]',
        indigo: 'bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa]',
        violet: 'bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa]',
        amber: 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300',
    };
    const cls = colorMap[plan] || colorMap.slate;
    return <span className={`font-bold px-2 py-0.5 rounded-full text-[10px] uppercase tracking-wider ${cls}`}>{label}</span>;
}

export default function UserShow({ user, jokiOrders, hostingProjects }) {
    const roleModalRef = useRef(null);
    const [role, setRole] = useState(user?.role || 'user_hosting');
    const [deleteConfirm, setDeleteConfirm] = useState(false);

    const activeBilling = user?.hosting_billings?.find(b => b.status === 'active');
    const currentPlan = activeBilling?.plan || 'free';
    const planLabels = {
        free: 'Free', starter: 'Starter', pro: 'Pro', enterprise: 'Enterprise',
        basic: 'Basic', premium: 'Premium',
    };

    function handleEditRole(e) {
        e.preventDefault();
        router.put(route('superadmin.users.role.update', { hashid: user.hashid }), { role }, {
            preserveState: true,
            onSuccess: () => roleModalRef.current?.close(),
        });
    }

    function handleToggleStatus() {
        const isActive = user.status === 'active';
        if (window.Swal) {
            window.Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin ${isActive ? 'menangguhkan' : 'mengaktifkan'} akun ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    router.patch(route('superadmin.users.status.toggle', { hashid: user.hashid }), {}, { preserveState: true });
                }
            });
        } else {
            if (confirm(`Apakah Anda yakin ingin ${isActive ? 'menangguhkan' : 'mengaktifkan'} akun ini?`)) {
                router.patch(route('superadmin.users.status.toggle', { hashid: user.hashid }), {}, { preserveState: true });
            }
        }
    }

    function handleDelete() {
        if (window.Swal) {
            window.Swal.fire({
                title: 'Peringatan',
                text: 'Peringatan: Aksi ini akan menghapus akun user secara permanen. Lanjutkan?',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    router.delete(route('superadmin.users.destroy', { hashid: user.hashid }), { preserveState: true });
                }
            });
        } else {
            if (confirm('Peringatan: Aksi ini akan menghapus akun user secara permanen. Lanjutkan?')) {
                router.delete(route('superadmin.users.destroy', { hashid: user.hashid }), { preserveState: true });
            }
        }
    }

    return (
        <DashboardLayout title={`Profil Klien: ${user?.name}`}>
            <div className="space-y-0">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-1">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                            <i className="fa-solid fa-user text-[#7c3aed] dark:text-[#a78bfa]"></i>
                        </div>
                        <div>
                            <h1 className="text-lg font-bold text-[#333] dark:text-white">Profil Klien: {user?.name}</h1>
                            <p className="text-[13px] text-[#999] dark:text-white/40">Lihat informasi detail dan riwayat pesanan klien.</p>
                        </div>
                    </div>

                    <div className="flex items-center gap-2 flex-wrap">
                        {user?.id !== usePage().props.auth?.user?.id && (
                            <>
                                <button
                                    onClick={() => roleModalRef.current?.showModal()}
                                    className="inline-flex justify-center items-center bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm border border-transparent"
                                >
                                    <i className="fa-solid fa-user-shield mr-2"></i> Edit Role
                                </button>
                                <button
                                    onClick={handleToggleStatus}
                                    className={`inline-flex justify-center items-center px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm border ${
                                        user?.status === 'active'
                                            ? 'bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-300 hover:bg-orange-100 dark:hover:bg-orange-500/20 border-orange-200 dark:border-orange-500/40'
                                            : 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 border-emerald-200 dark:border-emerald-500/40'
                                    }`}
                                >
                                    <i className={`fa-solid ${user?.status === 'active' ? 'fa-ban' : 'fa-check'} mr-2`}></i>
                                    {user?.status === 'active' ? 'Suspend' : 'Unsuspend'}
                                </button>
                                <button
                                    onClick={handleDelete}
                                    className="inline-flex justify-center items-center bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/40 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-700 dark:text-red-300 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm"
                                >
                                    <i className="fa-solid fa-trash mr-2"></i> Hapus
                                </button>
                            </>
                        )}
                        <Link
                            href={route('superadmin.users.index')}
                            className="inline-flex justify-center items-center bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] hover:bg-[#f5f0ff] dark:hover:bg-white/5 text-[#333] dark:text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm"
                        >
                            &larr; Kembali
                        </Link>
                    </div>
                </div>

                {/* Edit Role Modal */}
                <dialog ref={roleModalRef} className="m-auto backdrop:bg-black/50 p-6 rounded-2xl shadow-2xl max-w-md w-full border border-[#e5e5e5] dark:border-[#1a1a2e] open:animate-in open:fade-in open:zoom-in-95">
                    <form onSubmit={handleEditRole}>
                        <div className="mb-5">
                            <h3 className="font-bold text-lg text-[#333] dark:text-white mb-1">Edit Role Klien</h3>
                            <p className="text-sm text-[#999] dark:text-white/40">Pilih hak akses baru untuk {user?.name}</p>
                        </div>

                        <div className="mb-6">
                            <label className="block text-sm font-medium text-[#333] dark:text-white mb-2">Role Saat Ini: {user?.role?.replace(/_/g, ' ')}</label>
                            <select
                                value={role}
                                onChange={(e) => setRole(e.target.value)}
                                className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition text-[#333] dark:text-white"
                            >
                                <option value="user_hosting">User Hosting</option>
                                <option value="user_joki">User Joki</option>
                                <option value="admin_hosting">Admin Hosting</option>
                                <option value="admin_joki">Admin Joki</option>
                                <option value="superadmin">Superadmin</option>
                            </select>
                        </div>

                        <div className="flex justify-end gap-3 mt-4">
                            <button type="button" onClick={() => roleModalRef.current?.close()} className="px-4 py-2 text-[#666] dark:text-white/60 hover:text-[#333] dark:hover:text-white bg-white dark:bg-[#1a1a2e] hover:bg-[#f5f0ff] dark:hover:bg-white/5 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg font-medium transition-colors">
                                Batal
                            </button>
                            <button type="submit" className="px-4 py-2 bg-[#7c3aed] text-white rounded-lg font-medium hover:bg-[#6d28d9] shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </dialog>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                    {/* Left Sidebar */}
                    <div className="lg:col-span-1 space-y-6">
                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-6">
                            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                                <div className="flex items-center gap-4 min-w-0">
                                    <div className="w-16 h-16 shrink-0 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa] rounded-full flex items-center justify-center font-black text-3xl shadow-inner border border-[#e5e5e5] dark:border-[#1a1a2e]">
                                        {user?.name?.charAt(0)?.toUpperCase()}
                                    </div>
                                    <div className="min-w-0">
                                        <h2 className="text-lg font-bold text-[#333] dark:text-white leading-tight truncate">{user?.name}</h2>
                                        <p className="text-sm text-[#999] dark:text-white/40 mt-0.5 truncate">{user?.email}</p>
                                    </div>
                                </div>
                                <div className="shrink-0 self-start sm:self-auto">
                                    <RoleBadge role={user?.role} />
                                </div>
                            </div>

                            <div className="border-t border-[#e5e5e5] dark:border-[#1a1a2e] pt-5 text-left space-y-3 text-sm">
                                <div className="flex items-center text-[#666] dark:text-white/60">
                                    <i className="fa-solid fa-calendar-alt w-6 text-center text-[#999] dark:text-white/40"></i>
                                    <span>Terdaftar: <strong className="text-[#333] dark:text-white">{formatDate(user?.created_at)}</strong></span>
                                </div>
                                <div className="flex items-center text-[#666] dark:text-white/60">
                                    <i className="fa-solid fa-clock w-6 text-center text-[#999] dark:text-white/40"></i>
                                    <span>Waktu: <strong className="text-[#333] dark:text-white">{formatTime(user?.created_at)}</strong></span>
                                </div>
                            </div>

                            <div className="flex flex-col gap-3 mt-5 border-t border-[#e5e5e5] dark:border-[#1a1a2e] pt-5">
                                <div className="flex justify-between items-center text-sm">
                                    <span className="text-[#999] dark:text-white/40">Status Akun</span>
                                    <span className={`font-bold px-2 py-0.5 rounded-full text-[10px] uppercase tracking-wider ${user?.status === 'active' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300'}`}>
                                        {user?.status}
                                    </span>
                                </div>
                                <div className="flex justify-between items-center text-sm">
                                    <span className="text-[#999] dark:text-white/40">Paket Hosting</span>
                                    <PlanBadge plan={currentPlan} label={planLabels[currentPlan] || currentPlan} />
                                </div>
                                {currentPlan.toLowerCase() !== 'free' && (
                                    <div className="flex justify-between items-center text-sm">
                                        <span className="text-[#999] dark:text-white/40">Expired Hosting</span>
                                        {activeBilling?.next_due_date ? (
                                            <span className={`font-semibold ${new Date(activeBilling.next_due_date) < new Date() ? 'text-red-500 dark:text-red-400' : 'text-[#333] dark:text-white'}`}>
                                                {formatDate(activeBilling.next_due_date)}
                                            </span>
                                        ) : (
                                            <span className="text-[#999] dark:text-white/40 italic font-semibold">-</span>
                                        )}
                                    </div>
                                )}
                                <div className="flex justify-between items-center text-sm">
                                    <span className="text-[#999] dark:text-white/40">Total Pesanan Joki</span>
                                    <span className="font-semibold text-[#333] dark:text-white">{user?.client_orders_count ?? 0}</span>
                                </div>
                                <div className="flex justify-between items-center text-sm">
                                    <span className="text-[#999] dark:text-white/40">Total Proyek Hosting</span>
                                    <span className="font-semibold text-[#333] dark:text-white">{user?.hosting_projects_count ?? 0}</span>
                                </div>
                                <div className="flex justify-between items-center text-sm">
                                    <span className="text-[#999] dark:text-white/40">Storage Hosting</span>
                                    <span className="font-semibold text-[#333] dark:text-white">{Number(user?.hosting_storage_limit_mb ?? 256).toLocaleString('id-ID')} MB</span>
                                </div>
                                <div className="flex justify-between items-center text-sm">
                                    <span className="text-[#999] dark:text-white/40">IP Terakhir</span>
                                    <span className="font-mono text-xs text-[#666] dark:text-white/60">{user?.last_login_ip || '-'}</span>
                                </div>
                                <div className="flex justify-between items-center text-sm">
                                    <span className="text-[#999] dark:text-white/40">Aktivitas Terakhir</span>
                                    <span className="font-semibold text-[#333] dark:text-white">{user?.last_login_at ? formatDate(user?.last_login_at) : '-'}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right Content */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Joki Orders */}
                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-6">
                            <h3 className="font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">Riwayat Pesanan Joki Klien Ini</h3>

                            {jokiOrders?.length > 0 ? (
                                <div className="space-y-4">
                                    {jokiOrders.map((order) => (
                                        <div key={order.id} className="border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-4 hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                            <div>
                                                <div className="flex items-center gap-2 mb-1">
                                                    <h4 className="font-bold text-[#333] dark:text-white">{order.project_name}</h4>
                                                    <StatusBadge status={order.status} />
                                                </div>
                                                <p className="text-xs text-[#999] dark:text-white/40">Order ID: {order.order_number} | Harga: {formatRp(order.price)}</p>
                                            </div>
                                            <Link
                                                href={route('admin_joki.orders.edit', { hashid: order.hashid })}
                                                className="inline-block text-xs border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 px-4 py-2 rounded-lg hover:bg-[#7c3aed] hover:text-white hover:border-[#7c3aed] transition-all duration-200 font-semibold shadow-sm"
                                            >
                                                Kelola Proyek
                                            </Link>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-8">
                                    <div className="w-16 h-16 bg-[#fafafa] dark:bg-[#0d0d18] rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 dark:text-slate-400 text-2xl">
                                        <i className="fa-solid fa-box-open"></i>
                                    </div>
                                    <p className="text-sm font-medium text-[#999] dark:text-white/40">Klien ini belum pernah membuat pesanan Joki.</p>
                                </div>
                            )}
                        </div>

                        {/* Hosting Projects */}
                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-6">
                            <h3 className="font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">Riwayat Proyek Hosting Klien Ini</h3>

                            {hostingProjects?.length > 0 ? (
                                <div className="space-y-4">
                                    {hostingProjects.map((project) => (
                                        <div key={project.id} className="border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-4 hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                            <div>
                                                <div className="flex items-center gap-2 mb-1">
                                                    <h4 className="font-bold text-[#333] dark:text-white">{project.project_name}</h4>
                                                    <StatusBadge status={project.status} />
                                                </div>
                                                <p className="text-xs text-[#999] dark:text-white/40">
                                                    <i className="fa-solid fa-code mr-1"></i> {project.framework ? project.framework.charAt(0).toUpperCase() + project.framework.slice(1) : '-'}
                                                </p>
                                            </div>
                                            <Link
                                                href={route('admin_hosting.projects')}
                                                className="inline-block text-xs border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 px-4 py-2 rounded-lg hover:bg-[#7c3aed] hover:text-white hover:border-[#7c3aed] transition-all duration-200 font-semibold shadow-sm"
                                            >
                                                Lihat di Hosting
                                            </Link>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-8">
                                    <div className="w-16 h-16 bg-[#fafafa] dark:bg-[#0d0d18] rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 dark:text-slate-400 text-2xl">
                                        <i className="fa-solid fa-server"></i>
                                    </div>
                                    <p className="text-sm font-medium text-[#999] dark:text-white/40">Klien ini belum memiliki proyek hosting.</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
