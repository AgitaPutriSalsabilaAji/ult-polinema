<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RatingLayanan;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RatingLayananController extends Controller
{
    /**
     * Menampilkan halaman Rating & Ulasan Layanan ULT.
     */
    public function index(Request $request)
{
    $units = \App\Models\Unit::orderBy('nama_unit')->get();
    
    $query = RatingLayanan::with([
        'pengguna',
        'layanan.unit',
    ]);

    // Filter berdasarkan unit layanan
    if ($request->filled('id_unit')) {
        $query->whereHas('layanan', function ($q) use ($request) {
            $q->where('id_unit', $request->id_unit);
        });
    }

    // Filter berdasarkan jumlah bintang
    if ($request->filled('rating')) {
        $query->where('rating', $request->rating);
    }

    // Statistik mengikuti filter unit
    $statistikQuery = RatingLayanan::query();

    if ($request->filled('id_unit')) {
        $statistikQuery->whereHas('layanan', function ($q) use ($request) {
            $q->where('id_unit', $request->id_unit);
        });
    }

    $totalRating = (clone $statistikQuery)->count();

    $rataRataRating = (clone $statistikQuery)->avg('rating') ?? 0;

    // Data untuk dropdown unit
    $units = Unit::orderBy('nama_unit')->get();

    // Data tabel rating dan ulasan
    $ulasan = $query
        ->orderByDesc('dibuat_pada')
        ->paginate(10)
        ->withQueryString();

    return view('admin.rating', compact(
        'units',
        'ulasan',
        'totalRating',
        'rataRataRating'
    ));
}

    /**
     * Menyimpan rating dan ulasan dari pemohon.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pengajuan' => [
                'required',
                'integer',
                Rule::exists('pengajuan', 'id_pengajuan'),
            ],

            'id_layanan' => [
                'required',
                'integer',
                Rule::exists('layanan', 'id'),
            ],

            'rating' => [
                'required',
                'integer',
                Rule::in([1, 2, 3, 4, 5]),
            ],

            'komentar' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        /*
         * Pastikan route dilindungi middleware auth
         * dan pengguna sudah login.
         *
         * id_pengguna diambil dari akun yang login,
         * bukan dari input form yang dapat dimanipulasi.
         */
        $pengguna = $request->user();

        abort_unless($pengguna, 401);

        /*
         * Validasi kepemilikan pengajuan dan kecocokan
         * layanan harus disesuaikan dengan skema tabel
         * pengajuan yang digunakan proyek.
         */

        RatingLayanan::create([
            'id_pengajuan' => $validated['id_pengajuan'],
            'id_pengguna' => $pengguna->id,
            'id_layanan' => $validated['id_layanan'],
            'rating' => $validated['rating'],
            'komentar' => $validated['komentar'],
            'dibuat_pada' => now(),
        ]);

        return back()->with(
            'success',
            'Rating dan ulasan berhasil dikirim.'
        );
    }
}