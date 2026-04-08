@extends('components.master')

@section('title', 'Manajemen Pengguna')

@section('content')

    <div class="container mx-auto py-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manajemen Pengguna</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola akun pengguna dan hak akses sistem</p>
            </div>
            <div class="justify-end">
                <button id="btnTambahPengguna"
                    class="group flex items-center bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 transition-transform group-hover:rotate-90"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Pengguna
        </button>
    </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <!-- Search and Filter Section -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Pengguna</h2>
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Cari pengguna..."
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
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider sortable cursor-pointer hover:bg-gray-100" data-sort-by="name" style="width: 25%;">Nama 
                                <span class="sort-icons ml-2 inline-flex items-center align-middle">
                                    <svg class="h-3 w-3 inline-block sort-asc-icon {{ request('sort_by') === 'name' && request('sort_order') === 'asc' ? 'text-gray-900' : 'text-gray-400 hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    <svg class="h-3 w-3 inline-block sort-desc-icon {{ request('sort_by') === 'name' && request('sort_order') === 'desc' ? 'text-gray-900' : 'text-gray-400 hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                    <svg class="h-3 w-3 inline-block sort-neutral-icon {{ request('sort_by') === 'name' ? 'hidden' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                </span>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 10%;">NIP</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider sortable cursor-pointer hover:bg-gray-100" data-sort-by="jabatan" style="width: 13%;">Jabatan
                                <span class="sort-icons ml-2 inline-flex items-center align-middle">
                                    <svg class="h-3 w-3 inline-block sort-asc-icon {{ request('sort_by') === 'jabatan' && request('sort_order') === 'asc' ? 'text-gray-900' : 'text-gray-400 hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    <svg class="h-3 w-3 inline-block sort-desc-icon {{ request('sort_by') === 'jabatan' && request('sort_order') === 'desc' ? 'text-gray-900' : 'text-gray-400 hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                    <svg class="h-3 w-3 inline-block sort-neutral-icon {{ request('sort_by') === 'jabatan' ? 'hidden' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                </span>
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 25%;">Bidang</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 15%;">Email</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" 
                                style="width: 15%;">
                                Role
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pengguna as $user)
                            <tr class="hover:bg-gray-50 transition-colors" 
                                data-search="{{ strtolower($user->name) }} {{ strtolower($user->email) }} {{ strtolower($user->nip) }} {{ strtolower($user->jabatan) }} {{ strtolower($user->bidang) }} {{ strtolower($user->role->role_name) }}">
                                <td class="px-2 py-4">
                                    <div class="flex items-center">
                                        <div class="ml-4 flex-1">
                                            <div class="text-sm font-semibold text-gray-900 mb-1">
                                                {{ $user->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ $user->nip ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $user->jabatan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-sm">
                                        {{ $user->bidang ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $user->role->role_name === 'Super Admin' ? 'bg-red-100 text-red-800' : 
                                           ($user->role->role_name === 'Admin' ? 'bg-blue-100 text-blue-800' : 
                                           ($user->role->role_name === 'Ketua Tim' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800')) }}">
                                        {{ $user->role->role_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button type="button" class="text-blue-500 hover:text-blue-700 btnEditPengguna"
                                            title="Edit Pengguna" data-id="{{ $user->id }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button type="button" class="text-red-500 hover:text-red-700 btnDeletePengguna"
                                            title="Hapus Pengguna" data-id="{{ $user->id }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                    </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum ada pengguna</h3>
                                        <p class="text-gray-500 mb-4 max-w-md text-center">Tambahkan pengguna baru untuk mengatur akses sistem</p>
                                        <button id="btnTambahPengguna2"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Tambah Pengguna Pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                </tbody>
            </table>
            </div>
            
            <!-- Mobile Card Layout -->
            <div class="md:hidden">
                @forelse($pengguna as $user)
                    <div class="p-4 border-b border-gray-200 last:border-b-0" 
                         data-search="{{ strtolower($user->name) }} {{ strtolower($user->email) }} {{ strtolower($user->nip) }} {{ strtolower($user->jabatan) }} {{ strtolower($user->bidang) }} {{ strtolower($user->role->role_name) }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ $user->name }}</h3>
                                <div class="space-y-1 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <span class="w-12 text-xs text-gray-500">NIP:</span>
                                        <span>{{ $user->nip ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-12 text-xs text-gray-500">Email:</span>
                                        <span class="truncate">{{ $user->email }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-12 text-xs text-gray-500">Bidang:</span>
                                        <span>{{ $user->bidang ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $user->jabatan }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $user->role->role_name === 'Super Admin' ? 'bg-red-100 text-red-800' : 
                                           ($user->role->role_name === 'Admin' ? 'bg-blue-100 text-blue-800' : 
                                           ($user->role->role_name === 'Ketua Tim' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800')) }}">
                                        {{ $user->role->role_name }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col space-y-2 ml-4">
                                <button type="button" class="text-blue-500 hover:text-blue-700 btnEditPengguna p-2"
                                    title="Edit Pengguna" data-id="{{ $user->id }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z">
                                        </path>
                                    </svg>
                                </button>
                                <button type="button" class="text-red-500 hover:text-red-700 btnDeletePengguna p-2"
                                    title="Hapus Pengguna" data-id="{{ $user->id }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum ada pengguna</h3>
                            <p class="text-gray-500 mb-4 max-w-md text-center">Tambahkan pengguna baru untuk mengatur akses sistem</p>
                            <button id="btnTambahPengguna3"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Pengguna Pertama
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
            @if (isset($pengguna) && $pengguna->total() > 0)
                <div class="px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        @php
                            $currentPage = max(1, $pengguna->currentPage());
                            $totalItems = $pengguna->total();
                            $perPage = $pengguna->perPage();
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
                    <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-2">
                        <div class="order-2 sm:order-1">
                            <select name="per_page" id="perPageSelect"
                                class="items-center px-3 py-1.5 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 text-sm">
                                @foreach ([5, 10, 15, 25, 50] as $option)
                                    <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                                        {{ $option }} </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-center space-x-1 sm:space-x-2 order-1 sm:order-2">
                            <!-- Previous Button -->
                            @if($hasPrevPage)
                                <a href="{{ $pengguna->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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
                                            <a href="{{ $pengguna->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
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
                                <a href="{{ $pengguna->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
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

    <!-- Tambahkan script untuk filter -->
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('tbody tr');
            const sortableHeaders = document.querySelectorAll('.sortable');

            // Highlight active sort column and set initial icon
            const currentUrl = new URL(window.location.href);
            const currentSortBy = currentUrl.searchParams.get('sort_by');
            const currentSortOrder = currentUrl.searchParams.get('sort_order');

            if (currentSortBy) {
                const activeHeader = document.querySelector(`.sortable[data-sort-by="${currentSortBy}"]`);
                if (activeHeader) {
                    activeHeader.classList.add('bg-gray-100'); // Add highlight
                    // Hide neutral icon, show correct sort icon
                    activeHeader.querySelector('.sort-neutral-icon')?.classList.add('hidden');
                    if (currentSortOrder === 'asc') {
                        activeHeader.querySelector('.sort-asc-icon')?.classList.remove('hidden');
                        activeHeader.querySelector('.sort-desc-icon')?.classList.add('hidden');
                    } else {
                        activeHeader.querySelector('.sort-desc-icon')?.classList.remove('hidden');
                        activeHeader.querySelector('.sort-asc-icon')?.classList.add('hidden');
                    }
                }
            }

            sortableHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const sortBy = this.getAttribute('data-sort-by');
                    const currentUrl = new URL(window.location.href);
                    const currentSortBy = currentUrl.searchParams.get('sort_by');
                    const currentSortOrder = currentUrl.searchParams.get('sort_order');

                    // Determine new sort order
                    let newSortOrder = 'asc';
                    if (currentSortBy === sortBy) {
                        newSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
                    }

                    // Update URL parameters
                    currentUrl.searchParams.set('sort_by', sortBy);
                    currentUrl.searchParams.set('sort_order', newSortOrder);
                    currentUrl.searchParams.delete('page'); // Reset pagination

                    // Redirect
                    window.location.href = currentUrl.toString();
                });
            });

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                let visibleCount = 0;

                tableRows.forEach(row => {
                    const searchContent = row.getAttribute('data-search').toLowerCase();
                    const visible = searchContent.includes(searchTerm);

                    row.style.display = visible ? '' : 'none';

                    if (visible) {
                        visibleCount++;
                    }
                });

                // Tampilkan pesan jika tidak ada hasil
                const noResultsRow = document.querySelector('tr.no-results');
                if (visibleCount === 0) {
                    if (!noResultsRow) {
                        const emptyRow = document.createElement('tr');
                        emptyRow.className = 'no-results';
                        emptyRow.innerHTML = `
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada hasil</h3>
                                    <p class="text-gray-500 text-center">Tidak ada pengguna yang cocok dengan pencarian Anda</p>
                                </div>
                            </td>
                        `;
                        document.querySelector('tbody').appendChild(emptyRow);
                    }
                } else if (noResultsRow) {
                    noResultsRow.remove();
                }
            }

            searchInput.addEventListener('input', filterTable);

            // Tambahkan event listener untuk tombol tambah pengguna kedua
            const btnTambahPengguna2 = document.getElementById('btnTambahPengguna2');
            if (btnTambahPengguna2) {
                btnTambahPengguna2.addEventListener('click', function() {
                    document.getElementById('btnTambahPengguna').click();
                });
            }

            // Tambahkan event listener untuk per page select
            document.getElementById('perPageSelect').addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.delete('page'); // Reset ke halaman pertama
                window.location.href = url.toString();
            });
        });
    </script>
    @endpush

    <!-- Modal Tambah Pengguna -->
    <div id="modalTambahPengguna" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="bg-white rounded-lg shadow-lg z-10 w-1/2 max-w-xl">
            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-lg font-medium text-red-600">Tambah Pengguna Baru</h2>
                <button id="btnClosePengguna" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <form id="formTambahPengguna" method="POST" action="{{ route('pengguna.store') }}">
                    @csrf
                    <input type="hidden" name="user_id" id="userId" value="">
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <!-- Nama -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" id="name" name="name" required
                                class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md pl-10 pr-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent"
                                placeholder="Masukkan nama lengkap">
                        </div>
                        <p id="nameError" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" required
                                class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md pl-10 pr-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent"
                                placeholder="Masukkan email">
                        </div>
                        <p id="emailError" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>

                    <!-- NIP -->
                    <div class="mb-4">
                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zm-6 8.414V17a1 1 0 001 1h10a1 1 0 001-1v-6.586L12.586 9A2 2 0 0012 8V6a2 2 0 10-4 0v2a2 2 0 00-.586 1.414L4 10.414zM10 20a2 2 0 100-4 2 2 0 000 4z" />
                                </svg>
                            </div>
                            <input type="text" id="nip" name="nip"
                                maxlength="18" pattern="[0-9]{18}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 18)"
                                class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md pl-10 pr-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent"
                                placeholder="Masukkan 18 digit NIP">
                        </div>
                        <p id="nipError" class="text-red-500 text-xs mt-1 hidden"></p>
                        <p class="text-gray-500 text-xs mt-1">NIP harus terdiri dari 18 digit angka</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Role -->
                        <div class="mb-4">
                            <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select id="role_id" name="role_id" required
                                class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent">
                                <option value="">Pilih Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                            <p id="roleError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>

                        <!-- Jabatan -->
                        <div class="mb-4">
                            <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                            <select id="jabatan" name="jabatan" required
                                class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent">
                                <option value="">Pilih Jabatan</option>
                                @foreach (\App\Models\Pengguna::getJabatanOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <p id="jabatanError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                    </div>

                    <!-- Bidang -->
                    <div class="mb-4">
                        <label for="bidang" class="block text-sm font-medium text-gray-700 mb-1">Bidang</label>
                        <select id="bidang" name="bidang" required
                            class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent">
                            <option value="">Pilih Bidang</option>
                            @foreach (\App\Models\Pengguna::getBidangOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p id="bidangError" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>

                    <!-- Password section - only shown for new users -->
                    <div id="passwordSection">
                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="password" id="password" name="password"
                                    class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md pl-10 pr-10 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent"
                                    placeholder="Buat kata sandi">
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eyeSlashIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <p id="passwordError" class="text-red-500 text-xs mt-1 hidden"></p>
                            <p class="text-gray-500 text-xs mt-1">Kata sandi harus memiliki minimal 8 karakter, huruf
                                besar, huruf kecil, dan angka.</p>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="mb-4">
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md pl-10 pr-10 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent"
                                    placeholder="Konfirmasi kata sandi">
                                <button type="button" id="togglePasswordConfirmation" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg id="eyeIconConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eyeSlashIconConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <p id="confirmError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" id="btnBatalPengguna"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md transition duration-300">
                            Batal
                        </button>
                        <button type="submit" id="btnSimpanPengguna"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal konfirmasi hapus akan menggunakan sistem global -->
@endsection

@push('scripts')
    <script>
        // Function untuk konfirmasi hapus pengguna
        function confirmDeleteUser(userId) {
            confirmDelete('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.', function() {
                // Submit delete request
                fetch("{{ route('pengguna.destroy') }}", {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            ids: [userId]
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Success - reload page
                            showSuccess('Pengguna berhasil dihapus');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // Show error
                            showError(data.message || 'Terjadi kesalahan saat menghapus pengguna');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('Terjadi kesalahan pada server');
                    });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const btnTambahPengguna = document.getElementById('btnTambahPengguna');
            const modalTambahPengguna = document.getElementById('modalTambahPengguna');
            const btnClosePengguna = document.getElementById('btnClosePengguna');
            const btnBatalPengguna = document.getElementById('btnBatalPengguna');
            const formTambahPengguna = document.getElementById('formTambahPengguna');
            const passwordSection = document.getElementById('passwordSection');
            const userId = document.getElementById('userId');
            const formMethod = document.getElementById('formMethod');

            // Fungsi untuk membuka modal dalam mode tambah
            function openAddModal() {
                // Reset form
                formTambahPengguna.reset();
                userId.value = '';
                formMethod.value = 'POST';

                // Tampilkan bagian password
                passwordSection.classList.remove('hidden');

                // Atur action form untuk tambah data
                formTambahPengguna.action = "{{ route('pengguna.store') }}";

                // Ubah judul modal
                document.querySelector('#modalTambahPengguna h2').textContent = 'Tambah Pengguna Baru';

                // Buka modal
                modalTambahPengguna.classList.remove('hidden');

                // Password required untuk tambah data
                document.getElementById('password').required = true;
                document.getElementById('password_confirmation').required = true;
            }

            // Fungsi untuk membuka modal dalam mode edit
            function openEditModal(id) {
                // Reset form terlebih dahulu
                formTambahPengguna.reset();

                // Atur user id
                userId.value = id;
                formMethod.value = 'PUT';

                // Sembunyikan bagian password
                passwordSection.classList.add('hidden');

                // Atur action form untuk update data
                formTambahPengguna.action = "{{ route('pengguna.update') }}";

                // Ubah judul modal
                document.querySelector('#modalTambahPengguna h2').textContent = 'Edit Pengguna';

                // Password tidak required untuk edit data
                document.getElementById('password').required = false;
                document.getElementById('password_confirmation').required = false;

                // Ambil data pengguna dengan AJAX
                fetch(`/manajemen-pengguna/${id}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        // Isi form dengan data yang diterima
                        document.getElementById('name').value = data.name;
                        document.getElementById('email').value = data.email;
                        document.getElementById('role_id').value = data.role_id;
                        document.getElementById('jabatan').value = data.jabatan;
                        document.getElementById('bidang').value = data.bidang || ''; // Tambahkan Bidang
                        document.getElementById('nip').value = data.nip || ''; // Tambahkan NIP

                        // Buka modal
                        modalTambahPengguna.classList.remove('hidden');
                    })
                    .catch(error => console.error('Error:', error));
            }



            // Tambah Pengguna button
            btnTambahPengguna.addEventListener('click', openAddModal);

            // Close modal buttons
            [btnClosePengguna, btnBatalPengguna].forEach(btn => {
                btn.addEventListener('click', function() {
                    modalTambahPengguna.classList.add('hidden');
                });
            });

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modalTambahPengguna.querySelector('.fixed.inset-0')) {
                    modalTambahPengguna.classList.add('hidden');
                }
            });

            // Edit buttons
            document.querySelectorAll('.btnEditPengguna').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    openEditModal(id);
                });
            });

            // Delete buttons
            document.querySelectorAll('.btnDeletePengguna').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    confirmDeleteUser(id);
                });
            });

            // Password visibility toggle functions
            function togglePasswordVisibility(inputId, iconId) {
                const passwordInput = document.getElementById(inputId);
                const eyeIcon = document.getElementById(iconId);
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414L8.464 8.464m5.656 5.656l1.415 1.415m-1.415-1.415l1.415 1.415M14.828 14.828L16.243 16.243"></path>';
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                }
            }

            // Add event listeners for password toggle buttons
            document.getElementById('togglePassword').addEventListener('click', function() {
                togglePasswordVisibility('password', 'eyeIconPassword');
            });

            document.getElementById('togglePasswordConfirmation').addEventListener('click', function() {
                togglePasswordVisibility('password_confirmation', 'eyeIconPasswordConfirmation');
            });

            // Handle form submission
            formTambahPengguna.addEventListener('submit', function(e) {
                e.preventDefault();

                // Reset error messages
                document.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));

                // Collect form data
                const formData = new FormData(formTambahPengguna);

                // Add method override for PUT if editing
                if (userId.value) {
                    formData.append('_method', 'PUT');
                }

                // Submit form
                fetch(formTambahPengguna.action, {
                        method: 'POST', // Always POST because of Laravel's form method spoofing
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Success - show notification and reload
                            showSuccess('Pengguna berhasil disimpan');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // Show validation errors
                            if (data.errors) {
                                for (const [field, message] of Object.entries(data.errors)) {
                                    const errorElement = document.getElementById(`${field}Error`);
                                    if (errorElement) {
                                        errorElement.textContent = Array.isArray(message) ? message[0] :
                                            message; // Laravel returns arrays of errors
                                        errorElement.classList.remove('hidden');
                                    }
                                }
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });


        });
    </script>
@endpush
