import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, usePage, router, Link } from '@inertiajs/react';
import { useState, useRef } from 'react';
import Swal from 'sweetalert2';

function generatePassword(length = 16) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
    let pass = '';
    for (let i = 0; i < length; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
    return pass;
}

function CopyBtn({ text }) {
    const [copied, setCopied] = useState(false);
    function handleCopy() {
        navigator.clipboard.writeText(text);
        setCopied(true);
        setTimeout(() => setCopied(false), 1500);
    }
    return (
        <button type="button" onClick={handleCopy} className="text-[#999] dark:text-white/40 hover:text-[#7c3aed] dark:hover:text-[#a78bfa] transition ml-1" title="Salin">
            <i className={`fa-solid ${copied ? 'fa-check text-emerald-500' : 'fa-copy'} text-xs`}></i>
        </button>
    );
}

function PasswordField({ value, onChange, placeholder, readOnly }) {
    const [show, setShow] = useState(false);
    return (
        <div className="flex items-center gap-2">
            <div className="flex-1 relative">
                <input
                    type={show ? 'text' : 'password'}
                    value={value}
                    onChange={onChange}
                    readOnly={readOnly}
                    placeholder={placeholder}
                    className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition"
                />
            </div>
            <button type="button" onClick={() => setShow(!show)} className="text-[#999] dark:text-white/40 hover:text-[#7c3aed] dark:hover:text-[#a78bfa] transition" title={show ? 'Sembunyikan' : 'Tampilkan'}>
                <i className={`fa-solid ${show ? 'fa-eye-slash' : 'fa-eye'} text-sm`}></i>
            </button>
            <CopyBtn text={value} />
        </div>
    );
}

function Toggle({ label, checked, onChange }) {
    return (
        <div className="flex items-center justify-between py-2">
            <span className="text-sm text-[#333] dark:text-white font-medium">{label}</span>
            <button type="button" onClick={onChange} className={`relative w-11 h-6 rounded-full transition-colors ${checked ? 'bg-[#7c3aed]' : 'bg-slate-300 dark:bg-slate-600'}`}>
                <span className={`absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform ${checked ? 'translate-x-5' : ''}`}></span>
            </button>
        </div>
    );
}

export default function Databases({ databases, nosqlDatabases, pgsqlDatabases }) {
    const [activeTab, setActiveTab] = useState('mysql');
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showImportModal, setShowImportModal] = useState(false);
    const [importDbId, setImportDbId] = useState(null);
    const [showApiDocsModal, setShowApiDocsModal] = useState(false);
    const [showApiTesterModal, setShowApiTesterModal] = useState(false);
    const [testerDb, setTesterDb] = useState(null);
    const [testerMethod, setTesterMethod] = useState('GET');
    const [testerPath, setTesterPath] = useState('');
    const [testerBody, setTesterBody] = useState('');
    const [testerResponse, setTesterResponse] = useState('');
    const [testerLoading, setTesterLoading] = useState(false);
    const [docsDb, setDocsDb] = useState(null);

    const { data: createData, setData: setCreateData, post: postCreate, processing: createProcessing, reset: resetCreate } = useForm({
        db_name: '',
        username: '',
        password: '',
        type: 'mysql',
    });

    const { data: importData, setData: setImportData, post: postImport, processing: importProcessing } = useForm({
        sql_file: null,
        drop_tables: false,
    });

    const dbList = Array.isArray(databases) ? databases : (databases?.data || []);
    const nosqlList = Array.isArray(nosqlDatabases) ? nosqlDatabases : (nosqlDatabases?.data || []);
    const pgsqlList = Array.isArray(pgsqlDatabases) ? pgsqlDatabases : (pgsqlDatabases?.data || []);

    const tabs = [
        { key: 'mysql', label: 'MySQL', icon: 'fa-solid fa-database', count: dbList.length },
        { key: 'redis', label: 'Redis', icon: 'fa-solid fa-bolt', count: nosqlList.length },
        { key: 'pgsql', label: 'PostgreSQL', icon: 'fa-solid fa-elephant', count: pgsqlList.length },
    ];

    function handleCreate(e) {
        e.preventDefault();
        const url = createData.type === 'redis'
            ? route('user_hosting.databases.nosql.store')
            : createData.type === 'pgsql'
                ? route('user_hosting.databases.pgsql.store')
                : route('user_hosting.databases.store');
        postCreate(url, {
            onSuccess: () => { setShowCreateModal(false); resetCreate(); },
        });
    }

    function handleDelete(id, type) {
        const url = type === 'redis'
            ? route('user_hosting.databases.nosql.destroy', { hashid: id })
            : type === 'pgsql'
                ? route('user_hosting.databases.pgsql.destroy', { hashid: id })
                : route('user_hosting.databases.destroy', { hashid: id });
        Swal.fire({
            title: 'Hapus Database?',
            text: 'Tindakan ini tidak dapat dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
        }).then((result) => {
            if (result.isConfirmed) router.delete(url);
        });
    }

    function handleImport(e) {
        e.preventDefault();
        if (!importDbId) return;
        const formData = new FormData();
        formData.append('sql_file', importData.sql_file);
        formData.append('drop_tables', importData.drop_tables ? '1' : '0');
        postImport(route('user_hosting.databases.import', { hashid: importDbId }), {
            forceFormData: true,
            onSuccess: () => { setShowImportModal(false); setImportDbId(null); setImportData({ sql_file: null, drop_tables: false }); },
        });
    }

    function handleRegenerateApiKey(hashid) {
        Swal.fire({
            title: 'Regenerate API Key?',
            text: 'API key lama akan berhenti berfungsi.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            confirmButtonText: 'Ya, Regenerate!',
        }).then((result) => {
            if (result.isConfirmed) router.post(route('user_hosting.databases.apikey', { hashid: hashid }));
        });
    }

    function handleApiTest() {
        if (!testerDb || !testerPath) return;
        setTesterLoading(true);
        setTesterResponse('');
        const url = `https://${testerDb.host}:${testerDb.port}${testerPath}`;
        fetch(url, {
            method: testerMethod,
            headers: { 'Authorization': `Bearer ${testerDb.api_key}`, 'Content-Type': 'application/json' },
            body: testerMethod !== 'GET' ? testerBody : undefined,
        })
            .then(r => r.text())
            .then(data => { setTesterResponse(data); setTesterLoading(false); })
            .catch(err => { setTesterResponse('Error: ' + err.message); setTesterLoading(false); });
    }

    function openCreateModal(type) {
        resetCreate();
        setCreateData('type', type);
        if (type === 'mysql' && dbList.length > 0) {
            setCreateData('username', '');
        }
        setShowCreateModal(true);
    }

    return (
        <DashboardLayout title="Database Manager">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-database text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Database Manager</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola semua database MySQL, Redis, dan PostgreSQL Anda.</p>
                    </div>
                </div>
            </div>

            <div className="mt-6 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="border-b border-[#e5e5e5] dark:border-[#1a1a2e] overflow-x-auto">
                    <div className="flex">
                        {tabs.map(tab => (
                            <button key={tab.key} onClick={() => setActiveTab(tab.key)}
                                className={`flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap transition-colors border-b-2 ${activeTab === tab.key ? 'border-[#7c3aed] text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10' : 'border-transparent text-[#999] dark:text-white/40 hover:text-[#7c3aed] dark:hover:text-white hover:bg-[#fafafa] dark:hover:bg-white/[0.02]'}`}>
                                <i className={`${tab.icon} text-xs`}></i>
                                {tab.label}
                                <span className="px-1.5 py-0.5 text-[10px] font-bold bg-slate-100 dark:bg-slate-700/50 rounded-full">{tab.count}</span>
                            </button>
                        ))}
                    </div>
                </div>

                <div className="p-6">
                    {activeTab === 'mysql' && (
                        <div>
                            <div className="flex items-center justify-between mb-4">
                                <h3 className="font-bold text-[#333] dark:text-white text-sm">MySQL Databases</h3>
                                <button onClick={() => openCreateModal('mysql')} className="inline-flex items-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                                    <i className="fa-solid fa-plus text-xs"></i> Buat Database
                                </button>
                            </div>
                            {dbList.length === 0 ? (
                                <div className="text-center py-12">
                                    <div className="w-16 h-16 bg-slate-100 dark:bg-slate-700/50 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                                        <i className="fa-solid fa-database"></i>
                                    </div>
                                    <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Belum ada database</h3>
                                    <p className="text-[#999] dark:text-white/40 mb-4 text-sm">Buat database MySQL pertama Anda.</p>
                                </div>
                            ) : (
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {dbList.map(db => (
                                        <div key={db.id || db.hashid} className="bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5 hover:border-[#7c3aed]/30 transition-all">
                                            <div className="flex items-center justify-between mb-4">
                                                <div className="flex items-center gap-2">
                                                    <i className="fa-solid fa-database text-[#7c3aed]"></i>
                                                    <span className="font-bold text-[#333] dark:text-white text-sm">{db.db_name}</span>
                                                </div>
                                                <span className="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">
                                                    <i className="fa-solid fa-circle-check mr-1"></i>Active
                                                </span>
                                            </div>
                                            <div className="space-y-2 text-xs">
                                                <div className="flex items-center justify-between">
                                                    <span className="text-[#999] dark:text-white/40">Host / Port</span>
                                                    <div className="flex items-center gap-1">
                                                        <span className="font-mono text-[#333] dark:text-white">{db.host}:{db.port}</span>
                                                        <CopyBtn text={`${db.host}:${db.port}`} />
                                                    </div>
                                                </div>
                                                <div className="flex items-center justify-between">
                                                    <span className="text-[#999] dark:text-white/40">Username</span>
                                                    <div className="flex items-center gap-1">
                                                        <span className="font-mono text-[#333] dark:text-white">{db.username}</span>
                                                        <CopyBtn text={db.username} />
                                                    </div>
                                                </div>
                                                <div className="flex items-center justify-between">
                                                    <span className="text-[#999] dark:text-white/40">Password</span>
                                                    <PasswordField value={db.password_decrypted || db.password} readOnly />
                                                </div>
                                            </div>

                                            <div className="mt-4 pt-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                                                <p className="text-[10px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 mb-2">REST API</p>
                                                <div className="space-y-2 text-xs">
                                                    <div className="flex items-center justify-between">
                                                        <span className="text-[#999] dark:text-white/40">Endpoint</span>
                                                        <div className="flex items-center gap-1">
                                                            <span className="font-mono text-[#333] dark:text-white text-[10px] truncate max-w-[150px]">https://{db.host}:{db.port}</span>
                                                            <CopyBtn text={`https://${db.host}:${db.port}`} />
                                                        </div>
                                                    </div>
                                                    <div className="flex items-center justify-between">
                                                        <span className="text-[#999] dark:text-white/40">API Key</span>
                                                        <div className="flex items-center gap-1">
                                                            <span className="font-mono text-[#333] dark:text-white text-[10px] truncate max-w-[100px]">{db.api_key || 'N/A'}</span>
                                                            {db.api_key && <CopyBtn text={db.api_key} />}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="flex items-center gap-1 mt-2">
                                                    <button onClick={() => handleRegenerateApiKey(db.hashid)} className="text-[10px] font-medium text-[#7c3aed] dark:text-[#a78bfa] hover:underline">
                                                        <i className="fa-solid fa-rotate mr-1"></i>Regenerate
                                                    </button>
                                                </div>
                                            </div>

                                            <div className="mt-4 pt-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] space-y-2">
                                                <div className="flex items-center gap-2 flex-wrap">
                                                    <a href={route('user_hosting.databases.manager', { hashid: db.hashid })} target="_blank" rel="noopener noreferrer"
                                                        className="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition">
                                                        <i className="fa-solid fa-server"></i> Database Manager
                                                    </a>
                                                    <button onClick={() => { setTesterDb(db); setShowApiTesterModal(true); }}
                                                        className="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 rounded-lg hover:bg-[#ede4ff] dark:hover:bg-[#7c3aed]/20 transition">
                                                        <i className="fa-solid fa-flask"></i> Test API
                                                    </button>
                                                    <button onClick={() => { setDocsDb(db); setShowApiDocsModal(true); }}
                                                        className="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700/50 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                                                        <i className="fa-solid fa-book"></i> API Docs
                                                    </button>
                                                </div>
                                                <div className="flex items-center gap-2 flex-wrap">
                                                    <a href={route('user_hosting.databases.export', { hashid: db.hashid })} target="_blank" rel="noopener noreferrer"
                                                        className="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition">
                                                        <i className="fa-solid fa-download"></i> Export .sql
                                                    </a>
                                                    <button onClick={() => { setImportDbId(db.hashid); setShowImportModal(true); }}
                                                        className="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-500/10 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-500/20 transition">
                                                        <i className="fa-solid fa-upload"></i> Import
                                                    </button>
                                                    <CopyBtn text={`mysql -h ${db.host} -P ${db.port} -u ${db.username} -p`} />
                                                </div>
                                            </div>

                                            <div className="mt-3 pt-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-end">
                                                <button onClick={() => handleDelete(db.hashid || db.id, 'mysql')} className="text-xs font-medium text-rose-500 hover:text-rose-700 transition">
                                                    <i className="fa-solid fa-trash mr-1"></i>Hapus
                                                </button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    )}

                    {activeTab === 'redis' && (
                        <div>
                            <div className="flex items-center justify-between mb-4">
                                <h3 className="font-bold text-[#333] dark:text-white text-sm">Redis Databases</h3>
                                <button onClick={() => openCreateModal('redis')} className="inline-flex items-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                                    <i className="fa-solid fa-plus text-xs"></i> Buat Redis
                                </button>
                            </div>
                            {nosqlList.length === 0 ? (
                                <div className="text-center py-12">
                                    <div className="w-16 h-16 bg-slate-100 dark:bg-slate-700/50 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                                        <i className="fa-solid fa-bolt"></i>
                                    </div>
                                    <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Belum ada Redis</h3>
                                    <p className="text-[#999] dark:text-white/40 mb-4 text-sm">Buat Redis instance pertama Anda.</p>
                                </div>
                            ) : (
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {nosqlList.map(db => (
                                        <div key={db.id || db.hashid} className="bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5 hover:border-[#7c3aed]/30 transition-all">
                                            <div className="flex items-center justify-between mb-4">
                                                <div className="flex items-center gap-2">
                                                    <i className="fa-solid fa-bolt text-amber-500"></i>
                                                    <span className="font-bold text-[#333] dark:text-white text-sm">{db.name || db.username}</span>
                                                </div>
                                            </div>
                                            <div className="space-y-2 text-xs">
                                                <div className="flex items-center justify-between">
                                                    <span className="text-[#999] dark:text-white/40">Host</span>
                                                    <div className="flex items-center gap-1">
                                                        <span className="font-mono text-[#333] dark:text-white">{db.host}</span>
                                                        <CopyBtn text={db.host} />
                                                    </div>
                                                </div>
                                                <div className="flex items-center justify-between">
                                                    <span className="text-[#999] dark:text-white/40">Port</span>
                                                    <div className="flex items-center gap-1">
                                                        <span className="font-mono text-[#333] dark:text-white">{db.port}</span>
                                                        <CopyBtn text={String(db.port)} />
                                                    </div>
                                                </div>
                                                {db.username && (
                                                    <div className="flex items-center justify-between">
                                                        <span className="text-[#999] dark:text-white/40">Username</span>
                                                        <div className="flex items-center gap-1">
                                                            <span className="font-mono text-[#333] dark:text-white">{db.username}</span>
                                                            <CopyBtn text={db.username} />
                                                        </div>
                                                    </div>
                                                )}
                                                {db.prefix && (
                                                    <div className="flex items-center justify-between">
                                                        <span className="text-[#999] dark:text-white/40">Prefix</span>
                                                        <div className="flex items-center gap-1">
                                                            <span className="font-mono text-[#333] dark:text-white">{db.prefix}</span>
                                                            <CopyBtn text={db.prefix} />
                                                        </div>
                                                    </div>
                                                )}
                                                <div className="flex items-center justify-between">
                                                    <span className="text-[#999] dark:text-white/40">Password</span>
                                                    <PasswordField value={db.password_decrypted || db.password} readOnly />
                                                </div>
                                                <div className="flex items-center justify-between">
                                                    <span className="text-[#999] dark:text-white/40">Connection String</span>
                                                    <CopyBtn text={`redis://:${db.password_decrypted || db.password}@${db.host}:${db.port}`} />
                                                </div>
                                            </div>
                                            <div className="mt-3 pt-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-end">
                                                <button onClick={() => handleDelete(db.hashid || db.id, 'redis')} className="text-xs font-medium text-rose-500 hover:text-rose-700 transition">
                                                    <i className="fa-solid fa-trash mr-1"></i>Hapus
                                                </button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    )}

                    {activeTab === 'pgsql' && (
                        <div>
                            <div className="flex items-center justify-between mb-4">
                                <h3 className="font-bold text-[#333] dark:text-white text-sm">PostgreSQL Databases</h3>
                                <button onClick={() => openCreateModal('pgsql')} className="inline-flex items-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                                    <i className="fa-solid fa-plus text-xs"></i> Buat PostgreSQL
                                </button>
                            </div>
                            {pgsqlList.length === 0 ? (
                                <div className="text-center py-12">
                                    <div className="w-16 h-16 bg-slate-100 dark:bg-slate-700/50 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                                        <i className="fa-solid fa-elephant"></i>
                                    </div>
                                    <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Belum ada PostgreSQL</h3>
                                    <p className="text-[#999] dark:text-white/40 mb-4 text-sm">Buat PostgreSQL database pertama Anda.</p>
                                </div>
                            ) : (
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {pgsqlList.map(db => (
                                        <div key={db.id || db.hashid} className="bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl p-5 hover:border-[#7c3aed]/30 transition-all">
                                            <div className="flex items-center justify-between mb-4">
                                                <div className="flex items-center gap-2">
                                                    <i className="fa-solid fa-elephant text-blue-500"></i>
                                                    <span className="font-bold text-[#333] dark:text-white text-sm">{db.database || db.db_name}</span>
                                                </div>
                                            </div>
                                            <div className="space-y-2 text-xs">
                                                <div className="flex items-center justify-between">
                                                    <span className="text-[#999] dark:text-white/40">Host</span>
                                                    <div className="flex items-center gap-1">
                                                        <span className="font-mono text-[#333] dark:text-white">{db.host}</span>
                                                        <CopyBtn text={db.host} />
                                                    </div>
                                                </div>
                                                <div className="flex items-center justify-between">
                                                    <span className="text-[#999] dark:text-white/40">Port</span>
                                                    <div className="flex items-center gap-1">
                                                        <span className="font-mono text-[#333] dark:text-white">{db.port}</span>
                                                        <CopyBtn text={String(db.port)} />
                                                    </div>
                                                </div>
                                                <div className="flex items-center justify-between">
                                                    <span className="text-[#999] dark:text-white/40">Username / DB</span>
                                                    <div className="flex items-center gap-1">
                                                        <span className="font-mono text-[#333] dark:text-white">{db.username}</span>
                                                        <CopyBtn text={db.username} />
                                                    </div>
                                                </div>
                                                <div className="flex items-center justify-between">
                                                    <span className="text-[#999] dark:text-white/40">Password</span>
                                                    <PasswordField value={db.password_decrypted || db.password} readOnly />
                                                </div>
                                            </div>
                                            <div className="mt-3 pt-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                                                <a href={route('user_hosting.databases.manager', { hashid: db.hashid })} target="_blank" rel="noopener noreferrer"
                                                    className="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition">
                                                    <i className="fa-solid fa-server"></i> Database Manager
                                                </a>
                                                <div className="mt-2">
                                                    <CopyBtn text={`postgresql://${db.username}:${db.password_decrypted || db.password}@${db.host}:${db.port}/${db.database || db.db_name}`} />
                                                </div>
                                            </div>
                                            <div className="mt-3 pt-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-end">
                                                <button onClick={() => handleDelete(db.hashid || db.id, 'pgsql')} className="text-xs font-medium text-rose-500 hover:text-rose-700 transition">
                                                    <i className="fa-solid fa-trash mr-1"></i>Hapus
                                                </button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>

            {/* Create Modal */}
            {showCreateModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div className="w-full max-w-md bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl">
                        <div className="p-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm">
                                Buat Database {createData.type === 'redis' ? 'Redis' : createData.type === 'pgsql' ? 'PostgreSQL' : 'MySQL'}
                            </h3>
                            <button onClick={() => { setShowCreateModal(false); resetCreate(); }} className="text-[#999] hover:text-red-500 transition">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <form onSubmit={handleCreate} className="p-5 space-y-4">
                            {createData.type === 'redis' && (
                                <>
                                    <div>
                                        <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Username / Name</label>
                                        <input type="text" value={createData.username} onChange={e => setCreateData('username', e.target.value)} required
                                            className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                    </div>
                                </>
                            )}
                            {createData.type === 'pgsql' && (
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Database & Username</label>
                                    <input type="text" value={createData.username} onChange={e => setCreateData('username', e.target.value)} required
                                        placeholder="Nama database sekaligus username"
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                </div>
                            )}
                            {createData.type === 'mysql' && (
                                <>
                                    <div>
                                        <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Database Name</label>
                                        <div className="flex items-center">
                                            <span className="text-xs text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border border-r-0 border-[#e5e5e5] dark:border-[#1a1a2e] rounded-l-xl px-3 py-2.5 font-mono">ryz_{dbList.length + 1}_</span>
                                            <input type="text" value={createData.db_name} onChange={e => setCreateData('db_name', e.target.value)} required
                                                className="flex-1 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-r-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                        </div>
                                    </div>
                                    {dbList.length === 0 && (
                                        <div>
                                            <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Username</label>
                                            <input type="text" value={createData.username} onChange={e => setCreateData('username', e.target.value)} required
                                                className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                        </div>
                                    )}
                                </>
                            )}
                            <div>
                                <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Password</label>
                                <div className="flex items-center gap-2">
                                    <input type="text" value={createData.password} onChange={e => setCreateData('password', e.target.value)}
                                        className="flex-1 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                    <button type="button" onClick={() => setCreateData('password', generatePassword())}
                                        className="px-3 py-2.5 text-xs font-medium text-[#7c3aed] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 rounded-lg hover:bg-[#ede4ff] dark:hover:bg-[#7c3aed]/20 transition whitespace-nowrap">
                                        <i className="fa-solid fa-rotate mr-1"></i>Generate
                                    </button>
                                </div>
                            </div>
                            <div className="flex justify-end gap-3 pt-2">
                                <button type="button" onClick={() => { setShowCreateModal(false); resetCreate(); }}
                                    className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg hover:bg-[#fafafa] transition">
                                    Batal
                                </button>
                                <button type="submit" disabled={createProcessing}
                                    className="px-4 py-2 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition disabled:opacity-50">
                                    {createProcessing ? 'Membuat...' : 'Buat Database'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Import Modal */}
            {showImportModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div className="w-full max-w-md bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl">
                        <div className="p-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm">Import SQL</h3>
                            <button onClick={() => { setShowImportModal(false); setImportDbId(null); }} className="text-[#999] hover:text-red-500 transition">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <form onSubmit={handleImport} className="p-5 space-y-4">
                            <div>
                                <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">File SQL</label>
                                <input type="file" accept=".sql" onChange={e => setImportData('sql_file', e.target.files?.[0])} required
                                    className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#7c3aed] file:text-white hover:file:bg-[#6d28d9] file:cursor-pointer" />
                            </div>
                            <Toggle label="Drop tables sebelum import" checked={importData.drop_tables} onChange={() => setImportData('drop_tables', !importData.drop_tables)} />
                            <div className="flex justify-end gap-3 pt-2">
                                <button type="button" onClick={() => { setShowImportModal(false); setImportDbId(null); }}
                                    className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg hover:bg-[#fafafa] transition">
                                    Batal
                                </button>
                                <button type="submit" disabled={importProcessing}
                                    className="px-4 py-2 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition disabled:opacity-50">
                                    {importProcessing ? 'Mengimport...' : 'Import'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* API Docs Modal */}
            {showApiDocsModal && docsDb && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div className="w-full max-w-3xl max-h-[85vh] bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl flex flex-col">
                        <div className="p-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between shrink-0">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm flex items-center gap-2">
                                <i className="fa-solid fa-book text-[#7c3aed]"></i> API Documentation - {docsDb.db_name}
                            </h3>
                            <button onClick={() => setShowApiDocsModal(false)} className="text-[#999] hover:text-red-500 transition">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div className="p-5 overflow-y-auto flex-1 space-y-4 text-xs">
                            <div>
                                <h4 className="font-bold text-[#333] dark:text-white mb-2">GET - Ambil Data</h4>
                                <div className="bg-slate-900 text-emerald-400 font-mono p-4 rounded-lg overflow-x-auto">
                                    <pre>{`curl -X GET "https://${docsDb.host}:${docsDb.port}/api/data" \\
  -H "Authorization: Bearer ${docsDb.api_key}"`}</pre>
                                </div>
                            </div>
                            <div>
                                <h4 className="font-bold text-[#333] dark:text-white mb-2">POST - Kirim Data</h4>
                                <div className="bg-slate-900 text-emerald-400 font-mono p-4 rounded-lg overflow-x-auto">
                                    <pre>{`curl -X POST "https://${docsDb.host}:${docsDb.port}/api/data" \\
  -H "Authorization: Bearer ${docsDb.api_key}" \\
  -H "Content-Type: application/json" \\
  -d '{"key": "value"}'`}</pre>
                                </div>
                            </div>
                            <div>
                                <h4 className="font-bold text-[#333] dark:text-white mb-2">JavaScript Fetch</h4>
                                <div className="bg-slate-900 text-emerald-400 font-mono p-4 rounded-lg overflow-x-auto">
                                    <pre>{`const response = await fetch("https://${docsDb.host}:${docsDb.port}/api/data", {
  headers: { "Authorization": "Bearer ${docsDb.api_key}" }
});
const data = await response.json();`}</pre>
                                </div>
                            </div>
                            <div>
                                <h4 className="font-bold text-[#333] dark:text-white mb-2">Postman</h4>
                                <div className="bg-slate-900 text-emerald-400 font-mono p-4 rounded-lg overflow-x-auto">
                                    <pre>{`URL: https://${docsDb.host}:${docsDb.port}/api/data
Method: GET
Headers:
  Authorization: Bearer ${docsDb.api_key}`}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* API Tester Modal */}
            {showApiTesterModal && testerDb && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div className="w-full max-w-2xl bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl">
                        <div className="p-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm flex items-center gap-2">
                                <i className="fa-solid fa-flask text-[#7c3aed]"></i> API Tester - {testerDb.db_name}
                            </h3>
                            <button onClick={() => { setShowApiTesterModal(false); setTesterResponse(''); }} className="text-[#999] hover:text-red-500 transition">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div className="p-5 space-y-4">
                            <div className="flex items-center gap-2">
                                <select value={testerMethod} onChange={e => setTesterMethod(e.target.value)}
                                    className="bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-3 py-2.5 text-sm font-bold text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition">
                                    <option>GET</option><option>POST</option><option>PUT</option><option>DELETE</option>
                                </select>
                                <input type="text" value={testerPath} onChange={e => setTesterPath(e.target.value)} placeholder="/api/data"
                                    className="flex-1 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                <button onClick={handleApiTest} disabled={testerLoading || !testerPath}
                                    className="px-5 py-2.5 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition disabled:opacity-50">
                                    {testerLoading ? <i className="fa-solid fa-spinner fa-spin"></i> : 'Send'}
                                </button>
                            </div>
                            {testerMethod !== 'GET' && (
                                <textarea value={testerBody} onChange={e => setTesterBody(e.target.value)} placeholder='{"key": "value"}' rows={4}
                                    className="w-full bg-slate-900 text-emerald-400 font-mono text-xs p-4 rounded-lg border border-slate-700 focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none resize-none" />
                            )}
                            {testerResponse && (
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Response</label>
                                    <div className="bg-slate-900 text-emerald-400 font-mono text-xs p-4 rounded-lg border border-slate-700 max-h-60 overflow-auto">
                                        <pre className="whitespace-pre-wrap break-all">{testerResponse}</pre>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
