<?php

namespace App\Services;

use App\Models\AffiliateCommission;
use App\Models\DigitalPurchase;
use App\Models\HostingBilling;
use App\Models\HostingPayment;
use App\Models\HostingProject;
use App\Models\JokiPayment;
use App\Models\Tip;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\SystemNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentFulfillmentService
{
    /**
     * Selesaikan dan aktifkan transaksi pembayaran (QRIS, Pakasir, atau Admin Manual).
     *
     * @param string $orderId Invoice number atau Reference ID
     * @param string $method Metode pembayaran (e.g. 'QRIS', 'Pakasir', 'Manual')
     * @param string|null $reference Referensi eksternal / catatan
     */
    public function fulfill(string $orderId, string $method = 'QRIS', ?string $reference = null): bool
    {
        return DB::transaction(function () use ($orderId, $method, $reference) {
            if (str_starts_with($orderId, 'HST-INV-')) {
                return $this->fulfillHostingSubscription($orderId, $method);
            }

            if (str_starts_with($orderId, 'HST-UPG-')) {
                return $this->fulfillHostingStorageUpgrade($orderId, $method);
            }

            if (str_starts_with($orderId, 'WLT-TOPUP-')) {
                return $this->fulfillWalletTopUp($orderId, $method);
            }

            if (str_starts_with($orderId, 'PRD-')) {
                return $this->fulfillDigitalProduct($orderId, $method);
            }

            if (str_starts_with($orderId, 'TIP-')) {
                return $this->fulfillTip($orderId, $method);
            }

            return $this->fulfillJokiPayment($orderId, $method);
        });
    }

    /**
     * Cek status pembayaran apakah sudah lunas.
     */
    public function isPaid(string $orderId): bool
    {
        if (str_starts_with($orderId, 'HST-INV-') || str_starts_with($orderId, 'HST-UPG-')) {
            $payment = HostingPayment::where('invoice_number', $orderId)->first();
            return $payment && $payment->status === 'paid';
        }

        if (str_starts_with($orderId, 'WLT-TOPUP-')) {
            $tx = WalletTransaction::where('reference_id', $orderId)->first();
            return $tx && $tx->status === 'completed';
        }

        if (str_starts_with($orderId, 'PRD-')) {
            $purchase = DigitalPurchase::where('order_id', $orderId)->first();
            return $purchase && $purchase->status === 'paid';
        }

        if (str_starts_with($orderId, 'TIP-')) {
            $tip = Tip::where('order_id', $orderId)->first();
            return $tip && $tip->status === 'completed';
        }

        $joki = JokiPayment::where('invoice_number', $orderId)->first();
        return $joki && in_array($joki->status, ['paid', 'verified']);
    }

    /**
     * Dapatkan detail order untuk ditampilkan di halaman pembayaran.
     *
     * @return array{order_id: string, type: string, title: string, amount: int, status: string, user_id: int|null, user_name: string|null, user_email: string|null, redirect_url: string}|null
     */
    public function getOrderDetails(string $orderId): ?array
    {
        if (str_starts_with($orderId, 'HST-INV-')) {
            $payment = HostingPayment::where('invoice_number', $orderId)->first();
            if (!$payment) return null;

            $notes = json_decode($payment->notes, true);
            $plan = $notes['plan'] ?? 'starter';

            return [
                'order_id'     => $orderId,
                'type'         => 'hosting_subscription',
                'title'        => 'Langganan Hosting Paket ' . ucfirst($plan),
                'amount'       => (int) $payment->amount,
                'status'       => $payment->status,
                'user_id'      => $payment->user_id,
                'user_name'    => $payment->user->name ?? null,
                'user_email'   => $payment->user->email ?? null,
                'redirect_url' => route('user_hosting.billing'),
            ];
        }

        if (str_starts_with($orderId, 'HST-UPG-')) {
            $payment = HostingPayment::where('invoice_number', $orderId)->first();
            if (!$payment) return null;

            return [
                'order_id'     => $orderId,
                'type'         => 'hosting_upgrade',
                'title'        => 'Upgrade Storage Hosting +1GB',
                'amount'       => (int) $payment->amount,
                'status'       => $payment->status,
                'user_id'      => $payment->user_id,
                'user_name'    => $payment->user->name ?? null,
                'user_email'   => $payment->user->email ?? null,
                'redirect_url' => route('user_hosting.billing'),
            ];
        }

        if (str_starts_with($orderId, 'WLT-TOPUP-')) {
            $tx = WalletTransaction::where('reference_id', $orderId)->first();
            if (!$tx) return null;

            $user = $tx->wallet->user ?? null;

            return [
                'order_id'     => $orderId,
                'type'         => 'wallet_topup',
                'title'        => 'Top Up Saldo Ryaze Wallet',
                'amount'       => (int) $tx->amount,
                'status'       => $tx->status === 'completed' ? 'paid' : $tx->status,
                'user_id'      => $user->id ?? null,
                'user_name'    => $user->name ?? null,
                'user_email'   => $user->email ?? null,
                'redirect_url' => route('user.wallet.history'),
            ];
        }

        if (str_starts_with($orderId, 'PRD-')) {
            $purchase = DigitalPurchase::where('order_id', $orderId)->first();
            if (!$purchase) return null;

            return [
                'order_id'     => $orderId,
                'type'         => 'digital_product',
                'title'        => 'Pembelian Produk Digital',
                'amount'       => (int) $purchase->amount,
                'status'       => $purchase->status,
                'user_id'      => $purchase->user_id ?? null,
                'user_name'    => $purchase->buyer_name ?? null,
                'user_email'   => $purchase->buyer_email ?? null,
                'redirect_url' => route('portfolio.products.status', $orderId),
            ];
        }

        if (str_starts_with($orderId, 'TIP-')) {
            $tip = Tip::where('order_id', $orderId)->first();
            if (!$tip) return null;

            return [
                'order_id'     => $orderId,
                'type'         => 'tip',
                'title'        => 'Dukungan / Tip Ryaze',
                'amount'       => (int) $tip->amount,
                'status'       => $tip->status === 'completed' ? 'paid' : $tip->status,
                'user_id'      => null,
                'user_name'    => $tip->giver_name ?? 'Supporter',
                'user_email'   => null,
                'redirect_url' => route('portfolio.tips'),
            ];
        }

        $joki = JokiPayment::where('invoice_number', $orderId)->first();
        if ($joki) {
            $client = $joki->order->client ?? null;
            return [
                'order_id'     => $orderId,
                'type'         => 'joki_payment',
                'title'        => 'Pembayaran Layanan Joki (' . $joki->payment_name . ')',
                'amount'       => (int) $joki->amount,
                'status'       => in_array($joki->status, ['paid', 'verified']) ? 'paid' : $joki->status,
                'user_id'      => $client->id ?? null,
                'user_name'    => $client->name ?? null,
                'user_email'   => $client->email ?? null,
                'redirect_url' => $joki->order ? route('user_joki.orders.detail', $joki->order->id) : route('user_joki.dashboard'),
            ];
        }

        return null;
    }

    private function fulfillHostingSubscription(string $invoiceNumber, string $method): bool
    {
        $payment = HostingPayment::where('invoice_number', $invoiceNumber)->first();
        if (!$payment) return false;

        if ($payment->status === 'paid') return true;

        $payment->update([
            'status'         => 'paid',
            'payment_method' => $method,
            'paid_at'        => now(),
        ]);

        $user = $payment->user;
        if ($user) {
            $billing = HostingBilling::where('user_id', $user->id)
                ->where('status', 'active')
                ->latest()
                ->first();

            $decodedNotes = json_decode($payment->notes, true);
            $selectedPlan = is_array($decodedNotes) ? ($decodedNotes['plan'] ?? 'starter') : ($payment->notes ?: 'starter');
            $billingCycle = is_array($decodedNotes) ? ($decodedNotes['billing_cycle'] ?? 'monthly') : 'monthly';

            if (!in_array($selectedPlan, ['starter', 'pro', 'business'])) {
                $selectedPlan = $billing ? ($billing->plan ?? 'starter') : 'starter';
            }

            $planLimits = User::getPlanLimits($selectedPlan);
            $planPrice  = User::getPlanPrice($selectedPlan, $billingCycle);

            if ($billing) {
                $oldPlanBaseStorage = User::getPlanLimits($billing->plan)['storage_mb'] ?? 1024;
                $currentLimit = $user->hosting_storage_limit_mb ?? 1024;
                $extraStorage = max(0, $currentLimit - $oldPlanBaseStorage);

                HostingBilling::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->where('id', '!=', $billing->id)
                    ->update(['status' => 'canceled']);

                $baseDate = ($selectedPlan !== $billing->getOriginal('plan') || Carbon::parse($billing->next_due_date)->isPast())
                    ? now()
                    : Carbon::parse($billing->next_due_date);
                $newDueDate = $billingCycle === 'yearly' ? $baseDate->addYear() : $baseDate->addMonth();

                $billing->update([
                    'plan'          => $selectedPlan,
                    'plan_name'     => 'Paket ' . ucfirst($selectedPlan),
                    'amount'        => $planPrice,
                    'billing_cycle' => $billingCycle,
                    'status'        => 'active',
                    'next_due_date' => $newDueDate,
                ]);

                $user->update(['hosting_storage_limit_mb' => $planLimits['storage_mb'] + $extraStorage]);
            } else {
                $currentLimit = $user->hosting_storage_limit_mb ?? 1024;
                $extraStorage = max(0, $currentLimit - 1024);

                $newDueDate = $billingCycle === 'yearly' ? now()->addYear() : now()->addMonth();

                HostingBilling::create([
                    'user_id'            => $user->id,
                    'hosting_project_id' => null,
                    'plan_name'          => 'Paket ' . ucfirst($selectedPlan),
                    'plan'               => $selectedPlan,
                    'amount'             => $planPrice,
                    'billing_cycle'      => $billingCycle,
                    'next_due_date'      => $newDueDate,
                    'status'             => 'active',
                ]);

                $user->update(['hosting_storage_limit_mb' => $planLimits['storage_mb'] + $extraStorage]);
            }

            // Aktifkan / deploy project yang tertahan
            $unpaidProjects = HostingProject::where('user_id', $user->id)
                ->whereIn('status', ['unpaid', 'suspended'])
                ->get();

            foreach ($unpaidProjects as $proj) {
                if ($proj->status === 'suspended') {
                    $subdomain = explode('.', $proj->ryaze_domain)[0];
                    $suspendFile = hosting_clients_dir() . "/{$subdomain}/.suspended";
                    if (file_exists($suspendFile)) {
                        @unlink($suspendFile);
                    }
                    $proj->update(['status' => 'active']);
                    $proj->deployments()->create([
                        'status' => 'ready',
                        'build_logs' => "> SISTEM: Project aktif kembali setelah pembayaran diterima via {$method}.",
                    ]);
                } else {
                    $proj->update(['status' => 'building']);
                    $proj->deployments()->create([
                        'status' => 'queued',
                        'build_logs' => "> Pembayaran via {$method} diterima!\n> Memulai build pipeline...\n> Project sedang disiapkan.",
                    ]);
                    \App\Jobs\AutoDeployProject::dispatch($proj);
                }
            }

            $user->notify(new SystemNotification('Pembayaran langganan hosting (' . $payment->invoice_number . ') berhasil diverifikasi via ' . $method . '.', 'success'));
            $this->distributeAffiliateCommission($user, $payment->amount, 'Pembayaran Langganan Hosting');
        }

        return true;
    }

    private function fulfillHostingStorageUpgrade(string $invoiceNumber, string $method): bool
    {
        $payment = HostingPayment::where('invoice_number', $invoiceNumber)->first();
        if (!$payment) return false;

        if ($payment->status === 'paid') return true;

        $payment->update([
            'status'         => 'paid',
            'payment_method' => $method,
            'paid_at'        => now(),
        ]);

        $user = $payment->user;
        if ($user) {
            $user->increment('hosting_storage_limit_mb', 1024);
            $user->notify(new SystemNotification('Pembayaran upgrade storage (' . $payment->invoice_number . ') berhasil via ' . $method . '. Kapasitas storage bertambah 1GB.', 'success'));
            $this->distributeAffiliateCommission($user, $payment->amount, 'Pembayaran Upgrade Storage Hosting');
        }

        return true;
    }

    private function fulfillWalletTopUp(string $referenceId, string $method): bool
    {
        $tx = WalletTransaction::where('reference_id', $referenceId)->first();
        if (!$tx) return false;

        if ($tx->status === 'completed') return true;

        $tx->update([
            'status'      => 'completed',
            'description' => 'Top Up Saldo via ' . $method,
        ]);

        $wallet = $tx->wallet;
        if ($wallet) {
            $wallet->increment('balance', $tx->amount);
            $wallet->user->notify(new SystemNotification('Top Up Wallet sebesar Rp ' . number_format($tx->amount, 0, ',', '.') . ' berhasil ditambahkan via ' . $method . '.', 'success'));
        }

        return true;
    }

    private function fulfillDigitalProduct(string $orderId, string $method): bool
    {
        $purchase = DigitalPurchase::where('order_id', $orderId)->first();
        if (!$purchase) return false;

        if ($purchase->status === 'paid') return true;

        $purchase->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        return true;
    }

    private function fulfillTip(string $orderId, string $method): bool
    {
        $tip = Tip::where('order_id', $orderId)->first();
        if (!$tip) return false;

        if ($tip->status === 'completed') return true;

        $tip->update([
            'status'  => 'completed',
            'paid_at' => now(),
        ]);

        return true;
    }

    private function fulfillJokiPayment(string $invoiceNumber, string $method): bool
    {
        $payment = JokiPayment::where('invoice_number', $invoiceNumber)->first();
        if (!$payment) return false;

        if ($payment->status === 'paid') return true;

        $payment->update([
            'status'         => 'paid',
            'payment_method' => $method,
            'paid_at'        => now(),
        ]);

        if ($payment->order && $payment->order->client) {
            $payment->order->client->notify(new SystemNotification('Pembayaran joki (' . $payment->invoice_number . ') diterima via ' . $method . '. Admin segera memprosesnya.', 'success'));
            $this->distributeAffiliateCommission($payment->order->client, $payment->amount, 'Pembayaran Layanan Joki');
        }

        return true;
    }

    private function distributeAffiliateCommission($user, $amount, $description): void
    {
        if ($user && $user->referred_by) {
            $referrer = $user->referrer;
            if ($referrer) {
                $commissionAmount = $amount * 0.10; // 10%

                AffiliateCommission::create([
                    'user_id'          => $referrer->id,
                    'referred_user_id' => $user->id,
                    'amount'           => $commissionAmount,
                    'description'      => $description,
                    'status'           => 'paid',
                ]);

                $referrerWallet = $referrer->wallet()->firstOrCreate(['user_id' => $referrer->id], ['balance' => 0]);
                $referrerWallet->increment('balance', $commissionAmount);

                WalletTransaction::create([
                    'wallet_id'   => $referrerWallet->id,
                    'amount'      => $commissionAmount,
                    'type'        => 'credit',
                    'description' => 'Komisi Affiliate: ' . $description,
                    'status'      => 'completed',
                ]);

                $referrer->notify(new SystemNotification('Anda mendapatkan komisi afiliasi sebesar Rp ' . number_format($commissionAmount, 0, ',', '.') . ' dari referal Anda.', 'success'));
            }
        }
    }
}

