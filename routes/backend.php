<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;

// ======================================================================
// AUTHENTICATION ROUTES
// ======================================================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->middleware('guest:pegawai')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest:pegawai');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:pegawai')->name('logout');



// ======================================================================
// PROTECTED ROUTES - SEMUA RUTE DI BAWAH INI HANYA BISA DIAKSES SETELAH LOGIN
// ======================================================================
Route::middleware(['auth:pegawai'])->group(function () {

    // ======================================================================
    // ADMIN UNIVERSITAS ROUTES
    // ======================================================================
    Route::prefix('admin-universitas')
        ->name('admin-universitas.')
        ->middleware(['role:Admin Universitas'])
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\AdminUniversitas\DashboardController::class, 'index'])
                ->name('dashboard');

            // Periode Usulan Management
            Route::resource('/periode-usulan', App\Http\Controllers\Backend\AdminUniversitas\PeriodeUsulanController::class)
                ->parameters(['periode-usulan' => 'periode']);

            // Dashboard Usulan (Dashboard for each period)
            Route::get('/dashboard-usulan', [App\Http\Controllers\Backend\AdminUniversitas\DashboardUsulanController::class, 'index'])
                ->name('dashboard-usulan.index');
            Route::get('/dashboard-usulan/{periode}', [App\Http\Controllers\Backend\AdminUniversitas\DashboardUsulanController::class, 'show'])
                ->name('dashboard-usulan.show');
        });

    // ======================================================================
    // ADMIN UNIVERSITAS USULAN ROUTES
    // ======================================================================
    Route::prefix('admin-univ-usulan')
        ->name('backend.admin-univ-usulan.')
        ->middleware(['role:Admin Universitas Usulan'])
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\AdminUnivUsulan\DashboardController::class, 'index'])
                ->name('dashboard');

            // =====================================================
            // MASTER DATA ROUTES
            // =====================================================

            // Data Pegawai
            Route::resource('/data-pegawai', App\Http\Controllers\Backend\AdminUnivUsulan\DataPegawaiController::class)
                ->parameters(['data-pegawai' => 'pegawai']);
            Route::get('/data-pegawai/{pegawai}/dokumen/{field}', [App\Http\Controllers\Backend\AdminUnivUsulan\DataPegawaiController::class, 'showDocument'])
                ->name('data-pegawai.show-document');



            // Unit Kerja (Hierarchical)
            Route::prefix('unitkerja')->name('unitkerja.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\AdminUnivUsulan\UnitKerjaController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\AdminUnivUsulan\UnitKerjaController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\AdminUnivUsulan\UnitKerjaController::class, 'store'])
                    ->name('store');
                Route::get('/{type}/{id}/edit', [App\Http\Controllers\Backend\AdminUnivUsulan\UnitKerjaController::class, 'edit'])
                    ->name('edit');
                Route::put('/{type}/{id}', [App\Http\Controllers\Backend\AdminUnivUsulan\UnitKerjaController::class, 'update'])
                    ->name('update');
                Route::delete('/{type}/{id}', [App\Http\Controllers\Backend\AdminUnivUsulan\UnitKerjaController::class, 'destroy'])
                    ->name('destroy');

                // API routes untuk dropdown
                Route::get('/api/sub-unit-kerja/{unitKerjaId}', [App\Http\Controllers\Backend\AdminUnivUsulan\UnitKerjaController::class, 'getSubUnitKerja'])
                    ->name('api.sub-unit-kerja');
                Route::get('/api/sub-sub-unit-kerja/{subUnitKerjaId}', [App\Http\Controllers\Backend\AdminUnivUsulan\UnitKerjaController::class, 'getSubSubUnitKerja'])
                    ->name('api.sub-sub-unit-kerja');
            });

            // Jabatan
            Route::resource('/jabatan', App\Http\Controllers\Backend\AdminUnivUsulan\JabatanController::class);
            Route::get('/jabatan-export', [App\Http\Controllers\Backend\AdminUnivUsulan\JabatanController::class, 'export'])
                ->name('jabatan.export');

            // Pangkat
            Route::resource('/pangkat', App\Http\Controllers\Backend\AdminUnivUsulan\PangkatController::class);

            // =====================================================
            // PUSAT USULAN ROUTES
            // =====================================================
            Route::prefix('pusat-usulan')->name('pusat-usulan.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\AdminUnivUsulan\PusatUsulanController::class, 'index'])
                    ->name('index');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\AdminUnivUsulan\PusatUsulanController::class, 'show'])
                    ->name('show');
                Route::post('/{usulan}/process', [App\Http\Controllers\Backend\AdminUnivUsulan\PusatUsulanController::class, 'process'])
                    ->name('process');
                Route::get('/{usulan}/dokumen/{field}', [App\Http\Controllers\Backend\AdminUnivUsulan\PusatUsulanController::class, 'showUsulanDocument'])
                    ->name('show-document');
            });

            // =====================================================
            // DASHBOARD PERIODE USULAN ROUTES
            // =====================================================
            Route::prefix('dashboard-periode')->name('dashboard-periode.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\AdminUnivUsulan\DashboardPeriodeController::class, 'index'])
                    ->name('index');
                Route::get('/{periode}', [App\Http\Controllers\Backend\AdminUnivUsulan\DashboardPeriodeController::class, 'show'])
                    ->name('show');
            });

            // =====================================================
            // USULAN MANAGEMENT ROUTES
            // =====================================================
            Route::prefix('usulan')->name('usulan.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\AdminUnivUsulan\UsulanController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\AdminUnivUsulan\UsulanController::class, 'create'])
                    ->name('create');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\AdminUnivUsulan\UsulanController::class, 'show'])
                    ->name('show');
                Route::post('/toggle-periode', [App\Http\Controllers\Backend\AdminUnivUsulan\UsulanController::class, 'togglePeriode'])
                    ->name('toggle-periode');
            });

            // =====================================================
            // PERIODE USULAN ROUTES
            // =====================================================
            Route::resource('/periode-usulan', App\Http\Controllers\Backend\AdminUnivUsulan\PeriodeUsulanController::class)
                ->parameters(['periode-usulan' => 'periode_usulan']);
            Route::get('/periode-usulan/{periodeUsulan}/pendaftar', [App\Http\Controllers\Backend\AdminUnivUsulan\PusatUsulanController::class, 'showPendaftar'])
                ->name('periode-usulan.pendaftar');

            // =====================================================
            // ROLE PEGAWAI ROUTES
            // =====================================================
            Route::prefix('role-pegawai')->name('role-pegawai.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\AdminUnivUsulan\RolePegawaiController::class, 'index'])
                    ->name('index');
                Route::get('/{pegawai}/edit', [App\Http\Controllers\Backend\AdminUnivUsulan\RolePegawaiController::class, 'edit'])
                    ->name('edit');
                Route::put('/{pegawai}', [App\Http\Controllers\Backend\AdminUnivUsulan\RolePegawaiController::class, 'update'])
                    ->name('update');
            });

            // =====================================================
            // MANAJEMEN AKUN PEGAWAI ROUTES
            // =====================================================
            Route::prefix('pegawai')->name('pegawai.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\AdminUnivUsulan\PegawaiController::class, 'index'])
                    ->name('index');
                Route::get('/{pegawai}/edit', [App\Http\Controllers\Backend\AdminUnivUsulan\PegawaiController::class, 'edit'])
                    ->name('edit');
                Route::put('/{pegawai}', [App\Http\Controllers\Backend\AdminUnivUsulan\PegawaiController::class, 'update'])
                    ->name('update');
            });
        });

    // ======================================================================
    // PEGAWAI UNMUL ROUTES
    // ======================================================================
    Route::prefix('pegawai-unmul')
        ->name('pegawai-unmul.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\PegawaiUnmul\DashboardController::class, 'index'])
                ->name('dashboard-pegawai-unmul');

            // =====================================================
            // PROFILE ROUTES
            // =====================================================
            Route::prefix('profil')->name('profile.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\ProfileController::class, 'show'])
                    ->name('show');
                Route::get('/edit', [App\Http\Controllers\Backend\PegawaiUnmul\ProfileController::class, 'edit'])
                    ->name('edit');
                Route::put('/', [App\Http\Controllers\Backend\PegawaiUnmul\ProfileController::class, 'update'])
                    ->name('update');
                Route::get('/dokumen/{field}', [App\Http\Controllers\Backend\PegawaiUnmul\ProfileController::class, 'showDocument'])
                    ->name('show-document');
            });

            // =====================================================
            // USULAN ROUTES - SEPARATED ARCHITECTURE
            // =====================================================

            // Dashboard Usulan Utama (Pusat)
            Route::get('/usulan-saya', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPegawaiController::class, 'index'])
                ->name('usulan-pegawai.dashboard');

            // Usulan Selector (untuk memilih jenis usulan)
            Route::get('/usulan-create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPegawaiController::class, 'createUsulan'])
                ->name('usulan-pegawai.create');

            // API untuk statistics dashboard
            Route::get('/usulan/api/statistics', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPegawaiController::class, 'getStatistics'])
                ->name('usulan-pegawai.api.statistics');

            // =====================================================
            // USULAN JABATAN ROUTES (SPECIFIC CONTROLLER)
            // =====================================================
            Route::prefix('usulan-jabatan')->name('usulan-jabatan.')->group(function () {
                // Main CRUD routes
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulanJabatan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'destroy'])
                    ->name('destroy');

                // Document routes
                Route::get('/{usulanJabatan}/dokumen/{field}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'showUsulanDocument'])
                    ->name('show-document');

                // API routes
                Route::get('/{usulanJabatan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'getLogs'])
                    ->name('logs');
            });

            // =====================================================
            // USULAN NUPTK ROUTES
            // =====================================================
            Route::resource('usulan-nuptk', App\Http\Controllers\Backend\PegawaiUnmul\UsulanNuptkController::class)
                ->names('usulan-nuptk');

            // =====================================================
            // USULAN LAPORAN LKD ROUTES
            // =====================================================
            Route::resource('usulan-laporan-lkd', App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanLkdController::class)
                ->names('usulan-laporan-lkd');

            // =====================================================
            // USULAN PRESENSI ROUTES
            // =====================================================
            Route::resource('usulan-presensi', App\Http\Controllers\Backend\PegawaiUnmul\UsulanPresensiController::class)
                ->names('usulan-presensi');

            // =====================================================
            // USULAN PENYESUAIAN MASA KERJA ROUTES
            // =====================================================
            Route::resource('usulan-penyesuaian-masa-kerja', App\Http\Controllers\Backend\PegawaiUnmul\UsulanPenyesuaianMasaKerjaController::class)
                ->names('usulan-penyesuaian-masa-kerja');

            // =====================================================
            // USULAN UJIAN DINAS & IJAZAH ROUTES
            // =====================================================
            Route::resource('usulan-ujian-dinas-ijazah', App\Http\Controllers\Backend\PegawaiUnmul\UsulanUjianDinasIjazahController::class)
                ->names('usulan-ujian-dinas-ijazah');

            // =====================================================
            // USULAN LAPORAN SERDOS ROUTES
            // =====================================================
            Route::resource('usulan-laporan-serdos', App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanSerdosController::class)
                ->names('usulan-laporan-serdos');

            // =====================================================
            // USULAN PENSIUN ROUTES
            // =====================================================
            Route::resource('usulan-pensiun', App\Http\Controllers\Backend\PegawaiUnmul\UsulanPensiunController::class)
                ->names('usulan-pensiun');

            // =====================================================
            // USULAN KEPANGKATAN ROUTES
            // =====================================================
            Route::resource('usulan-kepangkatan', App\Http\Controllers\Backend\PegawaiUnmul\UsulanKepangkatanController::class)
                ->names('usulan-kepangkatan');

            // =====================================================
            // USULAN PENCANTUMAN GELAR ROUTES
            // =====================================================
            Route::resource('usulan-pencantuman-gelar', App\Http\Controllers\Backend\PegawaiUnmul\UsulanPencantumanGelarController::class)
                ->names('usulan-pencantuman-gelar');

            // =====================================================
            // USULAN ID SINTA KE SISTER ROUTES
            // =====================================================
            Route::resource('usulan-id-sinta-sister', App\Http\Controllers\Backend\PegawaiUnmul\UsulanIdSintaSisterController::class)
                ->names('usulan-id-sinta-sister');

            // =====================================================
            // USULAN SATYALANCANA ROUTES
            // =====================================================
            Route::resource('usulan-satyalancana', App\Http\Controllers\Backend\PegawaiUnmul\UsulanSatyalancanaController::class)
                ->names('usulan-satyalancana');

            // =====================================================
            // USULAN TUGAS BELAJAR ROUTES
            // =====================================================
            Route::resource('usulan-tugas-belajar', App\Http\Controllers\Backend\PegawaiUnmul\UsulanTugasBelajarController::class)
                ->names('usulan-tugas-belajar');

            // =====================================================
            // USULAN PENGAKTIFAN KEMBALI ROUTES
            // =====================================================
            Route::resource('usulan-pengaktifan-kembali', App\Http\Controllers\Backend\PegawaiUnmul\UsulanPengaktifanKembaliController::class)
                ->names('usulan-pengaktifan-kembali');

            // =====================================================
            // LEGACY COMPATIBILITY - Redirect old routes
            // =====================================================
            Route::get('/usulan-saya/{usulan}/dokumen/{field}', function ($usulan, $field) {
                return redirect()->route('pegawai-unmul.usulan-jabatan.show-document', [$usulan, $field]);
            });

            Route::get('/usulan/{usulan}/logs', function ($usulan) {
                return redirect()->route('pegawai-unmul.usulan-jabatan.logs', $usulan);
            });
        });

    // ======================================================================
    // ADMIN FAKULTAS ROUTES
    // ======================================================================
    Route::prefix('admin-fakultas')
        ->name('admin-fakultas.')
        ->middleware(['role:Admin Fakultas'])
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\AdminFakultas\DashboardController::class, 'index'])
                ->name('dashboard');

            // =====================================================
            // USULAN ROUTES
            // =====================================================
            Route::prefix('usulan')->name('usulan.')->group(function () {
                // Daftar Periode Usulan Jabatan
                Route::get('/jabatan', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'usulanJabatan'])
                    ->name('jabatan');
                Route::get('/pangkat', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'usulanPangkat'])
                    ->name('pangkat');
                Route::get('/', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'indexUsulanJabatan'])
                    ->name('index');

                // Detail Usulan untuk Validasi
                Route::get('/{adminUsulan}', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'show'])
                    ->name('show');

                // Simpan hasil validasi
                Route::post('/{adminUsulan}/validasi', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'saveValidation'])
                    ->name('save-validation');
                Route::post('/{usulan}/autosave', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'autosaveValidation'])
                    ->name('autosave');

                // =====================================================
                // DOCUMENT VIEWING ROUTES
                // =====================================================

                // Dokumen usulan (pakta, turnitin, artikel, dll)
                Route::get('/{usulan}/dokumen/{field}', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'showUsulanDocument'])
                    ->name('show-document');

                // Dokumen profil pegawai (ijazah, SK, dll)
                Route::get('/{usulan}/profil-dokumen/{field}', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'showPegawaiDocument'])
                    ->name('show-pegawai-document');

                // Dokumen pendukung fakultas (surat usulan, berita senat)
                Route::get('/{usulan}/pendukung-dokumen/{field}', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'showDokumenPendukung'])
                    ->name('show-dokumen-pendukung');
            });

            // Daftar Pengusul per Periode
            Route::get('/periode/{periodeUsulan}/pendaftar', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'showPendaftar'])
                ->name('periode.pendaftar');

            // =====================================================
            // DEBUG ROUTES (Development Only)
            // =====================================================
            if (app()->environment('local')) {
                Route::get('/test-dokumen/{usulan}/{field}', function($usulan, $field) {
                    return response()->json([
                        'usulan_id' => $usulan,
                        'field' => $field,
                        'usulan_exists' => \App\Models\BackendUnivUsulan\Usulan::find($usulan) ? true : false,
                        'document_path' => \App\Models\BackendUnivUsulan\Usulan::find($usulan)?->getDocumentPath($field),
                        'file_exists' => \Storage::disk('local')->exists(\App\Models\BackendUnivUsulan\Usulan::find($usulan)?->getDocumentPath($field) ?? '')
                    ]);
                })->name('test-dokumen');
            }
        });

    // ======================================================================
    // PENILAI UNIVERSITAS ROUTES
    // ======================================================================
    Route::prefix('penilai-universitas')
        ->name('penilai-universitas.')
        ->middleware(['role:Penilai Universitas'])
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\PenilaiUniversitas\DashboardController::class, 'index'])
                ->name('dashboard-penilai-universitas');

            // =====================================================
            // PUSAT USULAN ROUTES (Khusus Penilai)
            // =====================================================
            Route::prefix('pusat-usulan')->name('pusat-usulan.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PenilaiUniversitas\PusatUsulanController::class, 'index'])
                    ->name('index');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PenilaiUniversitas\PusatUsulanController::class, 'show'])
                    ->name('show');
                Route::post('/{usulan}/process', [App\Http\Controllers\Backend\PenilaiUniversitas\PusatUsulanController::class, 'process'])
                    ->name('process');
            });
        });
});

// ======================================================================
// ROUTE MODEL BINDING CUSTOMIZATION
// ======================================================================

// Usulan Jabatan binding (dengan ownership check untuk pegawai)
Route::bind('usulanJabatan', function ($value) {
    $usulan = \App\Models\BackendUnivUsulan\Usulan::where('id', $value)->first();

    if (!$usulan) {
        abort(404);
    }

    // Check ownership untuk pegawai routes
    if (request()->is('pegawai-unmul/*') && $usulan->pegawai_id !== Auth::id()) {
        abort(403, 'Anda tidak memiliki akses untuk usulan ini.');
    }

    return $usulan;
});

// Admin usulan binding (tanpa ownership restriction untuk admin)
Route::bind('adminUsulan', function ($value) {
    return \App\Models\BackendUnivUsulan\Usulan::findOrFail($value);
});

// Generic usulan binding untuk compatibility
Route::bind('usulan', function ($value) {
    $usulan = \App\Models\BackendUnivUsulan\Usulan::where('id', $value)->first();

    if (!$usulan) {
        abort(404);
    }

    // For pegawai routes, check ownership
    if (request()->is('pegawai-unmul/*')) {
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk usulan ini.');
        }
    }

    return $usulan;
    });

    // ======================================================================
    // ADMIN KEUANGAN ROUTES
    // ======================================================================
    Route::prefix('admin-keuangan')
        ->name('admin-keuangan.')
        ->middleware(['role:Admin Keuangan'])
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\AdminKeuangan\DashboardController::class, 'index'])
                ->name('dashboard');

            // Laporan Keuangan
            Route::get('/laporan-keuangan', [App\Http\Controllers\Backend\AdminKeuangan\LaporanKeuanganController::class, 'index'])
                ->name('laporan-keuangan.index');

            // Verifikasi Dokumen Keuangan
            Route::get('/verifikasi-dokumen', [App\Http\Controllers\Backend\AdminKeuangan\VerifikasiDokumenController::class, 'index'])
                ->name('verifikasi-dokumen.index');

            // SK Documents Routes
            Route::get('/sk-pangkat', [App\Http\Controllers\Backend\AdminKeuangan\SKPangkatController::class, 'index'])
                ->name('sk-pangkat.index');
            Route::get('/sk-jabatan', [App\Http\Controllers\Backend\AdminKeuangan\SKJabatanController::class, 'index'])
                ->name('sk-jabatan.index');
            Route::get('/sk-berkala', [App\Http\Controllers\Backend\AdminKeuangan\SKBerkalaController::class, 'index'])
                ->name('sk-berkala.index');
            Route::get('/model-d', [App\Http\Controllers\Backend\AdminKeuangan\ModelDController::class, 'index'])
                ->name('model-d.index');
            Route::get('/sk-cpns', [App\Http\Controllers\Backend\AdminKeuangan\SKCPNSController::class, 'index'])
                ->name('sk-cpns.index');
            Route::get('/sk-pns', [App\Http\Controllers\Backend\AdminKeuangan\SKPNSController::class, 'index'])
                ->name('sk-pns.index');
            Route::get('/sk-pppk', [App\Http\Controllers\Backend\AdminKeuangan\SKPPPKController::class, 'index'])
                ->name('sk-pppk.index');
            Route::get('/sk-mutasi', [App\Http\Controllers\Backend\AdminKeuangan\SKMutasiController::class, 'index'])
                ->name('sk-mutasi.index');
            Route::get('/sk-pensiun', [App\Http\Controllers\Backend\AdminKeuangan\SKPensiunController::class, 'index'])
                ->name('sk-pensiun.index');
            Route::get('/sk-tunjangan-sertifikasi', [App\Http\Controllers\Backend\AdminKeuangan\SKTunjanganSertifikasiController::class, 'index'])
                ->name('sk-tunjangan-sertifikasi.index');
            Route::get('/skpp', [App\Http\Controllers\Backend\AdminKeuangan\SKPPController::class, 'index'])
                ->name('skpp.index');
            Route::get('/sk-pemberhentian-meninggal', [App\Http\Controllers\Backend\AdminKeuangan\SKPemberhentianMeninggalController::class, 'index'])
                ->name('sk-pemberhentian-meninggal.index');
            Route::get('/sk-pengaktifan-kembali', [App\Http\Controllers\Backend\AdminKeuangan\SKPengaktifanKembaliController::class, 'index'])
                ->name('sk-pengaktifan-kembali.index');
            Route::get('/sk-tugas-belajar', [App\Http\Controllers\Backend\AdminKeuangan\SKTugasBelajarController::class, 'index'])
                ->name('sk-tugas-belajar.index');
            Route::get('/sk-pemberhentian-sementara', [App\Http\Controllers\Backend\AdminKeuangan\SKPemberhentianSementaraController::class, 'index'])
                ->name('sk-pemberhentian-sementara.index');
            Route::get('/sk-penyesuaian-masa-kerja', [App\Http\Controllers\Backend\AdminKeuangan\SKPenyesuaianMasaKerjaController::class, 'index'])
                ->name('sk-penyesuaian-masa-kerja.index');
        });

    // ======================================================================
    // TIM SENAT ROUTES
    // ======================================================================
    Route::prefix('tim-senat')
        ->name('tim-senat.')
        ->middleware(['role:Tim Senat'])
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\TimSenat\DashboardController::class, 'index'])
                ->name('dashboard');

            // Rapat Senat
            Route::get('/rapat-senat', [App\Http\Controllers\Backend\TimSenat\RapatSenatController::class, 'index'])
                ->name('rapat-senat.index');

            // Keputusan Senat
            Route::get('/keputusan-senat', [App\Http\Controllers\Backend\TimSenat\KeputusanSenatController::class, 'index'])
                ->name('keputusan-senat.index');
        });

// ======================================================================
// DEVELOPMENT DEBUGGING ROUTES (Local Environment Only)
// ======================================================================
if (app()->environment('local')) {
    Route::prefix('debug')->middleware(['auth:pegawai'])->name('debug.')->group(function () {
        Route::get('/routes', function () {
            $routes = collect(\Illuminate\Support\Facades\Route::getRoutes())->map(function ($route) {
                return [
                    'method' => implode('|', $route->methods()),
                    'uri' => $route->uri(),
                    'name' => $route->getName(),
                    'action' => $route->getActionName(),
                ];
            });

            return response()->json($routes->toArray());
        })->name('routes');

        Route::get('/user', function () {
            return response()->json([
                'user' => Auth::user(),
                'permissions' => Auth::user()->getAllPermissions()->pluck('name'),
                'roles' => Auth::user()->getRoleNames(),
            ]);
        })->name('user');
    });
}

