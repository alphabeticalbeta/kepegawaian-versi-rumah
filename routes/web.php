<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ProfilController as FrontendProfil;
use App\Http\Controllers\Frontend\LayananController as FrontendLayanan;
use App\Http\Controllers\Backend\AdminUniversitas\DashboardController as AdminUniversitasDashboardController;
use App\Http\Controllers\Backend\AdminUnivUsulan\DashboardController as AdminUnivUsulanDashboardController;


// ------ RUTE HALAMAN FRONTEND ------//
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

Route::view('/blangko-surat', 'frontend.layouts.blangko-surat')->name('blangko.surat');

// ------ RUTE HALAMAN LOGIN ------//
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// ------ RUTE HALAMAN BACKEND ADMIN UNIVERSITAS ------//
Route::prefix('admin-universitas')->name('admin-universitas.')->group(function () {
    Route::get('/dashboard', [AdminUniversitasDashboardController::class, 'index'])->name('dashboard-universitas');
});

// ------ RUTE HALAMAN BACKEND ADMIN UNIVERSITAS USULAN------//
Route::prefix('admin-universitas-usulan')->name('admin-universitas-usulan.')->group(function () {
    Route::get('/dashboard', [AdminUnivUsulanDashboardController::class, 'index'])->name('dashboard-universitas-usulan');
});
