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
        if (!Schema::hasTable('realisasi_fra')) {
            return;
        }

        $driver = DB::getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        if (Schema::hasColumn('realisasi_fra', 'kendala')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `kendala` TEXT NULL");
        }
        if (Schema::hasColumn('realisasi_fra', 'solusi')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `solusi` TEXT NULL");
        }
        if (Schema::hasColumn('realisasi_fra', 'tindak_lanjut')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `tindak_lanjut` TEXT NULL");
        }
        if (Schema::hasColumn('realisasi_fra', 'batas_waktu_tindak_lanjut')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `batas_waktu_tindak_lanjut` DATE NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('realisasi_fra')) {
            return;
        }

        $driver = DB::getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        if (Schema::hasColumn('realisasi_fra', 'kendala')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `kendala` TEXT NOT NULL");
        }
        if (Schema::hasColumn('realisasi_fra', 'solusi')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `solusi` TEXT NOT NULL");
        }
        if (Schema::hasColumn('realisasi_fra', 'tindak_lanjut')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `tindak_lanjut` TEXT NOT NULL");
        }
        if (Schema::hasColumn('realisasi_fra', 'batas_waktu_tindak_lanjut')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `batas_waktu_tindak_lanjut` DATE NOT NULL");
        }
    }
};
