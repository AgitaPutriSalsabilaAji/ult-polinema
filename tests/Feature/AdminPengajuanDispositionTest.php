<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminPengajuanDispositionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('unit', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_unit');
        });

        Schema::create('layanan', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('id_unit');
            $table->string('nama_layanan');
        });

        Schema::create('pengajuan', function (Blueprint $table): void {
            $table->id();
            $table->string('nomor_tiket');
            $table->unsignedBigInteger('id_pengguna');
            $table->unsignedBigInteger('id_layanan');
            $table->unsignedBigInteger('id_unit')->nullable();
            $table->unsignedBigInteger('id_admin_fo')->nullable();
            $table->string('subjek');
            $table->text('deskripsi');
            $table->string('status');
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
        });

        Schema::create('admin', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_lengkap_gelar');
            $table->string('email');
            $table->string('kata_sandi');
            $table->string('nip');
            $table->string('peran');
            $table->unsignedBigInteger('id_unit');
        });

        Schema::create('disposisi_pengajuan', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('id_pengajuan');
            $table->unsignedBigInteger('id_unit_tujuan');
            $table->unsignedBigInteger('id_admin_pengirim');
            $table->unsignedBigInteger('id_admin_tujuan');
            $table->text('catatan_disposisi')->nullable();
            $table->string('status_disposisi');
            $table->timestamp('tanggal_disposisi')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });

        Schema::create('riwayat_pengajuan', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('id_pengajuan');
            $table->string('status_sebelumnya')->nullable();
            $table->string('status_baru');
            $table->string('judul_status');
            $table->text('deskripsi');
            $table->string('nama_aktor');
            $table->unsignedBigInteger('id_admin')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
        });
    }

    public function test_admin_ult_can_dispose_a_new_application_to_the_service_unit(): void
    {
        $adminUlt = $this->createAdmin('ult@example.com', 'admin_ult', 1);
        $adminUnit = $this->createAdmin('unit@example.com', 'admin_unit', 2);
        $this->createTestApplication(2, 'diajukan');

        $response = $this->actingAs($adminUlt, 'admin')
            ->postJson(route('admin.pengajuan.disposisi', 1), [
                'id_unit_tujuan' => 1,
                'id_admin_tujuan' => $adminUnit->id,
                'catatan_disposisi' => 'Mohon diproses.',
            ]);

        $response->assertOk()
            ->assertJson(['message' => 'Pengajuan berhasil didisposisikan.']);
        $this->assertDatabaseHas('pengajuan', [
            'id' => 1,
            'status' => 'didisposisikan',
            'id_unit' => 2,
        ]);
        $this->assertDatabaseHas('disposisi_pengajuan', [
            'id_pengajuan' => 1,
            'id_unit_tujuan' => 2,
            'id_admin_tujuan' => $adminUnit->id,
        ]);
        $this->assertDatabaseHas('riwayat_pengajuan', [
            'id_pengajuan' => 1,
            'status_baru' => 'didisposisikan',
        ]);
    }

    public function test_disposition_rejects_an_admin_from_another_unit_and_duplicate_disposition(): void
    {
        $adminUlt = $this->createAdmin('ult@example.com', 'admin_ult', 1);
        $wrongAdmin = $this->createAdmin('wrong@example.com', 'admin_unit', 3);
        $this->createTestApplication(2, 'diajukan');

        $this->actingAs($adminUlt, 'admin')
            ->postJson(route('admin.pengajuan.disposisi', 1), [
                'id_admin_tujuan' => $wrongAdmin->id,
            ])
            ->assertStatus(422);

        $rightAdmin = $this->createAdmin('right@example.com', 'admin_unit', 2);
        $this->actingAs($adminUlt, 'admin')
            ->postJson(route('admin.pengajuan.disposisi', 1), [
                'id_admin_tujuan' => $rightAdmin->id,
            ])
            ->assertOk();

        $this->actingAs($adminUlt, 'admin')
            ->postJson(route('admin.pengajuan.disposisi', 1), [
                'id_admin_tujuan' => $rightAdmin->id,
            ])
            ->assertStatus(409);
    }

    public function test_admin_unit_list_and_detail_are_limited_to_their_service_unit(): void
    {
        $adminUnit = $this->createAdmin('unit@example.com', 'admin_unit', 2);
        $this->createTestApplication(2, 'didisposisikan');
        $this->createTestApplication(3, 'didisposisikan', 2);

        $this->actingAs($adminUnit, 'admin')
            ->get(route('admin.pengajuan.index'))
            ->assertOk()
            ->assertSee('ULT-2026-000001')
            ->assertDontSee('ULT-2026-000002');

        $this->actingAs($adminUnit, 'admin')
            ->get(route('admin.pengajuan.show', 2))
            ->assertForbidden();
    }

    public function test_all_admins_in_the_destination_unit_can_process_and_complete_a_ticket(): void
    {
        $adminA1 = $this->createAdmin('a1@example.com', 'admin_unit', 2);
        $adminA2 = $this->createAdmin('a2@example.com', 'admin_unit', 2);
        $this->createTestApplication(2, 'didisposisikan');

        $this->actingAs($adminA1, 'admin')
            ->get(route('admin.pengajuan.show', 1))
            ->assertOk();

        $this->assertDatabaseHas('pengajuan', [
            'id' => 1,
            'status' => 'diproses',
        ]);
        $this->assertDatabaseHas('riwayat_pengajuan', [
            'id_pengajuan' => 1,
            'status_baru' => 'diproses',
            'id_admin' => $adminA1->id,
        ]);

        $this->actingAs($adminA2, 'admin')
            ->get(route('admin.pengajuan.show', 1))
            ->assertOk();

        $this->assertSame(
            1,
            DB::table('riwayat_pengajuan')
                ->where('id_pengajuan', 1)
                ->where('status_baru', 'diproses')
                ->count()
        );

        $this->actingAs($adminA2, 'admin')
            ->post(route('admin.pengajuan.selesai', 1))
            ->assertRedirect(route('admin.pengajuan.show', 1));

        $this->assertDatabaseHas('pengajuan', [
            'id' => 1,
            'status' => 'selesai',
        ]);
        $this->assertDatabaseHas('riwayat_pengajuan', [
            'id_pengajuan' => 1,
            'status_baru' => 'selesai',
            'id_admin' => $adminA2->id,
        ]);
    }

    public function test_admin_from_another_unit_cannot_open_or_reject_a_ticket(): void
    {
        $adminUnitB = $this->createAdmin('b@example.com', 'admin_unit', 3);
        $this->createTestApplication(2, 'didisposisikan');

        $this->actingAs($adminUnitB, 'admin')
            ->get(route('admin.pengajuan.show', 1))
            ->assertForbidden();

        $this->actingAs($adminUnitB, 'admin')
            ->post(route('admin.pengajuan.tolak', 1), ['alasan' => 'Tidak sesuai unit.'])
            ->assertForbidden();

        $this->assertDatabaseHas('pengajuan', [
            'id' => 1,
            'status' => 'didisposisikan',
        ]);
    }

    public function test_admin_unit_can_reject_without_a_reason_and_finalized_tickets_cannot_be_changed(): void
    {
        $adminUnit = $this->createAdmin('unit@example.com', 'admin_unit', 2);
        $this->createTestApplication(2, 'diproses');

        $this->actingAs($adminUnit, 'admin')
            ->post(route('admin.pengajuan.tolak', 1))
            ->assertRedirect(route('admin.pengajuan.show', 1));

        $this->assertDatabaseHas('pengajuan', [
            'id' => 1,
            'status' => 'ditolak',
        ]);
        $this->assertDatabaseHas('riwayat_pengajuan', [
            'id_pengajuan' => 1,
            'status_baru' => 'ditolak',
            'id_admin' => $adminUnit->id,
        ]);

        $this->actingAs($adminUnit, 'admin')
            ->post(route('admin.pengajuan.selesai', 1))
            ->assertStatus(409);

        $this->assertDatabaseHas('pengajuan', [
            'id' => 1,
            'status' => 'ditolak',
        ]);
    }

    private function createAdmin(string $email, string $role, int $unitId): Admin
    {
        return Admin::query()->create([
            'nama_lengkap_gelar' => $email,
            'email' => $email,
            'kata_sandi' => 'secret',
            'nip' => $email,
            'peran' => $role,
            'id_unit' => $unitId,
        ]);
    }

    private function createTestApplication(int $unitId, string $status, int $id = 1): void
    {
        DB::table('unit')->insertOrIgnore([
            'id' => $unitId,
            'nama_unit' => 'Unit '.$unitId,
        ]);
        DB::table('layanan')->insert([
            'id' => $id,
            'id_unit' => $unitId,
            'nama_layanan' => 'Layanan '.$id,
        ]);
        DB::table('pengajuan')->insert([
            'id' => $id,
            'nomor_tiket' => sprintf('ULT-2026-%06d', $id),
            'id_pengguna' => 1,
            'id_layanan' => $id,
            'id_unit' => $unitId,
            'subjek' => 'Subjek '.$id,
            'deskripsi' => 'Deskripsi '.$id,
            'status' => $status,
        ]);
    }
}
