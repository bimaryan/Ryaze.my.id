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
        <PublicLayout title={`Pilih Layanan - ${siteName ?? 'Ryaze'}`} withNav={false} withFooter={false} bodyClass="bg-slate-50 dark:bg-[#0A1322] font-sans antialiased">
            <Head title={`Pilih Layanan - ${siteName ?? 'Ryaze'}`}>
                <meta head-key="robots" name="robots" content="noindex, nofollow" />
            </Head>

            <div className="min-h-screen flex items-center justify-center p-4 py-12">
                <div className="w-full max-w-[420px]">

                    {/* Card */}
                    <div className="bg-white dark:bg-[#0D1828] border border-[#E3EAF1] dark:border-[#1B2C44] overflow-hidden">
                        {/* Header */}
                        <div className="bg-[#F0F6FC] dark:bg-[#0F1A2A] border-b border-[#E3EAF1] dark:border-[#1B2C44] px-8 py-6">
                            <p className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] mb-1">ONBOARDING</p>
                            <h1 className="text-xl font-black text-[#0F1A2A] dark:text-white tracking-tight">Pilih Layanan Utama</h1>
                            <p className="text-[#525252] dark:text-[#8A9BAE] text-sm mt-1">Pilih layanan utama yang ingin Anda gunakan</p>
                        </div>

                        {/* Content */}
                        <div className="p-8">
                            <form onSubmit={submit} className="space-y-6">
                                <div>
                                    <div className="grid grid-cols-2 gap-3">
                                        <label className={`relative flex flex-col items-center justify-center gap-3 p-5 border cursor-pointer transition-all has-[:checked]:border-[#1D5B96] has-[:checked]:bg-[#EDF4FB] dark:has-[:checked]:bg-[#0F2035] ${errors.role ? 'border-rose-500' : 'border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322] hover:border-[#C9D5E2] dark:hover:border-[#24384F] hover:bg-slate-50 dark:hover:bg-[#0F1A2A]'}`}>
                                            <input
                                                type="radio"
                                                name="role"
                                                value="user_joki"
                                                checked={data.role === 'user_joki'}
                                                onChange={(e) => setData('role', e.target.value)}
                                                className="peer sr-only"
                                            />
                                            <i className="fa-solid fa-gamepad text-2xl text-[#94A3B4] dark:text-[#3D556E] peer-checked:text-[#1D5B96] dark:peer-checked:text-[#6BA8DC]"></i>
                                            <span className="font-mono text-[11px] uppercase tracking-[0.15em] text-[#737373] dark:text-[#4A7A9B] peer-checked:text-[#1D5B96] dark:peer-checked:text-[#6BA8DC] text-center">Jasa Joki</span>
                                        </label>

                                        <label className={`relative flex flex-col items-center justify-center gap-3 p-5 border cursor-pointer transition-all has-[:checked]:border-[#1D5B96] has-[:checked]:bg-[#EDF4FB] dark:has-[:checked]:bg-[#0F2035] ${errors.role ? 'border-rose-500' : 'border-[#E3EAF1] dark:border-[#1B2C44] bg-white dark:bg-[#0A1322] hover:border-[#C9D5E2] dark:hover:border-[#24384F] hover:bg-slate-50 dark:hover:bg-[#0F1A2A]'}`}>
                                            <input
                                                type="radio"
                                                name="role"
                                                value="user_hosting"
                                                checked={data.role === 'user_hosting'}
                                                onChange={(e) => setData('role', e.target.value)}
                                                className="peer sr-only"
                                            />
                                            <i className="fa-solid fa-server text-2xl text-[#94A3B4] dark:text-[#3D556E] peer-checked:text-[#1D5B96] dark:peer-checked:text-[#6BA8DC]"></i>
                                            <span className="font-mono text-[11px] uppercase tracking-[0.15em] text-[#737373] dark:text-[#4A7A9B] peer-checked:text-[#1D5B96] dark:peer-checked:text-[#6BA8DC] text-center">Beli Hosting</span>
                                        </label>
                                    </div>
                                    {errors.role && <p className="mt-2 text-xs text-rose-600 dark:text-rose-400">{errors.role}</p>}
                                </div>

                                <button
                                    type="submit"
                                    disabled={processing || !data.role}
                                    className="w-full bg-[#1D5B96] text-white font-mono text-[11px] uppercase tracking-[0.2em] py-3.5 px-4 hover:bg-[#2568A8] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2.5 transition-colors"
                                >
                                    {processing ? (
                                        <>
                                            <i className="fa-solid fa-spinner fa-spin text-sm"></i>
                                            <span>Menyimpan...</span>
                                        </>
                                    ) : (
                                        <>
                                            <span>Lanjutkan</span>
                                            <i className="fa-solid fa-arrow-right text-xs"></i>
                                        </>
                                    )}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
