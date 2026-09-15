import { usePage, useForm, router } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function EditOrder() {
    const { order, consultation } = usePage().props;

    const { data, setData, put, processing, errors } = useForm({
        status: order?.status || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(`/admin/joki/orders/${order.id}`, { preserveState: true });
    };

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
    };

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

    const detailRow = (label, value) => (
        <div className="flex items-start gap-4 py-2.5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] last:border-0">
            <span className="text-[12px] font-semibold text-[#999] dark:text-white/40 uppercase tracking-wider w-40 shrink-0">{label}</span>
            <span className="text-[13px] text-[#333] dark:text-white">{value || '-'}</span>
        </div>
    );

    return (
        <DashboardLayout title="Edit Pesanan">
            <div className="space-y-6 max-w-3xl">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Detail Pesanan</h2>
                        {statusBadge(order?.status)}
                    </div>
                    <div className="p-5">
                        {detailRow('Order Number', order?.order_number)}
                        {detailRow('Client', order?.user?.name)}
                        {detailRow('Service', order?.service?.name)}
                        {detailRow('Project Name', order?.project_name)}
                        {detailRow('Description', order?.description)}
                        {detailRow('Deadline', order?.deadline ? new Date(order.deadline).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-')}
                        {detailRow('Amount', formatCurrency(order?.amount))}
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Update Status</h2>
                    </div>
                    <div className="p-5">
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div>
                                <label className="block text-[12px] font-semibold text-[#999] dark:text-white/40 mb-1.5 uppercase tracking-wider">Status</label>
                                <select
                                    value={data.status}
                                    onChange={(e) => setData('status', e.target.value)}
                                    className="w-full px-3 py-2 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                {errors.status && <p className="text-[12px] text-red-500 mt-1">{errors.status}</p>}
                            </div>
                            <div className="flex items-center gap-2">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] disabled:opacity-50 transition-colors"
                                >
                                    {processing ? 'Menyimpan...' : 'Simpan'}
                                </button>
                                <button
                                    type="button"
                                    onClick={() => router.get('/admin/joki/orders')}
                                    className="px-4 py-2 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[13px] font-medium text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors"
                                >
                                    Kembali
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {consultation && (
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h2 className="text-sm font-bold text-[#333] dark:text-white">Sesi Konsultasi</h2>
                        </div>
                        <div className="p-5">
                            {detailRow('Status', consultation?.status)}
                            {detailRow('Catatan', consultation?.notes)}
                            {detailRow('Tanggal', consultation?.created_at ? new Date(consultation.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-')}
                        </div>
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
