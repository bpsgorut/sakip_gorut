<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target_Pk extends Model
{
    use HasFactory;

    protected $table = 'target_pk';

    protected $fillable = [
        'kegiatan_id',
        'matriks_fra_id',
        'target_pk',
    ];

    // Relationship dengan kegiatan
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    // Relationship dengan matriks_fra
    public function matriks_fra()
    {
        return $this->belongsTo(Matriks_Fra::class);
    }
}
