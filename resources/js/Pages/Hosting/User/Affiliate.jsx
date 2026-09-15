import { useState } from 'react';
import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Affiliate() {
    const { user, totalReferrals, totalCommission, commissions } = usePage().props;
    const [copied, setCopied] = useState(false);

    const referralLink = `${typeof window !== 'undefined' ? window.location.origin : ''}/ref/${user?.referral_code || user?.id}`;

    const handleCopy = () => {
        navigator.clipboard.writeText(referralLink);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
    };

    const statusBadge = (status) => {
        const styles = {
            approved: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            rejected: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${styles[status] || styles.pending}`}>
                {status || '-'}
            </span>
        );
    };

    return (
        <DashboardLayout title="Affiliate">
            <div className="space-y-6">
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider mb-2">Total Referrals</p>
                        <p className="text-[24px] font-bold text-[#333] dark:text-white">{totalReferrals || 0}</p>
                    </div>
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider mb-2">Total Commission</p>
                        <p className="text-[24px] font-bold text-[#7c3aed]">{formatCurrency(totalCommission)}</p>
                    </div>
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider mb-2">Commission Rate</p>
                        <p className="text-[24px] font-bold text-[#333] dark:text-white">10%</p>
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                    <h3 className="text-[13px] font-bold text-[#333] dark:text-white mb-3">Your Referral Link</h3>
                    <div className="flex items-center gap-2">
                        <div className="flex-1 px-3 py-2 bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e] text-[13px] font-mono text-[#666] dark:text-white/60 truncate">
                            {referralLink}
                        </div>
                        <button onClick={handleCopy} className={`px-4 py-2 text-[13px] font-semibold transition-colors ${copied ? 'bg-green-500 text-white' : 'bg-[#7c3aed] text-white hover:bg-[#6d28d9]'}`}>
                            {copied ? <i className="fa-solid fa-check mr-1"></i> : <i className="fa-solid fa-copy mr-1"></i>}
                            {copied ? 'Copied!' : 'Copy'}
                        </button>
                    </div>
                    <p className="text-[12px] text-[#999] dark:text-white/40 mt-2">Share this link with friends. You earn 10% commission on every payment they make.</p>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="text-[13px] font-bold text-[#333] dark:text-white">Commission History</h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Date</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Referred User</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Amount</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Commission</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {(commissions?.data || commissions || []).length > 0 ? (commissions?.data || commissions).map((c) => (
                                    <tr key={c.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(c.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                        <td className="px-5 py-3 text-[13px] text-[#333] dark:text-white">{c.referred_user?.name || c.user?.name || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{formatCurrency(c.amount)}</td>
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#7c3aed]">{formatCurrency(c.commission)}</td>
                                        <td className="px-5 py-3">{statusBadge(c.status)}</td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-12 text-center">
                                            <i className="fa-solid fa-users-viewfinder text-3xl text-[#e5e5e5] dark:text-white/10 mb-3 block"></i>
                                            <p className="text-[13px] text-[#999] dark:text-white/40">No commissions yet</p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {commissions?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Showing {commissions.from}-{commissions.to} of {commissions.total}
                            </p>
                            <div className="flex items-center gap-1">
                                {commissions.prev_page_url && (
                                    <a href={commissions.prev_page_url} className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</a>
                                )}
                                {commissions.next_page_url && (
                                    <a href={commissions.next_page_url} className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</a>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
