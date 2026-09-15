import { usePage, useForm, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Show() {
    const { ticket, auth } = usePage().props;
    const user = auth?.user;
    const { data, setData, post, processing } = useForm({ message: '' });

    const handleReply = (e) => {
        e.preventDefault();
        if (!data.message.trim()) return;
        post(`/admin/tickets/${ticket.hashid || ticket.id}/reply`, {
            onSuccess: () => setData('message', ''),
        });
    };

    const handleClose = () => {
        router.put(`/admin/tickets/${ticket.hashid || ticket.id}/close`);
    };

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

    const allMessages = [
        { id: 'ticket', sender: ticket.user, message: ticket.message, created_at: ticket.created_at, isSystem: false },
        ...(ticket.replies || []).map(r => ({ ...r, isSystem: false })),
    ];

    return (
        <DashboardLayout title={`Tiket - ${ticket.subject}`}>
            <div className="max-w-3xl mx-auto space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <div>
                            <div className="flex items-center gap-2">
                                <Link href="/admin/tickets" className="text-[13px] text-[#999] dark:text-white/40 hover:text-[#7c3aed] transition-colors">
                                    <i className="fa-solid fa-arrow-left mr-1"></i>Kembali
                                </Link>
                            </div>
                            <h2 className="text-sm font-bold text-[#333] dark:text-white mt-1">{ticket.subject}</h2>
                            <div className="flex items-center gap-2 mt-1">
                                {statusBadge(ticket.status)}
                                {priorityBadge(ticket.priority)}
                                <span className="text-[11px] text-[#999] dark:text-white/40">
                                    {ticket.user?.name}
                                </span>
                            </div>
                        </div>
                        {ticket.status !== 'closed' && (
                            <button onClick={handleClose} className="px-4 py-1.5 text-[12px] font-semibold border border-red-300 text-red-600 dark:border-red-500/30 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                <i className="fa-solid fa-lock mr-1"></i>Tutup Tiket
                            </button>
                        )}
                    </div>

                    <div className="p-5 space-y-4 max-h-[500px] overflow-y-auto">
                        {allMessages.map((msg, idx) => {
                            const isOwn = msg.sender?.id === user?.id;
                            return (
                                <div key={msg.id || idx} className={`flex ${isOwn ? 'justify-end' : 'justify-start'}`}>
                                    <div className={`max-w-[75%] ${isOwn ? 'order-2' : ''}`}>
                                        <div className={`px-4 py-3 text-[13px] ${
                                            isOwn
                                                ? 'bg-[#7c3aed] text-white'
                                                : 'bg-[#f5f5f5] dark:bg-white/[0.05] text-[#333] dark:text-white'
                                        }`}>
                                            {msg.message}
                                        </div>
                                        <p className={`text-[11px] text-[#999] dark:text-white/40 mt-1 ${isOwn ? 'text-right' : ''}`}>
                                            {msg.sender?.name || 'System'} &middot; {new Date(msg.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                                        </p>
                                    </div>
                                </div>
                            );
                        })}
                    </div>

                    {ticket.status !== 'closed' && (
                        <div className="border-t border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                            <form onSubmit={handleReply} className="flex items-end gap-3">
                                <textarea
                                    value={data.message}
                                    onChange={(e) => setData('message', e.target.value)}
                                    placeholder="Ketik balasan..."
                                    rows={3}
                                    className="flex-1 px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors resize-none"
                                />
                                <button
                                    type="submit"
                                    disabled={processing || !data.message.trim()}
                                    className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] disabled:opacity-40 transition-colors self-end"
                                >
                                    <i className="fa-solid fa-paper-plane mr-1"></i>Kirim
                                </button>
                            </form>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
