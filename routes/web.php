<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\LayananController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::view('/', 'admin.dashboard')->name('dashboard');

    // Pengajuan
    Route::view('/pengajuan', 'admin.pengajuan')->name('pengajuan');
    Route::view('/detail-pengajuan', 'admin.detail-pengajuan')
        ->name('detail-pengajuan');

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

