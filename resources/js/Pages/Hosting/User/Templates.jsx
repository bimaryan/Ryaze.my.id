import DashboardLayout from '../../../Layouts/DashboardLayout';
import { Link } from '@inertiajs/react';

const templateDetails = {
    html_landing: { icon: 'fa-brands fa-html5', color: 'text-orange-500 dark:text-orange-400', bg: 'bg-orange-50 dark:bg-orange-500/10 border-orange-100/50', badge: 'STATIC', badgeColor: 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-300' },
    tailwind_starter: { icon: 'fa-solid fa-wind', color: 'text-cyan-500 dark:text-cyan-400', bg: 'bg-cyan-50 dark:bg-cyan-500/10 border-cyan-100/50', badge: 'FRONTEND', badgeColor: 'bg-cyan-100 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300' },
    php_basic: { icon: 'fa-brands fa-php', color: 'text-indigo-500 dark:text-indigo-400', bg: 'bg-indigo-50 dark:bg-indigo-500/10 border-indigo-100/50', badge: 'BACKEND', badgeColor: 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' },
    wordpress: { icon: 'fa-brands fa-wordpress', color: 'text-blue-600 dark:text-blue-300', bg: 'bg-blue-50 dark:bg-blue-500/10 border-blue-100/50', badge: 'CMS', badgeColor: 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300' },
    laravel_starter_13: { icon: 'fa-brands fa-laravel', color: 'text-red-500 dark:text-red-400', bg: 'bg-red-50 dark:bg-red-500/10 border-red-100/50', badge: 'FRAMEWORK', badgeColor: 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300' },
    laravel_starter_12: { icon: 'fa-brands fa-laravel', color: 'text-red-500 dark:text-red-400', bg: 'bg-red-50 dark:bg-red-500/10 border-red-100/50', badge: 'FRAMEWORK', badgeColor: 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300' },
    laravel_starter_11: { icon: 'fa-brands fa-laravel', color: 'text-red-500 dark:text-red-400', bg: 'bg-red-50 dark:bg-red-500/10 border-red-100/50', badge: 'FRAMEWORK', badgeColor: 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300' },
    laravel_starter_10: { icon: 'fa-brands fa-laravel', color: 'text-red-500 dark:text-red-400', bg: 'bg-red-50 dark:bg-red-500/10 border-red-100/50', badge: 'FRAMEWORK', badgeColor: 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300' },
    react_starter: { icon: 'fa-brands fa-react', color: 'text-sky-500 dark:text-sky-400', bg: 'bg-sky-50 dark:bg-sky-500/10 border-sky-100/50', badge: 'FRONTEND', badgeColor: 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300' },
    nextjs_starter: { icon: 'fa-brands fa-node-js', color: 'text-slate-800 dark:text-slate-100', bg: 'bg-slate-100 dark:bg-slate-700/50 border-slate-200/50', badge: 'FULLSTACK', badgeColor: 'bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200' },
    node_express: { icon: 'fa-brands fa-node', color: 'text-emerald-600 dark:text-emerald-300', bg: 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-100/50', badge: 'BACKEND', badgeColor: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' },
    vue_starter: { icon: 'fa-brands fa-vuejs', color: 'text-emerald-500 dark:text-emerald-400', bg: 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-100/50', badge: 'FRONTEND', badgeColor: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' },
    nuxt_starter: { icon: 'fa-solid fa-code', color: 'text-emerald-600 dark:text-emerald-300', bg: 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-100/50', badge: 'FULLSTACK', badgeColor: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' },
    svelte_starter: { icon: 'fa-solid fa-code', color: 'text-orange-600 dark:text-orange-300', bg: 'bg-orange-50 dark:bg-orange-500/10 border-orange-100/50', badge: 'FULLSTACK', badgeColor: 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-300' },
    ghost_cms: { icon: 'fa-solid fa-ghost', color: 'text-slate-800 dark:text-slate-100', bg: 'bg-slate-900 border-slate-700', badge: 'CMS', badgeColor: 'bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200' },
};

export default function Templates({ availableTemplates }) {
    const templates = availableTemplates || [];

    return (
        <DashboardLayout title="Template Marketplace">
            {/* Hero */}
            <div className="relative bg-gradient-to-r from-[#7c3aed] to-purple-700 rounded-2xl p-8 mb-8 overflow-hidden shadow-lg border border-[#7c3aed]/50">
                <div className="absolute right-0 top-0 opacity-10 pointer-events-none">
                    <i className="fa-solid fa-layer-group text-[200px] -mt-10 -mr-10"></i>
                </div>
                <div className="relative z-10 max-w-2xl">
                    <h1 className="text-3xl font-black text-white mb-2 tracking-tight">Template Marketplace</h1>
                    <p className="text-indigo-100 text-sm mb-6 leading-relaxed">
                        Mulai proyek Anda dengan template starter yang sudah dikonfigurasi. Pilih framework favorit dan deploy instan tanpa setup manual.
                    </p>
                    <div className="flex items-center gap-3">
                        <span className="bg-white/20 text-white text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm border border-white/10">
                            <i className="fa-solid fa-bolt text-yellow-300 mr-1"></i> 1-Click Deploy
                        </span>
                        <span className="bg-white/20 text-white text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm border border-white/10">
                            <i className="fa-solid fa-shield-halved text-emerald-300 mr-1"></i> Production Ready
                        </span>
                    </div>
                </div>
            </div>

            {/* Template Grid */}
            {templates.length === 0 ? (
                <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] p-12 text-center">
                    <div className="w-16 h-16 bg-slate-100 dark:bg-slate-700/50 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i className="fa-solid fa-layer-group"></i>
                    </div>
                    <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Belum ada template</h3>
                    <p className="text-[#999] dark:text-white/40 text-sm">Template akan tersedia segera.</p>
                </div>
            ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    {templates.map((template) => {
                        const detail = templateDetails[template.key] || {
                            icon: 'fa-solid fa-code',
                            color: 'text-slate-500 dark:text-slate-400',
                            bg: 'bg-slate-100 dark:bg-slate-700/50 border-slate-200/50',
                            badge: 'APP',
                            badgeColor: 'bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200',
                        };

                        return (
                            <div key={template.key} className="bg-white dark:bg-[#0d0d18] rounded-2xl border border-[#e5e5e5] dark:border-[#1a1a2e] shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full overflow-hidden relative">
                                <div className={`absolute top-4 right-4 text-xs font-black ${detail.badgeColor} px-2.5 py-1 rounded-full z-10 uppercase tracking-wide`}>
                                    {detail.badge}
                                </div>
                                <div className="p-6 pb-0 flex-1 relative z-10">
                                    <div className={`w-16 h-16 ${detail.bg} rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-inner border`}>
                                        <i className={`${detail.icon} text-3xl ${detail.color}`}></i>
                                    </div>
                                    <h3 className="text-lg font-bold text-[#333] dark:text-white mb-1 group-hover:text-[#7c3aed] dark:group-hover:text-[#a78bfa] transition-colors">
                                        {template.name}
                                    </h3>
                                    <p className="text-sm text-[#999] dark:text-white/40 mb-4 leading-relaxed">
                                        {template.description || 'Template starter untuk proyek Anda.'}
                                    </p>
                                    {template.deploy && (
                                        <div className="flex items-center gap-1.5 text-[11px] text-[#999] dark:text-white/30 mb-4">
                                            <i className={`fa-solid ${template.deploy === 'Instant deploy' ? 'fa-bolt' : 'fa-gear fa-spin'}`}
                                                style={template.deploy !== 'Instant deploy' ? { animationDuration: '3s' } : {}}></i>
                                            {template.deploy}
                                        </div>
                                    )}
                                </div>
                                <div className="p-6 pt-0 mt-auto relative z-10">
                                    <Link
                                        href={route('user_hosting.create') + '?template=' + template.key}
                                        className="w-full inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-[#7c3aed] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm"
                                    >
                                        <i className="fa-solid fa-rocket text-xs"></i> Deploy Template
                                    </Link>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}
        </DashboardLayout>
    );
}
