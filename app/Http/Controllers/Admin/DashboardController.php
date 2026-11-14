<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\RatingLayanan;
use App\Models\Unit;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $jumlahFaq = Faq::count();
        $jumlahLayanan = Layanan::count();
        $jumlahUnit = Unit::count();

        $statistikPengajuan = Pengajuan::query()
            ->selectRaw('COUNT(*) AS total_pengajuan')
            ->selectRaw(
                'SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) AS pengajuan_baru',
                ['baru', 'diajukan']
            )
            ->selectRaw(
                'SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) AS pengajuan_diproses',
                ['didisposisikan', 'diproses']
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS pengajuan_selesai',
                ['selesai']
            )
            ->selectRaw(
                'SUM(CASE WHEN status NOT IN (?, ?) THEN 1 ELSE 0 END) AS eticket_aktif',
                Pengajuan::TERMINAL_STATUSES
            )
            ->first();

        $statistikRating = RatingLayanan::query()
            ->selectRaw('COUNT(*) AS jumlah_ulasan')
            ->selectRaw('AVG(rating) AS rata_rata_rating')
            ->first();

        return view('admin.dashboard', compact(
            'jumlahFaq',
            'jumlahLayanan',
            'jumlahUnit',
            'statistikPengajuan',
            'statistikRating'
        ));
    }
}
