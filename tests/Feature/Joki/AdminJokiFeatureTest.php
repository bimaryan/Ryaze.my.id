<?php

namespace Tests\Feature\Joki;

use App\Models\JokiOrder;
use App\Models\JokiService;
use App\Models\User;
use Database\Seeders\UsersSeeders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Vinkla\Hashids\Facades\Hashids;

class AdminJokiFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersSeeders::class);

        $this->admin = User::where('email', 'admin.joki@ryaze.my.id')->first();
        $this->client = User::where('email', 'dea@gmail.com')->first();

        Http::fake();
    }

    public function test_admin_can_view_orders()
    {
        $response = $this->actingAs($this->admin)->get('/admin/joki/orders');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_joki_service()
    {
        $response = $this->actingAs($this->admin)->post('/admin/joki/services', [
            'name' => 'API Development',
            'slug' => 'api-development',
            'description' => 'Building RESTful APIs',
            'base_price' => 500000,
            'is_active' => true
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('joki_services', [
            'name' => 'API Development',
            'base_price' => 500000
        ]);
    }

    public function test_admin_can_update_order_status()
    {
        $service = JokiService::create([
            'name' => 'API Dev',
            'slug' => 'api-dev',
            'description' => 'Building RESTful APIs',
            'base_price' => 500000,
            'is_active' => true
        ]);

        $order = JokiOrder::create([
            'client_id' => $this->client->id,
            'service_id' => $service->id,
            'order_number' => 'JOKI-999',
            'project_name' => 'Pending Project',
            'description' => 'Desc',
            'tech_stack' => 'Vue',
            'deadline' => now()->addDays(5),
            'status' => 'pending'
        ]);

        $hashid = Hashids::encode($order->id);

        $response = $this->actingAs($this->admin)->put("/admin/joki/orders/{$hashid}", [
            'status' => 'progress',
            'progress' => 10,
            'worker_id' => $this->admin->id
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('joki_orders', [
            'id' => $order->id,
            'status' => 'progress',
            'worker_id' => $this->admin->id
        ]);
    }

    public function test_admin_can_add_milestone()
    {
        $service = JokiService::create([
            'name' => 'API Dev 2',
            'slug' => 'api-dev-2',
            'description' => 'Building RESTful APIs',
            'base_price' => 500000,
            'is_active' => true
        ]);

        $order = JokiOrder::create([
            'client_id' => $this->client->id,
            'service_id' => $service->id,
            'order_number' => 'JOKI-999',
            'project_name' => 'Pending Project',
            'description' => 'Desc',
            'tech_stack' => 'Vue',
            'deadline' => now()->addDays(5),
            'status' => 'progress'
        ]);

        $hashid = Hashids::encode($order->id);

        $response = $this->actingAs($this->admin)->post("/admin/joki/orders/{$hashid}/milestone", [
            'title' => 'Database Design',
            'description' => 'Finished ERD',
            'status' => 'done'
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('joki_milestones', [
            'order_id' => $order->id,
            'title' => 'Database Design',
            'status' => 'done'
        ]);
    }
}
