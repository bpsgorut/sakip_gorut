@extends('components.master')

@section('title', 'Manajemen RKT')

@section('content')
    @include('components.breadcrumbs')

    <div class="container mx-auto py-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manajemen RKT</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola dokumen rencana kerja tahunan untuk pencapaian kinerja optimal
                </p>
            </div>
            @if($isSuperAdmin)
            <div class="justify-end">
                <button id="btnTambahKegiatan"
                    class="group flex items-center bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 transition-transform group-hover:rotate-90"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Kegiatan
                </button>
            </div>
            @endif
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <!-- Search and Filter Section -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    <h2 class="text-lg font-semibold text-gray-900">Kelola Kegiatan</h2>
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Cari kegiatan..."
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 55%;">Kegiatan</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 10%;">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 15%;">Kelengkapan</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 10%;">Detail</th>
                            @if($isSuperAdmin)
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 10%;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($daftar_kegiatan as $kegiatan)
                            <tr class="hover:bg-gray-50 transition-colors" data-year="{{ $kegiatan->tahun_berjalan }}"
                                data-status="{{ $kegiatan->status }}"
                                data-search="{{ strtolower($kegiatan->nama_kegiatan) }} {{ $kegiatan->tahun_berjalan }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <div class="text-sm font-semibold text-gray-900 mb-1">
                                                {{ $kegiatan->nama_kegiatan }} {{ $kegiatan->tahun_berjalan }}
                                            </div>
                                            <div class="text-xs text-gray-500 space-y-1">
                                                <div class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    {{ $kegiatan->tanggal_mulai ? date('d M Y', strtotime($kegiatan->tanggal_mulai)) : date('1 Jan Y') }}
                                                    -
                                                    {{ $kegiatan->tanggal_berakhir ? date('d M Y', strtotime($kegiatan->tanggal_berakhir)) : date('31 Dec Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $kegiatan->status_class }}">
                                        @if ($kegiatan->status == 'Lengkap')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                        {{ $kegiatan->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($kegiatan->kelengkapan)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Lengkap
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            Belum Lengkap
                                        </span>
                                    @endif
                                    <div class="text-xs text-gray-500 mt-1">{{ $kegiatan->keterangan }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('detail', ['id' => $kegiatan->id, 'year' => $kegiatan->tahun_berjalan]) }}"
                                        class="inline-flex items-center justify-center p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                        title="Lihat Detail RKT">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                                @if($isSuperAdmin)
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button type="button" class="text-blue-500 hover:text-blue-700 edit-kegiatan-btn"
                                            title="Edit Tanggal" data-id="{{ $kegiatan->id }}"
                                            data-url="{{ route('kegiatan.update', $kegiatan->id) }}"
                                            data-start-date="{{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('Y-m-d') }}"
                                            data-end-date="{{ \Carbon\Carbon::parse($kegiatan->tanggal_berakhir)->format('Y-m-d') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z">
                                                </path>
                                            </svg>
                                        </button>
                                        <form id="deleteForm{{ $kegiatan->id }}" action="{{ route('kegiatan.destroy', $kegiatan->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-500 hover:text-red-700 delete-kegiatan-btn" title="Hapus" data-kegiatan-id="{{ $kegiatan->id }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum ada kegiatan RKT</h3>
                                        <p class="text-gray-500 mb-4 max-w-md text-center">Mulai dengan menambahkan
                                            kegiatan Rencana Kerja Tahunan untuk tahun {{ date('Y') }}</p>
                                        @if($isSuperAdmin)
                                        <button id="btnTambahKegiatan2"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Tambah Kegiatan Pertama
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if (isset($daftar_kegiatan) && $daftar_kegiatan->total() > 0)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        @php
                            $currentPage = max(1, $daftar_kegiatan->currentPage());
                            $totalItems = $daftar_kegiatan->total();
                            $perPage = $daftar_kegiatan->perPage();
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
                                <a href="{{ $daftar_kegiatan->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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
                                            <a href="{{ $daftar_kegiatan->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
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
                                <a href="{{ $daftar_kegiatan->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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
            @endif
        </div>
    </div>
    </div>
    </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>

    <!-- Modal Tambah Kegiatan (Initially Hidden) -->
    <div id="modalTambahKegiatan"
        class="fixed inset-0 z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="fixed inset-0 bg-black opacity-50" id="modalOverlay"></div>
        <div
            class="bg-white rounded-lg shadow-lg w-full max-w-md relative z-10 scale-95 transition-transform duration-300">
            <div class="p-5">
                <div class="relative border-b pb-3 mb-4">
                    <div class="flex justify-center">
                        <h3 class="text-lg font-bold text-red-600">Tambah Kegiatan RKT</h3>
                    </div>
                    <button id="btnCloseModal"
                        class="absolute right-0 top-0 text-gray-500 hover:text-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('kegiatan.store') }}" method="POST">
                    @csrf
                    <!-- HIDDEN INPUTS -->
                    <input type="hidden" name="jenis_kegiatan" id="jenis_kegiatan" value="rkt">
                    <input type="hidden" name="renstra_id" id="renstra_id" value="{{ $activeRenstra->id ?? '' }}">

                    <!-- SUB KOMPONEN ID -->
                    @php
                        // Cari sub komponen Manajemen Renstra dengan berbagai cara
                        $manajemenRKTId = null;

                        if (isset($manajemenRKTSubKomponen) && $manajemenRKTSubKomponen) {
                            $manajemenRKTId = $manajemenRKTSubKomponen->id;
                        } else {
                            // Fallback: cari berdasarkan sub_komponen field
                            $found = $subKomponenList->first(function ($item) {
                                return stripos($item->sub_komponen, 'Manajemen RKT') !== false;
                            });
                            if ($found) {
                                $manajemenRKTId = $found->id;
                            }
                        }
                    @endphp

                    <input type="hidden" name="sub_komponen_id" value="{{ $manajemenRKTId }}">

                    <!-- Nama Kegiatan (Read Only) -->
                    <div class="mb-4">
                        <label for="nama_kegiatan" class="block text-sm font-bold mb-1">Nama Kegiatan</label>
                        <input type="text" id="nama_kegiatan" name="nama_kegiatan" value="Rencana Kerja Tahunan"
                            class="w-full p-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                    </div>

                    <div class="mb-4">
                        <label for="tahun_berjalan" class="block text-sm font-bold mb-1">Tahun</label>
                        <select id="tahun_berjalan" name="tahun_berjalan"
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                            @php
                                $currentYear = date('Y');
                                $existingYears = $daftar_kegiatan->pluck('tahun_berjalan')->toArray();
                                for ($year = $currentYear + 2; $year >= $currentYear - 2; $year--) {
                                    if (!in_array($year, $existingYears)) {
                                        $selected = $year == $currentYear ? 'selected' : '';
                                        echo "<option value=\"$year\" $selected>$year</option>";
                                    }
                                }
                            @endphp
                        </select>
                        @if(count($existingYears) > 0)
                            <p class="text-xs text-gray-500 mt-1">
                                Tahun yang sudah ada: {{ implode(', ', array_unique($existingYears)) }}
                            </p>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-bold mb-1">Tanggal Mulai</label>
                            <div class="relative">
                                <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                                    class="w-full p-2 border text-sm text-gray-700 border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
                                    required>
                            </div>
                        </div>
                        <div>
                            <label for="tanggal_berakhir" class="block text-sm font-bold mb-1">Tanggal Selesai</label>
                            <div class="relative">
                                <input type="date" id="tanggal_berakhir" name="tanggal_berakhir"
                                    class="w-full p-2 border text-sm text-gray-700 border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
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
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <form id="formEditKegiatan" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="edit_tanggal_mulai" class="block text-sm font-bold mb-1">Tanggal Mulai</label>
                            <input type="date" id="edit_tanggal_mulai" name="tanggal_mulai"
                                class="w-full p-2 border border-gray-300 rounded-md" required>
                        </div>
                        <div>
                            <label for="edit_tanggal_berakhir" class="block text-sm font-bold mb-1">Tanggal
                                Selesai</label>
                            <input type="date" id="edit_tanggal_berakhir" name="tanggal_berakhir"
                                class="w-full p-2 border border-gray-300 rounded-md" required>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="btnCancelEdit"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm transition-colors">Batal</button>
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm transition-colors">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Modal Tambah Kegiatan
            const modal = document.getElementById('modalTambahKegiatan');
            const btnTambahKegiatan = document.getElementById('btnTambahKegiatan');
            const btnCloseModal = document.getElementById('btnCloseModal');
            const modalOverlay = document.getElementById('modalOverlay');

            function openModal() {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    modal.querySelector('.scale-95').classList.add('scale-100');
                    modal.querySelector('.scale-95').classList.remove('scale-95');
                }, 10);
            }

            function closeModal() {
                modal.classList.remove('opacity-100');
                modal.querySelector('.scale-100').classList.add('scale-95');
                modal.querySelector('.scale-100').classList.remove('scale-100');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            if (btnTambahKegiatan) {
                btnTambahKegiatan.addEventListener('click', openModal);
            }

            if (btnCloseModal) {
                btnCloseModal.addEventListener('click', closeModal);
            }

            if (modalOverlay) {
                modalOverlay.addEventListener('click', closeModal);
            }

            // Close modal with ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });

            // --- Modal Edit Tanggal ---
            const modalEdit = document.getElementById('modalEditKegiatan');
            const btnCloseEditModal = document.getElementById('btnCloseEditModal');
            const btnCancelEdit = document.getElementById('btnCancelEdit');
            const formEdit = document.getElementById('formEditKegiatan');
            const inputMulai = document.getElementById('edit_tanggal_mulai');
            const inputBerakhir = document.getElementById('edit_tanggal_berakhir');
            const modalEditOverlay = document.getElementById('modalEditOverlay');

            document.querySelectorAll('.edit-kegiatan-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const url = this.dataset.url;
                    const startDate = this.dataset.startDate;
                    const endDate = this.dataset.endDate;

                    formEdit.action = url;
                    inputMulai.value = startDate;
                    inputBerakhir.value = endDate;

                    modalEdit.classList.remove('hidden');
                });
            });

            function closeEditModal() {
                modalEdit.classList.add('hidden');
            }

            btnCloseEditModal.addEventListener('click', closeEditModal);
            btnCancelEdit.addEventListener('click', closeEditModal);
            modalEditOverlay.addEventListener('click', closeEditModal);

            // Close edit modal with ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modalEdit.classList.contains('hidden')) {
                    closeEditModal();
                }
            });

            // Handle delete kegiatan with global modal
            document.querySelectorAll('.delete-kegiatan-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const kegiatanId = this.dataset.kegiatanId;
                    
                    showModal('warning', 'Konfirmasi Hapus Kegiatan', 'Anda yakin ingin menghapus kegiatan ini? Folder terkait di Google Drive juga akan dipindahkan ke sampah.', {
                        confirmText: 'Ya, Hapus',
                        cancelText: 'Batal',
                        showCancel: true,
                        confirmCallback: function() {
                            // Show loading overlay
                            showLoading('Menghapus kegiatan dan folder terkait...');
                            
                            // Submit the delete form
                            document.getElementById('deleteForm' + kegiatanId).submit();
                        }
                    });
                });
            });

            // Filter functionality for the table
            const searchInput = document.getElementById('searchInput');
            const filterTahun = document.getElementById('filterTahun');
            const filterStatus = document.getElementById('filterStatus');
            const applyFilterBtn = document.getElementById('applyFilter');
            const tableRows = document.querySelectorAll('#dataTable tbody tr');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedYear = filterTahun.value;
                const selectedStatus = filterStatus.value;

                let visibleCount = 0;

                tableRows.forEach(row => {
                    if (row.classList.contains('no-results-message')) {
                        return;
                    }

                    const year = row.getAttribute('data-year');
                    const status = row.getAttribute('data-status');
                    const searchContent = row.getAttribute('data-search').toLowerCase();

                    let visible = true;

                    if (searchTerm && !searchContent.includes(searchTerm)) {
                        visible = false;
                    }

                    if (selectedYear && year !== selectedYear) {
                        visible = false;
                    }

                    if (selectedStatus && status !== selectedStatus) {
                        visible = false;
                    }

                    row.style.display = visible ? '' : 'none';

                    if (visible) {
                        visibleCount++;
                    }
                });

                // Check if no rows are visible
                const noResultsRow = document.getElementById('noResultsRow');

                if (visibleCount === 0) {
                    if (!noResultsRow) {
                        const tbody = document.querySelector('#dataTable tbody');
                        const tr = document.createElement('tr');
                        tr.id = 'noResultsRow';
                        tr.className = 'border-b no-results-message';
                        tr.innerHTML =
                            `<td colspan="5" class="py-6 text-center text-gray-500">Tidak ada data yang sesuai dengan filter</td>`;
                        tbody.appendChild(tr);
                    }
                } else if (noResultsRow) {
                    noResultsRow.remove();
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterTable);
            }

            if (filterTahun) {
                filterTahun.addEventListener('change', filterTable);
            }

            if (filterStatus) {
                filterStatus.addEventListener('change', filterTable);
            }

            if (applyFilterBtn) {
                applyFilterBtn.addEventListener('click', filterTable);
            }

            // Handle perPage change
            document.getElementById('perPageSelect').addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            });

            // Set tanggal default untuk form tambah kegiatan
            const tanggalMulai = document.getElementById('tanggal_mulai');
            const tanggalBerakhir = document.getElementById('tanggal_berakhir');

            if (tanggalMulai && tanggalBerakhir) {
                const today = new Date();
                const oneYearLater = new Date();
                oneYearLater.setFullYear(today.getFullYear() + 1);

                tanggalMulai.valueAsDate = today;
                tanggalBerakhir.valueAsDate = oneYearLater;
            }

            // Add CSS for animations
            const style = document.createElement('style');
            style.textContent = `
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            tr.border-b {
                animation: fadeIn 0.3s ease-out forwards;
            }
        `;
            document.head.appendChild(style);
        });
    </script>
@endpush