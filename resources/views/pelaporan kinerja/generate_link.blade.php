@extends('components.master')

@section('title', 'Generate Link Google Drive')

@section('content')
    @include('components.breadcrumbs')

    <div class="container mx-auto py-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Generate Link Google Drive</h1>
                <p class="text-sm text-gray-500 mt-1">Akses cepat ke folder Google Drive untuk semua kegiatan yang sudah
                    terintegrasi</p>
            </div>
            <div class="flex items-center space-x-3 mt-4 md:mt-0">
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $daftar_kegiatan->total() }} Folder Tersedia
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('generate.link') }}" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label for="filter_type" class="block text-sm font-medium text-gray-700 mb-2">Filter
                            Berdasarkan</label>
                        <select id="filter_type" name="filter_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="tahun" {{ !$selectedPeriode ? 'selected' : '' }}>Tahun</option>
                            <option value="periode" {{ $selectedPeriode ? 'selected' : '' }}>Periode Renstra</option>
                        </select>
                    </div>

                    <div id="tahun_filter" class="flex-1 min-w-[200px]"
                        style="{{ $selectedPeriode ? 'display: none;' : '' }}">
                        <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <select name="tahun"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="" {{ is_null($selectedYear) ? 'selected' : '' }}>Pilih Tahun (Semua)
                            </option>
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    {{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="periode_filter" class="flex-1 min-w-[200px]"
                        style="{{ !$selectedPeriode ? 'display: none;' : '' }}">
                        <label for="periode" class="block text-sm font-medium text-gray-700 mb-2">Periode Renstra</label>
                        <select name="periode"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Pilih Periode</option>
                            @foreach ($availablePeriodes as $periode)
                                <option value="{{ $periode['value'] }}"
                                    {{ $selectedPeriode == $periode['value'] ? 'selected' : '' }}>{{ $periode['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                            <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z">
                                </path>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('generate.link') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm transition-colors">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <!-- Header Info -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">
                    @if ($selectedPeriode)
                        Folder Kegiatan Periode {{ str_replace('-', ' - ', $selectedPeriode) }}
                    @else
                        Folder Kegiatan Tahun {{ $selectedYear }}
                    @endif
                </h3>
                <p class="text-sm text-gray-600 mt-1">Klik link untuk membuka folder Google Drive langsung di browser</p>
            </div>

            <!-- Table Content -->
            <div class="overflow-x-auto">
                @if ($daftar_kegiatan->count() > 0)
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="py-4 px-6 text-left font-semibold text-gray-900">No</th>
                                <th class="py-4 px-6 text-left font-semibold text-gray-900">Nama Kegiatan</th>
                                <th class="py-4 px-6 text-left font-semibold text-gray-900">Sub Komponen</th>
                                <th class="py-4 px-6 text-center font-semibold text-gray-900">Tahun</th>
                                <th class="py-4 px-6 text-center font-semibold text-gray-900">Link Google Drive</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daftar_kegiatan as $index => $kegiatan)
                                <!-- Main Row -->
                                <tr class="border-b hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-6 text-gray-900 font-medium">
                                        {{ $daftar_kegiatan->firstItem() + $index }}</td>
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-gray-900">{{ $kegiatan->nama_kegiatan }}</div>
                                        @if ($kegiatan->keterangan)
                                            <div class="text-sm text-gray-500 mt-1">
                                                {{ Str::limit($kegiatan->keterangan, 60) }}</div>
                                        @endif
                                        @if ($kegiatan->has_triwulan_subfolders && count($kegiatan->triwulan_folders) > 0)
                                            <div class="text-xs text-gray-600 mt-1 font-medium">
                                                {{ count($kegiatan->triwulan_folders) }} Folder Triwulan
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $kegiatan->sub_komponen->sub_komponen ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="font-medium text-gray-900">{{ $kegiatan->tahun_berjalan }}</span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="https://drive.google.com/drive/folders/{{ $kegiatan->folder_id }}"
                                                target="_blank"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors text-sm font-medium"
                                                title="Buka Folder Google Drive">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M14 4h6m0 0v6m0-6L10 14">
                                                    </path>
                                                </svg>
                                                Buka Folder
                                            </a>
                                            <button
                                                onclick="copyToClipboard('https://drive.google.com/drive/folders/{{ $kegiatan->folder_id }}')"
                                                class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors text-sm font-medium"
                                                title="Salin Link">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                Salin
                                            </button>
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1 font-mono">
                                            {{ Str::limit($kegiatan->folder_id, 20) }}...
                                        </div>
                                    </td>
                                </tr>

                                <!-- Triwulan Sub-rows -->
                                @if ($kegiatan->has_triwulan_subfolders && count($kegiatan->triwulan_folders) > 0)
                                    @foreach ($kegiatan->triwulan_folders as $triwulan)
                                        <tr
                                            class="border-b border-gray-100 bg-slate-50 hover:bg-slate-100 transition-colors">
                                            <td class="py-3 px-6 text-gray-500 text-sm"></td>
                                            <td class="py-3 px-6">
                                                <div class="flex items-center">
                                                    <div class="w-4 h-4 mr-2 text-gray-400">
                                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="text-sm font-medium text-gray-700">{{ $triwulan['nama'] }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 px-6">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                                    Sub Folder
                                                </span>
                                            </td>
                                            <td class="py-3 px-6 text-center">
                                                <span class="text-sm text-gray-500">{{ $kegiatan->tahun_berjalan }}</span>
                                            </td>
                                            <td class="py-3 px-6 text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <a href="https://drive.google.com/drive/folders/{{ $triwulan['folder_id'] }}"
                                                        target="_blank"
                                                        class="inline-flex items-center px-2 py-1 bg-amber-100 text-amber-700 rounded-md hover:bg-amber-200 transition-colors text-xs font-medium"
                                                        title="Buka Folder Triwulan">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M14 4h6m0 0v6m0-6L10 14">
                                                            </path>
                                                        </svg>
                                                        Buka
                                                    </a>
                                                    <button
                                                        onclick="copyToClipboard('https://drive.google.com/drive/folders/{{ $triwulan['folder_id'] }}')"
                                                        class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 transition-colors text-xs font-medium"
                                                        title="Salin Link Triwulan">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                        Salin
                                                    </button>
                                                </div>
                                                <div class="text-xs text-gray-400 mt-1 font-mono">
                                                    {{ Str::limit($triwulan['folder_id'], 15) }}...
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="py-16 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <h3 class="text-lg font-medium mb-2">Tidak ada folder Google Drive</h3>
                        <p class="text-sm mb-4">
                            @if ($selectedPeriode)
                                Belum ada kegiatan dengan folder Google Drive untuk periode
                                {{ str_replace('-', ' - ', $selectedPeriode) }}
                            @else
                                Belum ada kegiatan dengan folder Google Drive untuk tahun {{ $selectedYear }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-400">Folder Google Drive dibuat otomatis saat kegiatan pertama kali
                            diupload dokumen</p>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-between">
                <div class="flex items-center space-x-2 text-sm text-gray-600 mb-4 sm:mb-0">
                    @if ($daftar_kegiatan->total() > 0) {{-- Check if there's any data at all --}}
                        <span>Menampilkan {{ $daftar_kegiatan->firstItem() }} - {{ $daftar_kegiatan->lastItem() }} dari
                            {{ $daftar_kegiatan->total() }} hasil</span>
                    @else
                        <span>Tidak ada hasil yang ditemukan</span>
                    @endif
                </div>
                <div class="flex items-center space-x-2">
                    <div>
                        <select name="per_page" id="perPageSelect"
                            class="items-center mr-2 px-3 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                            @foreach ([10, 25, 50, 100] as $option)
                                <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                                    {{ $option }} </option>
                            @endforeach
                        </select>
                    </div>
                    
                    @if ($daftar_kegiatan->total() > 0) {{-- Check if there's any data at all --}}
                    <div class="flex items-center space-x-2">
                        {{-- Previous Page Link --}}
                        @if ($daftar_kegiatan->currentPage() > 1)
                            <a href="{{ $daftar_kegiatan->previousPageUrl() }}" class="flex items-center justify-center w-8 h-8 rounded-full text-gray-600 border border-gray-300 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </a>
                        @else
                            <span class="flex items-center justify-center w-8 h-8 rounded-full text-gray-400 border border-gray-300 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </span>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                            $currentPage = $daftar_kegiatan->currentPage();
                            $lastPage = $daftar_kegiatan->lastPage();
                            // Ensure at least one page number is always shown even if $lastPage is 0
                            $displayPages = range(1, max(1, $lastPage));
                            
                            // Logic to show ellipsis for pages not directly adjacent to current
                            $startPage = max(1, $currentPage - 1);
                            $endPage = min($lastPage, $currentPage + 1);

                            if ($lastPage > 5) { // Adjust '5' as needed for number of visible pages
                                if ($currentPage <= 3) {
                                    $endPage = 5;
                                } elseif ($currentPage >= $lastPage - 2) {
                                    $startPage = $lastPage - 4;
                                } else {
                                    $startPage = $currentPage - 2;
                                    $endPage = $currentPage + 2;
                                }
                            }
                            $displayPages = range($startPage, $endPage);
                        @endphp

                        @if ($startPage > 1)
                            <a href="{{ $daftar_kegiatan->url(1) }}" class="flex items-center justify-center w-8 h-8 rounded-full text-red-600 border border-red-600 hover:bg-red-50 transition-colors">1</a>
                            @if ($startPage > 2)
                                <span class="flex items-center justify-center w-8 h-8 rounded-full text-gray-400">...</span>
                            @endif
                        @endif

                        @foreach ($displayPages as $page)
                            @if ($page == $currentPage)
                                <span aria-current="page" class="flex items-center justify-center w-8 h-8 rounded-full bg-red-600 text-white font-bold">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $daftar_kegiatan->url($page) }}" class="flex items-center justify-center w-8 h-8 rounded-full text-red-600 border border-red-600 hover:bg-red-50 transition-colors">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                                <span class="flex items-center justify-center w-8 h-8 rounded-full text-gray-400">...</span>
                            @endif
                            <a href="{{ $daftar_kegiatan->url($lastPage) }}" class="flex items-center justify-center w-8 h-8 rounded-full text-red-600 border border-red-600 hover:bg-red-50 transition-colors">{{ $lastPage }}</a>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($daftar_kegiatan->hasMorePages())
                            <a href="{{ $daftar_kegiatan->nextPageUrl() }}" class="flex items-center justify-center w-8 h-8 rounded-full text-gray-600 border border-gray-300 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @else
                            <span class="flex items-center justify-center w-8 h-8 rounded-full text-gray-400 border border-gray-300 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast"
        class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span id="toast-message">Link berhasil disalin!</span>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Filter type toggle
            const filterType = document.getElementById('filter_type');
            const tahunFilter = document.getElementById('tahun_filter');
            const periodeFilter = document.getElementById('periode_filter');

            filterType.addEventListener('change', function() {
                if (this.value === 'tahun') {
                    tahunFilter.style.display = 'block';
                    periodeFilter.style.display = 'none';
                } else {
                    tahunFilter.style.display = 'none';
                    periodeFilter.style.display = 'block';
                }
            });

            // Copy to clipboard function
            window.copyToClipboard = function(text) {
                try {
                    // Use Clipboard API
                    navigator.clipboard.writeText(text).then(function() {
                        showToast('Link berhasil disalin ke clipboard!');
                    }).catch(function(err) {
                        console.error('Error using Clipboard API: ', err);
                        // Fallback method
                        fallbackCopyTextToClipboard(text);
                    });
                } catch (err) {
                    console.error('Clipboard API not supported: ', err);
                    // Fallback method
                    fallbackCopyTextToClipboard(text);
                }
            }

            // Fallback copy method
            function fallbackCopyTextToClipboard(text) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                
                // Avoid scrolling to bottom
                textArea.style.top = '0';
                textArea.style.left = '0';
                textArea.style.position = 'fixed';

                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();

                try {
                    const successful = document.execCommand('copy');
                    const msg = successful ? 'Link berhasil disalin!' : 'Gagal menyalin link';
                    showToast(msg);
                } catch (err) {
                    console.error('Fallback copy failed', err);
                    showToast('Gagal menyalin link');
                }

                document.body.removeChild(textArea);
            }

            // Show toast notification
            function showToast(message) {
                const toast = document.getElementById('toast');
                const toastMessage = document.getElementById('toast-message');

                toastMessage.textContent = message;
                toast.classList.remove('translate-x-full');

                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                }, 3000);
            }

            // Handle perPage change
            document.getElementById('perPageSelect').addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            });
        });
    </script>
@endpush