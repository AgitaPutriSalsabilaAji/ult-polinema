<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CekStatusController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $ticket = strtoupper(trim((string) $request->query('ticket', '')));
        $pengajuan = null;
        $riwayat = collect();
        $error = null;

        if ($ticket !== '') {
            if (! Auth::guard('web')->check()) {
                return redirect()->guest(route('user.login'));
            }

            $user = Auth::guard('web')->user();
            $pemohon = $user ? Pengguna::query()->where('email', $user->email)->first() : null;

            $pengajuan = DB::table('pengajuan as p')
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
                    'p.tanggal_selesai',
                    'l.nama_layanan',
                    'un.nama_unit',
                    'u.nama_lengkap as nama_pemohon',
                ])
                ->first();

            if ($pengajuan) {
                $riwayat = DB::table('riwayat_pengajuan')
                    ->where('id_pengajuan', $pengajuan->id)
                    ->orderBy('dibuat_pada')
                    ->get();
            } else {
                $error = 'Nomor e-ticket tidak ditemukan. Periksa kembali nomor tiket Anda.';
            }
        }

        return view('user.cek-status', compact(
            'ticket',
            'pengajuan',
            'riwayat',
            'error'
        ));
    }
}
