<?php

namespace Tests\Feature;

use App\Models\RiwayatPengajuan;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PengajuanSubmissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('unit', function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit');
        });

        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_unit');
            $table->string('nama_layanan');
            $table->string('status')->default('aktif');
        });

        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('nama_lengkap');
            $table->string('nim_nip')->nullable();
            $table->string('nomor_telepon');
            $table->string('kata_sandi')->nullable();
            $table->timestamp('dibuat_pada')->nullable()->useCurrent();
            $table->timestamp('diperbarui_pada')->nullable()->useCurrent();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket')->unique();
            $table->unsignedBigInteger('id_pengguna');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('id_layanan');
            $table->unsignedBigInteger('id_admin_fo')->nullable();
            $table->string('subjek');
            $table->text('deskripsi');
            $table->string('status')->default('diajukan');
            $table->timestamp('dibuat_pada')->nullable()->useCurrent();
            $table->timestamp('diperbarui_pada')->nullable()->useCurrent();
            $table->timestamp('tanggal_selesai')->nullable();
        });

        Schema::create('riwayat_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pengajuan');
            $table->string('status_sebelumnya')->nullable();
            $table->string('status_baru');
            $table->string('judul_status');
            $table->text('deskripsi');
            $table->string('nama_aktor')->nullable();
            $table->unsignedBigInteger('id_admin')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
        });

        \DB::table('unit')->insert([
            'id' => 1,
            'nama_unit' => 'Bagian Akademik',
        ]);

        \DB::table('layanan')->insert([
            'id' => 1,
            'id_unit' => 1,
            'nama_layanan' => 'Surat Keterangan Kuliah',
            'status' => 'aktif',
        ]);
    }

    public function test_user_can_submit_pengajuan_and_is_redirected_to_success_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
        ]);

        $response = $this->actingAs($user)
            ->from(route('layanan.index'))
            ->post(route('pengajuan.submit'), [
                'nama_lengkap' => 'Nama Dari Request',
                'email' => 'pemalsu@example.com',
                'nomor_telepon' => '081234567890',
                'nim_nip' => '20240012',
                'id_layanan' => 1,
                'subjek' => 'Permohonan Surat Keterangan Kuliah',
                'deskripsi' => 'Saya membutuhkan surat keterangan kuliah untuk keperluan data beasiswa.',
                'pernyataan' => 'on',
            ]);

        $response->assertRedirect();

        $location = $response->headers->get('Location');
        $this->assertNotNull($location);
        $this->assertStringContainsString('/e-tiket/', $location);
        $this->assertStringContainsString('/berhasil', $location);

        $this->assertDatabaseCount('pengajuan', 1);
        $this->assertDatabaseCount('riwayat_pengajuan', 1);
        $this->assertDatabaseHas('pengajuan', [
            'user_id' => $user->id,
            'id_layanan' => 1,
            'subjek' => 'Permohonan Surat Keterangan Kuliah',
            'status' => 'diajukan',
        ]);
        $this->assertDatabaseHas('pengguna', [
            'nama_lengkap' => 'Budi Santoso',
            'email' => 'budi@example.com',
        ]);
        $this->assertDatabaseMissing('pengguna', [
            'nama_lengkap' => 'Nama Dari Request',
            'email' => 'pemalsu@example.com',
        ]);
    }

    public function test_existing_ticket_from_an_unlinked_legacy_user_is_not_reused_for_another_account(): void
    {
        $user = User::factory()->create(['email' => 'legacy@example.com']);
        DB::table('pengguna')->insert([
            'id' => 1,
            'email' => 'legacy@example.com',
            'nama_lengkap' => $user->name,
            'nomor_telepon' => '081234567890',
        ]);
        DB::table('pengajuan')->insert([
            'nomor_tiket' => 'ULT-2026-000001',
            'id_pengguna' => 1,
            'user_id' => null,
            'id_layanan' => 1,
            'subjek' => 'Permohonan Surat Keterangan Kuliah',
            'deskripsi' => 'Saya membutuhkan surat keterangan kuliah untuk keperluan data beasiswa.',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($user, 'web')->post(route('pengajuan.submit'), [
            'nama_lengkap' => $user->name,
            'email' => $user->email,
            'nomor_telepon' => '081234567890',
            'id_layanan' => 1,
            'subjek' => 'Permohonan Surat Keterangan Kuliah',
            'deskripsi' => 'Saya membutuhkan surat keterangan kuliah untuk keperluan data beasiswa.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('pengajuan', 2);
        $this->assertDatabaseHas('pengajuan', [
            'user_id' => $user->id,
            'id_layanan' => 1,
            'subjek' => 'Permohonan Surat Keterangan Kuliah',
        ]);
    }

    public function test_authenticated_user_can_open_the_selected_service_request_form(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
        ]);

        $response = $this->actingAs($user)
            ->get(route('pengajuan.form', ['layanan' => 1]));

        $response->assertOk()
            ->assertSee('Surat Keterangan Kuliah')
            ->assertSee('name="id_layanan" value="1"', false)
            ->assertSee('name="nama_lengkap" type="text" value="Budi Santoso" readonly', false)
            ->assertSee('name="email" type="email" value="budi@example.com" readonly', false);
    }

    public function test_guest_can_open_status_form_but_needs_login_to_view_a_ticket(): void
    {
        $this->withoutVite()
            ->get(route('cek-status'))
            ->assertOk()
            ->assertSee('Cek Status Pengajuan');

        $response = $this->get(route('cek-status', ['ticket' => 'ULT-2026-000001']));

        $response->assertRedirect(route('user.login'))
            ->assertSessionHas('url.intended', route('cek-status', ['ticket' => 'ULT-2026-000001']));
    }

    public function test_user_can_only_view_status_for_their_own_ticket(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        DB::table('pengguna')->insert([
            'id' => 1,
            'email' => 'status-owner@example.com',
            'nama_lengkap' => 'Pemilik Tiket',
            'nomor_telepon' => '081234567890',
        ]);
        DB::table('pengajuan')->insert([
            [
                'nomor_tiket' => 'ULT-2026-000001',
                'id_pengguna' => 1,
                'user_id' => $user->id,
                'id_layanan' => 1,
                'subjek' => 'Status milik sendiri',
                'deskripsi' => 'Deskripsi sendiri',
                'status' => 'diajukan',
            ],
            [
                'nomor_tiket' => 'ULT-2026-000002',
                'id_pengguna' => 1,
                'user_id' => $otherUser->id,
                'id_layanan' => 1,
                'subjek' => 'Status milik orang lain',
                'deskripsi' => 'Deskripsi orang lain',
                'status' => 'diajukan',
            ],
        ]);

        $this->actingAs($user, 'web')
            ->get(route('cek-status', ['ticket' => 'ULT-2026-000001']))
            ->assertOk()
            ->assertSeeText('Status milik sendiri');

        $this->get(route('cek-status', ['ticket' => 'ULT-2026-000002']))
            ->assertOk()
            ->assertSeeText('Nomor e-ticket tidak ditemukan.')
            ->assertDontSeeText('Status milik orang lain');
    }

    public function test_success_page_is_rendered_for_submitted_ticket(): void
    {
        $user = User::factory()->create();

        $submitResponse = $this->actingAs($user)->post(route('pengajuan.submit'), [
            'nama_lengkap' => 'Dewi Lestari',
            'email' => 'dewi@example.com',
            'nomor_telepon' => '081234567891',
            'nim_nip' => '20240013',
            'id_layanan' => 1,
            'subjek' => 'Permohonan Surat Keterangan Aktif Kuliah',
            'deskripsi' => 'Saya memerlukan surat keterangan aktif kuliah untuk administrasi.',
        ]);

        $location = $submitResponse->headers->get('Location');
        $this->assertNotNull($location);

        preg_match('#/e-tiket/([^/]+)/berhasil$#', $location, $matches);
        $this->assertNotEmpty($matches);

        $nomorTiket = $matches[1];

        $detailResponse = $this->actingAs($user)
            ->get(route('pengajuan.detail', ['nomorTiket' => $nomorTiket]));
        $detailResponse->assertOk()
            ->assertSee($nomorTiket);

        $viewResponse = $this
            ->get(route('pengajuan.berhasil', ['nomorTiket' => $nomorTiket]));
        $viewResponse->assertOk();
        $viewResponse->assertSee('PENGAJUAN BERHASIL DIKIRIM');
        $viewResponse->assertSee($nomorTiket);
    }

    public function test_authenticated_user_cannot_open_another_users_ticket_detail_or_success_page(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        DB::table('pengajuan')->insert([
            'nomor_tiket' => 'ULT-2026-000123',
            'id_pengguna' => 1,
            'user_id' => $owner->id,
            'id_layanan' => 1,
            'subjek' => 'Pengajuan privat',
            'deskripsi' => 'Informasi pengajuan privat',
            'status' => 'diajukan',
        ]);

        $this->get(route('pengajuan.detail', ['nomorTiket' => 'ULT-2026-000123']))
            ->assertRedirect(route('user.login'));

        $this->get(route('pengajuan.berhasil', ['nomorTiket' => 'ULT-2026-000123']))
            ->assertRedirect(route('user.login'));

        $this->actingAs($otherUser, 'web')
            ->get(route('pengajuan.detail', ['nomorTiket' => 'ULT-2026-000123']))
            ->assertNotFound();

        $this->get(route('pengajuan.berhasil', ['nomorTiket' => 'ULT-2026-000123']))
            ->assertNotFound();
    }

    public function test_ticket_numbers_are_generated_in_sequence_from_database_ids(): void
    {
        $user = User::factory()->create();

        $firstResponse = $this->actingAs($user)->post(route('pengajuan.submit'), [
            'nama_lengkap' => 'Rina Amelia',
            'email' => 'rina@example.com',
            'nomor_telepon' => '081234567892',
            'nim_nip' => '20240014',
            'id_layanan' => 1,
            'subjek' => 'Permohonan Surat Keterangan Kuliah 1',
            'deskripsi' => 'Saya memerlukan surat keterangan kuliah untuk kebutuhan administrasi 1.',
            'pernyataan' => 'on',
        ]);

        $secondResponse = $this->actingAs($user)->post(route('pengajuan.submit'), [
            'nama_lengkap' => 'Doni Pratama',
            'email' => 'doni@example.com',
            'nomor_telepon' => '081234567893',
            'nim_nip' => '20240015',
            'id_layanan' => 1,
            'subjek' => 'Permohonan Surat Keterangan Kuliah 2',
            'deskripsi' => 'Saya memerlukan surat keterangan kuliah untuk kebutuhan administrasi 2.',
            'pernyataan' => 'on',
        ]);

        $firstLocation = $firstResponse->headers->get('Location');
        $secondLocation = $secondResponse->headers->get('Location');

        $this->assertNotNull($firstLocation);
        $this->assertNotNull($secondLocation);

        preg_match('#/e-tiket/([^/]+)/berhasil$#', $firstLocation, $firstMatches);
        preg_match('#/e-tiket/([^/]+)/berhasil$#', $secondLocation, $secondMatches);

        $this->assertNotEmpty($firstMatches);
        $this->assertNotEmpty($secondMatches);

        $firstTicket = $firstMatches[1];
        $secondTicket = $secondMatches[1];

        $this->assertMatchesRegularExpression('/^ULT-'.date('Y').'-\d{6}$/', $firstTicket);
        $this->assertMatchesRegularExpression('/^ULT-'.date('Y').'-\d{6}$/', $secondTicket);
        $this->assertNotSame($firstTicket, $secondTicket);

        preg_match('/^ULT-'.date('Y').'-(\d{6})$/', $firstTicket, $firstNumber);
        preg_match('/^ULT-'.date('Y').'-(\d{6})$/', $secondTicket, $secondNumber);

        $this->assertNotEmpty($firstNumber);
        $this->assertNotEmpty($secondNumber);
        $this->assertNotSame($firstNumber[1], $secondNumber[1]);
    }

    public function test_riwayat_pengajuan_create_is_safe_when_status_sebelumnya_column_is_missing(): void
    {
        if (Schema::hasColumn('riwayat_pengajuan', 'status_sebelumnya')) {
            Schema::table('riwayat_pengajuan', function (Blueprint $table) {
                $table->dropColumn('status_sebelumnya');
            });
        }

        $this->assertFalse(Schema::hasColumn('riwayat_pengajuan', 'status_sebelumnya'));

        RiwayatPengajuan::create([
            'id_pengajuan' => 1,
            'status_sebelumnya' => null,
            'status_baru' => 'diajukan',
            'judul_status' => 'Pengajuan diterima',
            'deskripsi' => 'Pengajuan layanan berhasil dibuat.',
            'nama_aktor' => 'Sistem',
            'id_admin' => null,
            'dibuat_pada' => now(),
        ]);

        $this->assertDatabaseHas('riwayat_pengajuan', [
            'status_baru' => 'diajukan',
            'judul_status' => 'Pengajuan diterima',
        ]);
    }
}
