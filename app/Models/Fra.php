<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Fra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fra';

    // Pastikan status tidak masuk dalam fillable karena itu accessor, bukan kolom database
    protected $fillable = [
        'nama_fra',
        'tahun_berjalan',
        'file_template',
    ];

    // Tambahkan appends untuk memastikan status selalu disertakan dalam output JSON
    protected $appends = ['status'];

    // Relationship dengan template_fra
    public function template_fra()
    {
        return $this->hasMany(Template_Fra::class, 'fra_id');
    }

    // Relationship dengan template_jenis melalui template_fra
    public function template_jenis()
    {
        return $this->belongsToMany(
            Template_Jenis::class,
            'template_fra',
            'fra_id',
            'template_jenis_id'
        );
    }

    // Direct relationship dengan matriks_fra (untuk Google Drive integration)
    public function matriksFra()
    {
        return $this->hasMany(Matriks_Fra::class, 'fra_id');
    }

    // Relationship dengan matriks_fra melalui template_fra (legacy)
    public function matriks_fra()
    {
        return $this->hasManyThrough(
            Matriks_Fra::class,
            Template_Fra::class,
            'fra_id',
            'template_fra_id',
            'id',
            'id'
        );
    }

    // Relationship dengan target_fra melalui matriks_fra
    public function target_fra()
    {
        return $this->hasManyThrough(
            Target_Fra::class,
            Matriks_Fra::class,
            'template_fra_id',
            'matriks_fra_id',
            'id',
            'id'
        );
    }

    // Relationship dengan realisasi_fra melalui matriks_fra
    public function realisasi_fra()
    {
        return $this->hasManyThrough(
            Realisasi_Fra::class,
            Matriks_Fra::class,
            'template_fra_id',
            'matriks_fra_id',
            'id',
            'id'
        );
    }

    // Relationship dengan triwulan
    public function triwulans()
    {
        return $this->hasMany(Triwulan::class, 'fra_id');
    }

    // Relationship dengan Kegiatan untuk mengakses Google Drive folder_id
    public function capaianKinerjaKegiatan()
    {
        return \App\Models\Kegiatan::where('nama_kegiatan', 'like', 'Capaian Kinerja FRA ' . $this->tahun_berjalan . '%')
            ->where('tahun_berjalan', $this->tahun_berjalan)
            ->first();
    }

    // Relationship dengan Kegiatan Form Rencana Aksi untuk mengakses Google Drive folder_id
    public function formRencanaAksiKegiatan()
    {
        return \App\Models\Kegiatan::where('nama_kegiatan', 'like', 'Form Rencana Aksi ' . $this->tahun_berjalan . '%')
            ->where('tahun_berjalan', $this->tahun_berjalan)
            ->first();
    }

    // Accessor untuk mendapatkan Google Drive folder_id dari kegiatan Form Rencana Aksi
    public function getGoogleDriveFolderIdAttribute()
    {
        $kegiatan = $this->formRencanaAksiKegiatan();
        return $kegiatan ? $kegiatan->folder_id : null;
    }

    // Status accessor - Tetap seperti implementasi sebelumnya
    public function getStatusAttribute()
    {
        $currentYear = Carbon::now()->year;

        // Jika FRA dari tahun lalu/sebelumnya, selalu dianggap Selesai
        if ($this->tahun_berjalan < $currentYear) {
            return 'Selesai';
        }

        // Cek apakah ada target yang sudah diinput
        $matriksIds = $this->matriks_fra()->select('matriks_fra.id')->pluck('matriks_fra.id')->toArray();

        if (!empty($matriksIds)) {
            $hasTarget = Target_Fra::whereIn('matriks_fra_id', $matriksIds)->exists();

            if (!$hasTarget) {
                return 'Baru Dibuat';
            }

            // Jika semua triwulan selesai, status FRA menjadi Selesai
            $triwulanStatuses = $this->triwulans()->pluck('status')->toArray();
            if (
                count($triwulanStatuses) === 4 &&
                !in_array('Dalam Proses', $triwulanStatuses) &&
                !in_array('Belum Mulai', $triwulanStatuses)
            ) {
                return 'Selesai';
            }

            // Jika ada triwulan yang sedang dalam proses atau belum mulai, status FRA adalah Dalam Proses
            return 'Dalam Proses';
        }

        return 'Baru Dibuat';
    }

    // Get triwulans attribute dengan data dari database
    public function getTriwulansAttribute()
    {
        return $this->triwulans()->get()->map(function ($triwulan) {
            $triwulan->updateStatus(); // Pastikan status diperbarui saat diakses
            return [
                'number' => $triwulan->nomor,
                'status' => $triwulan->status,
                'date_range' => Carbon::parse($triwulan->tanggal_mulai)->format('d M') .
                    ' - ' .
                    Carbon::parse($triwulan->tanggal_selesai)->format('d M Y')
            ];
        });
    }

    // Method untuk mengecek apakah FRA memiliki template jenis tertentu
    public function hasTemplateJenis($nama)
    {
        return $this->template_fra()
            ->whereHas('template_jenis', function ($query) use ($nama) {
                $query->where('nama', $nama);
            })
            ->exists();
    }

    // Method to get current processing triwulan
    public function getCurrentTriwulan()
    {
        $now = Carbon::now();

        if ($this->tahun_berjalan < $now->year) {
            return 4; // Past year, all triwulans are finished
        } elseif ($this->tahun_berjalan > $now->year) {
            return 0; // Future year, no triwulans started yet
        }

        // Current year calculation
        return ceil($now->month / 3);
    }

    // Method untuk mengecek apakah target FRA sudah difinalisasi
    public function isTargetFinalized()
    {
        // ✅ FIXED: Pengecekan yang lebih akurat untuk memastikan target FRA benar-benar difinalisasi
        // 1. Cek apakah ada triwulan yang sudah dibuat (indikasi bahwa target sudah difinalisasi)
        $hasTriwulans = $this->triwulans()->exists();
        
        // 2. Cek apakah ada target FRA yang sudah diisi (memastikan data target ada)
        $hasTargets = \App\Models\Target_Fra::whereIn('matriks_fra_id', $this->matriks_fra->pluck('id'))
                        ->whereNotNull('target_tw4')
                        ->where('target_tw4', '!=', 0)
                        ->exists();
        
        // 3. Cek jumlah target yang diisi vs total matriks yang perlu target
        $totalMatriks = $this->matriks_fra
                            ->filter(function ($matriks) {
                                // Hanya indikator utama yang perlu target (yang memiliki satuan)
                                return !empty($matriks->indikator) && !empty($matriks->satuan);
                            })
                            ->count();
        
        $filledTargets = \App\Models\Target_Fra::whereIn('matriks_fra_id', $this->matriks_fra->pluck('id'))
                           ->whereNotNull('target_tw4')
                           ->where('target_tw4', '!=', 0)
                           ->count();
        
        // Target dianggap finalized jika:
        // - Ada triwulan yang sudah dibuat (proses finalisasi sudah dilakukan)
        // - Ada target yang sudah diisi
        // - SEMUA matriks yang memerlukan target sudah diisi (100% completion required)
        $isCompletelyFilled = $totalMatriks > 0 && ($filledTargets >= $totalMatriks);
        
        return $hasTriwulans && $hasTargets && $isCompletelyFilled;
    }

    // Method to check if capaian kinerja activity already exists
    public function hasCapaianKinerjaActivity()
    {
        return \App\Models\Kegiatan::where('nama_kegiatan', 'like', 'Monitoring Capaian Kinerja FRA ' . $this->tahun_berjalan . '%')
            ->where('tahun_berjalan', $this->tahun_berjalan)
            ->exists();
    }

    /**
     * Auto-create capaian kinerja activity when FRA is created or updated
     */
    public function autoCreateCapaianKinerja()
    {
        // Skip if capaian kinerja already exists
        if ($this->hasCapaianKinerjaActivity()) {
            return false;
        }

        try {
            // Get appropriate sub komponen for capaian kinerja (Lakin)
            $subKomponen = \App\Models\Sub_Komponen::where('sub_komponen', 'like', '%Lakin%')
                ->orWhere('sub_komponen', 'like', '%Pelaporan Kinerja%')
                ->first();

            if (!$subKomponen) {
                // Create if not exists
                $komponen = \App\Models\Komponen::where('komponen', 'like', '%Pelaporan%')->first();
                if (!$komponen) {
                    $komponen = \App\Models\Komponen::first(); // fallback
                }
                
                $subKomponen = \App\Models\Sub_Komponen::create([
                    'sub_komponen' => 'Lakin (Laporan Kinerja)',
                    'komponen_id' => $komponen ? $komponen->id : 3
                ]);
            }

                    // Calculate dates: Full year (January 1st to December 31st) of the FRA year
        // Since capaian kinerja is now divided into 4 triwulans with quarterly upload periods
        $startDate = \Carbon\Carbon::createFromDate($this->tahun_berjalan, 1, 1);
        $endDate = \Carbon\Carbon::createFromDate($this->tahun_berjalan, 12, 31);

            // Get correct renstra for FRA year
            $correctRenstra = \App\Models\Kegiatan::getCorrectRenstraForYear($this->tahun_berjalan);

            if ($correctRenstra) {
                $capaianKinerja = \App\Models\Kegiatan::create([
                    'nama_kegiatan' => 'Capaian Kinerja FRA ' . $this->tahun_berjalan,
                    'tahun_berjalan' => $this->tahun_berjalan,
                    'tanggal_mulai' => $startDate->format('Y-m-d'),
                    'tanggal_berakhir' => $endDate->format('Y-m-d'),
                    'sub_komponen_id' => $subKomponen->id,
                    'renstra_id' => $correctRenstra->id
                ]);

                \Illuminate\Support\Facades\Log::info('Auto-created Capaian Kinerja for FRA', [
                    'fra_id' => $this->id,
                    'fra_tahun' => $this->tahun_berjalan,
                    'capaian_kinerja_id' => $capaianKinerja->id
                ]);

                return $capaianKinerja;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to auto-create Capaian Kinerja for FRA', [
                'fra_id' => $this->id,
                'fra_tahun' => $this->tahun_berjalan,
                'error' => $e->getMessage()
            ]);
        }

        return false;
    }

    /**
     * Boot method - removed auto-create capaian kinerja to avoid duplicates
     * Capaian kinerja is now created explicitly in FraController
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-create capaian kinerja removed to prevent duplicates
        // It's now handled in FraController::store() method
    }
}
