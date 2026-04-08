<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\Fra;
use App\Models\Template_Fra;
use App\Models\Template_Jenis;
use App\Models\Matriks_Fra;
use App\Models\Kegiatan;
use App\Models\Sub_Komponen;
use App\Models\Triwulan;
use App\Models\Target_Fra;
use App\Models\Target_Pk;
use App\Models\Realisasi_Fra;
use App\Models\Buktidukung_Fra;
use App\Models\Bukti_Dukung;
use Carbon\Carbon;
use App\Services\GoogleDriveFraService;
use App\Services\GoogleDriveOAuthService;
use App\Services\EnhancedFraParser;
use App\Services\KabKotaFraParser;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Pengguna;
use App\Models\Komponen;
use App\Models\Renstra;
use Illuminate\Support\Facades\Auth;

class FraController extends Controller
{
    protected $googleDriveService;

    public function __construct()
    {
        // Lazy loading untuk menghindari error saat startup
        // $this->googleDriveService akan di-instantiate saat dibutuhkan
    }

    /**
     * Get GoogleDriveFraService instance dengan lazy loading
     */
    protected function getGoogleDriveService()
    {
        if (!$this->googleDriveService) {
            $this->googleDriveService = new GoogleDriveFraService();
        }
        return $this->googleDriveService;
    }

    public function index(Request $request)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        $isSuperAdmin = Auth::check() && $user->isSuperAdmin();
        $isAdmin = Auth::check() && $user->isAdmin();
        
        $fraQuery = Fra::orderBy('tahun_berjalan', 'desc');
        $fraList = $fraQuery->paginate(9);
        $capaianKinerjaActivities = $this->getCapaianKinerja($request);

        return view('pengukuran kinerja.fra', compact(
            'fraList', 
            'capaianKinerjaActivities', 
            'isSuperAdmin', 
            'isAdmin'));
    }

    public function inputTarget($id)
    {
        try {
            $fra = Fra::with(['matriks_fra.template_fra.template_jenis'])->findOrFail($id);

            $matriksIds = $fra->matriks_fra->pluck('id');

            // Get existing Target FRA data, keyed by matriks_fra_id
            $existingTargets = Target_Fra::whereIn('matriks_fra_id', $matriksIds)
                ->get()
                ->keyBy('matriks_fra_id');

            // Get target PK data for the corresponding 'Perjanjian Kinerja' (PK) activity year
            $kegiatanPK = Kegiatan::where('tahun_berjalan', $fra->tahun_berjalan)
                ->where('nama_kegiatan', 'like', '%Perjanjian Kinerja%')
                ->first();

            $targetPkData = collect(); // Default to empty collection
            if ($kegiatanPK) {
                // ✅ FIXED: Ambil semua target PK yang ada, tidak hanya yang terkait dengan matriks FRA
                $targetPkData = Target_Pk::where('kegiatan_id', $kegiatanPK->id)
                    ->get()
                    ->keyBy('matriks_fra_id');
            }

            // Enrich the matriks_fra collection with related data for cleaner view logic
            $fra->matriks_fra->each(function ($matriks) use ($existingTargets, $targetPkData) {
                $matriks->existing_target = $existingTargets->get($matriks->id);
                $matriks->target_pk_data = $targetPkData->get($matriks->id);
            });

            // ✅ FIXED: Get users for PIC dropdown based on requirements:
            // 1. Anggota Tim di bidang umum
            // 2. Ketua Tim seluruh bidang  
            // 3. Kasubag Umum
            $penggunas = Pengguna::where(function($query) {
                $query->where('jabatan', 'Kasubag Umum')
                      ->orWhere('jabatan', 'Ketua Tim')
                      ->orWhere(function($subQuery) {
                          $subQuery->where('jabatan', 'Anggota Tim')
                                   ->where('bidang', 'Bagian Umum');
                      });
            })
            ->orderBy('jabatan')
            ->orderBy('name')
            ->get();

            // Check if has suplemen data
            $hasSuplemenData = $fra->hasTemplateJenis('PK Suplemen');

            return view('pengukuran kinerja.form_target_fra', compact(
                'fra',
                'targetPkData', // Still pass this for the main PK display lookup
                'hasSuplemenData',
                'penggunas'
            ));
        } catch (\Exception $e) {
            Log::error('Error in inputTarget: ' . $e->getMessage());
            return redirect()->route('fra.index')->with('error', 'Terjadi kesalahan saat memuat form target: ' . $e->getMessage());
        }
    }

    public function simpanTarget(Request $request, $id)
    {
        Log::info('🚀 simpanTarget started', [
            'fra_id' => $id,
            'action_type' => $request->input('action_type'),
            'request_method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept'),
            'user_agent' => $request->header('User-Agent'),
            'x_requested_with' => $request->header('X-Requested-With'),
            'csrf_token' => $request->header('X-CSRF-TOKEN') ? 'PRESENT' : 'MISSING',
            'all_headers' => $request->headers->all(),
            'request_size' => strlen(json_encode($request->all()))
        ]);

        try {
            DB::beginTransaction();

            $fra = Fra::findOrFail($id);
            $actionType = $request->input('action_type', 'save');

            Log::info('📋 Processing FRA', [
                'fra_id' => $fra->id,
                'fra_tahun' => $fra->tahun_berjalan,
                'action_type' => $actionType
            ]);

            $targets = $request->input('target', []);
            $assignIds = $request->input('assign_id', []);

            Log::info('📊 Input data received', [
                'targets_count' => count($targets),
                'assign_ids_count' => count($assignIds),
                'targets_sample' => array_slice($targets, 0, 3, true), // First 3 items
                'assign_ids_sample' => array_slice($assignIds, 0, 3, true) // First 3 items
            ]);

            // ✅ FIXED: Get all unique matriks IDs from both target and assign inputs
            $allSubmittedMatriksIds = array_keys($targets + $assignIds);

            Log::info('🔍 Processing matriks IDs', [
                'total_matriks_ids' => count($allSubmittedMatriksIds),
                'matriks_ids' => $allSubmittedMatriksIds
            ]);

            $processedCount = 0;
            $skippedCount = 0;

            // Process each matriks from the request based on the combined keys
            foreach ($allSubmittedMatriksIds as $matriksId) {
                Log::info('🔄 Processing matriks', ['matriks_id' => $matriksId]);

                $dataForUpdate = [];

                // Check if target data was submitted for this matriksId
                if (isset($targets[$matriksId])) {
                    $dataForUpdate['target_tw1'] = $targets[$matriksId]['tw1'] ?? null;
                    $dataForUpdate['target_tw2'] = $targets[$matriksId]['tw2'] ?? null;
                    $dataForUpdate['target_tw3'] = $targets[$matriksId]['tw3'] ?? null;
                    $dataForUpdate['target_tw4'] = $targets[$matriksId]['tw4'] ?? null;
                    
                    Log::info('📝 Target data found', [
                        'matriks_id' => $matriksId,
                        'target_data' => $targets[$matriksId]
                    ]);
                }

                // Check if assign data was submitted for this matriksId.
                // This is crucial for setting or unsetting a PIC.
                if (array_key_exists($matriksId, $assignIds)) {
                    $dataForUpdate['assign_id'] = $assignIds[$matriksId];
                    
                    Log::info('👤 Assign data found', [
                        'matriks_id' => $matriksId,
                        'assign_id' => $assignIds[$matriksId]
                    ]);
                }

                // Only proceed if there is actually data to update for this matriks
                if (!empty($dataForUpdate)) {
                    // Set parent_id to null explicitly to handle nullable field
                    $dataForUpdate['parent_id'] = null;
                    
                    Log::info('💾 Attempting to updateOrCreate Target_Fra', [
                        'matriks_fra_id' => $matriksId,
                        'data_for_update' => $dataForUpdate
                    ]);
                    
                    try {
                        $targetFra = Target_Fra::updateOrCreate(
                            ['matriks_fra_id' => $matriksId],
                            $dataForUpdate
                        );
                        
                        Log::info('✅ Target_Fra saved successfully', [
                            'target_fra_id' => $targetFra->id,
                            'matriks_fra_id' => $matriksId,
                            'was_recently_created' => $targetFra->wasRecentlyCreated
                        ]);
                        
                        $processedCount++;
                    } catch (\Exception $e) {
                        Log::error('❌ Failed to save Target_Fra', [
                            'matriks_fra_id' => $matriksId,
                            'error' => $e->getMessage(),
                            'data_for_update' => $dataForUpdate
                        ]);
                        throw $e;
                    }
                } else {
                    Log::info('⏭️ Skipping matriks (no data to update)', ['matriks_id' => $matriksId]);
                    $skippedCount++;
                }
            }
            
            Log::info('📈 Processing summary', [
                'processed_count' => $processedCount,
                'skipped_count' => $skippedCount,
                'total_submitted' => count($allSubmittedMatriksIds)
            ]);

            // If finalizing, create triwulans and update FRA status
            if ($actionType === 'finalize') {
                Log::info('🏁 Starting finalization process', ['fra_id' => $fra->id]);
                
                // ✅ FIXED: Validasi semua target harus terisi sebelum finalisasi
                $matriksIds = $fra->matriks_fra->pluck('id');
                $requiredMatriks = $fra->matriks_fra
                    ->filter(function ($matriks) {
                        // Hanya indikator utama yang perlu target (yang memiliki satuan)
                        return !empty($matriks->indikator) && !empty($matriks->satuan);
                    });
                
                $unfilledTargets = [];
                foreach ($requiredMatriks as $matriks) {
                    $target = Target_Fra::where('matriks_fra_id', $matriks->id)->first();
                    if (!$target || empty($target->target_tw4) || $target->target_tw4 == 0) {
                        $unfilledTargets[] = $matriks->indikator;
                    }
                }
                
                if (!empty($unfilledTargets)) {
                    DB::rollback();
                    $errorMessage = 'Tidak dapat melakukan finalisasi. Target berikut belum diisi: ' . implode(', ', $unfilledTargets);
                    
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage
                        ], 400);
                    }
                    
                    return redirect()->back()->with('error', $errorMessage);
                }
                
                try {
                    $this->createTriwulans($fra);
                    Log::info('📅 Triwulans created successfully', ['fra_id' => $fra->id]);
                    
                    // Mungkin ada update status FRA di sini jika diperlukan
                    // $fra->update(['status' => 'Finalized']); 

                    DB::commit();
                    Log::info('💾 Transaction committed for finalization', ['fra_id' => $fra->id]);
                    Log::info('✅ simpanTarget finalized successfully', ['fra_id' => $fra->id]);
                    
                    // Handle AJAX/JSON response for finalization
                    if ($request->ajax() || $request->wantsJson()) {
                        $response = [
                            'success' => true,
                            'message' => 'Target berhasil difinalisasi! Triwulan telah dibuat dan FRA siap untuk input realisasi.',
                            'redirect_url' => route('fra.index')
                        ];
                        
                        Log::info('📤 Sending finalization JSON response', ['response' => $response]);
                        
                        return response()->json($response);
                    }
                    
                    return redirect()->route('fra.index')->with('success', 'Target berhasil difinalisasi! Triwulan telah dibuat dan FRA siap untuk input realisasi.');
                } catch (\Exception $e) {
                    Log::error('❌ Finalization failed', [
                        'fra_id' => $fra->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }

            DB::commit();
            Log::info('💾 Transaction committed successfully', ['fra_id' => $fra->id]);

            $successMessage = $actionType === 'finalize' 
                ? 'Target berhasil difinalisasi! Triwulan telah dibuat.' 
                : 'Perubahan target berhasil disimpan.';
            
            Log::info('🎯 Preparing response', [
                'fra_id' => $fra->id,
                'action_type' => $actionType,
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson(),
                'success_message' => $successMessage
            ]);

            // Tambahkan penanganan respons untuk request AJAX
            if ($request->ajax() || $request->wantsJson()) {
                $response = [
                    'success' => true,
                    'message' => $successMessage
                ];
                
                Log::info('📤 Sending JSON response', [
                    'response' => $response,
                    'is_ajax' => $request->ajax(),
                    'wants_json' => $request->wantsJson(),
                    'content_type_will_be' => 'application/json'
                ]);
                
                $jsonResponse = response()->json($response);
                Log::info('📤 JSON Response created', [
                    'status_code' => $jsonResponse->getStatusCode(),
                    'headers' => $jsonResponse->headers->all(),
                    'content' => $jsonResponse->getContent()
                ]);
                
                return $jsonResponse;
            }

            if ($actionType === 'finalize') {
                Log::info('🔄 Redirecting to FRA index for finalization', ['fra_id' => $fra->id]);
                return redirect()->route('fra.index')->with('success', 'Target berhasil difinalisasi! Triwulan telah dibuat dan FRA siap untuk input realisasi.');
            }

            Log::info('🔄 Redirecting back with success message', ['fra_id' => $fra->id]);
            return redirect()->back()->with('success', 'Perubahan target berhasil disimpan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            Log::error('❌ Validation error in simpanTarget', [
                'fra_id' => $id,
                'error_message' => $e->getMessage(),
                'validation_errors' => $e->errors(),
                'failed_rules' => $e->validator ? $e->validator->failed() : []
            ]);

            $errorMessage = 'Validasi gagal: ' . $e->getMessage();
            
            // Tambahkan penanganan respons untuk request AJAX
            if ($request->ajax() || $request->wantsJson()) {
                $errorResponse = [
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => $e->errors()
                ];
                
                Log::info('📤 Sending validation error JSON response', ['response' => $errorResponse]);
                
                return response()->json($errorResponse, 422);
            }

            Log::info('🔄 Redirecting back with validation errors', ['fra_id' => $id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('❌ General error in simpanTarget', [
                'fra_id' => $id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'previous_exception' => $e->getPrevious() ? $e->getPrevious()->getMessage() : null
            ]);

            $errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
            
            // Tambahkan penanganan respons untuk request AJAX
            if ($request->ajax() || $request->wantsJson()) {
                $errorResponse = [
                    'success' => false,
                    'message' => $errorMessage
                ];
                
                Log::info('📤 Sending general error JSON response', ['response' => $errorResponse]);
                
                return response()->json($errorResponse, 500);
            }

            Log::info('🔄 Redirecting back with general error', ['fra_id' => $id]);
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $fra = Fra::findOrFail($id);
            $tahun = $fra->tahun_berjalan;
            $googleDriveService = new GoogleDriveOAuthService();

            Log::info("Memulai penghapusan FRA tahun {$tahun}", ['fra_id' => $fra->id]);

            // 1. Hapus semua data terkait FRA secara berurutan untuk menghindari foreign key constraint

            // 1a. Hapus Bukti Dukung FRA dan file di Google Drive
            $buktiDukungFras = Buktidukung_Fra::whereHas('realisasi_fra.matriks_fra.template_fra', function ($query) use ($fra) {
                $query->where('fra_id', $fra->id);
            })->get();

            foreach ($buktiDukungFras as $buktiDukung) {
                if ($buktiDukung->google_drive_file_id) {
                    $googleDriveService->moveToTrash($buktiDukung->google_drive_file_id);
                }
                $buktiDukung->delete();
            }
            Log::info("Bukti Dukung FRA dihapus: " . $buktiDukungFras->count() . " item");

            // 1b. Hapus Realisasi FRA
            $templateFraIds = $fra->template_fra()->pluck('id');
            $matriksFraIds = Matriks_Fra::whereIn('template_fra_id', $templateFraIds)->pluck('id');
            $realisasiFraCount = Realisasi_Fra::whereIn('matriks_fra_id', $matriksFraIds)->count();
            Realisasi_Fra::whereIn('matriks_fra_id', $matriksFraIds)->delete();
            Log::info("Realisasi FRA dihapus: {$realisasiFraCount} item");

            // 1c. Hapus Target FRA
            $targetFraCount = Target_Fra::whereIn('matriks_fra_id', $matriksFraIds)->count();
            Target_Fra::whereIn('matriks_fra_id', $matriksFraIds)->delete();
            Log::info("Target FRA dihapus: {$targetFraCount} item");

            // 1d. Hapus Target PK yang terkait dengan matriks FRA ini
            $targetPkCount = Target_Pk::whereIn('matriks_fra_id', $matriksFraIds)->count();
            Target_Pk::whereIn('matriks_fra_id', $matriksFraIds)->delete();
            Log::info("Target PK terkait dihapus: {$targetPkCount} item");

            // 1e. Hapus Matriks FRA
            $matriksFraCount = Matriks_Fra::whereIn('template_fra_id', $templateFraIds)->count();
            Matriks_Fra::whereIn('template_fra_id', $templateFraIds)->delete();
            Log::info("Matriks FRA dihapus: {$matriksFraCount} item");

            // 1f. Hapus Template FRA
            $templateFraCount = $fra->template_fra()->count();
            $fra->template_fra()->delete();
            Log::info("Template FRA dihapus: {$templateFraCount} item");

            // 1g. Hapus Triwulan terkait FRA
            $triwulanCount = Triwulan::where('fra_id', $fra->id)->count();
            Triwulan::where('fra_id', $fra->id)->delete();
            Log::info("Triwulan dihapus: {$triwulanCount} item");

            // 2. Hapus Kegiatan "Monitoring Capaian Kinerja" yang terkait
            $capaianKegiatan = Kegiatan::where('nama_kegiatan', "Monitoring Capaian Kinerja FRA {$tahun}")
                ->where('tahun_berjalan', $tahun)
                ->first();
            if ($capaianKegiatan) {
                // Hapus semua Target PK yang terkait dengan kegiatan ini
                Target_Pk::where('kegiatan_id', $capaianKegiatan->id)->delete();

                // Hapus semua bukti dukung yang terkait dengan kegiatan ini
                Bukti_Dukung::where('kegiatan_id', $capaianKegiatan->id)->delete();

                if ($capaianKegiatan->folder_id) {
                    $googleDriveService->moveToTrash($capaianKegiatan->folder_id);
                    Log::info("Folder Google Drive untuk Capaian Kinerja '{$capaianKegiatan->nama_kegiatan}' telah dipindahkan ke sampah.", ['folder_id' => $capaianKegiatan->folder_id]);
                }
                $capaianKegiatan->delete();
                Log::info("Kegiatan Capaian Kinerja terkait FRA tahun {$tahun} telah dihapus.", ['kegiatan_id' => $capaianKegiatan->id]);
            } else {
                Log::warning("Tidak ditemukan kegiatan 'Monitoring Capaian Kinerja FRA {$tahun}' untuk dihapus.");
            }

            // 3. Hapus Kegiatan "Form Rencana Aksi" yang terkait
            $fraKegiatan = Kegiatan::where('nama_kegiatan', "Form Rencana Aksi {$tahun}")
                ->where('tahun_berjalan', $tahun)
                ->first();
            if ($fraKegiatan) {
                // Hapus semua Target PK yang terkait dengan kegiatan ini
                Target_Pk::where('kegiatan_id', $fraKegiatan->id)->delete();

                // Hapus semua bukti dukung yang terkait dengan kegiatan ini
                Bukti_Dukung::where('kegiatan_id', $fraKegiatan->id)->delete();

                if ($fraKegiatan->folder_id) {
                    $googleDriveService->moveToTrash($fraKegiatan->folder_id);
                    Log::info("Folder Google Drive untuk Form Rencana Aksi '{$fraKegiatan->nama_kegiatan}' telah dipindahkan ke sampah.", ['folder_id' => $fraKegiatan->folder_id]);
                }
                $fraKegiatan->delete();
                Log::info("Kegiatan Form Rencana Aksi terkait FRA tahun {$tahun} telah dihapus.", ['kegiatan_id' => $fraKegiatan->id]);
            }

            // 4. Hapus file template dari storage
            if ($fra->file_template && Storage::exists($fra->file_template)) {
                Storage::delete($fra->file_template);
                Log::info("File template FRA dihapus dari storage: " . $fra->file_template);
            }

            // 5. Hapus folder Google Drive FRA (jika ada) - menggunakan folder_id dari kegiatan terkait
            $capaianKinerjaKegiatan = $fra->capaianKinerjaKegiatan();
            if ($capaianKinerjaKegiatan && $capaianKinerjaKegiatan->folder_id) {
                $googleDriveService->moveToTrash($capaianKinerjaKegiatan->folder_id);
                Log::info("Folder Google Drive FRA dipindahkan ke sampah: " . $capaianKinerjaKegiatan->folder_id);
            }

            // 6. Terakhir, hapus data FRA itu sendiri
            $fra->delete();
            Log::info("Data FRA tahun {$tahun} berhasil dihapus dari database");

            DB::commit();

            // Tambahkan penanganan respons untuk request AJAX
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "FRA tahun {$tahun} beserta SEMUA data terkait, kegiatan, dan folder berhasil dihapus secara menyeluruh."
                ]);
            }

            return redirect()->route('fra.index')->with('success', "FRA tahun {$tahun} beserta SEMUA data terkait, kegiatan, dan folder berhasil dihapus secara menyeluruh.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menghapus FRA: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            // Tambahkan penanganan respons untuk request AJAX
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus FRA: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus FRA: ' . $e->getMessage());
        }
    }

    /**
     * Realisasi FRA
     */
    public function formRealisasi(Fra $fra, $triwulan)
    {
        /** @var \App\Models\Pengguna|null $user */
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        $isSuperAdmin = false;
        $isAdmin = false;
        $isKetuaTim = false;

        if ($user) {
            $isSuperAdmin = $user->isSuperAdmin();
            $isAdmin = $user->isAdmin();
            $isKetuaTim = $user->isKetuaTim() || $user->isAdmin();
        }
        
        // Pastikan triwulan valid
        if (!in_array($triwulan, [1, 2, 3, 4])) {
            abort(404, "Triwulan tidak valid.");
        }

        // Ambil triwulan object untuk mendapatkan triwulan_id
        $triwulanObj = Triwulan::where('fra_id', $fra->id)->where('nomor', $triwulan)->first();

        // Ambil data yang diperlukan
        $matriksListQuery = $fra->matriks_fra()->with(['template_fra.template_jenis', 'target_fra']);

        

        // Cek apakah triwulan sudah selesai untuk menentukan mode tampilan
        // Super Admin dapat mengedit data meskipun triwulan sudah selesai atau terlambat
        $mode = request()->get('mode');
        $readOnly = false;
        if ($triwulanObj && in_array($triwulanObj->status, ['Selesai', 'Terlambat']) && !$isSuperAdmin) {
            $readOnly = true;
        }
        
        // If mode=view is specified, force read-only mode
        if ($mode === 'view') {
            $readOnly = true;
        }

        // Jika triwulan sudah selesai, tampilkan semua indikator dalam mode read-only
        // Jika belum selesai dan user adalah ketua tim, filter berdasarkan assignment
        if ($readOnly || $isSuperAdmin || $isAdmin) {
            // Admin, Super Admin, atau mode read-only: fetch all matriks
            $matriksList = $matriksListQuery->get();
        } elseif ($isKetuaTim && $userId && !$readOnly) {
            // Ketua tim dalam mode input: filter berdasarkan assignment
            // Step 1: Get matriks_fra_id's assigned to this Ketua Tim (from Realisasi_Fra)
            $assignedMatriksIdsFromRealisasi = Realisasi_Fra::where('triwulan_id', $triwulanObj->id)
                ->where('pic_tindak_lanjut_id', $userId)
                ->pluck('matriks_fra_id')
                ->toArray();

            // Step 2: Get matriks_fra_id's assigned to this Ketua Tim (from Target_Fra)
            $assignedMatriksIdsFromTarget = Target_Fra::where('assign_id', $userId)
                ->pluck('matriks_fra_id')
                ->toArray();

            // Combine all assigned matriks IDs
            $allAssignedMatriksIds = array_unique(array_merge($assignedMatriksIdsFromRealisasi, $assignedMatriksIdsFromTarget));

            // If no assignments, return empty list
            if (empty($allAssignedMatriksIds)) {
                $matriksList = collect(); // Empty collection if no assignments
            } else {
                // Fetch all matriks_fra for the current FRA
                $allFraMatriks = $matriksListQuery->get();

                $filteredMatriksIds = [];

                foreach ($allAssignedMatriksIds as $assignedMatriksId) {
                    $assignedMatriks = $allFraMatriks->find($assignedMatriksId);

                    if ($assignedMatriks) {
                        // Add the assigned matriks itself
                        $filteredMatriksIds[] = $assignedMatriks->id;

                        // Traverse up the hierarchy and add all parent IDs
                        // Tujuan
                        $parentTujuan = $allFraMatriks->first(function ($m) use ($assignedMatriks) {
                            return $m->tujuan === $assignedMatriks->tujuan && is_null($m->sasaran) && is_null($m->indikator);
                        });
                        if ($parentTujuan) $filteredMatriksIds[] = $parentTujuan->id;

                        // Sasaran
                        $parentSasaran = $allFraMatriks->first(function ($m) use ($assignedMatriks) {
                            return $m->tujuan === $assignedMatriks->tujuan && $m->sasaran === $assignedMatriks->sasaran && is_null($m->indikator);
                        });
                        if ($parentSasaran) $filteredMatriksIds[] = $parentSasaran->id;

                        // Indikator
                        $parentIndikator = $allFraMatriks->first(function ($m) use ($assignedMatriks) {
                            return $m->tujuan === $assignedMatriks->tujuan && $m->sasaran === $assignedMatriks->sasaran && $m->indikator === $assignedMatriks->indikator && is_null($m->sub_indikator);
                        });
                        if ($parentIndikator) $filteredMatriksIds[] = $parentIndikator->id;

                        // Sub-indikator (if applicable)
                        if (!is_null($assignedMatriks->sub_indikator)) {
                            $parentSubIndikator = $allFraMatriks->first(function ($m) use ($assignedMatriks) {
                                return $m->tujuan === $assignedMatriks->tujuan && $m->sasaran === $assignedMatriks->sasaran && $m->indikator === $assignedMatriks->indikator && $m->sub_indikator === $assignedMatriks->sub_indikator && is_null($m->detail_sub);
                            });
                            if ($parentSubIndikator) $filteredMatriksIds[] = $parentSubIndikator->id;
                        }
                    }
                }
                // This is where the expansion logic for detail_indikator and detail_sub siblings should go.
                // Let's create a new collection for expanded IDs.
                $expandedFilteredMatriksIds = collect(array_unique($filteredMatriksIds));

                // Now, expand to include all siblings for detail_indikator and detail_sub if any of their kind are present
                $tempAdditionalIds = [];
                foreach ($expandedFilteredMatriksIds as $matriksId) {
                    $matriks = $allFraMatriks->find($matriksId);
                    if (!$matriks) continue;

                    // Case: Expand detail_indikator siblings
                    if (!is_null($matriks->detail_indikator)) {
                        // Find all detail_indikator siblings under the same parent indicator
                        $siblings = $allFraMatriks->where('tujuan', $matriks->tujuan)
                                                  ->where('sasaran', $matriks->sasaran)
                                                  ->where('indikator', $matriks->indikator)
                                                  ->whereNotNull('detail_indikator');
                        foreach ($siblings as $sibling) {
                            $tempAdditionalIds[] = $sibling->id;
                        }
                    }
                    
                    // Case: Expand detail_sub siblings
                    if (!is_null($matriks->detail_sub)) {
                        // Find all detail_sub siblings under the same parent sub_indikator
                        $siblings = $allFraMatriks->where('tujuan', $matriks->tujuan)
                                                  ->where('sasaran', $matriks->sasaran)
                                                  ->where('indikator', $matriks->indikator)
                                                  ->where('sub_indikator', $matriks->sub_indikator)
                                                  ->whereNotNull('detail_sub');
                        foreach ($siblings as $sibling) {
                            $tempAdditionalIds[] = $sibling->id;
                        }
                    }
                }
                
                // Merge and unique
                $finalFilteredMatriksIds = array_unique(array_merge($expandedFilteredMatriksIds->toArray(), $tempAdditionalIds));

                // Ensure unique IDs and then fetch the filtered matriksList
                $matriksList = $matriksListQuery->whereIn('matriks_fra.id', $finalFilteredMatriksIds)
                    ->orderBy('tujuan')
                    ->orderBy('sasaran')
                    ->orderBy('indikator')
                    ->orderBy('sub_indikator')
                    ->orderBy('detail_sub')
                    ->get();
            }
        } else {
            // Fallback: fetch all matriks
            $matriksList = $matriksListQuery->get();
        }

        // Ambil data realisasi yang sudah ada untuk triwulan ini
        $realisasiData = collect();
        if ($triwulanObj) {
            $realisasiData = Realisasi_Fra::with('buktidukung_fra')->whereHas('matriks_fra', function ($query) use ($fra) {
                $query->whereIn('template_fra_id', $fra->template_fra()->pluck('id'));
            })->where('triwulan_id', $triwulanObj->id)->get()->keyBy('matriks_fra_id');
        }

        // Ambil data target untuk triwulan ini (target_fra tidak memiliki kolom triwulan, jadi ambil semua)
        $targetData = $fra->target_fra()->get()->keyBy('matriks_fra_id');

        // Ambil daftar pengguna untuk dropdown PIC
        $penggunas = Pengguna::orderBy('name')->get();

        // Ambil data tambahan untuk view
        $hasSuplemenData = $matriksList->contains(function ($m) {
            return $m->template_fra->template_jenis->nama === 'PK Suplemen';
        });

        $hasUmumData = $matriksList->contains(function ($m) {
            return $m->template_fra->template_jenis->nama === 'PK Umum';
        });

        // Ambil data target PK untuk perhitungan
        $targetPkData = collect();
        if ($triwulanObj) {
            // Target PK tidak terkait dengan triwulan, ambil berdasarkan matriks_fra yang ada di FRA ini
            $matriksIds = $matriksList->pluck('id');
            $targetPkData = \App\Models\Target_Pk::whereIn('matriks_fra_id', $matriksIds)
                ->get()
                ->keyBy('matriks_fra_id');
        }

        // Ambil data realisasi yang sudah ada
        $existingRealisasi = $realisasiData;

        return view('pengukuran kinerja.form_realisasi_fra', compact(
            'fra',
            'triwulan',
            'triwulanObj',
            'matriksList',
            'realisasiData',
            'targetData',
            'penggunas',
            'readOnly',
            'hasSuplemenData',
            'hasUmumData',
            'targetPkData',
            'existingRealisasi'
        ));
    }

    public function store(Request $request)
    {
        // 🔥 DEBUG: Log semua data yang masuk
        Log::info('=== FRA STORE REQUEST RECEIVED ===', [
            'method' => $request->method(),
            'url' => $request->url(),
            'all_data' => $request->all(),
            'files' => $request->allFiles(),
            'headers' => $request->headers->all()
        ]);

        $request->validate([
            'nama_fra' => 'required',
            'tahun_berjalan' => 'required|numeric|digits:4',
            'template_file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            $existingFra = Fra::where('tahun_berjalan', $request->tahun_berjalan)->first();
            if ($existingFra) {
                // Tambahkan penanganan respons untuk request AJAX
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Form Rencana Aksi untuk tahun {$request->tahun_berjalan} sudah ada!"
                    ], 422);
                }
                return redirect()->back()->with('error', "Form Rencana Aksi untuk tahun {$request->tahun_berjalan} sudah ada!");
            }

            DB::beginTransaction();

            // 1. Dapatkan data penting
            $googleDriveService = new GoogleDriveOAuthService();
            $tahun = $request->tahun_berjalan;
            $activeRenstra = Renstra::whereYear('periode_awal', '<=', $tahun)
                ->whereYear('periode_akhir', '>=', $tahun)
                ->first() ?? Renstra::orderBy('periode_akhir', 'desc')->first();

            if (!$activeRenstra) {
                throw new Exception("Tidak ada data Renstra aktif yang bisa dikaitkan.");
            }

            // 2. Siapkan data untuk Kegiatan
            $komponenPelaporan = Komponen::firstOrCreate(['id' => 3], ['komponen' => 'Pelaporan Kinerja']);
            $subKomponenLakin = Sub_Komponen::firstOrCreate(
                ['sub_komponen' => 'Manajemen Lakin', 'komponen_id' => $komponenPelaporan->id]
            );
            $subKomponenFra = Sub_Komponen::firstOrCreate(
                ['sub_komponen' => 'Form Rencana Aksi', 'komponen_id' => 2] // Asumsi kompenen_id 2 = Pengukuran Kinerja
            );

            $namaCapaianKinerja = "Monitoring Capaian Kinerja FRA {$tahun}";
            $namaFormRencanaAksi = "Form Rencana Aksi {$tahun}";

            // 3. Buat Folder & Kegiatan "Capaian Kinerja" dengan folder triwulan
            $folderCapaianResult = $googleDriveService->createCapaianKinerjaFolder($namaCapaianKinerja, (int)$tahun);
            Kegiatan::create([
                'nama_kegiatan' => $namaCapaianKinerja,
                'tahun_berjalan' => $tahun,
                'tanggal_mulai' => "{$tahun}-01-01",
                'tanggal_berakhir' => "{$tahun}-12-31",
                'sub_komponen_id' => $subKomponenLakin->id,
                'renstra_id' => $activeRenstra->id,
                'folder_id' => $folderCapaianResult['folder_id'] ?? null,
            ]);

            // 4. Buat Folder & Kegiatan "Form Rencana Aksi" dengan folder triwulan
            $folderFraResult = $googleDriveService->createFormRencanaAksiFolder((int)$tahun);
            Kegiatan::create([
                'nama_kegiatan' => $namaFormRencanaAksi,
                'tahun_berjalan' => $tahun,
                'tanggal_mulai' => "{$tahun}-01-01",
                'tanggal_berakhir' => "{$tahun}-12-31",
                'sub_komponen_id' => $subKomponenFra->id,
                'renstra_id' => $activeRenstra->id,
                'folder_id' => $folderFraResult,
            ]);

            // Lanjutkan proses penyimpanan file
            $file = $request->file('template_file');
            $fileName = 'fra_template_' . $request->tahun_berjalan . '_' . time() . '.' . $file->getClientOriginalExtension();
            $storagePath = storage_path('app/public/fra');
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $file->move($storagePath, $fileName);
            $filePath = 'public/fra/' . $fileName;

            $fra = Fra::create([
                'nama_fra' => $request->nama_fra,
                'tahun_berjalan' => $request->tahun_berjalan,
                'file_template' => $filePath,
            ]);

            // Buat triwulan
            $this->createTriwulans($fra);

            $fullPath = storage_path('app/' . $filePath);

            // 🔍 SMART DETECTION: Cek apakah ini file format KabKota
            $isKabKotaFormat = $this->isKabKotaFormat($fileName, $fullPath);

            if ($isKabKotaFormat) {
                // 🎯 PRIORITAS: KabKota Format - Gunakan KabKotaFraParser
                Log::info('File format KabKota terdeteksi, menggunakan KabKotaFraParser');
                try {
                    $kabKotaParser = new KabKotaFraParser($fra);
                    $kabKotaResult = $kabKotaParser->parseExcel($fullPath);

                    if ($kabKotaResult['success'] && $kabKotaResult['items_count'] > 0) {
                        $successMessage = "✅ FRA berhasil dibuat dengan KabKota parser! ({$kabKotaResult['items_count']} item berhasil diparse)";
                    } else {
                        throw new \Exception('KabKota parser tidak menemukan data yang valid dalam file Excel');
                    }
                } catch (\Exception $kabKotaError) {
                    Log::warning('KabKota parser failed, fallback to enhanced parser: ' . $kabKotaError->getMessage());
                    // Fallback ke Enhanced Parser jika KabKota parser gagal
                    $isKabKotaFormat = false; // Reset flag untuk lanjut ke parser berikutnya
                }
            }

            if (!$isKabKotaFormat) {
                // 🚀 ENHANCED: Add Google Drive integration (optional)
                try {
                    $this->getGoogleDriveService()->processFraWorkflow($fra->id, $fullPath);

                    // Smart Parsing dengan Enhanced Parser
                    $parser = new EnhancedFraParser($fra);
                    $parseResult = $parser->parseWithSmartDetection($fullPath);

                    $successMessage = '✅ FRA berhasil dibuat dan tersync dengan Google Drive!';
                } catch (\Exception $e) {
                    // Fallback ke parsing biasa jika Google Drive gagal
                    Log::warning('Google Drive integration failed, fallback to regular parsing: ' . $e->getMessage());

                    try {
                        $this->processExcel($fra, $storagePath . DIRECTORY_SEPARATOR . $fileName);
                        $successMessage = 'FRA berhasil dibuat dengan parser standar';
                    } catch (\Exception $parseError) {
                        // 🆘 ULTIMATE FALLBACK: Use Unified Parser
                        Log::warning('Standard parser failed, using unified parser: ' . $parseError->getMessage());

                        try {
                            $unifiedParser = new \App\Services\UnifiedFraParser($fra);
                            $unifiedResult = $unifiedParser->parseExcel($storagePath . DIRECTORY_SEPARATOR . $fileName);

                            if ($unifiedResult['success'] && $unifiedResult['items_count'] > 0) {
                                $successMessage = "✅ FRA berhasil dibuat dengan unified parser! ({$unifiedResult['items_count']} item berhasil diparse)";
                            } else {
                                throw new \Exception('Unified parser tidak menemukan data yang valid dalam file Excel');
                            }
                        } catch (\Exception $unifiedError) {
                            Log::error('All parsers failed: ' . $unifiedError->getMessage());
                            throw new \Exception(
                                'Tidak dapat memproses file Excel. ' .
                                    'Pastikan file menggunakan format template FRA yang benar. ' .
                                    'Error: ' . $unifiedError->getMessage()
                            );
                        }
                    }
                }
            }

            DB::commit();

            // Tambahkan penanganan respons untuk request AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'FRA, folder Capaian Kinerja, dan folder Form Rencana Aksi berhasil dibuat.',
                    'data' => [
                        'fra_id' => $fra->id,
                        'tahun_berjalan' => $fra->tahun_berjalan
                    ]
                ]);
            }

            return redirect()->route('fra.index')->with('success', 'FRA, folder Capaian Kinerja, dan folder Form Rencana Aksi berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('FRA Store Error: ' . $e->getMessage());

            // Tambahkan penanganan respons untuk request AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function processExcel(Fra $fra, string $filePath)
    {
        try {
            $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);

            if (!file_exists($filePath)) {
                throw new \Exception("File tidak ditemukan di: {$filePath}");
            }

            $spreadsheet = IOFactory::load($filePath);

            // Cek apakah sheet PK IKU ada
            $pkIkuSheet = null;
            $pkSuplemenSheet = null;
            $ikuSuplemenSheet = null;

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                if (strcasecmp(trim($sheetName), 'PK IKU') === 0) {
                    $pkIkuSheet = $spreadsheet->getSheetByName($sheetName);
                } else if (strcasecmp(trim($sheetName), 'PK Suplemen') === 0) {
                    $pkSuplemenSheet = $spreadsheet->getSheetByName($sheetName);
                } else if (strcasecmp(trim($sheetName), 'IKU Suplemen') === 0) {
                    $ikuSuplemenSheet = $spreadsheet->getSheetByName($sheetName);
                }
            }

            if (!$pkIkuSheet) {
                throw new \Exception("Sheet 'PK IKU' tidak ditemukan pada template. PK IKU adalah sheet wajib.");
            }

            // Deteksi format file berdasarkan struktur dan nama sheet
            $sheetNames = $spreadsheet->getSheetNames();
            $isFormRencanaAksi = in_array('Panduan Pengisian', $sheetNames) ||
                in_array('Penjelasan Indikator', $sheetNames) ||
                (in_array('PK IKU', $sheetNames) && in_array('IKU Suplemen', $sheetNames));

            // Buat template FRA default untuk PK IKU
            $templateJenisIku = Template_Jenis::firstOrCreate(
                ['nama' => 'PK IKU'],
                ['wajib' => true]
            );

            $templateFraIku = Template_Fra::create([
                'fra_id' => $fra->id,
                'template_jenis_id' => $templateJenisIku->id
            ]);

            // Proses sheet PK IKU dengan method yang sesuai
            if ($isFormRencanaAksi) {
                Log::info('Menggunakan enhanced parser untuk format Form Rencana Aksi');
                $this->processEnhancedSheet($fra, $pkIkuSheet, $templateFraIku);
            } else {
                Log::info('Menggunakan parser standar untuk template biasa');
                $this->processSheet($fra, $pkIkuSheet, $templateFraIku);
            }
            Log::info('Proses Excel sheet PK IKU selesai untuk FRA ID: ' . $fra->id);

            // Jika ada sheet PK Suplemen atau IKU Suplemen, proses juga
            $suplemenSheet = $pkSuplemenSheet ?: $ikuSuplemenSheet;
            if ($suplemenSheet) {
                // Standardisasi semua suplemen menjadi 'PK Suplemen' untuk konsistensi
                $templateJenisSuplemen = Template_Jenis::firstOrCreate(
                    ['nama' => 'PK Suplemen'],
                    ['wajib' => false]
                );

                $templateFraSuplemen = Template_Fra::create([
                    'fra_id' => $fra->id,
                    'template_jenis_id' => $templateJenisSuplemen->id
                ]);

                if ($isFormRencanaAksi) {
                    $this->processEnhancedSheet($fra, $suplemenSheet, $templateFraSuplemen);
                } else {
                    $this->processSheet($fra, $suplemenSheet, $templateFraSuplemen);
                }
                Log::info('Proses Excel sheet Suplemen selesai untuk FRA ID: ' . $fra->id);
            } else {
                Log::info('Sheet Suplemen tidak ditemukan, hanya PK IKU yang diproses');
            }
        } catch (\Exception $e) {
            Log::error('Process Excel Error: ' . $e->getMessage());
            Log::error('Process Excel Trace: ' . $e->getTraceAsString());

            // User-friendly error messages
            $userMessage = $e->getMessage();
            if (strpos($e->getMessage(), 'Sheet') !== false && strpos($e->getMessage(), 'tidak ditemukan') !== false) {
                $userMessage = 'Format Excel tidak valid: ' . $e->getMessage() . ' Pastikan file menggunakan template FRA yang benar.';
            } elseif (strpos($e->getMessage(), 'Header') !== false) {
                $userMessage = 'Format Excel tidak sesuai template: ' . $e->getMessage() . ' Gunakan template FRA resmi.';
            }

            throw new \Exception($userMessage);
        }
    }

    /**
     * Deteksi apakah file Excel adalah format KabKota
     */
    private function isKabKotaFormat($fileName, $filePath)
    {
        // Deteksi berdasarkan nama file
        if (stripos($fileName, 'kabkota') !== false || stripos($fileName, 'kab kota') !== false) {
            return true;
        }

        // Deteksi berdasarkan isi file - cek struktur hierarki kode
        try {
            $spreadsheet = IOFactory::load($filePath);

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $worksheet = $spreadsheet->getSheetByName($sheetName);
                $maxRow = min($worksheet->getHighestDataRow(), 50); // Cek 50 baris pertama saja

                for ($row = 1; $row <= $maxRow; $row++) {
                    $cellValues = [];
                    for ($col = 'A'; $col <= 'H'; $col++) {
                        $cellValues[] = trim($worksheet->getCell($col . $row)->getCalculatedValue() ?? '');
                    }

                    $text = implode(' ', array_filter($cellValues));

                    // Cek pattern kode hierarki KabKota
                    if (
                        preg_match('/^\d+\.\d+\.\d+\.\d+(\s|$)/', $text) || // 4-digit code
                        preg_match('/^\d+\.\d+\.\d+\.\d+\.\d+(\s|$)/', $text)
                    ) { // 5-digit code
                        Log::info("KabKota format detected: {$text}");
                        return true;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("Error detecting KabKota format: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Fungsi untuk mendapatkan nilai dari merged cell
     */
    private function getMergedCellValue($sheet, $cellAddress)
    {
        $cell = $sheet->getCell($cellAddress);
        $value = $cell->getValue();

        if ($value !== null && $value !== '') {
            return trim($value);
        }

        // Cek apakah cell ini bagian dari merged range
        $mergedCells = $sheet->getMergeCells();
        foreach ($mergedCells as $range) {
            if ($cell->isInRange($range)) {
                // Ambil nilai dari cell pertama di range
                $startCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::splitRange($range)[0][0];
                $startValue = $sheet->getCell($startCell)->getValue();
                return trim($startValue ?? '');
            }
        }

        return '';
    }

    /**
     * Parse Excel dengan format Form Rencana Aksi (dengan merged cells) - UNIFIED VERSION
     */
    private function processEnhancedSheet(Fra $fra, $worksheet, $templateFra)
    {
        $currentTujuan = null;
        $currentSasaran = null;
        $currentIndikator = null;
        $currentSubIndikator = null;
        $rowsProcessed = 0;
        $inUmumSection = false;
        $umumTemplateFra = null;

        $maxRow = $worksheet->getHighestDataRow();

        for ($row = 1; $row <= $maxRow; $row++) {
            $colA = $this->getMergedCellValue($worksheet, "A$row");
            $colB = $this->getMergedCellValue($worksheet, "B$row");
            $colC = $this->getMergedCellValue($worksheet, "C$row");
            $colD = $this->getMergedCellValue($worksheet, "D$row");
            $colE = $this->getMergedCellValue($worksheet, "E$row");
            $colF = $this->getMergedCellValue($worksheet, "F$row");
            $colG = $this->getMergedCellValue($worksheet, "G$row");
            $colH = $this->getMergedCellValue($worksheet, "H$row");
            $colI = $this->getMergedCellValue($worksheet, "I$row"); // SATUAN COLUMN

            // UNIFIED: Gabungkan deskripsi dari merged cells dengan deduplication
            $deskripsiLengkap = $this->cleanAndMergeDescription($colD, $colE, $colF, $colG, $colH);

            // FIXED: Gunakan satuan dari kolom I, bukan extract dari teks
            $satuanValue = $this->validateSatuan($colI);

            // Skip baris kosong
            if (empty($colB) && empty($colC) && empty($deskripsiLengkap)) {
                continue;
            }

            // Skip baris instruksi
            if ($this->isInstructionRow($deskripsiLengkap)) {
                continue;
            }

            // Deteksi Bagian Umum
            if ($this->isUmumSection($colB, $colC, $deskripsiLengkap)) {
                $inUmumSection = true;
                $currentTujuan = null; // Reset tujuan untuk bagian umum
                $currentSasaran = null; // Reset sasaran untuk bagian umum
                $currentSubIndikator = null;
                $currentSubKode = null;
                $currentSubIndikatorId = null;

                // Buat template jenis "Umum" jika belum ada
                $umumTemplateJenis = Template_Jenis::firstOrCreate(
                    ['nama' => 'Umum'],
                    ['wajib' => false]
                );

                // Buat template fra untuk bagian umum jika belum ada
                if (!$umumTemplateFra) {
                    $umumTemplateFra = Template_Fra::firstOrCreate([
                        'fra_id' => $fra->id,
                        'template_jenis_id' => $umumTemplateJenis->id
                    ]);
                }

                Log::info("Memasuki bagian Umum di row: {$row}");
                continue;
            }

            // UNIFIED: Jika dalam bagian umum, gunakan template umum tapi logic parsing sama
            if ($inUmumSection) {
                // Set current context untuk umum
                if (empty($currentTujuan)) {
                    $currentTujuan = 'Umum';
                }

                // Gunakan template umum
                $activeTemplateId = $umumTemplateFra ? $umumTemplateFra->id : $templateFra->id;

                // UNIFIED: Parsing umum menggunakan logic yang sama dengan IKU/suplemen
                // Hanya bedanya: tujuan = 'Umum', sasaran = null, tidak ada detail sub

                // Skip jika ini baris instruksi
                if ($this->isInstructionRow($deskripsiLengkap)) {
                    continue;
                }

                // Deteksi Indikator Umum (kolom C tidak kosong, deskripsi tidak kosong)
                if (!empty($colC) && !empty($deskripsiLengkap)) {
                    $currentIndikator = $colC; // Indikator umum sederhana
                    $currentSubIndikator = null;

                    // Simpan indikator umum
                    Matriks_Fra::create([
                        'template_fra_id' => $activeTemplateId,
                        'tujuan' => 'Umum',
                        'sasaran' => null,
                        'indikator' => $currentIndikator,
                        'sub_indikator' => null,
                        'detail_sub' => null,
                        'satuan' => $satuanValue,
                        'excel_row' => $row
                    ]);

                    $rowsProcessed++;
                    Log::info("Indikator Umum: {$currentIndikator}");
                    continue;
                }

                // Deteksi Sub Indikator Umum (kolom C kosong, ada deskripsi, ada indikator current)
                if (empty($colC) && !empty($deskripsiLengkap) && !empty($currentIndikator)) {

                    // Simpan sub indikator umum (tanpa detail sub)
                    Matriks_Fra::create([
                        'template_fra_id' => $activeTemplateId,
                        'tujuan' => 'Umum',
                        'sasaran' => null,
                        'indikator' => $currentIndikator,
                        'sub_indikator' => $deskripsiLengkap,
                        'detail_sub' => null, // Umum tidak ada detail sub
                        'satuan' => $satuanValue,
                        'excel_row' => $row
                    ]);

                    $rowsProcessed++;
                    Log::info("Sub Indikator Umum: {$currentIndikator} -> {$deskripsiLengkap}");
                    continue;
                }

                // Skip row lainnya di bagian umum
                continue;
            }

            // Deteksi Tujuan dengan berbagai format (T1:, Tujuan 1, dll)
            if ((preg_match('/^T\d+[:.]?\s*/i', $colA) || preg_match('/^T\d+[:.]?\s*/i', $colB) ||
                    (stripos($colB, 'tujuan') !== false && preg_match('/\d+/', $colB)) ||
                    (stripos($colA, 'tujuan') !== false && preg_match('/\d+/', $colA))) &&
                (!empty($colD) || !empty($deskripsiLengkap))
            ) {

                $inUmumSection = false; // Keluar dari bagian umum

                // Extract tujuan text dari berbagai format
                if (preg_match('/^T\d+[:.]?\s*(.+)/i', $colA, $matches)) {
                    $currentTujuan = trim($matches[1]);
                } elseif (preg_match('/^T\d+[:.]?\s*(.+)/i', $colB, $matches)) {
                    $currentTujuan = trim($matches[1]);
                } elseif (!empty($colD)) {
                    $tujuanNumber = preg_replace('/[^0-9]/', '', $colB ?: $colA);
                    $currentTujuan = "Tujuan {$tujuanNumber}. {$colD}";
                } else {
                    $currentTujuan = $deskripsiLengkap;
                }

                $currentSasaran = null;
                $currentIndikator = null;
                $currentSubIndikator = null;
                Log::info("Tujuan baru: {$currentTujuan}");
                continue;
            }

            // Deteksi Sasaran dengan berbagai format (S1:, 1.1, dll)
            if ((preg_match('/^S\d+[:.]?\s*/i', $colA) || preg_match('/^S\d+[:.]?\s*/i', $colB) ||
                    preg_match('/^\d+\.\d+$/', $colA) || preg_match('/^\d+\.\d+$/', $colB) ||
                    (stripos($colA, 'sasaran') !== false && preg_match('/\d+/', $colA)) ||
                    (stripos($colB, 'sasaran') !== false && preg_match('/\d+/', $colB))) &&
                (!empty($colC) || !empty($deskripsiLengkap))
            ) {

                // Extract sasaran text dari berbagai format
                if (preg_match('/^S\d+[:.]?\s*(.+)/i', $colA, $matches)) {
                    $currentSasaran = trim($matches[1]);
                } elseif (preg_match('/^S\d+[:.]?\s*(.+)/i', $colB, $matches)) {
                    $currentSasaran = trim($matches[1]);
                } elseif (preg_match('/^\d+\.\d+/', $colA) && !empty($colB)) {
                    $currentSasaran = $colA . ' ' . $colB;
                } elseif (preg_match('/^\d+\.\d+/', $colB) && !empty($colC)) {
                    $currentSasaran = $colB . ' ' . $colC;
                } else {
                    $currentSasaran = $deskripsiLengkap;
                }

                $currentIndikator = null;
                $currentSubIndikator = null;
                Log::info("Sasaran baru: {$currentSasaran}");
                continue;
            }

            // Deteksi Indikator dengan berbagai format (I1:, 1.1.1, dll)
            if ((preg_match('/^I\d+[:.]?\s*/i', $colA) || preg_match('/^I\d+[:.]?\s*/i', $colB) ||
                    preg_match('/^\d+\.\d+\.\d+$/', $colA) || preg_match('/^\d+\.\d+\.\d+$/', $colB) || preg_match('/^\d+\.\d+\.\d+$/', $colC) ||
                    (stripos($colA, 'indikator') !== false && preg_match('/\d+/', $colA)) ||
                    (stripos($colB, 'indikator') !== false && preg_match('/\d+/', $colB))) &&
                (!empty($colD) || !empty($deskripsiLengkap))
            ) {

                // Extract indikator text dari berbagai format
                if (preg_match('/^I\d+[:.]?\s*(.+)/i', $colA, $matches)) {
                    $currentIndikator = trim($matches[1]);
                } elseif (preg_match('/^I\d+[:.]?\s*(.+)/i', $colB, $matches)) {
                    $currentIndikator = trim($matches[1]);
                } elseif (preg_match('/^\d+\.\d+\.\d+/', $colA) && !empty($colB)) {
                    $currentIndikator = $colA . ' ' . $colB;
                } elseif (preg_match('/^\d+\.\d+\.\d+/', $colB) && !empty($colC)) {
                    $currentIndikator = $colB . ' ' . $colC;
                } elseif (preg_match('/^\d+\.\d+\.\d+/', $colC) && !empty($colD)) {
                    $currentIndikator = $colC . ' ' . $colD;
                } else {
                    $currentIndikator = $deskripsiLengkap;
                }

                $currentSubIndikator = null;

                // Simpan indikator utama
                Matriks_Fra::create([
                    'template_fra_id' => $templateFra->id,
                    'tujuan' => $currentTujuan,
                    'sasaran' => $currentSasaran,
                    'indikator' => $currentIndikator,
                    'sub_indikator' => null,
                    'detail_sub' => null,
                    'satuan' => $satuanValue,
                    'excel_row' => $row
                ]);

                $rowsProcessed++;
                Log::info("Indikator disimpan: {$currentIndikator}");
                continue;
            }

            // SIMPLE: Deteksi Sub Indikator atau Detail Sub
            if (!empty($deskripsiLengkap) && empty($colC) && $currentIndikator) {

                // Cek apakah ini adalah sub indikator dengan kode
                $subKode = '';
                $isSubIndikator = false;

                if (!empty($colD) && strlen(trim($colD)) <= 2) {
                    $possibleCode = strtolower(trim($colD));
                    if (in_array($possibleCode, ['x', 'y', 'z', 'a', 'b', 'c'])) {
                        $subKode = $possibleCode;
                        $isSubIndikator = true;
                    }
                }

                // Cek apakah ini adalah detail sub (row setelah sub indikator x/y)
                $isDetailSub = false;
                if (
                    !$isSubIndikator && !empty($currentSubIndikator) &&
                    preg_match('/^[xy]\./', $currentSubIndikator)
                ) {
                    $isDetailSub = true;
                }

                if ($isSubIndikator) {
                    // Simpan sebagai sub indikator
                    $subIndikatorText = "{$subKode}. {$deskripsiLengkap}";

                    // Prevent duplicate codes
                    if (str_starts_with($deskripsiLengkap, $subKode . '. ' . $subKode)) {
                        $deskripsiLengkap = substr($deskripsiLengkap, strlen($subKode . '. '));
                        $subIndikatorText = "{$subKode}. {$deskripsiLengkap}";
                    }

                    Matriks_Fra::create([
                        'template_fra_id' => $templateFra->id,
                        'tujuan' => $currentTujuan,
                        'sasaran' => $currentSasaran,
                        'indikator' => $currentIndikator,
                        'sub_indikator' => $subIndikatorText,
                        'detail_sub' => null,
                        'satuan' => $satuanValue,
                        'excel_row' => $row
                    ]);

                    $currentSubIndikator = $subIndikatorText;
                    $rowsProcessed++;
                    Log::info("Sub indikator: {$subIndikatorText}");
                } elseif ($isDetailSub) {
                    // Simpan sebagai detail sub
                    Matriks_Fra::create([
                        'template_fra_id' => $templateFra->id,
                        'tujuan' => $currentTujuan,
                        'sasaran' => $currentSasaran,
                        'indikator' => $currentIndikator,
                        'sub_indikator' => $currentSubIndikator,
                        'detail_sub' => $deskripsiLengkap,
                        'satuan' => $satuanValue,
                        'excel_row' => $row
                    ]);

                    $rowsProcessed++;
                    Log::info("Detail sub: {$deskripsiLengkap}");
                } else {
                    // Default: simpan sebagai sub indikator biasa
                    Matriks_Fra::create([
                        'template_fra_id' => $templateFra->id,
                        'tujuan' => $currentTujuan,
                        'sasaran' => $currentSasaran,
                        'indikator' => $currentIndikator,
                        'sub_indikator' => $deskripsiLengkap,
                        'detail_sub' => null,
                        'satuan' => $satuanValue,
                        'excel_row' => $row
                    ]);

                    $currentSubIndikator = $deskripsiLengkap;
                    $rowsProcessed++;
                    Log::info("Sub indikator biasa: {$deskripsiLengkap}");
                }

                continue;
            }
        }

        if ($rowsProcessed == 0) {
            throw new \Exception("Tidak ada data yang berhasil diproses dari sheet enhanced");
        }

        Log::info("Berhasil memproses {$rowsProcessed} data dari enhanced sheet");
    }

    /**
     * Validasi dan potong satuan agar sesuai dengan batasan database
     */
    private function validateSatuan($satuan)
    {
        if (empty($satuan)) {
            return '';
        }

        $satuan = trim($satuan);

        // Potong jika terlalu panjang (maksimal 45 karakter untuk safety)
        if (strlen($satuan) > 45) {
            $satuan = substr($satuan, 0, 45);
        }

        return $satuan;
    }

    /**
     * Deteksi apakah baris adalah instruksi/petunjuk yang harus dilewati
     */
    private function isInstructionRow($text)
    {
        $text = strtolower($text);
        $keywords = [
            'indikator dapat dihapus',
            'silahkan sesuaikan',
            'petunjuk',
            'panduan',
            'keterangan',
            'contoh',
            'hapus jika tidak'
        ];

        foreach ($keywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Deteksi apakah ini bagian umum
     */
    private function isUmumSection($colB, $colC, $deskripsi)
    {
        $checkTexts = [
            strtolower($colB ?? ''),
            strtolower($colC ?? ''),
            strtolower($deskripsi ?? '')
        ];

        foreach ($checkTexts as $text) {
            if (
                stripos($text, 'bagian umum') !== false ||
                stripos($text, 'indikator umum') !== false ||
                ($text === 'umum' && strlen($text) <= 10)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gabungkan deskripsi dari merged cells dengan menghindari duplikasi
     */
    private function cleanAndMergeDescription($colD, $colE, $colF, $colG, $colH)
    {
        $parts = [$colD, $colE, $colF, $colG, $colH];
        $cleanParts = [];
        $seenTexts = [];

        foreach ($parts as $part) {
            $trimmed = trim($part);
            if (empty($trimmed)) {
                continue;
            }

            // Cek duplikasi - jika text sudah pernah ada, skip
            $normalized = strtolower($trimmed);
            if (!in_array($normalized, $seenTexts)) {
                $cleanParts[] = $trimmed;
                $seenTexts[] = $normalized;
            }
        }

        $result = implode(' ', $cleanParts);

        // Validasi panjang untuk sub_indikator (maksimal 200 karakter untuk safety)
        if (strlen($result) > 200) {
            $result = substr($result, 0, 200) . '...';
        }

        return $result;
    }

    private function processSheet(Fra $fra, $worksheet, $templateFra)
    {
        $currentTujuan = null;
        $currentSasaran = null;
        $currentIndikator = null;
        $currentSubIndikator = null;
        $rowsProcessed = 0;

        $maxRow = $worksheet->getHighestDataRow();

        // Mulai dari baris 2 (skip header)
        for ($row = 2; $row <= $maxRow; $row++) {
            $tipe = trim($worksheet->getCell("A{$row}")->getValue() ?? '');
            $kode = trim($worksheet->getCell("B{$row}")->getValue() ?? '');
            $deskripsi = trim($worksheet->getCell("C{$row}")->getValue() ?? '');
            $satuan = trim($worksheet->getCell("D{$row}")->getValue() ?? '');

            // Skip baris kosong
            if (empty($tipe) && empty($deskripsi)) {
                continue;
            }

            switch (strtolower($tipe)) {
                case 'tujuan':
                    $currentTujuan = "Tujuan {$kode}. {$deskripsi}";
                    Log::info("Tujuan baru: {$currentTujuan}");
                    break;

                case 'sasaran':
                    if ($currentTujuan) {
                        $currentSasaran = "{$kode} {$deskripsi}";
                        Log::info("Sasaran baru: {$currentSasaran}");
                    }
                    break;

                case 'indikator':
                    if ($currentTujuan && $currentSasaran) {
                        $currentIndikator = "{$kode} {$deskripsi}";
                        $currentSubIndikator = null; // Reset sub indikator saat indikator baru

                        Matriks_Fra::create([
                            'template_fra_id' => $templateFra->id,
                            'tujuan' => $currentTujuan,
                            'sasaran' => $currentSasaran,
                            'indikator' => $currentIndikator,
                            'sub_indikator' => null,
                            'detail_sub' => null,
                            'satuan' => $this->validateSatuan($satuan),
                            'excel_row' => $row
                        ]);
                        $rowsProcessed++;
                        Log::info("Indikator disimpan: {$currentIndikator}");
                    }
                    break;

                case 'sub_indikator':
                    if ($currentTujuan && $currentSasaran && $currentIndikator) {
                        // Sub indikator bisa memiliki kode atau tidak
                        // PERBAIKAN: Simpan tanpa HTML markup, format akan ditambahkan di blade
                        if (!empty($kode)) {
                            $currentSubIndikator = "{$kode}. {$deskripsi}";
                        } else {
                            $currentSubIndikator = $deskripsi;
                        }

                        Matriks_Fra::create([
                            'template_fra_id' => $templateFra->id,
                            'tujuan' => $currentTujuan,
                            'sasaran' => $currentSasaran,
                            'indikator' => $currentIndikator,
                            'sub_indikator' => $currentSubIndikator,
                            'detail_sub' => null,
                            'satuan' => $this->validateSatuan($satuan),
                            'excel_row' => $row
                        ]);
                        $rowsProcessed++;
                        Log::info("Sub indikator disimpan: {$currentSubIndikator}");
                    }
                    break;

                case 'detail_sub':
                    if ($currentTujuan && $currentSasaran && $currentIndikator && $currentSubIndikator) {
                        // Detail sub tidak memiliki kode
                        Matriks_Fra::create([
                            'template_fra_id' => $templateFra->id,
                            'tujuan' => $currentTujuan,
                            'sasaran' => $currentSasaran,
                            'indikator' => $currentIndikator,
                            'sub_indikator' => $currentSubIndikator, // Referensi ke parent sub_indikator
                            'detail_sub' => $deskripsi, // Disimpan di kolom terpisah
                            'satuan' => $this->validateSatuan($satuan),
                            'excel_row' => $row
                        ]);
                        $rowsProcessed++;
                        Log::info("Detail sub disimpan: {$deskripsi}");
                    }
                    break;

                default:
                    Log::warning("Tipe tidak dikenali di baris {$row}: {$tipe}");
                    break;
            }
        }

        if ($rowsProcessed == 0) {
            throw new \Exception("Tidak ada data yang berhasil diproses. Pastikan format sesuai template.");
        }

        Log::info("Berhasil memproses {$rowsProcessed} data dari template");
    }

    public function downloadTemplate()
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            // Sheet 1: PK IKU (Required)
            $pkIkuSheet = $spreadsheet->getActiveSheet();
            $pkIkuSheet->setTitle('PK IKU');
            $this->setupTemplateSheet($pkIkuSheet, 'PK IKU (Wajib)');

            // Sheet 2: PK Suplemen (Optional)
            $pkSuplemenSheet = $spreadsheet->createSheet();
            $pkSuplemenSheet->setTitle('PK Suplemen');
            $this->setupTemplateSheet($pkSuplemenSheet, 'PK Suplemen (Tidak Wajib)');

            // Set active sheet to first one
            $spreadsheet->setActiveSheetIndex(0);

            // Simpan file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Template_FRA.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat template: ' . $e->getMessage());
        }
    }

    private function setupTemplateSheet($sheet, $sheetTitle)
    {
        // Header kolom
        $headers = [
            'A1' => 'Tipe',
            'B1' => 'Kode',
            'C1' => 'Deskripsi',
            'D1' => 'Satuan',
            'E1' => 'Keterangan'
        ];

        // Header sheet title
        $sheet->setCellValue('A1', $sheetTitle);
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFDDEBF7');

        // Set header row
        $row = 2;
        foreach ($headers as $cell => $value) {
            $cell = str_replace('1', $row, $cell); // Replace row number
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFD3D3D3');
        }

        // Contoh data dengan struktur yang lebih fleksibel
        $exampleData = [
            ['Tujuan', '1', 'Menyediakan data statistik untuk dimanfaatkan sebagai dasar pembangunan', '', ''],
            ['Sasaran', '1.1', 'Meningkatnya pemanfaatan data statistik yang berkualitas', '', ''],
            ['Indikator', '1.1.1', 'Persentase pengguna data yang menggunakan data BPS', '%', ''],
            ['Sub_Indikator', '', 'Jumlah publikasi statistik yang terbit tepat waktu', 'Publikasi', ''],
            ['Sub_Indikator', '', 'Jumlah rilis data statistik yang tepat waktu', 'Rilis', ''],
            ['Indikator', '1.1.2', 'Persentase publikasi statistik yang menerapkan standar akurasi', '%', ''],
            ['Sub_Indikator', 'x', 'Jumlah publikasi statistik yang dihasilkan yang bersumber dari aktivitas statistik menerapkan standar akurasi', 'Publikasi', ''],
            ['Detail_Sub', '', 'Jumlah publikasi statistik sosial yang dihasilkan yang bersumber dari aktivitas statistik menerapkan standar akurasi', 'Publikasi', ''],
            ['Detail_Sub', '', 'Jumlah publikasi statistik produksi yang dihasilkan yang bersumber dari aktivitas statistik menerapkan standar akurasi', 'Publikasi', ''],
            ['Sub_Indikator', 'y', 'Jumlah target publikasi statistik yang bersumber dari aktivitas statistik menerapkan standar akurasi', 'Publikasi', ''],
            ['Detail_Sub', '', 'Jumlah target publikasi statistik sosial yang bersumber dari aktivitas statistik menerapkan standar akurasi', 'Publikasi', ''],
            ['Detail_Sub', '', 'Jumlah target publikasi statistik produksi yang bersumber dari aktivitas statistik menerapkan standar akurasi', 'Publikasi', ''],
        ];

        $row = 3; // Start from row 3
        foreach ($exampleData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);

                // Format berdasarkan tipe
                if ($col == 'A') {
                    switch ($value) {
                        case 'Tujuan':
                            $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);
                            $sheet->getStyle("A{$row}:E{$row}")->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFFFCCCC');
                            break;
                        case 'Sasaran':
                            $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);
                            $sheet->getStyle("A{$row}:E{$row}")->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFCCFFCC');
                            break;
                        case 'Indikator':
                            $sheet->getStyle("A{$row}:E{$row}")->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFCCCCFF');
                            break;
                        case 'Sub_Indikator':
                            $sheet->getStyle("C{$row}")->getAlignment()->setIndent(2);
                            $sheet->getStyle("A{$row}:E{$row}")->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFE6E6E6');
                            break;
                        case 'Detail_Sub':
                            $sheet->getStyle("C{$row}")->getAlignment()->setIndent(4);
                            break;
                    }
                }
                $col++;
            }
            $row++;
        }

        // Auto size kolom
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Petunjuk pengisian
        $row += 2;
        $sheet->setCellValue("A{$row}", 'PETUNJUK PENGISIAN:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);

        $instructions = [
            '1. Kolom Tipe: Pilih "Tujuan", "Sasaran", "Indikator", "Sub_Indikator", atau "Detail_Sub"',
            '2. Kolom Kode:',
            '   - Tujuan: Wajib diisi (contoh: 1, 2, dst)',
            '   - Sasaran: Wajib diisi (contoh: 1.1, 1.2, dst)',
            '   - Indikator: Wajib diisi (contoh: 1.1.1, 1.1.2, dst)',
            '   - Sub_Indikator: Opsional (bisa berisi kode seperti "x", "y", atau kosong)',
            '   - Detail_Sub: Tidak perlu diisi/kosong',
            '3. Struktur hirarkis: Tujuan → Sasaran → Indikator → Sub_Indikator → Detail_Sub',
            '4. Sub_Indikator ditempatkan langsung di bawah Indikator induknya',
            '5. Detail_Sub ditempatkan langsung di bawah Sub_Indikator induknya',
            '6. Kolom Satuan diisi dengan jenis satuan (%, Publikasi, Dokumen, dll)',
            '7. Jangan mengubah struktur atau format template ini',
        ];

        foreach ($instructions as $instruction) {
            $row++;
            $sheet->setCellValue("A{$row}", $instruction);
        }

        // Tambahkan validasi data untuk kolom Tipe
        $validation = $sheet->getCell('A3')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowDropDown(true);
        $validation->setFormula1('"Tujuan,Sasaran,Indikator,Sub_Indikator,Detail_Sub"');
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Input Error');
        $validation->setError('Pilih salah satu: Tujuan, Sasaran, Indikator, Sub_Indikator, atau Detail_Sub');

        // Terapkan validasi ke seluruh kolom A
        for ($i = 3; $i <= 1000; $i++) {
            $sheet->getCell("A{$i}")->setDataValidation(clone $validation);
        }
    }

    public function detail(Fra $fra, $triwulan)
    {
        $matriksList = $fra->matriks_fra()
            ->with(['target_fra'])
            ->get();

        return view('fra.detail', compact('fra', 'triwulan', 'matriksList'));
    }

    public function getTriwulanDetails($id)
    {
        Log::info('getTriwulanDetails called', ['fra_id' => $id]);
        
        try {
            $fra = Fra::findOrFail($id);
            
            Log::info('FRA found', [
                'fra_id' => $fra->id,
                'tahun_berjalan' => $fra->tahun_berjalan,
                'existing_triwulans_count' => $fra->triwulans()->count()
            ]);
            
            // Cek apakah ada triwulan yang sudah dibuat
            $hasTriwulans = $fra->triwulans()->exists();
            
            if (!$hasTriwulans) {
                // Jika belum ada triwulan, coba buat triwulan terlebih dahulu
                // Ini untuk menangani kasus dimana target sudah diisi tapi triwulan belum dibuat
                $hasTargets = \App\Models\Target_Fra::whereIn('matriks_fra_id', $fra->matriks_fra->pluck('id'))
                                ->whereNotNull('target_tw4')
                                ->where('target_tw4', '!=', 0)
                                ->exists();
                
                if ($hasTargets) {
                    // Jika ada target, buat triwulan
                    $this->createTriwulans($fra);
                    $hasTriwulans = true;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak dapat memuat data triwulan. Target FRA harus diisi dan difinalisasi terlebih dahulu.'
                    ], 400);
                }
            }
            
            // Validasi apakah target FRA sudah difinalisasi (setelah memastikan triwulan ada)
            if (!$fra->isTargetFinalized()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data triwulan belum dapat ditampilkan. Target FRA harus difinalisasi terlebih dahulu dengan mengisi semua target yang diperlukan.'
                ], 400);
            }
            
            /** @var \App\Models\Pengguna|null $user */
            $user = Auth::user();
            $isSuperAdmin = false;
            $isAdmin = false;
            $isKetuaTim = false;
            
            if ($user) {
                $isSuperAdmin = $user->isSuperAdmin();
                $isAdmin = $user->isAdmin();
                $isKetuaTim = $user->isKetuaTim();
            }
            
            // Tentukan apakah user adalah anggota tim (bukan admin, super admin, atau ketua tim)
            $isTeamMember = $user && !$isSuperAdmin && !$isAdmin && !$isKetuaTim;

            // Menggunakan accessor yang sudah ada di model Fra
            $triwulansData = $fra->triwulans; // Ini menggunakan accessor getTriwulansAttribute()
            
            // Validasi apakah data triwulan berhasil dimuat
            if (empty($triwulansData) || $triwulansData->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data triwulan tidak ditemukan atau belum dibuat. Silakan hubungi administrator untuk membuat data triwulan.'
                ], 404);
            }

            $triwulans = collect($triwulansData)->map(function ($triwulan) use ($fra) {
                return [
                    'number' => $triwulan['number'],
                    'status' => $triwulan['status'],
                    'dateRange' => $triwulan['date_range'],
                    'id' => $fra->id . '_' . $triwulan['number'],
                    'target_percentage' => 100,
                    'realisasi_percentage' => $triwulan['status'] === 'Selesai' ? 100 : ($triwulan['status'] === 'Dalam Proses' ? 75 : 0)
                ];
            });
            
            // Pastikan ada 4 triwulan
            if ($triwulans->count() !== 4) {
                 Log::warning('Jumlah triwulan tidak lengkap', [
                     'fra_id' => $fra->id,
                     'triwulan_count' => $triwulans->count(),
                     'expected' => 4
                 ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Data triwulan tidak lengkap. Ditemukan ' . $triwulans->count() . ' triwulan, seharusnya 4. Silakan hubungi administrator.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'fra_id' => $fra->id,
                    'fra_year' => $fra->tahun_berjalan,
                    'triwulans' => $triwulans,
                    'user_roles' => [
                        'isSuperAdmin' => $isSuperAdmin,
                        'isAdmin' => $isAdmin,
                        'isKetuaTim' => $isKetuaTim,
                        'isTeamMember' => $isTeamMember
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getTriwulanDetails', [
                'fra_id' => $id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data triwulan: ' . $e->getMessage() . '. Silakan coba lagi atau hubungi administrator.'
            ], 500);
        }
    }

    

    public function downloadFra($id)
    {
        $fra = Fra::findOrFail($id);

        if ($fra->status !== 'Selesai') {
            return redirect()->back()->with('error', 'Hanya FRA dengan status Selesai yang dapat diunduh');
        }

        try {
            $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, storage_path('app/' . $fra->file_template));

            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'File template FRA tidak ditemukan');
            }

            return response()->download($filePath, 'FRA_' . $fra->tahun_berjalan . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Download error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunduh file: ' . $e->getMessage());
        }
    }

    private function createTriwulans($fra)
    {
        $year = $fra->tahun_berjalan;
        $triwulanDates = [
            1 => ['start' => "{$year}-01-01", 'end' => "{$year}-03-31"],
            2 => ['start' => "{$year}-04-01", 'end' => "{$year}-06-30"],
            3 => ['start' => "{$year}-07-01", 'end' => "{$year}-09-30"],
            4 => ['start' => "{$year}-10-01", 'end' => "{$year}-12-31"]
        ];

        foreach ($triwulanDates as $nomor => $dates) {
            Triwulan::updateOrCreate(
                [
                    'fra_id' => $fra->id,
                    'nomor' => $nomor
                ],
                [
                    'nama_triwulan' => "Triwulan {$nomor}",
                    'tanggal_mulai' => $dates['start'],
                    'tanggal_selesai' => $dates['end'],
                    'status' => 'Belum Mulai'
                ]
            );
        }
    }

    private function getTriwulanDateRange($year, $triwulan)
    {
        switch ($triwulan) {
            case 1:
                return [
                    'start' => "{$year}-01-01",
                    'end' => "{$year}-03-31"
                ];
            case 2:
                return [
                    'start' => "{$year}-04-01",
                    'end' => "{$year}-06-30"
                ];
            case 3:
                return [
                    'start' => "{$year}-07-01",
                    'end' => "{$year}-09-30"
                ];
            case 4:
                return [
                    'start' => "{$year}-10-01",
                    'end' => "{$year}-12-31"
                ];
            default:
                throw new \Exception("Invalid triwulan number");
        }
    }

    public function updateTriwulanStatuses()
    {
        $triwulans = Triwulan::all();
        foreach ($triwulans as $triwulan) {
            $triwulan->updateStatus();
        }
    }

    public function simpanRealisasi(Request $request, Fra $fra, $triwulan)
    {
        try {
            DB::beginTransaction();

            $actionType = $request->input('action_type', 'save');
            $triwulanObj = Triwulan::where('fra_id', $fra->id)->where('nomor', $triwulan)->firstOrFail();

            // Simpan data realisasi, kendala, solusi, dll.
            $realisasiInput = $request->input('realisasi', []);
            $capkinKumulatifInput = $request->input('capkin_kumulatif', []);
            $capkinSetahunInput = $request->input('capkin_setahun', []);
            $kendalaInput = $request->input('kendala', []);
            $solusiInput = $request->input('solusi', []);
            $tindakLanjutInput = $request->input('tindak_lanjut', []);
            $picTindakLanjutInput = $request->input('pic_tindak_lanjut_id', []);
            $batasWaktuInput = $request->input('batas_waktu_tindak_lanjut', []);

            $matriksIds = array_keys($realisasiInput + $kendalaInput);

            foreach ($matriksIds as $matriksId) {
                Realisasi_Fra::updateOrCreate(
                    [
                        'matriks_fra_id' => $matriksId,
                        'triwulan_id' => $triwulanObj->id
                    ],
                    [
                        'realisasi' => isset($realisasiInput[$matriksId]) ? str_replace(',', '', $realisasiInput[$matriksId]) : null,
                        'capkin_kumulatif' => isset($capkinKumulatifInput[$matriksId]) ? str_replace(',', '', $capkinKumulatifInput[$matriksId]) : 0,
                        'capkin_setahun' => isset($capkinSetahunInput[$matriksId]) ? str_replace(',', '', $capkinSetahunInput[$matriksId]) : 0,
                        'kendala' => $kendalaInput[$matriksId] ?? null,
                        'solusi' => $solusiInput[$matriksId] ?? null,
                        'tindak_lanjut' => $tindakLanjutInput[$matriksId] ?? null,
                        'pic_tindak_lanjut_id' => $picTindakLanjutInput[$matriksId] ?? null,
                        'batas_waktu_tindak_lanjut' => $batasWaktuInput[$matriksId] ?? null,
                    ]
                );
            }

            // Handle file uploads
            if ($request->hasFile('bukti_dukung')) {
                $googleDriveService = new GoogleDriveOAuthService();

                // Pastikan FRA punya folder di Google Drive melalui kegiatan Form Rencana Aksi
                $formRencanaAksiKegiatan = $fra->formRencanaAksiKegiatan();
                $folderId = null;
                
                if ($formRencanaAksiKegiatan && $formRencanaAksiKegiatan->folder_id) {
                    $folderId = $formRencanaAksiKegiatan->folder_id;
                } else {
                    throw new Exception("FRA tidak memiliki kegiatan Form Rencana Aksi atau folder Google Drive");
                }

                // Dapatkan folder triwulan yang sesuai
                $triwulanFolderId = $googleDriveService->getTriwulanFolderId($folderId, $triwulanObj->nomor);
                
                if (!$triwulanFolderId) {
                    throw new Exception("Gagal mendapatkan folder triwulan {$triwulanObj->nomor}");
                }

                foreach ($request->file('bukti_dukung') as $matriksId => $files) {
                    $realisasiFra = Realisasi_Fra::where('matriks_fra_id', $matriksId)
                        ->where('triwulan_id', $triwulanObj->id)
                        ->first();
                    if (!$realisasiFra) continue;

                    foreach ($files as $file) {
                        $originalName = $file->getClientOriginalName();
                        $fileName = pathinfo($originalName, PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();

                        $uploadResult = $googleDriveService->uploadFile($file, $fileName, $triwulanFolderId);

                        if ($uploadResult && $uploadResult['success']) {
                            Buktidukung_Fra::create([
                                'realisasi_fra_id' => $realisasiFra->id,
                                'nama_dokumen' => $originalName,
                                'file_name' => $fileName,
                                'google_drive_file_id' => $uploadResult['file_id'],
                                'webViewLink' => $uploadResult['webViewLink'],
                            ]);
                        } else {
                            Log::error('Gagal mengunggah bukti dukung ke Google Drive', [
                                'matriks_id' => $matriksId,
                                'file_name' => $originalName,
                            ]);
                        }
                    }
                }
            }


            if ($actionType === 'finalize') {
                $triwulanObj->update(['status' => 'Selesai']);
                $this->updateTriwulanStatuses();
                DB::commit();
                
                // Handle AJAX/JSON response for finalization
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Realisasi Triwulan {$triwulan} berhasil difinalisasi!",
                        'redirect_url' => route('fra.detail', ['fra' => $fra->id, 'triwulan' => $triwulan])
                    ]);
                }
                
                return redirect()->route('fra.detail', ['fra' => $fra->id, 'triwulan' => $triwulan])
                    ->with('success', "Realisasi Triwulan {$triwulan} berhasil difinalisasi!");
            }

            DB::commit();
            
            // Handle AJAX/JSON response for save
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Perubahan realisasi berhasil disimpan.'
                ]);
            }
            
            return redirect()->back()->with('success', 'Perubahan realisasi berhasil disimpan.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in simpanRealisasi: ' . $e->getMessage());
            
            // Handle AJAX/JSON response for errors
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get capaian kinerja activities based on existing FRA
     */
    public function getCapaianKinerja(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Default 10 items per page

        $allFras = Fra::all();

        foreach ($allFras as $fra) {
            if (!$fra->hasCapaianKinerjaActivity()) {
                $this->createCapaianKinerjaActivity($fra);
            }
        }

        // Get all capaian kinerja activities with pagination
        $capaianKinerjaActivities = Kegiatan::where('nama_kegiatan', 'like', 'Monitoring Capaian Kinerja FRA%')
            ->with(['sub_komponen', 'renstra'])
            ->orderBy('tahun_berjalan', 'desc')
            ->paginate($perPage);

        return $capaianKinerjaActivities;
    }

    /**
     * Create capaian kinerja activity automatically when FRA exists
     */
    private function createCapaianKinerjaActivity(Fra $fra)
    {
        try {
            $tahun = $fra->tahun_berjalan;
            $namaKegiatan = 'Monitoring Capaian Kinerja FRA ' . $tahun;

            // 1. Dapatkan data penting
            $googleDriveService = new \App\Services\GoogleDriveOAuthService();
            $activeRenstra = Renstra::whereYear('periode_awal', '<=', $tahun)
                ->whereYear('periode_akhir', '>=', $tahun)
                ->first() ?? Renstra::orderBy('periode_akhir', 'desc')->first();

            if (!$activeRenstra) {
                Log::warning("Tidak dapat membuat kegiatan Capaian Kinerja untuk FRA {$tahun} karena tidak ada Renstra aktif.", ['fra_id' => $fra->id]);
                return;
            }

            // 2. Siapkan Sub Komponen yang benar - monitoring capaian kinerja masuk ke Form Rencana Aksi
            $komponenPengukuran = Komponen::firstOrCreate(['id' => 2], ['komponen' => 'Pengukuran Kinerja']);
            $subKomponenFormRencanaAksi = Sub_Komponen::firstOrCreate(
                ['sub_komponen' => 'Form Rencana Aksi', 'komponen_id' => $komponenPengukuran->id]
            );

            // 3. Buat Folder Google Drive
            $folderResult = $googleDriveService->createCapaianKinerjaFolder($namaKegiatan, (int)$tahun);
            $folderId = $folderResult['folder_id'] ?? null;

            if (empty($folderId)) {
                Log::error("Gagal membuat folder Google Drive untuk kegiatan '{$namaKegiatan}'", [
                    'fra_id' => $fra->id,
                    'folder_result' => $folderResult
                ]);
            }

            // 4. Buat Kegiatan dengan folder_id
            $kegiatan = Kegiatan::create([
                'nama_kegiatan' => $namaKegiatan,
                'tahun_berjalan' => $tahun,
                'tanggal_mulai' => "{$tahun}-01-01",
                'tanggal_berakhir' => "{$tahun}-12-31",
                'sub_komponen_id' => $subKomponenFormRencanaAksi->id,
                'renstra_id' => $activeRenstra->id,
                'folder_id' => $folderId,
            ]);

            Log::info("Kegiatan 'Monitoring Capaian Kinerja' berhasil dibuat secara otomatis.", [
                'kegiatan_id' => $kegiatan->id,
                'fra_id' => $fra->id,
                'folder_id' => $folderId
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal membuat kegiatan Capaian Kinerja secara otomatis.', [
                'fra_id' => $fra->id,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Store capaian kinerja activity manually
     */
    public function storeCapaianKinerja(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tahun_berjalan' => 'required|numeric|digits:4',
            'fra_id' => 'required|exists:fra,id'
        ]);

        try {
            $fra = Fra::find($request->fra_id);

            // Check if capaian kinerja already exists for this FRA
            $existingActivity = Kegiatan::where('nama_kegiatan', $request->nama_kegiatan)
                ->where('tahun_berjalan', $request->tahun_berjalan)
                ->first();

            if ($existingActivity) {
                return redirect()->back()->with('error', 'Kegiatan capaian kinerja untuk FRA ini sudah ada!');
            }

            // Calculate dates: Full year (January 1st to December 31st) of the FRA year
            // Since capaian kinerja is now divided into 4 triwulans with quarterly upload periods
            $startDate = Carbon::createFromDate($fra->tahun_berjalan, 1, 1); // January 1st of FRA year
            $endDate = Carbon::createFromDate($fra->tahun_berjalan, 12, 31);  // December 31st of FRA year

            // Find or create appropriate sub_komponen for capaian kinerja - use Form Rencana Aksi
            $komponenPengukuran = Komponen::firstOrCreate(['id' => 2], ['komponen' => 'Pengukuran Kinerja']);
            $subKomponen = Sub_Komponen::firstOrCreate(
                ['sub_komponen' => 'Form Rencana Aksi', 'komponen_id' => $komponenPengukuran->id]
            );

            // Get active renstra
            $activeRenstra = \App\Models\Renstra::orderBy('periode_akhir', 'desc')->first();

            // Create the capaian kinerja activity
            $kegiatan = Kegiatan::create([
                'nama_kegiatan' => $request->nama_kegiatan,
                'tahun_berjalan' => $request->tahun_berjalan,
                'tanggal_mulai' => $startDate->format('Y-m-d'),
                'tanggal_berakhir' => $endDate->format('Y-m-d'),
                'sub_komponen_id' => $subKomponen->id,
                'renstra_id' => $activeRenstra ? $activeRenstra->id : 1
            ]);

            return redirect()->back()->with('success', 'Kegiatan capaian kinerja berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Store Capaian Kinerja Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan kegiatan capaian kinerja.');
        }
    }

    /**
     * Menambah indikator umum baru
     */
    public function addUmumIndicator(Request $request, $fraId)
    {
        try {
            Log::info('=== START addUmumIndicator ===', [
                'fra_id' => $fraId,
                'request_method' => $request->method(),
                'request_url' => $request->url(),
                'content_type' => $request->header('Content-Type'),
                'request_data' => $request->all(),
                'raw_input' => $request->getContent()
            ]);

            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:500',
                'unit' => 'required|string|max:50'
            ]);

            Log::info('Validation passed', ['validated' => $validated]);

            // Cari FRA
            $fra = Fra::findOrFail($fraId);
            Log::info('FRA found', ['fra_id' => $fra->id, 'fra_name' => $fra->nama_fra]);

            // Start database transaction
            DB::beginTransaction();

            // Cari atau buat template jenis "Umum"
            $templateJenisUmum = Template_Jenis::firstOrCreate(
                ['nama' => 'Umum'],
                ['wajib' => false]
            );
            Log::info('Template Jenis created/found', ['template_jenis_id' => $templateJenisUmum->id]);

            // Cari atau buat template FRA untuk umum
            $templateFraUmum = Template_Fra::firstOrCreate([
                'fra_id' => $fra->id,
                'template_jenis_id' => $templateJenisUmum->id
            ]);
            Log::info('Template FRA created/found', ['template_fra_id' => $templateFraUmum->id]);

            // Buat matriks FRA baru
            $matriksFra = Matriks_Fra::create([
                'template_fra_id' => $templateFraUmum->id,
                'tujuan' => 'Umum',
                'sasaran' => null, // Biarkan null untuk konsistensi dengan data existing
                'indikator' => $validated['name'],
                'sub_indikator' => null,
                'detail_sub' => null,
                'satuan' => $validated['unit'],
                'excel_row' => null
            ]);

            Log::info('Matriks FRA created successfully', [
                'matriks_fra_id' => $matriksFra->id,
                'indikator' => $matriksFra->indikator,
                'satuan' => $matriksFra->satuan,
                'template_fra_id' => $matriksFra->template_fra_id,
                'tujuan' => $matriksFra->tujuan
            ]);

            // Verify data is actually in database
            $verifyData = Matriks_Fra::find($matriksFra->id);
            Log::info('Verification: Data in database', [
                'found' => $verifyData ? 'YES' : 'NO',
                'data' => $verifyData ? $verifyData->toArray() : null
            ]);

            // Commit transaction
            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Indikator umum berhasil ditambahkan',
                'data' => [
                    'id' => $matriksFra->id,
                    'indikator' => $matriksFra->indikator,
                    'satuan' => $matriksFra->satuan
                ]
            ];

            Log::info('=== SUCCESS addUmumIndicator ===', ['response' => $response]);

            return response()->json($response);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error in addUmumIndicator', [
                'fra_id' => $fraId,
                'errors' => $e->errors(),
                'messages' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . implode(', ', collect($e->errors())->flatten()->toArray()),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('=== ERROR addUmumIndicator ===', [
                'fra_id' => $fraId,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan indikator: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menambah sub indikator umum baru
     */
    public function addUmumSubIndicator(Request $request, $fraId)
    {
        try {
            $validated = $request->validate([
                'parent_id' => 'required|integer|exists:matriks_fra,id',
                'name' => 'required|string|max:500',
                'unit' => 'required|string|max:50'
            ]);

            $fra = Fra::findOrFail($fraId);
            $parentMatriks = Matriks_Fra::findOrFail($validated['parent_id']);

            // Pastikan parent adalah indikator utama di bagian umum
            if (
                !empty($parentMatriks->sub_indikator) ||
                (strtolower(trim($parentMatriks->tujuan)) !== 'umum') ||
                $parentMatriks->template_fra->template_jenis->nama !== 'Umum'
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parent indikator tidak valid'
                ], 400);
            }

            // Buat sub indikator baru
            $subMatriksFra = Matriks_Fra::create([
                'template_fra_id' => $parentMatriks->template_fra_id,
                'tujuan' => $parentMatriks->tujuan,
                'sasaran' => $parentMatriks->sasaran,
                'indikator' => $parentMatriks->indikator,
                'sub_indikator' => $validated['name'],
                'detail_sub' => null,
                'satuan' => $validated['unit'],
                'excel_row' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sub indikator berhasil ditambahkan',
                'data' => $subMatriksFra
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding umum sub indicator: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan sub indikator: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus indikator atau sub indikator umum
     */
    public function deleteUmumIndicator(Request $request, $fraId, $matriksId)
    {
        try {
            $fra = Fra::findOrFail($fraId);
            $matriks = Matriks_Fra::findOrFail($matriksId);

            // Pastikan matriks adalah bagian dari FRA ini dan bagian umum
            if (
                $matriks->template_fra->fra_id != $fra->id ||
                strtolower(trim($matriks->tujuan)) !== 'umum' ||
                $matriks->template_fra->template_jenis->nama !== 'Umum'
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Matriks tidak valid atau bukan bagian umum'
                ], 400);
            }

            DB::beginTransaction();

            $matriksIds = [$matriks->id];

            // Jika ini indikator utama, hapus semua sub indikator yang terkait
            if (empty($matriks->sub_indikator)) {
                // Ambil semua sub indikator yang terkait
                $subMatriks = Matriks_Fra::where('template_fra_id', $matriks->template_fra_id)
                    ->where('indikator', $matriks->indikator)
                    ->whereNotNull('sub_indikator')
                    ->get();

                $subMatriksIds = $subMatriks->pluck('id')->toArray();
                $matriksIds = array_merge($matriksIds, $subMatriksIds);

                // Hapus sub indikator
                Matriks_Fra::whereIn('id', $subMatriksIds)->delete();
            }

            // Hapus target FRA, target PK, dan realisasi yang terkait
            Target_Fra::whereIn('matriks_fra_id', $matriksIds)->delete();
            Target_Pk::whereIn('matriks_fra_id', $matriksIds)->delete();
            Realisasi_Fra::whereIn('matriks_fra_id', $matriksIds)->delete();

            // Hapus matriks utama
            $matriks->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Indikator berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting umum indicator: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus indikator: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan data target ke tabel yang sesuai dari form target FRA tab Umum
     * - Data triwulan (TW1-TW4) disimpan ke target_fra
     * - Data target PK disimpan ke target_pk
     */
    public function saveTargetPk(Request $request, $fraId)
    {
        try {
            $matriksId = $request->matriks_id;
            $type = $request->type;
            $value = $request->value;

            // Validate the request
            if (!$matriksId || !$type) {
                return response()->json(['success' => false, 'message' => 'Data tidak lengkap'], 400);
            }

            // Handle quarterly targets (tw1, tw2, tw3, tw4) - save to target_fra table
            if (strpos($type, 'tw') === 0) {
                $targetFra = Target_Fra::where('matriks_fra_id', $matriksId)->first();

                if (!$targetFra) {
                    $targetFra = new Target_Fra();
                    $targetFra->matriks_fra_id = $matriksId;
                }

                // Map the type to target_fra column
                $column = 'target_' . $type; // tw1 becomes target_tw1
                $targetFra->{$column} = $value;
                $targetFra->save();
            }
            // Handle target PK - save to target_pk table
            else if ($type === 'target') {
                $targetPk = Target_Pk::where('matriks_fra_id', $matriksId)->first();

                if (!$targetPk) {
                    $targetPk = new Target_Pk();
                    $targetPk->matriks_fra_id = $matriksId;
                }

                $targetPk->target_pk = $value;
                $targetPk->save();
            }

            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
        } catch (\Exception $e) {
            Log::error("Save target data error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Download FRA per triwulan dalam format Excel atau PDF - New Implementation
     */
    public function downloadFraTriwulan(Fra $fra, $triwulan, $format)
    {
        // Validasi parameter
        if (!in_array($triwulan, [1, 2, 3, 4])) {
            return redirect()->back()->with('error', 'Nomor triwulan tidak valid');
        }

        if (!in_array($format, ['excel', 'pdf'])) {
            return redirect()->back()->with('error', 'Format download tidak valid');
        }

        try {
            $optimizedController = new \App\Http\Controllers\OptimizedDownloadController();

            if ($format === 'excel') {
                return $optimizedController->downloadExcelFast($fra, $triwulan);
            } else {
                return $optimizedController->downloadPdfFast($fra, $triwulan);
            }
        } catch (\Exception $e) {
            Log::error('Error downloading FRA per triwulan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunduh FRA: ' . $e->getMessage());
        }
    }

    /**
     * Download FRA lengkap (semua triwulan) dalam format Excel atau PDF - New Implementation
     */
    public function downloadFraLengkap(Fra $fra, $format)
    {
        if (!in_array($format, ['excel', 'pdf'])) {
            return redirect()->back()->with('error', 'Format download tidak valid');
        }

        try {
            $optimizedController = new \App\Http\Controllers\OptimizedDownloadController();

            if ($format === 'excel') {
                return $optimizedController->downloadExcelFast($fra, null);
            } else {
                return $optimizedController->downloadPdfFast($fra, null);
            }
        } catch (\Exception $e) {
            Log::error('Error downloading FRA lengkap: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunduh FRA lengkap: ' . $e->getMessage());
        }
    }

    /**
     * Generate Excel untuk FRA per triwulan
     */
    private function generateExcelTriwulan($fra, $triwulan, $matriksList, $realisasiData, $fileName)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Load template asli jika ada
        $originalTemplatePath = storage_path('app/' . $fra->file_template);
        if (file_exists($originalTemplatePath)) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($originalTemplatePath);
        }

        // Ambil sheet PK IKU
        $sheet = $spreadsheet->getSheetByName('PK IKU');
        if (!$sheet) {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('PK IKU');
        }

        // Tulis data realisasi ke Excel
        $this->writeRealisasiToSheet($sheet, $matriksList->where('template_fra.template_jenis.nama', 'PK IKU'), $realisasiData, $triwulan);

        // Proses sheet Suplemen jika ada
        if ($fra->hasTemplateJenis('PK Suplemen')) {
            $suplemenSheet = $spreadsheet->getSheetByName('PK Suplemen') ?? $spreadsheet->getSheetByName('IKU Suplemen');
            if ($suplemenSheet) {
                $this->writeRealisasiToSheet($suplemenSheet, $matriksList->where('template_fra.template_jenis.nama', 'PK Suplemen'), $realisasiData, $triwulan);
            }
        }

        // Proses sheet Umum jika ada
        if ($fra->hasTemplateJenis('Umum')) {
            $umumSheet = $spreadsheet->getSheetByName('Umum');
            if (!$umumSheet) {
                $umumSheet = $spreadsheet->createSheet();
                $umumSheet->setTitle('Umum');
                $this->setupUmumSheet($umumSheet);
            }
            $this->writeRealisasiToSheet($umumSheet, $matriksList->where('template_fra.template_jenis.nama', 'Umum'), $realisasiData, $triwulan);
        }

        // Generate file
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Generate PDF untuk FRA per triwulan
     */
    private function generatePdfTriwulan($fra, $triwulan, $matriksList, $realisasiData, $fileName)
    {
        $data = [
            'fra' => $fra,
            'triwulan' => $triwulan,
            'matriksList' => $matriksList,
            'realisasiData' => $realisasiData,
            'title' => "Form Rencana Aksi {$fra->tahun_berjalan} - Triwulan {$triwulan}"
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.fra_triwulan', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($fileName . '.pdf');
    }

    /**
     * Generate Excel untuk FRA lengkap
     */
    private function generateExcelLengkap($fra, $matriksList, $allRealisasiData, $fileName)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Load template asli jika ada
        $originalTemplatePath = storage_path('app/' . $fra->file_template);
        if (file_exists($originalTemplatePath)) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($originalTemplatePath);
        }

        // Ambil sheet PK IKU
        $sheet = $spreadsheet->getSheetByName('PK IKU');
        if (!$sheet) {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('PK IKU');
        }

        // Tulis data lengkap ke Excel
        $this->writeFullYearDataToSheet($sheet, $matriksList->where('template_fra.template_jenis.nama', 'PK IKU'), $allRealisasiData);

        // Proses sheet Suplemen jika ada
        if ($fra->hasTemplateJenis('PK Suplemen')) {
            $suplemenSheet = $spreadsheet->getSheetByName('PK Suplemen') ?? $spreadsheet->getSheetByName('IKU Suplemen');
            if ($suplemenSheet) {
                $this->writeFullYearDataToSheet($suplemenSheet, $matriksList->where('template_fra.template_jenis.nama', 'PK Suplemen'), $allRealisasiData);
            }
        }

        // Proses sheet Umum jika ada
        if ($fra->hasTemplateJenis('Umum')) {
            $umumSheet = $spreadsheet->getSheetByName('Umum');
            if (!$umumSheet) {
                $umumSheet = $spreadsheet->createSheet();
                $umumSheet->setTitle('Umum');
                $this->setupUmumSheet($umumSheet);
            }
            $this->writeFullYearDataToSheet($umumSheet, $matriksList->where('template_fra.template_jenis.nama', 'Umum'), $allRealisasiData);
        }

        // Generate file
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Generate PDF untuk FRA lengkap
     */
    private function generatePdfLengkap($fra, $matriksList, $allRealisasiData, $fileName)
    {
        $data = [
            'fra' => $fra,
            'matriksList' => $matriksList,
            'allRealisasiData' => $allRealisasiData,
            'title' => "Form Rencana Aksi {$fra->tahun_berjalan} - Lengkap"
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.fra_lengkap', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($fileName . '.pdf');
    }

    /**
     * Tulis data realisasi ke sheet Excel untuk triwulan tertentu
     */
    private function writeRealisasiToSheet($sheet, $matriksList, $realisasiData, $triwulan)
    {
        $row = 3; // Mulai dari baris 3 (setelah header)

        foreach ($matriksList as $matriks) {
            $realisasi = $realisasiData->get($matriks->id);

            // Cari baris yang sesuai berdasarkan indikator/deskripsi
            $found = false;
            for ($searchRow = 3; $searchRow <= $sheet->getHighestDataRow(); $searchRow++) {
                $cellValue = $sheet->getCell("C{$searchRow}")->getValue();
                if (stripos($cellValue, $matriks->indikator) !== false) {
                    $row = $searchRow;
                    $found = true;
                    break;
                }
            }

            if ($found && $realisasi) {
                // Tulis realisasi berdasarkan triwulan ke kolom yang sesuai
                $realisasiCol = $this->getRealisasiColumn($triwulan); // F untuk TW1, G untuk TW2, dll
                $sheet->setCellValue($realisasiCol . $row, $realisasi->realisasi);

                // Tulis kendala, solusi, dll jika ada
                if (!empty($realisasi->kendala)) {
                    $kendalaCol = $this->getKendalaColumn();
                    $sheet->setCellValue($kendalaCol . $row, $realisasi->kendala);
                }

                if (!empty($realisasi->solusi)) {
                    $solusiCol = $this->getSolusiColumn();
                    $sheet->setCellValue($solusiCol . $row, $realisasi->solusi);
                }

                if (!empty($realisasi->tindak_lanjut)) {
                    $tindakLanjutCol = $this->getTindakLanjutColumn();
                    $sheet->setCellValue($tindakLanjutCol . $row, $realisasi->tindak_lanjut);
                }

                if (!empty($realisasi->pic_tindak_lanjut)) {
                    $picCol = $this->getPicColumn();
                    $sheet->setCellValue($picCol . $row, $realisasi->pic_tindak_lanjut);
                }

                if (!empty($realisasi->batas_waktu_tindak_lanjut)) {
                    $batasWaktuCol = $this->getBatasWaktuColumn();
                    $sheet->setCellValue($batasWaktuCol . $row, $realisasi->batas_waktu_tindak_lanjut->format('d/m/Y'));
                }
            }
        }
    }

    /**
     * Tulis data lengkap semua triwulan ke sheet Excel
     */
    private function writeFullYearDataToSheet($sheet, $matriksList, $allRealisasiData)
    {
        foreach ($matriksList as $matriks) {
            // Cari baris yang sesuai
            $found = false;
            for ($searchRow = 3; $searchRow <= $sheet->getHighestDataRow(); $searchRow++) {
                $cellValue = $sheet->getCell("C{$searchRow}")->getValue();
                if (stripos($cellValue, $matriks->indikator) !== false) {
                    $row = $searchRow;
                    $found = true;
                    break;
                }
            }

            if ($found) {
                // Tulis realisasi untuk semua triwulan
                foreach ([1, 2, 3, 4] as $tw) {
                    $realisasi = $allRealisasiData[$tw]->get($matriks->id);
                    if ($realisasi) {
                        $realisasiCol = $this->getRealisasiColumn($tw);
                        $sheet->setCellValue($realisasiCol . $row, $realisasi->realisasi);
                    }
                }

                // Gabungkan kendala, solusi, dll dari semua triwulan
                $kendalaAll = [];
                $solusiAll = [];
                $tindakLanjutAll = [];
                $picAll = [];
                $batasWaktuAll = [];

                foreach ([1, 2, 3, 4] as $tw) {
                    $realisasi = $allRealisasiData[$tw]->get($matriks->id);
                    if ($realisasi) {
                        if (!empty($realisasi->kendala)) {
                            $kendalaAll[] = "TW{$tw}: " . $realisasi->kendala;
                        }
                        if (!empty($realisasi->solusi)) {
                            $solusiAll[] = "TW{$tw}: " . $realisasi->solusi;
                        }
                        if (!empty($realisasi->tindak_lanjut)) {
                            $tindakLanjutAll[] = "TW{$tw}: " . $realisasi->tindak_lanjut;
                        }
                        if (!empty($realisasi->pic_tindak_lanjut)) {
                            $picAll[] = "TW{$tw}: " . $realisasi->pic_tindak_lanjut;
                        }
                        if (!empty($realisasi->batas_waktu_tindak_lanjut)) {
                            $batasWaktuAll[] = "TW{$tw}: " . $realisasi->batas_waktu_tindak_lanjut->format('d/m/Y');
                        }
                    }
                }

                // Tulis data gabungan dengan line break
                if (!empty($kendalaAll)) {
                    $kendalaCol = $this->getKendalaColumn();
                    $sheet->setCellValue($kendalaCol . $row, implode("\n\n", $kendalaAll));
                }

                if (!empty($solusiAll)) {
                    $solusiCol = $this->getSolusiColumn();
                    $sheet->setCellValue($solusiCol . $row, implode("\n\n", $solusiAll));
                }

                if (!empty($tindakLanjutAll)) {
                    $tindakLanjutCol = $this->getTindakLanjutColumn();
                    $sheet->setCellValue($tindakLanjutCol . $row, implode("\n\n", $tindakLanjutAll));
                }

                if (!empty($picAll)) {
                    $picCol = $this->getPicColumn();
                    $sheet->setCellValue($picCol . $row, implode("\n\n", $picAll));
                }

                if (!empty($batasWaktuAll)) {
                    $batasWaktuCol = $this->getBatasWaktuColumn();
                    $sheet->setCellValue($batasWaktuCol . $row, implode("\n\n", $batasWaktuAll));
                }
            }
        }
    }

    /**
     * Helper methods untuk mendapatkan kolom Excel yang sesuai
     */
    private function getRealisasiColumn($triwulan)
    {
        // Asumsi kolom realisasi: F=TW1, G=TW2, H=TW3, I=TW4
        $columns = [1 => 'F', 2 => 'G', 3 => 'H', 4 => 'I'];
        return $columns[$triwulan] ?? 'F';
    }

    private function getKendalaColumn()
    {
        return 'J'; // Kolom kendala
    }

    private function getSolusiColumn()
    {
        return 'K'; // Kolom solusi
    }

    private function getTindakLanjutColumn()
    {
        return 'L'; // Kolom tindak lanjut
    }

    private function getPicColumn()
    {
        return 'M'; // Kolom PIC
    }

    private function getBatasWaktuColumn()
    {
        return 'N'; // Kolom batas waktu
    }

    /**
     * Setup sheet Umum jika belum ada
     */
    private function setupUmumSheet($sheet)
    {
        // Setup header untuk sheet Umum
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Indikator');
        $sheet->setCellValue('C1', 'Satuan');
        $sheet->setCellValue('D1', 'Target TW1');
        $sheet->setCellValue('E1', 'Target TW2');
        $sheet->setCellValue('F1', 'Target TW3');
        $sheet->setCellValue('G1', 'Target TW4');
        $sheet->setCellValue('H1', 'Realisasi TW1');
        $sheet->setCellValue('I1', 'Realisasi TW2');
        $sheet->setCellValue('J1', 'Realisasi TW3');
        $sheet->setCellValue('K1', 'Realisasi TW4');
        $sheet->setCellValue('L1', 'Kendala');
        $sheet->setCellValue('M1', 'Solusi');
        $sheet->setCellValue('N1', 'Tindak Lanjut');
        $sheet->setCellValue('O1', 'PIC');
        $sheet->setCellValue('P1', 'Batas Waktu');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];

        $sheet->getStyle('A1:P1')->applyFromArray($headerStyle);
    }

    /**
     * Test download functionality by generating sample files and saving to storage/public
     */
    public function testDownload(Fra $fra, $triwulan = null, $format = 'excel')
    {
        try {
            if ($triwulan) {
                // Test triwulan download
                $fileName = "Test_FRA_{$fra->tahun_berjalan}_TW{$triwulan}";

                if ($format === 'excel') {
                    // Generate test Excel file
                    $filePath = $this->generateTestExcelTriwulan($fra, $triwulan, $fileName);
                    return response()->json([
                        'success' => true,
                        'message' => 'Test Excel TW ' . $triwulan . ' berhasil dibuat',
                        'file_path' => $filePath,
                        'download_url' => asset('storage/' . basename($filePath))
                    ]);
                } else {
                    // Generate test PDF file (simplified)
                    return response()->json([
                        'success' => true,
                        'message' => 'Test PDF TW ' . $triwulan . ' - fungsi akan dikembangkan lebih lanjut',
                        'note' => 'PDF generation memerlukan view template yang sesuai'
                    ]);
                }
            } else {
                // Test lengkap download
                $fileName = "Test_FRA_{$fra->tahun_berjalan}_Lengkap";

                if ($format === 'excel') {
                    $filePath = $this->generateTestExcelLengkap($fra, $fileName);
                    return response()->json([
                        'success' => true,
                        'message' => 'Test Excel FRA Lengkap berhasil dibuat',
                        'file_path' => $filePath,
                        'download_url' => asset('storage/' . basename($filePath))
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Test PDF FRA Lengkap - fungsi akan dikembangkan lebih lanjut',
                        'note' => 'PDF generation memerlukan view template yang sesuai'
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Test download error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate test Excel for triwulan
     */
    private function generateTestExcelTriwulan($fra, $triwulan, $fileName)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Test TW {$triwulan}");

        // Header
        $sheet->setCellValue('A1', 'Test FRA Triwulan ' . $triwulan);
        $sheet->setCellValue('A2', 'Tahun: ' . $fra->tahun_berjalan);
        $sheet->setCellValue('A3', 'Generated: ' . now()->format('d/m/Y H:i:s'));

        // Sample data headers
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Indikator');
        $sheet->setCellValue('C5', 'Satuan');
        $sheet->setCellValue('D5', 'Target');
        $sheet->setCellValue('E5', 'Realisasi');
        $sheet->setCellValue('F5', 'Capkin Kumulatif');
        $sheet->setCellValue('G5', 'Capkin Setahun');

        // Sample data
        for ($i = 1; $i <= 5; $i++) {
            $row = 5 + $i;
            $sheet->setCellValue('A' . $row, $i);
            $sheet->setCellValue('B' . $row, "Sample Indikator {$i}");
            $sheet->setCellValue('C' . $row, 'Persen');
            $sheet->setCellValue('D' . $row, rand(70, 100));
            $sheet->setCellValue('E' . $row, rand(60, 90));
            $sheet->setCellValue('F' . $row, rand(50, 80));
            $sheet->setCellValue('G' . $row, rand(40, 70));
        }

        // Style header
        $sheet->getStyle('A5:G5')->getFont()->setBold(true);
        $sheet->getStyle('A5:G5')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E3F2FD');

        // Auto width
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save to storage/public
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filePath = storage_path('app/public/' . $fileName . '.xlsx');
        $writer->save($filePath);

        return $filePath;
    }

    /**
     * Generate test Excel lengkap
     */
    private function generateTestExcelLengkap($fra, $fileName)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Test FRA Lengkap');

        // Header
        $sheet->setCellValue('A1', 'Test FRA Lengkap');
        $sheet->setCellValue('A2', 'Tahun: ' . $fra->tahun_berjalan);
        $sheet->setCellValue('A3', 'Generated: ' . now()->format('d/m/Y H:i:s'));

        // Headers
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Indikator');
        $sheet->setCellValue('C5', 'Satuan');
        $sheet->setCellValue('D5', 'TW1');
        $sheet->setCellValue('E5', 'TW2');
        $sheet->setCellValue('F5', 'TW3');
        $sheet->setCellValue('G5', 'TW4');
        $sheet->setCellValue('H5', 'Kendala');
        $sheet->setCellValue('I5', 'Solusi');

        // Sample data untuk semua triwulan
        for ($i = 1; $i <= 5; $i++) {
            $row = 5 + $i;
            $sheet->setCellValue('A' . $row, $i);
            $sheet->setCellValue('B' . $row, "Sample Indikator {$i}");
            $sheet->setCellValue('C' . $row, 'Persen');
            $sheet->setCellValue('D' . $row, rand(70, 100));
            $sheet->setCellValue('E' . $row, rand(75, 100));
            $sheet->setCellValue('F' . $row, rand(80, 100));
            $sheet->setCellValue('G' . $row, rand(85, 100));
            $sheet->setCellValue('H' . $row, "Sample kendala untuk indikator {$i}");
            $sheet->setCellValue('I' . $row, "Sample solusi untuk indikator {$i}");
        }

        // Style header
        $sheet->getStyle('A5:I5')->getFont()->setBold(true);
        $sheet->getStyle('A5:I5')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E3F2FD');

        // Auto width
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save to storage/public
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filePath = storage_path('app/public/' . $fileName . '.xlsx');
        $writer->save($filePath);

        return $filePath;
    }

    /**
     * Download comprehensive Excel with new blade template
     */
    public function downloadComprehensiveExcel(Fra $fra)
    {
        try {
            // Get all required data
            $matriksList = $fra->matriks_fra()->with(['template_fra.template_jenis'])->get();

            // Get all realisasi data for all triwulan
            $allRealisasiData = [];
            for ($tw = 1; $tw <= 4; $tw++) {
                $triwulanObj = \App\Models\Triwulan::where('fra_id', $fra->id)->where('nomor', $tw)->first();
                if ($triwulanObj) {
                    $allRealisasiData[$tw] = $fra->realisasi_fra()->where('triwulan_id', $triwulanObj->id)->get()->keyBy('matriks_fra_id');
                } else {
                    $allRealisasiData[$tw] = collect();
                }
            }

            // Prepare data for the blade template
            $data = [
                'fra' => $fra,
                'matriks_list' => $matriksList,
                'all_realisasi_data' => $allRealisasiData
            ];

            // Use the new excel blade template
            $output = view('excel.fra_comprehensive', compact('data'))->render();

            // The blade template handles the excel generation and download
            return response($output);
        } catch (\Exception $e) {
            Log::error('Error downloading comprehensive Excel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunduh Excel comprehensive: ' . $e->getMessage());
        }
    }
}
