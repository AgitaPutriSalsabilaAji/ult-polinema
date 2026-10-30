<?php

use App\Http\Controllers\Admin\DaftarAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\GlobalSearchController;
use App\Http\Controllers\Admin\LayananController as AdminLayananController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Admin\RatingLayananController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\User\CekStatusController;
use App\Http\Controllers\User\LayananController as UserLayananController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Publik Pengguna
|--------------------------------------------------------------------------
*/

Route::get('/', [UserLayananController::class, 'beranda'])->name('beranda');

Route::get('/layanan', [UserLayananController::class, 'index'])
    ->name('layanan.index');

Route::get('/layanan/{slug}', [UserLayananController::class, 'detail'])
    ->name('layanan.detail');

Route::middleware('guest:web')->group(function () {
    Route::get('/masuk', [UserAuthController::class, 'showLoginForm'])
        ->name('user.login');

    Route::post('/masuk', [UserAuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('user.login.submit');

    Route::get('/daftar', [UserAuthController::class, 'showRegistrationForm'])
        ->name('user.register');

    Route::post('/daftar', [UserAuthController::class, 'register'])
        ->middleware('throttle:5,1')
        ->name('user.register.submit');
});

Route::post('/keluar', [UserAuthController::class, 'logout'])
    ->middleware('auth:web')
    ->name('user.logout');

Route::get('/cek-status', [CekStatusController::class, 'index'])
    ->name('cek-status');

Route::middleware('auth:web')->group(function () {
    Route::get('/e-tiket/form', [UserLayananController::class, 'formPengajuan'])
        ->name('pengajuan.form');

    Route::post('/e-tiket/form', [UserLayananController::class, 'submitPengajuan'])
        ->name('pengajuan.submit');

    Route::get('/e-tiket/{nomorTiket}/berhasil', [UserLayananController::class, 'successPengajuan'])
        ->name('pengajuan.berhasil');

    Route::get('/e-tiket/{nomorTiket}', [UserLayananController::class, 'detailPengajuan'])
        ->name('pengajuan.detail');
});

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

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/search', [GlobalSearchController::class, 'index'])
            ->name('search.index');

        // Pengajuan
        Route::get('/pengajuan', [PengajuanController::class, 'index'])
            ->name('pengajuan.index');

        Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])
            ->name('pengajuan.show');

        Route::post('/pengajuan/{pengajuan}/disposisi', [
            PengajuanController::class,
            'disposisi',
        ])->name('pengajuan.disposisi');

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
            'toggleStatus',
        ])->name('layanan.status');

        Route::delete('/layanan/{layanan}', [
            AdminLayananController::class,
            'destroy',
        ])->name('layanan.destroy');

        Route::get('/layanan/{layanan}', [AdminLayananController::class, 'show'])
            ->name('layanan.show');

        // FAQ
        Route::resource('faq', FaqController::class)
            ->except(['create', 'show', 'edit']);

        // Rating dan ulasan
        Route::get('/rating', [RatingLayananController::class, 'index'])
            ->name('rating-ulasan.index');

        Route::post('/rating-ulasan', [RatingLayananController::class, 'store'])
            ->name('rating-ulasan.store');

        // Profil & Akun Admin
        Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
        Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');

    // Daftar Admin — Khusus Admin ULT
    Route::middleware('can:akses-daftar-admin')->group(function () {
        Route::resource('daftar-admin', DaftarAdminController::class)
            ->except(['show']);
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
});
