<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buktidukung_Fra extends Model
{
    use HasFactory;
    
    protected $table = 'buktidukung_fra';
    
    protected $fillable = [
        'realisasi_fra_id',
        'nama_dokumen',
        'file_name',
        'google_drive_file_id',
        'webViewLink',
    ];
    
    public function realisasi_fra()
    {
        return $this->belongsTo(Realisasi_Fra::class, 'realisasi_fra_id');
    }
}
