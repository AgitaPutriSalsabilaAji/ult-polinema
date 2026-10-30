<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\RiwayatPengajuan;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LayananController extends Controller
{
    private const CATEGORY_KEYWORDS = [
        'akademik' => ['akademik'],
        'keuangan' => ['keuangan'],
        'kemahasiswaan' => ['kemahasiswaan'],
        'humas' => ['humas', 'kerjasama', 'kerja sama'],
        'kepegawaian' => ['kepegawaian'],
    ];

    private function layananPublik(): Builder
    {
        return Layanan::query()
            ->where('status', 'aktif')
            ->with([
                'unit:id,nama_unit',
                'bagianLayanan' => static function (HasMany $query): void {
                    $query->whereNotNull('gambar_pendukung')
                        ->where('gambar_pendukung', '<>', '')
                        ->select(['id', 'id_layanan', 'gambar_pendukung']);
                },
            ]);
    }

    private function filterKategori(Builder $query, string $kategori): Builder
    {
        if (! isset(self::CATEGORY_KEYWORDS[$kategori])) {
            return $query;
        }

        return $query->whereHas('unit', function (Builder $unitQuery) use ($kategori): void {
            $unitQuery->where(function (Builder $query) use ($kategori): void {
                foreach (self::CATEGORY_KEYWORDS[$kategori] as $keyword) {
                    $query->orWhereRaw('LOWER(nama_unit) LIKE ?', ['%'.mb_strtolower($keyword).'%']);
                }
            });
        });
    }

    private function kategoriUnit(?string $namaUnit): ?string
    {
        if ($namaUnit === null) {
            return null;
        }

        $namaUnit = mb_strtolower($namaUnit);

        foreach (self::CATEGORY_KEYWORDS as $kategori => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($namaUnit, $keyword)) {
                    return $kategori;
                }
            }
        }

        return null;
    }

    /**
     * @return array{id: int, nama: string, slug: string, kategori: ?string, unit: string, deskripsi: string, gambar: ?string}
     */
    private function dataLayananPublik(Layanan $layanan): array
    {
        $gambar = $layanan->bagianLayanan->first()?->gambar_pendukung;

        return [
            'id' => (int) $layanan->getKey(),
            'nama' => $layanan->nama_layanan,
            'slug' => Str::slug($layanan->nama_layanan),
            'kategori' => $this->kategoriUnit($layanan->unit?->nama_unit),
            'unit' => $layanan->unit?->nama_unit ?? 'Unit Layanan',
            'deskripsi' => $layanan->deskripsi_singkat ?? '',
            'gambar' => $gambar ? Storage::disk('public')->url($gambar) : null,
        ];
    }

    public function beranda(Request $request): View
    {
        $kategori = $request->query('kategori', 'humas');
        $kategori = is_string($kategori) ? strtolower($kategori) : 'humas';

        if (! isset(self::CATEGORY_KEYWORDS[$kategori])) {
            $kategori = 'humas';
        }

        $searchServices = $this->layananPublik()
            ->orderBy('nama_layanan')
            ->get()
            ->map(fn (Layanan $layanan): array => $this->dataLayananPublik($layanan));

        $jumlahKategori = array_fill_keys(array_keys(self::CATEGORY_KEYWORDS), 0);
        foreach ($searchServices as $service) {
            if ($service['kategori'] !== null) {
                $jumlahKategori[$service['kategori']]++;
            }
        }

        $services = $searchServices
            ->where('kategori', $kategori)
            ->values();

        return view('user.beranda', compact(
            'services',
            'searchServices',
            'kategori',
            'jumlahKategori'
        ));
    }

    public function index(Request $request): View
    {
        $kategori = $request->query('kategori', 'semua');
        $kategori = is_string($kategori) ? strtolower($kategori) : 'semua';

        if ($kategori !== 'semua' && ! isset(self::CATEGORY_KEYWORDS[$kategori])) {
            $kategori = 'semua';
        }

        $keyword = trim($request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ])['q'] ?? '');

        $jumlahKategori = [
            'semua' => Layanan::query()->where('status', 'aktif')->count(),
        ];
        foreach (array_keys(self::CATEGORY_KEYWORDS) as $key) {
            $jumlahKategori[$key] = $this->filterKategori(
                Layanan::query()->where('status', 'aktif'),
                $key
            )->count();
        }

        $query = $this->filterKategori($this->layananPublik(), $kategori);
        if ($keyword !== '') {
            $query->whereRaw('LOWER(nama_layanan) LIKE ?', ['%'.mb_strtolower($keyword).'%']);
        }

        $services = $query->orderBy('nama_layanan')
            ->paginate(10)
            ->withQueryString();
        $services->setCollection(
            $services->getCollection()
                ->map(fn (Layanan $layanan): array => $this->dataLayananPublik($layanan))
        );

        $totalLayanan = $this->filterKategori(
            Layanan::query()->where('status', 'aktif'),
            $kategori
        )->count();

        $autocompleteServices = $this->filterKategori(
            Layanan::query()->where('status', 'aktif'),
            $kategori
        )->orderBy('nama_layanan')
            ->get(['id', 'nama_layanan'])
            ->map(fn (Layanan $layanan): array => [
                'id' => (int) $layanan->getKey(),
                'slug' => Str::slug($layanan->nama_layanan),
                'nama' => $layanan->nama_layanan,
            ]);

        return view('user.layanan.daftar_layanan', compact(
            'services',
            'kategori',
            'keyword',
            'jumlahKategori',
            'totalLayanan',
            'autocompleteServices'
        ));
    }

    public function detail(string $slug): View
    {
        $slugAliases = [
            'informasi-spmb' => ['spmb'],
            'informasi-pengaduan' => ['ppid'],
        ];
        $matchingSlugs = [$slug, ...($slugAliases[$slug] ?? [])];

        $layananId = ctype_digit($slug)
            ? (int) $slug
            : Layanan::query()
                ->where('status', 'aktif')
                ->get(['id', 'nama_layanan'])
                ->first(fn (Layanan $layanan): bool => in_array(
                    Str::slug($layanan->nama_layanan),
                    $matchingSlugs,
                    true
                ))
                ?->getKey();

        abort_if($layananId === null, 404);

        $layananDatabase = Layanan::query()
            ->where('status', 'aktif')
            ->with([
                'unit',
                'bagianLayanan' => static function (HasMany $query): void {
                    $query->whereNotNull('judul_bagian')
                        ->where('judul_bagian', '<>', '')
                        ->whereNotNull('konten')
                        ->where('konten', '<>', '')
                        ->orderBy('urutan_bagian')
                        ->orderBy('id');
                },
            ])
            ->findOrFail($layananId);

        $bagian = $layananDatabase->bagianLayanan;
        $layanan = [
            'id' => $layananDatabase->getKey(),
            'nama' => $layananDatabase->nama_layanan,
            'slug' => Str::slug($layananDatabase->nama_layanan),
            'deskripsi' => $layananDatabase->deskripsi_singkat ?? '',
            'unit' => $layananDatabase->unit?->nama_unit ?? 'Unit Layanan',
            'estimasi' => $layananDatabase->estimasi_waktu,
            'tarif' => $layananDatabase->tarif,
            'jam' => $layananDatabase->jam_operasional,
        ];

        return view('user.layanan.detail_layanan', compact('layanan', 'bagian'));
    }

    public function formPengajuan(Request $request): View
    {
        $layananList = Layanan::query()
            ->where('status', 'aktif')
            ->with('unit')
            ->orderBy('nama_layanan')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama_layanan,
                    'slug' => Str::slug($item->nama_layanan),
                    'unit' => $item->unit?->nama_unit ?? 'Unit Layanan',
                ];
            });

        $selectedService = $request->query('layanan');
        $selectedLayanan = is_string($selectedService)
            ? $layananList->first(fn (array $layanan): bool => (string) $layanan['id'] === $selectedService
                || $layanan['slug'] === $selectedService)
            : null;
        $selectedLayanan ??= $layananList->first();

        return view('user.form_pengajuan', [
            'layananList' => $layananList,
            'selectedLayanan' => $selectedLayanan,
        ]);
    }

    public function submitPengajuan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:100'],
            'nomor_telepon' => ['required', 'string', 'max:30'],
            'nim_nip' => ['nullable', 'string', 'max:50'],
            'id_layanan' => ['required', 'integer', 'exists:layanan,id'],
            'subjek' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'dokumen' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);

        $layanan = Layanan::findOrFail($data['id_layanan']);

        $pengguna = Pengguna::firstOrNew(['email' => $data['email']]);
        $pengguna->nama_lengkap = $data['nama_lengkap'];
        $pengguna->nim_nip = $data['nim_nip'] ?? $pengguna->nim_nip;
        $pengguna->nomor_telepon = $data['nomor_telepon'];

        if (! $pengguna->exists) {
            $pengguna->kata_sandi = bcrypt(Str::random(20));
        }

        $pengguna->save();

        $duplicatePengajuan = Pengajuan::query()
            ->where('id_pengguna', $pengguna->id)
            ->where('id_layanan', $layanan->id)
            ->where('subjek', $data['subjek'])
            ->where('deskripsi', $data['deskripsi'])
            ->where('status', 'diajukan')
            ->latest('dibuat_pada')
            ->first();

        if ($duplicatePengajuan) {
            return redirect()->route('pengajuan.berhasil', ['nomorTiket' => $duplicatePengajuan->nomor_tiket])
                ->with('success', 'Pengajuan Anda sudah tercatat. Nomor E-Tiket Anda: '.$duplicatePengajuan->nomor_tiket);
        }

        $pengajuanData = [
            'id_pengguna' => $pengguna->id,
            'id_layanan' => $layanan->id,
            'id_admin_fo' => null,
            'subjek' => $data['subjek'],
            'deskripsi' => $data['deskripsi'],
            'status' => 'diajukan',
        ];

        if (Schema::hasColumn('pengajuan', 'id_unit') && ! is_null($layanan->id_unit ?? null)) {
            $pengajuanData['id_unit'] = $layanan->id_unit;
        }

        $pengajuan = DB::transaction(function () use ($pengajuanData) {
            $lastPengajuan = Pengajuan::query()->lockForUpdate()->orderByDesc('id')->first();
            $nextSequence = $lastPengajuan ? (int) $lastPengajuan->id + 1 : 1;

            $pengajuanData['nomor_tiket'] = Pengajuan::generateNomorTiket($nextSequence);

            $pengajuan = Pengajuan::create($pengajuanData);

            RiwayatPengajuan::create([
                'id_pengajuan' => $pengajuan->id,
                'status_sebelumnya' => null,
                'status_baru' => 'diajukan',
                'judul_status' => 'Pengajuan diterima',
                'deskripsi' => 'Pengajuan layanan berhasil dibuat dan menunggu verifikasi admin ULT.',
                'nama_aktor' => 'Sistem',
                'id_admin' => null,
                'dibuat_pada' => now(),
            ]);

            return $pengajuan;
        });

        $nomorTiket = $pengajuan->nomor_tiket;

        if ($request->hasFile('dokumen') && Schema::hasTable('dokumen_pengajuan')) {
            $uploadedFile = $request->file('dokumen');
            $storedPath = $uploadedFile->storeAs(
                'pengajuan',
                time().'_'.$uploadedFile->getClientOriginalName(),
                'public'
            );

            DB::table('dokumen_pengajuan')->insert([
                'id_pengajuan' => $pengajuan->id,
                'nama_file' => $uploadedFile->getClientOriginalName(),
                'path_file' => $storedPath,
                'ukuran_file' => (string) $uploadedFile->getSize(),
                'diunggah_pada' => now(),
            ]);
        }

        return redirect()->route('pengajuan.berhasil', ['nomorTiket' => $nomorTiket])
            ->with('success', 'Pengajuan berhasil dibuat. Nomor E-Tiket Anda: '.$nomorTiket);
    }

    public function detailPengajuan(string $nomorTiket): View
    {
        $pengajuan = Pengajuan::query()
            ->with(['layanan.unit', 'riwayat'])
            ->where('nomor_tiket', $nomorTiket)
            ->firstOrFail();

        $pemohon = Pengguna::find($pengajuan->id_pengguna);

        return view('user.detail_pengajuan', [
            'pengajuan' => $pengajuan,
            'pemohon' => $pemohon,
        ]);
    }

    public function successPengajuan(string $nomorTiket): View
    {
        $pengajuan = Pengajuan::query()
            ->with(['layanan.unit', 'riwayat'])
            ->where('nomor_tiket', $nomorTiket)
            ->firstOrFail();

        $pemohon = Pengguna::find($pengajuan->id_pengguna);

        return view('user.pengajuan_berhasil', [
            'pengajuan' => $pengajuan,
            'pemohon' => $pemohon,
        ]);
    }
}
