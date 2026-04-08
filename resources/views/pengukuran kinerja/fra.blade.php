@extends('components.master')

@section('title', 'FRA')

@section('content')
    @include('components.breadcrumbs')



    <div class="container mx-auto py-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Form Rencana Aksi (FRA)</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola dokumen Form Rencana Aksi untuk pencapaian target kinerja
                    optimal</p>
            </div>
            @if($isSuperAdmin)
            <button id="btnTambahFRA"
                class="bg-red-700 hover:bg-red-800 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-200 shadow-md hover:shadow-xl transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Tambah FRA
            </button>
            @endif
        </div>

        <!-- Filter dan Search Bar -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center space-y-3 md:space-y-0 md:space-x-4">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="searchInput"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-red-500 focus:border-red-500"
                        placeholder="Cari FRA berdasarkan tahun atau status...">
                </div>

                <div class="flex space-x-2">
                    <select id="filterStatus"
                        class="border border-gray-200 rounded-lg px-3 py-2 focus:ring-red-500 focus:border-red-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="Baru Dibuat">Baru Dibuat</option>
                        <option value="Dalam Proses">Dalam Proses</option>
                        <option value="Selesai">Selesai</option>
                    </select>

                    <select id="filterTahun"
                        class="border border-gray-200 rounded-lg px-3 py-2 focus:ring-red-500 focus:border-red-500 text-sm">
                        <option value="">Semua Tahun</option>
                        @php
                            $currentYear = date('Y');
                            for ($year = $currentYear + 2; $year >= $currentYear - 5; $year--) {
                                echo "<option value=\"$year\">$year</option>";
                            }
                        @endphp
                    </select>

                    <button id="applyFilter"
                        class="bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Wrapper - Main Area -->
        <div class="bg-white rounded-xl shadow-sm mb-6 overflow-hidden">
            <!-- Tabs Navigation -->
            <div class="border-b">
                <div class="flex overflow-x-auto">
                    <button
                        class="tab-btn text-sm font-medium px-6 py-3 text-red-600 border-b-2 border-red-600 focus:outline-none active"
                        data-tab="fra">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            FRA
                        </div>
                    </button>
                    <button
                        class="tab-btn text-sm font-medium px-6 py-3 text-gray-500 hover:text-gray-900 focus:outline-none"
                        data-tab="capaian-kinerja">
                        <div class="flex items-center">
                            <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                            Capaian Kinerja
                        </div>
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Tab: FRA (Original Cards) -->
                <div id="fra" class="tab-pane active">
                    <div class="p-6">
                        <!-- FRA Cards Container (preserving original layout) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="fraContainer">
                            @if ($fraList->count() > 0)
                                @foreach ($fraList as $fra)
                                    @if ($fra->status === 'Baru Dibuat' && ($isSuperAdmin || $isAdmin))
                                    @php
                                        // Determine status styling
                                        $statusConfig = [
                                            'Baru Dibuat' => [
                                                'gradient' => 'from-yellow-400 to-orange-500',
                                                'bg' => 'bg-yellow-50',
                                                'text' => 'text-yellow-700',
                                                'icon' => 'text-yellow-600',
                                                'border' => 'border-yellow-200',
                                            ],
                                            'Dalam Proses' => [
                                                'gradient' => 'from-blue-400 to-blue-600',
                                                'bg' => 'bg-blue-50',
                                                'text' => 'text-blue-700',
                                                'icon' => 'text-blue-600',
                                                'border' => 'border-blue-200',
                                            ],
                                            'Selesai' => [
                                                'gradient' => 'from-green-400 to-green-600',
                                                'bg' => 'bg-green-50',
                                                'text' => 'text-green-700',
                                                'icon' => 'text-green-600',
                                                'border' => 'border-green-200',
                                            ],
                                        ];
                                        $config = $statusConfig[$fra->status] ?? $statusConfig['Baru Dibuat'];
                                    @endphp

                                    <div class="fra-card bg-white border border-gray-100 rounded-xl shadow-sm overflow-visible hover:shadow-lg transition-all duration-300 group"
                                        data-year="{{ $fra->tahun_berjalan }}" data-status="{{ $fra->status }}">

                                        <!-- Header with gradient and background image -->
                                        @php
                                            $backgroundImages = ['bg1.jpg', 'bg2.jpg', 'bg3.jpg', 'bg4.jpg', 'bg5.jpg'];
                                            $randomBg = $backgroundImages[array_rand($backgroundImages)];
                                        @endphp
                                        <div class="relative h-32 bg-gradient-to-br {{ $config['gradient'] }} overflow-hidden" 
                                             style="background-image: url('{{ asset('img/' . $randomBg) }}'); background-size: cover; background-position: center; background-blend-mode: overlay;">
                                            <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                                            <div class="absolute top-4 right-4">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white bg-opacity-95 {{ $config['text'] }} shadow-sm">
                                                    <span
                                                        class="mr-1 w-2 h-2 rounded-full {{ str_replace('text-', 'bg-', $config['icon']) }}"></span>
                                                    {{ $fra->status }}
                                                </span>
                                            </div>
                                            <div class="absolute bottom-4 left-4 text-white">
                                                <h3 class="text-lg font-bold drop-shadow-lg">FRA {{ $fra->tahun_berjalan }}</h3>
                                                <p class="text-sm opacity-90 drop-shadow-md">Form Rencana Aksi</p>
                                            </div>
                                            <!-- Icon -->
                                            <div class="absolute top-4 left-4">
                                                @if ($fra->status === 'Selesai')
                                                    <div
                                                        class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                @elseif($fra->status === 'Dalam Proses')
                                                    <div
                                                        class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div
                                                        class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Status Banner -->
                                        @if ($fra->status === 'Baru Dibuat')
                                            <div class="{{ $config['bg'] }} px-4 py-3 {{ $config['border'] }} border-b">
                                                <p class="text-sm {{ $config['text'] }} flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="font-medium">Perlu Input Target</span>
                                                </p>
                                            </div>
                                        @elseif($fra->status === 'Dalam Proses')
                                            <div class="{{ $config['bg'] }} px-4 py-3 {{ $config['border'] }} border-b">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm {{ $config['text'] }} flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                                                            fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        <span class="font-medium">Sedang Berjalan</span>
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($fra->status === 'Selesai')
                                            <div class="{{ $config['bg'] }} px-4 py-3 {{ $config['border'] }} border-b">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm {{ $config['text'] }} flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                                                            fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        <span class="font-medium">Periode Pengisian Berakhir</span>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Card Content -->
                                        <div class="p-4">
                                            <!-- Progress for Dalam Proses -->
                                            @if ($fra->status === 'Dalam Proses')
                                                @php
                                                    $currentTriwulan = $fra->getCurrentTriwulan();
                                                    $progress = ($currentTriwulan / 4) * 100;
                                                @endphp
                                                <div class="mb-4">
                                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                                        <span>Progress Triwulan</span>
                                                        <span>{{ $currentTriwulan }}/4</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div class="bg-gradient-to-r {{ $config['gradient'] }} h-2 rounded-full transition-all duration-300"
                                                            style="width: {{ $progress }}%"></div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Action Buttons -->
                                            <div class="flex flex-wrap gap-2">
                                                @if ($fra->status === 'Baru Dibuat')
                                                    @php
                                        // Cek apakah target PK sudah ada untuk kegiatan PK (bukan FRA) tahun ini
                                        $pkKegiatan = \App\Models\Kegiatan::where('nama_kegiatan', "Pengukuran Kinerja {$fra->tahun_berjalan}")
                                            ->where('tahun_berjalan', $fra->tahun_berjalan)
                                            ->first();
                                        
                                        // Debug: Jika kegiatan PK tidak ditemukan, coba cari dengan pattern yang mirip
                                        if (!$pkKegiatan) {
                                            $pkKegiatan = \App\Models\Kegiatan::where('tahun_berjalan', $fra->tahun_berjalan)
                                                ->where(function($query) use ($fra) {
                                                    $query->where('nama_kegiatan', 'like', "%Pengukuran Kinerja%{$fra->tahun_berjalan}%")
                                                          ->orWhere('nama_kegiatan', 'like', "%PK%{$fra->tahun_berjalan}%")
                                                          ->orWhere('nama_kegiatan', 'like', "%Manajemen PK%{$fra->tahun_berjalan}%");
                                                })
                                                ->first();
                                        }
                                        
                                        // Fallback: Cari kegiatan apapun yang memiliki target_pk untuk tahun ini
                                        if (!$pkKegiatan) {
                                            $pkKegiatan = \App\Models\Kegiatan::whereIn('id', function($query) use ($fra) {
                                                $query->select('kegiatan_id')
                                                      ->from('target_pk')
                                                      ->whereNotNull('target_pk')
                                                      ->where('target_pk', '>', 0);
                                            })
                                            ->where('tahun_berjalan', $fra->tahun_berjalan)
                                            ->first();
                                        }
                                        
                                        $hasTargetPk = false;
                                        $debugInfo = '';
                                        if ($pkKegiatan) {
                                            // Debug: Cek semua target PK untuk kegiatan PK ini
                                            $allTargetPk = \App\Models\Target_Pk::where('kegiatan_id', $pkKegiatan->id)->get();
                                            $targetPkCount = $allTargetPk->where('target_pk', '!=', null)->where('target_pk', '>', 0)->count();
                                            $hasTargetPk = $targetPkCount > 0;
                                            
                                            // Debug info lebih lengkap
                                            $debugInfo = "Found PK kegiatan: {$pkKegiatan->nama_kegiatan} (ID: {$pkKegiatan->id}), All Target PK: " . $allTargetPk->count() . ", Valid Target PK: {$targetPkCount}";
                                            
                                            // Debug: Tampilkan sample data target PK
                                            if ($allTargetPk->isNotEmpty()) {
                                                $sampleTargetPk = $allTargetPk->take(3)->map(function($item) {
                                                    return "ID:{$item->id}, matriks_fra_id:{$item->matriks_fra_id}, target_pk:{$item->target_pk}";
                                                })->implode('; ');
                                                $debugInfo .= " | Sample: " . $sampleTargetPk;
                                            }
                                        } else {
                                            // Cari semua kegiatan dengan tahun yang sama untuk debugging
                                            $allKegiatanSameTahun = \App\Models\Kegiatan::where('tahun_berjalan', $fra->tahun_berjalan)->get();
                                            $kegiatanList = $allKegiatanSameTahun->map(function($k) {
                                                return "{$k->nama_kegiatan} (ID: {$k->id})";
                                            })->implode(', ');
                                            
                                            // Cek kegiatan mana yang memiliki target_pk
                                            $kegiatanWithTargetPk = \App\Models\Target_Pk::with('kegiatan')
                                                ->whereHas('kegiatan', function($q) use ($fra) {
                                                    $q->where('tahun_berjalan', $fra->tahun_berjalan);
                                                })
                                        ->whereNotNull('target_pk')
                                        ->where('target_pk', '>', 0)
                                                ->get()
                                                ->groupBy('kegiatan_id')
                                                ->map(function($items, $kegiatanId) {
                                                    $kegiatan = $items->first()->kegiatan;
                                                    return "{$kegiatan->nama_kegiatan} (ID: {$kegiatanId}, Count: {$items->count()})";
                                                })->implode(', ');
                                            
                                            $debugInfo = "Kegiatan PK tidak ditemukan. Kegiatan tahun {$fra->tahun_berjalan}: {$kegiatanList}. Kegiatan dengan Target PK: {$kegiatanWithTargetPk}";
                                        }
                                                    @endphp

                                                    @if ($hasTargetPk)
                                                        {{-- Card Baru Dibuat - Target PK sudah diisi --}}
                                                        <div class="flex space-x-2 w-full">
                                                            @if($isSuperAdmin || $isAdmin)
                                                            <a href="{{ route('form.target.fra', $fra->id) }}"
                                                                class="flex-1 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                                </svg>
                                                                Input Target
                                                            </a>
                                                            @endif
                                                            @if($isSuperAdmin)
                                                            <form id="deleteForm{{ $fra->id }}" action="{{ route('fra.destroy', $fra->id) }}" method="POST" class="flex-1">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button"
                                                                    onclick="confirmDeleteFRA({{ $fra->id }})"
                                                                    class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd"
                                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                    @else
                                                        {{-- Card Baru Dibuat - Target PK belum diisi --}}
                                                        <div class="flex space-x-2 w-full">
                                                            <div class="flex-1 bg-amber-50 border border-amber-200 p-3 rounded-lg text-sm text-amber-800">
                                                                <div class="flex items-start">
                                                                    <div class="flex-shrink-0">
                                                                        <svg class="h-4 w-4 text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                                        </svg>
                                                                    </div>
                                                                    <div class="ml-2">
                                                                        <p class="font-medium">Target PK Belum Diisi</p>
                                                                        <a href="{{ route('manajemen.pk') }}" class="text-xs underline hover:text-amber-600">
                                                                            Input Target PK {{ $fra->tahun_berjalan }}
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if($isSuperAdmin)
                                                            <form id="deleteForm{{ $fra->id }}" action="{{ route('fra.destroy', $fra->id) }}" method="POST" class="flex-1">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button"
                                                                    onclick="confirmDeleteFRA({{ $fra->id }})"
                                                                    class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd"
                                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @elseif($fra->status === 'Dalam Proses' || $fra->status === 'Selesai')

                                                    {{-- Card Dalam Proses - Edit Target | Lihat Triwulan | Hapus --}}
                                                    <div class="flex space-x-2 w-full">
                                                        @php
                                            $currentYear = date('Y');
                                            // Super admin bisa edit target kapan saja, admin hanya bisa edit jika FRA belum selesai atau tahun berjalan >= tahun sekarang
                                            $showEditTarget = $isSuperAdmin || ($isAdmin && ($fra->status !== 'Selesai' || $fra->tahun_berjalan >= $currentYear));
                                        @endphp
                                                        @if($showEditTarget)
                                                        <a href="{{ route('form.target.fra', $fra->id) }}"
                                                            class="flex-1 bg-amber-600 hover:bg-amber-700 text-white px-2 py-2 rounded-lg text-sm flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit Target
                                                        </a>
                                                        @endif
                                                        <button
                                                            class="fra-detail-btn flex-1 bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md"
                                                            data-fra-id="{{ $fra->id }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                            </svg>
                                                            Lihat Triwulan
                                                        </button>

                                                        @if($isSuperAdmin)
                                                        <form id="deleteForm{{ $fra->id }}" action="{{ route('fra.destroy', $fra->id) }}" method="POST" class="flex-1">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                onclick="confirmDeleteFRA({{ $fra->id }})"
                                                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd"
                                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                                Hapus
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @elseif ($fra->status === 'Dalam Proses' || $fra->status === 'Selesai')
                                    @php
                                        // Determine status styling
                                        $statusConfig = [
                                            'Baru Dibuat' => [
                                                'gradient' => 'from-yellow-400 to-orange-500',
                                                'bg' => 'bg-yellow-50',
                                                'text' => 'text-yellow-700',
                                                'icon' => 'text-yellow-600',
                                                'border' => 'border-yellow-200',
                                            ],
                                            'Dalam Proses' => [
                                                'gradient' => 'from-blue-400 to-blue-600',
                                                'bg' => 'bg-blue-50',
                                                'text' => 'text-blue-700',
                                                'icon' => 'text-blue-600',
                                                'border' => 'border-blue-200',
                                            ],
                                            'Selesai' => [
                                                'gradient' => 'from-green-400 to-green-600',
                                                'bg' => 'bg-green-50',
                                                'text' => 'text-green-700',
                                                'icon' => 'text-green-600',
                                                'border' => 'border-green-200',
                                            ],
                                        ];
                                        $config = $statusConfig[$fra->status] ?? $statusConfig['Baru Dibuat'];
                                    @endphp

                                    <div class="fra-card bg-white border border-gray-100 rounded-xl shadow-sm overflow-visible hover:shadow-lg transition-all duration-300 group"
                                        data-year="{{ $fra->tahun_berjalan }}" data-status="{{ $fra->status }}">

                                        <!-- Header with gradient and background image -->
                                        @php
                                            $backgroundImages = ['bg1.jpg', 'bg2.jpg', 'bg3.jpg', 'bg4.jpg', 'bg5.jpg'];
                                            $randomBg = $backgroundImages[array_rand($backgroundImages)];
                                        @endphp
                                        <div class="relative h-32 bg-gradient-to-br {{ $config['gradient'] }} overflow-hidden" 
                                             style="background-image: url('{{ asset('img/' . $randomBg) }}'); background-size: cover; background-position: center; background-blend-mode: overlay;">
                                            <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                                            <div class="absolute top-4 right-4">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white bg-opacity-95 {{ $config['text'] }} shadow-sm">
                                                    <span
                                                        class="mr-1 w-2 h-2 rounded-full {{ str_replace('text-', 'bg-', $config['icon']) }}"></span>
                                                    {{ $fra->status }}
                                                </span>
                                            </div>
                                            <div class="absolute bottom-4 left-4 text-white">
                                                <h3 class="text-lg font-bold drop-shadow-lg">FRA {{ $fra->tahun_berjalan }}</h3>
                                                <p class="text-sm opacity-90 drop-shadow-md">Form Rencana Aksi</p>
                                            </div>
                                            <!-- Icon -->
                                            <div class="absolute top-4 left-4">
                                                @if ($fra->status === 'Selesai')
                                                    <div
                                                        class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                @elseif($fra->status === 'Dalam Proses')
                                                    <div
                                                        class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div
                                                        class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Status Banner -->
                                        @if ($fra->status === 'Baru Dibuat')
                                            <div class="{{ $config['bg'] }} px-4 py-3 {{ $config['border'] }} border-b">
                                                <p class="text-sm {{ $config['text'] }} flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="font-medium">Perlu Input Target</span>
                                                </p>
                                            </div>
                                        @elseif($fra->status === 'Dalam Proses')
                                            <div class="{{ $config['bg'] }} px-4 py-3 {{ $config['border'] }} border-b">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm {{ $config['text'] }} flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                                                            fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        <span class="font-medium">Sedang Berjalan</span>
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($fra->status === 'Selesai' || $fra->tahun_berjalan < date('Y'))
                                            <div class="{{ $config['bg'] }} px-4 py-3 {{ $config['border'] }} border-b">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm {{ $config['text'] }} flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                                                            fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        <span class="font-medium">Periode Pengisian Berakhir</span>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Card Content -->
                                        <div class="p-4">
                                            <!-- Progress for Dalam Proses -->
                                            @if ($fra->status === 'Dalam Proses')
                                                @php
                                                    $currentTriwulan = $fra->getCurrentTriwulan();
                                                    $progress = ($currentTriwulan / 4) * 100;
                                                @endphp
                                                <div class="mb-4">
                                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                                        <span>Progress Triwulan</span>
                                                        <span>{{ $currentTriwulan }}/4</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div class="bg-gradient-to-r {{ $config['gradient'] }} h-2 rounded-full transition-all duration-300"
                                                            style="width: {{ $progress }}%"></div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Action Buttons -->
                                            <div class="flex flex-wrap gap-2">
                                                @if ($fra->status === 'Baru Dibuat')
                                                    @php
                                        // Cek apakah target PK sudah ada untuk kegiatan PK (bukan FRA) tahun ini
                                        $pkKegiatan = \App\Models\Kegiatan::where('nama_kegiatan', "Pengukuran Kinerja {$fra->tahun_berjalan}")
                                            ->where('tahun_berjalan', $fra->tahun_berjalan)
                                            ->first();
                                        
                                        // Debug: Jika kegiatan PK tidak ditemukan, coba cari dengan pattern yang mirip
                                        if (!$pkKegiatan) {
                                            $pkKegiatan = \App\Models\Kegiatan::where('tahun_berjalan', $fra->tahun_berjalan)
                                                ->where(function($query) use ($fra) {
                                                    $query->where('nama_kegiatan', 'like', "%Pengukuran Kinerja%{$fra->tahun_berjalan}%")
                                                          ->orWhere('nama_kegiatan', 'like', "%PK%{$fra->tahun_berjalan}%")
                                                          ->orWhere('nama_kegiatan', 'like', "%Manajemen PK%{$fra->tahun_berjalan}%");
                                                })
                                                ->first();
                                        }
                                        
                                        // Fallback: Cari kegiatan apapun yang memiliki target_pk untuk tahun ini
                                        if (!$pkKegiatan) {
                                            $pkKegiatan = \App\Models\Kegiatan::whereIn('id', function($query) use ($fra) {
                                                $query->select('kegiatan_id')
                                                      ->from('target_pk')
                                                      ->whereNotNull('target_pk')
                                                      ->where('target_pk', '>', 0);
                                            })
                                            ->where('tahun_berjalan', $fra->tahun_berjalan)
                                            ->first();
                                        }
                                        
                                        $hasTargetPk = false;
                                        $debugInfo = '';
                                        if ($pkKegiatan) {
                                            // Debug: Cek semua target PK untuk kegiatan PK ini
                                            $allTargetPk = \App\Models\Target_Pk::where('kegiatan_id', $pkKegiatan->id)->get();
                                            $targetPkCount = $allTargetPk->where('target_pk', '!=', null)->where('target_pk', '>', 0)->count();
                                            $hasTargetPk = $targetPkCount > 0;
                                            
                                            // Debug info lebih lengkap
                                            $debugInfo = "Found PK kegiatan: {$pkKegiatan->nama_kegiatan} (ID: {$pkKegiatan->id}), All Target PK: " . $allTargetPk->count() . ", Valid Target PK: {$targetPkCount}";
                                            
                                            // Debug: Tampilkan sample data target PK
                                            if ($allTargetPk->isNotEmpty()) {
                                                $sampleTargetPk = $allTargetPk->take(3)->map(function($item) {
                                                    return "ID:{$item->id}, matriks_fra_id:{$item->matriks_fra_id}, target_pk:{$item->target_pk}";
                                                })->implode('; ');
                                                $debugInfo .= " | Sample: " . $sampleTargetPk;
                                            }
                                        } else {
                                            // Cari semua kegiatan dengan tahun yang sama untuk debugging
                                            $allKegiatanSameTahun = \App\Models\Kegiatan::where('tahun_berjalan', $fra->tahun_berjalan)->get();
                                            $kegiatanList = $allKegiatanSameTahun->map(function($k) {
                                                return "{$k->nama_kegiatan} (ID: {$k->id})";
                                            })->implode(', ');
                                            
                                            // Cek kegiatan mana yang memiliki target_pk
                                            $kegiatanWithTargetPk = \App\Models\Target_Pk::with('kegiatan')
                                                ->whereHas('kegiatan', function($q) use ($fra) {
                                                    $q->where('tahun_berjalan', $fra->tahun_berjalan);
                                                })
                                        ->whereNotNull('target_pk')
                                        ->where('target_pk', '>', 0)
                                                ->get()
                                                ->groupBy('kegiatan_id')
                                                ->map(function($items, $kegiatanId) {
                                                    $kegiatan = $items->first()->kegiatan;
                                                    return "{$kegiatan->nama_kegiatan} (ID: {$kegiatanId}, Count: {$items->count()})";
                                                })->implode(', ');
                                            
                                            $debugInfo = "Kegiatan PK tidak ditemukan. Kegiatan tahun {$fra->tahun_berjalan}: {$kegiatanList}. Kegiatan dengan Target PK: {$kegiatanWithTargetPk}";
                                        }
                                                    @endphp

                                                    @if ($hasTargetPk)
                                                        {{-- Card Baru Dibuat - Target PK sudah diisi --}}
                                                    <div class="flex space-x-2 w-full">
                                                            @if($isSuperAdmin || $isAdmin)
                                                            <a href="{{ route('form.target.fra', $fra->id) }}"
                                                                class="flex-1 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                                </svg>
                                                                Input Target
                                                            </a>
                                                            @endif
                                                            @if($isSuperAdmin)
                                                            <form id="deleteForm{{ $fra->id }}" action="{{ route('fra.destroy', $fra->id) }}" method="POST" class="flex-1">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button"
                                                                    onclick="confirmDeleteFRA({{ $fra->id }})"
                                                                    class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd"
                                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                    @else
                                                        {{-- Card Baru Dibuat - Target PK belum diisi --}}
                                                        <div class="flex space-x-2 w-full">
                                                            <div class="flex-1 bg-amber-50 border border-amber-200 p-3 rounded-lg text-sm text-amber-800">
                                                                <div class="flex items-start">
                                                                    <div class="flex-shrink-0">
                                                                        <svg class="h-4 w-4 text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                                        </svg>
                                                                    </div>
                                                                    <div class="ml-2">
                                                                        <p class="font-medium">Target PK Belum Diisi</p>
                                                                        <a href="{{ route('manajemen.pk') }}" class="text-xs underline hover:text-amber-600">
                                                                            Input Target PK {{ $fra->tahun_berjalan }}
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if($isSuperAdmin)
                                                            <form id="deleteForm{{ $fra->id }}" action="{{ route('fra.destroy', $fra->id) }}" method="POST" class="flex-1">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button"
                                                                    onclick="confirmDeleteFRA({{ $fra->id }})"
                                                                    class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd"
                                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endif
                                                @if ($fra->status === 'Dalam Proses' || $fra->status === 'Selesai')
                                                    {{-- Card Dalam Proses - Edit Target | Lihat Triwulan | Hapus --}}
                                                    <div class="flex space-x-2 w-full">
                                                        @php
                                                            $currentYear = date('Y');
                                                            // Super admin bisa edit target kapan saja, admin hanya bisa edit jika FRA belum selesai atau tahun berjalan >= tahun sekarang
                                                            $showEditTarget = $isSuperAdmin || ($isAdmin && ($fra->status !== 'Selesai' || $fra->tahun_berjalan >= $currentYear));
                                                        @endphp
                                                        @if($showEditTarget)
                                                        <a href="{{ route('form.target.fra', $fra->id) }}"
                                                            class="flex-1 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-2 py-2 rounded-lg text-sm flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit Target
                                                        </a>
                                                        @endif
                                                        <button
                                                            class="fra-detail-btn flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md"
                                                            data-fra-id="{{ $fra->id }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                            </svg>
                                                            Lihat Triwulan
                                                        </button>

                                                        @if($isSuperAdmin)
                                                        <form id="deleteForm{{ $fra->id }}" action="{{ route('fra.destroy', $fra->id) }}" method="POST" class="flex-1">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                onclick="confirmDeleteFRA({{ $fra->id }})"
                                                                class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd"
                                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                                Hapus
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="col-span-3 py-12 text-center">
                                    <div class="max-w-md mx-auto">
                                        <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada FRA</h3>
                                        <p class="text-gray-500 mb-6">Mulai dengan membuat Form Rencana Aksi pertama Anda.</p>
                                        <button onclick="document.getElementById('btnTambahFRA').click()"
                                            class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-3 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 inline" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Buat FRA Pertama
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Custom Pagination Links for FRA -->
                        <div class="mt-8">
                            @php
                                $currentPage = $fraList->currentPage();
                                $totalItems = $fraList->total();
                                $perPage = $fraList->perPage();
                                $totalPages = max(ceil($totalItems / $perPage), 1);
                                $hasNextPage = $currentPage < $totalPages;
                                $hasPrevPage = $currentPage > 1;
                                $currentItemsCount = min($perPage, $totalItems - (($currentPage - 1) * $perPage));
                            @endphp
                            
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="flex items-center justify-between px-6 py-4">
                                    <!-- Info Text -->
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-600">
                                            Menampilkan <span class="font-semibold text-gray-900">{{ $currentItemsCount }}</span> dari <span class="font-semibold text-gray-900">{{ $totalItems }}</span>
                                        </span>
                                    </div>
                                    
                                    <!-- Pagination Controls -->
                                    <div class="flex items-center space-x-2">
                                        <!-- Previous Button -->
                                        @if($hasPrevPage)
                                            <a href="{{ $fraList->appends(['tab' => request('tab', 'fra')])->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                </svg>
                                            </span>
                                        @endif
                                        
                                        <!-- Page Numbers -->
                                        <div class="flex items-center space-x-1">
                                            @if($totalPages > 1)
                                                @for($i = 1; $i <= $totalPages; $i++)
                                                    @if($i == $currentPage)
                                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-600 text-white text-sm font-medium">
                                                            {{ $i }}
                                                        </span>
                                                    @else
                                                        <a href="{{ $fraList->appends(['tab' => request('tab', 'fra')])->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                                                            {{ $i }}
                                                        </a>
                                                    @endif
                                                @endfor
                                            @else
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-600 text-white text-sm font-medium">
                                                    1
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <!-- Next Button -->
                                        @if($hasNextPage)
                                            <a href="{{ $fraList->appends(['tab' => request('tab', 'fra')])->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Capaian Kinerja -->
                <div id="capaian-kinerja" class="tab-pane">
                    <div class="p-6">
                        <!-- Header dengan breadcrumb mini -->
                        <div class="flex items-center mb-6 text-sm">
                            <a href="#" class="text-gray-500 hover:text-gray-700"
                                onclick="document.querySelector('.tab-btn[data-tab=\'fra\']').click(); return false;">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali ke FRA
                            </a>
                            <span class="mx-2 text-gray-400">•</span>
                            <span class="flex items-center">
                                <span class="w-2 h-2 rounded-full bg-blue-500 mr-1"></span>
                                <span class="font-medium text-gray-900">Capaian Kinerja</span>
                            </span>
                        </div>

                        <div class="mb-6 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Capaian Kinerja FRA</h3>
                                <p class="text-sm text-gray-500">Laporan pencapaian kinerja berdasarkan Form Rencana Aksi</p>
                            </div>
                            <button
                                class="flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm transition-colors"
                                id="btnTambahCapaianKinerja">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Kegiatan
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm" id="capaianKinerjaTable">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100">
                                        <th class="py-2.5 px-3 text-left font-semibold text-gray-900 text-xs">Kegiatan</th>
                                        <th class="py-2.5 px-3 text-left font-semibold text-gray-900 text-xs">Keterangan</th>
                                        <th class="py-2.5 px-3 text-center font-semibold text-gray-900 text-xs">Status</th>
                                        <th class="py-2.5 px-3 text-center font-semibold text-gray-900 text-xs">Kelengkapan</th>
                                        <th class="py-2.5 px-3 text-center font-semibold text-gray-900 text-xs w-20">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Use actual capaian kinerja activities from database
                                        $capaianKinerjaItems = $capaianKinerjaActivities->map(function($kegiatan) {
                                            $currentDate = now();
                                            $startDate = \Carbon\Carbon::parse($kegiatan->tanggal_mulai);
                                            $endDate = \Carbon\Carbon::parse($kegiatan->tanggal_berakhir);
                                            
                                            // Determine status based on dates
                                            if ($currentDate->lt($startDate)) {
                                                $status = 'Upcoming';
                                                $statusClass = 'bg-purple-100 text-purple-800';
                                                $statusDot = 'bg-purple-500';
                                            } elseif ($currentDate->lte($endDate)) {
                                                $status = 'Sedang Berlangsung';
                                                $statusClass = 'bg-blue-100 text-blue-800';
                                                $statusDot = 'bg-blue-500';
                                            } else {
                                                $status = 'Terlambat';
                                                $statusClass = 'bg-red-100 text-red-800';
                                                $statusDot = 'bg-red-500';
                                            }
                                            
                                            // Check completeness based on uploaded documents
                                            $uploadedCount = $kegiatan->buktiDukung()->count();
                                            $kelengkapan = $uploadedCount > 0;
                                            
                                            return (object) [
                                                'id' => $kegiatan->id,
                                                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                                                'tahun_berjalan' => $kegiatan->tahun_berjalan,
                                                'status' => $status,
                                                'status_class' => $statusClass,
                                                'status_dot' => $statusDot,
                                                'kelengkapan' => $kelengkapan,
                                                'keterangan' => 'Laporan capaian kinerja berdasarkan FRA tahun ' . $kegiatan->tahun_berjalan,
                                                'tanggal_mulai' => $startDate->format('d M Y'),
                                                'tanggal_berakhir' => $endDate->format('d M Y'),
                                            ];
                                        });
                                    @endphp

                                    @forelse($capaianKinerjaItems as $item)
                                        <tr class="border-b hover:bg-gray-50 transition-colors"
                                            data-year="{{ $item->tahun_berjalan }}"
                                            data-status="{{ $item->status }}"
                                            data-search="{{ strtolower($item->nama_kegiatan) }} {{ $item->tahun_berjalan }}">
                                            <td class="py-2.5 px-3">
                                                <div class="flex items-center">
                                                    <span class="text-sm">{{ $item->nama_kegiatan }}</span>
                                                </div>
                                            </td>
                                            <td class="py-2.5 px-3 text-gray-600 text-sm">{{ $item->keterangan }}</td>
                                            <td class="py-2.5 px-3 text-center">
                                                <div class="flex justify-center">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $item->status_class }}">
                                                        <span
                                                            class="mr-1 w-1.5 h-1.5 rounded-full {{ $item->status_dot }}"></span>
                                                        {{ $item->status }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-2.5 px-3 text-center">
                                                <div class="flex justify-center">
                                                    @if ($item->kelengkapan)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-3 w-3 mr-1 text-green-600" viewBox="0 0 20 20"
                                                                fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            Lengkap
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-3 w-3 mr-1 text-red-600" viewBox="0 0 20 20"
                                                                fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            Belum Lengkap
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-2.5 px-3 text-center">
                                                <a href="{{ route('capaian.kinerja.detail', ['id' => $item->id]) }}"
                                                    class="inline-flex items-center justify-center p-1 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors"
                                                    title="Lihat Detail">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="border-b">
                                            <td colspan="5" class="py-6 text-center text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                                <h3 class="text-base font-medium mb-1">Tidak ada data capaian kinerja</h3>
                                                <p class="text-sm">Belum ada laporan capaian kinerja yang tersedia saat ini.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination untuk Capaian Kinerja -->
                        @if (isset($capaianKinerjaActivities) && $capaianKinerjaActivities->total() > 0)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    @php
                                        $currentPage = max(1, $capaianKinerjaActivities->currentPage());
                                        $totalItems = $capaianKinerjaActivities->total();
                                        $perPage = $capaianKinerjaActivities->perPage();
                                        $totalPages = max(ceil($totalItems / max(1, $perPage)), 1);
                                        $hasNextPage = $currentPage < $totalPages;
                                        $hasPrevPage = $currentPage > 1;
                                        $currentItemsCount = min($perPage, $totalItems - (($currentPage - 1) * $perPage));
                                    @endphp
                                    
                                    <div class="flex items-center">
                                        <span>Menampilkan</span>
                                        <span class="font-semibold text-gray-900 mx-1">{{ $currentPage }}</span>
                                        <span>dari</span>
                                        <span class="font-semibold text-gray-900 mx-1">{{ $totalPages }}</span>
                                        <span>halaman</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center mr-2">
                                        <select id="perPageSelectCapaianKinerja" class="ml-2 border border-gray-300 rounded-md shadow-sm text-sm py-1 px-2">
                                            @foreach([5, 10, 15, 25, 50] as $option)
                                                <option value="{{ $option }}" @if($perPage == $option) selected @endif>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- Previous Page Link --}}
                                    @if ($hasPrevPage)
                                        <a href="{{ $capaianKinerjaActivities->appends(request()->except('page'))->previousPageUrl() }}" class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 border border-gray-300 bg-gray-50 cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </span>
                                    @endif
                                    
                                    {{-- Page Numbers --}}
                                    <div class="flex items-center space-x-1">
                                        @php
                                            $startPage = max(1, $currentPage - 2);
                                            $endPage = min($totalPages, $currentPage + 2);
                                            
                                            if ($startPage === 1 && $totalPages > 5) {
                                                $endPage = 5;
                                            } elseif ($endPage === $totalPages && $totalPages > 5) {
                                                $startPage = $totalPages - 4;
                                            }
                                        @endphp

                                        @if ($startPage > 1)
                                            <a href="{{ $capaianKinerjaActivities->appends(request()->except('page'))->url(1) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 text-sm font-medium transition-colors">1</a>
                                            @if ($startPage > 2)
                                                <span class="text-gray-500">...</span>
                                            @endif
                                        @endif

                                        @for ($i = $startPage; $i <= $endPage; $i++)
                                            @if ($i == $currentPage)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-600 text-white text-sm font-medium">
                                                    {{ $i }}
                                                </span>
                                            @else
                                                <a href="{{ $capaianKinerjaActivities->appends(request()->except('page'))->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 text-sm font-medium transition-colors">
                                                    {{ $i }}
                                                </a>
                                            @endif
                                        @endfor

                                        @if ($endPage < $totalPages)
                                            @if ($endPage < $totalPages - 1)
                                                <span class="text-gray-500">...</span>
                                            @endif
                                            <a href="{{ $capaianKinerjaActivities->appends(request()->except('page'))->url($totalPages) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 text-sm font-medium transition-colors">{{ $totalPages }}</a>
                                        @endif
                                    </div>
                                    
                                    {{-- Next Page Link --}}
                                    @if ($hasNextPage)
                                        <a href="{{ $capaianKinerjaActivities->appends(request()->except('page'))->nextPageUrl() }}" class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 border border-gray-300 bg-gray-50 cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Triwulan -->
    <div id="modalDetailTriwulan"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden transform transition-all duration-300 scale-95 opacity-0"
            id="modalTriwulanContent">
            <div class="flex flex-col h-full">
                <!-- Modal Header -->
                <div
                    class="flex justify-between items-center p-6 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-700">
                    <div class="text-white">
                        <h3 class="text-xl font-bold">Detail Progress Triwulan</h3>
                        <p class="text-blue-100 text-sm mt-1">Kelola realisasi per triwulan</p>
                    </div>
                    <button id="closeTriwulanModal" class="text-white hover:text-blue-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto p-6" id="triwulanDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah FRA -->
    <div id="modalTambahFRA"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0"
            id="modalContent">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Tambah Form Rencana Aksi</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('fra.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="fraForm" data-no-loading="true">
                    @csrf
                    <div>
                        <label for="nama_fra" class="block text-sm font-medium text-gray-700 mb-2">Nama Kegiatan</label>
                        <input type="text" id="nama_fra" name="nama_fra" required
                            class="w-full px-4 py-3 border border-gray-300 bg-gray-100 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                            value="Form Rencana Aksi" readonly>
                    </div>

                    <div>
                        <label for="tahun_berjalan" class="block text-sm font-medium text-gray-700 mb-2">Tahun
                            Berjalan</label>
                        <select id="tahun_berjalan" name="tahun_berjalan" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <option value="">Pilih Tahun</option>
                        </select>
                    </div>

                    <div>
                        <label for="template_file" class="block text-sm font-medium text-gray-700 mb-2">Upload
                            Template</label>
                        <div class="relative">
                            <input type="file" id="template_file" name="template_file" accept=".xlsx,.xls" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Format: .xlsx atau .xls</p>


                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button type="button" id="cancelModal"
                            class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                            Batal
                        </button>
                        <button type="submit" id="submitFraButton"
                            class="flex-1 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-3 rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl"
                            onclick="return true;">
                            <span class="flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Simpan
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div id="successMessage"
            class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div id="errorMessage"
            class="fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <script>
        // Function untuk konfirmasi hapus FRA
        function confirmDeleteFRA(fraId) {
            confirmDelete('Apakah Anda yakin ingin menghapus FRA ini? Tindakan ini tidak dapat dibatalkan.', function() {
                // Show loading indicator
                showLoading('Menghapus FRA... Mohon tunggu sebentar.');
                
                // Submit form dengan fetch untuk kontrol lebih baik
                const deleteForm = document.getElementById('deleteForm' + fraId);
                const formData = new FormData(deleteForm);

                fetch(deleteForm.action, {
                    method: 'DELETE',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showSuccess(data.message || 'FRA berhasil dihapus');
                        // Hapus card FRA dari DOM
                        const fraCard = deleteForm.closest('.fra-card');
                        if (fraCard) {
                            fraCard.remove();
                        }
                        // Refresh halaman atau update list FRA
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showError(data.message || 'Gagal menghapus FRA');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showError('Terjadi kesalahan saat menghapus FRA');
                });
            });
        }
        
        // Test function untuk debugging
        function testModal() {
            console.log('Testing modal...');
            showModal('warning', 'Test Modal', 'Ini adalah test modal', {
                confirmText: 'OK',
                cancelText: 'Batal',
                showCancel: true,
                confirmCallback: function() { /* Test confirmed */ },
                cancelCallback: function() { /* Test cancelled */ }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // ========== TAB FUNCTIONALITY ==========
            function initTabNavigation() {
                const tabBtns = document.querySelectorAll('.tab-btn');
                const tabPanes = document.querySelectorAll('.tab-pane');

                function switchTab(targetTab) {
                    // Deactivate all tabs
                    tabBtns.forEach(btn => {
                        btn.classList.remove('text-red-600', 'border-b-2', 'border-red-600', 'active');
                        btn.classList.add('text-gray-500');
                    });

                    // Hide all tab panes
                    tabPanes.forEach(pane => {
                        pane.classList.remove('active');
                        pane.style.display = 'none';
                    });

                    // Activate the target tab
                    const targetBtn = document.querySelector(`.tab-btn[data-tab="${targetTab}"]`);
                    const targetPane = document.getElementById(targetTab);

                    if (targetBtn && targetPane) {
                        targetBtn.classList.remove('text-gray-500');
                        targetBtn.classList.add('text-red-600', 'border-b-2', 'border-red-600', 'active');

                        targetPane.classList.add('active');
                        targetPane.style.display = 'block';
                    }
                }

                // Add click event listeners to all tab buttons
                tabBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const tabId = this.getAttribute('data-tab');
                        switchTab(tabId);
                    });
                });

                // Initialize the first tab as active
                switchTab('fra');
            }

            // Initialize tab navigation
            initTabNavigation();

            // Modal functionality
            const modal = document.getElementById('modalTambahFRA');
            const modalContent = document.getElementById('modalContent');
            const btnTambah = document.getElementById('btnTambahFRA');
            const closeModal = document.getElementById('closeModal');
            const cancelModal = document.getElementById('cancelModal');

            // Triwulan modal
            const triwulanModal = document.getElementById('modalDetailTriwulan');
            const triwulanModalContent = document.getElementById('modalTriwulanContent');
            const closeTriwulanModal = document.getElementById('closeTriwulanModal');

            function openModal() {
                if (modal && modalContent) {
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        modalContent.classList.add('show');
                    }, 10);
                }
            }

            function closeModalFunc() {
                if (modal && modalContent) {
                    modalContent.classList.remove('show');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                    }, 300);
                }
            }

            function openTriwulanModal() {
                if (triwulanModal && triwulanModalContent) {
                    triwulanModal.classList.remove('hidden');
                    setTimeout(() => {
                        triwulanModalContent.classList.add('show');
                    }, 10);
                }
            }

            function closeTriwulanModalFunc() {
                if (triwulanModal && triwulanModalContent) {
                    triwulanModalContent.classList.remove('show');
                    setTimeout(() => {
                        triwulanModal.classList.add('hidden');
                    }, 300);
                }
            }

            if (btnTambah) btnTambah.addEventListener('click', openModal);
                    if (closeModal) closeModal.addEventListener('click', closeModalFunc);
        if (cancelModal) cancelModal.addEventListener('click', closeModalFunc);
        if (closeTriwulanModal) closeTriwulanModal.addEventListener('click', closeTriwulanModalFunc);

        // Close modals with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (modal && !modal.classList.contains('hidden')) {
                    closeModalFunc();
                }
                if (triwulanModal && !triwulanModal.classList.contains('hidden')) {
                    closeTriwulanModalFunc();
                }
                if (modalCapaianKinerja && !modalCapaianKinerja.classList.contains('hidden')) {
                    closeCapaianKinerjaModalFunc();
                }
            }
        });

            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeModalFunc();
                    }
                });
            }

            if (triwulanModal) {
                triwulanModal.addEventListener('click', function(e) {
                    if (e.target === triwulanModal) {
                        closeTriwulanModalFunc();
                    }
                });
            }

            // Populate year dropdown
            const yearSelect = document.getElementById('tahun_berjalan');
            if (yearSelect) {
                const currentYear = new Date().getFullYear();
                for (let year = currentYear + 2; year >= currentYear - 5; year--) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    if (year === currentYear) {
                        option.selected = true;
                    }
                    yearSelect.appendChild(option);
                }
            }

            // File input display
            const fileInput = document.getElementById('template_file');
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    const fileName = this.files[0]?.name;
                    if (fileName) {
                        // File selected: fileName
                    }
                });
            }

            // 🔥 FIXED: Form submission dengan handling yang lebih robust
            const fraForm = document.getElementById('fraForm');
            const submitButton = document.getElementById('submitFraButton');
            
            if (fraForm && submitButton) {
                // FRA Form and Submit Button found
                
                // Reset semua interference
                fraForm.onsubmit = null;
                submitButton.disabled = false;
                submitButton.style.pointerEvents = 'auto';
                submitButton.style.opacity = '1';
                
                // Tambahkan data-no-loading untuk mencegah interference dari global loading system
                submitButton.setAttribute('data-no-loading', 'true');
                fraForm.setAttribute('data-no-loading', 'true');
                
                // Handler khusus untuk form submission
                fraForm.addEventListener('submit', function(e) {
                    // Form validation: namaFra, tahunBerjalan, templateFile
                    const namaFra = document.getElementById('nama_fra').value.trim();
                    const tahunBerjalan = document.getElementById('tahun_berjalan').value;
                    const templateFile = document.getElementById('template_file').files[0];
                    
                    if (!namaFra || !tahunBerjalan || !templateFile) {
                        e.preventDefault();
                        showModal('warning', 'Data Tidak Lengkap', 'Mohon lengkapi semua field yang diperlukan');
                        return false;
                    }
                    
                    // Validasi file type
                    const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
                    if (!allowedTypes.includes(templateFile.type)) {
                        e.preventDefault();
                        showModal('warning', 'Tipe File Tidak Valid', 'Hanya file Excel (.xlsx, .xls) yang diperbolehkan');
                        return false;
                    }

                    // Prevent default form submission
                    e.preventDefault();

                    // Tutup modal form secara otomatis
                    const modalTambahFRA = document.getElementById('modalTambahFRA');
                    const modalContent = document.getElementById('modalContent');
                    
                    // Animasi penutupan modal
                    modalContent.classList.remove('scale-100', 'opacity-100');
                    modalContent.classList.add('scale-95', 'opacity-0');
                    
                    setTimeout(() => {
                        modalTambahFRA.classList.add('hidden');
                    }, 300); // Sesuaikan dengan durasi transisi di CSS

                    // Gunakan fetch untuk submit form dengan ajax
                    const formData = new FormData(fraForm);

                    // Tampilkan loading
                    showLoading('Sedang membuat FRA... Mohon tunggu');

                    fetch(fraForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Sembunyikan loading
                        hideLoading();

                        if (data.success) {
                            // Gunakan showSuccess untuk konsistensi
                            showSuccess('FRA, folder Capaian Kinerja, dan folder Form Rencana Aksi berhasil dibuat.');
                            
                            // Reload halaman setelah sukses
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            // Tampilkan modal error
                            showModal('warning', 'Gagal', data.message);
                        }
                    })
                    .catch(error => {
                        // Sembunyikan loading
                        hideLoading();

                        // Tampilkan modal error
                        showModal('warning', 'Kesalahan', 'Terjadi kesalahan saat menghubungi server');
                        console.error('Error:', error);
                    });
                });
                
                // Handler untuk tombol submit (backup)
                submitButton.addEventListener('click', function(e) {
                    // Submit button clicked directly
                    
                    // Jika form invalid, trigger form validation
                    if (!fraForm.checkValidity()) {
                        e.preventDefault();
                        fraForm.reportValidity();
                        return false;
                    }
                });
                
                // Form handler setup complete
            } else {
                // FRA Form or Submit Button not found - check DOM structure
            }

            // Detail triwulan functionality
            document.querySelectorAll('.fra-detail-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const fraId = this.dataset.fraId;
                    loadTriwulanDetails(fraId);
                    openTriwulanModal();
                });
            });

            function loadTriwulanDetails(fraId) {
                const contentDiv = document.getElementById('triwulanDetailContent');
                contentDiv.innerHTML =
                    '<div class="flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';

                // Fetch data from server
                fetch(`{{ url('/') }}/pengukuran-kinerja/fra/${fraId}/triwulan-details`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', Object.fromEntries(response.headers.entries()));
                    
                    // Periksa tipe konten
                    const contentType = response.headers.get('content-type');
                    console.log('Content-Type:', contentType);

                    if (!contentType || !contentType.includes('application/json')) {
                        return response.text().then(text => {
                            console.error('Non-JSON response:', text);
                            throw new Error('Server mengembalikan respons yang tidak valid. Silakan coba lagi.');
                        });
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    
                    // Validasi struktur data
                    if (!data || typeof data !== 'object') {
                        throw new Error('Data yang diterima tidak valid');
                    }
                    
                    if (data.success && data.data && Array.isArray(data.data.triwulans)) {
                        generateTriwulanContent(data.data);
                    } else {
                        console.error('Invalid data structure:', data);
                        // Tampilkan pesan error yang lebih spesifik dari server
                        const errorMessage = data.message || 'Gagal memuat data triwulan';
                        throw new Error(errorMessage);
                    }
                })
                .catch(error => {
                    console.error('Fetch/parsing error:', error);
                    
                    // Tampilkan pesan error yang user-friendly
                    let displayMessage = error.message;
                    
                    // Jika error mengandung kata-kata teknis, berikan pesan yang lebih ramah
                    if (error.message.includes('JSON') || error.message.includes('parsing')) {
                        displayMessage = 'Terjadi kesalahan teknis saat memuat data. Silakan refresh halaman dan coba lagi.';
                    }
                    
                    contentDiv.innerHTML =
                        `<div class="text-center py-8 text-red-600">
                            <div class="mb-4">
                                <svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <p class="text-lg font-medium mb-2">Tidak dapat memuat data triwulan</p>
                            <p class="text-sm text-gray-600">${displayMessage}</p>
                        </div>`;
                });
            }

            function generateTriwulanContent(data) {
                const contentDiv = document.getElementById('triwulanDetailContent');
                const { fra_id: fraId, fra_year: year, triwulans, user_roles } = data;

                // Tambahkan validasi untuk mencegah undefined
                if (!triwulans || !Array.isArray(triwulans)) {
                    contentDiv.innerHTML = `
                        <div class="text-center py-8 text-red-600">
                            Data triwulan tidak valid atau kosong
                        </div>
                    `;
                    return;
                }
                
                // Extract user roles
                const isTeamMember = user_roles ? user_roles.isTeamMember : false;
                const isSuperAdmin = user_roles ? user_roles.isSuperAdmin : false;
                const isAdmin = user_roles ? user_roles.isAdmin : false;
                const isKetuaTim = user_roles ? user_roles.isKetuaTim : false;

                const statusColors = {
                    'Selesai': {
                        bg: 'bg-green-50',
                        text: 'text-green-800',
                        border: 'border-green-200',
                        icon: 'text-green-600',
                        btnBg: 'bg-green-600'
                    },
                    'Dalam Proses': {
                        bg: 'bg-blue-50',
                        text: 'text-blue-800',
                        border: 'border-blue-200',
                        icon: 'text-blue-600',
                        btnBg: 'bg-blue-600'
                    },
                    'Terlambat': {
                        bg: 'bg-red-50',
                        text: 'text-red-800',
                        border: 'border-red-200',
                        icon: 'text-red-600',
                        btnBg: 'bg-red-600'
                    },
                    'Belum Mulai': {
                        bg: 'bg-gray-50',
                        text: 'text-gray-800',
                        border: 'border-gray-200',
                        icon: 'text-gray-600',
                        btnBg: 'bg-gray-600'
                    }
                };

                let html = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${triwulans.map(triwulan => {
                            const colors = statusColors[triwulan.status] || statusColors['Belum Mulai'];
                            const progressClass = triwulan.realisasi_percentage === 100 
                                ? 'bg-green-500' 
                                : (triwulan.realisasi_percentage > 0 
                                    ? 'bg-blue-500' 
                                    : 'bg-gray-300');

                            return `
                                <div class="triwulan-card ${colors.bg} ${colors.border} border-2 rounded-xl p-6 hover:shadow-lg transition-all duration-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 ${colors.btnBg} rounded-full flex items-center justify-center mr-4">
                                                ${triwulan.status === 'Selesai' ? 
                                                    '<svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>' :
                                                    triwulan.status === 'Dalam Proses' ? 
                                                    '<svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>' :
                                                    `<span class="text-white text-lg font-bold">${triwulan.number}</span>`
                                                }
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-bold ${colors.text}">Triwulan ${triwulan.number}</h4>
                                                <p class="text-sm text-gray-600">${triwulan.dateRange}</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium ${colors.text} ${colors.bg} rounded-full border ${colors.border}">
                                            ${triwulan.status}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Target:</span>
                                            <span class="font-medium">${triwulan.target_percentage}%</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Realisasi:</span>
                                            <span class="font-medium">${triwulan.realisasi_percentage}%</span>
                                        </div>
                                        
                                        ${triwulan.status === 'Dalam Proses' ? `
                                            <div class="pt-3 border-t ${colors.border}">
                                                ${isTeamMember ? `
                                                    <div class="text-center py-2">
                                                        <span class="text-sm text-gray-600 italic">Realisasi Fra sedang berjalan</span>
                                                    </div>
                                                ` : `
                                                    <div class="flex justify-center">
                                                        <a href="{{ url('/') }}/pengukuran-kinerja/fra/${fraId}/realisasi/${triwulan.number}" 
                                                            class="${colors.btnBg} hover:opacity-90 text-white px-4 py-2 rounded-lg text-xs flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Input
                                                        </a>
                                                    </div>
                                                `}
                                            </div>
                                        ` : ''}
                                        
                                        ${triwulan.status === 'Terlambat' ? `
                                            <div class="pt-3 border-t ${colors.border}">
                                                ${isTeamMember ? `
                                                    <div class="flex space-x-1">
                                                        <a href="{{ url('/') }}/pengukuran-kinerja/fra/${fraId}/realisasi/${triwulan.number}?mode=view" 
                                                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-2 rounded-lg text-xs flex items-center justify-center transition-colors">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            Lihat
                                                        </a>
                                                        <button type="button" disabled
                                                            class="flex-1 bg-gray-400 text-gray-600 px-2 py-2 rounded-lg text-xs flex items-center justify-center cursor-not-allowed opacity-60"
                                                            title="Fitur belum tersedia">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            Excel
                                                        </button>
                                                    </div>
                                                ` : `
                                                    ${isSuperAdmin ? `
                                                        <div class="grid grid-cols-3 gap-1">
                                                            <a href="{{ url('/') }}/pengukuran-kinerja/fra/${fraId}/realisasi/${triwulan.number}" 
                                                                class="${colors.btnBg} hover:opacity-90 text-white px-2 py-2 rounded-lg text-xs flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md"
                                                                title="Super Admin dapat input meskipun terlambat">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                                Input
                                                            </a>
                                                            <a href="{{ url('/') }}/pengukuran-kinerja/fra/${fraId}/realisasi/${triwulan.number}?mode=view" 
                                                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-2 rounded-lg text-xs flex items-center justify-center transition-colors">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                                Lihat
                                                            </a>
                                                            <button type="button" disabled
                                                                class="bg-gray-400 text-gray-600 px-2 py-2 rounded-lg text-xs flex items-center justify-center cursor-not-allowed opacity-60"
                                                                title="Fitur belum tersedia">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                                Excel
                                                            </button>
                                                        </div>
                                                    ` : `
                                                        <div class="flex space-x-1">
                                                            <a href="{{ url('/') }}/pengukuran-kinerja/fra/${fraId}/realisasi/${triwulan.number}?mode=view" 
                                                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-2 rounded-lg text-xs flex items-center justify-center transition-colors">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                                Lihat
                                                            </a>
                                                            <button type="button" disabled
                                                                class="flex-1 bg-gray-400 text-gray-600 px-2 py-2 rounded-lg text-xs flex items-center justify-center cursor-not-allowed opacity-60"
                                                                title="Fitur belum tersedia">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                                Excel
                                                            </button>
                                                        </div>
                                                    `}
                                                `}
                                            </div>
                                        ` : ''}
                                        
                                        ${triwulan.status === 'Selesai' ? `
                                            <div class="pt-3 border-t ${colors.border}">
                                                ${isTeamMember ? `
                                                    <div class="flex space-x-1">
                                                        <a href="{{ url('/') }}/pengukuran-kinerja/fra/${fraId}/realisasi/${triwulan.number}" 
                                                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-2 rounded-lg text-xs flex items-center justify-center transition-colors">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            Lihat
                                                        </a>
                                                        <button type="button" disabled
                                                            class="flex-1 bg-gray-400 text-gray-600 px-2 py-2 rounded-lg text-xs flex items-center justify-center cursor-not-allowed opacity-60"
                                                            title="Fitur belum tersedia">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            Excel
                                                        </button>
                                                    </div>
                                                ` : `
                                                    ${isSuperAdmin ? `
                                                        <div class="grid grid-cols-3 gap-1">
                                                            <a href="{{ url('/') }}/pengukuran-kinerja/fra/${fraId}/realisasi/${triwulan.number}" 
                                                                class="${colors.btnBg} hover:opacity-90 text-white px-2 py-2 rounded-lg text-xs flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md"
                                                                title="Super Admin dapat input meskipun sudah selesai">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                                Input
                                                            </a>
                                                            <a href="{{ url('/') }}/pengukuran-kinerja/fra/${fraId}/realisasi/${triwulan.number}" 
                                                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-2 rounded-lg text-xs flex items-center justify-center transition-colors">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                                Lihat
                                                            </a>
                                                            <button type="button" disabled
                                                                class="bg-gray-400 text-gray-600 px-2 py-2 rounded-lg text-xs flex items-center justify-center cursor-not-allowed opacity-60"
                                                                title="Fitur belum tersedia">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                                Excel
                                                            </button>
                                                        </div>
                                                    ` : `
                                                        <div class="flex space-x-1">
                                                            <a href="{{ url('/') }}/pengukuran-kinerja/fra/${fraId}/realisasi/${triwulan.number}" 
                                                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-2 rounded-lg text-xs flex items-center justify-center transition-colors">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                                Lihat
                                                            </a>
                                                            <button type="button" disabled
                                                                class="flex-1 bg-gray-400 text-gray-600 px-2 py-2 rounded-lg text-xs flex items-center justify-center cursor-not-allowed opacity-60"
                                                                title="Fitur belum tersedia">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                                Excel
                                                            </button>
                                                        </div>
                                                    `}
                                                `}
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                `;

                contentDiv.innerHTML = html;
            }

            // Fungsi tambahan untuk mendapatkan kelas status
            function getStatusClass(status) {
                const statusClassMap = {
                    'Belum Mulai': 'bg-gray-100 text-gray-600 border-gray-200',
                    'Dalam Proses': 'bg-blue-100 text-blue-600 border-blue-200',
                    'Selesai': 'bg-green-100 text-green-600 border-green-200'
                };
                return statusClassMap[status] || 'bg-gray-100 text-gray-600 border-gray-200';
            }

            // Fungsi tambahan untuk mendapatkan kelas progress
            function getProgressClass(percentage) {
                if (percentage === 100) return 'bg-green-500';
                if (percentage > 0) return 'bg-blue-500';
                return 'bg-gray-300';
            }

            // Filter and search functionality
            const searchInput = document.getElementById('searchInput');
            const filterStatus = document.getElementById('filterStatus');
            const filterTahun = document.getElementById('filterTahun');
            const applyFilter = document.getElementById('applyFilter');

            function filterCards() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusFilter = filterStatus.value;
                const tahunFilter = filterTahun.value;
                const cards = document.querySelectorAll('.fra-card');

                cards.forEach(card => {
                    const year = card.dataset.year;
                    const status = card.dataset.status;
                    const cardText = card.textContent.toLowerCase();

                    const matchesSearch = !searchTerm || cardText.includes(searchTerm);
                    const matchesStatus = !statusFilter || status === statusFilter;
                    const matchesTahun = !tahunFilter || year === tahunFilter;

                    if (matchesSearch && matchesStatus && matchesTahun) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            if (searchInput) searchInput.addEventListener('input', filterCards);
            if (filterStatus) filterStatus.addEventListener('change', filterCards);
            if (filterTahun) filterTahun.addEventListener('change', filterCards);
            if (applyFilter) applyFilter.addEventListener('click', filterCards);

            // Form validation
            const form = document.querySelector('form[action*="fra.store"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const fileInput = document.getElementById('template_file');
                    const yearInput = document.getElementById('tahun_berjalan');

                    if (!fileInput.files.length) {
                        e.preventDefault();
                        alert('Silakan pilih file template terlebih dahulu.');
                        return;
                    }

                    if (!yearInput.value) {
                        e.preventDefault();
                        alert('Silakan pilih tahun berjalan.');
                        return;
                    }
                });
            }

            // Flash messages
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');

            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.transform = 'translateX(0)';
                }, 100);
                setTimeout(() => {
                    successMessage.style.transform = 'translateX(100%)';
                }, 5000);
            }

            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.transform = 'translateX(0)';
                }, 100);
                setTimeout(() => {
                    errorMessage.style.transform = 'translateX(100%)';
                }, 5000);
            }

            // Modal Capaian Kinerja functionality
            const modalCapaianKinerja = document.getElementById('modalTambahCapaianKinerja');
            const modalCapaianKinerjaContent = document.getElementById('modalCapaianKinerjaContent');
            const btnTambahCapaianKinerja = document.getElementById('btnTambahCapaianKinerja');
            const closeCapaianKinerjaModal = document.getElementById('closeCapaianKinerjaModal');
            const cancelCapaianKinerjaModal = document.getElementById('cancelCapaianKinerjaModal');

            function openCapaianKinerjaModal() {
                if (modalCapaianKinerja && modalCapaianKinerjaContent) {
                    modalCapaianKinerja.classList.remove('hidden');
                    setTimeout(() => {
                        modalCapaianKinerjaContent.classList.add('show');
                    }, 10);
                }
            }

            function closeCapaianKinerjaModalFunc() {
                if (modalCapaianKinerja && modalCapaianKinerjaContent) {
                    modalCapaianKinerjaContent.classList.remove('show');
                    setTimeout(() => {
                        modalCapaianKinerja.classList.add('hidden');
                    }, 300);
                }
            }

            if (btnTambahCapaianKinerja) {
                btnTambahCapaianKinerja.addEventListener('click', openCapaianKinerjaModal);
            }
            if (closeCapaianKinerjaModal) {
                closeCapaianKinerjaModal.addEventListener('click', closeCapaianKinerjaModalFunc);
            }
            if (cancelCapaianKinerjaModal) {
                cancelCapaianKinerjaModal.addEventListener('click', closeCapaianKinerjaModalFunc);
            }

            if (modalCapaianKinerja) {
                modalCapaianKinerja.addEventListener('click', function(e) {
                    if (e.target === modalCapaianKinerja) {
                        closeCapaianKinerjaModalFunc();
                    }
                });
            }

            // Download dropdown functionality
            function toggleDownloadDropdown(dropdownId) {
                const dropdown = document.getElementById(dropdownId);
                if (dropdown) {
                    // Toggle inline style display
                    if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                        dropdown.style.display = 'block';
                    } else {
                        dropdown.style.display = 'none';
                    }
                }
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.relative')) {
                    const dropdowns = document.querySelectorAll('[id^="dropdown-"]');
                    dropdowns.forEach(dropdown => {
                        dropdown.style.display = 'none';
                    });
                }
            });

            // Make toggleDownloadDropdown globally available
            window.toggleDownloadDropdown = toggleDownloadDropdown;

            // Download loading functionality - menggunakan global loading system
            function showDownloadLoading(message, linkElement) {
                // Gunakan global loading system yang sama
                if (window.showLoading) {
                    window.showLoading(message);
                } else if (window.showGlobalLoading) {
                    window.showGlobalLoading(message);
                }
                
                // Disable link sementara untuk mencegah multiple clicks
                if (linkElement) {
                    linkElement.style.pointerEvents = 'none';
                    linkElement.style.opacity = '0.6';
                }
                
                // Auto hide loading dan restore link after download (estimated 10 seconds)
                setTimeout(() => {
                    if (window.hideLoading) {
                        window.hideLoading();
                    } else if (window.hideGlobalLoading) {
                        window.hideGlobalLoading();
                    }
                    
                    if (linkElement) {
                        linkElement.style.pointerEvents = '';
                        linkElement.style.opacity = '1';
                    }
                }, 10000);
                
                return true; // Allow the link to proceed
            }

            // Make download loading function globally available
            window.showDownloadLoading = showDownloadLoading;
            
            // Download confirmation modal
            let currentDownloadHref = '';
            let currentDownloadMessage = '';
            
            window.confirmDownload = function(event, element) {
                event.preventDefault();
                
                currentDownloadHref = element.getAttribute('data-href');
                currentDownloadMessage = element.getAttribute('data-message') || 'Menyiapkan file download...';
                
                const modal = document.getElementById('modalDownloadConfirm');
                const modalContent = document.getElementById('modalDownloadContent');
                
                if (modal && modalContent) {
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        modalContent.classList.add('show');
                    }, 10);
                }
            };
            
            window.closeDownloadModal = function() {
                const modal = document.getElementById('modalDownloadConfirm');
                const modalContent = document.getElementById('modalDownloadContent');
                
                if (modal && modalContent) {
                    modalContent.classList.remove('show');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                    }, 300);
                }
            };
            
            // Confirm download button handler
            const btnConfirmDownload = document.getElementById('btnConfirmDownload');
            if (btnConfirmDownload) {
                btnConfirmDownload.addEventListener('click', function() {
                    closeDownloadModal();
                    
                    // Show loading with custom message
                    if (window.showLoading) {
                        window.showLoading(currentDownloadMessage);
                    } else if (window.showGlobalLoading) {
                        window.showGlobalLoading(currentDownloadMessage);
                    }
                    
                    // Create temporary link and trigger download
                    const link = document.createElement('a');
                    link.href = currentDownloadHref;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Auto hide loading after estimated download time
                    setTimeout(() => {
                        if (window.hideLoading) {
                            window.hideLoading();
                        } else if (window.hideGlobalLoading) {
                            window.hideGlobalLoading();
                        }
                    }, 15000); // 15 seconds for Excel with update
                });
            }
            
            // ESC key to close download modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const downloadModal = document.getElementById('modalDownloadConfirm');
                    if (downloadModal && !downloadModal.classList.contains('hidden')) {
                        closeDownloadModal();
                    }
                }
            });

            // Handle perPage change for Capaian Kinerja tab
            document.getElementById('perPageSelectCapaianKinerja').addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.set('tab', 'capaian-kinerja'); // Ensure tab remains active
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            });
        });
    </script>

    <!-- Modal Tambah Capaian Kinerja -->
    <div id="modalTambahCapaianKinerja"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0"
            id="modalCapaianKinerjaContent">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Tambah Kegiatan Capaian Kinerja</h3>
                    <button id="closeCapaianKinerjaModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('capaian.kinerja.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="nama_kegiatan_capaian" class="block text-sm font-medium text-gray-700 mb-2">Nama Kegiatan</label>
                        <input type="text" id="nama_kegiatan_capaian" name="nama_kegiatan" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Masukkan nama kegiatan capaian kinerja">
                    </div>

                    <div>
                        <label for="tahun_berjalan_capaian" class="block text-sm font-medium text-gray-700 mb-2">Tahun Berjalan</label>
                        <select id="tahun_berjalan_capaian" name="tahun_berjalan" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Pilih Tahun</option>
                            @php
                                $currentYear = date('Y');
                                for ($year = $currentYear + 2; $year >= $currentYear - 5; $year--) {
                                    echo "<option value=\"$year\">$year</option>";
                                }
                            @endphp
                        </select>
                    </div>

                    <div>
                        <label for="fra_id_capaian" class="block text-sm font-medium text-gray-700 mb-2">Berdasarkan FRA</label>
                        <select id="fra_id_capaian" name="fra_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Pilih FRA</option>
                            @foreach($fraList as $fra)
                                <option value="{{ $fra->id }}">FRA {{ $fra->tahun_berjalan }} ({{ $fra->status }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400 mt-0.5 mr-2 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">Informasi:</p>
                                <p>Tanggal kegiatan otomatis: 1 Januari - 31 Desember tahun yang sama dengan FRA. Sistem triwulan dengan periode upload: Q1→April, Q2→Juli, Q3→Oktober, Q4→Januari tahun berikutnya. Contoh: FRA 2024 → Capaian Kinerja 1 Januari - 31 Desember 2024.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button type="button" id="cancelCapaianKinerjaModal"
                            class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-3 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                            Tambah Kegiatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Download -->
    <div id="modalDownloadConfirm" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all" id="modalDownloadContent">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-900">Konfirmasi Download</h3>
                        <p class="text-sm text-gray-500">Generate file dengan data terbaru</p>
                    </div>
                </div>
                
                <p class="text-gray-600 mb-6">
                    File akan di-generate dengan data terbaru dari form input. Proses ini mungkin memerlukan beberapa detik tergantung jumlah data. Lanjutkan?
                </p>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeDownloadModal()" 
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button id="btnConfirmDownload" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Ya, Download
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Tab Styles */
        .tab-pane {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .tab-pane.active {
            display: block;
            opacity: 1;
        }
        .tab-btn {
            transition: all 0.2s ease;
        }
        .tab-btn:hover {
            color: #374151;
        }

        .fra-card {
            transition: all 0.3s ease;
        }

        .fra-card:hover {
            transform: translateY(-4px);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        #modalContent.show,
        #modalTriwulanContent.show,
        #modalCapaianKinerjaContent.show {
            transform: scale(1);
            opacity: 1;
        }

        .triwulan-card {
            transition: all 0.2s ease;
        }

        .triwulan-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        /* Modal animations */
        #modalDownloadContent {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
            transition: all 0.3s ease;
        }
        
        #modalDownloadContent.show {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
        
        .ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s ease-out;
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
// ... existing code ...
    </script>
@endpush
