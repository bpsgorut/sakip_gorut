<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template_Jenis extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'template_jenis';

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'nama',
        'wajib',
    ];

    // Relasi ke TemplateFra (One-to-Many)
    public function template_fra()
    {
        return $this->hasMany(Template_Fra::class, 'template_jenis_id');
    }

    // Relasi ke Fra melalui TemplateFra (Many-to-Many)
    public function fra()
    {
        return $this->belongsToMany(
            Fra::class,
            'template_fra',
            'template_jenis_id',
            'fra_id'
        );
    }
}
