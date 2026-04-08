<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Kegiatan;
use App\Models\Sub_Komponen;
use App\Models\Renstra;
use App\Models\Target_Pk;
use App\Models\Matriks_Fra;
use App\Models\Bukti_Dukung;
use App\Services\GoogleDriveOAuthService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\Triwulan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengguna;
use App\Models\Skp;

class KegiatanController extends Controller
{

    /**
     * Menampilkan halaman manajemen Renstra
     *
     * @return \Illuminate\View\View
     */
    public function manajemenRenstra(Request $request)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        $isSuperAdmin = Auth::check() && $user->isSuperAdmin();

        // Mengambil semua data yang relevan dalam satu query jika memungkinkan, atau sebagai collection
        $reviuRenstra = $this->getKegiatanData('Reviu Renstra', ['Target']);
        $reviuTargetRenstra = $this->getKegiatanData('Reviu Target Renstra');
        $capaianTargetRenstra = $this->getKegiatanData('Capaian Target Renstra');
        $renstraDocs = Renstra::orderBy('periode_awal', 'desc')->get()->map(function ($item) {
            $startYear = date('Y', strtotime($item->periode_awal));
            $endYear = date('Y', strtotime($item->periode_akhir));
            return (object) [
                'id' => $item->id,
                'nama_kegiatan' => $item->nama_renstra,
                'tahun_berjalan' => $startYear . '-' . $endYear,
                'tanggal_mulai' => $item->periode_awal,
                'tanggal_berakhir' => $item->periode_akhir,
                'keterangan' => 'Bukti dukung Renstra periode ' . $startYear . '-' . $endYear,
                'status' => 'Aktif', // Logika status bisa disesuaikan
                'status_class' => 'bg-green-100 text-green-800',
                'status_dot' => 'bg-green-500',
                'type' => 'renstra_document',
                'dokumenKegiatan' => $item->dokumenKegiatan,
            ];
        });

        // Gabungkan semua menjadi satu collection
        $allActivitiesCollection = collect()
            ->merge($renstraDocs)
            ->merge($reviuRenstra)
            ->merge($reviuTargetRenstra)
            ->merge($capaianTargetRenstra)
            ->sortByDesc('tanggal_berakhir'); // Urutkan berdasarkan tanggal

        // Ambil parameter pagination dari request
        $perPage = $request->input('per_page', 10); // Default 10 untuk tab selain 'Semua'
        $perPageAllActivities = 9; // Fix to 9 for 'Semua' tab
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $allActivitiesCollection->slice(($currentPage - 1) * $perPageAllActivities, $perPageAllActivities)->all();
        $allActivities = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $allActivitiesCollection->count(),
            $perPageAllActivities,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'page',
                'query' => ['per_page_all' => $perPageAllActivities] // Use a different query parameter for 'allActivities'
            ]
        );

        // Pagination untuk tab Renstra
        $renstra = Renstra::orderBy('periode_awal', 'desc')->paginate($perPage, ['*'], 'renstra_page');

        // Pagination untuk Reviu Renstra
        $reviuRenstraItems = collect($reviuRenstra);
        $reviuRenstraCollection = new \Illuminate\Pagination\LengthAwarePaginator(
            $reviuRenstraItems->slice(($currentPage - 1) * $perPage, $perPage),
            $reviuRenstraItems->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'reviu_renstra_page']
        );

        // Pagination untuk Reviu Target Renstra
        $reviuTargetItems = collect($reviuTargetRenstra);
        $reviuTargetCollection = new \Illuminate\Pagination\LengthAwarePaginator(
            $reviuTargetItems->slice(($currentPage - 1) * $perPage, $perPage),
            $reviuTargetItems->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'reviu_target_page']
        );

        // Pagination untuk Capaian Target Renstra
        $capaianTargetItems = collect($capaianTargetRenstra);
        $capaianTargetCollection = new \Illuminate\Pagination\LengthAwarePaginator(
            $capaianTargetItems->slice(($currentPage - 1) * $perPage, $perPage),
            $capaianTargetItems->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'capaian_target_page']
        );

        $currentYear = date('Y');
        $activeRenstra = Renstra::whereYear('periode_awal', '<=', $currentYear)
            ->whereYear('periode_akhir', '>=', $currentYear)
            ->orderBy('periode_akhir', 'desc')
            ->first() ?? Renstra::orderBy('periode_akhir', 'desc')->first();

        $subKomponenList = Sub_Komponen::orderBy('sub_komponen', 'asc')->get();
        $manajemenRenstraSubKomponen = $subKomponenList->first(function ($item) {
            return stripos($item->sub_komponen, 'Manajemen Renstra') !== false ||
                (isset($item->nama_sub_komponen) && stripos($item->nama_sub_komponen, 'Manajemen Renstra') !== false);
        });

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Perencanaan Kinerja', 'url' => route('manajemen.renstra'), 'clickable' => true],
            ['title' => 'Manajemen Renstra', 'url' => route('manajemen.renstra'), 'clickable' => false],
        ];

        return view('perencanaan kinerja.manajemen_renstra', compact(
            'allActivities',
            'renstra',
            'reviuRenstraCollection',
            'reviuTargetCollection',
            'capaianTargetCollection',
            'activeRenstra',
            'subKomponenList',
            'manajemenRenstraSubKomponen',
            'perPage',
            'reviuRenstra',
            'reviuTargetRenstra',
            'capaianTargetRenstra',
            'isSuperAdmin',
            'breadcrumbs'
        ));
    }

    /**
     * Menampilkan halaman manajemen RKT
     *
     * @return \Illuminate\View\View
     */
    public function manajemenRKT(Request $request)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        $isSuperAdmin = Auth::check() && $user->isSuperAdmin();
        $isAdmin = Auth::check() && $user->isAdmin();

        // Mengambil data kegiatan RKT
        $perPage = $request->input('per_page', 10); // Default 10 items per page

        $daftar_kegiatan = Kegiatan::where('nama_kegiatan', 'like', '%Rencana Kinerja Tahunan%')
            ->orderBy('tahun_berjalan', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate($perPage)
            ->through(function ($kegiatan) {
                $currentDate = Carbon::now();
                $startDate = Carbon::parse($kegiatan->tanggal_mulai);
                $endDate = Carbon::parse($kegiatan->tanggal_berakhir);

                // Menentukan status
                $kegiatan->status = $this->tentukanStatus($currentDate, $startDate, $endDate);
                $kegiatan->status_class = $this->getStatusClass($kegiatan->status);
                $kegiatan->status_dot = $this->getStatusDot($kegiatan->status);

                // Menentukan kelengkapan dokumen
                $this->aturKelengkapan($kegiatan);

                return $kegiatan;
            });

        // Data statistik untuk tab ringkasan
        $totalKegiatan = $daftar_kegiatan->count();
        $openKegiatan = $daftar_kegiatan->where('status', 'Open')->count();
        $fulfillmentKegiatan = $daftar_kegiatan->where('status', 'Fulfillment')->count();
        $closedKegiatan = $daftar_kegiatan->where('status', 'Closed')->count();
        $lengkapKegiatan = $daftar_kegiatan->where('kelengkapan', 1)->count();
        $tidakLengkapKegiatan = $daftar_kegiatan->where('kelengkapan', 0)->count();

        // Mengambil daftar sub komponen untuk dropdown
        $subKomponenList = Sub_Komponen::orderBy('sub_komponen', 'asc')->get();

        // Debug: Log sub komponen yang tersedia
        Log::info('Sub Komponen Available:', $subKomponenList->toArray());

        // Cari sub komponen "Manajemen RKT" dengan berbagai kemungkinan nama kolom
        $manajemenRKTSubKomponen = $subKomponenList->first(function ($item) {
            return stripos($item->sub_komponen, 'Manajemen RKT') !== false ||
                (isset($item->nama_sub_komponen) && stripos($item->nama_sub_komponen, 'Manajemen RKT') !== false);
        });

        // Mendapatkan renstra aktif (untuk hidden input)
        $activeRenstra = Renstra::orderBy('periode_akhir', 'desc')->first();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Perencanaan Kinerja', 'url' => route('manajemen.renstra'), 'clickable' => true],
            ['title' => 'Manajemen RKT', 'url' => route('manajemen.rkt'), 'clickable' => false],
        ];

        return view('perencanaan kinerja.manajemen_rkt', compact(
            'daftar_kegiatan',
            'totalKegiatan',
            'openKegiatan',
            'fulfillmentKegiatan',
            'closedKegiatan',
            'lengkapKegiatan',
            'tidakLengkapKegiatan',
            'subKomponenList',
            'activeRenstra',
            'manajemenRKTSubKomponen',
            'isSuperAdmin',
            'isAdmin',
            'breadcrumbs' // Pass breadcrumbs to the view
        ));
    }

    /**
     * Menampilkan halaman manajemen PK
     *
     * @return \Illuminate\View\View
     */
    public function manajemenPK(Request $request)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        $isSuperAdmin = Auth::check() && $user->isSuperAdmin();
        $isAdmin = Auth::check() && $user->isAdmin();

        // Mengambil data kegiatan PK
        $perPage = $request->input('per_page', 10); // Default 10 items per page

        $daftar_kegiatan = Kegiatan::where('nama_kegiatan', 'like', '%Perjanjian Kinerja%')
            ->orderBy('tahun_berjalan', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate($perPage)
            ->through(function ($kegiatan) {
                $currentDate = Carbon::now();
                $startDate = Carbon::parse($kegiatan->tanggal_mulai);
                $endDate = Carbon::parse($kegiatan->tanggal_berakhir);

                // Menentukan status
                $kegiatan->status = $this->tentukanStatus($currentDate, $startDate, $endDate);
                $kegiatan->status_class = $this->getStatusClass($kegiatan->status);
                $kegiatan->status_dot = $this->getStatusDot($kegiatan->status);

                // Menentukan kelengkapan dokumen
                $this->aturKelengkapan($kegiatan);

                return $kegiatan;
            });

        // Data statistik untuk tab ringkasan
        $totalKegiatan = $daftar_kegiatan->count();
        $openKegiatan = $daftar_kegiatan->where('status', 'Open')->count();
        $fulfillmentKegiatan = $daftar_kegiatan->where('status', 'Fulfillment')->count();
        $closedKegiatan = $daftar_kegiatan->where('status', 'Closed')->count();
        $lengkapKegiatan = $daftar_kegiatan->where('kelengkapan', 1)->count();
        $tidakLengkapKegiatan = $daftar_kegiatan->where('kelengkapan', 0)->count();

        // Mengambil daftar sub komponen untuk dropdown
        $subKomponenList = Sub_Komponen::orderBy('sub_komponen', 'asc')->get();

        // Debug: Log sub komponen yang tersedia
        Log::info('Sub Komponen Available:', $subKomponenList->toArray());

        // Cari sub komponen "Manajemen Renstra" dengan berbagai kemungkinan nama kolom
        $manajemenPKSubKomponen = $subKomponenList->first(function ($item) {
            return stripos($item->sub_komponen, 'Manajemen PK') !== false ||
                (isset($item->nama_sub_komponen) && stripos($item->nama_sub_komponen, 'Manajemen PK') !== false);
        });

        // Mendapatkan renstra aktif (untuk hidden input)
        $activeRenstra = Renstra::orderBy('periode_akhir', 'desc')->first();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Perencanaan Kinerja', 'url' => route('manajemen.renstra'), 'clickable' => true],
            ['title' => 'Manajemen PK', 'url' => route('manajemen.pk'), 'clickable' => false],
        ];

        return view('perencanaan kinerja.manajemen_pk', compact(
            'daftar_kegiatan',
            'totalKegiatan',
            'openKegiatan',
            'fulfillmentKegiatan',
            'closedKegiatan',
            'lengkapKegiatan',
            'tidakLengkapKegiatan',
            'subKomponenList',
            'activeRenstra',
            'manajemenPKSubKomponen',
            'isSuperAdmin',
            'isAdmin',
            'breadcrumbs' // Pass breadcrumbs to the view
        ));
    }

    /**
     * Menampilkan halaman SK Tim SAKIP
     *
     * @return \Illuminate\View\View
     */
    public function SkTimSakip(Request $request)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        $isSuperAdmin = Auth::check() && $user->isSuperAdmin();

        // Mengambil data kegiatan SK Tim SAKIP
        $perPage = $request->input('per_page', 10); // Default 10 items per page

        $daftar_kegiatan = Kegiatan::where('nama_kegiatan', 'like', '%SK Tim SAKIP%')
            ->orderBy('tahun_berjalan', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate($perPage)
            ->through(function ($kegiatan) {
                $currentDate = Carbon::now();
                $startDate = Carbon::parse($kegiatan->tanggal_mulai);
                $endDate = Carbon::parse($kegiatan->tanggal_berakhir);

                // Menentukan status
                $kegiatan->status = $this->tentukanStatus($currentDate, $startDate, $endDate);
                $kegiatan->status_class = $this->getStatusClass($kegiatan->status);
                $kegiatan->status_dot = $this->getStatusDot($kegiatan->status);

                // Menentukan kelengkapan dokumen
                $this->aturKelengkapan($kegiatan);

                return $kegiatan;
            });

        // Data statistik untuk tab ringkasan
        $totalKegiatan = $daftar_kegiatan->count();
        $openKegiatan = $daftar_kegiatan->where('status', 'Open')->count();
        $fulfillmentKegiatan = $daftar_kegiatan->where('status', 'Fulfillment')->count();
        $closedKegiatan = $daftar_kegiatan->where('status', 'Closed')->count();
        $lengkapKegiatan = $daftar_kegiatan->where('kelengkapan', 1)->count();
        $tidakLengkapKegiatan = $daftar_kegiatan->where('kelengkapan', 0)->count();

        // Mengambil daftar sub komponen untuk dropdown
        $subKomponenList = Sub_Komponen::orderBy('sub_komponen', 'asc')->get();

        // Debug: Log sub komponen yang tersedia
        Log::info('Sub Komponen Available:', $subKomponenList->toArray());

        // Cari sub komponen "SK Tim SAKIP" dengan berbagai kemungkinan nama kolom
        $SkTimSakipSubKomponen = $subKomponenList->first(function ($item) {
            return stripos($item->sub_komponen, 'SK Tim SAKIP') !== false ||
                (isset($item->nama_sub_komponen) && stripos($item->nama_sub_komponen, 'SK Tim SAKIP') !== false);
        });

        // Mendapatkan renstra aktif (untuk hidden input)
        $activeRenstra = Renstra::orderBy('periode_akhir', 'desc')->first();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Pengukuran Kinerja', 'url' => route('manajemen.pk'), 'clickable' => true],
            ['title' => 'SK Tim SAKIP', 'url' => route('sk.tim.sakip'), 'clickable' => false],
        ];

        return view('pengukuran kinerja.sk_tim_sakip', compact(
            'daftar_kegiatan',
            'totalKegiatan',
            'openKegiatan',
            'fulfillmentKegiatan',
            'closedKegiatan',
            'lengkapKegiatan',
            'tidakLengkapKegiatan',
            'subKomponenList',
            'activeRenstra',
            'SkTimSakipSubKomponen',
            'isSuperAdmin',
            'breadcrumbs'
        ));
    }

    /**
     * Menampilkan halaman Reward Punishment
     *
     * @return \Illuminate\View\View
     */
    public function rewardPunishment(Request $request)
    {
        // Mengambil data kegiatan Reward Punishment
        $perPage = $request->input('per_page', 10); // Default 10 items per page

        $daftar_kegiatan = Kegiatan::where(function ($query) {
            $query->where('nama_kegiatan', 'like', '%Reward & Punishment%')
                ->orWhere('nama_kegiatan', 'like', '%Reward Punishment%');
        })
            ->orderBy('tahun_berjalan', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate($perPage)
            ->through(function ($kegiatan) {
                $currentDate = Carbon::now();
                $startDate = Carbon::parse($kegiatan->tanggal_mulai);
                $endDate = Carbon::parse($kegiatan->tanggal_berakhir);

                // Menentukan status
                $kegiatan->status = $this->tentukanStatus($currentDate, $startDate, $endDate);
                $kegiatan->status_class = $this->getStatusClass($kegiatan->status);
                $kegiatan->status_dot = $this->getStatusDot($kegiatan->status);

                // Menentukan kelengkapan dokumen
                $this->aturKelengkapan($kegiatan);

                return $kegiatan;
            });

        // Data statistik untuk tab ringkasan
        $totalKegiatan = $daftar_kegiatan->count();
        $openKegiatan = $daftar_kegiatan->where('status', 'Open')->count();
        $fulfillmentKegiatan = $daftar_kegiatan->where('status', 'Fulfillment')->count();
        $closedKegiatan = $daftar_kegiatan->where('status', 'Closed')->count();
        $lengkapKegiatan = $daftar_kegiatan->where('kelengkapan', 1)->count();
        $tidakLengkapKegiatan = $daftar_kegiatan->where('kelengkapan', 0)->count();

        // Mengambil daftar sub komponen untuk dropdown
        $subKomponenList = Sub_Komponen::orderBy('sub_komponen', 'asc')->get();

        // Debug: Log sub komponen yang tersedia
        Log::info('Sub Komponen Available:', $subKomponenList->toArray());

        // Cari sub komponen "Reward Punishment"
        $rewardPunishmentSubKomponen = $subKomponenList->first(function ($item) {
            return stripos($item->sub_komponen, 'Reward Punishment') !== false ||
                (isset($item->nama_sub_komponen) && stripos($item->nama_sub_komponen, 'Reward Punishment') !== false);
        });

        // Mendapatkan renstra aktif (untuk hidden input)
        $activeRenstra = Renstra::orderBy('periode_akhir', 'desc')->first();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Pengukuran Kinerja', 'url' => route('manajemen.pk'), 'clickable' => true],
            ['title' => 'Reward & Punishment', 'url' => route('reward.punishment'), 'clickable' => false],
        ];

        return view('pengukuran kinerja.reward_punishment', compact(
            'daftar_kegiatan',
            'totalKegiatan',
            'openKegiatan',
            'fulfillmentKegiatan',
            'closedKegiatan',
            'lengkapKegiatan',
            'tidakLengkapKegiatan',
            'subKomponenList',
            'activeRenstra',
            'rewardPunishmentSubKomponen',
            'breadcrumbs'
        ));
    }

    /**
     * Menampilkan detail Reward Punishment dengan struktur triwulan
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function rewardPunishmentDetail($id)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        $isSuperAdmin = Auth::check() && $user->isSuperAdmin();
        $isAdmin = Auth::check() && $user->isAdmin();
        
        $kegiatan = Kegiatan::with(['buktiDukung', 'sub_komponen', 'renstra'])->findOrFail($id);
        
        // Check if activity period has expired and user permissions
        $currentDate = Carbon::now();
        $endDate = Carbon::parse($kegiatan->tanggal_berakhir);
        $activityExpired = $currentDate->gt($endDate);
        
        // Upload logic:
        // 1. If activity expired: only Super Admin can upload
        // 2. If activity active: only Super Admin and Admin can upload
        // 3. Ketua Tim and Anggota Tim can only view (no upload/edit/delete)
        if ($activityExpired) {
            $canUpload = $isSuperAdmin;
        } else {
            $canUpload = $isSuperAdmin || $isAdmin;
        }

        // Cek apakah ini kegiatan Reward Punishment
        if (!str_contains(strtolower($kegiatan->nama_kegiatan), 'reward & punishment')) {
            return redirect()->back()->with('error', 'Kegiatan ini bukan kegiatan Reward Punishment');
        }

        // Ambil bukti dukung yang sudah diupload
        $buktiDukung = $kegiatan->buktiDukung;

        // Cek dokumen penetapan mekanisme
        $penetapanExists = $buktiDukung->where('jenis', 'penetapan_mekanisme')->first();

        // Organisir dokumen triwulan berdasarkan jenis dan triwulan
        $dokumenTriwulan = [];

        // Definisi jenis dokumen
        $jenisMap = [
            'sk_penerima_triwulan' => 'sk_penerima_triwulan',
            'piagam_penghargaan_triwulan' => 'piagam_penghargaan_triwulan',
            'rekap_pemilihan_triwulan' => 'rekap_pemilihan_triwulan'
        ];

        // Organisir dokumen berdasarkan triwulan dan jenis dengan validasi waktu
        $currentDate = Carbon::now();
        $currentYear = $kegiatan->tahun_berjalan;

        for ($triwulan = 1; $triwulan <= 4; $triwulan++) {
            $dokumenTriwulan[$triwulan] = [];

            // Tentukan periode triwulan
            $quarterInfo = $this->getQuarterPeriod($triwulan, $currentYear);
            $quarterStatus = $this->getQuarterStatus($quarterInfo['start'], $quarterInfo['end'], $currentDate);

            // Tambahkan informasi status periode ke array
            // Apply same logic as main activity for each quarter
            $quarterEndDate = Carbon::parse($quarterInfo['end']);
            $quarterExpired = $currentDate->gt($quarterEndDate);
            
            if ($quarterExpired) {
                $periodCanUpload = $isSuperAdmin;
            } else {
                $periodCanUpload = $isSuperAdmin || $isAdmin;
            }
            
            $dokumenTriwulan[$triwulan]['_period_info'] = [
                'start_date' => $quarterInfo['start'],
                'end_date' => $quarterInfo['end'],
                'status' => $quarterStatus,
                'can_upload' => $periodCanUpload
            ];

            foreach ($jenisMap as $jenisDB => $jenisKey) {
                // Cari dokumen berdasarkan pola jenis yang lebih sederhana
                $doc = $buktiDukung->filter(function ($item) use ($jenisDB, $triwulan) {
                    // Check if jenis matches the exact pattern: jenis_triwulan_X
                    return $item->jenis === "{$jenisDB}_{$triwulan}";
                })->first();

                if ($doc) {
                    $dokumenTriwulan[$triwulan][$jenisKey] = $doc;
                }
            }
        }

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Pengukuran Kinerja', 'url' => route('manajemen.pk'), 'clickable' => true],
            ['title' => 'Reward & Punishment', 'url' => route('reward.punishment'), 'clickable' => true],
            ['title' => 'Detail ' . $kegiatan->nama_kegiatan . ' ' . $kegiatan->tahun_berjalan, 'clickable' => false],
        ];

        return view('pengukuran kinerja.reward_punishment_detail', compact(
            'kegiatan',
            'dokumenTriwulan',
            'penetapanExists',
            'isSuperAdmin',
            'isAdmin',
            'canUpload',
            'activityExpired',
            'breadcrumbs'
        ));
    }

    /**
     * Menampilkan detail Capaian Kinerja dengan struktur triwulan
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function capaianKinerjaDetail($id)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        $isSuperAdmin = Auth::check() && $user->isSuperAdmin();
        $isAdmin = Auth::check() && $user->isAdmin();
        
        $kegiatan = Kegiatan::with(['buktiDukung', 'sub_komponen', 'renstra'])->findOrFail($id);
        
        // Check if activity period has expired and user permissions
        $currentDate = Carbon::now();
        $endDate = Carbon::parse($kegiatan->tanggal_berakhir);
        $activityExpired = $currentDate->gt($endDate);
        
        // Upload logic:
        // 1. If activity expired: only Super Admin can upload
        // 2. If activity active: only Super Admin and Admin can upload
        // 3. Ketua Tim and Anggota Tim can only view (no upload/edit/delete)
        if ($activityExpired) {
            $canUpload = $isSuperAdmin;
        } else {
            $canUpload = $isSuperAdmin || $isAdmin;
        }

        // Cek apakah ini kegiatan Capaian Kinerja
        if (!str_contains(strtolower($kegiatan->nama_kegiatan), 'capaian kinerja')) {
            return redirect()->back()->with('error', 'Kegiatan ini bukan kegiatan Capaian Kinerja');
        }

        // Ambil bukti dukung yang sudah diupload
        $buktiDukung = $kegiatan->buktiDukung;

        // Organisir dokumen triwulan berdasarkan jenis dan triwulan
        $dokumenTriwulan = [];

        // Definisi jenis dokumen untuk capaian kinerja
        $jenisMap = [
            'notulensi_triwulan' => 'notulensi_triwulan',
            'surat_undangan_triwulan' => 'surat_undangan_triwulan',
            'daftar_hadir_triwulan' => 'daftar_hadir_triwulan',
            'fra_triwulan' => 'fra_triwulan'
        ];

        // Organisir dokumen berdasarkan triwulan dan jenis dengan validasi waktu
        $currentDate = Carbon::now();

        // Untuk capaian kinerja, gunakan tahun kegiatan untuk periode upload
        $currentYear = $kegiatan->tahun_berjalan;

        // Determine current period status for overall activity
        $currentQuarter = ceil($currentDate->month / 3);
        $currentQuarterInfo = $this->getCapaianKinerjaQuarterPeriod($currentQuarter, $currentYear);
        $periodStatus = $this->getQuarterStatus($currentQuarterInfo['start'], $currentQuarterInfo['end'], $currentDate);

        for ($triwulan = 1; $triwulan <= 4; $triwulan++) {
            $dokumenTriwulan[$triwulan] = [];

            // Tentukan periode triwulan untuk capaian kinerja (periode upload di tahun berikutnya)
            $quarterInfo = $this->getCapaianKinerjaQuarterPeriod($triwulan, $currentYear);
            $quarterStatus = $this->getQuarterStatus($quarterInfo['start'], $quarterInfo['end'], $currentDate);

            // Tambahkan informasi status periode ke array
            $dokumenTriwulan[$triwulan]['_period_info'] = [
                'start_date' => $quarterInfo['start'],
                'end_date' => $quarterInfo['end'],
                'status' => $quarterStatus,
                'can_upload' => $quarterStatus === 'active'
            ];

            foreach ($jenisMap as $jenisDB => $jenisKey) {
                // Cari dokumen berdasarkan pola jenis yang disesuaikan untuk capaian kinerja
                $doc = $buktiDukung->filter(function ($item) use ($jenisDB, $triwulan) {
                    // Check if jenis matches the exact pattern: jenis_triwulan_X
                    return $item->jenis === "{$jenisDB}_{$triwulan}";
                })->first();

                if ($doc) {
                    $dokumenTriwulan[$triwulan][$jenisKey] = $doc;
                }
            }
        }

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Pengukuran Kinerja', 'url' => route('manajemen.pk'), 'clickable' => true],
            ['title' => 'Capaian Kinerja', 'url' => route('capaian.kinerja'), 'clickable' => true],
            ['title' => 'Detail Capaian Kinerja ' . $kegiatan->tahun_berjalan, 'clickable' => false],
        ];

        return view('pengukuran kinerja.capaian_kinerja_detail', compact(
            'kegiatan',
            'dokumenTriwulan',
            'isSuperAdmin',
            'isAdmin',
            'canUpload',
            'activityExpired',
            'periodStatus',
            'breadcrumbs'
        ));
    }



    /**
     * Menampilkan SKP
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function skp(Request $request)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();

        // Mengambil data kegiatan SKP
        $perPage = $request->input('per_page', 10); // Default 10 items per page

        $daftar_kegiatan = Kegiatan::where('nama_kegiatan', 'like', '%Sasaran Kinerja Pegawai%')
            ->orderBy('tahun_berjalan', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate($perPage)
            ->through(function ($kegiatan) {
                $currentDate = Carbon::now();
                $startDate = Carbon::parse($kegiatan->tanggal_mulai);
                $endDate = Carbon::parse($kegiatan->tanggal_berakhir);

                // Hitung persentase kelengkapan berdasarkan jumlah dokumen yang diupload
                $currentYear = date('Y');
                
                // Ambil data SKP dari tabel skps untuk kegiatan ini
                $skpData = \App\Models\Skp::where('kegiatan_id', $kegiatan->id)
                    ->where('tahun', $currentYear)
                    ->get();
                
                // Ambil jumlah total pegawai
                $totalPegawai = \App\Models\Pengguna::count();
                
                // Hitung total dokumen yang seharusnya diupload
                // Setiap pegawai harus upload: SKP bulanan (12 bulan) + SKP tahunan (1) = 13 dokumen
                $totalExpectedDocs = $totalPegawai * 13;
                
                // Hitung dokumen yang sudah diupload oleh seluruh user
                $uploadedBulananDocs = $skpData->where('jenis', 'bulanan')->count();
                $uploadedTahunanDocs = $skpData->where('jenis', 'tahunan')->count();
                $totalUploadedDocs = $uploadedBulananDocs + $uploadedTahunanDocs;
                
                // Hitung persentase kelengkapan
                $totalProgress = $totalExpectedDocs > 0 ? round(($totalUploadedDocs / $totalExpectedDocs) * 100, 1) : 0;
                
                // Status lengkap hanya ketika persentase sudah 100%
                $kegiatan->status_text = $totalProgress >= 100 ? 'Lengkap' : 'Tidak Lengkap';
                $kegiatan->status_class = $totalProgress >= 100 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                $kegiatan->total_progress = $totalProgress;

                return $kegiatan;
            });

        // Untuk modal tambah kegiatan
        $subKomponenList = Sub_Komponen::all();
        $skpSubKomponen = $subKomponenList->firstWhere('sub_komponen', 'like', '%SKP%');
        $activeRenstra = Renstra::whereYear('periode_awal', '<=', date('Y'))
                                ->whereYear('periode_akhir', '>=', date('Y'))
                                ->first(); // Get active renstra
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Pengukuran Kinerja', 'url' => route('manajemen.pk'), 'clickable' => true],
            ['title' => 'SKP', 'url' => route('skp'), 'clickable' => false],
        ];

        return view('pengukuran kinerja.skp', compact('daftar_kegiatan', 'subKomponenList', 'skpSubKomponen', 'activeRenstra', 'breadcrumbs'));
    }

    /**
     * Menampilkan detail SKP
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function skpDetail($id)
    {
        $kegiatan = Kegiatan::with('buktiDukung')->findOrFail($id);

        // Pastikan ini adalah kegiatan SKP
        if (stripos($kegiatan->nama_kegiatan, 'Sasaran Kinerja Pegawai') === false) {
            abort(404, 'Kegiatan SKP tidak ditemukan');
        }

        // Ambil data pegawai dari tabel pengguna
        $penggunas = \App\Models\Pengguna::all();
        $currentYear = date('Y');
        $currentUserId = Auth::id();

        // Ambil data SKP dari tabel skps untuk kegiatan dan tahun ini
        $skpData = \App\Models\Skp::where('kegiatan_id', $id)
            ->where('tahun', $currentYear)
            ->get();

        // Nama bulan untuk matching
        $bulanNama = [
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

        // Transform data pegawai untuk SKP detail dengan data dari tabel skps
        $pegawaiData = $penggunas->map(function ($pengguna) use ($skpData, $bulanNama) {
            // Ambil SKP bulanan untuk pegawai ini
            $userSkpBulanan = $skpData->where('user_id', $pengguna->id)
                ->where('jenis', 'bulanan');
            
            $completedMonths = $userSkpBulanan->pluck('bulan')->toArray();
            $monthCount = count($completedMonths);

            // Check SKP tahunan untuk pegawai ini
            $hasSkpTahunan = $skpData->where('user_id', $pengguna->id)
                ->where('jenis', 'tahunan')
                ->count() > 0;
            $tahunanStatus = $hasSkpTahunan ? 'lengkap' : 'belum';

            return (object) [
                'id' => $pengguna->id,
                'nama' => $pengguna->name,
                'nip' => $pengguna->nip,
                'bidang' => $pengguna->bidang,
                'jabatan' => $pengguna->jabatan,
                'jabatan_label' => $pengguna->jabatan_label,
                'skp_bulanan_count' => $monthCount,
                'skp_bulanan_data' => $completedMonths,
                'skp_bulanan_status' => $monthCount == 12 ? 'lengkap' : ($monthCount > 0 ? 'sebagian' : 'belum'),
                'skp_tahunan_status' => $tahunanStatus,
                'skp_documents' => $userSkpBulanan->merge($skpData->where('user_id', $pengguna->id)->where('jenis', 'tahunan')),
            ];
        });

        // Statistik berdasarkan data dari tabel skps
        $currentMonth = date('n'); // Bulan saat ini (1-12)
        $total_pegawai = $pegawaiData->count();
        
        // SKP Lengkap: pegawai yang sudah mengisi semua SKP bulanan hingga bulan berjalan
        $skp_lengkap = $pegawaiData->filter(function ($p) use ($currentMonth) {
            // Cek apakah sudah mengisi semua SKP bulanan hingga bulan berjalan
            $requiredMonths = range(1, $currentMonth);
            $completedMonths = $p->skp_bulanan_data;
            $hasAllRequiredMonths = count(array_intersect($requiredMonths, $completedMonths)) === count($requiredMonths);
            
            return $hasAllRequiredMonths;
        })->count();
        
        // SKP Belum Lengkap: pegawai yang belum mengisi semua SKP bulanan hingga bulan berjalan
        $skp_belum_lengkap = $total_pegawai - $skp_lengkap;

        // Progress keseluruhan berdasarkan data dari tabel skps
        $overall_progress = $pegawaiData->avg(function ($p) {
            $bulananProgress = ($p->skp_bulanan_count / 12) * 80; // 80% dari bulanan
            $tahunanProgress = ($p->skp_tahunan_status === 'lengkap' ? 1 : 0) * 20; // 20% dari tahunan
            return $bulananProgress + $tahunanProgress;
        });

        // Ambil SKP untuk user yang sedang login (untuk form upload)
        $currentUserSkpBulanan = $skpData->where('user_id', $currentUserId)
            ->where('jenis', 'bulanan')
            ->keyBy('bulan');
        
        $currentUserSkpTahunan = $skpData->where('user_id', $currentUserId)
            ->where('jenis', 'tahunan')
            ->first();

        // Tentukan view berdasarkan sumber request
        $viewName = request()->is('pengukuran-kinerja/skp/detail/*')
            ? 'pengukuran kinerja.skp_detail'
            : 'skp_detail';

        // Breadcrumbs akan diatur otomatis oleh BreadcrumbsServiceProvider

        return view($viewName, [
            'skp_info' => $kegiatan,
            'daftar_pegawai' => $pegawaiData,
            'total_pegawai' => $total_pegawai,
            'skp_lengkap' => $skp_lengkap,
            'skp_belum_lengkap' => $skp_belum_lengkap,
            'overall_progress' => round($overall_progress, 1),
            'skp_documents' => $skpData, // Data SKP dari tabel skps
            'uploaded_months' => $currentUserSkpBulanan->keys(), // Bulan yang sudah diupload user saat ini
            'has_yearly_skp' => $currentUserSkpTahunan ? true : false,
            'skpTahunan' => $currentUserSkpTahunan ? collect([$currentUserSkpTahunan]) : collect([]),
            'skpBulanan' => $currentUserSkpBulanan,
            'bulanNama' => $bulanNama,
            'currentYear' => $currentYear
        ]);
    }

    /**
     * Menampilkan halaman manajemen Lakin
     *
     * @return \Illuminate\View\View
     */
    public function manajemenLakin(Request $request)
    {
        // Mengambil data kegiatan Lakin - cari berdasarkan nama yang mengandung kata "Laporan Kinerja" atau "LAKIN"
        $perPage = $request->input('per_page', 10); // Default 10 items per page

        $query = Kegiatan::query();

        // Tambahkan kondisi OR untuk setiap keyword
        $query->where(function ($q) {
            $q->where('nama_kegiatan', 'like', '%Laporan Kinerja%')
                ->orWhere('nama_kegiatan', 'like', '%LAKIN%');
        });

        $daftar_kegiatan = $query->orderBy('tahun_berjalan', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate($perPage)
            ->through(function ($kegiatan) {
                $currentDate = Carbon::now();
                $startDate = Carbon::parse($kegiatan->tanggal_mulai);
                $endDate = Carbon::parse($kegiatan->tanggal_berakhir);

                // Menentukan status
                $kegiatan->status = $this->tentukanStatus($currentDate, $startDate, $endDate);
                $kegiatan->status_class = $this->getStatusClass($kegiatan->status);
                $kegiatan->status_dot = $this->getStatusDot($kegiatan->status);

                // Menentukan kelengkapan dokumen
                $this->aturKelengkapan($kegiatan);

                return $kegiatan;
            });

        // Data statistik untuk tab ringkasan
        $totalKegiatan = $daftar_kegiatan->count();
        $openKegiatan = $daftar_kegiatan->where('status', 'Open')->count();
        $fulfillmentKegiatan = $daftar_kegiatan->where('status', 'Fulfillment')->count();
        $closedKegiatan = $daftar_kegiatan->where('status', 'Closed')->count();
        $lengkapKegiatan = $daftar_kegiatan->where('kelengkapan', 1)->count();
        $tidakLengkapKegiatan = $daftar_kegiatan->where('kelengkapan', 0)->count();

        // Mengambil daftar sub komponen untuk dropdown
        $subKomponenList = Sub_Komponen::orderBy('sub_komponen', 'asc')->get();

        // Debug: Log sub komponen yang tersedia
        Log::info('Sub Komponen Available for LAKIN:', $subKomponenList->toArray());
        Log::info('LAKIN Kegiatan Found:', ['count' => $daftar_kegiatan->count(), 'items' => $daftar_kegiatan->toArray()]);

        // Cari sub komponen "Lakin"
        $lakinSubKomponen = $subKomponenList->first(function ($item) {
            return stripos($item->sub_komponen, 'Lakin') !== false ||
                stripos($item->sub_komponen, 'Laporan Kinerja') !== false ||
                (isset($item->nama_sub_komponen) && stripos($item->nama_sub_komponen, 'Lakin') !== false);
        });

        // Mendapatkan renstra aktif (untuk hidden input)
        $activeRenstra = Renstra::orderBy('periode_akhir', 'desc')->first();

        // Determine if the current user is a super admin
        $user = Auth::check() ? Pengguna::find(Auth::id()) : null;
        $isSuperAdmin = $user && $user->isSuperAdmin();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Pelaporan Kinerja', 'url' => route('manajemen.lakin'), 'clickable' => true],
            ['title' => 'Manajemen Lakin', 'url' => route('manajemen.lakin'), 'clickable' => false],
        ];

        return view('pelaporan kinerja.manajemen_lakin', compact(
            'daftar_kegiatan',
            'totalKegiatan',
            'openKegiatan',
            'fulfillmentKegiatan',
            'closedKegiatan',
            'lengkapKegiatan',
            'tidakLengkapKegiatan',
            'subKomponenList',
            'activeRenstra',
            'lakinSubKomponen',
            'isSuperAdmin',
            'breadcrumbs'
        ));
    }

    /**
     * Menyimpan kegiatan baru
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // dd($request->all()); // Debug: Cek data yang diterima
            // Debug: Log semua data yang diterima
            Log::info('Request Data Received:', $request->all());

            $validated = $request->validate([
                'nama_kegiatan' => 'required|string|max:255',
                'tahun_berjalan' => 'required|string|size:4',
                'tanggal_mulai' => 'required|date',
                'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
                'jenis_kegiatan' => 'required|in:reviu_renstra,reviu_target_renstra,capaian_target_renstra,rkt,pk,sk_tim_sakip,skp,reward_punishment,lakin',
                'sub_komponen_id' => 'required|exists:sub_komponen,id',
                'renstra_id' => 'required|exists:renstra,id',
            ], [
                'required' => 'Kolom :attribute harus diisi.',
                'string' => 'Kolom :attribute harus berupa teks.',
                'max' => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
                'date' => 'Kolom :attribute harus berupa tanggal yang valid.',
                'after_or_equal' => 'Kolom :attribute harus setelah atau sama dengan tanggal mulai.',
                'exists' => 'Data yang dipilih tidak valid.',
                'size' => 'Kolom :attribute harus tepat :size karakter.',
                'in' => 'Jenis kegiatan tidak valid.',
            ]);

            // Debug: Log data yang tervalidasi
            Log::info('Validated Data:', $validated);

            // Verifikasi sub_komponen_id exists
            $subKomponen = Sub_Komponen::find($validated['sub_komponen_id']);
            if (!$subKomponen) {
                Log::error('Sub Komponen not found with ID:', $validated['sub_komponen_id']);
                return back()->withInput()
                    ->with('error', 'Sub komponen tidak ditemukan. ID: ' . $validated['sub_komponen_id']);
            }

            // Verifikasi renstra_id exists
            $renstra = Renstra::find($validated['renstra_id']);
            if (!$renstra) {
                Log::error('Renstra not found with ID:', $validated['renstra_id']);
                return back()->withInput()
                    ->with('error', 'Renstra tidak ditemukan. ID: ' . $validated['renstra_id']);
            }

            // Mapping jenis_kegiatan ke nama_kegiatan yang sesuai
            $namaKegiatan = match ($validated['jenis_kegiatan']) {
                'reviu_renstra' => 'Reviu Renstra',
                'reviu_target_renstra' => 'Reviu Target Renstra',
                'capaian_target_renstra' => 'Capaian Target Renstra',
                'rkt' => 'Rencana Kinerja Tahunan',
                'pk' => 'Perjanjian Kinerja',
                'sk_tim_sakip' => 'SK Tim SAKIP',
                'skp' => 'Sasaran Kinerja Pegawai',
                'reward_punishment' => 'Reward Punishment',
                'lakin' => 'Laporan Kinerja',
                default => $validated['nama_kegiatan'],
            };

            $validated['nama_kegiatan'] = $namaKegiatan;

            // Cek apakah kegiatan sudah ada (duplikasi)
            if (Kegiatan::isDuplicate(
                $validated['nama_kegiatan'],
                $validated['tahun_berjalan'],
                $validated['sub_komponen_id'],
                $validated['renstra_id']
            )) {
                $existingKegiatan = Kegiatan::getDuplicate(
                    $validated['nama_kegiatan'],
                    $validated['tahun_berjalan'],
                    $validated['sub_komponen_id'],
                    $validated['renstra_id']
                );

                Log::warning('Duplicate kegiatan detected:', [
                    'nama_kegiatan' => $validated['nama_kegiatan'],
                    'tahun_berjalan' => $validated['tahun_berjalan'],
                    'sub_komponen' => $existingKegiatan->sub_komponen->sub_komponen ?? 'Unknown',
                    'renstra' => $existingKegiatan->renstra->nama_renstra ?? 'Unknown',
                    'existing_id' => $existingKegiatan->id,
                    'existing_dates' => $existingKegiatan->tanggal_mulai . ' - ' . $existingKegiatan->tanggal_berakhir
                ]);

                return back()->withInput()
                    ->with('error', 'Kegiatan "' . $validated['nama_kegiatan'] . '" tahun ' . $validated['tahun_berjalan'] .
                        ' pada sub komponen "' . ($existingKegiatan->sub_komponen->sub_komponen ?? 'Unknown') .
                        '" sudah ada. Tidak dapat membuat duplikasi kegiatan.');
            }

            // Tentukan redirect route berdasarkan jenis kegiatan
            $redirectRoute = match ($validated['jenis_kegiatan']) {
                'reviu_renstra', 'reviu_target_renstra', 'capaian_target_renstra' => 'manajemen.renstra',
                'rkt' => 'manajemen.rkt',
                'pk' => 'manajemen.pk',
                'sk_tim_sakip' => 'sk.tim.sakip',
                'skp' => 'skp',
                'reward_punishment' => 'reward.punishment',
                'lakin' => 'manajemen.lakin',
                default => 'manajemen.renstra',
            };

            // Debug: Log data yang akan disimpan
            Log::info('Data to be saved:', [
                'nama_kegiatan' => $validated['nama_kegiatan'],
                'tahun_berjalan' => $validated['tahun_berjalan'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_berakhir' => $validated['tanggal_berakhir'],
                'sub_komponen_id' => $validated['sub_komponen_id'],
                'renstra_id' => $validated['renstra_id'],
                'sub_komponen_name' => $subKomponen->sub_komponen,
                'renstra_name' => $renstra->nama_renstra,
            ]);

            // Buat kegiatan baru
            $kegiatan = Kegiatan::create([
                'nama_kegiatan' => $validated['nama_kegiatan'],
                'tahun_berjalan' => $validated['tahun_berjalan'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_berakhir' => $validated['tanggal_berakhir'],
                'sub_komponen_id' => $validated['sub_komponen_id'],
                'renstra_id' => $validated['renstra_id'],
            ]);

            // Auto-create Google Drive folder untuk kegiatan
            try {
                $googleDriveService = new GoogleDriveOAuthService();
                
                // Untuk kegiatan SKP, buat folder dengan subfolder bulanan dan tahunan
                if ($validated['jenis_kegiatan'] === 'skp') {
                    $folderResult = $googleDriveService->createSKPFolder(
                        $validated['nama_kegiatan'],
                        (int)$validated['tahun_berjalan']
                    );
                } else {
                    $folderResult = $googleDriveService->createKegiatanFolder(
                        $validated['nama_kegiatan'],
                        (int)$validated['tahun_berjalan']
                    );
                }

                if ($folderResult['success'] && isset($folderResult['folder_id'])) {
                    // Simpan folder_id ke database
                    $kegiatan->update(['folder_id' => $folderResult['folder_id']]);

                    Log::info('Google Drive folder auto-created for kegiatan', [
                        'kegiatan_id' => $kegiatan->id,
                        'folder_id' => $folderResult['folder_id'],
                        'folder_result' => $folderResult
                    ]);

                    $successMessage = "Kegiatan {$validated['nama_kegiatan']} {$validated['tahun_berjalan']} berhasil ditambahkan! " .
                        $folderResult['message'];
                } else {
                    Log::warning('Failed to auto-create Google Drive folder for kegiatan', [
                        'kegiatan_id' => $kegiatan->id,
                        'folder_result' => $folderResult
                    ]);

                    $successMessage = "Kegiatan {$validated['nama_kegiatan']} {$validated['tahun_berjalan']} berhasil ditambahkan! " .
                        "Namun folder Google Drive gagal dibuat: {$folderResult['message']}";
                }
            } catch (\Exception $e) {
                Log::error('Exception during Google Drive folder creation for kegiatan', [
                    'kegiatan_id' => $kegiatan->id,
                    'error' => $e->getMessage()
                ]);

                $successMessage = "Kegiatan {$validated['nama_kegiatan']} {$validated['tahun_berjalan']} berhasil ditambahkan! " .
                    "Namun folder Google Drive gagal dibuat: " . $e->getMessage();
            }

            return redirect()->route($redirectRoute)
                ->with('success', $successMessage);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error:', $e->errors());
            return back()->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Data yang dimasukkan tidak valid. Silakan periksa kembali.');
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan kegiatan:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        try {
            $kegiatan = Kegiatan::findOrFail($id);
            $kegiatan->update($validated);

            return redirect()->back()->with('success', 'Tanggal kegiatan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error("Failed to update Kegiatan dates for ID: {$id}. Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui tanggal kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $kegiatan = Kegiatan::findOrFail($id);
            $namaKegiatan = $kegiatan->nama_kegiatan;
            $tahun = $kegiatan->tahun_berjalan;
            $folderId = $kegiatan->folder_id;

            // Hapus semua bukti dukung yang terkait dengan kegiatan ini
            $buktiDukungList = \App\Models\Bukti_Dukung::where('kegiatan_id', $id)->get();
            $googleDriveService = new GoogleDriveOAuthService();
            
            foreach ($buktiDukungList as $buktiDukung) {
                if ($buktiDukung->google_drive_file_id) {
                    try {
                        $googleDriveService->moveToTrash($buktiDukung->google_drive_file_id);
                        Log::info("Successfully moved bukti dukung file to trash: {$buktiDukung->google_drive_file_id}");
                    } catch (\Exception $e) {
                        Log::error("Failed to move bukti dukung file to trash: {$buktiDukung->google_drive_file_id}. Error: " . $e->getMessage());
                    }
                }
                $buktiDukung->delete();
            }

            // Hapus semua dokumen kegiatan yang terkait
            $dokumenKegiatanList = \App\Models\Dokumen_Kegiatan::where('kegiatan_id', $id)->get();
            foreach ($dokumenKegiatanList as $dokumenKegiatan) {
                if ($dokumenKegiatan->file && str_starts_with($dokumenKegiatan->file, 'http')) {
                    try {
                        // Jika file disimpan di Google Drive, hapus dari sana
                        $googleDriveService->moveToTrash($dokumenKegiatan->file);
                        Log::info("Successfully moved dokumen kegiatan file to trash: {$dokumenKegiatan->file}");
                    } catch (\Exception $e) {
                        Log::error("Failed to move dokumen kegiatan file to trash: {$dokumenKegiatan->file}. Error: " . $e->getMessage());
                    }
                }
                $dokumenKegiatan->delete();
            }

            // Hapus folder di Google Drive jika ada
            if ($folderId) {
                try {
                    $googleDriveService->moveToTrash($folderId);
                    Log::info("Successfully moved Google Drive folder to trash for Kegiatan ID: {$id}, Folder ID: {$folderId}");
                } catch (\Exception $e) {
                    Log::error("Failed to move Google Drive folder to trash for Kegiatan ID: {$id}. Error: " . $e->getMessage());
                    // Lanjutkan proses hapus dari DB meskipun gagal hapus folder
                }
            }

            $kegiatan->delete();

            return redirect()->back()->with('success', "Kegiatan '{$namaKegiatan} ({$tahun})', folder, dan semua bukti dukung terkait telah berhasil dihapus (dipindahkan ke sampah di Google Drive).");
        } catch (\Exception $e) {
            Log::error("Failed to delete Kegiatan ID: {$id}. Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail kegiatan
     *
     * @param  int  $id
     * @param  int  $year
     * @return \Illuminate\View\View
     */
    public function detail($id, $year)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        $isSuperAdmin = Auth::check() && $user->isSuperAdmin();
        
        $kegiatan = Kegiatan::with([
            'buktiDukung' => function ($query) use ($id) {
                $query->where('kegiatan_id', $id);
            },
            'sub_komponen',
            'dokumenKegiatan',
            'renstra'
        ])
            ->where('id', $id)
            ->where('tahun_berjalan', $year)
            ->firstOrFail();

        $currentDate = Carbon::now();
        $startDate = Carbon::parse($kegiatan->tanggal_mulai);
        $endDate = Carbon::parse($kegiatan->tanggal_berakhir);

        $kegiatan->status = $this->tentukanStatus($currentDate, $startDate, $endDate);
        $kegiatan->status_class = $this->getStatusClass($kegiatan->status);
        $kegiatan->status_dot = $this->getStatusDot($kegiatan->status);

        $this->aturKelengkapan($kegiatan);

        $dokumenKegiatan = \App\Models\Dokumen_Kegiatan::where('kegiatan_id', $id)->get();
        $buktiDukungByJenis = $kegiatan->buktiDukung->groupBy('jenis');

        $requiredDocuments = $this->getRequiredDocuments($kegiatan);
        $uploadedCount = $this->getUploadedCount($kegiatan, $requiredDocuments);
        $totalRequired = count($requiredDocuments);

        $isPKDetail = $this->isPKKegiatan($kegiatan);

        // Check if activity period has expired and user permissions
        $activityExpired = $currentDate->gt($endDate);
        $isAdmin = Auth::check() && $user->isAdmin();
        
        // Upload logic:
        // 1. If activity expired: only Super Admin can upload
        // 2. If activity active: only Super Admin and Admin can upload
        // 3. Ketua Tim and Anggota Tim can only view (no upload/edit/delete)
        if ($activityExpired) {
            $canUpload = $isSuperAdmin;
        } else {
            $canUpload = $isSuperAdmin || $isAdmin;
        }
        
        $canEditDateRange = true;

        // Determine base route for the parent page
        $baseRoute = '';
        $baseTitle = '';
        $baseType = '';

        // Determine the type of the parent page (e.g., Manajemen PK, Manajemen RKT, etc.)
        if (stripos($kegiatan->nama_kegiatan, 'Perjanjian Kinerja') !== false) {
            $baseRoute = route('manajemen.pk');
            $baseTitle = 'Manajemen PK';
            $baseType = 'Sub Komponen';
        } elseif (stripos($kegiatan->nama_kegiatan, 'Rencana Kinerja Tahunan') !== false) {
            $baseRoute = route('manajemen.rkt');
            $baseTitle = 'Manajemen RKT';
            $baseType = 'Sub Komponen';
        } elseif (stripos($kegiatan->nama_kegiatan, 'SK Tim SAKIP') !== false) {
            $baseRoute = route('sk.tim.sakip');
            $baseTitle = 'SK Tim SAKIP';
            $baseType = 'Sub Komponen';
        } elseif (stripos($kegiatan->nama_kegiatan, 'Sasaran Kinerja Pegawai') !== false) {
            $baseRoute = route('skp');
            $baseTitle = 'SKP';
            $baseType = 'Sub Komponen';
        } elseif (stripos($kegiatan->nama_kegiatan, 'Reward & Punishment') !== false || stripos($kegiatan->nama_kegiatan, 'Reward Punishment') !== false) {
            $baseRoute = route('reward.punishment');
            $baseTitle = 'Reward & Punishment';
            $baseType = 'Sub Komponen';
        } elseif (stripos($kegiatan->nama_kegiatan, 'Laporan Kinerja') !== false || stripos($kegiatan->nama_kegiatan, 'LAKIN') !== false) {
            $baseRoute = route('manajemen.lakin');
            $baseTitle = 'Manajemen Lakin';
            $baseType = 'Sub Komponen';
        } else {
            // Fallback for other types, e.g., Renstra related
            $baseRoute = route('manajemen.renstra');
            $baseTitle = 'Perencanaan Kinerja';
            $baseType = 'Komponen';
        }

        // Breadcrumbs array
        $breadcrumbs = [
            ['title' => 'Perencanaan Kinerja', 'url' => route('manajemen.renstra'), 'clickable' => true],
            ['title' => $baseTitle, 'url' => $baseRoute, 'clickable' => true],
            ['title' => 'Detail ' . $kegiatan->nama_kegiatan . ' ' . $kegiatan->tahun_berjalan, 'clickable' => false],
        ];

        return view('kegiatan.detail', compact(
            'kegiatan',
            'buktiDukungByJenis',
            'dokumenKegiatan',
            'requiredDocuments',
            'uploadedCount',
            'totalRequired',
            'isPKDetail',
            'canUpload',
            'canEditDateRange',
            'isSuperAdmin',
            'isAdmin',
            'activityExpired',
            'breadcrumbs' // Pass breadcrumbs to the view
        ));
    }


    /**
     * Menampilkan halaman generate link Google Drive
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function generateLink(Request $request)
    {
        // Default tahun adalah null untuk menampilkan semua kegiatan
        $selectedYear = $request->get('tahun');
        $selectedPeriode = $request->get('periode', '');

        // Mengambil data kegiatan yang memiliki folder_id (sudah terintegrasi dengan Google Drive)
        $query = Kegiatan::with(['sub_komponen', 'renstra'])
            ->whereNotNull('folder_id')
            ->where('folder_id', '!=', '');

        // Filter berdasarkan tahun atau periode
        if ($selectedPeriode) {
            // Jika periode dipilih, ambil berdasarkan periode renstra
            $query->whereHas('renstra', function ($q) use ($selectedPeriode) {
                [$tahunAwal, $tahunAkhir] = explode('-', $selectedPeriode);
                $q->whereYear('periode_awal', $tahunAwal)
                    ->whereYear('periode_akhir', $tahunAkhir);
            });
        } elseif ($selectedYear) {
            // Jika hanya tahun yang dipilih
            $query->where('tahun_berjalan', $selectedYear);
        }

        $perPage = $request->get('per_page', 10); // Jumlah item per halaman, default 10
        $daftar_kegiatan_paginated = $query->orderBy('tahun_berjalan', 'desc')
            ->orderBy('nama_kegiatan', 'asc')
            ->paginate($perPage); // Menggunakan paginate

        // Enrich kegiatan data dengan informasi triwulan menggunakan through()
        $googleDriveService = new \App\Services\GoogleDriveOAuthService();

        $daftar_kegiatan = $daftar_kegiatan_paginated->through(function ($kegiatan) use ($googleDriveService) {
            // Identifikasi kegiatan yang memiliki subfolder triwulan
            $hasTriwulanSubfolders = $this->hasTriwulanSubfolders($kegiatan->nama_kegiatan);

            if ($hasTriwulanSubfolders) {
                // Ambil data folder triwulan
                $triwulanFolders = $googleDriveService->getTriwulanFolders($kegiatan->folder_id);
                $kegiatan->triwulan_folders = $triwulanFolders;
            } else {
                $kegiatan->triwulan_folders = [];
            }

            $kegiatan->has_triwulan_subfolders = $hasTriwulanSubfolders;
            return $kegiatan;
        });

        // Mengambil daftar tahun yang tersedia
        $availableYears = Kegiatan::whereNotNull('folder_id')
            ->where('folder_id', '!=', '')
            ->distinct()
            ->orderBy('tahun_berjalan', 'desc')
            ->pluck('tahun_berjalan');

        // Mengambil daftar periode renstra yang tersedia
        $availablePeriodes = Renstra::select('periode_awal', 'periode_akhir')
            ->whereHas('kegiatans', function ($q) {
                $q->whereNotNull('folder_id')->where('folder_id', '!=', '');
            })
            ->orderBy('periode_awal', 'desc')
            ->get()
            ->map(function ($renstra) {
                $tahunAwal = date('Y', strtotime($renstra->periode_awal));
                $tahunAkhir = date('Y', strtotime($renstra->periode_akhir));
                return [
                    'value' => "{$tahunAwal}-{$tahunAkhir}",
                    'label' => "Periode {$tahunAwal}-{$tahunAkhir}"
                ];
            });

        return view('pelaporan kinerja.generate_link', compact(
            'daftar_kegiatan',
            'selectedYear',
            'selectedPeriode',
            'availableYears',
            'availablePeriodes',
            'perPage' // Pass perPage to the view
        ));
    }

    /**
     * Check if a kegiatan has triwulan subfolders
     *
     * @param string $namaKegiatan
     * @return bool
     */
    private function hasTriwulanSubfolders($namaKegiatan)
    {
        $kegiatanWithTriwulan = [
            'Capaian Kinerja',
            'Monitoring Capaian Kinerja',
            'Form Rencana Aksi',
            'Reward Punishment',
            'Reward & Punishment'
        ];

        foreach ($kegiatanWithTriwulan as $keyword) {
            if (stripos($namaKegiatan, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get quarter period dates
     *
     * @param int $quarter
     * @param int $year
     * @return array
     */
    private function getQuarterPeriod($quarter, $year)
    {
        $quarters = [
            1 => ['start' => "$year-01-01", 'end' => "$year-03-31"],
            2 => ['start' => "$year-04-01", 'end' => "$year-06-30"],
            3 => ['start' => "$year-07-01", 'end' => "$year-09-30"],
            4 => ['start' => "$year-10-01", 'end' => "$year-12-31"],
        ];

        return [
            'start' => Carbon::parse($quarters[$quarter]['start']),
            'end' => Carbon::parse($quarters[$quarter]['end'])
        ];
    }

    /**
     * Get quarter status based on current date
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param Carbon $currentDate
     * @return string
     */
    private function getQuarterStatus($startDate, $endDate, $currentDate)
    {
        if ($currentDate->lt($startDate)) {
            return 'upcoming'; // Belum dimulai
        }

        if ($currentDate->gt($endDate)) {
            return 'closed'; // Sudah lewat
        }

        return 'active'; // Sedang berjalan
    }

    /**
     * Mengambil data kegiatan untuk ditampilkan di view dengan parameter exclude
     * Fungsi ini mengambil kegiatan berdasarkan kriteria spesifik
     *
     * @param string $jenisKegiatan      Kata kunci yang harus ada dalam nama kegiatan
     * @param array $excludeKeywords     Kata kunci yang TIDAK boleh ada dalam nama kegiatan
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getKegiatanData(string $jenisKegiatan, array $excludeKeywords = [])
    {
        $query = Kegiatan::where('nama_kegiatan', 'like', "%{$jenisKegiatan}%");

        // Tambahkan filter untuk excludeKeywords
        foreach ($excludeKeywords as $exclude) {
            $query->where('nama_kegiatan', 'not like', "%{$exclude}%");
        }

        return $query->orderBy('tahun_berjalan', 'desc')
            ->get()
            ->map(function ($kegiatan) {
                $currentDate = Carbon::now();
                $startDate = Carbon::parse($kegiatan->tanggal_mulai);
                $endDate = Carbon::parse($kegiatan->tanggal_berakhir);

                // Menentukan status
                $kegiatan->status = $this->tentukanStatus($currentDate, $startDate, $endDate);
                $kegiatan->status_class = $this->getStatusClass($kegiatan->status);
                $kegiatan->status_dot = $this->getStatusDot($kegiatan->status);

                // Menentukan kelengkapan dokumen
                $this->aturKelengkapan($kegiatan);

                return $kegiatan;
            });
    }

    /**
     * Mengambil data kegiatan berdasarkan multiple keywords (OR condition)
     * Fungsi ini mencari kegiatan yang namanya mengandung salah satu dari kata kunci yang diberikan
     *
     * @param array $keywords  Array kata kunci yang dicari dalam nama kegiatan
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getKegiatanDataMultipleKeywords(array $keywords)
    {
        $query = Kegiatan::query();

        // Tambahkan kondisi OR untuk setiap keyword
        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                $q->orWhere('nama_kegiatan', 'like', "%{$keyword}%");
            }
        });

        return $query->orderBy('tahun_berjalan', 'desc')
            ->get()
            ->map(function ($kegiatan) {
                $currentDate = Carbon::now();
                $startDate = Carbon::parse($kegiatan->tanggal_mulai);
                $endDate = Carbon::parse($kegiatan->tanggal_berakhir);

                // Menentukan status
                $kegiatan->status = $this->tentukanStatus($currentDate, $startDate, $endDate);
                $kegiatan->status_class = $this->getStatusClass($kegiatan->status);
                $kegiatan->status_dot = $this->getStatusDot($kegiatan->status);

                // Menentukan kelengkapan dokumen
                $this->aturKelengkapan($kegiatan);

                return $kegiatan;
            });
    }

    /**
     * Menampilkan view dengan data kegiatan
     *
     * @param string $jenisKegiatan
     * @param string $viewName
     * @return \Illuminate\View\View
     */
    protected function getKegiatanView(string $jenisKegiatan, string $viewName): View
    {
        $daftar_kegiatan = $this->getKegiatanData($jenisKegiatan);
        return view($viewName, ['daftar_kegiatan' => $daftar_kegiatan]);
    }

    /**
     * Menentukan status kegiatan
     *
     * @param Carbon $currentDate
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return string
     */
    private function tentukanStatus(Carbon $currentDate, Carbon $startDate, Carbon $endDate): string
    {
        if ($currentDate->lt($startDate)) {
            return 'Upcoming';
        }

        if ($currentDate->gt($endDate)) {
            return 'Closed';
        }

        // Simulasi status Fulfillment (30% peluang untuk kegiatan yang sedang berjalan)
        if (rand(1, 100) <= 30) {
            return 'Fulfillment';
        }

        return 'Open';
    }

    /**
     * Mendapatkan class CSS untuk status
     *
     * @param string $status
     * @return string
     */
    private function getStatusClass(string $status): string
    {
        return match ($status) {
            'Upcoming', 'Open' => 'bg-blue-100 text-blue-800',
            'Fulfillment' => 'bg-amber-100 text-amber-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Mendapatkan dot warna untuk status
     *
     * @param string $status
     * @return string
     */
    private function getStatusDot(string $status): string
    {
        return match ($status) {
            'Upcoming', 'Open' => 'bg-blue-500',
            'Fulfillment' => 'bg-amber-500',
            default => 'bg-gray-500',
        };
    }

    /**
     * Mengatur properti kelengkapan dokumen
     *
     * @param Kegiatan $kegiatan
     */
    private function aturKelengkapan(Kegiatan $kegiatan): void
    {
        // Ambil dokumen yang diperlukan untuk kegiatan ini
        $requiredDocuments = $this->getRequiredDocuments($kegiatan);
        
        // Hitung jumlah dokumen yang sudah diupload
        $uploadedCount = $this->getUploadedCount($kegiatan, $requiredDocuments);
        
        // Total dokumen yang diperlukan
        $totalRequired = count($requiredDocuments);

        // Untuk Reward & Punishment, tambahkan logika khusus untuk menampilkan triwulan yang belum lengkap
        if (stripos($kegiatan->nama_kegiatan, 'Reward & Punishment') !== false) {
            // Definisi dokumen yang diperlukan per triwulan
            $triwulanDokumen = [
                'sk_penerima_triwulan',
                'piagam_penghargaan_triwulan',
                'rekap_pemilihan_triwulan'
            ];

            // Tentukan triwulan saat ini berdasarkan bulan
            $currentMonth = date('n');
            $currentQuarter = ceil($currentMonth / 3);

            // Cek kelengkapan per triwulan
            $triwulanBelumLengkap = [];
            for ($tw = 1; $tw <= $currentQuarter; $tw++) {
                $dokumenTriwulanLengkap = true;
                
                foreach ($triwulanDokumen as $jenisDokumen) {
                    $dokumenExists = $kegiatan->buktiDukung->filter(function ($bukti) use ($jenisDokumen, $tw) {
                        return stripos($bukti->jenis, $jenisDokumen) !== false && 
                               stripos($bukti->jenis, "TW {$tw}") !== false;
                    })->isNotEmpty();

                    if (!$dokumenExists) {
                        $dokumenTriwulanLengkap = false;
                        break;
                    }
                }

                if (!$dokumenTriwulanLengkap) {
                    $triwulanBelumLengkap[] = $tw;
                }
            }

            // Tentukan kelengkapan dan keterangan
            $kelengkapan = $uploadedCount >= $totalRequired ? 1 : 0;
            $kegiatan->kelengkapan = $kelengkapan;
            $kegiatan->kelengkapan_class = $kelengkapan ? 'text-green-500' : 'text-red-500';
            
            // Buat keterangan dengan daftar triwulan yang belum lengkap
            if (!$kelengkapan && !empty($triwulanBelumLengkap)) {
                $triwulanText = implode(', ', array_map(function($tw) {
                    return "Triwulan {$tw}";
                }, $triwulanBelumLengkap));
                $kegiatan->keterangan = "{$triwulanText} belum lengkap";
            } else {
                $kegiatan->keterangan = 'Semua dokumen sudah diupload';
            }
        } else {
            // Untuk kegiatan lain, gunakan logika sebelumnya
            $kelengkapan = $uploadedCount >= $totalRequired ? 1 : 0;

            $kegiatan->kelengkapan = $kelengkapan;
            $kegiatan->kelengkapan_class = $kelengkapan ? 'text-green-500' : 'text-red-500';
            $kegiatan->keterangan = $kelengkapan 
                ? 'Semua dokumen sudah diupload' 
                : "Dokumen yang diupload: {$uploadedCount}/{$totalRequired}";
        }
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
        if ($this->isPKKegiatan($kegiatan)) {
            $pkDocuments = [
                'dokumen_pk' => 'Dokumen PK',
                'notulensi' => 'Notulensi',
                'surat_undangan' => 'Surat Undangan',
                'daftar_hadir' => 'Daftar Hadir'
            ];
            return $pkDocuments;
        }

        // Jika kegiatan adalah SKP, tambahkan requirement khusus
        if (
            stripos($kegiatan->nama_kegiatan, 'SKP') !== false ||
            stripos($kegiatan->nama_kegiatan, 'Sasaran Kinerja') !== false
        ) {

            $skpDocuments = [];
            for ($i = 1; $i <= 12; $i++) {
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
    private function getUploadedCount($kegiatan, $requiredDocuments)
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
     * Determine if kegiatan is a PK (Perjanjian Kinerja) type
     */
    private function isPKKegiatan($kegiatan)
    {
        return stripos($kegiatan->nama_kegiatan, 'Perjanjian Kinerja') !== false ||
            stripos($kegiatan->nama_kegiatan, 'PK') !== false ||
            (isset($kegiatan->sub_komponen) && stripos($kegiatan->sub_komponen->sub_komponen, 'Manajemen PK') !== false);
    }

    /**
     * Show the form for inputting Target PK
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function inputTargetPK($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        // Ambil data matriks FRA berdasarkan tahun kegiatan dengan relasi yang lengkap
        $matriksFraData = Matriks_Fra::with(['template_fra.template_jenis', 'template_fra.fra'])
            ->whereHas('template_fra.fra', function ($query) use ($kegiatan) {
                $query->where('tahun_berjalan', $kegiatan->tahun_berjalan);
            })
            ->orderBy('id')
            ->get();

        // Ambil existing target PK untuk kegiatan ini
        $existingTargets = Target_Pk::where('kegiatan_id', $id)
            ->get()
            ->keyBy('matriks_fra_id');

        // Cek apakah ada template jenis suplemen
        $hasSuplemenData = $matriksFraData->filter(function ($matriks) {
            return $matriks->template_fra &&
                $matriks->template_fra->template_jenis &&
                $matriks->template_fra->template_jenis->nama === 'PK Suplemen';
        })->isNotEmpty();

        // Debug untuk memastikan data suplemen terambil
        Log::info('Matriks FRA Data Count: ' . $matriksFraData->count());
        Log::info('Has Suplemen Data: ' . ($hasSuplemenData ? 'Yes' : 'No'));
        Log::info('Suplemen Data:', $matriksFraData->filter(function ($matriks) {
            return $matriks->template_fra &&
                $matriks->template_fra->template_jenis &&
                $matriks->template_fra->template_jenis->nama === 'PK Suplemen';
        })->toArray());

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Perencanaan Kinerja', 'url' => route('manajemen.renstra'), 'clickable' => true],
            ['title' => 'Manajemen PK', 'url' => route('manajemen.pk'), 'clickable' => true],
            ['title' => 'Input Target PK ' . $kegiatan->nama_kegiatan . ' ' . $kegiatan->tahun_berjalan, 'clickable' => false],
        ];

        return view('pengukuran kinerja.form_target_pk', compact('kegiatan', 'matriksFraData', 'existingTargets', 'hasSuplemenData', 'breadcrumbs'));
    }

    /**
     * Save target PK
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function simpanTargetPK(Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $actionType = $request->input('action_type', 'save');
        $isAjax = $request->ajax();

        try {
            DB::beginTransaction();

            // Validation rules for all submitted targets
            $rules = [
                'targets_pk' => 'present|array',
                'targets_pk.*' => 'nullable|numeric|min:0'
            ];

            // For finalization, make fields required (only for editable fields)
            if ($actionType === 'finalize') {
                $rules['targets_pk.*'] = 'required|numeric|min:0';
            }

            $validated = $request->validate($rules, [
                'targets_pk.*.required' => 'Semua field Target PK harus diisi untuk finalisasi.',
                'targets_pk.*.numeric' => 'Target PK harus berupa angka.',
                'targets_pk.*.min' => 'Target PK tidak boleh negatif.',
            ]);

            // Get all target PK inputs from the validated data
            $targetPkInputs = $validated['targets_pk'] ?? [];

            // Get all valid matriks IDs for this specific FRA year
            $validMatriksIds = Matriks_Fra::whereHas('template_fra.fra', function ($query) use ($kegiatan) {
                $query->where('tahun_berjalan', $kegiatan->tahun_berjalan);
            })
                ->pluck('id')->all();

            // Prepare batch insert/update data
            $now = now();
            $batchData = [];
            $processedMatriksIds = []; // Track processed matriks IDs

            foreach ($targetPkInputs as $matriksId => $targetValue) {
                // Ensure the matriks_id is valid for this year and target is not empty
                if (in_array($matriksId, $validMatriksIds) && $targetValue !== null && $targetValue !== '') {
                    $batchData[] = [
                        'kegiatan_id' => $kegiatan->id,
                        'matriks_fra_id' => $matriksId,
                        'target_pk' => $targetValue,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                    $processedMatriksIds[] = $matriksId;
                }
            }

            // Batch upsert
            if (!empty($batchData)) {
                Target_Pk::upsert(
                    $batchData,
                    ['kegiatan_id', 'matriks_fra_id'],
                    ['target_pk', 'updated_at']
                );
            }

            // Log successful save
            Log::info('Target PK saved successfully', [
                'kegiatan_id' => $kegiatan->id,
                'action_type' => $actionType,
                'processed_matriks_ids' => $processedMatriksIds,
                'total_processed' => count($processedMatriksIds)
            ]);

            DB::commit();

            // Return appropriate response based on request type
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil disimpan'
                ]);
            }

            // If finalizing, redirect to manajemen PK
            if ($actionType === 'finalize') {
                $message = 'Target PK berhasil difinalisasi! Data akan tersedia di form target FRA.';
                return redirect()->route('manajemen.pk')->with('success', $message);
            }

            $message = 'Perubahan target PK berhasil disimpan!';
            return redirect()->back()->with('success', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            // Log validation errors
            Log::error('Target PK Validation Error', [
                'errors' => $e->errors(),
                'kegiatan_id' => $kegiatan->id,
                'action_type' => $actionType
            ]);

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang dimasukkan tidak valid.',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withInput()->withErrors($e->errors())->with('error', 'Data yang dimasukkan tidak valid.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Detailed error logging
            Log::error('Error saat menyimpan target PK', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'kegiatan_id' => $kegiatan->id,
                'action_type' => $actionType,
                'trace' => $e->getTraceAsString()
            ]);

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan target PK. Silakan coba lagi.',
                    'error_details' => $e->getMessage()
                ], 500);
            }
            return back()->withInput()->with('error', 'Gagal menyimpan target PK. Silakan coba lagi: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman SKP untuk pengguna biasa
     *
     * @return \Illuminate\View\View
     */
    public function unggahSkp(Request $request)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        $isSuperAdmin = Auth::check() && $user->isSuperAdmin();

        // Pagination logic
        $perPage = $request->input('perPage', 10); // Default to 10 items per page

        $query = Kegiatan::where('nama_kegiatan', 'like', '%Sasaran Kinerja Pegawai%')
            ->orderBy('tahun_berjalan', 'desc')
            ->orderBy('tanggal_mulai', 'desc');

        $daftar_kegiatan = $query->paginate($perPage)->appends($request->except('page'));

        // Mengambil daftar sub komponen untuk dropdown
        $subKomponenList = Sub_Komponen::orderBy('sub_komponen', 'asc')->get();

        // Cari sub komponen "SKP" dengan berbagai kemungkinan nama
        $SkpSubKomponen = $subKomponenList->first(function ($item) {
            return stripos($item->sub_komponen, 'SKP') !== false ||
                stripos($item->sub_komponen, 'Sasaran Kinerja') !== false ||
                stripos($item->sub_komponen, 'Kinerja Pegawai') !== false;
        });

        // Mendapatkan renstra aktif (untuk hidden input)
        $activeRenstra = Renstra::orderBy('periode_akhir', 'desc')->first();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Unggah SKP', 'url' => route('unggah.skp'), 'clickable' => false],
        ];

        return view('unggah_skp', compact(
            'daftar_kegiatan',
            'subKomponenList',
            'SkpSubKomponen',
            'activeRenstra',
            'isSuperAdmin',
            'breadcrumbs'
        ));
    }

    /**
     * Upload SKP Bulanan
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadSkpBulanan(Request $request)
    {
        try {
            $request->validate([
            'skp_file' => 'required|file|mimes:pdf|max:5120', // 5MB
            'nip' => 'required|string',
            'bulan' => 'required|integer|min:1|max:12',
            'kegiatan_id' => 'required|integer|exists:kegiatan,id'
        ]);

            $kegiatan = Kegiatan::findOrFail($request->kegiatan_id);
            $user = Pengguna::where('nip', $request->nip)->firstOrFail();
            
            // Get Google Drive service
            $googleDriveService = app(GoogleDriveOAuthService::class);
            
            // Get kegiatan folder (parent of SKP folders)
            $kegiatanFolderId = $googleDriveService->getSkpFolderId($kegiatan->id);
            if (!$kegiatanFolderId) {
                throw new \Exception('Folder kegiatan tidak ditemukan');
            }
            
            // Get SKP Bulanan folder directly from kegiatan folder
            $skpBulananFolderId = $googleDriveService->findFolderByName('SKP Bulanan', $kegiatanFolderId);
            if (!$skpBulananFolderId) {
                throw new \Exception('Folder SKP Bulanan tidak ditemukan');
            }
            
            // Get monthly folder ID
            $monthlyFolderId = $googleDriveService->getMonthlyFolderId($skpBulananFolderId, $request->bulan);
            if (!$monthlyFolderId) {
                throw new \Exception('Folder bulanan tidak ditemukan');
            }
            
            // Upload file to Google Drive
            $file = $request->file('skp_file');
            $fileName = 'SKP_Bulanan_' . $user->name . '_' . str_pad($request->bulan, 2, '0', STR_PAD_LEFT) . '_' . date('Y') . '.pdf';
            
            $uploadedFile = $googleDriveService->uploadFile(
                $file->getPathname(),
                $fileName,
                $monthlyFolderId
            );
            
            if (!$uploadedFile) {
                throw new \Exception('Gagal mengunggah file ke Google Drive');
            }
            
            // Save to database
            Skp::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'kegiatan_id' => $kegiatan->id,
                    'jenis' => 'bulanan',
                    'bulan' => $request->bulan,
                    'tahun' => date('Y')
                ],
                [
                    'nama_file' => $fileName,
                    'file_id' => $uploadedFile['file_id'],
                    'webViewLink' => $uploadedFile['webViewLink'] ?? null,
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => now()
                ]
            );
            
            Log::info('SKP Bulanan uploaded successfully', [
                'user_id' => $user->id,
                'kegiatan_id' => $kegiatan->id,
                'bulan' => $request->bulan,
                'nama_file' => $fileName
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'SKP Bulanan berhasil diunggah'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error uploading SKP Bulanan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah SKP Bulanan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Upload SKP Tahunan
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadSkpTahunan(Request $request)
    {
        try {
            $request->validate([
            'skp_file' => 'required|file|mimes:pdf|max:5120', // 5MB
            'nip' => 'required|string',
            'kegiatan_id' => 'required|integer|exists:kegiatan,id'
        ]);

            $kegiatan = Kegiatan::findOrFail($request->kegiatan_id);
            $user = Pengguna::where('nip', $request->nip)->firstOrFail();
            
            // Get Google Drive service
            $googleDriveService = app(GoogleDriveOAuthService::class);
            
            // Get kegiatan folder (parent of SKP folders)
            $kegiatanFolderId = $googleDriveService->getSkpFolderId($kegiatan->id);
            if (!$kegiatanFolderId) {
                throw new \Exception('Folder kegiatan tidak ditemukan');
            }
            
            // Get yearly folder ID (SKP Tahunan folder) directly from kegiatan folder
            $yearlyFolderId = $googleDriveService->getYearlyFolderId($kegiatanFolderId);
            if (!$yearlyFolderId) {
                throw new \Exception('Folder SKP Tahunan tidak ditemukan');
            }
            
            // Upload file to Google Drive
            $file = $request->file('skp_file');
            $fileName = 'SKP_Tahunan_' . $user->nama . '_' . date('Y') . '.pdf';
            
            $uploadedFile = $googleDriveService->uploadFile(
                $file->getPathname(),
                $fileName,
                $yearlyFolderId
            );
            
            if (!$uploadedFile) {
                throw new \Exception('Gagal mengunggah file ke Google Drive');
            }
            
            // Save to database
            Skp::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'kegiatan_id' => $kegiatan->id,
                    'jenis' => 'tahunan',
                    'tahun' => date('Y')
                ],
                [
                    'nama_file' => $fileName,
                    'file_id' => $uploadedFile['file_id'],
                    'webViewLink' => $uploadedFile['webViewLink'] ?? null,
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => now()
                ]
            );
            
            Log::info('SKP Tahunan uploaded successfully', [
                'user_id' => $user->id,
                'kegiatan_id' => $kegiatan->id,
                'nama_file' => $fileName
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'SKP Tahunan berhasil diunggah'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error uploading SKP Tahunan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah SKP Tahunan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Download SKP Bulanan per bulan
     *
     * @param string $nip
     * @param int $bulan
     * @return \Illuminate\Http\Response
     */
    public function downloadSkpBulananPerBulan($nip, $bulan)
    {
        try {
            $user = Pengguna::where('nip', $nip)->firstOrFail();
            
            // Find SKP record
            $skp = Skp::where('user_id', $user->id)
                ->where('jenis', 'bulanan')
                ->where('bulan', $bulan)
                ->where('tahun', date('Y'))
                ->whereNotNull('file_id')
                ->firstOrFail();
            
            // Get Google Drive service
            $googleDriveService = app(GoogleDriveOAuthService::class);
            
            // Download file from Google Drive
            $fileContent = $googleDriveService->downloadFile($skp->file_id);
            
            if (!$fileContent) {
                throw new \Exception('File tidak ditemukan di Google Drive');
            }
            
            $bulanNama = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            
            $fileName = 'SKP_Bulanan_' . $user->nama . '_' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '_' . date('Y') . '.pdf';
            
            return response($fileContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
                
        } catch (\Exception $e) {
            Log::error('Error downloading SKP Bulanan', [
                'nip' => $nip,
                'bulan' => $bulan,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan atau gagal diunduh'
            ], 404);
        }
    }

    /**
     * Download SKP Tahunan
     *
     * @param string $nip
     * @return \Illuminate\Http\Response
     */
    public function downloadSkpTahunan($nip)
    {
        try {
            $user = Pengguna::where('nip', $nip)->firstOrFail();
            
            // Find SKP record
            $skp = Skp::where('user_id', $user->id)
                ->where('jenis', 'tahunan')
                ->where('tahun', date('Y'))
                ->whereNotNull('file_id')
                ->firstOrFail();
            
            // Get Google Drive service
            $googleDriveService = app(GoogleDriveOAuthService::class);
            
            // Download file from Google Drive
            $fileContent = $googleDriveService->downloadFile($skp->file_id);
            
            if (!$fileContent) {
                throw new \Exception('File tidak ditemukan di Google Drive');
            }
            
            $fileName = 'SKP_Tahunan_' . $user->nama . '_' . date('Y') . '.pdf';
            
            return response($fileContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
                
        } catch (\Exception $e) {
            Log::error('Error downloading SKP Tahunan', [
                'nip' => $nip,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan atau gagal diunduh'
            ], 404);
        }
    }

    /**
     * Update SKP document name
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSkpName(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_dokumen' => 'required|string|max:255'
            ]);

            $skp = Skp::findOrFail($id);
            
            // Update nama file di Google Drive jika ada file_id
            if ($skp->file_id) {
                $googleDriveService = app(GoogleDriveOAuthService::class);
                $updateResult = $googleDriveService->updateFileName($skp->file_id, $request->nama_dokumen);
                
                if (!$updateResult) {
                    Log::warning('Failed to update file name in Google Drive, but continuing with database update', [
                        'skp_id' => $id,
                        'file_id' => $skp->file_id,
                        'new_name' => $request->nama_dokumen
                    ]);
                }
            }
            
            // Update nama file di database
            $skp->update([
                'nama_file' => $request->nama_dokumen
            ]);

            Log::info('SKP name updated successfully', [
                'skp_id' => $id,
                'new_name' => $request->nama_dokumen,
                'google_drive_updated' => isset($updateResult) ? $updateResult : 'no_file_id'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nama dokumen SKP berhasil diperbarui',
                'redirect_url' => route('skp.detail.unggah', $skp->kegiatan_id)
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating SKP name', [
                'skp_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui nama dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update SKP document file
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSkpFile(Request $request, $id)
    {
        try {
            $request->validate([
                'dokumen' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240' // 10MB
            ]);

            $skp = Skp::findOrFail($id);
            $file = $request->file('dokumen');
            
            // Get Google Drive service
            $googleDriveService = app(GoogleDriveOAuthService::class);
            
            // Generate new filename
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = $originalName . '_' . time() . '.' . $extension;
            
            // Get kegiatan folder
            $kegiatanFolderId = $googleDriveService->getSkpFolderId($skp->kegiatan_id);
            if (!$kegiatanFolderId) {
                throw new \Exception('Folder kegiatan tidak ditemukan');
            }
            
            // Get appropriate folder based on SKP type
            if ($skp->jenis === 'bulanan') {
                // For monthly SKP, we need the SKP Bulanan folder first
                $skpBulananFolderId = $googleDriveService->findFolderByName('SKP Bulanan', $kegiatanFolderId);
                if (!$skpBulananFolderId) {
                    throw new \Exception('Folder SKP Bulanan tidak ditemukan');
                }
                
                // Then get the specific monthly folder (assuming current month if not specified)
                $bulan = $skp->bulan ?? date('n'); // Use SKP's month or current month
                $folderId = $googleDriveService->getMonthlyFolderId($skpBulananFolderId, $bulan);
            } else {
                $folderId = $googleDriveService->getYearlyFolderId($kegiatanFolderId);
            }
            
            if (!$folderId) {
                throw new \Exception('Folder SKP tidak ditemukan');
            }
            
            // Upload new file to Google Drive
            $uploadedFile = $googleDriveService->uploadFile(
                $file->getPathname(),
                $fileName,
                $folderId
            );
            
            if (!$uploadedFile) {
                throw new \Exception('Gagal mengunggah file ke Google Drive');
            }
            
            // Delete old file if exists
            if ($skp->file_id) {
                try {
                    $googleDriveService->moveToTrash($skp->file_id);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete old SKP file', [
                        'file_id' => $skp->file_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Update database
            $skp->update([
                'nama_file' => $fileName,
                'file_id' => $uploadedFile['file_id'],
                'webViewLink' => $uploadedFile['webViewLink'] ?? null,
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now()
            ]);

            Log::info('SKP file updated successfully', [
                'skp_id' => $id,
                'new_file' => $fileName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File dokumen SKP berhasil diperbarui',
                'redirect_url' => route('skp.detail.unggah', $skp->kegiatan_id)
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating SKP file', [
                'skp_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui file dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get capaian kinerja quarter period dates (periode upload 1 bulan setelah triwulan selesai)
     *
     * @param int $quarter
     * @param int $year
     * @return array
     */
    private function getCapaianKinerjaQuarterPeriod($quarter, $year)
    {
        $uploadPeriods = [
            1 => ['start' => "$year-04-01", 'end' => "$year-04-30"], // April (setelah Q1)
            2 => ['start' => "$year-07-01", 'end' => "$year-07-31"], // Juli (setelah Q2)
            3 => ['start' => "$year-10-01", 'end' => "$year-10-31"], // Oktober (setelah Q3)
            4 => ['start' => ($year + 1) . "-01-01", 'end' => ($year + 1) . "-01-31"], // Januari tahun berikutnya (setelah Q4)
        ];

        return [
            'start' => Carbon::parse($uploadPeriods[$quarter]['start']),
            'end' => Carbon::parse($uploadPeriods[$quarter]['end'])
        ];
    }
}
