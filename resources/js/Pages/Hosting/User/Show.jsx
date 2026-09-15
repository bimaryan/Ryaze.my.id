import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, usePage, router, Link } from '@inertiajs/react';
import { useState, useEffect, useRef, useCallback } from 'react';
import Swal from 'sweetalert2';

const statusConfig = {
    active: { text: 'text-emerald-600 dark:text-emerald-300', bg: 'bg-emerald-100 dark:bg-emerald-500/20', icon: 'fa-circle-check', label: 'Active' },
    building: { text: 'text-amber-500 dark:text-amber-400', bg: 'bg-amber-100 dark:bg-amber-500/20', icon: 'fa-spinner fa-spin', label: 'Building' },
    unpaid: { text: 'text-rose-500 dark:text-rose-400', bg: 'bg-rose-100 dark:bg-rose-500/20', icon: 'fa-circle-xmark', label: 'Unpaid' },
    suspended: { text: 'text-slate-500 dark:text-slate-400', bg: 'bg-slate-100 dark:bg-slate-700/50', icon: 'fa-circle-xmark', label: 'Suspended' },
    error: { text: 'text-rose-500 dark:text-rose-400', bg: 'bg-rose-100 dark:bg-rose-500/20', icon: 'fa-circle-xmark', label: 'Error' },
};

const frameworkIcons = {
    html: { icon: 'fa-brands fa-html5', color: 'text-orange-500 dark:text-orange-400' },
    php: { icon: 'fa-brands fa-php', color: 'text-indigo-500 dark:text-indigo-400' },
    laravel: { icon: 'fa-brands fa-laravel', color: 'text-red-500 dark:text-red-400' },
    react: { icon: 'fa-brands fa-react', color: 'text-sky-500 dark:text-sky-400' },
    nextjs: { icon: 'fa-brands fa-node-js', color: 'text-slate-800 dark:text-slate-100' },
    python: { icon: 'fa-brands fa-python', color: 'text-yellow-500 dark:text-yellow-400' },
    node: { icon: 'fa-brands fa-node', color: 'text-emerald-500 dark:text-emerald-400' },
    vue: { icon: 'fa-brands fa-vuejs', color: 'text-emerald-500 dark:text-emerald-400' },
};

const tabs = [
    { key: 'overview', label: 'Overview', icon: 'fa-solid fa-chart-simple' },
    { key: 'build-logs', label: 'Build Logs', icon: 'fa-solid fa-scroll' },
    { key: 'terminal', label: 'Terminal', icon: 'fa-solid fa-terminal' },
    { key: 'files', label: 'Root Files', icon: 'fa-solid fa-folder-tree' },
    { key: 'env', label: '.env', icon: 'fa-solid fa-file-code' },
    { key: 'settings', label: 'Settings', icon: 'fa-solid fa-gear' },
    { key: 'crons', label: 'Cron Jobs', icon: 'fa-solid fa-clock-rotate-left' },
    { key: 'team', label: 'Team Access', icon: 'fa-solid fa-users' },
];

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

function formatRupiah(amount) {
    return `Rp${Number(amount || 0).toLocaleString('id-ID')}`;
}

const exampleNginx = `server {
    listen 80;
    server_name myapp.ryaze.my.id;
    root /var/www/myapp/public;

    index index.html index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \\.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}`;

export default function Show({ project, envContent = '', wafContent = '', diskUsage = null, visitorsCount = 0, projectEmails = [] }) {
    const { flash, auth } = usePage().props;
    const [activeTab, setActiveTab] = useState(() => {
        if (typeof window !== 'undefined') {
            const hash = window.location.hash.replace('#', '');
            return tabs.find(t => t.key === hash) ? hash : 'overview';
        }
        return 'overview';
    });

    const status = statusConfig[project?.status] || statusConfig.error;
    const fwInfo = frameworkIcons[project?.framework] || { icon: 'fa-solid fa-code', color: 'text-slate-500 dark:text-slate-400' };
    const activeDomain = project?.domains?.find?.(d => d.ssl_status === 'active');
    const displayUrl = activeDomain?.domain_name || project?.ryaze_domain;

    useEffect(() => {
        window.location.hash = activeTab;
    }, [activeTab]);

    function handleTabChange(key) {
        setActiveTab(key);
    }

    return (
        <DashboardLayout title={project?.project_name || 'Project Detail'}>
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className={`${fwInfo.icon} text-xl ${fwInfo.color}`}></i>
                    </div>
                    <div>
                        <div className="flex items-center gap-2">
                            <h1 className="text-lg font-bold text-[#333] dark:text-white">{project?.project_name}</h1>
                            <span className={`text-[11px] font-bold uppercase tracking-wider ${status.text} ${status.bg} px-2 py-0.5 rounded-full`}>
                                <i className={`fa-solid ${status.icon} mr-1`}></i>
                                {status.label}
                            </span>
                        </div>
                        <p className="text-[13px] text-[#999] dark:text-white/40">{displayUrl}</p>
                    </div>
                    <div className="ml-auto flex items-center gap-2">
                        <a href={`https://${displayUrl}`} target="_blank" rel="noopener noreferrer"
                            className="inline-flex items-center gap-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                            <i className="fa-solid fa-arrow-up-right-from-square text-xs"></i> Buka Website
                        </a>
                        <Link href={route('user_hosting.projects')}
                            className="inline-flex items-center gap-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            {/* Flash Messages */}
            {flash?.success && (
                <div className="mt-4 px-4 py-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-300 text-sm rounded-lg flex items-center gap-2">
                    <i className="fa-solid fa-circle-check"></i> {flash.success}
                </div>
            )}
            {flash?.error && (
                <div className="mt-4 px-4 py-3 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 text-sm rounded-lg flex items-center gap-2">
                    <i className="fa-solid fa-circle-xmark"></i> {flash.error}
                </div>
            )}

            {/* Tab Navigation */}
            <div className="mt-6 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="border-b border-[#e5e5e5] dark:border-[#1a1a2e] overflow-x-auto">
                    <div className="flex">
                        {tabs.map(tab => (
                            <button
                                key={tab.key}
                                onClick={() => handleTabChange(tab.key)}
                                className={`flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap transition-colors border-b-2 ${
                                    activeTab === tab.key
                                        ? 'border-[#7c3aed] text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10'
                                        : 'border-transparent text-[#999] dark:text-white/40 hover:text-[#7c3aed] dark:hover:text-white hover:bg-[#fafafa] dark:hover:bg-white/[0.02]'
                                }`}
                            >
                                <i className={`${tab.icon} text-xs`}></i>
                                {tab.label}
                            </button>
                        ))}
                    </div>
                </div>

                <div className="p-6">
                    {activeTab === 'overview' && <OverviewTab project={project} displayUrl={displayUrl} diskUsage={diskUsage} visitorsCount={visitorsCount} fwInfo={fwInfo} status={status} />}
                    {activeTab === 'build-logs' && <BuildLogsTab project={project} />}
                    {activeTab === 'terminal' && <TerminalTab project={project} />}
                    {activeTab === 'files' && <FilesTab project={project} />}
                    {activeTab === 'env' && <EnvTab project={project} envContent={envContent} />}
                    {activeTab === 'settings' && <SettingsTab project={project} />}
                    {activeTab === 'crons' && <CronsTab project={project} />}
                    {activeTab === 'team' && <TeamTab project={project} projectEmails={projectEmails} />}
                </div>
            </div>
        </DashboardLayout>
    );
}

function OverviewTab({ project, displayUrl, diskUsage, visitorsCount, fwInfo, status }) {
    const [showNginxExample, setShowNginxExample] = useState(false);
    const { data: nginxData, setData: setNginxData, post: postNginx, processing: nginxProcessing } = useForm({
        nginx_config: project?.nginx_config || '',
    });

    function handleNginxSave(e) {
        e.preventDefault();
        postNginx(route('user_hosting.nginx.update', { hashid: project.hashid }));
    }

    function handleNginxReset() {
        Swal.fire({
            title: 'Reset Nginx Config?',
            text: 'Konfigurasi Nginx akan dikembalikan ke default.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset!',
        }).then((result) => {
            if (result.isConfirmed) {
                router.post(route('user_hosting.nginx.reset', { hashid: project.hashid }));
            }
        });
    }

    function handleRedeploy() {
        Swal.fire({
            title: 'Redeploy Project?',
            text: 'Project akan di-build ulang dari source.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            confirmButtonText: 'Ya, Redeploy!',
        }).then((result) => {
            if (result.isConfirmed) {
                router.post(route('user_hosting.redeploy', { hashid: project.hashid }));
            }
        });
    }

    return (
        <div className="space-y-6">
            {/* Live Preview */}
            {project?.status === 'active' && (
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl overflow-hidden">
                    <div className="px-5 py-3 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <h3 className="font-bold text-[#333] dark:text-white text-sm flex items-center gap-2">
                            <i className="fa-solid fa-globe text-[#7c3aed]"></i> Live Preview
                        </h3>
                        <a href={`https://${displayUrl}`} target="_blank" rel="noopener noreferrer"
                            className="text-xs font-semibold text-[#7c3aed] dark:text-[#a78bfa] hover:underline flex items-center gap-1">
                            Buka di tab baru <i className="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>
                    <div className="relative" style={{ paddingBottom: '56.25%' }}>
                        <iframe
                            src={`https://${displayUrl}`}
                            className="absolute inset-0 w-full h-full border-0"
                            loading="lazy"
                            sandbox="allow-scripts allow-same-origin allow-forms allow-popups"
                            title="Live Preview"
                        />
                    </div>
                </div>
            )}

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Deployment Details */}
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5">
                    <h3 className="font-bold text-[#333] dark:text-white text-sm mb-4 flex items-center gap-2">
                        <i className="fa-solid fa-info-circle text-[#7c3aed]"></i> Deployment Details
                    </h3>
                    <div className="space-y-3">
                        <div className="flex justify-between items-center py-2 border-b border-slate-100 dark:border-[#1a1a2e]">
                            <span className="text-xs text-[#999] dark:text-white/40 font-medium">Source</span>
                            <span className="text-sm text-[#333] dark:text-white font-medium truncate max-w-[200px]" title={project?.repo_source}>
                                {project?.source_type === 'upload' ? 'ZIP Upload' : project?.source_type === 'template' ? 'Template' : project?.repo_source?.replace('https://github.com/', '')}
                            </span>
                        </div>
                        <div className="flex justify-between items-center py-2 border-b border-slate-100 dark:border-[#1a1a2e]">
                            <span className="text-xs text-[#999] dark:text-white/40 font-medium">Branch</span>
                            <span className="text-sm text-[#333] dark:text-white font-mono bg-slate-100 dark:bg-slate-700/50 px-2 py-0.5 rounded">{project?.branch || '-'}</span>
                        </div>
                        <div className="flex justify-between items-center py-2 border-b border-slate-100 dark:border-[#1a1a2e]">
                            <span className="text-xs text-[#999] dark:text-white/40 font-medium">Framework</span>
                            <span className="text-sm text-[#333] dark:text-white font-medium flex items-center gap-1.5">
                                <i className={`${fwInfo.icon} ${fwInfo.color}`}></i>
                                {project?.framework?.toUpperCase()}
                            </span>
                        </div>
                        <div className="flex justify-between items-center py-2 border-b border-slate-100 dark:border-[#1a1a2e]">
                            <span className="text-xs text-[#999] dark:text-white/40 font-medium">Root Directory</span>
                            <span className="text-sm text-[#333] dark:text-white font-mono bg-slate-100 dark:bg-slate-700/50 px-2 py-0.5 rounded">{project?.root_directory || '/'}</span>
                        </div>
                        <div className="flex justify-between items-center py-2">
                            <span className="text-xs text-[#999] dark:text-white/40 font-medium">Created</span>
                            <span className="text-sm text-[#333] dark:text-white">{formatDate(project?.created_at)}</span>
                        </div>
                    </div>
                    <button onClick={handleRedeploy}
                        className="mt-4 w-full bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-bold py-2.5 rounded-lg text-sm transition flex items-center justify-center gap-2 shadow-sm">
                        <i className="fa-solid fa-rotate"></i> Redeploy
                    </button>
                </div>

                {/* QR Code */}
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5">
                    <h3 className="font-bold text-[#333] dark:text-white text-sm mb-4 flex items-center gap-2">
                        <i className="fa-solid fa-qrcode text-[#7c3aed]"></i> QR Code
                    </h3>
                    <div className="flex flex-col items-center justify-center py-4">
                        <div className="bg-white p-3 rounded-xl shadow-sm border border-slate-200 mb-3">
                            <img
                                src={`https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://${displayUrl}`}
                                alt="QR Code"
                                className="w-36 h-36"
                            />
                        </div>
                        <p className="text-xs text-[#999] dark:text-white/40 text-center">Scan untuk membuka website</p>
                        <p className="text-xs text-[#7c3aed] dark:text-[#a78bfa] font-mono mt-1">{displayUrl}</p>
                    </div>
                </div>
            </div>

            {/* Nginx Config */}
            <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl overflow-hidden">
                <div className="px-5 py-3 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                    <h3 className="font-bold text-[#333] dark:text-white text-sm flex items-center gap-2">
                        <i className="fa-solid fa-network-wired text-[#7c3aed]"></i> Nginx Configuration
                    </h3>
                    <span className={`text-[11px] font-bold px-2 py-0.5 rounded-full ${project?.nginx_status === 'active' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300'}`}>
                        {project?.nginx_status === 'active' ? 'Applied' : 'Default'}
                    </span>
                </div>
                <div className="p-5">
                    <form onSubmit={handleNginxSave}>
                        <textarea
                            value={nginxData.nginx_config}
                            onChange={e => setNginxData('nginx_config', e.target.value)}
                            className="w-full h-48 bg-slate-900 text-emerald-400 font-mono text-xs p-4 rounded-lg border border-slate-700 focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none resize-none"
                            placeholder="Masukkan konfigurasi Nginx..."
                        />
                        <div className="flex items-center justify-between mt-3">
                            <button type="button" onClick={() => setShowNginxExample(!showNginxExample)}
                                className="text-xs text-[#7c3aed] dark:text-[#a78bfa] hover:underline font-medium flex items-center gap-1">
                                <i className="fa-solid fa-lightbulb"></i> {showNginxExample ? 'Sembunyikan' : 'Lihat'} Contoh Konfigurasi
                            </button>
                            <div className="flex gap-2">
                                <button type="button" onClick={handleNginxReset}
                                    className="px-4 py-2 text-sm font-medium text-rose-500 bg-rose-50 dark:bg-rose-500/10 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-500/20 transition">
                                    Restore Default
                                </button>
                                <button type="submit" disabled={nginxProcessing}
                                    className="px-4 py-2 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition disabled:opacity-50">
                                    {nginxProcessing ? 'Menyimpan...' : 'Save & Apply'}
                                </button>
                            </div>
                        </div>
                    </form>
                    {showNginxExample && (
                        <div className="mt-4 bg-slate-900 text-slate-300 font-mono text-xs p-4 rounded-lg border border-slate-700 overflow-x-auto">
                            <pre className="whitespace-pre-wrap">{exampleNginx}</pre>
                        </div>
                    )}
                </div>
            </div>

            {/* Resource Monitoring */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5">
                    <h3 className="font-bold text-[#333] dark:text-white text-sm mb-3 flex items-center gap-2">
                        <i className="fa-solid fa-hard-drive text-[#7c3aed]"></i> Disk Usage
                    </h3>
                    <div className="mb-2 flex items-end justify-between">
                        <span className="text-2xl font-black text-[#333] dark:text-white">{diskUsage || '0 MB'}</span>
                    </div>
                    <div className="w-full bg-slate-100 dark:bg-slate-700/50 rounded-full h-2.5">
                        <div className="bg-[#7c3aed] h-2.5 rounded-full transition-all" style={{ width: `${Math.min((parseFloat(diskUsage) / 1024) * 100, 100)}%` }}></div>
                    </div>
                    <p className="text-[11px] text-[#999] dark:text-white/40 mt-1.5">dari kuota tersedia</p>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5">
                    <h3 className="font-bold text-[#333] dark:text-white text-sm mb-3 flex items-center gap-2">
                        <i className="fa-solid fa-chart-line text-[#7c3aed]"></i> Visitor Count
                    </h3>
                    <div className="flex items-end justify-between">
                        <span className="text-2xl font-black text-[#333] dark:text-white">{visitorsCount || 0}</span>
                        <span className="text-xs text-[#999] dark:text-white/40">30 hari terakhir</span>
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5">
                    <h3 className="font-bold text-[#333] dark:text-white text-sm mb-3 flex items-center gap-2">
                        <i className="fa-solid fa-gauge-high text-[#7c3aed]"></i> CPU / RAM
                    </h3>
                    <div className="flex items-center justify-center h-20 text-[#999] dark:text-white/30">
                        <div className="text-center">
                            <i className="fa-solid fa-chart-area text-2xl mb-1 block"></i>
                            <p className="text-xs">Chart monitoring</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

function BuildLogsTab({ project }) {
    const [logs, setLogs] = useState('');
    const [loading, setLoading] = useState(true);
    const logRef = useRef(null);
    const intervalRef = useRef(null);

    const fetchLogs = useCallback(() => {
        fetch(route('user_hosting.build_logs', { hashid: project.hashid }))
            .then(r => r.json())
            .then(data => {
                setLogs(data.logs || data.message || 'No logs available.');
                setLoading(false);
                if (data.status === 'building') {
                    intervalRef.current = setTimeout(fetchLogs, 3000);
                }
            })
            .catch(() => {
                setLogs('Gagal memuat logs.');
                setLoading(false);
            });
    }, [project.hashid]);

    useEffect(() => {
        fetchLogs();
        return () => {
            if (intervalRef.current) clearTimeout(intervalRef.current);
        };
    }, [fetchLogs]);

    useEffect(() => {
        if (logRef.current) {
            logRef.current.scrollTop = logRef.current.scrollHeight;
        }
    }, [logs]);

    return (
        <div>
            <div className="flex items-center justify-between mb-4">
                <h3 className="font-bold text-[#333] dark:text-white text-sm flex items-center gap-2">
                    <i className="fa-solid fa-scroll text-[#7c3aed]"></i> Build Logs
                </h3>
                <button onClick={() => { setLoading(true); fetchLogs(); }}
                    className="px-3 py-1.5 text-xs font-medium text-[#7c3aed] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 rounded-lg hover:bg-[#ede4ff] dark:hover:bg-[#7c3aed]/20 transition">
                    <i className="fa-solid fa-rotate mr-1"></i> Refresh
                </button>
            </div>
            {project?.status === 'building' && (
                <div className="mb-4 px-4 py-2 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-amber-700 dark:text-amber-300 text-xs rounded-lg flex items-center gap-2">
                    <i className="fa-solid fa-spinner fa-spin"></i> Build sedang berlangsung, logs diperbarui otomatis...
                </div>
            )}
            <div ref={logRef} className="bg-slate-900 text-slate-300 font-mono text-xs p-4 rounded-lg overflow-auto max-h-[500px] border border-slate-700">
                {loading ? (
                    <div className="text-slate-500 flex items-center gap-2">
                        <i className="fa-solid fa-spinner fa-spin"></i> Memuat logs...
                    </div>
                ) : (
                    <pre className="whitespace-pre-wrap break-all">{logs}</pre>
                )}
            </div>
        </div>
    );
}

function TerminalTab({ project }) {
    const [input, setInput] = useState('');
    const [history, setHistory] = useState([]);
    const [output, setOutput] = useState('');
    const [processing, setProcessing] = useState(false);
    const outputRef = useRef(null);

    function handleRun(e) {
        e.preventDefault();
        if (!input.trim() || processing) return;
        const cmd = input.trim();
        setInput('');
        setHistory(prev => [...prev, cmd]);
        setProcessing(true);
        setOutput(prev => prev + `\n$ ${cmd}\n`);

        router.post(route('user_hosting.terminal', { hashid: project.hashid }), { command: cmd }, {
            preserveState: true,
            onSuccess: (page) => {
                const result = page.props.flash?.terminalOutput || page.props.terminalOutput || '';
                setOutput(prev => prev + result + '\n');
                setProcessing(false);
            },
            onError: () => {
                setOutput(prev => prev + 'Error executing command.\n');
                setProcessing(false);
            },
        });
    }

    useEffect(() => {
        if (outputRef.current) {
            outputRef.current.scrollTop = outputRef.current.scrollHeight;
        }
    }, [output]);

    return (
        <div>
            <h3 className="font-bold text-[#333] dark:text-white text-sm mb-4 flex items-center gap-2">
                <i className="fa-solid fa-terminal text-[#7c3aed]"></i> Web Terminal
            </h3>
            <div className="bg-slate-900 rounded-lg border border-slate-700 overflow-hidden">
                <div className="flex gap-1.5 px-4 py-2.5 bg-slate-800 border-b border-slate-700">
                    <div className="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                    <div className="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                    <div className="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                    <span className="ml-2 text-xs text-slate-400 font-mono">{project?.project_name}</span>
                </div>
                <div ref={outputRef} className="h-80 overflow-y-auto p-4 font-mono text-xs text-slate-300">
                    <div className="text-emerald-400 mb-2">Welcome to Ryaze Web Terminal</div>
                    <div className="text-slate-500 mb-3">Ketik perintah dan tekan Enter atau klik Run.</div>
                    <pre className="whitespace-pre-wrap break-all">{output}</pre>
                </div>
                <form onSubmit={handleRun} className="flex border-t border-slate-700">
                    <span className="flex items-center pl-4 pr-2 text-emerald-400 font-mono text-xs">$</span>
                    <input
                        type="text"
                        value={input}
                        onChange={e => setInput(e.target.value)}
                        disabled={processing}
                        placeholder="Ketik perintah..."
                        className="flex-1 bg-transparent text-slate-200 font-mono text-xs py-3 pr-4 outline-none placeholder-slate-500 disabled:opacity-50"
                        autoFocus
                    />
                    <button type="submit" disabled={processing || !input.trim()}
                        className="px-5 py-3 bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-xs font-bold transition disabled:opacity-50">
                        {processing ? <i className="fa-solid fa-spinner fa-spin"></i> : <i className="fa-solid fa-play"></i>}
                    </button>
                </form>
            </div>
        </div>
    );
}

function FilesTab({ project }) {
    const [files, setFiles] = useState([]);
    const [loading, setLoading] = useState(true);
    const [currentPath, setCurrentPath] = useState('/');
    const [editorOpen, setEditorOpen] = useState(false);
    const [editorFile, setEditorFile] = useState(null);
    const [editorContent, setEditorContent] = useState('');
    const [saving, setSaving] = useState(false);
    const [createModal, setCreateModal] = useState(false);
    const [createType, setCreateType] = useState('file');
    const [createName, setCreateName] = useState('');
    const fileInputRef = useRef(null);

    function fetchFiles(path = '/') {
        setLoading(true);
        fetch(route('user_hosting.files', { hashid: project.hashid }) + '?path=' + encodeURIComponent(path))
            .then(r => r.json())
            .then(data => {
                setFiles(data.files || data || []);
                setCurrentPath(path);
                setLoading(false);
            })
            .catch(() => {
                setFiles([]);
                setLoading(false);
            });
    }

    useEffect(() => { fetchFiles(); }, [project.hashid]);

    function openFile(file) {
        fetch(route('user_hosting.files.read', { hashid: project.hashid }) + '?path=' + encodeURIComponent(currentPath + '/' + file.name))
            .then(r => r.json())
            .then(data => {
                setEditorFile(file);
                setEditorContent(data.content || '');
                setEditorOpen(true);
            });
    }

    function saveFile() {
        setSaving(true);
        router.post(route('user_hosting.files.save', { hashid: project.hashid }), {
            path: currentPath + '/' + editorFile.name,
            content: editorContent,
        }, {
            preserveState: true,
            onFinish: () => {
                setSaving(false);
                setEditorOpen(false);
            },
        });
    }

    function createItem(e) {
        e.preventDefault();
        if (!createName.trim()) return;
        router.post(route('user_hosting.files.create', { hashid: project.hashid }), {
            type: createType,
            name: createName,
            path: currentPath,
        }, {
            preserveState: true,
            onFinish: () => {
                setCreateModal(false);
                setCreateName('');
                fetchFiles(currentPath);
            },
        });
    }

    function deleteItem(file) {
        Swal.fire({
            title: `Hapus ${file.name}?`,
            text: 'Tindakan ini tidak dapat dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
        }).then((result) => {
            if (result.isConfirmed) {
                router.post(route('user_hosting.files.delete', { hashid: project.hashid }), {
                    path: currentPath + '/' + file.name,
                }, {
                    preserveState: true,
                    onFinish: () => fetchFiles(currentPath),
                });
            }
        });
    }

    function handleUpload(e) {
        const file = e.target.files?.[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('file', file);
        formData.append('path', currentPath);
        router.post(route('user_hosting.files.upload', { hashid: project.hashid }), formData, {
            forceFormData: true,
            preserveState: true,
            onFinish: () => {
                fetchFiles(currentPath);
                if (fileInputRef.current) fileInputRef.current.value = '';
            },
        });
    }

    function navigateTo(name) {
        fetchFiles(currentPath === '/' ? '/' + name : currentPath + '/' + name);
    }

    function goUp() {
        const parts = currentPath.split('/').filter(Boolean);
        parts.pop();
        fetchFiles('/' + parts.join('/'));
    }

    function formatSize(bytes) {
        if (!bytes || bytes === 0) return '-';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    return (
        <div>
            <div className="flex items-center justify-between mb-4">
                <h3 className="font-bold text-[#333] dark:text-white text-sm flex items-center gap-2">
                    <i className="fa-solid fa-folder-tree text-[#7c3aed]"></i> File Manager
                </h3>
                <div className="flex items-center gap-2">
                    <button onClick={() => { setCreateType('file'); setCreateModal(true); }}
                        className="px-3 py-1.5 text-xs font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition">
                        <i className="fa-solid fa-file-circle-plus mr-1"></i> New File
                    </button>
                    <button onClick={() => { setCreateType('folder'); setCreateModal(true); }}
                        className="px-3 py-1.5 text-xs font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">
                        <i className="fa-solid fa-folder-plus mr-1"></i> New Folder
                    </button>
                    <button onClick={() => fileInputRef.current?.click()}
                        className="px-3 py-1.5 text-xs font-medium text-white bg-slate-600 rounded-lg hover:bg-slate-700 transition">
                        <i className="fa-solid fa-upload mr-1"></i> Upload
                    </button>
                    <input type="file" ref={fileInputRef} onChange={handleUpload} className="hidden" />
                    <button onClick={() => fetchFiles(currentPath)}
                        className="px-3 py-1.5 text-xs font-medium text-[#666] dark:text-white/60 bg-slate-100 dark:bg-slate-700/50 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                        <i className="fa-solid fa-rotate"></i>
                    </button>
                </div>
            </div>

            {/* Breadcrumb */}
            <div className="flex items-center gap-1 text-xs mb-3 text-[#999] dark:text-white/40">
                <button onClick={() => fetchFiles('/')} className="hover:text-[#7c3aed] transition font-medium">root</button>
                {currentPath.split('/').filter(Boolean).map((part, i, arr) => (
                    <span key={i} className="flex items-center gap-1">
                        <i className="fa-solid fa-chevron-right text-[8px]"></i>
                        <button
                            onClick={() => fetchFiles('/' + arr.slice(0, i + 1).join('/'))}
                            className="hover:text-[#7c3aed] transition font-medium"
                        >{part}</button>
                    </span>
                ))}
            </div>

            {currentPath !== '/' && (
                <button onClick={goUp} className="mb-3 text-xs text-[#7c3aed] dark:text-[#a78bfa] hover:underline font-medium flex items-center gap-1">
                    <i className="fa-solid fa-arrow-up"></i> Kembali ke parent
                </button>
            )}

            {/* File Table */}
            <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-5 py-3">Name</th>
                                <th className="px-5 py-3">Size</th>
                                <th className="px-5 py-3">Modified</th>
                                <th className="px-5 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {loading ? (
                                <tr><td colSpan="4" className="px-5 py-8 text-center text-[#999] dark:text-white/40"><i className="fa-solid fa-spinner fa-spin mr-2"></i>Memuat...</td></tr>
                            ) : files.length === 0 ? (
                                <tr><td colSpan="4" className="px-5 py-8 text-center text-[#999] dark:text-white/40">Direktori kosong</td></tr>
                            ) : files.map(file => (
                                <tr key={file.name} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-5 py-3">
                                        {file.is_dir ? (
                                            <button onClick={() => navigateTo(file.name)} className="flex items-center gap-2 text-[#7c3aed] dark:text-[#a78bfa] hover:underline font-medium">
                                                <i className="fa-solid fa-folder text-amber-400"></i> {file.name}
                                            </button>
                                        ) : (
                                            <button onClick={() => openFile(file)} className="flex items-center gap-2 text-[#333] dark:text-white hover:text-[#7c3aed] dark:hover:text-[#a78bfa] font-medium">
                                                <i className="fa-solid fa-file text-slate-400"></i> {file.name}
                                            </button>
                                        )}
                                    </td>
                                    <td className="px-5 py-3 text-xs text-[#999] dark:text-white/40">{file.is_dir ? '-' : formatSize(file.size)}</td>
                                    <td className="px-5 py-3 text-xs text-[#999] dark:text-white/40">{formatDate(file.modified)}</td>
                                    <td className="px-5 py-3 text-center">
                                        {!file.is_dir && (
                                            <button onClick={() => openFile(file)} className="w-7 h-7 rounded-md flex items-center justify-center text-[#7c3aed] dark:text-[#a78bfa] hover:bg-[#f5f0ff] dark:hover:bg-[#7c3aed]/10 transition" title="Edit">
                                                <i className="fa-solid fa-pen-to-square text-xs"></i>
                                            </button>
                                        )}
                                        <button onClick={() => deleteItem(file)} className="w-7 h-7 rounded-md flex items-center justify-center text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition ml-1" title="Hapus">
                                            <i className="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Create Modal */}
            {createModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div className="w-full max-w-sm bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl">
                        <div className="p-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm">
                                {createType === 'file' ? 'Buat File Baru' : 'Buat Folder Baru'}
                            </h3>
                            <button onClick={() => setCreateModal(false)} className="text-[#999] hover:text-red-500 transition">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <form onSubmit={createItem} className="p-5">
                            <input type="text" value={createName} onChange={e => setCreateName(e.target.value)}
                                placeholder={createType === 'file' ? 'filename.txt' : 'folder-name'} required autoFocus
                                className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                            <div className="flex justify-end gap-3 mt-4">
                                <button type="button" onClick={() => setCreateModal(false)}
                                    className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg hover:bg-[#fafafa] transition">
                                    Batal
                                </button>
                                <button type="submit"
                                    className="px-4 py-2 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition">
                                    Buat
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Editor Modal */}
            {editorOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div className="w-full max-w-4xl max-h-[85vh] bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl flex flex-col">
                        <div className="p-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between shrink-0">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm flex items-center gap-2">
                                <i className="fa-solid fa-file-pen text-[#7c3aed]"></i> {editorFile?.name}
                            </h3>
                            <button onClick={() => setEditorOpen(false)} className="text-[#999] hover:text-red-500 transition">
                                <i className="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <textarea
                            value={editorContent}
                            onChange={e => setEditorContent(e.target.value)}
                            className="flex-1 min-h-[400px] bg-slate-900 text-emerald-400 font-mono text-xs p-4 border-0 outline-none resize-none"
                            spellCheck={false}
                        />
                        <div className="p-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-end gap-3 shrink-0">
                            <button onClick={() => setEditorOpen(false)}
                                className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg hover:bg-[#fafafa] transition">
                                Batal
                            </button>
                            <button onClick={saveFile} disabled={saving}
                                className="px-5 py-2 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition disabled:opacity-50">
                                {saving ? 'Menyimpan...' : 'Simpan'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

function EnvTab({ project, envContent }) {
    const { data, setData, post, processing } = useForm({
        env_content: envContent || '',
    });

    function handleSave(e) {
        e.preventDefault();
        post(route('user_hosting.env.update', { hashid: project.hashid }));
    }

    return (
        <div>
            <h3 className="font-bold text-[#333] dark:text-white text-sm mb-4 flex items-center gap-2">
                <i className="fa-solid fa-file-code text-[#7c3aed]"></i> Environment Variables (.env)
            </h3>
            <form onSubmit={handleSave}>
                <textarea
                    value={data.env_content}
                    onChange={e => setData('env_content', e.target.value)}
                    className="w-full h-[400px] bg-slate-900 text-emerald-400 font-mono text-xs p-4 rounded-lg border border-slate-700 focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none resize-none"
                    placeholder="APP_NAME=Laravel&#10;APP_ENV=production&#10;APP_KEY=...&#10;DB_CONNECTION=mysql"
                    spellCheck={false}
                />
                <div className="flex items-center justify-between mt-4">
                    <p className="text-xs text-[#999] dark:text-white/40 flex items-center gap-1.5">
                        <i className="fa-solid fa-circle-info"></i> Edit file .env proyek Anda. Perubahan akan ditulis ulang otomatis.
                    </p>
                    <button type="submit" disabled={processing}
                        className="px-5 py-2.5 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition disabled:opacity-50 shadow-sm">
                        {processing ? 'Menyimpan...' : 'Simpan .env'}
                    </button>
                </div>
            </form>
        </div>
    );
}

function SettingsTab({ project }) {
    const { data, setData, patch, processing } = useForm({
        maintenance_mode: project?.maintenance_mode || false,
        force_https: project?.force_https ?? true,
        ddos_protection: project?.ddos_protection ?? true,
        blocked_ips: project?.blocked_ips || '',
    });

    const [backupFile, setBackupFile] = useState(null);
    const restoreRef = useRef(null);

    function handleSave(e) {
        e.preventDefault();
        patch(route('user_hosting.settings.update', { hashid: project.hashid }));
    }

    function handleRestore(e) {
        const file = e.target.files?.[0];
        if (!file) return;
        Swal.fire({
            title: 'Restore Backup?',
            text: 'Semua file dan data akan ditimpa dengan backup ini.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            confirmButtonText: 'Ya, Restore!',
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('backup', file);
                router.post(route('user_hosting.backup.upload', { hashid: project.hashid }), formData, {
                    forceFormData: true,
                });
            }
        });
    }

    function handleDelete() {
        Swal.fire({
            title: 'Hapus PERMANEN?',
            html: `<p class="text-sm text-[#666]">Project <strong>${project?.project_name}</strong> akan dihapus permanen beserta semua data dan file-nya!</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('user_hosting.destroy', { hashid: project.hashid }));
            }
        });
    }

    function Toggle({ label, description, checked, onChange }) {
        return (
            <div className="flex items-center justify-between py-3 border-b border-slate-100 dark:border-[#1a1a2e] last:border-0">
                <div>
                    <p className="text-sm font-semibold text-[#333] dark:text-white">{label}</p>
                    <p className="text-xs text-[#999] dark:text-white/40 mt-0.5">{description}</p>
                </div>
                <button type="button" onClick={onChange}
                    className={`relative w-11 h-6 rounded-full transition-colors ${checked ? 'bg-[#7c3aed]' : 'bg-slate-300 dark:bg-slate-600'}`}>
                    <span className={`absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform ${checked ? 'translate-x-5' : ''}`}></span>
                </button>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            {/* Toggles */}
            <form onSubmit={handleSave}>
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5">
                    <h3 className="font-bold text-[#333] dark:text-white text-sm mb-4 flex items-center gap-2">
                        <i className="fa-solid fa-gear text-[#7c3aed]"></i> Pengaturan
                    </h3>
                    <Toggle label="Maintenance Mode" description="Tampilkan halaman 503 saat sedang perbaikan."
                        checked={data.maintenance_mode} onChange={() => setData('maintenance_mode', !data.maintenance_mode)} />
                    <Toggle label="Force HTTPS" description="Redirect semua HTTP ke HTTPS secara otomatis."
                        checked={data.force_https} onChange={() => setData('force_https', !data.force_https)} />
                    <Toggle label="DDoS Protection" description="Rate limiting untuk mitigasi serangan bot."
                        checked={data.ddos_protection} onChange={() => setData('ddos_protection', !data.ddos_protection)} />
                    <div className="mt-4">
                        <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Blocked IPs</label>
                        <textarea value={data.blocked_ips} onChange={e => setData('blocked_ips', e.target.value)}
                            className="w-full h-20 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition resize-none"
                            placeholder="192.168.1.1&#10;10.0.0.1" />
                        <p className="text-[11px] text-[#999] dark:text-white/40 mt-1">Satu IP per baris.</p>
                    </div>
                    <button type="submit" disabled={processing}
                        className="mt-4 px-5 py-2.5 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition disabled:opacity-50 shadow-sm">
                        {processing ? 'Menyimpan...' : 'Simpan Pengaturan'}
                    </button>
                </div>
            </form>

            {/* Backup / Restore */}
            <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5">
                <h3 className="font-bold text-[#333] dark:text-white text-sm mb-4 flex items-center gap-2">
                    <i className="fa-solid fa-box-archive text-[#7c3aed]"></i> Backup & Restore
                </h3>
                <div className="flex flex-col sm:flex-row gap-4">
                    <a href={route('user_hosting.backup.download', { hashid: project.hashid })}
                        className="flex-1 flex items-center gap-3 p-4 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl hover:border-[#7c3aed]/30 hover:bg-[#f5f0ff] dark:hover:bg-[#7c3aed]/5 transition-all">
                        <div className="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg flex items-center justify-center">
                            <i className="fa-solid fa-download text-emerald-600 dark:text-emerald-300"></i>
                        </div>
                        <div>
                            <p className="font-bold text-[#333] dark:text-white text-sm">Download Backup</p>
                            <p className="text-xs text-[#999] dark:text-white/40">Unduh seluruh file project</p>
                        </div>
                    </a>
                    <button onClick={() => restoreRef.current?.click()}
                        className="flex-1 flex items-center gap-3 p-4 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl hover:border-amber-300 hover:bg-amber-50 dark:hover:bg-amber-500/5 transition-all">
                        <div className="w-10 h-10 bg-amber-50 dark:bg-amber-500/10 rounded-lg flex items-center justify-center">
                            <i className="fa-solid fa-upload text-amber-600 dark:text-amber-300"></i>
                        </div>
                        <div>
                            <p className="font-bold text-[#333] dark:text-white text-sm">Restore Backup</p>
                            <p className="text-xs text-[#999] dark:text-white/40">Upload & timpa file project</p>
                        </div>
                    </button>
                    <input type="file" ref={restoreRef} onChange={handleRestore} accept=".zip" className="hidden" />
                </div>
            </div>

            {/* Danger Zone */}
            <div className="bg-white dark:bg-[#0d0d18] border-2 border-rose-200 dark:border-rose-500/30 rounded-xl p-5">
                <h3 className="font-bold text-rose-600 dark:text-rose-400 text-sm mb-2 flex items-center gap-2">
                    <i className="fa-solid fa-triangle-exclamation"></i> Danger Zone
                </h3>
                <p className="text-xs text-[#999] dark:text-white/40 mb-4">
                    Menghapus project akan menghapus semua file, database, dan konfigurasi secara permanen.
                </p>
                <button onClick={handleDelete}
                    className="px-5 py-2.5 text-sm font-bold text-white bg-rose-600 rounded-lg hover:bg-rose-700 transition shadow-sm">
                    <i className="fa-solid fa-trash mr-2"></i> Hapus Project
                </button>
            </div>
        </div>
    );
}

function CronsTab({ project }) {
    const { data, setData, post, processing, reset } = useForm({
        command: '',
        schedule: '',
    });
    const [crons, setCrons] = useState(project?.crons || []);

    function handleAdd(e) {
        e.preventDefault();
        post(route('user_hosting.crons.store', { hashid: project.hashid }), {
            preserveState: true,
            onSuccess: (page) => {
                setCrons(page.props.project?.crons || []);
                reset();
            },
        });
    }

    function handleDelete(cron) {
        Swal.fire({
            title: 'Hapus Cron Job?',
            text: `Schedule "${cron.schedule}" akan dihapus.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('user_hosting.crons.destroy', { hashid: cron.hashid }), {
                    preserveState: true,
                    onSuccess: (page) => {
                        setCrons(page.props.project?.crons || []);
                    },
                });
            }
        });
    }

    return (
        <div>
            <h3 className="font-bold text-[#333] dark:text-white text-sm mb-4 flex items-center gap-2">
                <i className="fa-solid fa-clock-rotate-left text-[#7c3aed]"></i> Cron Jobs
            </h3>

            {/* Add Form */}
            <form onSubmit={handleAdd} className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5 mb-4">
                <div className="flex flex-col sm:flex-row gap-3">
                    <input type="text" value={data.command} onChange={e => setData('command', e.target.value)}
                        placeholder="Perintah (contoh: php artisan schedule:run)" required
                        className="flex-1 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                    <input type="text" value={data.schedule} onChange={e => setData('schedule', e.target.value)}
                        placeholder="Schedule (contoh: */5 * * * *)" required
                        className="w-full sm:w-56 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                    <button type="submit" disabled={processing}
                        className="px-5 py-2.5 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition disabled:opacity-50 shadow-sm whitespace-nowrap">
                        {processing ? 'Menambahkan...' : '+ Tambah'}
                    </button>
                </div>
            </form>

            {/* Cron Table */}
            <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-5 py-3">Command</th>
                                <th className="px-5 py-3">Schedule</th>
                                <th className="px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {crons.length === 0 ? (
                                <tr><td colSpan="3" className="px-5 py-8 text-center text-[#999] dark:text-white/40">Belum ada cron job.</td></tr>
                            ) : crons.map(cron => (
                                <tr key={cron.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-5 py-3 font-mono text-xs text-[#333] dark:text-white">{cron.command}</td>
                                    <td className="px-5 py-3 font-mono text-xs text-[#7c3aed] dark:text-[#a78bfa]">{cron.schedule}</td>
                                    <td className="px-5 py-3 text-center">
                                        <button onClick={() => handleDelete(cron)}
                                            className="w-7 h-7 rounded-md flex items-center justify-center text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition mx-auto" title="Hapus">
                                            <i className="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}

function TeamTab({ project, projectEmails }) {
    const { data, setData, post, processing, reset } = useForm({
        email: '',
        role: 'viewer',
    });
    const [members, setMembers] = useState(project?.team_members || []);

    function handleInvite(e) {
        e.preventDefault();
        post(route('user_hosting.team.invite', { hashid: project.hashid }), {
            preserveState: true,
            onSuccess: (page) => {
                setMembers(page.props.project?.team_members || []);
                reset();
            },
        });
    }

    function handleRemove(member) {
        Swal.fire({
            title: 'Hapus Anggota?',
            text: `${member.name || member.email} akan dihapus dari project.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('user_hosting.team.remove', [project.hashid, member.id]), {
                    preserveState: true,
                    onSuccess: (page) => {
                        setMembers(page.props.project?.team_members || []);
                    },
                });
            }
        });
    }

    return (
        <div>
            <h3 className="font-bold text-[#333] dark:text-white text-sm mb-4 flex items-center gap-2">
                <i className="fa-solid fa-users text-[#7c3aed]"></i> Team Access
            </h3>

            {/* Invite Form */}
            <form onSubmit={handleInvite} className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5 mb-4">
                <div className="flex flex-col sm:flex-row gap-3">
                    <input type="email" value={data.email} onChange={e => setData('email', e.target.value)}
                        placeholder="Email anggota" required
                        className="flex-1 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                    <select value={data.role} onChange={e => setData('role', e.target.value)}
                        className="w-full sm:w-40 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition cursor-pointer">
                        <option value="viewer">Viewer</option>
                        <option value="editor">Editor</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button type="submit" disabled={processing}
                        className="px-5 py-2.5 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition disabled:opacity-50 shadow-sm whitespace-nowrap">
                        {processing ? 'Mengirim...' : '+ Invite'}
                    </button>
                </div>
            </form>

            {/* Members Table */}
            <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-5 py-3">Nama</th>
                                <th className="px-5 py-3">Email</th>
                                <th className="px-5 py-3">Role</th>
                                <th className="px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {members.length === 0 ? (
                                <tr><td colSpan="4" className="px-5 py-8 text-center text-[#999] dark:text-white/40">Belum ada anggota tim.</td></tr>
                            ) : members.map(member => (
                                <tr key={member.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-5 py-3 font-medium text-[#333] dark:text-white">{member.name}</td>
                                    <td className="px-5 py-3 text-xs text-[#999] dark:text-white/40">{member.email}</td>
                                    <td className="px-5 py-3">
                                        <span className={`text-[11px] font-bold uppercase px-2 py-0.5 rounded-full ${
                                            member.pivot?.role === 'admin' ? 'bg-[#7c3aed]/10 text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]'
                                            : member.pivot?.role === 'editor' ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300'
                                            : 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300'
                                        }`}>
                                            {member.pivot?.role || 'viewer'}
                                        </span>
                                    </td>
                                    <td className="px-5 py-3 text-center">
                                        <button onClick={() => handleRemove(member)}
                                            className="w-7 h-7 rounded-md flex items-center justify-center text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition mx-auto" title="Hapus">
                                            <i className="fa-solid fa-user-xmark text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}
