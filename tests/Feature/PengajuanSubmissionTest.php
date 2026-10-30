<?php

namespace Tests\Feature;

use App\Models\RiwayatPengajuan;
use Illuminate\Database\Schema\Blueprint;
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

        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket')->unique();
            $table->unsignedBigInteger('id_pengguna');
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
        $response = $this->from(route('layanan.index'))
            ->post(route('pengajuan.submit'), [
                'nama_lengkap' => 'Budi Santoso',
                'email' => 'budi@example.com',
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
            'id_layanan' => 1,
            'subjek' => 'Permohonan Surat Keterangan Kuliah',
            'status' => 'diajukan',
        ]);
    }

    public function test_success_page_is_rendered_for_submitted_ticket(): void
    {
        $submitResponse = $this->post(route('pengajuan.submit'), [
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

        $viewResponse = $this->get(route('pengajuan.berhasil', ['nomorTiket' => $nomorTiket]));
        $viewResponse->assertOk();
        $viewResponse->assertSee('PENGAJUAN BERHASIL DIKIRIM');
        $viewResponse->assertSee($nomorTiket);
    }

    public function test_ticket_numbers_are_generated_in_sequence_from_database_ids(): void
    {
        $firstResponse = $this->post(route('pengajuan.submit'), [
            'nama_lengkap' => 'Rina Amelia',
            'email' => 'rina@example.com',
            'nomor_telepon' => '081234567892',
            'nim_nip' => '20240014',
            'id_layanan' => 1,
            'subjek' => 'Permohonan Surat Keterangan Kuliah 1',
            'deskripsi' => 'Saya memerlukan surat keterangan kuliah untuk kebutuhan administrasi 1.',
            'pernyataan' => 'on',
        ]);

        $secondResponse = $this->post(route('pengajuan.submit'), [
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

        $this->assertMatchesRegularExpression('/^ULT-' . date('Y') . '-\d{6}$/', $firstTicket);
        $this->assertMatchesRegularExpression('/^ULT-' . date('Y') . '-\d{6}$/', $secondTicket);
        $this->assertNotSame($firstTicket, $secondTicket);

        preg_match('/^ULT-' . date('Y') . '-(\d{6})$/', $firstTicket, $firstNumber);
        preg_match('/^ULT-' . date('Y') . '-(\d{6})$/', $secondTicket, $secondNumber);

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
