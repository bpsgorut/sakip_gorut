@extends('components.master')

@section('title', 'Detail Reward & Punishment')

@section('content')
    @include('components.breadcrumbs')

    <div class="container mx-auto py-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Reward & Punishment</h1>
                <p class="text-sm text-gray-500 mt-1">Rincian dokumen reward dan punishment pegawai</p>
            </div>
        </div>

        <!-- Header Card with Background -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="relative">
                <!-- Background with Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-purple-700 opacity-70"></div>
                <img class="w-full h-40 object-cover" src="{{ asset('img/bg3.jpg') }}" alt="">
                
                <!-- Content Overlay -->
                <div class="absolute inset-0 flex items-center justify-between p-6">
                    <div class="text-white flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold mb-1">{{ $kegiatan->nama_kegiatan }}</h1>
                                <p class="text-white/90 text-sm">Detail Reward Punishment • Tahun {{ $kegiatan->tahun_berjalan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Section with Stats -->
            <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-4">
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-1">
                <div class="flex flex-wrap space-x-1">
                    <button onclick="navigateToSection('penetapan')" 
                        id="nav-penetapan"
                        class="nav-pill flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Penetapan Mekanisme
                    </button>
                    <button onclick="navigateToSection('triwulan-1')" 
                        id="nav-triwulan-1"
                        class="nav-pill flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Triwulan I
                    </button>
                    <button onclick="navigateToSection('triwulan-2')" 
                        id="nav-triwulan-2"
                        class="nav-pill flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Triwulan II
                    </button>
                    <button onclick="navigateToSection('triwulan-3')" 
                        id="nav-triwulan-3"
                        class="nav-pill flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Triwulan III
                    </button>
                    <button onclick="navigateToSection('triwulan-4')" 
                        id="nav-triwulan-4"
                        class="nav-pill flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Triwulan IV
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="space-y-6">
            
            <!-- Penetapan Mekanisme Section -->
            <div id="section-penetapan" class="content-section">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-900">Penetapan Mekanisme</h2>
                                <p class="text-sm text-slate-600 mt-1">Dokumen penetapan mekanisme reward punishment tahun {{ $kegiatan->tahun_berjalan }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                @php
                                    $penetapanDoc = \App\Models\Bukti_Dukung::where('kegiatan_id', $kegiatan->id)
                                        ->where('jenis', 'penetapan_mekanisme')
                                        ->first();
                                    $penetapanExists = $penetapanDoc ? true : false;
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $penetapanExists ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-800' }}">
                                    {{ $penetapanExists ? '1/1 dokumen' : '0/1 dokumen' }}
                                </span>
                            </div>
                        </div>

                        @if($penetapanDoc)
                            <!-- Document Already Uploaded -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Dokumen yang Sudah Diupload</h3>
                                <div class="space-y-4">
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <!-- Document Info -->
                                            <div class="flex items-center flex-1">
                                                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center mr-3">
                                                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900">{{ $penetapanDoc->nama_dokumen ?? 'Dokumen Penetapan Mekanisme' }}</p>
                                                    <p class="text-xs text-gray-500">Diupload: {{ $penetapanDoc->created_at ? $penetapanDoc->created_at->format('d M Y H:i') : 'N/A' }}</p>
                                                </div>
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div class="flex gap-2 ml-4">
                                                @if($penetapanDoc->webViewLink)
                                                    <a href="{{ $penetapanDoc->webViewLink }}" target="_blank" class="px-5 py-2 bg-slate-600 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                                                        Lihat
                                                    </a>
                                                @endif
                                                @php
                                                    // Apply same logic as detail.blade.php:
                                                    // 1. If activity expired: only Super Admin can edit/delete
                                                    // 2. If activity active: only Super Admin and Admin can edit/delete
                                                    // 3. Ketua Tim and Anggota Tim can only view
                                                    $canEditDelete = ($activityExpired && $isSuperAdmin) || (!$activityExpired && ($isSuperAdmin || $isAdmin));
                                                @endphp
                                                @if($canEditDelete)
                                                    <button onclick="showEditModalPenetapan({{ $penetapanDoc->id }}, '{{ addslashes($penetapanDoc->nama_dokumen ?? 'Dokumen Penetapan Mekanisme') }}')" class="px-5 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                                                        Edit
                                                    </button>
                                                    <button onclick="deleteBuktiDukung({{ $penetapanDoc->id }}, '{{ addslashes($penetapanDoc->nama_dokumen ?? 'Dokumen Penetapan Mekanisme') }}')" class="px-5 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
                                                        Hapus
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            @php
                                // Apply same logic as detail.blade.php:
                                // 1. If activity expired: only Super Admin can upload
                                // 2. If activity active: only Super Admin and Admin can upload
                                // 3. Ketua Tim and Anggota Tim can only view
                                $canUploadNew = ($activityExpired && $isSuperAdmin) || (!$activityExpired && ($isSuperAdmin || $isAdmin));
                            @endphp
                            
                            @if($canUploadNew)
                                <!-- Upload Form -->
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Upload Dokumen Penetapan Mekanisme</h3>
                                    
                                    <form action="{{ route('bukti.dukung.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->id }}">
                                        <input type="hidden" name="renstra_id" value="{{ $kegiatan->renstra_id }}">
                                        <input type="hidden" name="jenis_dokumen" value="penetapan_mekanisme">
                                        <input type="hidden" name="current_tab" value="penetapan">

                                        <div>
                                            <input type="file" name="dokumen" id="penetapan-file" accept=".pdf,.doc,.docx" 
                                                   class="hidden" onchange="updateDokumenFileName('penetapan-file', 'penetapan_filename'); showSelectedFile('penetapan')">
                                            <label for="penetapan-file" id="penetapan-upload-area" class="cursor-pointer block w-full border-2 border-dashed border-gray-300 rounded-md p-3 text-center hover:border-slate-400 transition-colors">
                                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                <p class="mt-1 text-xs text-gray-600">Upload file</p>
                                            </label>

                                            <!-- File info display -->
                                            <div id="penetapan-file-info" class="mt-2 hidden">
                                                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center">
                                                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p id="penetapan_filename" class="text-sm font-medium text-gray-900"></p>
                                                                <p class="text-xs text-gray-500">File siap untuk diupload</p>
                                                            </div>
                                                        </div>
                                                        <button type="button" onclick="clearFile('penetapan')" class="text-red-500 hover:text-red-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="upload-btn w-full px-3 py-2 bg-slate-600 text-white text-xs font-medium rounded-md hover:bg-slate-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                            Upload
                                        </button>
                                    </form>
                                </div>
                            @else
                                <!-- Upload Not Available -->
                                <div class="text-center py-8">
                                    <div class="mx-auto h-12 w-12 text-slate-400 mb-4">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-slate-500 font-medium">
                                        @if($activityExpired && !$isSuperAdmin)
                                            Upload tidak tersedia - Hanya Super Admin yang dapat upload setelah kegiatan berakhir
                                        @elseif(!$isSuperAdmin && !$isAdmin)
                                            Upload tidak tersedia - Hanya Super Admin dan Admin yang dapat upload
                                        @else
                                            Dokumen belum diunggah
                                        @endif
                                    </p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Triwulan Sections -->
            @php
                $triwulanData = [
                    1 => ['nama' => 'Triwulan I', 'periode' => 'Januari - Maret', 'color' => 'green'],
                    2 => ['nama' => 'Triwulan II', 'periode' => 'April - Juni', 'color' => 'amber'],
                    3 => ['nama' => 'Triwulan III', 'periode' => 'Juli - September', 'color' => 'blue'],
                    4 => ['nama' => 'Triwulan IV', 'periode' => 'Oktober - Desember', 'color' => 'purple']
                ];

                $dokumenTypes = [
                    'sk_penerima_triwulan' => 'SK Penerima Pegawai Terbaik',
                    'piagam_penghargaan_triwulan' => 'Piagam Penghargaan Pegawai',
                    'rekap_pemilihan_triwulan' => 'Rekap Pemilihan Pegawai Terbaik'
                ];
            @endphp

            @foreach($triwulanData as $triwulan => $data)
                <div id="section-triwulan-{{ $triwulan }}" class="content-section">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h2 class="text-xl font-semibold text-slate-900">{{ $data['nama'] }}</h2>
                                    <p class="text-sm text-slate-600 mt-1">{{ $data['periode'] }} {{ $kegiatan->tahun_berjalan }}</p>
                                    @php
                                        $periodInfo = $dokumenTriwulan[$triwulan]['_period_info'] ?? null;
                                    @endphp
                                    @if($periodInfo)
                                        <div class="mt-2">
                                            @if($periodInfo['status'] === 'upcoming')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Belum Dimulai
                                                </span>
                                            @elseif($periodInfo['status'] === 'closed')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                                    </svg>
                                                    Periode Berakhir
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                    </svg>
                                                    Periode Aktif
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    @php
                                        $completedDocs = 0;
                                        $totalDocs = count($dokumenTypes);
                                        // Check existing documents for this triwulan
                                        foreach($dokumenTypes as $type => $label) {
                                            if(isset($dokumenTriwulan[$triwulan][$type])) {
                                                $completedDocs++;
                                            }
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $completedDocs == $totalDocs ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-800' }}">
                                        {{ $completedDocs }}/{{ $totalDocs }} dokumen
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach($dokumenTypes as $type => $label)
                                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-sm font-medium text-slate-900">{{ $label }}</h3>
                                            @php
                                                $docExists = isset($dokumenTriwulan[$triwulan][$type]);
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                {{ $docExists ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-800' }}">
                                                {{ $docExists ? 'Uploaded' : 'Pending' }}
                                            </span>
                                        </div>

                                        @php
                                            // Get actual document for this type and triwulan
                                            $uploadedDoc = $dokumenTriwulan[$triwulan][$type] ?? null;
                                        @endphp

                                        @if($uploadedDoc)
                                            <!-- Document Already Uploaded -->
                                            <div class="space-y-3">
                                                <div class="bg-white border border-slate-200 rounded-lg p-3">
                                                    <div class="flex items-start space-x-3">
                                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-slate-900 break-words">{{ $uploadedDoc->nama_dokumen ?? $label }}</p>
                                                            <p class="text-xs text-slate-500 mt-1">Diupload: {{ $uploadedDoc->created_at ? $uploadedDoc->created_at->format('d M Y H:i') : 'N/A' }}</p>
                                                            @if($uploadedDoc->keterangan)
                                                                <p class="text-xs text-blue-600 mt-1 italic">{{ $uploadedDoc->keterangan }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="grid grid-cols-3 gap-2">
                                                    @if($uploadedDoc->webViewLink)
                                                        <a href="{{ $uploadedDoc->webViewLink }}" target="_blank" 
                                                           class="flex items-center justify-center bg-slate-500 hover:bg-slate-600 text-white py-2 px-3 rounded text-xs font-medium transition-colors">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                            Lihat
                                                        </a>
                                                    @endif
                                                    
                                                    @php
                                                        $periodInfo = $dokumenTriwulan[$triwulan]['_period_info'] ?? null;
                                                        $periodStatus = $periodInfo['status'] ?? 'unknown';
                                                        
                                                        // Apply same logic as detail.blade.php:
                                                        // 1. Cannot edit/delete if quarter status is 'upcoming' (even Super Admin)
                                                        // 2. If quarter expired ('closed'): only Super Admin can edit/delete
                                                        // 3. If quarter active: only Super Admin and Admin can edit/delete
                                                        // 4. Ketua Tim and Anggota Tim can only view
                                                        $canEditDelete = false;
                                                        if ($periodStatus === 'upcoming') {
                                                            $canEditDelete = false; // No one can edit/delete if quarter hasn't started
                                                        } elseif ($periodStatus === 'closed') {
                                                            $canEditDelete = $isSuperAdmin; // Only Super Admin can edit/delete after quarter ends
                                                        } elseif ($periodStatus === 'active') {
                                                            $canEditDelete = $isSuperAdmin || $isAdmin; // Super Admin and Admin can edit/delete during active quarter
                                                        }
                                                    @endphp
                                                    

                                            
                                            @if($canEditDelete)
                                                        <button onclick="openEditModal('{{ $type }}', '{{ $triwulan }}', '{{ $label }}', {{ $uploadedDoc->id }}, '{{ addslashes($uploadedDoc->nama_dokumen ?? $label) }}')" 
                                                                class="flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white py-2 px-3 rounded text-xs font-medium transition-colors">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                            Edit
                                                        </button>

                                                        <button onclick="deleteDocument({{ $uploadedDoc->id }}, '{{ $label }}', '{{ $triwulan }}')"
                                                                class="flex items-center justify-center bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded text-xs font-medium transition-colors">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                            Hapus
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            @php
                                                $periodInfo = $dokumenTriwulan[$triwulan]['_period_info'] ?? null;
                                                $periodStatus = $periodInfo['status'] ?? 'unknown';
                                                
                                                // Apply same logic as detail.blade.php:
                                                // 1. Cannot upload if quarter status is 'upcoming' (even Super Admin)
                                                // 2. If quarter expired ('closed'): only Super Admin can upload
                                                // 3. If quarter active: only Super Admin and Admin can upload
                                                // 4. Ketua Tim and Anggota Tim can only view
                                                $canUploadNew = false;
                                                if ($periodStatus === 'upcoming') {
                                                    $canUploadNew = false; // No one can upload if quarter hasn't started
                                                } elseif ($periodStatus === 'closed') {
                                                    $canUploadNew = $isSuperAdmin; // Only Super Admin can upload after quarter ends
                                                } elseif ($periodStatus === 'active') {
                                                    $canUploadNew = $isSuperAdmin || $isAdmin; // Super Admin and Admin can upload during active quarter
                                                }
                                            @endphp
                                            

                                            
                                            @if($canUploadNew)
                                                
                                                <!-- Upload Form for New Document -->
                                                <form action="{{ route('bukti.dukung.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                                    @csrf
                                                    <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->id }}">
                                                    <input type="hidden" name="renstra_id" value="{{ $kegiatan->renstra_id }}">
                                                    <input type="hidden" name="jenis_dokumen" value="{{ $type }}">
                                                    <input type="hidden" name="triwulan" value="{{ $triwulan }}">
                                                    <input type="hidden" name="current_tab" value="triwulan-{{ $triwulan }}">

                                                    <div>
                                                        <input type="file" name="dokumen" id="{{ $type }}-{{ $triwulan }}-file" accept=".pdf,.doc,.docx" 
                                                               class="hidden" onchange="showSelectedFile('{{ $type }}-{{ $triwulan }}')">
                                                        <label for="{{ $type }}-{{ $triwulan }}-file" id="{{ $type }}-{{ $triwulan }}-upload-area" class="cursor-pointer block w-full border-2 border-dashed border-gray-300 rounded-md p-3 text-center hover:border-slate-400 transition-colors">
                                                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                            </svg>
                                                            <p class="mt-1 text-xs text-gray-600">Upload file</p>
                                                        </label>

                                                        <!-- File info display -->
                                                        <div id="{{ $type }}-{{ $triwulan }}-file-info" class="mt-2 hidden">
                                                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
                                                                <div class="flex items-center justify-between">
                                                                    <div class="flex items-center">
                                                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                            </svg>
                                                                        </div>
                                                                        <div>
                                                                            <p id="{{ $type }}-{{ $triwulan }}-filename" class="text-sm font-medium text-gray-900"></p>
                                                                            <p class="text-xs text-gray-500">File siap untuk diupload</p>
                                                                        </div>
                                                                    </div>
                                                                    <button type="button" onclick="clearFile('{{ $type }}-{{ $triwulan }}')" class="text-red-500 hover:text-red-700">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <button type="submit" class="upload-btn w-full flex items-center justify-center bg-slate-600 text-white py-2 px-3 rounded text-xs font-medium hover:bg-slate-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                        </svg>
                                                        Upload
                                                    </button>
                                                </form>
                                            @else
                                                <!-- Upload Not Available -->
                                                <div class="text-center py-4">
                                                    <div class="mx-auto h-8 w-8 text-slate-400 mb-2">
                                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                        </svg>
                                                    </div>
                                                    <p class="text-xs text-slate-500 font-medium">
                                                        @if($isSuperAdmin || $isAdmin)
                                                            @if($periodStatus === 'upcoming')
                                                                Upload tidak tersedia - Periode triwulan belum dimulai
                                                            @elseif($periodStatus === 'closed' && !$isSuperAdmin)
                                                                Upload tidak tersedia - Hanya Super Admin yang dapat upload setelah periode berakhir
                                                            @elseif($periodStatus === 'active' && !$isSuperAdmin && !$isAdmin)
                                                                Upload tidak tersedia - Hanya Super Admin dan Admin yang dapat upload
                                                            @else
                                                                Upload tidak tersedia
                                                            @endif
                                                        @else
                                                            Dokumen Belum Tersedia
                                                        @endif
                                                    </p>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>



    <!-- Modal Edit Dokumen -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0"
            id="modalContent">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-t-2xl p-5 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold">Edit Dokumen</h3>
                    </div>
                    <button onclick="closeEditModal()" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="p-5">
                <!-- Tab Navigation -->
                <div class="flex mb-5 bg-gray-100 rounded-lg p-1">
                    <button onclick="switchTab('name')" id="nameTab"
                        class="flex-1 py-2 px-3 rounded-md text-sm font-medium transition-colors bg-white text-red-600 shadow-sm">
                        Ubah Nama
                    </button>
                    <button onclick="switchTab('file')" id="fileTab"
                        class="flex-1 py-2 px-3 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-gray-800">
                        Upload Ulang
                    </button>
                </div>

                <!-- Tab Content: Edit Name -->
                <div id="nameContent" class="tab-content">
                    <form id="editNameForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-5">
                            <label for="nama_dokumen" class="block text-sm font-semibold text-gray-700 mb-2">Nama
                                Dokumen</label>
                            <div class="relative">
                                <input type="text" id="nama_dokumen" name="nama_dokumen" required
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" onclick="closeEditModal()"
                                class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-3 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all text-sm font-medium">
                                Simpan Nama
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab Content: Upload File -->
                <div id="fileContent" class="tab-content hidden">
                    <form id="editDokumenFileForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->id }}">
                        <input type="hidden" name="renstra_id" value="{{ $kegiatan->renstra_id }}">
                        <input type="hidden" name="jenis_dokumen" id="editJenisDokumen">
                        <input type="hidden" name="triwulan" id="editTriwulan">
                        <input type="hidden" name="replace_existing" value="1">
                        <input type="hidden" name="current_tab" id="editCurrentTab">
                        
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Upload Dokumen Baru</label>
                            <div
                                class="border-2 border-dashed border-red-300 rounded-lg p-4 text-center hover:border-red-400 transition-colors bg-red-50">
                                <div class="mb-3">
                                    <svg class="w-10 h-10 text-red-500 mx-auto" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                        </path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">Pilih file baru untuk mengganti dokumen</p>
                                <input type="file" name="dokumen" id="editFileInput" required
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                    class="w-full text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-100 file:text-red-700 hover:file:bg-red-200 cursor-pointer">
                                <p class="text-xs text-gray-500 mt-2" id="fileTypeInfo">PDF, DOC, DOCX, XLS, XLSX • Max
                                    10MB</p>
                            </div>
                        </div>

                        <input type="hidden" id="editDocumentId" name="document_id">

                        <div class="flex gap-2">
                            <button type="button" onclick="closeEditModal()"
                                class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-3 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all text-sm font-medium">
                                Upload Ulang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab navigation functionality
            const sections = document.querySelectorAll('.content-section');
            const navPills = document.querySelectorAll('.nav-pill');

            function showSection(targetId) {
                // Hide all sections
                sections.forEach(section => {
                    section.style.display = 'none';
                });

                // Remove active class from all nav pills
                navPills.forEach(pill => {
                    pill.classList.remove('bg-indigo-100', 'text-indigo-700');
                    pill.classList.add('text-slate-500', 'hover:text-slate-700');
                });

                // Show target section
                const targetSection = document.getElementById('section-' + targetId);
                if (targetSection) {
                    targetSection.style.display = 'block';
                }

                // Activate corresponding nav pill
                const targetNav = document.getElementById('nav-' + targetId);
                if (targetNav) {
                    targetNav.classList.add('bg-indigo-100', 'text-indigo-700');
                    targetNav.classList.remove('text-slate-500', 'hover:text-slate-700');
                }

                // Save active tab to session storage
                sessionStorage.setItem('activeTab', targetId);
            }

            // Handle URL hash navigation
            function handleHashChange() {
                const hash = window.location.hash.substring(1); // Remove # character
                if (hash) {
                    showSection(hash);
                } else {
                    // Always default to penetapan tab when page loads
                    showSection('penetapan');
                }
            }

            // Navigation function for buttons
            window.navigateToSection = function(sectionId) {
                window.location.hash = sectionId;
                showSection(sectionId);
                
                // Smooth scroll to top of section
                const targetSection = document.getElementById('section-' + sectionId);
                if (targetSection) {
                    targetSection.scrollIntoView({ behavior: 'smooth' });
                }
            };

            // Initialize on page load
            handleHashChange();

            // Listen for hash changes
            window.addEventListener('hashchange', handleHashChange);

            // Save tab before form submission
            const forms = document.querySelectorAll('form[action*="bukti.dukung.store"]');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const currentTab = window.location.hash.substring(1) || sessionStorage.getItem('activeTab') || 'penetapan';
                    sessionStorage.setItem('activeTab', currentTab);
                });
            });

            // File upload helper function for dokumen kegiatan (following detail.blade.php pattern)
            window.updateDokumenFileName = function(inputId, displayId) {
                const input = document.getElementById(inputId);
                const display = document.getElementById(displayId);

                if (input.files.length > 0) {
                    const file = input.files[0];
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);

                    // Check file size (max 10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        showErrorMessage('Ukuran file terlalu besar. Maksimal 10MB.');
                        input.value = '';
                        return;
                    }

                    display.textContent = `${file.name} (${fileSize} MB)`;
                } else {
                    display.textContent = '';
                }
            };

            // Legacy function for backward compatibility
            window.updateFileName = function(inputId, displayId) {
                const input = document.getElementById(inputId);
                const display = document.getElementById(displayId);
                const form = input.closest('form');
                const submitBtn = form.querySelector('.upload-btn');
                const uploadPrompt = document.getElementById(inputId + '-upload-prompt');
                const fileSelected = document.getElementById(inputId + '-file-selected');
                const selectedName = document.getElementById(inputId + '-selected-name');
                const selectedSize = document.getElementById(inputId + '-selected-size');
                
                if (input && display) {
                    if (input.files.length > 0) {
                        const file = input.files[0];
                        const fileName = file.name;
                        const fileSize = (file.size / 1024 / 1024).toFixed(2);
                        
                        // Validate file type
                        const allowedTypes = ['.pdf', '.doc', '.docx'];
                        const fileExtension = '.' + fileName.split('.').pop().toLowerCase();
                        
                        if (!allowedTypes.includes(fileExtension)) {
                            display.textContent = 'Tipe file tidak didukung. Gunakan PDF, DOC, atau DOCX.';
                            display.classList.add('text-red-600', 'font-medium');
                            display.classList.remove('text-blue-600');
                            submitBtn.disabled = true;
                            // Reset to upload prompt state
                            if (uploadPrompt && fileSelected) {
                                uploadPrompt.classList.remove('hidden');
                                fileSelected.classList.add('hidden');
                            }
                            return;
                        }
                        
                        // Validate file size (max 10MB)
                        if (file.size > 10 * 1024 * 1024) {
                            display.textContent = 'Ukuran file terlalu besar. Maksimal 10MB.';
                            display.classList.add('text-red-600', 'font-medium');
                            display.classList.remove('text-blue-600');
                            submitBtn.disabled = true;
                            // Reset to upload prompt state
                            if (uploadPrompt && fileSelected) {
                                uploadPrompt.classList.remove('hidden');
                                fileSelected.classList.add('hidden');
                            }
                            return;
                        }
                        
                        // Show file selected state
                        if (uploadPrompt && fileSelected && selectedName && selectedSize) {
                            uploadPrompt.classList.add('hidden');
                            fileSelected.classList.remove('hidden');
                            selectedName.textContent = fileName;
                            selectedSize.textContent = `${fileSize} MB`;
                        }
                        
                        display.textContent = `File terpilih: ${fileName} (${fileSize} MB)`;
                        display.classList.add('text-indigo-600', 'font-medium');
                        display.classList.remove('text-red-600');
                        submitBtn.disabled = false;
                    } else {
                        // Reset to upload prompt state
                        if (uploadPrompt && fileSelected) {
                            uploadPrompt.classList.remove('hidden');
                            fileSelected.classList.add('hidden');
                        }
                        
                        display.textContent = '';
                        display.classList.remove('text-indigo-600', 'text-red-600', 'font-medium');
                        submitBtn.disabled = true;
                    }
                }
            };

            // Show selected file with better UI (following detail.blade.php pattern)
            window.showSelectedFile = function(docType) {
                const input = document.getElementById(docType + '-file');
                const uploadArea = document.getElementById(docType + '-upload-area');
                const fileInfo = document.getElementById(docType + '-file-info');
                const filename = document.getElementById(docType + '-filename');
                const form = input ? input.closest('form') : null;
                const submitBtn = form ? form.querySelector('.upload-btn') : null;

                if (input && input.files.length > 0) {
                    const file = input.files[0];
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);

                    // Validation - max 10MB for all document types
                    const maxSize = 10 * 1024 * 1024;
                    const maxSizeText = '10MB';
                    
                    // Validate file type
                    const allowedTypes = ['.pdf', '.doc', '.docx'];
                    const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                    
                    if (!allowedTypes.includes(fileExtension)) {
                        showErrorMessage('Tipe file tidak didukung. Gunakan PDF, DOC, atau DOCX.');
                        input.value = '';
                        if (submitBtn) submitBtn.disabled = true;
                        return;
                    }
                    
                    if (file.size > maxSize) {
                        showErrorMessage(`Ukuran file terlalu besar. Maksimal ${maxSizeText}.`);
                        input.value = '';
                        if (submitBtn) submitBtn.disabled = true;
                        return;
                    }

                    // Update tampilan
                    if (filename) {
                        filename.textContent = `${file.name} (${fileSize} MB)`;
                    }
                    if (uploadArea) {
                        uploadArea.classList.add('hidden'); // Hide upload area
                    }
                    if (fileInfo) {
                        fileInfo.classList.remove('hidden'); // Show file info
                    }
                    
                    // Enable upload button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                    }
                }
            };

            // Function untuk clear file selection
            window.clearFile = function(docType) {
                const input = document.getElementById(docType + '-file');
                const uploadArea = document.getElementById(docType + '-upload-area');
                const fileInfo = document.getElementById(docType + '-file-info');
                const filename = document.getElementById(docType + '-filename');
                const form = input ? input.closest('form') : null;
                const submitBtn = form ? form.querySelector('.upload-btn') : null;

                if (input) {
                    input.value = '';
                }
                if (uploadArea) {
                    uploadArea.classList.remove('hidden'); // Show upload area
                }
                if (fileInfo) {
                    fileInfo.classList.add('hidden'); // Hide file info
                }
                if (filename) {
                    filename.textContent = '';
                }
                
                // Disable upload button
                if (submitBtn) {
                    submitBtn.disabled = true;
                }
            };

            // Special function for penetapan mekanisme file upload
            window.updatePenetapanFileName = function() {
                const input = document.getElementById('penetapan-file');
                const display = document.getElementById('penetapan-filename');
                const uploadPrompt = document.getElementById('penetapan-upload-prompt');
                const fileSelected = document.getElementById('penetapan-file-selected');
                const selectedName = document.getElementById('penetapan-selected-name');
                const selectedSize = document.getElementById('penetapan-selected-size');
                const submitBtn = document.getElementById('penetapan-upload-btn');
                
                if (input.files.length > 0) {
                    const file = input.files[0];
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    
                    // Validate file type
                    const allowedTypes = ['.pdf', '.doc', '.docx'];
                    const fileExtension = '.' + fileName.split('.').pop().toLowerCase();
                    
                    if (!allowedTypes.includes(fileExtension)) {
                        showErrorMessage('Tipe file tidak didukung. Gunakan PDF, DOC, atau DOCX.');
                        display.textContent = 'Tipe file tidak didukung. Gunakan PDF, DOC, atau DOCX.';
                        display.classList.add('text-red-600', 'font-medium');
                        display.classList.remove('text-blue-600');
                        submitBtn.disabled = true;
                        uploadPrompt.classList.remove('hidden');
                        fileSelected.classList.add('hidden');
                        return;
                    }
                    
                    // Validate file size (max 10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        showErrorMessage('Ukuran file terlalu besar. Maksimal 10MB.');
                        display.textContent = 'Ukuran file terlalu besar. Maksimal 10MB.';
                        display.classList.add('text-red-600', 'font-medium');
                        display.classList.remove('text-blue-600');
                        submitBtn.disabled = true;
                        uploadPrompt.classList.remove('hidden');
                        fileSelected.classList.add('hidden');
                        return;
                    }
                    
                    // Show file selected state
                    uploadPrompt.classList.add('hidden');
                    fileSelected.classList.remove('hidden');
                    selectedName.textContent = fileName;
                    selectedSize.textContent = `Ukuran: ${fileSize} MB`;
                    display.textContent = '';
                    display.classList.remove('text-red-600', 'font-medium');
                    submitBtn.disabled = false;
                } else {
                    // Reset to upload prompt state
                    uploadPrompt.classList.remove('hidden');
                    fileSelected.classList.add('hidden');
                    display.textContent = '';
                    display.classList.remove('text-blue-600', 'text-red-600', 'font-medium');
                    submitBtn.disabled = true;
                }
            };

            // Show edit modal for penetapan mekanisme (following detail.blade.php pattern)
            window.showEditModalPenetapan = function(documentId, documentName) {
                const modal = document.getElementById('editModal');
                const modalContent = document.getElementById('modalContent');
                
                // Show modal
                modal.classList.remove('hidden');
                
                // Animate modal appearance
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
                
                // Set form data for name editing
                document.getElementById('nama_dokumen').value = documentName || 'Dokumen Penetapan Mekanisme';
                document.getElementById('editNameForm').action = `{{ route('bukti.dukung.update.name', ':id') }}`.replace(':id', documentId);
                document.getElementById('editDokumenFileForm').action = `{{ route('bukti.dukung.update.file', ':id') }}`.replace(':id', documentId);
                document.getElementById('editDocumentId').value = documentId;
                
                // Set file type restrictions
                const fileInput = document.getElementById('editFileInput');
                const fileTypeInfo = document.getElementById('fileTypeInfo');
                
                fileInput.accept = '.pdf,.doc,.docx,.xls,.xlsx';
                fileTypeInfo.textContent = 'PDF, DOC, DOCX, XLS, XLSX • Max 10MB';
                
                // Reset to first tab
                switchTab('name');
            };
            
            // Clear file function for penetapan
            window.clearPenetapanFile = function() {
                const input = document.getElementById('penetapan-file');
                const display = document.getElementById('penetapan-filename');
                const uploadPrompt = document.getElementById('penetapan-upload-prompt');
                const fileSelected = document.getElementById('penetapan-file-selected');
                const submitBtn = document.getElementById('penetapan-upload-btn');
                
                // Reset file input
                input.value = '';
                
                // Reset display
                display.textContent = '';
                display.classList.remove('text-indigo-600', 'text-red-600', 'font-medium');
                
                // Show upload prompt, hide file selected
                uploadPrompt.classList.remove('hidden');
                fileSelected.classList.add('hidden');
                
                // Disable submit button
                submitBtn.disabled = true;
            };

            // Clear file function for quarterly documents
            window.clearQuarterlyFile = function(inputId) {
                const input = document.getElementById(inputId);
                const display = document.getElementById(inputId + '-filename');
                const uploadPrompt = document.getElementById(inputId + '-upload-prompt');
                const fileSelected = document.getElementById(inputId + '-file-selected');
                const submitBtn = document.getElementById(inputId + '-submit-btn');
                
                // Reset file input
                input.value = '';
                
                // Reset display
                display.textContent = '';
                display.classList.remove('text-indigo-600', 'text-red-600', 'font-medium');
                
                // Show upload prompt, hide file selected
                uploadPrompt.classList.remove('hidden');
                fileSelected.classList.add('hidden');
                
                // Disable submit button
                submitBtn.disabled = true;
            };

            // Initialize all upload buttons as disabled
            document.querySelectorAll('.upload-btn').forEach(btn => {
                btn.disabled = true;
            });

            // Open edit modal with tab functionality (following detail.blade.php pattern)
            window.openEditModal = function(type, triwulan, label, documentId, currentName) {
                const modal = document.getElementById('editModal');
                const modalContent = document.getElementById('modalContent');
                
                // Show modal
                modal.classList.remove('hidden');
                
                // Animate modal appearance
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
                
                // Set form data for name editing
                document.getElementById('nama_dokumen').value = currentName || label;
                document.getElementById('editNameForm').action = `{{ route('bukti.dukung.update.name', ':id') }}`.replace(':id', documentId);
                document.getElementById('editDokumenFileForm').action = `{{ route('bukti.dukung.update.file', ':id') }}`.replace(':id', documentId);
                document.getElementById('editDocumentId').value = documentId;
                
                // Set file type restrictions
                const fileInput = document.getElementById('editFileInput');
                const fileTypeInfo = document.getElementById('fileTypeInfo');
                
                fileInput.accept = '.pdf,.doc,.docx,.xls,.xlsx';
                fileTypeInfo.textContent = 'PDF, DOC, DOCX, XLS, XLSX • Max 10MB';
                
                // Reset to first tab
                switchTab('name');
            };

            // Close edit modal (following detail.blade.php pattern)
            window.closeEditModal = function() {
                const modal = document.getElementById('editModal');
                const modalContent = document.getElementById('modalContent');
                
                // Animate modal disappearance
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            };
            
            // Switch tab function for edit modal
            window.switchTab = function(tabName) {
                // Reset all tabs
                document.getElementById('nameTab').classList.remove('bg-white', 'text-red-600', 'shadow-sm');
                document.getElementById('nameTab').classList.add('text-gray-600', 'hover:text-gray-800');
                document.getElementById('fileTab').classList.remove('bg-white', 'text-red-600', 'shadow-sm');
                document.getElementById('fileTab').classList.add('text-gray-600', 'hover:text-gray-800');
                
                // Hide all content
                document.getElementById('nameContent').classList.add('hidden');
                document.getElementById('fileContent').classList.add('hidden');
                
                // Show selected tab
                if (tabName === 'name') {
                    document.getElementById('nameTab').classList.add('bg-white', 'text-red-600', 'shadow-sm');
                    document.getElementById('nameTab').classList.remove('text-gray-600', 'hover:text-gray-800');
                    document.getElementById('nameContent').classList.remove('hidden');
                } else {
                    document.getElementById('fileTab').classList.add('bg-white', 'text-red-600', 'shadow-sm');
                    document.getElementById('fileTab').classList.remove('text-gray-600', 'hover:text-gray-800');
                    document.getElementById('fileContent').classList.remove('hidden');
                }
            };

            // File input change handler for edit modal (following detail.blade.php pattern)
            document.getElementById('editFileInput').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    console.log(`Selected file: ${file.name}, Size: ${fileSize}MB`);
                }
            });

            // Delete bukti dukung function
            window.deleteBuktiDukung = function(documentId, documentName) {
                if (confirm(`Apakah Anda yakin ingin menghapus "${documentName}"?`)) {
                    // Create and submit delete form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ route('bukti.dukung.destroy', ':id') }}`.replace(':id', documentId);

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    const tabInput = document.createElement('input');
                    tabInput.type = 'hidden';
                    tabInput.name = 'current_tab';
                    tabInput.value = sessionStorage.getItem('activeTab') || 'penetapan';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    form.appendChild(tabInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            };
            
            // Delete document function for quarterly documents
            window.deleteDocument = function(documentId, label, triwulan) {
                if (confirm(`Apakah Anda yakin ingin menghapus dokumen "${label}"?`)) {
                    // Create and submit delete form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ route('bukti.dukung.destroy', ':id') }}`.replace(':id', documentId);

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    const tabInput = document.createElement('input');
                    tabInput.type = 'hidden';
                    tabInput.name = 'current_tab';
                    tabInput.value = sessionStorage.getItem('activeTab') || (triwulan ? 'triwulan-' + triwulan : 'penetapan');

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    form.appendChild(tabInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            };



            // Close modal when clicking outside
            document.getElementById('editModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeEditModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const editModal = document.getElementById('editModal');
                    
                    if (!editModal.classList.contains('hidden')) {
                        closeEditModal();
                    }
                }
            });
            
            // Add loading overlay to edit forms with auto modal close
            document.addEventListener('DOMContentLoaded', function() {
                // Handle edit name form
                const editNameForm = document.getElementById('editNameForm');
                if (editNameForm) {
                    editNameForm.addEventListener('submit', function(e) {
                        // Close modal first
                        closeEditModal();
                        
                        // Show global loading overlay
                        if (typeof window.showGlobalLoading === 'function') {
                            window.showGlobalLoading('Mengupdate nama dokumen...');
                        }
                    });
                }
                
                // Handle edit file form
                const editFileForm = document.getElementById('editDokumenFileForm');
                if (editFileForm) {
                    editFileForm.addEventListener('submit', function(e) {
                        const fileInput = this.querySelector('#editFileInput');
                        
                        if (!fileInput.files.length) {
                            e.preventDefault();
                            alert('Silakan pilih file terlebih dahulu');
                            return;
                        }
                        
                        // Close modal first
                        closeEditModal();
                        
                        // Show global loading overlay
                        if (typeof window.showGlobalLoading === 'function') {
                            window.showGlobalLoading('Mengupload file baru...');
                        }
                    });
                }
            });

            // Show error message using global notification system
            function showErrorMessage(message) {
                if (typeof window.showError === 'function') {
                    window.showError(message);
                } else {
                    alert(message);
                }
            }

            // Show success message using global notification system
            function showSuccessMessage(message) {
                if (typeof window.showSuccess === 'function') {
                    window.showSuccess(message);
                } else {
                    alert(message);
                }
            }

            // Handle form submissions with AJAX (following detail.blade.php pattern)
            document.querySelectorAll('form[action*="bukti.dukung.store"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Always prevent default for AJAX
                    
                    const fileInput = this.querySelector('input[type="file"]');
                    
                    if (!fileInput || !fileInput.files.length) {
                        showErrorMessage('Silakan pilih file terlebih dahulu');
                        return;
                    }
                    
                    // Validate file before submission
                    const file = fileInput.files[0];
                    const allowedTypes = ['.pdf', '.doc', '.docx'];
                    const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                    
                    if (!allowedTypes.includes(fileExtension)) {
                        showErrorMessage('Tipe file tidak didukung. Gunakan PDF, DOC, atau DOCX.');
                        return;
                    }
                    
                    if (file.size > 10 * 1024 * 1024) {
                        showErrorMessage('Ukuran file terlalu besar. Maksimal 10MB.');
                        return;
                    }
                    
                    // Show global loading overlay
                    if (typeof window.showGlobalLoading === 'function') {
                        window.showGlobalLoading('Mengupload file...');
                    }
                    
                    // Submit form via AJAX
                    const formData = new FormData(this);
                    
                    // 🔧 FIX: Remove _token from FormData to avoid conflict with X-CSRF-TOKEN header
                    // Laravel expects CSRF token either in form data (_token) OR in header (X-CSRF-TOKEN), not both
                    // Using both can cause 419 "Page Expired" errors due to token validation conflicts
                    formData.delete('_token');
                    
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Hide loading overlay
                        if (typeof window.hideGlobalLoading === 'function') {
                            window.hideGlobalLoading();
                        }
                        
                        if (data.success) {
                            showSuccessMessage(data.message);
                            // Reload page to show updated document list
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showErrorMessage(data.message || 'Terjadi kesalahan saat mengupload file');
                        }
                    })
                    .catch(error => {
                        console.error('Upload error:', error);
                        // Hide loading overlay
                        if (typeof window.hideGlobalLoading === 'function') {
                            window.hideGlobalLoading();
                        }
                        showErrorMessage('Terjadi kesalahan saat mengupload file. Silakan coba lagi.');
                    });
                });
            });

            // Remove old edit form handler as it's now handled by individual forms above

            // Handle all form submissions globally for consistent loading behavior
            document.addEventListener('submit', function(e) {
                const form = e.target;
                
                // Skip if form has data-no-loading attribute
                if (form.hasAttribute('data-no-loading')) {
                    return;
                }
                
                // Skip bukti dukung forms and edit forms as they are handled specifically above
                if (form.action && (form.action.includes('bukti.dukung.store') || form.id === 'editNameForm' || form.id === 'editDokumenFileForm')) {
                    return;
                }
                
                // Show loading for other forms
                if (typeof window.showGlobalLoading === 'function') {
                    window.showGlobalLoading('Memproses...');
                }
            });
        });
    </script>
@endpush