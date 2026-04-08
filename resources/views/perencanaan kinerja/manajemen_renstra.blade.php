@extends('components.master')

@section('title', 'Manajemen Renstra')

@section('content')
    @include('components.breadcrumbs')

    <div class="container mx-auto py-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manajemen Renstra</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola dokumen perencanaan strategis untuk pencapaian kinerja optimal
                </p>
            </div>
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
                        class="block w-full pl-10 pr-20 py-2 border border-gray-200 rounded-lg focus:ring-red-500 focus:border-red-500"
                        placeholder="Cari dokumen Renstra..."
                        title="Tekan Ctrl+F untuk fokus ke pencarian">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span id="searchCounter" class="text-xs text-gray-500 hidden"></span>
                    </div>
                </div>

                <div class="flex space-x-2">
                    <select id="filterTahun"
                        class="border border-gray-200 rounded-lg px-3 py-2 focus:ring-red-500 focus:border-red-500 text-sm">
                        <option value="">Semua Tahun</option>
                        @php
                            $currentYear = date('Y');
                            for ($year = $currentYear + 5; $year >= $currentYear - 5; $year--) {
                                echo "<option value=\"$year\">$year</option>";
                            }
                        @endphp
                    </select>

                    <select id="filterKategori"
                        class="border border-gray-200 rounded-lg px-3 py-2 focus:ring-red-500 focus:border-red-500 text-sm">
                        <option value="">Semua Kategori</option>
                        <option value="Renstra">Renstra</option>
                        <option value="Reviu Renstra">Reviu Renstra</option>
                        <option value="Reviu Target Renstra">Reviu Target</option>
                        <option value="Capaian Target Renstra">Capaian Target</option>
                    </select>

                    <button id="applyFilter"
                        class="bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition-colors flex items-center justify-center"
                        title="Terapkan Filter">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                    </button>

                    <button id="clearFilter"
                        class="bg-red-100 hover:bg-red-200 p-2 rounded-lg transition-colors flex items-center justify-center hidden"
                        title="Hapus Semua Filter (Ctrl+Shift+C atau Esc)"
                        onclick="clearAllFilters()">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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
                        data-tab="overview">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Semua
                        </div>
                    </button>
                    <button
                        class="tab-btn text-sm font-medium px-6 py-3 text-gray-500 hover:text-gray-900 focus:outline-none"
                        data-tab="renstra">
                        <div class="flex items-center">
                            <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>
                            Renstra
                        </div>
                    </button>
                    <button
                        class="tab-btn text-sm font-medium px-6 py-3 text-gray-500 hover:text-gray-900 focus:outline-none"
                        data-tab="reviu-renstra">
                        <div class="flex items-center">
                            <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span>
                            Reviu Renstra
                        </div>
                    </button>
                    <button
                        class="tab-btn text-sm font-medium px-6 py-3 text-gray-500 hover:text-gray-900 focus:outline-none"
                        data-tab="reviu-target">
                        <div class="flex items-center">
                            <span class="w-2 h-2 rounded-full bg-purple-500 mr-2"></span>
                            Reviu Target Renstra
                        </div>
                    </button>
                    <button
                        class="tab-btn text-sm font-medium px-6 py-3 text-gray-500 hover:text-gray-900 focus:outline-none"
                        data-tab="capaian-target">
                        <div class="flex items-center">
                            <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                            Capaian Target Renstra
                        </div>
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Tab: Overview / Semua (Card View) -->
                <div id="overview" class="tab-pane active">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="allActivitiesContainer">
                            @forelse($allActivities as $kegiatan)
                                @php
                                    // Menentukan warna dan kategori berdasarkan jenis kegiatan
                                    $categoryColor = '';
                                    $bgColor = '';
                                    $hoverBgColor = '';
                                    $textColor = '';
                                    $imagePath = '';

                                    // Khusus untuk renstra document
                                    if (isset($kegiatan->type) && $kegiatan->type === 'renstra_document') {
                                        $categoryColor = 'bg-red-500';
                                        $bgColor = 'bg-red-50';
                                        $hoverBgColor = 'hover:bg-red-100';
                                        $textColor = 'text-red-600';
                                        $category = 'Renstra';
                                        $imagePath = 'img/Renstra.jpeg';
                                        $targetTab = 'renstra';
                                    } elseif (
                                        stripos($kegiatan->nama_kegiatan, 'Renstra') !== false &&
                                        stripos($kegiatan->nama_kegiatan, 'Reviu') === false &&
                                        stripos($kegiatan->nama_kegiatan, 'Target') === false
                                    ) {
                                        $categoryColor = 'bg-red-500';
                                        $bgColor = 'bg-red-50';
                                        $hoverBgColor = 'hover:bg-red-100';
                                        $textColor = 'text-red-600';
                                        $category = 'Renstra';
                                        $imagePath = 'img/Renstra.jpeg';
                                        $targetTab = 'renstra';
                                    } elseif (
                                        stripos($kegiatan->nama_kegiatan, 'Reviu Renstra') !== false &&
                                        stripos($kegiatan->nama_kegiatan, 'Target') === false
                                    ) {
                                        $categoryColor = 'bg-amber-500';
                                        $bgColor = 'bg-amber-50';
                                        $hoverBgColor = 'hover:bg-amber-100';
                                        $textColor = 'text-amber-600';
                                        $category = 'Reviu Renstra';
                                        $imagePath = 'img/Reviu Renstra.jpeg';
                                        $targetTab = 'reviu-renstra';
                                    } elseif (stripos($kegiatan->nama_kegiatan, 'Reviu Target') !== false) {
                                        $categoryColor = 'bg-purple-500';
                                        $bgColor = 'bg-purple-50';
                                        $hoverBgColor = 'hover:bg-purple-100';
                                        $textColor = 'text-purple-600';
                                        $category = 'Reviu Target Renstra';
                                        $imagePath = 'img/Reviu Target Renstra.jpeg';
                                        $targetTab = 'reviu-target';
                                    } elseif (stripos($kegiatan->nama_kegiatan, 'Capaian Target') !== false) {
                                        $categoryColor = 'bg-green-500';
                                        $bgColor = 'bg-green-50';
                                        $hoverBgColor = 'hover:bg-green-100';
                                        $textColor = 'text-green-600';
                                        $category = 'Capaian Target Renstra';
                                        $imagePath = 'img/Capaian Target Renstra.jpeg';
                                        $targetTab = 'capaian-target';
                                    }
                                @endphp

                                <div class="kegiatan-card renstra-card bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow group cursor-pointer"
                                    data-tab="{{ $targetTab }}" data-kategori="{{ $category }}"
                                    data-tahun="{{ $kegiatan->tahun_berjalan }}">
                                    <div class="relative">
                                        <!-- Color Strip at the top -->
                                        <div class="absolute top-0 left-0 right-0 h-1 {{ $categoryColor }}"></div>
                                        <!-- Cover Image - Menggunakan gambar spesifik untuk masing-masing kategori -->
                                        <div class="h-40 bg-gray-100 overflow-hidden pt-1">
                                            <img src="{{ asset($imagePath) }}" alt="{{ $kegiatan->nama_kegiatan }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                onerror="this.src='{{ asset('img/default-renstra.jpeg') }}'">
                                        </div>
                                        <!-- Status Badge -->
                                        <div class="absolute top-3 right-3">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $kegiatan->status_class }}">
                                                <span class="mr-1 w-2 h-2 rounded-full {{ $kegiatan->status_dot }}"></span>
                                                {{ $kegiatan->status }}
                                            </span>
                                        </div>
                                        <!-- Category Badge -->
                                        <div
                                            class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
                                            <span
                                                class="text-xs font-medium text-white {{ $categoryColor }} py-1 px-2 rounded">{{ $category }}</span>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="font-bold text-gray-900">{{ $kegiatan->nama_kegiatan }}
                                                {{ $kegiatan->tahun_berjalan }}</h3>
                                            <span class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($kegiatan->tanggal_berakhir)->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">
                                            {{ $kegiatan->keterangan ?? 'Tidak ada keterangan' }}</p>

                                        <div class="flex items-center justify-end">
                                            <div class="flex space-x-2">
                                                @if(isset($kegiatan->type) && $kegiatan->type === 'renstra_document')
                                                    <!-- Button detail untuk renstra -->
                                                    <a href="{{ route('renstra.detail', ['id' => $kegiatan->id, 'year' => $kegiatan->tahun_berjalan]) }}"
                                                        class="view-detail inline-flex items-center justify-center {{ $bgColor }} {{ $hoverBgColor }} {{ $textColor }} px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                                                        data-tab="{{ $targetTab }}">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                                        Detail
                                                    </a>
                                                    <!-- Button dokumen untuk renstra -->
                                                    @if(isset($kegiatan->dokumenKegiatan) && $kegiatan->dokumenKegiatan)
                                                        <a href="{{ route('dokumen.view', $kegiatan->dokumenKegiatan->id) }}"
                                                            target="_blank"
                                                            class="inline-flex items-center justify-center {{ $bgColor }} {{ $hoverBgColor }} {{ $textColor }} px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                                                            title="Lihat Dokumen">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                                </path>
                                                            </svg>
                                                            Dokumen
                                                        </a>
                                                    @else
                                                        <span class="inline-flex items-center justify-center bg-gray-100 text-gray-500 px-3 py-1.5 rounded-lg text-sm font-medium">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                                </path>
                                                            </svg>
                                                            Belum Ada
                                                        </span>
                                                    @endif
                                                @else
                                                    <!-- Button detail untuk kegiatan biasa -->
                                                <a href="{{ route('detail', ['id' => $kegiatan->id, 'year' => $kegiatan->tahun_berjalan]) }}"
                                                    class="view-detail inline-flex items-center justify-center {{ $bgColor }} {{ $hoverBgColor }} {{ $textColor }} px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                                                    data-tab="{{ $targetTab }}">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                    Detail
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-3 py-8 text-center text-gray-500">
                                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <h3 class="text-lg font-medium mb-2">Tidak ada data kegiatan</h3>
                                    <p>Belum ada kegiatan yang tersedia saat ini.</p>
                                </div>
                            @endforelse
                        </div>
                        
                        <!-- Custom Pagination Links for Renstra (same as FRA) -->
                        <div class="mt-8">
                            @php
                                $currentPage = $allActivities->currentPage();
                                $totalItems = $allActivities->total();
                                $perPage = $allActivities->perPage();
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
                                            <a href="{{ $allActivities->appends(['tab' => request('tab', 'overview')])->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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
                                                        <a href="{{ $allActivities->appends(['tab' => request('tab', 'overview')])->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
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
                                            <a href="{{ $allActivities->appends(['tab' => request('tab', 'overview')])->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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
                        
                        <!-- Remove the JavaScript for perPageSelectRenstra as it's not needed for the "Semua" tab -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Handle perPage change for Renstra
                                if (document.getElementById('perPageSelectRenstra')) {
                                    document.getElementById('perPageSelectRenstra').removeEventListener('change', updateRenstraPerPage);
                                }
                            });
                        </script>
                    </div>
                </div>

                <!-- Tab: Renstra -->
                <div id="renstra" class="tab-pane hidden">
                    <div class="p-6">
                        <!-- Header dengan breadcrumb mini -->
                        <div class="flex items-center mb-6 text-sm">
                            <a href="#" class="text-gray-500 hover:text-gray-700"
                                onclick="document.querySelector('.tab-btn[data-tab=\'overview\']').click(); return false;">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali ke Semua
                            </a>
                            <span class="mx-2 text-gray-400">•</span>
                            <span class="flex items-center">
                                <span class="w-2 h-2 rounded-full bg-red-500 mr-1"></span>
                                <span class="font-medium text-gray-900">Renstra</span>
                            </span>
                        </div>

                        <div class="mb-6 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Dokumen Renstra</h3>
                                <p class="text-sm text-gray-500">Rencana strategis yang memuat visi, misi, tujuan strategis
                                    untuk periode pengembangan</p>
                            </div>
                            @if($isSuperAdmin)
                            <button
                                class="flex items-center bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm transition-colors"
                                id="btnTambahDokumenRenstra">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Periode
                            </button>
                            @endif
                        </div>

                        <div class="overflow-x-auto bg-white rounded-xl border border-gray-100 shadow-sm">
                            <table id="renstraTable" class="w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100">
                                        <th class="py-3 px-4 text-left font-semibold text-gray-900" style="width: 40%;">Nama Renstra</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Periode</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Status</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Kelengkapan</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 15%;">Keterangan</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Detail</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($renstra as $item)
                                        @php
                                            $startYear = date('Y', strtotime($item->periode_awal));
                                            $endYear = date('Y', strtotime($item->periode_akhir));
                                            $currentYear = date('Y');
                                            
                                            // Menentukan status
                                            if ($currentYear >= $startYear && $currentYear <= $endYear) {
                                                $status = 'Aktif';
                                                $statusClass = 'bg-green-100 text-green-800';
                                                $statusDot = 'bg-green-500';
                                            } elseif ($currentYear < $startYear) {
                                                $status = 'Upcoming';
                                                $statusClass = 'bg-blue-100 text-blue-800';
                                                $statusDot = 'bg-blue-500';
                                            } else {
                                                $status = 'Selesai';
                                                $statusClass = 'bg-gray-100 text-gray-800';
                                                $statusDot = 'bg-gray-500';
                                            }
                                            
                                            // Menentukan kelengkapan dokumen
                                            $hasDocument = isset($item->dokumenKegiatan) && $item->dokumenKegiatan;
                                            $kelengkapanClass = $hasDocument ? 'text-green-500' : 'text-red-500';
                                            $keterangan = $hasDocument ? 'Bukti dukung tersedia' : 'Bukti dukung belum lengkap';
                                        @endphp
                                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                            <td class="py-3 px-4 text-sm">
                                                <div class="flex items-center">
                                                    <svg class="w-6 h-6 text-red-500 mr-3" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <span>{{ $item->nama_renstra }}
                                                        {{ $startYear }}-{{ $endYear }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-center text-gray-600">
                                                {{ $startYear }} - {{ $endYear }}
                                            </td>
                                            <td class="py-3 px-4 text-sm text-center">
                                                <div class="flex justify-center">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                        <span
                                                            class="mr-1 w-2 h-2 rounded-full {{ $statusDot }}"></span>
                                                        {{ $status }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-center">
                                                <div class="flex justify-center">
                                                    @if($hasDocument)
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 {{ $kelengkapanClass }}"
                                                                    viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 {{ $kelengkapanClass }}"
                                                                    viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-gray-600 text-center">
                                                {{ $keterangan }}
                                            </td>
                                            <td class="py-3 px-4 text-sm text-center">
                                                <a href="{{ route('renstra.detail', ['id' => $item->id, 'year' => $startYear]) }}"
                                                    class="inline-flex items-center justify-center p-1 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors"
                                                    title="Lihat Detail">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                                    viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd"
                                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                </a>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-center">
                                                @if($isSuperAdmin)
                                                    <form id="deleteFormRenstra{{ $item->id }}" action="{{ route('renstra.destroy', $item->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="inline-flex items-center justify-center p-1 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors delete-renstra-btn" title="Hapus" data-renstra-id="{{ $item->id }}">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="border-b border-gray-100">
                                            <td colspan="7" class="py-6 text-center text-gray-500">
                                                Belum ada data Renstra. Mulai dengan menambahkan periode baru.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination untuk Renstra -->
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                @php
                                    $currentPage = max(1, $renstra->currentPage());
                                    $totalItems = $renstra->total();
                                    $perPage = $renstra->perPage();
                                    $totalPages = max(ceil($totalItems / max(1, $perPage)), 1);
                                    $hasNextPage = $currentPage < $totalPages;
                                    $hasPrevPage = $currentPage > 1;
                                    $currentItemsCount = min($perPage, $totalItems - (($currentPage - 1) * $perPage));
                                @endphp
                                
                                <div class="flex items-center space-x-2">
                                    <span>Menampilkan <span class="font-semibold text-gray-900">{{ $currentItemsCount }}</span> dari <span class="font-semibold text-gray-900">{{ $totalItems }}</span></span>
                            </div>
                            </div>
                            
                            <!-- Pagination Controls -->
                            <div class="flex items-center space-x-2">
                                <div>
                                    <select name="per_page" id="perPageSelectRenstra"
                                        class="items-center mr-2 px-3 py-1.5 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 text-sm">
                                        @foreach ([5, 10, 15, 25, 50] as $option)
                                            <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                                                {{ $option }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <!-- Previous Button -->
                                    @if($hasPrevPage)
                                        <a href="{{ $renstra->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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
                                                    <a href="{{ $renstra->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
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
                                        <a href="{{ $renstra->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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

                <!-- Tab: Reviu Renstra -->
                <div id="reviu-renstra" class="tab-pane hidden">
                    <div class="p-6">
                        <!-- Header dengan breadcrumb mini -->
                        <div class="flex items-center mb-6 text-sm">
                            <a href="#" class="text-gray-500 hover:text-gray-700"
                                onclick="document.querySelector('.tab-btn[data-tab=\'overview\']').click(); return false;">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali ke Semua
                            </a>
                            <span class="mx-2 text-gray-400">•</span>
                            <span class="flex items-center">
                                <span class="w-2 h-2 rounded-full bg-amber-500 mr-1"></span>
                                <span class="font-medium text-gray-900">Reviu Renstra</span>
                            </span>
                        </div>

                        <div class="mb-6 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Reviu Renstra</h3>
                                <p class="text-sm text-gray-500">Peninjauan kembali dokumen Renstra untuk penyesuaian
                                    strategis</p>
                            </div>
                            @if($isSuperAdmin)
                            <button
                                class="flex items-center bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-md text-sm transition-colors"
                                id="btnTambahKegiatan">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Kegiatan
                            </button>
                            @endif
                        </div>

                        <div class="overflow-x-auto bg-white rounded-xl border border-gray-100 shadow-sm">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100">
                                        <th class="py-3 px-4 text-left font-semibold text-gray-900" style="width: 40%;">Kegiatan</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Status</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Kelengkapan</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 20%;">Keterangan</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Detail</th>
                                        @if($isSuperAdmin)
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reviuRenstraCollection as $item)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                            <td class="py-3 px-4 text-sm">{{ $item->nama_kegiatan }}
                                                {{ $item->tahun_berjalan }}</td>
                                            <td class="py-3 px-4 text-sm text-center">
                                                <div class="flex justify-center">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->status_class }}">
                                                        <span
                                                            class="mr-1 w-2 h-2 rounded-full {{ $item->status_dot }}"></span>
                                                        {{ $item->status }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-center">
                                                <div class="flex justify-center">
                                                    @if (isset($item->kelengkapan) && $item->kelengkapan == 1)
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 {{ $item->kelengkapan_class }}"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 {{ $item->kelengkapan_class ?? 'text-red-500' }}"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-center text-gray-600">
                                                {{ $item->keterangan ?? 'Tidak ada keterangan' }}</td>
                                            <td class="py-3 px-4 text-sm text-center">
                                                <a href="{{ route('detail', ['id' => $item->id, 'year' => $item->tahun_berjalan]) }}"
                                                    class="inline-flex items-center justify-center p-1 bg-purple-100 text-purple-600 rounded-md hover:bg-purple-200 transition-colors"
                                                    title="Lihat Detail">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                            </td>
                                            @if($isSuperAdmin)
                                            <td class="py-3 px-4 text-sm text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <button type="button" class="text-blue-500 hover:text-blue-700 edit-kegiatan-btn" title="Edit Tanggal"
                                                        data-id="{{ $item->id }}"
                                                        data-url="{{ route('kegiatan.update', $item->id) }}"
                                                        data-start-date="{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('Y-m-d') }}"
                                                        data-end-date="{{ \Carbon\Carbon::parse($item->tanggal_berakhir)->format('Y-m-d') }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z"></path></svg>
                                                    </button>
                                                    <form id="deleteForm{{ $item->id }}" action="{{ route('kegiatan.destroy', $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="text-red-500 hover:text-red-700 delete-kegiatan-btn" title="Hapus" data-kegiatan-id="{{ $item->id }}">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr class="border-b border-gray-100">
                                            <td colspan="6" class="py-6 text-center text-gray-500">
                                                Belum ada data Reviu Renstra.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination untuk Reviu Renstra -->
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                @php
                                    $currentPage = max(1, $reviuRenstraCollection->currentPage());
                                    $totalItems = $reviuRenstraCollection->total();
                                    $perPage = $reviuRenstraCollection->perPage();
                                    $totalPages = max(ceil($totalItems / max(1, $perPage)), 1);
                                    $hasNextPage = $currentPage < $totalPages;
                                    $hasPrevPage = $currentPage > 1;
                                    $currentItemsCount = min($perPage, $totalItems - (($currentPage - 1) * $perPage));
                                @endphp
                                
                                <div class="flex items-center space-x-2">
                                    <span>Menampilkan <span class="font-semibold text-gray-900">{{ $currentItemsCount }}</span> dari <span class="font-semibold text-gray-900">{{ $totalItems }}</span></span>
                            </div>
                            </div>
                            
                            <!-- Pagination Controls -->
                            <div class="flex items-center space-x-2">
                                <div>
                                    <select name="per_page" id="perPageSelectReviuRenstra"
                                        class="items-center mr-2 px-3 py-1.5 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 text-sm">
                                        @foreach ([5, 10, 15, 25, 50] as $option)
                                            <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                                                {{ $option }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <!-- Previous Button -->
                                    @if($hasPrevPage)
                                        <a href="{{ $reviuRenstraCollection->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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
                                                    <a href="{{ $reviuRenstraCollection->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
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
                                        <a href="{{ $reviuRenstraCollection->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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

                <!-- Tab: Reviu Target Renstra -->
                <div id="reviu-target" class="tab-pane hidden">
                    <div class="p-6">
                        <!-- Header dengan breadcrumb mini -->
                        <div class="flex items-center mb-6 text-sm">
                            <a href="#" class="text-gray-500 hover:text-gray-700"
                                onclick="document.querySelector('.tab-btn[data-tab=\'overview\']').click(); return false;">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali ke Semua
                            </a>
                            <span class="mx-2 text-gray-400">•</span>
                            <span class="flex items-center">
                                <span class="w-2 h-2 rounded-full bg-purple-500 mr-1"></span>
                                <span class="font-medium text-gray-900">Reviu Target Renstra</span>
                            </span>
                        </div>

                        <div class="mb-6 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Reviu Target Renstra</h3>
                                <p class="text-sm text-gray-500">Evaluasi dan penyesuaian target yang telah ditetapkan
                                    dalam dokumen Renstra</p>
                            </div>
                            @if($isSuperAdmin)
                            <button
                                class="flex items-center bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md text-sm transition-colors"
                                id="btnTambahKegiatanTarget">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Kegiatan
                            </button>
                            @endif
                        </div>

                        <div class="overflow-x-auto bg-white rounded-xl border border-gray-100 shadow-sm">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100">
                                        <th class="py-3 px-4 text-left font-semibold text-gray-900" style="width: 40%;">Kegiatan</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Status</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Kelengkapan</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 20%;">Keterangan</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Detail</th>
                                        @if($isSuperAdmin)
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reviuTargetCollection as $item)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                            <td class="py-3 px-4 text-sm">{{ $item->nama_kegiatan }}
                                                {{ $item->tahun_berjalan }}</td>
                                            <td class="py-3 px-4 text-sm text-center">
                                                <div class="flex justify-center">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->status_class }}">
                                                        <span
                                                            class="mr-1 w-2 h-2 rounded-full {{ $item->status_dot }}"></span>
                                                        {{ $item->status }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-center">
                                                <div class="flex justify-center">
                                                    @if (isset($item->kelengkapan) && $item->kelengkapan == 1)
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 {{ $item->kelengkapan_class }}"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 {{ $item->kelengkapan_class ?? 'text-red-500' }}"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-center text-gray-600">
                                                {{ $item->keterangan ?? 'Tidak ada keterangan' }}</td>
                                            <td class="py-3 px-4 text-sm text-center">
                                                <a href="{{ route('detail', ['id' => $item->id, 'year' => $item->tahun_berjalan]) }}"
                                                    class="inline-flex items-center justify-center p-1 bg-purple-100 text-purple-600 rounded-md hover:bg-purple-200 transition-colors"
                                                    title="Lihat Detail">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                            </td>
                                            @if($isSuperAdmin)
                                            <td class="py-3 px-4 text-sm text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <button type="button" class="text-blue-500 hover:text-blue-700 edit-kegiatan-btn" title="Edit Tanggal"
                                                        data-id="{{ $item->id }}"
                                                        data-url="{{ route('kegiatan.update', $item->id) }}"
                                                        data-start-date="{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('Y-m-d') }}"
                                                        data-end-date="{{ \Carbon\Carbon::parse($item->tanggal_berakhir)->format('Y-m-d') }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z"></path></svg>
                                                    </button>
                                                    <form id="deleteForm{{ $item->id }}" action="{{ route('kegiatan.destroy', $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="text-red-500 hover:text-red-700 delete-kegiatan-btn" title="Hapus" data-kegiatan-id="{{ $item->id }}">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr class="border-b border-gray-100">
                                            <td colspan="6" class="py-6 text-center text-gray-500">
                                                Belum ada data Reviu Target Renstra.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination untuk Reviu Target Renstra -->
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                @php
                                    $currentPage = max(1, $reviuTargetCollection->currentPage());
                                    $totalItems = $reviuTargetCollection->total();
                                    $perPage = $reviuTargetCollection->perPage();
                                    $totalPages = max(ceil($totalItems / max(1, $perPage)), 1);
                                    $hasNextPage = $currentPage < $totalPages;
                                    $hasPrevPage = $currentPage > 1;
                                    $currentItemsCount = min($perPage, $totalItems - (($currentPage - 1) * $perPage));
                                @endphp
                                
                                <div class="flex items-center space-x-2">
                                    <span>Menampilkan <span class="font-semibold text-gray-900">{{ $currentItemsCount }}</span> dari <span class="font-semibold text-gray-900">{{ $totalItems }}</span></span>
                            </div>
                            </div>
                            
                            <!-- Pagination Controls -->
                            <div class="flex items-center space-x-2">
                                <div>
                                    <select name="per_page" id="perPageSelectReviuTarget"
                                        class="items-center mr-2 px-3 py-1.5 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 text-sm">
                                        @foreach ([5, 10, 15, 25, 50] as $option)
                                            <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                                                {{ $option }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <!-- Previous Button -->
                                    @if($hasPrevPage)
                                        <a href="{{ $reviuTargetCollection->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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
                                                    <a href="{{ $reviuTargetCollection->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
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
                                        <a href="{{ $reviuTargetCollection->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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

                <!-- Tab: Capaian Target -->
                <div id="capaian-target" class="tab-pane hidden">
                    <div class="p-6">
                        <!-- Header dengan breadcrumb mini -->
                        <div class="flex items-center mb-6 text-sm">
                            <a href="#" class="text-gray-500 hover:text-gray-700"
                                onclick="document.querySelector('.tab-btn[data-tab=\'overview\']').click(); return false;">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali ke Semua
                            </a>
                            <span class="mx-2 text-gray-400">•</span>
                            <span class="flex items-center">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                                <span class="font-medium text-gray-900">Capaian Target Renstra</span>
                            </span>
                        </div>

                        <div class="mb-6 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Capaian Target Renstra</h3>
                                <p class="text-sm text-gray-500">Laporan pencapaian target yang ditetapkan dalam dokumen
                                    Renstra</p>
                            </div>
                            @if($isSuperAdmin)
                            <button
                                class="flex items-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm transition-colors"
                                id="btnTambahCapaian">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Kegiatan
                            </button>
                            @endif
                        </div>

                        <div class="overflow-x-auto bg-white rounded-xl border border-gray-100 shadow-sm">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100">
                                        <th class="py-3 px-4 text-left font-semibold text-gray-900" style="width: 40%;">Kegiatan</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Status</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Kelengkapan</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 20%;">Keterangan</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Detail</th>
                                        @if($isSuperAdmin)
                                        <th class="py-3 px-4 text-center font-semibold text-gray-900" style="width: 10%;">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($capaianTargetCollection as $item)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                            <td class="py-3 px-4 text-sm">{{ $item->nama_kegiatan }}
                                                {{ $item->tahun_berjalan }}</td>
                                            <td class="py-3 px-4 text-sm text-center">
                                                <div class="flex justify-center">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->status_class }}">
                                                        <span
                                                            class="mr-1 w-2 h-2 rounded-full {{ $item->status_dot }}"></span>
                                                        {{ $item->status }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-center">
                                                <div class="flex justify-center">
                                                    @if (isset($item->kelengkapan) && $item->kelengkapan == 1)
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 {{ $item->kelengkapan_class }}"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 {{ $item->kelengkapan_class ?? 'text-red-500' }}"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-center text-gray-600">
                                                {{ $item->keterangan ?? 'Tidak ada keterangan' }}</td>
                                            <td class="py-3 px-4 text-sm text-center">
                                                <a href="{{ route('detail', ['id' => $item->id, 'year' => $item->tahun_berjalan]) }}"
                                                    class="inline-flex items-center justify-center p-1 bg-purple-100 text-purple-600 rounded-md hover:bg-purple-200 transition-colors"
                                                    title="Lihat Detail">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                            </td>
                                            @if($isSuperAdmin)
                                            <td class="py-3 px-4 text-sm text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <button type="button" class="text-blue-500 hover:text-blue-700 edit-kegiatan-btn" title="Edit Tanggal"
                                                        data-id="{{ $item->id }}"
                                                        data-url="{{ route('kegiatan.update', $item->id) }}"
                                                        data-start-date="{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('Y-m-d') }}"
                                                        data-end-date="{{ \Carbon\Carbon::parse($item->tanggal_berakhir)->format('Y-m-d') }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z"></path></svg>
                                                    </button>
                                                    <form id="deleteForm{{ $item->id }}" action="{{ route('kegiatan.destroy', $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="text-red-500 hover:text-red-700 delete-kegiatan-btn" title="Hapus" data-kegiatan-id="{{ $item->id }}">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr class="border-b border-gray-100">
                                            <td colspan="6" class="py-6 text-center text-gray-500">
                                                Belum ada data Capaian Target Renstra.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                @php
                                    $currentPage = max(1, $capaianTargetCollection->currentPage());
                                    $totalItems = $capaianTargetCollection->total();
                                    $perPage = $capaianTargetCollection->perPage();
                                    $totalPages = max(ceil($totalItems / max(1, $perPage)), 1);
                                    $hasNextPage = $currentPage < $totalPages;
                                    $hasPrevPage = $currentPage > 1;
                                    $currentItemsCount = min($perPage, $totalItems - (($currentPage - 1) * $perPage));
                                @endphp
                                
                                <div class="flex items-center space-x-2">
                                    <span>Menampilkan <span class="font-semibold text-gray-900">{{ $currentItemsCount }}</span> dari <span class="font-semibold text-gray-900">{{ $totalItems }}</span></span>
                            </div>
                            </div>
                            
                            <!-- Pagination Controls -->
                            <div class="flex items-center space-x-2">
                                <div>
                                    <select name="per_page" id="perPageSelect"
                                        class="items-center mr-2 px-3 py-1.5 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 text-sm">
                                        @foreach ([5, 10, 15, 25, 50] as $option)
                                            <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                                                {{ $option }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <!-- Previous Button -->
                                    @if($hasPrevPage)
                                        <a href="{{ $capaianTargetCollection->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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
                                                    <a href="{{ $capaianTargetCollection->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
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
                                        <a href="{{ $capaianTargetCollection->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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
        </div>

        <!-- Modal Tambah Periode Renstra -->
        <div id="modalTambahPeriodeRenstra" class="fixed inset-0 z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
            <div class="fixed inset-0 bg-black opacity-50" id="modalPeriodeOverlay"></div>
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md relative z-10 scale-95 transition-transform duration-300">
                <div class="p-5">
                    <div class="relative border-b pb-3 mb-4">
                        <button id="btnClosePeriodeModal"
                            class="absolute right-2 -top-2 text-gray-500 hover:text-gray-700 z-50 p-2 cursor-pointer bg-white rounded-full shadow-md hover:shadow-lg transition-all duration-200"
                            type="button"
                            title="Tutup Modal">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <div class="flex justify-center">
                            <h3 class="text-lg font-bold text-red-600">Tambah Periode Renstra</h3>
                        </div>
                    </div>

                    <form action="{{ route('perencanaan.renstra.store') }}" method="POST" id="formRenstra">
                        @csrf
                        <div class="mb-4">
                            <label for="nama_renstra" class="block text-sm font-bold mb-1">Nama Renstra</label>
                            <input type="text" id="nama_renstra" name="nama_renstra" value="Rencana Strategis"
                                class="w-full p-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="tahun_awal" class="block text-sm font-bold mb-1">Tahun Awal</label>
                                <div class="relative">
                                    <select id="tahun_awal" name="tahun_awal"
                                        class="w-full p-2 border border-gray-300 rounded-md" required
                                        onchange="setTahunAkhir()">
                                        <option value="">Pilih Tahun</option>
                                        @php
                                            $currentYear = date('Y');
                                            for ($year = $currentYear - 10; $year <= $currentYear + 10; $year++) {
                                                echo "<option value=\"$year\">$year</option>";
                                            }
                                        @endphp
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="tahun_akhir" class="block text-sm font-bold mb-1">Tahun Akhir</label>
                                <div class="relative">
                                    <input type="text" id="tahun_akhir" name="tahun_akhir"
                                        class="w-full p-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                                    <input type="hidden" id="periode_awal" name="periode_awal">
                                    <input type="hidden" id="periode_akhir" name="periode_akhir">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Periode Renstra selalu 5 tahun</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-bold mb-1">Tanggal Mulai</label>
                                <div class="relative">
                                    <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                                        class="w-full p-2 border text-sm text-gray-700 border-gray-300 rounded-md"
                                        required>
                                </div>
                            </div>
                            <div>
                                <label for="tanggal_selesai" class="block text-sm font-bold mb-1">Tanggal Selesai</label>
                                <div class="relative">
                                    <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                                        class="w-full p-2 border text-sm text-gray-700 border-gray-300 rounded-md"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" id="btnSubmitRenstra"
                                class="flex items-center justify-center bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-md transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Kegiatan (Reviu Renstra, Reviu Target Renstra, dan Capaian Target Renstra) -->
        <div id="modalTambahKegiatan" class="fixed inset-0 z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
            <div class="fixed inset-0 bg-black opacity-50" id="modalKegiatanOverlay"></div>
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md relative z-10 scale-95 transition-transform duration-300">
                <div class="p-5">
                    <div class="relative border-b pb-3 mb-4">
                        <div class="flex justify-center">
                            <h3 class="text-lg font-bold" id="modalTitle">Tambah Kegiatan</h3>
                        </div>
                        <button id="btnCloseModal" class="absolute right-0 top-0 text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('kegiatan.store') }}" method="POST" id="formModalKegiatan">
                        @csrf

                        <!-- HIDDEN INPUTS -->
                        <input type="hidden" name="jenis_kegiatan" id="jenis_kegiatan" value="">
                        <input type="hidden" name="renstra_id" id="renstra_id" value="{{ $activeRenstra->id ?? '' }}">

                        <!-- SUB KOMPONEN ID -->
                        @php
                            // Cari sub komponen Manajemen Renstra dengan berbagai cara
                            $manajemenRenstraId = null;

                            if (isset($manajemenRenstraSubKomponen) && $manajemenRenstraSubKomponen) {
                                $manajemenRenstraId = $manajemenRenstraSubKomponen->id;
                            } else {
                                // Fallback: cari berdasarkan sub_komponen field
                                $found = $subKomponenList->first(function ($item) {
                                    return stripos($item->sub_komponen, 'Manajemen Renstra') !== false;
                                });
                                if ($found) {
                                    $manajemenRenstraId = $found->id;
                                }
                            }
                        @endphp

                        <input type="hidden" name="sub_komponen_id" value="{{ $manajemenRenstraId }}">

                        <!-- Nama Kegiatan (Read Only) -->
                        <div class="mb-4">
                            <label for="nama_kegiatan" class="block text-sm font-bold mb-1">Nama Kegiatan</label>
                            <input type="text" id="nama_kegiatan" name="nama_kegiatan"
                                class="w-full p-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                        </div>

                        <!-- Tahun Berjalan -->
                        <div class="mb-4">
                            <label for="tahun_berjalan" class="block text-sm font-bold mb-1">Tahun Berjalan</label>
                            <select id="tahun_berjalan" name="tahun_berjalan"
                                class="w-full p-2 border border-gray-300 rounded-md appearance-none pr-8" required>
                                <option value="">Pilih Tahun</option>
                            </select>
                            <div id="tahun-warning" class="text-xs text-red-500 mt-1 hidden">
                                Tahun ini sudah digunakan untuk jenis kegiatan yang sama
                            </div>
                        </div>

                        <!-- Tanggal Mulai dan Berakhir -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-bold mb-1">Tanggal Mulai</label>
                                <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                                    class="w-full p-2 border text-sm text-gray-700 border-gray-300 rounded-md" required>
                            </div>
                            <div>
                                <label for="tanggal_berakhir" class="block text-sm font-bold mb-1">Tanggal Selesai</label>
                                <input type="date" id="tanggal_berakhir" name="tanggal_berakhir"
                                    class="w-full p-2 border text-sm text-gray-700 border-gray-300 rounded-md" required>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" id="btnSubmitModal"
                                class="flex items-center justify-center text-white px-6 py-2 rounded-md transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Simpan
                            </button>
                        </div>

                        <!-- Error Display -->
                        @if ($errors->any())
                            <div class="mt-4 text-red-500 text-sm">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Tanggal Kegiatan -->
        <div id="modalEditKegiatan" class="fixed inset-0 z-50 flex items-center justify-center hidden">
            <div class="fixed inset-0 bg-black opacity-50" id="modalEditOverlay"></div>
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md relative z-10">
                <div class="p-5">
                    <div class="relative border-b pb-3 mb-4">
                        <div class="flex justify-center">
                            <h3 class="text-lg font-bold text-gray-900">Edit Tanggal Kegiatan</h3>
                        </div>
                        <button id="btnCloseEditModal" class="absolute right-0 top-0 text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <form id="formEditKegiatan" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="edit_tanggal_mulai" class="block text-sm font-bold mb-1">Tanggal Mulai</label>
                                <input type="date" id="edit_tanggal_mulai" name="tanggal_mulai" class="w-full p-2 border border-gray-300 rounded-md" required>
                            </div>
                            <div>
                                <label for="edit_tanggal_berakhir" class="block text-sm font-bold mb-1">Tanggal Selesai</label>
                                <input type="date" id="edit_tanggal_berakhir" name="tanggal_berakhir" class="w-full p-2 border border-gray-300 rounded-md" required>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" id="btnCancelEdit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm transition-colors">Batal</button>
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm transition-colors">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Function to set tahun akhir automatically
        function initializeRenstraPeriodForm() {
            const tahunAwalInput = document.getElementById('tahun_awal');
            const tahunAkhirInput = document.getElementById('tahun_akhir');
            const periodeAwalInput = document.getElementById('periode_awal');
            const periodeAkhirInput = document.getElementById('periode_akhir');
            const tanggalMulaiRenstra = document.getElementById('tanggal_mulai');
            const tanggalSelesaiRenstra = document.getElementById('tanggal_selesai');

            // Collect existing Renstra years
            const existingYears = [];
            const renstraRows = document.querySelectorAll('#renstraTable tbody tr');
            renstraRows.forEach(row => {
                const periodCell = row.querySelector('td:nth-child(2)');
                if (periodCell) {
                    const periodText = periodCell.textContent.trim();
                    const yearMatch = periodText.match(/(\d{4})\s*-\s*(\d{4})/);
                    if (yearMatch) {
                        existingYears.push(parseInt(yearMatch[1]), parseInt(yearMatch[2]));
                    }
                }
            });

            // Get the last period's end year from the last Renstra entry
            const lastPeriodEndYearElement = document.querySelector('#renstraTable tbody tr:first-child td:nth-child(2)');
            let lastPeriodEndYear = null;

            if (lastPeriodEndYearElement) {
                const periodText = lastPeriodEndYearElement.textContent.trim();
                const yearMatch = periodText.match(/(\d{4})\s*-\s*(\d{4})/);
                if (yearMatch) {
                    lastPeriodEndYear = parseInt(yearMatch[2]);
                }
            }

            // If no last period found, use current year
            if (!lastPeriodEndYear) {
                lastPeriodEndYear = new Date().getFullYear();
            }

            // Generate suggested years
            const suggestedYears = [
                lastPeriodEndYear + 1,  // Next period after last
                lastPeriodEndYear + 6,  // Another future period
                lastPeriodEndYear + 11, // Another future period
                lastPeriodEndYear - 4,  // One period before last
                lastPeriodEndYear - 9   // Another past period
            ];

            // Sort, remove duplicates, and filter out existing years
            const uniqueYears = [...new Set(suggestedYears)]
                .filter(year => !existingYears.includes(year))
                .sort((a, b) => a - b);

            // Clear existing options
            tahunAwalInput.innerHTML = '';

            // Add default option
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.text = 'Pilih Tahun';
            tahunAwalInput.appendChild(defaultOption);

            // Add year options
            uniqueYears.forEach(year => {
                const option = document.createElement('option');
                option.value = year;
                option.text = year;
                tahunAwalInput.appendChild(option);
            });

            // Attach event listener to handle year selection
            tahunAwalInput.addEventListener('change', function(event) {
                console.log('Year selected:', this.value); // Debugging log

                if (this.value) {
                    const tahunAwal = parseInt(this.value);
                    const tahunAkhir = tahunAwal + 4; // 5 tahun periode

                    // Set tahun akhir input
                    tahunAkhirInput.value = tahunAkhir;

                    // Set periode awal dan akhir (format YYYY-MM-DD)
                    periodeAwalInput.value = `${tahunAwal}-01-01`;
                    periodeAkhirInput.value = `${tahunAkhir}-12-31`;

                    // Set tanggal mulai dan selesai
                    tanggalMulaiRenstra.value = `${tahunAwal}-01-01`;
                    tanggalSelesaiRenstra.value = `${tahunAkhir}-12-31`;
                } else {
                    // Reset all inputs if no start year is selected
                    tahunAkhirInput.value = '';
                    periodeAwalInput.value = '';
                    periodeAkhirInput.value = '';
                    tanggalMulaiRenstra.value = '';
                    tanggalSelesaiRenstra.value = '';
                }
            });

            // Ensure dropdown is fully interactive
            tahunAwalInput.removeAttribute('disabled');
        }

        // Call initialization when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure the form exists before initializing
            const tahunAwalInput = document.getElementById('tahun_awal');
            if (tahunAwalInput) {
                initializeRenstraPeriodForm();
            } else {
                console.error('Tahun Awal input not found');
            }
        });

        // Additional debugging to catch any potential issues
        window.addEventListener('load', function() {
            const tahunAwalInput = document.getElementById('tahun_awal');
            if (tahunAwalInput) {
                console.log('Tahun Awal Input Properties:', {
                    disabled: tahunAwalInput.disabled,
                    options: tahunAwalInput.options.length
                });
            }
        });

        // Function to populate 'Tahun Berjalan' dropdown for kegiatan forms
        function populateKegiatanTahunBerjalan(jenisKegiatan) {
            const tahunBerjalanSelect = document.getElementById('tahun_berjalan');
            tahunBerjalanSelect.innerHTML = ''; // Clear existing options

            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.text = 'Pilih Tahun';
            tahunBerjalanSelect.appendChild(defaultOption);

            const currentYear = new Date().getFullYear();
            const yearsToSuggest = [];

            // Determine existing years based on jenisKegiatan
            const existingData = @json([
                'reviu_renstra' => $reviuRenstra ?? collect(), // Use reviuRenstra for 'reviu_renstra' type activities
                'reviu_target_renstra' => $reviuTargetRenstra ?? collect(),
                'capaian_target_renstra' => $capaianTargetRenstra ?? collect()
            ]);

            let yearsInUse = [];
            if (existingData[jenisKegiatan]) {
                yearsInUse = existingData[jenisKegiatan].map(item => parseInt(item.tahun_berjalan));
            }

            // Suggest years from current year + 5 to current year - 5, excluding years in use
            for (let year = currentYear + 5; year >= currentYear - 5; year--) {
                if (!yearsInUse.includes(year)) {
                    yearsToSuggest.push(year);
                }
            }

            yearsToSuggest.sort((a, b) => a - b); // Sort years ascending

            yearsToSuggest.forEach(year => {
                const option = document.createElement('option');
                option.value = year;
                option.text = year;
                tahunBerjalanSelect.appendChild(option);
            });

            // Reset the selected value to the default 'Pilih Tahun' after populating
            tahunBerjalanSelect.value = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // ========== ELEMENT SELECTORS ==========
            // Modal elements untuk kegiatan
            const modalTambahKegiatan = document.getElementById('modalTambahKegiatan');
            const btnCloseModal = document.getElementById('btnCloseModal');
            const modalOverlay = document.getElementById('modalOverlay');
            const modalTitle = document.getElementById('modalTitle');
            const jenisKegiatanInput = document.getElementById('jenis_kegiatan');
            const namaKegiatanInput = document.getElementById('nama_kegiatan');
            const btnSubmitModal = document.getElementById('btnSubmitModal');
            const tahunBerjalanInput = document.getElementById('tahun_berjalan');

            // Modal elements untuk periode renstra
            const modalTambahPeriodeRenstra = document.getElementById('modalTambahPeriodeRenstra');
            const btnClosePeriodeModal = document.getElementById('btnClosePeriodeModal');
            const modalPeriodeOverlay = document.getElementById('modalPeriodeOverlay');

            // Form elements untuk renstra
            const formRenstra = document.getElementById('formRenstra');
            const tahunAwalInputRenstra = document.getElementById('tahun_awal');
            const tahunAkhirInputRenstra = document.getElementById('tahun_akhir');
            const periodeAwalInputRenstra = document.getElementById('periode_awal');
            const periodeAkhirInputRenstra = document.getElementById('periode_akhir');
            const tanggalMulaiRenstraForm = document.getElementById('tanggal_mulai');
            const tanggalSelesaiRenstraForm = document.getElementById('tanggal_selesai');

            // Form elements untuk kegiatan (tanggal mulai/berakhir)
            const tanggalMulaiKegiatan = document.querySelector('#modalTambahKegiatan input[name="tanggal_mulai"]');
            const tanggalBerakhirKegiatan = document.querySelector('#modalTambahKegiatan input[name="tanggal_berakhir"]');

            // Button elements
            const btnTambahDokumenRenstra = document.getElementById('btnTambahDokumenRenstra');
            const btnTambahKegiatan = document.getElementById('btnTambahKegiatan');
            const btnTambahKegiatanTarget = document.getElementById('btnTambahKegiatanTarget');
            const btnTambahCapaian = document.getElementById('btnTambahCapaian');

            // Search and filter elements
            const searchInput = document.getElementById('searchInput');
            const filterTahun = document.getElementById('filterTahun');
            const filterKategori = document.getElementById('filterKategori');
            const applyFilterBtn = document.getElementById('applyFilter');

            // Initialize Renstra Period Form separately
            if (tahunAwalInputRenstra) {
                initializeRenstraPeriodForm();
            }

            // Search and filter functionality
            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const selectedYear = filterTahun.value;
                const selectedCategory = filterKategori.value;

                // Get all kegiatan cards in overview tab
                const allCards = document.querySelectorAll('.kegiatan-card');
                let visibleCount = 0;

                allCards.forEach(card => {
                    const cardTitle = card.querySelector('h3').textContent.toLowerCase();
                    const cardYear = card.getAttribute('data-tahun');
                    const cardCategory = card.getAttribute('data-kategori');

                    // Check if card matches search criteria
                    const matchesSearch = searchTerm === '' || cardTitle.includes(searchTerm);
                    const matchesYear = selectedYear === '' || cardYear === selectedYear;
                    const matchesCategory = selectedCategory === '' || cardCategory === selectedCategory;

                    const shouldShow = matchesSearch && matchesYear && matchesCategory;

                    if (shouldShow) {
                        card.style.display = 'block';
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                        card.classList.add('hidden');
                    }
                });

                // Get all table rows in other tabs
                const allTableRows = document.querySelectorAll('tbody tr');
                allTableRows.forEach(row => {
                    // Skip empty state rows and no-results-message rows
                    if (row.querySelector('td[colspan]') || row.classList.contains('no-results-message')) {
                        return;
                    }

                    const firstCell = row.querySelector('td:first-child');
                    if (!firstCell) return;

                    const rowTitle = firstCell.textContent.toLowerCase();
                    const rowYear = extractYearFromTitle(rowTitle);
                    const rowCategory = getCategoryFromRow(row);

                    // Check if row matches search criteria
                    const matchesSearch = searchTerm === '' || rowTitle.includes(searchTerm);
                    const matchesYear = selectedYear === '' || rowYear === selectedYear;
                    const matchesCategory = selectedCategory === '' || rowCategory === selectedCategory;

                    const shouldShow = matchesSearch && matchesYear && matchesCategory;

                    if (shouldShow) {
                        row.style.display = '';
                        row.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                        row.classList.add('hidden');
                    }
                });

                // Update results info and counter
                updateSearchResults(visibleCount, searchTerm, selectedYear, selectedCategory);
                updateSearchCounter(visibleCount, searchTerm);
                updateClearButton(searchTerm, selectedYear, selectedCategory);
            }

            // Helper function to extract year from title
            function extractYearFromTitle(title) {
                const yearMatch = title.match(/(\d{4})/);
                return yearMatch ? yearMatch[1] : '';
            }

            // Helper function to determine category from table row context
            function getCategoryFromRow(row) {
                const activeTab = document.querySelector('.tab-btn.active');
                if (!activeTab) return '';

                const tabType = activeTab.getAttribute('data-tab');
                switch (tabType) {
                    case 'renstra':
                        return 'Renstra';
                    case 'reviu-renstra':
                        return 'Reviu Renstra';
                    case 'reviu-target':
                        return 'Reviu Target Renstra';
                    case 'capaian-target':
                        return 'Capaian Target Renstra';
                    default:
                        return '';
                }
            }

            function updateSearchResults(count, searchTerm, year, category) {
                // Remove existing results info
                const existingInfo = document.querySelector('.search-results-info');
                if (existingInfo) {
                    existingInfo.remove();
                }

                // Create results info only if filters are active
                if (searchTerm || year || category) {
                    const resultsInfo = document.createElement('div');
                    resultsInfo.className = 'search-results-info bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm text-blue-800';
                    
                    let infoText = `Menampilkan ${count} hasil`;
                    if (searchTerm) infoText += ` untuk "${searchTerm}"`;
                    if (year) infoText += ` tahun ${year}`;
                    if (category) infoText += ` kategori ${category}`;

                    resultsInfo.innerHTML = `
                        <div class="flex items-center justify-between">
                            <span>${infoText}</span>
                            <button onclick="clearAllFilters()" class="text-blue-600 hover:text-blue-800 underline">
                                Hapus Filter
                            </button>
                        </div>
                    `;

                    // Insert results info in active tab container
                    const activeTab = document.querySelector('.tab-pane.active');
                    if (activeTab) {
                        const container = activeTab.querySelector('#allActivitiesContainer') || activeTab.querySelector('.overflow-x-auto');
                        if (container) {
                            container.parentNode.insertBefore(resultsInfo, container);
                        }
                    }
                }

                // Show "no results" message if no results are visible and filters are active
                if (count === 0 && (searchTerm || year || category)) {
                    showNoResultsMessage();
                } else {
                    hideNoResultsMessage();
                }
            }

            function showNoResultsMessage() {
                // Remove existing no results message
                hideNoResultsMessage();

                const activeTab = document.querySelector('.tab-pane.active');
                if (!activeTab) return;

                const noResultsDiv = document.createElement('div');
                noResultsDiv.className = 'no-results-message py-12 text-center text-gray-500';
                noResultsDiv.innerHTML = `
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium mb-2">Tidak ada hasil ditemukan</h3>
                    <p class="mb-4">Coba ubah kata kunci pencarian atau filter yang digunakan.</p>
                    <button onclick="clearAllFilters()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                        Hapus Semua Filter
                    </button>
                `;

                // Insert no results message in appropriate container
                const overviewContainer = activeTab.querySelector('#allActivitiesContainer');
                const tableContainer = activeTab.querySelector('.overflow-x-auto');
                
                if (overviewContainer) {
                    // For overview tab (grid layout)
                    noResultsDiv.className += ' col-span-3';
                    overviewContainer.appendChild(noResultsDiv);
                } else if (tableContainer) {
                    // For table tabs
                    const tableBody = tableContainer.querySelector('tbody');
                    if (tableBody) {
                        const noResultsRow = document.createElement('tr');
                        noResultsRow.className = 'no-results-message';
                        const firstRow = tableBody.querySelector('tr:first-child');
                        const colSpan = firstRow ? firstRow.children.length : 6;
                        noResultsRow.innerHTML = `
                            <td colspan="${colSpan}" class="py-12 text-center text-gray-500">
                                ${noResultsDiv.innerHTML}
                            </td>
                        `;
                        tableBody.appendChild(noResultsRow);
                    }
                }
            }

            function hideNoResultsMessage() {
                const noResultsMessages = document.querySelectorAll('.no-results-message');
                noResultsMessages.forEach(msg => msg.remove());
            }

            function clearAllFilters() {
                searchInput.value = '';
                filterTahun.value = '';
                filterKategori.value = '';
                performSearch();
                // Hide counter and clear button when filters are cleared
                const counter = document.getElementById('searchCounter');
                if (counter) {
                    counter.classList.add('hidden');
                }
                const clearBtn = document.getElementById('clearFilter');
                if (clearBtn) {
                    clearBtn.classList.add('hidden');
                }
            }

            // Make clearAllFilters globally accessible
            window.clearAllFilters = clearAllFilters;

            // Debounce function for better performance
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Add event listeners for real-time search and filter
            if (searchInput) {
                const debouncedSearch = debounce(performSearch, 300);
                searchInput.addEventListener('input', debouncedSearch);
                searchInput.addEventListener('keyup', function(e) {
                    // Immediate search on Enter key
                    if (e.key === 'Enter') {
                        performSearch();
                    }
                });
            }

            if (filterTahun) {
                filterTahun.addEventListener('change', performSearch);
            }

            if (filterKategori) {
                filterKategori.addEventListener('change', performSearch);
            }

            if (applyFilterBtn) {
                applyFilterBtn.addEventListener('click', performSearch);
            }

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Clear filters with Escape key
                if (e.key === 'Escape' && (searchInput.value || filterTahun.value || filterKategori.value)) {
                    clearAllFilters();
                } 
                // Focus search with Ctrl+F
                else if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    searchInput.focus();
                }
                // Clear filters with Ctrl+Shift+C
                else if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'C') {
                    e.preventDefault();
                    clearAllFilters();
                }
            });

            // Update search placeholder based on active tab
            function updateSearchPlaceholder() {
                const activeTab = document.querySelector('.tab-btn.active');
                if (!activeTab) return;

                const tabType = activeTab.getAttribute('data-tab');
                let placeholder = 'Cari dokumen Renstra...';

                switch (tabType) {
                    case 'overview':
                        placeholder = 'Cari semua dokumen Renstra...';
                        break;
                    case 'renstra':
                        placeholder = 'Cari dokumen Renstra...';
                        break;
                    case 'reviu-renstra':
                        placeholder = 'Cari kegiatan Reviu Renstra...';
                        break;
                    case 'reviu-target':
                        placeholder = 'Cari kegiatan Reviu Target...';
                        break;
                    case 'capaian-target':
                        placeholder = 'Cari kegiatan Capaian Target...';
                        break;
                }

                searchInput.placeholder = placeholder;
            }

            // Update search counter in search bar
            function updateSearchCounter(count, searchTerm) {
                const counter = document.getElementById('searchCounter');
                if (!counter) return;

                if (searchTerm) {
                    counter.textContent = `${count} hasil`;
                    counter.classList.remove('hidden');
                } else {
                    counter.classList.add('hidden');
                }
            }

            // Update clear button visibility
            function updateClearButton(searchTerm, year, category) {
                const clearBtn = document.getElementById('clearFilter');
                if (!clearBtn) return;

                if (searchTerm || year || category) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }

            // Highlight search terms in results
            function highlightSearchTerm(text, term) {
                if (!term) return text;
                const regex = new RegExp(`(${term})`, 'gi');
                return text.replace(regex, '<mark class="bg-yellow-200 px-1 rounded">$1</mark>');
            }

            // Update highlight on search
            function updateHighlights() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                
                // Highlight in card titles (overview tab)
                const allTitles = document.querySelectorAll('.kegiatan-card h3');
                allTitles.forEach(title => {
                    const originalText = title.getAttribute('data-original-text') || title.textContent;
                    if (!title.getAttribute('data-original-text')) {
                        title.setAttribute('data-original-text', originalText);
                    }
                    
                    if (searchTerm) {
                        title.innerHTML = highlightSearchTerm(originalText, searchTerm);
                    } else {
                        title.textContent = originalText;
                    }
                });

                // Highlight in table rows (other tabs)
                const allTableCells = document.querySelectorAll('tbody tr td:first-child');
                allTableCells.forEach(cell => {
                    const originalText = cell.getAttribute('data-original-text') || cell.textContent;
                    if (!cell.getAttribute('data-original-text')) {
                        cell.setAttribute('data-original-text', originalText);
                    }
                    
                    if (searchTerm) {
                        cell.innerHTML = highlightSearchTerm(originalText, searchTerm);
                    } else {
                        cell.textContent = originalText;
                    }
                });
            }

            // Override performSearch to include highlighting
            const originalPerformSearch = performSearch;
            performSearch = function() {
                originalPerformSearch();
                updateHighlights();
            };

            // Tab elements
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-pane');

            // Tab navigation functionality
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Simpan tab aktif di local storage untuk persistensi
                    localStorage.setItem('activeRenstraTab', this.getAttribute('data-tab'));

                    // Remove active class from all tabs
                    tabButtons.forEach(btn => {
                        btn.classList.remove('text-red-600', 'border-b-2', 'border-red-600', 'active');
                        btn.classList.add('text-gray-500');
                    });

                    // Hide all tab content
                    tabContents.forEach(content => {
                        content.classList.remove('active');
                        content.classList.add('hidden');
                    });

                    // Add active class to clicked tab
                    this.classList.add('text-red-600', 'border-b-2', 'border-red-600', 'active');
                    this.classList.remove('text-gray-500');

                    // Show corresponding tab content
                    const tabId = this.getAttribute('data-tab');
                    const activeTab = document.getElementById(tabId);
                    if (activeTab) {
                        activeTab.classList.remove('hidden');
                        activeTab.classList.add('active');
                    }

                    // Re-apply search and filters when switching tabs
                    setTimeout(performSearch, 10);
                    // Update search placeholder
                    updateSearchPlaceholder();
                    
                    // Re-attach event listeners for buttons in the new tab content
                    setTimeout(attachButtonEventListeners, 50);
                });
            });

            // Restore active tab on page load
            const savedTab = localStorage.getItem('activeRenstraTab');
            if (savedTab) {
                const activeTabBtn = document.querySelector(`.tab-btn[data-tab="${savedTab}"]`);
                if (activeTabBtn) activeTabBtn.click();
            } else {
                // Default to overview tab if no saved tab
                const overviewTab = document.querySelector('.tab-btn[data-tab="overview"]');
                if (overviewTab) overviewTab.click();
            }

            // Initialize search placeholder and perform initial search
            updateSearchPlaceholder();
            performSearch();

            // ========== EVENT LISTENERS ==========
            // Event listener untuk button tambah periode renstra
            if (btnTambahDokumenRenstra) {
                btnTambahDokumenRenstra.addEventListener('click', function() {
                    console.log('Opening periode modal...');
                    if (modalTambahPeriodeRenstra) {
                        modalTambahPeriodeRenstra.classList.remove('hidden');
                        modalTambahPeriodeRenstra.classList.remove('opacity-0');
                        
                        setTimeout(() => {
                            modalTambahPeriodeRenstra.classList.add('opacity-100');
                            const modalContent = modalTambahPeriodeRenstra.querySelector('.bg-white');
                            if (modalContent) {
                                modalContent.classList.add('scale-100');
                                modalContent.classList.remove('scale-95');
                            }
                            console.log('Modal opened successfully');
                        }, 10);
                        
                        // Re-attach event listeners after modal opens
                        setTimeout(() => {
                            attachPeriodeModalListeners();
                        }, 50);
                    }
                });
            }

            // Event listener untuk button tambah kegiatan
            if (btnTambahKegiatan) {
                btnTambahKegiatan.addEventListener('click', function() {
                    if (modalTambahKegiatan) {
                        modalTambahKegiatan.classList.remove('hidden');
                        modalTitle.textContent = 'Tambah Kegiatan Baru';
                        jenisKegiatanInput.value = 'reviu_renstra'; // Corrected to reviu_renstra
                        namaKegiatanInput.value = 'Reviu Renstra'; // Corrected name
                        btnSubmitModal.className = 'flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white px-6 py-2 rounded-md transition-colors'; // Corrected color
                        populateKegiatanTahunBerjalan('reviu_renstra'); // Populate years for reviu renstra
                        setTimeout(() => {
                            modalTambahKegiatan.classList.add('opacity-100');
                            modalTambahKegiatan.querySelector('.scale-95').classList.add('scale-100');
                            modalTambahKegiatan.querySelector('.scale-95').classList.remove('scale-95');
                        }, 10);
                    }
                });
            }

            // Event listener untuk button tambah kegiatan target
            if (btnTambahKegiatanTarget) {
                btnTambahKegiatanTarget.addEventListener('click', function() {
                    if (modalTambahKegiatan) {
                        modalTambahKegiatan.classList.remove('hidden');
                        modalTitle.textContent = 'Tambah Kegiatan Target';
                        jenisKegiatanInput.value = 'reviu_target_renstra';
                        namaKegiatanInput.value = 'Reviu Target Renstra'; // Corrected name
                        btnSubmitModal.className = 'flex items-center justify-center bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-md transition-colors';
                        populateKegiatanTahunBerjalan('reviu_target_renstra'); // Populate years for target renstra
                        setTimeout(() => {
                            modalTambahKegiatan.classList.add('opacity-100');
                            modalTambahKegiatan.querySelector('.scale-95').classList.add('scale-100');
                            modalTambahKegiatan.querySelector('.scale-95').classList.remove('scale-95');
                        }, 10);
                    }
                });
            }

            // Event listener untuk button tambah capaian
            if (btnTambahCapaian) {
                btnTambahCapaian.addEventListener('click', function() {
                    if (modalTambahKegiatan) {
                        modalTambahKegiatan.classList.remove('hidden');
                        modalTitle.textContent = 'Tambah Capaian Target';
                        jenisKegiatanInput.value = 'capaian_target_renstra';
                        namaKegiatanInput.value = 'Capaian Target Renstra';
                        btnSubmitModal.className = 'flex items-center justify-center bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md transition-colors';
                        populateKegiatanTahunBerjalan('capaian_target_renstra'); // Populate years for capaian target renstra
                        setTimeout(() => {
                            modalTambahKegiatan.classList.add('opacity-100');
                            modalTambahKegiatan.querySelector('.scale-95').classList.add('scale-100');
                            modalTambahKegiatan.querySelector('.scale-95').classList.remove('scale-95');
                        }, 10);
                    }
                });
            }

            // Event listeners untuk menutup modal kegiatan
            if (btnCloseModal) {
                btnCloseModal.addEventListener('click', function() {
                    if (modalTambahKegiatan) {
                        modalTambahKegiatan.classList.remove('opacity-100');
                        modalTambahKegiatan.querySelector('.scale-100').classList.add('scale-95');
                        modalTambahKegiatan.querySelector('.scale-100').classList.remove('scale-100');
                        setTimeout(() => {
                            modalTambahKegiatan.classList.add('hidden');
                        }, 300);
                    }
                });
            }

            // Validasi tahun berdasarkan jenis kegiatan
            const existingDataRaw = @json([
                'reviu_renstra' => $reviuRenstra ?? collect(),
                'reviu_target_renstra' => $reviuTargetRenstra ?? collect(),
                'capaian_target_renstra' => $capaianTargetRenstra ?? collect()
            ]);

            // Convert collections to plain arrays for easier JS manipulation
            const existingData = {};
            for (const key in existingDataRaw) {
                if (existingDataRaw.hasOwnProperty(key)) {
                    existingData[key] = existingDataRaw[key].map(item => ({
                        id: item.id,
                        nama_kegiatan: item.nama_kegiatan,
                        tahun_berjalan: item.tahun_berjalan
                    }));
                }
            }

            function validateYearSelection() {
                const jenisKegiatan = jenisKegiatanInput.value;
                const selectedYear = tahunBerjalanInput.value;
                const warningDiv = document.getElementById('tahun-warning');

                if (!jenisKegiatan || !selectedYear) {
                    if (warningDiv) warningDiv.classList.add('hidden');
                    return true;
                }

                // Get existing years for current type from the already converted existingData
                let yearsInUse = [];
                if (existingData[jenisKegiatan]) {
                    yearsInUse = existingData[jenisKegiatan].map(item => parseInt(item.tahun_berjalan));
                }

                const isDuplicate = yearsInUse.includes(parseInt(selectedYear));

                if (isDuplicate && warningDiv) {
                    warningDiv.classList.remove('hidden');
                    warningDiv.textContent = `Tahun ${selectedYear} sudah digunakan untuk jenis kegiatan ini`;
                    return false;
                } else if (warningDiv) {
                    warningDiv.classList.add('hidden');
                    return true;
                }

                return true;
            }

            // Add event listeners for validation
            if (jenisKegiatanInput && tahunBerjalanInput) {
                // The populateKegiatanTahunBerjalan function now handles setting the options,
                // so the initial change listener here might be redundant or need adjustment.
                // Keeping it for now, but main population is done on modal open.
                jenisKegiatanInput.addEventListener('change', function() {
                    populateKegiatanTahunBerjalan(this.value);
                    validateYearSelection();
                });
                tahunBerjalanInput.addEventListener('change', validateYearSelection);
            }

            // Validate on form submit for modal kegiatan
            const formModalKegiatan = document.getElementById('formModalKegiatan');
            if (formModalKegiatan) {
                formModalKegiatan.addEventListener('submit', function(e) {
                    if (!validateYearSelection()) {
                        e.preventDefault();
                        alert('Silakan pilih tahun yang berbeda. Tahun yang dipilih sudah digunakan untuk jenis kegiatan ini.');
                        return;
                    }
                    
                    // Prevent default form submission
                    e.preventDefault();
                    
                    // Close modal first to prevent z-index conflicts
                    const modalTambahKegiatan = document.getElementById('modalTambahKegiatan');
                    if (modalTambahKegiatan) {
                        modalTambahKegiatan.classList.add('hidden');
                    }
                    
                    // Small delay to ensure modal is closed before showing loading
                    setTimeout(() => {
                        // Preserve active tab state before form submission
                        const activeTab = document.querySelector('.tab-btn.active');
                        if (activeTab) {
                            localStorage.setItem('activeRenstraTab', activeTab.getAttribute('data-tab'));
                        }
                        
                        // Show enhanced loading overlay with proper z-index
                        showGlobalLoadingEnhanced('Menyimpan kegiatan baru...');
                        
                        // Submit the form
                        formModalKegiatan.submit();
                    }, 150);
                });
            }

            // Add loading overlay to form renstra (tambah periode renstra)
            if (formRenstra) {
                formRenstra.addEventListener('submit', function(e) {
                    // Preserve active tab state before form submission
                    const activeTab = document.querySelector('.tab-btn.active');
                    if (activeTab) {
                        localStorage.setItem('activeRenstraTab', activeTab.getAttribute('data-tab'));
                    }
                    
                    // Show enhanced loading overlay with proper z-index
                    showGlobalLoadingEnhanced('Menyimpan periode renstra baru...');
                    
                    // Disable submit button to prevent multiple submissions
                    const submitBtn = document.getElementById('btnSubmitRenstra');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        `;
                    }
                });
            }

            if (modalOverlay) {
                modalOverlay.addEventListener('click', function() {
                    if (modalTambahKegiatan) {
                        modalTambahKegiatan.classList.add('hidden');
                    }
                });
            }

            // Close modal kegiatan with ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modalTambahKegiatan && !modalTambahKegiatan.classList.contains('hidden')) {
                    modalTambahKegiatan.classList.add('hidden');
                }
            });

            // Function to close modal periode renstra
            function closePeriodeModal() {
                console.log('closePeriodeModal called');
                const modal = document.getElementById('modalTambahPeriodeRenstra');
                if (modal) {
                    console.log('Modal found, closing...');
                    modal.classList.remove('opacity-100');
                    modal.classList.add('opacity-0');
                    
                    const modalContent = modal.querySelector('.bg-white');
                    if (modalContent) {
                        modalContent.classList.remove('scale-100');
                        modalContent.classList.add('scale-95');
                    }
                    
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        console.log('Modal hidden');
                    }, 300);
                } else {
                    console.error('Modal not found');
                }
            }

            // Event listeners untuk menutup modal periode renstra
            function attachPeriodeModalListeners() {
                console.log('Attaching periode modal listeners...');
                const btnClosePeriodeModal = document.getElementById('btnClosePeriodeModal');
                const modalPeriodeOverlay = document.getElementById('modalPeriodeOverlay');
                
                if (btnClosePeriodeModal) {
                    console.log('Close button found, attaching listeners...');
                    
                    // Remove all existing event listeners by cloning
                    const newCloseBtn = btnClosePeriodeModal.cloneNode(true);
                    btnClosePeriodeModal.parentNode.replaceChild(newCloseBtn, btnClosePeriodeModal);
                    
                    // Ensure button is clickable
                    newCloseBtn.style.pointerEvents = 'auto';
                    newCloseBtn.style.zIndex = '9999';
                    newCloseBtn.style.position = 'absolute';
                    
                    // Add multiple event listeners for maximum compatibility
                    newCloseBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Close periode modal button clicked via addEventListener');
                        closePeriodeModal();
                        return false;
                    }, true); // Use capture phase
                    
                    newCloseBtn.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Close periode modal button mousedown');
                        closePeriodeModal();
                        return false;
                    });
                    
                    // Add onclick as additional fallback
                    newCloseBtn.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Close periode modal button clicked via onclick');
                        closePeriodeModal();
                        return false;
                    };
                    
                    // Add touch event for mobile
                    newCloseBtn.addEventListener('touchstart', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Close periode modal button touched');
                        closePeriodeModal();
                        return false;
                    });
                    
                    console.log('All event listeners attached to close button');
                } else {
                    console.error('btnClosePeriodeModal not found!');
                }

                if (modalPeriodeOverlay) {
                    modalPeriodeOverlay.removeEventListener('click', handleOverlayClick);
                    modalPeriodeOverlay.addEventListener('click', handleOverlayClick);
                    console.log('Overlay click listener attached');
                } else {
                    console.error('modalPeriodeOverlay not found!');
                }
            }
            
            function handleOverlayClick(e) {
                if (e.target === e.currentTarget) {
                    console.log('Modal overlay clicked - closing modal');
                    closePeriodeModal();
                }
            }
            
            // Attach listeners immediately
            attachPeriodeModalListeners();

            // Close modal periode renstra with ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modalTambahPeriodeRenstra && !modalTambahPeriodeRenstra.classList.contains('hidden')) {
                    closePeriodeModal();
                }
            });

            // Close edit modal with ESC key
            document.addEventListener('keydown', function(e) {
                const modalEditKegiatan = document.getElementById('modalEditKegiatan');
                if (e.key === 'Escape' && modalEditKegiatan && !modalEditKegiatan.classList.contains('hidden')) {
                    modalEditKegiatan.classList.add('hidden');
                }
            });

            // Function to attach event listeners to buttons
            function attachButtonEventListeners() {
                // Handle edit kegiatan buttons
                document.querySelectorAll('.edit-kegiatan-btn').forEach(button => {
                    // Remove existing event listener to prevent duplicates
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);
                    
                    newButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const url = this.getAttribute('data-url');
                        const startDate = this.getAttribute('data-start-date');
                        const endDate = this.getAttribute('data-end-date');

                        const formEditKegiatan = document.getElementById('formEditKegiatan');
                        const inputMulaiEdit = document.getElementById('edit_tanggal_mulai');
                        const inputBerakhirEdit = document.getElementById('edit_tanggal_berakhir');
                        const modalEditKegiatan = document.getElementById('modalEditKegiatan');

                        if (formEditKegiatan && inputMulaiEdit && inputBerakhirEdit && modalEditKegiatan) {
                            formEditKegiatan.action = url;
                            inputMulaiEdit.value = startDate;
                            inputBerakhirEdit.value = endDate;
                            modalEditKegiatan.classList.remove('hidden');
                        }
                    });
                });
                
                // Handle delete kegiatan with global modal
                document.querySelectorAll('.delete-kegiatan-btn').forEach(button => {
                    // Remove existing event listener to prevent duplicates
                    button.removeEventListener('click', handleDeleteClick);
                    button.addEventListener('click', handleDeleteClick);
                });
                
                // Handle delete renstra with global modal
                document.querySelectorAll('.delete-renstra-btn').forEach(button => {
                    // Remove existing event listener to prevent duplicates
                    button.removeEventListener('click', handleDeleteRenstraClick);
                    button.addEventListener('click', handleDeleteRenstraClick);
                });
                
                // Add loading overlay to edit form
                const formEditKegiatan = document.getElementById('formEditKegiatan');
                if (formEditKegiatan) {
                    // Remove existing event listener to prevent duplicates
                    const newFormEdit = formEditKegiatan.cloneNode(true);
                    formEditKegiatan.parentNode.replaceChild(newFormEdit, formEditKegiatan);
                    
                    newFormEdit.addEventListener('submit', function(e) {
                        // Preserve active tab state before form submission
                        const activeTab = document.querySelector('.tab-btn.active');
                        if (activeTab) {
                            localStorage.setItem('activeRenstraTab', activeTab.getAttribute('data-tab'));
                        }
                        
                        // Show enhanced loading overlay with proper z-index
                        showGlobalLoadingEnhanced('Menyimpan perubahan kegiatan...');
                        
                        // Disable submit button to prevent multiple submissions
                        const submitBtn = newFormEdit.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = `
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            `;
                        }
                    });
                }
                
                // Re-attach modal close event listeners
                const modalEditKegiatan = document.getElementById('modalEditKegiatan');
                const btnCloseEditModal = document.getElementById('btnCloseEditModal');
                const modalEditOverlay = document.getElementById('modalEditOverlay');
                const btnCancelEdit = document.getElementById('btnCancelEdit');

                function closeEditModal() {
                    if (modalEditKegiatan) {
                        modalEditKegiatan.classList.add('hidden');
                    }
                }

                // Remove existing listeners and add new ones
                if (btnCloseEditModal) {
                    const newCloseBtn = btnCloseEditModal.cloneNode(true);
                    btnCloseEditModal.parentNode.replaceChild(newCloseBtn, btnCloseEditModal);
                    newCloseBtn.addEventListener('click', closeEditModal);
                }

                if (btnCancelEdit) {
                    const newCancelBtn = btnCancelEdit.cloneNode(true);
                    btnCancelEdit.parentNode.replaceChild(newCancelBtn, btnCancelEdit);
                    newCancelBtn.addEventListener('click', closeEditModal);
                }

                if (modalEditOverlay) {
                    const newOverlay = modalEditOverlay.cloneNode(true);
                    modalEditOverlay.parentNode.replaceChild(newOverlay, modalEditOverlay);
                    newOverlay.addEventListener('click', closeEditModal);
                }
            }

            // Function to close global modal
            function closeModal() {
                const modal = document.getElementById('globalModal');
                if (modal) {
                    modal.remove();
                }
            }

            // Delete button click handler
            function handleDeleteClick() {
                const kegiatanId = this.dataset.kegiatanId;
                
                showModal('warning', 'Konfirmasi Hapus Kegiatan', 'Anda yakin ingin menghapus kegiatan ini? Folder terkait di Google Drive juga akan dipindahkan ke sampah.', {
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    showCancel: true,
                    confirmCallback: function() {
                        // Close modal first to prevent z-index conflicts
                        closeModal();
                        
                        // Small delay to ensure modal is closed before showing loading
                        setTimeout(() => {
                            // Preserve active tab state before deletion
                            const activeTab = document.querySelector('.tab-btn.active');
                            if (activeTab) {
                                localStorage.setItem('activeRenstraTab', activeTab.getAttribute('data-tab'));
                            }
                            
                            // Show enhanced loading overlay with proper z-index
                            showGlobalLoadingEnhanced('Menghapus kegiatan dan folder terkait...');
                            
                            // Submit the delete form
                            document.getElementById('deleteForm' + kegiatanId).submit();
                        }, 150);
                    }
                });
            }

            // Delete Renstra button click handler
            function handleDeleteRenstraClick() {
                const renstraId = this.dataset.renstraId;
                
                showModal('warning', 'Konfirmasi Hapus Renstra', 'Anda yakin ingin menghapus data Renstra ini? Tindakan ini tidak dapat dibatalkan.', {
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    showCancel: true,
                    confirmCallback: function() {
                        // Close modal first to prevent z-index conflicts
                        closeModal();
                        
                        // Small delay to ensure modal is closed before showing loading
                        setTimeout(() => {
                            // Preserve active tab state before deletion
                            const activeTab = document.querySelector('.tab-btn.active');
                            if (activeTab) {
                                localStorage.setItem('activeRenstraTab', activeTab.getAttribute('data-tab'));
                            }
                            
                            // Show enhanced loading overlay with proper z-index
                            showGlobalLoadingEnhanced('Menghapus data Renstra...');
                            
                            // Submit the delete form with correct ID
                            const deleteForm = document.getElementById('deleteFormRenstra' + renstraId);
                            if (deleteForm) {
                                deleteForm.submit();
                            } else {
                                console.error('Form dengan ID deleteFormRenstra' + renstraId + ' tidak ditemukan');
                                hideGlobalLoadingEnhanced();
                            }
                        }, 150);
                    }
                });
            }

            // Initial attachment of event listeners
            attachButtonEventListeners();
            
            // Re-attach periode modal listeners to ensure they work
            attachPeriodeModalListeners();
            
            // Final safety check to ensure modal close button works
            setTimeout(() => {
                const btnClosePeriodeModal = document.getElementById('btnClosePeriodeModal');
                if (btnClosePeriodeModal) {
                    console.log('Final check: Periode modal close button found');
                    // Ensure button is clickable and visible
                    btnClosePeriodeModal.style.pointerEvents = 'auto';
                    btnClosePeriodeModal.style.zIndex = '9999';
                    
                    // Test if button responds to click
                    btnClosePeriodeModal.addEventListener('mouseenter', function() {
                        console.log('Mouse entered close button - button is responsive');
                    });
                } else {
                    console.error('btnClosePeriodeModal not found in DOM');
                }
            }, 200);
        });

        // Function to update row limit and reload page
        function updateRowLimit(selectElement, tabName) {
            const perPage = selectElement.value;
            
            // Get current URL
            let url = new URL(window.location.href);
            
            // Set per_page parameter based on tab
            switch(tabName) {
                case 'renstra':
                    url.searchParams.set('per_page', perPage);
                    url.searchParams.delete('renstra_page');
                    break;
                case 'reviu-renstra':
                    url.searchParams.set('per_page', perPage);
                    url.searchParams.delete('reviu_renstra_page');
                    break;
                case 'reviu-target-renstra':
                    url.searchParams.set('per_page', perPage);
                    url.searchParams.delete('reviu_target_page');
                    break;
                case 'capaian-target-renstra':
                    url.searchParams.set('per_page', perPage);
                    url.searchParams.delete('capaian_target_page');
                    break;
            }

            // Reload page with new parameters
            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Handle perPage change for Renstra (now specifically for the Renstra tab's pagination, not overview)
            const perPageSelectRenstraElement = document.getElementById('perPageSelectRenstra');
            if (perPageSelectRenstraElement) {
                perPageSelectRenstraElement.addEventListener('change', function() {
                    const url = new URL(window.location.href);
                    url.searchParams.set('per_page', this.value);
                    url.searchParams.set('tab', 'renstra'); // Ensure tab parameter is correct
                    url.searchParams.delete('page'); // Reset to first page
                    window.location.href = url.toString();
                });
            }

            // Handle perPage change for Reviu Renstra
            document.getElementById('perPageSelectReviuRenstra').addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.set('tab', 'reviu-renstra'); // Ensure tab parameter is correct
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            });

            // Handle perPage change for Reviu Target Renstra
            document.getElementById('perPageSelectReviuTarget').addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.set('tab', 'reviu-target'); // Ensure tab parameter is correct
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            });
        });

        // Use global loading functions from master.blade.php

            // Enhanced global loading functions with better z-index management and tab preservation
            window.showGlobalLoadingEnhanced = function(message = 'Memproses...') {
                // Preserve tab state before showing loading
                const activeTab = document.querySelector('.tab-btn.active');
                if (activeTab) {
                    localStorage.setItem('activeRenstraTab', activeTab.getAttribute('data-tab'));
                }
                
                // Hide any existing modals first
                closeModal();
                
                // Use global loading function from master.blade.php
                showLoading(message);
            };

            window.hideGlobalLoadingEnhanced = function() {
                // Use global loading function from master.blade.php
                hideLoading();
                
                // Restore tab state after hiding loading
                setTimeout(() => {
                    const savedTab = localStorage.getItem('activeRenstraTab');
                    if (savedTab) {
                        const activeTabBtn = document.querySelector(`.tab-btn[data-tab="${savedTab}"]`);
                        if (activeTabBtn && !activeTabBtn.classList.contains('active')) {
                            activeTabBtn.click();
                        }
                    }
                }, 100);
            };

        // Hide loading overlay when page loads (in case of redirect back)
        window.addEventListener('load', function() {
            hideLoading();
        });

        // Hide loading overlay on page visibility change (when user comes back to tab)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                hideLoading();
            }
        });
    </script>
@endpush
