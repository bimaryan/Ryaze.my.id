<?php

namespace Tests\Feature\Admin;

use App\Models\InfoUpdate;
use App\Models\User;
use Database\Seeders\UsersSeeders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InfoUpdateFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UsersSeeders::class);

        $this->admin = User::where('role', 'superadmin')->first()
            ?? tap(User::first())->update(['role' => 'superadmin']);

        $this->user = User::where('email', 'client.hosting@gmail.com')->firstOrFail();
        
        // Panggil HTTP yang tidak di-fake harus gagal keras, bukan diam-diam menembak
        // API sungguhan. Jangan pakai Http::fake() blanket: catch-all-nya berjalan
        // mendahului stub URL sehingga respons yang sudah disetel di test.
        Http::preventStrayRequests();
            }
        
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Ryaze Otomatis Deploy dari GitHub',
            'description' => 'Setiap push ke branch utama otomatis memicu build dan deploy ke hosting Anda.',
            'category' => InfoUpdate::CATEGORY_FEATURE,
            'priority' => InfoUpdate::PRIORITY_NORMAL,
            'version' => 'v2.1.0',
            'is_active' => 1,
        ], $overrides);
    }

    /* -----------------------------------------------------------------
     | Otorisasi
     |-------------------------------------------------------------- */

    public function test_guest_cannot_reach_admin_pages()
    {
        $this->get('/superadmin/info-updates')->assertRedirect();
        $this->get('/superadmin/info-updates/create')->assertRedirect();
    }

    public function test_non_admin_cannot_create_info_update()
    {
        $this->actingAs($this->user)
            ->post('/superadmin/info-updates', $this->payload())
            ->assertForbidden();

        $this->assertDatabaseCount('info_updates', 0);
    }

    public function test_admin_can_open_all_admin_pages()
    {
        $update = InfoUpdate::create($this->payload());

        $this->actingAs($this->admin)->get('/superadmin/info-updates')->assertStatus(200);
        $this->actingAs($this->admin)->get('/superadmin/info-updates/create')->assertStatus(200);
        $this->actingAs($this->admin)->get("/superadmin/info-updates/{$update->hashid}/edit")->assertStatus(200);
    }

    /* -----------------------------------------------------------------
     | CRUD
     |-------------------------------------------------------------- */

    public function test_admin_can_create_info_update()
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/info-updates', $this->payload())
            ->assertRedirect(route('superadmin.info_updates.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('info_updates', [
            'title' => 'Ryaze Otomatis Deploy dari GitHub',
            'category' => InfoUpdate::CATEGORY_FEATURE,
            'priority' => InfoUpdate::PRIORITY_NORMAL,
            'version' => 'v2.1.0',
            'author_id' => $this->admin->id,
        ]);
    }

    public function test_created_info_update_is_immediately_public()
    {
        $this->actingAs($this->admin)->post('/superadmin/info-updates', $this->payload());

        $this->get('/updates')->assertStatus(200)->assertSee('Ryaze Otomatis Deploy dari GitHub');
        $this->get('/updates/ryaze-otomatis-deploy-dari-github')->assertStatus(200);
    }

    public function test_duplicate_title_gets_unique_slug()
    {
        $this->actingAs($this->admin)->post('/superadmin/info-updates', $this->payload());
        $this->actingAs($this->admin)->post('/superadmin/info-updates', $this->payload());

        $slugs = InfoUpdate::pluck('slug')->all();
        $this->assertCount(2, array_unique($slugs));
        $this->assertContains('ryaze-otomatis-deploy-dari-github-2', $slugs);
    }

    public function test_validation_rejects_bad_category_and_priority()
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/info-updates', $this->payload([
                'category' => 'ngawur',
                'priority' => 'sangat-urgent',
            ]))
            ->assertSessionHasErrors(['category', 'priority']);

        $this->assertDatabaseCount('info_updates', 0);
    }

    public function test_description_is_required()
    {
        $this->actingAs($this->admin)
            ->post('/superadmin/info-updates', $this->payload(['description' => '']))
            ->assertSessionHasErrors('description');
    }

    public function test_admin_can_update_and_slug_tracks_title()
    {
        $update = InfoUpdate::create($this->payload());

        $this->actingAs($this->admin)
            ->put("/superadmin/info-updates/{$update->hashid}", $this->payload(['title' => 'Judul Baru Total']))
            ->assertRedirect(route('superadmin.info_updates.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('info_updates', [
            'id' => $update->id,
            'title' => 'Judul Baru Total',
            ]);
    }

    public function test_updating_without_title_change_keeps_slug_stable()
    {
        $update = InfoUpdate::create($this->payload());

        $this->actingAs($this->admin)->put("/superadmin/info-updates/{$update->hashid}", $this->payload());

        $this->assertSame('ryaze-otomatis-deploy-dari-github', $update->fresh()->slug);
    }

    public function test_admin_can_toggle_status()
    {
        $update = InfoUpdate::create($this->payload());

        $this->actingAs($this->admin)
            ->patch("/superadmin/info-updates/{$update->hashid}/status")
            ->assertSessionHas('success');

        $this->assertFalse($update->fresh()->is_active);
    }

    public function test_admin_can_delete()
    {
        $update = InfoUpdate::create($this->payload());

        $this->actingAs($this->admin)
            ->delete("/superadmin/info-updates/{$update->hashid}")
            ->assertSessionHas('success');

        $this->assertDatabaseCount('info_updates', 0);
    }

    /* -----------------------------------------------------------------
     | Penjadwalan tayang
     |-------------------------------------------------------------- */

    public function test_inactive_update_is_hidden_from_public()
    {
        InfoUpdate::create($this->payload(['is_active' => 0]));

        $this->get('/updates')->assertStatus(200)->assertDontSee('Ryaze Otomatis Deploy dari GitHub');
    }

    public function test_scheduled_update_is_hidden_until_its_time()
    {
        InfoUpdate::create($this->payload(['published_at' => now()->addDay()]));

        $this->get('/updates')->assertStatus(200)->assertDontSee('Ryaze Otomatis Deploy dari GitHub');
    }

    public function test_past_scheduled_update_is_visible()
    {
        InfoUpdate::create($this->payload(['published_at' => now()->subDay()]));

        $this->get('/updates')->assertStatus(200)->assertSee('Ryaze Otomatis Deploy dari GitHub');
    }

    /* -----------------------------------------------------------------
     | Halaman publik
     |-------------------------------------------------------------- */

    public function test_public_index_shows_all_categories()
    {
        InfoUpdate::create(['title' => 'Dukungan Email Enterprise', 'description' => 'a', 'category' => InfoUpdate::CATEGORY_FEATURE]);
        InfoUpdate::create(['title' => 'Jadwal Maintenance VPS', 'description' => 'b', 'category' => InfoUpdate::CATEGORY_MAINTENANCE]);
        InfoUpdate::create(['title' => 'Kebijakan Retensi Data Baru', 'description' => 'c', 'category' => InfoUpdate::CATEGORY_ANNOUNCEMENT]);

        $response = $this->get('/updates')->assertStatus(200);

        $response->assertSee('Dukungan Email Enterprise');
        $response->assertSee('Jadwal Maintenance VPS');
        $response->assertSee('Pengumuman');
    }

    public function test_public_index_can_filter_by_category()
    {
        InfoUpdate::create(['title' => 'Dukungan Email Enterprise', 'description' => 'a', 'category' => InfoUpdate::CATEGORY_FEATURE]);
        InfoUpdate::create(['title' => 'Jadwal Maintenance VPS', 'description' => 'b', 'category' => InfoUpdate::CATEGORY_MAINTENANCE]);

        $this->get('/updates?kategori=' . InfoUpdate::CATEGORY_MAINTENANCE)
            ->assertStatus(200)
            ->assertSee('Jadwal Maintenance VPS')
            ->assertDontSee('Dukungan Email Enterprise');
    }

    public function test_invalid_category_filter_is_ignored_not_error()
    {
        InfoUpdate::create(['title' => 'Dukungan Email Enterprise', 'description' => 'a', 'category' => InfoUpdate::CATEGORY_FEATURE]);

        $this->get('/updates?kategori=ngawur')->assertStatus(200)->assertSee('Dukungan Email Enterprise');
    }

    public function test_public_show_page_renders_and_lists_related()
    {
        InfoUpdate::create(['title' => 'Dukungan Email-Basic', 'description' => 'a', 'category' => InfoUpdate::CATEGORY_FEATURE]);
        $target = InfoUpdate::create(['title' => 'Dukungan Email-Pro', 'description' => 'b', 'category' => InfoUpdate::CATEGORY_FEATURE]);
        InfoUpdate::create(['title' => 'Perpanjangan Sertifikat SSL', 'description' => 'c', 'category' => InfoUpdate::CATEGORY_MAINTENANCE]);

        // Slug dibuat otomatis dari judul, jadi ambil dari model — bukan ditebak.
        $this->get('/updates/' . $target->slug)
            ->assertStatus(200)
            ->assertSee('Dukungan Email-Pro')
            // Related hanya kategori yang sama.
            ->assertSee('Dukungan Email-Basic')
            ->assertDontSee('Perpanjangan Sertifikat SSL');
    }

    public function test_unknown_slug_returns_404()
    {
        $this->get('/updates/tidak-ada-xyz')->assertNotFound();
    }

    public function test_public_show_rejects_inactive_update()
    {
        $hidden = InfoUpdate::create(['title' => 'Rahasia', 'description' => 'x', 'is_active' => false]);

        $this->get('/updates/' . $hidden->slug)->assertNotFound();
    }

    /* -----------------------------------------------------------------
     | Endpoint AI
     |-------------------------------------------------------------- */

    public function test_ai_endpoint_returns_description()
    {
        Http::fake([
            '*api.groq.com/*' => Http::response([
                'choices' => [['message' => ['content' => '{"title":"Judul AI","description":"Deskripsi hasil AI."}']]],
            ], 200),
        ]);

        $this->actingAs($this->admin)
            ->postJson('/superadmin/info-updates/generate-description', [
                'title' => '',
                'category' => InfoUpdate::CATEGORY_MAINTENANCE,
                'brief' => 'Maintenance 12 Oktober 23:00',
                'with_title' => true,
            ])
            ->assertStatus(200)
            ->assertJson([
                'title' => 'Judul AI',
                'description' => 'Deskripsi hasil AI.',
            ]);
    }

    public function test_ai_endpoint_fails_with_readable_message_when_groq_down()
    {
        Http::fake([
            '*api.groq.com/*' => Http::response(['error' => 'boom'], 500),
        ]);

        $this->actingAs($this->admin)
            ->postJson('/superadmin/info-updates/generate-description', ['title' => 'Deploy GitHub'])
            ->assertStatus(422)
            ->assertJsonStructure(['message']);
    }

    public function test_ai_endpoint_rejects_invalid_category()
    {
        $this->actingAs($this->admin)
            ->postJson('/superadmin/info-updates/generate-description', [
                'title' => 'x',
                'category' => 'ngawur',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('category');
    }

    public function test_non_admin_cannot_call_ai_endpoint()
    {
        $this->actingAs($this->user)
            ->postJson('/superadmin/info-updates/generate-description', ['title' => 'x'])
            ->assertForbidden();
    }
}