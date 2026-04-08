<?php

namespace App\Http\Controllers;

use App\Models\Renstra;
use App\Models\Dokumen_Kegiatan;
use App\Models\Bukti_Dukung;
use App\Services\GoogleDriveOAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RenstraController extends Controller
{
    
    /**
     * Menyimpan renstra baru.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_renstra' => 'required|string|max:255',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            'required' => 'Kolom :attribute harus diisi.',
            'string' => 'Kolom :attribute harus berupa teks.',
            'max' => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
            'date' => 'Kolom :attribute harus berupa tanggal yang valid.',
            'after_or_equal' => 'Kolom :attribute harus setelah atau sama dengan tanggal mulai.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validasi tambahan untuk periode 5 tahun
        $tahunAwal = date('Y', strtotime($request->periode_awal));
        $tahunAkhir = date('Y', strtotime($request->periode_akhir));
        if (($tahunAkhir - $tahunAwal) != 4) {
            return redirect()->back()->with('error', 'Periode Renstra harus 5 tahun')->withInput();
        }

        // Check for duplicate periods
        $existingRenstra = Renstra::where('periode_awal', $request->periode_awal)
            ->where('periode_akhir', $request->periode_akhir)
            ->first();

        if ($existingRenstra) {
            return redirect()->back()->with('error', 'Periode ini sudah ada. Silakan masukkan periode yang berbeda.')->withInput();
        }

        // Simpan data renstra baru
        $renstra = new Renstra();
        $renstra->nama_renstra = $request->nama_renstra;
        $renstra->periode_awal = $request->periode_awal;
        $renstra->periode_akhir = $request->periode_akhir;
        $renstra->tanggal_mulai = $request->tanggal_mulai;
        $renstra->tanggal_selesai = $request->tanggal_selesai;
        $renstra->save();

        // Otomatis membuat folder Google Drive untuk periode renstra menggunakan OAuth
        $message = 'Periode Renstra berhasil ditambahkan. Silahkan unggah dokumen Renstra.';
        
        // Gunakan OAuth system (yang sudah working) untuk auto-create folders
        try {
            $googleDriveOAuthService = new GoogleDriveOAuthService();
            
            // Buat folder-folder SAKIP untuk setiap tahun dalam periode
            $createdFolders = $googleDriveOAuthService->createSakipFoldersForPeriod($tahunAwal, $tahunAkhir);
            
            // Buat folder Renstra periode untuk menyimpan dokumen dan bukti dukung Renstra
            $renstraFolderResult = $googleDriveOAuthService->createRenstraPeriodeFolder($tahunAwal, $tahunAkhir);
            
            // Update folder_id di database jika berhasil
            if ($renstraFolderResult['success'] && isset($renstraFolderResult['folder_id'])) {
                $renstra->update(['folder_id' => $renstraFolderResult['folder_id']]);
                
                Log::info("Renstra folder ID saved to database", [
                    'renstra_id' => $renstra->id,
                    'folder_id' => $renstraFolderResult['folder_id'],
                    'periode' => "{$tahunAwal}-{$tahunAkhir}"
                ]);
            }
            
            // Initialize message components
            $sakipFolderMessage = '';
            $renstraFolderMessage = '';
            
            // Format message untuk SAKIP folders
            if (!empty($createdFolders)) {
                $newFolders = array_filter($createdFolders, function($folder) {
                    return $folder['status'] === 'created';
                });
                $newFolderCount = count($newFolders);
                
                if ($newFolderCount > 0) {
                    $sakipFolderMessage = " dan {$newFolderCount} folder SAKIP tahun telah dibuat otomatis";
                } else {
                    $sakipFolderMessage = ". Folder SAKIP untuk periode {$tahunAwal}-{$tahunAkhir} sudah tersedia";
                }
            }
            
            // Format message untuk Renstra folder
            if ($renstraFolderResult['success']) {
                if ($renstraFolderResult['status'] === 'created') {
                    $renstraFolderMessage = " dan folder Renstra periode {$tahunAwal}-{$tahunAkhir} telah dibuat";
                } else {
                    $renstraFolderMessage = ". Folder Renstra periode {$tahunAwal}-{$tahunAkhir} sudah tersedia";
                }
            } else {
                $renstraFolderMessage = ". Gagal membuat folder Renstra periode: " . $renstraFolderResult['message'];
            }
            
            $message = "Periode Renstra berhasil ditambahkan{$sakipFolderMessage}{$renstraFolderMessage}. Silahkan unggah dokumen Renstra.";
            
        } catch (\Exception $e) {
            Log::error('Error creating Google Drive folders for Renstra period via OAuth', [
                'periode_awal' => $tahunAwal,
                'periode_akhir' => $tahunAkhir,
                'error' => $e->getMessage()
            ]);
            
            $message = 'Periode Renstra berhasil ditambahkan, namun terjadi masalah saat membuat folder Google Drive. Silahkan periksa konfigurasi Google Drive atau buat folder secara manual.';
        }

        return redirect()->route('manajemen.renstra')->with('success', $message);
    }

    /**
     * Proses upload dokumen renstra.
     */
    public function handleUpload(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'dokumen' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ], [
            'dokumen.required' => 'Dokumen harus diunggah.',
            'dokumen.file' => 'File harus berupa dokumen.',
            'dokumen.mimes' => 'Format file harus pdf, doc, docx, xls, atau xlsx.',
            'dokumen.max' => 'Ukuran file tidak boleh lebih dari 10MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $renstra = Renstra::findOrFail($id);

        if ($request->hasFile('dokumen')) {
            try {
                $googleDriveOAuthService = new GoogleDriveOAuthService();
                
                // Dapatkan folder ID untuk Renstra ini
                $renstraFolderId = $googleDriveOAuthService->getRenstraFolderId($renstra);
                
                if (!$renstraFolderId) {
                    Log::error('Failed to get Renstra folder ID for document upload', [
                        'renstra_id' => $renstra->id,
                        'periode_awal' => $renstra->periode_awal,
                        'periode_akhir' => $renstra->periode_akhir
                    ]);
                    
                    return redirect()->back()->with('error', 'Gagal mendapatkan folder Renstra. Silahkan coba lagi atau hubungi administrator.')->withInput();
                }

                // Hapus file lama jika ada (dari Google Drive)
                if ($renstra->dokumenKegiatan && $renstra->dokumenKegiatan->file) {
                    // Jika ada file_id di Google Drive, hapus dari sana
                    if (str_starts_with($renstra->dokumenKegiatan->file, 'http')) {
                        // Extract file ID from Google Drive URL if needed
                        $oldFileId = $renstra->dokumenKegiatan->file;
                        // Try to move old file to trash
                        $googleDriveOAuthService->moveToTrash($oldFileId);
                    } else {
                        // Delete from local storage
                        Storage::disk('public')->delete('dokumen_renstra/' . $renstra->dokumenKegiatan->file);
                    }
                    $renstra->dokumenKegiatan()->delete();
                }

                // Upload file ke Google Drive
                $file = $request->file('dokumen');
                $tahunAwal = date('Y', strtotime($renstra->periode_awal));
                $tahunAkhir = date('Y', strtotime($renstra->periode_akhir));
                $fileName = "Dokumen_Renstra_{$tahunAwal}-{$tahunAkhir}_" . time() . '.' . $file->getClientOriginalExtension();
                
                $uploadResult = $googleDriveOAuthService->uploadFile($file, $fileName, $renstraFolderId);

                if ($uploadResult && isset($uploadResult['file_id'])) {
                    // Buat dokumen baru dengan informasi Google Drive
                    $dokumen = new Dokumen_Kegiatan();
                    $dokumen->file = $uploadResult['file_id'];
                    $dokumen->nama_dokumen = 'Dokumen Renstra ' . $tahunAwal . ' - ' . $tahunAkhir;
                    $dokumen->webViewLink = $uploadResult['webViewLink'] ?? '';
                    $renstra->dokumenKegiatan()->save($dokumen);

                    Log::info('Renstra document uploaded successfully to Google Drive', [
                        'renstra_id' => $renstra->id,
                        'file_id' => $uploadResult['file_id'],
                        'folder_id' => $renstraFolderId,
                        'file_name' => $fileName
                    ]);

                    return redirect()->route('manajemen.renstra')->with('success', 'Dokumen Renstra berhasil diunggah ke Google Drive.');
                } else {
                    Log::error('Failed to upload Renstra document to Google Drive', [
                        'renstra_id' => $renstra->id,
                        'folder_id' => $renstraFolderId
                    ]);
                    
                    return redirect()->back()->with('error', 'Gagal mengunggah dokumen ke Google Drive. Silahkan coba lagi.')->withInput();
                }
                
            } catch (\Exception $e) {
                Log::error('Exception during Renstra document upload', [
                    'renstra_id' => $renstra->id,
                    'error' => $e->getMessage()
                ]);
                
                return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah dokumen: ' . $e->getMessage())->withInput();
            }
        }

        return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah dokumen.')->withInput();
    }

    /**
     * Upload bukti dukung untuk renstra
     */
    public function uploadBuktiDukung(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'dokumen' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'nama_dokumen' => 'required|string|max:255',
            'jenis' => 'required|string|max:50',
        ], [
            'required' => 'Kolom :attribute harus diisi.',
            'file' => 'File harus berupa dokumen atau gambar.',
            'mimes' => 'Format file tidak valid.',
            'max' => 'Ukuran file tidak boleh lebih dari 10MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $renstra = Renstra::findOrFail($id);

        // Upload file
        if ($request->hasFile('dokumen')) {
            try {
                $googleDriveOAuthService = new GoogleDriveOAuthService();
                
                // Dapatkan folder ID untuk Renstra ini
                $renstraFolderId = $googleDriveOAuthService->getRenstraFolderId($renstra);
                
                if (!$renstraFolderId) {
                    Log::error('Failed to get Renstra folder ID for bukti dukung upload', [
                        'renstra_id' => $renstra->id,
                        'periode_awal' => $renstra->periode_awal,
                        'periode_akhir' => $renstra->periode_akhir
                    ]);
                    
                    // Fallback ke folder umum
                    $renstraFolderId = config('services.google.renstra_id');
                    
                    if (!$renstraFolderId) {
                        return redirect()->back()->with('error', 'Gagal mendapatkan folder untuk upload. Silahkan hubungi administrator.')->withInput();
                    }
                    
                    Log::warning('Using fallback folder for Renstra bukti dukung upload', [
                        'renstra_id' => $renstra->id,
                        'fallback_folder_id' => $renstraFolderId
                    ]);
                } else {
                    Log::info('Using Renstra-specific folder for bukti dukung upload', [
                        'renstra_id' => $renstra->id,
                        'folder_id' => $renstraFolderId
                    ]);
                }

                $file = $request->file('dokumen');
                $tahunAwal = date('Y', strtotime($renstra->periode_awal));
                $tahunAkhir = date('Y', strtotime($renstra->periode_akhir));
                $fileName = "BuktiDukung_{$request->jenis}_{$tahunAwal}-{$tahunAkhir}_" . time() . '.' . $file->getClientOriginalExtension();
                
                $uploadResult = $googleDriveOAuthService->uploadFile($file, $fileName, $renstraFolderId);

                if ($uploadResult && isset($uploadResult['file_id'])) {
                    // Simpan informasi bukti dukung
                    $buktiDukung = new Bukti_Dukung();
                    $buktiDukung->jenis = $request->jenis;
                    $buktiDukung->file_id = $uploadResult['file_id'];
                    $buktiDukung->nama_dokumen = $request->nama_dokumen;
                    $buktiDukung->webViewLink = $uploadResult['webViewLink'] ?? '';
                    $buktiDukung->renstra_id = $renstra->id;
                    $buktiDukung->kegiatan_id = $request->kegiatan_id ?? null;
                    $buktiDukung->save();

                    Log::info('Renstra bukti dukung uploaded successfully to Google Drive', [
                        'renstra_id' => $renstra->id,
                        'file_id' => $uploadResult['file_id'],
                        'folder_id' => $renstraFolderId,
                        'file_name' => $fileName,
                        'jenis' => $request->jenis
                    ]);

                    return redirect()->route('renstra.show', $renstra->id)->with('success', 'Bukti dukung berhasil diunggah ke Google Drive.');
                } else {
                    Log::error('Failed to upload Renstra bukti dukung to Google Drive', [
                        'renstra_id' => $renstra->id,
                        'folder_id' => $renstraFolderId
                    ]);
                    
                    return redirect()->back()->with('error', 'Gagal mengunggah bukti dukung ke Google Drive. Silahkan coba lagi.')->withInput();
                }
                
            } catch (\Exception $e) {
                Log::error('Exception during Renstra bukti dukung upload', [
                    'renstra_id' => $renstra->id,
                    'error' => $e->getMessage()
                ]);
                
                return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah bukti dukung: ' . $e->getMessage())->withInput();
            }
        }

        return redirect()->back()->with('error', 'Gagal mengunggah bukti dukung.')->withInput();
    }

    /**
     * Menampilkan form untuk edit renstra.
     */
    public function edit($id)
    {
        $renstra = Renstra::findOrFail($id);
        return view('renstra.edit', compact('renstra'));
    }

    /**
     * Memperbarui data renstra.
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_renstra' => 'required|string|max:255',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            'required' => 'Kolom :attribute harus diisi.',
            'string' => 'Kolom :attribute harus berupa teks.',
            'max' => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
            'date' => 'Kolom :attribute harus berupa tanggal yang valid.',
            'after_or_equal' => 'Kolom :attribute harus setelah atau sama dengan tanggal mulai.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Perbarui data renstra
        $renstra = Renstra::findOrFail($id);
        $renstra->nama_renstra = $request->nama_renstra;
        $renstra->periode_awal = $request->periode_awal;
        $renstra->periode_akhir = $request->periode_akhir;
        $renstra->tanggal_mulai = $request->tanggal_mulai;
        $renstra->tanggal_selesai = $request->tanggal_selesai;
        $renstra->save();

        return redirect()->route('manajemen.renstra')->with('success', 'Data Renstra berhasil diperbarui.');
    }

    /**
     * Menghapus renstra.
     */
    public function destroy($id)
    {
        try {
            $renstra = Renstra::findOrFail($id);
            $namaRenstra = $renstra->nama_renstra;
            $tahunAwal = date('Y', strtotime($renstra->periode_awal));
            $tahunAkhir = date('Y', strtotime($renstra->periode_akhir));
            
            Log::info("Memulai penghapusan Renstra '{$namaRenstra}' periode {$tahunAwal}-{$tahunAkhir}", ['renstra_id' => $renstra->id]);
            
            $googleDriveOAuthService = new GoogleDriveOAuthService();

            // Hapus dokumen utama dari Google Drive dan database
            if ($renstra->dokumenKegiatan) {
                // Jika file disimpan di Google Drive
                if ($renstra->dokumenKegiatan->file && !str_starts_with($renstra->dokumenKegiatan->file, 'dokumen_renstra/')) {
                    try {
                        $googleDriveOAuthService->moveToTrash($renstra->dokumenKegiatan->file);
                        Log::info("Dokumen Renstra '{$namaRenstra}' berhasil dipindahkan ke sampah Google Drive", [
                            'file_id' => $renstra->dokumenKegiatan->file
                        ]);
                    } catch (\Exception $e) {
                        Log::warning("Gagal menghapus dokumen Renstra dari Google Drive: " . $e->getMessage());
                    }
                } else {
                    // Jika file disimpan di local storage
                    Storage::disk('public')->delete('dokumen_renstra/' . $renstra->dokumenKegiatan->file);
                }
                $renstra->dokumenKegiatan()->delete();
            }

            // Hapus bukti dukung dari Google Drive dan database
            foreach ($renstra->buktiDukungs as $bukti) {
                try {
                    if ($bukti->file_id && !str_starts_with($bukti->file_id, 'bukti_dukung/')) {
                        $googleDriveOAuthService->moveToTrash($bukti->file_id);
                        Log::info("Bukti dukung Renstra '{$bukti->jenis}' berhasil dipindahkan ke sampah Google Drive", [
                            'file_id' => $bukti->file_id
                        ]);
                    } else {
                        Storage::disk('public')->delete('bukti_dukung/' . $bukti->file_id);
                    }
                } catch (\Exception $e) {
                    Log::warning("Gagal menghapus bukti dukung dari Google Drive: " . $e->getMessage());
                }
            }
            $renstra->buktiDukungs()->delete();

            // Hapus folder Renstra dari Google Drive jika ada
            if ($renstra->folder_id) {
                try {
                    $googleDriveOAuthService->moveToTrash($renstra->folder_id);
                    Log::info("Folder Google Drive untuk Renstra '{$namaRenstra}' telah dipindahkan ke sampah", [
                        'folder_id' => $renstra->folder_id
                    ]);
                } catch (\Exception $e) {
                    Log::warning("Gagal menghapus folder Renstra dari Google Drive: " . $e->getMessage());
                }
            }

            // Hapus folder SAKIP untuk periode renstra
            try {
                $deletedSakipFolders = $googleDriveOAuthService->deleteSakipFoldersForPeriod($tahunAwal, $tahunAkhir);
                
                $deletedCount = 0;
                $errorCount = 0;
                $notFoundCount = 0;
                
                foreach ($deletedSakipFolders as $folderInfo) {
                    switch ($folderInfo['status']) {
                        case 'deleted':
                            $deletedCount++;
                            break;
                        case 'error':
                            $errorCount++;
                            break;
                        case 'not_found':
                            $notFoundCount++;
                            break;
                    }
                }
                
                Log::info("Proses penghapusan folder SAKIP periode {$tahunAwal}-{$tahunAkhir} selesai", [
                    'deleted_count' => $deletedCount,
                    'error_count' => $errorCount,
                    'not_found_count' => $notFoundCount,
                    'total_years' => count($deletedSakipFolders)
                ]);
                
            } catch (\Exception $e) {
                Log::warning("Gagal menghapus folder SAKIP periode {$tahunAwal}-{$tahunAkhir}: " . $e->getMessage());
            }

            // Hapus data renstra dari database
            $renstra->delete();
            
            Log::info("Renstra '{$namaRenstra}' periode {$tahunAwal}-{$tahunAkhir} berhasil dihapus beserta folder terkait");

            return redirect()->route('manajemen.renstra')->with('success', "Data Renstra '{$namaRenstra}' periode {$tahunAwal}-{$tahunAkhir} beserta semua folder terkait berhasil dihapus.");
            
        } catch (\Exception $e) {
            Log::error('Error saat menghapus Renstra', [
                'renstra_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('manajemen.renstra')->with('error', 'Terjadi kesalahan saat menghapus data Renstra. Silakan coba lagi.');
        }
    }

    /**
     * Menampilkan detail renstra menggunakan halaman detail yang sama dengan kegiatan.
     */
    public function detail($id, $year)
    {
        // Log the incoming parameters
        Log::info('RenstraController detail method called', [
            'id' => $id,
            'year' => $year
        ]);

        try {
            // Ambil data renstra dengan relasi bukti dukung yang LANGSUNG milik renstra ini (bukan milik kegiatan)
            $renstra = Renstra::with([
                'buktiDukungs' => function($query) use ($id) {
                    $query->where('renstra_id', $id)->whereNull('kegiatan_id');
                },
                'dokumenKegiatan'
            ])->findOrFail($id);
            
            // Konversi data renstra ke format yang sesuai dengan detail view
            $kegiatan = (object) [
                'id' => $renstra->id,
                'nama_kegiatan' => $renstra->nama_renstra,
                'tahun_berjalan' => $year,
                'tanggal_mulai' => $renstra->periode_awal,
                'tanggal_berakhir' => $renstra->periode_akhir,
                'sub_komponen' => (object) ['sub_komponen' => 'Manajemen Renstra'],
                'buktiDukung' => $renstra->buktiDukungs,
                'keterangan' => 'Dokumen Renstra periode ' . date('Y', strtotime($renstra->periode_awal)) . '-' . date('Y', strtotime($renstra->periode_akhir))
            ];
            
            // Tentukan status berdasarkan periode renstra
            $currentYear = date('Y');
            $startYear = date('Y', strtotime($renstra->periode_awal));
            $endYear = date('Y', strtotime($renstra->periode_akhir));
            
            if ($currentYear >= $startYear && $currentYear <= $endYear) {
                $kegiatan->status = 'Aktif';
                $kegiatan->status_class = 'bg-green-100 text-green-800';
                $kegiatan->status_dot = 'bg-green-500';
            } elseif ($currentYear < $startYear) {
                $kegiatan->status = 'Upcoming';
                $kegiatan->status_class = 'bg-blue-100 text-blue-800';
                $kegiatan->status_dot = 'bg-blue-500';
            } else {
                $kegiatan->status = 'Selesai';
                $kegiatan->status_class = 'bg-gray-100 text-gray-800';
                $kegiatan->status_dot = 'bg-gray-500';
            }
            
            // Tentukan kelengkapan bukti dukung
            $hasDocument = $renstra->dokumenKegiatan !== null;
            $hasBuktiDukung = $renstra->buktiDukungs && $renstra->buktiDukungs->count() > 0;
            
            $kegiatan->kelengkapan = ($hasDocument && $hasBuktiDukung) ? 1 : 0;
            $kegiatan->kelengkapan_class = $kegiatan->kelengkapan ? 'text-green-500' : 'text-red-500';
            
            if ($kegiatan->status == 'Selesai') {
                $kegiatan->keterangan = 'Periode berakhir';
            } else {
                $kegiatan->keterangan = $kegiatan->kelengkapan ? 'Bukti dukung lengkap' : 'Bukti dukung belum lengkap';
            }

            // Ambil dokumen kegiatan
            $dokumenKegiatan = $renstra->dokumenKegiatan ? collect([$renstra->dokumenKegiatan]) : collect();

            // Kelompokkan bukti dukung berdasarkan jenis
            $buktiDukungByJenis = $renstra->buktiDukungs->groupBy('jenis');

            // Required documents untuk renstra
            $requiredDocuments = [
                'dokumen_renstra' => 'Dokumen Renstra',
                'notulensi' => 'Notulensi',
                'surat_undangan' => 'Surat Undangan',
                'daftar_hadir' => 'Daftar Hadir'
            ];

            // Hitung dokumen yang sudah di-upload
            $uploadedCount = 0;
            if ($hasDocument) $uploadedCount++;
            
            // Hanya hitung bukti dukung yang benar-benar milik renstra ini
            foreach (['notulensi', 'surat_undangan', 'daftar_hadir'] as $jenis) {
                $jenisToCheck = ucfirst(str_replace('_', ' ', $jenis));
                if ($jenis === 'surat_undangan') $jenisToCheck = 'Surat Undangan';
                if ($jenis === 'daftar_hadir') $jenisToCheck = 'Daftar Hadir';
                
                // Pastikan hanya menghitung bukti dukung yang langsung milik renstra ini
                if ($renstra->buktiDukungs->where('jenis', $jenisToCheck)->isNotEmpty()) {
                    $uploadedCount++;
                }
            }

            $totalRequired = count($requiredDocuments);

            // Tambahkan flag untuk menandai bahwa ini adalah halaman renstra
            $isRenstraDetail = true;
            
            // Determine user permissions and upload capability
            /** @var \App\Models\Pengguna $user */
            $user = Auth::user();
            $isSuperAdmin = Auth::check() && $user->isSuperAdmin();
            $isAdmin = Auth::check() && $user->isAdmin();
            
            // Check if activity period has expired
            $activityExpired = Carbon::now()->isAfter(Carbon::parse($renstra->periode_akhir));
            
            // Determine if user can upload documents
            $canUpload = ($isSuperAdmin || $isAdmin) && (!$activityExpired || $isSuperAdmin);
            
            // Breadcrumbs for renstra detail
            $breadcrumbs = [
                ['title' => 'Perencanaan Kinerja', 'url' => route('manajemen.renstra'), 'clickable' => true],
                ['title' => 'Manajemen Renstra', 'url' => route('manajemen.renstra'), 'clickable' => true],
                ['title' => 'Detail ' . $renstra->nama_renstra, 'clickable' => false],
            ];
            
            return view('kegiatan.detail', compact(
                'kegiatan', 
                'buktiDukungByJenis', 
                'dokumenKegiatan',
                'requiredDocuments',
                'uploadedCount',
                'totalRequired',
                'isRenstraDetail',
                'canUpload',
                'isSuperAdmin',
                'isAdmin',
                'activityExpired',
                'breadcrumbs'
            ));
        } catch (\Exception $e) {
            Log::error('Error in RenstraController detail method', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Throw the exception to see full details
            throw $e;
        }
    }

    /**
     * Menampilkan detail renstra (method lama, masih diperlukan untuk keperluan lain).
     */
    public function show($id)
    {
        // Hanya muat dokumen kegiatan, tanpa memuat bukti dukung
        $renstra = Renstra::with(['dokumenKegiatan'])->findOrFail($id);
        return view('renstra.show', compact('renstra'));
    }

    

    /**
     * Menghapus dokumen renstra.
     */
    public function deleteDokumen($id)
    {
        $dokumen = Dokumen_Kegiatan::findOrFail($id);

        // Hapus file dari storage
        Storage::disk('public')->delete('dokumen_renstra/' . $dokumen->file);

        // Hapus record dari database
        $dokumen->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

    
}
