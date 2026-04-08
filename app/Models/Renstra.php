<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renstra extends Model
{
    use HasFactory;

    protected $table = 'renstra';
    
    protected $fillable = [
        'nama_renstra',
        'periode_awal',
        'periode_akhir',
        'tanggal_mulai',
        'tanggal_selesai',
        'folder_id',
    ];

    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class);
    }

    public function dokumenKegiatan()
    {
        return $this->hasOne(Dokumen_Kegiatan::class);
    }
    
    public function buktiDukungs()
    {
        return $this->hasMany(Bukti_Dukung::class);
    }
}
