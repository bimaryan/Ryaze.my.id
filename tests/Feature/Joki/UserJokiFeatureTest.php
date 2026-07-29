<?php

namespace Tests\Feature\Joki;

use App\Models\JokiOrder;
use App\Models\JokiPayment;
use App\Models\JokiService;
use App\Models\User;
use Database\Seeders\UsersSeeders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Vinkla\Hashids\Facades\Hashids;

class UserJokiFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersSeeders::class);

        $this->user = User::where('email', 'dea@gmail.com')->first();
        $this->admin = User::where('email', 'admin.joki@ryaze.my.id')->first();

        // Prevent external API calls
        Http::fake();
        Storage::fake('public');
    }

    public function test_user_can_view_dashboard()
    {
        $response = $this->actingAs($this->user)->get('/user/joki/dashboard');
        $response->assertStatus(200);
    }

    public function test_user_can_create_joki_order()
    {
        $service = JokiService::create([
            'name' => 'Web App',
            'slug' => 'web-app',
            'description' => 'Test',
            'base_price' => 100000,
            'is_active' => true
        ]);

        $response = $this->actingAs($this->user)->post('/user/joki/store', [
            'service_id' => $service->id,
            'project_name' => 'My Test Project',
            'description' => 'A detailed description',
            'tech_stack' => 'Laravel, Vue',
            'deadline' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('user_joki.dashboard'));
        $this->assertDatabaseHas('joki_orders', [
            'client_id' => $this->user->id,
            'project_name' => 'My Test Project',
            'status' => 'pending'
        ]);
    }

    public function test_user_can_upload_payment_proof()
    {
        $service = JokiService::create([
            'name' => 'Mobile App',
            'slug' => 'mobile-app',
            'description' => 'Test',
            'base_price' => 100000,
            'is_active' => true
        ]);

        $order = JokiOrder::create([
            'client_id' => $this->user->id,
            'service_id' => $service->id,
            'order_number' => 'JOKI-12345',
            'project_name' => 'Test Project',
            'description' => 'Test Desc',
            'tech_stack' => 'PHP',
            'deadline' => now()->addDays(7),
            'status' => 'progress'
        ]);

        $payment = JokiPayment::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-TEST-123',
            'payment_name' => 'DP',
            'amount' => 150000,
            'payment_method' => 'Bank Transfer',
            'status' => 'unpaid'
        ]);

        $hashid = Hashids::encode($payment->id);
        $file = UploadedFile::fake()->image('proof.jpg');

        $response = $this->actingAs($this->user)->post("/user/joki/orders/payment/{$hashid}/proof", [
            'proof_image' => $file
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('joki_payments', [
            'id' => $payment->id,
            'status' => 'paid'
        ]);
    }

    public function test_user_can_request_revision()
    {
        $service = JokiService::create([
            'name' => 'UI UX',
            'slug' => 'ui-ux',
            'description' => 'Test',
            'base_price' => 100000,
            'is_active' => true
        ]);

        $order = JokiOrder::create([
            'client_id' => $this->user->id,
            'service_id' => $service->id,
            'order_number' => 'JOKI-12345',
            'project_name' => 'Test Project',
            'description' => 'Test Desc',
            'tech_stack' => 'PHP',
            'deadline' => now()->addDays(7),
            'status' => 'progress'
        ]);

        $hashid = Hashids::encode($order->id);

        $response = $this->actingAs($this->user)->post("/user/joki/orders/{$hashid}/revision", [
            'revision_note' => 'Tolong perbaiki bagian header.'
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('joki_revisions', [
            'order_id' => $order->id,
            'revision_note' => 'Tolong perbaiki bagian header.',
            'status' => 'pending'
        ]);
        
        $order->refresh();
        $this->assertEquals('review', $order->status);
    }
}
