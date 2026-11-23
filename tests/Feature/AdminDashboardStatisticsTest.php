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

class AdminDashboardStatisticsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('unit', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nama_unit');
            $table->timestamp('dibuat_pada')->nullable();
        });

        Schema::create('layanan', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('id_unit');
            $table->string('nama_layanan');
            $table->string('status')->default('aktif');
            $table->timestamp('dibuat_pada')->nullable();
        });

        Schema::create('faq', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('id_unit');
            $table->string('status')->default('aktif');
            $table->timestamp('dibuat_pada')->nullable();
        });

        Schema::create('pengajuan', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nomor_tiket')->unique();
            $table->unsignedInteger('id_unit');
            $table->unsignedInteger('id_layanan');
            $table->string('status')->default('baru');
            $table->timestamp('dibuat_pada')->nullable();
        });

        Schema::create('rating_layanan', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('id_layanan');
            $table->unsignedInteger('rating')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
        });
    }

    public function test_dashboard_applies_unit_and_inclusive_date_filters_to_statistics_and_charts(): void
    {
        $this->createUnit(1, 'Unit Akademik');
        $this->createUnit(2, 'Unit Keuangan');
        $this->createService(1, 1, 'Layanan Akademik', 'aktif', '2026-03-10 09:00:00');
        $this->createService(2, 1, 'Layanan Akademik Nonaktif', 'nonaktif', '2026-03-10 09:00:00');
        $this->createService(3, 2, 'Layanan Keuangan', 'aktif', '2026-03-10 09:00:00');
        $this->createFaq(1, 1, '2026-03-10 09:00:00');
        $this->createFaq(2, 2, '2026-03-10 09:00:00');

        foreach ([
            'baru',
            'diajukan',
            'didisposisikan',
            'diproses',
            'selesai',
            'ditolak',
            'selesai',
        ] as $status) {
            $this->createTestPengajuan(1, 1, $status, '2026-03-10 12:00:00');
        }
        $this->createTestPengajuan(1, 1, 'baru', '2026-03-10 23:59:59');
        $this->createTestPengajuan(1, 1, 'ditolak', '2026-03-11 00:00:00');
        $this->createTestPengajuan(2, 3, 'baru', '2026-03-10 12:00:00');

        $this->createRating(1, 5, '2026-03-10 10:00:00');
        $this->createRating(1, 4, '2026-03-10 10:00:00');
        $this->createRating(1, null, '2026-03-10 10:00:00');
        $this->createRating(3, 3, '2026-03-10 10:00:00');
        $this->createRating(1, 1, '2026-03-11 00:00:00');

        $response = $this->actingAsAdmin()
            ->get(route('admin.dashboard', [
                'id_unit' => 1,
                'tanggal_mulai' => '2026-03-10',
                'tanggal_selesai' => '2026-03-10',
            ]));

        $response->assertOk()
            ->assertViewHas('statistikPengajuan', [
                'total_pengajuan' => 8,
                'pengajuan_baru' => 3,
                'pengajuan_diproses' => 2,
                'pengajuan_selesai' => 2,
                'pengajuan_ditolak' => 1,
            ])
            ->assertViewHas('informasiLayananAktif', 1)
            ->assertViewHas('statistikRating', [
                'jumlah_ulasan' => 3,
                'rata_rata_rating' => 4.5,
            ])
            ->assertViewHas('unitApplicationStatistics', function ($statistics): bool {
                $this->assertCount(1, $statistics);
                $this->assertSame('Unit Akademik', $statistics[0]['nama_unit']);
                $this->assertSame(8, $statistics[0]['total_pengajuan']);
                $this->assertSame(1, $statistics[0]['pengajuan_ditolak']);

                return true;
            })
            ->assertViewHas('pieChart', function ($statistics): bool {
                $this->assertSame([3, 2, 2, 1], $statistics->pluck('count')->all());

                return true;
            })
            ->assertSee('id="applicationUnitChart"', false)
            ->assertSee('id="applicationStatusChart"', false)
            ->assertSeeText('Pengajuan Ditolak')
            ->assertSeeText('Informasi Layanan')
            ->assertSeeText('Statistik Pengajuan per Unit')
            ->assertSeeText('Distribusi Status Pengajuan')
            ->assertDontSeeText('Total E-Ticket')
            ->assertDontSeeText('E-Ticket Aktif');

        $this->assertTrue(
            str_contains($response->getContent(), 'resources/js/dashboard.js')
                || preg_match('/assets\/dashboard-[^"\']+\.js/', $response->getContent()) === 1,
            'The dashboard chart entry point must be included in the rendered page.'
        );

        $this->actingAsAdmin()
            ->get(route('admin.dashboard', [
                'tanggal_mulai' => '2026-03-10',
                'tanggal_selesai' => '2026-03-10',
            ]))
            ->assertOk()
            ->assertViewHas('statistikRating', [
                'jumlah_ulasan' => 4,
                'rata_rata_rating' => 4.0,
            ]);
    }

    public function test_admin_unit_is_limited_to_their_unit_and_cannot_tamper_with_export_filter(): void
    {
        $this->createUnit(1, 'Unit Akademik');
        $this->createUnit(2, 'Unit Keuangan');
        $this->createService(1, 1, 'Layanan Akademik', 'aktif', '2026-03-10 09:00:00');
        $this->createService(2, 2, 'Layanan Keuangan', 'aktif', '2026-03-10 09:00:00');
        $this->createTestPengajuan(1, 1, 'baru', '2026-03-10 10:00:00');
        $this->createTestPengajuan(2, 2, 'ditolak', '2026-03-10 10:00:00');

        $this->actingAsAdmin('admin_unit', 1)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('units', fn ($units): bool => $units->pluck('id')->all() === [1])
            ->assertViewHas('statistikPengajuan', [
                'total_pengajuan' => 1,
                'pengajuan_baru' => 1,
                'pengajuan_diproses' => 0,
                'pengajuan_selesai' => 0,
                'pengajuan_ditolak' => 0,
            ])
            ->assertViewHas('unitApplicationStatistics', fn ($statistics): bool => $statistics->count() === 1)
            ->assertDontSeeText('Unit layanan utama')
            ->assertDontSeeText('Pertanyaan yang tersedia');

        $this->actingAsAdmin('admin_unit', 1)
            ->get(route('admin.dashboard.export', ['id_unit' => 2]))
            ->assertForbidden();
    }

    public function test_admin_unit_export_contains_only_their_unit_statistics(): void
    {
        $this->createUnit(1, 'Unit Akademik');
        $this->createUnit(2, 'Unit Keuangan');
        $this->createService(1, 1, 'Layanan Akademik', 'aktif', '2026-03-10 09:00:00');
        $this->createService(2, 2, 'Layanan Keuangan', 'aktif', '2026-03-10 09:00:00');
        $this->createTestPengajuan(1, 1, 'baru', '2026-03-10 10:00:00');
        $this->createTestPengajuan(2, 2, 'ditolak', '2026-03-10 10:00:00');

        $pdf = Mockery::mock(DomPdfWrapper::class);
        $pdf->shouldReceive('setPaper')
            ->once()
            ->with('a4', 'portrait')
            ->andReturnSelf();
        $pdf->shouldReceive('download')
            ->once()
            ->andReturn(response('generated-pdf'));

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('admin.dashboard-pdf', Mockery::on(function (array $data): bool {
                $this->assertSame(1, $data['statistikPengajuan']['total_pengajuan']);
                $this->assertSame(1, $data['statistikPengajuan']['pengajuan_baru']);
                $this->assertCount(1, $data['allUnits']);
                $this->assertSame(
                    'Unit Akademik',
                    $data['unitApplicationStatistics']->first()['nama_unit']
                );
                $this->assertSame('Unit Akademik', $data['unitName']);
                $this->assertStringStartsWith('data:image/svg+xml;base64,', $data['barChartImage']);
                $this->assertStringStartsWith('data:image/svg+xml;base64,', $data['pieChartImage']);

                $barChartSvg = base64_decode(substr($data['barChartImage'], strlen('data:image/svg+xml;base64,')), true);
                $pieChartSvg = base64_decode(substr($data['pieChartImage'], strlen('data:image/svg+xml;base64,')), true);

                $this->assertIsString($barChartSvg);
                $this->assertIsString($pieChartSvg);
                $this->assertStringContainsString('<rect', $barChartSvg);
                $this->assertStringContainsString('<path', $pieChartSvg);
                $this->assertStringContainsString('width="820" height="230"', $barChartSvg);
                $this->assertStringContainsString('width="320" height="320"', $pieChartSvg);
                $this->assertStringContainsString('viewBox="0 0 320 320"', $pieChartSvg);

                return true;
            }))
            ->andReturn($pdf);

        $this->actingAsAdmin('admin_unit', 1)
            ->get(route('admin.dashboard.export'))
            ->assertOk()
            ->assertSeeText('generated-pdf');
    }

    public function test_dashboard_export_returns_a_pdf_using_the_active_filters(): void
    {
        $this->createUnit(1, 'Unit Akademik');
        $this->createService(1, 1, 'Layanan Akademik', 'aktif', '2026-03-10 09:00:00');
        $this->createTestPengajuan(1, 1, 'baru', '2026-03-10 12:00:00');
        $this->createTestPengajuan(2, 1, 'didisposisikan', '2026-03-10 12:00:00');
        $this->createRating(1, 5, '2026-03-10 12:00:00');

        $response = $this->actingAsAdmin()
            ->get(route('admin.dashboard.export', [
                'id_unit' => 1,
                'tanggal_mulai' => '2026-03-10',
                'tanggal_selesai' => '2026-03-10',
            ]));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_dashboard_validates_date_order_and_shows_zero_values_for_empty_data(): void
    {
        $this->createUnit(1, 'Unit Akademik');

        $this->actingAsAdmin()
            ->get(route('admin.dashboard', [
                'tanggal_mulai' => '2026-03-11',
                'tanggal_selesai' => '2026-03-10',
            ]))
            ->assertSessionHasErrors(['tanggal_selesai']);

        $this->actingAsAdmin()
            ->get(route('admin.dashboard', [
                'tanggal_mulai' => '2026-03-10',
                'tanggal_selesai' => '2026-03-10',
            ]))
            ->assertOk()
            ->assertViewHas('statistikPengajuan.total_pengajuan', 0)
            ->assertViewHas('statistikRating.rata_rata_rating', 0.0)
            ->assertSeeText('0.00 / 5');
    }

    public function test_export_redirects_when_the_selected_period_has_no_applications(): void
    {
        $this->createUnit(1, 'Unit Akademik');

        $this->actingAsAdmin()
            ->get(route('admin.dashboard.export', [
                'id_unit' => 1,
                'tanggal_mulai' => '2026-03-10',
                'tanggal_selesai' => '2026-03-10',
            ]))
            ->assertRedirect(route('admin.dashboard', [
                'tanggal_mulai' => '2026-03-10',
                'tanggal_selesai' => '2026-03-10',
                'id_unit' => 1,
            ]))
            ->assertSessionHas('info', 'Tidak ada pengajuan yang sesuai dengan filter laporan.');
    }

    private function actingAsAdmin(string $role = 'admin_ult', int $unitId = 1): static
    {
        $admin = new Admin;
        $admin->setAttribute('id', 1);
        $admin->setAttribute('peran', $role);
        $admin->setAttribute('id_unit', $unitId);

        return $this->actingAs($admin, 'admin');
    }

    private function createUnit(int $id, string $name): void
    {
        DB::table('unit')->insert([
            'id' => $id,
            'nama_unit' => $name,
            'dibuat_pada' => '2026-01-01 09:00:00',
        ]);
    }

    private function createService(
        int $id,
        int $unitId,
        string $name,
        string $status,
        string $createdAt
    ): void {
        DB::table('layanan')->insert([
            'id' => $id,
            'id_unit' => $unitId,
            'nama_layanan' => $name,
            'status' => $status,
            'dibuat_pada' => $createdAt,
        ]);
    }

    private function createFaq(int $id, int $unitId, string $createdAt): void
    {
        DB::table('faq')->insert([
            'id' => $id,
            'id_unit' => $unitId,
            'status' => 'aktif',
            'dibuat_pada' => $createdAt,
        ]);
    }

    private function createTestPengajuan(int $unitId, int $serviceId, string $status, string $createdAt): void
    {
        $ticketSequence = DB::table('pengajuan')->count() + 1;

        DB::table('pengajuan')->insert([
            'nomor_tiket' => 'ULT-2026-'.str_pad((string) $ticketSequence, 6, '0', STR_PAD_LEFT),
            'id_unit' => $unitId,
            'id_layanan' => $serviceId,
            'status' => $status,
            'dibuat_pada' => $createdAt,
        ]);
    }

    private function createRating(int $serviceId, ?int $rating, string $createdAt): void
    {
        DB::table('rating_layanan')->insert([
            'id_layanan' => $serviceId,
            'rating' => $rating,
            'dibuat_pada' => $createdAt,
        ]);
    }
}
