<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_guests_cannot_open_the_profile(): void
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
    }

    public function test_profile_is_an_inertia_page_for_each_role(): void
    {
        $labels = [
            'superadmin' => 'Superadmin',
            'admin_hosting' => 'Admin hosting',
            'admin_joki' => 'Admin jasa development',
            'user_hosting' => 'Pelanggan hosting',
            'user_joki' => 'Pelanggan jasa development',
        ];

        foreach ($labels as $role => $label) {
            $user = User::factory()->create(['name' => 'Bima Ryan', 'role' => $role]);

            $this->actingAs($user)->get(route('profile.edit'))
                ->assertOk()
                ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
                ->assertInertia(fn (Assert $page) => $page
                    ->component('Profile/Edit')
                    ->where('profile.name', 'Bima Ryan')
                    ->where('profile.email', $user->email)
                    ->where('profile.role_label', $label)
                    ->where('profile.two_factor', 'inactive')
                    ->where('seoTitle', 'Profil Saya | Ryaze')
                    ->where('canonical', route('profile.edit'))
                    ->where('robots', 'noindex, nofollow')
                    ->missing('profile.password')
                );
        }
    }

    public function test_profile_reports_each_two_factor_state(): void
    {
        $pending = User::factory()->create(['two_factor_secret' => 'pending-secret']);
        $this->actingAs($pending)->get(route('profile.edit'))
            ->assertInertia(fn (Assert $page) => $page->where('profile.two_factor', 'pending'));

        $active = User::factory()->create([
            'two_factor_secret' => 'confirmed-secret',
            'two_factor_confirmed_at' => now(),
        ]);
        $this->actingAs($active)->get(route('profile.edit'))
            ->assertInertia(fn (Assert $page) => $page->where('profile.two_factor', 'active'));
    }

    public function test_personal_information_can_still_be_saved(): void
    {
        $user = User::factory()->create();
        $originalPassword = $user->password;

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Nama Baru',
            'email' => 'updated@example.com',
        ])->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame('updated@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
        $this->assertSame($originalPassword, $user->password);
    }

    public function test_password_can_still_be_changed_with_the_current_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('CurrentPassword123!')]);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'CurrentPassword123!',
            'password' => 'UpdatedPassword123!',
            'password_confirmation' => 'UpdatedPassword123!',
        ])->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));

        $this->assertTrue(Hash::check('UpdatedPassword123!', $user->fresh()->password));
    }

    public function test_wrong_current_password_is_rejected_without_changing_it(): void
    {
        $user = User::factory()->create(['password' => Hash::make('CurrentPassword123!')]);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'WrongPassword123!',
            'password' => 'UpdatedPassword123!',
            'password_confirmation' => 'UpdatedPassword123!',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('CurrentPassword123!', $user->fresh()->password));
    }
}
