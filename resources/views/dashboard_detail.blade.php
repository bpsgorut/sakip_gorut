@extends('components.master')

@section('title', 'Detail Dashboard')

@section('content')
    @include('components.breadcrumbs')
    
    <!-- Custom Color Theme - Consistent with Dashboard -->
    <style>
        :root {
            --primary-50: #fff5f5;
            --primary-100: #fed7d7;
            --primary-200: #feb2b2;
            --primary-500: #f56565;
            --primary-600: #e53e3e;
            --primary-700: #c53030;
            --primary-900: #63171b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-700: #374151;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
    
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $komponen_title }}</h1>
                <div class="flex items-center mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">
                        Detail Komponen
                    </span>
                    <p class="text-sm text-gray-500">Informasi detail untuk komponen {{ strtolower($komponen_title) }}</p>
                </div>
            </div>
            <div class="flex items-center mt-4 md:mt-0 space-x-4">
                <div class="flex items-center bg-red-50 p-3 rounded-xl shadow-sm">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="ml-2 text-gray-700">Tahun {{ date('Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Sub Komponen Sections -->
        @foreach ($sub_komponen_list as $subKomponen)
            @if (isset($kegiatan_data[$subKomponen]) && count($kegiatan_data[$subKomponen]) > 0)
                <div class="mb-8">
                    <!-- Sub Komponen Header -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="bg-red-100 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-800">{{ $subKomponen }}</h2>
                        </div>
                        <div class="h-1 w-20 bg-red-500 rounded"></div>
                    </div>

                    <!-- Kegiatan Table -->
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-red-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 40%">
                                            Kegiatan</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%">
                                            Tenggat Waktu</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%">
                                            Progress</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($kegiatan_data[$subKomponen] as $kegiatan)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    @if(isset($kegiatan['is_form']) && $kegiatan['is_form'])
                                                        <div class="bg-blue-100 p-1.5 rounded-lg mr-3">
                                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="bg-green-100 p-1.5 rounded-lg mr-3">
                                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $kegiatan['nama_kegiatan'] }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            @if(isset($kegiatan['is_form']) && $kegiatan['is_form'])
                                                                Form Input
                                                            @else
                                                                {{ $kegiatan['uploaded_count'] }}/{{ $kegiatan['total_required'] }}
                                                                dokumen terupload
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($kegiatan['tanggal_berakhir'])->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-20 bg-gray-200 rounded-full h-2.5 mr-3">
                                                        <div class="bg-red-500 h-2.5 rounded-full transition-all duration-300"
                                                            style="width: {{ $kegiatan['progress_percentage'] }}%"></div>
                                                    </div>
                                                    <span
                                                        class="text-sm font-medium text-gray-700">{{ $kegiatan['progress_percentage'] }}%</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if(isset($kegiatan['is_form']) && $kegiatan['is_form'])
                                                    @if ($kegiatan['status'] == 'SUDAH INPUT')
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                            SUDAH INPUT
                                                        </span>
                                                    @else
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                            BELUM INPUT
                                                        </span>
                                                    @endif
                                                @else
                                                    @if ($kegiatan['status'] == 'SUDAH DIUNGGAH')
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                            SUDAH DIUNGGAH
                                                        </span>
                                                    @elseif($kegiatan['status'] == 'SEBAGIAN DIUNGGAH')
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            SEBAGIAN DIUNGGAH
                                                        </span>
                                                    @else
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                            BELUM DIUNGGAH
                                                        </span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                @if(isset($kegiatan['is_form']) && $kegiatan['is_form'])
                                                    @php
                                                        $user = auth()->user();
                                                        $isExpired = isset($kegiatan['is_expired']) && $kegiatan['is_expired'];
                                                        
                                                        // Logika role-based access control
                                                        $isLocked = false;
                                                        $lockReason = '';
                                                        
                                                        if ($user->isAnggotaTim()) {
                                                            // Anggota tim: semua button input terkunci tanpa pengecualian
                                                            $isLocked = true;
                                                            $lockReason = 'Akses terbatas untuk Anggota Tim';
                                                        } elseif ($user->isKetuaTim()) {
                                                            // Ketua tim: terkunci kecuali untuk realisasi FRA dengan logika Admin
                                                            if (str_contains($kegiatan['id'], 'fra_realisasi_tw')) {
                                                                // Ketua Tim untuk realisasi FRA: gunakan logika yang sama dengan Admin
                                                                $currentDate = now();
                                                                $triwulanNumber = (int) substr($kegiatan['id'], -1);
                                                                $triwulanStartDates = [
                                                                    1 => date('Y') . '-01-01',
                                                                    2 => date('Y') . '-04-01', 
                                                                    3 => date('Y') . '-07-01',
                                                                    4 => date('Y') . '-10-01'
                                                                ];
                                                                $triwulanEndDates = [
                                                                    1 => date('Y') . '-03-31',
                                                                    2 => date('Y') . '-06-30',
                                                                    3 => date('Y') . '-09-30',
                                                                    4 => date('Y') . '-12-31'
                                                                ];
                                                                
                                                                if (isset($triwulanStartDates[$triwulanNumber]) && isset($triwulanEndDates[$triwulanNumber])) {
                                                                    $startDate = $triwulanStartDates[$triwulanNumber];
                                                                    $endDate = $triwulanEndDates[$triwulanNumber];
                                                                    
                                                                    if ($currentDate->lt($startDate)) {
                                                                        $isLocked = true;
                                                                        $lockReason = 'Belum waktunya untuk triwulan ini';
                                                                    } elseif ($currentDate->gt($endDate)) {
                                                                        $isLocked = true;
                                                                        $lockReason = 'Waktu untuk triwulan ini sudah berakhir';
                                                                    } else {
                                                                        $isLocked = false;
                                                                    }
                                                                } else {
                                                                    $isLocked = true;
                                                                    $lockReason = 'Triwulan tidak valid';
                                                                }
                                                            } else {
                                                                // Selain realisasi FRA, semua terkunci untuk Ketua Tim
                                                                $isLocked = true;
                                                                $lockReason = 'Akses terbatas untuk Ketua Tim';
                                                            }
                                                        } elseif ($user->isAdmin()) {
                                                            // Admin: terkunci jika expired atau belum waktunya
                                                            if ($isExpired) {
                                                                $isLocked = true;
                                                                $lockReason = 'Periode input sudah berakhir';
                                                            }
                                                            // Cek apakah triwulan belum waktunya
                                                            if (str_contains($kegiatan['id'], 'fra_realisasi_tw')) {
                                                                $currentDate = now();
                                                                $triwulanNumber = (int) substr($kegiatan['id'], -1);
                                                                $triwulanStartDates = [
                                                                    1 => date('Y') . '-01-01',
                                                                    2 => date('Y') . '-04-01', 
                                                                    3 => date('Y') . '-07-01',
                                                                    4 => date('Y') . '-10-01'
                                                                ];
                                                                if (isset($triwulanStartDates[$triwulanNumber]) && $currentDate->lt($triwulanStartDates[$triwulanNumber])) {
                                                                    $isLocked = true;
                                                                    $lockReason = 'Belum waktunya untuk triwulan ini';
                                                                }
                                                            }
                                                        } elseif ($user->isSuperAdmin()) {
                                                            // Super admin: hanya terkunci jika triwulan belum waktunya
                                                            if (str_contains($kegiatan['id'], 'fra_realisasi_tw')) {
                                                                $currentDate = now();
                                                                $triwulanNumber = (int) substr($kegiatan['id'], -1);
                                                                $triwulanStartDates = [
                                                                    1 => date('Y') . '-01-01',
                                                                    2 => date('Y') . '-04-01', 
                                                                    3 => date('Y') . '-07-01',
                                                                    4 => date('Y') . '-10-01'
                                                                ];
                                                                if (isset($triwulanStartDates[$triwulanNumber]) && $currentDate->lt($triwulanStartDates[$triwulanNumber])) {
                                                                    $isLocked = true;
                                                                    $lockReason = 'Belum waktunya untuk triwulan ini';
                                                                }
                                                            }
                                                        }
                                                        
                                                        $buttonClass = $isLocked 
                                                            ? 'inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed'
                                                            : 'inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition-colors duration-200';
                                                    @endphp
                                                    
                                                    @if(str_contains($kegiatan['id'], 'fra_target'))
                                                        @if($isLocked)
                                                            <span class="{{ $buttonClass }}" title="{{ $lockReason }}">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                                </svg>
                                                                Input
                                                            </span>
                                                        @else
                                                            <a href="{{ route('fra.index') }}" class="{{ $buttonClass }}">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                </svg>
                                                                Input
                                                            </a>
                                                        @endif
                                                    @elseif(str_contains($kegiatan['id'], 'fra_realisasi'))
                                                        @if($isLocked)
                                                            <span class="{{ $buttonClass }}" title="{{ $lockReason }}">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                                </svg>
                                                                Input
                                                            </span>
                                                        @else
                                                            <a href="{{ route('fra.index') }}" class="{{ $buttonClass }}">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                </svg>
                                                                Input
                                                            </a>
                                                        @endif
                                                    @elseif(str_contains($kegiatan['id'], 'pk_target'))
                                                        @if($isLocked)
                                                            <span class="{{ $buttonClass }}" title="{{ $lockReason }}">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                                </svg>
                                                                Input
                                                            </span>
                                                        @else
                                                            <a href="{{ route('manajemen.pk') }}" class="{{ $buttonClass }}">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                </svg>
                                                                Input
                                                            </a>
                                                        @endif
                                                    @endif
                                                @else
                                                    @php
                                                        // Untuk dokumen upload, cek apakah ini Reward & Punishment atau Monitoring Capaian Kinerja
                                                        $detailRoute = route('detail', ['id' => $kegiatan['id'], 'year' => date('Y')]);
                                                        
                                                        // Jika ini adalah Reward & Punishment, gunakan route yang benar
                                                        if ($subKomponen === 'Reward & Punishment') {
                                                            $detailRoute = route('reward.punishment.detail', ['id' => str_replace(['_penetapan', '_tw1', '_tw2', '_tw3', '_tw4'], '', $kegiatan['id'])]);
                                                        }
                                                        // Jika ini adalah Monitoring Capaian Kinerja, gunakan route detail biasa
                                                        elseif ($subKomponen === 'Monitoring Capaian Kinerja') {
                                                            $detailRoute = route('detail', ['id' => str_replace(['_tw1', '_tw2', '_tw3', '_tw4'], '', $kegiatan['id']), 'year' => date('Y')]);
                                                        }
                                                    @endphp
                                                    
                                                    <a href="{{ $detailRoute }}"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Detail
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
        <!-- Empty State -->
        @if (empty($kegiatan_data) || collect($kegiatan_data)->flatten(1)->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Kegiatan</h3>
                <p class="text-gray-500">Belum ada kegiatan yang tersedia untuk komponen {{ strtolower($komponen_title) }}
                    tahun ini.</p>
            </div>
        @endif

        <!-- Back Button -->
        <div class="mt-8">
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-6 py-3 bg-red-600 text-white text-sm font-medium rounded-xl hover:bg-red-700 transition-all duration-200 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
@endsection
