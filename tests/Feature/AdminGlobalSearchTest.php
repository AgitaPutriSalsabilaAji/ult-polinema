<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminGlobalSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_layanan');
            $table->unsignedBigInteger('id_unit');
            $table->string('estimasi_waktu')->default('1 - 3 Hari Kerja');
            $table->text('deskripsi_singkat')->nullable();
            $table->string('tarif')->default('Rp 0 (Gratis)');
            $table->string('jam_operasional')->default('Senin - Jumat');
            $table->string('status')->default('aktif');
            $table->string('tautan_eksternal')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });

        Schema::create('unit', function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit');
        });

        Schema::create('bagian_layanan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_layanan');
            $table->string('judul_bagian');
            $table->text('konten');
            $table->string('gambar_pendukung')->nullable();
            $table->integer('urutan_bagian')->default(1);
            $table->timestamp('dibuat_pada')->nullable();
        });

        Schema::create('faq', function (Blueprint $table) {
            $table->id();
            $table->text('pertanyaan');
            $table->text('jawaban');
        });

        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('email');
            $table->string('nim_nip')->nullable();
        });

        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket');
            $table->string('subjek');
            $table->text('deskripsi');
            $table->string('status');
            $table->unsignedBigInteger('id_pengguna');
            $table->timestamp('dibuat_pada')->nullable();
        });
    }

    public function test_admin_search_finds_services_faqs_and_submissions_case_insensitively(): void
    {
        DB::table('unit')->insert([
            'id' => 1,
            'nama_unit' => 'Unit Contoh',
        ]);
        DB::table('layanan')->insert([
            'id' => 1,
            'nama_layanan' => 'CONTOH Layanan',
            'id_unit' => 1,
            'deskripsi_singkat' => 'Informasi layanan.',
        ]);
        DB::table('faq')->insert([
            'pertanyaan' => 'Bagaimana cara menghubungi?',
            'jawaban' => 'Gunakan CONTOH sebagai panduan.',
        ]);
        DB::table('pengguna')->insert([
            'id' => 1,
            'nama_lengkap' => 'Pemohon Contoh',
            'email' => 'pemohon@example.test',
            'nim_nip' => '12345',
        ]);
        DB::table('pengajuan')->insert([
            'id' => 7,
            'nomor_tiket' => 'ULT-0007',
            'subjek' => 'Permintaan informasi',
            'deskripsi' => 'CONTOH pengajuan informasi.',
            'status' => 'diajukan',
            'id_pengguna' => 1,
        ]);

        $this->actingAs(new Admin, 'admin')
            ->withoutVite()
            ->get(route('admin.search.index', ['q' => 'contoh']))
            ->assertOk()
            ->assertSeeText('CONTOH Layanan')
            ->assertSeeText('Bagaimana cara menghubungi?')
            ->assertSeeText('ULT-0007')
            ->assertSeeText('Pemohon Contoh')
            ->assertSee('admin-global-search')
            ->assertSee('data-bs-theme-value="dark"', false)
            ->assertSee(route('admin.layanan.show', 1));

        $this->get(route('admin.layanan.show', 1))
            ->assertOk()
            ->assertSeeText('Unit Contoh');
    }

    public function test_global_search_requires_admin_authentication(): void
    {
        $this->withoutVite()
            ->get(route('admin.search.index', ['q' => 'layanan']))
            ->assertRedirect(route('login'));
    }

    public function test_admin_search_shows_an_empty_state_when_there_are_no_matches(): void
    {
        $this->actingAs(new Admin, 'admin')
            ->withoutVite()
            ->get(route('admin.search.index', ['q' => 'tidak-ada']))
            ->assertOk()
            ->assertSeeText('Tidak ada hasil ditemukan');
    }
}
