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
            $pengajuan = $pengajuans->firstWhere('nomor_tiket', $ticket);

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
