<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JokiPayment;

class PaymentCallbackController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $payment = JokiPayment::where('invoice_number', $request->order_id)->first();

            if ($payment) {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $payment->update([
                        'status' => 'paid',
                        'payment_method' => $request->payment_type,
                        'paid_at' => now(),
                    ]);
                } elseif ($request->transaction_status == 'expire' || $request->transaction_status == 'cancel') {
                    $payment->update(['status' => 'failed']);
                }
            }
        }
        return response()->json(['status' => 'success']);
    }
}
