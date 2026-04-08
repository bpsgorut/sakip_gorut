@extends('components.master')

@section('title', 'Detail SKP')

@section('content')
    @include('components.breadcrumbs')

    <div class="container mx-auto py-6">
        <!-- Header Card with Refined Background -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="relative">
                <!-- Background with Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-700 opacity-40"></div>
                <img class="w-full h-40 object-cover" src="{{ asset('img/bg4.jpg') }}" alt="">

                <!-- Content Overlay -->
                <div class="absolute inset-0 flex items-center justify-between p-6">
                    <div class="text-white flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white/90">Sistem Kinerja Pegawai (SKP)</h1>
                                <p class="text-white/60 text-sm">Manajemen dokumen SKP bulanan dan tahunan {{ date('Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Summary -->
                    <div class="text-white text-right">
                        @php
                            $uploadedMonthly = isset($skpBulanan) ? $skpBulanan->count() : 0;
                            $uploadedYearly = isset($skpTahunan) && $skpTahunan->count() > 0 ? 1 : 0;
                        @endphp
                        <div class="text-2xl font-bold">{{ $uploadedMonthly + $uploadedYearly }}/13</div>
                        <div class="text-sm text-white/60">Dokumen Terupload</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Tab Headers -->
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button id="tab-bulanan"
                        class="tab-button active border-b-2 border-red-500 py-4 px-1 text-sm font-medium text-red-600"
                        onclick="switchTab('bulanan')">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span>SKP Bulanan</span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $uploadedMonthly }}/12
                            </span>
                        </div>
                    </button>
                    <button id="tab-tahunan"
                        class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                        onclick="switchTab('tahunan')">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span>SKP Tahunan</span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $uploadedYearly ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $uploadedYearly ? 'Selesai' : 'Pending' }}
                            </span>
                        </div>
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- SKP Bulanan Tab -->
                <div id="content-bulanan" class="tab-pane active">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Upload Dokumen SKP Bulanan
                                {{ date('Y') }}</h3>
                            <p class="text-gray-600 text-sm">Upload dokumen SKP setiap bulan sesuai dengan periode yang
                                ditentukan</p>
                        </div>

                        @php
                            $bulanData = [
                                ['name' => 'Januari', 'number' => 1, 'short' => 'Jan'],
                                ['name' => 'Februari', 'number' => 2, 'short' => 'Feb'],
                                ['name' => 'Maret', 'number' => 3, 'short' => 'Mar'],
                                ['name' => 'April', 'number' => 4, 'short' => 'Apr'],
                                ['name' => 'Mei', 'number' => 5, 'short' => 'Mei'],
                                ['name' => 'Juni', 'number' => 6, 'short' => 'Jun'],
                                ['name' => 'Juli', 'number' => 7, 'short' => 'Jul'],
                                ['name' => 'Agustus', 'number' => 8, 'short' => 'Agt'],
                                ['name' => 'September', 'number' => 9, 'short' => 'Sep'],
                                ['name' => 'Oktober', 'number' => 10, 'short' => 'Okt'],
                                ['name' => 'November', 'number' => 11, 'short' => 'Nov'],
                                ['name' => 'Desember', 'number' => 12, 'short' => 'Des'],
                            ];
                            $currentMonth = date('n');
                        @endphp

                        @php
                            // Enhanced color generation function with more professional palette
                            function getMonthCardClasses($isUploaded, $isPast, $isCurrent) {
                                $baseColors = [
                                    'uploaded' => [
                                        'card' => 'bg-emerald-50 border-emerald-200 hover:border-emerald-300',
                                        'icon' => 'text-emerald-700',
                                        'text' => 'text-emerald-900'
                                    ],
                                    'past_due' => [
                                        'card' => 'bg-rose-50 border-rose-200 hover:border-rose-300',
                                        'icon' => 'text-rose-700',
                                        'text' => 'text-rose-900'
                                    ],
                                    'current' => [
                                        'card' => 'bg-blue-50 border-blue-200 hover:border-blue-300 cursor-pointer hover:shadow-md',
                                        'icon' => 'text-blue-700',
                                        'text' => 'text-blue-900'
                                    ],
                                    'future' => [
                                        'card' => 'bg-gray-50 border-gray-200',
                                        'icon' => 'text-gray-400',
                                        'text' => 'text-gray-500'
                                    ]
                                ];

                                if ($isUploaded) return $baseColors['uploaded'];
                                if ($isPast && !$isUploaded) return $baseColors['past_due'];
                                if ($isCurrent) return $baseColors['current'];
                                return $baseColors['future'];
                            }
                        @endphp


                        <!-- Calendar Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach ($bulanData as $bulan)
                                @php
                                    $bulanNumber = $bulan['number'];
                                    // Check if document exists for this month from skps table
                                    $monthlyDocument = null;
                                    $isUploaded = false;
                                    
                                    if (isset($skpBulanan) && $skpBulanan->has($bulanNumber)) {
                                        $monthlyDocument = $skpBulanan->get($bulanNumber);
                                        $isUploaded = true;
                                    }
                                    
                                    $isPast = $bulanNumber < $currentMonth;
                                    $isCurrent = $bulanNumber == $currentMonth;
                                    $isFuture = $bulanNumber > $currentMonth;
                                    $canEdit = $isCurrent && $isUploaded;

                                    $cardClasses = getMonthCardClasses($isUploaded, $isPast, $isCurrent);
                                    $iconClass = $cardClasses['icon'];
                                @endphp

                                <!-- Month Card -->
                                <div class="group {{ $cardClasses['card'] }} border-2 rounded-xl transition-all duration-300 hover:shadow-lg {{ $isCurrent && !$isUploaded ? 'cursor-pointer hover:-translate-y-1' : '' }}"
                                     @if ($isCurrent && !$isUploaded) onclick="openUploadModal({{ $bulanNumber }}, '{{ $bulan['name'] }}')" @endif>

                                    <div class="p-4 flex flex-col h-48">
                                        <!-- Header -->
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="text-sm font-bold {{ $cardClasses['text'] }}">{{ $bulan['name'] }}</div>
                                            <div
                                                class="w-6 h-6 rounded-full flex items-center justify-center {{ str_replace('text-', 'bg-', $cardClasses['icon']) }} bg-opacity-20">
                                                @if ($isUploaded)
                                                    <svg class="w-4 h-4 {{ $cardClasses['icon'] }}" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                @elseif($isCurrent)
                                                    <svg class="w-4 h-4 {{ $cardClasses['icon'] }}" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                @elseif($isPast && !$isUploaded)
                                                    <svg class="w-4 h-4 {{ $cardClasses['icon'] }}" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 {{ $cardClasses['icon'] }}" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 mb-2 flex flex-col items-center justify-center text-center">
                                            @if ($isUploaded)
                                                <div class="flex items-center space-x-1 text-xs {{ $cardClasses['text'] }} mb-1">
                                                    <svg class="w-3 h-3 {{ $cardClasses['icon'] }}" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M3 17a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2zM3 7a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V7zM4 15h12v2H4v-2z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="font-medium">Dokumen Tersimpan</span>
                                                </div>
                                                <div class="text-xs text-gray-700 font-medium break-words px-1 mb-1">
                                                    {{ $monthlyDocument->nama_file ?? 'SKP Bulanan ' . $bulan['name'] }}
                                                </div>
                                            @elseif($isCurrent)
                                                <div class="flex items-center space-x-1 text-xs {{ $cardClasses['icon'] }} mb-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                                        </path>
                                                    </svg>
                                                    <span class="font-medium">Siap Upload</span>
                                                </div>
                                                <div class="text-xs text-gray-500 px-1">
                                                    Klik untuk mengunggah dokumen
                                                </div>
                                            @elseif($isPast && !$isUploaded)
                                                <div class="flex items-center space-x-1 text-xs {{ $cardClasses['icon'] }} mb-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="font-medium">Sudah Lewat</span>
                                                </div>
                                                <div class="text-xs text-gray-500 px-1">
                                                    Periode upload telah berakhir
                                                </div>
                                            @else
                                                <div class="flex items-center space-x-1 text-xs {{ $cardClasses['icon'] }} mb-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    <span class="font-medium">Menunggu</span>
                                                </div>
                                                <div class="text-xs text-gray-500 px-1">
                                                    Upload akan tersedia nanti
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Action Buttons -->
                                        @if ($isUploaded)
                                            <div class="pt-2 mt-auto">
                                                <div class="space-y-1">
                                    <!-- View button -->
                                    <button onclick="window.open('{{ route('skp.view', $monthlyDocument->id) }}', '_blank')"
                                        class="w-full flex items-center justify-center space-x-1 text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded border border-blue-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                        <span>Lihat</span>
                                    </button>

                                                    <!-- Edit and Delete buttons row -->
                                                    <div class="flex space-x-1">
                                                        <button onclick="editSkp({{ $monthlyDocument->id ?? 0 }}, '{{ $monthlyDocument->nama_file ?? 'SKP Bulanan ' . $bulan['name'] }}')"
                                                            class="flex-1 flex items-center justify-center space-x-1 text-xs text-amber-700 hover:text-amber-800 font-medium transition-colors bg-amber-50 hover:bg-amber-100 px-1 py-1 rounded border border-amber-100">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                                </path>
                                                            </svg>
                                                            <span>Edit</span>
                                                        </button>
                                                        <button onclick="deleteSkp({{ $monthlyDocument->id ?? 0 }}, '{{ $monthlyDocument->nama_file ?? 'SKP Bulanan ' . $bulan['name'] }}')"
                                                            class="flex-1 flex items-center justify-center space-x-1 text-xs text-rose-700 hover:text-rose-800 font-medium transition-colors bg-rose-50 hover:bg-rose-100 px-1 py-1 rounded border border-rose-100">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- SKP Tahunan Tab -->
                <div id="content-tahunan" class="tab-pane hidden">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Upload Dokumen SKP Tahunan {{ date('Y') }}</h3>
                            <p class="text-gray-600 text-sm">Upload dokumen evaluasi kinerja tahunan untuk periode {{ date('Y') }}</p>
                        </div>

                        @php
                            $isYearlyUploaded = isset($skpTahunan) && $skpTahunan->isNotEmpty();
                            $yearlyDocument = $isYearlyUploaded ? $skpTahunan->first() : null;
                        @endphp

                        @if ($isYearlyUploaded)
                            <!-- Uploaded Document Display -->
                            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                                <div class="p-6 flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $yearlyDocument->nama_file ?? 'SKP Tahunan ' . date('Y') }}</h4>
                                            <p class="text-sm text-gray-600">File ID: {{ $yearlyDocument->file_id ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500 mt-1">Diupload: {{ $yearlyDocument->uploaded_at ? \Carbon\Carbon::parse($yearlyDocument->uploaded_at)->format('d M Y, H:i') : 'Tanggal tidak tersedia' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Tersimpan
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex space-x-3">
                                    <button onclick="window.open('{{ route('skp.view', $yearlyDocument->id) }}', '_blank')" 
                                            class="flex-1 inline-flex items-center justify-center space-x-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span>Lihat Dokumen</span>
                                    </button>
                                    <button onclick="editSkp({{ $yearlyDocument->id ?? 0 }}, '{{ $yearlyDocument->nama_file ?? 'SKP Tahunan ' . date('Y') }}')" 
                                            class="flex-1 inline-flex items-center justify-center space-x-2 bg-white border border-amber-300 text-amber-700 px-4 py-2 rounded-lg hover:bg-amber-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span>Edit</span>
                                    </button>
                                    <button onclick="deleteSkp({{ $yearlyDocument->id ?? 0 }}, '{{ $yearlyDocument->nama_file ?? 'SKP Tahunan ' . date('Y') }}')" 
                                            class="flex-1 inline-flex items-center justify-center space-x-2 bg-white border border-rose-300 text-rose-700 px-4 py-2 rounded-lg hover:bg-rose-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- Direct Upload Section -->
                            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                                <div class="p-8 text-center">
                                    <form id="yearlyUploadForm" action="{{ route('skp.upload') }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                                        <input type="hidden" name="kegiatan_id" value="{{ $skp_info->id }}">
                                        <input type="hidden" name="jenis" value="tahunan">
                                        <input type="hidden" name="tahun" value="{{ $currentYear }}">

                                        <div id="yearlyUploadContainer"
                                            class="border-2 border-dashed border-gray-300 bg-gray-50 hover:border-red-500 hover:bg-red-50/50 rounded-xl p-8 text-center cursor-pointer transition-all duration-300">
                                            <input type="file" id="yearlyFileInput" name="dokumen"
                                                accept=".pdf,.doc,.docx" class="hidden" required>

                                            <div id="yearlyUploadPlaceholder">
                                                <div
                                                    class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center">
                                                    <svg class="w-10 h-10 text-red-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <h3 class="text-xl font-bold text-gray-900 mb-3">Upload Dokumen SKP Tahunan</h3>
                                                <p class="text-gray-600 mb-6">Klik atau seret file untuk upload</p>

                                                <button type="button"
                                                    onclick="event.stopPropagation(); document.getElementById('yearlyFileInput').click()"
                                                    class="inline-flex items-center space-x-2 bg-red-600 hover:bg-red-700 text-white font-medium px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                                        </path>
                                                    </svg>
                                                    <span>Upload Dokumen</span>
                                                </button>

                                                <p class="text-sm text-gray-500 mt-4">Format: PDF, DOC, DOCX (Maks: 10MB)</p>
                                            </div>

                                            <!-- File Preview Section -->
                                            <div id="yearlyFilePreview" class="hidden">
                                                <div
                                                    class="flex items-center justify-between bg-white border border-gray-200 p-4 rounded-lg">
                                                    <div class="flex items-center space-x-3">
                                                        <div
                                                            class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-red-600" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <div id="yearlyFileName"
                                                                class="text-sm font-medium text-gray-900"></div>
                                                            <div id="yearlyFileSize" class="text-xs text-gray-500"></div>
                                                        </div>
                                                    </div>
                                                    <button id="removeYearlyFile" type="button"
                                                        class="text-red-500 hover:text-red-700">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div class="mt-4 flex space-x-3">
                                                    <button type="button" id="cancelYearlyUpload"
                                                        class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                                        Batal
                                                    </button>
                                                    <button type="submit"
                                                        class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                                        Upload
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-t-2xl p-5 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold">Edit Dokumen</h3>
                    </div>
                    <button onclick="closeEditModal()" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="p-5">
                <!-- Tab Navigation -->
                <div class="flex mb-5 bg-gray-100 rounded-lg p-1">
                    <button onclick="switchTab('name')" id="nameTab" class="flex-1 py-2 px-3 rounded-md text-sm font-medium transition-colors bg-white text-red-600 shadow-sm">
                        Ubah Nama
                    </button>
                    <button onclick="switchTab('file')" id="fileTab" class="flex-1 py-2 px-3 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-gray-800">
                        Upload Ulang
                    </button>
                </div>

                <!-- Tab Content: Edit Name -->
                <div id="nameContent" class="tab-content">
                    <form id="editNameForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-5">
                            <label for="nama_dokumen" class="block text-sm font-semibold text-gray-700 mb-2">Nama Dokumen</label>
                            <div class="relative">
                                <input type="text" id="nama_dokumen" name="nama_dokumen" required
                                       class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"></path>
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
                            <div class="border-2 border-dashed border-red-300 rounded-lg p-4 text-center hover:border-red-400 transition-colors bg-red-50">
                                <div class="mb-3">
                                    <svg class="w-10 h-10 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">Pilih file baru untuk mengganti dokumen</p>
                                <input type="file" name="dokumen" id="editFileInput" required
                                       accept=".pdf,.doc,.docx,.xls,.xlsx"
                                       class="w-full text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-100 file:text-red-700 hover:file:bg-red-200 cursor-pointer"
                                       onchange="updateDokumenFileName('editFileInput', 'editFileDisplay')">
                                <p class="text-xs text-gray-500 mt-2" id="fileTypeInfo">PDF, DOC, DOCX, XLS, XLSX • Max 10MB</p>
                                <p id="editFileDisplay" class="text-sm mt-2"></p>
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="deleteModalContent">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
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

    <!-- Modern Upload Modal for Monthly SKP -->
    <div id="uploadModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white backdrop-blur-sm border border-white/20 rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-xl transform transition-all duration-300 scale-95 opacity-0"
            id="uploadModalContent">
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 id="modalTitle" class="text-xl font-semibold text-gray-900">Upload Dokumen</h3>
                        <p class="text-sm text-gray-500 mt-1">Pilih file PDF, DOC, atau DOCX</p>
                    </div>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="uploadForm" action="{{ route('skp.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                    <input type="hidden" name="kegiatan_id" value="{{ $skp_info->id }}">
                    <input type="hidden" name="jenis" value="bulanan">
                    <input type="hidden" id="monthInput" name="bulan" value="">
                    <input type="hidden" name="tahun" value="{{ $currentYear }}">

                    <!-- Clean Drop Zone -->
                    <div id="dropZone"
                        class="border-2 border-dashed border-gray-300 bg-gray-50 hover:border-red-500 hover:bg-red-50/50 rounded-xl p-8 text-center cursor-pointer mb-6 transition-all duration-300">
                        <div class="mb-4">
                            <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Drop file atau klik untuk upload</h4>
                        <p class="text-sm text-gray-500">Maksimal 10MB</p>
                        <input type="file" id="fileInput" name="dokumen" accept=".pdf,.doc,.docx" class="hidden"
                            required>
                    </div>

                    <!-- File Preview -->
                    <div id="filePreview" class="hidden mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div id="fileName" class="text-sm font-medium text-gray-900"></div>
                                <div id="fileSize" class="text-xs text-gray-500"></div>
                            </div>
                            <button type="button" id="removeFile" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <button type="button" id="cancelUpload"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" id="submitUpload"
                            class="flex-1 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    @push('scripts')
        <script>
            // Tab switching functionality
            function switchTab(tabName) {
                // Hide all tab panes
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.add('hidden');
                    pane.classList.remove('active');
                });

                // Remove active class from all tab buttons
                document.querySelectorAll('.tab-button').forEach(button => {
                    button.classList.remove('active', 'border-blue-500', 'text-blue-600');
                    button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700',
                        'hover:border-gray-300');
                });

                // Show selected tab pane
                const targetPane = document.getElementById('content-' + tabName);
                if (targetPane) {
                    targetPane.classList.remove('hidden');
                    targetPane.classList.add('active');
                }

                // Add active class to selected tab button
                const activeButton = document.getElementById('tab-' + tabName);
                if (activeButton) {
                    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
                    activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700',
                        'hover:border-gray-300');
                }
            }

            // Monthly Upload Modal Functions
            function openUploadModal(month, monthName) {
                const modalTitle = document.getElementById('modalTitle');
                const monthInput = document.getElementById('monthInput');
                const modal = document.getElementById('uploadModal');
                const modalContent = document.getElementById('uploadModalContent');
                
                if (!modal || !modalContent) {
                    return;
                }
                
                if (modalTitle) modalTitle.textContent = `Upload SKP ${monthName}`;
                if (monthInput) monthInput.value = month;
                
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function closeUploadModal() {
                const modal = document.getElementById('uploadModal');
                const modalContent = document.getElementById('uploadModalContent');
                modalContent.classList.add('scale-95', 'opacity-0');
                modalContent.classList.remove('scale-100', 'opacity-100');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.getElementById('uploadForm').reset();
                    document.getElementById('filePreview').classList.add('hidden');
                    // Show drop zone again when modal is closed
                    document.getElementById('dropZone').classList.remove('hidden');
                }, 300);
            }

            // Document Action Functions
            function viewDocument(month) {
                alert(`Melihat dokumen bulan ${month}`);
            }

            function editDocument(month) {
                alert(`Edit dokumen bulan ${month}`);
            }

            function deleteDocument(month) {
                if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
                    alert(`Dokumen bulan ${month} dihapus`);
                }
            }



            // Event listeners
            document.addEventListener('DOMContentLoaded', function() {
                // Monthly modal event listeners
                document.getElementById('closeModal').addEventListener('click', closeUploadModal);
                document.getElementById('cancelUpload').addEventListener('click', closeUploadModal);
                
                // Monthly remove file event listener
                document.getElementById('removeFile').addEventListener('click', function() {
                    document.getElementById('fileInput').value = '';
                    document.getElementById('filePreview').classList.add('hidden');
                    document.getElementById('dropZone').classList.remove('hidden');
                    document.getElementById('fileName').textContent = '';
                    document.getElementById('fileSize').textContent = '';
                });



                // File upload functionality for monthly
                const dropZone = document.getElementById('dropZone');
                const fileInput = document.getElementById('fileInput');
                const filePreview = document.getElementById('filePreview');

                dropZone.addEventListener('click', () => fileInput.click());
                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropZone.classList.add('border-blue-500', 'bg-blue-50');
                });
                dropZone.addEventListener('dragleave', () => {
                    dropZone.classList.remove('border-blue-500', 'bg-blue-50');
                });
                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('border-blue-500', 'bg-blue-50');
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        handleFileSelection(files[0], 'monthly');
                    }
                });

                fileInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        handleFileSelection(e.target.files[0], 'monthly');
                    }
                });

                // Monthly upload form submission
                const uploadForm = document.getElementById('uploadForm');
                uploadForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const fileInput = this.querySelector('#fileInput');

                    if (!fileInput.files.length) {
                        alert('Silakan pilih file terlebih dahulu');
                        return;
                    }

                    const formData = new FormData(this);

                    // Close modal first
                    closeUploadModal();

                    // Show global loading overlay
                    if (typeof window.showGlobalLoading === 'function') {
                        window.showGlobalLoading();
                    }

                    fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Hide global loading overlay
                            if (typeof window.hideGlobalLoading === 'function') {
                                window.hideGlobalLoading();
                            }

                            if (data.success) {
                                // Show success notification
                                if (typeof window.showSuccess === 'function') {
                                    window.showSuccess(data.message || 'Dokumen SKP Bulanan berhasil diupload');
                                }
                                
                                // Reload page after short delay
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                // Show error notification
                                if (typeof window.showError === 'function') {
                                    window.showError(data.message || 'Gagal mengupload dokumen');
                                } else {
                                    alert(data.message || 'Gagal mengupload dokumen');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            
                            // Hide global loading overlay
                            if (typeof window.hideGlobalLoading === 'function') {
                                window.hideGlobalLoading();
                            }
                            
                            // Show error notification
                            if (typeof window.showError === 'function') {
                                window.showError('Terjadi kesalahan saat mengupload dokumen');
                            } else {
                                alert('Terjadi kesalahan saat mengupload dokumen');
                            }
                        });
                });



                // File upload functionality for yearly
                const yearlyUploadContainer = document.getElementById('yearlyUploadContainer');
                const yearlyFileInput = document.getElementById('yearlyFileInput');
                const yearlyFilePreview = document.getElementById('yearlyFilePreview');
                const yearlyUploadPlaceholder = document.getElementById('yearlyUploadPlaceholder');
                const yearlyFileName = document.getElementById('yearlyFileName');
                const yearlyFileSize = document.getElementById('yearlyFileSize');
                const removeYearlyFile = document.getElementById('removeYearlyFile');
                const cancelYearlyUpload = document.getElementById('cancelYearlyUpload');
                const yearlyUploadForm = document.getElementById('yearlyUploadForm');

                function handleYearlyFileSelection(file) {
                    const maxSize = 10 * 1024 * 1024; // 10MB
                    const allowedTypes = ['application/pdf', 'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ];

                    if (!allowedTypes.includes(file.type)) {
                        alert('Hanya file PDF dan Word yang diperbolehkan!');
                        yearlyFileInput.value = '';
                        return false;
                    }

                    if (file.size > maxSize) {
                        alert('Ukuran file tidak boleh lebih dari 10MB!');
                        yearlyFileInput.value = '';
                        return false;
                    }

                    yearlyFileName.textContent = file.name;
                    yearlyFileSize.textContent = formatFileSize(file.size);
                    yearlyUploadPlaceholder.classList.add('hidden');
                    yearlyFilePreview.classList.remove('hidden');

                    return true;
                }

                // yearlyUploadContainer.addEventListener('click', () => yearlyFileInput.click()); // Removed to prevent double trigger
                yearlyUploadContainer.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    yearlyUploadContainer.classList.add('border-blue-500', 'bg-blue-50');
                });
                yearlyUploadContainer.addEventListener('dragleave', () => {
                    yearlyUploadContainer.classList.remove('border-blue-500', 'bg-blue-50');
                });
                yearlyUploadContainer.addEventListener('drop', (e) => {
                    e.preventDefault();
                    yearlyUploadContainer.classList.remove('border-blue-500', 'bg-blue-50');
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        yearlyFileInput.files = files;
                        handleYearlyFileSelection(files[0]);
                    }
                });

                yearlyFileInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        handleYearlyFileSelection(e.target.files[0]);
                    }
                });

                removeYearlyFile.addEventListener('click', () => {
                    document.getElementById('yearlyFileInput').value = '';
                    yearlyFilePreview.classList.add('hidden');
                    yearlyUploadPlaceholder.classList.remove('hidden');
                    yearlyFileName.textContent = '';
                    yearlyFileSize.textContent = '';
                });

                cancelYearlyUpload.addEventListener('click', function() {
                    yearlyUploadPlaceholder.classList.remove('hidden');
                    yearlyFilePreview.classList.add('hidden');
                    document.getElementById('yearlyFileInput').value = '';
                });

                yearlyUploadForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const fileInput = this.querySelector('#yearlyFileInput');

                    if (!fileInput.files.length) {
                        alert('Silakan pilih file terlebih dahulu');
                        return;
                    }

                    const formData = new FormData(this);

                    // Show global loading overlay
                    if (typeof window.showGlobalLoading === 'function') {
                        window.showGlobalLoading();
                    }

                    fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Hide global loading overlay
                            if (typeof window.hideGlobalLoading === 'function') {
                                window.hideGlobalLoading();
                            }

                            if (data.success) {
                                // Show success notification
                                if (typeof window.showSuccess === 'function') {
                                    window.showSuccess(data.message || 'Dokumen SKP Tahunan berhasil diupload');
                                }
                                
                                // Reload page after short delay
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                // Show error notification
                                if (typeof window.showError === 'function') {
                                    window.showError(data.message || 'Gagal mengupload dokumen');
                                } else {
                                    alert(data.message || 'Gagal mengupload dokumen');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            
                            // Hide global loading overlay
                            if (typeof window.hideGlobalLoading === 'function') {
                                window.hideGlobalLoading();
                            }
                            
                            // Show error notification
                            if (typeof window.showError === 'function') {
                                window.showError('Terjadi kesalahan saat mengupload dokumen');
                            } else {
                                alert('Terjadi kesalahan saat mengupload dokumen');
                            }
                        });
                });

                // File selection handler
                function handleFileSelection(file, type) {
                    const maxSize = 10 * 1024 * 1024; // 10MB
                    const allowedTypes = ['application/pdf', 'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ];

                    if (!allowedTypes.includes(file.type)) {
                        alert('Hanya file PDF dan Word yang diperbolehkan!');
                        // ✅ Bersihkan file input berdasarkan tipe
                        if (type === 'monthly') {
                            document.getElementById('fileInput').value = '';
                        } else if (type === 'yearly') {
                            document.getElementById('yearlyFileInput').value = '';
                        }
                        return;
                    }

                    if (file.size > maxSize) {
                        alert('Ukuran file tidak boleh lebih dari 10MB!');
                        // ✅ Bersihkan file input berdasarkan tipe
                        if (type === 'monthly') {
                            document.getElementById('fileInput').value = '';
                        } else if (type === 'yearly') {
                            document.getElementById('yearlyFileInput').value = '';
                        }
                        return;
                    }

                    let fileNameElement, fileSizeElement, filePreviewElement, removeButtonElement, dropZoneElement;

                    if (type === 'monthly') {
                        fileNameElement = document.getElementById('fileName');
                        fileSizeElement = document.getElementById('fileSize');
                        filePreviewElement = document.getElementById('filePreview');
                        removeButtonElement = document.getElementById('removeFile');
                        dropZoneElement = document.getElementById('dropZone');
                    } else if (type === 'yearly') {
                        fileNameElement = document.getElementById('yearlyFileName');
                        fileSizeElement = document.getElementById('yearlyFileSize');
                        filePreviewElement = document.getElementById('yearlyFilePreview');
                        removeButtonElement = document.getElementById('removeYearlyFile');
                        dropZoneElement = null; // Yearly doesn't have dropZone in modal
                    }

                    fileNameElement.textContent = file.name;
                    fileSizeElement.textContent = formatFileSize(file.size);
                    filePreviewElement.classList.remove('hidden');
                    
                    // Hide drop zone when file is selected (only for monthly)
                    if (dropZoneElement) {
                        dropZoneElement.classList.add('hidden');
                    }

                    // Only set onclick for yearly files, monthly uses dedicated event listener
                    if (type === 'yearly') {
                        removeButtonElement.onclick = () => {
                            document.getElementById('yearlyFileInput').value = '';
                            filePreviewElement.classList.add('hidden');
                        };
                    }
                }

                function formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }

                // Close modals when clicking outside
                document.getElementById('uploadModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeUploadModal();
                    }
                });

                // Keyboard shortcuts
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeUploadModal();
                    }
                });
            });

            // Functions for document management - same as kegiatan/detail.blade.php
            function updateDokumenFileName(inputId, displayId) {
                const input = document.getElementById(inputId);
                const display = document.getElementById(displayId);
                
                if (input.files.length > 0) {
                    const file = input.files[0];
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    
                    // Check file size (max 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        display.textContent = 'File terlalu besar (maksimal 5MB)';
                        display.classList.add('text-red-600');
                        display.classList.remove('text-green-600');
                        input.value = '';
                        return;
                    }
                    
                    display.textContent = `${file.name} (${fileSize} MB)`;
                    display.classList.add('text-green-600');
                    display.classList.remove('text-red-600');
                } else {
                    display.textContent = '';
                    display.classList.remove('text-green-600', 'text-red-600');
                }
            }

            // SKP functions
            function editSkp(id, currentName) {
                const modal = document.getElementById('editModal');
                const modalContent = document.getElementById('modalContent');

                // Show modal
                modal.classList.remove('hidden');

                // Animate modal appearance
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);

                // Set form data for SKP documents
                document.getElementById('nama_dokumen').value = currentName;
                document.getElementById('editNameForm').action = `{{ route('skp.update.name', ':id') }}`.replace(':id', id);
                document.getElementById('editDokumenFileForm').action = `{{ route('skp.update.file', ':id') }}`.replace(':id', id);
                document.getElementById('editDocumentId').value = id;

                // Set file type restrictions for SKP documents
                const fileInput = document.getElementById('editFileInput');
                const fileTypeInfo = document.getElementById('fileTypeInfo');

                fileInput.accept = '.pdf,.doc,.docx,.xls,.xlsx';
                fileTypeInfo.textContent = 'PDF, DOC, DOCX, XLS, XLSX • Max 10MB';

                // Reset to first tab
                switchTab('name');
            }

            function deleteSkp(id, documentName) {
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('deleteModalContent');
                const message = document.getElementById('deleteMessage');
                const confirmBtn = document.getElementById('confirmDeleteBtn');

                message.textContent = `Apakah Anda yakin ingin menghapus "${documentName}"?`;
                
                confirmBtn.onclick = function() {
                    // Close modal first
                    closeDeleteModal();
                    
                    // Show global loading overlay
                    if (typeof window.showGlobalLoading === 'function') {
                        window.showGlobalLoading();
                    }

                    // Create and submit delete form using fetch
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'DELETE');

                    fetch(`{{ route('skp.delete', ':id') }}`.replace(':id', id), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Hide global loading overlay
                        if (typeof window.hideGlobalLoading === 'function') {
                            window.hideGlobalLoading();
                        }

                        if (data.success) {
                            // Show success notification
                            if (typeof window.showSuccess === 'function') {
                                window.showSuccess(data.message || 'SKP berhasil dihapus');
                            }
                            
                            // Reload page after short delay
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            // Show error notification
                            if (typeof window.showError === 'function') {
                                window.showError(data.message || 'Gagal menghapus SKP');
                            } else {
                                alert(data.message || 'Gagal menghapus SKP');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        
                        // Hide global loading overlay
                        if (typeof window.hideGlobalLoading === 'function') {
                            window.hideGlobalLoading();
                        }
                        
                        // Show error notification
                        if (typeof window.showError === 'function') {
                            window.showError('Terjadi kesalahan saat menghapus SKP');
                        } else {
                            alert('Terjadi kesalahan saat menghapus SKP');
                        }
                    });
                };

                // Show modal
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            // Delete functions
            function deleteBuktiDukung(id, documentName) {
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('deleteModalContent');
                const message = document.getElementById('deleteMessage');
                const confirmBtn = document.getElementById('confirmDeleteBtn');

                message.textContent = `Apakah Anda yakin ingin menghapus "${documentName}"?`;
                
                confirmBtn.onclick = function() {
                    // Close modal first
                    closeDeleteModal();
                    
                    // Show global loading overlay
                    if (typeof window.showGlobalLoading === 'function') {
                        window.showGlobalLoading();
                    }

                    // Create and submit delete form using fetch
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'DELETE');

                    fetch(`{{ route('bukti.dukung.destroy', ':id') }}`.replace(':id', id), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Hide global loading overlay
                        if (typeof window.hideGlobalLoading === 'function') {
                            window.hideGlobalLoading();
                        }

                        if (data.success) {
                            // Show success notification
                            if (typeof window.showSuccess === 'function') {
                                window.showSuccess(data.message || 'Dokumen berhasil dihapus');
                            }
                            
                            // Reload page after short delay
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            // Show error notification
                            if (typeof window.showError === 'function') {
                                window.showError(data.message || 'Gagal menghapus dokumen');
                            } else {
                                alert(data.message || 'Gagal menghapus dokumen');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        
                        // Hide global loading overlay
                        if (typeof window.hideGlobalLoading === 'function') {
                            window.hideGlobalLoading();
                        }
                        
                        // Show error notification
                        if (typeof window.showError === 'function') {
                            window.showError('Terjadi kesalahan saat menghapus dokumen');
                        } else {
                            alert('Terjadi kesalahan saat menghapus dokumen');
                        }
                    });
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
                document.getElementById('editNameForm').action = `{{ route('skp.update.name', ':id') }}`.replace(':id', id);
                document.getElementById('editDokumenFileForm').action = `{{ route('skp.update.file', ':id') }}`.replace(':id', id);
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
            document.getElementById('deleteModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDeleteModal();
                }
            });

            document.getElementById('editModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeEditModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeEditModal();
                    closeDeleteModal();
                }
            });

            // File input change handler with validation
            document.getElementById('editFileInput').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    const maxSize = 10; // 10MB max
                    
                    if (file.size > maxSize * 1024 * 1024) {
                        if (typeof window.showError === 'function') {
                            window.showError(`Ukuran file terlalu besar. Maksimal ${maxSize}MB.`);
                        } else {
                            alert(`Ukuran file terlalu besar. Maksimal ${maxSize}MB.`);
                        }
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
                        e.preventDefault();
                        
                        // Close modal first
                        closeEditModal();
                        
                        // Show global loading overlay
                        if (typeof window.showGlobalLoading === 'function') {
                            window.showGlobalLoading('Mengupdate nama dokumen...');
                        }
                        
                        // Submit form via AJAX
                        const formData = new FormData(this);
                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (typeof window.hideGlobalLoading === 'function') {
                                window.hideGlobalLoading();
                            }
                            
                            if (data.success) {
                                // Show success message using global showSuccess
                                if (typeof window.showSuccess === 'function') {
                                    window.showSuccess(data.message);
                                } else if (typeof window.showAlert === 'function') {
                                    window.showAlert(data.message, 'success');
                                } else {
                                    alert(data.message);
                                }
                                
                                // Redirect if URL provided
                                if (data.redirect_url) {
                                    setTimeout(() => {
                                        window.location.href = data.redirect_url;
                                    }, 1500);
                                } else {
                                    location.reload();
                                }
                            } else {
                                if (typeof window.showError === 'function') {
                                    window.showError(data.message);
                                } else if (typeof window.showAlert === 'function') {
                                    window.showAlert(data.message, 'error');
                                } else {
                                    alert(data.message);
                                }
                            }
                        })
                        .catch(error => {
                            if (typeof window.hideGlobalLoading === 'function') {
                                window.hideGlobalLoading();
                            }
                            console.error('Error updating SKP name:', error);
                            
                            // Use showError for all error messages
                            if (typeof window.showError === 'function') {
                                window.showError('Terjadi kesalahan saat mengupdate nama dokumen: ' + error.message);
                            } else if (typeof window.showAlert === 'function') {
                                window.showAlert('Terjadi kesalahan saat mengupdate nama dokumen', 'error');
                            } else {
                                alert('Terjadi kesalahan saat mengupdate nama dokumen');
                            }
                        });
                    });
                }
                
                // Handle edit file form
            const editFileForm = document.getElementById('editDokumenFileForm');
            if (editFileForm) {
                editFileForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const fileInput = this.querySelector('#editFileInput');
                        
                        if (!fileInput.files.length) {
                            if (typeof window.showError === 'function') {
                                window.showError('Silakan pilih file terlebih dahulu');
                            } else {
                                alert('Silakan pilih file terlebih dahulu');
                            }
                            return;
                        }
                        
                        // Validate file size again before submit
                        const file = fileInput.files[0];
                        const maxSize = 10; // 10MB max
                        if (file.size > maxSize * 1024 * 1024) {
                            if (typeof window.showError === 'function') {
                                window.showError(`Ukuran file terlalu besar. Maksimal ${maxSize}MB.`);
                            } else {
                                alert(`Ukuran file terlalu besar. Maksimal ${maxSize}MB.`);
                            }
                            return;
                        }
                        
                        // Close modal first
                        closeEditModal();
                        
                        // Show global loading overlay
                        if (typeof window.showGlobalLoading === 'function') {
                            window.showGlobalLoading('Mengupload file baru...');
                        }
                        
                        // Submit form via AJAX
                        const formData = new FormData(this);
                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (typeof window.hideGlobalLoading === 'function') {
                                window.hideGlobalLoading();
                            }
                            
                            if (data.success) {
                                // Show success message using global showSuccess
                                if (typeof window.showSuccess === 'function') {
                                    window.showSuccess(data.message);
                                } else if (typeof window.showAlert === 'function') {
                                    window.showAlert(data.message, 'success');
                                } else {
                                    alert(data.message);
                                }
                                
                                // Redirect if URL provided
                                if (data.redirect_url) {
                                    setTimeout(() => {
                                        window.location.href = data.redirect_url;
                                    }, 1500);
                                } else {
                                    location.reload();
                                }
                            } else {
                                if (typeof window.showError === 'function') {
                                    window.showError(data.message);
                                } else if (typeof window.showAlert === 'function') {
                                    window.showAlert(data.message, 'error');
                                } else {
                                    alert(data.message);
                                }
                            }
                        })
                        .catch(error => {
                            if (typeof window.hideGlobalLoading === 'function') {
                                window.hideGlobalLoading();
                            }
                            console.error('Error updating SKP file:', error);
                            
                            // Use showError for all error messages
                            if (typeof window.showError === 'function') {
                                window.showError('Terjadi kesalahan saat mengupload file: ' + error.message);
                            } else if (typeof window.showAlert === 'function') {
                                window.showAlert('Terjadi kesalahan saat mengupload file', 'error');
                            } else {
                                alert('Terjadi kesalahan saat mengupload file');
                            }
                        });
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
    @endpush
@endsection
