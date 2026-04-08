<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen_Kegiatan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'dokumen_kegiatan';

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'file',
        'file_id',
        'nama_dokumen', 
        'webViewLink',
        'webContentLink',
        'renstra_id',
        'kegiatan_id'
    ];

    /**
     * Mendapatkan renstra yang terkait dengan dokumen.
     */
    public function renstra()
    {
        return $this->belongsTo(Renstra::class, 'renstra_id');
    }

    /**
     * Mendapatkan kegiatan yang terkait dengan dokumen.
     */
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }
}
