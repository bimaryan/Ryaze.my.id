import DashboardLayout from '../../../Layouts/DashboardLayout';
import { Link } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';

const statusConfig = {
    completed: { text: 'Selesai', color: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300', icon: 'fa-circle-check' },
    building: { text: 'Membangun', color: 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300', icon: 'fa-spinner fa-spin' },
    failed: { text: 'Gagal', color: 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300', icon: 'fa-circle-xmark' },
    pending: { text: 'Antrian', color: 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300', icon: 'fa-clock' },
};

export default function ApkBuilderProgress({ build }) {
    const [logs, setLogs] = useState(build?.log || '');
    const [status, setStatus] = useState(build?.status || 'pending');
    const logRef = useRef(null);
    const intervalRef = useRef(null);

    const statusInfo = statusConfig[status] || statusConfig.pending;

    useEffect(() => {
        function fetchLog() {
            fetch(route('user_hosting.apk.log', build.id))
                .then(r => r.json())
                .then(data => {
                    setLogs(data.log || data.logs || '');
                    if (data.status) setStatus(data.status);
                    if (data.status === 'building' || data.status === 'pending') {
                        intervalRef.current = setTimeout(fetchLog, 2000);
                    }
                })
                .catch(() => {});
        }

        if (status === 'building' || status === 'pending') {
            fetchLog();
        }

        return () => {
            if (intervalRef.current) clearTimeout(intervalRef.current);
        };
    }, [build.id, status]);

    useEffect(() => {
        if (logRef.current) {
            logRef.current.scrollTop = logRef.current.scrollHeight;
        }
    }, [logs]);

    return (
        <DashboardLayout title="Build Progress">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-brands fa-android text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">{build?.app_name || 'Build Progress'}</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Memantau progres build APK Anda.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href={route('user_hosting.apk.index')}
                            className="inline-flex items-center gap-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="mt-6 bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                {/* Terminal Header */}
                <div className="bg-slate-800 px-4 py-2.5 flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="flex gap-1.5">
                            <div className="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                            <div className="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                            <div className="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                        </div>
                        <span className="text-xs text-slate-400 font-mono">{build?.package_name || 'build-terminal'}</span>
                    </div>
                    <span className={`text-[10px] font-bold uppercase tracking-wider ${statusInfo.color} px-2.5 py-1 rounded-full`}>
                        <i className={`fa-solid ${statusInfo.icon} mr-1`}></i>
                        {statusInfo.text}
                    </span>
                </div>

                {/* Terminal Content */}
                <div ref={logRef} className="bg-slate-900 h-[600px] overflow-y-auto p-4 font-mono text-xs">
                    {status === 'building' && (
                        <div className="text-blue-400 mb-2 flex items-center gap-2">
                            <i className="fa-solid fa-spinner fa-spin"></i> Build sedang berlangsung, logs diperbarui otomatis...
                        </div>
                    )}
                    {!logs ? (
                        <div className="text-slate-500 flex items-center gap-2">
                            <i className="fa-solid fa-spinner fa-spin"></i> Menunggu log...
                        </div>
                    ) : (
                        <pre className="text-emerald-400 whitespace-pre-wrap break-all">{logs}</pre>
                    )}
                </div>
            </div>

            {/* Download Section */}
            {status === 'completed' && (
                <div className="mt-6 bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] p-6 text-center">
                    <div className="w-16 h-16 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i className="fa-solid fa-circle-check"></i>
                    </div>
                    <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Build Selesai!</h3>
                    <p className="text-[#999] dark:text-white/40 mb-6 text-sm">APK Anda siap diunduh.</p>
                    <a href={route('user_hosting.apk.download', build.id)}
                        className="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl text-sm font-bold transition shadow-sm">
                        <i className="fa-solid fa-download"></i> Download APK
                    </a>
                </div>
            )}

            {status === 'failed' && (
                <div className="mt-6 bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] p-6 text-center">
                    <div className="w-16 h-16 bg-rose-100 dark:bg-rose-500/20 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i className="fa-solid fa-circle-xmark"></i>
                    </div>
                    <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Build Gagal</h3>
                    <p className="text-[#999] dark:text-white/40 mb-6 text-sm">Terjadi kesalahan saat membangun APK. Periksa log di atas untuk detail.</p>
                    <Link href={route('user_hosting.apk.create')}
                        className="inline-flex items-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                        <i className="fa-solid fa-rotate"></i> Coba Lagi
                    </Link>
                </div>
            )}
        </DashboardLayout>
    );
}
