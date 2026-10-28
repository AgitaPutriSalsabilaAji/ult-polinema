<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\BagianLayanan;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LayananController extends Controller
{

    public function index(Request $request)
    {
        $query = Layanan::with('unit')
            ->withCount('bagianLayanan');

        if ($request->filled('q')) {
            $search = mb_strtolower($request->validate([
                'q' => ['required', 'string', 'max:100'],
            ])['q']);

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nama_layanan) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(deskripsi_singkat) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('unit')) {
            $query->where('id_unit', $request->unit);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $layanans = $query->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $units = Unit::orderBy('nama_unit')->get();

        return view('admin.layanan', compact('layanans', 'units'));
    }

    public function create()
    {
        $units = Unit::orderBy('nama_unit')->get();

        return view('admin.form-layanan', [
            'layanan' => new Layanan(),
            'units' => $units,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateLayanan($request);

        $paths = [];

        try {
            DB::transaction(function () use (
                $data,
                $request,
                &$paths
            ) {
                $layanan = Layanan::create(
                    $this->layananData($data)
                );

                $this->saveSections(
                    $layanan,
                    $data['bagian'],
                    $request,
                    $paths
                );
            });
        } catch (\Throwable $e) {
            foreach ($paths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }

        return redirect()
            ->route('admin.layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Layanan $layanan)
    {
        $layanan->load('bagianLayanan');

        $units = Unit::orderBy('nama_unit')->get();

        return view('admin.form-layanan', [
            'layanan' => $layanan,
            'units' => $units,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Layanan $layanan)
    {
        $data = $this->validateLayanan($request, $layanan);

        // Semua ID bagian yang dikirim harus milik layanan ini.
        foreach ($data['bagian'] as $item) {
            if (!empty($item['id'])) {
                abort_unless(
                    $layanan->bagianLayanan()
                        ->whereKey($item['id'])
                        ->exists(),
                    403,
                    'Bagian informasi tidak sesuai dengan layanan.'
                );
            }
        }

        $newPaths = [];
        $oldPaths = [];

        try {
            DB::transaction(function () use (
                $data,
                $request,
                $layanan,
                &$newPaths,
                &$oldPaths
            ) {
                $layanan->update($this->layananData($data));

                $existing = $layanan->bagianLayanan()
                    ->get()
                    ->keyBy('id');

                $keptIds = [];

                foreach ($data['bagian'] as $index => $item) {
                    $bagian = !empty($item['id'])
                        ? $existing->get($item['id'])
                        : null;

                    if ($bagian) {
                        $keptIds[] = $bagian->id;
                    } else {
                        $bagian = new BagianLayanan();
                        $bagian->id_layanan = $layanan->id;
                    }

                    $bagian->judul_bagian = $item['judul_bagian'];
                    $bagian->konten = $item['konten'];
                    $bagian->urutan_bagian = $index + 1;

                    if ($request->hasFile("bagian.$index.gambar")) {
                        $path = $request->file(
                            "bagian.$index.gambar"
                        )->store('layanan', 'public');

                        $newPaths[] = $path;

                        if ($bagian->gambar_pendukung) {
                            $oldPaths[] = $bagian->gambar_pendukung;
                        }

                        $bagian->gambar_pendukung = $path;
                    }

                    $bagian->dibuat_pada ??= now();
                    $bagian->save();

                    $keptIds[] = $bagian->id;
                }

                // Bagian yang dihapus dari formulir ikut dihapus dari DB.
                $removed = $layanan->bagianLayanan()
                    ->whereNotIn('id', $keptIds)
                    ->get();

                foreach ($removed as $item) {
                    if ($item->gambar_pendukung) {
                        $oldPaths[] = $item->gambar_pendukung;
                    }

                    $item->delete();
                }
            });
        } catch (\Throwable $e) {
            foreach ($newPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }

        foreach (array_unique($oldPaths) as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()
            ->route('admin.layanan.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function show(Layanan $layanan)
    {
        $layanan->load(['unit', 'bagianLayanan']);

        return view('admin.detail-layanan', compact('layanan'));
    }

    public function toggleStatus(Layanan $layanan)
    {
        $layanan->status = $layanan->status === 'aktif'
            ? 'nonaktif'
            : 'aktif';

        $layanan->save();

        return back()->with(
            'success',
            'Status layanan berhasil diperbarui.'
        );
    }

    public function destroy(Layanan $layanan)
    {
        DB::transaction(function () use ($layanan) {
            $layanan->load('bagianLayanan');

            foreach ($layanan->bagianLayanan as $bagian) {
                if ($bagian->gambar_pendukung) {
                    Storage::disk('public')->delete(
                        $bagian->gambar_pendukung
                    );
                }

                $bagian->delete();
            }

            $layanan->delete();
        });

        return redirect()
            ->route('admin.layanan.index')
            ->with('success', 'Layanan berhasil dihapus.');
    }

    private function validateLayanan(
        Request $request,
        ?Layanan $layanan = null
    ): array {
        return $request->validate([
            'nama_layanan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('layanan', 'nama_layanan')
                    ->ignore($layanan?->id),
            ],
            'id_unit' => [
                'required',
                'integer',
                'exists:unit,id',
            ],
            'estimasi_waktu' => ['required', 'string', 'max:100'],
            'deskripsi_singkat' => ['required', 'string'],
            'tarif' => ['nullable', 'string', 'max:255'],
            'jam_operasional' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'tautan_eksternal' => ['nullable', 'url', 'max:255'],

            'bagian' => ['required', 'array', 'min:1'],
            'bagian.*.id' => [
                'nullable',
                'integer',
                'distinct',
                'exists:bagian_layanan,id',
            ],
            'bagian.*.judul_bagian' => [
                'required',
                'string',
                'max:255',
            ],
            'bagian.*.konten' => ['required', 'string'],
            'bagian.*.gambar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);
    }

    private function layananData(array $data): array
    {
        return [
            'nama_layanan' => $data['nama_layanan'],
            'id_unit' => $data['id_unit'],
            'estimasi_waktu' => $data['estimasi_waktu'],
            'deskripsi_singkat' => $data['deskripsi_singkat'],
            'tarif' => $data['tarif'] ?? null,
            'jam_operasional' => $data['jam_operasional'] ?? null,
            'status' => $data['status'],
            'tautan_eksternal' => $data['tautan_eksternal'] ?? null,
        ];
    }

    private function saveSections(
        Layanan $layanan,
        array $sections,
        Request $request,
        array &$paths
    ): void {
        foreach ($sections as $index => $item) {
            $path = null;

            if ($request->hasFile("bagian.$index.gambar")) {
                $path = $request->file(
                    "bagian.$index.gambar"
                )->store('layanan', 'public');

                $paths[] = $path;
            }

            BagianLayanan::create([
                'id_layanan' => $layanan->id,
                'judul_bagian' => $item['judul_bagian'],
                'konten' => $item['konten'],
                'gambar_pendukung' => $path,
                'urutan_bagian' => $index + 1,
                'dibuat_pada' => now(),
            ]);
        }
    }
}
