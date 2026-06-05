<?php

namespace App\Http\Controllers\Joki\User;

use App\Http\Controllers\Controller;
use App\Models\JokiOrder;
use App\Models\JokiService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Hanya ambil orderan milik klien ini saja
        $activeOrders = JokiOrder::where('client_id', $user->id)
            ->whereIn('status', ['pending', 'progress', 'review'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.joki.user.index', compact('activeOrders'));
    }

    public function detail($id)
    {
        $order = JokiOrder::with(['worker', 'service'])
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
}
