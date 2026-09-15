import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Templates() {
    const { availableTemplates } = usePage().props;

    return (
        <DashboardLayout title="Templates">
            <div className="space-y-6">
                <div>
                    <h2 className="text-[13px] font-bold text-[#333] dark:text-white">Available Templates</h2>
                    <p className="text-[12px] text-[#999] dark:text-white/40 mt-1">Select a template to quickly deploy your project.</p>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {availableTemplates?.length > 0 ? availableTemplates.map((template, i) => (
                        <div key={template.id || i} className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed]/50 transition-all">
                            <div className="p-5">
                                <div className="flex items-center gap-3 mb-3">
                                    {template.icon ? (
                                        <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 flex items-center justify-center">
                                            <i className={`${template.icon} text-[#7c3aed] text-lg`}></i>
                                        </div>
                                    ) : (
                                        <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/10 flex items-center justify-center">
                                            <i className="fa-solid fa-code text-[#7c3aed] text-lg"></i>
                                        </div>
                                    )}
                                    <div>
                                        <h3 className="text-[14px] font-bold text-[#333] dark:text-white">{template.name}</h3>
                                        {template.version && <p className="text-[11px] text-[#999] dark:text-white/40">v{template.version}</p>}
                                    </div>
                                </div>
                                <p className="text-[12px] text-[#666] dark:text-white/50 mb-4 line-clamp-3">{template.description || 'No description available.'}</p>
                                {template.features && (
                                    <div className="flex flex-wrap gap-1 mb-4">
                                        {template.features.map((f, fi) => (
                                            <span key={fi} className="px-2 py-0.5 text-[10px] font-medium bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] dark:text-[#a78bfa]">
                                                {f}
                                            </span>
                                        ))}
                                    </div>
                                )}
                                <Link
                                    href={`/user/hosting/create?template=${template.slug || template.id}`}
                                    className="block w-full text-center px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors"
                                >
                                    <i className="fa-solid fa-rocket mr-1.5"></i>Deploy Template
                                </Link>
                            </div>
                        </div>
                    )) : (
                        <div className="col-span-full py-12 text-center">
                            <i className="fa-solid fa-layer-group text-3xl text-[#e5e5e5] dark:text-white/10 mb-3 block"></i>
                            <p className="text-[13px] text-[#999] dark:text-white/40">No templates available</p>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
