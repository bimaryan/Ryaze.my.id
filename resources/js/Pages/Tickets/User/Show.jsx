import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, Link } from '@inertiajs/react';
import { useState, useRef, useEffect } from 'react';

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) + ', ' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
}

function statusBadge(status) {
    if (status === 'open') return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-300 border border-amber-200 dark:border-amber-500/40">Terbuka</span>;
    if (status === 'answered') return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40">Dijawab</span>;
    return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-50 dark:bg-slate-500/10 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-500/40">Tertutup</span>;
}

export default function Show({ ticket }) {
    const { data, setData, post, processing, reset } = useForm({
        message: '',
        attachment: null,
    });
    const [emojiOpen, setEmojiOpen] = useState(false);
    const textareaRef = useRef(null);
    const chatEndRef = useRef(null);

    const emojis = ['😀','😂','😍','🤔','👍','👋','🎉','🔥','💯','✅','⭐','❤️','🙏','💪','🚀','📦','🔧','⚡'];

    useEffect(() => {
        chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [ticket?.replies?.length]);

    const autoResize = (el) => {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 150) + 'px';
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!data.message.trim() && !data.attachment) return;
        post(`/user/hosting/tickets/${ticket.hashid}/reply`, {
            onSuccess: () => {
                reset();
                if (textareaRef.current) textareaRef.current.style.height = 'auto';
            },
        });
    };

    const replies = ticket?.replies || [];
    const isClosed = ticket?.status === 'closed';

    return (
        <DashboardLayout title={`Tiket: ${ticket?.subject || ''}`}>
            <div className="mb-4">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-life-ring text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div className="flex-1 min-w-0">
                        <h1 className="text-lg font-bold text-[#333] dark:text-white truncate">{ticket?.subject}</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">
                            <span className="font-mono">#{ticket?.hashid}</span> &middot; {ticket?.department}
                        </p>
                    </div>
                    <div className="flex items-center gap-2">
                        {statusBadge(ticket?.status)}
                        <Link href="/user/hosting/tickets" className="inline-flex items-center gap-2 bg-[#fafafa] dark:bg-white/5 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:bg-[#f5f0ff] dark:hover:bg-white/10 text-[#666] dark:text-white/60 px-4 py-2 text-sm font-medium transition">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-2xl overflow-hidden flex flex-col h-[700px]">
                <div className="flex-1 overflow-y-auto p-5 space-y-4">
                    {replies.map((reply, idx) => {
                        const isSelf = !reply.is_admin;
                        return (
                            <div key={reply.id || idx} className={`flex ${isSelf ? 'justify-end' : 'justify-start'}`}>
                                <div className="max-w-[75%]">
                                    {!isSelf && (
                                        <p className="text-[11px] font-medium text-[#999] dark:text-white/40 mb-1 ml-1">Admin</p>
                                    )}
                                    <div className={`px-4 py-3 rounded-2xl text-sm leading-relaxed ${
                                        isSelf
                                            ? 'bg-emerald-500 text-white rounded-br-md'
                                            : 'bg-[#f5f0ff] dark:bg-white/5 text-[#333] dark:text-white border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-bl-md'
                                    }`}>
                                        <p className="whitespace-pre-wrap">{reply.message}</p>
                                        {reply.attachment && (
                                            <div className="mt-2 pt-2 border-t border-white/20">
                                                <a href={reply.attachment_url || reply.attachment} target="_blank" rel="noopener noreferrer" className={`text-xs underline ${isSelf ? 'text-white/80' : 'text-[#7c3aed] dark:text-[#a78bfa]'}`}>
                                                    <i className="fa-solid fa-paperclip me-1"></i>Lampiran
                                                </a>
                                            </div>
                                        )}
                                    </div>
                                    <div className={`flex items-center gap-1.5 mt-1 ${isSelf ? 'justify-end mr-1' : 'ml-1'}`}>
                                        <span className="text-[10px] text-[#999] dark:text-white/30">{formatDate(reply.created_at)}</span>
                                        {isSelf && reply.read && (
                                            <i className="fa-solid fa-check-double text-[10px] text-emerald-300"></i>
                                        )}
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                    <div ref={chatEndRef}></div>
                </div>

                {isClosed ? (
                    <div className="p-6 border-t border-[#e5e5e5] dark:border-[#1a1a2e] text-center">
                        <i className="fa-solid fa-lock text-2xl text-slate-300 dark:text-slate-400 mb-2 block"></i>
                        <p className="text-sm font-medium text-[#999] dark:text-white/40">Tiket ini sudah ditutup. Tidak dapat membalas lagi.</p>
                    </div>
                ) : (
                    <form onSubmit={handleSubmit} className="p-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="flex items-end gap-3">
                            <div className="relative">
                                <button type="button" onClick={() => setEmojiOpen(!emojiOpen)} className="w-10 h-10 flex items-center justify-center text-[#999] dark:text-white/40 hover:text-[#7c3aed] dark:hover:text-white transition-colors">
                                    <i className="fa-solid fa-face-smile"></i>
                                </button>
                                {emojiOpen && (
                                    <div className="absolute bottom-full mb-2 left-0 bg-white dark:bg-[#1a1025] border border-[#e5e5e5] dark:border-[#2d1f42] rounded-xl shadow-lg p-3 grid grid-cols-6 gap-1 z-10 w-[220px]">
                                        {emojis.map((em) => (
                                            <button key={em} type="button" onClick={() => { setData('message', data.message + em); setEmojiOpen(false); }} className="w-8 h-8 flex items-center justify-center text-lg hover:bg-[#f5f0ff] dark:hover:bg-white/5 rounded transition">
                                                {em}
                                            </button>
                                        ))}
                                    </div>
                                )}
                            </div>
                            <div className="flex-1 relative">
                                <textarea
                                    ref={textareaRef}
                                    rows={1}
                                    value={data.message}
                                    onChange={(e) => { setData('message', e.target.value); autoResize(e.target); }}
                                    onKeyDown={(e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); handleSubmit(e); } }}
                                    placeholder="Ketik balasan..."
                                    className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition resize-none"
                                />
                            </div>
                            <label className="w-10 h-10 flex items-center justify-center text-[#999] dark:text-white/40 hover:text-[#7c3aed] dark:hover:text-white transition-colors cursor-pointer">
                                <i className="fa-solid fa-image"></i>
                                <input type="file" accept="image/*" className="hidden" onChange={(e) => setData('attachment', e.target.files[0])} />
                            </label>
                            <button type="submit" disabled={processing || (!data.message.trim() && !data.attachment)} className="w-10 h-10 flex items-center justify-center bg-[#7c3aed] text-white rounded-xl hover:bg-[#6d28d9] disabled:opacity-40 disabled:cursor-not-allowed transition">
                                <i className="fa-solid fa-paper-plane text-sm"></i>
                            </button>
                        </div>
                        {data.attachment && (
                            <div className="mt-2 flex items-center gap-2 text-xs text-[#666] dark:text-white/60">
                                <i className="fa-solid fa-image"></i>
                                <span>{data.attachment.name}</span>
                                <button type="button" onClick={() => { setData('attachment', null); }} className="text-rose-500 hover:underline">Hapus</button>
                            </div>
                        )}
                    </form>
                )}
            </div>
        </DashboardLayout>
    );
}
