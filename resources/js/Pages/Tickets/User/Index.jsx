import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Index() {
    const { tickets } = usePage().props;

    const statusBadge = (status) => {
        const map = {
            open: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            answered: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            closed: 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-white/50',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${map[status] || map.open}`}>
                {status}
            </span>
        );
    };

    const priorityBadge = (priority) => {
        const map = {
            low: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            medium: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            high: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${map[priority] || map.medium}`}>
                {priority}
            </span>
        );
    };

    return (
        <DashboardLayout title="Tiket Saya">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Semua Tiket</h2>
                        <Link href="/user/tickets/create" className="px-4 py-1.5 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                            <i className="fa-solid fa-plus mr-1"></i>Buat Tiket
                        </Link>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Subjek</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Prioritas</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Update</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {tickets?.data?.length > 0 ? tickets.data.map((ticket) => (
                                    <tr key={ticket.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3">
                                            <Link href={`/user/tickets/${ticket.hashid || ticket.id}`} className="text-[13px] font-semibold text-[#333] dark:text-white hover:text-[#7c3aed] transition-colors">
                                                {ticket.subject}
                                            </Link>
                                        </td>
                                        <td className="px-5 py-3">{statusBadge(ticket.status)}</td>
                                        <td className="px-5 py-3">{priorityBadge(ticket.priority)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(ticket.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="4" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Belum ada tiket
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {tickets?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {tickets.from}-{tickets.to} dari {tickets.total} tiket
                            </p>
                            <div className="flex items-center gap-1">
                                {tickets.prev_page_url && (
                                    <Link href={tickets.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(tickets.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${tickets.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${tickets.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {tickets.next_page_url && (
                                    <Link href={tickets.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
