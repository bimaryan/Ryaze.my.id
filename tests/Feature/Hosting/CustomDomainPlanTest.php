<?php

namespace Tests\Feature\Hosting;

use App\Models\HostingBilling;
use App\Models\HostingDomain;
use App\Models\HostingProject;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\UsersSeeders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Custom domain & subdomain hanya untuk paket Pro dan Business.
 *
 * Yang dikunci hanya pembuatan hostname BARU — domain yang sudah ada tetap
 * bisa disambungkan & dilayani supaya situs yang sudah jalan tidak ikut mati
 * saat paket pelanggan turun.
 */
class CustomDomainPlanTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UsersSeeders::class);
        $this->user = User::where('email', 'client.hosting@gmail.com')->firstOrFail();

        // Jangan sampai ada panggilan HTTP liar (mis. ke Cloudflare).
        Http::preventStrayRequests();
        Http::fake(function ($request) {
            $url = $request->url();

            // Ambil account id dari zone utama.
            if (str_contains($url, '/zones/zone-primary')) {
                return Http::response([
                    'success' => true,
                    'result' => ['account' => ['id' => 'acct-1']],
                ], 200);
            }

            // Cek apakah domain sudah ada di akun ini -> belum ada.
            if ($request->method() === 'GET' && str_ends_with($url, '/zones')) {
                return Http::response(['success' => true, 'result' => []], 200);
            }

            // Buat zone baru.
            if ($request->method() === 'POST' && str_ends_with($url, '/zones')) {
                return Http::response([
                    'success' => true,
                    'result' => ['id' => 'zone-new', 'name_servers' => ['a.ns.cloudflare.com']],
                ], 200);
            }

            return Http::response(['success' => true, 'result' => []], 200);
        });

        $this->clientsDir = sys_get_temp_dir().'/ryaze-plan-'.bin2hex(random_bytes(6));
        config([
            'hosting.client_dir' => $this->clientsDir,
            'services.cloudflare.zone_id' => 'zone-primary',
            'services.cloudflare.tunnel_url' => 'tunnel.cfargotunnel.com',
            'services.cloudflare_tunnel.account_id' => null,
            'services.cloudflare_tunnel.tunnel_id' => null,
            'services.cloudflare_tunnel.api_token' => null,
        ]);
        mkdir($this->clientsDir, 0755, true);
    }

    protected string $clientsDir;

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->clientsDir);

        parent::tearDown();
    }

    private function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $path = $dir.'/'.$entry;
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }
        @rmdir($dir);
    }

    /**
     * Paket "free" TIDAK bisa dibuat lewat INSERT di test: migration yang
     * melebar enum plan di-skip untuk sqlite (pakai SQL MySQL mentah), jadi
     * di test enum masih ['starter','pro','business']. Di MySQL produksi 'free'
     * valid.
     *
     * Paket free direpresentasikan sebagai user tanpa langganan aktif —
     * persis seperti getActivePlan() yang jatuh ke 'free'.
     */
    private function subscribeToFree(): void
    {
        HostingBilling::where('user_id', $this->user->id)->delete();
    }

    /** Matikan paket free supaya getActivePlan() benar-benar null. */
    private function disableFreePlan(): void
    {
        Setting::setVal('plan_free_active', '0');
    }

    /** Berlangganan aktif dengan plan tertentu. */
    private function subscribeTo(string $plan): void
    {
        HostingBilling::where('user_id', $this->user->id)->delete();

        HostingBilling::create([
            'user_id' => $this->user->id,
            'plan_name' => 'Paket '.ucfirst($plan),
            'plan' => $plan,
            'amount' => 10000,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'next_due_date' => now()->addMonth(),
        ]);
    }

    private function makeActiveRootDomain(): HostingDomain
    {
        return HostingDomain::create([
            'user_id' => $this->user->id,
            'domain_name' => 'tokoanda.com',
            'type' => HostingDomain::TYPE_CUSTOM,
            'ssl_status' => 'active',
            'cf_zone_id' => 'zone-tokoanda',
        ]);
    }

    /* -----------------------------------------------------------------
     | Deteksi paket
     |-------------------------------------------------------------- */

    public function test_only_pro_and_business_are_listed_as_allowed_plans()
    {
        $this->assertSame(['pro', 'business'], User::customDomainPlans());
    }

    public function test_free_plan_is_locked_with_upgrade_message()
    {
        $this->subscribeToFree();
        $check = $this->user->fresh()->canManageCustomDomain();

        $this->assertFalse($check['allowed']);
        $this->assertStringContainsString('Pro', $check['message']);
        $this->assertStringContainsString('Business', $check['message']);
    }

    public function test_starter_plan_is_locked()
    {
        $this->subscribeTo('starter');
        $this->assertFalse($this->user->fresh()->canManageCustomDomain()['allowed']);
    }

    public function test_pro_and_business_are_allowed()
    {
        foreach (['pro', 'business'] as $plan) {
            $this->subscribeTo($plan);
            $this->assertTrue(
                $this->user->fresh()->canManageCustomDomain()['allowed'],
                "Paket {$plan} seharusnya boleh memakai custom domain."
            );
        }
    }

    public function test_superadmin_bypasses_the_lock()
    {
        $admin = User::factory()->create(['role' => 'superadmin']);
        $this->assertTrue($admin->canManageCustomDomain()['allowed']);
    }

    public function test_user_without_active_subscription_is_locked()
    {
        $this->disableFreePlan();
        $user = User::factory()->create(['role' => 'user_hosting']);
        $check = $user->canManageCustomDomain();

        $this->assertFalse($check['allowed']);
        $this->assertStringContainsString('berlangganan aktif', $check['message']);
    }

    /* -----------------------------------------------------------------
     | Pembuatan custom domain
     |-------------------------------------------------------------- */

    public function test_free_user_cannot_add_custom_domain()
    {
        $this->subscribeToFree();

        $this->actingAs($this->user)
            ->post('/user/hosting/domains', ['domain_name' => 'tokosaya.com'])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('hosting_domains', ['domain_name' => 'tokosaya.com']);
    }

    public function test_starter_user_cannot_add_custom_domain()
    {
        $this->subscribeTo('starter');

        $this->actingAs($this->user)
            ->post('/user/hosting/domains', ['domain_name' => 'tokosaya.com'])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('hosting_domains', 0);
    }

    public function test_pro_user_can_add_custom_domain()
    {
        $this->subscribeTo('pro');

        $this->actingAs($this->user)
            ->post('/user/hosting/domains', ['domain_name' => 'tokosaya.com'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('hosting_domains', ['domain_name' => 'tokosaya.com']);
    }

    /* -----------------------------------------------------------------
     | Pembuatan subdomain
     |-------------------------------------------------------------- */

    public function test_free_user_cannot_add_subdomain_even_with_active_custom_domain()
    {
        $this->subscribeToFree();
        $parent = $this->makeActiveRootDomain();

        $this->actingAs($this->user)
            ->post('/user/hosting/domains/subdomain', [
                'label' => 'blog',
                'parent_id' => $parent->id,
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('hosting_domains', ['domain_name' => 'blog.tokoanda.com']);
    }

    public function test_pro_user_can_add_subdomain()
    {
        $this->subscribeTo('pro');
        $parent = $this->makeActiveRootDomain();

        $this->actingAs($this->user)
            ->post('/user/hosting/domains/subdomain', [
                'label' => 'blog',
                'parent_id' => $parent->id,
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('hosting_domains', [
            'domain_name' => 'blog.tokoanda.com',
            'type' => HostingDomain::TYPE_SUBDOMAIN,
        ]);
    }

    public function test_business_user_can_add_subdomain()
    {
        $this->subscribeTo('business');
        $parent = $this->makeActiveRootDomain();

        $this->actingAs($this->user)
            ->post('/user/hosting/domains/subdomain', [
                'label' => 'blog',
                'parent_id' => $parent->id,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('hosting_domains', ['domain_name' => 'blog.tokoanda.com']);
    }

    /* -----------------------------------------------------------------
     | Domain yang sudah ada tidak dikunci
     |-------------------------------------------------------------- */

    public function test_downgraded_user_can_still_assign_existing_domain_to_project()
    {
        // Domain dibuat duluan saat paket Pro, lalu paketnya turun ke Free.
        $parent = $this->makeActiveRootDomain();
        $project = HostingProject::create([
            'user_id' => $this->user->id,
            'project_name' => 'Toko Saya',
            'ryaze_domain' => 'tokosaya.ryaze.my.id',
            'framework' => 'html',
            'status' => 'active',
        ]);
        $this->subscribeToFree();

        $this->actingAs($this->user)
            ->post("/user/hosting/domains/{$parent->hashid}/assign", ['project_id' => $project->id])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertSame($project->id, $parent->fresh()->project_id);
    }

    public function test_downgraded_user_can_still_detach_and_delete()
    {
        $parent = $this->makeActiveRootDomain();
        $project = HostingProject::create([
            'user_id' => $this->user->id,
            'project_name' => 'Toko Saya',
            'ryaze_domain' => 'tokosaya.ryaze.my.id',
            'framework' => 'html',
            'status' => 'active',
        ]);
        $parent->update(['project_id' => $project->id]);
        $this->subscribeToFree();

        $this->actingAs($this->user)
            ->post("/user/hosting/domains/{$parent->hashid}/detach")
            ->assertSessionHas('success');
        $this->assertNull($parent->fresh()->project_id);

        $this->actingAs($this->user)
            ->delete("/user/hosting/domains/{$parent->hashid}")
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('hosting_domains', ['id' => $parent->id]);
    }

    public function test_downgraded_user_can_still_run_check_status()
    {
        $this->subscribeToFree();
        $parent = $this->makeActiveRootDomain();

        $this->actingAs($this->user)
            ->post("/user/hosting/domains/{$parent->hashid}/status")
            ->assertSessionHasNoErrors();
    }

    /* -----------------------------------------------------------------
     | Tampilan halaman
     |-------------------------------------------------------------- */

    public function test_locked_user_sees_upgrade_prompt_on_domains_page()
    {
        $this->subscribeToFree();

        $this->actingAs($this->user)
            ->get('/user/hosting/domains')
            ->assertStatus(200)
            ->assertSee('Custom domain butuh paket Pro atau Business')
            ->assertSee('Upgrade Paket');
    }

    public function test_locked_user_does_not_see_add_buttons()
    {
        $this->subscribeToFree();

        $html = $this->actingAs($this->user)->get('/user/hosting/domains')->getContent();

        $this->assertStringNotContainsString("addDomainModal').classList.remove", $html);
        $this->assertStringNotContainsString("addSubdomainModal').classList.remove", $html);
    }

    public function test_allowed_user_sees_add_buttons()
    {
        $this->subscribeTo('pro');

        $html = $this->actingAs($this->user)->get('/user/hosting/domains')->getContent();

        $this->assertStringContainsString("addDomainModal').classList.remove", $html);
        $this->assertStringContainsString("addSubdomainModal').classList.remove", $html);
        $this->assertStringNotContainsString('Custom domain butuh paket Pro atau Business', $html);
    }
}