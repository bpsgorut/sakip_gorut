<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleDriveOAuthService
{
    private $accessToken;

    public function __construct()
    {
        $this->accessToken = $this->getAccessToken();
    }

    /**
     * Mendapatkan token akses Google Drive (dari DokumenKegiatanController)
     */
    private function getAccessToken()
    {
        try {
            $client_id = config('services.google.client_id');
            $client_secret = config('services.google.client_secret');
            $refresh_token = config('services.google.refresh_token');

            $response = Http::post('https://oauth2.googleapis.com/token', [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token',
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            Log::error('Failed to get access token for folder creation', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Token generation error for folder creation', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get current access token (public method for other services)
     * 
     * @return string|null
     */
    public function getCurrentAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Membuat folder di Google Drive menggunakan OAuth
     *
     * @param string $folderName
     * @param string|null $parentFolderId
     * @return string|null ID folder yang dibuat
     */
    public function createFolder($folderName, $parentFolderId = null)
    {
        if (!$this->accessToken) {
            Log::error('No access token available for folder creation');
            return null;
        }

        try {
            $metadata = [
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
            ];

            if ($parentFolderId) {
                $metadata['parents'] = [$parentFolderId];
            }

            $response = Http::withToken($this->accessToken)
                ->post('https://www.googleapis.com/drive/v3/files', $metadata);

            if ($response->successful()) {
                $folderId = $response->json()['id'];
                
                Log::info("Google Drive folder created successfully via OAuth", [
                    'folder_name' => $folderName,
                    'folder_id' => $folderId,
                    'parent_folder_id' => $parentFolderId
                ]);

                return $folderId;
            } else {
                Log::error("Failed to create Google Drive folder via OAuth", [
                    'folder_name' => $folderName,
                    'parent_folder_id' => $parentFolderId,
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return null;
            }
        } catch (Exception $e) {
            Log::error("Exception creating Google Drive folder via OAuth", [
                'folder_name' => $folderName,
                'parent_folder_id' => $parentFolderId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Membuat folder-folder SAKIP untuk periode renstra menggunakan OAuth
     *
     * @param int $startYear
     * @param int $endYear
     * @param string|null $parentFolderId
     * @return array Array berisi ID folder yang berhasil dibuat
     */
    public function createSakipFoldersForPeriod($startYear, $endYear, $parentFolderId = null)
    {
        $createdFolders = [];
        
        // Jika tidak ada parent folder, gunakan folder SAKIP yang sudah ada
        if (!$parentFolderId) {
            $parentFolderId = config('services.google.folder_id'); // Main SAKIP folder
        }
        
        for ($year = $startYear; $year <= $endYear; $year++) {
            $folderName = "SAKIP BPS Kabupaten Belitung Tahun {$year}";
            
            // Cek dulu apakah folder sudah ada
            $existingFolderId = $this->findFolderByName($folderName, $parentFolderId);
            
            if ($existingFolderId) {
                Log::info("Folder already exists, skipping creation", [
                    'folder_name' => $folderName,
                    'existing_folder_id' => $existingFolderId
                ]);
                
                $createdFolders[] = [
                    'year' => $year,
                    'folder_name' => $folderName,
                    'folder_id' => $existingFolderId,
                    'status' => 'existing'
                ];
            } else {
                $folderId = $this->createFolder($folderName, $parentFolderId);
                
                if ($folderId) {
                    $createdFolders[] = [
                        'year' => $year,
                        'folder_name' => $folderName,
                        'folder_id' => $folderId,
                        'status' => 'created'
                    ];
                }
            }
        }

        return $createdFolders;
    }

    /**
     * Mencari folder berdasarkan nama menggunakan OAuth
     * Mengecualikan folder yang ada di Trash
     *
     * @param string $folderName
     * @param string|null $parentFolderId
     * @return string|null ID folder jika ditemukan
     */
    public function findFolderByName($folderName, $parentFolderId = null)
    {
        if (!$this->accessToken) {
            return null;
        }

        try {
            // Query untuk mencari folder yang TIDAK ada di trash
            $query = "name='{$folderName}' and mimeType='application/vnd.google-apps.folder' and trashed=false";
            
            if ($parentFolderId) {
                $query .= " and '{$parentFolderId}' in parents";
            }

            $response = Http::withToken($this->accessToken)
                ->get('https://www.googleapis.com/drive/v3/files', [
                    'q' => $query,
                    'fields' => 'files(id, name, trashed)',
                    'pageSize' => 1
                ]);

            if ($response->successful()) {
                $files = $response->json()['files'] ?? [];
                if (count($files) > 0) {
                    $folder = $files[0];
                    
                    // Double check: pastikan folder tidak di trash
                    if (!($folder['trashed'] ?? false)) {
                        Log::info("Active folder found (not in trash)", [
                            'folder_name' => $folderName,
                            'folder_id' => $folder['id'],
                            'parent_folder_id' => $parentFolderId
                        ]);
                        return $folder['id'];
                    }
                }
            }

            // Log jika tidak ditemukan folder aktif
            Log::info("No active folder found (excluding trash)", [
                'folder_name' => $folderName,
                'parent_folder_id' => $parentFolderId,
                'query' => $query
            ]);

            return null;
        } catch (Exception $e) {
            Log::error("Failed to find Google Drive folder via OAuth", [
                'folder_name' => $folderName,
                'parent_folder_id' => $parentFolderId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Membuat folder kegiatan di dalam folder tahun SAKIP
     *
     * @param string $namaKegiatan
     * @param int $tahunBerjalan
     * @return array Result array dengan informasi folder yang dibuat
     */
    public function createKegiatanFolder($namaKegiatan, $tahunBerjalan)
    {
        try {
            // Cari folder tahun SAKIP yang sesuai
            $mainSakipFolderId = config('services.google.folder_id'); // Main SAKIP folder
            $yearFolderName = "SAKIP BPS Kabupaten Belitung Tahun {$tahunBerjalan}";
            
            // Cari folder tahun
            $yearFolderId = $this->findFolderByName($yearFolderName, $mainSakipFolderId);
            
            if (!$yearFolderId) {
                // Jika folder tahun belum ada, buat terlebih dahulu
                Log::info("Year folder doesn't exist, creating it first", [
                    'year_folder_name' => $yearFolderName,
                    'parent_folder_id' => $mainSakipFolderId
                ]);
                
                $yearFolderId = $this->createFolder($yearFolderName, $mainSakipFolderId);
                
                if (!$yearFolderId) {
                    Log::error("Failed to create year folder for kegiatan", [
                        'year_folder_name' => $yearFolderName,
                        'kegiatan_name' => $namaKegiatan
                    ]);
                    return [
                        'success' => false,
                        'message' => "Gagal membuat folder tahun {$tahunBerjalan}",
                        'folder_id' => null
                    ];
                }
            }
            
            // Cek apakah folder kegiatan sudah ada
            $existingKegiatanFolderId = $this->findFolderByName($namaKegiatan, $yearFolderId);
            
            if ($existingKegiatanFolderId) {
                Log::info("Kegiatan folder already exists", [
                    'kegiatan_name' => $namaKegiatan,
                    'year' => $tahunBerjalan,
                    'existing_folder_id' => $existingKegiatanFolderId
                ]);
                
                return [
                    'success' => true,
                    'message' => "Folder kegiatan '{$namaKegiatan}' sudah ada di tahun {$tahunBerjalan}",
                    'folder_id' => $existingKegiatanFolderId,
                    'status' => 'existing'
                ];
            }
            
            // Buat folder kegiatan baru
            $kegiatanFolderId = $this->createFolder($namaKegiatan, $yearFolderId);
            
            if ($kegiatanFolderId) {
                Log::info("Kegiatan folder created successfully", [
                    'kegiatan_name' => $namaKegiatan,
                    'year' => $tahunBerjalan,
                    'folder_id' => $kegiatanFolderId,
                    'year_folder_id' => $yearFolderId
                ]);
                
                return [
                    'success' => true,
                    'message' => "Folder kegiatan '{$namaKegiatan}' berhasil dibuat di tahun {$tahunBerjalan}",
                    'folder_id' => $kegiatanFolderId,
                    'year_folder_id' => $yearFolderId,
                    'status' => 'created'
                ];
            } else {
                Log::error("Failed to create kegiatan folder", [
                    'kegiatan_name' => $namaKegiatan,
                    'year' => $tahunBerjalan,
                    'year_folder_id' => $yearFolderId
                ]);
                
                return [
                    'success' => false,
                    'message' => "Gagal membuat folder kegiatan '{$namaKegiatan}' di tahun {$tahunBerjalan}",
                    'folder_id' => null
                ];
            }
            
        } catch (Exception $e) {
            Log::error("Exception creating kegiatan folder", [
                'kegiatan_name' => $namaKegiatan,
                'year' => $tahunBerjalan,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => "Error saat membuat folder kegiatan: " . $e->getMessage(),
                'folder_id' => null
            ];
        }
    }

    /**
     * Mencari atau membuat folder untuk kegiatan tertentu dan mengembalikan folder ID
     *
     * @param \App\Models\Kegiatan $kegiatan
     * @return string|null
     */
    public function getKegiatanFolderId($kegiatan)
    {
        try {
            // Jika kegiatan sudah punya folder_id yang tersimpan, gunakan itu
            if ($kegiatan->folder_id) {
                // Verifikasi folder masih ada di Google Drive
                $response = Http::withToken($this->accessToken)
                    ->get("https://www.googleapis.com/drive/v3/files/{$kegiatan->folder_id}", [
                        'fields' => 'id, name, trashed'
                    ]);

                if ($response->successful()) {
                    $fileInfo = $response->json();
                    // Jika folder masih ada dan tidak di trash, gunakan folder ini
                    if (!($fileInfo['trashed'] ?? false)) {
                        return $kegiatan->folder_id;
                    }
                }
            }

            // Jika tidak ada folder_id tersimpan atau folder sudah dihapus, cari/buat folder baru
            $folderResult = $this->createKegiatanFolder($kegiatan->nama_kegiatan, $kegiatan->tahun_berjalan);
            
            if ($folderResult['success'] && isset($folderResult['folder_id'])) {
                // Simpan folder_id ke database
                $kegiatan->update(['folder_id' => $folderResult['folder_id']]);
                
                Log::info("Kegiatan folder ID updated in database", [
                    'kegiatan_id' => $kegiatan->id,
                    'folder_id' => $folderResult['folder_id']
                ]);
                
                return $folderResult['folder_id'];
            }
            
            Log::error("Failed to get/create kegiatan folder", [
                'kegiatan_id' => $kegiatan->id,
                'kegiatan_name' => $kegiatan->nama_kegiatan,
                'year' => $kegiatan->tahun_berjalan
            ]);
            
            return null;
            
        } catch (Exception $e) {
            Log::error("Exception getting kegiatan folder ID", [
                'kegiatan_id' => $kegiatan->id,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Cek apakah file dengan nama tertentu sudah ada di folder (excluding trash)
     *
     * @param string $fileName
     * @param string $folderId
     * @return array|null File info jika ditemukan
     */
    public function findFileByName($fileName, $folderId)
    {
        if (!$this->accessToken) {
            return null;
        }

        try {
            // Query untuk mencari file yang TIDAK ada di trash
            $query = "name='{$fileName}' and '{$folderId}' in parents and trashed=false";

            $response = Http::withToken($this->accessToken)
                ->get('https://www.googleapis.com/drive/v3/files', [
                    'q' => $query,
                    'fields' => 'files(id, name, trashed, createdTime, modifiedTime)',
                    'pageSize' => 10
                ]);

            if ($response->successful()) {
                $files = $response->json()['files'] ?? [];
                
                // Filter files yang tidak di trash (double check)
                $activeFiles = array_filter($files, function($file) {
                    return !($file['trashed'] ?? false);
                });
                
                if (!empty($activeFiles)) {
                    Log::info("Active file found (not in trash)", [
                        'file_name' => $fileName,
                        'folder_id' => $folderId,
                        'found_files_count' => count($activeFiles)
                    ]);
                    
                    return array_values($activeFiles)[0]; // Return first active file
                }
            }

            return null;
        } catch (Exception $e) {
            Log::error("Failed to find file in Google Drive", [
                'file_name' => $fileName,
                'folder_id' => $folderId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Cek apakah file serupa sudah ada berdasarkan jenis dokumen dan kegiatan (excluding trash)
     *
     * @param string $jenisDokumen
     * @param int $kegiatanId
     * @param string $folderId
     * @param int|null $bulan For SKP bulanan
     * @param int|null $triwulan For reward punishment docs
     * @return array|null File info jika ditemukan duplikat
     */
    public function findDuplicateDocument($jenisDokumen, $kegiatanId, $folderId, $bulan = null, $triwulan = null)
    {
        if (!$this->accessToken) {
            return null;
        }

        try {
            // Generate pattern berdasarkan jenis dokumen
            $searchPattern = $this->getSearchPatternForDocument($jenisDokumen, $bulan, $triwulan);
            
            if (!$searchPattern) {
                return null;
            }

            // Query untuk mencari file yang TIDAK ada di trash dengan pattern nama tertentu
            $query = "name contains '{$searchPattern}' and '{$folderId}' in parents and trashed=false";

            $response = Http::withToken($this->accessToken)
                ->get('https://www.googleapis.com/drive/v3/files', [
                    'q' => $query,
                    'fields' => 'files(id, name, trashed, createdTime, modifiedTime)',
                    'pageSize' => 20
                ]);

            if ($response->successful()) {
                $files = $response->json()['files'] ?? [];
                
                // Filter files yang tidak di trash dan cocok dengan pattern
                $duplicateFiles = array_filter($files, function($file) use ($searchPattern) {
                    return !($file['trashed'] ?? false) && 
                           stripos($file['name'], $searchPattern) !== false;
                });
                
                if (!empty($duplicateFiles)) {
                    Log::info("Duplicate document found", [
                        'jenis_dokumen' => $jenisDokumen,
                        'kegiatan_id' => $kegiatanId,
                        'search_pattern' => $searchPattern,
                        'folder_id' => $folderId,
                        'found_files_count' => count($duplicateFiles),
                        'found_files' => array_map(function($file) {
                            return ['id' => $file['id'], 'name' => $file['name']];
                        }, $duplicateFiles)
                    ]);
                    
                    return array_values($duplicateFiles)[0]; // Return first duplicate
                }
            }

            return null;
        } catch (Exception $e) {
            Log::error("Failed to find duplicate document", [
                'jenis_dokumen' => $jenisDokumen,
                'kegiatan_id' => $kegiatanId,
                'folder_id' => $folderId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Generate search pattern untuk jenis dokumen tertentu
     */
    private function getSearchPatternForDocument($jenisDokumen, $bulan = null, $triwulan = null)
    {
        switch ($jenisDokumen) {
            case 'skp_bulanan':
                $bulanPadded = str_pad($bulan, 2, '0', STR_PAD_LEFT);
                return "SKP_Bulanan_{$bulanPadded}";
            case 'skp_tahunan':
                return "SKP_Tahunan";
            case 'notulensi':
                return "Notulensi_";
            case 'surat_undangan':
                return "Surat_Undangan_";
            case 'daftar_hadir':
                return "Daftar_Hadir_";
            case 'dokumentasi':
                return "Dokumentasi_";
            case 'penetapan_mekanisme':
                return "Penetapan_Mekanisme_Reward_Punishment";
            case 'sk_penerima_triwulan':
                return "SK_Penerima_Pegawai_Terbaik_Triwulan_{$triwulan}";
            case 'piagam_penghargaan_triwulan':
                return "Piagam_Penghargaan_Triwulan_{$triwulan}";
            case 'rekap_pemilihan_triwulan':
                return "Rekap_Pemilihan_Pegawai_Terbaik_Triwulan_{$triwulan}";
            default:
                return null;
        }
    }

    /**
     * Move file to trash in Google Drive
     *
     * @param string $fileId
     * @return array
     */
    public function moveToTrash($fileId)
    {
        if (!$this->accessToken) {
            return [
                'success' => false,
                'message' => 'No access token available'
            ];
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->patch("https://www.googleapis.com/drive/v3/files/{$fileId}", [
                    'trashed' => true
                ]);

            if ($response->successful()) {
                Log::info("File moved to trash successfully", [
                    'file_id' => $fileId
                ]);
                return [
                    'success' => true,
                    'message' => 'File moved to trash successfully'
                ];
            }

            Log::error("Failed to move file to trash", [
                'file_id' => $fileId,
                'response' => $response->body()
            ]);
            return [
                'success' => false,
                'message' => 'Failed to move file to trash: ' . $response->body()
            ];
        } catch (Exception $e) {
            Log::error("Exception moving file to trash", [
                'file_id' => $fileId,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => 'Exception moving file to trash: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Membuat folder Renstra periode di Google Drive
     *
     * @param int $tahunAwal
     * @param int $tahunAkhir
     * @return array Result array dengan informasi folder yang dibuat
     */
    public function createRenstraPeriodeFolder($tahunAwal, $tahunAkhir)
    {
        try {
            $mainSakipFolderId = config('services.google.folder_id'); // Main SAKIP folder
            $folderName = "Renstra Periode {$tahunAwal}-{$tahunAkhir}";
            
            // Cek apakah folder Renstra periode sudah ada
            $existingFolderId = $this->findFolderByName($folderName, $mainSakipFolderId);
            
            if ($existingFolderId) {
                Log::info("Renstra periode folder already exists", [
                    'folder_name' => $folderName,
                    'existing_folder_id' => $existingFolderId,
                    'tahun_awal' => $tahunAwal,
                    'tahun_akhir' => $tahunAkhir
                ]);
                
                return [
                    'success' => true,
                    'message' => "Folder Renstra periode {$tahunAwal}-{$tahunAkhir} sudah ada",
                    'folder_id' => $existingFolderId,
                    'status' => 'existing'
                ];
            }
            
            // Buat folder Renstra periode baru
            $folderId = $this->createFolder($folderName, $mainSakipFolderId);
            
            if ($folderId) {
                Log::info("Renstra periode folder created successfully", [
                    'folder_name' => $folderName,
                    'folder_id' => $folderId,
                    'tahun_awal' => $tahunAwal,
                    'tahun_akhir' => $tahunAkhir,
                    'parent_folder_id' => $mainSakipFolderId
                ]);
                
                return [
                    'success' => true,
                    'message' => "Folder Renstra periode {$tahunAwal}-{$tahunAkhir} berhasil dibuat",
                    'folder_id' => $folderId,
                    'status' => 'created'
                ];
            } else {
                Log::error("Failed to create Renstra periode folder", [
                    'folder_name' => $folderName,
                    'tahun_awal' => $tahunAwal,
                    'tahun_akhir' => $tahunAkhir,
                    'parent_folder_id' => $mainSakipFolderId
                ]);
                
                return [
                    'success' => false,
                    'message' => "Gagal membuat folder Renstra periode {$tahunAwal}-{$tahunAkhir}",
                    'folder_id' => null
                ];
            }
            
        } catch (Exception $e) {
            Log::error("Exception creating Renstra periode folder", [
                'tahun_awal' => $tahunAwal,
                'tahun_akhir' => $tahunAkhir,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => "Error saat membuat folder Renstra periode: " . $e->getMessage(),
                'folder_id' => null
            ];
        }
    }

    /**
     * Mencari atau membuat folder untuk Renstra tertentu dan mengembalikan folder ID
     *
     * @param \App\Models\Renstra $renstra
     * @return string|null
     */
    public function getRenstraFolderId($renstra)
    {
        try {
            // Jika renstra sudah punya folder_id yang tersimpan, gunakan itu
            if ($renstra->folder_id) {
                // Verifikasi folder masih ada di Google Drive
                $response = Http::withToken($this->accessToken)
                    ->get("https://www.googleapis.com/drive/v3/files/{$renstra->folder_id}", [
                        'fields' => 'id, name, trashed'
                    ]);

                if ($response->successful()) {
                    $fileInfo = $response->json();
                    // Jika folder masih ada dan tidak di trash, gunakan folder ini
                    if (!($fileInfo['trashed'] ?? false)) {
                        return $renstra->folder_id;
                    }
                }
            }

            // Jika tidak ada folder_id tersimpan atau folder sudah dihapus, cari/buat folder baru
            $tahunAwal = date('Y', strtotime($renstra->periode_awal));
            $tahunAkhir = date('Y', strtotime($renstra->periode_akhir));
            
            $folderResult = $this->createRenstraPeriodeFolder($tahunAwal, $tahunAkhir);
            
            if ($folderResult['success'] && isset($folderResult['folder_id'])) {
                // Simpan folder_id ke database
                $renstra->update(['folder_id' => $folderResult['folder_id']]);
                
                Log::info("Renstra folder ID updated in database", [
                    'renstra_id' => $renstra->id,
                    'folder_id' => $folderResult['folder_id'],
                    'periode' => "{$tahunAwal}-{$tahunAkhir}"
                ]);
                
                return $folderResult['folder_id'];
            }
            
            Log::error("Failed to get/create Renstra folder", [
                'renstra_id' => $renstra->id,
                'periode_awal' => $renstra->periode_awal,
                'periode_akhir' => $renstra->periode_akhir
            ]);
            
            return null;
            
        } catch (Exception $e) {
            Log::error("Exception getting Renstra folder ID", [
                'renstra_id' => $renstra->id,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Mengunggah file ke folder tertentu di Google Drive.
     *
     * @param string|\Illuminate\Http\UploadedFile $file File path or UploadedFile object
     * @param string $fileName
     * @param string $folderId
     * @return array|null
     */
    public function uploadFile($file, $fileName, $folderId)
    {
        if (!$this->accessToken) {
            Log::error('No access token available for file upload');
            return null;
        }

        try {
            // Handle both file path string and UploadedFile object
            $filePath = is_string($file) ? $file : $file->getRealPath();
            
            // Unggah file ke Google Drive
            $response = Http::withToken($this->accessToken)
                ->attach('metadata', json_encode([
                    'name' => $fileName,
                    'parents' => [$folderId],
                ]), 'metadata.json')
                ->attach('file', file_get_contents($filePath), $fileName)
                ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

            if ($response->successful()) {
                $fileId = $response->json()['id'];

                // Dapatkan detail file termasuk webViewLink
                $fileDetails = Http::withToken($this->accessToken)
                    ->get("https://www.googleapis.com/drive/v3/files/{$fileId}", [
                        'fields' => 'id, name, webViewLink'
                    ]);

                if ($fileDetails->successful()) {
                    $fileInfo = $fileDetails->json();
                    return [
                        'success' => true,
                        'file_id' => $fileInfo['id'],
                        'webViewLink' => $fileInfo['webViewLink'] ?? null,
                    ];
                }
            }
            
            Log::error('File upload to Google Drive failed', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            return null;

        } catch (Exception $e) {
            Log::error("Exception uploading file to Google Drive", [
                'file_name' => $fileName,
                'folder_id' => $folderId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create folder Form Rencana Aksi for specific year
     *
     * @param int $year
     * @return string|null Folder ID
     */
    public function createFormRencanaAksiFolder($year)
    {
        try {
            // Find or create year folder first
            $yearFolderName = "SAKIP BPS Kabupaten Belitung Tahun {$year}";
            $mainSakipFolderId = config('services.google.folder_id');
            
            $yearFolderId = $this->findFolderByName($yearFolderName, $mainSakipFolderId);
            if (!$yearFolderId) {
                $yearFolderId = $this->createFolder($yearFolderName, $mainSakipFolderId);
            }

            if (!$yearFolderId) {
                Log::error("Failed to get/create year folder for FRA", [
                    'year' => $year
                ]);
                return null;
            }

            // Create or find Form Rencana Aksi folder
            $fraFolderName = "Form Rencana Aksi";
            $fraFolderId = $this->findFolderByName($fraFolderName, $yearFolderId);
            
            if (!$fraFolderId) {
                $fraFolderId = $this->createFolder($fraFolderName, $yearFolderId);
                Log::info("Form Rencana Aksi folder created", [
                    'year' => $year,
                    'folder_id' => $fraFolderId,
                    'parent_folder_id' => $yearFolderId
                ]);
                
                // Create triwulan folders inside FRA folder
                $this->createTriwulanFolders($fraFolderId, 'FRA');
            }

            return $fraFolderId;

        } catch (Exception $e) {
            Log::error("Exception creating Form Rencana Aksi folder", [
                'year' => $year,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create triwulan folders (1-4) inside a parent folder
     *
     * @param string $parentFolderId
     * @param string $type - 'FRA' or 'Capaian Kinerja'
     * @return array Array of created folder IDs
     */
    public function createTriwulanFolders($parentFolderId, $type = 'Capaian Kinerja')
    {
        $createdFolders = [];
        
        try {
            for ($i = 1; $i <= 4; $i++) {
                $triwulanFolderName = "Triwulan {$i}";
                
                // Check if folder already exists
                $existingFolderId = $this->findFolderByName($triwulanFolderName, $parentFolderId);
                
                if ($existingFolderId) {
                    $createdFolders[$i] = $existingFolderId;
                    Log::info("Triwulan folder already exists", [
                        'triwulan' => $i,
                        'type' => $type,
                        'parent_folder_id' => $parentFolderId,
                        'folder_id' => $existingFolderId
                    ]);
                } else {
                    // Create new triwulan folder
                    $folderId = $this->createFolder($triwulanFolderName, $parentFolderId);
                    
                    if ($folderId) {
                        $createdFolders[$i] = $folderId;
                        Log::info("Triwulan folder created successfully", [
                            'triwulan' => $i,
                            'type' => $type,
                            'parent_folder_id' => $parentFolderId,
                            'folder_id' => $folderId
                        ]);
                    } else {
                        Log::error("Failed to create triwulan folder", [
                            'triwulan' => $i,
                            'type' => $type,
                            'parent_folder_id' => $parentFolderId
                        ]);
                    }
                }
            }
            
            return $createdFolders;
            
        } catch (Exception $e) {
            Log::error("Exception creating triwulan folders", [
                'parent_folder_id' => $parentFolderId,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            
            return $createdFolders;
        }
    }

    /**
     * Get specific triwulan folder ID for a parent folder
     *
     * @param string $parentFolderId
     * @param int $triwulan (1-4)
     * @return string|null
     */
    public function getTriwulanFolderId($parentFolderId, $triwulan)
    {
        try {
            $triwulanFolderName = "Triwulan {$triwulan}";
            $folderId = $this->findFolderByName($triwulanFolderName, $parentFolderId);
            
            if (!$folderId) {
                // Create the triwulan folder if it doesn't exist
                $folderId = $this->createFolder($triwulanFolderName, $parentFolderId);
                
                if ($folderId) {
                    Log::info("Triwulan folder created on demand", [
                        'triwulan' => $triwulan,
                        'parent_folder_id' => $parentFolderId,
                        'folder_id' => $folderId
                    ]);
                }
            }
            
            return $folderId;
            
        } catch (Exception $e) {
            Log::error("Exception getting triwulan folder ID", [
                'parent_folder_id' => $parentFolderId,
                'triwulan' => $triwulan,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Get all triwulan folders for a parent folder
     *
     * @param string $parentFolderId
     * @return array Array of triwulan folder data
     */
    public function getTriwulanFolders($parentFolderId)
    {
        try {
            $triwulanFolders = [];
            
            for ($i = 1; $i <= 4; $i++) {
                $triwulanFolderId = $this->getTriwulanFolderId($parentFolderId, $i);
                if ($triwulanFolderId) {
                    $triwulanFolders[] = [
                        'triwulan' => $i,
                        'folder_id' => $triwulanFolderId,
                        'nama' => "Triwulan {$i}"
                    ];
                }
            }
            
            return $triwulanFolders;
        } catch (Exception $e) {
            Log::error("Error getting triwulan folders", [
                'parent_folder_id' => $parentFolderId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Create capaian kinerja folder with triwulan subfolders
     *
     * @param string $namaKegiatan
     * @param int $tahunBerjalan
     * @return array Result array with folder information
     */
    public function createCapaianKinerjaFolder($namaKegiatan, $tahunBerjalan)
    {
        try {
            // Create main capaian kinerja folder using existing method
            $result = $this->createKegiatanFolder($namaKegiatan, $tahunBerjalan);
            
            if ($result['success'] && isset($result['folder_id'])) {
                // Create triwulan folders inside the capaian kinerja folder
                $triwulanFolders = $this->createTriwulanFolders($result['folder_id'], 'Capaian Kinerja');
                
                $result['triwulan_folders'] = $triwulanFolders;
                
                Log::info("Capaian Kinerja folder created with triwulan subfolders", [
                    'kegiatan_name' => $namaKegiatan,
                    'year' => $tahunBerjalan,
                    'main_folder_id' => $result['folder_id'],
                    'triwulan_folders' => $triwulanFolders
                ]);
            }
            
            return $result;
            
        } catch (Exception $e) {
            Log::error("Exception creating Capaian Kinerja folder with triwulan subfolders", [
                'kegiatan_name' => $namaKegiatan,
                'year' => $tahunBerjalan,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => "Error saat membuat folder Capaian Kinerja: " . $e->getMessage(),
                'folder_id' => null
            ];
        }
    }

    /**
     * Create reward punishment folder with triwulan subfolders
     *
     * @param string $namaKegiatan
     * @param int $tahunBerjalan
     * @return array Result array with folder information
     */
    public function createRewardPunishmentFolder($namaKegiatan, $tahunBerjalan)
    {
        try {
            // Create main reward punishment folder using existing method
            $result = $this->createKegiatanFolder($namaKegiatan, $tahunBerjalan);
            
            if ($result['success'] && isset($result['folder_id'])) {
                // Create triwulan folders inside the reward punishment folder
                $triwulanFolders = $this->createTriwulanFolders($result['folder_id'], 'Reward Punishment');
                
                $result['triwulan_folders'] = $triwulanFolders;
                
                Log::info("Reward Punishment folder created with triwulan subfolders", [
                    'kegiatan_name' => $namaKegiatan,
                    'year' => $tahunBerjalan,
                    'main_folder_id' => $result['folder_id'],
                    'triwulan_folders' => $triwulanFolders
                ]);
            }
            
            return $result;
            
        } catch (Exception $e) {
            Log::error("Exception creating Reward Punishment folder with triwulan subfolders", [
                'kegiatan_name' => $namaKegiatan,
                'year' => $tahunBerjalan,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => "Error saat membuat folder Reward Punishment: " . $e->getMessage(),
                'folder_id' => null
            ];
        }
    }

    /**
     * Create all monthly folders for SKP Bulanan
     *
     * @param string $skpBulananFolderId Parent SKP Bulanan folder ID
     * @return array Array of created monthly folder IDs
     */
    public function createAllMonthlyFolders($skpBulananFolderId)
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $monthlyFolders = [];
        
        foreach ($months as $monthNumber => $monthName) {
            try {
                // Check if folder already exists
                $existingFolderId = $this->findFolderByName($monthName, $skpBulananFolderId);
                
                if ($existingFolderId) {
                    $monthlyFolders[$monthNumber] = [
                        'month_number' => $monthNumber,
                        'month_name' => $monthName,
                        'folder_id' => $existingFolderId,
                        'status' => 'existing'
                    ];
                } else {
                    // Create new monthly folder
                    $newFolderId = $this->createFolder($monthName, $skpBulananFolderId);
                    
                    if ($newFolderId) {
                        $monthlyFolders[$monthNumber] = [
                            'month_number' => $monthNumber,
                            'month_name' => $monthName,
                            'folder_id' => $newFolderId,
                            'status' => 'created'
                        ];
                        
                        Log::info('Monthly folder created automatically', [
                            'month' => $monthName,
                            'folder_id' => $newFolderId,
                            'parent_folder_id' => $skpBulananFolderId
                        ]);
                    } else {
                        Log::warning('Failed to create monthly folder', [
                            'month' => $monthName,
                            'parent_folder_id' => $skpBulananFolderId
                        ]);
                    }
                }
            } catch (Exception $e) {
                Log::error('Exception creating monthly folder', [
                    'month' => $monthName,
                    'parent_folder_id' => $skpBulananFolderId,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $monthlyFolders;
    }

    /**
     * Create SKP folder with annual and monthly subfolders
     *
     * @param string $namaKegiatan
     * @param int $tahunBerjalan
     * @return array Result array with folder information
     */
    public function createSKPFolder($namaKegiatan, $tahunBerjalan)
    {
        try {
            // Get or create main kegiatan folder first
            $kegiatanResult = $this->createKegiatanFolder($namaKegiatan, $tahunBerjalan);
            
            if (!$kegiatanResult['success'] || !isset($kegiatanResult['folder_id'])) {
                return $kegiatanResult;
            }
            
            $mainKegiatanFolderId = $kegiatanResult['folder_id'];
            
            // Create "SKP Tahunan" subfolder directly in kegiatan folder
            $skpTahunanFolderId = $this->findFolderByName('SKP Tahunan', $mainKegiatanFolderId);
            if (!$skpTahunanFolderId) {
                $skpTahunanFolderId = $this->createFolder('SKP Tahunan', $mainKegiatanFolderId);
            }
            
            // Create "SKP Bulanan" subfolder directly in kegiatan folder
            $skpBulananFolderId = $this->findFolderByName('SKP Bulanan', $mainKegiatanFolderId);
            if (!$skpBulananFolderId) {
                $skpBulananFolderId = $this->createFolder('SKP Bulanan', $mainKegiatanFolderId);
            }
            
            if (!$skpTahunanFolderId || !$skpBulananFolderId) {
                throw new Exception('Gagal membuat subfolder SKP');
            }
            
            // Create all monthly folders automatically
            $monthlyFolders = $this->createAllMonthlyFolders($skpBulananFolderId);
            
            // Determine message based on whether folder was existing or created
            $message = $kegiatanResult['status'] === 'existing' ? 
                'Folder SKP sudah tersedia dan siap digunakan' : 
                'Folder SKP berhasil dibuat';
            
            $result = [
                'success' => true,
                'message' => $message,
                'folder_id' => $mainKegiatanFolderId,
                'status' => $kegiatanResult['status'],
                'skp_folders' => [
                    'skp_main_folder_id' => $mainKegiatanFolderId,
                    'skp_tahunan_folder_id' => $skpTahunanFolderId,
                    'skp_bulanan_folder_id' => $skpBulananFolderId,
                    'monthly_folders' => $monthlyFolders
                ]
            ];
            
            Log::info("SKP folder structure ready with all monthly folders", [
                'kegiatan_name' => $namaKegiatan,
                'year' => $tahunBerjalan,
                'kegiatan_folder_id' => $mainKegiatanFolderId,
                'skp_tahunan_folder_id' => $skpTahunanFolderId,
                'skp_bulanan_folder_id' => $skpBulananFolderId,
                'monthly_folders_count' => count($monthlyFolders),
                'status' => $kegiatanResult['status']
            ]);
            
            return $result;
            
        } catch (Exception $e) {
            Log::error("Exception creating SKP folder structure", [
                'kegiatan_name' => $namaKegiatan,
                'year' => $tahunBerjalan,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Gagal membuat struktur folder SKP: ' . $e->getMessage(),
                'folder_id' => null
            ];
        }
    }

    /**
     * Get monthly folder ID for SKP Bulanan
     *
     * @param string $skpBulananFolderId Parent SKP Bulanan folder ID
     * @param int $bulan Month number (1-12)
     * @return string|null Monthly folder ID or null if not found
     */
    public function getMonthlyFolderId($skpBulananFolderId, $bulan)
    {
        try {
            // Map month number to month name
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            if (!isset($months[$bulan])) {
                Log::warning('Invalid month number provided', ['bulan' => $bulan]);
                return null;
            }

            $monthName = $months[$bulan];
            $accessToken = $this->accessToken;

            if (!$accessToken) {
                Log::error('Failed to get access token for monthly folder search');
                return null;
            }

            // Search for the monthly folder within SKP Bulanan folder
            $response = Http::withToken($accessToken)
                ->get('https://www.googleapis.com/drive/v3/files', [
                    'q' => "name='{$monthName}' and parents in '{$skpBulananFolderId}' and mimeType='application/vnd.google-apps.folder' and trashed=false",
                    'fields' => 'files(id, name)'
                ]);

            if ($response->successful()) {
                $files = $response->json()['files'] ?? [];
                
                if (!empty($files)) {
                    $folderId = $files[0]['id'];
                    Log::info('Monthly folder found', [
                        'month' => $monthName,
                        'folder_id' => $folderId,
                        'parent_folder_id' => $skpBulananFolderId
                    ]);
                    return $folderId;
                } else {
                    // Monthly folder doesn't exist, create it
                    $newFolderId = $this->createFolder($monthName, $skpBulananFolderId);
                    if ($newFolderId) {
                        Log::info('Monthly folder created', [
                            'month' => $monthName,
                            'folder_id' => $newFolderId,
                            'parent_folder_id' => $skpBulananFolderId
                        ]);
                        return $newFolderId;
                    }
                }
            } else {
                Log::error('Failed to search for monthly folder', [
                    'month' => $monthName,
                    'parent_folder_id' => $skpBulananFolderId,
                    'response_status' => $response->status(),
                    'response_body' => $response->body()
                ]);
            }

            return null;
            
        } catch (Exception $e) {
            Log::error('Exception getting monthly folder ID', [
                'parent_folder_id' => $skpBulananFolderId,
                'bulan' => $bulan,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Mendapatkan folder ID untuk SKP Tahunan dalam folder SKP utama
     *
     * @param string $skpFolderId
     * @return string|null
     */
    public function getYearlyFolderId($skpFolderId)
    {
        try {
            // Cari folder "SKP Tahunan" yang sudah ada
            $existingYearlyFolderId = $this->findFolderByName('SKP Tahunan', $skpFolderId);
            
            if ($existingYearlyFolderId) {
                Log::info('SKP Tahunan folder found', [
                    'folder_id' => $existingYearlyFolderId,
                    'parent_folder_id' => $skpFolderId
                ]);
                return $existingYearlyFolderId;
            }

            // Jika folder SKP Tahunan belum ada, buat folder baru
            $yearlyFolderId = $this->createFolder('SKP Tahunan', $skpFolderId);
            
            if ($yearlyFolderId) {
                Log::info('SKP Tahunan folder created successfully', [
                    'folder_id' => $yearlyFolderId,
                    'parent_folder_id' => $skpFolderId
                ]);
                return $yearlyFolderId;
            }

            Log::error('Failed to create SKP Tahunan folder', [
                'parent_folder_id' => $skpFolderId
            ]);
            return null;
            
        } catch (Exception $e) {
            Log::error('Error getting SKP Tahunan folder ID', [
                'skp_folder_id' => $skpFolderId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Mendapatkan folder ID untuk SKP berdasarkan kegiatan_id
     * Struktur: Kegiatan Folder -> SKP Tahunan/SKP Bulanan (tidak ada folder SKP terpisah)
     *
     * @param int $kegiatanId
     * @return string|null Returns kegiatan folder ID (parent of SKP folders)
     */
    public function getSkpFolderId($kegiatanId)
    {
        try {
            $kegiatan = \App\Models\Kegiatan::findOrFail($kegiatanId);
            
            // Get kegiatan folder first - this is the parent of SKP Tahunan and SKP Bulanan
            $kegiatanFolderId = $this->getKegiatanFolderId($kegiatan);
            
            if (!$kegiatanFolderId) {
                Log::error('Kegiatan folder not found', ['kegiatan_id' => $kegiatanId]);
                return null;
            }
            
            Log::info('SKP folder structure accessed', [
                'kegiatan_folder_id' => $kegiatanFolderId,
                'kegiatan_id' => $kegiatanId
            ]);
            
            // Return kegiatan folder ID as the parent of SKP folders
            return $kegiatanFolderId;
            
        } catch (Exception $e) {
            Log::error('Error getting SKP folder ID', [
                'kegiatan_id' => $kegiatanId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Download file dari Google Drive
     *
     * @param string $fileId
     * @return string|null
     */
    public function downloadFile($fileId)
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->get("https://www.googleapis.com/drive/v3/files/{$fileId}", [
                    'alt' => 'media'
                ]);

            if ($response->successful()) {
                Log::info('File downloaded successfully from Google Drive', [
                    'file_id' => $fileId
                ]);
                return $response->body();
            }

            Log::error('Failed to download file from Google Drive', [
                'file_id' => $fileId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return null;
            
        } catch (Exception $e) {
            Log::error('Error downloading file from Google Drive', [
                'file_id' => $fileId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Update nama file di Google Drive
     *
     * @param string $fileId
     * @param string $newFileName
     * @return bool
     */
    public function updateFileName($fileId, $newFileName)
    {
        // Enhanced logging for debugging hosting issues
        Log::info('Google Drive Config Check', [
            'client_id_set' => config('services.google.client_id') ? true : false,
            'client_secret_set' => config('services.google.client_secret') ? true : false,
            'refresh_token_set' => config('services.google.refresh_token') ? true : false,
            'access_token_available' => $this->accessToken ? true : false,
            'file_id' => $fileId,
            'new_filename' => $newFileName
        ]);

        if (!$this->accessToken) {
            Log::error('No access token available for file rename', [
                'client_id' => config('services.google.client_id') ? 'SET' : 'NOT SET',
                'client_secret' => config('services.google.client_secret') ? 'SET' : 'NOT SET',
                'refresh_token' => config('services.google.refresh_token') ? 'SET' : 'NOT SET'
            ]);
            return false;
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->patch("https://www.googleapis.com/drive/v3/files/{$fileId}", [
                    'name' => $newFileName
                ]);

            if ($response->successful()) {
                Log::info('File renamed successfully in Google Drive', [
                    'file_id' => $fileId,
                    'new_name' => $newFileName
                ]);
                return true;
            }

            Log::error('Failed to rename file in Google Drive', [
                'file_id' => $fileId,
                'new_name' => $newFileName,
                'status' => $response->status(),
                'response' => $response->body(),
                'request_url' => "https://www.googleapis.com/drive/v3/files/{$fileId}",
                'access_token_length' => strlen($this->accessToken ?? '')
            ]);
            return false;
            
        } catch (Exception $e) {
            Log::error('Error renaming file in Google Drive', [
                'file_id' => $fileId,
                'new_name' => $newFileName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Menghapus folder SAKIP untuk periode renstra
     * Menghapus folder "SAKIP BPS Kabupaten Belitung Tahun {year}" untuk setiap tahun dalam periode
     *
     * @param int $startYear
     * @param int $endYear
     * @return array Result array dengan informasi folder yang dihapus
     */
    public function deleteSakipFoldersForPeriod($startYear, $endYear)
    {
        $deletedFolders = [];
        $mainSakipFolderId = config('services.google.folder_id'); // Main SAKIP folder
        
        try {
            for ($year = $startYear; $year <= $endYear; $year++) {
                $folderName = "SAKIP BPS Kabupaten Belitung Tahun {$year}";
                
                // Cari folder tahun yang akan dihapus
                $yearFolderId = $this->findFolderByName($folderName, $mainSakipFolderId);
                
                if ($yearFolderId) {
                    try {
                        // Pindahkan folder ke trash
                        $this->moveToTrash($yearFolderId);
                        
                        $deletedFolders[] = [
                            'year' => $year,
                            'folder_name' => $folderName,
                            'folder_id' => $yearFolderId,
                            'status' => 'deleted'
                        ];
                        
                        Log::info("SAKIP folder untuk tahun {$year} berhasil dipindahkan ke sampah", [
                            'folder_name' => $folderName,
                            'folder_id' => $yearFolderId
                        ]);
                    } catch (\Exception $e) {
                        $deletedFolders[] = [
                            'year' => $year,
                            'folder_name' => $folderName,
                            'folder_id' => $yearFolderId,
                            'status' => 'error',
                            'error' => $e->getMessage()
                        ];
                        
                        Log::warning("Gagal menghapus SAKIP folder untuk tahun {$year}: " . $e->getMessage());
                    }
                } else {
                    $deletedFolders[] = [
                        'year' => $year,
                        'folder_name' => $folderName,
                        'folder_id' => null,
                        'status' => 'not_found'
                    ];
                    
                    Log::info("SAKIP folder untuk tahun {$year} tidak ditemukan (mungkin sudah dihapus)", [
                        'folder_name' => $folderName
                    ]);
                }
            }
            
            return $deletedFolders;
            
        } catch (\Exception $e) {
            Log::error('Error saat menghapus SAKIP folders untuk periode', [
                'start_year' => $startYear,
                'end_year' => $endYear,
                'error' => $e->getMessage()
            ]);
            
            return $deletedFolders;
        }
    }
}