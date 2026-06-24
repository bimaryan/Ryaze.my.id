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

                        $project = $payment->project;
                        if ($project && $project->status == 'unpaid') {
                            $project->update(['status' => 'building']);

                            $project->deployments()->create([
                                'status' => 'queued',
                                'build_logs' => "> Payment received!\n> Initialize build pipeline...\n> Menunggu worker tersedia...\n> Mengambil repository dari ".$project->repo_source."\n> Branch: ".$project->branch."\n> Menyiapkan environment ".strtoupper($project->framework).'...',
                            ]);

                            \App\Jobs\AutoDeployProject::dispatch($project);

                            // Tambahkan masa aktif 1 bulan
                            \App\Models\HostingBilling::create([
                                'hosting_project_id' => $project->id,
                                'plan_name' => 'Bulanan Rp 15.000',
                                'amount' => 15000,
                                'billing_cycle' => 'monthly',
                                'next_due_date' => now()->addMonth(),
                                'status' => 'active'
                            ]);
                        }
                    }
                } elseif (in_array($statusLower, ['failed', 'cancelled', 'expired'])) {
                    $payment->update(['status' => 'failed']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Hosting Invoice not found'], 404);
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

