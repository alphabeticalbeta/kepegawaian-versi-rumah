<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ProfilController as FrontendProfil;
use App\Http\Controllers\Frontend\LayananController as FrontendLayanan;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\DasarHukumController;
use App\Http\Controllers\Backend\AdminUniversitas\DasarHukumController as AdminDasarHukumController;

// ------ RUTE HALAMAN FRONTEND ------//
Route::get('/', [HomeController::class, 'index'])->name('frontend.home');

Route::prefix('profil')->group(function () {
    Route::get('/visi-misi', [FrontendProfil::class, 'visiMisi'])->name('profil.visi-misi');
    Route::get('/struktur-organisasi', [FrontendProfil::class, 'strukturOrganisasi'])->name('profil.struktur-organisasi');
});

// Informasi Routes
Route::get('/berita', [App\Http\Controllers\Frontend\InformasiController::class, 'berita'])->name('frontend.berita');
Route::get('/berita/{informasi}', [App\Http\Controllers\Frontend\InformasiController::class, 'show'])->name('berita.show');
Route::get('/berita/{informasi}/download/{filename}', [App\Http\Controllers\Frontend\InformasiController::class, 'download'])->name('berita.download');

Route::get('/pengumuman', [App\Http\Controllers\Frontend\InformasiController::class, 'pengumuman'])->name('frontend.pengumuman');
Route::get('/pengumuman/{informasi}', [App\Http\Controllers\Frontend\InformasiController::class, 'show'])->name('pengumuman.show');
Route::get('/pengumuman/{informasi}/download/{filename}', [App\Http\Controllers\Frontend\InformasiController::class, 'download'])->name('pengumuman.download');

// Dasar Hukum Routes with Controller
Route::prefix('dasar-hukum')->name('dasar-hukum.')->group(function () {
    Route::get('/keputusan', [DasarHukumController::class, 'index'])->name('keputusan.index');
    Route::get('/keputusan/{keputusan}', [DasarHukumController::class, 'show'])->name('keputusan.show');
    Route::get('/keputusan/{keputusan}/download/{filename}', [DasarHukumController::class, 'download'])->name('keputusan.download');
        Route::get('/peraturan', [DasarHukumController::class, 'peraturan'])->name('peraturan.index');
        Route::get('/peraturan/{peraturan}', [DasarHukumController::class, 'show'])->name('peraturan.show');
        Route::get('/peraturan/{peraturan}/download/{filename}', [DasarHukumController::class, 'download'])->name('peraturan.download');
        Route::get('/surat-edaran', [DasarHukumController::class, 'suratEdaran'])->name('surat-edaran.index');
        Route::get('/surat-edaran/{surat_edaran}', [DasarHukumController::class, 'show'])->name('surat-edaran.show');
        Route::get('/surat-edaran/{surat_edaran}/download/{filename}', [DasarHukumController::class, 'download'])->name('surat-edaran.download');
        Route::get('/surat-kementerian', [DasarHukumController::class, 'suratKementerian'])->name('surat-kementerian.index');
        Route::get('/surat-kementerian/{surat_kementerian}', [DasarHukumController::class, 'show'])->name('surat-kementerian.show');
        Route::get('/surat-kementerian/{surat_kementerian}/download/{filename}', [DasarHukumController::class, 'download'])->name('surat-kementerian.download');
        Route::get('/surat-rektor-unmul', [DasarHukumController::class, 'suratRektorUnmul'])->name('surat-rektor-unmul.index');
        Route::get('/surat-rektor-unmul/{surat_rektor_unmul}', [DasarHukumController::class, 'show'])->name('surat-rektor-unmul.show');
        Route::get('/surat-rektor-unmul/{surat_rektor_unmul}/download/{filename}', [DasarHukumController::class, 'download'])->name('surat-rektor-unmul.download');
        Route::get('/undang-undang', [DasarHukumController::class, 'undangUndang'])->name('undang-undang.index');
        Route::get('/undang-undang/{undang_undang}', [DasarHukumController::class, 'show'])->name('undang-undang.show');
        Route::get('/undang-undang/{undang_undang}/download/{filename}', [DasarHukumController::class, 'download'])->name('undang-undang.download');
        Route::get('/pedoman', [DasarHukumController::class, 'pedoman'])->name('pedoman.index');
        Route::get('/pedoman/{pedoman}', [DasarHukumController::class, 'show'])->name('pedoman.show');
        Route::get('/pedoman/{pedoman}/download/{filename}', [DasarHukumController::class, 'download'])->name('pedoman.download');
    Route::get('/search-suggestions', [DasarHukumController::class, 'searchSuggestions'])->name('search-suggestions');
});

// Legacy route for backward compatibility
Route::get('/keputusan', [DasarHukumController::class, 'index'])->name('keputusan');
Route::get('/peraturan', [DasarHukumController::class, 'peraturan'])->name('peraturan');
Route::get('/pedoman', [DasarHukumController::class, 'pedoman'])->name('pedoman');
Route::get('/surat-edaran', [DasarHukumController::class, 'suratEdaran'])->name('surat-edaran');
Route::get('/surat-kementerian', [DasarHukumController::class, 'suratKementerian'])->name('surat-kementerian');
Route::get('/surat-rektor-unmul', [DasarHukumController::class, 'suratRektorUnmul'])->name('surat-rektor-unmul');
Route::get('/undang-undang', [DasarHukumController::class, 'undangUndang'])->name('undang-undang');








// Public route for dasar hukum documents (no authentication required)
Route::get('/dasar-hukum-document/{filename}', [DasarHukumController::class, 'showDocument'])
    ->name('dasar-hukum.document');

// Admin Universitas Routes (using frontend routes)
Route::prefix('admin-universitas')->middleware(['auth', 'web'])->group(function () {

    // Struktur Organisasi Routes
    Route::get('/struktur-organisasi', [App\Http\Controllers\Backend\AdminUniversitas\StrukturOrganisasiController::class, 'index'])
        ->name('admin-universitas.struktur-organisasi.index');
    Route::post('/struktur-organisasi', [App\Http\Controllers\Backend\AdminUniversitas\StrukturOrganisasiController::class, 'store'])
        ->name('admin-universitas.struktur-organisasi.store');
    Route::delete('/struktur-organisasi', [App\Http\Controllers\Backend\AdminUniversitas\StrukturOrganisasiController::class, 'destroy'])
        ->name('admin-universitas.struktur-organisasi.destroy');

    // API Routes for Admin Universitas (other controllers)

    Route::get('/aplikasi-kepegawaian', [App\Http\Controllers\Backend\AdminUniversitas\AplikasiKepegawaianController::class, 'index'])
        ->name('admin-universitas.aplikasi-kepegawaian.index');

    Route::post('/aplikasi-kepegawaian', [App\Http\Controllers\Backend\AdminUniversitas\AplikasiKepegawaianController::class, 'store'])
        ->name('admin-universitas.aplikasi-kepegawaian.store');
    Route::put('/aplikasi-kepegawaian/{id}', [App\Http\Controllers\Backend\AdminUniversitas\AplikasiKepegawaianController::class, 'update'])
        ->name('admin-universitas.aplikasi-kepegawaian.update');
    Route::delete('/aplikasi-kepegawaian/{id}', [App\Http\Controllers\Backend\AdminUniversitas\AplikasiKepegawaianController::class, 'destroy'])
        ->name('admin-universitas.aplikasi-kepegawaian.destroy');

    // Dasar Hukum Routes
    Route::get('/dasar-hukum', [DasarHukumController::class, 'index'])
        ->name('admin-universitas.dasar-hukum.index');
    Route::post('/dasar-hukum', [DasarHukumController::class, 'store'])
        ->name('admin-universitas.dasar-hukum.store');
    Route::get('/dasar-hukum/{id}', [DasarHukumController::class, 'show'])
        ->name('admin-universitas.dasar-hukum.show');
    Route::get('/dasar-hukum/{id}/download/{filename}', [DasarHukumController::class, 'download'])
        ->name('admin-universitas.dasar-hukum.download');
    Route::put('/dasar-hukum/{id}', [DasarHukumController::class, 'update'])
        ->name('admin-universitas.dasar-hukum.update');
    Route::delete('/dasar-hukum/{id}', [DasarHukumController::class, 'destroy'])
        ->name('admin-universitas.dasar-hukum.destroy');

    Route::get('/informasi', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'index'])
        ->name('admin-universitas.informasi.index');
    Route::get('/informasi/{id}', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'show'])
        ->name('admin-universitas.informasi.show');
    Route::get('/informasi/{id}/edit', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'edit'])
        ->name('admin-universitas.informasi.edit');
    Route::post('/informasi', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'store'])
        ->name('admin-universitas.informasi.store');
    Route::put('/informasi/{id}', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'update'])
        ->name('admin-universitas.informasi.update');
    Route::delete('/informasi/{id}', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'destroy'])
        ->name('admin-universitas.informasi.destroy');
    Route::post('/informasi/generate-nomor-surat', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'generateNomorSurat'])
        ->name('admin-universitas.informasi.generate-nomor-surat');
    Route::get('/informasi/{id}/download/{filename}', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'download'])
        ->name('admin-universitas.informasi.download');
    Route::get('/informasi/{id}/debug-storage', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'debugFileStorage'])
        ->name('admin-universitas.informasi.debug-storage');
});

// Simple endpoints for informasi (alternative to admin-universitas routes)
Route::get('/informasi/{id}', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'show'])
    ->name('informasi.show');
Route::post('/informasi-simple', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'store'])
    ->name('informasi.simple.store');
Route::put('/informasi-simple/{id}', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'update'])
    ->name('informasi.simple.update');
Route::delete('/informasi-simple/{id}', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'destroy'])
    ->name('informasi.simple.destroy');
Route::post('/informasi-simple/generate-nomor-surat', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'generateNomorSurat'])
    ->name('informasi.simple.generate-nomor-surat');

Route::prefix('layanan')->group(function () {
    Route::get('/aplikasi', [FrontendLayanan::class, 'aplikasi'])->name('layanan.aplikasi');
    Route::get('/usulan-kepegawaian', [FrontendLayanan::class, 'usulanKepegawaian'])->name('layanan.usulan-kepegawaian');
});

Route::view('/blangko-surat', 'frontend.layouts.blangko-surat')->name('blangko.surat');

// API Routes for Frontend
Route::get('/api/visi-misi', [App\Http\Controllers\Backend\AdminUniversitas\VisiMisiController::class, 'getData']);
Route::options('/api/visi-misi', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
});

Route::get('/api/struktur-organisasi', [App\Http\Controllers\Api\StrukturOrganisasiController::class, 'index']);
Route::post('/api/struktur-organisasi', [App\Http\Controllers\Api\StrukturOrganisasiController::class, 'store']);
Route::delete('/api/struktur-organisasi', [App\Http\Controllers\Api\StrukturOrganisasiController::class, 'destroy']);
Route::options('/api/struktur-organisasi', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
});


Route::get('/api/informasi', [App\Http\Controllers\Api\InformasiController::class, 'index']);
Route::get('/api/informasi/featured', [App\Http\Controllers\Api\InformasiController::class, 'featured']);
Route::get('/api/informasi/pinned', [App\Http\Controllers\Api\InformasiController::class, 'pinned']);
Route::get('/api/informasi/latest', [App\Http\Controllers\Api\InformasiController::class, 'latest']);
Route::get('/api/informasi/{id}', [App\Http\Controllers\Api\InformasiController::class, 'show']);


// Simple endpoints for Struktur Organisasi
Route::get('/struktur-organisasi/data', [App\Http\Controllers\Api\StrukturOrganisasiController::class, 'index'])
    ->name('struktur-organisasi.data');
Route::post('/struktur-organisasi/store', [App\Http\Controllers\Api\StrukturOrganisasiController::class, 'store'])
    ->name('struktur-organisasi.store');
Route::delete('/struktur-organisasi/delete', [App\Http\Controllers\Api\StrukturOrganisasiController::class, 'destroy'])
    ->name('struktur-organisasi.delete');

// Simple endpoints for Visi Misi
Route::get('/visi-misi/data', [App\Http\Controllers\Backend\AdminUniversitas\VisiMisiController::class, 'getData'])
    ->name('visi-misi.data');

// Route untuk mengakses file lampiran dengan authentication
Route::get('/lampiran/{filename}', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'showDocument'])
    ->name('lampiran.show');


Route::options('/api/informasi', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
});
