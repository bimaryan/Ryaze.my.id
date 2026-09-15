import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, usePage, Link } from '@inertiajs/react';
import { useState, useRef } from 'react';

const templates = {
    html_landing: { icon: 'fa-brands fa-html5', color: 'text-orange-500 dark:text-orange-400', bg: 'bg-orange-100 dark:bg-orange-500/20', name: 'HTML Landing Page', tag: 'HTML', tagColor: 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-300', desc: 'Template landing page modern dengan HTML, CSS & JS. Tidak butuh build step, langsung live.', deploy: 'Instant deploy' },
    tailwind_starter: { icon: 'fa-solid fa-wind', color: 'text-cyan-500 dark:text-cyan-400', bg: 'bg-cyan-100 dark:bg-cyan-500/20', name: 'Tailwind CSS', tag: 'HTML & CSS', tagColor: 'bg-cyan-100 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300', desc: 'Starter template UI modern dengan Tailwind CSS CDN. Langsung ngoding utility class tanpa build step.', deploy: 'Instant deploy' },
    php_basic: { icon: 'fa-brands fa-php', color: 'text-indigo-600 dark:text-indigo-400', bg: 'bg-indigo-100 dark:bg-indigo-500/20', name: 'PHP Basic App', tag: 'PHP Native', tagColor: 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300', desc: 'Starter PHP murni dengan struktur MVC sederhana, koneksi database, dan router dasar.', deploy: 'Instant deploy' },
    wordpress: { icon: 'fa-brands fa-wordpress', color: 'text-blue-600 dark:text-blue-300', bg: 'bg-blue-100 dark:bg-blue-500/20', name: 'WordPress CMS', tag: 'PHP & MySQL', tagColor: 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300', desc: 'Auto-install WordPress terbaru lengkap dengan database siap pakai.', deploy: 'Auto build' },
    laravel_starter_13: { icon: 'fa-brands fa-laravel', color: 'text-red-500 dark:text-red-400', bg: 'bg-red-100 dark:bg-red-500/20', name: 'Laravel 13', tag: 'Laravel', tagColor: 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300', desc: 'Laravel 13 fresh install resmi, siap pakai sebagai backend API atau web app.', deploy: 'Auto build' },
    laravel_starter_12: { icon: 'fa-brands fa-laravel', color: 'text-red-500 dark:text-red-400', bg: 'bg-red-100 dark:bg-red-500/20', name: 'Laravel 12', tag: 'Laravel', tagColor: 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300', desc: 'Laravel 12 fresh install resmi, stabil dan siap untuk produksi.', deploy: 'Auto build' },
    laravel_starter_11: { icon: 'fa-brands fa-laravel', color: 'text-red-500 dark:text-red-400', bg: 'bg-red-100 dark:bg-red-500/20', name: 'Laravel 11', tag: 'Laravel', tagColor: 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300', desc: 'Laravel 11 fresh install, struktur sederhana dan mudah dipelajari.', deploy: 'Auto build' },
    laravel_starter_10: { icon: 'fa-brands fa-laravel', color: 'text-red-500 dark:text-red-400', bg: 'bg-red-100 dark:bg-red-500/20', name: 'Laravel 10', tag: 'Laravel', tagColor: 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300', desc: 'Laravel 10 LTS, kompatibel dengan banyak package dan dokumentasi luas.', deploy: 'Auto build' },
    react_starter: { icon: 'fa-brands fa-react', color: 'text-sky-500 dark:text-sky-400', bg: 'bg-sky-100 dark:bg-sky-500/20', name: 'React + Vite', tag: 'React JS', tagColor: 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300', desc: 'React dengan Vite bundler, TailwindCSS, dan React Router. Starter app siap dikembangkan.', deploy: 'Auto build' },
    nextjs_starter: { icon: 'fa-brands fa-node-js', color: 'text-slate-800 dark:text-slate-100', bg: 'bg-slate-900', name: 'Next.js App', tag: 'Next.js', tagColor: 'bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200', desc: 'Next.js dengan App Router dan TailwindCSS. Cocok untuk SSR, SSG, maupun full-stack web app.', deploy: 'Auto build' },
    node_express: { icon: 'fa-brands fa-node', color: 'text-emerald-600 dark:text-emerald-300', bg: 'bg-emerald-100 dark:bg-emerald-500/20', name: 'Node.js Express API', tag: 'Node.js', tagColor: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300', desc: 'REST API Express.js dengan struktur MVC, middleware auth JWT, dan koneksi database siap pakai.', deploy: 'Auto build' },
    vue_starter: { icon: 'fa-brands fa-vuejs', color: 'text-emerald-500 dark:text-emerald-400', bg: 'bg-emerald-100 dark:bg-emerald-500/20', name: 'Vue 3 + Vite', tag: 'Vue JS', tagColor: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300', desc: 'Framework progresif JavaScript untuk membangun antarmuka pengguna yang interaktif dan cepat.', deploy: 'Auto build' },
    nuxt_starter: { icon: 'fa-solid fa-code', color: 'text-emerald-600 dark:text-emerald-300', bg: 'bg-emerald-100 dark:bg-emerald-500/20', name: 'Nuxt.js', tag: 'Vue JS', tagColor: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300', desc: 'Framework intuitif berbasis Vue.js untuk membangun aplikasi web SSR.', deploy: 'Auto build' },
    svelte_starter: { icon: 'fa-solid fa-code', color: 'text-orange-600 dark:text-orange-300', bg: 'bg-orange-100 dark:bg-orange-500/20', name: 'SvelteKit', tag: 'Node.js', tagColor: 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-300', desc: 'Framework super cepat karena tidak menggunakan virtual DOM.', deploy: 'Auto build' },
    ghost_cms: { icon: 'fa-solid fa-ghost', color: 'text-slate-800 dark:text-slate-100', bg: 'bg-slate-900', name: 'Ghost CMS', tag: 'Node.js', tagColor: 'bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200', desc: 'Platform CMS profesional dan modern berbasis Node.js untuk blog & newsletter.', deploy: 'Auto setup' },
};

const frameworkIcons = {
    html: { icon: 'fa-brands fa-html5', color: 'text-orange-500 dark:text-orange-400', name: 'HTML Statis' },
    php: { icon: 'fa-brands fa-php', color: 'text-indigo-500 dark:text-indigo-400', name: 'PHP Native' },
    laravel: { icon: 'fa-brands fa-laravel', color: 'text-red-500 dark:text-red-400', name: 'Laravel' },
    react: { icon: 'fa-brands fa-react', color: 'text-sky-500 dark:text-sky-400', name: 'React JS' },
    nextjs: { icon: 'fa-brands fa-node-js', color: 'text-slate-800 dark:text-slate-100', name: 'Next.js' },
    python: { icon: 'fa-brands fa-python', color: 'text-yellow-500 dark:text-yellow-400', name: 'Python' },
    node: { icon: 'fa-brands fa-node', color: 'text-emerald-500 dark:text-emerald-400', name: 'Node.js' },
    vue: { icon: 'fa-brands fa-vuejs', color: 'text-emerald-500 dark:text-emerald-400', name: 'Vue JS' },
};

const defaultFrameworks = ['html', 'php', 'laravel', 'react', 'nextjs', 'python', 'node', 'vue'];

export default function Create() {
    const { auth } = usePage().props;
    const { data, setData, post, processing } = useForm({
        source_type: 'repo',
        repo_source: '',
        branch: 'main',
        template_key: '',
        project_name: '',
        domain_extension: '.ryaze.my.id',
        framework: '',
        project_zip: null,
    });

    const [uploadFile, setUploadFile] = useState(null);
    const [dragOver, setDragOver] = useState(false);
    const fileInputRef = useRef(null);

    const availableFrameworks = defaultFrameworks;
    const hasSubscription = auth?.user?.has_hosting_subscription;

    function handleDomainPreview() {
        const name = data.project_name.trim().toLowerCase().replace(/[^a-z0-9-]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
        return (name || 'my-awesome-app') + data.domain_extension;
    }

    function handleFileSelect(e) {
        const file = e.target.files?.[0];
        if (file) {
            setUploadFile(file);
            setData('project_zip', file);
        }
    }

    function handleDrop(e) {
        e.preventDefault();
        setDragOver(false);
        const file = e.dataTransfer.files?.[0];
        if (file && file.name.endsWith('.zip')) {
            setUploadFile(file);
            setData('project_zip', file);
            if (fileInputRef.current) {
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInputRef.current.files = dt.files;
            }
        }
    }

    function handleSubmit(e) {
        e.preventDefault();
        post(route('user_hosting.store'), {
            forceFormData: true,
        });
    }

    const stepNum = data.source_type === 'repo' ? '2' : '2';

    return (
        <DashboardLayout title="Deploy Proyek Baru">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i className="fa-solid fa-plus text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Deploy Proyek Baru</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Impor repository Git, unggah file ZIP, atau mulai dengan template siap pakai.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href={route('user_hosting.dashboard')}
                            className="inline-flex justify-center items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-200 px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="mx-auto mt-6">
                <form onSubmit={handleSubmit} encType="multipart/form-data" className="space-y-6">
                    {/* STEP 1: Metode Deploy */}
                    <div className="bg-white dark:bg-[#0d0d18] p-6 border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="font-bold text-[#333] dark:text-white mb-4 flex items-center gap-2 text-sm">
                            <span className="w-6 h-6 bg-[#7c3aed] text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                            Pilih Metode Deploy
                        </h3>
                        <div className="flex flex-col sm:flex-row gap-4">
                            {[
                                { value: 'repo', icon: 'fa-brands fa-github', iconBg: 'bg-slate-900', label: 'Git Repository', desc: 'Clone dari repo Git Anda sendiri' },
                                { value: 'template', icon: 'fa-solid fa-wand-magic-sparkles', iconBg: 'bg-gradient-to-br from-[#7c3aed] to-purple-600', label: 'Gunakan Template', desc: 'Mulai cepat dengan starter code siap pakai', badge: 'New' },
                                { value: 'upload', icon: 'fa-solid fa-file-zipper', iconBg: 'bg-emerald-600', label: 'Upload File ZIP', desc: 'Upload website jadi langsung, tanpa Git', badge: 'New', badgeColor: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' },
                            ].map(opt => (
                                <label key={opt.value} className="relative cursor-pointer flex-1 group">
                                    <input type="radio" name="source_type" value={opt.value} className="peer hidden"
                                        checked={data.source_type === opt.value}
                                        onChange={() => setData('source_type', opt.value)} />
                                    <div className="h-full px-5 py-4 border-2 border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl peer-checked:border-[#7c3aed] peer-checked:bg-[#f5f0ff] dark:peer-checked:bg-[#7c3aed]/10 hover:border-slate-300 dark:hover:border-slate-600 transition-all flex items-center gap-4">
                                        <div className={`w-11 h-11 ${opt.iconBg} rounded-xl flex items-center justify-center shrink-0`}>
                                            <i className={`${opt.icon} text-xl text-white`}></i>
                                        </div>
                                        <div>
                                            <p className="font-bold text-[#333] dark:text-white text-sm flex items-center gap-2">
                                                {opt.label}
                                                {opt.badge && (
                                                    <span className={`${opt.badgeColor || 'bg-[#7c3aed]/10 text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]'} text-[9px] font-bold px-1.5 py-0.5 rounded-md uppercase`}>{opt.badge}</span>
                                                )}
                                            </p>
                                            <p className="text-[11px] text-[#999] dark:text-white/40 mt-0.5">{opt.desc}</p>
                                        </div>
                                        <div className="ml-auto shrink-0 w-5 h-5 border-2 border-slate-300 dark:border-slate-600 rounded-full flex items-center justify-center transition-colors group-has-[:checked]:border-[#7c3aed] group-has-[:checked]:bg-[#7c3aed]">
                                            <div className="w-2 h-2 bg-white dark:bg-slate-800/60 rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                        </div>
                                    </div>
                                </label>
                            ))}
                        </div>
                    </div>

                    {/* STEP 2a: Repository */}
                    {data.source_type === 'repo' && (
                        <div className="bg-white dark:bg-[#0d0d18] p-6 border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="font-bold text-[#333] dark:text-white mb-4 flex items-center gap-2 text-sm">
                                <span className="w-6 h-6 bg-[#7c3aed] text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                Sumber Repository
                            </h3>
                            <div className="space-y-4">
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">URL Git Repository <span className="text-rose-500 dark:text-rose-400">*</span></label>
                                    <input type="url" value={data.repo_source} required
                                        onChange={e => setData('repo_source', e.target.value)}
                                        placeholder="https://github.com/username/my-project"
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Branch</label>
                                    <input type="text" value={data.branch}
                                        onChange={e => setData('branch', e.target.value)}
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                    <p className="text-[11px] text-[#999] dark:text-white/40 mt-1">Cabang Git yang akan di-build (misal: main, master, atau production).</p>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* STEP 2b: Template */}
                    {data.source_type === 'template' && (
                        <div className="bg-white dark:bg-[#0d0d18] p-6 border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="font-bold text-[#333] dark:text-white mb-1 flex items-center gap-2 text-sm">
                                <span className="w-6 h-6 bg-[#7c3aed] text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                Pilih Starter Template
                            </h3>
                            <p className="text-xs text-[#999] dark:text-white/40 mb-5 ml-8">Sistem akan langsung generate file starter — tidak perlu GitHub, tidak perlu konfigurasi apa pun.</p>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                {Object.entries(templates).map(([key, tpl]) => (
                                    <label key={key} className="relative cursor-pointer group">
                                        <input type="radio" name="template_key" value={key} className="peer hidden"
                                            checked={data.template_key === key}
                                            onChange={() => setData('template_key', key)} />
                                        <div className="p-4 border-2 border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl peer-checked:border-[#7c3aed] peer-checked:bg-[#f5f0ff] dark:peer-checked:bg-[#7c3aed]/10 hover:border-slate-300 dark:hover:border-slate-600 hover:shadow-md transition-all">
                                            <div className="flex items-center gap-3 mb-3">
                                                <div className={`w-10 h-10 ${tpl.bg} rounded-lg flex items-center justify-center`}>
                                                    <i className={`${tpl.icon} text-xl ${tpl.color}`}></i>
                                                </div>
                                                <div>
                                                    <p className="font-bold text-[#333] dark:text-white text-sm">{tpl.name}</p>
                                                    <span className={`text-[10px] ${tpl.tagColor} px-2 py-0.5 rounded-full font-medium`}>{tpl.tag}</span>
                                                </div>
                                            </div>
                                            <p className="text-xs text-[#999] dark:text-white/40 leading-relaxed">{tpl.desc}</p>
                                            <div className="mt-3 flex items-center gap-1.5 text-[11px] text-[#999] dark:text-white/30">
                                                <i className={`fa-solid ${tpl.deploy === 'Instant deploy' ? 'fa-bolt' : 'fa-gear fa-spin'}`} style={tpl.deploy !== 'Instant deploy' ? { animationDuration: '3s' } : {}}></i> {tpl.deploy}
                                            </div>
                                        </div>
                                    </label>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* STEP 2c: Upload ZIP */}
                    {data.source_type === 'upload' && (
                        <div className="bg-white dark:bg-[#0d0d18] p-6 border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="font-bold text-[#333] dark:text-white mb-1 flex items-center gap-2 text-sm">
                                <span className="w-6 h-6 bg-[#7c3aed] text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                Unggah File ZIP
                            </h3>
                            <p className="text-xs text-[#999] dark:text-white/40 mb-5 ml-8">Arsip ZIP berisi semua file website Anda (HTML, PHP, build hasil `npm run build`, dsb.). File akan diekstrak otomatis saat deploy.</p>
                            <div
                                onDragOver={e => { e.preventDefault(); setDragOver(true); }}
                                onDragEnter={e => { e.preventDefault(); setDragOver(true); }}
                                onDragLeave={e => { e.preventDefault(); setDragOver(false); }}
                                onDrop={handleDrop}
                                onClick={() => fileInputRef.current?.click()}
                                className={`relative border-2 border-dashed rounded-xl p-10 text-center transition-all cursor-pointer ${
                                    dragOver
                                        ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-500/10'
                                        : 'border-slate-300 dark:border-slate-600 hover:border-emerald-500 hover:bg-emerald-50/50'
                                }`}>
                                <input type="file" ref={fileInputRef} accept=".zip,application/zip,application/x-zip-compressed"
                                    onChange={handleFileSelect} className="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                                {!uploadFile ? (
                                    <div className="pointer-events-none">
                                        <div className="w-16 h-16 mx-auto bg-emerald-100 dark:bg-emerald-500/20 rounded-2xl flex items-center justify-center mb-4">
                                            <i className="fa-solid fa-cloud-arrow-up text-2xl text-emerald-600 dark:text-emerald-300"></i>
                                        </div>
                                        <p className="font-bold text-[#333] dark:text-white text-sm">Klik untuk pilih file ZIP</p>
                                        <p className="text-[11px] text-[#999] dark:text-white/40 mt-1">atau seret & lepas di sini &mdash; maks. 50 MB</p>
                                    </div>
                                ) : (
                                    <div className="pointer-events-none">
                                        <div className="w-16 h-16 mx-auto bg-emerald-100 dark:bg-emerald-500/20 rounded-2xl flex items-center justify-center mb-4">
                                            <i className="fa-solid fa-file-zipper text-2xl text-emerald-600 dark:text-emerald-300"></i>
                                        </div>
                                        <p className="font-bold text-[#333] dark:text-white text-sm break-all px-4">{uploadFile.name}</p>
                                        <p className="text-[11px] text-emerald-600 dark:text-emerald-300 font-medium mt-1">{(uploadFile.size / 1024 / 1024).toFixed(2)} MB</p>
                                    </div>
                                )}
                            </div>
                            <p className="text-[11px] text-[#999] dark:text-white/30 mt-2 ml-8 flex items-center gap-1.5">
                                <i className="fa-solid fa-shield-halved text-emerald-500 dark:text-emerald-400"></i>
                                Terverifikasi otomatis: validasi ekstensi, ukuran, dan keamanan path file.
                            </p>
                        </div>
                    )}

                    {/* STEP 3: Konfigurasi Proyek */}
                    <div className="bg-white dark:bg-[#0d0d18] p-6 border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="font-bold text-[#333] dark:text-white mb-4 flex items-center gap-2 text-sm">
                            <span className="w-6 h-6 bg-[#7c3aed] text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                            Konfigurasi Proyek
                        </h3>

                        <div className="mb-5">
                            <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Nama Proyek & Domain <span className="text-rose-500 dark:text-rose-400">*</span></label>
                            <div className="flex flex-col sm:flex-row shadow-sm">
                                <input type="text" required placeholder="my-awesome-app" value={data.project_name}
                                    onChange={e => setData('project_name', e.target.value)}
                                    className="flex-1 w-full sm:rounded-l-xl sm:rounded-tr-none rounded-t-xl bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition z-10 relative" />
                                <select value={data.domain_extension}
                                    onChange={e => setData('domain_extension', e.target.value)}
                                    className="sm:rounded-r-xl sm:rounded-bl-none rounded-b-xl bg-slate-100 dark:bg-slate-700/50 border border-[#e5e5e5] dark:border-[#1a1a2e] sm:border-l-0 px-4 py-2.5 text-sm font-medium text-[#666] dark:text-white/60 focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition cursor-pointer">
                                    <option value=".ryaze.my.id">.ryaze.my.id</option>
                                    <option value=".ryz.my.id">.ryz.my.id</option>
                                    <option value=".safetalkai.my.id">.safetalkai.my.id</option>
                                </select>
                            </div>
                            <div className="mt-2 text-[11px] text-[#999] dark:text-white/40 font-medium flex items-center">
                                <i className="fa-solid fa-link mr-1.5"></i> Preview: <span className="text-[#7c3aed] dark:text-[#a78bfa] ml-1 font-mono">{handleDomainPreview()}</span>
                            </div>
                        </div>

                        {/* Framework (hanya tampil saat mode repo/upload) */}
                        {data.source_type !== 'template' && (
                            <div>
                                <label className="block text-xs font-bold text-[#333] dark:text-white mb-3">Pilih Framework <span className="text-rose-500 dark:text-rose-400">*</span></label>
                                <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    {availableFrameworks.map((fw, idx) => {
                                        const info = frameworkIcons[fw] || { icon: 'fa-solid fa-code', color: 'text-slate-500 dark:text-slate-400', name: fw.toUpperCase() };
                                        return (
                                            <label key={fw} className="relative cursor-pointer">
                                                <input type="radio" name="framework" value={fw} className="peer hidden"
                                                    checked={data.framework === fw}
                                                    onChange={() => setData('framework', fw)}
                                                    required={idx === 0 && data.source_type !== 'template'} />
                                                <div className="p-3 border-2 border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl peer-checked:border-[#7c3aed] peer-checked:bg-[#f5f0ff] dark:peer-checked:bg-[#7c3aed]/10 hover:border-slate-300 dark:hover:border-slate-600 transition-all text-center">
                                                    <i className={`${info.icon} text-2xl ${info.color} mb-1.5 block`}></i>
                                                    <p className="font-bold text-[#333] dark:text-white text-xs">{info.name}</p>
                                                </div>
                                            </label>
                                        );
                                    })}
                                </div>
                            </div>
                        )}
                    </div>

                    <div className="flex items-center justify-end gap-4 pt-4">
                        <Link href={route('user_hosting.dashboard')}
                            className="text-sm font-bold text-[#999] dark:text-white/40 hover:text-[#333] dark:hover:text-white transition-colors">Batal</Link>
                        {hasSubscription ? (
                            <button type="submit" disabled={processing}
                                className="bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-bold px-8 py-3 rounded-lg shadow-md transition-all disabled:opacity-50">
                                <i className="fa-solid fa-rocket mr-2"></i> {processing ? 'Deploying...' : 'Deploy Sekarang'}
                            </button>
                        ) : (
                            <Link href={route('user_hosting.subscription')}
                                className="bg-rose-500 hover:bg-rose-600 text-white font-bold px-6 py-3 rounded-lg shadow-md transition-all flex items-center gap-2">
                                <i className="fa-solid fa-credit-card"></i> Langganan Untuk Deploy
                            </Link>
                        )}
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}
