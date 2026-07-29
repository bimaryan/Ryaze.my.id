<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UsersSeeders;

class UserRolesFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run the UsersSeeder to populate the users
        $this->seed(UsersSeeders::class);
    }

    public function test_superadmin_can_access_superadmin_dashboard()
    {
        $superadmin = User::where('email', 'superadmin@ryaze.my.id')->first();
        
        $response = $this->actingAs($superadmin)->get('/superadmin/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_joki_can_access_admin_joki_dashboard()
    {
        $adminJoki = User::where('email', 'admin.joki@ryaze.my.id')->first();
        
        $response = $this->actingAs($adminJoki)->get('/admin/joki/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_hosting_can_access_admin_hosting_dashboard()
    {
        $adminHosting = User::where('email', 'admin.hosting@ryaze.my.id')->first();
        
        $response = $this->actingAs($adminHosting)->get('/admin/hosting/dashboard');
        $response->assertStatus(200);
    }

    public function test_user_joki_can_access_user_joki_dashboard()
    {
        $userJoki = User::where('email', 'dea@gmail.com')->first();
        
        $response = $this->actingAs($userJoki)->get('/user/joki/dashboard');
        $response->assertStatus(200);
    }

    public function test_user_hosting_can_access_user_hosting_dashboard()
    {
        $userHosting = User::where('email', 'client.hosting@gmail.com')->first();
        
        $response = $this->actingAs($userHosting)->get('/user/hosting/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_joki_cannot_access_superadmin_dashboard()
    {
        $adminJoki = User::where('email', 'admin.joki@ryaze.my.id')->first();
        
        $response = $this->actingAs($adminJoki)->get('/superadmin/dashboard');
        $response->assertForbidden(); // Check if middleware returns 403
    }

    public function test_user_hosting_cannot_access_admin_hosting_dashboard()
    {
        $userHosting = User::where('email', 'client.hosting@gmail.com')->first();
        
        $response = $this->actingAs($userHosting)->get('/admin/hosting/dashboard');
        $response->assertForbidden();
    }
}
