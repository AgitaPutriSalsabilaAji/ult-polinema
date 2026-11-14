<?php

namespace Tests\Feature;

use App\Models\Admin;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class RatingLayananExportTest extends TestCase
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
            $table->unsignedInteger('id_unit');
            $table->string('nama_layanan');
        });

        Schema::create('pengguna', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nama_lengkap');
            $table->string('nim_nip')->nullable();
        });

        Schema::create('rating_layanan', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('id_pengajuan');
            $table->unsignedInteger('id_pengguna');
            $table->unsignedInteger('id_layanan');
            $table->unsignedInteger('rating')->nullable();
            $table->text('komentar')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
        });
    }

    public function test_export_uses_all_matching_rows_for_summary_and_detail(): void
    {
        $this->createUnitAndService(1, 'Unit Satu', 1, 'Layanan Satu');
        $this->createUnitAndService(2, 'Unit Dua', 2, 'Layanan Dua');

        for ($index = 0; $index < 12; $index++) {
            $this->createRating(
                1,
                ($index % 5) + 1,
                $index === 0 ? '   ' : 'Komentar '.$index,
                $index === 11 ? '2026-03-10 23:59:59' : '2026-03-10 12:00:00'
            );
        }

        $this->createRating(1, 5, 'Di luar periode', '2026-03-11 00:00:00');
        $this->createRating(2, 5, 'Unit lain', '2026-03-10 12:00:00');

        $pdf = Mockery::mock(DomPdfWrapper::class);
        $pdf->shouldReceive('setPaper')
            ->once()
            ->with('a4', 'landscape')
            ->andReturnSelf();
        $pdf->shouldReceive('download')
            ->once()
            ->andReturn(response('generated-pdf'));

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('admin.rating-ulasan-pdf', Mockery::on(function (array $data): bool {
                $this->assertSame(12, (int) $data['summary']->total_rating);
                $this->assertEquals(2.75, (float) $data['summary']->average_rating);
                $this->assertSame(11, (int) $data['summary']->total_comments);
                $this->assertCount(1, $data['unitSummaries']);
                $this->assertSame(12, $data['unitSummaries'][0]->total_rating);
                $this->assertSame(3, $data['unitSummaries'][0]->rating_1);
                $this->assertSame(3, $data['unitSummaries'][0]->rating_2);
                $this->assertSame(2, $data['unitSummaries'][0]->rating_3);
                $this->assertSame(2, $data['unitSummaries'][0]->rating_4);
                $this->assertSame(2, $data['unitSummaries'][0]->rating_5);
                $this->assertCount(12, $data['ratings']);
                $this->assertSame('10 Maret 2026 - 10 Maret 2026', $data['period']);

                return true;
            }))
            ->andReturn($pdf);

        $this->actingAsAdmin()
            ->get(route('admin.rating-ulasan.export', [
                'id_unit' => 1,
                'tanggal_mulai' => '2026-03-10',
                'tanggal_selesai' => '2026-03-10',
            ]))
            ->assertOk()
            ->assertSeeText('generated-pdf');
    }

    public function test_export_downloads_a_valid_pdf(): void
    {
        $this->createUnitAndService(1, 'Unit Satu', 1, 'Layanan Satu');
        $this->createRating(1, 5, 'Pelayanan baik', '2026-03-10 10:30:00');

        $response = $this->actingAsAdmin()
            ->get(route('admin.rating-ulasan.export'));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_index_combines_date_unit_and_rating_filters(): void
    {
        $this->createUnitAndService(1, 'Unit Satu', 1, 'Layanan Satu');
        DB::table('pengguna')->insert([
            'id' => 1,
            'nama_lengkap' => 'Pemohon Satu',
        ]);
        $this->createRating(1, 5, 'Komentar terpilih', '2026-03-10 10:00:00');
        $this->createRating(1, 3, 'Komentar rating lain', '2026-03-10 11:00:00');
        $this->createRating(1, 4, 'Komentar di luar periode', '2026-03-11 00:00:00');

        $this->actingAsAdmin()
            ->get(route('admin.rating-ulasan.index', [
                'id_unit' => 1,
                'rating' => 5,
                'tanggal_mulai' => '2026-03-10',
                'tanggal_selesai' => '2026-03-10',
            ]))
            ->assertOk()
            ->assertViewHas('totalRating', 2)
            ->assertViewHas('rataRataRating', 4.0)
            ->assertViewHas('ulasan', fn ($ulasan): bool => $ulasan->total() === 1)
            ->assertSeeText('Komentar terpilih')
            ->assertDontSeeText('Komentar rating lain')
            ->assertSee('value="2026-03-10"', false);
    }

    public function test_export_redirects_back_with_an_informative_message_when_no_rows_match(): void
    {
        $this->createUnitAndService(1, 'Unit Satu', 1, 'Layanan Satu');

        $this->actingAsAdmin()
            ->get(route('admin.rating-ulasan.export', [
                'tanggal_mulai' => '2026-03-10',
                'tanggal_selesai' => '2026-03-10',
            ]))
            ->assertRedirect(route('admin.rating-ulasan.index', [
                'tanggal_mulai' => '2026-03-10',
                'tanggal_selesai' => '2026-03-10',
            ]))
            ->assertSessionHas('info', 'Tidak ada rating dan ulasan yang sesuai dengan filter laporan.');
    }

    public function test_export_rejects_an_end_date_before_the_start_date(): void
    {
        $this->createUnitAndService(1, 'Unit Satu', 1, 'Layanan Satu');

        $this->actingAsAdmin()
            ->get(route('admin.rating-ulasan.export', [
                'tanggal_mulai' => '2026-03-11',
                'tanggal_selesai' => '2026-03-10',
            ]))
            ->assertSessionHasErrors(['tanggal_selesai']);
    }

    private function actingAsAdmin(): static
    {
        $admin = new Admin;
        $admin->setAttribute('id', 1);

        return $this->actingAs($admin, 'admin');
    }

    private function createUnitAndService(
        int $unitId,
        string $unitName,
        int $serviceId,
        string $serviceName
    ): void {
        DB::table('unit')->insert([
            'id' => $unitId,
            'nama_unit' => $unitName,
        ]);
        DB::table('layanan')->insert([
            'id' => $serviceId,
            'id_unit' => $unitId,
            'nama_layanan' => $serviceName,
        ]);
    }

    private function createRating(
        int $serviceId,
        int $rating,
        string $comment,
        string $createdAt
    ): void {
        DB::table('rating_layanan')->insert([
            'id_pengajuan' => 1,
            'id_pengguna' => 1,
            'id_layanan' => $serviceId,
            'rating' => $rating,
            'komentar' => $comment,
            'dibuat_pada' => $createdAt,
        ]);
    }
}
