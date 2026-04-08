<?php

namespace App\Http\Controllers;

use App\Models\Fra;
use App\Models\Template_Jenis;
use App\Models\Template_Fra;
use App\Models\Matriks_Fra;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class UnifiedFraParser
{
    /**
     * Parse Excel dengan deteksi format otomatis
     */
    public function parseExcel(Fra $fra, string $filePath)
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheetNames = $spreadsheet->getSheetNames();
            
            // Deteksi format file
            $format = $this->detectFormat($sheetNames);
            Log::info("Format terdeteksi: {$format}");
            
            // Process sheet PK IKU (wajib ada)
            $pkIkuSheet = $this->findSheet($spreadsheet, ['PK IKU', 'pk iku']);
            if (!$pkIkuSheet) {
                throw new \Exception('Sheet PK IKU tidak ditemukan. Sheet ini wajib ada.');
            }
            
            $this->processSheet($fra, $pkIkuSheet, 'PK IKU', $format);
            
            // Process sheet IKU Suplemen jika ada
            $suplemenSheet = $this->findSheet($spreadsheet, ['IKU Suplemen', 'iku suplemen', 'PK Suplemen', 'pk suplemen']);
            if ($suplemenSheet) {
                $this->processSheet($fra, $suplemenSheet, 'IKU Suplemen', $format);
            }
            
            // Process sheet Umum jika ada (untuk format Form Rencana Aksi)
            if ($format === 'form_rencana_aksi') {
                $this->processUmumSection($fra, $pkIkuSheet);
            }
            
            return ['success' => true, 'message' => 'Excel berhasil diproses'];
            
        } catch (\Exception $e) {
            Log::error('Excel parsing error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Deteksi format file berdasarkan sheet names dan struktur
     */
    private function detectFormat(array $sheetNames): string
    {
        // Form Rencana Aksi memiliki sheet khusus seperti 'Panduan Pengisian'
        $formRencanaAksiIndicators = [
            'Panduan Pengisian',
            'Penjelasan Indikator',
            'Petunjuk Pengisian'
        ];
        
        foreach ($formRencanaAksiIndicators as $indicator) {
            if (in_array($indicator, $sheetNames)) {
                return 'form_rencana_aksi';
            }
        }
        
        // Jika ada kombinasi PK IKU dan IKU Suplemen tanpa sheet standar
        if (in_array('PK IKU', $sheetNames) && in_array('IKU Suplemen', $sheetNames)) {
            return 'form_rencana_aksi';
        }
        
        return 'standard_template';
    }
    
    /**
     * Cari sheet berdasarkan nama (case insensitive)
     */
    private function findSheet($spreadsheet, array $possibleNames)
    {
        foreach ($possibleNames as $name) {
            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                if (strcasecmp(trim($sheetName), trim($name)) === 0) {
                    return $spreadsheet->getSheetByName($sheetName);
                }
            }
        }
        return null;
    }
    
    /**
     * Process sheet dengan format yang terdeteksi
     */
    private function processSheet(Fra $fra, $worksheet, string $sheetType, string $format)
    {
        // Buat template jenis
        $templateJenis = Template_Jenis::firstOrCreate(
            ['nama' => $sheetType],
            ['wajib' => $sheetType === 'PK IKU']
        );
        
        $templateFra = Template_Fra::create([
            'fra_id' => $fra->id,
            'template_jenis_id' => $templateJenis->id
        ]);
        
        if ($format === 'form_rencana_aksi') {
            return $this->parseFormRencanaAksi($fra, $worksheet, $templateFra, $sheetType);
        } else {
            return $this->parseStandardTemplate($fra, $worksheet, $templateFra);
        }
    }
    
    /**
     * Parse format Form Rencana Aksi (dengan merged cells)
     */
    private function parseFormRencanaAksi(Fra $fra, $worksheet, $templateFra, string $sheetType)
    {
        $currentTujuan = null;
        $currentSasaran = null;
        $currentIndikator = null;
        $currentSubIndikator = null;
        $currentSubKode = null;
        $currentSubIndikatorId = null;
        $rowsProcessed = 0;
        
        // Apakah sheet ini mendukung detail sub
        $supportsDetailSub = in_array(strtolower($sheetType), ['pk iku', 'iku suplemen']);
        
        $maxRow = $worksheet->getHighestDataRow();
        
        for ($row = 1; $row <= $maxRow; $row++) {
            $data = $this->readRowData($worksheet, $row);
            
            // Skip baris kosong
            if ($this->isEmptyRow($data)) {
                continue;
            }
            
            // Skip baris Umum (akan diproses terpisah)
            if ($this->isUmumSection($data)) {
                continue;
            }
            
            // Deteksi Tujuan
            if ($this->isTujuan($data)) {
                $currentTujuan = $this->extractTujuan($data);
                $this->resetSubContexts($currentSubIndikator, $currentSubKode, $currentSubIndikatorId);
                Log::info("Tujuan: {$currentTujuan}");
                continue;
            }
            
            // Deteksi Sasaran
            if ($this->isSasaran($data)) {
                $currentSasaran = $this->extractSasaran($data);
                $this->resetSubContexts($currentSubIndikator, $currentSubKode, $currentSubIndikatorId);
                Log::info("Sasaran: {$currentSasaran}");
                continue;
            }
            
            // Deteksi Indikator
            if ($this->isIndikator($data)) {
                $currentIndikator = $this->extractIndikator($data);
                $this->resetSubContexts($currentSubIndikator, $currentSubKode, $currentSubIndikatorId);
                
                // Simpan indikator
                Matriks_Fra::create([
                    'template_fra_id' => $templateFra->id,
                    'tujuan' => $currentTujuan,
                    'sasaran' => $currentSasaran,
                    'indikator' => $currentIndikator,
                    'sub_indikator' => null,
                    'detail_sub' => null,
                    'parent_sub_id' => null,
                    'sub_kode' => null,
                    'satuan' => $this->extractAndValidateSatuan($data, 'indikator'),
                    'excel_row' => $row
                ]);
                
                $rowsProcessed++;
                Log::info("Indikator: {$currentIndikator}");
                continue;
            }
            
            // Deteksi Sub Indikator
            if ($this->isSubIndikator($data, $currentIndikator)) {
                $result = $this->extractSubIndikator($data);
                $subIndikatorText = $result['text'];
                $subKode = $result['code'];
                
                // Prevent duplicate codes in text
                if ($subKode && str_starts_with($subIndikatorText, $subKode . '. ' . $subKode)) {
                    $subIndikatorText = $subKode . '. ' . substr($subIndikatorText, strlen($subKode . '. ' . $subKode));
                }
                
                // Simpan sub indikator
                $subRecord = Matriks_Fra::create([
                    'template_fra_id' => $templateFra->id,
                    'tujuan' => $currentTujuan,
                    'sasaran' => $currentSasaran,
                    'indikator' => $currentIndikator,
                    'sub_indikator' => $subIndikatorText,
                    'detail_sub' => null,
                    'parent_sub_id' => null,
                    'sub_kode' => $subKode,
                    'satuan' => $this->extractAndValidateSatuan($data, 'sub_indikator'),
                    'excel_row' => $row
                ]);
                
                $currentSubIndikator = $subIndikatorText;
                $currentSubKode = $subKode;
                $currentSubIndikatorId = $subRecord->id;
                $rowsProcessed++;
                Log::info("Sub Indikator: {$subIndikatorText} (kode: {$subKode})");
                continue;
            }
            
            // Deteksi Detail Sub (hanya untuk IKU dan Suplemen)
            if ($supportsDetailSub && $this->isDetailSub($data, $currentSubKode)) {
                $detailSubText = $this->extractDetailSub($data);
                
                // Simpan detail sub
                Matriks_Fra::create([
                    'template_fra_id' => $templateFra->id,
                    'tujuan' => $currentTujuan,
                    'sasaran' => $currentSasaran,
                    'indikator' => $currentIndikator,
                    'sub_indikator' => $currentSubIndikator,
                    'detail_sub' => $detailSubText,
                    'parent_sub_id' => $currentSubIndikatorId,
                    'sub_kode' => null,
                    'satuan' => $this->extractAndValidateSatuan($data, 'detail_sub'),
                    'excel_row' => $row
                ]);
                
                $rowsProcessed++;
                Log::info("Detail Sub: {$detailSubText}");
                continue;
            }
        }
        
        if ($rowsProcessed == 0) {
            throw new \Exception("Tidak ada data yang berhasil diproses dari sheet {$sheetType}");
        }
        
        Log::info("Berhasil memproses {$rowsProcessed} data dari sheet {$sheetType}");
        return $rowsProcessed;
    }
    
    /**
     * Parse format Standard Template
     */
    private function parseStandardTemplate(Fra $fra, $worksheet, $templateFra)
    {
        $currentTujuan = null;
        $currentSasaran = null;
        $currentIndikator = null;
        $currentSubIndikator = null;
        $rowsProcessed = 0;
        
        $maxRow = $worksheet->getHighestDataRow();
        
        // Mulai dari baris 3 (skip header di baris 1-2)
        for ($row = 3; $row <= $maxRow; $row++) {
            $tipe = trim($worksheet->getCell("A{$row}")->getValue() ?? '');
            $kode = trim($worksheet->getCell("B{$row}")->getValue() ?? '');
            $deskripsi = trim($worksheet->getCell("C{$row}")->getValue() ?? '');
            $satuan = trim($worksheet->getCell("D{$row}")->getValue() ?? '');
            
            // Skip baris kosong
            if (empty($tipe) && empty($deskripsi)) {
                continue;
            }
            
            switch (strtolower($tipe)) {
                case 'tujuan':
                    $currentTujuan = "Tujuan {$kode}. {$deskripsi}";
                    break;
                    
                case 'sasaran':
                    if ($currentTujuan) {
                        $currentSasaran = "{$kode} {$deskripsi}";
                    }
                    break;
                    
                case 'indikator':
                    if ($currentTujuan && $currentSasaran) {
                        $currentIndikator = "{$kode} {$deskripsi}";
                        $currentSubIndikator = null;
                        
                        Matriks_Fra::create([
                            'template_fra_id' => $templateFra->id,
                            'tujuan' => $currentTujuan,
                            'sasaran' => $currentSasaran,
                            'indikator' => $currentIndikator,
                            'sub_indikator' => null,
                            'detail_sub' => null,
                            'satuan' => $this->validateSatuan($satuan),
                            'excel_row' => $row
                        ]);
                        $rowsProcessed++;
                    }
                    break;
                    
                case 'sub_indikator':
                    if ($currentTujuan && $currentSasaran && $currentIndikator) {
                        if (!empty($kode)) {
                            $currentSubIndikator = "{$kode}. {$deskripsi}";
                        } else {
                            $currentSubIndikator = $deskripsi;
                        }
                        
                        Matriks_Fra::create([
                            'template_fra_id' => $templateFra->id,
                            'tujuan' => $currentTujuan,
                            'sasaran' => $currentSasaran,
                            'indikator' => $currentIndikator,
                            'sub_indikator' => $currentSubIndikator,
                            'detail_sub' => null,
                            'satuan' => $this->validateSatuan($satuan),
                            'excel_row' => $row
                        ]);
                        $rowsProcessed++;
                    }
                    break;
                    
                case 'detail_sub':
                    if ($currentTujuan && $currentSasaran && $currentIndikator && $currentSubIndikator) {
                        Matriks_Fra::create([
                            'template_fra_id' => $templateFra->id,
                            'tujuan' => $currentTujuan,
                            'sasaran' => $currentSasaran,
                            'indikator' => $currentIndikator,
                            'sub_indikator' => $currentSubIndikator,
                            'detail_sub' => $deskripsi,
                            'satuan' => $this->validateSatuan($satuan),
                            'excel_row' => $row
                        ]);
                        $rowsProcessed++;
                    }
                    break;
            }
        }
        
        return $rowsProcessed;
    }
    
    /**
     * Process bagian Umum dari Form Rencana Aksi
     */
    private function processUmumSection(Fra $fra, $worksheet)
    {
        $umumTemplateJenis = Template_Jenis::firstOrCreate(
            ['nama' => 'Umum'],
            ['wajib' => false]
        );
        
        $umumTemplateFra = Template_Fra::firstOrCreate([
            'fra_id' => $fra->id,
            'template_jenis_id' => $umumTemplateJenis->id
        ]);
        
        $maxRow = $worksheet->getHighestDataRow();
        $inUmumSection = false;
        $currentIndikator = null;
        $rowsProcessed = 0;
        
        for ($row = 1; $row <= $maxRow; $row++) {
            $data = $this->readRowData($worksheet, $row);
            
            // Deteksi mulai bagian umum
            if ($this->isUmumSection($data)) {
                $inUmumSection = true;
                continue;
            }
            
            // Jika keluar dari bagian umum (ada tujuan baru)
            if ($inUmumSection && $this->isTujuan($data)) {
                break;
            }
            
            if (!$inUmumSection) {
                continue;
            }
            
            // Skip baris instruksi
            if ($this->isInstructionRow($data)) {
                continue;
            }
            
            // Process indikator umum
            if ($this->isUmumIndikator($data)) {
                $indikatorText = $this->extractUmumIndikator($data);
                $currentIndikator = $indikatorText;
                
                Matriks_Fra::create([
                    'template_fra_id' => $umumTemplateFra->id,
                    'tujuan' => 'Umum',
                    'sasaran' => null,
                    'indikator' => $indikatorText,
                    'sub_indikator' => null,
                    'detail_sub' => null,
                    'satuan' => $this->extractAndValidateSatuan($data, 'umum_indikator'),
                    'excel_row' => $row
                ]);
                
                $rowsProcessed++;
                continue;
            }
            
            // Process sub indikator umum
            if ($this->isUmumSubIndikator($data, $currentIndikator)) {
                $subIndikatorText = $this->extractUmumSubIndikator($data);
                
                Matriks_Fra::create([
                    'template_fra_id' => $umumTemplateFra->id,
                    'tujuan' => 'Umum',
                    'sasaran' => null,
                    'indikator' => $currentIndikator ?: 'Indikator Umum',
                    'sub_indikator' => $subIndikatorText,
                    'detail_sub' => null,
                    'satuan' => $this->extractAndValidateSatuan($data, 'umum_sub'),
                    'excel_row' => $row
                ]);
                
                $rowsProcessed++;
                continue;
            }
        }
        
        Log::info("Berhasil memproses {$rowsProcessed} data dari bagian Umum");
        return $rowsProcessed;
    }
    
    /**
     * Baca data dari satu row dengan semua kolom
     */
    private function readRowData($worksheet, int $row): array
    {
        return [
            'A' => $this->getMergedCellValue($worksheet, "A{$row}"),
            'B' => $this->getMergedCellValue($worksheet, "B{$row}"),
            'C' => $this->getMergedCellValue($worksheet, "C{$row}"),
            'D' => $this->getMergedCellValue($worksheet, "D{$row}"),
            'E' => $this->getMergedCellValue($worksheet, "E{$row}"),
            'F' => $this->getMergedCellValue($worksheet, "F{$row}"),
            'G' => $this->getMergedCellValue($worksheet, "G{$row}"),
            'H' => $this->getMergedCellValue($worksheet, "H{$row}"),
            'description' => trim(
                $this->getMergedCellValue($worksheet, "D{$row}") . ' ' .
                $this->getMergedCellValue($worksheet, "E{$row}") . ' ' .
                $this->getMergedCellValue($worksheet, "F{$row}") . ' ' .
                $this->getMergedCellValue($worksheet, "G{$row}") . ' ' .
                $this->getMergedCellValue($worksheet, "H{$row}")
            )
        ];
    }
    
    /**
     * Ambil nilai dari merged cell
     */
    private function getMergedCellValue($sheet, $cellAddress)
    {
        $cell = $sheet->getCell($cellAddress);
        $value = $cell->getValue();
        
        if ($value !== null && $value !== '') {
            return trim($value);
        }
        
        // Cek merged cells
        $mergedCells = $sheet->getMergeCells();
        foreach ($mergedCells as $range) {
            if ($cell->isInRange($range)) {
                $startCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::splitRange($range)[0][0];
                $startValue = $sheet->getCell($startCell)->getValue();
                return trim($startValue ?? '');
            }
        }
        
        return '';
    }
    
    // =================== DETECTION METHODS ===================
    
    private function isEmptyRow(array $data): bool
    {
        return empty($data['B']) && empty($data['C']) && empty($data['description']);
    }
    
    private function isUmumSection(array $data): bool
    {
        return stripos($data['B'], 'bagian umum') !== false || 
               stripos($data['B'], 'umum') !== false ||
               stripos($data['description'], 'bagian umum') !== false;
    }
    
    private function isTujuan(array $data): bool
    {
        return stripos($data['B'], 'tujuan') !== false && !empty($data['D']);
    }
    
    private function isSasaran(array $data): bool
    {
        return preg_match('/^\d+\.\d+$/', $data['B']) && !empty($data['C']);
    }
    
    private function isIndikator(array $data): bool
    {
        return preg_match('/^\d+\.\d+\.\d+$/', $data['C']) && !empty($data['D']);
    }
    
    private function isSubIndikator(array $data, $currentIndikator): bool
    {
        return !empty($data['description']) && 
               empty($data['C']) && 
               !empty($currentIndikator) &&
               !$this->isUmumSection($data) &&
               !$this->isInstructionRow($data);
    }
    
    private function isDetailSub(array $data, $currentSubKode): bool
    {
        return !empty($data['description']) && 
               empty($data['C']) && 
               empty($data['D']) &&
               in_array(strtolower($currentSubKode), ['x', 'y']) &&
               !$this->isInstructionRow($data);
    }
    
    private function isInstructionRow(array $data): bool
    {
        $text = strtolower($data['description']);
        return stripos($text, 'indikator dapat dihapus') !== false ||
               stripos($text, 'silahkan sesuaikan') !== false ||
               stripos($text, 'petunjuk') !== false ||
               stripos($text, 'panduan') !== false;
    }
    
    private function isUmumIndikator(array $data): bool
    {
        return !empty($data['C']) && 
               empty($data['D']) &&
               !$this->isInstructionRow($data);
    }
    
    private function isUmumSubIndikator(array $data, $currentIndikator): bool
    {
        return ((!empty($data['D']) && empty($data['C'])) || 
                (!empty($data['C']) && !empty($data['D']))) &&
               !empty($currentIndikator) &&
               !$this->isInstructionRow($data);
    }
    
    // =================== EXTRACTION METHODS ===================
    
    private function extractTujuan(array $data): string
    {
        $number = preg_replace('/[^0-9]/', '', $data['B']);
        return "Tujuan {$number}. {$data['D']}";
    }
    
    private function extractSasaran(array $data): string
    {
        return $data['B'] . ' ' . $data['C'];
    }
    
    private function extractIndikator(array $data): string
    {
        return $data['C'] . ' ' . $data['D'];
    }
    
    private function extractSubIndikator(array $data): array
    {
        $kode = '';
        $text = $data['description'];
        
        // Cek apakah ada kode di kolom D
        if (!empty($data['D']) && strlen($data['D']) <= 2) {
            $possibleCode = strtolower($data['D']);
            if (in_array($possibleCode, ['x', 'y', 'z', 'a', 'b', 'c'])) {
                $kode = $possibleCode;
                $text = "{$possibleCode}. {$data['description']}";
            }
        }
        
        return ['text' => $text, 'code' => $kode];
    }
    
    private function extractDetailSub(array $data): string
    {
        return $data['description'];
    }
    
    private function extractUmumIndikator(array $data): string
    {
        return $data['C'];
    }
    
    private function extractUmumSubIndikator(array $data): string
    {
        if (!empty($data['C']) && !empty($data['D'])) {
            return $data['D']; // D adalah sub dari C
        } elseif (!empty($data['D']) && empty($data['C'])) {
            return $data['D']; // D adalah sub dari indikator sebelumnya
        }
        return $data['description'];
    }
    
    // =================== SATUAN METHODS ===================
    
    private function extractAndValidateSatuan(array $data, string $context): string
    {
        // Strategi ekstraksi satuan berdasarkan konteks
        $satuan = '';
        
        switch ($context) {
            case 'indikator':
            case 'sub_indikator':
                // Coba ekstrak dari description
                $satuan = $this->extractSatuan($data['description']);
                break;
                
            case 'detail_sub':
                // Detail sub biasanya tidak memiliki satuan terpisah
                $satuan = $this->extractSatuan($data['description']);
                break;
                
            case 'umum_indikator':
                $satuan = $this->extractSatuan($data['C']);
                break;
                
            case 'umum_sub':
                $satuan = $this->extractSatuan($data['D'] ?: $data['description']);
                break;
        }
        
        return $this->validateSatuan($satuan);
    }
    
    private function extractSatuan($text): string
    {
        if (empty($text)) {
            return '';
        }
        
        // 1. Cari satuan dalam kurung (prioritas tertinggi)
        if (preg_match('/\(([^)]+)\)/', $text, $matches)) {
            $satuan = trim($matches[1]);
            if (strlen($satuan) <= 45) {
                return $satuan;
            }
        }
        
        // 2. Mapping kata kunci satuan
        $satuanMapping = [
            'persen' => '%',
            'persentase' => '%',
            'orang' => 'orang',
            'buah' => 'buah',
            'unit' => 'unit',
            'publikasi' => 'publikasi',
            'rilis' => 'rilis',
            'dokumen' => 'dokumen',
            'paket' => 'paket',
            'rupiah' => 'Rp',
            'meter' => 'm',
            'kg' => 'kg',
            'gram' => 'gram',
            'liter' => 'liter',
            'jam' => 'jam',
            'hari' => 'hari',
            'bulan' => 'bulan',
            'tahun' => 'tahun'
        ];
        
        foreach ($satuanMapping as $keyword => $satuan) {
            if (stripos($text, $keyword) !== false) {
                return $satuan;
            }
        }
        
        // 3. Cari kata terakhir yang masuk akal sebagai satuan
        $words = explode(' ', trim($text));
        $lastWord = end($words);
        
        if (strlen($lastWord) <= 10 && 
            !in_array(strtolower($lastWord), ['dan', 'atau', 'yang', 'dari', 'untuk', 'dengan', 'pada', 'di', 'ke', 'oleh'])) {
            return $lastWord;
        }
        
        return '';
    }
    
    private function validateSatuan($satuan): string
    {
        if (empty($satuan)) {
            return '';
        }
        
        $satuan = trim($satuan);
        
        // Potong jika terlalu panjang (max 45 karakter untuk safety)
        if (strlen($satuan) > 45) {
            $satuan = substr($satuan, 0, 45);
        }
        
        return $satuan;
    }
    
    // =================== UTILITY METHODS ===================
    
    private function resetSubContexts(&$currentSubIndikator, &$currentSubKode, &$currentSubIndikatorId)
    {
        $currentSubIndikator = null;
        $currentSubKode = null;
        $currentSubIndikatorId = null;
    }
} 