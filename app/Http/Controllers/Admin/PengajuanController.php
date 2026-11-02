<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\DisposisiPengajuan;
use App\Models\Pengajuan;
use App\Models\RiwayatPengajuan;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $adminLogin = auth('admin')->user();

        if (! $adminLogin) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        if (! in_array($adminLogin->peran, ['admin_ult', 'admin_unit'])) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $query = DB::table('pengajuan as p')
            ->leftJoin('layanan as l', 'l.id', '=', 'p.id_layanan')
            ->leftJoin('unit as un', 'un.id', '=', 'p.id_unit')
            ->leftJoin('admin as a', 'a.id', '=', 'p.id_admin_fo')
            ->leftJoin('disposisi_pengajuan as d', function ($join) {
                $join->on('d.id_pengajuan', '=', 'p.id')
                    ->where('d.status_disposisi', 'dikirim');
            })
            ->leftJoin('admin as at', 'at.id', '=', 'd.id_admin_tujuan')
            ->select(
                'p.*',
                'l.nama_layanan',
                'p.id_unit as unit_tujuan_id',
                'un.nama_unit',
                DB::raw('COALESCE(at.nama_lengkap_gelar, a.nama_lengkap_gelar) as nama_admin_fo')
            );

        // Ambil nama pemohon jika tabel pengguna tersedia.
        if (Schema::hasTable('pengguna')) {
            $columns = Schema::getColumnListing('pengguna');

            $nameColumn = collect([
                'nama_lengkap',
                'nama',
                'nama_pengguna',
                'name',
            ])->first(fn ($column) => in_array($column, $columns));

            $nipColumn = collect(['nip', 'nik', 'nomor_induk'])
                ->first(fn ($column) => in_array($column, $columns));

            if ($nameColumn) {
                $query->leftJoin(
                    'pengguna as u',
                    'u.id',
                    '=',
                    'p.id_pengguna'
                );

                $query->addSelect("u.{$nameColumn} as nama_pemohon");

                if ($nipColumn) {
                    $query->addSelect("u.{$nipColumn} as nip_pemohon");
                }
            }
        }

        // Hak akses berdasarkan role admin
        if ($adminLogin->peran === 'admin_unit') {
            $query->where('p.id_unit', $adminLogin->id_unit);
        }

        if ($request->filled('q')) {
            $keyword = $request->input('q');

            $query->where(function ($q) use ($keyword) {
                $q->where('p.nomor_tiket', 'like', "%{$keyword}%")
                    ->orWhere('p.subjek', 'like', "%{$keyword}%");

                if (Schema::hasTable('pengguna')) {
                    $columns = Schema::getColumnListing('pengguna');

                    $nameColumn = collect([
                        'nama_lengkap',
                        'nama',
                        'nama_pengguna',
                        'name',
                    ])->first(fn ($column) => in_array($column, $columns));

                    if ($nameColumn) {
                        $q->orWhere("u.{$nameColumn}", 'like', "%{$keyword}%");
                    }

                    if (in_array('nip', $columns) && $nameColumn) {
                        $q->orWhere('u.nip', 'like', "%{$keyword}%");
                    }
                }
            });
        }

        if ($request->filled('unit')) {
            $query->where('p.id_unit', $request->input('unit'));
        }

        if ($request->filled('status')) {
            $query->where('p.status', $request->input('status'));
        }

        $pengajuans = $query
            ->orderByDesc('p.dibuat_pada')
            ->paginate(5)
            ->withQueryString();

        // Mengatur data berdasarkan role admin
        if ($adminLogin->peran === 'admin_ult') {

            // Admin ULT bisa melihat semua unit dan memilih admin tujuan
            $units = Unit::orderBy('nama_unit')->get();

            $adminTujuan = DB::table('admin')
                ->where('peran', 'admin_unit')
                ->select('id', 'nama_lengkap_gelar', 'peran', 'id_unit')
                ->orderBy('nama_lengkap_gelar')
                ->get();

        } else {

            // Admin Unit hanya mendapatkan unit miliknya
            $units = Unit::where('id', $adminLogin->id_unit)->get();

            $adminTujuan = collect();
        }

        $isAdminUlt = $adminLogin->peran === 'admin_ult';
        $adminTujuanByUnit = $adminTujuan
            ->groupBy('id_unit')
            ->map(fn ($admins) => $admins->values());

        return view('admin.pengajuan', compact(
            'pengajuans',
            'units',
            'adminTujuan',
            'adminTujuanByUnit',
            'isAdminUlt'
        ));
    }

    public function show(Pengajuan $pengajuan)
    {
        $adminLogin = auth('admin')->user();

        if (! $adminLogin) {
            abort(401);
        }

        if (! in_array($adminLogin->peran, ['admin_ult', 'admin_unit'])) {
            abort(403);
        }

        $pengajuan->load([
            'layanan.unit',
            'disposisi.unitTujuan',
            'disposisi.adminTujuan',
            'riwayat',
        ]);

        if ($adminLogin->peran === 'admin_unit'
            && (int) $pengajuan->id_unit !== (int) $adminLogin->id_unit) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        if ($adminLogin->peran === 'admin_unit'
            && $pengajuan->status === 'didisposisikan') {
            $this->transitionStatus(
                $pengajuan,
                'diproses',
                $adminLogin,
                'Pengajuan mulai diproses oleh admin unit.'
            );
            $pengajuan->refresh();
            $pengajuan->load([
                'layanan.unit',
                'disposisi.unitTujuan',
                'disposisi.adminTujuan',
                'riwayat',
            ]);
        }

        $pemohon = null;

        if (Schema::hasTable('pengguna')) {
            $pemohon = DB::table('pengguna')
                ->where('id', $pengajuan->id_pengguna)
                ->first();
        }

        return view('admin.detail-pengajuan', compact(
            'pengajuan',
            'pemohon'
        ));
    }

    public function selesai(Pengajuan $pengajuan): RedirectResponse
    {
        $admin = $this->authorizeUnitAdmin($pengajuan);

        $this->transitionStatus(
            $pengajuan,
            'selesai',
            $admin,
            'Pengajuan diselesaikan oleh admin unit.'
        );

        return redirect()
            ->route('admin.pengajuan.show', $pengajuan)
            ->with('success', 'Pengajuan berhasil diselesaikan.');
    }

    public function tolak(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        $data = $request->validate([
            'alasan' => ['nullable', 'string', 'max:5000'],
        ]);
        $admin = $this->authorizeUnitAdmin($pengajuan);
        $description = $data['alasan'] ?: 'Pengajuan ditolak oleh admin unit.';

        $this->transitionStatus($pengajuan, 'ditolak', $admin, $description);

        return redirect()
            ->route('admin.pengajuan.show', $pengajuan)
            ->with('success', 'Pengajuan berhasil ditolak.');
    }

    private function authorizeUnitAdmin(Pengajuan $pengajuan): Admin
    {
        $admin = auth('admin')->user();

        if (! $admin || $admin->peran !== 'admin_unit') {
            abort(403, 'Hanya admin unit yang dapat melakukan tindakan ini.');
        }

        if ((int) $pengajuan->id_unit !== (int) $admin->id_unit) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        if ($pengajuan->status !== 'diproses') {
            abort(409, 'Pengajuan harus berstatus diproses sebelum tindakan ini dilakukan.');
        }

        return $admin;
    }

    private function transitionStatus(
        Pengajuan $pengajuan,
        string $newStatus,
        Admin $admin,
        string $description
    ): void {
        DB::transaction(function () use ($pengajuan, $newStatus, $admin, $description): void {
            $locked = Pengajuan::query()
                ->whereKey($pengajuan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status === $newStatus) {
                return;
            }

            $allowedTransitions = [
                'didisposisikan' => ['diproses'],
                'diproses' => ['selesai', 'ditolak'],
            ];

            if (! in_array($newStatus, $allowedTransitions[$locked->status] ?? [], true)) {
                abort(409, 'Perubahan status pengajuan tidak valid.');
            }

            $previousStatus = $locked->status;
            $attributes = ['status' => $newStatus];

            if ($newStatus === 'selesai') {
                $attributes['tanggal_selesai'] = now();
            }

            $locked->update($attributes);

            RiwayatPengajuan::create([
                'id_pengajuan' => $locked->id,
                'status_sebelumnya' => $previousStatus,
                'status_baru' => $newStatus,
                'judul_status' => 'Pengajuan '.strtolower($newStatus),
                'deskripsi' => $description,
                'nama_aktor' => $admin->nama_lengkap_gelar,
                'id_admin' => $admin->id,
            ]);
        });
    }

    public function disposisi(Request $request, Pengajuan $pengajuan)
    {
        $data = $request->validate([
            'id_admin_tujuan' => [
                'required',
                'integer',
            ],
            'catatan_disposisi' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $adminPengirim = auth('admin')->user();

        if (! $adminPengirim) {
            return back()->withErrors([
                'disposisi' => 'Data admin yang login tidak ditemukan.',
            ]);
        }

        if ($adminPengirim->peran !== 'admin_ult') {
            abort(403, 'Hanya Admin ULT yang dapat melakukan disposisi.');
        }

        DB::transaction(function () use ($pengajuan, $data, $adminPengirim) {
            $locked = Pengajuan::whereKey($pengajuan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($locked->status, ['baru', 'diajukan'], true)) {
                abort(409, 'Pengajuan ini sudah tidak dapat didisposisikan.');
            }

            $unitTujuanId = DB::table('layanan')
                ->where('id', $locked->id_layanan)
                ->value('id_unit');

            if (! $unitTujuanId) {
                abort(422, 'Unit tujuan pengajuan tidak ditemukan.');
            }

            $adminTujuan = DB::table('admin')
                ->where('id', $data['id_admin_tujuan'])
                ->where('peran', 'admin_unit')
                ->where('id_unit', $unitTujuanId)
                ->lockForUpdate()
                ->first();

            if (! $adminTujuan) {
                abort(422, 'Admin tujuan tidak aktif atau bukan bagian dari unit pengajuan.');
            }

            if (DB::table('disposisi_pengajuan')
                ->where('id_pengajuan', $locked->id)
                ->where('status_disposisi', 'dikirim')
                ->exists()) {
                abort(409, 'Pengajuan ini sudah memiliki disposisi awal.');
            }

            $statusSebelumnya = $locked->status;

            DisposisiPengajuan::create([
                'id_pengajuan' => $locked->id,
                'id_unit_tujuan' => $unitTujuanId,
                'id_admin_pengirim' => $adminPengirim->id,
                'id_admin_tujuan' => $data['id_admin_tujuan'],
                'catatan_disposisi' => $data['catatan_disposisi'] ?? null,
                'status_disposisi' => 'dikirim',
            ]);

            if (Schema::hasColumn('pengajuan', 'id_unit')) {
                $locked->update([
                    'id_unit' => $unitTujuanId,
                ]);
            }

            $locked->update([
                'status' => 'didisposisikan',
            ]);

            RiwayatPengajuan::create([
                'id_pengajuan' => $locked->id,
                'status_sebelumnya' => $statusSebelumnya,
                'status_baru' => 'didisposisikan',
                'judul_status' => 'Pengajuan didisposisikan',
                'deskripsi' => $data['catatan_disposisi']
                    ?? 'Pengajuan diteruskan ke unit tujuan.',
                'nama_aktor' => $adminPengirim->nama_lengkap_gelar
                    ?? 'Admin ULT',
                'id_admin' => $adminPengirim->id,
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pengajuan berhasil didisposisikan.',
            ]);
        }

        return redirect()
            ->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan berhasil didisposisikan.');
    }
}
