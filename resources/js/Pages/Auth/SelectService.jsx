import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function SelectService({ siteName }) {
    const { data, setData, post, processing, errors } = useForm({
        role: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('auth.select_service.store'));
    };

    return (
        <PublicLayout title={`Pilih Layanan - ${siteName}`} withNav={false} withFooter={false} bodyClass="bg-slate-50 dark:bg-slate-900 font-sans antialiased text-slate-900 dark:text-slate-50">
            <Head title={`Pilih Layanan - ${siteName}`} />

            <div className="min-h-screen flex items-center justify-center p-6 bg-slate-50 dark:bg-slate-900 font-sans antialiased text-slate-900 dark:text-slate-50">
                <div className="max-w-md w-full bg-white dark:bg-[#151521] rounded-2xl shadow-xl border border-slate-100 dark:border-white/5 overflow-hidden">
                    <div className="bg-[#7c3aed] px-8 py-10 text-center relative overflow-hidden">
                        <div className="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
                        <div className="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-black/10 blur-xl"></div>
                        <h1 className="text-3xl font-bold text-white tracking-tight relative z-10">Pilih Layanan</h1>
                        <p className="text-white/80 mt-2 text-sm relative z-10">Pilih layanan utama yang ingin Anda gunakan</p>
                    </div>

                    <div className="p-8">
                        <form onSubmit={submit} className="space-y-6">
                            <div>
                                <label className="block text-sm font-medium text-[#7c3aed] dark:text-white mb-2">Pilih Layanan Utama</label>
                                <div className="grid grid-cols-2 gap-4">
                                    <label
                                        className={`relative flex items-center justify-center p-4 border rounded-xl cursor-pointer transition-colors has-[:checked]:border-[#7c3aed] has-[:checked]:bg-[#f5f0ff] dark:has-[:checked]:bg-[#7c3aed]/10 has-[:checked]:ring-1 has-[:checked]:ring-[#7c3aed] ${errors.role ? 'border-red-500 ring-1 ring-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42] bg-white dark:bg-[#0a0a14] hover:bg-[#fafafa] dark:hover:bg-[#1a1a2e]'}`}
                                    >
                                        <input
                                            type="radio"
                                            name="role"
                                            value="user_joki"
                                            checked={data.role === 'user_joki'}
                                            onChange={(e) => setData('role', e.target.value)}
                                            className="peer sr-only"
                                        />
                                        <span className="text-sm font-semibold text-[#666] dark:text-white/60 peer-checked:text-[#7c3aed]">Jasa Joki</span>
                                    </label>

                                    <label
                                        className={`relative flex items-center justify-center p-4 border rounded-xl cursor-pointer transition-colors has-[:checked]:border-[#7c3aed] has-[:checked]:bg-[#f5f0ff] dark:has-[:checked]:bg-[#7c3aed]/10 has-[:checked]:ring-1 has-[:checked]:ring-[#7c3aed] ${errors.role ? 'border-red-500 ring-1 ring-red-500' : 'border-[#e5e5e5] dark:border-[#2d1f42] bg-white dark:bg-[#0a0a14] hover:bg-[#fafafa] dark:hover:bg-[#1a1a2e]'}`}
                                    >
                                        <input
                                            type="radio"
                                            name="role"
                                            value="user_hosting"
                                            checked={data.role === 'user_hosting'}
                                            onChange={(e) => setData('role', e.target.value)}
                                            className="peer sr-only"
                                        />
                                        <span className="text-sm font-semibold text-[#666] dark:text-white/60 peer-checked:text-[#7c3aed]">Beli Hosting</span>
                                    </label>
                                </div>
                                {errors.role && <p className="mt-1 text-sm text-red-500 dark:text-red-400">{errors.role}</p>}
                            </div>

                            <button
                                type="submit"
                                disabled={processing || !data.role}
                                className="w-full bg-[#7c3aed] hover:bg-[#6d28d9] disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 mt-2 flex items-center justify-center gap-2"
                            >
                                Lanjutkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
