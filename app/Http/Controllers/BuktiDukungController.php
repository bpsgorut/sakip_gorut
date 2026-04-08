<?php

namespace App\Http\Controllers;

use App\Models\Bukti_Dukung;
use App\Models\Skp;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Kegiatan;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleDriveOAuthService;

class BuktiDukungController extends Controller
{
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

    public function store(Request $request)
    {
        // Validate the request
        $allowedMimes = match ($request->jenis_dokumen) {
            'dokumentasi' => 'mimes:jpg,jpeg,png',
            default => 'mimes:pdf'
        };

        // Determine upload context first to set proper validation rules
        $isRenstraDetail = $request->boolean('is_renstra_detail');
        $isPKDetail = $request->boolean('is_pk_detail');
        
        // Set validation rules based on context
        $validationRules = [
            'dokumen' => "file|required|{$allowedMimes}|max:10240", // Format sesuai jenis dokumen, max 10MB
            'jenis_dokumen' => 'required|in:notulensi,surat_undangan,daftar_hadir,dokumentasi,skp_bulanan,skp_tahunan,dokumen_lainnya,penetapan_mekanisme,sk_penerima_triwulan,piagam_penghargaan_triwulan,rekap_pemilihan_triwulan,notulensi_triwulan,surat_undangan_triwulan,daftar_hadir_triwulan,fra_triwulan',
            'nama_dokumen_custom' => 'nullable|string|max:255',
            'bulan' => 'nullable|integer|min:1|max:12', // untuk SKP bulanan
            'triwulan' => 'nullable|integer|min:1|max:4', // untuk reward punishment triwulanan
            'is_renstra_detail' => 'nullable|boolean',
            'is_pk_detail' => 'nullable|boolean',
        ];
        
        // For Renstra context: kegiatan_id is nullable (actually renstra_id), renstra_id is required
        if ($isRenstraDetail) {
            $validationRules['kegiatan_id'] = 'nullable|integer'; // This is actually renstra_id in renstra context
            $validationRules['renstra_id'] = 'required|integer';
        } else {
            // For Kegiatan context: kegiatan_id is required, renstra_id is required (setiap kegiatan harus memiliki renstra)
            $validationRules['kegiatan_id'] = 'required|integer';
            $validationRules['renstra_id'] = 'required|integer';
        }
        
        // Custom validation with proper error handling for AJAX
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $validationRules, [
            'dokumen.required' => 'File dokumen harus diunggah.',
            'dokumen.file' => 'File yang diunggah harus berupa file yang valid.',
            'dokumen.mimes' => 'Format file tidak didukung. Gunakan format yang sesuai.',
            'dokumen.max' => 'Ukuran file tidak boleh lebih dari 10MB.',
            'kegiatan_id.required' => 'ID kegiatan diperlukan.',
            'jenis_dokumen.required' => 'Jenis dokumen harus dipilih.',
            'jenis_dokumen.in' => 'Jenis dokumen tidak valid.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Determine upload context: Renstra vs PK vs Regular Kegiatan
            $isRenstraDetail = $request->boolean('is_renstra_detail');
            $isPKDetail = $request->boolean('is_pk_detail');

            if ($isRenstraDetail) {
                // For Renstra detail page, kegiatan_id is actually renstra_id
                $renstra = \App\Models\Renstra::findOrFail($request->kegiatan_id);
                $fakeKegiatan = (object) [
                    'id' => $renstra->id,
                    'tahun_berjalan' => date('Y', strtotime($renstra->periode_awal)),
                    'renstra_id' => $renstra->id
                ];

                Log::info('Processing Renstra detail upload', [
                    'renstra_id' => $renstra->id,
                    'jenis_dokumen' => $request->jenis_dokumen
                ]);
            } elseif ($isPKDetail) {
                // For PK detail page, this is a regular kegiatan but with PK context
                $fakeKegiatan = \App\Models\Kegiatan::findOrFail($request->kegiatan_id);

                Log::info('Processing PK detail upload', [
                    'kegiatan_id' => $fakeKegiatan->id,
                    'jenis_dokumen' => $request->jenis_dokumen,
                    'nama_kegiatan' => $fakeKegiatan->nama_kegiatan
                ]);
            } else {
                // Regular kegiatan upload
                $fakeKegiatan = \App\Models\Kegiatan::findOrFail($request->kegiatan_id);

                Log::info('Processing regular kegiatan upload', [
                    'kegiatan_id' => $fakeKegiatan->id,
                    'jenis_dokumen' => $request->jenis_dokumen
                ]);
            }

            // Validate quarter period for reward punishment documents
            if ($this->isRewardPunishmentDocument($request->jenis_dokumen)) {
                $triwulan = $request->triwulan;

                // Allow super admin to upload even after period ends
                /** @var \App\Models\Pengguna $user */
                $user = Auth::user();
                $isSuperAdmin = Auth::check() && $user->isSuperAdmin();

                if ($triwulan && !$this->canUploadInQuarter($triwulan, $fakeKegiatan->tahun_berjalan) && !$isSuperAdmin) {
                    $quarterStatus = $this->getQuarterStatusForUpload($triwulan, $fakeKegiatan->tahun_berjalan);
                    $message = $quarterStatus === 'upcoming'
                        ? "Triwulan {$triwulan} belum dimulai. Upload akan tersedia mulai periode triwulan tersebut."
                        : "Triwulan {$triwulan} sudah berakhir. Upload tidak diizinkan untuk periode yang sudah lewat.";

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $message
                        ], 422);
                    }

                    $redirectUrl = $this->getRedirectUrl($request);
                    return redirect($redirectUrl)->with('error', $message);
                }
            }

            // Get specific Google Drive folder
            $googleDriveService = new \App\Services\GoogleDriveOAuthService();

            if ($isRenstraDetail) {
                // Use Renstra-specific folder
                $folder_id = $googleDriveService->getRenstraFolderId($renstra);

                if (!$folder_id) {
                    Log::error('Failed to get Renstra folder ID', ['renstra_id' => $renstra->id]);

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Gagal mendapatkan folder Renstra'
                        ], 500);
                    }

                    return redirect()->back()->with('error', 'Gagal mendapatkan folder Renstra');
                }

                Log::info('Using Renstra-specific folder for bukti dukung upload', [
                    'renstra_id' => $renstra->id,
                    'folder_id' => $folder_id
                ]);
            } else {
                // Use kegiatan-specific folder
                $folder_id = $googleDriveService->getKegiatanFolderId($fakeKegiatan);

                // Check if this is SKP document and create/get appropriate subfolder
                if ($folder_id && $this->isSKPDocument($request->jenis_dokumen)) {
                    // Create SKP folder structure if not exists
                    $skpFolderResult = $googleDriveService->createSKPFolder($fakeKegiatan->nama_kegiatan, $fakeKegiatan->tahun_berjalan);

                    if ($skpFolderResult['success'] && isset($skpFolderResult['skp_folders'])) {
                        $skpFolders = $skpFolderResult['skp_folders'];

                        if ($request->jenis_dokumen === 'skp_bulanan' && $request->bulan) {
                            // Get or create specific monthly folder for SKP Bulanan
                            $skpBulananFolderId = $skpFolders['skp_bulanan_folder_id'];

                            if ($skpBulananFolderId) {
                                $monthlyFolderId = $googleDriveService->getMonthlyFolderId($skpBulananFolderId, $request->bulan);

                                if ($monthlyFolderId) {
                                    $folder_id = $monthlyFolderId;
                                    Log::info('Using monthly SKP folder for upload', [
                                        'kegiatan_id' => $fakeKegiatan->id,
                                        'bulan' => $request->bulan,
                                        'folder_id' => $folder_id
                                    ]);
                                } else {
                                    // Fallback to SKP Bulanan folder if monthly folder creation failed
                                    $folder_id = $skpBulananFolderId;
                                    Log::warning('Failed to create monthly folder, using SKP Bulanan folder', [
                                        'kegiatan_id' => $fakeKegiatan->id,
                                        'bulan' => $request->bulan,
                                        'folder_id' => $folder_id
                                    ]);
                                }
                            } else {
                                Log::warning('SKP Bulanan folder not found, using main kegiatan folder', [
                                    'kegiatan_id' => $fakeKegiatan->id,
                                    'bulan' => $request->bulan,
                                    'main_folder_id' => $folder_id
                                ]);
                            }
                        } elseif ($request->jenis_dokumen === 'skp_tahunan') {
                            // Use yearly folder for SKP Tahunan
                            $folder_id = $skpFolders['skp_tahunan_folder_id'] ?? $folder_id;
                            Log::info('Using yearly SKP folder for upload', [
                                'kegiatan_id' => $fakeKegiatan->id,
                                'folder_id' => $folder_id
                            ]);
                        }
                    } else {
                        Log::warning('Failed to create/get SKP folder structure, using main kegiatan folder', [
                            'kegiatan_id' => $fakeKegiatan->id,
                            'main_folder_id' => $folder_id
                        ]);
                    }
                }
                // Check if this is reward punishment document
                elseif ($folder_id && $this->isRewardPunishmentDocument($request->jenis_dokumen)) {
                    if ($request->jenis_dokumen === 'penetapan_mekanisme') {
                        // For penetapan mekanisme, use main Drive folder (createKegiatanFolder)
                        // Keep using the existing folder_id from getKegiatanFolderId
                        Log::info('Using main Drive folder for penetapan mekanisme upload', [
                            'kegiatan_id' => $fakeKegiatan->id,
                            'jenis_dokumen' => $request->jenis_dokumen,
                            'folder_id' => $folder_id
                        ]);
                    } elseif ($request->triwulan) {
                        // For quarterly reward punishment documents, use reward punishment folder with triwulan subfolders
                        $rewardPunishmentResult = $googleDriveService->createRewardPunishmentFolder($fakeKegiatan->nama_kegiatan, $fakeKegiatan->tahun_berjalan);
                        
                        if ($rewardPunishmentResult['success'] && isset($rewardPunishmentResult['triwulan_folders'])) {
                            $triwulanFolders = $rewardPunishmentResult['triwulan_folders'];
                            $triwulanFolderId = $triwulanFolders[$request->triwulan] ?? null;
                            
                            if ($triwulanFolderId) {
                                $folder_id = $triwulanFolderId;
                                Log::info('Using reward punishment triwulan folder for upload', [
                                    'kegiatan_id' => $fakeKegiatan->id,
                                    'jenis_dokumen' => $request->jenis_dokumen,
                                    'triwulan' => $request->triwulan,
                                    'folder_id' => $folder_id
                                ]);
                            } else {
                                Log::warning('Failed to get reward punishment triwulan folder, using main kegiatan folder', [
                                    'kegiatan_id' => $fakeKegiatan->id,
                                    'triwulan' => $request->triwulan,
                                    'main_folder_id' => $folder_id
                                ]);
                            }
                        } else {
                            Log::warning('Failed to create reward punishment folder structure, using main kegiatan folder', [
                                'kegiatan_id' => $fakeKegiatan->id,
                                'main_folder_id' => $folder_id
                            ]);
                        }
                    }
                }
                // Check if this is a capaian kinerja kegiatan with triwulan
                elseif ($folder_id && $request->triwulan && $this->isCapaianKinerjaKegiatan($fakeKegiatan)) {
                    // Get triwulan-specific folder for capaian kinerja
                    $triwulanFolderId = $googleDriveService->getTriwulanFolderId($folder_id, $request->triwulan);

                    if ($triwulanFolderId) {
                        $folder_id = $triwulanFolderId;
                        Log::info('Using triwulan-specific folder for capaian kinerja upload', [
                            'kegiatan_id' => $fakeKegiatan->id,
                            'triwulan' => $request->triwulan,
                            'folder_id' => $folder_id
                        ]);
                    } else {
                        Log::warning('Failed to get triwulan folder, using main kegiatan folder', [
                            'kegiatan_id' => $fakeKegiatan->id,
                            'triwulan' => $request->triwulan,
                            'main_folder_id' => $folder_id
                        ]);
                    }
                }

                // Fallback ke folder umum jika tidak ada folder kegiatan spesifik
                if (!$folder_id) {
                    $folder_id = config('services.google.kegiatan_folder_id');
                    Log::warning('Using fallback folder for bukti dukung upload', [
                        'kegiatan_id' => $fakeKegiatan->id,
                        'fallback_folder_id' => $folder_id
                    ]);
                } else {
                    Log::info('Using kegiatan-specific folder for bukti dukung upload', [
                        'kegiatan_id' => $fakeKegiatan->id,
                        'folder_id' => $folder_id
                    ]);
                }
            }

            $accessToken = $this->token();

            if (!$accessToken) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mendapatkan token akses'
                    ], 500);
                }
                return redirect()->back()->with('error', 'Gagal mendapatkan token akses');
            }

            // Check for duplicate documents (excluding trashed files)
            if ($isRenstraDetail) {
                // For Renstra documents, check duplicates at renstra level
                $existingRenstraDokumen = \App\Models\Bukti_Dukung::where('renstra_id', $renstra->id)
                    ->whereNull('kegiatan_id')  // Only documents directly linked to renstra
                    ->where('jenis', $this->mapJenisDokumen($request->jenis_dokumen, $request->bulan, $request->triwulan))
                    ->first();

                if ($existingRenstraDokumen) {
                    $jenisLabel = $this->getJenisLabel($request->jenis_dokumen);
                    $errorMessage = "{$jenisLabel} untuk Renstra ini sudah ada. " .
                        "Silahkan hapus yang lama terlebih dahulu atau gunakan fitur update dokumen.";

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage
                        ], 422);
                    }

                    $redirectUrl = $this->getRedirectUrl($request);
                    return redirect($redirectUrl)->with('error', $errorMessage);
                }
            } else {
                // For regular kegiatan documents (including PK), use existing logic
                $duplicateFile = $googleDriveService->findDuplicateDocument(
                    $request->jenis_dokumen,
                    $fakeKegiatan->id,
                    $folder_id,
                    $request->bulan,
                    $request->triwulan
                );

                if ($duplicateFile) {
                    $jenisLabel = $this->getJenisLabel($request->jenis_dokumen);
                    $bulanLabel = $request->bulan ? " bulan " . str_pad($request->bulan, 2, '0', STR_PAD_LEFT) : '';
                    $triwulanLabel = $request->triwulan ? " triwulan {$request->triwulan}" : '';

                    $contextLabel = $isPKDetail ? "kegiatan PK" : "kegiatan";
                    $errorMessage = "{$jenisLabel}{$bulanLabel}{$triwulanLabel} untuk {$contextLabel} ini sudah ada. " .
                        "Silahkan hapus yang lama terlebih dahulu atau gunakan fitur update dokumen.";

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage
                        ], 422);
                    }

                    $redirectUrl = $this->getRedirectUrl($request);
                    return redirect($redirectUrl)->with('error', $errorMessage);
                }
            }

            // Prepare file details
            $file = $request->file('dokumen');
            $originalName = $file->getClientOriginalName();

            // Generate proper filename based on document type
            $fileName = $this->generateFileName($request, $originalName);
            $path = $file->getRealPath();

            // Check for duplicate documents before uploading
            $jenisDokumen = $this->mapJenisDokumen($request->jenis_dokumen, $request->bulan, $request->triwulan);
            
            $duplicateQuery = Bukti_Dukung::where('jenis', $jenisDokumen);
            
            if ($isRenstraDetail) {
                $duplicateQuery->where('renstra_id', $renstra->id)->whereNull('kegiatan_id');
            } else {
                $duplicateQuery->where('kegiatan_id', $fakeKegiatan->id);
            }
            
            // Check for duplicates based on document type
             if (in_array($request->jenis_dokumen, ['skp_bulanan', 'skp_tahunan', 'sk_penerima_triwulan', 'piagam_penghargaan_triwulan', 'rekap_pemilihan_triwulan', 'penetapan_mekanisme'])) {
                 // For unique documents (only one allowed per period/context)
                 $existingDocument = $duplicateQuery->first();
                 
                 if ($existingDocument) {
                     $contextLabel = $isRenstraDetail ? "Renstra" : "kegiatan";
                     $jenisLabel = $this->getJenisLabel($request->jenis_dokumen);
                     $duplicateMessage = "Dokumen {$jenisLabel} sudah ada untuk {$contextLabel} ini. Silakan hapus dokumen yang lama terlebih dahulu jika ingin mengganti.";
                     
                     if ($request->ajax()) {
                         return response()->json([
                             'success' => false,
                             'message' => $duplicateMessage
                         ], 422);
                     }
                     
                     $redirectUrl = $this->getRedirectUrl($request);
                     return redirect($redirectUrl)->with('error', $duplicateMessage);
                 }
             } else {
                 // For general documents (multiple allowed but check for exact filename)
                 $namaFile = $request->nama_dokumen_custom ?: $fileName;
                 $existingDocument = $duplicateQuery->where('nama_dokumen', $namaFile)->first();
                 
                 if ($existingDocument) {
                     $contextLabel = $isRenstraDetail ? "Renstra" : "kegiatan";
                     $jenisLabel = $this->getJenisLabel($request->jenis_dokumen);
                     $duplicateMessage = "Dokumen {$jenisLabel} dengan nama '{$namaFile}' sudah ada untuk {$contextLabel} ini. Silakan gunakan nama yang berbeda atau hapus dokumen yang lama.";
                     
                     if ($request->ajax()) {
                         return response()->json([
                             'success' => false,
                             'message' => $duplicateMessage
                         ], 422);
                     }
                     
                     $redirectUrl = $this->getRedirectUrl($request);
                     return redirect($redirectUrl)->with('error', $duplicateMessage);
                 }
             }

            // Upload file to Google Drive using service
            $googleDriveService = new \App\Services\GoogleDriveOAuthService();
            $uploadResult = $googleDriveService->uploadFile($file, $fileName, $folder_id);

            // Check if upload was successful
            if ($uploadResult && $uploadResult['success']) {
                $fileInfo = [
                    'id' => $uploadResult['file_id'],
                    'webViewLink' => $uploadResult['webViewLink']
                ];

                // Create record in BuktiDukung model
                $buktiDukung = new Bukti_Dukung();
                $buktiDukung->jenis = $this->mapJenisDokumen($request->jenis_dokumen, $request->bulan, $request->triwulan);
                $buktiDukung->nama_dokumen = $request->nama_dokumen_custom ?: $fileName;
                $buktiDukung->file_id = $fileInfo['id'];
                $buktiDukung->webViewLink = $fileInfo['webViewLink'];

                // Set kegiatan_id and renstra_id based on context
                if ($isRenstraDetail) {
                    // For Renstra documents, kegiatan_id is null and renstra_id is set
                    $buktiDukung->kegiatan_id = null;
                    $buktiDukung->renstra_id = $renstra->id;
                } else {
                    // For Kegiatan documents, kegiatan_id is set and renstra_id is required (setiap kegiatan harus memiliki renstra)
                    $buktiDukung->kegiatan_id = $fakeKegiatan->id;
                    $buktiDukung->renstra_id = $fakeKegiatan->renstra_id;
                }

                $buktiDukung->save();

                // Return success message with document type and preserve tab
                $jenisLabel = $this->getJenisLabel($request->jenis_dokumen);
                $contextLabel = $isRenstraDetail ? "Renstra" : ($isPKDetail ? "PK" : "kegiatan");
                $successMessage = $jenisLabel . ' berhasil diunggah untuk ' . $contextLabel;

                // Log successful upload
                Log::info('Document uploaded successfully', [
                    'bukti_dukung_id' => $buktiDukung->id,
                    'jenis' => $buktiDukung->jenis,
                    'kegiatan_id' => $buktiDukung->kegiatan_id,
                    'renstra_id' => $buktiDukung->renstra_id,
                    'file_id' => $buktiDukung->file_id
                ]);

                // Check if this is an AJAX request
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $successMessage,
                        'bukti_dukung' => [
                            'id' => $buktiDukung->id,
                            'nama_dokumen' => $buktiDukung->nama_dokumen,
                            'jenis' => $buktiDukung->jenis,
                            'webViewLink' => $buktiDukung->webViewLink
                        ]
                    ]);
                }

                $redirectUrl = $this->getRedirectUrl($request);
                return redirect($redirectUrl)->with('success', $successMessage);
            } else {
                // Handle upload failure
                Log::error('File upload to Google Drive failed', [
                    'upload_result' => $uploadResult
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengunggah file ke Google Drive'
                    ], 500);
                }

                return redirect()->back()->with('error', 'Gagal mengunggah file ke Google Drive');
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('File upload error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return error response
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate proper filename for update based on existing bukti dukung
     */
    private function generateFileNameForUpdate($buktiDukung, $originalName)
    {
        $timestamp = now()->format('YmdHis');
        
        // Check if this is a Renstra document (kegiatan_id is null) or Kegiatan document
        if ($buktiDukung->kegiatan_id === null) {
            // For Renstra context, use renstra data
            $renstra = \App\Models\Renstra::find($buktiDukung->renstra_id);
            $tahun = $renstra ? date('Y', strtotime($renstra->periode_awal)) : date('Y');
            $namaKegiatan = $renstra ? $renstra->nama_renstra : 'Renstra';
        } else {
            // For Kegiatan context (including PK), use kegiatan data
            $kegiatan = \App\Models\Kegiatan::find($buktiDukung->kegiatan_id);
            $tahun = $kegiatan ? $kegiatan->tahun_berjalan : date('Y');
            $namaKegiatan = $kegiatan ? $kegiatan->nama_kegiatan : 'Kegiatan';
        }
        
        // Map jenis dokumen from database format back to form format
        $jenisDokumen = $this->mapJenisFromDatabase($buktiDukung->jenis);
        
        switch ($jenisDokumen) {
            case 'skp_bulanan':
                // Extract month from jenis if available
                preg_match('/SKP Bulanan (\w+)/', $buktiDukung->jenis, $matches);
                $bulanNama = $matches[1] ?? 'Unknown';
                $bulanMap = [
                    'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
                    'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
                    'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'
                ];
                $bulan = $bulanMap[$bulanNama] ?? '01';
                return "SKP_Bulanan_{$bulan}_{$tahun}_{$timestamp}.pdf";
            case 'skp_tahunan':
                return "SKP_Tahunan_{$tahun}_{$timestamp}.pdf";
            case 'dokumen_pk':
                // Untuk dokumen PK, gunakan nama yang konsisten dengan upload biasa
                return "Perjanjian_Kinerja_{$tahun}_{$timestamp}." . pathinfo($originalName, PATHINFO_EXTENSION);
            case 'notulensi':
                return "Notulensi_{$namaKegiatan}_{$tahun}_{$timestamp}." . pathinfo($originalName, PATHINFO_EXTENSION);
            case 'surat_undangan':
                return "Surat_Undangan_{$namaKegiatan}_{$tahun}_{$timestamp}." . pathinfo($originalName, PATHINFO_EXTENSION);
            case 'daftar_hadir':
                return "Daftar_Hadir_{$namaKegiatan}_{$tahun}_{$timestamp}." . pathinfo($originalName, PATHINFO_EXTENSION);
            case 'dokumentasi':
                return "Dokumentasi_{$namaKegiatan}_{$tahun}_{$timestamp}." . pathinfo($originalName, PATHINFO_EXTENSION);
            case 'penetapan_mekanisme':
                return "Penetapan_Mekanisme_Reward_Punishment_{$tahun}_{$timestamp}.pdf";
            case 'sk_penerima_triwulan':
                // Extract triwulan from jenis if available
                preg_match('/sk_penerima_triwulan_(\d+)/', $buktiDukung->jenis, $matches);
                $triwulan = $matches[1] ?? '1';
                return "SK_Penerima_Pegawai_Terbaik_Triwulan_{$triwulan}_{$tahun}_{$timestamp}.pdf";
            case 'piagam_penghargaan_triwulan':
                preg_match('/piagam_penghargaan_triwulan_(\d+)/', $buktiDukung->jenis, $matches);
                $triwulan = $matches[1] ?? '1';
                return "Piagam_Penghargaan_Triwulan_{$triwulan}_{$tahun}_{$timestamp}.pdf";
            case 'rekap_pemilihan_triwulan':
                preg_match('/rekap_pemilihan_triwulan_(\d+)/', $buktiDukung->jenis, $matches);
                $triwulan = $matches[1] ?? '1';
                return "Rekap_Pemilihan_Pegawai_Terbaik_Triwulan_{$triwulan}_{$tahun}_{$timestamp}.pdf";
            default:
                return $buktiDukung->nama_dokumen ?: $originalName;
        }
    }
    
    /**
     * Map jenis from database format back to form format
     */
    private function mapJenisFromDatabase($jenis)
    {
        if (strpos($jenis, 'SKP Bulanan') !== false) {
            return 'skp_bulanan';
        }
        if ($jenis === 'SKP Tahunan') {
            return 'skp_tahunan';
        }
        if ($jenis === 'Notulensi') {
            return 'notulensi';
        }
        if ($jenis === 'Surat Undangan') {
            return 'surat_undangan';
        }
        if ($jenis === 'Daftar Hadir') {
            return 'daftar_hadir';
        }
        if ($jenis === 'Dokumentasi') {
            return 'dokumentasi';
        }
        if ($jenis === 'Perjanjian Kinerja') {
            return 'dokumen_pk';
        }
        if ($jenis === 'penetapan_mekanisme') {
            return 'penetapan_mekanisme';
        }
        if (strpos($jenis, 'sk_penerima_triwulan') !== false) {
            return 'sk_penerima_triwulan';
        }
        if (strpos($jenis, 'piagam_penghargaan_triwulan') !== false) {
            return 'piagam_penghargaan_triwulan';
        }
        if (strpos($jenis, 'rekap_pemilihan_triwulan') !== false) {
            return 'rekap_pemilihan_triwulan';
        }
        return 'dokumen_lainnya';
    }

    /**
     * Generate proper filename based on document type
     */
    private function generateFileName($request, $originalName)
    {
        $isRenstraDetail = $request->boolean('is_renstra_detail');
        
        if ($isRenstraDetail) {
            // For Renstra context, use renstra data
            $renstra = \App\Models\Renstra::find($request->kegiatan_id); // kegiatan_id contains renstra_id in this context
            $tahun = $renstra ? date('Y', strtotime($renstra->periode_awal)) : date('Y');
            $namaKegiatan = $renstra ? $renstra->nama_renstra : 'Renstra';
        } else {
            // For Kegiatan context, use kegiatan data
            $kegiatan = \App\Models\Kegiatan::find($request->kegiatan_id);
            $tahun = $kegiatan ? $kegiatan->tahun_berjalan : date('Y');
            $namaKegiatan = $kegiatan ? $kegiatan->nama_kegiatan : 'Kegiatan';
        }
        
        $timestamp = now()->format('YmdHis');

        switch ($request->jenis_dokumen) {
            case 'skp_bulanan':
                $bulan = str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
                return "SKP_Bulanan_{$bulan}_{$tahun}_{$timestamp}.pdf";
            case 'skp_tahunan':
                return "SKP_Tahunan_{$tahun}_{$timestamp}.pdf";
            case 'dokumen_pk':
                return "Perjanjian_Kinerja_{$tahun}_{$timestamp}." . pathinfo($originalName, PATHINFO_EXTENSION);
            case 'notulensi':
                return "Notulensi_{$namaKegiatan}_{$tahun}_{$timestamp}." . pathinfo($originalName, PATHINFO_EXTENSION);
            case 'surat_undangan':
                return "Surat_Undangan_{$namaKegiatan}_{$tahun}_{$timestamp}." . pathinfo($originalName, PATHINFO_EXTENSION);
            case 'daftar_hadir':
                return "Daftar_Hadir_{$namaKegiatan}_{$tahun}_{$timestamp}." . pathinfo($originalName, PATHINFO_EXTENSION);
            case 'dokumentasi':
                return "Dokumentasi_{$namaKegiatan}_{$tahun}_{$timestamp}." . pathinfo($originalName, PATHINFO_EXTENSION);
            case 'penetapan_mekanisme':
                return "Penetapan_Mekanisme_Reward_Punishment_{$tahun}_{$timestamp}.pdf";
            case 'sk_penerima_triwulan':
                $triwulan = $request->triwulan ?: 1;
                return "SK_Penerima_Pegawai_Terbaik_Triwulan_{$triwulan}_{$tahun}_{$timestamp}.pdf";
            case 'piagam_penghargaan_triwulan':
                $triwulan = $request->triwulan ?: 1;
                return "Piagam_Penghargaan_Triwulan_{$triwulan}_{$tahun}_{$timestamp}.pdf";
            case 'rekap_pemilihan_triwulan':
                $triwulan = $request->triwulan ?: 1;
                return "Rekap_Pemilihan_Pegawai_Terbaik_Triwulan_{$triwulan}_{$tahun}_{$timestamp}.pdf";
            default:
                return $request->nama_dokumen_custom ?: $originalName;
        }
    }

    /**
     * Map jenis dokumen to database format (optimized for shorter strings)
     */
    private function mapJenisDokumen($jenisDokumen, $bulan = null, $triwulan = null)
    {
        switch ($jenisDokumen) {
            case 'skp_bulanan':
                $namaBulan = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];
                return 'SKP Bulanan ' . ($namaBulan[$bulan] ?? $bulan);
            case 'skp_tahunan':
                return 'SKP Tahunan';
            case 'notulensi':
                return 'Notulensi';
            case 'surat_undangan':
                return 'Surat Undangan';
            case 'daftar_hadir':
                return 'Daftar Hadir';
            case 'dokumentasi':
                return 'Dokumentasi';
            case 'dokumen_pk':
                return 'Perjanjian Kinerja';
            case 'penetapan_mekanisme':
                return 'penetapan_mekanisme';
            case 'sk_penerima_triwulan':
                return 'sk_penerima_triwulan_' . ($triwulan ?? 1);
            case 'piagam_penghargaan_triwulan':
                return 'piagam_penghargaan_triwulan_' . ($triwulan ?? 1);
            case 'rekap_pemilihan_triwulan':
                return 'rekap_pemilihan_triwulan_' . ($triwulan ?? 1);
            // Capaian Kinerja document types with triwulan suffix
            case 'notulensi_triwulan':
                return 'notulensi_triwulan_' . ($triwulan ?? 1);
            case 'surat_undangan_triwulan':
                return 'surat_undangan_triwulan_' . ($triwulan ?? 1);
            case 'daftar_hadir_triwulan':
                return 'daftar_hadir_triwulan_' . ($triwulan ?? 1);
            case 'fra_triwulan':
                return 'fra_triwulan_' . ($triwulan ?? 1);
            default:
                return 'dokumen_lainnya';
        }
    }

    /**
     * Get label for jenis dokumen
     */
    private function getJenisLabel($jenisDokumen)
    {
        switch ($jenisDokumen) {
            case 'notulensi':
                return 'Notulensi';
            case 'surat_undangan':
                return 'Surat Undangan';
            case 'daftar_hadir':
                return 'Daftar Hadir';
            case 'dokumentasi':
                return 'Dokumentasi';
            case 'dokumen_pk':
                return 'Perjanjian Kinerja';
            case 'skp_bulanan':
                return 'SKP Bulanan';
            case 'skp_tahunan':
                return 'SKP Tahunan';
            case 'penetapan_mekanisme':
                return 'Penetapan Mekanisme';
            case 'sk_penerima_triwulan':
                return 'SK Penerima Pegawai Terbaik';
            case 'piagam_penghargaan_triwulan':
                return 'Piagam Penghargaan';
            case 'rekap_pemilihan_triwulan':
                return 'Rekap Pemilihan Pegawai Terbaik';
            case 'notulensi_triwulan':
                return 'Notulensi Triwulan';
            case 'surat_undangan_triwulan':
                return 'Surat Undangan Triwulan';
            case 'daftar_hadir_triwulan':
                return 'Daftar Hadir Triwulan';
            case 'fra_triwulan':
                return 'FRA Triwulan';
            default:
                return 'Dokumen';
        }
    }

    /**
     * Get redirect URL with preserved tab
     */
    private function getRedirectUrl($request)
    {
        $currentTab = $request->input('current_tab');
        $backUrl = url()->previous();

        if ($currentTab) {
            // Add hash to preserve tab for all tabs including penetapan
            return $backUrl . '#' . $currentTab;
        }

        return $backUrl;
    }

    /**
     * Delete dokumen from Google Drive and database
     */
    public function destroy($bukti_dukung)
    {
        Log::info('BuktiDukungController::destroy called with ID: ' . $bukti_dukung);

        try {
            $buktiDukung = Bukti_Dukung::findOrFail($bukti_dukung);
            $namaDoc = $buktiDukung->nama_dokumen;
            $fileId = $buktiDukung->file_id;

            Log::info('Attempting to delete document', [
                'id' => $bukti_dukung,
                'nama' => $namaDoc,
                'file_id' => $fileId
            ]);

            $accessToken = $this->token();

            if (!$accessToken) {
                Log::error('Failed to get access token for deletion');

                // Handle AJAX request
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mendapatkan token akses Google Drive'
                    ], 500);
                }

                return redirect()->back()->with('error', 'Gagal mendapatkan token akses Google Drive');
            }

            // Move file to trash in Google Drive first (safer than permanent deletion)
            Log::info('Moving file to trash in Google Drive', ['file_id' => $fileId]);

            $googleDriveService = new \App\Services\GoogleDriveOAuthService();
            $trashResult = $googleDriveService->moveToTrash($fileId);

            Log::info('Google Drive trash operation result', [
                'file_id' => $fileId,
                'trash_success' => $trashResult
            ]);

            if ($trashResult) {
                // Delete from database
                $buktiDukung->delete();

                Log::info('Document successfully deleted', [
                    'nama' => $namaDoc,
                    'google_drive_status' => 'moved_to_trash'
                ]);

                // Handle AJAX request
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Dokumen '{$namaDoc}' berhasil dihapus dari database dan dipindahkan ke trash Google Drive"
                    ]);
                }

                $redirectUrl = $this->getRedirectUrl(request());
                return redirect($redirectUrl)->with('success', "Dokumen '{$namaDoc}' berhasil dihapus dari database dan dipindahkan ke trash Google Drive");
            } else {
                Log::error('Failed to move file to trash in Google Drive', [
                    'file_id' => $fileId,
                    'trash_result' => $trashResult
                ]);

                // Try to delete from database anyway if Google Drive trash operation fails
                $buktiDukung->delete();

                // Handle AJAX request
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Dokumen dihapus dari database, tetapi gagal dipindahkan ke trash Google Drive"
                    ]);
                }

                $redirectUrl = $this->getRedirectUrl(request());
                return redirect($redirectUrl)->with('error', "Dokumen dihapus dari database, tetapi gagal dipindahkan ke trash Google Drive");
            }
        } catch (\Exception $e) {
            Log::error('File deletion error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Handle AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            $redirectUrl = $this->getRedirectUrl(request());
            return redirect($redirectUrl)->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * View/Download document
     */
    public function view($id)
    {
        try {
            $buktiDukung = Bukti_Dukung::findOrFail($id);
            return redirect($buktiDukung->webViewLink);
        } catch (\Exception $e) {
            Log::error('File view error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan');
        }
    }

    /**
     * Update document name
     */
    public function updateName(Request $request, $id)
    {
        try {
            $buktiDukung = Bukti_Dukung::findOrFail($id);

            $request->validate([
                'nama_dokumen' => 'required|string|max:255'
            ]);

            // Update Google Drive file name if file_id exists
            if ($buktiDukung->file_id) {
                $googleDriveService = new GoogleDriveOAuthService();
                $driveUpdateResult = $googleDriveService->updateFileName($buktiDukung->file_id, $request->nama_dokumen);
                
                if (!$driveUpdateResult) {
                    Log::warning('Failed to update file name on Google Drive', [
                        'file_id' => $buktiDukung->file_id,
                        'new_name' => $request->nama_dokumen
                    ]);
                    // Continue with database update even if Google Drive update fails
                }
            }

            $buktiDukung->nama_dokumen = $request->nama_dokumen;
            $buktiDukung->save();

            return redirect()->back()->with('success', 'Nama dokumen berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Document name update error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal memperbarui nama dokumen');
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
            $buktiDukung = Bukti_Dukung::findOrFail($id);

            $request->validate([
                'dokumen' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240' // Max 10MB
            ]);

            $accessToken = $this->token();

            if (!$accessToken) {
                return redirect()->back()->with('error', 'Gagal mendapatkan token akses Google Drive');
            }

            // Get specific Google Drive folder using service
            $googleDriveService = new \App\Services\GoogleDriveOAuthService();
            
            // Check if this is a Renstra document (kegiatan_id is null) or Kegiatan document
            if ($buktiDukung->kegiatan_id === null) {
                // Use Renstra-specific folder
                $renstra = \App\Models\Renstra::find($buktiDukung->renstra_id);
                $folder_id = $googleDriveService->getRenstraFolderId($renstra);
                
                if (!$folder_id) {
                    Log::error('Failed to get Renstra folder ID', ['renstra_id' => $buktiDukung->renstra_id]);
                    return redirect()->back()->with('error', 'Gagal mendapatkan folder Renstra');
                }
            } else {
                // Use kegiatan-specific folder
                $kegiatan = \App\Models\Kegiatan::find($buktiDukung->kegiatan_id);
                $folder_id = $googleDriveService->getKegiatanFolderId($kegiatan);
                
                if (!$folder_id) {
                    Log::error('Failed to get Kegiatan folder ID', ['kegiatan_id' => $buktiDukung->kegiatan_id]);
                    return redirect()->back()->with('error', 'Gagal mendapatkan folder kegiatan');
                }
            }
            
            Log::info('Using folder for bukti dukung update', [
                'bukti_dukung_id' => $buktiDukung->id,
                'folder_id' => $folder_id,
                'renstra_id' => $buktiDukung->renstra_id,
                'kegiatan_id' => $buktiDukung->kegiatan_id
            ]);

            // Persiapkan file
            $file = $request->file('dokumen');
            $originalName = $file->getClientOriginalName();
            
            // Generate proper filename based on document type and context
            $name = $this->generateFileNameForUpdate($buktiDukung, $originalName);
            $path = $file->getRealPath();

            // Log informasi file
            Log::info('File Update Details', [
                'bukti_dukung_id' => $buktiDukung->id,
                'original_name' => $originalName,
                'generated_name' => $name,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);

            // Unggah file baru ke Google Drive
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

                // Move old file to trash in Google Drive if exists
                if ($buktiDukung->file_id) {
                    $trashResult = $googleDriveService->moveToTrash($buktiDukung->file_id);
                    
                    if ($trashResult['success']) {
                        Log::info('Old file moved to trash successfully', [
                            'old_file_id' => $buktiDukung->file_id,
                            'new_file_id' => $file_id
                        ]);
                    } else {
                        Log::warning('Failed to move old file to trash', [
                            'old_file_id' => $buktiDukung->file_id,
                            'error' => $trashResult['message'] ?? 'Unknown error'
                        ]);
                    }
                }

                // Dapatkan detail file baru
                $fileDetails = Http::withToken($accessToken)
                    ->get("https://www.googleapis.com/drive/v3/files/{$file_id}", [
                        'fields' => 'id, name, webViewLink, webContentLink'
                    ]);

                if ($fileDetails->successful()) {
                    $fileInfo = $fileDetails->json();

                    // Update informasi dokumen
                    $buktiDukung->file_id = $fileInfo['id'];
                    $buktiDukung->webViewLink = $fileInfo['webViewLink'] ?? null;

                    // Simpan perubahan
                    $buktiDukung->save();

                    return redirect()->back()->with('success', 'File dokumen berhasil diperbarui');
                } else {
                    Log::error('Failed to retrieve updated file details', [
                        'response' => $fileDetails->body(),
                        'status' => $fileDetails->status()
                    ]);
                    return redirect()->back()->with('error', 'Gagal mendapatkan detail file');
                }
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

    /**
     * Check if document is reward punishment related
     */
    private function isRewardPunishmentDocument($jenisDokumen)
    {
        return in_array($jenisDokumen, [
            'penetapan_mekanisme',
            'sk_penerima_triwulan',
            'piagam_penghargaan_triwulan',
            'rekap_pemilihan_triwulan'
        ]);
    }

    /**
     * Check if upload is allowed in specific quarter
     */
    private function canUploadInQuarter($quarter, $year)
    {
        $currentDate = \Carbon\Carbon::now();
        $quarterInfo = $this->getQuarterPeriodForUpload($quarter, $year);

        // Allow upload only during the quarter period
        return $currentDate->between($quarterInfo['start'], $quarterInfo['end']);
    }

    /**
     * Get quarter period dates for upload validation
     */
    private function getQuarterPeriodForUpload($quarter, $year)
    {
        $quarters = [
            1 => ['start' => "$year-01-01", 'end' => "$year-03-31"],
            2 => ['start' => "$year-04-01", 'end' => "$year-06-30"],
            3 => ['start' => "$year-07-01", 'end' => "$year-09-30"],
            4 => ['start' => "$year-10-01", 'end' => "$year-12-31"],
        ];

        return [
            'start' => \Carbon\Carbon::parse($quarters[$quarter]['start']),
            'end' => \Carbon\Carbon::parse($quarters[$quarter]['end'])
        ];
    }

    /**
     * Get quarter status for upload validation
     */
    private function getQuarterStatusForUpload($quarter, $year)
    {
        $currentDate = \Carbon\Carbon::now();
        $quarterInfo = $this->getQuarterPeriodForUpload($quarter, $year);

        if ($currentDate->lt($quarterInfo['start'])) {
            return 'upcoming';
        }

        if ($currentDate->gt($quarterInfo['end'])) {
            return 'closed';
        }

        return 'active';
    }

    /**
     * Check if document is Renstra related
     */
    private function isRenstraDocument($kegiatan)
    {
        // Check if this is a Renstra-specific kegiatan based on nama_kegiatan
        $renstraKegiatanNames = [
            'Reviu Renstra',
            'Reviu Target Renstra',
            'Capaian Target Renstra',
            'Rencana Strategis'
        ];

        foreach ($renstraKegiatanNames as $name) {
            if (stripos($kegiatan->nama_kegiatan, $name) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if kegiatan is capaian kinerja related
     */
    private function isCapaianKinerjaKegiatan($kegiatan)
    {
        // Check if this is a capaian kinerja kegiatan based on nama_kegiatan
        $capaianKinerjaNames = [
            'Monitoring Capaian Kinerja FRA',
            'Capaian Kinerja FRA',
            'Capaian Kinerja'
        ];

        foreach ($capaianKinerjaNames as $name) {
            if (stripos($kegiatan->nama_kegiatan, $name) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if document is SKP related
     */
    private function isSKPDocument($jenisDokumen)
    {
        return in_array($jenisDokumen, [
            'skp_bulanan',
            'skp_tahunan'
        ]);
    }

    /**
     * Upload SKP document using dedicated SKP table
     */
    public function uploadSkp(Request $request)
    {
        // Validate the request
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'dokumen' => 'file|required|mimes:pdf|max:5120', // PDF only, max 5MB
            'user_id' => 'required|integer|exists:pengguna,id',
            'kegiatan_id' => 'required|integer|exists:kegiatan,id',
            'jenis' => 'required|in:bulanan,tahunan',
            'bulan' => 'nullable|integer|min:1|max:12', // required for monthly SKP
            'tahun' => 'required|integer|min:2020|max:2030',
        ], [
            'dokumen.required' => 'File SKP harus diunggah.',
            'dokumen.file' => 'File yang diunggah harus berupa file yang valid.',
            'dokumen.mimes' => 'File SKP harus berformat PDF.',
            'dokumen.max' => 'Ukuran file tidak boleh lebih dari 5MB.',
            'user_id.required' => 'ID pengguna diperlukan.',
            'user_id.exists' => 'Pengguna tidak ditemukan.',
            'kegiatan_id.required' => 'ID kegiatan diperlukan.',
            'kegiatan_id.exists' => 'Kegiatan tidak ditemukan.',
            'jenis.required' => 'Jenis SKP harus dipilih.',
            'jenis.in' => 'Jenis SKP tidak valid.',
            'bulan.required_if' => 'Bulan harus diisi untuk SKP bulanan.',
            'tahun.required' => 'Tahun harus diisi.',
        ]);

        // Additional validation for monthly SKP
        if ($request->jenis === 'bulanan' && !$request->bulan) {
            $validator->after(function ($validator) {
                $validator->errors()->add('bulan', 'Bulan harus diisi untuk SKP bulanan.');
            });
        }

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Get user and kegiatan
            $user = Pengguna::findOrFail($request->user_id);
            $kegiatan = Kegiatan::findOrFail($request->kegiatan_id);

            // Check for existing SKP
            $existingSkp = Skp::where('user_id', $request->user_id)
                ->where('kegiatan_id', $request->kegiatan_id)
                ->where('jenis', $request->jenis)
                ->where('tahun', $request->tahun);

            if ($request->jenis === 'bulanan') {
                $existingSkp->where('bulan', $request->bulan);
            }

            $existingSkp = $existingSkp->first();

            if ($existingSkp) {
                $jenisLabel = $request->jenis === 'bulanan' ? 'SKP Bulanan' : 'SKP Tahunan';
                $periodLabel = $request->jenis === 'bulanan'
                    ? " bulan " . str_pad($request->bulan, 2, '0', STR_PAD_LEFT) . " tahun {$request->tahun}"
                    : " tahun {$request->tahun}";

                $errorMessage = "{$jenisLabel}{$periodLabel} untuk {$user->name} sudah ada. " .
                    "Silahkan hapus yang lama terlebih dahulu atau gunakan fitur update dokumen.";

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 422);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Get Google Drive service
            $googleDriveService = new \App\Services\GoogleDriveOAuthService();

            // Get or create kegiatan folder first
            $kegiatanFolderId = $googleDriveService->getKegiatanFolderId($kegiatan);

            if (!$kegiatanFolderId) {
                throw new \Exception('Gagal mendapatkan atau membuat folder kegiatan');
            }

            // Create SKP folder structure
            $skpFolderResult = $googleDriveService->createSKPFolder($kegiatan->nama_kegiatan, $kegiatan->tahun_berjalan);

            if (!$skpFolderResult['success'] || !isset($skpFolderResult['skp_folders'])) {
                throw new \Exception('Gagal membuat struktur folder SKP');
            }

            $skpFolders = $skpFolderResult['skp_folders'];
            $folder_id = null;

            if ($request->jenis === 'bulanan' && $request->bulan) {
                // Get SKP Bulanan folder
                $skpBulananFolderId = $skpFolders['skp_bulanan_folder_id'];

                if ($skpBulananFolderId) {
                    // Get or create specific monthly folder
                    $monthlyFolderId = $googleDriveService->getMonthlyFolderId($skpBulananFolderId, $request->bulan);

                    if ($monthlyFolderId) {
                        $folder_id = $monthlyFolderId;
                        Log::info('Using monthly SKP folder for upload', [
                            'kegiatan_id' => $kegiatan->id,
                            'bulan' => $request->bulan,
                            'folder_id' => $folder_id
                        ]);
                    } else {
                        throw new \Exception('Gagal membuat folder bulanan untuk SKP');
                    }
                } else {
                    throw new \Exception('Folder SKP Bulanan tidak ditemukan');
                }
            } elseif ($request->jenis === 'tahunan') {
                // Use SKP Tahunan folder
                $folder_id = $skpFolders['skp_tahunan_folder_id'];

                if (!$folder_id) {
                    throw new \Exception('Folder SKP Tahunan tidak ditemukan');
                }

                Log::info('Using yearly SKP folder for upload', [
                    'kegiatan_id' => $kegiatan->id,
                    'tahun' => $request->tahun,
                    'folder_id' => $folder_id
                ]);
            } else {
                throw new \Exception('Jenis SKP tidak valid');
            }

            // Prepare file details
            $file = $request->file('dokumen');
            $originalName = $file->getClientOriginalName();

            // Generate proper filename for SKP
            $jenisLabel = $request->jenis === 'bulanan' ? 'SKP_Bulanan' : 'SKP_Tahunan';
            $periodLabel = $request->jenis === 'bulanan'
                ? "_" . str_pad($request->bulan, 2, '0', STR_PAD_LEFT) . "_{$request->tahun}"
                : "_{$request->tahun}";
            $fileName = "{$jenisLabel}_{$user->name}{$periodLabel}.pdf";

            // Upload file to Google Drive using service
            $uploadResult = $googleDriveService->uploadFile($file, $fileName, $folder_id);

            // Check if upload was successful
            if ($uploadResult && $uploadResult['success']) {
                // Create record in SKP model
                $skp = new Skp();
                $skp->user_id = $request->user_id;
                $skp->kegiatan_id = $request->kegiatan_id;
                $skp->jenis = $request->jenis;
                $skp->bulan = $request->jenis === 'bulanan' ? $request->bulan : null;
                $skp->tahun = $request->tahun;
                $skp->file_id = $uploadResult['file_id'];
                $skp->webViewLink = $uploadResult['webViewLink'];
                $skp->nama_file = $fileName;
                $skp->uploaded_by = Auth::id();
                $skp->uploaded_at = now();
                $skp->save();

                // Return success message
                $jenisLabel = $request->jenis === 'bulanan' ? 'SKP Bulanan' : 'SKP Tahunan';
                $periodLabel = $request->jenis === 'bulanan'
                    ? " bulan " . str_pad($request->bulan, 2, '0', STR_PAD_LEFT) . " tahun {$request->tahun}"
                    : " tahun {$request->tahun}";
                $successMessage = "{$jenisLabel}{$periodLabel} untuk {$user->name} berhasil diunggah";

                // Log successful upload
                Log::info('SKP uploaded successfully', [
                    'skp_id' => $skp->id,
                    'user_id' => $skp->user_id,
                    'kegiatan_id' => $skp->kegiatan_id,
                    'jenis' => $skp->jenis,
                    'bulan' => $skp->bulan,
                    'tahun' => $skp->tahun,
                    'file_id' => $skp->file_id
                ]);

                // Check if this is an AJAX request
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $successMessage,
                        'skp' => [
                            'id' => $skp->id,
                            'nama_file' => $skp->nama_file,
                            'jenis' => $skp->jenis,
                            'bulan' => $skp->bulan,
                            'tahun' => $skp->tahun,
                            'webViewLink' => $skp->webViewLink
                        ]
                    ]);
                }

                return redirect()->back()->with('success', $successMessage);
            } else {
                Log::error('Failed to upload SKP file to Google Drive', [
                    'upload_result' => $uploadResult
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengunggah file ke Google Drive: ' . ($uploadResult['message'] ?? 'Unknown error')
                    ], 500);
                }

                return redirect()->back()->with('error', 'Gagal mengunggah file ke Google Drive: ' . ($uploadResult['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('SKP upload error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete SKP document
     */
    public function deleteSkp($id)
    {
        try {
            $skp = Skp::findOrFail($id);
            $namaFile = $skp->nama_file;
            $fileId = $skp->file_id;

            // Use GoogleDriveOAuthService to move file to trash
            $googleDriveService = new \App\Services\GoogleDriveOAuthService();
            $trashResult = $googleDriveService->moveToTrash($fileId);

            if ($trashResult['success']) {
                // Delete from database
                $skp->delete();

                Log::info('SKP file deleted successfully', [
                    'skp_id' => $id,
                    'file_id' => $fileId,
                    'nama_file' => $namaFile
                ]);

                // Handle AJAX request
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => "SKP '{$namaFile}' berhasil dihapus"
                    ]);
                }

                return redirect()->back()->with('success', "SKP '{$namaFile}' berhasil dihapus dari database dan dipindahkan ke trash Google Drive");
            } else {
                Log::error('Failed to move SKP file to trash in Google Drive', [
                    'file_id' => $fileId,
                    'trash_result' => $trashResult
                ]);

                // Try to delete from database anyway if Google Drive trash operation fails
                $skp->delete();

                // Handle AJAX request
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => "SKP dihapus dari database, tetapi gagal dipindahkan ke trash Google Drive"
                    ]);
                }

                return redirect()->back()->with('error', "SKP dihapus dari database, tetapi gagal dipindahkan ke trash Google Drive");
            }
        } catch (\Exception $e) {
            Log::error('SKP deletion error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Handle AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * View SKP document
     */
    public function viewSkp($id)
    {
        try {
            $skp = Skp::findOrFail($id);
            return redirect($skp->webViewLink);
        } catch (\Exception $e) {
            Log::error('SKP view error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'SKP tidak ditemukan');
        }
    }

    /**
     * Get SKP statistics for a user
     */
    public function getSkpStats($userId, $kegiatanId, $tahun)
    {
        try {
            $stats = Skp::getSkpStats($userId, $kegiatanId, $tahun);

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('SKP stats error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan statistik SKP'
            ], 500);
        }
    }
}
