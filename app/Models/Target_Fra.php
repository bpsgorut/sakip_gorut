<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target_Fra extends Model
{
    use HasFactory;

    protected $table = 'target_fra';

    protected $fillable = [
        'matriks_fra_id',
        'target_tw1',
        'target_tw2',
        'target_tw3',
        'target_tw4',
        'assign_id',
        'parent_id'
    ];

    public function matriks_fra()
    {
        return $this->belongsTo(Matriks_Fra::class, 'matriks_fra_id');
    }

    // Relasi ke tabel pengguna untuk assign_id (PIC)
    public function assignedUser()
    {
        return $this->belongsTo(Pengguna::class, 'assign_id');
    }

    // Method helper untuk menghitung total target
    public function calculateTotalTarget()
    {
        return $this->target_tw1 + $this->target_tw2 + $this->target_tw3 + $this->target_tw4;
    }
}
