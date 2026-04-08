<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'role';

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'role_name',
    ];

    /**
     * Mendapatkan pengguna dengan role ini.
     */
    public function pengguna()
    {
        return $this->hasMany(Pengguna::class, 'role_id');
    }
}
