<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ProfilController as FrontendProfil;
use App\Http\Controllers\Frontend\LayananController as FrontendLayanan;


Route::get('/', function () {
    return view('frontend.index');
});

Route::prefix('profil')->group(function () {
    Route::get('/visi-misi', [FrontendProfil::class, 'visiMisi'])->name('profil.visi-misi');
    Route::get('/struktur-organisasi', [FrontendProfil::class, 'strukturOrganisasi'])->name('profil.struktur-organisasi');
});

Route::prefix('layanan')->group(function () {
    Route::get('/aplikasi', [FrontendLayanan::class, 'aplikasi'])->name('layanan.aplikasi');
    Route::get('/usulan-kepegawaian', [FrontendLayanan::class, 'usulanKepegawaian'])->name('layanan.usulan-kepegawaian');
});
