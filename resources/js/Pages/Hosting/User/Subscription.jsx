import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, usePage, router } from '@inertiajs/react';
import { useState } from 'react';

const plans = [
    {
        id: 'free',
        name: 'Free',
        badge: null,
        price_normal: 0,
        price_promo: 0,
        features: [
            '1 Proyek',
            '256 MB Storage',
            '100 MB Bandwidth/hari',
            'Subdomain .ryaze.my.id',
            'SSL Otomatis',
            'Build Logs',
            'Web Terminal (terbatas)',
        ],
        notIncluded: [
            'Custom Domain',
            'Database',
            'Backup & Restore',
            'Tim Access',
            'Cron Jobs',
            'DDoS Protection',
        ],
        color: 'border-slate-200 dark:border-slate-700',
        buttonColor: 'bg-slate-900 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100',
    },
    {
        id: 'starter',
        name: 'Starter',
        badge: { text: 'SAAT INI', color: 'bg-emerald-500' },
        price_normal: 25000,
        price_promo: 15000,
        features: [
            '3 Proyek',
            '2 GB Storage',
            '5 GB Bandwidth/hari',
            'Subdomain & Custom Domain',
            'SSL Otomatis',
            'Build Logs & Terminal',
            '1 Database MySQL',
            'Backup & Restore',
            'Cron Jobs (5 max)',
        ],
        notIncluded: [
            'Tim Access',
            'DDoS Protection Prioritas',
        ],
        color: 'border-emerald-300 dark:border-emerald-500/40',
        buttonColor: 'bg-emerald-600 hover:bg-emerald-700',
    },
    {
        id: 'pro',
        name: 'Pro',
        badge: { text: 'POPULER', color: 'bg-[#7c3aed]' },
        price_normal: 75000,
        price_promo: 49000,
        features: [
            '10 Proyek',
            '10 GB Storage',
            '50 GB Bandwidth/hari',
            'Subdomain & Custom Domain',
            'SSL Otomatis',
            'Build Logs & Terminal',
            '5 Database MySQL/PostgreSQL',
            'Backup & Restore',
            'Cron Jobs (20 max)',
            'Tim Access (3 anggota)',
            'DDoS Protection',
            'Prioritas Support',
        ],
        notIncluded: [],
        color: 'border-[#7c3aed] dark:border-[#7c3aed]/50',
        buttonColor: 'bg-[#7c3aed] hover:bg-[#6d28d9]',
    },
    {
        id: 'business',
        name: 'Business',
        badge: null,
        price_normal: 200000,
        price_promo: 149000,
        features: [
            'Unlimited Proyek',
            '50 GB Storage',
            'Unlimited Bandwidth',
            'Subdomain & Custom Domain',
            'SSL Otomatis',
            'Build Logs & Terminal',
            'Unlimited Database',
            'Backup & Restore Harian',
            'Cron Jobs (Unlimited)',
            'Tim Access (Unlimited)',
            'DDoS Protection Prioritas',
            'SLA 99.9% Uptime',
            'Dedicated Support',
        ],
        notIncluded: [],
        color: 'border-amber-300 dark:border-amber-500/40',
        buttonColor: 'bg-amber-600 hover:bg-amber-700',
    },
];

function formatRupiah(amount) {
    return `Rp${Number(amount || 0).toLocaleString('id-ID')}`;
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

export default function Subscription() {
    const { auth } = usePage().props;
    const user = auth?.user;
    const subscription = user?.hosting_subscription;

    return (
        <DashboardLayout title="Langganan Hosting">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-crown text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Langganan Hosting</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola paket dan langganan hosting Anda.</p>
                    </div>
                </div>
            </div>

            {/* Active Subscription Banner */}
            {subscription && (
                <div className="mt-6 bg-gradient-to-r from-[#7c3aed] to-purple-600 rounded-2xl p-6 mb-8 text-white shadow-lg border border-[#7c3aed]/50 relative overflow-hidden">
                    <div className="absolute -right-4 -top-10 opacity-20 pointer-events-none">
                        <i className="fa-solid fa-crown text-9xl"></i>
                    </div>
                    <div className="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div className="flex items-center gap-4">
                            <div className="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shrink-0">
                                <i className="fa-solid fa-crown text-2xl"></i>
                            </div>
                            <div>
                                <h3 className="font-bold text-lg">Paket {subscription.plan?.name || 'Aktif'}</h3>
                                <p className="text-indigo-100 text-sm">
                                    Aktif hingga <strong className="text-white">{formatDate(subscription.end_date)}</strong>
                                </p>
                                <p className="text-indigo-200 text-xs mt-1">
                                    {subscription.projects_count || 0} proyek aktif &middot; {subscription.storage_used || '0 MB'} terpakai
                                </p>
                            </div>
                        </div>
                        <div className="flex items-center gap-2">
                            <span className="bg-white/20 text-white text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm">
                                Status: {(subscription.status || 'active').toUpperCase()}
                            </span>
                        </div>
                    </div>
                </div>
            )}

            {/* Plan Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mt-4">
                {plans.map((plan) => (
                    <PlanCard key={plan.id} plan={plan} currentPlan={subscription?.plan?.name?.toLowerCase()} />
                ))}
            </div>
        </DashboardLayout>
    );
}

function PlanCard({ plan, currentPlan }) {
    const [voucher, setVoucher] = useState('');
    const isCurrent = currentPlan === plan.id;
    const { post, processing } = useForm({
        plan: plan.id,
        voucher_code: '',
    });

    function handleSubscribe() {
        post(route('user_hosting.billing.subscribe'), {
            plan: plan.id,
            voucher_code: voucher,
        });
    }

    return (
        <div className={`bg-white dark:bg-[#0d0d18] rounded-2xl border-2 ${plan.color} shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full relative overflow-hidden ${isCurrent ? 'ring-2 ring-[#7c3aed] dark:ring-[#7c3aed]/50' : ''}`}>
            {/* Badge */}
            {plan.badge && (
                <div className={`${plan.badge.color} text-white text-[10px] font-black uppercase tracking-widest text-center py-1.5`}>
                    {plan.badge.text}
                </div>
            )}

            <div className="p-6 flex flex-col flex-1">
                {/* Plan Name */}
                <h3 className="text-lg font-black text-[#333] dark:text-white mb-3">{plan.name}</h3>

                {/* Pricing */}
                <div className="mb-5">
                    {plan.price_normal === 0 ? (
                        <div className="text-3xl font-black text-[#333] dark:text-white">Gratis</div>
                    ) : (
                        <div className="flex items-baseline gap-2">
                            {plan.price_promo > 0 && plan.price_promo < plan.price_normal ? (
                                <>
                                    <span className="text-3xl font-black text-[#7c3aed]">{formatRupiah(plan.price_promo)}</span>
                                    <span className="text-sm text-[#999] dark:text-white/40 line-through">{formatRupiah(plan.price_normal)}</span>
                                </>
                            ) : (
                                <span className="text-3xl font-black text-[#333] dark:text-white">{formatRupiah(plan.price_normal)}</span>
                            )}
                        </div>
                    )}
                    {plan.price_normal > 0 && (
                        <p className="text-xs text-[#999] dark:text-white/40 mt-1">/bulan {plan.price_promo > 0 && plan.price_promo < plan.price_normal && '(promo)'}</p>
                    )}
                </div>

                {/* Features */}
                <ul className="space-y-2.5 mb-6 flex-1">
                    {plan.features.map((feature, i) => (
                        <li key={i} className="flex items-start gap-2.5 text-sm text-[#666] dark:text-white/60">
                            <i className="fa-solid fa-check text-emerald-500 dark:text-emerald-400 text-xs mt-1 shrink-0"></i>
                            <span>{feature}</span>
                        </li>
                    ))}
                    {plan.notIncluded.map((feature, i) => (
                        <li key={i} className="flex items-start gap-2.5 text-sm text-[#ccc] dark:text-white/20">
                            <i className="fa-solid fa-xmark text-slate-300 dark:text-white/10 text-xs mt-1 shrink-0"></i>
                            <span>{feature}</span>
                        </li>
                    ))}
                </ul>

                {/* Subscribe Form */}
                <div className="mt-auto">
                    {isCurrent ? (
                        <div className="w-full py-3 text-center bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] dark:text-[#a78bfa] font-bold text-sm rounded-xl border border-[#7c3aed]/20">
                            <i className="fa-solid fa-check-circle mr-1"></i> Paket Aktif
                        </div>
                    ) : plan.price_normal === 0 ? (
                        <div className="w-full py-3 text-center bg-slate-100 dark:bg-slate-700/50 text-[#999] dark:text-white/40 font-bold text-sm rounded-xl">
                            Paket Gratis
                        </div>
                    ) : (
                        <div className="space-y-2">
                            <input type="text" value={voucher} onChange={e => setVoucher(e.target.value)}
                                placeholder="Kode voucher (opsional)"
                                className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2 text-xs text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                            <button onClick={handleSubscribe} disabled={processing}
                                className={`w-full ${plan.buttonColor} text-white font-bold py-3 rounded-xl text-sm transition disabled:opacity-50 shadow-sm`}>
                                {processing ? 'Memproses...' : 'Langganan Sekarang'}
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
