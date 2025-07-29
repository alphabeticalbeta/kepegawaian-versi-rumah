<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AdminUniversitas\DashboardController as AdminUniversitasDashboardController;
use App\Http\Controllers\Backend\AdminUnivUsulan\DashboardController as AdminUnivUsulanDashboardController;
use App\Http\Controllers\Backend\PegawaiUnmul\DashboardController as PegawaiUnmulDashboardController;
use App\Http\Controllers\Backend\AdminFakultas\DashboardController as AdminFakultasDashboardController;
use App\Http\Controllers\Backend\PenilaiUniversitas\DashboardController as PenilaiUniversitasDashboardController;
use App\Http\Controllers\Backend\AdminUnivUsulan\DataPegawaiController as DataPegawaiController;
use App\Http\Controllers\Backend\AdminUnivUsulan\UnitKerjaController as UnitKerjaController;
use App\Http\Controllers\Backend\AdminUnivUsulan\SubUnitKerjaController as SubUnitKerjaController;
use App\Http\Controllers\Backend\AdminUnivUsulan\SubSubUnitKerjaController as SubSubUnitKerjaController;
use App\Http\Controllers\Backend\AdminUnivUsulan\PangkatController as PangkatController;
use App\Http\Controllers\Backend\AdminUnivUsulan\JabatanController as JabatanController;


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

    // Data Pegawai Routes
    Route::get('/data-pegawai', [DataPegawaiController::class, 'index'])->name('data-pegawai.index');
    Route::get('/data-pegawai/create', [DataPegawaiController::class, 'create'])->name('data-pegawai.create');
    Route::post('/data-pegawai', [DataPegawaiController::class, 'store'])->name('data-pegawai.store');
    Route::get('/data-pegawai/{pegawai}/edit', [DataPegawaiController::class, 'edit'])->name('data-pegawai.edit');
    Route::put('/data-pegawai/{pegawai}', [DataPegawaiController::class, 'update'])->name('data-pegawai.update');
    Route::delete('/data-pegawai/{pegawai}', [DataPegawaiController::class, 'destroy'])->name('data-pegawai.destroy');

    // Unit Kerja Routes
    Route::get('/unitkerja', [UnitKerjaController::class, 'index'])->name('unitkerja.index');
    Route::get('/unitkerja/create', [UnitKerjaController::class, 'create'])->name('unitkerja.create');
    Route::post('/unitkerja', [UnitKerjaController::class, 'store'])->name('unitkerja.store');
    Route::get('/unitkerja/{unitKerja}/edit', [UnitKerjaController::class, 'edit'])->name('unitkerja.edit');
    Route::put('/unitkerja/{unitKerja}', [UnitKerjaController::class, 'update'])->name('unitkerja.update');
    Route::delete('/unitkerja/{unitKerja}', [UnitKerjaController::class, 'destroy'])->name('unitkerja.destroy');

    // Sub Unit Kerja Routes
    Route::get('/sub-unitkerja', [SubUnitKerjaController::class, 'index'])->name('sub-unitkerja.index');
    Route::get('/sub-unitkerja/create', [SubUnitKerjaController::class, 'create'])->name('sub-unitkerja.create');
    Route::post('/sub-unitkerja', [SubUnitKerjaController::class, 'store'])->name('sub-unitkerja.store');
    Route::get('/sub-unitkerja/{subUnitKerja}/edit', [SubUnitKerjaController::class, 'edit'])->name('sub-unitkerja.edit');
    Route::put('/sub-unitkerja/{subUnitKerja}', [SubUnitKerjaController::class, 'update'])->name('sub-unitkerja.update');
    Route::delete('/sub-unitkerja/{subUnitKerja}', [SubUnitKerjaController::class, 'destroy'])->name('sub-unitkerja.destroy');

    // Sub Sub Unit Kerja Routes
    Route::get('/sub-sub-unitkerja', [SubSubUnitKerjaController::class, 'index'])->name('sub-sub-unitkerja.index');
    Route::get('/sub-sub-unitkerja/create', [SubSubUnitKerjaController::class, 'create'])->name('sub-sub-unitkerja.create');
    Route::post('/sub-sub-unitkerja', [SubSubUnitKerjaController::class, 'store'])->name('sub-sub-unitkerja.store');
    Route::get('/sub-sub-unitkerja/{subSubUnitKerja}/edit', [SubSubUnitKerjaController::class, 'edit'])->name('sub-sub-unitkerja.edit');
    Route::put('/sub-sub-unitkerja/{subSubUnitKerja}', [SubSubUnitKerjaController::class, 'update'])->name('sub-sub-unitkerja.update');
    Route::delete('/sub-sub-unitkerja/{subSubUnitKerja}', [SubSubUnitKerjaController::class, 'destroy'])->name('sub-sub-unitkerja.destroy');

    // Sub Sub Unit Kerja Import/Export Routes
    // Route::get('/sub-sub-unitkerja/import-form', [SubSubUnitKerjaController::class, 'importForm'])->name('sub-sub-unitkerja.import-form');
    // Route::post('/sub-sub-unitkerja/import', [SubSubUnitKerjaController::class, 'import'])->name('sub-sub-unitkerja.import');
    // Route::get('/sub-sub-unitkerja/export-template', [SubSubUnitKerjaController::class, 'exportTemplate'])->name('sub-sub-unitkerja.export-template');

    // AJAX Route for getting Sub Unit Kerjas based on Unit Kerja
    Route::get('/get-sub-unit-kerjas', [SubSubUnitKerjaController::class, 'getSubUnitKerjas'])->name('get-sub-unit-kerjas');

    // Pangkat Routes
    Route::get('/pangkat', [PangkatController::class, 'index'])->name('pangkat.index');
    Route::get('/pangkat/create', [PangkatController::class, 'create'])->name('pangkat.create');
    Route::post('/pangkat', [PangkatController::class, 'store'])->name('pangkat.store');
    Route::get('/pangkat/{pangkat}/edit', [PangkatController::class, 'edit'])->name('pangkat.edit');
    Route::put('/pangkat/{pangkat}', [PangkatController::class, 'update'])->name('pangkat.update');
    Route::delete('/pangkat/{pangkat}', [PangkatController::class, 'destroy'])->name('pangkat.destroy');

    // Jabatan Routes
    Route::get('/jabatan', [JabatanController::class, 'index'])->name('jabatan.index');
    Route::get('/jabatan/create', [JabatanController::class, 'create'])->name('jabatan.create');
    Route::post('/jabatan', [JabatanController::class, 'store'])->name('jabatan.store');
    Route::get('/jabatan/{jabatan}/edit', [JabatanController::class, 'edit'])->name('jabatan.edit');
    Route::put('/jabatan/{jabatan}', [JabatanController::class, 'update'])->name('jabatan.update');
    Route::delete('/jabatan/{jabatan}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');
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
