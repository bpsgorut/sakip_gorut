<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update existing data to match enum values
        $jabatanMapping = [
            'Kepala Sub Bagian Umum' => 'Kasubag Umum',
            'kepala sub bagian umum' => 'Kasubag Umum',
            'KEPALA SUB BAGIAN UMUM' => 'Kasubag Umum',
        ];
        
        $bidangMapping = [
            'Kepala BPS' => 'Bagian Umum',
            'Kepala Sub Bagian Umum' => 'Bagian Umum',
            'kepala bps' => 'Bagian Umum',
            'kepala sub bagian umum' => 'Bagian Umum',
            'KEPALA BPS' => 'Bagian Umum',
            'KEPALA SUB BAGIAN UMUM' => 'Bagian Umum',
            'Tim Neraca Wilayah Analisis Statistik dan Penjaminan Kualitas' => 'Tim Statistik Sosial',
        ];
        
        // Update jabatan values
        foreach ($jabatanMapping as $old => $new) {
            DB::table('pengguna')
                ->where('jabatan', $old)
                ->update(['jabatan' => $new]);
        }
        
        // Update bidang values
        foreach ($bidangMapping as $old => $new) {
            DB::table('pengguna')
                ->where('bidang', $old)
                ->update(['bidang' => $new]);
        }
        
        // Set default values for null or empty jabatan
        DB::table('pengguna')
            ->whereNull('jabatan')
            ->orWhere('jabatan', '')
            ->update(['jabatan' => 'Anggota Tim']);
            
        // Set default values for null or empty bidang
        DB::table('pengguna')
            ->whereNull('bidang')
            ->orWhere('bidang', '')
            ->update(['bidang' => 'Bagian Umum']);
        
        // Now modify the table structure
        Schema::table('pengguna', function (Blueprint $table) {
            $table->enum('jabatan', [
                'Kepala BPS',
                'Kasubag Umum', 
                'Ketua Tim',
                'Anggota Tim'
            ])->default('Anggota Tim')->change();
            
            $table->enum('bidang', [
                'Tim Humas dan Reformasi Birokrasi',
                'Tim Statistik Sosial',
                'Tim Pengolahan Teknologi Informasi dan Diseminasi',
                'Tim Sensus, Pengembangan Survei, Manajemen Lapangan dan Mitra',
                'Tim Statistik Produksi',
                'Tim Statistik Distribusi, KTIP, dan Harga',
                'Tim Pembinaan Statistik Sektoral dan Penilai Badan (EPSS)',
                'Bagian Umum'
            ])->default('Bagian Umum')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->string('jabatan')->nullable()->change();
            $table->string('bidang')->nullable()->change();
        });
    }
};