<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FaqController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::view('/pengajuan', 'admin.pengajuan')->name('pengajuan');
    Route::view('/detail-pengajuan', 'admin.detail-pengajuan')->name('detail-pengajuan');
    Route::view('/tracking', 'admin.tracking')->name('tracking');
    Route::view('/layanan', 'admin.layanan')->name('layanan');
    Route::resource('faq', FaqController::class)->except(['create', 'show', 'edit']);
    Route::view('/rating', 'admin.rating')->name('rating');
    Route::view('/profil', 'admin.profil')->name('profil');
    Route::view('/daftar-admin', 'admin.daftar-admin')->name('daftar-admin');
});
