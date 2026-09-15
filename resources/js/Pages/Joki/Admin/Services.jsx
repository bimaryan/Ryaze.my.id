import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Services() {
    const { services } = usePage().props;

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
    };

    const statusBadge = (status) => {
        const styles = {
            active: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            inactive: 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${styles[status] || styles.inactive}`}>
                {status || '-'}
            </span>
        );
    };

    return (
        <DashboardLayout title="Layanan Joki">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Semua Layanan</h2>
                        <Link
                            href="/admin/joki/services/create"
                            className="px-3 py-1.5 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors"
                        >
                            <i className="fa-solid fa-plus mr-1"></i> Tambah
                        </Link>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Name</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Price</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Description</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Duration</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {services?.length > 0 ? services.map((service) => (
                                    <tr key={service.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3">
                                            <span className="text-[13px] font-semibold text-[#333] dark:text-white">{service.name || '-'}</span>
                                        </td>
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{formatCurrency(service.price)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60 truncate max-w-[250px]">{service.description || '-'}</td>
                                        <td className="px-5 py-3">{statusBadge(service.status)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">{service.duration ? `${service.duration} hari` : '-'}</td>
                                        <td className="px-5 py-3">
                                            <Link
                                                href={`/admin/joki/services/${service.id}/edit`}
                                                className="text-[13px] font-medium text-[#7c3aed] hover:underline transition-colors"
                                            >
                                                Edit
                                            </Link>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="6" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada layanan
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
