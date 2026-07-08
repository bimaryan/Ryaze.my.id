<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JokiPayment;

class PaymentCallbackController extends Controller
{
    private function distributeAffiliateCommission($user, $amount, $description)
    {
        if ($user && $user->referred_by) {
            $referrer = $user->referrer;
            if ($referrer) {
                $commissionAmount = $amount * 0.10; // 10%
                
                \App\Models\AffiliateCommission::create([
                    'user_id' => $referrer->id,
                    'referred_user_id' => $user->id,
                    'amount' => $commissionAmount,
                    'description' => $description,
                    'status' => 'paid',
                ]);

                $referrerWallet = $referrer->wallet()->firstOrCreate(['user_id' => $referrer->id], ['balance' => 0]);
                $referrerWallet->increment('balance', $commissionAmount);

                \App\Models\WalletTransaction::create([
                    'wallet_id' => $referrerWallet->id,
                    'amount' => $commissionAmount,
                    'type' => 'credit',
                    'description' => 'Komisi Affiliate: ' . $description,
                    'status' => 'completed',
                ]);
                
                $referrer->notify(new \App\Notifications\SystemNotification('Anda mendapatkan komisi afiliasi sebesar Rp ' . number_format($commissionAmount, 0, ',', '.') . ' dari referal Anda.', 'success'));
            }
        }
    }

    public function handleWebhook(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Pakasir Webhook Hit:', $request->all());
        
        $notification = $request->all();
        $order_id = $notification['order_id'] ?? null;
        $amount = $notification['amount'] ?? null;

        if (!$order_id || !$amount) {
            return response()->json(['success' => false, 'message' => 'Missing order_id or amount'], 400);
        }

        // 1. Verifikasi Resmi Pakasir (Cross-Check API)
        $apiKey = env('PAKASIR_API_KEY');
        $projectSlug = env('PAKASIR_SLUG');
        
        if (!$apiKey || !$projectSlug) {
            \Illuminate\Support\Facades\Log::error('Pakasir API Key or Slug is missing in .env');
            return response()->json(['success' => false, 'message' => 'Server Configuration Error'], 500);
        }

        try {
            $verifyResponse = \Illuminate\Support\Facades\Http::get('https://app.pakasir.com/api/transactiondetail', [
                'project' => $projectSlug,
                'amount' => $amount,
                'order_id' => $order_id,
                'api_key' => $apiKey
            ]);

            if (!$verifyResponse->successful()) {
                \Illuminate\Support\Facades\Log::warning("Fake Webhook attempt detected for Order ID: {$order_id} from IP: " . $request->ip());
                return response()->json(['success' => false, 'message' => 'Transaction verification failed'], 401);
            }

            $transactionData = $verifyResponse->json('transaction');
            if (!$transactionData) {
                return response()->json(['success' => false, 'message' => 'Invalid transaction data from Pakasir'], 401);
            }

            $status = $transactionData['status'] ?? null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Pakasir Webhook Verification Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Internal server error during verification'], 500);
        }

        if (str_starts_with($order_id, 'HST-INV-')) {
            $payment = \App\Models\HostingPayment::where('invoice_number', $order_id)->first();
            if ($payment) {
                $statusLower = strtolower($status);
                if (in_array($statusLower, ['completed', 'paid', 'success'])) {
                    if ($payment->status != 'paid') {
                        $payment->update([
                            'status' => 'paid',
                            'payment_method' => 'Pakasir',
                            'paid_at' => now(),
                        ]);

                        $user = $payment->user;
                        if ($user) {
                            // Update atau buat langganan
                            $billing = \App\Models\HostingBilling::where('user_id', $user->id)
                                ->where('status', 'active')
                                ->first();

                            if ($billing) {
                                $billing->update([
                                    'next_due_date' => \Carbon\Carbon::parse($billing->next_due_date)->addMonth()
                                ]);
                            } else {
                                \App\Models\HostingBilling::create([
                                    'user_id' => $user->id,
                                    'hosting_project_id' => null,
                                    'plan_name' => 'Bulanan Rp 10.000',
                                    'amount' => 10000,
                                    'billing_cycle' => 'monthly',
                                    'next_due_date' => now()->addMonth(),
                                    'status' => 'active'
                                ]);
                            }

                            // Cari semua project unpaid dan deploy
                            $unpaidProjects = \App\Models\HostingProject::where('user_id', $user->id)
                                ->where('status', 'unpaid')
                                ->get();

                            foreach ($unpaidProjects as $proj) {
                                $proj->update(['status' => 'building']);
                                $isTemplate = $proj->source_type === 'template';
                                $proj->deployments()->create([
                                    'status' => 'queued',
                                    'build_logs' => $isTemplate
                                        ? "> Payment received!\n> Initialize build pipeline...\n> Menunggu worker tersedia...\n> Mengambil template starter code...\n> Menyiapkan environment ".strtoupper($proj->framework).'...'
                                        : "> Payment received!\n> Initialize build pipeline...\n> Menunggu worker tersedia...\n> Mengambil repository dari ".$proj->repo_source."\n> Branch: ".$proj->branch."\n> Menyiapkan environment ".strtoupper($proj->framework).'...',
                                ]);
                                \App\Jobs\AutoDeployProject::dispatch($proj);
                            }

                            $user->notify(new \App\Notifications\SystemNotification('Pembayaran langganan hosting ('.$payment->invoice_number.') berhasil. ' . $unpaidProjects->count() . ' project sedang disiapkan.', 'success'));
                            
                            $this->distributeAffiliateCommission($user, $payment->amount, 'Pembayaran Langganan Hosting');
                        }
                    }
                } elseif (in_array($statusLower, ['failed', 'cancelled', 'expired'])) {
                    $payment->update(['status' => 'failed']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Hosting Invoice not found'], 404);
            }
        } elseif (str_starts_with($order_id, 'HST-UPG-')) {
            $payment = \App\Models\HostingPayment::where('invoice_number', $order_id)->first();
            if ($payment) {
                $statusLower = strtolower($status);
                if (in_array($statusLower, ['completed', 'paid', 'success'])) {
                    if ($payment->status != 'paid') {
                        $payment->update([
                            'status' => 'paid',
                            'payment_method' => 'Pakasir',
                            'paid_at' => now(),
                        ]);

                        $user = $payment->user;
                        if ($user) {
                            $user->increment('hosting_storage_limit_mb', 1024); // Add 1GB globally

                            $user->notify(new \App\Notifications\SystemNotification('Pembayaran upgrade storage ('.$payment->invoice_number.') berhasil. Kapasitas storage akun Anda bertambah 1GB.', 'success'));
                            
                            $this->distributeAffiliateCommission($user, $payment->amount, 'Pembayaran Upgrade Storage Hosting');
                        }
                    }
                } elseif (in_array($statusLower, ['failed', 'cancelled', 'expired'])) {
                    $payment->update(['status' => 'failed']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Upgrade Invoice not found'], 404);
            }
        } elseif (str_starts_with($order_id, 'WLT-TOPUP-')) {
            $transaction = \App\Models\WalletTransaction::where('reference_id', $order_id)->first();
            if ($transaction) {
                $statusLower = strtolower($status);
                if (in_array($statusLower, ['completed', 'paid', 'success'])) {
                    if ($transaction->status != 'completed') {
                        $transaction->update(['status' => 'completed']);
                        $wallet = $transaction->wallet;
                        if ($wallet) {
                            $wallet->increment('balance', $transaction->amount);
                            $wallet->user->notify(new \App\Notifications\SystemNotification('Top Up Wallet sebesar Rp ' . number_format($transaction->amount, 0, ',', '.') . ' berhasil ditambahkan.', 'success'));
                        }
                    }
                } elseif (in_array($statusLower, ['failed', 'cancelled', 'expired'])) {
                    $transaction->update(['status' => 'failed']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Wallet Transaction not found'], 404);
            }
        } else {
            // Default ke Joki Payment
            $payment = JokiPayment::where('invoice_number', $order_id)->first();

            if ($payment) {
                $statusLower = strtolower($status);
                if (in_array($statusLower, ['completed', 'paid', 'success'])) {
                    $payment->update([
                        'status' => 'paid',
                        'payment_method' => 'Pakasir',
                        'paid_at' => now(),
                    ]);

                    if ($payment->order && $payment->order->client) {
                        $payment->order->client->notify(new \App\Notifications\SystemNotification('Pembayaran joki Anda ('.$payment->invoice_number.') telah kami terima. Admin segera memprosesnya.', 'success'));
                        $this->distributeAffiliateCommission($payment->order->client, $payment->amount, 'Pembayaran Layanan Joki');
                    }
                } elseif (in_array($statusLower, ['failed', 'cancelled', 'expired'])) {
                    $payment->update(['status' => 'failed']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Invoice not found'], 404);
            }
        }

        return response()->json(['success' => true, 'message' => 'Webhook received']);
    }
}

