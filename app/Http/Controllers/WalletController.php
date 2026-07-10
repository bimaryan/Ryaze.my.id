<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WalletTransaction;

class WalletController extends Controller
{
    public function history()
    {
        $user = Auth::user();
        $wallet = $user->wallet()->firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        
        $transactions = $wallet->transactions()->latest()->paginate(15);
        
        return view('pages.hosting.user.wallet_history', compact('wallet', 'transactions'));
    }

    public function topUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        $user = Auth::user();
        $wallet = $user->wallet()->firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        
        $amount = $request->amount;
        $referenceId = 'WLT-TOPUP-' . strtoupper(uniqid());

        // Create pending transaction
        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'reference_id' => $referenceId,
            'amount' => $amount,
            'type' => 'credit',
            'description' => 'Top Up Saldo via Pakasir',
            'status' => 'pending',
        ]);

        return back()->with('success', 'Silakan selesaikan pembayaran Top Up Anda.');
    }

    public function withdrawForm()
    {
        $user = Auth::user();
        $wallet = $user->wallet()->firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        
        return view('pages.hosting.user.wallet_withdraw', compact('wallet'));
    }

    public function withdrawProcess(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50000',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:100',
            'account_name' => 'required|string|max:255',
        ], [
            'amount.min' => 'Minimal penarikan adalah Rp 50.000',
        ]);

        $user = Auth::user();
        $wallet = $user->wallet()->firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        if ($wallet->balance < $request->amount) {
            return back()->with('error', 'Saldo Anda tidak mencukupi untuk melakukan penarikan ini.');
        }

        // Deduct balance
        $wallet->decrement('balance', $request->amount);

        // Create withdrawal request
        \App\Models\WalletWithdrawal::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'status' => 'pending',
        ]);

        // Create transaction log
        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $request->amount,
            'type' => 'debit',
            'description' => 'Penarikan Dana ke ' . $request->bank_name . ' (' . $request->account_number . ')',
            'status' => 'pending', // Menunggu persetujuan admin
        ]);

        return redirect()->route('user.wallet.history')->with('success', 'Permintaan penarikan dana berhasil dikirim dan sedang menunggu persetujuan.');
    }
}
