<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminDashboardStatisticsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('unit', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nama_unit');
        });

        Schema::create('layanan', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nama_layanan');
        });

        Schema::create('faq', function (Blueprint $table): void {
            $table->increments('id');
        });

        Schema::create('pengajuan', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nomor_tiket')->unique();
            $table->string('status')->default('baru');
        });

        Schema::create('rating_layanan', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('rating')->nullable();
        });
    }

    public function test_dashboard_displays_database_statistics_for_applications_tickets_and_ratings(): void
    {
        foreach ([
            'baru',
            'diajukan',
            'didisposisikan',
            'diproses',
            'selesai',
            'ditolak',
        ] as $index => $status) {
            DB::table('pengajuan')->insert([
                'nomor_tiket' => 'ULT-2026-'.str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT),
                'status' => $status,
            ]);
        }

        DB::table('unit')->insert([
            ['nama_unit' => 'Unit Satu'],
            ['nama_unit' => 'Unit Dua'],
        ]);
        DB::table('layanan')->insert(['nama_layanan' => 'Layanan Satu']);
        DB::table('rating_layanan')->insert([
            ['rating' => 5],
            ['rating' => 4],
            ['rating' => null],
        ]);

        $this->actingAsAdmin()
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('jumlahUnit', 2)
            ->assertViewHas('statistikPengajuan', function ($statistics): bool {
                $this->assertSame(6, (int) $statistics->total_pengajuan);
                $this->assertSame(2, (int) $statistics->pengajuan_baru);
                $this->assertSame(2, (int) $statistics->pengajuan_diproses);
                $this->assertSame(1, (int) $statistics->pengajuan_selesai);
                $this->assertSame(4, (int) $statistics->eticket_aktif);

                return true;
            })
            ->assertViewHas('statistikRating', function ($statistics): bool {
                $this->assertSame(3, (int) $statistics->jumlah_ulasan);
                $this->assertEquals(4.5, (float) $statistics->rata_rata_rating);

                return true;
            })
            ->assertSeeText('Total Pengajuan')
            ->assertSeeText('Pengajuan Baru')
            ->assertSeeText('Sedang Diproses')
            ->assertSeeText('Pengajuan Selesai')
            ->assertSeeText('Total E-Ticket')
            ->assertSeeText('E-Ticket Aktif')
            ->assertSeeText('Rating Layanan')
            ->assertSeeText('Informasi Sistem');
    }

    public function test_dashboard_displays_zero_values_when_there_is_no_data(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('0.00')
            ->assertSeeText('Dari 0 ulasan');
    }

    private function actingAsAdmin(): static
    {
        $admin = new Admin;
        $admin->setAttribute('id', 1);

        return $this->actingAs($admin, 'admin');
    }
}
