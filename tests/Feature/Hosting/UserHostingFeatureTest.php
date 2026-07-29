<?php

namespace Tests\Feature\Hosting;

use App\Models\HostingProject;
use App\Models\HostingBilling;
use App\Models\User;
use Database\Seeders\UsersSeeders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Jobs\AutoDeployProject;
use Vinkla\Hashids\Facades\Hashids;

class UserHostingFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersSeeders::class);

        // 'user_hosting' role user from UsersSeeders
        $this->user = User::where('email', 'client.hosting@gmail.com')->first();
        
        // Mock external API and Queues
        Http::fake();
        Queue::fake();
    }

    public function test_user_can_view_hosting_dashboard()
    {
        $response = $this->actingAs($this->user)->get('/user/hosting/dashboard');
        $response->assertStatus(200);
    }

    public function test_user_can_subscribe_to_hosting()
    {
        \Illuminate\Support\Facades\DB::table('hosting_billings')->where('user_id', $this->user->id)->delete();
        \Illuminate\Support\Facades\DB::table('hosting_payments')->where('user_id', $this->user->id)->delete();
        $this->user->wallet()->updateOrCreate([], ['balance' => 500000]);

        $response = $this->actingAs($this->user)->post('/user/hosting/billing/subscribe', [
            'plan' => 'pro',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        
        $invoice = \App\Models\HostingPayment::where('user_id', $this->user->id)->first();
        $this->assertNotNull($invoice);

        $payResponse = $this->actingAs($this->user)->post('/user/hosting/billing/pay-wallet', [
            'invoice_number' => $invoice->invoice_number,
        ]);

        $payResponse->assertSessionHasNoErrors();
        $payResponse->assertSessionHas('success');

        $this->assertDatabaseHas('hosting_billings', [
            'user_id' => $this->user->id,
            'plan' => 'pro',
            'status' => 'active'
        ]);
    }

    public function test_user_can_create_hosting_project()
    {
        // Give user an active subscription first
        HostingBilling::create([
            'user_id' => $this->user->id,
            'plan_name' => 'Pro Plan',
            'plan' => 'pro',
            'amount' => 50000,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'next_due_date' => now()->addMonth()
        ]);

        $response = $this->actingAs($this->user)->post('/user/hosting/store', [
            'source_type' => 'template',
            'template_key' => 'html_landing',
            'project_name' => 'My Hosting Site Unique',
            'domain_extension' => '.ryaze.my.id',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        
        $project = HostingProject::where('project_name', 'My Hosting Site Unique')->first();
        
        $this->assertNotNull($project);
        Queue::assertPushed(AutoDeployProject::class);
    }

    public function test_user_can_manage_database()
    {
        HostingBilling::create([
            'user_id' => $this->user->id,
            'plan_name' => 'Pro Plan',
            'plan' => 'pro',
            'amount' => 50000,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'next_due_date' => now()->addMonth()
        ]);

        $response = $this->actingAs($this->user)->post('/user/hosting/databases', [
            'db_name' => 'testdb2',
            'db_username' => 'testuser',
            'db_password' => 'secret1234'
        ]);
        
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
    }
}
