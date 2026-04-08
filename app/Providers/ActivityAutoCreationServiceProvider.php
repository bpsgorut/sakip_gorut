<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\Kegiatan;
use App\Models\Renstra; // Added this import
use App\Models\Sub_Komponen; // Added this import
use Carbon\Carbon; // Added this import
use App\Services\GoogleDriveFraService; // Added this import

class ActivityAutoCreationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Hapus pembatasan, jalankan di semua kondisi
        $this->checkAndCreateYearlyActivities();
    }

    /**
     * Cek dan buat kegiatan otomatis untuk tahun berjalan
     *
     * @return void
     */
    private function checkAndCreateYearlyActivities()
    {
        $currentYear = date('Y');

        try {
            // Daftar kegiatan yang harus ada
            $requiredActivities = [
                'Reviu Target Renstra',
                'Capaian Target Renstra',
                'Perjanjian Kinerja',
                'Rencana Kinerja Tahunan',
                'SK Tim SAKIP',
                'Sasaran Kinerja Pegawai',
                'Reward & Punishment',
                'LAKIN',
            ];

            // Cek apakah ada Renstra aktif
            $activeRenstra = Renstra::whereYear('periode_awal', '<=', $currentYear)
                ->whereYear('periode_akhir', '>=', $currentYear)
                ->first();

            if (!$activeRenstra) {
                Log::warning("Tidak ada Renstra aktif untuk tahun {$currentYear}. Pembuatan kegiatan otomatis dibatalkan.");
                return;
            }

            // Proses setiap kegiatan yang diperlukan
            foreach ($requiredActivities as $activityName) {
                $this->createActivityIfNotExists($activityName, $currentYear, $activeRenstra);
            }

        } catch (\Exception $e) {
            Log::error("Error saat membuat kegiatan otomatis untuk tahun {$currentYear}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Membuat kegiatan jika belum ada
     *
     * @param string $activityName
     * @param int $year
     * @param Renstra $activeRenstra
     * @return void
     */
    private function createActivityIfNotExists($activityName, $year, $activeRenstra)
    {
        // Cek kegiatan yang belum ada
        $existingActivity = Kegiatan::where('nama_kegiatan', $activityName)
            ->where('tahun_berjalan', $year)
            ->first();

        if ($existingActivity) {
            return; // Kegiatan sudah ada, tidak perlu dibuat
        }

        // Ambil semua sub komponen
        $subKomponenList = Sub_Komponen::orderBy('sub_komponen', 'asc')->get();

        // Tentukan sub komponen berdasarkan nama kegiatan
        $subKomponen = $this->findSubKomponenForActivity($activityName, $subKomponenList);

        if (!$subKomponen) {
            // Jika tidak ditemukan, buat sub komponen baru
            $subKomponen = $this->createSubKomponenForActivity($activityName);
        }

        // Buat kegiatan baru
        $kegiatan = new Kegiatan();
        $kegiatan->nama_kegiatan = $activityName;
        $kegiatan->tahun_berjalan = $year;
        $kegiatan->sub_komponen_id = $subKomponen->id;
        $kegiatan->renstra_id = $activeRenstra->id;
        
        // Set tanggal default untuk kegiatan otomatis
        $kegiatan->tanggal_mulai = Carbon::create($year, 1, 1);
        $kegiatan->tanggal_berakhir = Carbon::create($year, 12, 31);
        
        $kegiatan->save();

        // Buat folder Google Drive
        $this->createGoogleDriveFolderForKegiatan($kegiatan);
    }

    /**
     * Mencari sub komponen yang sesuai untuk kegiatan
     *
     * @param string $activityName
     * @param \Illuminate\Database\Eloquent\Collection $subKomponenList
     * @return Sub_Komponen|null
     */
    private function findSubKomponenForActivity($activityName, $subKomponenList)
    {
        $keywords = [
            'Reviu Target Renstra' => ['Manajemen Renstra', 'Reviu Target', 'Renstra'],
            'Capaian Target Renstra' => ['Manajemen Renstra', 'Renstra'],
            'Perjanjian Kinerja' => ['Manajemen PK', 'Perjanjian Kinerja'],
            'Rencana Kinerja Tahunan' => ['Manajemen RKT', 'Rencana Kinerja'],
            'SK Tim SAKIP' => ['SK Tim SAKIP', 'Tim SAKIP'],
            'Sasaran Kinerja Pegawai' => ['SKP', 'Sasaran Kinerja', 'Kinerja Pegawai'],
            'Reward & Punishment' => ['Reward', 'Punishment'],
            'LAKIN' => ['LAKIN', 'Laporan Kinerja']
        ];

        $activityKeywords = $keywords[$activityName] ?? [];

        foreach ($activityKeywords as $keyword) {
            $subKomponen = $subKomponenList->first(function ($item) use ($keyword) {
                return stripos($item->sub_komponen, $keyword) !== false;
            });

            if ($subKomponen) {
                return $subKomponen;
            }
        }

        return null;
    }

    /**
     * Membuat sub komponen baru untuk kegiatan
     *
     * @param string $activityName
     * @return Sub_Komponen
     */
    private function createSubKomponenForActivity($activityName)
    {
        $defaultSubKomponen = match($activityName) {
            'Reviu Target Renstra' => 'ManajemenRenstra',
            'Capaian Target Renstra' => 'Manajemen Renstra',
            'Perjanjian Kinerja' => 'Manajemen PK',
            'Rencana Kinerja Tahunan' => 'Manajemen RKT',
            'SK Tim SAKIP' => 'SK Tim SAKIP',
            'Sasaran Kinerja Pegawai' => 'SKP',
            'Reward & Punishment' => 'Reward & Punishment',
            'LAKIN' => 'LAKIN',
            default => 'Kegiatan Umum'
        };

        return Sub_Komponen::firstOrCreate(
            ['sub_komponen' => $defaultSubKomponen],
            ['created_at' => now(), 'updated_at' => now()]
        );
    }

    /**
     * Membuat folder Google Drive untuk kegiatan
     *
     * @param Kegiatan $kegiatan
     * @return void
     */
    private function createGoogleDriveFolderForKegiatan($kegiatan)
    {
        try {
            $googleDriveService = new \App\Services\GoogleDriveOAuthService();
            
            // Untuk kegiatan SKP, buat folder dengan subfolder bulanan dan tahunan
            if (stripos($kegiatan->nama_kegiatan, 'SKP') !== false || 
                stripos($kegiatan->nama_kegiatan, 'Sasaran Kinerja') !== false) {
                $folderResult = $googleDriveService->createSKPFolder(
                    $kegiatan->nama_kegiatan, 
                    $kegiatan->tahun_berjalan
                );
            } elseif (stripos($kegiatan->nama_kegiatan, 'Reward & Punishment') !== false) {
                $folderResult = $googleDriveService->createRewardPunishmentFolder(
                    $kegiatan->nama_kegiatan,
                    $kegiatan->tahun_berjalan
                );
            } else {
                // Buat folder untuk kegiatan lainnya, termasuk Capaian Target Renstra
                $folderResult = $googleDriveService->createKegiatanFolder(
                    $kegiatan->nama_kegiatan, 
                    $kegiatan->tahun_berjalan
                );
            }

            // Validasi hasil pembuatan folder
            if (!$folderResult['success'] || empty($folderResult['folder_id'])) {
                Log::error("Gagal membuat folder Google Drive untuk kegiatan", [
                    'kegiatan_id' => $kegiatan->id,
                    'nama_kegiatan' => $kegiatan->nama_kegiatan,
                    'tahun_berjalan' => $kegiatan->tahun_berjalan,
                    'folder_result' => $folderResult
                ]);

                // Hapus kegiatan yang baru dibuat jika folder gagal
                $kegiatan->delete();
                return;
            }

            // Update kegiatan dengan folder_id
            $kegiatan->folder_id = $folderResult['folder_id'];
            $kegiatan->save();

            Log::info("Folder Google Drive berhasil dibuat untuk kegiatan", [
                'kegiatan_id' => $kegiatan->id,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'folder_id' => $folderResult['folder_id']
            ]);

        } catch (\Exception $e) {
            Log::error("Error saat membuat folder Google Drive untuk kegiatan", [
                'kegiatan_id' => $kegiatan->id,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Hapus kegiatan yang baru dibuat jika terjadi kesalahan
            $kegiatan->delete();
        }
    }
}