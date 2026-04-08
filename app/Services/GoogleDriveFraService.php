<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GoogleDriveFraService
{
    private $client;
    private $driveService;
    private $sheetsService;

    public function __construct()
    {
        $this->initializeGoogleClient();
    }

    private function initializeGoogleClient()
    {
        $this->client = new Client();
        
        // Set service account credentials
        $keyPath = storage_path('app/google-service-account.json');
        if (file_exists($keyPath)) {
            $this->client->setAuthConfig($keyPath);
        }
        
        $this->client->setScopes([
            Drive::DRIVE,
            Sheets::SPREADSHEETS
        ]);

        $this->driveService = new Drive($this->client);
        $this->sheetsService = new Sheets($this->client);
    }

    /**
     * 1. Create FRA folder di Google Drive untuk tahun berjalan
     */
    public function createFraFolder($tahun = null)
    {
        $tahun = $tahun ?? date('Y');
        $folderName = "Form Rencana Aksi {$tahun}";

        try {
            // Check if folder already exists
            $existingFolder = $this->findFolderByName($folderName);
            if ($existingFolder) {
                Log::info("FRA folder sudah ada: {$folderName}", ['folder_id' => $existingFolder->getId()]);
                return $existingFolder->getId();
            }

            // Create new folder
            $fileMetadata = new Drive\DriveFile([
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder'
            ]);

            $folder = $this->driveService->files->create($fileMetadata, [
                'fields' => 'id,name,webViewLink'
            ]);

            Log::info("FRA folder created: {$folderName}", [
                'folder_id' => $folder->getId(),
                'link' => $folder->getWebViewLink()
            ]);

            return $folder->getId();

        } catch (\Exception $e) {
            Log::error("Error creating FRA folder: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 2. Upload Excel file ke Google Drive dan convert ke Google Sheets
     */
    public function uploadAndConvertExcel($localFilePath, $folderId, $newFileName = null)
    {
        try {
            if (!file_exists($localFilePath)) {
                throw new \Exception("File tidak ditemukan: {$localFilePath}");
            }

            $fileName = $newFileName ?? basename($localFilePath, '.xlsx') . ' - Google Sheets';

            // Upload file sebagai Google Sheets
            $fileMetadata = new Drive\DriveFile([
                'name' => $fileName,
                'parents' => [$folderId],
                'mimeType' => 'application/vnd.google-apps.spreadsheet'
            ]);

            $content = file_get_contents($localFilePath);

            $file = $this->driveService->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'uploadType' => 'multipart',
                'fields' => 'id,name,webViewLink,mimeType'
            ]);

            Log::info("Excel converted to Google Sheets", [
                'file_id' => $file->getId(),
                'name' => $file->getName(),
                'link' => $file->getWebViewLink()
            ]);

            return [
                'file_id' => $file->getId(),
                'sheets_id' => $file->getId(), // Same ID for converted sheets
                'name' => $file->getName(),
                'link' => $file->getWebViewLink(),
                'mime_type' => $file->getMimeType()
            ];

        } catch (\Exception $e) {
            Log::error("Error uploading/converting Excel: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 3. Update cell di Google Sheets berdasarkan database input
     */
    public function updateSheetsCells($sheetsId, $updates)
    {
        try {
            $requests = [];

            foreach ($updates as $update) {
                // Format: ['sheet' => 'PK IKU', 'cell' => 'H12', 'value' => '100']
                $sheetId = $this->getSheetIdByName($sheetsId, $update['sheet']);
                
                if ($sheetId !== null) {
                    $requests[] = [
                        'updateCells' => [
                            'rows' => [
                                [
                                    'values' => [
                                        [
                                            'userEnteredValue' => [
                                                'stringValue' => $update['value']
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'fields' => 'userEnteredValue',
                            'range' => [
                                'sheetId' => $sheetId,
                                'startRowIndex' => $this->getRowIndex($update['cell']) - 1,
                                'endRowIndex' => $this->getRowIndex($update['cell']),
                                'startColumnIndex' => $this->getColumnIndex($update['cell']) - 1,
                                'endColumnIndex' => $this->getColumnIndex($update['cell'])
                            ]
                        ]
                    ];
                }
            }

            if (!empty($requests)) {
                $batchUpdateRequest = new Sheets\BatchUpdateSpreadsheetRequest([
                    'requests' => $requests
                ]);

                $response = $this->sheetsService->spreadsheets->batchUpdate($sheetsId, $batchUpdateRequest);
                
                Log::info("Google Sheets updated", [
                    'sheets_id' => $sheetsId,
                    'updates_count' => count($requests),
                    'replies_count' => count($response->getReplies())
                ]);

                return $response;
            }

        } catch (\Exception $e) {
            Log::error("Error updating Google Sheets: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 4. Download Google Sheets sebagai Excel
     */
    public function downloadAsExcel($sheetsId, $downloadPath = null)
    {
        try {
            $response = $this->driveService->files->export($sheetsId, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            if ($downloadPath) {
                file_put_contents($downloadPath, $response->getBody());
                Log::info("Google Sheets downloaded as Excel", [
                    'sheets_id' => $sheetsId,
                    'path' => $downloadPath
                ]);
                return $downloadPath;
            }

            return $response->getBody();

        } catch (\Exception $e) {
            Log::error("Error downloading Google Sheets: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Helper methods
     */
    private function findFolderByName($name)
    {
        $response = $this->driveService->files->listFiles([
            'q' => "name='{$name}' and mimeType='application/vnd.google-apps.folder' and trashed=false",
            'fields' => 'files(id,name,webViewLink)'
        ]);

        $files = $response->getFiles();
        return !empty($files) ? $files[0] : null;
    }

    private function getSheetIdByName($sheetsId, $sheetName)
    {
        try {
            $spreadsheet = $this->sheetsService->spreadsheets->get($sheetsId);
            $sheets = $spreadsheet->getSheets();

            foreach ($sheets as $sheet) {
                if ($sheet->getProperties()->getTitle() === $sheetName) {
                    return $sheet->getProperties()->getSheetId();
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Error getting sheet ID: " . $e->getMessage());
            return null;
        }
    }

    private function getRowIndex($cell)
    {
        return (int) preg_replace('/[A-Z]/', '', $cell);
    }

    private function getColumnIndex($cell)
    {
        $column = preg_replace('/[0-9]/', '', $cell);
        return ord(strtoupper($column)) - ord('A') + 1;
    }

    /**
     * 5. Sync FRA data dari database ke Google Sheets
     */
    public function syncFraDataToSheets($fraId)
    {
        try {
            $fra = \App\Models\Fra::with(['matriksFra.targetFra', 'matriksFra.realisasiFra'])->find($fraId);
        $formRencanaAksiKegiatan = $fra ? $fra->formRencanaAksiKegiatan() : null;

        if (!$fra || !$formRencanaAksiKegiatan || !$formRencanaAksiKegiatan->google_sheets_id) {
            throw new \Exception("FRA atau Google Sheets ID tidak ditemukan di kegiatan terkait");
        }

            $updates = [];

            // Build updates dari target dan realisasi
            foreach ($fra->matriksFra as $matriks) {
                $cellPositions = json_decode($matriks->cell_positions, true);
                
                if ($cellPositions) {
                    // Update target
                    foreach ($matriks->targetFra as $target) {
                        if (isset($cellPositions['target'][$target->triwulan_id])) {
                            $updates[] = [
                                'sheet' => $cellPositions['sheet'] ?? 'PK IKU',
                                'cell' => $cellPositions['target'][$target->triwulan_id],
                                'value' => $target->target
                            ];
                        }
                    }

                    // Update realisasi
                    foreach ($matriks->realisasiFra as $realisasi) {
                        if (isset($cellPositions['realisasi'][$realisasi->triwulan_id])) {
                            $updates[] = [
                                'sheet' => $cellPositions['sheet'] ?? 'PK IKU',
                                'cell' => $cellPositions['realisasi'][$realisasi->triwulan_id],
                                'value' => $realisasi->realisasi
                            ];
                        }

                        // Update kendala, solusi, tindak lanjut (gabung ke cell yang sama)
                        if (isset($cellPositions['kendala'])) {
                            $kendalaText = $this->buildKendalaSolusiText($matriks);
                            $updates[] = [
                                'sheet' => $cellPositions['sheet'] ?? 'PK IKU',
                                'cell' => $cellPositions['kendala'],
                                'value' => $kendalaText
                            ];
                        }
                    }
                }
            }

            if (!empty($updates)) {
                return $this->updateSheetsCells($formRencanaAksiKegiatan->google_sheets_id, $updates);
            }

            return null;

        } catch (\Exception $e) {
            Log::error("Error syncing FRA data to sheets: " . $e->getMessage());
            throw $e;
        }
    }

    private function buildKendalaSolusiText($matriks)
    {
        $text = [];
        
        foreach ($matriks->realisasiFra as $realisasi) {
            if ($realisasi->kendala) {
                $text[] = "TW{$realisasi->triwulan_id} - Kendala: {$realisasi->kendala}";
            }
            if ($realisasi->solusi) {
                $text[] = "TW{$realisasi->triwulan_id} - Solusi: {$realisasi->solusi}";
            }
            if ($realisasi->tindak_lanjut) {
                $text[] = "TW{$realisasi->triwulan_id} - Tindak Lanjut: {$realisasi->tindak_lanjut}";
            }
        }

        return implode("\n", $text);
    }

    /**
     * 6. Complete FRA workflow
     */
    public function processFraWorkflow($fraId, $excelFilePath)
    {
        try {
            $fra = \App\Models\Fra::find($fraId);
            if (!$fra) {
                throw new \Exception("FRA tidak ditemukan");
            }

            Log::info("Starting FRA Google Drive workflow", ['fra_id' => $fraId]);

            // Step 1: Create folder
            $folderId = $this->createFraFolder($fra->tahun);

            // Step 2: Upload & convert Excel
            $uploadResult = $this->uploadAndConvertExcel($excelFilePath, $folderId, "FRA {$fra->nama} {$fra->tahun}");

            // Step 3: Update Kegiatan record (Form Rencana Aksi)
            $formRencanaAksiKegiatan = $fra->formRencanaAksiKegiatan();
            if ($formRencanaAksiKegiatan) {
                $formRencanaAksiKegiatan->update([
                    'google_drive_folder_id' => $folderId,
                    'google_drive_file_id' => $uploadResult['file_id'],
                    'google_sheets_id' => $uploadResult['sheets_id'],
                    'google_drive_synced_at' => now()
                ]);
            }

            // Step 4: Initial sync if data exists
            $this->syncFraDataToSheets($fraId);

            Log::info("FRA Google Drive workflow completed", [
                'fra_id' => $fraId,
                'folder_id' => $folderId,
                'sheets_id' => $uploadResult['sheets_id']
            ]);

            return [
                'success' => true,
                'folder_id' => $folderId,
                'file_id' => $uploadResult['file_id'],
                'sheets_id' => $uploadResult['sheets_id'],
                'link' => $uploadResult['link']
            ];

        } catch (\Exception $e) {
            Log::error("FRA workflow error: " . $e->getMessage());
            throw $e;
        }
    }
}