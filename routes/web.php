<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\LayananController as UserLayananController;
use App\Http\Controllers\Admin\LayananController as AdminLayananController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Auth\AdminLoginController;

/*
|--------------------------------------------------------------------------
| Halaman publik pengguna
|--------------------------------------------------------------------------
*/
Route::view('/', 'user.beranda')->name('beranda');
Route::get('/layanan', [UserLayananController::class, 'index'])->name('layanan.index');
Route::get('/layanan/{slug}', [UserLayananController::class, 'detail'])->name('layanan.detail');

/*
|--------------------------------------------------------------------------
| Autentikasi admin
|--------------------------------------------------------------------------
*/
Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->middleware('guest:admin')->name('login');
Route::post('/login', [AdminLoginController::class, 'login'])->middleware('guest:admin')->name('login.submit');
Route::post('/logout', [AdminLoginController::class, 'logout'])->middleware('auth:admin')->name('logout');

/*
|--------------------------------------------------------------------------
| Halaman admin (semua dilindungi auth:admin)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');

    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
    Route::post('/pengajuan/{pengajuan}/disposisi', [PengajuanController::class, 'disposisi'])->name('pengajuan.disposisi');

    Route::get('/tracking', fn () => view('admin.tracking'))->name('tracking');

    Route::get('/layanan', [AdminLayananController::class, 'index'])->name('layanan.index');
    Route::get('/layanan/tambah', [AdminLayananController::class, 'create'])->name('layanan.create');
    Route::post('/layanan', [AdminLayananController::class, 'store'])->name('layanan.store');
    Route::get('/layanan/{layanan}/edit', [AdminLayananController::class, 'edit'])->name('layanan.edit');
    Route::put('/layanan/{layanan}', [AdminLayananController::class, 'update'])->name('layanan.update');
    Route::patch('/layanan/{layanan}/status', [AdminLayananController::class, 'toggleStatus'])->name('layanan.status');
    Route::delete('/layanan/{layanan}', [AdminLayananController::class, 'destroy'])->name('layanan.destroy');
    Route::get('/layanan/{layanan}', [AdminLayananController::class, 'show'])->name('layanan.show');

    Route::resource('faq', FaqController::class)->except(['create','show','edit']);
    Route::view('/rating', 'admin.rating')->name('rating');
    Route::view('/profil', 'admin.profil')->name('profil');
    Route::view('/daftar-admin', 'admin.daftar-admin')->name('daftar-admin');
});
