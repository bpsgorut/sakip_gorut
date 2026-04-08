<?php

namespace App\Services;

use App\Models\Fra;
use App\Models\Template_Jenis;
use App\Models\Template_Fra;
use App\Models\Matriks_Fra;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class UnifiedFraParser
{
    private $fra;
    private $debug = true;

    public function __construct(Fra $fra)
    {
        $this->fra = $fra;
    }

    /**
     * Parse Excel dengan deteksi format otomatis
     */
    public function parseExcel($filePath)
    {
        try {
            Log::info('🚀 Starting Unified FRA Parser', ['fra_id' => $this->fra->id, 'file' => basename($filePath)]);

            $spreadsheet = IOFactory::load($filePath);
            $parsedData = [];

            // Analyze dan parse setiap sheet yang relevan
            foreach ($spreadsheet->getSheetNames() as $index => $sheetName) {
                if ($this->isRelevantSheet($sheetName)) {
                    $worksheet = $spreadsheet->getSheet($index);
                    $sheetData = $this->parseSheet($worksheet, $sheetName);
                    $parsedData = array_merge($parsedData, $sheetData);
                }
            }

            if (empty($parsedData)) {
                throw new \Exception('Tidak ada data yang berhasil diparse. File mungkin memiliki format yang tidak didukung.');
            }

            // Simpan ke database
            $this->saveToDatabase($parsedData);

            Log::info('✅ Unified FRA Parser completed', [
                'fra_id' => $this->fra->id,
                'items_parsed' => count($parsedData)
            ]);

            return [
                'success' => true,
                'items_count' => count($parsedData),
                'message' => "Berhasil memparse " . count($parsedData) . " item dari file Excel"
            ];
            
        } catch (\Exception $e) {
            Log::error('❌ Unified FRA Parser error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function isRelevantSheet($sheetName)
    {
        $relevantNames = ['PK IKU', 'IKU', 'FRA_input', 'FRA', 'Data'];
        foreach ($relevantNames as $name) {
            if (stripos($sheetName, $name) !== false) {
                return true;
            }
        }
        return false;
    }

    private function parseSheet($worksheet, $sheetName)
    {
        Log::info("📄 Parsing sheet: {$sheetName}");

        $config = $this->analyzeSheetStructure($worksheet);
        Log::info('🔧 Sheet config:', $config);

        return $this->extractDataWithConfig($worksheet, $sheetName, $config);
    }

    private function analyzeSheetStructure($worksheet)
    {
        $maxCol = $worksheet->getHighestDataColumn();
        $maxRow = $worksheet->getHighestDataRow();
        
        // Find data start row
        $dataStartRow = $this->findDataStartRow($worksheet, $maxCol);

        // Analyze column patterns
        $columnMap = $this->analyzeColumns($worksheet, $maxCol, $dataStartRow);

        // Detect if this is hierarchical or flat structure
        $hasHierarchy = $this->detectHierarchy($worksheet, $maxCol, $dataStartRow);

        return [
            'data_start_row' => $dataStartRow,
            'column_map' => $columnMap,
            'has_hierarchy' => $hasHierarchy,
            'max_row' => $maxRow,
            'max_col' => $maxCol
        ];
    }

    private function findDataStartRow($worksheet, $maxCol)
    {
        for ($row = 1; $row <= 50; $row++) {
            $contentCount = 0;
            $meaningfulContent = false;

            for ($col = 'A'; $col <= $maxCol; $col++) {
                $value = trim($worksheet->getCell($col . $row)->getCalculatedValue());
                if (!empty($value)) {
                    $contentCount++;
                    
                    // Look for meaningful content patterns
                    if (preg_match('/publikasi|data|statistik|indikator|target|realisasi/i', $value) || 
                        strlen($value) > 20) {
                        $meaningfulContent = true;
                    }
                }
            }

            // Need at least 2 non-empty cells and 1 meaningful content
            if ($contentCount >= 2 && $meaningfulContent) {
                Log::info("✅ Data starts at row: {$row}");
                return $row;
            }
        }

        Log::info("⚠️ Using default data start row: 15");
        return 15;
    }

    private function analyzeColumns($worksheet, $maxCol, $startRow)
    {
        $columnMap = [
            'description' => 'E',
            'satuan' => 'I',
            'targets' => [],
            'realisasi' => []
        ];

        // Analyze each column
        for ($col = 'A'; $col <= $maxCol; $col++) {
            $samples = [];
            for ($row = $startRow; $row <= min($startRow + 10, $worksheet->getHighestDataRow()); $row++) {
                $value = trim($worksheet->getCell($col . $row)->getCalculatedValue());
                if (!empty($value) && count($samples) < 5) {
                    $samples[] = $value;
                }
            }

            if (empty($samples)) continue;

            // Classify column based on content
            $satuanScore = 0;
            $descriptionScore = 0;
            $numericScore = 0;

            foreach ($samples as $sample) {
                if (preg_match('/persen|%|unit|orang|publikasi|rilis|dokumen|kegiatan|bulan|hari|kali/i', $sample)) {
                    $satuanScore++;
                } elseif (strlen($sample) > 20 && !is_numeric($sample)) {
                    $descriptionScore++;
                } elseif (is_numeric($sample)) {
                    $numericScore++;
                }
            }

            // Assign column roles
            if ($satuanScore >= 2) {
                $columnMap['satuan'] = $col;
                Log::info("🎯 Satuan column: {$col}");
            } elseif ($descriptionScore >= 2) {
                $columnMap['description'] = $col;
                Log::info("📝 Description column: {$col}");
            }
        }

        return $columnMap;
    }

    private function detectHierarchy($worksheet, $maxCol, $startRow)
    {
        $hierarchyCount = 0;
        
        for ($row = $startRow; $row <= min($startRow + 20, $worksheet->getHighestDataRow()); $row++) {
            for ($col = 'A'; $col <= $maxCol; $col++) {
                $value = trim($worksheet->getCell($col . $row)->getCalculatedValue());
                if (preg_match('/^(T|S|I)\d+|tujuan|sasaran|indikator/i', $value)) {
                    $hierarchyCount++;
                }
            }
        }

        $hasHierarchy = $hierarchyCount >= 3;
        Log::info($hasHierarchy ? "✅ Hierarchy detected" : "⚠️ No clear hierarchy, using flat structure");
        
        return $hasHierarchy;
    }

    private function extractDataWithConfig($worksheet, $sheetName, $config)
    {
        if ($config['has_hierarchy']) {
            return $this->extractHierarchicalData($worksheet, $sheetName, $config);
        } else {
            return $this->extractFlatData($worksheet, $sheetName, $config);
        }
    }

    private function extractHierarchicalData($worksheet, $sheetName, $config)
    {
        $data = [];
        $currentTujuan = '';
        $currentSasaran = '';
        $currentIndikator = '';
        $currentDetailIndikator = '';
        $currentSubIndikator = '';

        $startRow = $config['data_start_row'];
        $endRow = $config['max_row'];
        $descCol = $config['column_map']['description'];
        $satuanCol = $config['column_map']['satuan'];

        for ($row = $startRow; $row <= $endRow; $row++) {
            $rowData = $this->extractRowData($worksheet, $row, $config['max_col']);
            
            if (empty($rowData['primary_content'])) continue;

            // Detect hierarchy level
            $hierarchy = $this->detectRowHierarchy($rowData);
            
            if ($hierarchy['type'] === 'tujuan') {
                $currentTujuan = $hierarchy['text'];
                $currentSasaran = '';
                $currentIndikator = '';
                $currentDetailIndikator = '';
                $currentSubIndikator = '';
                continue;
            } elseif ($hierarchy['type'] === 'sasaran') {
                $currentSasaran = $hierarchy['text'];
                $currentIndikator = '';
                $currentDetailIndikator = '';
                $currentSubIndikator = '';
                continue;
            } elseif ($hierarchy['type'] === 'indikator') {
                $currentIndikator = $hierarchy['text'];
                $currentDetailIndikator = '';
                $currentSubIndikator = '';
            }

            // Extract main content
            $description = $rowData[$descCol] ?? $rowData['primary_content'];
            $satuan = $rowData[$satuanCol] ?? '';

            if (!empty($description) && strlen($description) > 5) {
                // Determine the type of content based on indentation and patterns
                if (preg_match('/^[xy]\.\s+/i', $description)) {
                    // This is a sub-indikator (x. or y.)
                    $currentSubIndikator = $description;
                    $data[] = [
                        'sheet_name' => $sheetName,
                        'tujuan' => $currentTujuan ?: 'Default',
                        'sasaran' => $currentSasaran,
                        'indikator' => $currentIndikator,
                        'detail_indikator' => $currentDetailIndikator,
                        'sub_indikator' => $currentSubIndikator,
                        'detail_sub' => null,
                        'satuan' => $this->cleanSatuan($satuan),
                        'excel_row' => $row
                    ];
                } elseif ($currentSubIndikator && !preg_match('/^[xy]\.\s+/i', $description)) {
                    // This is a detail_sub under a sub-indikator
                    $data[] = [
                        'sheet_name' => $sheetName,
                        'tujuan' => $currentTujuan ?: 'Default',
                        'sasaran' => $currentSasaran,
                        'indikator' => $currentIndikator,
                        'detail_indikator' => $currentDetailIndikator,
                        'sub_indikator' => $currentSubIndikator,
                        'detail_sub' => $description,
                        'satuan' => $this->cleanSatuan($satuan),
                        'excel_row' => $row
                    ];
                } elseif ($currentIndikator && $description !== $currentIndikator) {
                    // This is a detail_indikator
                    $currentDetailIndikator = $description;
                    $data[] = [
                        'sheet_name' => $sheetName,
                        'tujuan' => $currentTujuan ?: 'Default',
                        'sasaran' => $currentSasaran,
                        'indikator' => $currentIndikator,
                        'detail_indikator' => $currentDetailIndikator,
                        'sub_indikator' => null,
                        'detail_sub' => null,
                        'satuan' => $this->cleanSatuan($satuan),
                        'excel_row' => $row
                    ];
                } else {
                    // This is a main indikator
                    $data[] = [
                        'sheet_name' => $sheetName,
                        'tujuan' => $currentTujuan ?: 'Default',
                        'sasaran' => $currentSasaran,
                        'indikator' => $description,
                        'detail_indikator' => null,
                        'sub_indikator' => null,
                        'detail_sub' => null,
                        'satuan' => $this->cleanSatuan($satuan),
                        'excel_row' => $row
                    ];
                }
            }
        }

        return $data;
    }

    private function extractFlatData($worksheet, $sheetName, $config)
    {
        $data = [];
        $startRow = $config['data_start_row'];
        $endRow = $config['max_row'];
        $descCol = $config['column_map']['description'];
        $satuanCol = $config['column_map']['satuan'];

        Log::info("🔍 Extracting flat data from rows {$startRow}-{$endRow}, desc:{$descCol}, satuan:{$satuanCol}");

        for ($row = $startRow; $row <= $endRow; $row++) {
            $description = trim($worksheet->getCell($descCol . $row)->getCalculatedValue());
            $satuan = trim($worksheet->getCell($satuanCol . $row)->getCalculatedValue());

            // Skip empty rows
            if (empty($description) || strlen($description) < 5) {
                continue;
            }

            // Skip instruction rows
            if (preg_match('/^(masukkan|isi|input|contoh|format)/i', $description)) {
                continue;
            }

            Log::info("✅ Row {$row}: \"{$description}\" | Satuan: \"{$satuan}\"");

            $data[] = [
                'sheet_name' => $sheetName,
                'tujuan' => 'Default',
                'sasaran' => null,
                'indikator' => $description,
                'sub_indikator' => null,
                'detail_sub' => null,
                'satuan' => $this->cleanSatuan($satuan),
                'excel_row' => $row
            ];
        }

        Log::info("📊 Extracted " . count($data) . " items from flat structure");
        return $data;
    }

    private function extractRowData($worksheet, $row, $maxCol)
    {
        $data = [];
        $primaryContent = '';

        for ($col = 'A'; $col <= $maxCol; $col++) {
            $value = trim($worksheet->getCell($col . $row)->getCalculatedValue());
            if (!empty($value)) {
                $data[$col] = $value;
                if (strlen($value) > strlen($primaryContent)) {
                    $primaryContent = $value;
                }
            }
        }

        $data['primary_content'] = $primaryContent;
        return $data;
    }

    private function detectRowHierarchy($rowData)
    {
        $analysis = ['type' => 'data', 'text' => $rowData['primary_content'], 'confidence' => 0];

        foreach ($rowData as $col => $value) {
            if (preg_match('/^T(\d+)[\s\.:]*(.*)$/i', $value, $matches)) {
                return ['type' => 'tujuan', 'text' => trim($matches[2] ?: $value), 'confidence' => 95];
            } elseif (preg_match('/^S(\d+)[\s\.:]*(.*)$/i', $value, $matches)) {
                return ['type' => 'sasaran', 'text' => trim($matches[2] ?: $value), 'confidence' => 95];
            } elseif (preg_match('/^I(\d+)[\s\.:]*(.*)$/i', $value, $matches)) {
                return ['type' => 'indikator', 'text' => trim($matches[2] ?: $value), 'confidence' => 95];
            }
        }

        return $analysis;
    }

    private function cleanSatuan($satuan)
    {
        $satuan = trim($satuan);
        
        // Default satuan jika kosong
        if (empty($satuan)) {
            return 'Unit';
        }

        // Clean common issues
        $satuan = str_replace(['(', ')', '"', "'"], '', $satuan);
        $satuan = trim($satuan);

        return $satuan ?: 'Unit';
    }

    /**
     * Parse dan pisahkan konten X dan Y jika ada
     */
    private function parseAndSeparateXY($content)
    {
        // Pola untuk mendeteksi X: ... Y: ... atau X: ..., Y: ...
        $pattern = '/^(.*)X\s*:\s*([^,]*(?:,\s*[^YX]*)*)\s*[,;]?\s*Y\s*:\s*(.*)$/i';
        
        if (preg_match($pattern, $content, $matches)) {
            $prefix = trim($matches[1]);
            $xContent = trim($matches[2]);
            $yContent = trim($matches[3]);
            
            // Bersihkan konten X dan Y dari karakter yang tidak perlu
            $xContent = rtrim($xContent, ',;');
            $yContent = rtrim($yContent, ',;');
            
            return [
                'has_xy' => true,
                'prefix' => $prefix,
                'x_content' => $xContent,
                'y_content' => $yContent
            ];
        }
        
        return [
            'has_xy' => false,
            'content' => $content
        ];
    }

    /**
     * Pisahkan item yang mengandung X dan Y menjadi dua item terpisah
     */
    private function separateXYItems($parsedData)
    {
        $separatedData = [];
        
        foreach ($parsedData as $item) {
            $needsSeparation = false;
            $xyFields = ['detail_indikator', 'sub_indikator', 'detail_sub'];
            
            foreach ($xyFields as $field) {
                if (!empty($item[$field])) {
                    $parsed = $this->parseAndSeparateXY($item[$field]);
                    
                    if ($parsed['has_xy']) {
                        $needsSeparation = true;
                        
                        // Buat item X
                        $itemX = $item;
                        $itemX[$field] = 'X: ' . $parsed['x_content'];
                        $itemX['xy_type'] = 'X';
                        $separatedData[] = $itemX;
                        
                        // Buat item Y
                        $itemY = $item;
                        $itemY[$field] = 'Y: ' . $parsed['y_content'];
                        $itemY['xy_type'] = 'Y';
                        $separatedData[] = $itemY;
                        
                        break; // Hanya proses satu field per item
                    }
                }
            }
            
            // Jika tidak ada X dan Y, tambahkan item asli
            if (!$needsSeparation) {
                $separatedData[] = $item;
            }
        }
        
        return $separatedData;
    }

    private function saveToDatabase($parsedData)
    {
        if (empty($parsedData)) {
            throw new \Exception('Tidak ada data untuk disimpan ke database');
        }

        // 1. Expand hierarchies and handle X/Y separation in one go
        $finalData = $this->expandAndFinalizeData($parsedData);

        // 2. Save all entries to the database
        $templateFraCache = [];
        Log::info('💾 Starting database save operation...', ['items_to_save' => count($finalData)]);

        foreach ($finalData as $item) {
            $sheetName = $item['sheet_name'];
            
            if (!isset($templateFraCache[$sheetName])) {
                $templateFraCache[$sheetName] = $this->getOrCreateTemplateFra($sheetName);
            }
            $templateFra = $templateFraCache[$sheetName];

            // Build the condition for finding an existing record
            $searchConditions = [
                'template_fra_id' => $templateFra->id,
                'tujuan' => $item['tujuan'],
                'sasaran' => $item['sasaran'],
                'indikator' => $item['indikator'],
                'detail_indikator' => $item['detail_indikator'],
                'sub_indikator' => $item['sub_indikator'],
                'detail_sub' => $item['detail_sub'],
            ];

            // Data to be inserted or updated
            $dataToInsert = array_merge($searchConditions, [
                'satuan' => $item['satuan'],
                'excel_row' => $item['excel_row'],
            ]);
            
            Matriks_Fra::updateOrCreate($searchConditions, $dataToInsert);
        }

        Log::info('✅ Database save operation completed', ['items_saved' => count($finalData)]);
    }

    /**
     * Expands raw parsed data into a full list of hierarchical entries,
     * correctly handling X/Y separation and parent creation.
     */
    private function expandAndFinalizeData($parsedData)
    {
        $allEntries = [];
        $uniqueTracker = [];

        foreach ($parsedData as $item) {
            $xyParsedDetail = !empty($item['detail_indikator']) ? $this->parseAndSeparateXY($item['detail_indikator']) : ['has_xy' => false];
            $xyParsedSub = !empty($item['sub_indikator']) ? $this->parseAndSeparateXY($item['sub_indikator']) : ['has_xy' => false];

            // Case 1: X/Y found in `detail_indikator`
            if ($xyParsedDetail['has_xy']) {
                $this->addUniqueEntry($allEntries, $uniqueTracker, $item, ['detail_indikator' => null, 'sub_indikator' => null, 'detail_sub' => null]);
                $this->addUniqueEntry($allEntries, $uniqueTracker, $item, ['detail_indikator' => 'X: ' . $xyParsedDetail['x_content'], 'sub_indikator' => null, 'detail_sub' => null]);
                $this->addUniqueEntry($allEntries, $uniqueTracker, $item, ['detail_indikator' => 'Y: ' . $xyParsedDetail['y_content'], 'sub_indikator' => null, 'detail_sub' => null]);
            
            // Case 2: X/Y found in `sub_indikator`
            } elseif ($xyParsedSub['has_xy']) {
                $this->addUniqueEntry($allEntries, $uniqueTracker, $item, ['detail_indikator' => null, 'sub_indikator' => null, 'detail_sub' => null]);
                $this->addUniqueEntry($allEntries, $uniqueTracker, $item, ['sub_indikator' => $xyParsedSub['prefix'], 'detail_sub' => null]);
                $this->addUniqueEntry($allEntries, $uniqueTracker, $item, ['sub_indikator' => $xyParsedSub['prefix'], 'detail_sub' => 'X: ' . $xyParsedSub['x_content']]);
                $this->addUniqueEntry($allEntries, $uniqueTracker, $item, ['sub_indikator' => $xyParsedSub['prefix'], 'detail_sub' => 'Y: ' . $xyParsedSub['y_content']]);
            
            // Case 3: No X/Y, generate standard hierarchy
            } else {
                if (!empty($item['indikator'])) {
                    $this->addUniqueEntry($allEntries, $uniqueTracker, $item, ['detail_indikator' => null, 'sub_indikator' => null, 'detail_sub' => null]);
                }
                if (!empty($item['detail_indikator'])) {
                    $this->addUniqueEntry($allEntries, $uniqueTracker, $item, ['sub_indikator' => null, 'detail_sub' => null]);
                }
                if (!empty($item['sub_indikator'])) {
                     $this->addUniqueEntry($allEntries, $uniqueTracker, $item, ['detail_indikator' => $item['detail_indikator'], 'detail_sub' => null]);
                }
                if (!empty($item['detail_sub'])) {
                    $this->addUniqueEntry($allEntries, $uniqueTracker, $item, []); // Add the full item
                }
            }
        }
        
        return $allEntries;
    }

    /**
     * Helper to add a unique entry to the list.
     * Overrides specific fields for creating parent entries.
     */
    private function addUniqueEntry(&$allEntries, &$uniqueTracker, $originalItem, $overrides)
    {
        $entry = array_merge($originalItem, $overrides);
        
        $keyData = [
            'tujuan' => $entry['tujuan'] ?? null,
            'sasaran' => $entry['sasaran'] ?? null,
            'indikator' => $entry['indikator'] ?? null,
            'detail_indikator' => $entry['detail_indikator'] ?? null,
            'sub_indikator' => $entry['sub_indikator'] ?? null,
            'detail_sub' => $entry['detail_sub'] ?? null,
        ];
        
        $key = md5(implode('|', array_map('strval', $keyData)));

        if (!isset($uniqueTracker[$key])) {
            $allEntries[] = [
                'sheet_name' => $entry['sheet_name'],
                'tujuan' => $keyData['tujuan'],
                'sasaran' => $keyData['sasaran'],
                'indikator' => $keyData['indikator'],
                'detail_indikator' => $keyData['detail_indikator'],
                'sub_indikator' => $keyData['sub_indikator'],
                'detail_sub' => $keyData['detail_sub'],
                'satuan' => $entry['satuan'] ?? 'Unit',
                'excel_row' => $entry['excel_row'] ?? null,
            ];
            $uniqueTracker[$key] = true;
        }
    }

    private function getOrCreateTemplateFra($sheetName)
    {
        // Determine template type based on sheet name
        $templateTypeName = 'PK IKU'; // Default
        
        if (stripos($sheetName, 'suplemen') !== false) {
            $templateTypeName = 'PK Suplemen';
        } elseif (stripos($sheetName, 'umum') !== false) {
            $templateTypeName = 'Umum';
        }

        $templateJenis = Template_Jenis::firstOrCreate(
            ['nama' => $templateTypeName],
            ['wajib' => $templateTypeName === 'PK IKU']
        );

        $templateFra = Template_Fra::firstOrCreate([
            'fra_id' => $this->fra->id,
            'template_jenis_id' => $templateJenis->id
        ]);

        return $templateFra;
    }
} 