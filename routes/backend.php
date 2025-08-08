<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Backend\AdminFakultas\DashboardController as AdminFakultasDashboardController;
use App\Http\Controllers\Backend\AdminUniversitas\DashboardController as AdminUniversitasDashboardController;
use App\Http\Controllers\Backend\AdminUnivUsulan\DashboardController as AdminUnivUsulanDashboardController;
use App\Http\Controllers\Backend\AdminUnivUsulan\DataPegawaiController;
use App\Http\Controllers\Backend\AdminUnivUsulan\JabatanController;
use App\Http\Controllers\Backend\AdminUnivUsulan\PangkatController;
use App\Http\Controllers\Backend\AdminUnivUsulan\RolePegawaiController;
use App\Http\Controllers\Backend\AdminUnivUsulan\SubSubUnitKerjaController;
use App\Http\Controllers\Backend\AdminUnivUsulan\SubUnitKerjaController;
use App\Http\Controllers\Backend\AdminUnivUsulan\UnitKerjaController;
use App\Http\Controllers\Backend\AdminUnivUsulan\PegawaiController; // Digunakan untuk manajemen akun
use App\Http\Controllers\Backend\PegawaiUnmul\DashboardController as PegawaiUnmulDashboardController;
use App\Http\Controllers\Backend\PenilaiUniversitas\DashboardController as PenilaiUniversitasDashboardController;
use App\Http\Controllers\Backend\PegawaiUnmul\ProfileController as ProfileController;
use App\Http\Controllers\Backend\AdminUnivUsulan\PeriodeUsulanController;
use App\Http\Controllers\Backend\AdminUnivUsulan\PusatUsulanController;
use App\Http\Controllers\Backend\PegawaiUnmul\UsulanPegawaiController as UsulanPegawaiController;
use App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController;

// ------ RUTE HALAMAN LOGIN & LOGOUT ------//
Route::get('/login', [LoginController::class, 'showLoginForm'])->middleware('guest:pegawai')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest:pegawai');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:pegawai')->name('logout');


// ======================================================================
// SEMUA RUTE DI BAWAH INI SEKARANG HANYA BISA DIAKSES SETELAH LOGIN
// ======================================================================
Route::middleware(['auth:pegawai'])->group(function () {

    // ------ RUTE HALAMAN BACKEND ADMIN UNIVERSITAS ------//
    Route::prefix('admin-universitas')->name('admin-universitas.')->group(function () {
        Route::get('/dashboard', [AdminUniversitasDashboardController::class, 'index'])->name('dashboard-universitas');
    });


    // ------ RUTE HALAMAN BACKEND ADMIN UNIVERSITAS USULAN------//
    Route::prefix('admin-universitas-usulan')->middleware(['auth', 'role:Admin Universitas Usulan'])->name('backend.admin-univ-usulan.')->group(function () {
          Route::get('/dashboard', [AdminUnivUsulanDashboardController::class, 'index'])->name('dashboard');

        // Resource Routes untuk Master Data
        Route::resource('/data-pegawai', DataPegawaiController::class)
            ->parameters(['data-pegawai' => 'pegawai']);
        Route::resource('/unitkerja', UnitKerjaController::class);
        Route::resource('/sub-unitkerja', SubUnitKerjaController::class);
        Route::resource('/sub-sub-unitkerja', SubSubUnitKerjaController::class);
        Route::resource('/pangkat', PangkatController::class);
        Route::resource('/jabatan', JabatanController::class);

        // --- TAMBAHKAN BARIS DI BAWAH INI ---
        Route::get('/pusat-usulan', [PusatUsulanController::class, 'index'])->name('pusat-usulan.index');

         Route::get('/periode-usulan/{periodeUsulan}/pendaftar', [PusatUsulanController::class, 'showPendaftar'])->name('periode-usulan.pendaftar');

        // Route untuk Manajemen Periode Usulan
         Route::resource('/periode-usulan', PeriodeUsulanController::class);

        // Route untuk Manajemen Role Pegawai
        Route::get('/role-pegawai', [RolePegawaiController::class, 'index'])->name('role-pegawai.index');
        Route::get('/role-pegawai/{pegawai}/edit', [RolePegawaiController::class, 'edit'])->name('role-pegawai.edit');
        Route::put('/role-pegawai/{pegawai}', [RolePegawaiController::class, 'update'])->name('role-pegawai.update');

        // --- ROUTE BARU UNTUK MANAJEMEN AKUN PEGAWAI ---
        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');

        // Custom Routes
        Route::get('/data-pegawai/{pegawai}/dokumen/{field}', [DataPegawaiController::class, 'showDocument'])->name('data-pegawai.show-document');
        Route::get('/get-sub-unit-kerjas', [SubSubUnitKerjaController::class, 'getSubUnitKerjas'])->name('get-sub-unit-kerjas');
    });


    // ------ RUTE HALAMAN BACKEND USUL PEGAWAI UNMUL------//
    Route::prefix('pegawai-unmul')->name('pegawai-unmul.')->group(function () {
        Route::get('/dashboard', [PegawaiUnmulDashboardController::class, 'index'])->name('dashboard-pegawai-unmul');

        Route::prefix('profil')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'show'])->name('show');
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
        });

        Route::get('/usulan-saya', [UsulanPegawaiController::class, 'index'])->name('usulan-pegawai.dashboard');

         Route::prefix('usulan-jabatan')->name('usulan-jabatan.')->group(function () {
            // Rute untuk MEMBUAT usulan baru
            Route::get('/create', [UsulanPegawaiController::class, 'createJabatan'])->name('create');
            Route::post('/', [UsulanPegawaiController::class, 'storeJabatan'])->name('store');

            // Rute untuk MENGEDIT usulan yang ada
            Route::get('/{usulan}/edit', [UsulanPegawaiController::class, 'editJabatan'])->name('edit');
            Route::put('/{usulan}', [UsulanPegawaiController::class, 'updateJabatan'])->name('update');
        });

        Route::get('/usulan-saya/{usulan}/dokumen/{field}', [UsulanPegawaiController::class, 'showUsulanDocument'])->name('usulan-pegawai.show-document');
        Route::get('/usulan/{usulan}/logs', [UsulanPegawaiController::class, 'getLogs'])->name('usulan-pegawai.logs');
    });


    // ------ RUTE HALAMAN BACKEND ADMIN FAKULTAS ------//
    Route::prefix('admin-fakultas')->middleware(['auth', 'role:Admin Fakultas'])->name('admin-fakultas.')->group(function () {

            // Rute untuk menampilkan dasbor utama Admin Fakultas
            Route::get('/dashboard', [AdminFakultasController::class, 'index'])->name('dashboard');

            // (Nanti kita akan tambahkan rute untuk show, update, dll di sini)
    });


    // ------ RUTE HALAMAN BACKEND PENILAI UNIVERSITAS ------//
    Route::prefix('penilai-universitas')->name('penilai-universitas.')->group(function () {
        Route::get('/dashboard', [PenilaiUniversitasDashboardController::class, 'index'])->name('dashboard-penilai-universitas');
    });

}); // <-- Penutup untuk middleware group
