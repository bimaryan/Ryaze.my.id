<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JokiPayment;

class PaymentCallbackController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $notification = $request->all();
        $order_id = $notification['order_id'] ?? null;
        $status = $notification['status'] ?? null;

        if (!$order_id) {
            return response()->json(['success' => false, 'message' => 'Missing order_id'], 400);
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
                            $user->increment('hosting_storage_limit_mb', 2048); // Add 2GB globally

                            $user->notify(new \App\Notifications\SystemNotification('Pembayaran upgrade storage ('.$payment->invoice_number.') berhasil. Kapasitas storage akun Anda bertambah 2GB.', 'success'));
                        }
                    }
                } elseif (in_array($statusLower, ['failed', 'cancelled', 'expired'])) {
                    $payment->update(['status' => 'failed']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Upgrade Invoice not found'], 404);
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

