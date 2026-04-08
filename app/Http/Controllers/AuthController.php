<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Pengguna;
use App\Models\Target_Fra;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Mail\ResetPasswordMail;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Handle proses login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // If already authenticated, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari pengguna
        $pengguna = Pengguna::where('email', $request->email)->first();

        if ($pengguna) {
            // Periksa apakah password perlu dimigrasi (jika format lama)
            if (substr($pengguna->password, 0, 4) !== '$2y$') {
                // Jika password masih plain text, update ke bcrypt
                if ($pengguna->password === $request->password) {
                    $pengguna->password = Hash::make($request->password);
                    $pengguna->save();
                    Auth::login($pengguna);
                    return redirect()->intended(route('dashboard'));
                }
            } else {
                // Password sudah dalam format bcrypt, gunakan pengecekan normal
                if (Hash::check($request->password, $pengguna->password)) {
                    Auth::login($pengguna);
                    return redirect()->intended(route('dashboard'));
                }
            }
        }

        // Login gagal
        return back()->withErrors(['email' => 'Email atau password tidak valid'])->withInput($request->only('email'));
    }

    /**
     * Handle proses logout
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function masuk()
    {
        $user = Auth::user();
        $currentYear = date('Y');
        $currentMonth = date('m');
        
        // Cache dashboard statistics for 1 minute to improve real-time responsiveness
        $cacheKey = "dashboard_stats_{$currentYear}_{$currentMonth}";
        $dashboardData = Cache::remember($cacheKey, 60, function() use ($currentYear, $currentMonth) {
            
            // ===== PERHITUNGAN PROGRESS SAKIP BERDASARKAN DOKUMEN YANG DIUPLOAD =====
            
            // 1. PERENCANAAN KINERJA
            // Hitung progress Perencanaan Kinerja berdasarkan logika dashboard_detail
            $perencanaan_total_progress = 0;
            $perencanaan_total_items = 0;
            
            // Sub komponen Perencanaan Kinerja
            $perencanaan_sub_komponen = ['Manajemen Renstra', 'Manajemen RKT', 'Manajemen PK'];
            
            foreach ($perencanaan_sub_komponen as $subKomponen) {
                if ($subKomponen === 'Manajemen PK') {
                    // Handle Manajemen PK dengan form input target PK
                    $kegiatan = \App\Models\Kegiatan::whereHas('sub_komponen', function($query) use ($subKomponen) {
                        $query->where('sub_komponen', 'like', '%' . $subKomponen . '%');
                    })
                    ->where('tahun_berjalan', $currentYear)
                    ->with(['sub_komponen', 'buktiDukung', 'dokumenKegiatan'])
                    ->get();
                    
                    // Cek apakah Target PK sudah ada untuk tahun ini
                    $targetPkExists = \App\Models\Target_Pk::whereHas('kegiatan', function($query) use ($currentYear) {
                        $query->where('tahun_berjalan', $currentYear);
                    })->exists();
                    
                    // Tambahkan form input target PK berdasarkan status sebenarnya
                    $perencanaan_total_progress += $targetPkExists ? 100 : 0;
                    $perencanaan_total_items++;
                    
                    // Tambahkan kegiatan normal
                    foreach ($kegiatan as $item) {
                        $requiredDocuments = $this->getRequiredDocuments($item);
                        $uploadedCount = $this->getUploadedDocumentCount($item, $requiredDocuments);
                        $totalRequired = count($requiredDocuments);
                        
                        $progress = $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 0;
                        $perencanaan_total_progress += $progress;
                        $perencanaan_total_items++;
                    }
                } else {
                    // Handle sub komponen lainnya
                    $kegiatan = \App\Models\Kegiatan::whereHas('sub_komponen', function($query) use ($subKomponen) {
                        $query->where('sub_komponen', 'like', '%' . $subKomponen . '%');
                    })
                    ->where('tahun_berjalan', $currentYear)
                    ->with(['sub_komponen', 'buktiDukung', 'dokumenKegiatan'])
                    ->get();
                    
                    foreach ($kegiatan as $item) {
                        $requiredDocuments = $this->getRequiredDocuments($item);
                        $uploadedCount = $this->getUploadedDocumentCount($item, $requiredDocuments);
                        $totalRequired = count($requiredDocuments);
                        
                        $progress = $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 0;
                        $perencanaan_total_progress += $progress;
                        $perencanaan_total_items++;
                    }
                }
            }
            
            // 2. PENGUKURAN KINERJA
            // Hitung progress Pengukuran Kinerja berdasarkan logika dashboard_detail
            $pengukuran_total_progress = 0;
            $pengukuran_total_items = 0;
            
            // Sub komponen Pengukuran Kinerja
            $pengukuran_sub_komponen = ['FRA', 'SK Tim SAKIP', 'SKP', 'Reward & Punishment', 'Form Rencana Aksi'];
            
            foreach ($pengukuran_sub_komponen as $subKomponen) {
                if ($subKomponen === 'Reward & Punishment') {
                    // Handle Reward & Punishment khusus
                    $kegiatan = \App\Models\Kegiatan::whereHas('sub_komponen', function($query) {
                        $query->where('sub_komponen', 'like', '%Reward%')
                              ->where('sub_komponen', 'like', '%Punishment%');
                    })
                    ->where('tahun_berjalan', $currentYear)
                    ->with(['sub_komponen', 'buktiDukung', 'dokumenKegiatan'])
                    ->first();
                    
                    if ($kegiatan) {
                        // Penetapan Mekanisme (1 item)
                        $penetapanDoc = \App\Models\Bukti_Dukung::where('kegiatan_id', $kegiatan->id)
                            ->where('jenis', 'penetapan_mekanisme')
                            ->first();
                        $pengukuran_total_progress += $penetapanDoc ? 100 : 0;
                        $pengukuran_total_items++;
                        
                        // Triwulan 1-4 (4 items)
                        $dokumenTypes = ['reward_tw', 'punishment_tw', 'laporan_tw'];
                        for ($tw = 1; $tw <= 4; $tw++) {
                            $uploadedCount = 0;
                            foreach ($dokumenTypes as $type) {
                                $jenisWithTriwulan = str_replace('_tw', '_triwulan_' . $tw, $type);
                                $doc = \App\Models\Bukti_Dukung::where('kegiatan_id', $kegiatan->id)
                                    ->where('jenis', $jenisWithTriwulan)
                                    ->first();
                                if ($doc) $uploadedCount++;
                            }
                            $totalRequired = count($dokumenTypes);
                            $progress = $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 0;
                            $pengukuran_total_progress += $progress;
                            $pengukuran_total_items++;
                        }
                    }
                } elseif ($subKomponen === 'Form Rencana Aksi') {
                    // Handle Form Rencana Aksi khusus
                    // Monitoring Capaian Kinerja (4 items untuk 4 triwulan)
                    $monitoringKegiatan = \App\Models\Kegiatan::where('nama_kegiatan', 'like', '%Monitoring Capaian Kinerja%')
                        ->where('tahun_berjalan', $currentYear)
                        ->with(['sub_komponen', 'buktiDukung', 'dokumenKegiatan'])
                        ->first();
                    
                    if ($monitoringKegiatan) {
                        $dokumenTypes = ['notulensi_triwulan', 'surat_undangan_triwulan', 'daftar_hadir_triwulan', 'fra_triwulan'];
                        for ($tw = 1; $tw <= 4; $tw++) {
                            $uploadedCount = 0;
                            foreach ($dokumenTypes as $type) {
                                $jenisWithTriwulan = $type . '_' . $tw;
                                $doc = \App\Models\Bukti_Dukung::where('kegiatan_id', $monitoringKegiatan->id)
                                    ->where('jenis', $jenisWithTriwulan)
                                    ->first();
                                if ($doc) $uploadedCount++;
                            }
                            $totalRequired = count($dokumenTypes);
                            $progress = $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 0;
                            $pengukuran_total_progress += $progress;
                            $pengukuran_total_items++;
                        }
                    }
                    
                    // Form items (Input Target FRA + Input Realisasi FRA per triwulan)
                    $fra = \App\Models\Fra::where('tahun_berjalan', $currentYear)->first();
                    
                    // Hitung progress Target FRA berdasarkan field yang terisi
                    $targetFraProgress = $this->calculateTargetFraProgress($currentYear);
                    
                    // Input Target FRA berdasarkan progress sebenarnya
                    $pengukuran_total_progress += $targetFraProgress['progress'];
                    $pengukuran_total_items++;
                    
                    // Input Realisasi FRA per triwulan
                    if ($fra && $fra->triwulans()->exists()) {
                        $triwulans = $fra->triwulans()->orderBy('nomor')->get();
                        foreach ($triwulans as $triwulan) {
                            $hasRealisasi = $triwulan->realisasi_fra()->exists();
                            $pengukuran_total_progress += $hasRealisasi ? 100 : 0;
                            $pengukuran_total_items++;
                        }
                    }
                } else {
                    // Handle sub komponen lainnya
                    $kegiatan = \App\Models\Kegiatan::whereHas('sub_komponen', function($query) use ($subKomponen) {
                        $query->where('sub_komponen', 'like', '%' . $subKomponen . '%');
                    })
                    ->where('tahun_berjalan', $currentYear)
                    ->with(['sub_komponen', 'buktiDukung', 'dokumenKegiatan'])
                    ->get();
                    
                    foreach ($kegiatan as $item) {
                        $requiredDocuments = $this->getRequiredDocuments($item);
                        $uploadedCount = $this->getUploadedDocumentCount($item, $requiredDocuments);
                        $totalRequired = count($requiredDocuments);
                        
                        $progress = $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 0;
                        $pengukuran_total_progress += $progress;
                        $pengukuran_total_items++;
                    }
                }
            }
            
            // 3. PELAPORAN KINERJA
            // Hitung progress Pelaporan Kinerja berdasarkan logika dashboard_detail
            $pelaporan_total_progress = 0;
            $pelaporan_total_items = 0;
            
            // Sub komponen Pelaporan Kinerja
            $pelaporan_sub_komponen = ['Manajemen Lakin', 'Generate Link'];
            
            foreach ($pelaporan_sub_komponen as $subKomponen) {
                if ($subKomponen === 'Manajemen Lakin') {
                    $kegiatan = \App\Models\Kegiatan::whereHas('sub_komponen', function($query) {
                        $query->where('sub_komponen', 'like', '%Manajemen Lakin%')
                              ->orWhere('sub_komponen', 'like', '%Lakin%');
                    })
                    ->where('nama_kegiatan', 'not like', '%monitoring capaian kinerja%')
                    ->where('tahun_berjalan', $currentYear)
                    ->with(['sub_komponen', 'buktiDukung', 'dokumenKegiatan'])
                    ->get();
                } else {
                    $kegiatan = \App\Models\Kegiatan::whereHas('sub_komponen', function($query) use ($subKomponen) {
                        $query->where('sub_komponen', 'like', '%' . $subKomponen . '%');
                    })
                    ->where('tahun_berjalan', $currentYear)
                    ->with(['sub_komponen', 'buktiDukung', 'dokumenKegiatan'])
                    ->get();
                }
                
                foreach ($kegiatan as $item) {
                    $requiredDocuments = $this->getRequiredDocuments($item);
                    $uploadedCount = $this->getUploadedDocumentCount($item, $requiredDocuments);
                    $totalRequired = count($requiredDocuments);
                    
                    $progress = $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 0;
                    $pelaporan_total_progress += $progress;
                    $pelaporan_total_items++;
                }
            }
            
            // Calculate percentages
            $perencanaan_progress = $perencanaan_total_items > 0 ? round($perencanaan_total_progress / $perencanaan_total_items) : 0;
            $pengukuran_progress = $pengukuran_total_items > 0 ? round($pengukuran_total_progress / $pengukuran_total_items) : 0;
            $pelaporan_progress = $pelaporan_total_items > 0 ? round($pelaporan_total_progress / $pelaporan_total_items) : 0;
            
            // Overall SAKIP progress - gabungan dari ketiga komponen
            $overall_progress = round(($perencanaan_progress + $pengukuran_progress + $pelaporan_progress) / 3);
            
            // Komponen selesai
            $komponen_selesai = 0;
            if ($perencanaan_progress == 100) $komponen_selesai++;
            if ($pengukuran_progress == 100) $komponen_selesai++;
            if ($pelaporan_progress == 100) $komponen_selesai++;
            
            // ===== DEADLINE TERDEKAT (YANG BELUM LEWAT) =====
            $now = Carbon::now();
            
            // Cari kegiatan dengan tanggal selesai terdekat (belum lewat)
            $upcoming_kegiatan = \App\Models\Kegiatan::where('tahun_berjalan', $currentYear)
                ->where('tanggal_berakhir', '>=', $now)
                ->orderBy('tanggal_berakhir', 'asc')
                ->first();
            
            // Cari FRA triwulan dengan deadline terdekat (belum lewat)
            $upcoming_fra = \App\Models\Triwulan::whereHas('fra', function($q) use ($currentYear) {
                    $q->where('tahun_berjalan', $currentYear);
                })
                ->where('tanggal_selesai', '>=', $now)
                ->where('status', '!=', 'Selesai')
                ->orderBy('tanggal_selesai', 'asc')
                ->first();
            
            // Cari SKP dengan deadline terdekat (akhir bulan untuk SKP bulanan, akhir tahun untuk SKP tahunan)
            $upcoming_skp_bulanan = null;
            $upcoming_skp_tahunan = null;
            
            // SKP Bulanan - deadline akhir bulan berjalan
            $endOfCurrentMonth = Carbon::now()->endOfMonth();
            if ($endOfCurrentMonth >= $now) {
                $skp_kegiatan = \App\Models\Kegiatan::where('tahun_berjalan', $currentYear)
                    ->where('nama_kegiatan', 'like', '%Sasaran Kinerja Pegawai%')
                    ->first();
                if ($skp_kegiatan) {
                    $upcoming_skp_bulanan = (object) [
                        'nama_kegiatan' => 'SKP Bulanan ' . $now->format('F Y'),
                        'tanggal_berakhir' => $endOfCurrentMonth->format('Y-m-d'),
                        'type' => 'skp_bulanan'
                    ];
                }
            }
            
            // SKP Tahunan - deadline akhir tahun
            $endOfYear = Carbon::create($currentYear, 12, 31);
            if ($endOfYear >= $now) {
                $skp_kegiatan = \App\Models\Kegiatan::where('tahun_berjalan', $currentYear)
                    ->where('nama_kegiatan', 'like', '%Sasaran Kinerja Pegawai%')
                    ->first();
                if ($skp_kegiatan) {
                    $upcoming_skp_tahunan = (object) [
                        'nama_kegiatan' => 'SKP Tahunan ' . $currentYear,
                        'tanggal_berakhir' => $endOfYear->format('Y-m-d'),
                        'type' => 'skp_tahunan'
                    ];
                }
            }
            
            // Kumpulkan semua deadline dan cari yang terdekat
            $deadlines = collect();
            
            if ($upcoming_kegiatan) {
                $deadlines->push((object) [
                    'text' => $upcoming_kegiatan->nama_kegiatan,
                    'date' => Carbon::parse($upcoming_kegiatan->tanggal_berakhir),
                    'type' => 'kegiatan'
                ]);
            }
            
            if ($upcoming_fra) {
                $deadlines->push((object) [
                    'text' => 'FRA TW ' . $upcoming_fra->nomor,
                    'date' => Carbon::parse($upcoming_fra->tanggal_selesai),
                    'type' => 'fra'
                ]);
            }
            
            if ($upcoming_skp_bulanan) {
                $deadlines->push((object) [
                    'text' => $upcoming_skp_bulanan->nama_kegiatan,
                    'date' => Carbon::parse($upcoming_skp_bulanan->tanggal_berakhir),
                    'type' => 'skp_bulanan'
                ]);
            }
            
            if ($upcoming_skp_tahunan) {
                $deadlines->push((object) [
                    'text' => $upcoming_skp_tahunan->nama_kegiatan,
                    'date' => Carbon::parse($upcoming_skp_tahunan->tanggal_berakhir),
                    'type' => 'skp_tahunan'
                ]);
            }
            
            // Tentukan deadline terdekat
            $deadline_text = 'Tidak ada deadline';
            $deadline_date = 'N/A';
            
            if ($deadlines->isNotEmpty()) {
                $nearest = $deadlines->sortBy('date')->first();
                $deadline_text = $nearest->text;
                $deadline_date = 'Tenggat ' . $nearest->date->format('d M Y');
            }
            
            // ===== KEGIATAN BULAN INI (TAHUNAN, TRIWULANAN, DAN SKP) =====
            
            // Kegiatan tahunan dengan deadline bulan ini
            $activities_this_month = \App\Models\Kegiatan::where('tahun_berjalan', $currentYear)
                ->whereMonth('tanggal_berakhir', $currentMonth)
                ->whereYear('tanggal_berakhir', $currentYear)
                ->orderBy('tanggal_berakhir', 'asc')
                ->get();
            
            // FRA triwulan dengan deadline bulan ini
            $fra_this_month = \App\Models\Triwulan::whereHas('fra', function($q) use ($currentYear) {
                    $q->where('tahun_berjalan', $currentYear);
                })
                ->whereMonth('tanggal_selesai', $currentMonth)
                ->whereYear('tanggal_selesai', $currentYear)
                ->orderBy('tanggal_selesai', 'asc')
                ->get()
                ->map(function($triwulan) {
                    return (object) [
                        'nama_triwulan' => 'TW ' . $triwulan->nomor,
                        'tanggal_selesai' => $triwulan->tanggal_selesai,
                        'type' => 'fra'
                    ];
                });
            
            // SKP dengan deadline bulan ini
            $skp_this_month = collect();
            
            // SKP Bulanan - deadline akhir bulan berjalan
            $endOfCurrentMonth = Carbon::now()->endOfMonth();
            $skp_kegiatan = \App\Models\Kegiatan::where('tahun_berjalan', $currentYear)
                ->where('nama_kegiatan', 'like', '%Sasaran Kinerja Pegawai%')
                ->first();
            
            if ($skp_kegiatan) {
                $skp_this_month->push([
                    'nama_triwulan' => 'SKP Bulanan ' . $now->format('F Y'),
                    'tanggal_selesai' => $endOfCurrentMonth->format('Y-m-d'),
                    'type' => 'skp_bulanan'
                ]);
                
                // SKP Tahunan - jika bulan Desember
                if ($currentMonth == 12) {
                    $skp_this_month->push([
                        'nama_triwulan' => 'SKP Tahunan ' . $currentYear,
                        'tanggal_selesai' => Carbon::create($currentYear, 12, 31)->format('Y-m-d'),
                        'type' => 'skp_tahunan'
                    ]);
                }
            }
            
            // Gabungkan FRA dan SKP untuk timeline bulan ini
            $fra_this_month = $fra_this_month->concat($skp_this_month);
            
            return [
                'overall_progress' => $overall_progress,
                'komponen_selesai' => $komponen_selesai,
                'perencanaan_progress' => $perencanaan_progress,
                'pengukuran_progress' => $pengukuran_progress,
                'pelaporan_progress' => $pelaporan_progress,
                'deadline_text' => $deadline_text,
                'deadline_date' => $deadline_date,
                'fra_this_month' => $fra_this_month,
                'activities_this_month' => $activities_this_month,
            ];
        });
        
        return view('dashboard', array_merge(['user' => $user], $dashboardData));
    }
    
    /**
     * Get required documents based on kegiatan type
     */
    private function getRequiredDocuments($kegiatan)
    {
        $baseDocuments = [
            'notulensi' => 'Notulensi',
            'surat_undangan' => 'Surat Undangan',
            'daftar_hadir' => 'Daftar Hadir'
        ];

        // Jika kegiatan adalah PK, tambahkan dokumen PK
        if (stripos($kegiatan->nama_kegiatan, 'Perjanjian Kinerja') !== false ||
            stripos($kegiatan->nama_kegiatan, 'PK') !== false) {
            $pkDocuments = [
                'dokumen_pk' => 'Dokumen PK',
                'notulensi' => 'Notulensi',
                'surat_undangan' => 'Surat Undangan',
                'daftar_hadir' => 'Daftar Hadir'
            ];
            return $pkDocuments;
        }

        // Jika kegiatan adalah SKP, tambahkan requirement khusus
        if (stripos($kegiatan->nama_kegiatan, 'SKP') !== false ||
            stripos($kegiatan->nama_kegiatan, 'Sasaran Kinerja') !== false) {

            $skpDocuments = [];
            for ($i = 1; $i <= 12; $i++) {
                $namaBulan = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                $skpDocuments["skp_bulanan_{$i}"] = "SKP Bulanan {$namaBulan[$i]}";
            }
            $skpDocuments['skp_tahunan'] = 'SKP Tahunan';
            return $skpDocuments;
        }

        return $baseDocuments;
    }
    
    /**
     * Count uploaded documents
     */
    private function getUploadedDocumentCount($kegiatan, $requiredDocuments)
    {
        $uploaded = 0;

        foreach ($requiredDocuments as $key => $label) {
            if ($key === 'dokumen_pk') {
                // Cek apakah dokumen PK sudah diupload
                if ($kegiatan->dokumenKegiatan && $kegiatan->dokumenKegiatan->isNotEmpty()) {
                    $uploaded++;
                }
            } elseif (strpos($key, 'skp_bulanan_') === 0) {
                $bulan = str_replace('skp_bulanan_', '', $key);
                $namaBulan = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                $jenisToCheck = "SKP Bulanan {$namaBulan[$bulan]}";
            } elseif ($key === 'skp_tahunan') {
                $jenisToCheck = 'SKP Tahunan';
            } else {
                $jenisToCheck = ucfirst(str_replace('_', ' ', $key));
                if ($key === 'surat_undangan') $jenisToCheck = 'Surat Undangan';
                if ($key === 'daftar_hadir') $jenisToCheck = 'Daftar Hadir';
            }

            if (isset($jenisToCheck) && $kegiatan->buktiDukung->where('jenis', $jenisToCheck)->isNotEmpty()) {
                $uploaded++;
            }
        }

        return $uploaded;
    }

    /**
     * Show dashboard detail with sub components and activities
     *
     * @param string $komponen
     * @return \Illuminate\View\View
     */
    public function dashboardDetail($komponen = 'perencanaan')
    {
        $currentYear = date('Y');
        
        // Mapping komponen ke sub komponen
        $komponenMapping = [
            'perencanaan' => [
                'title' => 'Perencanaan Kinerja',
                'sub_komponen' => ['Manajemen Renstra', 'Manajemen RKT', 'Manajemen PK']
            ],
            'pengukuran' => [
                'title' => 'Pengukuran Kinerja',
                'sub_komponen' => ['FRA', 'SK Tim SAKIP', 'SKP', 'Reward & Punishment', 'Form Rencana Aksi']
            ],
            'pelaporan' => [
                'title' => 'Pelaporan Kinerja',
                'sub_komponen' => ['Manajemen Lakin', 'Generate Link']
            ]
        ];
        
        // Validasi komponen
        if (!isset($komponenMapping[$komponen])) {
            $komponen = 'perencanaan';
        }
        
        $komponenData = $komponenMapping[$komponen];
        
        // Ambil data kegiatan berdasarkan komponen
        $kegiatanData = [];
        
        foreach ($komponenData['sub_komponen'] as $subKomponen) {

            
            // Handle Reward & Punishment khusus
            if ($subKomponen === 'Reward & Punishment') {
                // Ambil kegiatan Reward & Punishment dari database
                $kegiatan = \App\Models\Kegiatan::whereHas('sub_komponen', function($query) {
                    $query->where('sub_komponen', 'like', '%Reward%')
                          ->where('sub_komponen', 'like', '%Punishment%');
                })
                ->where('tahun_berjalan', $currentYear)
                ->with(['sub_komponen', 'buktiDukung', 'dokumenKegiatan'])
                ->first(); // Ambil satu kegiatan utama
                
                if ($kegiatan) {
                    // Buat struktur seperti reward_punishment_detail.blade.php
                    // 1 dokumen penetapan mekanisme + 3 dokumen per triwulan (4 triwulan)
                    $rewardPunishmentItems = [];
                    
                    // Penetapan Mekanisme (1 dokumen)
                    $penetapanDoc = \App\Models\Bukti_Dukung::where('kegiatan_id', $kegiatan->id)
                        ->where('jenis', 'penetapan_mekanisme')
                        ->first();
                    
                    $rewardPunishmentItems[] = [
                        'id' => $kegiatan->id . '_penetapan',
                        'nama_kegiatan' => 'Reward & Punishment (Penetapan Mekanisme)',
                        'tanggal_berakhir' => $kegiatan->tanggal_berakhir,
                        'status' => $penetapanDoc ? 'SUDAH DIUNGGAH' : 'BELUM DIUNGGAH',
                        'uploaded_count' => $penetapanDoc ? 1 : 0,
                        'total_required' => 1,
                        'progress_percentage' => $penetapanDoc ? 100 : 0,
                        'is_form' => false
                    ];
                    
                    // Triwulan 1-4 (masing-masing 3 dokumen)
                    $dokumenTypes = [
                        'reward_tw' => 'Reward',
                        'punishment_tw' => 'Punishment', 
                        'laporan_tw' => 'Laporan'
                    ];
                    
                    for ($tw = 1; $tw <= 4; $tw++) {
                        $uploadedCount = 0;
                        foreach ($dokumenTypes as $type => $label) {
                            // Gunakan pola jenis dengan suffix triwulan seperti yang digunakan di sistem
                            $jenisWithTriwulan = str_replace('_tw', '_triwulan_' . $tw, $type);
                            $doc = \App\Models\Bukti_Dukung::where('kegiatan_id', $kegiatan->id)
                                ->where('jenis', $jenisWithTriwulan)
                                ->first();
                            if ($doc) $uploadedCount++;
                        }
                        
                        $totalRequired = count($dokumenTypes);
                        $status = 'BELUM DIUNGGAH';
                        if ($uploadedCount > 0 && $uploadedCount < $totalRequired) {
                            $status = 'SEBAGIAN DIUNGGAH';
                        } elseif ($uploadedCount == $totalRequired) {
                            $status = 'SUDAH DIUNGGAH';
                        }
                        
                        // Tentukan tanggal berakhir per triwulan
                        $triwulanEndDates = [
                            1 => $currentYear . '-03-31',
                            2 => $currentYear . '-06-30', 
                            3 => $currentYear . '-09-30',
                            4 => $currentYear . '-12-31'
                        ];
                        
                        $rewardPunishmentItems[] = [
                            'id' => $kegiatan->id . '_tw' . $tw,
                            'nama_kegiatan' => "Reward & Punishment Triwulan {$tw}",
                            'tanggal_berakhir' => $triwulanEndDates[$tw],
                            'status' => $status,
                            'uploaded_count' => $uploadedCount,
                            'total_required' => $totalRequired,
                            'progress_percentage' => $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 0,
                            'is_form' => false
                        ];
                    }
                    
                    $kegiatanData[$subKomponen] = collect($rewardPunishmentItems);
                }
                continue;
            }
            
            // Handle Monitoring Capaian Kinerja khusus (struktur per triwulan seperti Reward & Punishment)
            if ($subKomponen === 'Form Rencana Aksi') {
                // Cek apakah ada kegiatan monitoring capaian kinerja
                $monitoringKegiatan = \App\Models\Kegiatan::where('nama_kegiatan', 'like', '%Monitoring Capaian Kinerja%')
                    ->where('tahun_berjalan', $currentYear)
                    ->with(['sub_komponen', 'buktiDukung', 'dokumenKegiatan'])
                    ->first();
                
                if ($monitoringKegiatan) {
                    // Buat struktur per triwulan untuk monitoring capaian kinerja
                    $monitoringItems = [];
                    
                    // Triwulan 1-4 (masing-masing 4 dokumen: notulensi, surat undangan, daftar hadir, fra)
                    $dokumenTypes = [
                        'notulensi_triwulan' => 'Notulensi',
                        'surat_undangan_triwulan' => 'Surat Undangan',
                        'daftar_hadir_triwulan' => 'Daftar Hadir',
                        'fra_triwulan' => 'FRA'
                    ];
                    
                    for ($tw = 1; $tw <= 4; $tw++) {
                        $uploadedCount = 0;
                        foreach ($dokumenTypes as $type => $label) {
                            // Gunakan pola jenis dengan suffix nomor triwulan
                            $jenisWithTriwulan = $type . '_' . $tw;
                            $doc = \App\Models\Bukti_Dukung::where('kegiatan_id', $monitoringKegiatan->id)
                                ->where('jenis', $jenisWithTriwulan)
                                ->first();
                            if ($doc) $uploadedCount++;
                        }
                        
                        $totalRequired = count($dokumenTypes);
                        $status = 'BELUM DIUNGGAH';
                        if ($uploadedCount > 0 && $uploadedCount < $totalRequired) {
                            $status = 'SEBAGIAN DIUNGGAH';
                        } elseif ($uploadedCount == $totalRequired) {
                            $status = 'SUDAH DIUNGGAH';
                        }
                        
                        // Tentukan tanggal berakhir per triwulan
                        $triwulanEndDates = [
                            1 => $currentYear . '-03-31',
                            2 => $currentYear . '-06-30', 
                            3 => $currentYear . '-09-30',
                            4 => $currentYear . '-12-31'
                        ];
                        
                        $monitoringItems[] = [
                            'id' => $monitoringKegiatan->id . '_tw' . $tw,
                            'nama_kegiatan' => "Monitoring Capaian Kinerja Triwulan {$tw}",
                            'tanggal_berakhir' => $triwulanEndDates[$tw],
                            'status' => $status,
                            'uploaded_count' => $uploadedCount,
                            'total_required' => $totalRequired,
                            'progress_percentage' => $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 0,
                            'is_form' => false
                        ];
                    }
                    
                    // Tambahkan monitoring items ke collection
                    if (!empty($monitoringItems)) {
                        $kegiatanData['Monitoring Capaian Kinerja'] = collect($monitoringItems);
                    }
                }
                
                // Lanjutkan dengan form rencana aksi yang sudah ada
                $currentDate = now();
                // Ambil FRA untuk tahun berjalan untuk mengecek status triwulan
                $fra = \App\Models\Fra::where('tahun_berjalan', $currentYear)->first();
                
                // Hitung progress Target FRA berdasarkan field yang terisi
                $targetFraProgress = $this->calculateTargetFraProgress($currentYear);
                $fraStatus = $targetFraProgress['status'];
                $fraProgress = $targetFraProgress['progress'];
                
                $formItems = [
                    [
                        'id' => 'fra_target',
                        'nama_kegiatan' => 'Input Target FRA',
                        'tanggal_berakhir' => $currentYear . '-03-31',
                        'status' => $fraStatus,
                        'uploaded_count' => $targetFraProgress['filled_count'],
                        'total_required' => $targetFraProgress['total_count'],
                        'progress_percentage' => $targetFraProgress['progress'],
                        'is_form' => true,
                        'is_expired' => $currentDate->gt($currentYear . '-03-31')
                    ]
                ];
                
                // Tambahkan FRA realisasi berdasarkan triwulan yang ada di database
                if ($fra && $fra->triwulans()->exists()) {
                    $triwulans = $fra->triwulans()->orderBy('nomor')->get();
                    
                    foreach ($triwulans as $triwulan) {
                        // Update status triwulan terlebih dahulu
                        $triwulan->updateStatus();
                        
                        // Tentukan apakah triwulan sudah expired (status 'Terlambat')
                        $isExpired = $triwulan->status === 'Terlambat';
                        
                        // Hitung progress Realisasi FRA berdasarkan field yang terisi
                        $realisasiProgress = $this->calculateRealisasiFraProgress($triwulan->id);
                        
                        $formItems[] = [
                            'id' => 'fra_realisasi_tw' . $triwulan->nomor,
                            'nama_kegiatan' => 'Input Realisasi FRA Triwulan ' . $triwulan->nomor,
                            'tanggal_berakhir' => $triwulan->tanggal_selesai->format('Y-m-d'),
                            'status' => $realisasiProgress['status'],
                            'uploaded_count' => $realisasiProgress['filled_count'],
                            'total_required' => $realisasiProgress['total_count'],
                            'progress_percentage' => $realisasiProgress['progress'],
                            'is_form' => true,
                            'is_expired' => $isExpired
                        ];
                    }
                }
                $kegiatanData[$subKomponen] = collect($formItems);
                continue;
            }
            
            // Handle Manajemen PK khusus
            if ($subKomponen === 'Manajemen PK') {
                $currentDate = now();
                
                // Ambil kegiatan normal
                $kegiatan = \App\Models\Kegiatan::whereHas('sub_komponen', function($query) use ($subKomponen) {
                    $query->where('sub_komponen', 'like', '%' . $subKomponen . '%');
                })
                ->where('tahun_berjalan', $currentYear)
                ->with(['sub_komponen', 'buktiDukung', 'dokumenKegiatan'])
                ->get();
                
                // Hitung progress Target PK berdasarkan field yang terisi
                $targetPkProgress = $this->calculateTargetPkProgress($currentYear);
                
                // Cari kegiatan PK untuk mendapatkan deadline yang benar
                $pkKegiatan = \App\Models\Kegiatan::where('tahun_berjalan', $currentYear)
                    ->where(function($query) {
                        $query->where('nama_kegiatan', 'like', '%Pengukuran Kinerja%')
                              ->orWhere('nama_kegiatan', 'like', '%Manajemen PK%')
                              ->orWhereHas('sub_komponen', function($subQuery) {
                                  $subQuery->where('sub_komponen', 'like', '%Manajemen PK%');
                              });
                    })
                    ->first();
                
                // Tentukan deadline - gunakan deadline dari kegiatan PK jika ada, fallback ke 28 Feb
                $deadline = $pkKegiatan ? $pkKegiatan->tanggal_berakhir : $currentYear . '-02-28';
                
                // Tambahkan form input target PK
                $formItems = [
                    [
                        'id' => 'pk_target',
                        'nama_kegiatan' => 'Input Target PK',
                        'tanggal_berakhir' => $deadline,
                        'status' => $targetPkProgress['status'],
                        'uploaded_count' => $targetPkProgress['filled_count'],
                        'total_required' => $targetPkProgress['total_count'],
                        'progress_percentage' => $targetPkProgress['progress'],
                        'is_form' => true,
                        'is_expired' => $currentDate->gt($deadline)
                    ]
                ];
                
                // Gabungkan kegiatan normal dengan form items
                $kegiatanWithStatus = $kegiatan->map(function($item) {
                    $requiredDocuments = $this->getRequiredDocuments($item);
                    $uploadedCount = $this->getUploadedDocumentCount($item, $requiredDocuments);
                    $totalRequired = count($requiredDocuments);
                    
                    $status = 'BELUM DIUNGGAH';
                    if ($uploadedCount > 0 && $uploadedCount < $totalRequired) {
                        $status = 'SEBAGIAN DIUNGGAH';
                    } elseif ($uploadedCount == $totalRequired && $totalRequired > 0) {
                        $status = 'SUDAH DIUNGGAH';
                    }
                    
                    return [
                        'id' => $item->id,
                        'nama_kegiatan' => $item->nama_kegiatan,
                        'tanggal_berakhir' => $item->tanggal_berakhir,
                        'status' => $status,
                        'uploaded_count' => $uploadedCount,
                        'total_required' => $totalRequired,
                        'progress_percentage' => $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 0,
                        'is_form' => false
                    ];
                });
                
                $allItems = collect($formItems)->merge($kegiatanWithStatus);
                if ($allItems->isNotEmpty()) {
                    $kegiatanData[$subKomponen] = $allItems;
                }
                continue;
            }
            
            // Perbaiki untuk Manajemen Lakin - pastikan monitoring capaian kinerja tidak masuk ke sini
            if ($subKomponen === 'Manajemen Lakin') {
                $kegiatan = \App\Models\Kegiatan::whereHas('sub_komponen', function($query) {
                    $query->where('sub_komponen', 'like', '%Manajemen Lakin%')
                          ->orWhere('sub_komponen', 'like', '%Lakin%');
                })
                ->where('nama_kegiatan', 'not like', '%monitoring capaian kinerja%')
                ->where('tahun_berjalan', $currentYear)
                ->with(['sub_komponen', 'buktiDukung', 'dokumenKegiatan'])
                ->get();
            } else {
                // Ambil kegiatan berdasarkan sub komponen
                $kegiatan = \App\Models\Kegiatan::whereHas('sub_komponen', function($query) use ($subKomponen) {
                    $query->where('sub_komponen', 'like', '%' . $subKomponen . '%');
                })
                ->where('tahun_berjalan', $currentYear)
                ->with(['sub_komponen', 'buktiDukung', 'dokumenKegiatan'])
                ->get();
            }
            
            // Hitung status upload untuk setiap kegiatan
            $kegiatanWithStatus = $kegiatan->map(function($item) {
                $requiredDocuments = $this->getRequiredDocuments($item);
                $uploadedCount = $this->getUploadedDocumentCount($item, $requiredDocuments);
                $totalRequired = count($requiredDocuments);
                
                $status = 'BELUM DIUNGGAH';
                if ($uploadedCount > 0 && $uploadedCount < $totalRequired) {
                    $status = 'SEBAGIAN DIUNGGAH';
                } elseif ($uploadedCount == $totalRequired && $totalRequired > 0) {
                    $status = 'SUDAH DIUNGGAH';
                }
                
                return [
                    'id' => $item->id,
                    'nama_kegiatan' => $item->nama_kegiatan,
                    'tanggal_berakhir' => $item->tanggal_berakhir,
                    'status' => $status,
                    'uploaded_count' => $uploadedCount,
                    'total_required' => $totalRequired,
                    'progress_percentage' => $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 0,
                    'is_form' => false
                ];
            });
            
            if ($kegiatanWithStatus->isNotEmpty()) {
                $kegiatanData[$subKomponen] = $kegiatanWithStatus;
            }
        }
        
        return view('dashboard_detail', [
            'komponen' => $komponen,
            'komponen_title' => $komponenData['title'],
            'sub_komponen_list' => $komponenData['sub_komponen'],
            'kegiatan_data' => $kegiatanData
        ]);
    }
    
    /**
     * Hitung progress Target PK berdasarkan field yang terisi
     */
    private function calculateTargetPkProgress($currentYear)
    {
        // Ambil semua matriks FRA yang memerlukan target PK untuk tahun ini
        $totalMatriks = \App\Models\Matriks_Fra::whereHas('template_fra.fra', function($query) use ($currentYear) {
            $query->where('tahun_berjalan', $currentYear);
        })->count();
        
        if ($totalMatriks == 0) {
            return [
                'status' => 'BELUM INPUT',
                'progress' => 0,
                'filled_count' => 0,
                'total_count' => 0
            ];
        }
        
        // Hitung berapa banyak target PK yang sudah diisi
        $filledTargets = \App\Models\Target_Pk::whereHas('kegiatan', function($query) use ($currentYear) {
            $query->where('tahun_berjalan', $currentYear);
        })
        ->whereNotNull('target_pk')
        ->where('target_pk', '!=', '')
        ->where('target_pk', '!=', 0)
        ->count();
        
        $progress = $totalMatriks > 0 ? round(($filledTargets / $totalMatriks) * 100) : 0;
        
        $status = 'BELUM INPUT';
        if ($filledTargets > 0 && $filledTargets < $totalMatriks) {
            $status = 'SEBAGIAN INPUT';
        } elseif ($filledTargets == $totalMatriks && $totalMatriks > 0) {
            $status = 'SUDAH INPUT';
        }
        
        return [
            'status' => $status,
            'progress' => $progress,
            'filled_count' => $filledTargets,
            'total_count' => $totalMatriks
        ];
    }
    
    /**
     * Hitung progress Target FRA berdasarkan field yang terisi
     * Mengikuti logika yang sama dengan form_target_fra.blade.php
     */
    private function calculateTargetFraProgress($currentYear)
    {
        // Ambil semua matriks FRA yang memerlukan target untuk tahun ini
        $totalMatriks = \App\Models\Matriks_Fra::whereHas('template_fra.fra', function($query) use ($currentYear) {
            $query->where('tahun_berjalan', $currentYear);
        })->count();
        
        if ($totalMatriks == 0) {
            return [
                'status' => 'BELUM INPUT',
                'progress' => 0,
                'filled_count' => 0,
                'total_count' => 0
            ];
        }
        
        // Hitung total field input yang diperlukan (4 triwulan per matriks)
        $totalInputFields = $totalMatriks * 4;
        
        // Hitung berapa banyak field target yang sudah diisi
        $filledFields = 0;
        
        $targetFras = \App\Models\Target_Fra::whereHas('matriks_fra.template_fra.fra', function($query) use ($currentYear) {
            $query->where('tahun_berjalan', $currentYear);
        })->get();
        
        foreach ($targetFras as $target) {
            // Hitung field yang terisi untuk setiap target FRA (0 juga dianggap sebagai isian yang valid)
            if (!is_null($target->target_tw1) && $target->target_tw1 !== '') $filledFields++;
            if (!is_null($target->target_tw2) && $target->target_tw2 !== '') $filledFields++;
            if (!is_null($target->target_tw3) && $target->target_tw3 !== '') $filledFields++;
            if (!is_null($target->target_tw4) && $target->target_tw4 !== '') $filledFields++;
        }
        
        // Hitung progress berdasarkan field yang terisi (sama seperti di form)
        $progress = $totalInputFields > 0 ? round(($filledFields / $totalInputFields) * 100) : 0;
        
        $status = 'BELUM INPUT';
        if ($filledFields > 0 && $filledFields < $totalInputFields) {
            $status = 'SEBAGIAN INPUT';
        } elseif ($filledFields == $totalInputFields && $totalInputFields > 0) {
            $status = 'SUDAH INPUT';
        }
        
        return [
            'status' => $status,
            'progress' => $progress,
            'filled_count' => $filledFields,
            'total_count' => $totalInputFields
        ];
    }
    
    /**
     * Hitung progress Realisasi FRA berdasarkan field yang terisi
     * Mengikuti logika yang sama dengan form_realisasi_fra.blade.php
     */
    private function calculateRealisasiFraProgress($triwulanId)
    {
        // Ambil semua matriks FRA yang memerlukan realisasi untuk triwulan ini
        $triwulan = \App\Models\Triwulan::find($triwulanId);
        if (!$triwulan) {
            return [
                'status' => 'BELUM INPUT',
                'progress' => 0,
                'filled_count' => 0,
                'total_count' => 0
            ];
        }
        
        $totalMatriks = \App\Models\Matriks_Fra::whereHas('template_fra.fra', function($query) use ($triwulan) {
            $query->where('tahun_berjalan', $triwulan->fra->tahun_berjalan);
        })->count();
        
        if ($totalMatriks == 0) {
            return [
                'status' => 'BELUM INPUT',
                'progress' => 0,
                'filled_count' => 0,
                'total_count' => 0
            ];
        }
        
        // Hitung total field input yang diperlukan (4 field per matriks: realisasi, kendala, solusi, tindak_lanjut)
        $totalInputFields = $totalMatriks * 4;
        
        // Hitung berapa banyak field yang sudah diisi untuk triwulan ini
        $filledFields = 0;
        
        $realisasiFras = \App\Models\Realisasi_Fra::where('triwulan_id', $triwulanId)->get();
        
        foreach ($realisasiFras as $realisasi) {
            // Hitung field yang terisi untuk setiap realisasi FRA (0 juga dianggap sebagai isian yang valid)
            if (!is_null($realisasi->realisasi) && $realisasi->realisasi !== '') $filledFields++;
            if (!empty($realisasi->kendala) && trim($realisasi->kendala) !== '') $filledFields++;
            if (!empty($realisasi->solusi) && trim($realisasi->solusi) !== '') $filledFields++;
            if (!empty($realisasi->tindak_lanjut) && trim($realisasi->tindak_lanjut) !== '') $filledFields++;
        }
        
        // Hitung progress berdasarkan field yang terisi (sama seperti di form)
        $progress = $totalInputFields > 0 ? round(($filledFields / $totalInputFields) * 100) : 0;
        
        $status = 'BELUM INPUT';
        if ($filledFields > 0 && $filledFields < $totalInputFields) {
            $status = 'SEBAGIAN INPUT';
        } elseif ($filledFields == $totalInputFields && $totalInputFields > 0) {
            $status = 'SUDAH INPUT';
        }
        
        return [
             'status' => $status,
             'progress' => $progress,
             'filled_count' => $filledFields,
             'total_count' => $totalInputFields
         ];
     }
     
     /**
      * Clear dashboard cache manually
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearDashboardCache()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        $cacheKey = "dashboard_stats_{$currentYear}_{$currentMonth}";
        
        // Hapus cache
        Cache::forget($cacheKey);
        
        // Kembalikan respons sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Dashboard cache cleared successfully'
        ]);
    }

    /**
     * Send reset code to email
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:pengguna,email'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar dalam sistem'
        ]);

        try {
            // Get user data for personalization
            $user = \App\Models\Pengguna::where('email', $request->email)->first();
            
            // Generate 6 digit verification code
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store in session for verification
            session([
                'reset_email' => $request->email,
                'reset_code' => $verificationCode,
                'reset_code_expires' => now()->addMinutes(15),
                'step' => 'verify'
            ]);

            // Send email with verification code
            Mail::to($request->email)
                ->send(new ResetPasswordMail($verificationCode, $user->nama ?? null));
            
            // Log the email sending attempt
            Log::info('Reset password email sent', [
                'email' => $request->email,
                'timestamp' => now(),
                'code_expires' => now()->addMinutes(15)
            ]);
            
            return redirect()->route('lupa_password')
                ->with('success', 'Kode verifikasi telah dikirim ke email Anda. Silakan periksa inbox dan folder spam.');
                
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to send reset password email', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
            
            // Clear session data if email failed
            session()->forget(['reset_email', 'reset_code', 'reset_code_expires', 'step']);
            
            return redirect()->route('lupa_password')
                ->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi atau hubungi administrator.'])
                ->withInput();
        }
    }

    /**
     * Verify reset code and update password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed'
        ], [
            'verification_code.required' => 'Kode verifikasi wajib diisi',
            'verification_code.size' => 'Kode verifikasi harus 6 digit',
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai'
        ]);

        // Check if session data exists
        if (!session('reset_email') || !session('reset_code') || !session('reset_code_expires')) {
            return redirect()->route('lupa_password')
                ->withErrors(['verification_code' => 'Sesi telah berakhir. Silakan mulai ulang proses reset password.']);
        }

        // Check if code is expired
        if (now()->gt(session('reset_code_expires'))) {
            session()->forget(['reset_email', 'reset_code', 'reset_code_expires', 'step']);
            return redirect()->route('lupa_password')
                ->withErrors(['verification_code' => 'Kode verifikasi telah kedaluwarsa. Silakan minta kode baru.']);
        }

        // Verify email and code
        if ($request->email !== session('reset_email') || $request->verification_code !== session('reset_code')) {
            return redirect()->route('lupa_password')
                ->withErrors(['verification_code' => 'Kode verifikasi tidak valid.'])
                ->with('step', 'verify');
        }

        // Update password
        $user = \App\Models\Pengguna::where('email', $request->email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Clear session data
            session()->forget(['reset_email', 'reset_code', 'reset_code_expires', 'step']);

            return redirect()->route('login')
                ->with('success', 'Password berhasil diubah. Silakan login dengan password baru.');
        }

        return redirect()->route('lupa_password')
            ->withErrors(['email' => 'Terjadi kesalahan. Silakan coba lagi.']);
    }
}