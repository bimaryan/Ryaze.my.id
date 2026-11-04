<?php

namespace Tests\Feature\Hosting;

use App\Models\HostingBilling;
use App\Models\Setting;
use App\Models\StudentVerification;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\UsersSeeders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StudentPromoFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $otherUser;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersSeeders::class);

        $this->user = User::where('email', 'client.hosting@gmail.com')->first();
        $this->otherUser = User::where('email', 'dea@gmail.com')->first();
        $this->otherUser->update(['role' => 'user_hosting']);
        $this->admin = User::where('email', 'admin.hosting@ryaze.my.id')->first();

        Setting::setVal('student_promo_active', '1');
        Setting::setVal('student_promo_plan', 'pro');
        Setting::setVal('student_promo_days', '30');
    }

    private function fakePddikti(array $overrides = []): void
    {
        Http::fake([
            '*/api/pencarian/mhs/*' => Http::response(array_merge([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'abc123',
                        'nama' => 'Rian Adi Pratama',
                        'nim' => '10121067',
                        'nama_pt' => 'Universitas Contoh',
                        'nama_prodi' => 'Teknik Informatika',
                    ],
                ],
            ], $overrides['search'] ?? []), 200),
            '*/api/detail/mhs/*' => Http::response(array_merge([
                'status' => 'success',
                'data' => [
                    'id' => 'token-abc123',
                    'nama' => 'Rian Adi Pratama',
                    'nim' => '10121067',
                    'nama_pt' => 'Universitas Contoh',
                    'prodi' => 'Teknik Informatika',
                    'jenjang' => 'Sarjana',
                    'status_saat_ini' => 'Aktif-2026/2027 Ganjil',
                ],
            ], $overrides['detail'] ?? []), 200),
        ]);
    }

    public function test_success_verification_grants_pro_plan_for_a_month()
    {
        $this->fakePddikti();

        $response = $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', [
                'nim' => '10-121067',
                'nama' => 'Rian Adi',
            ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $this->assertDatabaseHas('student_verifications', [
            'user_id' => $this->user->id,
            'nim' => '10121067',
            'nama_pddikti' => 'Rian Adi Pratama',
            'nama_pt' => 'Universitas Contoh',
            'pddikti_status' => 'AKTIF',
        ]);

        $billing = HostingBilling::where('user_id', $this->user->id)->where('plan', 'pro')->latest()->first();
        $this->assertNotNull($billing);
        $this->assertEquals('active', $billing->status);
        $this->assertEquals(0, $billing->amount);
        $this->assertTrue(Carbon::parse($billing->next_due_date)->between(now()->addDays(28), now()->addDays(31)));
    }

    public function test_verification_creates_zero_invoice_and_updates_storage_limit()
    {
        $this->fakePddikti();

        $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', [
                'nim' => '10121067',
                'nama' => 'Rian Adi Pratama',
            ]);

        $this->assertDatabaseHas('hosting_payments', [
            'user_id' => $this->user->id,
            'amount' => 0,
            'status' => 'paid',
            'payment_method' => 'Promo Mahasiswa',
        ]);

        $this->user->refresh();
        $this->assertEquals(3072, $this->user->hosting_storage_limit_mb);
    }

    public function test_nim_not_found_rejects_claim()
    {
        $this->fakePddikti([
            'search' => ['data' => []],
        ]);

        $response = $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', [
                'nim' => '99999999',
                'nama' => 'Rian Adi',
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('student_verifications', ['nim' => '99999999']);
        $this->assertDatabaseMissing('hosting_billings', ['user_id' => $this->user->id]);
    }

    public function test_status_lulus_rejects_claim()
    {
        $this->fakePddikti([
            'detail' => ['data' => ['status_kuliah' => 'LULUS', 'riwayat_pendidikan' => [['tanggal_keluar' => '2025-08-01', 'nama_jenjang' => 'S1']]]],
        ]);

        $response = $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', [
                'nim' => '10121067',
                'nama' => 'Rian Adi Pratama',
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('student_verifications', ['nim' => '10121067']);
    }

    public function test_status_saat_ini_nonaktif_rejects_claim()
    {
        $this->fakePddikti([
            'detail' => ['data' => [
                'nama' => 'Rian Adi Pratama',
                'nim' => '10121067',
                'nama_pt' => 'Universitas Contoh',
                'prodi' => 'Teknik Informatika',
                'jenjang' => 'Sarjana',
                'status_saat_ini' => 'Lulus-2024/2025 Genap',
            ]],
        ]);

        $response = $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', [
                'nim' => '10121067',
                'nama' => 'Rian Adi Pratama',
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('student_verifications', ['nim' => '10121067']);
        $this->assertDatabaseMissing('hosting_billings', ['user_id' => $this->user->id]);
    }

    public function test_missing_detail_fails_closed_without_granting_plan()
    {
        Http::fake([
            '*/api/pencarian/mhs/*' => Http::response([
                'status' => 'success',
                'data' => [['id' => 'abc123', 'nama' => 'Rian Adi Pratama', 'nim' => '10121067']],
            ], 200),
            '*/api/detail/mhs/*' => Http::response(['status' => 'error', 'message' => 'Not found'], 404),
        ]);

        $response = $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', [
                'nim' => '10121067',
                'nama' => 'Rian Adi Pratama',
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('student_verifications', ['nim' => '10121067']);
        $this->assertDatabaseMissing('hosting_billings', ['user_id' => $this->user->id]);
    }

    /**
     * Regresi live-API: token id dari situs resmi adalah base64url yang berakhir
     * dengan padding "==". Bila di-encode jadi %3D%3D, situs resmi membalas
     * 400 "invalid id" dan setiap klaim promo gagal tanpa sebab yang jelas.
     */
    public function test_detail_request_keeps_base64_padding_literal()
    {
        $token = 'kZ9jQlm2Vd_xRt7PwAqB-sXnYbEcFdGhIjKlMnOpQrStUvWxYz0123456789AB==';

        Http::fake([
            '*/api/pencarian/mhs/*' => Http::response([
                'status' => 'success',
                'data' => [['id' => $token, 'nama' => 'Rian Adi Pratama', 'nim' => '10121067']],
            ], 200),
            '*/api/detail/mhs/*' => Http::response([
                'status' => 'success',
                'data' => [
                    'id' => $token,
                    'nama' => 'Rian Adi Pratama',
                    'nim' => '10121067',
                    'nama_pt' => 'Universitas Contoh',
                    'prodi' => 'Teknik Informatika',
                    'jenjang' => 'Sarjana',
                    'status_saat_ini' => 'Aktif-2026/2027 Ganjil',
                ],
            ], 200),
        ]);

        $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', [
                'nim' => '10121067',
                'nama' => 'Rian Adi Pratama',
            ])->assertSessionHas('success');

        Http::assertSent(fn ($request) => str_contains($request->url(), '/api/detail/mhs/'.$token));
    }

    /**
     * NIM cuma unik di dalam satu kampus. Regresi: dulu kode mengambil kandidat
     * pertama dengan NIM sama, jadi mahasiswa yang benar ikut ditolak karena
     * orang lain dengan NIM identik.
     */
    public function test_duplicate_nim_verifies_the_matching_person()
    {
        $this->fakeStudentsWithSharedNim();

        $response = $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', [
                'nim' => '2205036',
                'nama' => 'Bima Ryan Alfarizi',
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('student_verifications', [
            'nim' => '2205036',
            'nama_pddikti' => 'BIMA RYAN ALFARIZI',
            'nama_pt' => 'Politeknik Negeri Indramayu',
            'pddikti_status' => 'AKTIF',
        ]);
        $this->assertDatabaseHas('hosting_billings', ['user_id' => $this->user->id, 'plan' => 'pro']);
    }

    /** Orang yang sama bisa tercatat di kampus lama (pindah/alih bentuk) dengan status nonaktif. */
    public function test_same_student_at_two_campuses_uses_the_active_record()
    {
        $this->fakeStudentsWithSharedNim();

        $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', [
                'nim' => '2205036',
                'nama' => 'Bima Ryan',
            ])->assertSessionHas('success');

        $this->assertDatabaseHas('student_verifications', [
            'nim' => '2205036',
            'nama_pt' => 'Politeknik Negeri Indramayu',
        ]);
    }

    /** "Non-Aktif-2026/2027 Ganjil" dulu terpotong jadi "NON" dan lolos sebagai AKTIF. */
    public function test_nonaktif_status_rejects_claim()
    {
        $this->fakeStudentsWithSharedNim();

        $response = $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', [
                'nim' => '2205036',
                'nama' => 'Nicolas Nainggolan',
            ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('NON-AKTIF', strtoupper((string) session('error')));
        $this->assertDatabaseMissing('student_verifications', ['nim' => '2205036']);
    }

    /** Pesan error tidak boleh membocorkan nama mahasiswa lain yang kebetulan bernim sama. */
    public function test_mismatch_message_does_not_leak_other_student_names()
    {
        $this->fakeStudentsWithSharedNim();

        $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', [
                'nim' => '2205036',
                'nama' => 'Jesselina Amikel',
            ])->assertSessionHas('error');

        $error = (string) session('error');
        $this->assertStringNotContainsString('NICOLAS', $error);
        $this->assertStringNotContainsString('WAHYU', $error);
        $this->assertStringNotContainsString('BIMA', $error);
    }

    /**
     * Panggangan HTTP yang meniru bentuk response situs resmi: satu pencarian
     * menghasilkan banyak NIM sama, detail dipilih lewat token id.
     */
    private function fakeStudentsWithSharedNim(): void
    {
        $rows = [
            ['id' => 'id-nicol', 'nama' => 'NICOLAS NAINGGOLAN', 'nim' => '2205036', 'nama_pt' => 'UNIVERSITAS SYEDZA SAINTIKA'],
            ['id' => 'id-wahyu', 'nama' => 'WAHYU ESTI ANGGITA', 'nim' => '2205036', 'nama_pt' => 'UNIVERSITAS AN NUUR'],
            ['id' => 'id-bimalama', 'nama' => 'BIMA RYAN ALFARIZI', 'nim' => '2205036', 'nama_pt' => 'SMK TEKNINDO'],
            ['id' => 'id-bima', 'nama' => 'BIMA RYAN ALFARIZI', 'nim' => '2205036', 'nama_pt' => 'POLITEKNIK NEGERI INDRAMAYU'],
        ];

        $details = [
            'id-nicol' => ['nama' => 'NICOLAS NAINGGOLAN', 'nama_pt' => 'Universitas Syedza Saintika', 'prodi' => 'Laboratorium Medik', 'jenjang' => 'Diploma empat', 'status_saat_ini' => 'Non-Aktif-2026/2027 Ganjil'],
            'id-wahyu' => ['nama' => 'WAHYU ESTI ANGGITA', 'nama_pt' => 'Universitas An Nuur', 'prodi' => 'Manajemen', 'jenjang' => 'Sarjana', 'status_saat_ini' => 'Lulus-2025/2026 Genap'],
            'id-bimalama' => ['nama' => 'BIMA RYAN ALFARIZI', 'nama_pt' => 'Politeknik Negeri Indramayu', 'prodi' => 'Teknik Informatika', 'jenjang' => 'Diploma tiga', 'status_saat_ini' => 'Pindah-2024/2025 Ganjil'],
            'id-bima' => ['nama' => 'BIMA RYAN ALFARIZI', 'nama_pt' => 'Politeknik Negeri Indramayu', 'prodi' => 'Teknik Informatika', 'jenjang' => 'Diploma tiga', 'status_saat_ini' => 'Aktif-2026/2027 Ganjil'],
        ];

        Http::fake(function ($request) use ($rows, $details) {
            $url = $request->url();

            if (str_contains($url, '/api/pencarian/mhs/')) {
                return Http::response(['status' => 'success', 'data' => $rows], 200);
            }

            foreach ($details as $id => $payload) {
                if (str_ends_with($url, '/api/detail/mhs/'.$id)) {
                    return Http::response(['status' => 'success', 'data' => $payload], 200);
                }
            }

            return Http::response(['status' => 'error', 'message' => 'invalid id'], 400);
        });
    }

    public function test_name_mismatch_rejects_claim()
    {
        $this->fakePddikti();

        $response = $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', [
                'nim' => '10121067',
                'nama' => 'Budi Santoso',
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('student_verifications', ['nim' => '10121067']);
    }

    public function test_same_nim_cannot_be_claimed_twice_by_different_accounts()
    {
        $this->fakePddikti();

        $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', ['nim' => '10121067', 'nama' => 'Rian Adi Pratama']);

        $response = $this->actingAs($this->otherUser)
            ->post('/user/hosting/student-promo/verify', ['nim' => '10121067', 'nama' => 'Rian Adi Pratama']);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('student_verifications', ['user_id' => $this->otherUser->id]);
    }

    public function test_user_cannot_claim_twice()
    {
        $this->fakePddikti();

        $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', ['nim' => '10121067', 'nama' => 'Rian Adi Pratama']);

        $response = $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', ['nim' => '22222222', 'nama' => 'Rian Adi Pratama']);

        $response->assertSessionHas('error');
        $this->assertEquals(1, StudentVerification::count());
    }

    public function test_claim_rejected_when_user_has_active_paid_subscription()
    {
        $this->fakePddikti();

        HostingBilling::create([
            'user_id' => $this->user->id,
            'plan_name' => 'Paket Starter',
            'plan' => 'starter',
            'amount' => 15000,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'next_due_date' => now()->addMonth(),
        ]);

        $response = $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', ['nim' => '10121067', 'nama' => 'Rian Adi Pratama']);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('student_verifications', ['nim' => '10121067']);
    }

    public function test_claim_rejected_when_promo_inactive()
    {
        Setting::setVal('student_promo_active', '0');
        $this->fakePddikti();

        $response = $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', ['nim' => '10121067', 'nama' => 'Rian Adi Pratama']);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('student_verifications', ['nim' => '10121067']);
    }

    public function test_api_error_fails_closed()
    {
        Http::fake([
            '*/api/pencarian/mhs/*' => Http::response(['error' => 'Connection error'], 503),
        ]);

        $response = $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', ['nim' => '10121067', 'nama' => 'Rian Adi']);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('student_verifications', ['nim' => '10121067']);
        $this->assertDatabaseMissing('hosting_billings', ['user_id' => $this->user->id]);
    }

    public function test_promo_page_shows_form_when_verified()
    {
        $this->fakePddikti();

        $page = $this->actingAs($this->user)->get('/user/hosting/student-promo');
        $page->assertStatus(200);
        $page->assertSee('Nomor Induk Mahasiswa');

        $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', ['nim' => '10121067', 'nama' => 'Rian Adi Pratama']);

        $after = $this->actingAs($this->user)->get('/user/hosting/student-promo');
        $after->assertStatus(200);
        $after->assertSee('Rian Adi Pratama');
    }

    public function test_admin_can_view_and_update_verification_settings()
    {
        $response = $this->actingAs($this->admin)->get('/admin/hosting/student-verifications');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)
            ->put('/admin/hosting/student-verifications/settings', [
                'student_promo_active' => '0',
                'student_promo_plan' => 'starter',
                'student_promo_days' => '14',
                'pddikti_api_base' => 'https://pddikti.fastapicloud.dev/api',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('settings', ['key' => 'student_promo_days', 'value' => '14']);
        $this->assertDatabaseHas('settings', ['key' => 'student_promo_plan', 'value' => 'starter']);
    }

    public function test_rate_limits_frequent_attempts()
    {
        $this->fakePddikti();

        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($this->user)
                ->post('/user/hosting/student-promo/verify', [
                    'nim' => '10000'.$i,
                    'nama' => 'Rian Adi',
                ]);
        }

        $response = $this->actingAs($this->user)
            ->post('/user/hosting/student-promo/verify', ['nim' => '100999', 'nama' => 'Rian Adi']);

        $response->assertSessionHas('error');
    }

    public function test_home_carousel_lists_promo_only_while_active()
    {
        $this->get('/')->assertOk()->assertSee('student-promo');

        Setting::setVal('student_promo_active', '0');

        $this->get('/')->assertOk()->assertDontSee('student-promo');
    }

    public function test_promo_menu_shows_for_students_and_hides_when_promo_off()
    {
        $this->actingAs($this->user)
            ->get('/user/hosting/dashboard')
            ->assertOk()
            ->assertSee('Promo Mahasiswa');

        Setting::setVal('student_promo_active', '0');

        $this->actingAs($this->user)
            ->get('/user/hosting/dashboard')
            ->assertOk()
            ->assertDontSee('Promo Mahasiswa');
    }

    public function test_promo_menu_hidden_for_hosting_admin()
    {
        $this->actingAs($this->admin)
            ->get('/admin/hosting/dashboard')
            ->assertOk()
            ->assertDontSee('Promo Mahasiswa');
    }
}
