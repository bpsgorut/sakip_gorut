<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template_Fra extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'template_fra';

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'fra_id',
        'template_jenis_id',
    ];

    // Relasi ke Fra
    public function fra()
    {
        return $this->belongsTo(Fra::class, 'fra_id');
    }

    // Relasi ke TemplateJenis
    public function template_jenis()
    {
        return $this->belongsTo(Template_Jenis::class, 'template_jenis_id');
    }

    // Relasi ke MatrixSFra (One-to-Many)
    public function matriks_fra()
    {
        return $this->hasMany(Matriks_Fra::class, 'template_fra_id');
    }
}
