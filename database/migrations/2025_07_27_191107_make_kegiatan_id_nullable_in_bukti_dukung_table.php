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
        if (!Schema::hasTable('bukti_dukung')) {
            return;
        }

        $driver = DB::getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        Schema::table('bukti_dukung', function (Blueprint $table) {
            $table->dropForeign(['kegiatan_id']);
        });

        DB::statement("ALTER TABLE `bukti_dukung` MODIFY `kegiatan_id` BIGINT UNSIGNED NULL");

        Schema::table('bukti_dukung', function (Blueprint $table) {
            $table->foreign('kegiatan_id')->references('id')->on('kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('bukti_dukung')) {
            return;
        }

        $driver = DB::getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        Schema::table('bukti_dukung', function (Blueprint $table) {
            $table->dropForeign(['kegiatan_id']);
        });

        DB::statement("ALTER TABLE `bukti_dukung` MODIFY `kegiatan_id` BIGINT UNSIGNED NOT NULL");

        Schema::table('bukti_dukung', function (Blueprint $table) {
            $table->foreign('kegiatan_id')->references('id')->on('kegiatan');
        });
    }
};
