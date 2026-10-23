<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Auth\AdminLoginController;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Halaman beranda pengguna
Route::get('/', function () {
    return view('user.beranda');
})->name('beranda');

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

        // Rating Layanan
        Route::view('/rating', 'admin.rating')->name('rating');

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