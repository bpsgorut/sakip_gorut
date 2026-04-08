@extends('components.master')

@section('title', 'Detail Reward Punishment')

@section('content')
    @include('components.breadcrumbs')

    <div class="container mx-auto py-6">
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
                                <p class="text-white/90 text-sm">Monitoring Capaian Kinerja FRA • Tahun {{ $kegiatan->tahun_berjalan }}</p>
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
                    <button onclick="navigateToSection('triwulan-1')" 
                        id="nav-triwulan-1"
                        class="nav-pill flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors text-slate-600 hover:text-slate-900">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Triwulan I
                    </button>
                    <button onclick="navigateToSection('triwulan-2')" 
                        id="nav-triwulan-2"
                        class="nav-pill flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors text-slate-600 hover:text-slate-900">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Triwulan II
                    </button>
                    <button onclick="navigateToSection('triwulan-3')" 
                        id="nav-triwulan-3"
                        class="nav-pill flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors text-slate-600 hover:text-slate-900">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Triwulan III
                    </button>
                    <button onclick="navigateToSection('triwulan-4')" 
                        id="nav-triwulan-4"
                        class="nav-pill flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors text-slate-600 hover:text-slate-900">
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

            <!-- Triwulan Sections -->
            @php
                $triwulanData = [
                    1 => ['nama' => 'Triwulan I', 'periode' => 'Januari - Maret', 'periode_upload' => 'April', 'color' => 'green'],
                    2 => ['nama' => 'Triwulan II', 'periode' => 'April - Juni', 'periode_upload' => 'Juli', 'color' => 'amber'],
                    3 => ['nama' => 'Triwulan III', 'periode' => 'Juli - September', 'periode_upload' => 'Oktober', 'color' => 'blue'],
                    4 => ['nama' => 'Triwulan IV', 'periode' => 'Oktober - Desember', 'periode_upload' => 'Januari tahun berikutnya', 'color' => 'purple']
                ];

                $dokumenTypes = [
                    'notulensi' => 'Notulensi',
                    'surat_undangan' => 'Surat Undangan',
                    'daftar_hadir' => 'Daftar Hadir'
                ];
            @endphp

            @foreach($triwulanData as $triwulan => $data)
                <div id="section-triwulan-{{ $triwulan }}" class="content-section hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h2 class="text-xl font-semibold text-slate-900">{{ $data['nama'] }}</h2>
                                    <p class="text-sm text-slate-600 mt-1">{{ $data['periode'] }} {{ $kegiatan->tahun_berjalan }}</p>
                                    <p class="text-xs text-indigo-600 mt-1 font-medium">Upload: {{ $data['periode_upload'] }}</p>
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
                                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-xs font-medium text-slate-900 break-words">{{ $uploadedDoc->nama_dokumen ?? $label }}</p>
                                                            <p class="text-xs text-slate-500 mt-1">{{ $uploadedDoc->created_at ? $uploadedDoc->created_at->format('d M Y H:i') : 'N/A' }}</p>
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
                                                $periodStatus = $periodInfo['status'] ?? 'active';
                                                
                                                // Determine edit/delete permissions based on period status
                                                if ($periodStatus === 'upcoming') {
                                                    $canEditDelete = false; // No one can edit/delete if quarter hasn't started
                                                } elseif ($periodStatus === 'closed') {
                                                    $canEditDelete = $isSuperAdmin; // Only Super Admin can edit/delete after quarter ends
                                                } else {
                                                    $canEditDelete = $isSuperAdmin || $isAdmin; // Super Admin and Admin can edit/delete during active quarter
                                                }
                                            @endphp
                                            
                                            @if ($isSuperAdmin && $periodStatus === 'closed')
                                                <!-- Warning for Super Admin when editing/deleting after period ends -->
                                                <div class="mb-3 p-2 bg-amber-50 border border-amber-200 rounded-md">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                        </svg>
                                                        <p class="text-xs text-amber-800 font-medium">Periode upload telah berakhir - Anda dapat edit/hapus sebagai Super Admin</p>
                                                    </div>
                                                </div>
                                            @endif
                                            
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
                                                $periodStatus = $periodInfo['status'] ?? 'active';
                                                
                                                // Determine upload permissions based on period status
                                                if ($periodStatus === 'upcoming') {
                                                    $canUploadNew = false; // No one can upload if quarter hasn't started
                                                } elseif ($periodStatus === 'closed') {
                                                    $canUploadNew = $isSuperAdmin; // Only Super Admin can upload after quarter ends
                                                } else {
                                                    $canUploadNew = $isSuperAdmin || $isAdmin; // Super Admin and Admin can upload during active quarter
                                                }
                                            @endphp
                                            
                                            @if($canUploadNew)
                                                @if ($isSuperAdmin && $periodStatus === 'closed')
                                                    <!-- Warning for Super Admin when uploading after period ends -->
                                                    <div class="mb-3 p-2 bg-amber-50 border border-amber-200 rounded-md">
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                            </svg>
                                                            <p class="text-xs text-amber-800 font-medium">Periode upload telah berakhir - Anda dapat upload sebagai Super Admin</p>
                                                        </div>
                                                    </div>
                                                @endif
                                                
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
                                                                Upload belum tersedia - Periode upload belum dimulai
                                                            @elseif($periodStatus === 'closed' && !$isSuperAdmin)
                                                                Upload tidak tersedia - Periode upload telah berakhir
                                                            @elseif($periodStatus === 'active' && !$isSuperAdmin && !$isAdmin)
                                                                Upload tidak tersedia - Hanya Super Admin dan Admin yang dapat upload
                                                            @else
                                                                Dokumen belum diunggah
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
    <div id="editModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="fixed inset-0 bg-black opacity-50" id="editModalOverlay"></div>
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg relative z-10 scale-95 transition-transform duration-300">
            <div class="p-6">
                <div class="relative border-b pb-3 mb-4">
                    <div class="flex justify-center">
                        <h3 class="text-lg font-bold text-slate-800" id="editModalTitle">Edit Dokumen</h3>
                    </div>
                    <button onclick="closeEditModal()"
                        class="absolute right-0 top-0 text-gray-500 hover:text-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>

                <!-- Tab Navigation -->
                <div class="flex border-b border-gray-200 mb-4">
                    <button type="button" onclick="switchEditTab('name')" id="editNameTab" 
                        class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none">
                        Edit Nama
                    </button>
                    <button type="button" onclick="switchEditTab('file')" id="editFileTab" 
                        class="px-4 py-2 text-sm font-medium text-slate-600 border-b-2 border-slate-500 focus:outline-none">
                        Ganti File
                    </button>
                </div>

                <!-- Edit Name Tab -->
                <div id="editNameContent" class="hidden">
                    <form action="{{ route('bukti.dukung.update.name', ':id') }}" method="POST" id="editNameForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="current_tab" id="editNameCurrentTab">
                        
                        <div class="mb-4">
                            <label for="editDocumentName" class="block text-sm font-medium text-gray-700 mb-2">Nama Dokumen</label>
                            <input type="text" name="nama_dokumen" id="editDocumentName" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent"
                                placeholder="Masukkan nama dokumen">
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                Batal
                            </button>
                            <button type="submit" 
                                class="px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-700 transition-colors">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Edit File Tab -->
                <div id="editFileContent">
                    <form id="editForm" action="{{ route('bukti.dukung.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->id }}">
                        <input type="hidden" name="renstra_id" value="{{ $kegiatan->renstra_id }}">
                        <input type="hidden" name="jenis_dokumen" id="editJenisDokumen">
                        <input type="hidden" name="triwulan" id="editTriwulan">
                        <input type="hidden" name="replace_existing" value="1">
                        <input type="hidden" name="current_tab" id="editCurrentTab">

                        <div class="mb-4">
                            <div>
                                <input type="file" name="dokumen" id="editFileInput" accept=".pdf,.doc,.docx" 
                                       class="hidden" onchange="showEditSelectedFile()">
                                <label for="editFileInput" id="editUploadArea" class="cursor-pointer block w-full border-2 border-dashed border-gray-300 rounded-md p-4 text-center hover:border-slate-400 transition-colors">
                                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">Pilih file baru untuk mengganti</p>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX (Maksimal 10MB)</p>
                                </label>

                                <!-- File info display -->
                                <div id="editFileInfo" class="mt-2 hidden">
                                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p id="editFileName" class="text-sm font-medium text-gray-900"></p>
                                                    <p class="text-xs text-gray-500">File siap untuk diupload</p>
                                                </div>
                                            </div>
                                            <button type="button" onclick="clearEditFile()" class="text-red-500 hover:text-red-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                Batal
                            </button>
                            <button type="submit" id="editSubmitBtn"
                                class="flex items-center justify-center bg-slate-600 text-white px-4 py-2 rounded-md hover:bg-slate-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Ganti File
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
                    section.classList.add('hidden');
                });

                // Remove active class from all nav pills
                navPills.forEach(pill => {
                    pill.classList.remove('bg-indigo-600', 'text-white');
                    pill.classList.add('text-slate-600', 'hover:text-slate-900');
                });

                // Show target section
                const targetSection = document.getElementById('section-' + targetId);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                }

                // Activate corresponding nav pill
                const targetNav = document.getElementById('nav-' + targetId);
                if (targetNav) {
                    targetNav.classList.remove('text-slate-600', 'hover:text-slate-900');
                    targetNav.classList.add('bg-indigo-600', 'text-white');
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
                    // Check for saved tab or default to active quarter from backend
                    const savedTab = sessionStorage.getItem('activeTab');
                    if(savedTab) {
                        showSection(savedTab);
                    } else {
                        // Find the active quarter based on backend period info
                        let activeQuarter = null;
                        
                        @php
                            // Pass the period info to JavaScript
                            $periodInfoJS = [];
                            foreach($triwulanData as $triwulan => $data) {
                                $periodInfo = $dokumenTriwulan[$triwulan]['_period_info'] ?? null;
                                $periodInfoJS[$triwulan] = $periodInfo ? $periodInfo['status'] : 'closed';
                            }
                        @endphp
                        
                        const periodInfo = @json($periodInfoJS);
                        
                        // Find the first active quarter
                        for (let quarter = 1; quarter <= 4; quarter++) {
                            if (periodInfo[quarter] === 'active') {
                                activeQuarter = quarter;
                                break;
                            }
                        }
                        
                        // If no active quarter found, fall back to current month logic
                        if (!activeQuarter) {
                            const currentMonth = new Date().getMonth() + 1;
                            if (currentMonth >= 1 && currentMonth <= 3) {
                                activeQuarter = 1;
                            } else if (currentMonth >= 4 && currentMonth <= 6) {
                                activeQuarter = 2;
                            } else if (currentMonth >= 7 && currentMonth <= 9) {
                                activeQuarter = 3;
                            } else {
                                activeQuarter = 4;
                            }
                        }
                        
                        showSection('triwulan-' + activeQuarter);
                    }
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
                    const currentTab = window.location.hash.substring(1) || sessionStorage.getItem('activeTab') || 'triwulan-1';
                    sessionStorage.setItem('activeTab', currentTab);
                });
            });

            // File upload helper function
            window.showSelectedFile = function(prefix) {
                const fileInput = document.getElementById(prefix + '-file');
                const uploadArea = document.getElementById(prefix + '-upload-area');
                const fileInfo = document.getElementById(prefix + '-file-info');
                const fileName = document.getElementById(prefix + '-filename');
                const form = fileInput.closest('form');
                const submitBtn = form.querySelector('.upload-btn');
                
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    
                    // Validate file type
                    const allowedTypes = ['.pdf', '.doc', '.docx'];
                    const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                    
                    if (!allowedTypes.includes(fileExtension)) {
                        alert('Tipe file tidak didukung. Gunakan PDF, DOC, atau DOCX.');
                        fileInput.value = '';
                        return;
                    }
                    
                    // Validate file size (max 10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar. Maksimal 10MB.');
                        fileInput.value = '';
                        return;
                    }
                    
                    // Show file info and hide upload area
                    uploadArea.classList.add('hidden');
                    fileInfo.classList.remove('hidden');
                    fileName.textContent = file.name;
                    submitBtn.disabled = false;
                } else {
                    // Show upload area and hide file info
                    uploadArea.classList.remove('hidden');
                    fileInfo.classList.add('hidden');
                    fileName.textContent = '';
                    submitBtn.disabled = true;
                }
            };
            
            // Clear file function
            window.clearFile = function(prefix) {
                const fileInput = document.getElementById(prefix + '-file');
                const uploadArea = document.getElementById(prefix + '-upload-area');
                const fileInfo = document.getElementById(prefix + '-file-info');
                const fileName = document.getElementById(prefix + '-filename');
                const form = fileInput.closest('form');
                const submitBtn = form.querySelector('.upload-btn');
                
                fileInput.value = '';
                uploadArea.classList.remove('hidden');
                fileInfo.classList.add('hidden');
                fileName.textContent = '';
                submitBtn.disabled = true;
            };

            // Initialize all upload buttons as disabled
            document.querySelectorAll('.upload-btn').forEach(btn => {
                btn.disabled = true;
            });

            // Tab switching in edit modal
            window.switchEditTab = function(tab) {
                const nameTab = document.getElementById('editNameTab');
                const fileTab = document.getElementById('editFileTab');
                const nameContent = document.getElementById('editNameContent');
                const fileContent = document.getElementById('editFileContent');
                
                if (tab === 'name') {
                    nameTab.classList.remove('text-gray-500', 'border-transparent');
                    nameTab.classList.add('text-slate-600', 'border-slate-500');
                    fileTab.classList.remove('text-slate-600', 'border-slate-500');
                    fileTab.classList.add('text-gray-500', 'border-transparent');
                    
                    nameContent.classList.remove('hidden');
                    fileContent.classList.add('hidden');
                    
                    sessionStorage.setItem('editModalTab', 'name');
                } else {
                    fileTab.classList.remove('text-gray-500', 'border-transparent');
                    fileTab.classList.add('text-slate-600', 'border-slate-500');
                    nameTab.classList.remove('text-slate-600', 'border-slate-500');
                    nameTab.classList.add('text-gray-500', 'border-transparent');
                    
                    fileContent.classList.remove('hidden');
                    nameContent.classList.add('hidden');
                    
                    sessionStorage.setItem('editModalTab', 'file');
                }
            };
            
            // Open edit modal
            window.openEditModal = function(type, triwulan, label, documentId, documentName) {
                const modal = document.getElementById('editModal');
                const title = document.getElementById('editModalTitle');
                const form = document.getElementById('editForm');
                const nameForm = document.getElementById('editNameForm');
                
                // Set modal title
                title.textContent = 'Edit ' + label;
                
                // Set form values for file upload
                document.getElementById('editJenisDokumen').value = type;
                document.getElementById('editTriwulan').value = triwulan;
                document.getElementById('editCurrentTab').value = sessionStorage.getItem('activeTab') || 'triwulan-' + triwulan;
                
                // Set form values for name edit
                document.getElementById('editNameCurrentTab').value = sessionStorage.getItem('activeTab') || 'triwulan-' + triwulan;
                document.getElementById('editDocumentName').value = documentName || '';
                
                // Update name form action with document ID
                if (documentId) {
                    nameForm.action = nameForm.action.replace(':id', documentId);
                }
                
                // Reset file form
                form.reset();
                clearEditFile();
                
                // Show file tab by default
                switchEditTab('file');
                
                // Show modal with animation
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    modal.querySelector('.scale-95').classList.add('scale-100');
                    modal.querySelector('.scale-95').classList.remove('scale-95');
                }, 10);
            };

            // Edit file handling functions
            window.showEditSelectedFile = function() {
                const fileInput = document.getElementById('editFileInput');
                const uploadArea = document.getElementById('editUploadArea');
                const fileInfo = document.getElementById('editFileInfo');
                const fileName = document.getElementById('editFileName');
                const submitBtn = document.getElementById('editSubmitBtn');
                
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    
                    // Validate file type
                    const allowedTypes = ['.pdf', '.doc', '.docx'];
                    const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                    
                    if (!allowedTypes.includes(fileExtension)) {
                        alert('Tipe file tidak didukung. Gunakan PDF, DOC, atau DOCX.');
                        fileInput.value = '';
                        return;
                    }
                    
                    // Validate file size (max 10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar. Maksimal 10MB.');
                        fileInput.value = '';
                        return;
                    }
                    
                    // Show file info and hide upload area
                    uploadArea.classList.add('hidden');
                    fileInfo.classList.remove('hidden');
                    fileName.textContent = file.name;
                    submitBtn.disabled = false;
                } else {
                    // Show upload area and hide file info
                    uploadArea.classList.remove('hidden');
                    fileInfo.classList.add('hidden');
                    fileName.textContent = '';
                    submitBtn.disabled = true;
                }
            };
            
            window.clearEditFile = function() {
                const fileInput = document.getElementById('editFileInput');
                const uploadArea = document.getElementById('editUploadArea');
                const fileInfo = document.getElementById('editFileInfo');
                const fileName = document.getElementById('editFileName');
                const submitBtn = document.getElementById('editSubmitBtn');
                
                fileInput.value = '';
                uploadArea.classList.remove('hidden');
                fileInfo.classList.add('hidden');
                fileName.textContent = '';
                submitBtn.disabled = true;
            };
            
            // Close edit modal
            window.closeEditModal = function() {
                const modal = document.getElementById('editModal');
                const form = document.getElementById('editForm');
                const nameForm = document.getElementById('editNameForm');
                
                // Reset forms
                form.reset();
                nameForm.reset();
                clearEditFile();
                
                // Hide modal with animation
                modal.classList.remove('opacity-100');
                modal.querySelector('.scale-100').classList.add('scale-95');
                modal.querySelector('.scale-100').classList.remove('scale-100');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            };

            // Delete document function
            window.deleteDocument = function(documentId, label, triwulan) {
                if (confirm(`Yakin ingin menghapus dokumen "${label}"? Tindakan ini tidak dapat dibatalkan.`)) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ route('bukti.dukung.destroy', ':id') }}`.replace(':id', documentId);
                    
                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfInput);
                    
                    // Add method override
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    // Add current tab
                    const tabInput = document.createElement('input');
                    tabInput.type = 'hidden';
                    tabInput.name = 'current_tab';
                    tabInput.value = sessionStorage.getItem('activeTab') || (triwulan ? 'triwulan-' + triwulan : 'triwulan-1');
                    form.appendChild(tabInput);
                    
                    // Submit form
                    document.body.appendChild(form);
                    form.submit();
                }
            };

            // Close modal when clicking outside
            document.getElementById('editModalOverlay').addEventListener('click', function(e) {
                closeEditModal();
            });

            // Close modal when pressing Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('editModal');
                    if (!modal.classList.contains('hidden')) {
                        closeEditModal();
                    }
                }
            });

            // Form submission feedback and validation
            document.querySelectorAll('form[action*="bukti.dukung.store"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const fileInput = form.querySelector('input[type="file"]');
                    if (!fileInput || !fileInput.files.length) {
                        e.preventDefault();
                        alert('Silakan pilih file terlebih dahulu');
                        return;
                    }
                    
                    // Validate file again on submit
                    const file = fileInput.files[0];
                    const allowedTypes = ['.pdf', '.doc', '.docx'];
                    const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                    
                    if (!allowedTypes.includes(fileExtension)) {
                        e.preventDefault();
                        alert('Tipe file tidak didukung. Gunakan PDF, DOC, atau DOCX.');
                        return;
                    }
                    
                    if (file.size > 10 * 1024 * 1024) {
                        e.preventDefault();
                        alert('Ukuran file terlalu besar. Maksimal 10MB.');
                        return;
                    }
                    
                    const submitBtn = form.querySelector('.upload-btn');
                    if (submitBtn) {
                        submitBtn.textContent = 'Uploading...';
                        submitBtn.disabled = true;
                    }
                });
            });
            
            // Initialize on page load - this will handle both hash and default quarter activation
            handleHashChange();
        });
    </script>
@endpush