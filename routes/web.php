<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\CekStatusController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Admin\RatingLayananController;
use App\Http\Controllers\Auth\AdminLoginController;

// Halaman beranda pengguna
Route::get('/', function () {
    return view('user.beranda');
})->name('beranda');

// Cek status e-ticket untuk pengguna
Route::get('/cek-status', [CekStatusController::class, 'index'])
    ->name('cek-status');

// layanan
Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/layanan', function (\Illuminate\Http\Request $request) {
    return view('user.layanan.index', compact('services', 'kategori'));
})->name('layanan.index');


Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::view('/pengajuan', 'admin.pengajuan')->name('pengajuan');
    Route::view('/detail-pengajuan', 'admin.detail-pengajuan')->name('detail-pengajuan');
    Route::view('/tracking', 'admin.tracking')->name('tracking');
    Route::view('/layanan', 'admin.layanan')->name('layanan');
    Route::resource('faq', FaqController::class)->except(['create', 'show', 'edit']);
    // Route::view('/rating', 'admin.rating')->name('rating');
    Route::view('/profil', 'admin.profil')->name('profil');
    Route::view('/daftar-admin', 'admin.daftar-admin')->name('daftar-admin');
});


Route::get('/layanan/{slug}', function (string $slug) {
    return view('user.layanan.detail', compact('layanan', 'terkait'));
})->name('layanan.detail');

// Login admin — harus di luar grup auth:admin
Route::get('/login', [AdminLoginController::class, 'showLoginForm'])
    ->middleware('guest:admin')
    ->name('login');

Route::post('/login', [AdminLoginController::class, 'login'])
    ->middleware('guest:admin')
    ->name('login.submit');

// Logout admin
Route::post('/logout', [AdminLoginController::class, 'logout'])
    ->middleware('auth:admin')
    ->name('logout');

// Semua halaman admin wajib login
Route::middleware('auth:admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::view('/', 'admin.dashboard')->name('dashboard');

        // Pengajuan
        Route::get('/pengajuan', [PengajuanController::class, 'index'])
            ->name('pengajuan.index');

        Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])
            ->name('pengajuan.show');

        Route::post('/pengajuan/{pengajuan}/disposisi', [
            PengajuanController::class, 'disposisi'
        ])->name('pengajuan.disposisi');

        // Tracking E-Ticket
        Route::view('/tracking', 'admin.tracking')->name('tracking');

        // Manajemen Informasi Layanan
        Route::get('/layanan', [LayananController::class, 'index'])
            ->name('layanan.index');

        Route::get('/layanan/tambah', [LayananController::class, 'create'])
            ->name('layanan.create');

        Route::post('/layanan', [LayananController::class, 'store'])
            ->name('layanan.store');

        Route::get('/layanan/{layanan}/edit', [LayananController::class, 'edit'])
            ->name('layanan.edit');

        Route::put('/layanan/{layanan}', [LayananController::class, 'update'])
            ->name('layanan.update');

        Route::patch('/layanan/{layanan}/status', [
            LayananController::class, 'toggleStatus'
        ])->name('layanan.status');

        Route::delete('/layanan/{layanan}', [
            LayananController::class, 'destroy'
        ])->name('layanan.destroy');

        Route::get('/layanan/{layanan}', [LayananController::class, 'show'])
            ->name('layanan.show');

        // FAQ
        Route::resource('faq', FaqController::class)
            ->except(['create', 'show', 'edit']);

// Rating & Ulasan
Route::get('/rating', [RatingLayananController::class, 'index'])
    ->name('rating-ulasan.index');

Route::post('/rating-ulasan', [RatingLayananController::class, 'store'])
    ->name('rating-ulasan.store');

        // Profil dan Akun
        Route::view('/profil', 'admin.profil')->name('profil');

        // Daftar Admin
        Route::view('/daftar-admin', 'admin.daftar-admin')
            ->name('daftar-admin');
    });

    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])
    ->middleware('guest:admin')
    ->name('login');

    Route::post('/login', [AdminLoginController::class, 'login'])
    ->middleware('guest:admin')
    ->name('login.submit');

    Route::post('/logout', [AdminLoginController::class, 'logout'])
    ->middleware('auth:admin')
    ->name('logout');