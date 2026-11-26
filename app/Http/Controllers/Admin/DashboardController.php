<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\RatingLayanan;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    private const STATUS_GROUPS = [
        'baru' => [
            'label' => 'Pengajuan Baru',
            'statuses' => ['baru', 'diajukan'],
            'color' => '#f59e0b',
        ],
        'diproses' => [
            'label' => 'Sedang Diproses',
            'statuses' => ['didisposisikan', 'diproses'],
            'color' => '#7c3aed',
        ],
        'selesai' => [
            'label' => 'Pengajuan Selesai',
            'statuses' => ['selesai'],
            'color' => '#059669',
        ],
        'ditolak' => [
            'label' => 'Pengajuan Ditolak',
            'statuses' => ['ditolak'],
            'color' => '#ef4444',
        ],
    ];

    public function index(Request $request): View
    {
        $admin = $this->authorizedAdmin();
        $filters = $this->validatedFilters($request, $admin);
        $dashboard = $this->dashboardData($filters, $admin);

        return view('admin.dashboard', [
            ...$dashboard,
            'filters' => $filters,
            'units' => $this->availableUnits($admin),
            'isAdminUlt' => $admin->peran === 'admin_ult',
        ]);
    }

    public function export(Request $request): Response|RedirectResponse
    {
        $admin = $this->authorizedAdmin();
        $filters = $this->validatedFilters($request, $admin);
        $dashboard = $this->dashboardData($filters, $admin);

        if ($dashboard['statistikPengajuan']['total_pengajuan'] === 0) {
            return redirect()
                ->route('admin.dashboard', $filters)
                ->with('info', 'Tidak ada pengajuan yang sesuai dengan filter laporan.');
        }

        $logoPath = public_path('images/polinema-logo.png');
        $logoDataUri = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;
        $pieChartSegments = $this->pieChartSegments($dashboard['pieChart']);
        $barChartSvg = view('admin.dashboard-pdf-bar-chart-svg', [
            'unitApplicationStatistics' => $dashboard['unitApplicationStatistics'],
        ])->render();
        $pieChartSvg = view('admin.dashboard-pdf-pie-chart-svg', [
            'pieChartSegments' => $pieChartSegments,
        ])->render();

        return Pdf::loadView('admin.dashboard-pdf', [
            ...$dashboard,
            'unitName' => $filters['id_unit'] === null
                ? 'Semua Unit'
                : (string) $dashboard['allUnits']->firstWhere('id', $filters['id_unit'])?->nama_unit,
            'period' => $this->formatPeriod($filters),
            'createdAt' => now()->locale('id')->translatedFormat('d F Y H:i'),
            'logoDataUri' => $logoDataUri,
            'barChartImage' => $this->svgDataUri($barChartSvg),
            'pieChartImage' => $this->svgDataUri($pieChartSvg),
            'statusGroups' => self::STATUS_GROUPS,
            'pieChartSegments' => $pieChartSegments,
        ])
            ->setPaper('a4', 'portrait')
            ->download('laporan-statistik-ult-polinema-'.now()->format('Ymd-His').'.pdf');
    }

    private function authorizedAdmin(): Admin
    {
        $admin = auth('admin')->user();

        abort_unless(
            $admin && in_array($admin->peran, ['admin_ult', 'admin_unit'], true),
            403,
            'Anda tidak memiliki akses ke dashboard.'
        );

        return $admin;
    }

    /**
     * @return array{tanggal_mulai: string|null, tanggal_selesai: string|null, id_unit: int|null}
     */
    private function validatedFilters(Request $request, Admin $admin): array
    {
        $filters = $request->validate([
            'tanggal_mulai' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['nullable', 'date_format:Y-m-d'],
            'id_unit' => ['nullable', 'integer', Rule::exists('unit', 'id')],
        ]);

        if (
            ! empty($filters['tanggal_mulai'])
            && ! empty($filters['tanggal_selesai'])
            && $filters['tanggal_selesai'] < $filters['tanggal_mulai']
        ) {
            throw ValidationException::withMessages([
                'tanggal_selesai' => 'Tanggal selesai tidak boleh lebih awal daripada tanggal mulai.',
            ]);
        }

        $selectedUnitId = isset($filters['id_unit']) ? (int) $filters['id_unit'] : null;

        if ($admin->peran === 'admin_unit') {
            abort_unless(
                $selectedUnitId === null || $selectedUnitId === (int) $admin->id_unit,
                403,
                'Anda tidak memiliki akses ke unit yang dipilih.'
            );
            $selectedUnitId = (int) $admin->id_unit;
        }

        return [
            'tanggal_mulai' => $filters['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $filters['tanggal_selesai'] ?? null,
            'id_unit' => $selectedUnitId,
        ];
    }

    /**
     * @param  array{tanggal_mulai: string|null, tanggal_selesai: string|null, id_unit: int|null}  $filters
     * @return array<string, mixed>
     */
    private function dashboardData(array $filters, Admin $admin): array
    {
        $allUnits = $this->availableUnits($admin)
            ->when(
                $filters['id_unit'] !== null,
                fn (Collection $units) => $units->where('id', $filters['id_unit'])->values()
            );

        $applicationAggregates = $this->applyDateFilters(
            Pengajuan::query()
                ->leftJoin('unit', 'unit.id', '=', 'pengajuan.id_unit')
                ->select('pengajuan.id_unit', 'unit.nama_unit')
                ->selectRaw('COUNT(*) AS total_pengajuan')
                ->selectRaw(
                    'SUM(CASE WHEN pengajuan.status IN (?, ?) THEN 1 ELSE 0 END) AS pengajuan_baru',
                    self::STATUS_GROUPS['baru']['statuses']
                )
                ->selectRaw(
                    'SUM(CASE WHEN pengajuan.status IN (?, ?) THEN 1 ELSE 0 END) AS pengajuan_diproses',
                    self::STATUS_GROUPS['diproses']['statuses']
                )
                ->selectRaw(
                    'SUM(CASE WHEN pengajuan.status = ? THEN 1 ELSE 0 END) AS pengajuan_selesai',
                    self::STATUS_GROUPS['selesai']['statuses']
                )
                ->selectRaw(
                    'SUM(CASE WHEN pengajuan.status = ? THEN 1 ELSE 0 END) AS pengajuan_ditolak',
                    self::STATUS_GROUPS['ditolak']['statuses']
                )
                ->groupBy('pengajuan.id_unit', 'unit.nama_unit'),
            $filters,
            'pengajuan.dibuat_pada'
        )
            ->when(
                $filters['id_unit'] !== null,
                fn (Builder $query) => $query->where('pengajuan.id_unit', $filters['id_unit'])
            )
            ->get()
            ->keyBy('id_unit');

        $unitApplicationStatistics = $allUnits->map(function (Unit $unit) use ($applicationAggregates): array {
            $aggregate = $applicationAggregates->get($unit->id);

            return [
                'unit_id' => (int) $unit->id,
                'nama_unit' => $unit->nama_unit,
                'total_pengajuan' => (int) ($aggregate->total_pengajuan ?? 0),
                'pengajuan_baru' => (int) ($aggregate->pengajuan_baru ?? 0),
                'pengajuan_diproses' => (int) ($aggregate->pengajuan_diproses ?? 0),
                'pengajuan_selesai' => (int) ($aggregate->pengajuan_selesai ?? 0),
                'pengajuan_ditolak' => (int) ($aggregate->pengajuan_ditolak ?? 0),
            ];
        });

        $statistikPengajuan = [
            'total_pengajuan' => (int) $unitApplicationStatistics->sum('total_pengajuan'),
            'pengajuan_baru' => (int) $unitApplicationStatistics->sum('pengajuan_baru'),
            'pengajuan_diproses' => (int) $unitApplicationStatistics->sum('pengajuan_diproses'),
            'pengajuan_selesai' => (int) $unitApplicationStatistics->sum('pengajuan_selesai'),
            'pengajuan_ditolak' => (int) $unitApplicationStatistics->sum('pengajuan_ditolak'),
        ];

        $ratingQuery = $this->applyDateFilters(
            RatingLayanan::query()
                ->join('layanan', 'layanan.id', '=', 'rating_layanan.id_layanan')
                ->join('unit', 'unit.id', '=', 'layanan.id_unit')
                ->select('unit.id AS unit_id', 'unit.nama_unit')
                ->selectRaw('COUNT(*) AS total_rating')
                ->selectRaw('COUNT(rating_layanan.rating) AS jumlah_rating_bernilai')
                ->selectRaw('AVG(rating_layanan.rating) AS rata_rata_rating')
                ->groupBy('unit.id', 'unit.nama_unit'),
            $filters,
            'rating_layanan.dibuat_pada'
        )
            ->when(
                $filters['id_unit'] !== null,
                fn (Builder $query) => $query->where('unit.id', $filters['id_unit'])
            )
            ->get()
            ->keyBy('unit_id');

        $unitRatingStatistics = $allUnits->map(function (Unit $unit) use ($ratingQuery): array {
            $rating = $ratingQuery->get($unit->id);

            return [
                'unit_id' => (int) $unit->id,
                'nama_unit' => $unit->nama_unit,
                'total_rating' => (int) ($rating->total_rating ?? 0),
                'jumlah_rating_bernilai' => (int) ($rating->jumlah_rating_bernilai ?? 0),
                'rata_rata_rating' => (float) ($rating->rata_rata_rating ?? 0),
            ];
        });

        $totalRating = $unitRatingStatistics->sum('total_rating');
        $totalNilaiRating = $unitRatingStatistics->sum(
            fn (array $unit): float => $unit['rata_rata_rating'] * $unit['jumlah_rating_bernilai']
        );
        $jumlahRatingBernilai = $unitRatingStatistics->sum('jumlah_rating_bernilai');

        $statistikRating = [
            'jumlah_ulasan' => (int) $totalRating,
            'rata_rata_rating' => $jumlahRatingBernilai > 0
                ? $totalNilaiRating / $jumlahRatingBernilai
                : 0.0,
        ];

        $informasiLayananAktif = $this->applyDateFilters(
            Layanan::query()->where('status', 'aktif'),
            $filters,
            'layanan.dibuat_pada'
        )
            ->when(
                $filters['id_unit'] !== null,
                fn (Builder $query) => $query->where('layanan.id_unit', $filters['id_unit'])
            )
            ->count();

        $pieChart = collect(self::STATUS_GROUPS)
            ->map(fn (array $group, string $key): array => [
                'key' => $key,
                'label' => $group['label'],
                'count' => $statistikPengajuan['pengajuan_'.$key],
                'color' => $group['color'],
            ])
            ->values();

        return compact(
            'allUnits',
            'informasiLayananAktif',
            'pieChart',
            'statistikPengajuan',
            'statistikRating',
            'unitApplicationStatistics',
            'unitRatingStatistics'
        );
    }

    /**
     * @param  array{tanggal_mulai: string|null, tanggal_selesai: string|null}  $filters
     */
    private function applyDateFilters(
        Builder $query,
        array $filters,
        string $column
    ): Builder {
        if ($filters['tanggal_mulai'] !== null) {
            $query->where(
                $column,
                '>=',
                Carbon::createFromFormat('Y-m-d', $filters['tanggal_mulai'])->startOfDay()
            );
        }

        if ($filters['tanggal_selesai'] !== null) {
            $query->where(
                $column,
                '<',
                Carbon::createFromFormat('Y-m-d', $filters['tanggal_selesai'])
                    ->addDay()
                    ->startOfDay()
            );
        }

        return $query;
    }

    /**
     * @return Collection<int, Unit>
     */
    private function availableUnits(Admin $admin): Collection
    {
        return Unit::query()
            ->when(
                $admin->peran === 'admin_unit',
                fn (Builder $query) => $query->whereKey($admin->id_unit)
            )
            ->orderBy('nama_unit')
            ->get(['id', 'nama_unit']);
    }

    /**
     * @param  array{tanggal_mulai: string|null, tanggal_selesai: string|null, id_unit: int|null}  $filters
     */
    private function formatPeriod(array $filters): string
    {
        $formatDate = static fn (string $date): string => Carbon::parse($date)
            ->locale('id')
            ->translatedFormat('d F Y');

        if ($filters['tanggal_mulai'] !== null && $filters['tanggal_selesai'] !== null) {
            return $formatDate($filters['tanggal_mulai']).' - '.$formatDate($filters['tanggal_selesai']);
        }

        if ($filters['tanggal_mulai'] !== null) {
            return 'Mulai '.$formatDate($filters['tanggal_mulai']);
        }

        if ($filters['tanggal_selesai'] !== null) {
            return 'Sampai '.$formatDate($filters['tanggal_selesai']);
        }

        return 'Semua tanggal';
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array{key: string, label: string, count: int, color: string}>  $statuses
     * @return array<int, array{label: string, count: int, color: string, percentage: float, path: string}>
     */
    private function pieChartSegments($statuses): array
    {
        $total = (int) $statuses->sum('count');
        $angle = -90.0;
        $segments = [];

        foreach ($statuses as $status) {
            $count = (int) $status['count'];
            $percentage = $total > 0 ? $count / $total : 0;
            $segmentAngle = 360 * $percentage;
            $path = '';

            if ($count > 0 && $percentage === 1.0) {
                $path = 'M 160 160 L 160 50 A 110 110 0 1 1 160 270 A 110 110 0 1 1 160 50 Z';
            } elseif ($count > 0) {
                $endAngle = $angle + $segmentAngle;
                $start = $this->piePoint($angle);
                $end = $this->piePoint($endAngle);
                $largeArc = $segmentAngle > 180 ? 1 : 0;

                $path = sprintf(
                    'M 160 160 L %s %s A 110 110 0 %d 1 %s %s Z',
                    $start['x'],
                    $start['y'],
                    $largeArc,
                    $end['x'],
                    $end['y']
                );
                $angle = $endAngle;
            }

            $segments[] = [
                'label' => $status['label'],
                'count' => $count,
                'color' => $status['color'],
                'percentage' => $percentage * 100,
                'path' => $path,
            ];
        }

        return $segments;
    }

    /**
     * @return array{x: string, y: string}
     */
    private function piePoint(float $angle): array
    {
        $radians = deg2rad($angle);

        return [
            'x' => number_format(160 + 110 * cos($radians), 2, '.', ''),
            'y' => number_format(160 + 110 * sin($radians), 2, '.', ''),
        ];
    }

    private function svgDataUri(string $svg): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
