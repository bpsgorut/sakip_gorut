<?php

namespace App\Http\Controllers;

use App\Models\Buktidukung_Fra;
use App\Models\Realisasi_Fra;
use App\Models\Fra;
use App\Models\Triwulan;
use App\Services\GoogleDriveOAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class BuktiDukungFraController extends Controller
{
    /**
     * Store bukti dukung FRA files
     */
    public function store(Request $request)
    {
        // Force JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            try {
                $request->validate([
                    'realisasi_fra_id' => 'required|exists:realisasi_fra,id',
                    'files.*' => 'required|file|mimes:pdf|max:10240', // Max 10MB per file
                ]);

                $realisasiFra = Realisasi_Fra::with(['matriks_fra.template_fra.fra', 'triwulan'])->findOrFail($request->realisasi_fra_id);
                $fra = $realisasiFra->matriks_fra->template_fra->fra;
                $triwulan = $realisasiFra->triwulan;

                $googleDriveService = new GoogleDriveOAuthService();
                
                // Pastikan FRA punya folder di Google Drive melalui kegiatan Form Rencana Aksi
                $formRencanaAksiKegiatan = $fra->formRencanaAksiKegiatan();
                $folderId = null;
                
                if ($formRencanaAksiKegiatan && $formRencanaAksiKegiatan->folder_id) {
                    $folderId = $formRencanaAksiKegiatan->folder_id;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'FRA tidak memiliki kegiatan Form Rencana Aksi atau folder Google Drive'
                    ], 400, [
                        'Content-Type' => 'application/json',
                        'Cache-Control' => 'no-cache, no-store, must-revalidate'
                    ]);
                }

                // Dapatkan folder triwulan yang sesuai
                $triwulanFolderId = $googleDriveService->getTriwulanFolderId($folderId, $triwulan->nomor);
                
                if (!$triwulanFolderId) {
                    return response()->json([
                        'success' => false,
                        'message' => "Gagal mendapatkan folder triwulan {$triwulan->nomor}"
                    ], 400, [
                        'Content-Type' => 'application/json',
                        'Cache-Control' => 'no-cache, no-store, must-revalidate'
                    ]);
                }

                $uploadedFiles = [];
                $errors = [];

                foreach ($request->file('files') as $file) {
                    try {
                        $originalName = $file->getClientOriginalName();
                        $fileName = pathinfo($originalName, PATHINFO_FILENAME) . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                        $uploadResult = $googleDriveService->uploadFile($file, $fileName, $triwulanFolderId);

                        if ($uploadResult && $uploadResult['success']) {
                            $buktiDukung = Buktidukung_Fra::create([
                                'realisasi_fra_id' => $realisasiFra->id,
                                'nama_dokumen' => $originalName,
                                'file_name' => $fileName,
                                'google_drive_file_id' => $uploadResult['file_id'],
                                'webViewLink' => $uploadResult['webViewLink'],
                            ]);

                            $uploadedFiles[] = [
                                'id' => $buktiDukung->id,
                                'file_name' => $originalName,
                                'webViewLink' => $uploadResult['webViewLink']
                            ];

                            Log::info('Bukti dukung FRA uploaded successfully', [
                                'file_name' => $originalName,
                                'realisasi_fra_id' => $realisasiFra->id,
                                'triwulan' => $triwulan->nomor,
                                'folder_id' => $triwulanFolderId
                            ]);
                        } else {
                            $errors[] = "Gagal mengunggah file: {$originalName}";
                            Log::error('Failed to upload bukti dukung FRA to Google Drive', [
                                'file_name' => $originalName,
                                'realisasi_fra_id' => $realisasiFra->id,
                                'upload_result' => $uploadResult
                            ]);
                        }
                    } catch (Exception $e) {
                        $errors[] = "Error uploading {$originalName}: " . $e->getMessage();
                        Log::error('Exception during bukti dukung FRA upload', [
                            'file_name' => $originalName ?? 'unknown',
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                return response()->json([
                    'success' => count($uploadedFiles) > 0,
                    'message' => count($uploadedFiles) > 0 ? 
                        'File berhasil diunggah: ' . count($uploadedFiles) . ' file' : 
                        'Tidak ada file yang berhasil diunggah',
                    'uploaded_files' => $uploadedFiles,
                    'errors' => $errors
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate'
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()),
                    'errors' => $e->validator->errors()
                ], 422, [
                    'Content-Type' => 'application/json',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate'
                ]);
            } catch (Exception $e) {
                Log::error('Error in BuktiDukungFraController::store', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500, [
                    'Content-Type' => 'application/json',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate'
                ]);
            }
        }

        // Non-AJAX fallback (traditional form submission)
        try {
            $request->validate([
                'realisasi_fra_id' => 'required|exists:realisasi_fra,id',
                'files.*' => 'required|file|mimes:pdf|max:10240',
            ]);

            $realisasiFra = Realisasi_Fra::with(['matriks_fra.template_fra.fra', 'triwulan'])->findOrFail($request->realisasi_fra_id);
            $fra = $realisasiFra->matriks_fra->template_fra->fra;
            $triwulan = $realisasiFra->triwulan;

            $googleDriveService = new GoogleDriveOAuthService();
            
            $formRencanaAksiKegiatan = $fra->formRencanaAksiKegiatan();
            $folderId = null;
            
            if ($formRencanaAksiKegiatan && $formRencanaAksiKegiatan->folder_id) {
                $folderId = $formRencanaAksiKegiatan->folder_id;
            } else {
                throw new Exception("FRA tidak memiliki kegiatan Form Rencana Aksi atau folder Google Drive");
            }

            $triwulanFolderId = $googleDriveService->getTriwulanFolderId($folderId, $triwulan->nomor);
            
            if (!$triwulanFolderId) {
                throw new Exception("Gagal mendapatkan folder triwulan {$triwulan->nomor}");
            }

            $uploadedFiles = [];
            $errors = [];

            foreach ($request->file('files') as $file) {
                try {
                    $originalName = $file->getClientOriginalName();
                    $fileName = pathinfo($originalName, PATHINFO_FILENAME) . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                    $uploadResult = $googleDriveService->uploadFile($file, $fileName, $triwulanFolderId);

                    if ($uploadResult && $uploadResult['success']) {
                        $buktiDukung = Buktidukung_Fra::create([
                            'realisasi_fra_id' => $realisasiFra->id,
                            'nama_dokumen' => $originalName,
                            'file_name' => $fileName,
                            'google_drive_file_id' => $uploadResult['file_id'],
                            'webViewLink' => $uploadResult['webViewLink'],
                        ]);

                        $uploadedFiles[] = [
                            'id' => $buktiDukung->id,
                            'file_name' => $originalName,
                            'webViewLink' => $uploadResult['webViewLink']
                        ];

                        Log::info('Bukti dukung FRA uploaded successfully', [
                            'file_name' => $originalName,
                            'realisasi_fra_id' => $realisasiFra->id,
                            'triwulan' => $triwulan->nomor,
                            'folder_id' => $triwulanFolderId
                        ]);
                    } else {
                        $errors[] = "Gagal mengunggah file: {$originalName}";
                        Log::error('Failed to upload bukti dukung FRA to Google Drive', [
                            'file_name' => $originalName,
                            'realisasi_fra_id' => $realisasiFra->id,
                            'upload_result' => $uploadResult
                        ]);
                    }
                } catch (Exception $e) {
                    $errors[] = "Error uploading {$originalName}: " . $e->getMessage();
                    Log::error('Exception during bukti dukung FRA upload', [
                        'file_name' => $originalName ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if (count($uploadedFiles) > 0) {
                return redirect()->back()->with('success', 'File berhasil diunggah: ' . count($uploadedFiles) . ' file');
            } else {
                return redirect()->back()->with('error', 'Gagal mengunggah file: ' . implode(', ', $errors));
            }

        } catch (Exception $e) {
            Log::error('Error in BuktiDukungFraController::store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete bukti dukung FRA file
     */
    public function destroy($id)
    {
        try {
            $buktiDukung = Buktidukung_Fra::findOrFail($id);
            $fileName = $buktiDukung->nama_dokumen;
            $fileId = $buktiDukung->google_drive_file_id;

            Log::info('Attempting to delete bukti dukung FRA', [
                'id' => $id,
                'file_name' => $fileName,
                'file_id' => $fileId
            ]);

            $googleDriveService = new GoogleDriveOAuthService();
            $googleDriveDeleted = false;
            $googleDriveError = null;
            
            // Try to move file to trash in Google Drive
            try {
                $trashResult = $googleDriveService->moveToTrash($fileId);
                $googleDriveDeleted = $trashResult;
                
                if (!$trashResult) {
                    $googleDriveError = 'Gagal memindahkan file ke trash Google Drive';
                }
            } catch (Exception $e) {
                $googleDriveError = $e->getMessage();
                Log::warning('Google Drive deletion failed, but continuing with database deletion', [
                    'id' => $id,
                    'file_id' => $fileId,
                    'error' => $e->getMessage()
                ]);
            }

            // Always delete from database, regardless of Google Drive result
            $buktiDukung->delete();

            if ($googleDriveDeleted) {
                Log::info('Bukti dukung FRA deleted successfully from both Google Drive and database', [
                    'id' => $id,
                    'file_name' => $fileName
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'File berhasil dihapus'
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate'
                ]);
            } else {
                Log::warning('Bukti dukung FRA deleted from database but failed from Google Drive', [
                    'id' => $id,
                    'file_id' => $fileId,
                    'google_drive_error' => $googleDriveError
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'File berhasil dihapus dari sistem (file di Google Drive mungkin sudah tidak ada atau tidak dapat diakses)'
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate'
                ]);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Bukti dukung FRA not found for deletion', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 404, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache, no-store, must-revalidate'
            ]);
        } catch (Exception $e) {
            Log::error('Error deleting bukti dukung FRA', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache, no-store, must-revalidate'
            ]);
        }
    }

    /**
     * Get bukti dukung files for a specific realisasi FRA
     */
    public function getFiles($realisasiFraId)
    {
        try {
            // Validate realisasi_fra_id exists
            $realisasiFra = Realisasi_Fra::findOrFail($realisasiFraId);
            
            $files = Buktidukung_Fra::where('realisasi_fra_id', $realisasiFraId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'file_name' => $file->nama_dokumen,
                        'webViewLink' => $file->webViewLink,
                        'created_at' => $file->created_at->format('d M Y H:i')
                    ];
                });

            return response()->json([
                'success' => true,
                'files' => $files,
                'count' => $files->count()
            ], 200, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache, no-store, must-revalidate'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Realisasi FRA not found in BuktiDukungFraController::getFiles', [
                'realisasi_fra_id' => $realisasiFraId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Data realisasi FRA tidak ditemukan',
                'files' => []
            ], 404, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache, no-store, must-revalidate'
            ]);
        } catch (Exception $e) {
            Log::error('Error in BuktiDukungFraController::getFiles', [
                'realisasi_fra_id' => $realisasiFraId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat file: ' . $e->getMessage(),
                'files' => []
            ], 500, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache, no-store, must-revalidate'
            ]);
        }
    }
}