<?php

namespace Tests\Feature\User;

use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\RiwayatPengajuan;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DetailPengajuanTest extends TestCase
{
    use RefreshDatabase;

    public function test_detail_shows_dynamic_application_documents_and_ordered_history(): void
    {
        Storage::fake('local');
        [$user, $pemohon, $layanan] = $this->createApplicantAndService();
        $pengajuan = $this->createTestApplication($pemohon, $layanan, 'diproses');

        RiwayatPengajuan::query()->create([
            'id_pengajuan' => $pengajuan->id,
            'status_sebelumnya' => 'didisposisikan',
            'status_baru' => 'diproses',
            'judul_status' => 'Pengajuan Diproses',
            'deskripsi' => 'Pengajuan sedang ditangani oleh Unit Akademik.',
            'nama_aktor' => 'Admin Unit',
            'dibuat_pada' => '2026-11-10 13:20:00',
        ]);
        RiwayatPengajuan::query()->create([
            'id_pengajuan' => $pengajuan->id,
            'status_sebelumnya' => null,
            'status_baru' => 'baru',
            'judul_status' => 'Pengajuan diterima',
            'deskripsi' => 'Pengajuan berhasil dikirim melalui Sistem ULT.',
            'nama_aktor' => 'Sistem',
            'dibuat_pada' => '2026-11-10 09:15:00',
        ]);

        Storage::disk('local')->put('pengajuan/support/persyaratan.pdf', 'sample document');
        DB::table('dokumen_pengajuan')->insert([
            'id_pengajuan' => $pengajuan->id,
            'nama_file' => 'persyaratan.pdf',
            'path_file' => 'pengajuan/support/persyaratan.pdf',
            'ukuran_file' => '15 KB',
            'diunggah_pada' => '2026-11-10 09:15:00',
        ]);

        $this->actingAs($user)
            ->get(route('pengajuan.detail', ['nomorTiket' => $pengajuan->nomor_tiket]))
            ->assertOk()
            ->assertSeeInOrder([
                $pengajuan->nomor_tiket,
                'Diproses',
                '10 November 2026',
                'Unit Akademik',
                'EDUFAIR',
                'Subjek / Keperluan',
                'persyaratan.pdf',
                'Tahap 1',
                'Selesai dilalui',
                'Pengajuan Masuk',
                'Pengajuan berhasil dikirim melalui Sistem ULT.',
                'Tahap 2',
                'Proses terbaru',
                'Pengajuan Diproses',
            ])
            ->assertSee('Deskripsi lengkap pengajuan.')
            ->assertSee('DOKUMEN PENDUKUNG (Opsional)')
            ->assertSee('Lihat Dokumen')
            ->assertSee('Unduh')
            ->assertSee('RIWAYAT PROSES')
            ->assertSee('KOMUNIKASI DENGAN ADMIN')
            ->assertSee('id="komunikasi-form"', false)
            ->assertSee('name="pesan"', false)
            ->assertSee('Kirim Pesan')
            ->assertSee('Belum ada percakapan pada E-Tiket ini.')
            ->assertDontSee('Butuh bantuan terkait pengajuan?')
            ->assertDontSee('Hubungi admin untuk bertanya mengenai pengajuan ini.')
            ->assertDontSee('Chat Admin')
            ->assertSee('Pantau Status Terbaru')
            ->assertSee('timeline-item--baru is-complete', false)
            ->assertSee('timeline-item--diproses is-latest', false)
            ->assertSee('bi-inbox-fill', false)
            ->assertSee('bi-gear-fill', false);
    }

    public function test_detail_shows_empty_documents_message_when_application_has_no_attachments(): void
    {
        [$user, $pemohon, $layanan] = $this->createApplicantAndService();
        $pengajuan = $this->createTestApplication($pemohon, $layanan);

        $this->actingAs($user)
            ->get(route('pengajuan.detail', ['nomorTiket' => $pengajuan->nomor_tiket]))
            ->assertOk()
            ->assertSee('Tidak ada dokumen pendukung yang dilampirkan.');
    }

    public function test_user_can_send_a_message_for_their_active_application(): void
    {
        [$user, $pemohon, $layanan] = $this->createApplicantAndService();
        $pengajuan = $this->createTestApplication($pemohon, $layanan, 'diproses');

        $this->actingAs($user)
            ->post(route('pengajuan.pesan.store', ['nomorTiket' => $pengajuan->nomor_tiket]), [
                'pesan' => 'Mohon informasi perkembangan pengajuan.',
            ])
            ->assertRedirect(route('pengajuan.detail', ['nomorTiket' => $pengajuan->nomor_tiket]).'#komunikasi')
            ->assertSessionHas('success', 'Pesan berhasil dikirim.');

        $this->assertDatabaseHas('pesan_pengajuan', [
            'id_pengajuan' => $pengajuan->id,
            'pengirim_tipe' => 'pemohon',
            'id_pengirim' => $pemohon->id,
            'pesan' => 'Mohon informasi perkembangan pengajuan.',
        ]);
    }

    public function test_user_cannot_open_another_applicants_detail_or_send_a_message(): void
    {
        Storage::fake('local');
        $owner = $this->createApplicantAndService('owner@example.test');
        $otherUser = $this->createApplicantAndService('other@example.test');
        $pengajuan = $this->createTestApplication($owner[1], $owner[2], 'diproses');
        Storage::disk('local')->put('pengajuan/private/document.pdf', 'private document');
        $documentId = DB::table('dokumen_pengajuan')->insertGetId([
            'id_pengajuan' => $pengajuan->id,
            'nama_file' => 'document.pdf',
            'path_file' => 'pengajuan/private/document.pdf',
            'ukuran_file' => '15 KB',
            'diunggah_pada' => now(),
        ]);

        $this->actingAs($otherUser[0])
            ->get(route('pengajuan.detail', ['nomorTiket' => $pengajuan->nomor_tiket]))
            ->assertNotFound();

        $this->actingAs($otherUser[0])
            ->post(route('pengajuan.pesan.store', ['nomorTiket' => $pengajuan->nomor_tiket]), [
                'pesan' => 'Pesan tidak berizin.',
            ])
            ->assertNotFound();

        $this->actingAs($otherUser[0])
            ->get(route('pengajuan.dokumen.download', [
                'nomorTiket' => $pengajuan->nomor_tiket,
                'dokumenId' => $documentId,
            ]))
            ->assertNotFound();

        $this->assertDatabaseMissing('pesan_pengajuan', [
            'id_pengajuan' => $pengajuan->id,
            'pesan' => 'Pesan tidak berizin.',
        ]);
    }

    #[DataProvider('terminalStatuses')]
    public function test_terminal_application_messages_cannot_be_sent(string $status): void
    {
        [$user, $pemohon, $layanan] = $this->createApplicantAndService();
        $pengajuan = $this->createTestApplication($pemohon, $layanan, $status);

        $this->actingAs($user)
            ->get(route('pengajuan.detail', ['nomorTiket' => $pengajuan->nomor_tiket]))
            ->assertOk()
            ->assertSee('KOMUNIKASI DENGAN ADMIN')
            ->assertSee('Percakapan ini hanya dapat dibaca.')
            ->assertDontSee('id="komunikasi-form"', false);

        $this->actingAs($user)
            ->post(route('pengajuan.pesan.store', ['nomorTiket' => $pengajuan->nomor_tiket]), [
                'pesan' => 'Pesan setelah pengajuan ditutup.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('pesan_pengajuan', [
            'id_pengajuan' => $pengajuan->id,
            'pesan' => 'Pesan setelah pengajuan ditutup.',
        ]);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function terminalStatuses(): array
    {
        return [
            'completed' => ['selesai'],
            'rejected' => ['ditolak'],
        ];
    }

    /**
     * @return array{User, Pengguna, Layanan}
     */
    private function createApplicantAndService(string $email = 'pemohon@example.test'): array
    {
        $user = User::factory()->create(['email' => $email]);
        $pemohon = Pengguna::query()->create([
            'nama_lengkap' => 'Pemohon EDUFAIR',
            'email' => $email,
            'kata_sandi' => Hash::make('password'),
        ]);
        $unit = Unit::query()->create([
            'nama_unit' => 'Unit Akademik',
            'kode_unit' => 'AKADEMIK-'.substr(md5($email), 0, 8),
        ]);
        $layanan = Layanan::query()->create([
            'nama_layanan' => 'EDUFAIR',
            'id_unit' => $unit->id,
            'status' => 'aktif',
        ]);

        return [$user, $pemohon, $layanan];
    }

    private function createTestApplication(
        Pengguna $pemohon,
        Layanan $layanan,
        string $status = 'baru'
    ): Pengajuan {
        return Pengajuan::query()->create([
            'nomor_tiket' => 'ULT-2026-'.str_pad((string) $pemohon->id, 6, '0', STR_PAD_LEFT),
            'id_pengguna' => $pemohon->id,
            'id_layanan' => $layanan->id,
            'id_unit' => $layanan->id_unit,
            'subjek' => 'Permohonan partisipasi EDUFAIR',
            'deskripsi' => 'Deskripsi lengkap pengajuan.',
            'status' => $status,
            'dibuat_pada' => '2026-11-10 09:15:00',
        ]);
    }
}
