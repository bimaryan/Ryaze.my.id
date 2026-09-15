<?php

namespace App\Http\Controllers\Hosting\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostingPayment;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $payments = HostingPayment::with(['user', 'project.client'])
            ->latest()
            ->get();
            
        return view('pages.hosting.admin.billing.index', compact('payments'));
    }

    public function verifyPayment(Request $request, $hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        $id = $decoded[0];

        $payment = HostingPayment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:unpaid,paid,failed',
        ]);

        $payment->update([
            'status' => $validated['status'],
            'paid_at' => $validated['status'] === 'paid' ? now() : null,
        ]);

        return back()->with('success', 'Status tagihan hosting berhasil diperbarui!');
    }
}
