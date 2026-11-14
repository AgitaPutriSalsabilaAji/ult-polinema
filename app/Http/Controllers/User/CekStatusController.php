<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\Pengguna;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekStatusController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $ticket = strtoupper(trim((string) $request->query('ticket', '')));
        $user = Auth::guard('web')->user();

        if (! $user) {
            return redirect()->guest(route('user.login', ['auth' => 'login']));
        }

        $pemohon = Pengguna::query()->where('email', $user->email)->first();
        $pengajuans = $pemohon
            ? Pengajuan::query()
                ->where('id_pengguna', $pemohon->id)
                ->with([
                    'layanan.unit',
                    'riwayat' => static function ($query): void {
                        $query->orderBy('dibuat_pada')->orderBy('id');
                    },
                ])
                ->orderByDesc('dibuat_pada')
                ->orderByDesc('id')
                ->get()
            : collect();
        $pengajuan = null;
        $riwayat = collect();
        $error = null;

        if ($ticket !== '') {
            if (! Auth::guard('web')->check()) {
                return redirect()->guest(route('user.login', ['auth' => 'login']));
            }

            $user = Auth::guard('web')->user();
            $pemohon = $user ? Pengguna::query()->where('email', $user->email)->first() : null;

            $query = DB::table('pengajuan as p')
                ->leftJoin('layanan as l', 'l.id', '=', 'p.id_layanan')
                ->leftJoin('unit as un', 'un.id', '=', 'l.id_unit')
                ->leftJoin('pengguna as u', 'u.id', '=', 'p.id_pengguna')
                ->where('p.nomor_tiket', $ticket)
                ->when($pemohon, fn ($query) => $query->where('p.id_pengguna', $pemohon->id))
                ->select([
                    'p.id',
                    'p.nomor_tiket',
                    'p.subjek',
                    'p.deskripsi',
                    'p.status',
                    'p.dibuat_pada',
                    'p.diperbarui_pada',
                    'l.nama_layanan',
                    'un.nama_unit',
                    'u.nama_lengkap as nama_pemohon',
                ]);

            if (Schema::hasColumn('pengajuan', 'tanggal_selesai')) {
                $query->addSelect('p.tanggal_selesai');
            } elseif (
                Schema::hasTable('riwayat_pengajuan')
                && Schema::hasColumn('riwayat_pengajuan', 'judul_status')
                && Schema::hasColumn('riwayat_pengajuan', 'dibuat_pada')
            ) {
                $query->addSelect([
                    'tanggal_selesai' => DB::table('riwayat_pengajuan as rp')
                        ->select('rp.dibuat_pada')
                        ->whereColumn('rp.id_pengajuan', 'p.id')
                        ->where('rp.judul_status', 'Pengajuan selesai')
                        ->orderByDesc('rp.dibuat_pada')
                        ->limit(1),
                ]);
            } else {
                $query->addSelect(DB::raw('NULL as tanggal_selesai'));
            }

            $pengajuan = $query->first();

            if ($pengajuan) {
                $riwayat = $pengajuan->riwayat;
            } else {
                $error = 'Nomor E-Tiket tidak ditemukan atau tidak terdaftar pada akun Anda.';
            }
        }

        return view('user.cek-status', compact(
            'ticket',
            'pengajuans',
            'pengajuan',
            'riwayat',
            'error'
        ));
    }
}
