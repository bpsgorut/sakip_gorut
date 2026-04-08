@extends('components.master')

@section('title', 'Form Target PK')

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
                            <h1 class="text-3xl font-bold mb-2">Form Target PK</h1>
                            <p class="text-red-100 text-lg">Tahun {{ $kegiatan->tahun_berjalan }}</p>
                        </div>
                        <div class="text-white text-right">
                            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-medium">Target PK Setting</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Container -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <form action="{{ route('target.pk.simpan', $kegiatan->id) }}" method="POST" id="targetPkForm">
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
                                    class="px-6 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium rounded-t-xl transition-all duration-200">
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
                        </div>
                    </div>

                    <!-- Content Tabs -->
                    <div class="tab-content px-6 pb-24">
                        <!-- Konten Tab IKU -->
                        <div id="content-iku" class="space-y-6">
                            @php
                                $currentTemplateJenis = 'PK IKU';
                                $dataByTujuan = $matriksFraData
                                    ->filter(fn($m) => $m->template_fra->template_jenis->nama === $currentTemplateJenis)
                                    ->sortBy(['tujuan', 'sasaran', 'indikator'])
                                    ->groupBy('tujuan');
                            @endphp

                            @forelse($dataByTujuan as $tujuan => $matriksList)
                                <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden mb-8">
                                    <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                                        <h2 class="font-bold text-white text-lg flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            {{ $tujuan }}
                                        </h2>
                                    </div>
                                    <div class="p-6">
                                        @php $dataBySasaran = $matriksList->groupBy('sasaran'); @endphp
                                        @foreach ($dataBySasaran as $sasaran => $indikatorList)
                                            <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden {{ !$loop->first ? 'mt-6' : '' }}">
                                                <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                                                    <h3 class="font-semibold text-gray-800 flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                        </svg>
                                                        {{ $sasaran }}
                                                    </h3>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <table class="w-full bg-white table-fixed">
                                                        <thead>
                                                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                                                <th class="text-left py-3 px-4 font-semibold text-gray-700" style="width: 70%">Indikator</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 15%">Satuan</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 15%">Target PK</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $dataByIndikator = $indikatorList->groupBy('indikator'); @endphp
                                                            @foreach($dataByIndikator as $indikatorName => $rowsForIndikator)
                                                                @php
                                                                    // Find the main indicator row (where other hierarchy levels are empty)
                                                                    $mainMatriks = $rowsForIndikator->first(function($item) {
                                                                        return empty($item->detail_indikator) && empty($item->sub_indikator) && empty($item->detail_sub);
                                                                    });
                                                                    
                                                                    // If no main indicator found, skip this group to prevent errors.
                                                                    if(!$mainMatriks) continue;
                                                                    
                                                                    $xy_regex = fn($char) => '/^' . $char . '[\.:\s]/i';

                                                                    // Check if the main indicator group is calculated based on its children
                                                                    $hasXChild = $rowsForIndikator->contains(fn($r) => !empty($r->detail_indikator) && preg_match($xy_regex('x'), strip_tags($r->detail_indikator)));
                                                                    $hasYChild = $rowsForIndikator->contains(fn($r) => !empty($r->detail_indikator) && preg_match($xy_regex('y'), strip_tags($r->detail_indikator)));
                                                                    $isGroupCalculated = $hasXChild && $hasYChild;

                                                                    $virtualRows = [];
                                                                    
                                                                    // Level 1: Indikator utama (selalu ada)
                                                                    $virtualRows[] = ['matriks' => $mainMatriks, 'level' => 'main', 'content' => $mainMatriks->indikator, 'isCalculated' => $isGroupCalculated];
                                                                    
                                                                    // Helper to process children and their sub-children recursively (though only 2 levels here)
                                                                    $processedContents = [$mainMatriks->indikator];

                                                                    foreach($rowsForIndikator as $item) {
                                                                        // Level 2: Detail Indikator
                                                                        if(!empty($item->detail_indikator) && !in_array('detail_indicator_' . $item->detail_indikator, $processedContents)) {
                                                                            $virtualRows[] = ['matriks' => $item, 'level' => 'detail_indicator', 'content' => $item->detail_indikator, 'isCalculated' => false];
                                                                            $processedContents[] = 'detail_indicator_' . $item->detail_indikator;
                                                                        }
                                                                    }
                                                                    
                                                                    foreach($rowsForIndikator as $item) {
                                                                        // Level 3: Sub Indikator
                                                                        if(!empty($item->sub_indikator) && !in_array('sub_' . $item->sub_indikator, $processedContents)) {
                                                                            $isSubCalculated = false;
                                                                            $isParent = !preg_match($xy_regex('x'), strip_tags($item->sub_indikator)) && !preg_match($xy_regex('y'), strip_tags($item->sub_indikator));
                                                                            if($isParent){
                                                                                $subChildren = $rowsForIndikator->where('sub_indikator', $item->sub_indikator);
                                                                                $hasX_sub = $subChildren->contains(fn($r) => !empty($r->detail_sub) && preg_match($xy_regex('x'), strip_tags($r->detail_sub)));
                                                                                $hasY_sub = $subChildren->contains(fn($r) => !empty($r->detail_sub) && preg_match($xy_regex('y'), strip_tags($r->detail_sub)));
                                                                                $isSubCalculated = $hasX_sub && $hasY_sub;
                                                                            }
                                                                            $virtualRows[] = ['matriks' => $item, 'level' => 'sub', 'content' => $item->sub_indikator, 'isCalculated' => $isSubCalculated];
                                                                            $processedContents[] = 'sub_' . $item->sub_indikator;

                                                                            // Level 4: Detail Sub (anak dari sub-indikator ini)
                                                                            $detailSubs = $rowsForIndikator->where('sub_indikator', $item->sub_indikator)->whereNotNull('detail_sub');
                                                                            foreach($detailSubs as $detailSubItem) {
                                                                                if(!in_array('detail_sub_' . $detailSubItem->detail_sub, $processedContents)) {
                                                                                    $virtualRows[] = ['matriks' => $detailSubItem, 'level' => 'detail_sub', 'content' => $detailSubItem->detail_sub, 'isCalculated' => false];
                                                                                    $processedContents[] = 'detail_sub_' . $detailSubItem->detail_sub;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                @endphp

                                                                @foreach($virtualRows as $vRow)
                                                                    @php
                                                                        $matriks = $vRow['matriks'];
                                                                        $existingTarget = $existingTargets[$matriks->id] ?? null;
                                                                        $isX = preg_match($xy_regex('x'), strip_tags($vRow['content']));
                                                                        $isY = preg_match($xy_regex('y'), strip_tags($vRow['content']));
                                                                    @endphp
                                                                    <tr class="border-b border-gray-100 hover:bg-gray-50" 
                                                                        data-group-id="{{ $mainMatriks->id }}" 
                                                                        data-row-id="{{ $matriks->id }}"
                                                                        data-level="{{ $vRow['level'] }}"
                                                                        data-parent-sub-indicator="{{ $matriks->sub_indikator ?? '' }}"
                                                                        data-content="{{ strip_tags($vRow['content']) }}">
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
                                                                                <div class="flex items-start ml-14">
                                                                                    <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                                                    <span class="text-gray-800 leading-relaxed">{{ $vRow['content'] }}</span>
                                                                        </div>
                                                                            @endif
                                                                    </td>
                                                                        <td class="py-2 px-2 text-center align-middle">
                                                                            <span class="inline-block bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium">{{ $matriks->satuan }}</span>
                                                                    </td>
                                                                        <td class="py-2 px-2 align-middle">
                                                                        <div class="flex justify-center">
                                                                            <input type="number"
                                                                                    name="targets_pk[{{ $matriks->id }}]"
                                                                                    class="target-pk-input w-24 border border-gray-300 rounded-lg px-3 py-2 text-center text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 {{ $vRow['isCalculated'] ? 'bg-gray-100' : 'bg-white' }}"
                                                                                    value="{{ $existingTarget->target_pk ?? '' }}"
                                                                                placeholder="0" step="any" min="0"
                                                                                    {{ $vRow['isCalculated'] ? 'readonly' : '' }}
                                                                                    @if($isX) data-child-x="true" @endif
                                                                                    @if($isY) data-child-y="true" @endif
                                                                                    >
                                                                        </div>
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
                                <div class="border rounded-lg p-8 text-center">
                                    <div class="text-gray-400 mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-lg">Belum ada data matriks FRA</p>
                                    <p class="text-gray-400 text-sm mt-2">Silakan input template FRA tahun
                                        {{ $kegiatan->tahun_berjalan }} terlebih dahulu</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Tab Suplemen (hanya ditampilkan jika ada data) -->
                        @if (isset($hasSuplemenData) && $hasSuplemenData)
                            <div id="content-suplemen" class="space-y-6 hidden">
                                @php
                                    $currentTemplateJenis = 'PK Suplemen';
                                    $dataByTujuan = $matriksFraData
                                        ->filter(function ($matriks) use ($currentTemplateJenis) {
                                            return $matriks->template_fra->template_jenis->nama === $currentTemplateJenis;
                                        })
                                        ->sortBy(['tujuan', 'sasaran', 'indikator'])
                                        ->groupBy('tujuan');
                                @endphp

                                @forelse($dataByTujuan as $tujuan => $matriksList)
                                    <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden mb-8">
                                        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                                            <h2 class="font-bold text-white text-lg flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                {{ $tujuan }}
                                            </h2>
                                        </div>
                                        <div class="p-6">
                                            @php $dataBySasaran = $matriksList->groupBy('sasaran'); @endphp
                                            @foreach ($dataBySasaran as $sasaran => $indikatorList)
                                                <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden {{ !$loop->first ? 'mt-6' : '' }}">
                                                    <div class="bg-green-50 px-4 py-3 border-b border-green-200">
                                                        <h3 class="font-semibold text-gray-800 flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                            </svg>
                                                            {{ $sasaran }}
                                                        </h3>
                                                    </div>
                                                    <div class="overflow-x-auto">
                                                        <table class="w-full bg-white">
                                                            <thead>
                                                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                                                    <th class="text-left py-3 px-4 font-semibold text-gray-700" style="width: 70%">Indikator</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 15%">Satuan</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700" style="width: 15%">Target PK</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($indikatorList as $matriks)
                                                                    <tr
                                                                        class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                                                                        <td class="py-3 px-2">
                                                                            <div class="flex items-start">
                                                                                <div
                                                                                    class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0">
                                                                                </div>
                                                                                <span
                                                                                    class="font-medium text-gray-900 leading-relaxed">{{ $matriks->indikator }}</span>
                                                                            </div>
                                                                        </td>
                                                                        <td class="py-3 px-2 text-center">
                                                                            <span class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">{{ $matriks->satuan }}</span>
                                                                        </td>
                                                                        <td class="py-3 px-2">
                                                                            <div class="flex justify-center">
                                                                                <input type="number"
                                                                                    name="targets_pk[{{ $matriks->id }}]"
                                                                                    class="target-pk-input w-24 border border-gray-300 rounded-lg px-3 py-2 text-center text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                                                                    value="{{ $existingTargets[$matriks->id]->target_pk ?? '' }}"
                                                                                    placeholder="0" step="any" min="0"
                                                                                    >
                                                                            </div>
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
                                    <div class="border rounded-lg p-8 text-center">
                                        <div class="text-gray-400 mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 text-lg">Belum ada data matriks FRA Suplemen</p>
                                        <p class="text-gray-400 text-sm mt-2">Silakan input template FRA Suplemen tahun
                                            {{ $kegiatan->tahun_berjalan }} terlebih dahulu</p>
                                    </div>
                                @endforelse
                            </div>
                        @endif
                    </div>
            </div>
        </div>
    </div>
    </div>

    </form>
    </div>
    </div>
    </div>

    <!-- Spacing for sticky buttons -->
    <div class="pb-20"></div>

    <!-- Sticky Action Buttons -->
    <div class="fixed bottom-0 left-0 right-0 md:left-72 md:ml-2.5 bg-white/80 backdrop-blur-sm border-t border-gray-200 px-6 py-4 shadow-lg z-40">
        <div class="flex justify-between items-center mx-auto">
            <a href="{{ route('manajemen.pk') }}"
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

    {{-- Loading overlay menggunakan sistem global dari master layout --}}

@endsection

@push('scripts')
    <script>
        // Debounce function to limit how often a function can fire
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // LocalStorage save functionality (tidak auto submit)
        const saveDataToLocalStorageDebounced = debounce(saveDataToLocalStorage, 1000);

        // Add input event listeners to all target inputs
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('targetPkForm');

            form.addEventListener('input', debounce(function(event) {
                if (event.target.classList.contains('target-pk-input')) {
                    saveDataToLocalStorage();
                    updateProgress();

                    const row = event.target.closest('tr');
                    const isChildX = event.target.matches('[data-child-x]');
                    const isChildY = event.target.matches('[data-child-y]');
                    
                    if (isChildX || isChildY) {
                        calculateParents(row);
                    }
                }
            }, 300));

            // Load any saved data from localStorage
            loadDataFromLocalStorage();
            // Initial calculation for all groups
            document.querySelectorAll('tr[data-group-id]').forEach(row => {
                if(row.dataset.level === 'main' || row.dataset.level === 'sub'){
                    calculateParents(row);
                }
            });
            updateProgress();

            // Setup tab switching
            setupTabSwitching();
        });

        function setActionTypeAndSubmit(type) {
            document.getElementById('actionType').value = type;

            if (type === 'finalize') {
                // Validate all fields before finalization
                const inputs = document.querySelectorAll('input[name^="targets_pk"]');
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
                        `Mohon lengkapi semua field Target PK sebelum finalisasi.<br>
                        Progress saat ini: ${percentage}%`, {
                            confirmText: 'Oke, Saya Mengerti',
                            showCancel: false
                        });
                    return false;
                }

                // Show confirmation modal
                showModal('question', 'Konfirmasi Finalisasi',
                    'Apakah Anda yakin ingin memfinalisasi target PK?<br>Data yang sudah difinalisasi tidak dapat diubah lagi.', {
                        confirmText: 'Ya, Finalisasi',
                        cancelText: 'Batal',
                        showCancel: true,
                        confirmCallback: () => {
                            showGlobalLoading('Memfinalisasi data...');
                            submitFormWithErrorHandling();
                        }
                    });
                return false;
            }

            // For regular save
            showGlobalLoading('Menyimpan data...');
            submitFormWithErrorHandling();
        }

        // New function to handle form submission with better error handling
        function submitFormWithErrorHandling() {
            const timestamp = new Date().toISOString();
            console.log(`🚀 [${timestamp}] PK submitFormWithErrorHandling called`);
            
            const form = document.getElementById('targetPkForm');
            const actionType = document.getElementById('actionType').value;
            
            console.log(`📋 [${timestamp}] PK Form submission details:`, {
                action: form.action,
                method: form.method,
                actionType: actionType,
                formId: form.id,
                userAgent: navigator.userAgent,
                url: window.location.href
            });
            
            const formData = new FormData(form);
            
            // 🔧 FIX: Remove _token from FormData to avoid conflict with X-CSRF-TOKEN header
            // Laravel expects CSRF token either in form data (_token) OR in header (X-CSRF-TOKEN), not both
            // Using both can cause 419 "Page Expired" errors due to token validation conflicts
            formData.delete('_token');
            
            // Count form data entries
            let targetCount = 0;
            let otherCount = 0;
            
            console.log(`📊 [${timestamp}] PK Form data being submitted:`);
            for (let [key, value] of formData.entries()) {
                if (key.startsWith('targets_pk[')) {
                    targetCount++;
                } else {
                    otherCount++;
                }
                console.log(`  ${key}: ${value}`);
            }
            
            console.log(`📈 [${timestamp}] PK Form data summary:`, {
                targetFields: targetCount,
                otherFields: otherCount,
                totalFields: targetCount + otherCount
            });

            const requestStartTime = performance.now();

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                const requestEndTime = performance.now();
                const requestDuration = (requestEndTime - requestStartTime).toFixed(2);
                
                console.log(`📡 [${timestamp}] PK Response received:`, {
                    status: response.status,
                    statusText: response.statusText,
                    ok: response.ok,
                    headers: Object.fromEntries(response.headers.entries()),
                    url: response.url,
                    redirected: response.redirected,
                    type: response.type,
                    requestDuration: `${requestDuration}ms`
                });
                
                if (!response.ok) {
                    console.error(`❌ [${timestamp}] PK HTTP Error:`, {
                        status: response.status,
                        statusText: response.statusText
                    });
                }
                
                return response.json();
            })
            .then(data => {
                console.log(`📦 [${timestamp}] PK Response data parsed:`, {
                    success: data.success,
                    message: data.message,
                    hasErrors: !!data.errors,
                    errorCount: data.errors ? Object.keys(data.errors).length : 0,
                    fullData: data
                });
                
                hideGlobalLoading();
                
                if (data.success) {
                    console.log(`✅ [${timestamp}] PK Success scenario - actionType: ${actionType}`);
                    
                    // Success scenario
                    if (actionType === 'finalize') {
                        console.log(`🏁 [${timestamp}] PK Finalization successful, showing modal and redirecting`);
                        showModal('success', 'Berhasil', data.message, {
                            confirmText: 'Oke',
                            showCancel: false,
                            confirmCallback: () => {
                                console.log(`🔄 [${timestamp}] PK Redirecting to manajemen.pk`);
                                window.location.href = "{{ route('manajemen.pk') }}";
                            }
                        });
                    } else { // For 'save' action
                        console.log(`💾 [${timestamp}] PK Save successful, showing success message`);
                        showSuccess(data.message); // Use global flash message
                        // No redirect for save action
                    }
                } else {
                    console.error(`❌ [${timestamp}] PK Server returned error:`, {
                        message: data.message,
                        errors: data.errors,
                        actionType: actionType
                    });
                    
                    // Error scenario
                    let errorMessage = data.message || 'Terjadi kesalahan saat menyimpan data';
                    
                    // If there are specific validation errors, format them
                    if (data.errors) {
                        console.log(`🔍 [${timestamp}] PK Processing validation errors:`, data.errors);
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
                const requestEndTime = performance.now();
                const requestDuration = (requestEndTime - requestStartTime).toFixed(2);
                
                hideGlobalLoading();
                
                console.error(`💥 [${timestamp}] PK Network/Parse error:`, {
                    error: error,
                    message: error.message,
                    stack: error.stack,
                    name: error.name,
                    requestDuration: `${requestDuration}ms`,
                    actionType: actionType
                });
                
                showModal('error', 'Kesalahan Sistem', `Terjadi kesalahan tidak terduga. Silakan coba lagi atau hubungi administrator.<br><br>Detail: ${error.message}<br>Durasi Request: ${requestDuration}ms`, {
                    confirmText: 'Tutup',
                    showCancel: false
                });
            });
        }

        // Update progress bar
        function updateProgress() {
            const inputs = document.querySelectorAll('input[name^="targets_pk"]');
            let filledInputs = 0;
            const totalInputs = inputs.length;

            inputs.forEach(input => {
                if (input.value && input.value.trim() !== '' && !input.readOnly) {
                    filledInputs++;
                }
            });

            const totalMovableInputs = document.querySelectorAll('input[name^="targets_pk"]:not([readonly])').length;

            const percentage = totalMovableInputs > 0 ? Math.round((filledInputs / totalMovableInputs) * 100) : 100;
            document.getElementById('progressBar').style.width = percentage + '%';
            document.getElementById('progressText').textContent = percentage + '% selesai';
        }

        // Auto-save to localStorage
        function saveDataToLocalStorage() {
            const formData = {};
            const inputs = document.querySelectorAll('input[name^="targets_pk"]');

            inputs.forEach(input => {
                formData[input.name] = input.value;
            });

            localStorage.setItem('target_pk_data_{{ $kegiatan->id }}', JSON.stringify(formData));
        }

        // Load data from localStorage
        function loadDataFromLocalStorage() {
            const savedData = localStorage.getItem('target_pk_data_{{ $kegiatan->id }}');

            if (savedData) {
                const formData = JSON.parse(savedData);

                Object.keys(formData).forEach(name => {
                    const input = document.querySelector(`[name="${name}"]`);
                    if (input && !input.value) {
                        input.value = formData[name];
                    }
                });
            }
        }

        // Clear localStorage on successful submit
        window.addEventListener('beforeunload', function() {
            if (document.getElementById('targetPkForm').dataset.submitted === 'true') {
                localStorage.removeItem('target_pk_data_{{ $kegiatan->id }}');
            }
        });

        function calculateParents(startRow) {
            const groupId = startRow.dataset.groupId;
            
            // Find parent sub-indicator to calculate (if any)
            const parentSubIndicatorContent = startRow.dataset.parentSubIndicator;
            if(parentSubIndicatorContent) {
                 const subRow = document.querySelector(`tr[data-group-id="${groupId}"][data-level="sub"][data-content="${parentSubIndicatorContent}"]`);
                 if(subRow) calculateAndSetValue(subRow);
            }
           
            // Always try to calculate main indicator
            const mainRow = document.querySelector(`tr[data-group-id="${groupId}"][data-level="main"]`);
            if(mainRow) calculateAndSetValue(mainRow);
        }
        
        function calculateAndSetValue(parentRow) {
            const parentInput = parentRow.querySelector('input.target-pk-input');
            if (!parentInput || !parentInput.readOnly) return;
            
            const groupId = parentRow.dataset.groupId;
            const level = parentRow.dataset.level;
            
            let xInput, yInput;

            if (level === 'main') {
                xInput = document.querySelector(`tr[data-group-id="${groupId}"][data-level="detail_indicator"] input[data-child-x]`);
                yInput = document.querySelector(`tr[data-group-id="${groupId}"][data-level="detail_indicator"] input[data-child-y]`);
            } else if (level === 'sub') {
                const subIndicatorContent = parentRow.dataset.content;
                xInput = document.querySelector(`tr[data-group-id="${groupId}"][data-level="detail_sub"][data-parent-sub-indicator="${subIndicatorContent}"] input[data-child-x]`);
                yInput = document.querySelector(`tr[data-group-id="${groupId}"][data-level="detail_sub"][data-parent-sub-indicator="${subIndicatorContent}"] input[data-child-y]`);
            }

            if (xInput && yInput) {
                const xValue = parseFloat(xInput.value);
                const yValue = parseFloat(yInput.value);
                let result = NaN;

                if (!isNaN(xValue) && !isNaN(yValue) && yValue !== 0) {
                    result = (xValue / yValue) * 100;
                }

                if (!isNaN(result)) {
                    parentInput.value = (result % 1 === 0) ? result.toFixed(0) : result.toFixed(2);
                } else {
                    parentInput.value = '';
                }
            }
        }

        // Tab switching functionality
        function setupTabSwitching() {
            const tabIku = document.getElementById('tab-iku');
            const tabSuplemen = document.getElementById('tab-suplemen');
            const contentIku = document.getElementById('content-iku');
            const contentSuplemen = document.getElementById('content-suplemen');

            // Function to switch tabs with state saving
            function switchTab(activeTab, activeContent, inactiveTab, inactiveContent, tabId) {
                // Update active tab styles
                activeTab.classList.remove('text-gray-600', 'bg-gray-100', 'hover:bg-gray-200');
                activeTab.classList.add('bg-gradient-to-r', 'from-red-600', 'to-red-700', 'text-white', 'border-b-2',
                    'border-red-600');

                // Update inactive tab styles
                if (inactiveTab) {
                    inactiveTab.classList.remove('bg-gradient-to-r', 'from-red-600', 'to-red-700', 'text-white',
                        'border-b-2', 'border-red-600');
                    inactiveTab.classList.add('text-gray-600', 'bg-gray-100', 'hover:bg-gray-200');
                }

                // Show active content, hide inactive content
                activeContent.classList.remove('hidden');
                if (inactiveContent) {
                    inactiveContent.classList.add('hidden');
                }

                // Save active tab to localStorage and URL
                localStorage.setItem('form_target_pk_active_tab', tabId);
                updateURLWithTab(tabId);
            }

            // IKU tab click handler
            if (tabIku) {
                tabIku.addEventListener('click', function() {
                    switchTab(tabIku, contentIku, tabSuplemen, contentSuplemen, 'tab-iku');
                });
            }

            // Suplemen tab click handler
            if (tabSuplemen) {
                tabSuplemen.addEventListener('click', function() {
                    switchTab(tabSuplemen, contentSuplemen, tabIku, contentIku, 'tab-suplemen');
                });
            }

            // Update URL when tab changes (without page reload)
            function updateURLWithTab(tabId) {
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId.replace('tab-', ''));
                window.history.replaceState({}, '', url);
            }

            // Restore last active tab on page load
            // Check URL parameter first, then localStorage
            const urlParams = new URLSearchParams(window.location.search);
            const urlTab = urlParams.get('tab');
            const savedTab = urlTab ? 'tab-' + urlTab : localStorage.getItem('form_target_pk_active_tab');

            if (savedTab === 'tab-suplemen' && tabSuplemen && contentSuplemen) {
                switchTab(tabSuplemen, contentSuplemen, tabIku, contentIku, 'tab-suplemen');
            } else if (tabIku && contentIku) {
                // Default to IKU tab
                switchTab(tabIku, contentIku, tabSuplemen, contentSuplemen, 'tab-iku');
            }
        }
    </script>

    <style>
        .recently-saved {
            border-color: #10B981 !important;
            background-color: #ECFDF5 !important;
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
    </style>
@endpush
