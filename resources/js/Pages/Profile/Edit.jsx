import DashboardLayout from '../../Layouts/DashboardLayout';
import { useForm, Link, router, usePage } from '@inertiajs/react';

export default function Edit({ user }) {
    const { flash } = usePage().props;

    const profileForm = useForm({
        name: user?.name || '',
        email: user?.email || '',
    });

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";

    const handleProfileSubmit = (e) => {
        e.preventDefault();
        profileForm.put('/profile');
    };

    const handlePasswordSubmit = (e) => {
        e.preventDefault();
        passwordForm.put('/profile/password', {
            onSuccess: () => passwordForm.reset('current_password', 'password', 'password_confirmation'),
        });
    };

    return (
        <DashboardLayout title="Profil Saya">
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-user text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Profil Saya</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola informasi pribadi dan keamanan akun Anda.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href="/user/hosting/dashboard" className="inline-flex justify-center items-center bg-[#fafafa] dark:bg-white/5 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:bg-[#f5f0ff] dark:hover:bg-white/10 text-[#666] dark:text-white/60 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                    <div className="px-6 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white flex items-center gap-2">
                            <i className="fa-solid fa-id-card text-[#7c3aed] dark:text-[#a78bfa]"></i> Informasi Pribadi
                        </h2>
                    </div>
                    <form onSubmit={handleProfileSubmit} className="p-6 space-y-5">
                        <div>
                            <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Nama Lengkap</label>
                            <input
                                type="text"
                                required
                                value={profileForm.data.name}
                                onChange={(e) => profileForm.setData('name', e.target.value)}
                                className={inputCls}
                            />
                            {profileForm.errors.name && <p className="text-xs text-rose-500 mt-1">{profileForm.errors.name}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Alamat Email</label>
                            <input
                                type="email"
                                required
                                value={profileForm.data.email}
                                onChange={(e) => profileForm.setData('email', e.target.value)}
                                className={inputCls}
                            />
                            {profileForm.errors.email && <p className="text-xs text-rose-500 mt-1">{profileForm.errors.email}</p>}
                        </div>
                        <div className="flex justify-end pt-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <button type="submit" disabled={profileForm.processing} className="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm disabled:opacity-50">
                                {profileForm.processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                            </button>
                        </div>
                    </form>
                </div>

                <div className="space-y-6">
                    <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                        <div className="px-6 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h2 className="text-sm font-bold text-[#333] dark:text-white flex items-center gap-2">
                                <i className="fa-solid fa-shield-halved text-emerald-500 dark:text-emerald-400"></i> Keamanan Akun
                            </h2>
                        </div>
                        <form onSubmit={handlePasswordSubmit} className="p-6 space-y-5">
                            <div>
                                <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Password Saat Ini</label>
                                <input
                                    type="password"
                                    value={passwordForm.data.current_password}
                                    onChange={(e) => passwordForm.setData('current_password', e.target.value)}
                                    className={inputCls}
                                />
                                {passwordForm.errors.current_password && <p className="text-xs text-rose-500 mt-1">{passwordForm.errors.current_password}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Password Baru</label>
                                <input
                                    type="password"
                                    value={passwordForm.data.password}
                                    onChange={(e) => passwordForm.setData('password', e.target.value)}
                                    className={inputCls}
                                />
                                {passwordForm.errors.password && <p className="text-xs text-rose-500 mt-1">{passwordForm.errors.password}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-bold text-[#666] dark:text-white/60 mb-2">Konfirmasi Password</label>
                                <input
                                    type="password"
                                    value={passwordForm.data.password_confirmation}
                                    onChange={(e) => passwordForm.setData('password_confirmation', e.target.value)}
                                    className={inputCls}
                                />
                            </div>
                            <div className="flex justify-end pt-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <button type="submit" disabled={passwordForm.processing} className="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm disabled:opacity-50">
                                    {passwordForm.processing ? 'Memproses...' : 'Ganti Password'}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                        <div className="px-6 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h2 className="text-sm font-bold text-[#333] dark:text-white flex items-center gap-2">
                                <i className="fa-solid fa-lock text-amber-500 dark:text-amber-400"></i> Autentikasi Dua Faktor (2FA)
                            </h2>
                        </div>
                        <div className="p-6">
                            <div className="flex items-center justify-between mb-3">
                                <p className="text-sm text-[#666] dark:text-white/60">Status Keamanan</p>
                                <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-300 border border-amber-200 dark:border-amber-500/40">
                                    Belum Aktif
                                </span>
                            </div>
                            <p className="text-xs text-[#999] dark:text-white/40 mb-4">
                                Tambahkan lapisan keamanan ekstra dengan autentikasi dua faktor. Setiap kali Anda masuk, Anda akan memerlukan kode verifikasi dari aplikasi authenticator.
                            </p>
                            <button className="px-4 py-2 bg-[#fafafa] dark:bg-white/5 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 text-sm font-medium rounded-lg hover:bg-[#f5f0ff] dark:hover:bg-white/10 transition opacity-50 cursor-not-allowed" title="Segera hadir">
                                <i className="fa-solid fa-qrcode me-2"></i>Aktifkan 2FA
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
