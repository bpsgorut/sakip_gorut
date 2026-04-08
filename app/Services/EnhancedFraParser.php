<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use App\Models\MatriksFra;

class EnhancedFraParser
{
    private $fra;
    private $spreadsheet;
    private $hierarchyMap = [];
    private $cellMappings = [];

    public function __construct($fra)
    {
        $this->fra = $fra;
    }

    /**
     * Parse Excel dengan smart detection dan cell mapping
     */
    public function parseWithSmartDetection($filePath)
    {
        try {
            Log::info("Starting enhanced FRA parsing", ['fra_id' => $this->fra->id, 'file' => basename($filePath)]);

            $this->spreadsheet = IOFactory::load($filePath);
            
            // Parse dengan smart detection
            $parsedData = $this->processAllSheets();

            // Save parsed data ke database
            $this->saveToDatabase($parsedData);

            Log::info("Enhanced FRA parsing completed", ['fra_id' => $this->fra->id, 'items_count' => count($parsedData)]);

            return [
                'success' => true,
                'items_count' => count($parsedData),
                'hierarchy_map' => $this->hierarchyMap,
                'cell_mappings' => $this->cellMappings
            ];

        } catch (\Exception $e) {
            Log::error("Enhanced FRA parsing error: " . $e->getMessage());
            throw $e;
        }
    }

    private function processAllSheets()
    {
        $allParsedData = [];
        $sheetNames = ['PK IKU', 'FRA_input'];

        foreach ($sheetNames as $sheetName) {
            if (!$this->spreadsheet->getSheetByName($sheetName)) {
                Log::warning("Sheet tidak ditemukan: $sheetName");
                continue;
            }

            $worksheet = $this->spreadsheet->getSheetByName($sheetName);
            $sheetData = $this->processSheet($worksheet, $sheetName);
            $allParsedData = array_merge($allParsedData, $sheetData);
        }

        return $allParsedData;
    }

    private function processSheet($worksheet, $sheetName)
    {
        Log::info("Processing sheet: $sheetName");

        $hierarchy = $this->detectSmartHierarchy($worksheet);
        $parsedData = [];
        
        $currentTujuan = '';
        $currentSasaran = '';  
        $currentIndikator = '';
        $currentSubIndikator = '';

        foreach ($hierarchy as $item) {
            if ($item['confidence'] < 70) continue;

            // Track hierarchy context
            if ($item['type'] === 'tujuan') {
                $currentTujuan = $item['code'];
                $currentSasaran = '';
                $currentIndikator = '';
                $currentSubIndikator = '';
            } elseif ($item['type'] === 'sasaran') {
                $currentSasaran = $item['code'];
                $currentIndikator = '';
                $currentSubIndikator = '';
            } elseif ($item['type'] === 'indikator') {
                $currentIndikator = $item['code'];
                $currentSubIndikator = '';
            } elseif ($item['type'] === 'sub_indikator') {
                $currentSubIndikator = $item['code'];
            }

            // Build data untuk sub_indikator dan detail_sub
            if (in_array($item['type'], ['sub_indikator', 'detail_sub'])) {
                $matriksData = $this->buildMatriksData($item, $currentTujuan, $currentSasaran, $currentIndikator, $currentSubIndikator, $worksheet, $sheetName);
                
                if ($matriksData) {
                    $parsedData[] = $matriksData;
                }
            }
        }

        return $parsedData;
    }

    private function detectSmartHierarchy($worksheet)
    {
        $hierarchy = [];
        $maxRow = min($worksheet->getHighestRow(), 500);
        
        for ($row = 1; $row <= $maxRow; $row++) {
            $analysis = $this->analyzeRowForHierarchy($worksheet, $row);
            
            if ($analysis['level'] > 0) {
                $hierarchy[] = $analysis;
            }
        }
        
        return $hierarchy;
    }

    private function analyzeRowForHierarchy($worksheet, $row)
    {
        $data = [
            'row' => $row,
            'level' => 0,
            'type' => 'unknown',
            'code' => '',
            'text' => '',
            'confidence' => 0
        ];

        // Check kolom A sampai E untuk content
        $content = [];
        for ($col = 'A'; $col <= 'F'; $col++) {
            $cellValue = trim((string)$worksheet->getCell($col . $row)->getCalculatedValue());
            if (!empty($cellValue)) {
                $content[$col] = $cellValue;
            }
        }

        if (empty($content)) {
            return $data;
        }

        // SMART DETECTION LOGIC
        foreach ($content as $col => $value) {
            $detection = $this->smartDetectContent($value, $col, $row);
            
            if ($detection['confidence'] > $data['confidence']) {
                $data = array_merge($data, $detection);
                $data['row'] = $row;
            }
        }

        return $data;
    }

    private function smartDetectContent($value, $col, $row)
    {
        $data = [
            'level' => 0,
            'type' => 'unknown', 
            'code' => '',
            'text' => $value,
            'confidence' => 0,
            'column' => $col
        ];

        // 1. TUJUAN DETECTION
        if (preg_match('/^T(\d+)[\s\.:]*(.*)$/i', $value, $matches)) {
            return [
                'level' => 1,
                'type' => 'tujuan',
                'code' => 'T' . $matches[1],
                'text' => trim($matches[2] ?? $value),
                'confidence' => 95,
                'column' => $col
            ];
        }

        if (preg_match('/^Tujuan\s+(\d+)[\s\.:]*(.*)$/i', $value, $matches)) {
            return [
                'level' => 1,
                'type' => 'tujuan',
                'code' => 'T' . $matches[1],
                'text' => trim($matches[2] ?? $value),
                'confidence' => 90,
                'column' => $col
            ];
        }

        // 2. SASARAN DETECTION
        if (preg_match('/^S(\d+)[\s\.:]*(.*)$/i', $value, $matches)) {
            return [
                'level' => 2,
                'type' => 'sasaran',
                'code' => 'S' . $matches[1],
                'text' => trim($matches[2] ?? $value),
                'confidence' => 95,
                'column' => $col
            ];
        }

        if (preg_match('/^Sasaran\s+(\d+)[\s\.:]*(.*)$/i', $value, $matches)) {
            return [
                'level' => 2,
                'type' => 'sasaran',
                'code' => 'S' . $matches[1],
                'text' => trim($matches[2] ?? $value),
                'confidence' => 90,
                'column' => $col
            ];
        }

        // 3. INDIKATOR DETECTION
        if (preg_match('/^I(\d+)[\s\.:]*(.*)$/i', $value, $matches)) {
            return [
                'level' => 3,
                'type' => 'indikator',
                'code' => 'I' . $matches[1],
                'text' => trim($matches[2] ?? $value),
                'confidence' => 95,
                'column' => $col
            ];
        }

        if (preg_match('/^Indikator\s+(\d+)[\s\.:]*(.*)$/i, $value, $matches)) {
            return [
                'level' => 3,
                'type' => 'indikator',
                'code' => 'I' . $matches[1],
                'text' => trim($matches[2] ?? $value),
                'confidence' => 90,
                'column' => $col
            ];
        }

        // 4. SUB INDIKATOR DETECTION
        if (preg_match('/^(\d+\.?\d*)[\s\.:]*(.*)$/', $value, $matches)) {
            $indentLevel = $this->calculateIndentLevel($col);
            
            if ($indentLevel >= 3) {
                return [
                    'level' => 4,
                    'type' => 'sub_indikator',
                    'code' => trim($matches[1], '.'),
                    'text' => trim($matches[2] ?? $value),
                    'confidence' => 80 + ($indentLevel * 5),
                    'column' => $col
                ];
            }
        }

        // 5. DETAIL SUB DETECTION
        if (preg_match('/^([a-z]\.|[ivx]+\.|[\(\[]?\d+[\)\]]?)[\s\.:]*(.*)$/i', $value, $matches)) {
            $indentLevel = $this->calculateIndentLevel($col);
            
            if ($indentLevel >= 4) {
                return [
                    'level' => 5,
                    'type' => 'detail_sub',
                    'code' => trim($matches[1], '.()[]'),
                    'text' => trim($matches[2] ?? $value),
                    'confidence' => 70 + ($indentLevel * 5),
                    'column' => $col
                ];
            }
        }

        return $data;
    }

    private function calculateIndentLevel($col)
    {
        $levels = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6];
        return $levels[$col] ?? 7;
    }

    private function buildMatriksData($item, $tujuan, $sasaran, $indikator, $subIndikator, $worksheet, $sheetName)
    {
        // Extract informasi tambahan dari row
        $row = $item['row'];
        $additionalData = $this->extractRowData($worksheet, $row);

        // Detect cell positions untuk target dan realisasi (untuk Google Drive)
        $cellPositions = $this->detectCellPositions($worksheet, $row, $sheetName);
        
        // Store cell mappings untuk Google Drive integration
        $this->cellMappings[] = [
            'fra_id' => $this->fra->id,
            'row' => $row,
            'sheet' => $sheetName,
            'positions' => $cellPositions
        ];

        // Get or create template_fra yang sesuai dengan jenis
        $jenis = $this->detectJenis($sheetName, $item);
        $templateFra = $this->getOrCreateTemplateFra($jenis);

        return [
            'template_fra_id' => $templateFra->id,
            'tujuan' => $tujuan,
            'sasaran' => $sasaran ?: null,
            'indikator' => $indikator,
            'sub_indikator' => $item['type'] === 'sub_indikator' ? $item['text'] : $subIndikator,
            'detail_sub' => $item['type'] === 'detail_sub' ? $item['text'] : null,
            'satuan' => $additionalData['satuan'] ?? '',
            'excel_row' => $row
        ];
    }

    private function extractRowData($worksheet, $row)
    {
        $data = [];
        
        // Check kolom untuk satuan (biasanya kolom F-H)
        for ($col = 'F'; $col <= 'J'; $col++) {
            $value = trim((string)$worksheet->getCell($col . $row)->getCalculatedValue());
            if (!empty($value) && !is_numeric($value)) {
                // Detect satuan patterns
                if (preg_match('/(persen|%|unit|orang|dokumen|kegiatan|bulan|hari|kali)/i', $value)) {
                    $data['satuan'] = $value;
                    break;
                }
            }
        }

        return $data;
    }

    private function detectCellPositions($worksheet, $row, $sheetName)
    {
        $positions = [
            'sheet' => $sheetName,
            'target' => [],
            'realisasi' => [],
            'kendala' => null
        ];

        // Detect column headers untuk target/realisasi
        $headerRow = $this->findHeaderRow($worksheet);
        
        if ($headerRow) {
            // Map kolom berdasarkan header
            for ($col = 'H'; $col <= 'Z'; $col++) {
                $header = trim((string)$worksheet->getCell($col . $headerRow)->getCalculatedValue());
                
                // Target detection
                if (preg_match('/target.*tw.*(\d+)/i', $header, $matches)) {
                    $positions['target'][$matches[1]] = $col . $row;
                } elseif (preg_match('/target.*(\d+)/i', $header, $matches)) {
                    $positions['target'][$matches[1]] = $col . $row;
                }
                
                // Realisasi detection
                if (preg_match('/realisasi.*tw.*(\d+)/i', $header, $matches)) {
                    $positions['realisasi'][$matches[1]] = $col . $row;
                } elseif (preg_match('/realisasi.*(\d+)/i', $header, $matches)) {
                    $positions['realisasi'][$matches[1]] = $col . $row;
                }

                // Kendala/solusi detection
                if (preg_match('/(kendala|solusi|tindak)/i', $header)) {
                    $positions['kendala'] = $col . $row;
                }
            }
        }

        return $positions;
    }

    private function findHeaderRow($worksheet)
    {
        // Look for header row (biasanya ada "target", "realisasi")
        for ($row = 1; $row <= 20; $row++) {
            $rowContent = '';
            for ($col = 'A'; $col <= 'Z'; $col++) {
                $rowContent .= strtolower($worksheet->getCell($col . $row)->getCalculatedValue()) . ' ';
            }
            
            if (strpos($rowContent, 'target') !== false && strpos($rowContent, 'realisasi') !== false) {
                return $row;
            }
        }
        
        return null;
    }

    private function detectJenis($sheetName, $item)
    {
        if ($sheetName === 'PK IKU') {
            if (stripos($item['text'], 'iku') !== false) {
                return 'iku';
            }
            return 'umum';
        }
        
        return 'suplemen';
    }

    private function buildUniqueCode($tujuan, $sasaran, $indikator, $subIndikator, $item)
    {
        $parts = array_filter([
            $tujuan,
            $sasaran,
            $indikator,
            $item['type'] === 'sub_indikator' ? $item['code'] : $subIndikator,
            $item['type'] === 'detail_sub' ? $item['code'] : null
        ]);
        
        return implode('.', $parts);
    }

    private function getOrCreateTemplateFra($jenis)
    {
        // Get or create template jenis
        $templateJenis = \App\Models\Template_Jenis::firstOrCreate(
            ['nama' => $jenis === 'iku' ? 'PK IKU' : ($jenis === 'suplemen' ? 'PK Suplemen' : 'Umum')],
            ['wajib' => $jenis === 'iku']
        );

        // Get or create template fra
        $templateFra = \App\Models\Template_Fra::firstOrCreate([
            'fra_id' => $this->fra->id,
            'template_jenis_id' => $templateJenis->id
        ]);

        return $templateFra;
    }

    private function saveToDatabase($parsedData)
    {
        foreach ($parsedData as $data) {
            try {
                // Check for existing record berdasarkan template_fra_id dan komponen lainnya
                $existing = \App\Models\Matriks_Fra::where('template_fra_id', $data['template_fra_id'])
                    ->where('tujuan', $data['tujuan'])
                    ->where('indikator', $data['indikator'])
                    ->where('sub_indikator', $data['sub_indikator'])
                    ->where('detail_sub', $data['detail_sub'])
                    ->first();

                if ($existing) {
                    $existing->update($data);
                } else {
                    \App\Models\Matriks_Fra::create($data);
                }

            } catch (\Exception $e) {
                Log::error("Error saving matriks data: " . $e->getMessage(), $data);
            }
        }
    }
} 