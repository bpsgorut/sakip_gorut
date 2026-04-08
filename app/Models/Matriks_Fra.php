<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matriks_Fra extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'matriks_fra';

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'template_fra_id',
        'tujuan',
        'sasaran',
        'indikator',
        'detail_indikator',
        'sub_indikator',
        'detail_sub',
        'jenis_iku_proksi',
        'jenis_waktu',
        'jenis_persen',
        'satuan',
        'parent_sub_id',
        'excel_row'
    ];

    // Relasi ke TemplateFra
    public function template_fra()
    {
        return $this->belongsTo(Template_Fra::class, 'template_fra_id');
    }

    public function target_fra()
    {
        return $this->hasOne(Target_Fra::class, 'matriks_fra_id');
    }

    public function realisasi_fra()
    {
        return $this->hasMany(Realisasi_Fra::class, 'matriks_fra_id');
    }
    
    // Helper methods untuk identifikasi
    public function isMainIndicator()
    {
        return empty($this->detail_indikator) && empty($this->sub_indikator) && empty($this->detail_sub);
    }
    
    public function isDetailIndicator()
    {
        return !empty($this->detail_indikator) && empty($this->sub_indikator) && empty($this->detail_sub);
    }
    
    public function isSubIndicator()
    {
        return !empty($this->sub_indikator) && empty($this->detail_sub);
    }
    
    public function isDetailSub()
    {
        return !empty($this->detail_sub);
    }
    
    /**
     * Cek apakah ini indikator dengan kode 3 digit (sasaran)
     */
    public function isSasaran()
    {
        return preg_match('/^\d+\.\d+\.\d+(\s|$)/', $this->indikator ?? '');
    }
    
    /**
     * Cek apakah ini indikator dengan kode 4 digit
     */
    public function isIndicator4Digit()
    {
        return preg_match('/^\d+\.\d+\.\d+\.\d+(\s|$)/', $this->indikator ?? '');
    }
    
    /**
     * Cek apakah ini indikator dengan kode 5 digit (sub indikator)
     */
    public function isIndicator5Digit()
    {
        return preg_match('/^\d+\.\d+\.\d+\.\d+\.\d+(\s|$)/', $this->indikator ?? '');
    }
    
    /**
     * Cek apakah sub indikator ini memiliki kode x atau y
     */
    public function hasXorYCode()
    {
        return preg_match('/^[xy]\./', $this->sub_indikator ?? '');
    }
    
    /**
     * Dapatkan semua detail indikator untuk indikator ini
     */
    public function getDetailIndicators()
    {
        if (!$this->isMainIndicator()) {
            return collect();
        }
        
        return Matriks_Fra::where('template_fra_id', $this->template_fra_id)
            ->where('indikator', $this->indikator)
            ->whereNotNull('detail_indikator')
            ->get();
    }
    
    /**
     * Dapatkan semua detail sub untuk sub indikator ini
     */
    public function getDetailSubs()
    {
        if (!$this->isSubIndicator()) {
            return collect();
        }
        
        return Matriks_Fra::where('template_fra_id', $this->template_fra_id)
            ->where('indikator', $this->indikator)
            ->where('sub_indikator', $this->sub_indikator)
            ->whereNotNull('detail_sub')
            ->get();
    }
}
