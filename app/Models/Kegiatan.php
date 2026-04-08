<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bukti_Dukung;
use App\Models\Dokumen_Kegiatan;

class Kegiatan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'kegiatan';

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'nama_kegiatan',
        'tahun_berjalan',
        'tanggal_mulai',
        'tanggal_berakhir',
        'sub_komponen_id',
        'renstra_id',
        'folder_id',
    ];
    
    /**
     * Relasi ke model Sub_Komponen
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sub_komponen()
    {
        return $this->belongsTo(Sub_Komponen::class, 'sub_komponen_id');
    }
    
    /**
     * Relasi ke model Renstra
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function renstra()
    {
        return $this->belongsTo(Renstra::class, 'renstra_id');
    }

    /**
     * Relasi ke model BuktiDukung
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function buktiDukung()
    {
        return $this->hasMany(Bukti_Dukung::class, 'kegiatan_id');
    }

    /**
     * Relasi ke model Dokumen_Kegiatan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dokumenKegiatan()
    {
        return $this->hasMany(Dokumen_Kegiatan::class, 'kegiatan_id');
    }

    /**
     * Check if a kegiatan with the same parameters already exists
     *
     * @param string $namaKegiatan
     * @param int $tahunBerjalan
     * @param int $subKomponenId
     * @param int $renstraId
     * @return bool
     */
    public static function isDuplicate($namaKegiatan, $tahunBerjalan, $subKomponenId, $renstraId)
    {
        return self::where('nama_kegiatan', $namaKegiatan)
            ->where('tahun_berjalan', $tahunBerjalan)
            ->where('sub_komponen_id', $subKomponenId)
            ->where('renstra_id', $renstraId)
            ->exists();
    }

    /**
     * Get duplicate kegiatan with the same parameters
     *
     * @param string $namaKegiatan
     * @param int $tahunBerjalan
     * @param int $subKomponenId
     * @param int $renstraId
     * @return \App\Models\Kegiatan|null
     */
    public static function getDuplicate($namaKegiatan, $tahunBerjalan, $subKomponenId, $renstraId)
    {
        return self::where('nama_kegiatan', $namaKegiatan)
            ->where('tahun_berjalan', $tahunBerjalan)
            ->where('sub_komponen_id', $subKomponenId)
            ->where('renstra_id', $renstraId)
            ->first();
    }
}
