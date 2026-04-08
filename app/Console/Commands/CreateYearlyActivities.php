<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kegiatan;
use App\Models\Sub_Komponen;
use App\Models\Renstra;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleDriveOAuthService;
use App\Services\GoogleDriveFraService;

class CreateYearlyActivities extends Command
{
    protected $googleDriveService;

    public function __construct(GoogleDriveOAuthService $googleDriveService)
    {
        parent::__construct();
        $this->googleDriveService = $googleDriveService;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:create-yearly {--year= : Tahun yang akan dibuat kegiatannya (default: tahun sekarang)} {--force-check : Paksa pengecekan ulang meskipun dalam cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat kegiatan otomatis untuk tahun tertentu jika belum ada, termasuk folder Google Drive';

    /**
     * Daftar kegiatan yang harus dibuat otomatis
     *
     * @var array
     */
    protected $requiredActivities = [
        [
            'nama_kegiatan' => 'Capaian Target Renstra',
            'sub_komponen_keywords' => ['Manajemen Renstra', 'Renstra'],
            'default_sub_komponen' => 'Manajemen Renstra',
        ],
        [
            'nama_kegiatan' => 'Reviu Target Renstra',
            'sub_komponen_keywords' => ['Manajemen Renstra', 'Reviu Target'],
            'default_sub_komponen' => 'Manajemen Renstra',
        ],
        [
            'nama_kegiatan' => 'Perjanjian Kinerja',
            'sub_komponen_keywords' => ['Manajemen PK', 'Perjanjian Kinerja'],
            'default_sub_komponen' => 'Manajemen PK',
        ],
        [
            'nama_kegiatan' => 'Rencana Kinerja Tahunan',
            'sub_komponen_keywords' => ['Manajemen RKT', 'Rencana Kinerja'],
            'default_sub_komponen' => 'Manajemen RKT',
        ],
        [
            'nama_kegiatan' => 'SK Tim SAKIP',
            'sub_komponen_keywords' => ['SK Tim SAKIP', 'Tim SAKIP'],
            'default_sub_komponen' => 'SK Tim SAKIP',
        ],
        [
            'nama_kegiatan' => 'Sasaran Kinerja Pegawai',
            'sub_komponen_keywords' => ['SKP', 'Sasaran Kinerja', 'Kinerja Pegawai'],
            'default_sub_komponen' => 'SKP',
        ],
        [
            'nama_kegiatan' => 'Reward & Punishment',
            'sub_komponen_keywords' => ['Reward', 'Punishment'],
            'default_sub_komponen' => 'Reward & Punishment',
        ],
        [
            'nama_kegiatan' => 'LAKIN',
            'sub_komponen_keywords' => ['LAKIN', 'Laporan Kinerja'],
            'default_sub_komponen' => 'LAKIN',
        ],
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $year = $this->option('year') ?? date('Y');
        $this->info("Membuat kegiatan otomatis untuk tahun {$year}...");

        $createdCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        // Ambil renstra aktif
        $activeRenstra = $this->getActiveRenstra($year);
        if (!$activeRenstra) {
            $this->warn("❌ Tidak ada Renstra aktif untuk tahun {$year}. Pembuatan kegiatan dibatalkan.");
            Log::warning("Tidak ada Renstra aktif untuk tahun {$year}. Pembuatan kegiatan otomatis dibatalkan.");
            return 1; // Kembalikan kode error
        }

        $this->info("✅ Renstra aktif ditemukan: {$activeRenstra->nama_renstra} ({$activeRenstra->periode_awal} - {$activeRenstra->periode_akhir})");

        // Ambil semua sub komponen
        $subKomponenList = Sub_Komponen::orderBy('sub_komponen', 'asc')->get();

        // Log jumlah sub komponen yang tersedia
        $this->info("📋 Total Sub Komponen: {$subKomponenList->count()}");

        foreach ($this->requiredActivities as $activityTemplate) {
            try {
                $result = $this->createActivity($activityTemplate, $year, $activeRenstra, $subKomponenList);
                
                if ($result === 'created') {
                    $createdCount++;
                    $this->info("✅ Berhasil membuat: {$activityTemplate['nama_kegiatan']} {$year}");
                } elseif ($result === 'exists') {
                    $skippedCount++;
                    $this->line("⏭️  Sudah ada: {$activityTemplate['nama_kegiatan']} {$year}");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("❌ Gagal membuat {$activityTemplate['nama_kegiatan']} {$year}: " . $e->getMessage());
                Log::error("Error creating activity {$activityTemplate['nama_kegiatan']} {$year}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'activity_template' => $activityTemplate
                ]);
            }
        }

        $this->info("\n📊 Ringkasan:");
        $this->info("✅ Berhasil dibuat: {$createdCount}");
        $this->info("⏭️  Sudah ada: {$skippedCount}");
        $this->info("❌ Gagal: {$errorCount}");

        // Log ringkasan
        Log::info("Pembuatan kegiatan otomatis untuk tahun {$year} selesai", [
            'created' => $createdCount,
            'skipped' => $skippedCount,
            'errors' => $errorCount,
            'renstra_id' => $activeRenstra->id
        ]);

        return $errorCount > 0 ? 1 : 0;
    }

    /**
     * Membuat kegiatan berdasarkan template
     *
     * @param array $template
     * @param int $year
     * @param Renstra|null $activeRenstra
     * @param \Illuminate\Database\Eloquent\Collection $subKomponenList
     * @return string
     */
    private function createActivity($template, $year, $activeRenstra, $subKomponenList)
    {
        $activityName = $template['nama_kegiatan'];
        
        // Cek apakah kegiatan sudah ada
        $existingActivity = Kegiatan::where('nama_kegiatan', $activityName)
            ->where('tahun_berjalan', $year)
            ->first();

        if ($existingActivity) {
            return 'exists';
        }

        // Cari sub komponen yang sesuai
        $subKomponen = $this->findSubKomponen($subKomponenList, $template['sub_komponen_keywords']);
        
        if (!$subKomponen) {
            // Jika tidak ditemukan, coba buat sub komponen baru
            $subKomponen = $this->createSubKomponen($template['default_sub_komponen']);
        }

        // Buat kegiatan baru
        $kegiatan = new Kegiatan();
        $kegiatan->nama_kegiatan = $activityName;
        $kegiatan->tahun_berjalan = $year;
        $kegiatan->sub_komponen_id = $subKomponen->id;
        $kegiatan->renstra_id = $activeRenstra ? $activeRenstra->id : null;
        
        // Set tanggal default untuk kegiatan otomatis
        $kegiatan->tanggal_mulai = Carbon::create($year, 1, 1);
        $kegiatan->tanggal_berakhir = Carbon::create($year, 12, 31);
        
        $kegiatan->save();

        // ✅ FIXED: Buat folder Google Drive untuk kegiatan
        $folderId = $this->createGoogleDriveFolder($kegiatan);

        // Jika folder_id null, batalkan pembuatan kegiatan
        if ($folderId === null) {
            $kegiatan->delete(); // Hapus kegiatan yang baru dibuat
            Log::warning("Kegiatan {$activityName} {$year} dibatalkan karena gagal membuat folder Google Drive");
            return 'failed';
        }

        // Jika kegiatan adalah 'Reward & Punishment', buat subfolder triwulan
        if (in_array($activityName, ['Reward & Punishment'])) {
            $this->createTriwulanFolders($folderId);
        }

        return 'created';
    }

    /**
     * Mencari sub komponen berdasarkan keywords
     *
     * @param \Illuminate\Database\Eloquent\Collection $subKomponenList
     * @param array $keywords
     * @return \App\Models\Sub_Komponen|null
     */
    private function findSubKomponen($subKomponenList, $keywords)
    {
        foreach ($keywords as $keyword) {
            $subKomponen = $subKomponenList->first(function ($item) use ($keyword) {
                return stripos($item->sub_komponen, $keyword) !== false ||
                    (isset($item->nama_sub_komponen) && stripos($item->nama_sub_komponen, $keyword) !== false);
            });

            if ($subKomponen) {
                return $subKomponen;
            }
        }

        return null;
    }

    /**
     * Membuat sub komponen baru jika tidak ada
     *
     * @param string $namaSubKomponen
     * @return \App\Models\Sub_Komponen
     */
    private function createSubKomponen($namaSubKomponen)
    {
        // Cek apakah sub komponen sudah ada
        $existingSubKomponen = Sub_Komponen::where('sub_komponen', $namaSubKomponen)->first();
        
        if ($existingSubKomponen) {
            return $existingSubKomponen;
        }

        // Buat sub komponen baru
        $subKomponen = new Sub_Komponen();
        $subKomponen->sub_komponen = $namaSubKomponen;
        $subKomponen->created_at = now();
        $subKomponen->updated_at = now();
        $subKomponen->save();

        $this->info("📝 Membuat sub komponen baru: {$namaSubKomponen}");
        
        return $subKomponen;
    }

    /**
     * Mendapatkan renstra aktif untuk tahun tertentu
     *
     * @param int $year
     * @return \App\Models\Renstra|null
     */
    private function getActiveRenstra($year)
    {
        $activeRenstra = Renstra::whereYear('periode_awal', '<=', $year)
            ->whereYear('periode_akhir', '>=', $year)
            ->orderBy('periode_akhir', 'desc')
            ->first();

        if (!$activeRenstra) {
            $activeRenstra = Renstra::orderBy('periode_akhir', 'desc')->first();
        }

        return $activeRenstra;
    }

    /**
     * Membuat folder Google Drive untuk kegiatan
     *
     * @param Kegiatan $kegiatan
     * @return void
     */
    private function createGoogleDriveFolder($kegiatan)
    {
        try {
            // Buat folder untuk kegiatan
            $folderResult = $this->googleDriveService->createKegiatanFolder(
                $kegiatan->nama_kegiatan,
                $kegiatan->tahun_berjalan
            );

            // Validasi hasil pembuatan folder
            if (!$folderResult['success'] || empty($folderResult['folder_id'])) {
                Log::error("Gagal membuat folder Google Drive untuk kegiatan", [
                    'kegiatan_id' => $kegiatan->id,
                    'nama_kegiatan' => $kegiatan->nama_kegiatan,
                    'tahun_berjalan' => $kegiatan->tahun_berjalan,
                    'folder_result' => $folderResult
                ]);

                // Kirim notifikasi atau lakukan tindakan alternatif
                throw new \Exception("Tidak dapat membuat folder Google Drive untuk kegiatan: {$kegiatan->nama_kegiatan}");
            }

            // Update kegiatan dengan folder_id
            $kegiatan->folder_id = $folderResult['folder_id'];
            $kegiatan->save();

            Log::info("Folder Google Drive berhasil dibuat untuk kegiatan", [
                'kegiatan_id' => $kegiatan->id,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'folder_id' => $folderResult['folder_id']
            ]);

            return $folderResult['folder_id'];

        } catch (\Exception $e) {
            Log::error("Error saat membuat folder Google Drive untuk kegiatan", [
                'kegiatan_id' => $kegiatan->id,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Kembalikan null atau lakukan tindakan alternatif
            return null;
        }
    }

    private function createTriwulanFolders($parentFolderId)
    {
        for ($i = 1; $i <= 4; $i++) {
            $folderName = "Triwulan {$i}";
            try {
                $this->googleDriveService->createFolder($folderName, $parentFolderId);
                $this->info("    ✅ Berhasil membuat subfolder: {$folderName}");
            } catch (\Exception $e) {
                $this->error("    ❌ Gagal membuat subfolder: {$folderName}");
                Log::error("Gagal membuat subfolder triwulan", [
                    'parent_folder_id' => $parentFolderId,
                    'folder_name' => $folderName,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}