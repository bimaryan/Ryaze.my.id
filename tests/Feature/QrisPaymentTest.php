<?php

namespace Tests\Feature;

use App\Models\HostingBilling;
use App\Models\HostingPayment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\PaymentFulfillmentService;
use App\Services\QrisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrisPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_qris_service_generates_valid_dynamic_payload(): void
    {
        $service = new QrisService();
        $payload = $service->generateDynamicPayload(75000, 'HST-INV-TEST123');

        $this->assertNotEmpty($payload);
        $tags = $service->parseTlv($payload);

        $this->assertEquals('12', $tags['01'], 'Point of initiation must be 12 (Dynamic)');
        $this->assertEquals('75000', $tags['54'], 'Amount must match exactly');
        $this->assertStringContainsString('HST-INV-TEST123', $tags['62'] ?? '');

        // Check CRC is 4 hex chars
        $crc = substr($payload, -4);
        $this->assertEquals(4, strlen($crc));
        $this->assertEquals(strtoupper($crc), $crc);

        // Verify SVG data uri
        $dataUri = $service->generateQrDataUri($payload, 250);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $dataUri);
    }

    public function test_qris_payment_page_loads_for_wallet_topup(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 0]);
        $tx = WalletTransaction::create([
            'wallet_id'    => $wallet->id,
            'reference_id' => 'WLT-TOPUP-ABCDEF',
            'amount'       => 25000,
            'type'         => 'credit',
            'description'  => 'Top Up Saldo via QRIS Dinamis',
            'status'       => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('payment.qris', ['order_id' => $tx->reference_id]));
        $response->assertStatus(200);
        $response->assertSee('Ryaze, Komputer');
        $response->assertSee('25000');
        $response->assertSee('WLT-TOPUP-ABCDEF');
    }

    public function test_qris_payment_page_loads_for_hosting_invoice(): void
    {
        $user = User::factory()->create();
        $invoice = HostingPayment::create([
            'user_id'        => $user->id,
            'invoice_number' => 'HST-INV-XYZ123',
            'amount'         => 50000,
            'status'         => 'unpaid',
            'notes'          => json_encode(['plan' => 'starter', 'billing_cycle' => 'monthly']),
        ]);

        $response = $this->actingAs($user)->get(route('payment.qris', ['order_id' => $invoice->invoice_number]));
        $response->assertStatus(200);
        $response->assertSee('HST-INV-XYZ123');
        $response->assertSee('50000');
    }

    public function test_qris_status_polling_returns_json(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 0]);
        $tx = WalletTransaction::create([
            'wallet_id'    => $wallet->id,
            'reference_id' => 'WLT-TOPUP-STATUS99',
            'amount'       => 30000,
            'type'         => 'credit',
            'description'  => 'Top Up Saldo',
            'status'       => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('payment.qris.status', ['order_id' => $tx->reference_id]));
        $response->assertStatus(200);
        $response->assertJson([
            'success'  => true,
            'order_id' => 'WLT-TOPUP-STATUS99',
            'is_paid'  => false,
            'status'   => 'pending',
        ]);

        // Fulfill payment
        $fulfillment = app(PaymentFulfillmentService::class);
        $fulfillment->fulfill($tx->reference_id, 'QRIS');

        $responsePaid = $this->actingAs($user)->get(route('payment.qris.status', ['order_id' => $tx->reference_id]));
        $responsePaid->assertJson([
            'success' => true,
            'is_paid' => true,
            'status'  => 'paid',
        ]);

        // Verify balance was incremented
        $wallet->refresh();
        $this->assertEquals(30000, $wallet->balance);
    }
}

