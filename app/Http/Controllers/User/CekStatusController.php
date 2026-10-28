<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CekStatusController extends Controller
{
    public function index(Request $request)
    {
        $ticket = strtoupper(trim((string) $request->query('ticket', '')));
        $pengajuan = null;
        $riwayat = collect();
        $pesan = collect();
        $error = null;

        if ($ticket !== '') {
            $pengajuan = DB::table('pengajuan as p')
                ->leftJoin('layanan as l', 'l.id', '=', 'p.id_layanan')
                ->leftJoin('unit as un', 'un.id', '=', 'p.id_unit')
                ->leftJoin('pengguna as u', 'u.id', '=', 'p.id_pengguna')
                ->where('p.nomor_tiket', $ticket)
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

                if (Schema::hasTable('pesan_pengajuan')) {
                    $pesan = DB::table('pesan_pengajuan')
                        ->where('id_pengajuan', $pengajuan->id)
                        ->orderBy('dibuat_pada')
                        ->get();
                }
            } else {
                $error = 'Nomor e-ticket tidak ditemukan. Periksa kembali nomor tiket Anda.';
            }
        }

        return view('user.cek-status', compact(
            'ticket',
            'pengajuan',
            'riwayat',
            'pesan',
            'error'
        ));
    }
}