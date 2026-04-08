<?php

namespace App\Http\Controllers;

use App\Models\Dokumen_Kegiatan;
use App\Models\Renstra;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\GoogleDriveOAuthService;

class DokumenKegiatanController extends Controller
{
    /**
     * Mendapatkan token akses Google Drive
     */
    public function token()
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

            Log::error('Failed to get access token', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Token generation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Menyimpan dokumen Renstra ke Google Drive
     */
    public function storeRenstraDokumen(Request $request, $renstraId)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'dokumen' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cek apakah Renstra ada
        $renstra = Renstra::findOrFail($renstraId);

        try {
            // Get Renstra-specific folder
            $googleDriveService = new GoogleDriveOAuthService();
            $folder_id = $googleDriveService->getRenstraFolderId($renstra);
            
            if (!$folder_id) {
                Log::error('Failed to get Renstra folder ID', ['renstra_id' => $renstra->id]);
                return redirect()->back()->with('error', 'Gagal mendapatkan folder Renstra');
            }
            
            Log::info('Using Renstra-specific folder for dokumen upload', [
                'renstra_id' => $renstra->id,
                'folder_id' => $folder_id
            ]);

            $accessToken = $this->token();

            if (!$accessToken) {
                Log::error('Failed to get access token');
                return redirect()->back()->with('error', 'Gagal mendapatkan token akses Google Drive');
            }

            // Persiapkan file
            $file = $request->file('dokumen');
            $name = "Dokumen_Renstra_" . date('Y', strtotime($renstra->periode_awal)) . "-" .
                date('Y', strtotime($renstra->periode_akhir)) . "_" . time() . "." . $file->getClientOriginalExtension();
            $path = $file->getRealPath();

            // Log informasi file
            Log::info('File Upload Details', [
                'original_name' => $file->getClientOriginalName(),
                'generated_name' => $name,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);

            // Unggah file ke Google Drive
            $response = Http::withToken($accessToken)
                ->attach('metadata', json_encode([
                    'name' => $name,
                    'parents' => [$folder_id],
                ]), 'metadata.json')
                ->attach('file', file_get_contents($path), $name)
                ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

            // Log respon unggahan
            Log::info('Google Drive Upload Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Periksa apakah unggahan berhasil
            if ($response->successful()) {
                $file_id = $response->json()['id'];

                // Log file ID
                Log::info('File uploaded to Google Drive', ['file_id' => $file_id]);

                // Get file details including webViewLink
                $fileDetails = Http::withToken($accessToken)
                    ->get("https://www.googleapis.com/drive/v3/files/{$file_id}", [
                        'fields' => 'id, name, webViewLink, webContentLink'
                    ]);

                // Log file details
                Log::info('File Details Response', [
                    'status' => $fileDetails->status(),
                    'body' => $fileDetails->body()
                ]);

                if ($fileDetails->successful()) {
                    $fileInfo = $fileDetails->json();

                    // Delete old document if exists
                    if ($renstra->dokumenKegiatan && $renstra->dokumenKegiatan->isNotEmpty()) {
                        $existingDokumen = $renstra->dokumenKegiatan->first();
                        // Move old file to trash in Google Drive
                        if ($existingDokumen->file_id) {
                            $googleDriveService->moveToTrash($existingDokumen->file_id);
                        }
                        $existingDokumen->delete();
                    }

                    // Create new record in Dokumen_Kegiatan
                    $dokumen = new Dokumen_Kegiatan();
                    $dokumen->file = $file->getClientOriginalName();
                    $dokumen->file_id = $fileInfo['id'];
                    $dokumen->nama_dokumen = "Dokumen Renstra " . date('Y', strtotime($renstra->periode_awal)) .
                        "-" . date('Y', strtotime($renstra->periode_akhir));
                    $dokumen->webViewLink = $fileInfo['webViewLink'] ?? null;
                    $dokumen->webContentLink = $fileInfo['webContentLink'] ?? null;
                    $dokumen->renstra_id = $renstraId;

                    // Save the document
                    $dokumen->save();

                    // Redirect back to detail page instead of manajemen renstra
                    return redirect()->back()
                        ->with('success', 'Dokumen Renstra berhasil diunggah');
                } else {
                    Log::error('Failed to retrieve file details', [
                        'response' => $fileDetails->body(),
                        'status' => $fileDetails->status()
                    ]);
                    return redirect()->back()->with('error', 'Gagal mendapatkan detail file');
                }
            }

            // Tangani kegagalan unggah
            Log::error('File upload to Google Drive failed', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);
            return redirect()->back()->with('error', 'Gagal mengunggah file ke Google Drive');
        } catch (\Exception $e) {
            Log::error('File upload error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan dokumen Kegiatan ke Google Drive
     */
    public function storeKegiatanDokumen(Request $request, $kegiatanId)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'dokumen' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cek apakah Kegiatan ada
        $kegiatan = Kegiatan::findOrFail($kegiatanId);

        try {
            // Get specific Google Drive folder for this kegiatan
            $googleDriveService = new \App\Services\GoogleDriveOAuthService();
            $folder_id = $googleDriveService->getKegiatanFolderId($kegiatan);
            
            // Fallback ke folder umum jika tidak ada folder kegiatan spesifik
            if (!$folder_id) {
                $folder_id = config('services.google.kegiatan_folder_id');
                Log::warning('Using fallback folder for dokumen kegiatan upload', [
                    'kegiatan_id' => $kegiatan->id,
                    'fallback_folder_id' => $folder_id
                ]);
            } else {
                Log::info('Using kegiatan-specific folder for dokumen kegiatan upload', [
                    'kegiatan_id' => $kegiatan->id,
                    'folder_id' => $folder_id
                ]);
            }
            
            $accessToken = $this->token();

            if (!$accessToken) {
                return redirect()->back()->with('error', 'Gagal mendapatkan token akses Google Drive');
            }

            // Persiapkan file
            $file = $request->file('dokumen');
            $originalName = $file->getClientOriginalName();
            $path = $file->getRealPath();
            
            // Generate proper filename based on document type
            $timestamp = now()->format('YmdHis');
            if (stripos($kegiatan->nama_kegiatan, 'Perjanjian Kinerja') !== false || 
                stripos($kegiatan->nama_kegiatan, 'PK') !== false) {
                $name = "perjanjian_kinerja_{$kegiatan->tahun_berjalan}_{$timestamp}." . $file->getClientOriginalExtension();
            } else {
                $name = "{$kegiatan->nama_kegiatan}_{$kegiatan->tahun_berjalan}_{$timestamp}." . $file->getClientOriginalExtension();
            }

            // Unggah file ke Google Drive
            $response = Http::withToken($accessToken)
                ->attach('metadata', json_encode([
                    'name' => $name,
                    'parents' => [$folder_id],
                ]), 'metadata.json')
                ->attach('file', file_get_contents($path), $name)
                ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

            // Periksa apakah unggahan berhasil
            if ($response->successful()) {
                $file_id = $response->json()['id'];

                // Dapatkan detail file termasuk webViewLink
                $fileDetails = Http::withToken($accessToken)
                    ->get("https://www.googleapis.com/drive/v3/files/{$file_id}", [
                        'fields' => 'id, name, webViewLink, webContentLink'
                    ]);

                if ($fileDetails->successful()) {
                    $fileInfo = $fileDetails->json();

                    // Delete old document if exists
                    if ($kegiatan->dokumenKegiatan && $kegiatan->dokumenKegiatan->isNotEmpty()) {
                        $existingDokumen = $kegiatan->dokumenKegiatan->first();
                        // Move old file to trash in Google Drive
                        if ($existingDokumen->file_id) {
                            $googleDriveService->moveToTrash($existingDokumen->file_id);
                        }
                        $existingDokumen->delete();
                    }

                    // Buat record baru di Dokumen_Kegiatan
                    $dokumen = new Dokumen_Kegiatan();
                    $dokumen->file = $originalName;
                    $dokumen->file_id = $fileInfo['id'];
                    
                    // Generate nama dokumen berdasarkan jenis kegiatan
                    if (stripos($kegiatan->nama_kegiatan, 'Perjanjian Kinerja') !== false || 
                        stripos($kegiatan->nama_kegiatan, 'PK') !== false) {
                        $dokumen->nama_dokumen = "Dokumen PK " . $kegiatan->tahun_berjalan;
                    } else {
                        $dokumen->nama_dokumen = "Dokumen " . $kegiatan->nama_kegiatan . " " . $kegiatan->tahun_berjalan;
                    }
                    
                    $dokumen->webViewLink = $fileInfo['webViewLink'];
                    $dokumen->webContentLink = $fileInfo['webContentLink'] ?? null;
                    $dokumen->kegiatan_id = $kegiatan->id;
                    $dokumen->save();

                    return redirect()->back()
                        ->with('success', 'Dokumen kegiatan berhasil diunggah');
                } else {
                    Log::error('Failed to retrieve file details', [
                        'response' => $fileDetails->body(),
                        'status' => $fileDetails->status()
                    ]);
                    return redirect()->back()->with('error', 'Gagal mendapatkan detail file');
                }
            }

            // Tangani kegagalan unggah
            Log::error('File upload to Google Drive failed', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);
            return redirect()->back()->with('error', 'Gagal mengunggah file ke Google Drive');
        } catch (\Exception $e) {
            Log::error('File upload error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate proper filename for update based on existing dokumen kegiatan
     */
    private function generateFileNameForUpdate($dokumen, $originalName)
    {
        $timestamp = now()->format('YmdHis');
        
        if ($dokumen->renstra_id) {
            // For Renstra context, use renstra data
            $renstra = Renstra::find($dokumen->renstra_id);
            $tahun = $renstra ? date('Y', strtotime($renstra->periode_awal)) : date('Y');
            $namaKegiatan = $renstra ? $renstra->nama_renstra : 'Renstra';
            
            return "Dokumen_Renstra_{$tahun}_{$timestamp}." . pathinfo($originalName, PATHINFO_EXTENSION);
        } else {
            // For Kegiatan context, use kegiatan data
            $kegiatan = Kegiatan::find($dokumen->kegiatan_id);
            $tahun = $kegiatan ? $kegiatan->tahun_berjalan : date('Y');
            $namaKegiatan = $kegiatan ? $kegiatan->nama_kegiatan : 'Kegiatan';
            
            // Check if this is a Perjanjian Kinerja document
            if (stripos($namaKegiatan, 'Perjanjian Kinerja') !== false || stripos($namaKegiatan, 'PK') !== false) {
                return "perjanjian_kinerja_{$tahun}_{$timestamp}." . pathinfo($originalName, PATHINFO_EXTENSION);
            } else {
                return "{$namaKegiatan}_{$tahun}_{$timestamp}." . pathinfo($originalName, PATHINFO_EXTENSION);
            }
        }
    }

    /**
     * Memperbarui dokumen di Google Drive
     */
    public function update(Request $request, $id)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'dokumen' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Dapatkan dokumen yang akan diperbarui
            $dokumen = Dokumen_Kegiatan::findOrFail($id);
            $accessToken = $this->token();

            if (!$accessToken) {
                return redirect()->back()->with('error', 'Gagal mendapatkan token akses Google Drive');
            }

            // Get specific Google Drive folder using service
            $googleDriveService = new GoogleDriveOAuthService();
            
            // Tentukan folder ID berdasarkan jenis dokumen
            if ($dokumen->renstra_id) {
                // Untuk dokumen Renstra
                $renstra = Renstra::find($dokumen->renstra_id);
                $folder_id = $googleDriveService->getRenstraFolderId($renstra);
                
                if (!$folder_id) {
                    $folder_id = config('services.google.renstra_id');
                }
            } else {
                // Untuk dokumen Kegiatan
                $kegiatan = Kegiatan::find($dokumen->kegiatan_id);
                $folder_id = $googleDriveService->getKegiatanFolderId($kegiatan);
                
                // Fallback ke folder umum jika tidak ada folder kegiatan spesifik
                if (!$folder_id) {
                    $folder_id = config('services.google.kegiatan_folder_id');
                }
            }

            // Persiapkan file
            $file = $request->file('dokumen');
            $originalName = $file->getClientOriginalName();
            $path = $file->getRealPath();
            
            // Generate proper filename based on document type and context
            $name = $this->generateFileNameForUpdate($dokumen, $originalName);

            // Log informasi file
            Log::info('File Update Details', [
                'dokumen_id' => $dokumen->id,
                'original_name' => $originalName,
                'generated_name' => $name,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);

            // Update file yang sudah ada di Google Drive (jika ada file_id)
            if ($dokumen->file_id) {
                // Update existing file content using PATCH method
                $response = Http::withToken($accessToken)
                    ->attach('metadata', json_encode([
                        'name' => $name,
                    ]), 'metadata.json')
                    ->attach('file', file_get_contents($path), $name)
                    ->patch("https://www.googleapis.com/upload/drive/v3/files/{$dokumen->file_id}?uploadType=multipart");
                    
                if ($response->successful()) {
                    $file_id = $dokumen->file_id; // Keep the same file ID
                    
                    Log::info('File updated successfully', [
                        'file_id' => $file_id,
                        'new_name' => $name
                    ]);
                } else {
                    Log::error('Failed to update existing file', [
                        'file_id' => $dokumen->file_id,
                        'response' => $response->body(),
                        'status' => $response->status()
                    ]);
                    return redirect()->back()->with('error', 'Gagal memperbarui file yang sudah ada');
                }
            } else {
                // Jika tidak ada file_id, buat file baru
                $response = Http::withToken($accessToken)
                    ->attach('metadata', json_encode([
                        'name' => $name,
                        'parents' => [$folder_id],
                    ]), 'metadata.json')
                    ->attach('file', file_get_contents($path), $name)
                    ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
                    
                if ($response->successful()) {
                    $file_id = $response->json()['id'];
                    
                    Log::info('New file created successfully', [
                        'file_id' => $file_id,
                        'name' => $name
                    ]);
                } else {
                    Log::error('Failed to create new file', [
                        'response' => $response->body(),
                        'status' => $response->status()
                    ]);
                    return redirect()->back()->with('error', 'Gagal membuat file baru');
                }
            }

            // Dapatkan detail file yang sudah diupdate/dibuat
            $fileDetails = Http::withToken($accessToken)
                ->get("https://www.googleapis.com/drive/v3/files/{$file_id}", [
                    'fields' => 'id, name, webViewLink, webContentLink'
                ]);

            if ($fileDetails->successful()) {
                $fileInfo = $fileDetails->json();

                // Update informasi dokumen
                $dokumen->file = $originalName;
                $dokumen->file_id = $fileInfo['id'];
                $dokumen->webViewLink = $fileInfo['webViewLink'] ?? null;
                $dokumen->webContentLink = $fileInfo['webContentLink'] ?? null;

                // Simpan perubahan
                $dokumen->save();

                return redirect()->back()
                    ->with('success', 'Dokumen berhasil diperbarui');
            } else {
                Log::error('Failed to retrieve file details after update', [
                    'response' => $fileDetails->body(),
                    'status' => $fileDetails->status()
                ]);
                return redirect()->back()->with('error', 'Gagal mendapatkan detail file setelah update');
            }

            // Tangani kegagalan unggah
            Log::error('File update to Google Drive failed', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);
            return redirect()->back()->with('error', 'Gagal memperbarui file ke Google Drive');
        } catch (\Exception $e) {
            Log::error('Document update error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        try {
            $dokumen = Dokumen_Kegiatan::findOrFail($id);

            if (!$dokumen->webViewLink) {
                return redirect()->back()->with('error', 'Link dokumen tidak tersedia');
            }

            // Redirect ke Google Drive view link
            return redirect($dokumen->webViewLink);
        } catch (\Exception $e) {
            Log::error('Document view error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal membuka dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus dokumen dari Google Drive dan database
     */
    public function destroy($id)
    {
        try {
            $dokumen = Dokumen_Kegiatan::findOrFail($id);
            $accessToken = $this->token();

            if ($accessToken && $dokumen->file_id) {
                // Move file to trash in Google Drive (safer than permanent deletion)
                $googleDriveService = new GoogleDriveOAuthService();
                $trashResult = $googleDriveService->moveToTrash($dokumen->file_id);

                if (!$trashResult) {
                    Log::warning('Failed to move file to trash in Google Drive', [
                        'file_id' => $dokumen->file_id
                    ]);
                }
            }

            // Hapus record dari database
            $dokumen->delete();

            return redirect()->back()->with('success', 'Dokumen berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Document deletion error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh dokumen dari Google Drive
     */
    public function download($id)
    {
        try {
            $dokumen = Dokumen_Kegiatan::findOrFail($id);

            if (!$dokumen->webContentLink) {
                return redirect()->back()->with('error', 'Link unduhan tidak tersedia');
            }

            return redirect($dokumen->webContentLink);
        } catch (\Exception $e) {
            Log::error('Document download error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal mengunduh dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Update document name
     */
    public function updateName(Request $request, $id)
    {
        try {
            $dokumen = Dokumen_Kegiatan::findOrFail($id);

            $request->validate([
                'nama_dokumen' => 'required|string|max:255'
            ]);

            // Update Google Drive file name if file_id exists
            if ($dokumen->file_id) {
                $googleDriveService = new GoogleDriveOAuthService();
                $driveUpdateResult = $googleDriveService->updateFileName($dokumen->file_id, $request->nama_dokumen);
                
                if (!$driveUpdateResult) {
                    Log::warning('Failed to update file name on Google Drive', [
                        'file_id' => $dokumen->file_id,
                        'new_name' => $request->nama_dokumen
                    ]);
                    // Continue with database update even if Google Drive update fails
                }
            }

            $dokumen->nama_dokumen = $request->nama_dokumen;
            $dokumen->save();

            return redirect()->back()->with('success', 'Nama dokumen berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Document name update error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal memperbarui nama dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Update document file
     */
    public function updateFile(Request $request, $id)
    {
        Log::info('updateFile method called', [
            'id' => $id,
            'method' => $request->method(),
            'has_file' => $request->hasFile('dokumen')
        ]);
        
        try {
            $dokumen = Dokumen_Kegiatan::findOrFail($id);

            $request->validate([
                'dokumen' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240' // Max 10MB
            ]);

            $accessToken = $this->token();

            if (!$accessToken) {
                return redirect()->back()->with('error', 'Gagal mendapatkan token akses Google Drive');
            }

            // Tentukan folder ID berdasarkan jenis dokumen
            $folder_id = $dokumen->renstra_id
                ? config('services.google.renstra_id')
                : config('services.google.kegiatan_folder_id');

            // Persiapkan file
            $file = $request->file('dokumen');
            $originalName = $file->getClientOriginalName();
            $path = $file->getRealPath();
            
            // Generate proper filename based on document type and context
            $name = $this->generateFileNameForUpdate($dokumen, $originalName);

            // Log informasi file
            Log::info('File Update Details', [
                'dokumen_id' => $dokumen->id,
                'original_name' => $originalName,
                'generated_name' => $name,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);

            // Update file yang sudah ada di Google Drive (jika ada file_id)
            if ($dokumen->file_id) {
                // Update existing file content using PATCH method
                $response = Http::withToken($accessToken)
                    ->attach('metadata', json_encode([
                        'name' => $name,
                    ]), 'metadata.json')
                    ->attach('file', file_get_contents($path), $name)
                    ->patch("https://www.googleapis.com/upload/drive/v3/files/{$dokumen->file_id}?uploadType=multipart");
                    
                if ($response->successful()) {
                    $file_id = $dokumen->file_id; // Keep the same file ID
                    
                    Log::info('File updated successfully in updateFile method', [
                        'file_id' => $file_id,
                        'new_name' => $name
                    ]);
                } else {
                    Log::error('Failed to update existing file in updateFile method', [
                        'file_id' => $dokumen->file_id,
                        'response' => $response->body(),
                        'status' => $response->status()
                    ]);
                    return redirect()->back()->with('error', 'Gagal memperbarui file yang sudah ada');
                }
            } else {
                // Jika tidak ada file_id, buat file baru
                $response = Http::withToken($accessToken)
                    ->attach('metadata', json_encode([
                        'name' => $name,
                        'parents' => [$folder_id],
                    ]), 'metadata.json')
                    ->attach('file', file_get_contents($path), $name)
                    ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
                    
                if ($response->successful()) {
                    $file_id = $response->json()['id'];
                    
                    Log::info('New file created successfully in updateFile method', [
                        'file_id' => $file_id,
                        'name' => $name
                    ]);
                } else {
                    Log::error('Failed to create new file in updateFile method', [
                        'response' => $response->body(),
                        'status' => $response->status()
                    ]);
                    return redirect()->back()->with('error', 'Gagal membuat file baru');
                }
            }

            // Dapatkan detail file yang sudah diupdate/dibuat
            $fileDetails = Http::withToken($accessToken)
                ->get("https://www.googleapis.com/drive/v3/files/{$file_id}", [
                    'fields' => 'id, name, webViewLink, webContentLink'
                ]);

            if ($fileDetails->successful()) {
                $fileInfo = $fileDetails->json();

                // Update informasi dokumen
                $dokumen->file = $originalName;
                $dokumen->file_id = $fileInfo['id'];
                $dokumen->webViewLink = $fileInfo['webViewLink'] ?? null;
                $dokumen->webContentLink = $fileInfo['webContentLink'] ?? null;

                // Simpan perubahan
                $dokumen->save();

                return redirect()->back()->with('success', 'File dokumen berhasil diperbarui');
            } else {
                Log::error('Failed to retrieve file details after update in updateFile method', [
                    'response' => $fileDetails->body(),
                    'status' => $fileDetails->status()
                ]);
                return redirect()->back()->with('error', 'Gagal mendapatkan detail file setelah update');
            }

            // Tangani kegagalan unggah
            Log::error('File update to Google Drive failed', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);
            return redirect()->back()->with('error', 'Gagal memperbarui file ke Google Drive');
        } catch (\Exception $e) {
            Log::error('Document file update error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal memperbarui file dokumen: ' . $e->getMessage());
        }
    }
}
