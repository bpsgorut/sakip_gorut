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

        if (Schema::hasColumn('realisasi_fra', 'realisasi')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `realisasi` FLOAT NULL");
        }
        if (Schema::hasColumn('realisasi_fra', 'capkin_kumulatif')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `capkin_kumulatif` FLOAT NOT NULL DEFAULT 0");
        }
        if (Schema::hasColumn('realisasi_fra', 'capkin_setahun')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `capkin_setahun` FLOAT NOT NULL DEFAULT 0");
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

        if (Schema::hasColumn('realisasi_fra', 'realisasi')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `realisasi` FLOAT NOT NULL");
        }
        if (Schema::hasColumn('realisasi_fra', 'capkin_kumulatif')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `capkin_kumulatif` FLOAT NOT NULL");
        }
        if (Schema::hasColumn('realisasi_fra', 'capkin_setahun')) {
            DB::statement("ALTER TABLE `realisasi_fra` MODIFY `capkin_setahun` FLOAT NOT NULL");
        }
    }
};
