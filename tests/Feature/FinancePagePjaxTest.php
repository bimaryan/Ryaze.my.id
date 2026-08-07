<?php

namespace Tests\Feature;

use App\Models\HostingPayment;
use App\Models\JokiOrder;
use App\Models\JokiPayment;
use App\Models\JokiService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancePagePjaxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_finance_page_renders_with_pjax_container_and_data(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin']);
        $client = User::factory()->create(['role' => 'user_joki']);

        $service = JokiService::create([
            'name' => 'Web App',
            'slug' => 'web-app',
            'base_price' => 500000,
            'is_active' => true,
        ]);

        $order = JokiOrder::create([
            'order_number' => 'JK-0001',
            'client_id' => $client->id,
            'service_id' => $service->id,
            'project_name' => 'Landing Page',
            'description' => 'Landing page sederhana',
            'status' => 'completed',
            'price' => 500000,
        ]);

        JokiPayment::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-JK-0001',
            'payment_name' => 'Pakasir',
            'amount' => 500000,
            'status' => 'paid',
            'payment_method' => 'Pakasir',
            'paid_at' => now(),
        ]);

        HostingPayment::create([
            'user_id' => $client->id,
            'invoice_number' => 'INV-HS-0001',
            'amount' => 150000,
            'status' => 'paid',
            'payment_method' => 'Wallet',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('superadmin.finance'));
        $response->assertOk();
        $response->assertSee('pjax-container', false);
        $response->assertSee('INV-JK-0001');
        $response->assertSee('INV-HS-0001');
        $response->assertSee('Rp' . ' 500.000');
    }

    public function test_finance_filters_and_pagination_return_pjax_page(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)->get(route('superadmin.finance', [
            'service' => 'joki',
            'method' => 'Pakasir',
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
        ]));
        $response->assertOk();
        $response->assertSee('pjax-container', false);

        $response = $this->actingAs($admin)->get(route('superadmin.finance', ['page' => 2]));
        $response->assertOk();
        $response->assertSee('pjax-container', false);
    }
}
