<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class GlobalSearchController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $term = trim($validated['q'] ?? '');
        $results = collect();

        if ($term !== '') {
            $pattern = '%'.mb_strtolower($term).'%';

            if (Schema::hasTable('layanan')) {
                $services = DB::table('layanan')
                    ->select('id', 'nama_layanan', 'deskripsi_singkat')
                    ->where(function (Builder $query) use ($pattern) {
                        $this->whereMatches(
                            $query,
                            ['nama_layanan', 'deskripsi_singkat'],
                            $pattern
                        );
                    })
                    ->orderBy('nama_layanan')
                    ->limit(5)
                    ->get();

                $results = $results->concat($services->map(fn ($service) => [
                    'type' => 'Layanan',
                    'title' => $service->nama_layanan,
                    'excerpt' => $service->deskripsi_singkat,
                    'url' => route('admin.layanan.show', $service->id),
                ]));
            }

            if (Schema::hasTable('faq')) {
                $faqs = DB::table('faq')
                    ->select('pertanyaan', 'jawaban')
                    ->where(function (Builder $query) use ($pattern) {
                        $this->whereMatches(
                            $query,
                            ['pertanyaan', 'jawaban'],
                            $pattern
                        );
                    })
                    ->orderByDesc('id')
                    ->limit(5)
                    ->get();

                $results = $results->concat($faqs->map(fn ($faq) => [
                    'type' => 'FAQ',
                    'title' => $faq->pertanyaan,
                    'excerpt' => $faq->jawaban,
                    'url' => route('admin.faq.index', ['search' => $term]),
                ]));
            }

            if (Schema::hasTable('pengajuan')) {
                $userColumns = Schema::hasTable('pengguna')
                    ? Schema::getColumnListing('pengguna')
                    : [];
                $nameColumn = collect([
                    'nama_lengkap',
                    'nama',
                    'nama_pengguna',
                    'name',
                ])->first(fn ($column) => in_array($column, $userColumns, true));
                $personSearchColumns = array_values(array_intersect(
                    ['email', 'nip', 'nim_nip', 'nik', 'nomor_induk'],
                    $userColumns
                ));

                $applicationsQuery = DB::table('pengajuan as p')
                    ->select('p.id', 'p.nomor_tiket', 'p.subjek', 'p.deskripsi', 'p.status');

                if ($nameColumn || $personSearchColumns) {
                    $applicationsQuery->leftJoin(
                        'pengguna as u',
                        'u.id',
                        '=',
                        'p.id_pengguna'
                    );
                }

                if ($nameColumn) {
                    $applicationsQuery->addSelect("u.{$nameColumn} as nama_pemohon");
                }

                $applicationColumns = [
                    'p.nomor_tiket',
                    'p.subjek',
                    'p.deskripsi',
                ];

                if ($nameColumn) {
                    $applicationColumns[] = "u.{$nameColumn}";
                }

                foreach ($personSearchColumns as $column) {
                    $applicationColumns[] = "u.{$column}";
                }

                $applications = $applicationsQuery
                    ->where(function (Builder $query) use ($applicationColumns, $pattern) {
                        $this->whereMatches($query, $applicationColumns, $pattern);
                    })
                    ->orderByDesc('p.dibuat_pada')
                    ->limit(5)
                    ->get();

                $results = $results->concat($applications->map(fn ($application) => [
                    'type' => 'Pengajuan',
                    'title' => $application->nomor_tiket.' - '.$application->subjek,
                    'excerpt' => trim(implode(' · ', array_filter([
                        $application->nama_pemohon ?? null,
                        'Status: '.$application->status,
                    ]))),
                    'url' => route('admin.pengajuan.show', $application->id),
                ]));
            }
        }

        return view('admin.search', [
            'query' => $term,
            'results' => $results,
        ]);
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function whereMatches(Builder $query, array $columns, string $pattern): void
    {
        foreach ($columns as $index => $column) {
            $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
            $query->{$method}("LOWER({$column}) LIKE ?", [$pattern]);
        }
    }
}
