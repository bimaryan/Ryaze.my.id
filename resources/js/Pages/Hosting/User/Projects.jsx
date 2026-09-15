import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router, Link, usePage } from '@inertiajs/react';

const statusConfig = {
    active: { text: 'text-emerald-600 dark:text-emerald-300', icon: 'fa-circle-check', animate: '' },
    building: { text: 'text-amber-500 dark:text-amber-400', icon: 'fa-spinner fa-spin', animate: 'animate-pulse' },
    unpaid: { text: 'text-rose-500 dark:text-rose-400', icon: 'fa-circle-xmark', animate: '' },
    suspended: { text: 'text-slate-500 dark:text-slate-400', icon: 'fa-circle-xmark', animate: '' },
    error: { text: 'text-rose-500 dark:text-rose-400', icon: 'fa-circle-xmark', animate: '' },
};

const frameworkIcons = {
    html: 'fa-brands fa-html5',
    php: 'fa-brands fa-php',
    laravel: 'fa-brands fa-laravel',
    react: 'fa-brands fa-react',
    nextjs: 'fa-brands fa-node-js',
    python: 'fa-brands fa-python',
    node: 'fa-brands fa-node',
    vue: 'fa-brands fa-vuejs',
};

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

export default function Projects({ projects }) {
    const { gracePeriod } = usePage().props;
    const projectList = Array.isArray(projects) ? projects : (projects?.data || []);

    return (
        <DashboardLayout title="Aplikasi Ter-deploy">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i className="fa-solid fa-box-open text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Aplikasi Ter-deploy</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola semua proyek dan aplikasi yang berjalan di Ryaze.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href={route('user_hosting.create')}
                            className="inline-flex justify-center items-center flex-shrink-0 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            + Deploy Proyek Baru
                        </Link>
                    </div>
                </div>
            </div>

            <div className="mt-6">
                {/* Grace Period Banner */}
                {gracePeriod && (
                    <div className="bg-gradient-to-r from-[#7c3aed] to-purple-600 rounded-2xl shadow-md border-0 p-5 mb-6 text-white flex items-center justify-between relative overflow-hidden">
                        <div className="absolute -right-4 -top-10 opacity-20 transform rotate-12 pointer-events-none">
                            <i className="fa-solid fa-gift text-9xl"></i>
                        </div>
                        <div className="relative z-10 flex items-start gap-4">
                            <div className="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center shrink-0">
                                <i className="fa-solid fa-gift text-xl"></i>
                            </div>
                            <div>
                                <h3 className="font-bold text-lg mb-1">Apresiasi Pengguna Beta!</h3>
                                <p className="text-indigo-100 text-sm leading-relaxed max-w-3xl">
                                    Terima kasih telah menggunakan versi Beta kami! Sebagai bentuk apresiasi, kami telah memberikan Anda <strong>Masa Tenggang (Grace Period) 1 Bulan secara gratis</strong>.
                                    Tagihan Anda berikutnya akan dimulai pada <strong className="text-white">{gracePeriod.next_due_date}</strong>.
                                </p>
                            </div>
                        </div>
                        <Link href={route('user_hosting.billing')}
                            className="relative z-10 shrink-0 bg-white text-[#7c3aed] hover:bg-indigo-50 px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm ml-4 whitespace-nowrap">
                            Cek Tagihan &rarr;
                        </Link>
                    </div>
                )}

                {/* Empty State */}
                {projectList.length === 0 ? (
                    <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] p-12 text-center">
                        <div className="w-16 h-16 bg-slate-100 dark:bg-slate-700/50 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                            <i className="fa-solid fa-box-open"></i>
                        </div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Belum ada aplikasi</h3>
                        <p className="text-[#999] dark:text-white/40 mb-6 text-sm">Mulai deploy aplikasi pertamamu dari repositori GitHub.</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {projectList.map((project) => {
                            const isUpload = project.source_type === 'upload' || (typeof project.repo_source === 'string' && project.repo_source.startsWith('upload:'));
                            const isTemplate = project.source_type === 'template' || (typeof project.repo_source === 'string' && project.repo_source.startsWith('template:'));
                            const status = statusConfig[project.status] || statusConfig.error;
                            const fwIcon = frameworkIcons[project.framework] || 'fa-solid fa-code';
                            const activeDomain = project.domains?.find?.(d => d.ssl_status === 'active');
                            const displayUrl = activeDomain?.domain_name || project.ryaze_domain;

                            return (
                                <div key={project.id} className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed]/30 hover:shadow-md transition-all duration-200 flex flex-col">
                                    <div className="p-5 border-b border-slate-100 dark:border-[#1a1a2e] flex justify-between items-start">
                                        <div className="flex items-center gap-3">
                                            <div className="w-10 h-10 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg flex items-center justify-center bg-[#fafafa] dark:bg-[#0d0d18] shrink-0">
                                                <i className={`${fwIcon} text-xl text-[#333] dark:text-white`}></i>
                                            </div>
                                            <div>
                                                <Link href={route('user_hosting.show', project.hashid)}
                                                    className="font-bold text-[#333] dark:text-white hover:text-[#7c3aed] dark:hover:text-[#a78bfa] text-lg line-clamp-1">
                                                    {project.project_name}
                                                </Link>
                                                <div className="mt-2 text-sm text-[#999] dark:text-white/40 flex items-center">
                                                    <a href={`https://${displayUrl}`} target="_blank" rel="noopener noreferrer"
                                                        className="hover:text-[#7c3aed] dark:hover:text-[#a78bfa] hover:underline transition-colors flex items-center">
                                                        {displayUrl} <i className="fa-solid fa-arrow-up-right-from-square ml-1 text-[10px]"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="p-5 flex-grow">
                                        <div className="flex items-center gap-2 mb-3 text-xs">
                                            {isUpload ? (
                                                <>
                                                    <span className="text-emerald-600 dark:text-emerald-300"><i className="fa-solid fa-file-zipper mr-1"></i> ZIP:</span>
                                                    <span className="font-mono text-[#333] dark:text-white truncate" title={project.repo_source}>
                                                        {project.repo_source?.replace('upload:', '').split('/').pop()}
                                                    </span>
                                                </>
                                            ) : isTemplate ? (
                                                <>
                                                    <span className="text-[#7c3aed] dark:text-[#a78bfa]"><i className="fa-solid fa-wand-magic-sparkles mr-1"></i> Template:</span>
                                                    <span className="font-mono text-[#333] dark:text-white truncate">
                                                        {project.repo_source?.replace('template:', '').replace(/[_-]/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}
                                                    </span>
                                                </>
                                            ) : (
                                                <>
                                                    <span className="text-[#999] dark:text-white/40"><i className="fa-brands fa-github mr-1"></i> Repo:</span>
                                                    <span className="font-mono text-[#333] dark:text-white truncate" title={project.repo_source}>
                                                        {project.repo_source?.replace('https://github.com/', '')}
                                                    </span>
                                                </>
                                            )}
                                        </div>
                                        <div className="flex items-center gap-2 text-xs">
                                            {!isUpload && (
                                                <>
                                                    <span className="text-[#999] dark:text-white/40"><i className="fa-solid fa-code-branch mr-1"></i> Branch:</span>
                                                    <span className="font-mono bg-slate-100 dark:bg-slate-700/50 px-1.5 py-0.5 rounded text-[#333] dark:text-white">{project.branch}</span>
                                                </>
                                            )}
                                        </div>
                                    </div>

                                    <div className="px-5 py-3 bg-[#fafafa] dark:bg-[#0d0d18] border-t border-slate-100 dark:border-[#1a1a2e] rounded-b-xl flex justify-between items-center">
                                        <span className={`inline-flex items-center text-[11px] font-bold uppercase tracking-wider ${status.text} ${status.animate}`}>
                                            <i className={`fa-solid ${status.icon} mr-1.5`}></i>
                                            {project.status}
                                        </span>
                                        <Link href={route('user_hosting.show', project.hashid)}
                                            className="text-xs font-semibold text-[#666] dark:text-white/60 hover:text-[#7c3aed] dark:hover:text-[#a78bfa] transition-colors">
                                            Kelola &rarr;
                                        </Link>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
