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

        if (!Schema::hasColumn('pengguna', 'nip')) {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->string('nip', 18)->nullable()->after('name');
            });
        }

        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true) && Schema::hasColumn('pengguna', 'nip')) {
            DB::statement("ALTER TABLE `pengguna` MODIFY `nip` VARCHAR(18) NULL");
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
        if (in_array($driver, ['mysql', 'mariadb'], true) && Schema::hasColumn('pengguna', 'nip')) {
            DB::statement("ALTER TABLE `pengguna` MODIFY `nip` VARCHAR(16) NULL");
        }
    }
};
