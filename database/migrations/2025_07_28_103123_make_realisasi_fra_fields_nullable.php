<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('realisasi_fra', function (Blueprint $table) {
            $table->text('kendala')->nullable()->change();
            $table->text('solusi')->nullable()->change();
            $table->text('tindak_lanjut')->nullable()->change();
            $table->date('batas_waktu_tindak_lanjut')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi_fra', function (Blueprint $table) {
            $table->text('kendala')->nullable(false)->change();
            $table->text('solusi')->nullable(false)->change();
            $table->text('tindak_lanjut')->nullable(false)->change();
            $table->date('batas_waktu_tindak_lanjut')->nullable(false)->change();
        });
    }
};
