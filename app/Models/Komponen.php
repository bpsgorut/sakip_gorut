<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class komponen extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'komponen';

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'komponen',
    ];

    /**
     * Mendapatkan pengguna dengan role ini.
     */
    public function sub_komponen()
    {
        return $this->hasMany(Sub_Komponen::class, 'komponen_id');
    }
}
