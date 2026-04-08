<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Skp extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'skps';

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'kegiatan_id',
        'jenis',
        'bulan',
        'tahun',
        'file_id',
        'webViewLink',
        'nama_file',
        'uploaded_by',
        'uploaded_at',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array
     */
    protected $casts = [
        'uploaded_at' => 'datetime',
        'tahun' => 'integer',
        'bulan' => 'integer',
    ];

    /**
     * Relasi ke model Pengguna (user yang memiliki SKP).
     */
    public function user()
    {
        return $this->belongsTo(Pengguna::class, 'user_id');
    }

    /**
     * Relasi ke model Kegiatan.
     */
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    /**
     * Relasi ke model Pengguna (user yang mengupload).
     */
    public function uploader()
    {
        return $this->belongsTo(Pengguna::class, 'uploaded_by');
    }

    /**
     * Scope untuk filter berdasarkan user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan tahun.
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('tahun', $year);
    }

    /**
     * Scope untuk filter berdasarkan jenis (bulanan/tahunan).
     */
    public function scopeOfType($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    /**
     * Scope untuk filter berdasarkan bulan (khusus SKP bulanan).
     */
    public function scopeForMonth($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }

    /**
     * Scope untuk filter berdasarkan kegiatan.
     */
    public function scopeForKegiatan($query, $kegiatanId)
    {
        return $query->where('kegiatan_id', $kegiatanId);
    }

    /**
     * Mendapatkan nama bulan dalam bahasa Indonesia.
     */
    public function getNamaBulanAttribute()
    {
        if ($this->jenis === 'tahunan' || !$this->bulan) {
            return null;
        }

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $namaBulan[$this->bulan] ?? null;
    }

    /**
     * Mendapatkan label periode SKP.
     */
    public function getPeriodeLabelAttribute()
    {
        if ($this->jenis === 'tahunan') {
            return "SKP Tahunan {$this->tahun}";
        }

        return "SKP {$this->nama_bulan} {$this->tahun}";
    }

    /**
     * Cek apakah SKP ini adalah SKP bulanan.
     */
    public function isBulanan()
    {
        return $this->jenis === 'bulanan';
    }

    /**
     * Cek apakah SKP ini adalah SKP tahunan.
     */
    public function isTahunan()
    {
        return $this->jenis === 'tahunan';
    }

    /**
     * Static method untuk mendapatkan SKP berdasarkan user, kegiatan, dan periode.
     */
    public static function getSkpForUserPeriod($userId, $kegiatanId, $jenis, $bulan = null, $tahun = null)
    {
        $query = self::forUser($userId)
            ->forKegiatan($kegiatanId)
            ->ofType($jenis);

        if ($tahun) {
            $query->forYear($tahun);
        }

        if ($jenis === 'bulanan' && $bulan) {
            $query->forMonth($bulan);
        }

        return $query->first();
    }

    /**
     * Static method untuk cek apakah SKP sudah ada untuk periode tertentu.
     */
    public static function existsForUserPeriod($userId, $kegiatanId, $jenis, $bulan = null, $tahun = null)
    {
        return self::getSkpForUserPeriod($userId, $kegiatanId, $jenis, $bulan, $tahun) !== null;
    }

    /**
     * Static method untuk mendapatkan statistik SKP per user.
     */
    public static function getSkpStatsForUser($userId, $kegiatanId, $tahun = null)
    {
        $tahun = $tahun ?: date('Y');
        
        $bulananCompleted = self::forUser($userId)
            ->forKegiatan($kegiatanId)
            ->ofType('bulanan')
            ->forYear($tahun)
            ->count();
            
        $tahunanCompleted = self::forUser($userId)
            ->forKegiatan($kegiatanId)
            ->ofType('tahunan')
            ->forYear($tahun)
            ->exists();
            
        return [
            'bulanan_completed' => $bulananCompleted,
            'tahunan_completed' => $tahunanCompleted,
            'total_progress' => ($bulananCompleted / 12 * 80) + ($tahunanCompleted ? 20 : 0)
        ];
    }
}