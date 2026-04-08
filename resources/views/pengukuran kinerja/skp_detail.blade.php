@extends('components.master')

@section('title', 'Detail SKP')

@section('content')
    @include('components.breadcrumbs')

    <div class="container mx-auto py-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Detail Sasaran Kinerja Pegawai</h1>
                <p class="text-sm text-gray-600">Pemantauan kelengkapan pengisian SKP bulanan dan tahunan</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Pegawai</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $total_pegawai ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">SKP Lengkap</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $skp_lengkap ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Belum Lengkap</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $skp_belum_lengkap ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <!-- Filter Section -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Pegawai</h3>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="searchPegawai" placeholder="Cari nama pegawai..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Table -->
                        <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Pegawai</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jan</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Feb</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Mar</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Apr</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Mei</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jun</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jul</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Agu</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Sep</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Okt</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Nov</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Des</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">SKP Tahunan</th>

                                    </tr>
                                </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($daftar_pegawai ?? [] as $pegawai)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                                <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">{{ substr($pegawai->nama ?? 'AP', 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ $pegawai->nama ?? 'Ahmad Pratama' }}</div>
                                            <div class="text-sm text-gray-500">NIP: {{ $pegawai->nip ?? '198001012005011001' }}</div>
                                            <div class="text-sm text-gray-500">{{ $pegawai->bidang ?? 'Unit Kerja' }} - {{ $pegawai->jabatan_label ?? 'Anggota Umum' }}</div>
                                                </div>
                                                </div>
                                            </td>
                                
                                <!-- Kolom Bulan Jan-Des -->
                                @php
                                    $skpBulananData = $pegawai->skp_bulanan_data ?? [];
                                    $currentMonth = date('n'); // Bulan saat ini (1-12)
                                @endphp
                                @for($bulan = 1; $bulan <= 12; $bulan++)
                                    <td class="px-3 py-4 text-center">
                                        @php
                                            $isCompleted = in_array($bulan, $skpBulananData);
                                            $isPast = $bulan < $currentMonth;
                                            $isCurrent = $bulan == $currentMonth;
                                            $bulanNama = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'][$bulan];
                                        @endphp
                                        
                                        <div class="flex flex-col items-center space-y-1">
                                            @if($isCompleted)
                                                <!-- Sudah diisi - Centang hijau dengan button download dan view -->
                                                <svg class="w-5 h-5 text-green-600 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <button onclick="viewSkpDetail('{{ $pegawai->nip ?? '198001012005011001' }}', '{{ $pegawai->nama ?? 'Ahmad Pratama' }}', {{ $bulan }}, '{{ $bulanNama }}', 'bulanan')"
                                                        class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors text-xs"
                                                        title="Lihat Detail SKP {{ $bulanNama }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            @elseif($isPast)
                                                <!-- Sudah lewat tapi belum diisi - Silang merah dan button unggah -->
                                                <svg class="w-5 h-5 text-red-600 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                                <button onclick="openUploadModal('{{ $pegawai->nip ?? '198001012005011001' }}', '{{ $pegawai->nama ?? 'Ahmad Pratama' }}', {{ $bulan }}, '{{ $bulanNama }}', 'bulanan')" 
                                                        class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors text-xs mt-1"
                                                        title="Unggah SKP {{ $bulanNama }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 4.414V13a1 1 0 11-2 0V4.414L7.707 5.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            @elseif($isCurrent)
                                                <!-- Bulan ini - Bulat kuning -->
                                                <div class="w-3 h-3 bg-yellow-400 rounded-full mx-auto"></div>
                                            @else
                                                <!-- Belum saatnya - Kosong -->
                                                <div class="w-3 h-3 bg-gray-200 rounded-full mx-auto"></div>
                                            @endif
                                        </div>
                                    </td>
                                @endfor
                                
                                <!-- SKP Tahunan -->
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $skp_tahunan = $pegawai->skp_tahunan_status ?? 'belum';
                                        $isYearEnd = date('n') >= 12; // Sudah bulan Desember atau lewat
                                    @endphp
                                    
                                    @if($skp_tahunan == 'lengkap')
                                        <!-- Sudah diisi - Centang hijau dengan button view dan download -->
                                        <div class="flex flex-col items-center space-y-1">
                                            <svg class="w-5 h-5 text-green-600 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            <button onclick="viewSkpDetail('{{ $pegawai->nip ?? '198001012005011001' }}', '{{ $pegawai->nama ?? 'Ahmad Pratama' }}', 0, 'Tahunan', 'tahunan')"
                                                    class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors text-xs"
                                                    title="Lihat Detail SKP Tahunan">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    @elseif($isYearEnd && $skp_tahunan == 'belum')
                                        <!-- Sudah akhir tahun tapi belum diisi - Silang merah dan button unggah -->
                                        <div class="flex flex-col items-center space-y-1">
                                            <svg class="w-5 h-5 text-red-600 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            <button onclick="openUploadModal('{{ $pegawai->nip ?? '198001012005011001' }}', '{{ $pegawai->nama ?? 'Ahmad Pratama' }}', 0, 'Tahunan', 'tahunan')" 
                                                    class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors text-xs"
                                                    title="Unggah SKP Tahunan">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 4.414V13a1 1 0 11-2 0V4.414L7.707 5.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    @else
                                        <!-- Belum saatnya atau dalam proses - Bulat abu-abu -->
                                        <div class="w-3 h-3 bg-gray-200 rounded-full mx-auto"></div>
                                                    @endif
                                            </td>
                                

                                        </tr>
                                    @empty
                            <tr>
                                <td colspan="14" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data pegawai</h3>
                                        <p class="text-gray-500">Data pegawai untuk SKP ini belum tersedia</p>
                                    </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
            @if(isset($daftar_pegawai) && $daftar_pegawai->count() > 0)
                <div class="bg-white px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Menampilkan <span class="font-medium">1</span> hingga <span class="font-medium">{{ $daftar_pegawai->count() }}</span> dari <span class="font-medium">{{ $daftar_pegawai->count() }}</span> pegawai
                        </div>
                                <div class="flex items-center space-x-2">
                            <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50" disabled>
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                            <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-600 text-sm font-medium text-white">1</button>
                            <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50" disabled>
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 5.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Reminder Modal -->
    <div id="reminderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
    </div>
                <h3 class="text-lg font-medium text-gray-900 text-center mt-4">Kirim Reminder SKP</h3>
                <div class="mt-4">
                    <p class="text-sm text-gray-600 text-center">Kirim reminder kepada:</p>
                    <p id="reminderEmployeeName" class="text-base font-semibold text-gray-900 text-center mt-1"></p>
                    <p id="reminderEmployeeNip" class="text-sm text-gray-500 text-center"></p>
    </div>
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pesan Reminder</label>
                    <textarea id="reminderMessage" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Masukkan pesan reminder...">Halo, ini adalah pengingat untuk melengkapi SKP Anda. Mohon segera upload dokumen SKP yang belum lengkap. Terima kasih.</textarea>
                    </div>
                <div class="mt-6 flex space-x-3">
                    <button onclick="sendReminder()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Kirim Email
                    </button>
                    <button onclick="closeReminderModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-60 mx-auto p-5 border w-1/4 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 text-center mt-4">Unggah SKP</h3>
                <div class="mt-4">
                    <p class="text-sm text-gray-600 text-center">Unggah untuk:</p>
                    <p id="uploadEmployeeName" class="text-base font-semibold text-gray-900 text-center mt-1"></p>
                    <p id="uploadEmployeeNip" class="text-sm text-gray-500 text-center"></p>
                    <p id="uploadPeriod" class="text-sm font-medium text-blue-600 text-center mt-2"></p>
                </div>
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">File SKP (PDF)</label>
                    <div class="relative">
                        <input type="file" id="skpFile" name="skp_file" accept=".pdf" required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">File harus berformat PDF dan maksimal 5MB</p>
                    <div id="fileValidationMessage" class="mt-2 text-sm text-red-600 hidden">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span>Mohon pilih file terlebih dahulu</span>
                        </div>
                    </div>
                </div>
                </form>
                <div class="mt-6 flex space-x-3">
                    <button onclick="uploadSkp()" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Unggah SKP
                    </button>
                    <button onclick="closeUploadModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SKP Detail Modal -->
    <div id="skpDetailModal" class="fixed inset-0 bg-black bg-opacity-60 overflow-y-auto h-full w-full hidden backdrop-blur-sm flex items-center justify-center">
        <div class="relative mx-auto p-0 w-full max-w-2xl m-4">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 h-10 w-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Detail Dokumen SKP</h3>
                                <p id="skpDetailPeriod" class="text-sm text-red-100"></p>
                            </div>
                        </div>
                        <button onclick="closeSkpDetailModal()" class="text-white hover:text-red-200 transition-colors p-1 rounded-lg hover:bg-white hover:bg-opacity-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="p-6 space-y-6">
                    <!-- Employee Info Card -->
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Informasi Pegawai
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nama Pegawai</label>
                                <p id="skpDetailEmployeeName" class="text-sm font-semibold text-gray-900"></p>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">NIP</label>
                                <p id="skpDetailEmployeeNip" class="text-sm text-gray-700 font-mono"></p>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Unit Kerja</label>
                                <p id="skpDetailEmployeeUnit" class="text-sm text-gray-700"></p>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Jabatan</label>
                                <p id="skpDetailEmployeeJabatan" class="text-sm text-gray-700"></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Document Info Card -->
                    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                        <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Informasi Dokumen
                        </h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nama File</label>
                                <p id="skpDetailFileName" class="text-sm text-gray-700 break-all font-mono bg-gray-50 px-2 py-1 rounded"></p>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tanggal Upload</label>
                                    <p id="skpDetailUploadDate" class="text-sm text-gray-700"></p>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Diupload Oleh</label>
                                    <p id="skpDetailUploadedBy" class="text-sm text-gray-700 font-medium"></p>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ukuran File</label>
                                    <p id="skpDetailFileSize" class="text-sm text-gray-700"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 border-t border-gray-200">
                    <button id="skpDetailViewBtn" onclick="viewCurrentSkpDocument()" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Lihat Dokumen
                    </button>
                    <button id="skpDetailDownloadBtn" onclick="downloadCurrentSkp()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m-6 8h8a2 2 0 002-2V7a2 2 0 00-2-2H8a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Download
                    </button>
                    <button onclick="closeSkpDetailModal()" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-all duration-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentEmployee = {};
        let currentUploadData = {};
        let currentSkpDetail = {};

        function openReminderModal(nama, nip) {
            currentEmployee = { nama, nip };
            document.getElementById('reminderEmployeeName').textContent = nama;
            document.getElementById('reminderEmployeeNip').textContent = `NIP: ${nip}`;
            document.getElementById('reminderModal').classList.remove('hidden');
        }

        function closeReminderModal() {
            document.getElementById('reminderModal').classList.add('hidden');
            currentEmployee = {};
        }

        function openUploadModal(nip, nama, bulan, bulanNama, tipe) {
            currentUploadData = { nip, nama, bulan, bulanNama, tipe };
            document.getElementById('uploadEmployeeName').textContent = nama;
            document.getElementById('uploadEmployeeNip').textContent = `NIP: ${nip}`;
            
            if (tipe === 'bulanan') {
                document.getElementById('uploadPeriod').textContent = `SKP Bulanan - ${bulanNama}`;
            } else {
                document.getElementById('uploadPeriod').textContent = `SKP Tahunan`;
            }
            
            // Reset form and validation message
            document.getElementById('uploadForm').reset();
            document.getElementById('fileValidationMessage').classList.add('hidden');
            document.getElementById('uploadModal').classList.remove('hidden');
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            currentUploadData = {};
        }

        function uploadSkp() {
            const fileInput = document.getElementById('skpFile');
            const validationMessage = document.getElementById('fileValidationMessage');
            
            // Reset validation message
            validationMessage.classList.add('hidden');
            
            if (!fileInput.files.length) {
                showModal('warning', 'File Belum Dipilih', 'Mohon pilih file SKP terlebih dahulu sebelum mengunggah');
                return;
            }
            
            const file = fileInput.files[0];
            
            // Validasi file
            if (file.type !== 'application/pdf') {
                showError('❌ File harus berformat PDF! File yang dipilih: ' + file.type);
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) { // 5MB
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                showError(`❌ Ukuran file terlalu besar! Maksimal 5MB, file Anda: ${fileSizeMB}MB`);
                return;
            }
            
            // Prepare form data
            const formData = new FormData();
            formData.append('skp_file', file);
            formData.append('nip', currentUploadData.nip);
            formData.append('kegiatan_id', {{ $skp_info->id }});
            formData.append('_token', '{{ csrf_token() }}');
            
            // Add bulan for bulanan SKP
            if (currentUploadData.tipe === 'bulanan') {
                formData.append('bulan', currentUploadData.bulan);
            }
            
            // Determine upload endpoint
            const uploadUrl = currentUploadData.tipe === 'bulanan' ? 
                '{{ route("skp.upload.bulanan") }}' : 
                '{{ route("skp.upload.tahunan") }}';
            
            // Show loading state
            const loadingBtn = event.target;
            const originalText = loadingBtn.innerHTML;
            loadingBtn.innerHTML = '<svg class="animate-spin w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Mengunggah...';
            loadingBtn.disabled = true;
            
            // Upload file
            fetch(uploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                loadingBtn.innerHTML = originalText;
                loadingBtn.disabled = false;
                
                if (data.success) {
                    closeUploadModal();
                    
                    // Show success notification using response message from server
                    showSuccess(data.message || 'File berhasil diunggah');
                    
                    // Refresh halaman
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showError(data.message || 'Gagal mengupload file');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                loadingBtn.innerHTML = originalText;
                loadingBtn.disabled = false;
                showError('Gagal mengupload file - terjadi kesalahan');
            });
        }

        function sendReminder() {
            const message = document.getElementById('reminderMessage').value;
            
            if (!message.trim()) {
                alert('Mohon masukkan pesan reminder');
                        return;
            }

            // Simulasi pengiriman email
            const loadingBtn = event.target;
            const originalText = loadingBtn.innerHTML;
            loadingBtn.innerHTML = '<svg class="animate-spin w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Mengirim...';
            loadingBtn.disabled = true;

            setTimeout(() => {
                loadingBtn.innerHTML = originalText;
                loadingBtn.disabled = false;
                closeReminderModal();
                
                // Show success notification using global system
                showSuccess(`Reminder berhasil dikirim ke ${currentEmployee.nama}`);
            }, 2000);
        }

        // Removed local showNotification function - now using global notification system from master.blade.php

        function downloadSkpBulananPerBulan(nip, nama, bulan, bulanNama) {
            // Show global loading overlay
            if (typeof window.showGlobalLoading === 'function') {
                window.showGlobalLoading(`Menyiapkan download SKP ${bulanNama}...`);
            }
            
            // Create download URL using Laravel route
            const downloadUrl = `{{ route('skp.download.bulanan.month', ['nip' => ':nip', 'bulan' => ':bulan']) }}`
                .replace(':nip', nip)
                .replace(':bulan', bulan);
            
            // Create temporary download link
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = `SKP_Bulanan_${nama}_${bulan.toString().padStart(2, '0')}_{{ date('Y') }}.pdf`;
            link.style.display = 'none';
            
            document.body.appendChild(link);
            
            // Handle download completion
            link.addEventListener('click', function() {
                setTimeout(() => {
                    // Hide global loading overlay
                    if (typeof window.hideGlobalLoading === 'function') {
                        window.hideGlobalLoading();
                    }
                }, 1000);
            });
            
            // Handle download errors
            link.addEventListener('error', function() {
                // Hide global loading overlay
                if (typeof window.hideGlobalLoading === 'function') {
                    window.hideGlobalLoading();
                }
                // Show error notification
                if (typeof window.showError === 'function') {
                    window.showError(`Gagal mendownload SKP ${bulanNama}`);
                }
            });
            
            link.click();
            document.body.removeChild(link);
        }

        function downloadSkpTahunan(nip, nama) {
            // Show global loading overlay
            if (typeof window.showGlobalLoading === 'function') {
                window.showGlobalLoading('Menyiapkan download SKP Tahunan...');
            }
            
            // Create download URL using Laravel route
            const downloadUrl = `{{ route('skp.download.tahunan', ['nip' => ':nip']) }}`
                .replace(':nip', nip);
            
            // Create temporary download link
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = `SKP_Tahunan_${nama}_{{ date('Y') }}.pdf`;
            link.style.display = 'none';
            
            document.body.appendChild(link);
            
            // Handle download completion
            link.addEventListener('click', function() {
                setTimeout(() => {
                    // Hide global loading overlay
                    if (typeof window.hideGlobalLoading === 'function') {
                        window.hideGlobalLoading();
                    }
                }, 1000);
            });
            
            // Handle download errors
            link.addEventListener('error', function() {
                // Hide global loading overlay
                if (typeof window.hideGlobalLoading === 'function') {
                    window.hideGlobalLoading();
                }
                // Show error notification
                if (typeof window.showError === 'function') {
                    window.showError('Gagal mendownload SKP Tahunan');
                }
            });
            
            link.click();
            document.body.removeChild(link);
        }

        function viewSkpDetail(nip, nama, bulan, bulanNama, jenis) {
            // Find employee data from the current page data
            const pegawaiData = @json($daftar_pegawai ?? []);
            const employee = pegawaiData.find(emp => emp.nip === nip);
            
            // Get SKP documents data
            const skpDocuments = @json($skp_documents ?? []);
            
            // Find the actual SKP document
            let skpDocument = null;
            if (employee && employee.id) {
                skpDocument = skpDocuments.find(skp => {
                    return skp.user_id === employee.id && 
                           skp.jenis === jenis && 
                           (jenis === 'tahunan' || skp.bulan === bulan);
                });
            }
            
            // Store current SKP detail for download and view functions
            currentSkpDetail = {
                nip: nip,
                nama: nama,
                bulan: bulan,
                bulanNama: bulanNama,
                jenis: jenis,
                skpId: skpDocument?.id || null,
                webViewLink: skpDocument?.webViewLink || null,
                namaFile: skpDocument?.nama_file || null,
                uploadedAt: skpDocument?.uploaded_at || null,
                uploadedBy: skpDocument?.uploader?.nama || nama,
                fileSize: '2.5 MB' // This should come from actual file data
            };
            
            // Populate modal with employee data
            document.getElementById('skpDetailEmployeeName').textContent = nama || '-';
            document.getElementById('skpDetailEmployeeNip').textContent = nip || '-';
            document.getElementById('skpDetailEmployeeUnit').textContent = employee?.bidang || '-';
            document.getElementById('skpDetailEmployeeJabatan').textContent = employee?.jabatan || '-';
            
            // Set period info
            let periodText = '';
            if (jenis === 'bulanan') {
                periodText = `SKP Bulanan - ${bulanNama} {{ date('Y') }}`;
            } else {
                periodText = `SKP Tahunan - {{ date('Y') }}`;
            }
            document.getElementById('skpDetailPeriod').textContent = periodText;
            
            // Use actual document info if available
            if (skpDocument) {
                document.getElementById('skpDetailFileName').textContent = skpDocument.nama_file || 
                    (jenis === 'bulanan' ? 
                        `SKP_Bulanan_${nama}_${bulan.toString().padStart(2, '0')}_{{ date('Y') }}.pdf` :
                        `SKP_Tahunan_${nama}_{{ date('Y') }}.pdf`);
                
                if (skpDocument.uploaded_at) {
                    const uploadDate = new Date(skpDocument.uploaded_at);
                    document.getElementById('skpDetailUploadDate').textContent = uploadDate.toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } else {
                    document.getElementById('skpDetailUploadDate').textContent = '-';
                }
                
                document.getElementById('skpDetailUploadedBy').textContent = currentSkpDetail.uploadedBy;
                document.getElementById('skpDetailFileSize').textContent = currentSkpDetail.fileSize;
            } else {
                // Fallback for missing document data
                document.getElementById('skpDetailFileName').textContent = jenis === 'bulanan' ? 
                    `SKP_Bulanan_${nama}_${bulan.toString().padStart(2, '0')}_{{ date('Y') }}.pdf` :
                    `SKP_Tahunan_${nama}_{{ date('Y') }}.pdf`;
                document.getElementById('skpDetailUploadDate').textContent = '-';
                document.getElementById('skpDetailUploadedBy').textContent = nama;
                document.getElementById('skpDetailFileSize').textContent = '-';
            }
            
            // Load document preview (placeholder)
            const previewFrame = document.getElementById('skpPreviewFrame');
            const previewError = document.getElementById('skpPreviewError');
            
            // For demo purposes, show preview error (in real implementation, load actual PDF)
            if (previewFrame) previewFrame.style.display = 'none';
            if (previewError) previewError.classList.remove('hidden');
            
            // Show modal immediately without loading overlay
            document.getElementById('skpDetailModal').classList.remove('hidden');
        }
        
        function closeSkpDetailModal() {
            document.getElementById('skpDetailModal').classList.add('hidden');
            document.getElementById('skpPreviewFrame').src = '';
            currentSkpDetail = {};
        }
        
        function downloadCurrentSkp() {
            if (!currentSkpDetail.nip) {
                if (typeof window.showError === 'function') {
                    window.showError('Data SKP tidak valid!');
                }
                return;
            }
            
            if (currentSkpDetail.jenis === 'bulanan') {
                downloadSkpBulananPerBulan(currentSkpDetail.nip, currentSkpDetail.nama, currentSkpDetail.bulan, currentSkpDetail.bulanNama);
            } else if (currentSkpDetail.jenis === 'tahunan') {
                downloadSkpTahunan(currentSkpDetail.nip, currentSkpDetail.nama);
            }
        }
        
        function viewCurrentSkpDocument() {
            if (!currentSkpDetail.skpId) {
                if (typeof window.showError === 'function') {
                    window.showError('Dokumen SKP tidak ditemukan!');
                }
                return;
            }
            
            // Use the actual SKP ID with the existing skp.view route
            const viewUrl = `{{ route('skp.view', ':id') }}`.replace(':id', currentSkpDetail.skpId);
            
            if (viewUrl) {
                // Open document in new tab using webViewLink (Google Drive)
                window.open(viewUrl, '_blank');
                
                // Show success notification
                if (typeof window.showSuccess === 'function') {
                    window.showSuccess('Dokumen SKP dibuka di tab baru');
                }
            } else {
                if (typeof window.showError === 'function') {
                    window.showError('URL dokumen tidak tersedia');
                }
            }
        }

        // Search functionality for employee table
        document.getElementById('searchPegawai').addEventListener('input', function(e) {
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

        // Close modal when clicking outside
        document.getElementById('reminderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReminderModal();
            }
        });

        document.getElementById('uploadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUploadModal();
            }
        });

        document.getElementById('skpDetailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSkpDetailModal();
            }
        });

        // Close modals with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const reminderModal = document.getElementById('reminderModal');
                const uploadModal = document.getElementById('uploadModal');
                const skpDetailModal = document.getElementById('skpDetailModal');
                
                if (reminderModal && !reminderModal.classList.contains('hidden')) {
                    closeReminderModal();
                }
                if (uploadModal && !uploadModal.classList.contains('hidden')) {
                    closeUploadModal();
                }
                if (skpDetailModal && !skpDetailModal.classList.contains('hidden')) {
                    closeSkpDetailModal();
                }
            }
        });
    </script>
@endsection
