<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use App\Models\RatingLayanan;
use App\Models\RiwayatPengajuan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

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

    private function formatUkuranFile(?string $ukuran): string
    {
        if ($ukuran === null || $ukuran === '') {
            return 'Ukuran tidak tersedia';
        }

        $bytes = (float) $ukuran;
        if ($bytes <= 0) {
            return '0 KB';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return sprintf('%s %s', round($bytes, 2), $units[$index]);
    }

    private function renderAttachmentError(string $message, int $status = 404)
    {
        return response()->view('user.pengajuan_attachment_error', [
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    private function authorizeDocumentAccess(Pengajuan $pengajuan): void
    {
        if (auth()->getDefaultDriver() === 'admin') {
            $admin = auth('admin')->user();
            abort_unless($admin instanceof Admin, 404);

            $isAdminUlt = $admin->peran === 'admin_ult';
            $pengajuanUnitId = null;

            if (Schema::hasColumn('pengajuan', 'id_unit')) {
                $pengajuanUnitId = $pengajuan->id_unit;
            } elseif (Schema::hasColumn('layanan', 'id_unit')) {
                $pengajuanUnitId = $pengajuan->layanan()->value('id_unit');
            }

            $isAdminUnit = $admin->peran === 'admin_unit'
                && $pengajuanUnitId !== null
                && (int) $pengajuanUnitId === (int) $admin->id_unit;

            abort_unless($isAdminUlt || $isAdminUnit, 404);

            return;
        }

        $user = auth('web')->user();
        abort_unless($user instanceof User, 404);
        $isOwner = (int) $pengajuan->user_id === (int) $user->getKey();

        if (! $isOwner && Schema::hasTable('pengguna')) {
            $isOwner = $pengajuan->pengguna()
                ->where('email', $user->email)
                ->exists();
        }

        abort_unless($isOwner, 404);
    }

    private function documentStorageDisk(string $path): ?string
    {
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return $disk;
            }
        }

        return null;
    }

    private function detectStoredFileMimeType(string $disk, string $path): string
    {
        $file = Storage::disk($disk)->readStream($path);

        if (! is_resource($file)) {
            throw new \RuntimeException('Dokumen tidak dapat dibaca dari storage.');
        }

        try {
            $fileContents = fread($file, 8192);
        } finally {
            fclose($file);
        }

        if (! is_string($fileContents)) {
            throw new \RuntimeException('Tipe MIME dokumen tidak dapat dideteksi.');
        }

        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer($fileContents);

        if (! is_string($mimeType)) {
            throw new \RuntimeException('Tipe MIME dokumen tidak dapat dideteksi.');
        }

        return $mimeType;
    }

    private function serveImageDocument(string $disk, string $path, string $fileName): Response
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];
        $mimeType = $this->detectStoredFileMimeType($disk, $path);

        abort_unless(isset($mimeTypes[$extension]) && $mimeTypes[$extension] === $mimeType, 415);

        return Storage::disk($disk)->response(
            $path,
            $fileName,
            [
                'Content-Type' => $mimeType,
                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline'
        );
    }

    private function renderDocumentResponse(
        string $disk,
        string $path,
        string $fileName,
        string $downloadUrl,
        string $imageUrl
    ): Response {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $mimeType = $this->detectStoredFileMimeType($disk, $path);

        $supportedMimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        if (isset($supportedMimeTypes[$extension]) && $mimeType === $supportedMimeTypes[$extension]) {
            if (str_starts_with($mimeType, 'image/')) {
                return response()->view('user.pengajuan_image_preview', [
                    'fileName' => $fileName,
                    'imageUrl' => $imageUrl,
                    'downloadUrl' => $downloadUrl,
                ]);
            }

            return Storage::disk($disk)->response(
                $path,
                $fileName,
                [
                    'Content-Type' => $mimeType,
                    'X-Content-Type-Options' => 'nosniff',
                ],
                'inline'
            );
        }

        $fileType = $extension !== '' ? strtoupper($extension) : 'FILE';
        $message = in_array($extension, ['doc', 'docx', 'xls', 'xlsx'], true)
            ? "Browser belum mendukung preview langsung untuk file {$fileType}. Unduh file untuk membukanya."
            : "Format {$fileType} belum mendukung preview di browser. Unduh file untuk membukanya.";

        return response()->view('user.pengajuan_document_preview', [
            'fileName' => $fileName,
            'fileType' => $fileType,
            'mimeType' => $mimeType,
            'message' => $message,
            'downloadUrl' => $downloadUrl,
        ]);
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
            'user' => $request->user(),
        ]);
    }

    public function submitPengajuan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'nomor_telepon' => ['required', 'string', 'max:30'],
            'nim_nip' => ['nullable', 'string', 'max:50'],
            'jenis_pemohon' => ['required', 'string', 'max:50'],
            'id_layanan' => ['required', 'integer', 'exists:layanan,id'],
            'subjek' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'dokumen' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'dokumen.mimes' => 'Format file tidak didukung. Silakan upload PDF, JPG, JPEG, atau PNG.',
            'dokumen.extensions' => 'Format file tidak didukung. Silakan upload PDF, JPG, JPEG, atau PNG.',
            'dokumen.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $layanan = Layanan::query()
            ->where('status', 'aktif')
            ->findOrFail($data['id_layanan']);

        $pengguna = Pengguna::firstOrNew(['email' => $user->email]);
        $pengguna->nama_lengkap = $user->name;
        $pengguna->nim_nip = $data['nim_nip'] ?? $pengguna->nim_nip;
        $pengguna->nomor_telepon = $data['nomor_telepon'];

        if (! $pengguna->exists) {
            $pengguna->kata_sandi = $user->getAuthPassword();
        }

        $pengguna->save();

        $duplicateQuery = Pengajuan::query()
            ->where('id_pengguna', $pengguna->id)
            ->where('id_layanan', $layanan->id)
            ->where('subjek', $data['subjek'])
            ->where('deskripsi', $data['deskripsi'])
            ->whereIn('status', ['baru', 'diajukan'])
            ->latest('dibuat_pada');

        $duplicatePengajuan = $duplicateQuery->first();

        if ($duplicatePengajuan) {
            return redirect()->route('pengajuan.berhasil', ['nomorTiket' => $duplicatePengajuan->nomor_tiket])
                ->with('success', 'Pengajuan Anda sudah tercatat. Nomor E-Tiket Anda: '.$duplicatePengajuan->nomor_tiket);
        }

        $pengajuanData = [
            'id_pengguna' => $pengguna->id,
            'id_layanan' => $layanan->id,
            'subjek' => $data['subjek'],
            'deskripsi' => $data['deskripsi'],
            'status' => 'baru',
        ];

        if (Schema::hasColumn('pengajuan', 'user_id')) {
            $pengajuanData['user_id'] = $user->getKey();
        }

        if (Schema::hasColumn('pengajuan', 'id_admin_fo')) {
            $pengajuanData['id_admin_fo'] = null;
        }

        if (Schema::hasColumn('pengajuan', 'jenis_pemohon')) {
            $pengajuanData['jenis_pemohon'] = $data['jenis_pemohon'];
        }

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
                'status_baru' => 'baru',
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
            $storedPath = $uploadedFile->store('pengajuan', 'local');

            if ($storedPath === false) {
                throw new \RuntimeException('Dokumen pengajuan gagal disimpan.');
            }

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

    public function detailPengajuan(Request $request, string $nomorTiket): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $pemohon = Pengguna::query()->where('email', $user->email)->firstOrFail();

        $pengajuan = Pengajuan::query()
            ->where('id_pengguna', $pemohon->id)
            ->with(['layanan.unit', 'riwayat'])
            ->where('nomor_tiket', $nomorTiket)
            ->firstOrFail();

        $pemohon = Pengguna::find($pengajuan->id_pengguna);

        $dokumen = Schema::hasTable('dokumen_pengajuan')
            ? DB::table('dokumen_pengajuan')
                ->where('id_pengajuan', $pengajuan->id)
                ->orderByDesc('diunggah_pada')
                ->get()
                ->map(function ($document) {
                    $document->ukuran_label = $this->formatUkuranFile($document->ukuran_file ?? null);

                    return $document;
                })
            : collect();

        $komunikasi = collect();

        foreach ($pengajuan->riwayat as $history) {
            $historyDate = $history->dibuat_pada instanceof Carbon
                ? $history->dibuat_pada
                : (filled($history->dibuat_pada) ? Carbon::parse($history->dibuat_pada) : null);

            $komunikasi->push((object) [
                'type' => 'status',
                'judul' => $history->judul_status,
                'deskripsi' => $history->deskripsi,
                'dibuat_pada' => $historyDate,
            ]);
        }

        if (Schema::hasTable('pesan_pengajuan')) {
            $pesan = DB::table('pesan_pengajuan')
                ->where('id_pengajuan', $pengajuan->id)
                ->orderBy('dibuat_pada')
                ->get();

            foreach ($pesan as $item) {
                $historyDate = $item->dibuat_pada instanceof Carbon
                    ? $item->dibuat_pada
                    : (filled($item->dibuat_pada) ? Carbon::parse($item->dibuat_pada) : null);

                $lampiranPath = $item->lampiran ?? null;
                $komunikasi->push((object) [
                    'id' => $item->id,
                    'type' => 'message',
                    'pesan' => $item->pesan,
                    'dibuat_pada' => $historyDate,
                    'nama_pengirim' => $item->pengirim_tipe === 'admin'
                        ? 'Petugas ULT'
                        : ($pemohon?->nama_lengkap ?? 'Pemohon'),
                    'pengirim_tipe' => $item->pengirim_tipe,
                    'lampiran_route' => ! empty($lampiranPath)
                        ? route('pengajuan.pesan.lampiran.view', [
                            'nomorTiket' => $nomorTiket,
                            'pesanId' => $item->id,
                        ])
                        : null,
                ]);
            }
        }

        $komunikasi = $komunikasi
            ->sortBy(fn ($item) => $item->dibuat_pada instanceof Carbon
                ? $item->dibuat_pada->getTimestamp()
                : 0)
            ->values();

        $jenisPemohon = Schema::hasColumn('pengajuan', 'jenis_pemohon')
            ? ($pengajuan->jenis_pemohon ?? 'Tidak tersedia')
            : 'Tidak tersedia';
        $ratingUlasan = RatingLayanan::query()
            ->where('id_pengajuan', $pengajuan->id)
            ->exists();

        return view('user.detail_pengajuan', [
            'pengajuan' => $pengajuan,
            'pemohon' => $pemohon,
            'dokumen' => $dokumen,
            'komunikasi' => $komunikasi,
            'jenisPemohon' => $jenisPemohon,
            'ratingUlasan' => $ratingUlasan,
        ]);
    }

    public function storePesan(Request $request, string $nomorTiket): RedirectResponse
    {
        $pengajuan = Pengajuan::query()
            ->where('nomor_tiket', $nomorTiket)
            ->firstOrFail();

        $data = $request->validate([
            'pesan' => ['required', 'string', 'max:5000'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);

        $storedPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiran = $request->file('lampiran');
            $storedPath = $lampiran->storeAs(
                'pengajuan/pesan',
                time().'_'.$lampiran->getClientOriginalName(),
                'public'
            );
        }

        if (Schema::hasTable('pesan_pengajuan')) {
            DB::table('pesan_pengajuan')->insert([
                'id_pengajuan' => $pengajuan->id,
                'pengirim_tipe' => 'pemohon',
                'id_pengirim' => $pengajuan->id_pengguna,
                'pesan' => trim($data['pesan']),
                'lampiran' => $storedPath,
                'dibuat_pada' => now(),
            ]);
        }

        return redirect()->route('pengajuan.detail', ['nomorTiket' => $nomorTiket])
            ->with('success', 'Pesan berhasil dikirim.');
    }

    public function lihatLampiranPesan(string $nomorTiket, int $pesanId)
    {
        $pengajuan = Pengajuan::query()
            ->where('nomor_tiket', $nomorTiket)
            ->first();

        if (! $pengajuan) {
            return $this->renderAttachmentError('Nomor tiket tidak valid.', 404);
        }

        $this->authorizeDocumentAccess($pengajuan);

        if (! Schema::hasTable('pesan_pengajuan')) {
            return $this->renderAttachmentError('Dokumen tidak ditemukan.', 404);
        }

        $pesan = DB::table('pesan_pengajuan')
            ->where('id', $pesanId)
            ->where('id_pengajuan', $pengajuan->id)
            ->first();

        if (! $pesan || empty($pesan->lampiran)) {
            return $this->renderAttachmentError('Dokumen tidak ditemukan.', 404);
        }

        $filePath = $pesan->lampiran;
        if (! Storage::disk('public')->exists($filePath)) {
            return $this->renderAttachmentError('Dokumen tidak ditemukan.', 404);
        }

        return $this->renderDocumentResponse(
            'public',
            $filePath,
            basename($filePath),
            route('pengajuan.pesan.lampiran.download', [
                'nomorTiket' => $nomorTiket,
                'pesanId' => $pesanId,
            ]),
            route('pengajuan.pesan.lampiran.image', [
                'nomorTiket' => $nomorTiket,
                'pesanId' => $pesanId,
            ])
        );
    }

    public function lihatGambarLampiranPesan(string $nomorTiket, int $pesanId): Response
    {
        $pengajuan = Pengajuan::query()
            ->where('nomor_tiket', $nomorTiket)
            ->first();

        if (! $pengajuan) {
            return $this->renderAttachmentError('Nomor tiket tidak valid.', 404);
        }

        $this->authorizeDocumentAccess($pengajuan);

        $pesan = Schema::hasTable('pesan_pengajuan')
            ? DB::table('pesan_pengajuan')
                ->where('id', $pesanId)
                ->where('id_pengajuan', $pengajuan->id)
                ->first()
            : null;

        if (! $pesan || empty($pesan->lampiran) || ! Storage::disk('public')->exists($pesan->lampiran)) {
            return $this->renderAttachmentError('Dokumen tidak ditemukan.', 404);
        }

        return $this->serveImageDocument('public', $pesan->lampiran, basename($pesan->lampiran));
    }

    public function lihatDokumen(string $nomorTiket, int $dokumenId)
    {
        $pengajuan = Pengajuan::query()
            ->where('nomor_tiket', $nomorTiket)
            ->first();

        if (! $pengajuan) {
            return $this->renderAttachmentError('Nomor tiket tidak valid.', 404);
        }

        $this->authorizeDocumentAccess($pengajuan);

        if (! Schema::hasTable('dokumen_pengajuan')) {
            return $this->renderAttachmentError('Dokumen tidak ditemukan.', 404);
        }

        $dokumen = DB::table('dokumen_pengajuan')
            ->where('id', $dokumenId)
            ->where('id_pengajuan', $pengajuan->id)
            ->first();

        if (! $dokumen || empty($dokumen->path_file)) {
            return $this->renderAttachmentError('Dokumen tidak ditemukan.', 404);
        }

        $path = $dokumen->path_file;
        $disk = $this->documentStorageDisk($path);

        if ($disk === null) {
            return $this->renderAttachmentError('Dokumen tidak ditemukan.', 404);
        }

        return $this->renderDocumentResponse(
            $disk,
            $path,
            $dokumen->nama_file,
            route('pengajuan.dokumen.download', [
                'nomorTiket' => $nomorTiket,
                'dokumenId' => $dokumenId,
            ]),
            route('pengajuan.dokumen.image', [
                'nomorTiket' => $nomorTiket,
                'dokumenId' => $dokumenId,
            ])
        );
    }

    public function lihatGambarDokumen(string $nomorTiket, int $dokumenId): Response
    {
        $pengajuan = Pengajuan::query()
            ->where('nomor_tiket', $nomorTiket)
            ->first();

        if (! $pengajuan) {
            return $this->renderAttachmentError('Nomor tiket tidak valid.', 404);
        }

        $this->authorizeDocumentAccess($pengajuan);

        $dokumen = Schema::hasTable('dokumen_pengajuan')
            ? DB::table('dokumen_pengajuan')
                ->where('id', $dokumenId)
                ->where('id_pengajuan', $pengajuan->id)
                ->first()
            : null;

        if (! $dokumen || empty($dokumen->path_file)) {
            return $this->renderAttachmentError('Dokumen tidak ditemukan.', 404);
        }

        $disk = $this->documentStorageDisk($dokumen->path_file);

        if ($disk === null) {
            return $this->renderAttachmentError('Dokumen tidak ditemukan.', 404);
        }

        return $this->serveImageDocument($disk, $dokumen->path_file, $dokumen->nama_file);
    }

    public function unduhLampiranPesan(string $nomorTiket, int $pesanId)
    {
        $pengajuan = Pengajuan::query()
            ->where('nomor_tiket', $nomorTiket)
            ->first();

        if (! $pengajuan) {
            return $this->renderAttachmentError('Nomor tiket tidak valid.', 404);
        }

        $this->authorizeDocumentAccess($pengajuan);

        if (! Schema::hasTable('pesan_pengajuan')) {
            return $this->renderAttachmentError('Dokumen tidak ditemukan.', 404);
        }

        $pesan = DB::table('pesan_pengajuan')
            ->where('id', $pesanId)
            ->where('id_pengajuan', $pengajuan->id)
            ->first();

        if (! $pesan || empty($pesan->lampiran) || ! Storage::disk('public')->exists($pesan->lampiran)) {
            return $this->renderAttachmentError('Dokumen tidak ditemukan.', 404);
        }

        return Storage::disk('public')->download(
            $pesan->lampiran,
            basename($pesan->lampiran)
        );
    }

    public function unduhDokumen(string $nomorTiket, int $dokumenId)
    {
        $pengajuan = Pengajuan::query()
            ->where('nomor_tiket', $nomorTiket)
            ->first();

        if (! $pengajuan) {
            return $this->renderAttachmentError('Nomor tiket tidak valid.', 404);
        }

        $this->authorizeDocumentAccess($pengajuan);

        if (! Schema::hasTable('dokumen_pengajuan')) {
            return $this->renderAttachmentError('Dokumen pendukung tidak ditemukan.', 404);
        }

        $dokumen = DB::table('dokumen_pengajuan')
            ->where('id', $dokumenId)
            ->where('id_pengajuan', $pengajuan->id)
            ->first();

        if (! $dokumen || empty($dokumen->path_file)) {
            return $this->renderAttachmentError('Dokumen pendukung tidak ditemukan.', 404);
        }

        $disk = $this->documentStorageDisk($dokumen->path_file);

        if ($disk === null) {
            return $this->renderAttachmentError('Dokumen pendukung tidak tersedia atau file sudah dihapus.', 404);
        }

        return Storage::disk($disk)->download(
            $dokumen->path_file,
            $dokumen->nama_file
        );
    }

    public function storeRating(Request $request, string $nomorTiket): RedirectResponse
    {
        $pengajuan = Pengajuan::query()
            ->where('nomor_tiket', $nomorTiket)
            ->firstOrFail();

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'komentar' => ['nullable', 'string', 'max:2000'],
        ]);

        RatingLayanan::query()->updateOrCreate(
            [
                'id_pengajuan' => $pengajuan->id,
                'id_pengguna' => $pengajuan->id_pengguna,
            ],
            [
                'id_layanan' => $pengajuan->id_layanan,
                'rating' => $data['rating'],
                'komentar' => $data['komentar'] ?? null,
                'dibuat_pada' => now(),
            ]
        );

        return redirect()->route('pengajuan.detail', ['nomorTiket' => $nomorTiket])
            ->with('success', 'Terima kasih atas penilaian Anda.');
    }

    public function successPengajuan(Request $request, string $nomorTiket): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $pemohon = Pengguna::query()->where('email', $user->email)->firstOrFail();

        $pengajuan = Pengajuan::query()
            ->where('id_pengguna', $pemohon->id)
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
