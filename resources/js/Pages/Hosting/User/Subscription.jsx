import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Subscription() {
    const { subscription, plan, usage } = usePage().props;

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
    };

    const handleUpgrade = (planSlug) => {
        router.post('/user/hosting/subscription/upgrade', { plan: planSlug });
    };

    const handleCancel = () => {
        router.post('/user/hosting/subscription/cancel');
    };

    const plans = [
        { slug: 'starter', name: 'Starter', price: 25000, features: ['1 Project', '1 GB Storage', '1 Database', 'Shared CPU'] },
        { slug: 'basic', name: 'Basic', price: 50000, features: ['3 Projects', '5 GB Storage', '3 Databases', 'Shared CPU'] },
        { slug: 'pro', name: 'Pro', price: 100000, features: ['10 Projects', '20 GB Storage', '10 Databases', 'Dedicated CPU'] },
        { slug: 'business', name: 'Business', price: 250000, features: ['Unlimited Projects', '100 GB Storage', 'Unlimited Databases', 'Priority Support'] },
    ];

    return (
        <DashboardLayout title="Subscription">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                    <div className="flex items-center justify-between mb-4">
                        <div>
                            <h2 className="text-[15px] font-bold text-[#333] dark:text-white">Current Plan</h2>
                            <p className="text-[12px] text-[#999] dark:text-white/40 mt-1">Manage your hosting subscription</p>
                        </div>
                        {subscription?.status === 'active' && (
                            <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300">Active</span>
                        )}
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div className="p-4 border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <p className="text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider mb-1">Plan</p>
                            <p className="text-[15px] font-bold text-[#7c3aed]">{plan?.name || subscription?.plan || 'Free'}</p>
                        </div>
                        <div className="p-4 border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <p className="text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider mb-1">Price</p>
                            <p className="text-[15px] font-bold text-[#333] dark:text-white">{formatCurrency(plan?.price || subscription?.price)}/mo</p>
                        </div>
                        <div className="p-4 border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <p className="text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider mb-1">Projects</p>
                            <p className="text-[15px] font-bold text-[#333] dark:text-white">{usage?.projects || 0} / {plan?.projects_limit || '∞'}</p>
                        </div>
                        <div className="p-4 border border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <p className="text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider mb-1">Next Billing</p>
                            <p className="text-[15px] font-bold text-[#333] dark:text-white">{subscription?.next_billing_date || '-'}</p>
                        </div>
                    </div>

                    {subscription?.status === 'active' && (
                        <button onClick={handleCancel} className="text-[13px] text-red-500 hover:text-red-600 font-medium transition-colors">
                            Cancel Subscription
                        </button>
                    )}
                </div>

                <div>
                    <h2 className="text-[13px] font-bold text-[#333] dark:text-white mb-4">Available Plans</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        {plans.map((p) => {
                            const isCurrent = (plan?.slug || subscription?.plan) === p.slug;
                            return (
                                <div key={p.slug} className={`bg-white dark:bg-[#0d0d18] border ${isCurrent ? 'border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e]'} p-5 flex flex-col`}>
                                    {isCurrent && <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-[#7c3aed] text-white self-start mb-3">Current</span>}
                                    <h3 className="text-[15px] font-bold text-[#333] dark:text-white">{p.name}</h3>
                                    <p className="text-[20px] font-bold text-[#7c3aed] mt-2">{formatCurrency(p.price)}<span className="text-[12px] font-normal text-[#999] dark:text-white/40">/mo</span></p>
                                    <ul className="mt-4 space-y-2 flex-1">
                                        {p.features.map((f, i) => (
                                            <li key={i} className="flex items-center gap-2 text-[12px] text-[#666] dark:text-white/60">
                                                <i className="fa-solid fa-check text-[10px] text-[#7c3aed]"></i>{f}
                                            </li>
                                        ))}
                                    </ul>
                                    {!isCurrent && (
                                        <button onClick={() => handleUpgrade(p.slug)} className="mt-4 w-full px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                                            {plans.findIndex(lp => lp.slug === p.slug) < plans.findIndex(lp => lp.slug === (plan?.slug || subscription?.plan)) ? 'Downgrade' : 'Upgrade'}
                                        </button>
                                    )}
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
