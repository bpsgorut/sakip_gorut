<?php

namespace App\Services;

use App\Models\Fra;
use App\Models\Template_Jenis;
use App\Models\Template_Fra;
use App\Models\Matriks_Fra;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class KabKotaFraParser
{
    private $fra;
    private $debug = false;
    private $templateFra;

    public function __construct(Fra $fra)
    {
        $this->fra = $fra;
    }

    /**
     * Parse Excel format KabKota dengan struktur hierarki baru
     */
    public function parseExcel($filePath)
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $parsedData = [];

            // Analyze dan parse setiap sheet
            foreach ($spreadsheet->getSheetNames() as $index => $sheetName) {
                $sheetType = null;
                if (stripos($sheetName, 'IKU') !== false) {
                    $sheetType = 'PK IKU';
                } elseif (stripos($sheetName, 'Suplemen') !== false) {
                    $sheetType = 'Suplemen';
                }

                if ($sheetType) {
                    $templateJenis = Template_Jenis::firstOrCreate(
                        ['nama' => $sheetType]
                    );
                    
                    $this->templateFra = Template_Fra::firstOrCreate([
                        'fra_id' => $this->fra->id,
                        'template_jenis_id' => $templateJenis->id
                    ]);

                    $worksheet = $spreadsheet->getSheet($index);
                    $sheetData = $this->parseSheet($worksheet, $sheetName);
                    $parsedData = array_merge($parsedData, $sheetData);
                }
            }

            if (empty($parsedData)) {
                throw new \Exception('Tidak ada data yang berhasil diparse. File mungkin memiliki format yang tidak didukung.');
            }

            // Convert parsed data to actual Matriks_Fra records
            $finalData = [];
            foreach ($parsedData as $tempData) {
                // Hapus field xy_type sebelum disimpan ke database jika ada
                $xy_type = $tempData['xy_type'] ?? null;
                unset($tempData['xy_type']);
                
                $matriksData = Matriks_Fra::create([
                    'template_fra_id' => $this->templateFra->id,
                    'tujuan' => $tempData['tujuan'],
                    'sasaran' => $tempData['sasaran'],
                    'indikator' => $tempData['indikator'],
                    'detail_indikator' => $tempData['detail_indikator'],
                    'sub_indikator' => $tempData['sub_indikator'],
                    'detail_sub' => $tempData['detail_sub'],
                    'jenis_iku_proksi' => $tempData['jenis_iku_proksi'],
                    'jenis_waktu' => $tempData['jenis_waktu'],
                    'jenis_persen' => $tempData['jenis_persen'],
                    'satuan' => $tempData['satuan'],
                    'excel_row' => $tempData['excel_row']
                ]);
                
                $finalData[] = $matriksData;
            }

            return [
                'success' => true,
                'items_count' => count($finalData),
                'message' => "Berhasil memparse " . count($finalData) . " item dari file Excel KabKota",
                'parsed_data' => $finalData
            ];
            
        } catch (\Exception $e) {
            Log::error('❌ KabKota FRA Parser error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Parse individual sheet
     */
    private function parseSheet($sheet, $sheetName)
    {
        $maxRow = $sheet->getHighestDataRow();
        $maxColString = $sheet->getHighestDataColumn();
        $effectiveMaxCol = ($maxColString < 'L') ? 'L' : $maxColString;
    
        $currentTujuan = null;
        $currentSasaran = null;
        $currentIndikator = null;
        $currentSubIndikator = null;
    
        $extractedData = [];
    
        for ($row = 1; $row <= $maxRow; $row++) {
            $rowData = [];
            $isRowEmpty = true;
            for ($col = 'A'; $col <= $effectiveMaxCol; $col++) {
                $cellValue = $sheet->getCell($col . $row)->getCalculatedValue();
                $rowData[$col] = is_string($cellValue) ? trim($cellValue) : $cellValue;
                if (!empty($rowData[$col])) {
                    $isRowEmpty = false;
                }
            }
            
            if ($isRowEmpty) continue;
    
            $foundHierarchyInRow = false;

            // Iterasi kolom untuk mendeteksi level hierarki
            foreach ($rowData as $currentCol => $cellValue) {
                if (empty($cellValue)) continue;

                $level = $this->detectHierarchyLevel($cellValue);

                // Konversi 'detail_x_y' berdasarkan konteks
                if ($level === 'detail_x_y') {
                    if ($currentSubIndikator) {
                        $level = 'detail_sub';
                    } elseif ($currentIndikator) {
                        $level = 'detail_indikator';
                    } else {
                        continue; // Abaikan jika tidak ada konteks
                    }
                }

                if ($level) {
                    $content = $this->getContentForHierarchy($level, $currentCol, $rowData, $cellValue);
                    $jenisData = $this->getJenisData($rowData);

                    switch ($level) {
                        case 'tujuan':
                            $currentTujuan = $content;
                            $currentSasaran = null;
                            $currentIndikator = null;
                            $currentSubIndikator = null;
                            break;
                        case 'sasaran':
                            $currentSasaran = $content;
                            $currentIndikator = null;
                            $currentSubIndikator = null;
                            break;
                        case 'indikator':
                            $currentIndikator = $content;
                            $currentSubIndikator = null;
                            $extractedData[] = $this->createTempMatriksData(
                                $currentTujuan, $currentSasaran, $currentIndikator, null, null, null, $jenisData, $row
                            );
                            break;
                        case 'detail_indikator':
                            if ($currentIndikator) {
                                $extractedData[] = $this->createTempMatriksData(
                                    $currentTujuan, $currentSasaran, $currentIndikator, $content, null, null, $jenisData, $row
                                );
                            }
                            break;
                        case 'sub_indikator':
                            $currentSubIndikator = $content;
                            if ($currentIndikator) {
                                $extractedData[] = $this->createTempMatriksData(
                                    $currentTujuan, $currentSasaran, $currentIndikator, null, $currentSubIndikator, null, $jenisData, $row
                                );
                            }
                            break;
                        case 'detail_sub':
                            if ($currentSubIndikator) {
                                 $extractedData[] = $this->createTempMatriksData(
                                    $currentTujuan, $currentSasaran, $currentIndikator, null, $currentSubIndikator, $content, $jenisData, $row
                                );
                            }
                            break;
                    }
                    // Hanya proses hierarki pertama yang ditemukan di baris
                    $foundHierarchyInRow = true;
                    break; 
                }
            }
        }
        
        return $extractedData;
    }

    /**
     * Create temporary matriks data array (not saved to DB yet)
     */
    private function createTempMatriksData($tujuan, $sasaran, $indikator, $detailIndikator, $subIndikator, $detailSub, $jenisData, $row)
    {
        return [
            'tujuan' => $tujuan,
            'sasaran' => $sasaran,
            'indikator' => $indikator,
            'detail_indikator' => $detailIndikator,
            'sub_indikator' => $subIndikator,
            'detail_sub' => $detailSub,
            'jenis_iku_proksi' => $jenisData['jenis_iku_proksi'],
            'jenis_waktu' => $jenisData['jenis_waktu'],
            'jenis_persen' => $jenisData['jenis_persen'],
            'satuan' => $jenisData['satuan'],
            'excel_row' => $row
        ];
    }

    /**
     * Get appropriate content for hierarchy level based on Excel structure
     */
    private function getContentForHierarchy($level, $currentCol, $rowData, $cellValue)
    {
        switch ($level) {
            case 'tujuan':
                // Tujuan: biasanya sudah lengkap dalam satu cell
                return $cellValue;
                
            case 'sasaran':
                // Sasaran: kode di kolom B, konten di kolom C
                if ($currentCol === 'B' && isset($rowData['C'])) {
                    return $rowData['C']; // Ambil konten dari kolom C
                }
                return $cellValue; // Fallback jika struktur tidak sesuai
                
            case 'indikator':
                // Indikator: kode di kolom D, konten di kolom E
                if ($currentCol === 'D' && isset($rowData['E'])) {
                    return $rowData['E']; // Ambil konten dari kolom E
                }
                return $cellValue; // Fallback jika struktur tidak sesuai
                
            case 'detail_indikator':
                // Detail Indikator: sudah lengkap di kolom E (X: ..., Y: ...)
                // Pastikan format X dan Y sesuai
                if (preg_match('/^[xy]\s*:/i', $cellValue)) {
                    // Standardize format: "X: " or "Y: " with exactly one space after colon
                    $content = preg_replace('/^([xy])\s*:/i', '$1: ', trim($cellValue));
                    return $content;
                }
                
                // Jika belum dalam format X: atau Y:, cek apakah ada di kolom E
                if ($currentCol === 'D' && isset($rowData['E'])) {
                    $content = $rowData['E'];
                    if (preg_match('/^[xy]\s*:/i', $content)) {
                        // Standardize format here too
                        return preg_replace('/^([xy])\s*:/i', '$1: ', trim($content));
                    }
                }
                
                return $cellValue; // Fallback ke konten asli
                
            case 'sub_indikator':
                // Sub Indikator: kode di kolom F, konten di kolom G
                if ($currentCol === 'F' && isset($rowData['G'])) {
                    return $rowData['G']; // Ambil konten dari kolom G
                }
                return $cellValue; // Fallback jika struktur tidak sesuai
                
            case 'detail_sub':
                // Detail Sub: sudah lengkap di kolom G (X: ..., Y: ...)
                // Pastikan format X dan Y sesuai
                if (preg_match('/^[xy]\s*:/i', $cellValue)) {
                    // Standardize format: "X: " or "Y: " with exactly one space after colon
                    $content = preg_replace('/^([xy])\s*:/i', '$1: ', trim($cellValue));
                    return $content;
                }
                
                // Jika belum dalam format X: atau Y:, cek apakah ada di kolom G
                if ($currentCol === 'F' && isset($rowData['G'])) {
                    $content = $rowData['G'];
                    if (preg_match('/^[xy]\s*:/i', $content)) {
                        // Standardize format here too
                        return preg_replace('/^([xy])\s*:/i', '$1: ', trim($content));
                    }
                }
                
                return $cellValue; // Fallback ke konten asli
                
            default:
                return $cellValue;
        }
    }

    /**
     * Get jenis data from columns H, I, J and satuan from column L
     */
    private function getJenisData($rowData)
    {
        return [
            'jenis_iku_proksi' => isset($rowData['H']) ? trim($rowData['H']) : null,
            'jenis_waktu' => isset($rowData['I']) ? trim($rowData['I']) : null,
            'jenis_persen' => isset($rowData['J']) ? trim($rowData['J']) : null,
            'satuan' => isset($rowData['L']) ? trim($rowData['L']) : null, // Kolom L untuk satuan
        ];
    }

    private function detectHierarchyLevel($cellValue)
    {
        // Clean the cell value
        $cellValue = trim($cellValue);
        
        if (empty($cellValue)) {
            return null;
        }
        
        // Detect Tujuan (T1, T2, T3 patterns)
        if (preg_match('/^T\d+/i', $cellValue)) {
            return 'tujuan';
        }
        
        // Detect Sasaran (3 digit pattern: 1.1.1, 1.2.1, etc.)
        if (preg_match('/^\d+\.\d+\.\d+(\s|$)/', $cellValue)) {
            return 'sasaran';
        }
        
        // Detect Indikator (4 digit pattern: 1.1.1.1, 1.2.1.1, etc.)
        if (preg_match('/^\d+\.\d+\.\d+\.\d+(\s|$)/', $cellValue)) {
            return 'indikator';
        }
        
        // Detect Sub Indikator (5 digit pattern: 1.1.1.1.1, 1.2.1.1.1, etc.)
        if (preg_match('/^\d+\.\d+\.\d+\.\d+\.\d+\.?(\s|$)/', $cellValue)) {
            return 'sub_indikator';
        }
        
        // Detect Detail Indikator and Detail Sub (X: or Y: patterns)
        if (preg_match('/^[xy]\s*:\s*/i', $cellValue)) {
            return 'detail_x_y'; // Return generic pattern, context akan ditentukan di parseSheet
        }
        
        return null;
    }
}