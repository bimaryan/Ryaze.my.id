<?php

namespace Tests\Feature\Hosting;

use App\Models\HostingBilling;
use App\Models\HostingDomain;
use App\Models\HostingProject;
use App\Models\User;
use Database\Seeders\UsersSeeders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Subdomain management: subdomain dari domain milik user sendiri, subdomain
 * gratis dari domain platform, dan dampaknya ke routing nginx.
 *
 * Semua panggilan Cloudflare di-fake supaya test tidak menyentuh zona dan
 * tunnel produksi.
 */
class DomainSubdomainFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    /** Folder sementara untuk file mapping .domains milik test. */
    protected string $clientsDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UsersSeeders::class);
        $this->user = User::where('email', 'client.hosting@gmail.com')->firstOrFail();

        // Custom domain & subdomain hanya Pro & Business — test ini menguji
        // perilaku subdomain, jadi berlangganan Pro dulu. Pembatasan paketnya
        // sendiri punya test sendiri di CustomDomainPlanTest.
        HostingBilling::create([
            'user_id' => $this->user->id,
            'plan_name' => 'Paket Pro',
            'plan' => 'pro',
            'amount' => 30000,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'next_due_date' => now()->addMonth(),
        ]);

        Http::fake();
        \Illuminate\Support\Facades\Queue::fake();

        // Pindahkan direktori clients ke tmp supaya test tidak menulis ke
        // /www/sites/hosting_clients yang dipakai produksi.
        $this->clientsDir = sys_get_temp_dir().'/ryaze-test-'.bin2hex(random_bytes(6));
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

    private function makeProject(string $folder, ?int $ownerId = null): HostingProject
    {
        return HostingProject::create([
            'user_id' => $ownerId ?? $this->user->id,
            'project_name' => 'Project '.$folder,
            'ryaze_domain' => $folder.'.ryaze.my.id',
            'framework' => 'html',
            'status' => 'active',
        ]);
    }

    private function makeRootDomain(string $name = 'tokoanda.com', string $ssl = 'active'): HostingDomain
    {
        return HostingDomain::create([
            'user_id' => $this->user->id,
            'project_id' => null,
            'domain_name' => $name,
            'type' => HostingDomain::TYPE_CUSTOM,
            'ssl_status' => $ssl,
            'cf_zone_id' => 'zone-'.$name,
            'nameservers' => ['nelly.ns.cloudflare.com'],
        ]);
    }

    private function mapFile(string $hostname): ?string
    {
        $path = $this->clientsDir.'/.domains/'.$hostname;

        return file_exists($path) ? trim(file_get_contents($path)) : null;
    }

    /* -----------------------------------------------------------------
     | Subdomain dari domain sendiri
     |-------------------------------------------------------------- */

    public function test_user_can_create_subdomain_from_own_domain()
    {
        $parent = $this->makeRootDomain();

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
            'parent_id' => $parent->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_subdomain_accepts_full_hostname_as_input()
    {
        $parent = $this->makeRootDomain();

        $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
            'label' => 'https://blog.tokoanda.com/some/path',
            'parent_id' => $parent->id,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('hosting_domains', ['domain_name' => 'blog.tokoanda.com']);
    }

    public function test_subdomain_is_rejected_when_parent_domain_not_active()
    {
        $parent = $this->makeRootDomain('pending.com', 'pending');

        $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
            'label' => 'blog',
            'parent_id' => $parent->id,
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('hosting_domains', ['domain_name' => 'blog.pending.com']);
    }

    public function test_subdomain_cannot_be_created_under_another_users_domain()
    {
        $other = User::factory()->create(['role' => 'user']);
        $foreign = HostingDomain::create([
            'user_id' => $other->id,
            'domain_name' => 'asing.com',
            'type' => HostingDomain::TYPE_CUSTOM,
            'ssl_status' => 'active',
            'cf_zone_id' => 'zone-asing',
        ]);

        $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
            'label' => 'blog',
            'parent_id' => $foreign->id,
        ])->assertStatus(404);

        $this->assertDatabaseMissing('hosting_domains', ['domain_name' => 'blog.asing.com']);
    }

    public function test_duplicate_subdomain_label_is_rejected()
    {
        $parent = $this->makeRootDomain();

        $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
            'label' => 'blog',
            'parent_id' => $parent->id,
        ])->assertSessionHas('success');

        $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
            'label' => 'BLOG',
            'parent_id' => $parent->id,
        ])->assertSessionHas('error');

        $this->assertSame(1, HostingDomain::where('domain_name', 'blog.tokoanda.com')->count());
    }

    public function test_nested_subdomain_is_rejected()
    {
        $parent = $this->makeRootDomain();

        $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
            'label' => 'a.b',
            'parent_id' => $parent->id,
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('hosting_domains', ['domain_name' => 'a.b.tokoanda.com']);
    }

    public function test_invalid_label_is_rejected()
    {
        $parent = $this->makeRootDomain();

        // Awal/akhir tanda hubung harus ditolak dengan pesan jelas, bukan
        // diam-diam dipangkas menjadi label valid.
        foreach (['-lead', 'trail-', 'spa ce', 'excl!'] as $bad) {
            $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
                'label' => $bad,
                'parent_id' => $parent->id,
            ])->assertSessionHas('error');
        }

        // Hanya domain induk yang tersisa — tidak ada subdomain tercipta.
        $this->assertSame(1, HostingDomain::where('user_id', $this->user->id)->count());
    }

    /* -----------------------------------------------------------------
     | Subdomain hanya boleh dari custom domain milik sendiri
     |-------------------------------------------------------------- */

    public function test_platform_subdomain_endpoint_no_longer_exists()
    {
        $this->actingAs($this->user)->post('/user/hosting/domains/platform-subdomain', [
            'label' => 'toko',
            'base' => 'ryaze.my.id',
        ])->assertStatus(405);

        $this->assertDatabaseMissing('hosting_domains', ['domain_name' => 'toko.ryaze.my.id']);
    }

    public function test_subdomain_requires_an_active_custom_domain()
    {
        // Tanpa domain induk, endpoint harus menolak.
        $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
            'label' => 'blog',
            'parent_id' => 99999,
        ])->assertSessionHasErrors('parent_id');

        $this->assertDatabaseMissing('hosting_domains', ['domain_name' => 'blog.tokoanda.com']);
    }


    /* -----------------------------------------------------------------
     | Routing
     |-------------------------------------------------------------- */

    public function test_subdomain_assigned_to_project_writes_nginx_mapping()
    {
        $project = $this->makeProject('toko');
        $parent = $this->makeRootDomain();

        $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
            'label' => 'blog',
            'parent_id' => $parent->id,
            'project_id' => $project->id,
        ])->assertSessionHasNoErrors();

        $this->assertSame('toko', $this->mapFile('blog.tokoanda.com'));
    }

    public function test_custom_domain_assigned_to_project_writes_nginx_mapping()
    {
        $project = $this->makeProject('toko');
        $domain = $this->makeRootDomain();

        $this->actingAs($this->user)->post("/user/hosting/domains/{$domain->hashid}/assign", [
            'project_id' => $project->id,
        ])->assertSessionHasNoErrors();

        $this->assertSame('toko', $this->mapFile('tokoanda.com'));
        $this->assertSame('toko', $this->mapFile('www.tokoanda.com'));
    }

    public function test_unassigned_subdomain_writes_no_mapping()
    {
        $parent = $this->makeRootDomain();

        $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
            'label' => 'tanpaproyek',
            'parent_id' => $parent->id,
        ])->assertSessionHasNoErrors();

        $this->assertNull($this->mapFile('tanpaproyek.tokoanda.com'));
    }

    public function test_one_project_can_serve_many_hostnames()
    {
        $project = $this->makeProject('toko');
        $first = $this->makeRootDomain();
        $second = $this->makeRootDomain('kedua.com');

        $this->actingAs($this->user)->post("/user/hosting/domains/{$first->hashid}/assign", [
            'project_id' => $project->id,
        ])->assertSessionHasNoErrors();

        // Aturan lama "satu project satu domain" harus sudah dilepas.
        $this->actingAs($this->user)->post("/user/hosting/domains/{$second->hashid}/assign", [
            'project_id' => $project->id,
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertSame('toko', $this->mapFile('tokoanda.com'));
        $this->assertSame('toko', $this->mapFile('kedua.com'));
        $this->assertSame(2, HostingDomain::where('project_id', $project->id)->count());
    }

    public function test_detach_removes_mapping_but_keeps_record()
    {
        $project = $this->makeProject('toko');
        $domain = $this->makeRootDomain();

        $this->actingAs($this->user)->post("/user/hosting/domains/{$domain->hashid}/assign", [
            'project_id' => $project->id,
        ])->assertSessionHasNoErrors();

        $this->actingAs($this->user)
            ->post("/user/hosting/domains/{$domain->hashid}/detach")
            ->assertSessionHas('success');

        $this->assertNull($this->mapFile('tokoanda.com'));
        $this->assertDatabaseHas('hosting_domains', ['id' => $domain->id, 'project_id' => null]);
    }

    /* -----------------------------------------------------------------
     | Penghapusan
     |-------------------------------------------------------------- */

    public function test_deleting_root_domain_also_deletes_its_subdomains()
    {
        $parent = $this->makeRootDomain();
        $project = $this->makeProject('toko');

        $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
            'label' => 'blog',
            'parent_id' => $parent->id,
            'project_id' => $project->id,
        ])->assertSessionHasNoErrors();

        $this->assertSame('toko', $this->mapFile('blog.tokoanda.com'));

        $this->actingAs($this->user)
            ->delete("/user/hosting/domains/{$parent->hashid}")
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('hosting_domains', ['domain_name' => 'blog.tokoanda.com']);
        // Mapping subdomain ikut dibersihkan, tidak meninggalkan routing yatim.
        $this->assertNull($this->mapFile('blog.tokoanda.com'));
    }

    public function test_deleting_subdomain_keeps_parent_domain()
    {
        $parent = $this->makeRootDomain();
        $project = $this->makeProject('toko');

        $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
            'label' => 'blog',
            'parent_id' => $parent->id,
            'project_id' => $project->id,
        ])->assertSessionHasNoErrors();

        $child = HostingDomain::where('domain_name', 'blog.tokoanda.com')->firstOrFail();

        $this->actingAs($this->user)
            ->delete("/user/hosting/domains/{$child->hashid}")
            ->assertSessionHas('success');

        $this->assertDatabaseHas('hosting_domains', ['id' => $parent->id]);
        $this->assertNull($this->mapFile('blog.tokoanda.com'));
    }

    /* -----------------------------------------------------------------
     | Halaman
     |-------------------------------------------------------------- */

    public function test_domains_index_renders_with_subdomains()
    {
        $parent = $this->makeRootDomain();

        $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
            'label' => 'blog',
            'parent_id' => $parent->id,
        ])->assertSessionHasNoErrors();

        $this->actingAs($this->user)
            ->get('/user/hosting/domains')
            ->assertStatus(200)
            ->assertSee('blog.tokoanda.com')
            ->assertSee('tokoanda.com');
    }

    public function test_domain_show_page_lists_child_subdomains()
    {
        $parent = $this->makeRootDomain();

        $this->actingAs($this->user)->post('/user/hosting/domains/subdomain', [
            'label' => 'blog',
            'parent_id' => $parent->id,
        ])->assertSessionHasNoErrors();

        $this->actingAs($this->user)
            ->get("/user/hosting/domains/{$parent->hashid}")
            ->assertStatus(200)
            ->assertSee('blog.tokoanda.com');
    }

    public function test_guest_cannot_create_subdomain()
    {
        $parent = $this->makeRootDomain();

        $this->post('/user/hosting/domains/subdomain', [
            'label' => 'blog',
            'parent_id' => $parent->id,
        ])->assertRedirect();

        $this->assertDatabaseMissing('hosting_domains', ['domain_name' => 'blog.tokoanda.com']);
    }
}