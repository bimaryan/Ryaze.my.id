import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, router } from '@inertiajs/react';
import { useState } from 'react';

const apps = [
    { key: 'wordpress', name: 'WordPress', desc: 'Platform CMS terpopuler di dunia. Sudah termasuk database otomatis dan siap digunakan.', icon: 'fa-brands fa-wordpress', iconColor: 'text-blue-600 dark:text-blue-300', iconBg: 'bg-blue-50 dark:bg-blue-500/10 border-blue-100/50', badge: 'CMS', badgeColor: 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300' },
    { key: 'laravel_starter_13', name: 'Laravel 13', desc: 'Framework PHP modern versi terbaru. Cepat, aman, dan dirancang untuk developer profesional.', icon: 'fa-brands fa-laravel', iconColor: 'text-rose-500 dark:text-rose-400', iconBg: 'bg-rose-50 dark:bg-rose-500/10 border-rose-100/50', badge: 'FRAMEWORK', badgeColor: 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300' },
    { key: 'react_starter', name: 'React + Vite', desc: 'Bangun antarmuka dinamis super cepat dengan React dan build tool generasi terbaru (Vite).', icon: 'fa-brands fa-react', iconColor: 'text-sky-500 dark:text-sky-400', iconBg: 'bg-sky-50 dark:bg-sky-500/10 border-sky-100/50', badge: 'FRONTEND', badgeColor: 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300' },
    { key: 'nextjs_starter', name: 'Next.js', desc: 'Framework React untuk produksi dengan fitur rendering SSR/SSG, dan optimasi bawaan.', icon: 'fa-brands fa-node-js', iconColor: 'text-slate-900 dark:text-slate-50', iconBg: 'bg-slate-100 dark:bg-slate-700/50 border-slate-200/50', badge: 'FULLSTACK', badgeColor: 'bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200', isSvg: true },
    { key: 'node_express', name: 'Node + Express', desc: 'Bangun REST API yang cepat, skalabel, dan efisien dengan Node.js dan Express.', icon: 'fa-brands fa-node-js', iconColor: 'text-green-600 dark:text-green-300', iconBg: 'bg-green-50 dark:bg-green-500/10 border-green-100/50', badge: 'BACKEND', badgeColor: 'bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-300' },
    { key: 'html_landing', name: 'HTML Landing', desc: 'Situs statis super ringan untuk landing page. Tanpa database, loading instan.', icon: 'fa-brands fa-html5', iconColor: 'text-orange-500 dark:text-orange-400', iconBg: 'bg-orange-50 dark:bg-orange-500/10 border-orange-100/50', badge: 'STATIC', badgeColor: 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-300' },
    { key: 'tailwind_starter', name: 'Tailwind CSS', desc: 'Starter template UI modern dengan Tailwind CSS CDN. Langsung siap pakai tanpa build step.', icon: 'fa-solid fa-wind', iconColor: 'text-cyan-500 dark:text-cyan-400', iconBg: 'bg-cyan-50 dark:bg-cyan-500/10 border-cyan-100/50', badge: 'FRONTEND', badgeColor: 'bg-cyan-100 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300' },
    { key: 'vue_starter', name: 'Vue 3 + Vite', desc: 'Framework JavaScript yang progresif dan mudah digunakan untuk membangun UI interaktif.', icon: 'fa-brands fa-vuejs', iconColor: 'text-emerald-500 dark:text-emerald-400', iconBg: 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-100/50', badge: 'FRONTEND', badgeColor: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' },
    { key: 'nuxt_starter', name: 'Nuxt.js', desc: 'Framework intuitif berbasis Vue.js untuk membangun aplikasi web SSR dan static-site generation.', icon: 'fa-solid fa-code', iconColor: 'text-emerald-600 dark:text-emerald-300', iconBg: 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-100/50', badge: 'FULLSTACK', badgeColor: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300', isSvg: true },
    { key: 'svelte_starter', name: 'SvelteKit', desc: 'Framework super cepat karena tidak menggunakan virtual DOM. Performa kelas atas.', icon: 'fa-solid fa-code', iconColor: 'text-orange-600 dark:text-orange-300', iconBg: 'bg-orange-50 dark:bg-orange-500/10 border-orange-100/50', badge: 'FULLSTACK', badgeColor: 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-300', isSvg: true },
    { key: 'ghost_cms', name: 'Ghost CMS', desc: 'Platform penerbitan profesional dan modern berbasis Node.js untuk blog & newsletter.', icon: 'fa-solid fa-ghost', iconColor: 'text-white', iconBg: 'bg-slate-900 border-slate-700', badge: 'CMS', badgeColor: 'bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200' },
    { key: 'php_basic', name: 'PHP Basic (Native)', desc: 'Boilerplate aplikasi PHP murni tanpa framework tambahan. Cepat dan klasik.', icon: 'fa-brands fa-php', iconColor: 'text-indigo-500 dark:text-indigo-400', iconBg: 'bg-indigo-50 dark:bg-indigo-500/10 border-indigo-100/50', badge: 'BACKEND', badgeColor: 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' },
];

function DeployCard({ app }) {
    const { data, setData, post, processing } = useForm({
        source_type: 'template',
        template_key: app.key,
        project_name: '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('user_hosting.store'));
    }

    return (
        <div className="bg-white dark:bg-[#0d0d18] rounded-2xl border border-[#e5e5e5] dark:border-[#1a1a2e] shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
            <div className={`absolute top-4 right-4 text-xs font-black ${app.badgeColor} px-2.5 py-1 rounded-full z-10 uppercase tracking-wide`}>{app.badge}</div>
            <div className="p-6 pb-0 flex-1 relative z-10">
                <div className={`w-16 h-16 ${app.iconBg} rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border`}>
                    {app.isSvg ? (
                        <svg viewBox="0 0 128 128" className="w-8 h-8 fill-current"><path d="M72.9 22L45.4 69.5h16.6l10.9-18.8 24.6 42.6H128L72.9 22zM28.4 46.2L0 95.3h33.2l16.1-27.9 10.9 18.8h33.2L28.4 46.2z"></path></svg>
                    ) : (
                        <i className={`${app.icon} text-3xl ${app.iconColor}`}></i>
                    )}
                </div>
                <h3 className="text-lg font-bold text-[#333] dark:text-white mb-1 group-hover:text-[#7c3aed] dark:group-hover:text-[#a78bfa] transition-colors">{app.name}</h3>
                <p className="text-sm text-[#999] dark:text-white/40 mb-6 leading-relaxed">{app.desc}</p>
            </div>
            <div className="p-6 pt-0 mt-auto relative z-10">
                <form onSubmit={handleSubmit}>
                    <input type="hidden" name="source_type" value="template" />
                    <input type="hidden" name="template_key" value={app.key} />
                    <div className="flex gap-2">
                        <input type="text" placeholder="Nama Proyek" required value={data.project_name}
                            onChange={e => setData('project_name', e.target.value)}
                            className="flex-1 text-sm px-3 py-2 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl focus:ring-2 focus:ring-[#7c3aed] outline-none bg-white dark:bg-[#0d0d18] text-[#333] dark:text-white" />
                        <button type="submit" disabled={processing}
                            className="bg-slate-900 hover:bg-[#7c3aed] text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0 disabled:opacity-50" title="Deploy Now">
                            <i className="fa-solid fa-play"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default function Marketplace() {
    return (
        <DashboardLayout title="App Marketplace">
            {/* Hero Banner */}
            <div className="relative bg-gradient-to-r from-[#7c3aed] to-purple-700 rounded-2xl p-8 mb-8 overflow-hidden shadow-lg border border-[#7c3aed]/50">
                <div className="absolute right-0 top-0 opacity-10 pointer-events-none">
                    <i className="fa-solid fa-rocket text-[200px] -mt-10 -mr-10"></i>
                </div>
                <div className="relative z-10 max-w-2xl">
                    <h1 className="text-3xl font-black text-white mb-2 tracking-tight">App Marketplace</h1>
                    <p className="text-indigo-100 text-sm mb-6 leading-relaxed">
                        Deploy aplikasi modern dalam hitungan detik. Tanpa konfigurasi manual, tanpa setup server. Pilih framework favorit Anda dan biarkan Ryaze Auto-Deployer melakukan sisanya.
                    </p>
                    <div className="flex items-center gap-3">
                        <span className="bg-white/20 text-white text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm border border-white/10">
                            <i className="fa-solid fa-bolt text-yellow-300 mr-1"></i> 1-Click Install
                        </span>
                        <span className="bg-white/20 text-white text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm border border-white/10">
                            <i className="fa-solid fa-shield-halved text-emerald-300 mr-1"></i> Production Ready
                        </span>
                    </div>
                </div>
            </div>

            {/* App Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                {apps.map(app => (
                    <DeployCard key={app.key} app={app} />
                ))}
            </div>
        </DashboardLayout>
    );
}
