<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AdminUniversitas\DashboardController as AdminUniversitasDashboardController;
use App\Http\Controllers\Backend\AdminUnivUsulan\DashboardController as AdminUnivUsulanDashboardController;
use App\Http\Controllers\Backend\PegawaiUnmul\DashboardController as PegawaiUnmulDashboardController;
use App\Http\Controllers\Backend\AdminFakultas\DashboardController as AdminFakultasDashboardController;
use App\Http\Controllers\Backend\PenilaiUniversitas\DashboardController as PenilaiUniversitasDashboardController;


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

// ------ RUTE HALAMAN BACKEND USUL PEGAWAI UNMUL------//
Route::prefix('pegawai-unmul')->name('pegawai-unmul.')->group(function () {
    Route::get('/dashboard', [PegawaiUnmulDashboardController::class, 'index'])->name('dashboard-pegawai-unmul');
});

// ------ RUTE HALAMAN BACKEND ADMIN FAKULTAS ------//
Route::prefix('admin-fakultas')->name('admin-fakultas.')->group(function () {
    Route::get('/dashboard', [AdminFakultasDashboardController::class, 'index'])->name('dashboard-fakultas');
});

// ------ RUTE HALAMAN BACKEND PENILAI UNIVERSITAS ------//
Route::prefix('penilai-universitas')->name('penilai-universitas.')->group(function () {
    Route::get('/dashboard', [PenilaiUniversitasDashboardController::class, 'index'])->name('dashboard-penilai-universitas');
});
