import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

const templates = [
    { value: 'laravel', label: 'Laravel', icon: 'fa-brands fa-laravel', color: 'text-red-500' },
    { value: 'nodejs', label: 'Node.js', icon: 'fa-brands fa-node-js', color: 'text-green-500' },
    { value: 'static', label: 'Static HTML', icon: 'fa-solid fa-code', color: 'text-blue-500' },
    { value: 'nextjs', label: 'Next.js', icon: 'fa-brands fa-react', color: 'text-cyan-500' },
    { value: 'php', label: 'PHP', icon: 'fa-brands fa-php', color: 'text-purple-500' },
    { value: 'python', label: 'Python', icon: 'fa-brands fa-python', color: 'text-yellow-500' },
    { value: 'wordpress', label: 'WordPress', icon: 'fa-brands fa-wordpress', color: 'text-blue-600' },
    { value: 'django', label: 'Django', icon: 'fa-solid fa-leaf', color: 'text-green-600' },
];

export default function Create() {
    const { flash } = usePage().props;
    const urlParams = typeof window !== 'undefined' ? new URLSearchParams(window.location.search) : null;
    const preselectedTemplate = urlParams?.get('template') || '';

    const [form, setForm] = useState({
        project_name: '',
        template: preselectedTemplate,
        custom_domain: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        router.form(form).post('/user/hosting/create');
    };

    return (
        <DashboardLayout title="Deploy Project">
            <div className="max-w-2xl mx-auto space-y-6">
                {flash?.success && (
                    <div className="px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-300">
                        {flash.success}
                    </div>
                )}

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Deploy New Project</h2>
                        <p className="text-[12px] text-[#999] dark:text-white/40 mt-1">Choose a template and deploy your project in seconds.</p>
                    </div>

                    <form onSubmit={handleSubmit} className="p-5 space-y-5">
                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Project Name</label>
                            <input
                                type="text"
                                value={form.project_name}
                                onChange={(e) => setForm({ ...form, project_name: e.target.value })}
                                placeholder="my-awesome-project"
                                required
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                            />
                        </div>

                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-2">Template</label>
                            <div className="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                {templates.map((t) => (
                                    <button
                                        key={t.value}
                                        type="button"
                                        onClick={() => setForm({ ...form, template: t.value })}
                                        className={`flex flex-col items-center gap-2 p-3 border text-[12px] font-medium transition-all ${
                                            form.template === t.value
                                                ? 'border-[#7c3aed] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed]'
                                                : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed]/50'
                                        }`}
                                    >
                                        <i className={`${t.icon} text-xl ${t.color}`}></i>
                                        <span>{t.label}</span>
                                    </button>
                                ))}
                            </div>
                        </div>

                        <div>
                            <label className="block text-[12px] font-bold text-[#666] dark:text-white/60 uppercase tracking-wider mb-1.5">Custom Domain (Optional)</label>
                            <input
                                type="text"
                                value={form.custom_domain}
                                onChange={(e) => setForm({ ...form, custom_domain: e.target.value })}
                                placeholder="example.com"
                                className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                            />
                            <p className="text-[11px] text-[#999] dark:text-white/30 mt-1">Leave empty for a subdomain (project.ryaze.my.id)</p>
                        </div>

                        <div className="flex items-center gap-3 pt-2">
                            <button
                                type="submit"
                                disabled={!form.project_name || !form.template}
                                className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                            >
                                <i className="fa-solid fa-rocket mr-1.5"></i>Deploy Now
                            </button>
                            <Link href="/user/hosting/projects" className="px-5 py-2 text-[13px] font-medium text-[#666] dark:text-white/60 hover:text-[#7c3aed] transition-colors">
                                Cancel
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}
