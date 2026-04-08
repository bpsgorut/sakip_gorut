@extends('components.master')

@section('title', 'SKP')

@section('content')
    @include('components.breadcrumbs')

    <div class="container mx-auto py-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sasaran Kinerja Pegawai (SKP)</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola dokumen SKP bulanan dan tahunan untuk pencapaian target kinerja optimal</p>
            </div>
            {{-- Hapus button "Buat SKP Baru" --}}
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
                        placeholder="Cari SKP berdasarkan nama atau tahun...">
                                </div>

                                <div class="flex space-x-2">
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
                            
        <!-- Main Card Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <!-- SKP Cards Container -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="skpContainer">
                    @forelse($daftar_kegiatan as $kegiatan)
                        @php
                            // Hitung progress berdasarkan dokumen yang sudah diupload dari database
                            $totalDokumen = 13; // 12 bulanan + 1 tahunan
                            $buktiDukung = $kegiatan->buktiDukung ?? collect();
                            $dokumenBulanan = $buktiDukung->filter(function($item) {
                                return str_contains(strtolower($item->jenis ?? ''), 'bulanan');
                            })->count();
                            $dokumenTahunan = $buktiDukung->filter(function($item) {
                                return str_contains(strtolower($item->jenis ?? ''), 'tahunan');
                            })->count();
                            $dokumenTerisi = $dokumenBulanan + $dokumenTahunan;
                            $progress = $totalDokumen > 0 ? ($dokumenTerisi / $totalDokumen) * 100 : 0;
                            
                            // Uniform red-purple color scheme untuk semua card
                            $cardStyle = [
                                'bg' => 'from-red-500 via-purple-500 to-purple-600',
                                'accent' => 'red',
                                'icon_bg' => 'bg-red-100',
                                'icon_color' => 'text-red-600'
                            ];
                            
                            // Background images yang akan digunakan secara bergantian
                            $backgroundImages = ['bg1.jpg', 'bg2.jpg', 'bg3.jpg', 'bg4.jpg', 'bg5.jpg'];
                            $bgImage = $backgroundImages[$loop->index % count($backgroundImages)];
                        @endphp

                        <div class="skp-card bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden hover:shadow-2xl hover:border-gray-200 transition-all duration-500 group cursor-pointer transform hover:-translate-y-2"
                            data-year="{{ $kegiatan->tahun_berjalan }}"
                            onclick="window.location.href='{{ route('skp.detail.unggah', $kegiatan->id) }}'">

                            <!-- Decorative Header dengan Background Image -->
                            <div class="relative h-40 overflow-hidden">
                                <!-- Background Image -->
                                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('img/' . $bgImage) }}');"></div>
                                
                                <!-- Red-Purple Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-br {{ $cardStyle['bg'] }} opacity-85"></div>
                                
                                <!-- Subtle Pattern Overlay -->
                                <div class="absolute inset-0 opacity-20">
                                    <div class="absolute inset-0" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 20px 20px;"></div>
                                </div>
                                
                                <!-- Floating Elements -->
                                <div class="absolute top-4 right-4 w-8 h-8 bg-white bg-opacity-20 rounded-full animate-pulse"></div>
                                <div class="absolute bottom-8 right-8 w-4 h-4 bg-white bg-opacity-30 rounded-full animate-bounce"></div>
                                
                                <!-- Title Section -->
                                <div class="absolute bottom-6 left-6 right-6">
                                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl p-4">
                                        <h3 class="text-xl font-bold text-white mb-1">SKP {{ $kegiatan->tahun_berjalan }}</h3>
                                        <p class="text-sm text-white opacity-90 line-clamp-2">{{ $kegiatan->nama_kegiatan }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Content -->
                            <div class="p-6">
                                <!-- Progress Bar dengan Visual Enhancement -->
                                <div class="mb-6">
                                    <div class="flex justify-between text-sm text-gray-600 mb-3">
                                        <span class="flex items-center">
                                            <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                            Progress Dokumen
                                        </span>
                                        <span class="font-semibold">{{ $dokumenTerisi }}/13</span>
                                    </div>
                                    <div class="relative">
                                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                            <div class="bg-gradient-to-r {{ $cardStyle['bg'] }} h-3 rounded-full transition-all duration-700 ease-out relative"
                                                style="width: {{ $progress }}%">
                                                <div class="absolute inset-0 bg-white opacity-30 animate-pulse"></div>
                                            </div>
                                        </div>
                                        <div class="absolute -top-1 bg-white rounded-full w-5 h-5 border-2 border-red-500 transition-all duration-700"
                                             style="left: calc({{ $progress }}% - 10px)"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                                        <span>Mulai</span>
                                        <span class="font-medium">{{ round($progress) }}% Selesai</span>
                                        <span>Target</span>
                                    </div>
                                </div>

                                <!-- Status and Info -->
                                <div class="mb-4">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            @if($progress >= 100) bg-green-100 text-green-700
                                            @elseif($progress >= 50) bg-yellow-100 text-yellow-700
                                            @else bg-red-100 text-red-700 @endif">
                                            @if($progress >= 100) Lengkap
                                            @elseif($progress >= 50) Dalam Proses
                                            @else Belum Lengkap @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm mt-2">
                                        <span class="text-gray-600">Periode:</span>
                                        <span class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($kegiatan->tanggal_berakhir)->format('d M Y') }}</span>
                                    </div>
                                </div>

                                <!-- Action Section -->
                                <div class="flex space-x-3">
                                    <button class="flex-1 bg-red-700 hover:shadow-lg text-white py-3 px-4 rounded-xl text-sm font-semibold transition-all duration-300 flex items-center justify-center group-hover:scale-105 transform">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                        </svg>
                                        Kelola Dokumen
                                    </button>
                                </div>
                            </div>

                            <!-- Hover Effect Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        </div>

                    @empty
                        <!-- Empty State -->
                        <div class="col-span-full">
                            <div class="text-center py-12">
                                <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada SKP</h3>
                                <p class="text-gray-500 mb-6">Mulai dengan membuat SKP pertama untuk tahun berjalan</p>
                                {{-- Button to create first SKP (removed) --}}
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination Section -->
                <div class="mt-8">
                    @php
                        $currentPage = $daftar_kegiatan->currentPage();
                        $totalItems = $daftar_kegiatan->total();
                        $perPage = $daftar_kegiatan->perPage();
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
                                    <a href="{{ $daftar_kegiatan->appends(['tab' => request('tab', 'skp')])->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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
                                                <a href="{{ $daftar_kegiatan->appends(['tab' => request('tab', 'skp')])->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
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
                                    <a href="{{ $daftar_kegiatan->appends(['tab' => request('tab', 'skp')])->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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

    {{-- Hapus Modal Tambah Kegiatan --}}

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const filterStatus = document.getElementById('filterStatus');
            const filterTahun = document.getElementById('filterTahun');
            const applyFilterBtn = document.getElementById('applyFilter');
            const skpCards = document.querySelectorAll('.skp-card');

            function filterCards() {
                const searchTerm = searchInput.value.toLowerCase();
                const tahunFilter = filterTahun.value;

                skpCards.forEach(card => {
                    const year = card.dataset.year;
                    const cardText = card.textContent.toLowerCase();

                    const matchesSearch = cardText.includes(searchTerm);
                    const matchesTahun = !tahunFilter || year === tahunFilter;

                    if (matchesSearch && matchesTahun) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterCards);
            filterTahun.addEventListener('change', filterCards);
            applyFilterBtn.addEventListener('click', filterCards);

            // Auto-update tanggal based on tahun selection
            const tahunSelect = document.getElementById('tahun_berjalan');
            const tanggalMulai = document.getElementById('tanggal_mulai');
            const tanggalBerakhir = document.getElementById('tanggal_berakhir');

            tahunSelect?.addEventListener('change', function() {
                const selectedYear = this.value;
                tanggalMulai.value = `${selectedYear}-01-01`;
                tanggalBerakhir.value = `${selectedYear}-12-31`;
            });
        });
    </script>
@endsection