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
}
