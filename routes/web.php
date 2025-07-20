<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ProfilController as FrontendProfil;

Route::get('/', function () {
    return view('frontend.index');
});

Route::prefix('profil')->group(function () {
    Route::get('/visi-misi', [FrontendProfil::class, 'visiMisi'])->name('profil.visi-misi');
    Route::get('/struktur-organisasi', [FrontendProfil::class, 'strukturOrganisasi'])->name('profil.struktur-organisasi');
});
