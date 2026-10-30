<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('layanan', function (Blueprint $table): void {
            $table->id();
            $table->string('status')->default('aktif');
        });
    }

    public function test_login_route_does_not_open_a_modal_without_an_apply_action(): void
    {
        $this->withoutVite()
            ->get(route('user.login'))
            ->assertRedirect(route('beranda'))
            ->assertSessionMissing('auth_modal');
    }

    public function test_registration_route_does_not_open_a_modal_without_an_apply_action(): void
    {
        $this->withoutVite()
            ->get(route('user.register'))
            ->assertRedirect(route('beranda'))
            ->assertSessionMissing('auth_modal');
    }

    public function test_guest_navbar_has_no_login_or_registration_controls(): void
    {
        $this->withoutVite();
        $navbar = view('user.navbar', ['theme' => 'purple'])->render();

        $this->assertStringNotContainsString('ult-navbar__login', $navbar);
        $this->assertStringNotContainsString('ult-navbar__register', $navbar);
        $this->assertStringNotContainsString('data-bs-target="#loginModal"', $navbar);
        $this->assertStringNotContainsString('data-bs-target="#registerModal"', $navbar);
        $this->assertStringNotContainsString('href="'.route('user.login').'"', $navbar);
        $this->assertStringNotContainsString('href="'.route('user.register').'"', $navbar);
        $this->assertStringNotContainsString('ult-profile__avatar', $navbar);
        $this->assertStringContainsString('id="loginModal"', $navbar);
        $this->assertStringContainsString('id="registerModal"', $navbar);
        $this->assertStringContainsString('data-auth-modal-switch="register">Daftar sekarang</a>', $navbar);
    }

    public function test_guest_is_redirected_to_login_with_the_selected_service_preserved(): void
    {
        $response = $this->get(route('pengajuan.form', ['layanan' => 42]));

        $response->assertRedirect(route('user.login'))
            ->assertSessionHas('url.intended', route('pengajuan.form', ['layanan' => 42]));

        $this->get(route('user.login'))
            ->assertRedirect(route('beranda'))
            ->assertSessionMissing('auth_modal')
            ->assertSessionHas('url.intended', route('pengajuan.form', ['layanan' => 42]));
    }

    public function test_user_can_login_and_return_to_the_selected_service_form(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('correct-password'),
        ]);
        $intendedUrl = route('pengajuan.form', ['layanan' => 42]);

        $response = $this->withSession(['url.intended' => $intendedUrl])
            ->post(route('user.login.submit'), [
                'email' => 'USER@example.com',
                'password' => 'correct-password',
                'remember' => '1',
            ]);

        $response->assertRedirect($intendedUrl);
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_invalid_login_displays_an_error_without_authenticating(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->from(route('beranda'))
            ->post(route('user.login.submit'), [
                'email' => 'user@example.com',
                'password' => 'incorrect-password',
            ]);

        $response->assertRedirect(route('beranda'))
            ->assertSessionHasErrors('email')
            ->assertSessionHas('auth_modal', 'login')
            ->assertSessionHas('_old_input.email', 'user@example.com');
        $this->assertGuest('web');

        $modal = view('auth.user-modals')->render();
        $this->assertStringContainsString('id="loginModal"', $modal);
        $this->assertStringContainsString('data-auth-modal-autostart', $modal);
        $this->assertStringContainsString('user@example.com', $modal);
    }

    public function test_login_modal_returns_to_the_selected_service_after_authentication(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('correct-password'),
        ]);
        \DB::table('layanan')->insert(['id' => 42, 'status' => 'aktif']);

        $response = $this->from(route('layanan.detail', ['slug' => '42']))
            ->post(route('user.login.submit'), [
                'email' => 'user@example.com',
                'password' => 'correct-password',
                'layanan_id' => 42,
            ]);

        $response->assertRedirect(route('pengajuan.form', ['layanan' => 42]));
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_registration_creates_a_hashed_user_and_returns_to_the_selected_service(): void
    {
        $intendedUrl = route('pengajuan.form', ['layanan' => 17]);

        $response = $this->withSession(['url.intended' => $intendedUrl])
            ->post(route('user.register.submit'), [
                'name' => 'Pengguna Baru',
                'email' => 'BARU@example.com',
                'password' => 'rahasia-123',
            ]);

        $response->assertRedirect($intendedUrl);
        $this->assertDatabaseHas('users', [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.com',
        ]);
        $this->assertAuthenticated('web');
        $this->assertTrue(Hash::check(
            'rahasia-123',
            User::query()->where('email', 'baru@example.com')->value('password')
        ));
    }

    public function test_registration_modal_returns_to_the_service_selected_before_authentication(): void
    {
        \DB::table('layanan')->insert(['id' => 17, 'status' => 'aktif']);

        $response = $this->post(route('user.register.submit'), [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.com',
            'password' => 'rahasia-123',
            'layanan_id' => 17,
        ]);

        $response->assertRedirect(route('pengajuan.form', ['layanan' => 17]));
        $this->assertAuthenticated('web');
    }

    public function test_registration_rejects_an_existing_email(): void
    {
        User::factory()->create(['email' => 'registered@example.com']);

        $response = $this->from(route('beranda'))
            ->post(route('user.register.submit'), [
                'name' => 'Pengguna Baru',
                'email' => 'registered@example.com',
                'password' => 'rahasia-123',
            ]);

        $response->assertRedirect(route('beranda'))
            ->assertSessionHasErrors('email')
            ->assertSessionHas('auth_modal', 'register');
        $this->assertDatabaseCount('users', 1);
        $this->assertGuest('web');
    }

    public function test_registration_requires_a_valid_email_and_eight_character_password(): void
    {
        $response = $this->from(route('beranda'))
            ->post(route('user.register.submit'), [
                'name' => 'Pengguna Baru',
                'email' => 'bukan-email',
                'password' => 'pendek',
            ]);

        $response->assertRedirect(route('beranda'))
            ->assertSessionHasErrors(['email', 'password'])
            ->assertSessionHas('auth_modal', 'register')
            ->assertSessionHas('_old_input.name', 'Pengguna Baru')
            ->assertSessionHas('_old_input.email', 'bukan-email')
            ->assertSessionMissing('_old_input.password');
        $this->assertDatabaseCount('users', 0);

        $modal = view('auth.user-modals')->render();
        $this->assertStringContainsString('id="registerModal"', $modal);
        $this->assertStringContainsString('data-auth-modal-autostart', $modal);
        $this->assertStringContainsString('value="Pengguna Baru"', $modal);
        $this->assertStringContainsString('value="bukan-email"', $modal);
    }

    public function test_authenticated_user_sees_profile_details_instead_of_login_links(): void
    {
        $this->withoutVite();
        $user = User::factory()->create([
            'name' => 'Pemilik Akun',
            'email' => 'pemilik@example.com',
        ]);

        $this->actingAs($user, 'web');
        $navbar = view('user.navbar', ['theme' => 'purple'])->render();

        $this->assertStringContainsString('Pemilik Akun', $navbar);
        $this->assertStringContainsString('pemilik@example.com', $navbar);
        $this->assertStringContainsString('ult-profile__avatar', $navbar);
        $this->assertStringContainsString('bi bi-box-arrow-right', $navbar);
        $this->assertMatchesRegularExpression(
            '/<span class="ult-profile__avatar"[^>]*>\s*P\s*<\/span>/',
            $navbar
        );
        $this->assertStringContainsString('action="'.route('user.logout').'" method="POST"', $navbar);
        $this->assertStringContainsString('name="_token"', $navbar);
        $this->assertStringContainsString('data-bs-target="#logoutModal"', $navbar);
        $this->assertStringContainsString('Apakah Anda yakin ingin keluar dari akun Anda?', $navbar);
        $this->assertStringContainsString('>Batal</button>', $navbar);
        $this->assertStringContainsString('>Keluar</button>', $navbar);
        $this->assertStringNotContainsString('data-bs-target="#loginModal"', $navbar);
        $this->assertStringNotContainsString('data-bs-target="#registerModal"', $navbar);
    }

    public function test_user_can_logout_with_post_and_session_is_cleared(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')
            ->withSession(['profile-test' => 'value'])
            ->post(route('user.logout'));

        $response->assertRedirect(route('beranda'))
            ->assertSessionMissing('profile-test');
        $this->assertGuest('web');
    }
}
