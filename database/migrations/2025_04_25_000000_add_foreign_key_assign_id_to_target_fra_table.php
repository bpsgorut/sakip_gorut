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
        Schema::table('target_fra', function (Blueprint $table) {
            // Menambahkan foreign key constraint untuk assign_id yang merujuk ke tabel pengguna
            $table->foreign('assign_id')->references('id')->on('pengguna')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('target_fra', function (Blueprint $table) {
            // Menghapus foreign key constraint
            $table->dropForeign(['assign_id']);
        });
    }
};