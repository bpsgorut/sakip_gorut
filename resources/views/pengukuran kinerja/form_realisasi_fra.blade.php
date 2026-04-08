@extends('components.master')

@section('title', 'Form Realisasi FRA')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

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
                            <h1 class="text-3xl font-bold mb-2">Form Realisasi FRA</h1>
                            <p class="text-red-100 text-lg">Triwulan {{ $triwulan ?? ($triwulanObj->nomor ?? '') }} - Tahun {{ $fra->tahun_berjalan }}</p>
                        </div>
                        <div class="text-white text-right">
                            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm font-medium">Realisasi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($readOnly)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-xl p-4 mb-6 shadow-sm">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 mr-3" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="text-yellow-800 font-medium">Mode Baca Saja</h3>
                            <p class="text-yellow-700 text-sm mt-1">Form ini hanya dapat dilihat dalam mode baca saja.
                                Periode input telah berakhir atau triwulan telah diselesaikan.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <form action="{{ route('simpan.realisasi.fra', ['fra' => $fra->id, 'triwulan' => $triwulan]) }}"
                    method="POST" enctype="multipart/form-data" id="realisasiForm">
                    @csrf
                    <input type="hidden" name="action_type" id="actionType" value="save">

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
                            @if ($hasSuplemenData)
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
                            @if ($hasUmumData)
                                <button id="tab-umum" type="button"
                                    class="px-6 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium rounded-t-xl transition-all duration-200 mr-2">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 2a1 1 0 011-1h6a1 1 0 110 2H7z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Umum
                                    </div>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="tab-content px-6">
                        <!-- IKU Tab Content -->
                        <div id="content-iku" class="space-y-6">
                            @php
                                $dataByTujuanIku = $matriksList
                                    ->filter(fn($m) => $m->template_fra->template_jenis->nama === 'PK IKU')
                                    ->sortBy([
                                        'tujuan',
                                        'sasaran',
                                        'indikator',
                                        'detail_indikator',
                                        'sub_indikator',
                                        'detail_sub',
                                    ])
                                    ->groupBy('tujuan');
                            @endphp

                            @forelse($dataByTujuanIku as $tujuan => $matriksTujuan)
                                <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden mb-8">
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

                                    <div class="p-6">
                                        @php
                                            $dataBySasaran = $matriksTujuan->groupBy('sasaran');
                                        @endphp

                                        @foreach ($dataBySasaran as $sasaran => $matriksSasaran)
                                            <div
                                                class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden {{ !$loop->first ? 'mt-6' : '' }}">
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

                                                <div class="overflow-x-auto">
                                                    <table class="w-full bg-white table-fixed">
                                                        <thead>
                                                            <tr
                                                                class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                                                <th class="text-left py-3 px-4 font-semibold text-gray-700"
                                                                    style="width: 50%;">Indikator</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                                    style="width: 8%;">Satuan</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                                    style="width: 8%;">Target</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                                    style="width: 12%;">Realisasi</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                                    style="width: 11%;">Capkin Kumulatif</th>
                                                                <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                                    style="width: 11%;">Capkin Setahun</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $dataByIndikator = $matriksSasaran->groupBy(
                                                                    'indikator',
                                                                );
                                                            @endphp
                                                            @foreach ($dataByIndikator as $indikatorName => $items)
                                                                @php
                                                                    $mainIndicator = $items->first(
                                                                        fn($item) => empty($item->detail_indikator) &&
                                                                            empty($item->sub_indikator) &&
                                                                            empty($item->detail_sub),
                                                                    );
                                                                    if (!$mainIndicator) {
                                                                        continue;
                                                                    }

                                                                    $virtualRows = [];
                                                                    $processedDetails = [];

                                                                    if ($mainIndicator) {
                                                                        $virtualRows[] = [
                                                                            'matriks' => $mainIndicator,
                                                                            'level' => 'indikator',
                                                                        ];
                                                                    }

                                                                    foreach (
                                                                        $items->sortBy('detail_indikator')
                                                                        as $item
                                                                    ) {
                                                                        if (
                                                                            !empty($item->detail_indikator) &&
                                                                            !in_array(
                                                                                'detail_indicator_' .
                                                                                    $item->detail_indikator,
                                                                                $processedDetails,
                                                                            )
                                                                        ) {
                                                                            $virtualRows[] = [
                                                                                'matriks' => $item,
                                                                                'level' => 'detail_indikator',
                                                                            ];
                                                                            $processedDetails[] =
                                                                                'detail_indicator_' .
                                                                                $item->detail_indikator;
                                                                        }
                                                                    }

                                                                    $subIndicatorGroups = $items
                                                                        ->whereNotNull('sub_indikator')
                                                                        ->sortBy('sub_indikator')
                                                                        ->groupBy('sub_indikator');
                                                                    foreach (
                                                                        $subIndicatorGroups
                                                                        as $subName => $subItems
                                                                    ) {
                                                                        if (
                                                                            !in_array(
                                                                                'sub_' . $subName,
                                                                                $processedDetails,
                                                                            )
                                                                        ) {
                                                                            $virtualRows[] = [
                                                                                'matriks' => $subItems->first(),
                                                                                'level' => 'sub_indikator',
                                                                            ];
                                                                            $processedDetails[] = 'sub_' . $subName;
                                                                        }
                                                                        foreach (
                                                                            $subItems->sortBy('detail_sub')
                                                                            as $detailSub
                                                                        ) {
                                                                            if (
                                                                                !empty($detailSub->detail_sub) &&
                                                                                !in_array(
                                                                                    'detail_sub_' .
                                                                                        $detailSub->detail_sub,
                                                                                    $processedDetails,
                                                                                )
                                                                            ) {
                                                                                $virtualRows[] = [
                                                                                    'matriks' => $detailSub,
                                                                                    'level' => 'detail_sub',
                                                                                ];
                                                                                $processedDetails[] =
                                                                                    'detail_sub_' .
                                                                                    $detailSub->detail_sub;
                                                                            }
                                                                        }
                                                                    }
                                                                @endphp

                                                                @foreach ($virtualRows as $vRow)
                                                                    @php
                                                                        $matriks = $vRow['matriks'];
                                                                        $level = $vRow['level'];
                                                                        $isParent =
                                                                            $level === 'indikator' ||
                                                                            $level === 'sub_indikator';

                                                                        $xy_regex = fn($char) => '/^' .
                                                                            $char .
                                                                            '[\.:\s]/i';
                                                                        $content = '';
                                                                        if ($level === 'detail_indikator') {
                                                                            $content = $matriks->detail_indikator;
                                                                        } elseif ($level === 'detail_sub') {
                                                                            $content = $matriks->detail_sub;
                                                                        }

                                                                        $isY =
                                                                            !empty($content) &&
                                                                            preg_match(
                                                                                $xy_regex('y'),
                                                                                strip_tags($content),
                                                                            );
                                                                        $isX =
                                                                            !empty($content) &&
                                                                            preg_match(
                                                                                $xy_regex('x'),
                                                                                strip_tags($content),
                                                                            );

                                                                        $targetPk = $targetPkData->get($matriks->id);
                                                                        $realisasiData = $existingRealisasi->get(
                                                                            $matriks->id,
                                                                        );

                                                                        $realisasiValue = $realisasiData->realisasi ?? null;
                                                                        // Check if this should be calculated automatically 
                                                                        $shouldCalculateAutomatically = false;
                                                                        if ($level === 'indikator') {
                                                                            // Check if this indicator has X and Y detail indicators
                                                                            $hasXDetail = $items->contains(function($item) use ($xy_regex) {
                                                                                return !empty($item->detail_indikator) && preg_match($xy_regex('x'), strip_tags($item->detail_indikator));
                                                                            });
                                                                            $hasYDetail = $items->contains(function($item) use ($xy_regex) {
                                                                                return !empty($item->detail_indikator) && preg_match($xy_regex('y'), strip_tags($item->detail_indikator));
                                                                            });
                                                                            $shouldCalculateAutomatically = $hasXDetail && $hasYDetail;
                                                                        } elseif ($level === 'sub_indikator') {
                                                                            // Check if this sub-indicator has X and Y detail subs
                                                                            $hasXDetailSub = $items->contains(function($item) use ($xy_regex, $matriks) {
                                                                                return $item->sub_indikator === $matriks->sub_indikator && !empty($item->detail_sub) && preg_match($xy_regex('x'), strip_tags($item->detail_sub));
                                                                            });
                                                                            $hasYDetailSub = $items->contains(function($item) use ($xy_regex, $matriks) {
                                                                                return $item->sub_indikator === $matriks->sub_indikator && !empty($item->detail_sub) && preg_match($xy_regex('y'), strip_tags($item->detail_sub));
                                                                            });
                                                                            $shouldCalculateAutomatically = $hasXDetailSub && $hasYDetailSub;
                                                                        }
                                                                        
                                                                        $isRowReadOnly =
                                                                            $readOnly ||
                                                                            $shouldCalculateAutomatically;

                                                                        $targetFra = $matriks->target_fra
                                                                            ? $matriks->target_fra
                                                                                ->{'target_tw' . $triwulan}
                                                                            : null;

                                                                        // Function to format numbers
                                                                        if (!function_exists('format_number')) {
                                                                            function format_number($number)
                                                                            {
                                                                                if (!is_numeric($number)) {
                                                                                    return $number;
                                                                                }
                                                                                return floor($number) == $number
                                                                                    ? number_format(
                                                                                        $number,
                                                                                        0,
                                                                                        '.',
                                                                                        ',',
                                                                                    )
                                                                                    : number_format(
                                                                                        $number,
                                                                                        2,
                                                                                        '.',
                                                                                        ',',
                                                                                    );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    <tr class="border-b border-gray-100"
                                                                        data-row-id="{{ $matriks->id }}"
                                                                        data-group-id="{{ $mainIndicator->id }}"
                                                                        data-parent-sub-indicator="{{ $matriks->sub_indikator ?? '' }}"
                                                                        data-level="{{ $level }}"
                                                                        data-content="{{ $content ?: ($matriks->sub_indikator ?: $matriks->indikator) }}"
                                                                        data-target-fra="{{ $targetFra ?? 0 }}"
                                                                        data-target-pk="{{ $targetPk->target_pk ?? 0 }}">

                                                                        <td class="py-2 px-3 align-middle">
                                                                            @if ($level === 'indikator')
                                                                                <div class="flex items-start">
                                                                                    <div
                                                                                        class="w-2 h-2 bg-red-500 rounded-full mt-1.5 mr-3 flex-shrink-0">
                                                                                    </div>
                                                                                    <span
                                                                                        class="font-bold text-gray-800 leading-normal">{{ $matriks->indikator }}</span>
                                                                                </div>
                                                                            @elseif ($level === 'detail_indikator')
                                                                                <div class="flex items-start ml-4">
                                                                                    <div
                                                                                        class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 mr-3 flex-shrink-0">
                                                                                    </div>
                                                                                    <span
                                                                                        class="text-gray-700 leading-normal">{{ $matriks->detail_indikator }}</span>
                                                                                </div>
                                                                            @elseif ($level === 'sub_indikator')
                                                                                <div class="flex items-start ml-10">
                                                                                    <div
                                                                                        class="w-2 h-2 bg-orange-400 rounded-full mt-1.5 mr-3 flex-shrink-0">
                                                                                    </div>
                                                                                    <span
                                                                                        class="font-bold text-gray-800 leading-normal">{{ $matriks->sub_indikator }}</span>
                                                                                </div>
                                                                            @else
                                                                                <div class="flex items-start ml-14">
                                                                                    <div
                                                                                        class="w-2 h-2 bg-green-400 rounded-full mt-1.5 mr-3 flex-shrink-0">
                                                                                    </div>
                                                                                    <span
                                                                                        class="text-gray-600 leading-normal">{{ $matriks->detail_sub }}</span>
                                                                                </div>
                                                                            @endif
                                                                        </td>

                                                                        <td class="py-2 px-2 text-center align-middle">
                                                                            <span
                                                                class="inline-block bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">{{ $matriks->satuan }}</span>
                                                                        </td>

                                                                        <td class="py-2 px-2 text-center align-middle">
                                                                            <span
                                                                class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">{{ is_numeric($targetFra) ? format_number($targetFra) : '-' }}</span>
                                                                        </td>

                                                                        <td class="py-2 px-2 align-middle">
                                                                            <input type="number"
                                                                                name="realisasi[{{ $matriks->id }}]"
                                                                                step="any" min="0"
                                                                                class="realisasi-input w-full h-9 border-2 border-gray-200 rounded-lg px-3 py-1 text-center text-sm align-middle focus:border-blue-500 focus:ring-2 focus:ring-blue-200 {{ $isRowReadOnly ? 'bg-gray-100' : 'bg-white' }}"
                                                                                value="{{ format_number($realisasiValue) }}"
                                                                                {{ $isRowReadOnly ? 'readonly' : '' }}
                                                                            @if (!$isParent) placeholder="0" @endif
                                                                            @if($isX) data-child-x-for="{{ $mainIndicator->id }}" @endif
                                                                            @if($isY) data-child-y-for="{{ $mainIndicator->id }}" @endif>
                                                                        </td>

                                                                        <td class="py-2 px-2 align-middle text-center">
                                                                            @if ($level === 'indikator' || $level === 'sub_indikator')
                                                                                <input type="number"
                                                                                    name="capkin_kumulatif[{{ $matriks->id }}]"
                                                                                    step="any"
                                                                                    class="capkin-kumulatif-output w-full border-gray-200 rounded-lg px-3 py-2 text-center text-sm bg-gray-100 font-medium text-gray-700"
                                                                                    value="{{ format_number($realisasiData->capkin_kumulatif ?? 0) }}"
                                                                                    readonly>
                                                                            @else
                                                                                <span
                                                                                    class="text-gray-400 align-middle">-</span>
                                                                            @endif
                                                                        </td>

                                                                        <td class="py-2 px-2 align-middle text-center">
                                                                            @if ($level === 'indikator' || $level === 'sub_indikator')
                                                                                <input type="number"
                                                                                    name="capkin_setahun[{{ $matriks->id }}]"
                                                                                    step="any"
                                                                                    class="capkin-setahun-output w-full border-gray-200 rounded-lg px-3 py-2 text-center text-sm bg-gray-100 font-medium text-gray-700"
                                                                                    value="{{ format_number(is_numeric($realisasiData->capkin_setahun ?? null) ? $realisasiData->capkin_setahun : 0) }}"
                                                                                    readonly>
                                                                            @else
                                                                                <span
                                                                                    class="text-gray-400 align-middle">-</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach

                                                                @if ($mainIndicator)
                                                                    <tr class="bg-gradient-to-r from-gray-50 to-blue-50">
                                                                        <td class="py-6 px-4" colspan="6">
                                                                            @php
                                                                                $realisasiForDetail =
                                                                                    $existingRealisasi[
                                                                                        $mainIndicator->id
                                                                                    ] ?? null;
                                                                            @endphp
                                                                            <div
                                                                                class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                                                                                <h4
                                                                                    class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        class="h-5 w-5 mr-2 text-blue-600"
                                                                                        fill="none" viewBox="0 0 24 24"
                                                                                        stroke="currentColor">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            stroke-width="2"
                                                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                                    </svg>
                                                                                    Detail Realisasi
                                                                                </h4>
                                                                                <div
                                                                                    class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                                                                    <div class="space-y-4">
                                                                                        <div>
                                                                                            <label
                                                                                                class="block text-sm font-semibold text-gray-700 mb-2">Kendala</label>
                                                                                            <textarea name="kendala[{{ $mainIndicator->id }}]" rows="4"
                                                                                                class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 {{ $readOnly ? 'bg-gray-100' : 'bg-white' }}"
                                                                                                placeholder="Jelaskan kendala..." {{ $readOnly ? 'readonly' : '' }}>{{ $realisasiForDetail->kendala ?? '' }}</textarea>
                                                                                        </div>
                                                                                        <div>
                                                                                            <label
                                                                                                class="block text-sm font-semibold text-gray-700 mb-2">Solusi</label>
                                                                                            <textarea name="solusi[{{ $mainIndicator->id }}]" rows="4"
                                                                                                class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 {{ $readOnly ? 'bg-gray-100' : 'bg-white' }}"
                                                                                                placeholder="Jelaskan solusi..." {{ $readOnly ? 'readonly' : '' }}>{{ $realisasiForDetail->solusi ?? '' }}</textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="space-y-4">
                                                                                        <div>
                                                                                            <label
                                                                                                class="block text-sm font-semibold text-gray-700 mb-2">Tindak
                                                                                                Lanjut</label>
                                                                                            <textarea name="tindak_lanjut[{{ $mainIndicator->id }}]" rows="4"
                                                                                                class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 {{ $readOnly ? 'bg-gray-100' : 'bg-white' }}"
                                                                                                placeholder="Jelaskan tindak lanjut..." {{ $readOnly ? 'readonly' : '' }}>{{ $realisasiForDetail->tindak_lanjut ?? '' }}</textarea>
                                                                                        </div>
                                                                                        <div
                                                                                            class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                                            <div>
                                                                                                <label
                                                                                                    class="block text-sm font-semibold text-gray-700 mb-2">PIC
                                                                                                    Tindak Lanjut</label>
                                                                                                <select
                                                                                                    name="pic_tindak_lanjut_id[{{ $mainIndicator->id }}]"
                                                                                                    class="w-full border-2 border-gray-200 rounded-lg px-2 py-2 text-sm {{ $readOnly ? 'bg-gray-100' : 'bg-white' }}"
                                                                                                    {{ $readOnly ? 'disabled' : '' }}>
                                                                                                    <option value="">
                                                                                                        Pilih PIC</option>
                                                                                                    @foreach ($penggunas as $pengguna)
                                                                                                        <option
                                                                                                            value="{{ $pengguna->id }}"
                                                                                                            {{ ($realisasiForDetail->pic_tindak_lanjut_id ?? '') == $pengguna->id ? 'selected' : '' }}>
                                                                                                            {{ $pengguna->name }}
                                                                                                            ({{ $pengguna->bidang }})
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                            <div>
                                                                                                <label
                                                                                                    class="block text-sm font-semibold text-gray-700 mb-2">Batas
                                                                                                    Waktu</label>
                                                                                                <input type="date"
                                                                                                    name="batas_waktu_tindak_lanjut[{{ $mainIndicator->id }}]"
                                                                                                    class="w-full border-2 border-gray-200 rounded-lg px-2 py-2 text-sm {{ $readOnly ? 'bg-gray-100' : 'bg-white' }}"
                                                                                                    value="{{ $realisasiForDetail && $realisasiForDetail->batas_waktu_tindak_lanjut ? $realisasiForDetail->batas_waktu_tindak_lanjut->format('Y-m-d') : '' }}"
                                                                                                    {{ $readOnly ? 'readonly' : '' }}>
                                                                                            </div>
                                                                                        </div>
                                                                                        @if (!$readOnly)
                                                                                            <div>
                                                                                                <label
                                                                                                    class="block text-sm font-semibold text-gray-700 mb-2">Bukti
                                                                                                    Dukung (PDF)</label>
                                                                                                <div class="relative">
                                                                                                    <input type="file"
                                                                                                        id="pdfUpload_{{ $mainIndicator->id }}"
                                                                                                        name="bukti_dukung[{{ $mainIndicator->id }}][]"
                                                                                                        hidden
                                                                                                        accept="application/pdf"
                                                                                                        multiple
                                                                                                        onchange="updateFileDisplay('{{ $mainIndicator->id }}')">
                                                                                                    <div
                                                                                                        class="flex items-center gap-3">
                                                                                                        <input
                                                                                                            type="text"
                                                                                                            readonly
                                                                                                            placeholder="Pilih file PDF"
                                                                                                            id="fileNamesDisplay_{{ $mainIndicator->id }}"
                                                                                                            class="flex-1 px-3 py-2 border-2 border-gray-200 rounded-lg bg-gray-50 cursor-pointer text-sm"
                                                                                                            onclick="document.getElementById('pdfUpload_{{ $mainIndicator->id }}').click()">
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 text-sm"
                                                                                                            onclick="handleUploadClick({{ $mainIndicator->id }}, {{ $realisasiForDetail ? $realisasiForDetail->id : 'null' }})">Upload</button>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endif
                                                                                        <div id="filePreview_{{ $mainIndicator->id }}"
                                                                                            class="space-y-2 mt-2">
                                                                                            @if($realisasiForDetail && $realisasiForDetail->buktidukung_fra && $realisasiForDetail->buktidukung_fra->count() > 0)
                                                                                                @foreach ($realisasiForDetail->buktidukung_fra as $bukti)
                                                                                                    <div
                                                                                                        class="flex items-center justify-between bg-slate-50 border border-slate-200 px-4 py-3 rounded-lg uploaded-file shadow-sm">
                                                                                                        <div class="flex items-center flex-1 min-w-0">
                                                                                                            <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                                                                                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                                                                </svg>
                                                                                                            </div>
                                                                                                            <div class="flex-1 min-w-0">
                                                                                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $bukti->nama_dokumen }}</p>
                                                                                                                <p class="text-xs text-gray-500">Diupload: {{ $bukti->created_at->format('d M Y H:i') }}</p>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="flex gap-2 ml-4 flex-shrink-0">
                                                                                                            <a href="{{ $bukti->webViewLink }}" target="_blank" 
                                                                                                               class="px-3 py-1.5 bg-slate-600 text-white text-xs font-medium rounded-md hover:bg-slate-700 transition-colors flex items-center gap-1">
                                                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                                                                </svg>
                                                                                                                Lihat
                                                                                                            </a>
                                                                                                            @if (!$readOnly)
                                                                                                                <button type="button" 
                                                                                                                        class="px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 transition-colors flex items-center gap-1" 
                                                                                                                        onclick="hapusBuktiDukung({{ $bukti->id }}, this)">
                                                                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                                                                    </svg>
                                                                                                                    Hapus
                                                                                                                </button>
                                                                                                            @endif
                                                                                                        </div>
                                                                                                    </div>
                                                                                                @endforeach
                                                                                            @endif
                                                                                            

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endif
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
                                    <p class="text-gray-500 text-lg">Tidak ada target yang diassign untuk anda saat ini.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Suplemen Tab Content -->
                        @if ($hasSuplemenData)
                            <div id="content-suplemen" class="space-y-6 hidden">
                                @php
                                    $dataByTujuanSuplemen = $matriksList
                                        ->filter(fn($m) => $m->template_fra->template_jenis->nama === 'PK Suplemen')
                                        ->sortBy([
                                            'tujuan',
                                            'sasaran',
                                            'indikator',
                                            'detail_indikator',
                                            'sub_indikator',
                                            'detail_sub',
                                        ])
                                        ->groupBy('tujuan');
                                @endphp

                                @forelse($dataByTujuanSuplemen as $tujuan => $matriksTujuan)
                                    <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden mb-8">
                                        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                                            <h2 class="font-bold text-white text-lg flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                {{ $tujuan }}
                                            </h2>
                                        </div>

                                        <div class="p-6">
                                            @php
                                                $dataBySasaran = $matriksTujuan->groupBy('sasaran');
                                            @endphp

                                            @foreach ($dataBySasaran as $sasaran => $matriksSasaran)
                                                <div
                                                    class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden {{ !$loop->first ? 'mt-6' : '' }}">
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

                                                    <div class="overflow-x-auto">
                                                        <table class="w-full bg-white table-fixed">
                                                            <thead>
                                                                <tr
                                                                    class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                                                    <th class="text-left py-3 px-4 font-semibold text-gray-700"
                                                                        style="width: 45%;">Indikator</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                                        style="width: 7%;">Satuan</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                                        style="width: 8%;">Target</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                                        style="width: 10%;">Realisasi</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                                        style="width: 15%;">Capkin Kumulatif</th>
                                                                    <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                                        style="width: 15%;">Capkin Setahun</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $dataByIndikator = $matriksSasaran->groupBy(
                                                                        'indikator',
                                                                    );
                                                                @endphp
                                                                @foreach ($dataByIndikator as $indikatorName => $items)
                                                                    @php
                                                                        $mainIndicator = $items->first(
                                                                            fn($item) => empty(
                                                                                $item->detail_indikator
                                                                            ) &&
                                                                                empty($item->sub_indikator) &&
                                                                                empty($item->detail_sub),
                                                                        );

                                                                        $virtualRows = [];
                                                                        $processedDetails = [];

                                                                        if ($mainIndicator) {
                                                                            $virtualRows[] = [
                                                                                'matriks' => $mainIndicator,
                                                                                'level' => 'indikator',
                                                                            ];
                                                                        }
                                                                        foreach (
                                                                            $items->whereNotNull('detail_indikator')
                                                                            as $detail
                                                                        ) {
                                                                            if (
                                                                                !in_array(
                                                                                    $detail->detail_indikator,
                                                                                    $processedDetails,
                                                                                )
                                                                            ) {
                                                                                $virtualRows[] = [
                                                                                    'matriks' => $detail,
                                                                                    'level' => 'detail_indikator',
                                                                                ];
                                                                                $processedDetails[] =
                                                                                    $detail->detail_indikator;
                                                                            }
                                                                        }
                                                                        $subIndicatorGroups = $items
                                                                            ->whereNotNull('sub_indikator')
                                                                            ->groupBy('sub_indikator');
                                                                        foreach (
                                                                            $subIndicatorGroups
                                                                            as $subName => $subItems
                                                                        ) {
                                                                            $firstSubItem = $subItems->first();
                                                                            if (
                                                                                !in_array($subName, $processedDetails)
                                                                            ) {
                                                                                $virtualRows[] = [
                                                                                    'matriks' => $firstSubItem,
                                                                                    'level' => 'sub_indikator',
                                                                                ];
                                                                                $processedDetails[] = $subName;
                                                                            }
                                                                            foreach (
                                                                                $subItems->whereNotNull('detail_sub')
                                                                                as $detailSub
                                                                            ) {
                                                                                if (
                                                                                    !in_array(
                                                                                        $detailSub->detail_sub,
                                                                                        $processedDetails,
                                                                                    )
                                                                                ) {
                                                                                    $virtualRows[] = [
                                                                                        'matriks' => $detailSub,
                                                                                        'level' => 'detail_sub',
                                                                                    ];
                                                                                    $processedDetails[] =
                                                                                        $detailSub->detail_sub;
                                                                                }
                                                                            }
                                                                        }
                                                                    @endphp

                                                                    @foreach ($virtualRows as $vRow)
                                                                        @php
                                                                            $matriks = $vRow['matriks'];
                                                                            $level = $vRow['level'];

                                                                            $xy_regex = fn($char) => '/^' .
                                                                                $char .
                                                                                '[\.:\s]/i';
                                                                            $content = '';
                                                                            if ($level === 'detail_indikator') {
                                                                                $content = $matriks->detail_indikator;
                                                                            } elseif ($level === 'detail_sub') {
                                                                                $content = $matriks->detail_sub;
                                                                            }

                                                                            $isY =
                                                                                !empty($content) &&
                                                                                preg_match(
                                                                                    $xy_regex('y'),
                                                                                    strip_tags($content),
                                                                                );
                                                                            $isX =
                                                                                !empty($content) &&
                                                                                preg_match(
                                                                                    $xy_regex('x'),
                                                                                    strip_tags($content),
                                                                                );

                                                                            $targetPk = $targetPkData->get(
                                                                                $matriks->id,
                                                                            );
                                                                            $realisasiData =
                                                                                $existingRealisasi[$matriks->id] ??
                                                                                null;

                                                                            $realisasiValue = $realisasiData->realisasi ?? '';

                                                                            // Check if this should be calculated automatically 
                                                                            $shouldCalculateAutomatically = false;
                                                                            if ($level === 'indikator') {
                                                                                // Check if this indicator has X and Y detail indicators
                                                                                $hasXDetail = $items->contains(function($item) use ($xy_regex) {
                                                                                    return !empty($item->detail_indikator) && preg_match($xy_regex('x'), strip_tags($item->detail_indikator));
                                                                                });
                                                                                $hasYDetail = $items->contains(function($item) use ($xy_regex) {
                                                                                    return !empty($item->detail_indikator) && preg_match($xy_regex('y'), strip_tags($item->detail_indikator));
                                                                                });
                                                                                $shouldCalculateAutomatically = $hasXDetail && $hasYDetail;
                                                                            } elseif ($level === 'sub_indikator') {
                                                                                // Check if this sub-indicator has X and Y detail subs
                                                                                $hasXDetailSub = $items->contains(function($item) use ($xy_regex, $matriks) {
                                                                                    return $item->sub_indikator === $matriks->sub_indikator && !empty($item->detail_sub) && preg_match($xy_regex('x'), strip_tags($item->detail_sub));
                                                                                });
                                                                                $hasYDetailSub = $items->contains(function($item) use ($xy_regex, $matriks) {
                                                                                    return $item->sub_indikator === $matriks->sub_indikator && !empty($item->detail_sub) && preg_match($xy_regex('y'), strip_tags($item->detail_sub));
                                                                                });
                                                                                $shouldCalculateAutomatically = $hasXDetailSub && $hasYDetailSub;
                                                                            }

                                                                            $isRowReadOnly = $readOnly || $shouldCalculateAutomatically;

                                                                            $targetFra = $matriks->target_fra
                                                                                ? $matriks->target_fra
                                                                                    ->{'target_tw' . $triwulan}
                                                                                : null;
                                                                        @endphp
                                                                        <tr class="border-b border-gray-100 hover:bg-blue-50 transition-colors duration-200"
                                                                            data-row-id="{{ $matriks->id }}"
                                                                            data-group-id="{{ $mainIndicator->id }}"
                                                                            data-parent-sub-indicator="{{ $matriks->sub_indikator ?? '' }}"
                                                                            data-level="{{ $level }}"
                                                                            data-content="{{ $content ?: ($matriks->sub_indikator ?: $matriks->indikator) }}"
                                                                            data-target-fra="{{ $targetFra ?? 0 }}"
                                                                            data-target-pk="{{ $targetPk->target_pk ?? 0 }}">

                                                                            <td class="py-2 px-3 align-middle">
                                                                                @if ($level === 'indikator')
                                                                                    <div class="flex items-start">
                                                                                        <div
                                                                                            class="w-2 h-2 bg-red-500 rounded-full mt-1.5 mr-3 flex-shrink-0">
                                                                                        </div>
                                                                                        <span
                                                                                            class="font-bold text-gray-800 leading-normal">{{ $matriks->indikator }}</span>
                                                                                    </div>
                                                                                @elseif ($level === 'detail_indikator')
                                                                                    <div class="flex items-start ml-4">
                                                                                        <div
                                                                                            class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 mr-3 flex-shrink-0">
                                                                                        </div>
                                                                                        <span
                                                                                            class="text-gray-700 leading-normal">{{ $matriks->detail_indikator }}</span>
                                                                                    </div>
                                                                                @elseif ($level === 'sub_indikator')
                                                                                    <div class="flex items-start ml-10">
                                                                                        <div
                                                                                            class="w-2 h-2 bg-orange-400 rounded-full mt-1.5 mr-3 flex-shrink-0">
                                                                                        </div>
                                                                                        <span
                                                                                            class="font-bold text-gray-800 leading-normal">{{ $matriks->sub_indikator }}</span>
                                                                                    </div>
                                                                                @else
                                                                                    {{-- detail_sub --}}
                                                                                    <div class="flex items-start ml-14">
                                                                                        <div
                                                                                            class="w-2 h-2 bg-green-400 rounded-full mt-1.5 mr-3 flex-shrink-0">
                                                                                        </div>
                                                                                        <span
                                                                                            class="text-gray-600 leading-normal">{{ $matriks->detail_sub }}</span>
                                                                                    </div>
                                                                                @endif
                                                                            </td>

                                                                            <td class="py-2 px-2 text-center align-middle">
                                                                                <span
                                                                    class="inline-block bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">{{ $matriks->satuan }}</span>
                                                                            </td>

                                                                            <td class="py-2 px-2 text-center align-middle">
                                                                                @php
                                                                                    $targetDisplay = '-';
                                                                                    if ($targetFra !== null) {
                                                                                        $targetDisplay =
                                                                                            floor($targetFra) ==
                                                                                            $targetFra
                                                                                                ? number_format(
                                                                                                    $targetFra,
                                                                                                    0,
                                                                                                    ',',
                                                                                                    '.',
                                                                                                )
                                                                                                : number_format(
                                                                                                    $targetFra,
                                                                                                    2,
                                                                                                    '.',
                                                                                                    ',',
                                                                                                );
                                                                                    }
                                                                                @endphp
                                                                                <span
                                                                    class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">{{ $targetDisplay }}</span>
                                                                            </td>

                                                                            <td class="py-2 px-2 align-middle">
                                                                                @php
                                                                                    $realisasiDisplay = $realisasiValue;
                                                                                    if (is_numeric($realisasiValue)) {
                                                                                        $realisasiDisplay =
                                                                                            floor($realisasiValue) ==
                                                                                            $realisasiValue
                                                                                                ? number_format(
                                                                                                    $realisasiValue,
                                                                                                    0,
                                                                                                    '.',
                                                                                                    '',
                                                                                                )
                                                                                                : number_format(
                                                                                                    $realisasiValue,
                                                                                                    2,
                                                                                                    '.',
                                                                                                    ',',
                                                                                                );
                                                                                    }
                                                                                @endphp
                                                                                <input type="number"
                                                                                    name="realisasi[{{ $matriks->id }}]"
                                                                                    step="any" min="0"
                                                                                    class="realisasi-input w-full h-9 border-2 border-gray-200 rounded-lg px-3 py-1 text-center text-sm align-middle focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 {{ $isRowReadOnly ? 'bg-gray-100' : 'bg-white' }}"
                                                                                    value="{{ $realisasiDisplay }}"
                                                                                    {{ $isRowReadOnly ? 'readonly' : '' }}
                                                                                    @if (!$isParent) placeholder="0" @endif
                                                                                    @if($isX) data-child-x-for="{{ $mainIndicator->id }}" @endif
                                                                                    @if($isY) data-child-y-for="{{ $mainIndicator->id }}" @endif>
                                                                            </td>

                                                                            <td class="py-2 px-2 align-middle text-center">
                                                                                @if ($level === 'indikator' || $level === 'sub_indikator')
                                                                                    <input type="number"
                                                                                        name="capkin_kumulatif[{{ $matriks->id }}]"
                                                                                        step="any"
                                                                                        class="capkin-kumulatif-output w-full border-gray-200 rounded-lg px-3 py-2 text-center text-sm bg-gray-100 font-medium text-gray-700"
                                                                                        value="{{ format_number($realisasiData->capkin_kumulatif ?? 0) }}"
                                                                                        readonly>
                                                                                @else
                                                                                    <span
                                                                                        class="text-gray-400 align-middle">-</span>
                                                                                @endif
                                                                            </td>

                                                                            <td class="py-2 px-2 align-middle text-center">
                                                                                @if ($level === 'indikator' || $level === 'sub_indikator')
                                                                                    <input type="number"
                                                                                        name="capkin_setahun[{{ $matriks->id }}]"
                                                                                        step="any"
                                                                                        class="capkin-setahun-output w-full border-gray-200 rounded-lg px-3 py-2 text-center text-sm bg-gray-100 font-medium text-gray-700"
                                                                                        value="{{ format_number(is_numeric($realisasiData->capkin_setahun ?? null) ? $realisasiData->capkin_setahun : 0) }}"
                                                                                        readonly>
                                                                                @else
                                                                                    <span
                                                                                        class="text-gray-400 align-middle">-</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach

                                                                    @if ($mainIndicator)
                                                                        <tr
                                                                            class="bg-gradient-to-r from-gray-50 to-blue-50">
                                                                            <td class="py-6 px-4" colspan="6">
                                                                                @php
                                                                                    $realisasiForDetail =
                                                                                        $existingRealisasi[
                                                                                            $mainIndicator->id
                                                                                        ] ?? null;
                                                                                @endphp
                                                                                <div
                                                                                    class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                                                                                    <h4
                                                                                        class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            class="h-5 w-5 mr-2 text-blue-600"
                                                                                            fill="none"
                                                                                            viewBox="0 0 24 24"
                                                                                            stroke="currentColor">
                                                                                            <path stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                stroke-width="2"
                                                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                                        </svg>
                                                                                        Detail Realisasi
                                                                                    </h4>
                                                                                    <div
                                                                                        class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                                                                        <div class="space-y-4">
                                                                                            <div>
                                                                                                <label
                                                                                                    class="block text-sm font-semibold text-gray-700 mb-2">Kendala</label>
                                                                                                <textarea name="kendala[{{ $mainIndicator->id }}]" rows="4"
                                                                                                    class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 {{ $readOnly ? 'bg-gray-100' : 'bg-white' }}"
                                                                                                    placeholder="Jelaskan kendala..." {{ $readOnly ? 'readonly' : '' }}>{{ $realisasiForDetail->kendala ?? '' }}</textarea>
                                                                                            </div>
                                                                                            <div>
                                                                                                <label
                                                                                                    class="block text-sm font-semibold text-gray-700 mb-2">Solusi</label>
                                                                                                <textarea name="solusi[{{ $mainIndicator->id }}]" rows="4"
                                                                                                    class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 {{ $readOnly ? 'bg-gray-100' : 'bg-white' }}"
                                                                                                    placeholder="Jelaskan solusi..." {{ $readOnly ? 'readonly' : '' }}>{{ $realisasiForDetail->solusi ?? '' }}</textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="space-y-4">
                                                                                            <div>
                                                                                                <label
                                                                                                    class="block text-sm font-semibold text-gray-700 mb-2">Tindak
                                                                                                    Lanjut</label>
                                                                                                <textarea name="tindak_lanjut[{{ $mainIndicator->id }}]" rows="4"
                                                                                                    class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 {{ $readOnly ? 'bg-gray-100' : 'bg-white' }}"
                                                                                                    placeholder="Jelaskan tindak lanjut..." {{ $readOnly ? 'readonly' : '' }}>{{ $realisasiForDetail->tindak_lanjut ?? '' }}</textarea>
                                                                                            </div>
                                                                                            <div
                                                                                                class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                                                <div>
                                                                                                    <label
                                                                                                        class="block text-sm font-semibold text-gray-700 mb-2">PIC
                                                                                                        Tindak
                                                                                                        Lanjut</label>
                                                                                                    <select
                                                                                                        name="pic_tindak_lanjut_id[{{ $mainIndicator->id }}]"
                                                                                                        class="w-full border-2 border-gray-200 rounded-lg px-2 py-2 text-sm {{ $readOnly ? 'bg-gray-100' : 'bg-white' }}"
                                                                                                        {{ $readOnly ? 'disabled' : '' }}>
                                                                                                        <option
                                                                                                            value="">
                                                                                                            Pilih PIC
                                                                                                        </option>
                                                                                                        @foreach ($penggunas as $pengguna)
                                                                                                            <option
                                                                                                                value="{{ $pengguna->id }}"
                                                                                                                {{ ($realisasiForDetail->pic_tindak_lanjut_id ?? '') == $pengguna->id ? 'selected' : '' }}>
                                                                                                                {{ $pengguna->name }}
                                                                                                                ({{ $pengguna->bidang }})
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div>
                                                                                                    <label
                                                                                                        class="block text-sm font-semibold text-gray-700 mb-2">Batas
                                                                                                        Waktu</label>
                                                                                                    <input type="date"
                                                                                                        name="batas_waktu_tindak_lanjut[{{ $mainIndicator->id }}]"
                                                                                                        class="w-full border-2 border-gray-200 rounded-lg px-2 py-2 text-sm {{ $readOnly ? 'bg-gray-100' : 'bg-white' }}"
                                                                                                        value="{{ $realisasiForDetail && $realisasiForDetail->batas_waktu_tindak_lanjut ? $realisasiForDetail->batas_waktu_tindak_lanjut->format('Y-m-d') : '' }}"
                                                                                                        {{ $readOnly ? 'readonly' : '' }}>
                                                                                                </div>
                                                                                            </div>
                                                                                            @if (!$readOnly)
                                                                                                <div>
                                                                                                    <label
                                                                                                        class="block text-sm font-semibold text-gray-700 mb-2">Bukti
                                                                                                        Dukung (PDF)</label>
                                                                                                    <div class="relative">
                                                                                                        <input
                                                                                                            type="file"
                                                                                                            id="pdfUpload_{{ $mainIndicator->id }}"
                                                                                                            name="bukti_dukung[{{ $mainIndicator->id }}][]"
                                                                                                            hidden
                                                                                                            accept="application/pdf"
                                                                                                            multiple
                                                                                                            onchange="updateFileDisplay('{{ $mainIndicator->id }}')">
                                                                                                        <div
                                                                                                            class="flex items-center gap-3">
                                                                                                            <input
                                                                                                                type="text"
                                                                                                                readonly
                                                                                                                placeholder="Pilih file PDF"
                                                                                                                id="fileNamesDisplay_{{ $mainIndicator->id }}"
                                                                                                                class="flex-1 px-3 py-2 border-2 border-gray-200 rounded-lg bg-gray-50 cursor-pointer text-sm"
                                                                                                                onclick="document.getElementById('pdfUpload_{{ $mainIndicator->id }}').click()">
                                                                                                            <button
                                                                                                                type="button"
                                                                                                                class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 text-sm"
                                                                                                                onclick="handleUploadClick({{ $mainIndicator->id }}, {{ $realisasiForDetail ? $realisasiForDetail->id : 'null' }})">Upload</button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endif
                                                                                            <div id="filePreview_{{ $mainIndicator->id }}"
                                                                                                class="space-y-2 mt-2">
                                                                                                {{-- Bukti dukung lama sudah dihapus --}}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
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
                                        <p class="text-gray-500 text-lg">Tidak ada target yang diassign untuk anda saat ini.
                                        </p>
                                    </div>
                                @endforelse
                            </div>
                        @endif

                        <!-- Umum Tab Content -->
                        @if ($hasUmumData)
                            <div id="content-umum" class="space-y-6 hidden">
                                @php
                                    $matriksUmum = $matriksList
                                        ->filter(fn($m) => $m->template_fra->template_jenis->nama === 'Umum')
                                        ->sortBy(['indikator', 'detail_indikator', 'sub_indikator', 'detail_sub']);
                                @endphp
                                @if ($matriksUmum->isNotEmpty())
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                                        <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                                            <h3 class="font-semibold text-gray-800 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-600"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                Indikator Umum
                                            </h3>
                                        </div>

                                        <div class="overflow-x-auto">
                                            <table class="w-full bg-white table-fixed">
                                                <thead>
                                                    <tr
                                                        class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                                        <th class="text-left py-3 px-4 font-semibold text-gray-700"
                                                            style="width: 45%;">Indikator</th>
                                                        <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                            style="width: 7%;">Satuan</th>
                                                        <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                            style="width: 8%;">Target</th>
                                                        <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                            style="width: 10%;">Realisasi</th>
                                                        <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                            style="width: 15%;">Capkin Kumulatif</th>
                                                        <th class="text-center py-3 px-2 font-semibold text-gray-700"
                                                            style="width: 15%;">Capkin Setahun</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $dataByIndikator = $matriksUmum->groupBy('indikator');
                                                    @endphp
                                                    @foreach ($dataByIndikator as $indikatorName => $items)
                                                        @php
                                                            $mainIndicator = $items->first(
                                                                fn($item) => empty($item->detail_indikator) &&
                                                                    empty($item->sub_indikator) &&
                                                                    empty($item->detail_sub),
                                                            );

                                                            $virtualRows = [];
                                                            $processedDetails = [];

                                                            if ($mainIndicator) {
                                                                $virtualRows[] = [
                                                                    'matriks' => $mainIndicator,
                                                                    'level' => 'indikator',
                                                                ];
                                                            }
                                                            foreach (
                                                                $items->whereNotNull('detail_indikator')
                                                                as $detail
                                                            ) {
                                                                if (
                                                                    !in_array(
                                                                        $detail->detail_indikator,
                                                                        $processedDetails,
                                                                    )
                                                                ) {
                                                                    $virtualRows[] = [
                                                                        'matriks' => $detail,
                                                                        'level' => 'detail_indikator',
                                                                    ];
                                                                    $processedDetails[] = $detail->detail_indikator;
                                                                }
                                                            }
                                                            $subIndicatorGroups = $items
                                                                ->whereNotNull('sub_indikator')
                                                                ->groupBy('sub_indikator');
                                                            foreach ($subIndicatorGroups as $subName => $subItems) {
                                                                $firstSubItem = $subItems->first();
                                                                if (!in_array($subName, $processedDetails)) {
                                                                    $virtualRows[] = [
                                                                        'matriks' => $firstSubItem,
                                                                        'level' => 'sub_indikator',
                                                                    ];
                                                                    $processedDetails[] = $subName;
                                                                }
                                                                foreach (
                                                                    $subItems->whereNotNull('detail_sub')
                                                                    as $detailSub
                                                                ) {
                                                                    if (
                                                                        !in_array(
                                                                            $detailSub->detail_sub,
                                                                            $processedDetails,
                                                                        )
                                                                    ) {
                                                                        $virtualRows[] = [
                                                                            'matriks' => $detailSub,
                                                                            'level' => 'detail_sub',
                                                                        ];
                                                                        $processedDetails[] = $detailSub->detail_sub;
                                                                    }
                                                                }
                                                            }
                                                        @endphp

                                                        @foreach ($virtualRows as $vRow)
                                                            @php
                                                                $matriks = $vRow['matriks'];
                                                                $level = $vRow['level'];
                                                            @endphp
                                                            {{-- Removed realisasi-row-logic component as it's no longer needed --}}
                                                        @endforeach

                                                        @if ($mainIndicator)
                                                            @php
                                                                $realisasiForDetail =
                                                                    $existingRealisasi[$mainIndicator->id] ?? null;
                                                            @endphp
                                                            <tr class="bg-gradient-to-r from-gray-50 to-blue-50">
                                                                <td class="py-6 px-4" colspan="6">
                                                                    @include(
                                                                        'components.realisasi-detail-form-logic',
                                                                        [
                                                                            'matriks' => $mainIndicator,
                                                                            'realisasi' => $realisasiForDetail,
                                                                            'readOnly' => $readOnly,
                                                                        ]
                                                                    )
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <p class="text-gray-500 text-lg">Tidak ada target yang diassign untuk anda saat ini.</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Spacing for sticky buttons -->
                    <div class="pb-24"></div>

                    @if (!$readOnly)
                        <div
                            class="fixed bottom-0 left-0 right-0 md:left-72 md:ml-2.5 bg-white/80 backdrop-blur-sm border-t border-gray-200 px-6 py-4 shadow-lg z-40">
                            <div class="flex justify-between items-center mx-auto">
                                <a href="{{ route('fra.index') }}"
                                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    Kembali
                                </a>
                                <div class="flex items-center space-x-3">
                                    <button type="button" id="saveBtn"
                                        class="px-6 py-3 border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition-all duration-200 font-medium flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                        </svg>
                                        Simpan Perubahan
                                    </button>
                                    <button type="button" id="finalizeBtn"
                                        class="px-8 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Finalisasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Read-only mode sticky button -->
                        <div
                            class="fixed bottom-0 left-0 right-0 md:left-56 bg-white/80 backdrop-blur-sm border-t border-gray-200 px-6 py-4 shadow-lg z-40">
                            <div class="flex justify-start items-start mx-auto">
                                <a href="{{ route('fra.index') }}"
                                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    @include('components.loading')

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form submission logic
            const realisasiForm = document.getElementById('realisasiForm');
            const actionType = document.getElementById('actionType');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const saveBtn = document.getElementById('saveBtn');
            const finalizeBtn = document.getElementById('finalizeBtn');

            if (saveBtn) {
                saveBtn.addEventListener('click', function() {
                    setActionTypeAndSubmit('save');
                });
            }

            if (finalizeBtn) {
                finalizeBtn.addEventListener('click', function() {
                    setActionTypeAndSubmit('finalize');
                });
            }

            // Auto-save functionality (save to localStorage)
            function saveToLocalStorage() {
                const formData = new FormData(realisasiForm);
                const data = {};
                
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                
                localStorage.setItem(`fra_realisasi_${{{ $fra->id ?? 0 }}}`, JSON.stringify(data));
            }

            // Load from localStorage
            function loadFromLocalStorage() {
                const savedData = localStorage.getItem(`fra_realisasi_${{{ $fra->id ?? 0 }}}`);
                if (savedData) {
                    try {
                        const data = JSON.parse(savedData);
                        
                        Object.keys(data).forEach(key => {
                            const input = realisasiForm.querySelector(`[name="${key}"]`);
                            // Only update non-readonly fields from localStorage
                            if (input && !input.readOnly) {
                                input.value = data[key];
                            }
                        });
                        
                        // Recalculate all parent groups after loading data
        
                        document.querySelectorAll('tr[data-group-id]').forEach(row => {
                            const level = row.dataset.level;
                            if (level === 'indikator' || level === 'sub_indikator') {
                                const hasCalculatedInputs = row.querySelectorAll('.realisasi-input[readonly]').length > 0;
                                if (hasCalculatedInputs) {
                                    calculateAndSetRealisasi(row);
                                }
                            }
                        });

                        // Update capkin calculations
                        document.querySelectorAll('.realisasi-input').forEach(input => {
                            const row = input.closest('tr');
                            updateCapkinCalculations(row);
                        });

                    } catch (e) {
                        // Silent error handling for localStorage loading
                    }
                }
            }
            
            // Initial calculation setup on page load (similar to target FRA)
            function runInitialRealisasiSetup() {

                
                // Run initial calculation for all calculated groups

                
                // Find all calculated parent rows and trigger calculation
                document.querySelectorAll('tr[data-group-id]').forEach(row => {
                    const level = row.dataset.level;
                    if (level === 'indikator' || level === 'sub_indikator') {
                        const hasCalculatedInputs = row.querySelectorAll('.realisasi-input[readonly]').length > 0;
                        if (hasCalculatedInputs) {
                            calculateAndSetRealisasi(row);
                        }
                    }
                });
                
                // Update all capkin calculations
                document.querySelectorAll('.realisasi-input').forEach(input => {
                    const row = input.closest('tr');
                    updateCapkinCalculations(row);
                });
            }

            // Progress calculation function (similar to target FRA)
            function updateProgress() {
                const inputs = realisasiForm.querySelectorAll('input[type="number"]:not([readonly]), textarea:not([readonly]), select:not([disabled])');
                const totalInputs = inputs.length;
                let filledInputs = 0;

                inputs.forEach(input => {
                    if (input.value && input.value.trim() !== '') {
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

            // Save to localStorage on input change and update progress
            realisasiForm.addEventListener('input', function() {
                saveToLocalStorage();
                updateProgress();
            });

            // Update progress on change events for selects
            realisasiForm.addEventListener('change', function() {
                updateProgress();
            });

            // Run initial setup first, then load saved data on page load
            runInitialRealisasiSetup();
            loadFromLocalStorage();
            
            // Re-run calculations after loading from localStorage to ensure persistence
            setTimeout(() => {
                runInitialRealisasiSetup();
                updateProgress(); // Update progress after initial setup
            }, 200);

            // Function to handle form submission with validation
            function setActionTypeAndSubmit(action) {

                
                if (action === 'finalize') {
                    // Validate required fields for finalization
                    const validationResult = validateFormForFinalization();
                    
                    if (!validationResult.isValid) {
                        showModal('warning', 'Finalisasi Tidak Dapat Dilakukan',
                            `Mohon lengkapi semua field yang diperlukan sebelum finalisasi.<br>
                            <strong>Field yang belum diisi:</strong><br>
                            ${validationResult.missingFields.join('<br>')}<br><br>
                            <em>Catatan: Untuk menyimpan perubahan tanpa validasi, gunakan tombol "Simpan Perubahan".</em>`);
                        return;
                    }
                    
                    // Show confirmation dialog for finalization
                    showModal('question', 'Konfirmasi Finalisasi',
                        'Apakah Anda yakin ingin memfinalisasi realisasi FRA?<br>Data yang sudah difinalisasi tidak dapat diubah lagi.', {
                        confirmText: 'Ya, Finalisasi',
                        cancelText: 'Batal',
                        showCancel: true,
                        confirmCallback: function() {
                            submitForm(action);
                        }
                    });
                } else {
                    // For save action, submit directly without validation
                    submitForm(action);
                }
            }
            
            // Function to validate form for finalization
            function validateFormForFinalization() {
                const missingFields = [];
                let isValid = true;
                
                // Check realisasi inputs that are required
                const realisasiInputs = document.querySelectorAll('.realisasi-input:not([readonly])');
                realisasiInputs.forEach(input => {
                    const value = input.value.trim();
                    if (!value || value === '' || value === '0') {
                        const row = input.closest('tr');
                        const label = row.querySelector('td:first-child')?.textContent?.trim() || 'Field tidak diketahui';
                        missingFields.push(`• ${label} (Realisasi)`);
                        isValid = false;
                        
                        // Add visual indicator
                        input.classList.add('border-red-500', 'bg-red-50');
                        setTimeout(() => {
                            input.classList.remove('border-red-500', 'bg-red-50');
                        }, 3000);
                    }
                });
                
                // Check kendala, solusi, tindak_lanjut fields
                const textareaFields = [
                    { selector: 'textarea[name*="[kendala]"]', label: 'Kendala' },
                    { selector: 'textarea[name*="[solusi]"]', label: 'Solusi' },
                    { selector: 'textarea[name*="[tindak_lanjut]"]', label: 'Tindak Lanjut' }
                ];
                
                textareaFields.forEach(field => {
                    const textareas = document.querySelectorAll(field.selector);
                    textareas.forEach(textarea => {
                        const value = textarea.value.trim();
                        if (!value || value === '') {
                            const row = textarea.closest('tr');
                            const indicatorLabel = row.querySelector('td:first-child')?.textContent?.trim() || 'Indikator tidak diketahui';
                            missingFields.push(`• ${indicatorLabel} (${field.label})`);
                            isValid = false;
                            
                            // Add visual indicator
                            textarea.classList.add('border-red-500', 'bg-red-50');
                            setTimeout(() => {
                                textarea.classList.remove('border-red-500', 'bg-red-50');
                            }, 3000);
                        }
                    });
                });
                
                return {
                    isValid: isValid,
                    missingFields: missingFields
                };
            }
            
            // Function to submit form with AJAX
            function submitForm(action) {
                actionType.value = action;
                
                if (action === 'finalize') {
                    showGlobalLoading('Memfinalisasi data...');
                } else {
                    showGlobalLoading('Menyimpan data...');
                }
                
                const formData = new FormData(realisasiForm);
                formData.delete('_token');
                
                // Get CSRF token
                let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                if (!csrfToken) {
                    showModal('error', 'Kesalahan Keamanan', 
                        'Token keamanan tidak ditemukan. Silakan refresh halaman dan coba lagi.', {
                        confirmText: 'Refresh Halaman',
                        showCancel: false,
                        confirmCallback: () => window.location.reload()
                    });
                    hideGlobalLoading();
                    return;
                }
                
                fetch(realisasiForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 419) {
                            throw new Error('CSRF token mismatch. Silakan refresh halaman dan coba lagi.');
                        }
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    return response.json();
                })
                .then(data => {
                    hideGlobalLoading();
                    
                    if (data.success) {
                        if (action === 'save') {
                            showNotification('success', data.message || 'Data berhasil disimpan!');
                            // Clear localStorage after successful save
                            localStorage.removeItem(`fra_realisasi_${{{ $fra->id ?? 0 }}}`);
                        } else if (action === 'finalize') {
                            showModal('success', 'Finalisasi Berhasil', 
                                data.message || 'Data realisasi FRA berhasil difinalisasi!', {
                                confirmText: 'Kembali ke Daftar',
                                showCancel: false,
                                confirmCallback: () => {
                                    window.location.href = data.redirect_url || '{{ route("fra.index") }}';
                                }
                            });
                            // Clear localStorage after successful finalization
                            localStorage.removeItem(`fra_realisasi_${{{ $fra->id ?? 0 }}}`);
                        }
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            let errorMessage = 'Terdapat kesalahan pada form:<br><br>';
                            Object.keys(data.errors).forEach(field => {
                                const fieldErrors = data.errors[field];
                                errorMessage += `<strong>${field}:</strong><br>`;
                                fieldErrors.forEach(error => {
                                    errorMessage += `• ${error}<br>`;
                                });
                                errorMessage += '<br>';
                                
                                // Mark field with error
                                const input = realisasiForm.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('border-red-500', 'bg-red-50');
                                    setTimeout(() => {
                                        input.classList.remove('border-red-500', 'bg-red-50');
                                    }, 5000);
                                }
                            });
                            
                            showModal('error', 'Validasi Gagal', errorMessage, {
                                confirmText: 'Perbaiki Form',
                                showCancel: false
                            });
                        } else {
                            showModal('error', 'Gagal Menyimpan', 
                                data.message || 'Terjadi kesalahan saat menyimpan data.', {
                                confirmText: 'Coba Lagi',
                                showCancel: false
                            });
                        }
                    }
                })
                .catch(error => {
                    hideGlobalLoading();
                    
                    showModal('error', 'Kesalahan Sistem', 
                        `Terjadi kesalahan tidak terduga. Silakan coba lagi atau hubungi administrator.<br><br>Detail: ${error.message}`, {
                        confirmText: 'Tutup',
                        showCancel: false
                    });
                });
            }

            if (realisasiForm) {
                realisasiForm.addEventListener('submit', function() {
                    if (loadingOverlay) {
                        loadingOverlay.style.display = 'flex';
                    }
                });
            }

            // Tab switching logic
            const tabButtons = document.querySelectorAll('button[id^="tab-"]');
            const tabContents = document.querySelectorAll('div[id^="content-"]');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.id.replace('tab-', '');
                    
                    // Update button states
                    tabButtons.forEach(btn => {
                        btn.classList.remove('bg-gradient-to-r', 'from-red-600', 'to-red-700', 'text-white', 'border-red-600');
                        btn.classList.add('text-gray-600', 'bg-gray-100', 'hover:bg-gray-200');
                    });
                    
                    this.classList.add('bg-gradient-to-r', 'from-red-600', 'to-red-700', 'text-white', 'border-red-600');
                    this.classList.remove('text-gray-600', 'bg-gray-100', 'hover:bg-gray-200');
                    
                    // Update content visibility
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    const targetContent = document.getElementById('content-' + tabId);
                    if (targetContent) {
                        targetContent.classList.remove('hidden');
                    }
                });
            });

            // Enhanced input event handler for realisasi calculation
            realisasiForm.addEventListener('input', function(event) {
                const input = event.target;
                
                if (input.classList.contains('realisasi-input')) {
                    const row = input.closest('tr');
                    const groupId = row.dataset.groupId;
                    const level = row.dataset.level;
                    
                    // Check if this is a child X or Y input
                    const isChildX = input.hasAttribute('data-child-x-for');
                    const isChildY = input.hasAttribute('data-child-y-for');
                    
                    if (isChildX || isChildY) {
    
                        calculateParentRealisasi(row);
                    }
                    
                    // Update capkin calculations for all levels
                    updateCapkinCalculations(row);
                    
                    // Also update capkin for parent levels after child calculation
                    if (isChildX || isChildY) {
                        // Find and update parent indicators
                        const parentSubIndicator = row.dataset.parentSubIndicator;
                        if (parentSubIndicator) {
                            const subRow = document.querySelector(`tr[data-group-id="${groupId}"][data-level="sub_indikator"][data-content="${parentSubIndicator}"]`);
                            if (subRow) {
                                setTimeout(() => updateCapkinCalculations(subRow), 100);
                            }
                        }
                        
                        const mainRow = document.querySelector(`tr[data-group-id="${groupId}"][data-level="indikator"]`);
                        if (mainRow) {
                            setTimeout(() => updateCapkinCalculations(mainRow), 100);
                        }
                    }
                }
            });

            // Calculate parent realisasi based on X/Y*100 formula
            function calculateParentRealisasi(childRow) {
                const groupId = childRow.dataset.groupId;
                
                // Find parent rows to calculate
                const mainRow = document.querySelector(`tr[data-group-id="${groupId}"][data-level="indikator"]`);
                const parentSubIndicator = childRow.dataset.parentSubIndicator;
                
                if (parentSubIndicator) {
                    const subRow = document.querySelector(`tr[data-group-id="${groupId}"][data-level="sub_indikator"][data-content="${parentSubIndicator}"]`);
                    if (subRow) {
                        calculateAndSetRealisasi(subRow);
                    }
                }
                
                if (mainRow) {
                    calculateAndSetRealisasi(mainRow);
                }
            }

                        // Calculate and set realisasi value using X/Y*100 formula
            function calculateAndSetRealisasi(parentRow) {
                const groupId = parentRow.dataset.groupId;
                const level = parentRow.dataset.level;
                

                
                let xInputs, yInputs;
                
                if (level === 'indikator') {
                    xInputs = document.querySelectorAll(`tr[data-group-id="${groupId}"][data-level="detail_indikator"] input[data-child-x-for]`);
                    yInputs = document.querySelectorAll(`tr[data-group-id="${groupId}"][data-level="detail_indikator"] input[data-child-y-for]`);

                } else if (level === 'sub_indikator') {
                    const subIndicatorContent = parentRow.dataset.content;
                    xInputs = document.querySelectorAll(`tr[data-group-id="${groupId}"][data-level="detail_sub"][data-parent-sub-indicator="${subIndicatorContent}"] input[data-child-x-for]`);
                    yInputs = document.querySelectorAll(`tr[data-group-id="${groupId}"][data-level="detail_sub"][data-parent-sub-indicator="${subIndicatorContent}"] input[data-child-y-for]`);

                }
                
                if (xInputs && yInputs && xInputs.length > 0 && yInputs.length > 0) {
                    const parentInput = parentRow.querySelector('.realisasi-input[readonly]');
                    if (!parentInput) return;
                    
                    const xValue = parseFloat(xInputs[0].value) || 0;
                    const yValue = parseFloat(yInputs[0].value) || 0;
                    
                    let result = 0;
                    if (yValue > 0) {
                        result = (xValue / yValue) * 100;
                    }
                    
                    // Format the result
                    let formattedValue = result === 0 ? '0' : (result % 1 === 0 ? result.toString() : result.toFixed(2));
                    
                    const previousValue = parentInput.value;
                    if (parentInput.value !== formattedValue) {
                        parentInput.value = formattedValue;
    
                        
                        // Add visual feedback
                        parentInput.classList.add('recently-calculated');
                        setTimeout(() => {
                            parentInput.classList.remove('recently-calculated');
                        }, 2000);
                        
                        // Update capkin calculations after parent value changes
                        updateCapkinCalculations(parentRow);
                    }
                }
            }


            
            // Update capkin calculations for a row
            function updateCapkinCalculations(row) {
                const level = row.dataset.level;
                const rowId = row.dataset.rowId;
                
                // Only calculate capkin for parent levels (indikator and sub_indikator)
                if (level === 'indikator' || level === 'sub_indikator') {
                    const realisasiInput = row.querySelector('.realisasi-input');
                    const capkinKumulatifOutput = row.querySelector('.capkin-kumulatif-output');
                    const capkinSetahunOutput = row.querySelector('.capkin-setahun-output');
                    
                    if (realisasiInput && capkinKumulatifOutput && capkinSetahunOutput) {
                        const realisasiValue = parseFloat(realisasiInput.value) || 0;
                        const targetFra = parseFloat(row.dataset.targetFra) || 0;
                        const targetPk = parseFloat(row.dataset.targetPk) || 0;
                        
                        // Capkin Kumulatif: (realisasi / target_fra) * 100, max 120
                        let capkinKumulatif = 0;
                        if (targetFra > 0) {
                            capkinKumulatif = Math.min(120, (realisasiValue / targetFra) * 100);
                        }
                        
                        // Capkin Setahun: (realisasi / target_pk) * 100, max 120
                        let capkinSetahun = 0;
                        if (targetPk > 0) {
                            capkinSetahun = Math.min(120, (realisasiValue / targetPk) * 100);
                        }
                        
                        // Format and display values
                        capkinKumulatifOutput.value = formatNumberJs(capkinKumulatif);
                        capkinSetahunOutput.value = formatNumberJs(capkinSetahun);
                    }
                }
            }

            // Helper function to format numbers (mimics PHP's format_number)
            function formatNumberJs(number) {
                if (isNaN(number)) {
                    return ''; // Or '-' or some other indicator
                }
                // Check if it's an integer (no decimal part)
                if (number % 1 === 0) {
                    return number.toString();
                } else {
                    // If it has decimal, format to 2 decimal places
                    return number.toFixed(2);
                }
            }

            // Initialize calculations on page load
            function initializeCalculations() {
                document.querySelectorAll('.realisasi-input').forEach(input => {
                    const row = input.closest('tr');
                    updateCapkinCalculations(row);
                });
            }

            // Run initialization after DOM is ready
            setTimeout(initializeCalculations, 100);
        });

        function updateFileDisplay(indicatorId) {
            const fileInput = document.getElementById('pdfUpload_' + indicatorId);
            const displayInput = document.getElementById('fileNamesDisplay_' + indicatorId);
            const previewContainer = document.getElementById('filePreview_' + indicatorId);
            
            if (fileInput.files.length > 0) {
                if (fileInput.files.length === 1) {
                    displayInput.value = fileInput.files[0].name;
                } else {
                    displayInput.value = fileInput.files.length + ' file dipilih';
                }
                
                // Show preview of selected files
                showFilePreview(indicatorId, fileInput.files);
            } else {
                displayInput.value = 'Pilih file PDF';
                // Clear preview if no files selected
                clearNewFilePreview(indicatorId);
            }
        }
        
        function showFilePreview(indicatorId, files) {
             const previewContainer = document.getElementById('filePreview_' + indicatorId);
             
             // Remove any existing new file previews
             clearNewFilePreview(indicatorId);
             
             // Add preview for each selected file with improved UI
             Array.from(files).forEach((file, index) => {
                 const filePreview = document.createElement('div');
                 filePreview.className = 'flex items-center justify-between bg-yellow-50 border border-yellow-200 px-4 py-3 rounded-lg new-file-preview shadow-sm';
                 filePreview.innerHTML = `
                     <div class="flex items-center flex-1 min-w-0">
                         <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                             <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                             </svg>
                         </div>
                         <div class="flex-1 min-w-0">
                             <p class="text-sm font-medium text-yellow-800 truncate">${file.name}</p>
                             <p class="text-xs text-yellow-600">Belum diupload • ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                         </div>
                     </div>
                     <button type="button" 
                             class="px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 transition-colors flex items-center gap-1 flex-shrink-0" 
                             onclick="removeFileFromPreview(${indicatorId}, ${index})">
                         <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                         </svg>
                         Hapus
                     </button>
                 `;
                 previewContainer.appendChild(filePreview);
             });
         }
        
        function clearNewFilePreview(indicatorId) {
             const previewContainer = document.getElementById('filePreview_' + indicatorId);
             const newPreviews = previewContainer.querySelectorAll('.new-file-preview');
             newPreviews.forEach(preview => preview.remove());
         }
        
        function removeFileFromPreview(indicatorId, fileIndex) {
            const fileInput = document.getElementById('pdfUpload_' + indicatorId);
            const dt = new DataTransfer();
            
            // Add all files except the one to remove
            Array.from(fileInput.files).forEach((file, index) => {
                if (index !== fileIndex) {
                    dt.items.add(file);
                }
            });
            
            // Update the file input
            fileInput.files = dt.files;
            
            // Update display
            updateFileDisplay(indicatorId);
        }

        function hapusBuktiDukung(buktiDukungId, element) {
            showModal('warning', 'Konfirmasi Hapus',
                'Apakah Anda yakin ingin menghapus file ini? File akan dipindahkan ke trash di Google Drive.', {
                confirmText: 'Ya, Hapus',
                cancelText: 'Batal',
                showCancel: true,
                confirmCallback: function() {
                    const url = `{{ route('bukti.dukung.fra.destroy', ':id') }}`.replace(':id', buktiDukungId);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Show loading state with improved UI
                    const originalContent = element.innerHTML;
                    element.disabled = true;
                    element.innerHTML = `
                        <svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Menghapus...
                    `;

                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Hapus elemen dari DOM dengan animasi
                            const fileElement = element.closest('.uploaded-file');
                            if (fileElement) {
                                fileElement.style.transition = 'all 0.3s ease';
                                fileElement.style.opacity = '0';
                                fileElement.style.transform = 'translateX(100%)';
                                
                                setTimeout(() => {
                                    fileElement.remove();
                                }, 300);
                            }
                            
                            // Show success message
                            showNotification('success', data.message || 'File berhasil dihapus dari Google Drive.');
                        } else {
                            // Reset button state
                            element.disabled = false;
                            element.innerHTML = originalContent;
                            showNotification('error', 'Gagal menghapus file: ' + (data.message || 'Terjadi kesalahan'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Reset button state
                        element.disabled = false;
                        element.innerHTML = originalContent;
                        showNotification('error', 'Terjadi kesalahan saat menghapus file. Silakan coba lagi.');
                    });
                }
            });
        }
        
        function handleUploadClick(indicatorId, realisasiFraId) {
            // Check if realisasiFraId is null or invalid
            if (!realisasiFraId || realisasiFraId === 'null' || realisasiFraId === null) {
                showNotification('warning', 'Data realisasi belum tersedia. Silakan simpan form terlebih dahulu sebelum mengupload bukti dukung.');
                return;
            }
            
            const fileInput = document.getElementById('pdfUpload_' + indicatorId);
            
            // If no files selected, trigger file selection
            if (fileInput.files.length === 0) {
                fileInput.click();
                
                // Add event listener for when files are selected
                fileInput.onchange = function() {
                    if (this.files.length > 0) {
                        updateFileDisplay(indicatorId);
                        // Auto upload after file selection
                        setTimeout(() => {
                            uploadBuktiDukung(indicatorId, realisasiFraId);
                        }, 100);
                    }
                };
                return;
            }
            
            // If files are selected, proceed with upload
            uploadBuktiDukung(indicatorId, realisasiFraId);
        }
        
        function uploadBuktiDukung(indicatorId, realisasiFraId) {
            const fileInput = document.getElementById('pdfUpload_' + indicatorId);
            const files = fileInput.files;
            
            if (files.length === 0) {
                showNotification('warning', 'Pilih file terlebih dahulu.');
                return;
            }
            
            const formData = new FormData();
            Array.from(files).forEach(file => {
                formData.append('files[]', file);
            });
            formData.append('realisasi_fra_id', realisasiFraId);
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Show loading state on the main upload button
            const mainUploadButton = document.querySelector(`button[onclick*="handleUploadClick(${indicatorId}"]`);
            if (mainUploadButton) {
                mainUploadButton.disabled = true;
                mainUploadButton.textContent = 'Mengupload...';
            }
            
            fetch('{{ route('bukti.dukung.fra.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(async response => {
                // Get response text first for safer parsing
                const responseText = await response.text();
                
                console.log('Upload response details:', {
                    status: response.status,
                    statusText: response.statusText,
                    contentType: response.headers.get('content-type'),
                    url: response.url,
                    responseLength: responseText.length,
                    responsePreview: responseText.substring(0, 200)
                });

                // Handle CSRF token mismatch
                if (response.status === 419) {
                    throw new Error('CSRF token mismatch. Silakan refresh halaman dan coba lagi.');
                }

                // Check if response is actually JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    console.error('Server returned non-JSON response:', {
                        status: response.status,
                        statusText: response.statusText,
                        contentType: contentType,
                        responseText: responseText.substring(0, 500)
                    });
                    
                    throw new Error('Server mengembalikan response yang tidak valid (bukan JSON). Silakan coba lagi atau refresh halaman.');
                }

                // Safe JSON parsing
                let result;
                try {
                    // Clean response text from potential BOM or whitespace
                    const cleanResponseText = responseText.trim().replace(/^\uFEFF/, '');
                    
                    if (!cleanResponseText) {
                        throw new Error('Response kosong dari server');
                    }
                    
                    // Check if it starts with valid JSON characters
                    if (!cleanResponseText.startsWith('{') && !cleanResponseText.startsWith('[')) {
                        console.error('Response tidak dimulai dengan karakter JSON yang valid:', cleanResponseText.substring(0, 100));
                        throw new Error('Format response tidak valid');
                    }
                    
                    result = JSON.parse(cleanResponseText);
                } catch (parseError) {
                    console.error('JSON parsing error:', {
                        error: parseError.message,
                        responseText: responseText.substring(0, 500),
                        responseLength: responseText.length
                    });
                    
                    throw new Error('Gagal memproses response dari server. Format data tidak valid.');
                }

                // Handle HTTP errors after successful JSON parsing
                if (!response.ok) {
                    throw new Error(result.message || `HTTP ${response.status}: ${response.statusText}`);
                }

                return result;
            })
            .then(data => {
                console.log('Upload response data:', data);
                if (data.success) {
                    // Clear file input and preview
                    fileInput.value = '';
                    updateFileDisplay(indicatorId);
                    
                    // Reload the file list for this indicator
                    loadBuktiDukungFiles(indicatorId, realisasiFraId);
                    
                    // Show success message with proper count
                    const successMessage = data.uploaded_files && data.uploaded_files.length > 0 
                        ? `${data.uploaded_files.length} file berhasil diupload` 
                        : (data.message || 'File berhasil diupload');
                    showNotification('success', successMessage);
                    
                    // Show errors if any files failed
                    if (data.errors && data.errors.length > 0) {
                        setTimeout(() => {
                            showNotification('warning', 'Beberapa file gagal diupload: ' + data.errors.join(', '));
                        }, 2000);
                    }
                } else {
                    console.log('Upload failed with data:', data);
                    showNotification('error', 'Gagal mengupload file: ' + (data.message || 'Terjadi kesalahan'));
                    if (data.errors && data.errors.length > 0) {
                        console.error('Upload errors:', data.errors);
                    }
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                
                // Handle CSRF token mismatch specifically
                if (error.message.includes('CSRF token mismatch')) {
                    showNotification('error', error.message);
                    // Optionally reload the page after a delay
                    setTimeout(() => {
                        if (confirm('Halaman akan di-refresh untuk memperbarui token keamanan. Lanjutkan?')) {
                            window.location.reload();
                        }
                    }, 2000);
                } else {
                    showNotification('error', error.message || 'Terjadi kesalahan jaringan atau server. Silakan coba lagi.');
                }
            })
            .finally(() => {
                // Reset button state
                if (mainUploadButton) {
                    mainUploadButton.disabled = false;
                    mainUploadButton.textContent = 'Upload';
                }
            });
        }
        
        function loadBuktiDukungFiles(indicatorId, realisasiFraId) {
            // Check if realisasiFraId is null or invalid
            if (!realisasiFraId || realisasiFraId === 'null' || realisasiFraId === null) {
                console.log('Skipping loadBuktiDukungFiles: realisasiFraId is null or invalid');
                return;
            }
            
            fetch(`{{ route('bukti.dukung.fra.files', ':realisasiFraId') }}`.replace(':realisasiFraId', realisasiFraId), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                // Get response text first for safer parsing
                const responseText = await response.text();
                
                console.log('Load files response details:', {
                    status: response.status,
                    statusText: response.statusText,
                    contentType: response.headers.get('content-type'),
                    url: response.url,
                    responseLength: responseText.length,
                    responsePreview: responseText.substring(0, 200)
                });

                // Check if response is actually JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    console.error('Server returned non-JSON response for file loading:', {
                        status: response.status,
                        statusText: response.statusText,
                        contentType: contentType,
                        responseText: responseText.substring(0, 500)
                    });
                    
                    throw new Error('Server mengembalikan response yang tidak valid saat memuat file.');
                }

                // Safe JSON parsing
                let result;
                try {
                    // Clean response text from potential BOM or whitespace
                    const cleanResponseText = responseText.trim().replace(/^\uFEFF/, '');
                    
                    if (!cleanResponseText) {
                        throw new Error('Response kosong dari server saat memuat file');
                    }
                    
                    // Check if it starts with valid JSON characters
                    if (!cleanResponseText.startsWith('{') && !cleanResponseText.startsWith('[')) {
                        console.error('Response tidak dimulai dengan karakter JSON yang valid:', cleanResponseText.substring(0, 100));
                        throw new Error('Format response tidak valid saat memuat file');
                    }
                    
                    result = JSON.parse(cleanResponseText);
                } catch (parseError) {
                    console.error('JSON parsing error for file loading:', {
                        error: parseError.message,
                        responseText: responseText.substring(0, 500),
                        responseLength: responseText.length
                    });
                    
                    throw new Error('Gagal memproses response saat memuat file. Format data tidak valid.');
                }

                // Handle HTTP errors after successful JSON parsing
                if (!response.ok) {
                    throw new Error(result.message || `HTTP ${response.status}: ${response.statusText}`);
                }

                return result;
            })
            .then(data => {
                console.log('Load files response:', data);
                if (data.success) {
                    const previewContainer = document.getElementById('filePreview_' + indicatorId);
                    
                    if (!previewContainer) {
                        console.error('Preview container not found for indicator:', indicatorId);
                        return;
                    }
                    
                    // Clear existing uploaded files (not new previews)
                    const existingFiles = previewContainer.querySelectorAll('.uploaded-file');
                    existingFiles.forEach(file => file.remove());
                    
                    // Add uploaded files with improved UI
                    if (data.files && Array.isArray(data.files)) {
                        data.files.forEach(file => {
                            const fileElement = document.createElement('div');
                            fileElement.className = 'flex items-center justify-between bg-slate-50 border border-slate-200 px-4 py-3 rounded-lg uploaded-file shadow-sm';
                            fileElement.innerHTML = `
                                <div class="flex items-center flex-1 min-w-0">
                                    <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">${file.file_name || 'Unknown file'}</p>
                                        <p class="text-xs text-gray-500">Diupload: ${file.created_at || 'Unknown date'}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2 ml-4 flex-shrink-0">
                                    <a href="${file.webViewLink || '#'}" target="_blank" 
                                       class="px-3 py-1.5 bg-slate-600 text-white text-xs font-medium rounded-md hover:bg-slate-700 transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Lihat
                                    </a>
                                    <button type="button" 
                                            class="px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 transition-colors flex items-center gap-1" 
                                            onclick="hapusBuktiDukung(${file.id || 0}, this)">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            `;
                            previewContainer.appendChild(fileElement);
                        });
                    }
                } else {
                    console.error('Failed to load files:', data.message || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('Error loading files:', error);
                // Silent error handling for file loading - don't show notification to user
                // But log the error for debugging
            });
        }
        
        function showNotification(type, message) {
            // Create notification element with improved styling
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                type === 'warning' ? 'bg-yellow-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            
            // Add icon and message
            const iconSvg = type === 'success' ? 
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                type === 'error' ? 
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' :
                type === 'warning' ? 
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>' :
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">${iconSvg}</div>
                    <div class="flex-1 text-sm font-medium">${message}</div>
                    <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 ml-2 hover:opacity-75">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }
            }, 5000);
        }
        
        // Modal functions for validation with callback support
        function showModal(type, title, message, options = {}) {
            // Remove existing modal if any
            const existingModal = document.getElementById('validationModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            const {
                confirmText = 'OK',
                cancelText = 'Batal',
                showCancel = false,
                confirmCallback = null,
                cancelCallback = null
            } = options;
            
            // Create modal HTML
            const modalHTML = `
                <div id="validationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3 text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full ${
                                type === 'error' ? 'bg-red-100' : 
                                type === 'warning' ? 'bg-yellow-100' : 
                                type === 'success' ? 'bg-green-100' : 
                                type === 'question' ? 'bg-yellow-100' : 'bg-blue-100'
                            }">
                                <svg class="h-6 w-6 ${
                                    type === 'error' ? 'text-red-600' : 
                                    type === 'warning' ? 'text-yellow-600' : 
                                    type === 'success' ? 'text-green-600' : 
                                    type === 'question' ? 'text-yellow-600' : 'text-blue-600'
                                }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    ${type === 'error' ? 
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' :
                                        type === 'warning' || type === 'question' ? 
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>' :
                                        type === 'success' ? 
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                                    }
                                </svg>
                            </div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">${title}</h3>
                            <div class="mt-2 px-7 py-3">
                                <p class="text-sm text-gray-500">${message}</p>
                            </div>
                            <div class="${showCancel ? 'flex justify-center space-x-4' : 'items-center'} px-4 py-3">
                                ${showCancel ? `
                                    <button id="modalCancelButton" class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                        ${cancelText}
                                    </button>
                                ` : ''}
                                <button id="modalConfirmButton" class="px-4 py-2 ${
                                    type === 'error' ? 'bg-red-500 hover:bg-red-700 focus:ring-red-300' :
                                    type === 'success' ? 'bg-green-500 hover:bg-green-700 focus:ring-green-300' :
                                    type === 'warning' || type === 'question' ? 'bg-yellow-500 hover:bg-yellow-700 focus:ring-yellow-300' :
                                    'bg-blue-500 hover:bg-blue-700 focus:ring-blue-300'
                                } text-white text-base font-medium rounded-md ${showCancel ? '' : 'w-full'} shadow-sm focus:outline-none focus:ring-2">
                                    ${confirmText}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            // Add event listeners
            if (showCancel) {
                document.getElementById('modalCancelButton').addEventListener('click', function() {
                    document.getElementById('validationModal').remove();
                    if (cancelCallback) cancelCallback();
                });
            }
            
            document.getElementById('modalConfirmButton').addEventListener('click', function() {
                document.getElementById('validationModal').remove();
                if (confirmCallback) confirmCallback();
            });
            
            // Close modal when clicking outside
            document.getElementById('validationModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.remove();
                    if (cancelCallback) cancelCallback();
                }
            });
        }
        
        function showConfirmationModal(title, message, onConfirm) {
            // Remove existing modal if any
            const existingModal = document.getElementById('confirmationModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Create confirmation modal HTML
            const modalHTML = `
                <div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3 text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">${title}</h3>
                            <div class="mt-2 px-7 py-3">
                                <p class="text-sm text-gray-500">${message}</p>
                            </div>
                            <div class="flex justify-center space-x-4 px-4 py-3">
                                <button id="modalCancelButton" class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                    Batal
                                </button>
                                <button id="modalConfirmButton" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                                    Ya, Finalisasi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            // Add event listeners
            document.getElementById('modalCancelButton').addEventListener('click', function() {
                document.getElementById('confirmationModal').remove();
            });
            
            document.getElementById('modalConfirmButton').addEventListener('click', function() {
                document.getElementById('confirmationModal').remove();
                onConfirm();
            });
            
            // Close modal when clicking outside
            document.getElementById('confirmationModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.remove();
                }
            });
        }
        
        function showGlobalLoading() {
            // Remove existing loading if any
            const existingLoading = document.getElementById('globalLoading');
            if (existingLoading) {
                existingLoading.remove();
            }
            
            // Create loading overlay
            const loadingHTML = `
                <div id="globalLoading" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                        <p class="text-gray-700">Memproses...</p>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', loadingHTML);
        }
        
        function hideGlobalLoading() {
            const loading = document.getElementById('globalLoading');
            if (loading) {
                loading.remove();
            }
        }
        
        // Show notification function (similar to target FRA)
        function showNotification(type, message) {
            // Remove existing notification if any
            const existingNotification = document.getElementById('notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            const notificationHTML = `
                <div id="notification" class="fixed top-4 right-4 z-50 max-w-sm">
                    <div class="${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'} text-white px-6 py-4 rounded-lg shadow-lg">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${type === 'success' ? 
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                                    type === 'error' ? 
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' :
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                                }
                            </svg>
                            <span>${message}</span>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', notificationHTML);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                const notification = document.getElementById('notification');
                if (notification) {
                    notification.remove();
                }
            }, 5000);
        }
    </script>
    
    <style>
        .recently-calculated {
            border-color: #3B82F6 !important;
            background-color: #EFF6FF !important;
            transition: all 0.3s ease;
        }

        .realisasi-input[readonly] {
            background-color: #F3F4F6 !important;
            font-weight: 500;
        }

        .capkin-kumulatif-output, .capkin-setahun-output {
            background-color: #F3F4F6 !important;
            font-weight: 500;
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
