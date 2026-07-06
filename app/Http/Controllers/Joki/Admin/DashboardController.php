<?php

namespace App\Http\Controllers\Joki\Admin;

use App\Http\Controllers\Controller;
use App\Models\JokiOrder;
use App\Models\JokiPayment;
use App\Models\JokiRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Menghitung Statistik untuk Card
        $pendingOrders = JokiOrder::where('status', 'pending')->count();
        $progressOrders = JokiOrder::where('status', 'progress')->count();
        $reviewOrders = JokiOrder::where('status', 'review')->count();
        $completedOrders = JokiOrder::where('status', 'completed')
            ->whereMonth('updated_at', date('m')) // Selesai bulan ini
            ->count();

        // 2. Mengambil data antrean (Tabel)
        $queueOrders = JokiOrder::with('client')
            ->whereIn('status', ['pending', 'progress', 'review'])
            ->orderBy('deadline', 'asc') // Urutkan dari deadline terdekat
            ->get();

        // 3. Chart Data
        // 3a. Pie Chart: Order Status
        $statusCount = JokiOrder::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status')->toArray();
        $chartOrderStatus = [
            'labels' => array_keys($statusCount),
            'series' => array_values($statusCount)
        ];

        // 3b. Bar Chart: New Orders (Last 6 Months)
        $months = [];
        $newOrders = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->startOfMonth()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');
            $newOrders[] = JokiOrder::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count();
        }
        $chartNewOrders = [
            'labels' => $months,
            'series' => $newOrders
        ];

        // 3c. Line Chart: Completed Orders Trend
        $completedOrdersTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->startOfMonth()->subMonths($i);
            $completedOrdersTrend[] = JokiOrder::where('status', 'completed')
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->count();
        }
        $chartCompletedOrders = [
            'labels' => $months,
            'series' => $completedOrdersTrend
        ];

        // 4. Kirim data ke view
        return view('pages.joki.admin.index', compact(
            'pendingOrders',
            'progressOrders',
            'reviewOrders',
            'completedOrders',
            'queueOrders',
            'chartOrderStatus',
            'chartNewOrders',
            'chartCompletedOrders'
        ));
    }

    public function manageOrders()
    {
        // Menambahkan relasi payments agar admin bisa mengecek status pembayaran di tabel
        $orders = JokiOrder::with(['client', 'service', 'payments'])->latest()->paginate(15);

        return view('pages.joki.admin.orders', compact('orders'));
    }

    public function editOrder($hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        $id = $decoded[0];

        $order = JokiOrder::with(['client', 'service'])->findOrFail($id);

        return view('pages.joki.admin.edit_order', compact('order'));
    }

    public function updateOrder(Request $request, $hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        $id = $decoded[0];

        $order = JokiOrder::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,progress,review,completed,canceled',
            'progress' => 'required|integer|min:0|max:100',
            'price' => 'nullable|integer',
            'repo_link' => 'nullable|url',
            'demo_link' => 'nullable|url',
        ]);

        // Jika admin yang sedang login baru pertama kali mengupdate ini, catat dia sebagai workernya
        if (! $order->worker_id && $validated['status'] != 'pending') {
            $order->worker_id = Auth::id();
        }

        $oldStatus = $order->status;
        $order->update($validated);

        if ($oldStatus != $order->status) {
            $order->client->notify(new \App\Notifications\SystemNotification('Status pesanan Joki Anda (' . $order->order_number . ') telah diubah menjadi: ' . strtoupper($order->status), 'info'));
        }

        return redirect()->route('admin_joki.orders')->with('success', 'Data pesanan berhasil diperbarui!');
    }

    public function storeMilestone(Request $request, $hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        $id = $decoded[0];

        $order = JokiOrder::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,working,done',
        ]);

        $order->milestones()->create($validated);

        return back()->with('success', 'Milestone berhasil ditambahkan!');
    }

    /**
     * FUNGSI ADMIN: Membuat Tagihan (Invoice/DP/Pelunasan)
     */
    public function storePayment(Request $request, $hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        $id = $decoded[0];

        $order = JokiOrder::with('client')->findOrFail($id);

        $validated = $request->validate([
            'payment_name' => 'required|string|max:255',
            'amount' => 'required|integer|min:1000',
        ]);

        $invoiceNumber = 'INV-'.strtoupper(uniqid());

        // Simpan Tagihan ke Database
        $order->payments()->create([
            'invoice_number' => $invoiceNumber,
            'payment_name' => $validated['payment_name'],
            'amount' => $validated['amount'],
            'status' => 'unpaid',
            'snap_token' => null, // Tidak dibutuhkan untuk Pakasir
        ]);

        return back()->with('success', 'Tagihan berhasil dibuat!');
    }

    /**
     * FUNGSI ADMIN: Verifikasi Bukti Pembayaran
     */
    public function verifyPayment(Request $request, $hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        $payment_id = $decoded[0];

        $payment = JokiPayment::findOrFail($payment_id);

        $request->validate([
            'status' => 'required|in:paid,failed,unpaid',
        ]);

        $payment->update([
            'status' => $request->status,
            'paid_at' => $request->status == 'paid' ? now() : null,
        ]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui!');
    }

    /**
     * FUNGSI ADMIN: Merespon Request Revisi dari Klien
     */
    public function replyRevision(Request $request, $hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        $revision_id = $decoded[0];

        $revision = JokiRevision::findOrFail($revision_id);

        $request->validate([
            'admin_reply' => 'required|string',
            'status' => 'required|in:fixing,resolved,rejected',
        ]);

        $revision->update([
            'admin_reply' => $request->admin_reply,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Tanggapan revisi berhasil dikirim!');
    }

    public function financeReport()
    {
        // Ambil semua payment yang berstatus paid / verified
        $payments = \App\Models\JokiPayment::with(['order.client', 'order.service'])
            ->whereIn('status', ['paid', 'verified'])
            ->orderBy('paid_at', 'desc')
            ->get();
            
        $totalRevenue = $payments->sum('amount');
        
        return view('pages.joki.admin.finance', compact('payments', 'totalRevenue'));
    }
}
