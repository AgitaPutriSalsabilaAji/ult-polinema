<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RatingLayanan;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RatingLayananController extends Controller
{
    /**
     * Menampilkan halaman Rating & Ulasan Layanan ULT.
     */
    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);
        $units = Unit::orderBy('nama_unit')->get();

        $query = $this->applyFilters(
            RatingLayanan::with([
                'pengguna',
                'layanan.unit',
            ]),
            $filters
        );

        $statistikQuery = $this->applyFilters(
            RatingLayanan::query(),
            $filters,
            false
        );

        $totalRating = (clone $statistikQuery)->count();
        $rataRataRating = (clone $statistikQuery)->avg('rating') ?? 0;

        $ulasan = $query
            ->orderByDesc('dibuat_pada')
            ->paginate(10)
            ->withQueryString();

        return view('admin.rating', compact(
            'units',
            'ulasan',
            'totalRating',
            'rataRataRating'
        ));
    }

    /**
     * Mengunduh laporan rating dan ulasan sesuai filter halaman.
     */
    public function export(Request $request): Response|RedirectResponse
    {
        $filters = $this->validatedFilters($request);
        $aggregateQuery = $this->applyFilters(
            RatingLayanan::query()
                ->join('layanan', 'rating_layanan.id_layanan', '=', 'layanan.id')
                ->join('unit', 'layanan.id_unit', '=', 'unit.id'),
            $filters
        );

        $summary = (clone $aggregateQuery)
            ->selectRaw(
                'COUNT(rating_layanan.id) AS total_rating,
                AVG(rating_layanan.rating) AS average_rating,
                COUNT(CASE WHEN rating_layanan.komentar IS NOT NULL
                    AND TRIM(rating_layanan.komentar) <> \'\' THEN 1 END) AS total_comments'
            )
            ->first();

        if ((int) $summary->total_rating === 0) {
            return redirect()
                ->route('admin.rating-ulasan.index', $filters)
                ->with('info', 'Tidak ada rating dan ulasan yang sesuai dengan filter laporan.');
        }

        $unitSummaries = (clone $aggregateQuery)
            ->select('unit.id AS unit_id', 'unit.nama_unit')
            ->selectRaw(
                'COUNT(rating_layanan.id) AS total_rating,
                AVG(rating_layanan.rating) AS average_rating,
                SUM(CASE WHEN rating_layanan.rating = 5 THEN 1 ELSE 0 END) AS rating_5,
                SUM(CASE WHEN rating_layanan.rating = 4 THEN 1 ELSE 0 END) AS rating_4,
                SUM(CASE WHEN rating_layanan.rating = 3 THEN 1 ELSE 0 END) AS rating_3,
                SUM(CASE WHEN rating_layanan.rating = 2 THEN 1 ELSE 0 END) AS rating_2,
                SUM(CASE WHEN rating_layanan.rating = 1 THEN 1 ELSE 0 END) AS rating_1'
            )
            ->groupBy('unit.id', 'unit.nama_unit')
            ->orderBy('unit.nama_unit')
            ->get();

        $ratings = $this->applyFilters(
            RatingLayanan::with([
                'layanan:id,nama_layanan,id_unit',
                'layanan.unit:id,nama_unit',
            ]),
            $filters
        )
            ->orderByDesc('dibuat_pada')
            ->get([
                'id',
                'id_layanan',
                'rating',
                'komentar',
                'dibuat_pada',
            ]);

        $createdAt = now();
        $logoPath = public_path('images/polinema-logo.png');
        $logoDataUri = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        return Pdf::loadView('admin.rating-ulasan-pdf', [
            'summary' => $summary,
            'unitSummaries' => $unitSummaries,
            'ratings' => $ratings,
            'period' => $this->formatPeriod($filters),
            'createdAt' => $createdAt->locale('id')->translatedFormat('d F Y H:i'),
            'logoDataUri' => $logoDataUri,
        ])
            ->setPaper('a4', 'landscape')
            ->download('laporan-rating-ulasan-ult-polinema-'.$createdAt->format('Ymd-His').'.pdf');
    }

    /**
     * @return array{tanggal_mulai?: string|null, tanggal_selesai?: string|null, id_unit?: int|string|null, rating?: int|string|null}
     */
    private function validatedFilters(Request $request): array
    {
        $filters = $request->validate([
            'tanggal_mulai' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['nullable', 'date_format:Y-m-d'],
            'id_unit' => ['nullable', 'integer', Rule::exists('unit', 'id')],
            'rating' => ['nullable', 'integer', Rule::in([1, 2, 3, 4, 5])],
        ], [
            'tanggal_mulai.date_format' => 'Tanggal mulai harus menggunakan format tanggal yang valid.',
            'tanggal_selesai.date_format' => 'Tanggal selesai harus menggunakan format tanggal yang valid.',
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

        return $filters;
    }

    /**
     * @param  array{tanggal_mulai?: string|null, tanggal_selesai?: string|null, id_unit?: int|string|null, rating?: int|string|null}  $filters
     */
    private function applyFilters(
        Builder $query,
        array $filters,
        bool $includeRatingFilter = true
    ): Builder {
        if (! empty($filters['tanggal_mulai'])) {
            $query->where(
                'rating_layanan.dibuat_pada',
                '>=',
                Carbon::createFromFormat('Y-m-d', $filters['tanggal_mulai'])->startOfDay()
            );
        }

        if (! empty($filters['tanggal_selesai'])) {
            $query->where(
                'rating_layanan.dibuat_pada',
                '<',
                Carbon::createFromFormat('Y-m-d', $filters['tanggal_selesai'])
                    ->addDay()
                    ->startOfDay()
            );
        }

        if (! empty($filters['id_unit'])) {
            $query->whereHas('layanan', function (Builder $layananQuery) use ($filters): void {
                $layananQuery->where('id_unit', $filters['id_unit']);
            });
        }

        if ($includeRatingFilter && ! empty($filters['rating'])) {
            $query->where('rating_layanan.rating', $filters['rating']);
        }

        return $query;
    }

    /**
     * @param  array{tanggal_mulai?: string|null, tanggal_selesai?: string|null}  $filters
     */
    private function formatPeriod(array $filters): string
    {
        $formatDate = static fn (string $date): string => Carbon::parse($date)
            ->locale('id')
            ->translatedFormat('d F Y');

        if (! empty($filters['tanggal_mulai']) && ! empty($filters['tanggal_selesai'])) {
            return $formatDate($filters['tanggal_mulai']).' - '.$formatDate($filters['tanggal_selesai']);
        }

        if (! empty($filters['tanggal_mulai'])) {
            return 'Mulai '.$formatDate($filters['tanggal_mulai']);
        }

        if (! empty($filters['tanggal_selesai'])) {
            return 'Sampai '.$formatDate($filters['tanggal_selesai']);
        }

        return 'Semua tanggal';
    }

    /**
     * Menyimpan rating dan ulasan dari pemohon.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_pengajuan' => [
                'required',
                'integer',
                Rule::exists('pengajuan', 'id_pengajuan'),
            ],

            'id_layanan' => [
                'required',
                'integer',
                Rule::exists('layanan', 'id'),
            ],

            'rating' => [
                'required',
                'integer',
                Rule::in([1, 2, 3, 4, 5]),
            ],

            'komentar' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        /*
         * Pastikan route dilindungi middleware auth
         * dan pengguna sudah login.
         *
         * id_pengguna diambil dari akun yang login,
         * bukan dari input form yang dapat dimanipulasi.
         */
        $pengguna = $request->user();

        abort_unless($pengguna, 401);

        /*
         * Validasi kepemilikan pengajuan dan kecocokan
         * layanan harus disesuaikan dengan skema tabel
         * pengajuan yang digunakan proyek.
         */

        RatingLayanan::create([
            'id_pengajuan' => $validated['id_pengajuan'],
            'id_pengguna' => $pengguna->id,
            'id_layanan' => $validated['id_layanan'],
            'rating' => $validated['rating'],
            'komentar' => $validated['komentar'],
            'dibuat_pada' => now(),
        ]);

        return back()->with(
            'success',
            'Rating dan ulasan berhasil dikirim.'
        );
    }
}
