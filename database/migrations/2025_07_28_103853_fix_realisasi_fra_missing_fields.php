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
            // Tambahkan default value untuk field yang wajib
            $table->float('realisasi')->nullable()->change();
            $table->float('capkin_kumulatif')->default(0)->change();
            $table->float('capkin_setahun')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi_fra', function (Blueprint $table) {
            $table->float('realisasi')->nullable(false)->change();
            $table->float('capkin_kumulatif')->default(null)->change();
            $table->float('capkin_setahun')->default(null)->change();
        });
    }
};
