import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Detail() {
    const { order } = usePage().props;

    const statusBadge = (status) => {
        const styles = {
            pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            in_progress: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            completed: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${styles[status] || styles.pending}`}>
                {(status || '-').replace('_', ' ')}
            </span>
        );
    };

    const paymentStatusBadge = (status) => {
        const styles = {
            paid: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            failed: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
            unpaid: 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${styles[status] || styles.unpaid}`}>
                {status || '-'}
            </span>
        );
    };

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
    };

    const InfoRow = ({ label, value }) => (
        <div className="flex items-start justify-between py-2.5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] last:border-0">
            <span className="text-[12px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">{label}</span>
            <span className="text-[13px] text-[#333] dark:text-white text-right max-w-[60%]">{value || '-'}</span>
        </div>
    );

    return (
        <DashboardLayout title="Detail Pesanan">
            <div className="max-w-2xl mx-auto space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Detail Pesanan</h2>
                        <Link href="/user/joki/progress" className="text-[12px] text-[#7c3aed] hover:text-[#6d28d9] font-medium transition-colors">
                            <i className="fa-solid fa-arrow-left mr-1"></i>Kembali
                        </Link>
                    </div>

                    <div className="p-5 space-y-1">
                        <InfoRow label="Order Number" value={order?.order_number || '-'} />
                        <InfoRow label="Layanan" value={order?.service?.name || '-'} />
                        <InfoRow label="Project Name" value={order?.project_name || '-'} />
                        <InfoRow
                            label="Deskripsi"
                            value={
                                <span className="whitespace-pre-wrap">{order?.description || '-'}</span>
                            }
                        />
                        <InfoRow
                            label="Status"
                            value={statusBadge(order?.status)}
                        />
                        <InfoRow
                            label="Deadline"
                            value={order?.deadline ? new Date(order.deadline).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'}
                        />
                        <InfoRow label="Amount" value={formatCurrency(order?.amount)} />
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Status Pembayaran</h2>
                    </div>

                    <div className="p-5 space-y-1">
                        <InfoRow label="Invoice" value={order?.payment?.invoice_number || '-'} />
                        <InfoRow
                            label="Status"
                            value={paymentStatusBadge(order?.payment?.status)}
                        />
                        <InfoRow label="Metode" value={order?.payment?.method || '-'} />
                        <InfoRow label="Jumlah" value={formatCurrency(order?.payment?.amount)} />
                        <InfoRow
                            label="Tanggal Bayar"
                            value={order?.payment?.paid_at ? new Date(order.payment.paid_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'}
                        />
                    </div>

                    {order?.payment?.status === 'pending' && (
                        <div className="px-5 py-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <Link
                                href={`/user/joki/billing/${order?.payment?.id}/pay`}
                                className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors inline-block"
                            >
                                <i className="fa-solid fa-wallet mr-1.5"></i>Bayar Sekarang
                            </Link>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
