<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AdminUniversitas\DashboardController as AdminUniversitasDashboardController;
use App\Http\Controllers\Backend\AdminUnivUsulan\DashboardController as AdminUnivUsulanDashboardController;
use App\Http\Controllers\Backend\PegawaiUnmul\DashboardController as PegawaiUnmulDashboardController;
use App\Http\Controllers\Backend\AdminFakultas\DashboardController as AdminFakultasDashboardController;
use App\Http\Controllers\Backend\PenilaiUniversitas\DashboardController as PenilaiUniversitasDashboardController;
use App\Http\Controllers\Backend\AdminUnivUsulan\UnitKerjaController as UnitKerjaController;


// ------ RUTE HALAMAN LOGIN ------//
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// ------ RUTE HALAMAN BACKEND ADMIN UNIVERSITAS ------//
Route::prefix('admin-universitas')->name('admin-universitas.')->group(function () {
    Route::get('/dashboard', [AdminUniversitasDashboardController::class, 'index'])->name('dashboard-universitas');
});

// ------ REDIRECT FOR OLD URL FORMAT ------//
Route::prefix('admin-universitas-usulan')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('backend.admin-univ-usulan.dashboard');
    });
    Route::get('/dashboard/masterdata-unitkerja', function () {
        return redirect()->route('backend.admin-univ-usulan.unitkerja.index');
    });
    Route::get('/dashboard/masterdata-unitkerja/create', function () {
        return redirect()->route('backend.admin-univ-usulan.unitkerja.create');
    });
});

// ------ RUTE HALAMAN BACKEND ADMIN UNIVERSITAS USULAN------//
Route::prefix('admin-univ-usulan')->name('backend.admin-univ-usulan.')->group(function () {
    Route::get('/dashboard', [AdminUnivUsulanDashboardController::class, 'index'])->name('dashboard');
    Route::get('/unitkerja', [UnitKerjaController::class, 'index'])->name('unitkerja.index');
    Route::get('/unitkerja/create', [UnitKerjaController::class, 'create'])->name('unitkerja.create');
    Route::post('/unitkerja', [UnitKerjaController::class, 'store'])->name('unitkerja.store');
    Route::get('/unitkerja/{unitKerja}/edit', [UnitKerjaController::class, 'edit'])->name('unitkerja.edit');
    Route::put('/unitkerja/{unitKerja}', [UnitKerjaController::class, 'update'])->name('unitkerja.update');
    Route::delete('/unitkerja/{unitKerja}', [UnitKerjaController::class, 'destroy'])->name('unitkerja.destroy');
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
