<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Realisasi_Fra extends Model
{
    use HasFactory;
    
    protected $table = 'realisasi_fra';
    
    protected $fillable = [
        'matriks_fra_id',
        'triwulan_id',
        'realisasi',
        'capkin_kumulatif',
        'capkin_setahun',
        'kendala',
        'solusi',
        'tindak_lanjut',
        'pic_tindak_lanjut_id',
        'batas_waktu_tindak_lanjut'
    ];
    
    protected $casts = [
        'batas_waktu_tindak_lanjut' => 'date',
        'realisasi' => 'decimal:2',
        'capkin_kumulatif' => 'decimal:2',
        'capkin_setahun' => 'decimal:2'
    ];
    
    public function matriks_fra()
    {
        return $this->belongsTo(Matriks_Fra::class, 'matriks_fra_id');
    }
    
    public function triwulan()
    {
        return $this->belongsTo(Triwulan::class, 'triwulan_id');
    }
    
    public function buktidukung_fra()
    {
        return $this->hasMany(Buktidukung_Fra::class, 'realisasi_fra_id');
    }
    
    public function pic_tindak_lanjut()
    {
        return $this->belongsTo(Pengguna::class, 'pic_tindak_lanjut_id');
    }
}
