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
            // Ubah kolom pic_tindak_lanjut menjadi pic_tindak_lanjut_id dengan foreign key
            $table->dropColumn('pic_tindak_lanjut');
            $table->unsignedBigInteger('pic_tindak_lanjut_id')->nullable()->after('tindak_lanjut');
            
            // Tambahkan foreign key constraint ke tabel pengguna
            $table->foreign('pic_tindak_lanjut_id')->references('id')->on('pengguna')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi_fra', function (Blueprint $table) {
            // Drop foreign key dan kolom pic_tindak_lanjut_id
            $table->dropForeign(['pic_tindak_lanjut_id']);
            $table->dropColumn('pic_tindak_lanjut_id');
            
            // Kembalikan kolom pic_tindak_lanjut sebagai integer
            $table->integer('pic_tindak_lanjut')->after('tindak_lanjut');
        });
    }
};