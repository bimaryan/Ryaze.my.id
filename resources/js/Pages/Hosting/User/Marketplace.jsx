import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Marketplace() {
    const { templates } = usePage().props;

    return (
        <DashboardLayout title="Marketplace">
            <div className="space-y-6">
                <div>
                    <h2 className="text-[13px] font-bold text-[#333] dark:text-white">Browse Templates</h2>
                    <p className="text-[12px] text-[#999] dark:text-white/40 mt-1">Choose a template to deploy your project instantly.</p>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {templates?.length > 0 ? templates.map((template) => (
                        <div key={template.id} className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] hover:border-[#7c3aed]/50 transition-all group">
                            {template.image && (
                                <div className="h-40 overflow-hidden border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <img src={template.image} alt={template.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                </div>
                            )}
                            <div className="p-4">
                                <div className="flex items-center gap-2 mb-2">
                                    {template.icon && <i className={`${template.icon} text-[#7c3aed]`}></i>}
                                    <h3 className="text-[14px] font-bold text-[#333] dark:text-white">{template.name}</h3>
                                </div>
                                <p className="text-[12px] text-[#666] dark:text-white/50 mb-4 line-clamp-2">{template.description || 'No description available.'}</p>
                                {template.tags && (
                                    <div className="flex flex-wrap gap-1 mb-4">
                                        {template.tags.map((tag, i) => (
                                            <span key={i} className="px-2 py-0.5 text-[10px] font-medium bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] dark:text-[#a78bfa]">
                                                {tag}
                                            </span>
                                        ))}
                                    </div>
                                )}
                                <Link
                                    href={`/user/hosting/create?template=${template.slug || template.id}`}
                                    className="block w-full text-center px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors"
                                >
                                    <i className="fa-solid fa-rocket mr-1.5"></i>Deploy
                                </Link>
                            </div>
                        </div>
                    )) : (
                        <div className="col-span-full py-12 text-center">
                            <i className="fa-solid fa-store text-3xl text-[#e5e5e5] dark:text-white/10 mb-3 block"></i>
                            <p className="text-[13px] text-[#999] dark:text-white/40">No templates available</p>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
