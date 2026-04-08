<?php

namespace App\Http\Controllers;

use App\Models\Fra;
use App\Models\Triwulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use avadim\FastExcelWriter\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OptimizedDownloadController extends Controller
{
    /**
     * Download Excel dengan update data dari form input
     */
    public function downloadExcelWithUpdate(Fra $fra, $triwulan = null)
    {
        try {
            $startTime = microtime(true);
            
            // Cek apakah ada file original Excel
            $originalFilePath = storage_path('app/' . $fra->file_template);
            if (!file_exists($originalFilePath)) {
                // Jika tidak ada file original, gunakan method fast biasa sebagai fallback
                return $this->downloadExcelFast($fra, $triwulan);
            }
            
            // Load original Excel file
            $spreadsheet = IOFactory::load($originalFilePath);
            
            // Get data yang diperlukan
            $matriksList = $fra->matriks_fra()->with(['template_fra.template_jenis'])->get();
            
            if ($triwulan) {
                // Get triwulan object first
                $triwulanObj = Triwulan::where('fra_id', $fra->id)->where('nomor', $triwulan)->first();
                if ($triwulanObj) {
                    $realisasiData = $fra->realisasi_fra()->where('triwulan_id', $triwulanObj->id)->get()->keyBy('matriks_fra_id');
                } else {
                    $realisasiData = collect();
                }
                // Update single triwulan sheet
                $this->updateExcelWithTriwulanData($spreadsheet, $fra, $triwulan, $matriksList, $realisasiData);
            } else {
                $allRealisasiData = [];
                for ($tw = 1; $tw <= 4; $tw++) {
                    $triwulanObj = Triwulan::where('fra_id', $fra->id)->where('nomor', $tw)->first();
                    if ($triwulanObj) {
                        $allRealisasiData[$tw] = $fra->realisasi_fra()->where('triwulan_id', $triwulanObj->id)->get()->keyBy('matriks_fra_id');
                    } else {
                        $allRealisasiData[$tw] = collect();
                    }
                }
                // Update all sheets with complete data
                $this->updateExcelWithCompleteData($spreadsheet, $fra, $matriksList, $allRealisasiData);
            }
            
            // Generate filename
            $timestamp = now()->format('Y-m-d_H-i-s');
            if ($triwulan) {
                $fileName = "FRA_{$fra->tahun_berjalan}_TW{$triwulan}_Updated_{$timestamp}.xlsx";
            } else {
                $fileName = "FRA_{$fra->tahun_berjalan}_Lengkap_Updated_{$timestamp}.xlsx";
            }
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            // Stream the updated Excel file to browser
            return Response::streamDownload(function() use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'max-age=0',
                'X-Processing-Time' => $processingTime . 'ms',
                'X-Engine' => 'PhpSpreadsheet-Updated'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Excel update download error: ' . $e->getMessage());
            // Fallback to fast download if update fails
            return $this->downloadExcelFast($fra, $triwulan);
        }
    }
    
    /**
     * Update Excel with triwulan data
     */
    private function updateExcelWithTriwulanData($spreadsheet, $fra, $triwulan, $matriksList, $realisasiData)
    {
        // Find or create triwulan sheet
        $sheetName = "TW {$triwulan}";
        $sheet = null;
        
        foreach ($spreadsheet->getSheetNames() as $name) {
            if (stripos($name, "TW") !== false && stripos($name, (string)$triwulan) !== false) {
                $sheet = $spreadsheet->getSheetByName($name);
                break;
            }
        }
        
        if (!$sheet) {
            // Create new sheet if not exists
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($sheetName);
        }
        
        // Map data by excel row number for easier update
        $dataByRow = [];
        foreach ($matriksList as $matriks) {
            if ($matriks->excel_row) {
                $dataByRow[$matriks->excel_row] = [
                    'matriks' => $matriks,
                    'realisasi' => $realisasiData->get($matriks->id)
                ];
            }
        }
        
        // Update cells with form data
        foreach ($dataByRow as $row => $data) {
            $realisasi = $data['realisasi'];
            if ($realisasi) {
                // Update target
                $targetCol = $this->getTargetColumn($triwulan);
                if ($targetCol && $realisasi->target !== null) {
                    $sheet->setCellValue($targetCol . $row, $realisasi->target);
                }
                
                // Update realisasi
                $realisasiCol = $this->getRealisasiColumn($triwulan);
                if ($realisasiCol && $realisasi->realisasi !== null) {
                    $sheet->setCellValue($realisasiCol . $row, $realisasi->realisasi);
                }
                
                // Update other fields if columns are defined
                if ($realisasi->kendala) {
                    $kendalaCol = $this->getKendalaColumn();
                    if ($kendalaCol) {
                        $sheet->setCellValue($kendalaCol . $row, $realisasi->kendala);
                    }
                }
                
                if ($realisasi->solusi) {
                    $solusiCol = $this->getSolusiColumn();
                    if ($solusiCol) {
                        $sheet->setCellValue($solusiCol . $row, $realisasi->solusi);
                    }
                }
                
                if ($realisasi->tindak_lanjut) {
                    $tindakLanjutCol = $this->getTindakLanjutColumn();
                    if ($tindakLanjutCol) {
                        $sheet->setCellValue($tindakLanjutCol . $row, $realisasi->tindak_lanjut);
                    }
                }
            }
        }
    }
    
    /**
     * Update Excel with complete data (all triwulan)
     */
    private function updateExcelWithCompleteData($spreadsheet, $fra, $matriksList, $allRealisasiData)
    {
        // Update each triwulan sheet
        for ($tw = 1; $tw <= 4; $tw++) {
            $this->updateExcelWithTriwulanData($spreadsheet, $fra, $tw, $matriksList, $allRealisasiData[$tw]);
        }
        
        // Optionally create/update summary sheet
        $this->createSummarySheet($spreadsheet, $fra, $matriksList, $allRealisasiData);
    }
    
    /**
     * Create summary sheet with all triwulan data
     */
    private function createSummarySheet($spreadsheet, $fra, $matriksList, $allRealisasiData)
    {
        $sheet = null;
        
        // Find existing summary sheet
        foreach ($spreadsheet->getSheetNames() as $name) {
            if (stripos($name, 'summary') !== false || stripos($name, 'ringkasan') !== false) {
                $sheet = $spreadsheet->getSheetByName($name);
                break;
            }
        }
        
        if (!$sheet) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Ringkasan');
        }
        
        // Write headers
        $sheet->setCellValue('A1', 'Form Rencana Aksi (FRA) - Ringkasan');
        $sheet->setCellValue('A2', 'Tahun: ' . $fra->tahun_berjalan);
        $sheet->setCellValue('A3', 'Generated: ' . now()->format('d/m/Y H:i:s'));
        
        // Table headers
        $headers = ['No', 'Indikator', 'Satuan', 'TW1', 'TW2', 'TW3', 'TW4', 'Total', 'Capaian (%)'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $col++;
        }
        
        // Data rows
        $row = 6;
        $no = 1;
        foreach ($matriksList as $matriks) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $this->formatIndicatorText($matriks));
            $sheet->setCellValue('C' . $row, $matriks->satuan ?? '');
            
            $totalTarget = 0;
            $totalRealisasi = 0;
            
            // TW1-4 data
            for ($tw = 1; $tw <= 4; $tw++) {
                $realisasi = $allRealisasiData[$tw]->get($matriks->id);
                $value = $realisasi ? $realisasi->realisasi : 0;
                $sheet->setCellValue(chr(67 + $tw) . $row, $value);
                
                if ($realisasi) {
                    $totalTarget += $realisasi->target ?? 0;
                    $totalRealisasi += $realisasi->realisasi ?? 0;
                }
            }
            
            // Total and percentage
            $sheet->setCellValue('H' . $row, $totalRealisasi);
            $capaian = $totalTarget > 0 ? round(($totalRealisasi / $totalTarget) * 100, 2) : 0;
            $sheet->setCellValue('I' . $row, $capaian . '%');
            
            $row++;
        }
    }
    
    /**
     * Get target column based on template format
     */
    private function getTargetColumn($triwulan)
    {
        // Adjust based on your Excel template structure
        return 'D'; // Example: column D for target
    }
    
    /**
     * Get realisasi column
     */
    private function getRealisasiColumn($triwulan)
    {
        // Adjust based on your Excel template structure
        return 'E'; // Example: column E for realisasi
    }

    /**
     * Get kendala column
     */
    private function getKendalaColumn()
    {
        return 'F'; // Example: column F for kendala
    }

    /**
     * Get solusi column
     */
    private function getSolusiColumn()
    {
        return 'G'; // Example: column G for solusi
    }

    /**
     * Get tindak lanjut column
     */
    private function getTindakLanjutColumn()
    {
        return 'H'; // Example: column H for tindak lanjut
    }
    
    /**
     * Download Excel dengan FastExcelWriter (7-9x lebih cepat)
     */
    public function downloadExcelFast(Fra $fra, $triwulan = null)
    {
        try {
            $startTime = microtime(true);
            
            // Generate filename with proper .xlsx extension
            $timestamp = now()->format('Y-m-d_H-i-s');
            if ($triwulan) {
                $fileName = "FRA_{$fra->tahun_berjalan}_TW{$triwulan}_{$timestamp}.xlsx";
            } else {
                $fileName = "FRA_{$fra->tahun_berjalan}_Lengkap_{$timestamp}.xlsx";
            }

            // Ambil data yang diperlukan
            $matriksList = $fra->matriks_fra()->with(['template_fra.template_jenis'])->get();
            
            if ($triwulan) {
                // Get triwulan object first
                $triwulanObj = Triwulan::where('fra_id', $fra->id)->where('nomor', $triwulan)->first();
                if ($triwulanObj) {
                    $realisasiData = $fra->realisasi_fra()->where('triwulan_id', $triwulanObj->id)->get()->keyBy('matriks_fra_id');
                } else {
                    $realisasiData = collect();
                }
            } else {
                $allRealisasiData = [];
                for ($tw = 1; $tw <= 4; $tw++) {
                    $triwulanObj = Triwulan::where('fra_id', $fra->id)->where('nomor', $tw)->first();
                    if ($triwulanObj) {
                        $allRealisasiData[$tw] = $fra->realisasi_fra()->where('triwulan_id', $triwulanObj->id)->get()->keyBy('matriks_fra_id');
                    } else {
                        $allRealisasiData[$tw] = collect();
                    }
                }
            }

            // Create Excel with FastExcelWriter
            $excel = Excel::create();
            
            if ($triwulan) {
                // Single triwulan sheet
                $this->createTriwulanSheetFast($excel, $fra, $triwulan, $matriksList, $realisasiData);
            } else {
                // Multiple sheets for complete FRA
                $this->createCompleteSheetsFast($excel, $fra, $matriksList, $allRealisasiData);
            }

            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2); // milliseconds

            // Save Excel to temporary file and force download
            $tempPath = storage_path('app/temp/' . $fileName);
            
            // Ensure temp directory exists
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }
            
            // Save Excel to temp file
            $excel->save($tempPath);
            
            // Force download and delete temp file after download
            return response()->download($tempPath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Processing-Time' => $processingTime . 'ms',
                'X-Engine' => 'FastExcelWriter'
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Fast Excel download error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating Excel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download PDF dengan optimasi dompdf
     */
    public function downloadPdfFast(Fra $fra, $triwulan = null)
    {
        try {
            $startTime = microtime(true);
            
            // Generate filename with proper .pdf extension
            $timestamp = now()->format('Y-m-d_H-i-s');
            if ($triwulan) {
                $fileName = "FRA_{$fra->tahun_berjalan}_TW{$triwulan}_{$timestamp}.pdf";
            } else {
                $fileName = "FRA_{$fra->tahun_berjalan}_Lengkap_{$timestamp}.pdf";
            }

            // Ambil data yang diperlukan
            $matriksList = $fra->matriks_fra()->with(['template_fra.template_jenis'])->get();
            
            if ($triwulan) {
                // Get triwulan object first
                $triwulanObj = Triwulan::where('fra_id', $fra->id)->where('nomor', $triwulan)->first();
                if ($triwulanObj) {
                    $realisasiData = $fra->realisasi_fra()->where('triwulan_id', $triwulanObj->id)->get()->keyBy('matriks_fra_id');
                } else {
                    $realisasiData = collect();
                }
                $viewData = [
                    'fra' => $fra,
                    'triwulan' => $triwulan,
                    'matriksList' => $matriksList,
                    'realisasiData' => $realisasiData,
                    'title' => "Form Rencana Aksi {$fra->tahun_berjalan} - Triwulan {$triwulan}"
                ];
                $viewName = 'pdf.fra_triwulan_optimized';
            } else {
                $allRealisasiData = [];
                for ($tw = 1; $tw <= 4; $tw++) {
                    $triwulanObj = Triwulan::where('fra_id', $fra->id)->where('nomor', $tw)->first();
                    if ($triwulanObj) {
                        $allRealisasiData[$tw] = $fra->realisasi_fra()->where('triwulan_id', $triwulanObj->id)->get()->keyBy('matriks_fra_id');
                    } else {
                        $allRealisasiData[$tw] = collect();
                    }
                }
                $viewData = [
                    'fra' => $fra,
                    'matriksList' => $matriksList,
                    'allRealisasiData' => $allRealisasiData,
                    'title' => "Form Rencana Aksi {$fra->tahun_berjalan} - Lengkap"
                ];
                $viewName = 'pdf.fra_lengkap_optimized';
            }

            // Optimized PDF generation
            $pdf = Pdf::loadView($viewName, $viewData);
            $pdf->setPaper('A4', 'landscape');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'chroot' => public_path(),
                'dpi' => 96,
                'defaultFont' => 'DejaVu Sans'
            ]);

            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2); // milliseconds

            // Save PDF to temporary file and force download
            $tempPath = storage_path('app/temp/' . $fileName);
            
            // Ensure temp directory exists
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }
            
            // Save PDF to temp file
            file_put_contents($tempPath, $pdf->output());
            
            // Force download and delete temp file after download
            return response()->download($tempPath, $fileName, [
                'Content-Type' => 'application/pdf',
                'X-Processing-Time' => $processingTime . 'ms',
                'X-Engine' => 'DomPDF-Optimized'
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Fast PDF download error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create triwulan sheet with FastExcelWriter
     */
    private function createTriwulanSheetFast($excel, $fra, $triwulan, $matriksList, $realisasiData)
    {
        $sheet = $excel->sheet();
        $sheet->setName("TW {$triwulan}");

        // Header informasi
        $sheet->writeRow(['Form Rencana Aksi (FRA)', $fra->tahun_berjalan]);
        $sheet->writeRow(['Triwulan', $triwulan]);
        $sheet->writeRow(['Generated', now()->format('d/m/Y H:i:s')]);
        $sheet->writeRow([]); // Empty row

        // Header tabel
        $headers = [
            'No',
            'Tujuan/Sasaran/Indikator', 
            'Satuan',
            'Target',
            'Realisasi',
            'Capaian Kinerja (%)',
            'Kendala',
            'Solusi',
            'Tindak Lanjut',
            'PIC',
            'Batas Waktu'
        ];
        
        // Headers are written with styling above
        
        // Style header - FastExcelWriter doesn't support getStyle()
        // Headers will be styled when writing the row
        $sheet->writeRow($headers, ['font' => ['bold' => true], 'fill' => ['color' => '#E3F2FD']]);

        // Data rows
        $no = 1;
        foreach ($matriksList as $matriks) {
            $realisasi = $realisasiData->get($matriks->id);
            
            // Hitung capaian kinerja
            $capaianPersen = 0;
            if ($realisasi && $realisasi->target > 0) {
                $capaianPersen = round(($realisasi->realisasi / $realisasi->target) * 100, 2);
            }

            $rowData = [
                $no++,
                $this->formatIndicatorText($matriks),
                $matriks->satuan ?? '',
                $realisasi->target ?? '',
                $realisasi->realisasi ?? '',
                $capaianPersen . '%',
                $realisasi->kendala ?? '',
                $realisasi->solusi ?? '',
                $realisasi->tindak_lanjut ?? '',
                $realisasi->pic_tindak_lanjut ?? '',
                $realisasi->batas_waktu_tindak_lanjut ? $realisasi->batas_waktu_tindak_lanjut->format('d/m/Y') : ''
            ];

            $sheet->writeRow($rowData);
        }

        // Auto width untuk kolom
        $sheet->setColWidths([5, 50, 10, 12, 12, 15, 30, 30, 30, 20, 15]);
    }

    /**
     * Create complete sheets with FastExcelWriter
     */
    private function createCompleteSheetsFast($excel, $fra, $matriksList, $allRealisasiData)
    {
        // Sheet Ringkasan
        $summarySheet = $excel->sheet();
        $summarySheet->setName('Ringkasan');
        
        $summarySheet->writeRow(['Form Rencana Aksi (FRA) Lengkap', $fra->tahun_berjalan]);
        $summarySheet->writeRow(['Generated', now()->format('d/m/Y H:i:s')]);
        $summarySheet->writeRow([]); // Empty row

        $summaryHeaders = [
            'No',
            'Indikator',
            'Satuan',
            'TW1',
            'TW2', 
            'TW3',
            'TW4',
            'Total Capaian (%)',
            'Status'
        ];
        
        $summarySheet->writeRow($summaryHeaders, ['font' => ['bold' => true], 'fill' => ['color' => '#E3F2FD']]);

        $no = 1;
        foreach ($matriksList as $matriks) {
            $realisasiTW = [];
            $totalTarget = 0;
            $totalRealisasi = 0;

            // Hitung total untuk semua TW
            for ($tw = 1; $tw <= 4; $tw++) {
                $realisasi = $allRealisasiData[$tw]->get($matriks->id);
                $realisasiTW[$tw] = $realisasi ? $realisasi->realisasi : 0;
                if ($realisasi) {
                    $totalTarget += $realisasi->target ?? 0;
                    $totalRealisasi += $realisasi->realisasi ?? 0;
                }
            }

            $totalCapaian = $totalTarget > 0 ? round(($totalRealisasi / $totalTarget) * 100, 2) : 0;
            $status = $totalCapaian >= 100 ? 'Tercapai' : ($totalCapaian >= 80 ? 'Hampir Tercapai' : 'Belum Tercapai');

            $rowData = [
                $no++,
                $this->formatIndicatorText($matriks),
                $matriks->satuan ?? '',
                $realisasiTW[1],
                $realisasiTW[2],
                $realisasiTW[3], 
                $realisasiTW[4],
                $totalCapaian . '%',
                $status
            ];

            $summarySheet->writeRow($rowData);
        }

        $summarySheet->setColWidths([5, 40, 10, 12, 12, 12, 12, 15, 15]);

        // Create individual TW sheets
        for ($tw = 1; $tw <= 4; $tw++) {
            $twSheet = $excel->createSheet();
            $twSheet->setName("TW {$tw}");
            $this->createTriwulanSheetDetailFast($twSheet, $fra, $tw, $matriksList, $allRealisasiData[$tw]);
        }
    }

    /**
     * Create detailed triwulan sheet
     */
    private function createTriwulanSheetDetailFast($sheet, $fra, $triwulan, $matriksList, $realisasiData)
    {
        $sheet->writeRow(['Form Rencana Aksi (FRA)', $fra->tahun_berjalan]);
        $sheet->writeRow(['Triwulan', $triwulan]);
        $sheet->writeRow(['Generated', now()->format('d/m/Y H:i:s')]);
        $sheet->writeRow([]); // Empty row

        $headers = [
            'No',
            'Indikator',
            'Satuan', 
            'Target',
            'Realisasi',
            'Capaian (%)',
            'Kendala',
            'Solusi'
        ];
        
        $sheet->writeRow($headers, ['font' => ['bold' => true], 'fill' => ['color' => '#E3F2FD']]);

        $no = 1;
        foreach ($matriksList as $matriks) {
            $realisasi = $realisasiData->get($matriks->id);
            
            $capaianPersen = 0;
            if ($realisasi && $realisasi->target > 0) {
                $capaianPersen = round(($realisasi->realisasi / $realisasi->target) * 100, 2);
            }

            $rowData = [
                $no++,
                $this->formatIndicatorText($matriks),
                $matriks->satuan ?? '',
                $realisasi->target ?? '',
                $realisasi->realisasi ?? '',
                $capaianPersen . '%',
                $realisasi->kendala ?? '',
                $realisasi->solusi ?? ''
            ];

            $sheet->writeRow($rowData);
        }

        $sheet->setColWidths([5, 40, 10, 12, 12, 15, 30, 30]);
    }

    /**
     * Format indicator text for display
     */
    private function formatIndicatorText($matriks)
    {
        $text = '';
        
        if (!empty($matriks->tujuan)) {
            $text .= $matriks->tujuan . "\n";
        }
        
        if (!empty($matriks->sasaran)) {
            $text .= "  " . $matriks->sasaran . "\n";
        }
        
        if (!empty($matriks->indikator)) {
            $text .= "    " . $matriks->indikator;
        }
        
        if (!empty($matriks->sub_indikator)) {
            $text .= "\n      " . $matriks->sub_indikator;
        }
        
        if (!empty($matriks->detail_sub)) {
            $text .= "\n        " . $matriks->detail_sub;
        }
        
        return trim($text);
    }

    /**
     * Test dan benchmark performa download
     */
    public function benchmarkDownload(Fra $fra, $format = 'excel', $triwulan = null)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        try {
            if ($format === 'excel') {
                $response = $this->downloadExcelFast($fra, $triwulan);
            } else {
                $response = $this->downloadPdfFast($fra, $triwulan);
            }

            $endTime = microtime(true);
            $endMemory = memory_get_usage(true);
            
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            $memoryUsed = round(($endMemory - $startMemory) / 1024 / 1024, 2);

            Log::info("Optimized Download Benchmark", [
                'format' => $format,
                'triwulan' => $triwulan,
                'processing_time_ms' => $processingTime,
                'memory_used_mb' => $memoryUsed,
                'engine' => $format === 'excel' ? 'FastExcelWriter' : 'DomPDF-Optimized'
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error('Benchmark download error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Benchmark error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test download functionality - save files to storage for verification
     */
    public function testDownloadToStorage($fra_id = null)
    {
        try {
            // Get FRA - use first available if no ID provided
            if (!$fra_id) {
                $fra = Fra::first();
                if (!$fra) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada FRA yang tersedia untuk testing'
                    ], 404);
                }
            } else {
                $fra = Fra::find($fra_id);
                if (!$fra) {
                    return response()->json([
                        'success' => false,
                        'message' => "FRA dengan ID {$fra_id} tidak ditemukan"
                    ], 404);
                }
            }

            $testResults = [];
            $timestamp = now()->format('Y-m-d_H-i-s');
            $testDir = "test_downloads_fra_{$fra->id}_{$timestamp}";
            
            // Create test directory in storage/app/public
            $publicPath = storage_path("app/public/{$testDir}");
            if (!is_dir($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            // Test 1: Excel Lengkap
            try {
                $startTime = microtime(true);
                $fileName = "FRA_{$fra->tahun_berjalan}_Lengkap_Test_{$timestamp}.xlsx";
                $filePath = $publicPath . '/' . $fileName;
                
                $matriksList = $fra->matriks_fra()->with(['template_fra.template_jenis'])->get();
                $allRealisasiData = [];
                for ($tw = 1; $tw <= 4; $tw++) {
                    $triwulanObj = \App\Models\Triwulan::where('fra_id', $fra->id)->where('nomor', $tw)->first();
                    if ($triwulanObj) {
                        $allRealisasiData[$tw] = $fra->realisasi_fra()->where('triwulan_id', $triwulanObj->id)->get()->keyBy('matriks_fra_id');
                    } else {
                        $allRealisasiData[$tw] = collect();
                    }
                }

                $excel = Excel::create();
                $this->createCompleteSheetsFast($excel, $fra, $matriksList, $allRealisasiData);
                $excel->save($filePath);
                
                $endTime = microtime(true);
                $processingTime = round(($endTime - $startTime) * 1000, 2);
                $fileSize = file_exists($filePath) ? round(filesize($filePath) / 1024, 2) : 0;
                
                $testResults['excel_lengkap'] = [
                    'status' => 'success',
                    'filename' => $fileName,
                    'path' => $filePath,
                    'public_url' => asset("storage/{$testDir}/{$fileName}"),
                    'size_kb' => $fileSize,
                    'processing_time_ms' => $processingTime,
                    'engine' => 'FastExcelWriter'
                ];
            } catch (\Exception $e) {
                $testResults['excel_lengkap'] = [
                    'status' => 'error',
                    'error' => $e->getMessage()
                ];
            }

            // Test 2: PDF Lengkap
            try {
                $startTime = microtime(true);
                $fileName = "FRA_{$fra->tahun_berjalan}_Lengkap_Test_{$timestamp}.pdf";
                $filePath = $publicPath . '/' . $fileName;
                
                $matriksList = $fra->matriks_fra()->with(['template_fra.template_jenis'])->get();
                $allRealisasiData = [];
                for ($tw = 1; $tw <= 4; $tw++) {
                    $triwulanObj = \App\Models\Triwulan::where('fra_id', $fra->id)->where('nomor', $tw)->first();
                    if ($triwulanObj) {
                        $allRealisasiData[$tw] = $fra->realisasi_fra()->where('triwulan_id', $triwulanObj->id)->get()->keyBy('matriks_fra_id');
                    } else {
                        $allRealisasiData[$tw] = collect();
                    }
                }

                $viewData = [
                    'fra' => $fra,
                    'matriksList' => $matriksList,
                    'allRealisasiData' => $allRealisasiData,
                    'title' => "Form Rencana Aksi {$fra->tahun_berjalan} - Lengkap"
                ];

                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.fra_lengkap_optimized', $viewData);
                $pdf->setPaper('A4', 'landscape');
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                    'chroot' => public_path(),
                    'dpi' => 96,
                    'defaultFont' => 'DejaVu Sans'
                ]);
                
                file_put_contents($filePath, $pdf->output());
                
                $endTime = microtime(true);
                $processingTime = round(($endTime - $startTime) * 1000, 2);
                $fileSize = file_exists($filePath) ? round(filesize($filePath) / 1024, 2) : 0;
                
                $testResults['pdf_lengkap'] = [
                    'status' => 'success',
                    'filename' => $fileName,
                    'path' => $filePath,
                    'public_url' => asset("storage/{$testDir}/{$fileName}"),
                    'size_kb' => $fileSize,
                    'processing_time_ms' => $processingTime,
                    'engine' => 'DomPDF-Optimized'
                ];
            } catch (\Exception $e) {
                $testResults['pdf_lengkap'] = [
                    'status' => 'error',
                    'error' => $e->getMessage()
                ];
            }

            // Test 3: Excel per Triwulan (TW1)
            try {
                $startTime = microtime(true);
                $fileName = "FRA_{$fra->tahun_berjalan}_TW1_Test_{$timestamp}.xlsx";
                $filePath = $publicPath . '/' . $fileName;
                
                $matriksList = $fra->matriks_fra()->with(['template_fra.template_jenis'])->get();
                $triwulanObj = \App\Models\Triwulan::where('fra_id', $fra->id)->where('nomor', 1)->first();
                $realisasiData = $triwulanObj ? $fra->realisasi_fra()->where('triwulan_id', $triwulanObj->id)->get()->keyBy('matriks_fra_id') : collect();

                $excel = Excel::create();
                $this->createTriwulanSheetFast($excel, $fra, 1, $matriksList, $realisasiData);
                $excel->save($filePath);
                
                $endTime = microtime(true);
                $processingTime = round(($endTime - $startTime) * 1000, 2);
                $fileSize = file_exists($filePath) ? round(filesize($filePath) / 1024, 2) : 0;
                
                $testResults['excel_tw1'] = [
                    'status' => 'success',
                    'filename' => $fileName,
                    'path' => $filePath,
                    'public_url' => asset("storage/{$testDir}/{$fileName}"),
                    'size_kb' => $fileSize,
                    'processing_time_ms' => $processingTime,
                    'engine' => 'FastExcelWriter'
                ];
            } catch (\Exception $e) {
                $testResults['excel_tw1'] = [
                    'status' => 'error',
                    'error' => $e->getMessage()
                ];
            }

            // Create summary file
            $summaryContent = "=== FRA DOWNLOAD TEST RESULTS ===\n";
            $summaryContent .= "Test Date: " . now()->format('d/m/Y H:i:s') . "\n";
            $summaryContent .= "FRA ID: {$fra->id}\n";
            $summaryContent .= "FRA Year: {$fra->tahun_berjalan}\n";
            $summaryContent .= "Test Directory: {$testDir}\n\n";
            
            foreach ($testResults as $testName => $result) {
                $summaryContent .= "=== " . strtoupper(str_replace('_', ' ', $testName)) . " ===\n";
                if ($result['status'] === 'success') {
                    $summaryContent .= "✅ Status: SUCCESS\n";
                    $summaryContent .= "📁 Filename: {$result['filename']}\n";
                    $summaryContent .= "📂 Path: {$result['path']}\n";
                    $summaryContent .= "🌐 Public URL: {$result['public_url']}\n";
                    $summaryContent .= "📏 Size: {$result['size_kb']} KB\n";
                    $summaryContent .= "⚡ Processing Time: {$result['processing_time_ms']} ms\n";
                    $summaryContent .= "🔧 Engine: {$result['engine']}\n";
                } else {
                    $summaryContent .= "❌ Status: ERROR\n";
                    $summaryContent .= "💥 Error: {$result['error']}\n";
                }
                $summaryContent .= "\n";
            }
            
            $summaryContent .= "=== INSTRUCTIONS FOR VERIFICATION ===\n";
            $summaryContent .= "1. Check the files in: {$publicPath}\n";
            $summaryContent .= "2. Or access via web: " . asset("storage/{$testDir}") . "\n";
            $summaryContent .= "3. Verify Excel files open correctly and contain proper data\n";
            $summaryContent .= "4. Verify PDF files display proper formatting and complete content\n";
            $summaryContent .= "5. Check file integrity and ensure no corruption\n";
            
            file_put_contents($publicPath . '/TEST_SUMMARY.txt', $summaryContent);

            return response()->json([
                'success' => true,
                'message' => 'Test download completed successfully!',
                'fra_info' => [
                    'id' => $fra->id,
                    'year' => $fra->tahun_berjalan,
                    'status' => $fra->status
                ],
                'test_directory' => $testDir,
                'storage_path' => $publicPath,
                'public_url' => asset("storage/{$testDir}"),
                'results' => $testResults,
                'summary_file' => asset("storage/{$testDir}/TEST_SUMMARY.txt"),
                'instructions' => [
                    'Files saved to: ' . $publicPath,
                    'Access via web: ' . asset("storage/{$testDir}"),
                    'Check TEST_SUMMARY.txt for detailed results',
                    'Verify all files are not corrupted and contain expected content'
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Test download to storage error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Test download failed: ' . $e->getMessage()
            ], 500);
        }
    }
}