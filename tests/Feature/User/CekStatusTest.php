<?php

namespace Tests\Feature\User;

use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\RiwayatPengajuan;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CekStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_my_tickets_page(): void
    {
        $this->get(route('cek-status'))
            ->assertRedirect(route('user.login', ['auth' => 'login']));
    }

    public function test_user_sees_all_owned_tickets_with_latest_status_sorted_by_submission_date(): void
    {
        [$user, $pemohon] = $this->createApplicant('owner@example.test');
        [, $otherPemohon] = $this->createApplicant('other@example.test');
        [$academicUnit, $academicService] = $this->createService('Akademik', 'Surat Aktif Kuliah');
        [$financeUnit, $financeService] = $this->createService('Keuangan', 'Pengembalian Dana');
        [$privateUnit, $privateService] = $this->createService('Kemahasiswaan', 'Data Privat');

        $olderTicket = $this->createTestApplication(
            $pemohon,
            $academicService,
            $academicUnit,
            'ULT-2026-000123',
            'baru',
            '2026-02-24 09:00:00'
        );
        $newerTicket = $this->createTestApplication(
            $pemohon,
            $financeService,
            $financeUnit,
            'ULT-2026-000124',
            'baru',
            '2026-02-25 09:00:00'
        );
        $this->createTestApplication(
            $otherPemohon,
            $privateService,
            $privateUnit,
            'ULT-2026-000125',
            'diproses',
            '2026-02-26 09:00:00'
        );
        RiwayatPengajuan::query()->create([
            'id_pengajuan' => $olderTicket->id,
            'status_sebelumnya' => null,
            'status_baru' => 'baru',
            'judul_status' => 'Pengajuan diterima',
            'deskripsi' => 'Pengajuan telah dikirim.',
            'nama_aktor' => 'Sistem',
            'dibuat_pada' => '2026-02-24 09:00:00',
        ]);
        RiwayatPengajuan::query()->create([
            'id_pengajuan' => $olderTicket->id,
            'status_sebelumnya' => 'didisposisikan',
            'status_baru' => 'diproses',
            'judul_status' => 'Pengajuan diproses',
            'deskripsi' => 'Pengajuan sedang ditangani oleh Unit Akademik.',
            'nama_aktor' => 'Petugas Unit',
            'dibuat_pada' => '2026-02-26 10:30:00',
        ]);
        RiwayatPengajuan::query()->create([
            'id_pengajuan' => $newerTicket->id,
            'status_sebelumnya' => null,
            'status_baru' => 'baru',
            'judul_status' => 'Pengajuan diterima',
            'deskripsi' => 'Pengajuan layanan berhasil dibuat.',
            'nama_aktor' => 'Sistem',
            'dibuat_pada' => '2026-02-25 09:00:00',
        ]);

        $this->actingAs($user)
            ->get(route('cek-status'))
            ->assertOk()
            ->assertSee('E-Tiket')
            ->assertSee('E-Tiket | Unit Layanan Terpadu Polinema')
            ->assertSee('Daftar E-Tiket dari pengajuan layanan yang telah Anda ajukan melalui Sistem Informasi ULT Polinema. Pantau perkembangan setiap pengajuan melalui detail E-Tiket.')
            ->assertDontSee('E-Tiket Saya')
            ->assertDontSee('2 Pengajuan')
            ->assertDontSee('ticket-search')
            ->assertDontSee('id="ticket"')
            ->assertSeeInOrder([
                'ULT-2026-000124',
                'Pengembalian Dana',
                'Keuangan',
                'ULT-2026-000123',
                'Surat Aktif Kuliah',
                'Akademik',
            ])
            ->assertSee('Diproses')
            ->assertSee('Pengajuan sedang ditangani oleh Unit Akademik.')
            ->assertSee(route('pengajuan.detail', ['nomorTiket' => $olderTicket->nomor_tiket]))
            ->assertSee(route('pengajuan.detail', ['nomorTiket' => $newerTicket->nomor_tiket]))
            ->assertDontSee('ULT-2026-000125')
            ->assertDontSee('Data Privat');
    }

    public function test_user_without_applications_sees_the_empty_state(): void
    {
        [$user] = $this->createApplicant('empty@example.test');

        $this->actingAs($user)
            ->get(route('cek-status'))
            ->assertOk()
            ->assertDontSee('0 Pengajuan')
            ->assertSee('Anda belum memiliki pengajuan layanan.');
    }

    public function test_search_only_opens_a_ticket_owned_by_the_authenticated_user(): void
    {
        [$user, $pemohon] = $this->createApplicant('owner@example.test');
        [, $otherPemohon] = $this->createApplicant('other@example.test');
        [$unit, $service] = $this->createService('Akademik', 'Layanan Pemohon');
        $ownedTicket = $this->createTestApplication(
            $pemohon,
            $service,
            $unit,
            'ULT-2026-000126',
            'diproses',
            '2026-02-24 09:00:00'
        );
        $otherTicket = $this->createTestApplication(
            $otherPemohon,
            $service,
            $unit,
            'ULT-2026-000127',
            'diproses',
            '2026-02-24 09:00:00'
        );

        $this->actingAs($user)
            ->get(route('cek-status', ['ticket' => $otherTicket->nomor_tiket]))
            ->assertOk()
            ->assertSee('Nomor E-Tiket tidak ditemukan atau tidak terdaftar pada akun Anda.')
            ->assertDontSee('Informasi Pengajuan')
            ->assertDontSee($otherTicket->nomor_tiket)
            ->assertSee($ownedTicket->nomor_tiket);
    }

    /**
     * @return array{User, Pengguna}
     */
    private function createApplicant(string $email): array
    {
        $user = User::factory()->create(['email' => $email]);
        $pemohon = Pengguna::query()->create([
            'nama_lengkap' => 'Pemohon '.strtok($email, '@'),
            'email' => $email,
            'kata_sandi' => Hash::make('password'),
        ]);

        return [$user, $pemohon];
    }

    /**
     * @return array{Unit, Layanan}
     */
    private function createService(string $unitName, string $serviceName): array
    {
        $unit = Unit::query()->create([
            'nama_unit' => $unitName,
            'kode_unit' => strtoupper(str_replace(' ', '-', $unitName)),
        ]);
        $service = Layanan::query()->create([
            'nama_layanan' => $serviceName,
            'id_unit' => $unit->id,
            'status' => 'aktif',
        ]);

        return [$unit, $service];
    }

    private function createTestApplication(
        Pengguna $pemohon,
        Layanan $service,
        Unit $unit,
        string $ticketNumber,
        string $status,
        string $createdAt
    ): Pengajuan {
        return Pengajuan::query()->create([
            'nomor_tiket' => $ticketNumber,
            'id_pengguna' => $pemohon->id,
            'id_layanan' => $service->id,
            'id_unit' => $unit->id,
            'subjek' => 'Permohonan layanan',
            'deskripsi' => 'Deskripsi pengajuan.',
            'status' => $status,
            'dibuat_pada' => $createdAt,
        ]);
    }
}
