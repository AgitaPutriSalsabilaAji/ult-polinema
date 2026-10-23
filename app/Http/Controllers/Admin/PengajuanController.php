<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DisposisiPengajuan;
use App\Models\Pengajuan;
use App\Models\RiwayatPengajuan;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('pengajuan as p')
            ->leftJoin('layanan as l', 'l.id', '=', 'p.id_layanan')
            ->leftJoin('unit as un', 'un.id', '=', 'p.id_unit')
            ->leftJoin('admin as a', 'a.id', '=', 'p.id_admin_fo')
            ->select(
                'p.*',
                'l.nama_layanan',
                'un.nama_unit',
                'a.nama_lengkap_gelar as nama_admin_fo'
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

        $units = Unit::orderBy('nama_unit')->get();

        $adminTujuan = DB::table('admin')
            ->where('peran', 'admin_unit')
            ->select('id', 'nama_lengkap_gelar', 'id_unit')
            ->orderBy('nama_lengkap_gelar')
            ->get();

        return view('admin.pengajuan', compact(
            'pengajuans',
            'units',
            'adminTujuan'
        ));
    }

    public function show(Pengajuan $pengajuan)
    {
        $pengajuan->load([
            'layanan',
            'unit',
            'disposisi.unitTujuan',
            'riwayat',
        ]);

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

    public function disposisi(Request $request, Pengajuan $pengajuan)
    {
        $data = $request->validate([
            'id_unit_tujuan' => [
                'required',
                'integer',
                'exists:unit,id',
            ],
            'id_admin_tujuan' => [
                'required',
                'integer',
                Rule::exists('admin', 'id')
                    ->where(fn ($q) => $q
                        ->where('peran', 'admin_unit')
                        ->where('id_unit', $request->input('id_unit_tujuan')),
                    ),
            ],
            'catatan_disposisi' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        // Sesuaikan ID ini dengan guard autentikasi admin yang digunakan.
        $adminPengirim = auth('admin')->user();

        if (!$adminPengirim) {
            return back()->withErrors([
                'disposisi' => 'Data admin yang login tidak ditemukan.',
            ]);
        }

        if ($adminPengirim->peran !== 'admin_ult') {
            abort(403, 'Hanya Admin ULT yang dapat melakukan disposisi.');
        }

        $adminPengirimId = $adminPengirim->id;

        DB::transaction(function () use (
            $pengajuan,
            $data,
            $adminPengirimId,
            $adminPengirim
        ) {
            $locked = Pengajuan::whereKey($pengajuan->id)
                ->lockForUpdate()
                ->firstOrFail();

            $statusSebelumnya = $locked->status;

            DisposisiPengajuan::create([
                'id_pengajuan' => $locked->id,
                'id_unit_tujuan' => $data['id_unit_tujuan'],
                'id_admin_pengirim' => $adminPengirimId,
                'id_admin_tujuan' => $data['id_admin_tujuan'],
                'catatan_disposisi' => $data['catatan_disposisi'] ?? null,
                'status_disposisi' => 'dikirim',
            ]);

            $locked->update([
                'id_unit' => $data['id_unit_tujuan'],
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
                'id_admin' => $adminPengirimId,
            ]);
        });

        return redirect()
            ->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan berhasil didisposisikan.');
    }
}
