<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\FraController;
use App\Http\Controllers\RenstraController;
use App\Http\Controllers\BuktiDukungController;
use App\Http\Controllers\DokumenKegiatanController;
use App\Http\Controllers\OptimizedDownloadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Style\Supervisor;
use App\Http\Controllers\PenggunaController;

// Root route - redirect based on authentication status
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// =====================================================================
// PUBLIC ROUTES - No authentication required
// =====================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    
    // Lupa Password Routes
    Route::get('/lupa-password', function () {
        return view('lupa_password');
    })->name('lupa_password');
    Route::post('/lupa-password/send-code', [AuthController::class, 'sendResetCode'])->name('lupa_password.send_code');
    Route::post('/lupa-password/verify-code', [AuthController::class, 'verifyResetCode'])->name('lupa_password.verify_code');
});

// =====================================================================
// AUTHENTICATED ROUTES - All require authentication
// =====================================================================
Route::middleware('auth')->group(function () {
    // Basic authenticated routes
    Route::get('/dashboard', [AuthController::class, 'masuk'])->name('dashboard');
    Route::get('/dashboard/detail-dashboard/{komponen?}', [AuthController::class, 'dashboardDetail'])->name('dashboard.detail');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/register', [PenggunaController::class, 'register'])->name('register.submit');
    Route::get('/register', [PenggunaController::class, 'showRegistrationForm'])->name('register');

    // =====================================================================
    // ROUTE
    // =====================================================================
        // Route untuk manajemen pengguna
        Route::get('/manajemen-pengguna', [PenggunaController::class, 'index'])->name('manajemen.pengguna');
        Route::post('/manajemen-pengguna/store', [PenggunaController::class, 'store'])->name('pengguna.store');
        Route::get('/manajemen-pengguna/{id}/edit', [PenggunaController::class, 'edit'])->name('pengguna.edit');
        Route::put('/manajemen-pengguna/update', [PenggunaController::class, 'update'])->name('pengguna.update');
        Route::delete('/manajemen-pengguna/destroy', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');

        // Route untuk manajemen kegiatan
        Route::post('/kegiatan/store', [KegiatanController::class, 'store'])->name('kegiatan.store');
        Route::put('/kegiatan/{id}', [KegiatanController::class, 'update'])->name('kegiatan.update');
        Route::delete('/kegiatan/{id}', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');

        // Manajemen Renstra
        Route::post('/perencanaan-kinerja/manajemen-renstra/renstra/store', [RenstraController::class, 'store'])->name('perencanaan.renstra.store');
        Route::get('/perencanaan-kinerja/manajemen-renstra/renstra/{id}/upload', [RenstraController::class, 'showUploadForm'])->name('renstra.upload.form');
        Route::delete('/perencanaan-kinerja/manajemen-renstra/renstra/{id}', [RenstraController::class, 'destroy'])->name('renstra.destroy');
        Route::get('/perencanaan-kinerja/manajemen-renstra', [KegiatanController::class, 'manajemenRenstra'])->name('manajemen.renstra');
        Route::get('/perencanaan-kinerja/manajemen-renstra/renstra/{id}', [RenstraController::class, 'show'])->name('renstra.show');
        Route::get('/renstra/detail/{id}/{year}', [RenstraController::class, 'detail'])->name('renstra.detail');

        // Manajemen RKT
        Route::get('/perencanaan-kinerja/manajemen-rkt', [KegiatanController::class, 'manajemenRKT'])->name('manajemen.rkt');

        // Manajemen PK
        Route::get('/perencanaan-kinerja/manajemen-pk', [KegiatanController::class, 'manajemenPK'])->name('manajemen.pk');

        // Manajemen FRA
        Route::get('/pengukuran-kinerja/fra', [FraController::class, 'index'])->name('fra.index');
        Route::get('/fra/{id}/target', [FraController::class, 'inputTarget'])->name('form.target.fra');
        Route::post('/fra/{id}/target', [FraController::class, 'simpanTarget'])->name('simpan.target.fra');
        Route::delete('/fra/destroy/{id}', [FraController::class, 'destroy'])->name('fra.destroy');
        Route::post('/fra/store', [FraController::class, 'store'])->name('fra.store');
        Route::get('/pengukuran-kinerja/fra/{id}/triwulan-details', [FraController::class, 'getTriwulanDetails'])->name('fra.triwulan.details');
        Route::get('/pengukuran-kinerja/fra/{fra}/realisasi/{triwulan}', [FraController::class, 'formRealisasi'])->name('form.realisasi.fra');
        Route::post('/pengukuran-kinerja/fra/{fra}/realisasi/{triwulan}', [FraController::class, 'simpanRealisasi'])->name('simpan.realisasi.fra');

        // Detail kegiatan
        Route::get('/detail/{id}/{year}', [KegiatanController::class, 'detail'])->name('detail');
        // Route::get('/pengukuran-kinerja/fra/{fra}/detail/{triwulan}', [FraController::class, 'detail'])->name('fra.detail');

        // Download FRA
        Route::get('/fra/{fra}/download-triwulan/{triwulan}/{format}', [FraController::class, 'downloadFraTriwulan'])->name('fra.download.triwulan');
        Route::get('/fra/{fra}/download-lengkap/{format}', [FraController::class, 'downloadFraLengkap'])->name('fra.download.lengkap');

        // SK Tim SAKIP
        Route::get('/pengukuran-kinerja/sk-tim-sakip', [KegiatanController::class, 'SkTimSakip'])->name('sk.tim.sakip');

        // Reward Punishment
        Route::get('/pengukuran-kinerja/reward-punishment', [KegiatanController::class, 'rewardPunishment'])->name('reward.punishment');
        Route::get('/pengukuran-kinerja/reward-punishment/detail/{id}', [KegiatanController::class, 'rewardPunishmentDetail'])->name('reward.punishment.detail');

        // Manajemen Lakin
        Route::get('/pelaporan-kinerja/manajemen-lakin', [KegiatanController::class, 'manajemenLakin'])->name('manajemen.lakin');

        // Generate Link
        Route::get('/pelaporan-kinerja/generate-link', [KegiatanController::class, 'generateLink'])->name('generate.link');

        // Manajemen SKP Khusus Super Admin
        Route::get('/pengukuran-kinerja/skp', [KegiatanController::class, 'skp'])->name('skp');
        Route::get('/pengukuran-kinerja/skp/detail/{id}', [KegiatanController::class, 'skpDetail'])->name('skp.detail.kinerja');
        Route::post('/pengukuran-kinerja/skp/upload-bulanan', [KegiatanController::class, 'uploadSkpBulanan'])->name('skp.upload.bulanan');
        Route::post('/pengukuran-kinerja/skp/upload-tahunan', [KegiatanController::class, 'uploadSkpTahunan'])->name('skp.upload.tahunan');
        Route::post('/pengukuran-kinerja/skp/upload-manual', [KegiatanController::class, 'uploadSkpManual'])->name('skp.upload.manual');
        Route::get('/pengukuran-kinerja/skp/download-bulanan/{nip}', [KegiatanController::class, 'downloadSkpBulanan'])->name('skp.download.bulanan');
        Route::get('/pengukuran-kinerja/skp/download-bulanan/{nip}/{bulan}', [KegiatanController::class, 'downloadSkpBulananPerBulan'])->name('skp.download.bulanan.month');
        Route::get('/pengukuran-kinerja/skp/download-tahunan/{nip}', [KegiatanController::class, 'downloadSkpTahunan'])->name('skp.download.tahunan');

        // Unggah SKP Route
        Route::get('/pengukuran-kinerja/unggah-skp/detail/{id}', [KegiatanController::class, 'skpDetail'])->name('skp.detail.unggah');

        // FRA Advanced routes
        Route::post('/fra/{fraId}/umum/add-indicator', [FraController::class, 'addUmumIndicator'])->name('fra.umum.add.indicator');
        Route::post('/fra/{fraId}/umum/add-sub-indicator', [FraController::class, 'addUmumSubIndicator'])->name('fra.umum.add.sub.indicator');
        Route::delete('/fra/{fraId}/umum/delete-indicator/{matriksId}', [FraController::class, 'deleteUmumIndicator'])->name('fra.umum.delete.indicator');
        Route::post('/fra/{fraId}/save-target-pk', [FraController::class, 'saveTargetPk'])->name('fra.save.target.pk');

        // Optimized Download Routes
        Route::get('/fra/{fra}/download-fast/{format}', [OptimizedDownloadController::class, 'downloadExcelFast'])->name('fra.download.fast.lengkap');
        Route::get('/fra/{fra}/download-fast/{triwulan}/{format}', [OptimizedDownloadController::class, 'downloadExcelFast'])->name('fra.download.fast.triwulan');
        Route::get('/fra/{fra}/download-pdf-fast', [OptimizedDownloadController::class, 'downloadPdfFast'])->name('fra.download.pdf.fast');

        // Capaian Kinerja
        Route::get('/capaian-kinerja', [FraController::class, 'getCapaianKinerja'])->name('capaian.kinerja');
        Route::post('/capaian-kinerja/store', [FraController::class, 'storeCapaianKinerja'])->name('capaian.kinerja.store');
        Route::get('/capaian-kinerja/detail/{id}', [KegiatanController::class, 'capaianKinerjaDetail'])->name('capaian.kinerja.detail');

        //Target PK
        Route::get('/perencanaan-kinerja/form-target-pk/{id}', [KegiatanController::class, 'inputTargetPK'])->name('form.target.pk');
        Route::post('/perencanaan-kinerja/simpan-target/{id}', [KegiatanController::class, 'simpanTargetPK'])->name('target.pk.simpan');

        //Unggah SKP
        Route::get('/pengukuran-kinerja/unggah-skp', [KegiatanController::class, 'unggahSkp'])->name('unggah.skp');
        Route::get('/pengukuran-kinerja/unggah-skp/detail/{id}', [KegiatanController::class, 'skpDetail'])->name('skp.detail.unggah');


        // =====================================================================
        // DOCUMENT MANAGEMENT ROUTES
        // =====================================================================
        Route::post('/dokumen/renstra/{id}', [DokumenKegiatanController::class, 'storeRenstraDokumen'])->name('dokumen.renstra.store');
        Route::post('/dokumen/kegiatan/{id}', [DokumenKegiatanController::class, 'storeKegiatanDokumen'])->name('dokumen.kegiatan.store');
        Route::delete('/dokumen/{id}', [DokumenKegiatanController::class, 'destroy'])->name('dokumen.destroy');
        Route::delete('/dokumen-kegiatan/{id}', [DokumenKegiatanController::class, 'destroy'])->name('dokumen.kegiatan.destroy');

        // DEBUG ROUTES - HAPUS SETELAH TESTING DI HOSTING
        Route::get('/debug-google-config', function() {
            return [
                'client_id' => config('services.google.client_id') ? 'SET' : 'NOT SET',
                'client_secret' => config('services.google.client_secret') ? 'SET' : 'NOT SET', 
                'refresh_token' => config('services.google.refresh_token') ? 'SET' : 'NOT SET',
                'folder_id' => config('services.google.folder_id') ? 'SET' : 'NOT SET',
                'renstra_id' => config('services.google.renstra_id') ? 'SET' : 'NOT SET',
                'app_env' => config('app.env'),
                'app_url' => config('app.url')
            ];
        });

        Route::get('/test-google-drive', function() {
            try {
                $service = new \App\Services\GoogleDriveOAuthService();
                $token = $service->getCurrentAccessToken();
                
                return [
                    'status' => $token ? 'SUCCESS' : 'FAILED',
                    'token_available' => $token ? 'YES' : 'NO',
                    'token_length' => $token ? strlen($token) : 0,
                    'config_check' => [
                        'client_id' => config('services.google.client_id') ? 'SET' : 'NOT SET',
                        'client_secret' => config('services.google.client_secret') ? 'SET' : 'NOT SET',
                        'refresh_token' => config('services.google.refresh_token') ? 'SET' : 'NOT SET'
                    ]
                ];
            } catch (Exception $e) {
                return [
                    'status' => 'ERROR',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ];
            }
        });
        Route::get('/dokumen/{id}/download', [DokumenKegiatanController::class, 'download'])->name('dokumen.download');
        Route::post('/dokumen/update/{id}', [DokumenKegiatanController::class, 'update'])->name('dokumen.update');
        Route::put('/dokumen-kegiatan/{id}/update-name', [DokumenKegiatanController::class, 'updateName'])->name('dokumen.kegiatan.update.name');
        Route::put('/dokumen-kegiatan/{id}/update-file', [DokumenKegiatanController::class, 'updateFile'])->name('dokumen.kegiatan.update.file');
        Route::get('/dokumen/{id}/view', [DokumenKegiatanController::class, 'view'])->name('dokumen.view');

        // =====================================================================
        // BUKTI DUKUNG ROUTES
        // =====================================================================
        Route::post('/bukti-dukung/store', [BuktiDukungController::class, 'store'])->name('bukti.dukung.store');
        Route::delete('/bukti-dukung/{id}', [BuktiDukungController::class, 'destroy'])->name('bukti.dukung.destroy');
        Route::get('/bukti-dukung/{id}/view', [BuktiDukungController::class, 'view'])->name('bukti.dukung.view');
        Route::put('/bukti-dukung/{id}/update-name', [BuktiDukungController::class, 'updateName'])->name('bukti.dukung.update.name');
        Route::put('/bukti-dukung/{id}/update-file', [BuktiDukungController::class, 'updateFile'])->name('bukti.dukung.update.file');

        // =====================================================================
        // BUKTI DUKUNG FRA ROUTES
        // =====================================================================
        Route::post('/bukti-dukung-fra/store', [\App\Http\Controllers\BuktiDukungFraController::class, 'store'])->name('bukti.dukung.fra.store');
        Route::delete('/bukti-dukung-fra/{id}', [\App\Http\Controllers\BuktiDukungFraController::class, 'destroy'])->name('bukti.dukung.fra.destroy');
        Route::get('/bukti-dukung-fra/files/{realisasiFraId}', [\App\Http\Controllers\BuktiDukungFraController::class, 'getFiles'])->name('bukti.dukung.fra.files');

        // =====================================================================
        // SKP MANAGEMENT ROUTES
        // =====================================================================
        Route::post('/skp/upload', [BuktiDukungController::class, 'uploadSkp'])->name('skp.upload');
        Route::delete('/skp/{id}', [BuktiDukungController::class, 'deleteSkp'])->name('skp.delete');
        Route::get('/skp/{id}/view', [BuktiDukungController::class, 'viewSkp'])->name('skp.view');
        Route::get('/skp/stats/{userId}/{kegiatanId}/{tahun}', [BuktiDukungController::class, 'getSkpStats'])->name('skp.stats');
        Route::put('/skp/{id}/update-name', [KegiatanController::class, 'updateSkpName'])->name('skp.update.name');
        Route::put('/skp/{id}/update-file', [KegiatanController::class, 'updateSkpFile'])->name('skp.update.file');

        // =====================================================================
        // FRA UTILITY ROUTES
        // =====================================================================
        Route::delete('/fra/hapus-bukti-dukung/{id}', [FraController::class, 'hapusBuktiDukung'])->name('hapus.bukti.dukung');
        Route::delete('/pengukuran-kinerja/fra/bukti-dukung/{id}', [FraController::class, 'hapusBuktiDukung'])->name('fra.hapus.bukti');
        Route::get('/fra/download/{id}', [FraController::class, 'downloadFra'])->name('fra.download');
        Route::get('/fra/template', [FraController::class, 'downloadTemplate'])->name('fra.download.template');
        Route::get('/pengukuran-kinerja/fra/download/{id}', [FraController::class, 'downloadFra'])->name('fra.download.alt');
        Route::get('/pengukuran-kinerja/fra/template', [FraController::class, 'downloadTemplate'])->name('fra.download.template.alt');

        // =====================================================================
        // OPTIMIZED DOWNLOAD ROUTES
        // =====================================================================
        Route::get('/fra/{fra}/download-fast/{format}', [OptimizedDownloadController::class, 'downloadExcelFast'])->name('fra.download.fast.lengkap');
        Route::get('/fra/{fra}/download-fast/{triwulan}/{format}', [OptimizedDownloadController::class, 'downloadExcelFast'])->name('fra.download.fast.triwulan');
        Route::get('/fra/{fra}/download-pdf-fast', [OptimizedDownloadController::class, 'downloadPdfFast'])->name('fra.download.pdf.fast');
        Route::get('/fra/{fra}/download-updated/{format}', [OptimizedDownloadController::class, 'downloadExcelWithUpdate'])->name('fra.download.updated.lengkap');
        Route::get('/fra/{fra}/download-updated/{triwulan}/{format}', [OptimizedDownloadController::class, 'downloadExcelWithUpdate'])->name('fra.download.updated.triwulan');
        Route::get('/fra/{fra}/benchmark-download/{format?}', [OptimizedDownloadController::class, 'benchmarkDownload'])->name('fra.benchmark.download');
        Route::get('/pengukuran-kinerja/fra/{fra}/download-fast/{format}', [OptimizedDownloadController::class, 'downloadExcelFast'])->name('fra.download.fast.lengkap.alt');
        Route::get('/pengukuran-kinerja/fra/{fra}/download-fast/{triwulan}/{format}', [OptimizedDownloadController::class, 'downloadExcelFast'])->name('fra.download.fast.triwulan.alt');
        Route::get('/pengukuran-kinerja/fra/{fra}/download-pdf-fast/{triwulan?}', [OptimizedDownloadController::class, 'downloadPdfFast'])->name('fra.download.pdf.fast.alt');
        Route::get('/pengukuran-kinerja/fra/{fra}/download-comprehensive-excel', [FraController::class, 'downloadComprehensiveExcel'])->name('fra.download.comprehensive.excel');
        Route::get('/pengukuran-kinerja/fra/{fra}/benchmark/{format}/{triwulan?}', [OptimizedDownloadController::class, 'benchmarkDownload'])->name('fra.benchmark.download.alt');

        // =====================================================================
        // TESTING AND DEBUG ROUTES - Development only
        // =====================================================================
        Route::get('/testing', [KegiatanController::class, 'reviu_target_renstra'])->name('testing');
        Route::get('/fra/{fra}/test-download/{format?}', [FraController::class, 'testDownload'])->name('fra.test.download');
        Route::get('/pengukuran-kinerja/fra/{fra}/test-download/{format}', [FraController::class, 'testDownload'])->name('fra.test.download.lengkap');
        Route::get('/pengukuran-kinerja/fra/{fra}/test-download/{triwulan}/{format}', [FraController::class, 'testDownload'])->name('fra.test.download.triwulan');
        Route::get('/test-fra-downloads/{fra_id?}', [OptimizedDownloadController::class, 'testDownloadToStorage'])->name('test.fra.downloads');
        Route::post('/test-fra-submission', function (Request $request) {
            Log::info('=== TEST FRA SUBMISSION DEBUG ===', [
                'method' => $request->method(),
                'all_data' => $request->all(),
                'files' => $request->allFiles(),
                'headers' => $request->headers->all()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test submission berhasil!',
                'data' => $request->all()
            ]);
        })->name('test.fra.submission');

        // Rute untuk membersihkan cache dashboard
        Route::get('/clear-dashboard-cache', [AuthController::class, 'clearDashboardCache'])
            ->name('dashboard.clear-cache');

    // Manajemen Profil
    Route::get('/manajemen-profil', [PenggunaController::class, 'manajemenProfil'])
        ->name('manajemen.profil')
        ->middleware('auth');

    // Ganti Password
    Route::post('/change-password', [PenggunaController::class, 'changePassword'])
        ->name('profile.change-password')
        ->middleware('auth');
    
    // Alias untuk update password
    Route::post('/update-password', [PenggunaController::class, 'changePassword'])
        ->name('profile.update-password')
        ->middleware('auth');

    // Update Foto Profil
    Route::post('/update-profile-photo', [PenggunaController::class, 'updateProfilePhoto'])
        ->name('profile.update-photo')
        ->middleware('auth');

    // Hapus Foto Profil
    Route::delete('/delete-profile-photo', [PenggunaController::class, 'deleteProfilePhoto'])
        ->name('profil.hapus-foto')
        ->middleware('auth');

    // Update Profil (Nama dan Email)
    Route::post('/update-profile', [PenggunaController::class, 'updateProfile'])
        ->name('profile.update')
        ->middleware('auth');
});
