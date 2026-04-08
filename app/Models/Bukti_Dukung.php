<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bukti_Dukung extends Model
{
    use HasFactory;

    protected $table = 'bukti_dukung';

    protected $fillable = [
        'jenis',
        'nama_dokumen',
        'file_id',
        'webViewLink',
        'kegiatan_id',
        'renstra_id',
    ];

    // Relationship with Kegiatan (Belongs To)
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    // Relationship with Renstra (Belongs To)
    public function renstra()
    {
        return $this->belongsTo(Renstra::class, 'renstra_id');
    }
}
