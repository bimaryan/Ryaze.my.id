<?php

namespace App\Http\Controllers\Joki\Admin;

use App\Http\Controllers\Controller;
use App\Models\JokiOrder;
use App\Models\JokiPayment;
use App\Models\JokiRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

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

        // 3. Kirim data ke view
        return view('pages.joki.admin.index', compact(
            'pendingOrders',
            'progressOrders',
            'reviewOrders',
            'completedOrders',
            'queueOrders'
        ));
    }

    public function manageOrders()
    {
        // Menambahkan relasi payments agar admin bisa mengecek status pembayaran di tabel
        $orders = JokiOrder::with(['client', 'service', 'payments'])->latest()->get();

        return view('pages.joki.admin.orders', compact('orders'));
    }

    public function editOrder($id)
    {
        $order = JokiOrder::with(['client', 'service'])->findOrFail($id);

        return view('pages.joki.admin.edit_order', compact('order'));
    }

    public function updateOrder(Request $request, $id)
    {
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

        $order->update($validated);

        return redirect()->route('admin_joki.orders')->with('success', 'Data pesanan berhasil diperbarui!');
    }

    public function storeMilestone(Request $request, $id)
    {
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
    public function storePayment(Request $request, $id)
    {
        $order = JokiOrder::with('client')->findOrFail($id);

        $validated = $request->validate([
            'payment_name' => 'required|string|max:255',
            'amount' => 'required|integer|min:1000',
        ]);

        $invoiceNumber = 'INV-'.strtoupper(uniqid());

        // Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Parameter Kiriman ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $invoiceNumber,
                'gross_amount' => $validated['amount'],
            ],
            'customer_details' => [
                'first_name' => $order->client->name,
                'email' => $order->client->email,
            ],
        ];

        // Dapatkan Snap Token dari Midtrans
        $snapToken = Snap::getSnapToken($params);

        // Simpan ke Database
        $order->payments()->create([
            'invoice_number' => $invoiceNumber,
            'payment_name' => $validated['payment_name'],
            'amount' => $validated['amount'],
            'status' => 'unpaid',
            'snap_token' => $snapToken, // Simpan tokennya
        ]);

        return back()->with('success', 'Tagihan Midtrans berhasil dibuat!');
    }

    /**
     * FUNGSI ADMIN: Verifikasi Bukti Pembayaran
     */
    public function verifyPayment(Request $request, $payment_id)
    {
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
    public function replyRevision(Request $request, $revision_id)
    {
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
}
