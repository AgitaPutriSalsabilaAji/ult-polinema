<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\LayananController as UserLayananController;
use App\Http\Controllers\Admin\LayananController as AdminLayananController;
use App\Http\Controllers\User\CekStatusController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\GlobalSearchController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Admin\RatingLayananController;
use App\Http\Controllers\Auth\AdminLoginController;

/*
|--------------------------------------------------------------------------
| Halaman Publik Pengguna
|--------------------------------------------------------------------------
*/

Route::view('/', 'user.beranda')->name('beranda');

Route::get('/layanan', [UserLayananController::class, 'index'])
    ->name('layanan.index');

Route::get('/layanan/{slug}', [UserLayananController::class, 'detail'])
    ->name('layanan.detail');

Route::get('/cek-status', [CekStatusController::class, 'index'])
    ->name('cek-status');

/*
|--------------------------------------------------------------------------
| Autentikasi Admin
|--------------------------------------------------------------------------
*/

Route::get('/login', [AdminLoginController::class, 'showLoginForm'])
    ->middleware('guest:admin')
    ->name('login');

Route::post('/login', [AdminLoginController::class, 'login'])
    ->middleware('guest:admin')
    ->name('login.submit');

Route::post('/logout', [AdminLoginController::class, 'logout'])
    ->middleware('auth:admin')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Halaman Admin — Wajib Login
|--------------------------------------------------------------------------
*/

Route::middleware('auth:admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::view('/', 'admin.dashboard')->name('dashboard');
        Route::get('/search', [GlobalSearchController::class, 'index'])
            ->name('search.index');

        // Pengajuan
        Route::get('/pengajuan', [PengajuanController::class, 'index'])
            ->name('pengajuan.index');

        Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])
            ->name('pengajuan.show');

        Route::post('/pengajuan/{pengajuan}/disposisi', [
            PengajuanController::class,
            'disposisi'
        ])->name('pengajuan.disposisi');

        // Tracking e-ticket
        Route::view('/tracking', 'admin.tracking')->name('tracking');

        // Manajemen layanan admin
        Route::get('/layanan', [AdminLayananController::class, 'index'])
            ->name('layanan.index');

        Route::get('/layanan/tambah', [AdminLayananController::class, 'create'])
            ->name('layanan.create');

        Route::post('/layanan', [AdminLayananController::class, 'store'])
            ->name('layanan.store');

        Route::get('/layanan/{layanan}/edit', [AdminLayananController::class, 'edit'])
            ->name('layanan.edit');

        Route::put('/layanan/{layanan}', [AdminLayananController::class, 'update'])
            ->name('layanan.update');

        Route::patch('/layanan/{layanan}/status', [
            AdminLayananController::class,
            'toggleStatus'
        ])->name('layanan.status');

        Route::delete('/layanan/{layanan}', [
            AdminLayananController::class,
            'destroy'
        ])->name('layanan.destroy');

        Route::get('/layanan/{layanan}', [AdminLayananController::class, 'show'])
            ->name('layanan.show');

        // FAQ
        Route::resource('faq', FaqController::class)
            ->except(['create', 'show', 'edit']);

        // Rating dan ulasan
        Route::get('/rating', [RatingLayananController::class, 'index'])
            ->name('rating');

        Route::post('/rating-ulasan', [RatingLayananController::class, 'store'])
            ->name('rating-ulasan.store');

        // Profil dan daftar admin
        Route::view('/profil', 'admin.profil')->name('profil');

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
