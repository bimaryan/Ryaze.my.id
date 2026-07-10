<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletWithdrawal;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = WalletWithdrawal::with('user')->latest()->paginate(20);
        return view('pages.admin.withdrawals.index', compact('withdrawals'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,completed',
            'admin_note' => 'nullable|string',
        ]);

        $withdrawal = WalletWithdrawal::findOrFail($id);
        
        // Prevent updating already completed or rejected withdrawals
        if (in_array($withdrawal->status, ['completed', 'rejected'])) {
            return back()->with('error', 'Status penarikan ini sudah tidak bisa diubah.');
        }

        $withdrawal->status = $request->status;
        if ($request->filled('admin_note')) {
            $withdrawal->admin_note = $request->admin_note;
        }
        $withdrawal->save();

        if ($request->status === 'rejected') {
            // Refund the user's wallet
            $wallet = $withdrawal->user->wallet;
            $wallet->increment('balance', $withdrawal->amount);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $withdrawal->amount,
                'type' => 'credit',
                'description' => 'Pengembalian dana penarikan ditolak',
                'status' => 'completed',
            ]);

            $withdrawal->user->notify(new \App\Notifications\SystemNotification('Permintaan penarikan dana sebesar Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' ditolak. Saldo telah dikembalikan ke wallet Anda.', 'error'));
        } elseif ($request->status === 'approved' || $request->status === 'completed') {
            $withdrawal->user->notify(new \App\Notifications\SystemNotification('Permintaan penarikan dana sebesar Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' telah diproses.', 'success'));
        }

        return back()->with('success', 'Status penarikan berhasil diperbarui.');
    }
}
