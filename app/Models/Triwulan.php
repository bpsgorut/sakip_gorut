<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Triwulan extends Model
{
    use HasFactory;

    protected $table = 'triwulan';

    protected $fillable = [
        'nama_triwulan',
        'nomor',
        'fra_id',
        'status',
        'catatan',
        'tanggal_mulai',
        'tanggal_selesai'
    ];

    // Tambahkan kolom tanggal ke casting/date
    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime'
    ];

    // Enum untuk status yang valid
    protected $statusOptions = [
        'Belum Mulai',
        'Dalam Proses',
        'Selesai',
        'Terlambat'
    ];

    public function fra()
    {
        return $this->belongsTo(Fra::class, 'fra_id');
    }

    public function realisasi_fra()
    {
        return $this->hasMany(Realisasi_Fra::class, 'triwulan_id');
    }

    public function updateStatus()
    {
        $now = Carbon::now();

        // Pastikan tanggal tidak null sebelum parsing
        if (!$this->tanggal_mulai || !$this->tanggal_selesai) {
            return;
        }

        $startDate = Carbon::parse($this->tanggal_mulai);
        $endDate = Carbon::parse($this->tanggal_selesai)->endOfDay(); // Tambahkan endOfDay untuk mencakup hari terakhir

        $originalStatus = $this->status;

        if ($this->status === 'Selesai') {
            // Jika sudah selesai, jangan ubah status
            return;
        }

        if ($now < $startDate) {
            $this->status = 'Belum Mulai';
        } elseif ($now >= $startDate && $now <= $endDate) {
            $this->status = 'Dalam Proses';
        } else {
            $this->status = 'Terlambat';
        }

        // Hanya save jika status berubah
        if ($originalStatus !== $this->status) {
            $this->save();
        }
    }

    // Metode untuk melakukan update status untuk semua triwulan
    public static function updateAllStatuses()
    {
        self::query()->get()->each(function ($triwulan) {
            $triwulan->updateStatus();
        });
    }

    // Accessor untuk mengambil status yang valid
    public function getValidStatusAttribute()
    {
        return $this->statusOptions;
    }

    // Mutator untuk memastikan status yang diset valid
    public function setStatusAttribute($value)
    {
        if (in_array($value, $this->statusOptions)) {
            $this->attributes['status'] = $value;
        } else {
            throw new \InvalidArgumentException("Status tidak valid");
        }
    }
}
