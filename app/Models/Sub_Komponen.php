<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sub_Komponen extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'sub_komponen';

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'sub_komponen',
        'komponen_id',
    ];

    /**
     * Mendapatkan pengguna dengan role ini.
     */
    public function komponen()
    {
        return $this->belongsTo(Komponen::class, 'komponen_id');
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'sub_komponen_id');
    }
}
