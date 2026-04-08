@extends('components.master')

@section('title', 'Detail ' . $kegiatan->nama_kegiatan)

@section('content')
    @include('components.breadcrumbs')

    @php
        $currentDate = now();
        $startDate = \Carbon\Carbon::parse($kegiatan->tanggal_mulai);
        $endDate = \Carbon\Carbon::parse($kegiatan->tanggal_berakhir);
        
        // Check if user is super admin
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        $isSuperAdmin = Auth::check() && $user->isSuperAdmin();

        // Check if this is a capaian kinerja activity
        $isCapaianKinerja = stripos($kegiatan->nama_kegiatan, 'Capaian Kinerja FRA') !== false;

        // Determine status
        if ($currentDate->lt($startDate)) {
            $status = 'belum-dimulai';
        } elseif ($currentDate->lte($endDate)) {
            if ($uploadedCount == 0) {
                $status = 'open';
            } elseif ($uploadedCount > 0 && $uploadedCount < $totalRequired) {
                $status = 'fulfillment';
            } else {
                $status = 'complete';
            }
        } else {
            // For capaian kinerja that has passed deadline
            if ($isCapaianKinerja) {
                $status = 'expired';
            } else {
                $status = 'closed';
            }
        }
    @endphp


    <!-- Main Content -->
    <div class="container mx-auto py-6">

        <!-- Header Section - Custom Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            <!-- Card Judul Kegiatan (2 columns width) -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-rose-700 opacity-30"></div>
                    <img class="w-full h-32 object-cover" src="{{ asset('img/bg5.jpg') }}" alt="">
                    <div class="absolute inset-0 flex items-center p-4">
                        <div class="text-white">
                            <h1 class="text-xl font-bold mb-1">{{ $kegiatan->nama_kegiatan }}</h1>
                            <p class="text-sm opacity-90">Tahun {{ $kegiatan->tahun_berjalan }}</p>
                        </div>
                    </div>
                </div>

                <!-- Dates -->
                <div class="p-4 bg-white">
                    <div class="flex justify-between items-center text-sm">
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Tanggal Mulai</p>
                            <p class="font-semibold">{{ $startDate->format('d M Y') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Tanggal Selesai</p>
                            <p class="font-semibold">{{ $endDate->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Status Card (1 column width) -->
            <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col justify-center h-full">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <div class="flex items-center justify-center mb-2">
                        <div
                            class="w-8 h-8 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center mr-2">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-800">Timeline Status</h3>
                    </div>

                    <!-- Horizontal Timeline with Numbers 1-2-3-4 -->
                    <div class="flex items-center justify-between relative w-full">
                        <!-- Progress Line -->
                        <div class="absolute top-3 left-4 right-4 h-0.5 bg-gray-200 z-0">
                            <div class="h-full bg-red-600 transition-all duration-500"
                                style="width: @php
switch($status) {
                                     case 'open': echo '33%'; break;
                                     case 'fulfillment': echo '66%'; break;
                                     case 'complete': case 'closed': case 'expired': echo '100%'; break;
                                     default: echo '0%';
                                 } @endphp">
                            </div>
                        </div>

                        @php
                            if ($isCapaianKinerja) {
                                $statusSteps = [
                                    ['key' => 'open', 'label' => 'Dibuka'],
                                    ['key' => 'fulfillment', 'label' => 'Upload'],
                                    ['key' => 'complete', 'label' => 'Selesai'],
                                    ['key' => 'expired', 'label' => 'Kadaluwarsa'],
                                ];
                            } else {
                                $statusSteps = [
                                    ['key' => 'open', 'label' => 'Dibuka'],
                                    ['key' => 'fulfillment', 'label' => 'Upload'],
                                    ['key' => 'complete', 'label' => 'Selesai'],
                                    ['key' => 'closed', 'label' => 'Ditutup'],
                                ];
                            }
                        @endphp

                        @foreach ($statusSteps as $index => $step)
                            <div class="flex flex-col items-center relative z-10">
                                <div
                                    class="w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold mb-1
                                    {{ $status == $step['key'] ||
                                    ($status == 'complete' && in_array($step['key'], ['open', 'fulfillment'])) ||
                                    ($status == 'closed' && in_array($step['key'], ['open', 'fulfillment', 'complete'])) ||
                                    ($status == 'expired' && in_array($step['key'], ['open', 'fulfillment', 'complete'])) ||
                                    ($status == 'expired' && $step['key'] == 'expired')
                                        ? 'bg-red-600 text-white'
                                        : 'bg-gray-200 text-gray-500' }}">
                                    @if (
                                        $status == $step['key'] ||
                                            ($status == 'complete' && in_array($step['key'], ['open', 'fulfillment'])) ||
                                            ($status == 'closed' && in_array($step['key'], ['open', 'fulfillment', 'complete'])) ||
                                            ($status == 'expired' && in_array($step['key'], ['open', 'fulfillment', 'complete'])) ||
                                            ($status == 'expired' && $step['key'] == 'expired'))
                                        @if ($step['key'] == 'expired')
                                            ⏰
                                        @else
                                            ✓
                                        @endif
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <span class="text-xs font-medium text-gray-700 text-center">{{ $step['label'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Status Bell Section (Inside Timeline Card) -->
                    <div class="flex justify-center">
                        <div
                            class="bg-white px-3 py-1.5 rounded-full shadow-md border border-gray-200 flex items-center space-x-2">
                            <div class="flex items-center space-x-2">
                                <div
                                    class="w-6 h-6 rounded-full flex items-center justify-center
                                    @if ($status == 'belum-dimulai') bg-gray-100
                                    @elseif($status == 'open') bg-green-100
                                    @elseif($status == 'fulfillment') bg-amber-100
                                    @elseif($status == 'complete') bg-red-100
                                    @elseif($status == 'expired') bg-orange-100
                                    @else bg-gray-100 @endif">
                                    <svg class="w-3 h-3 
                                        @if ($status == 'belum-dimulai') text-gray-500
                                        @elseif($status == 'open') text-green-600
                                        @elseif($status == 'fulfillment') text-amber-600
                                        @elseif($status == 'complete') text-red-600
                                        @elseif($status == 'expired') text-orange-600
                                        @else text-gray-500 @endif"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($status == 'belum-dimulai')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @elseif($status == 'open')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @elseif($status == 'fulfillment')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @elseif($status == 'complete')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @elseif($status == 'expired')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728">
                                            </path>
                                        @endif
                                    </svg>
                                </div>

                                <div class="text-xs">
                                    <span
                                        class="font-semibold
                                        @if ($status == 'belum-dimulai') text-gray-700
                                        @elseif($status == 'open') text-green-700
                                        @elseif($status == 'fulfillment') text-amber-700
                                        @elseif($status == 'complete') text-red-700
                                        @elseif($status == 'expired') text-orange-700
                                        @else text-gray-700 @endif">
                                        @if ($status == 'belum-dimulai')
                                            Belum Dimulai
                                        @elseif($status == 'open')
                                            Sedang Berlangsung
                                        @elseif($status == 'fulfillment')
                                            Dalam Proses
                                        @elseif($status == 'complete')
                                            Selesai
                                        @elseif($status == 'expired')
                                            Kadaluwarsa
                                        @else
                                            Ditutup
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Pills -->
        @php
            // Tentukan tab yang tersedia berdasarkan status dan kondisi
            $availableTabs = [];

            // Hanya tampilkan tab jika kegiatan sudah dimulai
            if ($status !== 'belum-dimulai') {
                // Tab Kelengkapan Dokumen
                $availableTabs['kelengkapan-dokumen'] = [
                    'name' => 'Kelengkapan Dokumen',
                    'icon' =>
                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>',
                ];

                // Tab Dokumen untuk renstra dan PK (untuk upload dan lihat dokumen)
                if ((isset($isRenstraDetail) && $isRenstraDetail) || (isset($isPKDetail) && $isPKDetail)) {
                    $availableTabs['dokumen'] = [
                        'name' => 'Dokumen',
                        'icon' =>
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
                    ];
                }

                // Tab Bukti Dukung
                $availableTabs['bukti-dukung'] = [
                    'name' => 'Bukti Dukung',
                    'icon' =>
                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>',
                ];
            }

            // Hitung percentage untuk semua kondisi
            $percentage = $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 0;
        @endphp

        @if ($status == 'belum-dimulai')
            <!-- Tampilan Khusus untuk Belum Dimulai (Tanpa Tab) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Informasi Kegiatan</h2>
                            <p class="text-sm text-slate-700 mt-1">{{ $kegiatan->nama_kegiatan }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Status Belum Dimulai - Kompak -->
                    <div class="max-w-lg mx-auto">
                        <!-- Header Section -->
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2">Kegiatan Belum Dimulai</h3>
                            <p class="text-sm text-gray-600">
                                Dimulai pada <strong>{{ $startDate->format('d F Y') }}</strong>
                            </p>
                        </div>



                        <!-- Dokumen yang Diperlukan - Compact -->
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 text-center">Dokumen yang Diperlukan</h4>
                            @php
                                $previewDocs = [
                                    'notulensi' => 'Notulensi',
                                    'surat_undangan' => 'Surat Undangan',
                                    'daftar_hadir' => 'Daftar Hadir',
                                ];
                                if (isset($isPKDetail) && $isPKDetail) {
                                    $previewDocs = ['dokumen_pk' => 'Dokumen PK'] + $previewDocs;
                                }
                            @endphp
                            <div class="grid grid-cols-4 gap-3">
                                @foreach ($previewDocs as $key => $label)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
                                        <div
                                            class="w-8 h-8 bg-white rounded-lg flex items-center justify-center mx-auto mb-2">
                                            @if ($key === 'dokumen_pk')
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                            @elseif($key === 'notulensi')
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg>
                                            @elseif($key === 'surat_undangan')
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 8l7.89 7.89a2 2 0 002.828 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                            @endif
                                        </div>
                                        <h5 class="font-medium text-gray-700 text-xs">{{ $label }}</h5>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
@else
    <!-- Content dengan Tab Terintegrasi untuk Status Aktif -->
    <div class="space-y-0">

        <!-- Kelengkapan Dokumen Section -->
        <div id="section-kelengkapan-dokumen" class="content-section">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Tab Navigation Terintegrasi -->
                <div class="bg-white border-b border-gray-200">
                    <div class="flex">
                        @foreach ($availableTabs as $tabId => $tabData)
                            <button onclick="navigateToSection('{{ $tabId }}')" id="nav-{{ $tabId }}"
                                class="nav-pill flex-1 px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 transition-colors {{ $loop->first ? 'border-slate-500 text-slate-600 bg-slate-50' : 'text-gray-500 hover:text-gray-700' }}">
                                <div class="flex items-center justify-center space-x-2">
                                    <span class="flex-shrink-0">{!! $tabData['icon'] !!}</span>
                                    <span>{{ $tabData['name'] }}</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Content Area - Konsisten padding dengan section lain -->
                <div class="p-6">
                    @if ($status == 'expired')
                        <!-- Status Expired -->
                        <div class="text-center py-8">
                            <div
                                class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-3">Periode Upload Berakhir</h3>
                            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                Periode upload untuk kegiatan ini telah berakhir pada
                                <strong>{{ $endDate->format('d F Y') }}</strong>.
                                Anda masih dapat melihat dokumen yang telah diupload.
                            </p>

                            <!-- Summary -->
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 max-w-sm mx-auto">
                                <h4 class="text-lg font-semibold text-orange-800 mb-2">Status Kelengkapan</h4>
                                <div class="text-center">
                                    <span class="text-2xl font-bold text-orange-800">{{ $uploadedCount }}</span>
                                    <span class="text-sm text-orange-700"> dari {{ $totalRequired }} dokumen</span>
                                    <p class="text-xs text-orange-600 mt-1">telah berhasil diupload</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Status Kelengkapan Aktif - SUPER SIMPLIFIED -->
                        <div class="max-w-lg mx-auto">
                            <div class="text-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Status Kelengkapan</h3>
                                <p class="text-sm text-gray-600">{{ $startDate->format('d M Y') }} -
                                    {{ $endDate->format('d M Y') }}</p>
                            </div>

                            <!-- Simple Progress Display -->
                            <div class="bg-gray-50 rounded-lg p-6 border border-slate-200">
                                <div class="text-center">
                                    <!-- Progress Circle -->
                                    <div class="relative w-24 h-24 mx-auto mb-4">
                                        <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                                            <path
                                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                                fill="none" stroke="#e5e7eb" stroke-width="2" />
                                            <path
                                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                                fill="none" stroke="#64748b" stroke-width="2"
                                                stroke-dasharray="{{ $percentage }}, 100" />
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="text-center">
                                                <span class="text-xl font-bold text-grey-700">{{ $percentage }}%</span>
                                                <p class="text-xs text-gray-500">lengkap</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Document Count -->
                                    <div class="border-t border-slate-200 pt-4">
                                        <div class="flex justify-center items-center space-x-2">
                                            <span class="text-2xl font-bold text-gray-700">{{ $uploadedCount }}</span>
                                            <span class="text-gray-500">/</span>
                                            <span class="text-lg text-gray-600">{{ $totalRequired }}</span>
                                            <span class="text-sm text-gray-500">dokumen</span>
                                        </div>

                                        <div class="mt-2 text-sm text-gray-500">
                                            @if ($uploadedCount == $totalRequired)
                                                ✅ Semua dokumen telah lengkap
                                            @elseif($uploadedCount > 0)
                                                📋 {{ $totalRequired - $uploadedCount }} dokumen tersisa
                                            @else
                                                📄 Belum ada dokumen yang diupload
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if ((isset($isRenstraDetail) && $isRenstraDetail) || (isset($isPKDetail) && $isPKDetail))
            <!-- Dokumen Section -->
            <div id="section-dokumen" class="content-section" style="display: none;">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Tab Navigation Terintegrasi -->
                    <div class="bg-white border-b border-gray-200">
                        <div class="flex">
                            @foreach ($availableTabs as $tabId => $tabData)
                                <button onclick="navigateToSection('{{ $tabId }}')"
                                    id="nav-dokumen-{{ $tabId }}"
                                    class="nav-pill flex-1 px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 transition-colors {{ $tabId === 'dokumen' ? 'border-slate-500 text-slate-600 bg-slate-50' : 'text-gray-500 hover:text-gray-700' }}">
                                    <div class="flex items-center justify-center space-x-2">
                                        <span class="flex-shrink-0">{!! $tabData['icon'] !!}</span>
                                        <span>{{ $tabData['name'] }}</span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Content Area - Konsisten padding -->
                    <div class="p-6">
                        @php
                            // Use canUpload from controller
                            $canUploadDokumen = $canUpload;
                        @endphp
                        
                        @if ($canUploadDokumen && (!isset($dokumenKegiatan) || $dokumenKegiatan->isEmpty()))
                            @if ($isSuperAdmin && $activityExpired)
                                <div class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <span class="text-sm text-orange-800">
                                            <strong>Perhatian:</strong> Anda sedang mengupload dokumen setelah periode kegiatan berakhir sebagai Super Administrator.
                                        </span>
                                    </div>
                                </div>
                            @endif
                            <!-- Upload Form Section - only show when no document exists -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                    @if (isset($isRenstraDetail) && $isRenstraDetail)
                                        Upload Dokumen Renstra
                                    @elseif(isset($isPKDetail) && $isPKDetail)
                                        Upload Dokumen Perjanjian Kinerja
                                    @else
                                        Upload Dokumen
                                    @endif
                                </h3>
                                

                                
                                <form
                                    action="{{ isset($isRenstraDetail) && $isRenstraDetail ? route('dokumen.renstra.store', $kegiatan->id) : route('dokumen.kegiatan.store', $kegiatan->id) }}"
                                    method="POST" enctype="multipart/form-data"
                                    class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->id }}">

                                    <div>
                                        <input type="file" name="dokumen" id="dokumen_file"
                                            accept=".pdf,.doc,.docx,.xls,.xlsx" class="hidden"
                                            onchange="updateDokumenFileName('dokumen_file', 'dokumen_filename'); showSelectedFile('dokumen')">
                                        <label for="dokumen_file"
                                            id="dokumen-upload-area"
                                            class="cursor-pointer block w-full border-2 border-dashed border-gray-300 rounded-md p-3 text-center hover:border-slate-400 transition-colors">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="mt-1 text-xs text-gray-600">Upload file</p>
                                        </label>

                                        <!-- File info display -->
                                        <div id="dokumen-file-info" class="mt-2 hidden">
                                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div
                                                            class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                                            <svg class="w-4 h-4 text-green-600" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round"
                                                                    stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p id="dokumen_filename"
                                                                class="text-sm font-medium text-gray-900"></p>
                                                            <p class="text-xs text-gray-500">File siap untuk
                                                                diupload</p>
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                        onclick="clearFile('dokumen')"
                                                        class="text-red-500 hover:text-red-700">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit"
                                        class="upload-btn w-full px-3 py-2 bg-slate-600 text-white text-xs font-medium rounded-md hover:bg-slate-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                        Upload
                                    </button>
                                </form>
                            </div>
                        @endif

                        <!-- Divider jika ada dokumen -->
                        @if (isset($dokumenKegiatan) && $dokumenKegiatan->isNotEmpty())
                            <div class="border-t border-gray-200 mb-6"></div>
                        @endif

                        <!-- Dokumen yang Sudah Diupload -->
                        @if (isset($dokumenKegiatan) && $dokumenKegiatan->isNotEmpty())
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Dokumen yang Sudah Diupload</h3>
                                <div class="space-y-4">
                                    @foreach ($dokumenKegiatan as $dokumen)
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
                                                        <p class="text-sm font-medium text-gray-900">{{ $dokumen->nama_dokumen }}</p>
                                                        <p class="text-xs text-gray-500">Diupload: {{ $dokumen->created_at->format('d M Y H:i') }}</p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Action Buttons -->
                                                <div class="flex gap-2 ml-4">
                                                    @if ($dokumen->webViewLink)
                                                        <a href="{{ $dokumen->webViewLink }}" target="_blank" class="px-5 py-2 bg-slate-600 text-white text-sm font-medium rounded-md hover:bg-slate-700 transition-colors">
                                                            Lihat
                                                        </a>
                                                    @endif
                                                    @if ($canUpload)
                                                        <button onclick="showEditDokumenModal({{ $dokumen->id }}, '{{ $dokumen->nama_dokumen }}')" class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                                                            Edit
                                                        </button>
                                                        <button onclick="deleteDokumenKegiatan({{ $dokumen->id }}, '{{ $dokumen->nama_dokumen }}')" class="px-5 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
                                                            Hapus
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            @if ($status === 'expired')
                                <div class="text-center py-8">
                                    <div
                                        class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Dokumen</h3>
                                    <p class="text-gray-500">Belum ada dokumen yang diupload untuk kegiatan ini.</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Bukti Dukung Section -->
        <div id="section-bukti-dukung" class="content-section" style="display: none;">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Tab Navigation Terintegrasi -->
                <div class="bg-white border-b border-gray-200">
                    <div class="flex">
                        @foreach ($availableTabs as $tabId => $tabData)
                            <button onclick="navigateToSection('{{ $tabId }}')"
                                id="nav-bukti-{{ $tabId }}"
                                class="nav-pill flex-1 px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 transition-colors {{ $tabId === 'bukti-dukung' ? 'border-slate-500 text-slate-600 bg-slate-50' : 'text-gray-500 hover:text-gray-700' }}">
                                <div class="flex items-center justify-center space-x-2">
                                    <span class="flex-shrink-0">{!! $tabData['icon'] !!}</span>
                                    <span>{{ $tabData['name'] }}</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Content Area - Konsisten padding -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach (['notulensi', 'surat_undangan', 'daftar_hadir'] as $key)
                            @php
                                $label = ucfirst(str_replace('_', ' ', $key));
                                if ($key === 'surat_undangan') {
                                    $label = 'Surat Undangan';
                                }
                                if ($key === 'daftar_hadir') {
                                    $label = 'Daftar Hadir';
                                }

                                $jenisLabel = ucfirst(str_replace('_', ' ', $key));
                                if ($key === 'surat_undangan') {
                                    $jenisLabel = 'Surat Undangan';
                                }
                                if ($key === 'daftar_hadir') {
                                    $jenisLabel = 'Daftar Hadir';
                                }

                                $isUploaded = $buktiDukungByJenis->has($jenisLabel);
                            @endphp

                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        @if ($key === 'notulensi')
                                            <div
                                                class="w-6 h-6 bg-slate-100 rounded-lg flex items-center justify-center mr-2">
                                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg>
                                            </div>
                                        @elseif($key === 'surat_undangan')
                                            <div
                                                class="w-6 h-6 bg-slate-100 rounded-lg flex items-center justify-center mr-2">
                                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 8l7.89 7.89a2 2 0 002.828 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        @elseif($key === 'daftar_hadir')
                                            <div
                                                class="w-6 h-6 bg-slate-100 rounded-lg flex items-center justify-center mr-2">
                                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                        @endif
                                        <h3 class="text-sm font-medium text-gray-900">{{ $label }}</h3>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                {{ $isUploaded ? 'bg-slate-100 text-slate-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $isUploaded ? 'Uploaded' : 'Pending' }}
                                    </span>
                                </div>

                                @if ($isUploaded)
                                    @foreach ($buktiDukungByJenis[$jenisLabel] as $dokumen)
                                        <div class="space-y-3">
                                            <!-- Document Info -->
                                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
                                                <div class="flex items-start mb-2">
                                                    <div
                                                        class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                                        <svg class="h-4 w-4 text-slate-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p
                                                            class="text-xs font-medium text-gray-900 break-words leading-relaxed">
                                                            {{ $dokumen->nama_dokumen }}</p>
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            {{ $dokumen->created_at ? $dokumen->created_at->format('d M Y H:i') : 'N/A' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="grid grid-cols-3 gap-2">
                                                @if ($dokumen->webViewLink)
                                                    <a href="{{ $dokumen->webViewLink }}" target="_blank"
                                                        class="flex items-center justify-center bg-slate-500 hover:bg-slate-600 text-white py-2 px-3 rounded text-xs font-medium transition-colors">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                            </path>
                                                        </svg>
                                                        Lihat
                                                    </a>
                                                @endif

                                                @if ($canUpload)
                                                    <button
                                                        onclick="showEditModal({{ $dokumen->id }}, '{{ $dokumen->nama_dokumen }}', '{{ $key }}')"
                                                        class="flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white py-2 px-3 rounded text-xs font-medium transition-colors">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                            </path>
                                                        </svg>
                                                        Edit
                                                    </button>

                                                    <button
                                                        onclick="deleteBuktiDukung({{ $dokumen->id }}, '{{ $label }}')"
                                                        class="flex items-center justify-center bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded text-xs font-medium transition-colors">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @php
                                        // Use canUpload from controller
                                        $canUploadBuktiDukung = $canUpload;
                                    @endphp
                                    
                                    @if ($canUploadBuktiDukung)
                                        @if ($isSuperAdmin && $activityExpired)
                                            <!-- Super Admin Notice for Bukti Dukung -->
                                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-2 mb-3">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-orange-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    <p class="text-xs text-orange-800">
                                                        <span class="font-medium">Perhatian:</span> Anda sedang mengupload bukti dukung setelah periode kegiatan berakhir sebagai Super Administrator.
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Upload Form -->
                                        <form action="{{ route('bukti.dukung.store') }}" method="POST"
                                            enctype="multipart/form-data" class="space-y-3">
                                            @csrf
                                            <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->id }}">
                                            <input type="hidden" name="jenis_dokumen" value="{{ $key }}">
                                            <input type="hidden" name="current_tab" class="current-tab-input" value="">
                                            @if (isset($isRenstraDetail) && $isRenstraDetail)
                                                <input type="hidden" name="is_renstra_detail" value="1">
                                                <input type="hidden" name="renstra_id" value="{{ $kegiatan->id }}">
                                            @else
                                                <input type="hidden" name="renstra_id" value="{{ $kegiatan->renstra_id }}">
                                            @endif
                                            @if (isset($isPKDetail) && $isPKDetail)
                                                <input type="hidden" name="is_pk_detail" value="1">
                                            @endif

                                            <div>
                                                <input type="file" name="dokumen" id="{{ $key }}-file"
                                                    accept=".pdf,.doc,.docx,.xls,.xlsx" class="hidden"
                                                    onchange="updateFileName('{{ $key }}-file', '{{ $key }}-filename'); showSelectedFile('{{ $key }}')">
                                                <label for="{{ $key }}-file"
                                                    id="{{ $key }}-upload-area"
                                                    class="cursor-pointer block w-full border-2 border-dashed border-gray-300 rounded-md p-3 text-center hover:border-slate-400 transition-colors">
                                                    <svg class="mx-auto h-6 w-6 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    <p class="mt-1 text-xs text-gray-600">Upload file</p>
                                                </label>

                                                <!-- File info display -->
                                                <div id="{{ $key }}-file-info" class="mt-2 hidden">
                                                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center">
                                                                <div
                                                                    class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                                                    <svg class="w-4 h-4 text-green-600" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                                <div>
                                                                    <p id="{{ $key }}-filename"
                                                                        class="text-sm font-medium text-gray-900"></p>
                                                                    <p class="text-xs text-gray-500">File siap untuk
                                                                        diupload</p>
                                                                </div>
                                                            </div>
                                                            <button type="button"
                                                                onclick="clearFile('{{ $key }}')"
                                                                class="text-red-500 hover:text-red-700">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit"
                                                class="upload-btn w-full px-3 py-2 bg-slate-600 text-white text-xs font-medium rounded-md hover:bg-slate-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                                Upload
                                            </button>
                                        </form>
                                    @else
                                        <div class="text-center py-6">
                                            <div class="mx-auto h-12 w-12 text-gray-400 mb-3">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728">
                                                    </path>
                                                </svg>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                @if($isSuperAdmin || Auth::user()->isAdmin())
                                                    Upload tidak tersedia
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
    </div>
    @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0"
            id="deleteModalContent">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>

                <p class="text-gray-700 mb-6" id="deleteMessage">Apakah Anda yakin ingin menghapus dokumen ini?</p>

                <div class="flex gap-3">
                    <button onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button id="confirmDeleteBtn"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Hapus
                    </button>
                </div>
            </div>
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
                                    accept=".pdf,.doc,.docx,.xls,.xlsx"
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

    <script>
        // Navigation functions
        function navigateToSection(sectionId) {
            // Save current active tab to session storage
            sessionStorage.setItem('activeTab', sectionId);

            // Hide all sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => {
                section.style.display = 'none';
            });

            // Show selected section
            const targetSection = document.getElementById('section-' + sectionId);
            if (targetSection) {
                targetSection.style.display = 'block';
            }

            // Update navigation pills - reset all first
            const pills = document.querySelectorAll('.nav-pill');
            pills.forEach(pill => {
                pill.classList.remove('border-slate-500', 'text-slate-600', 'bg-slate-50');
                pill.classList.add('border-transparent', 'text-gray-500');
            });

            // Set active state for all instances of this navigation in each section
            const navSelectors = [
                'nav-' + sectionId,
                'nav-dokumen-' + sectionId,
                'nav-bukti-' + sectionId
            ];

            navSelectors.forEach(navId => {
                const navElement = document.getElementById(navId);
                if (navElement) {
                    navElement.classList.remove('border-transparent', 'text-gray-500');
                    navElement.classList.add('border-slate-500', 'text-slate-600', 'bg-slate-50');
                }
            });
        }

        // Initialize page - restore last active tab or show first section
        document.addEventListener('DOMContentLoaded', function() {
            // Check if there's a saved active tab
            const savedTab = sessionStorage.getItem('activeTab');
            const savedTabSection = savedTab ? document.getElementById('section-' + savedTab) : null;

            if (savedTab && savedTabSection) {
                // Restore the saved tab
                navigateToSection(savedTab);
            } else {
                // Default to first section if no saved tab or saved tab doesn't exist
                const firstSection = document.querySelector('.content-section');
                if (firstSection) {
                    const sectionId = firstSection.id.replace('section-', '');
                    navigateToSection(sectionId);
                }
            }

            // Add event listeners to all forms to save current tab before submit
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    // Find which section is currently active
                    const activeSection = document.querySelector(
                    '.content-section[style*="block"]');
                    if (activeSection) {
                        const activeSectionId = activeSection.id.replace('section-', '');
                        sessionStorage.setItem('activeTab', activeSectionId);
                        
                        // Update current_tab input for upload forms
                        const currentTabInput = this.querySelector('.current-tab-input');
                        if (currentTabInput) {
                            currentTabInput.value = activeSectionId;
                        }
                    }
                });
            });
            
            // Check for hash in URL on page load to restore tab after redirect
            const urlHash = window.location.hash.substring(1);
            if (urlHash) {
                const hashSection = document.getElementById('section-' + urlHash);
                if (hashSection) {
                    navigateToSection(urlHash);
                    // Clear the hash from URL
                    history.replaceState(null, null, window.location.pathname + window.location.search);
                }
            }
        });

        // File upload helper
        function updateFileName(inputId, displayId) {
            const input = document.getElementById(inputId);
            const display = document.getElementById(displayId);

            if (input.files.length > 0) {
                const file = input.files[0];
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                display.textContent = `${file.name} (${fileSize} MB)`;
                display.classList.add('text-green-600');
            } else {
                display.textContent = '';
                display.classList.remove('text-green-600');
            }
        }

        // Show selected file with better UI
        function showSelectedFile(docType) {
            const input = document.getElementById(docType + '-file') || document.getElementById('dokumen_file');
            const uploadArea = document.getElementById(docType + '-upload-area');
            const fileInfo = document.getElementById(docType + '-file-info');
            const filename = document.getElementById(docType + '-filename') || document.getElementById('dokumen_filename');

            if (input && input.files.length > 0) {
                const file = input.files[0];
                const fileSize = (file.size / 1024 / 1024).toFixed(2);

                // Validation - different limits for different types
                const maxSize = docType === 'dokumen' ? 10 * 1024 * 1024 : 10 * 1024 * 1024;
                const maxSizeText = docType === 'dokumen' ? '10MB' : '10MB';
                
                if (file.size > maxSize) {
                    alert(`Ukuran file terlalu besar. Maksimal ${maxSizeText}.`);
                    input.value = '';
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
            }
        }

        // Function untuk clear file selection
        function clearFile(docType) {
            // Handle different ID patterns for different document types
            let input, uploadArea, fileInfo;
            
            if (docType === 'dokumen') {
                input = document.getElementById('dokumen_file');
                uploadArea = document.getElementById('dokumen-upload-area');
                fileInfo = document.getElementById('dokumen-file-info');
            } else {
                input = document.getElementById(docType + '-file');
                uploadArea = document.getElementById(docType + '-upload-area');
                fileInfo = document.getElementById(docType + '-file-info');
            }

            if (input) {
                input.value = '';
            }
            if (uploadArea) {
                uploadArea.classList.remove('hidden'); // Show upload area
            }
            if (fileInfo) {
                fileInfo.classList.add('hidden'); // Hide file info
            }
            
            // Clear filename display for dokumen
            if (docType === 'dokumen') {
                const display = document.getElementById('dokumen_filename');
                if (display) {
                    display.textContent = '';
                }
            }
        }

        // File upload helper for dokumen kegiatan
        function updateDokumenFileName(inputId, displayId) {
            const input = document.getElementById(inputId);
            const display = document.getElementById(displayId);

            if (input.files.length > 0) {
                const file = input.files[0];
                const fileSize = (file.size / 1024 / 1024).toFixed(2);

                // Check file size (max 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 10MB.');
                    input.value = '';
                    return;
                }

                display.textContent = `${file.name} (${fileSize} MB)`;
            } else {
                display.textContent = '';
            }
        }

        // Delete functions
        function deleteBuktiDukung(id, documentName) {
            const modal = document.getElementById('deleteModal');
            const modalContent = document.getElementById('deleteModalContent');
            const message = document.getElementById('deleteMessage');
            const confirmBtn = document.getElementById('confirmDeleteBtn');

            message.textContent = `Apakah Anda yakin ingin menghapus "${documentName}"?`;

            confirmBtn.onclick = function() {
                // Create and submit delete form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('bukti.dukung.destroy', ':id') }}`.replace(':id', id);

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            };

            // Show modal
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function deleteDokumenKegiatan(id, documentName) {
            const modal = document.getElementById('deleteModal');
            const modalContent = document.getElementById('deleteModalContent');
            const message = document.getElementById('deleteMessage');
            const confirmBtn = document.getElementById('confirmDeleteBtn');

            message.textContent = `Apakah Anda yakin ingin menghapus dokumen "${documentName}"?`;

            confirmBtn.onclick = function() {
                // Create and submit delete form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('dokumen.kegiatan.destroy', ':id') }}`.replace(':id', id);

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            };

            // Show modal
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            const modalContent = document.getElementById('deleteModalContent');

            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        function showEditModal(id, currentName, documentType) {
            const modal = document.getElementById('editModal');
            const modalContent = document.getElementById('modalContent');

            // Show modal
            modal.classList.remove('hidden');

            // Animate modal appearance
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);

            // Set form data
            document.getElementById('nama_dokumen').value = currentName;
            document.getElementById('editNameForm').action = `{{ route('bukti.dukung.update.name', ':id') }}`.replace(':id', id);
            document.getElementById('editDokumenFileForm').action = `{{ route('bukti.dukung.update.file', ':id') }}`.replace(':id', id);
            document.getElementById('editDocumentId').value = id;

            // Set file type restrictions based on document type
            const fileInput = document.getElementById('editFileInput');
            const fileTypeInfo = document.getElementById('fileTypeInfo');

            // All document types use the same file formats now
            fileInput.accept = '.pdf,.doc,.docx,.xls,.xlsx';
            fileTypeInfo.textContent = 'PDF, DOC, DOCX, XLS, XLSX • Max 10MB';

            // Reset to first tab
            switchTab('name');
        }

        function showEditDokumenModal(id, currentName) {
            const modal = document.getElementById('editModal');
            const modalContent = document.getElementById('modalContent');

            // Show modal
            modal.classList.remove('hidden');

            // Animate modal appearance
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);

            // Set form data for dokumen kegiatan
            document.getElementById('nama_dokumen').value = currentName;
            document.getElementById('editNameForm').action = `{{ route('dokumen.kegiatan.update.name', ':id') }}`.replace(':id', id);
            document.getElementById('editDokumenFileForm').action = `{{ route('dokumen.kegiatan.update.file', ':id') }}`.replace(':id', id);
            document.getElementById('editDocumentId').value = id;

            // Set file type restrictions for dokumen kegiatan
            const fileInput = document.getElementById('editFileInput');
            const fileTypeInfo = document.getElementById('fileTypeInfo');

            fileInput.accept = '.pdf,.doc,.docx,.xls,.xlsx';
            fileTypeInfo.textContent = 'PDF, DOC, DOCX, XLS, XLSX • Max 10MB';

            // Reset to first tab
            switchTab('name');
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            const modalContent = document.getElementById('modalContent');

            // Animate modal disappearance
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function switchTab(tabName) {
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
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });

        // File input change handler with validation
        document.getElementById('editFileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const maxSize = 10; // 10MB max
                
                if (file.size > maxSize * 1024 * 1024) {
                    alert(`Ukuran file terlalu besar. Maksimal ${maxSize}MB.`);
                    e.target.value = '';
                    return;
                }
                
                console.log(`Selected file: ${file.name}, Size: ${fileSize}MB`);
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
                    
                    // Validate file size again before submit
                    const file = fileInput.files[0];
                    const maxSize = 10; // 10MB max
                    if (file.size > maxSize * 1024 * 1024) {
                        e.preventDefault();
                        alert(`Ukuran file terlalu besar. Maksimal ${maxSize}MB.`);
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
            
            // Handle other forms with generic loading
            const otherForms = document.querySelectorAll('form:not(#editNameForm):not(#editDokumenFileForm)');
            otherForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Show global loading overlay when form is submitted
                    if (typeof window.showGlobalLoading === 'function') {
                        window.showGlobalLoading('Memproses...');
                    }
                });
            });
        });
    </script>
@endsection
