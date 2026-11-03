<?php

namespace App\Http\Controllers;

use App\Models\PaymentConfirmation;
use App\Services\PaymentConfirmationService;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class PaymentConfirmationController extends Controller
{
    public function __construct(protected PaymentConfirmationService $service) {}

    public function all(Request $request)
    {
        return $this->index(
            $request,
            array_merge(PaymentConfirmation::HOSTING_TYPES, PaymentConfirmation::JOKI_TYPES),
            'superadmin.confirmations',
            PaymentConfirmation::GROUP_ALL
        );
    }

    public function hosting(Request $request)
    {
        return $this->index($request, PaymentConfirmation::HOSTING_TYPES, 'admin_hosting.confirmations', PaymentConfirmation::GROUP_HOSTING);
    }

    public function joki(Request $request)
    {
        return $this->index($request, PaymentConfirmation::JOKI_TYPES, 'admin_joki.confirmations', PaymentConfirmation::GROUP_JOKI);
    }

    private function index(Request $request, array $types, string $routeName, string $group)
    {
        $allowed = ['pending', 'verified', 'rejected'];
        $status = in_array($request->query('status'), $allowed, true) ? $request->query('status') : 'pending';

        $confirmations = PaymentConfirmation::with(['user', 'verifier'])
            ->whereIn('order_type', $types)
            ->where('status', $status)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = PaymentConfirmation::whereIn('order_type', $types)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $label = match ($group) {
            PaymentConfirmation::GROUP_JOKI => 'Joki',
            PaymentConfirmation::GROUP_HOSTING => 'Hosting',
            default => 'Semua Area',
        };

        return view('pages.shared.payment-confirmations', compact(
            'confirmations', 'status', 'counts', 'routeName', 'types', 'group', 'label'
        ));
    }

    public function verify(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $confirmation = PaymentConfirmation::findOrFail($decoded[0]);

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $user = $request->user();
        if ($user->role !== 'superadmin' && $user->role !== $confirmation->adminRole()) {
            abort(403, 'Konfirmasi ini bukan wewenang area Anda.');
        }

        if ($confirmation->status !== PaymentConfirmation::STATUS_PENDING) {
            return back()->with('info', 'Konfirmasi ini sudah diproses sebelumnya.');
        }

        if ($validated['action'] === 'approve') {
            $ok = $this->service->approve($confirmation, $user);

            return $ok
                ? back()->with('success', 'Pembayaran ' . $confirmation->order_number . ' diverifikasi dan tagihan dilunasi.')
                : back()->with('error', 'Tagihan tidak ditemukan untuk invoice ' . $confirmation->order_number . '.');
        }

        $this->service->reject($confirmation, $user);

        return back()->with('success', 'Konfirmasi ' . $confirmation->order_number . ' ditolak.');
    }
}
