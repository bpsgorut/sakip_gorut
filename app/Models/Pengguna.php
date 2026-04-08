<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'pengguna';



    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nip',
        'email',
        'bidang',
        'password',
        'jabatan',
        'profile_picture',
        'role_id',
    ];

    /**
     * Atribut yang harus disembunyikan.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Mendapatkan role untuk pengguna ini.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Mendapatkan nama jabatan yang dapat dibaca manusia
     */
    public function getJabatanLabelAttribute()
    {
        return $this->jabatan;
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($roleId): bool
    {
        return $this->role_id == $roleId;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roleIds): bool
    {
        if (!is_array($roleIds)) {
            $roleIds = [$roleIds];
        }
        return in_array($this->role_id, $roleIds);
    }

    /**
     * Check if user has role level equal or higher than given role
     */
    public function hasMinimumRole($roleId): bool
    {
        return $this->role_id <= $roleId; // Lower ID = Higher privilege
    }

    /**
     * Role-specific permission checks
     */
    public function isSuperAdmin(): bool
    {
        return $this->role_id == 1;
    }

    public function isAdmin(): bool
    {
        return $this->role_id == 2;
    }

    public function isKetuaTim(): bool
    {
        return $this->role_id == 3;
    }

    public function isAnggotaTim(): bool
    {
        return $this->role_id == 4;
    }

    /**
     * Permission checks for specific functions
     */
    public function canManageUsers(): bool
    {
        return $this->hasAnyRole([1, 2]); // Super Admin & Admin only
    }

    public function canManageFRA(): bool
    {
        return $this->hasAnyRole([1, 2]); // Super Admin & Admin only
    }

    public function canViewAllFRA(): bool
    {
        return $this->hasAnyRole([1, 2]); // Super Admin & Admin only
    }

    public function canCreateFRA(): bool
    {
        return $this->hasAnyRole([1, 2]); // Super Admin & Admin only
    }

    public function canEditFRA(): bool
    {
        return $this->hasAnyRole([1, 2, 3]); // Super Admin, Admin, Ketua Tim
    }

    public function canViewFRA(): bool
    {
        return true; // All roles can view FRA
    }

    public function canFillRealisasi(): bool
    {
        return $this->hasAnyRole([1, 2, 3, 4]); // All roles can fill realisasi
    }

    public function canDownloadFRA(): bool
    {
        return true; // All roles can download FRA
    }

    public function canManageSKP(): bool
    {
        return $this->hasAnyRole([1, 2]); // Super Admin & Admin only
    }

    public function canViewSKP(): bool
    {
        return $this->hasAnyRole([1, 2]); // Super Admin & Admin only
    }

    public function canManageKegiatan(): bool
    {
        return $this->hasAnyRole([1, 2]); // Super Admin & Admin only
    }

    public function canViewDashboard(): bool
    {
        return true; // All roles can view dashboard
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayName(): string
    {
        $roleNames = [
            1 => 'Super Admin',
            2 => 'Admin', 
            3 => 'Ketua Tim',
            4 => 'Anggota Tim'
        ];

        return $roleNames[$this->role_id] ?? 'Unknown';
    }

    /**
     * Get user's access level description
     */
    public function getAccessLevelDescription(): string
    {
        $descriptions = [
            1 => 'Akses penuh ke semua fitur sistem',
            2 => 'Akses manajemen dan administrasi', 
            3 => 'Akses tim dan koordinasi',
            4 => 'Akses dasar dan pengisian data'
        ];

        return $descriptions[$this->role_id] ?? 'Akses terbatas';
    }

    /**
     * Mendapatkan semua pilihan jabatan
     */
    public static function getJabatanOptions()
    {
        return [
            'Kepala BPS',
            'Kasubag Umum',
            'Ketua Tim',
            'Anggota Tim'
        ];
    }

    /**
     * Mendapatkan semua pilihan bidang
     */
    public static function getBidangOptions()
    {
        return [
            'Tim Humas dan Reformasi Birokrasi',
            'Tim Statistik Sosial',
            'Tim Pengolahan Teknologi Informasi dan Diseminasi',
            'Tim Sensus, Pengembangan Survei, Manajemen Lapangan dan Mitra',
            'Tim Statistik Produksi',
            'Tim Statistik Distribusi, KTIP, dan Harga',
            'Tim Pembinaan Statistik Sektoral dan Penilai Badan (EPSS)',
            'Bagian Umum'
        ];
    }
}
