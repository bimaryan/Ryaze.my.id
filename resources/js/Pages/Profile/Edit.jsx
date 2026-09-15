import { usePage, useForm } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Edit() {
    const { user } = usePage().props;
    const { data, setData, put, processing, errors } = useForm({
        name: user?.name || '',
        email: user?.email || '',
        current_password: '',
        new_password: '',
        new_password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put('/profile');
    };

    const initials = (user?.name || 'U').split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();

    return (
        <DashboardLayout title="Profil Saya">
            <div className="max-w-2xl mx-auto space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Avatar</h2>
                    </div>
                    <div className="p-5 flex items-center gap-5">
                        <div className="w-16 h-16 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa] flex items-center justify-center text-xl font-black shrink-0">
                            {initials}
                        </div>
                        <div>
                            <p className="text-[13px] font-semibold text-[#333] dark:text-white">{user?.name}</p>
                            <p className="text-[12px] text-[#999] dark:text-white/40 mt-0.5">{user?.email}</p>
                            <p className="text-[11px] text-[#999] dark:text-white/30 mt-1">
                                Role: {(user?.role || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}
                            </p>
                        </div>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Informasi Profil</h3>
                        </div>
                        <div className="p-5 space-y-4">
                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Nama</label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                {errors.name && <p className="mt-1 text-[12px] text-red-500">{errors.name}</p>}
                            </div>
                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Email</label>
                                <input
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                {errors.email && <p className="mt-1 text-[12px] text-red-500">{errors.email}</p>}
                            </div>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Ubah Password</h3>
                            <p className="text-[12px] text-[#999] dark:text-white/40 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                        </div>
                        <div className="p-5 space-y-4">
                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Password Saat Ini</label>
                                <input
                                    type="password"
                                    value={data.current_password}
                                    onChange={(e) => setData('current_password', e.target.value)}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                {errors.current_password && <p className="mt-1 text-[12px] text-red-500">{errors.current_password}</p>}
                            </div>
                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Password Baru</label>
                                <input
                                    type="password"
                                    value={data.new_password}
                                    onChange={(e) => setData('new_password', e.target.value)}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                                {errors.new_password && <p className="mt-1 text-[12px] text-red-500">{errors.new_password}</p>}
                            </div>
                            <div>
                                <label className="block text-[13px] font-semibold text-[#333] dark:text-white mb-1.5">Konfirmasi Password Baru</label>
                                <input
                                    type="password"
                                    value={data.new_password_confirmation}
                                    onChange={(e) => setData('new_password_confirmation', e.target.value)}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                />
                            </div>
                        </div>
                    </div>

                    <div className="flex justify-end">
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] disabled:opacity-50 transition-colors"
                        >
                            {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                        </button>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}
