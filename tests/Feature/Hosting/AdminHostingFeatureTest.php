<?php

namespace Tests\Feature\Hosting;

use App\Models\HostingProject;
use App\Models\HostingBilling;
use App\Models\User;
use Database\Seeders\UsersSeeders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Vinkla\Hashids\Facades\Hashids;

class AdminHostingFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersSeeders::class);

        $this->admin = User::where('email', 'admin.hosting@ryaze.my.id')->first();
        $this->client = User::where('email', 'client.hosting@gmail.com')->first();

        Http::fake();
    }

    public function test_admin_can_view_hosting_projects()
    {
        $response = $this->actingAs($this->admin)->get('/admin/hosting/projects');
        $response->assertStatus(200);
    }

    public function test_admin_can_suspend_project()
    {
        $project = HostingProject::create([
            'user_id' => $this->client->id,
            'project_name' => 'To Suspend',
            'framework' => 'html',
            'repo_source' => 'template:html',
            'branch' => 'main',
            'source_type' => 'template',
            'ryaze_domain' => 'suspend.ryaze.my.id',
            'status' => 'active',
        ]);

        $hashid = Hashids::encode($project->id);

        // Ensure directory exists for touch() in controller
        @mkdir(base_path('../www/sites/hosting_clients/suspend'), 0777, true);

        $response = $this->actingAs($this->admin)->patch("/admin/hosting/{$hashid}/suspend");
        
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('hosting_projects', [
            'id' => $project->id,
            'status' => 'suspended'
        ]);
    }

    public function test_admin_can_verify_billing()
    {
        $payment = \App\Models\HostingPayment::create([
            'user_id' => $this->client->id,
            'invoice_number' => 'HST-INV-TEST123',
            'amount' => 50000,
            'payment_method' => 'bank_transfer',
            'status' => 'unpaid'
        ]);

        $hashid = Hashids::encode($payment->id);

        $response = $this->actingAs($this->admin)->put("/admin/hosting/billing/{$hashid}/verify", [
            'status' => 'paid'
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('hosting_payments', [
            'id' => $payment->id,
            'status' => 'paid'
        ]);
    }
}
