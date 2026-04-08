@extends('components.master')

@section('title', 'Dashboard')

@section('content')
    @include('components.breadcrumbs')

    <!-- Custom Color Theme -->
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

    <!-- Header Welcome Section -->
    <div class="container mx-auto py-4">
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Selamat Datang, {{ $user->name }} 👋</h1>
                <div class="flex items-center mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">
                        {{ $user->getRoleDisplayName() }}
                    </span>
                    <p class="text-sm text-gray-500">{{ $user->jabatan }}</p>
                </div>
            </div>
            <div class="flex items-center mt-4 md:mt-0 space-x-4">
                <div class="flex items-center bg-red-50 p-3 rounded-xl shadow-sm">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="ml-2 text-gray-700">{{ date('d F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
            <!-- SAKIP Progress Card -->
            <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-md transition-all duration-300 hover:shadow-xl">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Progress SAKIP</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $overall_progress }}%</p>
                    </div>
                    <div class="relative size-16">
                        <svg class="w-full h-full" viewBox="0 0 36 36">
                            <!-- Background Circle -->
                            <circle cx="18" cy="18" r="16" fill="none" 
                                class="stroke-current text-red-100" 
                                stroke-width="2.5"></circle>
                            
                            <!-- Progress Circle -->
                            <circle cx="18" cy="18" r="16" fill="none" 
                                class="stroke-current text-red-500" 
                                stroke-width="2.5"
                                stroke-dasharray="100.5" 
                                stroke-dashoffset="{{ 100.5 - ($overall_progress * 100.5 / 100) }}"
                                stroke-linecap="round"
                                transform="rotate(-90 18 18)"></circle>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-gray-500">
                    <span class="font-medium">Berdasarkan data sistem</span>
                </div>
            </div>

            <!-- Completed Tasks Card -->
            <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-md transition-all duration-300 hover:shadow-xl">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Komponen Selesai</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $komponen_selesai }}/3</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-xl animate-float">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-gray-500">
                    <span class="font-medium">Komponen SAKIP</span> telah diselesaikan
                </div>
            </div>

            <!-- Upcoming Deadline Card -->
            <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-md transition-all duration-300 hover:shadow-xl">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Deadline Terdekat</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $deadline_text }}</p>
                    </div>
                    <div class="bg-amber-100 p-3 rounded-xl animate-float">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-amber-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    {{ $deadline_date }}
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Progress Section -->
            <div class="lg:col-span-2">
                <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-md">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Progress Pelaksanaan SAKIP</h2>
                        <p class="text-sm text-gray-500">Tahun berjalan 2025</p>
                    </div>

                    <!-- Progress Indicators -->
                    <div class="space-y-5">
                        <!-- Perencanaan Kinerja -->
                        <a href="{{ route('dashboard.detail', ['komponen' => 'perencanaan']) }}" class="block">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-yellow-50 rounded-xl group hover:bg-yellow-100 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-4 mb-3 sm:mb-0">
                                    <div class="bg-white p-3 rounded-lg shadow-sm">
                                        <span class="text-yellow-600 font-bold">1</span>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">Perencanaan Kinerja</h3>
                                        <p class="text-sm text-gray-500 mt-1">Menyelesaikan {{ $perencanaan_progress }}% target</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="w-24 h-2 bg-gray-200 rounded-full">
                                        <div class="h-2 bg-yellow-500 rounded-full" style="width: {{ $perencanaan_progress }}%"></div>
                                    </div>
                                    <div class="text-yellow-600 hover:text-yellow-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                        
                        <!-- Pengukuran Kinerja -->
                        <a href="{{ route('dashboard.detail', ['komponen' => 'pengukuran']) }}" class="block">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-red-50 rounded-xl group hover:bg-red-100 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-4 mb-3 sm:mb-0">
                                    <div class="bg-white p-3 rounded-lg shadow-sm">
                                        <span class="text-red-600 font-bold">2</span>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">Pengukuran Kinerja</h3>
                                        <p class="text-sm text-gray-500 mt-1">Menyelesaikan {{ $pengukuran_progress }}% target</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="w-24 h-2 bg-gray-200 rounded-full">
                                        <div class="h-2 bg-red-500 rounded-full" style="width: {{ $pengukuran_progress }}%"></div>
                                    </div>
                                    <div class="text-red-600 hover:text-red-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                        
                        <!-- Pelaporan Kinerja -->
                        <a href="{{ route('dashboard.detail', ['komponen' => 'pelaporan']) }}" class="block">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-green-50 rounded-xl group hover:bg-green-100 transition-colors cursor-pointer">
                                <div class="flex items-center space-x-4 mb-3 sm:mb-0">
                                    <div class="bg-white p-3 rounded-lg shadow-sm">
                                        <span class="text-green-600 font-bold">3</span>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">Pelaporan Kinerja</h3>
                                        <p class="text-sm text-gray-500 mt-1">Menyelesaikan {{ $pelaporan_progress }}% target</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="w-24 h-2 bg-gray-200 rounded-full">
                                        <div class="h-2 bg-green-500 rounded-full" style="width: {{ $pelaporan_progress }}%"></div>
                                    </div>
                                    <div class="text-green-600 hover:text-green-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Timeline Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-md h-full overflow-hidden">
                    <!-- Header with image -->
                    <div class="relative">
                        <div class="relative h-40 overflow-hidden">
                            <img class="w-full h-full object-cover" src="img/bg1.jpg" alt="Reminder background">
                            <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-red-800 opacity-60"></div>
                        </div>
                        
                        <!-- Card Content Overlay -->
                        <div class="absolute inset-0 p-6 flex flex-col justify-between">
                            <div class="flex items-center text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="font-bold text-lg text-white">Reminder</h3>
                            </div>
                            
                            <div>
                                <p class="text-white/90 font-medium">Tenggat waktu terdekat bulan ini</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Timeline Items -->
                    <div class="p-4 sm:p-6">
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            @if($fra_this_month->count() > 0 || $activities_this_month->count() > 0)
                                @php
                                    $allDeadlines = collect();
                                    $today = \Carbon\Carbon::today();
                                    
                                    // Gabungkan FRA deadlines
                                    foreach($fra_this_month as $fra) {
                                        if(is_object($fra)) {
                                            $deadlineDate = \Carbon\Carbon::parse($fra->tanggal_selesai);
                                            // Hanya tampilkan jika deadline belum terlewat
                                            if($deadlineDate->greaterThanOrEqualTo($today)) {
                                                $allDeadlines->push([
                                                    'title' => 'FRA ' . $fra->nama_triwulan,
                                                    'date' => $fra->tanggal_selesai,
                                                    'type' => 'fra',
                                                    'color' => 'bg-red-500'
                                                ]);
                                            }
                                        } else {
                                            $deadlineDate = \Carbon\Carbon::parse($fra['tanggal_selesai']);
                                            // Hanya tampilkan jika deadline belum terlewat
                                            if($deadlineDate->greaterThanOrEqualTo($today)) {
                                                $bgColor = isset($fra['type']) && $fra['type'] === 'skp_bulanan' ? 'bg-blue-500' : (isset($fra['type']) && $fra['type'] === 'skp_tahunan' ? 'bg-purple-500' : 'bg-red-500');
                                                $allDeadlines->push([
                                                    'title' => $fra['nama_triwulan'] ?? 'N/A',
                                                    'date' => $fra['tanggal_selesai'],
                                                    'type' => $fra['type'] ?? 'fra',
                                                    'color' => $bgColor
                                                ]);
                                            }
                                        }
                                    }
                                    
                                    // Gabungkan activity deadlines
                                    foreach($activities_this_month as $activity) {
                                        $deadlineDate = \Carbon\Carbon::parse($activity->tanggal_berakhir);
                                        // Hanya tampilkan jika deadline belum terlewat
                                        if($deadlineDate->greaterThanOrEqualTo($today)) {
                                            $allDeadlines->push([
                                                'title' => $activity->nama_kegiatan,
                                                'date' => $activity->tanggal_berakhir,
                                                'type' => 'activity',
                                                'color' => 'bg-amber-500'
                                            ]);
                                        }
                                    }
                                    
                                    // Urutkan berdasarkan tanggal dan batasi maksimal 8 item
                                    $sortedDeadlines = $allDeadlines->sortBy('date')->take(8);
                                    $hasMore = $allDeadlines->count() > 8;
                                @endphp
                                
                                @foreach($sortedDeadlines as $index => $deadline)
                                    <div class="relative pl-6 border-l-2 border-gray-100 {{ $loop->last && !$hasMore ? '' : 'pb-4' }} ml-2">
                                        <div class="absolute w-4 h-4 {{ $deadline['color'] }} rounded-full -left-2 top-0 shadow-md"></div>
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $deadline['title'] }}</h3>
                                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($deadline['date'])->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($hasMore)
                                    <div class="relative pl-6 border-l-2 border-gray-100 ml-2">
                                        <div class="absolute w-4 h-4 bg-gray-400 rounded-full -left-2 top-0 shadow-md"></div>
                                        <div>
                                            <p class="text-sm text-gray-500 italic">+{{ $allDeadlines->count() - 8 }} deadline lainnya...</p>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-8">
                                    <div class="text-gray-400 mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-sm">Tidak ada deadline bulan ini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Fungsi untuk membersihkan cache dashboard
        function clearDashboardCache() {
            fetch('{{ route('dashboard.clear-cache') }}')
                .then(response => response.json())
                .then(data => {
                    console.log('Dashboard cache cleared:', data.message);
                    // Opsional: Refresh halaman untuk memuat data terbaru
                    // window.location.reload();
                })
                .catch(error => {
                    console.error('Error clearing dashboard cache:', error);
                });
        }

        // Bersihkan cache saat halaman dimuat
        document.addEventListener('DOMContentLoaded', clearDashboardCache);

        // Bersihkan cache setiap 5 menit untuk memastikan data selalu terbaru
        setInterval(clearDashboardCache, 5 * 60 * 1000);
    </script>
@endpush