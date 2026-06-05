<?php

namespace App\Http\Controllers\Joki\User;

use App\Http\Controllers\Controller;
use App\Models\JokiOrder;
use App\Models\JokiPayment;
use App\Models\JokiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.joki.user.index');
    }

    public function detail($id)
    {
        // Eager load semua relasi baru agar datanya bisa diakses di blade
        $order = JokiOrder::with(['worker', 'service', 'milestones', 'payments', 'revisions'])
            ->where('client_id', Auth::id())
            ->findOrFail($id);

        return view('pages.joki.user.detail', compact('order'));
    }

    public function create()
    {
        $services = JokiService::where('is_active', true)->get();

        return view('pages.joki.user.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required',
            'project_name' => 'required',
            'description' => 'required',
            'tech_stack' => 'required',
            'deadline' => 'required|date',
        ]);

        $validated['client_id'] = Auth::id();
        $validated['order_number'] = 'JOKI-'.time();
        $validated['status'] = 'pending';

        JokiOrder::create($validated);

        return redirect()->route('user_joki.dashboard')->with('success', 'Pesanan berhasil dibuat!');
    }

    public function uploadPaymentProof(Request $request, $payment_id)
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Pastikan payment ini milik orderan si user yang login
        $payment = JokiPayment::whereHas('order', function ($query) {
            $query->where('client_id', Auth::id());
        })->findOrFail($payment_id);

        // Upload file ke storage/app/public/payments
        $path = $request->file('proof_image')->store('payments', 'public');

        $payment->update([
            'proof_image' => $path,
            'status' => 'pending_verification', // Ubah status agar admin mengecek
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi admin.');
    }

    /**
     * FUNGSI USER: Mengajukan Revisi
     */
    public function requestRevision(Request $request, $order_id)
    {
        $request->validate([
            'revision_note' => 'required|string|min:10',
        ]);

        $order = JokiOrder::where('client_id', Auth::id())->findOrFail($order_id);

        // Buat data revisi baru
        $order->revisions()->create([
            'revision_note' => $request->revision_note,
            'status' => 'pending',
        ]);

        // Ubah status order menjadi review
        $order->update(['status' => 'review']);

        return back()->with('success', 'Permintaan revisi berhasil dikirim ke developer!');
    }
}
