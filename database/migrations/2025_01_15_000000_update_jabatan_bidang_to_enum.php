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
        if (!Schema::hasTable('pengguna')) {
            return;
        }

        $hasBidang = Schema::hasColumn('pengguna', 'bidang');
        if (!$hasBidang) {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->string('bidang')->nullable()->after('jabatan');
            });
            $hasBidang = true;
        }

        $allowedJabatan = [
            'Kepala BPS',
            'Kasubag Umum',
            'Ketua Tim',
            'Anggota Tim',
        ];

        $allowedBidang = [
            'Tim Humas dan Reformasi Birokrasi',
            'Tim Statistik Sosial',
            'Tim Pengolahan Teknologi Informasi dan Diseminasi',
            'Tim Sensus, Pengembangan Survei, Manajemen Lapangan dan Mitra',
            'Tim Statistik Produksi',
            'Tim Statistik Distribusi, KTIP, dan Harga',
            'Tim Pembinaan Statistik Sektoral dan Penilai Badan (EPSS)',
            'Bagian Umum',
        ];

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
        
        foreach ($jabatanMapping as $old => $new) {
            DB::table('pengguna')
                ->where('jabatan', $old)
                ->update(['jabatan' => $new]);
        }
        
        DB::table('pengguna')
            ->whereNull('jabatan')
            ->orWhere('jabatan', '')
            ->update(['jabatan' => 'Anggota Tim']);

        DB::table('pengguna')
            ->whereNotIn('jabatan', $allowedJabatan)
            ->update(['jabatan' => 'Anggota Tim']);

        if ($hasBidang) {
            foreach ($bidangMapping as $old => $new) {
                DB::table('pengguna')
                    ->where('bidang', $old)
                    ->update(['bidang' => $new]);
            }

            DB::table('pengguna')
                ->whereNull('bidang')
                ->orWhere('bidang', '')
                ->update(['bidang' => 'Bagian Umum']);

            DB::table('pengguna')
                ->whereNotIn('bidang', $allowedBidang)
                ->update(['bidang' => 'Bagian Umum']);
        }

        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE `pengguna` MODIFY `jabatan` ENUM('Kepala BPS','Kasubag Umum','Ketua Tim','Anggota Tim') NOT NULL DEFAULT 'Anggota Tim'");
            if ($hasBidang) {
                DB::statement("ALTER TABLE `pengguna` MODIFY `bidang` ENUM('Tim Humas dan Reformasi Birokrasi','Tim Statistik Sosial','Tim Pengolahan Teknologi Informasi dan Diseminasi','Tim Sensus, Pengembangan Survei, Manajemen Lapangan dan Mitra','Tim Statistik Produksi','Tim Statistik Distribusi, KTIP, dan Harga','Tim Pembinaan Statistik Sektoral dan Penilai Badan (EPSS)','Bagian Umum') NOT NULL DEFAULT 'Bagian Umum'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('pengguna')) {
            return;
        }

        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            if (Schema::hasColumn('pengguna', 'jabatan')) {
                DB::statement("ALTER TABLE `pengguna` MODIFY `jabatan` VARCHAR(255) NULL");
            }
            if (Schema::hasColumn('pengguna', 'bidang')) {
                DB::statement("ALTER TABLE `pengguna` MODIFY `bidang` VARCHAR(255) NULL");
            }
        }
    }
};
