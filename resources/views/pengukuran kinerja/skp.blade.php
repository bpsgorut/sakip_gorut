@extends('components.master')

@section('title', 'Sasaran Kinerja Pegawai')

@section('content')
    @include('components.breadcrumbs')

    <div class="container mx-auto py-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Sasaran Kinerja Pegawai</h1>
                <p class="text-sm text-gray-600">Kelola dan pantau SKP pegawai tahun {{ date('Y') }}</p>
            </div>
            <div class="flex space-x-3 mt-4 md:mt-0">
                <button id="btnTambahKegiatan" class="flex items-center bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-colors shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Kegiatan
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <!-- Header with Search -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Kelola Kegiatan</h2>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="searchInput" placeholder="Cari kegiatan..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                </div>
            </div>

            <!-- Table -->
                        <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 50%;">Kegiatan</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 20%;">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 20%;">Persentase Kelengkapan</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 10%;">Detail</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($daftar_kegiatan ?? [] as $kegiatan)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <div class="text-sm font-semibold text-gray-900 mb-1">
                                                {{ $kegiatan->nama_kegiatan ?? 'Sasaran Kinerja Pegawai' }} {{ date('Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500 space-y-1">
                                                <div class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $kegiatan->tanggal_mulai ? date('d M Y', strtotime($kegiatan->tanggal_mulai)) : date('1 Jan Y') }} - {{ $kegiatan->tanggal_berakhir ? date('d M Y', strtotime($kegiatan->tanggal_berakhir)) : date('31 Dec Y') }}
                                                </div>
                                            </div>
                                        </div>
                                                </div>
                                            </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $kegiatan->status_class }}">
                                        @if($kegiatan->status_text === 'Lengkap')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                        {{ $kegiatan->status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 max-w-20">
                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $kegiatan->total_progress }}%"></div>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900 min-w-12">{{ $kegiatan->total_progress }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('skp.detail.kinerja', ['id' => $kegiatan->id ?? 1]) }}"
                                        class="inline-flex items-center justify-center p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                        title="Lihat Detail SKP">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <button type="button" class="text-blue-500 hover:text-blue-700 edit-kegiatan-btn" title="Edit Tanggal"
                                                        data-id="{{ $kegiatan->id }}"
                                                        data-url="{{ route('kegiatan.update', $kegiatan->id) }}"
                                                        data-start-date="{{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('Y-m-d') }}"
                                                        data-end-date="{{ \Carbon\Carbon::parse($kegiatan->tanggal_berakhir)->format('Y-m-d') }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z"></path></svg>
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
                                        </tr>
                                    @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum ada kegiatan SKP</h3>
                                        <p class="text-gray-500 mb-4 max-w-md text-center">Mulai dengan menambahkan kegiatan Sasaran Kinerja Pegawai untuk tahun {{ date('Y') }}</p>
                                        <button id="btnTambahKegiatan2" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            Tambah Kegiatan Pertama
                                        </button>
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
                        
                        <div class="flex items-center">
                            <span>Menampilkan</span>
                            <span class="font-semibold text-gray-900 mx-1">{{ $currentPage }}</span>
                            <span>dari</span>
                            <span class="font-semibold text-gray-900 mx-1">{{ $totalPages }}</span>
                            <span>halaman</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center">
                            <select id="perPageSelect" class="mr-2 border border-gray-300 rounded-md shadow-sm text-sm py-1.5 px-3">
                                @foreach([5, 10, 15, 25, 50] as $option)
                                    <option value="{{ $option }}" @if($perPage == $option) selected @endif>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Previous Page Link --}}
                        @if ($hasPrevPage)
                            <a href="{{ $daftar_kegiatan->appends(request()->except('page'))->previousPageUrl() }}" class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-100 transition-colors">
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
                                <a href="{{ $daftar_kegiatan->appends(request()->except('page'))->url(1) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 text-sm font-medium transition-colors">1</a>
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
                                    <a href="{{ $daftar_kegiatan->appends(request()->except('page'))->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 text-sm font-medium transition-colors">
                                        {{ $i }}
                                    </a>
                                @endif
                            @endfor

                            @if ($endPage < $totalPages)
                                @if ($endPage < $totalPages - 1)
                                    <span class="text-gray-500">...</span>
                                @endif
                                <a href="{{ $daftar_kegiatan->appends(request()->except('page'))->url($totalPages) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 text-sm font-medium transition-colors">{{ $totalPages }}</a>
                            @endif
                        </div>
                        
                        {{-- Next Page Link --}}
                        @if ($hasNextPage)
                            <a href="{{ $daftar_kegiatan->appends(request()->except('page'))->nextPageUrl() }}" class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-100 transition-colors">
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

    <!-- Modal Tambah Kegiatan -->
    <div id="modalTambahKegiatan"
        class="fixed inset-0 z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="fixed inset-0 bg-black opacity-50" id="modalOverlay"></div>
        <div
            class="bg-white rounded-lg shadow-lg w-full max-w-md relative z-10 scale-95 transition-transform duration-300">
            <div class="p-5">
                <div class="relative border-b pb-3 mb-4">
                    <div class="flex justify-center">
                        <h3 class="text-lg font-bold text-red-600">Tambah Kegiatan SKP</h3>
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
                    <input type="hidden" name="jenis_kegiatan" id="jenis_kegiatan" value="skp">
                    <input type="hidden" name="renstra_id" id="renstra_id" value="{{ $activeRenstra->id ?? '' }}">

                    <!-- SUB KOMPONEN ID -->
                    @php
                        // Cari sub komponen SKP dengan berbagai cara
                        $SkpId = null;

                        if (isset($skpSubKomponen) && $skpSubKomponen) {
                            $SkpId = $skpSubKomponen->id;
                        } else {
                            // Fallback: cari berdasarkan sub_komponen field
                            $found = $subKomponenList->first(function ($item) {
                                return stripos($item->sub_komponen, 'SKP') !== false ||
                                       stripos($item->sub_komponen, 'Sasaran Kinerja Pegawai') !== false;
                            });
                            if ($found) {
                                $SkpId = $found->id;
                            }
                        }
                    @endphp

                    <input type="hidden" name="sub_komponen_id" value="{{ $SkpId }}">

                    <!-- Nama Kegiatan (Read Only) -->
                    <div class="mb-4">
                        <label for="nama_kegiatan" class="block text-sm font-bold mb-1">Nama Kegiatan</label>
                        <input type="text" id="nama_kegiatan" name="nama_kegiatan" value="Sasaran Kinerja Pegawai"
                            class="w-full p-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="mb-4">
                        <label for="tanggal_mulai" class="block text-sm font-bold mb-1">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ date('Y-01-01') }}"
                            class="w-full p-2 border border-gray-300 rounded-md" required>
                    </div>

                    <!-- Tanggal Berakhir -->
                    <div class="mb-4">
                        <label for="tanggal_berakhir" class="block text-sm font-bold mb-1">Tanggal Berakhir</label>
                        <input type="date" id="tanggal_berakhir" name="tanggal_berakhir" value="{{ date('Y-12-31') }}"
                            class="w-full p-2 border border-gray-300 rounded-md" required>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3 mt-6">
                        <button type="button" id="btnCancelModal"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" id="btnSubmitModal"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Tambah Kegiatan
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



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal Tambah Kegiatan
            const modal = document.getElementById('modalTambahKegiatan');
            const btnTambahKegiatan = document.getElementById('btnTambahKegiatan');
            const btnTambahKegiatan2 = document.getElementById('btnTambahKegiatan2');
            const btnCloseModal = document.getElementById('btnCloseModal');
            const btnCancelModal = document.getElementById('btnCancelModal');
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

            if (btnTambahKegiatan2) {
                btnTambahKegiatan2.addEventListener('click', openModal);
            }

            if (btnCloseModal) {
                btnCloseModal.addEventListener('click', closeModal);
            }

            if (btnCancelModal) {
                btnCancelModal.addEventListener('click', closeModal);
            }

            if (modalOverlay) {
                modalOverlay.addEventListener('click', closeModal);
            }

            // Form submission handling
            const form = modal.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const submitBtn = document.getElementById('btnSubmitModal');
                    const originalText = submitBtn.innerHTML;
                    
                    // Show loading state
                    submitBtn.innerHTML = '<svg class="animate-spin w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Menyimpan...';
                    submitBtn.disabled = true;

                    // Submit to controller
                    fetch('{{ route("kegiatan.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeModal();
                            showNotification('Kegiatan SKP berhasil ditambahkan!', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Gagal menambahkan kegiatan: ' + error.message, 'error');
                    })
                    .finally(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
                });
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

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');

                tableRows.forEach(row => {
                const nameCell = row.querySelector('td:first-child');
                if (nameCell) {
                    const text = nameCell.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
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

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            const bgClass = type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200';
            const iconPath = type === 'success' 
                ? 'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z'
                : 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z';
            
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${bgClass}`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="${iconPath}" clip-rule="evenodd"></path>
                    </svg>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

            // Handle perPage change
            document.getElementById('perPageSelect').addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            });
    </script>
@endsection
