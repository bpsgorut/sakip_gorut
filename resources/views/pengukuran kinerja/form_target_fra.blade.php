@extends('components.master')

@section('title', 'Form Target FRA')

@section('content')

    @include('components.breadcrumbs')

    <div class="min-h-screen">
        <div class="container mx-auto py-6">
            <!-- Header Section -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-red-700 opacity-50"></div>
                    <img class="w-full h-40 object-cover" src="{{ asset('img/bg5.jpg') }}" alt="">
                    <div class="absolute inset-0 flex items-center justify-between p-6 z-10">
                        <div class="text-white">
                            <h1 class="text-3xl font-bold mb-2">Form Target FRA</h1>
                            <p class="text-red-100 text-lg">Tahun {{ $fra->tahun_berjalan }}</p>
                        </div>
                        <div class="text-white text-right">
                            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-medium">Target Setting</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Container -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <form action="{{ route('simpan.target.fra', $fra->id ?? 0) }}" method="POST" id="targetForm">
                    @csrf
                    <input type="hidden" name="action_type" id="actionType" value="save">

                    <!-- Progress Indicator -->
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Progress Pengisian</span>
                            <span class="text-sm text-gray-500" id="progressText">0% selesai</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-red-500 to-red-600 h-2 rounded-full transition-all duration-500"
                                id="progressBar" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="px-6 pt-6">
                        <div class="flex mb-6 border-b border-gray-200">
                            <button id="tab-iku" type="button"
                                class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-t-xl border-b-2 border-red-600 transition-all duration-200 mr-2">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    IKU
                                </div>
                            </button>
                            @if (isset($hasSuplemenData) && $hasSuplemenData)
                                <button id="tab-suplemen" type="button"
                                    class="px-6 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium rounded-t-xl transition-all duration-200 mr-2">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Suplemen
                                    </div>
                                </button>
                            @endif
                            @php
                                $hasUmumData = $fra->matriks_fra->some(function ($matriks) {
                                    return optional(optional($matriks)->template_fra)->template_jenis->nama === 'PK Umum';
                                });
                            @endphp
                            @if ($hasUmumData)
                                <button id="tab-umum" type="button"
                                    class="px-6 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium rounded-t-xl transition-all duration-200 mr-2">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"
                                                clip-rule="evenodd" />
                                            <path fill-rule="evenodd"
                                                d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM7 7a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm1 3a1 1 0 100 2h4a1 1 0 100-2H8z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Umum
                                    </div>
                                </button>
                            @endif
                            <button id="tab-petunjuk" type="button"
                                class="px-6 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium rounded-t-xl transition-all duration-200">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Petunjuk Pengisian
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Content Tabs -->
                    <div class="tab-content px-6 pb-24">
                        <!-- Konten Tab IKU -->
                        <div id="content-iku" class="space-y-6">
                            @php
                                $currentTemplateJenis = 'PK IKU';
                                
                                // Filter dan kelompokkan data berdasarkan tujuan
                                $dataByTujuan = $fra->matriks_fra
                                    ->filter(function ($matriks) use ($currentTemplateJenis) {
                                        return $matriks->template_fra->template_jenis->nama === $currentTemplateJenis;
                                    })
                                    ->sortBy([
                                        ['tujuan', 'asc'],
                                        ['sasaran', 'asc'], 
                                        ['indikator', 'asc']
                                    ])
                                    ->groupBy('tujuan');
                            @endphp

                            @forelse($dataByTujuan as $tujuan => $matriksList)
                                <!-- Card Tujuan -->
                                <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden mb-8">
                                    <!-- Header Tujuan -->
                                    <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                                        <h2 class="font-bold text-white text-lg flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            {{ $tujuan }}
                                        </h2>
                                    </div>

                                    <!-- Konten Tujuan -->
                                    <div class="p-6">
                                        @php
                                            // Kelompokkan berdasarkan sasaran
                                            $dataBySasaran = $matriksList->groupBy('sasaran');
                                        @endphp

                                        @foreach($dataBySasaran as $sasaran => $indikatorList)
                                            <!-- Card Sasaran -->
                                            <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden {{ !$loop->first ? 'mt-6' : '' }}">
                                                <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                                                    <h3 class="font-semibold text-gray-800 flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 mr-2 text-red-600" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                        </svg>
                                                        {{ $sasaran }}
                                                    </h3>
                                                </div>

                                                <!-- Tabel Indikator -->
                                                <div class="overflow-x-auto">
                                                    <table class="w-full bg-white table-fixed">
                                                        <thead>
                                                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                                                <th class="text-left py-3 px-4 font-semibold text-gray-700" style="width: 45%">Indikator</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 7%">Satuan</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 7%">TW I</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 7%">TW II</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 7%">TW III</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 7%">TW IV</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 8%">PK</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 12%">PIC</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                // Kelompokkan berdasarkan indikator untuk menangani hierarki dengan benar
                                                                $dataByIndikator = $indikatorList->groupBy('indikator');
                                                            @endphp

                                                            @foreach($dataByIndikator as $indikatorName => $rowsForIndikator)
                                                                @php
                                                                    $mainMatriks = $rowsForIndikator->first();
                                                                    
                                                                    $xy_regex = fn($char) => '/^' . $char . '[\.:\s]/i';

                                                                    // Cek apakah grup ini memiliki anak X dan Y untuk perhitungan otomatis
                                                                    // Hanya cek di level detail_indikator untuk menentukan apakah indikator utama dihitung otomatis
                                                                    $hasXChild = $rowsForIndikator->contains(function($row) use ($xy_regex) {
                                                                        return !empty($row->detail_indikator) && preg_match($xy_regex('x'), strip_tags($row->detail_indikator));
                                                                    });
                                                                    $hasYChild = $rowsForIndikator->contains(function($row) use ($xy_regex) {
                                                                        return !empty($row->detail_indikator) && preg_match($xy_regex('y'), strip_tags($row->detail_indikator));
                                                                    });
                                                                    $isGroupCalculated = $hasXChild && $hasYChild;

                                                                    $virtualRows = [];
                                                                    
                                                                    // Level 1: Indikator utama (selalu ada)
                                                                    $virtualRows[] = [
                                                                        'matriks' => $mainMatriks,
                                                                        'level' => 'main',
                                                                        'content' => $mainMatriks->indikator,
                                                                        'isCalculated' => $isGroupCalculated,
                                                                        'showPK' => true,
                                                                        'isX' => false,
                                                                        'isY' => false,
                                                                    ];
                                                                    
                                                                    // Kumpulkan semua anak dari grup indikator
                                                                    foreach ($rowsForIndikator as $childMatriks) {
                                                                        if (!empty($childMatriks->detail_indikator)) {
                                                                            $content = $childMatriks->detail_indikator;
                                                                            // ✅ FIXED: Tampilkan target PK untuk semua level yang memiliki data target PK
                                                                            $hasTargetPk = $targetPkData->get($childMatriks->id) !== null;
                                                                            $virtualRows[] = [
                                                                                'matriks' => $childMatriks, 'level' => 'detail_indicator', 'content' => $content,
                                                                                'isCalculated' => false, 'showPK' => $hasTargetPk,
                                                                                'isX' => preg_match($xy_regex('x'), strip_tags($content)), 'isY' => preg_match($xy_regex('y'), strip_tags($content)),
                                                                            ];
                                                                        }
                                                                        if (!empty($childMatriks->sub_indikator)) {
                                                                            $content = $childMatriks->sub_indikator;

                                                                            // Periksa apakah sub-indikator ini dapat dihitung
                                                                            $isSubIndicatorCalculated = false;
                                                                            $isParent = !preg_match($xy_regex('x'), strip_tags($content)) && !preg_match($xy_regex('y'), strip_tags($content));
                                                                            if($isParent){
                                                                                $subIndicatorChildrenRows = $rowsForIndikator->filter(fn($r) => $r->sub_indikator === $content && !empty($r->detail_sub));
                                                                                $hasXChild_sub = $subIndicatorChildrenRows->contains(fn($r) => preg_match($xy_regex('x'), strip_tags($r->detail_sub)));
                                                                                $hasYChild_sub = $subIndicatorChildrenRows->contains(fn($r) => preg_match($xy_regex('y'), strip_tags($r->detail_sub)));
                                                                                $isSubIndicatorCalculated = $hasXChild_sub && $hasYChild_sub;
                                                                                
                                                                                // Debug: Log sub-indicator calculation detection
                                                                                if($isSubIndicatorCalculated) {
                                                                                    \Log::info("Sub-indicator '{$content}' will be calculated automatically (has X and Y children)");
                                                                                }
                                                                            }

                                                                            // ✅ FIXED: Tampilkan target PK untuk sub-indikator yang memiliki data target PK
                                                                            $hasTargetPk = $targetPkData->get($childMatriks->id) !== null;
                                                                            $virtualRows[] = [
                                                                                'matriks' => $childMatriks, 'level' => 'sub', 'content' => $content,
                                                                                'isCalculated' => $isSubIndicatorCalculated,
                                                                                'showPK' => $hasTargetPk,
                                                                                'isX' => preg_match($xy_regex('x'), strip_tags($content)), 'isY' => preg_match($xy_regex('y'), strip_tags($content)),
                                                                            ];
                                                                        }
                                                                        if (!empty($childMatriks->detail_sub)) {
                                                                            $content = $childMatriks->detail_sub;
                                                                            // ✅ FIXED: Tampilkan target PK untuk detail_sub yang memiliki data target PK
                                                                            $hasTargetPk = $targetPkData->get($childMatriks->id) !== null;
                                                                            $virtualRows[] = [
                                                                                'matriks' => $childMatriks, 'level' => 'detail_sub', 'content' => $content,
                                                                                'isCalculated' => false, 'showPK' => $hasTargetPk,
                                                                                'isX' => preg_match($xy_regex('x'), strip_tags($content)), 'isY' => preg_match($xy_regex('y'), strip_tags($content)),
                                                                            ];
                                                                        }
                                                                    }
                                                                    
                                                                    // Hapus duplikat jika ada, berdasarkan konten dan level
                                                                    $virtualRows = collect($virtualRows)->unique(function ($item) {
                                                                        return $item['level'] . '_' . $item['content'];
                                                                    })->values()->all();
                                                                @endphp
                                                                
                                                                @foreach($virtualRows as $vRow)
                                                                    @php
                                                                        $matriks = $vRow['matriks'];
                                                                        // Data is now attached directly to the matriks object from the controller
                                                                        $existingTarget = $matriks->existing_target; 
                                                                        $targetPk = $matriks->target_pk_data; 
                                                                        
                                                                        $isYRow = $vRow['isY'];
                                                                        $isCalculatedRow = $vRow['isCalculated'];
                                                                        $isReadOnly = $isCalculatedRow;
                                                                        
                                                                        // Debug: Log calculated row creation for sub-indicators
                                                                        if($vRow['level'] == 'sub' && $isCalculatedRow) {
                                                                            \Log::info("Creating calculated sub-indicator row: {$vRow['content']} (readonly: {$isReadOnly})");
                                                                        }
                                                                        
                                                                        $yValue = null;
                                                                    @endphp
                                                                    
                                                                    {{-- Warning Row --}}
                                                                    <tr class="warning-row hidden" data-warning-for-row="{{ $matriks->id }}">
                                                                        <td class="pb-1"></td> {{-- Indikator --}}
                                                                        <td class="pb-1"></td> {{-- Satuan --}}
                                                                        <td class="warning-cell pb-1" data-tw="1"></td>
                                                                        <td class="warning-cell pb-1" data-tw="2"></td>
                                                                        <td class="warning-cell pb-1" data-tw="3"></td>
                                                                        <td class="warning-cell pb-1" data-tw="4"></td>
                                                                        <td class="pb-1"></td> {{-- PK --}}
                                                                        <td class="pb-1"></td> {{-- PIC --}}
                                                                    </tr>

                                                                    {{-- Data Row --}}
                                                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150" 
                                                                        data-group-id="{{ $mainMatriks->id }}" 
                                                                        data-level="{{ $vRow['level'] }}"
                                                                        data-content="{{ $vRow['content'] }}"
                                                                        @if($vRow['level'] == 'detail_sub')
                                                                            data-parent-sub-indicator="{{ $matriks->sub_indikator ?? '' }}"
                                                                        @endif
                                                                        >
                                                                        <td class="py-2 px-4 align-middle">
                                                                            @if ($vRow['level'] == 'main')
                                                                                <div class="flex items-start">
                                                                                    <div class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                                                    <span class="font-bold text-gray-800 leading-relaxed">{{ $vRow['content'] }}</span>
                                                                                </div>
                                                                            @elseif ($vRow['level'] == 'detail_indicator')
                                                                                <div class="flex items-start ml-4">
                                                                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                                                    <span class="text-gray-800 leading-relaxed">{{ $vRow['content'] }}</span>
                                                                                </div>
                                                                            @elseif ($vRow['level'] == 'sub')
                                                                                <div class="flex items-start ml-10">
                                                                                    <div class="w-2 h-2 bg-orange-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                                                    <span class="font-bold text-gray-800 leading-relaxed">{{ $vRow['content'] }}</span>
                                                                                </div>
                                                                            @else {{-- detail_sub --}}
                                                                                <div class="flex items-start ml-20">
                                                                                    <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                                                    <span class="text-gray-800 leading-relaxed">{{ $vRow['content'] }}</span>
                                                                                </div>
                                                                            @endif
                                                                        </td>
                                                                        
                                                                        <td class="py-2 px-2 align-middle">
                                                                            <div class="text-center">
                                                                                <span class="inline-block bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">{{ $matriks->satuan ?? '-' }}</span>
                                                                            </div>
                                                                        </td>
                                                                        
                                                                        {{-- Input fields --}}
                                                                        <td class="py-2 px-2 align-middle">
                                                                            <input type="number" name="target[{{ $matriks->id }}][tw1]" 
                                                                                class="target-input w-full h-9 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 {{ $isReadOnly ? 'bg-gray-100' : '' }}" 
                                                                                placeholder="0" step="any" min="0" data-tw="1" value="{{ $existingTarget->target_tw1 ?? '' }}"
                                                                                {{ $isReadOnly ? 'readonly' : '' }}
                                                                                 @if($vRow['isX']) data-child-x-for="{{ $mainMatriks->id }}" @endif
                                                                                 @if($vRow['isY']) data-child-y-for="{{ $mainMatriks->id }}" @endif
                                                                                 >
                                                                        </td>
                                                                        <td class="py-2 px-2 align-middle">
                                                                            <input type="number" name="target[{{ $matriks->id }}][tw2]" 
                                                                                class="target-input w-full h-9 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 {{ $isReadOnly ? 'bg-gray-100' : '' }}" 
                                                                                placeholder="0" step="any" min="0" data-tw="2" value="{{ $existingTarget->target_tw2 ?? '' }}"
                                                                                {{ $isReadOnly ? 'readonly' : '' }}
                                                                                 @if($vRow['isX']) data-child-x-for="{{ $mainMatriks->id }}" @endif
                                                                                 @if($vRow['isY']) data-child-y-for="{{ $mainMatriks->id }}" @endif
                                                                                 >
                                                                        </td>
                                                                        <td class="py-2 px-2 align-middle">
                                                                            <input type="number" name="target[{{ $matriks->id }}][tw3]" 
                                                                                class="target-input w-full h-9 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 {{ $isReadOnly ? 'bg-gray-100' : '' }}" 
                                                                                placeholder="0" step="any" min="0" data-tw="3" value="{{ $existingTarget->target_tw3 ?? '' }}"
                                                                                {{ $isReadOnly ? 'readonly' : '' }}
                                                                                 @if($vRow['isX']) data-child-x-for="{{ $mainMatriks->id }}" @endif
                                                                                 @if($vRow['isY']) data-child-y-for="{{ $mainMatriks->id }}" @endif
                                                                                 >
                                                                        </td>
                                                                        <td class="py-2 px-2 align-middle">
                                                                            <input type="number" name="target[{{ $matriks->id }}][tw4]" 
                                                                                class="target-input w-full h-9 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 {{ $isReadOnly ? 'bg-gray-100' : '' }}" 
                                                                                placeholder="0" step="any" min="0" data-tw="4" value="{{ $existingTarget->target_tw4 ?? '' }}"
                                                                                {{ $isReadOnly ? 'readonly' : '' }}
                                                                                 @if($vRow['isX']) data-child-x-for="{{ $mainMatriks->id }}" @endif
                                                                                 @if($vRow['isY']) data-child-y-for="{{ $mainMatriks->id }}" @endif
                                                                                 >
                                                                        </td>
                                                                        
                                                                        {{-- Kolom PK --}}
                                                                        <td class="py-2 px-2 align-middle">
                                                                             @if($vRow['showPK'])
                                                                                 @php
                                                                                     // ✅ FIXED: Gunakan ID matriks yang sesuai untuk setiap level
                                                                                     $targetPkForDisplay = $targetPkData->get($matriks->id);
                                                                                     $targetPkValue = $targetPkForDisplay ? $targetPkForDisplay->target_pk : null;
                                                                                     
                                                                                     $pkDisplay = '-';
                                                                                     if (!is_null($targetPkValue)) {
                                                                                         $pkDisplay = (floor($targetPkValue) == $targetPkValue)
                                                                                             ? number_format($targetPkValue, 0, '.', ',')
                                                                                             : number_format($targetPkValue, 2, '.', ',');
                                                                                     }
                                                                                 @endphp
                                                                                 <div class="text-center">
                                                                                     <span class="pk-value inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded-lg text-xs font-medium" data-matriks-id="{{ $matriks->id }}" data-pk-value="{{ $targetPkValue }}">
                                                                                         {{ $pkDisplay }}
                                                                                     </span>
                                                                                 </div>
                                                                             @else
                                                                                 <div class="text-center align-middle">-</div>
                                                                             @endif
                                                                        </td>
                                                                        
                                                                        {{-- PIC --}}
                                                                        <td class="py-2 px-2 align-middle">
                                                                            @php
                                                                                $depthMap = ['main' => 0, 'detail_indicator' => 1, 'sub' => 2, 'detail_sub' => 3];
                                                                                $maxDepth = 0;
                                                                                foreach ($virtualRows as $row) {
                                                                                    $maxDepth = max($maxDepth, $depthMap[$row['level']]);
                                                                                }
                                                                                
                                                                                // ✅ FIXED: PIC juga ditampilkan di level detail_indicator (X dan Y) dan level hierarki terkecil
                                                                                $shouldShowPIC = ($depthMap[$vRow['level']] == $maxDepth && !$vRow['isCalculated']) 
                                                                                              || ($vRow['level'] == 'detail_indicator' && !$vRow['isCalculated']);
                                                                            @endphp
                                                                            @if($shouldShowPIC && !$isYRow)
                                                                                <select name="assign_id[{{ $matriks->id }}]" class="w-full h-9 border border-gray-300 rounded-lg px-2 py-1 text-xs bg-white pic-select focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
                                                                                    <option value="" class="text-gray-500">Pilih PIC</option>
                                                                                    @foreach($penggunas as $pengguna)
                                                                                        <option value="{{ $pengguna->id }}" {{ ($existingTarget ? $existingTarget->assign_id : '') == $pengguna->id ? 'selected' : '' }}>
                                                                                            {{ $pengguna->name }} ({{ $pengguna->jabatan }} - {{ $pengguna->bidang }})
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data IKU</h3>
                                    <p class="text-gray-500">Data indikator kinerja utama belum tersedia untuk tahun ini.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Konten Tab Suplemen -->
                        @if (isset($hasSuplemenData) && $hasSuplemenData)
                            <div id="content-suplemen" class="space-y-6 hidden">
                                @php
                                    $currentTemplateJenis = 'PK Suplemen';
                                    
                                    // Filter dan kelompokkan data berdasarkan tujuan
                                    $dataByTujuan = $fra->matriks_fra
                                        ->filter(function ($matriks) use ($currentTemplateJenis) {
                                            return $matriks->template_fra->template_jenis->nama === $currentTemplateJenis;
                                        })
                                        ->sortBy([
                                            ['tujuan', 'asc'],
                                            ['sasaran', 'asc'], 
                                            ['indikator', 'asc'],
                                            ['detail_indikator', 'asc'],
                                            ['sub_indikator', 'asc'],
                                            ['detail_sub', 'asc']
                                        ])
                                        ->groupBy('tujuan');
                                @endphp

                                @forelse($dataByTujuan as $tujuan => $matriksList)
                                    <!-- Card Tujuan -->
                                    <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden mb-8">
                                        <!-- Header Tujuan -->
                                        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                                            <h2 class="font-bold text-white text-lg flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                {{ $tujuan }}
                                            </h2>
                                        </div>

                                        <!-- Konten Tujuan -->
                                        <div class="p-6">
                                            @php
                                                // Kelompokkan berdasarkan sasaran
                                                $dataBySasaran = $matriksList->groupBy('sasaran');
                                            @endphp

                                            @foreach($dataBySasaran as $sasaran => $indikatorList)
                                                <!-- Card Sasaran -->
                                                <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden {{ !$loop->first ? 'mt-6' : '' }}">
                                                    <div class="bg-green-50 px-4 py-3 border-b border-green-200">
                                                        <h3 class="font-semibold text-gray-800 flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-5 w-5 mr-2 text-green-600" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                            </svg>
                                                            {{ $sasaran }}
                                                        </h3>
                                                    </div>

                                                    <!-- Tabel Indikator -->
                                                    <div class="overflow-x-auto">
                                                        <table class="w-full bg-white table-fixed">
                                                            <thead>
                                                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                                                    <th class="text-left py-3 px-4 font-semibold text-gray-700" style="width: 30%">Indikator</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 8%">Satuan</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 8%">TW I</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 8%">TW II</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 8%">TW III</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 8%">TW IV</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 10%">Target PK</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 12%">PIC</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                        @foreach($indikatorList as $matriks)
                                                                    @php
                                                                        $existingTarget = $existingTargets->get($matriks->id);
                                                                        
                                                                        // Tentukan level dan format tampilan
                                                                        $content = '';
                                                                        $marginClass = '';
                                                                        $textClass = '';
                                                                        $showInput = true;
                                                                    @endphp
                                                                    
                                                                    @if (!empty($matriks->detail_sub))
                                                                        @php
                                                                            $content = $matriks->detail_sub;
                                                                            $marginClass = 'ml-20';
                                                                            $textClass = 'text-gray-600';
                                                                        @endphp
                                                                    @elseif (!empty($matriks->sub_indikator))
                                                                        @php
                                                                            $content = $matriks->sub_indikator;
                                                                            $marginClass = 'ml-10';
                                                                            $textClass = 'text-gray-700';
                                                                        @endphp
                                                                    @elseif (!empty($matriks->detail_indikator))
                                                                        @php
                                                                            $content = $matriks->detail_indikator;
                                                                            $marginClass = 'ml-4';
                                                                            $textClass = 'font-medium text-blue-800';
                                                                        @endphp
                                                                    @else
                                                                        @php
                                                                            $content = $matriks->indikator;
                                                                            $textClass = 'font-medium text-gray-900';
                                                                        @endphp
                                                                    @endif
                                                                    
                                                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150" data-group-id="{{ $mainMatriks->id }}" data-level="{{ $vRow['level'] }}">
                                                                    <td class="py-4 px-4">
                                                                        <div class="flex items-start {{ $marginClass }}">
                                                                            @if (!empty($marginClass))
                                                                                <div class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                                            @else
                                                                                <div class="w-2 h-2 bg-green-700 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                                            @endif
                                                                            <span class="{{ $textClass }} leading-relaxed">{{ $content }}</span>
                                                                        </div>
                                                                    </td>
                                                                    
                                                                    <td class="py-4 px-2">
                                                                        <div class="text-center">
                                                                            <span class="inline-block bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">{{ $matriks->satuan ?? '-' }}</span>
                                                                        </div>
                                                                    </td>
                                                                    
                                                                    {{-- Input fields --}}
                                                                    <td class="py-4 px-2">
                                                                        <input type="number" 
                                                                            name="target[{{ $matriks->id }}][tw1]"
                                                                            class="w-full h-9 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                                                            placeholder="0.00"
                                                                            step="0.01"
                                                                            min="0"
                                                                            value="{{ $existingTarget ? $existingTarget->target_tw1 : '' }}">
                                                                    </td>
                                                                    <td class="py-4 px-2">
                                                                        <input type="number" 
                                                                            name="target[{{ $matriks->id }}][tw2]"
                                                                            class="w-full h-9 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                                                            placeholder="0.00"
                                                                            step="0.01"
                                                                            min="0"
                                                                            value="{{ $existingTarget ? $existingTarget->target_tw2 : '' }}">
                                                                    </td>
                                                                    <td class="py-4 px-2">
                                                                        <input type="number" 
                                                                            name="target[{{ $matriks->id }}][tw3]"
                                                                            class="w-full h-9 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                                                            placeholder="0.00"
                                                                            step="0.01"
                                                                            min="0"
                                                                            value="{{ $existingTarget ? $existingTarget->target_tw3 : '' }}">
                                                                    </td>
                                                                    <td class="py-4 px-2">
                                                                        <input type="number" 
                                                                            name="target[{{ $matriks->id }}][tw4]"
                                                                            class="w-full h-9 px-2 py-1 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                                                            placeholder="0.00"
                                                                            step="0.01"
                                                                            min="0"
                                                                            value="{{ $existingTarget ? $existingTarget->target_tw4 : '' }}">
                                                                    </td>
                                                                    
                                                                    {{-- Target PK --}}
                                                                    <td class="py-4 px-2">
                                                                        @php
                                                                            $targetPk = $targetPkData->firstWhere('matriks_fra_id', $matriks->id);
                                                                            $targetPkValue = $targetPk ? $targetPk->target_pk : 0;
                                                                        @endphp
                                                                        <div class="text-center">
                                                                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded-lg text-xs font-medium">
                                                                                {{ number_format($targetPkValue, 2) }}
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    
                                                                    {{-- PIC --}}
                                                                    <td class="py-4 px-2">
                                                                        <select name="assign_id[{{ $matriks->id }}]"
                                                                            class="w-full h-9 border border-gray-300 rounded-lg px-2 py-1 text-xs bg-white pic-select focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                                                                            <option value="" class="text-gray-500">Pilih PIC</option>
                                                                            @foreach($penggunas as $pengguna)
                                                                                <option value="{{ $pengguna->id }}"
                                                                                    {{ ($existingTarget ? $existingTarget->assign_id : '') == $pengguna->id ? 'selected' : '' }}>
                                                                                    {{ $pengguna->name }} ({{ $pengguna->jabatan }})
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                        @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data Suplemen</h3>
                                        <p class="text-gray-500">Data suplemen belum tersedia untuk tahun ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        @endif

                        <!-- Konten Tab Umum -->
                        @if ($hasUmumData)
                            <div id="content-umum" class="space-y-6 hidden">
                                @php
                                    $currentTemplateJenis = 'PK Umum';
                                    
                                    // Filter dan kelompokkan data berdasarkan tujuan
                                    $dataByTujuan = $fra->matriks_fra
                                        ->filter(function ($matriks) use ($currentTemplateJenis) {
                                            return $matriks->template_fra->template_jenis->nama === $currentTemplateJenis;
                                        })
                                        ->sortBy([
                                            ['tujuan', 'asc'],
                                            ['sasaran', 'asc'], 
                                            ['indikator', 'asc'],
                                            ['detail_indikator', 'asc'],
                                            ['sub_indikator', 'asc'],
                                            ['detail_sub', 'asc']
                                        ])
                                        ->groupBy('tujuan');
                                @endphp

                                @forelse($dataByTujuan as $tujuan => $matriksList)
                                    <!-- Card Tujuan -->
                                    <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden mb-8">
                                        <!-- Header Tujuan -->
                                        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                                            <h2 class="font-bold text-white text-lg flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                {{ $tujuan }}
                                            </h2>
                                        </div>

                                        <!-- Konten Tujuan -->
                                        <div class="p-6">
                                            @php
                                                // Kelompokkan berdasarkan sasaran
                                                $dataBySasaran = $matriksList->groupBy('sasaran');
                                            @endphp

                                            @foreach($dataBySasaran as $sasaran => $indikatorList)
                                                <!-- Card Sasaran -->
                                                <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden {{ !$loop->first ? 'mt-6' : '' }}">
                                                    <div class="bg-purple-50 px-4 py-3 border-b border-purple-200">
                                                        <h3 class="font-semibold text-gray-800 flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-5 w-5 mr-2 text-purple-600" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                            </svg>
                                                            {{ $sasaran }}
                                                        </h3>
                                                    </div>

                                                    <!-- Tabel Indikator -->
                                                    <div class="overflow-x-auto">
                                                        <table class="w-full bg-white table-fixed">
                                                            <thead>
                                                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                                                    <th class="text-left py-3 px-4 font-semibold text-gray-700" style="width: 30%">Indikator</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 8%">Satuan</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 8%">TW I</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 8%">TW II</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 8%">TW III</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 8%">TW IV</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 10%">Target PK</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 12%">PIC</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($indikatorList as $matriks)
                                                                    @php
                                                                        $existingTarget = $existingTargets->get($matriks->id);
                                                                        
                                                                        // Untuk single row storage, kita perlu membuat "virtual rows" untuk setiap level
                                                                        $virtualRows = [];
                                                                        
                                                                        // Level 1: Indikator utama
                                                                        $virtualRows[] = [
                                                                            'id' => $matriks->id . '_main',
                                                                            'matriks_id' => $matriks->id,
                                                                            'level' => 'main',
                                                                            'content' => $matriks->indikator,
                                                                            'satuan' => $matriks->satuan,
                                                                            'isCalculated' => false,
                                                                            'showInput' => true
                                                                        ];
                                                                        
                                                                        // Level 2: Detail indikator (jika ada)
                                                                        if (!empty($matriks->detail_indikator)) {
                                                                            $virtualRows[] = [
                                                                                'id' => $matriks->id . '_detail',
                                                                                'matriks_id' => $matriks->id,
                                                                                'level' => 'detail_indicator',
                                                                                'content' => $matriks->detail_indikator,
                                                                                'satuan' => $matriks->satuan,
                                                                                'isCalculated' => false,
                                                                                'showInput' => true
                                                                            ];
                                                                        }
                                                                        
                                                                        // Level 3: Sub indikator (jika ada)
                                                                        if (!empty($matriks->sub_indikator)) {
                                                                            $virtualRows[] = [
                                                                                'id' => $matriks->id . '_sub',
                                                                                'matriks_id' => $matriks->id,
                                                                                'level' => 'sub',
                                                                                'content' => $matriks->sub_indikator,
                                                                                'satuan' => $matriks->satuan,
                                                                                'isCalculated' => false,
                                                                                'showInput' => true
                                                                            ];
                                                                        }
                                                                        
                                                                        // Level 4: Detail sub (jika ada)
                                                                        if (!empty($matriks->detail_sub)) {
                                                                            $virtualRows[] = [
                                                                                'id' => $matriks->id . '_detail_sub',
                                                                                'matriks_id' => $matriks->id,
                                                                                'level' => 'detail_sub',
                                                                                'content' => $matriks->detail_sub,
                                                                                'satuan' => $matriks->satuan,
                                                                                'isCalculated' => false,
                                                                                'showInput' => true
                                                                            ];
                                                                        }
                                                                        
                                                                        // Prioritas: detail_sub > sub_indikator > detail_indikator > indikator
                                                                        if (!empty($matriks->detail_x) || !empty($matriks->detail_y) || !empty($matriks->detail_pembilang) || !empty($matriks->detail_penyebut)) {
                                                                            // Level 4: Detail Sub (detail_x, detail_y, detail_pembilang, detail_penyebut)
                                                                            if (!empty($matriks->detail_x)) {
                                                                                $content = $matriks->detail_x;
                                                                            } elseif (!empty($matriks->detail_y)) {
                                                                                $content = $matriks->detail_y;
                                                                            } elseif (!empty($matriks->detail_pembilang)) {
                                                                                $content = $matriks->detail_pembilang;
                                                                            } elseif (!empty($matriks->detail_penyebut)) {
                                                                                $content = $matriks->detail_penyebut;
                                                                            }
                                                                            $marginClass = 'ml-12';
                                                                            $textClass = 'text-gray-600 text-sm';
                                                                            $showInput = true;
                                                                            $showPic = true;
                                                                        } elseif (!empty($matriks->sub_indikator)) {
                                                                            // Level 3: Sub Indikator
                                                                            $content = $matriks->sub_indikator;
                                                                            $marginClass = 'ml-8';
                                                                            $textClass = 'text-gray-700';
                                                                            $showInput = true;
                                                                            $showPic = true;
                                                                        } elseif (!empty($matriks->x) || !empty($matriks->y) || !empty($matriks->pembilang) || !empty($matriks->penyebut)) {
                                                                            // Level 2: Detail Indikator (x, y, pembilang, penyebut)
                                                                            if (!empty($matriks->x)) {
                                                                                $content = $matriks->x;
                                                                            } elseif (!empty($matriks->y)) {
                                                                                $content = $matriks->y;
                                                                            } elseif (!empty($matriks->pembilang)) {
                                                                                $content = $matriks->pembilang;
                                                                            } elseif (!empty($matriks->penyebut)) {
                                                                                $content = $matriks->penyebut;
                                                                            }
                                                                            $marginClass = 'ml-4';
                                                                            $textClass = 'text-gray-700';
                                                                            $showInput = true;
                                                                            $showPic = true;
                                                                        } else {
                                                                            // Level 1: Indikator Utama
                                                                            $content = $matriks->indikator;
                                                                            $marginClass = '';
                                                                            $textClass = 'text-gray-800 font-semibold';
                                                                            $showInput = true;
                                                                            $showPic = true;
                                                                        }

                                                                        $existingTarget = $existingTargets->get($matriks->id);
                                                                        
                                                                        // Tentukan level berdasarkan field yang ada
                                                                        if (!empty($matriks->detail_sub)) {
                                                                            $currentLevel = 'detail_sub';
                                                                        } elseif (!empty($matriks->sub_indikator)) {
                                                                            $currentLevel = 'sub';
                                                                        } elseif (!empty($matriks->detail_indikator)) {
                                                                            $currentLevel = 'detail_indicator';
                                                                        } else {
                                                                            $currentLevel = 'main';
                                                                        }
                                                                    @endphp
                                                                    
                                                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150" 
                                                                        data-group-id="{{ $matriks->id }}" 
                                                                        data-level="{{ $currentLevel }}"
                                                                        data-content="{{ $content }}"
                                                                        @if($currentLevel == 'detail_sub')
                                                                            data-parent-sub-indicator="{{ $matriks->sub_indikator ?? '' }}"
                                                                        @endif
                                                                        >
                                                                        <td class="py-4 px-4">
                                                                            <div class="flex items-start {{ $marginClass }}">
                                                                                <span class="{{ $textClass }} leading-relaxed">{{ $content }}</span>
                                                                            </div>
                                                                        </td>
                                                                        <td class="py-4 px-2">
                                                                            <div class="text-center">
                                                                                <span class="inline-block bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">{{ $matriks->satuan ?? '-' }}</span>
                                                                            </div>
                                                                        </td>
                                                                        @if($showInput)
                                                                            <!-- Target TW I -->
                                                                            <td class="py-4 px-2">
                                                                                @php
                                                                                    $isXField = stripos($content, 'X:') !== false || 
                                                                                              stripos($content, 'x:') !== false ||
                                                                                              stripos($content, 'pembilang') !== false;
                                                                                    $isYField = stripos($content, 'Y:') !== false || 
                                                                                              stripos($content, 'y:') !== false ||
                                                                                              stripos($content, 'penyebut') !== false;
                                                                                @endphp
                                                                                <input type="number" 
                                                                                    name="target[{{ $matriks->id }}][tw1]" 
                                                                                    value="{{ $existingTarget ? $existingTarget->target_tw1 : '' }}"
                                                                                    class="target-input w-full h-9 border border-gray-300 rounded-lg px-2 py-1 text-xs focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                                                                    placeholder="Target TW I"
                                                                                    step="0.01"
                                                                                    min="0"
                                                                                    data-tw="1"
                                                                                    @if($isXField) data-child-x-for="{{ $matriks->id }}" @endif
                                                                                    @if($isYField) data-child-y-for="{{ $matriks->id }}" @endif>
                                                                            </td>
                                                                            <!-- Target TW II -->
                                                                            <td class="py-4 px-2">
                                                                                <input type="number" 
                                                                                    name="target[{{ $matriks->id }}][tw2]" 
                                                                                    value="{{ $existingTarget ? $existingTarget->target_tw2 : '' }}"
                                                                                    class="target-input w-full h-9 border border-gray-300 rounded-lg px-2 py-1 text-xs focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                                                                    placeholder="Target TW II"
                                                                                    step="0.01"
                                                                                    min="0"
                                                                                    data-tw="2"
                                                                                    @if($isXField) data-child-x-for="{{ $matriks->id }}" @endif
                                                                                    @if($isYField) data-child-y-for="{{ $matriks->id }}" @endif>
                                                                            </td>
                                                                            <!-- Target TW III -->
                                                                            <td class="py-4 px-2">
                                                                                <input type="number" 
                                                                                    name="target[{{ $matriks->id }}][tw3]" 
                                                                                    value="{{ $existingTarget ? $existingTarget->target_tw3 : '' }}"
                                                                                    class="target-input w-full h-9 border border-gray-300 rounded-lg px-2 py-1 text-xs focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                                                                    placeholder="Target TW III"
                                                                                    step="0.01"
                                                                                    min="0"
                                                                                    data-tw="3"
                                                                                    @if($isXField) data-child-x-for="{{ $matriks->id }}" @endif
                                                                                    @if($isYField) data-child-y-for="{{ $matriks->id }}" @endif>
                                                                            </td>
                                                                            <!-- Target TW IV -->
                                                                            <td class="py-4 px-2">
                                                                                <input type="number" 
                                                                                    name="target[{{ $matriks->id }}][tw4]" 
                                                                                    value="{{ $existingTarget ? $existingTarget->target_tw4 : '' }}"
                                                                                    class="target-input w-full h-9 border border-gray-300 rounded-lg px-2 py-1 text-xs focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                                                                    placeholder="Target TW IV"
                                                                                    step="0.01"
                                                                                    min="0"
                                                                                    data-tw="4"
                                                                                    @if($isXField) data-child-x-for="{{ $matriks->id }}" @endif
                                                                                    @if($isYField) data-child-y-for="{{ $matriks->id }}" @endif>
                                                                            </td>
                                                                        @else
                                                                            <td class="py-4 px-2"></td>
                                                                            <td class="py-4 px-2"></td>
                                                                            <td class="py-4 px-2"></td>
                                                                            <td class="py-4 px-2"></td>
                                                                        @endif
                                                                        <td class="py-4 px-2 text-center">
                                                                            @php
                                                                                $targetPk = $targetPkData->firstWhere('matriks_fra_id', $matriks->id);
                                                                                $targetPkValue = $targetPk ? $targetPk->target_pk : 0;
                                                                            @endphp
                                                                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                                                                {{ number_format($targetPkValue, 2) }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="py-4 px-2">
                                                                            @php
                                                                                $isYRow = false; // Default value for umum section
                                                                                // Check if this is a Y row based on content
                                                                                if (stripos($content, 'Y:') !== false || 
                                                                                    stripos($content, 'y:') !== false ||
                                                                                    stripos($content, 'penyebut') !== false) {
                                                                                    $isYRow = true;
                                                                                }
                                                                            @endphp
                                                                            @if($showPic && !$isYRow)
                                                                                @php
                                                                                    $existingAssign = $existingTarget ? $existingTarget->assign_id : '';
                                                                                @endphp
                                                                                <select name="assign_id[{{ $matriks->id }}]"
                                                                                    class="w-full h-9 border border-gray-300 rounded-lg px-2 py-1 text-xs bg-white pic-select focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                                                                                    <option value="" class="text-gray-500">Pilih PIC</option>
                                                                                    @foreach($penggunas as $pengguna)
                                                                                        <option value="{{ $pengguna->id }}" 
                                                                                            {{ $existingAssign == $pengguna->id ? 'selected' : '' }}>
                                                                                            {{ $pengguna->name }} ({{ $pengguna->jabatan }})
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data Umum</h3>
                                        <p class="text-gray-500">Data indikator umum belum tersedia untuk tahun ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        @endif

                        <!-- Konten Tab Petunjuk -->
                        <div id="content-petunjuk" class="space-y-6 hidden">
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 mr-3 mt-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <h3 class="text-lg font-semibold text-blue-900 mb-3">Petunjuk Pengisian Form Target
                                            FRA</h3>
                                        <div class="space-y-3 text-blue-800">
                                            <p><strong>1. Struktur Hierarki:</strong></p>
                                            <ul class="list-disc list-inside ml-4 space-y-1">
                                                <li>Tujuan → Sasaran → Indikator → Detail Indikator → Sub Indikator → Detail Sub</li>
                                                <li>Setiap level memiliki warna penanda yang berbeda untuk memudahkan identifikasi</li>
                                            </ul>

                                            <p><strong>2. Target Triwulan:</strong></p>
                                            <ul class="list-disc list-inside ml-4 space-y-1">
                                                <li>Isi target untuk setiap triwulan (TW I, TW II, TW III, TW IV)</li>
                                                <li>Target bersifat kumulatif dari triwulan sebelumnya</li>
                                                <li>Pastikan target TW IV sama dengan target PK yang telah ditetapkan</li>
                                            </ul>

                                            <p><strong>3. Validasi Sistem:</strong></p>
                                            <ul class="list-disc list-inside ml-4 space-y-1">
                                                <li>Sistem akan memberikan peringatan jika target TW IV tidak sama dengan target PK</li>
                                                <li>Semua field target harus diisi sebelum menyimpan</li>
                                                <li>Data akan tersimpan otomatis saat tombol simpan ditekan</li>
                                            </ul>

                                            <p><strong>4. Tombol Aksi:</strong></p>
                                            <ul class="list-disc list-inside ml-4 space-y-1">
                                                <li><strong>Simpan:</strong> Menyimpan data tanpa mengubah status</li>
                                                <li><strong>Submit:</strong> Menyimpan dan mengubah status menjadi submitted</li>
                                                <li><strong>Reset:</strong> Menghapus semua isian dan kembali ke data awal</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

    <!-- Spacing for sticky buttons -->
    <div class="pb-20"></div>

    <!-- Sticky Action Buttons -->
    <div class="fixed bottom-0 left-0 right-0 md:left-72 md:ml-2.5 bg-white/80 backdrop-blur-sm border-t border-gray-200 px-6 py-4 shadow-lg z-40">
        <div class="flex justify-between items-center mx-auto">
            <a href="{{ route('fra.index') }}"
                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="setActionTypeAndSubmit('save')"
                    class="px-6 py-3 border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition-all duration-200 font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Simpan Perubahan
                </button>
                <button type="button" onclick="setActionTypeAndSubmit('finalize')"
                    class="px-8 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Finalisasi
                </button>
            </div>
        </div>
    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Loading overlay --}}
    @include('components.loading')

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = ['iku', 'suplemen', 'umum', 'petunjuk'];
            const tabButtons = {};
            const tabContents = {};

            // Initialize tab elements
            tabs.forEach(tab => {
                tabButtons[tab] = document.getElementById(`tab-${tab}`);
                tabContents[tab] = document.getElementById(`content-${tab}`);
            });

            // Tab switching function
            function switchTab(activeTab) {
                tabs.forEach(tab => {
                    if (tabButtons[tab]) {
                        if (tab === activeTab) {
                            tabButtons[tab].className =
                                'px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-t-xl border-b-2 border-red-600 transition-all duration-200 mr-2';
                        } else {
                            tabButtons[tab].className =
                                'px-6 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium rounded-t-xl transition-all duration-200 mr-2';
                        }
                    }

                    if (tabContents[tab]) {
                        if (tab === activeTab) {
                            tabContents[tab].classList.remove('hidden');
                        } else {
                            tabContents[tab].classList.add('hidden');
                        }
                    }
                });
            }

            // Add click event listeners to tabs
            tabs.forEach(tab => {
                if (tabButtons[tab]) {
                    tabButtons[tab].addEventListener('click', () => switchTab(tab));
                }
            });

            // Form submission handling
            const form = document.getElementById('targetForm');
            const saveBtn = document.getElementById('saveBtn');
            const submitBtn = document.getElementById('submitBtn');
            const resetBtn = document.getElementById('resetBtn');
            const actionType = document.getElementById('actionType');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const notification = document.getElementById('notification');
            const notificationText = document.getElementById('notificationText');

            // Save button click
            if (saveBtn) {
                saveBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    actionType.value = 'save';
                    submitForm();
                });
            }

            // Submit button click
            if (submitBtn) {
                submitBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    actionType.value = 'submit';
                    if (confirm('Apakah Anda yakin ingin submit data? Data yang sudah disubmit tidak dapat diubah.')) {
                        submitForm();
                    }
                });
            }

            // Reset button click
            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    if (confirm('Apakah Anda yakin ingin mereset semua data? Semua perubahan akan hilang.')) {
                        form.reset();
                        updateProgress();
                        showNotification('Data berhasil direset!', 'info');
                    }
                });
            }

            // Form submission function
            function submitForm() {
                if (validateForm()) {
                    showLoading(true);
                    
                    // Simulate form submission (replace with actual AJAX call)
                    setTimeout(() => {
                        showLoading(false);
                        const action = actionType.value === 'save' ? 'disimpan' : 'disubmit';
                        showNotification(`Data berhasil ${action}!`, 'success');
                        updateProgress();
                    }, 2000);
                }
            }

            // Form validation
            function validateForm() {
                const inputs = form.querySelectorAll('input[type="number"]');
                let isValid = true;
                let emptyFields = 0;

                inputs.forEach(input => {
                    if (!input.value || input.value === '') {
                        emptyFields++;
                        isValid = false;
                    }
                });

                if (!isValid) {
                    showNotification(`Masih ada ${emptyFields} field yang belum diisi!`, 'error');
                }

                return isValid;
            }

            // Progress calculation
            function updateProgress() {
                const inputs = form.querySelectorAll('input[type="number"]');
                const totalInputs = inputs.length;
                let filledInputs = 0;

                inputs.forEach(input => {
                    if (input.value && input.value !== '') {
                        filledInputs++;
                    }
                });

                const percentage = totalInputs > 0 ? Math.round((filledInputs / totalInputs) * 100) : 0;
                
                const progressBar = document.getElementById('progressBar');
                const progressText = document.getElementById('progressText');
                
                if (progressBar) {
                    progressBar.style.width = percentage + '%';
                }
                
                if (progressText) {
                    progressText.textContent = percentage + '% selesai';
                }
            }

            // Show/hide loading overlay
            function showLoading(show) {
                if (loadingOverlay) {
                    if (show) {
                        loadingOverlay.classList.remove('hidden');
                    } else {
                        loadingOverlay.classList.add('hidden');
                    }
                }
            }

            // Show notification
            function showNotification(message, type = 'success') {
                if (notification && notificationText) {
                    notificationText.textContent = message;
                    
                    // Set notification color based on type
                    notification.className = 'fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 z-50';
                    
                    if (type === 'success') {
                        notification.classList.add('bg-green-500', 'text-white');
                    } else if (type === 'error') {
                        notification.classList.add('bg-red-500', 'text-white');
                    } else if (type === 'info') {
                        notification.classList.add('bg-blue-500', 'text-white');
                    }
                    
                    // Show notification
                    notification.classList.remove('translate-x-full');
                    
                    // Hide after 3 seconds
                    setTimeout(() => {
                        notification.classList.add('translate-x-full');
                    }, 3000);
                }
            }

            // Function to validate TW4 against PK
            function validateTW4(matriksId) {
                const tw4Input = document.querySelector(`input[name="target[${matriksId}][tw4]"]`);
                const targetPkElement = document.querySelector(`[data-matriks-id="${matriksId}"][data-target-pk]`);
                const warningDiv = document.querySelector(`#tw4-warning-${matriksId}`);

                if (tw4Input && targetPkElement) {
                    const tw4Value = parseFloat(tw4Input.value) || 0;
                    const pkValue = parseFloat(targetPkElement.dataset.targetPk) || 0;
                    const hasWarning = tw4Value !== pkValue && pkValue > 0;

                    if (warningDiv) {
                        if (hasWarning) {
                            warningDiv.classList.remove('hidden');
                            const textDiv = warningDiv.querySelector('.mt-1');
                            if (textDiv) {
                                textDiv.textContent = `TW IV (${tw4Value}) harus sama dengan PK (${pkValue})`;
                            }
                        } else {
                            warningDiv.classList.add('hidden');
                        }
                    }
                }
            }

            // Function to validate triwulan sequence (must be increasing)
            function validateTriwulanSequence(matriksId, currentTw) {
                const inputs = [
                    document.querySelector(`input[name="target[${matriksId}][tw1]"]`),
                    document.querySelector(`input[name="target[${matriksId}][tw2]"]`),
                    document.querySelector(`input[name="target[${matriksId}][tw3]"]`),
                    document.querySelector(`input[name="target[${matriksId}][tw4]"]`)
                ];

                const values = inputs.map(input => parseFloat(input?.value) || 0);
                const currentValue = values[currentTw - 1];
                let hasError = false;

                // Check if current value is greater than or equal to previous values
                for (let i = 0; i < currentTw - 1; i++) {
                    if (values[i] > 0 && currentValue > 0 && currentValue < values[i]) {
                        hasError = true;
                        break;
                    }
                }

                // Add/remove error styling
                const currentInput = inputs[currentTw - 1];
                if (currentInput) {
                    if (hasError) {
                        currentInput.classList.add('border-red-500', 'bg-red-50');
                        currentInput.classList.remove('border-gray-300');
                    } else {
                        currentInput.classList.remove('border-red-500', 'bg-red-50');
                        currentInput.classList.add('border-gray-300');
                    }
                }
            }

            // Function to validate all triwulan targets and show warnings above cells
            function validateTriwulanWarnings(matriksId) {
                const inputs = [
                    document.querySelector(`input[name="target[${matriksId}][tw1]"]`),
                    document.querySelector(`input[name="target[${matriksId}][tw2]"]`),
                    document.querySelector(`input[name="target[${matriksId}][tw3]"]`),
                    document.querySelector(`input[name="target[${matriksId}][tw4]"]`)
                ];

                const values = inputs.map(input => parseFloat(input?.value) || 0);

                // Validate each triwulan
                for (let i = 0; i < 4; i++) {
                    const currentValue = values[i];
                    const twNumber = i + 1;
                    let hasWarning = false;
                    let warningMessage = '';

                    // Check if current TW is less than previous TW (should be increasing)
                    if (i > 0 && currentValue > 0) {
                        for (let j = 0; j < i; j++) {
                            if (values[j] > 0 && currentValue < values[j]) {
                                hasWarning = true;
                                warningMessage = `TW ${twNumber} (${currentValue}) harus ≥ TW ${j + 1} (${values[j]})`;
                                break;
                            }
                        }
                    }

                    // Special validation for TW4 vs PK
                    if (twNumber === 4) {
                        const targetPkElement = document.querySelector(`[data-matriks-id="${matriksId}"][data-target-pk]`);
                        if (targetPkElement) {
                            const pkValue = parseFloat(targetPkElement.dataset.targetPk) || 0;
                            if (currentValue > 0 && pkValue > 0 && currentValue !== pkValue) {
                                hasWarning = true;
                                warningMessage = `TW IV (${currentValue}) harus sama dengan PK (${pkValue})`;
                            }
                        }
                    }

                    // Show/hide warning
                    const warningDiv = document.querySelector(`#tw${twNumber}-warning-${matriksId}`);
                    if (warningDiv) {
                        if (hasWarning) {
                            warningDiv.classList.remove('hidden');
                            const textDiv = warningDiv.querySelector('.mt-1');
                            if (textDiv) {
                                textDiv.textContent = warningMessage;
                            }
                        } else {
                            warningDiv.classList.add('hidden');
                        }
                    }
                }
            }

            // Add input event listeners for real-time validation
            document.addEventListener('input', function(e) {
                if (e.target.matches('input[type="number"]')) {
                    updateProgress();
                    
                    // Extract matriks ID and TW number from input name
                    const match = e.target.name.match(/target\[(\d+)\]\[tw(\d+)\]/);
                    if (match) {
                        const matriksId = match[1];
                        const twNumber = parseInt(match[2]);
                        
                        // Validate triwulan sequence and warnings
                        validateTriwulanSequence(matriksId, twNumber);
                        validateTriwulanWarnings(matriksId);
                        
                        // Special validation for TW4
                        if (twNumber === 4) {
                            validateTW4(matriksId);
                        }
                    }
                }
            });

            // Auto-save functionality (save to localStorage)
            function saveToLocalStorage() {
                const formData = new FormData(form);
                const data = {};
                
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                
                localStorage.setItem(`fra_target_${{{ $fra->id ?? 0 }}}`, JSON.stringify(data));
            }

            // Load from localStorage
            function loadFromLocalStorage() {
                const savedData = localStorage.getItem(`fra_target_${{{ $fra->id ?? 0 }}}`);
                if (savedData) {
                    try {
                        const data = JSON.parse(savedData);
                        
                        Object.keys(data).forEach(key => {
                            const input = form.querySelector(`[name="${key}"]`);
                            // ✅ FIXED: Only update non-readonly fields from localStorage
                            if (input && !input.readOnly) {
                                input.value = data[key];
                            }
                        });
                        
                        // Data loaded from localStorage, recalculating parent values
                        
                        // Recalculate all parent groups after loading data
                        console.log('🔄 Recalculating after loading from localStorage...');
                        document.querySelectorAll('tr[data-group-id]').forEach(row => {
                            if(row.dataset.level === 'main' || row.dataset.level === 'sub'){
                                const hasCalculatedInputs = row.querySelectorAll('.target-input[readonly]').length > 0;
                                if (hasCalculatedInputs) {
                                    calculateAndSetValue(row);
                                }
                            }
                        });

                        updateProgress();

                    } catch (e) {
                        console.error('Error loading saved data:', e);
                    }
                }
            }

            // Save to localStorage on input change
            form.addEventListener('input', function() {
                saveToLocalStorage();
            });

            // Load saved data on page load
            loadFromLocalStorage();

            // Initial progress update
            updateProgress();

            // Hide notification on page load
            if (notification) {
                notification.classList.add('translate-x-full');
            }

            // Initialize page
            // Form Target FRA initialized for FRA ID: {{ $fra->id ?? 0 }}
        });

        function setActionTypeAndSubmit(type) {
            document.getElementById('actionType').value = type;

            if (type === 'finalize') {
                // Validate all fields before finalization
                const inputs = document.querySelectorAll('input[name^="target["]');
                let hasEmpty = false;
                let filledInputs = 0;
                const totalInputs = inputs.length;

                inputs.forEach(input => {
                    if (!input.value || input.value.trim() === '') {
                        hasEmpty = true;
                        input.classList.add('error');
                    } else {
                        filledInputs++;
                        input.classList.remove('error');
                    }
                });

                const percentage = Math.round((filledInputs / totalInputs) * 100);

                if (hasEmpty) {
                    showModal('warning', 'Finalisasi Tidak Dapat Dilakukan',
                        `Mohon lengkapi semua field Target FRA sebelum finalisasi.<br>
                        Progress saat ini: ${percentage}%`, {
                            confirmText: 'Oke, Saya Mengerti',
                            showCancel: false
                        });
                    return false;
                }

                // Show confirmation modal
                showModal('question', 'Konfirmasi Finalisasi',
                    'Apakah Anda yakin ingin memfinalisasi target FRA?<br>Data yang sudah difinalisasi tidak dapat diubah lagi.', {
                        confirmText: 'Ya, Finalisasi',
                        cancelText: 'Batal',
                        showCancel: true,
                        confirmCallback: () => {
                            showGlobalLoading('Memfinalisasi data...');
                            submitFormWithFetch(type);
                        }
                    });
                return false;
            }

            // For regular save, show loading state and submit the form
            showGlobalLoading('Menyimpan data...');
            submitFormWithFetch(type);
        }

        function submitFormWithFetch(actionType) {
            const form = document.getElementById('targetForm');
            
            // Debug: Log form action and method
            console.log('🔍 Form action:', form.action);
            console.log('🔍 Form method:', form.method);
            
            const formData = new FormData(form);
            
            // Ensure action type is set
            formData.set('action_type', actionType);
            
            // 🔧 FIX: Remove _token from FormData to avoid conflict with X-CSRF-TOKEN header
            // Laravel expects CSRF token either in form data (_token) OR in header (X-CSRF-TOKEN), not both
            // Using both can cause 419 "Page Expired" errors due to token validation conflicts
            formData.delete('_token');
            
            // Debug: Log all form data
            console.log('📋 Form data being sent:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }
            
            // Get CSRF token with fallback
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                // Fallback: try to get from form input
                const csrfInput = form.querySelector('input[name="_token"]');
                csrfToken = csrfInput ? csrfInput.value : null;
            }
            console.log('🔐 CSRF Token:', csrfToken ? 'Found' : 'Missing');
            
            if (!csrfToken) {
                console.error('❌ No CSRF token found!');
                hideGlobalLoading();
                showModal('error', 'Kesalahan Sistem', 'CSRF token tidak ditemukan. Silakan refresh halaman dan coba lagi.', {
                    confirmText: 'Refresh Halaman',
                    showCancel: false,
                    confirmCallback: () => {
                        window.location.reload();
                    }
                });
                return;
            }
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                console.log('📡 Response received:', {
                    status: response.status,
                    statusText: response.statusText,
                    ok: response.ok,
                    url: response.url
                });
                
                if (!response.ok) {
                    if (response.status === 419) {
                        console.error('❌ CSRF Token Mismatch - Status 419');
                        throw new Error('CSRF token mismatch. Silakan refresh halaman dan coba lagi.');
                    }
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                hideGlobalLoading();
                
                if (data.success) {
                    if (actionType === 'finalize') {
                        showModal('success', 'Berhasil', data.message, {
                            confirmText: 'Oke',
                            showCancel: false,
                            confirmCallback: () => {
                                window.location.href = "{{ route('fra.index') }}";
                            }
                        });
                    } else {
                        showSuccess(data.message);
                        // Clear localStorage on successful save
                        localStorage.removeItem(`fra_target_${{{ $fra->id ?? 0 }}}`);
                    }
                } else {
                    let errorMessage = data.message || 'Terjadi kesalahan saat menyimpan data';
                    
                    if (data.errors) {
                        errorMessage += '<br><br>Detail Error:<ul>';
                        Object.values(data.errors).forEach(errorList => {
                            errorList.forEach(error => {
                                errorMessage += `<li>${error}</li>`;
                            });
                        });
                        errorMessage += '</ul>';
                    }

                    showModal('error', 'Gagal Menyimpan', errorMessage, {
                        confirmText: 'Tutup',
                        showCancel: false
                    });
                }
            })
            .catch(error => {
                hideGlobalLoading();
                console.error('Form submission error:', error);
                
                showModal('error', 'Kesalahan Sistem', `Terjadi kesalahan tidak terduga. Silakan coba lagi atau hubungi administrator.<br><br>Detail: ${error.message}`, {
                    confirmText: 'Tutup',
                    showCancel: false
                });
            });
        }

        // Debug function for development
        function debugFormData() {
            const form = document.getElementById('targetForm');
            const formData = new FormData(form);
            
            console.log('=== Form Debug Data ===');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            console.log('=== End Debug Data ===');
        }

        // Make debug function available globally
        window.debugFormData = debugFormData;
        
        // Debug URL for development
        // Debug URL available: {{ url()->current() }}?debug=1&fra_id={{ $fra->id ?? 0 }}
    </script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('targetForm');

        // Enhanced input event handler with debounce for better performance
        const debounce = (func, wait) => {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        };

        form.addEventListener('input', debounce(function(event) {
            const input = event.target;
            if (input.classList.contains('target-input')) {
                console.log(`🔄 Input changed: ${input.name} = ${input.value}`);
                
                // Validate its own order
                validateTwOrder(input);

                // Re-validate the next TW in the same row
                const currentTw = parseInt(input.dataset.tw, 10);
                if (currentTw < 4) {
                    const nextInput = input.closest('tr').querySelector(`.target-input[data-tw="${currentTw + 1}"]`);
                    if (nextInput) {
                        validateTwOrder(nextInput);
                    }
                }
                
                // If it's a TW4 input, validate against PK
                if (currentTw === 4) {
                    validatePkMismatch(input);
                }

                // Check for auto calculation - use correct attributes for FRA
                const row = input.closest('tr');
                const isChildX = input.hasAttribute('data-child-x-for');
                const isChildY = input.hasAttribute('data-child-y-for');
                
                if (isChildX || isChildY) {
                    console.log(`🔄 Child input changed (Level: ${row.dataset.level}), triggering calculation`);
                    calculateParents(row);
                }
            }
        }, 300));

        // Initial validation and calculation on page load
        function runInitialSetup() {
            console.log('🔄 Running initial setup for FRA calculations...');
            
            // Run initial validation for warnings, but not for PK mismatch
            document.querySelectorAll('.target-input').forEach(input => {
                if (input.value !== '') {
                    validateTwOrder(input);
                }
            });

            // Run initial calculation for all calculated groups
            console.log('🧮 Running initial calculations...');
            
            // Find all calculated parent rows and trigger calculation
            document.querySelectorAll('tr[data-group-id]').forEach(row => {
                if(row.dataset.level === 'main' || row.dataset.level === 'sub'){
                    const hasCalculatedInputs = row.querySelectorAll('.target-input[readonly]').length > 0;
                    if (hasCalculatedInputs) {
                        calculateAndSetValue(row);
                    }
                }
            });
        }
        
        // Run initial setup with delay to ensure DOM is ready
        setTimeout(runInitialSetup, 100);

        // Auto calculation functions - improved version based on target PK logic
        function calculateParents(startRow) {
            const groupId = startRow.dataset.groupId;
            
            // Find parent sub-indicator to calculate (if any)
            const parentSubIndicatorContent = startRow.dataset.parentSubIndicator;
            if(parentSubIndicatorContent) {
                 const subRow = document.querySelector(`tr[data-group-id="${groupId}"][data-level="sub"][data-content="${parentSubIndicatorContent}"]`);
                 if(subRow) {
                     console.log(`🔄 Found sub-indicator to calculate: ${parentSubIndicatorContent}`);
                     calculateAndSetValue(subRow);
                 } else {
                     console.log(`⚠️ Sub-indicator not found: ${parentSubIndicatorContent}`);
                 }
            }
           
            // Always try to calculate main indicator
            const mainRow = document.querySelector(`tr[data-group-id="${groupId}"][data-level="main"]`);
            if(mainRow) calculateAndSetValue(mainRow);
        }
        
        function calculateAndSetValue(parentRow) {
            const groupId = parentRow.dataset.groupId;
            const level = parentRow.dataset.level;
            
            console.log(`🧮 calculateAndSetValue called for level: ${level}, groupId: ${groupId}`);
            
            let xInputs, yInputs;

            if (level === 'main') {
                xInputs = document.querySelectorAll(`tr[data-group-id="${groupId}"][data-level="detail_indicator"] input[data-child-x-for]`);
                yInputs = document.querySelectorAll(`tr[data-group-id="${groupId}"][data-level="detail_indicator"] input[data-child-y-for]`);
                console.log(`🔍 Main indicator - Found X inputs: ${xInputs.length}, Y inputs: ${yInputs.length}`);
            } else if (level === 'sub') {
                const subIndicatorContent = parentRow.dataset.content;
                console.log(`🔍 Sub indicator - Looking for children of: ${subIndicatorContent}`);
                xInputs = document.querySelectorAll(`tr[data-group-id="${groupId}"][data-level="detail_sub"][data-parent-sub-indicator="${subIndicatorContent}"] input[data-child-x-for]`);
                yInputs = document.querySelectorAll(`tr[data-group-id="${groupId}"][data-level="detail_sub"][data-parent-sub-indicator="${subIndicatorContent}"] input[data-child-y-for]`);
                console.log(`🔍 Sub indicator - Found X inputs: ${xInputs.length}, Y inputs: ${yInputs.length}`);
            }

            if (xInputs && yInputs && xInputs.length > 0 && yInputs.length > 0) {
                // For each TW (1-4), calculate the percentage
                for (let tw = 1; tw <= 4; tw++) {
                    const parentInput = parentRow.querySelector(`.target-input[data-tw="${tw}"][readonly]`);
                    if (!parentInput) continue;

                    const xInput = Array.from(xInputs).find(input => input.dataset.tw == tw);
                    const yInput = Array.from(yInputs).find(input => input.dataset.tw == tw);

                    if (xInput && yInput) {
                        const xValue = parseFloat(xInput.value) || 0;
                        const yValue = parseFloat(yInput.value) || 0;
                        let result = 0;

                        if (yValue > 0) {
                            result = (xValue / yValue) * 100;
                        }

                        let formattedValue;
                        // ✅ FIXED: Tampilkan nilai 0 sebagai '0' bukan string kosong
                        if (result === 0) {
                            formattedValue = '0';
                        } else {
                            formattedValue = (result % 1 === 0) ? result.toString() : result.toFixed(2);
                        }
                        
                        const previousValue = parentInput.value;
                        if (formattedValue !== previousValue) {
                            parentInput.value = formattedValue;
                            console.log(`✅ Updated TW${tw}: ${previousValue} → ${formattedValue} (${xValue}/${yValue}*100)`);
                            
                            // Add visual feedback
                            parentInput.classList.add('recently-calculated');
                            setTimeout(() => {
                                parentInput.classList.remove('recently-calculated');
                            }, 2000);
                            
                            // Trigger validation
                            validateTwOrder(parentInput);
                            if (tw === 4) {
                                validatePkMismatch(parentInput);
                            }
                        }
                    }
                }
            }
        }

        function validateTwOrder(input) {
            const currentTw = parseInt(input.dataset.tw, 10);
            const warningCell = input.closest('tr').previousElementSibling.querySelector(`.warning-cell[data-tw="${currentTw}"]`);

            clearWarning(warningCell, 'tw');

            if (currentTw > 1 && input.value !== '') {
                const currentRow = input.closest('tr');
                const prevTwInput = currentRow.querySelector(`.target-input[data-tw="${currentTw - 1}"]`);
                
                if (prevTwInput && prevTwInput.value !== '') {
                    const currentValue = parseFloat(input.value);
                    const prevValue = parseFloat(prevTwInput.value);
                    if (!isNaN(currentValue) && !isNaN(prevValue) && currentValue < prevValue) {
                        showWarning(warningCell, 'tw', 'Nilai < TW sebelumnya');
                    }
                }
            }
        }

        function validatePkMismatch(tw4Input) {
            const currentRow = tw4Input.closest('tr');
            const level = currentRow.dataset.level;
            
            // ✅ FIXED: Ambil matriks ID yang spesifik untuk row ini, bukan group ID
            const matriksId = tw4Input.name.match(/target\[(\d+)\]/)?.[1];

            // Get warning cell if exists
            const warningCell = currentRow.previousElementSibling?.querySelector('.warning-cell[data-tw="4"]');
            if (warningCell) {
                clearWarning(warningCell, 'pk');
            }

            // ✅ FIXED: Cari Target PK yang spesifik untuk matriks ID ini
            const pkSpan = document.querySelector(`.pk-value[data-matriks-id="${matriksId}"]`);
            
            // Hanya validasi jika ada Target PK untuk level ini
            if (pkSpan && pkSpan.dataset.pkValue !== undefined) {
                const pkValue = parseFloat(pkSpan.dataset.pkValue);
                const tw4Value = tw4Input.value !== '' ? parseFloat(tw4Input.value) : null;
                
                console.log(`🔍 Validating TW IV for matriks ${matriksId} (level: ${level}): TW4=${tw4Value}, PK=${pkValue}`);
                
                if (!isNaN(pkValue)) {
                    if (pkValue > 0) {
                        // Target PK > 0, TW IV harus sama
                        if (tw4Input.value === '' || tw4Value === null || tw4Value === 0) {
                            if (warningCell) {
                                showWarning(warningCell, 'pk', `TW IV harus diisi ${pkValue} (sesuai Target PK)`);
                            }
                        } else if (Math.abs(tw4Value - pkValue) > 0.01) {
                            if (warningCell) {
                                showWarning(warningCell, 'pk', `TW IV (${tw4Value}) ≠ Target PK (${pkValue})`);
                            }
                        }
                    } else if (pkValue === 0) {
                        // Target PK = 0, TW IV juga harus 0 atau kosong
                        if (tw4Input.value !== '' && tw4Value !== 0 && tw4Value !== null) {
                            if (warningCell) {
                                showWarning(warningCell, 'pk', 'TW IV harus 0 (sesuai Target PK)');
                            }
                        }
                    }
                }
            } else {
                // Jika tidak ada Target PK untuk level ini, tidak perlu validasi
                console.log(`ℹ️ No Target PK found for matriks ${matriksId} (level: ${level}), skipping validation`);
            }
        }

        function showWarning(cell, type, message) {
            if (!cell) return;
            const warningClass = type === 'pk' ? 'pk-mismatch-warning' : 'tw-order-warning';
            
            // Hapus warning lama dengan tipe yang sama
            const existingWarning = cell.querySelector(`.${warningClass}`);
            if (existingWarning) {
                existingWarning.remove();
            }

            const iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.21 3.03-1.742 3.03H4.42c-1.532 0-2.492-1.696-1.742-3.03l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-8a1 1 0 00-1 1v3a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>`;
            
            const warningEl = document.createElement('div');
            warningEl.className = `flex flex-col items-center justify-center w-full px-1 py-1 bg-yellow-100 border border-yellow-400 text-yellow-800 text-xs font-semibold rounded-md shadow-sm ${warningClass} gap-1`;
            
            const textSpan = document.createElement('span');
            textSpan.className = 'text-center';
            textSpan.textContent = message;
            
            warningEl.innerHTML = iconSvg;
            warningEl.appendChild(textSpan);
            
            cell.appendChild(warningEl);

            cell.closest('.warning-row').classList.remove('hidden');
        }

        function clearWarning(cell, type) {
            if (!cell) return;
            const warningClass = type === 'pk' ? 'pk-mismatch-warning' : 'tw-order-warning';
            const existingWarning = cell.querySelector(`.${warningClass}`);
            if (existingWarning) {
                existingWarning.remove();
            }

            const warningRow = cell.closest('.warning-row');
            if (warningRow && warningRow.querySelectorAll('.warning-cell > div').length === 0) {
                warningRow.classList.add('hidden');
            }
        }
    });
    </script>

    <style>
        .recently-saved {
            border-color: #10B981 !important;
            background-color: #ECFDF5 !important;
            transition: all 0.3s ease;
        }

        .recently-calculated {
            border-color: #3B82F6 !important;
            background-color: #EFF6FF !important;
            transition: all 0.3s ease;
        }

        .error {
            border-color: #EF4444 !important;
            background-color: #FEF2F2 !important;
        }

        /* Loading state styles */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        /* Progress bar animation */
        #progressBar {
            transition: width 0.5s ease-in-out;
        }

        /* Enhanced visual feedback for calculated fields */
        .target-input[readonly] {
            background-color: #F3F4F6 !important;
            font-weight: 500;
        }

        /* Warning styles */
        .warning-row {
            transition: all 0.3s ease;
        }

        .warning-cell {
            min-height: 40px;
            vertical-align: top;
        }

        .pk-mismatch-warning {
            background-color: #FEF3C7 !important;
            border-color: #F59E0B !important;
            color: #92400E !important;
        }

        .tw-order-warning {
            background-color: #FEE2E2 !important;
            border-color: #EF4444 !important;
            color: #DC2626 !important;
        }
    </style>
@endpush