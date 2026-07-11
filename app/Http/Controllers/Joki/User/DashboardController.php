<?php

namespace App\Http\Controllers\Joki\User;

use App\Http\Controllers\Controller;
use App\Models\JokiOrder;
use App\Models\JokiPayment;
use App\Models\JokiService;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.joki.user.index');
    }

    public function detail($hashed_id)
    {
        $decoded = Hashids::decode($hashed_id);
        if (count($decoded) === 0) {
            abort(404);
        }
        $id = $decoded[0];
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

        $order = JokiOrder::create($validated);

        // Notifikasi ke User
        $user = \Illuminate\Support\Facades\Auth::user();
        $msgUser = 'Pesanan Joki ' . $order->order_number . ' berhasil dibuat. Kami akan segera menghubungi Anda.';
        $user->notify(new \App\Notifications\SystemNotification($msgUser, 'success'));
        if ($user->phone) {
            \App\Services\WhatsAppService::send($user->phone, "*RYAZE ORDER BERHASIL*\n\n" . $msgUser);
        }

        // Notifikasi ke Admin Joki (Jika ada user dengan role admin_joki)
        $adminJoki = \App\Models\User::where('role', 'admin_joki')->first();
        if ($adminJoki) {
            $msgAdmin = 'Pesanan Joki Baru: ' . $order->order_number . ' dari ' . $user->name;
            $adminJoki->notify(new \App\Notifications\SystemNotification($msgAdmin, 'info'));
            if ($adminJoki->phone) {
                \App\Services\WhatsAppService::send($adminJoki->phone, "*RYAZE ORDER MASUK*\n\n" . $msgAdmin);
            }
        }

        return redirect()->route('user_joki.dashboard')->with('success', 'Pesanan berhasil dibuat!');
    }

    public function uploadPaymentProof(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        $payment_id = $decoded[0];

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
    public function requestRevision(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        $order_id = $decoded[0];

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

    public function billingHistory()
    {
        $payments = JokiPayment::whereHas('order', function ($query) {
            $query->where('client_id', Auth::id());
        })->with('order')->orderBy('created_at', 'desc')->get();

        return view('pages.joki.user.billing', compact('payments'));
    }

    public function submitReview(Request $request, $hashid)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000'
        ]);

        $orderId = \Vinkla\Hashids\Facades\Hashids::decode($hashid)[0];
        $order = JokiOrder::where('client_id', Auth::id())
            ->where('status', 'completed')
            ->findOrFail($orderId);

        if ($order->rating || $order->review) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk pesanan ini.');
        }

        $order->update([
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
    }

    public function deployToHosting(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        $order_id = $decoded[0];

        $order = JokiOrder::where('client_id', Auth::id())
            ->where('status', 'completed')
            ->findOrFail($order_id);

        if ($order->is_deployed_to_hosting) {
            return back()->with('error', 'Pesanan ini sudah pernah di-deploy ke Hosting.');
        }

        // Create a new Hosting Project from this Joki Order
        $project = \App\Models\HostingProject::create([
            'user_id' => Auth::id(),
            'name' => 'joki-' . strtolower(str_replace(' ', '-', $order->project_name)) . '-' . rand(100,999),
            'description' => 'Deployed from Joki Order: ' . $order->order_number,
            'status' => 'active',
            'type' => 'php', 
            'directory' => 'joki-' . $order->order_number . '-' . time(),
            'port' => rand(8000, 9000), 
            'storage_limit_mb' => Auth::user()->hosting_storage_limit_mb ?? 512,
        ]);
        
        // Buat folder project
        $clientDir = env('HOSTING_CLIENTS_DIR', storage_path('app/hosting_clients'));
        $projectPath = $clientDir . DIRECTORY_SEPARATOR . $project->directory;
        if (!file_exists($projectPath)) {
            mkdir($projectPath, 0777, true);
        }

        $order->update(['is_deployed_to_hosting' => true]);

        return redirect()->route('user_hosting.projects')->with('success', 'Project Joki berhasil di-deploy ke Hosting Anda!');
    }
}
