<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class BreadcrumbsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void 
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->defineBreadcrumbs();
    }

    /**
     * Define breadcrumbs for routes
     *
     * @return void
     */
    protected function defineBreadcrumbs()
    {
        View::composer('*', function ($view) {
            // Jika view sudah memiliki breadcrumbs, tidak perlu diproses lagi
            if ($view->offsetExists('breadcrumbs')) {
                return;
            }
            
            // Simpan path terakhir di session untuk tracking navigasi
            $currentPath = request()->path();
            $previousPath = session('last_path', '');
            
            // Jika halaman saat ini mengandung 'detail', simpan referrer path
            if (strpos($currentPath, 'detail') !== false) {
                if ($previousPath && strpos($previousPath, 'detail') === false) {
                    session(['referrer_path' => $previousPath]);
                }
            }
            
            // Update last path untuk penggunaan berikutnya
            session(['last_path' => $currentPath]);
            
            $breadcrumbs = $this->generateBreadcrumbsFromPath();
            $view->with('breadcrumbs', $breadcrumbs);
        });
    }

    /**
     * Generate breadcrumbs based on current URL path
     *
     * @return array
     */
    protected function generateBreadcrumbsFromPath()
    {
        // Ambil path saat ini
        $path = request()->path();
        $segments = explode('/', $path);
        $breadcrumbs = [];
        
        // Debug: Log path yang sedang diproses

        
        // Handle detail dashboard dengan format khusus
        if (strpos($path, 'dashboard/detail-dashboard') !== false) {
            $breadcrumbs = [
                ['title' => 'Beranda', 'url' => route('dashboard'), 'clickable' => true],
                ['title' => 'Dashboard', 'url' => route('dashboard'), 'clickable' => true],
            ];
            
            // Extract komponen dari URL parameter
            $komponen = request()->get('komponen') ?? request()->segment(3);
            if ($komponen) {
                $breadcrumbs[] = ['title' => "Detail {$komponen}", 'url' => null, 'clickable' => false];
            } else {
                $breadcrumbs[] = ['title' => 'Detail Dashboard', 'url' => null, 'clickable' => false];
            }
            
            return $breadcrumbs;
        }
        


        // Handle manajemen pengguna
        if (strpos($path, 'manajemen-pengguna') !== false) {
            $breadcrumbs = [
                ['title' => 'Beranda', 'url' => route('dashboard'), 'clickable' => true],
                ['title' => 'Manajemen Pengguna', 'url' => route('users.index'), 'clickable' => true],
            ];
            
            // Check if it's a detail page
            if (strpos($path, 'detail') !== false || preg_match('/\/\d+$/', $path)) {
                $breadcrumbs[] = ['title' => 'Detail Pengguna', 'url' => null, 'clickable' => false];
            } elseif (strpos($path, 'create') !== false) {
                $breadcrumbs[] = ['title' => 'Tambah Pengguna', 'url' => null, 'clickable' => false];
            } elseif (strpos($path, 'edit') !== false) {
                $breadcrumbs[] = ['title' => 'Edit Pengguna', 'url' => null, 'clickable' => false];
            }
            
            return $breadcrumbs;
        }
        
        // Handle manajemen renstra dan sub-komponennya
        if (strpos($path, 'manajemen-renstra') !== false) {

            $breadcrumbs = [
                ['title' => 'Beranda', 'url' => route('dashboard'), 'clickable' => true],
                ['title' => 'Perencanaan Kinerja', 'url' => null, 'clickable' => false],
                ['title' => 'Manajemen Renstra', 'url' => route('manajemen.renstra'), 'clickable' => true],
            ];
            
            // Handle sub-komponen dari Manajemen Renstra
            if (strpos($path, 'reviu-renstra') !== false) {
                $breadcrumbs[] = ['title' => 'Reviu Renstra', 'url' => route('manajemen.renstra') . '/reviu-renstra', 'clickable' => true];
            } elseif (strpos($path, 'reviu-target-renstra') !== false) {
                $breadcrumbs[] = ['title' => 'Reviu Target Renstra', 'url' => route('manajemen.renstra') . '/reviu-target-renstra', 'clickable' => true];
            } elseif (strpos($path, 'capaian-target-renstra') !== false) {
                $breadcrumbs[] = ['title' => 'Capaian Target Renstra', 'url' => route('manajemen.renstra') . '/capaian-target-renstra', 'clickable' => true];
            }
            
            // Check if it's a detail page
            if (strpos($path, 'detail') !== false || preg_match('/\/\d+$/', $path)) {
                $breadcrumbs[] = ['title' => 'Detail Kegiatan', 'url' => null, 'clickable' => false];
            }
            

            return $breadcrumbs;
        }
        
        // Handle Unggah SKP dan detailnya
        if (strpos($path, 'unggah-skp') !== false) {

            $breadcrumbs = [
                ['title' => 'Beranda', 'url' => route('dashboard'), 'clickable' => true],
                ['title' => 'Pengukuran Kinerja', 'url' => null, 'clickable' => false],
                ['title' => 'Unggah SKP', 'url' => route('unggah.skp'), 'clickable' => true],
            ];
            
            // Check if it's a detail page
            if (strpos($path, 'detail') !== false || preg_match('/\/\d+$/', $path)) {
                $breadcrumbs[] = ['title' => 'Detail Unggah Sasaran Kinerja Pegawai', 'url' => null, 'clickable' => false];
            }
            

            return $breadcrumbs;
        }
        
        // Handle manajemen profil
        if (strpos($path, 'manajemen-profil') !== false || strpos($path, 'profile') !== false) {
            $breadcrumbs = [
                ['title' => 'Beranda', 'url' => route('dashboard'), 'clickable' => true],
                ['title' => 'Manajemen Profil', 'url' => route('profile.edit'), 'clickable' => true],
            ];
            
            return $breadcrumbs;
        }
        
        // Handle detail kegiatan
        if (strpos($path, 'kegiatan') !== false && (strpos($path, 'detail') !== false || preg_match('/kegiatan\/\d+$/', $path))) {
            $breadcrumbs = [
                ['title' => 'Beranda', 'url' => route('dashboard'), 'clickable' => true],
                ['title' => 'Manajemen Kegiatan', 'url' => route('kegiatan.index'), 'clickable' => true],
                ['title' => 'Detail Kegiatan', 'url' => null, 'clickable' => false],
            ];
            
            return $breadcrumbs;
        }
        
        // Mapping khusus untuk label yang lebih user-friendly
        $labelMap = [
            'dashboard' => 'Dashboard',
            'detail-dashboard' => 'Detail Dashboard',
            'perencanaan-kinerja' => 'Perencanaan Kinerja',
            'manajemen-renstra' => 'Manajemen Renstra',
            'manajemen-rkt' => 'Rencana Kinerja Tahunan',
            'manajemen-pk' => 'Perencanaan Kinerja',
            'renstra' => 'Renstra',
            'reviu-renstra' => 'Reviu Renstra',
            'reviu-target-renstra' => 'Reviu Target Renstra',
            'capaian-target-renstra' => 'Capaian Target Renstra',
            'detail' => 'Detail',
            'pengukuran-kinerja' => 'Pengukuran Kinerja',
            'fra' => 'Form Rencana Aksi',
            'form-target-fra' => 'Target FRA',
            'sk-tim-sakip' => 'SK Tim SAKIP',
            'sk' => 'SK',
            'tim' => 'Tim',
            'sakip' => 'SAKIP', 
            'manajemen-pengguna' => 'Manajemen Pengguna',
            'edit' => 'Edit',
            'pelaporan-kinerja' => 'Pelaporan Kinerja',
            'manajemen-lakin' => 'Manajemen Lakin',
            'lakin' => 'LAKIN',
            'generate-link' => 'Generate Link',
            'lakip' => 'LAKIP',
            'generate-link-permindok' => 'Generate Link Permindok',
            'ckp' => 'CKP',
            'reward-and-punishment' => 'Reward and Punishment',
            'inovasi-dan-penghargaan' => 'Inovasi dan Penghargaan',
            'manajemen-profil' => 'Manajemen Profil',
            'skp' => 'SKP',
            'unggah-skp' => 'Unggah SKP',
            'reward-punishment' => 'Reward & Punishment',
            'capaian-kinerja' => 'Capaian Kinerja',
            'form-target-pk' => 'Target PK',
            'realisasi' => 'Realisasi',
            'target' => 'Target',
            'triwulan' => 'Triwulan'
        ];

        // List segment yang tidak diklik (no-link) - komponen utama tidak boleh diklik
        $noLinkSegments = ['perencanaan-kinerja', 'pengukuran-kinerja', 'pelaporan-kinerja'];
        
        // Periksa apakah ini adalah halaman detail dan ekstrak informasi penting
        $isDetailPage = in_array('detail', $segments);
        $hasYear = false;
        $year = null;
        $parentModule = null;
        $contextSegments = [];
        
        if ($isDetailPage) {
            // Cari context dari URL halaman detail
            $detailIndex = array_search('detail', $segments);
            
            // Cari tahun (biasanya setelah detail)
            if (isset($segments[$detailIndex + 1]) && preg_match('/^\d{4}$/', $segments[$detailIndex + 1])) {
                $hasYear = true;
                $year = $segments[$detailIndex + 1];
            }
            
            // Deteksi module induk
            if ($detailIndex > 0) {
                // Cek modul induk dari URL saat ini
                $possibleParentModules = ['reviu-renstra', 'reviu-target-renstra', 'capaian-target-renstra', 'manajemen-pk', 'manajemen-rkt'];
                
                // Identifikasi modul induk dari URL saat ini
                foreach ($segments as $segment) {
                    if (in_array($segment, $possibleParentModules)) {
                        $parentModule = $segment;
                        break;
                    }
                }
                
                // Identifikasi kategori induk dari URL saat ini
                $parentCategory = null;
                foreach ($segments as $segment) {
                    if (in_array($segment, ['perencanaan-kinerja', 'pengukuran-kinerja', 'pelaporan-kinerja'])) {
                        $parentCategory = $segment;
                        break;
                    }
                }
            }
            
            // Coba ambil context dari url sebelumnya jika belum ada
            if (!$parentModule) {
                $referrerPath = session('referrer_path', '');
                if ($referrerPath) {
                    $referrerSegments = explode('/', $referrerPath);
                    
                    // Identifikasi modul induk berdasarkan url sebelumnya
                    foreach ($referrerSegments as $segment) {
                        if (in_array($segment, ['reviu-renstra', 'reviu-target-renstra', 'capaian-target-renstra', 'manajemen-pk', 'manajemen-rkt'])) {
                            $parentModule = $segment;
                            break;
                        }
                    }
                    
                    // Identifikasi kategori dari URL sebelumnya jika belum ada
                    if (!isset($parentCategory)) {
                        foreach ($referrerSegments as $segment) {
                            if (in_array($segment, ['perencanaan-kinerja', 'pengukuran-kinerja', 'pelaporan-kinerja'])) {
                                $parentCategory = $segment;
                                break;
                            }
                        }
                    }
                }
            }
            
            // Buat breadcrumb dasar untuk navigasi
            // Selalu mulai dengan Beranda
            $breadcrumbs[] = [
                'title' => 'Beranda',
                'url' => route('dashboard'),
                'clickable' => true
            ];
            
            // Pertama tambahkan kategori induk (jika ada dan diketahui)
            if (!empty($parentCategory)) {
                $breadcrumbs[] = [
                    'title' => $labelMap[$parentCategory] ?? Str::title(str_replace('-', ' ', $parentCategory)),
                    'url' => null,
                    'clickable' => false
                ];
            }
            
            // Tentukan URL dasar berdasarkan modul
            $baseUrl = '/';
            if (!empty($parentCategory)) {
                $baseUrl .= $parentCategory;
            }
            
            // Tambahkan modul induk
            if ($parentModule) {
                // Jika parentModule adalah modul di perencanaan-kinerja
                if (in_array($parentModule, ['reviu-renstra', 'reviu-target-renstra', 'capaian-target-renstra'])) {
                    $breadcrumbs[] = [
                        'title' => $labelMap['manajemen-renstra'] ?? 'Manajemen Renstra',
                        'url' => url($baseUrl.'/manajemen-renstra'),
                        'clickable' => true
                    ];
                    
                    $breadcrumbs[] = [
                        'title' => $labelMap[$parentModule] ?? Str::title(str_replace('-', ' ', $parentModule)),
                        'url' => url($baseUrl.'/manajemen-renstra/'.$parentModule),
                        'clickable' => true
                    ];
                } else {
                    // Untuk modul lain (manajemen-pk, manajemen-rkt, dll)
                    $breadcrumbs[] = [
                        'title' => $labelMap[$parentModule] ?? Str::title(str_replace('-', ' ', $parentModule)),
                        'url' => url($baseUrl.'/'.$parentModule),
                        'clickable' => true
                    ];
                }
            }
            
            // Tambahkan Detail dengan konteks yang sesuai
            $detailTitle = 'Detail';
            if ($hasYear && $parentModule) {
                $moduleLabel = $labelMap[$parentModule] ?? Str::title(str_replace('-', ' ', $parentModule));
                $detailTitle = "Detail {$moduleLabel} {$year}";
            } elseif ($hasYear) {
                $detailTitle = "Detail {$year}";
            } elseif ($parentModule) {
                $moduleLabel = $labelMap[$parentModule] ?? Str::title(str_replace('-', ' ', $parentModule));
                $detailTitle = "Detail {$moduleLabel}";
            }
            
            $breadcrumbs[] = [
                'title' => $detailTitle,
                'url' => url($path),
                'clickable' => true
            ];
            
            return $breadcrumbs;
        }
        
        // Proses normal jika bukan halaman detail
        // Selalu mulai dengan Beranda
        $breadcrumbs[] = [
            'title' => 'Beranda',
            'url' => route('dashboard'),
            'clickable' => true
        ];
        
        $urlPath = '';
        
        foreach ($segments as $index => $segment) {
            // Skip untuk dashboard jika ini adalah segment pertama
            if ($segment === 'dashboard' && $index === 0) {
                continue;
            }
            
            $urlPath .= '/' . $segment;
            
            // Cek apakah segment adalah ID (numeric)
            if (is_numeric($segment)) {
                continue;
            }
            
            // Cek apakah ini adalah segment tahun (4 digit angka)
            if (preg_match('/^\d{4}$/', $segment)) {
                // Ini kemungkinan adalah parameter tahun
                // Gabungkan dengan segment sebelumnya jika ada
                if ($index > 0 && isset($breadcrumbs[count($breadcrumbs) - 1])) {
                    $prevTitle = $breadcrumbs[count($breadcrumbs) - 1]['title'];
                    $breadcrumbs[count($breadcrumbs) - 1]['title'] = $prevTitle . ' ' . $segment;
                } else {
                    $breadcrumbs[] = [
                        'title' => 'Tahun ' . $segment,
                        'url' => url($urlPath),
                        'clickable' => true
                    ];
                }
                continue;
            }
            
            // Tentukan label untuk segment ini
            $label = $labelMap[$segment] ?? Str::title(str_replace('-', ' ', $segment));
            
            // Tentukan apakah segment ini bisa diklik
            $clickable = !in_array($segment, $noLinkSegments) && !is_numeric($segment) && !preg_match('/^\d{4}$/', $segment);
            
            // Dapatkan URL untuk segment ini
            try {
                if ($clickable) {
                    $routeName = $this->getRouteNameFromPath($urlPath);
                    if ($routeName) {
                        // Jika ada route yang sesuai dengan path, gunakan route tersebut
                        $parameters = $this->getRouteParameters();
                        $url = route($routeName, $parameters);
                    } else {
                        $url = url($urlPath);
                    }
                } else {
                    $url = null;
                }
            } catch (\Exception $e) {
                $url = $clickable ? url($urlPath) : null;
            }
            
            $breadcrumbs[] = [
                'title' => $label,
                'url' => $url,
                'clickable' => $clickable
            ];
        }
        
        return $breadcrumbs;
    }
    
    /**
     * Get route name from path if available
     * 
     * @param string $path
     * @return string|null
     */
    protected function getRouteNameFromPath($path)
    {
        $routes = [
            '/dashboard' => 'dashboard',
            '/manajemen-pengguna' => 'manajemen.pengguna',
            '/manajemen-profil' => 'manajemen.profil',
            '/perencanaan-kinerja/manajemen-renstra' => 'manajemen.renstra',
            '/perencanaan-kinerja/manajemen-rkt' => 'manajemen.rkt',
            '/perencanaan-kinerja/manajemen-pk' => 'manajemen.pk',
            '/pengukuran-kinerja/fra' => 'fra.index',
            '/pengukuran-kinerja/sk-tim-sakip' => 'sk.tim.sakip',
            '/pengukuran-kinerja/reward-punishment' => 'reward.punishment',
            '/pengukuran-kinerja/skp' => 'skp',
            '/pengukuran-kinerja/unggah-skp' => 'unggah.skp',
            '/pelaporan-kinerja/manajemen-lakin' => 'manajemen.lakin',
            '/pelaporan-kinerja/generate-link' => 'generate.link',
            '/capaian-kinerja' => 'capaian.kinerja'
        ];

        return $routes[$path] ?? null;
    }
    

    
    /**
     * Get current route parameters
     * 
     * @return array
     */
    protected function getRouteParameters()
    {
        $parameters = [];
        $route = Route::current();
        
        if ($route) {
            $parameters = $route->parameters();
        }
        
        return $parameters;
    }
    
}